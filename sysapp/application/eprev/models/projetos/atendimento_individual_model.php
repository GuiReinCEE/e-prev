<?php
class atendimento_individual_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
	
	function usuario_solicitante(&$result, $args=array())
    {	
		$qr_sql = "
			SELECT DISTINCT ai.cd_usuario_inclusao AS value,
			       uc.nome AS text
			  FROM projetos.atendimento_individual ai
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = ai.cd_usuario_inclusao
			 WHERE ai.dt_exclusao IS NULL;";
			 
		 $result = $this->db->query($qr_sql);
	}
	
	function usuario_encaminhado(&$result, $args=array())
    {	
		$qr_sql = "
			SELECT DISTINCT ai.cd_usuario_encaminhamento AS value,
			       uc.nome AS text
			  FROM projetos.atendimento_individual ai
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = ai.cd_usuario_encaminhamento
			 WHERE ai.dt_exclusao IS NULL;";
			 
		 $result = $this->db->query($qr_sql);
	}
	
	function usuario_encerrado(&$result, $args=array())
    {	
		$qr_sql = "
			SELECT DISTINCT ai.cd_usuario_encerramento AS value,
			       uc.nome AS text
			  FROM projetos.atendimento_individual ai
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = ai.cd_usuario_encerramento
			 WHERE ai.dt_exclusao IS NULL;";
			 
		 $result = $this->db->query($qr_sql);
	}

	function listar(&$result, $args=array())
    {		
		$qr_sql = "
			SELECT ai.cd_atendimento_individual, 
			       projetos.nr_atendimento_individual(ai.nr_ano, ai.nr_numero) AS ano_numero,
				   ai.cd_empresa || '/' || ai.cd_registro_empregado || '/' || ai.seq_dependencia AS re,
				   ai.nome, 
				   CASE WHEN ai.dt_encerramento IS NOT NULL THEN 'Encerrado'
				        WHEN ai.dt_encaminhamento IS NOT NULL AND ai.dt_encerramento IS NULL THEN 'Iniciado'
						ELSE 'Cadastrado'
				   END AS status,
				   CASE WHEN ai.dt_encerramento IS NOT NULL THEN ''
				        WHEN ai.dt_encaminhamento IS NOT NULL AND ai.dt_encerramento IS NULL THEN 'label-important'
						ELSE 'label-success'
				   END AS class_status,
				   TO_CHAR(ai.dt_encaminhamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encaminhamento, 
				   uc.nome AS usuario_encaminhado,
				   TO_CHAR(ai.dt_atendimento, 'DD/MM/YYYY') AS dt_atendimento, 
				   TO_CHAR(ai.dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento, 
				   uc2.nome AS usuario_encerrado,
				   (SELECT TO_CHAR(aia.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') || ' : ' || aia.ds_atendimento_individual_acompanhamento
				      FROM projetos.atendimento_individual_acompanhamento aia
					 WHERE aia.dt_exclusao IS NULL
					   AND aia.cd_atendimento_individual = ai.cd_atendimento_individual
					 ORDER BY dt_inclusao DESC
					 LIMIT 1) AS acompanhamento,
                   TO_CHAR(dt_encerramento - dt_encaminhamento, 'HH24:MI:SS') AS hr_tempo_atendimento
              FROM projetos.atendimento_individual ai
			  LEFT JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = ai.cd_usuario_encaminhamento
			  LEFT JOIN projetos.usuarios_controledi uc2
			    ON uc2.codigo = ai.cd_usuario_encerramento
			 WHERE dt_exclusao IS NULL
			   ".(trim($args['cd_empresa']) != '' ? "AND ai.cd_empresa = ".intval($args['cd_empresa']) : "")."
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND ai.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
			   ".(trim($args['seq_dependencia']) != '' ? "AND ai.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
			   ".(trim($args["nome"]) != "" ? "AND UPPER(funcoes.remove_acento(ai.nome)) LIKE UPPER(funcoes.remove_acento('%".trim($args["nome"])."%'))" : "")."
			   ".(((trim($args['dt_cadastro_ini']) != "") AND (trim($args['dt_cadastro_fim']) != "")) ? " AND DATE_TRUNC('day', ai.dt_inclusao) BETWEEN TO_DATE('".$args['dt_cadastro_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_cadastro_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_encaminhamento_ini']) != "") AND (trim($args['dt_encaminhamento_fim']) != "")) ? " AND DATE_TRUNC('day', ai.dt_encaminhamento) BETWEEN TO_DATE('".$args['dt_encaminhamento_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_encaminhamento_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_encerramento_ini']) != "") AND (trim($args['dt_encerramento_fim']) != "")) ? " AND DATE_TRUNC('day', ai.dt_encerramento) BETWEEN TO_DATE('".$args['dt_encerramento_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_encerramento_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(trim($args['cd_usuario_inclusao']) != '' ? "AND ai.cd_usuario_inclusao = ".intval($args['cd_usuario_inclusao']) : "")."
			   ".(trim($args['cd_usuario_encaminhamento']) != '' ? "AND ai.cd_usuario_encaminhamento = ".intval($args['cd_usuario_encaminhamento']) : "")."
			   ".(trim($args['cd_usuario_encerramento']) != '' ? "AND ai.cd_usuario_encerramento = ".intval($args['cd_usuario_encerramento']) : "")."
			   ".(trim($args['fl_status']) == 'S' ? "AND ai.dt_encaminhamento IS NULL AND ai.dt_encerramento IS NULL" : "")."
			   ".(trim($args['fl_status']) == 'E' ? "AND ai.dt_encaminhamento IS NOT NULL AND ai.dt_encerramento IS NULL" : "")."
			   ".(trim($args['fl_status']) == 'C' ? "AND ai.dt_encerramento IS NOT NULL" : "").";";
		
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_atendimento_individual']) == 0)
		{
			$cd_atendimento_individual = intval($this->db->get_new_id("projetos.atendimento_individual", "cd_atendimento_individual"));
		
			$qr_sql = "
				INSERT INTO projetos.atendimento_individual
				     (
					    cd_atendimento_individual,
						cd_empresa, 
						cd_registro_empregado, 
						seq_dependencia, 
						nome, 
						ds_observacao, 
						cd_usuario_inclusao
					 )
                VALUES 
				    (
					   ".intval($cd_atendimento_individual).",
					   ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
					   ".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT").",
					   ".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
					   ".(trim($args['nome']) != '' ? str_escape($args['nome']) : "DEFAULT").",
					   ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
					   ".(trim($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT")."
					);";
		}
		else
		{
			$cd_atendimento_individual = intval($args['cd_atendimento_individual']);
		
			$qr_sql = "
				UPDATE projetos.atendimento_individual
				   SET cd_empresa            = ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
				       cd_registro_empregado = ".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT").",
					   seq_dependencia       = ".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
					   nome                  = ".(trim($args['nome']) != '' ? str_escape($args['nome']) : "DEFAULT").",
					   ds_observacao         = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
					   cd_usuario_alteracao  = ".(trim($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT").",
					   dt_alteracao          = CURRENT_TIMESTAMP
				 WHERE cd_atendimento_individual = ".intval($args['cd_atendimento_individual']).";";
		}
		
		$result = $this->db->query($qr_sql);
		
		return $cd_atendimento_individual;
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ai.cd_atendimento_individual, 
			       projetos.nr_atendimento_individual(ai.nr_ano, ai.nr_numero) AS ano_numero,
				   ai.cd_empresa,
				   ai.cd_registro_empregado,
				   ai.seq_dependencia,
				   ai.nome,
				   ai.ds_observacao,
				   TO_CHAR(ai.dt_atendimento, 'DD/MM/YYYY') AS dt_atendimento,
				   TO_CHAR(ai.dt_encaminhamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encaminhamento,
				   TO_CHAR(ai.dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento
			  FROM projetos.atendimento_individual ai
			 WHERE cd_atendimento_individual = ".intval($args['cd_atendimento_individual']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function encaminhar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.atendimento_individual
			   SET cd_usuario_encaminhamento = ".(trim($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT").",
			       dt_encaminhamento         = CURRENT_TIMESTAMP
			 WHERE cd_atendimento_individual = ".intval($args['cd_atendimento_individual']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function encerrar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.atendimento_individual
			   SET cd_usuario_encerramento = ".(trim($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT").",
			       --dt_atendimento          = ".(trim($args['dt_atendimento']) != '' ? "TO_DATE('".trim($args['dt_atendimento'])."', 'DD/MM/YYYY')" : "DEFAULT").",
			       dt_encerramento         = CURRENT_TIMESTAMP
			 WHERE cd_atendimento_individual = ".intval($args['cd_atendimento_individual']).";";
		
		$result = $this->db->query($qr_sql);
	}
	
	function listar_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT aia.cd_atendimento_individual_acompanhamento, 
                   aia.ds_atendimento_individual_acompanhamento,
				   TO_CHAR(aia.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
				   uc.nome
              FROM projetos.atendimento_individual_acompanhamento aia
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = aia.cd_usuario_inclusao
             WHERE aia.cd_atendimento_individual = ".intval($args['cd_atendimento_individual'])."
			   AND aia.dt_exclusao IS NULL;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_acompahamento(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.atendimento_individual_acompanhamento
			     (
                   cd_atendimento_individual, 
                   ds_atendimento_individual_acompanhamento, 
				   cd_usuario_inclusao
				 )
            VALUES 
			     (
				   ".(trim($args['cd_atendimento_individual']) != '' ? intval($args['cd_atendimento_individual']) : "DEFAULT").",
				   ".(trim($args['ds_atendimento_individual_acompanhamento']) != '' ? str_escape($args['ds_atendimento_individual_acompanhamento']) : "DEFAULT").",
				   ".(trim($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT")."
				 );";
				 
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_acompahamento(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.atendimento_individual_acompanhamento
			   SET cd_usuario_exclusao = ".(trim($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT").",
			       dt_exclusao         = CURRENT_TIMESTAMP		
			 WHERE cd_atendimento_individual_acompanhamento = ".intval($args['cd_atendimento_individual_acompanhamento']).";";
			
		$result = $this->db->query($qr_sql);
	}
}
?>