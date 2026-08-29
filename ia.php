<?php
declare(strict_types=1);

// PAGE AGENTS IA - Role View
// Cette page presente les solutions d'intelligence artificielle:
// agents, avantages, cas d'usage et bouton de demande de demo.
// Les cartes sont generees a partir de tableaux PHP pour eviter la repetition HTML.
$currentPage = 'ia';
$pageTitle = 'Agents IA — Info-Sup Digital';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ═══ PAGE HERO ═══ -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero__content fade-in">
            <span class="section-head__tag">Intelligence Artificielle</span>
            <h1>Des agents IA qui <span class="text-gradient">travaillent</span> pour vous</h1>
            <p>Automatisez vos processus, améliorez votre service client et boostez votre productivité grâce à l'IA.</p>
        </div>
    </div>
</section>

<!-- ═══ IA INTRO ═══ -->
<section class="section">
    <div class="container">
        <div class="ia-intro fade-in">
            <div class="ia-intro__visual">
                <div class="ia-intro__orb">
                    <div class="ia-intro__ring ia-intro__ring--1"></div>
                    <div class="ia-intro__ring ia-intro__ring--2"></div>
                    <div class="ia-intro__ring ia-intro__ring--3"></div>
                    <div class="ia-intro__core">IA</div>
                </div>
            </div>
            <div class="ia-intro__content">
                <h2>L'IA au service de votre entreprise</h2>
                <p>Nos agents intelligents utilisent les dernières avancées en intelligence artificielle pour automatiser vos tâches répétitives, améliorer l'expérience client et optimiser vos opérations.</p>
                <div class="ia-intro__stats">
                    <div class="ia-intro__stat">
                        <strong>-80%</strong>
                        <span>Temps de réponse</span>
                    </div>
                    <div class="ia-intro__stat">
                        <strong>24/7</strong>
                        <span>Disponibilité</span>
                    </div>
                    <div class="ia-intro__stat">
                        <strong>+60%</strong>
                        <span>Productivité</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ AGENTS GRID ═══ -->
<section class="section section--alt">
    <div class="container">
        <div class="section-head fade-in">
            <span class="section-head__tag">Nos Agents</span>
            <h2>Solutions IA sur mesure</h2>
            <p>Chaque agent est personnalisé pour votre secteur d'activité</p>
        </div>
        <div class="agents__grid">
            <?php
            // Tableau de creation des cartes agents.
            // Chaque agent contient: titre, description, couleur, tags et icone SVG.
            $agents = [
                ['Agent Support Client', 'Répondez instantanément aux questions de vos clients 24h/24 via votre site web ou WhatsApp.', '#FF5C00', ['NLP','Multilingue','WhatsApp'], '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>'],
                ['Agent Prospection',    'Qualifiez automatiquement vos leads et automatisez votre tunnel de vente.', '#2563EB', ['Lead scoring','CRM','Email'], '<circle cx="12" cy="12" r="3"/><path d="M12 2v4m0 12v4m-7.07-3.93l2.83-2.83m8.48-8.48l2.83-2.83M2 12h4m12 0h4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83"/>'],
                ['Agent Back-Office',    'Automatisez la saisie de données, la facturation et la gestion documentaire.', '#059669', ['OCR','Factures','Rapports'], '<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>'],
                ['Agent Contenu',        'Générez du contenu marketing optimisé SEO : articles, publications sociales, emails.', '#7C3AED', ['SEO','Rédaction','Social'], '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>'],
                ['Agent Médical',        'Assistant de prise de RDV, rappels patients et gestion de dossiers médicaux.', '#DC2626', ['RGPD','Agenda','SMS'], '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>'],
                ['Agent Immobilier',     'Matching automatique acquéreurs-biens, estimations et visites virtuelles.', '#0891B2', ['Matching','Estimation','360°'], '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>'],
            ];
            foreach ($agents as [$name, $desc, $color, $chips, $svgPath]): ?>
            <div class="agent-card fade-in" style="--agent-color:<?= $color ?>">
                <div class="agent-card__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="<?= $color ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?= $svgPath ?></svg>
                </div>
                <h3><?= $name ?></h3>
                <p><?= $desc ?></p>
                <div class="agent-card__chips">
                    <?php foreach ($chips as $chip): ?>
                        <span class="chip" style="background:<?= $color ?>15;color:<?= $color ?>"><?= $chip ?></span>
                    <?php endforeach; ?>
                </div>
                <button class="btn btn--outline btn--sm btn--full" onclick="openContactModal()">Demander une démo</button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══ ADVANTAGES ═══ -->
<section class="section">
    <div class="container">
        <div class="section-head fade-in">
            <span class="section-head__tag">Avantages</span>
            <h2>Pourquoi choisir nos agents IA ?</h2>
        </div>
        <div class="advantages__grid">
            <?php
            // Tableau de creation des avantages.
            // Chaque avantage contient: titre, description et icone SVG.
            $advantages = [
                ['Disponible 24/7', 'Vos agents ne dorment jamais. Service continu même la nuit et les week-ends.', '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'],
                ['Multilingue', 'Communication en français, arabe, anglais et darija marocain.', '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>'],
                ['Auto-apprentissage', 'L\'agent s\'améliore continuellement grâce au machine learning.', '<path d="M12 2a4 4 0 0 1 4 4c0 1.95-1.4 3.58-3.25 3.93L12 22"/><path d="M12 2a4 4 0 0 0-4 4c0 1.95 1.4 3.58 3.25 3.93"/>'],
                ['Intégration facile', 'Compatible avec vos outils existants : CRM, WhatsApp, email.', '<rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="2" x2="9" y2="4"/><line x1="15" y1="2" x2="15" y2="4"/>'],
                ['Données sécurisées', 'Hébergement au Maroc, conformité RGPD et chiffrement bout en bout.', '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>'],
                ['ROI mesurable', 'Tableaux de bord en temps réel pour suivre les performances et le ROI.', '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>'],
            ];
            foreach ($advantages as [$title, $desc, $path]): ?>
            <div class="advantage-card fade-in">
                <div class="advantage-card__icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?= $path ?></svg>
                </div>
                <h4><?= $title ?></h4>
                <p><?= $desc ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══ CTA ═══ -->
<section class="cta">
    <div class="container">
        <div class="cta__box fade-in">
            <h2>Prêt à automatiser votre business ?</h2>
            <p>Réservez une démo gratuite de 30 minutes et découvrez le potentiel de l'IA pour votre entreprise.</p>
            <div class="cta__actions">
                <button class="btn btn--white btn--lg" onclick="openContactModal()">Réserver une démo</button>
                <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>" class="btn btn--outline-white btn--lg" target="_blank">WhatsApp</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
