<?
	if ( !$ADMIN_FLAG )
		die("No Access");

	if ( $_GET['page'] > 0  )
		$page = $_GET['page'];
	else
		$page = 0;

	$directors = getDirectors($page);
	$totalrows = returnCount("directors");

	print("<h1>Director Listing</h1>");
	print("<a href=\"admin.php?section=director_add\" class=\"link\">Add new director</a>");
	if ( is_null($directors) )
		die("<h2>There are currently no directors in the system.</h2>");
?>
	<table cellspacing="0" cellpadding="4" bordercolor="black" border="2" bgcolor="seagreen">
	<tr><td>
		<table cellpadding="5" cellspacing="0" border="0" class="listing">
		<tr>
			<th>First Name</th>
			<th>Action</th>
		</tr>
<?
	foreach($directors as $i=>$director)
	{
		print("<tr align=\"center\"");
		if ( $i % 2 == 0 )
			print(" bgcolor=\"ECECEC\"");
			print(" ><td align=\"left\"><a href=\"admin.php?section=director_edit&director_id=".$director["director_id"]."\">".
				$director["first_name"]." ".$director["last_name"]."</a></td>");
		print("<td>[<a href=\"admin.php?section=director_delete&director_id=".$director["director_id"]."\">Delete</a>]</td></tr>");
	}
	print("\n<tr bgcolor=\"white\" class=\"pages\"><td>&nbsp;");
	if ( $page > 0 )
		print("<a href=\"admin.php?section=directors&page=".($page-PAGE_SIZE)."\">&lt;&lt;&nbsp;Prev</a>");
	print("</td><td>&nbsp;");
	if ( $page+PAGE_SIZE < $totalrows )
		print("<a href=\"admin.php?section=directors&page=".($page+PAGE_SIZE)."\">Next&nbsp;&gt;&gt;</a>");
	print("</td></tr></table>\n</td></tr></table>\n");
	print("Total Directors: $totalrows");
?>