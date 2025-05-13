<?php
$w=$_REQUEST['w'];
$h=isset($_REQUEST['h'])?$_REQUEST['h']:$w;    // h est facultatif, =w par défaut
$x=isset($_REQUEST['x'])?$_REQUEST['x']:0;    // x est facultatif, 0 par défaut
$y=isset($_REQUEST['y'])?$_REQUEST['y']:0;    // y est facultatif, 0 par défaut
$filename=$_REQUEST['src'];
header('Content-type: image/jpg');
header('Content-Disposition: attachment; filename='.$src);
$image = imagecreatefromjpeg($filename);
$crop = imagecreatetruecolor($w,$h);
imagecopy ( $crop, $image, 0, 0, $x, $y, $w, $h );
imagejpeg($crop);
?>