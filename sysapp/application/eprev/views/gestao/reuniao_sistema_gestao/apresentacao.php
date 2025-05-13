<html>
	<head>
		<title>Reunião Sistema de Gestão - <?= $reuniao_sistema_gestao['dt_reuniao_sistema_gestao'] ?></title>
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
			function get_cd_reuniao_sistema_gestao_processo(cd_indicador)
			{
				var indicador = new Array();
				<? $i = 1; ?>

				<? foreach ($processo as $key => $item): ?>
					<? foreach ($item['indicador'] as $key2 => $item2): ?>
					indicador[<?= $item2['cd_indicador'] ?>] = <?= $item['cd_reuniao_sistema_gestao_processo'] ?>;

					<? $i++; ?>
					<? endforeach; ?>
				<? endforeach; ?>

				return indicador[cd_indicador];
			}

			function get_cd_indicador(slide_numero)
			{
				var indicador = new Array();
				<? $i = 1; ?>

				<? foreach ($processo as $key => $item): ?>
					<? foreach ($item['indicador'] as $key2 => $item2): ?>
					indicador[<?= $i ?>] = <?= $item2['cd_indicador'] ?>;

					<? $i++; ?>
					<? endforeach; ?>
				<? endforeach; ?>

				return indicador[slide_numero];
			}

			function next()
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

					load_slide(slide_next);
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

			function load_slide(slide)
			{
				$("#slide_indicador").html("<div class='text-center'><?= loader_html() ?></div>");

				cd_indicador = get_cd_indicador(slide);

				$.post("<?= site_url('gestao/reuniao_sistema_gestao/apresentacao_indicador') ?>", 
				{
					cd_indicador                       : cd_indicador,
					cd_reuniao_sistema_gestao_processo : get_cd_reuniao_sistema_gestao_processo(cd_indicador)
				},
				function(data){
					$("#slide_indicador").html(data);
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

			$(function(){
				var slide_atual = $("#slide_atual").val();
				var slide_final = $("#slide_final").val();
				$("#numero_paginacao").html("0/"+parseInt(slide_final));

				$("#btn_prev").prop("disabled", true);
				$("#btn_backward").prop("disabled", true);
				$("#btn_mais").prop("disabled", true);

				$(document).keyup(operaEvento);

				$("#dropdown_processo").change(function(){
					location.href = "<?= site_url('gestao/reuniao_sistema_gestao/apresentacao/'.$reuniao_sistema_gestao['cd_reuniao_sistema_gestao']) ?>/" + $(this).val();
				});
			});
		</script>

	</head>
	<body>
		<input type="hidden" name="slide_atual" id="slide_atual" value="0">
		<input type="hidden" name="slide_final" id="slide_final" value="<?= intval($qt_indicador) ?>">

		<div id="header">
			<div id="header_one">
				<div class="text-left">
					<img src="<?=base_url() ?>img/logo_ffp.png" style="width:180px;"/>
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

			  	<div class="btn-group" role="group" aria-label="Processos">
					<select class="form-control" id="dropdown_processo">
						<option value="">Processo</option>
						<? foreach ($dropdown_processo as $key => $item): ?>
						<option value="<?= $item['cd_processo'] ?>" <?= ($item['cd_processo'] == $cd_processo ? 'selected=""' : '') ?>><?= $item['processo'] ?></option>
						<? endforeach; ?>
					</select>
				</div>
		
			</div>
			<div id="titulo" class="text-center">
				<h2 class="font_yannoka">Reunião de Gestão - <?= $reuniao_sistema_gestao['ds_reuniao_sistema_gestao_tipo'] ?> - <?= $reuniao_sistema_gestao['dt_reuniao_sistema_gestao'] ?></h2>
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
						<h1 class="font_yannoka">Processos</h1>
					</div>
					<div id="div_processo">
						<ul>
							<? foreach ($processo as $key => $item): ?>
							<li><h2 class="font_yannoka"><?= $item['processo'] ?></h2></li>
							<? endforeach; ?>
						</ul>
					</div>
				</div>
				<div id="slide_indicador" style="width:100%; display:none;"></div>
			</div>
		</div>
	</body>
</html>