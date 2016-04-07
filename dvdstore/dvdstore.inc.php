<?
/********************************************
	DEFINTIONS
********************************************/
define(MAX_FILE_UPLOAD_SIZE, 30000); //max file size is 30K
define(UPLOAD_DIR, "/home/studproj/351/2003s1/i1n2c3a/dave/pictures/"); //upload dir
define(ADMIN_PASSWORD, "quasar");
define(PAGE_SIZE, 15);
session_start();

function notYet()
{print("this page or function is not yet implemented.");
}

/*****************************************************************************
			    SECURTY FUNCTIONS
******************************************************************************/

/*
* isLoggedIn()
*  Returns TRUE if the user is logged in,
*	FALSE if not logged in.
*/
function isLoggedIn()
{
	if ( $_SESSION['username'] )
		return TRUE;
	else
		return FALSE;
}

/*
* Logout()
* Logs out the current user.
*/
function Logout()
{
	unset($_SESSION['username']);
	unset($_SESSION['basket']);
}

/*
* Login()
* Attepmts to log in with given credentials.
*	Returns TRUE on success and FALSE (and an error message)
*	on failure.
* $username - [string] Username to try and login with.
* $password - [string] password to try and login with
*/
function Login($username, $password)
{
	$query = "SELECT username,first_name FROM customers WHERE username='$username' AND password='".md5($password)."'";
	$result = mysqlQuery($query);
	$username = $result[0]['username'];
	if ( $username )
	{

		$_SESSION['username'] = $username;
		$_SESSION['first_name'] = $result[0]['first_name'];
		return TRUE;
	}
	printf("<div class=\"error\">Username or password is incorrect.</div>");
	return FALSE;
}

/*
* checkAdmin()
* Checks if the current logged in user is an administrator.
* 	Returns TRUE on success, FALSE on failure.
*	Prints error message on failure.
*/
function checkAdmin()
{
	if ( $_SESSION['admin'] == TRUE )
		return TRUE;
	else if ( $_POST['admin_password'] == ADMIN_PASSWORD )
	{
		$_SESSION['admin'] = TRUE;
		return TRUE;
	}
	printf("<div class=\"error\">The password entered is incorrect.</div>");
	return FALSE;
}

/*****************************************************************************
			    CUSTOMER-HANDELING FUNCTIONS
******************************************************************************/

/*
* addCustomer():
* Adds a new user with details in $input_ar into the customers table and adds
* the customer's credit card details as well into the credit_cards table.
*/
function addCustomer($input_ar)
{
	$first_name = trim($input_ar['first_name']);
	$last_name = trim($input_ar['last_name']);
	$username = trim($input_ar['username']);
	$email = trim($input_ar['email']);
	$street = trim($input_ar['street']);
	$suburb = trim($input_ar['suburb']);
	$postcode = trim($input_ar['postcode']);
	$state_id = $input_ar['state_id']; // Assume state exists :)
	$card_name = trim($input_ar['card_name']);
	$card_number = trim($input_ar['card_number']);
	$date_of_birth = reverseDate($input_ar['date_of_birth']);
	$expiry_date = reverseDate($input_ar['expiry_date']);
	$password = $input_ar['password1'];

	$check = "SELECT username FROM customers WHERE username='$username'";
	if ( mysqlExists($check) )
	{
		printf("<div class=\"error\">The username '$username' already exists. Try another.</div>");
		return false;
	}

	// Note I am relying the javascript to ensure that all fields are entered
	if ( $password != $input_ar['password2'] )
	{
		printf("<div class=\"error\">The passwords must match</div>");
		return false;
	}

	$query = "INSERT INTO customers (username, password, first_name, last_name, date_of_birth,".
		"email, street, suburb, postcode, state_id) VALUES('$username', '".md5($password)."',".
		"'$first_name', '$last_name', '$date_of_birth', '$email', '$street', '$suburb',".
		"'$postcode', $state_id)";
	mysqlInsert($query);

	$credit_check = "SELECT card_number FROM credit_cards WHERE card_number=$card_number";
	if ( !mysqlExists($credit_check) )
	{
		$query = "INSERT INTO credit_cards (card_number, card_name, expiry_date) VALUES(".
			"$card_number, '$card_name', '$expiry_date')";
		mysqlInsert($query);
	}
	$query = "INSERT INTO pays_with (card_number, username) VALUES($card_number, '$username')";
	mysqlInsert($query);
	return true;
}

/*
	reverseDate():
	Takes dates of two different formats and converts them to
	MYSQL-complient format. Accepts dd/mm/yyyy and mm/yy formats.
*/
function reverseDate($date)
{
	$posy = strrpos($date, "/");
	$year = substr($date, $posy+1);
	$posm = strpos($date, "/");
	if ( $posm != $posy )
	{
		$month = substr($date, $posm+1, ($posy-$posm-1));
		$day = substr($date, 0, $posm);
	}
	else
	{
		$month = substr($date, 0, $posy);
		$year = "20".$year;
		$day = "01";
	}
	return $year."-".$month."-".$day;
}

/*
* printStatesList():
* Prints the HTML drop down options form for states and
* can also have $state_id selected.
*/
function printStatesList($state_id=NULL)
{
	$query = "SELECT * FROM states";
	$states = mysqlQuery($query, "state_id");
	print("<select name=\"state_id\">\n");
	if ( is_null($state_id) )
		print("\t<option value=\"\" selected>Please select</option>\n");
	foreach($states as $id=>$state)
	{
		print("\t<option value=\"$id\"");
		// Check if item should be checked
		if ( $state_id == $id )
			print(" selected");
		print(">".$state['description']."</option>\n");
	}
	print("</select>");
}

/*
* getCustomer():
* Selects the listed customer attributes from the customers table for a given $username.
*/
function getCustomer($username)
{
	$query = "SELECT username, first_name, last_name, email, street, suburb, postcode, state_id, ".
		"DATE_FORMAT(date_of_birth, '%d/%m/%Y') AS date_of_birth FROM customers ".
		"WHERE username='$username'";
	$results = mysqlQuery($query);
	return $results;
}

/*
* getShipToDetails():
* Gets the name and address of a customer for the purpose of printing shipment destination
* details.
*/
function getShipToDetails($username)
{
	$query = "SELECT c.first_name, c.last_name, c.street, c.suburb, c.postcode, s.description ".
		"FROM customers c, states s WHERE c.username='$username' AND c.state_id=s.state_id ";
	$results = mysqlQuery($query);
	return $results;
}

/*
* getCreditCard():
* Get the credit card details for a given $username.
*/
function getCreditCard($username)
{
	$query = "SELECT cc.card_number, cc.card_name, DATE_FORMAT(cc.expiry_date, '%m/%y') AS expiry_date ".
		"FROM credit_cards cc, pays_with p, customers c WHERE c.username='$username' AND ".
		"p.username=c.username AND p.card_number=cc.card_number ";
	$results = mysqlQuery($query);
	return $results;
}

/*
* addToBasket():
* Checks that a DVD to be added by a customer is active and if so adds it to the customer's
* basket.  If the DVD is already in the basket, increment the quantity by 1.  Only adds 1
* DVD at a time for a given $barcode to the basket of a customer with $username.
*/
function addToBasket($username, $barcode)
{
	$check = "SELECT barcode FROM dvd WHERE barcode=$barcode AND ".
		"active=1";
	if ( !mysqlExists($check) )
	{
		print("<div class=\"error\">The barcode does not exist!</div>");
		return false;
	}
	$query1 = "SELECT quantity FROM shopping_trolley WHERE username=".
		"'$username' AND barcode=$barcode";
	$result = mysqlQuery($query1);
	if (!$result)
		$query2 = "INSERT INTO shopping_trolley (username, barcode)".
			" VALUES('$username', $barcode)";
	else
	{
		$new_qty = $result[0]['quantity']+1;
		$query2 = "UPDATE shopping_trolley SET quantity=$new_qty".
			" WHERE username='$username' AND barcode=$barcode";
	}
	mysqlInsert($query2);
	$_SESSION['basket'] = getBasket($username);
	return true;
}

/*
* deleteFromBasket():
* Delete a DVD with $barcode for customer with $username and unset() the basket session
* variable used by the mini-basket.
*/
function deleteFromBasket($username, $barcode)
{
	$query = "DELETE FROM shopping_trolley WHERE username=".
		"'$username' AND barcode=$barcode";
	mysqlInsert($query);
	unset($_SESSION['basket']);
	return true;
}

/*
* getBasketAll():
* Gets items in shopping_trolley of $username and pulls out $identifier as the top array index.
* The relevant items consist of the listed attributes.
*/
function getBasketAll($username, $identifier=NULL)
{
	$query = "SELECT s.barcode, s.quantity, d.active, d.stock_avail, d.title, d.sell_price ".
		"FROM shopping_trolley s, dvd d ".
		"WHERE username='$username' AND d.barcode = s.barcode";
	$results = mysqlQuery($query, $identifier);
	return $results;
}

/*
* getBasket():
* Only interested in displaying the DVD titles in the shopping_trolley of $username.
*/
function getBasket($username)
{
	$query = "SELECT s.barcode, d.title FROM shopping_trolley s, dvd d ".
		"WHERE username='$username' AND d.barcode = s.barcode";
	return mysqlQuery($query);
}

/*
* viewBasket():
* Sets basket session variable so that mini-basket can display DVDs of customer $username.
*/
function viewBasket($username)
{
	if ( !$_SESSION['basket'] )
		$_SESSION['basket']=getBasket($username);
	return $_SESSION['basket'];
}

/*
* updateBasket():
* Customers can update the quantity of DVDs in their basket by entering a number 1-99,
* number 0 is checked for before calling this function.  This may update if the number is
* <= the stock available amount.  Otherwise useful warning and message follows.
*/
function updateBasket($username, $barcode, $new_quantity, $stock_avail, $active, $title)
{
	if ( is_null($active) )
	{
		print("<p>We apologise, even though you normally can't add a deactivated DVD, ".
			"$title has been deactivated after you put it in your basket.</p>");
	}elseif ( $new_quantity <= $stock_avail )
	{
		$query = "UPDATE shopping_trolley SET quantity=$new_quantity".
			" WHERE username='$username' AND barcode=$barcode";
		mysqlInsert($query);
	}else
	{
		print("<p>Could not update $title quantity to $new_quantity. Only $stock_avail left.</p>");
	}
}

/*
* printCheckoutDetails():
* Control point for calling other functions to print checkout details.
*/
function printCheckoutDetails($username)
{
	printf("<h2 style=\"color: orange;\">Payment and Shipping</h2>");
	printShipTo($username);
	printCreditCardDetails($username);
	printBasketItemsAndPrice($username);
	printPurchaseButton();
	printNetscapeTableFix();
}

/*
* printShipTo():
* Prints the shipment details for $username.
*/
function printShipTo($username)
{
	$results = getShipToDetails($username);
	print("<h3>Ship To:</h3>".$results[0]['first_name']." ".$results[0]['last_name'].
		"<br>\n".$results[0]['street'].", ".$results[0]['suburb'].
		"<br>\n".$results[0]['postcode']." ".$results[0]['description']."<br>");
	print("<a href=\"profile_view.php\">Edit</a> shipping details.<br>");

}

/*
* printCreditCardDetails():
* Print credit card details for $username.
*/
function printCreditCardDetails($username)
{
	$results = getCreditCard($username);
	print("<h3>Credit Card Details:</h3>".
		"Card Name: ".$results[0]['card_name'].
		"<br>\nCard Number: ".$results[0]['card_number'].
		"<br>\nExpiry Date: ".$results[0]['expiry_date']."<br>");
	print("<a href=\"profile_view.php\">Edit</a> credit card details.<br>");
}

/*
* printBasketItemsAndPrice():
* Prints the DVDs to be purchased with no means to change them except a link to the
* My Basket page.
*/
function printBasketItemsAndPrice($username)
{
	$basket = getBasketAll($username);
	if ( is_null($basket) )
	{
		print("<h3>Basket is empty.<h3>");
	}else
	{
		print("<h3>Purchase Items:</h3>");
		$total=0;
		print("<table cellspacing=\"0\" cellpadding=\"4\" bordercolor=\"black\" border=\"2\" bgcolor=\"white\">".
		"\n\t<tr><th>Title</th><th>Price</th><th>Quantity</th><th>Subtotal</th></tr>");
		foreach($basket as $i=>$item)
		{
			$sell_price = $item['sell_price'];
			$quantity = $item['quantity'];
			$subtotal = $sell_price * $quantity;
			$total += $subtotal;
			print("\n\t<tr>\n\t\t<td>".$item['title']."</td>".
			"\n\t\t<td>$".$sell_price."</td>".
			"\n\t\t<td align=\"center\">".$quantity."</td>".
			"\n\t\t<td align=\"right\">$".money_format('%+n', $subtotal)."</td>".
			"\n\t</tr>");
		}
		print("\n\t<tr><td><b>Total:</b></td><td colspan=\"3\" align=\"right\">$".$total."</td></tr>".
		"</table>");
		print("<a href=\"basket_view.php\">Edit</a> basket details.<br>or<br>");
		print("Continue <a href=\"adv_search.php\">shopping</a> for DVDs.<br>");
	}//end else-clause
}

/*
* printPurchaseButton():
* Prints the "Purchase Now" button.
*/
function printPurchaseButton()
{
	print("<form action=\"checkout.php\" method=\"post\">".
	"<input type=\"submit\" name=\"submit\" value=\"Purchase Now\" class=\"button\">".
	"</form>");
}

/*
* printNetscapeTableFix():
* at the very end of all HTML outputs close off the table printCustomerHeading() left open.
*/
function printNetscapeTableFix()
{
	print("</td></tr></table>");
}

/*
* checkStockAvail():
* Control point for checking DVD quantities to be purchased and performing the transaction.
*/
function checkStockAvail($username)
{
	if ( $_POST['submit'] )
	{
		$basket = getBasketAll($username, 'barcode');
		if ( checkQuantitiesToBePurchased($basket) )/*** all quantities are fine ***/
		{
			purchaseDVDs($username, $basket);
			die();
		}else
		{
			/*** print errorneous dvds ***/
			printCustomerHeading("Checkout");
			printBasketItemsAndPrice($username);
			die();
		}
	}
	else
		printCustomerHeading("Checkout");
}

/*
* checkQuantitiesToBePurchased():
* Check that there is enough stock for the quantities to be purchased.
*/
function checkQuantitiesToBePurchased($basket)
{
	$quantities_fine = TRUE;
	foreach ($basket as $barcode=>$item)
	{
		if ( $item['quantity'] > $item['stock_avail'] )
		{
			printf("<div class=\"error\">Cannot purchase ".$item['quantity'].
			" of ".$item['title']." as there are only ".$item['stock_avail'].
			" left</div>");
			$quantities_fine = FALSE;
		}
	}
	return $quantities_fine;
}

/*
* purchaseDVDs():
* Performing the transaction by creating a new order, reducing the inventory stock amount of
* the appropriate DVDs, emptying the customer's basket, and printing a order number receipt.
*/
function purchaseDVDs($username, $basket)
{

	$query1 = "INSERT INTO orders (username, ship_datetime) VALUES('$username', NOW())";
	mysqlInsert($query1);
	$query2 = "SELECT LAST_INSERT_ID() AS order_no FROM orders";
	$result = mysqlQuery($query2);
	$orderno = $result[0]['order_no'];

	foreach ($basket as $barcode=>$item)
	{
		$new_stock_avail = $item['stock_avail'] - $item['quantity'];
		$query = "UPDATE dvd SET stock_avail=$new_stock_avail ".
			"WHERE barcode=$barcode";
		mysqlInsert($query);
		$query = "INSERT INTO contains (order_number, barcode, quantity) VALUES(".
			"$orderno, $barcode, ".$item['quantity'].")";
		mysqlInsert($query);
	}

	//empty the shopping trolley
	$query3 = "DELETE FROM shopping_trolley WHERE username='$username'";
	mysqlInsert($query3);
	unset($_SESSION['basket']);
	printCustomerHeading("Checkout");
	print("<h2>Purchase Successful</h2>\nYour order completed successfully.<br>\nYour order number is <b>$orderno<b/>.");
	print("<hr>Click <a href=\"adv_search.php\">here</a> to continue shopping.");

}

/*
* processBasketUpdates():
* Process the form on the basket_view.php page.  Which allows customers to delete DVDs from
* their basket by entering quantity of 0, or modifying the quantity of a certain DVD in their
* basket.
*/
function processBasketUpdates()
{
	if ($_POST['submit'])
	{
		$basket_ar = getBasketAll($_SESSION['username'], 'barcode');
		/*** relies on barcode being the only number in index. ***/
		foreach ($_POST as $index=>$item)
		{
			if ( is_numeric($index) )/*** $index is a barcode ***/
			{
				/*** $item is then a quantity ***/
				if ( $item == '0' )
				{
					deleteFromBasket($_SESSION['username'], $index);
				}else
				{
					updateBasket($_SESSION['username'], $index, $item,
						$basket_ar[$index]['stock_avail'],
						$basket_ar[$index]['active'],
						$basket_ar[$index]['title']);
				}
			}
		}//end foreach-clause
	}//end if-clause
}

/*
* printBasketContents():
* Prints a table showing the DVDs in a customer's basket with an input form for each DVD
* to change its amount.  Prints price, subtotal and total for the DVDs.
*/
function printBasketContents()
{
	$basket_ar = getBasketAll($_SESSION['username']);
	if ( is_null($basket_ar) )
	{
		print("<h3>Basket is empty.<h3>");
	}else
	{
		$total=0;
		printStartTable();
		foreach($basket_ar as $i=>$item)
		{
			$sell_price = $item['sell_price'];
			$quantity = $item['quantity'];
			$subtotal = $sell_price * $quantity;
			$total += $subtotal;
			print("\n\t<tr>\n\t\t<td>".$item['title']."</td>".
			"\n\t\t<td>$".$sell_price."</td>".
			"\n\t\t<td>");
			printQuantityForm($item['barcode'], $quantity);
			print("\n\t\t</td>\n\t\t<td align=\"right\">$".money_format('%+n', $subtotal).
			"\n\t\t</td>\n\t</tr>");
		}
		print("\n\t<tr><td><b>Total:</b></td><td align=\"right\" colspan=\"3\">$".$total."</td></tr>".
		"\n\t<tr><td colspan=\"4\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"Update\" ".
		"class=\"button\"></td></tr>");
		print("<tr><td colspan=\"4\" align=\"center\"><input type=\"button\" value=\"Proceed to checkout\" ".
			"class=\"button\" onclick=\"window.location='checkout.php';\"></td></tr>\n\t\t\t</form></table>");
	}//end else-clause
}

/*
* printStartTable():
* A break down of the above print function printBasketContents()
*/
function printStartTable()
{
	print("<table cellspacing=\"0\" cellpadding=\"4\" bordercolor=\"black\" border=\"2\" bgcolor=\"white\">".
	"\n\t<tr><th>Title</th><th>Price</th><th>Quantity</th><th>Subtotal</th></tr>");
}

/*
* printQuantityForm():
* A break down of the above print function printBasketContents()
*/
function printQuantityForm($barcode, $quantity)
{
	print("\n\t\t\t<form action=\"basket_view.php\" method=\"post\" onsubmit=\"return validate_quantity(this[0]);\">".
		"\n\t\t\t\t<input type=\"text\" name=\"$barcode\" size=\"2\" maxlength=\"2\" ".
		"\n\t\t\t\tvalue=\"$quantity\">");
}

/*****************************************************************************
			    DVD-HANDELING FUNCTIONS
******************************************************************************/
/*
* getDVDs():
* Returns an array of all the DVDs.
* The results are "paged" - ie only returns a subset of all the DVDs,
* allowing the user to "page" through the whole table, rather than always
* pulling the whole table.
* $start - [int] The record number to start retrieving from (starts at 0).
*/
function getDVDs($start)
{
	$query = "SELECT barcode,title,cost,sell_price,stock_avail,active FROM dvd ORDER BY title".
		" LIMIT $start, ".PAGE_SIZE;
	$results = mysqlQuery($query);
	return $results;
}

/*
* getDVD():
* Returns a particular DVD. Can also retureive a random DVD (in which
*	case the $barcode  input is ignored).
*	Also retrieves all the genres and actors for this DVD.
* $barcode - [int] the barcode of the DVD to retrieve.
* $random - [boolean] Whether to randomly select a DVD out of the database.
*/
function getDVD($barcode, $random=FALSE)
{
	if ( $random )
		$query1 = "SELECT *, CONCAT(first_name,' ',last_name) as director FROM dvd LEFT OUTER".
			" JOIN directors ON dvd.director_id=directors.director_id WHERE dvd.active=1".
			" ORDER BY RAND() LIMIT 1";
	else
		$query1 = "SELECT *, CONCAT(first_name,' ',last_name) as director FROM dvd LEFT OUTER".
			" JOIN directors ON dvd.director_id=directors.director_id WHERE barcode='$barcode'";
	$results = mysqlQuery($query1);
	$dvd = $results[0];		//only one row is returned, so don't need an array of arrays.
	$barcode = $dvd['barcode']; // only needed for RANDOM, but doesn't hurt non-random query.

	$query2 = "SELECT g.genre_id, description FROM is_type_of i, genres g WHERE i.barcode='$barcode'".
		" AND g.genre_id=i.genre_id";
	$query3 = "SELECT a.actor_id, a.first_name, a.last_name FROM has_stars_of s, actors a WHERE s.barcode='$barcode'".
		" AND a.actor_id=s.actor_id";
	$dvd['genres'] = mysqlQuery($query2, "genre_id");
	$dvd['actors'] = mysqlQuery($query3, "actor_id");
	return $dvd;
}

/*
* addDVD():
* Adds a new DVD to the database. Checks to make
* 	sure that the same barcode does not exist.
*	Also inserts associations for actors and genres.
*	Handles the uploading of the picture.
* $barcode - [int] the barcode of the DVD to add.
* $input_ar - [array] an array of the rest of the properties of
*	the DVD to insert.
*/
function addDVD($input_ar)
{
	$barcode = $input_ar['barcode'];
	$title = trim($input_ar['title']);
	$synopsis = trim($input_ar['synopsis']);
	$cost = $input_ar['cost'];
	$sell_price = $input_ar['sell_price'];
	$director_id = $input_ar['director_id'];
	/* Ignore errors with join() from null arrays (ie: user didn't
	 select any genres and/or actors) since dealt with below */
	$actors = @join($input_ar['actors'], ", ");
	$genres = @join($input_ar['genres'], ", ");

	if ( !$director_id )
		$director_id = 'NULL';
	if ( !$title || !$cost || !$sell_price || !$genres )
	{
		printf("<div class=\"error\">All fields must be entered (including at least one genre)</div>");
		return false;
	}
	$check = "SELECT barcode from dvd where barcode=$barcode";
	if ( mysqlExists($check) )
	{
		printf("<div class=\"error\">There is already a DVD with the barcode: $barcode !</div>");
		return false;
	}

	$queries[0] = "INSERT INTO dvd (barcode, title, synopsis, sell_price, cost, director_id, active) VALUES($barcode".
		", '$title', '$synopsis', $sell_price, $cost, $director_id, 0)";
	$queries[1] = "INSERT INTO is_type_of (barcode, genre_id) SELECT $barcode AS barcode, g.genre_id ".
		"FROM genres g WHERE genre_id IN ($genres)";
	if ( $actors )
		$queries[2] = "INSERT INTO has_stars_of (barcode, actor_id) SELECT $barcode AS barcode, a.actor_id ".
			"FROM actors a WHERE actor_id IN ($actors)";
	if ( $_FILES['picture']['size'] > 0 )
	{
		if ( $_FILES['picture']['size'] <= MAX_FILE_UPLOAD_SIZE )
			if ( strpos($_FILES['picture']['type'], "jpeg") !== false )
				$upload_success = move_uploaded_file($_FILES['picture']['tmp_name'],
					UPLOAD_DIR.$barcode.".jpg");
		if ( !$upload_success )
		{
			/* Don't need to delete temp file as PHP will automaticlly do this */
			printf("<div class=\"error\">The uploaded file was either too big".
				" or was not a JPEG.</div>");
			return false;
		}
		chmod(UPLOAD_DIR.$barcode.".jpg", 0764);
	}

	foreach ($queries as $query)
		$result = mysqlInsert($query) or
			mysql_ErrorMsg("Unable to perform update:<b> $query</b>");
	return true;
}

/*
* updateDVD():
* Updates a DVD to the database.
*	Also updates associations for actors and genres.
*	Handles the uploading of the picture.
* $barcode - [int] the barcode of the DVD to add.
* $input_ar - [array] an array of the rest of the properties of
*	the DVD to update.
*/
function updateDVD($barcode, $input_ar)
{
	$title = trim($input_ar['title']);
	$synopsis = trim($input_ar['synopsis']);
	$cost = $input_ar['cost'];
	$sell_price = $input_ar['sell_price'];
	$director_id = $input_ar['director_id'];
	/* Ignore errors with join() from null arrays (ie: user didn't
	 select any genres and/or actors) since dealt with below */
	$actors = @join($input_ar['actors'], ", ");
	$genres = @join($input_ar['genres'], ", ");

	if ( !$director_id )
		$director_id = 'NULL';
	if ( !$title || !$cost || !$sell_price || !$genres )
	{
		printf("<div class=\"error\">All fields must be entered (including at least one genre)</div>");
		return false;
	}
	$queries[0] = "UPDATE dvd set barcode=$barcode, title='$title', synopsis='$synopsis', ".
		"sell_price=$sell_price, cost=$cost, director_id=$director_id WHERE barcode=$barcode";
	$queries[1] = "DELETE FROM is_type_of WHERE barcode=$barcode AND genre_id NOT IN ($genres)";
	$queries[2] = "INSERT IGNORE INTO is_type_of (barcode, genre_id) SELECT $barcode AS barcode, g.genre_id ".
		"FROM genres g WHERE genre_id IN ($genres)";
	if ( !$actors ) /* Delete all actors for this DVD... */
		$queries[3] = "DELETE FROM has_stars_of WHERE barcode=$barcode";
	else
	{	/* ... or modify as per given list */
		$queries[3] = "DELETE FROM has_stars_of WHERE barcode=$barcode AND actor_id NOT IN ($actors)";
		$queries[4] = "INSERT IGNORE INTO has_stars_of (barcode, actor_id) SELECT $barcode AS barcode, a.actor_id ".
			"FROM actors a WHERE actor_id IN ($actors)";
	}
	if ( $_FILES['picture']['size'] > 0 )
	{
		if ( $_FILES['picture']['size'] <= MAX_FILE_UPLOAD_SIZE )
			if ( strpos($_FILES['picture']['type'], "jpeg") !== false )
				$upload_success = move_uploaded_file($_FILES['picture']['tmp_name'],
					UPLOAD_DIR.$barcode.".jpg");
		if ( !$upload_success )
		{
			/* Don't need to delete temp file as PHP will automaticlly do this */
			printf("<div class=\"error\">The uploaded file was either too big".
				" or was not a JPEG.</div>");
			return false;
		}
		chmod(UPLOAD_DIR.$barcode.".jpg", 0764);
	}

	foreach ($queries as $query)
		$result = mysqlInsert($query) or
			mysql_ErrorMsg("Unable to perform update:<b> $query</b>");
	return true;
}

/*
* modifyDVD():
* This function is extremly different than updateDVD.
* 	Has almost no sanity checks and DOES NOT handle pictures.
* 	Will not update actors or genres. This function only touches the
* 	"dvd" table in the database.
* 	Typically used to increase the number of stock or activate/
*	deactivate the DVD.
*
* $barcode - [int] the barcode of the DVD to add.
* $input_ar - [array] an array of the rest of the properties of
*	the DVD to update.
*/
function modifyDVD($barcode, $input_ar)
{
	$query = "UPDATE dvd set";
	foreach ( $input_ar as $field=>$value )
		$query .= " $field = $value,";
	$query = substr($query, 0, -1)." WHERE barcode=$barcode";
	$result = mysqlInsert($query) or
			mysql_ErrorMsg("Unable to perform update:<b> $query</b>");
	return true;
}

/*
* deleteDVD():
* Firstly checks whether there have been any purchases or
*	any shopping trolleys with this DVD. If there is
*	prints an error message and return FALSE.
*	Otherwise deletes the DVD (and associations with actors
*	and genres) and returns TRUE on success.
* $barcode - barcode of the DVD to delete
*/
function deleteDVD($barcode)
{
	$check1 = "SELECT barcode FROM contains WHERE barcode=$barcode";
	$check2 = "SELECT barcode FROM shopping_trolley WHERE barcode=$barcode";
	if ( mysqlExists($check1) || mysqlExists($check2) )
	{
		printf("<div class=\"error\">Cannot delete DVD - there is at ".
		"least one order or shopping trolley that contains this DVD.</div>");
		return false;
	}
	$queries[0] = "DELETE FROM dvd WHERE barcode=$barcode";
	$queries[1] = "DELETE FROM has_stars_of WHERE barcode=$barcode";
	$queries[2] = "DELETE FROM is_type_of WHERE barcode=$barcode";

	foreach ($queries as $query)
		$result = mysqlInsert($query) or
			mysql_ErrorMsg("Unable to perform delete:<b> $query</b>");
	@unlink("pictures/".$barcode.".jpg"); //ignore error in case there is no picture
	return true;
}

/*
* displayDVD():
* Prints out the details of a DVD for the _customer's_ view.
* $details - [array] Array of information retreived from
*	getDVD() function.
*/
function displayDVD($details)
{
	/*** SETUP VARIABLES FOR FORMATTING ***/
	if ( $details['stock_avail'] > 0 )
		$avail = "Currently in stock";
	else
		$avail = "Out of stock";

	foreach($details['genres'] as $row)
		$genres .= $row['description'].", ";
	$genres = substr($genres, 0, -2);


	if ( $details['actors'] )
	{
		foreach($details['actors'] as $row)
			$actors .= $row['first_name']." ".$row['last_name'].", ";
		$actors = substr($actors, 0, -2);
	}

	print("<h2>".$details['title']."</h2>");
	if ( file_exists("pictures/".$details['barcode'].".jpg") )
		$picture = "pictures/".$details['barcode'].".jpg";
	else
		$picture = "pictures/no_cover.gif";
	print("<img width=\"40\" src=\"$picture\" border=\"0\" align=\"left\">");
	/*** END SETUP VARIABLES FOR FORMATTING ***/
?>
	<table border="0" cellspacing="2" cellpadding="0">
	<tr>
		<td width="70" class="bold">
			Price:
		</td>
		<td class="price">
			$<? print($details['sell_price']); ?>
		</td>
		<td align="center" bgcolor="lavender">
		<? if ( isLoggedIn() && $details['stock_avail'] > 0 )
			print("<a href=\"add_to_basket.php?barcode=".
				$details['barcode']."\">Add to Basket</a>");
		?>
		</td>
	</tr>
	<tr>
		<td class="bold">
			Shipping:
		</td>
		<td>
			<i>Free</i>
	</tr>
	<tr>
		<td class="bold">
			Availability:
		</td>
		<td>
			<? print($avail); ?>
		</td>
	</tr>
	</table><BR>
<?
	if ( $actors )
		print("<span class=\"h3\">Starring:</span>&nbsp;".$actors."<br>");
	if ( $details['director'] )
		print("<span class=\"h3\">Directed by:</span>&nbsp;".$details['director']."<br>");
	print("<span class=\"h3\">Genre(s):</span>&nbsp;".$genres);
	print("<h3>Synopsis</h3>");
	print(str_replace("\n", "<BR>", $details['synopsis']));
}

/*****************************************************************************
			    DIRECTOR-HANDELING FUNCTIONS
******************************************************************************/
/*
* getDirectors():
* Returns an array of all the directors.
* The results are "paged" - ie only returns a subset of all the directors,
* allowing the user to "page" through the whole table, rather than always
* pulling the whole table.
* $start - [int] The record number to start retrieving from (starts at 0).
*/
function getDirectors($start=0)
{
	$query = "SELECT * FROM directors ORDER BY last_name, first_name LIMIT $start, ".PAGE_SIZE;
	$results = mysqlQuery($query);
	return $results;
}

/*
* getDirector():
* Returns a particular director.
* $director_id - [int] the director id to retrieve.
*/
function getDirector($director_id)
{
	$query = "SELECT * FROM directors where director_id=$director_id";
	$results = mysqlQuery($query);
	return $results[0];
}

/*
* addDirector():
* Adds a new Director to the database. Checks to make
* sure that the same name does not exist.
* $fname - [string] the first name of Director to add.
* $lname - [string] the last name of Director to add.
*/
function addDirector($fname, $lname)
{
	if ( !$fname || !$lname )
	{
		printf("<div class=\"error\">All fields must be entered</div>");
		return false;
	}
	$fname = trim($fname);
	$lname = trim($lname);
	$check = "SELECT director_id FROM directors WHERE first_name='$fname' AND last_name='$lname'";
	if ( mysqlExists($check) )
	{
		printf("<div class=\"error\">Director '$fname $lname' already exists!</div>");
		return false;
	}
	$query = "INSERT INTO directors (first_name, last_name) VALUES('$fname', '$lname')";
	$result = mysqlInsert($query) or
		mysql_ErrorMsg("Unable to perform insert:<b> $query</b>");
	return true;
}

/*
* updateDirector():
* Updates the name of a particular director
* $director_id - [int] director id to update
* $fname - [string] new first name of the Director.
* $lname - [string] new last name of the Director.
*/
function updateDirector($director_id, $fname, $lname)
{
	if ( !$fname || !$lname )
	{
		printf("<div class=\"error\">All fields must be entered</div>");
		return false;
	}
	$fname = trim($fname);
	$lname = trim($lname);
	$query = "UPDATE directors set first_name='$fname', last_name='$lname' WHERE director_id=$director_id";
	$result = mysqlInsert($query) or
		mysql_ErrorMsg("Unable to perform update:<b> $query</b>");
	return true;
}

/*
* deleteDirector():
* Firstly checks whether there are any DVDs have this director.
* 	If not, it will delete the Director, otherwise print an error
*	message and return FALSE. Returns TRUE on success.
* $director_id - director id to delete
*/
function deleteDirector($director_id)
{
	$check = "SELECT barcode FROM dvd where director_id=$director_id";
	if ( mysqlExists($check) )
	{
		printf("<div class=\"error\">Cannot delete director - there is at least one DVD that is directored by this director.</div>");
		return false;
	}
	$query = "DELETE FROM directors WHERE director_id=$director_id";
	$result = mysqlInsert($query) or
		mysql_ErrorMsg("Unable to delete:<b> $query</b>");
	return true;
}

/*
* printDirectorList():
* Prints a SELECT widget of directors with the given director
*	selected in the widget.
* $director_id - [int] director id to select.
*/
function printDirectorList($director_id=NULL)
{
	$query = "SELECT * FROM directors ORDER BY last_name, first_name";
	$directors = mysqlQuery($query, "director_id");
	print("<select name=\"director_id\">\n");
	if ( is_null($director_id) )
		print("\t<option value=\"\" selected>None</option>\n");
	else
		print("\t<option value=\"\">None</option>\n");
	foreach($directors as $id=>$director)
	{
		print("\t<option value=\"$id\"");
		// Check if item should be checked
		if ( $director_id == $id )
			print(" selected");
		print(">".$director['first_name']." ".$director['last_name']."</option>\n");
	}
	print("</select>");
}

/*****************************************************************************
			    ACTOR-HANDELING FUNCTIONS
******************************************************************************/
/*
* getActors():
* Returns an array of all the actors.
* The results are "paged" - ie only returns a subset of all the actors,
* allowing the user to "page" through the whole table, rather than always
* pulling the whole table.
* $start - [int] The record number to start retrieving from (starts at 0).
*/
function getActors($start=0)
{
	$query = "SELECT * FROM actors ORDER BY last_name, first_name LIMIT $start, ".PAGE_SIZE;
	$results = mysqlQuery($query);
	return $results;
}

/*
* getActor():
* Returns a particular actor.
* $actor_id - [int] the actor id to retrieve.
*/
function getActor($actor_id)
{
	$query = "SELECT * FROM actors where actor_id=$actor_id";
	$results = mysqlQuery($query);
	return $results[0];
}

/*
* addActor():
* Adds a new Actor to the database. Checks to make
* sure that the same name does not exist.
* $fname - [string] the first name of Actor to add.
* $lname - [string] the last name of Actor to add.
*/
function addActor($fname, $lname)
{
	if ( !$fname || !$lname )
	{
		printf("<div class=\"error\">All fields must be entered</div>");
		return false;
	}
	$fname = trim($fname);
	$lname = trim($lname);
	$check = "SELECT actor_id FROM actors WHERE first_name='$fname' AND last_name='$lname'";
	if ( mysqlExists($check) )
	{
		printf("<div class=\"error\">Actor '$fname $lname' already exists!</div>");
		return false;
	}
	$query = "INSERT INTO actors (first_name, last_name) VALUES('$fname', '$lname')";
	$result = mysqlInsert($query) or
		mysql_ErrorMsg("Unable to perform insert:<b> $query</b>");
	return true;
}

/*
* updateActor():
* Updates the name of a particular actor
* $actor_id - [int] actor id to update
* $fname - [string] new first name of the Actor.
* $lname - [string] new last name of the Actor.
*/
function updateActor($actor_id, $fname, $lname)
{
	if ( !$fname || !$lname )
	{
		printf("<div class=\"error\">All fields must be entered</div>");
		return false;
	}
	$fname = trim($fname);
	$lname = trim($lname);
	$query = "UPDATE actors set first_name='$fname', last_name='$lname' WHERE actor_id=$actor_id";
	$result = mysqlInsert($query) or
		mysql_ErrorMsg("Unable to perform update:<b> $query</b>");
	return true;
}

/*
* deleteActor():
* Firstly checks whether there are any DVDs have this actor starrring in it.
* 	If not, it will delete the Actor, otherwise print an error
*	message and return FALSE. Returns TRUE on success.
* $actor_id - actor id to delete
*/
function deleteActor($actor_id)
{
	$check = "SELECT barcode FROM has_stars_of where actor_id=$actor_id";
	if ( mysqlExists($check) )
	{
		printf("<div class=\"error\">Cannot delete actor - there is at least one DVD associated to this actor.</div>");
		return false;
	}
	$query = "DELETE FROM actors WHERE actor_id=$actor_id";
	$result = mysqlInsert($query) or
		mysql_ErrorMsg("Unable to delete:<b> $query</b>");
	return true;
}

/*
* printActorList():
* Prints a MULTIPLE-SELECT widget of actors with the given actors
*	selected in the widget.
* $search_ar - [array] of actors to select.
*/
function printActorList($search_ar=NULL)
{
	$query = "SELECT * FROM actors ORDER BY last_name, first_name";
	$actors = mysqlQuery($query, "actor_id");
	print("<select name=\"actors[]\" multiple size=\"6\">\n");
	if ( is_null($search_ar) )
		print("\t<option value=\"\" selected>None</option>");
	else
		print("\t<option value=\"\">None</option>");
	foreach($actors as $id=>$actor)
	{
		print("\t<option value=\"$id\"");
		// Check if item should be checked
		if ( !is_null($search_ar) && in_array($id,$search_ar) )
			print(" selected");
		print(">".$actor['first_name']." ".$actor['last_name']."</option>\n");
	}
	print("</select>");
}

/*****************************************************************************
			    GENRE-HANDELING FUNCTIONS
******************************************************************************/
/*
* getGenres():
* Returns an array of all the genres.
*/
function getGenres()
{
	$query = "SELECT * FROM genres";
	$results = mysqlQuery($query);
	return $results;
}

/*
* getGenre():
* Returns a particular genre.
* $genre_id - [int] the genre id to retrieve.
*/
function getGenre($genre_id)
{
	$query = "SELECT * FROM genres where genre_id=$genre_id";
	$results = mysqlQuery($query);
	return $results[0];
}

/*
* addGenre():
* Adds a new Genre to the database. Checks to make
* sure that the same name does not exist.
* $description - [string] the name of Genre to add.
*/
function addGenre($description)
{
	if ( !$description )
	{
		printf("<div class=\"error\">The description must be entered</div>");
		return false;
	}
	$description = trim($description);

	$check = "SELECT description FROM genres WHERE description='$description'";
	if ( mysqlExists($check) )
	{
		printf("<div class=\"error\">Genre '$description' already exists!</div>");
		return false;
	}
	$query = "INSERT INTO genres (description) VALUES('$description')";
	$result = mysqlInsert($query) or
		mysql_ErrorMsg("Unable to perform insert:<b> $query</b>");
	return true;
}

/*
* updateGenre():
* Updates the name of a particular genre
* $genre_id - [int] genre_id to update
* $description - [string] new description to name the Genre.
*/
function updateGenre($genre_id, $description)
{
	if ( !$description )
	{
		printf("<div class=\"error\">The description must be entered</div>");
		return false;
	}
	$description = trim($description);
	$query = "UPDATE genres set description='$description' WHERE genre_id=$genre_id";
	$result = mysqlInsert($query) or
		mysql_ErrorMsg("Unable to perform update:<b> $query</b>");
	return true;
}

/*
* deleteGenre():
* Firstly checks whether there are any DVDs using this genre.
* 	If not it will delete the GEnre, otherwise print an error
*	message and return FALSE. Returns TRUE on success.
* $genre_id - genre_id to delete
*/
function deleteGenre($genre_id)
{
	$check = "SELECT barcode FROM is_type_of where genre_id=$genre_id";
	if ( mysqlExists($check) )
	{
		printf("<div class=\"error\">Cannot delete genre - there is at least one DVD that is in this genre.</div>");
		return false;
	}
	$query = "DELETE FROM genres WHERE genre_id=$genre_id";
	$result = mysqlInsert($query) or
		mysql_ErrorMsg("Unable to delete:<b> $query</b>");
	return true;
}

/*
* printGenreList():
* Prints out a list of CHECKBOXES for al the existing genres.
*	Takes an array input of genre_ids - any genre_id inputed
* 	will set that CHECKBOX to be ticked.
* $search_ar - optional array of genre_ids to default as ticked.
*/
function printGenreList($search_ar=NULL)
{
	$item = 0;
	$query = "SELECT * FROM genres ORDER BY description";
	$genres = mysqlQuery($query, "genre_id");

	print("<table cellpadding=\"0\" cellspacing=\"1\"><tr>");
	foreach($genres as $id=>$genre)
	{
		if ( $item % 3 == 0 ) // start new row every 3rd item
			print("</tr>\n<tr>");
		print("<td>\n\t<input class=\"listing\" type=\"checkbox\" name=\"genres[]\"");
		// Check if item should be checked
		if ( !is_null($search_ar) && in_array($id,$search_ar) )
			print(" checked");
		print(" value=\"$id\">\n".$genre['description']."</td>\n");
		$item++;
	}
	print("</tr></table>");
}

/*
* printGenreWidget():
* Prints a SELECT widget of all the genres.
*/
function printGenreWidget()
{
	$query = "SELECT * FROM genres ORDER BY description";
	$genres = mysqlQuery($query, "genre_id");
	print("<select name=\"genres\">\n");
	foreach($genres as $id=>$genre)
	{
		print("\t<option value=\"".$genre['description']."\"");
		print(">".$genre['description']."</option>\n");
	}
	print("</select>\n");
}

/*****************************************************************************
				SEARCH FUNCTIONS
******************************************************************************/

/*
* processTitleSearchByAlphabet():
* When an alphabet in the A-Z panel search is clicked, get DVDs with the title starting
* with the chosen alphabet.
*/
function processTitleSearchByAlphabet()
{
	if ( $_GET['type']=="title" )
	{
		$alphabet = $_GET['search_field'];
		$results = searchTitleByAlphabet($alphabet);
		listSearchField($results, $alphabet);
	}
}

/*
* searchTitleByAlphabet():
* Get DVDs with the title starting with $alphabet.
*/
function searchTitleByAlphabet($alphabet)
{
	$query = "SELECT title, barcode, sell_price FROM dvd WHERE title LIKE '$alphabet%' AND active=1";
	$results = mysqlQuery($query);
	return $results;
}

/*
* processSearchByFieldsForm():
* Processes a search for a given input searchFieldValue and constraints set in the drop down
* menu.  Constraints are shown in the case statements.
*/
function processSearchByFieldsForm()
{
	if ( ($_POST['searchFieldValue'])!="" ) //check in case javascript validate_notempty(); fails
	{
		switch($_POST['searchFields'])
		{
			case "0":
				$result1 = searchByDVDTitle($_POST['searchFieldValue']);
				$result2 = parseAndProcessName($_POST['searchFieldValue'], "actors");
				$result3 = parseAndProcessName($_POST['searchFieldValue'], "directors");
				$result4 = array_unique2( array_merge($result1, $result2, $result3) );
				listSearchField($result4, $_POST['searchFieldValue']);
				break;
			case "dvdTitle":
				$results = searchByDVDTitle($_POST['searchFieldValue']);
				listSearchField($results, $_POST['searchFieldValue']);
				break;
			case "actorName":
				$results = parseAndProcessName($_POST['searchFieldValue'], "actors");
				listSearchField($results, $_POST['searchFieldValue']);
				break;
			case "directorName":
				$results = parseAndProcessName($_POST['searchFieldValue'], "directors");
				listSearchField($results, $_POST['searchFieldValue']);
				break;
		}// end switch-clause
	}// end if-clause
}

/*
* The below two functions (recursivemakehas(),
* array_unique2()) are taken from the www.php.net
* site in the user_comments section.
* These functions are used by processSearchByFieldsForm() to remove duplicates after merging
* multiple arrays.  Although they can be used in general to remove duplicates.
*/
function recursivemakehash($tab)
{
 if(!is_array($tab))
   return md5($tab);
 $p = '';
 foreach($tab as $a => $b)
   $p .= sprintf('%08X%08X', crc32($a), crc32(recursivemakehash($b)));
 return $p;
}

function array_unique2($input)
{
 $dumdum = array();
 foreach($input as $a => $b)
   $dumdum[$a] = recursivemakehash($b);
 $newinput = array();
foreach(array_unique($dumdum) as $a => $b)
   $newinput[$a] = $input[$a];
 return $newinput;
}

/*
* searchByDVDTitle():
* Searches for partial DVD titles given by $searchInput.
*/
function searchByDVDTitle($searchInput)
{
	$query = "SELECT title, barcode, sell_price FROM dvd WHERE title LIKE '%$searchInput%' AND active=1";
	$results = mysqlQuery($query);
	return $results;
}

/*
* searchProcessedName():
* Given a control argument $actorsOrdirectors will search for DVDs in the relevant tables
* using the constraints in $partialNameQuery.
*/
function searchProcessedName($partialNameQuery, $actorsOrdirectors)
{
	if ( $actorsOrdirectors == "actors" )
	{
		$query = "SELECT DISTINCT d.title, d.barcode, d.sell_price FROM dvd d, actors a, has_stars_of s ".
			"WHERE d.barcode=s.barcode AND s.actor_id=a.actor_id ".
			"AND $partialNameQuery AND active=1".
			" LIMIT 0,30 ";
	}else if ( $actorsOrdirectors == "directors" )
	{
		$query = "SELECT d.title, d.barcode, d.sell_price FROM dvd d, directors t ".
			"WHERE d.director_id=t.director_id ".
			"AND $partialNameQuery AND active=1".
			" LIMIT 0,30 ";
	}
	$results = mysqlQuery($query);
	return $results;
}

/*
* parseAndProcessName():
* Search for DVDs with the likeness of actor or director names given by $searchInput.
* The names can consist of multiple space separated strings but with the total length
* restrictions of the input form.
*/
function parseAndProcessName($searchInput, $actorsOrdirectors)
{
	$name_array = explode(" ", $searchInput);
	$name_array_size = sizeof($name_array);

	foreach( $name_array as $name )
		$name_ar[] = "(first_name LIKE '%$name%' OR last_name LIKE '%$name%')";

	$name_str = join(" AND ", $name_ar);
	$results = searchProcessedName($name_str, $actorsOrdirectors);
	return $results;
}

/*
* listSearchField():
* Prints the results of successful searches done for the form with name="searchByFieldsForm".
* If only 1 DVD is the results, display the details immediately using the dvd_details.php
* page.  Otherwise, list the multiple search results, without detail, in a plesant to read manner.
*/
function listSearchField($results, $searchInput)
{
	$num=sizeof($results);
	if( $num > 0 )
	{
		if ( $num == 1 )
			header("Location: dvd_details.php?barcode=".$results[0]['barcode']);
		else
			printResults($results, $searchInput);
	}else
	{
		printCustomerHeading("Advanced Search Results");
		print("<h2>No results found for $searchInput </h2>\n<!-- footer -->\n</td></tr></table>");
	}
}

/*
* processSearchByGenreForm():
* Calls two other functions to process the form with name="searchByGenreForm".
*/
function processSearchByGenreForm()
{
	if ( !is_null($_POST['genres']) )
	{
		$results=searchByGenre($_POST['genres']);
		listGenreSearch($results, $_POST['genres']);
	}
}

/*
* searchByGenre():
* Get all the DVDs for the genre given by $genre_description.
*/
function searchByGenre($genre_description)
{
	$query = "SELECT d.title, d.barcode, d.sell_price FROM dvd d, is_type_of t, genres g ".
		"WHERE g.description='$genre_description' AND d.active='1' AND ".
		"g.genre_id=t.genre_id AND t.barcode=d.barcode AND active=1 LIMIT 0,30";
	$results = mysqlQuery($query);
	return $results;
}

/*
* listGenreSearch():
* Prints the results of successful searches done for the form with name="searchByGenreForm".
* If only 1 DVD is the results, display the details immediately using the dvd_details.php
* page.  Otherwise, list the multiple search results, without detail, in a plesant to read manner.
*/
function listGenreSearch($results, $description)
{

	$num=sizeof($results);
	if( $num > 0 )
	{
		if ( $num == 1 )
			header("Location: dvd_details.php?barcode=".$results[0]['barcode']);
		else
			printResults($results, $description);
	}else
	{
		printCustomerHeading("Advanced Search Results");
		print("No listing for DVDs in the genre of ".$description);
	}
}

/*
* printResults():
* List the multiple search results, without detail, in a plesant to read manner.
*/
function printResults($results, $searchInput)
{
	$i = 0;
	printCustomerHeading("Advanced Search Results");
	print("\n\n<h2>DVDs found for $searchInput</h2>Found ".count($results)." DVDs<br>\n".
		"<table border=\"0\" cellspacing=\"4\" cellpading=\"0\">\n<tr>\n");
	foreach($results as $item)
	{
		if ( file_exists("pictures/".$item['barcode'].".jpg") )
			$picture = "pictures/".$item['barcode'].".jpg";
		else
			$picture = "pictures/no_cover.gif";
		if ( $i % 2 == 0 )
			print("</tr>\n<tr>");
		else
			print("<td width=\"10\">&nbsp;</td>");
		print("<td><b>".($i+1).".&nbsp;</b><a href=\"dvd_details.php?barcode=".$item['barcode'].
			"\"><img width=\"40\" src=\"$picture\" border=\"0\"".
			" align=\"top\"></a></td><td valign=\"top\">"."<a href=\"dvd_details.php?barcode=".
			$item['barcode']."\">".$item['title']."</a><br>\n~&nbsp;$".$item['sell_price']."\n</td>\n");
		$i++;
	}
	print("</tr></table>\n<!-- footer -->\n</td></tr></table>");
}

/*****************************************************************************
				MYSQL FUNCTIONS
******************************************************************************/

/*
* checkConn():
* Uses a global variable to check if there is currently an open connection
* to the database in the instance of this script. If there isn't
* this fucntion opens a new database connection. Increases efficiency of DB calls.
*/
function checkConn()
{
	global $MYSQL_CONN, $CONNECTED;
	if ( $CONNECTED )
		return; //no need to connect to DB if a connection exists

	$MYSQL_CONN=@mysql_connect("localhost:8080","droch","jinkrey") or
			mysql_ErrorMsg("Unable to connect to mysql server: localhost");
	@mysql_select_db("droch",$MYSQL_CONN) or
			mysql_ErrorMsg("Unable to select database: droch");
	$CONNECTED = true;
}

/*
* mysqlQuery():
* Executes the given query on the database. Returns the results in an
* array of arrays (ie table). If the optional $identifier column is specified
* then the keys of the array are filled with the $identifier values.
* $query - [string] SQL query to be executed over database.
* $identifier - [string] column name in a table to fill the keys of the array with.
* 	The values in this specified column *must* be UNIQUE (optional arg).
*/
function mysqlQuery($query, $identifier=NULL)
{
	global $MYSQL_CONN;
	checkConn();

	$id=@mysql_query($query,$MYSQL_CONN) or
			mysql_ErrorMsg("Unable to perform query:<b> $query</b>");
	if ( is_null($identifier) ) // check optional parameter
		while ( $row = mysql_fetch_assoc($id) )
			$results[] = $row; // add each row to the result array
	else
	{
		while ( $row = mysql_fetch_assoc($id) )
		{
			$curr = $row[$identifier];	//save the identifier to a temp variable
			unset($row[$identifier]);	//remove the identifier from the array
			$results[$curr] = $row;		//assign the row (less the identifier) to
		}					// the result array
	}
	mysql_free_result($id); // free resources
	return $results;
}

/*
* mysqlInsert():
* Executes the given query to INSERT/UPDATE/DELETE.
*	Returns TRUE on success, FALSE on failure.
* $query - [string] the SQL query to be executed.
*/
function mysqlInsert($query)
{
	global $MYSQL_CONN;
	checkConn();

	$result=@mysql_query($query,$MYSQL_CONN);
	if ( $result )
		return true;
	else
		return false;
}

/*
* mysqlExists():
* Checks to see if a specified query exists.
*	Return TRUE if results are returned, FALSE otherwise.
* $query - [string] SQL query to be executed and checked.
*/
function mysqlExists($query)
{
	global $MYSQL_CONN;
	checkConn();

	$result = @mysql_query($query,$MYSQL_CONN);
	if ( @mysql_num_rows($result) )
		return TRUE;
	else
		return FALSE;
}

/*
* returnCount():
* This is aspecilised databsae query. Returns the row count for
* a given table.
* $tablename - [string] name of the table.
*/
function returnCount($tablename)
{
	$query = "SELECT count(*) as count FROM $tablename";
	$result = mysqlQuery($query);
	return $result[0]["count"];
}

/*
* mysql_ErrorMsg():
* Prints out a standard error message for any MYSQL failures/errors.
* Also halts execution of the script.
* $msg - [string] particular message to print out.
*/
function mysql_ErrorMsg($msg){
	$now = date("d/m/Y H:i:s");

	// Get out of html constraints so we can see the message
	echo("</ul></ul></ul></dl></dl></dl></ol></ol></ol>\n");
	echo("</table></table></table></script></script></script>\n");
	// Display the error message
	$text ="<center><div color=\"#ff0000\" size=+1><p>A MySQL error has occured.</div>".
			"<div color=black>An e-mail would be sent to the administrator in a real-world application.</div>\n";
	$emailtext = "<div color=red><b>An error occured.</b></div>".$msg.
			"<br>MySQL said:<BR><i>".mysql_error()."</i><p>";
	$emailtext .= "<table><tr><td><b>Datetime:</td><td>".$now."</td></tr>".
		"<tr><td><b>Location:</td><td>".$_SERVER['PHP_SELF']."</td></tr></table>";
	print("<HR>".$emailtext); // normally would email this, not print it.
	die($text); // halt execution of script.
}

/*****************************************************************************
  			     HTML FORMAT FUNCTIONS
******************************************************************************/

/*
* printLoginForm():
* Prints the HTML for the login form
*/
function printLoginForm($which_page=NULL)
{
	print("<center><h2>You are not currently logged in.</h2>".
		"<form action=\"");
                if ($which_page==NULL)
			print("main.php");
                else
			print($which_page);
                print("\" method=\"post\">");
	createPanel("Login", "100");
	print("<table border=\"0\" cellpadding=\"0\" cellspacing=\"2\">");
	print("<tr><td>Username:</td>\n<td><input type=\"text\" name=\"username\" size=\"15\"></td>\n");
	print("</tr><tr><td>Password:</td><td><input type=\"password\" name=\"password\" size=\"15\"></td>\n".
		"</tr><tr><td colspan=\"2\" align=\"center\"><input class=\"button\" type=\"submit\" ".
		"value=\"enter\"></td></tr></table>");
	closePanel();
	print("</form>If you don't already have an account <a href=\"".
		"account_create.php\">click here</a> to register.</center>");
}

/*
* createPanel():
* Prints HTML formatting to create a 'standard' panel/table used many
* times in the application.
* $title - [string] the title of the table
* $width - [string] width of table (optional arg)
*/
function createPanel($title, $width='60%')
{
?>
	<table width="<?print($width);?>" border="0" cellpadding="0" cellspacing="0">
	<tr bgcolor="#1115AB"> <td valign="top" align="left">
			<img src="images/panel_top_left.gif" width=11 height=12 align="top" border="0"></td>
		<th><?print($title);?></th>
		<td align="right" valign="top">
			<img src="images/panel_top_right.gif" width=11 height=12 align="top" border="0"></td>
	</tr>
	<tr bgcolor="#CEFFCE">
		<td>&nbsp;</td>
		<td>
<?
	print("\n");
}

/*
* closePanel():
* Prints the HTML formatting to close off the standard panel.
*/
function closePanel()
{
?>
	</td><td>&nbsp;</tr></tr>
	<tr bgcolor="#CEFFCE"> <td valign="bottom" align="left">
		<img src="images/panel_middle_left.gif" width="11" height="12" align="absmiddle" border="0"></td>
		<td>&nbsp;</td>
		<td align="right" valign="bottom">
		<img src="images/panel_middle_right.gif" width="11" height="12" align="absmiddle" border="0"></td>
	</tr>
	</table>
<?
}
/**
	printHeader()
**/
function printHeader($page_title)
{
?>
<html>
<head>
<title>SuperDVD (433-351 Project)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="dvdstore.css" rel="stylesheet">
<script src="window_functions.inc.js"></script>
<script src="validation.inc.js"></script>
</head>

<body bgcolor="white">
<table width="100%">
	<tr>
		<td valign="top" width="200"><img src="images/logo_small.jpg"></td>
		<td align="center" nowrap><h1><? print($page_title); ?></h1></td>
		<td width="200">&nbsp;</td>
	</tr>
</table>
<?
}

function printCustomerHeading($title)
{
	printHeader($title);
?>
	<table width="100%" background="images/back.jpg">
	<tr><td align="right">
		<a href="main.php">Main</a>&nbsp;|&nbsp;
		<a href="adv_search.php">Search</a>&nbsp;|&nbsp;
		<a href="profile_view.php">My profile</a>&nbsp;|&nbsp;
		<a href="basket_view.php">My basket</a>&nbsp;|&nbsp;
		<a href="checkout.php">Checkout</a>&nbsp;|&nbsp;
		<a href="admin.php">Admin</a>
	</td></tr>
	</table>
	<table width="100%"><tr><td width="100" valign="top" align="center">
	<form method="post" action="adv_search.php">
	<? createPanel("Search", "100"); ?>
	<center>
		<input type="text" name="searchFieldValue" size="10"><br>
		<input type="hidden" name="searchFields" value="0">
		<input class="button" type="submit" name="submit_check" value="search">
	</center>
	<? closePanel(); ?>
	</form>
	<?
	createPanel("My Basket", "100");
	print("<center>");
		if ( !isLoggedIn() )
			print("Click <a href=\"main.php\">here</a> to login.");
		else
		{
			print("<span class=\"small\">");
			$basket = viewBasket($_SESSION['username']);
			if ( is_null($basket) )
				print("<br>empty<br>");
			else
				foreach($basket as $i=>$item)
					print("<b>".($i+1).".</b> ".$item['title']."<br>");
			print("</span>");
		}
	closePanel();
	?>
	<br>
	[<a href="main.php?logout=1">Logout</a>]
	</td><td width="5">&nbsp;</td>
<!-- The below is a hack for Netscape below version 4.7 because it is a bastard with tables! -->
<script language="Javascript">
	if ( (navigator.appName == "Netscape") && (parseFloat(navigator.appVersion) < 4.7) )
		document.write("<td width=\""+(self.outerWidth-110)+"\" valign=\"top\">");
	else
		document.write("<td valign=\"top\">");
</script>
<div class="medium" align="center">
<?
	for ( $i=65; $i<91; $i++ )
	{
		print("<a href=\"adv_search.php?submit_check=1&type=title&search_field=".chr($i)."\">".chr($i)."</a>");
		if ( $i < 90 )
			print("&nbsp;|&nbsp;");
	}
	print("</div>");
}


function printAdminHeading($title)
{
	printHeader($title); ?>
	<table width="100%" background="images/back.jpg">
	<tr><td align="right">
		<a href="adv_search.php">Search</a>&nbsp;|&nbsp;<a href="profile_view.php">My profile</a>&nbsp;|&nbsp;
		<a href="basket_view.php">My basket</a>&nbsp;|&nbsp;<a href="checkout.php">Checkout</a>&nbsp;|&nbsp;
		<a href="admin.php?logout=1">Logout</a>
	</td>
	</tr>
	</table><br>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td width="100" valign="top">
	<form method="post" action="adv_search.php" class="form">
	<? createPanel("Search", "100"); ?>
		<center>
		<input type="text" name="searchFieldValue" size="10" style="font-family: arial; font-size:10px;"><br>
		<input type="hidden" name="searchFields" value="0">
		<input type="submit" value="search" name="submit_check" class="button"><br>
		</center>
	<? closePanel(); ?>
	</form>
	<div class="link"><a href="admin.php?section=dvds">DVDs</a><br>
		<a href="admin.php?section=actors">Actors</a><br>
		<a href="admin.php?section=directors">Directors</a><br>
		<a href="admin.php?section=genres">Genres</a><br>
		<a href="admin.php?section=reports">Reports</a><br>
		<a href="admin.php?section=import_xml">Import XML</a>
	</div>
	</td>
	<td width="10">&nbsp;</td>
<!-- The below is a hack for Netscape below version 4.7 because it is a bastard with tables! -->
<script language="Javascript">
	if ( (navigator.appName == "Netscape") && (parseFloat(navigator.appVersion) < 4.7) )
		document.write("<td width=\""+(self.outerWidth-110)+"\" valign=\"top\">");
	else
		document.write("<td valign=\"top\">");
</script>
<? } ?>
