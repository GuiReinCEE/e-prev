<?php
   include_once("inc/sessao.php");
   include_once("inc/conexao.php");
   include_once("inc/funcoes_gerais.php");
   
   $operacao = $_POST['operacao'];
   $ano      = $_POST['ano'];
   $numero   = $_POST['numero'];
   $data     = $_POST['data'];

   if ( ($operacao == "I") or ($operacao == "A") ) {
	   if ($operacao == 'I') {
		  $ano   = date('Y');
//          $ano = 2005;
		  $numero = buscaNumero($db, $ano);
		  $sql  = " insert into projetos.correspondencias ( ";
		  $sql .= "          numero, ";
		  $sql .= "          ano, ";
		  $sql .= "          divisao, ";
		  $sql .= "          solicitante_emp, ";
		  $sql .= "          solicitante_re, ";
		  $sql .= "          solicitante_seq, ";
		  $sql .= "          assinatura_emp, ";
		  $sql .= "          assinatura_re, ";
		  $sql .= "          assinatura_seq, ";
		  $sql .= "          destinatario_nome, ";
		  $sql .= "          assunto, ";
		  $sql .= "          usuario, ";
		  $sql .= "          data ) ";
		  $sql .= " values ( ";	  
		  $sql .= "          $numero, ";
		  $sql .= "          $ano, ";
		  $sql .= "          '".$_POST['divisao']."',";
		  $sql .= "          ".$_POST['solicitante_emp'].", ";
		  $sql .= "          ".$_POST['solicitante_re'].", ";
		  $sql .= "          ".$_POST['solicitante_seq'].", ";
		  $sql .= "          ".$_POST['assinatura_emp'].", ";
		  $sql .= "          ".$_POST['assinatura_re'].", ";
		  $sql .= "          ".$_POST['assinatura_seq'].", ";
		  $sql .= "          '".$_POST['destinatario_nome']."', ";
		  $sql .= "          '".$_POST['assunto']."', ";
		  $sql .= "          '".$_SESSION['U']."', ";
		  $sql .= "          '".dataBr_Iso($_POST['data'])."') ";
          pg_exec($db, $sql);
	   }
	   else {
		   
		if ($_SESSION['D'] == 'SG'){

		  if ( ($operacao == 'A') ) {
			  $sql  = " update projetos.correspondencias set ";
			  $sql .= "        divisao='".$_POST['divisao']."',";
			  $sql .= "        solicitante_emp=".$_POST['solicitante_emp'].", ";
			  $sql .= "        solicitante_re=".$_POST['solicitante_re'].", ";
			  $sql .= "        solicitante_seq=".$_POST['solicitante_seq'].", ";
			  $sql .= "        assinatura_emp=".$_POST['assinatura_emp'].", ";
			  $sql .= "        assinatura_re=".$_POST['assinatura_re'].", ";
			  $sql .= "        assinatura_seq=".$_POST['assinatura_seq'].", ";
			  $sql .= "        destinatario_nome='".$_POST['destinatario_nome']."', ";
			  $sql .= "        assunto='".$_POST['assunto']."', ";
			  $sql .= "        data_alt = current_timestamp, ";
              $sql .= "        data = '".dataBr_Iso($_POST['data'])."', ";
              $sql .= "        usuario_alt = '".$_SESSION['U']."' ";
			  $sql .= " where  ano     = $ano";
			  $sql .= "   and  numero  = $numero";
//			  $sql .= "   and  usuario ='".$_SESSION['CODU']."'";

              pg_exec($db, $sql);
		  }
		 }else{
		 		$erro = "permissao";
		 }
	   }
       pg_close($db);
   }
   header("location: frm_correspondencias.php?n=$numero&a=$ano&erro=$erro");
   
   // ----- Funções -----
   function buscaNumero($cnx, $ano) {
      $sql = "select correspondencias_seq from projetos.correspondencias_sequences where ano=$ano";
	  $rs = pg_exec($cnx, $sql);
	  if ($reg=pg_fetch_array($rs)) {
         $numero = $reg['correspondencias_seq'];
		 $novo_numero = $numero+1;
		 $sql = "update projetos.correspondencias_sequences set correspondencias_seq=$novo_numero where ano=$ano";
		 pg_exec($cnx, $sql);
	  }
	  else {
	     $numero = 1;
         $sql  = " insert into projetos.correspondencias_sequences ( ";
         $sql .= "        correspondencias_seq, ";
		 $sql .= "        docs_recebidos_seq, ";
		 $sql .= "        ano) ";
		 $sql .= " values (2, 1, $ano) ";
		 pg_exec($cnx, $sql);
	  }
	  return $numero;
   }
?>