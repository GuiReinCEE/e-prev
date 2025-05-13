<?php
class Dominio_tipo_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function gerencia_unidade()
    {
    	$qr_sql = "
    		SELECT nome AS text,
       			   codigo as value
    		  FROM funcoes.get_usuario_gerencia_unidade('GTI')";
		return $this->db->query($qr_sql)->result_array();
    }

    public function listar($args = array())
    {
    	$qr_sql = "
    		SELECT a.cd_dominio_tipo,
    			   a.ds_dominio_tipo,
    			   a.nr_dias,
    			   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
			       funcoes.get_usuario_nome(a.cd_usuario_responsavel) AS ds_responsavel,
			       funcoes.get_usuario_nome(a.cd_usuario_substituto) AS ds_substituto
			  FROM informatica.dominio_tipo a 
			 WHERE a.dt_exclusao IS NULL
			   ".(trim($args['cd_usuario_responsavel']) != '' ? "AND a.cd_usuario_responsavel = ".intval($args['cd_usuario_responsavel']) : '')."
			   ".(trim($args['cd_usuario_substituto']) != '' ? "AND a.cd_usuario_substituto = ".intval($args['cd_usuario_substituto']) : '').";";

		return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_dominio_tipo)
    {
    	 $qr_sql = "
            SELECT a.cd_dominio_tipo,
    			   a.ds_dominio_tipo, 
			       a.cd_usuario_responsavel,
			       a.cd_usuario_substituto ,
			       a.nr_dias
			  FROM informatica.dominio_tipo a 
			 WHERE a.dt_exclusao IS NULL
               AND a.cd_dominio_tipo = ".intval($cd_dominio_tipo).";";
     
        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
    	$cd_dominio_tipo = intval($this->db->get_new_id('informatica.dominio_tipo', 'cd_dominio_tipo'));

    	$qr_sql = "
    	    INSERT INTO informatica.dominio_tipo
				(
				    cd_dominio_tipo, 
				    ds_dominio_tipo, 
				    cd_usuario_responsavel,
				    cd_usuario_substituto,
				    nr_dias,
				    cd_usuario_inclusao, 
			        cd_usuario_alteracao
				)
			    VALUES
			    (
			    	".intval($cd_dominio_tipo).",
			    	".(trim($args['ds_dominio_tipo']) != '' ? str_escape($args['ds_dominio_tipo']) : 'DEFAULT').",
			    	".(intval($args['cd_usuario_responsavel']) != ''? intval($args['cd_usuario_responsavel']) : 'DEFAULT').",
			    	".(intval($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : 'DEFAULT').",
			    	".(intval($args['nr_dias']) != '' ? intval($args['nr_dias']) : 'DEFAULT').",
			    	".intval($args['cd_usuario']).",
                	".intval($args['cd_usuario'])."
			    );";

		$this->db->query($qr_sql);

        return $cd_dominio_tipo;
	}

	public function atualizar($cd_dominio_tipo, $args = array())
	{
		$qr_sql = "
			UPDATE informatica.dominio_tipo
   			   	SET ds_dominio_tipo        = ".(trim($args['ds_dominio_tipo']) != '' ? str_escape($args['ds_dominio_tipo']) : 'DEFAULT').", 
   			   		cd_usuario_responsavel = ".(intval($args['cd_usuario_responsavel']) != ''? intval($args['cd_usuario_responsavel']) : 'DEFAULT').",
			        cd_usuario_substituto  = ".(intval($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : 'DEFAULT').",
			        nr_dias                = ".(intval($args['nr_dias']) != '' ? intval($args['nr_dias']) : 'DEFAULT').",
			        cd_usuario_alteracao   = ".intval($args['cd_usuario']).",
                    dt_alteracao           = CURRENT_TIMESTAMP			       
			    WHERE cd_dominio_tipo = ".intval($cd_dominio_tipo).";";
			  
       $this->db->query($qr_sql);
	}
}
?>