<?php
declare(strict_types=1);

/**
 * Info-Sup Digital — Fonctions utilitaires
 * PHP 8.4 — Sécurité renforcée
 */

// ── Sécurité : Sanitization ──────────────────────────
// Nettoie une valeur avant affichage dans le HTML pour reduire les risques XSS.
function sanitize(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// Retourne le sous-dossier public de l'application.
// Exemple XAMPP: /en-naji.com ; production en racine: chaine vide.
function app_base_path(): string
{
    static $basePath = null;
    if ($basePath !== null) {
        return $basePath;
    }

    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

    if (str_ends_with($dir, '/admin')) {
        $dir = substr($dir, 0, -6);
    }

    $basePath = ($dir === '' || $dir === '.' || $dir === '/') ? '' : $dir;
    return $basePath;
}

// Construit une URL interne compatible avec localhost/en-naji.com et production.
function site_url(string $path = ''): string
{
    if (preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, 'mailto:') || str_starts_with($path, 'tel:')) {
        return $path;
    }

    if (str_starts_with($path, '#')) {
        return app_base_path() . '/' . $path;
    }

    $path = '/' . ltrim($path, '/');
    if ($path === '/') {
        return app_base_path() !== '' ? app_base_path() . '/' : '/';
    }

    return app_base_path() . $path;
}

// Raccourci pour les fichiers CSS, JS, images et uploads.
function asset_url(string $path): string
{
    if (preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, 'data:')) {
        return $path;
    }

    if (str_starts_with($path, '/assets/')) {
        return site_url($path);
    }

    return site_url('/assets/' . ltrim($path, '/'));
}

// ── Sécurité : CSRF ──────────────────────────────────
// Cree ou reutilise un token CSRF stocke en session.
// Ce token prouve que le formulaire vient bien du site.
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_token_time'])
        || (time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_EXPIRY) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

// Genere le champ hidden a inserer dans les formulaires sensibles.
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

// Verifie le token CSRF envoye par un formulaire public.
function verify_csrf(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    if ($token === '' || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// ── Sécurité : Anti brute-force ──────────────────────
// Retourne le nombre d'essais de connexion admin rates dans la session.
function getLoginAttempts(): int
{
    return (int)($_SESSION['login_attempts'] ?? 0);
}

// Bloque temporairement la connexion si trop d'essais ont echoue.
function isLoginLocked(): bool
{
    if (getLoginAttempts() >= MAX_LOGIN_ATTEMPTS) {
        $lockTime = $_SESSION['login_lock_time'] ?? 0;
        if ((time() - $lockTime) < LOGIN_LOCKOUT_TIME) {
            return true;
        }
        // Lockout expired, reset
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['login_lock_time']);
    }
    return false;
}

// Enregistre un echec de connexion et active le verrouillage si necessaire.
function recordFailedLogin(): void
{
    $_SESSION['login_attempts'] = getLoginAttempts() + 1;
    if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
        $_SESSION['login_lock_time'] = time();
    }
}

// Remet le compteur d'echecs a zero apres une connexion reussie.
function resetLoginAttempts(): void
{
    $_SESSION['login_attempts'] = 0;
    unset($_SESSION['login_lock_time']);
}

// Calcule le temps restant avant une nouvelle tentative autorisee.
function getRemainingLockoutTime(): int
{
    $lockTime = $_SESSION['login_lock_time'] ?? 0;
    $remaining = LOGIN_LOCKOUT_TIME - (time() - $lockTime);
    return max(0, $remaining);
}

// ── Sécurité : Admin auth ────────────────────────────
// Verifie les identifiants admin: username en comparaison sure, mot de passe en bcrypt.
function verifyAdminLogin(string $user, string $pass): bool
{
    // Timing-safe comparison for username
    $userValid = hash_equals(ADMIN_USER, $user);
    // Bcrypt verification for password
    $passValid = password_verify($pass, ADMIN_PASS_HASH);
    return $userValid && $passValid;
}

// ── Validation ───────────────────────────────────────
// Valide le format d'une adresse email avant insertion en base.
function isValidEmail(string $email): bool
{
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Valide un numero de telephone simple: chiffres, espaces, tirets, parentheses, +.
function isValidPhone(string $phone): bool
{
    return (bool) preg_match('/^[\+]?[0-9\s\-\(\)]{7,20}$/', $phone);
}

// ── Admin ────────────────────────────────────────────
// Indique si la session courante est authentifiee comme administrateur.
function isAdmin(): bool
{
    return ($_SESSION['is_admin'] ?? false) === true;
}

// Protege une page admin: si l'utilisateur n'est pas connecte, retour au login.
function requireAdmin(): void
{
    if (!isAdmin()) {
        header('Location: ' . site_url('/admin/'));
        exit;
    }
}

// ── Sécurité : Verify admin CSRF for state-changing ──
// Verifie un token CSRF pour les actions admin qui modifient les donnees.
function verifyAdminCsrf(): bool
{
    $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    if ($token === '' || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// ── Base de données ──────────────────────────────────
// Recupere les produits du catalogue, avec filtre categorie optionnel.
function getProducts(PDO $pdo, string $category = ''): array
{
    if ($category !== '' && $category !== 'all') {
        $stmt = $pdo->prepare('SELECT * FROM products WHERE category = :cat ORDER BY is_featured DESC, id DESC');
        $stmt->execute(['cat' => $category]);
    } else {
        $stmt = $pdo->query('SELECT * FROM products ORDER BY is_featured DESC, id DESC');
    }
    return $stmt->fetchAll();
}

// Recupere quelques produits en vedette pour une mise en avant eventuelle.
function getFeaturedProducts(PDO $pdo, int $limit = 4): array
{
    $stmt = $pdo->prepare('SELECT * FROM products WHERE is_featured = 1 ORDER BY RAND() LIMIT :lim');
    $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Compte tous les produits du catalogue.
function getProductCount(PDO $pdo): int
{
    return (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
}

// Compte le nombre de marques differentes presentes dans le catalogue.
function getBrandCount(PDO $pdo): int
{
    return (int) $pdo->query('SELECT COUNT(DISTINCT brand) FROM products')->fetchColumn();
}

// Compte le nombre de categories differentes presentes dans le catalogue.
function getCategoryCount(PDO $pdo): int
{
    return (int) $pdo->query('SELECT COUNT(DISTINCT category) FROM products')->fetchColumn();
}

// Recupere tous les messages de contact, du plus recent au plus ancien.
function getContacts(PDO $pdo): array
{
    return $pdo->query('SELECT * FROM contacts ORDER BY created_at DESC')->fetchAll();
}

// Compte tous les messages recus via le formulaire de devis.
function getContactCount(PDO $pdo): int
{
    return (int) $pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn();
}

// ── Réponse JSON ─────────────────────────────────────
// Termine une requete AJAX avec une reponse JSON standardisee.
function jsonResponse(bool $success, string $message, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => $success, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

// ── Helpers ──────────────────────────────────────────
// Formate un prix en dirhams marocains pour l'affichage public/admin.
function formatPrice(float $price): string
{
    return number_format($price, 0, ',', ' ') . ' MAD';
}

// Affiche une date de maniere lisible dans l'administration.
function timeAgo(string $datetime): string
{
    $now  = new DateTimeImmutable();
    $past = new DateTimeImmutable($datetime);
    $diff = $now->diff($past);

    if ($diff->days === 0) return "Aujourd'hui";
    if ($diff->days === 1) return 'Hier';
    if ($diff->days < 7)   return $diff->days . ' jours';
    if ($diff->days < 30)  return ceil($diff->days / 7) . ' sem.';
    return $past->format('d/m/Y');
}

// ── Sécurité : Validation fichier upload ─────────────
// Verifie qu'un fichier image uploade est valide avant de le placer sur le serveur.
function validateUploadedFile(array $file, array $allowedTypes = ['jpg','jpeg','png','webp','gif'], int $maxSize = 5242880): ?string
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return 'Erreur lors du téléchargement.';
    }
    if ($file['size'] > $maxSize) {
        return 'Fichier trop volumineux (max ' . round($maxSize / 1048576) . ' MB).';
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes, true)) {
        return 'Type de fichier non autorisé.';
    }
    // Verify MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowedMimes = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'webp' => 'image/webp',
        'gif'  => 'image/gif',
    ];
    if (!isset($allowedMimes[$ext]) || $allowedMimes[$ext] !== $mime) {
        return 'Le contenu du fichier ne correspond pas à son extension.';
    }
    return null; // Valid
}

// ── Sécurité : Rate limiting (contact form) ──────────
// Limite le nombre d'envois du formulaire contact dans une session.
function isRateLimited(string $key = 'contact_submit', int $maxPerHour = 10): bool
{
    $now = time();
    $sessionKey = 'rate_' . $key;
    $timestamps = $_SESSION[$sessionKey] ?? [];

    // Remove entries older than 1 hour
    $timestamps = array_filter($timestamps, fn(int $t) => ($now - $t) < 3600);

    if (count($timestamps) >= $maxPerHour) {
        return true;
    }

    $timestamps[] = $now;
    $_SESSION[$sessionKey] = array_values($timestamps);
    return false;
}
