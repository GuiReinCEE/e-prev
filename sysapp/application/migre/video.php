<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	$qr_sql = " 
				SELECT arquivo, 
					   arquivo_original,
					   diretorio					   
				  FROM acs.videos 
				 WHERE cd_video = ".$_REQUEST['c'];
	$ob_resul = pg_query($db, $qr_sql);
	$ar_reg   = pg_fetch_array($ob_resul);
	$video    = "http://srvimagem:1111/".$ar_reg['diretorio'].$ar_reg['arquivo'];
	
	$link_download = "";
	$altura = "100%";
	if(trim($ar_reg['arquivo_original'])	!= "")
	{
		$link_download = '<a href="http://srvimagem:1111/down.php?arq='.$ar_reg['diretorio'].$ar_reg['arquivo_original'].'" style="font-family: Calibri, Arial; font-size: 12pt;"><img src="mediaplayer/download_video.png" border="0"> Baixar Vídeo</a>';
		$altura = "90%";
	}
	
?>
<html>
	<title>Fundação CEEE</title>
	<head>
		<script type='text/javascript' src='mediaplayer/swfobject.js'></script>
	</head>
<body>
<p id='preview'>The player will show in this paragraph</p>


<script type='text/javascript'>
var s1 = new SWFObject('mediaplayer/player.swf','player','100%','<?php echo $altura;?>','8');
	s1.addParam('allowfullscreen','true');
	s1.addParam('allowscriptaccess','always');
	s1.addParam('flashvars','file=<?php echo $video;?>&autostart=true&logo=mediaplayer/logo.png&backcolor=006600&frontcolor=FFFFFF&lightcolor=006600&screencolor=000000');
	s1.write('preview');
</script>

<?php
	if(trim($link_download)	!= "")
	{
		echo $link_download;
	}

?>



</body>
</html>