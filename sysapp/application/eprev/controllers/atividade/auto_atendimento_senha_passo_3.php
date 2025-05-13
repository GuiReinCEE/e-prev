<?php
	include_once('inc/sessao_senha.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	#ECHO "<pre>"; PRINT_R($_SESSION); EXIT;

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
					   'AUTO_ATENDIMENTO_SENHA_PASSO_3'
					 );
			  ";
	@pg_query($db,$qr_sql);
	
	$qr_sql = "
				SELECT COUNT(*) AS fl_consulta
				  FROM public.participantes_ccin p
				 WHERE p.cd_empresa            = ".intval($_SESSION['EMP'])."
				   AND p.cd_registro_empregado = ".intval($_SESSION['RE'])."
				   AND p.seq_dependencia       = ".intval($_SESSION['SEQ'])."
			       AND p.codigo_355            = 'S' 
				   AND p.codigo_356            = 'N' 
				   AND p.opcao_contrato_valida IN ('1')
			  ";
	$ob_resul = pg_query($db, $qr_sql);
	$ar_reg = pg_fetch_array($ob_resul);	
	
	if($ar_reg['fl_consulta'] > 0)
	{
		echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=auto_atendimento_senha_passo_4.php'>";
		exit;		
	}
	
	
	$tpl = new TemplatePower('tpl/tpl_auto_atendimento_senha_passo_3.html');
	$tpl->prepare();
	$tpl->printToScreen();
?>
