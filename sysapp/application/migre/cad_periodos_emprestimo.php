<?php
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   $tpl = new TemplatePower("tpl/tpl_periodos_emprestimo.html");
   $tpl->prepare();
   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
   $tpl->assign('divsao', $D);

   if ( (isset($_REQUEST['a'])) and (isset($_REQUEST['e'])) ) {
      $emp = $_REQUEST['e'];
      $ano = $_REQUEST['a'];
   } else {
      if (isset($_POST['ano_consulta'])) {
         $emp = $_POST['empresa_consulta'];
         $ano = $_POST['ano_consulta'];
      } else {
	     $emp = '';
		 $ano = '';
	  }
   }

   $tpl->assign('ano_consulta', $_POST['ano_consulta']);
/*   
   if (isset($_POST['ano_consulta'])) {
      $sql = "select cd_empresa, nome_reduz from patrocinadoras order by cd_empresa";
	  $rs = pg_exec($db, $sql);
	  while ($reg = pg_fetch_array($rs)) {
	     $tpl->newBlock('blk_empresa');
		 $tpl->assign('empresa', $reg['cd_empresa']);
		 $tpl->assign('nome_empresa', $reg['nome_reduz']);
		 if ($reg['cd_empresa'] == $_POST['empresa_consulta']) {
		    $tpl->assign('chkEmp', 'selected');
	     }
	  }
      $tpl->newBlock('blk_param_form');
      $tpl->assign('ano', $_POST['ano_consulta']);
      $tpl->assign('empresa', $_POST['empresa_consulta']);
      $sql  = " SELECT ano, mes, ";
	  $sql .= "        to_char(dt_abertura, 'dd/mm/yyyy') as dt_abertura, ";
	  $sql .= "        to_char(dt_fechamento, 'dd/mm/yyyy') as dt_fechamento ";
	  $sql .= " FROM   PROJETOS.PERIODOS_EMPRESTIMO_WEB ";
	  $sql .= " WHERE  ANO=".$_POST['ano_consulta'];
	  $sql .= "   AND  CD_EMPRESA=".$_POST['empresa_consulta'];
	  $sql .= " ORDER BY ANO, MES ";
	  $rs   = pg_exec($db, $sql);
	  if (pg_num_rows($rs) > 0) {
         while ($reg = pg_fetch_array($rs)) {
		    $tpl->newBlock('blk_periodos');
			$tpl->assign('mes', $reg['mes']);
			$tpl->assign('dt_abertura', $reg['dt_abertura']);
			$tpl->assign('dt_fechamento', $reg['dt_fechamento']);
		 }
	  }
	  else {
	     for ($i = 1; $i < 13; $i++) {
		    $tpl->newBlock('blk_periodos');
			$tpl->assign('mes', $i);
		 }
	  }
	  pg_close($db);
   } else {
      $sql = "select cd_empresa, nome_reduz from patrocinadoras order by cd_empresa";
	  $rs = pg_exec($db, $sql);
	  while ($reg = pg_fetch_array($rs)) {
	     $tpl->newBlock('blk_empresa');
		 $tpl->assign('empresa', $reg['cd_empresa']);
		 $tpl->assign('nome_empresa', $reg['nome_reduz']);
	  }
   }
*/
   if ($ano != '') {
      $sql = "select cd_empresa, sigla AS nome_reduz from patrocinadoras order by cd_empresa";
	  $rs = pg_exec($db, $sql);
	  while ($reg = pg_fetch_array($rs)) {
	     $tpl->newBlock('blk_empresa');
		 $tpl->assign('empresa', $reg['cd_empresa']);
		 $tpl->assign('nome_empresa', $reg['nome_reduz']);
		 if ($reg['cd_empresa'] == $emp) {
		    $tpl->assign('chkEmp', 'selected');
	     }
	  }
      $tpl->newBlock('blk_param_form');
      $tpl->assign('ano', $ano);
      $tpl->assign('empresa', $emp);
      $sql  = " SELECT ano, mes, ";
	  $sql .= "        to_char(dt_abertura, 'dd/mm/yyyy') as dt_abertura, ";
	  $sql .= "        to_char(dt_fechamento, 'dd/mm/yyyy') as dt_fechamento ";
	  $sql .= " FROM   PROJETOS.PERIODOS_EMPRESTIMO_WEB ";
	  $sql .= " WHERE  ANO=".$ano;
	  $sql .= "   AND  CD_EMPRESA=".$emp;
	  $sql .= " ORDER BY ANO, MES ";
	  
	  $rs   = pg_exec($db, $sql);
	  if (pg_num_rows($rs) > 0) {
         while ($reg = pg_fetch_array($rs)) {
		    $tpl->newBlock('blk_periodos');
			$tpl->assign('mes', $reg['mes']);
			$tpl->assign('dt_abertura', $reg['dt_abertura']);
			$tpl->assign('dt_fechamento', $reg['dt_fechamento']);
		 }
	  }
	  else {
	     for ($i = 1; $i < 13; $i++) {
		    $tpl->newBlock('blk_periodos');
			$tpl->assign('mes', $i);
		 }
	  }
	  pg_close($db);
   } else {
      $sql = "select cd_empresa, sigla AS nome_reduz from patrocinadoras order by cd_empresa";
	  $rs = pg_exec($db, $sql);
	  while ($reg = pg_fetch_array($rs)) {
	     $tpl->newBlock('blk_empresa');
		 $tpl->assign('empresa', $reg['cd_empresa']);
		 $tpl->assign('nome_empresa', $reg['nome_reduz']);
	  }
      $ano = date('Y');
	  $tpl->assignGlobal('ano_consulta', $ano);
   }
   $tpl->printToScreen();
?>