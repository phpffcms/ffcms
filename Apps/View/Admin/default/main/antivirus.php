<?php

use Ffcms\Core\Helper\Url;

$this->title = __('Antivirus');
$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    __('Antivirus')
];
// jquery scan function
?>

<script>
$(document).ready(function() {
    runscan = function (first) {
        $.getJSON(script_url+'/api/main/antivirus?lang='+script_lang, function (data) {
            if (first) {
                totalScan = data.left;
                progress = 0;
            } else {
                progress = ((totalScan - data.left) / totalScan) * 100;
            }
            $("#pbar-item").css("width", progress + "%");
            $("#pbar-item").text(parseInt(progress) + "%");
            $("#scancount").text(data.left);
            $("#detected").text(parseInt($("#detected").text()) + data.detect);
        }).done(function (data) {
            if (data.left > 0) {
                loadResults();
                runscan(false);
            } else {
                $("#runscan").text("Done!");
            }
        });
    }
});

// jquery init of scan
$(document).ready(function () {
    $("#runscan").on("click", function () {
        $.get(script_url + '/api/main/antivirusclear?lang=' + script_lang);
        $(this).addClass("disabled");
        $(this).text("Working ...");
        $("#scanlog").removeClass("hidden");
        $("#pbar-main").removeClass("hidden");
        runscan(true);
    })
});

// jquery load results via json
$(document).ready(function () {
    loadResults = function () {
        $.getJSON(script_url + '/api/main/antivirusresults?lang=' + script_lang, function(data) {
            if (data.status != 1) {
                $("#scanresult").addClass('hidden');
                $("#loadresults").addClass('hidden');
            } else {
                $("#scanresult").removeClass('hidden');
                $("#loadresults").removeClass('hidden');
            }
            // cleanup ;)
            $("#scanresult tbody").empty();
            if (typeof data.data === 'undefined') {
                return;
            }

            $.each(data.data, function(file, logs) {
                content = "<td>"+file+"</td>"+
                    "<td>"+logs[0].length+"</td>"+
                    "<td>";

                $.each(logs[0], function(num, info) {
                    if (info.sever == "c") { // critical malware posibility
                        content += "<span class=\"text-danger\">";
                    } else if(info.sever="w") { // warning, low posibility
                        content += "<span class=\"text-warning\">";
                    } else {
                        content += "<span>";
                    }
                    content += "Desc: "+info.title+", pos: "+info.pos+", malware_id: "+info.sigId+", regExp: "+info.sigRule + "<br />";
                });
                content += "</td>";
                $("#scanresult tbody").append("<tr>"+content+"</tr>");
            });
        });
    }
});

$(document).ready(function () {
    $("#loadresults").on("click", function () {
        loadResults();
    });
    loadResults();
});
var totalScan = 2000;
</script>
<h1><?= __('Antivirus scan'); ?></h1>
<hr />
<p><?= __('FFCMS 3 provide a simple signature-based antivirus software') . '. ' . __('Remember! This is just an advisory algorithm!') ?></p>

<div class="row">
    <div class="col-md-4">
        <div class="progress hidden" id="pbar-main">
            <div id="pbar-item" class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                0%
            </div>
        </div>
        <a href="#scanlog" class="btn btn-success" id="runscan"><?= __('Start scan') ?></a>
    </div>
    <div class="col-md-8">
        <div id="scanlog" class="panel panel-default hidden">
            <?= __('Files left') ?>: <span class="label label-primary" id="scancount">0</span> <br />
            <?= __('Detected issues') ?>: <span class="label label-warning" id="detected">0</span>
        </div>
    </div>
</div>

<br />
<div class="table table-responsive">
    <table id="scanresult" class="table table-bordered table-hover">
        <thead>
        <tr>
            <th><?= __('File') ?></th>
            <th><?= __('Issues') ?></th>
            <th><?= __('Descriptions of issues') ?></th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<a href="#scanresult" class="btn btn-primary" id="loadresults"><?= __('Update results') ?></a>