<?php
class cadastro_sem_email_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function listar(&$result, $args=array())
    {
        $qr_sql = "SELECT pa.sigla AS ds_empresa, 
                          p.cd_empresa,
                          p.cd_registro_empregado,
                          p.nome,
                          p.seq_dependencia,
                          projetos.participante_forma_pagamento(p.cd_empresa::INTEGER,p.cd_registro_empregado::INTEGER, p.seq_dependencia::INTEGER) AS forma_pagamento,
                     CASE WHEN COALESCE(p.cd_plano,0) > 0 
                          THEN 'S' 
                          ELSE 'N' 
                      END AS fl_plano,
                          TO_CHAR(t.dt_ingresso_eletro,'DD/MM/YYYY') AS dt_ingresso_eletro,
                          TO_CHAR(t.dt_desligamento_eletro,'DD/MM/YYYY') AS dt_desligamento_eletro,
                          TO_CHAR(t.dt_cancela_inscricao,'DD/MM/YYYY') AS dt_cancela_inscricao,
                          TO_CHAR(p.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
                     FROM public.participantes p
                     JOIN public.patrocinadoras pa
                       ON pa.cd_empresa   = p.cd_empresa
                      AND pa.tipo_cliente = 'I'
                     JOIN public.titulares t
                       ON t.cd_empresa            = p.cd_empresa
                      AND t.cd_registro_empregado = p.cd_registro_empregado
                      AND t.seq_dependencia       = p.seq_dependencia	
                    WHERE p.dt_obito        IS NULL
                      AND COALESCE(p.email,'') NOT LIKE '%@%'
                      AND COALESCE(p.email_profissional,'') NOT LIKE '%@%'
                      ".(((trim($args['dt_inclusao_inicio']) != "") and  (trim($args['dt_inclusao_fim']) != "")) ? " AND DATE_TRUNC('day', p.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")."
                      ".(((trim($args['dt_ingresso_inicio']) != "") and  (trim($args['dt_ingresso_fim']) != "")) ? " AND DATE_TRUNC('day', t.dt_ingresso_eletro) BETWEEN TO_DATE('".$args['dt_ingresso_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_ingresso_fim']."', 'DD/MM/YYYY')" : "")."
                      ".($args['cd_plano_empresa'] != '' ? " AND p.cd_plano_empresa =". intval($args['cd_plano_empresa']) : '')."
                      ".($args['cd_plano'] != '' ? " AND p.cd_plano =". intval($args['cd_plano']) : '')."
                      ".($args['fl_plano'] == 'N' ? " AND p.cd_plano <=0 " : '')."
                      ".($args['fl_plano'] == 'S' ? " AND p.cd_plano > 0 " : '')."
                      ".($args['fl_dt_cancela_inscricao'] == 'N' ? " AND t.dt_cancela_inscricao IS NULL " : '')."
                      ".($args['fl_dt_cancela_inscricao'] == 'S' ? " AND t.dt_cancela_inscricao IS NOT NULL " : '')."
                      ".($args['fl_dt_desligamento_eletro'] == 'N' ? " AND t.dt_desligamento_eletro IS NULL " : '')."
                      ".($args['fl_dt_desligamento_eletro'] == 'S' ? " AND t.dt_desligamento_eletro IS NOT NULL " : '')."
                          
                    ORDER BY p.cd_empresa, 
                          p.cd_registro_empregado, 
                          p.seq_dependencia";

        $result = $this->db->query($qr_sql);
    }
}