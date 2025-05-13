<?php
	session_unset("PESQUISA_FCEEE");
	session_start("PESQUISA_FCEEE");
	$_SESSION = Array();
	
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	if(intval($_REQUEST['id']) == 442)
	{
		$tpl = new TemplatePower('tpl/tpl_tche_inicio.html');
	}
	else
	{
		$tpl = new TemplatePower('tpl/tpl_padrao.html');
	}
	$tpl->prepare();	
	$tpl->newBlock('titulo');
	$tpl->assign('titulo',"Fundação Família Previdência - Pesquisa");
	$_REQUEST['cd_secao'] = 'INST'; #INDICA A SEÇÃO
	$_REQUEST['cd_artigo'] = 4;     #INDICA O ARTIGO
	include_once('monta_menu.php');
	$tpl->newBlock('conteudo');	
	
	$_SESSION['ENQ_NOME']     = "";
	$_SESSION['ENQ_EMAIL']    = "";
	
		$_AR_ENQUETE = array();
	#### VERIFICA A DISPONIBILIDADE DA PESQUISA ####
	if(intval($_REQUEST['id']) > 0)
	{
		$_SESSION['ENQ_CD_ENQUETE'] = intval($_REQUEST['id']);
		
		$qr_sql = " 
					SELECT cd_enquete,
					       controle_respostas AS fl_controle,
						   titulo, 
						   texto_abertura 
					  FROM projetos.enquetes  
					 WHERE cd_enquete  = ".$_SESSION['ENQ_CD_ENQUETE']."
					   AND dt_inicio   <= CURRENT_TIMESTAMP
					   AND dt_fim      >= CURRENT_TIMESTAMP
					   AND dt_exclusao IS NULL
				  ";
		$ob_resul = pg_query($db, $qr_sql);	
		if(pg_num_rows($ob_resul) > 0)
		{
			$_AR_ENQUETE = pg_fetch_array($ob_resul);			
		}
		else
		{
			$conteudo = "<BR>
							<DIV style='font-family: Calibri, Arial; width:100%; text-align:center; color:red;'>
								<H2>ERRO - Pesquisa indisponível</H2>
								Entre em contato através do 08005102596
							</DIV>
						<BR><BR>";
			$tpl->assign('conteudo',$conteudo);
			$tpl->printToScreen();
			exit;	
		}		
	}
	else
	{
		$conteudo = "<BR>
						<DIV style='font-family: Calibri, Arial; width:100%; text-align:center; color:red;'>
							<H2>ERRO - Pesquisa indisponível</H2>
							Entre em contato através do 08005102596
						</DIV>
					<BR><BR>";
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();
		exit;		
	}	
	
	/*
		<select id="tp_controle_resposta" name="tp_controle_resposta" onkeypress="handleEnter(this, event);" class="">
			<option value="">Selecione</option>
			<option value="I">Computador-IP (Público externo e/ou interno)</option>
			<option value="U">Usuário e-prev (Somente colaboradores)</option>
			<option value="F">Formulário (Digitação de formulários)</option>
			<option value="P">Participante</option>
			<option value="R" selected="selected">RE</option>
		</select>	
	*/
	
	#### VERIFICA TIPO DE CONTROLE ####
	if($_AR_ENQUETE['fl_controle'] == "F") 
	{
		#### FORMULARIO ####
		
		if(trim($_REQUEST['c']) == "")
		{
			$_SESSION['ENQ_CD_CHAVE'] = (md5(uniqid(rand(),true)));
		}
		else
		{
			$_SESSION['ENQ_CD_CHAVE'] = trim($_REQUEST['c']);
		}		
	}	
	elseif($_AR_ENQUETE['fl_controle'] == "I") 
	{
		#### IP ####
		
		if(trim($_REQUEST['c']) == "")
		{
			$_SESSION['ENQ_CD_CHAVE'] = $_SERVER['REMOTE_ADDR']."-".date("mY");
		}
		else
		{
			$_SESSION['ENQ_CD_CHAVE'] = trim($_REQUEST['c']);
		}		
	}
	elseif($_AR_ENQUETE['fl_controle'] == "U") 
	{
		#### USUARIO E-PREV ####
		
		if(trim($_REQUEST['c']) == "")
		{
			#### SOLICITAR USUARIO / SENHA (CHAVE (MD5(USUARIO) || MD5(CD_PESQUISA))) ####
			
			$conteudo = getTemplatePesquisa("tpl_pesquisa_usuario.html");
			$conteudo = str_replace("{cd_enquete}", $_AR_ENQUETE['cd_enquete'], $conteudo);
			$conteudo = str_replace("{titulo}",     $_AR_ENQUETE['titulo'], $conteudo);
			$conteudo = str_replace("{descricao}",  nl2br($_AR_ENQUETE['texto_abertura']), $conteudo);
			$tpl->assign('conteudo',$conteudo);
			$tpl->printToScreen();	
			exit;			
		}
		else
		{
			#### VERIFICAR USUARIO ####
			$qr_sql = "
						SELECT COUNT(*) AS fl_usuario
						  FROM projetos.usuarios_controledi
						 WHERE MD5(LOWER(usuario)) || MD5('".$_AR_ENQUETE['cd_enquete']."') = '".trim($_REQUEST['c'])."'
						   AND tipo    <> 'X'
					  ";
			$ob_resul = @pg_query($db, $qr_sql);		  
			$ar_reg   = @pg_fetch_array($ob_resul);
			
			if(intval($ar_reg['fl_usuario']) > 0)
			{
				$_SESSION['ENQ_CD_CHAVE'] = trim($_REQUEST['c']);
			}
			else
			{
				header("location: pesquisa.php?id=".$_AR_ENQUETE['cd_enquete']); 	
				exit;					
			}
		}		
	}
	elseif($_AR_ENQUETE['fl_controle'] == "P") 
	{
		#### PARTICIPANTE ####
		
		if(trim($_REQUEST['c']) == "")
		{
			#### SOLICITAR CPF / SENHA (CHAVE (funcoes.cripto_re(cd_empresa,cd_registro_empregado,seq_dependencia) || MD5(CD_PESQUISA))) ####
			
			$conteudo = "<BR>
							<DIV style='font-family: Calibri, Arial; width:100%; text-align:center; color:red;'>
								<H2>ERRO - Código inválido</H2>
								Entre em contato através do 08005102596
							</DIV>
						<BR><BR>";
			$tpl->assign('conteudo',$conteudo);
			$tpl->printToScreen();
			exit;			
			
		}
		else
		{
			
			$qr_sql = " 
						SELECT p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia
						  FROM public.participantes p
						 WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".trim($_REQUEST['c'])."'
					  ";
			$ob_resul = pg_query($db, $qr_sql);	
			if(pg_num_rows($ob_resul) > 0)
			{
				$_SESSION['ENQ_CD_CHAVE'] = trim($_REQUEST['c']);	
			}
			else
			{
				$conteudo = "<BR>
								<DIV style='font-family: Calibri, Arial; width:100%; text-align:center; color:red;'>
									<H2>ERRO - Código participante inválido</H2>
									Entre em contato através do 08005102596
								</DIV>
							<BR><BR>";
				$tpl->assign('conteudo',$conteudo);
				$tpl->printToScreen();
				exit;	
			}			
			
			
		}		
	}	
	
	#### VERIFICA SE JÁ VOTOU ####
	$qr_sql = " 
				SELECT COUNT(*) AS fl_participou 
				  FROM projetos.enquete_resultados er
				 WHERE er.cd_enquete = ".$_SESSION['ENQ_CD_ENQUETE']."
				   AND er.ip         = '".$_SESSION['ENQ_CD_CHAVE']."'
			  ";	
	#echo $qr_sql;		  
			  
	$ob_resul = pg_query($db, $qr_sql);
	$ar_reg   = pg_fetch_array($ob_resul);		
	if($ar_reg['fl_participou'] > 0)
	{
		$conteudo = "<BR>
						<DIV style='font-family: Calibri, Arial; width:100%; text-align:center; color:#005930;'>
							<H2>Você já respondeu, obrigado pela sua participação.</H2>
						</DIV>
					<BR><BR>";
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();		
		exit;
	}
	
	#### MONTA TELA INICIAL ####
	if(count($_AR_ENQUETE) > 0)	{
		$_SESSION['ENQ_AR_RESPOSTA'] = Array();
		
		$ar_reg   = pg_fetch_array($ob_resul);
		
		$conteudo = getTemplatePesquisa("tpl_pesquisa.html");
		$conteudo = str_replace("{cd_enquete}", $_AR_ENQUETE['cd_enquete'], $conteudo);
		$conteudo = str_replace("{titulo}",     $_AR_ENQUETE['titulo'], $conteudo);
		$conteudo = str_replace("{descricao}",  nl2br($_AR_ENQUETE['texto_abertura']), $conteudo);
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();	
		exit;
	}
	else
	{
		$conteudo = "<BR>
						<DIV style='font-family: Calibri, Arial; width:100%; text-align:center; color:red;'>
							<H2>ERRO - Pesquisa indisponível</H2>
							Entre em contato através do 08005102596
						</DIV>
					<BR><BR>";
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();
		exit;	
	}
	
	function getTemplatePesquisa($arquivo)
	{
		$ds_arq   = "tpl/".$arquivo;
		$ob_arq   = fopen($ds_arq, 'r');
		$conteudo = fread($ob_arq, filesize($ds_arq));
		fclose($ob_arq);	
		
		return $conteudo;
	}
?>