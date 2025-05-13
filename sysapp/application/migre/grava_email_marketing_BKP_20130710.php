<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
    include_once('inc/class.Email.inc.php');
	$txt_dt_inclusao  = ( $dt_inclusao  == '' ? 'Null' : "'".convdata_br_iso($dt_inclusao)."'" );
// ------------------------------------------------- 
	if (isset($sel_cidades)) {
		foreach ( $sel_cidades as $cidade ) {
			if ($v_sel_cidade == '') { 
				$v_sel_cidade = "'".$cidade."'"; 
			} else {
				$v_sel_cidade = $v_sel_cidade . ", "."'".$cidade."'";
			}
		}
	}
	
// ------------------------------------------------- 
	if ($insere=='I') 
	{
		$sql = " 
				INSERT INTO projetos.divulgacao ( 
							cd_divulgacao, 
							assunto, 
							conteudo, 
							dt_divulgacao, 
							cd_usuario,	
							divisao,
							tipo_divulgacao,
							email_avulsos,
                            remetente,
							url_link
							) 
				 VALUES ( 
							$cd_divulgacao, 
							'$assunto', 
							'$conteudo', 
							$txt_dt_inclusao, 
							$Z, 
							'$D',
							'E',
							'$emails_outros',
							TRIM('".$_POST['remetente']."'),
							TRIM('".$_POST['url_link']."')
						) ";
	}
	else 
	{
		$sql = " 
				UPDATE projetos.divulgacao 
				   SET assunto       = '$assunto', 
					   conteudo      = '$conteudo', 
					   dt_divulgacao = $txt_dt_inclusao, 
					   cd_usuario    = $Z, 
					   email_avulsos = '$emails_outros',
                       remetente     = TRIM('".$_POST['remetente']."'),							 
                       url_link      = TRIM('".$_POST['url_link']."')							 
				 WHERE cd_divulgacao = $cd_divulgacao 
			   ";
	}
// -------------------------------------------------
//echo $sql;
	if ($rs=pg_query($db, $sql)) 
	{
//echo 'ENV:'. $enviar;
		if ($enviar == 'S') {
			if ($chk_publicacao == 1) {
				$m = envia_email_cenario_legal($edicao, $db, $cd_divulgacao);
				pg_close($db);		
				header('location: lst_email_marketing.php');
			}
			elseif ($chk_publicacao == 3) {
				$msg = monta_boletim_informativo($edicao, $db);
				$conteudo = $conteudo . $msg; 
			}
		}
	}
	else {
		pg_close($db);
		header('location: lst_email_marketing.php?msg=Ocorreu um erro ao tentar gravar o cargo.');
	}

	
	if (trim($dt_envio) == '') 
	{
		$dt_envio = " DEFAULT ";
	}
	else 
	{
		$dt_envio = " TO_DATE('".$dt_envio."','DD/MM/YYYY') ";
	}
	
	
	//-- excluir e regravar todas os públicos e publicacoes desta divulgacao...
	if ($enviar == 'S') {
		$sql = " delete from projetos.divulgacoes_publicos where cd_divulgacao = $cd_divulgacao ";
		$s = (pg_query($db, $sql));
		$sql = " delete from projetos.divulgacoes_publicacoes where cd_divulgacao = $cd_divulgacao ";
		$s = (pg_query($db, $sql));
		while(list($key, $value) = each($HTTP_POST_VARS)) 
		{ 
			$v_str = $key;
			if (strpos($v_str, "_publicacao") > 0) {
				$m = fnc_grava_divulgacoes_publicacoes($cd_divulgacao, $db, $value);
				$cd_publicacao = $value;
			}
			if (strpos($v_str, "_publico") > 0) {
				$m = fnc_grava_divulgacoes_publicos($cd_divulgacao, $db, $value);
				$m = fnc_grava_emails($cd_divulgacao, $db, $value, $cd_publicacao, $dt_envio, $arquivo, $conteudo, $assunto, $_SESSION['D'], $edicao, $v_sel_cidade);
			}
			if (substr_count($v_str, "emails_outros") > 0) {
				$m = fnc_grava_emails_avulsos($cd_divulgacao, $db, $value, $cd_publicacao, $dt_envio, $arquivo, $conteudo, $assunto, $D, $edicao);
			}
		} 
	}
//-----------------------------------------------------------------------------------------------
	pg_close($db);
	header('location: lst_email_marketing.php');
//-----------------------------------------------------------------------------------------------
/*
0;CIA ESTADUAL ENERGIA ELÉTRICA;1154
1;FUNDAÇÃO ELETROCEEE - ATIVO;43
2;PENSÃO;206										<=== Pensão
3;APOSENT. POR TEMPO DE SERVIÇO;165		<=== Aposentadoria
4;APOSENTADORIA POR INVALIDEZ;20		<===
6;CARÊNCIA;157		
8;OUTROS;8
11;RIO GRANDE ENERGIA - ATIVO;42
12;AES SUL - ATIVO;148
13;GERAÇÃO TÉRMICA - ATIVO;106
14;APOSENTADORIA PROPORCIONAL;11		<===
16;CIA RIOGRANDENSE DE MINERAÇÃO;30
17;INSTITUIDOR;24
20;APOSENTADORIA SALDADA;376			<===
30;INVALIDEZ SALDADA;29					<===
40;ANTECIPADA SALDADA;49				<===
45;PENSÃO SALDADA;39								<=== Pensão
50;SALDADO CTP;292						<===
65;INVALIDEZ CD;1						<===
*/
function fnc_grava_emails($cd_divulgacao, $db, $cd_publico, $cd_publicacao, $dt_envio, $arquivo, $conteudo, $assunto, $div_solicitante, $edicao, $v_sel_cidade) 
{
	$v_arquivo = "<a href='http://www.e-prev.com.br/controle_projetos/upload/" . $arquivo . "'>" . $arquivo . "</a>";
	if (isset($cd_divulgacao)) {
        
// ------------------------------------ Pessoas
		if ($cd_publico == "CS1Q") {
			$sqlp = "select  distinct(email_1) as email, email_2 as email_profissional, nome_pessoa as nome, nome_empresa_entidade 
			        from expansao.mailing where email_1 like '%@%'"  ;//and flag_confirmado = 'S' group by email_1, nome_pessoa, nome_empresa_entidade ";
			
			if($_POST['chk_publico_CS3W2'] == "CS3W")
			{
				$sqlp.= "
							AND TRIM(email_1) NOT IN (select TRIM(email) 
							                          from acs.seminario 
							                         where email like '%@%' and cd_seminario_edicao = 2 AND dt_exclusao IS NULL)
				        ";
			}			
			
			$sqlp.= " group by email_1, email_2, nome_pessoa, nome_empresa_entidade ";
		}
// ------------------------------------ Seminário 2006:	
		elseif ($cd_publico == "CS1W") {
			$sqlp = 	"select codigo, email, null as email_profissional,  nome, empresa from acs.seminario ";
			$sqlp = $sqlp. 	" where email like '%@%' and cd_seminario_edicao = 1";
		}
// ------------------------------------ Seminário 2007:	
		elseif ($cd_publico == "CS2W") 
		{
			$sqlp = 	"select s.codigo, s.email, null as email_profissional, s.nome_sem_acento AS nome, s.empresa 
			               from acs.seminario s";
			$sqlp = $sqlp. 	" where s.email like '%@%' and s.cd_seminario_edicao = 2 AND dt_exclusao IS NULL";
			
			if($_POST['chk_publico_CS4W2'] == "CS4W")
			{
				$sqlp.= "
						   AND 0 < (SELECT COUNT(*)
						              FROM acs.seminario_presente sp
						             WHERE sp.cd_barra = s.cd_barra)
				        ";
			}			
		}	
// ------------------------------------ Seminário 2008:	
		elseif ($cd_publico == "CS2Y") 
		{
			$sqlp = "
						SELECT s.codigo, 
						       s.email, 
							   NULL AS email_profissional, 
							   s.nome_sem_acento AS nome,
					           COALESCE(s.cd_empresa,9999) AS cd_empresa,   
					           COALESCE(s.cd_registro_empregado,0) AS cd_registro_empregado,
					           COALESCE(s.seq_dependencia,0) AS seq_dependencia
			              FROM acs.seminario s
						 WHERE s.email LIKE '%@%' 
						   AND s.cd_seminario_edicao = 3 
						   AND s.dt_exclusao IS NULL";
			
			if($_POST['chk_publico_CS4Y2'] == "CS4Y")
			{
				$sqlp.= "
						   AND 0 < (SELECT COUNT(*)
						              FROM acs.seminario_presente sp
						             WHERE sp.cd_barra = s.cd_barra)
				        ";
			}			
		}		
		
// ------------------------------------ Seminário 2009:	
		elseif ($cd_publico == "SE09") 
		{
			$sqlp = "
						SELECT s.codigo, 
						       s.email, 
							   NULL AS email_profissional, 
							   s.nome_sem_acento AS nome,
					           COALESCE(s.cd_empresa,9999) AS cd_empresa,   
					           COALESCE(s.cd_registro_empregado,0) AS cd_registro_empregado,
					           COALESCE(s.seq_dependencia,0) AS seq_dependencia
			              FROM acs.seminario s
						 WHERE s.email LIKE '%@%' 
						   AND s.cd_seminario_edicao = 4 
						   AND s.dt_exclusao IS NULL";
			
			if($_POST['chk_publico_SP09'] == "SP09")
			{
				$sqlp.= "
						   AND 0 < (SELECT COUNT(*)
						              FROM acs.seminario_presente sp
						             WHERE sp.cd_barra = s.cd_barra)
				        ";
			}			
		}	
		// ------------------------------------ Seminário 2010:	
		elseif ($cd_publico == "SE10") 
		{
			$sqlp = "
						SELECT s.codigo, 
						       s.email, 
							   NULL AS email_profissional, 
							   s.nome,
					           COALESCE(s.cd_empresa,9999) AS cd_empresa,   
					           COALESCE(s.cd_registro_empregado,0) AS cd_registro_empregado,
					           COALESCE(s.seq_dependencia,0) AS seq_dependencia,
							   MD5(CAST(s.codigo AS TEXT)) AS re_cripto
			              FROM acs.seminario s
						 WHERE s.email LIKE '%@%' 
						   AND s.cd_seminario_edicao = 5 
						   AND s.dt_exclusao IS NULL
						 ORDER BY dt_inclusao
				    ";
		}
		// ------------------------------------ Seminário 2010 Somente presentes:	
		elseif ($cd_publico == "EP10") 
		{
			$sqlp = "
						SELECT s.codigo, 
						       s.email, 
							   NULL AS email_profissional, 
							   s.nome,
					           COALESCE(s.cd_empresa,9999) AS cd_empresa,   
					           COALESCE(s.cd_registro_empregado,0) AS cd_registro_empregado,
					           COALESCE(s.seq_dependencia,0) AS seq_dependencia,
							   MD5(CAST(s.codigo AS TEXT)) AS re_cripto
			              FROM acs.seminario s
						 WHERE s.email LIKE '%@%' 
						   AND s.cd_seminario_edicao = 5 
						   AND s.dt_exclusao IS NULL
						   AND s.fl_presente = 'S'
						 ORDER BY dt_inclusao
				    ";
		}
		// ------------------------------------ Seminário 2011:	
		elseif ($cd_publico == "SE11")  #"Seminário Econômico 2011 (Cenários 2012) - Inscritos"
		{
			$sqlp = "
						SELECT s.codigo, 
						       s.email, 
							   NULL AS email_profissional, 
							   s.nome,
					           COALESCE(s.cd_empresa,9999) AS cd_empresa,   
					           COALESCE(s.cd_registro_empregado,0) AS cd_registro_empregado,
					           COALESCE(s.seq_dependencia,0) AS seq_dependencia,
							   MD5(CAST(s.codigo AS TEXT)) AS re_cripto
			              FROM acs.seminario s
						 WHERE s.email LIKE '%@%' 
						   AND s.cd_seminario_edicao = 6 
						   AND s.dt_exclusao IS NULL
						 ORDER BY dt_inclusao
				    ";
		}
		// ------------------------------------ Seminário 2011 Somente presentes:	
		elseif ($cd_publico == "EP11") #"Seminário Econômico 2011 (Cenários 2012) - Somente Presentes"
		{
			$sqlp = "
						SELECT s.codigo, 
						       s.email, 
							   NULL AS email_profissional, 
							   s.nome,
					           COALESCE(s.cd_empresa,9999) AS cd_empresa,   
					           COALESCE(s.cd_registro_empregado,0) AS cd_registro_empregado,
					           COALESCE(s.seq_dependencia,0) AS seq_dependencia,
							   MD5(CAST(s.codigo AS TEXT)) AS re_cripto
			              FROM acs.seminario s
						 WHERE s.email LIKE '%@%' 
						   AND s.cd_seminario_edicao = 6 
						   AND s.dt_exclusao IS NULL
						   AND s.fl_presente = 'S'
						 ORDER BY dt_inclusao
				    ";
		}	
		// ------------------------------------ Seminário 2012 PARTICPANTES NÃO INSCRITOS:	
		elseif ($cd_publico == "PS12") #"Participantes Não Inscritos no Seminário Econômico 2012"
		{
			$sqlp = "
						SELECT p.cd_plano, 
							   p.cd_empresa, 
							   p.cd_registro_empregado, 
							   p.seq_dependencia, 
							   p.nome, 
							   p.email, 
							   p.email_profissional,
							   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
						  FROM participantes p
						 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
						   AND p.dt_obito  IS NULL 
						   AND p.cd_plano   > 0 
						   AND funcoes.remove_acento(TRIM(UPPER(p.cidade))) != 'PORTO ALEGRE'	
						   AND funcoes.format_cpf(coalesce(p.cpf_mf,0)::bigint)::TEXT not IN (SELECT DISTINCT COALESCE(ev.cpf,'')::TEXT
																								FROM projetos.eventos_institucionais_inscricao ev
																							   WHERE ev.dt_exclusao IS NULL
																								 AND ev.cd_eventos_institucionais = 92)		
						 ORDER BY p.nome
				    ";
		}	
		
		elseif ($cd_publico == "EP12") #"Seminário Econômico 2012 (Cenários 2013) - Somente Presentes"
		{
			$sqlp = "
						SELECT NULL AS cd_plano, 
							   cd_empresa, 
							   cd_registro_empregado, 
							   seq_dependencia, 
							   nome, 
							   email, 
							   NULL AS email_profissional,
							   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
						  FROM projetos.eventos_institucionais_inscricao ev
						 WHERE dt_exclusao IS NULL
						   AND cd_eventos_institucionais = 92
						   AND fl_presente = 'S'	
						 ORDER BY nome
				    ";
		}	
		
		elseif ($cd_publico == "SE12") #"Seminário Econômico 2012 (Cenários 2013) - Inscritos"
		{
			$sqlp = "
						SELECT NULL AS cd_plano, 
							   cd_empresa, 
							   cd_registro_empregado, 
							   seq_dependencia, 
							   nome, 
							   email, 
							   NULL AS email_profissional,
							   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
						  FROM projetos.eventos_institucionais_inscricao ev
						 WHERE dt_exclusao IS NULL
						   AND cd_eventos_institucionais = 92
						 ORDER BY nome
				    ";
		}	

		
		// ------------------------------------ Seminário 2012 PARTICPANTES NÃO INSCRITOS:	
		elseif ($cd_publico == "SEPA") #"Seminário Econômico 2012 Empresas Pessoas ABRAPH"
		{
			$sqlp = "
						SELECT DISTINCT LOWER(ee.ds_email) As email, 
							   e.ds_empresa AS nome, 
							   NULL AS cd_plano, 
							   NULL AS cd_empresa, 
							   NULL AS cd_registro_empregado, 
							   NULL AS seq_dependencia, 
							   NULL AS email_profissional,
							   NULL AS re_cripto,
							   'EMPRESA' AS tipo
						  FROM expansao.empresa_email ee
						  JOIN expansao.empresa e
							ON e.cd_empresa = ee.cd_empresa
						   AND TRIM(COALESCE(ee.ds_email,'')) NOT IN (SELECT TRIM(COALESCE(ev.email,''))
																		FROM projetos.eventos_institucionais_inscricao ev
																	   WHERE ev.dt_exclusao IS NULL
																	     AND ev.cd_eventos_institucionais = 92)
						 UNION
						SELECT DISTINCT LOWER(pe.ds_email) As email,
							   p.ds_pessoa AS nome, 
							   NULL AS cd_plano, 
							   NULL AS cd_empresa, 
							   NULL AS cd_registro_empregado, 
							   NULL AS seq_dependencia, 
							   NULL AS email_profissional,
							   NULL AS re_cripto,
							   'PESSOA' AS tipo
						  FROM expansao.pessoa_email pe
						  JOIN expansao.pessoa p
							ON p.cd_pessoa = pe.cd_pessoa
						   AND TRIM(COALESCE(pe.ds_email,'')) NOT IN (SELECT TRIM(COALESCE(ev.email,''))
																		FROM projetos.eventos_institucionais_inscricao ev
																	   WHERE ev.dt_exclusao IS NULL
																	     AND ev.cd_eventos_institucionais = 92)
						 UNION
						SELECT email_1 AS email,
							   nome,
							   NULL AS cd_plano,
							   NULL AS cd_empresa,
							   NULL AS cd_registro_empregado,
							   NULL AS seq_dependencia,  
							   email_2 AS email_profissional,
							   NULL AS re_cripto,
							  'ABRAPP' AS tipo
						  FROM prevenir.contato_abrapp
						 WHERE TRIM(COALESCE(email_1,'')) NOT IN (SELECT TRIM(COALESCE(ev.email,''))
																	FROM projetos.eventos_institucionais_inscricao ev
																   WHERE ev.dt_exclusao IS NULL
																	 AND ev.cd_eventos_institucionais = 92) OR (TRIM(COALESCE(email_2,'')) NOT IN (SELECT TRIM(COALESCE(ev.email,''))
																																					 FROM projetos.eventos_institucionais_inscricao ev
																																					WHERE ev.dt_exclusao IS NULL
																																					  AND ev.cd_eventos_institucionais = 92))
						 ORDER BY nome
				    ";
		}
		
						   

		// ------------------------------------ Seminário 2012:	
		elseif ($cd_publico == "SN12")  #"Seminário Econômico 2012 NÃO INSCRITOS"
		{
			$sqlp = "
					SELECT DISTINCT LOWER(s.email) As email, 
						   s.codigo, 
						   NULL AS email_profissional, 
						   s.nome,
						   COALESCE(s.cd_empresa,9999) AS cd_empresa,   
						   COALESCE(s.cd_registro_empregado,0) AS cd_registro_empregado,
						   COALESCE(s.seq_dependencia,0) AS seq_dependencia,
						   MD5(CAST(s.codigo AS TEXT)) AS re_cripto,
						   'SEMINARIO' AS tipo
					  FROM acs.seminario s
					 WHERE s.email LIKE '%@%' 
					   AND s.cd_seminario_edicao IN (4, 5, 6)
					   AND s.dt_exclusao IS NULL
					   AND TRIM(COALESCE(s.email,'')) NOT IN (SELECT TRIM(COALESCE(ev.email,''))
																FROM projetos.eventos_institucionais_inscricao ev
															   WHERE ev.dt_exclusao IS NULL
																 AND ev.cd_eventos_institucionais = 92)
					 UNION
					SELECT email_1 AS email,
						   NULL AS codigo,
						   email_2 AS email_profissional,
						   nome,
						   NULL AS cd_empresa,
						   NULL AS cd_registro_empregado,
						   NULL AS seq_dependencia,
						   NULL AS re_cripto,
						   'ABRAPP' AS tipo
					  FROM prevenir.contato_abrapp
					 WHERE TRIM(COALESCE(email_1,'')) NOT IN (SELECT TRIM(COALESCE(ev.email,''))
																FROM projetos.eventos_institucionais_inscricao ev
															   WHERE ev.dt_exclusao IS NULL
																 AND ev.cd_eventos_institucionais = 92) OR (TRIM(COALESCE(email_2,'')) NOT IN (SELECT TRIM(COALESCE(ev.email,''))
																										 FROM projetos.eventos_institucionais_inscricao ev
																										WHERE ev.dt_exclusao IS NULL
																										  AND ev.cd_eventos_institucionais = 92))
					 UNION
					SELECT email,
						   NULL AS codigo,
						   NULL AS email_profissional,
						   NULL AS nome,
						   NULL AS cd_empresa,
						   NULL AS cd_registro_empregado,
						   NULL AS seq_dependencia,
						   NULL AS re_cripto,
						  'CUT' AS tipo
					 FROM temporario.os35563
					WHERE TRIM(COALESCE(email,'')) NOT IN (SELECT TRIM(COALESCE(ev.email,''))
															 FROM projetos.eventos_institucionais_inscricao ev
															WHERE ev.dt_exclusao IS NULL
															  AND ev.cd_eventos_institucionais = 92)
					 UNION
					SELECT COALESCE(ci.email_1,ci.email_2) AS email,
						   NULL AS codigo,
						   COALESCE(COALESCE(ci.sec_email_1,ci.sec_email_2),ci.email_2) AS email_profissional,
						   UPPER(funcoes.remove_acento(ci.nome)) AS nome,
						   NULL AS cd_empresa,
						   NULL AS cd_registro_empregado,
						   NULL AS seq_dependencia,
						   NULL AS re_cripto,
						   'CONTATO INSTITUCIONAL' AS tipo
					  FROM projetos.contato_institucional ci
					 WHERE ci.dt_exclusao IS NULL
					   AND (TRIM(COALESCE(ci.email_1,''))  NOT IN (SELECT TRIM(COALESCE(ev.email,''))
																	 FROM projetos.eventos_institucionais_inscricao ev
																	WHERE ev.dt_exclusao IS NULL
																	  AND ev.cd_eventos_institucionais = 92) OR TRIM(COALESCE(ci.email_2,''))  NOT IN (SELECT TRIM(COALESCE(ev.email,''))
																												 FROM projetos.eventos_institucionais_inscricao ev
																												WHERE ev.dt_exclusao IS NULL
																												  AND ev.cd_eventos_institucionais = 92))
					 UNION
					SELECT email,
						   NULL AS codigo,
						   NULL AS email_profissional,
						   NULL AS nome,
						   NULL AS cd_empresa,
						   NULL AS cd_registro_empregado,
						   NULL AS seq_dependencia,
						   NULL AS re_cripto,
						  'CONGREGARH 2011' AS tipo
					 FROM temporario.os31156
					WHERE TRIM(COALESCE(email,'')) NOT IN (SELECT TRIM(COALESCE(ev.email,''))
															 FROM projetos.eventos_institucionais_inscricao ev
															WHERE ev.dt_exclusao IS NULL
															  AND ev.cd_eventos_institucionais = 92)	
					 UNION
					SELECT p.email,
						   NULL AS codigo,
						   p.email_profissional,
						   p.nome,
						   p.cd_empresa,
						   p.cd_registro_empregado,
						   p.seq_dependencia,
						   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
						   'ATIVOS' AS tipo
					  FROM public.titulares t 
					  JOIN public.participantes p
						ON t.cd_empresa            = p.cd_empresa
					   AND t.cd_registro_empregado = p.cd_registro_empregado
					   AND t.seq_dependencia       = p.seq_dependencia
					 WHERE p.seq_dependencia = 0
					   AND p.cd_plano        > 0
					   AND p.dt_obito        IS NULL
					   AND (p.tipo_folha IN (0,1,8,11,12,13,16,17,18,19) OR (p.tipo_folha = 6 AND t.tipo_aposentado IN (4,13)))
					   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')    
					   AND funcoes.format_cpf(coalesce(p.cpf_mf,0)::bigint)::TEXT not IN (SELECT distinct COALESCE(ev.cpf,'')::TEXT
																							FROM projetos.eventos_institucionais_inscricao ev
																						   WHERE ev.dt_exclusao IS NULL
																							 AND ev.cd_eventos_institucionais = 92)		
					 ORDER BY nome
                                                                         							          


				    ";
		}
		
// ------------------------------------ "Encontro mais vida (2008) - A arte de viver a mudança":	
		elseif ($cd_publico == "CS2K") 
		{
			$sqlp = "
						SELECT COALESCE(cd_empresa,9999) AS cd_empresa,
						       COALESCE(cd_registro_empregado,0) AS cd_registro_empregado,
						       COALESCE(seq_dependencia,0) AS seq_dependencia,
						       UPPER(nome) AS nome,  
						       email
						  FROM projetos.eventos_institucionais_inscricao
						 WHERE cd_eventos_institucionais = 18
						   AND dt_exclusao IS NULL
						   AND email like '%@%'
						 ORDER BY nome						   
					";
			
		}		
// ------------------------------------ "Seminário Concessões do setor público de energia elétrica - 2009":	
		elseif ($cd_publico == "SCEE") 
		{
			$sqlp = "
					SELECT COALESCE(cd_empresa,9999) AS cd_empresa,
						   COALESCE(cd_registro_empregado,0) AS cd_registro_empregado,
						   COALESCE(seq_dependencia,0) AS seq_dependencia,
						   UPPER(nome_sem_acento) AS nome,  
						   LOWER(email) AS email
					  FROM acs.seminario_concessao_energia
					 WHERE nr_ano_edicao = 2009
					   AND dt_exclusao IS NULL
					   AND email like '%@%'
					 ORDER BY nome					   
					";
			
		}		
// ------------------------------------ Agrupamentos:	
		elseif ($cd_publico == "CS1U") {
			$sqlp = 	"select   distinct(email_1) as email, email_2 as email_profissional, nome_pessoa as nome, nome_empresa_entidade from expansao.mailing m, projetos.usuarios_agrupamentos a ";
			$sqlp = $sqlp. 	"where (email_1 like '%@%' or email_2 like '%@%') and ";
			$sqlp = $sqlp. 	"m.cd_mailing = a.cd_mailing and ";
			$sqlp = $sqlp. 	"a.id_agrupamento = 'mailing' ";
		}
// ------------------------------------ Empresas e instituições:	
		elseif ($cd_publico == "CS1I") {
			$sqlp = "select nome_empresa_entidade, email from expansao.empresas_instituicoes where email like '%@%'";
			
			if($_POST['chk_publico_CS3W2'] == "CS3W")
			{
				$sqlp.= "
							AND TRIM(email) NOT IN (select TRIM(email) 
							                          from acs.seminario 
							                         where email like '%@%' and cd_seminario_edicao = 2 AND dt_exclusao IS NULL)
				        ";
			}
			
		}
// ------------------------------------ Visão região de Porto Alegre	
		elseif ($cd_publico == "CS1S") {
			$sqlp = "select cd_empresa, cd_registro_empregado, seq_dependencia, nome, email, email_profissional, cidade from consultas.emails_micro where email like '%@%' and  cd_microregiao = 43026";
		}
// ------------------------------------ Sem recadastramento até o momento
		elseif ($cd_publico == "CS1T") {	
			$sqlp = "select nome, email, email_profissional, cidade from consultas.recadastramento_pesionistas where email like '%@%'";
		}
// ------------------------------------ Emails profissionais CEEE identificados no cadastro
//		elseif ($cd_publico == "CS1U") {		
//			$sqlp = "select nome_lista as nome, email_lista as email from consultas.emails_identificados where email_lista like '%@%'";
//		}
// ------------------------------------ Emails profissionais CEEE não identificados no cadastro
		elseif ($cd_publico == "CS1V") {		
			$sqlp = "select nome, email_lista as email from consultas.emails_sem_identificacao where email_lista like '%@%'";
		}
// ------------------------------------ Eleitores
		elseif ($cd_publico == "CS1R") 
		{
			$sqlp = "
						select p.cd_plano, p.cd_empresa, p.cd_registro_empregado, 
						p.seq_dependencia, p.nome, p.email, p.email_profissional 
						from participantes p, eleicoes.cadastros_eleicoes ce 
						where (p.email like '%@%' or p.email_profissional) like '%@%'
						and p.dt_obito is null  
						and p.cd_empresa = ce.cd_empresa 
						and p.cd_registro_empregado = ce.cd_registro_empregado 
						and p.seq_dependencia = ce.seq_dependencia ";
		}
// ------------------------------------
		else 
		{		

			switch ($cd_publico) 
			{
				case "PAPF": //PARTICIPANTES Região Passo Fundo - OS: 32309
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito  IS NULL 
							   AND p.cd_plano   > 0 
							   AND (funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'PASSO FUNDO'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'CARAZINHO'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'ERECHIM'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'IBIRAPUITA'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'ILOPOLIS'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'MARAU'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'NAO ME TOQUE'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'SOLEDADE'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'TAPERA'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'TIO HUGO')							   
							 ORDER BY p.nome
							";
					break;		
					
				case "PASC": //PARTICIPANTES Região Santa Cruz - OS: 37794
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito  IS NULL 
							   AND p.cd_plano   > 0 
							   AND (funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'SANTA CRUZ DO SUL'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'CANDELARIA'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'CACHOEIRA DO SUL'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'ENCRUZILHADA DO SUL'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'PANTANO GRANDE'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'RIO PARDO'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'TAQUARI'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'VENANCIO AIRES'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'VERA CRUZ')							   
							 ORDER BY p.nome
							";

					break;
					
				case "PAGC": //PARTICIPANTES Gramado e Canela - OS: 35488
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito  IS NULL 
							   AND p.cd_plano   > 0 
							   AND (UPPER(p.cidade) =  UPPER('GRAMADO') OR UPPER(p.cidade) =  UPPER('CANELA'))						   
							 ORDER BY p.nome
							";
					break;		

				case "SAPE": //Saúde Financeira Pelotas: (Participantes)(Pelotas, Rio Grande, Piratini, São Lourenço do Sul, Canguçú, Pedro Osório, Arroio Grande) OS: 35781
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito  IS NULL 
							   AND p.cd_plano   > 0 
                               AND (UPPER(p.cidade) =  UPPER('PELOTAS')
										OR UPPER(p.cidade) =  UPPER('RIO GRANDE')
										OR UPPER(p.cidade) =  UPPER('PIRATINI')
										OR UPPER(p.cidade) =  UPPER('CANGUCU')
										OR UPPER(p.cidade) =  UPPER('PEDRO OSORIO')
										OR UPPER(p.cidade) =  UPPER('ARROIO GRANDE'))
							 ORDER BY p.nome
							";
					break;		

				case "SABA": //Saúde Financeira Bagé: (Participantes)(Bagé, Pinheiro Machado, Lavras do Sul e Dom Pedrito) OS: 35781
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito  IS NULL 
							   AND p.cd_plano   > 0 
                               AND (UPPER(p.cidade) =  UPPER('BAGE')
										OR UPPER(p.cidade) =  UPPER('PINHEIRO MACHADO')
										OR UPPER(p.cidade) =  UPPER('LAVRAS DO SUL')
										OR UPPER(p.cidade) =  UPPER('DOM PEDRITO'))
							 ORDER BY p.nome
							";
					break;		

				case "SACA": //Saúde Financeira Candiota: (Participantes)(Candiota) OS: 35781
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito  IS NULL 
							   AND p.cd_plano   > 0 
                               AND (UPPER(p.cidade) =  UPPER('CANDIOTA'))
							 ORDER BY p.nome
							";
					break;					
					
					
				case "PACH": //PARTICIPANTES (Cacequi, Cachoeira do Sul, São Sepé, São Gabriel, São Vicente, São Pedro do Sul, Julio de Castilhos) - OS: 35470
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito  IS NULL 
							   AND p.cd_plano   > 0 
							   AND (UPPER(p.cidade) =  UPPER('CACEQUI') 
									OR UPPER(p.cidade) =  UPPER('CACHOEIRA DO SUL')
									OR UPPER(p.cidade) =  UPPER('SAO SEPE')
									OR UPPER(p.cidade) =  UPPER('SAO GABRIEL')
									OR UPPER(p.cidade) =  UPPER('SAO VICENTE')
									OR UPPER(p.cidade) =  UPPER('SAO PEDRO DO SUL')
									OR UPPER(p.cidade) =  UPPER('JULIO DE CASTILHOS'))
							 ORDER BY p.nome
							";
					break;
					
					
				case "PAPR": //PARTICIPANTES (Arroio do Sal, Capão da Canoa, Cidreira, Dom Pedro de Alcântara, Imbé, Magistério, Mariluz, Maquiné, Osório, Pinhal, Quintão, Terra de Areia, Tramandaí, Três Forquilhas, Três Cachoeiras, Torres, Xangri-lá) - OS: 36855
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito  IS NULL 
							   AND p.cd_plano   > 0 
							   AND (UPPER(p.cidade) =  UPPER('ARROIO DO SAL') 
									OR UPPER(p.cidade) =  UPPER('CAPAO DA CANOA')
									OR UPPER(p.cidade) =  UPPER('CIDREIRA')
									OR UPPER(p.cidade) =  UPPER('DOM PEDRO DE ALCANTARA')
									OR UPPER(p.cidade) =  UPPER('IMBE')
									OR UPPER(p.cidade) =  UPPER('MAGISTERIO')
									OR UPPER(p.cidade) =  UPPER('MARILUZ')
									OR UPPER(p.cidade) =  UPPER('MAQUINE')
									OR UPPER(p.cidade) =  UPPER('OSORIO')
									OR UPPER(p.cidade) =  UPPER('PINHAL')
									OR UPPER(p.cidade) =  UPPER('QUINTAO')
									OR UPPER(p.cidade) =  UPPER('TERRA DE AREIA')
									OR UPPER(p.cidade) =  UPPER('TRAMANDAI')
									OR UPPER(p.cidade) =  UPPER('TRES FORQUILHAS')
									OR UPPER(p.cidade) =  UPPER('TRES CACHOEIRAS')
									OR UPPER(p.cidade) =  UPPER('TORRES')
									OR UPPER(p.cidade) =  UPPER('XANGRI-LA'))
							 ORDER BY p.nome
							";
					break;
					
				case "PABA": //PARTICIPANTES (Bagé, Candiota, Pinheiro Machado, Lavras do Sul, Dom Pedrito, Hulha Negra, Santana do Livramento) - OS: 36962
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito  IS NULL 
							   AND p.cd_plano   > 0 
							   AND (UPPER(p.cidade) =  UPPER('BAGE') 
									OR UPPER(p.cidade) =  UPPER('CANDIOTA')
									OR UPPER(p.cidade) =  UPPER('PINHEIRO MACHADO')
									OR UPPER(p.cidade) =  UPPER('LAVRAS DO SUL')
									OR UPPER(p.cidade) =  UPPER('DOM PEDRITO')
									OR UPPER(p.cidade) =  UPPER('HULHA NEGRA')
									OR UPPER(p.cidade) =  UPPER('SANTANA DO LIVRAMENTO'))
							 ORDER BY p.nome
							";
					break;	
				
				case "PSPS": //PARTICIPANTES (Salto do Jacuí, Panambi e Sobradinho) - OS: 37609
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito  IS NULL 
							   AND p.cd_plano   > 0 
							   AND (UPPER(p.cidade) =  UPPER('SALTO DO JACUI') 
									OR UPPER(p.cidade) =  UPPER('PANAMBI')
									OR UPPER(p.cidade) =  UPPER('SOBRADINHO'))
							 ORDER BY p.nome
							";
					break;	
					
				case "PCSI": //PARTICIPANTES (Canela, São Francisco de Paula, Igrejinha, Três Coroas, Taquara, Gramado) - OS: 37608
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito  IS NULL 
							   AND p.cd_plano   > 0 
							   AND (UPPER(p.cidade) =  UPPER('CANELA') 
									OR UPPER(p.cidade) =  UPPER('SAO FRANCISCO DE PAULA')
									OR UPPER(p.cidade) =  UPPER('IGREJINHA')
									OR UPPER(p.cidade) =  UPPER('TRES COROAS')
									OR UPPER(p.cidade) =  UPPER('TAQUARA')
									OR UPPER(p.cidade) =  UPPER('GRAMADO'))
							 ORDER BY p.nome
							";
					break;	
					
					
				case "PSPS": //PARTICIPANTES (Salto do Jacuí, Panambi e Sobradinho) - OS: 37609
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							  JOIN patrocinadoras pa
							    ON pa.cd_empresa = p.cd_empresa
							   AND pa.tipo_cliente = 'P'
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito  IS NULL 
							   AND p.cd_plano   > 0 
							   AND (UPPER(p.cidade) =  UPPER('SALTO DO JACUI') 
									OR UPPER(p.cidade) =  UPPER('PANAMBI')
									OR UPPER(p.cidade) =  UPPER('SOBRADINHO'))
							 ORDER BY p.nome
							";
					break;	

				case "PAPE": //PARTICIPANTES (Pelotas, Arroio Grande, Canguçú, Capão do Leão, Cristal, Jaguarão, Morro Redondo, Pedro Osório, Piratini e São Lourenço do Sul) - OS: 36962
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito  IS NULL 
							   AND p.cd_plano   > 0 
							   AND (UPPER(p.cidade) =  UPPER('PELOTAS') 
									OR UPPER(p.cidade) =  UPPER('ARROIO GRANDE')
									OR UPPER(p.cidade) =  UPPER('CANGUCU')
									OR UPPER(p.cidade) =  UPPER('CAPAO DO LEAO')
									OR UPPER(p.cidade) =  UPPER('CRISTAL')
									OR UPPER(p.cidade) =  UPPER('JAGUARAO')
									OR UPPER(p.cidade) =  UPPER('MORRO REDONDO')
									OR UPPER(p.cidade) =  UPPER('PEDRO OSORIO')
									OR UPPER(p.cidade) =  UPPER('PIRATINI')
									OR UPPER(p.cidade) =  UPPER('SAO LOURENCO DO SUL'))
							 ORDER BY p.nome
							";
					break;		

				case "PARG": //PARTICIPANTES (Rio Grande, Santa Vitória do Palmar,  São José do Norte) - OS: 36962
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito  IS NULL 
							   AND p.cd_plano   > 0 
							   AND (UPPER(p.cidade) =  UPPER('RIO GRANDE') 
									OR UPPER(p.cidade) =  UPPER('SANTA VITORIA DO PALMAR')
									OR UPPER(p.cidade) =  UPPER('SAO JOSE DO NORTE'))
							 ORDER BY p.nome
							";
					break;					
					
					
				case "PASM": //PARTICIPANTES (SEM ATIVOS CEEE) (Santa Maria, Cacequi, Cachoeira do Sul, São Sepé, São Gabriel, São Vicente, São Pedro do Sul, Julio de Castilhos) - OS: 35670
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito  IS NULL 
							   AND p.cd_plano   > 0 
							   AND NOT (projetos.participante_tipo(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = 'ATIV' AND p.cd_empresa = 0)
							   AND (UPPER(p.cidade) =  UPPER('CACEQUI') 
									OR UPPER(p.cidade) =  UPPER('SANTA MARIA')
									OR UPPER(p.cidade) =  UPPER('CACHOEIRA DO SUL')
									OR UPPER(p.cidade) =  UPPER('SAO SEPE')
									OR UPPER(p.cidade) =  UPPER('SAO GABRIEL')
									OR UPPER(p.cidade) =  UPPER('SAO VICENTE')
									OR UPPER(p.cidade) =  UPPER('SAO PEDRO DO SUL')
									OR UPPER(p.cidade) =  UPPER('JULIO DE CASTILHOS'))
							 ORDER BY p.nome
							";
					break;					
					

				case "CS1X": // Emails profissionais Fundação CEEE
					$sqlp = "
							SELECT 2 AS cd_plano,
								   cd_patrocinadora AS cd_empresa,
								   cd_registro_empregado,
								   0 AS seq_pendencia,
								   nome,
								   COALESCE(usuario) || '@eletroceee.com.br' AS email,
								   '' AS email_profissional,
								   funcoes.cripto_re(COALESCE(cd_patrocinadora,99), COALESCE(cd_registro_empregado, codigo), 0) AS re_cripto
							  FROM projetos.usuarios_controledi
							 WHERE tipo NOT IN ('X', 'E', 'T', 'f')
							 ORDER BY nome
							";
					break;
					
				case "GRIT": // GRI TESTE
					$sqlp = "
							SELECT 2 AS cd_plano,
								   cd_patrocinadora AS cd_empresa,
								   cd_registro_empregado,
								   0 AS seq_pendencia,
								   nome,
								   COALESCE(usuario) || '@eletroceee.com.br' AS email,
								   '' AS email_profissional,
								   funcoes.cripto_re(COALESCE(cd_patrocinadora,99), COALESCE(cd_registro_empregado, codigo), 0) AS re_cripto
							  FROM projetos.usuarios_controledi
							 WHERE divisao = 'GRI'
							   AND tipo NOT IN ('X', 'E', 'T', 'f')
							 ORDER BY nome
					        ";
					break;					
				
				case "CS1B": //APOSENTADO
					$sqlp = "
							SELECT p.cd_plano, 
								   p.cd_empresa, 
								   p.cd_registro_empregado, 
								   p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto 
							  FROM public.participantes p
							 WHERE p.dt_obito        IS NULL 
							   AND p.cd_plano        > 0 
							   AND p.seq_dependencia = 0
							   AND p.tipo_folha      IN (2,3,4,5,14,15,20,30,40,45,50,60,65,75,80)
   							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
							";
					break;
					
				case "APPO": //APOSENTADO que residem em Porto Alegre OS: 31426
					$sqlp = "
							SELECT p.cd_plano, 
								   p.cd_empresa, 
								   p.cd_registro_empregado, 
								   p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto 
							  FROM public.participantes p
							 WHERE p.dt_obito        IS NULL 
							   AND p.cd_plano        > 0 
							   AND p.seq_dependencia = 0
							   AND p.tipo_folha      IN (2,3,4,5,14,15,20,30,40,45,50,60,65,75,80)
   							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
							   AND funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'PORTO ALEGRE'
							";
					break;	

                case "APJS": //APOSENTADO que residem em Salto do Jacuí, Sobradinho e Panambi OS: 32190 
					$sqlp = "
							SELECT p.cd_plano, 
								   p.cd_empresa, 
								   p.cd_registro_empregado, 
								   p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto 
							  FROM public.participantes p
							 WHERE p.dt_obito        IS NULL 
							   AND p.cd_plano        > 0 
							   AND p.seq_dependencia = 0
							   AND p.tipo_folha      IN (2,3,4,5,14,15,20,30,40,45,50,60,65,75,80)
   							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
							   AND (funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'SALTO JACUI'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'SOBRADINHO'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'PANABI')
							";
					break;	

				case "CS1C": //PENSIONISTA
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto								   
							  FROM public.participantes p
							  JOIN public.dependentes d
								ON d.cd_empresa            = p.cd_empresa
							   AND d.cd_registro_empregado = p.cd_registro_empregado
							   AND d.seq_dependencia       = p.seq_dependencia          
							 WHERE p.dt_obito        IS NULL 
							   AND p.cd_plano        > 0 
							   AND p.seq_dependencia > 0
							   AND p.tipo_folha      IN (2,45,80)
							   AND d.dt_desligamento IS NULL
							   AND d.id_pensionista  = 'S' 
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
							";
					break;	

				case "PEPO": //PESIONISTAS que residem em Porto Alegre OS: 31426
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto								   
							  FROM public.participantes p
							  JOIN public.dependentes d
								ON d.cd_empresa            = p.cd_empresa
							   AND d.cd_registro_empregado = p.cd_registro_empregado
							   AND d.seq_dependencia       = p.seq_dependencia          
							 WHERE p.dt_obito        IS NULL 
							   AND p.cd_plano        > 0 
							   AND p.seq_dependencia > 0
							   AND p.tipo_folha      IN (2,45,80)
							   AND d.dt_desligamento IS NULL
							   AND d.id_pensionista  = 'S' 
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
							   AND funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'PORTO ALEGRE'
							";
					break;	
                
                case "PEJS": //PESIONISTAS que residem em Salto do Jacuí, Sobradinho e Panambi OS: 32190
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto								   
							  FROM public.participantes p
							  JOIN public.dependentes d
								ON d.cd_empresa            = p.cd_empresa
							   AND d.cd_registro_empregado = p.cd_registro_empregado
							   AND d.seq_dependencia       = p.seq_dependencia          
							 WHERE p.dt_obito        IS NULL 
							   AND p.cd_plano        > 0 
							   AND p.seq_dependencia > 0
							   AND p.tipo_folha      IN (2,45,80)
							   AND d.dt_desligamento IS NULL
							   AND d.id_pensionista  = 'S' 
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
                               AND (funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'SALTO JACUI'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'SOBRADINHO'
                                OR  funcoes.remove_acento(TRIM(UPPER(p.cidade))) = 'PANABI')
							";
					break;	

				case "SPEC": // PARTICIPANTES SEM PECÚLIO COM IDADE MÁXIMA 54 ANOS E MEIO

					$sqlp = "
							SELECT p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
							       funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.participantes p,
								   public.titulares t,
								   (SELECT cd_empresa, cd_registro_empregado, seq_dependencia, MAX(dt_ingresso_plano)
									  FROM public.titulares_planos
									 WHERE cd_plano IN (1,2,7)
									   AND dt_ingresso_plano <= CURRENT_DATE
									   AND ((dt_deslig_plano IS NULL) OR (dt_deslig_plano > CURRENT_DATE))
									 GROUP BY cd_empresa, 
											  cd_registro_empregado, 
											  seq_dependencia) x
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_nascimento >= (DATE_TRUNC('month',CURRENT_DATE - '54.5 years'::INTERVAL)::DATE) --BUSCA PARTICIPANTES COM ATÉ 54 E MEIO ANOS
							   AND p.cd_empresa            = t.cd_empresa
							   AND p.cd_registro_empregado = t.cd_registro_empregado
							   AND p.seq_dependencia       = t.seq_dependencia
							   AND t.cd_empresa            = x.cd_empresa
							   AND t.cd_registro_empregado = x.cd_registro_empregado
							   AND t.seq_dependencia       = x.seq_dependencia
							   AND t.categoria_funcional   <> 'B'
							   AND t.tipo_aposentado       <> 25
							   AND p.cd_empresa            IN (0,1,2,3,9,7)
							   AND ((p.tipo_folha IN (0,1,6,11,12,13,17)) OR (p.tipo_folha = 8 AND COALESCE(t.tipo_aposentado,0) NOT IN (15,88)))
							   AND p.dt_obito IS NULL
							   AND ((NOT EXISTS (SELECT 1
												   FROM public.peculios
												  WHERE cd_empresa            = p.cd_empresa
													AND cd_registro_empregado = p.cd_registro_empregado
													AND seq_dependencia       = p.seq_dependencia))
									OR
									(EXISTS (SELECT 1
											   FROM public.beneficios b1
											  WHERE b1.tifo_tipo_folha NOT IN (9,35,70,2,45,80)
												AND b1.part_cd_empresa            = p.cd_empresa
												AND b1.part_cd_registro_empregado = p.cd_registro_empregado
												AND b1.part_seq_dependencia       = p.seq_dependencia
												AND b1.data_inicio     > CURRENT_DATE
												AND b1.data_inicio     = (SELECT MAX(data_inicio)
																			FROM public.beneficios b2
																		   WHERE b2.part_cd_empresa            = p.cd_empresa
																			 AND b2.part_cd_registro_empregado = p.cd_registro_empregado
																			 AND b2.part_seq_dependencia       = p.seq_dependencia)
												AND NOT EXISTS         (SELECT 1
																		  FROM public.peculios
																		 WHERE cd_empresa            = p.cd_empresa
																		   AND cd_registro_empregado = p.cd_registro_empregado
																		   AND seq_dependencia       = p.seq_dependencia))))
							 ORDER BY p.cd_empresa, 
									  p.cd_registro_empregado, 
									  p.seq_dependencia
					";
					break;					
					
				case "AB12": //Abaixo Assinado Cruzeiro do Sul 09/2012 - OS: 35362
					$sqlp = "
							SELECT p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM acs.abaixo_assinado_092012 a
							  JOIN public.participantes p
								ON p.cd_empresa            = a.cd_empresa
							   AND p.cd_registro_empregado = a.cd_registro_empregado
							   AND p.seq_dependencia       = a.seq_dependencia
							 WHERE p.cd_plano        > 0
							   AND p.dt_obito        IS NULL
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
					";
					break;					
					
					
					
				case "ATV1": // ATIVOS
					$sqlp = "
							SELECT p.cd_plano,
							       p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
							       funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.titulares t 
							  JOIN public.participantes p
								ON t.cd_empresa            = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia       = p.seq_dependencia
							 WHERE p.seq_dependencia = 0
							   AND p.cd_plano        > 0
							   AND p.dt_obito        IS NULL
							   AND (p.tipo_folha IN (0,1,8,11,12,13,16,17,18,19) OR (p.tipo_folha = 6 AND t.tipo_aposentado IN (4,13)))
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
					";
					break;	
					
					
				case "ATV2": // ATIVOS - SEM FUNDAÇÃO CEEE
					$sqlp = "
							SELECT p.cd_plano,
							       p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
							       funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.titulares t 
							  JOIN public.participantes p
								ON t.cd_empresa            = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia       = p.seq_dependencia
							 WHERE p.seq_dependencia = 0
							   AND p.cd_plano        > 0
							   AND p.dt_obito        IS NULL
							   AND (p.tipo_folha IN (0,1,8,11,12,13,16,17,18,19) OR (p.tipo_folha = 6 AND t.tipo_aposentado IN (4,13)))
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.cd_empresa <> 9
					";
					break;					
					
				case "ATV3": #### ATIVOS PLANO ÚNICO (SEM CTP) ####
					$sqlp = "
							SELECT p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
							       funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM titulares AS t, 
								   participantes AS p
							 WHERE t.cd_empresa            = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia       = p.seq_dependencia
							   AND p.seq_dependencia       = 0
							   AND p.cd_plano              = 1
							   AND p.cd_empresa            IN (0, 1, 2, 3, 9)
							   AND p.tipo_folha            IN (0, 1, 8, 16, 17, 11, 12, 13, 18, 19)
							   AND p.dt_obito              IS NULL
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
					";
					break;
					
				case "ATV4": // ATIVOS PORTO ALEGRE E GRANDE PORTO ALEGRE - OS: 31838
					$sqlp = "
							SELECT p.cd_plano,
							       p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
							       funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.titulares t 
							  JOIN public.participantes p
								ON t.cd_empresa            = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia       = p.seq_dependencia
							 WHERE p.seq_dependencia = 0
							   AND p.cd_plano        > 0
							   AND p.dt_obito        IS NULL
							   AND (p.tipo_folha IN (0,1,8,11,12,13,16,17,18,19) OR (p.tipo_folha = 6 AND t.tipo_aposentado IN (4,13)))
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
						       AND (funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('PORTO ALEGRE')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('ALVORADA')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('CACHOEIRINHA')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('CAMPO BOM')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('CANOAS')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('ESTANCIA VELHA')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('ESTEIO')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('GRAVATAI')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('GUAIBA')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('NOVO HAMBURGO')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('SAO LEOPOLDO')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('SAPIRANGA')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('SAPUCAIA DO SUL')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('VIAMAO')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('DOIS IRMAOS')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('ELDORADO DO SUL')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('GLORINHA')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('IVOTI')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('NOVA HARTZ')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('PAROBE')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('PORTAO')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('TRIUNFO')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('CHARQUEADAS')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('ARARICA')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('NOVA SANTA RITA')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('MONTENEGRO')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('TAQUARA')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('SAO JERONIMO')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('ARROIO DOS RATOS')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('SANTO ANTANIO DA PATRULHA')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('CAPELA DE SANTANA')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('ROLANTE'))							   
					";
					break;	

				case "ATV5": //ATIVOS DE SALTO DO JACUÍ, SOBRADINHO E PANAMBI - OS: 32076
					$sqlp = "
							SELECT p.cd_plano,
							       p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
							       funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.titulares t 
							  JOIN public.participantes p
								ON t.cd_empresa            = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia       = p.seq_dependencia
							 WHERE p.seq_dependencia = 0
							   AND p.cd_plano        > 0
							   AND p.dt_obito        IS NULL
							   AND (p.tipo_folha IN (0,1,8,11,12,13,16,17,18,19) OR (p.tipo_folha = 6 AND t.tipo_aposentado IN (4,13)))
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
						       AND (funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('SALTO DO JACUI')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('SOBRADINHO')
						        OR funcoes.remove_acento(UPPER(p.cidade)) =  UPPER('PANAMBI'))							   
					";
					break;		

				case "ATV6": //Ativos (Pelotas, Rio Grande, Piratini, São Lourenço do Sul, Canguçú, Pedro Osório, Arroio Grande) OS: 35781
					$sqlp = "
								SELECT p.cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM public.participantes p
								 WHERE p.dt_obito        IS NULL 
								   AND p.seq_dependencia = 0
								   AND projetos.participante_tipo(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = 'ATIV'
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND (UPPER(p.cidade) =  UPPER('PELOTAS')
										OR UPPER(p.cidade) =  UPPER('RIO GRANDE')
										OR UPPER(p.cidade) =  UPPER('PIRATINI')
										OR UPPER(p.cidade) =  UPPER('SAO LOURENCO DO SUL')
										OR UPPER(p.cidade) =  UPPER('CANGUCU')
										OR UPPER(p.cidade) =  UPPER('PEDRO OSORIO')
										OR UPPER(p.cidade) =  UPPER('ARROIO GRANDE'))						
					   
					";
					break;						
					
				case "EX09": // EXTRATO - FUNDAÇÃO CEEE - CEEEPREV e MIGRADO
					$sqlp = "
							SELECT p.cd_plano,
							       p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
							       funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.participantes p
							  JOIN public.titulares t 
							    ON t.cd_empresa            = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia       = p.seq_dependencia
							 WHERE p.seq_dependencia = 0
							   AND p.cd_plano        = 2
							   AND t.cd_empresa      = 9
							   AND p.dt_obito        IS NULL
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND (p.tipo_folha IN (0,1,8,11,12,13,16,17,18,19) OR (p.tipo_folha = 6 AND t.tipo_aposentado IN (4,13)))
					";
					break;	
					
				case "EX00": // EXTRATO - GRUPO CEEE - CEEEPREV e MIGRADO
					$sqlp = "
							SELECT p.cd_plano,
							       p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
							       funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.participantes p
							  JOIN public.titulares t 
							    ON t.cd_empresa            = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia       = p.seq_dependencia
							 WHERE p.seq_dependencia = 0
							   AND p.cd_plano        = 2
							   AND t.cd_empresa      = 0
							   AND p.dt_obito        IS NULL
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND (p.tipo_folha IN (0,1,8,11,12,13,16,17,18,19) OR (p.tipo_folha = 6 AND t.tipo_aposentado IN (4,13)))
					";
					break;
					
				case "EX06": // EXTRATO - CRM
					$sqlp = "
							SELECT p.cd_plano,
							       p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
							       funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.participantes p
							  JOIN public.titulares t 
							    ON t.cd_empresa            = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia       = p.seq_dependencia
							 WHERE p.seq_dependencia = 0
							   AND p.cd_plano        = 6
							   AND t.cd_empresa      = 6
							   AND p.dt_obito        IS NULL
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND (p.tipo_folha IN (0,1,8,11,12,13,16,17,18,19) OR (p.tipo_folha = 6 AND t.tipo_aposentado IN (4,13)))
					";
					break;	

				case "SAPO": //CEEE: Ativos, Aposentados, Pensionistas e Não Participantes de Porto Alegre, Viamão, Guaíba, Eldorado do Sul, Gravataí e Cachoeirinha. (Sem ativos de Porto Alegre). OS: 35905
					$sqlp = "
								SELECT p.cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM public.participantes p
								 WHERE p.dt_obito   IS NULL 
								   AND p.cd_empresa = 0
								   AND NOT (projetos.participante_tipo(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = 'ATIV' AND UPPER(p.cidade) =  UPPER('PORTO ALEGRE'))
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND  (
											UPPER(p.cidade) =  UPPER('PORTO ALEGRE') OR
											UPPER(p.cidade) =  UPPER('VIAMAO') OR
											UPPER(p.cidade) =  UPPER('ELDORADO DO SUL') OR
											UPPER(p.cidade) =  UPPER('GRAVATAI') OR
											UPPER(p.cidade) =  UPPER('CACHOEIRINHA')
								        )
							";
					break;
					
					

				case "CEAT": //CEEE - ATIVO - Porto Alegre - OS: 32848
					$sqlp = "
								SELECT p.cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM public.participantes p
								 WHERE p.dt_obito   IS NULL 
								   AND p.cd_empresa = 0
								   AND projetos.participante_tipo(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = 'ATIV'
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND UPPER(p.cidade) =  UPPER('PORTO ALEGRE')	
							";
					break;	

				case "ATSM": //ATIVO - Santa Maria - OS: 35470
					$sqlp = "
								SELECT p.cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM public.participantes p
								 WHERE p.dt_obito   IS NULL 
								   AND projetos.participante_tipo(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = 'ATIV'
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND UPPER(p.cidade) =  UPPER('SANTA MARIA')	
							";
					break;
					
					
				case "CS1Y": // CEEE (CEEEPrev + Auto Patrocínio)

					$sqlp = "
							SELECT p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM titulares t, 
								   participantes p
							 WHERE t.cd_empresa = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia = p.seq_dependencia
							   AND p.tipo_folha = 8
							   AND p.cd_plano = 2
							   AND p.cd_empresa = 0
							   AND p.dt_obito IS NULL
							   AND t.tipo_aposentado NOT IN (15,88)
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 

							UNION

							SELECT p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE p.cd_empresa      = 0
							   AND p.seq_dependencia = 0
							   AND p.cd_plano        = 2
							   AND p.tipo_folha      = 0
							   AND p.dt_obito        IS NULL
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							";
					break;

                case "CS1Z": // CEEE - Não Participantes

                    $sqlp = "
							SELECT p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p,
								   titulares t
							 WHERE t.cd_empresa            = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia       = p.seq_dependencia
							   AND p.cd_empresa            = 0
							   AND p.seq_dependencia       = 0
							   AND p.cd_plano              = 0
							   AND p.tipo_folha            = 0
							   AND p.dt_obito              IS NULL
							   AND t.categoria_funcional   IN ('A','C','D')
							   AND t.cd_registro_empregado < 500000
							   AND p.dt_nascimento         >= DATE_TRUNC('year', CURRENT_DATE) - '612 months'::interval
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							";
                    break;
					
                case "CNPP": // CEEE - Não Participantes - Trabalham em PORTO ALEGRE

                    $sqlp = "
							  SELECT p.cd_plano,
								     p.cd_empresa,
								     p.cd_registro_empregado,
								     p.seq_dependencia,
								     p.nome,
								     p.email,
								     p.email_profissional,
								     funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								FROM public.participantes p,
									 public.titulares t,
									 public.lotacaos l
							   WHERE t.cd_empresa            = p.cd_empresa
								 AND t.cd_registro_empregado = p.cd_registro_empregado
								 AND t.seq_dependencia       = p.seq_dependencia
								 AND l.cd_empresa            = t.cd_empresa
								 AND l.cd_tipo_lotacao       = t.cd_tipo_lotacao
								 AND l.area_atuacao          = t.area_atuacao
								 AND UPPER(TRIM(l.cidade))   = 'PORTO ALEGRE'
								 AND p.cd_empresa            = 0
								 AND p.seq_dependencia       = 0
								 AND p.cd_plano              = 0
								 AND p.tipo_folha            = 0
								 AND p.dt_obito              IS NULL
								 AND t.categoria_funcional   IN ('A','C','D')
								 AND t.cd_registro_empregado < 500000
								 AND p.dt_nascimento         >= DATE_TRUNC('year', CURRENT_DATE) - '612 months'::interval
								 AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							";
                    break;					

                case "NPAR": //Não Participantes 

                    $sqlp = "
							SELECT p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.participantes p,
								   public.titulares t
							 WHERE t.cd_empresa            = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia       = p.seq_dependencia
							   AND p.seq_dependencia       = 0
							   AND p.cd_plano              = 0
							   AND p.tipo_folha            = 0
							   AND p.dt_obito              IS NULL
							   AND p.dt_nascimento         >= DATE_TRUNC('year', CURRENT_DATE) - '612 months'::interval
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							 ORDER BY p.cd_empresa, 
								  p.cd_registro_empregado, 
								  p.seq_dependencia								   
							";
                    break;					
					
					
                case "CPEL": // CEEE - Não Participantes - Região PELOTAS

                    $sqlp = "
							SELECT p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
							       funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p,
								   titulares t
							 WHERE t.cd_empresa            = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia       = p.seq_dependencia
							   AND p.cd_empresa            = 0
							   AND p.seq_dependencia       = 0
							   AND p.cd_plano              = 0
							   AND p.tipo_folha            = 0
							   AND p.dt_obito              IS NULL
							   AND t.categoria_funcional   IN ('A','C','D')
							   AND t.cd_registro_empregado < 500000
							   AND p.dt_nascimento         >= DATE_TRUNC('year', CURRENT_DATE) - '612 months'::interval
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND (UPPER(p.cidade) =  UPPER('PELOTAS')
								OR UPPER(p.cidade) =  UPPER('Arroio Grande')
								OR UPPER(p.cidade) =  UPPER('Cangucu')
								OR UPPER(p.cidade) =  UPPER('Capao do Leao')
								OR UPPER(p.cidade) =  UPPER('Herval do Sul')
								OR UPPER(p.cidade) =  UPPER('Morro Redondo')
								OR UPPER(p.cidade) =  UPPER('Pedro Osorio')
								OR UPPER(p.cidade) =  UPPER('Piratini')
								OR UPPER(p.cidade) =  UPPER('Pinheiro Machado')
								OR UPPER(p.cidade) =  UPPER('Rio Grande')
								OR UPPER(p.cidade) =  UPPER('Turucu')
								OR UPPER(p.cidade) =  UPPER('JAGUARAO')
								)
                            ";
                    break;
					
                case "NPEL": // CEEE - Não Participantes - Trabalham Região PELOTAS - OS: 30980

                    $sqlp = "
							  SELECT p.cd_plano,
								     p.cd_empresa,
								     p.cd_registro_empregado,
								     p.seq_dependencia,
								     p.nome,
								     p.email,
								     p.email_profissional,
								     funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								FROM public.participantes p,
									 public.titulares t,
									 public.lotacaos l
							   WHERE t.cd_empresa            = p.cd_empresa
								 AND t.cd_registro_empregado = p.cd_registro_empregado
								 AND t.seq_dependencia       = p.seq_dependencia
								 AND l.cd_empresa            = t.cd_empresa
								 AND l.cd_tipo_lotacao       = t.cd_tipo_lotacao
								 AND l.area_atuacao          = t.area_atuacao
								 --AND UPPER(TRIM(l.cidade))   = 'PORTO ALEGRE'
								 AND p.cd_empresa            = 0
								 AND p.seq_dependencia       = 0
								 AND p.cd_plano              = 0
								 AND p.tipo_folha            = 0
								 AND p.dt_obito              IS NULL
								 AND t.categoria_funcional   IN ('A','C','D')
								 AND t.cd_registro_empregado < 500000
								 AND p.dt_nascimento         >= DATE_TRUNC('year', CURRENT_DATE) - '612 months'::interval
								 AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								 AND (UPPER(l.cidade) =  UPPER('PELOTAS')
							           OR UPPER(l.cidade) =  UPPER('Arroio Grande')
							           OR UPPER(l.cidade) =  UPPER('Cangucu')
							           OR UPPER(l.cidade) =  UPPER('Capao do Leao')
							           OR UPPER(l.cidade) =  UPPER('Herval do Sul')
							           OR UPPER(l.cidade) =  UPPER('Morro Redondo')
							           OR UPPER(l.cidade) =  UPPER('Pedro Osorio')
							           OR UPPER(l.cidade) =  UPPER('Piratini')
							           OR UPPER(l.cidade) =  UPPER('Pinheiro Machado')
							           OR UPPER(l.cidade) =  UPPER('Rio Grande')
							           OR UPPER(l.cidade) =  UPPER('Turucu')
							           OR UPPER(l.cidade) =  UPPER('JAGUARAO'))
                            ";
                    break;						
					
					
                case "FNPE": // Família Previdência - Não Participantes - Região PELOTAS - OS: 30980

                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t, 
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND (UPPER(p.cidade) =  UPPER('PELOTAS')
									OR UPPER(p.cidade) =  UPPER('Arroio Grande')
									OR UPPER(p.cidade) =  UPPER('Cangucu')
									OR UPPER(p.cidade) =  UPPER('Capao do Leao')
									OR UPPER(p.cidade) =  UPPER('Herval do Sul')
									OR UPPER(p.cidade) =  UPPER('Morro Redondo')
									OR UPPER(p.cidade) =  UPPER('Pedro Osorio')
									OR UPPER(p.cidade) =  UPPER('Piratini')
									OR UPPER(p.cidade) =  UPPER('Pinheiro Machado')
									OR UPPER(p.cidade) =  UPPER('Rio Grande')
									OR UPPER(p.cidade) =  UPPER('Turucu')
									OR UPPER(p.cidade) =  UPPER('JAGUARAO'))

								UNION	

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(c.cidade) =  UPPER('PELOTAS')
									OR UPPER(c.cidade) =  UPPER('Arroio Grande')
									OR UPPER(c.cidade) =  UPPER('Cangucu')
									OR UPPER(c.cidade) =  UPPER('Capao do Leao')
									OR UPPER(c.cidade) =  UPPER('Herval do Sul')
									OR UPPER(c.cidade) =  UPPER('Morro Redondo')
									OR UPPER(c.cidade) =  UPPER('Pedro Osorio')
									OR UPPER(c.cidade) =  UPPER('Piratini')
									OR UPPER(c.cidade) =  UPPER('Pinheiro Machado')
									OR UPPER(c.cidade) =  UPPER('Rio Grande')
									OR UPPER(c.cidade) =  UPPER('Turucu')
									OR UPPER(c.cidade) =  UPPER('JAGUARAO'))								

                            ";
                    break;					
					
                case "FNRI": // Família Previdência - Não Participantes - Região RIO GRANDE - OS: 30980

                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t, 
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND (UPPER(p.cidade) =  UPPER('RIO GRANDE')
									OR UPPER(p.cidade) =  UPPER('SANTA VITORIA DO PALMAR')
									OR UPPER(p.cidade) =  UPPER('PEDRO OSORIO')
									OR UPPER(p.cidade) =  UPPER('PIRATINI')
									OR UPPER(p.cidade) =  UPPER('SAO JOSE DO NORTE')
									OR UPPER(p.cidade) =  UPPER('CHUI'))

								UNION	

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(c.cidade) =  UPPER('RIO GRANDE')
									OR UPPER(c.cidade) =  UPPER('SANTA VITORIA DO PALMAR')
									OR UPPER(c.cidade) =  UPPER('PEDRO OSORIO')
									OR UPPER(c.cidade) =  UPPER('PIRATINI')
									OR UPPER(c.cidade) =  UPPER('SAO JOSE DO NORTE')
									OR UPPER(c.cidade) =  UPPER('CHUI'))								

                            ";
                    break;	
                
                case "FNRI": // Família Previdência - Não Participantes - Região RIO GRANDE - OS: 30980

                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t, 
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND (UPPER(p.cidade) =  UPPER('RIO GRANDE')
									OR UPPER(p.cidade) =  UPPER('SANTA VITORIA DO PALMAR')
									OR UPPER(p.cidade) =  UPPER('PEDRO OSORIO')
									OR UPPER(p.cidade) =  UPPER('PIRATINI')
									OR UPPER(p.cidade) =  UPPER('SAO JOSE DO NORTE')
									OR UPPER(p.cidade) =  UPPER('CHUI'))

								UNION	

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(c.cidade) =  UPPER('RIO GRANDE')
									OR UPPER(c.cidade) =  UPPER('SANTA VITORIA DO PALMAR')
									OR UPPER(c.cidade) =  UPPER('PEDRO OSORIO')
									OR UPPER(c.cidade) =  UPPER('PIRATINI')
									OR UPPER(c.cidade) =  UPPER('SAO JOSE DO NORTE')
									OR UPPER(c.cidade) =  UPPER('CHUI'))								

                            ";
                    break;

                case "FNCH": // Família Previdência - Não participantes (Interessados, Participantes: CEEE, RGE, CGTEEE, AES SUL) - Região Cachoeira do Sul - OS: 32625 

                    $sqlp = "
                        SELECT p.cd_plano,
                               p.cd_empresa,
                               p.cd_registro_empregado,
                               p.seq_dependencia,
                               p.nome,
                               p.email,
                               p.email_profissional,
                               funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
                          FROM titulares AS t, 
                               participantes AS p
                         WHERE p.cd_empresa IN (0,1,2,3)
                           AND t.cd_empresa            = p.cd_empresa
                           AND t.cd_registro_empregado = p.cd_registro_empregado
                           AND t.seq_dependencia       = p.seq_dependencia
                           AND p.seq_dependencia       = 0
                           AND p.cd_plano > 0
                           AND p.dt_obito IS NULL
                           AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
                           AND (UPPER(p.cidade) =  UPPER('CACHOEIRA DO SUL')
                                OR UPPER(p.cidade) =  UPPER('CAÇAPAVA DO SUL'))
                                Cachoeira do Sul

                        UNION	

                        SELECT NULL AS cd_plano,
                                   c.cd_empresa,
                                   c.cd_registro_empregado,
                                   c.seq_dependencia,
                                   c.nome,
                                   c.email,
                                   NULL AS email_profissional,
                                   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
                          FROM familia_previdencia.cadastro c
                         WHERE c.email LIKE '%@%'
                           AND (UPPER(c.cidade) =  UPPER('CACHOEIRA DO SUL')
                                OR UPPER(c.cidade) =  UPPER('CAÇAPAVA DO SUL'))								

                            ";
                    break;	
                
                case "FNSA": // Família Previdência - Não participantes (Interessados, Participantes: Todas empresas) - Região Santo Ângelo - OS: 32268 

                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t, 
									   participantes AS p
								 WHERE t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND (UPPER(p.cidade) =  UPPER('ROQUE GONZALES')
									OR UPPER(p.cidade) =  UPPER('CERRO LARGO')
									OR UPPER(p.cidade) =  UPPER('ENTRE IJUIS')
									OR UPPER(p.cidade) =  UPPER('CATUIPE')
                                    OR UPPER(p.cidade) =  UPPER('SAO LUIZ GONZAGA')
                                    OR UPPER(p.cidade) =  UPPER('GIRUA')
                                    OR UPPER(p.cidade) =  UPPER('SANTO ANGELO')
                                    )

								UNION	

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(c.cidade) =  UPPER('ROQUE GONZALES')
									OR UPPER(c.cidade) =  UPPER('CERRO LARGO')
									OR UPPER(c.cidade) =  UPPER('ENTRE IJUIS')
									OR UPPER(c.cidade) =  UPPER('CATUIPE')
                                    OR UPPER(c.cidade) =  UPPER('SAO LUIZ GONZAGA')
                                    OR UPPER(c.cidade) =  UPPER('GIRUA')
                                    OR UPPER(c.cidade) =  UPPER('SANTO ANGELO')
                                    )							

                            ";
                    break;	
					
					
					case "DI13": // Palestra 2013 - São Leopoldo: Aposentados, Pensionistas, Ex.Autárquico, CTP, Ativos - OS: 36649  
                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t, 
									   participantes AS p
								 WHERE t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.dt_obito IS NULL
								   AND projetos.participante_tipo(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN ('APOS','EXAU','CTP','AUXD','ATIV','PENS')
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND (UPPER(p.cidade) =  UPPER('SAO LEOPOLDO')
									OR UPPER(p.cidade) =  UPPER('CANOAS')
									OR UPPER(p.cidade) =  UPPER('ESTEIO')
									OR UPPER(p.cidade) =  UPPER('SAPUCAIA DO SUL')
									OR UPPER(p.cidade) =  UPPER('BOM PRINCIPIO')
									OR UPPER(p.cidade) =  UPPER('CAPELA SANTANA')
									OR UPPER(p.cidade) =  UPPER('FELIZ')
									OR UPPER(p.cidade) =  UPPER('PORTAO')
									OR UPPER(p.cidade) =  UPPER('NOVA SANTA RITA')
                                    )						
                            ";
                    break;					
					
					case "PL12": // Palestra 2012 - São Leopoldo: ativos, aposentados , pensionistas, ex autárquicos e CTPs de todos os planos e não participantes - OS: 33862  
                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t, 
									   participantes AS p
								 WHERE t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND (UPPER(p.cidade) =  UPPER('SAO LEOPOLDO')
									OR UPPER(p.cidade) =  UPPER('NOVO HAMBURGO')
									OR UPPER(p.cidade) =  UPPER('CAMPO BOM')
									OR UPPER(p.cidade) =  UPPER('CANOAS')
                                    OR UPPER(p.cidade) =  UPPER('ESTEIO')
                                    OR UPPER(p.cidade) =  UPPER('ESTANCIA VELHA')
                                    OR UPPER(p.cidade) =  UPPER('SAPIRANGA')
									OR UPPER(p.cidade) =  UPPER('SAPUCAIA DO SUL')
									OR UPPER(p.cidade) =  UPPER('DOIS IRMAOS')
                                    )						

                            ";
                    break;	
					
					case "PLCA": // Ativos, Aposentados e Pensionistas (Sem RGE): Região CAXIAS DO SUL - OS: 35217  
                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t, 
									   participantes AS p
								 WHERE t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND p.cd_empresa NOT IN (1)
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND (UPPER(p.cidade) =  UPPER('CAXIAS DO SUL')
									OR UPPER(p.cidade) =  UPPER('FARROUPILHA')
									OR UPPER(p.cidade) =  UPPER('SAO MARCOS')
									OR UPPER(p.cidade) =  UPPER('FLORES DA CUNHA')
									OR UPPER(p.cidade) =  UPPER('NOVA PETROPOLIS')
									OR UPPER(p.cidade) =  UPPER('BENTO GONCALVES')
									OR UPPER(p.cidade) =  UPPER('CARLOS BARBOSA')
									OR UPPER(p.cidade) =  UPPER('GARIBALDI')
									OR UPPER(p.cidade) =  UPPER('NOVA PRATA')
									OR UPPER(p.cidade) =  UPPER('VERANOPOLIS')
									OR UPPER(p.cidade) =  UPPER('ANTONIO PRADO')
									OR UPPER(p.cidade) =  UPPER('VACARIA')
									OR UPPER(p.cidade) =  UPPER('BOM JESUS')
									OR UPPER(p.cidade) =  UPPER('GUAPORE')
									OR UPPER(p.cidade) =  UPPER('SERAFINA CORREA')
                                    )						
                            ";
                    break;	

					case "PLPF": // Ativos, Aposentados e Pensionistas (Sem RGE): Região PASSO FUNDO - OS: 35256  
                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t, 
									   participantes AS p
								 WHERE t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND p.cd_empresa NOT IN (1)
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND (UPPER(p.cidade) =  UPPER('PASSO FUNDO')
									OR UPPER(p.cidade) =  UPPER('ERECHIM')
									OR UPPER(p.cidade) =  UPPER('NAO ME TOQUE')
									OR UPPER(p.cidade) =  UPPER('TIO HUGO')
									OR UPPER(p.cidade) =  UPPER('TAPERA')
                                    )						
                            ";
                    break;						

                
                case "FNSR": // Família Previdência - Não participantes (Interessados, Participantes - Todas empresas) - Região Santa Rosa - OS: 32268 

                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t, 
									   participantes AS p
								 WHERE t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND (UPPER(p.cidade) =  UPPER('SANTA ROSA')
									OR UPPER(p.cidade) =  UPPER('HORIZONTINA')
									OR UPPER(p.cidade) =  UPPER('TENENTE PORTELA')
									OR UPPER(p.cidade) =  UPPER('CORONEL BICACO')
                                    OR UPPER(p.cidade) =  UPPER('TUPARENDI')
                                    OR UPPER(p.cidade) =  UPPER('FREDERICO WESTPHALEN')
                                    OR UPPER(p.cidade) =  UPPER('IJUI')
                                    )

								UNION	

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(c.cidade) =  UPPER('SANTA ROSA')
									OR UPPER(c.cidade) =  UPPER('HORIZONTINA')
									OR UPPER(c.cidade) =  UPPER('TENENTE PORTELA')
									OR UPPER(c.cidade) =  UPPER('CORONEL BICACO')
                                    OR UPPER(c.cidade) =  UPPER('TUPARENDI')
                                    OR UPPER(c.cidade) =  UPPER('FREDERICO WESTPHALEN')
                                    OR UPPER(c.cidade) =  UPPER('IJUI')
                                    )							

                            ";
                    break;	

                case "FNSL": // Família Previdência - Não Participantes - Região SAO LEOPOLDO - OS: 30980

                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t, 
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND (UPPER(p.cidade) =  UPPER('SAO LEOPOLDO')
									OR UPPER(p.cidade) =  UPPER('SAPUCAIA DO SUL'))

								UNION	

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(c.cidade) =  UPPER('SAO LEOPOLDO')
									OR UPPER(c.cidade) =  UPPER('SAPUCAIA DO SUL'))															

                            ";
                    break;

                case "FNSJ": // Família Previdência - Não Participantes - Região SAO JERONIMO - OS: 31239
                    $sqlp = "
                                 SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t,
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
								   AND (UPPER(p.cidade) =  UPPER('SAO JERONIMO')
									OR UPPER(p.cidade) =  UPPER('GENERAL CAMARA')
									OR UPPER(p.cidade) =  UPPER('BUTIA')
									OR UPPER(p.cidade) =  UPPER('ARROIO DOS RATOS')
									OR UPPER(p.cidade) =  UPPER('MINAS DO LEAO')
									OR UPPER(p.cidade) =  UPPER('CHARQUEADAS')
									)

								UNION

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(c.cidade) =  UPPER('SAO JERONIMO')
									OR UPPER(c.cidade) =  UPPER('GENERAL CAMARA')
									OR UPPER(c.cidade) =  UPPER('BUTIA')
									OR UPPER(c.cidade) =  UPPER('ARROIO DOS RATOS')
									OR UPPER(c.cidade) =  UPPER('MINAS DO LEAO')
									OR UPPER(c.cidade) =  UPPER('CHARQUEADAS'))
						";

                    break;

                case "FNGR": // Família Previdência - Não Participantes - Região GRAVATAI - OS: 31239
                    $sqlp = "
                                 SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t,
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
								   AND (UPPER(p.cidade) =  UPPER('GRAVATAI')
									OR UPPER(p.cidade) =  UPPER('ALVORADA')
									OR UPPER(p.cidade) =  UPPER('CACHOEIRINHA')
									)

								UNION

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(c.cidade) =  UPPER('GRAVATAI')
									OR UPPER(c.cidade) =  UPPER('ALVORADA')
									OR UPPER(c.cidade) =  UPPER('CACHOEIRINHA')
                                    )
						";

                    break;

                case "FNCS": // Família Previdência - Não Participantes - Região CAXIAS DO SUL - OS: 31239
                    $sqlp = "
                                 SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t,
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
								   AND (UPPER(p.cidade) =  UPPER('CAXIAS DO SUL')
									OR UPPER(p.cidade) =  UPPER('SAO MARCOS')
									OR UPPER(p.cidade) =  UPPER('FLORES DA CUNHA')
                                    OR UPPER(p.cidade) =  UPPER('FARROUPILHA')
                                    OR UPPER(p.cidade) =  UPPER('NOVA PETROPOLIS')
									)

								UNION

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(c.cidade) =  UPPER('CAXIAS DO SUL')
									OR UPPER(c.cidade) =  UPPER('SAO MARCOS')
									OR UPPER(c.cidade) =  UPPER('FLORES DA CUNHA')
                                    OR UPPER(c.cidade) =  UPPER('FARROUPILHA')
                                    OR UPPER(c.cidade) =  UPPER('NOVA PETROPOLIS')
                                    )
						";

                    break;

                case "FNVA": // Família Previdência - Não Participantes - Região VACARIA - OS: 31239
                    $sqlp = "
                                 SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t,
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
								   AND (UPPER(p.cidade) =  UPPER('VACARIA')
									OR UPPER(p.cidade) =  UPPER('BOM JESUS')
									OR UPPER(p.cidade) =  UPPER('ANTONIO PRADO')
                                    OR UPPER(p.cidade) =  UPPER('LAGOA VERMELHA')
									)

								UNION

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(c.cidade) =  UPPER('VACARIA')
									OR UPPER(c.cidade) =  UPPER('BOM JESUS')
									OR UPPER(c.cidade) =  UPPER('ANTONIO PRADO')
                                    OR UPPER(c.cidade) =  UPPER('LAGOA VERMELHA')
                                    )
						";

                    break;

                case "FNNP": // Família Previdência - Não Participantes - Região NOVA PRATA - OS: 31239
                    $sqlp = "
                                 SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t,
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
								   AND (UPPER(p.cidade) =  UPPER('NOVA PRATA')
									OR UPPER(p.cidade) =  UPPER('VERANOPOLIS')
									OR UPPER(p.cidade) =  UPPER('GUAPORE')
                                    OR UPPER(p.cidade) =  UPPER('PARAI')
                                    OR UPPER(p.cidade) =  UPPER('VILA FLORES')
									)

								UNION

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(c.cidade) =  UPPER('NOVA PRATA')
									OR UPPER(c.cidade) =  UPPER('VERANOPOLIS')
									OR UPPER(c.cidade) =  UPPER('GUAPORE')
                                    OR UPPER(c.cidade) =  UPPER('PARAI')
                                    OR UPPER(c.cidade) =  UPPER('VILA FLORES')
                                    )
						";

                    break;

                case "FNBG": // Família Previdência - Não Participantes - Região BENTO GONCALVES - OS: 31239
                    $sqlp = "
                                 SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t,
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
								   AND (UPPER(p.cidade) =  UPPER('BENTO GONCALVES')
									OR UPPER(p.cidade) =  UPPER('GARIBALDI')
									OR UPPER(p.cidade) =  UPPER('CARLOS BARBOSA')
									)
                                    
								UNION

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(c.cidade) =  UPPER('BENTO GONCALVES')
									OR UPPER(c.cidade) =  UPPER('GARIBALDI')
									OR UPPER(c.cidade) =  UPPER('CARLOS BARBOSA')
                                    )
						";

                    break;

                case "FNOS": // Família Previdência - Não Participantes - Região OSORIO - OS: 30980

                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t, 
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND (UPPER(p.cidade) =  UPPER('Osorio')
										OR UPPER(p.cidade) =  UPPER('Arroio do Sal')
										OR UPPER(p.cidade) =  UPPER('Areias Brancas')
										OR UPPER(p.cidade) =  UPPER('Balneario Pinhal')
										OR UPPER(p.cidade) =  UPPER('Cidreira')
										OR UPPER(p.cidade) =  UPPER('Capao da Canoa')
										OR UPPER(p.cidade) =  UPPER('Maquine')
										OR UPPER(p.cidade) =  UPPER('Palmares do Sul')
										OR UPPER(p.cidade) =  UPPER('Passo de Torres')
										OR UPPER(p.cidade) =  UPPER('Quintao')
										OR UPPER(p.cidade) =  UPPER('Santo Antonio da Patrulha')
										OR UPPER(p.cidade) =  UPPER('Torres')
										OR UPPER(p.cidade) =  UPPER('Terra de Areia')
										OR UPPER(p.cidade) =  UPPER('Tramandai')
										OR UPPER(p.cidade) =  UPPER('Xangri-la')
										OR UPPER(p.cidade) =  UPPER('Xangrila')
										OR UPPER(p.cidade) =  UPPER('Capao Novo')
										OR UPPER(p.cidade) =  UPPER('Mariluz')
										OR UPPER(p.cidade) =  UPPER('Magisterio'))

								UNION	

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(c.cidade) =  UPPER('Osorio')
										OR UPPER(c.cidade) =  UPPER('Arroio do Sal')
										OR UPPER(c.cidade) =  UPPER('Areias Brancas')
										OR UPPER(c.cidade) =  UPPER('Balneario Pinhal')
										OR UPPER(c.cidade) =  UPPER('Cidreira')
										OR UPPER(c.cidade) =  UPPER('Capao da Canoa')
										OR UPPER(c.cidade) =  UPPER('Maquine')
										OR UPPER(c.cidade) =  UPPER('Palmares do Sul')
										OR UPPER(c.cidade) =  UPPER('Passo de Torres')
										OR UPPER(c.cidade) =  UPPER('Quintao')
										OR UPPER(c.cidade) =  UPPER('Santo Antonio da Patrulha')
										OR UPPER(c.cidade) =  UPPER('Torres')
										OR UPPER(c.cidade) =  UPPER('Terra de Areia')
										OR UPPER(c.cidade) =  UPPER('Tramandai')
										OR UPPER(c.cidade) =  UPPER('Xangri-la')
										OR UPPER(c.cidade) =  UPPER('Xangrila')
										OR UPPER(c.cidade) =  UPPER('Capao Novo')
										OR UPPER(c.cidade) =  UPPER('Mariluz')
										OR UPPER(c.cidade) =  UPPER('Magisterio'))														

                            ";
                    break;

                case "FNCA": // Família Previdência - Não Participantes - Região CANOAS - OS: 30980

                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t,
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
								   AND (UPPER(p.cidade) =  UPPER('CANOAS')
										OR UPPER(p.cidade) =  UPPER('ESTEIO')
										OR UPPER(p.cidade) =  UPPER('SAPUCAIA DO SUL')
										OR UPPER(p.cidade) =  UPPER('SAO LEOPOLDO')
										OR UPPER(p.cidade) =  UPPER('NOVO HAMBURGO')
										OR UPPER(p.cidade) =  UPPER('NOVA SANTA RITA')
										OR UPPER(p.cidade) =  UPPER('ESTANCIA VELHA')
										OR UPPER(p.cidade) =  UPPER('CAMPO BOM')
										OR UPPER(p.cidade) =  UPPER('SAPIRANGA')
										OR UPPER(p.cidade) =  UPPER('PAROBE')
										OR UPPER(p.cidade) =  UPPER('DOIS IRMAOS')
										OR UPPER(p.cidade) =  UPPER('NOVA HARTZ'))

								UNION

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(c.cidade) =  UPPER('CANOAS')
										OR UPPER(c.cidade) =  UPPER('ESTEIO')
										OR UPPER(c.cidade) =  UPPER('SAPUCAIA DO SUL')
										OR UPPER(c.cidade) =  UPPER('SAO LEOPOLDO')
										OR UPPER(c.cidade) =  UPPER('NOVO HAMBURGO')
										OR UPPER(c.cidade) =  UPPER('NOVA SANTA RITA')
										OR UPPER(c.cidade) =  UPPER('ESTANCIA VELHA')
										OR UPPER(c.cidade) =  UPPER('CAMPO BOM')
										OR UPPER(c.cidade) =  UPPER('SAPIRANGA')
										OR UPPER(c.cidade) =  UPPER('PAROBE')
										OR UPPER(c.cidade) =  UPPER('DOIS IRMAOS')
										OR UPPER(c.cidade) =  UPPER('NOVA HARTZ'))

                            ";
                    break;

                case "FNCL": // Família Previdência - Não Participantes - Região CANELA - OS: 31453

                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t,
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
								   AND (UPPER(p.cidade) =  UPPER('CANELA')
										OR UPPER(p.cidade) =  UPPER('GRAMADO')
										OR UPPER(p.cidade) =  UPPER('SAO FRANCISCO DE PAULA'))

								UNION

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(c.cidade) =  UPPER('CANELA')
										OR UPPER(c.cidade) =  UPPER('GRAMADO')
										OR UPPER(c.cidade) =  UPPER('SAO FRANCISCO DE PAULA'))

                            ";
                    break;

                case "FNCT": // Família Previdência - Não Participantes - Região CRUZ ALTA - OS: 31453

                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t,
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
								   AND (UPPER(p.cidade) =  UPPER('CRUZ ALTA')
										OR UPPER(p.cidade) =  UPPER('JULIO DE CASTILHOS')
										OR UPPER(p.cidade) =  UPPER('IBIRUBA')
                                        OR UPPER(p.cidade) =  UPPER('SANTA BARBARA DO SUL'))

								UNION

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(c.cidade) =  UPPER('CRUZ ALTA')
										OR UPPER(c.cidade) =  UPPER('JULIO DE CASTILHOS')
										OR UPPER(c.cidade) =  UPPER('IBIRUBA')
                                        OR UPPER(c.cidade) =  UPPER('SANTA BARBARA DO SUL'))

                            ";
                    break;

                case "FNTA": // Família Previdência - Não Participantes - Região TAQUARA - OS: 31453

                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t,
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
								   AND (UPPER(p.cidade) =  UPPER('TAQUARA')
										OR UPPER(p.cidade) =  UPPER('IGREJINHA')
										OR UPPER(p.cidade) =  UPPER('TRES COROAS')
                                        OR UPPER(p.cidade) =  UPPER('ROLANET')
                                        OR UPPER(p.cidade) =  UPPER('PAROBE'))

								UNION

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(c.cidade) =  UPPER('TAQUARA')
										OR UPPER(c.cidade) =  UPPER('IGREJINHA')
										OR UPPER(c.cidade) =  UPPER('TRES COROAS')
                                        OR UPPER(c.cidade) =  UPPER('ROLANET')
                                        OR UPPER(c.cidade) =  UPPER('PAROBE'))

                            ";
                    break;
					
                case "FNRS": // Família Previdência - Não participantes (Interessados, Participantes: CEEE, RGE, CGTEEE, AES SUL) - Rosário do Sul - OS: 32845
                    $sqlp = "
                                 SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t,
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
								   AND (UPPER(funcoes.remove_acento(p.cidade)) =  UPPER('ROSARIO DO SUL'))
                                    
								UNION

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(funcoes.remove_acento(c.cidade)) =  UPPER('ROSARIO DO SUL'))
						";
                    break;	

                case "FNUR": // Família Previdência - Não participantes (Interessados, Participantes: CEEE, RGE, CGTEEE, AES SUL) - Região Uruguaiana - OS: 32845
                    $sqlp = "
                                 SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t,
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
								   AND (
										UPPER(funcoes.remove_acento(p.cidade)) =  UPPER('ROSARIO DO SUL')
										OR
										UPPER(funcoes.remove_acento(p.cidade)) =  UPPER('ITAQUI')
								       )
                                    
								UNION

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (
										UPPER(funcoes.remove_acento(c.cidade)) =  UPPER('ROSARIO DO SUL')
										OR
										UPPER(funcoes.remove_acento(c.cidade)) =  UPPER('ITAQUI')
								       )
						";
                    break;					
					
                case "FNSV": // Família Previdência - Não participantes (Interessados, Participantes: CEEE, RGE, CGTEEE, AES SUL) - Santana do Livramento - OS: 32845
                    $sqlp = "
                                 SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t,
									   participantes AS p
								 WHERE p.cd_empresa IN (0,1,2,3)
								   AND t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano > 0
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
								   AND (UPPER(funcoes.remove_acento(p.cidade)) =  UPPER('SANTANA DO LIVRAMENTO'))
                                    
								UNION

								SELECT NULL AS cd_plano,
									   c.cd_empresa,
									   c.cd_registro_empregado,
									   c.seq_dependencia,
									   c.nome,
									   c.email,
									   NULL AS email_profissional,
									   funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) AS re_cripto
								  FROM familia_previdencia.cadastro c
								 WHERE c.email LIKE '%@%'
								   AND (UPPER(funcoes.remove_acento(c.cidade)) =  UPPER('SANTANA DO LIVRAMENTO'))
						";
                    break; 		
					
                case "COIN": // CONTATO INSTITUCIONAL

                    $sqlp = "
								SELECT NULL AS cd_plano,
									   NULL AS cd_empresa,
									   NULL AS cd_registro_empregado,
									   NULL AS seq_dependencia,
									   UPPER(funcoes.remove_acento(ci.nome)) AS nome,
									   COALESCE(ci.email_1,ci.email_2) AS email,
									   COALESCE(COALESCE(ci.sec_email_1,ci.sec_email_2),ci.email_2) AS email_profissional,
									   NULL AS re_cripto
								  FROM projetos.contato_institucional ci
								 WHERE ci.dt_exclusao IS NULL
								 ORDER BY nome 
							";
                    break;					
					
                case "CUTR": // CUTRS - Sindicatos filiados (04/10/2012) - OS 35563

                    $sqlp = "
								SELECT NULL AS cd_plano,
									   NULL AS cd_empresa,
									   NULL AS cd_registro_empregado,
									   NULL AS seq_dependencia,
									   NULL AS nome,
									   email,
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM temporario.os35563
							";
                    break;					
					

                case "C712": // CEEE - Pesquisa Não participantes (16/07/2012) - OS 34914

                    $sqlp = "
								SELECT NULL AS cd_plano,
									   NULL AS cd_empresa,
									   NULL AS cd_registro_empregado,
									   NULL AS seq_dependencia,
									   nome,
									   email,
									   email_profissional,
									   NULL AS re_cripto
								  FROM temporario.os34914
							";
                    break;

                case "C812": // CEEE - Pesquisa Não participantes (20/07/2012) - OS 34958

                    $sqlp = "
								SELECT NULL AS cd_plano,
									   NULL AS cd_empresa,
									   NULL AS cd_registro_empregado,
									   NULL AS seq_dependencia,
									   nome,
									   email,
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM temporario.os34958
							";
                    break;					
					

                case "CS2A": // CEEE - POA - SEM PLANO (20/11/2008) - OS 18816

                    $sqlp = "
								SELECT NULL AS cd_plano,
									   NULL AS cd_empresa,
									   NULL AS cd_registro_empregado,
									   NULL AS seq_dependencia,
									   NULL AS nome,
									   email,
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM temporario.os18816
							";
                    break;		

               case "CG12": // CGTEE - Candiota (Planilha 22/06/2012) - OS 34628

                    $sqlp = "
								SELECT NULL AS cd_plano,
									   NULL AS cd_empresa,
									   NULL AS cd_registro_empregado,
									   NULL AS seq_dependencia,
									   nome,
									   email,
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM temporario.os34628
							";
                    break;						
					
					
                case "CS2B": // Ativos CEEE D.xls (13/04/2010) - OS 25535

                    $sqlp = "
								SELECT NULL AS cd_plano,
									   NULL AS cd_empresa,
									   NULL AS cd_registro_empregado,
									   NULL AS seq_dependencia,
									   nome,
									   email,
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM temporario.ceee_d_13042010
							";
                    break;	
					
                case "CS4B": // "CEEE - Não Participantes - 18 a 30 anos (planilha excel - 29/09/2011) - OS 32351" 817_Nao_Participantes___18_a_30_anos.xls

                    $sqlp = "
								SELECT NULL AS cd_plano,
									   cd_empresa,
									   cd_registro_empregado,
									   seq_dependencia,
									   nome,
									   email,
									   email_profissional,
									   funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) AS re_cripto
								  FROM temporario.os32351
								 ORDER BY nome
							";
                    break;						
					
                case "CS5B": // "CEEE - Não Participantes - 18 a 30 anos (planilha excel - 29/09/2011) - Não responderam pesquisa 290 - OS 32351" 817_Nao_Participantes___18_a_30_anos.xls

                    $sqlp = "
								SELECT NULL AS cd_plano,
									   cd_empresa,
									   cd_registro_empregado,
									   seq_dependencia,
									   nome,
									   email,
									   email_profissional,
									   funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) AS re_cripto
								  FROM temporario.os32351
								  WHERE funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) NOT IN (SELECT ip
										                                                                                FROM projetos.enquete_resultados
										                                                                               WHERE cd_enquete = 290
										                                                                               GROUP BY ip)								  
								 ORDER BY nome
							";
                    break;					
					
                case "CS3B": // Ativos (Salto do Jacuí, Sobradinho e Panambi) Planilha importada 15/09/2011 - OS 32214

                    $sqlp = "
								SELECT NULL AS cd_plano,
									   NULL AS cd_empresa,
									   NULL AS cd_registro_empregado,
									   NULL AS seq_dependencia,
									   nome,
									   email,
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM temporario.os32214
							";
                    break;						

                case "CS2C": // Ativos CEEE GT.xls (13/04/2010) - OS 25535

                    $sqlp = "
								SELECT NULL AS cd_plano,
									   NULL AS cd_empresa,
									   NULL AS cd_registro_empregado,
									   NULL AS seq_dependencia,
									   nome,
									   email,
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM temporario.ceee_gt_13042010
							";
                    break;					
					
                case "CRH1": //Congregarh 2011 - visitantes do stand (24/05/2011) - OS 31156

                    $sqlp = "
								SELECT NULL AS cd_plano,
									   NULL AS cd_empresa,
									   NULL AS cd_registro_empregado,
									   NULL AS seq_dependencia,
									   NULL AS nome,
									   email,
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM temporario.os31156
							";
                    break;						

                case "ABP1": // RelacaodeAssociadas05-2010.XLS.xls (10/05/2010) - OS 26034

                    $sqlp = "
								SELECT NULL AS cd_plano,
									   NULL AS cd_empresa,
									   NULL AS cd_registro_empregado,
									   NULL AS seq_dependencia,
									   nome,
									   email,
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM acs.contatos_abrap_05_2010
							";
                    break;
					
				
					
                case "2DIA": //  2º DIALOGO INSTITUCIONAL - OS 26384
                    $sqlp = "
								SELECT NULL AS cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM acs.dialogo_inscricao p
								 WHERE p.email       LIKE '%@%'
								   AND p.dt_exclusao IS NULL
								   AND p.cd_dialogo  = 1
							";
                    break;		
					
                case "3DIA": //  3º DIALOGO INSTITUCIONAL INSCRITOS
                    $sqlp = "
								SELECT NULL AS cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   MD5(p.cd_dialogo_inscricao::TEXT) AS re_cripto
								  FROM acs.dialogo_inscricao p
								 WHERE p.email       LIKE '%@%'
								   AND p.dt_exclusao IS NULL
								   AND p.cd_dialogo  = 2
							";
                    break;	
					
                case "3DIP": //  3º DIALOGO INSTITUCIONAL PRESENTES
                    $sqlp = "
								SELECT NULL AS cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   UPPER(funcoes.remove_acento(p.nome)) AS nome, 
									   p.email, 
									   MD5(p.cd_dialogo_inscricao::TEXT)  AS re_cripto
								  FROM acs.dialogo_inscricao p
								 WHERE p.email       LIKE '%@%'
								   AND p.dt_exclusao IS NULL
								   AND p.cd_dialogo  = 2
								   AND p.fl_presente = 'S'
							";
                    break;							
					
                case "4DIA": //  4º DIALOGO INSTITUCIONAL INSCRITOS
                    $sqlp = "
								SELECT NULL AS cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   MD5(p.cd_dialogo_inscricao::TEXT) AS re_cripto
								  FROM acs.dialogo_inscricao p
								 WHERE p.email       LIKE '%@%'
								   AND p.dt_exclusao IS NULL
								   AND p.cd_dialogo  = 3
							";
                    break;	

                case "4DIP": //  4º DIALOGO INSTITUCIONAL INSCRITOS - PRESENTES
                    $sqlp = "
								SELECT NULL AS cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   MD5(p.cd_dialogo_inscricao::TEXT) AS re_cripto
								  FROM acs.dialogo_inscricao p
								 WHERE p.email       LIKE '%@%'
								   AND p.dt_exclusao IS NULL
								   AND p.cd_dialogo  = 3
								   AND p.fl_presente = 'S'
							";
                    break;						
					
					
                case "CGAT": // CGTEE ATIVOS
                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
							           funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t, 
									   participantes AS p
								 WHERE p.cd_empresa = 3
								   AND t.cd_empresa = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia = p.seq_dependencia
								   AND p.seq_dependencia = 0
								   AND p.cd_plano > 0
								   AND (p.tipo_folha IN (8, 13) OR (p.tipo_folha = 6 AND t.tipo_aposentado = 13))
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							";
                    break;		

                case "CGAS": // CGTEE ATIVOS (SEM CTPS)
                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
							           funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t, 
									   participantes AS p
								 WHERE p.cd_empresa = 3
								   AND t.cd_empresa = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia = p.seq_dependencia
								   AND p.seq_dependencia = 0
								   AND p.cd_plano > 0
								   AND p.tipo_folha IN (8, 13)
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							";
                    break;	

                case "CGAC": // CGTEE ATIVOS (SOMENTE CTPS)
                    $sqlp = "
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
							           funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t, 
									   participantes AS p
								 WHERE p.cd_empresa = 3
								   AND t.cd_empresa = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia = p.seq_dependencia
								   AND p.seq_dependencia = 0
								   AND p.cd_plano > 0
								   AND p.tipo_folha = 6 
								   AND t.tipo_aposentado = 13
								   AND p.dt_obito IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							";
                    break;					
					

                case "CSR1": // RGE - REGIAO CAXIAS IBGE (09/01/2009) - Daniele GRI
                    $sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado,
								   p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
							       funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.participantes p
							 WHERE p.cd_empresa     = 1
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
							   AND p.dt_obito        IS NULL
							   AND p.cidade  IN ( SELECT c.nome_cidade
							                        FROM expansao.cidades c
							                       WHERE c.sigla_uf        = 'RS'
							                         AND c.cd_macroregiao  = (SELECT c1.cd_macroregiao
							                                                    FROM expansao.cidades c1
							                                                   WHERE c1.sigla_uf       = 'RS'
							                                                     AND c1.nome_cidade    = 'CAXIAS DO SUL'))                                                       
							";
                    break;	


                case "SG09": // SEMINARIO DE SEGURIDADE 2009
                    $sqlp = "
							SELECT NULL AS cd_plano,
								   cd_empresa,
								   cd_registro_empregado,
								   seq_dependencia,
								   nome,
								   email,
								   NULL AS email_profissional,
								   funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) AS re_cripto
							  FROM acs.seminario_seguridade
							 WHERE dt_exclusao IS NULL
							   AND nr_ano_edicao = 2009
							   AND email LIKE '%@%'
							 ORDER BY nome ASC                                                      
							";
                    break;	
                case "SG10": // SEMINARIO DE SEGURIDADE 2010
                    $sqlp = "
							SELECT NULL AS cd_plano,
								   cd_empresa,
								   cd_registro_empregado,
								   seq_dependencia,
								   nome,
								   email,
								   NULL AS email_profissional,
								   funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) AS re_cripto
							  FROM acs.seminario_seguridade
							 WHERE dt_exclusao IS NULL
							   AND nr_ano_edicao = 2010
							   AND email LIKE '%@%'
							 ORDER BY nome ASC                                                      
							";
                    break;	
					
                case "SP10": // SEMINARIO DE SEGURIDADE 2010 - PRESENTES
                    $sqlp = "
							SELECT NULL AS cd_plano,
								   cd_empresa,
								   cd_registro_empregado,
								   seq_dependencia,
								   nome,
								   email,
								   NULL AS email_profissional,
								   MD5(CAST(cd_seminario_seguridade AS TEXT)) AS re_cripto
							  FROM acs.seminario_seguridade
							 WHERE dt_exclusao IS NULL
							   AND nr_ano_edicao = 2010
							   AND email LIKE '%@%'
							   AND fl_presente = 'S'
							 ORDER BY nome ASC                                                      
							";
                    break;						

                case "SG11": // SEMINARIO DE SEGURIDADE 2011 - OS: 31330
                    $sqlp = "
							SELECT NULL AS cd_plano,
								   cd_empresa,
								   cd_registro_empregado,
								   seq_dependencia,
								   nome,
								   email,
								   NULL AS email_profissional,
								   MD5(CAST(cd_seminario_seguridade AS TEXT)) AS re_cripto
							  FROM acs.seminario_seguridade
							 WHERE dt_exclusao IS NULL
							   AND nr_ano_edicao = 2011
							   AND email LIKE '%@%'
							 ORDER BY nome ASC                                                      
							";
                    break;	

                case "SP11": // SEMINARIO DE SEGURIDADE 2011 - PRESENTES
                    $sqlp = "
							SELECT NULL AS cd_plano,
								   cd_empresa,
								   cd_registro_empregado,
								   seq_dependencia,
								   nome,
								   email,
								   NULL AS email_profissional,
								   MD5(CAST(cd_seminario_seguridade AS TEXT)) AS re_cripto
							  FROM acs.seminario_seguridade
							 WHERE dt_exclusao IS NULL
							   AND nr_ano_edicao = 2011
							   AND email LIKE '%@%'
							   AND fl_presente = 'S'
							 ORDER BY nome ASC                                                      
							";
                    break;						


                case "PRAB": // PARTICIPANTES PALESTRA PREVENIR 30º CONGRESSO ABRAPP
                    $sqlp = "
							SELECT NULL AS cd_plano,
								   'DEFAULT' AS cd_empresa,
								   'DEFAULT' AS cd_registro_empregado,
								   'DEFAULT' AS seq_dependencia,
								   nome,
								   email_1 AS email,
								   email_2 AS email_profissional,
								   NULL AS re_cripto
							  FROM prevenir.contato_abrapp
							 ORDER BY nome ASC                                                      
							";
                    break;	

                case "PRSU": // PREVENIR SUGESTOES 
                    $sqlp = "
							SELECT DISTINCT(LOWER(ds_email)) AS email,
								   UPPER(funcoes.remove_acento(ds_nome)) AS nome,
								   NULL AS email_profissional,
								   NULL AS cd_plano,
								   'DEFAULT' AS cd_empresa,
								   'DEFAULT' AS cd_registro_empregado,
								   'DEFAULT' AS seq_dependencia,
								   NULL AS re_cripto
							  FROM prevenir.prevenir_formulario
							 WHERE COALESCE(ds_email,'') LIKE '%@%' 
							   AND dt_envio              IS NOT NULL
							 ORDER BY nome
							";
                    break;						
					
					


				case "CS1K": // CEEE - Geral
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito  IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 0 
							"; 
					break;		
					
					
				case "CEPE": // CEEE - Participantes - Pelotas (OS 34759)
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
					               p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND UPPER(p.cidade) =  UPPER('PELOTAS')
							   AND p.dt_obito  IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 0 
							"; 
					break;					
					

				case "CS1M": // RGE - Geral
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
							       p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito   IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 1 
							"; 
					break;	
					
				case "CS1L": // AES - Geral
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
							       p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito   IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 2 
							"; 
					break;	

				case "CS1N": // CGTEE - Geral
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
							       p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito   IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 3 
							"; 
					break;
					
				case "CS1O": // CRM - Geral
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
							       p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito   IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 6 
							"; 
					break;					
					
					

				case "CS1P": //Senge
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
							       p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito   IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 7 
							"; 
					break;	
				
				case "EX07": //Senge EXTRATO
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
							       p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.participantes p
							  JOIN public.titulares t 
							    ON t.cd_empresa            = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia       = p.seq_dependencia
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito   IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 7 
							   AND p.tipo_folha IN (8,17,18,19)
							"; 
					break;	

				case "SG01": //Senge - Formulário Atualizar Dependentes - OS: 33248
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
							       p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.participantes p
							  JOIN public.titulares t 
							    ON t.cd_empresa            = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia       = p.seq_dependencia
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito   IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 7 
							   AND p.tipo_folha IN (8,17,18,19)
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) NOT IN (SELECT DISTINCT sd.cd_empresa, sd.cd_registro_empregado, sd.seq_dependencia FROM expansao.senge_dependente sd)
							"; 
					break;						
					
				
				case "CS2S": //SINPRORS
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
							       p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito   IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 8 
					        ";
					break;	
					
				case "SPRI": //SINPRORS - Participantes com Risco (Invalidez e/ou Morte) (OS: 37691)
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
							       p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito   IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 8 
							   AND (projetos.participante_valor_contrib_risco(p.cd_empresa::integer, p.cd_registro_empregado::integer, p.seq_dependencia::integer, 'I') > 0 
                                    OR
                                    projetos.participante_valor_contrib_risco(p.cd_empresa::integer, p.cd_registro_empregado::integer, p.seq_dependencia::integer, 'M') > 0)							   
					        ";
					break;						
					
				case "EX08": //SINPRORS EXTRATO
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
							       p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.participantes p
							  JOIN public.titulares t 
							    ON t.cd_empresa            = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia       = p.seq_dependencia
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito   IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 8 
							   AND p.tipo_folha IN (8,17,18,19)
							"; 
					break;						
					
				case "SEM8": //SINPRORS SEM SENHA (OS: 30356)
					$sqlp = "
							SELECT p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.participantes p
							  JOIN public.participantes_ccin pc
								ON pc.cd_empresa            = p.cd_empresa
							   AND pc.cd_registro_empregado = p.cd_registro_empregado
							   AND pc.seq_dependencia       = p.seq_dependencia
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito   IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 8 
							   AND COALESCE(pc.opcao_contrato_valida) = '0'
							 ORDER BY p.cd_empresa, 
								  p.cd_registro_empregado, 
								  p.seq_dependencia	
					        ";
					break;					

				case "CS3S": //SINTAE
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
							       p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito   IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 10 
					        ";
					break;
					
				case "SIRI": //SINTAE - Participantes com Risco (Invalidez e/ou Morte) (OS: 37691)
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
							       p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito   IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 10 
							   AND (projetos.participante_valor_contrib_risco(p.cd_empresa::integer, p.cd_registro_empregado::integer, p.seq_dependencia::integer, 'I') > 0 
                                    OR
                                    projetos.participante_valor_contrib_risco(p.cd_empresa::integer, p.cd_registro_empregado::integer, p.seq_dependencia::integer, 'M') > 0)							   
					        ";
					break;					
					
				case "EX10": //SINTAE EXTRATO
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
							       p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.participantes p
							  JOIN public.titulares t 
							    ON t.cd_empresa            = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia       = p.seq_dependencia
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito   IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 10 
							   AND p.tipo_folha IN (8,17,18,19)
							"; 
					break;					
					
				case "FPPA": //FAMILIA
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
							       p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito   IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 19 
					        ";
					break;		

				case "EX19": //FAMILIA EXTRATO
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
							       p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.participantes p
							  JOIN public.titulares t 
							    ON t.cd_empresa            = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia       = p.seq_dependencia
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito   IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 19 
							   AND p.tipo_folha IN (8,17,18,19)
							"; 
					break;					

				case "DA11": #Dia do Aposentado 2011 - Sorteados
					$sqlp = "
							SELECT p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.participantes p
							 WHERE (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN ((0,97276,0),(1,231681,0),(0,175706,0),(0,195618,0),(0,244490,0),(2,145254,0),(0,218448,0),(0,37885,0),(0,102423,0),(0,28673,0),(0,26743,0),(0,31917,0),(0,173291,0),(0,179132,0),(0,220264,0),(9,787,0))
							 ORDER BY p.cd_empresa, 
								  p.cd_registro_empregado, 
								  p.seq_dependencia							   
							"; 
					break;						
				
				
				case "CS1J": //Fundação CEEE - Extratos e Cenário Legal
					$sqlp = "
							SELECT p.cd_plano, 
							       p.cd_empresa, 
								   p.cd_registro_empregado, 
							       p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM participantes p
							 WHERE (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND p.dt_obito   IS NULL 
							   AND p.cd_plano   > 0 
							   AND p.cd_empresa = 9 
							 ORDER BY p.cd_empresa, 
								  p.cd_registro_empregado, 
								  p.seq_dependencia								   
							"; 
					break;	
					
				case "QZ01": #Quiz 1ª Edição (ESARH) - OS 
					$sqlp = "
							SELECT qc.nome,
							       qc.email,
							       NULL AS email_profissional,
								   NULL AS cd_plano,
								   'DEFAULT' AS cd_empresa,
								   'DEFAULT' AS cd_registro_empregado,
								   'DEFAULT' AS seq_dependencia,
								   NULL AS re_cripto
							  FROM acs.quiz_cadastro qc
							 WHERE qc.cd_quiz = 1
							   AND qc.email LIKE '%@%'
							 ORDER BY qc.nome								   
							"; 
					break;		
					
				case "5SSI": #5º Seminário Gaúcho de Seguridade 2012 - Inscritos
					$sqlp = "
							SELECT NULL AS cd_plano,
							       UPPER(funcoes.remove_acento(nome)) AS nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao i
							 WHERE i.dt_exclusao IS NULL
							   AND i.email       LIKE '%@%'
							   AND i.cd_eventos_institucionais = 87
							 ORDER BY i.nome							   
							"; 
					break;		
					
					
				case "E89I": #Mais Vida 2012 - Inscritos
					$sqlp = "
							SELECT NULL AS cd_plano,
							       UPPER(funcoes.remove_acento(nome)) AS nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao i
							 WHERE i.dt_exclusao IS NULL
							   AND i.email       LIKE '%@%'
							   AND i.cd_eventos_institucionais = 89
							 ORDER BY i.nome							   
							"; 
					break;	

				case "E89I": #Mais Vida 2012 - Presentes
					$sqlp = "
							SELECT NULL AS cd_plano,
							       UPPER(funcoes.remove_acento(nome)) AS nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao i
							 WHERE i.dt_exclusao IS NULL
							   AND i.email       LIKE '%@%'
							   AND i.cd_eventos_institucionais = 89
							   AND i.fl_presente = 'S'
							 ORDER BY i.nome							   
							"; 
					break;					

				case "5SSP": #5º Seminário Gaúcho de Seguridade 2012 - Presentes
					$sqlp = "
							SELECT NULL AS cd_plano,
							       UPPER(funcoes.remove_acento(nome)) AS nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao i
							 WHERE i.dt_exclusao IS NULL
							   AND i.email       LIKE '%@%'
							   AND i.fl_presente = 'S'
							   AND i.cd_eventos_institucionais = 87
							 ORDER BY i.nome							   
							"; 
					break;		

				case "E88I": #Planejamento Financeiro e Orçamento Familiar - Setembro - 2012 - Inscritos (OS 35425)
					$sqlp = "
							SELECT NULL AS cd_plano,
							       UPPER(funcoes.remove_acento(nome)) AS nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao i
							 WHERE i.dt_exclusao IS NULL
							   AND i.email       LIKE '%@%'
							   AND i.cd_eventos_institucionais = 88
							 ORDER BY i.nome							   
							"; 
					break;		

				case "E88P": #Planejamento Financeiro e Orçamento Familiar - Setembro - 2012 - Presentes (OS 35425)
					$sqlp = "
							SELECT NULL AS cd_plano,
							       UPPER(funcoes.remove_acento(nome)) AS nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao i
							 WHERE i.dt_exclusao IS NULL
							   AND i.email       LIKE '%@%'
							   AND i.fl_presente = 'S'
							   AND i.cd_eventos_institucionais = 88
							 ORDER BY i.nome							   
							"; 
					break;	
				
				case "I100": #Planejamento Financeiro e Orçamento Familiar - Abril - 2013 - Inscritos (OS 37029)
					$sqlp = "
							SELECT NULL AS cd_plano,
							       UPPER(funcoes.remove_acento(nome)) AS nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao i
							 WHERE i.dt_exclusao IS NULL
							   AND I.dt_cadastro <= TO_TIMESTAMP('05/04/2013 16:00', 'DD/MM/YYYY HH24:MI')
							   AND i.email       LIKE '%@%'
							   AND i.cd_eventos_institucionais = 100
							 ORDER BY i.nome							   
							"; 
					break;	
					
				case "I102": #5º Diálogo Institucional Fundação CEEE 2013 - Inscritos
					$sqlp = "
							SELECT NULL AS cd_plano,
							       UPPER(funcoes.remove_acento(nome)) AS nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao i
							 WHERE i.dt_exclusao IS NULL
							   AND i.email       LIKE '%@%'
							   AND i.cd_eventos_institucionais = 102
							 ORDER BY i.nome							   
							"; 
					break;	

				case "P102": #5º Diálogo Institucional Fundação CEEE 2013 - Presentes
					$sqlp = "
							SELECT NULL AS cd_plano,
							       UPPER(funcoes.remove_acento(nome)) AS nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao i
							 WHERE i.dt_exclusao IS NULL
							   AND i.fl_presente = 'S'
							   AND i.email       LIKE '%@%'
							   AND i.cd_eventos_institucionais = 102
							 ORDER BY i.nome							   
							"; 
					break;	
					
				case "P104": #Fundos de Investimentos - Tchê Previdência
					$sqlp = "
							SELECT NULL AS cd_plano,
							       UPPER(funcoes.remove_acento(nome)) AS nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao i
							 WHERE i.dt_exclusao IS NULL
							   AND i.fl_presente = 'S'
							   AND i.email       LIKE '%@%'
							   AND i.cd_eventos_institucionais = 104
							 ORDER BY i.nome							   
							"; 
					break;	
					
				case "P107": #Introdução ao Mercado de Capitais - 24 junho 2013 - Presentes - OS:37924
					$sqlp = "
							SELECT NULL AS cd_plano,
							       UPPER(funcoes.remove_acento(nome)) AS nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao i
							 WHERE i.dt_exclusao IS NULL
							   AND i.fl_presente = 'S'
							   AND i.email       LIKE '%@%'
							   AND i.cd_eventos_institucionais = 107
							 ORDER BY i.nome							   
							"; 
					break;	
				
				
				case "FE11": #Festa 2011 - 2012 O fim é só o começo - Presentes
					$sqlp = "
							SELECT NULL AS cd_plano,
							       UPPER(funcoes.remove_acento(nome)) AS nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao i
							 WHERE i.dt_exclusao IS NULL
							   AND i.email       LIKE '%@%'
							   AND i.fl_presente = 'S'
							   AND i.cd_eventos_institucionais = 78
							 ORDER BY i.nome							   
							"; 
					break;						
					
					
				case "FA12": #Fórum de Segregação de Ativos 2012
					$sqlp = "
							SELECT NULL AS cd_plano,
							       UPPER(funcoes.remove_acento(nome)) AS nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao i
							 WHERE i.dt_exclusao IS NULL
							   AND i.email       LIKE '%@%'
							   AND i.fl_presente = 'S'
							   AND i.cd_eventos_institucionais = 81
							 ORDER BY i.nome							   
							"; 
					break;					
					
				case "PF01": #Planejamento Financeiro e Orçamento Familiar 2011 - Presentes
					$sqlp = "
							SELECT NULL AS cd_plano,
							       nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao
							 WHERE dt_exclusao IS NULL
							   AND email       LIKE '%@%'
							   AND fl_presente = 'S'
							   AND cd_eventos_institucionais = 59
							 ORDER BY nome							   
							"; 
					break;		
					
				case "E062": #Introdução ao Mercado de Capitais - Noturno
					$sqlp = "
							SELECT NULL AS cd_plano,
							       UPPER(funcoes.remove_acento(nome)) AS nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao
							 WHERE dt_exclusao IS NULL
							   AND email       LIKE '%@%'
							   AND cd_eventos_institucionais = 62
							 ORDER BY nome							   
							"; 
					break;	
				
				case "E094": #Introdução ao case "E094": #Introdução ao Mercado de Capitais - 11/12(Inscritos) - Noturno
					$sqlp = "
							SELECT NULL AS cd_plano,
							       UPPER(funcoes.remove_acento(nome)) AS nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao
							 WHERE dt_exclusao IS NULL
							   AND email       LIKE '%@%'
							   AND cd_eventos_institucionais = 94
							 ORDER BY nome							   
							"; 
					break;	

				case "SF01": #Curso - Saúde Financeira uma decisão em sua mente 2011 - OS 32482
					$sqlp = "
							SELECT NULL AS cd_plano,
							       nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao
							 WHERE dt_exclusao IS NULL
							   AND email       LIKE '%@%'
							   AND fl_presente = 'S'
							   AND cd_eventos_institucionais = 63
							 ORDER BY nome							   
							"; 
					break;						

				case "IMC1": #Inscritos Curso Introdução ao Mercado de Capitais (2010) - OS 27253
					$sqlp = "
							SELECT nome,
								   email,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   NULL AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao
							 WHERE dt_exclusao IS NULL
							   AND email       LIKE '%@%'
							   AND cd_eventos_institucionais = 31
							 ORDER BY nome							   
							"; 
					break;	

				case "IMC2": #Inscritos Curso Introdução ao Mercado de Capitais (2010) - CERTIFICADO
					$sqlp = "
							SELECT nome,
								   email,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(CAST(cd_eventos_institucionais_inscricao AS TEXT)) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao
							 WHERE dt_exclusao IS NULL
							   AND email       LIKE '%@%'
							   AND cd_eventos_institucionais = 31
							   AND fl_presente = 'S'
							 ORDER BY nome
							"; 
					break;	

				case "IMC3": #Inscritos Curso Introdução ao Mercado de Capitais (2010) - 2ª Turma
					$sqlp = "
							SELECT nome,
								   email,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   NULL AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao
							 WHERE dt_exclusao IS NULL
							   AND email LIKE '%@%'
							   AND cd_eventos_institucionais = 36
							 ORDER BY nome
							";
					break;

				case "IMC4": #Inscritos Curso Introdução ao Mercado de Capitais (2010) - 2 turma - pesquisa
					$sqlp = "
							SELECT nome,
								   email,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(CAST(cd_eventos_institucionais_inscricao AS TEXT)) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao
							 WHERE dt_exclusao IS NULL
							   AND email       LIKE '%@%'
							   AND cd_eventos_institucionais = 36
							   AND fl_presente = 'S'
							 ORDER BY nome
							"; 
					break;	
					
				case "IMC5": #Curso Introdução ao Mercado de Capitais  - 18 de junho 2012 - INSCRITOS - OS: 34581
					$sqlp = "
							SELECT nome,
								   email,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(CAST(cd_eventos_institucionais_inscricao AS TEXT)) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao
							 WHERE dt_exclusao IS NULL
							   AND email       LIKE '%@%'
							   AND cd_eventos_institucionais = 86
							 ORDER BY nome
							"; 
					break;						
					

				case "MV01": #"Encontro Mais Vida - Tholl Imagem e Sonho (2010)" - OS 27711
					$sqlp = "
							SELECT nome,
								   email,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   NULL AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao
							 WHERE dt_exclusao IS NULL
							   AND email       LIKE '%@%'
							   AND cd_eventos_institucionais = 34
							 ORDER BY nome							   
							"; 
					break;

				case "MV02": #"Encontro Mais Vida - Tholl Imagem e Sonho (2010)" - Presentes - OS 28414
					$sqlp = "
							SELECT nome,
								   email,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   NULL AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao
							 WHERE dt_exclusao IS NULL
							   AND email LIKE '%@%'
							   AND cd_eventos_institucionais = 34
							   AND fl_presente='S'
							 ORDER BY nome
							"; 
					break;
					
				case "E94I": #Curso de Introdução ao mercado de capitais - Inscritos - OS 36202
					$sqlp = "
							SELECT NULL AS cd_plano,
							       UPPER(funcoes.remove_acento(nome)) AS nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao i
							 WHERE i.dt_exclusao               IS NULL
							   AND i.email                     LIKE '%@%'
							   AND i.cd_eventos_institucionais = 94
							 ORDER BY i.nome							   
							"; 
					break;	

				case "E94P": #Curso de Introdução ao mercado de capitais - Presentes - OS 36202
					$sqlp = "
							SELECT NULL AS cd_plano,
							       UPPER(funcoes.remove_acento(nome)) AS nome,
								   email,
								   NULL AS email_profissional,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   MD5(cd_eventos_institucionais_inscricao::TEXT) AS re_cripto
							  FROM projetos.eventos_institucionais_inscricao i
							 WHERE i.dt_exclusao               IS NULL
							   AND i.email                     LIKE '%@%'
							   AND i.cd_eventos_institucionais = 94
							   AND fl_presente                 = 'S'
							 ORDER BY i.nome							   
							"; 
					break;					
					

				case "FPUS": #"Família Previdência - Usuários da Área Corporativa do site 
					$sqlp = "
							SELECT nome,
								   email,
								   'DEFAULT' AS cd_empresa,
								   'DEFAULT' AS cd_registro_empregado,
								   'DEFAULT' AS seq_dependencia,
								   NULL AS re_cripto
							  FROM familia_previdencia.usuario
							 WHERE dt_exclusao IS NULL
							   AND email       LIKE '%@%'
							 ORDER BY nome							   
							"; 
					break;

				case "FPDE": #"Família Previdência - Delegacias
					$sqlp = "
							SELECT nome,
								   REPLACE(REPLACE(email,' ',''),'/',';') AS email,
								   'DEFAULT' AS cd_empresa,
								   'DEFAULT' AS cd_registro_empregado,
								   'DEFAULT' AS seq_dependencia,
								   NULL AS re_cripto
							  FROM familia_previdencia.delegacia
							 WHERE dt_exclusao IS NULL
							   AND email       LIKE '%@%'
							 ORDER BY nome							   
							"; 
					break;	

				case "FP01": #Família Previdência - Interessados no Família - menos Região Metropolitana - 5/11/2010
					$sqlp = "
							SELECT 'DEFAULT' AS cd_plano,
								   nome,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   email,
								   NULL AS email_profissional,
								   MD5(CAST(cd_cadastro AS TEXT)) AS re_cripto
							  FROM familia_previdencia.cadastro 
							 WHERE dt_exclusao is null
							   AND email LIKE '%@%'
							   AND cidade NOT IN ('ALVORADA','CACHOEIRINHA','CANOAS','ESTEIO','GRAVATAI','GUAIBA','MONTENEGRO',
												  'NOVO HAMBURGO','OSORIO','PORTO ALEGRE','SAO LEOPOLDO','SAPIRANGA','VIAMAO')						   
							"; 
					break;	
					
				case "FP02": #Família Previdência - Covidados Reunião em Família - 17/11/2010
					$sqlp = "
							SELECT 'DEFAULT' AS cd_plano,
								   nome,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   email,
								   NULL AS email_profissional,
								   MD5(CAST(cd_cadastro AS TEXT)) AS re_cripto
							  FROM familia_previdencia.cadastro 
							 WHERE dt_exclusao is null
							   AND email LIKE '%@%'
							   AND cidade IN ('ALVORADA','CACHOEIRINHA','CANOAS','ESTEIO','GRAVATAI','GUAIBA','MONTENEGRO',
											  'NOVO HAMBURGO','OSORIO','PORTO ALEGRE','SAO LEOPOLDO','SAPIRANGA','VIAMAO')						   
							"; 
					break;	

				case "FP03": #Família Previdência - Aguardando Contato pela Fundação - 22/12/2010
					$sqlp = "
								SELECT c.nome, 
									   c.email,
									   COALESCE(p.email,p.email_profissional) AS email_profissional,
								       COALESCE(CAST(c.cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								       COALESCE(CAST(c.cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								       COALESCE(CAST(c.seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
									   'DEFAULT' AS cd_plano,
									   MD5(CAST(c.cd_cadastro AS TEXT)) AS re_cripto
								  FROM familia_previdencia.cadastro c
								  JOIN familia_previdencia.usuario u
									ON u.cd_usuario = c.cd_usuario_alteracao
								  JOIN familia_previdencia.cadastro_situacao cs
									ON cs.cd_cadastro_situacao = c.cd_cadastro_situacao
								  LEFT JOIN public.participantes p
									ON p.cd_empresa            = c.cd_empresa
								   AND p.cd_registro_empregado = c.cd_registro_empregado
								   AND p.seq_dependencia       = c.seq_dependencia
								 WHERE c.dt_exclusao IS NULL
								   AND c.cd_cadastro_situacao IN (2,3)
								   AND (COALESCE(c.email,'') LIKE '%@%' OR COALESCE(p.email,p.email_profissional) LIKE '%@%')					   
							"; 
					break;

                case "FP04": #Família Previdência - pela pesquisa - 04/08/2011 - OS 31794
					$sqlp = "
							SELECT 'DEFAULT' AS cd_plano,
								   nome,
								   COALESCE(CAST(cd_empresa AS TEXT),'DEFAULT') AS cd_empresa,
								   COALESCE(CAST(cd_registro_empregado AS TEXT),'DEFAULT') AS cd_registro_empregado,
								   COALESCE(CAST(seq_dependencia AS TEXT),'DEFAULT') AS seq_dependencia,
								   email,
								   NULL AS email_profissional,
								   MD5(CAST(cd_cadastro AS TEXT)) AS re_cripto
							  FROM familia_previdencia.cadastro
							 WHERE dt_exclusao IS NULL
							   AND email LIKE '%@%'
							   AND cd_cadastro_situacao = 2
							";
					break;
						
				case "JOG1": #Jogo 1ª Edição (A partir do 51º jogador até o último)
					$sqlp = "
							SELECT p.cd_plano,
				                   p.nome,
				                   p.cd_empresa, 
					               p.cd_registro_empregado, 
					               p.seq_dependencia,
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  JOIN public.participantes p
								ON funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) = jpr.cd_chave
							 WHERE jp.cd_jogo   = 1
							   AND jpi.fl_certo = 'S'
							 GROUP BY p.cd_plano,
				                      p.nome,
						              p.cd_empresa, 
					                  p.cd_registro_empregado, 
					                  p.seq_dependencia,				 
								      p.email, 
								      p.email_profissional ,
								      re_cripto,
						              jpr.dt_inclusao
				             ORDER BY jpr.dt_inclusao ASC,
						              p.nome ASC
                            OFFSET 50
							"; 
					break;	

				case "JOG2": #Jogo 1ª Edição (Os jogadores que estão entre os 50 primeiros que acertaram até 14 questões)
					$sqlp = "
							SELECT *
							FROM (SELECT p.cd_plano,
				                   p.nome,
				                   p.cd_empresa, 
					               p.cd_registro_empregado, 
					               p.seq_dependencia,
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   COUNT(*) AS qt_acerto
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  JOIN public.participantes p
								ON funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) = jpr.cd_chave
							 WHERE jp.cd_jogo   = 1
							   AND jpi.fl_certo = 'S'
							 GROUP BY p.cd_plano,
				                      p.nome,
						              p.cd_empresa, 
					                  p.cd_registro_empregado, 
					                  p.seq_dependencia,				 
								      p.email, 
								      p.email_profissional ,
								      re_cripto,
						              jpr.dt_inclusao
				             ORDER BY jpr.dt_inclusao ASC,
						              p.nome ASC
                            LIMIT 50) AS t
							WHERE t.qt_acerto <= 14
							"; 
					break;	
					
				case "JOG3": #Jogo 1ª Edição (Os jogadores que estão entre os 50 primeiros que acertaram de 15 a 20 questões.)
					$sqlp = "
							SELECT *
							FROM (SELECT p.cd_plano,
				                   p.nome,
				                   p.cd_empresa, 
					               p.cd_registro_empregado, 
					               p.seq_dependencia,
								   p.email, 
								   p.email_profissional ,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   COUNT(*) AS qt_acerto
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  JOIN public.participantes p
								ON funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) = jpr.cd_chave
							 WHERE jp.cd_jogo   = 1
							   AND jpi.fl_certo = 'S'
							 GROUP BY p.cd_plano,
				                      p.nome,
						              p.cd_empresa, 
					                  p.cd_registro_empregado, 
					                  p.seq_dependencia,				 
								      p.email, 
								      p.email_profissional ,
								      re_cripto,
						              jpr.dt_inclusao
				             ORDER BY jpr.dt_inclusao ASC,
						              p.nome ASC
                            LIMIT 50) AS t
							WHERE t.qt_acerto BETWEEN 15 AND 20
							"; 
					break;					
					
				case "JO21": #Jogo 2ª Edição (Os jogadores que acertaram 20 questões.)
					$sqlp = "
								SELECT *
								  FROM (SELECT p.cd_plano,
										   p.nome,
										   p.cd_empresa, 
										   p.cd_registro_empregado, 
										   p.seq_dependencia,
										   p.email, 
										   p.email_profissional ,
										   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
										   COUNT(*) AS qt_acerto
									  FROM projetos.jogo_pergunta jp
									  JOIN projetos.jogo_pergunta_item jpi
										ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
									  JOIN projetos.jogo_pergunta_resposta jpr
										ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
									   AND jpr.cd_jogo                   = jp.cd_jogo
									  JOIN public.participantes p
										ON funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) = jpr.cd_chave
									 WHERE jp.cd_jogo   = 2
									   AND jpi.fl_certo = 'S'
									 GROUP BY p.cd_plano,
											  p.nome,
										      p.cd_empresa, 
										      p.cd_registro_empregado, 
										      p.seq_dependencia,				 
											  p.email, 
											  p.email_profissional ,
											  re_cripto,
										      jpr.dt_inclusao
									 ORDER BY jpr.dt_inclusao ASC,
											  p.nome ASC) AS t
								  WHERE t.qt_acerto = 20
							"; 
					break;	



				case "JO22": #Jogo 2ª Edição (Os jogadores que acertaram de 0 a 19 questões.)
					$sqlp = "
								SELECT *
								  FROM (SELECT p.cd_plano,
										   p.nome,
										   p.cd_empresa, 
										   p.cd_registro_empregado, 
										   p.seq_dependencia,
										   p.email, 
										   p.email_profissional ,
										   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
										   COUNT(*) AS qt_acerto
									  FROM projetos.jogo_pergunta jp
									  JOIN projetos.jogo_pergunta_item jpi
										ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
									  JOIN projetos.jogo_pergunta_resposta jpr
										ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
									   AND jpr.cd_jogo                   = jp.cd_jogo
									  JOIN public.participantes p
										ON funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) = jpr.cd_chave
									 WHERE jp.cd_jogo   = 2
									   AND jpi.fl_certo = 'S'
									 GROUP BY p.cd_plano,
											  p.nome,
										      p.cd_empresa, 
										      p.cd_registro_empregado, 
										      p.seq_dependencia,				 
											  p.email, 
											  p.email_profissional ,
											  re_cripto,
										      jpr.dt_inclusao
									 ORDER BY jpr.dt_inclusao ASC,
											  p.nome ASC) AS t
								  WHERE t.qt_acerto < 20
							"; 
					break;		
					
				case "JO16": #Jogo 3ª Edição Jogos Interativos (Ativo + Não participantes - Quem já jogou)
					$sqlp = "
							SELECT *
							  FROM (
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM public.titulares t 
								  JOIN public.participantes p
									ON t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								 WHERE p.seq_dependencia = 0
								   AND p.cd_plano        > 0
								   AND p.dt_obito        IS NULL
								   AND (p.tipo_folha IN (0,1,8,11,12,13,16,17,18,19) OR (p.tipo_folha = 6 AND t.tipo_aposentado IN (4,13)))
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND p.cd_empresa <> 9
								 UNION
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM public.participantes p,
									   public.titulares t
								 WHERE t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano              = 0
								   AND p.tipo_folha            = 0
								   AND p.dt_obito              IS NULL
								   AND p.dt_nascimento         >= DATE_TRUNC('year', CURRENT_DATE) - '612 months'::interval
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')) t
							WHERE t.re_cripto NOT IN (SELECT DISTINCT cd_chave
														FROM projetos.jogo_pergunta_resposta
													   WHERE cd_jogo = 16)				
						";
					break;
					
				case "J16J": #Jogo 3ª Edição Jogos Interativos (Jogadores - Vencedores)
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  LEFT JOIN public.participantes p
								ON funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) = jpr.cd_chave
							  LEFT JOIN projetos.usuarios_controledi uc 
								ON funcoes.cripto_re(COALESCE(uc.cd_patrocinadora,99), COALESCE(uc.cd_registro_empregado, codigo), 0) = jpr.cd_chave
							 WHERE jp.cd_jogo = 16
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) NOT IN
									((0,234524,0),
									(0,364517,0),
									(3,322041,0),
									(0,191108,0),
									(19,591,0),
									(0,183814,0),
									(0,250121,0),
									(0,255459,0),
									(0,365840,0),
									(0,350222,0))
						";
					break;	
					
				case "J16V": #Jogo 3ª Edição Jogos Interativos (Vencedores)
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  LEFT JOIN public.participantes p
								ON funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) = jpr.cd_chave
							  LEFT JOIN projetos.usuarios_controledi uc 
								ON funcoes.cripto_re(COALESCE(uc.cd_patrocinadora,99), COALESCE(uc.cd_registro_empregado, codigo), 0) = jpr.cd_chave
							 WHERE jp.cd_jogo = 16
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN
									((0,234524,0),
									(0,364517,0),
									(3,322041,0),
									(0,191108,0),
									(19,591,0),
									(0,183814,0),
									(0,250121,0),
									(0,255459,0),
									(0,365840,0),
									(0,350222,0))
						";
					break;	

				case "J17V": #Jogo 4ª Edição Jogos Interativos (Vencedores) - OS: 31641
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  LEFT JOIN public.participantes p
								ON funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) = jpr.cd_chave
							  LEFT JOIN projetos.usuarios_controledi uc 
								ON funcoes.cripto_re(COALESCE(uc.cd_patrocinadora,99), COALESCE(uc.cd_registro_empregado, codigo), 0) = jpr.cd_chave
							 WHERE jp.cd_jogo = 17
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN
									((0,369136,0),
									(0,326844,0),
									(9,2429,0),
									(0,244368,0),
									(0,340553,0),
									(0,359904,0),
									(0,347353,0),
									(0,354368,0),
									(9,566,0),
									(0,319422,0))
							 ORDER BY p.cd_empresa, 
								      p.cd_registro_empregado, 
								      p.seq_dependencia										
						";
					break;	

				case "J17J": #Jogo 4ª Edição Jogos Interativos (Jogadores - Vencedores) - OS: 31641
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  LEFT JOIN public.participantes p
								ON funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) = jpr.cd_chave
							  LEFT JOIN projetos.usuarios_controledi uc 
								ON funcoes.cripto_re(COALESCE(uc.cd_patrocinadora,99), COALESCE(uc.cd_registro_empregado, codigo), 0) = jpr.cd_chave
							 WHERE jp.cd_jogo = 17
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) NOT IN
									((0,369136,0),
									(0,326844,0),
									(9,2429,0),
									(0,244368,0),
									(0,340553,0),
									(0,359904,0),
									(0,347353,0),
									(0,354368,0),
									(9,566,0),
									(0,319422,0))
							 ORDER BY p.cd_empresa, 
								      p.cd_registro_empregado, 
								      p.seq_dependencia
						";
					break;	

				case "J18J": #Jogo 5ª Edição Jogos Interativos (Vencedores) - OS: 32656
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  LEFT JOIN public.participantes p
								ON funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) = jpr.cd_chave
							  LEFT JOIN projetos.usuarios_controledi uc 
								ON funcoes.cripto_re(COALESCE(uc.cd_patrocinadora,99), COALESCE(uc.cd_registro_empregado, codigo), 0) = jpr.cd_chave
							 WHERE jp.cd_jogo = 18
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN
									((9,5339,0),
									(9,7226,0),
									(9,1449,0),
									(19,337,0),
									(9,850,0),
									(8,1511,0),
									(0,365173,0),
									(9,4961,0),
									(19,124,0),
									(9,7366,0),
									(0,363740,0),
									(9,566,0),
									(0,350621,0),
									(0,354716,0),
									(9,7480,0),
									(0,333590,0),
									(9,5631,0),
									(0,357308,0),
									(0,373702,0),
									(0,336297,0),
									(0,318931,0),
									(9,7218,0),
									(9,2861,0),
									(0,358941,0),
									(0,316903,0),
									(9,7013,0),
									(7,477,0),
									(0,368211,0),
									(0,355585,0),
									(2,322261,0),
									(9,7111,0),
									(0,142191,0),
									(0,373265,0))
							 ORDER BY p.cd_empresa, 
								      p.cd_registro_empregado, 
								      p.seq_dependencia
						";
					break;					

				case "JO05": //Jogo 5ª Edição Jogos Interativos (Ativos menos jogadores até agora) - OS: 32632
					$sqlp = "
							SELECT p.cd_plano,
							       p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional,
							       funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM public.titulares t 
							  JOIN public.participantes p
								ON t.cd_empresa            = p.cd_empresa
							   AND t.cd_registro_empregado = p.cd_registro_empregado
							   AND t.seq_dependencia       = p.seq_dependencia
							 WHERE p.seq_dependencia = 0
							   AND p.cd_plano        > 0
							   AND p.dt_obito        IS NULL
							   AND (p.tipo_folha IN (0,1,8,11,12,13,16,17,18,19) OR (p.tipo_folha = 6 AND t.tipo_aposentado IN (4,13)))
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							   AND funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) NOT IN (SELECT DISTINCT cd_chave 
                                                                                                                         FROM projetos.jogo_pergunta_resposta
																														WHERE cd_jogo = 18)
					";
					break;		
					
					
					
					
					
				case "J20A": #Jogo - Abril 2012 (VENCEDORES - PORTO ALEGRE - AFCEEE) - OS: 34276
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  JOIN public.participantes p
								ON funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = jpr.cd_chave
							 WHERE jp.cd_jogo = 20
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN
								   (SELECT tj.cd_empresa, tj.cd_registro_empregado, tj.seq_dependencia
									  FROM temporario.jogo_20 tj
									 WHERE UPPER(TRIM(tj.situacao)) = 'VENCEU'
									   AND UPPER(TRIM(tj.cidade))   = 'PORTO ALEGRE'
                                       AND UPPER(TRIM(tj.sigla))    = 'AFCEEE')
							 ORDER BY p.cd_empresa, 
									  p.cd_registro_empregado, 
									  p.seq_dependencia
						";
					break;					
				case "J20D": #Jogo - Abril 2012 (VENCEDORES - PORTO ALEGRE - CEEE-D) - OS: 34276
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  JOIN public.participantes p
								ON funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = jpr.cd_chave
							 WHERE jp.cd_jogo = 20
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN
								   (SELECT tj.cd_empresa, tj.cd_registro_empregado, tj.seq_dependencia
									  FROM temporario.jogo_20 tj
									 WHERE UPPER(TRIM(tj.situacao)) = 'VENCEU'
									   AND UPPER(TRIM(tj.cidade))   = 'PORTO ALEGRE'
                                       AND UPPER(TRIM(tj.sigla))    = 'CEEE-D')
							 ORDER BY p.cd_empresa, 
									  p.cd_registro_empregado, 
									  p.seq_dependencia
						";
					break;						
				case "J20G": #Jogo - Abril 2012 (VENCEDORES - PORTO ALEGRE - CEEE-GT) - OS: 34276
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  JOIN public.participantes p
								ON funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = jpr.cd_chave
							 WHERE jp.cd_jogo = 20
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN
								   (SELECT tj.cd_empresa, tj.cd_registro_empregado, tj.seq_dependencia
									  FROM temporario.jogo_20 tj
									 WHERE UPPER(TRIM(tj.situacao)) = 'VENCEU'
									   AND UPPER(TRIM(tj.cidade))   = 'PORTO ALEGRE'
                                       AND UPPER(TRIM(tj.sigla))    = 'CEEE-GT')
							 ORDER BY p.cd_empresa, 
									  p.cd_registro_empregado, 
									  p.seq_dependencia
						";
					break;	
				case "J20T": #Jogo - Abril 2012 (VENCEDORES - PORTO ALEGRE - CGTEE) - OS: 34276
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  JOIN public.participantes p
								ON funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = jpr.cd_chave
							 WHERE jp.cd_jogo = 20
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN
								   (SELECT tj.cd_empresa, tj.cd_registro_empregado, tj.seq_dependencia
									  FROM temporario.jogo_20 tj
									 WHERE UPPER(TRIM(tj.situacao)) = 'VENCEU'
									   AND UPPER(TRIM(tj.cidade))   = 'PORTO ALEGRE'
                                       AND UPPER(TRIM(tj.sigla))    = 'CGTEE')
							 ORDER BY p.cd_empresa, 
									  p.cd_registro_empregado, 
									  p.seq_dependencia
						";
					break;					
				case "J20R": #Jogo - Abril 2012 (VENCEDORES - PORTO ALEGRE - CRM) - OS: 34276
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  JOIN public.participantes p
								ON funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = jpr.cd_chave
							 WHERE jp.cd_jogo = 20
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN
								   (SELECT tj.cd_empresa, tj.cd_registro_empregado, tj.seq_dependencia
									  FROM temporario.jogo_20 tj
									 WHERE UPPER(TRIM(tj.situacao)) = 'VENCEU'
									   AND UPPER(TRIM(tj.cidade))   = 'PORTO ALEGRE'
                                       AND UPPER(TRIM(tj.sigla))    = 'CRM')
							 ORDER BY p.cd_empresa, 
									  p.cd_registro_empregado, 
									  p.seq_dependencia
						";
					break;		
				case "J20E": #Jogo - Abril 2012 (VENCEDORES - PORTO ALEGRE - ELETROCEEE) - OS: 34276
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  JOIN public.participantes p
								ON funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = jpr.cd_chave
							 WHERE jp.cd_jogo = 20
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN
								   (SELECT tj.cd_empresa, tj.cd_registro_empregado, tj.seq_dependencia
									  FROM temporario.jogo_20 tj
									 WHERE UPPER(TRIM(tj.situacao)) = 'VENCEU'
									   AND UPPER(TRIM(tj.cidade))   = 'PORTO ALEGRE'
                                       AND UPPER(TRIM(tj.sigla))    = 'ELETROCEEE')
							 ORDER BY p.cd_empresa, 
									  p.cd_registro_empregado, 
									  p.seq_dependencia
						";
					break;	
				case "J20S": #Jogo - Abril 2012 (VENCEDORES - PORTO ALEGRE - SENGE) - OS: 34276
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  JOIN public.participantes p
								ON funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = jpr.cd_chave
							 WHERE jp.cd_jogo = 20
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN
								   (SELECT tj.cd_empresa, tj.cd_registro_empregado, tj.seq_dependencia
									  FROM temporario.jogo_20 tj
									 WHERE UPPER(TRIM(tj.situacao)) = 'VENCEU'
									   AND UPPER(TRIM(tj.cidade))   = 'PORTO ALEGRE'
                                       AND UPPER(TRIM(tj.sigla))    = 'SENGE')
							 ORDER BY p.cd_empresa, 
									  p.cd_registro_empregado, 
									  p.seq_dependencia
						";
					break;	
				case "J20I": #Jogo - Abril 2012 (VENCEDORES - PORTO ALEGRE - SINPRO RS) - OS: 34276
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  JOIN public.participantes p
								ON funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = jpr.cd_chave
							 WHERE jp.cd_jogo = 20
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN
								   (SELECT tj.cd_empresa, tj.cd_registro_empregado, tj.seq_dependencia
									  FROM temporario.jogo_20 tj
									 WHERE UPPER(TRIM(tj.situacao)) = 'VENCEU'
									   AND UPPER(TRIM(tj.cidade))   = 'PORTO ALEGRE'
                                       AND UPPER(TRIM(tj.sigla))    = 'SINPRO RS')
							 ORDER BY p.cd_empresa, 
									  p.cd_registro_empregado, 
									  p.seq_dependencia
						";
					break;	
				case "J20N": #Jogo - Abril 2012 (VENCEDORES - PORTO ALEGRE - SINTAE RS) - OS: 34276
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  JOIN public.participantes p
								ON funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = jpr.cd_chave
							 WHERE jp.cd_jogo = 20
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN
								   (SELECT tj.cd_empresa, tj.cd_registro_empregado, tj.seq_dependencia
									  FROM temporario.jogo_20 tj
									 WHERE UPPER(TRIM(tj.situacao)) = 'VENCEU'
									   AND UPPER(TRIM(tj.cidade))   = 'PORTO ALEGRE'
                                       AND UPPER(TRIM(tj.sigla))    = 'SINTAE RS')
							 ORDER BY p.cd_empresa, 
									  p.cd_registro_empregado, 
									  p.seq_dependencia
						";
					break;					
					
				case "J20V": #Jogo - Abril 2012 (VENCEDORES) - OS: 34276
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  JOIN public.participantes p
								ON funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = jpr.cd_chave
							 WHERE jp.cd_jogo = 20
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN
								   (SELECT tj.cd_empresa, tj.cd_registro_empregado, tj.seq_dependencia
									  FROM temporario.jogo_20 tj
									 WHERE UPPER(tj.situacao) = 'VENCEU')
							 ORDER BY p.cd_empresa, 
									  p.cd_registro_empregado, 
									  p.seq_dependencia
						";
					break;	

				case "J20P": #Jogo - Abril 2012 (PERDEDORES) - OS: 34276
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  JOIN public.participantes p
								ON funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = jpr.cd_chave
							 WHERE jp.cd_jogo = 20
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN
								   (SELECT tj.cd_empresa, tj.cd_registro_empregado, tj.seq_dependencia
                                      FROM temporario.jogo_20 tj
                                     WHERE UPPER(tj.situacao) = 'PERDEU')
							 ORDER BY p.cd_empresa, 
								      p.cd_registro_empregado, 
								      p.seq_dependencia										
						";
					break;					

				case "J201": #Jogo - Abril 2012 (VENCEDORES - CEEE - AGENCIAS) - OS: 34610
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  JOIN public.participantes p
								ON funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = jpr.cd_chave
							 WHERE jp.cd_jogo = 20
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN
								   (SELECT tj.cd_empresa, tj.cd_registro_empregado, tj.seq_dependencia
                                      FROM temporario.jogo_20 tj
                                     WHERE UPPER(tj.situacao) = 'VENCEU')
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) 
							       IN ((0,360716,0),(0,358541,0),(0,326071,0),(0,228478,0),
								       (0,348619,0),(0,366188,0),(0,336831,0),(0,365807,0),
									   (0,145424,0),(0,372790,0),(0,365815,0),(0,340839,0),
									   (0,361020,0),(0,362921,0),(0,244571,0),(0,354031,0),
									   (0,231606,0),(0,253731,0),(0,342718,0),(0,350346,0),
									   (0,230499,0),(0,332151,0))									 
							 ORDER BY p.cd_empresa, 
								      p.cd_registro_empregado, 
								      p.seq_dependencia										
						";
					break;					
					
					
				case "J202": #Jogo - Abril 2012 (VENCEDORES - CEEE - JOAQUIM P VILLANOV) - OS: 34610
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  JOIN public.participantes p
								ON funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = jpr.cd_chave
							 WHERE jp.cd_jogo = 20
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN
								   (SELECT tj.cd_empresa, tj.cd_registro_empregado, tj.seq_dependencia
                                      FROM temporario.jogo_20 tj
                                     WHERE UPPER(tj.situacao) = 'VENCEU')
							   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) 
							       IN ((0,344664,0),(0,346497,0),(0,342262,0),(0,365262,0),(0,337722,0),(0,344923,0),
								      (0,373842,0),(0,342289,0),(0,341282,0),(0,340553,0),(0,332747,0),(0,329851,0),
								      (0,364398,0),(0,368318,0),(0,222747,0),(0,227536,0),(0,250937,0),(0,196215,0),
								      (0,356662,0),(0,327697,0),(0,342513,0),(0,340863,0),(0,333328,0),(0,360163,0),
								      (0,344711,0),(0,326585,0),(0,353281,0),(0,348023,0),(0,342483,0),(0,331261,0),
								      (0,267724,0),(0,359912,0),(0,204153,0),(0,171697,0),(0,326046,0),(0,347183,0),
								      (0,171735,0),(0,006025,0),(0,338028,0),(0,364291,0),(0,361194,0),(0,172154,0),
								      (0,373915,0),(0,336718,0),(0,357502,0),(0,331813,0),(0,361411,0),(0,352667,0),
								      (0,013722,0),(0,354198,0),(0,204463,0),(0,331767,0),(0,250571,0),(0,191094,0),
								      (0,339172,0),(0,203840,0),(0,248291,0),(0,352497,0),(0,223034,0),(0,192627,0),
								      (0,359505,0),(0,346870,0),(0,337498,0),(0,366161,0),(0,372102,0),(0,337072,0),
								      (0,204765,0),(0,372404,0),(0,364924,0),(0,321338,0),(0,268496,0),(0,363740,0),
								      (0,206059,0),(0,345393,0),(0,268623,0),(0,178012,0),(0,347302,0),(0,363286,0),
								      (0,196321,0),(0,268232,0),(0,172600,0),(0,357499,0),(0,331881,0),(0,365360,0),
								      (0,179949,0),(0,350621,0),(0,331074,0),(0,332925,0),(0,324728,0),(0,335444,0),
								      (0,346519,0),(0,303453,0),(0,357758,0),(0,372421,0),(0,333832,0),(0,330477,0),
								      (0,359122,0),(0,353574,0),(0,372692,0),(0,357898,0),(0,340341,0),(0,357031,0),
								      (0,340596,0),(0,363499,0),(0,333131,0),(0,374041,0),(0,336297,0),(0,336661,0),
								      (0,364762,0),(0,364339,0),(0,348988,0),(0,325791,0),(0,358606,0),(0,336548,0),
								      (0,357952,0),(0,254312,0),(0,373664,0),(0,345806,0),(0,205559,0),(0,354040,0),
								      (0,346861,0),(0,336955,0),(0,324752,0),(0,232297,0),(0,325341,0),(0,269166,0),
								      (0,347434,0),(0,373079,0),(0,206644,0),(0,211087,0),(0,324990,0),(0,233684,0),
								      (0,346543,0),(0,347051,0),(0,229792,0),(0,325279,0),(0,357308,0),(0,373559,0),
								      (0,255793,0),(0,233056,0),(0,233064,0),(0,231223,0),(0,373672,0),(0,266370,0),
								      (0,325252,0),(0,352012,0),(0,346551,0),(0,347345,0),(0,369080,0),(0,363715,0),
								      (0,250066,0),(0,268356,0),(0,218251,0),(0,373761,0),(0,373061,0),(0,184381,0),
								      (0,206083,0),(0,358975,0),(0,373346,0),(0,347060,0),(0,364177,0),(0,268348,0),
								      (0,319783,0),(0,151556,0),(0,267601,0),(0,347175,0),(0,373192,0),(0,347086,0),
								      (0,353426,0),(0,203483,0),(0,347353,0),(0,049239,0),(0,319252,0),(0,213403,0),
								      (0,202304,0),(0,357979,0),(0,184039,0),(0,348287,0),(0,319279,0),(0,365173,0),
								      (0,326437,0),(0,333344,0),(0,49654,0),(0,364517,0))
							 ORDER BY p.cd_empresa, 
								      p.cd_registro_empregado, 
								      p.seq_dependencia										
						";
					break;					
					
					
				case "J21T": #Jogo - Agosto 2012 (Todos)
					$sqlp = "
							SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
								   p.cd_plano,
								   p.cd_empresa,
								   p.cd_registro_empregado,
								   p.seq_dependencia,
								   p.nome,
								   p.email,
								   p.email_profissional
							  FROM projetos.jogo_pergunta jp
							  JOIN projetos.jogo_pergunta_item jpi
								ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
							  JOIN projetos.jogo_pergunta_resposta jpr
								ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
							   AND jpr.cd_jogo                   = jp.cd_jogo
							  JOIN public.participantes p
								ON funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = jpr.cd_chave
							 WHERE jp.cd_jogo = 21
							 ORDER BY p.cd_empresa, 
								      p.cd_registro_empregado, 
								      p.seq_dependencia	
						    ";
					break;					
					
					
				case "CS1F": //Ex-Autárquico
					$sqlp = "
								SELECT p.cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM public.participantes p
								 WHERE p.dt_obito        IS NULL 
								   AND projetos.participante_tipo(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = 'EXAU'
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								 	
							";
					break;		
					
					
				case "E93N": //Concurso do Dia do Aposentado "Tudo novo de novo" - Não inscritos
					$sqlp = "
								SELECT p.cd_plano, 
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM titulares AS t, 
									   participantes AS p
								 WHERE t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								   AND p.seq_dependencia       = 0
								   AND p.cd_plano              = 1
								   AND p.cd_empresa            IN (0, 1, 2, 3, 9)
								   AND p.tipo_folha            IN (0, 1, 8, 16, 17, 11, 12, 13, 18, 19)
								   AND p.dt_obito              IS NULL
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND funcoes.format_cpf(coalesce(p.cpf_mf,0)::bigint)::TEXT NOT IN (SELECT DISTINCT COALESCE(ev.cpf,'')::TEXT
																										FROM projetos.eventos_institucionais_inscricao ev
																									   WHERE ev.dt_exclusao IS NULL
																										 AND ev.cd_eventos_institucionais = 93)	
								 UNION
								SELECT p.cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto 
								  FROM public.participantes p
								 WHERE p.dt_obito        IS NULL 
								   AND p.cd_plano        > 0 
								   AND p.seq_dependencia = 0
								   AND p.tipo_folha      IN (2,3,4,5,14,15,20,30,40,45,50,60,65,75,80)
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
								   AND funcoes.format_cpf(coalesce(p.cpf_mf,0)::bigint)::TEXT NOT IN (SELECT DISTINCT COALESCE(ev.cpf,'')::TEXT
																										FROM projetos.eventos_institucionais_inscricao ev
																									   WHERE ev.dt_exclusao IS NULL
																										 AND ev.cd_eventos_institucionais = 93)	
								 UNION
								SELECT p.cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM public.participantes p
								 WHERE p.dt_obito        IS NULL 
								   AND projetos.participante_tipo(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = 'EXAU'
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND funcoes.format_cpf(coalesce(p.cpf_mf,0)::bigint)::TEXT NOT IN (SELECT DISTINCT COALESCE(ev.cpf,'')::TEXT
																										FROM projetos.eventos_institucionais_inscricao ev
																									   WHERE ev.dt_exclusao IS NULL
																										 AND ev.cd_eventos_institucionais = 93)
								 	
							";
					break;	


				case "IN01": //Institucional - Região Pelotas (Pelotas, São Lourenço do Sul, Santa Vitória do Palmar, Rio Grande, Canguçu, Piratini, Pedro Osório, Jaguarão, Dom Pedrito, Arroio Grande, São José do Norte) - OS: 32720
					$sqlp = "
								SELECT p.cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM public.participantes p
								 WHERE p.dt_obito        IS NULL 
								   AND projetos.participante_tipo(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN ('APOS','PENS','EXAU','CTP')
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND (UPPER(p.cidade) =  UPPER('PELOTAS')
										OR UPPER(p.cidade) =  UPPER('SAO LOURENCO DO SUL')
										OR UPPER(p.cidade) =  UPPER('SANTA VITORIA DO PALMAR')
                                        OR UPPER(p.cidade) =  UPPER('RIO GRANDE')
                                        OR UPPER(p.cidade) =  UPPER('CANGUCU')
                                        OR UPPER(p.cidade) =  UPPER('PIRATINI')
                                        OR UPPER(p.cidade) =  UPPER('PEDRO OSORIO')
                                        OR UPPER(p.cidade) =  UPPER('JAGUARAO')
                                        OR UPPER(p.cidade) =  UPPER('ARROIO GRANDE')
                                        OR UPPER(p.cidade) =  UPPER('DOM PEDRITO')
                                        OR UPPER(p.cidade) =  UPPER('SAO JOSE DO NORTE'))		
							";
					break;	
					
				case "SF02": //Curso - Saúde Financeira - Salto do Jacui: ativos, aposentados e pensionistas de todos planos (com exceção de SINPRO/RS,SINTAE e CRMPrev) de Salto do Jacuí, Sobradinho e Panambi. - OS: 34275
					$sqlp = "
								SELECT p.cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM public.participantes p
								 WHERE p.dt_obito        IS NULL 
								   AND p.cd_empresa NOT IN (6,8,10)
								   AND p.cd_plano > 0
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND (UPPER(p.cidade) =  UPPER('SALTO DO JACUI')
										OR UPPER(p.cidade) =  UPPER('SOBRADINHO')
                                        OR UPPER(p.cidade) =  UPPER('PANAMBI'))		
							";
					break;						
					

				case "IN02": //Institucional - Região Porto Alegre (Porto Alegre, Eldorado do Sul, Cachoeirinha, Gravataí, Guaíba, Viamão) - OS: 32728
					$sqlp = "
								SELECT p.cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM public.participantes p
								 WHERE p.dt_obito        IS NULL 
								   AND projetos.participante_tipo(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN ('APOS','PENS','EXAU','CTP')
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND (UPPER(p.cidade) =  UPPER('PORTO ALEGRE')
										OR UPPER(p.cidade) =  UPPER('ELDORADO DO SUL')
										OR UPPER(p.cidade) =  UPPER('CACHOEIRINHA')
										OR UPPER(p.cidade) =  UPPER('GRAVATAI')
										OR UPPER(p.cidade) =  UPPER('GUAIBA')
                                        OR UPPER(p.cidade) =  UPPER('VIAMAO'))		
							";
					break;		

				case "IN03": //Familia Participantes - CRUZ ALTA (Sem CRM, SINPRO, SENGE e SINTAE). - OS: 34167
					$sqlp = "
								SELECT p.cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM public.participantes p
								 WHERE p.dt_obito        IS NULL 
								   AND projetos.participante_tipo(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN ('APOS','PENS','EXAU','CTP')
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
								   AND p.cd_empresa NOT IN (6,7,8,10)
								   AND UPPER(p.cidade) =  UPPER('CRUZ ALTA')	
							";
					break;		

				case "SIER": //SINPRORS envio de email errado (09/05/2012) - OS: 34267
					$sqlp = "
							SELECT p.cd_plano, 
								   p.cd_empresa, 
								   p.cd_registro_empregado, 
								   p.seq_dependencia, 
								   p.nome, 
								   p.email, 
								   p.email_profissional,
								   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
							  FROM projetos.contribuicao_controle cc
							  JOIN public.participantes p
								ON p.cd_empresa = cc.cd_empresa
							   AND p.cd_registro_empregado = cc.cd_registro_empregado
							   AND p.seq_dependencia = cc.seq_dependencia
							 WHERE cc.cd_empresa = 8
							   AND cc.nr_ano_competencia = 2012
							   AND cc.nr_mes_competencia = 5
							   AND cc.dt_controle = '2012-05-09 12:12:41.895924'
							   AND cc.cd_usuario = 133
							   AND cc.fl_email_enviado = 'S'
							   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
							";
					break;						

				case "AP12": //Dia do Aposentado 2012 - 24/01/2012 [evento 79]: Não inscritos (Aposentados, CTP, Ex-autárquico) - OS: 33171
					$sqlp = "
								SELECT p.cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
								  FROM public.participantes p
								 WHERE p.dt_obito IS NULL 
								   AND projetos.participante_tipo(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN ('APOS','EXAU','CTP')
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 
                                   AND 0 =  (SELECT COUNT(*)
											   FROM projetos.eventos_institucionais_inscricao i
										      WHERE i.cd_empresa                = p.cd_empresa
 											    AND i.cd_registro_empregado     = p.cd_registro_empregado
											    AND i.seq_dependencia           = p.seq_dependencia
											    AND i.dt_exclusao               IS NULL
											    AND i.cd_eventos_institucionais = 79)								   
							";
					break;	

				case "SC00": //SINPRORS - Área Corporativa - Cadastro SINPRO - (OS: 36746)
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

  							      JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
									   AND ie.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
									   ) cx
										ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
									   AND COALESCE(i.cd_situacao,0) <> 3
									   AND COALESCE(p.cd_registro_empregado,0) = 0						
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome							   
							";
					break;
					
					
				case "SC01": //SINPRORS - Área Corporativa - Cadastro SINPRO - SAO LEOPOLDO/RS
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

  							      JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
									   AND ie.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
									   AND TRIM(UPPER(ie.cidade || ' - ' || ie.uf)) = 'SAO LEOPOLDO - RS') cx
										ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
									   AND COALESCE(i.cd_situacao,0) <> 3
									   AND COALESCE(p.cd_registro_empregado,0) = 0						
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome							   
							";
					break;

				case "SC02": //SINPRORS - Área Corporativa - Cadastro SINPRO - CARAZINHO/RS (OS:36411)
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

  							      JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
									   AND ie.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
									   AND TRIM(UPPER(ie.cidade || ' - ' || ie.uf)) = 'CARAZINHO - RS') cx
										ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
									   AND COALESCE(i.cd_situacao,0) <> 3
									   AND COALESCE(p.cd_registro_empregado,0) = 0						
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome							   
							";
					break;		
					
				case "SC03": //SINPRORS - Área Corporativa - Cadastro SINPRO - NOVO HAMBURGO/RS (OS: 36655)
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

  							      JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
									   AND ie.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
									   AND TRIM(UPPER(ie.cidade || ' - ' || ie.uf)) = 'NOVO HAMBURGO - RS') cx
										ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
									   AND COALESCE(i.cd_situacao,0) <> 3
									   AND COALESCE(p.cd_registro_empregado,0) = 0						
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome							   
							";
					break;	

				case "SC04": //SINPRORS - Área Corporativa - Cadastro SINPRO - CAMAQUA/RS (OS: 36691)
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

  							      JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
									   AND ie.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
									   AND TRIM(UPPER(ie.cidade || ' - ' || ie.uf)) = 'CAMAQUA - RS') cx
										ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
									   AND COALESCE(i.cd_situacao,0) <> 3
									   AND COALESCE(p.cd_registro_empregado,0) = 0						
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome							   
							";
					break;		

				case "SC05": //SINPRORS - Área Corporativa - Cadastro SINPRO - SANTA MARIA/RS (OS: 36762)
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

  							      JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
									   AND ie.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
									   AND TRIM(UPPER(ie.cidade || ' - ' || ie.uf)) = 'SANTA MARIA - RS') cx
										ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
									   AND COALESCE(i.cd_situacao,0) <> 3
									   AND COALESCE(p.cd_registro_empregado,0) = 0						
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome							   
							";
					break;		

				case "SC06": //SINPRORS - Área Corporativa - Cadastro SINPRO - CAPAO DA CANOA/RS (OS: 36803)
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

  							      JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
									   AND ie.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
									   AND TRIM(UPPER(ie.cidade || ' - ' || ie.uf)) = 'CAPAO DA CANOA - RS') cx
										ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
									   AND COALESCE(i.cd_situacao,0) <> 3
									   AND COALESCE(p.cd_registro_empregado,0) = 0						
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome							   
							";
					break;						
				
				case "SC07": //SINPRORS - Área Corporativa - Cadastro SINPRO - SANTIAGO/RS (OS: 36916)
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

  							      JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
									   AND ie.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
									   AND TRIM(UPPER(ie.cidade || ' - ' || ie.uf)) = 'SANTIAGO - RS') cx
										ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
									   AND COALESCE(i.cd_situacao,0) <> 3
									   AND COALESCE(p.cd_registro_empregado,0) = 0						
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome							   
							";
					break;	
					
				case "SC08": //SINPRORS - Área Corporativa - Cadastro SINPRO - ALEGRETE/RS (OS: 36930)
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

  							      JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
									   AND ie.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
									   AND TRIM(UPPER(ie.cidade || ' - ' || ie.uf)) = 'ALEGRETE - RS') cx
										ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
									   AND COALESCE(i.cd_situacao,0) <> 3
									   AND COALESCE(p.cd_registro_empregado,0) = 0						
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome							   
							";
					break;	
					
				case "SC09": //SINPRORS - Área Corporativa - Cadastro SINPRO - SANTA CRUZ DO SUL/RS (OS: 37012)
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

  							      JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
									   AND ie.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
									   AND TRIM(UPPER(ie.cidade || ' - ' || ie.uf)) = 'SANTA CRUZ DO SUL - RS') cx
										ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
									   AND COALESCE(i.cd_situacao,0) <> 3
									   AND COALESCE(p.cd_registro_empregado,0) = 0						
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome							   
							";
					break;		

				case "SC10": //SINPRORS - Área Corporativa - Cadastro SINPRO - PASSO FUNDO/RS (OS: 37110)
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

  							      JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
									   AND ie.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
									   AND TRIM(UPPER(ie.cidade || ' - ' || ie.uf)) = 'PASSO FUNDO - RS') cx
										ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
									   AND COALESCE(i.cd_situacao,0) <> 3
									   AND COALESCE(p.cd_registro_empregado,0) = 0						
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome							   
							";
					break;		

				case "SC11": //SINPRORS - Área Corporativa - Cadastro SINPRO - BENTO GONCALVES/RS (OS: 37501)
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

  							      JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
									   AND ie.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
									   AND TRIM(UPPER(ie.cidade || ' - ' || ie.uf)) = 'BENTO GONCALVES - RS') cx
										ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
									   AND COALESCE(i.cd_situacao,0) <> 3
									   AND COALESCE(p.cd_registro_empregado,0) = 0						
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome							   
							";
					break;	

				case "SC12": //SINPRORS - Área Corporativa - Cadastro SINPRO - PELOTAS/RS (OS: 37723)
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

  							      JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
									   AND ie.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
									   AND TRIM(UPPER(ie.cidade || ' - ' || ie.uf)) = 'PELOTAS - RS') cx
										ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
									   AND COALESCE(i.cd_situacao,0) <> 3
									   AND COALESCE(p.cd_registro_empregado,0) = 0						
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome							   
							";
					break;					
					
				case "SC13": //SINPRORS - Área Corporativa - Cadastro SINPRO - PORTO ALEGRE/RS - INST SANTA LUZIA PARA CEGOS (OS: 37823)
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

								  JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
										   AND ie.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao i
											ON i.cd_instituicao = ie.cd_instituicao 
										   AND i.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
										   AND TRIM(UPPER(i.ds_instituicao)) = 'INSTITUTO SANTA LUZIA ESCOLA DE I GRAU PARA CEGOS'
										   AND TRIM(UPPER(ie.cidade || ' - ' || ie.uf)) = 'PORTO ALEGRE - RS') cx
									ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
								   AND COALESCE(i.cd_situacao,0) <> 3
								   AND COALESCE(p.cd_registro_empregado,0) = 0                      
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome    							   
							";
					break;						
				
				case "SC14": //SINPRORS - Área Corporativa - Cadastro SINPRO - CANOAS/RS - ULBRA (OS: 37855)
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

								  JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
										   AND ie.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao i
											ON i.cd_instituicao = ie.cd_instituicao 
										   AND i.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
										   AND ie.cd_instituicao IN (308,330,338,682) --ULBRA
										   AND TRIM(UPPER(ie.cidade || ' - ' || ie.uf)) = 'CANOAS - RS') cx
									ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
								   AND COALESCE(i.cd_situacao,0) <> 3
								   AND COALESCE(p.cd_registro_empregado,0) = 0                      
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome   							   
							";
					break;				
					
				case "SC15": //SINPRORS - Área Corporativa - Cadastro SINPRO - SAO LEOPOLDO/RS - SOC EDUC PADRE NORBERTO DIDONI (OS: 37923)
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

								  JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
										   AND ie.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao i
											ON i.cd_instituicao = ie.cd_instituicao 
										   AND i.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
										   AND ie.cd_instituicao = 1097 --SOCIEDADE EDUCACIONAL PADRE NORBERTO DIDONI
										   AND TRIM(UPPER(ie.cidade || ' - ' || ie.uf)) = 'SAO LEOPOLDO - RS') cx
									ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
								   AND COALESCE(i.cd_situacao,0) <> 3
								   AND COALESCE(p.cd_registro_empregado,0) = 0                      
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome   							   
							";
					break;

				case "SC16": //SINPRORS - Área Corporativa - Cadastro SINPRO - ALVORADA/RS - EXITO SIST DE ENSINO LTDA (OS: 37966)
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

								  JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
										   AND ie.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao i
											ON i.cd_instituicao = ie.cd_instituicao 
										   AND i.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
										   AND ie.cd_instituicao = 628 --EXITO SISTEMA DE ENSINO LTDA
										   AND TRIM(UPPER(ie.cidade || ' - ' || ie.uf)) = 'ALVORADA - RS') cx
									ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
								   AND COALESCE(i.cd_situacao,0) <> 3
								   AND COALESCE(p.cd_registro_empregado,0) = 0                      
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome   							   
							";
					break;	

				case "SC17": //SINPRORS - Área Corporativa - Cadastro SINPRO - ALVORADA/RS - COMUN EVAN LUT SAO MARCOS (OS: 37966)
					$sqlp = "
								SELECT NULL AS cd_plano, 
									   NULL AS cd_empresa, 
									   NULL AS cd_registro_empregado, 
									   NULL AS seq_dependencia, 
									   c.nome, 
									   c.email, 
									   NULL AS email_profissional,
									   NULL AS re_cripto
								  FROM sinprors_previdencia.cadastro_sinpro c
												  
								  LEFT JOIN sinprors_previdencia.interessado i
									ON i.cd_cadastro_sinpro = c.cd_cadastro_sinpro
													
								  LEFT JOIN sinprors_previdencia.interessado i1
									ON i1.cpf = c.cpf
													
								  LEFT JOIN public.participantes p
									ON p.cd_empresa = 8
								   AND p.cd_plano > 0
								   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf

								  JOIN (SELECT DISTINCT cs.cd_cadastro_sinpro
										  FROM sinprors_previdencia.cadastro_sinpro cs
										  JOIN sinprors_previdencia.cadastro_sinpro_instituicao csi
											ON csi.cd_cadastro_sinpro = cs.cd_cadastro_sinpro
										   AND csi.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao_endereco ie
											ON ie.cd_instituicao_endereco = csi.cd_instituicao_endereco    
										   AND ie.dt_exclusao IS NULL
										  JOIN sinprors_previdencia.instituicao i
											ON i.cd_instituicao = ie.cd_instituicao 
										   AND i.dt_exclusao IS NULL
										 WHERE cs.dt_exclusao IS NULL
										   AND ie.cd_instituicao = 329 --COMUNIDADE EVANGELICA LUTERANA SAO MARCOS
										   AND TRIM(UPPER(ie.cidade || ' - ' || ie.uf)) = 'ALVORADA - RS') cx
									ON cx.cd_cadastro_sinpro = c.cd_cadastro_sinpro
								   AND COALESCE(i.cd_situacao,0) <> 3
								   AND COALESCE(p.cd_registro_empregado,0) = 0                      
								 WHERE c.dt_exclusao IS NULL
								   AND c.fl_socio      = 'S'
								   AND c.fl_aposentado = 'N'
								 ORDER BY c.nome   							   
							";
					break;						
					
				case "FS01": #Fundação Solidária Eleições 2011 - Não votaram (Aposentado, Ativos, Pensionistas) - Pesquisa 282 - OS: 32329
					$sqlp = "
								SELECT pa.cd_plano,
									   pa.cd_empresa,
									   pa.cd_registro_empregado,
									   pa.seq_dependencia,
									   pa.nome,
									   pa.email,
									   pa.email_profissional,
									   pa.re_cripto
								  FROM
								(  
								-- ATIVOS
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
									   (p.cd_empresa || '.' ||  p.cd_registro_empregado || '.' || p.seq_dependencia) AS cd_chave
								  FROM public.titulares t 
								  JOIN public.participantes p
									ON t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								 WHERE p.seq_dependencia = 0
								   AND p.cd_plano        > 0
								   AND p.dt_obito        IS NULL
								   AND (p.tipo_folha IN (0,1,8,11,12,13,16,17,18,19) OR (p.tipo_folha = 6 AND t.tipo_aposentado IN (4,13)))
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 

								UNION

								--APOSENTADOS
								SELECT p.cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
									   (p.cd_empresa || '.' ||  p.cd_registro_empregado || '.' || p.seq_dependencia) AS cd_chave
								  FROM public.participantes p
								 WHERE p.dt_obito        IS NULL 
								   AND p.cd_plano        > 0 
								   AND p.seq_dependencia = 0
								   AND p.tipo_folha      IN (2,3,4,5,14,15,20,30,40,45,50,60,65,75,80)
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')   

								UNION 

								-- PENSIONISTAS
								SELECT p.cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
									   (p.cd_empresa || '.' ||  p.cd_registro_empregado || '.' || p.seq_dependencia) AS cd_chave
								  FROM public.participantes p
								  JOIN public.dependentes d
									ON d.cd_empresa            = p.cd_empresa
								   AND d.cd_registro_empregado = p.cd_registro_empregado
								   AND d.seq_dependencia       = p.seq_dependencia          
								 WHERE p.dt_obito        IS NULL 
								   AND p.cd_plano        > 0 
								   AND p.seq_dependencia > 0
								   AND p.tipo_folha      IN (2,45,80)
								   AND d.dt_desligamento IS NULL
								   AND d.id_pensionista  = 'S' 
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
								) pa
								WHERE pa.cd_chave NOT IN (SELECT DISTINCT ip
															FROM projetos.enquete_resultados
														   WHERE cd_enquete = 282)
						";
					break;						
					
				case "MV03": #Mais Vida 2011 - Sem inscritos - OS: 32539
					$sqlp = "
								SELECT pa.cd_plano,
									   pa.cd_empresa,
									   pa.cd_registro_empregado,
									   pa.seq_dependencia,
									   pa.nome,
									   pa.email,
									   pa.email_profissional,
									   pa.re_cripto
								  FROM
								(  
								-- ATIVOS
								SELECT p.cd_plano,
									   p.cd_empresa,
									   p.cd_registro_empregado,
									   p.seq_dependencia,
									   p.nome,
									   p.email,
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
									   (p.cd_empresa || '.' ||  p.cd_registro_empregado || '.' || p.seq_dependencia) AS cd_chave
								  FROM public.titulares t 
								  JOIN public.participantes p
									ON t.cd_empresa            = p.cd_empresa
								   AND t.cd_registro_empregado = p.cd_registro_empregado
								   AND t.seq_dependencia       = p.seq_dependencia
								 WHERE p.seq_dependencia = 0
								   AND p.cd_plano        > 0
								   AND p.dt_obito        IS NULL
								   AND (p.tipo_folha IN (0,1,8,11,12,13,16,17,18,19) OR (p.tipo_folha = 6 AND t.tipo_aposentado IN (4,13)))
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') 

								UNION

								--APOSENTADOS
								SELECT p.cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
									   (p.cd_empresa || '.' ||  p.cd_registro_empregado || '.' || p.seq_dependencia) AS cd_chave
								  FROM public.participantes p
								 WHERE p.dt_obito        IS NULL 
								   AND p.cd_plano        > 0 
								   AND p.seq_dependencia = 0
								   AND p.tipo_folha      IN (2,3,4,5,14,15,20,30,40,45,50,60,65,75,80)
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')   

								UNION 

								-- PENSIONISTAS
								SELECT p.cd_plano, 
									   p.cd_empresa, 
									   p.cd_registro_empregado, 
									   p.seq_dependencia, 
									   p.nome, 
									   p.email, 
									   p.email_profissional,
									   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
									   (p.cd_empresa || '.' ||  p.cd_registro_empregado || '.' || p.seq_dependencia) AS cd_chave
								  FROM public.participantes p
								  JOIN public.dependentes d
									ON d.cd_empresa            = p.cd_empresa
								   AND d.cd_registro_empregado = p.cd_registro_empregado
								   AND d.seq_dependencia       = p.seq_dependencia          
								 WHERE p.dt_obito        IS NULL 
								   AND p.cd_plano        > 0 
								   AND p.seq_dependencia > 0
								   AND p.tipo_folha      IN (2,45,80)
								   AND d.dt_desligamento IS NULL
								   AND d.id_pensionista  = 'S' 
								   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
								) pa
								WHERE 0 =  (SELECT COUNT(*)
											  FROM projetos.eventos_institucionais_inscricao i
										     WHERE i.cd_empresa                = pa.cd_empresa
 											   AND i.cd_registro_empregado     = pa.cd_registro_empregado
											   AND i.seq_dependencia           = pa.seq_dependencia
											   AND i.dt_exclusao               IS NULL
											   AND i.cd_eventos_institucionais = 61)
						";
					break;						
					

				case "NEFP": #EMAIL PARA PUBLICO ALVO DO FAMILIA PREVIDENCIA COM EXCESSÃO DOS QUE JÁ RESPONDERAM PESQUISA 237

					/* TEMPORARIO.DIVULGACAO_TEMP AGREGA GRUPOS CS1L, CS1K, CS1N, CS1J, CS1X, CS1M importados pelo pgadmin */
					$sqlp = "
						SELECT DISTINCT d.* 
						FROM temporario.divulgacao_temp d
						WHERE

							grupo='FAMILIA_PREVIDENCIA'

						AND
						
						NOT EXISTS ( 
							SELECT 1
							FROM projetos.enquete_resultados er
							where funcoes.cripto_re( d.cd_empresa, d.cd_registro_empregado, d.seq_dependencia ) = er.ip
							and cd_enquete=237
						)
					";
					break;

					
                case "AB01": #Amauri Bueno - Promoção do seguro automóvel (29/11/2011) - OS: 32876
					$sqlp = "
							SELECT c.nome, 
							       LOWER(c.email) AS email,
							       NULL AS email_profissional,
							       'DEFAULT' AS cd_empresa,
							       'DEFAULT' AS cd_registro_empregado,
							       'DEFAULT' AS seq_dependencia,
							       'DEFAULT' AS cd_plano,
							        MD5(CAST(c.cd_seguro_auto_amauri AS TEXT)) AS re_cripto
							  FROM acs.seguro_auto_amauri c
							 WHERE c.email LIKE '%@%'
							   AND c.cd_seguro_auto_amauri = (SELECT MIN(c1.cd_seguro_auto_amauri)
							                                    FROM acs.seguro_auto_amauri c1
							                                   WHERE c1.email LIKE '%@%'
							                                     AND c1.cpf = c.cpf)
							";
					break;					
					
			}    
		}

		if($sqlp != '')
		{
		
		$rs=pg_query($db, $sqlp);
		
		while ($reg=pg_fetch_array($rs)) 
		{
			$v_texto = $conteudo;
			$v_nome = str_replace(' Das ', ' das ',(str_replace(' Da ', ' da ',(str_replace(' Dos ', ' dos ', str_replace(' De ', ' de ',(($reg['nome']))))))));	
			$v_texto = str_replace("{nome}", (($v_nome)), $v_texto);
			if ($cd_publico == "CS1W") { $v_texto = str_replace("{cd_inscricao}", $reg['codigo'], $v_texto); }
			if ($cd_publico == "CS2W") { $v_texto = str_replace("{cd_inscricao}", $reg['codigo'], $v_texto); }
			
			$v_texto = str_replace("'", "´", $v_texto);

			$v_texto = str_replace("{link_arquivo}", $v_arquivo, $v_texto);
			$v_texto = str_replace("[EMP]",  $reg['cd_empresa'], $v_texto);
			$v_texto = str_replace("[RE]",   $reg['cd_registro_empregado'], $v_texto);
			$v_texto = str_replace("[SEQ]",  $reg['seq_dependencia'], $v_texto);
			$v_texto = str_replace("[NOME]", $reg['nome'], $v_texto);
			$v_texto = str_replace("[RE_CRIPTO]", $reg['re_cripto'], $v_texto);
			
			
			#### LINK ####
			$link_email = "";
			if(trim($_POST['url_link']) != "")
			{
				$link = str_replace("[RE_CRIPTO]", $reg['re_cripto'], trim($_POST['url_link']));
				
				if ((trim($reg['cd_empresa']) == "") or ($reg['cd_empresa'] == 'DEFAULT'))
				{
					$link_emp = "NULL";
				}
				else
				{
					$link_emp = intval($reg['cd_empresa']) ;
				}
				
				if ((trim($reg['cd_registro_empregado']) == "") or ($reg['cd_registro_empregado'] == 'DEFAULT'))
				{
					$link_re = "NULL";
				}
				else
				{
					$link_re = intval($reg['cd_registro_empregado']) ;
				}			
				
				if ((trim($reg['seq_dependencia']) == "") or ($reg['seq_dependencia'] == 'DEFAULT'))
				{
					$link_seq = "NULL";
				}
				else
				{
					$link_seq = intval($reg['seq_dependencia']) ;
				}			
				
				$qr_link = "
								SELECT funcoes.gera_link(
										'".$link."',
										".$link_emp.",
										".$link_re.",
										".$link_seq."
									   ) AS link
						   ";
				$ob_link = pg_query($db, $qr_link);
				$ar_link = pg_fetch_array($ob_link);
				$link_email = $ar_link['link'];
			}
			$v_texto = str_replace("[LINK_1]", $link_email, $v_texto);
			

// -----------------------------------------------------
			if ($cd_divulgacao == '') {
				$divlg = 0; }
			else {
				$divlg = $cd_divulgacao;
			}
// -----------------------------------------------------
			if ($reg['cd_empresa'] == '') {
				$emp = 0; }
			else {
				$emp = $reg['cd_empresa'];
			}
// -----------------------------------------------------
			if ($reg['cd_registro_empregado'] == '') {
				$re = 0; }
			else {
				$re = $reg['cd_registro_empregado'];
			}
// -----------------------------------------------------
			if ($reg['seq_dependencia'] == '') {
				$seq = 0; }
			else {
				$seq = $reg['seq_dependencia'];
			}

//----------AJUSTE NO EMAIL:

			$reg['email'] = trim(strtolower(str_replace("'","",$reg['email'])));
			$reg['email_profissional'] = trim(strtolower(str_replace("'","",$reg['email_profissional'])));
			
			if(!ereg(".*@.*", $reg['email'])) 
			{
				$reg['email'] = $reg['email_profissional'];
				$reg['email_profissional'] = "";
			}
			
			$v_cc = "";
			if(ereg(".*@.*", $reg['email_profissional'])) 
			{
				if(trim($reg['email']) != trim($reg['email_profissional']))
				{
					$v_cc = $reg['email_profissional'];
				}
			}			
			
			$email = $reg['email'];

			
	

				$sql =        " insert into projetos.envia_emails ( ";
				$sql = $sql . "		dt_envio, ";
				$sql = $sql . "		dt_schedule_email, ";
				$sql = $sql . "		de, ";
				$sql = $sql . "		para, ";
				$sql = $sql . "		cc,	";
				$sql = $sql . "		cco, ";
				$sql = $sql . "		assunto, ";
				$sql = $sql . "		arquivo_anexo, ";
				$sql = $sql . "		texto, ";
				$sql = $sql . "		div_solicitante, ";
				$sql = $sql . "		cd_divulgacao, ";
				$sql = $sql . "		cd_empresa, ";
				$sql = $sql . "		cd_registro_empregado, ";
				$sql = $sql . "		seq_dependencia, ";
				$sql = $sql . "		cd_usuario ";				
				$sql = $sql . " ) ";
				$sql = $sql . " VALUES ( ";
				$sql = $sql . "		CURRENT_TIMESTAMP, ";
				$sql = $sql . "		".$dt_envio.", ";
				
				if(trim($_POST['remetente']) != "")
				{
					$sql = $sql . "		'".trim($_POST['remetente'])."', ";
				}
				elseif($emp == 7)
				{
					$sql = $sql . "		'Senge Previdência', ";
				}				
				elseif($emp == 8)
				{
					$sql = $sql . "		'SINPRORS Previdência', ";
				}
				elseif($emp == 10)
				{
					$sql = $sql . "		'SINPRORS Previdência', ";
				}
				elseif($emp == 19)
				{
					$sql = $sql . "		'Família Previdência', ";
				}				
				else
				{
					$sql = $sql . "		'Fundação CEEE', ";
				}
				
				$sql = $sql . "		'" . $email . "', ";
				$sql = $sql . "		'".$v_cc."', ";
				$sql = $sql . "    	'', ";
				$sql = $sql . "    	'$assunto', ";
				$sql = $sql . "    	'$arquivo', ";
				$sql = $sql . "    	'$v_texto', ";
				$sql = $sql . " 	'$div_solicitante', ";
				$sql = $sql . " 	$divlg, ";
				$sql = $sql . " 	$emp, ";
				$sql = $sql . " 	$re, ";
				$sql = $sql . " 	$seq, ";
				$sql = $sql . " 	".intval($_SESSION['Z'])." ";
				$sql = $sql . " )";	 
				//echo $sql; exit;
				$s = (pg_query($db, $sql));
			
		}	// fim do laço de leitura da tabela participantes
		}
	}
	return $ret;
}
//-----------------------------------------------------------------------------------------------
function fnc_grava_emails_avulsos($cd_divulgacao, $db, $publicos, $cd_publicacao, $dt_envio, $arquivo, $conteudo, $assunto, $div_solicitante, $edicao) 
{
	$v_arquivo = "http://www.e-prev.com.br/controle_projetos/upload/".$arquivo;
	$emails=explode(";",$publicos);
	$v_num_ocorr = (substr_count($publicos, ';') + 1);
	for($i=0;$i < $v_num_ocorr;$i++){
		$v_texto = $conteudo;
		$v_texto = str_replace("{link_arquivo}", $v_arquivo, $v_texto);
// -----------------------------------------------------
		if ($cd_divulgacao == '') {
			$divlg = 0; }
		else {
			$divlg = $cd_divulgacao;
		}
// -----------------------------------------------------
		if ($reg['cd_empresa'] == '') {
			$emp = 0; }
		else {
			$emp = $reg['cd_empresa'];
		}
// -----------------------------------------------------
		if ($reg['cd_registro_empregado'] == '') {
			$re = 0; }
		else {
			$re = $reg['cd_registro_empregado'];
		}
// -----------------------------------------------------
		if ($reg['seq_dependencia'] == '') {
			$seq = 0; }
		else {
			$seq = $reg['seq_dependencia'];
		}
//-------------------------------------------------------------------- Evita a duplicidade:
		$email = $emails[$i];
		$sql2 =        " select 	count(*) as num_regs from projetos.envia_emails  ";
		$sql2 = $sql2 . " where		assunto = '$assunto' ";
		$sql2 = $sql2 . " and		para = '" . $email . "' ";
		$sql2 = $sql2 . " and 		dt_email_enviado is null ";
//			echo $sql2;
		$rs2 	= 	(pg_query($db, $sql2));
		$reg2	=	pg_fetch_array($rs2);
		if ($reg2['num_regs'] == 0) {
			if ($email != '') {
				$sql =        " insert into projetos.envia_emails ( ";
				$sql = $sql . "		dt_envio, ";
				$sql = $sql . "		dt_schedule_email, ";
				$sql = $sql . "		de, ";
				$sql = $sql . "		para, ";
				$sql = $sql . "		cc,	";
				$sql = $sql . "		cco, ";
				$sql = $sql . "		assunto, ";
				$sql = $sql . "		arquivo_anexo, ";
				$sql = $sql . "		texto, ";
				$sql = $sql . "		div_solicitante, ";
				$sql = $sql . "		cd_divulgacao, ";
				$sql = $sql . "		cd_empresa, ";
				$sql = $sql . "		cd_registro_empregado, ";
				$sql = $sql . "		seq_dependencia, ";
				$sql = $sql . "		tipo_mensagem ";
				$sql = $sql . " ) ";
				$sql = $sql . " VALUES ( ";
				$sql = $sql . "		CURRENT_TIMESTAMP, ";
				$sql = $sql . "		".$dt_envio.", ";
				
				if(trim($_POST['remetente']) != "")
				{
					$sql = $sql . "		'".trim($_POST['remetente'])."', ";
				}
				elseif($emp == 7)
				{
					$sql = $sql . "		'Senge Previdência', ";
				}				
				elseif($emp == 8)
				{
					$sql = $sql . "		'SINPRORS Previdência', ";
				}
				elseif($emp == 10)
				{
					$sql = $sql . "		'SINPRORS Previdência', ";
				}
				elseif($emp == 19)
				{
					$sql = $sql . "		'Família Previdência', ";
				}					
				else
				{
					$sql = $sql . "		'Fundação CEEE', ";
				}
				$sql = $sql . "		'" . $email . "', ";
				$sql = $sql . "		'$v_cc', ";
				$sql = $sql . "    	'', ";
				$sql = $sql . "    	'$assunto', ";
				$sql = $sql . "    	'$arquivo', ";
				$sql = $sql . "    	'$v_texto', ";
				$sql = $sql . " 	'$div_solicitante', ";
				$sql = $sql . " 	$divlg, ";
				$sql = $sql . " 	$emp, ";
				$sql = $sql . " 	$re, ";
				$sql = $sql . " 	$seq, ";
				$sql = $sql . " 	'html' ";
				$sql = $sql . " )";	 
				$s = (pg_query($db, $sql));
			}
		}
	}
	return $ret;
}
//-----------------------------------------------------------------------------------------------
function fnc_grava_divulgacoes_publicacoes($cd_divulgacao, $db, $cd_publicacao) {
	if (isset($cd_divulgacao)) {
		if (is_numeric($cd_publicacao)) {
			$sql = 			" insert into projetos.divulgacoes_publicacoes (";
			$sql = $sql . 	" cd_publicacao, cd_divulgacao ";
	    	$sql = $sql . 	" ) ";
	    	$sql = $sql . 	" VALUES ( ";
			$sql = $sql . 	" $cd_publicacao, $cd_divulgacao ";
    		$sql = $sql . 	")";
			$s = (pg_query($db, $sql));
		}
	}
	return $ret;
}
//-----------------------------------------------------------------------------------------------
function fnc_grava_divulgacoes_publicos($cd_divulgacao, $db, $cd_publico) {
	if (isset($cd_divulgacao)) {
			$sql = 			" insert into projetos.divulgacoes_publicos (";
			$sql = $sql . 	" cd_publico, cd_divulgacao ";
	    	$sql = $sql . 	" ) ";
	    	$sql = $sql . 	" VALUES ( ";
			$sql = $sql . 	" '" . $cd_publico . "', $cd_divulgacao ";
    		$sql = $sql . 	")";
			$s = (pg_query($db, $sql));
	}
	return $ret;
}
//-----------------------------------------------------------------------------------------------
	
function convdata_br_iso($dt) {
      // Pressupõe que a data esteja no formato DD/MM/AAAA
      // A melhor forma de gravar datas no PostgreSQL é utilizando 
      // uma string no formato DDDD-MM-AA. Esta função justamente 
      // adequa a data a este formato
      $d = substr($dt, 0, 2);
      $m = substr($dt, 3, 2);
      $a = substr($dt, 6, 4);
	  $hora = '00:00:00';
      return $a.'-'.$m.'-'.$d.' '.$hora;
   }
//------------------------------------------------------------------------
function envia_email_cenario_legal($num_edicao, $db, $cd_divulgacao) 
{
	####   MIGRADO 07/01/2013 /cieprev/index.php/ecrm/informativo_cenario_legal/conteudo   ####
}
//------------------------------------------------------------------------------------

function insere_atividade($areas_indicadas, $db, $descricao, $cd_cenario) 
{
	####   MIGRADO 07/01/2013 /cieprev/index.php/ecrm/informativo_cenario_legal/conteudo   ####
}
//-----------------------------------------------------------------------------------------
function envia_emailx($num_atividade, $db, $tp) 
{
	####   MIGRADO 07/01/2013 /cieprev/index.php/ecrm/informativo_cenario_legal/conteudo   ####
}
//-----------------------------------------------------------------------------------------------
function fnc_grava_divulgacao($cd_divulgacao, $db, $cd_publicacao) {
	if (isset($num_divulgacao)) {
		$sql = 			" insert into projetos.divulgacoes_publicacoes (";
		$sql = $sql . 	" cd_divulgacao, cd_publicacao ";
	    $sql = $sql . 	" ) ";
    	$sql = $sql . 	" VALUES ( ";
		$sql = $sql . 	" $cd_divulgacao, $cd_publicacao ";
    	$sql = $sql . 	")";
//		echo $sql . '<br>';
		$s = (pg_query($db, $sql));
	}
	return $ret;
}
//-----------------------------------------------------------------------------------------------
function monta_boletim_informativo($num_edicao, $db) {
	$vbcrlf2 = chr(10).chr(13);
	$vbcrlf = chr(13);
	$v_linha = '-------------------------------------------------';
// --------------------------------------------------- Montagem da mensagem com os links para as notícias;
// TEXTO DA CAPA:
		
	$sql =        " select tit_capa, titulo_edicao ";			
	$sql = $sql . " from   acs.edicao_boletim ";
	$sql = $sql . " where  cd_edicao = $num_edicao ";

	$rs = pg_query($db, $sql);
	if ($reg = pg_fetch_array($rs)) {
		$v_edicao = str_replace('&nbsp;','',$reg['tit_capa']);
		$v_titulo = str_replace('&nbsp;','',$reg['titulo_edicao']);
	}
// TEXTO DAS MATÉRIAS:	
	$sql =        " select	cd_materia, titulo, l.descricao as secao, l.valor as valor";			
	$sql = $sql . " from   	acs.boletim, listas l ";
	$sql = $sql . " where  	cd_edicao = $num_edicao and cd_secao = l.codigo and l.categoria = 'SBOL' ";
	$sql = $sql . " order by valor, cd_materia ";
//	echo $sql; 
	$rs = pg_query($db, $sql);
	
	$v_texto = $vbcrlf2 . $v_linha . $vbcrlf2;
	$v_texto = $v_texto . 'Olá {nome}' . $vbcrlf2;
	$v_texto = $v_texto . 'Leia nesta edição:' . $vbcrlf2;

//	$v_texto = $v_texto . '  ' . $v_titulo . $vbcrlf . '  "http://www.e-prev.com.br/boletim/Conteudo/index.php?ed=' . $num_edicao . '"' . $vbcrlf2;	
    while ($reg = pg_fetch_array($rs))
	{
		if ($v_secao_ant != $reg['secao']) {
			$v_secao_ant = $reg['secao'];
			$v_texto = $v_texto . $v_linha . $vbcrlf . strtoupper($v_secao_ant) . $vbcrlf . $v_linha . $vbcrlf2;
			if ($v_titulo != '') 
			{  // Colocado aqui para que a chamada da capa saia dentro de Institucional
				$v_texto = $v_texto . '  ' . $v_titulo . $vbcrlf . '  "http://www.e-prev.com.br/boletim/Conteudo/index.php?ed=' . $num_edicao . '"' . $vbcrlf2;	
				$v_titulo = '';
			}
		}	
		$v_texto = $v_texto . '  ' . str_replace('<br>',': ',$reg['titulo'])  . $vbcrlf;
		$v_texto = $v_texto . '  "http://www.e-prev.com.br/boletim/Conteudo/conteudo.php?ed=' . $num_edicao . '&c=' . $reg['cd_materia'] . '"' . $vbcrlf2;			
	}
	$v_texto = $v_texto . $v_linha . $vbcrlf;	
	return $v_texto;
}
//------------------------------------------------------------------------------------
?>