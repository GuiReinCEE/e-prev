<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   
	$tpl = new TemplatePower('tpl/tpl_cad_telas_programas.html');
	$tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');
   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->newBlock('cadastro');
	$tpl->assign('programa', $cp);
	if (isset($c))	{
		$sql =        " select cd_programa, cd_tela, nome_tela, caption_tela, ";
		$sql = $sql . "        cd_programa_fceee, descricao, influencia_internet, ";
		$sql = $sql . "        to_char(dt_cadastro, 'DD/MM/YYYY') as data_cad ";
		$sql = $sql . " from   projetos.telas_programas ";
		$sql = $sql . " where  cd_programa   = '$cp' and cd_tela = $c ";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);		
		$tpl->assign('cd_tela', $reg['cd_tela']);
		$tpl->assign('tela', $reg['nome_tela']);
		$tpl->assign('caption', $reg['caption_tela']);
		$tpl->assign('descricao', $reg['descricao']);
		$tpl->assign('influencia_web', $reg['influencia_internet']);
		$tpl->assign('data_implantacao', $reg['data_cad']);
		 
		$programa_fceee = $reg['cd_programa_fceee'];
	}
	else {

		$date = date("d/m/Y");
		$tpl->assign('cd_tela', "");
		$tpl->assign('data_implantacao',  "");
	}

//	  echo 'ponto 1';
      if ($op == 'A') {
	      $n = 'U';
	  }
	  else {
	    $n = 'I';
	  }
	  $tpl->assign('insere', $n);
// --------------------------------------------------------- Combo Programa fceee
		$sql = "";
		$sql = $sql . " select 	codigo, descricao ";
		$sql = $sql . " from   	listas ";
		$sql = $sql . " where 	categoria = 'PRFC' ";
		$sql = $sql . " order by descricao ";
		$rs = pg_exec($db, $sql);
		$tpl->newBlock('programa');
		$tpl->assign('cd_programa', '');
		$tpl->assign('nome_programa', '');
     
		while ($reg=pg_fetch_array($rs)) {
			$tpl->newBlock('programa');
			$tpl->assign('cd_programa', $reg['codigo']);
			$tpl->assign('nome_programa', $reg['descricao']);
			if ($reg['codigo'] == $programa_fceee) { $tpl->assign('sel_programa', ' selected'); }
		}
// ----------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>