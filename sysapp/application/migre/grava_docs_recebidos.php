<?php
   include_once("inc/sessao.php");
   include_once("inc/conexao.php");
   include_once("inc/funcoes_gerais.php");
   
   $operacao = $_REQUEST['operacao'];
   $ano = $_REQUEST['ano'];
   $numero = $_REQUEST['numero'];

//print_r($_REQUEST);
//echo "FFF: ".$_REQUEST['tipo_protocolo'];
  
   if ( ($operacao == "I") or ($operacao == "A") ) {
	   if ($operacao == 'I') {
		  $ano   = date('Y');
		  $numero = buscaNumero($db, $ano);
		  $sql  = " insert into projetos.docs_recebidos ( ";
		  $sql .= "          numero, ";
		  $sql .= "          ano, ";
		  $sql .= "          datahora, ";
		  $sql .= "          remetente, ";
		  $sql .= "          assunto, ";
		  $sql .= "          destino_emp, ";
		  $sql .= "          destino_re, ";
		  $sql .= "          destino_seq, ";
		  $sql .= "          usuario, ";
		  $sql .= "          data_cad ) ";
		  $sql .= " values ( ";	  
		  $sql .= "          $numero, ";
		  $sql .= "          $ano, ";
		  $sql .= "          to_timestamp( '".$_REQUEST['datahora']."', 'DD/MM/YYYY HH24:MI' ) , ";
		  $sql .= "          '".$_REQUEST['remetente']."', ";
		  $sql .= "          '".$_REQUEST['assunto']."', ";
		  $sql .= "          ".$_REQUEST['destino_emp'].", ";
		  $sql .= "          ".$_REQUEST['destino_re'].", ";
		  $sql .= "          ".$_REQUEST['destino_seq'].", ";
		  $sql .= "          '".$_SESSION['CODU']."', ";
		  $sql .= "           current_timestamp ) ";
          pg_exec($db, $sql);
	   }
	   else {
		  if ($operacao == 'A') {
			  $sql  = " update projetos.docs_recebidos set ";
			  $sql .= "        datahora=to_timestamp( '".$_REQUEST['datahora']."', 'DD/MM/YYYY HH24:MI' ),";
			  $sql .= "        remetente='".$_REQUEST['remetente']."', ";
			  $sql .= "        assunto='".$_REQUEST['assunto']."', ";
			  $sql .= "        destino_emp=".$_REQUEST['destino_emp'].", ";
			  $sql .= "        destino_re=".$_REQUEST['destino_re'].", ";
			  $sql .= "        destino_seq=".$_REQUEST['destino_seq'].", ";
			  $sql .= "        data_alteracao=current_timestamp ";
			  $sql .= " where  ano     = $ano";
			  $sql .= "   and  numero  = $numero";
//			  $sql .= "   and  usuario ='".$_SESSION['CODU']."'";
              pg_exec($db, $sql);
		  }
	   }
       pg_close($db);
   }
   
			 // echo $sql;
   
   header("location: frm_docs_recebidos.php?n=$numero&a=$ano");
   
   // ----- Funções -----
   function buscaNumero($cnx, $ano) {
      $sql = "select docs_recebidos_seq from projetos.correspondencias_sequences where ano=$ano";
	  $rs = pg_exec($cnx, $sql);
	  if ($reg=pg_fetch_array($rs)) {
         $numero = $reg['docs_recebidos_seq'];
		 $novo_numero = $numero+1;
		 $sql = "update projetos.correspondencias_sequences set docs_recebidos_seq=$novo_numero where ano=$ano";
		 pg_exec($cnx, $sql);
	  }
	  else {
	     $numero = 1;
         $sql  = " insert into projetos.correspondencias_sequences ( ";
         $sql .= "        correspondencias_seq, ";
		 $sql .= "        docs_recebidos_seq, ";
		 $sql .= "        ano) ";
		 $sql .= " values ($numero, $numero, $ano) ";
		 pg_exec($cnx, $sql);
	  }
	  return $numero;
   }
?>