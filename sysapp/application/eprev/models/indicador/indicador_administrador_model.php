<?php
class Indicador_administrador_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function get_gerencia()
	{
		$qr_sql = "
			SELECT codigo AS value,
                   codigo || ' - ' || nome AS text
              FROM funcoes.get_gerencias_vigente();";

        return $this->db->query($qr_sql)->result_array();
	}

	public function indicador_grupo()
	{
		$qr_sql = "
			SELECT cd_indicador_grupo AS value,
                   ds_indicador_grupo AS text
              FROM indicador.indicador_grupo
             WHERE dt_exclusao IS NULL
             ORDER BY ds_indicador_grupo;";

		return $this->db->query($qr_sql)->result_array();
	}	

	public function listar($args = array())
	{
		$qr_sql = " 
			SELECT a.cd_indicador_administrador,
			       funcoes.get_usuario_nome(a.cd_usuario) AS ds_usuario,
			       funcoes.get_usuario_area(a.cd_usuario) AS cd_gerencia	   
              FROM indicador.indicador_administrador a
             WHERE a.ds_tipo     = 'RESPONSAVEL'
               AND a.dt_exclusao IS NULL
               ".(trim($args['cd_gerencia']) != '' ? "AND funcoes.get_usuario_area(a.cd_usuario) = '".trim($args['cd_gerencia'])."'" : "")."
               ".(trim($args['cd_indicador_grupo']) > 0 ? "AND (SELECT COUNT(*)
               	                                                  FROM indicador.indicador_administrador_grupo g
               	                                                 WHERE g.dt_exclusao IS NULL
               	                                                   AND g.cd_indicador_administrador = a.cd_indicador_administrador
               	                                                   AND g.cd_indicador_grupo = ".intval($args['cd_indicador_grupo']).") > 0" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function administrador_indicador_grupo($cd_indicador_administrador)
	{
		$qr_sql = "
			SELECT ig.cd_indicador_grupo,
			       ig.ds_indicador_grupo
              FROM indicador.indicador_grupo ig
			  JOIN indicador.indicador_administrador_grupo iag
			    ON iag.cd_indicador_grupo = ig.cd_indicador_grupo
			   AND iag.dt_exclusao IS NULL
		      JOIN indicador.indicador_administrador ia
			    ON ia.cd_indicador_administrador = iag.cd_indicador_administrador
			   AND ia.dt_exclusao IS NULL
             WHERE ig.dt_exclusao IS NULL
               AND ia.cd_indicador_administrador = ".intval($cd_indicador_administrador).";";

		return $this->db->query($qr_sql)->result_array();
	}	

	public function carrega($cd_indicador_administrador)
	{
		$qr_sql = "
			SELECT ia.cd_indicador_administrador,
			       ia.cd_usuario,
			       ia.ds_tipo,
			       funcoes.get_usuario_area(ia.cd_usuario) AS cd_gerencia,
			       funcoes.get_usuario_nome(ia.cd_usuario) AS ds_usuario,
			       funcoes.get_usuario_area(ia.cd_usuario) || ' - ' || d.nome AS gerencia
			  FROM indicador.indicador_administrador ia
			  JOIN projetos.divisoes d
			    ON d.codigo = funcoes.get_usuario_area(ia.cd_usuario)
			 WHERE ia.cd_indicador_administrador = ".intval($cd_indicador_administrador).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_usuarios($cd_gerencia, $cd_usuario = 0)
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_usuario_gerencia('".trim($cd_gerencia)."')
             WHERE codigo NOT IN (SELECT cd_usuario 
                                    FROM indicador.indicador_administrador
                                   WHERE dt_exclusao IS NULL
                                     AND cd_usuario != ".intval($cd_usuario).")";

        return $this->db->query($qr_sql)->result_array();
    }

	public function salvar($args = array())
	{
		$cd_indicador_administrador = $this->db->get_new_id('indicador.indicador_administrador', 'cd_indicador_administrador');

		$qr_sql = "
			INSERT INTO indicador.indicador_administrador
				 (
				   cd_indicador_administrador,
				   cd_usuario
				 )
			VALUES 
				 (
				   ".intval($cd_indicador_administrador).",
				   ".intval($args['cd_usuario'])."
				 );";

		if(count($args['grupo']) > 0)
        {
    		$qr_sql .= "
				INSERT INTO indicador.indicador_administrador_grupo(cd_indicador_administrador, cd_indicador_grupo, cd_usuario_inclusao)
				SELECT ".intval($cd_indicador_administrador).", x.column1, ".intval($args['cd_usuario_inclusao'])."
				  FROM (VALUES (".implode("),(", $args['grupo']).")) x;";
        }

        $this->db->query($qr_sql);
	}

	public function atualizar($cd_indicador_administrador, $args = array())
	{
		$qr_sql = "
			UPDATE indicador.indicador_administrador_grupo
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args['cd_usuario_inclusao'])."
			 WHERE cd_indicador_administrador = ".intval($cd_indicador_administrador)."
			   AND cd_indicador_grupo NOT IN (".implode(",",$args['grupo']).");

			INSERT INTO indicador.indicador_administrador_grupo(cd_indicador_administrador, cd_indicador_grupo, cd_usuario_inclusao)
			SELECT ".intval($cd_indicador_administrador).", x.column1, ".intval($args['cd_usuario_inclusao'])."
			  FROM (VALUES (".implode("),(", $args['grupo']).")) x
			 WHERE x.column1 NOT IN (SELECT a.cd_indicador_grupo
									   FROM indicador.indicador_administrador_grupo a
									  WHERE a.cd_indicador_administrador = ".intval($cd_indicador_administrador)."
										AND a.dt_exclusao IS NULL);";

		$this->db->query($qr_sql);
	}
	
    public function excluir($cd_indicador_administrador, $cd_usuario)
    {
        $qr_sql = "
			UPDATE indicador.indicador_administrador
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
				   cd_usuario_exclusao = ".intval($cd_usuario)."
			 WHERE cd_indicador_administrador = ".intval($cd_indicador_administrador).";
			 
			UPDATE indicador.indicador_administrador_grupo
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
				   cd_usuario_exclusao = ".intval($cd_usuario)."
			 WHERE cd_indicador_administrador = ".intval($cd_indicador_administrador).";";

        $this->db->query($qr_sql);
    }
}
?>