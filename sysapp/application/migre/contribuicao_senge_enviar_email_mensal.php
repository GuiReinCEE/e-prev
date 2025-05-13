<?php
    include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/ePrev.DAL.DBConnection.php');
	include_once('inc/ePrev.Enums.php');

	include 'oo/start.php';
	using( array('projetos.contribuicao_controle') );

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

		SELECT DISTINCT ii.cd_empresa,
		       ii.cd_registro_empregado,
		       ii.seq_dependencia,
		       COALESCE(b.nome, ii.nome) AS nome,
		       COALESCE(p.email, ii.email) AS email,
		       COALESCE(p.cpf_mf, ii.cpf) AS cpf,
		       COALESCE(p.logradouro, ii.endereco) AS endereco,
		       COALESCE(p.bairro, ii.bairro) AS bairro
		  FROM public.bloqueto b
		  JOIN public.inscritos_internet ii
		    ON b.cd_empresa            = ii.cd_empresa 
		   AND b.cd_registro_empregado = ii.cd_registro_empregado
		   AND b.seq_dependencia       = ii.seq_dependencia
		  LEFT JOIN participantes p 
		    ON ii.cd_empresa            = p.cd_empresa 
		   AND ii.cd_registro_empregado = p.cd_registro_empregado 
		   AND ii.seq_dependencia       = p.seq_dependencia
		 WHERE b.mes_competencia           = {mes_competencia}
		   AND b.ano_competencia           = {ano_competencia}
		   AND b.data_retorno              IS NULL
		   AND ii.cd_plano                 = {cd_plano}
		   AND ii.cd_empresa               = {cd_empresa}
		   AND ii.cd_pacote                = 1
		   AND ii.dt_geracao_primeira_cobr IS NOT NULL   
		   AND ii.dt_primeiro_pgto         IS NOT NULL

	" );

	$dal->setAttribute( "{cd_plano}", $cd_plano );
	$dal->setAttribute( "{cd_empresa}", $cd_empresa );
	$dal->setAttribute( "{mes_competencia}", $mm );
	$dal->setAttribute( "{ano_competencia}", $aaaa );

	$rs = $dal->getResultset();
	
	$rows=array();
	
	while($row = pg_fetch_array($rs))
	{
		$rows[] = $row;
	}
	
	$sql_inserts = "";
	foreach($rows as $row)
	{
		$sql_inserts .= send_email( $row, $dal );
	}
	//while( $row = pg_fetch_array($rs) )	{		$sql_inserts .= send_email( $row, $dal );	}

	$dal->createQuery( $sql_inserts );
	$ret = $dal->executeQuery();

	if( $ret )
	{
		// Operaзгo de envio de emails nгo retornou erro
		send_email_confirmando_envio($dal, $mm, $aaaa, 'COBRANЗA MENSAL');

		foreach($rows as $row)
		{
			// controle
			$args['cd_empresa']=intval($row['cd_empresa']);
			$args['cd_registro_empregado']=intval($row['cd_registro_empregado']);
			$args['seq_dependencia']=intval($row['seq_dependencia']);
			$args['nr_ano_competencia']=$aaaa;
			$args['nr_mes_competencia']=$mm;
			$args['cd_contribuicao_controle_tipo']=enum_projetos_contribuicao_controle_tipo::PAGAMENTO_MENSAL_BDL;
			$args['cd_usuario']=intval(usuario_id());
			$args['fl_email_enviado']='S';

			contribuicao_controle::inserir($args);
		}

		echo 'true';
	}
	else
	{
		// Operaзгo de envio de emails retornou erro

		echo 'false';
	}

	pg_close( $db );
	exit;

	function send_email($reg, $dal)
	{
		// ----------------- 

		$msg = "Prezada(o) " . $reg['nome'];
		$v_assunto = 'Contribuiзгo do Plano SENGE Previdencia disponнvel para pagamento';
		$v_para = $reg['email'];
		$v_cc = '';
		$v_cco = '';
		$v_de = 'Senge Previdкncia';
		$vbcrlf = chr(10) . chr(13);

		$msg = $msg . $vbcrlf . $vbcrlf;
		$msg = $msg . "Sua contribuiзгo do Plano SENGE Previdencia encontra-se disponнvel para pagamento" . $vbcrlf;
		$msg = $msg . "Identificaзгo:" . $vbcrlf . $vbcrlf;

		// ------------------------- Бrea da mensagem texto:

		$msg = $msg . "-------------------------------------------------------------".$vbcrlf;
		$msg = $msg . "Nome: " . $reg['nome'] .$vbcrlf;
		$msg = $msg . "CPF: " . $reg['cpf'] .$vbcrlf;
		$msg = $msg . "Endereзo: " . $reg['endereco'].$vbcrlf;
		$msg = $msg . "Bairro: " . $reg['bairro'].$vbcrlf;
		$msg = $msg . "-------------------------------------------------------------".$vbcrlf;

		$msg = $msg . "Por favor, pague a contribuiзгo do plano, atravйs do link abaixo ou atravйs da бrea de auto-atendimento de nosso site:".$vbcrlf;

		$link="http://www.sengeprevidencia.com.br/escolha_valor.php?n=" . $reg['cd_registro_empregado'];
		$link=gera_link($link, $reg['cd_empresa'] , $reg['cd_registro_empregado'] , $reg['seq_dependencia']);

		// $msg = $msg . "http://www.sengeprevidencia.com.br/escolha_valor.php?n=" . $reg['cd_registro_empregado'] . $vbcrlf;
		$msg = $msg . $link . $vbcrlf;
		$msg = $msg . "-------------------------------------------------------------".$vbcrlf;
		$msg = $msg . "Esta mensagem foi enviada pelo Sistema SENGE Previdкncia.";
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
		$texto = $tipo . " - Contribuiзгo de {MES}/{ANO} enviada.";

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

		$dal->setAttribute('{de}', 'SENGE Contribuiзгo');
		$dal->setAttribute('{para}', $emails);
		$dal->setAttribute('{cc}', '');
		$dal->setAttribute('{cco}', '');
		$dal->setAttribute('{assunto}', 'SENGE Contribuiзгo');
		$dal->setAttribute('{texto}', $texto);
		$dal->setAttribute('{cd_evento}', enum_projetos_eventos::SENGE_EMAIL_CONTRIBUICAO);

		$ret = $dal->executeQuery(true);

		return $ret;
	}
?>