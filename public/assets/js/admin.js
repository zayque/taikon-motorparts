/**
 * Admin-only behaviour: sidebar toggle, image preview, slug suggestions.
 */
(() => {
    'use strict';

    const $ = (sel, root = document) => root.querySelector(sel);
    const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

    function initSidebar() {
        const btn = $('[data-admin-menu]');
        if (!btn) return;
        const set = (open) => {
            document.body.classList.toggle('admin-nav-open', open);
            btn.setAttribute('aria-expanded', String(open));
        };
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            set(!document.body.classList.contains('admin-nav-open'));
        });
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#admin-sidebar')) set(false);
        });
        document.addEventListener('keydown', (e) => e.key === 'Escape' && set(false));
    }

    function initImagePreview() {
        $$('[data-image-drop]').forEach((drop) => {
            const input = $('input[type="file"]', drop);
            input.addEventListener('change', () => {
                const file = input.files?.[0];
                if (!file) return;
                let img = $('img', drop);
                if (!img) {
                    img = document.createElement('img');
                    img.alt = '';
                    drop.prepend(img);
                }
                img.src = URL.createObjectURL(file);
                const remove = $('input[name="remove_image"]');
                if (remove) remove.checked = false;
            });
        });
    }

    function initSlugs() {
        $$('[data-slug-from]').forEach((slug) => {
            const source = document.getElementById(slug.dataset.slugFrom);
            if (!source) return;
            let touched = slug.value !== '';
            slug.addEventListener('input', () => { touched = slug.value !== ''; });
            source.addEventListener('input', () => {
                if (touched) return;
                slug.placeholder = source.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initSidebar();
        initImagePreview();
        initSlugs();
    });
})();
