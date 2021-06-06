<!-- Navigation -->
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="search.html">Artikel Datenbank</a>
    </div>
    <!-- /.navbar-header -->

    <ul class="nav navbar-top-links navbar-right">
        <!-- /.dropdown -->
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                {if $is_logged}
                    <li><a href="logout.html"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                {else}
                    <li><a href="login.html"><i class="fa fa-sign-out fa-fw"></i> Login</a></li>
                {/if}
            </ul>
            <!-- /.dropdown-user -->
        </li>
    </ul>
    <!-- /.navbar-top-links -->

    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu">
                <li>
                    <a href="search.html"><i class="fa fa-search fa-fw"></i> Suche</a>
                </li>
                <!--  <li>
                      <a href="scan.html"><i class="fa fa-barcode fa-fw"></i> Scanner</a>
                  </li> -->
                <li>
                    <a href="add.html"><i class="fa fa-plus-square fa-fw"></i> Einf&uuml;gen</a>
                </li>
                <li>
                    <a href="edit.html"><i class="fa fa-edit fa-fw"></i> Bearbeiten</a>
                </li>
                <li>
                    <a href="export.html"><i class="fa fa-file-export fa-fw"></i> Exportieren als PDF</a>
                </li>
            </ul>
        </div>
        <!-- /.sidebar-collapse -->
    </div>
    <!-- /.navbar-static-side -->
</nav>
