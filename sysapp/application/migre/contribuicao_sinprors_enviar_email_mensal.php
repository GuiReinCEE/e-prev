<?
    include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/ePrev.DAL.DBConnection.php');
	include_once('inc/ePrev.Enums.php');
	include_once('inc/ePrev.Service.EmailListas.php');

	$dal = new DBConnection();
	$dal->loadConnection($db);

	$cd_empresa = enum_public_patrocinadoras::SINPRO;
	$mm = $_POST['mes'];
	$aaaa = $_POST['ano'];
	$usuario = $_SESSION['U'];

	$cd_plano = enum_public_planos::SINPRORS_PREVIDENCIA;

	$dal->createQuery( "

		SELECT 

			pa.*
			, funcoes.cripto_re(cc.cd_empresa, cc.cd_registro_empregado, cc.seq_dependencia) AS re
			, funcoes.cripto_mes_ano(nr_mes_competencia, nr_ano_competencia) AS competencia
			, cc.cd_contribuicao_controle_tipo
			, nr_mes_competencia
			, nr_ano_competencia

		FROM

			projetos.contribuicao_controle cc
			JOIN public.participantes pa
			ON pa.cd_empresa=cc.cd_empresa
			AND pa.cd_registro_empregado=cc.cd_registro_empregado
			AND pa.seq_dependencia=cc.seq_dependencia

		WHERE 

			cc.nr_mes_competencia = {nr_mes_competencia}
			AND cc.nr_ano_competencia = {nr_ano_competencia}
			AND cc.cd_empresa = {cd_empresa}
			AND cc.cd_contribuicao_controle_tipo IN ( 'PMBDL', 'PMDCC' );

	" );

	$dal->setAttribute( "{cd_empresa}", $cd_empresa );
	$dal->setAttribute( "{nr_mes_competencia}", $mm );
	$dal->setAttribute( "{nr_ano_competencia}", $aaaa );

	$rs = $dal->getResultset();

	$sql_inserts = "";
	while( $row = pg_fetch_array($rs) )
	{
		if( $row['cd_contribuicao_controle_tipo']==enum_projetos_contribuicao_controle_tipo::PAGAMENTO_MENSAL_BDL )
		{
			$sql_inserts .= send_email( $row, $dal );
		}
		else
		{
			$sql_inserts .= send_email_bco( $row, $dal );
		}
	}

	$dal->createQuery( $sql_inserts );
	$ret = $dal->executeQuery();

	if( $ret )
	{
		// Operação de envio de emails não retornou erro
		send_email_aviso($dal, $db, $mm, $aaaa);
		send_email_confirmando_envio($dal, $db, $mm, $aaaa,'PAGAMENTO MENSAL');
		echo 'true';
	}
	else
	{
		// Operação de envio de emails retornou erro
		echo 'false';
	}

	pg_close( $db );
	exit;
	
	function send_email_aviso($dal, $db, $mes, $ano)
	{
		$texto = "Aviso
		
A GF executou o envio e-mail do {NOME_DO_PROCESSO}

Por favor verifique os emails retornados: 
http://www.e-prev.com.br/controle_projetos/contribuicao_sinprors_relatorio.php?aba=retornados&mes={MES}&ano={ANO}";

		// Enviar email a grupo avisando da cobrança gerada
		$emailListas = new EmailListas($db);
		$emails = $emailListas->getEmailsToString( "sinprors_contribuicao_gerada" );

		$dal->createQuery( "
			INSERT INTO projetos.envia_emails (
				dt_envio
				, de
				, para
				, assunto
				, texto    
				, cd_evento
				, tp_email
			)     	
			VALUES     	
			(     		  
				CURRENT_TIMESTAMP     		
				, '{de}'     		
				, '{para}'     		
				, '{assunto}'     		
				, '{texto}'
				, {cd_evento}     	
				, 'A'
			);" 
		);

		$texto = str_replace( '{NOME_DO_PROCESSO}', 'Envio de cobrança mensal do SINPRORS', $texto );
		$texto = str_replace( '{MES}', $mes, $texto );
		$texto = str_replace( '{ANO}', $ano, $texto );
		
		$dal->setAttribute('{de}', 'SINPRORS Contribuição');
		$dal->setAttribute('{para}', $emails);
		$dal->setAttribute('{cc}', '');
		$dal->setAttribute('{cco}', '');
		$dal->setAttribute('{assunto}', 'SINPRORS Contribuição');
		$dal->setAttribute('{texto}', $texto);
		$dal->setAttribute('{cd_evento}', enum_projetos_eventos::SINPRORS_EMAIL_CONTRIBUICAO);
		
		$ret = $dal->executeQuery(true);
		
		return $ret;
	}

	function send_email($reg, $dal)
	{
		$msg = "Prezada(o) " . $reg['nome'];
		$v_assunto = 'Contribuição do Plano SINPRORS Previdencia disponível para pagamento';
		$v_para = ($reg['email']!="")?$reg['email']:$reg['email_profissional'];
		$v_cc = '';
		$v_cco = '';
		$v_de = 'SINPRORS Previdência';
		$vbcrlf = chr(10) . chr(13);

		$msg = $msg . $vbcrlf . $vbcrlf;
		$msg = $msg . "Sua contribuição do Plano SINPRORS Previdencia encontra-se disponível para pagamento" . $vbcrlf;
		$msg = $msg . "Identificação:" . $vbcrlf . $vbcrlf;

		// ------------------------- Área da mensagem texto:

		$msg = $msg . "-------------------------------------------------------------".$vbcrlf;
		$msg = $msg . "Empresa/RE.d/Sequência: " . $reg['cd_empresa'] . '/' . $reg['cd_registro_empregado'] . '/' . $reg['seq_dependencia'] .$vbcrlf;
		$msg = $msg . "Nome: " . $reg['nome'] . $vbcrlf;
		$msg = $msg . "CPF: " . $reg['cpf_mf'] . $vbcrlf;
		$msg = $msg . "Endereço: " . $reg['logradouro'] . $vbcrlf;
		$msg = $msg . "Bairro: " . $reg['bairro'] . $vbcrlf;
		$msg = $msg . "Cidade/UF: " . $reg['cidade'] . '/' . $reg['unidade_federativa'] . $vbcrlf;
		$msg = $msg . "CEP: " . $reg['cep'] . "-" . $reg['complemento_cep'] . $vbcrlf;
		$msg = $msg . "-------------------------------------------------------------".$vbcrlf;

		$msg = $msg . "Por favor, pague a contribuição do plano, através do link abaixo ou através da área de auto-atendimento de nosso site:".$vbcrlf;

		$re_md5 = $reg['re'];
		$comp_md5 = $reg['competencia'];

		$link=gera_link( "https://www.fundacaoceee.com.br/sinprors_pagamento.php?re=" . $re_md5 . "&comp=" . $comp_md5, $reg['cd_empresa'], $reg['cd_registro_empregado'], $reg['seq_dependencia']);

		$msg = $msg . $link . $vbcrlf;
		$msg = $msg . "-------------------------------------------------------------" . $vbcrlf;
		$msg = $msg . "**** ATENÇÃO ****
Este e-mail é somente para leitura.
Caso queira falar conosco clique no link abaixo:
https://www.fundacaoceee.com.br/fale_conosco.php";
		$date = date("d/m/Y");

		$dal->createQuery( "

			INSERT INTO projetos.envia_emails 
						(
					      dt_envio
					    , de
					    , para
					    , cc
					    , cco
					    , assunto
					    , cd_empresa
					    , cd_registro_empregado
					    , seq_dependencia
					    , texto
					    , cd_evento
					    , tp_email
					    )
			     VALUES 
			     		(
			     		  CURRENT_TIMESTAMP
			     		, '{de}'
			     		, '{para}'
			     		, '{cc}'
			     		, '{cco}'
			     		, '{assunto}'
			     		, {cd_empresa}
			     		, {cd_registro_empregado}
			     		, {seq_dependencia}
			     		, '{texto}'
			     		, {cd_evento}
			     		, 'A'
			     		);

					UPDATE 

						projetos.contribuicao_controle

					SET 

						fl_email_enviado = 'S'

					WHERE 

						 cd_empresa = {cd_empresa}
						 AND cd_registro_empregado = {cd_registro_empregado}
						 AND seq_dependencia = {seq_dependencia}
						 AND nr_ano_competencia = {nr_ano_competencia}
						 AND nr_mes_competencia = {nr_mes_competencia}
						 AND cd_contribuicao_controle_tipo = '{cd_contribuicao_controle_tipo}';

		" );
		$dal->setAttribute('{de}', $v_de);
		$dal->setAttribute('{para}', $v_para);
		$dal->setAttribute('{cc}', $v_cc);
		$dal->setAttribute('{cco}', $v_cco);
		$dal->setAttribute('{assunto}', $v_assunto);
		$dal->setAttribute('{cd_empresa}', $reg['cd_empresa']);
		$dal->setAttribute('{cd_registro_empregado}', $reg['cd_registro_empregado']);
		$dal->setAttribute('{seq_dependencia}', $reg['seq_dependencia']);
		$dal->setAttribute('{texto}', $msg);
		$dal->setAttribute('{cd_evento}', enum_projetos_eventos::SINPRORS_EMAIL_CONTRIBUICAO);

		$dal->setAttribute('{nr_ano_competencia}', $reg['nr_ano_competencia']);
		$dal->setAttribute('{nr_mes_competencia}', $reg['nr_mes_competencia']);
		$dal->setAttribute('{cd_contribuicao_controle_tipo}', $reg['cd_contribuicao_controle_tipo']);

		$sql = $dal->getSql();

		return $sql;
	}
	
	function send_email_bco($reg, $dal)
	{
		$template = 'Prezado(a): {NOME_PARTICIPANTE}

Informamos que no próximo dia 10, será debitado automaticamente em conta corrente sua contribuição do Plano SINPRORS Previdência conforme anteriormente autorizado. 
Confirme o lançamento futuro em sua conta, se por algum motivo de seu conhecimento não ocorra o débito automático, favor entrar em contato com a Fundação CEEE pelo nosso Tele Atendimento, através do telefone 0800 51 2596.

Identificação:
-------------------------------------------------------------
Empresa/RE.d/Sequência: {CD_EMPRESA} / {CD_REGISTRO_EMPREGADO} / {SEQ_DEPENDENCIA}
Nome: {NOME_PARTICIPANTE}
CPF: {CPF} 
Endereço: {ENDERECO}
Bairro: {BAIRRO}
Cidade/UF: {CIDADE} / {UF}
CEP: {CEP}-{COMPLEMENTO_CEP}
-------------------------------------------------------------

Esta mensagem foi enviada pelo Sistema SINPRORS Previdência.
';
		
		$de = "SINPRORS Previdência";
		$assunto = "Contribuição do Plano SINPRORS Previdência";
		$para = ($reg['email']!="")?$reg['email']:$reg['email_profissional'];

		$corpo = $template;
		$corpo = str_replace( "{NOME_PARTICIPANTE}", $reg['nome'], $corpo );
		$corpo = str_replace( "{CD_EMPRESA}", $reg['cd_empresa'], $corpo );
		$corpo = str_replace( "{CD_REGISTRO_EMPREGADO}", $reg['cd_registro_empregado'], $corpo );
		$corpo = str_replace( "{SEQ_DEPENDENCIA}", $reg['seq_dependencia'], $corpo );
		$corpo = str_replace( "{CPF}", $reg['cpf_mf'], $corpo );
		$corpo = str_replace( "{ENDERECO}", $reg['logradouro'], $corpo );
		$corpo = str_replace( "{BAIRRO}", $reg['bairro'], $corpo );
		$corpo = str_replace( "{CIDADE}", $reg['cidade'], $corpo );
		$corpo = str_replace( "{UF}", $reg['unidade_federativa'], $corpo );
		$corpo = str_replace( "{CEP}", $reg['cep'], $corpo );
		$corpo = str_replace( "{COMPLEMENTO_CEP}", $reg['complemento_cep'], $corpo );

		$dal->createQuery( "

			INSERT INTO projetos.envia_emails 
						(
					      dt_envio
					    , de
					    , para
					    , cc
					    , cco
					    , assunto
					    , cd_empresa
					    , cd_registro_empregado
					    , seq_dependencia
					    , texto
					    , cd_evento
					    , tp_email
					    )
			     VALUES 
			     		(
			     		  CURRENT_TIMESTAMP
			     		, '{de}'
			     		, '{para}'
			     		, '{cc}'
			     		, '{cco}'
			     		, '{assunto}'
			     		, {cd_empresa}
			     		, {cd_registro_empregado}
			     		, {seq_dependencia}
			     		, '{texto}'
			     		, {cd_evento}
			     		, 'A'
			     		);

     			UPDATE 
				
					projetos.contribuicao_controle
					
				SET 
								   
					fl_email_enviado = 'S'
				
				WHERE 
			 
					 cd_empresa = {cd_empresa}
					 AND cd_registro_empregado = {cd_registro_empregado}
					 AND seq_dependencia = {seq_dependencia}
					 AND nr_ano_competencia = {nr_ano_competencia}
					 AND nr_mes_competencia = {nr_mes_competencia}
					 AND cd_contribuicao_controle_tipo = '{cd_contribuicao_controle_tipo}';

		" );
		$dal->setAttribute('{de}', $de);
		$dal->setAttribute('{para}', $para);
		$dal->setAttribute('{cc}', '');
		$dal->setAttribute('{cco}', '');
		$dal->setAttribute('{assunto}', $assunto);
		$dal->setAttribute('{cd_empresa}', $reg['cd_empresa']);
		$dal->setAttribute('{cd_registro_empregado}', $reg['cd_registro_empregado']);
		$dal->setAttribute('{seq_dependencia}', $reg['seq_dependencia']);
		$dal->setAttribute('{texto}', $corpo);
		$dal->setAttribute('{cd_evento}', enum_projetos_eventos::SINPRORS_EMAIL_CONTRIBUICAO);
		
		$dal->setAttribute('{nr_ano_competencia}', $reg['nr_ano_competencia']);
		$dal->setAttribute('{nr_mes_competencia}', $reg['nr_mes_competencia']);
		$dal->setAttribute('{cd_contribuicao_controle_tipo}', $reg['cd_contribuicao_controle_tipo']);

		$sql = $dal->getSql();

		return $sql;
	}
	
	
	function send_email_confirmando_envio($dal, $db, $mes, $ano, $tipo)
	{
		$texto = $tipo . " - Contribuição de {MES}/{ANO} enviada.";

		$emails = 'jmarques@eletroceee.com.br';

		$dal->createQuery( "
			INSERT INTO projetos.envia_emails (
				dt_envio		    
				, de		    
				, para		    
				, assunto		    
				, texto		    
				, cd_evento		
				, tp_email		
			)     	
			VALUES     	
			(     		  
				CURRENT_TIMESTAMP     		
				, '{de}'     		
				, '{para}'     		
				, '{assunto}'     		
				, '{texto}'     		
				, {cd_evento}     	
				, 'A'     		
			);" 
		);

		$texto = str_replace( '{NOME_DO_PROCESSO}', 'Envio de cobrança de primeiro pagamento do SINPRORS', $texto );
		$texto = str_replace( '{MES}', $mes, $texto );
		$texto = str_replace( '{ANO}', $ano, $texto );
		
		$dal->setAttribute('{de}', 'SINPRORS Contribuição');
		$dal->setAttribute('{para}', $emails);
		$dal->setAttribute('{cc}', '');
		$dal->setAttribute('{cco}', '');
		$dal->setAttribute('{assunto}', 'SINPRORS Contribuição');
		$dal->setAttribute('{texto}', $texto);
		$dal->setAttribute('{cd_evento}', enum_projetos_eventos::SINPRORS_EMAIL_CONTRIBUICAO);
		
		$ret = $dal->executeQuery(true);
		
		return $ret;
	}
?>