<?
//   include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
	$txt_dt_nascimento  = ($dt_nascimento  == '' ? 'NULL' : "TO_DATE('".$dt_nascimento."','DD/MM/YYYY')" );
	$txt_dt_emissao  = ( $dt_emissao  == '' ? 'NULL' : "TO_DATE('".$dt_emissao."','DD/MM/YYYY')" );
	$txt_crea  = ( $dt_crea  == '' ? 'NULL' : $crea );
	$cd_empresa = 7;
	$cd_sequencia = 0;
// --------------------------------------------------------------------------------
	$qr_sql = " update 	expansao.inscritos ";
	$qr_sql = $qr_sql . " set 	nome = '$nome',	";
	$qr_sql = $qr_sql . "			cpf = $cpf,	";
	$qr_sql = $qr_sql . "			rg = $rg,	";
	$qr_sql = $qr_sql . "			emissor = '$emissor', ";		
	$qr_sql = $qr_sql . "			dt_emissao = $txt_dt_emissao, ";
	$qr_sql = $qr_sql . "			crea = $txt_crea, ";
	$qr_sql = $qr_sql . "			cd_instituicao = $cbo_banco, ";		
	$qr_sql = $qr_sql . "			cd_agencia = '$cbo_agencia', ";
	$qr_sql = $qr_sql . "			conta_bco = '$conta', ";
	$qr_sql = $qr_sql . "			sexo = '$sexo', ";
	$qr_sql = $qr_sql . "			dt_nascimento = $txt_dt_nascimento, ";
	$qr_sql = $qr_sql . "			cd_estado_civil = $cbo_estado_civil, ";
	$qr_sql = $qr_sql . "			cd_grau_instrucao = $cbo_grau_instrucao, ";
	$qr_sql = $qr_sql . "			nome_pai = '$nome_pai', ";
	$qr_sql = $qr_sql . "			nome_mae = '$nome_mae', ";
	$qr_sql = $qr_sql . "	 		opt_irpf = $irpf ";
	$qr_sql = $qr_sql . " where 	cd_empresa = $cd_empresa ";
	$qr_sql = $qr_sql . "	  and	cd_registro_empregado = $cd_registro_empregado ";

	//echo $qr_sql;EXIT;
	
	#### ABRE TRANSACAO COM O BD ####
	pg_query($db,"BEGIN TRANSACTION");	
	$ob_resul= @pg_query($db,$qr_sql);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		#### DESFAZ A TRANSACAO COM O BD ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		echo $ds_erro;//."<PRE>".$qr_sql."</PRE>";
		exit;
	}
	else
	{
		#### GRAVA DADOS NO BD ####
		pg_query($db,"COMMIT TRANSACTION");
		header('location: cad_inscritos.php?c=' . $cd_registro_empregado . '&a=s');
	}	
	
	exit;
// --------------------------------------------------------------------------------	
function convdata_br_iso($dt) {
      // Pressupõe que a data esteja no formato DD/MM/AAAA
      // A melhor forma de gravar datas no PostgreSQL é utilizando 
      // uma string no formato DDDD-MM-AA. Esta função justamente 
      // adequa a data a este formato
      $d = substr($dt, 0, 2);
      $m = substr($dt, 3, 2);
      $a = substr($dt, 6, 4);
	  $hora = date("H:m:s");
      return $a.'-'.$m.'-'.$d.' '.$hora;
   }
?>