<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');

   header('location:'.base_url().'index.php/gestao/indicador');
   
   $tpl = new TemplatePower('tpl/tpl_lst_indic_raiz.html');
   $tpl->prepare();
   $tpl->assign('n', $n);
   
   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

   	if (($D <> 'GRI') and ($D <> 'GI')) {
   		header('location: acesso_restrito.php?IMG=banner_lst_destaques');
	}

   $tpl->newBlock('lista');
     $sql =        " select cd_indic, ";
     $sql = $sql . "        nome_indic ";
     $sql = $sql . " from   acs.raiz_indicadores  ";
     $sql = $sql . " order by cd_indic ";
//echo $sql ;
   $rs=pg_exec($db, $sql);
   $cont = 0;
   while ($reg=pg_fetch_array($rs)) {
      $tpl->newBlock('indicadores');
      $cont = $cont + 1;
      if (($cont % 2) <> 0) {
         $tpl->assign('cor_fundo', '#F4F4F4');
      }
      else {
         $tpl->assign('cor_fundo', '#FAFAFA');
      }
	  $tpl->assign('cd_indicador',$reg['cd_indic']);
      $tpl->assign('descricao', $reg['nome_indic']);
  	}
	pg_close($db);
	$tpl->printToScreen();	
?>