<?php
class Atendimento_confirma_bco_ag_conta_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
    
    public function listar($args = array())
	{
        $qr_sql = "
            SELECT a.cd_atendimento,
				   a.cd_empresa,
				   a.cd_registro_empregado,
				   a.seq_dependencia,
				   a.nome,
				   a.indic_ativo,
				   CASE WHEN a.indic_ativo = 'T' THEN 'Telefone'
						WHEN a.indic_ativo = 'P' THEN 'Pessoal'
						WHEN a.indic_ativo = 'C' THEN 'Consulta'
						ELSE 'E-mail'
				   END AS fl_indic_ativo,
				   ac.ds_observacao,
				   ac.cd_atendimento_confirma_bco_ag_conta,
				   TO_CHAR(ac.dt_confirmacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_confirmacao,
				   TO_CHAR(ac.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(ac.cd_usuario_inclusao) AS usuario_inclusao,
				   uc.nome AS atendente,
				   TO_CHAR(c.jn_datetime, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
				   funcoes.get_usuario_nome(ac.cd_usuario_confirmacao) AS usuario_confirmacao
			  FROM projetos.atendimento a
			  JOIN public.controle_bco_ag_conta c
				ON c.cd_empresa            = a.cd_empresa
			   AND c.cd_registro_empregado = a.cd_registro_empregado
			   AND UPPER(c.jn_oracle_user) = UPPER(funcoes.get_usuario(a.id_atendente))
			   AND c.jn_datetime           BETWEEN a.dt_hora_inicio_atendimento AND a.dt_hora_fim_atendimento
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = a.id_atendente
			  JOIN public.participantes p 
				ON p.cd_empresa            = a.cd_empresa
			   AND p.cd_registro_empregado = a.cd_registro_empregado
			   AND p.seq_dependencia       = a.seq_dependencia
			  LEFT JOIN projetos.atendimento_confirma_bco_ag_conta ac
				ON ac.cd_atendimento = a.cd_atendimento
			 WHERE 1 = 1
               ".(((trim($args['dt_atendimento_ini']) != '') AND (trim($args['dt_atendimento_fim']) != '')) ? "AND DATE_TRUNC('day', a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_atendimento_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_atendimento_fim']."', 'DD/MM/YYYY')" : '')."
               ".(trim($args['fl_atendente']) != '' ? "AND a.id_atendente = ".$args['fl_atendente'] : '' )."
               ".(trim($args['fl_confirmado']) == 'S' ? 'AND ac.dt_confirmacao IS NOT NULL' : '' )."
               ".(trim($args['fl_confirmado']) == 'N' ? 'AND ac.dt_confirmacao IS NULL' : '' ).";";
				
        return $this->db->query($qr_sql)->result_array();
    }
    
    public function confirmar($cd_atendimento_confirma_bco_ag_conta, $cd_atendimento, $cd_usuario)
    {
		if(intval($cd_atendimento_confirma_bco_ag_conta) > 0)
		{
			$qr_sql = "
				UPDATE projetos.atendimento_confirma_bco_ag_conta
				   SET cd_usuario_inclusao 	  = ".intval($cd_usuario).",
					   cd_usuario_confirmacao = ".intval($cd_usuario).",
					   dt_confirmacao         = CURRENT_TIMESTAMP,
					   cd_usuario_alteracao   = ".intval($cd_usuario).",
					   dt_alteracao           = CURRENT_TIMESTAMP
				 WHERE cd_atendimento_confirma_bco_ag_conta = ".intval($cd_atendimento_confirma_bco_ag_conta).";";
				 
			$this->db->query($qr_sql);  
		}
		else
		{
			$cd_atendimento_confirma_bco_ag_conta = intval($this->db->get_new_id('projetos.atendimento_confirma_bco_ag_conta', 'cd_atendimento_confirma_bco_ag_conta'));

			$qr_sql = "
				INSERT INTO projetos.atendimento_confirma_bco_ag_conta
					 (
						 cd_atendimento_confirma_bco_ag_conta,
						 cd_atendimento,
						 cd_usuario_inclusao,
						 cd_usuario_confirmacao,
						 dt_confirmacao,
						 cd_usuario_alteracao,
						 dt_alteracao
					 )
				VALUES 
					 (
						 ".$cd_atendimento_confirma_bco_ag_conta.",
						 ".intval($cd_atendimento).",
						 ".intval($cd_usuario).",
						 ".intval($cd_usuario).",
						 CURRENT_TIMESTAMP,
						 ".intval($cd_usuario).",
						 CURRENT_TIMESTAMP
					 );";
					 
			$this->db->query($qr_sql);
		}		
    }
	
	public function get_atendente()
	{
		$qr_sql = "	
			SELECT DISTINCT a.id_atendente AS value,
				   uc.nome AS text
			  FROM projetos.atendimento a
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = a.id_atendente
			  JOIN public.controle_bco_ag_conta c 
			    ON c.cd_empresa = a.cd_empresa 
			   AND c.cd_registro_empregado = a.cd_registro_empregado 
			   AND UPPER(c.jn_oracle_user) = UPPER(funcoes.get_usuario(a.id_atendente))
			 WHERE tipo NOT IN ('X')
			 ORDER BY text;";
			
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function alterar_motivo($cd_atendimento_confirma_bco_ag_conta, $cd_atendimento, $ds_observacao, $cd_usuario)
	{
		if(intval($cd_atendimento_confirma_bco_ag_conta) > 0)
		{
			$qr_sql = "
				UPDATE projetos.atendimento_confirma_bco_ag_conta
				   SET ds_observacao   		 = ".(trim($ds_observacao) != '' ? str_escape($ds_observacao) : "DEFAULT").",
					   cd_usuario_alteracao  = ".intval($cd_usuario).",
					   dt_alteracao          = CURRENT_TIMESTAMP
				 WHERE cd_atendimento_confirma_bco_ag_conta = ".intval($cd_atendimento_confirma_bco_ag_conta).";";
				 
			$this->db->query($qr_sql);  
		}
		else
		{
			$cd_atendimento_confirma_bco_ag_conta = intval($this->db->get_new_id('projetos.atendimento_confirma_bco_ag_conta', 'cd_atendimento_confirma_bco_ag_conta'));

			$qr_sql = "
				INSERT INTO projetos.atendimento_confirma_bco_ag_conta
					 (
						 cd_atendimento_confirma_bco_ag_conta,
						 cd_atendimento,
						 ds_observacao,
						 cd_usuario_inclusao,
						 cd_usuario_alteracao,
						 dt_alteracao
					 )
				VALUES 
					 (
						 ".$cd_atendimento_confirma_bco_ag_conta.",
						 ".intval($cd_atendimento).",
						 ".(trim($ds_observacao) != '' ? str_escape($ds_observacao) : "DEFAULT").",
						 ".intval($cd_usuario).",
						 ".intval($cd_usuario).",
						 CURRENT_TIMESTAMP
					 );";
			
			$this->db->query($qr_sql);  
		}
	}
}