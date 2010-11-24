<?php include 'imgpath.php'; ?>
<?php

function directoryToArray($directory, $recursive) {
	$array_items = array();
	if ($handle = opendir($directory)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if (is_dir($directory. "/" . $file)) {
					if($recursive) {
						$array_items = array_merge($array_items, directoryToArray($directory. "/" . $file, $recursive));
					}
					//$file = $directory . "/" . $file;
					//$array_items[] = preg_replace("/\/\//si", "/", $file);
				} else {
					$file = $directory . "/" . $file;
					$array_items[] = preg_replace("/\/\//si", "/", $file);
				}
			}
		}
		closedir($handle);
	}
	return $array_items;
}


$list = directoryToArray(IMGSDIRPATH, true);
//print_r($list);

$results = array();


foreach($list as $k=>$item) {
  if (stristr($item, $_REQUEST['search'])) {
    $sub = substr($item, -7, 3);
    if ($sub == 'web') {
      $results[] = $item;
    } 

    
  }
}


echo json_encode($results);

?>