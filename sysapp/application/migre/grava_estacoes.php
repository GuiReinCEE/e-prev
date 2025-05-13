<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
    include_once('../inc/class.Email.inc.php');
	$txt_dt_inclusao  = ( $dt_inclusao  == '' ? 'Null' : "'".convdata_br_iso($dt_inclusao)."'" );
//-----------------------------------------------------------------------------------------------
	while(list($key, $value) = each($HTTP_POST_VARS)) 
	{ 
//		echo $value .  '<br>'; 
		$v_str = $key;
		//echo substr($v_str, 10) . '<br>';
		if (substr_count($v_str, "opt_ativo_") > 0) {
			$m = fnc_atualiza_estacao(str_replace('_', '.', substr($v_str, 10)), $db, $value);
			$cd_publicacao = $value;
		}
	} 
//-----------------------------------------------------------------------------------------------
	pg_close($db);
	header('location: adm_atendimento.php');
//-----------------------------------------------------------------------------------------------
function fnc_atualiza_estacao($ip_estacao, $db, $cd_estado) {	
	if (isset($ip_estacao)) {
//		echo $cd_estado . '<br>';
		if (($cd_estado == 'P') or ($cd_estado == 'T') or ($cd_estado == 'C') or ($cd_estado == 'N')) {
			$sql = 			" update projetos.usuarios_controledi ";
			$sql = $sql . 	" set indic_08 = '" . $cd_estado . "' ";
	    	$sql = $sql . 	" where estacao_trabalho = '" . $ip_estacao . "' ";
//			echo $sql;
			$s = (pg_exec($db, $sql));
		}
	}
	return $ret;
}
?>