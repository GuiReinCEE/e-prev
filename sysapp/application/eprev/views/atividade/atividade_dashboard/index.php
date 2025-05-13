<?php 
#https://docs.webix.com/samples/63_kanban/01_basic/03_user_avatars.html 
?>
<html>
	<head>
		<title>Atividade Dashboard</title>
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
		<script src="<?= base_url() ?>js/jquery-plugins/jquery.price_format.1.7.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/qtip2/jquery.qtip.min.js" type="text/javascript"></script>
		
		<script src="<?= base_url() ?>js/FileSaver/FileSaver.min.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/html2canvas/dist/html2canvas.js" type="text/javascript"></script>
		
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
			.bs-callout h6{ font-family: 'YanoneKaffeesatzRegular'; color: #514D4D; margin-top:0;margin-bottom:5px; font-size: 18px; font-weight: bold;}
			.bs-callout p:last-child{  margin-bottom:0; font-size: 14px; white-space: nowrap;}
			.bs-callout code{border-radius:3px}
			.bs-callout+
			.bs-callout{margin-top:-5px}
			.bs-callout-danger{border-left-color:#d9534f}
			.bs-callout-danger h55{color:#d9534f}			
			.bs-callout-danger{border-left-color:#d9534f}
			.bs-callout-danger h55{color:#d9534f}
			.bs-callout-warning{border-left-color:#f0ad4e}
			.bs-callout-warning h55{color:#f0ad4e}
			.bs-callout-info{border-left-color:#5bc0de}
			.bs-callout-info h55{color:#5bc0de}	
			.bs-callout-success{border-left-color:#449d44}
			.bs-callout-success h55{color:#449d44}	
			.bs-callout-primary{border-left-color:#337ab7}
			
			
			.table{
				font-size: 90%;
			}
			
			.table th {
				text-align: center;
			}
		</style>
	</head>
	<body>
	

	
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">RESUMO</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-2">
						<div class="bs-callout bs-callout-primary">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>Período</th>
										<th>Atividades</th>
										<th title="Tempo Médio de Atendimento em dias úteis">TMA</th>
										<th title="Tempo Médio de Operação em dias úteis">TMO</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Últ. ano</td>
										<td align="center"><?php echo number_format($ar_tm_1ano['qt_atividade'], 0, ',', '.');?></td>
										<td align="center"><?php echo number_format($ar_tm_1ano['qt_dias_uteis_tma'], 0, ',', '.');?></td>
										<td align="center"><?php echo number_format($ar_tm_1ano['qt_dias_uteis_tmo'], 0, ',', '.');?></td>
									</tr>
									<tr>
										<td>Últ. 3 anos</td>
										<td align="center"><?php echo number_format($ar_tm_3ano['qt_atividade'], 0, ',', '.');?></td>
										<td align="center"><?php echo number_format($ar_tm_3ano['qt_dias_uteis_tma'], 0, ',', '.');?></td>
										<td align="center"><?php echo number_format($ar_tm_3ano['qt_dias_uteis_tmo'], 0, ',', '.');?></td>
									</tr>
									<tr>
										<td>Últ. 5 anos</td>
										<td align="center"><?php echo number_format($ar_tm_5ano['qt_atividade'], 0, ',', '.');?></td>
										<td align="center"><?php echo number_format($ar_tm_5ano['qt_dias_uteis_tma'], 0, ',', '.');?></td>
										<td align="center"><?php echo number_format($ar_tm_5ano['qt_dias_uteis_tmo'], 0, ',', '.');?></td>
									</tr>
								</tbody>
							</table>
							
						</div>					
					</div>
					<div class="col-md-3">
						<div class="bs-callout bs-callout-primary">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>Período</th>
										<th>Abertas</th>
										<th>Encerradas</th>
										<th>Resultado</th>
									</tr>
								</thead>
								<tbody> 
									<tr>
										<td>Mês atual</td>
										<td align="center"><?php echo number_format($ar_01meses_aberta['qt_atividade'], 0, ',', '.');?></td>
										<td align="center"><?php echo number_format($ar_01meses_encerrada['qt_atividade'], 0, ',', '.');?></td>
										<td align="center"><?php echo number_format((($ar_01meses_encerrada['qt_atividade']/(intval($ar_01meses_aberta['qt_atividade']) == 0 ? 1 : $ar_01meses_aberta['qt_atividade'])) * 100), 2, ',', '.');?>%</td>
									</tr>
									<tr>
										<td>Últ. 3 meses</td>
										<td align="center"><?php echo number_format($ar_03meses_aberta['qt_atividade'], 0, ',', '.');?></td>
										<td align="center"><?php echo number_format($ar_03meses_encerrada['qt_atividade'], 0, ',', '.');?></td>
										<td align="center"><?php echo number_format((($ar_03meses_encerrada['qt_atividade']/$ar_03meses_aberta['qt_atividade']) * 100), 2, ',', '.');?>%</td>
									</tr>
									<tr>
										<td>Últ. 12 meses</td>
										<td align="center"><?php echo number_format($ar_12meses_aberta['qt_atividade'], 0, ',', '.');?></td>
										<td align="center"><?php echo number_format($ar_12meses_encerrada['qt_atividade'], 0, ',', '.');?></td>
										<td align="center"><?php echo number_format((($ar_12meses_encerrada['qt_atividade']/$ar_12meses_aberta['qt_atividade']) * 100), 2, ',', '.');?>%</td>
									</tr>
								</tbody>
							</table>
						</div>					
					</div>
					<div class="col-md-2">
						<div class="bs-callout bs-callout-primary">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>Período</th>
										<th>Ano Aberta</th>
										<th>Percentual</th>
									</tr>
								</thead>
								<tbody>
									<?php
										foreach($ar_ano as $item)
										{
											echo '
													<tr>
														<td>'.$item['ano'].'</td>
														<td align="center">'.number_format($item['qt_atividade'], 0, ',', '.').'</td>
														<td align="center">'.number_format($item['pr_atividade'], 2, ',', '.').'%</td>
													</tr>
									             ';
										}
									?>
								</tbody>
							</table>
							
						</div>					
					</div>					
					<div class="col-md-1">
						<div class="bs-callout bs-callout-primary">
							<span style="font-size: 85%; font-weight: bold;">Em Teste p/ mês<br></span>
							<?php
							
								foreach($ar_teste_ano_mes as $item)
								{
									echo $item['ano_mes_limite'].": ".$item['qt_atividade'].br();
								}							
							
							
								#foreach($ar_area as $item)
								#{
								#	$ar_titulo[] = $item['gerencia'];
								#	$ar_dado[] = $item['qt_atividade'];
								#}	
							
								#$configuracao = Array('Xlabel' => '','Ylabel' => '', 'TextboxFontSize' => 10, 'Textbox' => '');
								#$image = $this->charts->cartesianChart('bar',$ar_titulo,$ar_dado,350,150,'', $configuracao);	
								#echo '<img src="'.$image['name'].'" border="0">';	
							?>
											
						</div>						
					</div>
					<div class="col-md-2">
						<div class="bs-callout bs-callout-primary">
							<span style="font-size: 85%; font-weight: bold;">Atividades por área (em andamento)<br></span>
							<?php
								$ar_dado['AI']   = Array('qt_atividade'=>'','pr_atividade'=>'');
								$ar_dado['GC']   = Array('qt_atividade'=>'','pr_atividade'=>'');
								$ar_dado['GCM']  = Array('qt_atividade'=>'','pr_atividade'=>'');
								$ar_dado['GFC']  = Array('qt_atividade'=>'','pr_atividade'=>'');
								$ar_dado['GGPA'] = Array('qt_atividade'=>'','pr_atividade'=>'');
								$ar_dado['GIN']  = Array('qt_atividade'=>'','pr_atividade'=>'');
								$ar_dado['GJ']   = Array('qt_atividade'=>'','pr_atividade'=>'');
								$ar_dado['GP']   = Array('qt_atividade'=>'','pr_atividade'=>'');
								$ar_dado['GRC']  = Array('qt_atividade'=>'','pr_atividade'=>'');
								$ar_dado['GTI']  = Array('qt_atividade'=>'','pr_atividade'=>'');
								$ar_dado['DE']   = Array('qt_atividade'=>'','pr_atividade'=>'');
								foreach($ar_area as $item)
								{
									#echo $item['gerencia'].": ".$item['qt_atividade']." (".number_format($item['pr_atividade'], 1, ',', '.').'%)'.br();
									
									$ar_dado[$item['gerencia']] = array('qt_atividade' => $item['qt_atividade'], 'pr_atividade' => "(".number_format($item['pr_atividade'], 0, ',', '.')."%)");
								}							
							
							?>
									
								<div class="row">
									<div class="col-md-2">
										DE<br>
										GC<br>
										GCM<br>
										GFC<br>
										GGPA<br>
										GIN<br>
									</div>
									<div class="col-md-4">
										<?php
											echo $ar_dado['DE']['qt_atividade']." ".$ar_dado['DE']['pr_atividade'].br();
											echo $ar_dado['GC']['qt_atividade']." ".$ar_dado['GC']['pr_atividade'].br();
											echo $ar_dado['GCM']['qt_atividade']." ".$ar_dado['GCM']['pr_atividade'].br();
											echo $ar_dado['GFC']['qt_atividade']." ".$ar_dado['GFC']['pr_atividade'].br();
											echo $ar_dado['GGPA']['qt_atividade']." ".$ar_dado['GGPA']['pr_atividade'].br();
										?>
									</div>	
									
									<div class="col-md-2">
										<BR>
										GJ<br>
										GP<br>
										GRC<br>
										GTI<br>
										AI<br>
									</div>
									<div class="col-md-4">
										<br>
										<?php
											echo $ar_dado['GJ']['qt_atividade']." ".$ar_dado['GJ']['pr_atividade'].br();
											echo $ar_dado['GP']['qt_atividade']." ".$ar_dado['GP']['pr_atividade'].br();
											echo $ar_dado['GRC']['qt_atividade']." ".$ar_dado['GRC']['pr_atividade'].br();
											echo $ar_dado['GTI']['qt_atividade']." ".$ar_dado['GTI']['pr_atividade'].br();
											echo $ar_dado['AI']['qt_atividade']." ".$ar_dado['AI']['pr_atividade'].br();
										?>
									</div>									
									
								</div>



									
						</div>						
					</div>					
					<div class="col-md-2">
						<div class="bs-callout bs-callout-primary">
							<span style="font-size: 85%; font-weight: bold;">Monitoramento<br></span>
							<div class="row">
								<div class="col-md-6">					
									<?php
										echo "Backlog:".br();
										echo "Andamento:".br();
										echo "Tot Andamento:".br();
										echo "Teste:".br();
										echo "Agd User:".br();
										echo "Total Geral:".br();
									?>								
								</div>
								<div class="col-md-6">					
									<?php
										echo count($ar_backlog).br();
										echo count($ar_andamento).br();
										echo count($ar_backlog)+count($ar_andamento).br();
										echo count($ar_teste).br();
										echo count($ar_usuario).br();
										echo (count($ar_backlog) + count($ar_andamento) + count($ar_teste) + count($ar_usuario)).br();
									?>									
								</div>								
							</div>
						</div>						
					</div>					
				</div>
				
				<div class="row">
					<div class="col-md-4">
						
						<div class="bs-callout bs-callout-primary">
							<span style="font-size: 85%; font-weight: bold;">Categoria (total)<br></span>
							<div class="row">
								<div class="col-md-9">					
									<?php
										foreach($ar_categoria as $item)
										{
											echo $item['ds_atividade_classificacao'].br();
										}
										echo "Total:".br();
									?>	
								</div>
								<div class="col-md-1">					
									<?php
										$qt_geral = 0;
										foreach($ar_categoria as $item)
										{
											echo number_format($item['qt_atividade'], 0, ',', '.').br();
											$qt_geral+=intval($item['qt_atividade']);
										}
										echo number_format($qt_geral, 0, ',', '.').br();
									?>									
								</div>	
								<div class="col-md-2">					
									<?php
										$qt_geral = 0;
										foreach($ar_categoria as $item)
										{
											echo "(".number_format($item['pr_atividade'], 0, ',', '.')."%)".br();
											$qt_geral+=intval($item['qt_atividade']);
										}
									?>									
								</div>								
							</div>
						</div>							
					</div>	
					<div class="col-md-4">	
						<div class="bs-callout bs-callout-primary">
							<span style="font-size: 85%; font-weight: bold;">Categoria (ano)<br></span>
							<div class="row">
								<div class="col-md-9">					
									<?php
										foreach($ar_categoria_ano as $item)
										{
											echo $item['ds_atividade_classificacao'].br();
										}
										echo "Total:".br();
									?>	
								</div>
								<div class="col-md-1">					
									<?php
										$qt_geral = 0;
										foreach($ar_categoria_ano as $item)
										{
											echo number_format($item['qt_atividade'], 0, ',', '.').br();
											$qt_geral+=intval($item['qt_atividade']);
										}
										echo number_format($qt_geral, 0, ',', '.').br();
									?>									
								</div>		
								<div class="col-md-1">					
									<?php
										$qt_geral = 0;
										foreach($ar_categoria_ano as $item)
										{
											echo "(".number_format($item['pr_atividade'], 0, ',', '.')."%)".br();
											$qt_geral+=intval($item['qt_atividade']);
										}
									?>									
								</div>									
							</div>
						</div>							
					</div>
					<div class="col-md-4">	
						<div class="bs-callout bs-callout-primary">
							<span style="font-size: 85%; font-weight: bold;">Categoria (ano | desenvolvimento)<br></span>
							<div class="row">
								<div class="col-md-9">					
									<?php
										foreach($ar_categoria_ano_desenv as $item)
										{
											echo $item['ds_atividade_classificacao'].br();
										}
										echo "Total:".br();
									?>	
								</div>
								<div class="col-md-1">					
									<?php
										$qt_geral = 0;
										foreach($ar_categoria_ano_desenv as $item)
										{
											echo number_format($item['qt_atividade'], 0, ',', '.').br();
											$qt_geral+=intval($item['qt_atividade']);
										}
										echo number_format($qt_geral, 0, ',', '.').br();
									?>									
								</div>		
								<div class="col-md-1">					
									<?php
										$qt_geral = 0;
										foreach($ar_categoria_ano_desenv as $item)
										{
											echo "(".number_format($item['pr_atividade'], 0, ',', '.')."%)".br();
											$qt_geral+=intval($item['qt_atividade']);
										}
									?>									
								</div>									
							</div>
						</div>							
					</div>					
				</div>
				
				<span style="font-size: 9px;"><?php echo date("Y-m-d H:i:s");?></span>
			</div>
		</div>
	
		<div class="row">
			<div class="col-md-3">
			
				<div class="panel panel-success">
					<div class="panel-heading">
						<h3 class="panel-title">BACKLOG (<?php echo count($ar_backlog);?>)</h3>
					</div>
					<div class="panel-body panel-body-context">
						<?php
							$ar_titulo = Array();
							$ar_dado = Array();
							$ar_image = Array();
							foreach($ar_backlog_area as $item)
							{
								$ar_titulo[] = $item['area_solicitante'];
								$ar_dado[] = $item['quantidade'];	
							}
						
							$ar_image = $this->charts->pieChart(55,$ar_dado,$ar_titulo,'','');	
							echo '<center><img src="'.$ar_image['name'].'" border="0"></center>';
							
							echo '<div class="panel-body-conteudo">';
							foreach($ar_backlog as $item)
							{
								echo '
										<div class="bs-callout bs-callout-success"> 
											<table border="0" width="100%"> 
												<tr height="35">
													<td align="left" valign="top"><h6>'.$item['assunto'].'</h6></td>
													<td width="40" align="right" valign="top"><img class="rounded-circle" height="30" width="30" src="'.base_url().'up/avatar/'.$item['ds_atendente_avatar'].'" alt="'.$item['ds_atendente'].'" title="'.$item['ds_atendente'].'"></td>
												</tr>
											</table>
											<table border="0" width="100%" style="color: gray;"> 
												<tr height="35">
													<td width="70" align="center" valign="bottom"><p><span class="glyphicon glyphicon glyphicon-tag" aria-hidden="true" style="font-size: 11px;"></span> '.$item['numero'].'</p></td>
													<td width="40" align="center" valign="bottom"><p><span class="glyphicon glyphicon glyphicon-pushpin" aria-hidden="true" style="font-size: 11px;"></span> '.$item['area_solicitante'].'</p> </td>
													<td width="50" align="center" valign="bottom"><p><span class="glyphicon glyphicon glyphicon-star" aria-hidden="true" style="font-size: 11px;"></span> '.$item['nr_prioridade'].'</p> </td>
													<td width="60" align="center" valign="bottom"><p><span class="glyphicon glyphicon glyphicon-calendar" aria-hidden="true" style="font-size: 11px;"></span> '.$item['dt_cadastro_min'].'</p> </td>
													<td width="40" align="right"  valign="bottom"><img class="corner iradius24" height="30" width="30" src="'.base_url().'up/avatar/'.$item['ds_solicitante_avatar'].'" alt="'.$item['ds_solicitante'].'" title="'.$item['ds_solicitante'].'"></td>
												</tr>								
											</table>
										</div>									
								     ';
							}
							echo '</div>';
						?>
					</div>
				</div>				
			
			</div>
			<div class="col-md-3">
			
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title">EM ANDAMENTO (<?php echo count($ar_andamento);?>)</h3>
					</div>
					<div class="panel-body panel-body-context">
						<?php
							$ar_titulo = Array();
							$ar_dado = Array();
							$ar_image = Array();
							foreach($ar_andamento_area as $item)
							{
								$ar_titulo[] = $item['area_solicitante'];
								$ar_dado[] = $item['quantidade'];	
							}
						
							$ar_image = $this->charts->pieChart(55,$ar_dado,$ar_titulo,'','');	
							echo '<center><img src="'.$ar_image['name'].'" border="0"></center>';						
						
							echo '<div class="panel-body-conteudo">';
							foreach($ar_andamento as $item)
							{
								echo '
										<div class="bs-callout bs-callout-info"> 
											<table border="0" width="100%"> 
												<tr height="35">
													<td align="left" valign="top"><h6>'.$item['assunto'].'</h6></td>
													<td width="40" align="right" valign="top"><img class="corner iradius24" height="30" width="30" src="'.base_url().'up/avatar/'.$item['ds_atendente_avatar'].'" alt="'.$item['ds_atendente'].'" title="'.$item['ds_atendente'].'"></td>
												</tr>
											</table>
											<table border="0" width="100%" style="color: gray;"> 
												<tr height="35">
													<td width="70" align="center" valign="bottom"><p><span class="glyphicon glyphicon glyphicon-tag" aria-hidden="true" style="font-size: 11px;"></span> '.$item['numero'].'</p></td>
													<td width="40" align="center" valign="bottom"><p><span class="glyphicon glyphicon glyphicon-pushpin" aria-hidden="true" style="font-size: 11px;"></span> '.$item['area_solicitante'].'</p> </td>
													<td width="50" align="center" valign="bottom"><p><span class="glyphicon glyphicon glyphicon-star" aria-hidden="true" style="font-size: 11px;"></span> '.$item['nr_prioridade'].'</p> </td>
													<td width="60" align="center" valign="bottom"><p><span class="glyphicon glyphicon glyphicon-calendar" aria-hidden="true" style="font-size: 11px;"></span> '.$item['dt_cadastro_min'].'</p> </td>
													<td width="40" align="right" valign="bottom"><img class="corner iradius24" height="30" width="30" src="'.base_url().'up/avatar/'.$item['ds_solicitante_avatar'].'" alt="'.$item['ds_solicitante'].'" title="'.$item['ds_solicitante'].'"></td>
												</tr>								
											</table>
										</div>									
								     ';								
							}
							echo '</div>';
						?>
					</div>
				</div>	
				
			</div>
			<div class="col-md-3">
			
				<div class="panel panel-danger">
					<div class="panel-heading">
						<h3 class="panel-title">EM TESTE (<?php echo count($ar_teste);?>)</h3>
					</div>
					<div class="panel-body panel-body-context">
						<?php
							$ar_titulo = Array();
							$ar_dado = Array();
							$ar_image = Array();
							foreach($ar_teste_area as $item)
							{
								$ar_titulo[] = $item['area_solicitante'];
								$ar_dado[] = $item['quantidade'];	
							}
						
							$ar_image = $this->charts->pieChart(55,$ar_dado,$ar_titulo,'','');	
							echo '<center><img src="'.$ar_image['name'].'" border="0"></center>';						
						
							echo '<div class="panel-body-conteudo">';
							foreach($ar_teste as $item)
							{
								echo '
										<div class="bs-callout bs-callout-danger"> 
											<table border="0" width="100%"> 
												<tr height="35">
													<td align="left" valign="top"><h6>'.$item['assunto'].'</h6></td>
													<td width="40" align="right" valign="top"><img class="corner iradius24" height="30" width="30" src="'.base_url().'up/avatar/'.$item['ds_atendente_avatar'].'" alt="'.$item['ds_atendente'].'" title="'.$item['ds_atendente'].'"></td>
												</tr>
											</table>
											<table border="0" width="100%" style="color: gray;"> 
												<tr height="35">
													<td width="70" align="center" valign="bottom"><p><span class="glyphicon glyphicon glyphicon-tag" aria-hidden="true" style="font-size: 11px;"></span> '.$item['numero'].'</p></td>
													<td width="40" align="center" valign="bottom"><p><span class="glyphicon glyphicon glyphicon-pushpin" aria-hidden="true" style="font-size: 11px;"></span> '.$item['area_solicitante'].'</p> </td>
													<td width="50" align="center" valign="bottom"><p><span class="glyphicon glyphicon glyphicon-star" aria-hidden="true" style="font-size: 11px;"></span> '.$item['nr_prioridade'].'</p> </td>
													<td width="60" align="center" valign="bottom"><p><span class="glyphicon glyphicon glyphicon-calendar" aria-hidden="true" style="font-size: 11px;"></span> '.$item['dt_cadastro_min'].'</p> </td>
													<td width="40" align="right" valign="bottom"><img class="corner iradius24" height="30" width="30" src="'.base_url().'up/avatar/'.$item['ds_solicitante_avatar'].'" alt="'.$item['ds_solicitante'].'" title="'.$item['ds_solicitante'].'"></td>
													
												</tr>								
											</table>
											<table border="0" width="100%"> 
												<tr height="25">
													<td width="60" align="center" valign="bottom"><p style="font-weight: bold;"><span class="glyphicon glyphicon glyphicon-time" aria-hidden="true" style="font-size: 11px;"></span> '.$item['dt_limite_teste'].'</p> </td>
												</tr>
											</table>											
										</div>									
								     ';								
							}
							echo '</div>';
						?>
					</div>
				</div>	
				
			</div>	
			<div class="col-md-3">
			
				<div class="panel panel-warning">
					<div class="panel-heading">
						<h3 class="panel-title">AGUARDANDO USUÁRIO (<?php echo count($ar_usuario);?>)</h3>
					</div>
					<div class="panel-body panel-body-context">
						<?php
							$ar_titulo = Array();
							$ar_dado = Array();
							$ar_image = Array();
							foreach($ar_usuario_area as $item)
							{
								$ar_titulo[] = $item['area_solicitante'];
								$ar_dado[] = $item['quantidade'];	
							}
						
							$ar_image = $this->charts->pieChart(55,$ar_dado,$ar_titulo,'','');	
							echo '<center><img src="'.$ar_image['name'].'" border="0"></center>';							
						
							echo '<div class="panel-body-conteudo">';
							foreach($ar_usuario as $item)
							{
								echo '
										<div class="bs-callout bs-callout-warning"> 
											<table border="0" width="100%"> 
												<tr height="35">
													<td align="left" valign="top"><h6>'.$item['assunto'].'</h6></td>
													<td width="40" align="right" valign="top"><img class="corner iradius24" height="30" width="30" src="'.base_url().'up/avatar/'.$item['ds_atendente_avatar'].'" alt="'.$item['ds_atendente'].'" title="'.$item['ds_atendente'].'"></td>
												</tr>
											</table>
											<table border="0" width="100%" style="color: gray;"> 
												<tr height="35">
													<td width="70" align="center" valign="bottom"><p><span class="glyphicon glyphicon glyphicon-tag" aria-hidden="true" style="font-size: 11px;"></span> '.$item['numero'].'</p></td>
													<td width="40" align="center" valign="bottom"><p><span class="glyphicon glyphicon glyphicon-pushpin" aria-hidden="true" style="font-size: 11px;"></span> '.$item['area_solicitante'].'</p> </td>
													<td width="50" align="center" valign="bottom"><p><span class="glyphicon glyphicon glyphicon-star" aria-hidden="true" style="font-size: 11px;"></span> '.$item['nr_prioridade'].'</p> </td>
													<td width="60" align="center" valign="bottom"><p><span class="glyphicon glyphicon glyphicon-calendar" aria-hidden="true" style="font-size: 11px;"></span> '.$item['dt_cadastro_min'].'</p> </td>
													<td width="40" align="right" valign="bottom"><img class="corner iradius24" height="30" width="30" src="'.base_url().'up/avatar/'.$item['ds_solicitante_avatar'].'" alt="'.$item['ds_solicitante'].'" title="'.$item['ds_solicitante'].'"></td>
												</tr>								
											</table>
											<table border="0" width="100%"> 
												<tr height="25">
													<td width="60" align="center" valign="bottom"><p style="font-weight: bold;"><span class="glyphicon glyphicon glyphicon-time" aria-hidden="true" style="font-size: 11px;"></span> '.$item['dt_aguardando_usuario_limite'].'</p> </td>
												</tr>
											</table>											
										</div>									
								     ';									
							}
							echo '</div>';
						?>
					</div>
				</div>	
				
			</div>			

		</div>	
	

	
	
	</body>
</html>	
