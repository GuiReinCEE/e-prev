<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
// ------------------------------------------------
	if ($site == '') { $site = 0; }
	if ($evento == '') { $evento = 0; }
	if ($servico == '') { $servico = 0; }
	if ($publicacao == '') { $publicacao = 0; }
	if ($ultimo_respondente == '') { $ultimo_respondente = 0; }
// ------------------------------------------------
	$txt_dt_inicio = ( $dt_inicio == '' ? 'Null' : "'".convdata_br_iso($dt_inicio)."'" );
	$txt_dt_fim = ( $dt_fim	== '' ? 'Null' : "'".convdata_br_iso($dt_fim)."'" );
	if ($codigo <> "") {
		$sql = "select cd_pergunta from projetos.enquete_perguntas where cd_enquete = $codigo and texto is null and pergunta_texto is not null";
//			echo $sql;
	} else {
		$sql = "select max(cd_enquete) as cd_enquete from projetos.enquetes";
//			echo $sql;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$codigo = $reg['cd_enquete'];
		$sql = "select cd_pergunta from projetos.enquete_perguntas where cd_enquete = $codigo and texto is null and pergunta_texto is not null";
	}
//	echo $sql;
	$rs = pg_exec($db, $sql);
	$reg = pg_fetch_array($rs); // <== verifica a existência de pergunta texto
	$nlinhas =	pg_numrows($rs);
	if ($agrup_diss == '') { $agrup_diss = 0; }
//		echo 'Número de linhas: '.$nlinhas;
	if ($agrup_diss == '/')  { $agrup_diss = 0; }
	if ($nlinhas != 0) {
		$cd_pergunta = $reg['cd_pergunta'];
		$sql =        " update 	projetos.enquete_perguntas ";
		$sql = $sql . " set 	pergunta_texto = '$pergunta_texto', cd_agrupamento = " . $agrup_diss;
		$sql = $sql . " where 	cd_enquete = $codigo and cd_pergunta = $cd_pergunta ";
	} else {
		$sql =        " insert 	into projetos.enquete_perguntas ( ";
		$sql = $sql . "        	cd_enquete , ";
		$sql = $sql . "        	cd_agrupamento , ";
		$sql = $sql . "        	pergunta_texto )";
		$sql = $sql . " values 	( ";
		$sql = $sql . "        	$codigo, ";
		$sql = $sql . "        	$agrup_diss, ";
		$sql = $sql . "     	'$pergunta_texto' ) ";
	}
//		echo $sql;
	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		if ($codigo <> "") {
			header('location: cad_enquetes_estrutura.php?c='.$codigo);
		} else {
			header('location: lst_enquetes.php');
		}
	} else {
		pg_close($db);
		header('location: lst_enquetes.php?msg=Ocorreu um erro ao tentar gravar esta enquete.');
	}
// ------------------------------------------------
function convdata_br_iso($dt) {
      // Pressupõe que a data esteja no formato DD/MM/AAAA
      // A melhor forma de gravar datas no PostgreSQL é utilizando 
      // uma string no formato DDDD-MM-AA. Esta função justamente 
      // adequa a data a este formato
      $d = substr($dt, 0, 2);
      $m = substr($dt, 3, 2);
      $a = substr($dt, 6, 4);
      return $a.'-'.$m.'-'.$d;
}
?>