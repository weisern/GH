<?
$file_name = "sample_import.xml";
print("<h2>Commencing importation:</h2><textarea rows=\"10\" cols=\"80\">");
readfile("/mount/autofs/home_studproj/351/2003s1/i1n2c3a/dave/".$file_name, "r");
print("</textarea><hr>");

# open XML file
$dom = domxml_open_file($file_name)
  or die ("Error while parsing the document");
#parse the xml data
$root = domxml_array($dom);

$dvds = $root['dvd'][0]['item'];

foreach($dvds as $d_id=>$dvd)
{
	print("<p>Processing dvd #".($d_id+1)." . . .<br>");
	$d_fname = $dvd['director'][0]['first_name'];
	$d_lname = $dvd['director'][0]['last_name'];

	/*** Ensure Director is in DB ***/
	addDirector($d_fname, $d_lname);

	// Retreive id for director
	$query = "SELECT director_id FROM directors WHERE first_name = '$d_fname'".
		" AND last_name='$d_lname'";
	$result = mysqlQuery($query);
	$dvd['director_id'] = $result[0]['director_id'];
	unset($dvd['director']);

	/*** Ensure actors are in DB ***/
	foreach ( $dvd['actor'] as $actor )
	{
		$a_fname = $actor['first_name'];
		$a_lname = $actor['last_name'];
		addActor($a_fname, $a_lname);
		$actor_ar[] = "(first_name='$a_fname' AND last_name='$a_lname')";
	}
	if ( $actor_ar )
	{ // Retreive id for each actor

		$actor_list = join(" OR ", $actor_ar);
		$query = "SELECT actor_id FROM actors WHERE $actor_list";
		$result = mysqlQuery($query, "actor_id");
		$dvd['actors'] = array_keys($result);
	}

	/*** Ensure genres are in DB ***/
	foreach ( $dvd['genre'] as $genre )
	{
		$desc = $genre['description'];
		addGenre($desc);
		$genre_ar[] = "'".$desc."'";
	}
	if ( $genre_ar )
	{ // Retreive id for each genre

		$genre_list = join(",", $genre_ar);
		$query = "SELECT genre_id FROM genres WHERE description IN ($genre_list)";
		$result = mysqlQuery($query, "genre_id");
		$dvd['genres'] = array_keys($result);
	}

	unset($dvd['actor']);
	unset($dvd['genre']);
	$dvd['barcode'] = $dvd['barcode'][0]['cdata'];
	$dvd['title'] = $dvd['title'][0]['cdata'];
	$dvd['synopsis'] = $dvd['synopsis'][0]['cdata'];
	$dvd['stock_avail'] = $dvd['stock_avail'][0]['cdata'];
	$dvd['cost'] = $dvd['cost'][0]['cdata'];
	$dvd['sell_price'] = $dvd['sell_price'][0]['cdata'];

	deleteDVD($dvd['barcode']); //Delete the DVD if already exist
	addDVD($dvd);
	print("DVD #".($d_id+1)." added successfully</p>");
}
?>
<h2>XML imported successfully</h2>
<a href="sample_import.xml">View the XML file</a>
