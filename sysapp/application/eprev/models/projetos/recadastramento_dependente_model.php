<?php
class Recadastramento_dependente_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT rd.cd_recadastramento_dependente,
				   TO_CHAR(rd.dt_envio_participante, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_participante,
			       rd.cd_empresa, 
			       rd.cd_registro_empregado,  
			       rd.seq_dependencia,
			       projetos.participante_nome(rd.cd_empresa, rd.cd_registro_empregado, rd.seq_dependencia) AS ds_nome,
				   TO_CHAR(rd.dt_confirmacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_confirmacao,
				   funcoes.get_usuario_nome(rd.cd_usuario_confirmacao) AS ds_usuario_confirmacao,
				   TO_CHAR(rd.dt_cancelamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento,
				   funcoes.get_usuario_nome(rd.cd_usuario_cancelamento) AS ds_usuario_cancelamento,
				   rd.ds_justificativa,
				   TO_CHAR(cd.dt_limite, 'DD/MM/YYYY HH24:MI:SS') AS dt_limite,
				   CASE WHEN (CAST(cd.dt_limite AS DATE) < CURRENT_DATE) OR cd.dt_concluido IS NOT NULL THEN 'label' 
				        WHEN CAST((cd.dt_limite - '1 week'::INTERVAL) AS DATE) < CURRENT_DATE THEN 'label label-important'
						ELSE 'label' 
				   END AS cor_limite,
				   (CASE WHEN cd.dt_concluido IS NOT NULL THEN 'Concluído'
						 WHEN (cd.dt_cancelado IS NOT NULL OR cd.dt_finalizado IS NOT NULL) THEN 'Cancelado/Finalizado'
						 WHEN (SELECT COUNT(*)
								 FROM clicksign.contrato_digital_assinatura cda1
								WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
								  AND cda1.tp_assinatura = 'P'
								  AND cda1.dt_assinatura IS NULL) > 0 THEN 'Pendente Participante'
						 WHEN (SELECT COUNT(*)
								 FROM clicksign.contrato_digital_assinatura cda1
								WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
								  AND cda1.tp_assinatura = 'T1'
								  AND cda1.dt_assinatura IS NULL) > 0 THEN 'Pendente Testemunha 1'	
						 WHEN (SELECT COUNT(*)
								 FROM clicksign.contrato_digital_assinatura cda1
								WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
								  AND cda1.tp_assinatura = 'T2'
								  AND cda1.dt_assinatura IS NULL) > 0 THEN 'Pendente Testemunha 2'	
						 WHEN (SELECT COUNT(*)
								 FROM clicksign.contrato_digital_assinatura cda1
								WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
								  AND cda1.tp_assinatura = 'V'
								  AND cda1.dt_assinatura IS NULL) > 0 THEN 'Pendente Validador'											  
						 ELSE 'Não identificado'
				   END) AS situacao,  				   
				   (CASE WHEN cd.dt_concluido IS NOT NULL THEN 'label label-success' --Concluído
						 WHEN (cd.dt_cancelado IS NOT NULL OR cd.dt_finalizado IS NOT NULL) THEN 'label' --Cancelado/Finalizado
						 WHEN (SELECT COUNT(*)
								 FROM clicksign.contrato_digital_assinatura cda1
								WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
								  AND cda1.tp_assinatura = 'P'
								  AND cda1.dt_assinatura IS NULL) > 0 THEN 'label label-warning' --Pendente Participante
						 WHEN (SELECT COUNT(*)
								 FROM clicksign.contrato_digital_assinatura cda1
								WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
								  AND cda1.tp_assinatura = 'T1'
								  AND cda1.dt_assinatura IS NULL) > 0 THEN 'label label-important' --Pendente Testemunha 1
						 WHEN (SELECT COUNT(*)
								 FROM clicksign.contrato_digital_assinatura cda1
								WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
								  AND cda1.tp_assinatura = 'T2'
								  AND cda1.dt_assinatura IS NULL) > 0 THEN 'label label-important' --Pendente Testemunha 2
						 WHEN (SELECT COUNT(*)
								 FROM clicksign.contrato_digital_assinatura cda1
								WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
								  AND cda1.tp_assinatura = 'V'
								  AND cda1.dt_assinatura IS NULL) > 0 THEN 'label label-important' --Pendente Validador											  
						 ELSE 'Não identificado'
				   END) AS situacao_label				   
				   
			  FROM autoatendimento.recadastramento_dependente rd
			  LEFT JOIN clicksign.contrato_digital cd
			    ON cd.cd_contrato_digital = rd.cd_contrato_digital 			  
			 WHERE rd.dt_envio_participante IS NOT NULL

			   ".(trim($args['fl_pendente']) == 'S' ? "AND cd.dt_concluido IS NULL AND cd.dt_cancelado IS NULL AND cd.dt_finalizado IS NULL" : "")."												
			   ".(trim($args['fl_pendente']) == 'N' ? "AND (cd.dt_concluido IS NOT NULL OR cd.dt_cancelado IS NOT NULL OR cd.dt_finalizado IS NOT NULL)" : "")."												
				".(trim($args['fl_pendente_participante']) == 'S' ? 
				"
					AND cd.dt_concluido IS NULL AND cd.dt_cancelado IS NULL AND cd.dt_finalizado IS NULL
					AND (SELECT COUNT(*)
									 FROM clicksign.contrato_digital_assinatura cda1
									WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
									  AND cda1.tp_assinatura = 'P'
									  AND cda1.dt_assinatura IS NULL) > 0
				
				" : "")."
				".(trim($args['fl_pendente_participante']) == 'N' ? 
				"
					AND cd.dt_concluido IS NULL AND cd.dt_cancelado IS NULL AND cd.dt_finalizado IS NULL
					AND (SELECT COUNT(*)
						   FROM clicksign.contrato_digital_assinatura cda1
						  WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
							AND cda1.tp_assinatura = 'P'
							AND cda1.dt_assinatura IS NOT NULL) > 0
				
				" : "")."

			   ".(trim($args['cd_empresa']) != '' ? "AND rd.cd_empresa = ".intval($args['cd_empresa']) : "")."			   
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND rd.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."			   
			   ".(trim($args['seq_dependencia']) != '' ? "AND rd.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
			   ".(trim($args['fl_cancelado']) == 'S' ? "AND rd.dt_cancelamento IS NOT NULL" : "")."
			   ".(trim($args['fl_cancelado']) == 'N' ? "AND rd.dt_cancelamento IS NULL" : "")."
			   ".(trim($args['fl_confirmado']) == 'S' ? "AND rd.dt_confirmacao IS NOT NULL" : "")."
			   ".(trim($args['fl_confirmado']) == 'N' ? "AND rd.dt_confirmacao IS NULL" : "")."
			   ".(((trim($args['dt_confirmacao_ini']) != '') AND (trim($args['dt_confirmacao_fim']) != '')) ? " AND DATE_TRUNC('day', rd.dt_confirmacao) BETWEEN TO_DATE('".$args['dt_confirmacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_confirmacao_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_envio_participante_ini']) != '') AND (trim($args['dt_envio_participante_fim']) != '')) ? " AND DATE_TRUNC('day', rd.dt_envio_participante) BETWEEN TO_DATE('".$args['dt_envio_participante_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_participante_fim']."', 'DD/MM/YYYY')" : "").";";
		
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_recadastramento_dependente)
	{
		$qr_sql = "
			SELECT rd.cd_recadastramento_dependente,
				   funcoes.cripto_re(rd.cd_empresa, rd.cd_registro_empregado, rd.seq_dependencia) AS re_cripto,
				   rd.cd_empresa, 
			       rd.cd_registro_empregado,  
			       rd.seq_dependencia,
				   TO_CHAR(rd.dt_solicitacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_solicitacao,
				   p.nome,
				   TO_CHAR(rd.dt_confirmacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_confirmacao,
				   funcoes.get_usuario_nome(cd_usuario_confirmacao) AS ds_usuario_confirmacao,
				   TO_CHAR(rd.dt_cancelamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento,
				   funcoes.get_usuario_nome(cd_usuario_cancelamento) AS ds_usuario_cancelamento,
				   TO_CHAR(rd.dt_confirmacao_endereco, 'DD/MM/YYYY HH24:MI:SS') AS dt_confirmacao_endereco,
				   funcoes.get_usuario_nome(cd_usuario_confirmacao_endereco) AS ds_usuario_confirmacao_endereco,
				   rd.ds_justificativa,
				   rd.fl_sem_dependente AS fl_dependente,
				   rd.cd_contrato_digital,
				   cd.id_doc,
				   rd.endereco,
                   rd.nr_endereco,
                   rd.complemento_endereco,
                   rd.logradouro,
                   rd.bairro,
                   rd.cidade,
                   rd.unidade_federativa,
                   rd.cep,
                   TRIM(TO_CHAR(rd.complemento_cep,'000')) AS complemento_cep,
                   rd.ddd,
                   rd.telefone,
                   rd.ramal,
                   rd.ddd_celular,
                   rd.celular,
                   rd.email,
                   rd.email_profissional,
                   rd.banco,
                   rd.agencia,
                   rd.conta,
                   rd.nome_correntista
			  FROM autoatendimento.recadastramento_dependente rd
			  JOIN public.participantes p
			    ON rd.cd_registro_empregado = p.cd_registro_empregado 
			   AND rd.seq_dependencia       = p.seq_dependencia 
			   AND rd.cd_empresa            = p.cd_empresa
			  LEFT JOIN clicksign.contrato_digital cd
			    ON cd.cd_contrato_digital = rd.cd_contrato_digital
		     WHERE rd.cd_recadastramento_dependente = ".intval($cd_recadastramento_dependente).";";

		return $this->db->query($qr_sql)->row_array(); 	
	}

	public function get_participante($cd_empresa, $cd_registro_empregado, $seq_dependencia)
	{
		$qr_sql = "
			SELECT p.cd_empresa,
                   p.cd_registro_empregado,
                   p.seq_dependencia,
                   p.endereco,
                   p.nr_endereco,
                   p.complemento_endereco,
                   p.logradouro,
                   p.bairro,
                   p.cidade,
                   p.unidade_federativa,
                   p.cep,
                   p.complemento_cep,
                   p.ddd,
                   p.telefone,
                   p.ramal,
                   p.ddd_celular,
                   p.celular,
                   p.email,
                   p.email_profissional,
                   p.cd_instituicao  || ' - ' || if.razao_social_nome AS banco, 
                   p.cd_agencia, 
                   p.conta_folha,
                   p.nome
              FROM participantes p
              LEFT JOIN public.instituicao_financeiras if 
                ON if.cd_instituicao      = p.cd_instituicao 
               AND if.cd_agencia::integer = 0 
             WHERE cd_empresa            = ".intval($cd_empresa)."
               AND cd_registro_empregado = ".intval($cd_registro_empregado)."
               AND seq_dependencia       = ".intval($seq_dependencia).";";

		return $this->db->query($qr_sql)->row_array(); 	
	}
	
	public function listar_dependente($cd_recadastramento_dependente)
	{
		$qr_sql = "
			SELECT rdc.cd_recadastramento_dependente_cadastro,
				   rdc.cd_recadastramento_dependente,
				   rdc.cd_recadastramento_dependente_grau,
				   rdc.certidao_nascimento,
				   rdc.certidao_casamento,
				   rdc.declaracao_convivencia,
				   rdc.documento_identificacao,
				   rdg.ds_recadastramento_dependente_grau,
				   rdc.ds_nome,
				   TO_CHAR(rdc.dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento,
				   (CASE WHEN UPPER(rdc.fl_invalido) = 'S' THEN 'Sim'
				   		 WHEN UPPER(rdc.fl_invalido) = 'N' THEN 'Não'
				   END) AS ds_invalido,
				   (CASE WHEN UPPER(rdc.fl_sexo) = 'F' THEN 'Feminino'
				   		 WHEN UPPER(rdc.fl_sexo) = 'M' THEN 'Masculino'
				   END) AS ds_sexo
			  FROM autoatendimento.recadastramento_dependente_cadastro rdc
			  JOIN autoatendimento.recadastramento_dependente_grau rdg
			  	ON rdg.cd_recadastramento_dependente_grau = rdc.cd_recadastramento_dependente_grau
			 WHERE rdc.dt_exclusao                   IS NULL 
			   AND rdc.cd_recadastramento_dependente = ".intval($cd_recadastramento_dependente).";";

		return $this->db->query($qr_sql)->result_array(); 	
	}

	public function listar_dependente_participante($cd_recadastramento_dependente)
	{
		$qr_sql = "
			SELECT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS cd_dependente,
                   p.cd_empresa,
                   p.cd_registro_empregado, 
                   p.seq_dependencia,
                   p.nome,
                   TO_CHAR(p.dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento, 
                   (CASE WHEN UPPER(p.sexo) = 'M' THEN 'Masculino' 
                         WHEN UPPER(p.sexo) = 'F' THEN 'Feminino' 
                   END) AS ds_sexo, 
                   d.cd_grau_parentesco,
                   gp.descricao_grau_parentesco,
                   d.id_pensionista,
                   d.seq_pensionista,
                   rdp.fl_opcao,
                   (CASE WHEN UPPER(rdp.fl_opcao) = 'M' THEN 'Manter' 
                         WHEN UPPER(rdp.fl_opcao) = 'E' THEN 'Excluir' 
                   END) AS ds_opcao,
                   rdp.arquivo_dependente
              FROM autoatendimento.recadastramento_dependente_opcao rdp
              JOIN public.participantes p
          	    ON rdp.cd_registro_empregado = p.cd_registro_empregado 
               AND rdp.seq_dependencia       = p.seq_dependencia 
               AND rdp.cd_empresa            = p.cd_empresa 
              JOIN public.dependentes d
                ON d.cd_registro_empregado = p.cd_registro_empregado 
               AND d.seq_dependencia       = p.seq_dependencia 
               AND d.cd_empresa            = p.cd_empresa 
              JOIN public.grau_parentescos gp 
                ON gp.cd_grau_parentesco   = d.cd_grau_parentesco
             WHERE rdp.cd_recadastramento_dependente = ".intval($cd_recadastramento_dependente).";";

        return $this->db->query($qr_sql)->result_array();
	}

	public function cancelar($cd_recadastramento_dependente, $args)
	{
		$qr_sql = "
			UPDATE autoatendimento.recadastramento_dependente 
			   SET dt_cancelamento         = CURRENT_TIMESTAMP,
			       cd_usuario_cancelamento = ".intval($args['cd_usuario']).",
			       ds_justificativa        = ".(trim($args['ds_justificativa']) != '' ? str_escape($args['ds_justificativa']) : "DEFAULT")."
			 WHERE cd_recadastramento_dependente = ".intval($cd_recadastramento_dependente).";

			UPDATE projetos.auto_atendimento_mensagem_publico AS pub
   			   SET dt_exibido = NULL
             WHERE (pub.cd_empresa, pub.cd_registro_empregado, pub.seq_dependencia) 
             	   IN (
            			SELECT p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia 
            			  FROM autoatendimento.recadastramento_dependente p
            			 WHERE p.cd_recadastramento_dependente = ".intval($cd_recadastramento_dependente)."
            	   )
               AND pub.cd_auto_atendimento_mensagem IN (32, 33);";

		$this->db->query($qr_sql);	 
	}

	public function confirmar($cd_recadastramento_dependente, $cd_usuario)
	{
		$qr_sql = "
			SELECT oracle.confirma_dependente_recadastramento(".intval($cd_recadastramento_dependente).");

			UPDATE autoatendimento.recadastramento_dependente 
			   SET dt_confirmacao         = CURRENT_TIMESTAMP,
			   	   cd_usuario_confirmacao = ".intval($cd_usuario)."
			 WHERE cd_recadastramento_dependente = ".intval($cd_recadastramento_dependente).";";

		$this->db->query($qr_sql);	 
	}

	public function confirmar_sem_oracle($cd_recadastramento_dependente, $cd_usuario)
	{
		$qr_sql = "
			UPDATE autoatendimento.recadastramento_dependente 
			   SET dt_confirmacao         = CURRENT_TIMESTAMP,
			   	   cd_usuario_confirmacao = ".intval($cd_usuario)."
			 WHERE cd_recadastramento_dependente = ".intval($cd_recadastramento_dependente).";";

		$this->db->query($qr_sql);	 
	}

	public function confirmar_endereco($cd_recadastramento_dependente, $cd_usuario)
	{
		$qr_sql = "
			UPDATE autoatendimento.recadastramento_dependente 
			   SET dt_confirmacao_endereco         = CURRENT_TIMESTAMP,
			   	   cd_usuario_confirmacao_endereco = ".intval($cd_usuario)."
			 WHERE cd_recadastramento_dependente = ".intval($cd_recadastramento_dependente).";";

		$this->db->query($qr_sql);	 
	}

	public function participante_email($cd_recadastramento_dependente)
	{
		$qr_sql = "
			SELECT rd.cd_recadastramento_dependente,
				   rd.cd_empresa, 
			       rd.cd_registro_empregado,  
			       rd.seq_dependencia,
			       p.email, 
       			   p.email_profissional,
       			   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
			  FROM autoatendimento.recadastramento_dependente rd
			  JOIN public.participantes p
			    ON rd.cd_registro_empregado = p.cd_registro_empregado 
			   AND rd.seq_dependencia       = p.seq_dependencia 
			   AND rd.cd_empresa            = p.cd_empresa
			   AND COALESCE(p.email, COALESCE(p.email_profissional, '')) LIKE '%@%'
		     WHERE rd.cd_recadastramento_dependente = ".intval($cd_recadastramento_dependente).";";

		return $this->db->query($qr_sql)->row_array(); 
	}

	public function get_dependentes_cadastro($cd_recadastramento_dependente)
	{
		$qr_sql = "
			SELECT p.nome AS ds_nome
			  FROM autoatendimento.recadastramento_dependente_opcao rdp
			  JOIN public.participantes p
			    ON rdp.cd_registro_empregado = p.cd_registro_empregado 
			   AND rdp.seq_dependencia       = p.seq_dependencia 
			   AND rdp.cd_empresa            = p.cd_empresa 
			  JOIN public.dependentes d
			    ON d.cd_registro_empregado = p.cd_registro_empregado 
			   AND d.seq_dependencia       = p.seq_dependencia 
			   AND d.cd_empresa            = p.cd_empresa 
			 WHERE fl_opcao                          = 'M'
			   AND rdp.cd_recadastramento_dependente = ".intval($cd_recadastramento_dependente)."

			 UNION 

			SELECT UPPER(funcoes.remove_acento(rdc.ds_nome)) AS ds_nome
			  FROM autoatendimento.recadastramento_dependente_cadastro rdc
			 WHERE rdc.dt_exclusao                   IS NULL 
			   AND rdc.cd_recadastramento_dependente = ".intval($cd_recadastramento_dependente).";";

        return $this->db->query($qr_sql)->result_array();
	}
}
?>