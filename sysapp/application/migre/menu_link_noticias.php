<?php
   include_once('inc/class.TemplatePower.inc.php');
   include_once('inc/conexao.php');
   $tpl = new TemplatePower('tpl/tpl_menu_noticias.html');

   
   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
   $sql =        " SELECT CODIGO, TITULO, DATA, DESCRICAO, EDITORIAL ";
   $sql = $sql . " FROM   acs.noticias "; 
   $sql = $sql . " ORDER BY   EDITORIAL, TITULO "; 
   $rs = pg_exec($db, $sql);
   $cat = '';
   $tpl->prepare();
   while ($reg = pg_fetch_array($rs))
   {
      if ($cat != $reg['editorial']) 
      {
         $cat = $reg['editorial'];
         $tpl->newBlock('editorial');
         switch ($reg['editorial'])
         {
            case 'FP':$tpl->assign('editorial', 'Fundos de Pensуo'); break;
            case 'FC':$tpl->assign('editorial', 'Fundaчуo CEEE'); break;
            case 'PR':$tpl->assign('editorial', 'Previdъncia'); break;
         }
      }
      $tpl->newBlock('noticia');
      $tpl->assign('codigo', $reg['codigo']);
      $tpl->assign('titulo', $reg['titulo']);
   }
   $tpl->printToScreen();
   pg_close($db);         
?>