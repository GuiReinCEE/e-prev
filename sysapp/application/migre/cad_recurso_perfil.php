<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   $tpl = new TemplatePower('tpl/tpl_cad_recurso_perfil.html');
   $tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');
   
   $tpl->prepare();
   $tpl->assign('n', $n);
   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
    $tpl->assign('divsao', $D);
//------------------------------------------------ Verifica perfil do usuário atual 
	$sql = "";
	$sql =        " select 	indic_01, indic_02, indic_03, indic_04, indic_05, indic_06, ";
	$sql = $sql . " 		indic_07, indic_08, indic_09, indic_10, indic_11, indic_12 ";
	$sql = $sql . " from projetos.usuarios_controledi  " ;
	$sql = $sql . " where codigo           	= $Z";
	$rs = pg_exec($db, $sql);
	$reg=pg_fetch_array($rs);
	$v_indic_adm = $reg['indic_05'];
//---------------------------------------------------------------------------------

	// ABAS - BEGIN
	if($op!='I'){$abas[] = array('aba_pref', 'Preferências', false, 'ir_pref();');}	
	$abas[] = array('aba_perfil', 'Perfil', true, 'void(0);');
	if($op!='I'){$abas[] = array('aba_work', 'Meu Workspace', false, 'ir_work();');}
	$tpl->assignGlobal( 'ABA_START', aba_start( $abas ) );
	$tpl->assignGlobal( 'ABA_END', aba_end( '') );
	// ABAS - END

	if($op!='I')
	{
		$tpl->assignGlobal('usuario_link', $u);
		$tpl->assignGlobal('codigo_link', $c);
	}

	$tpl->newBlock('cadastro');
	$tpl->assign('indic_adm', $v_indic_adm);
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);
	if ($v_indic_adm <> '*') { $tpl->assign('ro_adm', 'readonly'); }
	if (isset($u))
	{
		$sql = "";
		$sql =        " select codigo, usuario, nome, divisao, observacao, tipo, cd_cargo,  ";
		$sql = $sql . " 		formato_mensagem, e_mail_alternativo, cd_registro_empregado, skin, opt_tarefas, opt_workspace, ";
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
		$tpl->assign('email_alternativo', $reg['e_mail_alternativo']);			// garcia - 30/03/2004 - OS 2321
		$tpl->assign('forma_mens', $reg['formato_mensagem']);					// garcia - 30/03/2004 - OS 2321
		$fmens  = $reg['formato_mensagem'];									// garcia - 30/03/2004 - OS 2321
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
// ---------------------------------------------- Combo Formato mensagem
         $tpl->newBlock('form_mens');											// garcia - 30/03/2004 - OS 2321
         $tpl->assign('cod_formato', 'T');										// garcia - 30/03/2004 - OS 2321
         $tpl->assign('nome_formato', 'Texto');									// garcia - 30/03/2004 - OS 2321
         if ($fmens == 'T') { $tpl->assign('sel_formato', ' selected'); }	// garcia - 30/03/2004 - OS 2321	
         $tpl->newBlock('form_mens');											// garcia - 30/03/2004 - OS 2321
         $tpl->assign('cod_formato', 'H');										// garcia - 30/03/2004 - OS 2321
         $tpl->assign('nome_formato', 'HTML');									// garcia - 30/03/2004 - OS 2321
         if ($fmens == 'H') { $tpl->assign('sel_formato', ' selected'); }	// garcia - 30/03/2004 - OS 2321
// ---------------------------------------------- Combo Papel
      $sql =        " select codigo, descricao ";
      $sql = $sql . " from   listas ";
//      $sql = $sql . " where  categoria = 'PERF' ";
      $sql = $sql . " where  categoria = 'TPUS' ";
      $sql = $sql . " order by descricao ";
 
      $rs = pg_exec($db, $sql);
      while ($reg = pg_fetch_array($rs)) {
         $tpl->newBlock('blk_perfil');
         $tpl->assign('cod_perfil', $reg['codigo']);
         $tpl->assign('nome_perfil', $reg['descricao']);
         if ($reg['codigo'] == $perfil) { 
		    $tpl->assign('sel_perfil', ' selected'); 
		 }
      }	  
// ---------------------------------------------- Combo Cargo
      $sql =        " select 	cd_cargo, nome_cargo ";
      $sql = $sql . " from   	projetos.cargos ";
      $sql = $sql . " order	by 	nome_cargo ";
 
      $rs = pg_exec($db, $sql);
      while ($reg = pg_fetch_array($rs)) {
         $tpl->newBlock('blk_cargo');
         $tpl->assign('cod_cargo', $reg['cd_cargo']);
         $tpl->assign('nome_cargo', $reg['nome_cargo']);
         if ($reg['cd_cargo'] == $cd_cargo) { 
		    $tpl->assign('sel_cargo', ' selected'); 
		 }
      }	  
//--------------------------------------------------------------------------------- Habilidades
	  if ($_REQUEST['op'] <> 'I') 
	  {
         $sql = "";
		 $sql = $sql . " select h.codigo, h.descricao, ";
		 $sql = $sql . "        rh.grau_conhecimento ";
         $sql = $sql . " from   projetos.habilidades          h , ";
		 $sql = $sql . "        projetos.habilidades_recursos rh ";
         $sql = $sql . " where h.codigo            = rh.cod_habilidade ";
         $sql = $sql . "       and  rh.cod_recurso = $codrec ";
//         echo $sql;
         $rs = pg_exec($db, $sql);
         while ($reg = pg_fetch_array($rs)) {
            $tpl->newBlock('habilidade');
            $tpl->assign('codigo', $c);
            $tpl->assign('cod_hab', $reg['codigo']);
            $tpl->assign('habilidade', $reg['descricao']);
            $tpl->assign('grau_conhecimento', $reg['grau_conhecimento']);
         }
         $tpl->newBlock('blk_link_habilidades');
         $tpl->assign('codigo', $c);
	  }
//--------------------------------------------------------------------------------- Inicializa Senha
	if ($v_indic_adm == '*') { 
		$tpl->newBlock('blk_senha');
		$tpl->assign('codigo', $c);
	}
//---------------------------------------------------------------------------------	
   pg_close($db);
   $tpl->printToScreen();	
?>