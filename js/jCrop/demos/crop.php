<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script src="jquery-1.3.2.min.js" type="text/javascript"></script>
		<script src="../js/jquery.Jcrop.js" type="text/javascript"></script>

		<link rel="stylesheet" href="../css/jquery.Jcrop.css" type="text/css" />
		<script language="Javascript">
			$(function(){
				$('#cropbox').Jcrop({
					allowSelect: false,
					onSelect:    updateCoords,
					bgColor:     'black',
					bgOpacity:   .4,
					setSelect:   [ 10, 10, 150, 150 ],
					aspectRatio: 100 / 100
				});				
			});

			function updateCoords(c)
			{
				$('#x').val(c.x);
				$('#y').val(c.y);
				$('#w').val(c.w);
				$('#h').val(c.h);
			};
		</script>

	</head>
	<body>
		<!-- This is the image we're attaching Jcrop to -->
		<img src="demo_files/1.jpg" id="cropbox" />

		<!-- This is the form that our event handler fills -->
		<form action="crop-img.php" method="post">
			<input type="hidden" id="nome_arq" name="nome_arq" value="1.jpg"/>
			<input type="hidden" id="x" name="x" />
			<input type="hidden" id="y" name="y" />
			<input type="hidden" id="w" name="w" />
			<input type="hidden" id="h" name="h" />
			<input type="submit" value="Crop Image" />
		</form>
	</body>
</html>
