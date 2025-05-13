<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_relatorios.html');
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//-----------------------------------------------
	$tpl->newBlock('cadastro');
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);
	if (isset($c))	{
		$sql =        " select 	cd_relatorio, esquema, tipo, tabela, query, to_char(dt_criacao, 'DD/MM/YYYY') as dt_criacao, ";
		$sql = $sql . " titulo, num_colunas, fonte, divisao, clausula_where, grupo, ordem, cd_proprietario, restricao_acesso, ";
		$sql = $sql . " pos_x, largura, mostrar_sombreamento, tam_fonte, tam_fonte_titulo, mostrar_cabecalho, mostrar_linhas, orientacao, ";
		$sql = $sql . " cd_projeto, especie ";
		$sql = $sql . " from   	projetos.relatorios ";
		$sql = $sql . " where 	cd_relatorio = '". $c . "' ";	
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('codigo', $reg['cd_relatorio']);
		$tpl->assign('tabela', $reg['tabela']);
		$tpl->assign('esquema', $reg['esquema']);
		$tpl->assign('query', $reg['query']);
		$tpl->assign('clausula_where', $reg['clausula_where']);
		$tpl->assign('ordem', $reg['ordem']);
		$tpl->assign('grupo', $reg['grupo']);
		$tpl->assign('dt_criacao', $reg['dt_criacao']);
		$tpl->assign('titulo', $reg['titulo']);
		$tpl->assign('divisao', $reg['divisao']);
		$tpl->assign('colunas', $reg['num_colunas']);
		$tpl->assign('especie', $reg['especie']);
		$tpl->assign('pos_x', $reg['pos_x']);
		$tpl->assign('largura', $reg['largura']);
		if ($reg['mostrar_cabecalho'] == 'S') {
			$tpl->assign('chk_mostrar_cabecalhos', 'checked');
		}
		if ($reg['mostrar_linhas'] == 'S') {
			$tpl->assign('chk_mostrar_linhas', 'checked');
		}
		if ($reg['mostrar_sombreamento'] == 'S') {
			$tpl->assign('chk_mostrar_sombreamento', 'checked');
		}
		$tpl->assign('tam_fonte', $reg['tam_fonte']);
		$tpl->assign('tam_fonte_titulo', $reg['tam_fonte_titulo']);
		if ($reg['orientacao'] == 'L') {
			$tpl->assign('alin_esq', 'selected');
		}			
		elseif ($reg['orientacao'] == 'C') {
			$tpl->assign('alin_cent', 'selected');
		}			
		elseif ($reg['orientacao'] == 'R') {
			$tpl->assign('alin_dir', 'selected');
		}			
		$v_esquema = $reg['esquema'];
		$v_tabela = $reg['tabela'];
		$v_tipo = $reg['tipo'];
		$v_restricao_acesso = $reg['restricao_acesso'];		
		$v_cd_proprietario = $reg['cd_proprietario'];
		$v_fonte = $reg['fonte'];
		$v_cd_sistema = $reg['cd_projeto'];
//-----------------------------------------------
		$sql = "	select 	cd_coluna, nome_coluna, alinhamento, largura ";
		$sql = $sql . " 	from 	projetos.relatorios_colunas ";
		$sql = $sql . " 	where 	cd_relatorio = $c order by cd_coluna";
		$rs=pg_exec($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->assign('cabec'.$reg['cd_coluna'], $reg['nome_coluna']);
			$tpl->assign('larg_col'.$reg['cd_coluna'], $reg['largura']);
			if ($reg['alinhamento'] == 'L') {
				$tpl->assign('alin_esq_col'.$reg['cd_coluna'], 'selected');
			}			
			elseif ($reg['alinhamento'] == 'C') {
				$tpl->assign('alin_cent_col'.$reg['cd_coluna'], 'selected');
			}			
			elseif ($reg['alinhamento'] == 'R') {
				$tpl->assign('alin_dir_col'.$reg['cd_coluna'], 'selected');
			}			
		}
	}
//----------------------------------------------- Lista de tabelas:
	$sql = "SELECT object_name, schema_name, row_count, object_size ";
	$sql = $sql . " FROM 	dba.dba_tam_objetos tao";
	$sql = $sql . " WHERE 	(tao.object_type = 'TABLE' or tao.object_type = 'VIEW')";
	$sql = $sql . " order by schema_name, object_name ";
	$tpl->newBlock('cbo_tabela');
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('cbo_tabela');
		$tpl->assign('cod_tabela', $reg['schema_name'] . "." . $reg['object_name']);
		$tpl->assign('nome_tabela', $reg['schema_name'] . "." . $reg['object_name']);
		if (($v_esquema == $reg['schema_name']) and ($v_tabela == $reg['object_name'])) {
			$tpl->assign('chk_tabela', 'selected');
		}
	}
//----------------------------------------------- Tipos de relatуrios:
	$sql = "SELECT codigo, descricao,  categoria ";
	$sql = $sql . " FROM 	listas ";
	$sql = $sql . " WHERE 	categoria = 'TPRL'";
	$sql = $sql . " order by descricao ";
	$tpl->newBlock('cbo_tabela');
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('tipo_rel');
		$tpl->assign('cd_tipo_rel', $reg['codigo']);
		$tpl->assign('nome_tipo_rel', $reg['descricao']);
		if ($v_tipo == $reg['codigo']) {
			$tpl->assign('chk_tipo_rel', 'selected');
		}
	}
//----------------------------------------------- Restriзгo de acesso:
	$sql = "SELECT trim(codigo) as codigo, descricao,  categoria ";
	$sql = $sql . " FROM 	listas ";
	$sql = $sql . " WHERE 	categoria = 'REAC'";
	$sql = $sql . " order by descricao ";
	$tpl->newBlock('cbo_tabela');
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('restricao_acesso');
		$tpl->assign('cd_restricao_acesso', $reg['codigo']);
		$tpl->assign('nome_restricao_acesso', $reg['descricao']);
		if ($v_restricao_acesso == $reg['codigo']) {
			$tpl->assign('chk_restricao_acesso', 'selected');
		}
	}
//----------------------------------------------- Fontes:
	$sql = "SELECT codigo, descricao,  categoria ";
	$sql = $sql . " FROM 	listas ";
	$sql = $sql . " WHERE 	categoria = 'FONT'";
	$sql = $sql . " order by descricao ";
	$tpl->newBlock('cbo_tabela');
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('fonte');
		$tpl->assign('cd_fonte', $reg['codigo']);
		$tpl->assign('nome_fonte', $reg['descricao']);
		if ($v_fonte == $reg['codigo']) {
			$tpl->assign('chk_fonte', 'selected');
		}
	}
//----------------------------------------------- Responsбvel:
	$sql = "SELECT codigo, divisao, guerra ";
	$sql = $sql . " FROM 	projetos.usuarios_controledi ";
	$sql = $sql . " WHERE 	tipo not in ('X', 'T', 'P')";
	$sql = $sql . " order by divisao, guerra ";
	$tpl->newBlock('cbo_tabela');
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('proprietario');
		$tpl->assign('cd_proprietario', $reg['codigo']);
		$tpl->assign('nome_proprietario', $reg['divisao'].' - '.$reg['guerra']);
		if ($v_cd_proprietario == $reg['codigo']) {
			$tpl->assign('chk_proprietario', 'selected');
		}
	}
//----------------------------------------------- Sistema:
	$sql = "SELECT codigo, nome ";
	$sql = $sql . " FROM 	projetos.projetos ";
	$sql = $sql . " WHERE 	dt_exclusao is null";
	$sql = $sql . " order by nome ";
	$tpl->newBlock('cbo_tabela');
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('sistema');
		$tpl->assign('cd_sistema', $reg['codigo']);
		$tpl->assign('nome_sistema', $reg['nome']);
		if ($v_cd_sistema == $reg['codigo']) {
			$tpl->assign('chk_sistema', 'selected');
		}
	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>