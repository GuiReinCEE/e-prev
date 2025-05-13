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
  <link rel="shortcut icon" href="<?php echo $this->config->item('base_url'); ?>favicon.ico" type="image/x-icon" >
  
  <link href="<?= $this->config->item('base_url'); ?>skins/skin001/css/abas.css" rel="stylesheet" type="text/css">
  <link href="<?= $this->config->item('base_url'); ?>skins/skin001/css/default.css" rel="stylesheet" type="text/css">

  <script type='text/javascript' src='<?= $this->config->item('base_url'); ?>js/default.js'></script>
  <script type='text/javascript' src='<?= $this->config->item('base_url'); ?>skins/skin001/sort_table/sortabletable.js'></script>
  <link type='text/css' rel='StyleSheet' href='<?= $this->config->item('base_url'); ?>skins/skin001/sort_table/sortabletable.css'>

	<script src="<?= $this->config->item('base_url'); ?>js/jquery-1.2.6.min.js" type="text/javascript"></script>
	<script src="<?= $this->config->item('base_url'); ?>js/jquery.maskedinput-1.2.js" type="text/javascript"></script>

	<!-- calendar stylesheet -->
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url() ?>js/jscalendar/calendar-win2k-cold-1.css" title="win2k-cold-1" />

	<!-- main calendar program -->
	<script type="text/javascript" src="<?php echo base_url() ?>js/jscalendar/calendar.js"></script>

	<!-- language for the calendar -->
	<script type="text/javascript" src="<?php echo base_url() ?>js/jscalendar/lang/calendar-br.js"></script>

	<!-- the following script defines the Calendar.setup helper function, which makes
	 adding a calendar a matter of 1 or 2 lines of code. -->
	<script type="text/javascript" src="<?php echo base_url() ?>js/jscalendar/calendar-setup.js"></script>

  <!-- MENU -->
  <link rel="stylesheet" href="<?= base_url() ?>skins/skin001/css/verde.css" type="text/css" media="all">
  <script type='text/javascript' src='<?= $this->config->item('base_url'); ?>js/menu.js'></script>
  <!-- // MENU -->

  <style>

	.links2
	{
		color:#000000;
		font-family:Verdana,Arial,Helvetica,sans-serif;
		font-size:10px;
		font-style:normal;
		font-weight:bold;
		line-height:20px;
		text-decoration:none;
	}

  </style>

 </HEAD>

<BODY leftmargin="0" topmargin="0" bgcolor="">

<div id="general"> <!-- GENERAL -->

	<div id="header"></div>

	<div id="menu" style="padding-bottom:30px; clear:both;"></div>

	<input id="root" name="root" type="hidden" value="<?= $this->config->item('base_url'); ?>" />
	<input id="base_url" name="base_url" type="hidden" value="<?= $this->config->item('base_url'); ?>index.php/" />
	<input type="hidden" name="current_page" id="current_page" value="0" />

<div id="conteudo" style="clear:both;">  <!-- DIV CONTEUDO -->
<br>