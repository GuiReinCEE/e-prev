<?php
class Investimento_rentabilidade_planos_cd_pga_model extends Model {
	
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_investimento_rentabilidade_planos_cd_pga,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'MM') AS mes_referencia,
				   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_ano_referencia,
				   dt_referencia,
				   fl_media,
				   nr_realizado_ceee_mes,
				   nr_projetado_ceee_mes,
				   nr_bechmark_ceee_mes,
				  
				   nr_realizado_crm_mes,
				   nr_projetado_crm_mes,
				   nr_bechmark_crm_mes,
				  
				   nr_realizado_senge_mes,
				   nr_projetado_senge_mes,
				   nr_bechmark_senge_mes,
				  
				   nr_realizado_sinpro_mes,
				   nr_projetado_sinpro_mes,
				   nr_bechmark_sinpro_mes,
				  
				   nr_realizado_familia_mes,
				   nr_projetado_familia_mes,
				   nr_bechmark_familia_mes,
				  
				   nr_realizado_inpel_mes,
				   nr_projetado_inpel_mes,
				   nr_bechmark_inpel_mes,
				  
				   nr_realizado_pga_mes,
				   nr_projetado_pga_mes,
				   nr_bechmark_pga_mes,
				  
				   nr_realizado_ceee_ano,
				   nr_projetado_ceee_ano,
				   nr_bechmark_ceee_ano,
				  
				   nr_realizado_crm_ano,
				   nr_projetado_crm_ano,
				   nr_bechmark_crm_ano,
				  
				   nr_realizado_senge_ano,
				   nr_projetado_senge_ano,
				   nr_bechmark_senge_ano,
				  
				   nr_realizado_sinpro_ano,
				   nr_projetado_sinpro_ano,
				   nr_bechmark_sinpro_ano,
				  
				   nr_realizado_familia_ano,
				   nr_projetado_familia_ano,
				   nr_bechmark_familia_ano,
				  
				   nr_realizado_inpel_ano,
				   nr_projetado_inpel_ano,
				   nr_bechmark_inpel_ano,
				  
				   nr_realizado_pga_ano,
				   nr_projetado_pga_ano,
				   nr_bechmark_pga_ano,
				   observacao
			  FROM indicador_plugin.investimento_rentabilidade_planos_cd_pga 
			 WHERE dt_exclusao IS NULL
			   AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS dt_referencia_n, 
				   cd_indicador_tabela,
				   nr_projetado_ceee_ano,
				   nr_projetado_crm_ano,
				   nr_projetado_senge_ano,
				   nr_projetado_sinpro_ano,
				   nr_projetado_familia_ano,
				   nr_projetado_inpel_ano,
				   nr_projetado_pga_ano
			  FROM indicador_plugin.investimento_rentabilidade_planos_cd_pga 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_investimento_rentabilidade_planos_cd_pga)
	{
		$qr_sql = "
			SELECT cd_investimento_rentabilidade_planos_cd_pga,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   nr_realizado_ceee_mes,
				   nr_projetado_ceee_ano,
				   nr_bechmark_ceee_mes,
				   nr_realizado_crm_mes,
				   nr_projetado_crm_ano,
				   nr_bechmark_crm_mes,
				   nr_realizado_senge_mes,
				   nr_projetado_senge_ano,
				   nr_bechmark_senge_mes,
				   nr_realizado_sinpro_mes,
				   nr_projetado_sinpro_ano,
				   nr_bechmark_sinpro_mes,
				   nr_realizado_familia_mes,
				   nr_projetado_familia_ano,
				   nr_bechmark_familia_mes,
				   nr_realizado_inpel_mes,
				   nr_projetado_inpel_ano,
				   nr_bechmark_inpel_mes,
				   nr_realizado_pga_mes,
				   nr_projetado_pga_ano,
				   nr_bechmark_pga_mes,
				   observacao
			  FROM indicador_plugin.investimento_rentabilidade_planos_cd_pga 
			 WHERE dt_exclusao IS NULL
			   AND cd_investimento_rentabilidade_planos_cd_pga = ".intval($cd_investimento_rentabilidade_planos_cd_pga).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.investimento_rentabilidade_planos_cd_pga
			     (
                   dt_referencia, 
                   cd_indicador_tabela, 
                   fl_media, 

                   nr_realizado_ceee_mes,
				   nr_projetado_ceee_mes,
				   nr_bechmark_ceee_mes,

				   nr_realizado_crm_mes,
				   nr_projetado_crm_mes,
				   nr_bechmark_crm_mes,

				   nr_realizado_senge_mes,
				   nr_projetado_senge_mes,
				   nr_bechmark_senge_mes,

				   nr_realizado_sinpro_mes,
				   nr_projetado_sinpro_mes,
				   nr_bechmark_sinpro_mes,

				   nr_realizado_familia_mes,
				   nr_projetado_familia_mes,
				   nr_bechmark_familia_mes,

				   nr_realizado_inpel_mes,
				   nr_projetado_inpel_mes,
				   nr_bechmark_inpel_mes,

				   nr_realizado_pga_mes,
				   nr_projetado_pga_mes,
				   nr_bechmark_pga_mes,

				   nr_realizado_ceee_ano,
				   nr_projetado_ceee_ano,
				   nr_bechmark_ceee_ano,

				   nr_realizado_crm_ano,
				   nr_projetado_crm_ano,
				   nr_bechmark_crm_ano,

				   nr_realizado_senge_ano,
				   nr_projetado_senge_ano,
				   nr_bechmark_senge_ano,

				   nr_realizado_sinpro_ano,
				   nr_projetado_sinpro_ano,
				   nr_bechmark_sinpro_ano,

				   nr_realizado_familia_ano,
				   nr_projetado_familia_ano,
				   nr_bechmark_familia_ano,

				   nr_realizado_inpel_ano,
				   nr_projetado_inpel_ano,
				   nr_bechmark_inpel_ano,

				   nr_realizado_pga_ano,
				   nr_projetado_pga_ano,
				   nr_bechmark_pga_ano,

                   observacao,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
            VALUES 
			     (
				   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
				   ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : "DEFAULT").",

				   ".(trim($args['nr_realizado_ceee_mes']) != '' ? floatval($args['nr_realizado_ceee_mes']) : "DEFAULT").",
				   ".(trim($args['nr_projetado_ceee_mes']) != '' ? floatval($args['nr_projetado_ceee_mes']) : "DEFAULT").",
				   ".(trim($args['nr_bechmark_ceee_mes']) != '' ? floatval($args['nr_bechmark_ceee_mes']) : "DEFAULT").",

				   ".(trim($args['nr_realizado_crm_mes']) != '' ? floatval($args['nr_realizado_crm_mes']) : "DEFAULT").",
				   ".(trim($args['nr_projetado_crm_mes']) != '' ? floatval($args['nr_projetado_crm_mes']) : "DEFAULT").",
				   ".(trim($args['nr_bechmark_crm_mes']) != '' ? floatval($args['nr_bechmark_crm_mes']) : "DEFAULT").",

				   ".(trim($args['nr_realizado_senge_mes']) != '' ? floatval($args['nr_realizado_senge_mes']) : "DEFAULT").",
				   ".(trim($args['nr_projetado_senge_mes']) != '' ? floatval($args['nr_projetado_senge_mes']) : "DEFAULT").",
				   ".(trim($args['nr_bechmark_senge_mes']) != '' ? floatval($args['nr_bechmark_senge_mes']) : "DEFAULT").",

				   ".(trim($args['nr_realizado_sinpro_mes']) != '' ? floatval($args['nr_realizado_sinpro_mes']) : "DEFAULT").",
				   ".(trim($args['nr_projetado_sinpro_mes']) != '' ? floatval($args['nr_projetado_sinpro_mes']) : "DEFAULT").",
				   ".(trim($args['nr_bechmark_sinpro_mes']) != '' ? floatval($args['nr_bechmark_sinpro_mes']) : "DEFAULT").",

				   ".(trim($args['nr_realizado_familia_mes']) != '' ? floatval($args['nr_realizado_familia_mes']) : "DEFAULT").",
				   ".(trim($args['nr_projetado_familia_mes']) != '' ? floatval($args['nr_projetado_familia_mes']) : "DEFAULT").",
				   ".(trim($args['nr_bechmark_familia_mes']) != '' ? floatval($args['nr_bechmark_familia_mes']) : "DEFAULT").",

				   ".(trim($args['nr_realizado_inpel_mes']) != '' ? floatval($args['nr_realizado_inpel_mes']) : "DEFAULT").",
				   ".(trim($args['nr_projetado_inpel_mes']) != '' ? floatval($args['nr_projetado_inpel_mes']) : "DEFAULT").",
				   ".(trim($args['nr_bechmark_inpel_mes']) != '' ? floatval($args['nr_bechmark_inpel_mes']) : "DEFAULT").",

				   ".(trim($args['nr_realizado_pga_mes']) != '' ? floatval($args['nr_realizado_pga_mes']) : "DEFAULT").",
				   ".(trim($args['nr_projetado_pga_mes']) != '' ? floatval($args['nr_projetado_pga_mes']) : "DEFAULT").",
				   ".(trim($args['nr_bechmark_pga_mes']) != '' ? floatval($args['nr_bechmark_pga_mes']) : "DEFAULT").",

				   ".(trim($args['nr_realizado_ceee_ano']) != '' ? floatval($args['nr_realizado_ceee_ano']) : "DEFAULT").",
				   ".(trim($args['nr_projetado_ceee_ano']) != '' ? floatval($args['nr_projetado_ceee_ano']) : "DEFAULT").",
				   ".(trim($args['nr_bechmark_ceee_ano']) != '' ? floatval($args['nr_bechmark_ceee_ano']) : "DEFAULT").",

				   ".(trim($args['nr_realizado_crm_ano']) != '' ? floatval($args['nr_realizado_crm_ano']) : "DEFAULT").",
				   ".(trim($args['nr_projetado_crm_ano']) != '' ? floatval($args['nr_projetado_crm_ano']) : "DEFAULT").",
				   ".(trim($args['nr_bechmark_crm_ano']) != '' ? floatval($args['nr_bechmark_crm_ano']) : "DEFAULT").",

				   ".(trim($args['nr_realizado_senge_ano']) != '' ? floatval($args['nr_realizado_senge_ano']) : "DEFAULT").",
				   ".(trim($args['nr_projetado_senge_ano']) != '' ? floatval($args['nr_projetado_senge_ano']) : "DEFAULT").",
				   ".(trim($args['nr_bechmark_senge_ano']) != '' ? floatval($args['nr_bechmark_senge_ano']) : "DEFAULT").",

				   ".(trim($args['nr_realizado_sinpro_ano']) != '' ? floatval($args['nr_realizado_sinpro_ano']) : "DEFAULT").",
				   ".(trim($args['nr_projetado_sinpro_ano']) != '' ? floatval($args['nr_projetado_sinpro_ano']) : "DEFAULT").",
				   ".(trim($args['nr_bechmark_sinpro_ano']) != '' ? floatval($args['nr_bechmark_sinpro_ano']) : "DEFAULT").",

				   ".(trim($args['nr_realizado_familia_ano']) != '' ? floatval($args['nr_realizado_familia_ano']) : "DEFAULT").",
				   ".(trim($args['nr_projetado_familia_ano']) != '' ? floatval($args['nr_projetado_familia_ano']) : "DEFAULT").",
				   ".(trim($args['nr_bechmark_familia_ano']) != '' ? floatval($args['nr_bechmark_familia_ano']) : "DEFAULT").",

				   ".(trim($args['nr_realizado_inpel_ano']) != '' ? floatval($args['nr_realizado_inpel_ano']) : "DEFAULT").",
				   ".(trim($args['nr_projetado_inpel_ano']) != '' ? floatval($args['nr_projetado_inpel_ano']) : "DEFAULT").",
				   ".(trim($args['nr_bechmark_inpel_ano']) != '' ? floatval($args['nr_bechmark_inpel_ano']) : "DEFAULT").",

				   ".(trim($args['nr_realizado_pga_ano']) != '' ? floatval($args['nr_realizado_pga_ano']) : "DEFAULT").",
				   ".(trim($args['nr_projetado_pga_ano']) != '' ? floatval($args['nr_projetado_pga_ano']) : "DEFAULT").",
				   ".(trim($args['nr_bechmark_pga_ano']) != '' ? floatval($args['nr_bechmark_pga_ano']) : "DEFAULT").",
				  
				   ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";

		$this->db->query($qr_sql);	
	}

	public function atualizar($cd_investimento_rentabilidade_planos_cd_pga, $args)
	{
		$qr_sql = "
			UPDATE indicador_plugin.investimento_rentabilidade_planos_cd_pga
			   SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
			       fl_media             = ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : "DEFAULT").",
			       cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
			       
			       nr_realizado_ceee_mes = ".(trim($args['nr_realizado_ceee_mes']) != '' ? floatval($args['nr_realizado_ceee_mes']) : "DEFAULT").",
				   nr_projetado_ceee_mes = ".(trim($args['nr_projetado_ceee_mes']) != '' ? floatval($args['nr_projetado_ceee_mes']) : "DEFAULT").",
				   nr_bechmark_ceee_mes  = ".(trim($args['nr_bechmark_ceee_mes']) != '' ? floatval($args['nr_bechmark_ceee_mes']) : "DEFAULT").",

				   nr_realizado_crm_mes = ".(trim($args['nr_realizado_crm_mes']) != '' ? floatval($args['nr_realizado_crm_mes']) : "DEFAULT").",
				   nr_projetado_crm_mes = ".(trim($args['nr_projetado_crm_mes']) != '' ? floatval($args['nr_projetado_crm_mes']) : "DEFAULT").",
				   nr_bechmark_crm_mes  = ".(trim($args['nr_bechmark_crm_mes']) != '' ? floatval($args['nr_bechmark_crm_mes']) : "DEFAULT").",

				   nr_realizado_senge_mes = ".(trim($args['nr_realizado_senge_mes']) != '' ? floatval($args['nr_realizado_senge_mes']) : "DEFAULT").",
				   nr_projetado_senge_mes =	".(trim($args['nr_projetado_senge_mes']) != '' ? floatval($args['nr_projetado_senge_mes']) : "DEFAULT").",
				   nr_bechmark_senge_mes  =	".(trim($args['nr_bechmark_senge_mes']) != '' ? floatval($args['nr_bechmark_senge_mes']) : "DEFAULT").",

				   nr_realizado_sinpro_mes = ".(trim($args['nr_realizado_sinpro_mes']) != '' ? floatval($args['nr_realizado_sinpro_mes']) : "DEFAULT").",
				   nr_projetado_sinpro_mes = ".(trim($args['nr_projetado_sinpro_mes']) != '' ? floatval($args['nr_projetado_sinpro_mes']) : "DEFAULT").",
				   nr_bechmark_sinpro_mes  = ".(trim($args['nr_bechmark_sinpro_mes']) != '' ? floatval($args['nr_bechmark_sinpro_mes']) : "DEFAULT").",

				   nr_realizado_familia_mes = ".(trim($args['nr_realizado_familia_mes']) != '' ? floatval($args['nr_realizado_familia_mes']) : "DEFAULT").",
				   nr_projetado_familia_mes = ".(trim($args['nr_projetado_familia_mes']) != '' ? floatval($args['nr_projetado_familia_mes']) : "DEFAULT").",
				   nr_bechmark_familia_mes  = ".(trim($args['nr_bechmark_familia_mes']) != '' ? floatval($args['nr_bechmark_familia_mes']) : "DEFAULT").",

				   nr_realizado_inpel_mes = ".(trim($args['nr_realizado_inpel_mes']) != '' ? floatval($args['nr_realizado_inpel_mes']) : "DEFAULT").",
				   nr_projetado_inpel_mes =	".(trim($args['nr_projetado_inpel_mes']) != '' ? floatval($args['nr_projetado_inpel_mes']) : "DEFAULT").",
				   nr_bechmark_inpel_mes  =	".(trim($args['nr_bechmark_inpel_mes']) != '' ? floatval($args['nr_bechmark_inpel_mes']) : "DEFAULT").",

				   nr_realizado_pga_mes = ".(trim($args['nr_realizado_pga_mes']) != '' ? floatval($args['nr_realizado_pga_mes']) : "DEFAULT").",
				   nr_projetado_pga_mes = ".(trim($args['nr_projetado_pga_mes']) != '' ? floatval($args['nr_projetado_pga_mes']) : "DEFAULT").",
				   nr_bechmark_pga_mes  = ".(trim($args['nr_bechmark_pga_mes']) != '' ? floatval($args['nr_bechmark_pga_mes']) : "DEFAULT").",

				   nr_realizado_ceee_ano = ".(trim($args['nr_realizado_ceee_ano']) != '' ? floatval($args['nr_realizado_ceee_ano']) : "DEFAULT").",
				   nr_projetado_ceee_ano = ".(trim($args['nr_projetado_ceee_ano']) != '' ? floatval($args['nr_projetado_ceee_ano']) : "DEFAULT").",
				   nr_bechmark_ceee_ano  = ".(trim($args['nr_bechmark_ceee_ano']) != '' ? floatval($args['nr_bechmark_ceee_ano']) : "DEFAULT").",

				   nr_realizado_crm_ano = ".(trim($args['nr_realizado_crm_ano']) != '' ? floatval($args['nr_realizado_crm_ano']) : "DEFAULT").",
				   nr_projetado_crm_ano = ".(trim($args['nr_projetado_crm_ano']) != '' ? floatval($args['nr_projetado_crm_ano']) : "DEFAULT").",
				   nr_bechmark_crm_ano  = ".(trim($args['nr_bechmark_crm_ano']) != '' ? floatval($args['nr_bechmark_crm_ano']) : "DEFAULT").",

				   nr_realizado_senge_ano = ".(trim($args['nr_realizado_senge_ano']) != '' ? floatval($args['nr_realizado_senge_ano']) : "DEFAULT").",
				   nr_projetado_senge_ano =	".(trim($args['nr_projetado_senge_ano']) != '' ? floatval($args['nr_projetado_senge_ano']) : "DEFAULT").",
				   nr_bechmark_senge_ano  =	".(trim($args['nr_bechmark_senge_ano']) != '' ? floatval($args['nr_bechmark_senge_ano']) : "DEFAULT").",

				   nr_realizado_sinpro_ano = ".(trim($args['nr_realizado_sinpro_ano']) != '' ? floatval($args['nr_realizado_sinpro_ano']) : "DEFAULT").",
				   nr_projetado_sinpro_ano = ".(trim($args['nr_projetado_sinpro_ano']) != '' ? floatval($args['nr_projetado_sinpro_ano']) : "DEFAULT").",
				   nr_bechmark_sinpro_ano  = ".(trim($args['nr_bechmark_sinpro_ano']) != '' ? floatval($args['nr_bechmark_sinpro_ano']) : "DEFAULT").",

				   nr_realizado_familia_ano = ".(trim($args['nr_realizado_familia_ano']) != '' ? floatval($args['nr_realizado_familia_ano']) : "DEFAULT").",
				   nr_projetado_familia_ano = ".(trim($args['nr_projetado_familia_ano']) != '' ? floatval($args['nr_projetado_familia_ano']) : "DEFAULT").",
				   nr_bechmark_familia_ano  = ".(trim($args['nr_bechmark_familia_ano']) != '' ? floatval($args['nr_bechmark_familia_ano']) : "DEFAULT").",

				   nr_realizado_inpel_ano = ".(trim($args['nr_realizado_inpel_ano']) != '' ? floatval($args['nr_realizado_inpel_ano']) : "DEFAULT").",
				   nr_projetado_inpel_ano =	".(trim($args['nr_projetado_inpel_ano']) != '' ? floatval($args['nr_projetado_inpel_ano']) : "DEFAULT").",
				   nr_bechmark_inpel_ano  =	".(trim($args['nr_bechmark_inpel_ano']) != '' ? floatval($args['nr_bechmark_inpel_ano']) : "DEFAULT").",

				   nr_realizado_pga_ano = ".(trim($args['nr_realizado_pga_ano']) != '' ? floatval($args['nr_realizado_pga_ano']) : "DEFAULT").",
				   nr_projetado_pga_ano = ".(trim($args['nr_projetado_pga_ano']) != '' ? floatval($args['nr_projetado_pga_ano']) : "DEFAULT").",
				   nr_bechmark_pga_ano  = ".(trim($args['nr_bechmark_pga_ano']) != '' ? floatval($args['nr_bechmark_pga_ano']) : "DEFAULT").",

				   observacao           = ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_investimento_rentabilidade_planos_cd_pga = ".intval($cd_investimento_rentabilidade_planos_cd_pga).";";

		$this->db->query($qr_sql);	
	}

	public function excluir($cd_investimento_rentabilidade_planos_cd_pga, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.investimento_rentabilidade_planos_cd_pga 
			   SET dt_exclusao          = CURRENT_TIMESTAMP, 
				   cd_usuario_exclusao  = ".intval($cd_usuario)."
			 WHERE cd_investimento_rentabilidade_planos_cd_pga = ".intval($cd_investimento_rentabilidade_planos_cd_pga).";"; 

		$this->db->query($qr_sql);
	}

	public function fechar_indicador($cd_indicador_tabela, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			       cd_usuario_fechamento_periodo = ".intval($cd_usuario)."
			 WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela).";";
			 
		$this->db->query($qr_sql);
	}
}