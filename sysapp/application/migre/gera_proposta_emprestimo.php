<?php
	// Verificar se o usuário corrente tem acesso a esta página

	/*
	 * Esta página não deve estar disponível para o participante, apenas para o atendente. 
	 * Ela é responsável pela impressão de todos os documentos referentes ao empréstimo.
	 * Necessida do script "gera_documentos_emprestimp.php", que é responsável pela 
	 *    renderização dos documentos.
	 */
	 
   /*
    * Parâmetros recebidos (pelo método GET):
    * ---------------------------------------
    * pro (1..4): Páginas da proposta que serão impressas. Se nenhuma for informada, não imprime proposta
    * dem  (S/N): Imprimir Demonstrativo
    * np   (S/N): Imprimir Nota Promissória
    * aut  (S/N): Imprimir Autorização Banrisul
    * d    (N/S): Destino. N = Normal; S = Sedex
    * call (S/N): Se é para Call Center (Controla a impressão de uma área do Demonstrativo)
    * pgproposta: 1234 - Páginas que devem ser impressas (a ordem não altera o resultado)
    * e         : cd_empresa
    * r         : cd_registro_empregado
    * s         : seq_dependencia
    * c         : cd_contrato
    * t         : Tipo de impressão da proposta 
    *             C = Proposta completa, com 4 páginas; 
    *             P = Proposta parcial, apenas a primeira e última página
    *             V = Somente valores
    *             D = Dados Cadastrais
    *             L = Proposta em Lote. Somente primeira e última página
    **/

	include_once('inc/class.SocketAbstraction2.inc.php');
	include_once('inc/class.TemplatePower.inc.php');
	// include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	require_once('inc/config.inc.php');

	$LISTNER_IP    = SKT_IP;
	$LISTNER_PORTA = SKT_PORTA;

	$imp_proposta      = (isset($_REQUEST['pro'])  ? $_REQUEST['pro']  : '');
	$imp_demonstrativo = (isset($_REQUEST['dem'])  ? $_REQUEST['dem']  : 'N');
	$imp_np            = (isset($_REQUEST['np'])   ? $_REQUEST['np']   : 'N');
	$imp_autorizacao   = (isset($_REQUEST['aut'])  ? $_REQUEST['aut']  : 'N');
	$destino           = (isset($_REQUEST['d'])    ? $_REQUEST['d']    : 'N'); // N = Normal; S=Sedex
	$callcenter        = (isset($_REQUEST['call']) ? $_REQUEST['call'] : 'N'); // Callcenter: S=Sim; N=Não
   
	$e = $_REQUEST['e'];
	$r = $_REQUEST['r'];
	$s = $_REQUEST['s'];
	
	$cd_contrato = $_REQUEST['c'];

	$tipo = (isset($_REQUEST['t']) ? $_REQUEST['t'] : 'C'); // C = Completa ; P = Parcial
	$assinaturas = (isset($_REQUEST['a']) ? $_REQUEST['a'] : ($tipo == 'N' ? 'N' : 'S')); // Define se a assinatura será impressa ou não
   
	if ($imp_proposta == 'S') { // Por questões de compatibilidade
		if ($tipo == 'C') 
		{
			$imp_proposta = '1234';
		} 
		else 
		{
			$imp_proposta = '14';
		}
	}
    
   require_once('gera_documentos_emprestimo.php');
?>
