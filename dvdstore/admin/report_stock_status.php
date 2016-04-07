<?
	if ( !$ADMIN_FLAG )
		die("No Access");

	$query = "SELECT title, stock_avail FROM dvd ORDER BY title";
	$results = mysqlQuery($query);
?>
<h2>Stock Status Report</h2>
<b>Date:</b> <? print(date("d/m/Y H:m:i"));?><br>

<table cellspacing="0" cellpadding="4" bordercolor="black" border="2" bgcolor="orange">
	<tr><td>
		<table cellpadding="0" cellspacing="0" border="0" class="listing">
	<tr><th>Item</th><th>Stock</th></tr>
<?
	$total = 0;
	foreach($results as $i=>$dvd)
	{
		if ( $i % 2 == 0 )
			$bgcolor="#ECECEC";
		else
			$bgcolor="";
		if ( $dvd['stock_avail'] <= 0 )
			$tdcolor="mistyrose";
		else if ( $dvd['stock_avail'] < 5 )
			$tdcolor="lemonchiffon";
		else 
			$tdcolor="";
		print("<tr bgcolor=\"$bgcolor\"><td>".$dvd['title']."</td><td");
		if ($tdcolor)
			print(" bgcolor=\"$tdcolor\"");
		print(" align=\"center\">".$dvd['stock_avail']."</td></tr>");
		$total += $dvd['stock_avail'];
	}
?>

<tr bgcolor="white">
	<td class="bold" style="border-top: double black">
		Total:
	</td>
	<td class="bold" align="center" style="border-top: double black">
		<? print($total);?>
	</td>
</tr>
</table>
<!-- footer -->
</td></tr></table>
