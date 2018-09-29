<?php

function WriteBoxHeader($title)
{
	echo "<div class='main-left-box'>";
	echo "<div class='main-left-title'>$title</div>";
	echo "<div class='main-left-inner'>";
}

$showrental = (bool) YAAMP_RENTAL;

$algo = user()->getState('yaamp-algo');

$count = getparam('count');
$count = $count? $count: 50;

WriteBoxHeader("Last $count Blocks ($algo)");

$criteria = new CDbCriteria();
$criteria->condition = "t.category NOT IN ('stake','generated')";
$criteria->condition .= " AND IFNULL(coin.visible,1)=1"; // ifnull for rental
if($algo != 'all') {
	$criteria->condition .= " AND t.algo=:algo";
	$criteria->params = array(':algo'=>$algo);
}
$criteria->limit = $count;
$criteria->order = 't.time DESC';
$db_blocks = getdbolistWith('db_blocks', 'coin', $criteria);

echo <<<EOT

<style type="text/css">
span.block { padding: 2px; display: inline-block; text-align: center; min-width: 75px; border-radius: 3px; }
span.block.new       { color: white; background-color: #5FC2EF; }
span.block.orphan    { color: white; background-color: #772D0B; }
span.block.immature  { color: white; background-color: #EF8C5F; }
span.block.confirmed { color: white; background-color: #0B5577; }
b.row a { font-size: 10pt; }
.ssrow td.row { font-size: .8em; }
td.right { text-align: right; }
</style>

<table class="dataGrid2">
<thead>
<tr>
<td></td>
<th>Coin</th>
<th align="right">Reward</th>
<th align="right">Reward BTC</th>
<th align="right">Reward USD</th>
<th align="right">Difficulty</th>
<th align="right">Block</th>
<th align="right">When</th>
<th align="right">Status</th>
</tr>
</thead>
EOT;

foreach($db_blocks as $db_block)
{
	$d = datetoa2($db_block->time);
	if(!$db_block->coin_id)
	{
		if (!$showrental)
			continue;

		$reward = bitcoinvaluetoa($db_block->amount);

		echo '<tr class="ssrow">';
		echo '<td width="18px"><img width="16px" src="/images/btc.png"/></td>';
		echo '<td class="row"><b>Rental</b> ('.$db_block->algo.')</td>';
		echo '<td class="row right"><b>'.$reward.' BTC</b></td>';
		echo '<td class="row right"></td>';
		echo '<td class="row right"></td>';
		echo '<td class="row right">'.$d.' ago</td>';
		echo '<td class="row right">';
		echo '<span class="block confirmed">Confirmed</span>';
		echo '</td>';
		echo '</tr>';

		continue;
	}
  $mining = getdbosql('db_mining');

	$reward = round($db_block->amount, 3);
	$coin = $db_block->coin ? $db_block->coin : getdbo('db_coins', $db_block->coin_id);
	$difficulty = Itoa2($db_block->difficulty, 3);
	$height = number_format($db_block->height, 0, '.', ' ');
	//$value = mbitcoinvaluetoa($reward*$coin->price);
	$price = bitcoinvaluetoa($coin->price);
  $total_earned_btc = mbitcoinvaluetoa($reward*$price); 
  $total_earned_usd = mbitcoinvaluetoa($reward*$price*$mining->usdbtc); 
	$link = $coin->createExplorerLink($coin->name, array('hash'=>$db_block->blockhash));

	$flags = $db_block->segwit ? '&nbsp;<img src="/images/ui/segwit.png" height="8px" valign="center" title="segwit"/>' : '';

	echo '<tr class="ssrow">';
	echo '<td width="18px"><img width="16px" src="'.$coin->image.'"></td>';
	echo '<td class="row"><b class="row">'.$link.'</b> ('.$db_block->algo.')'.$flags.'</td>';
	echo '<td class="row right"><b>'.$reward.' '.$coin->symbol_show.'</b></td>';

	echo '<td class="row right"><b>'.$total_earned_btc.'</b></td>';
	echo '<td class="row right"><b>$'.$total_earned_usd.'</b></td>';

	echo '<td class="row right" title="found '.$db_block->difficulty_user.'">'.$difficulty.'</td>';
	echo '<td class="row right">'.$height.'</td>';
	echo '<td class="row right">'.$d.' ago</td>';
	echo '<td class="row right">';

	if($db_block->category == 'orphan')
		echo '<span class="block orphan">Orphan</span>';

	else if($db_block->category == 'immature') {
		$eta = '';
		if ($coin->block_time && $coin->mature_blocks) {
			$t = (int) ($coin->mature_blocks - $db_block->confirmations) * $coin->block_time;
			$eta = "ETA: ".sprintf('%dh %02dmn', ($t/3600), ($t/60)%60);
		}
		echo '<span class="block immature" title="'.$eta.'">Immature ('.$db_block->confirmations.' of '.$coin->mature_blocks.')</span>';
	}
	else if($db_block->category == 'generate')
		echo '<span class="block confirmed">Confirmed</span>';

	else if($db_block->category == 'new')
		echo '<span class="block new">New</span>';

	echo "</td>";
	echo "</tr>";
}

echo "</table>";

echo "<br></div></div><br>";




