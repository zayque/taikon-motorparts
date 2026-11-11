/**
 * Taikon Motorparts — storefront behaviour.
 * Everything here is progressive enhancement: every form and link works without JS.
 */
(() => {
    'use strict';

    const $ = (sel, root = document) => root.querySelector(sel);
    const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
    const money = (n) => 'RM ' + Number(n).toLocaleString('en-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    /* ---- Toasts ---------------------------------------------------------- */

    function dismiss(alert) {
        alert.classList.add('is-leaving');
        alert.addEventListener('animationend', () => alert.remove(), { once: true });
    }

    function toast(type, text) {
        let region = $('[data-flash]');
        if (!region) {
            region = document.createElement('div');
            region.className = 'flash-region';
            region.dataset.flash = '';
            document.body.appendChild(region);
        }
        const alert = document.createElement('div');
        alert.className = `alert alert--${type}`;
        alert.setAttribute('role', 'status');
        const p = document.createElement('p');
        p.textContent = text;
        const close = document.createElement('button');
        close.type = 'button';
        close.className = 'alert__close';
        close.setAttribute('aria-label', 'Dismiss');
        close.textContent = '×';
        close.addEventListener('click', () => dismiss(alert));
        alert.append(p, close);
        region.appendChild(alert);
        setTimeout(() => alert.isConnected && dismiss(alert), 4000);
    }

    function initFlash() {
        $$('[data-flash] .alert').forEach((alert) => {
            $('[data-dismiss]', alert)?.addEventListener('click', () => dismiss(alert));
            if (!alert.classList.contains('alert--error')) {
                setTimeout(() => alert.isConnected && dismiss(alert), 5000);
            }
        });
    }

    /* ---- Header: mobile nav + account dropdown ---------------------------- */

    function initNav() {
        const toggle = $('[data-nav-toggle]');
        if (!toggle) return;

        const setOpen = (open) => {
            document.body.classList.toggle('nav-open', open);
            toggle.setAttribute('aria-expanded', String(open));
        };
        toggle.addEventListener('click', () => setOpen(!document.body.classList.contains('nav-open')));
        document.addEventListener('keydown', (e) => e.key === 'Escape' && setOpen(false));
        window.matchMedia('(min-width: 961px)').addEventListener('change', (e) => e.matches && setOpen(false));
    }

    function initDropdowns() {
        $$('[data-dropdown]').forEach((dd) => {
            const btn = $('[data-dropdown-toggle]', dd);
            const close = () => { dd.classList.remove('is-open'); btn.setAttribute('aria-expanded', 'false'); };
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const open = !dd.classList.contains('is-open');
                dd.classList.toggle('is-open', open);
                btn.setAttribute('aria-expanded', String(open));
            });
            document.addEventListener('click', (e) => !dd.contains(e.target) && close());
            document.addEventListener('keydown', (e) => e.key === 'Escape' && close());
        });
    }

    /* ---- Quantity steppers ------------------------------------------------ */

    function initQty() {
        $$('[data-qty]').forEach((wrap) => {
            const input = $('input', wrap);
            $$('[data-step]', wrap).forEach((btn) => {
                btn.addEventListener('click', () => {
                    const min = Number(input.min || 0);
                    const max = Number(input.max || 99);
                    const next = Math.min(max, Math.max(min, Number(input.value || 0) + Number(btn.dataset.step)));
                    if (next !== Number(input.value)) {
                        input.value = next;
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            });
        });

        // Cart page: submit quantity changes automatically (debounced).
        const cartForm = $('#cart-form');
        if (cartForm) {
            let timer;
            $('[data-update-btn]', cartForm)?.setAttribute('hidden', '');
            cartForm.addEventListener('change', (e) => {
                if (!e.target.matches('input[type="number"]')) return;
                clearTimeout(timer);
                timer = setTimeout(() => cartForm.requestSubmit(), 500);
            });
        }
    }

    /* ---- Add to cart without a page reload --------------------------------- */

    function updateCartCount(count) {
        $$('[data-cart-count]').forEach((el) => {
            el.textContent = count;
            el.dataset.count = count;
            el.classList.remove('is-bumped');
            void el.offsetWidth; // restart animation
            el.classList.add('is-bumped');
        });
    }

    function initAddToCart() {
        $$('form[data-add-to-cart]').forEach((form) => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = $('button', form);
                btn.classList.add('is-loading');
                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: { Accept: 'application/json' },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) throw new Error();
                    const data = await res.json();
                    updateCartCount(data.count);
                    toast('success', `${data.name} added to your cart.`);
                } catch {
                    form.submit(); // fall back to a normal post
                } finally {
                    btn.classList.remove('is-loading');
                }
            });
        });
    }

    /* ---- Generic form helpers -------------------------------------------- */

    function initForms() {
        // Auto-submit filter forms when a control changes.
        $$('form[data-autosubmit]').forEach((form) => {
            form.addEventListener('change', () => form.requestSubmit());
        });

        // Confirmation prompts on destructive actions.
        $$('form[data-confirm]').forEach((form) => {
            form.addEventListener('submit', (e) => {
                if (!window.confirm(form.dataset.confirm)) e.preventDefault();
            });
        });

        // Prevent double submits on important forms.
        $$('[data-submit-once]').forEach((btn) => {
            btn.form?.addEventListener('submit', () => {
                setTimeout(() => btn.classList.add('is-loading'), 0);
            });
        });
    }

    /* ---- Checkout: delivery / pickup toggles and live total ------------------ */

    function initCheckout() {
        const form = $('form[data-checkout]');
        if (!form) return;

        const subtotal = Number(form.dataset.subtotal);
        const flat = Number(form.dataset.flat);
        const freeOver = Number(form.dataset.freeOver);
        const pickupOnly = $('[data-pickup-only]', form);

        const sync = () => {
            const mode = $('input[name="fulfilment"]:checked', form)?.value || 'delivery';
            $$('[data-show-when]', form).forEach((el) => { el.hidden = el.dataset.showWhen !== mode; });

            const shipping = mode === 'pickup' || subtotal >= freeOver ? 0 : flat;
            $('[data-shipping]', form).textContent = shipping ? money(shipping) : 'Free';
            $('[data-total]', form).textContent = money(subtotal + shipping);

            if (pickupOnly) {
                const radio = $('input', pickupOnly);
                radio.disabled = mode !== 'pickup';
                if (radio.disabled && radio.checked) {
                    radio.checked = false;
                    $('input[name="payment_method"][value="cod"]', form).checked = true;
                }
            }
        };

        form.addEventListener('change', (e) => e.target.name === 'fulfilment' && sync());
        sync();
    }

    /* ---- Booking: live slot availability ------------------------------------ */

    function initBooking() {
        const dateInput = $('input[data-slots-url]');
        const grid = $('[data-slots]');
        if (!dateInput || !grid) return;

        const render = (slots) => {
            grid.replaceChildren();
            if (!slots.length) {
                const empty = document.createElement('p');
                empty.className = 'slot-empty';
                empty.textContent = 'No slots available for this date. Please choose another day.';
                grid.appendChild(empty);
                return;
            }
            slots.forEach((slot) => {
                const label = document.createElement('label');
                label.className = 'slot';
                const input = document.createElement('input');
                input.type = 'radio';
                input.name = 'booking_time';
                input.value = slot.time;
                input.disabled = slot.left <= 0;
                const strong = document.createElement('strong');
                strong.textContent = slot.label;
                const span = document.createElement('span');
                span.textContent = slot.left > 0 ? `${slot.left} left` : 'Full';
                label.append(input, strong, span);
                grid.appendChild(label);
            });
        };

        dateInput.addEventListener('change', async () => {
            if (!dateInput.value) return;
            grid.setAttribute('aria-busy', 'true');
            try {
                const url = `${dateInput.dataset.slotsUrl}?date=${encodeURIComponent(dateInput.value)}`;
                const res = await fetch(url, { headers: { Accept: 'application/json' } });
                const data = await res.json();
                render(data.slots || []);
            } catch {
                toast('error', 'Could not load time slots. Please try again.');
            } finally {
                grid.removeAttribute('aria-busy');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initFlash();
        initNav();
        initDropdowns();
        initQty();
        initAddToCart();
        initForms();
        initCheckout();
        initBooking();
    });
})();
