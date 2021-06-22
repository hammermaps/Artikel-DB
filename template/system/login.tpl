<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Anmeldung</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <!-- /.col-lg-12 (nested) -->
                    <div class="col-lg-12">
                        {$notifications}
                        <form name="login" method="post" role="form">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Benutzerkennung" name="username" type="username" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Passwort" name="password" type="password" value="">
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="1">Autologin
                                    </label>
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <button href="" class="btn btn-lg btn-success btn-block">Anmelden</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>