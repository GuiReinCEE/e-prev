<?php
class Expediente_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

	public function listar($args=array())
    {
    	$qr_sql = "
			SELECT e.cd_expediente, 
			       e.nr_expediente, 
				   e.ds_descricao, 
				   e.cd_expediente_origem, 
				   TO_CHAR(e.dt_conclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_conclusao, 
				   e.cd_usuario_conclusao,						   
				   TO_CHAR(e.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
				   e.cd_usuario_inclusao, 
				   TO_CHAR(COALESCE((SELECT ea.dt_inclusao
									   FROM comite_etica.expediente_andamento ea
									  WHERE ea.cd_expediente = e.cd_expediente
									    AND ea.dt_exclusao IS NULL
									  ORDER BY ea.dt_inclusao DESC
									  LIMIT 1), e.dt_alteracao), 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
				   e.cd_usuario_alteracao, 
				   e.dt_exclusao, 
				   e.cd_usuario_exclusao,
				   eo.ds_expediente_origem,
				   (SELECT es.ds_expediente_status
				      FROM comite_etica.expediente_andamento ea
			          JOIN comite_etica.expediente_status es
			            ON es.cd_expediente_status = ea.cd_expediente_status								  
			         WHERE ea.cd_expediente = e.cd_expediente
					   AND ea.dt_exclusao IS NULL
				     ORDER BY ea.dt_inclusao DESC
				     LIMIT 1) AS ds_expediente_status
			  FROM comite_etica.expediente e
			  JOIN comite_etica.expediente_origem eo
			    ON eo.cd_expediente_origem = e.cd_expediente_origem
			 WHERE e.dt_exclusao IS NULL
				".(((trim($args['dt_ini']) != '') AND (trim($args['dt_fim']) != '')) ? "AND p.dt_inclusao::DATE BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "").";";

    	return $this->db->query($qr_sql)->result_array();
    }

	public function carrega($cd_expediente)
    {
    	$qr_sql = "
			SELECT e.cd_expediente, 
			       e.nr_expediente, 
				   e.ds_descricao,
				   e.cd_expediente_origem, 
				   TO_CHAR(e.dt_conclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_conclusao, 
				   e.cd_usuario_conclusao, 						   
				   TO_CHAR(e.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
				   e.cd_usuario_inclusao, 
				   e.dt_alteracao, 
				   TO_CHAR(COALESCE((SELECT ea.dt_inclusao
									   FROM comite_etica.expediente_andamento ea
									  WHERE ea.cd_expediente = e.cd_expediente
									    AND ea.dt_exclusao IS NULL
									  ORDER BY ea.dt_inclusao DESC
									  LIMIT 1), e.dt_alteracao), 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
				   e.dt_exclusao, 
				   e.cd_usuario_exclusao,
				   eo.ds_expediente_origem,
				   (SELECT es.ds_expediente_status
				      FROM comite_etica.expediente_andamento ea
			          JOIN comite_etica.expediente_status es
			            ON es.cd_expediente_status = ea.cd_expediente_status								  
			         WHERE ea.cd_expediente = e.cd_expediente
					   AND ea.dt_exclusao IS NULL
				     ORDER BY ea.dt_inclusao DESC
				     LIMIT 1) AS ds_expediente_status,
				    TO_CHAR(e.dt_envio_comite, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_comite
			  FROM comite_etica.expediente e
			  JOIN comite_etica.expediente_origem eo
			    ON eo.cd_expediente_origem = e.cd_expediente_origem
			 WHERE e.dt_exclusao IS NULL
			   AND e.cd_expediente = ".intval($cd_expediente).";";

    	return $this->db->query($qr_sql)->row_array();	
	}

	public function salvar($args = array())
    {
		$qr_sql = " 
			INSERT INTO comite_etica.expediente
			     (
					nr_expediente,          	
					ds_descricao,            	
					cd_expediente_origem,			
					cd_usuario_inclusao,
					cd_usuario_alteracao
				  )                     	
			 VALUES 
				  (                 	
					(SELECT comite_etica.expediente_numero()),
					".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").", 
					".(trim($args['cd_expediente_origem']) != '' ? intval($args['cd_expediente_origem']) : "DEFAULT").",
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				  );";
		
    	$this->db->query($qr_sql);
	}

	public function atualizar($cd_expediente, $args = array())
	{
		$qr_sql = "
			UPDATE comite_etica.expediente 
			   SET ds_descricao         = ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").", 	  
				   cd_expediente_origem = ".(trim($args['cd_expediente_origem']) != '' ? intval($args['cd_expediente_origem']) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE nr_expediente = ".intval($cd_expediente).";";

		$this->db->query($qr_sql);
	}
	
	public function concluir($cd_expediente, $cd_usuario)
    {
        $qr_sql = "
			UPDATE comite_etica.expediente 
			   SET cd_usuario_conclusao = ".intval($cd_usuario).",
				   dt_conclusao         = CURRENT_TIMESTAMP
			 WHERE cd_expediente = ".intval($cd_expediente).";";

		$this->db->query($qr_sql);
	}

	public function enviar_email($cd_expediente, $cd_usuario)
    {
        $qr_sql = "
			UPDATE comite_etica.expediente 
			   SET cd_usuario_envio_comite = ".intval($cd_usuario).",
				   dt_envio_comite         = CURRENT_TIMESTAMP
			 WHERE cd_expediente = ".intval($cd_expediente).";";

		$this->db->query($qr_sql);
	}

	public function andamento_listar($cd_expediente)
    {
    	$qr_sql = "
			SELECT ea.cd_expediente_andamento,
			       ea.ds_expediente_andamento,
				   TO_CHAR(ea.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
				   funcoes.get_usuario_nome(ea.cd_usuario_inclusao) AS ds_usuario,
				   es.ds_expediente_status
			  FROM comite_etica.expediente_andamento ea
			  JOIN comite_etica.expediente_status es
				ON es.cd_expediente_status = ea.cd_expediente_status					  
			 WHERE ea.dt_exclusao IS NULL
			   AND ea.cd_expediente = ".intval($cd_expediente)."
			 ORDER BY ea.dt_inclusao;";

    	return $this->db->query($qr_sql)->result_array();
	}	
	
	public function andamento_salvar($cd_expediente, $args = array())
    {
        $qr_sql = " 
			INSERT INTO comite_etica.expediente_andamento
			     (
					cd_expediente,
					ds_expediente_andamento,            	
					cd_expediente_status,			
					cd_usuario_inclusao
				 )                        	
			VALUES 
				 (                 	
					".intval($cd_expediente).",
					".(trim($args['ds_expediente_andamento']) != '' ? str_escape($args['ds_expediente_andamento']) :  "DEFAULT").", 
					".(trim($args['cd_expediente_status']) != '' ? intval($args['cd_expediente_status']) : "DEFAULT").",
					".(intval($args['cd_usuario']))."
				 );";

		$this->db->query($qr_sql);
	}
	
	public function anexo_listar($cd_expediente)
    {
    	$qr_sql = "
			SELECT ea.cd_expediente_anexo, 
			       ea.cd_expediente, 
				   ea.ds_arquivo, 
				   ea.ds_arquivo_nome,
				   TO_CHAR(ea.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
				   ea.cd_usuario_inclusao,
				   funcoes.get_usuario_nome(ea.cd_usuario_inclusao) AS ds_usuario,
				   (CASE WHEN ea.cd_usuario_inclusao = 99999 
				         THEN 'https://www.fundacaoceee.com.br/iframe/up/'
						 ELSE '".base_url()."up/expediente/'
				   END) AS url
              FROM comite_etica.expediente_anexo ea
			 WHERE ea.dt_exclusao IS NULL
			   AND ea.cd_expediente = ".intval($cd_expediente)."
			 ORDER BY ea.dt_inclusao;";

    	return $this->db->query($qr_sql)->result_array();
	}	
	
	public function anexo_salvar($cd_expediente, $args = array())
	{
		$qr_sql = "
			INSERT INTO comite_etica.expediente_anexo
				 (
					cd_expediente,
					ds_arquivo,
					ds_arquivo_nome,
					cd_usuario_inclusao
				 )
			VALUES
				 (
					".intval($cd_expediente).",
					'".trim($args['arquivo'])."',
					'".trim($args['arquivo_nome'])."',
					".intval($args['cd_usuario'])."
				 ); ";

		$this->db->query($qr_sql);
	}	
}
?>