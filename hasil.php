    <?php
    //session_start();

    //session_start();
    if (!isset($_SESSION['apriori_toko_id'])) {
        header("location:index.php?sidebar=forbidden");
    }


    include_once "database.php";
    include_once "mining.php";
    ?>
    <?php
    if (isset($_GET['hapus'])) {
        $id_process = $_GET['hapus'];

        // Hapus data dari tabel process_log berdasarkan id_process
        $sql_delete = "DELETE FROM process_log WHERE id = $id_process";
        $delete_result = $db_object->db_query($sql_delete);

        if ($delete_result) {
            // Redirect ke halaman yang sama setelah menghapus data
            header("Location: index.php?pesan_success=Data berhasil dihapus");
            exit();
        } else {
            // Jika terjadi kesalahan saat menghapus data
            $pesan_error = "Terjadi kesalahan saat menghapus data";
        }
    }
    ?>

    <div class="container-fluid mt-3    ">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Hasil Proses Apriori</h1>
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

        $sql = "SELECT
        *
        FROM
         process_log ";
        $query = $db_object->db_query($sql);
        $jumlah = $db_object->db_num_rows($query);
        ?>


        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Rekaman Hasil Proses Apriori</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <?php
                    if (!empty($pesan_error)) {
                        display_error($pesan_error);
                    }
                    if (!empty($pesan_success)) {
                        display_success($pesan_success);
                    }


                    //echo "Jumlah data: ".$jumlah."<br>";
                    if ($jumlah == 0) {
                        echo "Data kosong...";
                    } else {
                    ?>
                        <table class="table table-bordered" id="dataTable10" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mulai Tanggal</th>
                                    <th>Sampai Tanggal</th>
                                    <th>Support</th>
                                    <th>Min Confidence</th>
                                    <th>Rule Yang Terbentuk</th>
                                    <th>Hasil</th>
                                </tr>
                            </thead>

                            <tbody>



                                <?php
                                $no = 1;
                                while ($row = $db_object->db_fetch_array($query)) {
                                    echo "<tr>";
                                    echo "<td>" . $no . "</td>";
                                    echo "<td>" . format_date2($row['start_date']) . "</td>";
                                    echo "<td>" . format_date2($row['end_date']) . "</td>";
                                    echo "<td>" . $row['min_support'] . "</td>";
                                    echo "<td>" . $row['min_confidence'] . "</td>";

                                    $view = "<a href='index.php?sidebar=view_rule&id_process=" . $row['id'] . "'><i class='fas fa-eye'></i> lihat rule</a>";

                                    echo "<td>" . $view . "</td>";
                                    echo "<td>";

                                    echo "<a href='export/CLP.php?id_process=" . $row['id'] . "' class='btn btn-outline-primary' target='blank'>
                                    <i class='far fa-file-pdf mr-2'></i> Download
                                </a>";
                                    echo "</td>";

                                    //                           
                                    $no++;
                                }
                                ?>





                            </tbody>
                        </table>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>
    <script>
        $(document).ready(function() {
            $('#dataTable10').DataTable();

        });
    </script>
    <!-- Tautan ke jQuery -->
   