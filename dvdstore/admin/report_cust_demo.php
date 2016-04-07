<?
	if ( !$ADMIN_FLAG )
		die("No Access");

	$query = "SELECT description as state, count(*) as count FROM customers c NATURAL JOIN states GROUP BY c.state_id";
	$geo = mysqlQuery($query);

	$query = "SELECT DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(date_of_birth)), '%Y')+0 AS age, COUNT(*) as count ".
		"FROM customers GROUP BY DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(date_of_birth)), '%Y')+0 ORDER BY age";

	$ages = mysqlQuery($query);
?>
<h2>Customer Demographics</h2>
<b>Date:</b> <? print(date("d/m/Y H:m:i"));?><br>

<table cellspacing="0" cellpadding="4" bordercolor="black" border="2" bgcolor="orange">
	<tr><td>
		<table cellpadding="0" cellspacing="0" border="0" class="listing">
	<tr><th colspan="2">Geographical</th></tr>
<?
	$total = 0;
	foreach($geo as $i=>$state)
	{
		if ( $i % 2 == 0 )
			$bgcolor="#ECECEC";
		else
			$bgcolor="";
		print("<tr bgcolor=\"$bgcolor\"><td>".$state['state']."</td>");
		print("<td align=\"center\" width=\"50\">".$state['count']."</td></tr>");
		$total += $state['count'];
	}

	$count = array(0, 0, 0, 0, 0, 0);
	foreach($ages as $i=>$age)
	{
		switch( $age['age'] )
		{
			case $age['age'] < 15:
				$count[0] += $age['count'];
				break;

			case $age['age'] >= 15 && $age['age'] <= 20:
				$count[1] += $age['count'];
				break;

			case $age['age'] > 20 && $age['age'] <= 25:
				$count[2] += $age['count'];
				break;

			case $age['age'] > 25 && $age['age'] <= 30:
				$count[3] += $age['count'];
				break;

			case $age['age'] > 30 && $age['age'] <= 40:
				$count[4] += $age['count'];
				break;

			default:
				$count[5] += $age['count'];
				break;
		}
	}
?>
	<tr><th colspan="2">Temporal</th></tr>
	<tr><td>Under 15 years old</td><td align="center"><? print($count[0]); ?></td></tr>
	<tr bgcolor="#ECECEC"><td>15 - 20 years old</td><td align="center"><? print($count[1]); ?></td></tr>
	<tr><td>21 - 25 years old</td><td align="center"><? print($count[2]); ?></td></tr>
	<tr bgcolor="#ECECEC"><td>26 - 30 years old</td><td align="center"><? print($count[3]); ?></td></tr>
	<tr><td>31 - 40 years old</td><td align="center"><? print($count[4]); ?></td></tr>
	<tr bgcolor="#ECECEC"><td>Over 40 years old</td><td align="center"><? print($count[5]); ?></td></tr>
</table>
</td></tr></table>
Total Customers:&nbsp;<b><? print($total); ?></b>
<!-- footer -->
</td></tr></table>