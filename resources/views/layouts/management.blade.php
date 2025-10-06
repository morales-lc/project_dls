<!doctype html>
<html lang="en">
<head>
    @include('layouts.management-head')
</head>
<body class="bg-light" style="min-height:50vh;">
    @if(auth()->check() && auth()->user()->role === 'librarian')
        @include('librarian.components.librarian-topnav')
        @include('librarian.components.librarian-sidebar')
        <div id="managementMain" class="flex-grow-1">
            <main class="py-4">
                <div class="container-fluid px-4">
                    @yield('content')
                </div>
            </main>
        </div>
    @else
        @include('components.admin-topnav')
        @include('components.admin-sidebar')
        <div id="managementMain" class="flex-grow-1">
            <main class="py-4">
                <div class="container-fluid px-4">
                    @yield('content')
                </div>
            </main>
        </div>
    @endif

    @include('layouts.management-scripts')
</body>
</html>
