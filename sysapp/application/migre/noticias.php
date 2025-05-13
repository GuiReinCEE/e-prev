<?
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   $tpl = new TemplatePower('tpl/tpl_noticias.htm');
   $tpl->prepare();
   $sql =        " select codigo, ";
   $sql = $sql . "        lpad(extract(day from data), 2, '0')||'/'||lpad(extract(month from data),2,'0')||'/'||extract(year from data)||' - '||lpad(extract(hour from data),2,'0')||':'||lpad(extract(minutes from data),2,'0') as data, ";
   $sql = $sql . "        titulo, ";
   $sql = $sql . "        descricao, ";
   $sql = $sql . "        extract(year from data)||lpad(extract(month from data),2,'0')||lpad(extract(day from data), 2, '0') as data_ord ";
   $sql = $sql . " from   acs.noticias ";
   $sql = $sql . " where  CURRENT_DATE >= (CURRENT_DATE - interval '7 days') ";
   $sql = $sql . " order by editorial, titulo";
   
   $rs = pg_exec($db, $sql);
   while ($r = pg_fetch_array($rs))
   {
      $tpl->newBlock('noticia');
      $tpl->assign('codigo', $r['codigo']);
      $tpl->assign('titulo', $r['titulo']);
      $tpl->assign('data', $r['data']);
      $tpl->assign('noticia', ereg_replace("\n", "<br>", $r['descricao']));
   }
   $tpl->printToScreen();
   pg_close($db);
?>
