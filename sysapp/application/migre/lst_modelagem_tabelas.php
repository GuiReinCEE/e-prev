<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
    $tpl = new TemplatePower('tpl/tpl_lst_modelagem_tabelas.html');
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	


	
	function limpaFiltroSessao()
	{
		$ar_keys  = array_keys($_SESSION);
		$nr_fim   = count($ar_keys);
		$nr_conta = 0;
		while($nr_conta < $nr_fim)
		{
			if(ereg("_lst_modelagem_tabelas",$ar_keys[$nr_conta])) 
			{
				unset($_SESSION[$ar_keys[$nr_conta]]);
			}
			$nr_conta++;
		}
		
		$ar_keys  = array_keys($_REQUEST);
		$nr_fim   = count($ar_keys);
		$nr_conta = 0;
		while($nr_conta < $nr_fim)
		{
			unset($_REQUEST[$ar_keys[$nr_conta]]);
			$nr_conta++;
		}		
	}

	function setFiltroSessao($ds_campo,$fl_padrao,$vl_padrao)
	{
		#### COLOCA CAMPOS DO FILTRO NA SESSAO ####
		if(trim($_REQUEST[$ds_campo]) <> "")
		{
			if(!array_key_exists($ds_campo, $_REQUEST))
			{
				$_REQUEST[$ds_campo] = $vl_padrao;
			}
			else
			{
				$_SESSION[$ds_campo.'_lst_modelagem_tabelas'] = $_REQUEST[$ds_campo];
			}
		}
		else if(count($_POST) > 1)
		{
			if((!array_key_exists($ds_campo.'_lst_modelagem_tabelas', $_SESSION)) and ($fl_padrao))
			{
				$_REQUEST[$ds_campo] = $vl_padrao;
				$_SESSION[$ds_campo.'_lst_modelagem_tabelas'] = $vl_padrao;
			}
			else
			{
				$_SESSION[$ds_campo.'_lst_modelagem_tabelas'] = $_REQUEST[$ds_campo];
			}
		}
		else 
		{
			if((!array_key_exists($ds_campo.'_lst_modelagem_tabelas', $_SESSION)) and ($fl_padrao))
			{
				$_REQUEST[$ds_campo] = $vl_padrao;
				$_SESSION[$ds_campo.'_lst_modelagem_tabelas'] = $vl_padrao;
			}
			else
			{
				$_REQUEST[$ds_campo] = $_SESSION[$ds_campo.'_lst_modelagem_tabelas'];
			}
		}
	}

	#### LIMPA FILTROS DA SESSÃO ####
	if($_REQUEST['fl_filtro_padrao'] == "S")
	{
		//limpaFiltroSessao();
	}
	
	#### FIXA FILTROS DE ACORDO COM A SESSÃO ####
	setFiltroSessao('ds_esquema_filtro',false,'');		
	setFiltroSessao('ds_modelo_filtro',false,'');			
	setFiltroSessao('ds_tabela_modelo',false,'');	
	
	
	#### MONTA COMBO ESQUEMAS PARA FILTRO ####
	$qr_select = "
					 SELECT DISTINCT(n.nspname) AS ds_esquema
					   FROM pg_catalog.pg_class c
					   LEFT JOIN pg_catalog.pg_namespace n 
					     ON n.oid = c.relnamespace
					  WHERE c.relkind = 'r'
					    AND n.nspname NOT LIKE 'pg_temp%'
					  ORDER BY ds_esquema
				 ";
	$ob_result = pg_query($db, $qr_select);	
	$nr_conta  = 0;
	while ($ar_reg = pg_fetch_array($ob_result)) 
	{	
		$tpl->newBlock('lst_esquemas');
		$tpl->assign('ds_esquema', $ar_reg['ds_esquema']);	
		if($_REQUEST['ds_esquema_filtro'] == $ar_reg['ds_esquema'])
		{
			$tpl->assign('fl_selecionado', 'selected');	
		}
	}
	
	#### MONTA COMBO MODELOS PARA FILTRO ####
	$qr_select = "
					 SELECT cd_modelo,
					        ds_modelo
					   FROM modelagem.modelos
					  ORDER BY ds_modelo
				 ";
	$ob_result = pg_query($db, $qr_select);	
	$nr_conta  = 0;
	while ($ar_reg = pg_fetch_array($ob_result)) 
	{	
		$tpl->newBlock('lst_modelos_filtro');
		$tpl->assign('cd_modelo', $ar_reg['cd_modelo']);	
		$tpl->assign('ds_modelo', $ar_reg['ds_modelo']);	
		if($_REQUEST['ds_modelo_filtro'] == $ar_reg['cd_modelo'])
		{
			$tpl->assign('fl_selecionado', 'selected');	
		}
	}	
	
	#### MONTA COMBO TABELAS COM MODELO ####
	$tpl->newBlock('ds_tabela_modelo');
	$tpl->assign('cd_tabela_modelo', 'S');	
	$tpl->assign('ds_tabela_modelo', 'Sim');	
	if($_REQUEST['ds_tabela_modelo'] == "S")
	{
		$tpl->assign('fl_selecionado', 'selected');	
	}
	$tpl->newBlock('ds_tabela_modelo');
	$tpl->assign('cd_tabela_modelo', 'N');	
	$tpl->assign('ds_tabela_modelo', 'Não');	
	if($_REQUEST['ds_tabela_modelo'] == "N")
	{
		$tpl->assign('fl_selecionado', 'selected');	
	}
	
	#### LISTA TABELAS ####
	$qr_select = "
					 SELECT n.nspname AS ds_esquema, 
					        c.relname AS ds_tabela,
                            mt.cd_modelo,
                            m.ds_modelo,
							m.ds_cor, 
							mt.fl_principal
					   FROM pg_catalog.pg_class c
					   LEFT JOIN pg_catalog.pg_namespace n 
					     ON n.oid = c.relnamespace
					   LEFT JOIN modelagem.modelos_tabelas mt
						 ON mt.ds_esquema = n.nspname
						AND mt.ds_tabela  = c.relname
					   LEFT JOIN modelagem.modelos m
						 ON m.cd_modelo = mt.cd_modelo
					  WHERE c.relkind = 'r'
					    AND n.nspname NOT LIKE 'pg_temp%'
					  ";
					  
	#### FILTRO ESQUEMA ####
	if(trim($_REQUEST['ds_esquema_filtro']) != "")
	{
		$qr_select.=" AND n.nspname = '".$_REQUEST['ds_esquema_filtro']."'";	
	}

	#### FILTRO MODELO ####
	if(trim($_REQUEST['ds_modelo_filtro']) != "")
	{
		$qr_select.=" AND mt.cd_modelo = '".$_REQUEST['ds_modelo_filtro']."'";	
	}
	
	#### FILTRO TABELAS COM MODELO ####
	if($_REQUEST['ds_tabela_modelo'] == "S")
	{
		$qr_select.=" AND 0 < (SELECT COUNT(*)  
		                         FROM modelagem.modelos_tabelas mt1 
							    WHERE mt1.ds_esquema = n.nspname
						          AND mt1.ds_tabela  = c.relname)";	
	}

	#### FILTRO TABELAS SEM MODELO ####
	if($_REQUEST['ds_tabela_modelo'] == "N")
	{
		$qr_select.=" AND 0 = (SELECT COUNT(*)  
		                         FROM modelagem.modelos_tabelas mt1 
							    WHERE mt1.ds_esquema = n.nspname
						          AND mt1.ds_tabela  = c.relname)";		
	}	
	
	
	$qr_select.= "
					  ORDER BY ds_esquema,
					           ds_tabela,
							   ds_modelo
				 ";
	$ob_result    = pg_query($db, $qr_select);	
	$nr_conta     = 0;
	$ds_reg_atual = "";
	while ($ar_reg = pg_fetch_array($ob_result)) 
	{
		if(($nr_conta % 2) != 0)
		{
			$bg_color = '#F4F4F4';
		}
		else
		{
			$bg_color = '#FFFFFF';		
		}
		
		
		if($ds_reg_atual != $ar_reg['ds_esquema'].".".$ar_reg['ds_tabela'])
		{
			$tpl->newBlock('lst_tabelas');
			$tpl->assign('bg_color',   $bg_color);
			$tpl->assign('ds_esquema', $ar_reg['ds_esquema']);
			$tpl->assign('ds_tabela',  $ar_reg['ds_tabela']);
			$ds_reg_atual = $ar_reg['ds_esquema'].".".$ar_reg['ds_tabela'];
			$nr_conta++;
		}
		
			$tpl->newBlock('lst_modelos');
			$tpl->assign('ds_modelo', $ar_reg['ds_modelo']);
			$tpl->assign('ds_estilo', '#'.$ar_reg['ds_cor']);
			if($ar_reg['fl_principal'] == "S")
			{
				$tpl->assign('fl_principal', 'font-weight: bold;');
			}

	}
	$tpl->newBlock('qt_total_reg');
	$tpl->assign('qt_total_reg', $nr_conta);		
	
	$tpl->printToScreen();
	pg_close($db);
?>