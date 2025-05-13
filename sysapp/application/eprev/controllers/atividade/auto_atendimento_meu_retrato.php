<?php
	#include_once('inc/conexao.php');
	$db = @pg_connect('host=srvpg.eletroceee.com.br port=5555 dbname=fundacaoweb user=gerente');
	#$db = @pg_connect('host=127.0.0.1 port=5432 dbname=fundacaoweb user=gerente');
	#https://www.fundacaoceee.com.br/auto_atendimento_meu_retrato.php?P=ca860bbff87535e130da473cfe83f020&ED=220&ID_APP=6aa6507bd5001298e2c9d987d2311bd7
	
	$ar_meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");

	$ds_url_app = '';

	if (
		((preg_match('/10.63./',$_SERVER['REMOTE_ADDR'])) OR (isset($_REQUEST['ID_APP'])))
		AND 
		((($_REQUEST['EMP'] != "") AND ($_REQUEST['RE'] != "") AND ($_REQUEST['SEQ'] != "")) OR ($_REQUEST['P'] != ""))
		)
	{
		session_start();
		$_SESSION['SID']  = 0;

		if($_REQUEST['ID_APP'] != "")
		{
			$qr_sql = "
	            SELECT ds_app
	              FROM autoatendimento.app 
	             WHERE dt_exclusao IS NULL 
	               AND id_app = '".trim($_REQUEST['ID_APP'])."';";

	        $ob_resul = pg_query($db,$qr_sql);	
			$ar_reg = pg_fetch_array($ob_resul);

			if(count($ar_reg) == 0)
			{
				echo utf8_decode('<h1>ID APP INVÁLIDO</h1>');
				exit;
			}

			if(trim($ar_reg['ds_app']) == 'APP')
			{
				$ds_url_app = '&pdf=S';
			}
		}
		
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
		$_SESSION['MR_CONSULTA'] = 1;
		$_SESSION['ID_AUTOATENDIMENTO'] = session_id();
	}

	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	
	#echo "<PRE>".print_r($_SESSION,true)."</PRE>"; exit;
	
	$_ARQ_TPL = "";
	if(intval($_REQUEST['ED']) == 0)
	{
		#### ULTIMA EDICAO ####
		$qr_sql = "
					SELECT e.cd_edicao
					  FROM meu_retrato.edicao e
					  JOIN meu_retrato.edicao_participante ep
					    ON ep.cd_edicao = e.cd_edicao
					 WHERE ep.cd_empresa            = ".intval($_SESSION['EMP'])."
					   AND ep.cd_registro_empregado = ".intval($_SESSION['RE'])."
					   AND ep.seq_dependencia       = ".intval($_SESSION['SEQ'])."
					   AND e.dt_exclusao IS NULL
					   AND e.dt_liberacao IS NOT NULL
					 ORDER BY e.dt_base_extrato DESC
					 LIMIT 1
				  ";
		$ob_resul = pg_query($db,$qr_sql);	
		$ar_reg = pg_fetch_array($ob_resul);	
		$_REQUEST['ED'] = intval($ar_reg['cd_edicao']);
	}	
	
	#### CEEE - PLANO UNICO ####
	if(in_array(intval($_SESSION['EMP']), array(0)))
	{
		$qr_sql = "
					SELECT e.cd_plano
					  FROM meu_retrato.edicao e
					 WHERE e.cd_edicao = ".intval($_REQUEST['ED'])."
				  ";
		$ob_resul = pg_query($db,$qr_sql);	
		$ar_reg = pg_fetch_array($ob_resul);		
	
		if(intval($ar_reg["cd_plano"]) == 1) //PLANO UNICO
		{
			header("location: auto_atendimento_meu_retrato_unico_ceee.php?ED=".intval($_REQUEST['ED']).$ds_url_app);
			exit;
		}
	}		
	
	#### RGE ####
	if(in_array(intval($_SESSION['EMP']), array(1)))
	{
		header("location: auto_atendimento_meu_retrato_unico_rge.php?ED=".intval($_REQUEST['ED']).$ds_url_app);
		exit;
	}		
	
	#### AES SUL ####
	if(in_array(intval($_SESSION['EMP']), array(2)))
	{
		header("location: auto_atendimento_meu_retrato_unico_aes_sul.php?ED=".intval($_REQUEST['ED']).$ds_url_app);
		exit;
	}	

	#### CGTEE ####
	if(in_array(intval($_SESSION['EMP']), array(3)))
	{
		header("location: auto_atendimento_meu_retrato_unico_cgtee.php?ED=".intval($_REQUEST['ED']).$ds_url_app);
		exit;
	}	
	
	#### CRMPREV ####
	if(in_array(intval($_SESSION['EMP']), array(6)))
	{
		header("location: auto_atendimento_meu_retrato_crmprev.php?ED=".intval($_REQUEST['ED']).$ds_url_app);
		exit;
	}		
	
	#### SENGE ####
	if(in_array(intval($_SESSION['EMP']), array(7)))
	{
		header("location: auto_atendimento_meu_retrato_senge.php?ED=".intval($_REQUEST['ED']).$ds_url_app);
		exit;
	}		
	
	#### SINPRORS ####
	if(in_array(intval($_SESSION['EMP']), array(8,10,11,12)))
	{
		header("location: auto_atendimento_meu_retrato_sinprors.php?ED=".intval($_REQUEST['ED']).$ds_url_app);
		exit;
	}
	
	#### FAMILIA ####
	if(in_array(intval($_SESSION['EMP']), array(19,20,24,25,26,27,28,29,30,31)))
	{
		header("location: auto_atendimento_meu_retrato_familia.php?ED=".intval($_REQUEST['ED']).$ds_url_app);
		exit;
	}	
	
	#### CEEEPREV ####
	if(in_array(intval($_SESSION['EMP']), array(0,9)))
	{
		header("location: auto_atendimento_meu_retrato_ceeeprev.php?ED=".intval($_REQUEST['ED']).$ds_url_app);
		exit;
	}	

	#### FAMILIA CORP ####
	if(in_array(intval($_SESSION['EMP']), array(21)))
	{
		header("location: auto_atendimento_meu_retrato_familia_corporativo.php?ED=".intval($_REQUEST['ED']).$ds_url_app);
		exit;
	}	

	#### CERANPREV #### 
	if(in_array(intval($_SESSION['EMP']), array(22)))
	{
		header("location: auto_atendimento_meu_retrato_ceranprev.php?ED=".intval($_REQUEST['ED']).$ds_url_app);
		exit;
	}	

	#### FOZDOCHAPECOPREV ####
	if(in_array(intval($_SESSION['EMP']), array(23)))
	{
		header("location: auto_atendimento_meu_retrato_fozdochapecoprev.php?ED=".intval($_REQUEST['ED']).$ds_url_app);
		exit;
	}	
?>