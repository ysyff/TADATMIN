<!-- Topbar -->
<?php include_once 'database.php'; ?>
<!-- End of Topbar -->

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Dashboard Page</h1>
    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Process Log
                            </div>
                            <?php
                            $db_object = new database(); // Buat objek database
                            $sql = "SELECT COUNT(*) AS total FROM process_log";
                            $query = $db_object->db_query($sql);
                            $result = $db_object->db_fetch_array($query);
                            $total = $result['total'];
                            ?>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Transactions
                            </div>
                            <?php
                            $sql = "SELECT COUNT(*) AS total FROM transaksi"; // Menghitung jumlah data di tabel "transaksi"
                            $query = $db_object->db_query($sql);
                            $result = $db_object->db_fetch_array($query);
                            $totalTransactions = $result['total'];
                            ?>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalTransactions; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->