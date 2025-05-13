<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

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
			   'PEDIDO_APOSENTADORIA_CEEEPREV_GRAVA'
			 );";
	@pg_query($db,$qr_sql);

	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/set_pedido_aposentadoria_ceeeprev");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID.
			"&re_cripto=".$_SESSION['RE_CRIPTO'].
			"&cd_pedido_aposentadoria_ceeeprev=".$_POST['cd_pedido_aposentadoria_ceeeprev'].
			"&tp_pedido_aposentadoria=".$_POST['tp_pedido_aposentadoria'].
			"&ds_nome=".$_POST['ds_nome'].
			"&dt_nascimento=".$_POST['dt_nascimento'].
			"&ds_cpf=".$_POST['ds_cpf'].
			"&ds_estado_civil=".$_POST['ds_estado_civil'].
			"&ds_naturalidade=".$_POST['ds_naturalidade'].
			"&ds_nacionalidade=".$_POST['ds_nacionalidade'].
			"&ds_endereco=".$_POST['ds_endereco'].
			"&nr_endereco=".$_POST['nr_endereco'].
			"&ds_complemento_endereco=".$_POST['ds_complemento_endereco'].
			"&ds_bairro=".$_POST['ds_bairro'].
			"&ds_cidade=".$_POST['ds_cidade'].
			"&ds_uf=".$_POST['ds_uf'].
			"&ds_cep=".$_POST['ds_cep'].
			"&ds_telefone1=".$_POST['ds_telefone1'].
			"&ds_telefone2=".$_POST['ds_telefone2'].
			"&ds_celular=".$_POST['ds_celular'].
			"&ds_email1=".$_POST['ds_email1'].
			"&ds_email2=".$_POST['ds_email2'].
			"&ds_banco=".$_POST['ds_banco'].
			"&ds_agencia=".$_POST['ds_agencia'].
			"&ds_conta=".$_POST['ds_conta'].
			"&fl_adiantamento_cip=".$_POST['fl_adiantamento_cip'].
			"&nr_adiantamento_cip=".$_POST['nr_adiantamento_cip'].
			"&fl_reversao_beneficio=".$_POST['fl_reversao_beneficio'].
			"&fl_politicamente_exposta=".$_POST['fl_politicamente_exposta'].
			"&fl_us_person=".$_POST['fl_us_person'].
			"&fl_encaminhar=".$_POST['fl_encaminhar']
		);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$_RETORNO = curl_exec($ch);
		curl_close ($ch);
		
		$FL_RETORNO = TRUE;

		$_RETORNO = json_decode($_RETORNO, TRUE);
		if (!(json_last_error() === JSON_ERROR_NONE))
		{
			switch (json_last_error()) 
			{
				case JSON_ERROR_NONE:
					$FL_RETORNO = TRUE;
				break;
					default:
					$FL_RETORNO = FALSE;
				break;
			}
		}

		if($FL_RETORNO)
		{
			if(intval($_RETORNO['error']['status']) == 0)
			{
				foreach ($_POST['dependente'] as $key => $item) 
				{
					if(trim($item) != '')
					{
						$ch = curl_init();

						curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/set_opcao_pedido_aposentadoria_ceeeprev_dependente_prev");
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID.
							"&re_cripto=".$_SESSION['RE_CRIPTO'].
							"&cd_pedido_aposentadoria_ceeeprev=".$_POST['cd_pedido_aposentadoria_ceeeprev'].
							"&re_cripto_dep=".$key.
							"&tp_opcao=".$item
						);
						
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$_RETORNO = curl_exec($ch);
						curl_close ($ch);
						
						$FL_RETORNO = TRUE;
						$_RETORNO = json_decode($_RETORNO, TRUE);
						if (!(json_last_error() === JSON_ERROR_NONE))
						{
							switch (json_last_error()) 
							{
								case JSON_ERROR_NONE:
									$FL_RETORNO = TRUE;
								break;
									default:
									$FL_RETORNO = FALSE;
								break;
							}
						}

						if($FL_RETORNO)
						{
							if(intval($_RETORNO['error']['status']) == 1)
							{
								echo "
									<script>
										alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
										document.location.href = 'auto_atendimento_pedido_aposentadoria_ceeeprev.php';
									</script>";
								exit;
							}
						}
						else 
						{
							echo "
								<script>
									alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
									document.location.href = 'auto_atendimento_pedido_aposentadoria_ceeeprev.php';
								</script>";
							exit;
						}
					}
				}

				if(trim($_FILES["documento_identidade"]["tmp_name"]) != '')
				{
					$file = file_get_contents($_FILES["documento_identidade"]["tmp_name"]);
			    	$documento = base64_encode($file);
			   
			    	$ext = pathinfo($_FILES["documento_identidade"]["name"], PATHINFO_EXTENSION);

					$ch = curl_init();

					curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/set_pedido_aposentadoria_ceeeprev_documento");
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, "id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']."&cd_pedido_aposentadoria_ceeeprev=".$_POST['cd_pedido_aposentadoria_ceeeprev']."&documento=".urlencode($documento)."&documento_ext=".$ext."&tp_documento=DI");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$_RETORNO = curl_exec($ch);
					curl_close ($ch);

					$FL_RETORNO = TRUE;
					$_RETORNO = json_decode($_RETORNO, TRUE);
					if (!(json_last_error() === JSON_ERROR_NONE))
					{
						switch (json_last_error()) 
						{
							case JSON_ERROR_NONE:
								$FL_RETORNO = TRUE;
							break;
								default:
								$FL_RETORNO = FALSE;
							break;
						}
					}

					if($FL_RETORNO)
					{
						if(intval($_RETORNO['error']['status']) == 1)
						{
							echo "
								<script>
									alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
									document.location.href = 'auto_atendimento_pedido_aposentadoria_ceeeprev.php';
								</script>";
							exit;
						}
					}
					else 
					{
						echo "
							<script>
								alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
								document.location.href = 'auto_atendimento_pedido_aposentadoria_ceeeprev.php';
							</script>";
						exit;
					}
				}

				if(trim($_FILES["documento_cpf"]["tmp_name"]) != '')
				{
					$file = file_get_contents($_FILES["documento_cpf"]["tmp_name"]);
			    	$documento = base64_encode($file);
			   
			    	$ext = pathinfo($_FILES["documento_cpf"]["name"], PATHINFO_EXTENSION);

					$ch = curl_init();

					curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/set_pedido_aposentadoria_ceeeprev_documento");
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, "id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']."&cd_pedido_aposentadoria_ceeeprev=".$_POST['cd_pedido_aposentadoria_ceeeprev']."&documento=".urlencode($documento)."&documento_ext=".$ext."&tp_documento=DC");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$_RETORNO = curl_exec($ch);
					curl_close ($ch);
					
					$FL_RETORNO = TRUE;
					$_RETORNO = json_decode($_RETORNO, TRUE);
					if (!(json_last_error() === JSON_ERROR_NONE))
					{
						switch (json_last_error()) 
						{
							case JSON_ERROR_NONE:
								$FL_RETORNO = TRUE;
							break;
								default:
								$FL_RETORNO = FALSE;
							break;
						}
					}

					if($FL_RETORNO)
					{
						if(intval($_RETORNO['error']['status']) == 1)
						{
							echo "
								<script>
									alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
									document.location.href = 'auto_atendimento_pedido_aposentadoria_ceeeprev.php';
								</script>";
							exit;
						}
					}
					else 
					{
						echo "
							<script>
								alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
								document.location.href = 'auto_atendimento_pedido_aposentadoria_ceeeprev.php';
							</script>";
						exit;
					}
				}

				if(trim($_FILES["rescisao_contrato"]["tmp_name"]) != '')
				{
					$file = file_get_contents($_FILES["rescisao_contrato"]["tmp_name"]);
			    	$documento = base64_encode($file);
			   
			    	$ext = pathinfo($_FILES["rescisao_contrato"]["name"], PATHINFO_EXTENSION);

					$ch = curl_init();

					curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/set_pedido_aposentadoria_ceeeprev_documento");
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, "id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']."&cd_pedido_aposentadoria_ceeeprev=".$_POST['cd_pedido_aposentadoria_ceeeprev']."&documento=".urlencode($documento)."&documento_ext=".$ext."&tp_documento=RC");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$_RETORNO = curl_exec($ch);
					curl_close ($ch);
					
					$FL_RETORNO = TRUE;
					$_RETORNO = json_decode($_RETORNO, TRUE);
					if (!(json_last_error() === JSON_ERROR_NONE))
					{
						switch (json_last_error()) 
						{
							case JSON_ERROR_NONE:
								$FL_RETORNO = TRUE;
							break;
								default:
								$FL_RETORNO = FALSE;
							break;
						}
					}

					if($FL_RETORNO)
					{
						if(intval($_RETORNO['error']['status']) == 1)
						{
							echo "
								<script>
									alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
									document.location.href = 'auto_atendimento_pedido_aposentadoria_ceeeprev.php';
								</script>";
							exit;
						}
					}
					else 
					{
						echo "
							<script>
								alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
								document.location.href = 'auto_atendimento_pedido_aposentadoria_ceeeprev.php';
							</script>";
						exit;
					}
				}

				if(trim($_FILES["comprovante_conta_corrente"]["tmp_name"]) != '')
				{
					$file = file_get_contents($_FILES["comprovante_conta_corrente"]["tmp_name"]);
			    	$documento = base64_encode($file);
			   
			    	$ext = pathinfo($_FILES["comprovante_conta_corrente"]["name"], PATHINFO_EXTENSION);

					$ch = curl_init();

					curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/set_pedido_aposentadoria_ceeeprev_documento");
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, "id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']."&cd_pedido_aposentadoria_ceeeprev=".$_POST['cd_pedido_aposentadoria_ceeeprev']."&documento=".urlencode($documento)."&documento_ext=".$ext."&tp_documento=CB");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$_RETORNO = curl_exec($ch);
					curl_close ($ch);
					
					$FL_RETORNO = TRUE;
					$_RETORNO = json_decode($_RETORNO, TRUE);
					if (!(json_last_error() === JSON_ERROR_NONE))
					{
						switch (json_last_error()) 
						{
							case JSON_ERROR_NONE:
								$FL_RETORNO = TRUE;
							break;
								default:
								$FL_RETORNO = FALSE;
							break;
						}
					}

					if($FL_RETORNO)
					{
						if(intval($_RETORNO['error']['status']) == 1)
						{
							echo "
								<script>
									alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
									document.location.href = 'auto_atendimento_pedido_aposentadoria_ceeeprev.php';
								</script>";
							exit;
						}
					}
					else 
					{
						echo "
							<script>
								alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
								document.location.href = 'auto_atendimento_pedido_aposentadoria_ceeeprev.php';
							</script>";
						exit;
					}
				}

				$fl_adicionar_dependente = (trim($_POST['fl_adicionar_dependente']) != '' ? $_POST['fl_adicionar_dependente'] : 'N');
				$fl_adicionar_dependente_prev = (trim($_POST['fl_adicionar_dependente_prev']) != '' ? $_POST['fl_adicionar_dependente_prev'] : 'N');

				echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=auto_atendimento_pedido_aposentadoria_ceeeprev.php?fl_adicionar_dependente='.$fl_adicionar_dependente.'&fl_adicionar_dependente_prev='.$fl_adicionar_dependente_prev.'">';
			}
			else
			{
				echo "
					<script>
						alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
						document.location.href = 'auto_atendimento_pedido_aposentadoria_ceeeprev.php';
					</script>";
				exit;
			}
		}
		else 
		{
			echo "
				<script>
					alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
					document.location.href = 'auto_atendimento_pedido_aposentadoria_ceeeprev.php';
				</script>";
			exit;
		}
	}
	else
	{
		echo "
			<script>
				alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
				document.location.href = 'auto_atendimento_pedido_aposentadoria_ceeeprev.php';
			</script>";
		exit;	
	}