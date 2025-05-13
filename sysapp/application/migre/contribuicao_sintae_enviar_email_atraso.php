<?
    include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/ePrev.DAL.DBConnection.php');
	include_once('inc/ePrev.Enums.php');
	include_once('inc/ePrev.Service.EmailListas.php');

	$dal = new DBConnection();
	$dal->loadConnection($db);

	$cd_empresa = enum_public_patrocinadoras::SINTAE;
	$mm = $_POST['mes'];
	$aaaa = $_POST['ano'];
	$usuario = $_SESSION['U'];

	$cd_plano = enum_public_planos::SINPRORS_PREVIDENCIA;

	$dal->createQuery( "

		SELECT 

			pa.*
			, funcoes.cripto_re(cc.cd_empresa, cc.cd_registro_empregado, cc.seq_dependencia) AS re
			, CASE WHEN (cc.cd_contribuicao_controle_tipo = '{tipo_1p}') THEN funcoes.cripto_mes_ano(0, 0)
			       WHEN (cc.cd_contribuicao_controle_tipo IN ( '{tipo_folha}', '{tipo_bco}')) THEN funcoes.cripto_mes_ano(nr_mes_competencia, nr_ano_competencia)
			  END AS competencia

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
			AND cc.cd_contribuicao_controle_tipo IN ( '{tipo_folha}', '{tipo_bco}', '{tipo_1p}' );

	" );

	$dal->setAttribute( "{cd_empresa}", $cd_empresa );
	$dal->setAttribute( "{nr_mes_competencia}", $mm );
	$dal->setAttribute( "{nr_ano_competencia}", $aaaa );
	$dal->setAttribute( "{tipo_folha}", enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DESCONTO_FOLHA );
	$dal->setAttribute( "{tipo_bco}", enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DEBITO_CONTA_CORRENTE );
	$dal->setAttribute( "{tipo_1p}", enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_PRIMEIRO_PAGAMENTO );

	$rs = $dal->getResultset();

	$sql_inserts = "";
	while( $row = pg_fetch_array($rs) )
	{
		if( $row['cd_contribuicao_controle_tipo']==enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DEBITO_CONTA_CORRENTE )
		{
			$sql_inserts .= send_mail_bco( $row, $dal );
		}
		elseif( $row['cd_contribuicao_controle_tipo']==enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DESCONTO_FOLHA )
		{
			$sql_inserts .= send_mail_folha( $row, $dal );
		}
		elseif( $row['cd_contribuicao_controle_tipo']==enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_PRIMEIRO_PAGAMENTO )
		{
			$sql_inserts .= send_mail_primeiro( $row, $dal );
		}
	}

	$dal->createQuery( $sql_inserts );
	$ret = $dal->executeQuery();

	if( $ret )
	{
		// Opera��o de envio de emails n�o retornou erro
		send_email_aviso($dal, $db, $mm, $aaaa);
		send_email_confirmando_envio($dal, $db, $mm, $aaaa,'COBRAN�A ATRASO');
		echo 'true';
	}
	else
	{
		// Opera��o de envio de emails retornou erro
		echo 'false';
		// echo '<pre>' . $dal->getMessage() . '</pre>';
	}

	pg_close( $db );
	exit;
	
	function send_email_aviso($dal, $db, $mes, $ano)
	{
		$texto = "Aviso

A GF executou o envio e-mail do {NOME_DO_PROCESSO}

Por favor verifique os emails retornados: 
http://www.e-prev.com.br/controle_projetos/contribuicao_sintae_relatorio.php?aba=retornados&mes={MES}&ano={ANO}";

		// Enviar email a grupo avisando da cobran�a gerada
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

		$texto = str_replace( '{NOME_DO_PROCESSO}', 'Envio de cobran�a de contribui��es atrasadas do SINTAE', $texto );
		$texto = str_replace( '{MES}', $mes, $texto );
		$texto = str_replace( '{ANO}', $ano, $texto );

		$dal->setAttribute('{de}', 'SINTAE Contribui��o');
		$dal->setAttribute('{para}', $emails);
		$dal->setAttribute('{cc}', '');
		$dal->setAttribute('{cco}', '');
		$dal->setAttribute('{assunto}', 'SINTAE Contribui��o');
		$dal->setAttribute('{texto}', $texto);
		$dal->setAttribute('{cd_evento}', enum_projetos_eventos::SINTAE_EMAIL_CONTRIBUICAO);

		$ret = $dal->executeQuery(true);

		return $ret;
	}

	function send_mail_bco($reg, $dal)
	{
		$template = 'Prezado(a): {NOME_PARTICIPANTE}

Em decorr�ncia de n�o ter sido poss�vel o desconto autom�tico em conta corrente, sua contribui��o do Plano SINPRORS Previd�ncia encontra-se dispon�vel para pagamento conforme instru��es abaixo (continuaremos encaminhando as pr�ximas contribui��es para desconto em conta corrente).

Identifica��o:
-------------------------------------------------------------
Empresa/RE.d/Sequ�ncia: {CD_EMPRESA} / {CD_REGISTRO_EMPREGADO} / {SEQ_DEPENDENCIA}
Nome: {NOME_PARTICIPANTE}
CPF: {CPF} 
Endere�o: {ENDERECO}
Bairro: {BAIRRO}
Cidade/UF: {CIDADE} / {UF}
CEP: {CEP}-{COMPLEMENTO_CEP}
-------------------------------------------------------------

Por favor, pague a contribui��o do plano, atrav�s do link abaixo ou atrav�s da �rea de auto-atendimento de nosso site:
{LINK}

-------------------------------------------------------------
**** ATEN��O ****
Este e-mail � somente para leitura.
Caso queira falar conosco clique no link abaixo:
https://www.fundacaoceee.com.br/fale_conosco.php
';

		$de = "SINPRORS Previd�ncia";
		$assunto = "Contribui��o do Plano SINPRORS Previd�ncia dispon�vel para pagamento";
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
		
		$link="https://www.fundacaoceee.com.br/sintae_pagamento.php?re=".$reg['re']."&comp=".$reg['competencia']."";
		$link=gera_link($link, $reg['cd_empresa'] , $reg['cd_registro_empregado'] , $reg['seq_dependencia']);
		$corpo = str_replace( "{LINK}", $link, $corpo );

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
					    , tpl_email
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
		$dal->setAttribute('{cd_evento}', enum_projetos_eventos::SINTAE_EMAIL_CONTRIBUICAO);

		$dal->setAttribute('{nr_ano_competencia}', $reg['nr_ano_competencia']);
		$dal->setAttribute('{nr_mes_competencia}', $reg['nr_mes_competencia']);
		$dal->setAttribute('{cd_contribuicao_controle_tipo}', $reg['cd_contribuicao_controle_tipo']);

		$sql = $dal->getSql();

		return $sql;
	}

	function send_mail_folha($reg, $dal)
	{
		$template = 'Prezado(a): {NOME_PARTICIPANTE}

Em decorr�ncia de n�o ter sido poss�vel o desconto em folha de pagamento, sua contribui��o do Plano SINPRORS Previd�ncia encontra-se dispon�vel para pagamento conforme instru��es abaixo (continuaremos encaminhando as pr�ximas contribui��es para a folha de pagamento).

Identifica��o:
-------------------------------------------------------------
Empresa/RE.d/Sequ�ncia: {CD_EMPRESA} / {CD_REGISTRO_EMPREGADO} / {SEQ_DEPENDENCIA}
Nome: {NOME_PARTICIPANTE}
CPF: {CPF} 
Endere�o: {ENDERECO}
Bairro: {BAIRRO}
Cidade/UF: {CIDADE} / {UF}
CEP: {CEP}-{COMPLEMENTO_CEP}
-------------------------------------------------------------

Por favor, pague a contribui��o do plano, atrav�s do link abaixo ou atrav�s da �rea de auto-atendimento de nosso site:
{LINK}

-------------------------------------------------------------
**** ATEN��O ****
Este e-mail � somente para leitura.
Caso queira falar conosco clique no link abaixo:
https://www.fundacaoceee.com.br/fale_conosco.php
';

		$de = "SINPRORS Previd�ncia";
		$assunto = "Contribui��o do Plano SINPRORS Previd�ncia dispon�vel para pagamento";
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
		
		$link="https://www.fundacaoceee.com.br/sintae_pagamento.php?re=".$reg['re']."&comp=".$reg['competencia']."";
		$link=gera_link($link, $reg['cd_empresa'] , $reg['cd_registro_empregado'] , $reg['seq_dependencia']);
		$corpo = str_replace( "{LINK}", $link, $corpo );

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
		$dal->setAttribute('{cd_evento}', enum_projetos_eventos::SINTAE_EMAIL_CONTRIBUICAO);

		$dal->setAttribute('{nr_ano_competencia}', $reg['nr_ano_competencia']);
		$dal->setAttribute('{nr_mes_competencia}', $reg['nr_mes_competencia']);
		$dal->setAttribute('{cd_contribuicao_controle_tipo}', $reg['cd_contribuicao_controle_tipo']);

		$sql = $dal->getSql();

		return $sql;
	}
	
	function send_mail_primeiro($reg, $dal)
	{
		$v_assunto = 'Contribui��o do Plano SINPRORS Previdencia dispon�vel para pagamento';
		$v_para = ($reg['email']!="")?$reg['email']:$reg['email_profissional'];
		$v_cc = '';
		$v_cco = '';
		$v_de = 'SINPRORS Previd�ncia';
		$vbcrlf = chr(10) . chr(13);

		$msg = "Prezada(o) " . $reg['nome'];
		$msg = $msg . $vbcrlf . $vbcrlf;
		$msg = $msg . "Informamos que n�o identificamos o recebimento da primeira contribui��o ao Plano SINPRORS Previd�ncia gerada para pagamento em per�odos anteriores. Sua contribui��o do Plano SINPRORS Previd�ncia encontra-se dispon�vel para pagamento conforme instru��es abaixo (lembramos que a inclus�o como participante do Plano est� condicionada a efetiva��o do primeiro pagamento) :" . $vbcrlf;
		$msg = $msg . "Identifica��o:" . $vbcrlf . $vbcrlf;

		// ------------------------- �rea da mensagem texto:

		$msg = $msg . "-------------------------------------------------------------".$vbcrlf;
		$msg = $msg . "Empresa/RE.d/Sequ�ncia: " . $reg['cd_empresa'] . '/' . $reg['cd_registro_empregado'] . '/' . $reg['seq_dependencia'] .$vbcrlf;
		$msg = $msg . "Nome: " . $reg['nome'] . $vbcrlf;
		$msg = $msg . "CPF: " . $reg['cpf_mf'] . $vbcrlf;
		$msg = $msg . "Endere�o: " . $reg['logradouro'] . $vbcrlf;
		$msg = $msg . "Bairro: " . $reg['bairro'] . $vbcrlf;
		$msg = $msg . "Cidade/UF: " . $reg['cidade'] . '/' . $reg['unidade_federativa'] . $vbcrlf;
		$msg = $msg . "CEP: " . $reg['cep'] . "-" . $reg['complemento_cep'] . $vbcrlf;
		$msg = $msg . "-------------------------------------------------------------".$vbcrlf;

		$msg = $msg . "Por favor, pague a contribui��o do plano, atrav�s do link abaixo ou atrav�s da �rea de auto-atendimento de nosso site:".$vbcrlf;

		$re_md5 = $reg['re'];
		$comp_md5 = $reg['competencia'];

		$link=gera_link("https://www.fundacaoceee.com.br/sintae_pagamento.php?re=" . $re_md5 . "&comp=" . $comp_md5, $reg['cd_empresa'] , $reg['cd_registro_empregado'] , $reg['seq_dependencia']);

		$msg = $msg . $link . $vbcrlf;
		$msg = $msg . "-------------------------------------------------------------".$vbcrlf;
		$msg = $msg . "**** ATEN��O ****
Este e-mail � somente para leitura.
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

	     		UPDATE public.inscritos_internet
				   SET dt_email_contribuicao  = CURRENT_DATE, 
				       dt_envio_primeira_cobr = CURRENT_DATE
				 WHERE cd_empresa             = {cd_empresa} 
				   AND cd_registro_empregado  = {cd_registro_empregado}
				   AND seq_dependencia        = {seq_dependencia};   

				INSERT INTO public.inscritos_internet_hist
				SELECT *
				  FROM public.inscritos_internet
				 WHERE cd_empresa            = {cd_empresa} 
				   AND cd_registro_empregado = {cd_registro_empregado}
				   AND seq_dependencia       = {seq_dependencia};
				   
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
		$dal->setAttribute('{cd_evento}', enum_projetos_eventos::SINTAE_EMAIL_CONTRIBUICAO);
		
		$dal->setAttribute('{nr_ano_competencia}', $reg['nr_ano_competencia']);
		$dal->setAttribute('{nr_mes_competencia}', $reg['nr_mes_competencia']);
		$dal->setAttribute('{cd_contribuicao_controle_tipo}', $reg['cd_contribuicao_controle_tipo']);

		$sql = $dal->getSql();

		return $sql;
	}
	
	function send_email_confirmando_envio($dal, $db, $mes, $ano, $tipo)
	{
		$texto = $tipo . " - Contribui��o de {MES}/{ANO} enviada.";

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

		$texto = str_replace( '{MES}', $mes, $texto );
		$texto = str_replace( '{ANO}', $ano, $texto );
		
		$dal->setAttribute('{de}', 'SINTAE Contribui��o');
		$dal->setAttribute('{para}', $emails);
		$dal->setAttribute('{cc}', '');
		$dal->setAttribute('{cco}', '');
		$dal->setAttribute('{assunto}', 'SINTAE Contribui��o');
		$dal->setAttribute('{texto}', $texto);
		$dal->setAttribute('{cd_evento}', enum_projetos_eventos::SINTAE_EMAIL_CONTRIBUICAO);
		
		$ret = $dal->executeQuery(true);
		
		return $ret;
	}
?>