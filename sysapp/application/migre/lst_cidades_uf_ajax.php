<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
// --------------------------------------------------------------- Busca as cidades do estado indicado
	$sql = "select cd_municipio_ibge, cd_microregiao, cd_macroregiao, nome_cidade from expansao.cidades where sigla_uf = UPPER('".$cd_estado."') order by nome_cidade";
	$rs = pg_query($sql);
	echo "<select name='lista_cidades' id='lista_cidades'>";                              
	echo "<option value=''>Selecione ...</option>";
	while ($reg = pg_fetch_object($rs)) 
	{
	 	echo "<option value=".$reg->cd_municipio_ibge.">".$reg->nome_cidade."</option>";
	}
	echo "</select>";
?>