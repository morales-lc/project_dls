    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // small helper to toggle admin/librarian sidebar (desktop collapse vs mobile slide)
        (function(){
            var adminSidebar = document.getElementById('sidebar');
            var librarianSidebar = document.getElementById('librarianSidebar');
            var main = document.getElementById('managementMain') || document.querySelector('.flex-grow-1');

            function collapseSidebar(sideEl) {
                if(!sideEl) return;
                sideEl.classList.toggle('sidebar-collapsed');
                if(sideEl.classList.contains('sidebar-collapsed')) {
                    // collapsed: narrow width
                    sideEl.style.width = '0px';
                    sideEl.style.minWidth = '0px';
                    sideEl.style.maxWidth = '0px';
                    if(main) main.style.marginLeft = '0px';
                } else {
                    sideEl.style.width = '260px';
                    sideEl.style.minWidth = '260px';
                    sideEl.style.maxWidth = '260px';
                    if(main) main.style.marginLeft = '260px';
                }
            }

            function slideToggleSidebar(sideEl) {
                if(!sideEl) return;
                if(sideEl.style.transform === 'translateX(0px)' || sideEl.style.transform === 'translateX(0)') {
                    sideEl.style.transform = 'translateX(-100%)';
                    if(main && window.innerWidth >= 992) main.style.marginLeft = '0';
                } else {
                    sideEl.style.transform = 'translateX(0)';
                    if(main && window.innerWidth >= 992) main.style.marginLeft = '260px';
                }
            }

            document.addEventListener('click', function(e){
                var t = e.target;
                // admin topnav toggle (desktop collapse / mobile slide)
                if(t && (t.id === 'sidebarToggle' || t.closest('#sidebarToggle') || t.id === 'sidebarToggleTop' || t.closest('#sidebarToggleTop'))) {
                    if(window.innerWidth >= 992) {
                        collapseSidebar(adminSidebar);
                    } else {
                        slideToggleSidebar(adminSidebar);
                    }
                }

                // librarian topnav toggle button
                if(t && (t.id === 'sidebarToggleBtnTop' || t.closest('#sidebarToggleBtnTop'))) {
                    if(window.innerWidth >= 992) {
                        collapseSidebar(librarianSidebar);
                    } else {
                        slideToggleSidebar(librarianSidebar);
                    }
                }

                // sidebar bottom toggles (in case used)
                if(t && (t.id === 'sidebarToggleBottom' || t.closest('#sidebarToggleBottom'))) {
                    // determine which sidebar this belongs to
                    var parent = t.closest('#librarianSidebar') ? librarianSidebar : adminSidebar;
                    if(window.innerWidth >= 992) collapseSidebar(parent); else slideToggleSidebar(parent);
                }
            });

            // Ensure on resize we reset transforms and margins
            window.addEventListener('resize', function(){
                if(window.innerWidth >= 992) {
                    if(adminSidebar) { adminSidebar.style.transform = 'translateX(0)'; adminSidebar.style.width = adminSidebar.classList.contains('sidebar-collapsed') ? '64px' : '260px'; }
                    if(librarianSidebar) { librarianSidebar.style.transform = 'translateX(0)'; librarianSidebar.style.width = librarianSidebar.classList.contains('sidebar-collapsed') ? '64px' : '260px'; }
                    if(main) main.style.marginLeft = ( (adminSidebar && adminSidebar.classList.contains('sidebar-collapsed')) || (librarianSidebar && librarianSidebar.classList.contains('sidebar-collapsed')) ) ? '64px' : '260px';
                } else {
                    // mobile: hide sidebars by default
                    if(adminSidebar) adminSidebar.style.transform = 'translateX(-100%)';
                    if(librarianSidebar) librarianSidebar.style.transform = 'translateX(-100%)';
                    if(main) main.style.marginLeft = '0';
                }
            });

            // initial setup
            document.addEventListener('DOMContentLoaded', function(){
                if(window.innerWidth < 992) {
                    if(adminSidebar) adminSidebar.style.transform = 'translateX(-100%)';
                    if(librarianSidebar) librarianSidebar.style.transform = 'translateX(-100%)';
                } else {
                    if(adminSidebar) adminSidebar.style.transform = 'translateX(0)';
                    if(librarianSidebar) librarianSidebar.style.transform = 'translateX(0)';
                }

                // Ensure mouse wheel always scrolls the visible sidebar when hovered
                try {
                    var ensureSidebarWheelScroll = function(sideEl){
                        if(!sideEl) return;
                        sideEl.addEventListener('wheel', function(e){
                            // Only handle when pointer is over the sidebar
                            if(!sideEl.matches(':hover')) return;
                            var delta = e.deltaY;
                            var prev = sideEl.scrollTop;
                            sideEl.scrollTop += delta;
                            // If the sidebar can scroll, prevent page scroll
                            if(sideEl.scrollTop !== prev) {
                                e.preventDefault();
                                e.stopPropagation();
                            }
                        }, { passive: false });
                    };
                    ensureSidebarWheelScroll(adminSidebar);
                    ensureSidebarWheelScroll(librarianSidebar);
                } catch(err) {
                    // no-op: older browsers
                }
            });
        })();
    </script>
    @stack('management-scripts')
