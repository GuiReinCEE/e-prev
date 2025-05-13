<?php
class Relatorio_atividades_participante_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function get_gerencia_solicitante()
    {
        $qr_sql = "
            SELECT DISTINCT codigo AS value, 
                   nome AS text 
              FROM projetos.divisoes a 
              JOIN projetos.atividades b 
                ON a.codigo = b.divisao 
             ORDER BY nome ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_projetos()
    {
        $qr_sql = "
            SELECT codigo AS value, nome AS text 
              FROM projetos.projetos 
             WHERE codigo IN
                 (
                    SELECT DISTINCT(a.sistema) 
                      FROM projetos.atividades a, listas l1, listas l2 
                     WHERE l1.codigo    = a.status_atual 
                       AND l1.categoria = 'STAT' 
                       AND l2.categoria = 'TPAT' 
                       AND l2.codigo    = a.tipo
                 ) 
             ORDER BY nome;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_tipo_solicitacao()
    {
        $qr_sql = "
            SELECT tm.codigo AS value,
                   tm.divisao || ' - ' || tm.descricao AS text
              FROM public.listas tm
              JOIN projetos.divisoes d
                ON d.codigo = tm.divisao
             WHERE tm.categoria   = 'TPMN' 
               AND tm.dt_exclusao IS NULL
               AND tm.divisao     <> '*'
             ORDER BY tm.divisao   ASC,
                      tm.descricao ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_solicitante()
    {
        $qr_sql = "
            SELECT DISTINCT a.codigo AS value, 
                   a.nome AS text
              FROM projetos.usuarios_controledi a
              JOIN projetos.atividades b ON a.codigo=b.cod_solicitante
             /*WHERE a.tipo IN ('D','G','N','U')*/
             ORDER BY a.nome;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_atendente()
    {
        $qr_sql = "
            SELECT DISTINCT a.codigo AS value, 
                   a.nome AS text
              FROM projetos.usuarios_controledi a
              JOIN projetos.atividades b on a.codigo=b.cod_atendente
             /*WHERE a.tipo IN ('D','G','N','U')*/
             ORDER BY a.nome;";

        return $this->db->query($qr_sql)->result_array();
    }
}