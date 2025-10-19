{{-- Deprecated: legacy static modals retained for reference only. Do not include this file. Use resources/views/e-libraries/*.blade.php instead. --}}
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

@php
$galeLink = 'https://link.gale.com/apps/menu?userGroupName=phlccdo&amp;prodId=MENU';
@endphp





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
                    <li>
                        Click this link:
                        <a href="https://link.gale.com/apps/menu?userGroupName=phlccdo&amp;prodId=MENU"
                            target="_blank" rel="noreferrer noopener">Open GALE</a>

                    </li>
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
                <a href="https://link.gale.com/apps/menu?userGroupName=phlccdo&amp;prodId=MENU"
                    target="_blank" rel="noreferrer noopener">Open GALE</a>
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

     function fire(el){
       ['input','change','keyup','blur'].forEach(function(e){
         try{el.dispatchEvent(new Event(e,{bubbles:true}));}catch(_){}
       });
     }

     function tryDoc(doc){
       var u=doc.querySelector('#username');
       var p=doc.querySelector('#password');
       if(u && !u.value){ u.focus(); u.value=USER; fire(u); }
       if(p && !p.value){ p.focus(); p.value=PASS; fire(p); }

       var btn=doc.querySelector('#submit_1, input[type=submit], button[type=submit]');
       if(btn && !submitted){
         submitted=true;
         btn.dispatchEvent(new MouseEvent('click',{bubbles:true,cancelable:true,view:window}));
         btn.click();
         return true;
       }
       return !!(u||p);
     }

     function tick(){
       var ok=tryDoc(document);
       document.querySelectorAll('iframe').forEach(function(fr){
         try{ if(fr.contentDocument) ok=tryDoc(fr.contentDocument)||ok; }catch(e){}
       });
       if(Date.now()<deadline && !submitted){ setTimeout(tick,300); }
       else if(!ok){ alert('ProQuest login fields not found.'); }
     }

     tick();
   })();">📚 ProQuest Auto Login</a>

                <p class="small text-muted mt-1">To install: drag this button to your bookmarks bar.</p>
            </div>
            <div class="modal-footer">
                <a href="https://www.proquest.com/login" target="_blank" class="btn btn-success">Proceed to ProQuest</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>