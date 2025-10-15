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
            /* Hide the top toolbar and show a compact bottom toolbar for touch devices */
            #toolbar { display: none; }
            #bottom-toolbar {
                position: fixed;
                bottom: 8px;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(34,34,34,0.95);
                padding: 8px 10px;
                border-radius: 999px;
                display: flex;
                gap: 8px;
                z-index: 1200;
                align-items: center;
                box-shadow: 0 6px 18px rgba(0,0,0,0.45);
            }

            #bottom-toolbar button {
                background: #333;
                color: #fff;
                border: none;
                padding: 10px 12px;
                border-radius: 8px;
                font-size: 16px;
                min-width: 44px; /* recommend touch size */
                min-height: 44px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            #pdf-container {
                margin-top: 0;
                height: calc(100vh - 24px); /* leave room for bottom toolbar */
                padding: 8px 6px 64px; /* bottom padding so last page isn't hidden */
                box-sizing: border-box;
            }

            /* Add a small toggle button on the bottom-left to open the full toolbar if needed */
            #toolbar-toggle {
                position: fixed;
                left: 10px;
                bottom: 18px;
                z-index: 1210;
                background: rgba(34,34,34,0.95);
                color: #fff;
                border: none;
                padding: 10px 12px;
                border-radius: 8px;
                font-size: 16px;
                box-shadow: 0 6px 18px rgba(0,0,0,0.35);
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

        // Create bottom toolbar for mobile and the toggle control
        function ensureBottomToolbar() {
            if (!document.getElementById('bottom-toolbar')) {
                const bt = document.createElement('div');
                bt.id = 'bottom-toolbar';
                bt.innerHTML = `
                    <button id="b-prev">⬅</button>
                    <button id="b-next">➡</button>
                    <button id="b-zoomOut">➖</button>
                    <button id="b-zoomIn">➕</button>
                    <button id="b-fitWidth">↔</button>
                `;
                document.body.appendChild(bt);

                const toggle = document.createElement('button');
                toggle.id = 'toolbar-toggle';
                toggle.title = 'Open controls';
                toggle.innerText = '☰';
                document.body.appendChild(toggle);

                // wire bottom toolbar to existing handlers
                bt.querySelector('#b-prev').addEventListener('click', () => document.getElementById('prev').click());
                bt.querySelector('#b-next').addEventListener('click', () => document.getElementById('next').click());
                bt.querySelector('#b-zoomOut').addEventListener('click', () => document.getElementById('zoomOut').click());
                bt.querySelector('#b-zoomIn').addEventListener('click', () => document.getElementById('zoomIn').click());
                bt.querySelector('#b-fitWidth').addEventListener('click', () => document.getElementById('fitWidth').click());

                toggle.addEventListener('click', () => {
                    const top = document.getElementById('toolbar');
                    if (top) {
                        if (top.style.display === 'flex' || top.style.display === '') {
                            top.style.display = 'none';
                        } else {
                            top.style.display = 'flex';
                            top.style.position = 'fixed';
                            top.style.top = '8px';
                            top.style.left = '8px';
                            top.style.right = '8px';
                            top.style.zIndex = '1300';
                        }
                    }
                });
            }
        }

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
                    // fit to available width and height (considering mobile bottom toolbar)
                    const desiredWidth = container.clientWidth - 10;
                    const desiredHeight = container.clientHeight - 10;
                    const unscaledViewport = page.getViewport({ scale: 1 });
                    const scaleX = desiredWidth / unscaledViewport.width;
                    const scaleY = desiredHeight / unscaledViewport.height;
                    const scaleFactor = Math.min(scaleX, scaleY);
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
            // Ensure bottom toolbar exists on mobile
            ensureBottomToolbar();
        });

        window.addEventListener("resize", () => {
            // recreate bottom toolbar if viewport changes
            try { ensureBottomToolbar(); } catch (e) {}
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

        document.addEventListener('contextmenu', event => event.preventDefault());
    </script>
</body>
</html>
