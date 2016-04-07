<?
	if ( !$ADMIN_FLAG )
		die("No Access");
	if ( $_POST['submit'] )
	{
		$result = addDirector($_POST['first_name'], $_POST['last_name']);
		if ( $result == TRUE )
		{
			printf("<div class=\"success\">Director added successfully</div>");
			include("directors.php");
			die();
		}
	}	
?>
<h1>Add a new Director</h1>
<a href="admin.php?section=directors" class="link">Back</a>
<form action="admin.php?section=director_add" method="post" onsubmit="return validate(this);">
	<table cellspacing="0" cellpadding="4" bordercolor="black" border="2" bgcolor="seagreen">
	<tr><td>
		<table cellpadding="5" cellspacing="0" border="0" class="listing">
		<tr>
			<th>First Name</th>
			<td bgcolor="#ECECEC">
				<input type="text" name="first_name" size="20" maxlength="40">
			</td>
		</tr>
		<tr>
			<th>Last Name</th>
			<td>
				<input type="text" name="last_name" size="20" maxlength="40">
			</td>
		</tr>
		</table>
	</td></tr>
	<tr>
		<td align="center">
			<input type="submit" name="submit" value="Add Director" class="button">
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