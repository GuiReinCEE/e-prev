<?php
class integracao_cadastro_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function buscar(&$result, $args=array())
    {
        $qr_sql = "
					SELECT x.nome, 
					       x.cpf, 
						   TO_CHAR(x.dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento,
						   x.telefone_1, 
						   x.telefone_2, 
						   x.email_1, 
						   x.email_2, 
                           x.endereco, 
						   x.cidade, 
						   x.uf, 
						   x.cep, 
						   x.observacao, 
						   TO_CHAR(x.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   TO_CHAR(x.dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao
					  FROM (
								SELECT se.nome, 
									   se.cpf, 
									   se.dt_nascimento, 
									   se.telefone_1, 
									   se.telefone_2, 
									   se.email_1, 
									   se.email_2, 
									   se.endereco, 
									   se.cidade, 
									   se.uf, 
									   se.cep, 
									   se.observacao, 
									   se.dt_inclusao, 
									   se.dt_alteracao
								  FROM senge_previdencia.inscrito_vw se
								 WHERE se.cpf = '".trim($args['cpf'])."'
								 
								 UNION 
								
								SELECT se.nome, 
									   se.cpf, 
									   se.dt_nascimento, 
									   se.telefone_1, 
									   se.telefone_2, 
									   se.email_1, 
									   se.email_2, 
									   se.endereco, 
									   se.cidade, 
									   se.uf, 
									   se.cep, 
									   se.observacao, 
									   se.dt_inclusao, 
									   se.dt_alteracao
								  FROM sinprors_previdencia.inscrito_vw se
								 WHERE se.cpf = '".trim($args['cpf'])."'	

								 UNION 
								
								SELECT se.nome, 
									   se.cpf, 
									   se.dt_nascimento, 
									   se.telefone_1, 
									   se.telefone_2, 
									   se.email_1, 
									   se.email_2, 
									   se.endereco, 
									   se.cidade, 
									   se.uf, 
									   se.cep, 
									   se.observacao, 
									   se.dt_inclusao, 
									   se.dt_alteracao
								  FROM familia_previdencia.inscrito_vw se
								 WHERE se.cpf = '".trim($args['cpf'])."'								 
						   ) x
					ORDER BY x.dt_alteracao DESC
					LIMIT 1
                  ";
		#echo "<PRE>$qr_sql</PRE>";exit;
        $result = $this->db->query($qr_sql);
    }

    public function buscar_formulario(&$result, $args=array())
    {
    	$qr_sql = "
    		SELECT x.*
				 FROM (
				SELECT cd_formulario, 
				       cd_instituidor,
					   ds_nome,
					   ds_nome_social,
					   dt_solicitacao,
					   dt_expedicao,
					   dt_nascimento,
					   fl_menor,
					   ds_estado_civil, 
				       tp_sexo, 
				       ds_associado, 
				       ds_vinculo_associado, 
					   ds_vinculo_grau,
					   nr_contrib_mensal,
					   nr_contrib_primeira,
					   nr_contrib_extra_inicial,
					   ds_nome_representante_legal,
					   ds_cpf_representante_legal,
					   documento_representante_legal,
					   ds_cpf,
					   ds_rg,
					   ds_orgao_expedidor,
					   ds_nome_pai,
					   ds_nome_mae,
					   ds_naturalidade,
					   ds_nacionalidade,
					   email_representante_legal,
					   telefone_representante_legal,
					   ds_cep,
					   ds_endereco,
					   nr_endereco, 
				       ds_complemento, 
				       ds_bairro, 
				       ds_cidade, 
					   ds_uf, 
				       ds_email, 
				       email_1, 
				       email_2, 
				       ds_telefone, 
				       ds_celular,
				       telefone_1,
					   telefone_2,
					   fl_ppe,
					   fl_usperson,
					   fl_pessoa_associada_ffp,
					   fl_tributacao,
					   tp_forma_pagamento_primeira,
					   tp_forma_pagamento_mensal,
					   tp_forma_pagamento_extra_inicial,
					   re_folha_pagamento,
					   ds_nome_folha_pagamento,
					   cpf_folha_pagamento,
					   ds_empresa_folha_pagamento,
					   email_folha_pagamento,
					   telefone_folha_pagamento,
					   ds_nome_debito_conta,
					   cpf_debito_conta,
					   documento_debito_conta,
					   email_debito_conta,
					   telefone_debito_conta,
					   agencia_debito_conta,
					   conta_corrente_debito_conta,
					   
					   beneficiario_1_nome, 
					   beneficiario_1_dt_nascimento, 
					   beneficiario_1_sexo,
				       beneficiario_1_beneficio,
				       beneficiario_1_cpf,
					   
					   beneficiario_2_nome, 
					   beneficiario_2_dt_nascimento, 
					   beneficiario_2_sexo,
				       beneficiario_2_beneficio,
				       beneficiario_2_cpf,
					   
					   beneficiario_3_nome, 
					   beneficiario_3_dt_nascimento, 
					   beneficiario_3_sexo,
				       beneficiario_3_beneficio,
					   beneficiario_3_cpf,
					   
					   beneficiario_4_nome, 
					   beneficiario_4_dt_nascimento, 
					   beneficiario_4_sexo,
				       beneficiario_4_beneficio,
				       beneficiario_4_cpf,
					   
					   ds_nome_vendedor,
				       ds_vendedor_celular,
				       ds_vendedor_email,
					   indicacao_interna_nome,
					   indicacao_interna_cpf,
					   cpf_indicacao,
					   ds_dia_recebimento,
					   ds_mes_recebimento,
					   ds_ano_recebimento,
					   fl_interesse_associar_ffp,
					   fl_lgpd
				  FROM familia_previdencia.cadastro_ficha_inscricao_vw x 
				 UNION
				  SELECT nr_protocolo AS cd_formulario,
				         cd_instituidor,
						 ds_nome,
						 ds_nome_social,
						 TO_CHAR(dt_alteracao, 'DD/MM/YYYY') AS dt_solicitacao,
				         TO_CHAR(dt_expedicao, 'DD/MM/YYYY') AS dt_expedicao,
						 TO_CHAR(dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento,
						 (CASE WHEN EXTRACT(YEAR FROM AGE(dt_inclusao::DATE, dt_nascimento::DATE)) < 18
							  THEN 'S'
							  ELSE 'N'
					     END) AS fl_menor,
						 ds_estado_civil, 
				         tp_sexo, 
				         ds_associado, 
				         ds_vinculo_associado, 
						 ds_vinculo_grau,
					     nr_contrib_mensal,
						 (CASE WHEN COALESCE(nr_contrib_primeira,0) = 0 
						       THEN nr_contrib_mensal 
						       ELSE nr_contrib_primeira 
						 END) AS nr_contrib_primeira,
						 nr_contrib_extra_inicial,
						 UPPER(funcoes.remove_acento(ds_nome_representante_legal)) AS ds_nome_representante_legal, 
				         ds_cpf_representante_legal, 
						 documento_representante_legal,
				         ds_cpf, 
				         ds_rg, 
				         ds_orgao_expedidor,
						 UPPER(funcoes.remove_acento(ds_nome_pai)) AS ds_nome_pai, 
				         UPPER(funcoes.remove_acento(ds_nome_mae)) AS ds_nome_mae, 
				         UPPER(funcoes.remove_acento(ds_naturalidade)) AS ds_naturalidade, 
				         UPPER(funcoes.remove_acento(ds_nacionalidade)) AS ds_nacionalidade,
						 email_representante_legal,
						 telefone_representante_legal,				   
				         ds_cep, 
				         UPPER(funcoes.remove_acento(ds_endereco)) AS ds_endereco,
						 nr_endereco, 
				         UPPER(funcoes.remove_acento(ds_complemento)) AS ds_complemento, 
				         UPPER(funcoes.remove_acento(ds_bairro)) AS ds_bairro, 
				         UPPER(funcoes.remove_acento(ds_cidade)) AS ds_cidade, 
				         ds_uf, 
				         ds_email, 
				         ds_email AS email_1, 
				         '' AS email_2, 
				         ds_telefone, 
				         ds_celular,
				         ds_celular AS telefone_1,
						 ds_telefone AS telefone_2,
						 fl_ppe,
						 fl_usperson,
						 '' AS fl_pessoa_associada_ffp,
						 fl_tributacao,
						 tp_forma_pagamento_primeira,
						 tp_forma_pagamento_mensal,
						 tp_forma_pagamento_extra_inicial,
						 folha_pagamento_re AS re_folha_pagamento,
						 ds_nome_folha_pagamento,
						 cpf_folha_pagamento,
						 ds_empresa_folha_pagamento,
						 email_folha_pagamento,
						 telefone_folha_pagamento,
						 ds_nome_debito_conta,
						 cpf_debito_conta,
						 documento_debito_conta,
						 email_debito_conta,
						 telefone_debito_conta,
						 agencia_debito_conta,
						 conta_corrente_debito_conta,
						 
						 
						 beneficiario_1_nome, 
						 TO_CHAR(beneficiario_1_dt_nascimento, 'DD/MM/YYYY') AS beneficiario_1_dt_nascimento, 
						 beneficiario_1_sexo,
				         beneficiario_1_beneficio,
				         beneficiario_1_cpf,

						 beneficiario_2_nome, 
						 TO_CHAR(beneficiario_2_dt_nascimento, 'DD/MM/YYYY') AS beneficiario_2_dt_nascimento, 
						 beneficiario_2_sexo,
				         beneficiario_2_beneficio,
				         beneficiario_2_cpf,
						 
						 beneficiario_3_nome, 
						 TO_CHAR(beneficiario_3_dt_nascimento, 'DD/MM/YYYY') AS beneficiario_3_dt_nascimento, 
						 beneficiario_3_sexo,
				         beneficiario_3_beneficio,
				         beneficiario_3_cpf,

					     beneficiario_4_nome, 
						 TO_CHAR(beneficiario_4_dt_nascimento, 'DD/MM/YYYY') AS beneficiario_4_dt_nascimento, 
						 beneficiario_4_sexo,
				         beneficiario_4_beneficio,
				         beneficiario_4_cpf,
						 
						 ds_nome_vendedor,
				         ds_vendedor_celular,
				         ds_vendedor_email,
						 indicacao_interna_nome,
						 indicacao_interna_cpf,
						 cpf_indicacao,
						 TO_CHAR(dt_recebimento, 'DD') AS ds_dia_recebimento,   
				         TO_CHAR(dt_recebimento, 'MM') AS ds_mes_recebimento,   
				         TO_CHAR(dt_recebimento, 'YYYY') AS ds_ano_recebimento,
						 '' AS fl_interesse_associar_ffp,
				         '' AS fl_lgpd
				    FROM familiavendas.app_solicitacao
				) x
				WHERE x.cd_formulario = '".trim($args['cd_formulario'])."';";

    	$result = $this->db->query($qr_sql);
    }
}
?>
