<?php 
#https://docs.webix.com/samples/63_kanban/01_basic/03_user_avatars.html 
#http://bootstrapdocs.com/v3.3.1/docs/getting-started/
?>
<html>
	<head>
		<title>Programas/Projetos/Ações/Iniciativas - Dashboard</title>
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

			@font-face {
				font-family: 'AldrichRegular';
				src: url('<?php echo base_url(); ?>fonts/Aldrich/aldrich-regular-webfont.eot');
				src: url('<?php echo base_url(); ?>fonts/Aldrich/aldrich-regular-webfont.eot?#iefix') format('embedded-opentype'),
					 url('<?php echo base_url(); ?>fonts/Aldrich/aldrich-regular-webfont.woff') format('woff'),
					 url('<?php echo base_url(); ?>fonts/Aldrich/aldrich-regular-webfont.ttf') format('truetype'),
					 url('<?php echo base_url(); ?>fonts/Aldrich/aldrich-regular-webfont.svg#AldrichRegular') format('svg');
				font-weight: normal;
				font-style: normal;
			}	

			
			@font-face {
				font-family: 'FrancoisOneRegular';
				src: url('<?php echo base_url(); ?>fonts/Francois_One/francoisone-webfont.eot');
				src: url('<?php echo base_url(); ?>fonts/Francois_One/francoisone-webfont.eot?#iefix') format('embedded-opentype'),
					 url('<?php echo base_url(); ?>fonts/Francois_One/francoisone-webfont.woff') format('woff'),
					 url('<?php echo base_url(); ?>fonts/Francois_One/francoisone-webfont.ttf') format('truetype'),
					 url('<?php echo base_url(); ?>fonts/Francois_One/francoisone-webfont.svg#AldrichRegular') format('svg');
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
		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.orderBars.js" type="text/javascript"></script>		
		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.symbol.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.navigate.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.pie.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.tooltip.min.js" type="text/javascript"></script>
		
		
		<script src="<?= base_url() ?>js/jquery-plugins/jquery.price_format.1.7.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/qtip2/jquery.qtip.min.js" type="text/javascript"></script>
		
		<script src="<?= base_url() ?>js/FileSaver/FileSaver.min.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/html2canvas/dist/html2canvas.js" type="text/javascript"></script>
		
		<script>
			function getCor(fl_situacao)
			{
				if(fl_situacao == "T")
				{
					//ATRASADO
					return "danger";
				}
				else if(fl_situacao == "A")
				{
					//ABERTO
					return "warning";
				}
				else if(fl_situacao == "X")
				{
					//EM ANDAMENTO
					return "info";
				}	
				else if(fl_situacao == "I")
				{
					//IMPLEMENTADO
					return "success";
				}
				else if(fl_situacao == "E")
				{
					//ENCERRADO
					return "default";
				}				
				else
				{
					return "";
				}
			}
		
			function getSituacao(fl_situacao)
			{
				
				
				$("#obCard"+fl_situacao).html("<?= loader_html() ?>");
				
				$.post("<?= site_url('gestao/pendencia_gestao/dashboardItem') ?>",
				{
					fl_situacao : fl_situacao
				},
				function(data)
				{
					$("#obCard"+fl_situacao).html("");
					var qt_item = 0;
					var item = "";
					var card = '<div class="bs-callout bs-callout-'+ getCor(fl_situacao) +'">';
						card +='	<table border="0" width="100%"> ';
						card +='		<tr height="35">';
						card +='			<td align="left" valign="middle"><h6>[DESCRICAO]</h6></td>';
						card +='		</tr>';
						card +='	</table>';
						card +='	<div class="row">';
						card +='		<div class="col-md-4"><p><span class="glyphicon glyphicon glyphicon-tag" aria-hidden="true" style="font-size: 11px;"></span> [NUMERO]</p></div>';
						card +='		<div class="col-md-4"><p><span class="glyphicon glyphicon glyphicon-user" aria-hidden="true" style="font-size: 11px;"></span> [AREA_RESPONSAVEL]</p></div>';
						card +='		<div class="col-md-4">[PRAZO]</div>';
						card +='	</div>';
						card +='	<div class="row">';
						card +='		<div class="col-md-3"><p><a href="<?= site_url('gestao/pendencia_gestao/acompanhamento') ?>/[CD_ITEM]" target="_blank"><span class="glyphicon glyphicon glyphicon-eye-open" aria-hidden="true" style="font-size: 11px;"></span> Detalhar</a></p></div>';
						card +='		<div class="col-md-3">[ARQUIVO]</div>';
						card +='		<div class="col-md-3">[CRONOGRAMA]</div>';
						card +='		<div class="col-md-3">[ANEXO]</div>';
						card +='	</div>';						
						card +='	<table border="0" width="100%" style="color: gray; margin-top: 5px;"> ';
						card +='		<tr height="35">';
						card +='			<td align="left" valign="middle" class="acompanhamento">[ACOMPANHAMENTO]</td>';
						card +='		</tr>';
						card +='	</table>';						
						card +='</div>';
					$.each(data, function(i, ob) 
					{
						var card_tmp = card;
							card_tmp = card_tmp.replace("[DESCRICAO]", ob.ds_item);
							card_tmp = card_tmp.replace("[NUMERO]", ob.cd_pendencia_gestao);
							card_tmp = card_tmp.replace("[CD_ITEM]", ob.cd_pendencia_gestao);
							card_tmp = card_tmp.replace("[AREA_RESPONSAVEL]", ob.ds_gerencia_responsavel);
							
							if(ob.dt_prazo == "#")
							{
								card_tmp = card_tmp.replace("[PRAZO]", '<p style="text-decoration: line-through;"><span class="glyphicon glyphicon glyphicon-time" aria-hidden="true" style="font-size: 11px;"></span> Sem Prazo</p>');
							}
							else
							{
								card_tmp = card_tmp.replace("[PRAZO]", '<p style="font-weight:bold;"><span class="glyphicon glyphicon glyphicon-time" aria-hidden="true" style="font-size: 11px;"></span> '+ob.dt_prazo+'</p>');
							}
							
							if(ob.ds_arquivo == "#")
							{
								card_tmp = card_tmp.replace("[ARQUIVO]", '<p style="text-decoration: line-through;"><span class="glyphicon glyphicon glyphicon-flash" aria-hidden="true" style="font-size: 11px;"></span> Plano</p>');
							}
							else
							{
								card_tmp = card_tmp.replace("[ARQUIVO]", '<p><a href="<?= base_url().'up/pendencia_gestao' ?>/'+ob.ds_arquivo+'" target="_blank"><span class="glyphicon glyphicon glyphicon-flash" aria-hidden="true" style="font-size: 11px;"></span> Plano</a></p>');
							}
							
							if(ob.qt_anexo == 0)
							{
								card_tmp = card_tmp.replace("[ANEXO]", '<p style="text-decoration: line-through;"><span class="glyphicon glyphicon glyphicon-paperclip" aria-hidden="true" style="font-size: 11px;"></span> Anexos (0)</p>');
							}
							else
							{
								card_tmp = card_tmp.replace("[ANEXO]", '<p><a href="<?= site_url('gestao/pendencia_gestao/anexo') ?>/'+ob.cd_pendencia_gestao+'" target="_blank"><span class="glyphicon glyphicon glyphicon-paperclip" aria-hidden="true" style="font-size: 11px;"></span> Anexos ('+ob.qt_anexo+')</a></p>');
							}

							if(ob.qt_cronograma == 0)
							{
								card_tmp = card_tmp.replace("[CRONOGRAMA]", '<p style="text-decoration: line-through;"><span class="glyphicon glyphicon glyphicon-calendar" aria-hidden="true" style="font-size: 11px;"></span>Cronograma</p>');
							}
							else
							{
								card_tmp = card_tmp.replace("[CRONOGRAMA]", '<p><a href="<?= base_url().'up/pendencia_gestao' ?>/'+ob.arquivo_cronograma+'" target="_blank"><span class="glyphicon glyphicon glyphicon-calendar" aria-hidden="true" style="font-size: 11px;"></span>Cronograma</a></p>');
							}
							
							card_tmp = card_tmp.replace("[ACOMPANHAMENTO]", ob.ds_acompanhamento);
						
						item += card_tmp;
						
						qt_item += 1;
					});					
					
					$("#obCardQuant"+fl_situacao).html(qt_item);
					$("#obCard"+fl_situacao).html(item);
				},
				'json');
			}
			
			function legendFormatter(label, series) {
				return '<div style="font-size:10pt;text-align:left;padding:2px;">' + label + ' (' + Math.round(series.percent)+'%)</div>';
			};				
			
			function getQuantidades()
			{
				$.post("<?= site_url('gestao/pendencia_gestao/dashboardQTItem') ?>",
				{ },
				function(data)
				{
					console.log(data);
					
					grafico(data);
				},
				'json');				
			}
			
			function grafico(obj)
			{
				var data = [
					{label: "Atrasado",      data: obj.qt_atrasado,     color: '#d9534f'},
					{label: "Aberto",        data: obj.qt_aberto,       color: '#f0ad4e'},
					{label: "Em Andamento",  data: obj.qt_execuntado,   color: '#5bc0de'},
					{label: "Implementado",  data: obj.qt_implementado, color: '#449d44'},
					{label: "Encerrado",     data: obj.qt_encerrado,    color: '#808080'}
				];
					
					
				var options = {
					series: {
						pie: {
							show: true,
							radius: 1,
							threshold: 0.1,
							label: {
								show: false
							}
						}
					},
					grid: {
						hoverable: true
					},
					tooltip: true,
					tooltipOpts: {
						cssClass: "flotTip",
						content: "%p.0%: %s",
						shifts: {
							x: 20,
							y: 0
						},
						defaultTheme: false
					},
					legend: {
						show: true, 
						noColumns: 1,
						container: $("#chartLegend"),		
						labelFormatter: legendFormatter
					}
				};

				$.plot($("#placeholder"), data, options);					
			}
			
			$(function() {
				getSituacao("T");
				getSituacao("A");
				getSituacao("X");
				getSituacao("I");
				getSituacao("E");
				
				getQuantidades();
			});
		
		</script>
		<style>
			body {
				margin : 10px;
				/*background-color: #f5f5f5;*/
				background-color: #ddd;
			}
			
			.panel {
				    background-color: #f5f5f5;
			}
			
			.panel-title {
				font-family: 'FrancoisOneRegular';
				font-weight: bold;
				font-size: 140%;
			}
			
			.panel-body-context {
				overflow-y: scroll;
				height: 60%;
			}
			
			.bs-calloutx {
				padding: 10px;
				margin: 15px 0;
				border: 1px solid #B1B1B1;
				border-left-width: 5px;
				border-radius: 3px;
				background-color: #ffffff;
			}

			.bs-callout {
				box-sizing: border-box;
				background-color: #fff;
				padding: 10px;
				margin: 15px 0;
				border-radius: 2px;
				border: 1px solid #e2e2e2;
				border-left: 3px solid #27ae60;
				box-shadow: 2px 2px 3px #ddd;		
			}

			.bs-callout h5{color: gray; margin-top:0;margin-bottom:5px; font-size: 14px; font-weight: bold;}
			.bs-callout h6{ font-family: 'YanoneKaffeesatzRegular'; color: #514D4D; margin-top:0;margin-bottom:5px; font-size: 20px; font-weight: bold;}
			.bs-callout p:last-child{  margin-bottom:0; font-size: 14px; white-space: nowrap;}
			.bs-callout code{border-radius:3px}
			.bs-callout+
			.bs-callout{margin-top:-5px}
			.bs-callout-default{border-left-color:#808080}
			.bs-callout-default h55{color:#D9D9D9}
			.bs-callout-danger{border-left-color:#d9534f}
			.bs-callout-danger h55{color:#d9534f}			
			.bs-callout-warning{border-left-color:#f0ad4e}
			.bs-callout-warning h55{color:#f0ad4e}
			.bs-callout-info{border-left-color:#5bc0de}
			.bs-callout-info h55{color:#5bc0de}	
			.bs-callout-success{border-left-color:#449d44}
			.bs-callout-success h55{color:#449d44}	
			.bs-callout-primary{border-left-color:#337ab7}
			
			.navbar-primary {
				background-color: #337ab7;
				border-color: #08080;
				
			}			
			
			.panel-default {
				border-color: #CACACA;
			}
			
			.panel-default>.panel-heading {
				color: #808080;
				background-color: #D9D9D9;
				border-color: #CACACA;
			}			
			
			.acompanhamento {
				font-family: 'YanoneKaffeesatzRegular';
				font-size: 17px;
			}
			
			.table{
				font-size: 90%;
			}
			
			.table th {
				text-align: center;
			}
			
			#flotTip {
				padding: 3px 5px;
				background-color: #000;
				z-index: 100;
				color: #fff;
				opacity: .80;
				filter: alpha(opacity=85);
			}
		</style>
	</head>
	<body>
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="javascript: location.reload();" style="color: #08080; font-family: 'AldrichRegular'">Programas/Projetos/Ações/Iniciativas - Dashboard</a>
			</div>
		</div>
    </nav>
	<div style="height: 50px;"></div>
	
		<div class="row">
			<div class="col-md-4">
			
				<div class="panel panel-danger">
					<div class="panel-heading">
						<h3 class="panel-title">ATRASADO (<span id="obCardQuantT"></span>)</h3>
					</div>
					<div class="panel-body panel-body-context">
						<div class="panel-body-conteudo" id="obCardT">
						
						</div>						
					</div>
				</div>	
				
			</div>			
		
			<div class="col-md-4">
			
				<div class="panel panel-warning">
					<div class="panel-heading">
						<h3 class="panel-title">ABERTO (<span id="obCardQuantA"></span>)</h3>
					</div>
					<div class="panel-body panel-body-context">
						<div class="panel-body-conteudo" id="obCardA">
						
						</div>						
					</div>
				</div>	
				
			</div>
			
			<div class="col-md-4">
			
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title">EM ANDAMENTO (<span id="obCardQuantX"></span>)</h3>
					</div>
					<div class="panel-body panel-body-context">
						<div class="panel-body-conteudo" id="obCardX">
						
						</div>					

					</div>
				</div>	
				
			</div>			
		</div>	
	
	
		<div class="row">
			<div class="col-md-4">
			
				<div class="panel panel-success">
					<div class="panel-heading">
						<h3 class="panel-title">IMPLEMENTADO (<span id="obCardQuantI"></span>)</h3>
					</div>
					<div class="panel-body panel-body-context" style="height: 21%;">
						<div class="panel-body-conteudo" id="obCardI">
						
						</div>
					</div>
				</div>				
			
			</div>			
		
			<div class="col-md-4">
			
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">ENCERRADO (<span id="obCardQuantE"></span>)</h3>
					</div>
					<div class="panel-body panel-body-context" style="height: 21%;">
						<div class="panel-body-conteudo" id="obCardE">
						
						</div>						
					</div>
				</div>	
				
			</div>
			
			<div class="col-md-4">
			
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">RESUMO</h3>
					</div>
					<div class="panel-body panel-body-context" style="height: 21%;">
						<div class="panel-body-conteudo">
						
							<div class="row">
								<div class="col-md-1">
								</div>
								<div class="col-md-5">
									<BR>
									<div id="chartLegend"></div>
								</div>								
								<div class="col-md-6">
									<div id="placeholder" class="demo-placeholder" style="text-align:center; width: 148px; height: 148px;"></div>
								</div>
							</div>
						</div>						
					</div>
				</div>	
				
			</div>			
		</div>
	</body>
</html>	
