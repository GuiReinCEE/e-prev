<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   if (! isset($aa)) {
      $aa = $D;
   }
   
   header( 'location:'.base_url().'index.php/atividade/atividade_anexo/index/'.$n.'/'.$aa);
   
//------------------------------------------------------------------------------------------
	$tpl = new TemplatePower('tpl/tpl_frm_atividade_anexos.html');
//----------------------------------------------------------------------------------- TAREFAS ASSOCIADAS:		
	$tpl->prepare();
	$tpl->assign('n', $n);
	$tpl->assign('aa', $aa);
// --------------------------------------------------------- inicialização do skin das telas:
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
// ---------------------------------------------------------
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->assign('status_anterior', 	$cbo_status_atual);
	$tpl->assign('numero_os', $n);
	$tpl->assign( "site_url", site_url());
	
//----------------------------------------------------------------------------------- TAREFAS ASSOCIADAS:
	$sql = 		"	SELECT cd_anexo, tipo_anexo, tam_arquivo, to_char(dt_upload, 'dd/mm/yyyy') as dt_upload, ";
	$sql = $sql . " nome_arquivo, caminho ";
	$sql = $sql . " from projetos.atividade_anexo  ";
	$sql = $sql . " where cd_atividade = ".intval($n)."";
	$rs = pg_exec($db, $sql);
	$num = 1;
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('anexos');
		$tpl->assign('n', $n);
		$tpl->assign('cd_anexo', $reg['cd_anexo']);
		$tpl->assign('tipo_anexo', $reg['tipo_anexo']);
		$tpl->assign('nome_arquivo', $reg['nome_arquivo']);
		$tpl->assign('dt_upload', $reg['dt_upload']);
		$num = ($num + 1);
	}	
//----------------------------------------------------------------------------------- Acompanhamentos:
	$sql = 		"	SELECT cd_acomp, texto_acomp, to_char(dt_acompanhamento, 'DD/MM/YYYY HH24:MI:SS') as dt_acompanhamento ";
	$sql = $sql . " from projetos.acompanhamento_atividades  ";
	$sql = $sql . " where cd_atividade = ".intval($n)."";
	$rs = pg_exec($db, $sql);
	$num = 1;
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('acompanhamento');
		$tpl->assign('n', $n);
		$tpl->assign('texto', $reg['texto_acomp']);
		$tpl->assign('dt_acomp', $reg['dt_acompanhamento']);		
		$num = ($num + 1);
	}	
//------------------------------------------------------------------------------------ Finaliza construção da página
	pg_close($db);
	$tpl->printToscreen();
?>