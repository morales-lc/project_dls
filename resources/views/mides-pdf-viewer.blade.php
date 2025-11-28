
<!-- PDF viewer for MIDES based on pdf.js with improved zooming, panning, and mobile support -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title>{{ $doc->title }} - Viewer</title>
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
        }
        
        body {
            margin: 0;
            padding: 0;
            background: #111;
            color: white;
            font-family: "Inter", sans-serif;
            overflow: hidden;
            touch-action: none;
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

        /* Desktop toolbar show/hide floating button */
        #toolbar-fab {
            position: fixed;
            top: 8px;
            right: 8px;
            z-index: 1300;
            background: rgba(34,34,34,0.95);
            color: #fff;
            border: none;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 14px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.35);
            display: none; /* shown when toolbar is hidden on desktop */
            cursor: pointer;
        }

        /* PDF container */
        #pdf-container {
            margin-top: 55px;
            height: calc(100vh - 55px);
            overflow: auto;
            padding: 0 5px;
            position: relative;
        }

        canvas {
            display: block;
            margin: 0 auto;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.6);
            /* Avoid CSS scaling that can distort aspect ratio at large zooms */
            max-width: none;
            height: auto;
            /* height will be set via JS; auto here won’t conflict since width isn’t clamped */
            flex: 0 0 auto;
            /* prevent flexbox from stretching the canvas */
        }

        /* Drag-to-pan cues for desktop */
        canvas.grabbable {
            cursor: grab;
        }
        canvas.grabbing {
            cursor: grabbing;
        }

        /* Mobile Adjustments */
        @media (max-width: 768px) {

            /* Hide the top toolbar and show a compact bottom toolbar for touch devices */
            #toolbar {
                display: none;
            }

            #bottom-toolbar {
                position: fixed;
                bottom: 12px;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(34, 34, 34, 0.97);
                padding: 10px 14px;
                border-radius: 999px;
                display: flex;
                gap: 10px;
                z-index: 1200;
                align-items: center;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(10px);
                max-width: 95vw;
                overflow-x: auto;
            }

            #bottom-toolbar button {
                background: #444;
                color: #fff;
                border: none;
                padding: 12px 14px;
                border-radius: 10px;
                font-size: 18px;
                min-width: 48px;
                min-height: 48px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                font-weight: 600;
                transition: all 0.2s;
            }
            
            #bottom-toolbar button:active {
                background: #666;
                transform: scale(0.95);
            }

            #pdf-container {
                margin-top: 0;
                height: 100vh;
                padding: 12px 4px 88px 4px;
                box-sizing: border-box;
                overflow-x: auto !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch;
                overscroll-behavior: contain;
            }
            
            canvas {
                touch-action: manipulation;
                margin: 0 !important;
                display: block !important;
                flex: none !important;
                max-width: none !important;
            }

            /* Add a small toggle button on the bottom-left to open the full toolbar if needed */
            #toolbar-toggle {
                position: fixed;
                left: 12px;
                bottom: 24px;
                z-index: 1210;
                background: rgba(34, 34, 34, 0.97);
                color: #fff;
                border: none;
                padding: 12px 14px;
                border-radius: 50%;
                font-size: 18px;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.45);
                min-width: 48px;
                min-height: 48px;
                display: flex;
                align-items: center;
                justify-content: center;
                backdrop-filter: blur(10px);
            }
            
            #mobile-page-indicator {
                position: fixed;
                top: 12px;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(34, 34, 34, 0.9);
                color: #fff;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 14px;
                z-index: 1100;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(8px);
                pointer-events: none;
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
        <span id="zoomIndicator" style="min-width:64px;text-align:center;opacity:.9">120%</span>
        <button id="hideControls" title="Hide controls">Hide Controls</button>
        <button id="fullscreen">⛶ Fullscreen</button>
    </div>

    <!-- Desktop toolbar toggle (shown when toolbar is hidden) -->
    <button id="toolbar-fab" title="Show controls">☰ Controls</button>

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
        // Zoom boundaries and step for smoother, "real" zooming behavior
        const MIN_SCALE = 0.25;
        const MAX_SCALE = 5;
        const ZOOM_IN_FACTOR = 1.1; // ~10% per notch
        const ZOOM_OUT_FACTOR = 1 / ZOOM_IN_FACTOR;
        // Anchor to maintain point-of-interest during zoom (set before render, consumed in renderPage)
        let pendingAnchor = null; // { ratioX, ratioY }

        const container = document.getElementById("pdf-container");

        // Create bottom toolbar for mobile and the toggle control
        function ensureBottomToolbar() {
            const isMobile = window.matchMedia('(max-width: 768px)').matches;
            const existing = document.getElementById('bottom-toolbar');
            const existingToggle = document.getElementById('toolbar-toggle');
            if (!isMobile) {
                // Remove mobile controls on desktop
                if (existing && existing.parentNode) existing.parentNode.removeChild(existing);
                if (existingToggle && existingToggle.parentNode) existingToggle.parentNode.removeChild(existingToggle);
                return;
            }
            if (!document.getElementById('bottom-toolbar')) {
                const bt = document.createElement('div');
                bt.id = 'bottom-toolbar';
                bt.innerHTML = `
                    <button id="b-prev" title="Previous page">⬅</button>
                    <button id="b-next" title="Next page">➡</button>
                    <button id="b-zoomOut" title="Zoom out">➖</button>
                    <button id="b-zoomIn" title="Zoom in">➕</button>
                    <button id="b-fitWidth" title="Fit to screen">↔</button>
                    <span id="b-zoomIndicator" style="color:#e0e0e0;min-width:52px;text-align:center;font-weight:600;font-size:15px;">100%</span>
                `;
                document.body.appendChild(bt);
                
                // Add mobile page indicator
                const pageInd = document.createElement('div');
                pageInd.id = 'mobile-page-indicator';
                pageInd.innerHTML = 'Page <span id="m-page-num">1</span> / <span id="m-page-count">--</span>';
                document.body.appendChild(pageInd);

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
                        updateDesktopFabVisibility();
                    }
                });
            }
        }

        // Desktop toolbar hide/show helpers
        function hideToolbarDesktop() {
            const top = document.getElementById('toolbar');
            if (top) top.style.display = 'none';
            updateDesktopFabVisibility();
        }
        function showToolbarDesktop() {
            const top = document.getElementById('toolbar');
            if (top) top.style.display = 'flex';
            updateDesktopFabVisibility();
        }
        function updateDesktopFabVisibility() {
            const fab = document.getElementById('toolbar-fab');
            const top = document.getElementById('toolbar');
            const isMobile = window.matchMedia('(max-width: 768px)').matches;
            if (!fab) return;
            if (!isMobile && top && (top.style.display === 'none')) {
                fab.style.display = 'inline-block';
            } else {
                fab.style.display = 'none';
            }
        }

        function renderPage(num) {
            if (!pdfDoc) return;
            // cancel any ongoing render to avoid flicker
            if (renderTask && typeof renderTask.cancel === 'function') {
                try {
                    renderTask.cancel();
                } catch (e) {
                    /* ignore */ }
                renderTask = null;
            }

            pdfDoc.getPage(num).then(page => {
                let viewport;
                if (fitMode) {
                    // fit to available width and height (considering mobile bottom toolbar)
                    const isMobile = window.matchMedia('(max-width: 768px)').matches;
                    const desiredWidth = container.clientWidth - (isMobile ? 16 : 10);
                    const desiredHeight = container.clientHeight - (isMobile ? 100 : 10);
                    const unscaledViewport = page.getViewport({
                        scale: 1
                    });
                    const scaleX = desiredWidth / unscaledViewport.width;
                    const scaleY = desiredHeight / unscaledViewport.height;
                    // On mobile, prefer width fit for better readability
                    const scaleFactor = isMobile ? scaleX : Math.min(scaleX, scaleY);
                    viewport = page.getViewport({
                        scale: scaleFactor
                    });
                } else {
                    viewport = page.getViewport({
                        scale
                    });
                }

                // Update zoom indicator based on the effective viewport scale
                updateZoomIndicator(viewport.scale);

                // Use devicePixelRatio for crisp rendering on HiDPI displays
                const outputScale = window.devicePixelRatio || 1;
                canvas.style.width = Math.floor(viewport.width) + 'px';
                canvas.style.height = Math.floor(viewport.height) + 'px';
                canvas.width = Math.floor(viewport.width * outputScale);
                canvas.height = Math.floor(viewport.height * outputScale);
                // reset transform and scale drawing to device pixels
                ctx.setTransform(outputScale, 0, 0, outputScale, 0, 0);

                // If we had a requested anchor (from a zoom action), keep the same point in view
                if (pendingAnchor) {
                    try {
                        const newW = parseFloat(canvas.style.width) || 0;
                        const newH = parseFloat(canvas.style.height) || 0;
                        const targetLeft = newW * pendingAnchor.ratioX - container.clientWidth / 2;
                        const targetTop = newH * pendingAnchor.ratioY - container.clientHeight / 2;
                        const maxLeft = Math.max(0, newW - container.clientWidth);
                        const maxTop = Math.max(0, newH - container.clientHeight);
                        container.scrollLeft = Math.min(maxLeft, Math.max(0, targetLeft));
                        container.scrollTop = Math.min(maxTop, Math.max(0, targetTop));
                    } catch (e) {}
                    pendingAnchor = null;
                }

                // Toggle grab cursor when content exceeds viewport in any axis (desktop only)
                try {
                    const isMobile = window.matchMedia('(max-width: 768px)').matches;
                    const cw = parseFloat(canvas.style.width) || 0;
                    const ch = parseFloat(canvas.style.height) || 0;
                    if (!isMobile && (cw > container.clientWidth + 0.5 || ch > container.clientHeight + 0.5)) {
                        canvas.classList.add('grabbable');
                    } else {
                        canvas.classList.remove('grabbable');
                    }
                } catch (e) {}

                // hide canvas until rendering completes to avoid flicker
                try {
                    canvas.style.visibility = 'hidden';
                } catch (e) {}
                renderTask = page.render({
                    canvasContext: ctx,
                    viewport
                });

                // update page number when rendering finished
                renderTask.promise.then(function() {
                    document.getElementById("page_num").textContent = num;
                    const mPageNum = document.getElementById('m-page-num');
                    if (mPageNum) mPageNum.textContent = num;
                    try {
                        canvas.style.visibility = 'visible';
                    } catch (e) {}
                    renderTask = null;
                }).catch(function(err) {
                    // ignore render cancellation errors
                    renderTask = null;
                });
            }).catch(function(err) {
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

        function getCenterAnchorRatios() {
            // Compute center based on scroll offsets to avoid jumps when crossing overflow thresholds
            try {
                const dpr = window.devicePixelRatio || 1;
                const canvW = canvas.clientWidth || parseFloat(canvas.style.width) || (canvas.width / dpr) || 0;
                const canvH = canvas.clientHeight || parseFloat(canvas.style.height) || (canvas.height / dpr) || 0;
                let ratioX = 0.5, ratioY = 0.5;
                if (canvW > container.clientWidth + 0.5) {
                    ratioX = (container.scrollLeft + container.clientWidth / 2) / canvW;
                }
                if (canvH > container.clientHeight + 0.5) {
                    ratioY = (container.scrollTop + container.clientHeight / 2) / canvH;
                }
                return { ratioX: Math.min(1, Math.max(0, ratioX)), ratioY: Math.min(1, Math.max(0, ratioY)) };
            } catch (e) {
                return { ratioX: 0.5, ratioY: 0.5 };
            }
        }

        function getMouseAnchorRatios(evt) {
            try {
                const rect = canvas.getBoundingClientRect();
                const x = evt.clientX - rect.left;
                const y = evt.clientY - rect.top;
                const ratioX = rect.width ? x / rect.width : 0.5;
                const ratioY = rect.height ? y / rect.height : 0.5;
                return {
                    ratioX: Math.min(1, Math.max(0, ratioX)),
                    ratioY: Math.min(1, Math.max(0, ratioY))
                };
            } catch (e) {
                return {
                    ratioX: 0.5,
                    ratioY: 0.5
                };
            }
        }

        function setScale(newScale, anchor) {
            const clamped = Math.min(MAX_SCALE, Math.max(MIN_SCALE, newScale));
            if (Math.abs(clamped - scale) < 0.001) return;
            fitMode = false;
            pendingAnchor = anchor || getCenterAnchorRatios();
            scale = clamped;
            renderPage(pageNum);
        }

        document.getElementById("zoomIn").onclick = () => {
            setScale(scale * ZOOM_IN_FACTOR, getCenterAnchorRatios());
        };

        document.getElementById("zoomOut").onclick = () => {
            setScale(scale * ZOOM_OUT_FACTOR, getCenterAnchorRatios());
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
            const mPageCount = document.getElementById('m-page-count');
            if (mPageCount) mPageCount.textContent = pdf.numPages;
            renderPage(pageNum);
            // Ensure bottom toolbar exists on mobile
            ensureBottomToolbar();
            updateDesktopFabVisibility();
        });

        window.addEventListener("resize", () => {
            // recreate bottom toolbar if viewport changes
            try {
                ensureBottomToolbar();
            } catch (e) {}
            updateDesktopFabVisibility();
            if (fitMode) renderPage(pageNum);
        });

        // Smooth Ctrl/Meta + wheel zoom with anchor at mouse position
        container.addEventListener('wheel', (e) => {
            if (e.ctrlKey || e.metaKey) {
                e.preventDefault();
                const delta = e.deltaY;
                const factor = delta < 0 ? ZOOM_IN_FACTOR : ZOOM_OUT_FACTOR;
                const anchor = getMouseAnchorRatios(e);
                setScale(scale * factor, anchor);
            }
        }, {
            passive: false
        });

        // Double-click to zoom in (Shift + double-click to zoom out)
        container.addEventListener('dblclick', (e) => {
            const anchor = getMouseAnchorRatios(e);
            if (e.shiftKey) setScale(scale * ZOOM_OUT_FACTOR, anchor);
            else setScale(scale * (ZOOM_IN_FACTOR * ZOOM_IN_FACTOR), anchor); // a bit stronger zoom on dblclick
        });

        // Keyboard shortcuts similar to common viewers
        document.addEventListener('keydown', (e) => {
            // Arrow key navigation (Left/Up = Previous, Right/Down = Next)
            if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                e.preventDefault();
                if (pageNum > 1) {
                    pageNum--;
                    renderPage(pageNum);
                }
                return;
            }
            if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                e.preventDefault();
                if (pdfDoc && pageNum < pdfDoc.numPages) {
                    pageNum++;
                    renderPage(pageNum);
                }
                return;
            }

            if (e.ctrlKey || e.metaKey) {
                // Ctrl+'+' or Ctrl+=''
                if (e.key === '+' || e.key === '=') {
                    e.preventDefault();
                    setScale(scale * ZOOM_IN_FACTOR, getCenterAnchorRatios());
                }
                // Ctrl+'-'
                if (e.key === '-') {
                    e.preventDefault();
                    setScale(scale * ZOOM_OUT_FACTOR, getCenterAnchorRatios());
                }
                // Ctrl+'0' => Fit width
                if (e.key === '0') {
                    e.preventDefault();
                    fitMode = true;
                    renderPage(pageNum);
                }
                // Ctrl+'1' => 100%
                if (e.key === '1') {
                    e.preventDefault();
                    setScale(1.0, getCenterAnchorRatios());
                }
            }
        });

        // Zoom indicator updater
        function updateZoomIndicator(effectiveScale) {
            try {
                const pct = Math.round(effectiveScale * 100);
                const el = document.getElementById('zoomIndicator');
                if (el) el.textContent = pct + '%';
                const bel = document.getElementById('b-zoomIndicator');
                if (bel) bel.textContent = pct + '%';
            } catch (e) {}
        }

        // --- Mobile pinch-to-zoom and swipe navigation ---
        (function enableMobileTouchGestures() {
            const isMobile = () => window.matchMedia('(max-width: 768px)').matches;
            let initialDistance = 0;
            let initialScale = 1;
            let lastTouchEnd = 0;
            let swipeStartX = 0;
            let swipeStartY = 0;
            const SWIPE_THRESHOLD = 80;
            
            canvas.addEventListener('touchstart', (e) => {
                if (!isMobile()) return;
                
                if (e.touches.length === 2) {
                    // Pinch zoom start
                    e.preventDefault();
                    const touch1 = e.touches[0];
                    const touch2 = e.touches[1];
                    initialDistance = Math.hypot(
                        touch2.clientX - touch1.clientX,
                        touch2.clientY - touch1.clientY
                    );
                    initialScale = scale;
                } else if (e.touches.length === 1) {
                    // Track for swipe
                    swipeStartX = e.touches[0].clientX;
                    swipeStartY = e.touches[0].clientY;
                }
            }, { passive: false });
            
            canvas.addEventListener('touchmove', (e) => {
                if (!isMobile()) return;
                
                if (e.touches.length === 2) {
                    // Pinch zoom
                    e.preventDefault();
                    const touch1 = e.touches[0];
                    const touch2 = e.touches[1];
                    const currentDistance = Math.hypot(
                        touch2.clientX - touch1.clientX,
                        touch2.clientY - touch1.clientY
                    );
                    
                    if (initialDistance > 0) {
                        const ratio = currentDistance / initialDistance;
                        const newScale = initialScale * ratio;
                        setScale(newScale, getCenterAnchorRatios());
                    }
                }
            }, { passive: false });
            
            canvas.addEventListener('touchend', (e) => {
                if (!isMobile()) return;
                
                // Handle double-tap to zoom
                const now = Date.now();
                if (now - lastTouchEnd < 300) {
                    e.preventDefault();
                    if (scale > 1.5) {
                        // Zoom out to fit
                        fitMode = true;
                        renderPage(pageNum);
                    } else {
                        // Zoom in
                        setScale(scale * 1.8, getCenterAnchorRatios());
                    }
                }
                lastTouchEnd = now;
                
                // Handle swipe navigation
                if (e.changedTouches.length === 1 && swipeStartX !== 0) {
                    const swipeEndX = e.changedTouches[0].clientX;
                    const swipeEndY = e.changedTouches[0].clientY;
                    const deltaX = swipeEndX - swipeStartX;
                    const deltaY = swipeEndY - swipeStartY;
                    
                    // Only trigger if horizontal swipe is dominant
                    if (Math.abs(deltaX) > SWIPE_THRESHOLD && Math.abs(deltaX) > Math.abs(deltaY) * 1.5) {
                        if (deltaX > 0 && pageNum > 1) {
                            // Swipe right = previous page
                            pageNum--;
                            renderPage(pageNum);
                        } else if (deltaX < 0 && pdfDoc && pageNum < pdfDoc.numPages) {
                            // Swipe left = next page
                            pageNum++;
                            renderPage(pageNum);
                        }
                    }
                }
                
                initialDistance = 0;
                swipeStartX = 0;
                swipeStartY = 0;
            }, { passive: false });
        })();
        
        // --- Desktop drag-to-pan (click and drag) ---
        (function enableDesktopDragToPan(){
            const isDesktop = () => !window.matchMedia('(max-width: 768px)').matches;
            let panning = false;
            let moved = false;
            let startX = 0, startY = 0;
            let startLeft = 0, startTop = 0;
            const THRESH = 3; // px before we treat it as a drag

            const onPointerDown = (e) => {
                if (!isDesktop()) return; // desktop only
                if (e.button !== 0 && e.button !== 1) return; // left or middle button
                // Only start if there is actually something to pan
                const cw = canvas.clientWidth;
                const ch = canvas.clientHeight;
                if (cw <= container.clientWidth + 0.5 && ch <= container.clientHeight + 0.5) return;

                panning = true;
                moved = false;
                startX = e.clientX;
                startY = e.clientY;
                startLeft = container.scrollLeft;
                startTop = container.scrollTop;
                try { canvas.classList.add('grabbing'); } catch (err) {}
                try { canvas.setPointerCapture && canvas.setPointerCapture(e.pointerId); } catch (err) {}
            };

            const onPointerMove = (e) => {
                if (!panning) return;
                const dx = e.clientX - startX;
                const dy = e.clientY - startY;
                if (!moved && (Math.abs(dx) > THRESH || Math.abs(dy) > THRESH)) moved = true;
                if (moved) {
                    e.preventDefault();
                    container.scrollLeft = startLeft - dx;
                    container.scrollTop = startTop - dy;
                }
            };

            const endPan = (e) => {
                if (!panning) return;
                panning = false;
                try { canvas.classList.remove('grabbing'); } catch (err) {}
                try { canvas.releasePointerCapture && canvas.releasePointerCapture(e.pointerId); } catch (err) {}
            };

            // Prefer pointer events if available
            const downEvt = ('onpointerdown' in window) ? 'pointerdown' : 'mousedown';
            const moveEvt = (downEvt === 'pointerdown') ? 'pointermove' : 'mousemove';
            const upEvt   = (downEvt === 'pointerdown') ? 'pointerup'   : 'mouseup';

            canvas.addEventListener(downEvt, onPointerDown);
            window.addEventListener(moveEvt, onPointerMove, { passive: false });
            window.addEventListener(upEvt, endPan);
            window.addEventListener('mouseleave', endPan);
        })();

        // Debounce helper
        function debounce(fn, wait) {
            let t = null;
            return function() {
                const args = arguments;
                clearTimeout(t);
                t = setTimeout(function() {
                    fn.apply(null, args);
                }, wait);
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
                    const mo = new MutationObserver(function(mutations) {
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
                    mo.observe(modalEl, {
                        attributes: true,
                        attributeFilter: ['class']
                    });
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
                    parentModal.addEventListener('show.bs.modal', function() {
                        setTimeout(() => renderPage(pageNum), 120);
                    });
                    // if modal already open, force a render
                    if ((parentModal.className || '').indexOf('show') !== -1) {
                        setTimeout(() => renderPage(pageNum), 80);
                    }
                }
            }
        } catch (e) {
            // ignore
        }

    // Desktop toolbar events
    document.getElementById('hideControls').addEventListener('click', hideToolbarDesktop);
    document.getElementById('toolbar-fab').addEventListener('click', showToolbarDesktop);

    document.addEventListener('contextmenu', event => event.preventDefault());
    </script>
</body>

</html>