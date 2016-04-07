<?
	if ( !$ADMIN_FLAG )
		die("No Access");

	$barcode = $_GET["barcode"];
	if ( $_POST['submit'] )
	{
		if ( $_POST['add_units'] > 0 )
			$stock_avail = $_POST['add_units'] + $_POST['stock_avail'];
		else
			$stock_avail = $_POST['stock_avail'];
		$result = modifyDVD($_POST['barcode'], array("stock_avail"=>$stock_avail,
			"cost"=>$_POST['cost'], "sell_price"=>$_POST['sell_price']));
		if ( $result == TRUE )
		{
			printf("<div class=\"success\">DVD receipted-in successfully</div>");
			$_GET['page'] = $_POST['page'];
			include("dvds.php");
			die();
		}
		$barcode = $_POST['barcode'];
	}
	if ( !$barcode )
	{
		print("<div class=\"error\">No barcode specified.</div>");
		include("dvds.php");
		die();		
	}		
	$details = getDVD($barcode);
?>
<h1>Receipt In Units</h1>
<a href="admin.php?section=dvds&page=<? print($_GET['page']); ?>" class="link">Back</a>
<form action="admin.php?section=dvd_receipt" method="post" onsubmit="return validate(this);">
<input type="hidden" name="page" value="<? print($_GET['page']); ?>">
<input type="hidden" name="stock_avail" value="<? print($details['stock_avail']); ?>">
<input type="hidden" name="barcode" value="<? print($barcode); ?>">
	<table cellspacing="0" cellpadding="4" bordercolor="black" border="2" bgcolor="seagreen">
	<tr><td>
		<table cellpadding="5" cellspacing="0" border="0" class="listing">
		
		<tr>
			<td bgcolor="honeydew" colspan="2">
				<? print("<b>".$details['title']."</b> (".$details['barcode'].")"); ?>
			</td>
		</tr>
		<tr>
			<th rowspan="2" style="vertical-align: middle;">Confirm</th>
			<td>
				 Unit Cost $<input type="text" name="cost" size="5" maxlength="10"
					value="<? print($details['cost']); ?>">
			</td>
		</tr>
		<tr>
			<td  bgcolor="#ECECEC">
				 Unit Price $<input type="text" name="sell_price" size="5" maxlength="10"
					value="<? print($details['sell_price']); ?>">
			</td>
		</tr>
		<tr height="2" bgcolor="white"><td colspan="2"></td></tr>
		<tr>
			<th rowspan="2" style="vertical-align: middle;">Stock</th>
			<td>
				 Currently <b><? print($details['stock_avail']); ?></b> units in stock
			</td>
		</tr>
		<tr>
			<td  bgcolor="#ECECEC">
				 Receipt <input type="text" name="add_units" size="4" maxlength="10"> new units
			</td>
		</tr>
		</table>
	</td></tr>
	<tr>
		<td align="center">
			<input type="submit" name="submit" value="Receipt In" class="button">
		</td>
	</tr>
	</table>
</form>
<script language="javascript">
<!--
function validate(obj)
{
	rvar = validate_number(obj.cost, "Unit cost");
	if ( rvar )
		rvar = validate_number(obj.sell_price, "Unit sell price");
	if ( rvar )
		rvar = validate_number(obj.add_units, "New units");
	return rvar;
}
-->
</script>