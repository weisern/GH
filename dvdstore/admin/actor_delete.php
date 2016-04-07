<?
	if ( !$ADMIN_FLAG )
		die("No Access");

	if ( $_POST['confirm'] )
	{
		$result = deleteActor($_POST['actor_id']);
		printf("<div class=\"success\">Actor deleted successfully</div>");
		include("actors.php");
		die();
	}
	$actor_id = $_GET["actor_id"];
	if ( !$actor_id )
	{
		print("<div class=\"error\">No actor id specified</div>");
		include("actors.php");
		die();		
	}
	$details = getActor($actor_id);
	$name = $details['first_name']." ".$details['last_name'];
?>
<h1>Delete Actor</h1>

<a href="admin.php?section=actors" class="link">Back</a>
<form action="admin.php?section=actor_delete" method="post">
<input type="hidden" name="actor_id" value="<? print($actor_id); ?>">
<?
	createPanel("Confirmation", "300");
	print("<center>Are you sure you want to delete:<br><b>$name?</b><br>");
	print("<input type=\"submit\" name=\"confirm\" value=\"Yes\" class=\"button\"></center><br>");
	closePanel();
?>
</form>