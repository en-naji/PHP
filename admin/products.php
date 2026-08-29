<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ARBORESCENCE DETAILLEE - ADMIN / PRODUCTS
|--------------------------------------------------------------------------
| Role de ce fichier
| - Combine un controleur admin et une vue HTML pour gerer le catalogue.
| - Permet d'ajouter, modifier, supprimer et lister les produits.
| - Toutes les actions sensibles passent par POST + token CSRF admin.
|
| 1. Initialisation et securite
|    - config.php charge la session, les constantes et la connexion applicative.
|    - functions.php charge les helpers admin, validation, upload et affichage.
|    - requireAdmin() bloque l'acces si l'utilisateur n'est pas connecte.
|    - Les headers reduisent les risques de sniffing, iframe et cache admin.
|
| 2. Donnees de travail
|    - $pdo contient la connexion PDO.
|    - $msg et $msgType pilotent les messages d'interface.
|    - $categories est une whitelist: seules ces categories peuvent etre
|      enregistrees en base.
|    - $editProduct contient le produit a pre-remplir dans le formulaire.
|
| 3. Suppression d'un produit
|    - Detectee par POST + delete_id.
|    - verifyAdminCsrf() valide le token.
|    - L'id est caste en entier puis supprime via requete preparee.
|    - Une redirection GET evite une nouvelle suppression au rafraichissement.
|
| 4. Modification d'un produit
|    - Detectee par POST + update_id.
|    - Les champs sont nettoyes, la categorie est controlee par whitelist.
|    - L'image existante reste conservee si aucun fichier n'est envoye.
|    - Si un fichier est uploade, validateUploadedFile() controle le fichier,
|      puis un nom aleatoire evite collisions et noms dangereux.
|    - L'UPDATE est prepare, puis la page redirige avec ?updated=1.
|
| 5. Ajout d'un produit
|    - Detecte par POST avec name et sans update_id.
|    - Reutilise la meme logique de nettoyage, categorie et upload.
|    - Insere les champs dans products via requete preparee.
|
| 6. Chargement pour edition
|    - Un GET edit_id charge le produit a modifier.
|    - Si l'id n'existe pas, un message d'erreur est affiche.
|
| 7. Liste des produits
|    - Tous les produits sont charges du plus recent au plus ancien.
|    - Le tableau admin affiche image, nom, categorie, marque, prix et actions.
|
| 8. Vue HTML
|    - Header admin: navigation, lien site public et formulaire de deconnexion.
|    - Formulaire produit: mode ajout ou edition selon $isEditing.
|    - Tableau: boutons Modifier et Supprimer, avec CSRF sur chaque suppression.
|--------------------------------------------------------------------------
*/

// ── ARBORESCENCE DE LA PAGE ADMIN PRODUITS ──────────────────────────────
// ├─ 1. Initialisation de l'administration
// │  ├─ chargement de la configuration
// │  ├─ vérification de l'accès admin
// │  └─ en-têtes de sécurité HTTP
// ├─ 2. Traitement des actions sensibles
// │  ├─ suppression d'un produit (POST + CSRF)
// │  ├─ modification d'un produit (POST + CSRF)
// │  └─ ajout d'un produit (POST + CSRF)
// ├─ 3. Chargement des données
// │  ├─ produit en cours d'édition si demandé
// │  └─ liste complète des produits depuis la base
// └─ 4. Affichage du tableau de bord admin
//    ├─ formulaire d'ajout / modification
//    └─ tableau de produits avec actions

// ADMIN PRODUITS - Role Controller + View
// Controller: traite les actions POST d'ajout, modification et suppression.
// Model: utilise PDO pour inserer/modifier/supprimer dans la table products.
// View: affiche le formulaire d'ajout/modification et le tableau des produits.
// require_once charge la configuration globale de l'application.
require_once __DIR__ . '/../includes/config.php';
// require_once charge les helpers admin, securite, base de donnees et affichage.
require_once __DIR__ . '/../includes/functions.php';
// requireAdmin() bloque l'acces aux utilisateurs non connectes.
requireAdmin();

// header() ajoute une protection contre le sniffing MIME.
header('X-Content-Type-Options: nosniff');
// header() interdit l'affichage de l'admin dans une iframe.
header('X-Frame-Options: DENY');
// header() empeche le navigateur de mettre les pages admin en cache.
header('Cache-Control: no-store, no-cache, must-revalidate, private');

// getDB() ouvre une connexion PDO vers MySQL.
$pdo = getDB();
$msg = '';
$msgType = '';
$categories = ['cameras','dvr_nvr','access','videophone','alarme','reseau','pc','cablage','imprimante'];
$editProduct = null;

// ── Delete (POST + CSRF) ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    // Suppression produit: action critique, donc POST + CSRF obligatoires.
    // verifyAdminCsrf() confirme que la suppression vient du formulaire admin.
    if (!verifyAdminCsrf()) {
        $msg = 'Token de securite invalide. Rechargez la page.';
        $msgType = 'error';
    } else {
        $deleteId = (int)$_POST['delete_id'];
        if ($deleteId > 0) {
            // Requete preparee: l'id est fourni separement du SQL.
            // prepare() cree une requete parametree pour supprimer un seul produit.
            $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
            // execute() injecte l'id caste en entier dans la requete preparee.
            $stmt->execute([$deleteId]);
            // header() redirige apres suppression pour eviter une double action au refresh.
            header('Location: ' . site_url('/admin/products.php?deleted=1'));
            exit;
        }
    }
}
if (isset($_GET['deleted'])) { $msg = 'Produit supprime.'; $msgType = 'success'; }
if (isset($_GET['updated'])) { $msg = 'Produit modifie avec succes.'; $msgType = 'success'; }

// Update Product (POST + CSRF)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    // Modification produit: meme validation que l'ajout, avec conservation de l'image existante.
    // verifyAdminCsrf() protege la modification contre les soumissions externes.
    if (!verifyAdminCsrf()) {
        $msg = 'Token de securite invalide. Rechargez la page.';
        $msgType = 'error';
    } else {
        $updateId  = (int)($_POST['update_id'] ?? 0);
        // sanitize() nettoie tous les champs texte avant validation et stockage.
        $name      = sanitize($_POST['name'] ?? '');
        $category  = sanitize($_POST['category'] ?? '');
        $brand     = sanitize($_POST['brand'] ?? '');
        $tag       = sanitize($_POST['tag'] ?? '');
        $price     = (float)($_POST['price'] ?? 0);
        $oldPrice  = (float)($_POST['old_price'] ?? 0);
        $desc      = sanitize($_POST['description'] ?? '');
        $features  = sanitize($_POST['features'] ?? '');
        $imageUrl  = sanitize($_POST['image_url'] ?? '');
        $featured  = isset($_POST['is_featured']) ? 1 : 0;

        // in_array() verifie que la categorie existe dans la whitelist autorisee.
        if (!in_array($category, $categories, true)) {
            $category = '';
        }

        if (!empty($_FILES['image_file']['name']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            // validateUploadedFile() controle le type, la taille et l'extension.
            $uploadError = validateUploadedFile($_FILES['image_file']);
            if ($uploadError !== null) {
                $msg = $uploadError;
                $msgType = 'error';
            } else {
                $uploadDir = __DIR__ . '/../assets/images/products/';
                // is_dir() verifie que le dossier d'upload existe avant creation.
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                // pathinfo() extrait l'extension et strtolower() la normalise.
                $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
                // random_bytes() et bin2hex() generent un nom de fichier imprevisible.
                $fileName = bin2hex(random_bytes(12)) . '.' . $ext;
                // move_uploaded_file() deplace le fichier valide vers le dossier public.
                move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $fileName);
                $imageUrl = '/assets/images/products/' . $fileName;
            }
        }

        if ($msg === '' && $updateId > 0 && $name && $category && $price > 0) {
            // Mise a jour preparee: l'id et les valeurs utilisateur sont separes du SQL.
            // prepare() cree la requete de mise a jour du produit.
            $stmt = $pdo->prepare('
                UPDATE products
                SET category = ?, brand = ?, name = ?, tag = ?, price = ?, old_price = ?,
                    description = ?, features = ?, image_url = ?, is_featured = ?
                WHERE id = ?
            ');
            // execute() transmet les valeurs nettoyees a la requete UPDATE.
            $stmt->execute([$category, $brand, $name, $tag, $price, $oldPrice ?: null, $desc, $features, $imageUrl, $featured, $updateId]);
            // header() redirige apres sauvegarde pour afficher un etat stable.
            header('Location: ' . site_url('/admin/products.php?updated=1'));
            exit;
        } elseif ($msg === '') {
            $msg = 'Veuillez remplir nom, categorie et prix.';
            $msgType = 'error';
        }

        if ($msgType === 'error') {
            $editProduct = [
                'id'          => $updateId,
                'category'    => $category,
                'brand'       => $brand,
                'name'        => $name,
                'tag'         => $tag,
                'price'       => $price,
                'old_price'   => $oldPrice ?: null,
                'description' => $desc,
                'features'    => $features,
                'image_url'   => $imageUrl,
                'is_featured' => $featured,
            ];
        }
    }
}

// ── Add Product (POST + CSRF) ─────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && !isset($_POST['update_id'])) {
    // Ajout produit: les champs du formulaire sont nettoyes puis valides.
    // verifyAdminCsrf() protege l'ajout contre les soumissions non autorisees.
    if (!verifyAdminCsrf()) {
        $msg = 'Token de securite invalide. Rechargez la page.';
        $msgType = 'error';
    } else {
        // sanitize() nettoie tous les champs texte avant insertion.
        $name      = sanitize($_POST['name'] ?? '');
        $category  = sanitize($_POST['category'] ?? '');
        $brand     = sanitize($_POST['brand'] ?? '');
        $tag       = sanitize($_POST['tag'] ?? '');
        $price     = (float)($_POST['price'] ?? 0);
        $oldPrice  = (float)($_POST['old_price'] ?? 0);
        $desc      = sanitize($_POST['description'] ?? '');
        $features  = sanitize($_POST['features'] ?? '');
        $imageUrl  = sanitize($_POST['image_url'] ?? '');
        $featured  = isset($_POST['is_featured']) ? 1 : 0;

        // Validate category against whitelist
        // Whitelist: evite d'enregistrer des categories inconnues ou injectees.
        // in_array() confirme que la categorie soumise est autorisee.
        if (!in_array($category, $categories, true)) {
            $category = '';
        }

        // Secure file upload
        // L'admin peut fournir une URL image ou uploader un fichier local.
        // En upload, validateUploadedFile() verifie extension, taille et MIME.
        if (!empty($_FILES['image_file']['name']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            // validateUploadedFile() retourne un message si le fichier est refuse.
            $uploadError = validateUploadedFile($_FILES['image_file']);
            if ($uploadError !== null) {
                $msg = $uploadError;
                $msgType = 'error';
            } else {
                $uploadDir = __DIR__ . '/../assets/images/products/';
                // is_dir() verifie le dossier, mkdir() le cree si necessaire.
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                // Nom aleatoire: evite les collisions et les noms dangereux.
                // pathinfo() recupere l'extension et strtolower() la met en minuscule.
                $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
                // random_bytes() et bin2hex() creent un nom difficile a deviner.
                $fileName = bin2hex(random_bytes(12)) . '.' . $ext;
                // move_uploaded_file() finalise l'upload dans le dossier produits.
                move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $fileName);
                $imageUrl = '/assets/images/products/' . $fileName;
            }
        }

        if ($msg === '' && $name && $category && $price > 0) {
            // Insertion du produit dans le catalogue MySQL.
            // prepare() cree une requete INSERT protegee par parametres.
            $stmt = $pdo->prepare('
                INSERT INTO products (category, brand, name, tag, price, old_price, description, features, image_url, is_featured)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');
            // execute() insere le produit avec les valeurs nettoyees.
            $stmt->execute([$category, $brand, $name, $tag, $price, $oldPrice ?: null, $desc, $features, $imageUrl, $featured]);
            $msg = 'Produit ajoute avec succes !';
            $msgType = 'success';
        } elseif ($msg === '') {
            $msg = 'Veuillez remplir nom, categorie et prix.';
            $msgType = 'error';
        }
    }
}

// Edit Product (GET)
if ($editProduct === null && isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    if ($editId > 0) {
        // prepare() cree la requete de recuperation du produit a modifier.
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
        // execute() cherche uniquement l'id demande.
        $stmt->execute([$editId]);
        // fetch() recupere la ligne produit ou false si elle n'existe pas.
        $editProduct = $stmt->fetch() ?: null;
        if ($editProduct === null && $msg === '') {
            $msg = 'Produit introuvable.';
            $msgType = 'error';
        }
    }
}

// Liste affichee dans le tableau admin, du plus recent au plus ancien.
// query() execute la lecture simple et fetchAll() recupere toutes les lignes.
$products = $pdo->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Produits — Admin Info-Sup</title>
    <!-- asset_url() genere le chemin public vers le favicon. -->
    <link rel="icon" href="<?= asset_url('images/favicon.svg') ?>" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- asset_url() genere le chemin public vers la feuille CSS admin. -->
    <link rel="stylesheet" href="<?= asset_url('css/admin.css') ?>">
</head>
<body>

<header class="admin-header">
    <div class="admin-header__left">
        <h2>Info-Sup Admin</h2>
    </div>
    <nav class="admin-header__nav">
        <!-- site_url() construit les liens internes selon la base URL configuree. -->
        <a href="<?= site_url('/admin/') ?>">Dashboard</a>
        <a href="<?= site_url('/admin/products.php') ?>" class="active">Produits</a>
        <a href="<?= site_url('/admin/contacts.php') ?>">Contacts</a>
        <a href="<?= site_url('/') ?>" target="_blank">Voir le site</a>
        <form method="POST" action="<?= site_url('/admin/') ?>" style="display:inline">
            <!-- csrf_field() ajoute le token CSRF pour securiser la deconnexion. -->
            <?= csrf_field() ?>
            <input type="hidden" name="logout" value="1">
            <button type="submit" style="background:none;border:none;color:inherit;cursor:pointer;font:inherit;padding:8px 12px">Deconnexion</button>
        </form>
    </nav>
</header>

<main class="admin-main">
    <h1 class="admin-title">Gestion des produits</h1>

    <?php if ($msg): ?>
        <!-- sanitize() protege l'affichage du message admin. -->
        <div class="msg msg--<?= $msgType ?>"><?= sanitize($msg) ?></div>
    <?php endif; ?>

    <?php $isEditing = $editProduct !== null; ?>
    <div class="add-form" id="productForm">
        <h3><?= $isEditing ? 'Modifier le produit' : 'Ajouter un produit' ?></h3>
        <form method="POST" enctype="multipart/form-data">
            <!-- csrf_field() ajoute le token CSRF pour l'ajout ou la modification. -->
            <?= csrf_field() ?>
            <?php if ($isEditing): ?>
                <input type="hidden" name="update_id" value="<?= (int)$editProduct['id'] ?>">
            <?php endif; ?>
            <!-- sanitize() pre-remplit les champs en mode edition sans exposer de HTML utilisateur. -->
            <div class="form-row">
                <div class="form-group">
                    <label>Nom du produit *</label>
                    <input type="text" name="name" required placeholder="Ex: DS-2CD2143G2-I" maxlength="200" value="<?= $isEditing ? sanitize((string)$editProduct['name']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Categorie *</label>
                    <select name="category" required>
                        <option value="">-- Choisir --</option>
                        <?php
                        // str_replace() rend la categorie lisible, ucfirst() met la premiere lettre en majuscule.
                        foreach ($categories as $cat): ?>
                            <option value="<?= $cat ?>" <?= $isEditing && $editProduct['category'] === $cat ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$cat)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Marque</label>
                    <input type="text" name="brand" placeholder="Ex: Hikvision" maxlength="100" value="<?= $isEditing ? sanitize((string)$editProduct['brand']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Tag / Badge</label>
                    <input type="text" name="tag" placeholder="Ex: Nouveau, Promo" maxlength="50" value="<?= $isEditing ? sanitize((string)$editProduct['tag']) : '' ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Prix (MAD) *</label>
                    <input type="number" name="price" step="0.01" min="0" max="999999" required placeholder="0.00" value="<?= $isEditing ? sanitize((string)$editProduct['price']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Ancien prix (MAD)</label>
                    <input type="number" name="old_price" step="0.01" min="0" max="999999" placeholder="0.00" value="<?= $isEditing && $editProduct['old_price'] !== null ? sanitize((string)$editProduct['old_price']) : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="2" placeholder="Description courte du produit" maxlength="1000"><?= $isEditing ? sanitize((string)$editProduct['description']) : '' ?></textarea>
            </div>
            <div class="form-group">
                <label>Caracteristiques (separees par |)</label>
                <input type="text" name="features" placeholder="4MP|IR 30m|IP67|WDR 120dB" maxlength="500" value="<?= $isEditing ? sanitize((string)$editProduct['features']) : '' ?>">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Image (fichier)</label>
                    <input type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp,.gif">
                </div>
                <div class="form-group">
                    <label>Ou URL / chemin image</label>
                    <input type="text" name="image_url" placeholder="https://... ou /assets/images/products/image.jpg" maxlength="500" value="<?= $isEditing ? sanitize((string)$editProduct['image_url']) : '' ?>">
                </div>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" name="is_featured" id="is_featured" <?= $isEditing && (int)$editProduct['is_featured'] === 1 ? 'checked' : '' ?>>
                <label for="is_featured">Produit en vedette</label>
            </div>
            <button type="submit" class="btn-admin btn-admin--primary">
                <?= $isEditing ? 'Enregistrer les modifications' : 'Ajouter le produit' ?>
            </button>
            <?php if ($isEditing): ?>
                <!-- site_url() renvoie vers la liste sans parametre d'edition. -->
                <a href="<?= site_url('/admin/products.php') ?>" class="btn-admin btn-admin--outline" style="margin-top:10px;text-decoration:none;width:100%">Annuler la modification</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="admin-card">
        <div class="admin-card__header">
            <!-- count() affiche le nombre de produits charges depuis la base. -->
            <h3>Liste des produits (<?= count($products) ?>)</h3>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Categorie</th>
                    <th>Marque</th>
                    <th>Prix</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- foreach parcourt chaque produit pour construire une ligne du tableau. -->
                <?php foreach ($products as $p): ?>
                <tr>
                    <td>
                        <?php if ($p['image_url']): ?>
                            <!-- site_url() genere l'URL de l'image et sanitize() securise l'attribut src. -->
                            <img src="<?= sanitize(site_url($p['image_url'])) ?>" class="thumb" alt="">
                        <?php else: ?>
                            <span style="color:#ccc">—</span>
                        <?php endif; ?>
                    </td>
                    <!-- sanitize() evite l'injection HTML dans les cellules texte. -->
                    <td><strong><?= sanitize($p['name']) ?></strong></td>
                    <td><?= sanitize($p['category']) ?></td>
                    <td><?= sanitize($p['brand']) ?></td>
                    <!-- formatPrice() affiche le montant au format prix lisible. -->
                    <td><?= formatPrice((float)$p['price']) ?></td>
                    <td>
                        <div style="display:flex;gap:8px;flex-wrap:wrap">
                            <!-- site_url() construit le lien d'edition avec l'id du produit. -->
                            <a href="<?= site_url('/admin/products.php?edit_id=' . (int)$p['id'] . '#productForm') ?>" class="btn-admin btn-admin--outline btn-admin--sm" style="text-decoration:none">Modifier</a>
                            <form method="POST" style="display:inline" onsubmit="return confirm('Supprimer ce produit ?')">
                                <!-- csrf_field() protege chaque formulaire de suppression. -->
                                <?= csrf_field() ?>
                                <input type="hidden" name="delete_id" value="<?= (int)$p['id'] ?>">
                                <button type="submit" class="btn-admin btn-admin--danger">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
