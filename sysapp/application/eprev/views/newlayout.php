<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Ext 2.0 Desktop Sample App</title>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>extjs/resources/css/ext-all.css" />
    <!-- GC -->
 	<!-- LIBS -->
 	<script type="text/javascript" src="<?php echo base_url(); ?>extjs/adapter/ext/ext-base.js"></script>
 	<!-- ENDLIBS -->

    <script type="text/javascript" src="<?php echo base_url(); ?>extjs/ext-all.js"></script>

    <!-- DESKTOP -->
    <script type="text/javascript" src="<?php echo base_url(); ?>extjs/lib/desktop/js/StartMenu.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>extjs/lib/desktop/js/TaskBar.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>extjs/lib/desktop/js/Desktop.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>extjs/lib/desktop/js/App.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>extjs/lib/desktop/js/Module.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>extjs/lib/desktop/sample.js"></script>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>extjs/lib/desktop/css/desktop.css" />
</head>
<body scroll="no">

<div id="x-desktop">
    <a href="http://extjs.com" target="_blank" style="margin:5px; float:right;"><img src="images/powered.gif" /></a>

    <dl id="x-shortcuts">
        <dt id="grid-win-shortcut">
            <a href="#"><img src="<?php echo base_url(); ?>extjs/lib/desktop/images/s.gif" />
            <div>Grid Window</div></a>
        </dt>
        <dt id="acc-win-shortcut">
            <a href="#"><img src="<?php echo base_url(); ?>extjs/lib/desktop/images/s.gif" />
            <div>Accordion Window</div></a>
        </dt>
    </dl>
</div>

<div id="ux-taskbar">
	<div id="ux-taskbar-start"></div>
	<div id="ux-taskbuttons-panel"></div>
	<div class="x-clear"></div>
</div>

</body>
</html>