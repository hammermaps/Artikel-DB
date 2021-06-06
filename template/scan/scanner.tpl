<!-- QR Code Scanner -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/3.3.3/adapter.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.min.js"></script>
<script type="text/javascript" src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
<div id="scanner"></div>
<div id="page-wrapper">
    <!-- /.row -->
    <div class="row">
        <div style="margin-top: 25px" class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    QR Code Scanner
                </div>
                <div class="panel-body">
                    <div class="row">
                        <!-- /.col-lg-12 (nested) -->
                        <div class="col-lg-12">
                            {literal}
                            <div id="app">
                                <div class="sidebar">
                                    <section class="scans">
                                        <transition-group name="scans" tag="ul">
                                            <li v-for="scan in scans" :key="scan.date" :title="scan.content">{{ scan.content }}</li>
                                        </transition-group>
                                    </section>
                                </div>
                                <div class="preview-container">
                                    <video id="preview"></video>
                                </div>
                            </div>
                            {/literal}
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