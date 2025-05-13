<?php
class Documento_plano_model extends Model
{
    function __construct()
    {
		parent::Model();
	}

	public function get_tipo_documento()
   	{
   		$qr_sql = "
   			SELECT cd_documento_plano_tipo AS value,
   				   ds_documento_plano_tipo AS text
   			  FROM autoatendimento.documento_plano_tipo
   			 WHERE dt_exclusao IS NULL;";

   		return $this->db->query($qr_sql)->result_array();
   	}

	public function get_documento_plano()
	{
		$qr_sql = "
			SELECT dc.cd_documento_plano AS value,
			       (CASE WHEN dc.cd_empresa IS NOT NULL THEN (SELECT p.sigla FROM funcoes.get_patrocinadora(dc.cd_empresa) p)  || ' - PLANO ' || funcoes.get_plano_nome(dc.cd_plano) 
			             ELSE funcoes.get_plano_nome(dc.cd_plano)
			       END) AS text
			  FROM autoatendimento.documento_plano dc
			 WHERE dc.dt_exclusao IS NULL
			 ORDER BY text ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function listar($args = array())
    {
    	$qr_sql = "
      		SELECT dc.cd_documento_plano,
			       (CASE WHEN dc.cd_empresa IS NOT NULL THEN (SELECT p.sigla FROM funcoes.get_patrocinadora(dc.cd_empresa) p)  || ' - PLANO ' || funcoes.get_plano_nome(dc.cd_plano) 
			             ELSE funcoes.get_plano_nome(dc.cd_plano)
			       END) AS ds_documento_plano
              FROM autoatendimento.documento_plano dc
             WHERE dc.dt_exclusao IS NULL
               ".(intval($args['cd_documento_plano']) > 0 ? "AND dc.cd_documento_plano = ".intval($args['cd_documento_plano']) : "")."
			 ORDER BY cd_documento_plano ASC;";

  		return $this->db->query($qr_sql)->result_array();
    }

    public function get_documento_plano_tipo($cd_documento_plano)
    {
    	$qr_sql = "
			SELECT DISTINCT dst.ds_documento_plano_tipo,
			       dst.cd_documento_plano_tipo
			  FROM autoatendimento.documento_plano_tipo dst
			  JOIN autoatendimento.documento_plano_arquivo dsa
                ON dst.cd_documento_plano_tipo = dsa.cd_documento_plano_tipo 
             WHERE dsa.dt_exclusao        IS NULL
               AND dsa.cd_documento_plano = ".intval($cd_documento_plano).";";

	    return $this->db->query($qr_sql)->result_array();
    }

    public function get_documento_plano_arquivo($cd_documento_plano, $cd_documento_plano_tipo, $limit = true)
    {
    	$qr_sql = "
			SELECT dsa.cd_documento_plano_arquivo,
			       dsa.cd_documento_plano,
			       dst.cd_documento_plano_tipo,
			       dst.ds_documento_plano_tipo,
			       dsa.arquivo,
			       dsa.arquivo_nome,
			       dsa.cd_documento_plano_arquivo,
			       TO_CHAR(dsa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       funcoes.get_usuario_nome(dsa.cd_usuario_inclusao) AS ds_usuario_inclusao,
			       (SELECT COUNT(*) 
                      FROM autoatendimento.documento_plano_arquivo dsa1
                     WHERE dsa1.dt_exclusao IS NULL
                       AND dsa1.cd_documento_plano_tipo = dsa.cd_documento_plano_tipo
                       AND dsa1.cd_documento_plano      = dsa.cd_documento_plano
                   ) AS tl_documento
			  FROM autoatendimento.documento_plano_arquivo dsa
			  JOIN autoatendimento.documento_plano_tipo dst
                ON dst.cd_documento_plano_tipo = dsa.cd_documento_plano_tipo 
             WHERE dsa.dt_exclusao            IS NULL
               AND dsa.cd_documento_plano      = ".intval($cd_documento_plano)."
               AND dsa.cd_documento_plano_tipo = ".intval($cd_documento_plano_tipo)."
             ORDER BY dsa.dt_inclusao DESC
             ".($limit ? "LIMIT 1" : "").";";
     
        if($limit)
        {
        	return $this->db->query($qr_sql)->row_array();
        }
        else
        {
        	return $this->db->query($qr_sql)->result_array();
        }
    }
	
    public function carrega($cd_documento_plano)
    {
    	$qr_sql = "
      		SELECT dc.cd_documento_plano,
			       (CASE WHEN dc.cd_empresa IS NOT NULL THEN (SELECT p.sigla FROM funcoes.get_patrocinadora(dc.cd_empresa) p)  || ' - PLANO ' || funcoes.get_plano_nome(dc.cd_plano) 
			             ELSE funcoes.get_plano_nome(dc.cd_plano)
			       END) AS ds_documento_plano
              FROM autoatendimento.documento_plano dc
             WHERE dc.dt_exclusao IS NULL
               AND dc.cd_documento_plano = ".intval($cd_documento_plano)."
			 ORDER BY ds_documento_plano ASC;";

  		return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_arquivo($args = array())
    {
    	$qr_sql = "
    		INSERT INTO autoatendimento.documento_plano_arquivo
	    		(
	    			cd_documento_plano,
	    			cd_documento_plano_tipo,
	    			arquivo_nome,
	    			arquivo,
	    			cd_usuario_inclusao,
	    			cd_usuario_alteracao
	    		)
	       VALUES
	    		(
	    			".intval($args['cd_documento_plano']).",
	    			".(trim($args['cd_documento_plano_tipo']) != '' ? intval($args['cd_documento_plano_tipo']) : "DEFAULT").",
	    			".str_escape($args['arquivo']).",
                 	".str_escape($args['arquivo_nome']).",
	    			".intval($args['cd_usuario']).",
	    			".intval($args['cd_usuario'])."
	    		);";

	    $this->db->query($qr_sql);
    }

    public function excluir($cd_documento_plano_arquivo, $cd_usuario)
    {
    	$qr_sql = "
	        UPDATE autoatendimento.documento_plano_arquivo
	           SET cd_usuario_exclusao = ".intval($cd_usuario).",
	               dt_exclusao         = CURRENT_TIMESTAMP
	         WHERE cd_documento_plano_arquivo = ".intval($cd_documento_plano_arquivo).";";

      	$this->db->query($qr_sql);
    }

    public function get_tipo_documento_nome($cd_documento_plano_tipo)
    {
    	$qr_sql = "
      		SELECT ds_documento_plano_tipo 
   			  FROM autoatendimento.documento_plano_tipo
   			 WHERE dt_exclusao             IS NULL
   			   AND cd_documento_plano_tipo = ".intval($cd_documento_plano_tipo).";";

  		return $this->db->query($qr_sql)->row_array();
    }
}