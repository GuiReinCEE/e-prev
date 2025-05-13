<html>
	<title>e-prev [Vídeo Player]</title>
	<head>
		<script src="<?php echo base_url(); ?>js/mediaplayer/swfobject.js" type="text/javascript"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-1.3.2.min.js"></script>
	</head>
<body>
	<table border="0" height="100%" width="100%" style="border-collapse: collapse; padding: 0px;" cellspacing="0" cellpadding="0">
		<tr>
			<td style="width: 23px; height: 23px; background: url('<?php echo base_url(); ?>img/tv_cima_esquerda.png') no-repeat;"></td>
			<td style="background: url('<?php echo base_url(); ?>img/tv_cima_centro.png') repeat-x;"></td>
			<td style="width: 23px; height: 23px; background: url('<?php echo base_url(); ?>img/tv_cima_direita.png') no-repeat;"></td>
		</tr>
		<tr>
			<td style="width: 23px; background: url('<?php echo base_url(); ?>img/tv_meio_esquerda.png') repeat-y;"></td>
			<td id='preview' style="background-color: #000000; color: #FFFFFF; font-family: Calibri, Arial; font-size: 16pt; text-align:center;">
				Para visualizar a TV E-prev é necessário que você instale o Adobe Flash Player, clique no icone abaixo para instalar.
				<BR><BR>
				<a href="http://www.adobe.com/go/getflashplayer_br" title="Clique para instalar do Abobe Flash Player"><img src="<?php echo base_url(); ?>img/get_adobe_flash_player.png" border="0"></a>
			</td>
			<td style="width: 23px; background: url('<?php echo base_url(); ?>img/tv_meio_direita.png') repeat-y;"></td>
		</tr>
		<tr>
			<td style="width: 23px; height: 23px; background: url('<?php echo base_url(); ?>img/tv_baixo_esquerda.png') no-repeat;"></td>
			<td style="background: url('<?php echo base_url(); ?>img/tv_baixo_centro.png') repeat-x;"></td>
			<td style="width: 23px; height: 23px; background: url('<?php echo base_url(); ?>img/tv_baixo_direita.png') no-repeat;"></td>
		</tr>
		<tr>
			<td colspan="3" style="height: 50px; background-image:url('<?php echo base_url(); ?>img/tv_pe.png'); background-repeat:no-repeat; background-position:center top;"></td>
		</tr>		
	</table>
	<script type="text/javascript">
		jQuery(document).ready(function($) 
		{
			var s1 = new SWFObject('<?php echo base_url(); ?>js/mediaplayer/player.swf','player','100%','100%','8');
				s1.addParam('allowfullscreen','true');
				s1.addParam('allowscriptaccess','always');
				s1.addParam('flashvars','file=<?php echo $row['video_link'];?>&autostart=true&logo=<?php echo base_url(); ?>js/mediaplayer/logo.png&backcolor=D4D6D9&frontcolor=000000&lightcolor=D4D6D9&screencolor=000000');
				s1.write('preview');		
			
			window.moveTo(0, 0);
			window.resizeTo(screen.availWidth,screen.availHeight);
			$(window).focus();
		});
	</script>
</body>
</html>