<?
	include_once("dvdstore.inc.php");
	if ( $_POST['submit'] )
	{
		if ( addCustomer($_POST) )
		{
			$_POST['password'] = $_POST['password1'];
			include("main.php");
			die();			
		}
			
	}
	printCustomerHeading("Registration");
?>
<form action="account_create.php" method="post" onsubmit="return validate(this);">
<table border="0" cellpadding="0" cellspacing="0">
<tr><td>
	<h2>Personal Details</h2>
	<table border="0" cellpadding="1" cellspacing="3">
		<tr><td class="bold">
			First name:
		</td><td>
			<input type="text" name="first_name" size="10" maxlength="40">
		</tr>
		<tr><td class="bold">
			Last name:
		</td><td>
			<input type="text" name="last_name" size="10" maxlength="40">
		</tr>
		<tr><td class="bold">
			Your date of birth<br>(dd/mm/yyyy):
		</td><td>
			<input type="text" name="date_of_birth" size="10" maxlength="10">
		</td></tr>
	</table>
</td><td width="50">&nbsp;
</td><td valign="top">
	<h2>Login details</h2>
	<table border="0" cellpadding="1" cellspacing="3">
		<tr><td class="bold">
			Create a username:
		</td><td>
			<input type="text" name="username" size="10" maxlength="20">
		</tr>
		<tr><td class="bold">
			Create a password:
		</td><td>
			<input type="password" name="password1" size="10" maxlength="20">
		</tr>
		<tr><td class="bold">
			Confirm your password:
		</td><td>
			<input type="password" name="password2" size="10" maxlength="20">
		</td></tr>
	</table>
</td></tr>
</table>
<h2>Address details</h2>
<table border="0" cellpadding="1" cellspacing="4">
	<tr><td class="bold">
		Email address:
	</td><td colspan="3">
		<input type="text" name="email" size="25" maxlength="80">
	</tr>
	<tr><td class="bold">
		Street address:
	</td><td colspan="3">
		<input type="text" name="street" size="25" maxlength="30">
	</tr>
	<tr><td class="bold">
		Suburb:
	</td><td>
		<input type="text" name="suburb" size="10" maxlength="30">
	</td><td class="bold">
		Post Code:
	</td><td>
		<input type="text" name="postcode" size="4" maxlength="4">
	</td></tr>
	<tr><td class="bold">
		State:
	</td><td colspan="3">
		<? printStatesList(); ?>
	</tr>
</table>
<h2>Credit Card Details</h2>
<table border="0" cellpadding="1" cellspacing="4">
	<tr><td class="bold">
		Name on Card:
	</td><td>
		<input type="text" name="card_name" size="25" maxlength="50">
	</tr>
	<tr><td class="bold">
		Card number:
	</td><td>
		<input type="text" name="card_number" size="16" maxlength="16">
	</tr>
	<tr><td class="bold">
		Expiry Date (mm/yy):
	</td><td>
		<input type="text" name="expiry_date" size="5" maxlength="5">
	</td></tr>
</table>
Please note that all fields are required.<br>
<input type="submit" name="submit" value="Register" class="button">
</form>

<script language="javascript">
<!--
function validate(obj)
{
	rvar = validate_notempty(obj.first_name, "First Name");
	if ( rvar )
		rvar = validate_notempty(obj.last_name, "Last Name");
	if ( rvar )
		rvar = validate_date(obj.date_of_birth, "Date of birth");
	if ( rvar )
		rvar = validate_notempty(obj.username, "Username");
	if ( rvar )
		rvar = validate_notempty(obj.password1, "Password");
	if ( rvar )
		rvar = validate_notempty(obj.password2, "Password Confirmation");
	if ( rvar && (obj.password1.value != obj.password2.value) )
	{
		alert("The passwords you have entered do not match.\nPlease try again.");
		rvar = false;
	}
	if ( rvar )
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
	if ( rvar )
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
</td></tr>
</table>