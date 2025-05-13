<?php
class contracheque_controle_model extends Model
{
	function __construct()
    {
        parent::Model();
    }
	
	function verifica_liberacao(&$result, $args=array())
    {
		$qr_sql = "
					SELECT CASE WHEN pf.dt_consulta_contracheque <= CURRENT_DATE
								THEN 'S'
								ELSE 'N'
						   END AS fl_libera,
						   TO_CHAR(pf.dt_consulta_contracheque,'DD/MM/YYYY') AS dt_libera
					  FROM public.periodos_folha pf
					 WHERE pf.mes = TO_CHAR(CURRENT_DATE,'MM')::INTEGER
					   AND pf.ano = TO_CHAR(CURRENT_DATE, 'YYYY')::INTEGER
					   AND pf.dt_consulta_contracheque IS NOT NULL
					   AND pf.tipo = '".trim($args["tp_contracheque"])."'
					   AND pf.tipo IN ('M','B')
					   AND CASE WHEN TO_CHAR(CURRENT_DATE,'MM')::INTEGER = 12 
								AND CURRENT_DATE >= (SELECT pf1.dt_consulta_contracheque 
													   FROM public.periodos_folha pf1
													  WHERE pf1.tipo = 'B'
														AND pf1.mes  = pf.mes
														AND pf1.ano  = pf.ano
														AND pf1.tifo_tipo_folha = pf.tifo_tipo_folha)
								AND CURRENT_DATE < (SELECT pf1.dt_consulta_contracheque 
													  FROM public.periodos_folha pf1
													 WHERE pf1.tipo = 'M'
													   AND pf1.mes  = pf.mes
													   AND pf1.ano  = pf.ano
													   AND pf1.tifo_tipo_folha = pf.tifo_tipo_folha)
								THEN pf.tipo = 'B'
								ELSE pf.tipo = 'M'
						   END						   
					ORDER BY pf.dt_consulta_contracheque DESC
					LIMIT 1;					
                  ";
			
		$result = $this->db->query($qr_sql);
		
		#echo "<PRE>".$qr_sql."</PRE>";
	}	
	
	function resumo_folha(&$result, $args=array())
    {
		$qr_sql = "
			SELECT c.tipo_folha, 
				   tf.descricao_folha,
				   SUM(CASE WHEN (COALESCE(p.email,'') LIKE '%@%' OR COALESCE(p.email_profissional,'') LIKE '%@%') AND COALESCE(p.motivo_devolucao_correio,0) <> 20 THEN 1 ELSE 0 END) AS qt_email,
				   COUNT(*) AS qt_total
			  FROM (SELECT cd_empresa, cd_registro_empregado, seq_dependencia, tipo_folha 
					  FROM oracle.contracheque_participante(TO_DATE('".$args['dt_pagamento']."','DD/MM/YYYY')) 
						AS (cd_empresa NUMERIC, cd_registro_empregado NUMERIC, seq_dependencia NUMERIC, tipo_folha NUMERIC)) c
			  JOIN public.participantes p
				ON p.cd_empresa            = c.cd_empresa
			   AND p.cd_registro_empregado = c.cd_registro_empregado
			   AND p.seq_dependencia       = c.seq_dependencia
			  JOIN public.tipo_folhas tf
				ON tf.tipo_folha = c.tipo_folha
			 GROUP BY c.tipo_folha, 
					  tf.descricao_folha
			 ORDER BY c.tipo_folha;";
			
		$result = $this->db->query($qr_sql);
		
		#echo "<PRE>".$qr_sql."</PRE>";
	}
	
	function resumo_controle(&$result, $args=array())
    {
		$qr_sql = "
			SELECT c.tipo_folha, 
				   tf.descricao_folha,
				   c.qt_email,
				   c.qt_total
			  FROM projetos.contracheque_controle_resumo c
			  JOIN public.tipo_folhas tf
				ON tf.tipo_folha = c.tipo_folha
			 WHERE c.dt_pagamento = TO_DATE('".$args['dt_pagamento']."','DD/MM/YYYY')
			 ORDER BY c.tipo_folha;";
			
		$result = $this->db->query($qr_sql);
		
		#echo "<PRE>".$qr_sql."</PRE>";
	}	
	
	function get_data_envio(&$result, $args=array())
	{
		$qr_sql = "
			SELECT DISTINCT TO_CHAR(c.dt_envio_email,'DD/MM/YYYY: HH24:MI:SS') AS dt_envio_email,
				   uc.nome AS ds_envio_email
			  FROM projetos.contracheque_controle c
			  LEFT JOIN projetos.usuarios_controledi uc
				ON uc.codigo = c.cd_usuario_envio_email
			 WHERE c.dt_pagamento   = TO_DATE('".$args['dt_pagamento']."','DD/MM/YYYY')
			   AND c.dt_envio_email IS NOT NULL;";
		$result = $this->db->query($qr_sql);
	}	
	
	function verifica_envio(&$result, $args=array())
	{
		$qr_sql = "
			SELECT (CASE WHEN COUNT(*) > 0 THEN 'S' ELSE 'N' END) AS fl_envio
			  FROM projetos.contracheque_controle c
			 WHERE c.dt_pagamento   = TO_DATE('".$args['dt_pagamento']."','DD/MM/YYYY')
			   AND c.dt_envio_email IS NOT NULL;";
		$result = $this->db->query($qr_sql);
	}
	
	function gerar(&$result, $args=array())
    {
		$qr_sql = "
			--RESUMO
			INSERT INTO projetos.contracheque_controle_resumo
				 (
					dt_pagamento, 
					tipo_folha, 
					qt_total, 
					qt_email,
					cd_usuario_inclusao
				 )

			SELECT TO_DATE('".$args['dt_pagamento']."','DD/MM/YYYY'),
				   c.tipo_folha, 
				   COUNT(*) AS qt_total,
				   SUM(CASE WHEN (COALESCE(p.email,'') LIKE '%@%' OR COALESCE(p.email_profissional,'') LIKE '%@%') AND COALESCE(p.motivo_devolucao_correio,0) <> 20 THEN 1 ELSE 0 END) AS qt_email,
				   ".intval($args['cd_usuario'])."
			  FROM (SELECT cd_empresa, cd_registro_empregado, seq_dependencia, tipo_folha 
					  FROM oracle.contracheque_participante(TO_DATE('".$args['dt_pagamento']."','DD/MM/YYYY')) 
						AS (cd_empresa NUMERIC, cd_registro_empregado NUMERIC, seq_dependencia NUMERIC, tipo_folha NUMERIC)) c
			  JOIN public.participantes p
				ON p.cd_empresa            = c.cd_empresa
			   AND p.cd_registro_empregado = c.cd_registro_empregado
			   AND p.seq_dependencia       = c.seq_dependencia
			 GROUP BY c.tipo_folha
			 ORDER BY c.tipo_folha;
			
			--PARTICIPANTES
			INSERT INTO projetos.contracheque_controle
				 (
					dt_pagamento, 
					cd_empresa, 
					cd_registro_empregado, 
					seq_dependencia, 
					tipo_folha, 
					cd_usuario_inclusao
				 )		

			SELECT TO_DATE('".$args['dt_pagamento']."','DD/MM/YYYY'),
				   c.cd_empresa, 
				   c.cd_registro_empregado, 
				   c.seq_dependencia, 
				   c.tipo_folha,
				   ".intval($args['cd_usuario'])."
			  FROM (SELECT cd_empresa, cd_registro_empregado, seq_dependencia, tipo_folha 
					  FROM oracle.contracheque_participante(TO_DATE('".$args['dt_pagamento']."','DD/MM/YYYY')) 
						AS (cd_empresa NUMERIC, cd_registro_empregado NUMERIC, seq_dependencia NUMERIC, tipo_folha NUMERIC)) c
			  JOIN public.participantes p
				ON p.cd_empresa            = c.cd_empresa
			   AND p.cd_registro_empregado = c.cd_registro_empregado
			   AND p.seq_dependencia       = c.seq_dependencia
			 WHERE (COALESCE(p.email,'') LIKE '%@%' OR COALESCE(p.email_profissional,'') LIKE '%@%')
			   AND COALESCE(p.motivo_devolucao_correio,0) <> 20;";
			
		$result = $this->db->query($qr_sql);
		
		#echo "<PRE>".$qr_sql."</PRE>";
	}

	function enviar_email(&$result, $args=array())
	{
		$qr_sql = "
			SELECT rotinas.email_contracheque_participante(TO_DATE('".$args['dt_pagamento']."','DD/MM/YYYY'), ".intval($args['cd_usuario']).");";
		$result = $this->db->query($qr_sql);
	}
	
	function emails_listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ee.cd_email, 
				   ee.cd_empresa,
				   ee.cd_registro_empregado,
				   ee.seq_dependencia,
				   ee.para,
				   ee.cc,
				   ee.cco,
				   TO_CHAR(ee.dt_envio, 'DD/MM/YYYY HH24:MI') AS dt_email, 
				   TO_CHAR(ee.dt_email_enviado, 'DD/MM/YYYY HH24:MI') AS dt_envio, 
				   ee.assunto,
				   ee.fl_retornou AS fl_retorno
			  FROM projetos.envia_emails ee			
             WHERE cd_evento = 136
			   AND ee.fl_retornou = '".trim($args['fl_retornou'])."'	
               AND DATE_TRUNC('day', ee.dt_envio) BETWEEN TO_DATE('".trim($args['dt_email_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_email_fim'])."','DD/MM/YYYY')	
               ".(trim($args['cd_empresa']) != '' ? "AND  ee.cd_empresa = ".$args['cd_empresa'] : "")."		
               ".(trim($args['cd_registro_empregado']) != '' ? "AND  ee.cd_registro_empregado = ".$args['cd_registro_empregado'] : "")."		
               ".(trim($args['seq_dependencia']) != '' ? "AND  ee.seq_dependencia = ".$args['seq_dependencia'] : "")."				   
			 ORDER BY COALESCE(ee.dt_email_enviado,ee.dt_envio) DESC, 
					  ee.assunto ASC;";
		#echo "<PRE>".$qr_sql."</PRE>";
		$result = $this->db->query($qr_sql);
   }
}
?>