<?php
class Controladoria_certificado_iso_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_controladoria_certificado_iso,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'MM') AS mes_referencia,
				   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_ano_referencia,
				   dt_referencia,
				   fl_media,
				   nr_resultado,
				   nr_meta,
				   observacao
			  FROM indicador_plugin.controladoria_certificado_iso 
			 WHERE dt_exclusao IS NULL
			   AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 year'::interval,'DD/MM/YYYY') AS dt_referencia_n, 
				   TO_CHAR(dt_referencia + '1 year'::interval,'YYYY') AS ano_referencia, 
				   nr_meta, 
				   cd_indicador_tabela 
			  FROM indicador_plugin.controladoria_certificado_iso 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_controladoria_certificado_iso)
	{
		$qr_sql = "
			SELECT cd_controladoria_certificado_iso,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   nr_resultado,
				   nr_meta,
				   observacao
			  FROM indicador_plugin.controladoria_certificado_iso 
			 WHERE dt_exclusao IS NULL
			   AND cd_controladoria_certificado_iso = ".intval($cd_controladoria_certificado_iso)."
			 ORDER BY dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.controladoria_certificado_iso
			     (
                   dt_referencia, 
                   cd_indicador_tabela, 
                   fl_media, 
				   nr_resultado, 
				   nr_meta, 
                   observacao,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
            VALUES 
			     (
				   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
				   ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : "DEFAULT").",
				   ".(trim($args['nr_resultado']) != '' ? intval($args['nr_resultado']) : "DEFAULT").",
				   ".(trim($args['nr_meta']) != '' ? intval($args['nr_meta']) : "DEFAULT").",
				   ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";


		$this->db->query($qr_sql);	
	}

	public function atualizar($cd_controladoria_certificado_iso, $args)
	{
		$qr_sql = "
			UPDATE indicador_plugin.controladoria_certificado_iso
			   SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
			       fl_media             = ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : "DEFAULT").",
			       cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
			       nr_resultado         = ".(trim($args['nr_resultado']) != '' ? intval($args['nr_resultado']) : "DEFAULT").",
				   nr_meta              = ".(trim($args['nr_meta']) != '' ? intval($args['nr_meta']) : "DEFAULT").",
				   observacao           = ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_controladoria_certificado_iso = ".intval($cd_controladoria_certificado_iso).";";

		$this->db->query($qr_sql);	
	}

	public function excluir($cd_controladoria_certificado_iso, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.controladoria_certificado_iso 
			   SET dt_exclusao          = CURRENT_TIMESTAMP, 
				   cd_usuario_exclusao  = ".intval($cd_usuario)."
			 WHERE cd_controladoria_certificado_iso = ".intval($cd_controladoria_certificado_iso).";"; 

		$this->db->query($qr_sql);
	}	
}