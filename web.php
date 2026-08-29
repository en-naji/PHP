<?php
declare(strict_types=1);

// PAGE CREATION WEB - Role View
// Cette page presente les offres de creation de sites web:
// portfolio, forfaits, tarifs et appel a devis.
// Les donnees sont statiques dans un tableau PHP local ($projects).
$currentPage = 'web';
$pageTitle = 'Création de Sites Web — Info-Sup Digital';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ═══ PAGE HERO ═══ -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero__content fade-in">
            <span class="section-head__tag">Création Web</span>
            <h1>Des sites web qui <span class="text-gradient">convertissent</span></h1>
            <p>Sites vitrine, e-commerce et applications web professionnels, optimisés pour le marché marocain.</p>
        </div>
    </div>
</section>

<!-- ═══ PORTFOLIO ═══ -->
<section class="section">
    <div class="container">
        <div class="section-head fade-in">
            <span class="section-head__tag">Réalisations</span>
            <h2>Nos derniers projets web</h2>
            <p>Découvrez quelques-unes de nos réalisations récentes</p>
        </div>
        <div class="portfolio__grid">
            <?php
            // Donnees de portfolio creees sous forme de tableau.
            // Chaque element contient: nom, description, type, couleur et tag affiche.
            $projects = [
                ['E-Commerce Fashion',  'Boutique en ligne avec paiement intégré et gestion de stock', 'e-commerce', '#FF5C00', 'MODE'],
                ['Restaurant Gourmet',  'Site vitrine avec réservation en ligne et menu interactif',   'restaurant', '#059669', 'FOOD'],
                ['Clinique Dentaire',   'Plateforme médicale avec prise de RDV et espace patient',     'medical',    '#2563EB', 'SANTÉ'],
                ['Agence Immobilière',  'Portail immobilier avec recherche avancée et géolocalisation','immobilier', '#7C3AED', 'IMMO'],
                ['Hôtel & Riad',        'Site hôtelier avec moteur de réservation et galerie photo',   'hotel',      '#0891B2', 'TRAVEL'],
                ['Cabinet Juridique',   'Site institutionnel avec blog juridique et consultation RDV',  'juridique',  '#DC2626', 'DROIT'],
            ];
            foreach ($projects as [$name, $desc, $type, $color, $tag]): ?>
            <div class="portfolio-card fade-in">
                <div class="portfolio-card__preview" style="background:<?= $color ?>15">
                    <div class="portfolio-card__mockup" style="border-color:<?= $color ?>40">
                        <div class="portfolio-card__dots">
                            <span style="background:#EF4444"></span>
                            <span style="background:#F59E0B"></span>
                            <span style="background:#22C55E"></span>
                        </div>
                        <div class="portfolio-card__screen" style="background:<?= $color ?>10">
                            <div style="width:60%;height:8px;background:<?= $color ?>30;border-radius:4px;margin-bottom:8px"></div>
                            <div style="width:80%;height:6px;background:<?= $color ?>20;border-radius:3px;margin-bottom:6px"></div>
                            <div style="width:40%;height:6px;background:<?= $color ?>20;border-radius:3px"></div>
                        </div>
                    </div>
                    <span class="portfolio-card__tag" style="background:<?= $color ?>"><?= $tag ?></span>
                </div>
                <div class="portfolio-card__info">
                    <h3><?= $name ?></h3>
                    <p><?= $desc ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══ PRICING ═══ -->
<section class="section section--alt">
    <div class="container">
        <div class="section-head fade-in">
            <span class="section-head__tag">Tarifs</span>
            <h2>Des forfaits adaptés à chaque besoin</h2>
            <p>Tous nos forfaits incluent l'hébergement 1 an, le SSL et le support technique</p>
        </div>
        <div class="pricing__grid">
            <div class="pricing-card fade-in">
                <div class="pricing-card__header">
                    <h3>Vitrine</h3>
                    <p>Idéal pour présenter votre activité</p>
                </div>
                <div class="pricing-card__price">
                    <span class="pricing-card__amount">3 000</span>
                    <span class="pricing-card__currency">MAD</span>
                </div>
                <ul class="pricing-card__features">
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Design responsive sur mesure</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Jusqu'à 5 pages</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Formulaire de contact</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Optimisation SEO de base</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Hébergement 1 an offert</li>
                </ul>
                <button class="btn btn--outline btn--full" onclick="openContactModal()">Choisir ce forfait</button>
            </div>
            <div class="pricing-card pricing-card--featured fade-in">
                <div class="pricing-card__badge">Populaire</div>
                <div class="pricing-card__header">
                    <h3>Business</h3>
                    <p>Pour les entreprises en croissance</p>
                </div>
                <div class="pricing-card__price">
                    <span class="pricing-card__amount">6 000</span>
                    <span class="pricing-card__currency">MAD</span>
                </div>
                <ul class="pricing-card__features">
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Tout du forfait Vitrine</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Jusqu'à 15 pages</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Panel administration</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Blog intégré</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Intégration WhatsApp</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Analytics & reporting</li>
                </ul>
                <button class="btn btn--primary btn--full" onclick="openContactModal()">Choisir ce forfait</button>
            </div>
            <div class="pricing-card fade-in">
                <div class="pricing-card__header">
                    <h3>E-Commerce</h3>
                    <p>Boutique en ligne complète</p>
                </div>
                <div class="pricing-card__price">
                    <span class="pricing-card__amount">12 000</span>
                    <span class="pricing-card__currency">MAD</span>
                </div>
                <ul class="pricing-card__features">
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Tout du forfait Business</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Catalogue produits illimité</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Paiement en ligne sécurisé</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Gestion stock & commandes</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Multi-langues (FR/AR)</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Formation admin complète</li>
                </ul>
                <button class="btn btn--outline btn--full" onclick="openContactModal()">Choisir ce forfait</button>
            </div>
        </div>
    </div>
</section>

<!-- ═══ CTA ═══ -->
<section class="cta">
    <div class="container">
        <div class="cta__box fade-in">
            <h2>Un projet web en tête ?</h2>
            <p>Discutons de votre projet et obtenez un devis personnalisé gratuit sous 24h.</p>
            <div class="cta__actions">
                <button class="btn btn--white btn--lg" onclick="openContactModal()">Demander un devis</button>
                <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>" class="btn btn--outline-white btn--lg" target="_blank">WhatsApp</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
