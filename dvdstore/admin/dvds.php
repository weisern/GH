<?
	if ( !$ADMIN_FLAG )
		die("No Access");

	if ( $_GET['page'] > 0  )
		$page = $_GET['page'];
	else
		$page = 0;

	$dvds = getDVDs($page);
	$totalrows = returnCount("dvd");

	print("<h1>DVD Listing</h1>");
	print("<a href=\"admin.php?section=dvd_add\" class=\"link\">Add new DVD</a>");
	if ( is_null($dvds) )
		die("<h2>There are currently no DVDs in the system.</h2>");
?>
	<table cellspacing="0" cellpadding="4" bordercolor="black" border="2" bgcolor="seagreen">
	<tr><td>
		<table cellpadding="5" cellspacing="0" border="0" class="listing">
		<tr>
			<th>Title</th>
			<th>Cost</th>
			<th>Sell Price</th>
			<th>Quantity</th>
			<th>Active</th>
			<th>Action</th>
		</tr>
<?	
	foreach($dvds as $i=>$dvd)
	{
		print("<tr align=\"center\"");
		if ( $i % 2 == 0 )
			print(" bgcolor=\"#ECECEC\"");
			print(" ><td align=\"left\"><a href=\"admin.php?section=dvd_edit&page=$page&barcode=".$dvd["barcode"]."\">".
				$dvd["title"]."</a></td>".
				"<td>".$dvd["cost"]."</td>".
				"<td>".$dvd["sell_price"]."</td>".
				"<td>".$dvd["stock_avail"]."</td><td>");
			if ( $dvd["active"]==1 )
				print("<img src=\"images/tick.gif\"></td><td nowrap>[<a href=\"admin.php?section".
					"=dvd_deactivate&page=$page&barcode=".$dvd["barcode"]."\">Deactivate</a>]");
			else
				print("<img src=\"images/cross.gif\"></td><td nowrap>[<a href=\"admin.php?section".
					"=dvd_activate&page=$page&barcode=".$dvd["barcode"]."\">Activate</a>]");
		print("&nbsp;[<a href=\"admin.php?section=dvd_receipt&page=$page&barcode=".$dvd["barcode"]."\">Receipt-in</a>]".
			"&nbsp;[<a href=\"admin.php?section=dvd_delete&barcode=".$dvd["barcode"]."\">Delete</a>]</td></tr>");
	}
	print("\n<tr bgcolor=\"white\" class=\"pages\"><td>&nbsp;");
	if ( $page > 0 )
		print("<a href=\"admin.php?section=dvds&page=".($page-PAGE_SIZE)."\">&lt;&lt;&nbsp;Prev</a>");
	print("</td><td align=\"right\" colspan=\"5\">&nbsp;");
	if ( $page+PAGE_SIZE < $totalrows )
		print("<a href=\"admin.php?section=dvds&page=".($page+PAGE_SIZE)."\">Next&nbsp;&gt;&gt;</a>");
	print("</td></tr></table>\n</td></tr></table>\n");
	print("Total DVDs: $totalrows");
?>