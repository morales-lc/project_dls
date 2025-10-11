<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $doc->title }} - Viewer</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #111;
            color: white;
            font-family: "Inter", sans-serif;
            overflow: hidden;
        }

        /* Toolbar Styling */
        #toolbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #222;
            padding: 8px 10px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 10px;
            z-index: 999;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }

        #toolbar button {
            background: #444;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        #toolbar button:hover {
            background: #666;
        }

        #toolbar span {
            font-size: 14px;
            white-space: nowrap;
        }

        /* PDF container */
        #pdf-container {
            margin-top: 55px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: calc(100vh - 55px);
            overflow: auto;
            padding: 0 5px;
        }

        canvas {
            display: block;
            margin: 0 auto;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 0 8px rgba(0,0,0,0.6);
            max-width: 100%;
            height: auto;
        }

        /* Mobile Adjustments */
        @media (max-width: 768px) {
            #toolbar {
                flex-direction: column;
                gap: 6px;
                padding: 10px;
                position: sticky;
            }

            #toolbar button {
                flex: 1 1 auto;
                width: 100%;
                max-width: 280px;
                font-size: 13px;
                padding: 8px;
            }

            #pdf-container {
                margin-top: 130px; /* more space for stacked toolbar */
                height: calc(100vh - 130px);
                padding: 5px;
            }
        }

        @media (max-width: 480px) {
            #toolbar {
                font-size: 12px;
            }
            #toolbar button {
                padding: 6px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Toolbar -->
    <div id="toolbar">
        <button id="prev">⬅ Prev</button>
        <span>Page <span id="page_num">1</span> / <span id="page_count">--</span></span>
        <button id="next">Next ➡</button>
        <button id="zoomOut">➖ Zoom Out</button>
        <button id="zoomIn">➕ Zoom In</button>
        <button id="fitWidth">↔ Fit Width</button>
        <button id="fullscreen">⛶ Fullscreen</button>
    </div>

    <!-- PDF Render Area -->
    <div id="pdf-container">
        <canvas id="pdf-render"></canvas>
    </div>

    <script type="module">
        import * as pdfjsLib from "/pdfjs/pdf.mjs";
        pdfjsLib.GlobalWorkerOptions.workerSrc = "/pdfjs/pdf.worker.mjs";

        const url = "{{ asset('storage/' . $doc->pdf_path) }}";

        let pdfDoc = null,
            pageNum = 1,
            scale = 1.2,
            fitMode = false,
            canvas = document.getElementById("pdf-render"),
            ctx = canvas.getContext("2d");
        let renderTask = null;

        const container = document.getElementById("pdf-container");

        function renderPage(num) {
            if (!pdfDoc) return;
            // cancel any ongoing render to avoid flicker
            if (renderTask && typeof renderTask.cancel === 'function') {
                try { renderTask.cancel(); } catch (e) { /* ignore */ }
                renderTask = null;
            }

            pdfDoc.getPage(num).then(page => {
                let viewport;
                if (fitMode) {
                    const desiredWidth = container.clientWidth - 10;
                    const unscaledViewport = page.getViewport({ scale: 1 });
                    const scaleFactor = desiredWidth / unscaledViewport.width;
                    viewport = page.getViewport({ scale: scaleFactor });
                } else {
                    viewport = page.getViewport({ scale });
                }

                // Use devicePixelRatio for crisp rendering on HiDPI displays
                const outputScale = window.devicePixelRatio || 1;
                canvas.style.width = Math.floor(viewport.width) + 'px';
                canvas.style.height = Math.floor(viewport.height) + 'px';
                canvas.width = Math.floor(viewport.width * outputScale);
                canvas.height = Math.floor(viewport.height * outputScale);
                // reset transform and scale drawing to device pixels
                ctx.setTransform(outputScale, 0, 0, outputScale, 0, 0);

                // hide canvas until rendering completes to avoid flicker
                try { canvas.style.visibility = 'hidden'; } catch (e) {}
                renderTask = page.render({
                    canvasContext: ctx,
                    viewport
                });

                // update page number when rendering finished
                renderTask.promise.then(function() {
                    document.getElementById("page_num").textContent = num;
                    try { canvas.style.visibility = 'visible'; } catch (e) {}
                    renderTask = null;
                }).catch(function(err) {
                    // ignore render cancellation errors
                    renderTask = null;
                });
            }).catch(function(err){
                console.error('Failed to get page', err);
            });
        }

        document.getElementById("prev").onclick = () => {
            if (pageNum <= 1) return;
            pageNum--;
            renderPage(pageNum);
        };

        document.getElementById("next").onclick = () => {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            renderPage(pageNum);
        };

        document.getElementById("zoomIn").onclick = () => {
            scale += 0.2;
            fitMode = false;
            renderPage(pageNum);
        };

        document.getElementById("zoomOut").onclick = () => {
            if (scale > 0.5) {
                scale -= 0.2;
                fitMode = false;
                renderPage(pageNum);
            }
        };

        document.getElementById("fitWidth").onclick = () => {
            fitMode = true;
            renderPage(pageNum);
        };

        document.getElementById("fullscreen").onclick = () => {
            if (!document.fullscreenElement) {
                if (document.documentElement.requestFullscreen) document.documentElement.requestFullscreen();
                else if (document.documentElement.webkitRequestFullscreen) document.documentElement.webkitRequestFullscreen();
            } else {
                if (document.exitFullscreen) document.exitFullscreen();
                else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
            }
        };

        pdfjsLib.getDocument(url).promise.then(pdf => {
            pdfDoc = pdf;
            document.getElementById("page_count").textContent = pdf.numPages;
            renderPage(pageNum);
        });

        window.addEventListener("resize", () => {
            if (fitMode) renderPage(pageNum);
        });

        // Debounce helper
        function debounce(fn, wait) {
            let t = null;
            return function () {
                const args = arguments;
                clearTimeout(t);
                t = setTimeout(function () { fn.apply(null, args); }, wait);
            };
        }

        // Re-render when container size changes (use ResizeObserver if available)
        if (window.ResizeObserver) {
            const debouncedRender = debounce(() => renderPage(pageNum), 150);
            try {
                const ro = new ResizeObserver(debouncedRender);
                ro.observe(container);
            } catch (err) {
                // fallback already covered by window resize
            }
        }

        // If this viewer is inside an iframe modal, observe the parent modal for class changes
        try {
            if (window.frameElement && window.frameElement.closest) {
                const modalEl = window.frameElement.closest('.modal');
                if (modalEl) {
                    const debouncedRender = debounce(() => renderPage(pageNum), 120);
                    const mo = new MutationObserver(function (mutations) {
                        for (const m of mutations) {
                            if (m.attributeName === 'class') {
                                const cls = modalEl.className || '';
                                if (cls.indexOf('show') !== -1) {
                                    // modal shown — wait a tick then render
                                    debouncedRender();
                                }
                            }
                        }
                    });
                    mo.observe(modalEl, { attributes: true, attributeFilter: ['class'] });
                }
            }
        } catch (e) {
            // cross-origin or other access issues — ignore
        }

        // Also listen for Bootstrap modal events on the parent modal (preferred over mutation in some cases)
        try {
            if (window.frameElement && window.frameElement.closest) {
                const parentModal = window.frameElement.closest('.modal');
                if (parentModal && parentModal.addEventListener) {
                    const onShown = function() {
                        // render after a short delay; run twice to ensure stability after animation
                        setTimeout(() => renderPage(pageNum), 80);
                        setTimeout(() => renderPage(pageNum), 260);
                    };
                    parentModal.addEventListener('shown.bs.modal', onShown);
                    parentModal.addEventListener('show.bs.modal', function() { setTimeout(() => renderPage(pageNum), 120); });
                    // if modal already open, force a render
                    if ((parentModal.className || '').indexOf('show') !== -1) {
                        setTimeout(() => renderPage(pageNum), 80);
                    }
                }
            }
        } catch (e) {
            // ignore
        }
    </script>
</body>
</html>
