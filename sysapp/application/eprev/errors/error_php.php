<html>
<head>
<title>Error</title>
<style type="text/css">

#content_error  {
text-align: left;
border:				#999 1px solid;
background-color:	#fff;
padding:			20px 20px 12px 20px;
}

#content_error_msg p {
	font-family: calibri, arial, verdana;
	font-size: 100%;
}

h1 {
font-family: calibri, arial, verdana;
font-size: 150%;
font-weight:		bold;
color:				#990000;
margin: 			0 0 4px 0;
}
</style>
</head>

	<div id="content_error">
		<h1>A PHP Error was encountered</h1>
		<div id="content_error_msg">
		<p>Severity: <?php echo $severity; ?></p>
		<p style="color: red; font-weigth:bold;">Message:  <?php echo $message; ?></p>
		<p>Filename: <?php echo $filepath; ?></p>
		<p>Line Number: <?php echo $line; ?></p>		
		</div>
	</div>
	<BR>
</body>
</html>