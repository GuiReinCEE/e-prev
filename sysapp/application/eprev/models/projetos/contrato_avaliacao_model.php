<?php
class Contrato_avaliacao_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT a.cd_contrato_avaliacao,
			       b.ds_empresa,
			       b.ds_servico,
				   b.seq_contrato,
			       TO_CHAR(a.dt_inicio_avaliacao,'DD/MM/YYYY') AS dt_inicio_avaliacao,
			       TO_CHAR(a.dt_fim_avaliacao,'DD/MM/YYYY') AS dt_fim_avaliacao,
			       TO_CHAR(a.dt_limite_avaliacao,'DD/MM/YYYY') AS dt_limite_avaliacao,
			       TO_CHAR(a.dt_integracao_oracle,'DD/MM/YYYY HH24:MI:SS') AS dt_integracao_oracle,
			       TO_CHAR(a.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       c.nome AS nome_usuario_inclusao,
			       COALESCE(d.vl_resultado,0) AS vl_resultado,
				   b.gestor_contrato,
				   (SELECT COUNT(DISTINCT(cai.cd_usuario_avaliador))
					  FROM projetos.contrato_avaliacao_item cai
					 WHERE cai.cd_contrato_avaliacao = a.cd_contrato_avaliacao
					   AND cai.dt_exclusao IS NULL) AS qt_avaliador
		      FROM projetos.contrato_avaliacao a
		      JOIN projetos.contrato b 
		        ON a.cd_contrato = b.cd_contrato
		      JOIN projetos.usuarios_controledi c 
		        ON a.cd_usuario_inclusao = c.codigo
              LEFT JOIN consultas.contrato_resultado_final d 
			    ON d.cd_contrato_avaliacao = a.cd_contrato_avaliacao
		     WHERE a.dt_exclusao IS NULL
			 ".(trim($args['ds_empresa']) != '' ? "AND UPPER(b.ds_empresa) LIKE UPPER('%".trim($args['ds_empresa'])."%')" : '')."
			 ".(trim($args['ds_servico']) != '' ? "AND UPPER(b.ds_servico) LIKE UPPER('%".trim($args['ds_servico'])."%')" : '')."
			 ".(((trim($args['dt_inicio_ini']) != "") AND (trim($args['dt_inicio_fim']) != "")) ? " AND DATE_TRUNC('day', dt_inicio_avaliacao) BETWEEN TO_DATE('".$args['dt_inicio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inicio_fim']."', 'DD/MM/YYYY')" : "")."
			 ".(((trim($args['dt_fim_ini']) != "") AND (trim($args['dt_fim_fim']) != "")) ? " AND DATE_TRUNC('day', dt_fim_avaliacao) BETWEEN TO_DATE('".$args['dt_fim_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim_fim']."', 'DD/MM/YYYY')" : "")."
			 ".(((trim($args['dt_limite_ini']) != "") AND (trim($args['dt_limite_fim']) != "")) ? " AND DATE_TRUNC('day', dt_limite_avaliacao) BETWEEN TO_DATE('".$args['dt_limite_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_limite_fim']."', 'DD/MM/YYYY')" : "").";";
	
		$result = $this->db->query($qr_sql);
	}
	
	function carrega( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT a.cd_contrato_avaliacao, 
				   a.cd_contrato_formulario, 
				   a.cd_contrato, 
				   TO_CHAR(a.dt_inicio_avaliacao,'DD/MM/YYYY') AS dt_inicio_avaliacao, 
				   TO_CHAR(a.dt_fim_avaliacao,'DD/MM/YYYY') AS dt_fim_avaliacao,
				   TO_CHAR(a.dt_limite_avaliacao,'DD/MM/YYYY') AS dt_limite_avaliacao, 
				   a.dt_envio_email,
				   c.ds_empresa || ' - ' || c.ds_servico AS ds_contrato
			  FROM projetos.contrato_avaliacao a
			  JOIN projetos.contrato c
			    ON c.cd_contrato = a.cd_contrato
			 WHERE a.cd_contrato_avaliacao = ".$args['cd_contrato_avaliacao'].";";
	
		$result = $this->db->query($qr_sql);
	}

	function salvar( &$result, $args=array() )
	{
		if(intval($args['cd_contrato_avaliacao']) > 0)
		{
			$cd_contrato_avaliacao = $args['cd_contrato_avaliacao'];
			
			$qr_sql = "
				UPDATE projetos.contrato_avaliacao
			       SET ".(trim($args['cd_contrato_formulario']) != '' ? "cd_contrato_formulario = ".intval($args['cd_contrato_formulario'])."," : "")."
				       dt_inicio_avaliacao    = TO_DATE('".trim($args['dt_inicio_avaliacao'])."', 'DD/MM/YYYY'),
				       dt_fim_avaliacao       = TO_DATE('".trim($args['dt_fim_avaliacao'])."', 'DD/MM/YYYY'),
				       dt_limite_avaliacao    = TO_DATE('".trim($args['dt_limite_avaliacao'])."', 'DD/MM/YYYY'),
				       cd_contrato            = ".intval($args['cd_contrato']).",
					   cd_usuario_alteracao   = ".intval($args['cd_usuario']).",
					   dt_alteracao           = CURRENT_TIMESTAMP
		 	     WHERE cd_contrato_avaliacao = ".intval($args['cd_contrato_avaliacao'])."
			";
		}
		else
		{
			$cd_contrato_avaliacao = intval($this->db->get_new_id("projetos.contrato_avaliacao", "cd_contrato_avaliacao"));
			
			$qr_sql = "
				INSERT INTO projetos.contrato_avaliacao 
					 ( 
						cd_contrato_avaliacao,
						cd_contrato_formulario,
						dt_inicio_avaliacao,
						dt_fim_avaliacao,
						dt_limite_avaliacao,
						cd_contrato,
						cd_usuario_inclusao,
						cd_usuario_alteracao,
						dt_inclusao,
						dt_alteracao
						
					 ) 
				VALUES 
					 (
						".$cd_contrato_avaliacao.",
						".intval($args['cd_contrato_formulario']).",
						TO_DATE('".trim($args['dt_inicio_avaliacao'])."', 'DD/MM/YYYY'),
						TO_DATE('".trim($args['dt_fim_avaliacao'])."', 'DD/MM/YYYY'),
						TO_DATE('".trim($args['dt_limite_avaliacao'])."', 'DD/MM/YYYY'),
						".intval($args['cd_contrato']).",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario']).",
						CURRENT_TIMESTAMP,
						CURRENT_TIMESTAMP
					 );";
		}

		$this->db->query($qr_sql);
		
		return $cd_contrato_avaliacao;
	}
	
	function excluir( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE projetos.contrato_avaliacao
		       SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
			       dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_contrato_avaliacao = ".intval($args['cd_contrato_avaliacao']).";";
			 
		$this->db->query($qr_sql);
	}
	
	function grupos( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT a.cd_contrato_formulario_grupo,
			       a.ds_contrato_formulario_grupo
			  FROM projetos.contrato_formulario_grupo a
			 WHERE a.cd_contrato_formulario = ".intval($args['cd_contrato_formulario'])."
			   AND dt_exclusao IS NULL
			 ORDER BY a.nr_ordem";
			
		$result = $this->db->query($qr_sql);
	}
	
	function avaliadores( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT b.nome,
                   b.divisao,
				   a.cd_contrato_avaliacao_item				   
			  FROM projetos.contrato_avaliacao_item a
			  JOIN projetos.usuarios_controledi b 
			    ON a.cd_usuario_avaliador = b.codigo 
			 WHERE cd_contrato_formulario_grupo = ".intval($args['cd_contrato_formulario_grupo'])."
			   AND cd_contrato_avaliacao        = ".intval($args['cd_contrato_avaliacao'])."
			   AND dt_exclusao IS NULL;";

		$result = $this->db->query($qr_sql);
	}
	
	function salvar_avaliador( &$result, $args=array() )
	{
		$qr_sql = "
			INSERT INTO projetos.contrato_avaliacao_item 
				 (
					cd_contrato_avaliacao, 
					cd_divisao, 
					cd_usuario_avaliador, 
					cd_contrato_formulario_grupo, 
					cd_usuario_inclusao, 
					dt_inclusao
				
				 ) 
			VALUES 
				 (
					".intval($args['cd_contrato_avaliacao']).",
					'".trim($args['cd_divisao'])."',
					".intval($args['cd_usuario_avaliador']).",
					".intval($args['cd_contrato_formulario_grupo']).",
					".intval($args['cd_usuario']).",
					CURRENT_TIMESTAMP
				 );";
				 
		$this->db->query($qr_sql);
	}
	
	function excluir_avaliador( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE projetos.contrato_avaliacao_item 
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_contrato_avaliacao_item = ".intval($args['cd_contrato_avaliacao_item']).";";
			 
		$this->db->query($qr_sql);
	}
	
	function enviar_email( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT rotinas.contrato_avaliacao(".intval($args['cd_contrato_avaliacao']).");";
			
		$this->db->query($qr_sql);
	}
	
	function listar_avaliadores( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_divisao, 
				   nome, 
				   CASE WHEN fl_avaliou = 'N' THEN 'Não'
				        ELSE 'Sim'
				   END AS avaliou,
				   TO_CHAR(dt_resposta,'DD/MM/YYYY HH24:MI:SS') AS dt_resposta
			  FROM consultas.contrato_resultado_controle
			 WHERE cd_contrato_avaliacao = ".intval($args['cd_contrato_avaliacao']).";";
		 
		$result = $this->db->query($qr_sql);
	}
	
	function listar_respostas( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT ds_contrato_formulario_grupo, 
			       ds_contrato_formulario_pergunta, 
				   cd_divisao, 
				   ds_resposta, 
		 		   COUNT(*) AS total
		      FROM consultas.contrato_resultado_resposta 
			 WHERE cd_contrato_avaliacao = ".intval($args['cd_contrato_avaliacao'])."
		     GROUP BY ds_contrato_formulario_grupo, 
			          ds_contrato_formulario_pergunta, 
					  cd_divisao, 
					  ds_resposta, 
					  grupo_ordem,
					  pergunta_ordem, 
					  resposta_ordem
		     ORDER BY grupo_ordem, 
			          pergunta_ordem, 
					  cd_divisao, 
					  resposta_ordem;";
		 
		$result = $this->db->query($qr_sql);
	}
	
	function pontuacao_final( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT vl_resultado
			  FROM consultas.contrato_resultado_final
		     WHERE cd_contrato_avaliacao = ".intval($args['cd_contrato_avaliacao']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function reabrir( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE projetos.contrato_avaliacao
			   SET cd_usuario_alteracao = ".intval($args['cd_usuario']).",
			       dt_alteracao         = CURRENT_TIMESTAMP,
				   dt_envio_email       = NULL
			 WHERE cd_contrato_avaliacao = ".intval($args['cd_contrato_avaliacao']).";";
			 
		$result = $this->db->query($qr_sql);
	}
}
?>