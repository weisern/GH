<? 
	$path = dirname(dirname(__FILE__));
	require_once("$path/dvdstore.inc.php");
	$actors = getActors;
?>
<html>
<head>
<title>Select Actors Dialog</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../dvdstore.css" rel="stylesheet">
</head>
<form name="selection">
<h1>Select Actors</h1>
<?
	printActorList();
?>
</tr><tr><td align="center"><input type="button" value="Update" class="button" onclick="update_actors();"></td></tr></table>

</form>
<script language="javascript">
<!--
function update_actors(obj)
{
	obj = window.opener; 
	var str='';
	var names='';
	for (i=0; i < document.selection.actors.length; i++ )
	{
		if ( document.selection.actors[i].checked )
		{
			str = str + document.selection.actors[i].value+', ';
			names = names + document.selection.actors[i].actor+', ';
		}
	}
	if ( names == '' )
		obj.actorstext.innerHTML='No actors currently selected';
	else
		obj.actorstext.innerHTML=names.substr(0, names.length-2);
	obj.document.dvd.actors.value=str.substr(0, str.length-2);
}
-->
</script>