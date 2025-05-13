<?php
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   $tpl = new TemplatePower("tpl/tpl_extrato_periodos_emprestimo.html");
   $tpl->prepare();
   $sql  = " select peri.ano, peri.cd_empresa, to_char(peri.cd_empresa, '99')||' - '||patr.sigla as nome_empresa, peri.mes, '&nbsp;'||to_char(peri.dt_abertura, 'dd/mm')||'<br>'||'&nbsp;'||to_char(peri.dt_fechamento, 'dd/mm') as periodo ";
   $sql .= " from   projetos.periodos_emprestimo_web peri, ";
   $sql .= "        patrocinadoras patr ";
   $sql .= " where  patr.cd_empresa = peri.cd_empresa ";
   $sql .= " order by peri.ano, peri.cd_empresa, peri.mes ";
   $rs = pg_exec($db, $sql);
   $ano = 0;
   $mes = 0;
   while ($reg = pg_fetch_array($rs)) {
      if ($reg['ano'] != $ano) {
	     $tpl->newBlock('blk_ano');
		 $tpl->assign('ano', $reg['ano']);
	  }
	  $ano = $reg['ano'];
	  if ($reg['mes'] == 1) {
	     $tpl->newBlock('blk_empresa');
		 $tpl->assign('empresa', $reg['nome_empresa']);
	  }
	  $tpl->newBlock('blk_mes');
	  $tpl->assign('periodo', $reg['periodo']);
   }
   $tpl->printToScreen();
?>