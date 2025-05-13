<?php
class Pendencia_query_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

   	public function listar($args = array())
	{
		$qr_sql = "
			SELECT pmq.cd_pendencia_minha_query,
	               pmq.cd_pendencia_minha,
	               pmq.ds_descricao,
	               pmq.ds_pendencia_minha_query,
	               pm.ds_pendencia_minha, 
                   (CASE WHEN pmq.fl_superior = 'S' 
                        THEN 'Sim'        
                        ELSE 'Não'
                   END) AS ds_superior                                    
			  FROM gestao.pendencia_minha_query pmq
			  JOIN gestao.pendencia_minha pm
			    ON pm.cd_pendencia_minha = pmq.cd_pendencia_minha
	         WHERE pmq.dt_exclusao IS NULL
	           AND pm.dt_exclusao IS NULL
		       ".(trim($args['cd_pendencia_minha']) != '' ? "AND pmq.cd_pendencia_minha = '".trim($args['cd_pendencia_minha'])."'" : "")."
		       ".(trim($args['fl_superior']) != '' ? "AND pmq.fl_superior = '".trim($args['fl_superior'])."'" : "")."
		     ORDER BY ds_pendencia_minha_query ASC;";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_pendencia_minha()
	{
		$qr_sql = "
			SELECT cd_pendencia_minha AS value,
                   cd_pendencia_minha ||' - '||ds_pendencia_minha AS text
			  FROM gestao.pendencia_minha
			 WHERE dt_exclusao IS NULL
             ORDER BY cd_pendencia_minha ASC;";

		return $this->db->query($qr_sql)->result_array();
	}	

	public function carrega($cd_pendencia_minha_query)
	{
		$qr_sql = "
			SELECT pmq.cd_pendencia_minha_query,
	               pmq.cd_pendencia_minha,
	               pmq.ds_descricao,	               
	               pmq.ds_pendencia_minha_query,
	               pmq.fl_superior
			  FROM gestao.pendencia_minha_query pmq
		     WHERE pmq.dt_exclusao IS NULL
		       AND pmq.cd_pendencia_minha_query = ".intval($cd_pendencia_minha_query).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_pendencia_minha_query = intval($this->db->get_new_id('gestao.pendencia_minha_query', 'cd_pendencia_minha_query'));

		$qr_sql = "
			INSERT INTO gestao.pendencia_minha_query
			     (
			       cd_pendencia_minha_query,
	               cd_pendencia_minha,
	               ds_descricao,
	               ds_pendencia_minha_query,
	               fl_superior,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
			       
			     )
			VALUES
			     (
			     	".intval($cd_pendencia_minha_query).",			     	
			     	".(trim($args['cd_pendencia_minha']) != '' ? str_escape($args['cd_pendencia_minha']) : "DEFAULT").",
			     	".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
			     	".(trim($args['ds_pendencia_minha_query']) != '' ? str_escape($args['ds_pendencia_minha_query']) : "DEFAULT").",
			     	".(trim($args['fl_superior']) != '' ?  "'".trim($args['fl_superior'])."'" : "DEFAULT").",
			        ".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
				    
			     );";

		$this->db->query($qr_sql); 

		return $cd_pendencia_minha_query;
	}

	public function atualizar($cd_pendencia_minha_query, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.pendencia_minha_query
               SET cd_pendencia_minha       = ".(trim($args['cd_pendencia_minha']) != '' ? str_escape($args['cd_pendencia_minha']) : "DEFAULT").",
	               ds_descricao             = ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
	               ds_pendencia_minha_query = ".(trim($args['ds_pendencia_minha_query']) != '' ? str_escape($args['ds_pendencia_minha_query']) : "DEFAULT").",
	                fl_superior              = ".(trim($args['fl_superior']) != '' ?   "'".trim($args['fl_superior'])."'" : "DEFAULT").",
			       cd_usuario_alteracao     = ".intval($args['cd_usuario']).",
                   dt_alteracao             = CURRENT_TIMESTAMP
            WHERE cd_pendencia_minha_query  = ".intval($cd_pendencia_minha_query).";";

        $this->db->query($qr_sql);  
	}
}
?>