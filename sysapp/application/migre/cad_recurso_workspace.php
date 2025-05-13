<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   $tpl = new TemplatePower('tpl/tpl_cad_recurso_workspace.html');
   $tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');
   
   if ($c == '') { $c = $Z; }
   if ($u == '') { $u = $U; }
   $tpl->prepare();
   $tpl->assign('n', $n);
   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
   $tpl->assign('divsao', $D);
//------------------------------------------------ Verifica perfil do usuário atual 
	$sql = "";
	$sql =        " SELECT indic_01, indic_02, indic_03, indic_04, indic_05, indic_06, ";
	$sql = $sql . " 		indic_07, indic_08, indic_09, indic_10, indic_11, indic_12 ";
	$sql = $sql . " FROM projetos.usuarios_controledi  " ;
	$sql = $sql . " where codigo           	= $Z";
	$rs = pg_exec($db, $sql);
	$reg=pg_fetch_array($rs);
	$indic_adm = $reg['indic_05'];
//---------------------------------------------------------------------------------


	// ABAS - BEGIN
	$abas[] = array('aba_pref', 'Preferências', false, 'ir_pref();');
	$abas[] = array('aba_perfil', 'Perfil', false, 'ir_perfil();');
	$abas[] = array('aba_work', 'Meu Workspace', true, 'void(0);');
	$tpl->assignGlobal( 'ABA_START', aba_start( $abas ) );
	$tpl->assignGlobal( 'ABA_END', aba_end( '') );
	// ABAS - END
	
	$tpl->assignGlobal('usuario_link', $u);
	$tpl->assignGlobal('codigo_link', $c);
	

	$tpl->newBlock('cadastro');
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);
	if ($indic_adm <> '*') {
		$tpl->assign('ro_adm', 'readonly'); 
		if ($c != $Z) {
			header("location: acesso_restrito.php?IMG=banner_recursos_humanos"); 
		}
	}
	if (isset($u))
	{
		$sql = "";
		$sql =        " select codigo, usuario, nome, divisao, observacao, tipo, cd_cargo, tela_inicial,  ";
		$sql = $sql . " 		formato_mensagem, e_mail_alternativo, cd_registro_empregado, skin, opt_tarefas, opt_workspace, ";
		$sql = $sql . " 		favorito2, favorito3, favorito4, favorito5,  ";
		$sql = $sql . " 		dash1, dash2, dash3, dash4, dash5, dash6, dash7, ";
		$sql = $sql . " 		indic_01, indic_02, indic_03, indic_04, indic_05, indic_06, ";
		$sql = $sql . " 		indic_07, indic_08, indic_09, indic_10, indic_11, indic_12 ";
		$sql = $sql . " from projetos.usuarios_controledi  " ;
		$sql = $sql . " where codigo           	= $c";
		$sql = $sql . "       and usuario      = '$u' ";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);

		$tpl->assign('codigo', $reg['codigo']);
		$tpl->assign('re', $reg['cd_registro_empregado']);
		$tpl->assign('usuario', $reg['usuario']);
		$tpl->assign('nome', $reg['nome']);
		$tpl->assign('divisao', $reg['divisao']);
		$tpl->assign('obs', $reg['observacao']);
		$tpl->assign('indic1', $reg['indic_01']);
		$tpl->assign('indic2', $reg['indic_02']);
		$tpl->assign('indic3', $reg['indic_03']);
		$tpl->assign('indic4', $reg['indic_04']);
		$tpl->assign('indic5', $reg['indic_05']);
		$tpl->assign('indic6', $reg['indic_06']);
		$tpl->assign('indic7', $reg['indic_07']);
		$tpl->assign('indic8', $reg['indic_08']);
		$tpl->assign('indic9', $reg['indic_09']);
		$tpl->assign('indic10', $reg['indic_10']);
		$tpl->assign('indic11', $reg['indic_11']);
		$tpl->assign('indic12', $reg['indic_12']);
		$tpl->assign('email_alternativo', $reg['e_mail_alternativo']);
		$tpl->assign('forma_mens', $reg['formato_mensagem']);
		$v_programa_inicial = $reg['tela_inicial'];
		$v_favorito2 = $reg['favorito2'];
		$v_favorito3 = $reg['favorito3'];
		$v_favorito4 = $reg['favorito4'];
		$v_favorito5 = $reg['favorito5'];
		$v_dash1 = $reg['dash1'];
		$v_dash2 = $reg['dash2'];
		$v_dash3 = $reg['dash3'];
		$v_dash4 = $reg['dash4'];
		$v_dash5 = $reg['dash5'];
		$v_dash6 = $reg['dash6'];
		$v_dash7 = $reg['dash7'];
		$fmens  = $reg['formato_mensagem'];
		$perfil = $reg['tipo'];
		$codrec = $reg['codigo'];
		$cd_cargo = $reg['cd_cargo'];
		if ($reg['opt_tarefas'] == 'S') {
			 $tpl->assign('chk_tarefas', 'checked');		 	
		}
		if ($reg['opt_workspace'] == 'S') {
			 $tpl->assign('chk_workspace', 'checked');		 	
		}

		if ($reg['skin'] == 'TRAD') {
			 $tpl->assign('chk_trad', 'checked');		 	
		}
		elseif  ($reg['skin'] == 'NEO1') {
			 $tpl->assign('chk_neo1', 'checked');		 	
		}
		elseif  ($reg['skin'] == 'MODE') {
			 $tpl->assign('chk_mode', 'checked');		 	
		}
		elseif  ($reg['skin'] == 'PRAT') {
			 $tpl->assign('chk_prat', 'checked');		 	
		}
		
//		 echo $tr;
      }
// ---------------------------------------------- Combo Área de Interesse
//	$sql =        " select cd_area_interesse, nome, descricao ";
//	$sql = $sql . " from   projetos.areas_interesse ";
//	$sql = $sql . " order by descricao ";
//	$rs = pg_exec($db, $sql);
//	while ($reg = pg_fetch_array($rs)) {
//			$tpl->newBlock('area_interesse');
//			$tpl->assign('nome_area', $reg['cd_area_interesse']);
//			$tpl->assign('desc_area', $reg['descricao']);
//			$sql2 =        	" select  count(*) as num_regs ";
//			$sql2 = $sql2 . " from   projetos.usuarios_areas_interesse ";
//			$sql2 = $sql2 . " where  cd_usuario = $Z and cd_area_interesse = " . $reg['cd_area_interesse'];
//			$rs2 = pg_exec($db, $sql2);
//			$reg2 = pg_fetch_array($rs2);
//			if ($reg2['num_regs'] > 0) {
//				$tpl->assign('valor_area', 'checked');
//			}
//	}	  
//---------------------------------------------------------------------------------	
// ---------------------------------------------- Combo Tela Inicial
	$sql =        " select 	nome_programa, desc_programa ";
	$sql = $sql . " from   	projetos.telas_eprev	 ";
	$sql = $sql . " where	tipo = 'P' ";
	$sql = $sql . " order 	by desc_programa ";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('tela_inicial');
		$tpl->assign('nome_prog', $reg['nome_programa']);
		$tpl->assign('desc_tela', $reg['desc_programa']);
		if ($reg['nome_programa'] == $v_programa_inicial ) {
			$tpl->assign('chk_tela', 'selected');
		}
	}	  
// ---------------------------------------------- Combo Tela Favorita2
	$tpl->newBlock('favorito2');
	$tpl->assign('nome_prog', '');
	$tpl->assign('desc_tela', '');
	$sql =        " select nome_programa, desc_programa ";
	$sql = $sql . " from   projetos.telas_eprev	 ";
	$sql = $sql . " where	tipo = 'P' ";
	$sql = $sql . " order by desc_programa ";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('favorito2');
		$tpl->assign('nome_prog', $reg['nome_programa']);
		$tpl->assign('desc_tela', $reg['desc_programa']);
		if ($reg['nome_programa'] == $v_favorito2 ) {
			$tpl->assign('chk_tela', 'selected');
		}
	}	  
// ---------------------------------------------- Combo Tela Favorita3
	$tpl->newBlock('favorito3');
	$tpl->assign('nome_prog', '');
	$tpl->assign('desc_tela', '');
	$sql =        " select nome_programa, desc_programa ";
	$sql = $sql . " from   projetos.telas_eprev	 ";
	$sql = $sql . " where	tipo = 'P' ";
	$sql = $sql . " order by desc_programa ";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('favorito3');
		$tpl->assign('nome_prog', $reg['nome_programa']);
		$tpl->assign('desc_tela', $reg['desc_programa']);
		if ($reg['nome_programa'] == $v_favorito3 ) {
			$tpl->assign('chk_tela', 'selected');
		}
	}	  
// ---------------------------------------------- Combo Tela Favorita4
	$tpl->newBlock('favorito4');
	$tpl->assign('nome_prog', '');
	$tpl->assign('desc_tela', '');
	$sql =        " select nome_programa, desc_programa ";
	$sql = $sql . " from   projetos.telas_eprev	 ";
	$sql = $sql . " where	tipo = 'P' ";
	$sql = $sql . " order by desc_programa ";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('favorito4');
		$tpl->assign('nome_prog', $reg['nome_programa']);
		$tpl->assign('desc_tela', $reg['desc_programa']);
		if ($reg['nome_programa'] == $v_favorito4 ) {
			$tpl->assign('chk_tela', 'selected');
		}
	}	  
// ---------------------------------------------- Combo Tela Favorita5
	$tpl->newBlock('favorito5');
	$tpl->assign('nome_prog', '');
	$tpl->assign('desc_tela', '');
	$sql =        " select nome_programa, desc_programa ";
	$sql = $sql . " from   projetos.telas_eprev	 ";
	$sql = $sql . " where	tipo = 'P' ";
	$sql = $sql . " order by desc_programa ";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('favorito5');
		$tpl->assign('nome_prog', $reg['nome_programa']);
		$tpl->assign('desc_tela', $reg['desc_programa']);
		if ($reg['nome_programa'] == $v_favorito5 ) {
			$tpl->assign('chk_tela', 'selected');
		}
	}	  
// ---------------------------------------------- Combo Dash1
	$tpl->newBlock('dash1');
	$tpl->assign('nome_dash', '');
	$tpl->assign('desc_dash', '');
	$sql =        " select nome_programa, desc_programa ";
	$sql = $sql . " from   projetos.telas_eprev	 ";
	$sql = $sql . " where	tipo = 'D' ";
	$sql = $sql . " order by desc_programa ";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('dash1');
		$tpl->assign('nome_dash', $reg['nome_programa']);
		$tpl->assign('desc_dash', $reg['desc_programa']);
		if ($reg['nome_programa'] == $v_dash1 ) {
			$tpl->assign('chk_dash', 'selected');
		}
	}	  
// ---------------------------------------------- Combo Dash2
	$tpl->newBlock('dash2');
	$tpl->assign('nome_dash', '');
	$tpl->assign('desc_dash', '');
	$sql =        " select nome_programa, desc_programa ";
	$sql = $sql . " from   projetos.telas_eprev	 ";
	$sql = $sql . " where	tipo = 'D' ";
	$sql = $sql . " order by desc_programa ";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('dash2');
		$tpl->assign('nome_dash', $reg['nome_programa']);
		$tpl->assign('desc_dash', $reg['desc_programa']);
		if ($reg['nome_programa'] == $v_dash2 ) {
			$tpl->assign('chk_dash', 'selected');
		}
	}	  
// ---------------------------------------------- Combo Dash3
	$tpl->newBlock('dash3');
	$tpl->assign('nome_dash', '');
	$tpl->assign('desc_dash', '');
	$sql =        " select nome_programa, desc_programa ";
	$sql = $sql . " from   projetos.telas_eprev	 ";
	$sql = $sql . " where	tipo = 'D' ";
	$sql = $sql . " order by desc_programa ";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('dash3');
		$tpl->assign('nome_dash', $reg['nome_programa']);
		$tpl->assign('desc_dash', $reg['desc_programa']);
		if ($reg['nome_programa'] == $v_dash3 ) {
			$tpl->assign('chk_dash', 'selected');
		}
	}	  
// ---------------------------------------------- Combo Dash4
	$tpl->newBlock('dash4');
	$tpl->assign('nome_dash', '');
	$tpl->assign('desc_dash', '');
	$sql =        " select nome_programa, desc_programa ";
	$sql = $sql . " from   projetos.telas_eprev	 ";
	$sql = $sql . " where	tipo = 'D' ";
	$sql = $sql . " order by desc_programa ";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('dash4');
		$tpl->assign('nome_dash', $reg['nome_programa']);
		$tpl->assign('desc_dash', $reg['desc_programa']);
		if ($reg['nome_programa'] == $v_dash4 ) {
			$tpl->assign('chk_dash', 'selected');
		}
	}	  
// ---------------------------------------------- Combo Dash5
	$tpl->newBlock('dash5');
	$tpl->assign('nome_dash', '');
	$tpl->assign('desc_dash', '');
	$sql =        " select nome_programa, desc_programa ";
	$sql = $sql . " from   projetos.telas_eprev	 ";
	$sql = $sql . " where	tipo = 'D' ";
	$sql = $sql . " order by desc_programa ";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('dash5');
		$tpl->assign('nome_dash', $reg['nome_programa']);
		$tpl->assign('desc_dash', $reg['desc_programa']);
		if ($reg['nome_programa'] == $v_dash5 ) {
			$tpl->assign('chk_dash', 'selected');
		}
	}	  
// ---------------------------------------------- Combo Dash6
	$tpl->newBlock('dash6');
	$tpl->assign('nome_dash', '');
	$tpl->assign('desc_dash', '');
	$sql =        " select nome_programa, desc_programa ";
	$sql = $sql . " from   projetos.telas_eprev	 ";
	$sql = $sql . " where	tipo = 'D' ";
	$sql = $sql . " order by desc_programa ";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('dash6');
		$tpl->assign('nome_dash', $reg['nome_programa']);
		$tpl->assign('desc_dash', $reg['desc_programa']);
		if ($reg['nome_programa'] == $v_dash6) {
			$tpl->assign('chk_dash', 'selected');
		}
	}	  
// ---------------------------------------------- Combo Dash7
	$tpl->newBlock('dash7');
	$tpl->assign('nome_dash', '');
	$tpl->assign('desc_dash', '');
	$sql =        " select nome_programa, desc_programa ";
	$sql = $sql . " from   projetos.telas_eprev	 ";
	$sql = $sql . " where	tipo = 'D' ";
	$sql = $sql . " order by desc_programa ";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('dash7');
		$tpl->assign('nome_dash', $reg['nome_programa']);
		$tpl->assign('desc_dash', $reg['desc_programa']);
		if ($reg['nome_programa'] == $v_dash7 ) {
			$tpl->assign('chk_dash', 'selected');
		}
	}	  
//---------------------------------------------------------------------------------	
   pg_close($db);
   $tpl->printToScreen();	
?>