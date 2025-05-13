<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	include( 'inc/nextval_sequence.php' );

	// Verifica se existe cadastrado algum "exame ingresso" (não deve gravar se já existir!)
	$cd_empresa = 0;
	$cd_registro_empregado = 0;
	$seq_dependencia = 0;
	if(isset($_POST['cd_empresa'])) 			$cd_empresa = intval($_POST['cd_empresa']);
	if(isset($_POST['cd_registro_empregado'])) 	$cd_registro_empregado = intval($_POST['cd_registro_empregado']);
	if(isset($_POST['seq_dependencia'])) 		$seq_dependencia = intval($_POST['seq_dependencia']);
	
	$qr_sql = "
	SELECT cd_exame_ingresso AS qt 
	FROM projetos.exame_ingresso 
	WHERE cd_empresa={1} 
	AND cd_registro_empregado={2} 
	AND seq_dependencia={3};
	";

	$qr_sql = str_replace( "{1}", intval($cd_empresa), $qr_sql );
	$qr_sql = str_replace( "{2}", intval($cd_registro_empregado), $qr_sql );
	$qr_sql = str_replace( "{3}", intval($seq_dependencia), $qr_sql );

	$query = pg_query( $db, $qr_sql );
	$row = pg_fetch_row($query);
	
	if(intval($row[0])>0)
	{
		// já existe algum registro na tabela exame_ingresso para o RE postado!
		
		echo "
		<SCRIPT> 
		
			alert( 'O RE informado já consta no cadastro!' );
			location.href='exame_ingresso_contato_cad.php?cd_exame_ingresso=$row[0]';

		</SCRIPT>
		";
		
		return false;
		exit;
	}

	// NÃO existe nenhum registro em exame_ingresso para o RE postado, pode prosseguir com a gravação!
	$newId = intval(getNextval('projetos', 'exame_ingresso', 'cd_exame_ingresso', $db));

	$qr_sql = "
        INSERT INTO projetos.exame_ingresso
		     (
		       cd_exame_ingresso,
                       cd_empresa, 
			   cd_registro_empregado, 
			   seq_dependencia, 
                       ds_nome, 
			   dt_inclusao, 
			   cd_usuario_inclusao
			 )
		VALUES 
		     (
		       " . intval($newId) . ",
		       ".($_POST['cd_empresa']            == "" ? "NULL" : $_POST['cd_empresa']).",
		       ".($_POST['cd_registro_empregado'] == "" ? "NULL" : $_POST['cd_registro_empregado']).",
		       ".($_POST['seq_dependencia']       == "" ? "NULL" : $_POST['seq_dependencia']).",
			   TRIM(UPPER(funcoes.remove_acento('".$_POST['ds_nome']."'))),
			   CURRENT_TIMESTAMP,
			   ".$_SESSION['Z']."
			 );
	";

	if(trim($qr_sql) != "")
	{
		#### ---> ABRE TRANSACAO COM O BD <--- ####
		pg_query($db,"BEGIN TRANSACTION");	
		$ob_resul= @pg_query($db, $qr_sql);
		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:", "", pg_last_error($db));
			#### ---> DESFAZ A TRANSACAO COM BD <--- ####
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			echo $ds_erro."<BR><BR>";
			exit;
		}
		else
		{
			#### ---> COMITA DADOS NO BD <--- ####
			pg_query($db,"COMMIT TRANSACTION"); 
			echo "
					<SCRIPT> 

						alert( 'Registro gravado com sucesso!' );

						if( confirm('Deseja cadastrar contatos? Para cadastrar contatos clique OK!') )
						{
							location.href='exame_ingresso_contato_cad.php?cd_exame_ingresso=$newId';
						}
						else
						{
							location.href='exame_ingresso_cad.php';
						}

					</SCRIPT>
					<!--<META HTTP-EQUIV='Refresh' CONTENT='0;URL=exame_ingresso_cad.php'>-->
			     ";
			exit;
		}
	}
?>