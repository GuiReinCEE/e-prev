<?php
class Formulario_periodo_experiencia_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT fps.cd_formulario_periodo_experiencia_solic,
			       funcoes.get_usuario_nome(fps.cd_usuario_avaliador) AS ds_avaliador,
			       funcoes.get_usuario_nome(fps.cd_usuario_avaliado) AS ds_avaliado,
			       TO_CHAR(fps.dt_limite, 'DD/MM/YYYY') AS dt_limite,
			       TO_CHAR(fps.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       TO_CHAR(fps.dt_resposta, 'DD/MM/YYYY HH24:MI:SS') AS dt_resposta,
			       (CASE WHEN fps.dt_resposta IS NOT NULL THEN 'Sim'
	                     ELSE 'Não'
                   END) AS ds_resposta,
                   (CASE WHEN fps.dt_resposta IS NOT NULL THEN 'label label-success'
	                     ELSE 'label label-important'
                   END) AS ds_class_resposta,
			       fp.ds_formulario_periodo_experiencia,
			       fps.arquivo,
			       fps.arquivo_nome
			  FROM projetos.formulario_periodo_experiencia_solic fps
			  JOIN projetos.formulario_periodo_experiencia fp
			    ON fp.cd_formulario_periodo_experiencia = fps.cd_formulario_periodo_experiencia
			 WHERE fps.dt_exclusao IS NULL
			   ".(((trim($args['dt_inclusao_ini']) != '') AND (trim($args['dt_inclusao_fim']) != '')) ? " AND DATE_TRUNC('day', fps.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_limite_ini']) != '') AND (trim($args['dt_limite_fim']) != '')) ? " AND DATE_TRUNC('day', fps.dt_limite) BETWEEN TO_DATE('".$args['dt_limite_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_limite_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(trim($args['cd_usuario_avaliador']) != '' ? "AND fps.cd_usuario_avaliador = '".intval($args['cd_usuario_avaliador'])."'" : "")."
			   ".(trim($args['cd_usuario_avaliado']) != '' ? "AND fps.cd_usuario_avaliado = '".intval($args['cd_usuario_avaliado'])."'" : "")."
			   ".(trim($args['fl_resposta']) == 'S' ? "AND fps.dt_resposta IS NOT NULL" : "")."
               ".(trim($args['fl_resposta']) == 'N' ? "AND fps.dt_resposta IS NULL" : "")."
			 ORDER BY fps.dt_inclusao DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_emails($cd_usuario)
	{
		$qr_sql = " 
			SELECT DISTINCT funcoes.get_usuario(cd_usuario_avaliador) || '@eletroceee.com.br' AS para
			  FROM projetos.formulario_periodo_experiencia_solic			
			 WHERE cd_usuario_avaliador = ".intval($cd_usuario).";";
		
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_formulario_periodo_experiencia_solic)
	{
		$qr_sql = "
			SELECT fps.cd_formulario_periodo_experiencia_solic,
			       fps.cd_usuario_avaliador,
			       fps.cd_usuario_avaliado,
			       funcoes.get_usuario_nome(cd_usuario_avaliado) AS ds_avaliado,
			       funcoes.get_usuario_nome(cd_usuario_avaliador) AS ds_avaliador,
			       funcoes.get_usuario_area(cd_usuario_avaliador) AS ds_gerencia,
			       TO_CHAR(fps.dt_limite, 'DD/MM/YYYY') AS dt_limite,
			       TO_CHAR(fps.dt_resposta, 'DD/MM/YYYY HH24:MI:SS') AS dt_resposta,
			       TO_CHAR(fps.dt_resposta, 'DD/MM/YYYY') AS ds_resposta,
			       fp.cd_formulario_periodo_experiencia,
			       fps.ds_formulario,
			       fp.ds_formulario_periodo_experiencia,
			       uc.nome AS ds_usuario_avaliado,
			       c.nome_cargo AS ds_cargo,
			       TO_CHAR(COALESCE(uc.dt_admissao, um.dt_admissao),'DD/MM/YYYY') AS dt_admissao,
			       uc.divisao,
			       fp.ds_descricao,
			       fps.ds_resposta,
			       fps.arquivo,
			       fps.arquivo_nome
			  FROM projetos.formulario_periodo_experiencia_solic fps
			  JOIN projetos.formulario_periodo_experiencia fp
			    ON fp.cd_formulario_periodo_experiencia = fps.cd_formulario_periodo_experiencia
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = fps.cd_usuario_avaliado
			  LEFT JOIN projetos.usuario_matriz um
			    ON um.cd_usuario = uc.codigo
			  LEFT JOIN projetos.cargos c
                ON c.cd_cargo = uc.cd_cargo
			 WHERE fps.cd_formulario_periodo_experiencia_solic = ".intval($cd_formulario_periodo_experiencia_solic).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_formulario_periodo_experiencia_solic = intval($this->db->get_new_id('projetos.formulario_periodo_experiencia_solic', 'cd_formulario_periodo_experiencia_solic'));

		$qr_sql = " 	
			INSERT INTO projetos.formulario_periodo_experiencia_solic 
                 ( 
                    cd_formulario_periodo_experiencia_solic,
				    cd_formulario_periodo_experiencia,
				    cd_usuario_avaliador, 
				    cd_usuario_avaliado, 
				    dt_limite, 
				    ds_formulario,
				    arquivo,
				    arquivo_nome,
				    cd_usuario_inclusao, 
				    cd_usuario_alteracao 
    
                 )
            VALUES 
                 (
                    ".intval($cd_formulario_periodo_experiencia_solic).",
			        ".(trim($args['cd_formulario_periodo_experiencia']) != '' ? intval($args['cd_formulario_periodo_experiencia']) : "DEFAULT").",
			        ".(trim($args['cd_usuario_avaliador']) != '' ? intval($args['cd_usuario_avaliador']) : "DEFAULT").",
			        ".(trim($args['cd_usuario_avaliado']) != '' ? intval($args['cd_usuario_avaliado']) : "DEFAULT").",
			        ".(trim($args['dt_limite']) != '' ? "TO_TIMESTAMP('".trim($args['dt_limite'])."', 'DD/MM/YYYY')" : "DEFAULT").",
			      	".(trim($args['ds_formulario']) != '' ? str_escape($args['ds_formulario']) : "DEFAULT").",
			      	".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
			      	".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
			      	".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 )";

        $this->db->query($qr_sql);

        return $cd_formulario_periodo_experiencia_solic;
	}

	public function atualizar($cd_formulario_periodo_experiencia_solic, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.formulario_periodo_experiencia_solic 
			   SET cd_formulario_periodo_experiencia = ".(trim($args['cd_formulario_periodo_experiencia']) != '' ? intval($args['cd_formulario_periodo_experiencia']) : "DEFAULT").",
			   	   cd_usuario_avaliador              = ".(trim($args['cd_usuario_avaliador']) != '' ? intval($args['cd_usuario_avaliador']) : "DEFAULT").",
			       cd_usuario_avaliado               = ".(trim($args['cd_usuario_avaliado']) != '' ? intval($args['cd_usuario_avaliado']) : "DEFAULT").",
			       dt_limite                         = ".(trim($args['dt_limite']) != '' ? "TO_DATE('".$args['dt_limite']."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
               	   ds_formulario 					 = ".(trim($args['ds_formulario']) != '' ? str_escape($args['ds_formulario']) : "DEFAULT").",
               	   arquivo                           = ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
				   arquivo_nome                      = ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
               	   cd_usuario_alteracao              = ".intval($args['cd_usuario']).", 
			       dt_alteracao                      =  CURRENT_TIMESTAMP                   
             WHERE cd_formulario_periodo_experiencia_solic = ".intval($cd_formulario_periodo_experiencia_solic).";";

		$this->db->query($qr_sql);
	} 

	public function get_formulario()
	{
		$qr_sql = "
			SELECT cd_formulario_periodo_experiencia AS value,
				   ds_formulario_periodo_experiencia AS text
			  FROM projetos.formulario_periodo_experiencia
			 ORDER BY ds_formulario_periodo_experiencia DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_usuario()
	{
		$qr_sql = "
			SELECT codigo AS value,
				   nome AS text
			  FROM projetos.usuarios_controledi 
			 WHERE tipo NOT IN ('X', 'T') 
			 ORDER BY nome ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

    public function get_avaliado()
    {
        $qr_sql = "
			SELECT DISTINCT cd_usuario_avaliado AS value, 
			       funcoes.get_usuario_nome(cd_usuario_avaliado) AS text
			  FROM projetos.formulario_periodo_experiencia_solic
			 WHERE dt_exclusao IS NULL
			 ORDER BY 2;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_avaliador()
    {
    	$qr_sql = "
			SELECT DISTINCT cd_usuario_avaliador AS value, 
			       funcoes.get_usuario_nome(cd_usuario_avaliador) AS text
			  FROM projetos.formulario_periodo_experiencia_solic
			 WHERE dt_exclusao IS NULL
			 ORDER BY 2;";
	    
	    return $this->db->query($qr_sql)->result_array();	
    }

    public function listar_perguntas($cd_formulario_periodo_experiencia_grupo)
    {
		$qr_sql = "
			 SELECT cd_formulario_periodo_experiencia_pergunta,
			        nr_ordem  ||' - '|| ds_formulario_periodo_experiencia_pergunta AS ds_formulario_periodo_experiencia_pergunta
			   FROM projetos.formulario_periodo_experiencia_pergunta
			  WHERE cd_formulario_periodo_experiencia_grupo = ".intval($cd_formulario_periodo_experiencia_grupo)."
			  ORDER BY nr_ordem ASC;";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function listar_grupos($cd_formulario_periodo_experiencia)
    {
    	$qr_sql = "
			SELECT cd_formulario_periodo_experiencia_grupo,
			       nr_ordem ||' - '|| ds_formulario_periodo_experiencia_grupo AS ds_formulario_periodo_experiencia_grupo
			  FROM projetos.formulario_periodo_experiencia_grupo 
			 wHERE cd_formulario_periodo_experiencia = ".intval($cd_formulario_periodo_experiencia)."
			 ORDER BY nr_ordem ASC;"; 
			 
    	return $this->db->query($qr_sql)->result_array();
    }

    public function get_avaliado_minhas($cd_usuario)
    {
        $qr_sql = "
			SELECT DISTINCT cd_usuario_avaliado AS value, 
			       funcoes.get_usuario_nome(cd_usuario_avaliado) AS text
			  FROM projetos.formulario_periodo_experiencia_solic
			 WHERE cd_usuario_avaliador = ".intval($cd_usuario)."
			   AND dt_exclusao IS NULL
			 ORDER BY 2;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function listar_minhas($cd_usuario, $args = array())
	{
		$qr_sql = "
			SELECT fps.cd_formulario_periodo_experiencia_solic,
			       funcoes.get_usuario_nome(fps.cd_usuario_avaliado) AS ds_avaliado,
			       TO_CHAR(fps.dt_limite, 'DD/MM/YYYY') AS dt_limite,
			       TO_CHAR(fps.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       TO_CHAR(fps.dt_resposta, 'DD/MM/YYYY HH24:MI:SS') AS dt_resposta,
			       (CASE WHEN fps.dt_resposta IS NOT NULL THEN 'Sim'
	                     ELSE 'Não'
                   END) AS ds_resposta,
                   (CASE WHEN fps.dt_resposta IS NOT NULL THEN 'label label-success'
	                     ELSE 'label label-important'
                   END) AS ds_class_resposta,
                   fps.cd_usuario_avaliado,
                   fp.cd_formulario_periodo_experiencia,
			       fp.ds_formulario_periodo_experiencia,
			       fps.arquivo,
			       fps.arquivo_nome
			  FROM projetos.formulario_periodo_experiencia_solic fps
			  JOIN projetos.formulario_periodo_experiencia fp
			    ON fp.cd_formulario_periodo_experiencia = fps.cd_formulario_periodo_experiencia
			 WHERE fps.cd_usuario_avaliador = ".intval($cd_usuario)."
			   AND fps.dt_exclusao IS NULL
			   ".(((trim($args['dt_inclusao_ini']) != '') AND (trim($args['dt_inclusao_fim']) != '')) ? " AND DATE_TRUNC('day', fps.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_limite_ini']) != '') AND (trim($args['dt_limite_fim']) != '')) ? " AND DATE_TRUNC('day', fps.dt_limite) BETWEEN TO_DATE('".$args['dt_limite_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_limite_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(trim($args['cd_usuario_avaliado']) != '' ? "AND fps.cd_usuario_avaliado = '".intval($args['cd_usuario_avaliado'])."'" : "")."
			   ".(trim($args['fl_resposta']) == 'S' ? "AND fps.dt_resposta IS NOT NULL" : "")."
               ".(trim($args['fl_resposta']) == 'N' ? "AND fps.dt_resposta IS NULL" : "")."
			 ORDER BY fps.dt_inclusao DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function responder($cd_formulario_periodo_experiencia_solic, $ds_resposta, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.formulario_periodo_experiencia_solic
			   SET ds_resposta          = ".(trim($ds_resposta) != '' ? "'".trim($ds_resposta)."'" : "DEFAULT").",
			       cd_usuario_alteracao = ".intval($cd_usuario).",
			       dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_formulario_periodo_experiencia_solic = ".intval($cd_formulario_periodo_experiencia_solic).";";

		$this->db->query($qr_sql);
	}

	public function responder_arquivo($cd_formulario_periodo_experiencia_solic, $arquivo, $arquivo_nome, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.formulario_periodo_experiencia_solic
			   SET arquivo              = ".(trim($arquivo) != '' ? "'".trim($arquivo)."'" : "DEFAULT").",
			       arquivo_nome         = ".(trim($arquivo_nome) != '' ? "'".trim($arquivo_nome)."'" : "DEFAULT").",
			       cd_usuario_alteracao = ".intval($cd_usuario).",
			       dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_formulario_periodo_experiencia_solic = ".intval($cd_formulario_periodo_experiencia_solic).";";

		$this->db->query($qr_sql);
	}

	public function responder_encerrar($cd_formulario_periodo_experiencia_solic, $ds_resposta, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.formulario_periodo_experiencia_solic
			   SET ds_resposta         = ".(trim($ds_resposta) != '' ? "'".trim($ds_resposta)."'" : "DEFAULT").",
			       cd_usuario_resposta = ".intval($cd_usuario).",
			       dt_resposta         = CURRENT_TIMESTAMP
			 WHERE cd_formulario_periodo_experiencia_solic = ".intval($cd_formulario_periodo_experiencia_solic).";";

		$this->db->query($qr_sql);
	}

	public function responder_arquivo_encerrar($cd_formulario_periodo_experiencia_solic, $arquivo, $arquivo_nome, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.formulario_periodo_experiencia_solic
			   SET arquivo              = ".(trim($arquivo) != '' ? "'".trim($arquivo)."'" : "DEFAULT").",
			       arquivo_nome         = ".(trim($arquivo_nome) != '' ? "'".trim($arquivo_nome)."'" : "DEFAULT").",
			       cd_usuario_resposta  = ".intval($cd_usuario).",
			       cd_usuario_alteracao = ".intval($cd_usuario).",
			       dt_resposta          = CURRENT_TIMESTAMP,
			       dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_formulario_periodo_experiencia_solic = ".intval($cd_formulario_periodo_experiencia_solic).";";

		$this->db->query($qr_sql);
	}
}    