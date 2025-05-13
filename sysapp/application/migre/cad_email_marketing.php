<?php
	exit;

   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');

	$tpl = new TemplatePower('tpl/tpl_cad_email_marketing.html');
	$tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');

	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->assign( "site_url", site_url());
	$tpl->newBlock('cadastro');
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);
	if (isset($c))	
	{

		$sql =        " select 	count(*) as num_regs 
						from 	projetos.envia_emails 
						where 	cd_divulgacao   = $c ";
		$rs = pg_exec($db, $sql);
		$reg = pg_fetch_array($rs);
		if ($reg['num_regs'] == 0) {
			$tpl->assign('situacao', '');
		} else {
			$tpl->assign('situacao', $reg['num_regs'].' emails enviados.');
		}
		$sql =        " select cd_divulgacao, assunto, conteudo,  ";
		$sql = $sql . "        to_char(dt_divulgacao, 'DD/MM/YYYY') as data_inc, ";
		$sql = $sql . "        cd_usuario, arquivo_associado, email_avulsos, remetente, url_link ";
		$sql = $sql . " from   projetos.divulgacao ";
		$sql = $sql . " where  cd_divulgacao   = $c ";
//		echo $sql;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		
		$tpl->assign('cd_divulgacao', $reg['cd_divulgacao']);
		$tpl->assign('assunto', $reg['assunto']);
		$tpl->assign('conteudo', $reg['conteudo']);
		$tpl->assign('arquivo_associado', $reg['arquivo_associado'] );
		$tpl->assign('dt_inclusao', $reg['data_inc']);
		$tpl->assign('emails_outros', $reg['email_avulsos']);
		$tpl->assign('remetente', $reg['remetente']);
		$tpl->assign('url_link', $reg['url_link']);
		$cd_divulgacao = $reg['cd_divulgacao'];
	}
	else 
	{
		if ($pesq != '') 
		{
			$sql = "select titulo, texto_abertura,
					to_char(dt_inicio, 'dd/mm/yyyy hh24:mi') as dt_inicio,
					to_char(dt_fim, 'dd/mm/yyyy hh24:mi') as dt_fim
				from projetos.enquetes where cd_enquete = " .$pesq;
			$rs = pg_exec($db, $sql);
			$reg = pg_fetch_array($rs);
			$titulo = $reg['titulo'];
			$tpl->assign('assunto', $reg['titulo']);
			$tpl->assign('conteudo', $reg['titulo'].chr(10).
				'------------------------------'.chr(10).
				$reg['texto_abertura'].chr(10).
				'------------------------------'.chr(10).
				'Período desta pesquisa: De '.$reg['dt_inicio'].' à '.$reg['dt_fim'].chr(10).
				'------------------------------'.chr(10).
				'Link para pesquisa: http://www.e-prev.com.br/controle_projetos/resp_enquetes_capa.php?c='.$pesq.chr(10).
				'------------------------------'.chr(10).
				'Em caso de dúvidas ao acessar este link, entre em contato com nosso suporte técnico.'.chr(10).
				'------------------------------');
		}
		

		#### PEGA PROXIMO ID PARA DIVULGACAO ####
		$sql = "
				SELECT nextval(('projetos.divulgacao_cd_divulgacao_seq'::text)::regclass) AS cd_divulgacao
			   ";
		$rs = pg_query($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('cd_divulgacao', $reg['cd_divulgacao']);
		$date = date("d/m/Y");
		$tpl->assign('dt_inclusao',  $date);
	
		
		#### CHAMADO PELO CONTROLE DE EXTRATOS NO ORACLE/FORMS ####
		#http://10.63.255.150/cieprev/sysapp/application/migre/cad_email_marketing.php?&op=I&fl_extrato=S&dt_mes=09/2010&dt_envio=19/10/2010&cd_emp=9
		if	(
				($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") 
				and 
				(
					(intval($_REQUEST['cd_emp']) == 7)
					or
					(intval($_REQUEST['cd_emp']) == 8)
					or
					(intval($_REQUEST['cd_emp']) == 10)
					or
					(intval($_REQUEST['cd_emp']) == 19)					
				)
			)
		{
			$plano_descricao = "";
			switch (intval($_REQUEST['cd_emp']))
			{
				case 7 : $empresa_assunto = "SENGE: "; $plano_descricao = "SENGE Previdência"; break;
				case 8 : $empresa_assunto = "SINPRORS: "; $plano_descricao = "SINPRORS Previdência"; break;
				case 10 : $empresa_assunto = "SINTAE: "; $plano_descricao = "SINPRORS Previdência"; break;
				case 19 : $empresa_assunto = "AFCEEE: "; $plano_descricao = "Família Previdência"; break;
			}
			
			
			$tpl->assignGlobal('fl_enviar', "checked");
			$tpl->assign('url_link', "http://www.fundacaoceee.com.br/auto_atendimento_extratos.php?_p=[RE_CRIPTO]");
			$tpl->assign('assunto', $empresa_assunto.": Extrato ".$_REQUEST['dt_mes']);
			$tpl->assign('conteudo',"Prezado(a): [NOME]".chr(10).chr(10).
									"Está disponível o extrato do mês de ".$_REQUEST['dt_mes']." da sua conta do plano ".$plano_descricao." no autoatendimento.".chr(10).chr(10).
									"Para acessá-lo, clique no link abaixo ou pelo autoatendimento.".chr(10).chr(10).
									"[LINK_1]".chr(10).chr(10).
									"Colocamo-nos à disposição para quaisquer esclarecimentos através do 0800512596.".chr(10).chr(10).
									"Atenciosamente,".chr(10).chr(10).
									"Fundação CEEE de Seguridade Social".chr(10).chr(10).chr(10).
									"---------------------------------------------------------".chr(10).
									"**** ATENÇÃO ****".chr(10).
									"Este e-mail é somente para leitura.".chr(10).
									"Caso queira falar conosco clique no link abaixo:".chr(10).
									"http://www.fundacaoceee.com.br/fale_conosco.php");		
		}
		
		
		if(($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") and ($_REQUEST['cd_emp'] == 9))
		{
			#FUNDACAO CEEE
			$tpl->assignGlobal('fl_enviar', "checked");
			$tpl->assign('url_link', "http://www.fundacaoceee.com.br/auto_atendimento_extratos.php?_p=[RE_CRIPTO]");
			$tpl->assign('assunto', "Fundação CEEE: Extrato ".$_REQUEST['dt_mes']);
			$tpl->assign('conteudo',"Prezado(a): [NOME]".chr(10).chr(10).
									"Está disponível o extrato da sua conta do plano CeeePrev no autoatendimento.".chr(10).chr(10).
									"Para acessá-lo, clique no link abaixo ou pelo autoatendimento.".chr(10).chr(10).
									"[LINK_1]".chr(10).chr(10).
									"Colocamo-nos à disposição para quaisquer esclarecimentos através do 0800512596.".chr(10).chr(10).
									"Atenciosamente,".chr(10).chr(10).
									"Fundação CEEE de Seguridade Social".chr(10).chr(10).chr(10).
									"---------------------------------------------------------".chr(10).
									"**** ATENÇÃO ****".chr(10).
									"Este e-mail é somente para leitura.".chr(10).
									"Caso queira falar conosco clique no link abaixo:".chr(10).
									"http://www.fundacaoceee.com.br/fale_conosco.php");										
		}	

		if(($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") and ($_REQUEST['cd_emp'] == 6))
		{
			#CRM
			$tpl->assignGlobal('fl_enviar', "checked");
			$tpl->assign('url_link', "http://www.fundacaoceee.com.br/auto_atendimento_extratos.php?_p=[RE_CRIPTO]");
			$tpl->assign('assunto', "CRM: Extrato ".$_REQUEST['dt_mes']);
			$tpl->assign('conteudo',"Prezado(a): [NOME]".chr(10).chr(10).
									"Está disponível o extrato da sua conta do plano CRMPrev no autoatendimento.".chr(10).chr(10).
									"Para acessá-lo, clique no link abaixo ou pelo autoatendimento.".chr(10).chr(10).
									"[LINK_1]".chr(10).chr(10).
									"Colocamo-nos à disposição para quaisquer esclarecimentos através do 0800512596.".chr(10).chr(10).
									"Atenciosamente,".chr(10).chr(10).
									"Fundação CEEE de Seguridade Social".chr(10).chr(10).chr(10).
									"---------------------------------------------------------".chr(10).
									"**** ATENÇÃO ****".chr(10).
									"Este e-mail é somente para leitura.".chr(10).
									"Caso queira falar conosco clique no link abaixo:".chr(10).
									"http://www.fundacaoceee.com.br/fale_conosco.php");										
	
		}		
		
		if(($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") and ($_REQUEST['cd_emp'] == 0))
		{
			#GRUPO CEEE - CEEEPREV
			$tpl->assignGlobal('fl_enviar', "checked");
			$tpl->assign('url_link', "http://www.fundacaoceee.com.br/auto_atendimento_extratos.php?_p=[RE_CRIPTO]");
			$tpl->assign('assunto', "Grupo CEEE: Extrato ".$_REQUEST['dt_mes']);
			$tpl->assign('conteudo',"Prezado(a): [NOME]".chr(10).chr(10).
									"Está disponível o extrato da sua conta do plano CeeePrev no autoatendimento.".chr(10).chr(10).
									"Para acessá-lo, clique no link abaixo ou pelo autoatendimento.".chr(10).chr(10).
									"[LINK_1]".chr(10).chr(10).
									"Colocamo-nos à disposição para quaisquer esclarecimentos através do 0800512596.".chr(10).chr(10).
									"Atenciosamente,".chr(10).chr(10).
									"Fundação CEEE de Seguridade Social".chr(10).chr(10).chr(10).
									"---------------------------------------------------------".chr(10).
									"**** ATENÇÃO ****".chr(10).
									"Este e-mail é somente para leitura.".chr(10).
									"Caso queira falar conosco clique no link abaixo:".chr(10).
									"http://www.fundacaoceee.com.br/fale_conosco.php");										
	
		}		
	}

	if ($op == 'A') 
	{
		$n = 'U';
	}
	else 
	{
		$n = 'I';
	}
	$tpl->assign('insere', $n);
//------------------------------------------------ Publicações:
	$sql =        " select 	cd_publicacao, nome_publicacao ";
	$sql = $sql . " from   	projetos.publicacoes where cd_publicacao <> 1";
	$sql = $sql . " order 	by nome_publicacao ";
	$rs = pg_exec($db, $sql);
	while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('publicacao');
		$tpl->assign('cd_publicacao', $reg['cd_publicacao']);
		$tpl->assign('nome_publicacao', $reg['nome_publicacao']);
		if (isset($c)) 
		{
			$sql2 =			" select * from   projetos.divulgacoes_publicacoes ";
			$sql2 = $sql2 . " where cd_divulgacao = " . $cd_divulgacao ;
			$sql2 = $sql2 . " 	and cd_publicacao = " . $reg['cd_publicacao'];
			$rs2 = pg_exec($db, $sql2);
			if (pg_fetch_array($rs2)) { $tpl->assign('publicacao_checked', 'checked'); }
		}
		else if(($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I"))
		{
			#### CHAMADO PELO CONTROLE DE EXTRATOS NO ORACLE/FORMS ####
			if($reg['cd_publicacao'] == 2) //EXTRATO
			{
				$tpl->assign('publicacao_checked', 'checked');
			}
		}
	}

	
	#### GRUPOS PARA ENVIO ####
	$qr_sql = "
				SELECT dg.cd_divulgacao_grupo,
				       dg.ds_divulgacao_grupo,
					   dg.cd_lista,
				       CASE WHEN dgs.cd_divulgacao_grupo IS NOT NULL 
					        THEN 'S' 
							ELSE 'N' 
					   END AS fl_marcado,
					   COALESCE(dgt.qt_registro,0) AS qt_registro
				  FROM projetos.divulgacao_grupo dg
				  LEFT JOIN projetos.divulgacao_grupo_total dgt
				    ON dgt.cd_divulgacao_grupo = dg.cd_divulgacao_grupo
				  LEFT JOIN projetos.divulgacao_grupo_selecionado dgs
				    ON dgs.cd_divulgacao_grupo = dg.cd_divulgacao_grupo
				   AND dgs.cd_divulgacao       = ".intval($cd_divulgacao)."
				   AND dgs.dt_exclusao IS NULL
				 WHERE dg.dt_exclusao IS NULL
				 ORDER BY ds_divulgacao_grupo
	          ";
	$ob_resul = pg_query($db, $qr_sql);
	while($ar_pub = pg_fetch_array($ob_resul))
	{
		$tpl->newBlock('publico');
		$tpl->assign('cd_publico',   $ar_pub['cd_divulgacao_grupo']);
		$tpl->assign('nome_publico', $ar_pub['ds_divulgacao_grupo']);
		$tpl->assign('qt_registro_grupo', $ar_pub['qt_registro']);
		$tpl->assign('publico_checked', ($ar_pub['fl_marcado'] == "S" ? "checked" : ""));
		
		if(($ar_pub['cd_lista'] == 'EX07') and ($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") and ($_REQUEST['cd_emp'] == 7))
		{
			#### CHAMADO PELO CONTROLE DE EXTRATOS NO ORACLE/FORMS ####
			#### SENGE ####
			$tpl->assign('publico_checked', 'checked');
		}
		
		if(($ar_pub['cd_lista'] == 'EX08') and ($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") and ($_REQUEST['cd_emp'] == 8))
		{
			#### CHAMADO PELO CONTROLE DE EXTRATOS NO ORACLE/FORMS ####
			#### SINPRORS ####
			$tpl->assign('publico_checked', 'checked');
		}
		
		if(($ar_pub['cd_lista'] == 'EX10') and ($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") and ($_REQUEST['cd_emp'] == 10))
		{
			#### CHAMADO PELO CONTROLE DE EXTRATOS NO ORACLE/FORMS ####
			#### SINTAE ####
			$tpl->assign('publico_checked', 'checked');
		}	
		
		if(($ar_pub['cd_lista'] == 'EX19') and ($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") and ($_REQUEST['cd_emp'] == 19))
		{
			#### CHAMADO PELO CONTROLE DE EXTRATOS NO ORACLE/FORMS ####
			#### FAMILIA ####
			$tpl->assign('publico_checked', 'checked');
		}
		
		if(($ar_pub['cd_lista'] == 'EX09') and ($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") and ($_REQUEST['cd_emp'] == 9))
		{
			#### CHAMADO PELO CONTROLE DE EXTRATOS NO ORACLE/FORMS ####
			#### FUNDAÇÃO CEEE - CEEEPREV ####
			$tpl->assign('publico_checked', 'checked');
		}	
		
		if(($ar_pub['cd_lista'] == 'EX06') and ($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") and ($_REQUEST['cd_emp'] == 6))
		{
			#### CHAMADO PELO CONTROLE DE EXTRATOS NO ORACLE/FORMS ####
			#### CRM ####
			$tpl->assign('publico_checked', 'checked');
		}		
		
		if(($ar_pub['cd_lista'] == 'EX00') and ($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") and ($_REQUEST['cd_emp'] == 0))
		{
			#### CHAMADO PELO CONTROLE DE EXTRATOS NO ORACLE/FORMS ####
			#### GRUPO CEEE - CEEEPREV ####
			$tpl->assign('publico_checked', 'checked');
		}	
		
		if (($pesq != '') and ($ar_pub['cd_lista'] == 'CS1X')) 
		{
			#### ENQUETE ####
			$tpl->assign('publico_checked', 'checked');
		}
		
	}
	
	
	
	
	/*
	$sql =        " select codigo, descricao, divisao, valor ";
	$sql = $sql . " from   listas where categoria = 'PACS' ";
	$sql = $sql . " and 	tipo <> 'V' and dt_exclusao IS NULL";
	$sql = $sql . " order 	by descricao ";
	$rs = pg_exec($db, $sql);
	while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('publico');
		$tpl->assign('cd_publico', $reg['codigo']);
		$tpl->assign('nome_publico', $reg['descricao']);
		
		if (isset($c)) 
		{
			$sql2 =			" select * from   projetos.divulgacoes_publicos ";
			$sql2 = $sql2 . " where cd_divulgacao = " . intval($cd_divulgacao) ;
			$sql2 = $sql2 . " 	and cd_publico = '" . $reg['codigo'] . "'";
			$rs2 = pg_exec($db, $sql2);
			if (pg_fetch_array($rs2)) { $tpl->assign('publico_checked', 'checked'); }
		} 
		else if(($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") and ($_REQUEST['cd_emp'] == 7))
		{
			#### CHAMADO PELO CONTROLE DE EXTRATOS NO ORACLE/FORMS ####
			#### SENGE ####
			if($reg['codigo'] == 'EX07')
			{
				$tpl->assign('publico_checked', 'checked');
			}
		}
		else if(($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") and ($_REQUEST['cd_emp'] == 8))
		{
			#### CHAMADO PELO CONTROLE DE EXTRATOS NO ORACLE/FORMS ####
			#### SINPRORS ####
			if($reg['codigo'] == 'EX08')
			{
				$tpl->assign('publico_checked', 'checked');
			}
		}
		else if(($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") and ($_REQUEST['cd_emp'] == 10))
		{
			#### CHAMADO PELO CONTROLE DE EXTRATOS NO ORACLE/FORMS ####
			#### SINTAE ####
			if($reg['codigo'] == 'EX10')
			{
				$tpl->assign('publico_checked', 'checked');
			}
		}	
		else if(($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") and ($_REQUEST['cd_emp'] == 19))
		{
			#### CHAMADO PELO CONTROLE DE EXTRATOS NO ORACLE/FORMS ####
			#### FAMILIA ####
			if($reg['codigo'] == 'EX19')
			{
				$tpl->assign('publico_checked', 'checked');
			}
		}
		else if(($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") and ($_REQUEST['cd_emp'] == 9))
		{
			#### CHAMADO PELO CONTROLE DE EXTRATOS NO ORACLE/FORMS ####
			#### FUNDAÇÃO CEEE - CEEEPREV ####
			if($reg['codigo'] == 'EX09')
			{
				$tpl->assign('publico_checked', 'checked');
			}
		}	
		else if(($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") and ($_REQUEST['cd_emp'] == 6))
		{
			#### CHAMADO PELO CONTROLE DE EXTRATOS NO ORACLE/FORMS ####
			#### CRM ####
			if($reg['codigo'] == 'EX06')
			{
				$tpl->assign('publico_checked', 'checked');
			}
		}		
		else if(($_REQUEST['fl_extrato'] == "S") and ($_REQUEST['op'] == "I") and ($_REQUEST['cd_emp'] == 0))
		{
			#### CHAMADO PELO CONTROLE DE EXTRATOS NO ORACLE/FORMS ####
			#### GRUPO CEEE - CEEEPREV ####
			if($reg['codigo'] == 'EX00')
			{
				$tpl->assign('publico_checked', 'checked');
			}
		}			
		else 
		{
			if (($pesq != '') and ($reg['codigo'] == 'CS1X')) 
			{
				$tpl->assign('publico_checked', 'checked');
			}
		}
	}
	*/
	
//----------------------------------------------- Cidades:
	/*
	$sql = "select distinct (cidade), unidade_federativa from participantes where cidade is not null group by cidade, unidade_federativa order by cidade, unidade_federativa";
	$rs = pg_exec($db, $sql);
	$tpl->newBlock('cidade');
	$tpl->assign('estado', '');
	$tpl->assign('nome_cidade', '-');

	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('cidade');
		$tpl->assign('estado', $reg['unidade_federativa']);
		$tpl->assign('nome_cidade', $reg['cidade']);
	}
//------------------------------------------------ Publicos EXCLUSIVOS:
	$sql =        " select codigo, descricao, divisao, valor ";
	$sql = $sql . " from   listas where categoria = 'PACS' ";
	$sql = $sql . " and 	tipo = 'V' AND dt_exclusao is null";
	$sql = $sql . " order 	by descricao ";
	$rs = pg_exec($db, $sql);
	while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('publico_exclusivo');
		$tpl->assign('cd_publico', $reg['codigo']);
		$tpl->assign('nome_publico', $reg['descricao']);
		if (isset($c)) {
			$sql2 =			" select * from   projetos.divulgacoes_publicos ";
			$sql2 = $sql2 . " where cd_divulgacao = " . intval($cd_divulgacao) ;
			$sql2 = $sql2 . " 	and cd_publico = '" . $reg['codigo'] . "'";
			$rs2 = pg_exec($db, $sql2);
			if (pg_fetch_array($rs2)) { $tpl->assign('publico_checked', 'checked'); }
		}
	}
	*/
// --------------------------------------------------------------

	if(trim($_REQUEST['c']) != "")
	{
		#### QUANTIDADE DE EMAILS ENVIADOS ####
		$qr_sql = " 
					SELECT COUNT(*) AS qt_email
					  FROM projetos.envia_emails ee
					 WHERE ee.cd_divulgacao = ".$_REQUEST['c']."
					   AND ee.fl_retornou <> 'S'
				  ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg_env = pg_fetch_array($ob_resul);
		$tpl->newBlock('qt_email_enviado');
		$tpl->assign('qt_email_enviado', $ar_reg_env['qt_email']);
		
		
		#### QUANTIDADE DE EMAILS NÃO ENVIADOS ####
		$qr_sql = " 
					SELECT COUNT(*) AS qt_email
					  FROM projetos.envia_emails ee
					 WHERE ee.cd_divulgacao = ".$_REQUEST['c']."
					   AND ee.fl_retornou = 'S'
				  ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg_env_nao = pg_fetch_array($ob_resul);
		$tpl->newBlock('qt_email_nao_enviado');
		$tpl->assign('qt_email_nao_enviado', $ar_reg_env_nao['qt_email']);

		$tpl->newBlock('qt_email');
		$tpl->assign('qt_email', $ar_reg_env['qt_email'] + $ar_reg_env_nao['qt_email']);			  
	}
	
	
	#### CHAMADO PELO CONTROLE DE EXTRATOS NO ORACLE/FORMS ####
	if(trim($_REQUEST['dt_envio']) != "")
	{
		#http://10.63.255.222/controle_projetos/cad_email_marketing.php?&op=I&fl_extrato=S&dt_mes=09/2010&dt_envio=19/10/2010&cd_emp=9
		$qr_sql = "
					SELECT TO_CHAR((CASE WHEN TO_DATE('".trim($_REQUEST['dt_envio'])."', 'DD/MM/YYYY') <= CURRENT_DATE 
										 THEN CURRENT_DATE + '1 day'::INTERVAL
										 ELSE TO_DATE('".trim($_REQUEST['dt_envio'])."', 'DD/MM/YYYY')
									END),'DD/MM/YYYY') AS dt_envio					
		          ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);				  
		$tpl->assignGlobal('dt_envio', $ar_reg['dt_envio']);	
	}
	
	pg_close($db);
	$tpl->printToScreen();
?>