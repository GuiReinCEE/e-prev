<?php
class Equipe_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function listar($cd_divisao)
    {
        $qr_sql = "
            SELECT uc.codigo,
                   uc.nome,
                   uc.divisao,
                   uc.usuario,
                   uc.guerra,
                   funcoes.get_usuario_avatar(uc.codigo) AS avatar,
                   uc.tipo,
                   uc.nr_ramal,
                   CASE WHEN tipo = 'D' THEN uc.observacao 
                        WHEN tipo = 'G' THEN (CASE WHEN SUBSTRING(uc.divisao FROM 1 FOR 1) = 'A' THEN 'Assessor(a)' ELSE 'Gerente' END)
                        WHEN tipo = 'U' THEN 'Colaborador(a)'
                        WHEN tipo = 'N' THEN 'Colaborador(a)'
                        WHEN tipo = 'P' THEN 'Prestador(a) de Serviço'
                        WHEN tipo = 'A' THEN 'Aprendiz'
                        WHEN tipo = 'E' THEN 'Estagiário(a)'
                        ELSE ''
                   END || (CASE WHEN COALESCE(uc.indic_13,'N') = 'S' THEN ' - Supervisor(a)' ELSE '' END) AS papel,
                   c.nome_cargo,
                   uc.cd_registro_empregado,
                   CASE WHEN COALESCE(uc.tipo,'') = 'D' AND COALESCE(uc.observacao,'') = 'Diretor-Presidente' THEN 0 
				        WHEN COALESCE(uc.tipo,'') = 'G' THEN 1
                        WHEN COALESCE(uc.indic_13,'N') = 'S' THEN 2
                        ELSE 3
                   END AS nr_ordem,
                   (SELECT CASE WHEN COUNT(*) > 0 THEN 'S' ELSE 'N' END
                      FROM public.benef_rh_ferias brf
                     WHERE brf.cd_empresa            = 9
                       AND brf.seq_dependencia       = 0
                       AND brf.cd_registro_empregado = COALESCE(uc.cd_registro_empregado,0)
                       AND CURRENT_DATE BETWEEN brf.dt_ini_ferias AND brf.dt_fim_ferias) AS fl_ferias,
				   (SELECT TO_CHAR(brf.dt_ini_ferias,'DD/MM/YY') || ' até ' || TO_CHAR(brf.dt_fim_ferias,'DD/MM/YY')
				      FROM public.benef_rh_ferias brf
				     WHERE brf.cd_empresa            = 9
				       AND brf.seq_dependencia       = 0
				       AND brf.cd_registro_empregado = COALESCE(uc.cd_registro_empregado,0)
				       AND CURRENT_DATE BETWEEN brf.dt_ini_ferias AND brf.dt_fim_ferias
				     ORDER BY brf.dt_ini_ferias
				     LIMIT 1) AS periodo_ferias							   
              FROM projetos.usuarios_controledi uc
              LEFT JOIN projetos.cargos c
                ON c.cd_cargo = uc.cd_cargo
             WHERE uc.tipo    NOT IN ('X','T')
               AND uc.divisao = '".trim($cd_divisao)."'
            ORDER BY nr_ordem, 
                     uc.nome;";

        return $this->db->query($qr_sql)->result_array();
    }
}
?>