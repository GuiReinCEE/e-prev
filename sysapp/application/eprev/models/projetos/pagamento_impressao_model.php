<?php
class Pagamento_impressao_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT ai.cd_auto_atendimento_pagamento_impressao, 
                           MD5(ai.cd_auto_atendimento_pagamento_impressao::TEXT) AS cd_auto_atendimento_pagamento_impressao_md5,
                           ai.cd_plano, 
                           ai.cd_empresa, 
                           ai.cd_registro_empregado, 
                           ai.seq_dependencia, 
                           projetos.participante_nome(ai.cd_empresa, ai.cd_registro_empregado, ai.seq_dependencia) AS nome,
                           ai.tp_documento, 
                           ai.vl_valor, 
                           ai.mes_competencia,
                           ai.ano_competencia, 
                           REPLACE(ai.competencia_lista,'Pagamento ','') AS competencia_lista,
                           TO_CHAR(ai.dt_vencimento,'DD/MM/YYYY') AS dt_vencimento,
                           TO_CHAR(ai.dt_impressao,'DD/MM/YYYY HH24:MI:SS') AS dt_impressao, 
                           ai.ip, 
                           ai.codigo_barra,
                           ai.codigo_barra_interno,
                           ai.dados_post,
                           ai.dados_post_json,
                           ai.num_bloqueto,
                           CASE WHEN ai.ip LIKE ('10.63.%') THEN 'I' ELSE 'E' END AS fl_origem,
                           ai.cd_contrib_antecipada, 
                           ai.fl_tipo_registro, 
                           ai.xml_envio, 
                           ai.xml_retorno, 
                           ai.xml_check, 
                           ai.fl_erro_registro, 
                           ai.nr_registro,
                           ai.tp_registro_ambiente,
                           ai.origem,
                           CASE WHEN ai.origem = 'I' THEN 'Internet'
                                WHEN ai.origem = 'A' THEN 'Aplicativo'
                                ELSE ai.origem
                           END AS ds_origem,
                           (CASE WHEN ai.tp_documento = 'ARR' 
                                 THEN
                                    (SELECT (CASE WHEN COUNT(*) > 0 THEN 'S' ELSE 'N' END) AS fl_pago
                                       FROM public.bloqueto b
                                      WHERE b.cd_empresa            = ai.cd_empresa   
                                        AND b.cd_registro_empregado = ai.cd_registro_empregado
                                        AND b.seq_dependencia       = ai.seq_dependencia
                                        AND b.status = 'R'
                                        AND b.data_retorno IS NOT NULL
                                        AND (CASE WHEN b.num_bloqueto_novo IS NULL 
                                                  THEN b.num_bloqueto
                                                  ELSE CASE WHEN (SELECT MAX(CAST(b1.dt_emissao AS DATE))
                                                                    FROM bloqueto b1
                                                                   WHERE b1.num_bloqueto = b.num_bloqueto_novo) = CURRENT_DATE 
                                                  THEN b.num_bloqueto
                                                  ELSE b.num_bloqueto_novo
                                                  END 
                                             END) = ai.num_bloqueto) 
                                 ELSE
                                    (SELECT (CASE WHEN COUNT(*) > 0 THEN 'S' ELSE 'N' END) AS fl_pago 
                                       FROM public.retorno_boletos rb
                                     WHERE COALESCE(rb.nr_bloq_banco, rb.num_bloqueto) = COALESCE(ai.nr_registro, ai.num_bloqueto)
                                       AND rb.cd_empresa            = ai.cd_empresa   
                                       AND rb.cd_registro_empregado = ai.cd_registro_empregado
                                       AND rb.seq_dependencia       = ai.seq_dependencia) 
                            END) AS fl_pago
                           
                      FROM projetos.auto_atendimento_pagamento_impressao ai       
                     WHERE ai.dt_exclusao IS NULL
                       ".(intval($args["cd_auto_atendimento_pagamento_impressao"]) > 0 ? "AND ai.cd_auto_atendimento_pagamento_impressao = ".intval($args["cd_auto_atendimento_pagamento_impressao"]) : "")."
                       ".(trim($args["cd_plano"]) != "" ? "AND ai.cd_empresa IN (SELECT pl.cd_empresa FROM public.planos_patrocinadoras pl WHERE pl.cd_plano = ".intval(trim($args["cd_plano"])).")" : "")."
                       ".(trim($args["cd_plano_empresa"]) != "" ? "AND ai.cd_empresa = ".intval(trim($args["cd_plano_empresa"])) : "")."
                       ".(intval($args['cd_registro_empregado']) > 0 ? "AND ai.cd_empresa = ".$args['cd_empresa']." AND ai.cd_registro_empregado = ".$args['cd_registro_empregado']." AND ai.seq_dependencia = ".$args['seq_dependencia']: "")."
                       ".(((trim($args["dt_impressao_ini"]) != "") and (trim($args["dt_impressao_fim"]) != "")) ? "AND DATE_TRUNC('day', ai.dt_impressao) BETWEEN TO_DATE('".$args["dt_impressao_ini"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_impressao_fim"]."','DD/MM/YYYY')": "")."
                       ".(((trim($args["dt_vencimento_ini"]) != "") and (trim($args["dt_vencimento_fim"]) != "")) ? "AND DATE_TRUNC('day', ai.dt_vencimento) BETWEEN TO_DATE('".$args["dt_vencimento_ini"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_vencimento_fim"]."','DD/MM/YYYY')": "")."
                       ".(trim($args["cpf"]) != "" ? "AND (ai.cd_empresa, ai.cd_registro_empregado, ai.seq_dependencia) IN (SELECT x.cd_empresa, x.cd_registro_empregado, x.seq_dependencia FROM projetos.participante_cpf('".trim($args["cpf"])."', NULL) x)" : "")."
                       ".(trim($args["fl_erro_registro"]) != "" ? "AND ai.fl_erro_registro = '".trim($args["fl_erro_registro"])."'" : "")."
                     ORDER BY dt_impressao DESC 
                  ";
            
        #echo "<pre>$qr_sql</pre>"; 
        $result = $this->db->query($qr_sql);
    }
}
?>