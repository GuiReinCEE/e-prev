<?php
class boas_vindas_controle_patrocinadora_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function listar(&$result, $args=array())
    {
        $qr_sql = "
            SELECT p.cd_plano, 
                   p.cd_empresa, 
                   p.cd_registro_empregado, 
                   p.seq_dependencia, 
                   p.nome,
                   p.email,
                   p.email_profissional,
                   CASE WHEN (COALESCE(p.email,'') LIKE '%@%' OR COALESCE(p.email_profissional,'') LIKE '%@%') THEN 'S' ELSE 'N' END AS fl_email,
                   TO_CHAR(t.dt_ingresso_eletro,'DD/MM/YYYY') AS dt_ingresso,
                   TO_CHAR(b.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_gerado,
                   TO_CHAR(b.dt_envio_email,'DD/MM/YYYY HH24:MI:SS') AS dt_envio_email,
                   (SELECT TO_CHAR(d.dt_documento,'DD/MM/YYYY') 
                      FROM public.documentos d
                     WHERE d.cd_empresa            = p.cd_empresa 
                       AND d.cd_registro_empregado = p.cd_registro_empregado
                       AND d.seq_dependencia       = p.seq_dependencia      
                       AND d.cd_tipo_doc           = 273 --CERTIFICADO
                     ORDER by d.dt_documento DESC, d.dt_digitalizacao DESC 
                     LIMIT 1) AS dt_certificado,
                   (SELECT TO_CHAR(d.dt_documento,'DD/MM/YYYY') 
                      FROM public.documentos d
                     WHERE d.cd_empresa            = p.cd_empresa 
                       AND d.cd_registro_empregado = p.cd_registro_empregado
                       AND d.seq_dependencia       = p.seq_dependencia      
                       AND d.cd_tipo_doc           = 225 --PEDIDO INSCRICAO
                     ORDER by d.dt_documento DESC, d.dt_digitalizacao DESC 
                     LIMIT 1) AS dt_inscricao,
                   oracle.fnc_retorna_opcao(p.cd_empresa::INTEGER,p.cd_registro_empregado::INTEGER, p.seq_dependencia::INTEGER, 'WEB0001', COALESCE(b.dt_inclusao::DATE, CURRENT_DATE)) AS fl_eletronico,
                   CASE WHEN ((SELECT TO_CHAR(d.dt_documento,'DD/MM/YYYY') 
                                 FROM public.documentos d
                                WHERE d.cd_empresa            = p.cd_empresa 
                                  AND d.cd_registro_empregado = p.cd_registro_empregado
                                  AND d.seq_dependencia       = p.seq_dependencia       
                                  AND d.cd_tipo_doc           = 225 --PEDIDO INSCRICAO
                                ORDER by d.dt_documento DESC, d.dt_digitalizacao DESC 
                                LIMIT 1) IS NOT NULL AND (SELECT TO_CHAR(d.dt_documento,'DD/MM/YYYY') 
                                                            FROM public.documentos d
                                                           WHERE d.cd_empresa            = p.cd_empresa 
                                                             AND d.cd_registro_empregado = p.cd_registro_empregado
                                                             AND d.seq_dependencia       = p.seq_dependencia        
                                                             AND d.cd_tipo_doc           = 273 --CERTIFICADO
                                                           ORDER by d.dt_documento DESC, d.dt_digitalizacao DESC 
                                                           LIMIT 1) IS NOT NULL AND dt_envio_email IS NULL) THEN 'S' ELSE 'N' END AS fl_enviar,
                   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
              FROM public.participantes p
              JOIN public.titulares t
                ON t.cd_empresa            = p.cd_empresa 
               AND t.cd_registro_empregado = p.cd_registro_empregado 
               AND t.seq_dependencia       = p.seq_dependencia      
              LEFT JOIN projetos.boas_vindas_controle_patrocinadora b
                ON b.cd_empresa            = p.cd_empresa 
               AND b.cd_registro_empregado = p.cd_registro_empregado 
               AND b.seq_dependencia       = p.seq_dependencia  
               AND b.cd_plano              = p.cd_plano     
             WHERE p.cd_plano > 0
               AND p.dt_obito IS NULL 
               AND (
                    (p.cd_empresa = 0 AND p.cd_plano = 2) -- CEEE CEEEPREV
                    OR
                    (p.cd_empresa = 9 AND p.cd_plano = 2) -- FCEEE CEEEPREV
                    OR
                    (p.cd_empresa = 6 AND p.cd_plano = 6) -- CRM CRMPREV
                    OR
                    (p.cd_empresa = 3 AND p.cd_plano = 1) -- CGTEE UNICO
                    OR
                    (p.cd_empresa = 21 AND p.cd_plano = 21) -- INPEL INPELPREV
                    OR
                    (p.cd_empresa = 22 AND p.cd_plano = 22) -- CERAN CERANPREV
                    OR
                    (p.cd_empresa = 23 AND p.cd_plano = 23) -- FOZ FOZ DO CHAPECÓ 
                   )
               ".(trim($args['fl_eletronico']) != "" ? "AND oracle.fnc_retorna_opcao(p.cd_empresa::INTEGER,p.cd_registro_empregado::INTEGER, p.seq_dependencia::INTEGER, 'WEB0001', COALESCE(b.dt_inclusao::DATE, CURRENT_DATE)) = '".trim($args['fl_eletronico'])."'" : "")."
               ".(trim($args['fl_email']) == "S" ? "AND (COALESCE(p.email,'') LIKE '%@%' OR COALESCE(p.email_profissional,'') LIKE '%@%')" : "")."
               ".(trim($args['fl_email']) == "N" ? "AND NOT (COALESCE(p.email,'') LIKE '%@%' OR COALESCE(p.email_profissional,'') LIKE '%@%')" : "")."
               ".(trim($args['fl_enviado']) == "S" ? "AND b.dt_envio_email IS NOT NULL" : "")."
               ".(trim($args['fl_enviado']) == "N" ? "AND b.dt_envio_email IS NULL" : "")."
               ".(trim($args['fl_gerado']) == "S" ? "AND b.dt_inclusao IS NOT NULL" : "")."
               ".(trim($args['fl_gerado']) == "N" ? "AND b.dt_inclusao IS NULL" : "")."                    
               ".(trim($args['fl_certificado']) == "S" ? "AND (SELECT TO_CHAR(d.dt_documento,'DD/MM/YYYY') 
                                                                 FROM public.documentos d
                                                                WHERE d.cd_empresa            = p.cd_empresa 
                                                                  AND d.cd_registro_empregado = p.cd_registro_empregado
                                                                  AND d.seq_dependencia       = p.seq_dependencia       
                                                                  AND d.cd_tipo_doc           = 273 --CERTIFICADO
                                                                ORDER by d.dt_documento DESC, d.dt_digitalizacao DESC 
                                                                LIMIT 1) IS NOT NULL" : "")."
               ".(trim($args['fl_certificado']) == "N" ? "AND (SELECT TO_CHAR(d.dt_documento,'DD/MM/YYYY') 
                                                                 FROM public.documentos d
                                                                WHERE d.cd_empresa            = p.cd_empresa 
                                                                  AND d.cd_registro_empregado = p.cd_registro_empregado
                                                                  AND d.seq_dependencia       = p.seq_dependencia       
                                                                  AND d.cd_tipo_doc           = 273 --CERTIFICADO
                                                                ORDER by d.dt_documento DESC, d.dt_digitalizacao DESC 
                                                                LIMIT 1) IS NULL" : "")."
               ".(trim($args['fl_inscricao']) == "S" ? "AND (SELECT TO_CHAR(d.dt_documento,'DD/MM/YYYY') 
                                                               FROM public.documentos d
                                                              WHERE d.cd_empresa            = p.cd_empresa 
                                                                AND d.cd_registro_empregado = p.cd_registro_empregado
                                                                AND d.seq_dependencia       = p.seq_dependencia         
                                                                AND d.cd_tipo_doc           = 225 --PEDIDO INSCRICAO
                                                              ORDER by d.dt_documento DESC, d.dt_digitalizacao DESC 
                                                              LIMIT 1) IS NOT NULL" : "")."
               ".(trim($args['fl_inscricao']) == "N" ? "AND (SELECT TO_CHAR(d.dt_documento,'DD/MM/YYYY') 
                                                               FROM public.documentos d
                                                              WHERE d.cd_empresa            = p.cd_empresa 
                                                                AND d.cd_registro_empregado = p.cd_registro_empregado
                                                                AND d.seq_dependencia       = p.seq_dependencia         
                                                                AND d.cd_tipo_doc           = 225 --PEDIDO INSCRICAO
                                                              ORDER by d.dt_documento DESC, d.dt_digitalizacao DESC 
                                                              LIMIT 1) IS NULL" : "")."
               ".(trim($args['cd_empresa']) != "" ? "AND p.cd_empresa = ".trim($args['cd_empresa']) : "")."
               ".(trim($args['cd_plano']) != "" ? "AND p.cd_plano = ".trim($args['cd_plano']) : "")."

               AND CAST(t.dt_ingresso_eletro AS DATE) BETWEEN TO_DATE('".trim($args['dt_ini_ingresso'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_fim_ingresso'])."','DD/MM/YYYY')
             ORDER BY p.nome;";

        $result = $this->db->query($qr_sql);
    }
    
    function enviar(&$result, $args=array())
    {
        $qr_sql = "
                    INSERT INTO projetos.boas_vindas_controle_patrocinadora
                        (
                            cd_empresa, 
                            cd_registro_empregado, 
                            seq_dependencia, 
                            cd_plano, 
                            cd_usuario_inclusao
                         )
                    SELECT p.cd_empresa,
                           p.cd_registro_empregado,
                           p.seq_dependencia,
                           p.cd_plano,
                           ".intval($args['cd_usuario'])."
                      FROM public.participantes p
                     WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN (".$args['part_selecionado'].");

                    SELECT rotinas.email_boas_vindas_patrocinadora(x.cd_empresa::INTEGER, x.cd_plano::INTEGER, ".$args['cd_usuario'].")
                      FROM (SELECT DISTINCT p.cd_empresa,
                                   p.cd_plano
                              FROM public.participantes p
                             WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN (".$args['part_selecionado'].")) x;
                  ";        

        #echo "<PRE>".$qr_sql."</PRE>"; exit;
        $result = $this->db->query($qr_sql);
    }
}
?>