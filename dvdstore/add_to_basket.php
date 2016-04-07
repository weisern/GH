<?
	include("dvdstore.inc.php");
	if ( !isLoggedIn() )
		Header("Location: main.php");

	if ( !$_GET['barcode'] )
	{
		include("main.php");
		print("<div class=\"error\">No barcode entered!</div>");
	}
	else
	{
		addToBasket($_SESSION['username'], $_GET['barcode']);
		$item_added = TRUE;
	}
	include("dvd_details.php");
?>
