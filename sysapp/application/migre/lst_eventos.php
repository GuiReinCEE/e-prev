<?PHP
	include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');

	header( 'location:'.base_url().'index.php/gestao/evento' );

   $tpl = new TemplatePower('tpl/tpl_lst_eventos.htm');
   $tpl->prepare();
   
   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

   $sql =        " SELECT 	e.cd_evento		as cd_evento, ";
   $sql = $sql . "        	e.nome			as nome, ";
   $sql = $sql . "        	p.nome 			as nome_projeto, ";
   $sql = $sql . "        	e.tipo			as tipo ";
   $sql = $sql . " FROM 	projetos.eventos e, projetos.projetos p ";
   $sql = $sql . " where 	e.cd_projeto = p.codigo ";
   $sql = $sql . " order by e.nome ";
   $rs = pg_exec($db, $sql);
   while ($reg = pg_fetch_array($rs)) 
   {
      $tpl->newBlock('eventos');
      $tpl->assign('cd_evento', $reg['cd_evento']);
      $tpl->assign('nome', $reg['nome']);
      $tpl->assign('sistema', $reg['nome_projeto']);
      switch ($reg['tipo']) 
      {
        case 'E':$tpl->assign('tipo', 'Externo');break;         
        case 'T':$tpl->assign('tipo', 'Temporal');break;         
      }
   }
   $tpl->printToScreen();
   pg_close($db);      
?>
