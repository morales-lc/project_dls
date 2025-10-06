<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $doc->title }} - Viewer</title>
    <style>
        body { margin:0; padding:0; background:#111; color:white; font-family:sans-serif; }
        #toolbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: #222;
            padding: 8px;
            display: flex;
            justify-content: center;
            gap: 10px;
            z-index: 999;
        }
        #pdf-container {
            margin-top: 50px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: calc(100vh - 50px);
            overflow: auto;
        }
        canvas {
            display: block;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 0 5px rgba(0,0,0,0.5);
        }
        button {
            background: #444;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover { background: #666; }
        span { margin: 0 8px; }
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
            scale = 1.3,
            fitMode = false,
            canvas = document.getElementById("pdf-render"),
            ctx = canvas.getContext("2d");

        const container = document.getElementById("pdf-container");

        // Render page
        function renderPage(num) {
            pdfDoc.getPage(num).then(page => {
                let viewport;
                if (fitMode) {
                    // Auto fit width to container
                    const desiredWidth = container.clientWidth - 20;
                    const unscaledViewport = page.getViewport({ scale: 1 });
                    const scaleFactor = desiredWidth / unscaledViewport.width;
                    viewport = page.getViewport({ scale: scaleFactor });
                } else {
                    viewport = page.getViewport({ scale: scale });
                }

                canvas.height = viewport.height;
                canvas.width = viewport.width;

                let renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                page.render(renderContext);

                // Update page counters
                document.getElementById("page_num").textContent = num;
            });
        }

        // Prev page
        document.getElementById("prev").addEventListener("click", () => {
            if (pageNum <= 1) return;
            pageNum--;
            renderPage(pageNum);
        });

        // Next page
        document.getElementById("next").addEventListener("click", () => {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            renderPage(pageNum);
        });

        // Zoom In
        document.getElementById("zoomIn").addEventListener("click", () => {
            scale += 0.2;
            fitMode = false;
            renderPage(pageNum);
        });

        // Zoom Out
        document.getElementById("zoomOut").addEventListener("click", () => {
            if (scale > 0.5) {
                scale -= 0.2;
                fitMode = false;
                renderPage(pageNum);
            }
        });

        // Fit to Width
        document.getElementById("fitWidth").addEventListener("click", () => {
            fitMode = true;
            renderPage(pageNum);
        });

        // Fullscreen toggle
        document.getElementById("fullscreen").addEventListener("click", () => {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        });

        // Load PDF
        pdfjsLib.getDocument(url).promise.then(pdf => {
            pdfDoc = pdf;
            document.getElementById("page_count").textContent = pdf.numPages;
            renderPage(pageNum);
        });

        // Handle resize for fitWidth
        window.addEventListener("resize", () => {
            if (fitMode) renderPage(pageNum);
        });
    </script>
</body>
</html>
