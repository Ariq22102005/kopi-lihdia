// KopiLihdia: halaman Home (loader, reveal, parallax, progres scroll)
(function () {
    'use strict';

    const root = document.documentElement;
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const $ = (s, c) => (c || document).querySelector(s);
    const $$ = (s, c) => Array.from((c || document).querySelectorAll(s));

    /* ---------- Loader ---------- */
    const loader = $('#kl-loader');
    const MIN_SHOW = 1900;      // tampil minimal, biar animasinya sempat terlihat
    const MAX_WAIT = 4500;      // batas aman kalau ada aset yang lambat
    const started = performance.now();
    let finished = false;

    function finish() {
        if (finished) return;
        finished = true;

        try { sessionStorage.setItem('kl-seen', '1'); } catch (e) {}
        if (!loader) { root.classList.remove('kl-loading'); root.classList.add('kl-ready'); return; }

        loader.classList.add('is-done');
        // hero mulai masuk saat tirai sudah naik setengah jalan
        setTimeout(() => root.classList.add('kl-ready'), 450);
        setTimeout(() => {
            root.classList.remove('kl-loading');
            loader.remove();
        }, 1000);
    }

    if (root.classList.contains('kl-loading')) {
        const done = () => setTimeout(finish, Math.max(0, MIN_SHOW - (performance.now() - started)));
        if (document.readyState === 'complete') done();
        else window.addEventListener('load', done, { once: true });
        setTimeout(finish, MAX_WAIT);
    }

    /* ---------- Reveal saat scroll ---------- */
    const revealEls = $$('[data-reveal]');
    if ('IntersectionObserver' in window && !reduce) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach((e) => {
                if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
            });
        }, { threshold: 0.14, rootMargin: '0px 0px -6% 0px' });
        revealEls.forEach((el) => io.observe(el));
    } else {
        revealEls.forEach((el) => el.classList.add('is-in'));
    }

    /* ---------- Progres scroll + bayangan header ---------- */
    const bar = $('#kl-progress');
    const header = $('.kl-header');
    let ticking = false;

    function onScroll() {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
            const max = document.documentElement.scrollHeight - innerHeight;
            const y = scrollY;
            if (bar) bar.style.transform = 'scaleX(' + (max > 0 ? Math.min(1, y / max) : 0) + ')';
            if (header) header.classList.toggle('is-stuck', y > 8);

            // parallax foto hero
            if (!reduce) {
                $$('[data-parallax]').forEach((img) => {
                    const k = parseFloat(img.dataset.parallax) || 0.05;
                    img.style.transform = 'translateY(' + (-y * k) + 'px)';
                });
            }
            ticking = false;
        });
    }
    addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    /* ---------- Chip melayang mengikuti kursor (desktop) ---------- */
    const wrap = $('[data-parallax-wrap]');
    if (wrap && !reduce && matchMedia('(hover: hover)').matches) {
        const floats = $$('[data-depth]', wrap);
        wrap.addEventListener('pointermove', (e) => {
            const r = wrap.getBoundingClientRect();
            const x = (e.clientX - r.left) / r.width - 0.5;
            const y = (e.clientY - r.top) / r.height - 0.5;
            floats.forEach((el) => {
                const d = parseFloat(el.dataset.depth);
                el.style.transform = 'translate(' + (x * d * 2) + 'px,' + (y * d * 2) + 'px)';
            });
        });
        wrap.addEventListener('pointerleave', () => floats.forEach((el) => (el.style.transform = '')));
    }

    /* ---------- Tab menu pilihan ---------- */
    const tabs = $('#menu-tabs');
    const grid = $('#menu-grid');
    if (tabs && grid) {
        const items = $$('[data-cat]', grid);
        const LIMIT = 8;
        let current = 'all';

        function apply(filter, animate) {
            const match = items.filter((el) => filter === 'all' || el.dataset.cat === filter).slice(0, LIMIT);
            items.forEach((el) => {
                const show = match.includes(el);
                el.hidden = !show;
                el.classList.remove('is-entering');
                if (show && animate && !reduce) {
                    el.style.setProperty('--i', match.indexOf(el));
                    void el.offsetWidth;
                    el.classList.add('is-entering');
                }
            });
        }

        tabs.addEventListener('click', (e) => {
            const b = e.target.closest('[data-filter]');
            if (!b || b.dataset.filter === current) return;
            current = b.dataset.filter;
            $$('[data-filter]', tabs).forEach((t) => {
                const on = t === b;
                t.classList.toggle('active', on);
                t.setAttribute('aria-selected', on ? 'true' : 'false');
            });
            apply(current, true);
        });

        apply('all', false);
    }

    /* ---------- Lightbox galeri ---------- */
    const lb = $('#kl-lightbox');
    const shots = $$('[data-lightbox]');
    if (lb && shots.length && typeof lb.showModal === 'function') {
        const img = $('img', lb);
        const cap = $('figcaption', lb);
        let idx = 0;

        function show(i) {
            idx = (i + shots.length) % shots.length;
            const s = shots[idx];
            img.style.animation = 'none'; void img.offsetWidth; img.style.animation = '';
            img.src = s.dataset.lightbox;
            img.alt = s.dataset.caption || '';
            cap.textContent = s.dataset.caption || '';
        }
        shots.forEach((s, i) => s.addEventListener('click', () => { show(i); lb.showModal(); }));

        lb.addEventListener('click', (e) => {
            const act = e.target.closest('[data-lb]')?.dataset.lb;
            if (act === 'close' || e.target === lb) lb.close();
            else if (act === 'prev') show(idx - 1);
            else if (act === 'next') show(idx + 1);
        });
        lb.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') show(idx - 1);
            if (e.key === 'ArrowRight') show(idx + 1);
        });
    }

    /* ---------- Slider testimoni ---------- */
    const slider = $('#kl-slider');
    if (slider) {
        const track = $('.kl-slider-track', slider);
        const dotsBox = $('.kl-dots', slider);
        const prev = $('[data-slide=prev]', slider);
        const next = $('[data-slide=next]', slider);
        const cards = $$('.kl-testi', track);
        const AUTO = 5500;
        let pages = 1, page = 0, timer = null, fillRaf = null, paused = false, fillStart = 0;

        const perView = () => Math.max(1, Math.round(parseFloat(getComputedStyle(slider).getPropertyValue('--per')) || 1));
        const step = () => cards[0].getBoundingClientRect().width + parseFloat(getComputedStyle(track).columnGap || 0);

        function build() {
            pages = Math.max(1, Math.ceil(cards.length / perView()));
            dotsBox.innerHTML = '';
            for (let i = 0; i < pages; i++) {
                const d = document.createElement('button');
                d.type = 'button'; d.className = 'kl-dot-b'; d.setAttribute('role', 'tab');
                d.setAttribute('aria-label', 'Halaman ' + (i + 1));
                d.addEventListener('click', () => { goTo(i); restart(); });
                dotsBox.appendChild(d);
            }
            sync();
        }

        function currentPage() {
            const max = track.scrollWidth - track.clientWidth;
            if (max <= 1) return 0;
            if (track.scrollLeft >= max - 2) return pages - 1;
            return Math.min(pages - 1, Math.round(track.scrollLeft / (step() * perView())));
        }

        function sync() {
            page = currentPage();
            $$('.kl-dot-b', dotsBox).forEach((d, i) => {
                d.classList.toggle('active', i === page);
                d.setAttribute('aria-selected', i === page ? 'true' : 'false');
            });
            prev.disabled = page === 0;
            next.disabled = page >= pages - 1;
        }

        function goTo(i) {
            const target = Math.min(pages - 1, Math.max(0, i));
            track.scrollTo({ left: target * step() * perView(), behavior: reduce ? 'auto' : 'smooth' });
        }

        /* autoplay dengan isian di titik aktif */
        function fill(ts) {
            if (paused) return;
            if (!fillStart) fillStart = ts;
            const p = Math.min(1, (ts - fillStart) / AUTO);
            const dot = $('.kl-dot-b.active', dotsBox);
            if (dot) dot.style.setProperty('--fill', p);
            if (p >= 1) { goTo(page >= pages - 1 ? 0 : page + 1); fillStart = 0; }
            fillRaf = requestAnimationFrame(fill);
        }
        function restart() { fillStart = 0; $$('.kl-dot-b', dotsBox).forEach((d) => d.style.setProperty('--fill', 0)); }
        function play() {
            if (reduce || pages < 2) return;
            cancelAnimationFrame(fillRaf);
            fillRaf = requestAnimationFrame(fill);
        }
        function pause(v) { paused = v; if (!v) { fillStart = 0; play(); } else cancelAnimationFrame(fillRaf); }

        prev.addEventListener('click', () => { goTo(page - 1); restart(); });
        next.addEventListener('click', () => { goTo(page + 1); restart(); });
        track.addEventListener('scroll', () => { requestAnimationFrame(sync); }, { passive: true });
        track.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') { goTo(page - 1); restart(); }
            if (e.key === 'ArrowRight') { goTo(page + 1); restart(); }
        });

        // berhenti saat disentuh/di-hover/fokus, atau saat tidak terlihat
        ['mouseenter', 'focusin', 'touchstart'].forEach((ev) => slider.addEventListener(ev, () => pause(true), { passive: true }));
        ['mouseleave', 'focusout', 'touchend'].forEach((ev) => slider.addEventListener(ev, () => pause(false), { passive: true }));
        document.addEventListener('visibilitychange', () => pause(document.hidden));
        if ('IntersectionObserver' in window) {
            new IntersectionObserver((en) => pause(!en[0].isIntersecting)).observe(slider);
        }

        // geser dengan mouse (drag)
        let down = false, sx = 0, sl = 0, moved = false;
        track.addEventListener('pointerdown', (e) => {
            if (e.pointerType !== 'mouse') return;
            down = true; moved = false; sx = e.clientX; sl = track.scrollLeft;
        });
        addEventListener('pointermove', (e) => {
            if (!down) return;
            const dx = e.clientX - sx;
            if (Math.abs(dx) > 4) { moved = true; track.classList.add('is-drag'); }
            track.scrollLeft = sl - dx;
        });
        addEventListener('pointerup', () => {
            if (!down) return;
            down = false;
            if (moved) {
                track.classList.remove('is-drag');
                const per = step() * perView();
                goTo(Math.round(track.scrollLeft / per));
                restart();
            }
        });

        let rt; addEventListener('resize', () => { clearTimeout(rt); rt = setTimeout(() => { build(); goTo(Math.min(page, pages - 1)); }, 150); });
        build();
        play();
    }

    /* ---------- Reservasi ---------- */
    const rsv = $('#rsv-form');
    const rsvSection = $('#reservasi');

    // Setelah kirim (atau ada error), langsung bawa ke bagian reservasi
    if (rsvSection && rsvSection.hasAttribute('data-scroll-to')) {
        setTimeout(() => rsvSection.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' }), 700);
    }

    if (rsv) {
        const dateInput = $('#rsv-date', rsv);
        const slotInputs = $$('[data-slot]', rsv);
        const pad = (n) => String(n).padStart(2, '0');

        // Jam dinonaktifkan jika sudah lewat (hari ini) atau kursinya sudah penuh
        let left = {};

        function syncSlots() {
            const d = new Date();
            const today = d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
            const nowHM = pad(d.getHours()) + ':' + pad(d.getMinutes());
            const isToday = dateInput.value === today;

            slotInputs.forEach((i) => {
                const past = isToday && i.dataset.slot <= nowHM;
                const n = left[i.dataset.slot];
                const full = n !== undefined && n <= 0;

                i.disabled = past || full;
                i.closest('.kl-slot').classList.toggle('is-full', full);
                i.closest('.kl-slot').title = full ? 'Penuh' : (n !== undefined ? 'Tersisa ' + n + ' kursi' : '');
                if (i.disabled && i.checked) i.checked = false;
            });
        }

        async function loadAvailability() {
            left = {};
            const url = rsv.dataset.availUrl;
            if (dateInput.value && url) {
                try {
                    const res = await fetch(url + '?date=' + encodeURIComponent(dateInput.value), { headers: { 'Accept': 'application/json' } });
                    if (res.ok) left = (await res.json()).left || {};
                } catch (e) { /* abaikan, server tetap memeriksa saat dikirim */ }
            }
            syncSlots();
        }

        dateInput.addEventListener('change', loadAvailability);
        syncSlots();
        if (dateInput.value) loadAvailability();

        // Jumlah orang: jaga tetap dalam batas saat diketik
        const guests = $('#rsv-guests', rsv);
        guests.addEventListener('change', () => {
            const min = parseInt(guests.min, 10), max = parseInt(guests.max, 10);
            guests.value = Math.min(max, Math.max(min, parseInt(guests.value, 10) || min));
        });

        // Validasi ringan di browser; server tetap memeriksa ulang
        rsv.addEventListener('submit', (e) => {
            const missing = [];
            if (!$('#rsv-name', rsv).value.trim()) missing.push('#rsv-name');
            if (!$('#rsv-phone', rsv).value.trim()) missing.push('#rsv-phone');
            if (!dateInput.value) missing.push('#rsv-date');
            if (!slotInputs.some((i) => i.checked)) missing.push('.kl-slots');

            if (missing.length) {
                e.preventDefault();
                e.stopImmediatePropagation();
                const first = $(missing[0], rsv);
                first.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'center' });
                if (first.focus && first.tagName === 'INPUT') first.focus({ preventScroll: true });
                const box = missing[0] === '.kl-slots' ? first : first;
                box.classList.add('kl-shake');
                setTimeout(() => box.classList.remove('kl-shake'), 500);
            }
        }, true);
    }
})();
