<?php
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   
   header('location:'.base_url().'index.php/cadastro/rh/detalhe/'.$_REQUEST['c']); EXIT;
   
   
   $tpl = new TemplatePower('tpl/tpl_cad_recurso_preferencias.html');
   $tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');
   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//------------------------------------------------ Verifica perfil do usuário atual 
	$sql = "";
	$sql =        " SELECT indic_01, indic_02, indic_03, indic_04, indic_05, indic_06, ";
	$sql = $sql . " indic_07, indic_08, indic_09, indic_10, indic_11, indic_12 ";
	$sql = $sql . " FROM projetos.usuarios_controledi  " ;
	$sql = $sql . " WHERE codigo = $Z";
	$rs = pg_exec($db, $sql);
	$reg=pg_fetch_array($rs);
	$indic_adm = $reg['indic_05'];
//---------------------------------------------------------------------------------

	// ABAS - BEGIN
	$abas[] = array('aba_pref', 'Preferências', true, 'void(0);');
	$abas[] = array('aba_perfil', 'Perfil', false, 'ir_perfil();');
	$abas[] = array('aba_work', 'Meu Workspace', false, 'ir_work();');
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
		$sql =        " select codigo, usuario, nome, divisao, observacao, tipo, cd_cargo, opt_dicas, opt_interatividade, ";
		$sql = $sql . " 		formato_mensagem, e_mail_alternativo, cd_registro_empregado, skin, opt_tarefas, opt_workspace, guerra, ";
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
		$tpl->assign('guerra', $reg['guerra']);
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
		if ($reg['opt_dicas'] == 'S') {
			 $tpl->assign('chk_dicas', 'checked');		 	
		}
		if ($reg['opt_interatividade'] == 'S') {
			 $tpl->assign('chk_interatividade', 'checked');		 	
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
		elseif  ($reg['skin'] == 'COPA') {
			 $tpl->assign('chk_copa', 'checked');		 	
		}
// ---------------------------------------------- Cargo
		if (isset($cd_cargo)) {
			$sql =        " select 	nome_cargo ";
			$sql = $sql . " from   	projetos.cargos ";
			$sql = $sql . " where cd_cargo = $cd_cargo ";
			$rs = pg_exec($db, $sql);
			$reg = pg_fetch_array($rs);
			$tpl->assign('cargo', $reg['nome_cargo']); 
		}
// ---------------------------------------------- Papel
		if ($perfil != '') {
			$sql =        " select 	descricao ";
			$sql = $sql . " from   	listas ";
			$sql = $sql . " where  	categoria = 'TPUS' ";
			$sql = $sql . " and 	codigo ='". $perfil ."'";
			$rs = pg_exec($db, $sql);
			$reg = pg_fetch_array($rs);
			$tpl->assign('papel', $reg['descricao']); 
		}
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
//--------------------------------------------------------------------------------- Habilidades
	  if (($_REQUEST['op'] <> 'I') and ($codrec != '')) 
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
	if ($indic_adm == '*') { 
		$tpl->newBlock('blk_senha');
		$tpl->assign('codigo', $c);
	}
//---------------------------------------------------------------------------------	
   pg_close($db);
   $tpl->printToScreen();	
?>