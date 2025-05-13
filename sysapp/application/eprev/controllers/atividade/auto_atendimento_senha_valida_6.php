<?php
	include_once('inc/sessao_senha.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	$quebra = chr(10);
	$mensagem =	"Prezado(a) {NOME}".$quebra.$quebra.
				"Conforme sua solicitação, enviamos a senha para acesso aos serviços de consulta no autoatendimento disponível no site da Fundação Família Previdência e pelo telefone 0800512596.".$quebra.$quebra.
				"Confira seus dados pessoais:".$quebra.
				"-----------------------------------------------------------------------".$quebra.
				"Emp/RE/Seq (sua identificaçao junto à Fundação Família Previdência): {RE}" .$quebra.
				"Nome: {NOME}".$quebra.
				"CPF: {CPF}".$quebra.
				"-----------------------------------------------------------------------".$quebra.$quebra.
				"***********************************************************************".$quebra.
				"A sua senha é: {SENHA}".$quebra.
				"***********************************************************************".$quebra.$quebra.
				"Para utilizá-la acesse: http://www.fundacaofamiliaprevidencia.com.br.".$quebra.$quebra.
				"Lembre-se, a senha para utilização dos serviços de autoatendimento é pessoal e intransferível.".$quebra.$quebra.
				"Mantenha resguardo, ela passa a ser sua assinatura com os compromissos decorrentes dos serviços solicitados.".$quebra.$quebra.
				"Atenciosamente,".$quebra.$quebra.
				"Fundação Família Previdência".$quebra.
				"Gerência de Atendimento ao Participante".$quebra.$quebra.$quebra.
				"**** ATENÇÃO ****".$quebra.
				"Este e-mail é somente para leitura.".$quebra.
				"Caso queira falar conosco clique no link abaixo:".$quebra.
				"http://www.fundacaofamiliaprevidencia.com.br/fale_conosco.php".$quebra;	
	
	
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
					   'AUTO_ATENDIMENTO_SENHA_VALIDA_6'
					 );
			  ";
	@pg_query($db,$qr_sql);	

	if(intval($_POST['senha']) > 0)
	{
		$NOVA_SENHA = trim($_POST['senha']);
		if(trim(strlen($NOVA_SENHA) == 6))
		{
			$qr_sql = "
						UPDATE public.participantes_ccin
						   SET codigo_345            = '".trim($NOVA_SENHA)."',
						       codigo_358            = '".trim($NOVA_SENHA)."',
						       opcao_contrato_valida = '".trim($_SESSION['OCV'])."',
							   opcao_contrato_pedido = '".trim($_SESSION['OCV'])."',
							   motivo_alteracao      = 17,
							   codigo_356            = 'N',
							   codigo_357            = 'N',
							   data_envio_356        = CURRENT_TIMESTAMP,
							   data_envio_357        = CURRENT_TIMESTAMP,
							   data_envio_345        = CURRENT_TIMESTAMP, 
							   data_recebimento_ar   = CURRENT_TIMESTAMP,
							   data_bloqueio         = NULL,
							   data_desbloqueio      = CURRENT_TIMESTAMP,
							   dt_solicitacao        = CURRENT_TIMESTAMP,
							   dt_validade           = CURRENT_TIMESTAMP 
						 WHERE cd_empresa            = ".$_SESSION['EMP']." 
						   AND cd_registro_empregado = ".$_SESSION['RE']."
						   AND seq_dependencia       = ".$_SESSION['SEQ'].";

						UPDATE projetos.auto_atendimento_senha
						   SET dt_confirmacao = CURRENT_TIMESTAMP
						 WHERE cd_codigo = '".$_SESSION['CD_SENHA']."';						   
						   
						INSERT INTO projetos.log_solicita_senha
							 (
							   cd_empresa, 
							   cd_registro_empregado, 
							   cd_sequencia, 
							   tp_solicitacao
							 )
						VALUES 
							 (
							   ".$_SESSION['EMP'].", 
							   ".$_SESSION['RE'].", 
							   ".$_SESSION['SEQ'].",
							   'S'					   
							 );						   
					  ";		
			#echo "<pre>$qr_sql</pre>"; exit;
			#### ABRE TRANSACAO COM O BD ####
			pg_query($db,"BEGIN TRANSACTION");	
			$ob_resul= @pg_query($db,$qr_sql);
			if(!$ob_resul)
			{
				$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
				#### DESFAZ A TRANSACAO COM BD ####
				pg_query($db,"ROLLBACK TRANSACTION");
				pg_close($db);
				#echo $ds_erro;EXIT;
				echo "	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
						<SCRIPT>
							alert('Ocorreu um erro.\\n\\nEntre em contato pelo 0800 51 2596.');
							document.location.href = 'auto_atendimento_senha_passo_6.php?c=".$_SESSION['CD_SENHA']."';
						</SCRIPT>
					 ";	
				exit;
			}
			else
			{
				#### COMITA DADOS NO BD ####
				pg_query($db,"COMMIT TRANSACTION"); 
			}		
	
			$qr_sql = "
						SELECT p.nome,
							   funcoes.format_cpf(TO_CHAR(p.cpf_mf,'FM00000000000')) AS cpf_mf,
							   p.cd_empresa,
							   p.cd_registro_empregado,
							   p.seq_dependencia,
							   p.email,
							   p.email_profissional,
							   pc.codigo_358 AS senha
						  FROM public.participantes p
						  JOIN public.participantes_ccin pc
						    ON pc.cd_empresa            = p.cd_empresa 
						   AND pc.cd_registro_empregado = p.cd_registro_empregado 
						   AND pc.seq_dependencia       = p.seq_dependencia 					  
						 WHERE p.cd_empresa            = ".$_SESSION['EMP']." 
						   AND p.cd_registro_empregado = ".$_SESSION['RE']."
						   AND p.seq_dependencia       = ".$_SESSION['SEQ']."							   
					  ";
			$ob_resul = pg_query($db, $qr_sql);
			$ar_reg = pg_fetch_array($ob_resul);	
			
			/*
			$mensagem = str_replace("{NOME}",  str_replace("'", "", $ar_reg['nome']), $mensagem);
			$mensagem = str_replace("{CPF}",   $ar_reg['cpf_mf'], $mensagem);
			$mensagem = str_replace("{RE}", $ar_reg['cd_empresa']."/".$ar_reg['cd_registro_empregado']."/".$ar_reg['seq_dependencia'], $mensagem);
			$mensagem = str_replace("{SENHA}", $ar_reg['senha'], $mensagem);
			
			$qr_sql = "
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
							   'Fundação Família Previdência', 
							   '".str_replace("'","",$ar_reg['email'])."', 
							   '".str_replace("'","",$ar_reg['email_profissional'])."', 
							   '', 
							   'Senha de Autoatendimento Fundação Família Previdência', 
							   '".$mensagem."',
							   41,
							   'A'
							 );			
			          ";
			#echo "<pre>$qr_sql</pre>"; exit;
			@pg_query($db,$qr_sql);
			*/
			
			echo '
					<script>
						var confirmacao = "SUA SENHA ESTÁ PRONTA PARA UTILIZAÇÃO.\n\n" +
										  "Deseja ir para o autoatendimento agora?\n\n"+
										  "Clique [Ok] para SIM\n\n"+
										  "Clique [Cancelar] para NÃO\n\n";						
						if(confirm(confirmacao))
						{
							location.href="login.php?emp='.$_SESSION['EMP'].'&re='.$_SESSION['RE'].'&seq='.$_SESSION['SEQ'].'";
						}
						else
						{
							location.href="index.php";
						}
					</script>
				 ';
			$_SESSION = Array();
			@session_destroy("AUTO_ATENDIMENTO_SENHA");				 
			exit;
		}
		else
		{
			echo "	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
					<SCRIPT>
						alert('A senha não obedeçe as características necessárias.');
						document.location.href = 'auto_atendimento_senha_passo_6.php?c=".$_SESSION['CD_SENHA']."';
					</SCRIPT>
				 ";	
			exit;		
		}
	}
	else
	{
		$_SESSION = Array();
		@session_destroy("AUTO_ATENDIMENTO_SENHA");		
		echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=index.php'>";
	}
	?>
