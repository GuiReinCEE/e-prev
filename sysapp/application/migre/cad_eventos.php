<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   
   $tpl = new TemplatePower('tpl/tpl_cad_eventos.html');

   $tpl->prepare();
   $tpl->assign('n', $n);
   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
   $tpl->assign('divsao', $D);
   $tpl->newBlock('cadastro');
   // ----------------------------------------------------- se é um novo evento, TR vem com 'I'
		if ($tr == 'U') {
			$n = 'U';
		}
		else {
			$n = 'I';
		}
		$tpl->assign('insere', $n);

   if (isset($c))	{
        $sql =   " ";
		$sql = $sql . " select 	ev.cd_evento  			as cd_evento, ";
		$sql = $sql . "			ev.nome					as nome, ";
		$sql = $sql . "        	ev.cd_projeto			as cd_projeto,	";
		$sql = $sql . "			ev.tipo					as cd_tipo_evento, ";
		$sql = $sql . "        	ev.dt_referencia      	as cd_dt_referencia, ";
		$sql = $sql . "        	ev.indic_email			as indic_email,	";
		$sql = $sql . "			ev.indic_historico		as indic_historico,	";
		$sql = $sql . "			ev.dias_dt_referencia	as dias, ";
		$sql = $sql . "			ev.email				as email ";
		$sql = $sql . "  from 	projetos.eventos   		ev		";
		$sql = $sql . "  where 	ev.cd_evento			= $c ";
        $rs = pg_exec($db, $sql);
        $reg=pg_fetch_array($rs);
		$cod_evento = $reg['cd_evento'];
		$cod_projeto = $reg['cd_projeto'];
		$cod_tipo_evento = $reg['cd_tipo_evento'];
		$cod_data_referencia = $reg['cd_dt_referencia'];
		$tipo_acao = $reg['tipo_acao'];
		$tpl->assign('cod_evento', $reg['cd_evento']);
		$tpl->assign('codigo', $reg['cd_evento']);
        $tpl->assign('nome', $reg['nome']);
		$tpl->assign('dias', $reg['dias']);
		$tpl->assign('texto_email', $reg['email']);
		if ($reg['indic_email'] == 'S') { $tpl->assign('email_checked', 'checked');	}
		if ($reg['indic_historico'] == 'S') { $tpl->assign('historico_checked', 'checked'); }
   }
   	else {
		$sql =        " select max(cd_evento) as cd_evento ";
		$sql = $sql . " from   projetos.eventos ";
//		echo $sql;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('codigo', ($reg['cd_evento'] + 1));
		$cd_evento = $reg['cd_evento'] + 1;
	}

//------------------------------------------------ Projeto Associado:
	$sql =        " select codigo as cd_projeto, nome as nome_projeto ";
	$sql = $sql . " from   projetos.projetos ";
	$sql = $sql . " order by nome_projeto ";
	$rs = pg_exec($db, $sql);
	while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('projeto');
		$tpl->assign('cd_projeto', $reg['cd_projeto']);
		$tpl->assign('nome_projeto', $reg['nome_projeto']);
		if (($reg['cd_projeto'] == $cod_projeto))  { $tpl->assign('sel_projeto', 'selected'); }
	}
//------------------------------------------------ Tipo do evento:
	$tpl->newBlock('tipo_evento');
	$sql =        " select 	codigo as cd_tipo_evento, descricao as desc_tipo_evento ";
	$sql = $sql . " from   	listas ";
	$sql = $sql . "	where 	categoria = 'EVEN' ";
	$sql = $sql . " order 	by descricao ";
	$rs = pg_exec($db, $sql);
	while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('tipo_evento');
		$tpl->assign('cd_tipo_evento', $reg['cd_tipo_evento']);
		$tpl->assign('nome_tipo_evento', $reg['desc_tipo_evento']);
		if (($reg['cd_tipo_evento'] == $cod_tipo_evento))  { $tpl->assign('sel_tipo_evento', 'selected'); }
	}
//------------------------------------------------ Data referência para os eventos temporais:
	$tpl->newBlock('data_referencia');
	$sql =        " select 	codigo as cd_dt_referencia, descricao as desc_dt_referencia ";
	$sql = $sql . " from   	listas ";
	$sql = $sql . "	where 	categoria = 'DTRE' ";
	$sql = $sql . " order 	by descricao ";
	$rs = pg_exec($db, $sql);
//	echo $cod_data_referencia;
	while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('data_referencia');
		$tpl->assign('cd_data_referencia', $reg['cd_dt_referencia']);
		$tpl->assign('nome_data_referencia', $reg['desc_dt_referencia']);
		if (($reg['cd_dt_referencia'] == $cod_data_referencia))  { $tpl->assign('sel_data_referencia', 'selected'); }
	}
//------------------------------------------------ Instâncias:
	$sql =        " select 	cd_instancia, nome as desc_instancia ";
	$sql = $sql . " from   	projetos.instancias ";
	$sql = $sql . " order 	by nome ";
	$rs = pg_exec($db, $sql);
	while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('instancia');
		$tpl->assign('cd_instancia', $reg['cd_instancia']);
		$tpl->assign('nome_instancia', $reg['desc_instancia']);
		if (isset($cod_evento)) {
			$sql2 =			" select * from   projetos.instancias_eventos ";
			$sql2 = $sql2 . " where cd_evento = " . $cod_evento ;
			$sql2 = $sql2 . " 	and cd_instancia = " . $reg['cd_instancia'];
			$rs2 = pg_exec($db, $sql2);
//  		echo $sql2;
			if (pg_fetch_array($rs2)) { $tpl->assign('instancia_checked', 'checked'); }
		}
	}
//------------------------------------------------ Instâncias alternativas:
	$sql =        " select 	cd_instancia, nome as desc_instancia ";
	$sql = $sql . " from   	projetos.instancias ";
	$sql = $sql . " order 	by nome ";
	$rs = pg_exec($db, $sql);
	while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('instancia2');
		$tpl->assign('cd_instancia2', $reg['cd_instancia']);
		$tpl->assign('nome_instancia2', $reg['desc_instancia']);
		if (isset($cod_evento)) {
			$sql2 =			" select * from   projetos.instancias_eventos_sec ";
			$sql2 = $sql2 . " where cd_evento = " . $cod_evento ;
			$sql2 = $sql2 . " 	and cd_instancia = " . $reg['cd_instancia'];
			$rs2 = pg_exec($db, $sql2);
//  		echo $sql2;
			if (pg_fetch_array($rs2)) { $tpl->assign('instancia2_checked', 'checked'); }
		}
	}
//-------------------------------------------------------
   pg_close($db);
   $tpl->printToScreen();	
?>