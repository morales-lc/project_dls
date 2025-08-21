<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>

<body>


    <nav class="navbar navbar-expand-lg navbar-dark bg-pink px-4">
        <a class="navbar-brand fw-bold text-white" href="{{ route("dashboard") }}">Logo here</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="{{ route('dashboard') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route("about") }}">About</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Libraries
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#wileyModal">WILEY E-BOOKS</a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#galeModal">GALE</a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#proquestModal">ProQuest</a></li>



                    </ul>


                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Services
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                        <li><a class="dropdown-item" href="#">LiRA</a></li>
                        <li><a class="dropdown-item" href="#">MIDES Repository</a></li>
                        <li><a class="dropdown-item" href="#">Alert Services</a></li>
                        <li><a class="dropdown-item" href="#">ALINET</a></li>
                        <li><a class="dropdown-item" href="#">Book borrowing</a></li>
                        <li><a class="dropdown-item" href="#">Information Literacy Alert Schedule</a></li>
                        <li><a class="dropdown-item" href="#">Scanning Services</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="eresourcesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Electronic Resources
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="eresourcesDropdown">
                        <li><a class="dropdown-item" href="{{ route('mides.dashboard') }}">MIDES repository</a></li>
                        <li><a class="dropdown-item" href="#">SIDLAk</a></li>
                    </ul>
                </li>
            </ul>
            <div class="d-flex align-items-center text-white">
                @php
                $profilePic = Auth::user()->studentFaculty->profile_picture ?? null;
                $isGooglePic = $profilePic && str_starts_with($profilePic, 'http');
                $fullName = trim((Auth::user()->studentFaculty->first_name ?? '') . ' ' . (Auth::user()->studentFaculty->last_name ?? ''));
                @endphp
                <img src="{{ $isGooglePic ? $profilePic : ($profilePic ? asset('storage/profile_pictures/' . $profilePic) : 'https://ui-avatars.com/api/?name=' . urlencode($fullName ?: Auth::user()->name)) }}" alt="Profile Picture" class="rounded-circle me-2" width="36" height="36">
                <div class="d-flex flex-column">
                    <span class="fw-bold">{{ $fullName ?: Auth::user()->name }}</span>
                    <a href="{{ route('profile') }}" class="text-white-50 text-decoration-underline small">View profile</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Wiley Instructions Modal -->
    <!-- Wiley Instructions Modal -->
    <div class="modal fade" id="wileyModal" tabindex="-1" aria-labelledby="wileyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-light">
                <div class="modal-header">
                    <h5 class="modal-title" id="wileyModalLabel">Wiley E-Books Access</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ol>
                        <li>Click this link: <a href="https://onlinelibrary.wiley.com/action/ssostart?redirectUri=%2F#" target="_blank">Wiley Login Page</a></li>
                        <li>On the Wiley page, click <strong>"enter the credentials here."</strong></li>
                        <li>Enter the following credentials:</li>
                    </ol>

                    <div class="bg-white p-3 rounded border mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <strong class="me-2">Username:</strong>
                            <code id="wileyUsername">lourdescollege</code>
                            <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('wileyUsername')">Copy</button>
                        </div>
                        <div class="d-flex align-items-center">
                            <strong class="me-2">Password:</strong>
                            <code id="wileyPassword">Wiley12345!</code>
                            <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('wileyPassword')">Copy</button>
                        </div>
                    </div>

                    <p class="small mb-1"><strong>Optional:</strong> Drag this button to your bookmarks bar for 1-click Wiley login:</p>
                    <a class="btn btn-sm btn-warning"
                        href="javascript:(function(){
                                    var USER='lourdescollege', PASS='Wiley12345!', submitted=false, deadline=Date.now()+15000;
                                    function fire(el){['input','change','keyup','blur'].forEach(e=>{try{el.dispatchEvent(new Event(e,{bubbles:true}));}catch(_){}});}
                                    function fU(doc){
                                    return doc.querySelector('#institution-username, input[name=login], input.login[type=text]');
                                    }
                                    function fP(doc){
                                    return doc.querySelector('#institution-password, input[name=password], input[type=password]');
                                    }
                                    function clickSubmit(doc){
                                    var btn=doc.querySelector('input.button.btn.submit.primary.no-margin-bottom.accessSubmit');
                                    if(btn){btn.click();return true;}
                                    var fallback=doc.querySelector('button[type=submit], input[type=submit]');
                                    if(fallback){fallback.click();return true;}
                                    var form=doc.querySelector('form');
                                    if(form){form.submit();return true;}
                                    return false;
                                    }
                                    function tryDoc(doc){
                                    var u=fU(doc), p=fP(doc);
                                    if(u && !u.value){u.focus();u.value=USER;fire(u);}
                                    if(p && !p.value){p.focus();p.value=PASS;fire(p);}
                                    if((u||p) && p && !submitted){submitted=clickSubmit(doc);}
                                    return !!(u||p);
                                    }
                                    function tick(){
                                    var ok=tryDoc(document);
                                    document.querySelectorAll('iframe').forEach(fr=>{try{if(fr.contentDocument)ok=tryDoc(fr.contentDocument)||ok;}catch(e){}});
                                    if(Date.now()<deadline && !submitted){setTimeout(tick,300);}else if(!ok){alert('Wiley username/password fields not found. Make sure you are on the Wiley login page.');}
                                    }
                                    tick();
                                })();">
                        📚 Wiley Auto Login
                    </a>


                    <p class="small text-muted mt-1">To install: drag this button to your bookmarks bar.</p>
                </div>
                <div class="modal-footer">
                    <a href="https://onlinelibrary.wiley.com/action/ssostart?redirectUri=%2F#" target="_blank" class="btn btn-success">Proceed to Wiley Login Page</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- GALE Instructions Modal -->
    <div class="modal fade" id="galeModal" tabindex="-1" aria-labelledby="galeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-light">
                <div class="modal-header">
                    <h5 class="modal-title" id="galeModalLabel">GALE Access</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ol>
                        <li>Click this link: <a href="https://l.facebook.com/l.php?u=https%3A%2F%2Fbit.ly%2FLC-GaleDatabase%3Ffbclid%3DIwZXh0bgNhZW0CMTAAYnJpZBExSVdkczA0VXhzbDcyOXo0dAEenq4sfyObtsfMJsl8YLOWOs01YYucU1_4kh050yerk9h5OM7GgnWyb64nbOo_aem_3Jq5Mkjs8CqIhyLIFFo3LQ&h=AT3EN7SnlMk-nuwNWcqbLnDgaDmb9zaEL5I1AwDKTHnAEn182QAbRbA4G3cggSWhkjgGEltLZWrmS_4Wv9GCz817nnNGxTSq1FAr5WjT5W16jhEy51bmtR3vcQGp-P6wx_txgGGe4QpASFFJ&__tn__=-UK-R&c[0]=AT0g4ibse3_TS5OCr7V8w7m4y7saiPzoXJ_L8861HAPPcrNbadMhSW9Ra78bxT9KHvoSSfrL0yW5BOsGDUkZggQ97UgbEwynZXS3TzdujHgCJJSSYMDBxsZ01Z88tlEkwrR5Vacy18D4gBU969zRhm7vam8yClFfupO8ZxAlTD2rSoPaCrIruemoRE2pdJEeZ9G-CDE6u_zHgIWjUQj6whzE" target="_blank">Open GALE</a></li>
                        <li>If prompted, enter the password below, or use the bookmarklet to auto-fill it.</li>
                    </ol>

                    <div class="bg-white p-3 rounded border mb-3">
                        <div class="d-flex align-items-center">
                            <strong class="me-2">Password:</strong>
                            <code id="galePassword">discover</code>
                            <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('galePassword')">Copy</button>
                        </div>
                    </div>

                    <p class="small mb-1"><strong>Optional:</strong> Drag this button to your bookmarks bar for 1-click GALE login:</p>
                    <a class="btn btn-sm btn-warning"
                        href="javascript:(function(){var passField=document.querySelector('input[type=%22password%22], input[name=%22password%22]');if(passField){passField.value='discover';passField.dispatchEvent(new Event('input',{bubbles:true}));var form=passField.closest('form');if(form){form.submit();}else{var btn=document.querySelector('button[type=%22submit%22], input[type=%22submit%22]');if(btn)btn.click();}}else{alert('Password field not found. Make sure you are on the GALE login page.');}})();">
                        📚 GALE Auto Login
                    </a>
                    <p class="small text-muted mt-1">To install: drag this button to your bookmarks bar.</p>
                </div>
                <div class="modal-footer">
                    <a href="https://l.facebook.com/l.php?u=https%3A%2F%2Fbit.ly%2FLC-GaleDatabase%3Ffbclid%3DIwZXh0bgNhZW0CMTAAYnJpZBExSVdkczA0VXhzbDcyOXo0dAEenq4sfyObtsfMJsl8YLOWOs01YYucU1_4kh050yerk9h5OM7GgnWyb64nbOo_aem_3Jq5Mkjs8CqIhyLIFFo3LQ&h=AT3EN7SnlMk-nuwNWcqbLnDgaDmb9zaEL5I1AwDKTHnAEn182QAbRbA4G3cggSWhkjgGEltLZWrmS_4Wv9GCz817nnNGxTSq1FAr5WjT5W16jhEy51bmtR3vcQGp-P6wx_txgGGe4QpASFFJ&__tn__=-UK-R&c[0]=AT0g4ibse3_TS5OCr7V8w7m4y7saiPzoXJ_L8861HAPPcrNbadMhSW9Ra78bxT9KHvoSSfrL0yW5BOsGDUkZggQ97UgbEwynZXS3TzdujHgCJJSSYMDBxsZ01Z88tlEkwrR5Vacy18D4gBU969zRhm7vam8yClFfupO8ZxAlTD2rSoPaCrIruemoRE2pdJEeZ9G-CDE6u_zHgIWjUQj6whzE" target="_blank" class="btn btn-success">Proceed to GALE</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- PROQUEST Instructions Modal -->
    <div class="modal fade" id="proquestModal" tabindex="-1" aria-labelledby="proquestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-light">
                <div class="modal-header">
                    <h5 class="modal-title" id="proquestModalLabel">ProQuest Access</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ol>
                        <li>Click this link: <a href="https://www.proquest.com/login" target="_blank">Open ProQuest Login Page</a></li>
                        <li>Use the credentials below to log in.</li>
                    </ol>

                    <div class="bg-white p-3 rounded border">
                        <div class="d-flex align-items-center mb-2">
                            <strong class="me-2">Username:</strong>
                            <code id="proquestUsername">lcpqreslib</code>
                            <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('proquestUsername')">Copy</button>
                        </div>
                        <div class="d-flex align-items-center">
                            <strong class="me-2">Password:</strong>
                            <code id="proquestPassword">LCPQ#2023</code>
                            <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('proquestPassword')">Copy</button>
                        </div>
                    </div>

                    <p class="small mt-3"><strong>Optional:</strong> Drag this bookmarklet to auto-fill and submit:</p>
                    <a class="btn btn-sm btn-warning"
                        href="javascript:(function(){
          var USER='lcpqreslib', PASS='LCPQ#2023', submitted=false, deadline=Date.now()+15000;
          function fire(el){['input','change','keyup','blur'].forEach(e=>{try{el.dispatchEvent(new Event(e,{bubbles:true}));}catch(_){}});}
          function fU(doc){return doc.querySelector('#username, input[name=\'username\'], input[aria-labelledby=\'username-label\']');}
          function fP(doc){return doc.querySelector('#password, input[name=\'password\']');}
          function clickSubmit(doc){
            var btn=doc.querySelector('button[type=\'submit\'], input[type=\'submit\']');
            if(btn){btn.click();return true;}
            var form=doc.querySelector('form');
            if(form){form.submit();return true;}
            return false;
          }
          function tryDoc(doc){
            var u=fU(doc), p=fP(doc);
            if(u && u.id!=='institutionName' && !u.value){u.focus();u.value=USER;fire(u);}
            if(p && !p.value){p.focus();p.value=PASS;fire(p);}
            if((u||p) && p && !submitted){submitted=clickSubmit(doc);}
            return !!(u||p);
          }
          function tick(){
            var ok=tryDoc(document);
            document.querySelectorAll('iframe').forEach(fr=>{try{if(fr.contentDocument)ok=tryDoc(fr.contentDocument)||ok;}catch(e){}});
            if(Date.now()<deadline && !submitted){setTimeout(tick,300);}else if(!ok){alert('ProQuest username/password fields not found. Make sure you are on the correct login page.');}
          }
          tick();
        })();">
                        📚 ProQuest Auto Login
                    </a>
                    <p class="small text-muted mt-1">To install: drag this button to your bookmarks bar.</p>
                </div>
                <div class="modal-footer">
                    <a href="https://www.proquest.com/login" target="_blank" class="btn btn-success">Proceed to ProQuest</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>







    <!-- Copy Script -->
    <script>
        function copyToClipboard(elementId) {
            const text = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert('Copied: ' + text);
            }).catch(err => {
                console.error('Failed to copy text: ', err);
            });
        }
    </script>


    <style>
        /*drop down*/
        @media (min-width: 992px) {
            .navbar-nav .dropdown:hover .dropdown-menu {
                display: block;
                margin-top: 0;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



</body>

</html>