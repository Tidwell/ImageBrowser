<?php include 'imgpath.php'; ?>
<?php


include 'dirlist.php';
//error_reporting(E_ALL);
$folder = $_REQUEST['folder'];
$returnArr = array();

$imglist = directory_list(IMGSDIRPATH.$folder, true, false, '', false);
foreach($imglist as $img) {
  $sub = substr($img, -7, 3);
  if ($sub == 'web') {
    $returnArr[] = $img;
  } 
}

echo json_encode($returnArr);

?>