<script>
/**
 * Tata letak elemen kartu depan (drag & drop).
 *
 * Posisi disimpan sebagai [x, y, skala]: pergeseran dari posisi bawaan dalam piksel desain kartu
 * (landscape lebar 1004, portrait lebar 620), per kombinasi "orientasi-template",
 * mis. {"landscape-1": {"logo": [10, -5, 1.2]}} (skala opsional, bawaan 1). Di halaman cetak banyak (ukuran mm),
 * satuan desain dikonversi lewat variabel CSS --u pada kartu, jadi hasilnya sejajar.
 * Pergeseran & ukuran dipasang sebagai transform: translate() scale(), yang ikut tertangkap ke PDF/print.
 */
window.CardLayout = (function () {
    // px layar per 1 piksel desain kartu
    function unitPx(card) {
        const u = getComputedStyle(card).getPropertyValue('--u').trim();
        const mm = /^([\d.]+)mm$/.exec(u);
        return mm ? parseFloat(mm[1]) * 96 / 25.4 : 1;
    }

    function apply(card, positions, selectors) {
        const unit = unitPx(card);
        Object.keys(selectors).forEach(item => {
            const el = card.querySelector(selectors[item]);
            if (!el) return;
            const point = positions && positions[item];
            if (!point) {
                el.style.transform = '';
                return;
            }
            const scale = point[2] || 1;
            el.style.transform = `translate(${point[0] * unit}px, ${point[1] * unit}px)` + (scale !== 1 ? ` scale(${scale})` : '');
        });
    }

    /**
     * Aktifkan penyuntingan drag & drop pada satu kartu.
     * @param {object} cfg { card, selectors, layout, getKey(), saveUrl, onStatus(msg, type), onSelect(item, scale) }
     */
    function editor(cfg) {
        let layout = cfg.layout || {};
        let working = {};
        let editing = false;
        let selected = null;
        const card = cfg.card;
        const MIN_SCALE = 0.3;
        const MAX_SCALE = 3;

        function scaleOf(item) {
            return (working[item] && working[item][2]) || 1;
        }

        function select(item) {
            card.querySelectorAll('[data-drag-item].selected').forEach(el => el.classList.remove('selected'));
            selected = item;
            const el = item && card.querySelector(`[data-drag-item="${item}"]`);
            if (el) el.classList.add('selected');
            if (cfg.onSelect) cfg.onSelect(selected, selected ? scaleOf(selected) : 1);
        }

        function setScale(value) {
            if (!selected) return;
            const scale = Math.min(MAX_SCALE, Math.max(MIN_SCALE, Math.round(value * 100) / 100));
            const point = working[selected] || [0, 0];
            working[selected] = [point[0], point[1], scale];
            apply(card, working, cfg.selectors);
            if (cfg.onSelect) cfg.onSelect(selected, scale);
        }

        function load() {
            working = JSON.parse(JSON.stringify(layout[cfg.getKey()] || {}));
            apply(card, working, cfg.selectors);
        }

        function markItems() {
            Object.keys(cfg.selectors).forEach(item => {
                const el = card.querySelector(cfg.selectors[item]);
                if (!el) return;
                el.dataset.dragItem = item;
                if (el.tagName === 'IMG') el.draggable = false;
            });
        }

        let drag = null;
        card.addEventListener('pointerdown', event => {
            if (!editing) return;
            const el = event.target.closest('[data-drag-item]');
            if (!el || !card.contains(el)) return;
            event.preventDefault();
            const item = el.dataset.dragItem;
            select(item);
            // Preview bisa diperkecil (transform scale); hitung skala layar -> piksel desain
            const scale = (card.getBoundingClientRect().width / card.offsetWidth) * unitPx(card);
            drag = { el, item, startX: event.clientX, startY: event.clientY, origin: working[item] || [0, 0], scale };
            el.setPointerCapture(event.pointerId);
            el.classList.add('dragging');
        });
        card.addEventListener('pointermove', event => {
            if (!drag) return;
            const x = drag.origin[0] + (event.clientX - drag.startX) / drag.scale;
            const y = drag.origin[1] + (event.clientY - drag.startY) / drag.scale;
            working[drag.item] = [Math.round(x), Math.round(y), scaleOf(drag.item)];
            apply(card, working, cfg.selectors);
        });
        const stop = () => {
            if (!drag) return;
            drag.el.classList.remove('dragging');
            drag = null;
        };
        card.addEventListener('pointerup', stop);
        // Scroll mouse di atas elemen terpilih = perbesar/perkecil
        card.addEventListener('wheel', event => {
            if (!editing) return;
            const el = event.target.closest('[data-drag-item]');
            if (!el || el.dataset.dragItem !== selected) return;
            event.preventDefault();
            setScale(scaleOf(selected) + (event.deltaY < 0 ? 0.05 : -0.05));
        }, { passive: false });
        card.addEventListener('pointercancel', stop);

        async function send(positions) {
            const response = await fetch(cfg.saveUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ key: cfg.getKey(), positions })
            });
            const result = await response.json().catch(() => ({ success: false, message: 'Respons server tidak valid.' }));
            if (result.success) {
                layout = result.layout || {};
                load();
            }
            cfg.onStatus(result.message || 'Gagal menyimpan posisi.', result.success ? 'success' : 'error');
            return result.success;
        }

        markItems();
        load();

        return {
            refresh() { markItems(); load(); select(null); },
            setEditing(on) {
                editing = !!on;
                document.body.classList.toggle('layout-edit', editing);
                if (!editing) {
                    load(); // buang perubahan yang belum disimpan
                    select(null);
                }
            },
            setScale,
            stepScale(delta) { if (selected) setScale(scaleOf(selected) + delta); },
            save() { return send(working); },
            reset() { return send({}); },
        };
    }

    return { apply, editor };
})();
</script>
