<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    @if (Auth::check())
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <!-- Messages Dropdown Menu -->
        <li class="nav-item">
            <button class="nav-link logout border-0 bg-transparent">
                <i class="fas fa-sign-out-alt"></i>&nbsp;Logout
            </button>
        </li>
    </ul>
    @endif
    <!-- Right navbar links -->
</nav>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
        <img src="/assets/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">SPK Tempat Wisata</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="/assets/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">Administrator</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
       with font-awesome or any other icon font library -->
                <li class="nav-header">Main</li>
                @if(!auth()->check())
                <li class="nav-item">
                    <a href="/rekomendasi" class="nav-link">
                        <i class="fas fa-columns"></i>
                        <p>&nbsp;
                            Rekomendasi
                        </p>
                    </a>
                </li>
                @endif
                @if(auth()->check())
                <li class="nav-item">
                    <a href="/" class="nav-link">
                        <i class="fas fa-columns"></i>
                        <p>&nbsp;
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-header">Master</li>
                <li class="nav-item">
                    <a href="/kriteria" class="nav-link">
                        <i class="fas fa-copyright"></i>
                        <p>&nbsp;
                            Kriteria
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/alternatif" class="nav-link">
                        <i class="fas fa-list"></i>
                        <p>&nbsp;
                            Alternatif
                        </p>
                    </a>
                </li>
                <li class="nav-header">Sistem Penunjang Keputusan (SPK)</li>
                <li class="nav-item">
                    <a href="/electre" class="nav-link">
                        <i class="fas fa-calculator"></i>
                        <p>&nbsp;
                            Perhitungan Electre
                        </p>
                    </a>
                </li>
                @endif
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<script>
    let logout = document.querySelector('.logout')
    logout.addEventListener('click', function() {
        let unique = $(this).attr("data-unique");
        let token = $(this).attr("data-token");
        Swal.fire({
            title: "Apakah Kamu Yakin?"
            , text: "Anda akan keluar!"
            , icon: "warning"
            , showCancelButton: true
            , confirmButtonColor: "#3085d6"
            , cancelButtonColor: "#d33"
            , confirmButtonText: "Yes, Keluar!"
        , }).then((result) => {
            if (result.isConfirmed) {
                document.location.href = '/logout';
            }
        });
    })

</script>
