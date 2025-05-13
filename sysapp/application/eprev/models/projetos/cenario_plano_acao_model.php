<?php
class Cenario_plano_acao_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_gerencia, $args = array())
	{
		$qr_sql = " 
			SELECT cp.cd_cenario_plano_acao,
			       c.titulo,
			       cp.cd_gerencia_responsavel,
				   cp.cd_cenario,
				   cp.ds_cenario_plano_acao,
				   TO_CHAR(cp.dt_prazo_previsto,'DD/MM/YYYY') AS dt_prazo_previsto,
				   TO_CHAR(cp.dt_verificacao_eficacia,'DD/MM/YYYY') AS dt_verificacao_eficacia,
				   TO_CHAR(cp.dt_validacao_eficacia,'DD/MM/YYYY') AS dt_validacao_eficacia,
				   (SELECT TO_CHAR(cpa.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') || ' : ' || cpa.ds_cenario_plano_acao_acompanhamento
				      FROM projetos.cenario_plano_acao_acompanhamento cpa
				     WHERE cpa.cd_cenario_plano_acao = cp.cd_cenario_plano_acao
				       AND cpa.dt_exclusao IS NULL
				     ORDER BY cpa.dt_inclusao DESC
				     LIMIT 1) AS ds_acompanhamento
			  FROM projetos.cenario_plano_acao cp
			  JOIN projetos.cenario c
			    ON c.cd_cenario = cp.cd_cenario
			 WHERE cp.dt_exclusao IS NULL
			   ".(!gerencia_in(array('AI', 'GC')) ? "AND cp.cd_gerencia_responsavel = '".trim($cd_gerencia)."'" : "")."
			   ".(trim($args['cd_cenario']) != '' ? "AND cp.cd_cenario = ".intval($args['cd_cenario']) : "")."
			   ".(trim($args['cd_gerencia_responsavel']) != '' ? "AND cp.cd_gerencia_responsavel = '".trim($args['cd_gerencia_responsavel'])."'" : "")."
			   ".(((trim($args['dt_validacao_eficacia_ini']) != '') AND (trim($args['dt_validacao_eficacia_fim']) != '')) ? " AND DATE_TRUNC('day', cp.dt_validacao_eficacia) BETWEEN TO_DATE('".$args['dt_validacao_eficacia_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_validacao_eficacia_fim']."', 'DD/MM/YYYY')" : "")."
	           ".(((trim($args['dt_prazo_previsto_ini']) != '') AND (trim($args['dt_prazo_previsto_fim']) != '')) ? "AND DATE_TRUNC('day', cp.dt_prazo_previsto) BETWEEN TO_DATE('".$args['dt_prazo_previsto_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_prazo_previsto_fim']."', 'DD/MM/YYYY')" : "")."
	           ".(((trim($args['dt_verificacao_eficacia_ini']) != '') AND (trim($args['dt_verificacao_eficacia_fim']) != '')) ? "AND DATE_TRUNC('day', cp.dt_verificacao_eficacia) BETWEEN TO_DATE('".$args['dt_verificacao_eficacia_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_verificacao_eficacia_fim']."', 'DD/MM/YYYY')" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_cenario_plano_acao)
	{
		$qr_sql = "
			SELECT cp.cd_cenario_plano_acao,
				   c.titulo,
				   c.cd_edicao,
				   cp.cd_cenario,
				   cp.ds_cenario_plano_acao,
				   cp.cd_gerencia_responsavel,
				   cp.cd_atividade,
				   TO_CHAR(cp.dt_verificacao_eficacia,'DD/MM/YYYY') AS dt_verificacao_eficacia,
				   TO_CHAR(cp.dt_validacao_eficacia,'DD/MM/YYYY') AS dt_validacao_eficacia,
				   TO_CHAR(cp.dt_prazo_previsto, 'DD/MM/YYYY') AS dt_prazo_previsto,
				   TO_CHAR(cp.dt_envio_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_responsavel,
				   TO_CHAR(cp.dt_envio_auditoria, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_auditoria 
			  FROM projetos.cenario_plano_acao cp
			  JOIN projetos.cenario c
			    ON c.cd_cenario = cp.cd_cenario
			 WHERE cp.cd_cenario_plano_acao = ".intval($cd_cenario_plano_acao).";";

		return $this->db->query($qr_sql)->row_array();										
	}

	public function atualiza($cd_cenario_plano_acao, $args = array())
	{
		$qr_sql = " 
 			UPDATE projetos.cenario_plano_acao
 			   SET ds_cenario_plano_acao   = ".(trim($args['ds_cenario_plano_acao']) != '' ? "'".trim($args['ds_cenario_plano_acao'])."'" : "DEFAULT").",
 			   	   dt_prazo_previsto       = ".(trim($args['dt_prazo_previsto']) != '' ? "TO_DATE('".trim($args['dt_prazo_previsto'])."', 'DD/MM/YYYY')" : "DEFAULT").",
 			   	   dt_verificacao_eficacia = ".(trim($args['dt_verificacao_eficacia']) != '' ? "TO_DATE('".trim($args['dt_verificacao_eficacia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
 			   	   dt_validacao_eficacia   = ".(trim($args['dt_validacao_eficacia']) != '' ? "TO_DATE('".trim($args['dt_validacao_eficacia'])."', 'DD/MM/YYYY')" : "DEFAULT")."
 			 WHERE cd_cenario_plano_acao = ".intval($cd_cenario_plano_acao).";";
		
		$this->db->query($qr_sql);
	}

	public function envio_responsavel($cd_cenario_plano_acao, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE projetos.cenario_plano_acao
			   SET cd_usuario_envio_responsavel = ".intval($cd_usuario).",
			   	   dt_envio_responsavel         = CURRENT_TIMESTAMP
			 WHERE cd_cenario_plano_acao = ".intval($cd_cenario_plano_acao).";";

		$this->db->query($qr_sql);
	}

	public function envio_auditoria($cd_cenario_plano_acao, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE projetos.cenario_plano_acao
			   SET cd_usuario_envio_auditoria = ".intval($cd_usuario).",
			   	   dt_envio_auditoria         = CURRENT_TIMESTAMP
			 WHERE cd_cenario_plano_acao = ".intval($cd_cenario_plano_acao).";";

		$this->db->query($qr_sql);
	}

	public function get_titulo()
	{
		$qr_sql = "
   			SELECT DISTINCT cp.cd_cenario AS value,
                   cp.cd_cenario ||' - '|| c.titulo  AS text
	          FROM projetos.cenario_plano_acao cp
	          JOIN projetos.cenario c
                ON c.cd_cenario = cp.cd_cenario
	         WHERE cp.dt_exclusao IS NULL
             ORDER BY 2 DESC";

        return $this->db->query($qr_sql)->result_array();
	}

	public function get_gerencia()
	{
		$qr_sql = "
			 SELECT DISTINCT cpc.cd_gerencia_responsavel AS value, 
			 	   d.nome AS text
              FROM projetos.cenario_plano_acao cpc
              JOIN projetos.divisoes d
                ON d.codigo = cpc.cd_gerencia_responsavel
             WHERE cpc.dt_exclusao IS NULL
             ORDER BY d.nome ASC";

        return $this->db->query($qr_sql)->result_array();
	}

	public function salvar_acompanhamento($args = array())
	{
		$qr_sql = "
			INSERT INTO projetos.cenario_plano_acao_acompanhamento
			     (
			       cd_cenario_plano_acao,
			       ds_cenario_plano_acao_acompanhamento,
			       cd_usuario_inclusao,
               	   cd_usuario_alteracao
			     )                  
			VALUES
			     (
			        ".intval($args['cd_cenario_plano_acao']).",
			     	".(trim($args['ds_cenario_plano_acao_acompanhamento']) != '' ? str_escape($args['ds_cenario_plano_acao_acompanhamento']) : "DEFAULT").",                    		
				    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
			     );";

        $this->db->query($qr_sql);
	} 	

    public function listar_acompanhamento($cd_cenario_plano_acao)
    {
    	$qr_sql = "
			SELECT cd_cenario_plano_acao_acompanhamento,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       ds_cenario_plano_acao_acompanhamento,
			       cd_usuario_inclusao,
			       funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
			  FROM projetos.cenario_plano_acao_acompanhamento
			 WHERE dt_exclusao IS NULL
			   AND cd_cenario_plano_acao = ".intval($cd_cenario_plano_acao).";";

    	return $this->db->query($qr_sql)->result_array();
    }

 	public function excluir_acompanhamento($cd_cenario_plano_acao_acompanhamento, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.cenario_plano_acao_acompanhamento
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_cenario_plano_acao_acompanhamento = ".intval($cd_cenario_plano_acao_acompanhamento).";";

        $this->db->query($qr_sql);
    }

	public function anexo_listar($cd_cenario_plano_acao)
	{
		$qr_sql = "
			SELECT cd_cenario_plano_acao_anexo,
			       arquivo,
			       arquivo_nome,
			       cd_usuario_inclusao,
			       TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
			  FROM projetos.cenario_plano_acao_anexo 
			 WHERE cd_cenario_plano_acao = ".intval($cd_cenario_plano_acao)."
			   AND dt_exclusao IS NULL
			 ORDER BY dt_inclusao DESC; ";

		return $this->db->query($qr_sql)->result_array();
	}

	public function anexo_salvar($cd_cenario_plano_acao, $args = array())
	{
		$qr_sql = "
			INSERT INTO projetos.cenario_plano_acao_anexo 
			     (
					cd_cenario_plano_acao,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				 )
		    VALUES
			     (
					".intval($cd_cenario_plano_acao).",
					'".trim($args['arquivo'])."',
					'".trim($args['arquivo_nome'])."',
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				 )";

		 $this->db->query($qr_sql);
	}

	public function anexo_excluir($cd_cenario_plano_acao_anexo, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.cenario_plano_acao_anexo 
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_cenario_plano_acao_anexo = ".intval($cd_cenario_plano_acao_anexo).";";

		$this->db->query($qr_sql);
	}

	public function atualiza_prazo_previsto($cd_atividade, $cd_cenario, $args = array())
	{
		$qr_sql = "
            UPDATE projetos.atividades 
               SET dt_prevista_implementacao_norma_legal = TO_DATE('".trim($args['dt_prazo_previsto'])."', 'DD/MM/YYYY')
             WHERE numero = ".intval($cd_atividade).";

            UPDATE projetos.cenario
               SET dt_prevista      = TO_DATE('".trim($args['dt_prazo_previsto'])."', 'DD/MM/YYYY')
             WHERE cd_cenario = ".intval($cd_cenario).";";

        $this->db->query($qr_sql);
	}

	public function atualiza_implementacao($cd_atividade, $cd_cenario, $args = array())
	{
		$qr_sql = "
            UPDATE projetos.atividades 
               SET dt_implementacao_norma_legal = TO_DATE('".trim($args['dt_verificacao_eficacia'])."', 'DD/MM/YYYY')
             WHERE numero = ".intval($cd_atividade).";

            UPDATE projetos.cenario
               SET dt_implementacao = TO_DATE('".trim($args['dt_verificacao_eficacia'])."', 'DD/MM/YYYY')
             WHERE cd_cenario = ".intval($cd_cenario).";";

        $this->db->query($qr_sql);
	}

	public function get_usuario_responsavel($cd_gerencia)
	{
		$qr_sql = "
			SELECT uc.usuario || '@eletroceee.com.br' AS ds_email
			  FROM projetos.usuarios_controledi uc
			 WHERE uc.indic_03 = '*'
			   AND uc.divisao  = '".trim($cd_gerencia)."' 
			   AND uc.tipo     NOT IN ('X', 'T');";

		return $this->db->query($qr_sql)->result_array();
	}
}