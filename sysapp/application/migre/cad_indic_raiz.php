<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   
	$tpl = new TemplatePower('tpl/tpl_cad_indic_raiz.html');
   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->newBlock('cadastro');
	if (isset($c))	{
		$sql =        " select cd_indic, nome_indic, ordem, eixox, eixoy, ";
		$sql = $sql . "        num_series, seta, meta_raiz, img1, label1, img2, label2, img3, label3, img4, label4, img5, label5, ";
		$sql = $sql . "        tipo_grafico, indic_meta, indic_rotulos ";
		$sql = $sql . " from   acs.raiz_indicadores ";
		$sql = $sql . " where  cd_indic   = '$c'  ";
//		echo $sql;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		
		$tpl->assign('cd_indic', $reg['cd_indic']);
		$tpl->assign('nome_indic', $reg['nome_indic']);
		$tpl->assign('ordem', $reg['ordem']);
		$tpl->assign('eixox', $reg['eixox']);
		$tpl->assign('eixoy', $reg['eixoy']);
		$tpl->assign('num_series', $reg['num_series']);
		$tpl->assign('seta', $reg['seta']);
		$tpl->assign('meta_raiz', $reg['meta_raiz']);
		$tpl->assign('imagem1', $reg['img1']);	
		$tpl->assign('label1', $reg['label1']);
		$tpl->assign('imagem2', $reg['img2']);	
		$tpl->assign('label2', $reg['label2']);
		$tpl->assign('imagem3', $reg['img3']);	
		$tpl->assign('label3', $reg['label3']);
		$tpl->assign('imagem4', $reg['img4']);	
		$tpl->assign('label4', $reg['label4']);
		$tpl->assign('imagem5', $reg['img5']);	
		$tpl->assign('label5', $reg['label5']);
		$tpl->assign('tipo_grafico', $reg['tipo_grafico']);
		$tpl->assign('indic_meta', $reg['indic_meta']);
		$tpl->assign('indic_rotulos', $reg['indic_rotulos']);
		$cd_indicador = $reg['cd_indic'];
	}
//	  echo 'ponto 1';
      if ($op == 'A') {
	      $n = 'U';
	  }
	  else {
	    $n = 'I';
	  }
	  $tpl->assign('insere', $n);
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