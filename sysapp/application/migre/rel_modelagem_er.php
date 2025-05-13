<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	require_once('inc/ajaxobject.php');
	include_once('inc/class.TemplatePower.inc.php');
	
    $tpl = new TemplatePower('tpl/tpl_rel_modelagem_er.html');
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	//include_once('inc/skin.php');
	//$tpl->assign('usuario', $N);
	//$tpl->assign('divsao', $D);
	
	$tpl->assign('cd_modelo', $_GET['cd_modelo']);
	
	#### DESCRIÇÃO DO MODELO ####
	$qr_select = "
					SELECT m.ds_modelo,
					       m.ds_cor
                      FROM modelagem.modelos m
                     WHERE m.cd_modelo = ".$_GET['cd_modelo']."
				 ";
	$ob_result = pg_query($db, $qr_select);	
	$ar_reg = pg_fetch_array($ob_result);
	$tpl->assign('ds_modelo', $ar_reg['ds_modelo']);
	$tpl->assign('ds_cor',    $ar_reg['ds_cor']);
	
	#### LISTA MODELOS ####
	$qr_select = "
					SELECT DISTINCT(m.ds_modelo) AS ds_modelo,
                           m.ds_cor
                      FROM modelagem.modelos_tabelas mt,
                           (SELECT DISTINCT(m1.ds_modelo),
                                   m1.ds_cor,
                                   mt1.ds_esquema,
                                   mt1.ds_tabela
						      FROM modelagem.modelos_tabelas mt1,
						 	       modelagem.modelos m1
						     WHERE m1.cd_modelo     = mt1.cd_modelo
						       AND mt1.fl_principal = 'S') AS m
                     WHERE mt.cd_modelo = ".$_GET['cd_modelo']."
					   AND m.ds_esquema = mt.ds_esquema
					   AND m.ds_tabela  = mt.ds_tabela
				 ";
	$ob_result = pg_query($db, $qr_select);	
	while ($ar_reg = pg_fetch_array($ob_result)) 
	{
		$tpl->newBlock('lst_modelos');
		$tpl->assign('ds_cor',    'background: #'.$ar_reg['ds_cor'].';');
		$tpl->assign('ds_modelo', $ar_reg['ds_modelo']);	
	}	
	
	
	#### SETA AR_RELACIONAMENTOS JAVASCRIPT ####
	/*
	$qr_select = "
					SELECT DISTINCT 'FK_' || mt.ds_esquema || '.' || mt.ds_tabela || '.' || mt2.ds_esquema || '.' || mt2.ds_tabela AS ds_chave,
						   mt.ds_esquema || '.' || mt.ds_tabela AS ds_tabela_ini,  
						   mt2.ds_esquema || '.' || mt2.ds_tabela AS ds_tabela_fim
					  FROM pg_catalog.pg_class r,
						   pg_catalog.pg_class f,
						   pg_catalog.pg_constraint c,
						   pg_catalog.pg_namespace n,
						   modelagem.modelos_tabelas mt,
						   modelagem.modelos_tabelas mt2
					 WHERE contype        IN('f')
					   AND r.oid          = c.conrelid
					   AND n.oid          = r.relnamespace 
					   AND f.oid          = c.confrelid 
					   AND mt.ds_esquema  = n.nspname
					   AND mt.ds_tabela   = r.relname 
					   AND mt.cd_modelo   = ".$_GET['cd_modelo']."						   
					   AND mt2.cd_modelo  = mt.cd_modelo
					   AND mt2.ds_esquema = n.nspname
					   AND mt2.ds_tabela  = f.relname 
				 ";
	*/
	$qr_select = "
						SELECT c.conname || '_' || ROUND((((((RANDOM() * (1000)) * (100)) / (1000)) * (999999)) / (100))) AS ds_chave,
							   nf.nspname || '.' || f.relname AS ds_tabela_ini,  
							   a.ds_esquema || '.' || a.ds_tabela AS ds_tabela_fim,							   
							   af.attname AS ds_campo_ini,
							   a.ds_campo AS ds_campo_fim
						  FROM modelagem.modelos_tabelas mt,
                               modelagem.modelos_tabelas mt2,
                               pg_catalog.pg_class r,
                               pg_catalog.pg_class f,
						       pg_catalog.pg_constraint c,
						       pg_catalog.pg_namespace n,
						       pg_catalog.pg_namespace nf,
						       pg_catalog.pg_attribute af,
						       (SELECT n1.nspname AS ds_esquema,
						               c1.relname AS ds_tabela,
						               a1.attnum AS nr_ordem, 
						               a1.attname AS ds_campo
						          FROM pg_catalog.pg_class c1, 
						               pg_catalog.pg_attribute a1, 
						               pg_catalog.pg_namespace n1 
						         WHERE a1.attnum     > 0 
						           AND a1.attrelid   = c1.oid 
						           AND n1.oid        = c1.relnamespace) a
						 WHERE contype        = 'f'
						   AND f.oid          = c.confrelid
						   AND nf.oid         = f.relnamespace 
						   AND af.attrelid    = f.oid 
						   AND (af.attnum = c.confkey[1] OR af.attnum = c.confkey[2] OR af.attnum = c.confkey[3] OR af.attnum = c.confkey[4] OR af.attnum = c.confkey[5] OR af.attnum = c.confkey[6] OR af.attnum = c.confkey[7] OR af.attnum = c.confkey[8] OR af.attnum = c.confkey[9])
                           AND (a.nr_ordem = c.conkey[1] OR a.nr_ordem = c.conkey[2] OR a.nr_ordem = c.conkey[3] OR a.nr_ordem = c.conkey[4] OR a.nr_ordem = c.conkey[5] OR a.nr_ordem = c.conkey[6] OR a.nr_ordem = c.conkey[7] OR a.nr_ordem = c.conkey[8] OR a.nr_ordem = c.conkey[9])		
						   AND r.oid          = c.conrelid
						   AND n.oid          = r.relnamespace  
						   AND n.nspname      = a.ds_esquema
						   AND r.relname      = a.ds_tabela
						   AND mt.ds_esquema  = n.nspname
					       AND mt.ds_tabela   = r.relname 
						   AND mt2.ds_esquema = nf.nspname
					       AND mt2.ds_tabela  = f.relname 	
                           AND mt.cd_modelo   = mt2.cd_modelo
                           AND mt.cd_modelo   = ".$_GET['cd_modelo']."
					     ORDER BY a.nr_ordem
				 ";	
	
	$ob_result = pg_query($db, $qr_select);	
	$lt_relacionamento = "";
	while ($ar_reg = pg_fetch_array($ob_result)) 
	{
		if(trim($lt_relacionamento) == "")
		{
			$lt_relacionamento .= "'".$ar_reg['ds_tabela_ini'].";".$ar_reg['ds_tabela_fim'].";".$ar_reg['ds_chave'].";".$ar_reg['ds_campo_ini'].";".$ar_reg['ds_campo_fim']."'";
		}
		else
		{
			$lt_relacionamento .= ",'".$ar_reg['ds_tabela_ini'].";".$ar_reg['ds_tabela_fim'].";".$ar_reg['ds_chave'].";".$ar_reg['ds_campo_ini'].";".$ar_reg['ds_campo_fim']."'";
		}
	}	
	$tpl->newBlock('relacionamento');
	$tpl->assign('lt_relacionamento', $lt_relacionamento);	
	
	
	#### LISTA TABELAS DO MODELO ####
	$qr_select = "
					SELECT mt.ds_esquema,
					       mt.ds_tabela,
						   mt.nr_x,
						   mt.nr_y,
					       (SELECT m1.ds_cor 
						      FROM modelagem.modelos_tabelas mt1,
						 	       modelagem.modelos m1
						     WHERE m1.cd_modelo     = mt1.cd_modelo
						       AND mt1.ds_esquema   = mt.ds_esquema
						       AND mt1.ds_tabela    = mt.ds_tabela
						       AND mt1.fl_principal = 'S') AS ds_cor,
				           a.attnum AS nr_ordem, 
				           a.attname AS ds_campo, 
				           REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(pg_catalog.format_type(a.atttypid, a.atttypmod)
                                                   ,'character varying','varchar') 
                                                   ,'timestamp(0) without time zone','timestamp') 
                                                   ,'timestamp without time zone','timestamp') 
                                                   ,'timestamp with time zone','timestamp') 
                                                   ,'time without time zone','time')  AS ds_tipo,
						   (CASE WHEN a.attnotnull = TRUE
						         THEN 'NOT NULL'
						         ELSE 'NULL'
						   END) AS ds_notnull,
				           (SELECT adsrc 
						      FROM pg_attrdef adef 
						     WHERE a.attrelid = adef.adrelid 
						       AND a.attnum   = adef.adnum) AS adsrc	   
					  FROM modelagem.modelos_tabelas mt,
				           pg_catalog.pg_class c, 
				           pg_catalog.pg_attribute a, 
				           pg_catalog.pg_type t,
				           pg_catalog.pg_namespace n 
				     WHERE a.attnum     > 0 
				       AND a.attrelid   = c.oid 
				       AND n.nspname    = mt.ds_esquema 
				       AND c.relname    = mt.ds_tabela 
				       AND a.atttypid   = t.oid
				       AND n.oid        = c.relnamespace		 
				       AND mt.cd_modelo = ".$_GET['cd_modelo']."
				 ";
	$ob_result = pg_query($db, $qr_select);	
	$ds_tabela_atual = "";
	$lt_tabelas = "";	
	while ($ar_reg = pg_fetch_array($ob_result)) 
	{
		if($ds_tabela_atual != $ar_reg['ds_esquema'].".".$ar_reg['ds_tabela'])
		{
			$tpl->newBlock('lst_tabelas');
			$ds_cor = 'background: #'.$ar_reg['ds_cor'].';';
			$tpl->assign('ds_cor',     $ds_cor);	
			$tpl->assign('ds_esquema', $ar_reg['ds_esquema']);	
			$tpl->assign('ds_tabela',  $ar_reg['ds_tabela']);	
			$tpl->assign('nr_x',       $ar_reg['nr_x']);
			$tpl->assign('nr_y',       $ar_reg['nr_y']);
			$ds_tabela_atual = $ar_reg['ds_esquema'].".".$ar_reg['ds_tabela'];
			
			if(trim($lt_tabelas) == "")
			{
				$lt_tabelas .= "'".$ar_reg['ds_esquema'].".".$ar_reg['ds_tabela']."'";
			}
			else
			{
				$lt_tabelas .= ",'".$ar_reg['ds_esquema'].".".$ar_reg['ds_tabela']."'";
			}
		}
		
		$tpl->newBlock('lst_campos');
		$ds_chave = getCampoKey($ar_reg['ds_esquema'],$ar_reg['ds_tabela'],$ar_reg['ds_campo'],$db);
		$tpl->assign('tp_campo', $ds_chave);
		$tpl->assign('ds_campo', $negrito.$ar_reg['ds_campo']);
		$tpl->assign('ds_tipo',  $negrito.$ar_reg['ds_tipo']);
	}
	
	
	#### SETA AR_TABELAS DO JAVASCRIPT ####
	$tpl->newBlock('drag_drop');
	$tpl->assign('lt_tabelas', $lt_tabelas);
	
	$tpl->printToScreen();
	pg_close($db);
	
	function getCampoKey($ds_esquema,$ds_tabela,$ds_campo,$db)
	{
		$qr_select = "
						SELECT a.ds_esquema,
						       a.ds_tabela,
						       a.ds_campo,
						       c.conname AS ds_chave,
							   (CASE WHEN c.contype = 'p' THEN 'table_key.gif'
							         WHEN c.contype = 'f' THEN 'table_relationship.gif'
							   END) AS tp_chave,
							   (CASE WHEN c.contype = 'p' THEN 'PK'
							         WHEN c.contype = 'f' THEN 'FK'
							   END) AS id_chave							   
						  FROM pg_catalog.pg_class r,
						       pg_catalog.pg_constraint c,
						       pg_catalog.pg_namespace n,
						       (SELECT n1.nspname AS ds_esquema,
						               c1.relname AS ds_tabela,
						               a1.attnum AS nr_ordem, 
						               a1.attname AS ds_campo
						          FROM pg_catalog.pg_class c1, 
						               pg_catalog.pg_attribute a1, 
						               pg_catalog.pg_namespace n1 
						         WHERE a1.attnum     > 0 
						           AND a1.attrelid   = c1.oid 
						           AND n1.oid        = c1.relnamespace) a
						 WHERE contype      IN('p','f')
						   AND r.oid        = c.conrelid
						   AND n.oid        = r.relnamespace  
						   AND n.nspname    = a.ds_esquema
						   AND r.relname    = a.ds_tabela
						   AND a.ds_esquema = '".$ds_esquema."' 
						   AND a.ds_tabela  = '".$ds_tabela."' 
						   AND a.ds_campo   = '".$ds_campo."'
						   AND (a.nr_ordem = c.conkey[1] OR a.nr_ordem = c.conkey[2] OR a.nr_ordem = c.conkey[3] OR a.nr_ordem = c.conkey[4] OR a.nr_ordem = c.conkey[5] OR a.nr_ordem = c.conkey[6] OR a.nr_ordem = c.conkey[7] OR a.nr_ordem = c.conkey[8] OR a.nr_ordem = c.conkey[9])		
						 ORDER BY id_chave DESC
		             ";
		$ob_result = pg_query($db, $qr_select);	
		$ds_retorno = "";
		while ($ar_reg = pg_fetch_array($ob_result)) 
		{	
			$ds_retorno.= "<img src='img/".$ar_reg['tp_chave']."' border='0' title='".$ar_reg['ds_chave']."''>";
			/*
			if(trim($ds_retorno) == "")
			{
				$ds_retorno.= $ar_reg['id_chave'];
			}
			else
			{
				$ds_retorno.= "/".$ar_reg['id_chave'];
			}
			*/			
		}
		if(trim($ds_retorno) == "")
		{
			$ds_retorno = "<img src='img/table_null.gif' border='0' width='10' height='10'>";
		}		
		return $ds_retorno;
	}
	
	
	
?>