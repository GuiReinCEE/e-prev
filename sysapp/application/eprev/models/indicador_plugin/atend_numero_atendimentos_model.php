<?php
class Atend_numero_atendimentos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_atend_numero_atendimentos,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'MM') AS mes_referencia,
				   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_ano_referencia,
				   dt_referencia,
				   fl_media,
				   nr_pessoal,
				   nr_telefonico,
				   nr_email,
				   nr_correspondencia,
				   nr_virtual,
				   nr_whatsapp,
				   nr_total,
				   observacao
			  FROM indicador_plugin.atend_numero_atendimentos 
			 WHERE dt_exclusao IS NULL
			   AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS dt_referencia_n, 
				   cd_indicador_tabela 
			  FROM indicador_plugin.atend_numero_atendimentos 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_atend_numero_atendimentos)
	{
		$qr_sql = "
			SELECT cd_atend_numero_atendimentos,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   nr_pessoal,
				   nr_telefonico,
				   nr_email,
				   nr_correspondencia,
				   nr_virtual,
				   nr_whatsapp,
				   observacao
			  FROM indicador_plugin.atend_numero_atendimentos 
			 WHERE dt_exclusao IS NULL
			   AND cd_atend_numero_atendimentos = ".intval($cd_atend_numero_atendimentos)."
			 ORDER BY dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.atend_numero_atendimentos
			     (
                   dt_referencia, 
                   cd_indicador_tabela, 
                   fl_media, 
				   nr_pessoal,
				   nr_telefonico,
				   nr_email,
				   nr_correspondencia,
				   nr_virtual,
				   nr_whatsapp,
				   nr_total,
                   observacao,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
            VALUES 
			     (
				   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : 'DEFAULT').",
				   ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : 'DEFAULT').",
				   ".(trim($args['fl_media']) != '' ?  str_escape(trim($args['fl_media'])) : 'DEFAULT').",
				   ".(trim($args['nr_pessoal']) != '' ? floatval($args['nr_pessoal']) : 'DEFAULT').",
				   ".(trim($args['nr_telefonico']) != '' ? floatval($args['nr_telefonico']) : 'DEFAULT').",
				   ".(trim($args['nr_email']) != '' ? floatval($args['nr_email']) : 'DEFAULT').",
				   ".(trim($args['nr_correspondencia']) != '' ? floatval($args['nr_correspondencia']) : 'DEFAULT').",
				   ".(trim($args['nr_virtual']) != '' ? floatval($args['nr_virtual']) : 'DEFAULT').",
				   ".(trim($args['nr_whatsapp']) != '' ? floatval($args['nr_whatsapp']) : 'DEFAULT').",
				   ".(trim($args['nr_total']) != '' ? floatval($args['nr_total']) : 'DEFAULT').",
				   ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : 'DEFAULT').",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";


		$this->db->query($qr_sql);	
	}

	public function atualizar($cd_atend_numero_atendimentos, $args)
	{
		$qr_sql = "
			UPDATE indicador_plugin.atend_numero_atendimentos
			   SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : 'DEFAULT').",
			       fl_media             = ".(trim($args['fl_media']) != '' ?  str_escape(trim($args['fl_media'])) : 'DEFAULT').",
			       cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
			       nr_pessoal           = ".(trim($args['nr_pessoal']) != '' ? floatval($args['nr_pessoal']) : 'DEFAULT').",
                   nr_telefonico        = ".(trim($args['nr_telefonico']) != '' ? floatval($args['nr_telefonico']) : 'DEFAULT').",
				   nr_email             = ".(trim($args['nr_email']) != '' ? floatval($args['nr_email']) : 'DEFAULT').",
				   nr_correspondencia   = ".(trim($args['nr_correspondencia']) != '' ? floatval($args['nr_correspondencia']) : 'DEFAULT').",
				   nr_virtual           = ".(trim($args['nr_virtual']) != '' ? floatval($args['nr_virtual']) : 'DEFAULT').",
				   nr_whatsapp          = ".(trim($args['nr_whatsapp']) != '' ? floatval($args['nr_whatsapp']) : 'DEFAULT').",
				   nr_total             = ".(trim($args['nr_total']) != '' ? floatval($args['nr_total']) : 'DEFAULT').",
				   observacao           = ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : 'DEFAULT').",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_atend_numero_atendimentos = ".intval($cd_atend_numero_atendimentos).";";

		$this->db->query($qr_sql);	
	}

	public function excluir($cd_atend_numero_atendimentos, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.atend_numero_atendimentos 
			   SET dt_exclusao          = CURRENT_TIMESTAMP, 
				   cd_usuario_exclusao  = ".intval($cd_usuario)."
			 WHERE cd_atend_numero_atendimentos = ".intval($cd_atend_numero_atendimentos).";"; 

		$this->db->query($qr_sql);
	}	

	public function fechar_ano($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.atend_numero_atendimentos
			     (
                   dt_referencia, 
                   cd_indicador_tabela, 
                   fl_media, 
                   nr_pessoal,
                   nr_telefonico,
                   nr_email,
                   nr_correspondencia,
                   nr_virtual,
				   nr_whatsapp,
				   nr_total, 
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
            VALUES 
			     (
				   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : 'DEFAULT').",
				   ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : 'DEFAULT').",
				   ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : 'DEFAULT').",
				   ".(trim($args['nr_pessoal_total']) != '' ? intval($args['nr_pessoal_total']) : 'DEFAULT').",
				   ".(trim($args['nr_telefonico_total']) != '' ? intval($args['nr_telefonico_total']) : 'DEFAULT').",
				   ".(trim($args['nr_email_total']) != '' ? intval($args['nr_email_total']) : 'DEFAULT').",
				   ".(trim($args['nr_correspondencia_total']) != '' ? intval($args['nr_correspondencia_total']) : 'DEFAULT').",
				   ".(trim($args['nr_virtual_total']) != '' ? intval($args['nr_virtual_total']) : 'DEFAULT').",
				   ".(trim($args['nr_whatsapp_total']) != '' ? intval($args['nr_whatsapp_total']) : 'DEFAULT').",
				   ".(trim($args['nr_total_total']) != '' ? intval($args['nr_total_total']) : "DEFAULT").",
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