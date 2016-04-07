<?
	include("dvdstore.inc.php");
	printCustomerHeading("My Profile");

	if ( $_POST['username'] )
		login($_POST['username'], $_POST['password']);

	if ( isLoggedIn() )
	{
		printf("<h2 style=\"color: orange;\">Changing details of ".$_SESSION['first_name'].",</h2>");
		$customer_details = getCustomer($_SESSION['username']);
		$card_details = getCreditCard($_SESSION['username']);

		if ( $_POST['submit'] )
		{
			switch( $_POST['form'] )
			{
				case 'changePD':
					$result = changePersonalDetails($_SESSION['username'],
						$_POST['first_name'], $_POST['last_name'],
						$_POST['date_of_birth']);
					if ( $result )
					{
						$customer_details = getCustomer($_SESSION['username']);
						$card_details = getCreditCard($_SESSION['username']);
					}
					break;
				case 'changePW':
					$result = changePassword($_SESSION['username'],
						$_POST['oldPassword'], $_POST['newPassword1']);
					if ( $result )
					{
						$customer_details = getCustomer($_SESSION['username']);
						$card_details = getCreditCard($_SESSION['username']);
						print("password changed<br>");
					}
					break;
				case 'changeAD':
					$result = changeAddressDetails($_SESSION['username'],
						$_POST['email'], $_POST['street'],
						$_POST['suburb'], $_POST['postcode'],
						$_POST['state_id']);
					if ( $result )
					{
						$customer_details = getCustomer($_SESSION['username']);
						$card_details = getCreditCard($_SESSION['username']);
					}
					break;
				case 'changeCCD':
					$result = changeCreditCardDetails($_SESSION['username'],
						$_POST['card_name'], $_POST['card_number'],
						$card_details[0]['card_number'],
						$_POST['expiry_date']);
					if ( $result )
					{
						$customer_details = getCustomer($_SESSION['username']);
						$card_details = getCreditCard($_SESSION['username']);
					}
					break;
				default:
					break;
			}//end switch-clause
		}//end if-clause
	}else
	{
		printLoginForm("profile_view.php");
		die();
	}

function changePersonalDetails($username, $fn, $ln, $dob)
{
	$dob = reverseDate($dob);
	$query = "UPDATE customers set first_name='$fn', last_name='$ln', date_of_birth='$dob' ".
		"WHERE username='$username' ";
	$result = mysqlInsert($query) or
			mysql_ErrorMsg("Unable to perform update:<b>$query</b>");
	return $result;
}

function changePassword($username, $oldp, $newp)
{

	$query = "SELECT password FROM customers WHERE username='$username'";
	$result = mysqlQuery($query);
	if ( $result[0]['password'] != md5($oldp) )
	{
		printf("<div class=\"error\">Old password is incorrect.</div>");
	}else
	{
		$query = "UPDATE customers set password='".md5($newp)."' WHERE username='$username' ";
		$result = mysqlInsert($query) or
				mysql_ErrorMsg("Unable to perform update:<b>$query</b>");
		return $result;
	}
}

function changeAddressDetails($username, $email, $street, $suburb, $postcode, $state_id)
{
	$query = "UPDATE customers set email='$email', street='$street', suburb='$suburb', ".
		"postcode=$postcode, state_id=$state_id WHERE username='$username' ";
	$result = mysqlInsert($query) or
			mysql_ErrorMsg("Unable to perform update:<b>$query</b>");
	return $result;
}

function changeCreditCardDetails($username, $card_name, $card_num, $old_card_num, $exp_date)
{
	$exp_date = reverseDate($exp_date);
	$query1 = "UPDATE credit_cards set card_name='$card_name', card_number=$card_num, ".
	"expiry_date='$exp_date' WHERE card_number=$old_card_num ";
	$query2 = "UPDATE pays_with set card_number=$card_num WHERE username='$username' ";
	$result1 = mysqlInsert($query1) or
			mysql_ErrorMsg("Unable to perform update:<b>$query</b>");
	if ( $result1 )
	{
		$result2 = mysqlInsert($query2) or
			mysql_ErrorMsg("Unable to perform update:<b>$query</b>");
		return $result2;
	}

}

?>
<form action="main.php" method="post">
	<input type="submit" name="cancel" value="Cancel" class="button">
</form><br>

Please note that all fields are required.<br>
<table border="0" cellpadding="0" cellspacing="0">
<tr>
	<td>
		<h2>Personal Details</h2>
		<form action="profile_view.php" method="post" name="changePersonalDetails"
			onsubmit="return validate_personal_details(this);">
		<input type="hidden" name="form" value="changePD">
		<table border="0" cellpadding="1" cellspacing="3">
			<tr><td class="bold">
				First name:
			</td><td>
				<input type="text" name="first_name" size="10" maxlength="40"
				value="<? print($customer_details[0]['first_name']); ?>">
			</tr>
			<tr><td class="bold">
				Last name:
			</td><td>
				<input type="text" name="last_name" size="10" maxlength="40"
				value="<? print($customer_details[0]['last_name']); ?>">
			</tr>
			<tr><td class="bold">
				Your date of birth<br>(dd-mm-yyyy):
			</td><td>
				<input type="text" name="date_of_birth" size="10" maxlength="10"
				value="<? print($customer_details[0]['date_of_birth']); ?>">
			</td></tr>
		<tr><td>
		<input type="submit" name="submit" value="Modify" class="button">
		</td></tr>
		</table>
		</form>
	</td>
	<td width="50">&nbsp;</td>
	<td>
		<h2>Password</h2>
		<form action="profile_view.php" method="post" name="changePassword"
			onsubmit="return validate_new_password(this);">
		<input type="hidden" name="form" value="changePW">
		<table border="0" cellpadding="1" cellspacing="3">
		<tr><td class="bold">
			Old password:
		</td><td>
			<input type="password" name="oldPassword" size="10" maxlength="20">
		</tr>
		<tr><td class="bold">
			New password:
		</td><td>
			<input type="password" name="newPassword1" size="10" maxlength="20">
		</tr>
		<tr><td class="bold">
			Confirm new password:
		</td><td>
			<input type="password" name="newPassword2" size="10" maxlength="20">
		</td></tr>
		<tr><td>
		<input type="submit" name="submit" value="Modify" class="button">
		</td></tr>
		</table>
		</form>
	</td>
</tr>

<tr>
	<td>
		<h2>Address details</h2>
		<form action="profile_view.php" method="post" name="changeAddressDetails"
			onsubmit="return validate_address_details(this);">
		<input type="hidden" name="form" value="changeAD">
		<table border="0" cellpadding="1" cellspacing="4">
		<tr><td class="bold">
			Email address:
		</td><td colspan="3">
			<input type="text" name="email" size="25" maxlength="80"
			value="<? print($customer_details[0]['email']); ?>">
		</tr>
		<tr><td class="bold">
			Street address:
		</td><td colspan="3">
			<input type="text" name="street" size="25" maxlength="30"
			value="<? print($customer_details[0]['street']); ?>">
		</tr>
		<tr><td class="bold">
			Suburb:
		</td><td>
			<input type="text" name="suburb" size="10" maxlength="30"
			value="<? print($customer_details[0]['suburb']); ?>">
		</td><td class="bold">
			Post Code:
		</td><td>
			<input type="text" name="postcode" size="4" maxlength="4"
			value="<? print($customer_details[0]['postcode']); ?>">
		</td></tr>
		<tr><td class="bold">
			State:
		</td><td colspan="3">
			<? printStatesList($customer_details[0]['state_id']); ?>
		</tr>
		<tr><td>
		<input type="submit" name="submit" value="Modify" class="button">
		</td></tr>
		</table>
		</form>
	</td>
	<td width="50">&nbsp;</td>
	<td>
		<h2>Credit Card Details</h2>
		<form action="profile_view.php" method="post" name="changeCreditCardDetails"
			onsubmit="return validate_credit_card(this);">
		<input type="hidden" name="form" value="changeCCD">
		<table border="0" cellpadding="1" cellspacing="4">
		<tr><td class="bold">
			Name on Card:
		</td><td>
			<input type="text" name="card_name" size="25" maxlength="50"
			value="<? print($card_details[0]['card_name']); ?>">
		</tr>
		<tr><td class="bold">
			Card number:
		</td><td>
			<input type="text" name="card_number" size="16" maxlength="16"
			value="<? print($card_details[0]['card_number']); ?>">
		</tr>
		<tr><td class="bold">
			Expiry Date (mm-yy):
		</td><td>
			<input type="text" name="expiry_date" size="5" maxlength="5"
			value="<? print($card_details[0]['expiry_date']); ?>">
		</td></tr>
		<tr><td>
		<input type="submit" name="submit" value="Modify" class="button">
		</td></tr>
		</table>
		</form>
	</td>
</tr>

</table>

<script language="javascript">
<!--
function validate_personal_details(obj)
{
	rvar = validate_notempty(obj.first_name, "First Name");
	if ( rvar )
		rvar = validate_notempty(obj.last_name, "Last Name");
	if ( rvar )
		rvar = validate_date(obj.date_of_birth, "Date of birth");
	return rvar;
}

function validate_new_password(obj)
{
	rvar = validate_notempty(obj.oldPassword, "password");
	if ( rvar )
		rvar = validate_notempty(obj.newPassword1, "password");
	if ( rvar )
		rvar = validate_notempty(obj.newPassword2, "password");
	if ( rvar && (obj.newPassword1.value != obj.newPassword2.value) )
	{
		alert("The passwords you have entered do not match.\nPlease try again.");
		rvar = false;
	}
	return rvar;
}

function validate_address_details(obj)
{
	rvar = validate_email(obj.email, "Email");
	if ( rvar )
		rvar = validate_notempty(obj.street, "Street Address");
	if ( rvar )
		rvar = validate_notempty(obj.suburb, "Suburb");
	if ( rvar )
		rvar = validate_number(obj.postcode, "Postcode");
	if ( rvar && (obj.postcode.value.length != 4) )
	{
		alert("Please enter a valid postcode");
		rvar = false;
	}
	if ( rvar )
		rvar = validate_select(obj.state_id, "State");
	return rvar;
}

function validate_credit_card(obj)
{
	rvar = validate_notempty(obj.card_name, "Name on Card");
	if ( rvar )
		rvar = validate_number(obj.card_number, "Card Number");
	if ( rvar )
		rvar = validate_shortdate(obj.expiry_date, "Expiry Date");
	return rvar;
}
-->
</script>

<!-- footer -->
</td></tr></table>

