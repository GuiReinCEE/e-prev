<?php
class contracheque_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {       
    	if(trim($this->session->userdata('indic_04')) == "*")
        {
        	$qr_sql = "
        		INSERT INTO projetos.contracheque_log_acesso (cd_usuario, cd_registro_empregado) 
        		VALUES (".intval($this->session->userdata('codigo')).", ".intval($args['cc_registro_empregado']).");";

        		$this->db->query($qr_sql);
        }

        $qr_sql = "
					SELECT c.codigo,
					       c.descricao,
						   c.referencia,
						   c.valor,
						   c.tipo,
						   CASE WHEN c.tipo = 'P' THEN 'blue'
								WHEN c.tipo = 'D' THEN 'red'
								ELSE ''
						   END AS tp_cor,
					       c.banco,
						   c.agencia,
						   c.conta,						   
						   TO_CHAR(c.dt_pgto, 'DD/MM/YYYY') AS dt_pagamento,					   
						   TO_CHAR(ca.nr_mes,'FM00') || '/' || TO_CHAR(ca.nr_ano,'FM0000') AS mes_ano,					   
						   p.nome,     
						   p.endereco,
						   p.nr_endereco,
						   '/' || p.complemento_endereco AS complemento_endereco,
						   p.bairro,
						   TO_CHAR(COALESCE(p.cep,0),'FM00000') || '-' || TO_CHAR(COALESCE(p.complemento_cep,0), 'FM000') AS cep,
						   p.cidade, 
						   p.unidade_federativa AS uf
					  FROM projetos.contracheque c
					  JOIN projetos.contracheque_arquivo ca
					    ON ca.dt_pagamento = c.dt_pgto						  
					  LEFT JOIN participantes p
                        ON p.cd_registro_empregado = c.cd_registro_empregado           
					   AND p.cd_empresa            = 9  						   
					   AND p.seq_dependencia       = 0					   
					 WHERE c.cd_empresa            = 1                                            
					   AND c.cd_registro_empregado = ".intval($args['cc_registro_empregado'])."
					   AND c.tipo                  <> 'C' 
					    ".((trim($this->session->userdata('indic_04')) == "*") ? '' : 'AND c.dt_liberacao IS NOT NULL')." 	
					   AND DATE_TRUNC('day', c.dt_pgto)::DATE = TO_DATE('".$args['cc_dt_pagamento']."','YYYY-MM-DD')
					 ORDER BY c.tipo DESC, 
							  c.codigo ASC;
                  ";
            


		#echo "<PRE>$qr_sql</PRE>"; exit;
        $result = $this->db->query($qr_sql);
    }   
	
	function listarBeneficios(&$result, $args=array())
    {       
        $qr_sql = "
					SELECT br.seq_ben, 
					       LOWER(TRIM(REPLACE(br.tipo_beneficio, 'TICKET',''))) AS tipo_beneficio,
						   br.vl_empresa, 
						   br.vl_funcionario AS vl_empregado, 
						   br.vl_total
					  FROM public.beneficios_rh br
					 WHERE br.dt_conferencia IS NOT NULL
					   AND br.emp = 9
					   AND br.seq = 0
					   AND br.re  = ".intval($args['cc_registro_empregado'])."
					   AND br.dt_referencia::DATE = CAST(DATE_TRUNC('month', TO_DATE('".$args['cc_dt_pagamento']."','YYYY-MM-DD')) AS DATE)
					 ORDER BY br.seq_ben 					
                  ";
		#echo "<PRE>$qr_sql</PRE>"; exit;
        $result = $this->db->query($qr_sql);
    } 	
	
    function cbCompetencia(&$result, $args=array())
    {       
        $qr_sql = "
					SELECT DISTINCT TO_CHAR(c.dt_pgto, 'YYYY-MM-DD') AS value,
						   TO_CHAR(ca.nr_mes,'FM00') || '/' || TO_CHAR(ca.nr_ano,'FM0000') AS text
					  FROM projetos.contracheque c
					  JOIN projetos.contracheque_arquivo ca
					    ON ca.dt_pagamento = c.dt_pgto					  
					 WHERE c.cd_empresa            = 1
					   AND c.cd_registro_empregado = ".$args['cc_registro_empregado']."
					   AND c.tipo                  <> 'C'
					   ".((trim($this->session->userdata('indic_04')) == "*") ? '' : 'AND c.dt_liberacao IS NOT NULL')." 	
					 ORDER BY value DESC
                  ";
        $result = $this->db->query($qr_sql);
    } 	
}
?>