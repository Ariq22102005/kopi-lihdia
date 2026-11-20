// KopiLihdia: microinteraction santai (kartu produk, keranjang, favorit, sapaan)
(function () {
    'use strict';

    window.__klLoaded = true;

    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const canHover = matchMedia('(hover: hover)').matches;
    const $ = (s, c) => (c || document).querySelector(s);
    const $$ = (s, c) => Array.from((c || document).querySelectorAll(s));
    const parseJSON = (v, d) => { try { return JSON.parse(v); } catch (e) { return d; } };

    /* ---------- Muncul pelan saat scroll (untuk halaman selain Home) ---------- */
    const pending = $$('[data-reveal]:not(.is-in)');
    if (pending.length) {
        if ('IntersectionObserver' in window && !reduce) {
            const io = new IntersectionObserver((entries) => {
                entries.forEach((e) => {
                    if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -4% 0px' });
            pending.forEach((el) => io.observe(el));
        } else {
            pending.forEach((el) => el.classList.add('is-in'));
        }
    }

    /* ---------- Foto: kilau saat memuat ---------- */
    $$('.kl-product-img img:not(.kl-ph)').forEach((img) => {
        if (img.complete) return;
        const box = img.closest('.kl-product-img');
        box.classList.add('is-loading');
        img.classList.add('is-pending');
        const done = () => { box.classList.remove('is-loading'); img.classList.remove('is-pending'); };
        img.addEventListener('load', done, { once: true });
        img.addEventListener('error', done, { once: true });
    });

    /* ---------- Kartu miring mengikuti kursor ---------- */
    if (!reduce && canHover) {
        let active = null;
        const reset = (c) => { c.style.removeProperty('--rx'); c.style.removeProperty('--ry'); c.classList.remove('is-tilting'); };

        document.addEventListener('pointermove', (e) => {
            if (e.pointerType !== 'mouse') return;
            const c = e.target.closest ? e.target.closest('[data-tilt]') : null;
            if (c !== active) { if (active) reset(active); active = c; }
            if (!c) return;

            const r = c.getBoundingClientRect();
            const x = (e.clientX - r.left) / r.width - 0.5;
            const y = (e.clientY - r.top) / r.height - 0.5;
            c.classList.add('is-tilting');
            c.style.setProperty('--ry', (x * 7).toFixed(2) + 'deg');
            c.style.setProperty('--rx', (-y * 7).toFixed(2) + 'deg');
            c.style.setProperty('--mx', (x * 100 + 50).toFixed(0) + '%');
            c.style.setProperty('--my', (y * 100 + 50).toFixed(0) + '%');
        }, { passive: true });
        document.addEventListener('pointerleave', () => { if (active) { reset(active); active = null; } });
    }

    /* ---------- Percikan biji kopi + terbang ke keranjang ---------- */
    const COLORS = ['#3a9160', '#2b7347', '#7a4a2b', '#e0a526'];

    function burst(x, y) {
        if (reduce) return;
        const n = 9;
        for (let i = 0; i < n; i++) {
            const p = document.createElement('span');
            p.className = 'kl-bit';
            p.style.left = x + 'px';
            p.style.top = y + 'px';
            p.style.background = COLORS[i % COLORS.length];
            document.body.appendChild(p);

            const a = (Math.PI * 2 * i) / n + Math.random() * 0.6;
            const d = 30 + Math.random() * 30;
            const anim = p.animate([
                { transform: 'translate(-50%,-50%) scale(1)', opacity: 1 },
                { transform: 'translate(calc(-50% + ' + Math.cos(a) * d + 'px), calc(-50% + ' + Math.sin(a) * d + 'px)) scale(.15)', opacity: 0 }
            ], { duration: 520 + Math.random() * 220, easing: 'cubic-bezier(.2,.8,.3,1)' });
            anim.onfinish = () => p.remove();
        }
    }

    function visibleCartTarget() {
        return $$('[data-cart-target]').find((el) => el.getClientRects().length > 0);
    }

    function fly(fromEl, src) {
        const target = visibleCartTarget();
        if (reduce || !target || !fromEl) return;

        const a = fromEl.getBoundingClientRect();
        const b = target.getBoundingClientRect();
        const size = 54;
        const dot = document.createElement('div');
        dot.className = 'kl-fly';
        dot.style.width = dot.style.height = size + 'px';
        dot.style.left = (a.left + a.width / 2 - size / 2) + 'px';
        dot.style.top = (a.top + a.height / 2 - size / 2) + 'px';
        if (src) dot.style.backgroundImage = 'url("' + src + '")';
        document.body.appendChild(dot);

        const dx = (b.left + b.width / 2) - (a.left + a.width / 2);
        const dy = (b.top + b.height / 2) - (a.top + a.height / 2);
        const anim = dot.animate([
            { transform: 'translate(0,0) scale(1) rotate(0)', opacity: 1 },
            { transform: 'translate(' + dx * 0.45 + 'px,' + (dy * 0.45 - 90) + 'px) scale(.75) rotate(120deg)', opacity: 1, offset: 0.5 },
            { transform: 'translate(' + dx + 'px,' + dy + 'px) scale(.12) rotate(300deg)', opacity: 0.5 }
        ], { duration: 780, easing: 'cubic-bezier(.45,0,.25,1)' });
        anim.onfinish = () => dot.remove();
    }

    document.addEventListener('kl:added', (e) => {
        const { form, btn } = e.detail || {};
        if (!btn) return;

        const r = btn.getBoundingClientRect();
        burst(r.left + r.width / 2, r.top + r.height / 2);

        // sumber foto yang terbang: foto di kartu, atau foto besar di halaman detail
        const holder = btn.closest('[data-product-card]')?.querySelector('.kl-product-img') || $('.kl-detail-img');
        const img = holder?.querySelector('img');
        fly(holder, img ? (img.currentSrc || img.src) : '');

        // tombol berubah jadi centang sebentar
        if (btn.classList.contains('kl-add')) {
            const use = btn.querySelector('use');
            const was = use?.getAttribute('href');
            if (use) use.setAttribute('href', '#i-check');
            btn.classList.add('kl-add-ok');
            setTimeout(() => {
                btn.classList.remove('kl-add-ok');
                if (use && was) use.setAttribute('href', was);
            }, 1100);
        }
    });

    /* ---------- Angka berdenyut dan total berhitung naik ---------- */
    function countTo(el, from, to) {
        if (reduce || from === to) return;
        const t0 = performance.now();
        const dur = 480;
        (function step(t) {
            const p = Math.min(1, (t - t0) / dur);
            const v = Math.round(from + (to - from) * (1 - Math.pow(1 - p, 3)));
            el.textContent = 'Rp ' + v.toLocaleString('id-ID');
            if (p < 1) requestAnimationFrame(step);
        })(t0);
    }

    document.addEventListener('kl:cart-updated', (e) => {
        $$('[data-cart-count]').forEach((el) => {
            el.classList.remove('kl-bump');
            void el.offsetWidth;
            el.classList.add('kl-bump');
        });

        const totalEl = $('#mini-cart .kl-summary-total span:last-child');
        const prev = parseInt(String((e.detail || {}).prevTotal || '').replace(/\D/g, ''), 10) || 0;
        if (totalEl) {
            const now = parseInt(totalEl.textContent.replace(/\D/g, ''), 10) || 0;
            countTo(totalEl, prev, now);
        }
    });

    /* ---------- Favorit (disimpan di browser) ---------- */
    const KEY = 'kl-favs';
    const getFavs = () => { try { return new Set(JSON.parse(localStorage.getItem(KEY) || '[]')); } catch (e) { return new Set(); } };
    const saveFavs = (set) => { try { localStorage.setItem(KEY, JSON.stringify(Array.from(set))); } catch (e) { /* mode privat */ } };
    let favs = getFavs();

    const toastBox = () => document.getElementById('kl-toasts');
    function say(msg) {
        const box = toastBox();
        if (!box) return;
        const el = document.createElement('div');
        el.className = 'kl-toast';
        el.innerHTML = '<svg class="kl-i" width="18" height="18" aria-hidden="true"><use href="#i-heart"/></svg>';
        const t = document.createElement('span'); t.textContent = msg; el.appendChild(t);
        box.appendChild(el);
        setTimeout(() => { el.classList.add('hide'); setTimeout(() => el.remove(), 300); }, 1800);
    }

    function paintFavs() {
        $$('[data-fav]').forEach((b) => {
            const on = favs.has(b.dataset.fav);
            b.classList.toggle('is-on', on);
            b.setAttribute('aria-pressed', on ? 'true' : 'false');
        });
        const n = $$('[data-fav]').filter((b) => favs.has(b.dataset.fav)).length;
        $$('[data-fav-count]').forEach((el) => (el.textContent = n));
        applyFavFilter();
    }

    let favOnly = false;
    function applyFavFilter() {
        const grid = $('#product-grid');
        const empty = $('#fav-empty');
        if (!grid) return;

        let shown = 0;
        $$('[data-product-card]', grid).forEach((card) => {
            const col = card.parentElement;
            const keep = !favOnly || favs.has(card.dataset.productCard);
            col.hidden = !keep;
            if (keep) shown++;
        });
        if (empty) empty.hidden = !(favOnly && shown === 0);
    }

    document.addEventListener('click', (e) => {
        const heart = e.target.closest('[data-fav]');
        if (heart) {
            const id = heart.dataset.fav;
            const on = !favs.has(id);
            on ? favs.add(id) : favs.delete(id);
            saveFavs(favs);
            heart.classList.remove('pop');
            void heart.offsetWidth;
            heart.classList.add('pop');
            if (on) { const r = heart.getBoundingClientRect(); burst(r.left + r.width / 2, r.top + r.height / 2); }
            say(on ? 'Masuk favorit' : 'Dihapus dari favorit');
            paintFavs();
            return;
        }

        const f = e.target.closest('[data-fav-filter]');
        if (f) {
            favOnly = !favOnly;
            f.classList.toggle('active', favOnly);
            f.setAttribute('aria-pressed', favOnly ? 'true' : 'false');
            applyFavFilter();
        }
    });
    paintFavs();

    /* ---------- Sapaan sesuai jam ---------- */
    const greet = $('[data-greet]');
    if (greet) {
        const h = new Date().getHours();
        const part = h < 4 ? 'malam' : h < 11 ? 'pagi' : h < 15 ? 'siang' : h < 18 ? 'sore' : 'malam';
        const name = greet.dataset.name;
        const span = $('span', greet);
        if (span) span.textContent = 'Selamat ' + part + (name ? ', ' + name : '');
    }

    /* ---------- Kata di judul berganti ---------- */
    const swap = $('[data-swap]');
    if (swap && !reduce) {
        const words = parseJSON(swap.dataset.words, []);
        if (words.length > 1) {
            let i = 0;
            setInterval(() => {
                if (document.hidden) return;
                swap.classList.add('is-out');
                setTimeout(() => {
                    i = (i + 1) % words.length;
                    swap.textContent = words[i];
                    swap.classList.remove('is-out');
                    swap.classList.add('is-in');
                    setTimeout(() => swap.classList.remove('is-in'), 420);
                }, 280);
            }, 2800);
        }
    }

    /* ---------- Placeholder pencarian "mengetik" ---------- */
    const typing = $('input[data-typing]');
    if (typing && !reduce) {
        const list = parseJSON(typing.dataset.typing, []).map((s) => String(s).toLowerCase());
        const base = typing.getAttribute('placeholder') || '';
        if (list.length) {
            let si = 0, ci = 0, del = false;
            (function tick() {
                if (document.activeElement === typing || typing.value) {
                    typing.placeholder = base;
                    return setTimeout(tick, 900);
                }
                const word = list[si];
                ci += del ? -1 : 1;
                typing.placeholder = 'Cari ' + word.slice(0, ci) + (ci % 2 ? '' : '');

                let wait = del ? 35 : 75;
                if (!del && ci === word.length) { del = true; wait = 1500; }
                else if (del && ci === 0) { del = false; si = (si + 1) % list.length; wait = 350; }
                setTimeout(tick, wait);
            })();
        }
    }
})();
