<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   
   header( 'location:'.base_url().'index.php/gestao/processo/cadastro/'.(isset($c) ? $c : ''));
   
//---------------------------------------------------------------------------------------
// CAD_PROCESSOS.PHP
// Manutenчѕes de Processos, sub-processos e procedimentos.
//---------------------- Alteraчѕes nesta pсgina: (por favor, registre aqui as alteraчѕes realizadas)
// garcia - 09/03/2004 - procedimentos
// garcia - 08/03/2004 - responsavel e envolvidos no processo
// garcia - 10/02/2004 - novos campos de processos
//---------------------------------------------------------------------------------------
   $tpl = new TemplatePower('tpl/tpl_cad_processos.html');

   $tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');
   
   $tpl->prepare();
   $tpl->assign('n', $n);
   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
   	$tpl->assign('divsao', $D);

   	$tpl->assign('link_lista', site_url('gestao/processo'));
   	
   
//   if ($T=='G' or $T == 'D') { // Se for Gerente, pode cadastrar
      $tpl->newBlock('cadastro');
      if (isset($c))	{
         $sql =        " select pp.cd_processo as processo, pp.desc_proc as descricao, to_char(pp.data, 'DD/MM/YYYY') as data_cad,";
		 $sql = $sql . "        pp.procedimento as nome, pp.cod_responsavel, pp.cod_responsavel as codigo, ";	// garcia - 09/03/2004
		 $sql = $sql . "	 	pp.objetivo as objetivo, pp.insumos as insumos, pp.produtos as produtos, pp.cd_processo_pai as cd_processo_pai, "; // garcia - 09/02/2004 e 08/03/2004
		 $sql = $sql . "        pp.envolvidos as envolvidos, pp.sub_processos as sub_processos, pp.requisitos_aplicaveis as requisitos_aplicaveis "; // garcia - 09/03/2004
         $sql = $sql . " from   projetos.processos pp, ";
		 $sql = $sql . "        projetos.usuarios_controledi u, ";
		 $sql = $sql . "        projetos.divisoes d ";
		 $sql = $sql . " where  ((pp.cod_responsavel = u.codigo::text) or (pp.cod_responsavel = d.codigo)) and pp.cd_processo = $c ";
// echo $sql;
         $rs = pg_exec($db, $sql);
         $reg=pg_fetch_array($rs);

		 $tpl->assign('processo',   $reg['nome']);
		 $tpl->assign('cod_processo',   $c);
         $tpl->assign('responsavel',      $reg['responsavel']);
         $tpl->assign('descricao', $reg['descricao']);
		 $tpl->assign('processo_pai', $reg['cd_processo_pai']);		// garcia - 09/02/2004
		 $tpl->assign('objetivo', $reg['objetivo']);				// garcia - 09/02/2004
		 $tpl->assign('insumos', $reg['insumos']);					// garcia - 09/02/2004
		 $tpl->assign('produtos', $reg['produtos']);				// garcia - 09/02/2004
 		 $tpl->assign('envolvidos', $reg['envolvidos']);			// garcia - 08/03/2004
 		 $tpl->assign('requisitos', $reg['requisitos_aplicaveis']);	// garcia - 17/03/2004
 		 $tpl->assign('sub_processos', $reg['sub_processos']);		// garcia - 17/03/2004
         $tpl->assign('data_cad', $reg['data_cad']);
		 $tpl->assign('insere', $n);
		 $cod_processo = $reg['codigo'];
		 $processo_pai = $reg['cd_processo_pai'];				// garcia - 09/02/2004
 		 $v_envolvidos = $reg['envolvidos'];					// garcia - 17/03/2004
//		echo $v_envolvidos;
//		echo $reg['cd_processo_pai'];		 
//		echo $processo_pai;
      }
	  if (isset($tr)) {
	     if ($tr == 'U') {
		    $n = 'U';
		 }
		 else {
		   $n = 'I';
		 }
	   }	
	  $tpl->assign('insere', $n);
//---------------------------------------------------- Responsсvel pelo projeto - COLABORADORES E GERENTES
      $sql =        " select codigo as codigo_resp, nome ";
      $sql = $sql . " from   projetos.usuarios_controledi  where tipo not in ('X', 'P', 'T')";
      $sql = $sql . " order by nome ";
//      echo $sql;
	  $rs = pg_exec($db, $sql);
      while ($reg=pg_fetch_array($rs)) {
         $tpl->newBlock('responsavel');
         $tpl->assign('codigo_resp', $reg['codigo_resp']);
         $tpl->assign('responsavel', $reg['nome']);
         if ($reg['codigo_resp'] == $cod_processo) { $tpl->assign('sel_responsavel', ' selected'); }
      }
//---------------------------------------------------- Responsсvel pelo projeto - ASSESSORIAS E DIVISеES:
      $sql =        " select codigo as codigo_resp, nome ";
      $sql = $sql . " from   projetos.divisoes ";
      $sql = $sql . " order by nome ";
//      echo $sql;
	  $rs = pg_exec($db, $sql);
      while ($reg=pg_fetch_array($rs)) {
         $tpl->newBlock('responsavel');
         $tpl->assign('codigo_resp', $reg['codigo_resp']);
         $tpl->assign('responsavel', $reg['nome']);
         if ($reg['codigo_resp'] == $cod_processo) { $tpl->assign('sel_responsavel', ' selected'); }
      }
 
//--------------------------------------------------- PROJETO PAI  
      $tpl->newBlock('processo_pai');											// garcia - 09/02/2004
      $tpl->assign('cd_processo_pai', ' selected');								// garcia - 09/02/2004	
      $tpl->assign('processo_pai', "");											// garcia - 09/02/2004

      $sql =        " select cd_processo, procedimento ";						// garcia - 09/02/2004
      $sql = $sql . " from   projetos.processos ";								// garcia - 09/02/2004
	  $sql = $sql . " where cd_processo_pai is null ";							// garcia - 09/02/2004
      $sql = $sql . " order by procedimento ";									// garcia - 09/02/2004

	  $rs = pg_exec($db, $sql);													// garcia - 09/02/2004			
      while ($reg=pg_fetch_array($rs)) 											// garcia - 09/02/2004	
	  {																			// garcia - 09/02/2004		
         $tpl->newBlock('processo_pai');										// garcia - 09/02/2004
         $tpl->assign('cd_proc_pai', $reg['cd_processo']);						// garcia - 09/02/2004	
         $tpl->assign('processo_pai', $reg['procedimento']);					// garcia - 09/02/2004
         if ($reg['cd_processo'] == $processo_pai) 								// garcia - 09/02/2004
		 { 																		// garcia - 09/02/2004
//	 		echo $reg['cd_processo'];											// garcia - 09/02/2004
//			echo $processo_pai;													// garcia - 09/02/2004
//		 	echo igual;															// garcia - 09/02/2004
		 	$tpl->assign('sel_processo_pai', ' selected'); 						// garcia - 09/02/2004
		 }																		// garcia - 09/02/2004
      }																			// garcia - 09/02/2004

//-------------------------------------------------- ENVOLVIDOS NO PROCESSO - USUСRIOS
//      $sql =        " select codigo as codigo_env, nome ";						// garcia - 09/03/2004
//      $sql = $sql . " from   projetos.usuarios_controledi ";					// garcia - 09/03/2004
//      $sql = $sql . " order by nome ";											// garcia - 09/03/2004
//      echo $sql;
//	  $rs = pg_exec($db, $sql);													// garcia - 09/03/2004
//      while ($reg=pg_fetch_array($rs)) {										// garcia - 09/03/2004
//         $tpl->newBlock('envolvido_b');											// garcia - 09/03/2004
//         $tpl->assign('codigo_env', $reg['codigo_env']);						// garcia - 09/03/2004
//         $tpl->assign('envolvido', $reg['nome']);								// garcia - 09/03/2004//
// 		 $pos = strpos ($v_envolvidos, $reg['codigo_env']);						// garcia - 17/03/2004
//		 if ($pos === false) {													// garcia - 17/03/2004
//			}																	// garcia - 17/03/2004
//		 else {																	// garcia - 17/03/2004
//			{ $tpl->assign('sel_envolvido', ' selected'); }	} 					// garcia - 17/03/2004      }
//     }																			// garcia - 17/03/2004
//---------------------------------------------------- Envolvidos no projeto - ASSESSORIAS E DIVISеES:
      $sql =        " select codigo as codigo_env, nome ";						// garcia - 17/03/2004
      $sql = $sql . " from   projetos.divisoes ";								// garcia - 17/03/2004		
      $sql = $sql . " order by nome ";											// garcia - 17/03/2004	
//      echo $sql;
	  $rs = pg_exec($db, $sql);													// garcia - 17/03/2004
      while ($reg=pg_fetch_array($rs)) {										// garcia - 17/03/2004
         $tpl->newBlock('envolvido_b');											// garcia - 17/03/2004
         $tpl->assign('codigo_env', $reg['codigo_env']);						// garcia - 17/03/2004
         $tpl->assign('envolvido', $reg['nome']);								// garcia - 17/03/2004
 		 $pos = strpos ($v_envolvidos, $reg['codigo_env']);						// garcia - 17/03/2004
		 if ($pos === false) {													// garcia - 17/03/2004
			}																	// garcia - 17/03/2004
		 else {																	// garcia - 17/03/2004
			{ $tpl->assign('sel_envolvido', ' selected'); }	} 					// garcia - 17/03/2004
      }																			// garcia - 17/03/2004
//---------------------------------------------------   
//  }
//   else {
//      $tpl->newBlock('mensagem');
//	  $tpl->assign('msg', 'Somente gerentes podem cadastrar ou alterar projetos');
//   }
//-------------------------------------------------- 	PROCEDIMENTOS DESTE PROCESSO:
   if (isset($c))	{ 
   	$sql =   " ";																// garcia - 09/03/2004	
   	$sql = $sql . "  select 	distinct pp.cd_processo as codigo, 			";	// garcia - 09/03/2004			
   	$sql = $sql . "  		pp.cod_responsavel, pp.objetivo as objetivo, 	";	// garcia - 09/03/2004
   	$sql = $sql . "			pp.procedimento as nome,  						"; 	// garcia - 09/03/2004
   	$sql = $sql . "         	to_char(pp.data,'dd/mm/yyyy') as dt_inclusao";	// garcia - 09/03/2004
   	$sql = $sql . "  from projetos.processos           pp,                 	";	// garcia - 09/03/2004						
   	$sql = $sql . "       projetos.usuarios_controledi u,                   ";	// garcia - 09/03/2004
   	$sql = $sql . "		 projetos.divisoes d								";	// garcia - 09/03/2004	
   	$sql = $sql . "  where  pp.cd_processo_pai = $c 						";	// garcia - 09/03/2004	
   	$sql = $sql . "  order by pp.procedimento	            				";	// garcia - 09/03/2004	
//	echo $sql;																	// garcia - 09/03/2004
   	$rs = pg_exec($sql);														// garcia - 09/03/2004
   	while ($reg=pg_fetch_array($rs))											// garcia - 09/03/2004
   	{																			// garcia - 09/03/2004
	  	$tpl->newBlock('procedimentos');										// garcia - 09/03/2004		
	  	$tpl->assign('codigo_proced', $reg['codigo']);							// garcia - 09/03/2004
	  	$tpl->assign('nome_proced', $reg['nome']);								// garcia - 09/03/2004
	  	$tpl->assign('objetivo_proced', $reg['objetivo']);						// garcia - 09/03/2004			
	  	$tpl->assign('dt_inclusao_proced', $reg['dt_inclusao']);				// garcia - 09/03/2004
   	}																			// garcia - 09/03/2004
   }																			// garcia - 09/03/2004
//-------------------------------------------------- 	INSTRUЧеES DE TRABALHO:   
	  	$tpl->newBlock('its');										// garcia - 09/03/2004		
//--------------------------------------------------    
   
   pg_close($db);
   $tpl->printToScreen();	
?>