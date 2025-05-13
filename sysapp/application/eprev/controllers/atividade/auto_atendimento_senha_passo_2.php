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
					   'AUTO_ATENDIMENTO_SENHA_PASSO_2'
					 );
			  ";
	@pg_query($db,$qr_sql);
	
	$tpl = new TemplatePower('tpl/tpl_auto_atendimento_senha_passo_2.html');
	$tpl->prepare();
	
	
	$qr_sql = "
				SELECT CASE WHEN cd_empresa IN(0,1,2,3,6,9) --POSSUI EMPRESTIMO
				            THEN 'P'
				            ELSE 'I'
					   END tipo_cliente
				  FROM public.patrocinadoras p
				 WHERE p.cd_empresa = ".intval($_SESSION['EMP'])."
			  ";
	$ob_resul = pg_query($db, $qr_sql);
	$ar_reg = pg_fetch_array($ob_resul);	
	
	#### GAMBIARRA PARA ELEICOES 2014 ####
	if (($_SESSION['FL_ELEICOES_2014']) and ($_SESSION['OCV'] != 2))
	{
		$ar_reg['tipo_cliente'] = "I";
	}
	#print_r($_SESSION); print_r($ar_reg);exit;
	
	#### INSTITUIDOR SOMENTE SENHA FRACA)
	if($ar_reg['tipo_cliente'] == "I")
	{
		#### VAI DIRETO PARA O PASSO 3 ####
		#$tpl->newBlock('instituidor');

		if($_SESSION['OCV'] == 2)
		{
			#### POSSUI SENHA FORTE ####
			echo "	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
					<SCRIPT>
						alert('Você já possui Senha para Consultas e Concessão de Empréstimos.\\n\\nEntre em contato pelo 0800 51 2596, de segunda a sexta, das 8 horas às 17 horas.');
						document.location.href = 'index.php';
					</SCRIPT>
				 ";	
			exit;			
		}
		else
		{
			echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=auto_atendimento_senha_valida_2.php?fl_tipo=".$_SESSION['OCV']."'>";
			exit;
		}
	}
	else
	{
		if($_SESSION['OCV'] == 2)
		{
			#### POSSUI SENHA FORTE ####
			echo "	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
					<SCRIPT>
						alert('Você já possui Senha para Consultas e Concessão de Empréstimos.\\n\\nEntre em contato pelo 0800 51 2596, de segunda a sexta, das 8 horas às 17 horas.');
						document.location.href = 'index.php';
					</SCRIPT>
				 ";	
			exit;			
		}
		elseif($_SESSION['OCV'] == 3)
		{
			#### POSSUI SENHA NAO PARTICIPANTE ####
			echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=auto_atendimento_senha_valida_2.php?fl_tipo=".$_SESSION['OCV']."'>";
			exit;			
		}		
		else
		{
			$tpl->newBlock('patrocinadora');
			
			#$tpl->newBlock('patrocinadora_emp_pos');
		}
	}
	
	
	
	$tpl->printToScreen();

	
?>
