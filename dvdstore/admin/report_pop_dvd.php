<?
	if ( !$ADMIN_FLAG )
		die("No Access");

	$query="SELECT title, SUM(quantity) AS number_bought FROM dvd d, contains c WHERE d.barcode".
		"=c.barcode GROUP BY title ORDER BY number_bought DESC LIMIT 10";
	$results = mysqlQuery($query);
?>
<h2>Popular DVDs</h2>
<b>Date:</b> <? print(date("d/m/Y H:m:i"));?><br>
<table cellspacing="0" cellpadding="4" bordercolor="black" border="2" bgcolor="orange">
	<tr><td>
		<table cellpadding="0" cellspacing="0" border="0" class="listing">
	<tr><th>Title</th><th>Number bought</th></tr>
<?
	foreach($results as $i=>$item)
	{
		if ( $i % 2 == 0 )
			$bgcolor="#ECECEC";
		else
			$bgcolor="";
		print("<tr bgcolor=\"$bgcolor\"><td><b>".($i+1)."</b>.&nbsp;".$item['title']."</td>".
		"<td align=\"center\">".$item['number_bought']."</td></tr>");
	}
?>
</table>
</td></tr></table>

<!-- footer -->
</td></tr></table>