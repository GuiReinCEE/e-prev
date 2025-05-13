<?php
class portabilidade_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT p.cd_portabilidade,
			       TO_CHAR(p.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       funcoes.get_usuario_nome(p.cd_usuario_inclusao) AS ds_usuario_inclusao,
			       p.cd_empresa,
			       p.cd_registro_empregado,
			       p.seq_dependencia,
			       part.nome,
			       (SELECT TO_CHAR(pa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS')
					  FROM projetos.portabilidade_acompanhamento pa
					 WHERE pa.cd_portabilidade = p.cd_portabilidade
					   AND pa.dt_exclusao IS NULL
					 ORDER BY pa.dt_inclusao DESC
					 LIMIT 1) AS dt_acompanhamento,
				    (SELECT TO_CHAR(pa.dt_agendamento_alerta, 'DD/MM/YYYY')
					  FROM projetos.portabilidade_acompanhamento pa
					 WHERE pa.cd_portabilidade = p.cd_portabilidade
					   AND pa.dt_exclusao IS NULL
					 ORDER BY pa.dt_inclusao DESC
					 LIMIT 1) AS dt_agendamento_alerta,
				   (SELECT ps.ds_portabilidade_status
					  FROM projetos.portabilidade_acompanhamento pa
					  JOIN projetos.portabilidade_status ps
					    ON ps.cd_portabilidade_status = pa.cd_portabilidade_status
					 WHERE pa.cd_portabilidade = p.cd_portabilidade
					   AND pa.dt_exclusao IS NULL
					 ORDER BY pa.dt_inclusao DESC
					 LIMIT 1) AS ds_portabilidade_status,
				   (SELECT CASE WHEN pa.cd_portabilidade_status = 1
				                THEN 'label'
				                WHEN pa.cd_portabilidade_status = 2
				                THEN 'label label-warning'
				                WHEN pa.cd_portabilidade_status = 3
				                THEN 'label label-info'
				                WHEN pa.cd_portabilidade_status = 4
				                THEN 'label label-success'
				                ELSE 'label label-inverse'
				           END
					  FROM projetos.portabilidade_acompanhamento pa
					 WHERE pa.cd_portabilidade = p.cd_portabilidade
					   AND pa.dt_exclusao IS NULL
					 ORDER BY pa.dt_inclusao DESC
					 LIMIT 1) AS ds_class_status,
				   (SELECT ds_portabilidade_acompanhamento
					  FROM projetos.portabilidade_acompanhamento pa
					 WHERE pa.cd_portabilidade = p.cd_portabilidade
					   AND pa.dt_exclusao IS NULL
					 ORDER BY pa.dt_inclusao DESC
					 LIMIT 1) AS ds_portabilidade_acompanhamento
			  FROM projetos.portabilidade p
			  JOIN participantes part
			    ON part.cd_empresa            = p.cd_empresa
			   AND part.cd_registro_empregado = p.cd_registro_empregado
			   AND part.seq_dependencia       = p.seq_dependencia
			 WHERE p.dt_exclusao IS NULL
               ".(((trim($args['dt_inclusao_ini']) != '') AND trim($args['dt_inclusao_fim']) != '') ? "AND DATE_TRUNC('day', p.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYY')" : '')."
               ".(trim($args['cd_empresa']) != '' ? "AND p.cd_empresa = ".intval($args['cd_empresa']) : '')."
               ".(trim($args['cd_registro_empregado']) != '' ? "AND p.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : '')."
               ".(trim($args['seq_dependencia']) != '' ? "AND p.seq_dependencia = ".intval($args['seq_dependencia']) : '')."
               ".(((trim($args['dt_acompanhamento_ini']) != '') AND trim($args['dt_acompanhamento_fim']) != '') ? "AND DATE_TRUNC('day', 
				(
	               	SELECT pa.dt_inclusao
					  FROM projetos.portabilidade_acompanhamento pa
					 WHERE pa.cd_portabilidade = p.cd_portabilidade
					   AND pa.dt_exclusao IS NULL
					 ORDER BY pa.dt_inclusao DESC
					 LIMIT 1
				 )
               	) BETWEEN TO_DATE('".$args['dt_acompanhamento_ini']."', 'DD/MM/YYY') AND TO_DATE('".$args['dt_acompanhamento_fim']."', 'DD/MM/YYY')" : '')."
                ".(trim($args['cd_portabilidade_status']) != '' ? "AND (
	               	SELECT pa.cd_portabilidade_status
					  FROM projetos.portabilidade_acompanhamento pa
					 WHERE pa.cd_portabilidade = p.cd_portabilidade
					   AND pa.dt_exclusao IS NULL
					 ORDER BY pa.dt_inclusao DESC
					 LIMIT 1
				 ) = ".intval($args['cd_portabilidade_status']) : '')."
               ;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_portabilidade)
	{
		$qr_sql = "
			SELECT p.cd_portabilidade,
			       TO_CHAR(p.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       p.cd_empresa,
			       p.cd_registro_empregado,
			       p.seq_dependencia,
			       part.nome,
			       funcoes.get_usuario_nome(p.cd_usuario_inclusao) AS ds_usuario_inclusao,
			       pa.cd_portabilidade_status
			  FROM projetos.portabilidade p
			  JOIN participantes part
			    ON part.cd_empresa            = p.cd_empresa
			   AND part.cd_registro_empregado = p.cd_registro_empregado
			   AND part.seq_dependencia       = p.seq_dependencia
			  JOIN projetos.portabilidade_acompanhamento pa
			    ON pa.cd_portabilidade = p.cd_portabilidade
			 WHERE p.cd_portabilidade = ".intval($cd_portabilidade)."
			   AND pa.dt_exclusao IS NULL
			 ORDER BY pa.dt_inclusao DESC
			 LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_portabilidade = intval($this->db->get_new_id('projetos.portabilidade', 'cd_portabilidade'));

		$qr_sql = "
			INSERT INTO projetos.portabilidade
			     (
			       	cd_portabilidade, 
			       	cd_empresa, 
			       	cd_registro_empregado, 
			       	seq_dependencia, 
            		cd_usuario_inclusao, 
            		cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_portabilidade).",
			     	".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT" ).",
			     	".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT" ).",
			     	".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT" ).",
				    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."    
			     );";

		$this->db->query($qr_sql); 

		return $cd_portabilidade;
	}

	public function salvar_acompanhamento($cd_portabilidade, $args = array())
	{
		$qr_sql = "
			INSERT INTO projetos.portabilidade_acompanhamento
		         (
        			cd_portabilidade, 
        			ds_portabilidade_acompanhamento, 
        			cd_portabilidade_status, 
        			dt_agendamento_alerta,
        			cd_agenda,
        			cd_usuario_inclusao, 
        			cd_usuario_alteracao
        		 )
       		VALUES
			     (
			     	".intval($cd_portabilidade).",
			     	".(trim($args['ds_portabilidade_acompanhamento']) != '' ? str_escape($args['ds_portabilidade_acompanhamento']) : "DEFAULT" ).",
			     	".(intval($args['cd_portabilidade_status']) > 0 ? intval($args['cd_portabilidade_status']) : "DEFAULT" ).",
			     	".(trim($args['dt_agendamento_alerta']) != '' ? "TO_DATE('".$args['dt_agendamento_alerta']."', 'DD/MM/YYYY')" : "DEFAULT").",
			     	".(intval($args['cd_agenda']) > 0 ? intval($args['cd_agenda']) : "DEFAULT" ).",
				    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."    
			     );";

        $this->db->query($qr_sql);  
	}

	public function get_status()
	{
		$qr_sql = "
			SELECT cd_portabilidade_status AS value, 
			       ds_portabilidade_status AS text
              FROM projetos.portabilidade_status
             WHERE dt_exclusao IS NULL
             ORDER BY ds_portabilidade_status ASC;";

        return $this->db->query($qr_sql)->result_array();
	}

	public function lista_acompahamento($cd_portabilidade)
	{
		$qr_sql = "
			SELECT pa.cd_portabilidade,
			       TO_CHAR(pa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       TO_CHAR(pa.dt_agendamento_alerta, 'DD/MM/YYYY') AS dt_agendamento_alerta,
			       pa.ds_portabilidade_acompanhamento,
			       funcoes.get_usuario_nome(pa.cd_usuario_inclusao) AS ds_usuario_inclusao,
			       pa.cd_portabilidade_status,
			       ps.ds_portabilidade_status,
			       CASE WHEN pa.cd_portabilidade_status = 1
		                THEN 'label'
		                WHEN pa.cd_portabilidade_status = 2
		                THEN 'label label-warning'
		                WHEN pa.cd_portabilidade_status = 3
		                THEN 'label label-info'
		                WHEN pa.cd_portabilidade_status = 4
		                THEN 'label label-success'
		                ELSE 'label label-inverse'
		           END AS ds_class_status
			  FROM projetos.portabilidade_acompanhamento pa
			  JOIN projetos.portabilidade_status ps
				ON ps.cd_portabilidade_status = pa.cd_portabilidade_status
			 WHERE pa.cd_portabilidade = ".intval($cd_portabilidade)."
			   AND pa.dt_exclusao IS NULL
			 ORDER BY pa.dt_inclusao DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function agendar($dt_agendamento_alerta, $assunto, $mensagem, $cd_usuario)
	{
		$qr_sql = "
			 (SELECT agendar 
				FROM agenda.agendar(0,
					 ".$cd_usuario.",
					 TO_TIMESTAMP('".$dt_agendamento_alerta." 08:00', 'DD/MM/YYYY HH24:MI'),
					 TO_TIMESTAMP('".$dt_agendamento_alerta." 08:30', 'DD/MM/YYYY HH24:MI'),
					'".trim($assunto)."',
					'-',
					'".trim($mensagem)."',
					'S',
					15,
					funcoes.get_usuario(".$cd_usuario.") || '@eletroceee.com.br;previdencia@eletroceee.com.br')
			);";

		return $this->db->query($qr_sql)->row_array();
	}
}
?>