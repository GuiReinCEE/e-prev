<?php
	// Verificar se o usu�rio corrente tem acesso a esta p�gina

	/*
	 * Esta p�gina n�o deve estar dispon�vel para o participante, apenas para o atendente. 
	 * Ela � respons�vel pela impress�o de todos os documentos referentes ao empr�stimo.
	 * Necessida do script "gera_documentos_emprestimp.php", que � respons�vel pela 
	 *    renderiza��o dos documentos.
	 */
	 
   /*
    * Par�metros recebidos (pelo m�todo GET):
    * ---------------------------------------
    * pro (1..4): P�ginas da proposta que ser�o impressas. Se nenhuma for informada, n�o imprime proposta
    * dem  (S/N): Imprimir Demonstrativo
    * np   (S/N): Imprimir Nota Promiss�ria
    * aut  (S/N): Imprimir Autoriza��o Banrisul
    * d    (N/S): Destino. N = Normal; S = Sedex
    * call (S/N): Se � para Call Center (Controla a impress�o de uma �rea do Demonstrativo)
    * pgproposta: 1234 - P�ginas que devem ser impressas (a ordem n�o altera o resultado)
    * e         : cd_empresa
    * r         : cd_registro_empregado
    * s         : seq_dependencia
    * c         : cd_contrato
    * t         : Tipo de impress�o da proposta 
    *             C = Proposta completa, com 4 p�ginas; 
    *             P = Proposta parcial, apenas a primeira e �ltima p�gina
    *             V = Somente valores
    *             D = Dados Cadastrais
    *             L = Proposta em Lote. Somente primeira e �ltima p�gina
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
	$callcenter        = (isset($_REQUEST['call']) ? $_REQUEST['call'] : 'N'); // Callcenter: S=Sim; N=N�o
   
	$e = $_REQUEST['e'];
	$r = $_REQUEST['r'];
	$s = $_REQUEST['s'];
	
	$cd_contrato = $_REQUEST['c'];

	$tipo = (isset($_REQUEST['t']) ? $_REQUEST['t'] : 'C'); // C = Completa ; P = Parcial
	$assinaturas = (isset($_REQUEST['a']) ? $_REQUEST['a'] : ($tipo == 'N' ? 'N' : 'S')); // Define se a assinatura ser� impressa ou n�o
   
	if ($imp_proposta == 'S') { // Por quest�es de compatibilidade
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
