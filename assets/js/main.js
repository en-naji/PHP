/**
 * Info-Sup Digital — Main JavaScript v5.1
 * Compatible: Chrome, Edge, Firefox, Safari, iOS Safari
 */
;(function() {
    'use strict';

    // ── Navbar scroll effect ───────────────────────
    var navbar = document.getElementById('navbar');
    if (navbar) {
        var onScroll = function() {
            if (window.scrollY > 20) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // ── Burger menu ────────────────────────────────
    var burger = document.getElementById('navBurger');
    var menu   = document.getElementById('navMenu');
    if (burger && menu) {
        burger.addEventListener('click', function() {
            burger.classList.toggle('active');
            menu.classList.toggle('active');
        });
        var menuLinks = menu.querySelectorAll('a');
        for (var i = 0; i < menuLinks.length; i++) {
            menuLinks[i].addEventListener('click', function() {
                burger.classList.remove('active');
                menu.classList.remove('active');
            });
        }
    }

    // ── Intersection Observer fade-in ──────────────
    var fadeEls = document.querySelectorAll('.fade-in');
    if (fadeEls.length > 0 && 'IntersectionObserver' in window) {
        var observer = new IntersectionObserver(
            function(entries) {
                for (var i = 0; i < entries.length; i++) {
                    if (entries[i].isIntersecting) {
                        entries[i].target.classList.add('visible');
                        observer.unobserve(entries[i].target);
                    }
                }
            },
            { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }
        );
        for (var i = 0; i < fadeEls.length; i++) {
            observer.observe(fadeEls[i]);
        }
    }

    // ── Animated counters ──────────────────────────
    var counters = document.querySelectorAll('[data-count]');
    if (counters.length > 0 && 'IntersectionObserver' in window) {
        var animateCounter = function(el) {
            var target = parseInt(el.getAttribute('data-count'), 10);
            var suffix = el.getAttribute('data-suffix') || '';
            var duration = 2000;
            var start = performance.now();

            var step = function(now) {
                var elapsed = now - start;
                var progress = Math.min(elapsed / duration, 1);
                var ease = 1 - Math.pow(1 - progress, 3);
                var current = Math.round(target * ease);
                el.textContent = current + suffix;
                if (progress < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        };

        var counterObs = new IntersectionObserver(
            function(entries) {
                for (var i = 0; i < entries.length; i++) {
                    if (entries[i].isIntersecting) {
                        animateCounter(entries[i].target);
                        counterObs.unobserve(entries[i].target);
                    }
                }
            },
            { threshold: 0.3 }
        );
        for (var i = 0; i < counters.length; i++) {
            counterObs.observe(counters[i]);
        }
    }

    // ── Contact modal ──────────────────────────────
    var modal = document.getElementById('contactModal');

    // Fonction publique appelee depuis les boutons PHP/HTML.
    // Elle affiche le modal de devis et bloque le scroll de la page.
    window.openContactModal = function() {
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    };

    // Fonction publique appelee par le bouton fermer, Escape ou apres succes.
    window.closeContactModal = function() {
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    };

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeContactModal();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeContactModal();
        });
    }

    // ── AJAX form submission ───────────────────────
    var form = document.getElementById('contactForm');
    var formSuccess = document.getElementById('formSuccess');

    if (form) {
        form.addEventListener('submit', function(e) {
            // AJAX contact:
            // 1. empeche le rechargement de la page;
            // 2. envoie FormData vers contact.php;
            // 3. lit la reponse JSON;
            // 4. affiche le succes et prepare le message WhatsApp.
            e.preventDefault();
            var btn = form.querySelector('button[type="submit"]');
            var originalText = btn.innerHTML;
            btn.innerHTML = '<span>Envoi en cours...</span>';
            btn.disabled = true;

            var formData = new FormData(form);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', form.action, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState !== 4) return;

                btn.innerHTML = originalText;
                btn.disabled = false;

                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        var data = JSON.parse(xhr.responseText);
                        if (data.success) {
                            form.style.display = 'none';
                            if (formSuccess) formSuccess.style.display = 'block';

                            var waLink = document.querySelector('.whatsapp-float');
                            if (waLink) {
                                var waUrl = waLink.href;
                                var nameEl = form.querySelector('[name="first_name"]');
                                var msgEl = form.querySelector('[name="message"]');
                                var name = nameEl ? nameEl.value : '';
                                var msg = msgEl ? msgEl.value : '';
                                var text = encodeURIComponent('Bonjour, je suis ' + name + '. ' + msg);
                                setTimeout(function() {
                                    window.open(waUrl + '?text=' + text, '_blank');
                                }, 1500);
                            }

                            setTimeout(function() {
                                closeContactModal();
                                form.style.display = '';
                                if (formSuccess) formSuccess.style.display = 'none';
                                form.reset();
                            }, 3000);
                        } else {
                            alert(data.message || 'Erreur. Veuillez réessayer.');
                        }
                    } catch (parseErr) {
                        alert('Erreur de connexion. Veuillez réessayer.');
                    }
                } else {
                    alert('Erreur de connexion. Veuillez réessayer.');
                }
            };
            xhr.send(formData);
        });
    }

    // ── Smooth scroll for anchor links ─────────────
    var anchors = document.querySelectorAll('a[href^="#"]');
    for (var i = 0; i < anchors.length; i++) {
        anchors[i].addEventListener('click', function(e) {
            var href = this.getAttribute('href');
            var target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // ══════════════════════════════════════════════════
    // CATEGORY TABS — Filtrage sans rechargement
    // ══════════════════════════════════════════════════

    var catTabs = document.getElementById('catTabs');
    var productsGrid = document.getElementById('productsGrid');

    if (catTabs && productsGrid) {
        var allTabs = catTabs.querySelectorAll('.cat-tab');
        var allCards = productsGrid.querySelectorAll('.product-card');

        for (var t = 0; t < allTabs.length; t++) {
            allTabs[t].addEventListener('click', function(e) {
                // Filtrage catalogue cote client:
                // les cartes sont deja chargees par PHP, JS masque/affiche selon data-category.
                e.preventDefault();
                var selectedCat = this.getAttribute('data-cat');

                // Update active tab
                for (var j = 0; j < allTabs.length; j++) {
                    allTabs[j].classList.remove('cat-tab--active');
                }
                this.classList.add('cat-tab--active');

                // Filter products
                var visibleCount = 0;
                for (var k = 0; k < allCards.length; k++) {
                    var cardCat = allCards[k].getAttribute('data-category');
                    if (selectedCat === 'all' || cardCat === selectedCat) {
                        allCards[k].style.display = '';
                        visibleCount++;
                    } else {
                        allCards[k].style.display = 'none';
                    }
                }
            });
        }
    }

    // ══════════════════════════════════════════════════
    // SHOPPING CART SYSTEM
    // ══════════════════════════════════════════════════

    var CART_KEY = 'infosup_cart';
    var WA_NUMBER = '212614516545';

    // Lit le panier depuis localStorage.
    // Si le JSON est invalide ou absent, on repart avec un panier vide.
    function getCart() {
        try { return JSON.parse(localStorage.getItem(CART_KEY)) || []; }
        catch(e) { return []; }
    }

    // Sauvegarde le panier puis rafraichit tous les elements visuels.
    function saveCart(cart) {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
        updateCartUI();
    }

    function formatMAD(price) {
        return price.toLocaleString('fr-FR', { minimumFractionDigits: 0 }) + ' MAD';
    }

    // ── Add to cart — Using mousedown + touchstart for max compatibility
    // These fire BEFORE click and work on all browsers including Edge/Chrome on PHP 8.3
    // Controleur frontend du bouton "Ajouter au panier".
    // Il lit les attributs data-id, data-name et data-price generes par materiel.php.
    function handleAddToCart(e) {
        var btn = null;
        var el = e.target;

        // Walk up the DOM to find .btn--cart
        while (el && el !== document) {
            if (el.classList && el.classList.contains('btn--cart')) {
                btn = el;
                break;
            }
            el = el.parentElement || el.parentNode;
        }

        if (!btn) return;

        // Prevent duplicate from both mousedown and touchstart
        if (btn.getAttribute('data-adding') === '1') return;
        btn.setAttribute('data-adding', '1');
        setTimeout(function() { btn.removeAttribute('data-adding'); }, 300);

        // Prevent default to avoid any navigation
        e.preventDefault();

        var id = parseInt(btn.getAttribute('data-id'), 10);
        var name = btn.getAttribute('data-name');
        var price = parseFloat(btn.getAttribute('data-price'));

        if (!id || !name || isNaN(price) || price <= 0) return;

        // Add to cart
        var cart = getCart();
        var existing = null;
        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id === id) { existing = cart[i]; break; }
        }
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({ id: id, name: name, price: price, qty: 1 });
        }
        saveCart(cart);

        // Toast
        showCartToast();

        // Button feedback
        btn.classList.add('added');
        var origHTML = btn.innerHTML;
        btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Ajouté !';
        setTimeout(function() {
            btn.classList.remove('added');
            btn.innerHTML = origHTML;
        }, 1500);
    }

    // Bind on both mousedown and touchstart for full compatibility
    document.addEventListener('mousedown', handleAddToCart, false);
    document.addEventListener('touchstart', handleAddToCart, { passive: false });

    // Also bind click as fallback for accessibility (keyboard Enter)
    document.addEventListener('click', function(e) {
        var el = e.target;
        while (el && el !== document) {
            if (el.classList && el.classList.contains('btn--cart')) {
                e.preventDefault();
                return;
            }
            el = el.parentElement || el.parentNode;
        }
    }, false);

    // ── Remove from cart
    // Supprime completement un produit du panier local.
    window.removeFromCart = function(id) {
        var cart = getCart();
        var filtered = [];
        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id !== id) filtered.push(cart[i]);
        }
        saveCart(filtered);
    };

    // ── Update quantity
    // Augmente ou diminue la quantite d'un produit.
    // Si la quantite arrive a zero, le produit est retire.
    window.updateQty = function(id, delta) {
        var cart = getCart();
        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id === id) {
                cart[i].qty += delta;
                if (cart[i].qty <= 0) { cart.splice(i, 1); }
                break;
            }
        }
        saveCart(cart);
    };

    // ── Clear cart
    // Vide tout le panier du navigateur.
    window.clearCart = function() {
        localStorage.removeItem(CART_KEY);
        updateCartUI();
    };

    // ── Toggle cart panel
    // Ouvre/ferme le panneau lateral du panier.
    window.toggleCart = function() {
        var panel = document.getElementById('cartPanel');
        var overlay = document.getElementById('cartOverlay');
        if (!panel || !overlay) return;
        var isOpen = panel.getAttribute('data-open') === '1';
        if (isOpen) {
            panel.style.transform = 'translateX(100%)';
            overlay.style.opacity = '0';
            overlay.style.visibility = 'hidden';
            panel.setAttribute('data-open', '0');
            panel.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        } else {
            panel.style.transform = 'translateX(0)';
            overlay.style.opacity = '1';
            overlay.style.visibility = 'visible';
            panel.setAttribute('data-open', '1');
            panel.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    };

    // ── Toast notification
    function showCartToast() {
        var old = document.querySelector('.cart-toast');
        if (old) old.parentNode.removeChild(old);
        var toast = document.createElement('div');
        toast.className = 'cart-toast';
        toast.style.cssText = 'position:fixed;top:84px;right:24px;z-index:2000;background:#111;color:#fff;padding:12px 24px;border-radius:12px;font-size:14px;font-weight:600;display:flex;align-items:center;gap:10px;box-shadow:0 8px 24px rgba(0,0,0,.2);';
        toast.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Ajouté au panier !';
        document.body.appendChild(toast);
        setTimeout(function() {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity .4s ease';
            setTimeout(function() {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 400);
        }, 2500);
    }

    // ── Update all cart UI
    // Recalcule le nombre d'articles, le total et le HTML du panneau panier.
    // Cette fonction est appelee apres chaque modification du panier.
    function updateCartUI() {
        var cart = getCart();
        var totalItems = 0, totalPrice = 0;
        for (var i = 0; i < cart.length; i++) {
            totalItems += cart[i].qty;
            totalPrice += cart[i].price * cart[i].qty;
        }

        // Navbar badge — visible avec animation bounce
        var countEl = document.getElementById('cartCount');
        if (countEl) {
            var oldCount = parseInt(countEl.textContent, 10) || 0;
            countEl.textContent = totalItems;
            if (totalItems > 0) {
                countEl.style.display = 'inline-flex';
                // Bounce animation quand le nombre change
                if (totalItems !== oldCount) {
                    countEl.style.transform = 'scale(1.5)';
                    setTimeout(function() { countEl.style.transform = 'scale(1)'; }, 250);
                }
            } else {
                countEl.style.display = 'none';
            }
        }

        // Panel count
        var panelCount = document.getElementById('cartPanelCount');
        if (panelCount) panelCount.textContent = '(' + totalItems + ')';

        // Empty vs items
        var emptyEl = document.getElementById('cartEmpty');
        var itemsEl = document.getElementById('cartItems');
        var footerEl = document.getElementById('cartFooter');

        if (emptyEl) emptyEl.style.display = cart.length === 0 ? 'block' : 'none';
        if (footerEl) footerEl.style.display = cart.length > 0 ? 'block' : 'none';

        // Render items
        if (itemsEl) {
            var html = '';
            for (var i = 0; i < cart.length; i++) {
                var item = cart[i];
                html += '<div class="cart-item" style="display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid #f3f4f6">';
                html += '<div style="width:48px;height:48px;background:#FFF5EE;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#FF5C00" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></div>';
                html += '<div style="flex:1;min-width:0">';
                html += '<div style="font-size:14px;font-weight:600;color:#111;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + item.name + '</div>';
                html += '<div style="font-size:13px;color:#FF5C00;font-weight:700">' + formatMAD(item.price) + ' x ' + item.qty + '</div>';
                html += '</div>';
                html += '<div style="display:flex;align-items:center;gap:8px;flex-shrink:0">';
                html += '<button type="button" onclick="updateQty(' + item.id + ', -1)" style="width:28px;height:28px;border-radius:50%;border:1px solid #e5e7eb;background:#fff;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center">\u2212</button>';
                html += '<span style="font-size:15px;font-weight:700;min-width:20px;text-align:center">' + item.qty + '</span>';
                html += '<button type="button" onclick="updateQty(' + item.id + ', 1)" style="width:28px;height:28px;border-radius:50%;border:1px solid #e5e7eb;background:#fff;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center">+</button>';
                html += '</div>';
                html += '<button type="button" onclick="removeFromCart(' + item.id + ')" style="width:28px;height:28px;border-radius:50%;border:none;background:#FEF2F2;color:#DC2626;font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0">\u2715</button>';
                html += '</div>';
            }
            itemsEl.innerHTML = html;
        }

        // Total
        var totalEl = document.getElementById('cartTotal');
        if (totalEl) totalEl.textContent = formatMAD(totalPrice);
    }

    // ── Send order to WhatsApp
    // Transforme le panier local en message WhatsApp pre-rempli.
    // Il n'y a pas de table commandes: la commande part directement via wa.me.
    window.sendCartToWhatsApp = function() {
        var cart = getCart();
        if (cart.length === 0) return;
        var totalPrice = 0;
        for (var i = 0; i < cart.length; i++) { totalPrice += cart[i].price * cart[i].qty; }

        var message = '\uD83D\uDED2 *Commande Info-Sup Digital*\n\n';
        for (var i = 0; i < cart.length; i++) {
            message += (i + 1) + '. *' + cart[i].name + '*\n';
            message += '   Quantité: ' + cart[i].qty + ' | Prix: ' + formatMAD(cart[i].price * cart[i].qty) + '\n\n';
        }
        message += '\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\n';
        message += '\uD83D\uDCB0 *Total estimé: ' + formatMAD(totalPrice) + '*\n\n';
        message += 'Merci de confirmer la disponibilité et le délai de livraison.';

        window.open('https://wa.me/' + WA_NUMBER + '?text=' + encodeURIComponent(message), '_blank');

        setTimeout(function() {
            clearCart();
            toggleCart();
        }, 1000);
    };

    // ── Close cart on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var panel = document.getElementById('cartPanel');
            if (panel && panel.getAttribute('data-open') === '1') {
                toggleCart();
            }
        }
    });

    // ── Init cart
    updateCartUI();

})();
