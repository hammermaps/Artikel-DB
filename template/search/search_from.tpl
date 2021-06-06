<div id="page-wrapper">
    <!-- /.row -->
    <div class="row">
	{$notifications}
        <div style="margin-top: 25px" class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Artikel Suche
                </div>
                <div class="panel-body">
                    <div class="row">
                        <!-- /.col-lg-12 (nested) -->
                        <div class="col-lg-12">
                            <form role="form">
                                <div class="form-group input-group">
                                    <input type="text" id="search" name="search" class="form-control">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>
                                <div id="results">{$entities}</div>
                            </form>
                        </div>
                        <!-- /.col-lg-6 (nested) -->
                    </div>
                    <!-- /.row (nested) -->
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->