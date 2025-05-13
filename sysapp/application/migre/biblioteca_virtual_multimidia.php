<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	header( 'location: '.base_url().'index.php/intranet/biblioteca_multimidia');
	EXIT;	
	
	

	include_once('inc/class.TemplatePower.inc.php');
   
	$tpl = new TemplatePower('tpl/tpl_biblioteca_virtual_multimidia.htm');

	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	if ($ano == '') {
		$ano = date('Y');
	}
	$tpl->assign('ano', $ano);
	$tpl->newBlock('lista');
    $sql =        " select cd_video, ";
    $sql = $sql . "        titulo, ";
    $sql = $sql . "        to_char(dt_evento, 'DD/MM/YYYY') as dt_evento_ed, ";
	$sql = $sql . "        to_char(dt_atualizacao, 'DD/MM/YYYY') as dt_atualizacao ";
    $sql = $sql . " from   acs.videos  ";
	$sql = $sql . " where	extract(year from dt_evento) = $ano  ";
    $sql = $sql . " order by dt_evento desc ";
//echo $sql ;
   $rs=pg_exec($db, $sql);
   $cont = 0;
   while ($reg=pg_fetch_array($rs)) {
      $tpl->newBlock('filme');
      $cont = $cont + 1;
      if (($cont % 2) <> 0) {
         $tpl->assign('cor_fundo', $v_cor_fundo1);
      }
      else {
         $tpl->assign('cor_fundo', $v_cor_fundo2);
      }
	  $tpl->assign('cd_filme',$reg['cd_video']);
      $tpl->assign('titulo', $reg['titulo']);
      $tpl->assign('dt_evento', $reg['dt_evento_ed']);
      $tpl->assign('dt_atualizacao', $reg['dt_atualizacao']);
  	}
	pg_close($db);
	$tpl->printToScreen();	
?>