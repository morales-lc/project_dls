<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    @include('navbar')
@include('elibraries.modals')
<div class="container py-4">
    <h2 class="mb-4">Online E-Library Access</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Wiley E-Books</h5>
                    <p class="card-text">Access a wide range of academic e-books via Wiley. Use the credentials and bookmarklet for easy login.</p>
                    <button class="btn btn-primary mt-auto" data-bs-toggle="modal" data-bs-target="#wileyModal">Access Wiley</button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">GALE</h5>
                    <p class="card-text">Gale Cengage Learning contains selected current newspapers; social, medical, legal, business, health issues; and literary criticism. Articles are offered with full-text, abstracts, and citations. Users may print or email documents. Gale offers advanced search features for users to refine their search.</p>
                    <button class="btn btn-primary mt-auto" data-bs-toggle="modal" data-bs-target="#galeModal">Access GALE</button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">ProQuest</h5>
                    <p class="card-text">Log in to ProQuest for journals, dissertations, and more. Credentials and bookmarklet provided.</p>
                    <button class="btn btn-primary mt-auto" data-bs-toggle="modal" data-bs-target="#proquestModal">Access ProQuest</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="height: 40px;"></div>
<div style="height: 40px;"></div>
<div style="height: 40px;"></div>
@include('footer')

</body>
</html>


<!-- Modals are included from the navbar for now -->

