<?
	if ( !$ADMIN_FLAG )
		die("No Access");

	$actor_id = $_GET["actor_id"];
	if ( $_POST['submit'] )
	{
		$result = updateActor($_POST['actor_id'], $_POST['first_name'], $_POST['last_name']);
		if ( $result == TRUE )
		{
			printf("<div class=\"success\">Actor updated successfully</div>");
			include("actors.php");
			die();
		}
		$actor_id = $_POST['actor_id'];
	}

	if ( !$actor_id )
	{
		print("<div class=\"error\">No actor id specified</div>");
		include("actors.php");
		die();		
	}
	$details = getActor($actor_id);
?>
<h1>Edit Actor</h1>
<a href="admin.php?section=actors" class="link">Back</a>
<form action="admin.php?section=actor_edit" method="post" onsubmit="return validate(this);">
<input type="hidden" name="actor_id" value="<? print($actor_id); ?>">
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
			<input type="submit" name="submit" value="Update Actor" class="button">
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