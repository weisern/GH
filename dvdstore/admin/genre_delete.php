<?
	if ( !$ADMIN_FLAG )
		die("No Access");

	if ( $_POST['confirm'] )
	{
		$result = deleteGenre($_POST['genre_id']);
		printf("<div class=\"success\">Genre updated successfully</div>");
		include("genres.php");
		die();
	}
	$genre_id = $_GET["genre_id"];
	if ( !$genre_id )
	{
		print("<div class=\"error\">No genre id specified</div>");
		include("genres.php");
		die();		
	}
	$details = getGenre($genre_id);
?>
<h1>Delete Genre</h1>

<a href="admin.php?section=genres" class="link">Back</a>
<form action="admin.php?section=genre_delete" method="post">
<input type="hidden" name="genre_id" value="<? print($genre_id); ?>">
<?
	createPanel("Confirmation", "300");
	print("<center>Are you sure you want to delete:<br><b>".$details['description']."?</b><br>");
	print("<input type=\"submit\" name=\"confirm\" value=\"Yes\" class=\"button\"></center><br>");
	closePanel();
?>
</form>