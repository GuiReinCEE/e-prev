<?php
class Enquete_grupo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT eg.cd_enquete_grupo,
				   eg.ds_titulo,
				   TO_CHAR(eg.dt_cadastro,'DD/MM/YYYY HH24:MI') AS dt_cadastro,
				   uc.nome AS usuario
			  FROM projetos.enquete_grupo eg 
			  JOIN projetos.usuarios_controledi uc  
				ON uc.codigo = eg.cd_usuario
			 WHERE eg.dt_exclusao IS NULL;";

		$result = $this->db->query($qr_sql);
	}
	
	function carrega( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_enquete_grupo,
				   ds_titulo,
				   ds_pergunta,
				   cd_enquete_sim,
				   cd_enquete_nao
			  FROM projetos.enquete_grupo
			 WHERE cd_enquete_grupo = ".intval($args['cd_enquete_grupo'])."";

		$result = $this->db->query($qr_sql);
	}

	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_enquete_grupo']) == 0)
		{
			$cd_enquete_grupo = intval($this->db->get_new_id("projetos.enquete_grupo", "cd_enquete_grupo"));
		
			$qr_sql = "
				INSERT INTO projetos.enquete_grupo
				     (
                       cd_enquete_grupo, 
					   ds_titulo, 
                       ds_pergunta, 
					   cd_enquete_sim, 
					   cd_enquete_nao, 
					   cd_usuario
					 )
                VALUES 
				     (
					   ".intval($cd_enquete_grupo).",
					   ".(trim($args['ds_titulo']) != '' ? str_escape($args['ds_titulo']) : "DEFAULT").",
					   ".(trim($args['ds_pergunta']) != '' ? str_escape($args['ds_pergunta']) : "DEFAULT").",
					   ".(trim($args['cd_enquete_sim']) != '' ? intval($args['cd_enquete_sim']) : "DEFAULT").",
					   ".(trim($args['cd_enquete_nao']) != '' ? intval($args['cd_enquete_nao']) : "DEFAULT").",
					   ".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$cd_enquete_grupo = $args['cd_enquete_grupo'];
		
			$qr_sql = "
				UPDATE projetos.enquete_grupo
                   SET ds_titulo      = ".(trim($args['ds_titulo']) != '' ? str_escape($args['ds_titulo']) : "DEFAULT").",
				       ds_pergunta    = ".(trim($args['ds_pergunta']) != '' ? str_escape($args['ds_pergunta']) : "DEFAULT").",
                       cd_enquete_sim = ".(trim($args['cd_enquete_sim']) != '' ? intval($args['cd_enquete_sim']) : "DEFAULT").",
					   cd_enquete_nao = ".(trim($args['cd_enquete_nao']) != '' ? intval($args['cd_enquete_nao']) : "DEFAULT")."
                 WHERE cd_enquete_grupo = ".intval($cd_enquete_grupo).";";
		}
	
		$result = $this->db->query($qr_sql);
		
		return $cd_enquete_grupo;
	}
}
?>