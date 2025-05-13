<?php
class atendimento_recadastro_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT a.cd_atendimento_recadastro,
                   a.nome,
                   TO_CHAR(a.dt_criacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_criacao,
                   TO_CHAR(a.dt_cancelamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento,
                   a.cd_usuario_criacao,
                   a.cd_empresa,
                   a.cd_registro_empregado,
                   a.seq_dependencia,
                   b.guerra AS nome_gap,
                   a.observacao,
                   a.servico_social,
                   TO_CHAR( a.dt_atualizacao, 'DD/MM/YYYY HH24:MI:SS' ) AS dt_atualizacao,
                   a.cd_usuario_atualizacao,
				   ua.guerra AS nome_usuario_atualizacao,
                   pp.ddd,
                   pp.telefone,
                   pp.ddd_outro,
                   pp.telefone_outro
              FROM projetos.atendimento_recadastro a
              JOIN projetos.usuarios_controledi b
                ON a.cd_usuario_criacao = b.codigo
              JOIN public.participantes pp 
        		ON a.cd_empresa = pp.cd_empresa 
			   AND a.cd_registro_empregado = pp.cd_registro_empregado 
			   AND a.seq_dependencia = pp.seq_dependencia
		      LEFT JOIN projetos.usuarios_controledi ua
                ON a.cd_usuario_atualizacao = ua.codigo
			 WHERE 1 = 1
			   ".(trim($args['cd_empresa']) != '' ? "AND a.cd_empresa = ".intval($args['cd_empresa']) : "")."
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND a.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
			   ".(trim($args['seq_dependencia']) != '' ? "AND a.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
			   ".(((trim($args['dt_criacao_ini']) != "") AND (trim($args['dt_criacao_fim']) != "")) ? "AND CAST(a.dt_criacao AS DATE) BETWEEN TO_DATE('".trim($args['dt_criacao_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_criacao_fim'])."','DD/MM/YYYY') " : "")."
			  ";

		$result = $this->db->query($qr_sql);
	}	
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_atendimento_recadastro,
                   nome,
                   TO_CHAR(dt_cancelamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento,
                   cd_empresa,
                   cd_registro_empregado,
                   seq_dependencia,
                   observacao,
                   servico_social,
				   dt_periodo,
				   TO_CHAR(dt_criacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_criacao,
				   TO_CHAR(dt_atualizacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_atualizacao,
				   motivo_cancelamento
              FROM projetos.atendimento_recadastro
			 WHERE cd_atendimento_recadastro = ".intval($args['cd_atendimento_recadastro']).";";
			
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_atendimento_recadastro']) == 0)
		{
			$qr_sql = "
				INSERT INTO projetos.atendimento_recadastro
				     (
                       cd_empresa, 
					   cd_registro_empregado, 
                       seq_dependencia, 
					   nome, 
					   observacao, 
					   servico_social,
					   dt_periodo,
					   cd_usuario_criacao, 
					   dt_criacao
					 )
                VALUES 
				     (
					   ".intval($args['cd_empresa']).",
					   ".intval($args['cd_registro_empregado']).",
					   ".intval($args['seq_dependencia']).",
					   ".str_escape($args['nome']).",
					   ".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
					   ".(trim($args['servico_social']) != '' ? str_escape($args['servico_social']) : "DEFAULT").",
					   TO_CHAR(CURRENT_TIMESTAMP, 'YYYY')::integer,
					   ".intval($args['cd_usuario']).",
					   CURRENT_TIMESTAMP
					 );";
		}
		else
		{
			$qr_sql = "
				UPDATE projetos.atendimento_recadastro
                   SET cd_empresa             = ".intval($args['cd_empresa']).", 
				       cd_registro_empregado  = ".intval($args['cd_registro_empregado']).", 
                       seq_dependencia        = ".intval($args['seq_dependencia']).",
					   nome                   = ".str_escape($args['nome']).",
                       observacao             = ".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").", 
                       servico_social         = ".(trim($args['servico_social']) != '' ? str_escape($args['servico_social']) : "DEFAULT").", 
					   cd_usuario_atualizacao = ".intval($args['cd_usuario']).", 
					   dt_atualizacao         = CURRENT_TIMESTAMP
                 WHERE cd_atendimento_recadastro = ".intval($args['cd_atendimento_recadastro']).";";
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_cancelamento(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.atendimento_recadastro
			   SET motivo_cancelamento    = ".(trim($args['motivo_cancelamento']) != '' ? str_escape($args['motivo_cancelamento']) : "DEFAULT").", 
				   dt_cancelamento        = CURRENT_TIMESTAMP
			 WHERE cd_atendimento_recadastro = ".intval($args['cd_atendimento_recadastro']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function verifica_re_ano(&$result, $args=array())
	{
		$qr_sql = "
			SELECT COUNT(*) AS total
			  FROM projetos.atendimento_recadastro
			 WHERE cd_empresa             = ".intval($args['cd_empresa'])."
			   AND cd_registro_empregado  = ".intval($args['cd_registro_empregado'])."
               AND seq_dependencia        = ".intval($args['seq_dependencia'])."
			   AND dt_periodo             = ".intval($args['ano']).";";
			   
		$result = $this->db->query($qr_sql);
	}
}
?>