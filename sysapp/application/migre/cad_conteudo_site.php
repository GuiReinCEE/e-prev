<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/funcoes.php');
   include_once('inc/class.TemplatePower.inc.php');
   
	$tpl = new TemplatePower('tpl/tpl_cad_conteudo_site.html');
   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->assign('banner', 'banner_conteudo_' . $cs . '.jpg');
	$tpl->newBlock('cadastro');	
	$tpl->assign('cd_versao', $ed);
	$tpl->assign('cd_site', $cs);
	if (isset($c))	{
		$sql =        " select cd_materia, titulo, conteudo, visao, ";
		$sql = $sql . "        to_char(dt_inclusao, 'DD/MM/YYYY') as data_inc, ordem, ";
		$sql = $sql . "        to_char(dt_exclusao, 'DD/MM/YYYY') as data_exc, ";
		$sql = $sql . "        cd_usuario, imagem, link1, link2, link3, link4, ";
		$sql = $sql . "        item_menu, cd_secao, posicao_imagem, alinhamento_imagem, indice_alternativo ";
		$sql = $sql . " from   projetos.conteudo_site ";
		$sql = $sql . " where  cd_materia   = $c and cd_versao = $ed and cd_site = $cs";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		
		$tpl->assign('cd_materia', $reg['cd_materia']);
		$tpl->assign('titulo', $reg['titulo']);

		$conteudo = prepara_conteudo_html($reg[conteudo]);
		$tpl->assign('conteudo',$conteudo);

		$tpl->assign('indice_alternativo', $reg['indice_alternativo']);
		$tpl->assign('conteudo_ed', '"'.$conteudo.'"');
		$tpl->assign('imagem', $reg['imagem']);
		if ($reg['ordem'] != '') {
			$tpl->assign('ordem', $reg['ordem']);
		}
		else {
			$tpl->assign('ordem', '0');
		}
		$tpl->assign('visao', $reg['visao']);
		$tpl->assign('fonte', $reg['fonte']);
		$tpl->assign('link1', $reg['link1']);
		$tpl->assign('link2', $reg['link2']);
		$tpl->assign('link3', $reg['link3']);
		$tpl->assign('link4', $reg['link4']);
		$tpl->assign('item_menu', $reg['item_menu']);
		$tpl->assign('dt_inclusao', $reg['data_inc']);
		$tpl->assign('dt_exclusao', $reg['data_exc']);		 
		switch($reg['posicao_imagem']) {
			case 'S': $tpl->assign('chk_superior', 'checked'); break;
			case 'T': $tpl->assign('chk_texto', 'checked'); break;
			case 'I': $tpl->assign('chk_inferior', 'checked'); break;
		}	
		switch($reg['alinhamento_imagem']) {
			case 'C': $tpl->assign('chk_centro', 'checked'); break;
			case 'D': $tpl->assign('chk_direita', 'checked'); break;
			case 'E': $tpl->assign('chk_esquerda', 'checked'); break;
		}	
		$cd_materia = $reg['cd_materia'];
		$cd_usuario = $reg['cd_usuario'];
		$cd_secao = $reg['cd_secao'];
	}
	else {
		$sql =        " select max(cd_materia) as cd_materia ";
		$sql = $sql . " from   projetos.conteudo_site where cd_versao = $ed and cd_site = $cs";
//		echo $sql;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('cd_materia', ($reg['cd_materia'] + 1));
		$date = date("d/m/Y");
		$tpl->assign('dt_inclusao',  $date);

	}
//	  echo 'ponto 1';
      if ($op == 'A') {
	      $n = 'U';
	  }
	  else {
	    $n = 'I';
	  }
	  $tpl->assign('insere', $n);
// --------------------------------------------------------- Combo usuсrio
		$sql = "";
		$sql = $sql . " select codigo, nome";
		$sql = $sql . " from   projetos.usuarios_controledi  where tipo not in ('X', 'P', 'T') ";
		$sql = $sql . " order by nome ";
		$rs = pg_exec($db, $sql);
 
		while ($reg=pg_fetch_array($rs)) {
			$tpl->newBlock('usuario');
			$tpl->assign('cod_usuario', $reg['codigo']);
			$tpl->assign('nome_usuario', $reg['nome']);
			if ($reg['codigo'] == $cd_usuario) { $tpl->assign('sel_usuario', ' selected'); }
		}
// ---------------------------------------------------------- Combo secao
		$sql = "";
		$sql = $sql . " SELECT 	codigo as codigo, ";
		$sql = $sql . "        	descricao as descricao    ";
		$sql = $sql . " FROM 	listas ";
		
		if ($cs == 1){
			$sql = $sql . " WHERE 	categoria = 'SENG'  order by descricao ";
		}
		else {
			$sql = $sql . " where categoria = 'SSIT' and valor1 = $cs and valor2 = $ed ";
//			$sql = $sql . " WHERE 	categoria = 'FCEE'  order by descricao ";
		}
		$rs = pg_exec($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('cbo_secao');
			$tpl->assign('cod_secao', $reg['codigo']);
			$tpl->assign('secao', $reg['descricao']);
			if ($reg['codigo'] == $cd_secao) { $tpl->assign('sel_secao', ' selected'); }
		}
// ----------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();
// ----------------------------------------------------------
	function convdata_br_iso($dt) {
		// Pressupѕe que a data esteja no formato DD/MM/AAAA
		// A melhor forma de gravar datas no PostgreSQL щ utilizando 
		// uma string no formato DDDD-MM-AA. Esta funчуo justamente 
		// adequa a data a este formato
		$d = substr($dt, 0, 2);
		$m = substr($dt, 3, 2);
		$a = substr($dt, 6, 4);
		return $a.'-'.$m.'-'.$d;
	}
?>