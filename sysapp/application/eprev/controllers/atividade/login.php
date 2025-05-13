<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$BT_ATENDIMENTO = '
						<div style="float:left; height: 83px; padding-left: 5px; padding-top: 32px; ">
							<img src="img/atendimento_login.png" border="0" onclick="loginAtendente()" style="cursor:pointer;">
						</div>	
	                  ';
	if($_REQUEST['login_tipo'] == "C")
	{
		$lin_modelo = "	
			<div class='box-plano-escolhe-content box-plano-escolhe-statistic'>
				<table border='0' class='cadastro_identifica'>
					<tr>
						<td valign='top'>
							<img src='https://www.fcprev.com.br/logo_plano/plano_{codigo_plano}.png' border='0' style='clear:both; height: 80px;'>
						</td>
						<td valign='middle' align='center' >
							<span style='font-family: Calibri, Verdana, Arial; font-size: 14pt;'>
								<b>{nome}</b>
							</span>
							<BR>
							<span style='font-size: 8pt;'>
							{cd_empresa}/{cd_registro_empregado}/{seq_dependencia}
							</span>
						</td>
					</tr>
				</table>
				<table border='0' class='cadastro_identifica' style='margin-top: 0px;'>
					<tr>
						<td>
							{tipo_participante}
						</td>
					</tr>								
					<tr>
						<td>
							Plano: {nome_plano}
						</td>
					</tr>
					<tr>
						<td>{tipo_cliente}: {nome_empresa}</td>
					</tr>
					<tr>
						<td align='center'>
							<BR>
							<a href='login.php?emp={cd_empresa}&re={cd_registro_empregado}&seq={seq_dependencia}&re_cripto={RE_CRIPTO}&_v=".$_REQUEST['_v']."'><img src='img/bt_login_cpf_entrar.png' border='0'></a>
						</td>
					</tr>									
					<tr>
						<td align='center'>
							<a href='auto_atendimento_senha_valida_1.php?p={RE_CRIPTO}' style='color:#A8A8A8'>&raquo; Esqueci ou não tenho senha, clique aqui &laquo;</a>
						</td>
					</tr>
				</table>
			</div>";
			
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_plano");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&cpf=".$_REQUEST['login_aa_cpf']);
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
				$planos = $_RETORNO['result'];

				if(count($planos) < 2)
				{
					$_REQUEST['emp']       = intval($planos[0]['cd_empresa']);
					$_REQUEST['re']        = intval($planos[0]['cd_registro_empregado']);
					$_REQUEST['seq']       = intval($planos[0]['seq_dependencia']);			
					$_REQUEST['re_cripto'] = trim($planos[0]['re_cripto']);			
				}
				else
				{
					$conteudo = "";
					foreach ($planos as $key => $item)
					{
						$conteudo.= $lin_modelo;
						
						$conteudo = str_replace("{tipo_cliente}", ($item['tipo_cliente'] == "I" ? "Instituidor" : "Patrocinadora"), $conteudo);
						$conteudo = str_replace("{nome}", $item['nome'], $conteudo);
						$conteudo = str_replace("{codigo_plano}", intval($item['cd_plano']).(intval($item['cd_plano']) == 1 ? "_".intval($item['cd_empresa']): ""), $conteudo);
						$conteudo = str_replace("{cd_plano}", $item['cd_plano'], $conteudo);
						$conteudo = str_replace("{cd_empresa}", $item['cd_empresa'], $conteudo);
						$conteudo = str_replace("{cd_registro_empregado}", $item['cd_registro_empregado'], $conteudo);
						$conteudo = str_replace("{seq_dependencia}", $item['seq_dependencia'], $conteudo);
						$conteudo = str_replace("{nome_plano}", utf8_decode($item['nome_plano']), $conteudo);
						$conteudo = str_replace("{nome_empresa}", utf8_decode($item['nome_empresa']), $conteudo);
						$conteudo = str_replace("{tipo_participante}", $item['tipo_participante'], $conteudo);
						$conteudo = str_replace("{RE_CRIPTO}", $item['re_cripto'], $conteudo);
					}
								
					$tpl = new TemplatePower('tpl/tpl_escolhe_login_cpf.html');
					$tpl->prepare();
					$tpl->newBlock('conteudo');			
					$tpl->assign('conteudo',$conteudo);
					$tpl->printToScreen();				
					exit;
				}
			}
		}
	}
	elseif(trim($_REQUEST['_p']) != "")
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_plano_re");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_REQUEST['_p']);
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
				$plano = $_RETORNO['result'];

				$_REQUEST['emp']       = intval($plano['cd_empresa']);
				$_REQUEST['re']        = intval($plano['cd_registro_empregado']);
				$_REQUEST['seq']       = intval($plano['seq_dependencia']);
				$_REQUEST['re_cripto'] = trim($plano['re_cripto']);
			}
		}
	}
	elseif((trim($_REQUEST['emp']) == "") and (trim($_REQUEST['re']) == "") and (trim($_REQUEST['seq']) == ""))
	{

		$_REQUEST['emp'] = intval($_REQUEST['login_aa_cd_empresa_re']);
		$_REQUEST['re']  = intval($_REQUEST['login_aa_cd_registro_empregado']);
		$_REQUEST['seq'] = intval($_REQUEST['login_aa_seq_dependencia']);

		$qr_sql = "
			SELECT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
			  FROM participantes p 
			 WHERE cd_registro_empregado = ".intval($_REQUEST['re'])."
			   AND cd_empresa            = ".intval($_REQUEST['emp'])."
			   AND seq_dependencia	     = ".intval($_REQUEST['seq']).";";

		$ob_resul = @pg_query($db, $qr_sql);

		if ($reg = pg_fetch_array($ob_resul)) 
		{
			$_REQUEST['re_cripto'] = $reg['re_cripto'];
		}
		else
		{
			$_REQUEST['emp']       = '';
			$_REQUEST['re']        = '';
			$_REQUEST['seq']       = '';
			$_REQUEST['re_cripto'] = '';

		}

	}
	elseif((trim($_REQUEST['re_cripto']) == "") and (trim($_REQUEST['emp']) != "") and (trim($_REQUEST['re']) != "") and (trim($_REQUEST['seq']) != ""))
	{
		$qr_sql = "
			SELECT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
			  FROM participantes p 
			 WHERE cd_registro_empregado = ".intval($_REQUEST['re'])."
			   AND cd_empresa            = ".intval($_REQUEST['emp'])."
			   AND seq_dependencia	     = ".intval($_REQUEST['seq']).";";

		$ob_resul = @pg_query($db, $qr_sql);

		if ($reg = pg_fetch_array($ob_resul)) 
		{
			$_REQUEST['re_cripto'] = $reg['re_cripto'];
		}
		else
		{
			$_REQUEST['emp']       = '';
			$_REQUEST['re']        = '';
			$_REQUEST['seq']       = '';
			$_REQUEST['re_cripto'] = '';

		}
	}	
					  
	/*
	echo "<PRE>".print_r($qr_sql,true)."</PRE>"; 
	echo "<PRE>".print_r($_REQUEST,true)."</PRE>"; 
	echo "<PRE>".print_r($_SESSION,true)."</PRE>"; 
	exit;	
	*/		

	##############################################################
			
	$emp       = intval($_REQUEST['emp']);
	$re        = intval($_REQUEST['re']);
	$seq       = intval($_REQUEST['seq']);
	$re_cripto = trim($_REQUEST['re_cripto']);

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_plano_re");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$re_cripto);
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
			$plano = $_RETORNO['result'];

			$nome = $plano['nome'];

			if(trim($plano['tp_senha']) == 'SS')
			{
				$qr_sql = "
					INSERT INTO public.erros_login_autoatendimento 
					VALUES 
						 (
						   ".intval($_REQUEST['emp']).", 
						   ".intval($_REQUEST['re']).", 
						   '".$_REQUEST['senha']."', 
						   CURRENT_TIMESTAMP, 
						   'Não possui senha'
						  );";
				@pg_query($db, $qr_sql);				
				
				echo '
					<script>
						var confirmacao = "VOCÊ NÃO POSSUI SENHA.\n\n" +
										  "Deseja criar uma senha agora?\n\n"+
										  "Clique [Ok] para SIM\n\n"+
										  "Clique [Cancelar] para NÃO\n\n";						
						if(confirm(confirmacao))
						{
							location.href="auto_atendimento_senha_valida_1.php?p='.$re_cripto.'";
						}
						else
						{
							location.href="index.php";
						}
					</script>';

				exit;
			}
			else if(trim($plano['tp_senha']) == 'BE')
			{
				$qr_sql = "
					INSERT INTO public.erros_login_autoatendimento 
					VALUES 
						 (
						   ".intval($_REQUEST['emp']).", 
						   ".intval($_REQUEST['re']).", 
						   '".$_REQUEST['senha']."', 
						   CURRENT_TIMESTAMP, 
						   'Senha bloqueada'
						  );";
				@pg_query($db, $qr_sql);				
				echo '
						<script>
							alert("SUA SENHA ESTÁ BLOQUEADA.\n\nPara desbloquear, entre contato com 08005102596, de segunda a sexta, das 10 às 16 horas.\n\n");
							location.href="index.php";
						</script>
					 ';				
				exit;
			}
			else if(trim($plano['tp_senha']) == 'BC')
			{
				$qr_sql = "
					INSERT INTO public.erros_login_autoatendimento 
					VALUES 
						 (
						   ".intval($_REQUEST['emp']).", 
						   ".intval($_REQUEST['re']).", 
						   '".$_REQUEST['senha']."', 
						   CURRENT_TIMESTAMP, 
						   'Senha bloqueada'
						  );";
				@pg_query($db, $qr_sql);
				
				echo '
					<script>
						var confirmacao = "SUA SENHA ESTÁ BLOQUEADA.\n\n" +
										  "Deseja deseja desbloquear agora?\n\n"+
										  "Clique [Ok] para SIM\n\n"+
										  "Clique [Cancelar] para NÃO\n\n";						
						if(confirm(confirmacao))
						{
							location.href="auto_atendimento_senha_valida_1.php?p='.$re_cripto.'";
						}
						else
						{
							location.href="index.php";
						}
					</script>';
				exit;
			}
			else
			{
				$solicita_senha = '<a href="#" style="text-decoration:none;cursor:default;">Esqueceu a senha? Entre contato com 08005102596, de segunda a sexta, das 10 às 16 horas.</a>';
				if(trim($plano['tp_senha']) == 'SC')
				{
					$solicita_senha = '<a href="auto_atendimento_senha_valida_1.php?p='.$re_cripto.'">&raquo; Esqueci minha senha, clique aqui &laquo;</a>';
				}

				$tpl = new TemplatePower('tpl/tpl_login_senha.html');
				$tpl->prepare();
				$tpl->assign('img_sup', $emp);
				$tpl->assign('msg', $m);
				$tpl->assign('nome', $nome);
				$tpl->assign('RE_CRIPTO', $re_cripto);
				$tpl->assign('emp', $emp);
				$tpl->assign('re', $re);
				$tpl->assign('seq', $seq);
				$tpl->assign('origem', $origem);
				$tpl->assign('solicita_senha', $solicita_senha);
				$tpl->assign('ir_para', str_replace(";","?",str_replace("|","&",trim($_REQUEST['_v']))));

				if($plano['cd_plano'] == 0)
				{
					if (($_SERVER['SERVER_ADDR'] == '10.63.255.5') or ($_SERVER['SERVER_ADDR'] == '10.63.255.7'))
					{
						$tpl->assign('form', 'https://www.fundacaofamiliaprevidencia.com.br/autoatendimento/index.php/login/logar');
					}
					else
					{
						$tpl->assign('form', 'http://10.63.255.222/eletroceee/autoatendimento/index.php/login/logar');
					}
				}
				else
				{
					$tpl->assign('form', 'login_check.php');
				}
				
				if((preg_match('/10.63./',$_SERVER['REMOTE_ADDR'])) or (preg_match('/10.63.4./',$_SERVER['REMOTE_ADDR'])))
				{
					$tpl->assign('BT_ATENDIMENTO', $BT_ATENDIMENTO);
				}
				else
				{
					$tpl->assign('BT_ATENDIMENTO', "");
				}
				
				srand ((float) microtime() * 10000000);
				$input = array ("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
				$rand_keys = array_rand ($input, 10);
				$tpl->assign('n0', $input[$rand_keys[0]]);
				$tpl->assign('n1', $input[$rand_keys[1]]);
				$tpl->assign('n2', $input[$rand_keys[2]]);
				$tpl->assign('n3', $input[$rand_keys[3]]);
				$tpl->assign('n4', $input[$rand_keys[4]]);
				$tpl->assign('n5', $input[$rand_keys[5]]);
				$tpl->assign('n6', $input[$rand_keys[6]]);
				$tpl->assign('n7', $input[$rand_keys[7]]);
				$tpl->assign('n8', $input[$rand_keys[8]]);
				$tpl->assign('n9', $input[$rand_keys[9]]);
				$tpl->printToScreen();
			}	
		}
		else 
		{
			$qr_sql = "
						INSERT INTO public.erros_login_autoatendimento 
						VALUES 
							 (
							   ".intval($_REQUEST['emp']).", 
							   ".intval($_REQUEST['re']).", 
							   '".$_REQUEST['senha']."', 
							   CURRENT_TIMESTAMP, 
							   'Usuário não cadastrado'
							  )
					  ";
			@pg_query($db, $qr_sql);	
		
			echo '
					<script>
						alert("NÃO FOI POSSÍVEL LOCALIZAR SEU CADASTRO.\n\nPor favor verifique os dados de identificação (CPF ou RE).\n\nEm caso de dúvida, entre contato com 08005102596, de segunda a sexta, das 10 às 16 horas.\n\n");
						location.href="index.php";
					</script>
				 ';				
			exit;
		}
	}
	else 
	{
		$qr_sql = "
					INSERT INTO public.erros_login_autoatendimento 
					VALUES 
						 (
						   ".intval($_REQUEST['emp']).", 
						   ".intval($_REQUEST['re']).", 
						   '".$_REQUEST['senha']."', 
						   CURRENT_TIMESTAMP, 
						   'Usuário não cadastrado'
						  )
				  ";
		@pg_query($db, $qr_sql);	
	
		echo '
				<script>
					alert("Erro Interno.\n\nTente mais tarde ou entre contato com 08005102596, de segunda a sexta, das 10 às 16 horas.\n\n");
					location.href="index.php";
				</script>
			 ';				
		exit;
	}

	
	/*
	$fl_exibe = "display:none;";
	$qr_sql = "
		SELECT p.nome, 
			   p.seq_dependencia, 
			   s.codigo_355, 
			   s.codigo_356, 
			   s.opcao_contrato_valida,
			   s.data_bloqueio,
               funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
		  FROM participantes p, 
			   participantes_ccin s 
		 WHERE p.cd_registro_empregado = s.cd_registro_empregado 
		   AND p.cd_empresa            = s.cd_empresa 
		   AND p.seq_dependencia       = s.seq_dependencia 
		   AND s.cd_registro_empregado = ".intval($re)."
		   AND s.cd_empresa            = ".intval($emp)."
		   AND s.seq_dependencia	   = ".intval($seq)."
		   AND p.dt_obito              IS NULL
	";
	$ob_resul = @pg_query($db, $qr_sql);
	if ($reg = pg_fetch_array($ob_resul)) 
	{
		$nome = $reg['nome'];
		$re_cripto = $reg['re_cripto'];

		$solicita_senha = '<a href="#" style="text-decoration:none;cursor:default;">Esqueceu a senha? Entre contato com 08005102596, de segunda a sexta, das 10 às 16 horas.</a>';
		if(intval($reg['opcao_contrato_valida']) == 1)
		{
			$solicita_senha = '<a href="auto_atendimento_senha_valida_1.php?p='.$re_cripto.'">&raquo; Esqueci minha senha, clique aqui &laquo;</a>';
		}

		if (($reg['codigo_355'] == 'S') and ($reg['codigo_356'] == 'N'))
		{
			$tpl = new TemplatePower('tpl/tpl_login_senha.html');
			$tpl->prepare();
			$tpl->assign('img_sup', $emp);
			$tpl->assign('msg', $m);
			$tpl->assign('nome', $nome);
			$tpl->assign('RE_CRIPTO', $re_cripto);
			$tpl->assign('emp', $emp);
			$tpl->assign('re', $re);
			$tpl->assign('seq', $seq);
			$tpl->assign('origem', $origem);
			$tpl->assign('solicita_senha', $solicita_senha);
			$tpl->assign('ir_para', str_replace(";","?",str_replace("|","&",trim($_REQUEST['_v']))));
			
			if((preg_match('/10.63./',$_SERVER['REMOTE_ADDR'])) or (preg_match('/10.63.4./',$_SERVER['REMOTE_ADDR'])))
			{
				$tpl->assign('BT_ATENDIMENTO', $BT_ATENDIMENTO);
			}
			else
			{
				$tpl->assign('BT_ATENDIMENTO', "");
			}
			
			srand ((float) microtime() * 10000000);
			$input = array ("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
			$rand_keys = array_rand ($input, 10);
			$tpl->assign('n0', $input[$rand_keys[0]]);
			$tpl->assign('n1', $input[$rand_keys[1]]);
			$tpl->assign('n2', $input[$rand_keys[2]]);
			$tpl->assign('n3', $input[$rand_keys[3]]);
			$tpl->assign('n4', $input[$rand_keys[4]]);
			$tpl->assign('n5', $input[$rand_keys[5]]);
			$tpl->assign('n6', $input[$rand_keys[6]]);
			$tpl->assign('n7', $input[$rand_keys[7]]);
			$tpl->assign('n8', $input[$rand_keys[8]]);
			$tpl->assign('n9', $input[$rand_keys[9]]);
			$tpl->printToScreen();		
		}
		else
		{
			if(intval($reg['opcao_contrato_valida']) == 0)
			{
				$qr_sql = "
							INSERT INTO public.erros_login_autoatendimento 
							VALUES 
								 (
								   ".intval($_REQUEST['emp']).", 
								   ".intval($_REQUEST['re']).", 
								   '".$_REQUEST['senha']."', 
								   CURRENT_TIMESTAMP, 
								   'Não possui senha'
								  )
						  ";
				@pg_query($db, $qr_sql);				
				
				echo '
						<script>
							var confirmacao = "VOCÊ NÃO POSSUI SENHA.\n\n" +
											  "Deseja criar uma senha agora?\n\n"+
											  "Clique [Ok] para SIM\n\n"+
											  "Clique [Cancelar] para NÃO\n\n";						
							if(confirm(confirmacao))
							{
								location.href="auto_atendimento_senha_valida_1.php?p='.$re_cripto.'";
							}
							else
							{
								location.href="index.php";
							}
						</script>
				     ';
				exit;
			}
			elseif(intval($reg['opcao_contrato_valida']) == 2)
			{
				$qr_sql = "
							INSERT INTO public.erros_login_autoatendimento 
							VALUES 
								 (
								   ".intval($_REQUEST['emp']).", 
								   ".intval($_REQUEST['re']).", 
								   '".$_REQUEST['senha']."', 
								   CURRENT_TIMESTAMP, 
								   'Senha bloqueada'
								  )
						  ";
				@pg_query($db, $qr_sql);				
				echo '
						<script>
							alert("SUA SENHA ESTÁ BLOQUEADA.\n\nPara desbloquear, entre contato com 0800512596, de segunda a sexta, das 08 às 17 horas.\n\n");
							location.href="index.php";
						</script>
					 ';				
				exit;				
			}
			else
			{
				$qr_sql = "
							INSERT INTO public.erros_login_autoatendimento 
							VALUES 
								 (
								   ".intval($_REQUEST['emp']).", 
								   ".intval($_REQUEST['re']).", 
								   '".$_REQUEST['senha']."', 
								   CURRENT_TIMESTAMP, 
								   'Senha bloqueada'
								  )
						  ";
				@pg_query($db, $qr_sql);
				
				echo '
						<script>
							var confirmacao = "SUA SENHA ESTÁ BLOQUEADA.\n\n" +
											  "Deseja deseja desbloquear agora?\n\n"+
											  "Clique [Ok] para SIM\n\n"+
											  "Clique [Cancelar] para NÃO\n\n";						
							if(confirm(confirmacao))
							{
								location.href="auto_atendimento_senha_valida_1.php?p='.$re_cripto.'";
							}
							else
							{
								location.href="index.php";
							}
						</script>
					 ';
				exit;				
				
			}
		}
	}
	else 
	{
		$qr_sql = "
					INSERT INTO public.erros_login_autoatendimento 
					VALUES 
						 (
						   ".intval($_REQUEST['emp']).", 
						   ".intval($_REQUEST['re']).", 
						   '".$_REQUEST['senha']."', 
						   CURRENT_TIMESTAMP, 
						   'Usuário não cadastrado'
						  )
				  ";
		@pg_query($db, $qr_sql);	
	
		echo '
				<script>
					alert("NÃO FOI POSSÍVEL LOCALIZAR SEU CADASTRO.\n\nPor favor verifique os dados de identificação (CPF ou RE).\n\nEm caso de dúvida, entre contato com 0800512596, de segunda a sexta, das 08 às 17 horas.\n\n");
					location.href="index.php";
				</script>
			 ';				
		exit;
	}
	*/
?>