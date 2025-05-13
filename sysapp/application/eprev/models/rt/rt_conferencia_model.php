<?php
class rt_conferencia_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT c.cd_ticket, 
                           TO_CHAR(c.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                           TO_CHAR(c.dt_atualizacao,'DD/MM/YYYY HH24:MI:SS') AS dt_atualizacao,
                           TO_CHAR(c.dt_resolvido,'DD/MM/YYYY HH24:MI:SS') AS dt_resolvido,
                           --c.assunto,
						   convert_from(convert_to(c.assunto,'utf-8'),'latin-1') AS assunto,
                           c.subprograma,
                           c.cpf,
                           p.cd_empresa,
                           p.cd_registro_empregado,
                           p.seq_dependencia,
                           p.nome,
                           p.email,
                           p.email_profissional
                      FROM rt.conferencia(TO_DATE('".(trim($args['dt_rt_inicio']) != "" ? trim($args['dt_rt_inicio']) : "01/01/2000")."','DD/MM/YYYY'), TO_DATE('".(trim($args['dt_rt_fim']) != "" ? trim($args['dt_rt_fim']) : date("d/m/Y"))."','DD/MM/YYYY')) c
                      LEFT JOIN public.participantes p
                        ON funcoes.format_cpf(p.cpf_mf) = c.cpf
                       AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN (SELECT pc.cd_empresa,
                                                                                                 pc.cd_registro_empregado,
                                                                                                 pc.seq_dependencia 
                                                                                            FROM projetos.participante_cpf(c.cpf,1) pc)
                     WHERE 1 = 1
                     ".(trim($args['cpf']) != "" ? "AND c.cpf = '".trim($args['cpf'])."'" : "")."
                     ".(trim($args['nome']) != "" ? "AND TRIM(UPPER(p.nome)) = TRIM(UPPER('".trim($args['nome'])."'))" : "")."
                     ".(trim($args['cd_empresa']) != "" ? "AND p.cd_empresa = '".intval($args['cd_empresa'])."'" : "")."
                     ".(trim($args['cd_registro_empregado']) != "" ? "AND p.cd_registro_empregado = '".intval($args['cd_registro_empregado'])."'" : "")."
                     ".(trim($args['seq_dependencia']) != "" ? "AND p.seq_dependencia = '".intval($args['seq_dependencia'])."'" : "")."
                     ORDER BY c.dt_atualizacao DESC;
                  ";
        $result = $this->db->query($qr_sql);
        #echo "<PRE>$qr_sql</PRE>";
    }
}
?>