<?php
class Relatorio_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function relatorio_contato(&$result, $args=array())
	{
		$qr_sql = "
					SELECT r.ano_mes, 
						   COALESCE(i.qt_interessado,0) AS qt_interessado,
						   COALESCE(ic.qt_contato,0) AS qt_contato,
						   COALESCE(it.qt_inscrito,0) AS qt_inscrito,
						   COALESCE(ip.qt_participante,0) AS qt_participante
					  FROM 
					(
					SELECT TRIM(TO_CHAR(ano, 'FM0000')) || '/' || TRIM(TO_CHAR(mes, 'FM00')) AS ano_mes
					  FROM generate_series(".intval($args['nr_ano']).", ".intval($args['nr_ano']).") AS ano, 
						   generate_series(1,12) AS mes 
					 ORDER BY ano_mes				   
					) AS r

					LEFT JOIN 
					(
					-- INTERESSADOS
					SELECT TO_CHAR(i1.dt_inclusao,'YYYY/MM') AS ano_mes,
						   COUNT(i1.cpf) AS qt_interessado
					  FROM senge_previdencia.interessado i1
					 WHERE i1.dt_exclusao IS NULL
					   AND TO_CHAR(i1.dt_inclusao,'YYYY') = '".intval($args['nr_ano'])."'
					 GROUP BY ano_mes
					) i ON i.ano_mes = r.ano_mes

					LEFT JOIN 
					(
					-- INSCRITOS
					SELECT TO_CHAR(i2.dt_inclusao,'YYYY/MM') AS ano_mes,
						   COUNT(i2.cpf) AS qt_inscrito
					  FROM senge_previdencia.interessado i2
					 WHERE i2.dt_exclusao IS NULL
					   AND i2.cd_situacao = 3
					   AND TO_CHAR(i2.dt_inclusao,'YYYY') = '".intval($args['nr_ano'])."'
					 GROUP BY ano_mes
					) it ON it.ano_mes = r.ano_mes

					LEFT JOIN 
					(
					-- PARTICIPANTES
					SELECT TO_CHAR(i3.dt_inclusao,'YYYY/MM') AS ano_mes,
						   COUNT(i3.cpf) AS qt_participante
					  FROM senge_previdencia.interessado i3
					  JOIN public.participantes p
						ON p.cd_empresa = 8
					   AND p.cd_plano > 0
					   AND funcoes.format_cpf(p.cpf_mf::bigint) = i3.cpf	
					  JOIN public.titulares t
						ON t.cd_empresa            = p.cd_empresa
					   AND t.cd_registro_empregado = p.cd_registro_empregado
					   AND t.seq_dependencia       = p.seq_dependencia				   
					 WHERE i3.dt_exclusao IS NULL
					   AND TO_CHAR(i3.dt_inclusao,'YYYY') = '".intval($args['nr_ano'])."'
					 GROUP BY ano_mes
					) ip ON ip.ano_mes = r.ano_mes

					LEFT JOIN 
					(
					-- CONTATOS
					SELECT TO_CHAR(i4.dt_inclusao,'YYYY/MM') AS ano_mes,
						   COUNT(*) AS qt_contato
					  FROM senge_previdencia.interessado_contato i4
					 WHERE i4.dt_exclusao IS NULL
					   AND TO_CHAR(i4.dt_inclusao,'YYYY') = '".intval($args['nr_ano'])."'
					 GROUP BY ano_mes
					) ic ON ic.ano_mes = r.ano_mes

					ORDER BY r.ano_mes
		          ";
		#echo "<PRE>".$qr_sql."</PRE>";
				  
		$result = $this->db->query($qr_sql);
	}
	
	function relatorio_agenda(&$result, $args=array())
	{
		$qr_sql = "
					SELECT r.ano_mes, 
						   COALESCE(i.qt_interessado,0) AS qt_interessado,
						   COALESCE(ic.qt_agenda,0) AS qt_agenda,
						   COALESCE(it.qt_inscrito,0) AS qt_inscrito,
						   COALESCE(ip.qt_participante,0) AS qt_participante
					  FROM 
					(
					SELECT TRIM(TO_CHAR(ano, 'FM0000')) || '/' || TRIM(TO_CHAR(mes, 'FM00')) AS ano_mes
					  FROM generate_series(".intval($args['nr_ano']).", ".intval($args['nr_ano']).") AS ano, 
						   generate_series(1,12) AS mes 
					 ORDER BY ano_mes				   
					) AS r

					LEFT JOIN 
					(
					-- INTERESSADOS
					SELECT TO_CHAR(i1.dt_inclusao,'YYYY/MM') AS ano_mes,
						   COUNT(i1.cpf) AS qt_interessado
					  FROM senge_previdencia.interessado i1
					 WHERE i1.dt_exclusao IS NULL
					   AND TO_CHAR(i1.dt_inclusao,'YYYY') = '".intval($args['nr_ano'])."'
					 GROUP BY ano_mes
					) i ON i.ano_mes = r.ano_mes

					LEFT JOIN 
					(
					-- INSCRITOS
					SELECT TO_CHAR(i2.dt_inclusao,'YYYY/MM') AS ano_mes,
						   COUNT(i2.cpf) AS qt_inscrito
					  FROM senge_previdencia.interessado i2
					 WHERE i2.dt_exclusao IS NULL
					   AND i2.cd_situacao = 3
					   AND TO_CHAR(i2.dt_inclusao,'YYYY') = '".intval($args['nr_ano'])."'
					 GROUP BY ano_mes
					) it ON it.ano_mes = r.ano_mes

					LEFT JOIN 
					(
					-- PARTICIPANTES
					SELECT TO_CHAR(i3.dt_inclusao,'YYYY/MM') AS ano_mes,
						   COUNT(i3.cpf) AS qt_participante
					  FROM senge_previdencia.interessado i3
					  JOIN public.participantes p
						ON p.cd_empresa = 8
					   AND p.cd_plano > 0
					   AND funcoes.format_cpf(p.cpf_mf::bigint) = i3.cpf	
					  JOIN public.titulares t
						ON t.cd_empresa            = p.cd_empresa
					   AND t.cd_registro_empregado = p.cd_registro_empregado
					   AND t.seq_dependencia       = p.seq_dependencia				   
					 WHERE i3.dt_exclusao IS NULL
					   AND TO_CHAR(i3.dt_inclusao,'YYYY') = '".intval($args['nr_ano'])."'
					 GROUP BY ano_mes
					) ip ON ip.ano_mes = r.ano_mes

					LEFT JOIN 
					(
					-- AGENDAMENTO
					SELECT TO_CHAR(i4.dt_inclusao,'YYYY/MM') AS ano_mes,
						   COUNT(*) AS qt_agenda
					  FROM senge_previdencia.interessado_agenda i4
					 WHERE i4.dt_exclusao IS NULL
					   AND TO_CHAR(i4.dt_inclusao,'YYYY') = '".intval($args['nr_ano'])."'
					 GROUP BY ano_mes
					) ic ON ic.ano_mes = r.ano_mes

					ORDER BY r.ano_mes
		          ";
		#echo "<PRE>".$qr_sql."</PRE>";
				  
		$result = $this->db->query($qr_sql);
	}	
}
?>