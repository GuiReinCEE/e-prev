<?php
class Atend_atendimentos_programa_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_atend_atendimentos_programa,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'MM') AS mes_referencia,
				   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_ano_referencia,
				   dt_referencia,
				   fl_media,
				   nr_pessoal_cad,
				   nr_pessoal_emp,
				   nr_pessoal_inv,
				   nr_pessoal_pre,
				   nr_pessoal_seg,
				   nr_telefonico_cad,
				   nr_telefonico_emp,
				   nr_telefonico_inv,
				   nr_telefonico_pre,
				   nr_telefonico_seg,
				   nr_email_cad,
				   nr_email_emp,
				   nr_email_inv,
				   nr_email_pre,
				   nr_email_seg,
				   nr_whatsapp_cad,
				   nr_whatsapp_emp,
				   nr_whatsapp_inv,
				   nr_whatsapp_pre,
				   nr_whatsapp_seg,
				   nr_virtual_cad,
				   nr_virtual_emp,
				   nr_virtual_inv,
				   nr_virtual_pre,
				   nr_virtual_seg,
				   nr_consulta_cad,
				   nr_consulta_emp,
				   nr_consulta_inv,
				   nr_consulta_pre,
				   nr_consulta_seg,
				   nr_total_cad,
				   nr_total_emp,
				   nr_total_inv,
				   nr_total_pre,
				   nr_total_seg,
				   observacao
			  FROM indicador_plugin.atend_atendimentos_programa 
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
			  FROM indicador_plugin.atend_atendimentos_programa 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_atend_atendimentos_programa)
	{
		$qr_sql = "
			SELECT cd_atend_atendimentos_programa,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   nr_pessoal_cad,
				   nr_pessoal_emp,
				   nr_pessoal_inv,
				   nr_pessoal_pre,
				   nr_pessoal_seg,
				   nr_telefonico_cad,
				   nr_telefonico_emp,
				   nr_telefonico_inv,
				   nr_telefonico_pre,
				   nr_telefonico_seg,
				   nr_email_cad,
				   nr_email_emp,
				   nr_email_inv,
				   nr_email_pre,
				   nr_email_seg,
				   nr_whatsapp_cad,
				   nr_whatsapp_emp,
				   nr_whatsapp_inv,
				   nr_whatsapp_pre,
				   nr_whatsapp_seg,
				   nr_virtual_cad,
				   nr_virtual_emp,
				   nr_virtual_inv,
				   nr_virtual_pre,
				   nr_virtual_seg,
				   nr_consulta_cad,
				   nr_consulta_emp,
				   nr_consulta_inv,
				   nr_consulta_pre,
				   nr_consulta_seg,
				   nr_total_cad,
				   nr_total_emp,
				   nr_total_inv,
				   nr_total_pre,
				   nr_total_seg,
				   observacao
			  FROM indicador_plugin.atend_atendimentos_programa 
			 WHERE dt_exclusao IS NULL
			   AND cd_atend_atendimentos_programa = ".intval($cd_atend_atendimentos_programa)."
			 ORDER BY dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.atend_atendimentos_programa
			     (
                   dt_referencia, 
                   cd_indicador_tabela, 
                   fl_media, 
				   nr_pessoal_cad,
				   nr_pessoal_emp,
				   nr_pessoal_inv,
				   nr_pessoal_pre,
				   nr_pessoal_seg,
				   nr_telefonico_cad,
				   nr_telefonico_emp,
				   nr_telefonico_inv,
				   nr_telefonico_pre,
				   nr_telefonico_seg,
				   nr_email_cad,
				   nr_email_emp,
				   nr_email_inv,
				   nr_email_pre,
				   nr_email_seg,
				   nr_whatsapp_cad,
				   nr_whatsapp_emp,
				   nr_whatsapp_inv,
				   nr_whatsapp_pre,
				   nr_whatsapp_seg,
				   nr_virtual_cad,
				   nr_virtual_emp,
				   nr_virtual_inv,
				   nr_virtual_pre,
				   nr_virtual_seg,
				   nr_consulta_cad,
				   nr_consulta_emp,
				   nr_consulta_inv,
				   nr_consulta_pre,
				   nr_consulta_seg,
				   nr_total_cad,
				   nr_total_emp,
				   nr_total_inv,
				   nr_total_pre,
				   nr_total_seg,
				   observacao,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
            VALUES 
			     (
				   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : 'DEFAULT').",
				   ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : 'DEFAULT').",
				   ".(trim($args['fl_media']) != '' ?  str_escape(trim($args['fl_media'])) : 'DEFAULT').",
				   ".(trim($args['nr_pessoal_cad']) != '' ? intval($args['nr_pessoal_cad']) : 'DEFAULT').",
				   ".(trim($args['nr_pessoal_emp']) != '' ? intval($args['nr_pessoal_emp']) : 'DEFAULT').",
				   ".(trim($args['nr_pessoal_inv']) != '' ? intval($args['nr_pessoal_inv']) : 'DEFAULT').",
				   ".(trim($args['nr_pessoal_pre']) != '' ? intval($args['nr_pessoal_pre']) : 'DEFAULT').",
				   ".(trim($args['nr_pessoal_seg']) != '' ? intval($args['nr_pessoal_seg']) : 'DEFAULT').",
				   ".(trim($args['nr_telefonico_cad']) != '' ? intval($args['nr_telefonico_cad']) : 'DEFAULT').",
				   ".(trim($args['nr_telefonico_emp']) != '' ? intval($args['nr_telefonico_emp']) : 'DEFAULT').",
				   ".(trim($args['nr_telefonico_inv']) != '' ? intval($args['nr_telefonico_inv']) : 'DEFAULT').",
				   ".(trim($args['nr_telefonico_pre']) != '' ? intval($args['nr_telefonico_pre']) : 'DEFAULT').",
				   ".(trim($args['nr_telefonico_seg']) != '' ? intval($args['nr_telefonico_seg']) : 'DEFAULT').",
				   ".(trim($args['nr_email_cad']) != '' ? intval($args['nr_email_cad']) : 'DEFAULT').",
				   ".(trim($args['nr_email_emp']) != '' ? intval($args['nr_email_emp']) : 'DEFAULT').",
				   ".(trim($args['nr_email_inv']) != '' ? intval($args['nr_email_inv']) : 'DEFAULT').",
				   ".(trim($args['nr_email_pre']) != '' ? intval($args['nr_email_pre']) : 'DEFAULT').",
				   ".(trim($args['nr_email_seg']) != '' ? intval($args['nr_email_seg']) : 'DEFAULT').",

				   ".(trim($args['nr_whatsapp_cad']) != '' ? intval($args['nr_whatsapp_cad']) : 'DEFAULT').",
				   ".(trim($args['nr_whatsapp_emp']) != '' ? intval($args['nr_whatsapp_emp']) : 'DEFAULT').",
				   ".(trim($args['nr_whatsapp_inv']) != '' ? intval($args['nr_whatsapp_inv']) : 'DEFAULT').",
				   ".(trim($args['nr_whatsapp_pre']) != '' ? intval($args['nr_whatsapp_pre']) : 'DEFAULT').",
				   ".(trim($args['nr_whatsapp_seg']) != '' ? intval($args['nr_whatsapp_seg']) : 'DEFAULT').",

				   ".(trim($args['nr_virtual_cad']) != '' ? intval($args['nr_virtual_cad']) : 'DEFAULT').",
				   ".(trim($args['nr_virtual_emp']) != '' ? intval($args['nr_virtual_emp']) : 'DEFAULT').",
				   ".(trim($args['nr_virtual_inv']) != '' ? intval($args['nr_virtual_inv']) : 'DEFAULT').",
				   ".(trim($args['nr_virtual_pre']) != '' ? intval($args['nr_virtual_pre']) : 'DEFAULT').",
				   ".(trim($args['nr_virtual_seg']) != '' ? intval($args['nr_virtual_seg']) : 'DEFAULT').",

				   ".(trim($args['nr_consulta_cad']) != '' ? intval($args['nr_consulta_cad']) : 'DEFAULT').",
				   ".(trim($args['nr_consulta_emp']) != '' ? intval($args['nr_consulta_emp']) : 'DEFAULT').",
				   ".(trim($args['nr_consulta_inv']) != '' ? intval($args['nr_consulta_inv']) : 'DEFAULT').",
				   ".(trim($args['nr_consulta_pre']) != '' ? intval($args['nr_consulta_pre']) : 'DEFAULT').",
				   ".(trim($args['nr_consulta_seg']) != '' ? intval($args['nr_consulta_seg']) : 'DEFAULT').",

				   ".(trim($args['nr_total_cad']) != '' ? intval($args['nr_total_cad']) : 'DEFAULT').",
				   ".(trim($args['nr_total_emp']) != '' ? intval($args['nr_total_emp']) : 'DEFAULT').",
				   ".(trim($args['nr_total_inv']) != '' ? intval($args['nr_total_inv']) : 'DEFAULT').",
				   ".(trim($args['nr_total_pre']) != '' ? intval($args['nr_total_pre']) : 'DEFAULT').",
				   ".(trim($args['nr_total_seg']) != '' ? intval($args['nr_total_seg']) : 'DEFAULT').",
				   ".(trim($args['observacao']) != '' ?  str_escape(trim($args['observacao'])) : 'DEFAULT').",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";

		$this->db->query($qr_sql);	
	}

	public function atualizar($cd_atend_atendimentos_programa, $args)
	{
		$qr_sql = "
			UPDATE indicador_plugin.atend_atendimentos_programa
			   SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : 'DEFAULT').",
			       fl_media             = ".(trim($args['fl_media']) != '' ?  str_escape(trim($args['fl_media'])) : 'DEFAULT').",
			       cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
				   nr_pessoal_cad       = ".(trim($args['nr_pessoal_cad']) != '' ? intval($args['nr_pessoal_cad']) : 'DEFAULT').",
				   nr_pessoal_emp       = ".(trim($args['nr_pessoal_emp']) != '' ? intval($args['nr_pessoal_emp']) : 'DEFAULT').",
				   nr_pessoal_inv       = ".(trim($args['nr_pessoal_inv']) != '' ? intval($args['nr_pessoal_inv']) : 'DEFAULT').",
				   nr_pessoal_pre       = ".(trim($args['nr_pessoal_pre']) != '' ? intval($args['nr_pessoal_pre']) : 'DEFAULT').",
				   nr_pessoal_seg       = ".(trim($args['nr_pessoal_seg']) != '' ? intval($args['nr_pessoal_seg']) : 'DEFAULT').",
				   nr_telefonico_cad    = ".(trim($args['nr_telefonico_cad']) != '' ? intval($args['nr_telefonico_cad']) : 'DEFAULT').",
				   nr_telefonico_emp    = ".(trim($args['nr_telefonico_emp']) != '' ? intval($args['nr_telefonico_emp']) : 'DEFAULT').",
				   nr_telefonico_inv    = ".(trim($args['nr_telefonico_inv']) != '' ? intval($args['nr_telefonico_inv']) : 'DEFAULT').",
				   nr_telefonico_pre    = ".(trim($args['nr_telefonico_pre']) != '' ? intval($args['nr_telefonico_pre']) : 'DEFAULT').",
				   nr_telefonico_seg    = ".(trim($args['nr_telefonico_seg']) != '' ? intval($args['nr_telefonico_seg']) : 'DEFAULT').",
				   nr_email_cad         = ".(trim($args['nr_email_cad']) != '' ? intval($args['nr_email_cad']) : 'DEFAULT').",
				   nr_email_emp         = ".(trim($args['nr_email_emp']) != '' ? intval($args['nr_email_emp']) : 'DEFAULT').",
				   nr_email_inv         = ".(trim($args['nr_email_inv']) != '' ? intval($args['nr_email_inv']) : 'DEFAULT').",
				   nr_email_pre         = ".(trim($args['nr_email_pre']) != '' ? intval($args['nr_email_pre']) : 'DEFAULT').",
				   nr_email_seg         = ".(trim($args['nr_email_seg']) != '' ? intval($args['nr_email_seg']) : 'DEFAULT').",

				   nr_whatsapp_cad         = ".(trim($args['nr_whatsapp_cad']) != '' ? intval($args['nr_whatsapp_cad']) : 'DEFAULT').",
				   nr_whatsapp_emp         = ".(trim($args['nr_whatsapp_emp']) != '' ? intval($args['nr_whatsapp_emp']) : 'DEFAULT').",
				   nr_whatsapp_inv         = ".(trim($args['nr_whatsapp_inv']) != '' ? intval($args['nr_whatsapp_inv']) : 'DEFAULT').",
				   nr_whatsapp_pre         = ".(trim($args['nr_whatsapp_pre']) != '' ? intval($args['nr_whatsapp_pre']) : 'DEFAULT').",
				   nr_whatsapp_seg         = ".(trim($args['nr_whatsapp_seg']) != '' ? intval($args['nr_whatsapp_seg']) : 'DEFAULT').",

				   nr_virtual_cad         = ".(trim($args['nr_virtual_cad']) != '' ? intval($args['nr_virtual_cad']) : 'DEFAULT').",
				   nr_virtual_emp         = ".(trim($args['nr_virtual_emp']) != '' ? intval($args['nr_virtual_emp']) : 'DEFAULT').",
				   nr_virtual_inv         = ".(trim($args['nr_virtual_inv']) != '' ? intval($args['nr_virtual_inv']) : 'DEFAULT').",
				   nr_virtual_pre         = ".(trim($args['nr_virtual_pre']) != '' ? intval($args['nr_virtual_pre']) : 'DEFAULT').",
				   nr_virtual_seg         = ".(trim($args['nr_virtual_seg']) != '' ? intval($args['nr_virtual_seg']) : 'DEFAULT').",

				   nr_consulta_cad         = ".(trim($args['nr_consulta_cad']) != '' ? intval($args['nr_consulta_cad']) : 'DEFAULT').",
				   nr_consulta_emp         = ".(trim($args['nr_consulta_emp']) != '' ? intval($args['nr_consulta_emp']) : 'DEFAULT').",
				   nr_consulta_inv         = ".(trim($args['nr_consulta_inv']) != '' ? intval($args['nr_consulta_inv']) : 'DEFAULT').",
				   nr_consulta_pre         = ".(trim($args['nr_consulta_pre']) != '' ? intval($args['nr_consulta_pre']) : 'DEFAULT').",
				   nr_consulta_seg         = ".(trim($args['nr_consulta_seg']) != '' ? intval($args['nr_consulta_seg']) : 'DEFAULT').",

				   nr_total_cad         = ".(trim($args['nr_total_cad']) != '' ? intval($args['nr_total_cad']) : 'DEFAULT').",
				   nr_total_emp         = ".(trim($args['nr_total_emp']) != '' ? intval($args['nr_total_emp']) : 'DEFAULT').",
				   nr_total_inv         = ".(trim($args['nr_total_inv']) != '' ? intval($args['nr_total_inv']) : 'DEFAULT').",
				   nr_total_pre         = ".(trim($args['nr_total_pre']) != '' ? intval($args['nr_total_pre']) : 'DEFAULT').",
				   nr_total_seg         = ".(trim($args['nr_total_seg']) != '' ? intval($args['nr_total_seg']) : 'DEFAULT').",
				   observacao           = ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : 'DEFAULT').",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_atend_atendimentos_programa = ".intval($cd_atend_atendimentos_programa).";";

		$this->db->query($qr_sql);	
	}

	public function excluir($cd_atend_atendimentos_programa, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.atend_atendimentos_programa 
			   SET dt_exclusao          = CURRENT_TIMESTAMP, 
				   cd_usuario_exclusao  = ".intval($cd_usuario)."
			 WHERE cd_atend_atendimentos_programa = ".intval($cd_atend_atendimentos_programa).";"; 

		$this->db->query($qr_sql);
	}	

	public function fechar_ano($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.atend_atendimentos_programa
			     (
                   dt_referencia, 
                   cd_indicador_tabela, 
                   fl_media, 
				   nr_pessoal_cad,
				   nr_pessoal_emp,
				   nr_pessoal_inv,
				   nr_pessoal_pre,
				   nr_pessoal_seg,
				   nr_telefonico_cad,
				   nr_telefonico_emp,
				   nr_telefonico_inv,
				   nr_telefonico_pre,
				   nr_telefonico_seg,
				   nr_email_cad,
				   nr_email_emp,
				   nr_email_inv,
				   nr_email_pre,
				   nr_email_seg,
				   nr_whatsapp_cad,
				   nr_whatsapp_emp,
				   nr_whatsapp_inv,
				   nr_whatsapp_pre,
				   nr_whatsapp_seg,
				   nr_virtual_cad,
				   nr_virtual_emp,
				   nr_virtual_inv,
				   nr_virtual_pre,
				   nr_virtual_seg,
				   nr_consulta_cad,
				   nr_consulta_emp,
				   nr_consulta_inv,
				   nr_consulta_pre,
				   nr_consulta_seg,
				   nr_total_cad,
				   nr_total_emp,
				   nr_total_inv,
				   nr_total_pre,
				   nr_total_seg,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
            VALUES 
			     (
				   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : 'DEFAULT').",
				   ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : 'DEFAULT').",
				   ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : 'DEFAULT').",
				   ".(trim($args['nr_pessoal_cad_total']) != '' ? intval($args['nr_pessoal_cad_total']) : 'DEFAULT').",
				   ".(trim($args['nr_pessoal_emp_total']) != '' ? intval($args['nr_pessoal_emp_total']) : 'DEFAULT').",
				   ".(trim($args['nr_pessoal_inv_total']) != '' ? intval($args['nr_pessoal_inv_total']) : 'DEFAULT').",
				   ".(trim($args['nr_pessoal_pre_total']) != '' ? intval($args['nr_pessoal_pre_total']) : 'DEFAULT').",
				   ".(trim($args['nr_pessoal_seg_total']) != '' ? intval($args['nr_pessoal_seg_total']) : 'DEFAULT').",
				   ".(trim($args['nr_telefonico_cad_total']) != '' ? intval($args['nr_telefonico_cad_total']) : 'DEFAULT').",
				   ".(trim($args['nr_telefonico_emp_total']) != '' ? intval($args['nr_telefonico_emp_total']) : 'DEFAULT').",
				   ".(trim($args['nr_telefonico_inv_total']) != '' ? intval($args['nr_telefonico_inv_total']) : 'DEFAULT').",
				   ".(trim($args['nr_telefonico_pre_total']) != '' ? intval($args['nr_telefonico_pre_total']) : 'DEFAULT').",
				   ".(trim($args['nr_telefonico_seg_total']) != '' ? intval($args['nr_telefonico_seg_total']) : 'DEFAULT').",
				   ".(trim($args['nr_email_cad_total']) != '' ? intval($args['nr_email_cad_total']) : 'DEFAULT').",
				   ".(trim($args['nr_email_emp_total']) != '' ? intval($args['nr_email_emp_total']) : 'DEFAULT').",
				   ".(trim($args['nr_email_inv_total']) != '' ? intval($args['nr_email_inv_total']) : 'DEFAULT').",
				   ".(trim($args['nr_email_pre_total']) != '' ? intval($args['nr_email_pre_total']) : 'DEFAULT').",
				   ".(trim($args['nr_email_seg_total']) != '' ? intval($args['nr_email_seg_total']) : 'DEFAULT').",

				   ".(trim($args['nr_whatsapp_cad']) != '' ? intval($args['nr_whatsapp_cad']) : 'DEFAULT').",
				   ".(trim($args['nr_whatsapp_emp']) != '' ? intval($args['nr_whatsapp_emp']) : 'DEFAULT').",
				   ".(trim($args['nr_whatsapp_inv']) != '' ? intval($args['nr_whatsapp_inv']) : 'DEFAULT').",
				   ".(trim($args['nr_whatsapp_pre']) != '' ? intval($args['nr_whatsapp_pre']) : 'DEFAULT').",
				   ".(trim($args['nr_whatsapp_seg']) != '' ? intval($args['nr_whatsapp_seg']) : 'DEFAULT').",

				   ".(trim($args['nr_virtual_cad']) != '' ? intval($args['nr_virtual_cad']) : 'DEFAULT').",
				   ".(trim($args['nr_virtual_emp']) != '' ? intval($args['nr_virtual_emp']) : 'DEFAULT').",
				   ".(trim($args['nr_virtual_inv']) != '' ? intval($args['nr_virtual_inv']) : 'DEFAULT').",
				   ".(trim($args['nr_virtual_pre']) != '' ? intval($args['nr_virtual_pre']) : 'DEFAULT').",
				   ".(trim($args['nr_virtual_seg']) != '' ? intval($args['nr_virtual_seg']) : 'DEFAULT').",

				   ".(trim($args['nr_consulta_cad']) != '' ? intval($args['nr_consulta_cad']) : 'DEFAULT').",
				   ".(trim($args['nr_consulta_emp']) != '' ? intval($args['nr_consulta_emp']) : 'DEFAULT').",
				   ".(trim($args['nr_consulta_inv']) != '' ? intval($args['nr_consulta_inv']) : 'DEFAULT').",
				   ".(trim($args['nr_consulta_pre']) != '' ? intval($args['nr_consulta_pre']) : 'DEFAULT').",
				   ".(trim($args['nr_consulta_seg']) != '' ? intval($args['nr_consulta_seg']) : 'DEFAULT').",
				   
				   ".(trim($args['nr_total_cad_total']) != '' ? intval($args['nr_total_cad_total']) : 'DEFAULT').",
				   ".(trim($args['nr_total_emp_total']) != '' ? intval($args['nr_total_emp_total']) : 'DEFAULT').",
				   ".(trim($args['nr_total_inv_total']) != '' ? intval($args['nr_total_inv_total']) : 'DEFAULT').",
				   ".(trim($args['nr_total_pre_total']) != '' ? intval($args['nr_total_pre_total']) : 'DEFAULT').",
				   ".(trim($args['nr_total_seg_total']) != '' ? intval($args['nr_total_seg_total']) : 'DEFAULT').",
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