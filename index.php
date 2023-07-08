<?php
error_reporting(0);
session_start();
$sidebar = '';
if (isset($_GET['sidebar'])) {
    $sidebar = $_GET['sidebar'];
}

//if (!file_exists($sidebar . ".php")) {
//    $sidebar = 'not_found';
//}

if (
    !isset($_SESSION['apriori_kopi_id']) &&
    ($sidebar != 'tentang' & $sidebar != 'not_found' & $sidebar != 'forbidden')
) {
    header("location:login.php");
}
include_once 'fungsi.php';
//include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Apriori Kopi Khanti</title>
    <link rel="icon" href="logo.webp">
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/stylesloader.css">
    <script src="js/jquery-2.1.4.min.js"></script>
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script> -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="js/daterangepicker.min.js"></script>
    <script src="js/bootstrap-datetimepicker.min.js"></script>
    <!-- <script src="js/jquery.js"></script> -->
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/ace.min.js"></script>
    <!-- <script src="js/ace-editable.min.js"></script> -->
    <script src="js/ace-elements.min.js"></script>
    <script src="js/jquery-ui.custom.min.js"></script>
    <link rel="stylesheet" href="css/daterangepicker.min.css" />
    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> -->
</head>

<body id="page-top">
    <div id="wrapper">
        <div id="loading-overlay">
            <div class="lds-circle">
                <div></div>
            </div>
        </div>
        <?php include 'sidebar.php' ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'header.php' ?>


                <?php
                $sidebar = ''; //variable untuk menampung  sidebar
                if (isset($_GET['sidebar'])) {
                    $sidebar = $_GET['sidebar'];
                }

                if ($sidebar != '') {
                    if (can_access_sidebar($sidebar)) {
                        if (file_exists($sidebar . ".php")) {
                            include $sidebar . '.php';
                        } else {
                            include "not_found.php";
                        }
                    } else {
                        include "forbidden.php";
                    }
                } else {
                    include "blank.php";
                }

                ?>



            </div>
            <?php include "footer.php" ?>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <!-- <script src="vendor/jquery/jquery.min.js"></script> -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <!-- <script src="vendor/chart.js/Chart.min.js"></script> -->

    <!-- Page level custom scripts -->
    <!-- <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script> -->

    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="js/demo/datatables-demo.js"></script>
    <script type="text/javascript">
        jQuery(function($) {
            //datepicker plugin
            //link
            $('.date-picker').datepicker({
                    autoclose: true,
                    todayHighlight: true
                })
                //show datepicker when clicking on the icon
                .next().on(ace.click_event, function() {
                    $(this).prev().focus();
                });

            //or change it into a date range picker
            $('.input-daterange').datepicker({
                autoclose: true
            });


            //to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
            $('input[name=range_tanggal]').daterangepicker(

                    {
                        'applyClass': 'btn-sm btn-success',
                        'cancelClass': 'btn-sm btn-default',
                        locale: {
                            applyLabel: 'Apply',
                            cancelLabel: 'Cancel',
                            format: 'DD/MM/YYYY',
                        }
                    })
                .prev().on(ace.click_event, function() {
                    $(this).next().focus();
                });

            $('#id-input-file-1 , #id-input-file-2').ace_file_input({
                no_file: 'No File ...',
                btn_choose: 'Choose',
                btn_change: 'Change',
                droppable: false,
                onchange: null,
                thumbnail: false //| true | large
                //whitelist:'gif|png|jpg|jpeg'
                //blacklist:'exe|php'
                //onchange:''
                //
            });

            //flot chart resize plugin, somehow manipulates default browser resize event to optimize it!
            //but sometimes it brings up errors with normal resize event handlers
            // $.resize.throttleWindow = false;

            /////////////////////////////////////
            $(document).one('ajaxloadstart.page', function(e) {
                $tooltip.remove();
            });
        });
    </script>
    <script>
        // overlay loading
        function showLoadingOverlay() {
            document.getElementById('loading-overlay').classList.add('active');
        }

        // Fungsi untuk menyembunyikan layar loading
        function hideLoadingOverlay() {
            document.getElementById('loading-overlay').classList.remove('active');
        }

        // Menambahkan event listener untuk menampilkan layar loading saat halaman sedang dimuat
        window.addEventListener('beforeunload', function() {
            showLoadingOverlay();
        });

        // Panggil fungsi hideLoadingOverlay() setelah halaman selesai dimuat
        window.addEventListener('load', function() {
            hideLoadingOverlay();
        });
    </script>


</body>
<script>

</script>

</html>