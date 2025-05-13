<?php
class extrato_envio_model extends Model 
{
    function __construct()
    {
        parent::Model();
    }
	
    function cadastroListar(&$result, $args=array())
    {
        $qr_sql = "
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
			    ".(((array_key_exists("fl_email", $args)) and (trim($args['fl_email']) == "S")) ? "AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')" : "")."
			    ".(((array_key_exists("fl_email", $args)) and (trim($args['fl_email']) == "N")) ? "AND p.email IS NULL AND p.email_profissional IS NULL" : "")."
			   AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia, p.cd_plano) IN 
			   			(SELECT DISTINCT ep.cd_empresa, ep.cd_registro_empregado, ep.seq_dependencia, ep.cd_plano
                           FROM public.extrato_participantes ep
                          WHERE ep.cd_empresa  = ".intval($args['cd_empresa'])."
                            AND ep.cd_plano    = ".intval($args['cd_plano'])."
                            AND ep.nro_extrato = ".intval($args['nr_extrato']).")
			   ".(((array_key_exists("cd_participante", $args)) and (is_array($args['cd_participante']))) ? " AND funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN ('".implode("','",$args['cd_participante'])."')" : "")."; ";

		#echo "<PRE>".$qr_sql."</PRE>"; exit;
        $result = $this->db->query($qr_sql);
    }	
	
    function controle(&$result, $args=array())
    {
        $qr_sql = "
			SELECT CASE WHEN SUM(CASE WHEN eec.cd_extrato_envio_controle IS NOT NULL THEN 1 ELSE 0 END) > 0 THEN 'S' ELSE 'N' END AS fl_gerado,
			       CASE WHEN SUM(CASE WHEN eec.dt_envio_email IS NOT NULL THEN 1 ELSE 0 END) > 0 THEN 'S' ELSE 'N' END AS fl_enviado,
				   TO_CHAR(MIN(eec.dt_inclusao),'DD/MM/YYYY HH24:MI:SS') AS dt_gerado,
				   TO_CHAR(MIN(eec.dt_envio_email),'DD/MM/YYYY HH24:MI:SS') AS dt_enviado
			  FROM projetos.extrato_envio_controle eec
			 WHERE eec.dt_exclusao    IS NULL
			   AND eec.cd_plano   = ".intval($args['cd_plano'])."
			   AND eec.cd_empresa = ".intval($args['cd_empresa'])." 
			   AND eec.nr_ano     = ".intval($args['nr_ano'])." 
			   AND eec.nr_mes     = ".intval($args['nr_mes']).";";

		#echo "<PRE>".$qr_sql."</PRE>";
        $result = $this->db->query($qr_sql);
    }	
	
    function controleListar(&$result, $args=array())
    {
        $qr_sql = "
			SELECT p.cd_plano, 
				   p.cd_empresa, 
				   p.cd_registro_empregado, 
				   p.seq_dependencia, 
				   p.nome, 
				   p.email, 
				   p.email_profissional,
				   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
				   TO_CHAR(eec.dt_envio,'DD/MM/YYYY') AS dt_envio
			  FROM projetos.extrato_envio_controle eec
			  JOIN public.participantes p
			    ON p.cd_empresa            = eec.cd_empresa
			   AND p.cd_registro_empregado = eec.cd_registro_empregado
			   AND p.seq_dependencia       = eec.seq_dependencia
			 WHERE eec.dt_exclusao    IS NULL
               AND eec.dt_envio_email IS NOT NULL
               AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
			   AND eec.cd_plano   = ".intval($args['cd_plano'])."
			   AND eec.cd_empresa = ".intval($args['cd_empresa'])." 
			   AND eec.nr_ano     = ".intval($args['nr_ano'])." 
			   AND eec.nr_mes     = ".intval($args['nr_mes'])." 
			 ORDER BY eec.dt_envio, p.nome;";

		#echo "<PRE>".$qr_sql."</PRE>";
        $result = $this->db->query($qr_sql);
    }	
	
    function controleEmail(&$result, $args=array())
    {
        $qr_sql = "
					SELECT (SELECT TO_CHAR(MIN(ea.dt_schedule_email),'DD/MM/YYYY HH24:MI:SS')
							  FROM projetos.envia_emails ea
							 WHERE ea.cd_email IN (SELECT eec.cd_email
												     FROM projetos.extrato_envio_controle eec
												    WHERE eec.dt_exclusao    IS NULL
												      AND eec.cd_plano   = ".intval($args['cd_plano'])."
													  AND eec.cd_empresa = ".intval($args['cd_empresa'])." 
													  AND eec.nr_ano     = ".intval($args['nr_ano'])." 
													  AND eec.nr_mes     = ".intval($args['nr_mes']).")) AS dt_agendado,
					       COALESCE((SELECT COUNT(*)
									   FROM projetos.envia_emails ea
									  WHERE ea.fl_retornou = 'N'
										AND ea.dt_email_enviado IS NULL
										AND ea.cd_email IN (SELECT eec.cd_email
												              FROM projetos.extrato_envio_controle eec
												              JOIN participantes p
															    ON p.cd_empresa            = eec.cd_empresa
															   AND p.cd_registro_empregado = eec.cd_registro_empregado
															   AND p.seq_dependencia       = eec.seq_dependencia
															   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
												             WHERE eec.dt_exclusao    IS NULL
												               AND eec.cd_plano   = ".intval($args['cd_plano'])."
					                                           AND eec.cd_empresa = ".intval($args['cd_empresa'])." 
					                                           AND eec.nr_ano     = ".intval($args['nr_ano'])." 
					                                           AND eec.nr_mes     = ".intval($args['nr_mes']).")),0) AS qt_aguardando,
						   COALESCE((SELECT COUNT(*)
								       FROM projetos.envia_emails er
									  WHERE er.fl_retornou = 'S'
										AND er.dt_email_enviado IS NOT NULL
										AND er.cd_email IN (SELECT eec.cd_email
												              FROM projetos.extrato_envio_controle eec
												              JOIN participantes p
															    ON p.cd_empresa            = eec.cd_empresa
															   AND p.cd_registro_empregado = eec.cd_registro_empregado
															   AND p.seq_dependencia       = eec.seq_dependencia
															   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
												             WHERE eec.dt_exclusao    IS NULL
												               AND eec.cd_plano   = ".intval($args['cd_plano'])."
					                                           AND eec.cd_empresa = ".intval($args['cd_empresa'])." 
					                                           AND eec.nr_ano     = ".intval($args['nr_ano'])." 
					                                           AND eec.nr_mes     = ".intval($args['nr_mes']).")),0) AS qt_enviado_nao,
						   COALESCE((SELECT COUNT(*)
									   FROM projetos.envia_emails ee
									  WHERE ee.fl_retornou = 'N'
										AND ee.dt_email_enviado IS NOT NULL
										AND ee.cd_email IN (SELECT eec.cd_email
												              FROM projetos.extrato_envio_controle eec
												              JOIN participantes p
															    ON p.cd_empresa            = eec.cd_empresa
															   AND p.cd_registro_empregado = eec.cd_registro_empregado
															   AND p.seq_dependencia       = eec.seq_dependencia
															   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
												             WHERE eec.dt_exclusao    IS NULL
												               AND eec.cd_plano   = ".intval($args['cd_plano'])."
					                                           AND eec.cd_empresa = ".intval($args['cd_empresa'])." 
					                                           AND eec.nr_ano     = ".intval($args['nr_ano'])." 
					                                           AND eec.nr_mes     = ".intval($args['nr_mes']).")),0) AS qt_enviado;";
		#echo "<PRE>".$qr_sql."</PRE>";exit;
        $result = $this->db->query($qr_sql);
    }	
	
    function controleInsere(&$result, $args=array())
    {
        $this->cadastroListar($result, $args);
		$ar_lista = $result->result_array();

		$qr_sql = "";
		foreach($ar_lista as  $item)
		{
			$qr_sql.= "
						INSERT INTO projetos.extrato_envio_controle
						     (
								cd_plano, 
								cd_empresa, 
								cd_registro_empregado,
								seq_dependencia, 
								nr_ano, 
								nr_mes, 
								nr_extrato, 
								dt_envio, 
								cd_usuario_inclusao
							 )
						VALUES
						     (
								".(trim($item["cd_plano"])              != "" ? intval($item["cd_plano"]) : " DEFAULT").", 
								".(trim($item["cd_empresa"])            != "" ? intval($item["cd_empresa"]) : " DEFAULT").", 
								".(trim($item["cd_registro_empregado"]) != "" ? intval($item["cd_registro_empregado"]) : " DEFAULT").", 
								".(trim($item["seq_dependencia"])       != "" ? intval($item["seq_dependencia"]) : " DEFAULT").", 
								".(trim($args["nr_ano"])                != "" ? intval($args["nr_ano"]) : " DEFAULT").", 
								".(trim($args["nr_mes"])                != "" ? intval($args["nr_mes"]) : " DEFAULT").", 
								".(trim($args["nr_extrato"])            != "" ? intval($args["nr_extrato"]) : " DEFAULT").", 
								".(trim($args["dt_envio"])              != "" ? "TO_DATE('".trim($args["dt_envio"])."','DD/MM/YYYY')" : " DEFAULT").", 
								".(trim($args["cd_usuario"])            != "" ? intval($args["cd_usuario"]) : " DEFAULT")."
							 );						
					  ";
		}
		
		if(trim($qr_sql) != "")
		{
			$qr_sql.= " 
						SELECT rotinas.email_extrato_participante(".intval($item["cd_plano"]).", ".intval($item["cd_empresa"]).", ".intval($args["nr_mes"]).", ".intval($args["nr_ano"]).", TO_DATE('".trim($args["dt_envio"])."','DD/MM/YYYY'), ".intval($args["cd_usuario"])."); 
			          ";
		}
		
		#echo "<PRE>".$qr_sql."</PRE>";exit;
        $result = $this->db->query($qr_sql);
    }	
}
?>