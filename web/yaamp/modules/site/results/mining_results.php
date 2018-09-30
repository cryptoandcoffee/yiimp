<?php

function WriteBoxHeader($title)
{
	echo "<div class='main-left-box'>";
	echo "<div class='main-left-title'>$title</div>";
	echo "<div class='main-left-inner'>";
}

$showrental = (bool) YAAMP_RENTAL;

$algo = user()->getState('yaamp-algo');

$total_rate = yaamp_pool_rate();
$total_rate_d = $total_rate? 'at '.Itoa2($total_rate).'h/s': '';

if($algo == 'all')
	$list = getdbolist('db_coins', "enable and visible order by index_avg desc");
else
	$list = getdbolist('db_coins', "enable and visible and algo=:algo order by index_avg desc", array(':algo'=>$algo));

$count = count($list);

if($algo == 'all')
	$worker = getdbocount('db_workers');
else
	$worker = getdbocount('db_workers', "algo=:algo", array(':algo'=>$algo));

if ($showrental)
	$services = getdbolist('db_services', "algo=:algo ORDER BY price DESC", array(':algo'=>$algo));
else
	$services = array();

////////////////////////////////////////////////////////////////////////////////////
$mining = getdbosql('db_mining');
$coin_count = $count > 1 ? "on $count wallets" : 'on a single wallet';
$miner_count = $worker > 1 ? "$worker miners" : "$worker miner";
WriteBoxHeader("Mining $coin_count $total_rate_d, $miner_count");

showTableSorter('maintable3', "{
	tableClass: 'dataGrid2',
	textExtraction: {
		3: function(node, table, n) { return $(node).attr('data'); },
		6: function(node, table, n) { return $(node).attr('data'); },
		7: function(node, table, n) { return $(node).attr('data'); }
	}
}");

//<th align="right">Net Hash</th>

echo <<<END
 
<thead>
<tr>
<th data-sorter=""></th>
<th data-sorter="text">Symbol</th>
<th data-sorter="text">Name</th>
<th data-sorter="text">Algo</th>
<th align="right">Reward</th>
<th align="right">Price</th>
<th data-sorter="numeric" align="right">Difficulty</th>
<th align="right">Block</th>
<th align="right">TTF</th>
<th align="right">TTF Pool</th>
<th data-sorter="numeric" align="right">Hash Stats</th>
<th data-sorter="currency" align="right">Profit BTC/C&C</th>
<th data-sorter="currency" align="right">Profit BTC/YIIMP</th>
<th data-sorter="currency" align="right">Profit USD/C&C</th>
<th data-sorter="currency" align="right">Profit USD/YIIMP</th>	
<th align="right"><a href='#'>nvidia-docker Install Guide | </a><a href="https://hub.docker.com/u/cryptoandcoffee/" target="_new">Browse our Docker Hub</a><br><b>Run in background with "-itd" or benchmark by adding "--benchmark"</b></th>
</tr>
</thead>
END;

if($algo != 'all' && $showrental)
{
	$hashrate_jobs = yaamp_rented_rate($algo);
	$hashrate_jobs = $hashrate_jobs? Itoa2($hashrate_jobs).'h/s': '';

	$price_rent = dboscalar("select rent from hashrate where algo=:algo order by time desc", array(':algo'=>$algo));
	$price_rent = mbitcoinvaluetoa($price_rent);

	$amount_rent = dboscalar("select sum(amount) from jobsubmits where status=1 and algo=:algo", array(':algo'=>$algo));
	$amount_rent = bitcoinvaluetoa($amount_rent);
}
	$port = getAlgoPort($algo);

foreach($list as $coin)
{
	$name = substr($coin->name, 0, 12);
	$difficulty = Itoa2($coin->difficulty, 3);
	$price = bitcoinvaluetoa($coin->price);
	$height = number_format($coin->block_height, 0, '.', ' ');
//	$pool_ttf = $coin->pool_ttf? sectoa2($coin->pool_ttf): '';
	$pool_ttf = $total_rate? $coin->difficulty * 0x100000000 / $total_rate: 0;
	$reward = round($coin->reward, 3);
	



	$btcmhd = yaamp_profitability($coin);
	$pool_hash = yaamp_coin_rate($coin->id);
	$real_ttf = $pool_hash? $coin->difficulty * 0x100000000 / $pool_hash: 0;
	$pool_hash_sfx = $pool_hash? Itoa4($pool_hash).'': '';
	$pool_hash_sfx_1 = $pool_hash? Itoa3($pool_hash).'': '';
	$pool_hash_sfx_2 = $pool_hash? Itoa5($pool_hash).'': '';

	$real_ttf247 = $real_ttf; //in seconds

	$real_ttf = $real_ttf? sectoa2($real_ttf): '';

	$pool_ttf = $pool_ttf? sectoa2($pool_ttf): '';

	$reward247 = 86400 / $real_ttf247 * $reward;
  $reward24 = round($reward247, 3);
  //convert ttf to mins first
	//then divide by 3600 
	//then multiply times reward
	$pool_hash_pow = yaamp_pool_rate_pow($coin->algo);
	$pool_hash_pow_sfx = $pool_hash_pow? Itoa3($pool_hash_pow).'h/s': '';

	$min_ttf = $coin->network_ttf>0? min($coin->actual_ttf, $coin->network_ttf): $coin->actual_ttf;
	$network_hash = $coin->difficulty * 0x100000000 / ($min_ttf? $min_ttf: 60);
	$network_hash = $network_hash? ''.Itoa3($network_hash).'': '';

	if(controller()->admin && $services)
	{
		foreach($services as $i=>$service)
		{
			if($service->price*100 < $btcmhd) continue;
			$service_btcmhd = mbitcoinvaluetoa($service->price*100);

			echo "<tr class='ssrow'>";
			echo "<td width=18><img width=16 src='/images/btc.png'></td>";
			echo "<td><b>$service->name</b></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td align=right style='font-size: .8em;'><b>$service_btcmhd</b></td>";
			echo "</tr>";

			unset($services[$i]);
		}
	}

	if(isset($price_rent) && $price_rent > $btcmhd)
	{
		echo "<tr class='ssrow'>";
		echo "<td width=18><img width=16 src='/images/btc.png'></td>";
		echo "<td><b>Rental</b></td>";
		echo "<td align=right style='font-size: .8em;'><b>$amount_rent BTC</b></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td align=right style='font-size: .8em;'>$hashrate_jobs</td>";
		echo "<td align=right style='font-size: .8em;'><b>$price_rent</b></td>";
		echo "</tr>";

		unset($price_rent);
	}

	if(!$coin->auto_ready)
		echo "<tr style='opacity: 0.4;'>";
	else
		echo "<tr>";

	echo '<td width="18">';
	echo $coin->createExplorerLink('<img width="16" src="'.$coin->image.'">');
	echo '</td>';

	$owed = dboscalar("select sum(balance) from accounts where coinid=$coin->id");
	if(YAAMP_ALLOW_EXCHANGE && $coin->balance+$coin->mint < $owed*0.9 ) {
		$owed2 = bitcoinvaluetoa($owed - $coin->balance);
		$symbol = $coin->getOfficialSymbol();
		$title = "We are short of this currency ($owed2 $symbol). Please switch to another currency until we find more $symbol blocks.";
		echo "<td><b><a href=\"/site/block?id={$coin->id}\" title=\"$title\" style=\"color: #c55;\">$name</a></b><span style=\"font-size: .8em;\"> ({$coin->algo})</span></td>";
	} else {
				$symbol = $coin->getOfficialSymbol();
		echo "<td><b><a href='/site/block?id=$coin->id'>$coin->symbol</a></b></td>";
		echo "<td><b><a href='/site/block?id=$coin->id'>$coin->name</a></b></td>";
		echo "<td><b><a href='/site/block?id=$coin->id'>$coin->algo</a></b></td>";

	}
  //////////////////////////////////////////////////////
		if (!$coin->network_hash) {
		$remote = new WalletRPC($coin);
		if ($remote)
			$info = $remote->getmininginfo();
		if (isset($info['networkhashps'])) {
			$coin->network_hash = $info['networkhashps'];
			// Got 12493610317.376
						//echo "<p>got coin->network_hash : $coin->network_hash</p>";

			controller()->memcache->set("yiimp-nethashrate-{$coin->symbol}", $info['networkhashps'], 60);
		}
		else if (isset($info['netmhashps'])) {
			$coin->network_hash = floatval($info['netmhashps']) * 1e6;
						//echo "<p>got coin->network_hash2 : $coin->network_hash</p>";

			controller()->memcache->set("yiimp-nethashrate-{$coin->symbol}", $coin->network_hash, 60);
		}
		}
//////////////////////////////////////////////////////////
	//$network_hash = $coin->difficulty * 0x100000000 / ($min_ttf? $min_ttf: 60);
	//		echo "<p>got network_hash : $network_hash</p>";
	
	//$network_hash = $network_hash? 'network hash '.Itoa2($network_hash).'h/s': '';
	//		echo "<p>got network_hash : $network_hash</p>";
	
	$nethash_sfx = $coin->network_hash? strtoupper(Itoa2($coin->network_hash)).'H/s': '';
  $price_btc = bitcoinvaluetoa($coin->price);
  $coin_usd_price = round($price_btc*$mining->usdbtc);  
  $coin_btc_price = round($coin->price);  
  $coin_btc_price = bitcoinvaluetoa($coin->price);
  $block_reward_btc = ($reward24 * $price_btc * $mining->usdbtc);  
  $block_reward_usd = round($price_btc*$mining->usdbtc);  
  
	echo "<td align=right style='font-size: .8em;'>$reward $coin->symbol_show/block<br>$reward24 $coin->symbol_show/day<br>USD $block_reward_btc</td>";

	echo "<td align=right style='font-size: .8em;'>$price_btc BTC<br>$coin_usd_price USD</td>";
	//echo "<td align=right style='font-size: .8em;'><b>$reward24</b></td>";

	$title = "POW $coin->difficulty";
	if($coin->rpcencoding == 'POS')
		$title .= "\nPOS $coin->difficulty_pos";

	echo '<td align="right" style="font-size: .8em;" data="'.$coin->difficulty.'" title="'.$title.'">'.$difficulty.'</td>';

	if(!empty($coin->errors))
		echo "<td align=right style='font-size: .8em; color: red;' title='$coin->errors'>$height</td>";
	else
		echo "<td align=right style='font-size: .8em;'>$height</td>";

	//if(!YAAMP_ALLOW_EXCHANGE && !empty($real_ttf))
		echo '<td align="right" style="font-size: .8em;" title="'.$real_ttf.' at current speed '.Itoa2($pool_hash).'">'.$real_ttf.'</td>';
		echo '<td align="right" style="font-size: .8em;" title="'.$pool_ttf.' at full pool speed">'.$pool_ttf.'</td>';
		//echo '<td align="right" style="font-size: .8em;" title="'.$network_hash.' current network hash rate">'.$network_hash.'</td>';


	//echo '<td align="right" style="font-size: .8em;" title="'.$real_ttf.' at current pool speed">'.$real_ttf.'</td>';
	//echo '<td align="right" style="font-size: .8em;" title="'.$pool_ttf.' at full pool speed">'.$pool_ttf.'</td>';
	//elseif(!empty($real_ttf))
	//	echo '<td align="right" style="font-size: .8em;" title="'.$real_ttf.' at '.Itoa2($pool_hash).'">'.$pool_ttf.'</td>';

	//else
	//	echo '<td align="right" style="font-size: .8em;" title="At current pool speed">'.$pool_ttf.'</td>';

	if($coin->auxpow && $coin->auto_ready)
		echo "<td align=right style='font-size: .8em; opacity: 0.6;' title='merge mined\n$network_hash' data='$pool_hash_pow'>$pool_hash_pow_sfx</td>";
	else
	  $ppp = round(($pool_hash / $coin->network_hash)*100,4);
		//	echo "$pool_hash";
		//echo "$coin->network_hash";

		echo "<td align=right style='font-size: .8em;' title='$network_hash' data='$pool_hash'>$pool_hash_sfx_1 is $ppp% of $nethash_sfx</td>";
				//echo "<td align=right style='font-size: .8em;'></td>";

  //BTC column
	$btcmhd = mbitcoinvaluetoa($btcmhd);
	$btcmhd_cc = round($reward24*$btcmhd,2);

	echo "<td align=right style='font-size: .8em;' data='$btcmhd_cc'><b>$btcmhd_cc</b></td>";
	$btcmhd_yiimp = mbitcoinvaluetoa($btcmhd);
	echo "<td align=right style='font-size: .8em;' data='$btcmhd_yiimp'><b>$btcmhd_yiimp</b></td>";

  //USD column
  $usdmhd = round($coin->price * $reward24 * $mining->usdbtc,2);
	echo "<td align=right style='background-color: #98D513; color: #fff; text-align:center; font-size: 1.2em;' data='$usdmhd'><b>$$usdmhd</b></td>";

  $usdmhd = round($mining->usdbtc * $btcmhd,2);
	echo "<td align=right style='font-size: .8em; text-align:center;' data='$usdmhd'><b>$$usdmhd</b></td>";
/*
<td style='width: 100%; font-size: .8em;' data='$usdmhd'><b>Background:</b><br>nvidia-docker run -itd --rm --name cryptoandcoffee_$coin->symbol cryptoandcoffee/nvidia-docker-ccminer-klaust-c92 --algo $coin->algo -o stratum+tcp://operator.cryptoandcoffee.com:$port  -u $coin->master_wallet.$(hostname) -p c=$coin->symbol -R 1</td>
<td style='width: 100%; font-size: .8em;' data='$usdmhd'><b>Benchmark:</b><br>nvidia-docker run -it --rm --name cryptoandcoffee_$coin->symbol cryptoandcoffee/nvidia-docker-ccminer-klaust-c92 --benchmark --algo $coin->algo -o stratum+tcp://operator.cryptoandcoffee.com:$port  -u $coin->master_wallet.$(hostname) -p c=$coin->symbol -R 1</td>
*/

echo <<<END


   


<td style='width: 100%; font-size: .8em;' data='$usdmhd'><div class="">nvidia-docker run -it --rm --name cryptoandcoffee_$coin->symbol $coin->link_site --algo $coin->algo -o stratum+tcp://operator.cryptoandcoffee.com:$port -u $coin->master_wallet.$(hostname) -p c=$coin->symbol -R 1</div> <!-- The button used to copy the text -->
<button onclick="myFunction()">Quick Connect/Copy to Clipboard</button>
<input type="hidden" display="" value="nvidia-docker run -it --rm --name cryptoandcoffee_$coin->symbol $coin->link_site --algo $coin->algo -o stratum+tcp://operator.cryptoandcoffee.com:$port -u $coin->master_wallet.$(hostname) -p c=$coin->symbol -R 1" id="myInput">

</tr>


<!-- The text field -->


END;
	echo "</tr>";

}

if(controller()->admin && $services)
{
	foreach($services as $i=>$service)
	{
		$service_btcmhd = mbitcoinvaluetoa($service->price*100);

		echo "<tr class='ssrow'>";
		echo "<td width=18><img width=16 src='/images/btc.png'></td>";
		echo "<td><b>$service->name</b></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td align=right style='font-size: .8em;'><b>$service_btcmhd</b></td>";
		echo "</tr>";
	}
}

if(isset($price_rent) && $showrental)
{
	echo "<tr class='ssrow'>";
	echo "<td width=18><img width=16 src='/images/btc.png'></td>";
	echo "<td><b>Rental</b></td>";
	echo "<td align=right style='font-size: .8em;'><b>$amount_rent BTC</b></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td align=right style='font-size: .8em;'>$hashrate_jobs</td>";
	echo "<td align=right style='font-size: .8em;'><b>$price_rent</b></td>";
	echo "</tr>";

	unset($price_rent);
}


echo "</table>";

/*
echo '<p style="font-size: .8em;">
	&nbsp;*** estimated average time to find a block at full pool speed<br/>
	&nbsp;** approximate from the last 5 minutes submitted shares<br/>
	&nbsp;* 24h estimation from net difficulty in mBTC/MH/day (GH/day for sha & blake algos)<br>
</p>';
*/
echo "</div></div>";
echo <<<END
<p style='font-size: .8em;'>
nvidia-docker can be run a few different ways:<br> 
Run in the foreground: <b>nvidia-docker run -it --rm --name cryptoandcoffee_\$SYMBOL</b><br>
Run in the background: <b>nvidia-docker run -itd --rm --name cryptoandcoffee_\$SYMBOL</b><br>
You can always stop the container with: <b>docker kill cryptoandcoffee_\$SYMBOL</b>
</p>

END;

