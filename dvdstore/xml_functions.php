<?


/*------------------ Parse XMl data ----------------------*/

function domxml_array ($branch) {
  $object = Array ();
  $objptr =& $object;
  $branch = $branch->first_child ();
  while ($branch) {
    if (!($branch->is_blank_node())) {
      switch ($branch->node_type()) {
        case XML_TEXT_NODE: {
          $objptr['cdata'] = $branch->node_value ();
          break;
        }
        case XML_ELEMENT_NODE: {
          $objptr =& $object[$branch->node_name ()][];
          if ($branch->has_attributes ()) {
            $attributes = $branch->attributes ();
            if (!is_array ($attributes)) { break; }
            foreach ($attributes as $index => $domobj) {
              $objptr[$index] = $objptr[$domobj->name] = $domobj->value;
            }
          }
          break;
        }
      }
      if ($branch->has_child_nodes ()) {
        $objptr = array_merge ($objptr, domxml_array ($branch));
      }
    }
    $branch = $branch->next_sibling ();
  }
  return $object;
}

/*-------------Convert mysql table to xml --------------*/

function db2xml($host,$user,$password,$database,$table) {

  $xml = "<?xml version='1.0'?>
          <xml>\r\n<table>\r\n";

  mysql_connect($host, $user, $password)
    or die("Cannot connect");
  $req = mysql_db_query($database, "select * from $table")
    or die("Query unsuccessful");

  while($row = mysql_fetch_array($req)) {
    $xml .= "<item>\r\n";                             
   for($j=0;$line=each($row);$j++) {
      if($j%2) {
        $xml .= "<$line[0]>$line[1]</$line[0]>\r\n";
      }
    }
    $xml .= "</item>\r\n";
  }
  $xml .= "</table>\r\n</xml>";
  mysql_free_result($req);
  return $xml;
}
?>                                     


