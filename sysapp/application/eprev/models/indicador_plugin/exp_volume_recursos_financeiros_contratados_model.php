<?php
class Exp_volume_recursos_financeiros_contratados_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT i.cd_exp_volume_recursos_financeiros_contratados,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'MM') AS mes_referencia,
				   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_ano_referencia,
				   i.dt_referencia,
				   i.cd_indicador_tabela,
				   i.fl_media,
				   i.observacao,
				   i.nr_contratado,
				   i.nr_meta,
				   i.fl_meta,
				   i.fl_direcao,
				   (SELECT i1.tp_analise
					  FROM indicador.indicador_tabela it
					  JOIN indicador.indicador i1
						ON i1.cd_indicador = it.cd_indicador
					 WHERE it.cd_indicador_tabela = i.cd_indicador_tabela) AS tp_analise
			  FROM indicador_plugin.exp_volume_recursos_financeiros_contratados i
			 WHERE i.dt_exclusao IS NULL
			   AND(i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY i.dt_referencia ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($cd_indicador_tabela, $nr_ano_referencia)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS dt_referencia_n, 
				   nr_meta, 
				   cd_indicador_tabela,
				   (SELECT COUNT(*)
				      FROM indicador_plugin.exp_volume_recursos_financeiros_contratados
				     WHERE TO_CHAR(dt_referencia, 'YYYY')::integer = ".intval($nr_ano_referencia)."
				       AND dt_exclusao IS NULL) AS qt_ano
			  FROM indicador_plugin.exp_volume_recursos_financeiros_contratados 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_exp_volume_recursos_financeiros_contratados)
	{
		$qr_sql = "
			SELECT cd_exp_volume_recursos_financeiros_contratados,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   cd_indicador_tabela,
				   nr_contratado,
				   nr_meta,
				   observacao
			  FROM indicador_plugin.exp_volume_recursos_financeiros_contratados 
			 WHERE dt_exclusao IS NULL
			   AND cd_exp_volume_recursos_financeiros_contratados = ".intval($cd_exp_volume_recursos_financeiros_contratados)."
			 ORDER BY dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.exp_volume_recursos_financeiros_contratados
			     (
                   dt_referencia, 
                   cd_indicador_tabela, 
                   fl_media, 
				   nr_contratado,
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
				   ".(trim($args['nr_contratado']) != '' ? floatval($args['nr_contratado']) : "DEFAULT").",
				   ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";


		$this->db->query($qr_sql);	
	}

	public function atualizar($cd_exp_volume_recursos_financeiros_contratados, $args)
	{
		$qr_sql = "
			UPDATE indicador_plugin.exp_volume_recursos_financeiros_contratados
			   SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
			       fl_media             = ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : "DEFAULT").",
			       cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
			       nr_contratado        = ".(trim($args['nr_contratado']) != '' ? floatval($args['nr_contratado']) : "DEFAULT").",
				   nr_meta              = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   observacao           = ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_exp_volume_recursos_financeiros_contratados = ".intval($cd_exp_volume_recursos_financeiros_contratados).";";

		$this->db->query($qr_sql);	
	}
	
	public function excluir($cd_exp_volume_recursos_financeiros_contratados, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.exp_volume_recursos_financeiros_contratados 
			   SET dt_exclusao          = CURRENT_TIMESTAMP, 
				   cd_usuario_exclusao  = ".intval($cd_usuario)."
			 WHERE cd_exp_volume_recursos_financeiros_contratados = ".intval($cd_exp_volume_recursos_financeiros_contratados).";"; 

		$this->db->query($qr_sql);
	}	
	
	public function fechar_ano($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.exp_volume_recursos_financeiros_contratados
			     (
                   dt_referencia, 
                   cd_indicador_tabela, 
                   fl_media, 
                   nr_contratado,
				   nr_meta, 
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
            VALUES 
			     (
				   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
				   ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : "DEFAULT").",
				   ".(trim($args['nr_contratado']) != '' ? floatval($args['nr_contratado']) : "DEFAULT").",
				   ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";


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
?>