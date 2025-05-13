<?php
	session_start("PESQUISA_FCEEE");
	$nr_erro = 0;

	#echo "<PRE>"; print_r($_REQUEST); exit;
	
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	if(intval($_SESSION['ENQ_CD_ENQUETE']) == 442)
	{
		$tpl = new TemplatePower('tpl/tpl_tche_inicio.html');
	}
	else if(intval($_SESSION['ENQ_CD_ENQUETE']) == 634)
	{
		$tpl = new TemplatePower('tpl/tpl_padrao_634.html');
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
	
	$ds_arq   = "tpl/tpl_pesquisa_responde.html";
	$ob_arq   = fopen($ds_arq, 'r');
	$conteudo = fread($ob_arq, filesize($ds_arq));
	fclose($ob_arq);	
	
	#### VERIFICA SESSÃO ####
	$nr_erro++;
	if($_SESSION['ENQ_CD_CHAVE'] == "")
	{
		$conteudo = "<BR>
						<DIV style='font-family: Calibri, Arial; width:100%; text-align:center; color:red;'>
							<H2>ERRO - Respondente não identificado [EB".$nr_erro."]</H2>
							Entre em contato através do 0800512596
						</DIV>
					<BR><BR>";
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();
		exit;	
	}	
	
	#### VERIFICA A DISPONIBILIDADE DA PESQUISA ####
	$qr_sql = "
				SELECT COUNT(*) AS fl_enquete
				  FROM projetos.enquetes 
				 WHERE cd_enquete         = ".$_SESSION['ENQ_CD_ENQUETE']."
				   AND dt_inicio          <= CURRENT_TIMESTAMP
				   AND dt_fim             >= CURRENT_TIMESTAMP
				   AND dt_exclusao        IS NULL
	          ";
	$ob_resul = pg_query($db, $qr_sql);
	$ar_reg   = pg_fetch_array($ob_resul);		
	$nr_erro++;
	if($ar_reg['fl_enquete'] == 0)
	{
		$conteudo = "<BR>
						<DIV style='font-family: Calibri, Arial; width:100%; text-align:center; color:red;'>
							<H2>ERRO - Avaliação indisponível [EB".$nr_erro."]</H2>
							Entre em contato através do 0800512596
						</DIV>
					<BR><BR>";
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();
		exit;		
	}	
	
	#### VERIFICA SE JÁ VOTOU ####
	$qr_sql = " 
				SELECT COUNT(*) AS fl_participou 
				  FROM projetos.enquete_resultados er
				 WHERE er.cd_enquete = ".$_SESSION['ENQ_CD_ENQUETE']."
				   AND er.ip         = '".$_SESSION['ENQ_CD_CHAVE']."'
			  ";	
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
	
	#### VERIFICA ORDEM PAGINACAO ####
	$_REQUEST['proxima_ordem'] = intval(trim($_REQUEST['proxima_ordem']) == "" ? 1 : $_REQUEST['proxima_ordem']);
	
	#### VERIFICA SE É ULTIMA TELA ####
	$ultima_tela = ($_REQUEST['proxima_ordem'] == 0 ? "S" : "N");

	#### BUSCA NOME DO AGRUPAMENTO ####
	$qr_sql = " 
				SELECT nome AS nome
				  FROM projetos.enquete_agrupamentos 
				 WHERE cd_enquete  = ".$_SESSION['ENQ_CD_ENQUETE']." 
				   AND ordem       = ".$_REQUEST['proxima_ordem']."
				   AND dt_exclusao IS NULL 
			   ";
	$ob_resul = pg_query($db, $qr_sql);
	$ar_reg   = pg_fetch_array($ob_resul);	
	$conteudo = str_replace("{NOME_AGRUPAMENTO}", $ar_reg['nome'], $conteudo);
	
	#### BUSCA 1º AGRUPAMENTO ####
	if ($_REQUEST['proxima_ordem'] == 1 ) 
	{ 
		$qr_sql = " 
					SELECT cd_agrupamento, 
					       ordem,
						   nome
					  FROM projetos.enquete_agrupamentos 
					 WHERE cd_enquete  = ".$_SESSION['ENQ_CD_ENQUETE']." 
					   AND ordem       = ".$_REQUEST['proxima_ordem']."
					   AND dt_exclusao IS NULL 
				   ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		### VER
		$proximo_agrupamento = $ar_reg['cd_agrupamento'];
		if ($_REQUEST["agrup"] == '') { $_REQUEST["agrup"] = $proximo_agrupamento; }
	}

	#### BUSCA PROXIMA PAGINACAO ####
	$qr_sql = " 
				SELECT ordem
				  FROM projetos.enquete_agrupamentos 
				 WHERE cd_enquete  = ".$_SESSION['ENQ_CD_ENQUETE']." 
				   AND ordem       > ".$_REQUEST['proxima_ordem']." 
				   AND dt_exclusao IS NULL 
				 ORDER BY ordem
		      ";
	$ob_resul = pg_query($db, $qr_sql);
	$ar_reg   = pg_fetch_array($ob_resul);
	$_REQUEST['proxima_ordem'] = $ar_reg['ordem'];
	$conteudo = str_replace("{proxima_ordem}", $_REQUEST['proxima_ordem'], $conteudo);
	
	
	#### AGRUPAMENTO ####
	$_REQUEST["agrup"] = intval(trim($_REQUEST["agrup"]) == "" ? $proximo_agrupamento : $_REQUEST["agrup"]);
	$conteudo = str_replace("{cd_agrupamento}", $_REQUEST["agrup"], $conteudo);
	
	#### VERIFICA AGRUPAMENTOS ####
	$qr_sql = " 
				SELECT COUNT(*) AS fl_agrupamento
				  FROM projetos.enquete_agrupamentos  
				 WHERE cd_enquete   = ".$_SESSION['ENQ_CD_ENQUETE']."
				   AND indic_escala = 'S'
				   AND dt_exclusao  IS NULL 
			  ";
	$ob_resul = pg_query($db, $qr_sql);
	$ar_reg   = pg_fetch_array($ob_resul);	
	$nr_erro++;
	if($ar_reg['fl_agrupamento'] > 0)
	{
		$conteudo = "<BR>
						<DIV style='font-family: Calibri, Arial; width:100%; text-align:center; color:red;'>
							<H2>ERRO - Agrupamento por Escala [EB".$nr_erro."]</H2>
							Entre em contato através do 0800512596
						</DIV>
					<BR><BR>";
		echo $conteudo;
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();
		exit;
	}
	
	#### MONTA CABEÇALHO ####
	$conteudo = str_replace("{eq}", $_SESSION['ENQ_CD_ENQUETE'], $conteudo);
	$qr_sql = " 
				SELECT e.cd_enquete, 
				       e.titulo, 
					   e.texto_encerramento, 
					   (SELECT COUNT(*) 
			              FROM projetos.enquete_agrupamentos ea
				         WHERE ea.cd_enquete     = e.cd_enquete
				           AND ea.cd_agrupamento = ".$_REQUEST["agrup"]."
				           AND ea.dt_exclusao    IS NULL) AS fl_agrupamento
				  FROM projetos.enquetes e
				 WHERE e.cd_enquete  = ".$_SESSION['ENQ_CD_ENQUETE']."  
			  ";
	$ob_resul = pg_query($db, $qr_sql);
	$ar_reg   = pg_fetch_array($ob_resul);
	$conteudo = str_replace("{titulo}", $ar_reg['titulo'], $conteudo);
	
	$fl_agrupamento       = $ar_reg['fl_agrupamento'];
	$texto_encerramento = (trim($ar_reg['texto_encerramento']) != "" ? nl2br($ar_reg['texto_encerramento']) : "Obrigado por participar.");
		
	$nr_erro++;	
	if (($fl_agrupamento == 0) or ($ultima_tela == 'S'))
	{
		#### ENCERRA VOTAÇÃO ####
		
		#### MONTA AS RESPOSTAS ####
		while(list($chave, $valor) = each($_SESSION['ENQ_AR_RESPOSTA'])) 
		{
			#echo "<PRE>"; print_r($valor); echo "</PRE>";
			$qr_resp.="
						INSERT INTO projetos.enquete_resultados 
						     ( 
							   cd_enquete, 
							   cd_agrupamento, 
							   ip, 
							   questao, 
							   valor, 
							   dt_resposta,
							   descricao,
							   complemento
						     )
						VALUES 
						     ( 
							   ".$valor['cd_enquete'].",
							   ".$valor['cd_agrupamento'].", 
							   '".$valor['ip']."', --CODIGO DA INSCRICAO EM MD5
							   '".$valor['questao']."', 
							   ".$valor['valor'].",
							   CURRENT_TIMESTAMP ,
							   ".$valor['descricao'].",
							   ".$valor['complemento']."
						     );
			          ";
			#echo "<PRE>"; print_r($qr_resp); echo "</PRE>";
		}
		$qr_sql = $qr_resp;
		
		#### ABRE TRANSACAO COM O BD ####
		pg_query($db,"BEGIN TRANSACTION");	
		$ob_resul= @pg_query($db,$qr_sql);
		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			echo $ds_erro;
			#### DESFAZ A TRANSACAO COM BD ####
			pg_query($db,"ROLLBACK TRANSACTION");
	
			$conteudo = "<BR>
							<DIV style='font-family: Calibri, Arial; width:100%; text-align:center; color:red;'>
								<H2>ERRO - Não foi possível gravar [EB".$nr_erro."]</H2>
								Entre em contato através do 0800512596
							</DIV>
						<BR><BR>";
			$tpl->assign('conteudo',$conteudo);
			$tpl->printToScreen();
			exit;		
		}
		else
		{
			#### COMITA DADOS NO BD ####
			pg_query($db,"COMMIT TRANSACTION"); 
			
			if($_SESSION['ENQ_CD_ENQUETE'] == 315)
			{
				#### SOMENTE PARA PESQUISA DIALOGO INSTITUCIONAL ####
				$qr_sql = "
							SELECT di.cd_dialogo_inscricao, 
								   UPPER(funcoes.remove_acento(di.nome)) AS nome, 
								   di.email,
								   di.cd_empresa, 
								   di.cd_registro_empregado, 
								   di.seq_dependencia, 
								   CASE WHEN di.fl_presente = 'S' 
										THEN funcoes.gera_link(REPLACE(d.certificado,'{CD_CERTIFICADO_MD5}',MD5(di.cd_dialogo_inscricao::TEXT)), di.cd_empresa, di.cd_registro_empregado, di.seq_dependencia)
										ELSE ''
								   END AS link_certificado,
								   CASE WHEN di.fl_presente = 'S' 
										THEN REPLACE(d.certificado,'{CD_CERTIFICADO_MD5}',MD5(di.cd_dialogo_inscricao::TEXT))
										ELSE 'index.php'
								   END AS link_redireciona,							   
								   d.cd_dialogo,
								   d.ds_dialogo
							  FROM acs.dialogo_inscricao di
							  JOIN acs.dialogo d
								ON d.cd_dialogo = di.cd_dialogo
							 WHERE di.dt_exclusao IS NULL
							   AND MD5(di.cd_dialogo_inscricao::TEXT) = '".$_SESSION['ENQ_CD_CHAVE']."'
						  ";
				$ob_resul = pg_query($db, $qr_sql);
				$ar_reg   = pg_fetch_array($ob_resul);		
				if(pg_num_rows($ob_resul))
				{
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
	   cd_empresa,
	   cd_registro_empregado,
	   seq_dependencia,
	   cd_evento,
	   tp_email
	 ) 
VALUES 
	 (
	   CURRENT_TIMESTAMP,
	   '".$ar_reg['ds_dialogo']."',
	   '".$ar_reg['email']."',     
	   '',
	   '',                      
	   'Certificado: ".$ar_reg['ds_dialogo']."',
'Prezado(a): ".$ar_reg['nome'].".

Agradecemos a sua participação no ".$ar_reg['ds_dialogo'].".

Aproveitamos a oportunidade para disponibilizar, no link abaixo, seu certificado de participação no Diálogo:

".$ar_reg['link_certificado']."

Atenciosamente, 

Fundação Família Previdência
www.fundacaofamiliaprevidencia.com.br
0800 51 2596

**** ATENÇÃO ****
Este e-mail é somente para leitura.
Caso queira falar conosco clique no link abaixo:
https://www.fundacaofamiliaprevidencia.com.br/fale_conosco.php
',
	   ".(trim($ar_reg['cd_empresa']) == "" ? "DEFAULT" : intval($ar_reg['cd_empresa'])).",
	   ".(intval($ar_reg['cd_registro_empregado']) == 0 ? "DEFAULT" : intval($ar_reg['cd_registro_empregado'])).",
	   ".(trim($ar_reg['seq_dependencia']) == "" ? "DEFAULT" : intval($ar_reg['seq_dependencia'])).",
		73,
		'F'
		);	
		
UPDATE acs.dialogo_inscricao
   SET dt_envio_certificado = CURRENT_TIMESTAMP
 WHERE cd_dialogo_inscricao = ".intval($ar_reg['cd_dialogo_inscricao']).";
		                          ";		
					@pg_query($db, $qr_sql);
					echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL='.$ar_reg['link_redireciona'].'">';
					exit;
				}				
			}
			elseif(
					($_SESSION['ENQ_CD_ENQUETE'] == 284) or 
					($_SESSION['ENQ_CD_ENQUETE'] == 286) or 
					($_SESSION['ENQ_CD_ENQUETE'] == 304) or 
					($_SESSION['ENQ_CD_ENQUETE'] == 344) or 
                    ($_SESSION['ENQ_CD_ENQUETE'] == 376) or
					($_SESSION['ENQ_CD_ENQUETE'] == 378) or
					($_SESSION['ENQ_CD_ENQUETE'] == 376) or 
					($_SESSION['ENQ_CD_ENQUETE'] == 386) or 
					($_SESSION['ENQ_CD_ENQUETE'] == 387) or
					($_SESSION['ENQ_CD_ENQUETE'] == 391) or
					($_SESSION['ENQ_CD_ENQUETE'] == 394) or
					($_SESSION['ENQ_CD_ENQUETE'] == 392) or
					($_SESSION['ENQ_CD_ENQUETE'] == 403) or
                    ($_SESSION['ENQ_CD_ENQUETE'] == 407) or
                    ($_SESSION['ENQ_CD_ENQUETE'] == 414) or
                    ($_SESSION['ENQ_CD_ENQUETE'] == 442) or
                    ($_SESSION['ENQ_CD_ENQUETE'] == 455) or
                    ($_SESSION['ENQ_CD_ENQUETE'] == 455) or
                    ($_SESSION['ENQ_CD_ENQUETE'] == 476) or
                    ($_SESSION['ENQ_CD_ENQUETE'] == 479) or
                    ($_SESSION['ENQ_CD_ENQUETE'] == 489) or
                    ($_SESSION['ENQ_CD_ENQUETE'] == 514) or
                    ($_SESSION['ENQ_CD_ENQUETE'] == 516) or
                    ($_SESSION['ENQ_CD_ENQUETE'] == 518) or 
                    ($_SESSION['ENQ_CD_ENQUETE'] == 532) or
                    ($_SESSION['ENQ_CD_ENQUETE'] == 537) or
                    ($_SESSION['ENQ_CD_ENQUETE'] == 574) or
                    ($_SESSION['ENQ_CD_ENQUETE'] == 591) or
                    ($_SESSION['ENQ_CD_ENQUETE'] == 593) or
                    ($_SESSION['ENQ_CD_ENQUETE'] == 630) or
                    ($_SESSION['ENQ_CD_ENQUETE'] == 645)
				  )
			{
				$qr_sql = "
							SELECT i.cd_eventos_institucionais_inscricao,
								   MD5(i.cd_eventos_institucionais_inscricao::TEXT) AS cd_eventos_institucionais_inscricao_md5,
								   i.nome,
								   i.cd_empresa,
								   i.cd_registro_empregado,
								   i.seq_dependencia,		
								   i.email,
								   e.nome AS evento,
								   funcoes.gera_link('https://www.fundacaofamiliaprevidencia.com.br/evento_certificado.php?i='||MD5(i.cd_eventos_institucionais_inscricao::TEXT),
								                     i.cd_empresa,
								                     i.cd_registro_empregado,
								                     i.seq_dependencia) AS link_certificado
							  FROM projetos.eventos_institucionais_inscricao i
							  JOIN projetos.eventos_institucionais e
								ON e.cd_evento = i.cd_eventos_institucionais
							 WHERE i.dt_exclusao IS NULL
							   AND i.fl_presente = 'S'
							   AND MD5(i.cd_eventos_institucionais_inscricao::TEXT) = '".$_SESSION['ENQ_CD_CHAVE']."'
						  ";
				$ob_resul = pg_query($db, $qr_sql);
				$ar_reg   = pg_fetch_array($ob_resul);		
				if(pg_num_rows($ob_resul))
				{
					if($_SESSION['ENQ_CD_ENQUETE'] == 442)
					{
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
	   cd_empresa,
	   cd_registro_empregado,
	   seq_dependencia,
	   cd_evento,
	   tp_email
	 ) 
VALUES 
	 (
	   CURRENT_TIMESTAMP,
	   'Fundação CEEE ".$ar_reg['evento']."',
	   '".$ar_reg['email']."',     
	   '',
	   '',                      
	   'Certificado: ".$ar_reg['evento']."',
'Prezado(a): ".$ar_reg['nome'].".

Agradecemos a sua participação no ".$ar_reg['evento'].".

Aproveitamos a oportunidade para disponibilizar, no link abaixo, seu certificado de participação:

".$ar_reg['link_certificado']."

Atenciosamente, 

Tchê Previdência
http://www.tcheprevidencia.com.br/
',
	   ".(trim($ar_reg['cd_empresa']) == "" ? "DEFAULT" : intval($ar_reg['cd_empresa'])).",
	   ".(intval($ar_reg['cd_registro_empregado']) == 0 ? "DEFAULT" : intval($ar_reg['cd_registro_empregado'])).",
	   ".(trim($ar_reg['seq_dependencia']) == "" ? "DEFAULT" : intval($ar_reg['seq_dependencia'])).",
		73,
		'F'
		);	
		                          ";							
					}
					else
					{
					
					
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
	   cd_empresa,
	   cd_registro_empregado,
	   seq_dependencia,
	   cd_evento,
	   tp_email
	 ) 
VALUES 
	 (
	   CURRENT_TIMESTAMP,
	   'Fundação CEEE ".$ar_reg['evento']."',
	   '".$ar_reg['email']."',     
	   '',
	   '',                      
	   'Certificado: ".$ar_reg['evento']."',
'Prezado(a): ".$ar_reg['nome'].".

Agradecemos a sua participação no ".$ar_reg['evento'].".

Aproveitamos a oportunidade para disponibilizar, no link abaixo, seu certificado de participação:

".$ar_reg['link_certificado']."

Atenciosamente, 

Fundação Família Previdência
www.fundacaofamiliaprevidencia.com.br
0800 51 2596

**** ATENÇÃO ****
Este e-mail é somente para leitura.
Caso queira falar conosco clique no link abaixo:
http://www.fundacaofamiliaprevidencia.com.br/fale_conosco.php
',
	   ".(trim($ar_reg['cd_empresa']) == "" ? "DEFAULT" : intval($ar_reg['cd_empresa'])).",
	   ".(intval($ar_reg['cd_registro_empregado']) == 0 ? "DEFAULT" : intval($ar_reg['cd_registro_empregado'])).",
	   ".(trim($ar_reg['seq_dependencia']) == "" ? "DEFAULT" : intval($ar_reg['seq_dependencia'])).",
		73,
		'F'
		);	
		                          ";	
					}								  
					@pg_query($db, $qr_sql);
					$_SESSION = Array();
					session_unset("PESQUISA_FCEEE");					
					echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL='.$ar_reg['link_certificado'].'">';
					exit;
				}
			}
			else
			{
				$_SESSION = Array();
				session_unset("PESQUISA_FCEEE");
			}
		}	
		
		$conteudo = str_replace("{LISTA_PERGUNTA}", $lt_pergunta, $conteudo);
		$conteudo = str_replace("{ultima_tela}", 'S', $conteudo);
		$conteudo = str_replace("{fl_nota_cabecalho}", 'display:none;', $conteudo);
		$conteudo = str_replace("{fl_nota_rodape}", 'display:none;', $conteudo);
		$conteudo = str_replace("{fl_continuar}", 'display:none;', $conteudo);
		$conteudo = str_replace("{texto_encerramento}", $texto_encerramento, $conteudo);
		$conteudo = str_replace("{fl_texto_encerramento}", 'display:block;', $conteudo);
	}	
	else 
	{
		#### ABRI VOTAÇÃO ####
		$conteudo = str_replace("{ultima_tela}", 'N', $conteudo);
		$conteudo = str_replace("{fl_continuar}", 'display:block;', $conteudo);
		$conteudo = str_replace("{fl_texto_encerramento}", 'display:none;', $conteudo);	
		
		#### BUSCA DADOS DO AGRUPAMENTO ####
		$qr_sql = " 
					SELECT mostrar_valores, 
						   nota_cabecalho,
						   nota_rodape
					  FROM projetos.enquete_agrupamentos  
					 WHERE cd_enquete     = ".$_SESSION['ENQ_CD_ENQUETE']."
					   AND cd_agrupamento = ".$_REQUEST["agrup"]."
					   AND dt_exclusao    IS NULL 
			      ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		
		$fl_cabecalho = "display:none;";
		if(trim($ar_reg['nota_cabecalho']) != "")
		{
			$fl_cabecalho = "display:block;";
			$conteudo = str_replace("{nota_cabecalho}", $ar_reg['nota_cabecalho'], $conteudo);
		}
		$conteudo = str_replace("{fl_nota_cabecalho}", $fl_cabecalho, $conteudo);		
		
		
		$fl_rodape = "display:none;";
		if(trim($ar_reg['nota_rodape']) != "")
		{
			$fl_rodape = "display:block;";
			$conteudo = str_replace("{nota_rodape}", $ar_reg['nota_rodape'], $conteudo);
		}
		$conteudo = str_replace("{fl_nota_rodape}", $fl_rodape, $conteudo);
		
		$fl_mostrar_valor = $ar_reg['mostrar_valores'];

		#### LISTA PERGUNTA E RESPOSTAS ####
		$qr_sql = "
					SELECT ep.cd_pergunta, 
						   ep.texto AS ds_pergunta, 
						   ep.r1, ep.r2, ep.r3, ep.r4, ep.r5, ep.r6, 
						   ep.r7, ep.r8, ep.r9, ep.r10, ep.r11, ep.r12, ep.r13, 
						   ep.r1_complemento, ep.r2_complemento, ep.r3_complemento, ep.r4_complemento, 
						   ep.r5_complemento, ep.r6_complemento, ep.r7_complemento, ep.r8_complemento, 
						   ep.r9_complemento, ep.r10_complemento, ep.r11_complemento, ep.r12_complemento, ep.r13_complemento,
						   ep.r1_complemento_rotulo, ep.r2_complemento_rotulo, ep.r3_complemento_rotulo, ep.r4_complemento_rotulo, 
						   ep.r5_complemento_rotulo, ep.r6_complemento_rotulo, ep.r7_complemento_rotulo, ep.r8_complemento_rotulo, 
						   ep.r9_complemento_rotulo, ep.r10_complemento_rotulo, ep.r11_complemento_rotulo, ep.r12_complemento_rotulo,						   ep.r13_complemento_rotulo,	
						   ep.r_diss, ep.r_justificativa, 
						   ep.rotulo1, ep.rotulo2, ep.rotulo3, ep.rotulo4, ep.rotulo5, ep.rotulo6, 
						   ep.rotulo7, ep.rotulo8, ep.rotulo9, ep.rotulo10, ep.rotulo11, ep.rotulo12, ep.rotulo13, 
						   ep.rotulo_dissertativa, 
						   ep.rotulo_justificativa, 
						   ep.pergunta_texto,
						   ep.fl_multipla_resposta,
						   ep.qt_multipla_resposta
					  FROM projetos.enquete_perguntas ep 
					 WHERE ep.cd_enquete     = ".$_SESSION['ENQ_CD_ENQUETE']." 
					   AND ep.cd_agrupamento = ".$_REQUEST["agrup"]."
					   AND ep.dt_exclusao    IS NULL 
					 ORDER BY ep.cd_pergunta ";
		$ob_resul = pg_query($db, $qr_sql);
		$modelo_pergunta = '
							<div class="eleicao_fundacao_solidaria_pergunta" id="box_pergunta_{CD_PERGUNTA}">
								<h2 id="P_TEXTO_{CD_PERGUNTA}">{PERGUNTA}</h2>
								<input type="hidden" name="P_{CD_PERGUNTA}" id="P_{CD_PERGUNTA}" value="{QT_MULTIPLA_RESPOSTA}">
								<div style="margin-top:3px; line-height: 16pt;">
									{RESPOSTA}
								</div>
							</div>
						   ';
		$modelo_resposta = '<div id="input_resposta_{CD_PERGUNTA}_{VALOR}"><input name="R_{CD_PERGUNTA}" type="{INPUT_TIPO}" value="{VALOR}" {JS_COMPLEMENTO}>{ROTULO} {COMPLEMENTO}<BR></div>';						   
		$lt_pergunta = "";
		$qt_pergunta = pg_num_rows($ob_resul);
		while ($reg = pg_fetch_array($ob_resul)) 
		{
			if ((trim($reg['pergunta_texto']) == '') and (trim($reg['pergunta_texto']) == ''))
			{
				$lt_pergunta.= $modelo_pergunta;
				
				$lt_pergunta = str_replace("{PERGUNTA}", $reg['ds_pergunta'], $lt_pergunta);
				$lt_pergunta = str_replace("{CD_PERGUNTA}", $reg['cd_pergunta'], $lt_pergunta);
				$lt_pergunta = str_replace("{QT_MULTIPLA_RESPOSTA}", $reg['qt_multipla_resposta'], $lt_pergunta);
				
				$lt_resposta = $modelo_resposta;
				
				$nr_conta = 1;
				while($nr_conta < 14)
				{
					
					if ($reg['r'.$nr_conta] == 'S') 
					{
						if($lt_resposta != $modelo_resposta)
						{
							$lt_resposta.= $modelo_resposta;
						}
						$lt_resposta = str_replace("{INPUT_TIPO}", ($reg['fl_multipla_resposta'] != "S" ? "radio" : "checkbox"), $lt_resposta);
						$lt_resposta = str_replace("{CD_PERGUNTA}", ($reg['fl_multipla_resposta'] != "S" ? $reg['cd_pergunta'] : $reg['cd_pergunta']."[]"), $lt_resposta); 
						$lt_resposta = str_replace("{VALOR}", $nr_conta, $lt_resposta); 
					
						if ($fl_mostrar_valor == 'S') 
						{
							if ($reg['rotulo'.$nr_conta] != '') 
							{
								$lt_resposta = str_replace("{ROTULO}", $reg['rotulo'.$nr_conta], $lt_resposta);
							} 
							else 
							{
								$lt_resposta = str_replace("{ROTULO}", $nr_conta, $lt_resposta);
							}
						}
						else
						{
							$lt_resposta = str_replace("{ROTULO}", "", $lt_resposta);
						}
						
					}
					
					$complemento    = '<span id="CR_'.$reg['cd_pergunta'].'_complemento_'.$nr_conta.'" style="display:none;"><BR>'.$reg['r'.$nr_conta.'_complemento_rotulo'].'<textarea  name="CR_'.$reg['cd_pergunta'].'_complemento_'.$nr_conta.'" rows="2" style="width: 100%;"></textarea></span>';
					$js_complemento = 'onclick="checaComplemento(this,'.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_'.$nr_conta.'\',\''.$reg['r'.$nr_conta.'_complemento'].'\');"';
					
					$lt_resposta = str_replace("{COMPLEMENTO}", $complemento, $lt_resposta);
					$lt_resposta = str_replace("{JS_COMPLEMENTO}", $js_complemento, $lt_resposta);
					
					$nr_conta++;
				}
				
				$lt_pergunta = str_replace("{RESPOSTA}", $lt_resposta, $lt_pergunta);
			}
			else if (trim($reg['pergunta_texto']) != '') 
			{
				$lt_pergunta.= $modelo_pergunta;
				$lt_pergunta = str_replace("{PERGUNTA}", $reg['pergunta_texto'], $lt_pergunta);
				
				$modelo_resposta2 = '<textarea name="R_Texto" style="width:100%" rows="8"></textarea>';
				$lt_resposta = $modelo_resposta2;
				$lt_resposta = str_replace("{CD_PERGUNTA}", $reg['cd_pergunta'], $lt_resposta); 
				
				$lt_pergunta = str_replace("{RESPOSTA}", $lt_resposta, $lt_pergunta);	

				$qt_pergunta--;
			}
		}
		
		$conteudo = str_replace("{qt_pergunta}", $qt_pergunta, $conteudo);
		$conteudo = str_replace("{LISTA_PERGUNTA}", $lt_pergunta, $conteudo);
	}
	
	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();
?>