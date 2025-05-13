<?php
   include_once("inc/sessao.php");
   include_once("inc/conexao.php");
   $numero = $_REQUEST['n'];
   $ano    = $_REQUEST['a'];
   $op     = $_REQUEST['op'];
    // Se o ano não está preenchido, busca o ano
   if ( (! isset($_REQUEST['a'])) or ($ano == '') ) {
      $sql = '';
      switch($op) {
	     case 'U': $sql = " select max(ano) as ano from projetos.docs_recebidos "; break;
         default : $sql = " select min(ano) as ano from projetos.docs_recebidos "; break;
      }
      $rs = pg_exec($db, $sql);
      $reg = pg_fetch_array($rs);
      $ano = $reg['ano'];
   }
   
   if ( ($op == 'U') or ($op == 'P') ) {
      switch($op) {
         case 'U': $sql = "select max(numero) as num from projetos.docs_recebidos where ano = $ano "; break;
         case 'P': $sql = "select min(numero) as num from projetos.docs_recebidos where ano = $ano "; break;
      }	  
//      echo $sql;
      $rs = pg_exec($db, $sql);
      $reg = pg_fetch_array($rs);
      $numero = $reg['num'];
   } else {
      if ( (! isset($_REQUEST['n'])) || ($numero == '') ) {
	     $sql = "select min(numero) as num from projetos.docs_recebidos where ano = $ano ";
         $rs = pg_exec($db, $sql);
         $reg = pg_fetch_array($rs);
         $numero = $reg['num'];
	  }
   }
   
   $sql  = " select doc.numero,  ";
   $sql .= "        doc.ano, ";
   $sql .= "        to_char(doc.datahora, 'dd/mm/yyyy hh24:mi') as datahora, ";
   $sql .= "        remetente, ";
   $sql .= "        doc.assunto, ";
   $sql .= "        doc.destino_emp, ";
   $sql .= "        doc.destino_re, ";
   $sql .= "        doc.destino_seq, ";
   $sql .= "        part.nome ";
   $sql .= " from   projetos.docs_recebidos doc, ";
   $sql .= "        participantes part ";
   $sql .= " where  part.cd_empresa            = doc.destino_emp ";
   $sql .= "   and  part.cd_registro_empregado = doc.destino_re ";
   $sql .= "   and  part.seq_dependencia       = doc.destino_seq ";
   $sql .= "   and  doc.ano=$ano";
   $sql .= "   and  doc.numero= $numero";
   $rs = pg_exec($db, $sql);
   echo "<script language='JavaScript'>";
   if ($reg = pg_fetch_array($rs)) {
      echo "   parent.document.getElementById('operacao').value = 'A';";
      echo "   parent.document.getElementById('numero').value = ".$reg['numero'].";";
      echo "   parent.document.getElementById('ano').value = ".$reg['ano'].";";
      echo "   parent.document.getElementById('datahora').value = '".$reg['datahora']."';";
      echo "   parent.document.getElementById('remetente').value = '".$reg['remetente']."';";
      echo "   parent.document.getElementById('assunto').value = '".str_replace(chr(13), "\\n", str_replace(chr(10), "", $reg['assunto']))."';";
      echo "   parent.document.getElementById('destino_emp').value = ".$reg['destino_emp'].";";
      echo "   parent.document.getElementById('destino_re').value = ".$reg['destino_re'].";";
      echo "   parent.document.getElementById('destino_seq').value = ".$reg['destino_seq'].";";
      echo "   parent.document.getElementById('destino_nome').value = '".$reg['nome']."';";
	  
	  // Botões Avançar e Voltar
      $sql = "select coalesce(max(numero), -1) as num from projetos.docs_recebidos where ano=$ano and numero<$numero limit 1";
	  $rs = pg_exec($db, $sql);
      $regMov = pg_fetch_array($rs);
      echo "   parent.document.getElementById('ante').value=".$regMov['num'].";";

      $sql = "select coalesce(min(numero),-1) as num from projetos.docs_recebidos where ano=$ano and numero>$numero limit 1";
	  $rs = pg_exec($db, $sql);
      $regMov = pg_fetch_array($rs);
      echo "   parent.document.getElementById('prox').value=".$regMov['num'].";";
	  
	  // Permissões de acesso
	  if ($reg['usuario'] != $_SESSION['CODU']) {
         echo "parent.setaModo('C');";
	  }
	  else {
	     echo "parent.setaModo('A');";
	  }
   }
   else {
      echo "parent.setaModo('C');";
      echo "alert('Documento nº $numero de $ano não encontrada');";
   }
   echo "</script>";

?>