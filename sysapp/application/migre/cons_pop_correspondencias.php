<?php
   include_once("inc/sessao.php");
   include_once("inc/conexao.php");
   $numero = $_REQUEST['n'];
   $ano    = $_REQUEST['a'];
   $op     = $_REQUEST['op'];
/*
   echo "<br>Ano: ".$ano;
   echo "<br>Num: ".$numero;
   echo "<br>Op: ".$op;
   echo "<br><br>";
*/ 
   // Se o ano não está preenchido, busca o ano
   if ( (! isset($_REQUEST['a'])) or ($ano == '') ) {
      $sql = '';
      switch($op) {
	     case 'U': $sql = " select max(ano) as ano from projetos.correspondencias "; break;
         default : $sql = " select min(ano) as ano from projetos.correspondencias "; break;
      }
      $rs = pg_exec($db, $sql);
      $reg = pg_fetch_array($rs);
      $ano = $reg['ano'];
   }
   
   if ( ($op == 'U') or ($op == 'P') ) {
      switch($op) {
         case 'U': $sql = "select max(numero) as num from projetos.correspondencias where ano = $ano "; break;
         case 'P': $sql = "select min(numero) as num from projetos.correspondencias where ano = $ano "; break;
      }	  
//      echo $sql;
      $rs = pg_exec($db, $sql);
      $reg = pg_fetch_array($rs);
      $numero = $reg['num'];
   } else {
      if ( (! isset($_REQUEST['n'])) || ($numero == '') ) {
	     $sql = "select min(numero) as num from projetos.correspondencias where ano = $ano ";
         $rs = pg_exec($db, $sql);
         $reg = pg_fetch_array($rs);
         $numero = $reg['num'];
	  }
   }
/*   
   echo "<br>Ano: ".$ano;
   echo "<br>Num: ".$numero;
   echo "<br><br>";
*/
   $sql  = " select cor.numero,  ";
   $sql .= "        cor.ano, ";
   $sql .= "        cor.divisao, ";
   $sql .= "        cor.solicitante_emp, ";
   $sql .= "        cor.solicitante_re, ";
   $sql .= "        cor.solicitante_seq, ";
   $sql .= "        cor.assinatura_emp, ";
   $sql .= "        cor.assinatura_re, ";
   $sql .= "        cor.assinatura_seq, ";
   $sql .= "        cor.destinatario_nome, ";
   $sql .= "        cor.assunto, ";
   $sql .= "        to_char(cor.data, 'DD/MM/YYYY') as data, ";
//   $sql .= "        cor.protocolo, ";
//   $sql .= "        cor.retorno_protocolo, ";
//   $sql .= "        cor.tipo_protocolo, ";
   $sql .= "        sol.nome as solicitante_nome, ";
   $sql .= "        ass.nome as assinatura_nome, ";
   $sql .= "        cor.usuario ";
   $sql .= " from   projetos.correspondencias cor, ";
   $sql .= "        participantes sol, ";
   $sql .= "        participantes ass  ";
   $sql .= " where  sol.cd_empresa            = cor.solicitante_emp ";
   $sql .= "   and  sol.cd_registro_empregado = cor.solicitante_re ";
   $sql .= "   and  sol.seq_dependencia       = cor.solicitante_seq ";
   $sql .= "   and  ass.cd_empresa            = cor.assinatura_emp ";
   $sql .= "   and  ass.cd_registro_empregado = cor.assinatura_re ";
   $sql .= "   and  ass.seq_dependencia       = cor.assinatura_seq ";
   //
   $sql .= "   and  cor.numero                = $numero ";
   $sql .= "   and  cor.ano                   = $ano ";
   //
   
   $rs = pg_exec($db, $sql);
   echo "<script language='JavaScript'>";
   if ($reg = pg_fetch_array($rs)) {
      echo "   parent.document.getElementById('operacao').value = 'A';";
      echo "   parent.document.getElementById('numero').value = ".$reg['numero'].";";
      echo "   parent.document.getElementById('ano').value = ".$reg['ano'].";";
      echo "   parent.document.getElementById('divisao').value = '".$reg['divisao']."';";
      echo "   parent.document.getElementById('solicitante_emp').value = ".$reg['solicitante_emp'].";";
      echo "   parent.document.getElementById('solicitante_re').value = ".$reg['solicitante_re'].";";
      echo "   parent.document.getElementById('solicitante_seq').value = ".$reg['solicitante_seq'].";";
      echo "   parent.document.getElementById('solicitante_nome').value = '".$reg['solicitante_nome']."';";
      echo "   parent.document.getElementById('assinatura_emp').value = ".$reg['assinatura_emp'].";";
      echo "   parent.document.getElementById('assinatura_re').value = ".$reg['assinatura_re'].";";
      echo "   parent.document.getElementById('assinatura_seq').value = ".$reg['assinatura_seq'].";";
      echo "   parent.document.getElementById('assinatura_nome').value = '".$reg['assinatura_nome']."';";
      echo "   parent.document.getElementById('destinatario_nome').value = '".$reg['destinatario_nome']."';";
//      echo "   parent.document.getElementById('assunto').value = '".str_replace(chr(13), "\r", str_replace(chr(10), "", $reg['assunto']))."';";
      echo "   parent.document.getElementById('assunto').value = '".str_replace(chr(13), "\\n", str_replace(chr(10), "", $reg['assunto']))."';";
      echo "   parent.document.getElementById('data').value = '".$reg['data']."';";
	  
      // Botões Avançar e Voltar
      $sql = "select coalesce(max(numero), -1) as num from projetos.correspondencias where ano=$ano and numero<$numero limit 1";
	  $rs = pg_exec($db, $sql);
      $regMov = pg_fetch_array($rs);
      echo "   parent.document.getElementById('ante').value=".$regMov['num'].";";

      $sql = "select coalesce(min(numero),-1) as num from projetos.correspondencias where ano=$ano and numero>$numero limit 1";
	  $rs = pg_exec($db, $sql);
      $regMov = pg_fetch_array($rs);
      echo "   parent.document.getElementById('prox').value=".$regMov['num'].";";

	  // Permissões de acesso
	  if ($reg['usuario'] != $_SESSION['CODU']) {
         echo "   parent.setaModo('C');";
	  }
	  else {
	     echo "   parent.setaModo('A');";
	  }
   }
   else {
      echo "   parent.setaModo('C');";
      echo "   alert('Correspondência nº $numero de $ano não encontrada');";
   }
   echo "</script>";

?>