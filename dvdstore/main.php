<?
	include_once("dvdstore.inc.php");

	if ( $_POST['username'] )
		login($_POST['username'], $_POST['password']);
	else
		if ( $_GET['logout'] )
			logout();

	printCustomerHeading("Welcome to SuperDVD");
	if ( isLoggedIn() )
		printf("<h2 style=\"color: orange;\">Welcome ".$_SESSION['first_name'].",</h2>");
	else
		printLoginForm();

	$rand_dvd = getDVD(NULL, TRUE);
	printf("<hr>");
	displayDVD($rand_dvd);
	print("</td></tr></table>");


?>

