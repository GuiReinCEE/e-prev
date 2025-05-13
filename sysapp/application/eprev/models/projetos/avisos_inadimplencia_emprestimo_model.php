<?php
class avisos_inadimplencia_emprestimo_model extends Model
{
	function __construct()
    {
        parent::Model();
    }
	
	function listar(&$result, $args=array())
    {
		$qr_sql = "
					SELECT TO_CHAR(a.dt_aviso,'DD/MM/YYYY') AS dt_aviso,
					       p.cd_empresa,
					       p.cd_registro_empregado,
					       p.seq_dependencia,
						   p.nome,
						   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
						   p.email,
						   p.email_profissional,
						   TO_CHAR(aie.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_gerado,
						   TO_CHAR(aie.dt_envio_email,'DD/MM/YYYY HH24:MI:SS') AS dt_envio_email,
						   CASE WHEN ((COALESCE(p.email,'') <> '' OR COALESCE(p.email_profissional,'') <> '') AND aie.dt_inclusao IS NULL) THEN 'S' ELSE 'N' END AS fl_enviar,						   
						   CASE WHEN aie.dt_envio_email IS NOT NULL THEN 'Enviado'
						        WHEN (COALESCE(p.email,'') = '' AND COALESCE(p.email_profissional,'') = '' AND aie.dt_envio_email IS NULL) THEN 'Não possui email'
						        WHEN ((COALESCE(p.email,'') <> '' OR COALESCE(p.email_profissional,'') <> '') AND aie.dt_inclusao IS NOT NULL AND aie.dt_envio_email IS NULL) THEN 'Em processo de envio'
						        WHEN ((COALESCE(p.email,'') <> '' OR COALESCE(p.email_profissional,'') <> '') AND aie.dt_envio_email IS NULL) THEN 'Aguardando envio'
								ELSE 'ERRO'
						   END AS status,
						   CASE WHEN aie.dt_envio_email IS NOT NULL THEN 'color: gray; font-weight: bold;'
						        WHEN (COALESCE(p.email,'') = '' AND COALESCE(p.email_profissional,'') = '' AND aie.dt_envio_email IS NULL) THEN 'color: red; font-weight: bold;'
						        WHEN ((COALESCE(p.email,'') <> '' OR COALESCE(p.email_profissional,'') <> '') AND aie.dt_inclusao IS NOT NULL AND aie.dt_envio_email IS NULL) THEN 'color: blue; font-weight: bold;'
						        WHEN ((COALESCE(p.email,'') <> '' OR COALESCE(p.email_profissional,'') <> '') AND aie.dt_envio_email IS NULL) THEN 'color: green; font-weight: bold;'
								ELSE ''
						   END AS status_cor						   
					  FROM (SELECT dt_aviso, 
								   cd_empresa, 
								   cd_registro_empregado, 
								   seq_dependencia 
							  FROM oracle.avisos_inadimplencia_emprestimo(TO_DATE('01/".$args['nr_mes']."/".$args['nr_ano']."','DD/MM/YYYY')) AS (dt_aviso TIMESTAMP, cd_empresa NUMERIC, cd_registro_empregado NUMERIC, seq_dependencia NUMERIC)) a
					  JOIN public.participantes p
						ON p.cd_empresa            = a.cd_empresa
					   AND p.cd_registro_empregado = a.cd_registro_empregado
					   AND p.seq_dependencia       = a.seq_dependencia	
					  LEFT JOIN projetos.avisos_inadimplencia_emprestimo aie
					    ON TO_CHAR(aie.dt_aviso,'MM-YYYY') = TO_CHAR(a.dt_aviso,'MM-YYYY')
					   AND aie.cd_empresa             = a.cd_empresa
					   AND aie.cd_registro_empregado  = a.cd_registro_empregado
					   AND aie.seq_dependencia        = a.seq_dependencia						  
					 ORDER BY p.nome
	              ";
			
		$result = $this->db->query($qr_sql);
		#echo "<PRE>".$qr_sql."</PRE>";
	}	
	
	function enviar(&$result, $args=array())
    {
		$qr_sql = "
					INSERT INTO projetos.avisos_inadimplencia_emprestimo
					     (
							cd_empresa, 
							cd_registro_empregado,
							seq_dependencia, 
							dt_aviso,
							cd_usuario_inclusao
						  )
						  
					SELECT p.cd_empresa,
						   p.cd_registro_empregado,
						   p.seq_dependencia,
						   TO_DATE('01/".$args['nr_mes']."/".$args['nr_ano']."','DD/MM/YYYY'),
						   ".intval($args['cd_usuario'])."
					  FROM public.participantes p
					 WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN (".$args['part_selecionado'].");
					 
					SELECT rotinas.email_aviso_inadimplencia_emprestimo(".intval($args['cd_usuario'])."); 
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
					 WHERE cd_evento = 148
					   AND ee.fl_retornou = '".trim($args['fl_retornou'])."'	
					   AND DATE_TRUNC('day', ee.dt_envio) BETWEEN TO_DATE('".trim($args['dt_email_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_email_fim'])."','DD/MM/YYYY')	
					   ".(trim($args['cd_empresa']) != '' ? "AND  ee.cd_empresa = ".$args['cd_empresa'] : "")."		
					   ".(trim($args['cd_registro_empregado']) != '' ? "AND  ee.cd_registro_empregado = ".$args['cd_registro_empregado'] : "")."		
					   ".(trim($args['seq_dependencia']) != '' ? "AND  ee.seq_dependencia = ".$args['seq_dependencia'] : "")."				   
					 ORDER BY COALESCE(ee.dt_email_enviado,ee.dt_envio) DESC, 
							  ee.assunto ASC
				  ";
		#echo "<PRE>".$qr_sql."</PRE>";
		$result = $this->db->query($qr_sql);
	}	
}
?>