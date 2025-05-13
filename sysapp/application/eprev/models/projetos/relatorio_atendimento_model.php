<?php
class relatorio_atendimento_model extends Model
{
    function __construct()
	{
		parent::Model();
	}

    function lista_atendente( &$result, $args=array() )
	{
        $qr_sql = "
				SELECT u.id_atendente,
					   u.guerra,
					   SUM(COALESCE(qt.qt_telefone,0)) AS qt_telefone,
					   SUM(COALESCE(qp.qt_pessoal,0)) AS qt_pessoal,
					   SUM(COALESCE(qe.qt_email,0)) AS qt_email,
					   (SUM(COALESCE(qt.qt_telefone,0)) + SUM(COALESCE(qp.qt_pessoal,0)) + SUM(COALESCE(qe.qt_email,0))) AS qt_total,
					   TO_CHAR(AVG(ht.hr_media_telefone),'HH24:MI:SS') AS hr_media_telefone,
					   TO_CHAR(AVG(hp.hr_media_pessoal),'HH24:MI:SS') AS hr_media_pessoal,
					   TO_CHAR(AVG(he.hr_media_email),'HH24:MI:SS') AS hr_media_email
				  FROM (SELECT a.id_atendente,
							   u.guerra
						  FROM projetos.atendimento a,
							   projetos.usuarios_controledi u
						 WHERE a.id_atendente = u.codigo
						   AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                           AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
						 GROUP BY a.id_atendente,
								  u.guerra) u
				  LEFT JOIN (SELECT a.id_atendente,
									u.guerra,
									COUNT(a.id_atendente) AS qt_telefone
							   FROM projetos.atendimento a,
									projetos.usuarios_controledi u
							  WHERE a.id_atendente = u.codigo
								AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                                AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
								AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
								AND a.indic_ativo IN ('a','T') -- contar Avulsos como telefone (MARCUS/GAP - 09/07/2007)
							  GROUP BY a.id_atendente,
									   u.guerra) qt
					ON qt.id_atendente = u.id_atendente
				  LEFT JOIN (SELECT a.id_atendente,
									u.guerra,
									COUNT(a.id_atendente) AS qt_pessoal
							   FROM projetos.atendimento a,
									projetos.usuarios_controledi u
							  WHERE a.id_atendente               = u.codigo
								AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                                AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
								AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
								AND a.indic_ativo = 'P'
							  GROUP BY a.id_atendente,
									   u.guerra) qp
					ON qp.id_atendente = u.id_atendente
				  LEFT JOIN (SELECT a.id_atendente,
									u.guerra,
									COUNT(a.id_atendente) AS qt_email
							   FROM projetos.atendimento a,
									projetos.usuarios_controledi u
							  WHERE a.id_atendente               = u.codigo
								AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                                AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
								AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
								AND a.indic_ativo = 'E'
							  GROUP BY a.id_atendente,
									   u.guerra) qe
					ON qe.id_atendente = u.id_atendente
				  LEFT JOIN (SELECT a.id_atendente,
									u.guerra,
									AVG(a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) AS hr_media_telefone
							   FROM projetos.atendimento a,
									projetos.usuarios_controledi u
							  WHERE a.id_atendente               = u.codigo
								AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                                AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
								AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
								AND a.indic_ativo                IN ('a','T') -- contar Avulsos como telefone (MARCUS/GAP - 09/07/2007)
							  GROUP BY a.id_atendente,
									   u.guerra) ht
					ON ht.id_atendente = u.id_atendente
				  LEFT JOIN (SELECT a.id_atendente,
									u.guerra,
									AVG(a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento)  AS hr_media_pessoal
							   FROM projetos.atendimento a,
									projetos.usuarios_controledi u
							  WHERE a.id_atendente               = u.codigo
								AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                                AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
								AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
								AND a.indic_ativo                = 'P'
							  GROUP BY a.id_atendente,
									   u.guerra) hp
					ON hp.id_atendente = u.id_atendente
				  LEFT JOIN (SELECT a.id_atendente,
									u.guerra,
									AVG(a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento)  AS hr_media_email
							   FROM projetos.atendimento a,
									projetos.usuarios_controledi u
							  WHERE a.id_atendente               = u.codigo
								AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                                AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
								AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
								AND a.indic_ativo                = 'E'
							  GROUP BY a.id_atendente,
									   u.guerra) he
					ON he.id_atendente = u.id_atendente
				GROUP BY u.id_atendente,
						 u.guerra
				ORDER BY qt_total DESC
	       ";

        $result = $this->db->query($qr_sql);
    }

    function lista_horario( &$result, $args=array() )
	{
        $qr_sql = "
				SELECT h.hr_ini,
					   (SUM(h.qt_pessoal) + SUM(h.qt_telefone) + SUM(h.qt_email)) AS qt_total,
				       SUM(h.qt_pessoal) AS qt_pessoal,
				       SUM(h.hr_media_pessoal) AS hr_media_pessoal,
				       SUM(h.qt_telefone) AS qt_telefone,
				       SUM(h.hr_media_telefone) AS hr_media_telefone,
				       SUM(h.qt_email) AS qt_email,
				       SUM(h.hr_media_email) AS hr_media_email
				  FROM (
						-- ATENDIMENTOS PESSOAL
						SELECT TO_CHAR(DATE_TRUNC('hour',a.dt_hora_inicio_atendimento),'HH24:MI') AS hr_ini,
						       COUNT(a.id_atendente) AS qt_pessoal,
						       TO_CHAR(AVG(a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento),'HH24:MI:SS')::TIME AS hr_media_pessoal,
						       0 AS qt_telefone,
						       '00:00:00'::interval AS hr_media_telefone,
						       0 AS qt_email,
						       '00:00:00'::TIME AS hr_media_email
						  FROM projetos.atendimento a
						 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                           AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
						   AND a.indic_ativo = 'P'
						 GROUP BY hr_ini

						 UNION

						-- ATENDIMENTO TELEFONE
						SELECT TO_CHAR(DATE_TRUNC('hour',a.dt_hora_inicio_atendimento),'HH24:MI') AS hr_ini,
						       0 AS qt_pessoal,
						       '00:00:00'::TIME AS hr_media_pessoal,
						       COUNT(a.id_atendente) AS qt_telefone,
						       TO_CHAR(AVG(a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento),'HH24:MI:SS')::interval AS hr_media_telefone,
						       0 AS qt_email,
						       '00:00:00'::TIME AS hr_media_email
						  FROM projetos.atendimento a
						 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                           AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
						   AND a.indic_ativo IN ('a','T') -- contar Avulsos como telefone (MARCUS/GAP - 09/07/2007)
						 GROUP BY hr_ini

						UNION

						-- ATENDIMENTOS EMAIL
						SELECT TO_CHAR(DATE_TRUNC('hour',a.dt_hora_inicio_atendimento),'HH24:MI') AS hr_ini,
						       0 AS qt_pessoal,
						       '00:00:00'::TIME AS hr_media_pessoal,
						       0 AS qt_telefone,
						       '00:00:00'::interval AS hr_media_telefone,
						       COUNT(a.id_atendente) AS qt_email,
						       TO_CHAR(AVG(a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento),'HH24:MI:SS')::TIME AS hr_media_email
						  FROM projetos.atendimento a
						 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                           AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
						   AND a.indic_ativo = 'E'
						 GROUP BY hr_ini
				       ) h
				 GROUP BY h.hr_ini
				 ORDER BY h.hr_ini
	       ";

        $result = $this->db->query($qr_sql);
    }

    function lista_tipo_atendimento( &$result, $args=array() )
    {
        $qr_sql = "
				SELECT a1.indic_ativo,
				       CASE WHEN (a1.indic_ativo = 'T') THEN 'Telefônico'
					        WHEN (a1.indic_ativo = 'P') THEN 'Pessoal'
							WHEN (a1.indic_ativo = 'C') THEN 'Administrativo'
							WHEN (a1.indic_ativo = 'E') THEN 'E-mail'
				       END AS ds_tipo_atendimento,
				       COALESCE(ta.qt_total_avulso,0) AS qt_avulso,
				       COALESCE(tp.qt_total_normal,0) AS qt_normal,
				       COALESCE(tn.qt_total_nao_partipante,0) AS qt_nao_partipante
				  FROM (SELECT DISTINCT(a.indic_ativo) AS indic_ativo
				          FROM projetos.atendimento a
				         WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                           AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
				           AND a.indic_ativo IN ('T','P','C','E')) a1
				  LEFT JOIN(SELECT a.indic_ativo,
					               COUNT(*) AS qt_total_avulso
					      FROM projetos.atendimento a
					     WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                           AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
					       AND a.origem_atendimento = 'A'
					     GROUP BY a.indic_ativo) ta
				    ON ta.indic_ativo = a1.indic_ativo
				  LEFT JOIN(SELECT a.indic_ativo,
					               COUNT(*) AS qt_total_normal
					      FROM projetos.atendimento a
					     WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                           AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
					       AND a.origem_atendimento = 'P'
					     GROUP BY a.indic_ativo) tp
				    ON tp.indic_ativo = a1.indic_ativo
				  LEFT JOIN(SELECT a.indic_ativo,
					               COUNT(*) AS qt_total_nao_partipante
					      FROM projetos.atendimento a
					     WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                           AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
					       AND a.origem_atendimento = 'N'
					     GROUP BY a.indic_ativo) tn
				    ON tn.indic_ativo = a1.indic_ativo
                 ORDER BY ds_tipo_atendimento ASC
	          ";	

        $result = $this->db->query($qr_sql);
    }

    function lista_programas( &$result, $args=array() )
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
				 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                   AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
				   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
				   AND a.indic_ativo IN ('T','P','E')
				 GROUP BY tp_programa
				 ORDER BY qt_programa DESC
		      ";
        $result = $this->db->query($qr_sql);
    }

    function lista_programa_tipo_atendimento( &$result, $args=array() )
    {
        $qr_sql = "
				SELECT h.tp_programa,
					   (SUM(h.qt_pessoal) + SUM(h.qt_telefone) + SUM(h.qt_email)) AS qt_total,
				       SUM(h.qt_pessoal) AS qt_pessoal,
				       SUM(h.hr_media_pessoal) AS hr_media_pessoal,
				       SUM(h.qt_telefone) AS qt_telefone,
				       SUM(h.hr_media_telefone) AS hr_media_telefone,
				       SUM(h.qt_email) AS qt_email,
				       SUM(h.hr_media_email) AS hr_media_email       
				  FROM (
						-- ATENDIMENTOS PESSOAL
						 SELECT lt.descricao AS tp_programa,
								COUNT(*) AS qt_pessoal,
								TO_CHAR(AVG(hr_tempo),'HH24:MI:SS')::TIME AS hr_media_pessoal,
								0 AS qt_telefone,
								'00:00:00'::TIME AS hr_media_telefone,
								0 AS qt_email,
								'00:00:00'::TIME AS hr_media_email 
						  FROM projetos.atendimento_tela_capturada atc	
						  JOIN projetos.atendimento a
							ON a.cd_atendimento = atc.cd_atendimento
						  JOIN projetos.telas_programas tp
							ON tp.cd_tela = atc.cd_tela
						  JOIN public.listas lt
							ON lt.codigo = tp.cd_programa_fceee
						   AND lt.categoria = 'PRFC'			  
						 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')  
                           AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
						   AND a.indic_ativo = 'P'
						 GROUP BY tp_programa

						UNION

						-- ATENDIMENTOS TELEFONE
						 SELECT lt.descricao AS tp_programa,
								0 AS qt_pessoal,
								'00:00:00'::TIME AS hr_media_pessoal,
								COUNT(*) AS qt_telefone,
								TO_CHAR(AVG(hr_tempo),'HH24:MI:SS')::TIME AS hr_media_telefone,
								0 AS qt_email,
								'00:00:00'::TIME AS hr_media_email 
						  FROM projetos.atendimento_tela_capturada atc	
						  JOIN projetos.atendimento a
							ON a.cd_atendimento = atc.cd_atendimento
						  JOIN projetos.telas_programas tp
							ON tp.cd_tela = atc.cd_tela
						  JOIN public.listas lt
							ON lt.codigo = tp.cd_programa_fceee
						   AND lt.categoria = 'PRFC'			  
						 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')  
                           AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
						   AND a.indic_ativo IN ('a','T') -- contar Avulsos como telefone (MARCUS/GAP - 09/07/2007) 
						 GROUP BY tp_programa

						UNION

						-- ATENDIMENTOS EMAIL
						 SELECT lt.descricao AS tp_programa,
								0 AS qt_pessoal,
								'00:00:00'::TIME AS hr_media_pessoal,
								0 AS qt_telefone,
								'00:00:00'::TIME AS hr_media_telefone,
								COUNT(*) AS qt_email,
								TO_CHAR(AVG(hr_tempo),'HH24:MI:SS')::TIME AS hr_media_email 
						  FROM projetos.atendimento_tela_capturada atc	
						  JOIN projetos.atendimento a
							ON a.cd_atendimento = atc.cd_atendimento
						  JOIN projetos.telas_programas tp
							ON tp.cd_tela = atc.cd_tela
						  JOIN public.listas lt
							ON lt.codigo = tp.cd_programa_fceee
						   AND lt.categoria = 'PRFC'			  
						 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')  
                           AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
						   AND a.indic_ativo = 'E'
						 GROUP BY tp_programa
				       ) h
				 GROUP BY h.tp_programa
				 ORDER BY h.tp_programa	
	       ";

        $result = $this->db->query($qr_sql);
    }

    function lista_empresa_planos( &$result, $args=array() )
    {
        $qr_sql = "
					SELECT pa.sigla,
					       up.descricao,
						   COUNT(*) AS total
					  FROM projetos.atendimento a
					  JOIN public.participantes p
						ON p.cd_empresa            = a.cd_empresa
					   AND p.cd_registro_empregado = a.cd_registro_empregado
					   AND p.seq_dependencia       = a.seq_dependencia
					  JOIN(SELECT pl.cd_plano,
								  pl.descricao,
								  tp.cd_empresa,
								  tp.cd_registro_empregado,
								  tp.seq_dependencia
							 FROM public.titulares_planos tp
							 JOIN public.planos pl
							   ON tp.cd_plano = pl.cd_plano
							WHERE tp.dt_ingresso_plano = (SELECT MAX(tp1.dt_ingresso_plano)
															FROM public.titulares_planos tp1
														   WHERE tp1.cd_empresa            = tp.cd_empresa
															 AND tp1.cd_registro_empregado = tp.cd_registro_empregado
															 AND tp1.seq_dependencia       = tp.seq_dependencia)) AS up
						ON up.cd_empresa            = a.cd_empresa
					   AND up.cd_registro_empregado = a.cd_registro_empregado
					   AND up.seq_dependencia       = a.seq_dependencia
					  JOIN public.patrocinadoras pa
						ON pa.cd_empresa = up.cd_empresa
					 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
					   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
					   AND a.indic_ativo IN ('a','T','P','E')
					 GROUP BY pa.sigla,
					          up.descricao		
				     ORDER BY pa.sigla,
					          up.descricao
	              ";
        $result = $this->db->query($qr_sql);
    }
	
    function lista_empresa_planos_tipo_atendimento( &$result, $args=array() )
    {
        $qr_sql = "
					SELECT h.emp_plano,
						   (SUM(h.qt_pessoal) + SUM(h.qt_telefone) + SUM(h.qt_email)) AS qt_total,
						   SUM(h.qt_pessoal) AS qt_pessoal,
						   SUM(h.hr_media_pessoal) AS hr_media_pessoal,
						   SUM(h.qt_telefone) AS qt_telefone,
						   SUM(h.hr_media_telefone) AS hr_media_telefone,
						   SUM(h.qt_email) AS qt_email,
						   SUM(h.hr_media_email) AS hr_media_email       
					  FROM (
							-- ATENDIMENTOS PESSOAL
							SELECT pa.sigla || ' - ' ||  up.descricao AS emp_plano,
								   COUNT(*) AS qt_pessoal,
								   TO_CHAR(AVG((a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento)),'HH24:MI:SS')::TIME AS hr_media_pessoal,
								   0 AS qt_telefone,
								   '00:00:00'::TIME AS hr_media_telefone,
								   0 AS qt_email,
								   '00:00:00'::TIME AS hr_media_email 
							  FROM projetos.atendimento a
							  JOIN public.participantes p
								ON p.cd_empresa            = a.cd_empresa
							   AND p.cd_registro_empregado = a.cd_registro_empregado
							   AND p.seq_dependencia       = a.seq_dependencia
							  JOIN(SELECT pl.cd_plano,
										  pl.descricao,
										  tp.cd_empresa,
										  tp.cd_registro_empregado,
										  tp.seq_dependencia
									 FROM public.titulares_planos tp
									 JOIN public.planos pl
									   ON tp.cd_plano = pl.cd_plano
									WHERE tp.dt_ingresso_plano = (SELECT MAX(tp1.dt_ingresso_plano)
																	FROM public.titulares_planos tp1
																   WHERE tp1.cd_empresa            = tp.cd_empresa
																	 AND tp1.cd_registro_empregado = tp.cd_registro_empregado
																	 AND tp1.seq_dependencia       = tp.seq_dependencia)) AS up
								ON up.cd_empresa            = a.cd_empresa
							   AND up.cd_registro_empregado = a.cd_registro_empregado
							   AND up.seq_dependencia       = a.seq_dependencia
							  JOIN public.patrocinadoras pa
								ON pa.cd_empresa = up.cd_empresa
							 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
							   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
							   AND a.indic_ativo = 'P'
							 GROUP BY emp_plano
							 
							UNION

							-- ATENDIMENTOS TELEFONE
							SELECT pa.sigla || ' - ' ||  up.descricao AS emp_plano,
								   0 AS qt_pessoal,
								   '00:00:00'::TIME AS hr_media_pessoal,
								   COUNT(*) AS qt_telefone,
								   TO_CHAR(AVG((a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento)),'HH24:MI:SS')::TIME AS hr_media_telefone,
								   0 AS qt_email,
								   '00:00:00'::TIME AS hr_media_email 
							  FROM projetos.atendimento a
							  JOIN public.participantes p
								ON p.cd_empresa            = a.cd_empresa
							   AND p.cd_registro_empregado = a.cd_registro_empregado
							   AND p.seq_dependencia       = a.seq_dependencia
							  JOIN(SELECT pl.cd_plano,
										  pl.descricao,
										  tp.cd_empresa,
										  tp.cd_registro_empregado,
										  tp.seq_dependencia
									 FROM public.titulares_planos tp
									 JOIN public.planos pl
									   ON tp.cd_plano = pl.cd_plano
									WHERE tp.dt_ingresso_plano = (SELECT MAX(tp1.dt_ingresso_plano)
																	FROM public.titulares_planos tp1
																   WHERE tp1.cd_empresa            = tp.cd_empresa
																	 AND tp1.cd_registro_empregado = tp.cd_registro_empregado
																	 AND tp1.seq_dependencia       = tp.seq_dependencia)) AS up
								ON up.cd_empresa            = a.cd_empresa
							   AND up.cd_registro_empregado = a.cd_registro_empregado
							   AND up.seq_dependencia       = a.seq_dependencia
							  JOIN public.patrocinadoras pa
								ON pa.cd_empresa = up.cd_empresa
							 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
							   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
							   AND a.indic_ativo IN ('a','T') -- contar Avulsos como telefone (MARCUS/GAP - 09/07/2007)
							 GROUP BY emp_plano
							 
							UNION

							-- ATENDIMENTOS EMAIL
							SELECT pa.sigla || ' - ' ||  up.descricao AS emp_plano,
								   0 AS qt_pessoal,
								   '00:00:00'::TIME AS hr_media_pessoal,
								   0 AS qt_telefone,
								   '00:00:00'::TIME AS hr_media_telefone,
								   COUNT(*) AS qt_email,
								   TO_CHAR(AVG((a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento)),'HH24:MI:SS')::TIME AS hr_media_email
							  FROM projetos.atendimento a
							  JOIN public.participantes p
								ON p.cd_empresa            = a.cd_empresa
							   AND p.cd_registro_empregado = a.cd_registro_empregado
							   AND p.seq_dependencia       = a.seq_dependencia
							  JOIN(SELECT pl.cd_plano,
										  pl.descricao,
										  tp.cd_empresa,
										  tp.cd_registro_empregado,
										  tp.seq_dependencia
									 FROM public.titulares_planos tp
									 JOIN public.planos pl
									   ON tp.cd_plano = pl.cd_plano
									WHERE tp.dt_ingresso_plano = (SELECT MAX(tp1.dt_ingresso_plano)
																	FROM public.titulares_planos tp1
																   WHERE tp1.cd_empresa            = tp.cd_empresa
																	 AND tp1.cd_registro_empregado = tp.cd_registro_empregado
																	 AND tp1.seq_dependencia       = tp.seq_dependencia)) AS up
								ON up.cd_empresa            = a.cd_empresa
							   AND up.cd_registro_empregado = a.cd_registro_empregado
							   AND up.seq_dependencia       = a.seq_dependencia
							  JOIN public.patrocinadoras pa
								ON pa.cd_empresa = up.cd_empresa
							 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
							   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
							   AND a.indic_ativo = 'E'
							 GROUP BY emp_plano 
						   ) h
					 GROUP BY h.emp_plano
					 ORDER BY h.emp_plano
	              ";
        $result = $this->db->query($qr_sql);
    }	

    function lista_tipo_participante( &$result, $args=array() )
    {
        $qr_sql = "
					SELECT CASE WHEN projetos.participante_tipo(a.cd_empresa::numeric, a.cd_registro_empregado::numeric, a.seq_dependencia::numeric) IN ('SEMP','OUTR','ERRO') THEN 'Sem Plano'
								WHEN projetos.participante_tipo(a.cd_empresa::numeric, a.cd_registro_empregado::numeric, a.seq_dependencia::numeric) = 'APOS' THEN 'Aposentado'
								WHEN projetos.participante_tipo(a.cd_empresa::numeric, a.cd_registro_empregado::numeric, a.seq_dependencia::numeric) = 'ATIV' THEN 'Ativo'
								WHEN projetos.participante_tipo(a.cd_empresa::numeric, a.cd_registro_empregado::numeric, a.seq_dependencia::numeric) = 'AUXD' THEN 'Auxílio Doença'
								WHEN projetos.participante_tipo(a.cd_empresa::numeric, a.cd_registro_empregado::numeric, a.seq_dependencia::numeric) = 'CTP' THEN 'CTP'
								WHEN projetos.participante_tipo(a.cd_empresa::numeric, a.cd_registro_empregado::numeric, a.seq_dependencia::numeric) = 'EXAU' THEN 'Ex-Autárquico'
								WHEN projetos.participante_tipo(a.cd_empresa::numeric, a.cd_registro_empregado::numeric, a.seq_dependencia::numeric) = 'PENS' THEN 'Pensionista'
								ELSE projetos.participante_tipo(a.cd_empresa::numeric, a.cd_registro_empregado::numeric, a.seq_dependencia::numeric)
						   END AS tipo,
						   COUNT(*) AS qt_total
					  FROM projetos.atendimento a
					 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
					   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
					   AND a.indic_ativo IN ('T','P','E')
					 GROUP BY tipo
					 ORDER BY tipo;
		      ";
        $result = $this->db->query($qr_sql);
    }	
	
    function lista_reclamacoes( &$result, $args=array() )
    {
        $qr_sql = "
				SELECT t.ds_programa,
					   SUM(t.qt_total) AS qt_total
				  FROM (SELECT l.descricao AS ds_programa,
							   COUNT(*) AS qt_total
						  FROM projetos.atendimento a,
							   projetos.atendimento_reclamacao ar,
							   public.listas l
						 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                           AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
						   AND a.cd_atendimento = ar.cd_atendimento
						   AND l.codigo         = ar.cd_programa_institucional
						   AND l.categoria      = 'PRFC'
						 GROUP BY ds_programa

						 UNION

						SELECT rp.ds_reclamacao_programa AS ds_programa,
							   COUNT(*) AS qt_total
						  FROM projetos.reclamacao r
						  JOIN projetos.reclamacao_programa rp
							ON rp.cd_reclamacao_programa = r.cd_reclamacao_programa
						 WHERE DATE_TRUNC('day',r.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                           AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
						   AND r.dt_exclusao IS NULL
						   AND r.tipo        = 'R'
						 GROUP BY rp.ds_reclamacao_programa) AS t
					 GROUP BY ds_programa
					 ORDER BY qt_total DESC
		      ";
        $result = $this->db->query($qr_sql);
    }

    function lista_reclamacoes_lista( &$result, $args=array() )
    {
        $qr_sql = "
				SELECT a.cd_atendimento,
				       a.cd_empresa,
				       a.cd_registro_empregado,
				       a.seq_dependencia,
				       ar.texto_reclamacao AS obs,
				       l.descricao AS ds_programa,
				       TO_CHAR(arr.dt_retorno,'DD/MM/YYYY HH24:MI') AS dt_retorno
				  FROM projetos.atendimento a
				  JOIN projetos.atendimento_reclamacao ar
				    ON ar.cd_atendimento = a.cd_atendimento
				  JOIN public.listas l
				    ON l.codigo         = ar.cd_programa_institucional
				   AND l.categoria      = 'PRFC'
				  LEFT JOIN projetos.atendimento_retorno arr
				    ON arr.cd_atendimento = a.cd_atendimento
				 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                   AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')

				 UNION

				SELECT r.cd_atendimento,
				       r.cd_empresa,
				       r.cd_registro_empregado,
				       r.seq_dependencia,
				       TO_CHAR(r.numero,'FM0000') || '/' || TO_CHAR(r.ano,'FM0000') || '/' || r.tipo || ': ' || r.descricao,
				       rp.ds_reclamacao_programa AS ds_programa,
				       TO_CHAR(ran.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_retorno
				  FROM projetos.reclamacao r
				  JOIN projetos.reclamacao_programa rp
				    ON rp.cd_reclamacao_programa = r.cd_reclamacao_programa
			      LEFT JOIN projetos.reclamacao_andamento ran
				    ON ran.numero                  = r.numero
				   AND ran.ano                     = r.ano
				   AND ran.tipo                    = r.tipo
				   AND ran.tp_reclamacao_andamento = 'R'
				 WHERE DATE_TRUNC('day',r.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                   AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
				   AND r.dt_exclusao IS NULL
				   AND r.tipo = 'R'
				 ORDER BY ds_programa ASC,
                          cd_atendimento ASC
        ";
        $result = $this->db->query($qr_sql);
    }

    function lista_sugestoes( &$result, $args=array() )
    {
        $qr_sql = "
				SELECT rp.ds_reclamacao_programa AS ds_programa,
					   COUNT(*) AS qt_total
				  FROM projetos.reclamacao r
				  JOIN projetos.reclamacao_programa rp
					ON rp.cd_reclamacao_programa = r.cd_reclamacao_programa
				 WHERE DATE_TRUNC('day',r.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                   AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
				   AND r.dt_exclusao IS NULL
				   AND r.tipo        = 'S'
				 GROUP BY rp.ds_reclamacao_programa
				 ORDER BY qt_total DESC
		      ";

        $result = $this->db->query($qr_sql);
    }

    function lista_sugestoes_lista( &$result, $args=array() )
    {
        $qr_sql = "
				SELECT r.cd_atendimento,
				       r.cd_empresa,
				       r.cd_registro_empregado,
				       r.seq_dependencia,
				       TO_CHAR(r.numero,'FM0000') || '/' || TO_CHAR(r.ano,'FM0000') || '/' || r.tipo || ': ' || r.descricao AS obs,
				       rp.ds_reclamacao_programa AS ds_programa,
				       TO_CHAR(ran.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_retorno
				  FROM projetos.reclamacao r
				  JOIN projetos.reclamacao_programa rp
				    ON rp.cd_reclamacao_programa = r.cd_reclamacao_programa
			      LEFT JOIN projetos.reclamacao_andamento ran
				    ON ran.numero                  = r.numero
				   AND ran.ano                     = r.ano
				   AND ran.tipo                    = r.tipo
				   AND ran.tp_reclamacao_andamento = 'S'
				 WHERE DATE_TRUNC('day',r.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                   AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
				   AND r.dt_exclusao IS NULL
				   AND r.tipo = 'S'
				 ORDER BY ds_programa ASC,
                          cd_atendimento ASC
		      ";

        $result = $this->db->query($qr_sql);
    }

    function lista_encaminhamentos( &$result, $args=array() )
    {
        $qr_sql = " 
				SELECT a.cd_atendimento, 
				       a.cd_empresa, 
				       a.cd_registro_empregado, 
				       a.seq_dependencia, 
				       ae.texto_encaminhamento AS obs
				  FROM projetos.atendimento a,  
				       projetos.atendimento_encaminhamento ae
				 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY') 
                   AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
				   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
				   AND a.cd_atendimento = ae.cd_atendimento
				ORDER BY a.cd_atendimento
		      ";

        $result = $this->db->query($qr_sql);
    }

    function lista_encaminhamentos_gerencias( &$result, $args=array() )
    {
        $qr_sql = "
				SELECT a.area,
				       COUNT(*) AS qt_area
				  FROM projetos.atividades a,
				       listas l
				 WHERE DATE_TRUNC('day',dt_cad) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                   AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
				   AND a.divisao = 'GAP'
				   AND a.cd_registro_empregado IS NOT NULL
				   AND a.cd_registro_empregado > 0
				   AND a.status_atual = l.codigo
				   AND l.categoria = 'STAT'
				 GROUP BY a.area
				 ORDER BY qt_area DESC
		      ";

        $result = $this->db->query($qr_sql);
    }

    function lista_encaminhamentos_gerencias_listar( &$result, $args=array() )
    {

        $qr_sql = "
				SELECT a.numero,
				       TO_CHAR(a.dt_cad,'DD/MM/YYYY HH24:MI') AS dt_cadastro,
				       TRIM(a.descricao) AS descricao,
				       l.descricao AS status,
				       a.cd_empresa,
				       a.cd_registro_empregado,
				       a.cd_sequencia,
					   a.area
				  FROM projetos.atividades a,
				       listas l
				 WHERE DATE_TRUNC('day',dt_cad) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                   AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
				   AND a.divisao = 'GAP'
				   AND a.cd_registro_empregado IS NOT NULL
				   AND a.cd_registro_empregado > 0
				   AND a.status_atual = l.codigo
				   AND l.categoria = 'STAT'
				 ORDER BY numero
		      ";

        $result = $this->db->query($qr_sql);
    }

    function lista_capa( &$result, $args=array() )
    {
        $qr_sql = "
				SELECT TO_CHAR(AVG(a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento),'HH24:MI:SS') AS hr_media,
                       COUNT(*) AS qt_atendimento
				  FROM projetos.atendimento a, 
				       projetos.usuarios_controledi u
				 WHERE a.id_atendente = u.codigo 
				   AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY') 
                   AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
				   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
				   AND a.indic_ativo IN ('T','P','E')
	          ";


        $result = $this->db->query($qr_sql);
    }

    function lista_tempo_espera( &$result, $args=array() )
    {
        $qr_sql = "
				SELECT TO_CHAR(AVG((TO_TIMESTAMP(TO_CHAR(a.dt_hora_inicio_atendimento,'HH24:MI'), 'HH24:MI') - TO_TIMESTAMP((a.hora_senha), 'HH24:MI'))),'HH24:MI:SS') AS hr_media,
				       funcoes.converte_segundo_hora(TRUNC(STDDEV(funcoes.converte_hora_segundo((TO_CHAR((TO_TIMESTAMP(TO_CHAR(a.dt_hora_inicio_atendimento,'HH24:MI'), 'HH24:MI') - TO_TIMESTAMP((a.hora_senha), 'HH24:MI')),'HH24:MI:SS')))))) AS hr_desvio
				  FROM projetos.atendimento a
				 WHERE DATE_TRUNC('day',dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')
                   AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
				   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
				   AND a.hora_senha       <> '00:00'
				   AND a.indic_ativo      = 'P'
				   AND TRIM(a.hora_senha) <> ''
				   AND a.hora_senha       IS NOT NULL
				   AND TO_TIMESTAMP(TO_CHAR(a.dt_hora_inicio_atendimento,'HH24:MI'), 'HH24:MI') - TO_TIMESTAMP(a.hora_senha, 'HH24:MI') > '00:00:00'::INTERVAL
	          ";


        $result = $this->db->query($qr_sql);
    }
  
    function lista_email_rt( &$result, $args=array() )
    {
        $qr_sql = "
					SELECT CASE WHEN t.status IN ('new','open') THEN 'Aberto'
								WHEN t.status = 'stalled'       THEN 'Pendente'
								WHEN t.status = 'rejected'      THEN 'Rejeitado'
								WHEN t.status = 'resolved'      THEN 'Resolvido'
						   END AS situacao,
						   COUNT(*) AS qt_email
					  FROM rt.tickets t
					 WHERE DATE_TRUNC('day',t.created) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
					   AND t.status IN ('new','open','stalled','rejected','resolved')
					   AND t.queue = 4 -- Fila Principal de Atendimento ao Participante 
					 GROUP BY situacao
					 ORDER BY situacao
		          ";
        $result = $this->db->query($qr_sql);
    }
	

    function lista_email_rt_interno( &$result, $args=array() )
    {
        $qr_sql = "
					SELECT CASE WHEN t.status IN ('new','open') THEN 'Aberto'
								WHEN t.status = 'stalled'       THEN 'Pendente'
								WHEN t.status = 'rejected'      THEN 'Rejeitado'
								WHEN t.status = 'resolved'      THEN 'Resolvido'
						   END AS situacao,
						   COUNT(*) AS qt_email
					  FROM rt.tickets t
					 WHERE DATE_TRUNC('day',t.created) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
					   AND t.status IN ('new','open','stalled','rejected','resolved')
					   AND t.queue > 4 -- DEMAIS FILAS
					 GROUP BY situacao
					 ORDER BY situacao
		          ";
        $result = $this->db->query($qr_sql);
    }

    function lista_email_rt_interno_tickets( &$result, $args=array() )
    {
        $qr_sql = "
        	 SELECT COALESCE(t.effectiveid,t.id) AS codigo,
			       TO_CHAR(t.created,'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro,
			       CASE WHEN t.status IN ('new','open') THEN 'Aberto'
			 	    WHEN t.status = 'stalled'       THEN 'Pendente'
				    WHEN t.status = 'rejected'      THEN 'Rejeitado'
				    WHEN t.status = 'resolved'      THEN 'Resolvido'
			       END AS situacao,
			       convert_from(convert_to(t.subject,'utf-8'),'latin-1') AS assunto,
			       convert_from(convert_to(q.name,'utf-8'),'latin-1') AS fila,
			       u.name AS usuario
			  FROM rt.tickets t
			  JOIN rt.queues q
			    ON q.id  = t.queue
			  JOIN rt.users u
			    ON u.id = t.owner
			 WHERE DATE_TRUNC('day',t.created) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
			   AND t.queue > 4
			   AND t.status IN ('new','open','stalled','rejected','resolved')
			 ORDER BY t.created;";
        $result = $this->db->query($qr_sql);
    }

    function lista_email_tempo_resposta_rt( &$result, $args=array() )
    {
        $qr_sql = "
					SELECT TO_CHAR(tr.resolved,'DD/MM/YYYY') AS dt_referencia,
					       tr.resolved::DATE AS dt_ordem,
					       SUM(tr.tempo_resposta_real) AS hr_real,
                           SUM(tr.tempo_resposta_util) AS hr_util,
						   COUNT(*) AS qt_email
					  FROM rt.tempo_resposta tr
					 WHERE tr.resolved BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
					   AND tr.resolved IS NOT NULL
					 GROUP BY dt_referencia, dt_ordem
					 ORDER BY dt_ordem
		          ";
        $result = $this->db->query($qr_sql);
    }	

    function lista_atendimento_retorno_listar( &$result, $args=array() )
    {
        $qr_sql = "
				SELECT a.cd_atendimento,
				       a.cd_empresa,
				       a.cd_registro_empregado,
				       a.seq_dependencia,
				       ar.texto_retorno AS retorno,
				       TO_CHAR(ar.dt_retorno,'DD/MM/YYYY HH24:MI') AS dt_retorno
				  FROM projetos.atendimento a
				  JOIN projetos.atendimento_retorno ar
				    ON ar.cd_atendimento = a.cd_atendimento
				 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
				 ORDER BY a.cd_atendimento
        ";
        $result = $this->db->query($qr_sql);
    }	
}
?>