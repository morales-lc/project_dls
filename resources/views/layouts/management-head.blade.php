    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Management')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    @stack('management-head')
    <style>
        :root { --management-topnav-height: 72px; }
        /* reasonable spacing between topnav and content */
        #managementMain > main .container-fluid { padding-top: 0rem; }
          /* don't add padding to body (causes white gap above sticky topnav).
              Reserve the top space for management pages on the main content container instead. */
          /* body { padding-top: var(--management-topnav-height); } */

        /* Sidebars are fixed under the topnav */
        .sidebar, #sidebar, #librarianSidebar {
            position: fixed;
            top: var(--management-topnav-height);
            left: 0;
            height: calc(100vh - var(--management-topnav-height));
            width: 260px;
            transition: width .2s ease, transform .2s ease;
            z-index: 1020;
        }
        .sidebar.sidebar-collapsed, #sidebar.sidebar-collapsed, #librarianSidebar.sidebar-collapsed {
            width: 0px !important;
            min-width: 0px !important;
            max-width: 0px !important;
            overflow: visible;
        }
        /* ensure base sidebar allows being overridden from inline styles */
        .sidebar, #sidebar, #librarianSidebar { min-width: 0 !important; }
          /* avoid overriding nav-link color here; color is defined in the theme css (admin-dashboard.css)
              so we don't accidentally make text invisible on a white background */

    /* main content shifts right to make space for sidebar on large screens */
    /* keep padding-top on the inner container to control spacing; remove extra padding here */
    #managementMain { transition: margin-left .2s ease; margin-left: 260px; padding-top: 0; }
        @media (max-width: 991px) {
            /* mobile: main content occupies full width, sidebars hidden by default */
            #managementMain { margin-left: 0; }
            .sidebar, #sidebar, #librarianSidebar { transform: translateX(-100%); }
        }
    </style>
