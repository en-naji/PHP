<?php
declare(strict_types=1);

/**
 * Info-Sup Digital — Configuration
 * PHP 8.4 | Hostinger Shared Hosting
 *
 * Role MVC:
 * - Ce fichier sert de configuration globale pour tout le site.
 * - Il initialise aussi la session et cree l'objet PDO utilise par la couche "Model".
 * - Toutes les pages incluent ce fichier avant d'appeler la base de donnees.
 */

// ── Environnement ──────────────────────────────────
// Ces constantes decrivent l'identite publique du site.
// Elles sont utilisees dans les vues: titres, footer, emails, liens absolus.
define('APP_NAME',    'Info-Sup Digital');
define('APP_URL',     'https://info-sup.com');
define('APP_VERSION', '5.0.1');
define('APP_LOCALE',  'fr_FR');

// ── Base de données ────────────────────────────────
// Les valeurs peuvent venir des variables d'environnement du serveur.
// Cela permet de deployer le meme code sur local, test et production.
// Si aucune variable n'est definie, les valeurs par defaut sont utilisees.
define('DB_HOST', 'localhost');
define('DB_NAME', 'info-sup');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ── Contact ────────────────────────────────────────
// Donnees affichees dans le footer et utilisees pour WhatsApp/email.
define('WHATSAPP_NUMBER', '212614516545');
define('CONTACT_EMAIL',   'contact@info-sup.com');
define('COMPANY_PHONE',   '+212 614 516 545');
define('COMPANY_CITY',    'Casablanca, Maroc');

// ── Admin (bcrypt hash — NEVER store plain text) ───
define('ADMIN_USER', 'administrateur');
define('ADMIN_PASS_HASH', '$2y$12$K0.GzoHZheI9jBCkvsQAo.Gnp9Fc.NzS.fGDgoG1DYl07ux/U15HC');

// ── Sécurité ───────────────────────────────────────
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes
define('CSRF_TOKEN_EXPIRY', 3600); // 1 heure
$isSecureRequest = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['SERVER_PORT'] ?? '') === '443');
define('SESSION_LIFETIME', 1800);  // 30 minutes inactivité

// ── Connexion PDO ──────────────────────────────────
// getDB() cree l'objet PDO une seule fois puis le reutilise.
// C'est l'objet principal qui permet aux pages/controlleurs de parler a MySQL.
function getDB(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        // DSN MySQL: host + nom de base + encodage UTF-8 complet.
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            // Les erreurs PDO deviennent des exceptions: plus simple a gerer avec try/catch.
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // Les resultats SELECT sont recuperes comme tableaux associatifs.
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Les vraies requetes preparees MySQL sont utilisees.
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

// ── Session sécurisée ──────────────────────────────
// La session est demarree ici pour que CSRF, login admin et rate limiting
// soient disponibles dans toutes les pages du site.
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly'  => true,
        'cookie_samesite'  => 'Strict',
        'cookie_secure'    => $isSecureRequest,
        'use_strict_mode'  => true,
        'use_only_cookies' => true,
        'gc_maxlifetime'   => SESSION_LIFETIME,
    ]);
}

// ── Auto-expire session après inactivité ───────────
// Si l'utilisateur reste inactif trop longtemps, la session est detruite.
// Cela protege l'espace admin si une session reste ouverte sur un poste public.
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_LIFETIME) {
    session_unset();
    session_destroy();
    session_start([
        'cookie_httponly'  => true,
        'cookie_samesite'  => 'Strict',
        'cookie_secure'    => $isSecureRequest,
        'use_strict_mode'  => true,
        'use_only_cookies' => true,
    ]);
}
$_SESSION['last_activity'] = time();
