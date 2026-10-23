(function () {
    var root = document.querySelector('[data-pdf-preview]');
    if (!root || !window.pdfjsLib) {
        return;
    }

    var canvas = root.querySelector('canvas');
    var status = root.querySelector('[data-pdf-status]');
    pdfjsLib.GlobalWorkerOptions.workerSrc = root.getAttribute('data-pdf-worker');

    pdfjsLib.getDocument({ url: root.getAttribute('data-pdf-preview') }).promise
        .then(function (pdf) {
            return pdf.getPage(1);
        })
        .then(function (page) {
            var width = root.clientWidth || 440;
            var base = page.getViewport({ scale: 1 });
            var viewport = page.getViewport({ scale: width / base.width });
            canvas.width = Math.floor(viewport.width);
            canvas.height = Math.floor(viewport.height);
            return page.render({
                canvasContext: canvas.getContext('2d'),
                viewport: viewport
            }).promise;
        })
        .then(function () {
            canvas.hidden = false;
            if (status) {
                status.hidden = true;
            }
        })
        .catch(function () {
            if (status) {
                status.textContent = 'Preview tidak tampil di panel ini. Gunakan Tab baru.';
            }
        });
})();
