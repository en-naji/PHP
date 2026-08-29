<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ARBORESCENCE DETAILLEE - ADMIN / CONTACTS
|--------------------------------------------------------------------------
| Role de ce fichier
| - Vue admin de consultation des messages envoyes depuis le formulaire public.
| - Permet de lire les contacts, ouvrir un email, ouvrir WhatsApp et supprimer
|   les messages qui ne sont plus utiles.
|
| 1. Initialisation
|    - config.php charge la configuration globale et la session.
|    - functions.php fournit requireAdmin(), verifyAdminCsrf(), getContacts(),
|      sanitize(), site_url(), asset_url() et timeAgo().
|    - requireAdmin() empeche tout acces non authentifie.
|
| 2. Headers admin
|    - nosniff limite les interpretations MIME inattendues.
|    - DENY empeche l'inclusion dans une iframe.
|    - no-store/no-cache evite de conserver les messages dans le cache.
|
| 3. Connexion base et message UI
|    - $pdo est la connexion PDO partagee.
|    - $msg contient le retour utilisateur apres suppression.
|
| 4. Suppression d'un contact
|    - Detectee par POST + delete_id.
|    - verifyAdminCsrf() valide que la demande vient bien de l'interface admin.
|    - L'id est caste en entier puis supprime via requete preparee.
|    - Redirection vers ?deleted=1 pour eviter une double suppression au refresh.
|
| 5. Chargement des contacts
|    - getContacts($pdo) recupere les messages du plus recent au plus ancien.
|    - La vue utilise count($contacts) pour afficher le total dans le titre.
|
| 6. Vue HTML
|    - Header admin: navigation dashboard, produits, contacts, site public.
|    - Tableau: nom, email, telephone, WhatsApp, message, date et actions.
|    - mailto: ouvre le client email; wa.me ouvre une conversation WhatsApp.
|    - Chaque suppression possede son propre token CSRF.
|--------------------------------------------------------------------------
*/

// ADMIN CONTACTS - Role Controller + View
// Controller: traite la suppression d'un message.
// Model: lit/supprime les donnees de la table contacts.
// View: affiche tous les messages avec liens email et WhatsApp.
// require_once charge la configuration globale avant la page admin.
require_once __DIR__ . '/../includes/config.php';
// require_once charge les helpers de securite, base de donnees et affichage.
require_once __DIR__ . '/../includes/functions.php';
// requireAdmin() bloque l'acces aux utilisateurs non connectes.
requireAdmin();

// header() ajoute une protection contre le sniffing MIME.
header('X-Content-Type-Options: nosniff');
// header() interdit l'affichage de cette page dans une iframe.
header('X-Frame-Options: DENY');
// header() empeche la mise en cache des messages admin.
header('Cache-Control: no-store, no-cache, must-revalidate, private');

// getDB() ouvre une connexion PDO vers MySQL.
$pdo = getDB();
$msg = '';

// ── Delete (POST + CSRF) ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    // Suppression d'un contact: action critique, donc POST + CSRF.
    // verifyAdminCsrf() confirme que la demande vient de l'interface admin.
    if (!verifyAdminCsrf()) {
        $msg = 'Token de securite invalide.';
    } else {
        $deleteId = (int)$_POST['delete_id'];
        if ($deleteId > 0) {
            // Requete preparee pour supprimer uniquement l'id demande.
            // prepare() cree une requete parametree pour supprimer un message.
            $stmt = $pdo->prepare('DELETE FROM contacts WHERE id = ?');
            // execute() injecte l'id caste en entier dans la requete preparee.
            $stmt->execute([$deleteId]);
            // header() redirige apres suppression pour eviter un double POST.
            header('Location: ' . site_url('/admin/contacts.php?deleted=1'));
            exit;
        }
    }
}
if (isset($_GET['deleted'])) $msg = 'Contact supprime.';

// Recuperation de tous les messages, du plus recent au plus ancien.
// getContacts() charge la liste des contacts a afficher dans le tableau.
$contacts = getContacts($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Contacts — Admin Info-Sup</title>
    <!-- asset_url() genere le chemin public vers le favicon. -->
    <link rel="icon" href="<?= asset_url('images/favicon.svg') ?>" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- asset_url() genere le chemin public vers le CSS admin. -->
    <link rel="stylesheet" href="<?= asset_url('css/admin.css') ?>">
</head>
<body>

<header class="admin-header">
    <div class="admin-header__left">
        <h2>Info-Sup Admin</h2>
    </div>
    <nav class="admin-header__nav">
        <!-- site_url() construit les liens internes de navigation admin. -->
        <a href="<?= site_url('/admin/') ?>">Dashboard</a>
        <a href="<?= site_url('/admin/products.php') ?>">Produits</a>
        <a href="<?= site_url('/admin/contacts.php') ?>" class="active">Contacts</a>
        <a href="<?= site_url('/') ?>" target="_blank">Voir le site</a>
        <form method="POST" action="<?= site_url('/admin/') ?>" style="display:inline">
            <!-- csrf_field() protege le formulaire de deconnexion. -->
            <?= csrf_field() ?>
            <input type="hidden" name="logout" value="1">
            <button type="submit" style="background:none;border:none;color:inherit;cursor:pointer;font:inherit;padding:8px 12px">Deconnexion</button>
        </form>
    </nav>
</header>

<main class="admin-main">
    <!-- count() affiche le nombre total de messages charges. -->
    <h1 class="admin-title">Messages recus (<?= count($contacts) ?>)</h1>

    <?php if ($msg): ?>
        <!-- sanitize() protege l'affichage du message de confirmation. -->
        <div class="msg msg--success"><?= sanitize($msg) ?></div>
    <?php endif; ?>

    <div class="admin-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Telephone</th>
                    <th>WhatsApp</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($contacts)): ?>
                    <tr><td colspan="7" style="text-align:center;color:#999;padding:32px">Aucun message recu</td></tr>
                <?php else: ?>
                    <!-- foreach parcourt chaque message de contact. -->
                    <?php foreach ($contacts as $c): ?>
                    <tr>
                        <!-- sanitize() protege les donnees utilisateur affichees. -->
                        <td><strong><?= sanitize($c['name']) ?></strong></td>
                        <td><a href="mailto:<?= sanitize($c['email']) ?>" style="color:#2563EB"><?= sanitize($c['email']) ?></a></td>
                        <td><?= sanitize($c['phone'] ?: '') ?></td>
                        <td>
                            <?php if ($c['whatsapp']): ?>
                                <!-- Nettoyage du numero pour construire un lien WhatsApp compatible wa.me. -->
                                <!-- preg_replace() garde uniquement les chiffres pour le lien wa.me. -->
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $c['whatsapp']) ?>" target="_blank" style="color:#25D366;font-weight:600"><?= sanitize($c['whatsapp']) ?></a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td style="max-width:280px"><?= sanitize($c['message']) ?></td>
                        <!-- timeAgo() transforme la date en libelle relatif lisible. -->
                        <td><?= timeAgo($c['created_at']) ?></td>
                        <td>
                            <form method="POST" style="display:inline" onsubmit="return confirm('Supprimer ce contact ?')">
                                <!-- csrf_field() protege chaque formulaire de suppression. -->
                                <?= csrf_field() ?>
                                <input type="hidden" name="delete_id" value="<?= (int)$c['id'] ?>">
                                <button type="submit" class="btn-admin btn-admin--danger">Suppr.</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
