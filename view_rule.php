<?php
session_start();
if (!isset($_SESSION['apriori_kopi_id'])) {
    header("location:index.php?sidebar=forbidden");
}

include_once "database.php";
include_once "fungsi.php";
include_once "mining.php";
include_once "display_mining.php";
?>
<div class="main-content">
    <div class="main-content-inner">
        <div class="page-content">
            <div class="page-header">
                <h1 class="text-center bold" >
                    Hasil
                </h1>
            </div><!-- /.page-header -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="widget-box">
                        <div class="widget-body">
                            <div class="widget-main">
                                <?php
                                //object database class
                                $db_object = new database();

                                $pesan_error = $pesan_success = "";
                                if (isset($_GET['pesan_error'])) {
                                    $pesan_error = $_GET['pesan_error'];
                                }
                                if (isset($_GET['pesan_success'])) {
                                    $pesan_success = $_GET['pesan_success'];
                                }

                                if (isset($_POST['submit'])) {
                                    $can_process = true;
                                    if (empty($_POST['min_support']) || empty($_POST['min_confidence'])) {
                                        $can_process = false;
                                ?>
                                        <script>
                                            location.replace("?sidebar=view_rule&pesan_error=Min Support dan Min Confidence harus diisi");
                                        </script>
                                    <?php
                                    }
                                    if (!is_numeric($_POST['min_support']) || !is_numeric($_POST['min_confidence'])) {
                                        $can_process = false;
                                    ?>
                                        <script>
                                            location.replace("?sidebar=view_rule&pesan_error=Min Support dan Min Confidence harus diisi angka");
                                        </script>
                                    <?php
                                    }

                                    if ($can_process) {
                                        $id_process = $_POST['id_process'];

                                        $tgl = explode(" - ", $_POST['range_tanggal']);
                                        $start = format_date($tgl[0]);
                                        $end = format_date($tgl[1]);

                                        echo "Min Support Absolut: " . $_POST['min_support'];
                                        echo "<br>";
                                        $sql = "SELECT COUNT(*) FROM transaksi 
                                                WHERE transaction_date BETWEEN '$start' AND '$end' ";
                                        $res = $db_object->db_query($sql);
                                        $num = $db_object->db_fetch_array($res);
                                        $minSupportRelatif = ($_POST['min_support'] / $num[0]) * 100;
                                        echo "Min Support Relatif: " . $minSupportRelatif;
                                        echo "<br>";
                                        echo "Min Confidence: " . $_POST['min_confidence'];
                                        echo "<br>";
                                        echo "Start Date: " . $_POST['range_tanggal'];
                                        echo "<br>";

                                        //delete hitungan untuk id_process
                                        reset_hitungan($db_object, $id_process);

                                        //update log process
                                        $field = array(
                                            "start_date" => $start,
                                            "end_date" => $end,
                                            "min_support" => $_POST['min_support'],
                                            "min_confidence" => $_POST['min_confidence']
                                        );
                                        $where = array(
                                            "id" => $id_process
                                        );
                                        $query = $db_object->update_record("process_log", $field, $where);

                                        $result = mining_process(
                                            $db_object,
                                            $_POST['min_support'],
                                            $_POST['min_confidence'],
                                            $start,
                                            $end,
                                            $id_process
                                        );
                                        if ($result) {
                                            display_success("Proses mining selesai");
                                        } else {
                                            display_error("Gagal mendapatkan aturan asosiasi");
                                        }

                                        display_process_hasil_mining($db_object, $id_process);
                                    }
                                } else {
                                    $id_process = 0;
                                    if (isset($_GET['id_process'])) {
                                        $id_process = $_GET['id_process'];
                                    }
                                    $sql = "SELECT
                                            conf.*, log.start_date, log.end_date
                                            FROM
                                            confidence conf, process_log log
                                            WHERE conf.id_process = '$id_process' "
                                        . " AND conf.id_process = log.id "
                                        . " AND conf.from_itemset=3 "; //. " ORDER BY conf.lolos DESC";
                                    //        echo $sql;
                                    $query = $db_object->db_query($sql);
                                    $jumlah = $db_object->db_num_rows($query);


                                    $sql1 = "SELECT
                                            conf.*, log.start_date, log.end_date
                                            FROM
                                            confidence conf, process_log log
                                            WHERE conf.id_process = '$id_process' "
                                        . " AND conf.id_process = log.id "
                                        . " AND conf.from_itemset=2 "; //. " ORDER BY conf.lolos DESC";
                                    //        echo $sql;
                                    $query1 = $db_object->db_query($sql1);
                                    $jumlah1 = $db_object->db_num_rows($query1);

                                    $sql_log = "SELECT * FROM process_log
                                                WHERE id = " . $id_process;
                                    $res_log = $db_object->db_query($sql_log);
                                    $row_log = $db_object->db_fetch_array($res_log);

                                    
                                    ?>
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Confidence dari itemset 3</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="dataTable01" width="100%" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>X => Y</th>
                                                            <th>Support XUY</th>
                                                            <th>Support X</th>
                                                            <th>Confidence</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>

                                                        <?php
                                                        $no = 1;
                                                        $data_confidence = array();
                                                        while ($row = $db_object->db_fetch_array($query)) {

                                                            echo "<tr>";
                                                            echo "<td>" . $no . "</td>";
                                                            echo "<td>" . $row['kombinasi1'] . " => " . $row['kombinasi2'] . "</td>";
                                                            echo "<td>" . price_format($row['support_xUy']) . "</td>";
                                                            echo "<td>" . price_format($row['support_x']) . "</td>";
                                                            echo "<td>" . price_format($row['confidence']) . "</td>";
                                                            $keterangan = ($row['confidence'] <= $row['min_confidence']) ? "Tidak Lolos" : "Lolos";
                                                            echo "<td>" . $keterangan . "</td>";
                                                            echo "</tr>";
                                                            $no++;
                                                            //if($row['confidence']>=$row['min_cofidence']){
                                                            if ($row['lolos'] == 1) {
                                                                $data_confidence[] = $row;
                                                            }
                                                        }
                                                        ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Confidence dari itemset 2 -->
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Confidence dari itemset 2</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>X => Y</th>
                                                            <th>Support XUY</th>
                                                            <th>Support X</th>
                                                            <th>Confidence</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>

                                                        <?php
                                                        $no = 1;
                                                        while ($row = $db_object->db_fetch_array($query1)) {
                                                            echo "<tr>";
                                                            echo "<td>" . $no . "</td>";
                                                            echo "<td>" . $row['kombinasi1'] . " => " . $row['kombinasi2'] . "</td>";
                                                            echo "<td>" . price_format($row['support_xUy']) . "</td>";
                                                            echo "<td>" . price_format($row['support_x']) . "</td>";
                                                            echo "<td>" . price_format($row['confidence']) . "</td>";
                                                            $keterangan = ($row['confidence'] <= $row['min_confidence']) ? "Tidak Lolos" : "Lolos";
                                                            echo "<td>" . $keterangan . "</td>";
                                                            echo "</tr>";
                                                            $no++;
                                                            //if($row['confidence']>=$row['min_cofidence']){
                                                            if ($row['lolos'] == 1) {
                                                                $data_confidence[] = $row;
                                                            }
                                                        }
                                                        ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Rule Assosiasi</h6>
                                        </div>
                                        <div class="card-body">

                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>X => Y</th>
                                                            <th>Confidence</th>
                                                            <th>Nilai Uji Lift</th>
                                                            <th>Korelasi Rule</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $no = 1;
                                                        //while($row=$db_object->db_fetch_array($query)){
                                                        foreach ($data_confidence as $key => $val) {
                                                            if ($no == 1) {
                                                                echo "<br>";
                                                                echo "Min support: " . $val['min_support'];
                                                                echo "<br>";
                                                                echo "Min confidence: " . $val['min_confidence'];
                                                                echo "<br>";
                                                                echo "Start Date: " . format_date_db($val['start_date']);
                                                                echo "<br>";
                                                                echo "End Date: " . format_date_db($val['end_date']);
                                                            }
                                                            echo "<tr>";
                                                            echo "<td>" . $no . "</td>";
                                                            echo "<td>" . $val['kombinasi1'] . " => " . $val['kombinasi2'] . "</td>";
                                                            echo "<td>" . price_format($val['confidence']) . "</td>";
                                                            echo "<td>" . price_format($val['nilai_uji_lift']) . "</td>";
                                                            echo "<td>" . ($val['korelasi_rule']) . "</td>";
                                                            //echo "<td>" . ($val['lolos'] == 1 ? "Lolos" : "Tidak Lolos") . "</td>";
                                                            echo "</tr>";
                                                            $no++;
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Hasil</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="dataTable2" width="100%" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Hasil Analisa</th>
                                                        </tr>
                                                        <a href="export/CLP.php?id_process=<?php echo $id_process; ?>" class="btn btn-outline-secondary mb-3" target="blank"><i class="fas fa-file-pdf mr-2"></i>Print</a>
                                                    </thead>

                                                    <tbody>

                                                        <?php
                                                        $no = 1;
                                                        //while($row=$db_object->db_fetch_array($query)){
                                                        foreach ($data_confidence as $key => $val) {
                                                            if ($val['lolos'] == 1) {
                                                                echo "<tr>";
                                                                echo "<td>" . $no . ".</td>";
                                                                echo "<td> Jika konsumen membeli " . $val['kombinasi1']
                                                                    . ", maka konsumen juga akan membeli " . $val['kombinasi2'] . "</td>";
                                                                echo "</tr>";
                                                            }
                                                            $no++;
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    //query itemset 1
                                    $sql1 = "SELECT
                    *
                    FROM
                     itemset1 
                    WHERE id_process = '$id_process' "
                                        . " ORDER BY lolos DESC";
                                    $query1 = $db_object->db_query($sql1);
                                    $jumlah1 = $db_object->db_num_rows($query1);
                                    $itemset1 = $jumlahItemset1 = $supportItemset1 = array();
                                    ?>
                                    <hr>
                                    <h3>Perhitungan</h3>
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Perhitungan Itemset 1 :</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="dataTable3" width="100%" cellspacing="0">
                                                    </tr>

                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Item 1</th>
                                                            <th>Jumlah</th>
                                                            <th>Support</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php
                                                        $no = 1;
                                                        while ($row1 = $db_object->db_fetch_array($query1)) {
                                                            echo "<tr>";
                                                            echo "<td>" . $no . "</td>";
                                                            echo "<td>" . $row1['atribut'] . "</td>";
                                                            echo "<td>" . $row1['jumlah'] . "</td>";
                                                            echo "<td>" . price_format($row1['support']) . "</td>";
                                                            echo "<td>" . ($row1['lolos'] == 1 ? "Lolos" : "Tidak Lolos") . "</td>";
                                                            echo "</tr>";
                                                            $no++;
                                                            if ($row1['lolos'] == 1) {
                                                                $itemset1[] = $row1['atribut']; //item yg lolos itemset1
                                                                $jumlahItemset1[] = $row1['jumlah'];
                                                                $supportItemset1[] = price_format($row1['support']);
                                                            }
                                                        }
                                                        ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    echo "<div class='card shadow mb-4'>";
                                    echo "<div class='card-header py-3'>";
                                    echo "<h6 class='m-0 font-weight-bold text-primary'> Itemset 1 Yang Lolos:</h6>";
                                    echo "</div>";
                                    echo "<div class='card-body'>";
                                    echo "<div class='table-responsive'>";
                                    echo "<table class='table table-bordered' id='dataTable4' width='100%' cellspacing='0'>
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Item</th>
                                                <th>Jumlah</th>
                                                <th>Support X</th>
                                            </tr>
                                        </thead>";

                                    echo "<tbody>";

                                    $no = 1;
                                    foreach ($itemset1 as $key => $value) {
                                        echo "<tr>";
                                        echo "<td>" . $no . "</td>";
                                        echo "<td>" . $value . "</td>";
                                        echo "<td>" . $jumlahItemset1[$key] . "</td>";
                                        echo "<td>" . $supportItemset1[$key] . "</td>";
                                        echo "</tr>";
                                        $no++;
                                    }
                                    echo "</tbody>";
                                    echo "</table>";
                                    echo "</div>";
                                    echo "</div>";
                                    echo " </div>";
                                    ?>


                                    <?php
                                    //query itemset 2
                                    $sql2 = "SELECT
                    *
                    FROM
                     itemset2 
                    WHERE id_process = '$id_process' "
                                        . " ORDER BY lolos DESC";
                                    $query2 = $db_object->db_query($sql2);
                                    $jumlah2 = $db_object->db_num_rows($query2);
                                    $itemset2_var1 = $itemset2_var2 = $jumlahItemset2 = $supportItemset2 = array();
                                    ?>
                                    <hr>
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Perhitungan Itemset 2:</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="dataTable5" width="100%" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>item 1</th>
                                                            <th>item 2</th>
                                                            <th>Jumlah</th>
                                                            <th>Support </th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php
                                                        $no = 1;
                                                        while ($row2 = $db_object->db_fetch_array($query2)) {
                                                            echo "<tr>";
                                                            echo "<td>" . $no . "</td>";
                                                            echo "<td>" . $row2['atribut1'] . "</td>";
                                                            echo "<td>" . $row2['atribut2'] . "</td>";
                                                            echo "<td>" . $row2['jumlah'] . "</td>";
                                                            echo "<td>" . price_format($row2['support']) . "</td>";
                                                            echo "<td>" . ($row2['lolos'] == 1 ? "Lolos" : "Tidak Lolos") . "</td>";
                                                            echo "</tr>";
                                                            $no++;
                                                            if ($row2['lolos'] == 1) {
                                                                $itemset2_var1[] = $row2['atribut1'];
                                                                $itemset2_var2[] = $row2['atribut2'];
                                                                $jumlahItemset2[] = $row2['jumlah'];
                                                                $supportItemset2[] = price_format($row2['support']);
                                                            }
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    echo "<div class='card shadow mb-4'>";
                                    echo "<div class='card-header py-3'>";
                                    echo "<h6 class='m-0 font-weight-bold text-primary'>Itemset 2 yang lolos :</h6>";
                                    echo "</div>";
                                    echo "<div class='card-body'>";
                                    echo "<div class='table-responsive'>";
                                    echo "<table class='table table-bordered' id='dataTable6' width='100%' cellspacing='0'>";
                                    echo "<thead>";
                                    echo "<tr>";
                                    echo "<th>No</th>";
                                    echo "<th>Item 1</th>";
                                    echo "<th>Item 2</th>";
                                    echo "<th>Jumlah</th>";
                                    echo "<th>Support</th>";
                                    echo "</tr>";
                                    echo "</thead>";


                                    echo "<tbody>";
                                    $no = 1;
                                    foreach ($itemset2_var1 as $key => $value) {
                                        echo "<tr>";
                                        echo "<td>" . $no . "</td>";
                                        echo "<td>" . $value . "</td>";
                                        echo "<td>" . $itemset2_var2[$key] . "</td>";
                                        echo "<td>" . $jumlahItemset2[$key] . "</td>";
                                        echo "<td>" . $supportItemset2[$key] . "</td>";
                                        echo "</tr>";
                                        $no++;
                                    }

                                    echo "</tbody>";
                                    echo "</table>";
                                    echo "</div>";
                                    echo "</div>";
                                    echo "</div>";

                                    ?>

                                    <?php
                                    //query itemset 3
                                    $sql3 = "SELECT
                    *
                    FROM
                     itemset3 
                    WHERE id_process = '$id_process' "
                                        . " ORDER BY lolos DESC";
                                    $query3 = $db_object->db_query($sql3);
                                    $jumlah3 = $db_object->db_num_rows($query3);
                                    $itemset3_var1 = $itemset3_var2 = $itemset3_var3 = $jumlahItemset3 = $supportItemset3 = array();
                                    ?>
                                    <hr>
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Perhitungan Itemset 3:</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="dataTable7" width="100%" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Item 1</th>
                                                            <th>Item 2</th>
                                                            <th>Item 3</th>
                                                            <th>Jumlah</th>
                                                            <th>Support</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php
                                                        $no = 1;
                                                        while ($row3 = $db_object->db_fetch_array($query3)) {
                                                            echo "<tr>";
                                                            echo "<td>" . $no . "</td>";
                                                            echo "<td>" . $row3['atribut1'] . "</td>";
                                                            echo "<td>" . $row3['atribut2'] . "</td>";
                                                            echo "<td>" . $row3['atribut3'] . "</td>";
                                                            echo "<td>" . $row3['jumlah'] . "</td>";
                                                            echo "<td>" . price_format($row3['support']) . "</td>";
                                                            echo "<td>" . ($row3['lolos'] == 1 ? "Lolos" : "Tidak Lolos") . "</td>";
                                                            echo "</tr>";
                                                            $no++;
                                                            if ($row3['lolos'] == 1) {
                                                                $itemset3_var1[] = $row3['atribut1'];
                                                                $itemset3_var2[] = $row3['atribut2'];
                                                                $itemset3_var3[] = $row3['atribut3'];
                                                                $jumlahItemset3[] = $row3['jumlah'];
                                                                $supportItemset3[] = $row3['support'];
                                                            }
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <?php

                                    echo "<div class='card shadow mb-4'>";
                                    echo "<div class='card-header py-3'>";
                                    echo "<h6 class='m-0 font-weight-bold text-primary'>Itemset 3 yang lolos :</h6>";
                                    echo "</div>";
                                    echo "<div class='card-body'>";
                                    echo "<div class='table-responsive'>";
                                    echo "<table class='table table-bordered' id='dataTable8' width='100%' cellspacing='0'>";
                                    echo "<thead>";
                                    echo "<tr>";
                                    echo "<th>No</th>";
                                    echo "<th>Item 1</th>";
                                    echo "<th>Item 2</th>";
                                    echo "<th>Item 3</th>";
                                    echo "<th>Jumlah</th>";
                                    echo "<th>Support</th>";
                                    echo "</tr>";
                                    echo "</thead>";


                                    echo "<tbody>";


                                    $no = 1;
                                    foreach ($itemset3_var1 as $key => $value) {
                                        echo "<tr>";
                                        echo "<td>" . $no . "</td>";
                                        echo "<td>" . $value . "</td>";
                                        echo "<td>" . $itemset3_var2[$key] . "</td>";
                                        echo "<td>" . $itemset3_var3[$key] . "</td>";
                                        echo "<td>" . $jumlahItemset3[$key] . "</td>";
                                        echo "<td>" . $supportItemset3[$key] . "</td>";
                                        echo "</tr>";
                                        $no++;
                                    }

                                    echo "</tbody>";
                                    echo "</table>";

                                    echo "</div>";
                                    echo "</div>";
                                    echo "</div>";
                                    ?>


                                    <?php
                                    //}
                                    ?>

                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    $('#dataTable').DataTable();
                    $('#dataTable01').DataTable();
                    $('#dataTable1').DataTable();
                    $('#dataTable2').DataTable();
                    $('#dataTable3').DataTable();
                    $('#dataTable4').DataTable();
                    $('#dataTable5').DataTable();
                    $('#dataTable6').DataTable();
                    $('#dataTable7').DataTable();
                    $('#dataTable8').DataTable();

                });
            </script>
            <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

            <!-- Tautan ke DataTables -->
            <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>