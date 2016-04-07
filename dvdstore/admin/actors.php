<?
	if ( !$ADMIN_FLAG )
		die("No Access");

	if ( $_GET['page'] > 0  )
		$page = $_GET['page'];
	else
		$page = 0;

	$actors = getActors($page);
	$totalrows = returnCount("actors");

	print("<h1>Actor Listing</h1>");
	print("<a href=\"admin.php?section=actor_add\" class=\"link\">Add new actor</a>");
	if ( is_null($actors) )
		die("<h2>There are currently no actors in the system.</h2>");
?>
	<table cellspacing="0" cellpadding="4" bordercolor="black" border="2" bgcolor="seagreen">
	<tr><td>
		<table cellpadding="5" cellspacing="0" border="0" class="listing">
		<tr>
			<th>Name</th>
			<th>Action</th>
		</tr>
<?
	foreach($actors as $i=>$actor)
	{
		print("<tr align=\"center\"");
		if ( $i % 2 == 0 )
			print(" bgcolor=\"ECECEC\"");
			print(" ><td align=\"left\"><a href=\"admin.php?section=actor_edit&actor_id=".$actor["actor_id"]."\">".
				$actor["first_name"]." ".$actor["last_name"]."</a></td>");
		print("<td>[<a href=\"admin.php?section=actor_delete&actor_id=".$actor["actor_id"]."\">Delete</a>]</td></tr>");
	}
	print("\n<tr bgcolor=\"white\" class=\"pages\"><td>&nbsp;");
	if ( $page > 0 )
		print("<a href=\"admin.php?section=actors&page=".($page-PAGE_SIZE)."\">&lt;&lt;&nbsp;Prev</a>");
	print("</td><td>&nbsp;");
	if ( $page+PAGE_SIZE < $totalrows )
		print("<a href=\"admin.php?section=actors&page=".($page+PAGE_SIZE)."\">Next&nbsp;&gt;&gt;</a>");
	print("</td></tr></table>\n</td></tr></table>\n");
	print("Total Actors: $totalrows");
?>