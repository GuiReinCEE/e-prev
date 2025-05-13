<?php
class Ficha_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

    public function get_gerencia()
	{
		$qr_sql = "
			SELECT codigo AS value,
			       nome AS text 
			  FROM funcoes.get_gerencias_vigente();";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_ficha_gerencia($cd_ficha)
    {
        $qr_sql = "
            SELECT cd_gerencia
              FROM liquid.ficha_gerencia
             WHERE cd_ficha = ".intval($cd_ficha)."
               AND dt_exclusao IS NULL
             ORDER BY cd_gerencia ASC;";
        
        return $this->db->query($qr_sql)->result_array();
    }

    public function listar($args = array())
	{
		$qr_sql = "
			SELECT f.cd_ficha,
                   f.nr_ficha,
                   f.ds_ficha,
                   f.ds_caminho 
              FROM liquid.ficha f
             WHERE f.dt_exclusao IS NULL
             ".(trim($args['cd_gerencia']) != '' ? "AND (SELECT COUNT(*)
                                                           FROM liquid.ficha_gerencia fg
                                                          WHERE fg.dt_exclusao IS NULL
                                                            AND f.cd_ficha     = fg.cd_ficha 
                                                            AND fg.cd_gerencia = ".str_escape($args['cd_gerencia']).") > 0" : "").";";

        return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_ficha)
	{
		$qr_sql = "
			SELECT f.cd_ficha,
                   f.nr_ficha,
  				   f.ds_ficha,
  				   f.ds_caminho 
  		      FROM liquid.ficha f
			 WHERE f.dt_exclusao IS NULL
               AND f.cd_ficha = ".intval($cd_ficha).";";
		
        return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_ficha = $this->db->get_new_id('liquid.ficha', 'cd_ficha');

		$qr_sql = "
			INSERT INTO liquid.ficha
			    (
			       cd_ficha,
                   nr_ficha,
			       ds_ficha,
			       ds_caminho,
                   cd_usuario_inclusao,
			       cd_usuario_alteracao
			    )
		   VALUES
			    (
			        ".intval($cd_ficha).",
                    ".(intval($args['nr_ficha']) > 0 ? intval($args['nr_ficha']) : "DEFAULT").",
			     	".(trim($args['ds_ficha']) != '' ? str_escape($args['ds_ficha']) : "DEFAULT").",
			     	".(trim($args['ds_caminho']) != '' ? str_escape($args['ds_caminho']) : "DEFAULT").",
			     	".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
			    );";

			if(count($args['ficha_gerencia']) > 0)
            {
                $qr_sql .= "
                    INSERT INTO liquid.ficha_gerencia 
                    (
                        cd_ficha,
                        cd_gerencia, 
                        cd_usuario_inclusao,
                        cd_usuario_alteracao 
                    )
                    SELECT ".intval($cd_ficha).", x.column1, ".intval($args['cd_usuario']).",".intval($args['cd_usuario'])."
                      FROM (VALUES ('".implode("'),('", $args['ficha_gerencia'])."')) x ;";
            }

		$this->db->query($qr_sql); 
	}

	public function atualizar($cd_ficha, $args = array())
    {
        $qr_sql = "
            UPDATE liquid.ficha
               SET ds_ficha             = ".(trim($args['ds_ficha']) != '' ? str_escape($args['ds_ficha']) : "DEFAULT").",
                   ds_caminho           = ".(trim($args['ds_caminho']) != '' ? str_escape($args['ds_caminho']) : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_ficha = ".intval($cd_ficha).";";

        if(count($args['ficha_gerencia']) > 0)
        {
            $qr_sql .= "
                UPDATE liquid.ficha_gerencia 
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_ficha = ".intval($cd_ficha)."
                   AND dt_exclusao IS NULL
                   AND cd_gerencia NOT IN ('".implode("','", $args['ficha_gerencia'])."');
       
                INSERT INTO liquid.ficha_gerencia 
                (
                    cd_ficha,
                    cd_gerencia, 
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
                SELECT ".intval($cd_ficha).", x.column1, ".intval($args['cd_usuario']).",".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['ficha_gerencia'])."')) x
                 WHERE x.column1 NOT IN (
                                        SELECT a.cd_gerencia
                                          FROM liquid.ficha_gerencia  a
                                         WHERE a.cd_ficha = ".intval($cd_ficha)."
                                           AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE liquid.ficha_gerencia 
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_ficha_gerencia = ".intval($args['cd_ficha_gerencia'])."
                   AND dt_exclusao IS NULL;";
        }

        $this->db->query($qr_sql);
    }
}