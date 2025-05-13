<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   $tpl = new TemplatePower('tpl/tpl_lst_fator_atuarial.html');
   $tpl->prepare();
   $tpl->assign('n', $n);
   
   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

   	if (($D <> 'GA') and ($D <> 'GI')) {
   		header('location: acesso_restrito.php?IMG=banner_fator_atuarial');
	}

//   $tpl->newBlock('lista');
     $sql =        " select idade, ax, ";
     $sql = $sql . "                 idade2, ";
     $sql = $sql . "                 tabua, ";
	 $sql = $sql . "                 obs ";
     $sql = $sql . " from   sim_fator_atuarial  ";
     $sql = $sql . " order by tabua, idade, idade2 ";
//echo $sql ;
   $rs=pg_exec($db, $sql);
   $cont = 0;
   while ($reg=pg_fetch_array($rs)) {
      $tpl->newBlock('projetos');
      $cont = $cont + 1;
	  $tpl->assign('idade',$reg['idade']);
	  $tpl->assign('ax',$reg['ax']);
      $tpl->assign('idade2', $reg['idade2']);
      $tpl->assign('tabua', $reg['tabua']);
      $tpl->assign('obs', $reg['obs']);
  	}
	pg_close($db);
	$tpl->printToScreen();	
?>