<?php
class calculo_irrf_model extends Model
{   
    function __construct()
    {
        parent::Model();
    }
	
	public function tipo_aplicacao(&$result, $args=array())
    {
        $qr_sql = "
			SELECT cd_calculo_irrf_tipo_aplicacao AS value,
			       ds_calculo_irrf_tipo_aplicacao AS text
			  FROM escritorio_juridico.calculo_irrf_tipo_aplicacao
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_calculo_irrf_tipo_aplicacao;";

        $result = $this->db->query($qr_sql);
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
			SELECT nr_processo_ano || '/' || nr_processo AS value,
			       nr_processo_ano || '/' || nr_processo AS text
			  FROM escritorio_juridico.calculo_irrf
			 WHERE dt_exclusao IS NULL;";
		
		$result = $this->db->query($qr_sql);
	}
	
	public function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ci.cd_calculo_irrf,
			       escritorio_juridico.nr_calculo_irrf(ci.nr_ano, ci.nr_numero) AS nr_ano_numero,
			       ci.cpf,
				   ci.cd_empresa,
				   ci.cd_registro_empregado,
				   ci.seq_dependencia,
				   ci.nome,
				   ci.nr_processo_ano || '/' || ci.nr_processo AS nr_processo,
				   TO_CHAR(ci.dt_envio,'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   u.nome AS solicitado,
				   CASE WHEN ci.dt_envio IS NOT NULL AND ci.dt_confirma IS NULL THEN 'Aguardando Benefício'
					    ELSE 'Confirmado'
				   END AS status,
				   CASE WHEN ci.dt_envio IS NOT NULL AND ci.dt_confirma IS NULL THEN  'green'
					    ELSE 'blue'
				   END AS cor
			  FROM escritorio_juridico.calculo_irrf ci
			  LEFT JOIN escritorio_juridico.usuario u
			    ON u.cd_usuario = ci.cd_usuario_envio
			 WHERE ci.dt_exclusao IS NULL
			   AND ci.dt_envio IS NOT NULL
			   ".(trim($args['fl_status']) == 'GB' ? "AND ci.dt_envio IS NOT NULL AND ci.dt_confirma IS NULL" : "")."
			   ".(trim($args['fl_status']) == 'C' ? "AND ci.dt_confirma IS NOT NULL" : "")."
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
	
	public function tipo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_calculo_irrf_tipo AS value,
			       ds_calculo_irrf_tipo AS text
			  FROM escritorio_juridico.calculo_irrf_tipo
			 WHERE dt_exclusao IS NULL;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ci.cd_calculo_irrf,
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
				   TO_CHAR(ci.dt_envio,'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   ci.vl_bruto_tributavel,
				   ci.vl_isento_tributacao,
				   ci.vl_contribuicao,
				   ci.vl_custeio_administrativo,
				   ci.vl_desconto_pensao_alimenticia,
				   u.nome AS solicitado,
				   dt_confirma,
				   p.nome AS nome_participante,
				   (SELECT COUNT(cia.*) 
				      FROM escritorio_juridico.calculo_irrf_anexo cia
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = cia.cd_usuario_inclusao_fceee
					 WHERE dt_exclusao IS NULL
					   AND cia.cd_calculo_irrf = ci.cd_calculo_irrf
					   AND uc.divisao = 'GP') AS tl_anexo_gb,
				   ci.cd_calculo_irrf_tipo_aplicacao,
				   TO_CHAR(ci.dt_fato_gerador, 'DD/MM/YYYY') AS dt_fato_gerador,
				   e.cd_escritorio_oracle
			  FROM escritorio_juridico.calculo_irrf ci
			  JOIN escritorio_juridico.escritorio e
			    ON e.cd_escritorio = ci.cd_escritorio
			  LEFT JOIN escritorio_juridico.usuario u
			    ON u.cd_usuario = ci.cd_usuario_envio 
			  LEFT JOIN public.participantes p
			    ON p.cd_empresa            = ci.cd_empresa
			   AND p.cd_registro_empregado = ci.cd_registro_empregado
			   AND p.seq_dependencia       = ci.seq_dependencia
			 WHERE ci.cd_calculo_irrf = ".intval($args['cd_calculo_irrf']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	public function processos_cpf(&$result, $args=array())
	{
		$qr_sql = "
			SELECT pj.ano || '/' || pj.nro_processo AS value,
			       pj.ano || '/' || pj.nro_processo AS text
              FROM public.processos_juridicos pj
              JOIN public.participantes p
                ON p.cd_empresa            = pj.part_patr_cd_empresa
               AND p.cd_registro_empregado = pj.part_cd_registro_empregado
               AND p.seq_dependencia       = pj.part_seq_dependencia
             WHERE funcoes.format_cpf(p.cpf_mf::bigint) = '".trim($args['cpf'])."';";

		$result = $this->db->query($qr_sql);
	}
	
	public function salvar(&$result, $args=array())
	{
		if(intval($args['cd_calculo_irrf']) > 0)
		{
			$qr_sql = "
				UPDATE escritorio_juridico.calculo_irrf
				   SET cpf                            = ".(trim($args['cpf']) != '' ? "'".trim($args['cpf'])."'" : "DEFAULT").",
					   nome                           = ".(trim($args['nome']) != '' ? "'".trim($args['nome'])."'" : "DEFAULT").",
					   nr_processo                    = ".(trim($args['nr_processo']) != '' ? ($args['nr_processo']) : "DEFAULT").",
					   nr_processo_ano                = ".(trim($args['nr_processo_ano']) != '' ? intval($args['nr_processo_ano']) : "DEFAULT").",
                       cd_calculo_irrf_correspondente = ".(trim($args['cd_calculo_irrf_correspondente']) != '' ? intval($args['cd_calculo_irrf_correspondente']) : "DEFAULT").",
					   cd_calculo_irrf_tipo           = ".(trim($args['cd_calculo_irrf_tipo']) != '' ? intval($args['cd_calculo_irrf_tipo']) : "DEFAULT").",
					   dt_pagamento                   = ".(trim($args['dt_pagamento']) != '' ? "TO_DATE('".$args['dt_pagamento']."', 'DD/MM/YYYY')" : "DEFAULT").",
                       vl_bruto_tributavel            = ".(trim($args['vl_bruto_tributavel']) != '' ? floatval($args['vl_bruto_tributavel']) : "DEFAULT").", 
					   vl_isento_tributacao           = ".(trim($args['vl_isento_tributacao']) != '' ? floatval($args['vl_isento_tributacao']) : "DEFAULT").",
					   vl_contribuicao                = ".(trim($args['vl_contribuicao']) != '' ? floatval($args['vl_contribuicao']) : "DEFAULT").",
                       vl_custeio_administrativo      = ".(trim($args['vl_custeio_administrativo']) != '' ? floatval($args['vl_custeio_administrativo']) : "DEFAULT").",
					   vl_desconto_pensao_alimenticia = ".(trim($args['vl_desconto_pensao_alimenticia']) != '' ? floatval($args['vl_desconto_pensao_alimenticia']) : "DEFAULT").", 
                       dt_alteracao                   = CURRENT_TIMESTAMP, 
					   cd_usuario_alteracao           = ".intval($args['cd_usuario'])."
                 WHERE cd_calculo_irrf = ".intval($args['cd_calculo_irrf']).";";
		}
		
		$this->db->query($qr_sql);
	}
	
	public function liberar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE escritorio_juridico.calculo_irrf
			   SET cd_usuario_confirma = ".intval($args['cd_usuario']).",
			       dt_confirma         = CURRENT_TIMESTAMP
		     WHERE cd_calculo_irrf = ".intval($args['cd_calculo_irrf']).";";
			 
		$this->db->query($qr_sql);
	}
	
	public function salvar_re(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE escritorio_juridico.calculo_irrf
			   SET cd_empresa                 = ".intval($args['cd_empresa']).",
			       cd_registro_empregado      = ".intval($args['cd_registro_empregado']).",
				   seq_dependencia            = ".intval($args['seq_dependencia']).",
			       cd_usuario_alteracao_fceee = ".intval($args['cd_usuario']).",
			       dt_alteracao_fceee         = CURRENT_TIMESTAMP
		     WHERE cd_calculo_irrf = ".intval($args['cd_calculo_irrf']).";";
			 
		$this->db->query($qr_sql);
	}
	
	public function salvar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO escritorio_juridico.calculo_irrf_anexo
			     (
					cd_calculo_irrf,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao_fceee,
					cd_usuario_alteracao_fceee
				 )
			VALUES
			     (
					".intval($args['cd_calculo_irrf']).",
					'".trim($args['arquivo'])."',
					'".trim($args['arquivo_nome'])."',
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				 )";
				 
		$this->db->query($qr_sql);
	}
	
	public function listar_anexo(&$result, $args=array())
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
			 WHERE cia.dt_exclusao IS NULL
			   AND cia.cd_calculo_irrf = ".intval($args['cd_calculo_irrf']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	public function excluir_anexo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE escritorio_juridico.calculo_irrf_anexo
			   SET dt_exclusao               = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao_fceee = ".intval($args['cd_usuario'])."
			 WHERE cd_calculo_irrf_anexo = ".intval($args['cd_calculo_irrf_anexo']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	public function salvar_rejeitar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE escritorio_juridico.calculo_irrf
			   SET dt_envio                   = NULL,
			       cd_usuario_envio           = NULL,
				   dt_confirma                = NULL,
				   cd_usuario_confirma        = NULL,
			       cd_usuario_alteracao_fceee = ".intval($args['cd_usuario']).",
				   dt_alteracao_fceee         = CURRENT_TIMESTAMP
			 WHERE cd_calculo_irrf = ".intval($args['cd_calculo_irrf']).";
			 
			INSERT INTO escritorio_juridico.calculo_irrf_rejeitado
			     (
				   cd_calculo_irrf,
				   ds_calculo_irrf_rejeitado,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
			VALUES
			     (
				   ".intval($args['cd_calculo_irrf']).",
				   ".str_escape($args['ds_calculo_irrf_rejeitado']).",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";
			 
		$result = $this->db->query($qr_sql);
	}
	
	public function listar_beneficiario(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cpf
			  FROM escritorio_juridico.calculo_irrf_beneficiario
			 WHERE dt_exclusao IS NULL
			   AND cd_calculo_irrf = ".intval($args["cd_calculo_irrf"]).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	public function listar_participante_re(&$result, $args=array())
	{
		$qr_sql = "
			SELECT p.cd_empresa,
				   p.cd_registro_empregado,
				   p.seq_dependencia,
				   p.nome
			  FROM public.processos_juridicos pj
			  JOIN public.participantes p
				ON p.cd_empresa            = pj.part_patr_cd_empresa
			   AND p.cd_registro_empregado = pj.part_cd_registro_empregado
			   AND p.seq_dependencia       = pj.part_seq_dependencia
			 WHERE funcoes.format_cpf(p.cpf_mf::bigint) = '".trim($args['cpf'])."'
			   AND pj.dt_fim_processo   IS NULL
			   AND pj.esc_cd_escritorio = ".intval($args['cd_escritorio_oracle'])."
			   AND pj.ano               = ".intval($args['proc_ano'])."
			   AND pj.nro_processo      = ".trim($args['proc_nro'])."

			 UNION

			SELECT p.cd_empresa,
				   p.cd_registro_empregado,
				   p.seq_dependencia,
				   p.nome  
			  FROM public.plurimas pl
			  JOIN public.processos_juridicos pj
				ON pj.ano               = pl.proc_ano
			   AND pj.nro_processo      = pl.proc_nro_processo
			   AND pj.dt_fim_processo   IS NULL
			   AND pj.esc_cd_escritorio = ".intval($args['cd_escritorio_oracle'])."
			  JOIN public.participantes p
				ON p.cd_empresa            = pl.part_cd_empresa
			   AND p.cd_registro_empregado = pl.part_cd_registro_empregado
			   AND p.seq_dependencia       = pl.part_seq_dependencia
			 WHERE funcoes.format_cpf(p.cpf_mf::bigint) = '".trim($args['cpf'])."'
			   AND pl.proc_ano                          = ".intval($args['proc_ano'])."
			   AND pl.proc_nro_processo                 = ".trim($args['proc_nro'])."

			 ORDER BY nome;";
			 
		$result = $this->db->query($qr_sql);
	}
}
?>