<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.Email.inc.php');
// ------------------------------------------------------------
	$sql = "select count(*) as num_regs from participantes_ccin where cd_registro_empregado = $re and cd_empresa = 7 ";
	$rs = pg_exec($db, $sql);
	$reg = pg_fetch_array($rs);
	if ($reg['num_regs'] == 0) {
		$sql =        "  select   codigo_345, nome, email, cd_registro_empregado, cpf, rg ";
		$sql = $sql . "    from   expansao.inscritos  ";
		$sql = $sql . "    where  cd_registro_empregado = $re and cd_empresa = 7 ";
	}
	else	{
		$sql =        "  select   c.codigo_345, i.nome, i.email, i.cd_registro_empregado, i.cpf, i.rg ";
		$sql = $sql . "    from   expansao.inscritos i, participantes_ccin c ";
		$sql = $sql . "    where  i.cd_registro_empregado = $re  and i.cd_registro_empregado = c.cd_registro_empregado and i.cd_empresa = c.cd_empresa ";
	}
	$rs = pg_exec($db, $sql);
	$reg = pg_fetch_array($rs);
// ----------------- 
	$msg = "Prezada(o) ".$reg['nome'];
	$v_assunto = 'Senha SENGE Previdencia';
	$v_para = $reg['email'];
	$v_cc = '';
	$v_cco = '';
	$v_de = 'FUNDAЧУO CEEE - Senge Previdencia';
	$senha = $reg['codigo_345'];	
	$vbcrlf = chr(10).chr(13);
	$msg = $msg . $vbcrlf . $vbcrlf;
	$msg = $msg . "Sua senha pessoal do plano Senge Previdъncia encontra-se logo abaixo nesta mensagem." . $vbcrlf;
	$msg = $msg . "Confira seus dados pessoais:" . $vbcrlf;;
// ------------------------- Сrea da mensagem texto:
	$msg = $msg . "-------------------------------------------------------------".$vbcrlf;
	$msg = $msg . "REd (sua identificaчуo junto р Fundaчуo CEEE): " . $reg['cd_registro_empregado'] . $vbcrlf;
	$msg = $msg . "Nome: " . $reg['nome']. $vbcrlf;
	$msg = $msg . "CPF: " . $reg['cpf'] . $vbcrlf;
	$msg = $msg . "Identidade (RG): " . $reg['rg'] . $vbcrlf;
	$msg = $msg . "Sua senha pessoal para acesso ao auto-atendimento: " . $senha . $vbcrlf;
	$msg = $msg . "-------------------------------------------------------------".$vbcrlf;
	$msg = $msg . "por favor, caso vocъ nуo tenha solicitado o envio deste email, entre em contato com nosso auto-atendimento: 0800-51-2596" . $vbcrlf;
	$msg = $msg . "-------------------------------------------------------------".$vbcrlf;
	$msg = $msg . "Esta mensagem foi enviada pelo Sistema SENGE Previdъncia.". $vbcrlf;
//	$e->SetBody($msg);
//	return $e->Send() and $ret;
	$date = date("d/m/Y");
	$sql =        " insert into projetos.envia_emails ( ";
	$sql = $sql . "		dt_envio, ";
	$sql = $sql . "		de, ";
	$sql = $sql . "		para, ";
	$sql = $sql . "		cc,	";
	$sql = $sql . "		cco, ";
	$sql = $sql . "		assunto, ";
	$sql = $sql . "		texto ";
	$sql = $sql . " ) ";
	$sql = $sql . " VALUES ( ";
	$sql = $sql . "		CURRENT_TIMESTAMP, ";
	$sql = $sql . "		'$v_de', ";
	$sql = $sql . "		'$v_para', ";
	$sql = $sql . "		'$v_cc', ";
	$sql = $sql . "    	'$v_cco', ";
	$sql = $sql . "    	'$v_assunto', ";
	$sql = $sql . "    	'$msg' ";
	$sql = $sql . ")";	 
//	echo $sql;
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: cad_inscritos_contato.php?c='.$re.'&a=a');
   }
   else {
      pg_close($db);
	  echo "Ocorreu um erro ao tentar incluir esta tarefa";
   }
 
   function convdata_br_iso($dt) {
      // Pressupѕe que a data esteja no formato DD/MM/AAAA
      // A melhor forma de gravar datas no PostgreSQL щ utilizando 
      // uma string no formato DDDD-MM-AA. Esta funчуo justamente 
      // adequa a data a este formato
      $d = substr($dt, 0, 2);
      $m = substr($dt, 3, 2);
      $a = substr($dt, 6, 4);
      return $a.'-'.$m.'-'.$d;
   }
?>