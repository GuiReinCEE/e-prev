<?php
class Regulamento_revisao_model extends Model
{
    public function get_revisao($cd_regulamento_revisao = 0, $cd_regulamento_revisao_pai = 0)
    {
        $qr_sql = "
            SELECT cd_regulamento_revisao,
                   cd_regulamento_revisao_pai,
                   nr_ordem,
                   ds_regulamento_revisao,
                   ds_descricao,
                   cd_regulamento_revisao AS value,
                   substring(ds_regulamento_revisao for 100) || 
                   (CASE WHEN character_length(ds_regulamento_revisao) > 100
                         THEN '...'
                         ELSE ''
                   END) AS text
              FROM gestao.regulamento_revisao
             WHERE dt_exclusao IS NULL
               AND cd_regulamento_revisao != ".intval($cd_regulamento_revisao)."
               AND ".(intval($cd_regulamento_revisao_pai) > 0 ? "cd_regulamento_revisao_pai = ".intval($cd_regulamento_revisao_pai) : "cd_regulamento_revisao_pai IS NULL")."
              ORDER BY nr_ordem ASC;";
             
        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_regulamento_revisao)
    {
        $qr_sql = "
            SELECT cd_regulamento_revisao,
                   cd_regulamento_revisao_pai,
                   nr_ordem,
                   ds_regulamento_revisao,
                   ds_descricao
              FROM gestao.regulamento_revisao
             WHERE cd_regulamento_revisao = ".intval($cd_regulamento_revisao)."
               AND dt_exclusao IS NULL;";
             
        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $cd_regulamento_revisao = intval($this->db->get_new_id('gestao.regulamento_revisao', 'cd_regulamento_revisao'));

        $qr_sql = "
            INSERT INTO gestao.regulamento_revisao
            (
                cd_regulamento_revisao,
                cd_regulamento_revisao_pai, 
                nr_ordem, 
                ds_regulamento_revisao, 
                ds_descricao,
                cd_usuario_inclusao,
                cd_usuario_alteracao
            ) 
        VALUES 
            ( 
                ".(intval($cd_regulamento_revisao) != 0 ? intval($cd_regulamento_revisao) : "DEFAULT").",
                ".(intval($args['cd_regulamento_revisao_pai']) != 0 ? intval($args['cd_regulamento_revisao_pai']) : "DEFAULT").",
                ".(intval($args['nr_ordem']) != 0 ? intval($args['nr_ordem']) : "DEFAULT").",
                ".(trim($args['ds_regulamento_revisao']) != '' ? str_escape($args['ds_regulamento_revisao']) : "DEFAULT").",
                ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
                ".intval($args['cd_usuario']).",
                ".intval($args['cd_usuario'])."
            );";

        if(count($args['cd_regulamento_tipo']) > 0)
        {
            $qr_sql .= "
                INSERT INTO gestao.regulamento_revisao_regulamento_tipo
                (
                    cd_regulamento_revisao, 
                    cd_regulamento_tipo,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
                SELECT ".intval($cd_regulamento_revisao).", 
                       x.column1,
                       ".intval($args['cd_usuario']).", 
                       ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['cd_regulamento_tipo']).")) x;";
        }

        $this->db->query($qr_sql);

        return $cd_regulamento_revisao;

    }

    public function atualizar($cd_regulamento_revisao, $args = array())
    {
        $qr_sql = "
            UPDATE gestao.regulamento_revisao 
               SET cd_regulamento_revisao_pai = ".(intval($args['cd_regulamento_revisao_pai']) > 0 ? intval($args['cd_regulamento_revisao_pai']) : "DEFAULT").", 
                   nr_ordem                   = ".(intval($args['nr_ordem']) > 0 ? intval($args['nr_ordem']) : "DEFAULT").", 
                   ds_regulamento_revisao     = ".(trim($args['ds_regulamento_revisao']) != '' ? str_escape($args['ds_regulamento_revisao']) : "DEFAULT").",
                   ds_descricao               = ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
                   cd_usuario_alteracao       = ".intval($args['cd_usuario']).",
                   dt_alteracao               = CURRENT_TIMESTAMP
             WHERE cd_regulamento_revisao     = ".intval($cd_regulamento_revisao).";";   

        if(count($args['cd_regulamento_tipo']) > 0)
        {
            $qr_sql .= "
                UPDATE gestao.regulamento_revisao_regulamento_tipo
                   SET cd_usuario_exclusao                      = ".intval($args['cd_usuario']).",
                       dt_exclusao                              = CURRENT_TIMESTAMP
                 WHERE cd_regulamento_revisao = ".intval($cd_regulamento_revisao)."
                   AND dt_exclusao IS NULL
                   AND cd_regulamento_tipo NOT IN (".implode(",", $args['cd_regulamento_tipo']).");
    
                INSERT INTO gestao.regulamento_revisao_regulamento_tipo
                     (
                        cd_regulamento_revisao, 
                        cd_regulamento_tipo,
                        cd_usuario_inclusao,
                        cd_usuario_alteracao
                     )
                SELECT ".intval($cd_regulamento_revisao).", 
                        x.column1, 
                       ".intval($args['cd_usuario']).", 
                       ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['cd_regulamento_tipo']).")) x
                 WHERE x.column1 NOT IN (SELECT rart.cd_regulamento_tipo
                                           FROM gestao.regulamento_revisao_regulamento_tipo rart
                                          WHERE rart.cd_regulamento_revisao = ".intval($cd_regulamento_revisao)."
                                            AND rart.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE gestao.regulamento_revisao_regulamento_tipo
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_regulamento_revisao = ".intval($cd_regulamento_revisao)."
                   AND dt_exclusao IS NULL;";
        } 

        $this->db->query($qr_sql);
    }

    public function get_regulamento($cd_regulamento_revisao)
    {
        $qr_sql = "
            SELECT rart.cd_regulamento_tipo,
                   rt.ds_regulamento_tipo
              FROM gestao.regulamento_revisao_regulamento_tipo rart
              JOIN gestao.regulamento_tipo rt
                ON rt.cd_regulamento_tipo = rart.cd_regulamento_tipo
             WHERE rart.cd_regulamento_revisao = ".intval($cd_regulamento_revisao)."
               AND rart.dt_exclusao IS NULL;";
         
        return $this->db->query($qr_sql)->result_array();
    }

    public function get_next_ordem($cd_regulamento_revisao_pai = 0)
	{
		$qr_sql = "
			SELECT (COALESCE(MAX(nr_ordem), 0) + 1) AS nr_ordem
			  FROM gestao.regulamento_revisao
			 WHERE dt_exclusao              IS NULL
			   ".(intval($cd_regulamento_revisao_pai) > 0 ? "AND cd_regulamento_revisao_pai = ".intval($cd_regulamento_revisao_pai) : "").";";

		return $this->db->query($qr_sql)->row_array();
	}

    public function get_regulamento_tipo()
    {
        $qr_sql = "
            SELECT cd_regulamento_tipo AS value,
                   ds_regulamento_tipo AS text
              FROM gestao.regulamento_tipo
             WHERE dt_exclusao                 IS NULL
               AND cd_regulamento_tipo_vigente IS NULL
               AND cd_plano                    IS NOT NULL
             ORDER BY cd_plano ASC;";
         
        return $this->db->query($qr_sql)->result_array();
    }

    public function verifica_ordem($nr_ordem, $cd_regulamento_revisao_pai = 0)
	{
        $qr_sql = "               
            SELECT (CASE WHEN COUNT(*) > 0 THEN 'S' ELSE 'N' END) AS fl_ordem
              FROM gestao.regulamento_revisao rr
             WHERE dt_exclusao IS NULL
               AND nr_ordem    = ".intval($nr_ordem)."
               ".(intval($cd_regulamento_revisao_pai) > 0 ? "AND cd_regulamento_revisao_pai = ".intval($cd_regulamento_revisao_pai) : "").";";

		return $this->db->query($qr_sql)->row_array();
    }	

    public function atualiza_nr_ordem($cd_regulamento_revisao, $args = array(), $ds_operador = '+')
	{
        $qr_sql = "
            UPDATE gestao.regulamento_revisao AS t
               SET nr_ordem             = x.nr_ordem,
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
              FROM (SELECT cd_regulamento_revisao,
                           (nr_ordem ".trim($ds_operador)."1) AS nr_ordem
                      FROM gestao.regulamento_revisao
                     WHERE dt_exclusao                             IS NULL
                       AND nr_ordem                                >= ".intval($args['nr_ordem'])."
                       AND cd_regulamento_revisao                  != ".intval($cd_regulamento_revisao)."
                       ".(intval($args['cd_regulamento_revisao_pai']) > 0 ? "AND cd_regulamento_revisao_pai = ".intval($args['cd_regulamento_revisao_pai']) : "")."
                     ORDER BY nr_ordem) x
             WHERE t.cd_regulamento_revisao = x.cd_regulamento_revisao;";

		$this->db->query($qr_sql); 
    }

    public function remover($cd_regulamento_revisao, $cd_usuario)
    {
		$qr_sql = "
			UPDATE gestao.regulamento_revisao
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_regulamento_revisao = ".intval($cd_regulamento_revisao).";";

        $this->db->query($qr_sql);
    }
}