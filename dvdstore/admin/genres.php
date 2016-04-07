<?
	if ( !$ADMIN_FLAG )
		die("No Access");

	$genres = getGenres();
	print("<h1>Genre Listing</h1>");
	print("<form action=\"admin.php?section=genre_add\" method=\"post\" onsubmit=\"return validate(this);\">");
	createPanel("Add New Genre", "60");	
?>
	<center>
	<input type="text" name="description" size="20" maxlength="30"><br>
	<input type="submit" name="submit" value="Add Genre" class="button">
	</center>
<?
	closePanel();
	print("	</form>");

	if ( is_null($genres) )
		die("<h2>There are currently no genres in the system.</h2>");
?>
	<table cellspacing="0" cellpadding="4" bordercolor="black" border="2" bgcolor="seagreen">
	<tr><td>
		<table cellpadding="5" cellspacing="0" border="0" class="listing">
		<tr>
			<th>Genre</th>
			<th>Action</th>
		</tr>
<?
	foreach($genres as $i=>$genre)
	{
		print("<tr align=\"center\"");
		if ( $i % 2 == 0 )
			print(" bgcolor=\"ECECEC\"");
			print(" ><td align=\"left\"><a href=\"admin.php?section=genre_edit&genre_id=".$genre["genre_id"]."\">".
				$genre["description"]."</a></td>");
		print("<td>[<a href=\"admin.php?section=genre_delete&genre_id=".$genre["genre_id"]."\">Delete</a>]</td></tr>");
	}
	print("</table>\n</td></tr></table>\n");
?>
<script language="javascript">
<!--
function validate(obj)
{		
	return validate_notempty(obj.description, "Genre Description");
}
-->
</script>