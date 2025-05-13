<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_enquetes_agrupamentos.html');

//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	$v_cor_fundo1 = "#F2F8FC";
	$v_cor_fundo2 = "#FFFFFF";	
	
//-----------------------------------------------
	$tpl->newBlock('cadastro');
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);
	if (isset($eq))	{
		$sql =        " select 	cd_enquete, titulo, cd_responsavel ";
		$sql = $sql . " from 	projetos.enquetes  ";
		$sql = $sql . " where 	cd_enquete = $eq ";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('titulo', $reg['titulo']);
		$tpl->assign('eq', $eq);
		if ($reg['cd_responsavel'] != $Z) {
			$tpl->assignGlobal('ro_responsavel', 'readonly');
			$tpl->assignGlobal('dis_responsavel', 'disabled');
		}		
	}
//------------------------------------------------------------------------------------------- 
	if (isset($c))	{
		$sql =        " select 	nome, indic_escala, mostrar_valores, ordem, nota_rodape, disposicao ";
		$sql = $sql . " from 	projetos.enquete_agrupamentos  ";
		$sql = $sql . " where 	cd_enquete = $eq and cd_agrupamento = $c ";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('codigo', $c);
		$tpl->assign('ordem', $reg['ordem']);
		$tpl->assign('agrupamento', $reg['nome']);
		$tpl->assign('nota_rodape', $reg['nota_rodape']);
		if ($reg['indic_escala'] == 'S') {
			$tpl->assign('chk_escala_s', 'checked');
		} else {
			$tpl->assign('chk_escala_n', 'checked');
		}
		if ($reg['mostrar_valores'] == 'S') {
			$tpl->assign('chk_valores_s', 'checked');
		} else {
			$tpl->assign('chk_valores_n', 'checked');
		}
		if ($reg['disposicao'] == 'H') {
			$tpl->assign('chk_disp_horizontal', 'checked');
		} else {
			$tpl->assign('chk_disp_vertical', 'checked');
		}
	} else {
        $tpl->assign('chk_escala_n', 'checked');
        $tpl->assign('chk_valores_s', 'checked');
        $tpl->assign('chk_disp_horizontal', 'checked');

        $sql = " 
            SELECT MAX( ordem ) AS maior, COUNT(*) as quantos
              FROM projetos.enquete_agrupamentos
             WHERE cd_enquete = $eq
        ";
        $rs = pg_exec($db, $sql);
        $reg=pg_fetch_array($rs);
        if ($reg["quantos"]==0) {
    		$tpl->assign('ordem', '1');
		}
        else {
    		$tpl->assign('ordem', $reg["maior"]+1);
        }

	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>