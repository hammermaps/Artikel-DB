<!-- /.row -->
<div class="row">
    <div style="margin-top: 25px" class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Artikel bearbeiten
            </div>
            <div class="panel-body">
                <div class="row" id="add">
                    <!-- /.col-lg-6 (nested) -->
                    <div class="col-lg-12">
                        {$notifications}
                        <form role="form" action="edit_{$id}.html" method="post">
                            <div class="form-group">
                                <label>EAN ( Artikel Nummer )</label>
                                <input name="ean" class="form-control" value="{$from.ean}" {$from.disabled}>
                            </div>
                            <div class="form-group">
                                <label>Name</label>
                                <input name="name" class="form-control" value="{$from.name}" {$from.disabled}>
                            </div>
                            <div class="form-group">
                                <label>Meta Tags</label>
                                <textarea name="tags" class="form-control" rows="3" {$from.disabled}>{$from.tags}</textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-default">Speichern</button>
                                <button type="reset" class="btn btn-default">Zur&uuml;cksetzen</button>
                            </div>
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