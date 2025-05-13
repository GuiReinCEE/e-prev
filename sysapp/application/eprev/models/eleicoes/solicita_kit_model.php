<?php
class Solicita_kit_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT sk.cd_solicita_kit,
				   TO_CHAR(sk.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(sk.dt_envio, 'DD/MM/YYYY') AS dt_envio,
				   sk.cd_empresa || '/' || sk.cd_registro_empregado  || '/' || sk.seq_dependencia AS re,
				   p.nome,
				   fl_endereco_atualizado,
				   skt.ds_solicita_kit_tipo,
				   uc.nome AS solicitante,
				   uc2.nome AS enviado
			  FROM eleicoes.solicita_kit sk
			  JOIN eleicoes.solicita_kit_tipo skt
				ON sk.cd_solicita_kit_tipo = skt.cd_solicita_kit_tipo 
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = sk.cd_usuario_inclusao
			  LEFT JOIN projetos.usuarios_controledi uc2
				ON uc2.codigo = sk.cd_usuario_envio
			  JOIN public.participantes p
				ON p.cd_empresa = sk.cd_empresa
			 WHERE sk.dt_exclusao IS NULL
			   AND skt.dt_exclusao IS NULL
			   AND p.cd_registro_empregado = sk.cd_registro_empregado
			   AND p.seq_dependencia = sk.seq_dependencia 
			   ".(trim($args['cd_solicita_kit_tipo']) != '' ? "AND skt.cd_solicita_kit_tipo = ".intval($args["cd_solicita_kit_tipo"] ) : '')."
			   ".(trim($args['fl_enviado']) == 'S' ? "AND sk.dt_envio IS NOT NULL" : '')."
			   ".(trim($args['fl_enviado']) == 'N' ? "AND sk.dt_envio IS NULL" : '')."
			   ".(trim($args['cd_empresa']) != '' ? "AND p.cd_empresa = ".intval($args["cd_empresa"] ) : '')."
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND p.cd_registro_empregado = ".intval($args["cd_registro_empregado"] ) : '')."
			   ".(trim($args['seq_dependencia']) != '' ? "AND p.seq_dependencia = ".intval($args["seq_dependencia"] ) : '')."
			   ".(trim($args['nome']) != '' ? "AND UPPER(p.nome) LIKE UPPER('%".trim($args["nome"]."%')" ) : '')."
			   ".(trim($args['cd_usuario_inclusao']) != '' ? "AND sk.cd_usuario_inclusao = ".intval($args["cd_usuario_inclusao"] ) : '')."
			   ".(trim($args['cd_usuario_envio']) != '' ? "AND sk.cd_usuario_envio = ".intval($args["cd_usuario_envio"] ) : '')."
			   " . (((trim($args['dt_solicitacao_ini']) != "") and (trim($args['dt_solicitacao_fim']) != "")) ? " AND DATE_TRUNC('day', sk.dt_inclusao) BETWEEN TO_DATE('" . $args['dt_solicitacao_ini'] . "', 'DD/MM/YYYY') AND TO_DATE('" . $args['dt_solicitacao_fim'] . "', 'DD/MM/YYYY')" : "") . "
			   " . (((trim($args['dt_envio_ini']) != "") and (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', sk.dt_envio) BETWEEN TO_DATE('" . $args['dt_envio_ini'] . "', 'DD/MM/YYYY') AND TO_DATE('" . $args['dt_envio_fim'] . "', 'DD/MM/YYYY')" : "") . "
			 ORDER BY sk.dt_inclusao DESC;";

		$result = $this->db->query($qr_sql);
	}
	
	function solicitantes( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_usuario_inclusao AS value,
				   uc.nome AS text
			  FROM eleicoes.solicita_kit sk
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = sk.cd_usuario_inclusao
		     WHERE sk.dt_exclusao IS NULL
		     ORDER BY uc.nome ASC;";

		$result = $this->db->query($qr_sql);
	
	}
	
	function enviados( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_usuario_envio AS value,
				   uc.nome AS text
			  FROM eleicoes.solicita_kit sk
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = sk.cd_usuario_envio
			 WHERE sk.dt_exclusao IS NULL
			 ORDER BY uc.nome ASC;";

		$result = $this->db->query($qr_sql);
	}
	
	function enviar( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE eleicoes.solicita_kit
			   SET dt_envio = CURRENT_TIMESTAMP,
			       cd_usuario_envio = ".intval($args['cd_usuario'])."
			 WHERE cd_solicita_kit = ".intval($args['cd_solicita_kit']).";";

		$result = $this->db->query($qr_sql);
	}
	
	function tipo( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_solicita_kit_tipo AS value,
				   ds_solicita_kit_tipo AS text
			  FROM eleicoes.solicita_kit_tipo
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_solicita_kit_tipo ASC;";

		$result = $this->db->query($qr_sql);
	}
	
	function eleicao( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT id_eleicao AS value,
				   ano_eleicao || '-' || cd_eleicao AS text 
			  FROM eleicoes.eleicao
			 ORDER BY ano_eleicao DESC;";

		$result = $this->db->query($qr_sql);
	}
	
	function salvar( &$result, $args=array() )
	{
		if(intval($args['cd_solicita_kit']) > 0)
		{
			$qr_sql = "
				UPDATE eleicoes.solicita_kit
				   SET cd_empresa             = ".intval($args['cd_empresa']).",
					   cd_registro_empregado  = ".intval($args['cd_registro_empregado']).",
					   seq_dependencia        = ".intval($args['seq_dependencia']).",
					   cd_solicita_kit_tipo   = ".intval($args['cd_solicita_kit_tipo']).",
					   fl_endereco_atualizado = '".trim($args['fl_endereco_atualizado'])."'
			     WHERE cd_solicita_kit       = ".intval($args['cd_solicita_kit'])."";
		}
		else
		{
			$qr_sql = "
				INSERT INTO eleicoes.solicita_kit
				     (
						cd_empresa,
						cd_registro_empregado,
						seq_dependencia,
						cd_solicita_kit_tipo,
						fl_endereco_atualizado,
						id_eleicao,
						cd_usuario_inclusao
					 )
			    VALUES
				     (
						".intval($args['cd_empresa']).",
						".intval($args['cd_registro_empregado']).",
						".intval($args['seq_dependencia']).",
						".intval($args['cd_solicita_kit_tipo']).",
						'".trim($args['fl_endereco_atualizado'])."',
						".intval($args['cd_eleicao']).",
						".intval($args['cd_usuario'])."
					 )";
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	function verifica_cadatro( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT COUNT(*) AS total
			  FROM eleicoes.cadastros_eleicoes ce
			 WHERE ce.ano_eleicao = (SELECT e.ano_eleicao
									   FROM eleicoes.eleicao e
									  WHERE e.id_eleicao = ".intval($args['cd_eleicao']).")
			   AND ce.cd_registro_empregado = ".intval($args['cd_registro_empregado'])."
			   AND ce.cd_empresa            = ".intval($args['cd_empresa'])."
			   AND ce.seq_dependencia          = ".intval($args['seq_dependencia']);
		$result = $this->db->query($qr_sql);
	}
	
}
?>