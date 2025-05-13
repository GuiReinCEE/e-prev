<?
	header('Content-Type: text/html; charset=ISO-8859-1'); 
	include_once('inc/conexao.php');
	
	//print_r($_POST);
	
	if($_POST)
	{
		if($_POST['ds_funcao'] == "buscaRE")
		{
			buscaRE($_POST['cd_pessoa']);
		}
		
		if($_POST['ds_funcao'] == "buscaCPF")
		{
			buscaCPF($_POST['cd_pessoa']);
		}		
		
		if($_POST['ds_funcao'] == "buscaRG")
		{
			buscaRG($_POST['cd_pessoa']);
		}	

		if($_POST['ds_funcao'] == "buscaString")
		{
			buscaString($_POST['ds_busca'],$_POST['ds_campo']);
		}	

		if($_POST['ds_funcao'] == "buscaTipoAcesso")
		{
			buscaTipoAcesso($_POST['cd_tipo_acesso']);
		}		
		
		if($_POST['ds_funcao'] == "buscaNome")
		{
			buscaNome($_POST['ds_busca']);
		}

		if($_POST['ds_funcao'] == "buscaNomeDados")
		{
			buscaNomeDados($_POST['ds_busca']);
		}
		
		if($_POST['ds_funcao'] == "buscaNomeSaida")
		{
			buscaNomeSaida($_POST['ds_busca']);
		}

		if($_POST['ds_funcao'] == "buscaProcedenciaSaida")
		{
			buscaProcedenciaSaida($_POST['ds_busca']);
		}
		
		if($_POST['ds_funcao'] == "marcaSaida")
		{
			marcaSaida($_POST['cd_acesso']);
		}	
		
		if($_POST['ds_funcao'] == "marcaSaidaCracha")
		{
			marcaSaidaCracha($_POST['nr_cracha']);
		}			
		
		if($_POST['ds_funcao'] == "marcaSaidaNome")
		{
			marcaSaidaNome($_POST['ds_nome']);
		}			

		if($_POST['ds_funcao'] == "marcaSaidaProcedencia")
		{
			marcaSaidaProcedencia($_POST['ds_origem']);
		}
		
		if($_POST['ds_funcao'] == "buscaMovimento")
		{
			buscaMovimento();
		}		
		
		if($_POST['ds_funcao'] == "buscaMovimentoSaida")
		{
			buscaMovimentoSaida();
		}		
	}
	else
	{
		echo "<br>";
	}

	#### BUSCA POR RE ####
	function buscaRE($cd_pessoa)
	{
		global $db;
		$qr_select = "
						SELECT UPPER(TRIM(REPLACE(p.nome,'''',''))) AS nome,
						       TRIM(TO_CHAR(p.cpf_mf,'00000000000')) AS cpf,
							   p.cd_empresa,
							   p.cd_registro_empregado AS cd_re,
							   p.seq_dependencia AS seq
						  FROM public.participantes p
						 WHERE p.cd_registro_empregado = ".$cd_pessoa."
						 
						 UNION
						
						SELECT DISTINCT(UPPER(TRIM(REPLACE(ap.ds_nome,'''','')))) AS nome,
						       TRIM(TO_CHAR(ap.nr_cpf,'00000000000')) AS cpf,
							   ap.cd_empresa,
							   ap.cd_registro_empregado AS cd_re,
							   ap.seq_dependencia AS seq
						  FROM projetos.visitantes ap
						 WHERE ap.cd_registro_empregado = ".$cd_pessoa."
						 
						 ORDER BY 1		
				     ";
		$ob_result = pg_query($db, $qr_select);
		
		if(pg_num_rows($ob_result) == 1)
		{
			$ar_reg = pg_fetch_array($ob_result);
			echo "nr_total = 1; setDados('".$ar_reg['cd_empresa']."','".$ar_reg['cd_re']."','".$ar_reg['seq']."','','".$ar_reg['cpf']."','".$ar_reg['nome']."');";
		}
		else if(pg_num_rows($ob_result) > 1)
		{
			echo "
					<table  align='center' class='tb_resultado'>
						<tr>
							<th>
								SEQ
							</th>
							<th>
								NOME
							</th>						
						</tr>
			     ";
			while ($ar_reg = pg_fetch_array($ob_result)) 
			{
				echo "	<tr onmouseover=\"this.className='tb_resultado_selecionado';\" 
							onmouseout=\"this.className='';\" title='Clique para selecionar'
							onclick=\"setDados('".$ar_reg['cd_empresa']."','".$ar_reg['cd_re']."','".$ar_reg['seq']."','','".$ar_reg['cpf']."','".$ar_reg['nome']."');\"
						>
							<td align='right'>
								".$ar_reg['seq']."
							</td>
							<td>
								".$ar_reg['nome']."
							</td>
						</tr>";
			}
			echo "	</table>";
		}
	}	
	
	#### BUSCA POR CPF ####
	function buscaCPF($cd_pessoa)
	{
		global $db;
		$qr_select = "
						SELECT UPPER(TRIM(p.nome)) AS nome,
							   TRIM(TO_CHAR(p.cpf_mf,'00000000000')) AS cpf,
							   p.cd_empresa,
							   p.cd_registro_empregado AS cd_re,
							   p.seq_dependencia AS seq							   
						  FROM public.participantes p
						 WHERE p.cpf_mf  = ".$cd_pessoa."
						 
						 UNION
						
						SELECT DISTINCT(UPPER(TRIM(ap.ds_nome))) AS nome,
						       TRIM(TO_CHAR(ap.nr_cpf,'00000000000')) AS cpf,
							   ap.cd_empresa,
							   ap.cd_registro_empregado AS cd_re,
							   ap.seq_dependencia AS seq						   
						  FROM projetos.visitantes ap
						 WHERE ap.nr_cpf  = ".$cd_pessoa."
						 
						 ORDER BY 1						 
				     ";
		$ob_result = pg_query($db, $qr_select);
		
		if(pg_num_rows($ob_result) == 1)
		{
			$ar_reg = pg_fetch_array($ob_result);
			echo "nr_total = 1; setDados('".$ar_reg['cd_empresa']."','".$ar_reg['cd_re']."','".$ar_reg['seq']."','','".$ar_reg['cpf']."','".$ar_reg['nome']."');";
		}
		else if(pg_num_rows($ob_result) > 1)
		{
			echo "
					<table align='center' class='tb_resultado'>
						<tr>
							<th align='center'>
								SEQ
							</th>
							<th align='center'>
								NOME
							</th>						
						</tr>
			     ";
			while ($ar_reg = pg_fetch_array($ob_result)) 
			{
				echo "	<tr onmouseover=\"this.className='tb_resultado_selecionado';\" 
							onmouseout=\"this.className='';\" title='Clique para selecionar'
							onclick=\"setDados('".$ar_reg['cd_empresa']."','".$ar_reg['cd_re']."','".$ar_reg['seq']."','','".$ar_reg['cpf']."','".$ar_reg['nome']."');\"
						>
							<td align='right'>
								".$ar_reg['seq']."
							</td>
							<td>
								".$ar_reg['nome']."
							</td>
						</tr>";
			}
			echo "	</table>";
		}
	}		
	
	#### BUSCA POR RG ####
	function buscaRG($cd_pessoa)
	{
		global $db;
		$qr_select = "
						SELECT DISTINCT(UPPER(TRIM(ap.ds_nome))) AS nome,
							   TRIM(TO_CHAR(ap.nr_rg,'0000000000')) AS nr_rg
						  FROM projetos.visitantes ap
						 WHERE ap.nr_rg = ".$cd_pessoa."
						 
						 ORDER BY 1						 
				     ";
		$ob_result = pg_query($db, $qr_select);
		
		if(pg_num_rows($ob_result) == 1)
		{
			$ar_reg = pg_fetch_array($ob_result);
			echo "nr_total = 1; setDados('','','','".$ar_reg['nr_rg']."','','".$ar_reg['nome']."');";
		}
		else if(pg_num_rows($ob_result) > 1)
		{
			echo "
					<table align='center' class='tb_resultado'>
						<tr>
							<th align='center'>
								NOME
							</th>						
						</tr>
			     ";
			while ($ar_reg = pg_fetch_array($ob_result)) 
			{
				echo "	<tr onmouseover=\"this.className='tb_resultado_selecionado';\" 
							onmouseout=\"this.className='';\" title='Clique para selecionar'
							onclick=\"setDados('','','','".$ar_reg['nr_rg']."','','".$ar_reg['nome']."');\"
						>
							<td>
								".$ar_reg['nome']."
							</td>
						</tr>";
			}
			echo "	</table>";
		}
	}		
	
	#### BUSCA TIPO DE ACESSO ####
	function buscaTipoAcesso($cd_tipo_visita)
	{
		global $db;
		$qr_select = "
						SELECT l.descricao
						  FROM public.listas l
						 WHERE l.codigo    = '".$cd_tipo_visita."'
						   AND l.categoria = 'TACE'
						   AND l.divisao   = 'GAD'
				     ";
		$ob_result = pg_query($db, $qr_select);
		$ar_reg = pg_fetch_array($ob_result);
		echo $ar_reg['descricao'];
	}	
	
	#### BUSCA NOME ####
	function buscaNome($ds_busca)
	{
		global $db;

		$qr_select = "
						SELECT TRIM(UPPER(p.nome)) AS nome
						  FROM public.participantes p
						 WHERE UPPER(p.nome) LIKE UPPER('".utf8_decode($ds_busca)."%')
						 
						 UNION
						
						SELECT DISTINCT(TRIM(UPPER(ap.ds_nome))) AS nome
						  FROM projetos.visitantes ap
						 WHERE UPPER(ap.ds_nome) LIKE UPPER('".utf8_decode($ds_busca)."%')
						 
						 ORDER BY nome	
						 
						 LIMIT 15
				     ";
		$ob_result = pg_query($db, $qr_select);
		while ($ar_reg = pg_fetch_array($ob_result)) 
		{
			echo '"'.$ar_reg['nome'].'",';
		}
		echo '""';
	}		
	
	#### BUSCA NOME DADOS ####
	function buscaNomeDados($ds_busca)
	{
		global $db;

		$qr_select = "
						SELECT n.nome,
						       p.cd_registro_empregado,	       
					           p.cd_empresa,
						       p.seq_dependencia,
					           COALESCE(p.nr_cpf,v.nr_cpf) AS nr_cpf,
					           v.nr_rg
					      FROM (SELECT UPPER(TRIM(p.nome)) AS nome
								  FROM public.participantes p
								 WHERE UPPER(p.nome) LIKE UPPER('".utf8_decode($ds_busca)."%')
										 
								 UNION
										
								SELECT DISTINCT(UPPER(TRIM(ap.ds_nome))) AS nome
								  FROM projetos.visitantes ap
								 WHERE UPPER(ap.ds_nome) LIKE UPPER('".utf8_decode($ds_busca)."%')
								 ORDER BY nome	
								 ) n
								 
						   LEFT JOIN (SELECT UPPER(TRIM(p1.nome)) AS nome,
							                 p1.cd_empresa,
							                 p1.cd_registro_empregado,
							                 p1.seq_dependencia,
							                 NULL AS nr_rg,
							                 TRIM(TO_CHAR(p1.cpf_mf,'00000000000')) AS nr_cpf
							            FROM public.participantes p1
							           WHERE UPPER(p1.nome) LIKE UPPER('".utf8_decode($ds_busca)."%')) p
					         ON p.nome = n.nome	

					       LEFT JOIN (SELECT DISTINCT(UPPER(TRIM(v1.ds_nome))) AS nome,
							                 v1.cd_empresa,
							                 v1.cd_registro_empregado,
							                 v1.seq_dependencia,
							                 v1.nr_rg,
							                 TRIM(TO_CHAR(v1.nr_cpf,'00000000000')) AS nr_cpf
							            FROM projetos.visitantes v1
							           WHERE UPPER(v1.ds_nome) LIKE UPPER('".utf8_decode($ds_busca)."%')) v
					         ON v.nome = n.nome

					      RIGHT JOIN (SELECT UPPER(TRIM(p1.nome)) AS nome,
							                 MAX(p1.cd_registro_empregado) AS cd_registro_empregado
							            FROM public.participantes p1
							           WHERE UPPER(p1.nome) LIKE UPPER('".utf8_decode($ds_busca)."%')
					                   GROUP BY nome) p2
					         ON p2.nome                  = n.nome	
					        AND p2.cd_registro_empregado = p.cd_registro_empregado

					      RIGHT JOIN (SELECT UPPER(TRIM(p1.nome)) AS nome,
							                 MAX(p1.seq_dependencia) AS seq_dependencia
							            FROM public.participantes p1
							           WHERE UPPER(p1.nome) LIKE UPPER('".utf8_decode($ds_busca)."%')
					                   GROUP BY nome) p3
					         ON p3.nome            = n.nome	
					        AND p3.seq_dependencia = p.seq_dependencia

					      GROUP BY n.nome,
						           p.cd_empresa,
						           p.cd_registro_empregado,
						           p.seq_dependencia,
					               COALESCE(p.nr_cpf,v.nr_cpf),
					               v.nr_rg	
						
						  UNION

						 SELECT DISTINCT(UPPER(TRIM(ap.ds_nome))) AS nome,
						        ap.cd_registro_empregado,
						        ap.cd_empresa,
						        ap.seq_dependencia,
						        TRIM(TO_CHAR(ap.nr_cpf,'00000000000')) AS nr_cpf,
						        ap.nr_rg
						   FROM projetos.visitantes ap
						  WHERE UPPER(ap.ds_nome) LIKE UPPER('".utf8_decode($ds_busca)."%')
						  ORDER BY nome	
						  	 
				     ";
		$ob_result = pg_query($db, $qr_select);
		$ar_reg = pg_fetch_array($ob_result);
		echo "setDados('".$ar_reg['cd_empresa']."', '".$ar_reg['cd_registro_empregado']."', '".$ar_reg['seq_dependencia']."', '".$ar_reg['nr_rg']."', '".$ar_reg['nr_cpf']."', document.getElementById('ds_nome').value);";
	}	
	
	#### BUSCA STRING NO CAMPO ####
	function buscaString($ds_busca,$ds_campo)
	{
		global $db;
		$qr_select = "
						SELECT DISTINCT(TRIM(UPPER(TRIM(REPLACE(REPLACE(".$ds_campo.",'\n',''),'\r',''))))) AS ds_campo
						  FROM projetos.visitantes
						 WHERE UPPER(".$ds_campo.") LIKE UPPER('".str_replace('º','_',utf8_decode($ds_busca))."%')
						 ORDER BY ds_campo
						 LIMIT 10						 
				     ";
		$ob_result = pg_query($db, $qr_select);
		$lt_valor = "";
		while ($ar_reg = pg_fetch_array($ob_result)) 
		{
			$lt_valor.= '"'.$ar_reg['ds_campo'].'",';
		}
		echo $lt_valor;
		echo '""';
	}	
	
	#### BUSCA NOME PARA DAR SAIDA ####
	function buscaNomeSaida($ds_busca)
	{
		global $db;
		$qr_select = "
						SELECT DISTINCT(UPPER(TRIM(ap.ds_nome))) AS nome
						  FROM projetos.visitantes ap
						 WHERE UPPER(ap.ds_nome) LIKE UPPER('".utf8_decode($ds_busca)."%')
						   AND ap.dt_saida IS NULL
				     ";
		$ob_result = pg_query($db, $qr_select);
		while ($ar_reg = pg_fetch_array($ob_result)) 
		{
			echo '"'.$ar_reg['nome'].'",';
		}
		echo '""';
	}	
	
	#### BUSCA PROCEDENCIA PARA DAR SAIDA ####
	function buscaProcedenciaSaida($ds_busca)
	{
		global $db;
		$qr_select = "
						SELECT DISTINCT(UPPER(TRIM(ap.ds_origem))) AS nome
						  FROM projetos.visitantes ap
						 WHERE UPPER(ap.ds_origem) LIKE UPPER('".utf8_decode($ds_busca)."%')
						   AND ap.dt_saida IS NULL
				     ";
		$ob_result = pg_query($db, $qr_select);
		while ($ar_reg = pg_fetch_array($ob_result)) 
		{
			echo '"'.$ar_reg['nome'].'",';
		}
		echo '""';
	}	
	
	#### MARCA SAÍDA CLIQUE ####
	function marcaSaida($cd_acesso)
	{
		global $db;
		// ---> ABRE TRANSACAO COM O BD <--- //
		pg_query($db,"BEGIN TRANSACTION");			
		$qr_update = "
						UPDATE projetos.visitantes
						   SET dt_saida     = CURRENT_TIMESTAMP
						 WHERE cd_visitante = ".$cd_acesso."
				     ";
		$ob_resul = @pg_query($db,$qr_update);
		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			// ---> DESFAZ A TRANSACAO COM BD<--- //
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			echo $ds_erro;
		}
		else
		{
			// ---> COMITA DADOS NO BD <--- //
			pg_query($db,"COMMIT TRANSACTION"); 
		}	
	}
	
	#### MARCA SAÍDA CRACHA ####
	function marcaSaidaCracha($nr_cracha)
	{
		global $db;
		// ---> ABRE TRANSACAO COM O BD <--- //
		pg_query($db,"BEGIN TRANSACTION");			

		$qr_select = "
						SELECT COUNT(*) AS qt_reg
						  FROM projetos.visitantes 
						 WHERE nr_cracha = ".$nr_cracha."
						   AND dt_saida  IS NULL
				     ";
		$ob_resul = @pg_query($db,$qr_select);	
		$ar_reg = pg_fetch_array($ob_resul);
		
		$qr_update = "
						UPDATE projetos.visitantes 
						   SET dt_saida  = CURRENT_TIMESTAMP
						 WHERE nr_cracha = ".$nr_cracha."
						   AND dt_saida  IS NULL
				     ";
		$ob_resul = @pg_query($db,$qr_update);
		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			// ---> DESFAZ A TRANSACAO COM BD<--- //
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			echo $ds_erro;
		}
		else
		{
			echo "QT_REG ".$ar_reg['qt_reg'];
			// ---> COMITA DADOS NO BD <--- //
			pg_query($db,"COMMIT TRANSACTION"); 
		}	
	}	
	
	#### MARCA SAÍDA NOME ####
	function marcaSaidaNome($ds_nome)
	{
		global $db;
		// ---> ABRE TRANSACAO COM O BD <--- //
		pg_query($db,"BEGIN TRANSACTION");	

		$qr_select = "
						SELECT COUNT(*) AS qt_reg
						  FROM projetos.visitantes 
						 WHERE UPPER(ds_nome)  = UPPER('".utf8_decode($ds_nome)."')
						   AND dt_saida  IS NULL
				     ";
		$ob_resul = @pg_query($db,$qr_select);	
		$ar_reg = pg_fetch_array($ob_resul);		

		$qr_update = "
						UPDATE projetos.visitantes 
						   SET dt_saida = CURRENT_TIMESTAMP
						 WHERE UPPER(ds_nome)  = UPPER('".utf8_decode($ds_nome)."')
						   AND dt_saida IS NULL
				     ";
		$ob_resul = @pg_query($db,$qr_update);
		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			// ---> DESFAZ A TRANSACAO COM BD<--- //
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			echo $ds_erro;
		}
		else
		{
			echo "QT_REG ".$ar_reg['qt_reg'];
			// ---> COMITA DADOS NO BD <--- //
			pg_query($db,"COMMIT TRANSACTION"); 
		}
	}

	#### MARCA SAÍDA PROCEDENCIA ####
	function marcaSaidaProcedencia($ds_origem)
	{
		global $db;
		// ---> ABRE TRANSACAO COM O BD <--- //
		pg_query($db,"BEGIN TRANSACTION");		

		$qr_select = "
						SELECT COUNT(*) AS qt_reg
						  FROM projetos.visitantes 
						 WHERE UPPER(ds_origem)  = UPPER('".utf8_decode($ds_origem)."')
						   AND dt_saida  IS NULL
				     ";
		$ob_resul = @pg_query($db,$qr_select);	
		$ar_reg = pg_fetch_array($ob_resul);		

		$qr_update = "
						UPDATE projetos.visitantes 
						   SET dt_saida = CURRENT_TIMESTAMP
						 WHERE UPPER(ds_origem)  = UPPER('".utf8_decode($ds_origem)."')
						   AND dt_saida IS NULL
				     ";
		$ob_resul = @pg_query($db,$qr_update);
		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			// ---> DESFAZ A TRANSACAO COM BD<--- //
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			echo $ds_erro;
		}
		else
		{
			echo "QT_REG ".$ar_reg['qt_reg'];
			// ---> COMITA DADOS NO BD <--- //
			pg_query($db,"COMMIT TRANSACTION"); 
		}
	}	
	
	#### LISTA MOVIMENTO DENTRO DA FUNDACAO ####
	function buscaMovimento()
	{
		global $db;
		echo '	<BR>
				<table class="tb_lista_resultado">
					<tr>
						<th>
							Crachá
						</th>
						<th>
							RE
						</th>						
						<th>
							Nome
						</th>		
						<th>
							Entrada
						</th>
						<th>
							Procedência
						</th>						
						<th>
							Destino
						</th>						
						<th>
							Editar
						</th>						
					</tr>
			';
		$qr_select = "
					SELECT cd_visitante,
					       cd_registro_empregado,
					       (CASE WHEN nr_cracha IS NULL 
                                 THEN ' - '
                                 ELSE TO_CHAR(nr_cracha, '9999999999')
                           END) AS nr_cracha,
					       TO_CHAR(dt_entrada,'DD/MM/YYYY HH24:MI:SS') AS dt_entra,
						   cd_tipo_visita || ' - ' || ds_origem AS ds_origem,
					       ds_nome,
						   UPPER(ds_destino) AS ds_destino
					  FROM projetos.visitantes
					 WHERE dt_saida IS NULL
                     ORDER BY dt_entrada					 
				     ";
		$ob_result = pg_query($db, $qr_select);	
		$nr_conta  = 0;
		while ($ar_reg = pg_fetch_array($ob_result)) 
		{		
			if(($nr_conta % 2) != 0)
			{
				$bg_color = '#F4F4F4';
			}
			else
			{
				$bg_color = '#FFFFFF';		
			}			
			$js_saida = 'title="Clique para marcar saída" onclick="marcaSaida(\''.$ar_reg['cd_visitante'].'\');"';
			$js_editar = 'title="Clique para editar" onclick="editarEntrada(\''.$ar_reg['cd_visitante'].'\');"';
			echo '			
					<tr bgcolor="'.$bg_color.'" onmouseover="this.className=\'tb_resultado_selecionado\';" onmouseout="this.className=\'\';">
						<td style="white-space:nowrap;text-align:center;" '.$js_saida.'>
							'.trim($ar_reg['nr_cracha']).'
						</td>
						<td style="white-space:nowrap;text-align:center;" '.$js_saida.'>
							'.$ar_reg['cd_registro_empregado'].'
						</td>						
						<td style="white-space:nowrap;" '.$js_saida.'>
							'.$ar_reg['ds_nome'].'
						</td>						
						<td style="white-space:nowrap;text-align:center;" '.$js_saida.'>
							'.$ar_reg['dt_entra'].'
						</td>								
						<td style="white-space:nowrap;" '.$js_saida.'>
							'.$ar_reg['ds_origem'].'
						</td>						
						<td style="white-space:nowrap;" '.$js_saida.'>
							'.$ar_reg['ds_destino'].'
						</td>						
						<td style="white-space:nowrap;text-align:center;" '.$js_editar.'>
							<img src="img/visitante_edt.png" border="0" >
						</td>						
					</tr>	
				';
			$nr_conta++;				
		}
		echo '	</table>';
	}
	
	
	#### LISTA MOVIMENTO SAIRAM DA FUNDACAO ####
	function buscaMovimentoSaida()
	{
		global $db;
		echo '	<BR>
				<table class="tb_lista_resultado">
					<tr>
						<th>
							Crachá
						</th>
						<th>
							RE
						</th>						
						<th>
							Nome
						</th>		
						<th>
							Entrada
						</th>
						<th>
							Permanência
						</th>
						<th>
							Procedência
						</th>						
						<th>
							Destino
						</th>						
						<th>
							Editar
						</th>						
					</tr>
			';
		$qr_select = "
					SELECT cd_visitante,
					       cd_registro_empregado,
					       (CASE WHEN nr_cracha IS NULL 
                                 THEN ' - '
                                 ELSE TO_CHAR(nr_cracha, '9999999999')
                           END) AS nr_cracha,
					       TO_CHAR(dt_entrada,'HH24:MI:SS') AS dt_entra,
						   cd_tipo_visita || ' - ' || ds_origem AS ds_origem,
					       ds_nome,
						   UPPER(ds_destino) AS ds_destino,
						   (dt_saida - dt_entrada) AS hr_tempo
					  FROM projetos.visitantes
					 WHERE DATE_TRUNC('day',dt_saida) = CURRENT_DATE
                     ORDER BY dt_entrada					 
				     ";
		$ob_result = pg_query($db, $qr_select);	
		$nr_conta  = 0;
		while ($ar_reg = pg_fetch_array($ob_result)) 
		{		
			if(($nr_conta % 2) != 0)
			{
				$bg_color = '#F4F4F4';
			}
			else
			{
				$bg_color = '#FFFFFF';		
			}
			
			$js_editar = 'title="Clique para editar" onclick="editarEntrada(\''.$ar_reg['cd_visitante'].'\');"';			
			echo '			
					<tr bgcolor="'.$bg_color.'" onmouseover="this.className=\'tb_resultado_selecionado\';" onmouseout="this.className=\'\';">
						<td style="white-space:nowrap;text-align:center;">
							'.trim($ar_reg['nr_cracha']).'
						</td>
						<td style="white-space:nowrap;text-align:center;">
							'.$ar_reg['cd_registro_empregado'].'
						</td>						
						<td style="white-space:nowrap;">
							'.$ar_reg['ds_nome'].'
						</td>						
						<td style="white-space:nowrap;text-align:center;">
							'.$ar_reg['dt_entra'].'
						</td>								
						<td style="white-space:nowrap;text-align:center;">
							'.$ar_reg['hr_tempo'].'
						</td>	
						<td style="white-space:nowrap;">
							'.$ar_reg['ds_origem'].'
						</td>						
						<td style="white-space:nowrap;" >
							'.$ar_reg['ds_destino'].'
						</td>						
						<td style="white-space:nowrap;text-align:center;" '.$js_editar.'>
							<img src="img/visitante_edt.png" border="0" >
						</td>						
					</tr>	
				';
			$nr_conta++;				
		}
		echo '	</table>';
	}	
?>