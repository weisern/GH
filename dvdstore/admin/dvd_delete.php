<?
	if ( !$ADMIN_FLAG )
		die("No Access");

	if ( $_POST['confirm'] )
	{
		$result = deleteDVD($_POST['barcode']);
		if ( $result )
			printf("<div class=\"success\">DVD deleted successfully</div>");
		include("dvds.php");
		die();
	}
	$barcode = $_GET["barcode"];
	if ( !$barcode )
	{
		print("<div class=\"error\">No barcode specified</div>");
		include("dvds.php");
		die();		
	}
	$details = getDVD($barcode);
?>
<h1>Delete Genre</h1>
<a href="admin.php?section=dvds" class="link">Back</a>
<form action="admin.php?section=dvd_delete" method="post">
<input type="hidden" name="barcode" value="<? print($barcode); ?>">
<?
	createPanel("Confirmation", "300");
	print("<center>Are you sure you want to delete:<br><b>".$details['title']."</b>?<br>");
	print("<input type=\"submit\" name=\"confirm\" value=\"Yes\" class=\"button\"></center><br>");
	closePanel();
?>
</form>