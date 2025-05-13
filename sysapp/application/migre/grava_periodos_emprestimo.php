<?php
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/funcoes.php');
   include_once('inc/funcoes_gerais.php');
//   echo count($_POST["mes"]);
//   echo $_POST["mes"][0];
   for ($i=0; $i < count($_POST["mes"]); $i++) {
      $dt_abertura = dataBr_Iso($_POST['dt_abertura'][$i]);
      $dt_fechamento = dataBr_Iso($_POST['dt_fechamento'][$i]);
	  $mes = $_POST['mes'][$i];
	  $ano = $_POST['ano'];
	  $emp = $_POST['empresa'];
      $sql = "select 1 as ok from projetos.periodos_emprestimo_web where ano=$ano and mes=$mes and cd_empresa=$emp";
      $rs = pg_exec($db, $sql);
      $reg = pg_fetch_array($rs);
      if ($reg['ok'] == 1) { // Atualização
         if ( ($dt_abertura != '--') and ($dt_fechamento != '--')) { // Testa se as datas foram preenchidas
            $sql  = " update projetos.periodos_emprestimo_web ";
            $sql .= " set    dt_abertura = '$dt_abertura', ";
            $sql .= "        dt_fechamento = '$dt_fechamento' ";
            $sql .= " where  ano = $ano ";
            $sql .= "   and  mes = $mes ";
			$sql .= "   and  cd_empresa = $emp ";
         } 
         else {
            $sql  = " update projetos.periodos_emprestimo_web ";
            $sql .= " set    dt_abertura = null, ";
            $sql .= "        dt_fechamento = null ";
            $sql .= " where  ano = $ano ";
            $sql .= "   and  mes = $mes ";		 
			$sql .= "   and  cd_empresa = $emp ";
         }
      }
      else {
         if ( ($dt_abertura != '--') and ($dt_fechamento != '--')) { // Testa se as datas foram preenchidas
            $sql  = " insert into projetos.periodos_emprestimo_web (ano, mes, dt_abertura, dt_fechamento, cd_empresa) ";
            $sql .= " values ($ano, $mes, '$dt_abertura', '$dt_fechamento', $emp) ";
         }
		 else {
            $sql  = " insert into projetos.periodos_emprestimo_web (ano, mes, dt_abertura, dt_fechamento, cd_empresa) ";
            $sql .= " values ($ano, $mes, null, null, $emp) ";
         }
	  }
      $rs = pg_exec($db, $sql);
   }
   pg_close($db);
   header("location: cad_periodos_emprestimo.php");
?>