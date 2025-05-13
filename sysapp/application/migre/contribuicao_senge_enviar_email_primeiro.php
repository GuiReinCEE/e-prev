<?
    include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/ePrev.DAL.DBConnection.php');
	include_once('inc/ePrev.Enums.php');

	$dal = new DBConnection();
	$dal->loadConnection($db);

	$cd_empresa = 7;
	$cd_plano = 7;
	$cd_sequencia = 0;
	$date = date("d/m/Y");
	$mm = $_POST['mes'];
	$aaaa = $_POST['ano'];
	$usuario = $_SESSION['U'];

	$dal->createQuery( "

		-- PRIMEIRO PAGAMENTO
		SELECT ii.cd_empresa,
		       ii.cd_registro_empregado,
		       ii.seq_dependencia,
		       ii.nome,
		       ii.email,
		       ii.cpf,
		       ii.endereco,
		       ii.bairro
		  FROM public.inscritos_internet ii,
		       public.controle_geracao_cobranca cgc,
		       public.taxas t,
		       public.pacotes p
		 WHERE ii.dt_envio_primeira_cobr   IS NULL
		   AND ii.dt_primeiro_pgto         IS NULL
		   AND ii.dt_geracao_primeira_cobr IS NOT NULL
		   AND ii.cd_pacote                = 1
		   AND ii.cd_plano                 = {cd_plano}
		   AND ii.cd_empresa               = {cd_empresa}
		   AND cgc.cd_plano                = ii.cd_plano
		   AND cgc.cd_empresa              = ii.cd_empresa
		   AND cgc.mes_competencia         = {mes_competencia}
		   AND cgc.ano_competencia         = {ano_competencia}
		   AND cgc.dt_geracao              IS NOT NULL
		   AND t.cd_indexador              = 42 
		   AND t.dt_taxa                   = DATE_TRUNC('month',CURRENT_DATE)
		   AND p.cd_pacote                 = ii.cd_pacote
		   AND p.cd_plano                  = ii.cd_plano
		   AND p.cd_empresa                = ii.cd_empresa
		   AND p.tipo_cobranca             = 'I'
		   AND p.dt_inicio                 = DATE_TRUNC('month',CURRENT_DATE)

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
		send_email_confirmando_envio($dal, $mm, $aaaa, 'PRIMEIRO PAGAMENTO');

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
				   	   , tot_internet_enviado = {tot_internet_enviado}
				   	   , vlr_internet_enviado = {vlr_internet_enviado}

				   	   , dt_envio_bdl = CURRENT_DATE
				   	   , usuario_envio_bdl = UPPER('{usuario_envio_bdl}')
				   	   , tot_bdl_enviado = {tot_bdl_enviado}
				   	   , vlr_bdl_enviado = {vlr_bdl_enviado}

				   	   , dt_envio_arrec = CURRENT_DATE
				   	   , usuario_envio_arrec = UPPER('{usuario_envio_arrec}')
				   	   , tot_arrec_enviado = {tot_arrec_enviado}
				   	   , vlr_arrec_enviado = {vlr_arrec_enviado}

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
		$dal->setAttribute( "{tot_internet_enviado}", $_POST['tot_internet_enviado'] );
		$dal->setAttribute( "{vlr_internet_enviado}", floatval( $_POST['vlr_internet_enviado'] ) );

		$dal->setAttribute( "{usuario_envio_bdl}", $_SESSION['U'] );
		$dal->setAttribute( "{tot_bdl_enviado}", $_POST['tot_bdl_enviado'] );
		$dal->setAttribute( "{vlr_bdl_enviado}", floatval( $_POST['vlr_bdl_enviado'] ) );

		$dal->setAttribute( "{usuario_envio_arrec}", $_SESSION['U'] );
		$dal->setAttribute( "{tot_arrec_enviado}", $_POST['tot_arrec_enviado'] );
		$dal->setAttribute( "{vlr_arrec_enviado}", floatval( $_POST['vlr_arrec_enviado'] ) );

		$dal->setAttribute( "{cd_plano}", $cd_plano );
		$dal->setAttribute( "{cd_empresa}", $cd_empresa );
		$dal->setAttribute( "{mes_competencia}", $mm );
		$dal->setAttribute( "{ano_competencia}", $aaaa );

		$sql = $dal->getSQL();

		return $sql;
	}

	function send_email($reg, $dal)
	{
		// ----------------- 

		$msg = "Prezada(o) " . $reg['nome'];
		$v_assunto = 'Contribuição do Plano SENGE Previdencia disponível para pagamento';
		$v_para = $reg['email'];
		$v_cc = '';
		$v_cco = '';
		$v_de = 'Senge Previdência';
		$vbcrlf = chr(10) . chr(13);

		$msg = $msg . $vbcrlf . $vbcrlf;
		$msg = $msg . "Sua contribuição do Plano SENGE Previdencia encontra-se disponível para pagamento" . $vbcrlf;
		$msg = $msg . "Identificação:" . $vbcrlf . $vbcrlf;

		// ------------------------- Área da mensagem texto:

		$msg = $msg . "-------------------------------------------------------------".$vbcrlf;
		$msg = $msg . "Nome: " . $reg['nome'] .$vbcrlf;
		$msg = $msg . "CPF: " . $reg['cpf'] .$vbcrlf;
		$msg = $msg . "Endereço: " . $reg['endereco'].$vbcrlf;
		$msg = $msg . "Bairro: " . $reg['bairro'].$vbcrlf;
		$msg = $msg . "-------------------------------------------------------------".$vbcrlf;

		$msg = $msg . "Por favor, pague a contribuição do plano, através do link abaixo ou através da área de auto-atendimento de nosso site:".$vbcrlf;

		$link="http://www.sengeprevidencia.com.br/escolha_valor.php?n=".$reg['cd_registro_empregado'];
		$link=gera_link($link, $reg['cd_empresa'] , $reg['cd_registro_empregado'] , $reg['seq_dependencia']);

		// $msg = $msg . "http://www.sengeprevidencia.com.br/escolha_valor.php?n=" . $reg['cd_registro_empregado'] . $vbcrlf;
		$msg = $msg . $link . $vbcrlf;
		$msg = $msg . "-------------------------------------------------------------".$vbcrlf;
		$msg = $msg . "Esta mensagem foi enviada pelo Sistema SENGE Previdência.";
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
		$dal->setAttribute('{cd_evento}', enum_projetos_eventos::SENGE_EMAIL_CONTRIBUICAO);

		$sql = $dal->getSql();

		return $sql;
	}

	function send_email_confirmando_envio($dal, $mes, $ano, $tipo)
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

		$texto = str_replace( '{MES}', $mes, $texto );
		$texto = str_replace( '{ANO}', $ano, $texto );

		$dal->setAttribute('{de}', 'SENGE Contribuição');
		$dal->setAttribute('{para}', $emails);
		$dal->setAttribute('{cc}', '');
		$dal->setAttribute('{cco}', '');
		$dal->setAttribute('{assunto}', 'SENGE Contribuição');
		$dal->setAttribute('{texto}', $texto);
		$dal->setAttribute('{cd_evento}', enum_projetos_eventos::SENGE_EMAIL_CONTRIBUICAO);

		$ret = $dal->executeQuery(true);
		
		return $ret;
	}
?>