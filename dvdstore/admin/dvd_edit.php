<?
	if ( !$ADMIN_FLAG )
		die("No Access");

	$barcode = $_GET["barcode"];
	if ( $_POST['submit'] )
	{
		$result = updateDVD($_POST['barcode'], $_POST);
		if ( $result == TRUE )
		{
			printf("<div class=\"success\">DVD updated successfully</div>");
			$_GET['page'] = $_POST['page'];
			include("dvds.php");
			die();
		}
		$barcode = $_POST['barcode'];
	}
	if ( !$barcode )
	{
		print("<div class=\"error\">No barcode specified.</div>");
		include("dvds.php");
		die();		
	}		
	$details = getDVD($barcode);
?>
<h1>Edit DVD details</h1>
<a href="admin.php?section=dvds&page=<? print($_GET['page']); ?>" class="link">Back</a>
<form action="admin.php?section=dvd_edit" method="post" enctype="multipart/form-data" onsubmit="return validate(this);">
<input type="hidden" name="page" value="<? print($_GET['page']); ?>">
<input type="hidden" name="MAX_FILE_SIZE" value="30000">
	<table cellspacing="0" cellpadding="4" bordercolor="black" border="2" bgcolor="seagreen">
	<tr><td>
		<table cellpadding="5" cellspacing="0" border="0" class="listing">
		
		<tr>
			<th>Barcode</th>
			<td bgcolor="#ECECEC" colspan="2">
				<span class="form">
				<input type="text" name="barcode" size="20" maxlength="20" value="<? print($details['barcode']); ?>">
				</span>
			</td>
		</tr>
		<tr>
			<th>Title</th>
			<td colspan="2">
				<input type="text" name="title" size="30" maxlength="60" value="<? print($details['title']); ?>">
			</td>
		</tr>		
		<tr>
			<th>Synopsis</th>
			<td bgcolor="#ECECEC" colspan="2">
				<span class="textarea">
				<textarea name="synopsis" rows="5" cols="40"><? print($details['synopsis']); ?></textarea>
				</span>
			</td>
		</tr>		
		<tr>
			<th>Cost per unit</th>
			<td colspan="2">
				$<input type="text" name="cost" size="7" maxlength="7" value="<? print($details['cost']); ?>">
			</td>
		</tr>		
		<tr bgcolor="#ECECEC">
			<th>Unit sell price</th>
			<td colspan="2">
				$<input type="text" name="sell_price" size="7" maxlength="7" value="<? print($details['sell_price']); ?>">
			</td>
		</tr>		
		<tr>
			<th>Genre(s)</th>
			<td colspan="2">
<?
	if ( !is_null($details['genres']) )
		printGenreList(array_keys($details['genres']));
	else
		printGenreList();
?>
			</td>
		</tr>		
		<tr bgcolor="#ECECEC">
			<th>Director</th>
			<td colspan="2">
<?
		printDirectorList($details['director_id']);
?>
			</td>
		</tr>
		<tr>
			<th>Actors</th>
			<td colspan="2">
<?
	if ( !is_null($details['actors']) )
		printActorList(array_keys($details['actors']));
	else
		printActorList();
?>
				Hold down the CTRL-key to select multiple actors.
			</td>
		</tr>
		<tr bgcolor="#ECECEC">
			<th>Graphic</th>
			<td>
<?
			if ( file_exists("pictures/".$details['barcode'].".jpg") )
				$picture = "pictures/".$details['barcode'].".jpg";
			else
				$picture = "pictures/no_cover.gif";
			print("<img width=\"40\" src=\"$picture\" name=\"picturesrc\">");
?>
			</td>
			<td>
				<b>Change Graphic (*.jpg ONLY):</b><br>
				<input type="file" name="picture" size="20" onChange="picturesrc.src=this.value;">
			</td>
		</tr>	
		</table>
	</td></tr>
	<tr>
		<td align="center">
			<input type="submit" name="submit" value="Update DVD" class="button">
		</td>
	</tr>
	</table>
</form>
<script language="javascript">
<!--
function validate(obj)
{
	rvar = validate_number(obj.barcode, "DVD Barcode");
	if ( rvar )
		rvar = validate_notempty(obj.title, "Title");
	if ( rvar )
		rvar = validate_details(obj.synopsis, "Synopsis", 65535, false);
	if ( rvar )
		rvar = validate_number(obj.cost, "Cost per unit");
	if ( rvar )
		rvar = validate_number(obj.sell_price, "Unit sell price");
	if ( rvar )
		rvar = validate_checkbox(obj.elements['genres[]'], "Genre(s)");
	return rvar;
}
-->
</script>	