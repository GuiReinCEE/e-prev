<?php
class Solic_entrega_documento_resp_area_consolidadora_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function carrega_divisao()
    {
        $qr_sql = "
            SELECT codigo AS cd_gerencia,
                   nome
              FROM funcoes.get_gerencias_vigente();";

        return $this->db->query($qr_sql)->result_array(); 
    }

    public function carrega_usuario($cd_gerencia)
    {
        $qr_sql = "
            SELECT funcoes.get_usuario_nome(cd_usuario) AS ds_usuario,
                   cd_usuario
              FROM projetos.solic_entrega_documento_resp_area_consolidadora
             WHERE cd_gerencia = '".trim($cd_gerencia)."'
               AND dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array(); 
    }

    public function get_usuario_area_consolidadora($cd_gerencia)
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_usuario_gerencia('".trim($cd_gerencia)."')";

        return $this->db->query($qr_sql)->result_array(); 
    }

    public function carrega_nome_area($cd_gerencia)
    {
        $qr_sql = "
            SELECT codigo AS cd_gerencia,
                   nome
              FROM funcoes.get_gerencias_vigente('', '')
             WHERE codigo = '".trim($cd_gerencia)."';";

        return $this->db->query($qr_sql)->row_array(); 
    }

    public function salvar_resp_area_concolidadora($args = array())
    {
        if(count($args['usuario']) > 0)
        {
            $qr_sql = "
                UPDATE projetos.solic_entrega_documento_resp_area_consolidadora
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_gerencia = '".trim($args['cd_gerencia'])."'
                   AND dt_exclusao IS NULL
                   AND cd_usuario NOT IN (".implode(",", $args['usuario']).");
                    
                INSERT INTO projetos.solic_entrega_documento_resp_area_consolidadora(cd_gerencia, cd_usuario, cd_usuario_inclusao,                              cd_usuario_alteracao)
                     SELECT '".trim($args['cd_gerencia'])."', x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                       FROM (VALUES (".implode("),(", $args['usuario']).")) x
                      WHERE x.column1 NOT IN (SELECT a.cd_usuario
                                                FROM projetos.solic_entrega_documento_resp_area_consolidadora a
                                               WHERE a.cd_gerencia = '".trim($args['cd_gerencia'])."'
                                                 AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql = "
                UPDATE projetos.solic_entrega_documento_resp_area_consolidadora
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_gerencia = '".trim($args['cd_gerencia'])."'
                   AND dt_exclusao   IS NULL;";
        }

        $this->db->query($qr_sql);
    }
}