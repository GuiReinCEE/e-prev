<?php
class Atendimento_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT a.cd_atendimento,
						   TO_CHAR(a.dt_hora_inicio_atendimento,'DD/MM/YYYY HH24:MI:SS') AS dt_atendimento,
                           TO_CHAR(a.dt_hora_fim_atendimento,'DD/MM/YYYY HH24:MI:SS') AS hr_fim,
                           TO_CHAR((a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento),'HH24:MI:SS') AS hr_tempo,
                           CASE WHEN (a.indic_ativo = 'T') THEN 'Telefônico'
                           WHEN (a.indic_ativo = 'P') THEN 'Pessoal'
                           WHEN (a.indic_ativo = 'C') THEN 'Consulta'
                           WHEN (a.indic_ativo = 'E') THEN 'E-mail'
                           ELSE 'Não Informado'
                           END AS tp_atendimento,
                           uc.guerra AS atendente,
                           a.cd_empresa,
                           a.cd_registro_empregado,
                           a.seq_dependencia,
                           COALESCE(p.nome,a.nome) AS nome,
						   p.cidade,
						   p.unidade_federativa AS uf,
                           a.obs,
                           a.dt_hora_inicio_atendimento,
                           a.tipo_atendimento_indicado,
						   (CASE WHEN TRIM(COALESCE(act.nome_arquivo,'')) <> '' 
								 THEN COALESCE(ac.nome_arquivo, TO_CHAR(act.data,'YYYY_MM_DD') || '/' || act.nome_arquivo) 
								 ELSE TRIM(COALESCE('xcally/' || acx.nome_arquivo,''))
						   END) AS nm_arquivo,	
						   (CASE WHEN TRIM(COALESCE(act.nome_arquivo,'')) <> '' 
								 THEN 'PADRAO'
								 ELSE 'XCALLY'
						   END) AS tp_arquivo,						   
                           COUNT(r.cd_atendimento) AS qt_reclamacao_novo,
                           COUNT(ar.cd_reclamacao) AS qt_reclamacao,
                           COUNT(ao.cd_observacao) AS qt_obs,
                           COUNT(aos.cd_observacao) AS qt_elogio,
                           COUNT(ae.cd_encaminhamento) AS qt_encaminhamento,
                           COUNT (aret.cd_retorno) AS qt_retorno,
                           (SELECT lt.descricao
							  FROM projetos.atendimento_tela_capturada atc
							  JOIN projetos.telas_programas tp
							    ON tp.cd_tela = atc.cd_tela
							  JOIN public.listas lt
							    ON lt.codigo = tp.cd_programa_fceee
							   AND lt.categoria = 'PRFC'  
							 WHERE atc.cd_atendimento = a.cd_atendimento
							 ORDER BY dt_acesso DESC
							 LIMIT 1) AS ds_programa
		              FROM projetos.atendimento a
			          JOIN projetos.usuarios_controledi uc
			            ON uc.codigo = a.id_atendente  
			 
			          LEFT JOIN public.participantes p
			            ON p.cd_empresa            = a.cd_empresa
			           AND p.cd_registro_empregado = a.cd_registro_empregado
			           AND p.seq_dependencia       = a.seq_dependencia
			
			          LEFT JOIN projetos.reclamacao r
			            ON r.cd_atendimento = a.cd_atendimento 			
			
			          LEFT JOIN projetos.atendimento_reclamacao ar
			            ON ar.cd_atendimento = a.cd_atendimento 				   
			
			          LEFT JOIN projetos.atendimento_observacao ao
			            ON ao.cd_atendimento     = a.cd_atendimento 	
			           AND ao.cd_tipo_observacao <> '0002'
			
			          LEFT JOIN projetos.atendimento_observacao aos
			            ON aos.cd_atendimento = a.cd_atendimento					
			           AND aos.cd_tipo_observacao = '0002'
			
			          LEFT JOIN projetos.atendimento_encaminhamento ae
			            ON ae.cd_atendimento = a.cd_atendimento 					
			
			          LEFT JOIN projetos.arquivos_call ac
			            ON ac.cd_atendimento = a.cd_atendimento 					
						
			          LEFT JOIN projetos.arquivos_call_teledata act
			            ON act.cd_atendimento = a.cd_atendimento
						
			          LEFT JOIN projetos.arquivos_call_xcally acx
			            ON acx.cd_atendimento = a.cd_atendimento						
			
			          LEFT JOIN projetos.atendimento_retorno aret
			            ON aret.cd_atendimento = a.cd_atendimento
		             
					 WHERE CAST(a.dt_hora_inicio_atendimento AS  DATE) BETWEEN TO_DATE('".trim($args["dt_inicio"])."','DD/MM/YYYY')  AND TO_DATE('".trim($args["dt_fim"])."','DD/MM/YYYY')
		               AND ((a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > CAST('00:00:10' AS INTERVAL) OR a.id_callcenter IS NOT NULL)
        
                       ".(trim($args["cd_atendimento"]) != "" ? "AND a.cd_atendimento = ".trim($args["cd_atendimento"]) : "")."
					   ".(trim($args["tipo_atendimento"]) == 'A'      ? "AND (a.indic_ativo IN ('P', 'E') OR (a.indic_ativo = 'T' AND (CASE WHEN TRIM(COALESCE(act.nome_arquivo,'')) <> '' 
								 THEN COALESCE(ac.nome_arquivo, TO_CHAR(act.data,'YYYY_MM_DD') || '/' || act.nome_arquivo) 
								 ELSE TRIM(COALESCE('xcally/' || acx.nome_arquivo,''))
						   END) != ''))" : "")."
					   ".(trim($args["tipo_atendimento"]) == 'C'      ? "AND a.indic_ativo = '".trim($args["tipo_atendimento"])."'" : "")."
					   ".(trim($args["tipo_atendimento"]) == 'P'      ? "AND a.indic_ativo = '".trim($args["tipo_atendimento"])."'" : "")."
					   ".(trim($args["tipo_atendimento"]) == 'T'      ? "AND a.indic_ativo = '".trim($args["tipo_atendimento"])."'" : "")."
					   ".(trim($args["tipo_atendimento"]) == 'E'      ? "AND a.indic_ativo = '".trim($args["tipo_atendimento"])."'" : "")."
					   ".(intval($args["id_atendente"]) > 0          ? "AND a.id_atendente = ".intval($args["id_atendente"]) : "")."
					   ".(intval($args["cd_empresa"]) > -1           ? "AND a.cd_empresa = ".intval($args["cd_empresa"]) : "")."
					   ".(intval($args["cd_registro_empregado"]) > 0 ? "AND a.cd_registro_empregado = ".intval($args["cd_registro_empregado"]) : "")."
		               ".(intval($args["seq_dependencia"]) > -1      ? "AND a.seq_dependencia = ".intval($args["seq_dependencia"]) : "")."
                       ".(trim($args['obs']) == 'T' ? "AND(TRIM(COALESCE(a.obs,'')) <> '' OR 0 < (SELECT COUNT(*) FROM projetos.atendimento_retorno ar1 WHERE ar1.cd_atendimento = a.cd_atendimento))" : "")."	
					   ".(trim($args['obs']) == 'O' ? "AND(TRIM(COALESCE(a.obs,'')) <> '' OR 0 < (SELECT COUNT(*) FROM projetos.atendimento_observacao ao1 WHERE ao1.cd_atendimento = a.cd_atendimento))" : "")."	
					   ".(trim($args['obs']) == 'E' ? "AND 0 < (SELECT COUNT(*) FROM projetos.atendimento_encaminhamento ae1 WHERE ae1.cd_atendimento = a.cd_atendimento)" : "")."	
					   ".(trim($args['obs']) == 'R' ? "AND	(a.tipo_atendimento_indicado = 'R' 
				                                            OR 0 < (SELECT COUNT(*) FROM projetos.atendimento_reclamacao ar1 WHERE ar1.cd_atendimento = a.cd_atendimento)
				                                            OR 0 < (SELECT COUNT(*) FROM projetos.reclamacao r1 WHERE r1.cd_atendimento = a.cd_atendimento AND r1.dt_exclusao IS NULL)
				                                            OR 0 < (SELECT COUNT(*) FROM projetos.atendimento_observacao ao1 WHERE ao1.cd_atendimento = a.cd_atendimento AND ao1.cd_tipo_observacao IN ('0002','0003')))" : "")."	
					  ".(trim($args['cd_programa_fceee']) != '' ? "AND 0 < (SELECT COUNT(*)
																			  FROM projetos.atendimento_tela_capturada atc
																			  JOIN projetos.telas_programas tp
																			    ON tp.cd_tela = atc.cd_tela
																			  JOIN public.listas lt
																			    ON lt.codigo = tp.cd_programa_fceee
																			   AND lt.categoria = 'PRFC'  
																			 WHERE atc.cd_atendimento = a.cd_atendimento
																			   AND tp.cd_programa_fceee = '".trim($args['cd_programa_fceee'])."')" : "")."
		             GROUP BY a.cd_atendimento,
                              dt_atendimento,
                              hr_fim,
                              hr_tempo,
                              tp_atendimento,
                              atendente,
                              a.cd_empresa,
                              a.cd_registro_empregado,
                              a.seq_dependencia,
                              COALESCE(p.nome,a.nome),
						      p.cidade,
						      uf,							  
                              a.obs,
                              a.dt_hora_inicio_atendimento,
                              a.tipo_atendimento_indicado,
                              nm_arquivo,
							  tp_arquivo
		          ";

		#print_r($args);
		#echo "<pre style='text-align:left;'>$qr_sql</pre>"; 
		#exit;

		$result = $this->db->query($qr_sql);
	}

	function listar_atendente_dd()
	{
		$qr_sql = "
					SELECT codigo as value, 
					       nome as text
					  FROM projetos.usuarios_controledi 
					 WHERE indic_08 IN ('N', 'C', 'P', 'T', 'E') 
					   AND divisao = 'GRSC' 
					   AND tipo    NOT IN ('X', 'T')
					 ORDER BY nome;				  
		          ";		  
		 
		$result = $this->db->query($qr_sql);

		return $result->result_array();
	}
	
	function listar_programa_dd(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value,
			       descricao AS text
		      FROM public.listas
		     WHERE categoria = 'PRFC'
		       AND dt_exclusao IS NULL
		     ORDER BY descricao;";		  
		 
		$result = $this->db->query($qr_sql);
	}

	function encaminhar($args,&$msg=array())
	{
		$sql = "
				UPDATE projetos.atendimento 
				   SET dt_encaminhamento = CURRENT_TIMESTAMP, 
		               resp_encaminhamento = ".intval($args['cd_usuario_logado'])."
		         WHERE cd_atendimento = ".intval($args['cd_atendimento'])."
		           AND 0 = (SELECT COUNT(*)
			                  FROM projetos.atendimento_encaminhamento
			                 WHERE cd_atendimento    = ".intval($args['cd_atendimento'])."
			                   AND cd_encaminhamento = ".intval($args['cd_encaminhamento'])."
			                   AND dt_retorno_encaminhamento IS NULL)
		";

		$q = $this->db->query($sql);

		$msg[]='OK';

		return true;
	}

    function listar_atendente( &$result, $args=array() )
	{
        $qr_sql = "
            SELECT a.id_atendente,
				   uc.nome,
				   COUNT(*) AS qt_atendimento
		      FROM projetos.atendimento a,
				   projetos.usuarios_controledi uc
		     WHERE a.id_atendente = uc.codigo
		     --AND uc.tipo NOT IN('X', 'P')
		     --AND a.dt_hora_inicio_atendimento > '2004-07-04'
			   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
			   AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
               AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
               ".($args['tipo_atendimento'] != '' ?  " AND a.indic_ativo = '".$args['tipo_atendimento']."'" : " AND a.indic_ativo <> 'C'" )."
             GROUP BY a.id_atendente, uc.nome
		     ORDER BY qt_atendimento DESC
        ";

        #echo '<pre>'.$qr_sql.'</pre>';

        $result = $this->db->query($qr_sql);
    }

    function listar_data( &$result, $args=array() )
	{
        $qr_sql = "
            SELECT TO_CHAR(a.dt_hora_inicio_atendimento,'DD/MM/YYYY') AS dt_data,
                   DATE_TRUNC('day', a.dt_hora_inicio_atendimento) AS dt_data_ordem,
                   COUNT(*) AS qt_atendimento
              FROM projetos.atendimento a
             WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
               AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
               AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
               ".($args['tipo_atendimento'] != '' ?  " AND a.indic_ativo = '".$args['tipo_atendimento']."'" : "" )."
             GROUP BY dt_data, dt_data_ordem
             ORDER BY dt_data_ordem DESC
        ";
        #echo '<pre>'.$qr_sql.'</pre>';

        $result = $this->db->query($qr_sql);
    }

    function listar_tipo( &$result, $args=array() )
    {
        $qr_sql = "
            SELECT CASE WHEN (a.indic_ativo = 'T') THEN 'Telefônico'
                        WHEN (a.indic_ativo = 'P') THEN 'Pessoal'
                        WHEN (a.indic_ativo = 'C') THEN 'Consulta'
                        WHEN (a.indic_ativo = 'E') THEN 'E-mail'
                        ELSE 'Não Informado'
                   END AS ds_tipo_atendimento,
                   CASE WHEN (a.indic_ativo = 'T') THEN 'T'
                        WHEN (a.indic_ativo = 'P') THEN 'P'
                        WHEN (a.indic_ativo = 'C') THEN 'C'
                        WHEN (a.indic_ativo = 'E') THEN 'E'
                        ELSE 'NI'
                   END AS tp_atendimento,
                   COUNT(*) AS qt_atendimento
              FROM projetos.atendimento a
             WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
               AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
               AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
               ".(intval($args['id_atendente']) > 0 ? " AND a.id_atendente = ".intval($args['id_atendente']) : "")."
               ".(trim($args['tipo_atendimento']) != '' ? " AND a.indic_ativo = '".$args['tipo_atendimento']."'" : "")."
             GROUP BY ds_tipo_atendimento,
                      tp_atendimento
             ORDER BY qt_atendimento DESC
        ";
        #echo '<pre>'.$qr_sql.'</pre>';

        $result = $this->db->query($qr_sql);
    }

    function listar_programa(&$result, $args=array())
    {
        $qr_sql = "
            SELECT lt.descricao AS tp_programa,
                   COUNT(*) AS qt_programa,
                   TO_CHAR(AVG(hr_tempo),'HH24:MI:SS') AS qt_tempo
              FROM projetos.atendimento_tela_capturada atc
              JOIN projetos.atendimento a
                    ON a.cd_atendimento = atc.cd_atendimento
              JOIN projetos.telas_programas tp
                    ON tp.cd_tela = atc.cd_tela
              JOIN public.listas lt
                ON lt.codigo = tp.cd_programa_fceee
               AND lt.categoria = 'PRFC'
             WHERE CAST(atc.dt_acesso AS DATE) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
               AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
               AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
               ".(intval($args['id_atendente']) > 0 ? " AND a.id_atendente = ".intval($args['id_atendente']) : "")."
               ".(trim($args['tipo_atendimento']) != '' ? " AND a.indic_ativo = '".$args['tipo_atendimento']."'" : "")."
             GROUP BY tp_programa
             ORDER BY qt_programa DESC
            ";

        #echo '<pre>'.$qr_sql.'</pre>';

        $result = $this->db->query($qr_sql);
    }

    function atendimento (&$result, $args=array())
    {
        $qr_sql = "
				SELECT a.cd_atendimento,
					   a.id_callcenter,
				       a.cd_plano, 
					   a.cd_empresa, 
					   a.cd_registro_empregado, 
					   a.seq_dependencia, 
					   a.obs, 
					   COALESCE(p.nome,a.nome) AS nome,
					   u.guerra, 
					   a.resp_encaminhamento, 
					   r.guerra AS ds_resp_encaminhamento,
					   TO_CHAR(a.dt_hora_inicio_atendimento, 'DD/MM/YYYY HH24:MI:SS') AS dt_atendimento, 
					   TO_CHAR(a.dt_hora_fim_atendimento, 'DD/MM/YYYY HH24:MI:SS') AS dt_fim_atendimento, 
					   TO_CHAR(a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento,'HH24:MI:SS') AS hr_atendimento, 
					   TO_CHAR(a.dt_encaminhamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encaminhamento, 
					   CASE WHEN dt_encaminhamento IS NOT NULL 
					        THEN 'Encaminhado'
							ELSE 'Aberto'
					   END AS situacao,
					   a.id_atendente, 
					   CASE WHEN (a.tipo_atendimento_indicado = 'E')
							THEN 'Empréstimo'
							ELSE a.tipo_atendimento_indicado
					   END AS tipo_atendimento_indicado
				  FROM projetos.atendimento a
				  JOIN projetos.usuarios_controledi u 
				    ON u.codigo = a.id_atendente 
				  LEFT JOIN projetos.usuarios_controledi r
				    ON u.codigo = a.resp_encaminhamento
				  LEFT JOIN participantes p
			        ON p.cd_empresa            = a.cd_empresa
			       AND p.cd_registro_empregado = a.cd_registro_empregado
			       AND p.seq_dependencia       = a.seq_dependencia				  
				 WHERE a.cd_atendimento = ".intval($args['cd_atendimento']);

         #echo '<pre>'.$qr_sql.'</pre>';

        $result = $this->db->query($qr_sql);
    }

    function encaminhamento(&$result, $args=array())
    {
        $qr_sql = "
            SELECT texto_encaminhamento,
                   cd_encaminhamento,
                   cd_atendimento,
                   CASE WHEN dt_cancelado IS NOT NULL THEN 'Cancelado'
                        WHEN dt_retorno_encaminhamento IS NOT NULL THEN 'Encaminhado'
			            ELSE 'Aberto'
			       END AS fl_encaminhamento
			  FROM projetos.atendimento_encaminhamento
		     WHERE cd_atendimento = ".intval($args['cd_atendimento']);

        #echo '<pre>'.$qr_sql.'</pre>';

        $result = $this->db->query($qr_sql);
    }

    function reclamacoes(&$result, $args=array())
    {
        $qr_sql = "
            SELECT texto_reclamacao,
                   cd_reclamacao
			  FROM projetos.atendimento_reclamacao
			 WHERE cd_atendimento = ".intval($args['cd_atendimento']);

        #echo '<pre>'.$qr_sql.'</pre>';

        $result = $this->db->query($qr_sql);
    }

    function reclamacoes_sugestoes(&$result, $args=array())
    {
        $qr_sql = "
            SELECT r.numero,
                   r.ano,
                   r.tipo,
                   TO_CHAR(r.numero,'FM0000') || '/' || TO_CHAR(r.ano,'FM0000') || '/' || r.tipo AS cd_reclamacao,
                   r.descricao,
                   TO_CHAR(ran.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_retorno,
                   ra.descricao AS ds_acao,
                   uca.nome AS ds_usuario_responsavel
              FROM projetos.reclamacao r
              LEFT JOIN projetos.reclamacao_andamento ra
                ON ra.numero                  = r.numero
               AND ra.ano                     = r.ano
               AND ra.tipo                    = r.tipo
               AND ra.tp_reclamacao_andamento = 'A'
              LEFT JOIN projetos.reclamacao_andamento ran
                ON ran.numero                  = r.numero
               AND ran.ano                     = r.ano
               AND ran.tipo                    = r.tipo
               AND ran.tp_reclamacao_andamento = 'R' --RETORNO
              LEFT JOIN projetos.reclamacao_atendimento rat
                ON rat.numero = r.numero
               AND rat.ano    = r.ano
               AND rat.tipo   = r.tipo
              LEFT JOIN projetos.usuarios_controledi uca
                ON uca.codigo = rat.cd_usuario_responsavel
             WHERE r.cd_atendimento = ".intval($args['cd_atendimento'])."
               AND r.dt_exclusao    IS NULL";

        #echo '<pre>'.$qr_sql.'</pre>';

        $result = $this->db->query($qr_sql);
    }

    function busca_atendimento(&$result, $args=array())
    {
        $qr_sql = "
            SELECT tp.nome_tela AS tela,
                   TO_CHAR(atc.dt_acesso,'HH24:MI') AS hr_hora,
                   lt.descricao AS tp_tela
              FROM projetos.atendimento_tela_capturada atc
              LEFT JOIN projetos.telas_programas tp
                ON tp.cd_tela = atc.cd_tela
              LEFT JOIN public.listas lt
                ON lt.codigo = tp.cd_programa_fceee
               AND lt.categoria = 'PRFC'
             WHERE atc.cd_atendimento = ".intval($args['cd_atendimento'])."
             ORDER BY atc.dt_acesso ASC";

        #echo '<pre>'.$qr_sql.'</pre>';

        $result = $this->db->query($qr_sql);
    }
	
	function gravacaoXcally(&$result, $args=array())
	{
        $qr_sql = "
                     SELECT vr.id,
					        acx.nome_arquivo
                       FROM projetos.arquivos_call_xcally acx
                       JOIN xcally.voice_recordings vr
                         ON vr.uniqueid = REPLACE(LOWER(acx.nome_arquivo),'.wav','')
                      WHERE acx.cd_atendimento = ".intval($args['cd_atendimento'])."
				  ";

        #echo '<pre>'.$qr_sql.'</pre>';

        $result = $this->db->query($qr_sql);		
	}
}
