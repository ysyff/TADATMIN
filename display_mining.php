<?php

function display_process_hasil_mining($db_object, $id_process)
{
    //    
?>



    <?php
    $sql1 = "SELECT * FROM confidence "
        . " WHERE id_process = " . $id_process
        . " AND from_itemset=3 "; //. " ORDER BY lolos DESC";
    $query1 = $db_object->db_query($sql1);
    ?>
    <div class="container-fluid mt-3">
        <!-- Page Heading -->
        <p class="mb-4"> </p>
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Confidence dari itemset 3</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable9" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>X => Y</th>
                                <th>Support X U Y</th>
                                <th>Support X </th>
                                <th>Confidence</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $data_confidence = array();
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
    </div>


    <?php
    $sql1 = "SELECT * FROM confidence "
        . " WHERE id_process = " . $id_process
        . " AND from_itemset=2 "; //. " ORDER BY lolos DESC";
    $query1 = $db_object->db_query($sql1);
    ?>

    <div class="container-fluid mt-3    ">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800"></h1>
        <p class="mb-4"> </p>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">

            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">confidence dari itemset 2</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">

                    <table class="table table-bordered" id="dataTable10" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>X => Y</th>
                                <th>Support X U Y</th>
                                <th>Support X </th>
                                <th>Confidence</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php
                            $no = 1;
                            //$data_confidence = array();
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

    </div>
    <div class="container-fluid mt-3    ">

        <p class="mb-4"> </p>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">

            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Rule Assosiasi yang terbentuk</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">

                    <table class="table table-bordered" id="dataTable11" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>X => Y</th>
                                <th>Confidence</th>
                                <th>Nilai Uji lift</th>
                                <th>Korelasi rule</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php

                            $no = 1;
                            foreach ($data_confidence as $key => $val) {
                                echo "<tr>";
                                echo "<td>" . $no . "</td>";
                                echo "<td>" . $val['kombinasi1'] . " => " . $val['kombinasi2'] . "</td>";
                                echo "<td>" . price_format($val['confidence']) . "</td>";
                                echo "<td>" . price_format($val['nilai_uji_lift']) . "</td>";
                                echo "<td>" . ($val['korelasi_rule']) . "</td>";
                                echo "</tr>";
                                $no++;
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <script>
        $(document).ready(function() {
            $('#dataTable9').DataTable();
            $('#dataTable10').DataTable();
            $('#dataTable11').DataTable();

        });
    </script>
    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>

    <!-- Tautan ke DataTables -->
    <script src='https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js'></script>
<?php
}
?>