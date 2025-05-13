<?php
class Prevenir_formulario_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_prevenir_formulario,
                   ds_nome,
                   TO_CHAR(dt_envio, 'DD/MM/YYYY HH24:MI') AS dt_envio
              FROM prevenir.prevenir_formulario
             WHERE dt_envio IS NOT NULL
			 ".(((trim($args['dt_envio_ini']) != "") and  (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', dt_envio) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
              ;";

		$result = $this->db->query($qr_sql);
	}
	
	function formulario_item(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_pergunta,
				   o_que,
				   porque,
				   quem,
				   quando,
				   onde,
				   como
			  FROM prevenir.prevenir_formulario_item
			 WHERE dt_exclusao IS NULL
			   AND fl_exibir   = 'S'
			   AND cd_pergunta = ".intval($args['cd_pergunta']).";";
			   
		$result = $this->db->query($qr_sql);
	}

	function formulario(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ds_nome,
                   ds_instituicao,
                   ds_email,
                   nr_telefone,
				   TO_CHAR(dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio
			  FROM prevenir.prevenir_formulario
			 WHERE cd_prevenir_formulario = ".intval($args['cd_prevenir_formulario']).";";
			   
		$result = $this->db->query($qr_sql);
	}

	function previnir_formulario(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_pergunta,
				   cd_prevenir_formulario_item,
				   o_que,
				   porque,
				   quem,
				   quando,
				   onde,
				   como,
				   fl_exibir
			  FROM prevenir.prevenir_formulario_item
			 WHERE dt_exclusao IS NULL
			   AND cd_prevenir_formulario = ".intval($args['cd_prevenir_formulario'])."
			 ORDER BY cd_pergunta ASC;";
			 
		$result = $this->db->query($qr_sql);	 
	}
	
	function muda_exibicao(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE prevenir.prevenir_formulario_item
			   SET fl_exibir = '".trim($args['fl_exibir'])."'
			 WHERE cd_prevenir_formulario_item = ".intval($args['cd_prevenir_formulario_item']).";";
			
		$result = $this->db->query($qr_sql);	
	}
}
?>