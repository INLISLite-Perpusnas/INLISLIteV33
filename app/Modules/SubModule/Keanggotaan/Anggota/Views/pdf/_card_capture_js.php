<script>
/**
 * Penangkap kartu anggota bersama untuk PDF, kirim email, dan print.
 *
 * Tujuannya agar hasil PDF/print sama dengan preview di layar. html2canvas tidak mendukung
 * dua fitur CSS yang dipakai kartu, sehingga keduanya ditiru hanya di salinan (clone) kartu:
 *  - backdrop-filter: blur(...) di panel  -> latar panel diganti potongan latar kartu yang
 *    sudah di-blur di canvas, ditimpa warna panel (hasil visual sama).
 *  - object-fit: cover/contain di foto/logo -> <img> diganti <div> dengan background-size
 *    yang sama (html2canvas memelarkan <img> ber-object-fit).
 * Font dan gambar juga ditunggu sampai siap supaya teks tidak tertangkap dengan font cadangan.
 */
window.CardCapture = (function () {
    let sequence = 0;

    function blurRadius(value) {
        const match = /blur\(\s*([\d.]+)px\s*\)/.exec(value || '');
        return match ? parseFloat(match[1]) : 0;
    }

    // Bekukan animasi/transisi di salinan kartu pada keadaan awalnya (keyframe 0%),
    // supaya hasil tangkapan tidak bergantung pada detik saat tombol diklik
    function freezeAnimations(doc) {
        const style = doc.createElement('style');
        style.textContent = '*,*::before,*::after{animation-play-state:paused!important;'
            + 'animation-delay:0s!important;transition:none!important}';
        doc.head.appendChild(style);
        if (doc.body) doc.body.classList.remove('layout-edit');
    }

    // html2canvas mengabaikan z-index pada elemen tanpa position (mis. flex item .right-panel),
    // sehingga overlay ::before kartu (z-index 1) tergambar MENIMPA isi kartu dan semuanya
    // tampak pucat. Di browser, flex item ber-z-index tetap di atas. Beri position: relative
    // pada salinan saja, kecuali ada turunan absolute yang posisinya bisa ikut bergeser.
    function fixStaticZIndex(root, win) {
        [root, ...root.querySelectorAll('*')].forEach(el => {
            const cs = win.getComputedStyle(el);
            if (cs.position !== 'static' || cs.zIndex === 'auto') return;
            const hasAbsoluteChild = Array.from(el.querySelectorAll('*')).some(child => {
                const pos = win.getComputedStyle(child).position;
                return pos === 'absolute' || pos === 'fixed';
            });
            if (!hasAbsoluteChild) el.style.position = 'relative';
        });
    }

    // Salinan kartu ada di iframe baru; font web (Inter) di sana baru diunduh saat dipakai.
    // Tunggu semua font yang dipakai kartu selesai dimuat agar teks tidak jatuh ke font cadangan.
    async function loadCloneFonts(doc, root) {
        if (!doc.fonts) return;
        const win = doc.defaultView;
        const specs = new Set();
        [root, ...root.querySelectorAll('*')].forEach(el => {
            const cs = win.getComputedStyle(el);
            specs.add(`${cs.fontStyle} ${cs.fontWeight} 16px ${cs.fontFamily}`);
        });
        const loading = Promise.all(Array.from(specs).map(spec => doc.fonts.load(spec).catch(() => null)))
            .then(() => doc.fonts.ready);
        await Promise.race([loading, new Promise(resolve => setTimeout(resolve, 5000))]);
    }

    function backdropOf(style) {
        return style.backdropFilter || style.webkitBackdropFilter || '';
    }

    async function waitForAssets(root) {
        if (document.fonts && document.fonts.ready) {
            try { await document.fonts.ready; } catch (e) { /* abaikan */ }
        }
        await Promise.all(Array.from(root.querySelectorAll('img')).map(img => img.complete
            ? Promise.resolve()
            : new Promise(resolve => {
                img.addEventListener('load', resolve, { once: true });
                img.addEventListener('error', resolve, { once: true });
            })));
    }

    // Blur canvas; tepi diperpanjang dulu agar pinggiran tidak memudar transparan
    function blurCanvas(source, radius) {
        const out = document.createElement('canvas');
        out.width = source.width;
        out.height = source.height;
        const ctx = out.getContext('2d');
        if (!('filter' in ctx) || radius <= 0) {
            ctx.drawImage(source, 0, 0);
            return out;
        }
        const pad = Math.ceil(radius * 3);
        const extended = document.createElement('canvas');
        extended.width = source.width + pad * 2;
        extended.height = source.height + pad * 2;
        const ext = extended.getContext('2d');
        ext.drawImage(source, 0, 0, extended.width, extended.height);
        ext.drawImage(source, pad, pad);
        ctx.filter = `blur(${radius}px)`;
        ctx.drawImage(extended, -pad, -pad);
        ctx.filter = 'none';
        return out;
    }

    const COPIED_STYLES = [
        'border-top', 'border-right', 'border-bottom', 'border-left', 'border-radius', 'box-shadow',
        'margin-top', 'margin-right', 'margin-bottom', 'margin-left', 'opacity', 'vertical-align',
    ];

    function replaceObjectFitImages(root, win) {
        root.querySelectorAll('img').forEach(img => {
            const cs = win.getComputedStyle(img);
            if ((cs.objectFit !== 'cover' && cs.objectFit !== 'contain') || cs.display === 'none') {
                return;
            }
            const div = img.ownerDocument.createElement('div');
            div.className = img.className;
            if (img.id) div.id = img.id;
            COPIED_STYLES.forEach(name => div.style.setProperty(name, cs.getPropertyValue(name)));
            div.style.display = cs.display === 'inline' ? 'inline-block' : cs.display;
            div.style.boxSizing = cs.boxSizing;
            div.style.width = cs.width;
            div.style.height = cs.height;
            div.style.flexShrink = '0';
            div.style.backgroundImage = `url("${img.currentSrc || img.src}")`;
            div.style.backgroundSize = cs.objectFit;
            div.style.backgroundPosition = cs.objectPosition;
            div.style.backgroundRepeat = 'no-repeat';
            img.replaceWith(div);
        });
    }

    /**
     * Tangkap elemen kartu menjadi canvas.
     * @param {HTMLElement} element
     * @param {object} options opsi html2canvas (scale, width, height, ...)
     */
    async function capture(element, options = {}) {
        if (typeof html2canvas === 'undefined') {
            throw new Error('Library html2canvas gagal dimuat. Periksa koneksi internet lalu muat ulang halaman.');
        }
        await waitForAssets(element);

        const id = 'cc' + (++sequence);
        const base = Object.assign({ scale: 2, useCORS: true, allowTaint: true, backgroundColor: null, logging: false }, options);
        const blurred = [element, ...element.querySelectorAll('*')]
            .filter(el => blurRadius(backdropOf(getComputedStyle(el))) > 0);

        element.setAttribute('data-cc-root', id);
        blurred.forEach((el, index) => el.setAttribute('data-cc-blur', String(index)));

        try {
            const crops = [];
            if (blurred.length) {
                // Tahap 1: tangkap latar kartu saja (semua isi disembunyikan)
                const background = await html2canvas(element, Object.assign({}, base, {
                    onclone: doc => {
                        freezeAnimations(doc);
                        doc.querySelectorAll(`[data-cc-root="${id}"] *`).forEach(el => { el.style.visibility = 'hidden'; });
                    }
                }));
                const rootRect = element.getBoundingClientRect();
                const screenRatio = background.width / rootRect.width;        // px kanvas per px layar
                const cssRatio = background.width / (element.offsetWidth || rootRect.width); // px kanvas per px CSS

                blurred.forEach(el => {
                    const style = getComputedStyle(el);
                    const source = blurCanvas(background, blurRadius(backdropOf(style)) * cssRatio);
                    const rect = el.getBoundingClientRect();
                    const crop = document.createElement('canvas');
                    crop.width = Math.max(1, Math.round(rect.width * screenRatio));
                    crop.height = Math.max(1, Math.round(rect.height * screenRatio));
                    crop.getContext('2d').drawImage(
                        source,
                        Math.round((rect.left - rootRect.left) * screenRatio),
                        Math.round((rect.top - rootRect.top) * screenRatio),
                        crop.width, crop.height, 0, 0, crop.width, crop.height
                    );
                    crops.push({ url: crop.toDataURL('image/png'), color: style.backgroundColor });
                });
            }

            // Tahap 2: tangkap kartu lengkap dengan panel blur & object-fit yang sudah ditiru
            return await html2canvas(element, Object.assign({}, base, {
                onclone: async doc => {
                    freezeAnimations(doc);
                    const root = doc.querySelector(`[data-cc-root="${id}"]`);
                    if (!root) return;
                    await loadCloneFonts(doc, root);
                    fixStaticZIndex(root, doc.defaultView);
                    [root, ...root.querySelectorAll('[data-cc-blur]')].forEach(el => {
                        const crop = crops[Number(el.getAttribute('data-cc-blur'))];
                        if (!el.hasAttribute('data-cc-blur') || !crop) return;
                        el.style.backdropFilter = 'none';
                        el.style.webkitBackdropFilter = 'none';
                        el.style.backgroundImage = `linear-gradient(${crop.color}, ${crop.color}), url("${crop.url}")`;
                        el.style.backgroundSize = '100% 100%';
                        el.style.backgroundRepeat = 'no-repeat';
                        el.style.backgroundColor = 'transparent';
                    });
                    replaceObjectFitImages(root, doc.defaultView);
                    if (typeof options.onclone === 'function') options.onclone(doc, root);
                }
            }));
        } finally {
            element.removeAttribute('data-cc-root');
            blurred.forEach(el => el.removeAttribute('data-cc-blur'));
        }
    }

    /**
     * Cetak gambar kartu (hasil capture) lewat iframe tersembunyi, satu gambar per halaman.
     * Ukuran halaman = ukuran kartu, jadi hasil print sama dengan PDF.
     */
    function printImages(urls, widthMm, heightMm) {
        return new Promise(resolve => {
            const iframe = document.createElement('iframe');
            iframe.setAttribute('aria-hidden', 'true');
            iframe.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;visibility:hidden;';
            document.body.appendChild(iframe);

            const doc = iframe.contentDocument;
            doc.open();
            doc.write('<!DOCTYPE html><html><head><meta charset="UTF-8"><style>'
                + `@page{size:${widthMm}mm ${heightMm}mm;margin:0}`
                + 'html,body{margin:0;padding:0;background:#fff}'
                + '*{-webkit-print-color-adjust:exact;print-color-adjust:exact}'
                + `img{display:block;width:${widthMm}mm;height:${heightMm}mm;break-after:page;page-break-after:always}`
                + 'img:last-child{break-after:auto;page-break-after:auto}'
                + '</style></head><body>' + urls.map(url => `<img src="${url}" alt="">`).join('') + '</body></html>');
            doc.close();

            Promise.all(Array.from(doc.images).map(img => img.complete ? null : new Promise(r => { img.onload = img.onerror = r; })))
                .then(() => {
                    const win = iframe.contentWindow;
                    const cleanup = () => { iframe.remove(); resolve(); };
                    win.addEventListener('afterprint', () => setTimeout(cleanup, 100), { once: true });
                    win.focus();
                    win.print();
                    setTimeout(() => { if (iframe.isConnected) cleanup(); }, 60000);
                });
        });
    }

    return { capture, printImages };
})();
</script>
