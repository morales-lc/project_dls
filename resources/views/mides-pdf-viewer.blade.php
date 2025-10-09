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

        const container = document.getElementById("pdf-container");

        function renderPage(num) {
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

                canvas.height = viewport.height;
                canvas.width = viewport.width;

                page.render({
                    canvasContext: ctx,
                    viewport
                });

                document.getElementById("page_num").textContent = num;
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
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen?.();
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
    </script>
</body>
</html>
