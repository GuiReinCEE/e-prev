<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
/*
	$tpl = new TemplatePower('tpl/tpl_frm_os.html');
   $tpl->assignInclude('menu_d', 'tpl/menu_analista.html');
	$tpl->prepare();
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
*/
/*
   $sql =        " SELECT numero,                                            ";
   $sql = $sql . "        area,                                              ";
	$sql = $sql . "        sistema,                                           ";
	$sql = $sql . "        solicitante,                                       ";
	$sql = $sql . "        tipo_solicitacao,                                  ";
	$sql = $sql . "        to_char(dt_cad, 'dd/mm/yyyy') as dt_cad,           ";
	$sql = $sql . "        tipo_solicitacao,                                  ";
	$sql = $sql . "        status_atual,                                      ";
	$sql = $sql . "        atendente,                                         ";
	$sql = $sql . "        complexidade,                                      ";
	$sql = $sql . "        descricao,                                         ";
	$sql = $sql . "        problema,                                          ";
	$sql = $sql . "        solucao,                                           ";
	$sql = $sql . "        negocio_fim,                                       ";
	$sql = $sql . "        prejuizo,                                          ";
	$sql = $sql . "        legislacao,                                        ";
	$sql = $sql . "        cliente_externo,                                   ";
	$sql = $sql . "        concorrencia,                                      ";
	$sql = $sql . "        to_char(dt_inicio_prev, 'dd/mm/yyyy') as dt_inicio_prev, ";
	$sql = $sql . "        to_char(dt_fim_prev, 'dd/mm/yyyy') as dt_fim_prev, ";
	$sql = $sql . "        to_char(dt_inicio_real, 'dd/mm/yyyy') as dt_inicio_real, ";
	$sql = $sql . "        to_char(dt_fim_real, 'dd/mm/yyyy') as dt_fim_real ";
	$sql = $sql . " FROM   os_software       ";
	$sql = $sql . " WHERE  numero = $n       ";
*/

   $sql =        " SELECT o.numero, ";
   $sql = $sql . "        (select descricao from listas where categoria='DIVI' and codigo=o.area) as area, ";
   $sql = $sql . "        (select nome from projetos.projetos where codigo=int2(o.sistema)) as sistema, ";
   $sql = $sql . "        (select nome from projetos.usuarios_controledi where codigo = o.cod_solicitante) as solicitante, ";
   $sql = $sql . "        (select descricao from listas where codigo=o.tipo_solicitacao) as tipo_solicitacao, ";
   $sql = $sql . "        to_char(o.dt_cad, 'dd/mm/yyyy') as dt_cad, ";
   $sql = $sql . "        (select descricao from listas where codigo=o.status_atual) as status_atual, ";
   $sql = $sql . "        (select nome from projetos.usuarios_controledi where codigo=o.cod_atendente) as atendente, ";
   $sql = $sql . "        (select descricao from listas where categoria='CPLX' and codigo=o.complexidade) as complexidade, ";
   $sql = $sql . "        o.descricao, o.titulo, o.dt_limite, ";
   $sql = $sql . "        o.problema, ";
   $sql = $sql . "        o.solucao, ";
   $sql = $sql . "        o.negocio_fim, ";
   $sql = $sql . "        o.prejuizo, ";
   $sql = $sql . "        o.legislacao, ";
   $sql = $sql . "        o.cliente_externo, ";
   $sql = $sql . "        o.concorrencia, ";
   $sql = $sql . "        to_char(o.dt_inicio_prev, 'dd/mm/yyyy') as dt_inicio_prev, ";
   $sql = $sql . "        to_char(o.dt_fim_prev, 'dd/mm/yyyy') as dt_fim_prev, ";
   $sql = $sql . "        to_char(o.dt_inicio_real, 'dd/mm/yyyy') as dt_inicio_real, ";
   $sql = $sql . "        to_char(o.dt_fim_real, 'dd/mm/yyyy') as dt_fim_real ";
   $sql = $sql . " FROM   projetos.atividades o ";
   $sql = $sql . " WHERE  numero = $n ";
//echo $sql;
   $rsOs = pg_exec($db, $sql);
   $regOs = pg_fetch_array($rsOs);
	// Seleciona o template. Se for o responsável pela OS, permite alterar, 
	// caso contrário, apenas mostra a OS
   $tpl = new TemplatePower('tpl/tpl_imprime_atividade.html');
   $tpl->prepare();
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->assign('numero_os', $regOs['numero']);
	$tpl->assign('dt_cad', $regOs['dt_cad']);
	$tpl->assign('projeto', $regOs['sistema']);
	$tpl->assign('divisao_destino', $regOs['area']);
	$tpl->assign('solicitante', $regOs['solicitante']);
	$tpl->assign('tipo_manut', $regOs['tipo_solicitacao']);
   $tpl->assign('descricao', ( $U==$regOs['atendente'] ? $regOs['descricao'] : str_replace(chr(13).chr(10), "<br>", $regOs['descricao']) ) );
   $tpl->assign('problema', ( $U==$regOs['atendente'] ? $regOs['problema'] : str_replace(chr(13).chr(10), "<br>", $regOs['problema']) ) );
	$tpl->assign('status_atual', $regOs['status_atual']);
	$tpl->assign('titulo', $regOs['titulo']);
	$tpl->assign('dt_limite', $regOs['dt_limite']);
	$tpl->assign('atendente', $regOs['atendente']);
   $tpl->assign('dt_inicio_prevista', $regOs['dt_inicio_prev']);
   $tpl->assign('dt_fim_prevista', $regOs['dt_fim_prev']);
   $tpl->assign('dt_inicio_real', $regOs['dt_inicio_real']);
   $tpl->assign('dt_fim_real', $regOs['dt_fim_real']);
   $tpl->assign('solucao', ( $U==$regOs['atendente'] ? $regOs['solucao'] : str_replace(chr(13).chr(10), "<br>", $regOs['solucao']) ) );
	$tpl->assign('complexidade', $regOs['complexidade']);

   // Finaliza construção da página
	pg_close($db);
	$tpl->printToscreen();
?>