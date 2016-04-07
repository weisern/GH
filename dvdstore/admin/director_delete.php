<?
	if ( !$ADMIN_FLAG )
		die("No Access");

	if ( $_POST['confirm'] )
	{
		$result = deleteDirector($_POST['director_id']);
		printf("<div class=\"success\">Director deleted successfully</div>");
		include("directors.php");
		die();
	}
	$director_id = $_GET["director_id"];
	if ( !$director_id )
	{
		print("<div class=\"error\">No director id specified</div>");
		include("directors.php");
		die();		
	}
	$details = getDirector($director_id);
	$name = $details['first_name']." ".$details['last_name'];
?>
<h1>Delete Director</h1>
<a href="admin.php?section=directors" class="link">Back</a>
<form action="admin.php?section=director_delete" method="post">
<input type="hidden" name="director_id" value="<? print($director_id); ?>">
<?
	createPanel("Confirmation", "300");
	print("<center>Are you sure you want to delete:<br><b>$name?</b><br>");
	print("<input type=\"submit\" name=\"confirm\" value=\"Yes\" class=\"button\"></center><br>");
	closePanel();
?>
</form>