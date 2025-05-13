<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
    $tpl = new TemplatePower('tpl/tpl_lst_modelagem_modelos.html');
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	
	#### LISTA MODELOS ####
	$qr_select = "
					SELECT m1.cd_modelo,
					       m1.ds_modelo,
                           m1.ds_cor,
                           m.ds_esquema,
                           m.ds_tabela,
						   (SELECT COUNT(*) 
						      FROM modelagem.modelos_tabelas 
							 WHERE cd_modelo = m1.cd_modelo) AS qt_tabela,
				           m.ds_cor AS ds_cor_principal,
						   m.ds_modelo AS ds_modelo_principal
                      FROM modelagem.modelos m1
					  LEFT JOIN modelagem.modelos_tabelas mt
					    ON mt.cd_modelo = m1.cd_modelo
                      LEFT JOIN (SELECT DISTINCT(m1.ds_modelo),
						                m1.cd_modelo,
                                        m1.ds_cor,
                                        mt1.ds_esquema,
                                        mt1.ds_tabela
						           FROM modelagem.modelos_tabelas mt1,
						 	            modelagem.modelos m1
						          WHERE m1.cd_modelo     = mt1.cd_modelo
						            AND mt1.fl_principal = 'S') AS m
                        ON m.ds_esquema = mt.ds_esquema
					   AND m.ds_tabela  = mt.ds_tabela	
                     ORDER BY m1.ds_modelo,
                              m.ds_esquema,
                              m.ds_tabela
				 ";
	$ob_result = pg_query($db, $qr_select);	
	$cd_modelo_atual = 0;
	$nr_conta  = 0;
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
		
		
		if($cd_modelo_atual != $ar_reg['cd_modelo'])
		{
			
			$tpl->newBlock('lst_modelos');
			$tpl->assign('bg_color',  $bg_color);
			$tpl->assign('cd_modelo', $ar_reg['cd_modelo']);	
			$tpl->assign('ds_modelos_tipos', $ar_reg['ds_modelos_tipos']);	
			$tpl->assign('ds_modelo', $ar_reg['ds_modelo']);	
			$tpl->assign('ds_cor',    $ar_reg['ds_cor']);
			$tpl->assign('qt_tabela', $ar_reg['qt_tabela']);
			$cd_modelo_atual = $ar_reg['cd_modelo'];
			$nr_conta++;
		}
		
		if(trim($ar_reg['ds_esquema']) != "")
		{
			$tpl->newBlock('lst_tabelas');
			$tpl->assign('bg_color',   $bg_color);
			$tpl->assign('ds_modelo_principal', $ar_reg['ds_modelo_principal']);	
			$tpl->assign('ds_esquema',          $ar_reg['ds_esquema']);		
			$tpl->assign('ds_tabela',           $ar_reg['ds_tabela']);

			if(checkTable($ar_reg['ds_esquema'], $ar_reg['ds_tabela'], $db))
			{
				$tpl->assign('fl_erro','display:none;');
				$tpl->assign('fl_deta','');
			}
			else
			{
				$tpl->assign('fl_erro','');		
				$tpl->assign('fl_deta','display:none;');
			}
		}
	}
	$tpl->newBlock('qt_total_reg');
	$tpl->assign('qt_total_reg', $nr_conta);		
	
	$tpl->printToScreen();
	pg_close($db);
	
	function checkTable($ds_esquema, $ds_tabela, $db)
	{
		$qr_select = "
						 SELECT COUNT(*) AS fl_check
						   FROM pg_catalog.pg_class c
						   LEFT JOIN pg_catalog.pg_namespace n 
						     ON n.oid = c.relnamespace
						  WHERE c.relkind = 'r'
                            AND n.nspname = '".$ds_esquema."'
	                        AND c.relname = '".$ds_tabela."' 					  
		             ";
		$ob_result = pg_query($db, $qr_select);	
		$ar_reg    = pg_fetch_array($ob_result);
		
		return $ar_reg['fl_check'];
	}
?>