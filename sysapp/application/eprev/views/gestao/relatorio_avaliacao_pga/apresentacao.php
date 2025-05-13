<html>
	<head>
		<title> Indicadores de Gestão do PGA – Avaliação da Diretoria Executiva - <?= $relatorio_avaliacao_pga['nr_ano'].'/'.sprintf('%02d', $relatorio_avaliacao_pga['nr_trimestre']) ?></title>
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
		<script src="<?= base_url() ?>js/jquery-plugins/bootbox.min.js" type="text/javascript"></script>

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

			.well_print {
				min-height: 20px;
				padding: 19px;
				margin-bottom: 20px;
				background-color: #FFFFFF !important;
			}
		</style>

		<script>
			function get_cd_relatorio_avaliacao_pga(cd_indicador)
			{
				var indicador = new Array();
				<? $i = 1; ?>

				<? foreach ($indicador as $item): ?>
					indicador[<?= $item['cd_indicador'] ?>] = <?= $item['cd_relatorio_avaliacao_pga'] ?>;

					<? $i++; ?>
				<? endforeach; ?>
				
				return indicador[cd_indicador];
			}

			function get_cd_indicador(slide_numero)
			{
				var indicador = new Array();
				<? $i = 1; ?>

				<? foreach ($indicador as $item): ?>
					
					indicador[<?= $i ?>] = <?= $item['cd_indicador'] ?>;
					<? $i++; ?>

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
						$("#btn_avaliacao").hide();
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
						$("#btn_avaliacao").show();
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
						
					    $("#btn_avaliacao").hide();
					}

					if(slide_next > 0)
					{
						$("#btn_next").prop("disabled", false);
						$("#btn_forward").prop("disabled", false);
						$("#btn_avaliacao").show();
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
				
				$("#btn_avaliacao").hide();
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
				
				$("#btn_avaliacao").show();
			}
		
			function alterar_avaliacao()
			{
				var slide_atual = $("#slide_atual").val();
				cd_indicador = get_cd_indicador(slide_atual);

				$.post("<?= site_url('gestao/relatorio_avaliacao_pga/get_indicador_apresentacao') ?>", 
				{
					cd_indicador               : cd_indicador,
					cd_relatorio_avaliacao_pga : get_cd_relatorio_avaliacao_pga(cd_indicador)
				},
				function(data){
					bootbox.dialog({
						title: "Avaliação da Diretoria Executiva",
						message: "<textarea id='editar_avaliacao' rows='10' class='bootbox-input bootbox-input-textarea form-control'>" + data.ds_avaliacao + "</textarea>",
						size: 'large',
						buttons: {
							cancel: {
								label: 'Cancelar',
								className: 'btn-danger'
							},
							confirm: {
								label: 'Salvar',
								className: 'btn-success',
								callback: function (result) {
									if (result)
									{
										$.post("<?= site_url('gestao/relatorio_avaliacao_pga/alterar_avaliacao') ?>", 
										{
											cd_indicador               : cd_indicador,
											cd_relatorio_avaliacao_pga : get_cd_relatorio_avaliacao_pga(cd_indicador),
											ds_avaliacao			   : $("#editar_avaliacao").val()
										},
										function(data){
											load_slide(slide_atual);
										})
									}
								}
							}
						},
					})
				}, 'json');
			}
			
			function load_slide(slide)
			{
				var retorno;

				$("#slide_indicador").html("<div class='text-center'><?= loader_html() ?></div>");
				
				cd_indicador = get_cd_indicador(slide);

				$.post("<?= site_url('gestao/relatorio_avaliacao_pga/apresentacao_indicador') ?>", 
				{
					cd_indicador               : cd_indicador,
					cd_relatorio_avaliacao_pga : get_cd_relatorio_avaliacao_pga(cd_indicador)
				},
				function(data){
					
					$("#slide_indicador").html(data);

				}, 'html', true); 

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

			function ver_assinaturas()
			{
				$("#btn_apresentacao").show();
				$("#btn_assinatura").hide();

				$("#btn_backward").prop("disabled", true);
				$("#btn_prev").prop("disabled", true);
				$("#btn_next").prop("disabled", true);
				$("#btn_forward").prop("disabled", true);

				if($("#slide_atual").val() == 0)
				{
					$("#slide_apresentacao").hide();
				}
				else
				{
					$("#slide_indicador").hide();
				}

				$("#slide_assinatura").show();
			}

			function ver_apresentacao()
			{
				$("#btn_assinatura").show();
				$("#btn_apresentacao").hide();

				if($("#slide_atual").val() == 0)
				{
					$("#btn_next").prop("disabled", false);
					$("#btn_forward").prop("disabled", false);

					$("#slide_apresentacao").show();
				}
				else if (($("#slide_atual").val() > 0) && ($("#slide_atual").val() < $("#slide_final").val()))
				{
					$("#btn_backward").prop("disabled", false);
					$("#btn_prev").prop("disabled", false);
					$("#btn_next").prop("disabled", false);
					$("#btn_forward").prop("disabled", false);

					$("#slide_indicador").show();
				}
				else
				{
					$("#btn_prev").prop("disabled", false);
					$("#btn_backward").prop("disabled", false);

					$("#slide_indicador").show();
				}

				$("#slide_assinatura").hide();
			}

			function gera_image(i)
			{
				var slide_final = $("#slide_final").val();
				var slide_atual = $("#slide_atual").val();
				var slide_assinatura = $("#slide_assinatura").val();

				$("#pagina_item").removeClass('well');
				$("#pagina_item").addClass('well_print');

				html2canvas($("#pagina_item"), {
					onrendered: function(canvas) {
						$.post("<?= site_url('gestao/relatorio_avaliacao_pga/salvar_imagem') ?>", 
						{
							id_imagem    : i,
							nr_trimestre : <?= $relatorio_avaliacao_pga["nr_trimestre"] ?>,
							nr_ano       : <?= $relatorio_avaliacao_pga["nr_ano"] ?>,
							ob_imagem    : canvas.toDataURL('image/png')
						},
						function(data){
							var obj = data; 
							
							if(i < slide_final)
							{
								next();
								i++;
								setTimeout(gera_image, 3000, i);
							}	
							<? if(count($assinatura_diretores) > 0) : ?>
							else if(i == slide_final)
							{

								ver_assinaturas();
								i++;
								setTimeout(gera_image, 3000, i);
							}
							<? endif; ?>
							else
							{
								gera_pdf(i+1);
							}

							$("#pagina_item").removeClass('well_print');
							$("#pagina_item").addClass('well');
							
						});
					}
				});
			}

			function gera_pdf(qt)
			{
				location.href = "<?= site_url('gestao/relatorio_avaliacao_pga/gera_pdf/'.$relatorio_avaliacao_pga["nr_ano"]."/".$relatorio_avaliacao_pga['nr_trimestre']) ?>/" + qt;
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

				$("#btn_apresentacao").hide();http:
				$(document).keyup(operaEvento);
				
				bootbox.addLocale("pt-br", {
					OK      : "Salvar",
					CANCEL  : "Cancelar",
					CONFIRM : "Salvar"
				}).setLocale("pt-br");

				if(slide_atual == 0)
				{
					$("#btn_avaliacao").hide();
				}

				$("#btn_gera_pdf").on("click", function(e) {
					e.preventDefault();
					
					ver_apresentacao();

					primeiro();

					gera_image($("#slide_atual").val());
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
					<img src="<?=base_url() ?>img/certificado_logo_fundacao.png" style="width:180px"/>
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
				<? if(trim($relatorio_avaliacao_pga['dt_encerramento']) == ""): ?>
					<button type="button" data-toggle="tooltip" title="Avaliação da Diretoria" class="btn btn-default glyphicon glyphicon-pencil" onclick="alterar_avaliacao();" id="btn_avaliacao"></button>
				<? endif; ?>

				<? if(count($assinatura_diretores) > 0) : ?>
					<button type="button" data-toggle="tooltip" title="Assinaturas" class="btn btn-default glyphicon glyphicon-font" onclick="ver_assinaturas();" id="btn_assinatura"></button>
					<button type="button" data-toggle="tooltip" title="Apresentação" class="btn btn-default glyphicon glyphicon-film" onclick="ver_apresentacao();" id="btn_apresentacao"></button>
				<? endif; ?>

				<div class="btn-group" role="group" aria-label="Ações">
				  	<button type="button" title="Gerar PDF" data-toggle="tooltip" class="btn btn-default glyphicon glyphicon-print" id="btn_gera_pdf"></button>
				</div>

			</div>
			<div id="titulo" class="text-center">
				<h2 class="font_yannoka">
					Indicadores de Gestão do PGA – Avaliação da Diretoria Executiva <?= $relatorio_avaliacao_pga['nr_ano'].'/'.sprintf('%02d', $relatorio_avaliacao_pga['nr_trimestre'])?>	
				</h2>
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
								<? foreach ($indicador as $key => $item): ?>
									<li><h2 class="font_yannoka"><?= $item['ds_indicador']?></h2></li>
								<? endforeach; ?>
							</ul>
						</div>
					</div>
				<div id="slide_indicador" style="width:100%; display:none;"></div>
				<? if(count($assinatura_diretores) > 0) : ?>
				<div id="slide_assinatura" style="padding-left: 15%; width:80%; display:none; text-align: center">
					<div class="col-md-12" style="text-align: left; font-size: 25px;">
						De acordo,
				 	</div>
					<?foreach ($assinatura_diretores as $key => $item): ?>
						<div class=" col-md-6" style="text-align: center;">
							<? if(trim($item['usuario']) != '') : ?>
							<img src="<?= base_url('img/assinatura/'.$item['usuario'].'.png') ?>" style="width:400px; text-align: center;" />
							<? else: ?>
							<div style="height:25%;"></div>
							<? endif;?>
							<div style="top: -70px; position:relative">
						        <h2 class="font_yannoka" style="text-align: center; font-size: 35px;"><?= nl2br($item['nome_usuario_assinatura']) ?></h2>
						        <h2 class="font_yannoka" style="text-align: center; font-size: 35px;"><?= nl2br($item['diretoria']) ?></h2>
					        </div>
				        </div>
			    	<? endforeach; ?>
				</div>
				<? endif;?>
			</div>
		</div>
	</body>
</html>