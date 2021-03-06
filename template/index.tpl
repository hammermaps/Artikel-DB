<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Artikel Datenbank</title>

    <!-- Bootstrap Core CSS -->
    <link href="{$index.dir}/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="{$index.dir}/vendor/datatables-plugins/dataTables.bootstrap.min.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="{$index.dir}/vendor/datatables-responsive/responsive.bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Calendar CSS -->
    <link href="{$index.dir}/css/calendar.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="{$index.dir}/vendor/metismenu/css/metisMenu.min.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="{$index.dir}/vendor/font-awesome/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{$index.dir}/css/template.css" rel="stylesheet">
</head>

<body>
    <div id="wrapper">
        {$index.navigation}
        <div id="page-wrapper">
        {$index.content}
        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; CodeDesigns 2021 | Script: Version {$index.version} / Database: Version {$index.dbv}</span>
                </div>
            </div>
        </footer>
        <!-- End of Footer -->
        </div>
    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="{$index.dir}/vendor/jquery/js/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="{$index.dir}/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="{$index.dir}/vendor/font-awesome/js/fontawesome.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="{$index.dir}/vendor/datatables/js/datatables.min.js"></script>
    <script src="{$index.dir}/vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="{$index.dir}/vendor/datatables-responsive/dataTables.responsive.min.js"></script>

    <!-- Bootstrap Calendar JavaScript -->
    <script src="{$index.dir}/js/calendar.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="{$index.dir}/vendor/metismenu/js/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="{$index.dir}/js/template.min.js"></script>
</body>
</html>