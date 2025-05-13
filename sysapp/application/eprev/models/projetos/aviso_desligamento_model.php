<?php

class Aviso_desligamento_model extends Model
{

    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        #TELA DE CONSULTA NO ELETRO COBP0402
        $qr_sql = "
					SELECT p.cd_empresa,
						   p.cd_registro_empregado,
						   p.seq_dependencia,
						   p.nome,
						   TO_CHAR(adc.dt_controle,'DD/MM/YYYY HH24:MI:SS') AS dt_controle,
						   adc.fl_email_enviado,
						   adc.nr_ano_competencia,
						   adc.nr_mes_competencia,
						   TO_CHAR(adc.dt_exclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao
					  FROM public.titulares t
					  JOIN public.participantes p
						ON p.cd_empresa            = t.cd_empresa
					   AND p.cd_registro_empregado = t.cd_registro_empregado
					   AND p.seq_dependencia       = t.seq_dependencia
					  JOIN public.patrocinadoras pp
						ON pp.cd_empresa = p.cd_empresa
					  LEFT JOIN projetos.aviso_desligamento_controle adc
						ON adc.cd_empresa            = t.cd_empresa
					   AND adc.cd_registro_empregado = t.cd_registro_empregado
					   AND adc.seq_dependencia       = t.seq_dependencia	
					   AND adc.nr_mes_competencia    = ".intval($args['nr_mes'])."
					   AND adc.nr_ano_competencia    = ".intval($args['nr_ano'])."
					 WHERE (t.dt_aviso_desligamento IS NOT NULL OR adc.fl_email_enviado IS NOT NULL)
                       AND t.dt_desligamento_eletro IS NULL
					   AND (DATE_TRUNC('month',t.dt_aviso_desligamento)::DATE = TO_DATE('01/" . $args['nr_mes'] . "/" . $args['nr_ano'] . "','DD/MM/YYYY') OR (adc.nr_mes_competencia = ".intval($args['nr_mes'])." AND adc.nr_ano_competencia = ".intval($args['nr_ano'])."))
					   --AND pp.tipo_cliente         = 'I'
					   AND p.cd_empresa            = " . $args['cd_empresa'] . "
		          ";

        #echo "<pre>$qr_sql</pre>";	
        $result = $this->db->query($qr_sql);
        $count = $result->num_rows();
    }

    function gerar(&$result, $args=array())
    {
        $qr_sql = "
                INSERT INTO projetos.aviso_desligamento_controle
                     (
                       cd_empresa,
                       cd_registro_empregado,
                       seq_dependencia,
                       nr_mes_competencia,
                       nr_ano_competencia,
                       cd_usuario
                      )
                SELECT p.cd_empresa,
                       p.cd_registro_empregado,
                       p.seq_dependencia,
                       " . $args['nr_mes'] . "::INT,
                       " . $args['nr_ano'] . "::INT,
                       " . $args['cd_usuario'] . "::INT
                  FROM public.titulares t
                  JOIN public.participantes p
                    ON p.cd_empresa            = t.cd_empresa
                   AND p.cd_registro_empregado = t.cd_registro_empregado
                   AND p.seq_dependencia       = t.seq_dependencia
                  JOIN public.patrocinadoras pp
                    ON pp.cd_empresa = p.cd_empresa					   
                 WHERE t.dt_aviso_desligamento IS NOT NULL
                   AND t.dt_desligamento_eletro IS NULL
                   --AND pp.tipo_cliente         = 'I'
                   AND t.cd_empresa            = " . $args['cd_empresa'] . "
                   AND DATE_TRUNC('month',t.dt_aviso_desligamento)::DATE = TO_DATE('01/" . $args['nr_mes'] . "/" . $args['nr_ano'] . "','DD/MM/YYYY');
		       ";

        #echo "<pre>$qr_sql</pre>";	
        #exit;
        $this->db->query($qr_sql);
    }
	
    function excluir_aviso(&$result, $args=array())
    {
        $qr_sql = "
					UPDATE projetos.aviso_desligamento_controle
					   SET dt_exclusao         = CURRENT_TIMESTAMP,
					       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
					 WHERE cd_empresa            = ".$args['cd_empresa']."
					   AND cd_registro_empregado = ".$args['cd_registro_empregado']."
					   AND seq_dependencia       = ".$args['seq_dependencia']."
					   AND nr_mes_competencia    = ".$args['nr_mes']."
					   AND nr_ano_competencia    = ".$args['nr_ano'].";
				  ";

        #echo "<pre>$qr_sql</pre>";	
        #exit;
        $result = $this->db->query($qr_sql);
    }	

    function envia_email(&$result, $args=array())
    {
        $qr_sql = "
					SELECT rotinas.aviso_desligamento(" . $args['cd_empresa'] . " ," . $args['nr_mes'] . " , " . $args['nr_ano'] . ");
				  ";

        #echo "<pre>$qr_sql</pre>";	
        #exit;
        $this->db->query($qr_sql);
    }	
	
    function verificaGeracao(&$result, $args=array())
    {
        $qr_sql = "
					SELECT adc.cd_empresa,
						   adc.cd_registro_empregado,
						   adc.seq_dependencia,
						   adc.nr_mes_competencia,
						   adc.nr_ano_competencia
					  FROM projetos.aviso_desligamento_controle adc
					 WHERE COALESCE(adc.fl_email_enviado,'')   <> 'S'
					   AND adc.dt_exclusao        IS NULL
					   AND adc.cd_empresa         = " . $args['cd_empresa'] . "
					   AND adc.nr_mes_competencia = " . $args['nr_mes'] . "
					   AND adc.nr_ano_competencia = " . $args['nr_ano'] . "
		          ";

        #echo "<pre>$qr_sql</pre>";	
        $result = $this->db->query($qr_sql);
    }	
	
    function verificaEnvio(&$result, $args=array())
    {
        $qr_sql = "
					SELECT adc.cd_empresa,
						   adc.cd_registro_empregado,
						   adc.seq_dependencia,
						   adc.nr_mes_competencia,
						   adc.nr_ano_competencia
					  FROM projetos.aviso_desligamento_controle adc
					 WHERE COALESCE(adc.fl_email_enviado,'') = 'S'
					   AND adc.dt_exclusao        IS NULL
					   AND adc.cd_empresa         = " . $args['cd_empresa'] . "
					   AND adc.nr_mes_competencia = " . $args['nr_mes'] . "
					   AND adc.nr_ano_competencia = " . $args['nr_ano'] . "
		          ";

        #echo "<pre>$qr_sql</pre>";	
        $result = $this->db->query($qr_sql);
    }
    
    function aviso_desligamento_controler(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cc.cd_empresa, 
                   cc.cd_registro_empregado, 
                   cc.seq_dependencia, 
                   cc.nr_ano_competencia, 
                   cc.nr_mes_competencia, 
                   TO_CHAR(cc.dt_controle,'DD/MM/YYYY HH24:MI:SS') AS dt_geracao,
                   cc.cd_usuario, 
                   cc.fl_email_enviado,
                   p.nome
              FROM projetos.aviso_desligamento_controle cc
              JOIN public.participantes p
                ON p.cd_empresa            = cc.cd_empresa
               AND p.cd_registro_empregado = cc.cd_registro_empregado
               AND p.seq_dependencia       = cc.seq_dependencia
             WHERE cc.cd_empresa         = " . intval($args['cd_empresa']) . "
               AND cc.nr_mes_competencia = " . intval($args['nr_mes']) . "
               AND cc.nr_ano_competencia = " . intval($args['nr_ano']) . "
			   AND cc.dt_exclusao        IS NULL
               ".(trim($args['fl_email_enviado']) == "S" ? " AND cc.fl_email_enviado = 'S' " : "")."
      ";
        #echo "<pre style='text-align:left;'>contribuicao_controle<BR>$qr_sql</pre>"; exit;
        $result = $this->db->query($qr_sql);
    }
    
    function relatorio_listar(&$result, $args=array())
    {
        $qr_sql = "
					SELECT e.cd_email,
						   TO_CHAR(e.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
						   TO_CHAR(e.dt_email_enviado, 'DD/MM/YYYY HH24:MI:SS') AS dt_email_enviado,
						   e.para,
						   e.de,
						   e.cc,
						   e.cd_empresa,
						   e.cd_registro_empregado,
						   e.seq_dependencia,
						   p.nome,
						   e.assunto,
						   e.fl_retornou,
						   e.texto
					  FROM projetos.aviso_desligamento_controle cc
					  JOIN projetos.envia_emails e
						ON e.cd_empresa            = cc.cd_empresa
					   AND e.cd_registro_empregado = cc.cd_registro_empregado
					   AND e.seq_dependencia       = cc.seq_dependencia
					  JOIN public.participantes p
						ON p.cd_empresa            = e.cd_empresa
					   AND p.cd_registro_empregado = e.cd_registro_empregado
					   AND p.seq_dependencia       = e.seq_dependencia
					 WHERE e.cd_evento             = 63
					   AND e.cd_empresa            = ".intval($args['cd_empresa'])."
					   ".(intval($args['cd_registro_empregado']) > 0 ? "AND e.cd_registro_empregado = " . intval($args['cd_registro_empregado']) : "") . "
					   ".(trim($args['seq_dependencia']) != ""       ? "AND e.seq_dependencia       = " . intval($args['seq_dependencia']) : "") . "
                       ".(trim($args["fl_retornou"]) != ""           ? "AND e.fl_retornou           = '" . $args["fl_retornou"] . "'" : "") . "
					   AND cc.nr_mes_competencia   = ".intval($args['nr_mes'])."
					   AND cc.nr_ano_competencia   = ".intval($args['nr_ano'])."
					   AND cc.dt_exclusao          IS NULL
					   AND DATE_TRUNC('day', e.dt_envio) = DATE_TRUNC('day', COALESCE(cc.dt_email_enviado,cc.dt_controle))
				     ORDER BY e.cd_email DESC
				     LIMIT 1		
		          ";
        #echo "<pre style='text-align:left;'>$qr_sql</pre>";exit;	
        $result = $this->db->query($qr_sql);
    }
}

?>