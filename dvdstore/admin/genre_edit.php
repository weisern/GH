<?
	if ( !$ADMIN_FLAG )
		die("No Access");

	$genre_id = $_GET["genre_id"];
	if ( $_POST['submit'] )
	{
		$result = updateGenre($_POST['genre_id'], $_POST['description']);
		if ( $result == TRUE )
		{
			printf("<div class=\"success\">Genre updated successfully</div>");
			include("genres.php");
			die();
		}
		$genre_id = $_POST['genre_id'];
	}

	if ( !$genre_id )
	{
		print("<div class=\"error\">No genre id specified</div>");
		include("genres.php");
		die();		
	}
	$details = getGenre($genre_id);
?>
<h1>Edit Genre</h1>
<a href="admin.php?section=genres" class="link">Back</a>
<form action="admin.php?section=genre_edit" method="post" onsubmit="return validate(this);">
<input type="hidden" name="genre_id" value="<? print($genre_id); ?>">
	<table cellspacing="0" cellpadding="4" bordercolor="black" border="2" bgcolor="seagreen">
	<tr><td>
		<table cellpadding="5" cellspacing="0" border="0" class="listing">
		<tr>
			<th>Description</th>
			<td bgcolor="#ECECEC">
				<input type="text" name="description" size="40" maxlength="40" value="<? print($details['description']); ?>">
			</td>
		</tr>
		</table>
	</td></tr>
	<tr>
		<td align="center">
			<input type="submit" name="submit" value="Update Genre" class="button">
		</td>
	</tr>
	</table>
</form>
<script language="javascript">
<!--
function validate(obj)
{		
	return validate_notempty(obj.description, "Genre Description");
}
-->
</script>