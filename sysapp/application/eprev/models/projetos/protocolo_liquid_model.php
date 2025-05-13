<?php
class Protocolo_liquid_model extends Model
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

	public function get_protocolo_gerencia($cd_protocolo_liquid_ficha)
    {
        $qr_sql = "
            SELECT cd_gerencia
              FROM projetos.protocolo_liquid_ficha_gerencia
             WHERE cd_protocolo_liquid_ficha = ".intval($cd_protocolo_liquid_ficha)."
               AND dt_exclusao IS NULL
             ORDER BY cd_gerencia ASC;";
        
        return $this->db->query($qr_sql)->result_array();
    }

    public function listar($args = array())
	{
		$qr_sql = "
			SELECT pl.cd_protocolo_liquid_ficha,
                   pl.nr_protocolo_liquid_ficha,
                   pl.ds_protocolo_liquid_ficha,
                   pl.ds_descricao 
              FROM projetos.protocolo_liquid_ficha pl
             WHERE pl.dt_exclusao IS NULL
             ".(trim($args['cd_gerencia']) != '' ? "AND (SELECT COUNT(*)
                                                           FROM projetos.protocolo_liquid_ficha_gerencia plg
                                                          WHERE plg.dt_exclusao IS NULL
                                                            AND pl.cd_protocolo_liquid_ficha = plg.cd_protocolo_liquid_ficha 
                                                            AND plg.cd_gerencia = ".str_escape($args['cd_gerencia']).") > 0" : "").";";

        return $this->db->query($qr_sql)->result_array();
	}

	public function salvar($args = array())
	{
		$cd_protocolo_liquid_ficha = $this->db->get_new_id('projetos.protocolo_liquid_ficha', 'cd_protocolo_liquid_ficha');

		$qr_sql = "
			INSERT INTO projetos.protocolo_liquid_ficha
			    (
			       cd_protocolo_liquid_ficha,
                   nr_protocolo_liquid_ficha,
			       ds_protocolo_liquid_ficha,
			       ds_descricao,
                   cd_usuario_inclusao,
			       cd_usuario_alteracao
			    )
		   VALUES
			    (
			        ".intval($cd_protocolo_liquid_ficha).",
                    ".(intval($args['nr_protocolo_liquid_ficha']) > 0 ? intval($args['nr_protocolo_liquid_ficha']) : "DEFAULT").",
			     	".(trim($args['ds_protocolo_liquid_ficha']) != '' ? str_escape($args['ds_protocolo_liquid_ficha']) : "DEFAULT").",
			     	".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
			     	".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
			    );";

			if(count($args['protocolo_gerencia']) > 0)
            {
                $qr_sql .= "
                    INSERT INTO projetos.protocolo_liquid_ficha_gerencia 
                    (
                        cd_protocolo_liquid_ficha,
                        cd_gerencia, 
                        cd_usuario_inclusao,
                        cd_usuario_alteracao 
                    )
                    SELECT ".intval($cd_protocolo_liquid_ficha).", x.column1, ".intval($args['cd_usuario']).",".intval($args['cd_usuario'])."
                      FROM (VALUES ('".implode("'),('", $args['protocolo_gerencia'])."')) x ;";
            }

		$this->db->query($qr_sql); 
	}

	public function atualizar($cd_protocolo_liquid_ficha, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.protocolo_liquid_ficha
               SET ds_protocolo_liquid_ficha  = ".(trim($args['ds_protocolo_liquid_ficha']) != '' ? str_escape($args['ds_protocolo_liquid_ficha']) : "DEFAULT").",
                   ds_descricao         = ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_protocolo_liquid_ficha = ".intval($cd_protocolo_liquid_ficha).";";

        if(count($args['protocolo_gerencia']) > 0)
        {
            $qr_sql .= "
                UPDATE projetos.protocolo_liquid_ficha_gerencia 
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_protocolo_liquid_ficha = ".intval($cd_protocolo_liquid_ficha)."
                   AND dt_exclusao IS NULL
                   AND cd_gerencia NOT IN ('".implode("','", $args['protocolo_gerencia'])."');
       
                INSERT INTO projetos.protocolo_liquid_ficha_gerencia 
                (
                    cd_protocolo_liquid_ficha,
                    cd_gerencia, 
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
                SELECT ".intval($cd_protocolo_liquid_ficha).", x.column1, ".intval($args['cd_usuario']).",".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['protocolo_gerencia'])."')) x
                 WHERE x.column1 NOT IN (
                                        SELECT a.cd_gerencia
                                          FROM projetos.protocolo_liquid_ficha_gerencia  a
                                         WHERE a.cd_protocolo_liquid_ficha = ".intval($cd_protocolo_liquid_ficha)."
                                           AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE projetos.protocolo_liquid_ficha_gerencia 
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_protocolo_liquid_ficha_gerencia = ".intval($args['cd_protocolo_liquid_ficha_gerencia'])."
                   AND dt_exclusao IS NULL;";
        }

        $this->db->query($qr_sql);
    }

    public function carrega($cd_protocolo_liquid_ficha)
	{
		$qr_sql = "
			SELECT pl.cd_protocolo_liquid_ficha,
                   pl.nr_protocolo_liquid_ficha,
  				   pl.ds_protocolo_liquid_ficha,
  				   pl.ds_descricao 
  		      FROM projetos.protocolo_liquid_ficha pl
			 WHERE pl.dt_exclusao IS NULL
               AND pl.cd_protocolo_liquid_ficha = ".intval($cd_protocolo_liquid_ficha).";";
		
        return $this->db->query($qr_sql)->row_array();
	}

    public function get_ficha_gerencia($cd_gerencia)
    {
        $qr_sql = "
            SELECT pl.cd_protocolo_liquid_ficha AS value,
                   pl.ds_protocolo_liquid_ficha AS text
              FROM projetos.protocolo_liquid_ficha pl
             WHERE pl.dt_exclusao IS NULL
               AND (SELECT COUNT(*)
                      FROM projetos.protocolo_liquid_ficha_gerencia plg
                     WHERE plg.dt_exclusao IS NULL
                       AND pl.cd_protocolo_liquid_ficha = plg.cd_protocolo_liquid_ficha 
                       AND plg.cd_gerencia = ".str_escape($cd_gerencia).") > 0
             ORDER BY pl.ds_protocolo_liquid_ficha;";

        return $this->db->query($qr_sql)->result_array();
    }
}
?>