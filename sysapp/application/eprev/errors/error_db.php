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

#content_error_msg * {
	font-family: courier, tahoma, verdana;
	font-size: 10pt;
}

h1 {
font-family: calibri, arial, verdana;
font-size: 150%;
font-weight:		bold;
font-size:			180%;
color:				#990000;
margin: 			0 0 4px 0;
}
</style>
</head>
<body>
	<div id="content_error">
		<h1><?php echo $heading; ?></h1>
		<div id="content_error_msg"><?php echo $message; ?></div>
	</div>
</body>
</html>