<?php
	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();
	include_once('auto_atendimento_monta_sessao.php');
	$ds_arq   = "tpl/tpl_auto_atendimento_participante.html";
	$ob_arq   = fopen($ds_arq, 'r');
	$conteudo = fread($ob_arq, filesize($ds_arq));
	fclose($ob_arq);	


	
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
					   'PARTICIPANTE'
					 )
		      ";
	@pg_query($db,$qr_sql);   

	/*
	$qr_sql = "
				SELECT CASE WHEN projetos.participante_tipo(".$_SESSION['EMP'].", ".$_SESSION['RE'].",".$_SESSION['SEQ'].") IN('APOS', 'EXAU', 'CTP')
							 AND CURRENT_TIMESTAMP <= TO_TIMESTAMP('31/12/2012 23:59:59','DD/MM/YYYY HH24:MI:SS')
							THEN 'S'
							ELSE 'N'
					   END AS fl_banner
	          ";
	$ob_res = @pg_query($db,$qr_sql);
	$ar_reg = pg_fetch_array($ob_res);
	
	*/
	
	$ar_reg['fl_banner'] = "S";
	#$banner_fixo = (($ar_reg['fl_banner'] == "S") ? '<img src="i/app/bannerapp.png" border="0" usemap="#bannerapp"><map name="bannerapp"><area shape="rect" coords="107, 107, 214, 139" alt="Download Android" title="Download Android" target="_blank" href="http://fceee.com.br/android"><area shape="rect" coords="243, 107, 350, 139" alt="Download iOS" title="Download iOS" target="_blank" href="http://fceee.com.br/ios"></map>' : "");
	
	#### BANNER FIXO ####
	$conteudo = str_replace('{BANNER_FIXO}', $banner_fixo, $conteudo);
	
	#### OPCAO ENVIO ####
	$qr_sql = "
				SELECT pog.cd_opcao
				  FROM public.participantes_opcoes_grupos pog
				  JOIN public.grupos g
				    ON g.cd_grupo = pog.cd_grupo
				 WHERE pog.cd_empresa            = ".$_SESSION['EMP']."
				   AND pog.cd_registro_empregado = ".$_SESSION['RE']."
				   AND pog.seq_dependencia       = ".$_SESSION['SEQ']."
				   AND (
						(
					      pog.dt_fim_sistema IS NOT NULL 
						  AND CAST(pog.dt_fim_sistema AS DATE) BETWEEN CURRENT_DATE AND last_day(CURRENT_DATE) 
						  AND CAST(pog.dt_fim_sistema AS DATE) <> CAST(pog.dt_inicio_sistema AS DATE)
						 ) 
						 OR 
						 (
						  pog.dt_fim_sistema IS NULL 
						  AND CAST(pog.dt_inicio_sistema AS DATE) <= CURRENT_DATE
						 )
					   )
                
				 UNION 	
				
				SELECT pog.cd_opcao
				  FROM public.participantes_opcoes_grupos pog
				  JOIN public.grupos g
				    ON g.cd_grupo = pog.cd_grupo
				 WHERE pog.cd_empresa            = ".$_SESSION['EMP']."
				   AND pog.cd_registro_empregado = ".$_SESSION['RE']."
				   AND pog.seq_dependencia       = ".$_SESSION['SEQ']."
				   AND pog.dt_inicio_sistema::DATE > CURRENT_DATE
				   AND pog.dt_fim_sistema IS NULL	
	          ";
	$ob_resul = pg_query($db,$qr_sql);
	$fl_impresso = false;
	if(pg_num_rows($ob_resul) == 1)
	{
		$ar_reg = pg_fetch_array($ob_resul);
		
		if((intval($ar_reg["cd_opcao"]) == 2) AND ($_SESSION['TIPO_EMPRESA'] == "P"))
		{
			$fl_impresso = true;
		}
	}
	
    #echo var_dump($fl_impresso); exit;
	$opcao_eletronica = "";
	if($fl_impresso)
	{
		$opcao_eletronica = '
							<style>		                        
							.btOpcaoEnvio {
								width: 670px;
								vertical-align: top;
								background-color: #4D90FE;
								background-image: -moz-linear-gradient(center top , #4D90FE, #4787ED);
								border: 1px solid #3079ED;
								color: #FFFFFF !important;
								margin: 0;
								-moz-user-select: none;
								border-radius: 2px 2px 2px 2px;
								cursor: pointer;
								display: inline-block;
								
								height: 29px;
								line-height: 29px;
								min-width: 54px;
								padding: 0 8px;
								text-align: center;
								text-decoration: none !important;
								font-family: Verdana, Arial, Helvetica, sans-serif;
								font-weight: bold;
								font-size: 10pt;
							}								
							</style>	
							<center>
								<div class="btOpcaoEnvio" onclick="location=\'auto_atendimento_opcao_envio_doc.php\';">
									<span class="gbqfi">Clique aqui e faça a opção pelo recebimento ELETRÔNICO</span>
								</div>
							</center>
							<BR>
								<!--
								<table border="0" width="100%" class="sort-table" style="border: 1px solid #3079ED;">  
								<tr style="background:#4C8FFC; " align="center" >
		                            <td height="20" colspan="2" onclick="location=\'auto_atendimento_opcao_envio_doc.php\';" style="cursor:pointer;">
										<font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">
										<b>
											Clique aqui e faça a opção pelo recebimento ELETRÔNICO
										</b>
										</font>
									</td>
		                        </tr>
								</table>
								-->
								
		                      ';
	}
	$conteudo = str_replace('{opcao_eletronica}', $opcao_eletronica, $conteudo);	
	
   
   
	$sql = "
				select p.cd_empresa, 
                       pa.nome_empresa, 
					   UPPER(pa.tipo_cliente) AS tipo_empresa,
					   p.nome, 
					   p.cd_registro_patroc,
					   TO_CHAR(p.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
					   TO_CHAR(p.dt_obito,'DD/MM/YYYY') AS dt_obito, 
					   TO_CHAR(p.dt_recadastramento,'DD/MM/YYYY') AS dt_recadastramento, 
					   p.quant_dep_economico, 
					   funcoes.format_cpf(TO_CHAR(p.cpf_mf,'FM00000000000')) AS cpf_mf,
					   CASE WHEN UPPER(p.sexo) = 'M' THEN 'Masculino' 
							WHEN UPPER(p.sexo) = 'F' THEN 'Feminino' 
					   END AS sexo, 
					   (SELECT MAX(TO_CHAR(b.data_inicio, 'DD/MM/YYYY'))
					      FROM beneficios b
                         WHERE b.tifo_tipo_folha            = p.tipo_folha
                           AND b.part_cd_empresa            = p.cd_empresa
                           AND b.part_cd_registro_empregado = p.cd_registro_empregado
                           AND b.part_seq_dependencia       = p.seq_dependencia) AS dt_inicio_beneficio,
					   p.cd_estado_civil, 
					   ec.descricao_estado_civil AS estado_civil, 
					   p.cd_grau_de_instrucao, 
					   p.tipo_folha, 
					   g.descricao_grau_instrucao, 
					   p.cd_instituicao, 
					   p.cd_agencia, 
					   p.conta_folha, 
					   p.cd_instituicao_pode_ter_conta_, 
					   p.cd_agencia_pode_ter_conta_debi, 
					   p.conta_debitos, 
					   p.logradouro,
					   p.endereco,
					   p.nr_endereco,
					   p.complemento_endereco,
					   p.bairro, 
					   p.cidade, 
					   p.unidade_federativa, 
					   p.cep, 
					   TRIM(TO_CHAR(p.complemento_cep,'000')) AS complemento_cep, 
					   TRIM(TO_CHAR(COALESCE(p.ddd,0),'000')) AS ddd, 
					   p.telefone, 
					   p.ramal, 
					   TRIM(TO_CHAR(COALESCE(p.ddd_celular,0),'000')) AS ddd_celular,
					   p.celular,
					   p.email, 
					   p.email_profissional,
					   p.bloqueio_ender, 
					   p.opcao_ir,
					   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
                       (SELECT COUNT(*) 
					      FROM public.dependentes d1 
						 WHERE d1.cd_empresa            = p.cd_empresa 
						   AND d1.cd_registro_empregado = p.cd_registro_empregado 
						   AND dt_desligamento          IS NULL) AS num_dep, ";   
    if ($_SESSION['SEQ'] == 0) 
	{
		$sql.= "  
		               ce.descricao_categoria_eletro, 
					   TO_CHAR(t.dt_solicitacao,'DD/MM/YYYY') AS dt_solicitacao, 
					   TO_CHAR(t.dt_admissao,'DD/MM/YYYY') AS dt_admissao, 
					   TO_CHAR(t.dt_ingresso_eletro,'DD/MM/YYYY') AS dt_ingresso_eletro, 
					   t.cd_categoria_eletro, 
			   ";	  
	}
	$sql.= " 
	                   tf.descricao_folha, 
	                   if.razao_social_nome 
	              FROM public.participantes p 
	              JOIN public.patrocinadoras pa
				    ON pa.cd_empresa = p.cd_empresa 
				  JOIN public.tipo_folhas tf
					ON tf.tipo_folha = p.tipo_folha 
	       ";
	if ($_SESSION['SEQ'] == 0) 
	{
		$sql.= "
				  JOIN public.titulares t
				    ON t.cd_empresa            = p.cd_empresa 
	               AND t.cd_registro_empregado = p.cd_registro_Empregado
	               AND t.seq_dependencia       = p.seq_dependencia
			      JOIN public.categoria_eletros ce
				    ON ce.cd_categoria_eletro  = t.cd_categoria_eletro
		       ";
	}
	$sql.= "
	              LEFT JOIN public.grau_instrucaos g
                    ON g.cd_grau_de_instrucao = p.cd_grau_de_instrucao 
				  LEFT JOIN public.estado_civils ec
                    ON ec.cd_estado_civil = p.cd_estado_civil				  
	              LEFT JOIN public.instituicao_financeiras if 
				  	ON if.cd_instituicao = p.cd_instituicao 
				   AND if.cd_agencia     = '0' 
	             WHERE p.cd_empresa            = ".$_SESSION['EMP']."
				   AND p.cd_registro_empregado = ".$_SESSION['RE']."
				   AND p.seq_dependencia       = ".$_SESSION['SEQ']."
	               
	       ";
	#ECHO "<!-- SQL => <PRE>".$sql."</PRE>-->";
	#echo "<PRE>$sql</PRE>"; exit;
   
	$rs  = pg_query($db,$sql);
	$reg = pg_fetch_array($rs);
   
	$cd_grau_de_instrucao = intval($reg['cd_grau_de_instrucao']);
   
	#echo "<PRE>".print_r($reg,true)."</PRE>"; #exit;
	
	#### DADOS DA IDENTIFICAÇÃO DE ACORDO COM TIPO DA EMPRESA ####
	$ds_arq                 = "tpl/tpl_auto_atendimento_participante_".$reg['tipo_empresa'].".html";
	$ob_arq                 = fopen($ds_arq, 'r');
	$conteudo_identificacao = fread($ob_arq, filesize($ds_arq));
	fclose($ob_arq);	

	#### ATALHO CONTRACHEQUE ####
		#### VERIFICA RECEBIMENTO RISCO INVALIDEZ #####
		$fl_recebe_risco = false;
		if((in_array($_SESSION['EMP'],array(7,8,10))) and (in_array($reg['tipo_folha'],array(17,18))))
		{
			$qr_afa = "
						SELECT COUNT(*) AS fl_risco
						  FROM public.afastados a
						 WHERE a.cd_empresa            = ".$_SESSION['EMP']."
						   AND a.cd_registro_empregado = ".$_SESSION['RE']."
						   AND a.seq_dependencia       = ".$_SESSION['SEQ']."
						   AND a.tipo_afastamento      = 96
						   AND ((a.dt_final_afastamento IS NULL) OR (DATE_TRUNC('month', a.dt_final_afastamento) >= DATE_TRUNC('month',CURRENT_DATE))) 		
					  ";
			$ob_afa = pg_query($db, $qr_afa);
			$reg_afa = pg_fetch_array($ob_afa);	
			
			if(intval($reg_afa["fl_risco"]) > 0)
			{
				$fl_recebe_risco = true;
			}
		}	
	
	$atalho_contracheque = "";
	if((trim($reg['dt_inicio_beneficio']) != "") or ($fl_recebe_risco))
	{
		$atalho_contracheque = '
							<style>		                        
							.btAtalhoContracheque {
								width: 670px;
								vertical-align: top;
								background-color: #FF8F49;
								background-image: -moz-linear-gradient(center top , #FFB587, #FF954F);
								border: 1px solid #FF6A00;
								color: #FFFFFF !important;
								margin: 0;
								-moz-user-select: none;
								border-radius: 2px 2px 2px 2px;
								cursor: pointer;
								display: inline-block;
								
								height: 29px;
								line-height: 29px;
								min-width: 54px;
								padding: 0 8px;
								text-align: center;
								text-decoration: none !important;
								font-family: Verdana, Arial, Helvetica, sans-serif;
								font-weight: bold;
								font-size: 10pt;
							}								
							</style>	
							<center>
								<div class="btAtalhoContracheque" onclick="location=\'auto_atendimento_contra_cheque.php\';">
									<span class="gbqfi">Clique aqui para ver o seu CONTRACHEQUE</span>
								</div>
							</center>
							<BR>
		                      ';
	}
	$conteudo = str_replace('{atalho_contracheque}', $atalho_contracheque, $conteudo);
	
	$atalho_fatca = '
						<style>		                        
						.btAtalhoFatca {
							width: 670px;
							vertical-align: top;
							background-color: #FF002A;
							background-image: -moz-linear-gradient(center top , #FF2A2A, #FF5555);
							border: 1px solid #FF6A00;
							color: #FFFFFF !important;
							margin: 0;
							-moz-user-select: none;
							border-radius: 2px 2px 2px 2px;
							cursor: pointer;
							display: inline-block;
							
							height: 29px;
							line-height: 29px;
							min-width: 54px;
							padding: 0 8px;
							text-align: center;
							text-decoration: none !important;
							font-family: Verdana, Arial, Helvetica, sans-serif;
							font-weight: bold;
							font-size: 10pt;
						}								
						</style>	
						<center>
							<div class="btAtalhoFatca" onclick="location=\'auto_atendimento_fatca.php\';">
								<span class="gbqfi">Clique aqui para responder o formulário FATCA</span>
							</div>
						</center>
						<BR>';
	
	$conteudo = str_replace('{atalho_fatca}', $atalho_fatca, $conteudo);

	$conteudo_identificacao = str_replace('{empresa}', $_SESSION['EMP'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{registro}', $_SESSION['RE'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{sequencia}', $_SESSION['SEQ'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{nome_empresa}', $reg['nome_empresa'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{nome}', $reg['nome'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{dt_adm}', $reg['dt_admissao'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{dt_solicitacao}', $reg['dt_solicitacao'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{dt_ingresso}', $reg['dt_ingresso_eletro'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{dt_recadastramento}', $reg['dt_recadastramento'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{cat_func}', $reg['descricao_categoria_eletro'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{num_dep}', $reg['num_dep'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{dt_nasc}', $reg['dt_nascimento'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{cpf}', $reg['cpf_mf'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{sexo}', $reg['sexo'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{ini_beneficio}', $reg['dt_inicio_beneficio'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{est_civil}', $reg['estado_civil'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{grau_inst}', $reg['descricao_grau_instrucao'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{tp_folha}', $reg['descricao_folha'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{banco}', $reg['cd_instituicao'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{agencia}', $reg['cd_agencia'], $conteudo_identificacao);
	$conteudo_identificacao = str_replace('{conta_folha}', $reg['conta_folha'], $conteudo_identificacao);

	$opcao_ir = '';

	if(trim($reg['opcao_ir']) == 1)
	{
		$opcao_ir = 'Tabela Regressiva';
	}
	else if(trim($reg['opcao_ir']) == 2)
	{
		$opcao_ir = 'Tabela Progressiva';
	}

	$conteudo = str_replace('{LISTA_IR}', $opcao_ir, $conteudo);
		
	$conteudo = str_replace('{DADOS_IDENTIFICACAO}', $conteudo_identificacao, $conteudo);
	
	
	$endereco_bloqueado = "";
	if($reg['bloqueio_ender'] == 'S')
	{
		$endereco_bloqueado = '
		                        <tr style="background:red;" align="center">
		                            <td height="20" colspan="2">
										<font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#ffffff">
										<strong>
											ENDEREÇO BLOQUEADO
										</strong>
										</font>
									</td>
		                        </tr>	
		                      ';
	}
	$conteudo = str_replace('{endereco_bloqueado}', $endereco_bloqueado, $conteudo);
	
	$conteudo = str_replace('{logradouro}', $reg['logradouro'], $conteudo);
	$conteudo = str_replace('{endereco}', $reg['endereco'], $conteudo);
	$conteudo = str_replace('{nr_endereco}', $reg['nr_endereco'], $conteudo);
	$conteudo = str_replace('{complemento_endereco}', $reg['complemento_endereco'], $conteudo);
	$conteudo = str_replace('{bairro}', $reg['bairro'], $conteudo);
	$conteudo = str_replace('{cidade}', $reg['cidade'], $conteudo);
	$conteudo = str_replace('{uf}', $reg['unidade_federativa'], $conteudo);
	$conteudo = str_replace('{cep}', $reg['cep'], $conteudo);
	$conteudo = str_replace('{complemento}', $reg['complemento_cep'], $conteudo);
	$conteudo = str_replace('{ddd}', $reg['ddd'], $conteudo);
	$conteudo = str_replace('{fone}', $reg['telefone'], $conteudo);
	$conteudo = str_replace('{ramal}', $reg['ramal'], $conteudo);
	$conteudo = str_replace('{ddd_celular}', $reg['ddd_celular'], $conteudo);
	$conteudo = str_replace('{celular}', $reg['celular'], $conteudo);	
	$conteudo = str_replace('{email}', $reg['email'], $conteudo);
	$conteudo = str_replace('{email_profissional}', $reg['email_profissional'], $conteudo);
	$conteudo = str_replace('{re_cripto}', $reg['re_cripto'], $conteudo);

	$estado = '
				<option '.(trim($reg['unidade_federativa']) == "AC" ? "selected" : "").' value="AC">ACRE</option>
				<option '.(trim($reg['unidade_federativa']) == "AL" ? "selected" : "").' value="AL">ALAGOAS</option>
				<option '.(trim($reg['unidade_federativa']) == "AP" ? "selected" : "").' value="AP">AMAPÁ</option>
				<option '.(trim($reg['unidade_federativa']) == "AM" ? "selected" : "").' value="AM">AMAZONAS</option>
				<option '.(trim($reg['unidade_federativa']) == "BA" ? "selected" : "").' value="BA">BAHIA</option>
				<option '.(trim($reg['unidade_federativa']) == "CE" ? "selected" : "").' value="CE">CEARÁ</option>
				<option '.(trim($reg['unidade_federativa']) == "DF" ? "selected" : "").' value="DF">DISTRITO FEDERAL</option>
				<option '.(trim($reg['unidade_federativa']) == "ES" ? "selected" : "").' value="ES">ESPÍRITO SANTO</option>
				<option '.(trim($reg['unidade_federativa']) == "GO" ? "selected" : "").' value="GO">GOIÁS</option>
				<option '.(trim($reg['unidade_federativa']) == "MA" ? "selected" : "").' value="MA">MARANHÃO</option>
				<option '.(trim($reg['unidade_federativa']) == "MT" ? "selected" : "").' value="MT">MATO GROSSO</option>
				<option '.(trim($reg['unidade_federativa']) == "MS" ? "selected" : "").' value="MS">MATO GROSSO DO SUL</option>
				<option '.(trim($reg['unidade_federativa']) == "MG" ? "selected" : "").' value="MG">MINAS GERAIS</option>
				<option '.(trim($reg['unidade_federativa']) == "PA" ? "selected" : "").' value="PA">PARÁ</option>
				<option '.(trim($reg['unidade_federativa']) == "PB" ? "selected" : "").' value="PB">PARAÍBA</option>
				<option '.(trim($reg['unidade_federativa']) == "PR" ? "selected" : "").' value="PR">PARANÁ</option>
				<option '.(trim($reg['unidade_federativa']) == "PE" ? "selected" : "").' value="PE">PERNAMBUCO</option>
				<option '.(trim($reg['unidade_federativa']) == "PI" ? "selected" : "").' value="PI">PIAUÍ</option>
				<option '.(trim($reg['unidade_federativa']) == "RJ" ? "selected" : "").' value="RJ">RIO DE JANEIRO</option>
				<option '.(trim($reg['unidade_federativa']) == "RN" ? "selected" : "").' value="RN">RIO GRANDE DO NORTE</option>
				<option '.(trim($reg['unidade_federativa']) == "RS" ? "selected" : "").' value="RS">RIO GRANDE DO SUL</option>
				<option '.(trim($reg['unidade_federativa']) == "RO" ? "selected" : "").' value="RO">RONDÔNIA</option>
				<option '.(trim($reg['unidade_federativa']) == "RR" ? "selected" : "").' value="RR">RORAIMA</option>
				<option '.(trim($reg['unidade_federativa']) == "SC" ? "selected" : "").' value="SC">SANTA CATARINA</option>
				<option '.(trim($reg['unidade_federativa']) == "SP" ? "selected" : "").' value="SP">SÃO PAULO</option>
				<option '.(trim($reg['unidade_federativa']) == "SE" ? "selected" : "").' value="SE">SERGIPE</option>
				<option '.(trim($reg['unidade_federativa']) == "TO" ? "selected" : "").' value="TO">TOCANTINS</option>
		      '; 
	$conteudo = str_replace('{LISTA_UF}', $estado, $conteudo);
	
	
	include('auto_atendimento_mensagem.php');
	if(trim($DS_MSG) != "")
	{
		$DS_MSG = "
					<table border='0' width='100%'>
						<tr>
							<td width='100%'>
								".$DS_MSG."
							</td>
						</tr>					
					</table>
				  ";
	}
	$conteudo = str_replace('{aviso_autoatendimento}', $DS_MSG, $conteudo);
	
	#### GRAU DE INSTRUÇÃO ####
	$qr_sql = "
				SELECT cd_grau_de_instrucao,
					   descricao_grau_instrucao,
					   CASE WHEN cd_grau_de_instrucao = ".intval($cd_grau_de_instrucao)." THEN 'selected' ELSE '' END fl_grau_de_instrucao
				  FROM public.grau_instrucaos
				 WHERE 1 = 1
				 ".(intval($cd_grau_de_instrucao) == 0 ? "AND cd_grau_de_instrucao <> 1" : "")."
				 ".(intval($cd_grau_de_instrucao) > 0  ? "AND cd_grau_de_instrucao > 0" : "")."
	          ";
	$ob_resul = pg_query($db,$qr_sql);
	$grau_de_instrucao = '<select name="cd_grau_de_instrucao" id="cd_grau_de_instrucao" onchange="setGrauDeInstrucao()">';
	while($ar_reg = pg_fetch_array($ob_resul))
	{
		$grau_de_instrucao.= '<option '.$ar_reg['fl_grau_de_instrucao'].' value="'.$ar_reg['cd_grau_de_instrucao'].'">'.$ar_reg['descricao_grau_instrucao'].'</option>';
	}
	$grau_de_instrucao.= '</select>';
	$conteudo = str_replace('{cd_grau_de_instrucao_old}', intval($cd_grau_de_instrucao), $conteudo);
	$conteudo = str_replace('{grau_de_instrucao}', $grau_de_instrucao, $conteudo);
	
	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();
?>