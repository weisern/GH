<?
	include("dvdstore.inc.php");

	if ( $_POST['username'] )
		login($_POST['username'], $_POST['password']);

	if ( isLoggedIn() )
	{
		checkStockAvail($_SESSION['username']);
		printCheckoutDetails($_SESSION['username']);
	}else
	{
		printCustomerHeading("Checkout");
		printLoginForm("checkout.php");
		print("<!--footer--></td></tr></table>");
		die();
	}
?>



