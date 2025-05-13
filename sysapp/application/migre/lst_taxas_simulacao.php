<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   $tpl = new TemplatePower('tpl/tpl_lst_taxas_simulacao.html');
   $tpl->prepare();
   $tpl->assign('n', $n);
   
   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

   	if (($D <> 'GA') and ($D <> 'GI')) {
   		header('location: acesso_restrito.php?IMG=banner_taxas');
	}

//   $tpl->newBlock('lista');
     $sql =        " select idade, tx1, tx2, tx3 ";
     $sql = $sql . " from   sim_taxas ";
//echo $sql ;
   $rs=pg_exec($db, $sql);
   $cont = 0;
   while ($reg=pg_fetch_array($rs)) {
      $tpl->newBlock('projetos');
      $cont = $cont + 1;
	  $tpl->assign('idade',$reg['idade']);
	  $tpl->assign('tx1',$reg['tx1']);
      $tpl->assign('tx2', $reg['tx2']);
      $tpl->assign('tx3', $reg['tx3']);
  	}
	pg_close($db);
	$tpl->printToScreen();	
?>