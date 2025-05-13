<?php
class Atendimento_rodizio_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_atendimento_rodizio,
				   TO_CHAR(dt_atendimento_rodizio, 'DD/MM/YYYY') AS dt_atendimento_rodizio,
				   (CASE WHEN tp_turno = 'T' 
				         THEN 'Tarde'
				   		 ELSE 'Manhã'	
				   END) AS ds_turno,
				   (CASE WHEN tp_turno = 'T' 
				         THEN 'label label-warning'
				   		 ELSE 'label label-success'	
				   END) AS ds_class_turno
			  FROM projetos.atendimento_rodizio
			 WHERE dt_exclusao IS NULL 
			 ".(((trim($args['dt_atendimento_rodizio_ini']) != '') AND  (trim($args['dt_atendimento_rodizio_fim']) != '')) ? " AND DATE_TRUNC('day', dt_atendimento_rodizio) BETWEEN TO_DATE('".$args['dt_atendimento_rodizio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_atendimento_rodizio_fim']."', 'DD/MM/YYYY')" : "")."
			 ".(trim($args['tp_turno']) != '' ? "AND tp_turno = '".trim($args['tp_turno'])."'" : "")."
 		     ORDER BY dt_atendimento_rodizio ASC;";


		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_atendente($cd_atendimento_rodizio)
	{
		$qr_sql = "
			SELECT cd_atendimento_rodizio,
				   funcoes.get_usuario_nome(cd_usuario_atendimento) AS ds_usuario_atendimento,
				   (CASE WHEN tp_posicao = 'T' 
				         THEN 'Telefone'
				         WHEN tp_posicao = 'P'
				   	     THEN 'Atendimento Pessoal'
				   	     ELSE ''
				   END) AS ds_posicao
			  FROM projetos.atendimento_rodizio_usuario
			 WHERE dt_exclusao            IS NULL
			   AND tp_posicao             IS NOT NULL
			   AND cd_atendimento_rodizio = ".intval($cd_atendimento_rodizio).";";

		return $this->db->query($qr_sql)->result_array();	 
	}

	public function get_atendente()
	{
		$qr_sql = "
			SELECT codigo AS cd_usuario,
			       nome AS ds_nome,
			       usuario
			  FROM funcoes.get_usuario_gerencia_unidade('GCM-ATEN')
			 ORDER BY codigo ASC;";

  	    return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_atendimento_rodizio)
	{
		$qr_sql = "
			SELECT ar.cd_atendimento_rodizio,		
				   TO_CHAR(ar.dt_atendimento_rodizio, 'DD/MM/YYYY') AS dt_atendimento_rodizio,
			       ar.tp_turno,
			       funcoes.get_usuario_nome(cd_usuario_atendimento) AS ds_usuario_atendimento,
			       aru.cd_usuario_atendimento,
			       aru.tp_posicao
			  FROM projetos.atendimento_rodizio ar
			  JOIN projetos.atendimento_rodizio_usuario aru
			    ON aru.cd_atendimento_rodizio = ar.cd_atendimento_rodizio 
			 WHERE ar.cd_atendimento_rodizio  = ".intval($cd_atendimento_rodizio)." 
			   AND ar.dt_exclusao IS NULL
			 ORDER BY aru.cd_usuario_atendimento asc;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function atendimento_rodizio($cd_atendimento_rodizio, $cd_usuario)
	{
		$qr_sql = "
			SELECT aru.tp_posicao
			  FROM projetos.atendimento_rodizio_usuario aru
			 WHERE aru.cd_atendimento_rodizio = ".intval($cd_atendimento_rodizio)." 
			   AND aru.cd_usuario_atendimento = ".intval($cd_usuario)." 
			   AND aru.dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
       	$cd_atendimento_rodizio = intval($this->db->get_new_id('projetos.atendimento_rodizio', 'cd_atendimento_rodizio'));

		$qr_sql = "
			INSERT INTO projetos.atendimento_rodizio
                 (
               		cd_atendimento_rodizio,
               		dt_atendimento_rodizio, 
               		tp_turno,
               		cd_usuario_inclusao, 
              		cd_usuario_alteracao
                 )
            VALUES 
                 (
                   ".intval($cd_atendimento_rodizio).",
                   ".(trim($args['dt_atendimento_rodizio']) != '' ? "TO_DATE('".$args['dt_atendimento_rodizio']."', 'DD/MM/YYYY')" : "DEFAULT").",
                   ".(trim($args['tp_turno']) != '' ? "'".trim($args['tp_turno'])."'" : "DEFAULT").", 
                   ".intval($args['cd_usuario']).",
                   ".intval($args['cd_usuario'])."
             );";

		foreach($args['atendente'] as $key => $item)
		{
	        $qr_sql .= "
	        	INSERT INTO projetos.atendimento_rodizio_usuario
	        		(
	        			cd_usuario_atendimento,
	        			cd_atendimento_rodizio,
	        			tp_posicao,
	        			cd_usuario_inclusao,
	        			cd_usuario_alteracao
	        		)
	           VALUES
	           		(
	           			".$key.",
	           			".$cd_atendimento_rodizio.",
	           			".(trim($item) != ''? "'".trim($item)."'" : "DEFAULT").",
	           			".intval($args['cd_usuario']).",
	           			".intval($args['cd_usuario'])."
	           		);";			
		}#echo '<pre>'.$qr_sql;exit;

        $this->db->query($qr_sql);
		
		return $cd_atendimento_rodizio;
	}

	public function atualizar($cd_atendimento_rodizio,  $args = array())
	{
		$qr_sql = "
			UPDATE projetos.atendimento_rodizio
			   SET dt_atendimento_rodizio = ".(trim($args['dt_atendimento_rodizio']) != '' ? "TO_DATE('".$args['dt_atendimento_rodizio']."', 'DD/MM/YYYY')" : "DEFAULT").",		       
			       tp_turno               = ".(trim($args['tp_turno']) != '' ? "'".trim($args['tp_turno'])."'" : "DEFAULT").",
			       cd_usuario_alteracao   = ".intval($args['cd_usuario']).", 
			       dt_alteracao           = CURRENT_TIMESTAMP
			 WHERE cd_atendimento_rodizio = ".intval($cd_atendimento_rodizio).";";

		foreach($args['atendente'] as $key => $item)
		{
			$qr_sql .= "
				UPDATE projetos.atendimento_rodizio_usuario
				   SET tp_posicao             = ".(trim($item) != '' ? "'".trim($item)."'" : 'DEFAULT').",
				       cd_usuario_alteracao   = ".intval($args['cd_usuario']).", 
				       dt_alteracao           = CURRENT_TIMESTAMP
				 WHERE cd_atendimento_rodizio = ".intval($cd_atendimento_rodizio)."
				   AND cd_usuario_atendimento = ".$key.";";		
		}

		$this->db->query($qr_sql);
	}

	// public function excluir($cd_atendimento_rodizio, $cd_usuario)
	// {
	// 	$qr_sql = "
	// 		UPDATE projetos.atendimento_rodizio
	// 		   SET cd_usuario_exclusao    = ".intval($cd_usuario).", 
	// 		       dt_exclusao            = CURRENT_TIMESTAMP
	// 		 WHERE cd_atendimento_rodizio = ".intval($cd_atendimento_rodizio).";";

	// 	$this->db->query($qr_sql);
	// }
}