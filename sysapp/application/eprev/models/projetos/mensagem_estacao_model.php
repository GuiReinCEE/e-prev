<?php
class Mensagem_estacao_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT me.cd_mensagem_estacao,
		           me.nome,
		           me.arquivo,
				   me.arquivo_nome,
		           TO_CHAR(me.dt_inicio, 'DD/MM/YYYY HH24:MI') AS dt_inicio,
				   TO_CHAR(me.dt_final, 'DD/MM/YYYY HH24:MI') AS dt_final,
		           me.dt_inclusao,
				   uc.usuario
		      FROM projetos.mensagem_estacao  me
		      JOIN projetos.usuarios_controledi uc 
			    ON me.cd_usuario_inclusao = uc.codigo
			 WHERE me.dt_exclusao IS NULL
			 ".(((trim($args['dt_inicio_ini']) != "") and  (trim($args['dt_inicio_fim']) != "")) ? " AND DATE_TRUNC('day', me.dt_inicio) BETWEEN TO_DATE('".$args['dt_inicio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inicio_fim']."', 'DD/MM/YYYY')" : "")."";

		$result = $this->db->query($qr_sql);
	}
	
	function gerencias( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT codigo AS value,
                   nome AS text
		      FROM projetos.divisoes
		     WHERE tipo = 'DIV'
		     ORDER BY nome";

		$result = $this->db->query($qr_sql);
	}
	
	function carrega( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_mensagem_estacao,
		           nome,
		           arquivo,
				   arquivo_nome,
		           TO_CHAR(dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
				   TO_CHAR(dt_final, 'DD/MM/YYYY') AS dt_final,
				   TO_CHAR(dt_inicio, 'HH24:MI') AS hr_inicio,
				   TO_CHAR(dt_final, 'HH24:MI') AS hr_final,
		           dt_inclusao,
				   url
		      FROM projetos.mensagem_estacao
			 WHERE cd_mensagem_estacao = ".intval($args['cd_mensagem_estacao']);
		
		$result = $this->db->query($qr_sql);
	}
	
	function gerencia_checked( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT gerencia
		      FROM projetos.mensagem_estacao_gerencia
			 WHERE cd_mensagem_estacao = ".intval($args['cd_mensagem_estacao'])."
			 ORDER BY gerencia";
		
		$result = $this->db->query($qr_sql);
	}
	
	function salvar( &$result, $args=array() )
	{
		if(intval($args['cd_mensagem_estacao']) > 0)
		{
			$id = $args['cd_mensagem_estacao'];
			
			$qr_sql = "
				UPDATE projetos.mensagem_estacao
			       SET nome                   = ".(trim($args['nome']) != '' ? "'".$args['nome']."'" : "DEFAULT").",
				       url                    = ".(trim($args['url']) != '' ? "'".$args['url']."'" : "DEFAULT").",
					   dt_inicio              = TO_TIMESTAMP('".$args['dt_inicio']." ".$args['hr_inicio']."','DD/MM/YYYY HH24:MI'),
					   dt_final               = TO_TIMESTAMP('".$args['dt_final']." ".$args['hr_final']."','DD/MM/YYYY HH24:MI'),
					   arquivo                = ".(trim($args['arquivo']) != '' ? "'".$args['arquivo']."'" : "DEFAULT").",
					   arquivo_nome           = ".(trim($args['arquivo_nome']) != '' ? "'".$args['arquivo_nome']."'" : "DEFAULT").",
					   dt_alteracao           = CURRENT_TIMESTAMP,
					   cd_usuario_alteracao   = ".intval($args['cd_usuario'])."
				 WHERE cd_mensagem_estacao = ".intval($id)."";
				 
			$this->db->query($qr_sql);
			
			$qr_sql = "
				DELETE 
			      FROM projetos.mensagem_estacao_gerencia
				 WHERE cd_mensagem_estacao = ".intval($id);
				 
			$this->db->query($qr_sql);
		}
		else
		{
			$id = intval($this->db->get_new_id("projetos.mensagem_estacao", "cd_mensagem_estacao"));
			
			$qr_sql = "
				INSERT INTO projetos.mensagem_estacao
				     (
						cd_mensagem_estacao,
						nome,
						url,
						dt_inicio,
						dt_final,
						arquivo,
						arquivo_nome,
						cd_usuario_inclusao,
						cd_usuario_alteracao,
						dt_alteracao 
					 )
			    VALUES
					 (
						".$id.",
						".(trim($args['nome']) != '' ? "'".$args['nome']."'" : "DEFAULT").",
						".(trim($args['url']) != '' ? "'".$args['url']."'" : "DEFAULT").",
						TO_TIMESTAMP('".$args['dt_inicio']." ".$args['hr_inicio']."','DD/MM/YYYY HH24:MI'),
						TO_TIMESTAMP('".$args['dt_final']." ".$args['hr_final']."','DD/MM/YYYY HH24:MI'),
						".(trim($args['arquivo']) != '' ? "'".$args['arquivo']."'" : "DEFAULT").",
						".(trim($args['arquivo_nome']) != '' ? "'".$args['arquivo_nome']."'" : "DEFAULT").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario']).",
						CURRENT_TIMESTAMP
					 )";
			
			$this->db->query($qr_sql);
			
		}
		
		$total = count($args['ar_gerencia']);
		$i = 0;
		while($i < $total)
		{
			$qr_sql = "
				INSERT INTO projetos.mensagem_estacao_gerencia
				     (
						cd_mensagem_estacao,
						gerencia,
						cd_usuario_inclusao,
						dt_inclusao
					 )
			    VALUES
				     (
						".$id.",
						'".trim($args['ar_gerencia'][$i])."',
						".intval($args['cd_usuario']).",
						CURRENT_TIMESTAMP
					 )";
			$this->db->query($qr_sql);
			$i++;
		}
	}
		
	function excluir( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE projetos.mensagem_estacao
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_mensagem_estacao = ".intval($args['cd_mensagem_estacao']).";";
			 
		$this->db->query($qr_sql);
	}
	
	function carrega_mensagem( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT me.url,
				   me.arquivo,
				   me.arquivo_nome
		      FROM projetos.mensagem_estacao me
			  JOIN projetos.mensagem_estacao_gerencia meg 
			    ON meg.cd_mensagem_estacao = me.cd_mensagem_estacao
			  JOIN projetos.usuarios_controledi uc
			    ON uc.divisao = meg.gerencia
			 WHERE me.dt_exclusao IS NULL
			   AND CURRENT_TIMESTAMP BETWEEN me.dt_inicio AND me.dt_final 
			   AND UPPER(uc.usuario) = UPPER('".trim($args['usuario'])."')";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function temMensagem( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT COUNT(*) AS fl_mensagem
					  FROM projetos.mensagem_estacao me
					  JOIN projetos.mensagem_estacao_gerencia meg
						ON meg.cd_mensagem_estacao = me.cd_mensagem_estacao
					  JOIN projetos.usuarios_controledi uc
						ON uc.divisao = meg.gerencia
					 WHERE me.dt_exclusao IS NULL
					   AND UPPER(uc.usuario) = UPPER('".trim($args['usuario'])."')
					   AND CURRENT_TIMESTAMP BETWEEN me.dt_inicio AND me.dt_final
				  ";
			 
		$result = $this->db->query($qr_sql);
	}	
	
	function setExibiuMensagem( &$result, $args=array() )
	{
		$qr_sql = "
					INSERT INTO projetos.mensagem_estacao_log (cd_usuario)
					SELECT codigo
					  FROM projetos.usuarios_controledi
					 WHERE UPPER(usuario) = UPPER('".trim($args['usuario'])."')				
				  ";
			 
		$result = $this->db->query($qr_sql);
	}	
}
?>
