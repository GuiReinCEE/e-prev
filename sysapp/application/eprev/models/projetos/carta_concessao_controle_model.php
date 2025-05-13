<?php
class carta_concessao_controle_model extends Model
{
	function __construct()
    {
        parent::Model();
    }
	
	function listar(&$result, $args=array())
    {
		$qr_sql = "
					SELECT pp.sigla AS ds_empresa,
						   p.cd_empresa,
						   p.cd_registro_empregado,
						   p.seq_dependencia,
						   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
						   p.nome,
						   p.email,
						   p.email_profissional,
						   TO_CHAR(d.dt_documento,'DD/MM/YYYY') AS dt_documento,
						   TO_CHAR(ccc.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_gerado,
						   TO_CHAR(ccc.dt_envio_email,'DD/MM/YYYY HH24:MI:SS') AS dt_envio_email,
						   CASE WHEN ((COALESCE(p.email,'') <> '' OR COALESCE(p.email_profissional,'') <> '') AND ccc.dt_envio_email IS NULL) THEN 'S' ELSE 'N' END AS fl_enviar,						   
						   CASE WHEN ccc.dt_envio_email IS NOT NULL THEN 'Enviado'
						        WHEN (COALESCE(p.email,'') = '' AND COALESCE(p.email_profissional,'') = '' AND ccc.dt_envio_email IS NULL) THEN 'Não possui email'
						        WHEN ((COALESCE(p.email,'') <> '' OR COALESCE(p.email_profissional,'') <> '') AND ccc.dt_inclusao IS NOT NULL AND ccc.dt_envio_email IS NULL) THEN 'Em processo de envio'
						        WHEN ((COALESCE(p.email,'') <> '' OR COALESCE(p.email_profissional,'') <> '') AND ccc.dt_envio_email IS NULL) THEN 'Aguardando envio'
								ELSE 'ERRO'
						   END AS status,
						   CASE WHEN ccc.dt_envio_email IS NOT NULL THEN 'color: gray; font-weight: bold;'
						        WHEN (COALESCE(p.email,'') = '' AND COALESCE(p.email_profissional,'') = '' AND ccc.dt_envio_email IS NULL) THEN 'color: red; font-weight: bold;'
						        WHEN ((COALESCE(p.email,'') <> '' OR COALESCE(p.email_profissional,'') <> '') AND ccc.dt_inclusao IS NOT NULL AND ccc.dt_envio_email IS NULL) THEN 'color: blue; font-weight: bold;'
						        WHEN ((COALESCE(p.email,'') <> '' OR COALESCE(p.email_profissional,'') <> '') AND ccc.dt_envio_email IS NULL) THEN 'color: green; font-weight: bold;'
								ELSE ''
						   END AS status_cor						   
					  FROM public.documentos d
					  JOIN public.participantes p
						ON p.cd_empresa            = d.cd_empresa
					   AND p.cd_registro_empregado = d.cd_registro_empregado
					   AND p.seq_dependencia       = d.seq_dependencia
					  JOIN public.patrocinadoras pp
						ON pp.cd_empresa = p.cd_empresa
					  LEFT JOIN projetos.carta_concessao_controle ccc 
						ON ccc.cd_empresa            = d.cd_empresa
					   AND ccc.cd_registro_empregado = d.cd_registro_empregado
					   AND ccc.seq_dependencia       = d.seq_dependencia
					   AND ccc.dt_exclusao IS NULL
					 WHERE d.cd_tipo_doc = 364 -- NOVA CARTA DE CONCESSAO
					   AND (p.cd_plano > 0 OR projetos.participante_tipo(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = 'PENS')
					   AND p.dt_obito IS NULL
					   AND d.dt_documento BETWEEN TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_final']."','DD/MM/YYYY')
					   ".(trim($args['fl_enviado']) == "S" ? "AND ccc.dt_envio_email IS NOT NULL" : "")."
					   ".(trim($args['fl_enviado']) == "N" ? "AND ccc.dt_envio_email IS NULL" : "")."
					   ".(trim($args['fl_email']) == "S" ? "AND ((COALESCE(p.email,'') <> '' OR COALESCE(p.email_profissional,'') <> ''))" : "")."
					   ".(trim($args['fl_email']) == "N" ? "AND ((COALESCE(p.email,'') = '' AND COALESCE(p.email_profissional,'') = ''))" : "")."
					 ORDER BY ds_empresa, 
							  p.nome					
	              ";
			
		$result = $this->db->query($qr_sql);
		#echo "<PRE>".$qr_sql."</PRE>";
	}	
	
	function enviar(&$result, $args=array())
    {
		$qr_sql = "
					INSERT INTO projetos.carta_concessao_controle
					     (
							cd_empresa, 
							cd_registro_empregado,
							seq_dependencia, 
							cd_usuario_inclusao
						  )
						  
					SELECT p.cd_empresa,
						   p.cd_registro_empregado,
						   p.seq_dependencia,
						   ".intval($args['cd_usuario'])."
					  FROM public.participantes p
					 WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN (".$args['part_selecionado'].");
					
					SELECT rotinas.email_carta_concessao(".intval($args['cd_usuario']).");
	              ";
			
		$result = $this->db->query($qr_sql);	
	
	}
	
	function emails_listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ee.cd_email, 
				   ee.cd_empresa,
				   ee.cd_registro_empregado,
				   ee.seq_dependencia,
				   p.nome,
				   ee.para,
				   ee.cc,
				   ee.cco,
				   TO_CHAR(ee.dt_envio, 'DD/MM/YYYY HH24:MI') AS dt_email, 
				   TO_CHAR(ee.dt_email_enviado, 'DD/MM/YYYY HH24:MI') AS dt_envio, 
				   ee.assunto,
				   ee.fl_retornou AS fl_retorno
			  FROM projetos.envia_emails ee
			  JOIN public.participantes p
				ON p.cd_empresa            = ee.cd_empresa
			   AND p.cd_registro_empregado = ee.cd_registro_empregado
			   AND p.seq_dependencia       = ee.seq_dependencia
             WHERE cd_evento = 141
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