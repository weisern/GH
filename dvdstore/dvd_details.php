<?
	include_once("dvdstore.inc.php");
	printCustomerHeading("DVD details");

	if ( !$_GET['barcode'] )
	{
		print("<div class=\"error\">No barcode specified.</div>");
		die();		
	}		
	$details = getDVD($_GET['barcode']);
	
	if ( $item_added )
		print("<div class=\"success\">".$details['title']." added to your basket</div>");
	displayDVD($details);
?>
<!-- footer -->
</td></tr></table>
