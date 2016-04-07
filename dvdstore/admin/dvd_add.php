<?
	if ( !$ADMIN_FLAG )
		die("No Access");
	if ( $_POST['submit'] )
	{
		$result = addDVD($_POST);
		if ( $result == TRUE )
		{
			printf("<div class=\"success\">DVD added successfully</div>");
			include("dvds.php");
			die();
		}
	}	
?>
<h1>Add a new DVD</h1>
<a href="admin.php?section=dvds" class="link">Back</a>
<form action="admin.php?section=dvd_add" method="post" name="dvd" enctype="multipart/form-data" onsubmit="return validate(this);">
<input type="hidden" name="MAX_FILE_SIZE" value="30000">
	<table cellspacing="0" cellpadding="4" bordercolor="black" border="2" bgcolor="seagreen">
	<tr><td>
		<table cellpadding="5" cellspacing="0" border="0" class="listing">
		<tr>
			<th>Barcode</th>
			<td bgcolor="#ECECEC" colspan="2">
				<input type="text" name="barcode" size="20" maxlength="20">
			</td>
		</tr>
		<tr>
			<th>Title</th>
			<td colspan="2">
				<input type="text" name="title" size="30" maxlength="60">
			</td>
		</tr>		
		<tr>
			<th>Synopsis</th>
			<td bgcolor="#ECECEC" colspan="2">
				<span class="textarea">
				<textarea name="synopsis" rows="5" cols="40"></textarea>
				</span>
			</td>
		</tr>		
		<tr>
			<th>Cost per unit</th>
			<td colspan="2">
				$<input type="text" name="cost" size="7" maxlength="7">
			</td>
		</tr>		
		<tr>
			<th>Unit sell price</th>
			<td bgcolor="#ECECEC" colspan="2">
				$<input type="text" name="sell_price" size="7" maxlength="7">
			</td>
		</tr>		
		<tr>
			<th>Genre(s)</th>
			<td colspan="2">
<?
		printGenreList();
?>
			</td>
		</tr>		
		<tr>
			<th>Director</th>
			<td bgcolor="#ECECEC" colspan="2">
<?
		printDirectorList();
?>
				<a href="admin.php?section=director_add">Add Director</a>
			</td>
		</tr>
		<tr>
			<th>Actors</th>
			<td colspan="2">
<?
		printActorList();
?>
				Hold down the CTRL-key to select multiple actors.<br>
				<a href="admin.php?section=actor_add">Add Actor</a>
			</td>
		</tr>
		<tr bgcolor="#ECECEC">
			<th>Graphic</th>
			<td>
<?
			print("<img width=\"40\" name=\"picturesrc\" src=\"pictures/no_cover.gif\">");
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
			<input type="submit" name="submit" value="Add DVD" class="button">
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