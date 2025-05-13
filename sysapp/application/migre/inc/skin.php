<?php
date_default_timezone_set("America/Sao_Paulo");

// =================================================================================================================
//
//
// ========================================================= MÓDULO DE SEGURANÇA E CONTROLE DE ACESSO ============== (Seguration division)
//
//
// =================================================================================================================
// --------------------------------------------------------- Verifica telas com restrições de acesso: --------------
// 										'S' ==> Restrição por senha
//										'M' ==> Tela em manutenção
//										'D' ==> Restrição por divisão (gerência)
//										'U' ==> Restrição por usuário (de rede)
// -----------------------------------------------------------------------------------------------------------------
	$sql = "
			INSERT INTO projetos.log_acessos 
				 ( 	
				   cd_usuario,
				   prog,
				   dt_acesso,
				   ip 
				 )
			VALUES 
			     (
				   ".intval($_SESSION['Z']).",
				   '".$PROG."',
				   CURRENT_TIMESTAMP,
				   '".$_SERVER['REMOTE_ADDR']."'
				 )
		   ";
	@pg_query($db, $sql);

// -----------------------------------------------------------------------------------------------------------------
	$PROGATU = ''; 
	
// ================================================================================================
//
//
// ========================================================= MÓDULO DO SKIN ======================= (Enrolation division)
//
//
// ================================================================================================
// --------------------------------------------------------- inicialização do skin das telas:
	$sqlsk = "
				SELECT skin, top_menu, left_menu, caminho, tipo_imagem_menu, opt_dicas, 
				       tela_inicial, favorito2, favorito3, favorito4, favorito5, top_molduras_internas, 
				       dash1, dash2, dash3, dash4, dash5, dash6, dash7, indic_msg, texto_msg, 
				       date_trunc('day', dt_ult_login) as dt_login, to_char(dt_ult_login, 'YYYY-MM-DD') as dt_ult_login, 
				       to_char(dt_hora_scanner_computador, 'DD/MM/YYYY HH24:MI') as dt_scanner, 
					   to_char(dt_ult_login, 'DD/MM/YYYY HH24:MI') as dt_login, 
				       alt_menu_principal, espaco_superior, classe_menu, top_usuario, pos_usuario, espaco_vertical, 
				       classe_usuario, pos_menu_geral, alt_menu_geral, top_menu_geral, larg_menu_geral, 
				       bgcolor, text, link, vlink, alink, cor_fundo1, cor_fundo2, cor_fundo3, cor_fundo4, 
				       largura_submenu, cor_fundo_banner, altura_banner, classe_banner, altura_sup_banner, 
					   cor_sup_banner, altura_inf_banner, cor_inf_banner,
				       classe_links_menu, cor_fundo_links_menu, pos_menu_sites, larg_menu_sites, top_menu_sites, top_ferramentas, 
				       top_menu_mainframe, pos_menu_mainframe, top_menu_contexto, pos_menu_contexto, usu_email 
			      FROM projetos.usuarios_controledi u, projetos.skin s 
			     WHERE s.cd_skin = u.skin 
				   AND u.codigo = ".intval($_SESSION['Z'])."
			 ";	
	$rs = pg_query($db, $sqlsk);
	$regsk = pg_fetch_array($rs);
	$skin = $regsk['caminho'];
	$alt_menu = $regsk['top_menu'];
	$pos_menu = $regsk['left_menu'];	
	$espaco_celulas = 0;
//	$tpl->assigninclude('menu_geral', 'inc/menu_versao.htm');
	$tpl->assign('dt_login', 'Último login no e-prev: ' .$regsk['dt_login'].' ');
	$tpl->assign('dt_scanner', 'Último login na rede: '.$regsk['dt_scanner']);
	$tpl->assign('classe_usuario', $regsk['classe_usuario']);
	$tpl->assign('usu_email', $regsk['usu_email']);
	$tpl->assign('c_banner', 'img/' . $skin . '/banners/');	
	$tpl->assign('c_menu', 'img/' . $skin . '/menu/');
	$tpl->assign('c_skin', 'img/' . $skin . '/');
	$tpl->assign('img_fundo_pagina', 'img_fundo1.gif');
	$tpl->assign('ext_skin', $regsk['tipo_imagem_menu']);	
	$tpl->assign('alt_menu_principal', $regsk['alt_menu_principal']);
	$tpl->assign('espaco_superior', $regsk['espaco_superior']);	
	$tpl->assign('pos_menu', $pos_menu);
	$tpl->assign('classe_menu', $regsk['classe_menu']);
	$tpl->assign('classe_links_menu', $regsk['classe_links_menu']);
	$tpl->assign('cor_fundo_links_menu', $regsk['cor_fundo_links_menu']);
	$tpl->assign('cor_fundo1', $regsk['cor_fundo1']);
	//$tpl->assign('cor_fundo2', $regsk['cor_fundo2']);
	$tpl->assign('cor_fundo2', "#dae9f7");
	$tpl->assign('cor_fundo3', $regsk['cor_fundo3']);
	$tpl->assign('cor_fundo4', $regsk['cor_fundo4']);
	$v_cor_fundo1 = $regsk['cor_fundo1'];
	//$v_cor_fundo2 = $regsk['cor_fundo2'];
	$v_cor_fundo2 = "#dae9f7";
	$v_cor_fundo3 = $regsk['cor_fundo3'];
	$v_cor_fundo4 = $regsk['cor_fundo4'];
	$tpl->assign('top_ferramentas', $regsk['top_ferramentas']);
	$tpl->assign('top_frequentes', $regsk['top_ferramentas']);
	$tpl->assign('top_usuario', $regsk['top_usuario']);
	$tpl->assign('pos_usuario', $regsk['pos_usuario']);
	$tpl->assign('espaco_celulas', $espaco_celulas);
	$larg_submenu = $regsk['largura_submenu'];
	$link1 = $regsk['tela_inicial'];
	$link2 = $regsk['favorito2'];
	$link3 = $regsk['favorito3'];
	$link4 = $regsk['favorito4'];
	$link5 = $regsk['favorito5'];
	$dash1 = $regsk['dash1'];
	$dash2 = $regsk['dash2'];
	$dash3 = $regsk['dash3'];
	$dash4 = $regsk['dash4'];
	$dash5 = $regsk['dash5'];
	$dash6 = $regsk['dash6'];
	$dash7 = $regsk['dash7'];
	$v_dicas = $regsk['opt_dicas'];
	$v_data_login = $regsk['dt_ult_login'];

//------------------------------------------------------------- Molduras de gadgets, planilhas, rss e mensagens do sistema
//	if ($Z == 110) {	// and programa = workspace.php...
		$tpl->assign('img_fundo_pagina', 'img_fundo1.gif');

		$tpl->assign('msg_inst', 'Mensg. Institucionais:');
		$tpl->assign('top_moldura_msg_sistema', $regsk['top_molduras_internas']);
		$tpl->assign('pos_moldura_msg_sistema', '101');
		
		$tpl->assign('msg_novidades', 'Últimas atualizações:');
		$tpl->assign('top_moldura_novidades', $regsk['top_molduras_internas']);
		$tpl->assign('pos_moldura_novidades', '551');
		
		$tpl->assign('msg_novidades', 'Últimas atualizações:');
		$tpl->assign('pos_moldura_avisos_sistema', '599');
		$tpl->assign('top_moldura_avisos_sistema', $regsk['top_molduras_internas']); //(377 - 50) = 327 
		
		$tpl->assign('pos_moldura_rss', '451');
		$tpl->assign('top_moldura_rss', $regsk['top_molduras_internas']);
		$tpl->assign('msg_rss', 'Notícias:');
		
		$tpl->assign('pos_moldura_plan', '341');
		$tpl->assign('top_moldura_plan', $regsk['top_molduras_internas']);
		$tpl->assign('msg_plan', 'Meus Documentos:');
		
		$tpl->assign('pos_moldura_gadgets', '231');
		$tpl->assign('top_moldura_gadgets', $regsk['top_molduras_internas']);
		$tpl->assign('msg_gadgets', 'Recursos externos:');
//	} else {
//		$tpl->assign('visibilidade_moldura_msg_sistema', 'hidden');
//	} 

// --------------------------------------------------------- Opção menu Horizontal:
	$tpl->assign('visibilidade_menu_horizontal', 'hidden');
// --------------------------------------------------------- Banner expandido / contraido
	if ($MOSTRAR_BANNER == 'N')
	{
		$tpl->assignGlobal('mostra_topo', 'display:none;');
		$tpl->assign('altura_banner', ' height="0" ');
		$tpl->assign('largura_banner', ' width="0" ');
		$tpl->assign('espaco_vertical', '0');
		$tpl->assign('alt_menu_principal', '0');
		$tpl->assign('top_menu_sites', '-5');
		$tpl->assign('pos_menu_geral', '0');
		$tpl->assign('larg_menu_geral', '0');
		$tpl->assign('pos_caixa_ferramentas', '0');
//		$tpl->assign('bgcolor', '#F0FFF0');
		$tpl->assign('visibilidade_menu_geral', 'hidden');						
		$tpl->assign('visibilidade_usuario', 'hidden');
		$tpl->assign('visibilidade_menu_contextual', 'hidden');		
		$tpl->assign('img_seta_esconde_menu', '<img src="img/img_mini_seta_para_baixo.gif" border="0" alt="&raquo;">');
	}
	else {
		$tpl->assign('MG2', 'menu_geral2.tpl');	
		$tpl->assign('largura_tela', '100%');
		$tpl->assign('img_fundo_sup', 'img_fundo_sup.jpg');
		$tpl->assign('img_fundo_menu', 'img_fundo_menu.jpg');
		$tpl->assign('img_seta_esconde_menu', '<img src="img/img_mini_seta_para_cima.gif" border="0" alt="&laquo;">');		
		$tpl->assign('pos_caixa_ferramentas', '500');
		$tpl->assign('text', $regsk['text']);
		$tpl->assign('link', $regsk['link']);
		$tpl->assign('vlink', $regsk['vlink']);
		$tpl->assign('alink', $regsk['alink']);
		$tpl->assign('espaco_vertical', $regsk['espaco_vertical']);
		$tpl->assign('bgcolor', $regsk['bgcolor']);
		$tpl->assign('pos_menu_geral', $regsk['pos_menu_geral']);
		$tpl->assign('alt_menu_geral', $regsk['alt_menu_geral']);
		$tpl->assign('top_menu_geral', $regsk['top_menu_geral']);
		$tpl->assign('larg_menu_geral', $regsk['larg_menu_geral']);
		$tpl->assign('top_menu_sites', $regsk['top_menu_sites']);
		$tpl->assign('altura_banner', $regsk['altura_banner']);
		$tpl->assign('cor_fundo_banner', $regsk['cor_fundo_banner']);
		$tpl->assign('classe_banner', 'titulo_branco');
		$tpl->assign('altura_sup_banner', $regsk['altura_sup_banner']);
		$tpl->assign('cor_sup_banner', $regsk['cor_sup_banner']);
		$tpl->assign('altura_inf_banner', $regsk['altura_inf_banner']);
		$tpl->assign('cor_inf_banner', $regsk['cor_inf_banner']);
	}
	$tpl->assign('pos_menu_sites', $regsk['pos_menu_sites']);
	$tpl->assign('larg_menu_sites', $regsk['larg_menu_sites']);
	$tpl->assign('pos_menu_ferramentas', ($regsk['pos_menu_sites'] + 81));
	$tpl->assign('pos_menu_workspace', ($regsk['pos_menu_sites'] + 170));
	$tpl->assign('pos_menu_dashboard', ($regsk['pos_menu_sites'] + 233));
	$tpl->assign('pos_menu_frequentes', ($regsk['pos_menu_sites'] + 277));
	$tpl->assign('pos_menu_mainframe', $regsk['pos_menu_mainframe']);
	$tpl->assign('top_menu_mainframe', $regsk['top_menu_mainframe']);
//-----------------------------------------------------------

	#### DEFINE O CSS QUE SERÁ USADO ####
	$tpl->assign('skin_css', strtolower($skin));

	include_once( 'menu-extjs.php' );

	$tpl->assignGlobal( 'menu_extjs_start_8', menu_extjs_start( 8, $db ) );
	$tpl->assignGlobal( 'menu_extjs_start_40', menu_extjs_start( 40, $db ) );
	$tpl->assignGlobal( 'menu_extjs_start_4', menu_extjs_start( 4, $db ) );
	$tpl->assignGlobal( 'menu_extjs_start_29', menu_extjs_start( 29, $db ) );
	$tpl->assignGlobal( 'menu_extjs_start_281', menu_extjs_start( 281, $db ) );
	$tpl->assignGlobal( 'menu_extjs_start_16', menu_extjs_start( 16, $db ) );
	$tpl->assignGlobal( 'menu_extjs_start_31', menu_extjs_start( 31, $db ) );

	$tpl->assignGlobal( 'eprev_url', base_url_eprev() );
	$tpl->assignGlobal( 'cieprev_url', base_url() );
	$tpl->assignGlobal( 'cieprev_passport', md5($_SESSION["Z"].$_SESSION["U"]) );
	$tpl->assignGlobal( 'cieprev_passport2', md5($_SESSION["Z"].$_SESSION["U"]) );

	$tpl->assign('pos_menu1', ($pos_menu + 16));
	$tpl->assign('pos_menu1d', ($pos_menu + 182)); 	// direita de 1
	$tpl->assign('pos_menu2', ($pos_menu + 106));
	$tpl->assign('pos_menu2d', ($pos_menu + 272));
	$tpl->assign('pos_menu3', ($pos_menu + 187));
	$tpl->assign('pos_menu3d', ($pos_menu + 353)); 	// direita de 3
	$tpl->assign('pos_menu3e', ($pos_menu + 37 + (150 - $larg_submenu))); 	// esquerda de 3
	$tpl->assign('pos_menu4', ($pos_menu + 242));
	$tpl->assign('pos_menu4d', ($pos_menu + 408));
	$tpl->assign('pos_menu5', ($pos_menu + 302));
	$tpl->assign('pos_menu5d', ($pos_menu + 468));	// direita de 5
	$tpl->assign('pos_menu6', ($pos_menu + 350));	// 371
	$tpl->assign('pos_menu6d', ($pos_menu + 517));	// direita de 6
	$tpl->assign('pos_menu7', ($pos_menu + 410));	//427
	$tpl->assign('pos_menu7d', ($pos_menu + 576));	// direita de 7
	$tpl->assign('pos_menu7e', ($pos_menu + 261 + (150 - $larg_submenu))); 	// esquerda de 3	
	$tpl->assign('pos_menu8', ($pos_menu + 528));	// 507
	$tpl->assign('pos_menu8d', ($pos_menu + 684));	// direita de 8	
	$tpl->assign('pos_menu9', ($pos_menu + 470));	// 507
	$tpl->assign('pos_menu10', ($pos_menu + 510));	// 507
//-----------------------------------------------------------
	$tpl->assign('alt_menu', $alt_menu);
	$tpl->assign('alt_menu1', ($alt_menu + 32));
	$tpl->assign('alt_menu2', ($alt_menu + 49 + (1 * $espaco_celulas)));
	$tpl->assign('alt_menu2b', ($alt_menu + 51 + (1 * $espaco_celulas)));
	$tpl->assign('alt_menu3', ($alt_menu + 66 + (2 * $espaco_celulas)));
	$tpl->assign('alt_menu4', ($alt_menu + 83 + (3 * $espaco_celulas)));
	$tpl->assign('alt_menu4b', ($alt_menu + 95 + (3 * $espaco_celulas)));
	$tpl->assign('alt_menu5', ($alt_menu + 100 + (4 * $espaco_celulas)));
	$tpl->assign('alt_menu5b', ($alt_menu + 112 + (4 * $espaco_celulas)));
	$tpl->assign('alt_menu6', ($alt_menu + 117));
	$tpl->assign('alt_menu6b', ($alt_menu + 129));
	$tpl->assign('alt_menu7', ($alt_menu + 134));
	$tpl->assign('alt_menu7b', ($alt_menu + 146));
	$tpl->assign('alt_menu8', ($alt_menu + 151));
	$tpl->assign('alt_menu8b', ($alt_menu + 163));
	$tpl->assign('alt_menu9', ($alt_menu + 168));
	$tpl->assign('alt_menu9b', ($alt_menu + 180));
	$tpl->assign('alt_menu10', ($alt_menu + 185));
	$tpl->assign('alt_menu10b', ($alt_menu + 197));
	$tpl->assign('alt_menu10c', ($alt_menu + 209));
	$tpl->assign('alt_menu11', ($alt_menu + 202));
	$tpl->assign('alt_menu11b', ($alt_menu + 214));			// classe b => menus com 1 barra divisória
	$tpl->assign('alt_menu11c', ($alt_menu + 226));			// classe b => menus com 1 barra divisória
	$tpl->assign('alt_menu12', ($alt_menu + 219));
	$tpl->assign('alt_menu12b', ($alt_menu + 231));
	$tpl->assign('alt_menu12c', ($alt_menu + 243));
	$tpl->assign('alt_menu13b', ($alt_menu + 248));
	$tpl->assign('alt_menu14b', ($alt_menu + 265));
	$tpl->assign('alt_menu14c', ($alt_menu + 260));
	$tpl->assign('alt_menu15b', ($alt_menu + 282));
	$tpl->assign('alt_menu15c', ($alt_menu + 277));			// classe c => menus com 2 barras divisórias
	$tpl->assign('alt_menu16b', ($alt_menu + 299));
	$tpl->assign('alt_menu17b', ($alt_menu + 316));

	$tpl->assign('alt_menu23b', ($alt_menu + 413));
// --------------------------------------------------------- Submenus de terceiro nível: 167 / 150
	$tpl->assign('pos_submenu2d', ($pos_menu + $larg_submenu + 182));
	$tpl->assign('pos_submenu2_e_meio_d', ($pos_menu + $larg_submenu + 270));
	$tpl->assign('pos_submenu3d', ($pos_menu + $larg_submenu + 352));
	$tpl->assign('pos_submenu5d', ($pos_menu + $larg_submenu + 468));
	$tpl->assign('pos_submenu7d', ($pos_menu + $larg_submenu + 515));
	$tpl->assign('pos_submenu9d', ($pos_menu + $larg_submenu + 575));
	$tpl->assign('pos_submenu8e', ($pos_menu + 112 + (300 - (2 * $larg_submenu)))); 	// esquerda de 3
	$tpl->assign('top_mctx', $regsk['top_menu_contexto']);
	$tpl->assign('pos_mctx', $regsk['pos_menu_contexto']);
// ================================================================================================
//
//
// ========================================================= MÓDULO DE INTERAÇÃO COM USUÁRIOS ===== (Integration division)
//
//
// ================================================================================================
// --------------------------------------------------------- Mensagem aos usuários:
	if ($regsk['indic_msg'] == 'C') {
//		$v_msg = '<table border="0" width="195" bgcolor="'.$regsk['cor_fundo2'].'" cellspacing="1" cellpadding="3">';
// 		$v_msg = $v_msg . '<tr>';
//		$v_msg = $v_msg . '<td width="195" bgcolor="'.$regsk['cor_fundo2'].'"  background="img/img_fundo_barra.jpg">';
//		$v_msg = $v_msg . '<p align="center"><b><font face="Verdana" size="2">Mensagem do Sistema</font></b></td>';
//		$v_msg = $v_msg . '</tr>';
//		$v_msg = $v_msg . '<tr>';
//		$v_msg = $v_msg . '<td width="195" bgcolor="'.$regsk['cor_fundo1'].'">';
//		$v_msg = $v_msg . '<p align="center"><font face="Verdana" size="1"><b>'.$regsk['texto_msg'].'</b></font></td>';
//		$v_msg = $v_msg . '</tr>';
//		$v_msg = $v_msg . '</table>';
		$v_msg = '<img src="img/img_copa.jpg" border="0">';
		$tpl->assign('mensagem_principal', $v_msg);
		$tpl->assign('larg_msgp', '100');
		$tpl->assign('alt_msgp', '20');
		$tpl->assign('pos_msgp', ($regsk['pos_menu_sites'] + 70));
		$tpl->assign('top_msgp', '27');
		$tpl->assign('z_index_msgp', '0');
		$sql =        " update projetos.usuarios_controledi ";
		$sql = $sql . " set 	indic_msg = 'N' ";
		$sql = $sql . " where codigo = ".intval($_SESSION['Z'])." ";
		$rs = pg_exec($db, $sql);
	}	
// ------------------------------------------------------------- Enquete
	$sqle = "
				SELECT COUNT(*) AS num_regs 
				  FROM projetos.usuarios_enquetes 
				 WHERE cd_enquete = 2 
				   AND cd_usuario = ".intval($_SESSION['Z'])."
			";
	$rs = pg_query($db, $sqle);
	$rege = pg_fetch_array($rs);

	if ($rege['num_regs'] == 0) {
	
		if ($IND_ENQ == 'N') {			
			$tpl->assign('pos_banner_enquete', '200');
			$tpl->assign('top_banner_enquete', '200');
			$sqle = "select to_char(dt_inicio, 'DD/MM/YYYY') as dt_inicio, to_char(dt_fim, 'DD/MM/YYYY') as dt_fim, dt_fim as fim from projetos.enquetes where cd_enquete = 2";
			$rs = pg_exec($db, $sqle);
			$rege = pg_fetch_array($rs);
			if ($rege['fim']  < date('Y-m-d')) {
				$tpl->assign('vis_enquete', 'hidden');
			}
			else {
				$tpl->assign('dt_inicio_enquete', $rege['dt_inicio']);
				$tpl->assign('dt_fim_enquete', $rege['dt_fim']);			
				$IND_ENQ = 'S';
			}
		}
		else {
			$tpl->assign('vis_enquete', 'hidden');	
		}
	}
	else {
		$tpl->assign('vis_enquete', 'hidden');	
	}
	
//	if ($D == 'DI') {
//	}
//	else {
//		$tpl->assign('vis_enquete', 'hidden');	
//	}
//----------------------------------------------------------- Menu contextual
// ---------------- Tela inicial
	$sqlf = "select desc_programa from projetos.telas_eprev where nome_programa = '" . $link1 . "'";
	$rs = pg_exec($db, $sqlf);
	$regf = pg_fetch_array($rs);
	$menu_contextual = '<a href="' . $link1 .'" class="'.$regsk['classe_usuario'].'">Página inicial</a>';
// ---------------- Tela anterior
	if (($HIST_CAMINHO != '') and ($HIST_CAMINHO != $link1)) {
		$sqlf = "select desc_programa from projetos.telas_eprev where nome_programa like '" . $HIST_CAMINHO . "%'";
		$rs = pg_exec($db, $sqlf);
		$regf = pg_fetch_array($rs);
//		$menu_contextual = $menu_contextual . '<span class="'.$regsk['classe_usuario'].'"> > </span><a href="' . $HIST_CAMINHO .'" class="'.$regsk['classe_usuario'].'">'.$regf['desc_programa'].'</a>';
		$menu_contextual = $menu_contextual . '<span class="'.$regsk['classe_usuario'].'"> > </span><a href="' . $HIST_CAMINHO .'" class="'.$regsk['classe_usuario'].'">Tela anterior</a>';
	}
// ---------------- Tela atual	
	$arquivo = basename(basename($_SERVER['PHP_SELF'])); 
	if (($HIST_CAMINHO != '') and ($arquivo != $HIST_CAMINHO)) {
		$sqlf = "select desc_programa from projetos.telas_eprev where nome_programa like '" . $arquivo . "%'";
		$rs = pg_exec($db, $sqlf);
		$regf = pg_fetch_array($rs);
//		$menu_contextual = $menu_contextual . '<span class="'.$regsk['classe_usuario'].'">  </span><a href="' . $arquivo .'" class="'.$regsk['classe_usuario'].'">'.$regf['desc_programa'].'</a>';
	}
// ---------------- Propriedades do menu contextual
	$tpl->assign('z_index_mctx',  '1');
	$tpl->assign('larg_mctx',  '350');
	$tpl->assign('alt_mctx',  '30');		
	$tpl->assign('menu_contextual',  $menu_contextual);
	$HIST_CAMINHO = basename(basename($_SERVER['PHP_SELF']));
// --------------------------------------------------------- Favoritos
	if ($link1 != '') {
		$sqlf = "select desc_programa from projetos.telas_eprev where nome_programa = '" . $link1 . "'";
		$rs = pg_exec($db, $sqlf);
		$regf = pg_fetch_array($rs);
		$tpl->assign('link1', $link1);
		$tpl->assign('opt_workspace1', ':: ' . $regf['desc_programa']);
	}
	if ($link2 != '') {
		$sqlf = "select desc_programa from projetos.telas_eprev where nome_programa = '" . $link2 . "'";
		$rs = pg_exec($db, $sqlf);
		$regf = pg_fetch_array($rs);
		$tpl->assign('link2', $link2);
		$tpl->assign('opt_workspace2', ':: ' . $regf['desc_programa']);
	}
	if ($link3 != '') {
		$sqlf = "select desc_programa from projetos.telas_eprev where nome_programa = '" . $link3 . "'";
		$rs = pg_exec($db, $sqlf);
		$regf = pg_fetch_array($rs);
		$tpl->assign('link3', $link3);
		$tpl->assign('opt_workspace3', ':: ' . $regf['desc_programa']);
	}
	if ($link4 != '') {
		$sqlf = "select desc_programa from projetos.telas_eprev where nome_programa = '" . $link4 . "'";
		$rs = pg_exec($db, $sqlf);
		$regf = pg_fetch_array($rs);
		$tpl->assign('link4', $link4);
		$tpl->assign('opt_workspace4', ':: ' . $regf['desc_programa']);
	}
	if ($link5 != '') {
		$sqlf = "select desc_programa from projetos.telas_eprev where nome_programa = '" . $link5 . "'";
		$rs = pg_exec($db, $sqlf);
		$regf = pg_fetch_array($rs);
		$tpl->assign('link5', $link5);
		$tpl->assign('opt_workspace5', ':: ' . $regf['desc_programa']);
	}
// --------------------------------------------------------- Dashboard
	if ($dash1 != '') {
		$sqlf = "select nome_programa, desc_programa, altura, largura from projetos.telas_eprev where nome_programa = '" . $dash1 . "'";
		$rs = pg_exec($db, $sqlf);
		$regf = pg_fetch_array($rs);
		$tpl->assign('linkd1', $dash1);
		$tpl->assign('opt_dash1', ':: ' . $regf['desc_programa']);
		$tpl->assign('a1', $regf['altura']);
		$tpl->assign('l1', $regf['largura']);
		$tpl->assign('p1', $regf['nome_programa']);
	}
	else {
		$tpl->assign('linkd1', 'x');
		$tpl->assign('a1', '277');
		$tpl->assign('l1', '277');
	}
	if ($dash2 != '') {
		$sqlf = "select nome_programa, desc_programa, altura, largura from projetos.telas_eprev where nome_programa = '" . $dash2 . "'";
		$rs = pg_exec($db, $sqlf);
		$regf = pg_fetch_array($rs);
		$tpl->assign('linkd2', $dash2);
		$tpl->assign('opt_dash2', ':: ' . $regf['desc_programa']);
		$tpl->assign('a2', $regf['altura']);
		$tpl->assign('l2', $regf['largura']);
		$tpl->assign('p2', $regf['nome_programa']);
	}
	else {
		$tpl->assign('linkd2', 'x');
		$tpl->assign('a2', '277');
		$tpl->assign('l2', '277');
	}
	if ($dash3 != '') {
		$sqlf = "select nome_programa, desc_programa, altura, largura from projetos.telas_eprev where nome_programa = '" . $dash3 . "'";
		$rs = pg_exec($db, $sqlf);
		$regf = pg_fetch_array($rs);
		$tpl->assign('linkd3', $dash3);
		$tpl->assign('opt_dash3', ':: ' . $regf['desc_programa']);
		$tpl->assign('a3', $regf['altura']);
		$tpl->assign('l3', $regf['largura']);
		$tpl->assign('p3', $regf['nome_programa']);
	}
	else {
		$tpl->assign('linkd3', 'x');
		$tpl->assign('a3', '277');
		$tpl->assign('l3', '277');
	}
	if ($dash4 != '') {
		$sqlf = "select nome_programa, desc_programa, altura, largura from projetos.telas_eprev where nome_programa = '" . $dash4 . "'";
		$rs = pg_exec($db, $sqlf);
		$regf = pg_fetch_array($rs);
		$tpl->assign('linkd4', $dash4);
		$tpl->assign('opt_dash4', ':: ' . $regf['desc_programa']);
		$tpl->assign('a4', $regf['altura']);
		$tpl->assign('l4', $regf['largura']);
		$tpl->assign('p4', $regf['nome_programa']);
	}
	else {
		$tpl->assign('linkd4', 'x');
		$tpl->assign('a4', '277');
		$tpl->assign('l4', '277');
	}
	if ($dash5 != '') {
		$sqlf = "select nome_programa, desc_programa, altura, largura from projetos.telas_eprev where nome_programa = '" . $dash5 . "'";
		$rs = pg_exec($db, $sqlf);
		$regf = pg_fetch_array($rs);
		$tpl->assign('linkd5', $dash5);
		$tpl->assign('opt_dash5', ':: ' . $regf['desc_programa']);
		$tpl->assign('a5', $regf['altura']);
		$tpl->assign('l5', $regf['largura']);
		$tpl->assign('p5', $regf['nome_programa']);
	}
	else {
		$tpl->assign('linkd5', 'x');
		$tpl->assign('a5', '277');
		$tpl->assign('l5', '277');
	}
	if ($dash6 != '') {
		$sqlf = "select nome_programa, desc_programa, altura, largura from projetos.telas_eprev where nome_programa = '" . $dash6 . "'";
		$rs = pg_exec($db, $sqlf);
		$regf = pg_fetch_array($rs);
		$tpl->assign('linkd6', $dash6);
		$tpl->assign('opt_dash6', ':: ' . $regf['desc_programa']);
		$tpl->assign('a6', $regf['altura']);
		$tpl->assign('l6', $regf['largura']);
		$tpl->assign('p6', $regf['nome_programa']);
	}
	else {
		$tpl->assign('linkd6', 'x');
		$tpl->assign('a6', '277');
		$tpl->assign('l6', '277');
	}
	if ($dash7 != '') {
		$sqlf = "select nome_programa, desc_programa, altura, largura from projetos.telas_eprev where nome_programa = '" . $dash7 . "'";
		$rs = pg_exec($db, $sqlf);
		$regf = pg_fetch_array($rs);
		$tpl->assign('linkd7', $dash7);
		$tpl->assign('opt_dash7', ':: ' . $regf['desc_programa']);
		$tpl->assign('a7', $regf['altura']);
		$tpl->assign('l7', $regf['largura']);
		$tpl->assign('p7', $regf['nome_programa']);
	}
	else {
		$tpl->assign('linkd7', 'x');
		$tpl->assign('a7', '277');
		$tpl->assign('l7', '277');
	}
// ---------------------------------------------------------- Mais utilizados: (14/03/2007)
	/*
	$sqlf = "select count(*) as nr, l.prog, t.desc_programa  from projetos.log_acessos l, projetos.telas_eprev t where cd_usuario = ".intval($_SESSION['Z'])." and l.prog = t.nome_programa group by prog, t.desc_programa order by nr desc limit 10 ";
	$rs = pg_exec($db, $sqlf);
	$vlink = 0;
	while ($regf = pg_fetch_array($rs)){
		$vlink = ($vlink + 1);
		$tpl->assign('link_freq'.$vlink, $regf['prog']);
		$tpl->assign('nome_freq'.$vlink, ':: ' . $regf['desc_programa']);
	}
	*/
// --------------------------------------------------------- Dicas: (tem que ser variável de sessão para mostrar somente uma vez)
	$v_ip = $_SERVER['REMOTE_ADDR'];
	if ($v_data_login != date('Y-m-d')) {
		$sql =        " update projetos.usuarios_controledi ";
		$sql = $sql . " set estacao_trabalho = '" . $v_ip . "' , ";
		$sql = $sql . " 	dt_ult_login = current_timestamp ";
		$sql = $sql . " where codigo = ".intval($_SESSION['Z'])." ";
		if ($rs = pg_exec($db, $sql)) {
		}
		if ($v_dicas == 'S') {
			echo '<script language="JavaScript" type="text/JavaScript">
					<!--
					w = window.open("dicas.php?r=1", "wskin2", "menubar=no,location=no,scrollbars=no,resizable=no,width=277,height=277");
					//-->
			  </script>';
		}
	}
?>