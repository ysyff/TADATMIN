<?php
$sidebar_active = '';
if (isset($_GET['sidebar'])) {
    $sidebar_active = $_GET['sidebar'];
}
?>

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-mug-hot"></i>
        </div>
        <div class="sidebar-brand-text mx-3">APRIORI <sup>Kopi</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li <?php echo ($sidebar_active == '') ? 'class="nav-item active"' : 'class="nav-item"'; ?>>
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    <hr class="sidebar-divider">
    <li <?php echo ($sidebar_active == 'data_transaksi') ? 'class="nav-item active"' : 'class="nav-item"'; ?>>
        <a class="nav-link" href="index.php?sidebar=data_transaksi">
            <i class="fas fa-fw fa-table"></i>
            <span>Data Transaksi</span></a>
    </li>
    <hr class="sidebar-divider">
    <li <?php echo ($sidebar_active == 'proses_apriori') ? 'class="nav-item active"' : 'class="nav-item"'; ?>>
        <a class="nav-link" href="index.php?sidebar=proses_apriori">
            <i class="fas fa-spinner"></i>
            <span>Proses Apriori</span></a>
    </li>
    <hr class="sidebar-divider">
    <li <?php echo ($sidebar_active == 'hasil') ? 'class="nav-item active"' : 'class="nav-item"'; ?>>
        <a class="nav-link" href="index.php?sidebar=hasil">
            <i class="fas fa-book-open"></i>
            <span>Hasil Apriori</span></a>
    </li>
    <hr class="sidebar-divider">
    <li class="nav-item">
    <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">
        <i class="fas fa-sign-out-alt"></i>
        <span>Keluar</span>
    </a>
</li>

<!-- Modal Konfirmasi Logout -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin keluar?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <a href="logout.php" class="btn btn-primary">Keluar</a>
            </div>
        </div>
    </div>
</div>


    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

    <!-- Sidebar Message -->
</ul>

<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script type="text/javascript">
    // Add the active class to the clicked sidebar item
    $('.nav-item').on('click', function() {
        $('.nav-item').removeClass('active');
        $(this).addClass('active');
    });
</script>
<!-- End of Sidebar -->

<!-- Content Wrapper -->