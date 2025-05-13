<?php
class Controladoria_acom_legislacao_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public  function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_controladoria_acom_legislacao,
			       cd_indicador_tabela,
				   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia, 'MM') AS mes_referencia,
				   TO_CHAR(dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
				   dt_referencia,
				   nr_normas_publicadas,
				   nr_normas_publicadas_fora_prazo,
				   nr_normas_respondidas_fora_prazo,
				   nr_normas_implementadas_fora_prazo,
				   nr_meta,
				   fl_media,
				   ds_observacao
		      FROM indicador_plugin.controladoria_acom_legislacao
		     WHERE dt_exclusao IS NULL
		       AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
		     ORDER BY dt_referencia ASC;";
		
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function carrega_referencia($nr_ano_referencia)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
			       nr_meta
				   cd_indicador_tabela ,
				   (SELECT COUNT(*)
				      FROM indicador_plugin.controladoria_acom_legislacao
				     WHERE TO_CHAR(dt_referencia, 'YYYY')::integer = ".intval($nr_ano_referencia)."
				       AND dt_exclusao IS NULL) AS qt_ano
			  FROM indicador_plugin.controladoria_acom_legislacao
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function carrega($cd_controladoria_acom_legislacao)
	{
		$qr_sql = "
            SELECT cd_controladoria_acom_legislacao,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_normas_publicadas,
				   nr_normas_publicadas_fora_prazo,
				   nr_normas_respondidas_fora_prazo,
				   nr_normas_implementadas_fora_prazo, 
				   nr_meta,
                   ds_observacao
		      FROM indicador_plugin.controladoria_acom_legislacao 
			 WHERE cd_controladoria_acom_legislacao = ".intval($cd_controladoria_acom_legislacao).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		if(intval($args['cd_controladoria_acom_legislacao']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.controladoria_acom_legislacao 
				     (
				        cd_indicador_tabela, 
						dt_referencia, 
					    fl_media,
						nr_normas_publicadas,
						nr_normas_publicadas_fora_prazo,
						nr_normas_respondidas_fora_prazo,
						nr_normas_implementadas_fora_prazo,
						nr_meta,
                        ds_observacao,
					    cd_usuario_inclusao,
					    cd_usuario_alteracao
			          ) 
			     VALUES 
				      ( 
				        ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
						".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					    ".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",
					    ".(trim($args['nr_normas_publicadas']) != '' ? intval($args['nr_normas_publicadas']) : "DEFAULT").",
					    ".(trim($args['nr_normas_publicadas_fora_prazo']) != '' ? intval($args['nr_normas_publicadas_fora_prazo']) : "DEFAULT").",
					    ".(trim($args['nr_normas_respondidas_fora_prazo']) != '' ? intval($args['nr_normas_respondidas_fora_prazo']) : "DEFAULT").",
					    ".(trim($args['nr_normas_implementadas_fora_prazo']) != '' ? intval($args['nr_normas_implementadas_fora_prazo']) : "DEFAULT").",
					    ".(trim($args['nr_meta']) != '' ? intval($args['nr_meta']) : "DEFAULT").",
					    ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
					    ".intval($args['cd_usuario']).",
					    ".intval($args['cd_usuario'])."
                      );";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.controladoria_acom_legislacao
				   SET dt_referencia                      = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				       nr_normas_publicadas               = ".(trim($args['nr_normas_publicadas']) != '' ? floatval($args['nr_normas_publicadas']) : "DEFAULT").",
				       nr_normas_publicadas_fora_prazo    = ".(trim($args['nr_normas_publicadas_fora_prazo']) != '' ? floatval($args['nr_normas_publicadas_fora_prazo']) : "DEFAULT").",
				       nr_normas_respondidas_fora_prazo   = ".(trim($args['nr_normas_respondidas_fora_prazo']) != '' ? floatval($args['nr_normas_respondidas_fora_prazo']) : "DEFAULT").",
				       nr_normas_implementadas_fora_prazo = ".(trim($args['nr_normas_implementadas_fora_prazo']) != '' ? floatval($args['nr_normas_implementadas_fora_prazo']) : "DEFAULT").",
				       nr_meta                            = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
                       ds_observacao                      = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
					   cd_usuario_alteracao               = ".intval($args['cd_usuario']).",
					   dt_alteracao                       = CURRENT_TIMESTAMP
			     WHERE cd_controladoria_acom_legislacao = ".intval($args['cd_controladoria_acom_legislacao']).";";
		}

		$this->db->query($qr_sql);
	}
	
	public function excluir($cd_controladoria_acom_legislacao, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.controladoria_acom_legislacao
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($cd_usuario)."
		     WHERE cd_controladoria_acom_legislacao = ".intval($cd_controladoria_acom_legislacao).";"; 
			 
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
?>