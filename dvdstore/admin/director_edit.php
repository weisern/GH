<?
	if ( !$ADMIN_FLAG )
		die("No Access");
	$director_id = $_GET["director_id"];
	if ( $_POST['submit'] )
	{
		$result = updateDirector($_POST['director_id'], $_POST['first_name'], $_POST['last_name']);
		if ( $result == TRUE )
		{
			printf("<div class=\"success\">Director updated successfully</div>");
			include("directors.php");
			die();
		}
		$director_id = $_POST['director_id'];
	}

	if ( !$director_id )
	{
		print("<div class=\"error\">No director id specified</div>");
		include("directors.php");
		die();		
	}
	$details = getDirector($director_id);
	
?>
<h1>Edit Director</h1>
<a href="admin.php?section=directors" class="link">Back</a>
<form action="admin.php?section=director_edit" method="post" onsubmit="return validate(this);">
<input type="hidden" name="director_id" value="<? print($director_id); ?>">
	<table cellspacing="0" cellpadding="4" bordercolor="black" border="2" bgcolor="seagreen">
	<tr><td>
		<table cellpadding="5" cellspacing="0" border="0" class="listing">
		<tr>
			<th>First Name</th>
			<td bgcolor="#ECECEC">
				<input type="text" name="first_name" size="20" maxlength="40" value="<? print($details['first_name']); ?>">
			</td>
		</tr>
		<tr>
			<th>Last Name</th>
			<td>
				<input type="text" name="last_name" size="20" maxlength="40" value="<? print($details['last_name']); ?>">
			</td>
		</tr>
		</table>
	</td></tr>
	<tr>
		<td align="center">
			<input type="submit" name="submit" value="Update Director" class="button">
		</td>
	</tr>
	</table>
</form>
<script language="javascript">
<!--
function validate(obj)
{		
	rvar = validate_notempty(obj.first_name, "Director's first name(s)");
	if ( rvar )
		rvar = validate_notempty(obj.last_name, "Director's last name");

	return rvar;
}
-->
</script>