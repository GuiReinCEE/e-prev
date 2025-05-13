<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	header( 'location:'.base_url().'index.php/gestao/nc' );
	
	$tpl = new TemplatePower('tpl/tpl_lst_nao_conf.html');
	$tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');

	$tpl->prepare();
	$tpl->assign('n', $n);

	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

	$filtro_cd_processo = 0;
	if(isset($_POST['cd_processo']))
	{
		$filtro_cd_processo = intval($_POST['cd_processo']);
	}

	$filtro_dt_limite_apres_inicio = '';
	if(isset($_POST['dt_limite_apres_inicio']))
	{
		$filtro_dt_limite_apres_inicio = $_POST['dt_limite_apres_inicio'];
	}
	$filtro_dt_limite_apres_fim = '';
	if(isset($_POST['dt_limite_apres_fim']))
	{
		$filtro_dt_limite_apres_fim = $_POST['dt_limite_apres_fim'];
	}


	$filtro_dt_encerramento_inicio = '';
	if(isset($_POST['dt_encerramento_inicio']))
	{
		$filtro_dt_encerramento_inicio = $_POST['dt_encerramento_inicio'];
	}

	$filtro_dt_encerramento_fim = '';
	if(isset($_POST['dt_encerramento_fim']))
	{
		$filtro_dt_encerramento_fim = $_POST['dt_encerramento_fim'];
	}

	$filtro_dt_proposta_inicio = '';
	if(isset($_POST['dt_proposta_inicio']))
	{
		$filtro_dt_proposta_inicio = $_POST['dt_proposta_inicio'];
	}

	$filtro_dt_proposta_fim = '';
	if(isset($_POST['dt_proposta_fim']))
	{
		$filtro_dt_proposta_fim = $_POST['dt_proposta_fim'];
	}

	$filtro_dt_prorrogada_inicio = '';
	if(isset($_POST['dt_prorrogada_inicio']))
	{
		$filtro_dt_prorrogada_inicio = $_POST['dt_prorrogada_inicio'];
	}

	$filtro_dt_prorrogada_fim = '';
	if(isset($_POST['dt_prorrogada_fim']))
	{
		$filtro_dt_prorrogada_fim = $_POST['dt_prorrogada_fim'];
	}

	###################### INICIALIZA VARIAVEIS DO FILTRO #######################
	if((trim($_SESSION['lst_nao_conf_cbo_diretoria']) == "") and (trim($_POST['cbo_diretoria']) == ""))
	{
		$_POST['cbo_diretoria'] = -1;
	}
	if((trim($_SESSION['lst_nao_conf_cbo_divisao']) == "") and (trim($_POST['cbo_divisao']) == ""))
	{
		$_POST['cbo_divisao'] = -1;
	}	
	if((trim($_SESSION['lst_nao_conf_cbo_status']) == "") and (trim($_POST['cbo_status']) == ""))
	{
		$_POST['cbo_status'] = -1;
	}
	if((trim($_SESSION['lst_nao_conf_cbo_implementada']) == "") and (trim($_POST['cbo_implementada']) == ""))
	{
		$_POST['cbo_implementada'] = -1;
	}

	######################## VERIFICA AVISO ####################################
	$qr_select = "
		SELECT COUNT(cd_nao_conformidade) AS nr_aviso
		FROM projetos.aviso_evento_nc 
		WHERE cd_evento = 5 
		AND cd_nao_conformidade NOT IN (SELECT cd_acao FROM projetos.acao_corretiva)
	";
	$ob_resul = pg_query($qr_select);
	$ar_reg   = pg_fetch_array($ob_resul);
	if ($ar_reg['nr_aviso'] != 0) 
	{
		$tpl->assign('som', 'sons/plang.wav');
	}

	######################## FILTROS DATAS ##############################
	$tpl->assign('dt_limite_apres_inicio', $filtro_dt_limite_apres_inicio);
	$tpl->assign('dt_limite_apres_fim', $filtro_dt_limite_apres_fim);

	$tpl->assign('dt_encerramento_inicio', $filtro_dt_encerramento_inicio);
	$tpl->assign('dt_encerramento_fim', $filtro_dt_encerramento_fim);

	$tpl->assign('dt_proposta_inicio', $filtro_dt_proposta_inicio);
	$tpl->assign('dt_proposta_fim', $filtro_dt_proposta_fim);

	$tpl->assign('dt_prorrogada_inicio', $filtro_dt_prorrogada_inicio);
	$tpl->assign('dt_prorrogada_fim', $filtro_dt_prorrogada_fim);

	######################## FILTRO COMBO DIRETORIA ######################
	$qr_select = "
		SELECT DISTINCT(area) AS cd_diretoria, 
		area AS ds_diretoria
		FROM projetos.divisoes
		WHERE area IS NOT NULL
		OR TRIM(area) <> ''
	";
	$ob_resul = pg_query($qr_select);
	while ($ar_reg = pg_fetch_array($ob_resul))
	{
		$tpl->newBlock('cbo_diretoria');
		$tpl->assign('cd_diretoria', $ar_reg['cd_diretoria']);	
		$tpl->assign('ds_diretoria', $ar_reg['ds_diretoria']);
		$tpl->assign('chk_diretoria', '');

		if(trim($_POST['cbo_diretoria']) == "")
		{
			$_POST['cbo_diretoria'] = $_SESSION['lst_nao_conf_cbo_diretoria'];
		}
		else
		{
			$_SESSION['lst_nao_conf_cbo_diretoria'] = $_POST['cbo_diretoria'];
		}

		if(trim($_POST['cbo_diretoria']) == $ar_reg['cd_diretoria']) 
		{
			$tpl->assign('chk_diretoria', 'selected');	
		}		
	}

	######################## FILTRO COMBO DIVISAO ##############################
	$qr_select = "
		SELECT DISTINCT(codigo) AS cd_divisao,
		nome AS ds_divisao
		FROM projetos.divisoes
	";
	if(trim($_POST['cbo_diretoria']) != "") 
	{
		$qr_select .= " WHERE area = '" . $_POST['cbo_diretoria'] . "'";
	}
	$ob_resul = pg_query($qr_select);
	while ($ar_reg = pg_fetch_array($ob_resul))
	{
		$tpl->newBlock('cbo_divisao');
		$tpl->assign('cd_divisao', $ar_reg['cd_divisao']);	
		$tpl->assign('ds_divisao', $ar_reg['ds_divisao']);	
		$tpl->assign('chk_divisao', '');

		if(trim($_POST['cbo_divisao']) == "")
		{
			$_POST['cbo_divisao'] = $_SESSION['lst_nao_conf_cbo_divisao'];
		}
		else
		{
			$_SESSION['lst_nao_conf_cbo_divisao'] = $_POST['cbo_divisao'];
		}		

		if(trim($_POST['cbo_divisao']) == $ar_reg['cd_divisao']) 
		{
			$tpl->assign('chk_divisao', 'selected');	
		}
	}

	######################## FILTRO COMBO STATUS #######################
	$tpl->newBlock('cbo_status');
	$tpl->assign('cd_status', 'EN');	
	$tpl->assign('ds_status', 'Encerradas');	
	$tpl->assign('chk_status', '');
	
	if(trim($_POST['cbo_status']) == "")
	{
		$_POST['cbo_status'] = $_SESSION['lst_nao_conf_cbo_status'];
	}
	else
	{
		$_SESSION['lst_nao_conf_cbo_status'] = $_POST['cbo_status'];
	}
	
	if(trim($_POST['cbo_status']) == 'EN')
	{
		$tpl->assign('chk_status', 'selected');	
	}
	
	$tpl->newBlock('cbo_status');
	$tpl->assign('cd_status', 'NE');	
	$tpl->assign('ds_status', 'Não Encerradas');	
	$tpl->assign('chk_status', '');	
	if(trim($_POST['cbo_status']) == 'NE') 
	{
		$tpl->assign('chk_status', 'selected');	
	}


	######################## FILTRO COMBO IMPLEMENTADA ###############################
	$tpl->newBlock('cbo_implementada');
	$tpl->assign('cd_implementada', 'S');	
	$tpl->assign('ds_implementada', 'Sim');	
	$tpl->assign('chk_implementada', '');	
	if(trim($_POST['cbo_implementada']) == "")
	{
		$_POST['cbo_implementada'] = $_SESSION['lst_nao_conf_cbo_implementada'];
	}
	else
	{
		$_SESSION['lst_nao_conf_cbo_implementada'] = $_POST['cbo_implementada'];
	}	
	
	if(trim($_POST['cbo_implementada']) == 'S')
	{
		$tpl->assign('chk_implementada', 'selected');	
	}
	
	$tpl->newBlock('cbo_implementada');
	$tpl->assign('cd_implementada', 'N');	
	$tpl->assign('ds_implementada', 'Não');	
	$tpl->assign('chk_implementada', '');	
	if(trim($_POST['cbo_implementada']) == 'N') 
	{
		$tpl->assign('chk_implementada', 'selected');	
	}	
	
	######################## FILTRO COMBO PRORROGADA ###############################
	$tpl->newBlock('cbo_prorrogada');
	$tpl->assign('cd_prorrogada', 'S');	
	$tpl->assign('ds_prorrogada', 'Sim');	
	$tpl->assign('chk_prorrogada', '');	
	if(trim($_POST['cbo_prorrogada']) == "")
	{
		$_POST['cbo_prorrogada'] = $_SESSION['lst_nao_conf_cbo_prorrogada'];
	}
	else
	{
		$_SESSION['lst_nao_conf_cbo_prorrogada'] = $_POST['cbo_prorrogada'];
	}	
	
	if(trim($_POST['cbo_prorrogada']) == 'S')
	{
		$tpl->assign('chk_prorrogada', 'selected');	
	}	
	
	$tpl->newBlock('cbo_prorrogada');
	$tpl->assign('cd_prorrogada', 'N');	
	$tpl->assign('ds_prorrogada', 'Não');	
	$tpl->assign('chk_prorrogada', '');	
	if(trim($_POST['cbo_prorrogada']) == 'N') 
	{
		$tpl->assign('chk_prorrogada', 'selected');	
	}

	######################## FILTRO COMBO PROCESSOS ###############################
	$result = pg_query($db, 'SELECT * FROM projetos.processos ORDER BY ordem;');
	$processos = pg_fetch_all($result);

	foreach($processos as $processo)
	{
		$tpl->newBlock('cbo_processo');
		$tpl->assign('cd_processo', $processo['cd_processo']);
		$tpl->assign('ds_processo', $processo['procedimento']);

		if( intval($processo['cd_processo'])==intval($filtro_cd_processo) )
		{
			$tpl->assign('chk_processo', 'selected');
		}
	}

	########################## LISTA NÃO CONFORMIDADES ##########################
	$qr_select = "  
					SELECT TO_CHAR(nc.dt_cadastro,'DD/MM/YYYY') AS dt_inclusao, 
					        TO_CHAR(nc.data_fechamento,'DD/MM/YYYY') AS dt_encerramento,
							TO_CHAR(ac.dt_efe_imp,'DD/MM/YYYY') AS dt_efe_imp,
					        TO_CHAR(ac.dt_prop_imp,'DD/MM/YYYY') AS dt_prop_imp,
					        TO_CHAR(ac.dt_prorrogada,'DD/MM/YYYY') AS dt_prorrogada,
							TO_CHAR(COALESCE(ac.dt_limite_apres,(nc.dt_cadastro + '15 days'::interval)),'DD/MM/YYYY') AS dt_limite_apres,
							TO_CHAR(COALESCE(ac.dt_apres),'DD/MM/YYYY') AS dt_apres,
					        nc.cd_processo AS processo,                  
				            pp.procedimento AS procedimento,              
				            nc.cd_nao_conformidade AS cod_nao_conf,              
				            nc.descricao AS descricao,                 
				            nc.cd_responsavel AS cod_responsavel,           
				            nc.numero_cad_nc AS numero_cad_nc,
							

				            (SELECT nome
		                       FROM projetos.usuarios_controledi
		                      WHERE codigo = nc.aberto_por) AS nome_aberto_por,
		                  	(SELECT nome
		                       FROM projetos.usuarios_controledi
		                      WHERE codigo = nc.cd_responsavel) AS nome_responsavel,
		                    (SELECT COUNT(cd_nao_conformidade)
		                       FROM projetos.aviso_evento_nc
		                      WHERE cd_evento           = 5
		                        AND cd_nao_conformidade NOT IN ( SELECT cd_acao FROM projetos.acao_corretiva )
		                        AND cd_nao_conformidade = nc.cd_nao_conformidade) AS nr_aviso

				       FROM projetos.nao_conformidade nc
				       JOIN projetos.processos pp 
					     ON nc.cd_processo = pp.cd_processo
				       LEFT JOIN projetos.acao_corretiva ac
				         ON ac.cd_processo         = nc.cd_processo
				        AND ac.cd_nao_conformidade = nc.cd_nao_conformidade
				      WHERE 1=1
	";

	//FILTRA POR DATAS
	if (($filtro_dt_limite_apres_inicio != "" ) and ($filtro_dt_limite_apres_fim != ""))
	{
		$qr_select.= " AND DATE_TRUNC('DAY', COALESCE(ac.dt_limite_apres,(nc.dt_cadastro + '15 days'::interval))) BETWEEN TO_DATE('" . pg_escape_string($filtro_dt_limite_apres_inicio) . "', 'DD/MM/YYYY') AND TO_DATE('" . pg_escape_string($filtro_dt_limite_apres_fim) . "', 'DD/MM/YYYY')";
	}

	if( $filtro_dt_encerramento_inicio!="" )
	{
		$qr_select.= " AND DATE_TRUNC('DAY', nc.data_fechamento) >= TO_DATE('" . pg_escape_string($filtro_dt_encerramento_inicio) . "', 'DD/MM/YYYY') ";
	}
	if( $filtro_dt_encerramento_fim!="" )
	{
		$qr_select.= " AND DATE_TRUNC('DAY', nc.data_fechamento) <= TO_DATE('" . pg_escape_string($filtro_dt_encerramento_fim) . "', 'DD/MM/YYYY') ";
	}

	if(($filtro_dt_proposta_inicio != "") and ($filtro_dt_proposta_inicio != "" ))
	{
		$qr_select.= " AND DATE_TRUNC('DAY', COALESCE(ac.dt_prorrogada, ac.dt_prop_imp)) BETWEEN TO_DATE('" . pg_escape_string($filtro_dt_proposta_inicio) . "', 'DD/MM/YYYY') AND TO_DATE('" . pg_escape_string($filtro_dt_proposta_fim) . "', 'DD/MM/YYYY')";
	}

	//FILTRA POR DIRETORIA PELO RESPONSAVEL, CASO NÃO TENHA RESPONSAVEL FILTRA POR QUEM ABRIU
   	if (trim($_POST['cbo_diretoria']) != -1) 
	{
		$qr_select.= "
						AND (CASE WHEN nc.cd_responsavel = 0
						          THEN nc.aberto_por     IN (SELECT codigo 
						                                       FROM projetos.usuarios_controledi 
						                                      WHERE divisao IN (SELECT codigo
 															                      FROM projetos.divisoes
																				 WHERE area = '".trim($_POST['cbo_diretoria'])."'))
						          ELSE nc.cd_responsavel IN (SELECT codigo 
						                                       FROM projetos.usuarios_controledi 
						                                      WHERE divisao IN (SELECT codigo
 															                      FROM projetos.divisoes
																				 WHERE area = '".trim($_POST['cbo_diretoria'])."'))
						    END)		
		";
	}
	// FILTRA POR DIVISÃO PELO RESPONSAVEL, CASO NÃO TENHA RESPONSAVEL FILTRA POR QUEM ABRIU
   	if (trim($_POST['cbo_divisao']) != -1) 
	{
		$qr_select.= "
						AND (CASE WHEN nc.cd_responsavel = 0
						          THEN nc.aberto_por     IN (SELECT codigo 
						                                       FROM projetos.usuarios_controledi 
						                                      WHERE divisao = '".trim($_POST['cbo_divisao'])."')
						          ELSE nc.cd_responsavel IN (SELECT codigo 
						                                       FROM projetos.usuarios_controledi 
						                                      WHERE divisao = '".trim($_POST['cbo_divisao'])."')
						    END)		
		";
	}
	// FILTRA SOMENTE PRORROGADAS
	if(trim($_POST['cbo_prorrogada']) == 'S') 
	{
		$qr_select.= " AND ac.dt_prorrogada IS NOT NULL ";	
	}
	// FILTRA SOMENTE NÃO PRORROGADAS
	if(trim($_POST['cbo_prorrogada']) == 'N') 
	{
		$qr_select.= " AND ac.dt_prorrogada IS NULL ";	
	}
	// FILTRA SOMENTE ENCERRADAS
	if(trim($_POST['cbo_status']) == 'EN') 
	{
		$qr_select.= " AND nc.data_fechamento IS NOT NULL ";	
	}
	// FILTRA SOMENTE NÃO ENCERRADAS
	if(trim($_POST['cbo_status']) == 'NE') 
	{
		$qr_select.= "  AND nc.data_fechamento IS NULL ";	
	}
	// FILTRA SOMENTE IMPLEMENTADAS
	if(trim($_POST['cbo_implementada']) == 'S') 
	{
		$qr_select.= "  AND (SELECT ac.dt_efe_imp 
		                       FROM projetos.acao_corretiva ac 
							  WHERE ac.cd_acao     = nc.cd_nao_conformidade 
							    AND ac.cd_processo = nc.cd_processo) IS NOT NULL ";	
	}
	// FILTRA SOMENTE NÃO IMPLEMENTADAS
	if(trim($_POST['cbo_implementada']) == 'N')
	{
		$qr_select.= "  AND (SELECT ac.dt_efe_imp 
		                       FROM projetos.acao_corretiva ac 
							  WHERE ac.cd_acao     = nc.cd_nao_conformidade 
							    AND ac.cd_processo = nc.cd_processo) IS NULL ";
	}

	//FILTRA POR PROCESSO
	if(intval($filtro_cd_processo)>0)
	{
		$qr_select.="
			AND nc.cd_processo = ".intval($filtro_cd_processo)."
		";
	}

	$qr_select.= " ORDER BY nc.cd_nao_conformidade DESC ";

	$ob_resul = pg_query($qr_select);
	$nr_conta = 0;
	while ($ar_reg = pg_fetch_array($ob_resul))
	{
		//Se tiver sido mandado mensagem EV5 e mesmo assim, não existir na tabela AC
		$tpl->newBlock('nao_conf');
		$tpl->assign('codigo', $ar_reg['cod_nao_conf']);
		$tpl->assign('descricao', $ar_reg['descricao']);
		$tpl->assign('dt_limite_apres', $ar_reg['dt_limite_apres']."<BR>".$ar_reg['dt_apres']);
		$tpl->assign('dt_efe_imp', $ar_reg['dt_efe_imp']);
		$tpl->assign('dt_encerramento', $ar_reg['dt_encerramento']);
		$tpl->assign('dt_prop_imp', $ar_reg['dt_prop_imp']);
		$tpl->assign('dt_prorrogada', $ar_reg['dt_prorrogada']);
		$tpl->assign('aberto_por', $ar_reg['nome_aberto_por']);
		$tpl->assign('cod_processo', $ar_reg['processo']);
		$tpl->assign('procedimento', $ar_reg['procedimento']);
		$tpl->assign('numero_cad_nc', conv_num_nc($ar_reg['cod_nao_conf']));
		$tpl->assign('responsavel', $ar_reg['nome_responsavel']);

		// marcar as NC sem AC com mais de 15 dias:
		if ($ar_reg['nr_aviso'] != 0) 
		{
			$tpl->assign('bcg_nc','img/CA.gif');			
		}
		
		if(($nr_conta % 2) == 0)
		{
			$tpl->assign('bg_color', '#F4F4F4');
		}
		$nr_conta++;
	}
	############################################################################
	
	pg_close($db);
	$tpl->printToScreen();	
	require_once('inc/ajaxobject.php');
	
	function conv_num_nc($n) 
	{
		//Pressupõe que o num esteja no formato AAAANNN
		$aaaa = substr($n, 0, 4);
		$nc = substr($n, 4, 3);
		return $aaaa . '/' . $nc;
	}

?>