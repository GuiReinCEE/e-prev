<?php
header('Content-Type: text/html; charset=ISO-8859-1');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE> ePrev (with CI) </TITLE>
  <META NAME="Generator" CONTENT="EditPlus">
  <META NAME="Author" CONTENT="">
  <META NAME="Keywords" CONTENT="">
  <META NAME="Description" CONTENT="">
  <link rel="shortcut icon" href="<?= $this->config->item('base_url'); ?>favicon.ico" type="image/x-icon" >
  
  <link href="<?= $this->config->item('base_url'); ?>skins/skin001/css/default.css" rel="stylesheet" type="text/css">

  <script type='text/javascript' src='<?= $this->config->item('base_url'); ?>js/prototype_1_6_0.js'></script>
  <script type='text/javascript' src='<?= $this->config->item('base_url'); ?>js/effects.js'></script>

  <script>
  	function realizar_login()
  	{
  		var f = document.forms[0];
  		f.action = document.getElementById('base_url').value + 'login/entrar';
  		f.submit();
  	}
  </script>

 </HEAD>

 <BODY leftmargin="0" topmargin="0" bgcolor="#6FAE6F">
<? echo form_open(''); ?>

<input id="root" name="root" type="hidden" value="<?= $this->config->item('base_url'); ?>" />
<input id="base_url" name="base_url" type="hidden" value="<?= $this->config->item('base_url'); ?>index.php/" />
	
<div id="conteudo" style="clear:both;">  <!-- DIV CONTEUDO -->
<br>