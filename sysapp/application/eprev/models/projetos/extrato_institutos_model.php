<?php
class Extrato_institutos_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

	public function listar($args = array()) 
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
				   TO_CHAR(id.dt_ar_extrato,'DD/MM/YYYY') AS dt_recebido_extrato,
				   (SELECT TO_CHAR(d.dt_documento,'DD/MM/YYYY')
				      FROM public.documentos d
				     WHERE d.cd_empresa            = id.cd_empresa
					   AND d.cd_registro_empregado = id.cd_registro_empregado
					   AND d.seq_dependencia       = id.seq_dependencia
					   AND d.cd_tipo_doc           = 269
					 ORDER BY dt_digitalizacao ASC
					 LIMIT 1) AS dt_documento,
				   TO_CHAR(ccc.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_gerado,
				   (CASE WHEN (ccc.fl_envio = 'C') THEN TO_CHAR(ccc.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS')
				         WHEN (ccc.fl_envio = 'M') THEN TO_CHAR(ccc.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS')
				         ELSE TO_CHAR(ccc.dt_envio_email, 'DD/MM/YYYY HH24:MI:SS')
				   END) AS dt_envio_email,
				   TO_CHAR(id.dt_emissao_extrato, 'DD/MM/YYYY HH24:MI:SS') AS dt_emissao_extrato,
				   TO_CHAR(id.dt_limite_extrato, 'DD/MM/YYYY') AS dt_limite_extrato,
				   (CASE WHEN (ccc.dt_envio_email IS NULL 
                         AND ccc.dt_inclusao IS NULL AND ccc.dt_envio_email IS NULL       
				         AND 
				   			(SELECT d.dt_digitalizacao
						       FROM public.documentos d
						      WHERE d.cd_empresa            = id.cd_empresa
							    AND d.cd_registro_empregado = id.cd_registro_empregado
							    AND d.seq_dependencia       = id.seq_dependencia
							    AND d.cd_tipo_doc           = 269
							    AND d.dt_documento IS NOT NULL
							    AND d.id_liquid    IS NOT NULL
							  ORDER BY d.dt_digitalizacao ASC
							  LIMIT 1) IS NOT NULL

				       ) THEN 'S' 
				         ELSE 'N'
				   END) AS fl_enviar,  
				   (CASE WHEN (
				   			 SELECT d.dt_digitalizacao
						       FROM public.documentos d
						      WHERE d.cd_empresa            = id.cd_empresa
							    AND d.cd_registro_empregado = id.cd_registro_empregado
							    AND d.seq_dependencia       = id.seq_dependencia
							    AND d.cd_tipo_doc           = 269
							    AND d.dt_documento IS NOT NULL
							    AND d.id_liquid    IS NOT NULL
							  ORDER BY d.dt_digitalizacao ASC
							  LIMIT 1) IS NOT NULL
				         THEN 'S' 
				         ELSE 'N'
				   END) AS fl_documento_liquid,    
				   (CASE WHEN (ccc.dt_envio_email IS NOT NULL AND ccc.fl_envio = 'E') THEN 'Enviado'
				         WHEN (ccc.fl_envio = 'C') THEN 'Enviado Por Correio'
				         WHEN (ccc.fl_envio = 'M') THEN 'Enviado Manualmente'
						 WHEN (COALESCE(p.email,'') = '' AND COALESCE(p.email_profissional,'') = '' AND ccc.dt_envio_email IS NULL) THEN 'Não possui email'
						 WHEN (SELECT d.dt_digitalizacao
						        FROM public.documentos d
						       WHERE d.cd_empresa            = id.cd_empresa
							     AND d.cd_registro_empregado = id.cd_registro_empregado
							     AND d.seq_dependencia       = id.seq_dependencia
							     AND d.cd_tipo_doc           = 269
							     AND d.dt_documento IS NOT NULL
							     AND d.id_liquid    IS NOT NULL
							   ORDER BY d.dt_digitalizacao ASC
							   LIMIT 1) IS NULL THEN 'Sem Documento no LIQUID'
						 WHEN ((COALESCE(p.email,'') <> '' OR COALESCE(p.email_profissional,'') <> '') AND ccc.dt_inclusao IS NOT NULL AND ccc.dt_envio_email IS NULL) THEN 'Em processo de envio'
					 	 WHEN ((COALESCE(p.email,'') <> '' OR COALESCE(p.email_profissional,'') <> '') AND ccc.dt_envio_email IS NULL) THEN 'Aguardando envio'
						 ELSE 'ERRO'
				   END) AS status,
				   (CASE WHEN ccc.dt_envio_email IS NOT NULL THEN ''
						WHEN (COALESCE(p.email,'') = '' AND COALESCE(p.email_profissional,'') = '' AND ccc.dt_envio_email IS NULL) THEN 'label-important'
						WHEN (SELECT d.dt_digitalizacao
						        FROM public.documentos d
						       WHERE d.cd_empresa            = id.cd_empresa
							     AND d.cd_registro_empregado = id.cd_registro_empregado
							     AND d.seq_dependencia       = id.seq_dependencia
							     AND d.cd_tipo_doc           = 269
							     AND d.dt_documento IS NOT NULL
							     AND d.id_liquid    IS NOT NULL
							   ORDER BY d.dt_digitalizacao ASC
							   LIMIT 1) IS NULL THEN 'label-important'
						WHEN ((COALESCE(p.email,'') <> '' OR COALESCE(p.email_profissional,'') <> '') AND ccc.dt_inclusao IS NOT NULL AND ccc.dt_envio_email IS NULL) THEN 'label-info'
						WHEN ((COALESCE(p.email,'') <> '' OR COALESCE(p.email_profissional,'') <> '') AND ccc.dt_envio_email IS NULL) THEN 'label-success'
						ELSE ''
				   END) AS class_status,
				   oracle.fnc_retorna_opcao(id.cd_empresa::INTEGER, id.cd_registro_empregado::INTEGER, id.seq_dependencia::INTEGER, 'WEB0001', COALESCE(ccc.dt_inclusao::DATE, CURRENT_DATE)) AS fl_eletronico,
				   CASE WHEN (COALESCE((SELECT DISTINCT e.empresa_integradora
										  FROM titulares t
										  JOIN empresas_integradoras e
									        ON e.empresa_integradora = t.empresa_integradora
										   AND e.cd_empresa          = t.cd_empresa
										   AND e.empresa_integradora = 1 -- FILIAL GERAÇÃO
										   AND e.cd_empresa          = 0 -- CEEE
										 WHERE t.cd_empresa            = id.cd_empresa
										   AND t.cd_registro_empregado = id.cd_registro_empregado), 0)) = 1
				        THEN 'SIM'
				        ELSE 'NÃO'
				   END AS ds_ceeeg,
				   CASE WHEN (COALESCE((SELECT DISTINCT e.empresa_integradora
										  FROM titulares t
										  JOIN empresas_integradoras e
									        ON e.empresa_integradora = t.empresa_integradora
										   AND e.cd_empresa          = t.cd_empresa
										   AND e.empresa_integradora = 1 -- FILIAL GERAÇÃO
										   AND e.cd_empresa          = 0 -- CEEE
										 WHERE t.cd_empresa            = id.cd_empresa
										   AND t.cd_registro_empregado = id.cd_registro_empregado), 0)) = 1
				        THEN 'label-important'
				        ELSE 'label-success'
				   END AS ds_ceeeg_class_status	
			  FROM public.institutos_desligamentos id
			  JOIN public.participantes p
				ON p.cd_empresa            = id.cd_empresa
			   AND p.cd_registro_empregado = id.cd_registro_empregado
			   AND p.seq_dependencia       = id.seq_dependencia
			  JOIN public.patrocinadoras pp
				ON pp.cd_empresa = p.cd_empresa
			  LEFT JOIN projetos.extrato_institutos ccc 
				ON ccc.cd_empresa            = id.cd_empresa
			   AND ccc.cd_registro_empregado = id.cd_registro_empregado
			   AND ccc.seq_dependencia       = id.seq_dependencia
			   AND ccc.dt_exclusao           IS NULL
			    AND (CASE WHEN (ccc.fl_envio = 'C') THEN ccc.dt_inclusao
				         WHEN (ccc.fl_envio = 'M') THEN ccc.dt_inclusao
				         ELSE ccc.dt_envio_email
				   END) >= id.dt_emissao_extrato::date 
		 	 WHERE id.tipo_calculo = 1 --MOVIMENTO
		 	   AND id.dt_emissao_extrato::date >= '2017-12-18'::date
		 	   AND DATE_TRUNC('day', id.dt_emissao_extrato) BETWEEN TO_DATE('".$args['dt_emissao_extrato_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_emissao_extrato_fim']."','DD/MM/YYYY')
		 	   ".(trim($args['fl_eletronico']) != '' ? "AND oracle.fnc_retorna_opcao(id.cd_empresa::INTEGER, id.cd_registro_empregado::INTEGER, id.seq_dependencia::INTEGER, 'WEB0001', COALESCE(ccc.dt_inclusao::DATE, CURRENT_DATE)) = '".trim($args['fl_eletronico'])."'" : '')."
			   ".(trim($args['fl_recebido_extrato']) == 'S' ? "AND id.dt_ar_extrato IS NOT NULL" : '')."
			   ".(trim($args['fl_recebido_extrato']) == 'N' ? "AND id.dt_ar_extrato IS NULL" : '')."
			   ".(trim($args['fl_enviado']) == 'S' ? "AND ccc.dt_envio_email IS NOT NULL" : '')."
			   ".(trim($args['fl_enviado']) == 'N' ? "AND ccc.dt_envio_email IS NULL" : '')."
			   ".(trim($args['fl_email']) == 'S' ? "AND ((COALESCE(p.email,'') <> '' OR COALESCE(p.email_profissional,'') <> ''))" : '')."
			   ".(trim($args['fl_email']) == 'N' ? "AND ((COALESCE(p.email,'') = '' AND COALESCE(p.email_profissional,'') = ''))" : '').";";

		return $this->db->query($qr_sql)->result_array();
	}
	
	public function enviar($args = array())
    {
		$qr_sql = "
			INSERT INTO projetos.extrato_institutos
				 (
					cd_empresa, 
					cd_registro_empregado,
					seq_dependencia, 
					arquivo,
					id_liquid,
					fl_envio,
					cd_usuario_inclusao
				  )
			SELECT p.cd_empresa,
				   p.cd_registro_empregado,
				   p.seq_dependencia,
				   (SELECT d.caminho_imagem
				      FROM public.documentos d
				     WHERE d.cd_empresa            = id.cd_empresa
					   AND d.cd_registro_empregado = id.cd_registro_empregado
					   AND d.seq_dependencia       = id.seq_dependencia
					   AND d.cd_tipo_doc           = 269
					 ORDER BY dt_digitalizacao ASC
					 LIMIT 1),
				   (SELECT d.id_liquid
				      FROM public.documentos d
				     WHERE d.cd_empresa            = id.cd_empresa
					   AND d.cd_registro_empregado = id.cd_registro_empregado
					   AND d.seq_dependencia       = id.seq_dependencia
					   AND d.cd_tipo_doc           = 269
					 ORDER BY dt_digitalizacao ASC
					 LIMIT 1),
				   '".trim($args['fl_envio'])."',
				   ".intval($args['cd_usuario'])."
			  FROM public.institutos_desligamentos id
			  JOIN public.participantes p
			    ON p.cd_empresa            = id.cd_empresa
			   AND p.cd_registro_empregado = id.cd_registro_empregado
			   AND p.seq_dependencia       = id.seq_dependencia
			 WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN (".$args['arr_re_cripto'].")
			   AND DATE_TRUNC('day', id.dt_emissao_extrato) BETWEEN TO_DATE('".$args['dt_emissao_extrato_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_emissao_extrato_fim']."','DD/MM/YYYY');";

		if(trim($args['fl_envio']) == 'E')
		{
			$qr_sql .= "SELECT rotinas.email_extrato_institutos(".intval($args['cd_usuario']).");";
		}

		$this->db->query($qr_sql);	
	}
	
	public function emails_listar($args = array())
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
             WHERE cd_evento = 165	
               AND DATE_TRUNC('day', ee.dt_envio) BETWEEN TO_DATE('".trim($args['dt_email_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_email_fim'])."','DD/MM/YYYY')	
               ".(trim($args['fl_retornou']) != '' ? "AND  ee.fl_retornou = '".trim($args['fl_retornou'])."'" : "")."		
               ".(trim($args['cd_empresa']) != '' ? "AND  ee.cd_empresa = ".$args['cd_empresa'] : "")."		
               ".(trim($args['cd_registro_empregado']) != '' ? "AND  ee.cd_registro_empregado = ".$args['cd_registro_empregado'] : "")."		
               ".(trim($args['seq_dependencia']) != '' ? "AND  ee.seq_dependencia = ".$args['seq_dependencia'] : "")."				   
			 ORDER BY COALESCE(ee.dt_email_enviado,ee.dt_envio) DESC, 
					  ee.assunto ASC;";
					  
		return $this->db->query($qr_sql)->result_array();
   }

   public function atualizar_documento($cd_empresa, $cd_registro_empregado, $seq_dependencia)
   {
   		$qr_sql = "
			SELECT sincroniza.atualiza_tabelas_oracle('public',tabela,'WHERE cd_tipo_doc = 269 AND cd_empresa = ".intval($cd_empresa)." AND cd_registro_empregado = ".intval($cd_registro_empregado)." AND seq_dependencia = ".intval($seq_dependencia)."',NULL,NULL,truncar,'N')
			  FROM projetos.tabelas_atualizar
		 	 WHERE tabela = 'DOCUMENTOS'";

		$this->db->query($qr_sql);
   }
}
?>