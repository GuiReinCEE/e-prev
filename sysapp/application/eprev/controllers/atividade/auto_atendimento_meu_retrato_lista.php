<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	#$db = @pg_connect('host=srvpg.eletroceee.com.br port=5555 dbname=fundacaoweb user=gerente');
	
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
					   'MEU_RETRATO_LISTA'
					 )
		      ";
	@pg_query($db,$qr_sql);  	
	
	$extrato_item_modelo = '
					<div class="box-extrato-content box-extrato-statistic">
						<a href="{LINK}" target="_blank" title="Clique para abrir o Meu Retrato de {DATA}">
						<img src="img/meu_retrato_boneco_{SEXO}.png" border="0" style="float:left;">
						
						<h3 class="title-extrato text-extrato-success" style="margin-left: 10px; float:left;">{DATA}</h3>
						<small style="margin-left: 10px; float:left;">Visualizar</small>
						
						</a>
					</div>	
	                ';
	$extrato_item = "";
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
						width: 170px;
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
	
	#### BUSCA EDICOES MEU RETRATO ####
	           /*
	$sql = "
			SELECT e.cd_edicao,
			       e.nr_extrato,
				   TO_CHAR(e.dt_base_extrato,'DD/MM/YYYY') AS dt_extrato,
				   UPPER(p.sexo) AS sexo
			  FROM meu_retrato.edicao e
			  JOIN meu_retrato.edicao_participante ep
				ON ep.cd_edicao = e.cd_edicao
			  JOIN public.participantes p
				ON p.cd_empresa            = ep.cd_empresa
			   AND p.cd_registro_empregado = ep.cd_registro_empregado
			   AND p.seq_dependencia       = ep.seq_dependencia
			 WHERE e.dt_liberacao          IS NOT NULL
			   AND e.dt_exclusao           IS NULL
			   AND e.cd_plano              = p.cd_plano
			   AND e.cd_empresa            = p.cd_empresa
			   AND p.cd_empresa            = ".$_SESSION['EMP']."
			   AND p.cd_registro_empregado = ".$_SESSION['RE']."
			   AND p.seq_dependencia       = ".$_SESSION['SEQ']."
			 ORDER BY e.dt_base_extrato DESC								 
			 ";
	$ob_resul = pg_query($db,$sql);
	$nr_ano = "";
	$cont = 0;
	while($ar_reg = pg_fetch_array($ob_resul))
	*/
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_edicao");
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
			foreach ($_RETORNO['result'] as $key => $ar_reg) 
			{	
				$extrato_item_tmp = $extrato_item_modelo;
				$extrato_item_tmp = str_replace("{SEXO}",   $ar_reg["sexo"],$extrato_item_tmp);
				$extrato_item_tmp = str_replace("{DATA}",   $ar_reg["dt_extrato"],$extrato_item_tmp);
				$extrato_item_tmp = str_replace("{NUMERO}", $ar_reg["nr_extrato"],$extrato_item_tmp);
				$extrato_item_tmp = str_replace("{LINK}",   "auto_atendimento_meu_retrato.php?ED=".intval($ar_reg["cd_edicao"]),$extrato_item_tmp);
				
				$extrato_item.= $extrato_item_tmp;
			}

			$extrato = str_replace("{EXTRATO_ITEM}",$extrato_item,$extrato);
			$conteudo = str_replace('{tabela}', $extrato, $conteudo);
			$conteudo = str_replace('{msg}', '', $conteudo);
				
		} 
		else 
		{
			$conteudo = '
				<br><br><br>
				<center>
					<h1 style="font-family: Calibri, Arial; font-size: 15pt;">
						Opção não disponível.
					</h1>
				</center>
				<br><br><br>';		
		}
	}
	else
	{
		$conteudo = '
			<br><br><br>
			<center>
				<h1 style="font-family: Calibri, Arial; font-size: 15pt;">
					Opção não disponível.
				</h1>
			</center>
			<br><br><br>';	
	}

	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();
?>