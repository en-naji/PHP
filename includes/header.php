<?php
// Vue commune: ce header est inclus par toutes les pages publiques.
// $currentPage permet d'activer le bon lien dans le menu.
$currentPage ??= 'home';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Info-Sup Digital - Agence digitale a Casablanca. Creation web, materiel informatique, solutions IA.">
    <meta name="theme-color" content="#FF5C00">
    <title><?= $pageTitle ?? 'Info-Sup Digital - Agence Digitale a Casablanca' ?></title>
    <link rel="icon" href="<?= asset_url('images/favicon.svg') ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset_url('css/style.css') ?>">
</head>
<body>

<!--
    NAVBAR
    Role:
    - Afficher le logo Info-Sup et les liens principaux.
    - Ouvrir le panier via toggleCart() defini dans assets/js/main.js.
    - Ouvrir le modal de devis via openContactModal().
-->
<nav class="navbar" id="navbar">
    <div class="container navbar__inner">
        <a href="<?= site_url('/') ?>" class="navbar__logo">
            <img src="<?= asset_url('images/logo.svg') ?>" alt="Info-Sup Digital" height="38">
        </a>
        <!-- Menu public: la classe active depend de $currentPage. -->
        <ul class="navbar__menu" id="navMenu">
            <li><a href="<?= site_url('/') ?>" class="<?= $currentPage === 'home' ? 'active' : '' ?>">Accueil</a></li>
            <li><a href="<?= site_url('/web') ?>" class="<?= $currentPage === 'web' ? 'active' : '' ?>">Creation Web</a></li>
            <li><a href="<?= site_url('/materiel') ?>" class="<?= $currentPage === 'materiel' ? 'active' : '' ?>">Materiel</a></li>
            <li><a href="<?= site_url('/ia') ?>" class="<?= $currentPage === 'ia' ? 'active' : '' ?>">Agents IA</a></li>
        </ul>
        <div class="navbar__right">
            <!-- Bouton panier: le compteur est mis a jour cote client avec localStorage. -->
            <button class="cart-toggle" id="cartToggle" onclick="toggleCart()" aria-label="Panier" style="position:relative;background:none;border:none;padding:10px 12px;cursor:pointer;color:#4B5563;border-radius:12px;display:flex;align-items:center;gap:6px">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                <span id="cartCount" style="background:#FF5C00;color:#fff;font-size:12px;font-weight:800;min-width:22px;height:22px;border-radius:11px;display:none;align-items:center;justify-content:center;padding:0 6px;line-height:22px;transition:transform .3s ease">0</span>
            </button>
            <!-- Bouton global de devis: ouvre le formulaire commun place dans footer.php. -->
            <button class="btn btn--primary btn--sm btn--devis" onclick="openContactModal()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Devis gratuit
            </button>
            <button class="navbar__burger" id="navBurger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>
