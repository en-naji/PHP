<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ARBORESCENCE DETAILLEE - PAGE INDEX / ACCUEIL
|--------------------------------------------------------------------------
| Role de ce fichier
| - Sert de vue principale du site public Info-Sup Digital.
| - Presente l'agence, les services, le processus, les preuves de confiance,
|   les temoignages et les appels a l'action vers le formulaire ou WhatsApp.
| - Ne traite pas de formulaire et ne modifie pas la base de donnees.
|
| 1. Initialisation PHP
|    - $currentPage = 'home' permet au header de marquer le lien actif.
|    - $pageTitle definit le titre HTML et participe au referencement.
|    - config.php charge les constantes globales comme WHATSAPP_NUMBER.
|    - functions.php expose les helpers site_url(), asset_url(), sanitize(), etc.
|    - header.php ouvre la structure HTML commune et inclut navigation/modale.
|
| 2. Section HERO
|    - Premiere zone visible de la page.
|    - Affiche le positionnement commercial, deux appels a l'action et des
|      statistiques animees via les attributs data-count/data-suffix.
|    - Les cartes flottantes representent les familles de services.
|
| 3. Section TECHNOLOGIES / MARQUES
|    - Le tableau $brands contient les marques et technologies affichees.
|    - Le tableau est duplique dans la boucle pour creer un effet marquee fluide.
|
| 4. Section SERVICES
|    - Oriente le visiteur vers les pages metier: creation web, materiel et IA.
|    - Les services sans page dediee restent en carte informative.
|    - Les liens utilisent site_url() pour rester compatibles avec la base URL.
|
| 5. Section PROCESSUS
|    - Decrit les etapes de collaboration: consultation, proposition,
|      realisation, livraison et support.
|    - Purement statique: aucun traitement serveur.
|
| 6. Section STATISTIQUES
|    - Chiffres de preuve sociale affiches en bandeau.
|    - L'animation de compteur est pilotee par JavaScript via data-count.
|
| 7. Section TEMOIGNAGES
|    - Cartes statiques destinees a rassurer les visiteurs.
|    - Les contenus sont directement dans la vue.
|
| 8. Section CTA FINALE
|    - Redonne deux chemins de conversion: formulaire modal et WhatsApp.
|    - openContactModal() est defini cote JavaScript dans assets/js/main.js.
|
| 9. Footer commun
|    - footer.php ferme le HTML, ajoute les scripts communs et les composants
|      partages de fin de page.
|--------------------------------------------------------------------------
*/

// ── ARBORESCENCE DE LA PAGE D'ACCUEIL ───────────────────────────────────
// ├─ 1. Initialisation du contenu
// │  ├─ définition de la page courante
// │  └─ définition du titre SEO de la page
// ├─ 2. Chargement des composants communs
// │  ├─ includes/config.php
// │  ├─ includes/functions.php
// │  └─ includes/header.php
// ├─ 3. Structure principale de la page
// │  ├─ hero / présentation de l'agence
// │  ├─ logos de confiance / partenaires
// │  ├─ section services
// │  ├─ section processus
// │  ├─ section statistiques
// │  ├─ section témoignages
// │  └─ appels à l'action / contact
// └─ 4. Pied de page commun
//    └─ includes/footer.php

// PAGE ACCUEIL - Role View
// Cette page presente l'agence, les services, le processus, les statistiques
// et les appels a l'action. Elle ne lit pas directement la base de donnees.
// Elle inclut config.php/functions.php puis les vues communes header/footer.
$currentPage = 'home';
$pageTitle = 'Info-Sup Digital — Agence Digitale à Casablanca';
// require_once charge les constantes, la session et la configuration globale.
require_once __DIR__ . '/includes/config.php';
// require_once charge les helpers utilises par la vue.
require_once __DIR__ . '/includes/functions.php';
// require_once insere l'en-tete commun, la navigation et les composants globaux.
require_once __DIR__ . '/includes/header.php';
?>

<!-- ═══ HERO ═══ -->
<section class="hero">
    <div class="hero__bg">
        <div class="hero__grid-pattern"></div>
        <div class="hero__glow hero__glow--1"></div>
        <div class="hero__glow hero__glow--2"></div>
    </div>
    <div class="container hero__inner">
        <div class="hero__content">
            <div class="hero__badge fade-in">
                <span class="pulse-dot"></span>
                Agence Digitale #1 à Casablanca
            </div>
            <h1 class="hero__title fade-in">
                Transformez votre vision en
                <span class="text-gradient">succès digital</span>
            </h1>
            <p class="hero__subtitle fade-in">
                Création de sites web, matériel informatique professionnel et solutions d'intelligence artificielle pour propulser votre entreprise.
            </p>
            <div class="hero__actions fade-in">
                <!-- openContactModal() ouvre le formulaire de contact sans quitter la page. -->
                <button class="btn btn--primary btn--lg" onclick="openContactModal()">
                    Démarrer un projet
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
                <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>" class="btn btn--outline btn--lg" target="_blank">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                    WhatsApp
                </a>
            </div>
            <div class="hero__stats fade-in">
                <div class="hero__stat">
                    <span class="hero__stat-number" data-count="150" data-suffix="+">0</span>
                    <span class="hero__stat-label">Projets livrés</span>
                </div>
                <div class="hero__stat">
                    <span class="hero__stat-number" data-count="98" data-suffix="%">0</span>
                    <span class="hero__stat-label">Clients satisfaits</span>
                </div>
                <div class="hero__stat">
                    <span class="hero__stat-number" data-count="5" data-suffix=" ans">0</span>
                    <span class="hero__stat-label">D'expérience</span>
                </div>
            </div>
        </div>
        <div class="hero__visual fade-in">
            <div class="hero__cards">
                <div class="hero__card hero__card--1">
                    <div class="hero__card-icon" style="background:#FF5C00">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    </div>
                    <span>Création Web</span>
                    <small>Sites & Apps</small>
                </div>
                <div class="hero__card hero__card--2">
                    <div class="hero__card-icon" style="background:#2563EB">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="2" x2="9" y2="4"/><line x1="15" y1="2" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="22"/><line x1="15" y1="20" x2="15" y2="22"/><line x1="20" y1="9" x2="22" y2="9"/><line x1="20" y1="15" x2="22" y2="15"/><line x1="2" y1="9" x2="4" y2="9"/><line x1="2" y1="15" x2="4" y2="15"/></svg>
                    </div>
                    <span>Matériel IT</span>
                    <small>Pro & Sécurité</small>
                </div>
                <div class="hero__card hero__card--3">
                    <div class="hero__card-icon" style="background:#7C3AED">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 2a4 4 0 0 1 4 4c0 1.95-1.4 3.58-3.25 3.93L12 22"/><path d="M12 2a4 4 0 0 0-4 4c0 1.95 1.4 3.58 3.25 3.93"/><line x1="4.5" y1="12.5" x2="19.5" y2="12.5"/><line x1="6" y1="16" x2="18" y2="16"/></svg>
                    </div>
                    <span>Agents IA</span>
                    <small>Automatisation</small>
                </div>
                <div class="hero__card hero__card--4">
                    <div class="hero__card-icon" style="background:#059669">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                    </div>
                    <span>Sécurité</span>
                    <small>Surveillance</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ TRUSTED BY ═══ -->
<section class="trusted">
    <div class="container">
        <p class="trusted__label">Technologies & marques partenaires</p>
        <div class="trusted__marquee">
            <div class="trusted__track">
                <?php
                $brands = ['Hikvision','Dahua','ZKTeco','TP-Link','Ubiquiti','Dell','Apple','Lenovo','Ajax','Ezviz','WordPress','Laravel','React','Figma','AWS'];
                // foreach parcourt deux fois la liste pour obtenir un defilement continu.
                foreach ([...$brands, ...$brands] as $b): ?>
                    <span class="trusted__item"><?= $b ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ═══ SERVICES ═══ -->
<section class="services" id="services">
    <div class="container">
        <div class="section-head fade-in">
            <span class="section-head__tag">Nos Services</span>
            <h2>Des solutions complètes pour votre entreprise</h2>
            <p>De la conception à la réalisation, nous couvrons tous vos besoins digitaux</p>
        </div>
        <div class="services__grid">
            <!-- site_url() genere une URL compatible avec la configuration du site. -->
            <a href="<?= site_url('/web') ?>" class="service-card fade-in">
                <div class="service-card__icon service-card__icon--orange">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/><line x1="14" y1="4" x2="10" y2="20" stroke-width="1.5"/></svg>
                </div>
                <h3>Développement Web</h3>
                <p>Sites vitrine, e-commerce et applications web sur mesure avec les dernières technologies.</p>
                <span class="service-card__link">Explorer <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
            </a>
            <!-- site_url() construit le lien vers la page catalogue materiel. -->
            <a href="<?= site_url('/materiel') ?>" class="service-card fade-in">
                <div class="service-card__icon service-card__icon--blue">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                </div>
                <h3>Matériel Informatique</h3>
                <p>Caméras, systèmes d'accès, réseaux, PC et équipements de sécurité professionnels.</p>
                <span class="service-card__link">Explorer <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
            </a>
            <!-- site_url() construit le lien vers la page des solutions IA. -->
            <a href="<?= site_url('/ia') ?>" class="service-card fade-in">
                <div class="service-card__icon service-card__icon--purple">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 2v4m0 12v4m-7.07-3.93l2.83-2.83m8.48-8.48l2.83-2.83M2 12h4m12 0h4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83"/></svg>
                </div>
                <h3>Agents IA</h3>
                <p>Chatbots intelligents, automatisation des processus et solutions IA personnalisées.</p>
                <span class="service-card__link">Explorer <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
            </a>
            <div class="service-card fade-in">
                <div class="service-card__icon service-card__icon--green">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Vidéosurveillance</h3>
                <p>Systèmes de surveillance IP, DVR/NVR, caméras haute définition et accès à distance.</p>
                <span class="service-card__link">En savoir plus <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
            </div>
            <div class="service-card fade-in">
                <div class="service-card__icon service-card__icon--red">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                </div>
                <h3>Réseaux & Infrastructure</h3>
                <p>Installation réseau, câblage structuré, switches, routeurs et solutions WiFi pro.</p>
                <span class="service-card__link">En savoir plus <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
            </div>
            <div class="service-card fade-in">
                <div class="service-card__icon service-card__icon--teal">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a4 4 0 0 0-8 0v2"/><circle cx="12" cy="15" r="1"/></svg>
                </div>
                <h3>Contrôle d'Accès</h3>
                <p>Pointeuses biométriques, lecteurs RFID, vidéophones et systèmes d'alarme.</p>
                <span class="service-card__link">En savoir plus <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
            </div>
        </div>
    </div>
</section>

<!-- ═══ PROCESS ═══ -->
<section class="process" id="process">
    <div class="container">
        <div class="section-head fade-in">
            <span class="section-head__tag">Notre Processus</span>
            <h2>Comment nous travaillons</h2>
            <p>Un processus simple et transparent pour des résultats exceptionnels</p>
        </div>
        <div class="process__timeline">
            <div class="process__step fade-in">
                <div class="process__number">01</div>
                <div class="process__content">
                    <h3>Consultation</h3>
                    <p>Analyse approfondie de vos besoins, objectifs et budget pour définir la meilleure stratégie.</p>
                </div>
            </div>
            <div class="process__step fade-in">
                <div class="process__number">02</div>
                <div class="process__content">
                    <h3>Proposition</h3>
                    <p>Devis détaillé avec planning, maquettes et spécifications techniques adaptées.</p>
                </div>
            </div>
            <div class="process__step fade-in">
                <div class="process__number">03</div>
                <div class="process__content">
                    <h3>Réalisation</h3>
                    <p>Développement agile avec points d'avancement réguliers et ajustements en temps réel.</p>
                </div>
            </div>
            <div class="process__step fade-in">
                <div class="process__number">04</div>
                <div class="process__content">
                    <h3>Livraison & Support</h3>
                    <p>Mise en ligne, formation utilisateur et support technique continu post-livraison.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ STATS BAR ═══ -->
<section class="stats-bar">
    <div class="container stats-bar__grid">
        <div class="stats-bar__item fade-in">
            <span class="stats-bar__number" data-count="150" data-suffix="+">0</span>
            <span class="stats-bar__label">Projets réalisés</span>
        </div>
        <div class="stats-bar__item fade-in">
            <span class="stats-bar__number" data-count="80" data-suffix="+">0</span>
            <span class="stats-bar__label">Clients actifs</span>
        </div>
        <div class="stats-bar__item fade-in">
            <span class="stats-bar__number" data-count="30" data-suffix="+">0</span>
            <span class="stats-bar__label">Marques partenaires</span>
        </div>
        <div class="stats-bar__item fade-in">
            <span class="stats-bar__number" data-count="24" data-suffix="h/7">0</span>
            <span class="stats-bar__label">Support technique</span>
        </div>
    </div>
</section>

<!-- ═══ TESTIMONIALS ═══ -->
<section class="testimonials" id="testimonials">
    <div class="container">
        <div class="section-head fade-in">
            <span class="section-head__tag">Témoignages</span>
            <h2>Ce que disent nos clients</h2>
            <p>La satisfaction de nos clients est notre meilleure publicité</p>
        </div>
        <div class="testimonials__grid">
            <div class="testimonial-card fade-in">
                <div class="testimonial-card__stars">★★★★★</div>
                <blockquote>"Info-Sup Digital a transformé notre présence en ligne. Le site est moderne, rapide et nos ventes ont augmenté de 40% en 3 mois."</blockquote>
                <div class="testimonial-card__author">
                    <div class="testimonial-card__avatar">KA</div>
                    <div>
                        <strong>Karim Alaoui</strong>
                        <span>Directeur, Maroc Import</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-card fade-in">
                <div class="testimonial-card__stars">★★★★★</div>
                <blockquote>"Installation professionnelle de notre système de vidéosurveillance. Équipe réactive et matériel de qualité supérieure."</blockquote>
                <div class="testimonial-card__author">
                    <div class="testimonial-card__avatar">SB</div>
                    <div>
                        <strong>Sara Bennani</strong>
                        <span>Gérante, Clinique Santé+</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-card fade-in">
                <div class="testimonial-card__stars">★★★★★</div>
                <blockquote>"L'agent IA de support client a réduit notre temps de réponse de 80%. Un investissement qui se rentabilise rapidement."</blockquote>
                <div class="testimonial-card__author">
                    <div class="testimonial-card__avatar">MH</div>
                    <div>
                        <strong>Mohammed Hajji</strong>
                        <span>CEO, TechVision Maroc</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ CTA ═══ -->
<section class="cta">
    <div class="container">
        <div class="cta__box fade-in">
            <h2>Prêt à digitaliser votre entreprise ?</h2>
            <p>Contactez-nous pour une consultation gratuite et un devis personnalisé sous 24h.</p>
            <div class="cta__actions">
                <!-- openContactModal() relance le formulaire de contact depuis le CTA final. -->
                <button class="btn btn--white btn--lg" onclick="openContactModal()">
                    Demander un devis gratuit
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
                <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>" class="btn btn--outline-white btn--lg" target="_blank">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                    Discuter sur WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

<?php
// require_once insere le pied de page commun et les scripts JavaScript.
require_once __DIR__ . '/includes/footer.php';
?>
