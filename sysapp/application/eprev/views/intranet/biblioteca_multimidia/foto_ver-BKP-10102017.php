<html>
	<head>
		<title>e-prev [Ver Fotos]</title>
		<link rel="stylesheet" href="<?php echo base_url(); ?>js/galleriffic-2.0/css/basic.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>js/galleriffic-2.0/css/galleriffic-2.css" type="text/css" />
		<script type="text/javascript" src="<?php echo base_url(); ?>js/galleriffic-2.0/js/jquery-1.3.2.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>js/galleriffic-2.0/js/jquery.galleriffic.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>js/galleriffic-2.0/js/jquery.opacityrollover.js"></script>
		<!-- We only want the thunbnails to display when javascript is disabled -->
		<script type="text/javascript">
			document.write('<style>.noscript { display: none; }</style>');
		</script>
	</head>
	<body>
<?php
	$nr_tam_thumb = 78;
	$nr_tam_foto  = 450;

	$caminho_foto  = "http://srvimagem:1111/img.php?t=".$nr_tam_foto."&i=".str_replace("\\","/",$row["ds_caminho"])."/";
	$caminho_thumb = "http://srvimagem:1111/img.php?t=".$nr_tam_thumb."&i=".str_replace("\\","/",$row["ds_caminho"])."/";
	$url_xml = "http://srvimagem:1111/foto.php?d=".$row["ds_caminho"];
	$xml = simplexml_load_file($url_xml) or die("Erro ao buscar fotos");
	$ar_xml = Array();
	$ar_xml = objectsIntoArray($xml);
	$ar_reg = $ar_xml['reg'];
	
/*
	echo "<PRE>";
	var_dump($xml);
	print_r($ar_reg);
	exit;
*/	
	
	$nr_fim   = count($ar_reg);
	$nr_conta = 0;
	$lista_foto = "";
	$nr_tamanho = 200;
	while($nr_conta < $nr_fim)
	{
		$lista_foto.= '
						<li>
							<a class="thumb" name="leaf" href="'.$caminho_foto.$ar_reg[$nr_conta].'" title="Foto '.($nr_conta+1).'">
								<img src="'.$caminho_thumb.$ar_reg[$nr_conta].'" alt="Foto '.($nr_conta+1).'">
							</a>
						</li>		
		              ';
		$nr_conta++;
	}
?>
	<body>
		<div id="page">
			<div id="container">
				<h2><?php echo $row["ds_titulo"]." (".$row["dt_data"].")";?></h2>
				<div id="gallery" class="content">
					<div id="controls" class="controls"></div>
					<div class="slideshow-container">
						<div id="loading" class="loader"></div>
						<div id="slideshow" class="slideshow"></div>
					</div>
					
				</div>
				<div id="thumbs" class="navigation">
					<ul class="thumbs noscript">
						<?php
							echo $lista_foto;
						?>
					</ul>
				</div>
				<div style="clear: both;"></div>
			</div>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function($) 
			{
				// We only want these styles applied when javascript is enabled
				$('div.navigation').css({'width' : '200px', 'float' : 'left'});
				$('div.content').css('display', 'block');

				// Initially set opacity on thumbs and add
				// additional styling for hover effect on thumbs
				var onMouseOutOpacity = 0.67;
				$('#thumbs ul.thumbs li').opacityrollover({
					mouseOutOpacity:   onMouseOutOpacity,
					mouseOverOpacity:  1.0,
					fadeSpeed:         'fast',
					exemptionSelector: '.selected'
				});
				
				// Initialize Advanced Galleriffic Gallery
				var gallery = $('#thumbs').galleriffic({
					delay:                     2500,
					numThumbs:                 8,
					preloadAhead:              10,
					enableTopPager:            true,
					enableBottomPager:         true,
					maxPagesToShow:            4,
					imageContainerSel:         '#slideshow',
					controlsContainerSel:      '#controls',
					captionContainerSel:       '#caption',
					loadingContainerSel:       '#loading',
					renderSSControls:          true,
					renderNavControls:         true,
					playLinkText:              'Iniciar Slideshow',
					pauseLinkText:             'Parar Slideshow',
					prevLinkText:              '&lsaquo;- Foto Anterior',
					nextLinkText:              'Próxima Foto -&rsaquo;',
					nextPageLinkText:          '&rsaquo;',
					prevPageLinkText:          '&lsaquo;',
					enableHistory:             false,
					autoStart:                 false,
					syncTransitions:           true,
					defaultTransitionDuration: 900,
					onSlideChange:             function(prevIndex, nextIndex) {
						// 'this' refers to the gallery, which is an extension of $('#thumbs')
						this.find('ul.thumbs').children()
							.eq(prevIndex).fadeTo('fast', onMouseOutOpacity).end()
							.eq(nextIndex).fadeTo('fast', 1.0);
					},
					onPageTransitionOut:       function(callback) {
						this.fadeTo('fast', 0.0, callback);
					},
					onPageTransitionIn:        function() {
						this.fadeTo('fast', 1.0);
					}
				});
				
				$(window).focus();
			});
		</script>
	</body>
</html>
<?php
	function objectsIntoArray($arrObjData, $arrSkipIndices = array())
	{
		$arrData = array();
	   
		// if input is object, convert into array
		if (is_object($arrObjData)) {
			$arrObjData = get_object_vars($arrObjData);
		}
	   
		if (is_array($arrObjData)) {
			foreach ($arrObjData as $index => $value) {
				if (is_object($value) || is_array($value)) {
					$value = objectsIntoArray($value, $arrSkipIndices); // recursive call
				}
				if (in_array($index, $arrSkipIndices)) {
					continue;
				}
				$arrData[$index] = $value;
			}
		}
		return $arrData;
	}
?>