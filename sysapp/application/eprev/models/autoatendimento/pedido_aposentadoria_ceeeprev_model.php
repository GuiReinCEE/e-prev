<?php
class Pedido_aposentadoria_ceeeprev_model extends Model
{
	function __construct()
	{
		parent::model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT pac.cd_pedido_aposentadoria_ceeeprev, 
				   pac.cd_empresa, 
				   pac.cd_registro_empregado, 
			       pac.seq_dependencia,
			       TO_CHAR(dt_encaminhamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encaminhamento,
			       TO_CHAR(dt_analise, 'DD/MM/YYYY HH24:MI:SS') AS dt_analise,
			       TO_CHAR(dt_indeferido, 'DD/MM/YYYY HH24:MI:SS') AS dt_indeferido,
			       TO_CHAR(dt_assinatura, 'DD/MM/YYYY HH24:MI:SS') AS dt_assinatura,
			       TO_CHAR(dt_deferido, 'DD/MM/YYYY HH24:MI:SS') AS dt_deferido,
			       (CASE WHEN pac.tp_pedido_aposentadoria = 'N'
			             THEN 'Normal'
                         WHEN pac.tp_pedido_aposentadoria = 'A'
			             THEN 'Antecipada'
			       END) AS ds_pedido_aposentadoria,
			       (CASE WHEN dt_deferido IS NOT NULL
			             THEN 'Deferido'
			       		 WHEN dt_assinatura IS NOT NULL
			             THEN 'Em Assinatura'
			             WHEN dt_indeferido IS NOT NULL
			             THEN 'Indeferido'
			             WHEN dt_analise IS NOT NULL 
			             THEN 'Em análise'
			             ELSE 'Encaminhado'
			       END) AS ds_status,
			       (CASE WHEN dt_deferido IS NOT NULL
			             THEN 'label label-success'
			             WHEN dt_assinatura IS NOT NULL
			             THEN 'label label-warning'
			             WHEN dt_indeferido IS NOT NULL
			             THEN 'label lable-important'
			             WHEN dt_analise IS NOT NULL 
			             THEN 'label label-info'
			             ELSE 'label'
			       END) AS ds_class_status,
			       pac.tp_pedido_aposentadoria,
			       pac.ds_nome
			  FROM autoatendimento.pedido_aposentadoria_ceeeprev pac
			 WHERE dt_encaminhamento IS NOT NULL
			   ".(trim($args['cd_empresa']) != '' ? "AND pac.cd_empresa = ".intval($args['cd_empresa']) : "")."			   
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND pac.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."			   
			   ".(trim($args['seq_dependencia']) != '' ? "AND pac.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
			   ".(((trim($args['dt_encaminhamento_ini']) != '') AND (trim($args['dt_encaminhamento_fim']) != '')) ? "AND DATE_TRUNC('day', dt_encaminhamento) BETWEEN TO_DATE('".$args['dt_encaminhamento_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_encaminhamento_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(trim($args['fl_deferido']) == 'S' ? 'AND dt_deferido IS NOT NULL' : '')."
		       ".(trim($args['fl_deferido']) == 'N' ? 'AND dt_deferido IS NULL' : '')."
		       ".(trim($args['fl_indeferido']) == 'S' ? 'AND dt_indeferido IS NOT NULL' : '')."
		       ".(trim($args['fl_indeferido']) == 'N' ? 'AND dt_indeferido IS NULL' : '').";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_pedido_aposentadoria_ceeeprev)
	{
		$qr_sql = "
			SELECT cd_pedido_aposentadoria_ceeeprev, 
				   cd_empresa, 
				   cd_registro_empregado, 
			       seq_dependencia,
			       TO_CHAR(dt_analise, 'DD/MM/YYYY HH24:MI:SS') AS dt_analise,
			       TO_CHAR(dt_indeferido, 'DD/MM/YYYY HH24:MI:SS') AS dt_indeferido,
			       TO_CHAR(dt_assinatura, 'DD/MM/YYYY HH24:MI:SS') AS dt_assinatura,
			       TO_CHAR(dt_deferido, 'DD/MM/YYYY HH24:MI:SS') AS dt_deferido,
			       (CASE WHEN tp_pedido_aposentadoria = 'N'
			             THEN 'Normal'
                         WHEN tp_pedido_aposentadoria = 'A'
			             THEN 'Antecipada'
			       END) AS ds_pedido_aposentadoria,
			       (CASE WHEN dt_deferido IS NOT NULL
			             THEN 'Deferido'
			       		 WHEN dt_assinatura IS NOT NULL
			             THEN 'Em Assinatura'
			             WHEN dt_indeferido IS NOT NULL
			             THEN 'Indeferido'
			             WHEN dt_analise IS NOT NULL 
			             THEN 'Em análise'
			             ELSE 'Encaminhado'
			       END) AS ds_status,
			       (CASE WHEN dt_deferido IS NOT NULL
			             THEN 'label label-success'
			             WHEN dt_assinatura IS NOT NULL
			             THEN 'label label-warning'
			             WHEN dt_indeferido IS NOT NULL
			             THEN 'label lable-important'
			             WHEN dt_analise IS NOT NULL 
			             THEN 'label label-info'
			             ELSE 'label'
			       END) AS ds_class_status,
			       tp_pedido_aposentadoria,
			       ds_nome,
			       TO_CHAR(dt_nascimento, 'DD/MM/YYYY') dt_nascimento,
			       ds_cpf,
			       ds_estado_civil, 
			       ds_naturalidade, 
			       ds_nacionalidade, 
			       ds_endereco, 
			       nr_endereco, 
			       ds_complemento_endereco, 
			       ds_bairro, 
			       ds_cidade, 
			       ds_uf, 
			       ds_cep, 
			       ds_telefone1, 
			       ds_telefone2, 
			       ds_celular, 
			       ds_email1, 
			       ds_email2, 
			       ds_banco, 
			       ds_agencia, 
			       ds_conta, 
			       fl_adiantamento_cip, 
			       nr_adiantamento_cip, 
			       fl_reversao_beneficio,
			       fl_politicamente_exposta, 
			       fl_us_person,
			       arquivo_conta_bancaria,
			       arquivo_doc_identidade,
			       arquivo_doc_cpf,
			       arquivo_recisao_contrato,
			       arquivo_simulacao,
			       ds_motivo_indeferido
			  FROM autoatendimento.pedido_aposentadoria_ceeeprev
			 WHERE cd_pedido_aposentadoria_ceeeprev = ".intval($cd_pedido_aposentadoria_ceeeprev).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_estado_civil()
    {
        $qr_sql = "
            SELECT cd_estado_civil, 
                   descricao_estado_civil AS value,
                   descricao_estado_civil AS text
              FROM estado_civils
             WHERE cd_estado_civil > 0
             ORDER BY descricao_estado_civil";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_instituicao_financeira()
    {
        $qr_sql = "
            SELECT cd_instituicao, 
                   razao_social_nome AS value,
                   razao_social_nome AS text
              FROM public.instituicao_financeiras
             WHERE cd_agencia::integer = 0 
               AND cd_instituicao      > 0
             ORDER BY razao_social_nome;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_grau_parentesco()
    {
        $qr_sql = "
            SELECT cd_grau_parentesco,
                   descricao_grau_parentesco AS value,
                   descricao_grau_parentesco AS text
              FROM grau_parentescos
             WHERE cd_grau_parentesco > 0
             ORDER BY descricao_grau_parentesco;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function atualizar($cd_pedido_aposentadoria_ceeeprev, $args = array ())
    {
    	$qr_sql = "
    		UPDATE autoatendimento.pedido_aposentadoria_ceeeprev
			   SET ds_nome                  = ".(trim($args['ds_nome']) != '' ? str_escape($args['ds_nome']) : "DEFAULT").",
			       dt_nascimento            = ".(trim($args['dt_nascimento']) != '' ? "TO_DATE('".$args['dt_nascimento']."', 'DD/MM/YYYY')" : "DEFAULT").",
			       ds_cpf                   = ".(trim($args['ds_cpf']) != '' ? str_escape($args['ds_cpf']) : "DEFAULT").",
			       ds_estado_civil          = ".(trim($args['ds_estado_civil']) != '' ? str_escape($args['ds_estado_civil']) : "DEFAULT").",
			       ds_naturalidade          = ".(trim($args['ds_naturalidade']) != '' ? str_escape($args['ds_naturalidade']) : "DEFAULT").",
			       ds_nacionalidade         = ".(trim($args['ds_nacionalidade']) != '' ? str_escape($args['ds_nacionalidade']) : "DEFAULT").",
			       ds_endereco              = ".(trim($args['ds_endereco']) != '' ? str_escape($args['ds_endereco']) : "DEFAULT").",
			       nr_endereco              = ".(intval($args['nr_endereco']) > 0 ? intval($args['nr_endereco']) : "DEFAULT").",
			       ds_complemento_endereco  = ".(trim($args['ds_complemento_endereco']) != '' ? str_escape($args['ds_complemento_endereco']) : "DEFAULT").",
			       ds_bairro                = ".(trim($args['ds_bairro']) != '' ? str_escape($args['ds_bairro']) : "DEFAULT").",
			       ds_cidade                = ".(trim($args['ds_cidade']) != '' ? str_escape($args['ds_cidade']) : "DEFAULT").",
			       ds_uf                    = ".(trim($args['ds_uf']) != '' ? str_escape($args['ds_uf']) : "DEFAULT").",
			       ds_cep                   = ".(trim($args['ds_cep']) != '' ? str_escape($args['ds_cep']) : "DEFAULT").",
			       ds_telefone1             = ".(trim($args['ds_telefone1']) != '' ? str_escape($args['ds_telefone1']) : "DEFAULT").",
			       ds_telefone2             = ".(trim($args['ds_telefone2']) != '' ? str_escape($args['ds_telefone2']) : "DEFAULT").",
			       ds_celular               = ".(trim($args['ds_celular']) != '' ? str_escape($args['ds_celular']) : "DEFAULT").",
			       ds_email1                = ".(trim($args['ds_email1']) != '' ? str_escape($args['ds_email1']) : "DEFAULT").",
			       ds_email2                = ".(trim($args['ds_email2']) != '' ? str_escape($args['ds_email2']) : "DEFAULT").",   
			       ds_banco                 = ".(trim($args['ds_banco']) != '' ? str_escape($args['ds_banco']) : "DEFAULT").",  
			       ds_agencia               = ".(trim($args['ds_agencia']) != '' ? str_escape($args['ds_agencia']) : "DEFAULT").",
			       ds_conta                 = ".(trim($args['ds_conta']) != '' ? str_escape($args['ds_conta']) : "DEFAULT").",
			       fl_adiantamento_cip      = ".(trim($args['fl_adiantamento_cip']) != '' ? str_escape($args['fl_adiantamento_cip']) : "DEFAULT").",
			       nr_adiantamento_cip      = ".(trim($args['nr_adiantamento_cip']) != '' ? floatval($args['nr_adiantamento_cip']) : "DEFAULT").",
			       fl_reversao_beneficio    = ".(trim($args['fl_reversao_beneficio']) != '' ? str_escape($args['fl_reversao_beneficio']) : "DEFAULT").",  
			       fl_politicamente_exposta = ".(trim($args['fl_politicamente_exposta']) != '' ? str_escape($args['fl_politicamente_exposta']) : "DEFAULT").", 
			       fl_us_person             = ".(trim($args['fl_us_person']) != '' ? str_escape($args['fl_us_person']) : "DEFAULT").",
			       arquivo_conta_bancaria   = ".(trim($args['arquivo_conta_bancaria']) != '' ? "'".trim($args['arquivo_conta_bancaria'])."'" : "DEFAULT").",
			       arquivo_doc_identidade   = ".(trim($args['arquivo_doc_identidade']) != '' ? "'".trim($args['arquivo_doc_identidade'])."'" : "DEFAULT").",
			       arquivo_doc_cpf          = ".(trim($args['arquivo_doc_cpf']) != '' ? "'".trim($args['arquivo_doc_cpf'])."'" : "DEFAULT").",
			       arquivo_recisao_contrato = ".(trim($args['arquivo_recisao_contrato']) != '' ? "'".trim($args['arquivo_recisao_contrato'])."'" : "DEFAULT").",
			       arquivo_simulacao        = ".(trim($args['arquivo_simulacao']) != '' ? "'".trim($args['arquivo_simulacao'])."'" : "DEFAULT").",
			       cd_usuario_alteracao     = ".intval($args['cd_usuario']).",
			       dt_alteracao             = CURRENT_TIMESTAMP
			 WHERE cd_pedido_aposentadoria_ceeeprev = ".intval($cd_pedido_aposentadoria_ceeeprev).";";

    	$this->db->query($qr_sql);
    }

    public function carrega_dependente($cd_pedido_aposentadoria_ceeeprev_dependente)
    {
    	$qr_sql = "
    		SELECT cd_pedido_aposentadoria_ceeeprev, 
    		       cd_pedido_aposentadoria_ceeeprev_dependente,
            	   ds_nome, 
            	   TO_CHAR(dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento,
            	   ds_sexo,
            	   ds_grau_parentesco, 
            	   ds_estado_civil, 
            	   fl_incapaz, 
            	   fl_estudante, 
            	   fl_guarda_juridica, 
            	   fl_previdenciario, 
            	   fl_imposto_renda
    		  FROM autoatendimento.pedido_aposentadoria_ceeeprev_dependente
    		 WHERE cd_pedido_aposentadoria_ceeeprev_dependente = ".intval($cd_pedido_aposentadoria_ceeeprev_dependente).";";

    	return $this->db->query($qr_sql)->row_array(); 
    }

    public function listar_dependente($cd_pedido_aposentadoria_ceeeprev)
    {
    	$qr_sql = "
    		SELECT cd_pedido_aposentadoria_ceeeprev, 
    		       cd_pedido_aposentadoria_ceeeprev_dependente,
            	   ds_nome, 
            	   TO_CHAR(dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento,
            	   ds_sexo,
            	   ds_grau_parentesco, 
            	   ds_estado_civil, 
            	   fl_incapaz, 
            	   fl_estudante, 
            	   fl_guarda_juridica, 
            	   fl_previdenciario, 
            	   fl_imposto_renda
    		  FROM autoatendimento.pedido_aposentadoria_ceeeprev_dependente
    		 WHERE dt_exclusao IS NULL
    		   AND cd_pedido_aposentadoria_ceeeprev = ".intval($cd_pedido_aposentadoria_ceeeprev).";";

    	return $this->db->query($qr_sql)->result_array(); 
    }

    public function carrega_dependente_previdenciario($cd_pedido_aposentadoria_ceeeprev_dependente_prev)
    {
    	$qr_sql = "
    		SELECT cd_pedido_aposentadoria_ceeeprev, 
    		       cd_pedido_aposentadoria_ceeeprev_dependente_prev,
            	   ds_nome, 
            	   TO_CHAR(dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento,
            	   ds_sexo,
            	   ds_grau_parentesco, 
            	   ds_estado_civil, 
            	   fl_incapaz, 
            	   fl_estudante, 
            	   fl_guarda_juridica, 
            	   fl_previdenciario, 
            	   fl_imposto_renda
    		  FROM autoatendimento.pedido_aposentadoria_ceeeprev_dependente_prev
    		 WHERE cd_pedido_aposentadoria_ceeeprev_dependente_prev = ".intval($cd_pedido_aposentadoria_ceeeprev_dependente_prev).";";

    	return $this->db->query($qr_sql)->row_array(); 
    }

    public function listar_dependente_previdenciario($cd_pedido_aposentadoria_ceeeprev)
    {
    	$qr_sql = "
    		SELECT o.cd_pedido_aposentadoria_ceeeprev, 
    		       o.cd_pedido_aposentadoria_ceeeprev_dependente_opcao,
    		       0 AS cd_pedido_aposentadoria_ceeeprev_dependente_prev,
            	   p.nome AS ds_nome,
                   TO_CHAR(p.dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento, 
                   p.sexo AS ds_sexo, 
            	   gp.descricao_grau_parentesco AS ds_grau_parentesco,
            	   ec.descricao_estado_civil AS ds_estado_civil,
            	   d.id_incapacidade AS fl_incapaz,
            	   o.fl_opcao,
                   (CASE WHEN o.fl_opcao = 'M' THEN 'MANTER'
                         WHEN o.fl_opcao = 'E' THEN 'EXCLUIR'
                         ELSE ''
                   END) AS ds_opcao
    		  FROM autoatendimento.pedido_aposentadoria_ceeeprev_dependente_opcao o
    		  JOIN participantes p
                ON p.cd_registro_empregado = o.cd_registro_empregado 
               AND p.seq_dependencia       = o.seq_dependencia 
               AND p.cd_empresa            = o.cd_empresa
              JOIN public.dependentes d
                ON d.cd_registro_empregado = p.cd_registro_empregado 
               AND d.seq_dependencia       = p.seq_dependencia 
               AND d.cd_empresa            = p.cd_empresa
              JOIN public.grau_parentescos gp 
                ON gp.cd_grau_parentesco   = d.cd_grau_parentesco
              LEFT JOIN public.estado_civils ec
                ON ec.cd_estado_civil = p.cd_estado_civil
       		 WHERE o.dt_exclusao IS NULL
       		   AND o.fl_opcao  = 'M'
    		   AND o.cd_pedido_aposentadoria_ceeeprev = ".intval($cd_pedido_aposentadoria_ceeeprev)."

    		 UNION
            SELECT cd_pedido_aposentadoria_ceeeprev,
                   0 AS cd_pedido_aposentadoria_ceeeprev_dependente_opcao,
                   cd_pedido_aposentadoria_ceeeprev_dependente_prev,
                   ds_nome,
                   TO_CHAR(dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento, 
                   ds_sexo,
                   ds_grau_parentesco,
                   ds_estado_civil,
                   fl_incapaz,
                   'I' AS fl_opcao,
                   'ADICIONADO' AS ds_opcao
              FROM autoatendimento.pedido_aposentadoria_ceeeprev_dependente_prev
             WHERE dt_exclusao IS NULL
               AND cd_pedido_aposentadoria_ceeeprev = ".intval($cd_pedido_aposentadoria_ceeeprev).";";

    	return $this->db->query($qr_sql)->result_array(); 
    }

    public function salvar_dependente($cd_pedido_aposentadoria_ceeeprev, $args = array())
    {
    	$qr_sql = "
    		INSERT INTO autoatendimento.pedido_aposentadoria_ceeeprev_dependente
    			 (
            		cd_pedido_aposentadoria_ceeeprev, 
            		ds_nome, 
            		dt_nascimento, 
            		ds_sexo,
            		ds_grau_parentesco, 
            		ds_estado_civil, 
            		fl_incapaz, 
            		fl_estudante, 
            		fl_guarda_juridica, 
            		fl_previdenciario, 
            		fl_imposto_renda,
            		cd_usuario_inclusao,
            		cd_usuario_alteracao,
            		dt_alteracao
            	 )
    		VALUES 
    		     (
    		     	".intval($cd_pedido_aposentadoria_ceeeprev).",
    		     	".(trim($args['ds_nome']) != '' ? str_escape($args['ds_nome']) : "DEFAULT").",
    		     	".(trim($args['dt_nascimento']) != '' ? "TO_DATE('".$args['dt_nascimento']."', 'DD/MM/YYYY')" : "DEFAULT").",
    		     	".(trim($args['ds_sexo']) != '' ? str_escape($args['ds_sexo']) : "DEFAULT").",
    		     	".(trim($args['ds_grau_parentesco']) != '' ? str_escape($args['ds_grau_parentesco']) : "DEFAULT").",
    		     	".(trim($args['ds_estado_civil']) != '' ? str_escape($args['ds_estado_civil']) : "DEFAULT").",
    		     	".(trim($args['fl_incapaz']) != '' ? str_escape($args['fl_incapaz']) : "DEFAULT").",
    		     	".(trim($args['fl_estudante']) != '' ? str_escape($args['fl_estudante']) : "DEFAULT").",
    		     	".(trim($args['fl_guarda_juridica']) != '' ? str_escape($args['fl_guarda_juridica']) : "DEFAULT").",
    		     	".(trim($args['fl_previdenciario']) != '' ? str_escape($args['fl_previdenciario']) : "DEFAULT").",
    		     	".(trim($args['fl_imposto_renda']) != '' ? str_escape($args['fl_imposto_renda']) : "DEFAULT").",
    		     	".intval($args['cd_usuario']).",
    		     	".intval($args['cd_usuario']).",
    		     	CURRENT_TIMESTAMP
    		     );";

    	$this->db->query($qr_sql);
    }

    public function atualizar_dependente($cd_pedido_aposentadoria_ceeeprev_dependente, $args = array())
    {
    	$qr_sql = "
    		UPDATE autoatendimento.pedido_aposentadoria_ceeeprev_dependente
    		   SET ds_nome              = ".(trim($args['ds_nome']) != '' ? str_escape($args['ds_nome']) : "DEFAULT").", 
            	   dt_nascimento        = ".(trim($args['dt_nascimento']) != '' ? "TO_DATE('".$args['dt_nascimento']."', 'DD/MM/YYYY')" : "DEFAULT").", 
            	   ds_sexo              = ".(trim($args['ds_sexo']) != '' ? str_escape($args['ds_sexo']) : "DEFAULT").",
            	   ds_grau_parentesco   = ".(trim($args['ds_grau_parentesco']) != '' ? str_escape($args['ds_grau_parentesco']) : "DEFAULT").", 
            	   ds_estado_civil      = ".(trim($args['ds_estado_civil']) != '' ? str_escape($args['ds_estado_civil']) : "DEFAULT").", 
            	   fl_incapaz           = ".(trim($args['fl_incapaz']) != '' ? str_escape($args['fl_incapaz']) : "DEFAULT").", 
            	   fl_estudante         = ".(trim($args['fl_estudante']) != '' ? str_escape($args['fl_estudante']) : "DEFAULT").", 
            	   fl_guarda_juridica   = ".(trim($args['fl_guarda_juridica']) != '' ? str_escape($args['fl_guarda_juridica']) : "DEFAULT").", 
            	   fl_previdenciario    = ".(trim($args['fl_previdenciario']) != '' ? str_escape($args['fl_previdenciario']) : "DEFAULT").", 
            	   fl_imposto_renda     = ".(trim($args['fl_imposto_renda']) != '' ? str_escape($args['fl_imposto_renda']) : "DEFAULT").",
            	   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
			       dt_alteracao         = CURRENT_TIMESTAMP
    		 WHERE cd_pedido_aposentadoria_ceeeprev_dependente = ".intval($cd_pedido_aposentadoria_ceeeprev_dependente).";";

    	$this->db->query($qr_sql);
    }

    public function salvar_dependente_previdenciario($cd_pedido_aposentadoria_ceeeprev, $args = array())
    {
    	$qr_sql = "
    		INSERT INTO autoatendimento.pedido_aposentadoria_ceeeprev_dependente_prev
    			 (
            		cd_pedido_aposentadoria_ceeeprev, 
            		ds_nome, 
            		dt_nascimento, 
            		ds_sexo,
            		ds_grau_parentesco, 
            		ds_estado_civil, 
            		fl_incapaz, 
            		fl_estudante, 
            		fl_guarda_juridica, 
            		fl_previdenciario, 
            		fl_imposto_renda,
            		cd_usuario_inclusao,
            		cd_usuario_alteracao,
            		dt_alteracao
            	 )
    		VALUES 
    		     (
    		     	".intval($cd_pedido_aposentadoria_ceeeprev).",
    		     	".(trim($args['ds_nome']) != '' ? str_escape($args['ds_nome']) : "DEFAULT").",
    		     	".(trim($args['dt_nascimento']) != '' ? "TO_DATE('".$args['dt_nascimento']."', 'DD/MM/YYYY')" : "DEFAULT").",
    		     	".(trim($args['ds_sexo']) != '' ? str_escape($args['ds_sexo']) : "DEFAULT").",
    		     	".(trim($args['ds_grau_parentesco']) != '' ? str_escape($args['ds_grau_parentesco']) : "DEFAULT").",
    		     	".(trim($args['ds_estado_civil']) != '' ? str_escape($args['ds_estado_civil']) : "DEFAULT").",
    		     	".(trim($args['fl_incapaz']) != '' ? str_escape($args['fl_incapaz']) : "DEFAULT").",
    		     	".(trim($args['fl_estudante']) != '' ? str_escape($args['fl_estudante']) : "DEFAULT").",
    		     	".(trim($args['fl_guarda_juridica']) != '' ? str_escape($args['fl_guarda_juridica']) : "DEFAULT").",
    		     	".(trim($args['fl_previdenciario']) != '' ? str_escape($args['fl_previdenciario']) : "DEFAULT").",
    		     	".(trim($args['fl_imposto_renda']) != '' ? str_escape($args['fl_imposto_renda']) : "DEFAULT").",
    		     	".intval($args['cd_usuario']).",
    		     	".intval($args['cd_usuario']).",
    		     	CURRENT_TIMESTAMP
    		     );";

    	$this->db->query($qr_sql);
    }

    public function atualizar_dependente_previdenciario($cd_pedido_aposentadoria_ceeeprev_dependente_prev, $args = array())
    {
    	$qr_sql = "
    		UPDATE autoatendimento.pedido_aposentadoria_ceeeprev_dependente_prev
    		   SET ds_nome              = ".(trim($args['ds_nome']) != '' ? str_escape($args['ds_nome']) : "DEFAULT").", 
            	   dt_nascimento        = ".(trim($args['dt_nascimento']) != '' ? "TO_DATE('".$args['dt_nascimento']."', 'DD/MM/YYYY')" : "DEFAULT").", 
            	   ds_sexo              = ".(trim($args['ds_sexo']) != '' ? str_escape($args['ds_sexo']) : "DEFAULT").",
            	   ds_grau_parentesco   = ".(trim($args['ds_grau_parentesco']) != '' ? str_escape($args['ds_grau_parentesco']) : "DEFAULT").", 
            	   ds_estado_civil      = ".(trim($args['ds_estado_civil']) != '' ? str_escape($args['ds_estado_civil']) : "DEFAULT").", 
            	   fl_incapaz           = ".(trim($args['fl_incapaz']) != '' ? str_escape($args['fl_incapaz']) : "DEFAULT").", 
            	   fl_estudante         = ".(trim($args['fl_estudante']) != '' ? str_escape($args['fl_estudante']) : "DEFAULT").", 
            	   fl_guarda_juridica   = ".(trim($args['fl_guarda_juridica']) != '' ? str_escape($args['fl_guarda_juridica']) : "DEFAULT").", 
            	   fl_previdenciario    = ".(trim($args['fl_previdenciario']) != '' ? str_escape($args['fl_previdenciario']) : "DEFAULT").", 
            	   fl_imposto_renda     = ".(trim($args['fl_imposto_renda']) != '' ? str_escape($args['fl_imposto_renda']) : "DEFAULT").",
            	   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
			       dt_alteracao         = CURRENT_TIMESTAMP
    		 WHERE cd_pedido_aposentadoria_ceeeprev_dependente_prev = ".intval($cd_pedido_aposentadoria_ceeeprev_dependente_prev).";";

    	$this->db->query($qr_sql);
    }

    public function excluir_dependente($cd_pedido_aposentadoria_ceeeprev_dependente, $cd_usuario)
    {
    	$qr_sql = "
    		UPDATE autoatendimento.pedido_aposentadoria_ceeeprev_dependente
    		   SET cd_usuario_exclusao = ".intval($cd_usuario).",
			       dt_exclusao         = CURRENT_TIMESTAMP
    		 WHERE cd_pedido_aposentadoria_ceeeprev_dependente = ".intval($cd_pedido_aposentadoria_ceeeprev_dependente).";";

    	$this->db->query($qr_sql);
    }

    public function excluir_dependente_previdenciario($cd_pedido_aposentadoria_ceeeprev_dependente_prev, $cd_usuario)
    {
    	$qr_sql = "
    		UPDATE autoatendimento.pedido_aposentadoria_ceeeprev_dependente_prev
    		   SET cd_usuario_exclusao = ".intval($cd_usuario).",
			       dt_exclusao         = CURRENT_TIMESTAMP
    		 WHERE cd_pedido_aposentadoria_ceeeprev_dependente_prev = ".intval($cd_pedido_aposentadoria_ceeeprev_dependente_prev).";";

    	$this->db->query($qr_sql);
    }

    public function analise($cd_pedido_aposentadoria_ceeeprev, $cd_usuario)
    {
    	$qr_sql = "
    		UPDATE autoatendimento.pedido_aposentadoria_ceeeprev
    		   SET cd_usuario_analise = ".intval($cd_usuario).",
			       dt_analise         = CURRENT_TIMESTAMP
    		 WHERE cd_pedido_aposentadoria_ceeeprev = ".intval($cd_pedido_aposentadoria_ceeeprev).";";

    	$this->db->query($qr_sql);
    }

    public function indeferir($cd_pedido_aposentadoria_ceeeprev, $ds_motivo_indeferido, $cd_usuario)
    {
    	$qr_sql = "
    		UPDATE autoatendimento.pedido_aposentadoria_ceeeprev
    		   SET ds_motivo_indeferido  = ".(trim($ds_motivo_indeferido) != '' ? str_escape($ds_motivo_indeferido) : "DEFAULT").", 
    		       cd_usuario_indeferido = ".intval($cd_usuario).",
			       dt_indeferido         = CURRENT_TIMESTAMP
    		 WHERE cd_pedido_aposentadoria_ceeeprev = ".intval($cd_pedido_aposentadoria_ceeeprev).";";

    	$this->db->query($qr_sql);
    }

    public function assinatura($cd_pedido_aposentadoria_ceeeprev, $cd_usuario)
    {
    	$qr_sql = "
    		UPDATE autoatendimento.pedido_aposentadoria_ceeeprev
    		   SET cd_usuario_assinatura = ".intval($cd_usuario).",
			       dt_assinatura         = CURRENT_TIMESTAMP
    		 WHERE cd_pedido_aposentadoria_ceeeprev = ".intval($cd_pedido_aposentadoria_ceeeprev).";";

    	$this->db->query($qr_sql);
    }

    public function deferido($cd_pedido_aposentadoria_ceeeprev, $cd_usuario)
    {
    	$qr_sql = "
    		UPDATE autoatendimento.pedido_aposentadoria_ceeeprev
    		   SET cd_usuario_deferido = ".intval($cd_usuario).",
			       dt_deferido         = CURRENT_TIMESTAMP
    		 WHERE cd_pedido_aposentadoria_ceeeprev = ".intval($cd_pedido_aposentadoria_ceeeprev).";";

    	$this->db->query($qr_sql);
    }

    public function get($ds_ambiente = 'PRODUCAO')
    {
        $qr_sql = "
            SELECT ds_ambiente, 
				   ds_token, 
				   ds_url
              FROM clicksign.configuracao
			 WHERE ds_ambiente = '".trim($ds_ambiente)."'
			   AND dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_contrato_digital($args = array())
    {
        $cd_contrato_digital = $this->db->get_new_id('clicksign.contrato_digital', 'cd_contrato_digital');

        $qr_sql = "
            INSERT INTO clicksign.contrato_digital
                 (
                    cd_contrato_digital,
                    ip, 
                    dt_limite,
                    cd_empresa, 
                    cd_registro_empregado, 
                    seq_dependencia, 
                    cd_doc, 
                    id_doc, 
                    json_doc
                 )
            VALUES 
                 (
                    ".intval($cd_contrato_digital).",
                    '".$args['ip']."',
                    TO_TIMESTAMP('".$args['dt_limite']."','DD/MM/YYYY HH24:MI:SS'),
                    ".$args['cd_empresa'].", 
                    ".$args['cd_registro_empregado'].", 
                    ".$args['seq_dependencia'].",
                    ".$args['cd_doc'].",
                    '".$args['id_doc']."', 
                    '".$args['json_doc']."'                    
                 );";

        $this->db->query($qr_sql);

        return $cd_contrato_digital;
    }

    public function salvar_contrato_digital_assinatura($args = array())
    {
        $qr_sql = "
            INSERT INTO clicksign.contrato_digital_assinatura
                 (
                    cd_contrato_digital, 
                    tp_assinatura,
                    id_assinador,
                    id_assinatura, 
                    ds_url_assinatura, 
                    json_assinatura
                 )
            VALUES 
                 (
                    ".$args['cd_contrato_digital'].",
                    '".$args['tp_assinatura']."',
                    '".$args['id_assinador']."',
                    '".$args['id_assinatura']."',
                    '".$args['ds_url_assinatura']."',
                    '".$args['json_assinatura']."'            
                 );";

        $this->db->query($qr_sql);
    }

    public function get_documento_nome($cd_tipo_doc)
	{
		$qr_sql = "
			SELECT cd_tipo_doc,
			       nome_documento
			  FROM tipo_documentos
			 WHERE cd_tipo_doc = ".intval($cd_tipo_doc).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function protocolos_assinatura_docs($cd_empresa, $cd_registro_empregado, $seq_dependencia, $id_signatario, $ds_doc)
    {
        $qr_sql = "
            SELECT protocolos_assinatura_docs
              FROM oracle.protocolos_assinatura_docs(
                        ".$cd_empresa.", 
                        ".$cd_registro_empregado.", 
                        ".$seq_dependencia.",
                        '".$id_signatario."', 
                        '".$ds_doc."'
            );";

        $this->db->query($qr_sql);
    }

}