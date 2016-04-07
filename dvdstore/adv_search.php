<?
	include("dvdstore.inc.php");

	if ( $_POST['submit_check'] || $_GET['quick'] )
	{
		if ( $_GET['quick'] )
			$_POST['searchFieldValue'] = $_GET['search'];
		processSearchByFieldsForm();
		processSearchByGenreForm();
		die(); // won't print forms.
	}

	if ( $_GET['submit_check'])
	{
		processTitleSearchByAlphabet();
		die(); // won't print forms.
	}

	printCustomerHeading("Advanced Search");
?>

<script language="javascript">
<!--
function validate(obj)
{
	return validate_notempty(obj.searchFieldValue);
}
-->
</script>
<center>
<br>
<form method="post" action="adv_search.php" name="searchByFieldsForm" onSubmit="return validate(this);">
<?	createPanel("Search fields", "250"); ?>
	<table align="center">
		<tr>
			<td nowrap></td>
			<td><input type="text" name="searchFieldValue" size="15" maxlength="40"></td>
		</tr>
		<tr>
			<td nowrap>search by:</td>
			<td>
				<select name="searchFields">
					<option value="0">Any</option>
					<option value="dvdTitle">DVD title</option>
					<option value="actorName">Actor</option>
					<option value="directorName">Director</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input class="button" type="submit" 
					name="submit_check" value="search">
			</td>
		</tr>
	</table>
<? closePanel() ?>
</form>
<form method="post" action="adv_search.php" name="searchByGenreForm">
<?	createPanel("Search by Genre", "250"); ?>
	<table align="center">
		<tr>
			<td nowrap>search by:</td>
			<td>
			<? printGenreWidget(); ?>
			</td>
		</tr>
		<tr><td colspan="2" align="center">
			<input class="button" type="submit" name="submit_check" value="search">
		</td></tr>
	</table>
<? closePanel() ?>
</form>
</center>
<div class="help">
<li>Try searching for "Mo" under the <b>Any</b> category.
<li>Try searching for "Fight Club" or "End of Days" under the <b>DVD title</b> category.
<li>Try searching for "Tom Hanks" or "Julia Roberts" under <b>Actor</b> category.
<li>Try searching for "Frank Darabont" or "James Cameron" under <b>Director</b> category.
</div>
<!-- footer -->
</td></tr></table>

