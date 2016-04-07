<?

	if ( !$ADMIN_FLAG )

		die("No Access");
	if ( $_POST['submit'] )
	{
		$result = addGenre($_POST['description']);
		if ( $result == TRUE )
			printf("<div class=\"success\">Genre added successfully</div>");
	}
	include("genres.php");

?>