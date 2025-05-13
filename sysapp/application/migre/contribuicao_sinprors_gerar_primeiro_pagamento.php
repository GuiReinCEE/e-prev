<?





















/*
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/ePrev.DAL.DBConnection.php');
	include_once('inc/ePrev.Enums.php');

	$dal = new DBConnection();
	$dal->loadConnection($db);

	$cd_empresa = enum_public_patrocinadoras::SINPRO;
	$cd_plano = enum_public_planos::SINPRORS_PREVIDENCIA;
	$cd_sequencia = 0;
	$date = date("d/m/Y");
	$mm = $_POST['mes'];
	$aaaa = $_POST['ano'];
	$usuario = $_SESSION['U'];

	$dal->createQuery( "

		SELECT part.cd_empresa,
		       part.cd_registro_empregado,
		       part.seq_dependencia,
		       part.nome,
		       COALESCE(part.email, part.email_profissional) as email,
		       part.cpf_mf as cpf,
		       part.logradouro as endereco,
		       part.bairro,
		       part.cidade,
		       part.unidade_federativa,
		       part.cep || '-' || trim(to_char(part.complemento_cep, '000')) as cep, 
		       funcoes.cripto_re(part.cd_empresa, part.cd_registro_empregado, part.seq_dependencia) AS re,
		       funcoes.cripto_mes_ano(0::numeric, 0::numeric) AS competencia
		  FROM public.protocolos_participantes p
		  JOIN public.participantes part
		    ON part.cd_empresa = p.cd_empresa AND part.cd_registro_empregado = p.cd_registro_empregado AND part.seq_dependencia = p.seq_dependencia
		  JOIN public.calendarios_planos cp
		    ON cp.cd_empresa = p.cd_empresa
		  JOIN public.controle_geracao_cobranca cgc
		    ON cgc.cd_empresa = cp.cd_empresa
		   AND cgc.cd_plano   = cp.cd_plano
		  LEFT JOIN public.contribuicoes_programadas cpr
		    ON cpr.cd_empresa            = p.cd_empresa
		   AND cpr.cd_registro_empregado = p.cd_registro_empregado
		   AND cpr.seq_dependencia       = p.seq_dependencia
		   AND cpr.dt_confirma_opcao     IS NOT NULL
		   AND cpr.dt_confirma_canc      IS NULL
     LEFT JOIN titulares_planos tp
            ON tp.cd_empresa = p.cd_empresa
           AND tp.cd_registro_empregado = p.cd_registro_empregado
           AND tp.seq_dependencia = p.seq_dependencia
           AND tp.dt_ingresso_plano = ( SELECT max(tp1.dt_ingresso_plano) as max 
										  FROM titulares_planos tp1 
										 WHERE tp1.cd_empresa=p.cd_empresa 
										   AND tp1.cd_registro_empregado=p.cd_registro_empregado 
										   AND tp1.seq_dependencia=p.seq_dependencia )
		   WHERE cp.cd_empresa     = {cd_empresa}
		   AND cp.cd_plano         = {cd_plano}
		   AND cp.dt_competencia   = '{ano_competencia}-{mes_competencia}-01'
		   AND cgc.mes_competencia = {mes_competencia}
		   AND cgc.ano_competencia = {ano_competencia}
		   AND cgc.dt_geracao      IS NOT NULL
		   AND p.dt_confirma       BETWEEN cp.dt_inicio AND cp.dt_fim
		   AND p.forma_pagamento   = 'BDL'
		   AND COALESCE(email, email_profissional) IS NOT NULL
		   AND 0 = CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL THEN 0 ELSE 1 END

	" );

	$dal->setAttribute( "{cd_plano}", $cd_plano );
	$dal->setAttribute( "{cd_empresa}", $cd_empresa );
	$dal->setAttribute( "{mes_competencia}", $mm );
	$dal->setAttribute( "{ano_competencia}", $aaaa );

	$rs = $dal->getResultset();

	$sql_inserts = "";
	while( $row = pg_fetch_array($rs) )
	{
		$sql_inserts .= send_email( $row, $dal );
	}

	// Update na tabela de controle para enviar informações ao oracle
	$sql_controle = gera_comandos_controle($dal, $cd_plano, $cd_empresa, $mm, $aaaa);
	$sql_inserts .= $sql_controle;

	$dal->createQuery( $sql_inserts );
	$ret = $dal->executeQuery();

	if( $ret )
	{
		// Operação de envio de emails não retornou erro
		echo 'true';
	}
	else
	{
		// Operação de envio de emails retornou erro
		echo 'false';
		// echo '<pre>' . $dal->getMessage() . '</pre>';
	}

	pg_close( $db );
	exit;

	function gera_comandos_controle($dal, $cd_plano, $cd_empresa, $mm, $aaaa)
	{
		$dal->createQuery( "

	     		UPDATE public.controle_geracao_cobranca
				   SET 
				         dt_envio_internet = CURRENT_DATE
				   	   , usuario_envio_internet = UPPER('{usuario_envio_internet}')
				   	   , tot_internet_enviado = tot_internet_gerado
				   	   , vlr_internet_enviado = vlr_internet_gerado

				   	   , dt_envio_bdl = CURRENT_DATE
				   	   , usuario_envio_bdl = UPPER('{usuario_envio_bdl}')
				   	   , tot_bdl_enviado = {tot_bdl_enviado}
				   	   , vlr_bdl_enviado = {vlr_bdl_enviado}

				   	   , dt_envio_arrec = CURRENT_DATE
				   	   , usuario_envio_arrec = UPPER('{usuario_envio_arrec}')
				   	   , tot_arrec_enviado = tot_arrec_gerado
				   	   , vlr_arrec_enviado = vlr_arrec_gerado

                 WHERE cd_plano = {cd_plano} 
				   AND cd_empresa  = {cd_empresa}
				   AND mes_competencia = {mes_competencia}
				   AND ano_competencia = {ano_competencia};

				INSERT INTO public.controle_geracao_cobranca_hist
				SELECT *
				  FROM public.controle_geracao_cobranca

				 WHERE cd_plano = {cd_plano} 
				   AND cd_empresa = {cd_empresa}
				   AND mes_competencia = {mes_competencia}
				   AND ano_competencia = {ano_competencia};

		" );

		$dal->setAttribute( "{usuario_envio_internet}", $_SESSION['U'] );
		$dal->setAttribute( "{usuario_envio_arrec}", $_SESSION['U'] );

		$dal->setAttribute( "{usuario_envio_bdl}", $_SESSION['U'] );
		$dal->setAttribute( "{tot_bdl_enviado}", $_POST['tot_bdl_enviado'] );
		$dal->setAttribute( "{vlr_bdl_enviado}", floatval( $_POST['vlr_bdl_enviado'] ) );

		$dal->setAttribute( "{cd_plano}", $cd_plano );
		$dal->setAttribute( "{cd_empresa}", $cd_empresa );
		$dal->setAttribute( "{mes_competencia}", $mm );
		$dal->setAttribute( "{ano_competencia}", $aaaa );

		$sql = $dal->getSQL();

		return $sql;
	}

	function send_email($reg, $dal)
	{
		$msg = "Prezada(o) " . $reg['nome'];
		$v_assunto = 'Contribuição do Plano SINPRO Previdencia disponível para pagamento';
		$v_para = $reg['email'];
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
		$msg = $msg . "CPF: " . $reg['cpf'] . $vbcrlf;
		$msg = $msg . "Endereço: " . $reg['endereco'] . $vbcrlf;
		$msg = $msg . "Bairro: " . $reg['bairro'] . $vbcrlf;
		$msg = $msg . "Cidade/UF: " . $reg['cidade'] . '/' . $reg['unidade_federativa'] . $vbcrlf;
		$msg = $msg . "CEP: " . $reg['cep'] . $vbcrlf;
		$msg = $msg . "-------------------------------------------------------------".$vbcrlf;

		$msg = $msg . "Por favor, pague a contribuição do plano, através do link abaixo ou através da área de auto-atendimento de nosso site:".$vbcrlf;

		$re_md5 = $reg['re'];
		$comp_md5 = $reg['competencia'];
		$msg = $msg . "https://www.e-prev.com.br/controle_projetos/sinprors_pagamento.php?re=" . $re_md5 . "&comp=" . $comp_md5 . $vbcrlf;
		$msg = $msg . "-------------------------------------------------------------".$vbcrlf;
		$msg = $msg . "Esta mensagem foi enviada pelo Sistema SINPRORS Previdência.";
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

		$sql = $dal->getSql();

		return $sql;
	}

*/
?>