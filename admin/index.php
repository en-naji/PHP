<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ARBORESCENCE DETAILLEE - ADMIN / INDEX
|--------------------------------------------------------------------------
| Role de ce fichier
| - Point d'entree principal de l'administration Info-Sup.
| - Affiche soit le formulaire de connexion, soit le dashboard admin.
| - Gere aussi la deconnexion et plusieurs controles de securite de session.
|
| 1. Chargement du socle
|    - config.php initialise session, constantes et parametres globaux.
|    - functions.php fournit les helpers admin: isAdmin(), requireAdmin(),
|      verifyAdminLogin(), verify_csrf(), recordFailedLogin(), etc.
|
| 2. Headers de securite
|    - X-Content-Type-Options limite le sniffing MIME.
|    - X-Frame-Options empeche l'affichage dans une iframe.
|    - Cache-Control et Pragma evitent que le navigateur garde des pages admin.
|
| 3. Deconnexion POST
|    - Le logout modifie la session: il doit donc passer par POST + CSRF.
|    - La session est videe, le cookie de session est expire, puis on redirige.
|
| 4. Ancienne deconnexion GET
|    - Compatibilite pour une ancienne URL de logout.
|    - Elle detruit la session et redirige vers /admin/.
|
| 5. Connexion admin
|    - Le formulaire POST verifie d'abord le token CSRF.
|    - Le verrouillage anti-bruteforce bloque apres trop de tentatives.
|    - verifyAdminLogin() controle l'identifiant et le mot de passe.
|    - En cas de succes, session_regenerate_id(true) evite la fixation.
|    - La session stocke IP, User-Agent et last_activity.
|
| 6. Controle anti-detournement de session
|    - Si l'IP ou le User-Agent change pendant une session admin, la session
|      est detruite par prudence et l'utilisateur revient a la connexion.
|
| 7. Vue non connectee
|    - Si isAdmin() est faux, le fichier rend uniquement la page login.
|    - Un exit empeche le dashboard d'etre execute apres le formulaire.
|
| 8. Vue dashboard
|    - getDB() ouvre la connexion.
|    - getContactCount() et getProductCount() alimentent les cartes stats.
|    - Les 10 derniers contacts sont affiches dans un tableau resumant le CRM.
|--------------------------------------------------------------------------
*/

// ADMIN INDEX - Role Controller + View
// Cette page a deux comportements:
// 1. Si l'admin n'est pas connecte, elle affiche le formulaire de connexion.
// 2. Si l'admin est connecte, elle affiche le dashboard.
// Elle gere aussi la deconnexion et les protections de session.
// require_once charge la configuration globale avant la logique admin.
require_once __DIR__ . '/../includes/config.php';
// require_once charge les helpers de session, securite, login et affichage.
require_once __DIR__ . '/../includes/functions.php';

// ── Security headers for admin ────────────────────────
// header() ajoute une protection contre le sniffing MIME.
header('X-Content-Type-Options: nosniff');
// header() empeche l'inclusion de l'admin dans une iframe.
header('X-Frame-Options: DENY');
// header() active une protection XSS pour les anciens navigateurs.
header('X-XSS-Protection: 1; mode=block');
// header() interdit la mise en cache des pages admin.
header('Cache-Control: no-store, no-cache, must-revalidate, private');
// header() complete la consigne no-cache pour compatibilite.
header('Pragma: no-cache');

// ── Logout (POST only for CSRF safety) ────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // La deconnexion modifie l'etat de session: elle doit donc verifier le CSRF.
    // verifyAdminCsrf() valide le token CSRF de la deconnexion.
    if (!verifyAdminCsrf()) {
        header('Location: ' . site_url('/admin/'));
        exit;
    }
    $_SESSION = [];
    // Suppression du cookie de session cote navigateur.
    // ini_get() verifie si PHP utilise un cookie de session.
    if (ini_get('session.use_cookies')) {
        // session_get_cookie_params() recupere les options du cookie de session.
        $p = session_get_cookie_params();
        // setcookie(), session_name() et time() expirent le cookie cote navigateur.
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    // session_destroy() detruit les donnees de session cote serveur.
    session_destroy();
    header('Location: ' . site_url('/admin/'));
    exit;
}

// Legacy GET logout (redirect to proper logout)
if (isset($_GET['logout'])) {
    // Compatibilite ancienne URL: conservee mais redirige vers l'etat deconnecte.
    $_SESSION = [];
    // session_destroy() ferme aussi les anciennes deconnexions en GET.
    session_destroy();
    header('Location: ' . site_url('/admin/'));
    exit;
}

// ── Login ─────────────────────────────────────────────
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    // Check brute-force lockout
    // verify_csrf() controle le token du formulaire de connexion.
    if (!verify_csrf()) {
        // Le formulaire de login contient aussi un token CSRF.
        $error = 'Token de securite invalide. Rechargez la page.';
    // isLoginLocked() indique si trop de tentatives ont ete faites.
    } elseif (isLoginLocked()) {
        // getRemainingLockoutTime() recupere le temps restant du verrouillage.
        $remaining = getRemainingLockoutTime();
        // ceil() arrondit le temps restant en minutes lisibles.
        $minutes = (int)ceil($remaining / 60);
        $error = "Trop de tentatives. Réessayez dans {$minutes} min.";
    } else {
        // trim() retire les espaces autour de l'identifiant.
        $user = trim($_POST['username'] ?? '');
        $pass = $_POST['password'] ?? '';

        if ($user === '' || $pass === '') {
            $error = 'Veuillez remplir tous les champs.';
            // recordFailedLogin() incremente le compteur d'echecs.
            recordFailedLogin();
        // verifyAdminLogin() compare les identifiants admin.
        } elseif (verifyAdminLogin($user, $pass)) {
            // Success: regenerate session to prevent fixation
            // Regeneration de l'ID pour eviter la fixation de session.
            // session_regenerate_id() evite la fixation de session apres login.
            session_regenerate_id(true);
            $_SESSION['is_admin'] = true;
            $_SESSION['admin_ip'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['admin_ua'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $_SESSION['last_activity'] = time();
            // resetLoginAttempts() remet a zero le compteur d'echecs.
            resetLoginAttempts();
            header('Location: ' . site_url('/admin/'));
            exit;
        } else {
            // recordFailedLogin() memorise chaque tentative incorrecte.
            recordFailedLogin();
            // getLoginAttempts() recupere le nombre d'echecs actuels.
            $attempts = getLoginAttempts();
            $remaining = MAX_LOGIN_ATTEMPTS - $attempts;
            if ($remaining > 0) {
                $error = "Identifiants incorrects. {$remaining} tentative(s) restante(s).";
            } else {
                // ceil() transforme la duree de verrouillage en minutes.
                $minutes = (int)ceil(LOGIN_LOCKOUT_TIME / 60);
                $error = "Compte verrouillé pour {$minutes} minutes.";
            }
        }
    }
}

// ── Session hijacking check ───────────────────────────
// isAdmin() verifie si une session admin est actuellement active.
if (isAdmin()) {
    // Verifie que la session admin reste sur la meme IP et le meme navigateur.
    // Si ces valeurs changent, on force une deconnexion par prudence.
    $currentIp = $_SERVER['REMOTE_ADDR'];
    $currentUa = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (isset($_SESSION['admin_ip']) && $_SESSION['admin_ip'] !== $currentIp) {
        $_SESSION = [];
        // session_destroy() ferme la session si l'IP change.
        session_destroy();
        header('Location: ' . site_url('/admin/'));
        exit;
    }
    if (isset($_SESSION['admin_ua']) && $_SESSION['admin_ua'] !== $currentUa) {
        $_SESSION = [];
        // session_destroy() ferme la session si le navigateur change.
        session_destroy();
        header('Location: ' . site_url('/admin/'));
        exit;
    }
}

// ── Not logged in → show login ────────────────────────
// isAdmin() decide si la page doit afficher le login ou le dashboard.
if (!isAdmin()):
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin — Info-Sup Digital</title>
    <!-- asset_url() genere le chemin public vers le favicon. -->
    <link rel="icon" href="<?= asset_url('images/favicon.svg') ?>" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- asset_url() genere le chemin public vers le CSS admin. -->
    <link rel="stylesheet" href="<?= asset_url('css/admin.css') ?>">
</head>
<body>
<div class="login-page">
    <div class="login-card">
        <div class="logo-icon">IS</div>
        <h1>Admin Panel</h1>
        <p>Info-Sup Digital — Espace administration</p>
        <?php if ($error): ?>
            <!-- sanitize() protege l'affichage du message d'erreur. -->
            <div class="msg msg--error"><?= sanitize($error) ?></div>
        <?php endif; ?>
        <form method="POST" autocomplete="off">
            <!-- csrf_field() ajoute le token CSRF du formulaire de connexion. -->
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="username">Identifiant</label>
                <input type="text" id="username" name="username" required autofocus autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn-admin btn-admin--primary">Se connecter</button>
        </form>
    </div>
</div>
</body>
</html>
<?php exit; endif;

// ── Dashboard ─────────────────────────────────────────
try {
    // getDB() ouvre une connexion PDO pour charger les stats admin.
    $pdo = getDB();
    // Donnees de synthese affichees dans le dashboard.
    // getContactCount() compte les messages recus.
    $contactCount = getContactCount($pdo);
    // getProductCount() compte les produits du catalogue.
    $productCount = getProductCount($pdo);
    // query() lit les derniers contacts et fetchAll() les retourne en tableau.
    $recentContacts = $pdo->query('SELECT * FROM contacts ORDER BY created_at DESC LIMIT 10')->fetchAll();
} catch (PDOException) {
    $contactCount = 0; $productCount = 0; $recentContacts = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Dashboard — Admin Info-Sup</title>
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
        <a href="<?= site_url('/admin/') ?>" class="active">Dashboard</a>
        <a href="<?= site_url('/admin/products.php') ?>">Produits</a>
        <a href="<?= site_url('/admin/contacts.php') ?>">Contacts</a>
        <a href="<?= site_url('/') ?>" target="_blank">Voir le site</a>
        <form method="POST" style="display:inline">
            <!-- csrf_field() protege le formulaire de deconnexion. -->
            <?= csrf_field() ?>
            <input type="hidden" name="logout" value="1">
            <button type="submit" style="background:none;border:none;color:inherit;cursor:pointer;font:inherit;padding:8px 12px">Deconnexion</button>
        </form>
    </nav>
</header>

<main class="admin-main">
    <h1 class="admin-title">Dashboard</h1>

    <div class="stats-grid">
        <div class="stat-card stat-card--orange">
            <div class="stat-card__label">Contacts recus</div>
            <div class="stat-card__value"><?= $contactCount ?></div>
        </div>
        <div class="stat-card stat-card--orange">
            <div class="stat-card__label">Produits en catalogue</div>
            <div class="stat-card__value"><?= $productCount ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-card__label">Services</div>
            <div class="stat-card__value">3</div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card__header">
            <h3>Derniers contacts</h3>
            <!-- site_url() construit le lien vers la liste complete des contacts. -->
            <a href="<?= site_url('/admin/contacts.php') ?>" class="btn-admin btn-admin--outline btn-admin--sm">Voir tout</a>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Telephone</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentContacts)): ?>
                    <tr><td colspan="5" style="text-align:center;color:#999;padding:32px">Aucun contact pour le moment</td></tr>
                <?php else: ?>
                    <!-- foreach parcourt les derniers contacts affiches sur le dashboard. -->
                    <?php foreach ($recentContacts as $c): ?>
                    <tr>
                        <!-- sanitize() protege les donnees contact affichees dans le tableau. -->
                        <td><strong><?= sanitize($c['name']) ?></strong></td>
                        <td><?= sanitize($c['email']) ?></td>
                        <td><?= sanitize($c['phone'] ?: '') ?></td>
                        <td style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= sanitize($c['message']) ?></td>
                        <!-- timeAgo() transforme la date en libelle relatif lisible. -->
                        <td><?= timeAgo($c['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
