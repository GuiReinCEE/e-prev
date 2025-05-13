<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/nextval_sequence.php');
	include_once('inc/funcoes.php');
// ------------------------------------------------
	$sql = "select max(versao_certificado) as nmax from planos_certificados where cd_plano = ".$p;
	$rs=pg_query($db, $sql);
	$reg = pg_fetch_array($rs);
	$cd_novo_certificado = $reg['nmax'] + 1;
// ------------------------------------------------ Enquete:
	$sql = "insert into planos_certificados (
			cd_plano,
			nome_certificado,
			cd_spc,
			largura_imagem,
			pos_imagem,
			coluna_1,
			coluna_2,
			versao_certificado ) 
			(select ".$p . ",
			nome_certificado,
			cd_spc,
			largura_imagem,
			pos_imagem,
			coluna_1,
			coluna_2,
			".$cd_novo_certificado."  
			from planos_certificados where cd_plano = ".$p." and versao_certificado = ".$v.") ";
// ------------------------------------------------
//	echo $sql;
	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_planos.php?p='.$p.'&msg=Sua pesquisa foi duplicada com o nњmero'.$cd_nova_enquete);
	} else {
		pg_close($db);
		header('location: lst_planos.php?p='.$p.'&msg=Ocorreu um erro ao tentar gravar esta enquete.');
	}
// ------------------------------------------------	
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