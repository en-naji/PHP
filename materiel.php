<?php
declare(strict_types=1);

// PAGE MATERIEL - Role Controller + View
// Controller: charge les statistiques, categories et produits depuis MySQL.
// View: affiche le catalogue, les filtres et les boutons "Ajouter au panier".
// Model: les requetes reutilisables sont dans includes/functions.php.
$currentPage = 'materiel';
$pageTitle = 'Matériel Informatique — Info-Sup Digital';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// ── Données (charger TOUS les produits) ───────────────
try {
    $pdo = getDB();

    // Statistiques affichees dans le hero de la page.
    $totalProducts = getProductCount($pdo);
    $totalBrands   = getBrandCount($pdo);
    $totalCats     = getCategoryCount($pdo);

    // Catégories avec comptage
    // Cette requete construit automatiquement les onglets de filtre.
    $catStmt = $pdo->query('SELECT category, COUNT(*) as cnt FROM products GROUP BY category ORDER BY cnt DESC');
    $categories = $catStmt->fetchAll();

    // Charger TOUS les produits (filtrage côté client)
    // Le filtrage par categorie est fait ensuite par JavaScript, sans recharger la page.
    $products = getProducts($pdo, '');
} catch (PDOException $e) {
    // Si MySQL est indisponible, la page reste affichable avec un catalogue vide.
    $totalProducts = 0; $totalBrands = 0; $totalCats = 0;
    $categories = []; $products = [];
}

require_once __DIR__ . '/includes/header.php';

// Traduction des noms techniques de categories en libelles lisibles.
$categoryLabels = [
    'cameras'     => 'Caméras IP',
    'dvr_nvr'     => 'DVR / NVR',
    'access'      => 'Contrôle d\'Accès',
    'videophone'  => 'Vidéophones',
    'alarme'      => 'Alarmes',
    'reseau'      => 'Réseaux',
    'pc'          => 'PC & Laptops',
    'cablage'     => 'Câblage',
    'imprimante'  => 'Imprimantes',
];
?>

<!-- ═══ PAGE HERO ═══ -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero__content fade-in">
            <span class="section-head__tag">Matériel IT</span>
            <h1>Équipement <span class="text-gradient">professionnel</span></h1>
            <p>Caméras, réseaux, contrôle d'accès, PC et plus. Matériel certifié avec garantie constructeur.</p>
        </div>
        <div class="page-hero__stats fade-in">
            <div class="page-hero__stat">
                <strong data-count="<?= $totalProducts ?>" data-suffix="+">0</strong>
                <span>Produits</span>
            </div>
            <div class="page-hero__stat">
                <strong data-count="<?= $totalBrands ?>" data-suffix="">0</strong>
                <span>Marques</span>
            </div>
            <div class="page-hero__stat">
                <strong data-count="<?= $totalCats ?>" data-suffix="">0</strong>
                <span>Catégories</span>
            </div>
        </div>
    </div>
</section>

<!-- ═══ CATALOGUE ═══ -->
<section class="section section--alt">
    <div class="container">
        <div class="section-head fade-in">
            <span class="section-head__tag">Catalogue</span>
            <h2>Tous nos produits</h2>
        </div>

        <!-- Category Tabs (JS filtering, no page reload) -->
        <div class="cat-tabs fade-in" id="catTabs">
            <button type="button" class="cat-tab cat-tab--active" data-cat="all">
                Tous <span class="cat-tab__count"><?= $totalProducts ?></span>
            </button>
            <?php foreach ($categories as $cat): ?>
            <button type="button" class="cat-tab" data-cat="<?= sanitize($cat['category']) ?>">
                <?= $categoryLabels[$cat['category']] ?? ucfirst($cat['category']) ?>
                <span class="cat-tab__count"><?= $cat['cnt'] ?></span>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Products Grid -->
        <?php if (empty($products)): ?>
            <div class="empty-state fade-in" id="emptyState">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <p>Aucun produit dans cette catégorie pour le moment.</p>
            </div>
        <?php else: ?>
        <div class="products__grid" id="productsGrid">
            <?php foreach ($products as $p): ?>
            <div class="product-card fade-in" data-category="<?= sanitize($p['category']) ?>">
                <?php if ($p['tag']): ?><span class="product-card__badge"><?= sanitize($p['tag']) ?></span><?php endif; ?>
                <div class="product-card__image">
                    <?php if ($p['image_url']): ?>
                        <img src="<?= sanitize(site_url($p['image_url'])) ?>" alt="<?= sanitize($p['name']) ?>" loading="lazy">
                    <?php else: ?>
                        <div class="product-card__placeholder">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="product-card__body">
                    <span class="product-card__brand"><?= sanitize($p['brand']) ?></span>
                    <h3><?= sanitize($p['name']) ?></h3>
                    <?php if ($p['features']):
                        $feats = explode('|', $p['features']); ?>
                        <ul class="product-card__features">
                            <?php foreach (array_slice($feats, 0, 3) as $f): ?>
                                <li><?= sanitize($f) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <div class="product-card__pricing">
                        <span class="product-card__price"><?= formatPrice((float)$p['price']) ?></span>
                        <?php if ($p['old_price']): ?>
                            <span class="product-card__old-price"><?= formatPrice((float)$p['old_price']) ?></span>
                        <?php endif; ?>
                    </div>
                    <!-- Les attributs data-* sont lus par main.js pour ajouter le produit au panier. -->
                    <button type="button" class="btn btn--cart btn--sm btn--full"
                        data-id="<?= (int)$p['id'] ?>"
                        data-name="<?= sanitize($p['brand'] . ' ' . $p['name']) ?>"
                        data-price="<?= (float)$p['price'] ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        Ajouter au panier
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ═══ REASSURANCE ═══ -->
<section class="section">
    <div class="container">
        <div class="reassurance__grid">
            <div class="reassurance-card fade-in">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#FF5C00" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                <h4>Garantie constructeur</h4>
                <p>Tous nos produits sont certifiés et couverts par la garantie officielle.</p>
            </div>
            <div class="reassurance-card fade-in">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#FF5C00" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                <h4>Livraison Maroc</h4>
                <p>Livraison rapide dans tout le Maroc avec suivi en temps réel.</p>
            </div>
            <div class="reassurance-card fade-in">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#FF5C00" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <h4>Support technique</h4>
                <p>Assistance technique par téléphone et WhatsApp 6j/7.</p>
            </div>
            <div class="reassurance-card fade-in">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#FF5C00" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                <h4>Paiement flexible</h4>
                <p>Paiement à la livraison, virement bancaire ou facilités de paiement.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══ CTA ═══ -->
<section class="cta">
    <div class="container">
        <div class="cta__box fade-in">
            <h2>Besoin d'un devis personnalisé ?</h2>
            <p>Contactez-nous pour un devis sur mesure avec les meilleurs prix du marché.</p>
            <div class="cta__actions">
                <button class="btn btn--white btn--lg" onclick="openContactModal()">Demander un devis</button>
                <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>" class="btn btn--outline-white btn--lg" target="_blank">WhatsApp</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
