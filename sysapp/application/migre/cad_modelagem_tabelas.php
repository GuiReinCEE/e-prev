<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
    $tpl = new TemplatePower('tpl/tpl_cad_modelagem_tabelas.html');
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	

	$tpl->assign('tela_voltar', $_SERVER['HTTP_REFERER']);

	$tpl->assign('ds_esquema', $_POST['ds_esquema']);
	$tpl->assign('ds_tabela',  $_POST['ds_tabela']);
	$tpl->assign('ds_esquema_filtro',  $_POST['ds_esquema_filtro']);
	
	#### LISTA MODELOS ####
	$qr_select = "
					 SELECT m.cd_modelo,
					        m.ds_modelo,
							mt.cd_modelo AS cd_modelo_incluido,
							mt.fl_principal
					   FROM modelagem.modelos m
					   LEFT JOIN modelagem.modelos_tabelas mt
						 ON mt.cd_modelo = m.cd_modelo
					    AND mt.ds_esquema = '".$_POST['ds_esquema']."'
					    AND mt.ds_tabela  = '".$_POST['ds_tabela']."'
					  ORDER BY m.ds_modelo
				 ";
	$ob_result = pg_query($db, $qr_select);	
	$nr_conta  = 0;
	while ($ar_reg = pg_fetch_array($ob_result)) 
	{	
		$tpl->newBlock('lst_modelos_radio');
		$tpl->assign('cd_modelo', $ar_reg['cd_modelo']);	
		$tpl->assign('ds_modelo', $ar_reg['ds_modelo']);	
		if($ar_reg['cd_modelo'] == $ar_reg['cd_modelo_incluido'])
		{
			$tpl->assign('fl_modelo_check', 'checked');	
		}
		if($ar_reg['fl_principal'] == "S")
		{
			$tpl->assign('fl_modelo_radio', 'checked');	
		}
	}	
	
	$tpl->printToScreen();
	pg_close($db);
?>