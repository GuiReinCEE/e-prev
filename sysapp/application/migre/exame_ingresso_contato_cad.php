<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	require_once('inc/ajaxobject.php');
	include_once('inc/class.TemplatePower.inc.php');

	$cd_exame_ingresso_contato = 0;
	if( isset($_POST['cd_exame_ingresso_contato']) )
	{
		$cd_exame_ingresso_contato = intval($_POST['cd_exame_ingresso_contato']);
	}
	$cd_exame_ingresso = 0;
	if(isset($_REQUEST['cd_exame_ingresso']))
	{
		$cd_exame_ingresso = intval($_REQUEST['cd_exame_ingresso']);
	}

	$tpl = new TemplatePower('tpl/tpl_exame_ingresso_contato_cad.html');
	$tpl->prepare();

	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');

	$tpl->assign('n', $n);
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

	#### PERMISSOES ####
	if(($_SESSION['D'] != "GAP") and ($_SESSION['D'] != "GI"))
	{
		$tpl->assign('fl_editar', 'disabled');
	}

	#### BUSCA INFORMACAO PARTICIPANTE ####
	$qr_sql = "
			SELECT ei.cd_exame_ingresso, 
			       ei.cd_empresa, 
				   ei.cd_registro_empregado, 
				   ei.seq_dependencia, 
	                       ei.ds_nome
	                  FROM projetos.exame_ingresso ei
			 WHERE ei.cd_exame_ingresso = ".intval($cd_exame_ingresso)."
	";
	$ob_resul = pg_query($db,$qr_sql);
	$ar_reg   = pg_fetch_array($ob_resul);
	$tpl->assign('cd_exame_ingresso',     $ar_reg['cd_exame_ingresso']);
	$tpl->assign('cd_empresa',            $ar_reg['cd_empresa']);
	$tpl->assign('cd_registro_empregado', $ar_reg['cd_registro_empregado']);
	$tpl->assign('seq_dependencia',       $ar_reg['seq_dependencia']);
	$tpl->assign('ds_nome',               $ar_reg['ds_nome']);

	if($cd_exame_ingresso_contato > 0)
	{
		#### BUSCA INFORMACAO DO CONTATO ####
		$qr_sql = "
					SELECT EIC.cd_exame_ingresso_contato,
					       TO_CHAR(eic.dt_contato,'DD/MM/YYYY') AS dt_contato, 
					       TO_CHAR(eic.dt_contato,'HH24:MI') AS hr_contato, 
	                       eic.ds_contato
	                  FROM projetos.exame_ingresso_contato eic
					 WHERE eic.cd_exame_ingresso_contato = ".intval($cd_exame_ingresso_contato)."
		          ";
		$ob_resul = pg_query($db,$qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		$tpl->assign('cd_exame_ingresso_contato', $ar_reg['cd_exame_ingresso_contato']);
		$tpl->assign('dt_contato', $ar_reg['dt_contato']);
		$tpl->assign('hr_contato', $ar_reg['hr_contato']);
		$tpl->assign('ds_contato', $ar_reg['ds_contato']);
	}

	#### LISTA CONTATOS ####
	$qr_sql = "
				SELECT eic.cd_exame_ingresso_contato,
                       eic.cd_exame_ingresso,				
				       TO_CHAR(eic.dt_contato,'DD/MM/YYYY HH24:MI') AS dt_contato_br,
					   eic.ds_contato,
					   uc.nome AS ds_usuario
                  FROM projetos.exame_ingresso_contato eic
				  JOIN projetos.usuarios_controledi uc
				    ON uc.codigo = eic.cd_usuario_inclusao
				 WHERE eic.cd_exame_ingresso = ".intval($_REQUEST['cd_exame_ingresso'])."
				   AND eic.dt_exclusao       IS NULL
				 ORDER BY dt_contato DESC
	          ";
	$ob_resul = pg_query($db,$qr_sql);
	$tpl->assign('qt_inscrito', pg_num_rows($ob_resul));
	while($ar_reg   = pg_fetch_array($ob_resul))
	{
		$tpl->newBlock('lista');
		$tpl->assign('cd_exame_ingresso_contato', $ar_reg['cd_exame_ingresso_contato']);		
		$tpl->assign('cd_exame_ingresso',         $ar_reg['cd_exame_ingresso']);		
		$tpl->assign('dt_contato',                $ar_reg['dt_contato_br']);		
		$tpl->assign('ds_contato',                $ar_reg['ds_contato']);		
		$tpl->assign('ds_usuario',                $ar_reg['ds_usuario']);		
	}

	$tpl->printToScreen();
	pg_close($db);
?>