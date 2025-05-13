<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	
    header( 'location:'.base_url().'index.php/cadastro/avaliacao_cargo');

	$tpl = new TemplatePower('tpl/tpl_cad_cargo.html');
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//-----------------------------------------------
	if(!gerencia_in(array('GAD')))
	{
   		header('location: acesso_restrito.php?IMG=banner_cargos');
	}
	
	// ABAS - BEGIN
	$abas[] = array('aba_lista', 'Lista', false, 'aba_lista_click(this)');
	$abas[] = array('aba_cadastro', 'Cadastro', true, '');
	$tpl->assignGlobal( 'ABA_START', aba_start( $abas ) );
	$tpl->assignGlobal( 'ABA_END', aba_end( '') );
	$tpl->assignGlobal( 'link_lista', site_url("cadastro/avaliacao_cargo") );
	// ABAS - END
	
		$tpl->newBlock('cadastro');
		if (isset($c))
		{
			$sql = " select cd_cargo, nome_cargo, desc_cargo, cd_familia ";
			$sql = $sql . " from projetos.cargos where cd_cargo=$c " ;
			$rs = pg_exec($db, $sql);
			$reg=pg_fetch_array($rs);
			$tpl->assign('codigo', $reg['cd_cargo']);
			$tpl->assign('nome', $reg['nome_cargo']);
			$tpl->assign('descricao', $reg['desc_cargo']);
			$v_cd_familia = $reg['cd_familia'];
		}
//------------------------------------------------ Competencias Específicas:
	if (isset($c)) 
	{
		$sql =		  " select 	ce.cd_comp_espec, ce.nome_comp_espec from   projetos.cargos_comp_espec cce, projetos.comp_espec ce";
		$sql = $sql . " where  	ce.cd_comp_espec = cce.cd_comp_espec and cce.cd_cargo = " . $c ;
		$sql = $sql . " order 	by ce.nome_comp_espec ";
		$rs = pg_exec($db, $sql);
		while ($reg=pg_fetch_array($rs)) 
		{
			$tpl->newBlock('comp_espec');
			$tpl->assign('cd_comp_espec', $reg['cd_comp_espec']);
			$tpl->assign('nome_comp_espec', $reg['nome_comp_espec']);
			$tpl->assign('comp_espec_checked', 'checked'); 
		}
//------------------------------------------------ Competencias Institucionais:
		$sql =		  " select 	ci.cd_comp_inst, ci.nome_comp_inst from   projetos.cargos_comp_inst cci, projetos.comp_inst ci";
		$sql = $sql . " where  	ci.cd_comp_inst = cci.cd_comp_inst and cci.cd_cargo = " . $c ;
		$sql = $sql . " order 	by ci.nome_comp_inst ";
		$rs = pg_exec($db, $sql);
		while ($reg=pg_fetch_array($rs)) 
		{
			$tpl->newBlock('comp_inst');
			$tpl->assign('cd_comp_inst', $reg['cd_comp_inst']);
			$tpl->assign('nome_comp_inst', $reg['nome_comp_inst']);
			$tpl->assign('comp_inst_checked', 'checked');
		}
//------------------------------------------------ Responsabilidades:
		$sql =		  " select 	ci.cd_responsabilidade, ci.nome_responsabilidade from   projetos.cargos_responsabilidades cci, projetos.responsabilidades ci";
		$sql = $sql . " where  	ci.cd_responsabilidade = cci.cd_responsabilidade and cci.cd_cargo = " . $c ;
		$sql = $sql . " order 	by ci.nome_responsabilidade ";
		$rs = pg_exec($db, $sql);
		while ($reg=pg_fetch_array($rs)) 
		{
			$tpl->newBlock('responsabilidade');
			$tpl->assign('cd_responsabilidade', $reg['cd_responsabilidade']);
			$tpl->assign('nome_responsabilidade', $reg['nome_responsabilidade']);
			$tpl->assign('responsabilidade_checked', 'checked');
		}
	}
//----------------------------------------------- Escolaridades:
	$sql =        " select 	c.cd_familia, c.nome_familia ";
	$sql = $sql . " from   	projetos.familias_cargos c ";
	$sql = $sql . " order 	by c.nome_familia ";
	$rs = pg_exec($db, $sql);
	while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('cbo_familia');
		$tpl->assign('cd_familia', $reg['cd_familia']);
		$tpl->assign('nome_familia', $reg['nome_familia']);
		if ($reg['cd_familia'] == $v_cd_familia) { $tpl->assign('sel_familia', ' selected'); }
	}
//----------------------------------------------- Escolaridades:
//	$sql =        " select 	c.cd_escolaridade, c.nome_escolaridade ";
//	$sql = $sql . " from   	projetos.escolaridade c ";
//	$sql = $sql . " order 	by c.nome_escolaridade ";
//	$rs = pg_exec($db, $sql);
//	while ($reg=pg_fetch_array($rs)) 
//	{
//		$tpl->newBlock('escolaridade');
//		$tpl->assign('cd_escolaridade', $reg['cd_escolaridade']);
//		$tpl->assign('nome_escolaridade', $reg['nome_escolaridade']);
//		if (isset($c)) {
//			$sql2 =			" select * from   projetos.cargos_escolaridade ";
//			$sql2 = $sql2 . " where cd_cargo = " . $c ;
//			$sql2 = $sql2 . " 	and cd_escolaridade = " . $reg['cd_escolaridade'];
//			$rs2 = pg_exec($db, $sql2);
//			if (pg_fetch_array($rs2)) { $tpl->assign('escolaridade_checked', 'checked'); }
//		}
//	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>