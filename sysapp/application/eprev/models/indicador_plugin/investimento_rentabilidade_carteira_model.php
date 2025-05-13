<?php
class Investimento_rentabilidade_carteira_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_investimento_rentabilidade_carteira,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'MM') AS mes_referencia,
				   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_ano_referencia,
				   dt_referencia,
				   fl_media,
				   nr_valor_1,
				   nr_meta,
				   nr_inpc,
				   nr_atuarial_projetado,
				   nr_atuarial,
				   nr_rentabilidade_acum,
				   nr_meta_acum,
				   nr_inpc_acum,
				   nr_poder_referencia,
				   nr_poder_resultado,

				   nr_acumulado_ceee,
				   nr_benchmark_ceee,

				   nr_acumulado_rge,
				   nr_benchmark_rge,

				   nr_acumulado_aes,
				   nr_benchmark_aes,

				   nr_acumulado_cgte,
				   nr_benchmark_cgte,

				   nr_acumulado_ceeeprev,
				   nr_benchmark_ceeeprev,

				   nr_acumulado_senge,
				   nr_benchmark_senge,

				   nr_acumulado_sinpro,
				   nr_benchmark_sinpro,

				   nr_acumulado_fam_corp,
				   nr_benchmark_fam_corp,

				   nr_acumulado_fam_assoc,
				   nr_benchmark_fam_assoc,

				   nr_acumulado_ceranprev,
				   nr_benchmark_ceranprev,

				   nr_acumulado_fozprev,
				   nr_benchmark_fozprev,

				   nr_acumulado_crmprev,
				   nr_benchmark_cremprev,

				   nr_acumulado_pga,
				   nr_benchmark_pga,

				   nr_acumulado_municipio,
				   nr_benchmark_municipio,

				   nr_acumulado_ieab,
				   nr_benchmark_ieab,

				   ds_tabela,

				   observacao
			  FROM indicador_plugin.investimento_rentabilidade_carteira 
			 WHERE dt_exclusao IS NULL
			   AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($cd_indicador_tabela, $nr_ano_referencia)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS dt_referencia_n, 
				   nr_meta, 
				   cd_indicador_tabela,
				   nr_atuarial_projetado,
				   (SELECT COUNT(*)
				      FROM indicador_plugin.investimento_rentabilidade_carteira
				     WHERE TO_CHAR(dt_referencia, 'YYYY')::integer = ".intval($nr_ano_referencia)."
				       AND dt_exclusao IS NULL) AS qt_ano
			  FROM indicador_plugin.investimento_rentabilidade_carteira 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function carrega($cd_investimento_rentabilidade_carteira)
	{
		$qr_sql = "
			SELECT cd_investimento_rentabilidade_carteira,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   nr_valor_1,
				   nr_meta,
				   nr_inpc,
				   nr_atuarial_projetado,

				   nr_acumulado_ceee,
				   nr_benchmark_ceee,

				   nr_acumulado_rge,
				   nr_benchmark_rge,

				   nr_acumulado_aes,
				   nr_benchmark_aes,

				   nr_acumulado_cgte,
				   nr_benchmark_cgte,

				   nr_acumulado_ceeeprev,
				   nr_benchmark_ceeeprev,

				   nr_acumulado_senge,
				   nr_benchmark_senge,

				   nr_acumulado_sinpro,
				   nr_benchmark_sinpro,

				   nr_acumulado_fam_corp,
				   nr_benchmark_fam_corp,

				   nr_acumulado_fam_assoc,
				   nr_benchmark_fam_assoc,

				   nr_acumulado_ceranprev,
				   nr_benchmark_ceranprev,

				   nr_acumulado_fozprev,
				   nr_benchmark_fozprev,

				   nr_acumulado_crmprev,
				   nr_benchmark_cremprev,

				   nr_acumulado_pga,
				   nr_benchmark_pga,

				   nr_acumulado_municipio,
				   nr_benchmark_municipio,

				   nr_acumulado_ieab,
				   nr_benchmark_ieab,

				   ds_tabela,

				   observacao
			  FROM indicador_plugin.investimento_rentabilidade_carteira 
			 WHERE dt_exclusao IS NULL
			   AND cd_investimento_rentabilidade_carteira = ".intval($cd_investimento_rentabilidade_carteira)."
			 ORDER BY dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->row_array();
	}	

	public function salvar($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.investimento_rentabilidade_carteira
			     (
                   dt_referencia, 
                   cd_indicador_tabela, 
                   fl_media, 
				   nr_valor_1,
				   nr_meta, 
				   nr_inpc,
				   nr_atuarial_projetado,
				   nr_atuarial,
				   nr_rentabilidade_acum,
				   nr_meta_acum,
				   nr_inpc_acum,
				   nr_poder_referencia,
				   nr_poder_resultado,

				   nr_acumulado_ceee,
				   nr_benchmark_ceee,

				   nr_acumulado_rge,
				   nr_benchmark_rge,

				   nr_acumulado_aes,
				   nr_benchmark_aes,

				   nr_acumulado_cgte,
				   nr_benchmark_cgte,

				   nr_acumulado_ceeeprev,
				   nr_benchmark_ceeeprev,

				   nr_acumulado_senge,
				   nr_benchmark_senge,

				   nr_acumulado_sinpro,
				   nr_benchmark_sinpro,

				   nr_acumulado_fam_corp,
				   nr_benchmark_fam_corp,

				   nr_acumulado_fam_assoc,
				   nr_benchmark_fam_assoc,

				   nr_acumulado_ceranprev,
				   nr_benchmark_ceranprev,

				   nr_acumulado_fozprev,
				   nr_benchmark_fozprev,

				   nr_acumulado_crmprev,
				   nr_benchmark_cremprev,

				   nr_acumulado_pga,
				   nr_benchmark_pga,

				   nr_acumulado_municipio,
				   nr_benchmark_municipio,

				   nr_acumulado_ieab,
				   nr_benchmark_ieab,

				   ds_tabela,

                   observacao,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
            VALUES 
			     (
				   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
				   ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : "DEFAULT").",
				   ".(trim($args['nr_valor_1']) != '' ? floatval($args['nr_valor_1']) : "DEFAULT").",
				   ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   ".(trim($args['nr_inpc']) != '' ? floatval($args['nr_inpc']) : "DEFAULT").",
				   ".(trim($args['nr_atuarial_projetado']) != '' ? floatval($args['nr_atuarial_projetado']) : "DEFAULT").",
				   ".(trim($args['nr_atuarial']) != '' ? floatval($args['nr_atuarial']) : "DEFAULT").",
				   ".(trim($args['nr_rentabilidade_acum']) != '' ? floatval($args['nr_rentabilidade_acum']) : "DEFAULT").",
				   ".(trim($args['nr_meta_acum']) != '' ? floatval($args['nr_meta_acum']) : "DEFAULT").",
				   ".(trim($args['nr_inpc_acum']) != '' ? floatval($args['nr_inpc_acum']) : "DEFAULT").",
				   ".(trim($args['nr_poder_referencia']) != '' ? floatval($args['nr_poder_referencia']) : "DEFAULT").",
				   ".(trim($args['nr_poder_resultado']) != '' ? floatval($args['nr_poder_resultado']) : "DEFAULT").",

				   ".(trim($args['nr_acumulado_ceee']) != '' ? floatval($args['nr_acumulado_ceee']) : "DEFAULT").",
				   ".(trim($args['nr_benchmark_ceee']) != '' ? floatval($args['nr_benchmark_ceee']) : "DEFAULT").",

				   ".(trim($args['nr_acumulado_rge']) != '' ? floatval($args['nr_acumulado_rge']) : "DEFAULT").",
				   ".(trim($args['nr_benchmark_rge']) != '' ? floatval($args['nr_benchmark_rge']) : "DEFAULT").",

				   ".(trim($args['nr_acumulado_aes']) != '' ? floatval($args['nr_acumulado_aes']) : "DEFAULT").",
				   ".(trim($args['nr_benchmark_aes']) != '' ? floatval($args['nr_benchmark_aes']) : "DEFAULT").",

				   ".(trim($args['nr_acumulado_cgte']) != '' ? floatval($args['nr_acumulado_cgte']) : "DEFAULT").",
				   ".(trim($args['nr_benchmark_cgte']) != '' ? floatval($args['nr_benchmark_cgte']) : "DEFAULT").",

				   ".(trim($args['nr_acumulado_ceeeprev']) != '' ? floatval($args['nr_acumulado_ceeeprev']) : "DEFAULT").",
				   ".(trim($args['nr_benchmark_ceeeprev']) != '' ? floatval($args['nr_benchmark_ceeeprev']) : "DEFAULT").",

				   ".(trim($args['nr_acumulado_senge']) != '' ? floatval($args['nr_acumulado_senge']) : "DEFAULT").",
				   ".(trim($args['nr_benchmark_senge']) != '' ? floatval($args['nr_benchmark_senge']) : "DEFAULT").",

				   ".(trim($args['nr_acumulado_sinpro']) != '' ? floatval($args['nr_acumulado_sinpro']) : "DEFAULT").",
				   ".(trim($args['nr_benchmark_sinpro']) != '' ? floatval($args['nr_benchmark_sinpro']) : "DEFAULT").",

				   ".(trim($args['nr_acumulado_fam_corp']) != '' ? floatval($args['nr_acumulado_fam_corp']) : "DEFAULT").",
				   ".(trim($args['nr_benchmark_fam_corp']) != '' ? floatval($args['nr_benchmark_fam_corp']) : "DEFAULT").",

				   ".(trim($args['nr_acumulado_fam_assoc']) != '' ? floatval($args['nr_acumulado_fam_assoc']) : "DEFAULT").",
				   ".(trim($args['nr_benchmark_fam_assoc']) != '' ? floatval($args['nr_benchmark_fam_assoc']) : "DEFAULT").",

				   ".(trim($args['nr_acumulado_ceranprev']) != '' ? floatval($args['nr_acumulado_ceranprev']) : "DEFAULT").",
				   ".(trim($args['nr_benchmark_ceranprev']) != '' ? floatval($args['nr_benchmark_ceranprev']) : "DEFAULT").",

				   ".(trim($args['nr_acumulado_fozprev']) != '' ? floatval($args['nr_acumulado_fozprev']) : "DEFAULT").",
				   ".(trim($args['nr_benchmark_fozprev']) != '' ? floatval($args['nr_benchmark_fozprev']) : "DEFAULT").",

				   ".(trim($args['nr_acumulado_crmprev']) != '' ? floatval($args['nr_acumulado_crmprev']) : "DEFAULT").",
				   ".(trim($args['nr_benchmark_cremprev']) != '' ? floatval($args['nr_benchmark_cremprev']) : "DEFAULT").",

				   ".(trim($args['nr_acumulado_pga']) != '' ? floatval($args['nr_acumulado_pga']) : "DEFAULT").",
				   ".(trim($args['nr_benchmark_pga']) != '' ? floatval($args['nr_benchmark_pga']) : "DEFAULT").",

				   ".(trim($args['nr_acumulado_municipio']) != '' ? floatval($args['nr_acumulado_municipio']) : "DEFAULT").",
				   ".(trim($args['nr_benchmark_municipio']) != '' ? floatval($args['nr_benchmark_municipio']) : "DEFAULT").",

				   ".(trim($args['nr_acumulado_ieab']) != '' ? floatval($args['nr_acumulado_ieab']) : "DEFAULT").",
				   ".(trim($args['nr_benchmark_ieab']) != '' ? floatval($args['nr_benchmark_ieab']) : "DEFAULT").",

				   ".(trim($args['ds_tabela']) != '' ? str_escape($args['ds_tabela']) : "DEFAULT").",

				   ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";


		$this->db->query($qr_sql);	
	}

	public function atualizar($cd_investimento_rentabilidade_carteira, $args)
	{
		$qr_sql = "
			UPDATE indicador_plugin.investimento_rentabilidade_carteira
			   SET dt_referencia          = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
			       fl_media               = ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : "DEFAULT").",
			       cd_indicador_tabela    = ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
				   nr_valor_1             = ".(trim($args['nr_valor_1']) != '' ? floatval($args['nr_valor_1']) : "DEFAULT").",
				   nr_meta                = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   nr_inpc                = ".(trim($args['nr_inpc']) != '' ? floatval($args['nr_inpc']) : "DEFAULT").",
				   nr_atuarial_projetado  = ".(trim($args['nr_atuarial_projetado']) != '' ? floatval($args['nr_atuarial_projetado']) : "DEFAULT").",
				   nr_atuarial            = ".(trim($args['nr_atuarial']) != '' ? floatval($args['nr_atuarial']) : "DEFAULT").",
				   nr_rentabilidade_acum  = ".(trim($args['nr_rentabilidade_acum']) != '' ? floatval($args['nr_rentabilidade_acum']) : "DEFAULT").",
				   nr_meta_acum           = ".(trim($args['nr_meta_acum']) != '' ? floatval($args['nr_meta_acum']) : "DEFAULT").",
				   nr_inpc_acum           = ".(trim($args['nr_inpc_acum']) != '' ? floatval($args['nr_inpc_acum']) : "DEFAULT").",
				   nr_poder_referencia    = ".(trim($args['nr_poder_referencia']) != '' ? floatval($args['nr_poder_referencia']) : "DEFAULT").",
				   nr_poder_resultado     = ".(trim($args['nr_poder_resultado']) != '' ? floatval($args['nr_poder_resultado']) : "DEFAULT").",

				   nr_acumulado_ceee 	  = ".(trim($args['nr_acumulado_ceee']) != '' ? floatval($args['nr_acumulado_ceee']) : "DEFAULT").",
				   nr_benchmark_ceee 	  = ".(trim($args['nr_benchmark_ceee']) != '' ? floatval($args['nr_benchmark_ceee']) : "DEFAULT").",

				   nr_acumulado_rge 	  = ".(trim($args['nr_acumulado_rge']) != '' ? floatval($args['nr_acumulado_rge']) : "DEFAULT").",
				   nr_benchmark_rge 	  = ".(trim($args['nr_benchmark_rge']) != '' ? floatval($args['nr_benchmark_rge']) : "DEFAULT").",

				   nr_acumulado_aes 	  = ".(trim($args['nr_acumulado_aes']) != '' ? floatval($args['nr_acumulado_aes']) : "DEFAULT").",
				   nr_benchmark_aes 	  = ".(trim($args['nr_benchmark_aes']) != '' ? floatval($args['nr_benchmark_aes']) : "DEFAULT").",

				   nr_acumulado_cgte 	  = ".(trim($args['nr_acumulado_cgte']) != '' ? floatval($args['nr_acumulado_cgte']) : "DEFAULT").",
				   nr_benchmark_cgte 	  = ".(trim($args['nr_benchmark_cgte']) != '' ? floatval($args['nr_benchmark_cgte']) : "DEFAULT").",

				   nr_acumulado_ceeeprev  = ".(trim($args['nr_acumulado_ceeeprev']) != '' ? floatval($args['nr_acumulado_ceeeprev']) : "DEFAULT").",
				   nr_benchmark_ceeeprev  = ".(trim($args['nr_benchmark_ceeeprev']) != '' ? floatval($args['nr_benchmark_ceeeprev']) : "DEFAULT").",

				   nr_acumulado_senge 	  = ".(trim($args['nr_acumulado_senge']) != '' ? floatval($args['nr_acumulado_senge']) : "DEFAULT").",
				   nr_benchmark_senge 	  = ".(trim($args['nr_benchmark_senge']) != '' ? floatval($args['nr_benchmark_senge']) : "DEFAULT").",

				   nr_acumulado_sinpro 	  = ".(trim($args['nr_acumulado_sinpro']) != '' ? floatval($args['nr_acumulado_sinpro']) : "DEFAULT").",
				   nr_benchmark_sinpro 	  = ".(trim($args['nr_benchmark_sinpro']) != '' ? floatval($args['nr_benchmark_sinpro']) : "DEFAULT").",

				   nr_acumulado_fam_corp  = ".(trim($args['nr_acumulado_fam_corp']) != '' ? floatval($args['nr_acumulado_fam_corp']) : "DEFAULT").",
				   nr_benchmark_fam_corp  = ".(trim($args['nr_benchmark_fam_corp']) != '' ? floatval($args['nr_benchmark_fam_corp']) : "DEFAULT").",

				   nr_acumulado_fam_assoc = ".(trim($args['nr_acumulado_fam_assoc']) != '' ? floatval($args['nr_acumulado_fam_assoc']) : "DEFAULT").",
				   nr_benchmark_fam_assoc = ".(trim($args['nr_benchmark_fam_assoc']) != '' ? floatval($args['nr_benchmark_fam_assoc']) : "DEFAULT").",

				   nr_acumulado_ceranprev = ".(trim($args['nr_acumulado_ceranprev']) != '' ? floatval($args['nr_acumulado_ceranprev']) : "DEFAULT").",
				   nr_benchmark_ceranprev = ".(trim($args['nr_benchmark_ceranprev']) != '' ? floatval($args['nr_benchmark_ceranprev']) : "DEFAULT").",

				   nr_acumulado_fozprev   = ".(trim($args['nr_acumulado_fozprev']) != '' ? floatval($args['nr_acumulado_fozprev']) : "DEFAULT").",
				   nr_benchmark_fozprev   = ".(trim($args['nr_benchmark_fozprev']) != '' ? floatval($args['nr_benchmark_fozprev']) : "DEFAULT").",

				   nr_acumulado_crmprev   = ".(trim($args['nr_acumulado_crmprev']) != '' ? floatval($args['nr_acumulado_crmprev']) : "DEFAULT").",
				   nr_benchmark_cremprev  = ".(trim($args['nr_benchmark_cremprev']) != '' ? floatval($args['nr_benchmark_cremprev']) : "DEFAULT").",

				   nr_acumulado_pga 	  = ".(trim($args['nr_acumulado_pga']) != '' ? floatval($args['nr_acumulado_pga']) : "DEFAULT").",
				   nr_benchmark_pga 	  = ".(trim($args['nr_benchmark_pga']) != '' ? floatval($args['nr_benchmark_pga']) : "DEFAULT").",

				   nr_acumulado_municipio = ".(trim($args['nr_acumulado_municipio']) != '' ? floatval($args['nr_acumulado_municipio']) : "DEFAULT").",
				   nr_benchmark_municipio = ".(trim($args['nr_benchmark_municipio']) != '' ? floatval($args['nr_benchmark_municipio']) : "DEFAULT").",

				   nr_acumulado_ieab      = ".(trim($args['nr_acumulado_ieab']) != '' ? floatval($args['nr_acumulado_ieab']) : "DEFAULT").",
				   nr_benchmark_ieab      = ".(trim($args['nr_benchmark_ieab']) != '' ? floatval($args['nr_benchmark_ieab']) : "DEFAULT").",

				   ds_tabela 		  	  = ".(trim($args['ds_tabela']) != '' ? str_escape($args['ds_tabela']) : "DEFAULT").",

				   observacao             = ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : "DEFAULT").",
				   cd_usuario_alteracao   = ".intval($args['cd_usuario']).",
				   dt_alteracao           = CURRENT_TIMESTAMP
			 WHERE cd_investimento_rentabilidade_carteira = ".intval($cd_investimento_rentabilidade_carteira).";";

		$this->db->query($qr_sql);	
	}

	public function excluir($cd_investimento_rentabilidade_carteira, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.investimento_rentabilidade_carteira 
			   SET dt_exclusao          = CURRENT_TIMESTAMP, 
				   cd_usuario_exclusao  = ".intval($cd_usuario)."
			 WHERE cd_investimento_rentabilidade_carteira = ".intval($cd_investimento_rentabilidade_carteira).";"; 

		$this->db->query($qr_sql);
	}	

	public function fechar_ano($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.investimento_rentabilidade_carteira
			     (
                   dt_referencia, 
                   cd_indicador_tabela, 
                   fl_media, 
				   nr_valor_1,
				   nr_meta, 
				   nr_inpc,
				   nr_atuarial_projetado,
				   nr_atuarial,
				   nr_rentabilidade_acum,
				   nr_meta_acum,
				   nr_inpc_acum,
				   nr_poder_referencia,
				   nr_poder_resultado,
				   ds_tabela,
                   observacao,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
            VALUES 
			     (
				   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
				   ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : "DEFAULT").",
				   ".(trim($args['nr_valor_1']) != '' ? floatval($args['nr_valor_1']) : "DEFAULT").",
				   ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   ".(trim($args['nr_inpc']) != '' ? floatval($args['nr_inpc']) : "DEFAULT").",
				   ".(trim($args['nr_atuarial_projetado']) != '' ? floatval($args['nr_atuarial_projetado']) : "DEFAULT").",
				   ".(trim($args['nr_atuarial']) != '' ? floatval($args['nr_atuarial']) : "DEFAULT").",
				   ".(trim($args['nr_rentabilidade_acum']) != '' ? floatval($args['nr_rentabilidade_acum']) : "DEFAULT").",
				   ".(trim($args['nr_meta_acum']) != '' ? floatval($args['nr_meta_acum']) : "DEFAULT").",
				   ".(trim($args['nr_inpc_acum']) != '' ? floatval($args['nr_inpc_acum']) : "DEFAULT").",
				   ".(trim($args['nr_poder_referencia']) != '' ? floatval($args['nr_poder_referencia']) : "DEFAULT").",
				   ".(trim($args['nr_poder_resultado']) != '' ? floatval($args['nr_poder_resultado']) : "DEFAULT").",
				   ".(trim($args['ds_tabela']) != '' ? str_escape($args['ds_tabela']) : "DEFAULT").",
				   ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";


		$this->db->query($qr_sql);	
	}
	
	function fechar_periodo(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			       cd_usuario_fechamento_periodo = ".intval($args['cd_usuario'])."
			 WHERE cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).";";
			 			 
		$this->db->query($qr_sql);
	}	
}
?>