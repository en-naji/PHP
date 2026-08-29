<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ARBORESCENCE DETAILLEE - PAGE CONTACT
|--------------------------------------------------------------------------
| Role de ce fichier
| - Sert de controleur AJAX pour le formulaire de contact du site public.
| - Ne rend pas de HTML: il recoit une requete POST et retourne du JSON.
| - Le resultat est lu cote navigateur par assets/js/main.js pour afficher
|   un message de succes ou une erreur dans l'interface.
|
| Flux complet de traitement
| 1. Chargement du socle applicatif
|    - includes/config.php initialise la session, les constantes, la base URL,
|      les informations SMTP/contact et les parametres de securite.
|    - includes/functions.php fournit les helpers: sanitize(), verify_csrf(),
|      jsonResponse(), getDB(), isValidEmail(), isValidPhone(), etc.
|
| 2. Controle de la methode HTTP
|    - Le formulaire doit arriver en POST uniquement.
|    - Une requete GET, PUT ou autre est refusee avec le code 405.
|
| 3. Verification CSRF
|    - Le token envoye dans le formulaire est compare au token de session.
|    - Cela empeche un autre site d'envoyer un message a la place du visiteur.
|
| 4. Limitation anti-spam
|    - isRateLimited('contact_submit', 10) limite les envois depuis la session.
|    - Si le visiteur depasse le quota, la reponse JSON retourne le code 429.
|
| 5. Lecture et nettoyage des champs
|    - Les champs first_name, last_name, email, phone, whatsapp et message sont
|      lus depuis $_POST avec une valeur vide par defaut.
|    - sanitize() retire/neutralise les caracteres dangereux avant validation,
|      stockage et reutilisation dans l'email.
|
| 6. Validation metier
|    - Nom complet, email et message sont obligatoires.
|    - L'email doit respecter un format valide.
|    - Le telephone est optionnel, mais s'il est fourni son format est controle.
|
| 7. Enregistrement en base
|    - getDB() retourne une connexion PDO.
|    - Une requete preparee insere le contact dans la table contacts.
|    - Les valeurs utilisateur ne sont jamais concatenees directement au SQL.
|
| 8. Notification email
|    - Un email interne est prepare avec les informations du prospect.
|    - L'operateur @mail evite qu'un echec email bloque la reponse au visiteur.
|
| 9. Reponse finale
|    - jsonResponse(true, ...) confirme l'envoi cote frontend.
|    - Tous les chemins d'erreur retournent aussi une reponse JSON homogene.
|--------------------------------------------------------------------------
*/

// ── ARBORESCENCE DE LA PAGE CONTACT ─────────────────────────────────────
// ├─ 1. Chargement des dépendances
// │  └─ includes/config.php + includes/functions.php
// ├─ 2. Sécurisation de la requête
// │  ├─ vérification de la méthode HTTP (POST uniquement)
// │  ├─ vérification du token CSRF
// │  └─ limitation de débit anti-spam
// ├─ 3. Traitement des données
// │  ├─ nettoyage des champs du formulaire
// │  ├─ reconstruction du nom complet
// │  └─ validation métier des informations
// ├─ 4. Persistance en base
// │  └─ insertion du message dans la table contacts
// ├─ 5. Notification
// │  └─ envoi d'un email au bureau Info-Sup
// └─ 6. Réponse finale
//    └─ retour JSON exploité par le frontend

// CONTROLEUR CONTACT - Role Controller
// Ce fichier ne produit pas une page HTML.
// Il recoit le formulaire du modal en POST AJAX, valide les donnees,
// insere le message dans MySQL, envoie une notification email,
// puis retourne une reponse JSON lue par assets/js/main.js.
// require_once charge la configuration globale avant tout traitement.
require_once __DIR__ . '/includes/config.php';
// require_once charge les fonctions reutilisables du projet.
require_once __DIR__ . '/includes/functions.php';

// ── POST uniquement ────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Protection: le formulaire doit toujours arriver en POST.
    // jsonResponse() stoppe le script et renvoie une erreur HTTP 405.
    jsonResponse(false, 'Méthode non autorisée.', 405);
}

// ── CSRF ───────────────────────────────────────────
// verify_csrf() controle que le token du formulaire correspond a la session.
if (!verify_csrf()) {
    // Protection CSRF: evite qu'un autre site envoie le formulaire.
    // jsonResponse() renvoie une erreur 403 lorsque le token CSRF est invalide.
    jsonResponse(false, 'Token de sécurité invalide. Rechargez la page.', 403);
}

// ── Rate limiting ──────────────────────────────────
// isRateLimited() limite le nombre d'envois autorises pour reduire le spam.
if (isRateLimited('contact_submit', 10)) {
    // Limite anti-spam: maximum 10 messages par heure et par session.
    // jsonResponse() renvoie une erreur 429 quand le quota anti-spam est depasse.
    jsonResponse(false, 'Trop de messages envoyés. Réessayez plus tard.', 429);
}

// ── Données ────────────────────────────────────────
// sanitize() nettoie chaque champ avant validation, stockage ou email.
$firstName = sanitize($_POST['first_name'] ?? '');
$lastName  = sanitize($_POST['last_name']  ?? '');
$email     = sanitize($_POST['email']      ?? '');
$phone     = sanitize($_POST['phone']      ?? '');
$whatsapp  = sanitize($_POST['whatsapp']   ?? '');
$message   = sanitize($_POST['message']    ?? '');

// Le formulaire separe prenom/nom pour l'UX, mais la base stocke un champ name unique.
// trim() retire les espaces inutiles autour du nom complet.
$fullName = trim("$firstName $lastName");

// ── Validation ─────────────────────────────────────
if ($fullName === '' || $email === '' || $message === '') {
    // Validation metier: ces champs sont obligatoires pour recontacter le client.
    // jsonResponse() renvoie une erreur 422 pour les donnees incompletes.
    jsonResponse(false, 'Veuillez remplir tous les champs obligatoires.', 422);
}

// isValidEmail() verifie que l'adresse email est exploitable.
if (!isValidEmail($email)) {
    // jsonResponse() renvoie une erreur 422 pour un email invalide.
    jsonResponse(false, 'Adresse email invalide.', 422);
}

// isValidPhone() controle le telephone seulement si le champ est rempli.
if ($phone !== '' && !isValidPhone($phone)) {
    // jsonResponse() renvoie une erreur 422 pour un telephone invalide.
    jsonResponse(false, 'Numéro de téléphone invalide.', 422);
}

// ── Enregistrement ─────────────────────────────────
try {
    // getDB() ouvre une connexion PDO vers la base MySQL.
    $pdo  = getDB();
    // Requete preparee: les valeurs utilisateur ne sont jamais concatenees au SQL.
    // prepare() compile la requete SQL avec des parametres nommes.
    $stmt = $pdo->prepare('
        INSERT INTO contacts (name, email, phone, whatsapp, message, created_at)
        VALUES (:name, :email, :phone, :whatsapp, :message, NOW())
    ');
    // execute() envoie les valeurs nettoyees a la requete preparee.
    $stmt->execute([
        'name'     => $fullName,
        'email'    => $email,
        'phone'    => $phone,
        'whatsapp' => $whatsapp,
        'message'  => $message,
    ]);
} catch (PDOException $e) {
    // On cache le detail technique au visiteur et on renvoie une erreur generique.
    // jsonResponse() renvoie une erreur 500 sans exposer le detail technique.
    jsonResponse(false, 'Erreur serveur. Veuillez réessayer.', 500);
}

// ── Notification email ─────────────────────────────
$subject = "Nouveau contact: $fullName";
$body    = "Nom: $fullName\nEmail: $email\nTél: $phone\nWhatsApp: $whatsapp\n\nMessage:\n$message";
$headers = "From: noreply@info-sup.com\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";

// mail() envoie une notification interne; @ evite de bloquer le client si l'email echoue.
@mail(CONTACT_EMAIL, $subject, $body, $headers);

// La reponse JSON est consommee par main.js pour afficher le succes.

// ── Succès ─────────────────────────────────────────
// jsonResponse() termine le flux avec un succes exploitable par JavaScript.
jsonResponse(true, 'Message envoyé avec succès !');
