<?php
class solicitacao_model extends model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function instituidor($args = array())
    {
    	$qr_sql = "
					SELECT pa.cd_empresa AS value, 
					       UPPER(pa.sigla) AS text
					  FROM public.patrocinadoras pa
					  JOIN public.planos_patrocinadoras p
					    ON p.cd_empresa = pa.cd_empresa
					   AND p.cd_plano   = 9
					 ORDER BY text
			      ";

    	return $this->db->query($qr_sql)->result_array();
    }	

	public function estado_civil($args = array())
    {
    	$qr_sql = "
					SELECT UPPER(ds_estado_civil) AS value, 
					       UPPER(ds_estado_civil) AS text
					  FROM familia_previdencia.estado_civil
			      ";

    	return $this->db->query($qr_sql)->result_array();
    }

	public function listar($args = array())
    {
    	$qr_sql = "
	        SELECT cd_app_solicitacao,
	        	   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
	        	   TO_CHAR(dt_alteracao,'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
	        	   TO_CHAR(dt_cancelado,'DD/MM/YYYY HH24:MI:SS') AS dt_cancelado,
	        	   TO_CHAR(dt_analise,'DD/MM/YYYY HH24:MI:SS') AS dt_analise,
	        	   TO_CHAR(dt_concluido,'DD/MM/YYYY HH24:MI:SS') AS dt_concluido,
	        	   (CASE WHEN tp_status = 'N'
			             THEN 'Casdatro não concluído'
			             WHEN tp_status = 'S'
			             THEN 'Solicitado'
			             WHEN tp_status = 'A'
			             THEN 'Em análise'
			             WHEN tp_status = 'C'
			             THEN 'Cancelado'
			             WHEN tp_status = 'O'
			             THEN 'Concluído'
			             ELSE 'Outros'
    			   END) AS ds_status,
    			   (CASE WHEN tp_status = 'N'
				         THEN 'label label-warning'
				         WHEN tp_status = 'S'
				         THEN 'label label-success'
				         WHEN tp_status = 'A'
				         THEN 'label label-info'
				         WHEN tp_status = 'O'
				         THEN 'label label'
				         WHEN tp_status = 'C'
				         THEN 'label label-important'
				         ELSE 'label'
   				   END) AS ds_class_status,
				   TO_CHAR(dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
				   CASE WHEN EXTRACT(YEAR FROM age(dt_nascimento::date)) < 18
						THEN 'S'
						ELSE 'N'
				   END AS fl_menor,				   
	               ds_nome,
	               ds_cpf,
				   cpf_indicacao,
	               nr_protocolo,
	               nr_contrib_mensal,
	               ds_celular,
	               ds_telefone,
	               ds_email,
				   fl_ppe,
				   fl_usperson,
				   fl_tributacao,
				   beneficiario_1_nome, TO_CHAR(beneficiario_1_dt_nascimento,'DD/MM/YYYY') AS beneficiario_1_dt_nascimento, beneficiario_1_sexo, beneficiario_1_beneficio,
				   beneficiario_2_nome, TO_CHAR(beneficiario_2_dt_nascimento,'DD/MM/YYYY') AS beneficiario_2_dt_nascimento, beneficiario_2_sexo, beneficiario_2_beneficio,
				   beneficiario_3_nome, TO_CHAR(beneficiario_3_dt_nascimento,'DD/MM/YYYY') AS beneficiario_3_dt_nascimento, beneficiario_3_sexo, beneficiario_3_beneficio,
				   beneficiario_4_nome, TO_CHAR(beneficiario_4_dt_nascimento,'DD/MM/YYYY') AS beneficiario_4_dt_nascimento, beneficiario_4_sexo, beneficiario_4_beneficio
	          FROM familiavendas.app_solicitacao
	         WHERE tp_status != 'N'
	         	".((trim($args['nr_protocolo']) != '') ? " AND nr_protocolo = '".trim($args['nr_protocolo']). "'": " ")."
	            ".(((trim($args['dt_inclusao_ini']) != '') AND (trim($args['dt_inclusao_fim']) != '')) ? " AND DATE_TRUNC('day', dt_alteracao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")." 
	            ".((trim($args['tp_status']) == 'S') ? " AND tp_status = '".trim($args['tp_status']). "'": " ")."
	            ".((trim($args['tp_status']) == 'A') ? " AND tp_status = '".trim($args['tp_status']). "'": " ")."
	            ".((trim($args['tp_status']) == 'C') ? " AND tp_status = '".trim($args['tp_status']). "'": " ")."
	            ".((trim($args['tp_status']) == 'O') ? " AND tp_status = '".trim($args['tp_status']). "'": " ").";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function cadastro($cd_app_solicitacao = 0, $nr_protocolo = "")
    {
    	$qr_sql = "
	        SELECT cd_app_solicitacao,
	        	   MD5(MD5('CDSOLICITACAO' || cd_app_solicitacao)) AS cd_app_solicitacao_md5,
	        	   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
	        	   TO_CHAR(dt_alteracao,'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
	        	   TO_CHAR(dt_cancelado,'DD/MM/YYYY HH24:MI:SS') AS dt_cancelado,
	        	   TO_CHAR(dt_analise,'DD/MM/YYYY HH24:MI:SS') AS dt_analise,
	        	   TO_CHAR(dt_concluido,'DD/MM/YYYY HH24:MI:SS') AS dt_concluido,
	        	   (CASE WHEN tp_status = 'N'
		            	 THEN 'Casdatro não concluído'
			             WHEN tp_status = 'S'
			             THEN 'Solicitado'
			             WHEN tp_status = 'A'
			             THEN 'Em análise'
			             WHEN tp_status = 'C'
			             THEN 'Cancelado'
			             WHEN tp_status = 'O'
			             THEN 'Concluído'
			             ELSE 'Outros'
    			    END) AS ds_status,
    			    (CASE WHEN tp_status = 'N'
				          THEN 'label label-warning'
				          WHEN tp_status = 'S'
				          THEN 'label label-success'
				          WHEN tp_status = 'A'
				          THEN 'label label-info'
				          WHEN tp_status = 'O'
				          THEN 'label label'
				          WHEN tp_status = 'C'
				          THEN 'label label-important'
				          ELSE 'label'
   					END) AS ds_class_status,
				   TO_CHAR(dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
				   CASE WHEN EXTRACT(YEAR FROM AGE(dt_inclusao::DATE, dt_nascimento::DATE)) < 18
						THEN 'S'
						ELSE 'N'
				   END AS fl_menor,
	               UPPER(funcoes.remove_acento(TRIM(ds_nome))) AS ds_nome,
	               ds_cpf,
				   cpf_indicacao,
	               ds_nome_representante_legal,
	               ds_cpf_representante_legal,
				   email_representante_legal,
				   telefone_representante_legal,
	               nr_contrib_mensal,
				   (CASE WHEN COALESCE(nr_contrib_primeira,0) = 0 THEN nr_contrib_mensal ELSE nr_contrib_primeira END) AS nr_contrib_primeira,
				   nr_contrib_extra_inicial,
	               ds_celular,
	               ds_telefone,
	               ds_email,
	               UPPER(ds_estado_civil) AS ds_estado_civil,
	               tp_sexo,
	               ds_associado,
	               ds_vinculo_associado,
				   ds_vinculo_grau,
				   ds_rg,
				   ds_orgao_expedidor,
				   TO_CHAR(dt_expedicao,'DD/MM/YYYY') AS dt_expedicao,
				   ds_nome_pai,
				   ds_nome_mae,
				   ds_naturalidade,
				   ds_nacionalidade,
				   nr_protocolo,
				   ds_cep,
				   ds_endereco,
				   nr_endereco,
				   ds_complemento,
				   ds_bairro,
				   ds_cidade,
				   ds_uf,
				   ds_celular,
				   ds_telefone,
				   ds_email,
				   ds_doc_representante_frente,
				   ds_doc_representante_verso,
				   ds_doc_frente,
				   ds_doc_verso,
				   fl_ppe,
				   fl_usperson,
				   fl_tributacao,
				   tp_forma_pagamento_primeira,
				   tp_forma_pagamento_mensal,
				   tp_forma_pagamento_extra_inicial,
				   ds_nome_folha_pagamento,
				   cpf_folha_pagamento,
				   ds_empresa_folha_pagamento,
				   email_folha_pagamento,
				   telefone_folha_pagamento,
				   ds_nome_debito_conta,
				   cpf_debito_conta,
				   email_debito_conta,
				   telefone_debito_conta,
				   agencia_debito_conta,
				   conta_corrente_debito_conta,				   
				   beneficiario_1_nome, TO_CHAR(beneficiario_1_dt_nascimento,'DD/MM/YYYY') AS beneficiario_1_dt_nascimento, beneficiario_1_sexo, beneficiario_1_cpf, beneficiario_1_beneficio, 
				   beneficiario_2_nome, TO_CHAR(beneficiario_2_dt_nascimento,'DD/MM/YYYY') AS beneficiario_2_dt_nascimento, beneficiario_2_sexo, beneficiario_2_cpf, beneficiario_2_beneficio, 
				   beneficiario_3_nome, TO_CHAR(beneficiario_3_dt_nascimento,'DD/MM/YYYY') AS beneficiario_3_dt_nascimento, beneficiario_3_sexo, beneficiario_3_cpf, beneficiario_3_beneficio, 
				   beneficiario_4_nome, TO_CHAR(beneficiario_4_dt_nascimento,'DD/MM/YYYY') AS beneficiario_4_dt_nascimento, beneficiario_4_sexo, beneficiario_4_cpf, beneficiario_4_beneficio,
				   ds_nome_vendedor,
				   ds_vendedor_celular,
                   ds_vendedor_email,
				   TO_CHAR(dt_recebimento,'DD/MM/YYYY') AS dt_recebimento,
				   id_doc_assinatura,
				   cd_instituidor,
				   indicacao_interna_nome,
				   indicacao_interna_cpf,
				   ds_nome_social,
				   fl_lgpd
	          FROM familiavendas.app_solicitacao
	         WHERE cd_app_solicitacao = ".intval($cd_app_solicitacao)."
			    OR nr_protocolo = '".trim($nr_protocolo)."';
			 
			 ";
		#echo $qr_sql; #exit;
    	return $this->db->query($qr_sql)->row_array();
    }

    public function atualizar($cd_app_solicitacao, $args = array())
    {
    	$qr_sql = "
	    	UPDATE familiavendas.app_solicitacao
		       SET ds_nome           		    = ".(trim($args['ds_nome']) != '' ? str_escape($args['ds_nome']) : "DEFAULT").",
		       	   dt_nascimento          	    = ".(trim($args['dt_nascimento']) != '' ? "TO_DATE('".$args['dt_nascimento']."','DD/MM/YYYY')" : "DEFAULT").",
		       	   cd_instituidor				= ".(trim($args['cd_instituidor']) != '' ? intval($args['cd_instituidor']) : "DEFAULT").",
				   ds_associado  			    = ".(trim($args['ds_associado']) != '' ? str_escape($args['ds_associado'])  : "DEFAULT").",
		       	   ds_vinculo_associado         = ".(trim($args['ds_vinculo_associado']) != '' ? str_escape($args['ds_vinculo_associado']) : "DEFAULT").",
		       	   ds_vinculo_grau              = ".(trim($args['ds_vinculo_grau']) != '' ? str_escape($args['ds_vinculo_grau']) : "DEFAULT").",
		       	   nr_contrib_primeira          = ".(trim($args['nr_contrib_primeira']) != '' ? floatval($args['nr_contrib_primeira']) : "DEFAULT").",
		       	   nr_contrib_mensal            = ".(trim($args['nr_contrib_mensal']) != '' ? floatval($args['nr_contrib_mensal']) : "DEFAULT").",
		       	   nr_contrib_extra_inicial     = ".(trim($args['nr_contrib_extra_inicial']) != '' ? floatval($args['nr_contrib_extra_inicial']) : "DEFAULT").",
				   tp_forma_pagamento_primeira 		= ".(trim($args['tp_forma_pagamento_primeira']) != '' ? str_escape($args['tp_forma_pagamento_primeira']) : "DEFAULT").",
				   tp_forma_pagamento_mensal 		= ".(trim($args['tp_forma_pagamento_mensal']) != '' ? str_escape($args['tp_forma_pagamento_mensal']) : "DEFAULT").",
				   tp_forma_pagamento_extra_inicial = ".(trim($args['tp_forma_pagamento_extra_inicial']) != '' ? str_escape($args['tp_forma_pagamento_extra_inicial']) : "DEFAULT").",
				   ds_cpf 					    = ".(trim($args['ds_cpf']) != '' ? str_escape($args['ds_cpf']) : "DEFAULT").",
				   cpf_indicacao 					    = ".(trim($args['cpf_indicacao']) != '' ? str_escape($args['cpf_indicacao']) : "DEFAULT").",
				   ds_rg 					    = ".(trim($args['ds_rg']) != '' ? str_escape($args['ds_rg']) : "DEFAULT").",
				   ds_orgao_expedidor		    = ".(trim($args['ds_orgao_expedidor']) != '' ? str_escape($args['ds_orgao_expedidor']) : "DEFAULT").",
				   dt_expedicao				    = ".(trim($args['dt_expedicao']) != '' ? "TO_DATE('".$args['dt_expedicao']."','DD/MM/YYYY')" : "DEFAULT").",
				   ds_nome_pai			   	    = ".(trim($args['ds_nome_pai']) != '' ? str_escape($args['ds_nome_pai']) : "DEFAULT").",
				   ds_nome_mae				    = ".(trim($args['ds_nome_mae']) != '' ? str_escape($args['ds_nome_mae']) : "DEFAULT").",
				   ds_naturalidade			    = ".(trim($args['ds_naturalidade']) != '' ? str_escape($args['ds_naturalidade']) : "DEFAULT").",
				   ds_nacionalidade			    = ".(trim($args['ds_nacionalidade']) != '' ? str_escape($args['ds_nacionalidade']) : "DEFAULT").",
				   ds_nome_representante_legal  = ".(trim($args['ds_nome_representante_legal']) != '' ? str_escape($args['ds_nome_representante_legal']) : "DEFAULT").",
				   ds_cpf_representante_legal   = ".(trim($args['ds_cpf_representante_legal']) != '' ? str_escape($args['ds_cpf_representante_legal']) : "DEFAULT").",
				   email_representante_legal    = ".(trim($args['email_representante_legal']) != '' ? str_escape($args['email_representante_legal']) : "DEFAULT").",
				   telefone_representante_legal = ".(trim($args['telefone_representante_legal']) != '' ? str_escape($args['telefone_representante_legal']) : "DEFAULT").",
				   ds_cep 					    = ".(trim($args['ds_cep']) != '' ? str_escape($args['ds_cep']) : "DEFAULT").",
				   ds_endereco 				    = ".(trim($args['ds_endereco']) != '' ? str_escape($args['ds_endereco']) : "DEFAULT").",
				   nr_endereco				    = ".(trim($args['nr_endereco']) != '' ? intval($args['nr_endereco']) : "DEFAULT").",
				   ds_complemento			    = ".(trim($args['ds_complemento']) != '' ? str_escape($args['ds_complemento'])  : "DEFAULT").",
				   ds_bairro				    = ".(trim($args['ds_bairro']) != '' ? str_escape($args['ds_bairro']) : "DEFAULT").",
				   ds_cidade 				    = ".(trim($args['ds_cidade']) != '' ? str_escape($args['ds_cidade']) : "DEFAULT").",
				   ds_uf 					    = ".(trim($args['ds_uf']) != '' ? str_escape($args['ds_uf']) : "DEFAULT").",
				   ds_celular 				    = ".(trim($args['ds_celular']) != '' ? str_escape($args['ds_celular']) : "DEFAULT").",
				   ds_telefone 				    = ".(trim($args['ds_telefone']) != '' ? str_escape($args['ds_telefone']) : "DEFAULT").",
				   ds_email 				    = ".(trim($args['ds_email']) != '' ? str_escape($args['ds_email']) : "DEFAULT").",
				   fl_ppe 				        = ".(trim($args['fl_ppe']) != '' ? str_escape($args['fl_ppe']) : "DEFAULT").",
				   fl_usperson 				    = ".(trim($args['fl_usperson']) != '' ? str_escape($args['fl_usperson']) : "DEFAULT").",
				   fl_tributacao 			    = ".(trim($args['fl_tributacao']) != '' ? str_escape($args['fl_tributacao']) : "DEFAULT").",
				   ds_nome_folha_pagamento      = ".(trim($args['ds_nome_folha_pagamento']) != '' ? str_escape($args['ds_nome_folha_pagamento']) : "DEFAULT").",
				   cpf_folha_pagamento          = ".(trim($args['cpf_folha_pagamento']) != '' ? str_escape($args['cpf_folha_pagamento']) : "DEFAULT").",
				   ds_empresa_folha_pagamento   = ".(trim($args['ds_empresa_folha_pagamento']) != '' ? str_escape($args['ds_empresa_folha_pagamento']) : "DEFAULT").",
				   email_folha_pagamento        = ".(trim($args['email_folha_pagamento']) != '' ? str_escape($args['email_folha_pagamento']) : "DEFAULT").",
				   telefone_folha_pagamento     = ".(trim($args['telefone_folha_pagamento']) != '' ? str_escape($args['telefone_folha_pagamento']) : "DEFAULT").",
				   ds_nome_debito_conta         = ".(trim($args['ds_nome_debito_conta']) != '' ? str_escape($args['ds_nome_debito_conta']) : "DEFAULT").",
				   cpf_debito_conta             = ".(trim($args['cpf_debito_conta']) != '' ? str_escape($args['cpf_debito_conta']) : "DEFAULT").",
				   email_debito_conta           = ".(trim($args['email_debito_conta']) != '' ? str_escape($args['email_debito_conta']) : "DEFAULT").",
				   telefone_debito_conta        = ".(trim($args['telefone_debito_conta']) != '' ? str_escape($args['telefone_debito_conta']) : "DEFAULT").",
				   agencia_debito_conta         = ".(trim($args['agencia_debito_conta']) != '' ? str_escape($args['agencia_debito_conta']) : "DEFAULT").",
				   conta_corrente_debito_conta	= ".(trim($args['conta_corrente_debito_conta']) != '' ? str_escape($args['conta_corrente_debito_conta']) : "DEFAULT").",
				  
				   beneficiario_1_nome			= ".(trim($args['beneficiario_1_nome']) != '' ? str_escape($args['beneficiario_1_nome']) : "DEFAULT").",
				   beneficiario_1_dt_nascimento = ".(trim($args['beneficiario_1_dt_nascimento']) != '' ? "TO_DATE('".$args['beneficiario_1_dt_nascimento']."','DD/MM/YYYY')" : "DEFAULT").",
				   beneficiario_1_sexo 			= ".(trim($args['beneficiario_1_sexo']) != '' ? str_escape($args['beneficiario_1_sexo']) : "DEFAULT").",
				   beneficiario_1_cpf 			= ".(trim($args['beneficiario_1_cpf']) != '' ? str_escape($args['beneficiario_1_cpf']) : "DEFAULT").",
				   beneficiario_1_beneficio     = ".(trim($args['beneficiario_1_beneficio']) != '' ? intval($args['beneficiario_1_beneficio']) : "DEFAULT").",
				   
				   beneficiario_2_nome			= ".(trim($args['beneficiario_2_nome']) != '' ? str_escape($args['beneficiario_2_nome']) : "DEFAULT").",
				   beneficiario_2_dt_nascimento = ".(trim($args['beneficiario_2_dt_nascimento']) != '' ? "TO_DATE('".$args['beneficiario_2_dt_nascimento']."','DD/MM/YYYY')" : "DEFAULT").",
				   beneficiario_2_sexo			= ".(trim($args['beneficiario_2_sexo']) != '' ? str_escape($args['beneficiario_2_sexo']) : "DEFAULT").",
				   beneficiario_2_cpf			= ".(trim($args['beneficiario_2_cpf']) != '' ? str_escape($args['beneficiario_2_cpf']) : "DEFAULT").",
				   beneficiario_2_beneficio     = ".(trim($args['beneficiario_2_beneficio']) != '' ? intval($args['beneficiario_2_beneficio']) : "DEFAULT").",
				   
				   beneficiario_3_nome			= ".(trim($args['beneficiario_3_nome']) != '' ? str_escape($args['beneficiario_3_nome']) : "DEFAULT").",
				   beneficiario_3_dt_nascimento = ".(trim($args['beneficiario_3_dt_nascimento']) != '' ? "TO_DATE('".$args['beneficiario_3_dt_nascimento']."','DD/MM/YYYY')" : "DEFAULT").",
				   beneficiario_3_sexo			= ".(trim($args['beneficiario_3_sexo']) != '' ? str_escape($args['beneficiario_3_sexo']) : "DEFAULT").",
				   beneficiario_3_cpf			= ".(trim($args['beneficiario_3_cpf']) != '' ? str_escape($args['beneficiario_3_cpf']) : "DEFAULT").",
				   beneficiario_3_beneficio     = ".(trim($args['beneficiario_3_beneficio']) != '' ? intval($args['beneficiario_3_beneficio']) : "DEFAULT").",
				   
				   beneficiario_4_nome 			= ".(trim($args['beneficiario_4_nome']) != '' ? str_escape($args['beneficiario_4_nome']) : "DEFAULT").",
				   beneficiario_4_dt_nascimento = ".(trim($args['beneficiario_4_dt_nascimento']) != '' ? "TO_DATE('".$args['beneficiario_4_dt_nascimento']."','DD/MM/YYYY')" : "DEFAULT").",
				   beneficiario_4_sexo 			= ".(trim($args['beneficiario_4_sexo']) != '' ? str_escape($args['beneficiario_4_sexo']) : "DEFAULT").",
				   beneficiario_4_cpf 			= ".(trim($args['beneficiario_4_cpf']) != '' ? str_escape($args['beneficiario_4_cpf']) : "DEFAULT").",
				   beneficiario_4_beneficio     = ".(trim($args['beneficiario_4_beneficio']) != '' ? intval($args['beneficiario_4_beneficio']) : "DEFAULT").",
				   
				   ds_nome_vendedor 			= ".(trim($args['ds_nome_vendedor']) != '' ? str_escape($args['ds_nome_vendedor']) : "DEFAULT").",
				   ds_vendedor_celular 			= ".(trim($args['ds_vendedor_celular']) != '' ? str_escape($args['ds_vendedor_celular']) : "DEFAULT").",
				   ds_vendedor_email 			= ".(trim($args['ds_vendedor_email']) != '' ? str_escape($args['ds_vendedor_email']) : "DEFAULT").",
				   dt_recebimento          	    = ".(trim($args['dt_recebimento']) != '' ? "TO_DATE('".$args['dt_recebimento']."','DD/MM/YYYY')" : "DEFAULT").",			   
				   indicacao_interna_nome       = ".(trim($args['indicacao_interna_nome']) != '' ? str_escape($args['indicacao_interna_nome']) : "DEFAULT").",
				   indicacao_interna_cpf        = ".(trim($args['indicacao_interna_cpf']) != '' ? str_escape($args['indicacao_interna_cpf']) : "DEFAULT").",
				   ds_nome_social               = ".(trim($args['ds_nome_social']) != '' ? str_escape($args['ds_nome_social']) : "DEFAULT").",
				   fl_lgpd                      = ".(trim($args['fl_lgpd']) != '' ? str_escape($args['fl_lgpd']) : "DEFAULT").",
				   dt_alteracao			  	    = CURRENT_TIMESTAMP
		      WHERE cd_app_solicitacao  = ".intval($cd_app_solicitacao).";";
    	
    	$this->db->query($qr_sql);
    }

    public function acompanhamento($cd_app_solicitacao)
    {
    	$qr_sql = "
	        SELECT cd_app_solicitacao,
	        	   nr_protocolo,
	        	   TO_CHAR(dt_alteracao,'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
	        	   (CASE WHEN tp_status = 'N'
		                 THEN 'Casdatro não concluído'
		                 WHEN tp_status = 'S'
		                 THEN 'Solicitado'
		                 WHEN tp_status = 'A'
		                 THEN 'Em análise'
		                 WHEN tp_status = 'C'
		                 THEN 'Cancelado'
		                 WHEN tp_status = 'O'
		                 THEN 'Concluído'
		                 ELSE 'Outros'
    			    END) AS ds_status,
    			    (CASE WHEN tp_status = 'N'
			              THEN 'label label-warning'
			              WHEN tp_status = 'S'
			              THEN 'label label-success'
			              WHEN tp_status = 'A'
			              THEN 'label label-info'
			              WHEN tp_status = 'O'
			              THEN 'label label'
			              WHEN tp_status = 'C'
			              THEN 'label label-important'
			              ELSE 'label'
   					END) AS ds_class_status,
    			    ds_nome
	          FROM familiavendas.app_solicitacao
	         WHERE cd_app_solicitacao = ".intval($cd_app_solicitacao).";";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function listar_acompanhamento($cd_app_solicitacao)
    {
    	$qr_sql = "
	        SELECT cd_app_solicitacao_acompanhamento,
	        	   ds_app_solicitacao_acompanhamento,
	        	   TO_CHAR(dt_alteracao,'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
    			   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
	          FROM familiavendas.app_solicitacao_acompanhamento
	         WHERE cd_app_solicitacao = ".intval($cd_app_solicitacao).";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_acompanhamento($args = array())
    {
    	$qr_sql = "
	        INSERT INTO familiavendas.app_solicitacao_acompanhamento
	        	 (
	        	 	ds_app_solicitacao_acompanhamento,
	        	 	cd_usuario_inclusao,
	        	 	cd_app_solicitacao,
	        	 	cd_usuario_alteracao
	        	 )
	        	 VALUES 
	        	 (
	        	 	'".trim($args['ds_app_solicitacao_acompanhamento'])."',
	        	 	".intval($args['cd_usuario']).",
	        	 	".intval($args['cd_app_solicitacao']).",
	        	 	".intval($args['cd_usuario'])."
	        	 );";

    	$this->db->query($qr_sql);
    }

    public function em_analise($cd_app_solicitacao, $cd_usuario)
    {
    	$qr_sql = "
    	 	UPDATE familiavendas.app_solicitacao
	           SET tp_status           = 'A',
	           	   dt_analise          = CURRENT_TIMESTAMP,
	        	   cd_usuario_analise  = ".intval($cd_usuario)."
	         WHERE cd_app_solicitacao  = ".intval($cd_app_solicitacao).";";

    	$this->db->query($qr_sql);
    }

    public function concluir($cd_app_solicitacao, $cd_usuario)
    {
    	$qr_sql = "
	    	UPDATE familiavendas.app_solicitacao
		       SET tp_status 			 = 'O',
		           dt_concluido         = CURRENT_TIMESTAMP,
		           cd_usuario_concluido = ".intval($cd_usuario)."
		     WHERE cd_app_solicitacao   = ".intval($cd_app_solicitacao).";";
    	
    	$this->db->query($qr_sql);
    }

    public function cancelar($cd_app_solicitacao, $cd_usuario)
    {
    	$qr_sql = "
    	 	UPDATE familiavendas.app_solicitacao
	           SET tp_status 			= 'C',
	               dt_cancelado        	= CURRENT_TIMESTAMP,
	        	   cd_usuario_cancelado = ".intval($cd_usuario)."
	         WHERE cd_app_solicitacao 	= ".intval($cd_app_solicitacao).";";
    	
    	$this->db->query($qr_sql);
    }
	
    public function setIDdocAssinar($cd_app_solicitacao, $cd_documento)
    {
    	$qr_sql = "
    	 	UPDATE familiavendas.app_solicitacao
	           SET id_doc_assinatura 	= '".$cd_documento."',
	               dt_alteracao        	= CURRENT_TIMESTAMP
	         WHERE cd_app_solicitacao 	= ".intval($cd_app_solicitacao).";";
    	
    	$this->db->query($qr_sql);
    }	
}