<?php
class Solic_fiscalizacao_audit_tipo_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

    public function listar($args = array())
	{
		$qr_sql = "
         	SELECT t.cd_solic_fiscalizacao_audit_tipo,
         	       t.ds_solic_fiscalizacao_audit_tipo,
         	       ta.ds_solic_fiscalizacao_audit_tipo_agrupamento,
         	       t.cd_gerencia,
                   (CASE WHEN t.fl_especificar = 'S' 
                         THEN 'Sim'        
                         ELSE 'Não'
                   END) AS ds_especificar,
                   (CASE WHEN t.fl_especificar = 'S' 
                         THEN 'success'        
                         ELSE 'info'
                   END) AS ds_class_especificar
		      FROM projetos.solic_fiscalizacao_audit_tipo t
		      JOIN projetos.solic_fiscalizacao_audit_tipo_agrupamento ta
		        ON ta.cd_solic_fiscalizacao_audit_tipo_agrupamento = t.cd_solic_fiscalizacao_audit_tipo_agrupamento
			 WHERE t.dt_exclusao IS NULL
	     	   ".(trim($args['fl_especificar']) != '' ? "AND fl_especificar = '".trim($args['fl_especificar'])."'" : "")."
			   ".(trim($args['ds_solic_fiscalizacao_audit_tipo']) != '' ? "AND UPPER(funcoes.remove_acento(t.ds_solic_fiscalizacao_audit_tipo)) LIKE UPPER(funcoes.remove_acento('%".trim($args['ds_solic_fiscalizacao_audit_tipo'])."%'))" : "")."
			   ".(intval($args['cd_solic_fiscalizacao_audit_tipo_agrupamento']) > 0 ? "AND t.cd_solic_fiscalizacao_audit_tipo_agrupamento = ".intval($args['cd_solic_fiscalizacao_audit_tipo_agrupamento']) : "").";";

		return $this->db->query($qr_sql)->result_array();	
	}

	public function carrega($cd_solic_fiscalizacao_audit_tipo)
	{
		$qr_sql = "
			SELECT cd_solic_fiscalizacao_audit_tipo,
         	       ds_solic_fiscalizacao_audit_tipo,
         	       cd_solic_fiscalizacao_audit_tipo_agrupamento,
         	       cd_gerencia, 
         	       fl_especificar
			  FROM projetos.solic_fiscalizacao_audit_tipo
			 WHERE cd_solic_fiscalizacao_audit_tipo = ".intval($cd_solic_fiscalizacao_audit_tipo).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_solic_fiscalizacao_audit_tipo = intval($this->db->get_new_id('projetos.solic_fiscalizacao_audit_tipo', 'cd_solic_fiscalizacao_audit_tipo'));

		$qr_sql = "
			INSERT INTO projetos.solic_fiscalizacao_audit_tipo
			     (
			       cd_solic_fiscalizacao_audit_tipo,
			       ds_solic_fiscalizacao_audit_tipo,
			       cd_solic_fiscalizacao_audit_tipo_agrupamento,
			       cd_gerencia,
			       fl_especificar,
			       cd_usuario_inclusao,
               	   cd_usuario_alteracao
			     )
			VALUES
			     (
			        ".intval($cd_solic_fiscalizacao_audit_tipo).",
			     	".(trim($args['ds_solic_fiscalizacao_audit_tipo']) != '' ? str_escape($args['ds_solic_fiscalizacao_audit_tipo']) : "DEFAULT").",
                    ".(intval($args['cd_solic_fiscalizacao_audit_tipo_agrupamento']) > 0 ? intval($args['cd_solic_fiscalizacao_audit_tipo_agrupamento']) : "DEFAULT").",
                    ".(trim($args['cd_gerencia']) != '' ? "'".trim($args['cd_gerencia'])."'" : "DEFAULT").",                   
                    ".(trim($args['fl_especificar']) != '' ? "'".trim($args['fl_especificar'])."'" : "DEFAULT").",                   
                    ".intval($args['cd_usuario']).", 
                    ".intval($args['cd_usuario'])." 
			     );";

		if(count($args['tipo_gerencia']) > 0)
        {
            $qr_sql .= "
                INSERT INTO projetos.solic_fiscalizacao_audit_tipo_gerencia(cd_solic_fiscalizacao_audit_tipo, cd_gerencia, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_solic_fiscalizacao_audit_tipo).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['tipo_gerencia'])."')) x;";
        }

		$this->db->query($qr_sql); 
	}

	public function atualizar($cd_solic_fiscalizacao_audit_tipo, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.solic_fiscalizacao_audit_tipo
			   SET ds_solic_fiscalizacao_audit_tipo             = ".(trim($args['ds_solic_fiscalizacao_audit_tipo']) != '' ? str_escape($args['ds_solic_fiscalizacao_audit_tipo']) : "DEFAULT").",
			       cd_solic_fiscalizacao_audit_tipo_agrupamento = ".(intval($args['cd_solic_fiscalizacao_audit_tipo_agrupamento']) > 0 ? intval($args['cd_solic_fiscalizacao_audit_tipo_agrupamento']) : "DEFAULT").",
			       cd_gerencia             	                    = ".(trim($args['cd_gerencia']) != '' ? "'".trim($args['cd_gerencia'])."'" : "DEFAULT").", 
			       fl_especificar                               = ".(trim($args['fl_especificar']) != '' ? "'".trim($args['fl_especificar'])."'" : "DEFAULT").",
               	   cd_usuario_alteracao                         = ".intval($args['cd_usuario']).", 
			       dt_alteracao                                 =  CURRENT_TIMESTAMP                   
             WHERE cd_solic_fiscalizacao_audit_tipo = ".intval($cd_solic_fiscalizacao_audit_tipo).";";

        if(count($args['tipo_gerencia']) > 0)
        {
            $qr_sql .= "
                UPDATE projetos.solic_fiscalizacao_audit_tipo_gerencia
                   SET cd_usuario_exclusao              = ".intval($args['cd_usuario']).",
                       dt_exclusao                      = CURRENT_TIMESTAMP
                 WHERE cd_solic_fiscalizacao_audit_tipo = ".intval($cd_solic_fiscalizacao_audit_tipo)."
                   AND dt_exclusao IS NULL
                   AND cd_gerencia NOT IN ('".implode("','", $args['tipo_gerencia'])."');
       
                INSERT INTO projetos.solic_fiscalizacao_audit_tipo_gerencia(cd_solic_fiscalizacao_audit_tipo, cd_gerencia, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_solic_fiscalizacao_audit_tipo).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['tipo_gerencia'])."')) x
                 WHERE x.column1 NOT IN (SELECT a.cd_gerencia
                                           FROM projetos.solic_fiscalizacao_audit_tipo_gerencia a
                                          WHERE a.cd_solic_fiscalizacao_audit_tipo = ".intval($cd_solic_fiscalizacao_audit_tipo)."
                                            AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE projetos.solic_fiscalizacao_audit_tipo_gerencia
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_solic_fiscalizacao_audit_tipo = ".intval($cd_solic_fiscalizacao_audit_tipo)."
                   AND dt_exclusao IS NULL;";
        }

        $this->db->query($qr_sql);  
	}

	public function get_gerencia($tipo = array())
	{
		$qr_sql = " 
			SELECT codigo AS value,
			       nome AS text
			  FROM funcoes.get_gerencias_vigente(".(count($tipo) > 0 ? "'".implode(', ', $tipo)."'" : "").");";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_tipo_gerencia($cd_solic_fiscalizacao_audit_tipo)
	{
		$qr_sql = "
			SELECT cd_solic_fiscalizacao_audit_tipo_gerencia,
			       cd_gerencia
			  FROM projetos.solic_fiscalizacao_audit_tipo_gerencia
			 WHERE dt_exclusao                      IS NULL
			   AND cd_solic_fiscalizacao_audit_tipo = ".intval($cd_solic_fiscalizacao_audit_tipo)."
			 ORDER BY cd_gerencia;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_agrupamento()
	{
		$qr_sql = "
			SELECT cd_solic_fiscalizacao_audit_tipo_agrupamento AS value,
			       ds_solic_fiscalizacao_audit_tipo_agrupamento AS text
			  FROM projetos.solic_fiscalizacao_audit_tipo_agrupamento
			 WHERE dt_exclusao IS NULL
			 ORDER BY cd_solic_fiscalizacao_audit_tipo_agrupamento;";

		return $this->db->query($qr_sql)->result_array();
	}
}