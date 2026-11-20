/* Interaksi toko: tambah ke keranjang tanpa reload, panel pesanan, dan notifikasi. */
(function () {
    'use strict';

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const loginUrl = document.body.dataset.loginUrl;
    const miniUrl = document.body.dataset.miniUrl;

    function toast(message, type) {
        const box = document.getElementById('kl-toasts');
        if (!box || !message) return;

        const kind = type || 'success';
        const el = document.createElement('div');
        el.className = 'kl-toast kl-toast-' + kind;
        el.innerHTML = '<svg class="kl-i" width="18" height="18" aria-hidden="true"><use href="#i-' +
            (kind === 'error' ? 'alert' : 'check') + '"/></svg>';
        const text = document.createElement('span');
        text.textContent = message;
        el.appendChild(text);
        box.appendChild(el);

        setTimeout(() => {
            el.classList.add('hide');
            setTimeout(() => el.remove(), 300);
        }, 2800);
    }

    async function send(url, method, data) {
        const body = new FormData();
        Object.entries(data || {}).forEach(([k, v]) => body.append(k, v));
        if (method !== 'POST') body.append('_method', method);

        const res = await fetch(url, {
            method: 'POST',
            body: body,
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (res.status === 401) {          // belum login
            window.location.href = loginUrl;
            return null;
        }

        let json = {};
        try { json = await res.json(); } catch (e) { /* abaikan */ }

        return { ok: res.ok, json: json };
    }

    async function refreshMini() {
        if (!miniUrl) return;

        const res = await fetch(miniUrl, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return;
        const d = await res.json();

        const panel = document.getElementById('mini-cart');
        const prevTotal = panel?.querySelector('.kl-summary-total span:last-child')?.textContent || '';
        if (panel) panel.innerHTML = d.html;

        document.querySelectorAll('[data-cart-count]').forEach((el) => {
            el.textContent = d.count;
            el.style.display = d.count > 0 ? '' : 'none';
        });

        const bar = document.getElementById('mobile-cart-bar');
        if (bar) {
            bar.style.display = d.count > 0 ? '' : 'none';
            const total = bar.querySelector('[data-bar-total]');
            if (total) total.textContent = d.subtotal_label;
        }

        const qty = Array.isArray(d.quantities) ? {} : d.quantities;
        document.querySelectorAll('[data-product-card]').forEach((card) => {
            const n = qty[card.dataset.productCard] || 0;
            const badge = card.querySelector('[data-incart]');
            const add = card.querySelector('.kl-add');

            if (badge) {
                (badge.querySelector('b') || badge).textContent = n;
                badge.style.display = n > 0 ? '' : 'none';
            }
            if (add) add.classList.toggle('kl-add-on', n > 0);
        });

        document.dispatchEvent(new CustomEvent('kl:cart-updated', { detail: { count: d.count, prevTotal: prevTotal } }));
    }

    // Tombol + pada kartu produk dan halaman detail
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('form[data-ajax-add]');
        if (!form) return;

        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        if (btn) btn.disabled = true;

        const qty = form.querySelector('[name="quantity"]')?.value || 1;
        const r = await send(form.action, 'POST', { quantity: qty });

        if (btn) btn.disabled = false;
        if (!r) return;

        toast(r.json.message || (r.ok ? 'Ditambahkan ke keranjang.' : 'Gagal menambahkan.'), r.ok ? 'success' : 'error');
        if (r.ok) {
            if (btn && btn.classList.contains('kl-add')) {
                btn.classList.remove('pop');
                void btn.offsetWidth;          // restart animasi
                btn.classList.add('pop');
            }
            document.dispatchEvent(new CustomEvent('kl:added', { detail: { form: form, btn: btn } }));
            await refreshMini();
        }
    });

    // Tombol jumlah dan hapus di panel pesanan
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-cart-action]');
        if (!btn) return;

        btn.disabled = true;

        const r = btn.dataset.cartAction === 'remove'
            ? await send(btn.dataset.url, 'DELETE')
            : await send(btn.dataset.url, 'PATCH', { quantity: btn.dataset.qty });

        if (!r) return;
        if (!r.ok) toast(r.json.message || 'Gagal memperbarui keranjang.', 'error');

        await refreshMini();
    });

    // Stepper jumlah di halaman detail produk
    document.addEventListener('click', (e) => {
        const b = e.target.closest('[data-stepper] [data-step]');
        if (!b) return;

        const input = b.closest('[data-stepper]').querySelector('input');
        const min = parseInt(input.min || '1', 10);
        const max = parseInt(input.max || '999', 10);
        const next = (parseInt(input.value, 10) || min) + parseInt(b.dataset.step, 10);
        input.value = Math.min(max, Math.max(min, next));
    });

    // Tampilkan / sembunyikan password
    document.addEventListener('click', (e) => {
        const b = e.target.closest('[data-toggle-password]');
        if (!b) return;

        const input = document.getElementById(b.dataset.togglePassword);
        if (!input) return;

        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        b.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        const use = b.querySelector('use');
        if (use) use.setAttribute('href', show ? '#i-eye-off' : '#i-eye');
    });

    // Cegah kirim ganda: tombol submit berubah jadi "memproses" setelah formulir valid
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (form.hasAttribute('data-ajax-add') || e.defaultPrevented) return;

        const btn = form.querySelector('button[data-loading-text]');
        if (!btn) return;

        setTimeout(() => {
            btn.classList.add('is-loading');
            btn.setAttribute('aria-disabled', 'true');
            btn.dataset.origText = btn.textContent;
            btn.textContent = btn.dataset.loadingText;
        }, 0);
    });

    // Kembali dari cache browser (tombol Back): pulihkan tombol yang tadi "memproses"
    window.addEventListener('pageshow', (e) => {
        if (!e.persisted) return;
        document.querySelectorAll('button.is-loading').forEach((b) => {
            b.classList.remove('is-loading');
            b.removeAttribute('aria-disabled');
            if (b.dataset.origText) b.textContent = b.dataset.origText;
        });
    });
})();
