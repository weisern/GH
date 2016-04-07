<? 
	require_once("dvdstore.inc.php");

	$admin = checkAdmin();

	
	if ( $_GET['logout'] )
	{
		unset($_SESSION['admin']);
		header("Location: main.php");
		die(); // precaution only - not needed.
	}

	printAdminHeading("SuperDVD Administration");



	if ( $admin )

	{

		$ADMIN_FLAG = true;

		switch($_GET['section'])

		{

		case "dvds":

			include "admin/dvds.php";

			break;

		

		case "dvd_add":

			include "admin/dvd_add.php";

			break;



		case "dvd_edit":

			include "admin/dvd_edit.php";

			break;



		case "dvd_receipt":

			include "admin/dvd_receipt_in.php";

			break;

		case "dvd_delete":

			include "admin/dvd_delete.php";

			break;

		case "dvd_activate":
			$active = 1; //set dvd to active

			include "admin/dvd_activate_deactivate.php";

			break;

		case "dvd_deactivate":
			$active = 0; //set dvd to deactive

			include "admin/dvd_activate_deactivate.php";

			break;



		case "actors":

			include "admin/actors.php";

			break;			



		case "actor_add":

			include "admin/actor_add.php";

			break;

			

		case "actor_edit":

			include "admin/actor_edit.php";

			break;


		case "actor_delete":

			include "admin/actor_delete.php";

			break;


		case "directors":

			include "admin/directors.php";

			break;



		case "director_add":

			include "admin/director_add.php";

			break;



		case "director_edit":

			include "admin/director_edit.php";

			break;


		case "director_delete":

			include "admin/director_delete.php";

			break;


		case "genres":

			include "admin/genres.php";

			break;



		case "genre_add":

			include "admin/genre_add.php";

			break;

			

		case "genre_edit":

			include "admin/genre_edit.php";

			break;

		case "genre_delete":

			include "admin/genre_delete.php";

			break;

		case "reports":

			include "admin/reports.php";

			break;

		case "report_stock_status":

			include "admin/report_stock_status.php";

			break;

		case "report_sales_todate":

			include "admin/report_sales_todate.php";

			break;

		case "report_cust_demo":

			include "admin/report_cust_demo.php";

			break;

		case "report_top_profit":

			include "admin/report_top_profit.php";

			break;

		case "report_pop_dvd":

			include "admin/report_pop_dvd.php";

			break;

		case "report_pop_genre":

			include "admin/report_pop_genre.php";

			break;

		case "report_best_customer":

			include "admin/report_best_customer.php";

			break;

		case "import_xml":
			include("xml_functions.php");

			include("xml_import.php");

			break;
				

		default:

?>
			<table border="0" cellspacing="5" cellpadding="0" align="center">
			<tr><td align="center">
			<a href="admin.php?section=dvds"><img src="images/dvds.gif" border="0"><br>
			DVDs</a>
			</td><td colspan="2" align="center" valign="bottom">
			<a href="admin.php?section=actors"><img src="images/actors.gif" border="0"><br>
			Actors</a>
			</td><td align="center" valign="bottom">
			<a href="admin.php?section=directors"><img src="images/directors.gif" border="0"><br>
			Directors</a>
			</td></tr>
			<tr><td></td><td align="center" valign="bottom">
			<a href="admin.php?section=genres"><img src="images/genres.gif" border="0"><br>
			Genres</a>
			</td><td align="center">
			<a href="admin.php?section=reports"><img src="images/reports.gif" border="0"><br>
			Reports</a>
			</td><td></td></tr>
			<tr><td colspan="5" align="center">
			<a href="admin.php?section=import_xml"><img src="images/import.gif" border="0"><br>
			Import XML</a>	
			</td></tr>
			</table>
<?

			break;

		} //switch-clause

	} //if-clause

	else

		include("admin/loginform.php");

?>

</td></tr></table>