<html>
	<head>
		<title>Apresentação Indicadores PGA</title>
		<style type="text/css">
			@font-face {
				font-family: 'YanoneKaffeesatzRegular';
				src: url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.eot');
				src: url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.eot?#iefix') format('embedded-opentype'),
					 url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.woff') format('woff'),
					 url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.ttf') format('truetype'),
					 url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.svg#YanoneKaffeesatzRegular') format('svg');
				font-weight: normal;
				font-style: normal;
			}	
		</style>

		<link href="<?= base_url() ?>bootstrap-3.3.1/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="<?= base_url() ?>js/jquery-plugins/qtip2/jquery.qtip.min.css" rel="stylesheet" type="text/css" />
		<!--[if lte IE 8]>
		<script language="javascript" type="text/javascript" src="<?= base_url() ?>js/jquery-plugins/flot/excanvas.min.js"></script>
		<![endif]-->
		<script src="<?= base_url() ?>js/jquery-1.11.3.min.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>bootstrap-3.3.1/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/qtip2/jquery.qtip.min.js" type="text/javascript"></script>

		<script src="<?= base_url() ?>js/FileSaver/FileSaver.min.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/html2canvas/dist/html2canvas.js" type="text/javascript"></script>
		
		<style>
			* {
				margin: 0;
				padding: 0;
			}

			body {
			    font-family: Arial;
			}

			.font_yannoka {
		  		font-family: 'YanoneKaffeesatzRegular';
			}

			#header {
				margin: 2px;
				margin-bottom: 5px;
			}

			#header_one img {
				width: 300px;
			}

			#titulo {
			  	margin-top:-70px;
			}

			#acoes {
				position:relative; 
				float:left;
				left: 20px;
			}

			#pagina {
				height: 95%;
				width: 100%;
				position: relative;
			}

			#pagina .well-black {
				overflow-y : auto;
				overflow-x : auto;
			}

			#div_processo {
				margin-left: 300px;
			}

			#numeracao {
				position:relative; 
				float:right;
				right: 20px;
			}

			.indicador_table thead tr {
				background:#136813; 
				color:white; 
				font-weight:bold;
			}

			.indicador_table tbody .tr_par {
				background-color: #DFF0D8;
			}
		</style>

		<script>
			function get_cd_indicador(slide_numero)
			{
				var indicador = new Array();
				<? $i = 1; ?>

				<? foreach ($collection as $key => $item): ?>
					indicador[<?= $i ?>] = <?= $item['cd_indicador'] ?>;
					<? $i++; ?>
				<? endforeach; ?>

				return indicador[slide_numero];
			}

			function next(callback)
			{
				var slide_atual = $("#slide_atual").val();
				var slide_final = $("#slide_final").val();

				var slide_next =  parseInt(slide_atual)+1;

				if(slide_next <= slide_final)
				{
					if(slide_atual == 0)
					{
						$("#slide_apresentacao").hide();
						$("#slide_indicador").show();
					}

					if(slide_next == slide_final)
					{
						$("#btn_next").prop("disabled", true);
						$("#btn_forward").prop("disabled", true);
					}

					if(slide_next > 0)
					{
						$("#btn_prev").prop("disabled", false);
						$("#btn_backward").prop("disabled", false);
					}

					$("#numero_paginacao").html((parseInt(slide_next))+"/"+(parseInt(slide_final)));

					$("#slide_atual").val(slide_next);

					load_slide(slide_next, callback);
				}
			}

			function prev()
			{
				var slide_atual = $("#slide_atual").val();
				var slide_final = $("#slide_final").val();

				var slide_next =  parseInt(slide_atual)-1;

				if(slide_atual > 0)
				{
					if(slide_next == 0)
					{
						$("#slide_apresentacao").show();
						$("#slide_indicador").hide();

						$("#btn_prev").prop("disabled", true);
						$("#btn_backward").prop("disabled", true);
					}

					if(slide_next > 0)
					{
						$("#btn_next").prop("disabled", false);
						$("#btn_forward").prop("disabled", false);
					}

					$("#numero_paginacao").html((parseInt(slide_next))+"/"+(parseInt(slide_final)));

					$("#slide_atual").val(slide_next);

					if(slide_next > 0)
					{
						load_slide(slide_next);
					}
				}
			}

			function primeiro()
			{
				var slide_final = $("#slide_final").val();

				$("#slide_apresentacao").show();
				$("#slide_indicador").hide();

				$("#btn_next").prop("disabled", false);
				$("#btn_forward").prop("disabled", false);

				$("#btn_prev").prop("disabled", true);
				$("#btn_backward").prop("disabled", true);

				$("#numero_paginacao").html("0/"+(parseInt(slide_final)));

				$("#slide_atual").val(0);
			}

			function ultimo()
			{
				var slide_final = $("#slide_final").val();

				$("#slide_apresentacao").hide();
				$("#slide_indicador").show();

				$("#btn_next").prop("disabled", true);
				$("#btn_forward").prop("disabled", true);

				$("#btn_prev").prop("disabled", false);
				$("#btn_backward").prop("disabled", false);

				$("#numero_paginacao").html((parseInt(slide_final))+"/"+(parseInt(slide_final)));

				$("#slide_atual").val(slide_final);

				load_slide(slide_final);
			}

			function load_slide(slide, callback)
			{
				$("#slide_indicador").html("<div class='text-center'><?= loader_html() ?></div>");

				cd_indicador = get_cd_indicador(slide);

				$.post("<?= site_url('gestao/apresentacao_pga/indicador_result') ?>", 
				{
					cd_indicador : cd_indicador
				},
				function(data){
					$("#slide_indicador").html(data);

					if (typeof callback === "function") 
					{
						callback();
					}
				});
			}

			function operaEvento(evento)
			{
				if(evento.which == 39)
				{
					next();
				}
				else if(evento.which == 37)
				{
					prev();
				}
			}

			function menos()
			{
				$("#header_one").hide();
				$("#titulo").hide();

				$("#btn_menos").prop("disabled", true);
				$("#btn_mais").prop("disabled", false);
			}

			function mais()
			{
				$("#header_one").show();
				$("#titulo").show();

				$("#btn_mais").prop("disabled", true);
				$("#btn_menos").prop("disabled", false);
			}

			function gera_image(i)
			{
				var fun_image = function (){
					var nr_final        = $("#slide_final").val() - 1;
					var slide_indicador = $("#slide_indicador").width();
					var indicador_table = $(".indicador_table").width();
					var font_tabela     = 15;

					while(indicador_table > slide_indicador)
					{
						$(".indicador_table").css('font-size', font_tabela);

						font_tabela = font_tabela - 2;

						var indicador_table = $(".indicador_table").width();
					}

					html2canvas($("#slide_indicador"), {
						onrendered: function(canvas) {
							$.post("<?= site_url('gestao/apresentacao_pga/salvar_imagem') ?>", 
							{
								id_imagem : i,
								ob_imagem : canvas.toDataURL('image/png')
							},
							function(data){
								var obj = data; 

								if(i < nr_final)
								{
									i++;
									setTimeout(gera_image, 1200, i);
								}
								else
								{
									//$("#grafico_"+i+" .legend-box").css({'font-size':'250%'});
									
									gera_pdf((i+1));
								}
							});
						}
					});
				};
				
				next(fun_image);
			}

			function gera_pdf(qt)
			{
				location.href = "<?= site_url('gestao/apresentacao_pga/gera_pdf'); ?>/" + qt;
			}

			$(function(){
				var slide_atual = $("#slide_atual").val();
				var slide_final = $("#slide_final").val();
				$("#numero_paginacao").html("0/"+parseInt(slide_final));

				$("#btn_prev").prop("disabled", true);
				$("#btn_backward").prop("disabled", true);
				$("#btn_mais").prop("disabled", true);

				$(document).keyup(operaEvento);

				$("#btn_gera_pdf").on("click", function(e) {
					e.preventDefault();

					primeiro();
					
					gera_image($("#slide_atual").val());
			    });
			});
		</script>

	</head>
	<body>
		<input type="hidden" name="slide_atual" id="slide_atual" value="0">
		<input type="hidden" name="slide_final" id="slide_final" value="<?= count($collection) ?>">

		<div id="header">
			<div id="header_one">
				<div class="text-left">
					<img src="<?=base_url() ?>img/certificado_logo_fundacao.png" style="width:180px;"/>
				</div>
			</div>
			<br/>
			<div id="acoes">

				<div class="btn-group" role="group" aria-label="Navegação">
					<button type="button" data-toggle="tooltip" title="Menos" class="btn btn-default glyphicon glyphicon-chevron-up" onclick="menos();" id="btn_menos"></button>
					<button type="button" data-toggle="tooltip" title="Mais" class="btn btn-default glyphicon glyphicon-chevron-down" onclick="mais();" id="btn_mais"></button>
			  		<button type="button" data-toggle="tooltip" title="Primeiro Slide" class="btn btn-default glyphicon glyphicon-fast-backward" onclick="primeiro();" id="btn_backward"></button>
					<button type="button" data-toggle="tooltip" title="<< Anterior" class="btn btn-default glyphicon glyphicon-backward" onclick="prev();" id="btn_prev"></button>
					<button type="button" data-toggle="tooltip" title="Próximo >>" class="btn btn-default glyphicon glyphicon-forward" onclick="next();" id="btn_next"></button>
					<button type="button" data-toggle="tooltip" title="Último Slide" class="btn btn-default glyphicon glyphicon-fast-forward" onclick="ultimo();" id="btn_forward"></button>
			  	</div>

			  	<div class="btn-group" role="group" aria-label="Ações">
			  		<button type="button" title="Gerar PDF" data-toggle="tooltip" class="btn btn-default glyphicon glyphicon-print" id="btn_gera_pdf"></button>
			  	</div>	
		
			</div>
			<div id="titulo" class="text-center">
				<h2 class="font_yannoka">Apresentação Indicadores PGA</h2>
			</div>
			<div id="numeracao">
				<span id="numero_paginacao" class="badge" style="font-size:20px;"></span>
			</div>
			<br/>
		</div>
		<br/>
		<div id="pagina">
			<div id="pagina_item" class="span12 well well-black" style="height:93%; margin-bottom:0px;">
				<div id="slide_apresentacao" style="width:100%;">
					<div class="text-center">
						<h1 class="font_yannoka">Indicadores</h1>
					</div>
					<div id="div_processo">
						<ul>
							<? foreach ($collection as $key => $item): ?>
							<li><h2 class="font_yannoka"><?= $item['ds_indicador'] ?></h2></li>
							<? endforeach; ?>
						</ul>
					</div>
				</div>
				<div id="slide_indicador" style="width:100%; display:none;"></div>
			</div>
		</div>
	</body>
</html>