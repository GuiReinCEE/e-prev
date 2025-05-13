<?
	include_once('inc/sessao.php');

header( 'Location:'.str_replace('cieprev/sysapp/application/migre','controle_projetos',base_url_eprev())."lst_empresas.php?chk_rel=S" );
exit;

	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
// -------------------------------------------------------------------
	$v_busca = '2000';
	if (isset($presente_seminario_2001)) {
		$v_busca = $v_busca . ", ". $presente_seminario_2001;
		$v_presente_seminario = 'S';
	}
	if (isset($presente_seminario_2002)) {
		$v_busca = $v_busca . ", ". $presente_seminario_2002;
		$v_presente_seminario = 'S';
	}
	if (isset($presente_seminario_2003)) {
		$v_busca = $v_busca . ", ". $presente_seminario_2003;
		$v_presente_seminario = 'S';
	}
	if (isset($presente_seminario_2004)) {
		$v_busca = $v_busca . ", ". $presente_seminario_2004;
		$v_presente_seminario = 'S';
	}
	if (isset($presente_seminario_2005)) {
		$v_busca = $v_busca . ", ". $presente_seminario_2005;
		$v_presente_seminario = 'S';
	}
  	if (($D <> 'GEX') and ($D <> 'GGS') and ($D <> 'SG')) {
   		header('location: acesso_restrito.php?IMG=banner_empresas');
	}
//  	if ($Z <> 110) {
//  		header('location: manutencao.php?IMG=banner_empresas');
//	}

	$tpl = new TemplatePower('tpl/tpl_lst_empresas.html');
	$tpl->prepare();
	
    if(isset($_REQUEST['n']))
    {
        $n = $_REQUEST['n'];
    }
    else
    {
        $n = '';
    }
    $tpl->assign('n', $n);
    
// -------------------------------------------------------------------   
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

	$_REQUEST['chk_rel'] == "S" ? $tpl->assign('chk_rel_checked', "checked") : $tpl->assign('chk_rel_checked', "");
	
	
	$sql = "
			SELECT ei.cd_emp_inst, 
				   ei.nome_empresa_entidade, 
				   l.descricao AS segmento,
				   ll.descricao AS relacionamento
			  FROM expansao.empresas_instituicoes ei
			  LEFT JOIN listas l
				ON l.codigo    = ei.cd_segmento
			   AND l.categoria = 'SACS'
			  LEFT JOIN listas ll
			    ON ll.codigo    = CAST(ei.relacionamento AS  TEXT)
			   AND ll.categoria = 'RREL' 
			 WHERE ei.dt_exclusao IS NULL  
			   ".($_REQUEST['chk_rel'] == "S" ? "AND ei.relacionamento IS NOT NULL AND ei.relacionamento > 0": "")."
			 ORDER BY ei.cd_emp_inst DESC
		   ";

	$ob_resul = pg_query($db, $sql);
	$tpl->assign('qt_registro',pg_num_rows($ob_resul));
	while ($ar_reg = pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('lista');
		$tpl->assign('cd_emp_inst',$ar_reg['cd_emp_inst']);
		$tpl->assign('nome_empresa_entidade',$ar_reg['nome_empresa_entidade']);
		$tpl->assign('relacionamento',$ar_reg['relacionamento']);
		$tpl->assign('segmento',$ar_reg['segmento']);

	}
    
	
    pg_close($db);
	$tpl->printToScreen();	
?>