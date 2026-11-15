/* Interaksi toko: tambah ke keranjang tanpa reload, panel pesanan, dan notifikasi. */
(function () {
    'use strict';

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const loginUrl = document.body.dataset.loginUrl;
    const miniUrl = document.body.dataset.miniUrl;

    function toast(message, type) {
        const box = document.getElementById('kl-toasts');
        if (!box || !message) return;

        const el = document.createElement('div');
        el.className = 'kl-toast kl-toast-' + (type || 'success');
        el.textContent = message;
        box.appendChild(el);

        setTimeout(() => {
            el.classList.add('hide');
            setTimeout(() => el.remove(), 300);
        }, 2600);
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
                badge.textContent = n;
                badge.style.display = n > 0 ? '' : 'none';
            }
            if (add) add.classList.toggle('kl-add-on', n > 0);
        });
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
        if (r.ok) await refreshMini();
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
})();
