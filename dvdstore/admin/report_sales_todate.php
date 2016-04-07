<?
	if ( !$ADMIN_FLAG )
		die("No Access");

	$query = "SELECT d.title, d.stock_avail, d.cost, d.sell_price, sum(c.quantity) as sales ".
		"FROM dvd d LEFT OUTER JOIN contains c ON c.barcode=d.barcode GROUP BY d.title ";
	$results = mysqlQuery($query);
?>
<h2>Stock Status Report</h2>
<b>Date:</b> <? print(date("d/m/Y H:m:i"));?><br>

<table cellspacing="0" cellpadding="4" bordercolor="black" border="2" bgcolor="orange">
	<tr><td>
		<table cellpadding="0" cellspacing="2" border="0" class="listing">
	<tr>
		<th>Item</th><th>Stock Level</th><th>Cost / unit</th><th>Price / unit</th>
		<th>Dispatches</th><th>Revenue</th><th>Profit/Loss</th><th>Liability</th>
	</tr>
<?
	$total = 0;
	foreach($results as $i=>$dvd)
	{
		if ( $i % 2 == 0 )
			$bgcolor="#ECECEC";
		else
			$bgcolor="white";
		$total_revenue += $revenue = ($dvd['sales']*$dvd['sell_price']);
		$total_profit += $profit = ($dvd['sell_price']-$dvd['cost'])*$dvd['sales'];
		$total_liability += $liability = ($dvd['stock_avail']*$dvd['cost']);
		$total_stock += $dvd['stock_avail'];
		$total_dispatches += $dvd['sales'];
		print("<tr bgcolor=\"$bgcolor\" align=\"center\"><td align=\"left\">".$dvd['title']."</td><td>".
			$dvd['stock_avail']."</td><td>$".$dvd['cost']."</td>".
			"<td>$".$dvd['sell_price']."</td><td>".$dvd['sales']."</td><td>$".$revenue."</td><td>$".
			$profit."</td><td>$".$liability."</td></tr>");
	}
?>
<tr bgcolor="lavendar">
	<td class="bold" style="border-top: double black">
		Total:
	</td>
	<td class="bold" align="center" style="border-top: double black">
		<? print($total_stock);?>
	</td>
	<td class="bold" align="center" style="border-top: double black">
		&nbsp;
	</td>
	<td class="bold" align="center" style="border-top: double black">
		&nbsp;
	</td>
	<td class="bold" align="center" style="border-top: double black">
		<? print($total_dispatches);?>
	</td>
	<td class="bold" align="center" style="border-top: double black">
		$<? print($total_revenue);?>
	</td>
	<td class="bold" align="center" style="border-top: double black">
		$<? print($total_profit);?>
	</td>
	<td class="bold" align="center" style="border-top: double black">
		$<? print($total_liability);?>
	</td>
</tr>
	</table>
<!-- footer -->
</td></tr></table>
<div class="success">Current Position:&nbsp;&nbsp;$<? print($total_revenue - $total_liability);?></div>
(Sales to date less liability of stock)
