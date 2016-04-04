<?php

/** @var array $stats */
/** @var \Ffcms\Core\Arch\View $this */

$this->title = __('FFCMS 3 Dashboard');
$this->breadcrumbs = [
    __('Main'),
    __('Dashboard')
]
?>
<link href="<?= \App::$Alias->currentViewUrl; ?>/assets/css/gAnalytics.css" rel="stylesheet">
<h1><?= __('Main dashboard') ?></h1>
<hr/>
<div class="row">
	<div class="col-md-12">
		<div class="pull-right">
			<div id="embed-api-auth-container"></div>
		</div>
	</div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __('Server info') ?></div>
            <div class="panel-body">
                <?= \Ffcms\Core\Helper\HTML\Table::display([
                    'table' => ['class' => 'table table-bordered'],
                    'tbody' => [
                        'items' => [
                            [
                                ['text' => 'FFCMS version'],
                                ['text' => $stats['ff_version']]
                            ],
                            [
                                ['text' => 'PHP version'],
                                ['text' => $stats['php_version']]
                            ],
                            [
                                ['text' => 'OS name'],
                                ['text' => $stats['os_name']]
                            ],
                            [
                                ['text' => 'Database name'],
                                ['text' => $stats['database_name']]
                            ],
                            [
                                ['text' => 'Files size'],
                                ['text' => $stats['file_size']]
                            ],
                            [
                                ['text' => 'Load average'],
                                ['text' => $stats['load_avg']]
                            ],
                        ]
                    ]
                ]); ?>
                <?= \Ffcms\Core\Helper\Url::link(['main/cache'], 'Clean cache', ['class' => 'btn btn-warning']) ?>
                <?= \Ffcms\Core\Helper\Url::link(['main/sessions'], 'Clean sessions', ['class' => 'btn btn-info']) ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __('GA: visits and users') ?></div>
            <div class="panel-body">
				<div id="chart-1-container"></div>
				<div id="view-selector-1-container" class="ViewSelector"></div>
			</div>
        </div>
    </div>
    <div class="col-md-4">
    	<div class="panel panel-default">
    		<div class="panel-heading"><?= __('GA: Countries')?></div>
    		<div class="panel-body">
				<div id="chart-2-container"></div>
				<div id="view-selector-2-container" class="ViewSelector"></div>
			</div>
    	</div>
	</div>
</div>
<script>
(function(w,d,s,g,js,fs){
  g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
  js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
  js.src='https://apis.google.com/js/platform.js';
  fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
}(window,document,'script'));
</script>
<script>
var GOOGLE_OAUTH2_CLIENT_ID = '<?= \App::$Properties->get('gaClientId') ?>';
gapi.analytics.ready(function() {

  gapi.analytics.auth.authorize({
    container: 'embed-api-auth-container',
    clientid: GOOGLE_OAUTH2_CLIENT_ID
  });

  var viewSelector1 = new gapi.analytics.ViewSelector({
    container: 'view-selector-1-container'
  });

  var viewSelector2 = new gapi.analytics.ViewSelector({
    container: 'view-selector-2-container'
  });

  // Render both view selectors to the page.
  viewSelector1.execute();
  viewSelector2.execute();

  var dataChart1 = new gapi.analytics.googleCharts.DataChart({
	    query: {
	        metrics: 'ga:sessions,ga:users',
	        dimensions: 'ga:date',
	        'start-date': '30daysAgo',
	        'end-date': 'yesterday'
	      },
	      chart: {
	        container: 'chart-1-container',
	        type: 'LINE',
	        options: {
	          width: '100%',
	          series: {
	        	  0: { color: '#e2431e' },
	              1: { color: '#6f9654' }
		      }
	        }
	      }
  });

  var dataChart2 = new gapi.analytics.googleCharts.DataChart({
    query: {
      metrics: 'ga:sessions',
      dimensions: 'ga:country',
      'start-date': '30daysAgo',
      'end-date': 'yesterday',
      'max-results': 6,
      sort: '-ga:sessions'
    },
    chart: {
      container: 'chart-2-container',
      type: 'PIE',
      options: {
        width: '100%',
        pieHole: 4/9
      }
    }
  });

  viewSelector1.on('change', function(ids) {
    dataChart1.set({query: {ids: ids}}).execute();
  });

  viewSelector2.on('change', function(ids) {
    dataChart2.set({query: {ids: ids}}).execute();
  });

});
</script>