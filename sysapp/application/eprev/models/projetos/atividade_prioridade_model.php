<?php
class Atividade_prioridade_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function get_gerencia_solicitante($cd_atendente)
	{
		$qr_sql = "
			SELECT DISTINCT a.area_solicitante AS value,
			       a.area_solicitante || ' - ' || d.nome AS text
			  FROM informatica.atividade_prioridade a
			  JOIN projetos.divisoes d
			    ON d.codigo = a.area_solicitante
			 WHERE a.cd_atendente = ".intval($cd_atendente)."
			 ORDER BY a.area_solicitante || ' - ' || d.nome;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_atendente($cd_area_solicitante)
	{
		$qr_sql = "
			SELECT DISTINCT a.cd_atendente AS value,
			       funcoes.get_usuario_nome(a.cd_atendente::INTEGER) AS text
			  FROM informatica.atividade_prioridade a
			 WHERE a.area_solicitante = '".trim($cd_area_solicitante)."'
			 ORDER BY funcoes.get_usuario_nome(a.cd_atendente::INTEGER);";

		return $this->db->query($qr_sql)->result_array();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT a.cd_atendente,
				   a.ds_atendente, 	
				   a.cd_solicitante,
				   a.ds_solicitante,
				   a.numero,
				   a.dt_cadastro,
				   a.area_solicitante,
				   a.descricao,
				   a.nr_prioridade,
				   a.dt_prioridade,
				   a.cd_prioridade_usuario,
				   a.ds_prioridade_usuario,
				   a.cd_status,
				   a.ds_status,
				   a.status_label,
				   a.area_atendente
			  FROM informatica.atividade_prioridade a
			 WHERE a.cd_atendente     = ".intval($args['cd_atendente'])."
			   AND a.area_solicitante = '".trim($args['cd_area_solicitante'])."'
			 ORDER BY a.nr_prioridade ASC;";

		return $this->db->query($qr_sql)->result_array();
	}
	
	public function salvar($args=array())
    {
        $qr_sql = '';
		
		foreach($args['ar_prioridade_nova'] as $ar_item)
		{
			$qr_sql .= " 
				UPDATE projetos.atividades AS aa
				   SET nr_prioridade         = ".intval($ar_item['nr_prioridade']).",
					   dt_prioridade         = CURRENT_TIMESTAMP,
					   cd_prioridade_usuario = ".intval($ar_item['cd_usuario'])."
				 WHERE numero = ".intval($ar_item['cd_atividade']).";
				 
				INSERT INTO projetos.atividade_historico 
					 ( 
						cd_atividade, 
						cd_recurso, 
						dt_inicio_prev,
						status_atual,
						observacoes 
					 )
				VALUES 
					 ( 
						".intval($ar_item['cd_atividade']).", 
						".intval($ar_item['cd_usuario']).",
						CURRENT_TIMESTAMP,
						(SELECT a.status_atual FROM projetos.atividades a WHERE a.numero = ".intval($ar_item['cd_atividade'])."),
						'Definiчуo de Prioridade: ".intval($ar_item['nr_prioridade'])."'
					 );";
		}

		$this->db->query($qr_sql);
    }	

	public function carrega($cd_atividade)
	{
		$qr_sql = "
			SELECT a.cd_atendente,
				   a.ds_atendente, 	
				   a.cd_solicitante,
				   a.ds_solicitante,
				   a.numero,
				   a.area_solicitante,
				   funcoes.get_usuario(a.cd_atendente) AS atendente,
				   a.nr_prioridade,
				   funcoes.get_usuario(a.cd_solicitante) AS solicitante,
				   a.ds_status,
				   funcoes.get_usuario(a.cd_prioridade_usuario) AS usuario
			  FROM informatica.atividade_prioridade a
			 WHERE a.numero = ".intval($cd_atividade).";";

		return $this->db->query($qr_sql)->row_array();
	}	
}
?>