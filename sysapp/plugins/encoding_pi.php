<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('encoding.php'); 

use \ForceUTF8\Encoding;  

function fixUTF8($filename)
{
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
	$filename = str_replace(".".$ext, '', $filename);
	$filename = str_replace(array('[\', \']'), '', $filename);
    $filename = preg_replace('/\[.*\]/U', '', $filename);
    $filename = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $filename);
    #$filename = htmlentities($filename, ENT_COMPAT, 'utf-8');
    $filename = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $filename );
    $filename = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $filename);	
	$filename.= ".".$ext;	
	
	
	$filename = str_replace(chr(150),"-",$filename);
	
	return  Encoding::fixUTF8($filename);
}