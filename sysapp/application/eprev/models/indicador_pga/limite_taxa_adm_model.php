<?php
class Limite_taxa_adm_model extends Model
{
	function __construct()
	{
		parent::Model();
	}


	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_limite_taxa_adm,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia, 'MM') AS mes_referencia,
				   TO_CHAR(dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
				   dt_referencia,
				   nr_recurso_garantidor,
				   nr_limite,
				   nr_deducao_limite,
				   nr_limite_efetivo,
				   nr_custeio_adm,
				   nr_efetivo_custeio,
				   nr_efetivo_garantidor,
				   nr_custeio_recurso,
				   fl_media,
				   ds_observacao
			  FROM indicador_pga.limite_taxa_adm
			 WHERE dt_exclusao IS NULL
			   AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY dt_referencia ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($cd_limite_taxa_adm)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS dt_referencia_n, 
				   cd_indicador_tabela 
			  FROM indicador_pga.limite_taxa_adm 
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_limite_taxa_adm,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   nr_recurso_garantidor,
				   nr_deducao_limite,  
				   nr_limite_efetivo,     
				   nr_custeio_adm,   
				   fl_media,
				   ds_observacao
			  FROM indicador_pga.limite_taxa_adm
			 WHERE cd_limite_taxa_adm = ".intval($cd_indicador_tabela).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}	

	public function salvar($args = array())
	{
		if(intval($args['cd_limite_taxa_adm']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_pga.limite_taxa_adm
				     (
				       cd_indicador_tabela,
                       dt_referencia, 
					   nr_recurso_garantidor,
					   nr_limite,
					   nr_deducao_limite,
					   nr_limite_efetivo,
					   nr_custeio_adm,
					   nr_efetivo_custeio,
					   nr_efetivo_garantidor,
					   nr_custeio_recurso,
					   fl_media, 
                       ds_observacao,
					   cd_usuario_inclusao,
					   cd_usuario_alteracao
					 )
                VALUES 
				     (
					   ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
					   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					   ".(trim($args['nr_recurso_garantidor']) != '' ? floatval($args['nr_recurso_garantidor']) : "DEFAULT").",
					   ".(trim($args['nr_limite']) != '' ? floatval($args['nr_limite']) : "DEFAULT").",
                       ".(trim($args['nr_deducao_limite']) != '' ? floatval($args['nr_deducao_limite']) : "DEFAULT").",
                       ".(trim($args['nr_limite_efetivo']) != '' ? floatval($args['nr_limite_efetivo']) : "DEFAULT").",
                       ".(trim($args['nr_custeio_adm']) != '' ? floatval($args['nr_custeio_adm']) : "DEFAULT").",
                       ".(trim($args['nr_efetivo_custeio']) != '' ? floatval($args['nr_efetivo_custeio']) : "DEFAULT").",
                       ".(trim($args['nr_efetivo_garantidor']) != '' ? floatval($args['nr_efetivo_garantidor']) : "DEFAULT").",
                       ".(trim($args['nr_custeio_recurso']) != '' ? floatval($args['nr_custeio_recurso']) : "DEFAULT").",
					   ".(trim($args['fl_media']) != '' ?  str_escape($args['fl_media']) : "DEFAULT").",
					   ".(trim($args['ds_observacao']) != '' ?  str_escape($args['ds_observacao']) : "DEFAULT").",
					   ".intval($args['cd_usuario']).",
					   ".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_pga.limite_taxa_adm
				   SET dt_referencia         = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				       nr_recurso_garantidor = ".(trim($args['nr_recurso_garantidor']) != '' ? floatval($args['nr_recurso_garantidor']) : "DEFAULT").",
					   nr_limite             = ".(trim($args['nr_limite']) != '' ? floatval($args['nr_limite']) : "DEFAULT").",
					   nr_deducao_limite     = ".(trim($args['nr_deducao_limite']) != '' ? floatval($args['nr_deducao_limite']) : "DEFAULT").",
					   nr_limite_efetivo     = ".(trim($args['nr_limite_efetivo']) != '' ? floatval($args['nr_limite_efetivo']) : "DEFAULT").",
					   nr_custeio_adm        = ".(trim($args['nr_custeio_adm']) != '' ? floatval($args['nr_custeio_adm']) : "DEFAULT").",
					   nr_efetivo_custeio    = ".(trim($args['nr_efetivo_custeio']) != '' ? floatval($args['nr_efetivo_custeio']) : "DEFAULT").",
					   nr_efetivo_garantidor = ".(trim($args['nr_efetivo_garantidor']) != '' ? floatval($args['nr_efetivo_garantidor']) : "DEFAULT").",
					   nr_custeio_recurso    = ".(trim($args['nr_custeio_recurso']) != '' ? floatval($args['nr_custeio_recurso']) : "DEFAULT").",
					   ds_observacao         = ".(trim($args['ds_observacao']) != '' ?  str_escape($args['ds_observacao']) : "DEFAULT").",
					   cd_usuario_alteracao  = ".intval($args['cd_usuario']).",
					   dt_alteracao          = CURRENT_TIMESTAMP
				 WHERE cd_limite_taxa_adm = ".intval($args['cd_limite_taxa_adm']).";";
		}
	
		$this->db->query($qr_sql);		
	}

	public function excluir($limite_taxa_adm, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_pga.limite_taxa_adm 
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			   	   cd_usuario_exclusao = ".intval($cd_usuario)." 
			 WHERE cd_limite_taxa_adm = ".intval($limite_taxa_adm).";"; 
		
		$this->db->query($qr_sql);
	}

	public function fechar_periodo($cd_indicador_tabela, $cd_usuario)
	{
		$qr_sql = "
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			       cd_usuario_fechamento_periodo = ".intval($cd_usuario)." 
		     WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela).";";

		$this->db->query($qr_sql);
	}
}