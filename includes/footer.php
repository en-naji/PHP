<!--
    FOOTER COMMUN
    Role:
    - Afficher les informations de l'entreprise.
    - Fournir le panneau panier commun a toutes les pages.
    - Fournir le modal de contact commun a toutes les pages.
    - Charger assets/js/main.js qui active les interactions.
-->
<footer class="footer">
    <div class="container">
        <div class="footer__grid">
            <div class="footer__brand">
                <img src="<?= asset_url('images/logo-white.svg') ?>" alt="Info-Sup Digital" height="36">
                <p>Agence digitale Ã  Casablanca. Nous transformons vos idÃ©es en solutions numÃ©riques performantes.</p>
                <div class="footer__socials">
                    <a href="#" aria-label="Facebook"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
                    <a href="#" aria-label="Instagram"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/></svg></a>
                    <a href="#" aria-label="LinkedIn"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2zM4 2a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/></svg></a>
                </div>
            </div>
            <div class="footer__col">
                <h4>Services</h4>
                <ul>
                    <li><a href="<?= site_url('/web') ?>">CrÃ©ation de Sites Web</a></li>
                    <li><a href="<?= site_url('/materiel') ?>">MatÃ©riel Informatique</a></li>
                    <li><a href="<?= site_url('/ia') ?>">Agents IA</a></li>
                </ul>
            </div>
            <div class="footer__col">
                <h4>Entreprise</h4>
                <ul>
                    <li><a href="<?= site_url('/#services') ?>">Nos Services</a></li>
                    <li><a href="<?= site_url('/#process') ?>">Notre Processus</a></li>
                    <li><a href="<?= site_url('/#testimonials') ?>">TÃ©moignages</a></li>
                </ul>
            </div>
            <div class="footer__col">
                <h4>Contact</h4>
                <ul>
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <?= COMPANY_PHONE ?>
                    </li>
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <?= CONTACT_EMAIL ?>
                    </li>
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <?= COMPANY_CITY ?>
                    </li>
                </ul>
            </div>
        </div>
        <div class="footer__bottom">
            <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. Tous droits rÃ©servÃ©s.</p>
        </div>
    </div>
</footer>

<!--
    CART PANEL
    Le panier n'est pas sauvegarde en base de donnees.
    Il est stocke dans le navigateur avec localStorage par assets/js/main.js.
    Le bouton final genere un message WhatsApp avec la liste des produits.
-->
<div class="cart-overlay" id="cartOverlay" onclick="toggleCart()" style="position:fixed;inset:0;z-index:1500;background:rgba(0,0,0,.4);opacity:0;visibility:hidden"></div>
<aside class="cart-panel" id="cartPanel" style="position:fixed;top:0;right:0;z-index:1501;width:420px;max-width:100vw;height:100vh;background:#fff;transform:translateX(100%);transition:transform .35s ease">
    <div class="cart-panel__header">
        <h3>
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            Mon Panier <span id="cartPanelCount">(0)</span>
        </h3>
        <button class="cart-panel__close" onclick="toggleCart()" aria-label="Fermer">&times;</button>
    </div>
    <div class="cart-panel__body" id="cartBody">
        <div class="cart-panel__empty" id="cartEmpty">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            <p>Votre panier est vide</p>
            <span>Ajoutez des produits depuis le catalogue</span>
        </div>
        <div class="cart-panel__items" id="cartItems"></div>
    </div>
    <div class="cart-panel__footer" id="cartFooter" style="display:none">
        <div class="cart-panel__total">
            <span>Total estimÃ©</span>
            <strong id="cartTotal">0 MAD</strong>
        </div>
        <button class="btn btn--primary btn--full btn--lg" onclick="sendCartToWhatsApp()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
            Commander via WhatsApp
        </button>
        <button class="btn btn--outline btn--full btn--sm" onclick="clearCart()" style="margin-top:8px">
            Vider le panier
        </button>
    </div>
</aside>

<!--
    CONTACT MODAL
    Ce formulaire est la vue HTML du module contact.
    Le champ CSRF vient de functions.php.
    L'envoi est intercepte par main.js puis poste en AJAX vers contact.php.
-->
<div class="modal-overlay" id="contactModal">
    <div class="modal">
        <button class="modal__close" onclick="closeContactModal()" aria-label="Fermer">&times;</button>
        <div class="modal__header">
            <h3>Demander un devis gratuit</h3>
            <p>RÃ©ponse sous 24h par WhatsApp ou email</p>
        </div>
        <form id="contactForm" class="modal__form" method="POST" action="<?= site_url('/contact.php') ?>">
            <?= csrf_field() ?>
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">PrÃ©nom</label>
                    <input type="text" id="first_name" name="first_name" required placeholder="Votre prÃ©nom">
                </div>
                <div class="form-group">
                    <label for="last_name">Nom</label>
                    <input type="text" id="last_name" name="last_name" required placeholder="Votre nom">
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="votre@email.com">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="phone">TÃ©lÃ©phone</label>
                    <input type="tel" id="phone" name="phone" placeholder="+212 6XX XXX XXX">
                </div>
                <div class="form-group">
                    <label for="whatsapp">WhatsApp</label>
                    <input type="tel" id="whatsapp" name="whatsapp" placeholder="+212 6XX XXX XXX">
                </div>
            </div>
            <div class="form-group">
                <label for="message">Votre projet</label>
                <textarea id="message" name="message" rows="4" required placeholder="DÃ©crivez votre projet..."></textarea>
            </div>
            <button type="submit" class="btn btn--primary btn--full">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                Envoyer ma demande
            </button>
        </form>
        <div id="formSuccess" class="modal__success" style="display:none">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
            <h4>Message envoyÃ© !</h4>
            <p>Redirection vers WhatsApp...</p>
        </div>
    </div>
</div>

<!--
    WHATSAPP FLOAT
    Lien rapide vers WhatsApp, visible sur toutes les pages publiques.
-->
<a href="https://wa.me/<?= WHATSAPP_NUMBER ?>" target="_blank" class="whatsapp-float" aria-label="WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
</a>

<!-- Script principal: animations, modal, AJAX contact, filtre catalogue et panier. -->
<script src="<?= asset_url('js/main.js') ?>"></script>
</body>
</html>

