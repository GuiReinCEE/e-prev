<?php
	require_once('inc/sessao_auto_atendimento.php');
	include_once('inc/class.SocketAbstraction2.inc.php');
	include_once('inc/class.TemplatePower.inc.php');
	include_once('inc/class.BrowserDetect.inc.php');
	include_once('funcoes.inc.php');
	include_once('inc/conexao.php');
	require_once('inc/config.inc.php');
	
	#https://github.com/thread-pond/signature-pad

	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();
	include_once('auto_atendimento_monta_sessao.php');
	
	$ds_arq   = "tpl/tpl_auto_atendimento_emprestimo_confirmar.html";
	$ob_arq   = fopen($ds_arq, 'r');
	$conteudo = fread($ob_arq, filesize($ds_arq));
	fclose($ob_arq);

 
	$impressaoOk = false;
	$emp            = intval($_SESSION['EMP']);
	$re             = intval($_SESSION['RE']);
	$seq            = intval($_SESSION['SEQ']);
	$sessao         = $_POST['session_id'];
	$num_prestacoes = intval($_POST['num_prestacoes']);

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
					   'EMP_CONFIRMA'
					 )
			  ";
	@pg_query($db,$qr_sql);	

	$send = "fnc_valida_sessao_partic;$emp;$re;$seq;$sessao";
	$skt = new Socket();
	$skt->SetRemoteHost(SKT_IP);
	$skt->SetRemotePort(SKT_PORTA);
	$skt->SetBufferLength(131072);
	$skt->SetConnectTimeOut(1);
	if ($skt->Connect()) 
	{
		$ret = $skt->Ask($send);
		if ($skt->Error()) 
		{
			echo "Ocorreu um erro na troca de mensagens com o webservice";
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
				echo $erro;
				exit;				
			
			} 
			else 
				{
				$send = "fnc_busca_inf_concessao;$sessao;$num_prestacoes";
				
				#<fld tp="DAT" id="FORMA_CALCULO">O</fld>
				
				$cn = new Socket();
				$cn->SetRemoteHost(SKT_IP);
				$cn->SetRemotePort(SKT_PORTA);
				$cn->SetBufferLength(131072);
				$cn->SetConnectTimeOut(1);
				if ($cn->Connect()) 
				{
					$ret = $cn->Ask($send);
					if ($cn->Error()) 
					{
						echo "Ocorreu um erro de conexão com o webservice";
						exit;
					} 
					else 
					{
						// Coloca os dados na tela
						$dom = new DOMDocument("1.0", "utf-8");
						$dom->loadXML(utf8_encode($ret)); 
						$campos = $dom->getElementsByTagName("fld");
						
						foreach ($campos as $nodo) 
						{
							if ($nodo->getAttribute('tp') == 'DAT') 
							{
								if((strtolower($nodo->getAttribute('id')) == "email") and (trim($nodo->nodeValue) == "0"))
								{
									$conteudo = str_replace("{".strtolower($nodo->getAttribute('id'))."}","",$conteudo);
								}
								elseif ((strtolower($nodo->getAttribute('id')) == "email_profissional") and (trim($nodo->nodeValue) == "0"))
								{
									$conteudo = str_replace("{".strtolower($nodo->getAttribute('id'))."}","",$conteudo);
								}
								elseif (strtolower($nodo->getAttribute('id')) == "forma_calculo") 
								{
									if ($nodo->nodeValue == "O") # POS-FIXADO
									{
										$conteudo = str_replace("{".strtolower($nodo->getAttribute('id'))."}","PÓS-FIXADO",$conteudo);
										$conteudo = str_replace("{nova_simulacao}","posfixado",$conteudo);
										$conteudo = str_replace("{ds_vlr_prestacao}","Prestação Projetada(*)",$conteudo);
										$conteudo = str_replace("{fl_vlr_prestacao}","",$conteudo);										
									}
									elseif ($nodo->nodeValue == "P") # PREFIXADO
									{
										$conteudo = str_replace("{".strtolower($nodo->getAttribute('id'))."}","PREFIXADO",$conteudo);
										$conteudo = str_replace("{nova_simulacao}","prefixado",$conteudo);
										$conteudo = str_replace("{ds_vlr_prestacao}","Valor da prestação",$conteudo);
										$conteudo = str_replace("{fl_vlr_prestacao}","display:none;",$conteudo);
									}
									else									
									{
										$conteudo = str_replace("{".strtolower($nodo->getAttribute('id'))."}","",$conteudo);
										$conteudo = str_replace("{nova_simulacao}","",$conteudo);
										$conteudo = str_replace("{ds_vlr_prestacao}","Valor da prestação",$conteudo);
										$conteudo = str_replace("{fl_vlr_prestacao}","",$conteudo);										
									}
								}								
								else
								{
									$conteudo = str_replace("{".strtolower($nodo->getAttribute('id'))."}",$nodo->nodeValue,$conteudo);
								}
							}
						}
						$conteudo = str_replace("{session_id}",$sessao,$conteudo);
					}
				} 
				else 
				{
					echo "Ocorreu um erro de conexão com o webservice";
					exit;
				}
			}
		}
	}
	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();
?>
