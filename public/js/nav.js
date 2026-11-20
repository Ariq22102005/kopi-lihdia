// KopiLihdia: navbar (garis penanda geser, header sembunyi saat scroll turun, menu akun)
(function () {
    'use strict';

    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const root = document.documentElement;
    const header = document.querySelector('[data-header]');

    /* ---------- Garis penanda yang geser ---------- */
    const nav = document.querySelector('[data-nav]');
    const ink = nav && nav.querySelector('.kl-ink');

    if (nav && ink) {
        const links = Array.from(nav.querySelectorAll('.kl-link'));
        const active = () => nav.querySelector('.kl-link.active');

        function moveTo(link) {
            if (!link) { ink.classList.remove('on'); return; }
            const pad = parseFloat(getComputedStyle(link).paddingLeft) || 0;
            const x = link.offsetLeft + pad;
            const w = link.offsetWidth - pad * 2;
            ink.style.width = w + 'px';
            ink.style.transform = 'translateX(' + x + 'px)';
            ink.classList.add('on');
        }

        function place() {
            // jangan beranimasi saat penempatan pertama
            const t = ink.style.transition;
            ink.style.transition = 'none';
            moveTo(active());
            void ink.offsetWidth;
            ink.style.transition = t;
        }

        if (nav.offsetParent !== null || getComputedStyle(nav).display !== 'none') place();
        root.classList.add('kl-ink-ready');

        links.forEach((l) => {
            l.addEventListener('pointerenter', () => moveTo(l));
            l.addEventListener('focus', () => moveTo(l));
        });
        nav.addEventListener('pointerleave', () => moveTo(active()));
        nav.addEventListener('focusout', () => moveTo(active()));

        let rt;
        addEventListener('resize', () => { clearTimeout(rt); rt = setTimeout(place, 120); });
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(place);
    }

    /* ---------- Header: kaca saat scroll, sembunyi saat turun, muncul saat naik ---------- */
    if (header) {
        let last = scrollY;
        let ticking = false;

        function update() {
            const y = scrollY;
            const dy = y - last;
            header.classList.toggle('is-stuck', y > 8);

            const busy = header.querySelector('.kl-account.is-open') || header.contains(document.activeElement) && document.activeElement.matches('input, textarea');
            if (!reduce && !busy) {
                if (y > 140 && dy > 6) header.classList.add('is-hidden');
                else if (dy < -4 || y < 80) header.classList.remove('is-hidden');
            }
            if (Math.abs(dy) > 4) last = y;
            ticking = false;
        }

        addEventListener('scroll', () => {
            if (!ticking) { ticking = true; requestAnimationFrame(update); }
        }, { passive: true });
        update();

        // saat link anchor (#reservasi dll) diklik, header tidak menutupi tujuan
        header.addEventListener('focusin', () => header.classList.remove('is-hidden'));
    }

    /* ---------- Menu akun ---------- */
    const acc = document.querySelector('[data-account]');
    if (acc) {
        const btn = acc.querySelector('[data-account-btn]');
        const items = () => Array.from(acc.querySelectorAll('.kl-menu a, .kl-menu button'));

        function set(open) {
            acc.classList.toggle('is-open', open);
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        btn.addEventListener('click', (e) => { e.stopPropagation(); set(!acc.classList.contains('is-open')); });
        document.addEventListener('click', (e) => { if (!acc.contains(e.target)) set(false); });
        document.addEventListener('keydown', (e) => {
            if (!acc.classList.contains('is-open')) return;
            if (e.key === 'Escape') { set(false); btn.focus(); }
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                const list = items();
                const i = list.indexOf(document.activeElement);
                const n = e.key === 'ArrowDown' ? (i + 1) % list.length : (i - 1 + list.length) % list.length;
                list[n].focus();
            }
        });
        // buka lewat keyboard: fokus pindah ke item pertama
        btn.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown') { e.preventDefault(); set(true); setTimeout(() => items()[0] && items()[0].focus(), 50); }
        });
    }
})();
