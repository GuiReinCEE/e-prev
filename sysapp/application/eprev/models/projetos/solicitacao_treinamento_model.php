<?php
class Solicitacao_treinamento_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_usuario, $args = array())
	{
		$qr_sql = "
			SELECT st.cd_solicitacao_treinamento,
				   st.cd_treinamento_colaborador_tipo,
				   st.ds_evento,
				   st.ds_promotor,
				   st.ds_endereco,
				   st.ds_cidade,
				   st.ds_uf,
				   TO_CHAR(st.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
				   TO_CHAR(st.dt_final, 'DD/MM/YYYY') AS dt_final,
				   st.nr_hr_final,
				   st.nr_carga_horaria,
				   st.arquivo,
				   st.arquivo_nome,
				   TO_CHAR(st.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(st.dt_validacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_validacao,
				   funcoes.get_usuario_nome(st.cd_usuario_validacao) AS ds_usuario_validacao,
				   funcoes.get_usuario_nome(st.cd_usuario_inclusao) AS ds_usuario_inclusao,
				   st.fl_pertinente,
				   st.ds_descricao,
				   (CASE WHEN st.dt_validacao IS NULL 
				   		 THEN 'Aguardando RH'
				         WHEN st.dt_validacao IS NOT NULL AND st.fl_pertinente = 'S'
				         THEN 'Pertinente'
				         ELSE 'Não Pertinente'
				   END) AS ds_status,
				   (CASE WHEN st.dt_validacao IS NULL 
				   		 THEN 'label label-info'
				         WHEN st.dt_validacao IS NOT NULL AND st.fl_pertinente = 'S'
				         THEN 'label label-success'
				         ELSE 'label label-important'
				   END) AS ds_class_status,
				   (CASE WHEN st.fl_pertinente = 'S'
				         THEN 'Sim'
				         WHEN st.fl_pertinente = 'N'
				         THEN 'Não'
				         ELSE ''
				   END) AS ds_pertinente,
				   (CASE WHEN st.fl_pertinente = 'S'
				         THEN 'label label-success'
				         WHEN st.fl_pertinente = 'N'
				         THEN 'label label-important'
				         ELSE ''
				   END) AS ds_class_pertinente,
				   tct.ds_treinamento_colaborador_tipo
			  FROM projetos.solicitacao_treinamento st
			  JOIN projetos.treinamento_colaborador_tipo tct
			    ON tct.cd_treinamento_colaborador_tipo = st.cd_treinamento_colaborador_tipo
			 WHERE st.dt_exclusao IS NULL
			   AND (st.cd_usuario_inclusao = ".intval($cd_usuario)." OR (SELECT COUNT(*)
																		    FROM projetos.usuarios_controledi
																		   WHERE codigo   = ".intval($cd_usuario)."
																		     AND indic_09 = '*') > 0)
			   AND tct.dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_solicitacao_treinamento)
	{
		$qr_sql = "
			SELECT st.cd_solicitacao_treinamento,
				   st.cd_treinamento_colaborador_tipo,
				   st.ds_evento,
				   st.ds_promotor,
				   st.ds_endereco,
				   st.ds_cidade,
				   st.ds_uf,
				   TO_CHAR(st.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
				   TO_CHAR(st.dt_final, 'DD/MM/YYYY') AS dt_final,
				   st.nr_hr_final,
				   st.nr_carga_horaria,
				   st.arquivo,
				   st.arquivo_nome,
				   TO_CHAR(st.dt_validacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_validacao,
				   funcoes.get_usuario_nome(st.cd_usuario_validacao) AS ds_usuario_validacao,
				   st.fl_pertinente,
				   st.ds_descricao,
				   st.cd_usuario_inclusao,
				   (CASE WHEN st.dt_validacao IS NULL 
				   		 THEN 'Aguardando RH'
				         WHEN st.dt_validacao IS NOT NULL AND st.fl_pertinente = 'S'
				         THEN 'Pertinente'
				         ELSE 'Não Pertinente'
				   END) AS ds_status,
				   (CASE WHEN st.dt_validacao IS NULL 
				   		 THEN 'label label-info'
				         WHEN st.dt_validacao IS NOT NULL AND st.fl_pertinente = 'S'
				         THEN 'label label-success'
				         ELSE 'label label-important'
				   END) AS ds_class_status,
				   (CASE WHEN st.fl_pertinente = 'S'
				         THEN 'Sim'
				         WHEN st.fl_pertinente = 'N'
				         THEN 'Não'
				         ELSE ''
				   END) AS ds_pertinente,
				   (CASE WHEN st.fl_pertinente = 'S'
				         THEN 'label label-success'
				         WHEN st.fl_pertinente = 'N'
				         THEN 'label label-important'
				         ELSE ''
				   END) AS ds_class_pertinente,
				   uc.nome AS ds_usuario_inclusao,
				   uc.usuario AS usuario_inclusao,
				   uc.cd_registro_empregado,
				   uc.divisao AS cd_gerencia
			  FROM projetos.solicitacao_treinamento st
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = st.cd_usuario_inclusao
			 WHERE st.dt_exclusao IS NULL
			   AND st.cd_solicitacao_treinamento = ".intval($cd_solicitacao_treinamento).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function lista_uf()
	{
        $qr_sql = "
			SELECT cd_uf AS value,
				   ds_uf AS text
			  FROM geografico.uf
			 ORDER BY text;";
        
        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar($args = array())
    {
    	$cd_solicitacao_treinamento = intval($this->db->get_new_id("projetos.solicitacao_treinamento", "cd_solicitacao_treinamento"));

    	$qr_sql = "
    		INSERT INTO projetos.solicitacao_treinamento
    			(
    			    cd_solicitacao_treinamento,
					cd_treinamento_colaborador_tipo,
					ds_evento,
					ds_promotor,
					ds_endereco,
					ds_cidade,
					ds_uf,
					dt_inicio,
					dt_final,
					nr_hr_final,
					nr_carga_horaria,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao,
					cd_usuario_alteracao
    			)
    		VALUES
    			(
    				".intval($cd_solicitacao_treinamento).",
    				".(intval($args['cd_treinamento_colaborador_tipo']) > 0 ? intval($args['cd_treinamento_colaborador_tipo']) : "DEFAULT").",
					".(trim($args['ds_evento']) != '' ? str_escape($args['ds_evento']) : "DEFAULT").",
					".(trim($args['ds_promotor']) != '' ? str_escape($args['ds_promotor']) : "DEFAULT").",
					".(trim($args['ds_endereco']) != '' ? str_escape($args['ds_endereco']) : "DEFAULT").",
					".(trim($args['ds_cidade']) != '' ? str_escape($args['ds_cidade']) : "DEFAULT").",
					".(trim($args['ds_uf']) != '' ? str_escape($args['ds_uf']) : "DEFAULT").",
					".(trim($args['dt_inicio']) != '' ? "TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')" : "DEFAULT").",
					".(trim($args['dt_final']) != '' ? "TO_DATE('".$args['dt_final']."','DD/MM/YYYY')" : "DEFAULT").",
					".(trim($args['nr_hr_final']) != '' ? "CAST('".$args['nr_hr_final']."' AS TIME)" : "DEFAULT").",
					".(trim($args['nr_carga_horaria']) != '' ? floatval($args['nr_carga_horaria']) : "DEFAULT").",
					".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
					".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
    			);";

    	$this->db->query($qr_sql);

    	return $cd_solicitacao_treinamento;
    }

    public function atualizar($cd_solicitacao_treinamento, $args = array())
    {
    	$qr_sql = "
    		UPDATE projetos.solicitacao_treinamento
    		   SET cd_treinamento_colaborador_tipo = ".(intval($args['cd_treinamento_colaborador_tipo']) > 0 ? intval($args['cd_treinamento_colaborador_tipo']) : "DEFAULT").",
				   ds_evento 					   = ".(trim($args['ds_evento']) != '' ? str_escape($args['ds_evento']) : "DEFAULT").",
				   ds_promotor 					   = ".(trim($args['ds_promotor']) != '' ? str_escape($args['ds_promotor']) : "DEFAULT").",
				   ds_endereco 					   = ".(trim($args['ds_endereco']) != '' ? str_escape($args['ds_endereco']) : "DEFAULT").",
				   ds_cidade 					   = ".(trim($args['ds_cidade']) != '' ? str_escape($args['ds_cidade']) : "DEFAULT").",
				   ds_uf 						   = ".(trim($args['ds_uf']) != '' ? str_escape($args['ds_uf']) : "DEFAULT").",
				   dt_inicio 					   = ".(trim($args['dt_inicio']) != '' ? "TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')" : "DEFAULT").",
				   dt_final 					   = ".(trim($args['dt_final']) != '' ? "TO_DATE('".$args['dt_final']."','DD/MM/YYYY')" : "DEFAULT").",
				   nr_hr_final 				   	   = ".(trim($args['nr_hr_final']) != '' ? "CAST('".$args['nr_hr_final']."' AS TIME)" : "DEFAULT").",
				   nr_carga_horaria 			   = ".(trim($args['nr_carga_horaria']) != '' ? floatval($args['nr_carga_horaria']) : "DEFAULT").",
				   arquivo 			   			   = ".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
				   arquivo_nome 				   = ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
				   cd_usuario_inclusao 			   = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
				   cd_usuario_alteracao 		   = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
    		 WHERE cd_solicitacao_treinamento = ".intval($cd_solicitacao_treinamento).";";

    	$this->db->query($qr_sql);
    }

    public function salvar_validacao($cd_solicitacao_treinamento, $args = array())
    {
    	$qr_sql = "
    		UPDATE projetos.solicitacao_treinamento
    		   SET fl_pertinente 		= ".(trim($args['fl_pertinente']) != '' ? str_escape($args['fl_pertinente']) : "DEFAULT").",
    			   ds_descricao 		= ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
    			   dt_validacao 		= CURRENT_TIMESTAMP,
    			   cd_usuario_validacao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
    			   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
    		 WHERE cd_solicitacao_treinamento = ".intval($cd_solicitacao_treinamento).";";

    	$this->db->query($qr_sql);
    }

    public function salvar_treinamento($args = array())
    {
    	$cd_treinamento_colaborador = intval($this->db->get_new_id("projetos.treinamento_colaborador", "cd_treinamento_colaborador"));

    	$qr_sql = "
    		INSERT INTO projetos.treinamento_colaborador
    			(
    				cd_treinamento_colaborador,
                    nome,
                    promotor,
                    endereco,
                    cidade,
                    uf,
                    dt_inicio,
                    dt_final,
                    hr_final,
                    carga_horaria,
                    cd_treinamento_colaborador_tipo,
                    fl_cadastro_rh,
                    cd_usuario_inclusao
    			)
    		VALUES
    			(
    				".intval($cd_treinamento_colaborador).",
					".(trim($args['nome']) != '' ? str_escape($args['nome']) : "DEFAULT").",
					".(trim($args['promotor']) != '' ? str_escape($args['promotor']) : "DEFAULT").",
					".(trim($args['endereco']) != '' ? str_escape($args['endereco']) : "DEFAULT").",
					".(trim($args['cidade']) != '' ? str_escape($args['cidade']) : "DEFAULT").",
					".(trim($args['uf']) != '' ? str_escape($args['uf']) : "DEFAULT").",
					".(trim($args['dt_inicio']) != '' ? "TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')" : "DEFAULT").",
					".(trim($args['dt_final']) != '' ? "TO_DATE('".$args['dt_final']."','DD/MM/YYYY')" : "DEFAULT").",
					".(trim($args['hr_final']) != '' ? "CAST('".$args['hr_final']."' AS TIME)" : "DEFAULT").",
					".(trim($args['carga_horaria']) != '' ? floatval($args['carga_horaria']) : "DEFAULT").",
					".(intval($args['cd_treinamento_colaborador_tipo']) > 0 ? intval($args['cd_treinamento_colaborador_tipo']) : "DEFAULT").",
					".(trim($args['fl_cadastro_rh']) != '' ? str_escape($args['fl_cadastro_rh']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."

    			);";

    	$this->db->query($qr_sql);

    	return $cd_treinamento_colaborador;
    }

    public function get_numero_treinamento_colaborador($cd_treinamento_colaborador)
    {
    	$qr_sql = "
			SELECT numero,
			       ano
              FROM projetos.treinamento_colaborador
             WHERE cd_treinamento_colaborador = ".intval($cd_treinamento_colaborador).";";

    	return $this->db->query($qr_sql)->row_array();
    }

    function salvar_colaborador($args = array())
    {        
        $qr_sql = "
			INSERT INTO projetos.treinamento_colaborador_item
				(
					numero,
					ano,
					nome,
					area,
					cd_empresa,
                    cd_registro_empregado,
                    seq_dependencia,
                    fl_certificado,
                    arquivo,
                    arquivo_nome,
					cd_usuario_inclusao
				)
			VALUES
				(
					".(intval($args['numero']) > 0 ? intval($args['numero']) : "DEFAULT").",
					".(intval($args['ano']) > 0 ? intval($args['ano']) : "DEFAULT").",
					".(trim($args['ds_nome_usuario']) != '' ? "UPPER(funcoes.remove_acento('".trim($args['ds_nome_usuario'])."'))" : "DEFAULT").",
					".(trim($args['cd_gerencia']) != '' ? str_escape($args['cd_gerencia']) : "DEFAULT").",
					".(intval($args['cd_empresa']) > 0 ? intval($args['cd_empresa']) : "DEFAULT").",
					".(intval($args['cd_registro_empregado']) > 0 ? intval($args['cd_registro_empregado']) : "DEFAULT").",
					".intval($args['seq_dependencia']).",
					".(trim($args['fl_certificado']) != '' ? str_escape($args['fl_certificado']) : "DEFAULT").",
			        ".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
			        ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
				);";
        
        $this->db->query($qr_sql);
    }
}