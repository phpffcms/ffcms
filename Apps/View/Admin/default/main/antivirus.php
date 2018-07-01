<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Antivirus'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        __('Antivirus')
    ]
]);
?>

<?php $this->start('body') ?>
<h1><?= __('Antivirus scan'); ?></h1>
<hr />
<p><?= __('FFCMS 3 provide a simple signature-based antivirus software') . '. ' . __('Remember! This is just an advisory algorithm!') ?></p>

<div class="row mb-2">
    <div class="col-md-8">
        <div class="progress d-none" id="pbar-main">
            <div id="pbar-item" class="progress-bar bg-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0">
                0%
            </div>
        </div>
        <a href="#scanlog" class="btn btn-success" id="runscan"><?= __('Scan') ?></a>
    </div>
    <div class="col-md-4">
        <div class="card d-none" id="scanlog">
            <div class="card-body">
                <?= __('Files left') ?>: <span class="badge badge-primary" id="scancount">0</span> <br />
                <?= __('Detected issues') ?>: <span class="badge badge-warning" id="detected">0</span>
            </div>
        </div>
    </div>
</div>

<ul class="nav nav-tabs" id="tab-menu" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#critical" role="tab" aria-controls="home" aria-selected="true"><?= __('Critical') ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#suspicious" role="tab" aria-controls="profile" aria-selected="false"><?= __('Suspicious') ?></a>
    </li>
</ul>
<!-- Tab panes -->
<div class="tab-content">
    <div class="tab-pane active" id="critical" role="tabpanel" aria-labelledby="home-tab">
        <div class="table-responsive">
            <table id="criticalresult" class="table table-hover d-none">
                <thead>
                <tr>
                    <th><?= __('File') ?></th>
                    <th><?= __('Issues') ?></th>
                    <th><?= __('Descriptions of issues') ?></th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <p id="no-critical-msg"><?= __('No critical issues found') ?></p>
    </div>
    <div class="tab-pane" id="suspicious" role="tabpanel" aria-labelledby="profile-tab">
        <div class="table-responsive">
            <table id="scanresult" class="table table-hover">
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
    </div>
</div>
<a href="#scanresult" class="btn btn-primary" id="loadresults"><?= __('Update results') ?></a>
<?php $this->stop() ?>

<?php $this->push('javascript') ?>
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
                $("#scanlog").removeClass("d-none");
                $("#pbar-main").removeClass("d-none");
                runscan(true);
            })
        });
        // jquery load results via json
        $(document).ready(function () {
            loadResults = function () {
                $.getJSON(script_url + '/api/main/antivirusresults?lang=' + script_lang, function(data) {
                    if (data.status != 1) {
                        $("#scanresult").addClass('d-none');
                        $('#criticalresult').addClass('d-none');
                    } else {
                        $("#scanresult").removeClass('d-none');
                        $('#criticalresult').removeClass('d-none');
                    }
                    // cleanup ;)
                    $("#scanresult tbody").empty();
                    if (typeof data.data === 'undefined') {
                        return;
                    }
                    $.each(data.data, function(file, logs) {
                        var isCritical = false;
                        var content = "<td>" + file + "</td>" +
                            "<td>" + logs[0].length + "</td>" +
                            "<td>";
                        $.each(logs[0], function (num, info) {
                            if (info.sever == "c") { // critical malware posibility
                                content += "<span class=\"text-danger\">";
                                isCritical = true;
                            } else if (info.sever = "w") { // warning, low posibility
                                content += "<span class=\"text-warning\">";
                            } else {
                                content += "<span>";
                            }
                            content += info.title + ", pos: " + info.pos + ", malware_id: " + info.sigId + ", regExp: " + info.sigRule + "<br />";
                        });
                        content += "</td>";
                        if (isCritical) {
                            $('#criticalresult tbody').append('<tr>' + content + '</tr>');
                            if (!$('#no-critical-msg').hasClass('d-none')) {
                                $('#no-critical-msg').addClass('d-none');
                            }
                        } else {
                            $("#scanresult tbody").append("<tr>" + content + "</tr>");
                        }
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
<?php $this->stop() ?>