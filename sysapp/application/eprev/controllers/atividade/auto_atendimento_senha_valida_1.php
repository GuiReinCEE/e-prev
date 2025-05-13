<?php
	include_once('inc/conexao.php');
	include_once('inc/nextval_sequence.php');

	#7528 http://10.63.255.150/eletroceee/auto_atendimento_senha_valida_1.php?p=f106bcfb5cf91e06a97fd25c5d9c4a87
	#7846 http://10.63.255.150/eletroceee/auto_atendimento_senha_valida_1.php?p=abedad356bcf12c2d5e0a798d8d8d33f
	#51 http://10.63.255.150/eletroceee/auto_atendimento_senha_valida_1.php?p=e2a005419213e9f27f68bf4479a29c26
	#51 http://10.63.255.150/eletroceee/auto_atendimento_senha_valida_1.php?c=539ddd17a71f01bc0b12eae8744f8fe8
	#vários RE http://10.63.255.150/eletroceee/auto_atendimento_senha_valida_1.php?c=2f07275491fcdbf6c186542aa5799488
	
	#print_r($_REQUEST); EXIT;
	
	if((isset($_REQUEST['cd_empresa'])) or (isset($_REQUEST['p'])) or (isset($_REQUEST['c'])))
	{
		$_REQUEST['cd_empresa'] = trim($_REQUEST['cd_empresa']) == "" ? 0 : $_REQUEST['cd_empresa'];
		$_REQUEST['cd_registro_empregado'] = trim($_REQUEST['cd_registro_empregado']) == "" ? 0 : $_REQUEST['cd_registro_empregado'];
		$_REQUEST['seq_dependencia'] = trim($_REQUEST['seq_dependencia']) == "" ? 0 : $_REQUEST['seq_dependencia'];
		
		#### RE CRIPTO ####
		if((isset($_REQUEST['p'])) and (trim($_REQUEST['p']) != ""))
		{
			$qr_sql = "
						SELECT p.cd_empresa,
						       p.cd_registro_empregado,
							   p.seq_dependencia
						  FROM public.participantes p
						 WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".trim($_REQUEST['p'])."'
			          ";
			$ob_resul = pg_query($db, $qr_sql);
			$ar_reg = pg_fetch_array($ob_resul);

			$_REQUEST['cd_empresa']             = intval($ar_reg['cd_empresa']);
			$_REQUEST['cd_registro_empregado']  = intval($ar_reg['cd_registro_empregado']);
			$_REQUEST['seq_dependencia']        = intval($ar_reg['seq_dependencia']);	
		}
		
		#### CPF CRIPTO ####
		if((isset($_REQUEST['c'])) and (trim($_REQUEST['c']) != ""))
		{		
			$qr_sql = "
						SELECT p.cd_empresa, 
							   p.cd_registro_empregado,
							   p.seq_dependencia,
							   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
							   funcoes.format_cpf(TO_CHAR(p.cpf_mf,'FM00000000000')) AS cpf
						  FROM public.participantes p
						  JOIN public.participantes_ccin pc 
							ON pc.cd_empresa            = p.cd_empresa 
						   AND pc.cd_registro_empregado = p.cd_registro_empregado 
						   AND pc.seq_dependencia       = p.seq_dependencia 
						  JOIN public.planos pl
							ON pl.cd_plano = p.cd_plano
						  JOIN public.patrocinadoras pe
							ON pe.cd_empresa = p.cd_empresa						
						 WHERE MD5(funcoes.format_cpf(TO_CHAR(p.cpf_mf,'FM00000000000'))) = '".trim($_REQUEST['c'])."'
						   AND COALESCE(p.cpf_mf,0) > 0
						   AND p.dt_obito           IS NULL
						 ORDER BY p.cd_empresa, p.nome
					  ";
			#echo "<PRE>".print_r($qr_sql,true)."</PRE>"; exit;		  
			$ob_resul = @pg_query($db, $qr_sql);
			
			if(@pg_num_rows($ob_resul) < 2)
			{
				$ar_reg = pg_fetch_array($ob_resul);
				$_REQUEST['cd_empresa']             = intval($ar_reg['cd_empresa']);
				$_REQUEST['cd_registro_empregado']  = intval($ar_reg['cd_registro_empregado']);
				$_REQUEST['seq_dependencia']        = intval($ar_reg['seq_dependencia']);				
			}
			else
			{
				$ar_reg = pg_fetch_array($ob_resul);
				echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=login.php?login_tipo=C&login_aa_cpf=".$ar_reg['cpf']."'>";
				exit;
			}
		}
		
		if ($_REQUEST['seq_dependencia'] == 0)
		{
			$qr_sql = " 
						SELECT p.nome,
						       p.cd_empresa,
						       p.cd_registro_empregado,
							   p.seq_dependencia, 
						       pc.opcao_contrato_valida,
							   (CASE WHEN p.motivo_devolucao_correio = 17 THEN 'S' ELSE 'N' END) AS fl_bloqueio,
							   tp.id_nao_participante
						  FROM public.participantes p
						  JOIN public.titulares t
							ON t.cd_empresa             = p.cd_empresa 
						   AND t.cd_registro_empregado  = p.cd_registro_empregado
						   AND t.seq_dependencia        = p.seq_dependencia 

                          JOIN public.titulares_planos tp
							ON tp.cd_empresa             = p.cd_empresa 
						   AND tp.cd_registro_empregado  = p.cd_registro_empregado
						   AND tp.seq_dependencia        = p.seq_dependencia	
                           AND tp.dt_ingresso_plano      = (SELECT MAX(tp1.dt_ingresso_plano)
	 			                                              FROM public.titulares_planos tp1
				                                             WHERE tp1.cd_empresa            = p.cd_empresa 
				                                               AND tp1.cd_registro_empregado = p.cd_registro_empregado
				                                               AND tp1.seq_dependencia       = p.seq_dependencia)
					   
						  LEFT JOIN public.participantes_ccin pc
							ON pc.cd_empresa            = p.cd_empresa 
						   AND pc.cd_registro_empregado = p.cd_registro_empregado
						   AND pc.seq_dependencia       = p.seq_dependencia 
						 WHERE p.cd_empresa             = ".intval($_REQUEST['cd_empresa'])."
						   AND p.cd_registro_empregado  = ".intval($_REQUEST['cd_registro_empregado'])."
						   AND p.seq_dependencia        = ".intval($_REQUEST['seq_dependencia'])."
						   AND p.dt_obito               IS NULL
						   AND (
									(t.dt_desligamento_eletro IS NULL AND p.cd_plano > 0)
									OR
									COALESCE(tp.id_nao_participante,'N') = 'S'
							   )
						   
			           ";
		}
		else
		{
			#### VERIFICA TITULAR SEQUENCIA ZERO ####
			$qr_sql = "
						SELECT COUNT(*) AS fl_titular
						  FROM public.participantes p
						  JOIN public.titulares t
							ON t.cd_empresa             = p.cd_empresa 
						   AND t.cd_registro_empregado  = p.cd_registro_empregado
						   AND t.seq_dependencia        = p.seq_dependencia 	
						 WHERE p.cd_empresa             = ".intval($_REQUEST['cd_empresa'])."
						   AND p.cd_registro_empregado  = ".intval($_REQUEST['cd_registro_empregado'])."
						   AND p.seq_dependencia        = 0						   
						   AND p.dt_obito               IS NULL
					  ";
			$ob_resul = pg_query($db, $qr_sql);
			$ar_reg = pg_fetch_array($ob_resul);
			if ($ar_reg['fl_titular'] == 0) 
			{ 
				$qr_sql = " 
							SELECT p.nome,
							       p.cd_empresa,
							       p.cd_registro_empregado,
								   p.seq_dependencia, 
							       pc.opcao_contrato_valida,
								   (CASE WHEN p.motivo_devolucao_correio = 17 THEN 'S' ELSE 'N' END) AS fl_bloqueio
							  FROM public.participantes p
							  LEFT JOIN public.participantes_ccin pc
								ON pc.cd_empresa            = p.cd_empresa 
							   AND pc.cd_registro_empregado = p.cd_registro_empregado
							   AND pc.seq_dependencia       = p.seq_dependencia 
							 WHERE p.cd_empresa             = ".intval($_REQUEST['cd_empresa'])."
							   AND p.cd_registro_empregado  = ".intval($_REQUEST['cd_registro_empregado'])."
							   AND p.seq_dependencia        = ".intval($_REQUEST['seq_dependencia'])."
							   AND p.dt_obito               IS NULL
				           ";				
			}			
			else
			{
				echo "	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
						<SCRIPT>
							alert('Sequência inválida.\\n\\nPor favor verifique os dados informados.');
							document.location.href = 'auto_atendimento_senha.php';
						</SCRIPT>
					 ";	
				exit;
			}		   
		}
		
		##echo "<PRE>$qr_sql</PRE>"; exit;
		
		$ob_resul = pg_query($db, $qr_sql);	
		if(pg_num_rows($ob_resul) > 0)
		{
			$ar_reg = pg_fetch_array($ob_resul);
			
			if($ar_reg['fl_bloqueio'] == "S") ### BLOQUEADO PARA NÃO PERMITIR SOLICITA SENHA PELO SITE
			{
				echo "	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
						<SCRIPT>
							alert('Você não pode solicitar senha pelo site.\\n\\nPara mais informações entre em contato através 0800512596, de segunda a sexta, das 8 horas às 17 horas.\\n\\n');
							document.location.href = 'index.php';
						</SCRIPT>
					 ";	
				exit;				
			}
			else
			{
				$_SESSION = Array();
				@session_destroy("AUTO_ATENDIMENTO_SENHA");
				session_start("AUTO_ATENDIMENTO_SENHA");
				
				$_SESSION['EMP'] = $ar_reg['cd_empresa'];
				$_SESSION['RE']  = $ar_reg['cd_registro_empregado'];
				$_SESSION['SEQ'] = $ar_reg['seq_dependencia'];
				$_SESSION['OCV'] = ($ar_reg['opcao_contrato_valida'] == 0 ?  1 :  $ar_reg['opcao_contrato_valida']); # 1 = FRACA | 2 = FORTE | 3 = NAO PARTICIPANTE
				$_SESSION['FL_ELEICOES_2014'] = FALSE;
				
				#### GAMBIARRA PARA ELEICOES 2014 ####
				if (($_REQUEST["e2014"] == "S") and ($_SESSION['OCV'] != 2))
				{
					$_SESSION['FL_ELEICOES_2014'] = TRUE;
				}
				#print_r($_SESSION); exit;
				
				#### PEGA NEXTVAL DA SEQUENCE DO CAMPO ####
				$_SESSION['SID'] = getNextval("public", "log_acessos", "sid", $db);
				
				$qr_sql = "
							INSERT INTO public.log_acessos 
								 (
								   sid,
								   sistema, 
								   data_hora, 
								   cd_empresa, 
								   cd_registro_empregado, 
								   seq_dependencia
								 ) 
							VALUES
								 (
								   ".intval($_SESSION['SID']).",
								   'AUTO_ATENDIMENTO_SENHA', 
								   CURRENT_TIMESTAMP, 
								   ".intval($_SESSION['EMP']).", 
								   ".intval($_SESSION['RE']).", 
								   ".intval($_SESSION['SEQ'])."
								 );
							
							INSERT INTO public.log_acessos_usuario 
								 (
								   sid,
								   hora,
								   pagina
								 )
							VALUES
								 (
								   ".intval($_SESSION['SID']).", 
								   CURRENT_TIMESTAMP,
								   'AUTO_ATENDIMENTO_SENHA_PASSO_1'
								 );
						  ";

				@pg_query($db,$qr_sql);
				echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=auto_atendimento_senha_passo_2.php'>";
			}
		}
		else
		{
			echo "	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
					<SCRIPT>
						alert('Participante não encontrado.\\n\\nPor favor verifique os dados informados.');
						document.location.href = 'auto_atendimento_senha.php';
					</SCRIPT>
				 ";	
			exit;
		}
	}
	else
	{
		echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=auto_atendimento_senha.php'>";
	}
?>