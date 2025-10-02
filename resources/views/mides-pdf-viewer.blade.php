<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $doc->title }} - Viewer</title>
    <style>
        body { margin:0; padding:0; background:#111; }
        #pdf-container { width:100%; height:100vh; overflow:auto; background:#111; display:flex; justify-content:center; }
        canvas { margin:10px auto; display:block; background:#fff; box-shadow:0 0 5px rgba(0,0,0,0.5); }
    </style>
</head>
<body>
    <div id="pdf-container"></div>

    <script type="module">
        import * as pdfjsLib from "/pdfjs/pdf.mjs";

        // Worker path
        pdfjsLib.GlobalWorkerOptions.workerSrc = "/pdfjs/pdf.worker.mjs";

        // PDF path (from storage)
        const url = "{{ asset('storage/' . $doc->pdf_path) }}";

        const container = document.getElementById("pdf-container");

        pdfjsLib.getDocument(url).promise.then(pdf => {
            console.log("PDF loaded, total pages:", pdf.numPages);

            for (let i = 1; i <= pdf.numPages; i++) {
                pdf.getPage(i).then(page => {
                    const viewport = page.getViewport({ scale: 1.3 });
                    const canvas = document.createElement("canvas");
                    const context = canvas.getContext("2d");
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    container.appendChild(canvas);

                    page.render({ canvasContext: context, viewport: viewport });
                });
            }
        });
    </script>
</body>
</html>
