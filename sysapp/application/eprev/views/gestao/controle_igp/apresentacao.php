<html>
	<head>
		<title><?= ($row['nr_ano'] >= 2024 ? 'Indicadores PE' : 'IGP') ?> - <?= $row['nr_ano'] ?></title>
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
				height: 97%;
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
				background:#21304f; 
				color:white; 
				font-weight:bold;
			}

			.indicador_table tbody .tr_par {
				background-color: #7bb0e7;
			}

		</style>

		<script>
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

				$("#btn_mais").prop("disabled", true);
				
				$("#dropdown_ano").change(function(){
					location.href = "<?= site_url('gestao/controle_igp/apresentacao') ?>/" + $(this).val();
				});

				$("#btn_gera_pdf").on("click", function(e) {
					location.href = "<?= site_url('gestao/controle_igp/apresentacao_pdf/'.$row['cd_controle_igp']) ?>";
			    });
				
			});
		</script>

	</head>
	<body>
		<input type="hidden" name="slide_atual" id="slide_atual" value="0">
		<input type="hidden" name="slide_final" id="slide_final" value="1">

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
			  	</div>

			  	<div class="btn-group" role="group" aria-label="Anos">
					<select class="form-control" id="dropdown_ano">
						<? foreach ($anos as $key => $item): ?>
						<option value="<?= $item['cd_controle_igp'] ?>" <?= ($item['nr_ano'] == $row['nr_ano'] ? 'selected=""' : '') ?>><?= $item['nr_ano'] ?></option>
						<? endforeach; ?>
					</select>
				</div>

				<div class="btn-group" role="group" aria-label="Ações">
			  		<button type="button" title="Gerar PDF" data-toggle="tooltip" class="btn btn-default glyphicon glyphicon-print" id="btn_gera_pdf"></button>
			  	</div>	
		
			</div>
			<div id="titulo" class="text-center">
				<h2 class="font_yannoka"><?= ($row['nr_ano'] >= 2024 ? 'Indicadores do Planejamento Estratégico' : 'Índice Geral de Performance - IGP') ?> - <?= $row['ds_referenfcia'] ?></h2>
			</div>
			<div id="numeracao"></div>
			<br/>
		</div>
		<br/>
		<div id="pagina">
			<div id="pagina_item" class="span12 well well-black" style="height:93%; margin-bottom:0px;">
				<div id="slide_apresentacao" style="width:100%;">
					<div id="tabela" class="text-center">
						<table class="table table-bordered table_table indicador_table">
							<thead>
								<tr>
									<td colspan="8"></td>
									<td colspan="2" class="text-center">Indicador</td>
									<td colspan="2" class="text-center"><?= ($row['nr_ano'] >= 2024 ? 'PE' : 'IGP') ?></td>
								</tr>
								<tr>
									<td class="text-center">Categoria</td>
									<td class="text-center">Resp.</td>
									<td class="text-center">Indicador</td>
									<td class="text-center">Melhor</td>
									<td class="text-center">Peso Indic.(%)</td>
									<td class="text-center">Unid.</td>
									<td class="text-center">Controle</td>
									<td class="text-center">Referência</td>
									<td class="text-center">Meta</td>
									<td class="text-center">Resultado</td>
									<td class="text-center">Meta Ponderada</td>
									<td class="text-center">Resultado Ponderado</td>
								</tr>

							</thead>
							<tbody>
							<?php 
								$nr_peso                = 0;
								$nr_resultado_ponderado = 0;
								$nr_ano                 = '';
							?>
							<?php foreach($collection as $key => $item): ?>

							<?php 

								$par = 'N';

								if(($key % 2) == 0)
								{
									$par = 'S';
								}

								$nr_peso                += $item['nr_peso'];
								$nr_resultado_ponderado += $item['nr_resultado_ponderado'];
								$nr_ano                  = $item['nr_ano'];
								
								if(trim($item['tp_analise']) == '+')
								{
									$color = 'success';

									if(floatval($item['nr_peso']) > floatval($item['nr_resultado_ponderado']))
									{
										$color = 'danger';
									}
								}
								else
								{
									$color = 'danger';

									if(floatval($item['nr_peso']) <= floatval($item['nr_resultado_ponderado']))
									{
										$color = 'success';
									}
								}

								$color_resultado = 'danger';

								if(floatval($nr_peso) <= floatval($nr_resultado_ponderado))
								{
									$color_resultado = 'success';
								}

							?>
							<tr <?= ($par == 'S' ? 'class="tr_par"' : 'class="tr_impar"') ?>>
								<td><?= $item['ds_controle_igp_categoria'] ?></td>
								<td><?= $item['cd_responsavel'] ?></td>
								<td><?= $item['indicador'] ?></td>
								<td><?= $item['ds_analise'] ?></td>
								<td class="text-right"><?= number_format($item['nr_peso'], 2, ',', '.') ?></td>
								<td><?= $item['ds_indicador_unidade_medida'] ?></td>
								<td><?= $item['ds_indicador_controle'] ?></td>
								<td class="text-center"><?= $item['ds_referencia_indicador'] ?></td>
								<td class="text-right"><?= number_format($item['nr_meta_indicador'], 2, ',', '.') ?></td>
								<td class="text-right"><?= number_format($item['nr_resultado_indicador'], 2, ',', '.') ?></td>
								<td class="text-right"><span class="text-info"><strong><?= number_format($item['nr_peso'], 2, ',', '.') ?></strong></span></td>
								<td class="text-right"><span class="text-<?= $color ?>"><strong><?= number_format($item['nr_resultado_ponderado'], 2, ',', '.') ?></strong></span></td>
							</tr>
							<?php endforeach; ?>
							</tbody>

							<tr style="background-color: #FFFF7F;">
								<td colspan="10"><strong>Resultado Acumulado <?= $nr_ano ?></strong></td>
								<td class="text-right"><span class="text-info"><strong><?= number_format($nr_peso, 2, ',', '.') ?></strong></span></td>
								<td class="text-right"><span class="text-<?= $color_resultado ?>"><strong><?= number_format($nr_resultado_ponderado, 2, ',', '.') ?></strong></span></td>
							</tr>

							<?php foreach($resultados as $key => $item): ?>
							<tr style="background-color: #FFFF7F;">
								<td colspan="10"><strong>Resultado Acumulado <?= $item['nr_ano'] ?></strong></td>
								<td class="text-right"><span class="text-info"><strong><?= number_format($item['nr_peso'], 2, ',', '.') ?></strong></span></td>
								<td class="text-right"><span class="text-<?= $color_resultado ?>"><strong><?= number_format($item['nr_resultado_ponderado'], 2, ',', '.') ?></strong></span></td>
							</tr>
							<?php endforeach; ?>
						</table>
					</div>
				</div>
		
			</div>
		</div>
	</body>
</html>