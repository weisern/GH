<?
	include("dvdstore.inc.php");

	if ( $_POST['username'] )
		login($_POST['username'], $_POST['password']);

	if ( !isLoggedIn() )
	{
		printCustomerHeading("Basket View");
		printLoginForm("basket_view.php");
		print("<!--footer--></td></tr></table>");
		die();
	}
	processBasketUpdates();

	printCustomerHeading("Basket View");
	printf("<h2 style=\"color: orange;\">Basket contents of ".$_SESSION['first_name']."</h2>");
	print("<h3>Enter quantity 0 to delete a DVD from yout basket.</h3>");
	printBasketContents();
	printNetscapeTableFix();
?>

<script language="javascript">
<!--

function validate_quantity(fieldobj)
{
	rvar = validate_notempty(fieldobj, "Quantity");
	if ( rvar )
		rvar = validate_number(fieldobj, "Quantity");
	return rvar;
}

-->
</script>
