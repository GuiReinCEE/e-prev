<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();

	include_once('auto_atendimento_monta_sessao.php');
	$qr_sql = "
				INSERT INTO public.log_acessos_usuario 
					 (
					   sid,
					   hora,
					   pagina
					 ) 
				VALUES
					 (
					   ".$_SESSION['SID'].",
					   CURRENT_TIMESTAMP,
					   'EXTRATOS'
					 )
		      ";
	@pg_query($db,$qr_sql);  	
	
	$extrato_item_modelo = '
		<div class="box-extrato-content box-extrato-statistic">
			<a href="{LINK}" target="_blank" title="Clique para abrir o Extrato {NUMERO} de {DATA}">
			<h3 class="title-extrato text-extrato-success">{DATA}</h3>
			<small>Número {NUMERO}</small>
			
			</a>
		</div>';

	$extrato_item = '';

	$extrato = '
				<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
				<link href="https://fonts.googleapis.com/css?family=Oxygen+Mono" rel="stylesheet" type="text/css">
				<style>
					.box-extrato-content a {
						font-weight: normal;
						text-decoration:none;
					}
				
					.box-extrato-content {
						float:left;
						margin-top: 10px;
						margin-left: 10px;
						width: 120px;
						background: none repeat scroll 0 0 white;
						border: 1px solid #DDDDDD;
						box-shadow: 0 1px 3px rgba(0, 0, 0, 0.055);
						display: block;
						padding: 10px;
						
						color: #333333;
						font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
						font-size: 14px;
						line-height: 20px;						
					}
					
					.box-extrato-content small{
						color: #333333;
						font-family: "Oxygen Mono", "Helvetica Neue",Helvetica,Arial,sans-serif;
						font-size: 12px;
						line-height: 20px;
					}					
					
					.box-extrato-statistic {
						background-color: white;
						padding: 5px 10px;
						position: relative;
					}	
					
					.box-extrato-statistic .title-extrato {
						margin: 0px;
						line-height: 28px;
					}
					
					.text-extrato-success {
						font-family: Montserrat;
						font-weight: 400;					
						font-size: 18px;
						color: #2e97e0 !important;
					}					
				</style>
				
				<div style="width: 90%">
					{EXTRATO_ITEM}
				</div>
	
				<div style="clear: both;"></div>	
	           ';
	
	$acrobat = '
				<BR>
				<div style="width: 100%; font-family: verdana; font-size: 9pt;">
					Para ver os extratos é necessário o <a href="http://get.adobe.com/reader/" target="_blank" style="font-size: 9pt;">Adobe Acrobat Reader</a>, clique no icone para fazer download.
					<BR>
					<a href="http://get.adobe.com/reader/" target="_blank" alt="Download Adobe Acrobat Reader" title="Download Adobe Acrobat Reader"><img src="img/get_adobe_reader.png" border="0"></a>
				</div>	
	           ';
	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_extrato");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$_RETORNO = curl_exec($ch);
	curl_close ($ch);

	$FL_RETORNO = TRUE;
	$_RETORNO = json_decode($_RETORNO, TRUE);
	if (!(json_last_error() === JSON_ERROR_NONE))
	{
		switch (json_last_error()) 
		{
			case JSON_ERROR_NONE:
				$FL_RETORNO = TRUE;
			break;
				default:
				$FL_RETORNO = FALSE;
			break;
		}
	}

	if($FL_RETORNO)
	{
		if(intval($_RETORNO['error']['status']) == 0)
		{
			foreach ($_RETORNO['result']['extrato'] as $key => $item) 
			{
				$cont++;

				$extrato_item_tmp = $extrato_item_modelo;
				$extrato_item_tmp = str_replace("{DATA}",$item['dt_extrato'],$extrato_item_tmp);
				$extrato_item_tmp = str_replace("{NUMERO}",$item['nr_extrato'],$extrato_item_tmp);
				//$extrato_item_tmp = str_replace("{LINK}","extratos_pdf.php?cd_plano=$CD_PLANO&nr_extrato=$NR_EXTRATO&nr_indexador=$NR_INDEXADOR&tp_patrocinadora=$TP_PATROCINADORA&dt_base_extrato=$DT_BASE_EXTRATO",$extrato_item_tmp);
				$extrato_item_tmp = str_replace("{LINK}","extratos_pdf.php?tp_extrato=".$item['tp_extrato']."&nr_extrato=".$item['nr_extrato']."&dt_inicio=".$item['dt_inicio']."&data_base=".$item['data_base']."&cd_plano=".$item['cd_plano'],$extrato_item_tmp);

				$extrato_item.= $extrato_item_tmp;
			}
		}
		else
		{
			#echo 'ERRO - [2]<br/>';
			#echo implode(' ', $_RETORNO['error']['mensagem']);
		}
	}
	else 
	{
		#echo 'ERRO [1]';
	}

	$extrato = str_replace("{EXTRATO_ITEM}",$extrato_item,$extrato);
	
	if ($cont == 0) 
	{	
		$conteudo = '
					<br><br><br>
					<center>
						<h1 style="font-family: Calibri, Arial; font-size: 15pt;">
							Não há extrato disponível.
						</h1>
					</center>
					<br><br><br>
					';			
	} 
	else 
	{
		$conteudo = str_replace('{tabela}', $extrato. $acrobat, $conteudo);
		$conteudo = str_replace('{msg}', '', $conteudo);
	}

	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();
?>