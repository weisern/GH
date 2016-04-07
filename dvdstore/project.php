<?
	include("dvdstore.inc.php");
	print("<center>");
	printHeader("Introduction");
	createPanel("Features");
?>
			<ol>
			<li>Anyone can browse the database. 
			<li>Customers must have a shopping basket. Customers will have sessions (cookie only).
			<li>Customers must have an account with login ID and password to place items in their shopping basket.
			<li>A system which procuses outstanding reports when inventory is low. 
			<li>Staff are able to receipt in new goods to inventory.
			</ol>
<? 
	closePanel(); 
?>
	<p>
	<? createPanel("Limitations"); ?>
		
		<li>It is assumed (as allowed by Steven Bird) that all users are using <u>javascript-enabled</u> browsers (IE4.0+ or N4.0+).
		<li>All orders are assumed to ship instantly, to reduce the complexity of having to track orders.
		<li>The security model is simplified, this site does not use SSL (or other security measures). 
			This means that passwords, credit card numbers, etc are sent between users and the site in plain text.</li>
		<li>No refunds and no returns. 
		<li>Stock arrives from thin air. No need to place inventory orders. 
		<li>No preorders. 
		<li>No promotions or discounts.
		<li>Payment by credit card only. All cards accepted and approved instantly. 
		<li>Goods can not be shipped to others as gift. 
		<li>Service operates only in Australia, so DVDs are in Australian currency.
		<li>The administration system is protected by a simple password (again without SSL)</li> 
	<? closePanel(); ?>
	<p>
	<? createPanel("Using the DVD store"); ?>
		You can use the administration component of the system to:
		<ol>
			<li>Receipt-in Stock</li>
			<li>Run Reports</li>
			<li>Run Queries</li>
		</ol>
		The password to access administration is <span class="note">quasar</span>.
	<? closePanel(); ?>	
	<br><hr>
	<a href="main.php" class="large">Enter DVD Store</a>
</center>
</body>
</html>