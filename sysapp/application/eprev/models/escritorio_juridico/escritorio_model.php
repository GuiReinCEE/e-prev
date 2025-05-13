<?php
class Escritorio_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT pe.cd_escritorio AS cd_escritorio_oracle,
			       pe.nome_fantasia,
			       pe.cgc,
				   pe.representante,
				   eje.cd_escritorio,
				   eje.dt_exclusao
			  FROM public.escritorios pe
			  LEFT JOIN escritorio_juridico.escritorio eje
			    ON eje.cd_escritorio_oracle = pe.cd_escritorio
			 WHERE pe.situacao = 'A'
			   ".(trim($args['nome_fantasia']) != '' ? "AND UPPER(funcoes.remove_acento(pe.nome_fantasia)) LIKE (UPPER(funcoes.remove_acento('%".trim($args['nome_fantasia'])."%'))) " : "" )."
			   ".(trim($args['representante']) != '' ? "AND UPPER(funcoes.remove_acento(pe.representante)) LIKE (UPPER(funcoes.remove_acento('%".trim($args['representante'])."%'))) " : "" )."
			   ".(trim($args['fl_ativo']) == 'S' ? "AND eje.dt_exclusao IS NULL AND eje.cd_escritorio_oracle IS NOT NULL" : '') ."
			   ".(trim($args['fl_ativo']) == 'N' ? "AND (eje.dt_exclusao IS NOT NULL AND eje.cd_escritorio_oracle IS NOT NULL OR eje.cd_escritorio_oracle IS NULL)" : '').";";

		$result = $this->db->query($qr_sql);
	}	
	
	function ativar(&$result, $args=array())
	{
		$cd_escritorio = intval($this->db->get_new_id("escritorio_juridico.escritorio", "cd_escritorio"));
	
		$qr_sql = "
			INSERT INTO escritorio_juridico.escritorio
			     (
				   cd_escritorio,
				   ds_escritorio,
				   cd_escritorio_oracle,
				   cd_usuario_alteracao,
				   dt_alteracao
				 )
			VALUES
			     (
				    ".intval($cd_escritorio).",
					(SELECT nome_fantasia FROM public.escritorios WHERE cd_escritorio = ".intval($args['cd_escritorio_oracle'])."),
					".intval($args['cd_escritorio_oracle']).",
					".intval($args['cd_usuario']).",
					CURRENT_TIMESTAMP
				 );";
		
		$this->db->query($qr_sql);
		
		return $cd_escritorio;
	}
	
	function desativar(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE escritorio_juridico.escritorio
			   SET dt_exclusao          = CURRENT_TIMESTAMP,
			       dt_alteracao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao  = ".intval($args['cd_usuario']).",
				   cd_usuario_alteracao = ".intval($args['cd_usuario'])."
			 WHERE cd_escritorio = ".intval($args['cd_escritorio']).";";	
			 
		$this->db->query($qr_sql);
	}	
	
	function reativar(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE escritorio_juridico.escritorio
			   SET dt_exclusao          = NULL,
			       dt_alteracao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao  = NULL,
				   cd_usuario_alteracao = ".intval($args['cd_usuario'])."
			 WHERE cd_escritorio = ".intval($args['cd_escritorio']).";";
			 
		$this->db->query($qr_sql);
	}
	
	function monta_menu(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO escritorio_juridico.menu_escritorio
			    (
                   cd_menu, 
				   cd_escritorio
			    )
			SELECT cd_menu, 
			       ".intval($args['cd_escritorio'])."
              FROM escritorio_juridico.menu
			 WHERE dt_exclusao IS NULL;";
			
		$this->db->query($qr_sql);
	}
}
?>