<?php
class Contracheque_imagem extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
					SELECT arquivo_nome,
						   arquivo,
						   cd_contracheque_imagem,
						   TO_CHAR(dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
						   fl_tipo,
						   CASE WHEN fl_tipo = 'M' THEN 'Mensal'
								WHEN fl_tipo = 'B' THEN 'Anual'
						   END AS tipo
                      FROM autoatendimento.contracheque_imagem			
					 WHERE dt_exclusao IS NULL
						   ".(trim($args['fl_tipo'] !='') ? " AND fl_tipo = '".trim($args['fl_tipo'])."'" : '').";";

		return $this->db->query($qr_sql)->result_array();
	}
	
	public function carregar($cd_contracheque_imagem)
	{
		$qr_sql = "
			SELECT arquivo_nome,
				   arquivo,
				   cd_contracheque_imagem,
				   TO_CHAR(dt_referencia, 'DD/MM/YYYY HH24:MI:SS') AS dt_referencia,
				   fl_tipo
			  FROM autoatendimento.contracheque_imagem	
			 WHERE cd_contracheque_imagem  = ".intval($cd_contracheque_imagem)."
		  ";

		return $this->db->query($qr_sql)->row_array();
	}	
	
	public function salvar($cd_usuario, $args = array())
	{
		$cd_contracheque_imagem = intval($this->db->get_new_id('autoatendimento.contracheque_imagem', 'cd_contracheque_imagem'));
	
		$qr_sql = " 
			INSERT INTO autoatendimento.contracheque_imagem	
				 (
				   cd_contracheque_imagem,
				   fl_tipo, 
				   dt_referencia,
				   arquivo,
				   arquivo_nome,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
			VALUES 
				 (
				   ".intval($cd_contracheque_imagem).",
				   ".(trim($args['fl_tipo']) == "" ? "DEFAULT" : "'".$args['fl_tipo']."'").", 
				   ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_referencia']."','DD/MM/YYYY')").",
				   ".(trim($args['arquivo']) == "" ? "DEFAULT" : "'".$args['arquivo']."'").",
				   ".(trim($args['arquivo_nome']) == "" ? "DEFAULT" : "'".$args['arquivo_nome']."'").",
				   ".intval($cd_usuario).",
				   ".intval($cd_usuario)."
				 );";
				 
		$this->db->query($qr_sql);
		
	}	
	
	public function alterar($cd_contracheque_imagem, $cd_usuario, $args = array())
	{
		$qr_sql = " 
			UPDATE autoatendimento.contracheque_imagem	
			   SET fl_tipo        		= ".(trim($args['fl_tipo']) == "" ? "DEFAULT" : "'".$args['fl_tipo']."'").", 
				   dt_referencia  		= ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_referencia']."','DD/MM/YYYY')").",
				   arquivo        		= ".(trim($args['arquivo']) == "" ? "DEFAULT" : "'".$args['arquivo']."'").",
				   arquivo_nome         = ".(trim($args['arquivo_nome']) == "" ? "DEFAULT" : "'".$args['arquivo_nome']."'").",
				   cd_usuario_alteracao = ".intval($cd_usuario)."
			 WHERE cd_contracheque_imagem = ".intval($cd_contracheque_imagem).";";
			 
		$this->db->query($qr_sql);
	}	
}
?>