<?php
class Atendimento_Recadastro_Devolucao_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT ard.cd_atendimento_recadastro_devolucao, 
				   ard.cd_empresa, 
				   ard.cd_registro_empregado, 
				   ard.seq_dependencia, 
				   p.nome,
				   TO_CHAR(ard.dt_devolucao,'DD/MM/YYYY') AS dt_devolucao, 
				   ard.cd_atendimento_recadastro_devolucao_motivo, 
				   ardm.motivo,
				   ard.descricao, 
				   ard.observacao, 
				   ard.dt_inclusao, 
				   ard.cd_usuario_inclusao,
				   uc.nome AS nome_usuario,
				   TO_CHAR(ard.dt_alteracao, 'DD/MM/YYYY HH24:MI') AS dt_alteracao,		
				   TO_CHAR(ard.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao,	
				   uc2.nome AS nome_usuario_alteracao
			  FROM projetos.atendimento_recadastro_devolucao ard
			  JOIN projetos.atendimento_recadastro_devolucao_motivo ardm
				ON ardm.cd_atendimento_recadastro_devolucao_motivo = ard.cd_atendimento_recadastro_devolucao_motivo					  
			  JOIN public.participantes p
				ON p.cd_empresa = ard.cd_empresa
			   AND p.cd_registro_empregado = ard.cd_registro_empregado
			   AND p.seq_dependencia = ard.seq_dependencia
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = ard.cd_usuario_inclusao
			  LEFT JOIN projetos.usuarios_controledi uc2
				ON uc2.codigo = ard.cd_usuario_alteracao
			 WHERE ard.dt_exclusao IS NULL
				".(trim($args["cd_empresa"]) != '' ? "AND ard.cd_empresa = ".intval($args["cd_empresa"]) : "")."
				".(trim($args["cd_registro_empregado"]) != '' ? "AND ard.cd_registro_empregado = ".intval($args["cd_registro_empregado"]) : "")."
				".(trim($args["seq_dependencia"]) != '' ? "AND ard.seq_dependencia = ".intval($args["seq_dependencia"]) : "")."
				".(trim($args["nome"]) != '' ? "AND UPPER(p.nome) LIKE UPPER('%".$args["nome"]."%')" : "")."
				".(trim($args["cd_atendimento_recadastro_devolucao_motivo"]) ? " AND ard.cd_atendimento_recadastro_devolucao_motivo = ".intval($args["cd_atendimento_recadastro_devolucao_motivo"]) : "")."
				".(((trim($args["dt_devolucao_ini"]) != "") and (trim($args["dt_devolucao_fim"]) != "")) ? " AND CAST(ard.dt_devolucao AS DATE) BETWEEN TO_DATE('".$args["dt_devolucao_ini"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_devolucao_fim"]."','DD/MM/YYYY')" : "").";";

		$result = $this->db->query($qr_sql);
	}
	
	function carrega( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT ard.cd_atendimento_recadastro_devolucao, 
				   ard.cd_empresa, 
				   ard.cd_registro_empregado, 
				   ard.seq_dependencia, 
				   p.nome,
				   TO_CHAR(ard.dt_devolucao,'DD/MM/YYYY') AS dt_devolucao, 
				   ard.cd_atendimento_recadastro_devolucao_motivo, 
				   ardm.motivo,
				   ard.descricao, 
				   ard.observacao, 
				   TO_CHAR(ard.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao,
				   ard.cd_usuario_inclusao,
				   uc.nome AS nome_usuario,
				   TO_CHAR(ard.dt_exclusao, 'DD/MM/YYYY HH24:MI') AS dt_exclusao,
				   ard.cd_usuario_exclusao,
				   uce.nome AS nome_usuario_exclusao,
				   TO_CHAR(ard.dt_alteracao, 'DD/MM/YYYY HH24:MI') AS dt_alteracao,		
				   uc2.nome AS nome_usuario_alteracao
			  FROM projetos.atendimento_recadastro_devolucao ard
			  JOIN projetos.atendimento_recadastro_devolucao_motivo ardm
				ON ardm.cd_atendimento_recadastro_devolucao_motivo = ard.cd_atendimento_recadastro_devolucao_motivo					  
			  JOIN public.participantes p
				ON p.cd_empresa = ard.cd_empresa
			   AND p.cd_registro_empregado = ard.cd_registro_empregado
			   AND p.seq_dependencia = ard.seq_dependencia
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = ard.cd_usuario_inclusao
			  LEFT JOIN projetos.usuarios_controledi uce
				ON uce.codigo = ard.cd_usuario_exclusao		
              LEFT JOIN projetos.usuarios_controledi uc2
				ON uc2.codigo = ard.cd_usuario_alteracao				
			 WHERE ard.cd_atendimento_recadastro_devolucao = ".intval($args["cd_atendimento_recadastro_devolucao"]).";";

		$result = $this->db->query($qr_sql);
	}	
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_atendimento_recadastro_devolucao']) > 0)
		{
			$qr_sql = " 
				UPDATE projetos.atendimento_recadastro_devolucao
				   SET cd_empresa                                 = ".(trim($args['cd_empresa']) == "" ? "DEFAULT" : $args['cd_empresa']).", 
					   cd_registro_empregado                      = ".(trim($args['cd_registro_empregado']) == "" ? "DEFAULT" : $args['cd_registro_empregado']).",
					   seq_dependencia                            = ".(trim($args['seq_dependencia']) == "" ? "DEFAULT" : $args['seq_dependencia']).",
					   dt_devolucao                               = ".(trim($args['dt_devolucao']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_devolucao']."','DD/MM/YYYY')").",  
					   descricao                                  = ".(trim($args['descricao']) == "" ? "DEFAULT" : "'".$args['descricao']."'").", 
					   observacao                                 = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".$args['observacao']."'").",
					   cd_atendimento_recadastro_devolucao_motivo = ".(trim($args['cd_atendimento_recadastro_devolucao_motivo']) == "" ? "DEFAULT" : $args['cd_atendimento_recadastro_devolucao_motivo']).",
					   cd_usuario_alteracao                       = ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : $args['cd_usuario']).",
					   dt_alteracao                               = CURRENT_TIMESTAMP
				 WHERE cd_atendimento_recadastro_devolucao = ".intval($args['cd_atendimento_recadastro_devolucao']).";";		
			$this->db->query($qr_sql);
			
			$cd_atendimento_recadastro_devolucao = intval($args['cd_atendimento_recadastro_devolucao']);
		}
		else
		{
			$cd_atendimento_recadastro_devolucao = intval($this->db->get_new_id("projetos.atendimento_recadastro_devolucao", "cd_atendimento_recadastro_devolucao"));
			$qr_sql = " 
						INSERT INTO projetos.atendimento_recadastro_devolucao
						     (
                               cd_atendimento_recadastro_devolucao,
							   cd_empresa,
							   cd_registro_empregado,
							   seq_dependencia,
							   dt_devolucao,
							   descricao,
							   observacao,
							   cd_atendimento_recadastro_devolucao_motivo,
							   cd_usuario_inclusao
							 )
                        VALUES 
						     (
							   ".$cd_atendimento_recadastro_devolucao.",
							   ".(trim($args['cd_empresa']) == "" ? "DEFAULT" : $args['cd_empresa']).",
							   ".(trim($args['cd_registro_empregado']) == "" ? "DEFAULT" : $args['cd_registro_empregado']).",
							   ".(trim($args['seq_dependencia']) == "" ? "DEFAULT" : $args['seq_dependencia']).",
							   ".(trim($args['dt_devolucao']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_devolucao']."','DD/MM/YYYY')").",
							   ".(trim($args['descricao']) == "" ? "DEFAULT" : "'".$args['descricao']."'").",
							   ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".$args['observacao']."'").",
							   ".(trim($args['cd_atendimento_recadastro_devolucao_motivo']) == "" ? "DEFAULT" : $args['cd_atendimento_recadastro_devolucao_motivo']).",
							   ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : $args['cd_usuario'])."
							 );			
					  ";
			$this->db->query($qr_sql);	

		}
		
		return $cd_atendimento_recadastro_devolucao;
	}	
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE projetos.atendimento_recadastro_devolucao
			   SET cd_usuario_exclusao  = ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : $args['cd_usuario']).", 
				   dt_exclusao          = CURRENT_TIMESTAMP
			 WHERE cd_atendimento_recadastro_devolucao = ".$args['cd_atendimento_recadastro_devolucao'].";";		
				  
		$this->db->query($qr_sql);
	}	
	
	function devolucao_motivo( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT ardm.cd_atendimento_recadastro_devolucao_motivo AS value, 
				   ardm.motivo AS text
			  FROM projetos.atendimento_recadastro_devolucao_motivo ardm
			 WHERE ardm.dt_exclusao IS NULL
			 ORDER BY text;";
			 
		$result = $this->db->query($qr_sql);
	}
}
?>
