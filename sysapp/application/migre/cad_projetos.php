<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   
        
   
	$tpl = new TemplatePower('tpl/tpl_cad_projetos.html');
	$tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);   
        
        
	
	
	$tpl->newBlock('cadastro');

	if ($tp == 'S') {
		$tpl->assignGlobal('tipo_proj', 'sistemas');
		$tpl->assign('chk_sistema',  'checked');
                
                header( 'location:'.base_url().'index.php/cadastro/sistema/detalhe/'.$c);
	}
	else {
		$tpl->assignGlobal('tipo_proj', 'projetos');
		$tpl->assign('chk_projeto',  'checked');
                
                header( 'location:'.base_url().'index.php/cadastro/projeto/detalhe/'.$c);
	}
// ---------------------------------------------------------
	
	$tpl->assign('tela_voltar', $_SERVER['HTTP_REFERER']);
	$tpl->assignGlobal('link_projetos', site_url('cadastro/projeto'));
	$tpl->assign('tp',   $tp);
	if (isset($c))	
	{
		$sql =        " select codigo, nome, descricao, analista_responsavel, programa_institucional, ";
		$sql = $sql . "        area, nivel, administrador1, administrador2, ";
		$sql = $sql . "        atendente, to_char(data_cad, 'DD/MM/YYYY') as data_cad,";
		$sql = $sql . "        cod_projeto_superior, tipo, ";
		$sql = $sql . "        diretriz, to_char(data_implantacao, 'DD/MM/YYYY') as data_implantacao ";
		$sql = $sql . " from   projetos.projetos ";
		$sql = $sql . " where  codigo   = $c ";
		if ($Z == 191) {
		}	
		else {
			$sql = $sql . "       and (area='$D' or atendente='$U' or analista_responsavel = '$U') " ;
		}
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$pai = $reg['cod_projeto_superior'];
		if ($reg['tipo']  == 'S') {
			$tpl->assign('chk_sistema',  'checked');
		} else {
			$tpl->assign('chk_projeto',  'checked');
		}
		$tpl->assign('codproj',   $reg['codigo']);
		$tpl->assign('codigo',    $reg['codigo']);
		$tpl->assign('nome',      $reg['nome']);
		$tpl->assign('descricao', $reg['descricao']);
		$tpl->assign('data_implantacao', $reg['data_implantacao']);
		$cod_projeto = $reg['codigo'];		 
		$nivel = $reg['nivel'];
		$administrador1 = $reg['administrador1'];
		$administrador2 = $reg['administrador2'];
		$atendente = $reg['atendente'];
		$analista_responsavel = $reg['analista_responsavel'];
		$diretriz = $reg['diretriz'];
		$area = $reg['area'];
		$programa_institucional = $reg['programa_institucional'];		
// --------------------------------------------------------- Acompanhamento de projeto 
		$sql = "";
		$sql = $sql . " select 	cd_acomp";
		$sql = $sql . " from	projetos.acompanhamento_projetos ";
		$sql = $sql . " where 	cd_projeto = $c ";
		$rs = pg_exec($db, $sql);
		if ($reg=pg_fetch_array($rs)) {
			$tpl->newBlock('acomp');
			$tpl->assign('cd_acompanhamento', $reg['cd_acomp']);
		}
	}
	
// --------------------------------------------------------- Divisões 
	$sql = "";
	$sql = $sql . " select 	codigo, nome";
	$sql = $sql . " from   	projetos.divisoes ";
	$sql = $sql . " order 	by nome ";
	$rs = pg_exec($db, $sql);
     
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('area');
		$tpl->assign('cod_area', $reg['codigo']);
		$tpl->assign('nome_area', $reg['nome']);
		if ($reg['codigo'] == $area) { $tpl->assign('sel_area', ' selected'); }
	}
// --------------------------------------------------------- Combo Nível
	$sql =        " select codigo, descricao ";
	$sql = $sql . " from   listas ";
	$sql = $sql . " where  categoria = 'NIVL' ";
	$sql = $sql . " order by codigo ";
	$rs = pg_exec($db, $sql);
     
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('nivel');
		$tpl->assign('cod_nivel', $reg['codigo']);
		$tpl->assign('nome_nivel', $reg['descricao']);
		if ($reg['codigo'] == $nivel) { $tpl->assign('sel_nivel', ' selected'); }
	}     
// --------------------------------------------------------- Combo Adminstrador1
	$sql =        " select usuario, nome ";
	$sql = $sql . " from   projetos.usuarios_controledi where tipo not in ('X', 'P', 'T')";
	$sql = $sql . " order by nome ";      
	$rs = pg_exec($db, $sql);
	  
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('administrador1');
		$tpl->assign('cod_admin1', $reg['usuario']);
		$tpl->assign('nome_admin1', $reg['nome']);
		if ($reg['usuario'] == $administrador1) { $tpl->assign('sel_admin1', ' selected'); }
	}		
// --------------------------------------------------------- Combo Administrador2
	$sql =        " select usuario, nome ";
	$sql = $sql . " from   projetos.usuarios_controledi  where tipo not in ('X', 'P', 'T')";
	$sql = $sql . " order by nome ";     
	$rs = pg_exec($db, $sql);
	
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('administrador2');
		$tpl->assign('cod_admin2', $reg['usuario']);
		$tpl->assign('nome_admin2', $reg['nome']);
		if ($reg['usuario'] == $administrador2) { $tpl->assign('sel_admin2', ' selected'); }
	}
// --------------------------------------------------------- Combo Gerente responsável (Sponsor)
	$sql =        " select usuario, nome ";
	$sql = $sql . " from   projetos.usuarios_controledi ";
	$sql = $sql . " where  (tipo = 'A' or tipo ='G') ";
	$sql = $sql . " order by nome ";
	$rs = pg_exec($db, $sql);
	
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('responsavel');
		$tpl->assign('cod_responsavel', $reg['usuario']);
		$tpl->assign('nome_responsavel', $reg['nome']);
		if ($reg['usuario'] == $atendente) { $tpl->assign('sel_responsavel', ' selected'); }
	}
// --------------------------------------------------------- Combo Atendente Responsavel (ANALISTA)
	$sql =        " select usuario, nome ";
	$sql = $sql . " from   projetos.usuarios_controledi ";
	$sql = $sql . " where  (tipo = 'N') and (divisao = '" . $D . "') ";
	$sql = $sql . " order by nome ";
	$rs = pg_exec($db, $sql);
	
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('analista');
		$tpl->assign('cod_analista', $reg['usuario']);
		$tpl->assign('nome_analista', $reg['nome']);
		if ($reg['usuario'] == $analista_responsavel) { $tpl->assign('sel_analista', ' selected'); }
	}
// --------------------------------------------------------- Combo Gerente responsável (Sponsor)
	if ($c != '') {
		$sql =        " select nome, cd_envolvido, cd_projeto ";
		$sql = $sql . " from   projetos.usuarios_controledi, projetos.projetos_envolvidos ";
		$sql = $sql . " where  cd_projeto = $c and cd_envolvido = codigo ";
		$sql = $sql . " order by nome ";
		//	echo $sql;
		$rs = pg_exec($db, $sql);
		$v_envolvidos = '';
		while ($reg=pg_fetch_array($rs)) {
			if ($v_envolvidos == '') {
				$v_envolvidos = '<a href="cad_envolvidos_projeto.php?cd_projeto='.$reg['cd_projeto'].'&cd_envolvido='.$reg['cd_envolvido'].'" class="links2">'.$reg['nome']."</a>";
			}
			else {
				$v_envolvidos = $v_envolvidos . ', ' . '<a href="cad_envolvidos_projeto.php?cd_projeto='.$reg['cd_projeto'].'&cd_envolvido='.$reg['cd_envolvido'].'" class="links2">'.$reg['nome']."</a>";
			}
		}
	}
	$tpl->newBlock('pessoas_chave');
	$tpl->assign('pessoas_chave', $v_envolvidos);
// --------------------------------------------------------- Combo Diretrizes
	$sql =        " select codigo, descricao ";
	$sql = $sql . " from   listas ";
	$sql = $sql . " where  categoria = 'DTRZ' ";
	$sql = $sql . " order by codigo ";
	$rs = pg_exec($db, $sql);
	
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('diretriz');
		$tpl->assign('cod_diretriz', $reg['codigo']);
		$tpl->assign('desc_diretriz', $reg['descricao']);
		if ($reg['codigo'] == $diretriz) { $tpl->assign('sel_diretriz', ' selected'); }
	}
// --------------------------------------------------------- PROJETO Superior (PAI)
	$sql = "";
	$sql = $sql . " select codigo, nome ";
	$sql = $sql . " from   projetos.projetos order by nome ";
	$rs = pg_exec($db, $sql);
	
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('projeto_superior');
		$tpl->assign('cod_projeto_superior', $reg['codigo']);
		$tpl->assign('desc_projeto_superior', $reg['nome']);
		$tpl->assign('sel_projeto_superior', ($reg['codigo'] == $pai ? ' selected' : ''));
//		if ($reg['cod_projeto_dependente'] == $reg['codigo']) { $tpl->assign('sel_dependente', ' selected'); }
	}
// --------------------------------------------------------- Programa Institucional
	$sql = "";
	$sql = $sql . " select codigo, descricao ";
	$sql = $sql . " from   listas where categoria = 'PRFC' and divisao is null order by descricao ";
	$rs = pg_exec($db, $sql);
	
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('programa');
		$tpl->assign('cod_programa', $reg['codigo']);
		$tpl->assign('desc_programa', $reg['descricao']);
		$tpl->assign('sel_programa', ($reg['codigo'] == $programa_institucional ? ' selected' : ''));
//		if ($reg['cod_projeto_dependente'] == $reg['codigo']) { $tpl->assign('sel_dependente', ' selected'); }
	}
// ----------------------------------------------------------------- Tabela de Rateio Previdenciário
	if (isset($c)) {
		$sql = "";
		$sql = $sql . " select 	distinct pl.descricao        as plano,        ";
		$sql = $sql . "			patro.nome_empresa as empresa,       ";
		$sql = $sql . "			prp.dt_inicio       as data_inicio,  ";
		$sql = $sql . "			prp.dt_fim          as data_fim,     ";
		$sql = $sql . "			prp.vl_percentual   as percentual,   ";
		$sql = $sql . "			prp.cd_projeto      as codigo        ";
		$sql = $sql . " from 	patrocinadoras               patro,    ";
		$sql = $sql . "			planos                       pl ,      ";
		$sql = $sql . "			projetos.plano_patrocinadora ppp,      ";
		$sql = $sql . "			projetos.projetos            pp,       ";
		$sql = $sql . "			projetos.rateio_projeto      prp       ";
		$sql = $sql . " where 	ppp.cd_empresa     = patro.cd_empresa ";
		$sql = $sql . "			and ppp.cd_plano   = pl.cd_plano      ";
		$sql = $sql . "			and ppp.cd_plano   = prp.cd_plano     ";
		$sql = $sql . "			and ppp.cd_empresa = prp.cd_empresa   ";
		//$sql = $sql . "			and prp.dt_fim     is null            ";
		$sql = $sql . "			and prp.cd_projeto = $c               ";
		$sql = $sql . "			and prp.cd_programa = 'P'			  "; 

		$primeiro = true;
		$rs = pg_exec($db, $sql);
		while ($reg=pg_fetch_array($rs)) {
			if ($primeiro) {
				$tpl->newBlock('cabecalho');
				$tpl->assign('dt_ini', $reg['data_inicio']);
				$tpl->assign('projeto', $reg['codigo']);
				$tpl->assign('link', 'insere_tabela_rateio.php');
				$primeiro = false;
			}
			$tpl->newBlock('rateio'); 
			$tpl->assign('projeto', $reg['codigo']);			
			$tpl->assign('patrocinadora', $reg['empresa']);
            $tpl->assign('dt_fim', $reg['data_fim']);
			$tpl->assign('plano', $reg['plano']);
		    $tpl->assign('percentual', $reg['percentual']);
		}
		$tpl->newBlock('rateio_projeto');
		$tpl->assign('cod_projeto',$cod_projeto); 		
// ----------------------------------------------------------------- Perc Previdenciário
		$tpl->newBlock('perc_prev');
		$sql = "";
		$sql = $sql . " select 	vl_percentual as percentual ";
		$sql = $sql . " from 	projetos.rateio_projeto     ";
		$sql = $sql . " where 	cd_empresa	= 9 			";
		$sql = $sql . " 	and cd_plano   	= 0 			";
		//$sql = $sql . "		and dt_fim     	is null 		";
		$sql = $sql . "		and cd_projeto = $c     		";
		$sql = $sql . "		and cd_programa = 'P'			"; 
		$rs  = pg_exec($db, $sql);
		$reg = pg_fetch_array($rs);
	    $tpl->assign('perc_prev', $reg['percentual']);
// ----------------------------------------------------------------- Perc Investimentos
		$tpl->newBlock('perc_outros');
		$sql = "";
		$sql = $sql . " select 	vl_percentual as percentual ";
		$sql = $sql . " from 	projetos.rateio_projeto     ";
		$sql = $sql . " where 	cd_empresa	= 9 			";
		$sql = $sql . " 	and cd_plano   	= 0 			";
		//$sql = $sql . "		and dt_fim     	is null 		";
		$sql = $sql . "		and cd_projeto = $c     		";
		$sql = $sql . "		and cd_programa = 'I'			"; 
		$rs  = pg_exec($db, $sql);
		$reg = pg_fetch_array($rs);
	    $tpl->assign('perc_inv', $reg['percentual']);
// ----------------------------------------------------------------- Perc Empréstimos
		$sql = "";
		$sql = $sql . " select 	vl_percentual as percentual ";
		$sql = $sql . " from 	projetos.rateio_projeto     ";
		$sql = $sql . " where 	cd_empresa	= 9 			";
		$sql = $sql . " 	and cd_plano   	= 0 			";
		//$sql = $sql . "		and dt_fim     	is null 		";
		$sql = $sql . "		and cd_projeto = $c     		";
		$sql = $sql . "		and cd_programa = 'E'			"; 
		$rs  = pg_exec($db, $sql);
		$reg = pg_fetch_array($rs);
	    $tpl->assign('perc_emp', $reg['percentual']);
// ----------------------------------------------------------------- Perc Seguros
		$sql = "";
		$sql = $sql . " select 	vl_percentual as percentual ";
		$sql = $sql . " from 	projetos.rateio_projeto     ";
		$sql = $sql . " where 	cd_empresa	= 9 			";
		$sql = $sql . " 	and cd_plano   	= 0 			";
		//$sql = $sql . "		and dt_fim     	is null 		";
		$sql = $sql . "		and cd_projeto = $c     		";
		$sql = $sql . "		and cd_programa = 'S'			"; 
		$rs  = pg_exec($db, $sql);
		$reg = pg_fetch_array($rs);
	    $tpl->assign('perc_seg', $reg['percentual']);
// --------------------------------------------------------- PROJETOS DEPENDENTES
		$sql = "";
		$sql = $sql . " select codigo,nome,atendente,cod_projeto_superior,to_char(data_cad,'dd/mm/yyyy') as data_cad ";
        $sql = $sql . " from   projetos.projetos ";
	    $sql = $sql . " where cod_projeto_superior = $c order by nome";
		$rs = pg_exec($db, $sql);
		
        while ($reg=pg_fetch_array($rs)) {
			$tpl->newBlock('lst_dependentes');
			$tpl->assign('atendente', $reg['atendente']);
			$tpl->assign('descricao', $reg['nome']);
			$tpl->assign('dt_inicio', $reg['data_cad']);
		}
	}
// --------------------------------------------------------- 
	pg_close($db);
	$tpl->printToScreen();	
?>