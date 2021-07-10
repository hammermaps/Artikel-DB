/*!
 * Start Bootstrap - SB Admin 2 v3.3.7+1 (http://startbootstrap.com/template-overviews/sb-admin-2)
 * Copyright 2013-2016 Start Bootstrap
 * Licensed under MIT (https://github.com/BlackrockDigital/startbootstrap/blob/gh-pages/LICENSE)
 */
$(function() {
    $('#side-menu').metisMenu();

    var currentYear = new Date().getFullYear();

    $('#calendar').calendar({
        style:'background',
        dataSource: [
            {
                startDate: new Date(currentYear, 1, 4),
                endDate: new Date(currentYear, 1, 15)
            },
            {
                startDate: new Date(currentYear, 3, 5),
                endDate: new Date(currentYear, 5, 15)
            }
        ]
    });
});

$(function () {
    if($("#scanner").length){
        var app = new Vue({
            el: '#app',
            data: {
                scanner: null,
                activeCameraId: null,
                cameras: [],
                scans: []
            },
            mounted: function () {
                var self = this;
                self.scanner = new Instascan.Scanner({ video: document.getElementById('preview'), scanPeriod: 1, refractoryPeriod: 2000 });
                self.scanner.addListener('scan', function (content, image) {
                    self.scans.unshift({ date: +(Date.now()), content: content });
                });
                Instascan.Camera.getCameras().then(function (cameras) {
                    self.cameras = cameras;
                    if (cameras.length > 0) {
                        self.activeCameraId = cameras[1].id;
                        self.scanner.start(cameras[1]);
                    } else {
                        console.error('No cameras found.');
                    }
                }).catch(function (e) {
                    console.error(e);
                });
            },
            methods: {
                formatName: function (name) {
                    return name || '(unknown)';
                }
            }
        });
    }
})

$(document).ready(function() {
    var article = document.getElementById('table_div');
    var dataset_to_index = 'search';
    if(article !== null) {
        dataset_to_index = article.dataset.type;
    }

    var table = $('#dataTables').DataTable( {
        responsive: true,
        processing: false,
        serverSide: true,
        pageLength: 15,
        language: {
            "paginate": {
                "next": "Weiter",
                "previous": "Zurück"
            }
        },
        ajax:{
            url : (dataset_to_index === "edit" ? "search.php?type=edit" : "search.php?type=search"), // json datasource
            dataType: "jsonp",
            type: "post",
            error: function() {
                $(".dataTables-error").html("");
               // $("#dataTables").append('<tbody class="employee-grid-error"><tr><th colspan="2">Keine Daten auf dem Server gefunden</th></tr></tbody>');
                $("#dataTables_processing").css("display","none");
            },
        }
    });

    $('#dataTablesEAN').DataTable( {
        responsive: true,
        processing: false,
        serverSide: true,
        paging: false,
        searching: false,
        ordering:  false,
        ajax:{
            url :"search_ean.php", // json datasource
            dataType: "jsonp",
            type: "post"
        }
    });

    $('#search').keyup(function() {
        table.search($(this).val()).draw();
    });
});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        var topOffset = 50;
        var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    var url = window.location;
    // var element = $('ul.nav a').filter(function() {
    //     return this.href == url;
    // }).addClass('active').parent().parent().addClass('in').parent();
    var element = $('ul.nav a').filter(function() {
        return this.href == url;
    }).addClass('active').parent();

    while (true) {
        if (element.is('li')) {
            element = element.parent().addClass('in').parent();
        } else {
            break;
        }
    }
});