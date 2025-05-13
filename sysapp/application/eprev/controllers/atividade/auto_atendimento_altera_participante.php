<?php
   include_once('inc/sessao_auto_atendimento.php');
   include_once('inc/conexao.php');
   include_once('inc/funcoes.php');
   include_once('inc/class.TemplatePower.inc.php');
   
	#### SEPARA CEP E COMPLEMENTO ###
	$ar_tmp = explode("-",$_POST['nr_cep']);
	$nr_cep             = $ar_tmp[0] == '' ? 'NULL' : $ar_tmp[0];
	$nr_cep_complemento = $ar_tmp[1] == '' ? 'NULL' : $ar_tmp[1];   

	#### SEPARA TELEFONE E DDD ###
	$ar_tmp = explode(" ",str_replace("(","",str_replace(")","",$_POST['nr_telefone'])));
	$nr_ddd      = $ar_tmp[0] == '' ? 'NULL' : substr($ar_tmp[0],0,3);
	$nr_telefone = $ar_tmp[1] == '' ? 'NULL' : $ar_tmp[1];  

	#### SEPARA CELULAR E DDD ###
	$ar_tmp = explode(" ",str_replace("(","",str_replace(")","",$_POST['nr_celular'])));
	$nr_ddd_celular = $ar_tmp[0] == '' ? 'NULL' : substr($ar_tmp[0],0,3);
	$nr_celular     = $ar_tmp[1] == '' ? 'NULL' : $ar_tmp[1]; 	
	
	if ((($nr_cep == "NULL") or (is_null($nr_cep))) or (($nr_cep_complemento == "NULL") or (is_null($nr_cep_complemento))))
	{
		$tpl = new TemplatePower('tpl/tpl_erros.htm');
		$tpl->prepare();
		$tpl->assign('mensagem', 'Informe um CEP válido.');	 
		$tpl->printToScreen();	   
	}
	else
	{
        $qr_sql = "
					INSERT INTO public.log_acessos_usuario 
						 (
						   sid,
						   hora,
						   pagina
						 ) 
				    VALUES           
					     (
						   ".$_SESSION['SID'].", 
						   CURRENT_TIMESTAMP,
						   'ALTERA_PARTICIPANTE'
						 )
				  ";
        @pg_query($db,$qr_sql);  
		
        $qr_sql = "
					UPDATE public.participantes 
					   SET endereco                 = UPPER(funcoes.remove_acento('".$_POST['endereco']."')),
					       nr_endereco              = UPPER(funcoes.remove_acento('".$_POST['nr_endereco']."')),
					       complemento_endereco     = ".(trim($_POST['complemento_endereco']) == "" ? "NULL" : "UPPER(funcoes.remove_acento('".$_POST['complemento_endereco']."'))").",
						   logradouro               = UPPER(funcoes.remove_acento('".$_POST['endereco']."')) || ', ' ||
						                              UPPER(funcoes.remove_acento('".$_POST['nr_endereco']."')) || 
													  CASE WHEN ".(trim($_POST['complemento_endereco']) == "" ? "NULL" : "UPPER(funcoes.remove_acento('".$_POST['complemento_endereco']."'))")." IS NOT NULL
													       THEN ".(trim($_POST['complemento_endereco']) == "" ? "NULL" : "'/'||UPPER(funcoes.remove_acento('".$_POST['complemento_endereco']."'))")."
														   ELSE ''
												      END,
						   bairro                   = UPPER(funcoes.remove_acento('".$_POST['txtBairro']."')),
						   cidade                   = TRIM(UPPER(funcoes.remove_acento('".$_POST['txtCidade']."'))),
						   unidade_federativa       = UPPER(funcoes.remove_acento('".$_POST['txtUf']."')),
						   cep                      =  ".intval($nr_cep).",
						   complemento_cep          =  ".intval($nr_cep_complemento).",
						   ddd                      =  ".intval($nr_ddd).",
						   telefone                 =  ".intval($nr_telefone).",
						   ramal                    =  ".intval(trim($_POST['nr_ramal']) == "" ? 0 : $_POST['nr_ramal']).",
						   ddd_celular              =  ".intval($nr_ddd_celular).",
						   celular                  =  ".intval($nr_celular).",						   
						   email                    = LOWER('".$_POST['txtEmail']."'),
						   email_profissional       = LOWER('".$_POST['txtEmailProfissional']."'),
						   dt_alteracao             =  CURRENT_TIMESTAMP,
						   bloqueio_ender           = 'N',
						   motivo_devolucao_correio = 0,
						   dt_alteracao_endereco    = CURRENT_TIMESTAMP
					 WHERE cd_registro_empregado = ".$_SESSION['RE']."
					   AND seq_dependencia       = ".$_SESSION['SEQ']."
					   AND cd_empresa            = ".$_SESSION['EMP'].";
					   
					INSERT INTO public.participantes_hist
					SELECT *
					  FROM public.participantes
					 WHERE cd_registro_empregado = ".$_SESSION['RE']."
					   AND seq_dependencia       = ".$_SESSION['SEQ']."
					   AND cd_empresa            = ".$_SESSION['EMP'].";
					   
					UPDATE projetos.auto_atendimento_mensagem_publico
					   SET dt_exibido            = CURRENT_TIMESTAMP
					 WHERE cd_auto_atendimento_mensagem = 4 --MENSAGEM DE ENDERECO BLOQUEADO
					   AND cd_registro_empregado = ".$_SESSION['RE']."
					   AND seq_dependencia       = ".$_SESSION['SEQ']."
					   AND cd_empresa            = ".$_SESSION['EMP'].";
		";
		
		#echo "<PRE>$qr_sql</PRE>"; exit;
		
		#### ABRE TRANSACAO COM O BD ####
		pg_query($db,"BEGIN TRANSACTION");	
		$ob_resul= @pg_query($db,$qr_sql);

		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			#### DESFAZ A TRANSACAO COM BD####
			pg_query($db,"ROLLBACK TRANSACTION");
			//echo $ds_erro;
			#echo "<div style='display:none'>".$qr_sql."</div>";
			//exit;
			/*$tpl = new TemplatePower('tpl/tpl_erros.htm');
			$tpl->prepare();
			$tpl->assign('mensagem', 'Ocorreu um erro. Não foi possível gravar as alterações.');	 
			$tpl->printToScreen();*/

			echo 'Ocorreu um erro. Não foi possível gravar as alterações';
		}
		else
		{
			#### COMITA DADOS NO BD ####
			pg_query($db,"COMMIT TRANSACTION"); 		
			header('Location: auto_atendimento_participante.php?cd_secao=AACD&cd_artigo=32');
		}		

    }
?>