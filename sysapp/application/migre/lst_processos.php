<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');

	header('location:'.base_url().'index.php/gestao/processo');

   include_once('inc/class.TemplatePower.inc.php');
   $tpl = new TemplatePower('tpl/tpl_lst_processos.html');
   $tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');
   
   $tpl->prepare();
   $tpl->assign('n', $n);
   
   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
   $sql =   " ";
   $sql = $sql . "  select distinct pp.cd_processo as codigo, pp.cod_responsavel, pp.objetivo as objetivo, pp.procedimento as nome,  "; 	// garcia - 10/02/2004
   $sql = $sql . "         to_char(pp.data,'dd/mm/yyyy') as dt_inclusao, d.nome as responsavel  ";
//   $sql = $sql . "         u.nome as responsavel                                 ";
   $sql = $sql . "  from projetos.processos           pp,                 ";
   $sql = $sql . "       projetos.usuarios_controledi u,                   						";
   $sql = $sql . "		 projetos.divisoes d													";	// garcia - 09/03/2004	
   $sql = $sql . "  where (pp.cd_processo_pai is null) and pp.cod_responsavel = d.codigo	"; 	// garcia - 09/03/2004	
   if (isset($DIV)){
   		$sql = $sql . "	and	(pp.cod_responsavel = '$DIV') ";
	}   
   $sql = $sql . "  order by pp.procedimento	                          						";	// garcia - 10/02/2004	

   $rs = pg_exec($sql);
   
   while ($reg=pg_fetch_array($rs))
   {
	  $tpl->newBlock('processos');
	  $tpl->assign('codigo', $reg['codigo']);
	  $tpl->assign('nome', $reg['nome']);
	  $tpl->assign('objetivo', $reg['objetivo']);		// garcia - 10/02/2004
	  $tpl->assign('responsavel', $reg['responsavel']);		// garcia - 10/02/2004
//	  $tpl->assign('dt_inclusao', $reg['dt_inclusao']);
//	  $tpl->assign('responsavel', $reg['responsavel']);
   }
	
   pg_close($db);
   $tpl->printToScreen();	
?>