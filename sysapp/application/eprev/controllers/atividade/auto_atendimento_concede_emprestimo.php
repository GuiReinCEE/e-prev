<?php
	set_time_limit(0);
	
	require_once('inc/sessao_auto_atendimento.php');
	include_once('inc/class.SocketAbstraction2.inc.php');
	include_once('inc/class.TemplatePower.inc.php');
	include_once('inc/class.BrowserDetect.inc.php');
	include_once('funcoes.inc.php');
	include_once('inc/conexao.php');
	require_once('inc/config.inc.php');
	// Será substituido no "proxyEmprestimo" pelas variáveis de sessão
	//   session_start();
	//$sessao = session_id();
	
	//define(SKT_IP, '10.63.255.16'); 
	//define(SKT_PORTA, '4444');
	#https://www.fundacaofamiliaprevidencia.com.br/auto_atendimento_confirma_emprestimo.php?cd_secao=AASR&cd_artigo=47
	
	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();
	include_once('auto_atendimento_monta_sessao.php');
   
	/*
	echo "<PRE>";
	print_r($_REQUEST);
	print_r($_GET);
	print_r($_POST);
	print_r($_SESSION);
	exit;
	*/
   
	#### GRAVAR IMAGEM DA ASSINATURA DO EMPRESTIMO ####

	$assinatura = explode(",", $_POST['assinatura-base64']);

	$assinatura_participante = 'emp/'.$_SESSION['EMP'].'-'.$_SESSION['RE'].'-'.$_SESSION['SEQ'].'_ass_emp_'.date('YmdHis').'.jpg';
	file_put_contents($assinatura_participante, base64_decode($assinatura[1]));   
	
	$fl_assinatura_erro = FALSE;
    if (file_exists($assinatura_participante))
    {
        $imagesizedata = getimagesize($assinatura_participante);
        if ($imagesizedata === FALSE)
        {
            #echo "Not image"; exit;
			$fl_assinatura_erro = TRUE;
        }
    }
    else
    {
        #echo "Not file"; exit;
		$fl_assinatura_erro = TRUE;
    }	
	
	if($fl_assinatura_erro)
	{
		echo "<script>
				alert('Não foi possível conceder este empréstimo.\\n\\nÉ NECESSÁRIO ASSINAR.\\n\\nFicou com dúvida, entre em contato com o teleatendimento de segunda a sexta-feira, pelo telefone 0800512596.');
			  </script>
			 ";		
		
		$qr_erro = "
					INSERT INTO projetos.log
						 (
							tipo, 
							\"local\", 
							descricao, 
							dt_cadastro
						 )
					VALUES 
						 ( 
							'EMP_WEB',
							'EMP_CONCEDE',
							'EMPRESTIMO ERRO 0:\n".$skt->Error()."\nOcorreu um erro na assinatura\n".$send."\nIP: ".$_SERVER['REMOTE_ADDR']."\n".$_SERVER['HTTP_REFERER']."',
							CURRENT_TIMESTAMP
						 )
				   ";
		@pg_query($db,$qr_erro);			
		exit;		
	}
	
	#exit;
	
	$emp = $_SESSION['EMP'];
	$re  = $_SESSION['RE'];
	$seq = $_SESSION['SEQ'];
	$sessao = $_REQUEST['session_id'];

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
					   'EMP_CONCEDE'
					 )
		      ";
	@pg_query($db,$qr_sql); 	
	
	$send = "fnc_valida_sessao_partic;$emp;$re;$seq;$sessao";
	$skt = new Socket();
	$skt->SetRemoteHost(SKT_IP);
	$skt->SetRemotePort(SKT_PORTA);
	$skt->SetBufferLength(131072);
	$skt->SetConnectTimeOut(5);
	if ($skt->Connect()) 
	{
		$ret = $skt->Ask($send);
		if ($skt->Error()) 
		{
			echo "Ocorreu um erro na troca de mensagens com o webservice";
			$qr_erro = "
						INSERT INTO projetos.log
							 (
								tipo, 
								\"local\", 
								descricao, 
								dt_cadastro
							 )
						VALUES 
							 ( 
								'EMP_WEB',
								'EMP_CONCEDE',
								'EMPRESTIMO ERRO 1:\n".$skt->Error()."\nOcorreu um erro na troca de mensagens com o webservice\n".$send."\nIP: ".$_SERVER['REMOTE_ADDR']."\n".$_SERVER['HTTP_REFERER']."',
								CURRENT_TIMESTAMP
							 )
					   ";
			@pg_query($db,$qr_erro);			
			exit;
		 
		} 
		else 
		{
			$dom = new DOMDocument('1.0', 'iso-8859-1');
			$dom->loadXML('<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>'.$ret);
			$campos = $dom->getElementsByTagName("fld");
			$erro = getFieldValueXML($campos, 'ERR');
			if ($erro != 'NULL') 
			{
				echo "<script>
						alert('Não foi possível conceder este empréstimo.\\n\\nEntre em contato com o teleatendimento de segunda a sexta-feira, pelo telefone 0800512596.');
					  </script>
					 ";

				$qr_erro = "
							INSERT INTO projetos.log
							     (
								    tipo, 
									\"local\", 
									descricao, 
									dt_cadastro
								 )
							VALUES 
							     ( 
								    'EMP_WEB',
									'EMP_CONCEDE',
									'EMPRESTIMO ERRO 2:\n".utf8_decode($erro)."\n".$send."\nIP: ".$_SERVER['REMOTE_ADDR']."\n".$_SERVER['HTTP_REFERER']."',
									CURRENT_TIMESTAMP
								 )
				           ";
				@pg_query($db,$qr_erro);
				
				if (preg_match('/10.63./',$_SERVER['REMOTE_ADDR']))
				{
					echo "<script>
							alert(\"".utf8_decode($erro)."\");
							document.location.href='auto_atendimento_simulacao_participante.php?cd_secao=AASR&cd_artigo=36';
					      </script>
					     ";
				}
				
				exit;				
				
			} 
			else 
			{
	
				// Busca informações novamente, por questões de segurança (principalmente em relação a banco, agência, conta de depósito
				$sessao         = $_REQUEST['session_id'];
				$num_prestacoes = $_REQUEST['nro_prestacoes'];
				$fl_autoriza_debito = ($_REQUEST['fl_autoriza_debito'] == 'S' ? $_REQUEST['fl_autoriza_debito'] : 'N');
				$telefone       = ($_REQUEST['telefone'] != '' ? $_REQUEST['telefone'] : 'null');
				$email          = ($_REQUEST['email'] != '' ? $_REQUEST['email'] : 'null');
				
				$send = "fnc_busca_inf_concessao;$sessao;$num_prestacoes";
				
				$cn = new Socket();
				$cn->SetRemoteHost(SKT_IP);
				$cn->SetRemotePort(SKT_PORTA);
				$cn->SetBufferLength(131072);
				$cn->SetConnectTimeOut(5);
				if ($cn->Connect()) 
				{
					$ret = $cn->Ask($send);
					if ($cn->Error()) 
					{
						echo "Ocorreu um erro de conexão com o webservice !";
						$qr_erro = "
									INSERT INTO projetos.log
										 (
											tipo, 
											\"local\", 
											descricao, 
											dt_cadastro
										 )
									VALUES 
										 ( 
											'EMP_WEB',
											'EMP_CONCEDE',
											'EMPRESTIMO ERRO 3:\n".$cn->Error()."\nOcorreu um erro na troca de mensagens com o webservice\n".$send."\nIP: ".$_SERVER['REMOTE_ADDR']."\n".$_SERVER['HTTP_REFERER']."',
											CURRENT_TIMESTAMP
										 )
								   ";
						@pg_query($db,$qr_erro);			
						exit;						
					} 
					else 
					{
						// Coloca os dados na tela
			//         $dom = new DOMDocument("1.0", "ISO-8859-1");
			
						// BUSCA INFORMAÇÕES NECESSÁRIAS PARA A CONCESSÃO
						$dom = new DOMDocument("1.0", "utf-8");
						$dom->loadXML(utf8_encode($ret)); // Rever isto, pois está enviando caracteres estranhos ao invés dos acentos.
						$campos = $dom->getElementsByTagName("fld");
						$emp            = getFieldValueXML($campos, 'CD_EMPRESA');
						$re             = getFieldValueXML($campos, 'CD_REGISTRO_EMPREGADO');
						$seq            = getFieldValueXML($campos, 'SEQ_DEPENDENCIA');
						$nro_prestacoes = getFieldValueXML($campos, 'NRO_PRESTACOES');
						$usuario        = 'WEB';
						$senha          = 'WEB';
						$banco          = getFieldValueXML($campos, 'CD_INSTITUICAO');
						$agencia        = getFieldValueXML($campos, 'CD_AGENCIA');
						$conta          = getFieldValueXML($campos, 'CONTA_FOLHA');
			
						// CONCEDE O EMPRÉSTIMO
						$send = "fnc_concede_emprestimo;$sessao;$emp;$re;$seq;$usuario;$senha;$nro_prestacoes;$banco;$agencia;$conta;$telefone;$email;BCO;P;N;$assinatura_participante;";
						$cn2 = new Socket();
						$cn2->SetRemoteHost(SKT_IP);
						$cn2->SetRemotePort(SKT_PORTA);
						$cn2->SetBufferLength(131072);
						$cn2->SetConnectTimeOut(5);
						if ($cn2->Connect()) 
						{
							$ret = $cn2->Ask($send);
							if ($cn2->Error()) 
							{
								//$tpl = new TemplatePower('tpl_erro.html');
								//$tpl->prepare();
								//$tpl->assign('detalhes_erro', "Ocorreu um erro de conexão com o webservice");
								//$tpl->printToScreen();
								echo "Ocorreu um erro de conexão com o webservice";
								$qr_erro = "
											INSERT INTO projetos.log
												 (
													tipo, 
													\"local\", 
													descricao, 
													dt_cadastro
												 )
											VALUES 
												 ( 
													'EMP_WEB',
													'EMP_CONCEDE',
													'EMPRESTIMO ERRO 4:\n".$cn2->Error()."\nOcorreu um erro de conexão com o webservice\n".$send."\nIP: ".$_SERVER['REMOTE_ADDR']."\n".$_SERVER['HTTP_REFERER']."',
													CURRENT_TIMESTAMP
												 )
										   ";
								@pg_query($db,$qr_erro);								
								exit;
								
							} 
							else 
							{
								// Coloca os dados na tela
								$dom = new DOMDocument('1.0', 'iso-8859-1');
								$dom->loadXML('<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>'.$ret);
								$campos = $dom->getElementsByTagName("fld");
								$erro = getFieldValueXML($campos, 'ERR');
								if ($erro != 'NULL') 
								{
									//$tpl = new TemplatePower('tpl/tpl_erro.html');
									//$tpl->prepare();
									//$tpl->assign('detalhes_erro', $erro);
									//$tpl->printToScreen();
									echo "<script>
											alert('Não foi possível conceder este empréstimo.\\n\\nEntre em contato com o teleatendimento de segunda a sexta-feira, pelo telefone 0800512596.');
										  </script>
										 ";			
									$qr_erro = "
												INSERT INTO projetos.log
													 (
														tipo, 
														\"local\", 
														descricao, 
														dt_cadastro
													 )
												VALUES 
													 ( 
														'EMP_WEB',
														'EMP_CONCEDE',
														'EMPRESTIMO ERRO 5:\n".utf8_decode($erro)."\n".$send."\nIP: ".$_SERVER['REMOTE_ADDR']."\n".$_SERVER['HTTP_REFERER']."',
														CURRENT_TIMESTAMP
													 )
											   ";
									@pg_query($db,$qr_erro);
										 
									if (preg_match('/10.63./',$_SERVER['REMOTE_ADDR']))
									{
										echo "<script>
												alert(\"".utf8_decode($erro)."\");
												document.location.href='auto_atendimento_simulacao_participante.php?cd_secao=AASR&cd_artigo=36';
										      </script>
										     ";
									}
									exit;
									
								} 
								else 
								{
									$contrato = getFieldValueXML($campos, 'CONTRATO');
									$qr_erro = "
												INSERT INTO projetos.log
													 (
														tipo, 
														\"local\", 
														descricao, 
														dt_cadastro
													 )
												VALUES 
													 ( 
														'EMP_WEB',
														'EMP_CONCEDE',
														'EMPRESTIMO SUCESSO:\nContrato: ".$contrato."\n".$send."\nIP: ".$_SERVER['REMOTE_ADDR']."\n".$_SERVER['HTTP_REFERER']."',
														CURRENT_TIMESTAMP
													 )
											   ";
									@pg_query($db,$qr_erro);										
													
									echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=auto_atendimento_mostra_contrato_emprestimo.php?cd_secao=AASR&cd_artigo=49&c='.$contrato.'">';
									//header("location: auto_atendimento_mostra_contrato_emprestimo.php?cd_secao=AASR&cd_artigo=49&c=$contrato");
								}
							}
						}
					}
				} 
				else 
				{
					//$tpl = new TemplatePower('tpl/tpl_erro.html');
					//$tpl->prepare();
					//$tpl->assign('detalhes_erro', "Ocorreu um erro de conexão com o webservice");
					//$tpl->printToScreen();
					echo "Ocorreu um erro de conexão com o webservice";
					$qr_erro = "
								INSERT INTO projetos.log
									 (
										tipo, 
										\"local\", 
										descricao, 
										dt_cadastro
									 )
								VALUES 
									 ( 
										'EMP_WEB',
										'EMP_CONCEDE',
										'EMPRESTIMO ERRO 6:\nOcorreu um erro na troca de mensagens com o webservice\n".$send."\nIP: ".$_SERVER['REMOTE_ADDR']."\n".$_SERVER['HTTP_REFERER']."',
										CURRENT_TIMESTAMP
									 )
							   ";
					@pg_query($db,$qr_erro);			
					exit;
				}
			}
		}
	}
   
?>
