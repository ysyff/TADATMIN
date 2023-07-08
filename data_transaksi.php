<?php
//session_start();
if (!isset($_SESSION['apriori_kopi_id'])) {
    header("location:index.php?sidebar=forbidden");
}

include_once "database.php";
include_once "import/excel_reader2.php";
?>
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
    // if(!$input_error){
    $data = new Spreadsheet_Excel_Reader($_FILES['file_data_transaksi']['tmp_name']);

    $baris = $data->rowcount($sheet_index = 0);
    $column = $data->colcount($sheet_index = 0);
    //import data excel dari baris kedua, karena baris pertama adalah nama kolom
    for ($i = 2; $i <= $baris; $i++) {
        for ($c = 1; $c <= $column; $c++) {
            $value[$c] = $data->val($i, $c);
        }


        $table = "transaksi";
        $temp_date = format_date($value[1]);
        $produkIn = $value[2];

        //mencegah ada jarak spasi
        $produkIn = str_replace(" ,", ",", $produkIn);
        $produkIn = str_replace("  ,", ",", $produkIn);
        $produkIn = str_replace("   ,", ",", $produkIn);
        $produkIn = str_replace("    ,", ",", $produkIn);
        $produkIn = str_replace(", ", ",", $produkIn);
        $produkIn = str_replace(",  ", ",", $produkIn);
        $produkIn = str_replace(",   ", ",", $produkIn);
        $produkIn = str_replace(",    ", ",", $produkIn);
        //$item1 = explode(",", $produkIn);



        $sql = "INSERT INTO transaksi (transaction_date, produk) VALUES ";
        $value_in = array();

        $sql .= " ('$temp_date', '$produkIn')";
        $db_object->db_query($sql);
    }
?>
    <script>
        location.replace("?sidebar=data_transaksi&pesan_success=Data berhasil disimpan");
    </script>
<?php
}

if (isset($_POST['delete'])) {
    $sql = "TRUNCATE transaksi";
    $db_object->db_query($sql);
?>
    <script>
        location.replace("?sidebar=data_transaksi&pesan_success=Data transaksi berhasil dihapus");
    </script>
<?php
}

$sql = "SELECT
        *
        FROM
         transaksi";
$query = $db_object->db_query($sql);
$jumlah = $db_object->db_num_rows($query);
?>
<div class="accordion" id="accordionExample">
    <div class="card">
        <div class="card-header" id="headingOne">
            <h2 class="mb-0">
                <button class="btn btn-info btn-block text-center    " type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Input Data Penjualan
                </button>
            </h2>
        </div>

        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
            <div class="card-header">
                <form method="post" enctype="multipart/form-data" class="ml-5">
                    <div class="form-row">
                        <div class="custom-file col-md-10 ml-5">
                            <input type="file" class="custom-file-input" id="id-input-file-2" name="file_data_transaksi">
                            <script>
                                document.getElementById('id-input-file-2').addEventListener('change', function(event) {
                                    var fileName = event.target.files[0].name;
                                    var label = document.querySelector('.custom-file-label');
                                    label.textContent = fileName;
                                });

                                function validateForm() {
                                    var fileInput = document.getElementById("id-input-file-2");
                                    if (fileInput.files.length === 0) {
                                        showAlert("Please select a file.");
                                        return false;
                                    }

                                    var allowedExtensions = /(\.xls)$/i;
                                    var fileName = fileInput.files[0].name;
                                    if (!allowedExtensions.test(fileName)) {
                                        showAlert("Invalid file format. Please select a file with the .xls extension.");
                                        return false;
                                    }

                                    return true;
                                }

                                function showAlert(message) {
                                    var modalHtml = `
                                        <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="alertModalLabel">Alert</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>${message}</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;

                                    $(modalHtml).modal('show');

                                }
                            </script>
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                    </div>
                    <div class="form-row">
                        <button name="submit" class="btn btn-primary mt-2 col-md-5 ml-5" type="submit" onclick="return validateForm()">Submit form</button>
                        <button class="btn btn-danger mt-2 col-md-5 ml-2" type="submit" onclick="return showDeleteConfirmation()">Delete all data</button>
                        <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteConfirmationModalLabel">Delete Confirmation
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete all data?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" name="delete" class="btn btn-primary">Delete</button>
                                        <script>
                                            function showDeleteConfirmation() {
                                                $('#deleteConfirmationModal').modal('show');
                                                return false;
                                            }
                                        </script>
                                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




<!-- Begin Page Content -->
<div class="container-fluid mt-3    ">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Data Penjualan</h1>


    <!-- DataTales Example -->
    <div class="card shadow mb-4">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Transaksi Penjualan</h6>
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

                echo "Jumlah data: " . $jumlah . "<br>";
                if ($jumlah == 0) {
                    echo "Data kosong...";
                } else {
                ?>
                    <table class="table table-bordered" id="dataTable9" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Produk</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php
                            $no = 1;
                            while ($row = $db_object->db_fetch_array($query)) {
                                echo "<tr>";
                                echo "<td>" . $no . "</td>";
                                echo "<td>" . format_date2($row['transaction_date']) . "</td>";
                                echo "<td>" . $row['produk'] . "</td>";
                                echo "</tr>";
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


<?php
function get_produk_to_in($produk)
{
    $ex = explode(",", $produk);
    //$temp = "";
    for ($i = 0; $i < count($ex); $i++) {

        $jml_key = array_keys($ex, $ex[$i]);
        if (count($jml_key) > 1) {
            unset($ex[$i]);
        }

        //$temp = $ex[$i];
    }
    return implode(",", $ex);
}

?>
<script>
    $(document).ready(function() {
        $('#dataTable9').DataTable({
        });
    });
</script>
