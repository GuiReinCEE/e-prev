<?php
class pagamento_cheque_model extends Model
{   
    function __construct()
    {
        parent::Model();
    }	
	
	public function correspondentes(&$result, $args=array())
    {
        $qr_sql = "
			SELECT cd_calculo_irrf_correspondente AS value,
			       ds_calculo_irrf_correspondente AS text
			  FROM escritorio_juridico.calculo_irrf_correspondente
			 WHERE dt_exclusao IS NULL;";

        $result = $this->db->query($qr_sql);
    }
	
	public function processos(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ci.nr_processo_ano || '/' || ci.nr_processo AS value,
			       ci.nr_processo_ano || '/' || ci.nr_processo AS text
			  FROM escritorio_juridico.pagamento_cheque pc
			  JOIN escritorio_juridico.calculo_irrf ci
			    ON ci.cd_calculo_irrf = pc.cd_calculo_irrf
			 WHERE ci.dt_exclusao IS NULL
			   AND pc.dt_exclusao IS NULL;";
		
		$result = $this->db->query($qr_sql);
	}
	
	public function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ci.cd_calculo_irrf,
			       pc.cd_pagamento_cheque,
			       escritorio_juridico.nr_calculo_irrf(ci.nr_ano, ci.nr_numero) AS nr_ano_numero,
			       ci.cpf,
				   ci.cd_empresa,
				   ci.cd_registro_empregado,
				   ci.seq_dependencia,
				   ci.nome,
				   ci.nr_processo_ano || '/' || ci.nr_processo AS nr_processo,
				   TO_CHAR(pc.dt_envio,'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   TO_CHAR(pc.dt_deposito,'DD/MM/YYYY') AS dt_deposito,
				   pc.vl_custo,
				   u.nome AS solicitado,
				   CASE WHEN pc.dt_envio IS NOT NULL AND pc.dt_confirma IS NULL THEN 'Aguardando Benefício'
					    ELSE 'Confirmado'
				   END AS status,
				   CASE WHEN pc.dt_envio IS NOT NULL AND pc.dt_confirma IS NULL THEN  'green'
					    ELSE 'blue'
				   END AS cor
			  FROM escritorio_juridico.pagamento_cheque pc
			  JOIN escritorio_juridico.calculo_irrf ci
			    ON ci.cd_calculo_irrf = pc.cd_calculo_irrf
			  LEFT JOIN escritorio_juridico.usuario u
			    ON u.cd_usuario = pc.cd_usuario_envio
			 WHERE ci.dt_exclusao IS NULL
			   AND pc.dt_exclusao IS NULL
			   AND pc.dt_envio IS NOT NULL
			   ".(trim($args['fl_status']) == 'GB' ? "AND pc.dt_envio IS NOT NULL AND pc.dt_confirma IS NULL" : "")."
			   ".(trim($args['fl_status']) == 'C' ? "AND pc.dt_confirma IS NOT NULL" : "")."
			   ".(trim($args['nr_ano']) != '' ? "AND ci.nr_ano = ".intval($args['nr_ano']) : "")."
			   ".(trim($args['cd_empresa']) != '' ? "AND ci.cd_empresa = ".intval($args['cd_empresa']) : "")."
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND ci.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
			   ".(trim($args['seq_dependencia']) != '' ? "AND ci.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
			   ".(trim($args['nr_numero']) != '' ? "AND ci.nr_numero = ".intval($args['nr_numero']) : "")."
			   ".(trim($args['nome']) != '' ? "AND UPPER(ci.nome) LIKE UPPER('%".str_like($args['nome'])."%')" : "")."
			   ".(trim($args['nr_processo_ano']) != '' ? "AND ci.nr_processo_ano = ".intval($args['nr_processo_ano']) : "")."
			   ".(trim($args['nr_processo']) != '' ? "AND ci.nr_processo = ".$args['nr_processo'] : "")."
			   ".(trim($args['cd_calculo_irrf_correspondente']) != '' ? "AND ci.cd_calculo_irrf_correspondente = ".intval($args['cd_calculo_irrf_correspondente']) : "")."
			   ".(((trim($args['dt_pagamento_ini']) != "") AND  (trim($args['dt_pagamento_fim']) != "")) ? " AND DATE_TRUNC('day', ci.dt_pagamento) BETWEEN TO_DATE('".$args['dt_pagamento_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_pagamento_fim']."', 'DD/MM/YYYY')" : "").";";

		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ci.cd_calculo_irrf,
			       pc.cd_pagamento_cheque,
			       escritorio_juridico.nr_calculo_irrf(ci.nr_ano, ci.nr_numero) AS nr_ano_numero,
				   ci.cpf,
				   ci.nome,
				   ci.nr_processo_ano || '/' || ci.nr_processo AS ano_nr_processo,
				   ci.cd_calculo_irrf_correspondente,
				   ci.cd_calculo_irrf_tipo,
				   ci.cd_empresa,
				   ci.cd_registro_empregado,
				   ci.seq_dependencia,
				   TO_CHAR(ci.dt_pagamento,'DD/MM/YYYY HH24:MI:SS') AS dt_pagamento,
				   TO_CHAR(pc.dt_envio,'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   ci.vl_bruto_tributavel,
				   ci.vl_isento_tributacao,
				   ci.vl_contribuicao,
				   ci.vl_custeio_administrativo,
				   ci.vl_desconto_pensao_alimenticia,
				   u.nome AS solicitado,
				   pc.dt_confirma,
				   cic.ds_calculo_irrf_correspondente,
				   cit.ds_calculo_irrf_tipo,
				   TO_CHAR(pc.dt_deposito,'DD/MM/YYYY') AS dt_deposito,
				   pc.vl_custo,
				   (SELECT COUNT(pca.*) 
				      FROM escritorio_juridico.pagamento_cheque_anexo pca
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = pca.cd_usuario_inclusao_fceee
					 WHERE dt_exclusao IS NULL
					   AND pca.cd_pagamento_cheque = pc.cd_pagamento_cheque
					   AND uc.divisao = 'GB') AS tl_anexo_gb,
					p.nome AS nome_participante
			  FROM escritorio_juridico.pagamento_cheque pc
			  JOIN escritorio_juridico.calculo_irrf ci
			    ON ci.cd_calculo_irrf = pc.cd_calculo_irrf
			  LEFT JOIN public.participantes p
			    ON p.cd_registro_empregado = ci.cd_registro_empregado
			   AND p.cd_empresa            = ci.cd_empresa
			   AND p.seq_dependencia       = ci.seq_dependencia
			  LEFT JOIN escritorio_juridico.usuario u
			    ON u.cd_usuario = ci.cd_usuario_envio 
			  LEFT JOIN escritorio_juridico.calculo_irrf_correspondente cic
			    ON cic.cd_calculo_irrf_correspondente = ci.cd_calculo_irrf_correspondente
			  LEFT JOIN escritorio_juridico.calculo_irrf_tipo cit
			    ON cit.cd_calculo_irrf_tipo = ci.cd_calculo_irrf_tipo
			 WHERE pc.cd_pagamento_cheque = ".intval($args['cd_pagamento_cheque']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	public function liberar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE escritorio_juridico.pagamento_cheque
			   SET cd_usuario_confirma = ".intval($args['cd_usuario']).",
			       dt_confirma         = CURRENT_TIMESTAMP
		     WHERE cd_pagamento_cheque = ".intval($args['cd_pagamento_cheque']).";";
			 
		$this->db->query($qr_sql);
	}
	
	public function salvar_rejeitar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE escritorio_juridico.pagamento_cheque
			   SET dt_envio                   = NULL,
			       cd_usuario_envio           = NULL,
				   dt_confirma                = NULL,
				   cd_usuario_confirma        = NULL,
			       cd_usuario_alteracao_fceee = ".intval($args['cd_usuario']).",
				   dt_alteracao_fceee         = CURRENT_TIMESTAMP
			 WHERE cd_pagamento_cheque = ".intval($args['cd_pagamento_cheque']).";
			 
			INSERT INTO escritorio_juridico.pagamento_cheque_rejeitado
			     (
				   cd_pagamento_cheque,
				   ds_pagamento_cheque_rejeitado,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
			VALUES
			     (
				   ".intval($args['cd_pagamento_cheque']).",
				   '".trim($args['ds_pagamento_cheque_rejeitado'])."',
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";
			 
		$result = $this->db->query($qr_sql);
	}
	
	public function listar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT pca.cd_pagamento_cheque_anexo,
			       pca.arquivo,
				   pca.arquivo_nome,
				   pc.dt_confirma,
				   TO_CHAR(pca.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM escritorio_juridico.pagamento_cheque_anexo pca
			  JOIN escritorio_juridico.pagamento_cheque pc
			    ON pc.cd_pagamento_cheque = pca.cd_pagamento_cheque
			 WHERE pca.dt_exclusao IS NULL
			   AND pca.cd_pagamento_cheque = ".intval($args['cd_pagamento_cheque']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	public function salvar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO escritorio_juridico.pagamento_cheque_anexo
			     (
					cd_pagamento_cheque,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao_fceee,
					cd_usuario_alteracao_fceee
				 )
			VALUES
			     (
					".intval($args['cd_pagamento_cheque']).",
					'".trim($args['arquivo'])."',
					'".trim($args['arquivo_nome'])."',
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				 )";
				 
		$this->db->query($qr_sql);
	}
	
	public function excluir_anexo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE escritorio_juridico.pagamento_cheque_anexo
			   SET dt_exclusao               = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao_fceee = ".intval($args['cd_usuario'])."
			 WHERE cd_pagamento_cheque_anexo = ".intval($args['cd_pagamento_cheque_anexo']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	public function listar_anexo_calculo_irrf(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cia.cd_calculo_irrf_anexo,
			       cia.arquivo,
				   cia.arquivo_nome,
				   ci.dt_confirma,
				   TO_CHAR(cia.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM escritorio_juridico.calculo_irrf_anexo cia
			  JOIN escritorio_juridico.calculo_irrf ci
			    ON ci.cd_calculo_irrf = cia.cd_calculo_irrf
		      JOIN escritorio_juridico.pagamento_cheque pc
			    ON pc.cd_calculo_irrf = ci.cd_calculo_irrf
			 WHERE cia.dt_exclusao IS NULL
			   AND pc.cd_pagamento_cheque = ".intval($args['cd_pagamento_cheque']).";";
			 
		$result = $this->db->query($qr_sql);
	}
}
?>