<?php

$algo = user()->getState('yaamp-algo');

JavascriptFile("/extensions/jqplot/jquery.jqplot.js");
JavascriptFile("/extensions/jqplot/plugins/jqplot.dateAxisRenderer.js");
JavascriptFile("/extensions/jqplot/plugins/jqplot.barRenderer.js");
JavascriptFile("/extensions/jqplot/plugins/jqplot.highlighter.js");
JavascriptFile("/extensions/jqplot/plugins/jqplot.cursor.js");
JavascriptFile('/yaamp/ui/js/auto_refresh.js');

$height = '240px';

$min_payout = floatval(YAAMP_PAYMENTS_MINI);
$min_sunday = $min_payout/10;

$payout_freq = (YAAMP_PAYMENTS_FREQ / 3600)." hours";
?>

<div id='resume_update_button' style='color: white; background-color: #5FC2EF; border: 5px solid #0B5577;	padding: 10px; margin-left: 20px; margin-right: 20px; margin-top: 15px; cursor: pointer; display: none;'	onclick='auto_page_resume();' align=center>
	<b>Auto refresh is paused - Click to resume</b></div>

<table cellspacing=20 width=100%>
<tr><td valign=top width=50%>

<!--  -->

<div class="main-left-box">
<div class="main-left-title">WELCOME TO CRYPTO AND COFFEE</div>
<div class="main-left-inner">

<ul>

<div>
    <h2 align="center">YiimP meet nvidia-docker!</h2><br><br>
    <div align="center" class="float-left"><img style="display: block; margin: auto; width: 15%; padding-bottom: 10px; padding-top:0px;" src="/images/docker.jpg"></img>
</div>
    <div align="center" class="float-left"><img style="display: block; margin: auto; width: 15%; padding-bottom: 10px; padding-top:0px;" src="/images/cncminer-icon-512.png"></img>
</div>
    <div align="center" class="float-left"><img style="display: block; margin: auto; width: 15%; padding-bottom: 10px; padding-top:0px;" src="/images/nvidia.jpg"></img>
</div>
    <h2 align="center">nvidia-docker.com is brought to you by Crypto and Coffee</h2>
    <h2 align="center">We proudly host the world's largest collection of Docker Mining Images on Docker Hub.</h2>
    <h2 align="right">Our pool has many unique features including:<ul>
    				<h2>Redundant Stratum Servers on every Continent</h2>
						<h2>HAProxy powered connections using leastconn</h2>
						<h2>Custom Profit Calculation Code</h2>
						<h2>FIAT/USD Statistics</h2>
						<h2>High Resolution Charts</h2>
						<h2>Daily Backups</h2>
							<h2>Live Chat</h2>
							<h2>Tutorials and Livestreaming on Twitch.TV</h2>

						<!--<h2>TOS/Privacy Policy(Coming Soon)</h2>-->
						</ul></h2>
</div>
</br>
</br>
<li>YOu should familiarize yourself with the source code of YIIMP.</li>
<p> Link to readme wiki</p>
<li>Payouts are made automatically every <?= $payout_freq ?> for all balances above <b><?= $min_payout ?></b>, or <b><?= $min_sunday ?></b> on Sunday. We use PPLNS (Pay-Per-Last-Known-Share) as a payout method$
<li>For support please write <a href="mailto:support@cryptoandcoffee.com">support@cryptoandcoffee.com</a> our reach us directly on Discord</li>
<li>Continue to support Crypto Currencies by following us on social media<a href="https://twitter.com/cryptoandcoffee" target="_new">@cryptoandcoffee</a></li>
<!--<li>Blocks are distributed proportionally among valid submitted shares.</li>-->

<br/>

<br/>

</ul>
</div></div>
<br/>

<!--  -->



<!--  -->

<div class="main-left-box">
<div class="main-left-title">LINKS</div>
<div class="main-left-inner">

<ul>

<!--<li><b>BitcoinTalk</b> - <a href='https://bitcointalk.org/index.php?topic=508786.0' target=_blank >https://bitcointalk.org/index.php?topic=508786.0</a></li>-->
<!--<li><b>IRC</b> - <a href='http://webchat.freenode.net/?channels=#yiimp' target=_blank >http://webchat.freenode.net/?channels=#yiimp</a></li>-->
<li><b>Crypto and Coffee</b> - <a href='https://cryptoandcoffee.com'>https://cryptoandcoffee.com</a></li>
<li><b>API</b> - <a href='/site/api'>http://<?= YAAMP_SITE_URL ?>/site/api</a></li>
<li><b>Difficulty</b> - <a href='/site/diff'>http://<?= YAAMP_SITE_URL ?>/site/diff</a></li>
<?php if (YIIMP_PUBLIC_BENCHMARK): ?>
<li><b>Benchmarks</b> - <a href='/site/benchmarks'>http://<?= YAAMP_SITE_URL ?>/site/benchmarks</a></li>
<?php endif; ?>

<?php if (YAAMP_ALLOW_EXCHANGE): ?>
<li><b>Algo Switching</b> - <a href='/site/multialgo'>http://<?= YAAMP_SITE_URL ?>/site/multialgo</a></li>
<?php endif; ?>

<br>

</ul>
</div></div><br>

<!--  -->

<a class="twitter-timeline" href="https://twitter.com/hashtag/YAAMP" data-widget-id="617405893039292417" data-chrome="transparent" height="450px" data-tweet-limit="3" data-aria-polite="polite">Tweets about #nvidia-docker</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

</td><td valign=top>

<!--  -->

<div id='pool_current_results'>
<br><br><br><br><br><br><br><br><br><br>
</div>
<div class="main-left-box">
<div class="main-left-title">STRATUM SERVERS</div>
<div class="main-left-inner">

<ul>

<li>
	<a href="https://hub.docker.com/u/cryptoandcoffee/" target="_new">Browse our Docker Hub</a>
<p class="main-left-box" style='padding: 3px; font-size: 0.8em; background-color: #1498D5; color: #FFF; font-family: monospace;'>
	nvidia-docker run -it cryptoandcoffee/nvidia-docker-ccminer-tpruvot-c92 \<br>
	--algo &lt;ALGO&gt; -o stratum+tcp://<?= YAAMP_STRATUM_URL ?>:&lt;PORT&gt; -u &lt;WALLET&gt; [-p &lt;OPTIONS&gt;]</p>
</li>

<?php if (YAAMP_ALLOW_EXCHANGE): ?>
<li>&lt;WALLET_ADDRESS&gt; can be one of any currency we mine or a BTC address.</li>
<?php else: ?>
<li>&lt;WALLET_ADDRESS&gt; should be valid for the currency you mine. <b>DO NOT USE a BTC address here, the auto exchange is disabled for now ;)</b>!</li>
<?php endif; ?>

<br>

</ul>
</div></div><br>
<div id='pool_history_results'>
<br><br><br><br><br><br><br><br><br><br>
</div>

</td></tr></table>

<br><br><br><br><br><br><br><br><br><br>
<br><br><br><br><br><br><br><br><br><br>
<br><br><br><br><br><br><br><br><br><br>
<br><br><br><br><br><br><br><br><br><br>

<script>

function page_refresh()
{
	pool_current_refresh();
	pool_history_refresh();
}

function select_algo(algo)
{
	window.location.href = '/site/algo?algo='+algo+'&r=/';
}

////////////////////////////////////////////////////

function pool_current_ready(data)
{
	$('#pool_current_results').html(data);
}

function pool_current_refresh()
{
	var url = "/site/current_results";
	$.get(url, '', pool_current_ready);
}

////////////////////////////////////////////////////

function pool_history_ready(data)
{
	$('#pool_history_results').html(data);
}

function pool_history_refresh()
{
	var url = "/site/history_results";
	$.get(url, '', pool_history_ready);
}

</script>

