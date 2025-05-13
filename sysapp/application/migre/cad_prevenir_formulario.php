<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/ajaxobject.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_prevenir_formulario.html');
	$tpl->prepare();
	$tpl->assign('n', $n);

	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');

	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	if (($_SESSION['D'] != 'GRI') AND ($_SESSION['D'] != 'AAA')  AND ($_SESSION['D'] != 'DE'))
	{
   		#header('location: acesso_restrito.php?IMG=banner_prevnet');
		$fl_editar = false;
	}
	else
	{
		$fl_editar = true;
	}	
	

	$qr_sql = "  
				SELECT ".($fl_editar == true ? "ds_nome" : "MD5(ds_nome) AS ds_nome").",
                       ".($fl_editar == true ? "ds_instituicao" : "MD5(ds_instituicao) AS ds_instituicao").",
                       ".($fl_editar == true ? "ds_email" : "MD5(ds_email) AS ds_email").",
                       ".($fl_editar == true ? "nr_telefone" : "MD5(nr_telefone) AS nr_telefone").",
					   TO_CHAR(dt_envio, 'DD/MM/YYYY HH24:MI') AS dt_envio
				  FROM prevenir.prevenir_formulario
				 WHERE MD5(cd_prevenir_formulario::TEXT) = '".$_REQUEST['cd_prevenir_formulario']."'
			  ";
	$ob_resul = pg_query($db, $qr_sql);
	$ar_reg = pg_fetch_array($ob_resul);
	$tpl->assign('ds_nome', $ar_reg['ds_nome']);
	$tpl->assign('ds_instituicao', $ar_reg['ds_instituicao']);
	$tpl->assign('ds_email', $ar_reg['ds_email']);
	$tpl->assign('nr_telefone', $ar_reg['nr_telefone']);
	$tpl->assign('dt_envio', $ar_reg['dt_envio']);

	$qr_sql = "
				SELECT cd_pergunta,
				       MD5(cd_prevenir_formulario_item::TEXT) AS cd_prevenir_formulario_item,
				       o_que,
					   porque,
					   quem,
					   quando,
					   onde,
					   como,
					   CASE WHEN fl_exibir <> 'S' 
					        THEN 'nao'
							ELSE 'sim'
					   END AS fl_exibir
				  FROM prevenir.prevenir_formulario_item
				 WHERE dt_exclusao                       IS NULL
				   AND MD5(cd_prevenir_formulario::TEXT) = '".$_REQUEST['cd_prevenir_formulario']."'
				   ".($fl_editar == true ? "" : "AND fl_exibir = 'S'")."
				 ORDER BY cd_pergunta ASC	
			  ";
	$ob_resul = pg_query($db, $qr_sql);
	while($ar_reg = pg_fetch_array($ob_resul))
	{
		$tpl->newBlock('pergunta_'.$ar_reg['cd_pergunta']);
		$tpl->assign('cd_prevenir_formulario_item', $ar_reg['cd_prevenir_formulario_item']);
		$tpl->assign('o_que', $ar_reg['o_que']);
		$tpl->assign('porque', $ar_reg['porque']);				  
		$tpl->assign('quem', $ar_reg['quem']);				  
		$tpl->assign('quando', $ar_reg['quando']);				  
		$tpl->assign('onde', $ar_reg['onde']);				  
		$tpl->assign('como', $ar_reg['como']);				  
		$tpl->assign('fl_exibir_'.$ar_reg['fl_exibir'], 'selected');
		($fl_editar == true ? $tpl->assign('fl_exibir_editar', '') : $tpl->assign('fl_exibir_editar', 'display:none;'));
	}

	
//--------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>