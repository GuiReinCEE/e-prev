<?php
	#include_once('inc/conexao.php');
	$db = @pg_connect('host=srvpg.eletroceee.com.br port=5555 dbname=fundacaoweb user=gerente');
	#$db = @pg_connect('host=127.0.0.1 port=5432 dbname=fundacaoweb user=gerente');

	#print_r($_POST); EXIT;
	
	
	$ar_meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
	
	if ((preg_match('/10.63./',$_SERVER['REMOTE_ADDR'])) and ((($_REQUEST['EMP'] != "") and ($_REQUEST['RE'] != "") and ($_REQUEST['SEQ'] != "")) or ($_REQUEST['P'] != "")))
	{
		session_start();
		$_SESSION['SID']  = 0;
		
		if($_REQUEST['P'] != "")
		{
			$qr_sql = "
						SELECT cd_empresa,
							   cd_registro_empregado,
							   seq_dependencia
						  FROM public.participantes
						 WHERE funcoes.cripto_re(cd_empresa,cd_registro_empregado,seq_dependencia) = '".$_REQUEST['P']."'
					  ";
			$ob_resul = pg_query($db,$qr_sql);	
			$ar_reg = pg_fetch_array($ob_resul);
			
			$_REQUEST['EMP'] = $ar_reg['cd_empresa'];
			$_REQUEST['RE']  = $ar_reg['cd_registro_empregado'];
			$_REQUEST['SEQ'] = $ar_reg['seq_dependencia'];
		}
		
		$_SESSION['EMP']  = $_REQUEST['EMP'];
		$_SESSION['RE']   = $_REQUEST['RE'];
		$_SESSION['SEQ']  = $_REQUEST['SEQ'];
		$_SESSION['ID_AUTOATENDIMENTO'] = session_id();
	}
	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	
	#echo "<PRE>".print_r($_SESSION,true)."</PRE>"; exit;
	
	#### FAMILIA ####
	$_CD_PLANO = 9;
	if(!in_array(intval($_SESSION['EMP']), array(8,10,11,12,19,20,24,25,26,27,28,29,30,31)))
	{
		echo "
				<br><br><br>
				<center>
					<h1 style='font-family: Calibri, Arial; font-size: 15pt;'>
						Acesso não permitido
					</h1>
				</center>
				<br><br><br>
             ";
		exit;
	}
	
	#### LOG ####
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
					   'MEU_RETRATO_".($_REQUEST['pdf'] == "S" ? "PDF" : "")."'
					 )
		      ";
	@pg_query($db,$qr_sql);	
	
	
	if(intval($_REQUEST['ED']) == 0)
	{
		#### ULTIMA EDICAO ####
		$qr_sql = "
					SELECT cd_edicao
					  FROM meu_retrato.edicao
					 WHERE cd_empresa  = ".intval($_SESSION['EMP'])."
					   AND dt_exclusao IS NULL
					   AND dt_liberacao IS NOT NULL
					 ORDER BY dt_base_extrato DESC
					 LIMIT 1
				  ";
		$ob_resul = pg_query($db,$qr_sql);	
		$ar_reg = pg_fetch_array($ob_resul);	
		$_REQUEST['ED'] = intval($ar_reg['cd_edicao']);
	}	
	
	
	#### ARQUIVO PHP ####
	$qr_sql = "
				SELECT arquivo_php
				  FROM meu_retrato.edicao
				 WHERE cd_edicao = ".intval($_REQUEST['ED'])."
			  ";
	$ob_resul = pg_query($db,$qr_sql);	
	$ar_reg = pg_fetch_array($ob_resul);	
	if((trim($ar_reg['arquivo_php'] != "")) and (file_exists($ar_reg['arquivo_php'])))
	{
		include($ar_reg['arquivo_php']);
		exit;
	}
	else
	{
		echo "
				<br><br><br>
				<center>
					<h1 style='font-family: Calibri, Arial; font-size: 15pt;'>
						ERRO: arquivo não encontrado
					</h1>
				</center>
				<br><br><br>
             ";
		exit;	
	}
?>