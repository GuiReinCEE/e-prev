<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/funcoes.php');
   include_once('inc/class.TemplatePower.inc.php');
   
	$tpl = new TemplatePower('tpl/tpl_cad_cenario.html');
	$tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');
   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->newBlock('cadastro');
	$tpl->assign('cd_edicao', $ed);
	if (isset($c))	{
		$sql =        " select cd_cenario, titulo, conteudo, arquivo_associado, ";
		$sql = $sql . "        to_char(dt_inclusao, 'DD/MM/YYYY') as data_inc, ";
		$sql = $sql . "        to_char(dt_exclusao, 'DD/MM/YYYY') as data_exc, ";
		$sql = $sql . "        to_char(dt_prevista, 'DD/MM/YYYY') as dt_prevista, ";
		$sql = $sql . "        to_char(dt_legal, 'DD/MM/YYYY') as dt_legal, ";
		$sql = $sql . "        to_char(dt_implementacao, 'DD/MM/YYYY') as dt_implementacao, pertinencia, ";
		$sql = $sql . "        cd_usuario, imagem, link1, link2, link3, link4, referencia, fonte, area_indicada, cd_secao, ";
		$sql = $sql . "        indic_aa, indic_acs, indic_aj, indic_da, indic_dap, indic_db, ";
		$sql = $sql . "        indic_dcg, indic_df, indic_di, indic_die, indic_din, indic_drh, indic_sg ";
		$sql = $sql . " from   projetos.cenario ";
		$sql = $sql . " where  cd_cenario   = $c and cd_edicao = $ed";
//		echo $sql;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		
		$tpl->assign('cd_cenario', $reg['cd_cenario']);
		$tpl->assign('titulo', $reg['titulo']);
//		$tpl->assign('conteudo', $reg['conteudo']);
		$conteudo = prepara_conteudo_html($reg[conteudo]);
		$tpl->assign('conteudo',$conteudo);

		$tpl->assign('imagem', $reg['imagem']);
		$tpl->assign('arquivo', $reg['arquivo_associado']);
		$tpl->assign('referencia', $reg['referencia']);
		$tpl->assign('fonte', $reg['fonte']);
		$tpl->assign('link1', $reg['link1']);
		$tpl->assign('link2', $reg['link2']);
		$tpl->assign('link3', $reg['link3']);
		$tpl->assign('link4', $reg['link4']);
		$tpl->assign('dt_inclusao', $reg['data_inc']);
		$tpl->assign('dt_exclusao', $reg['data_exc']);
		$tpl->assign('dt_prevista', $reg['dt_prevista']);
		$tpl->assign('dt_legal', $reg['dt_legal']);
		
		if ($reg['pertinencia'] == 0) {
			$tpl->assign('check_pert0', 'checked');	
		}
		else {
			if ($reg['pertinencia'] == 1) {
				$tpl->assign('check_pert1', 'checked'); 
			}	
			else {
				if ($reg['pertinencia'] == 2) {
					$tpl->assign('check_pert2', 'checked');	
				}
			}
		}
		$tpl->assign('dt_implementacao', $reg['dt_implementacao']);	
		
		#### СREAS INDICADAS ####
		$qr_sql = "
					SELECT d.codigo AS cd_divisao,
						   CASE WHEN ca.cd_cenario_areas IS NOT NULL 
								THEN 'S' 
								ELSE 'N' 
						   END AS fl_area_indicada
					  FROM projetos.divisoes d
					  LEFT JOIN projetos.cenario_areas ca
						ON ca.cd_divisao  = d.codigo
					   AND ca.cd_cenario  = ".$c."
					   AND ca.dt_exclusao IS NULL
					 WHERE d.tipo IN ('ASS','DIV')		
		          ";
		$ob_resul = pg_query($db, $qr_sql);
		while($ar_reg = pg_fetch_array($ob_resul))
		{
			if (($ar_reg['cd_divisao'] == "GA")  and ($ar_reg['fl_area_indicada'] == 'S')){ $tpl->assign('chk_aa', 'checked'); }
			if (($ar_reg['cd_divisao'] == "GRI") and ($ar_reg['fl_area_indicada'] == 'S')){ $tpl->assign('chk_acs', 'checked'); }
			if (($ar_reg['cd_divisao'] == "GJ")  and ($ar_reg['fl_area_indicada'] == 'S')){ $tpl->assign('chk_aj', 'checked'); }
			if (($ar_reg['cd_divisao'] == "GAD") and ($ar_reg['fl_area_indicada'] == 'S')){ $tpl->assign('chk_da', 'checked'); $tpl->assign('chk_drh', 'checked');}
			if (($ar_reg['cd_divisao'] == "GAP") and ($ar_reg['fl_area_indicada'] == 'S')){ $tpl->assign('chk_dap', 'checked'); }
			if (($ar_reg['cd_divisao'] == "GB")  and ($ar_reg['fl_area_indicada'] == 'S')){ $tpl->assign('chk_db', 'checked'); }
			if (($ar_reg['cd_divisao'] == "GC")  and ($ar_reg['fl_area_indicada'] == 'S')){ $tpl->assign('chk_dcg', 'checked'); }
			if (($ar_reg['cd_divisao'] == "GF")  and ($ar_reg['fl_area_indicada'] == 'S')){ $tpl->assign('chk_df', 'checked'); }
			if (($ar_reg['cd_divisao'] == "GI")  and ($ar_reg['fl_area_indicada'] == 'S')){ $tpl->assign('chk_di', 'checked'); }
			if (($ar_reg['cd_divisao'] == "DE")  and ($ar_reg['fl_area_indicada'] == 'S')){ $tpl->assign('chk_die', 'checked'); }
			if (($ar_reg['cd_divisao'] == "GIN") and ($ar_reg['fl_area_indicada'] == 'S')){ $tpl->assign('chk_din', 'checked'); }
			if (($ar_reg['cd_divisao'] == "SG")  and ($ar_reg['fl_area_indicada'] == 'S')){ $tpl->assign('chk_sg', 'checked'); }
		}
		
		
		$cd_cenario = $reg['cd_cenario'];
		$cd_usuario = $reg['cd_usuario'];
		$cd_secao = $reg['cd_secao'];
	}
	else {
		$sql =        " select max(cd_cenario) as cd_cenario ";
		$sql = $sql . " from   projetos.cenario ";
//		echo $sql;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('cd_cenario', ($reg['cd_cenario'] + 1));
		$tpl->assign('fonte', 'Site Fiscodata');
		$tpl->assign('link1', 'http://www.fiscodata.com.br');
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
		$sql = $sql . " WHERE 	categoria = 'SCEN'  order by descricao ";
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