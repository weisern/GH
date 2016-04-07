<?
	if ( !$ADMIN_FLAG )
		die("No Access");
	if ( $_POST['submit'] )
	{
		$result = addActor($_POST['first_name'], $_POST['last_name']);
		if ( $result == TRUE )
		{
			printf("<div class=\"success\">Actor added successfully</div>");
			include("actors.php");
			die();
		}
	}
?>
<h1>Add a new Actor</h1>
<a href="admin.php?section=actors" class="link">Back</a>
<form action="admin.php?section=actor_add" method="post" onsubmit="return validate(this);">
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
			<input type="submit" name="submit" value="Add Actor" class="button">
		</td>
	</tr>
	</table>
</form>
<script language="javascript">
<!--
function validate(obj)
{		
	rvar = validate_notempty(obj.first_name, "Actor's first name(s)");
	if ( rvar )
		rvar = validate_notempty(obj.last_name, "Actor's last name");

	return rvar;
}
-->
</script>	