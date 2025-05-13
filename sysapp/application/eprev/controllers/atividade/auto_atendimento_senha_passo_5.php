<?php
	include_once('inc/sessao_senha.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

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
					   'AUTO_ATENDIMENTO_SENHA_PASSO_5'
					 );
			  ";
	@pg_query($db,$qr_sql);	

	if($_POST['nr_codigo_seguranca'] > 0)
	{
		$_POST['nr_cpf'] = trim(str_replace('_','',str_replace('-','',str_replace('.','',$_POST['nr_cpf']))));
		$qr_sql = "
					SELECT COUNT(*) AS fl_valida
					  FROM public.participantes p
					  JOIN public.patrocinadoras pp
						ON pp.cd_empresa = p.cd_empresa 
					 WHERE p.cd_empresa                  = ".$_SESSION['EMP']."
					   AND p.cd_registro_empregado       = ".$_SESSION['RE']."
					   AND p.seq_dependencia             = ".$_SESSION['SEQ']."
					   AND CAST(p.dt_nascimento AS DATE) = TO_DATE('".$_POST['dt_nascimento']."','DD/MM/YYYY')
					   AND p.cpf_mf                      = ".$_POST['nr_cpf']."
					   AND ".$_SESSION['NR_SEG']."       = ".$_POST['nr_codigo_seguranca']."
				  ";
		#echo "<PRE>".$qr_sql;			   
		$ob_resul = pg_query($db, $qr_sql);	
		$ar_reg = pg_fetch_array($ob_resul);
		
		if($ar_reg['fl_valida'] > 0)
		{
			#### TESTA SE SENHA FRACA OU NAO PARTICIPANTE ####
			if (($_SESSION['TPS'] == 1) OR ($_SESSION['TPS'] == 3))
			{
				#### FRACA ####
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
								   '".($_SESSION['TPS'] == 3 ? "AUTO_ATENDIMENTO_SENHA_NAO_PARTICIPANTE" : "AUTO_ATENDIMENTO_SENHA_CONSULTA")."'
								 );
						  ";
				@pg_query($db,$qr_sql);			
				
				#### CODIGO UNICO DA SOLICITACAO ####
				$cd_solicitacao = md5($_SESSION['EMP'].$_SESSION['RE'].$_SESSION['SEQ'].(uniqid(rand(), true)));
				
				$quebra = chr(10);
				$mensagem =	"Prezado(a) {NOME}".$quebra.$quebra.
							"Conforme sua solicitação, enviamos as informações para liberar o acesso aos serviços de consulta no autoatendimento disponível no site da Fundação Família Previdência e pelo telefone 0800512596.".$quebra.$quebra.
							"Confira seus dados pessoais:".$quebra.
							"-----------------------------------------------------------------------".$quebra.
							"Emp/RE/Seq (sua identificaçao junto à Fundação Família Previdência): {RE}" .$quebra.
							"Nome: {NOME}".$quebra.
							"CPF: {CPF}".$quebra.
							"-----------------------------------------------------------------------".$quebra.$quebra.
							"**************************************************************************************".$quebra.
							"Clique no link para fazer sua nova senha: {LINK_SENHA}".$quebra.
							"**************************************************************************************".$quebra.$quebra.
							"Para utilizá-la acesse: http://www.fundacaofamiliaprevidencia.com.br.".$quebra.$quebra.
							"Lembre-se, a senha para utilização dos serviços de autoatendimento é pessoal e intransferível.".$quebra.$quebra.
							"Mantenha resguardo, ela passa a ser sua assinatura com os compromissos decorrentes dos serviços solicitados.".$quebra.$quebra.
							"Em caso de qualquer dúvida, fique à vontade para responder esse email ou nos contatar no 0800512596 ou http://www.fundacaofamiliaprevidencia.com.br.".$quebra.$quebra.
							"Atenciosamente,".$quebra.$quebra.
							"Fundação Família Previdência".$quebra.$quebra;					

				$qr_sql = "
							SELECT p.nome,
								   p.email,
								   p.email_profissional,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   funcoes.format_cpf(p.cpf_mf::bigint) AS cpf,
								   funcoes.gera_link('http://www.fundacaofamiliaprevidencia.com.br/auto_atendimento_senha_passo_6.php?c=".$cd_solicitacao."',p.cd_empresa::integer, p.cd_registro_empregado::integer, p.seq_dependencia::integer) AS link_senha
							  FROM public.participantes p
							 WHERE p.cd_empresa             = ".$_SESSION['EMP']."
							   AND p.cd_registro_empregado  = ".$_SESSION['RE']."
							   AND p.seq_dependencia        = ".$_SESSION['SEQ']."
						  ";
				$ob_resul = pg_query($db, $qr_sql);	
				$ar_reg = pg_fetch_array($ob_resul);	


				$mensagem = str_replace("{NOME}",       str_replace("'", "", $ar_reg['nome']), $mensagem);
				$mensagem = str_replace("{CPF}",        $ar_reg['cpf'], $mensagem);
				$mensagem = str_replace("{RE}",         $ar_reg['cd_empresa']."/".$ar_reg['cd_registro_empregado']."/".$ar_reg['seq_dependencia'], $mensagem);
				$mensagem = str_replace("{LINK_SENHA}", $ar_reg['link_senha'], $mensagem);				
				
				$qr_sql = "
							INSERT INTO projetos.auto_atendimento_senha
							     (
									cd_empresa, 
									cd_registro_empregado, 
                                    seq_dependencia, 
									cd_codigo,
									opcao_contrato_solicitada
								 )
                            VALUES 
							     (
									".intval($ar_reg['cd_empresa']).",
									".intval($ar_reg['cd_registro_empregado']).",
									".intval($ar_reg['seq_dependencia']).",
									'".$cd_solicitacao."',
									'".$_SESSION['TPS']."'
								 );
				
							INSERT INTO projetos.envia_emails 
								 ( 
								   dt_envio, 
								   de, 
								   para, 
								   cc,	
								   cco, 
								   assunto, 
								   texto,
								   cd_evento,
								   tp_email
								 ) 
							VALUES 
								 ( 
								   CURRENT_TIMESTAMP, 
								   'FFP', 
								   '".str_replace("'","",$ar_reg['email'])."', 
								   '".str_replace("'","",$ar_reg['email_profissional'])."', 
								   '', 
								   'Senha de Autoatendimento Fundação Família Previdência - Confirmação', 
								   '".$mensagem."',
								   41,
								   'A'
								 );			
						  ";
				@pg_query($db,$qr_sql);				
				#echo "<pre>$qr_sql</pre>"; exit;

				$tpl = new TemplatePower('tpl/tpl_auto_atendimento_senha_consulta.html');
				$tpl->prepare();
				$tpl->assign('email_confirma', $_POST['email_confirma']);
				$tpl->printToScreen();					
				exit;
			}
			else
			{
				#### FORTE ####
				$tpl = new TemplatePower('tpl/tpl_auto_atendimento_senha_completa.html');
				
				$qr_sql = "
							SELECT cd_atendimento 
							  FROM oracle.atendimento_novo()
				          ";
				$ob_resul = pg_query($db, $qr_sql);	
				$ar_reg = pg_fetch_array($ob_resul);
				$cd_atendimento_new = intval($ar_reg["cd_atendimento"]);
				
				$qr_sql = "
							SELECT p.nome,
								   p.email,
								   p.email_profissional,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   funcoes.format_cpf(p.cpf_mf::bigint) AS cpf
							  FROM public.participantes p
							 WHERE p.cd_empresa             = ".$_SESSION['EMP']."
							   AND p.cd_registro_empregado  = ".$_SESSION['RE']."
							   AND p.seq_dependencia        = ".$_SESSION['SEQ']."
						  ";
				$ob_resul = pg_query($db, $qr_sql);	
				$ar_reg = pg_fetch_array($ob_resul);				
				
				$quebra = chr(10);
				$mensagem =	"Foi solicitado o envio do Contrato de Prestação de Serviços - CALL CENTER e a senha.".$quebra.$quebra.
							"-----------------------------------------------------------------------".$quebra.
							"Nome: ".str_replace("'", "", $ar_reg['nome']).$quebra.
							"Emp/RE/Seq: ".intval($ar_reg['cd_empresa'])."/".intval($ar_reg['cd_registro_empregado'])."/".intval($ar_reg['seq_dependencia'])."," .$quebra.
							"CPF: ".$ar_reg['cpf'].$quebra.
							"https://www.e-prev.com.br/cieprev/index.php/ecrm/encaminhamento".$quebra.$quebra.$quebra;					
				
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
								   'AUTO_ATENDIMENTO_SENHA_COMPLETA'
								 );
								 
							INSERT INTO projetos.envia_emails 
								 ( 
								   dt_envio, 
								   de, 
								   para, 
								   cc,	
								   cco, 
								   assunto, 
								   texto,
								   cd_evento,
								   tp_email
								 ) 
							VALUES 
								 ( 
								   CURRENT_TIMESTAMP, 
								   'Fundação CEEE', 
								   'gpatendimento@eletroceee.com.br', 
								   '', 
								   '', 
								   'ENVIAR: Contrato de Prestação de Serviços - CALL CENTER e a senha', 
								   '".$mensagem."',
								   187,
								   'A'
								 );	
								 
							INSERT INTO projetos.atendimento
							     (
									cd_atendimento,
									cd_empresa, 
									cd_registro_empregado, 
									seq_dependencia, 
									nome,
									dt_hora_inicio_atendimento, 
									dt_hora_fim_atendimento, 
									origem_atendimento, 
									indic_ativo,  
									opt_atendimento, 
									id_atendente
								 )
							VALUES	 
							     (
									".intval($cd_atendimento_new).",
									".intval($ar_reg['cd_empresa']).",
									".intval($ar_reg['cd_registro_empregado']).",
									".intval($ar_reg['seq_dependencia']).",
									'".str_replace("'", "", $ar_reg['nome'])."',
									CURRENT_TIMESTAMP, 
									CURRENT_TIMESTAMP, 
									'P', 
									'C',  
									'M', 
									99999
								 );	
									
							INSERT INTO projetos.atendimento_encaminhamento
							     (
									cd_atendimento, 
									cd_empresa, 
									cd_registro_empregado, 
									seq_dependencia, 									
									dt_encaminhamento, 
									id_atendente, 
									cd_atendente, 
									cd_atendimento_encaminhamento_tipo,
									texto_encaminhamento
								 )
							VALUES
							     (
									".intval($cd_atendimento_new).",
									".intval($ar_reg['cd_empresa']).",
									".intval($ar_reg['cd_registro_empregado']).",
									".intval($ar_reg['seq_dependencia']).",									
									CURRENT_TIMESTAMP, 
									99999, 
									funcoes.get_usuario(99999), 
									2,
									'ENVIAR: Contrato de Prestação de Serviços - CALL CENTER e a senha'
								 );								 
						  ";
				
				#echo "<PRE>$qr_sql</PRE>"; exit;
				
				@pg_query($db,$qr_sql);		

				$tpl->prepare();
				$tpl->printToScreen();	
				exit;				
			}
		}
		else
		{
			echo "	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
					<SCRIPT>
						alert('Dados não conferem.\\n\\nPor favor verifique os dados informados.');
						document.location.href = 'auto_atendimento_senha_passo_4.php';
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
