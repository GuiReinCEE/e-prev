<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	echo "<PRE>"; print_r($_REQUEST); echo "</PRE>";

	
	/*
	while(list($questao, $valor) = each($_REQUEST)) 
	{ 
		if (ereg('R_',$questao))
		{
			$a['ENQ_AR_RESPOSTA'][$questao."_".$cd_agrupamento] = Array(
													'cd_enquete'     => $_SESSION['ENQ_CD_ENQUETE'],
													'cd_agrupamento' => $_REQUEST['cd_agrupamento'],
													'ip'             => $_SESSION['ENQ_CD_EMPRESA'].".".$_SESSION['ENQ_CD_REGISTRO_EMPREGADO'].".".$_SESSION['ENQ_SEQ_DEPENDENCIA'],
													'questao'        => $questao,
													'valor'          => $valor
												);
		}
	} 
	*/
	

#	echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=eleicao_fundacao_solidaria_responde.php?agrup='.$v_proxima.'&proxima_ordem='.$proxima_ordem.'">';	

?>