<?
	if ( !$ADMIN_FLAG )
		die("No Access");
	if ( $active == 1 )
	{
		modifyDVD($_GET['barcode'], array("active"=>1));
		printf("<div class=\"success\">DVD activated</div>");
	}else
	{
		modifyDVD($_GET['barcode'], array("active"=>0));
		printf("<div class=\"success\">DVD deactivated</div>");
	}
	include("dvds.php");
?>