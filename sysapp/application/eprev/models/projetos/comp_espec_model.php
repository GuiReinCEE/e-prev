<?php
class Comp_espec_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_comp_espec,
				   nome_comp_espec,
				   desc_comp_espec
			  FROM projetos.comp_espec
			 WHERE UPPER(funcoes.remove_acento(nome_comp_espec)) LIKE UPPER('%' || funcoes.remove_acento('" . trim(str_replace(' ','%',utf8_decode($args['nome']))) . "') || '%');";

		$result = $this->db->query($qr_sql);
	}
	
	function carrega( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_comp_espec,
				   nome_comp_espec,
				   desc_comp_espec
			  FROM projetos.comp_espec
			 WHERE cd_comp_espec = ".intval($args['cd_comp_espec']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar( &$result, $args=array() )
	{
		if(intval($args['cd_comp_espec']) == 0)
		{
			$qr_sql = " 
				INSERT INTO projetos.comp_espec 
				     ( 
					   nome_comp_espec, 
			           desc_comp_espec 
			         ) 
			    VALUES 
				     ( 
					    ".(trim($args['nome_comp_espec']) != '' ? "'".trim($args['nome_comp_espec'])."'" : "DEFAULT").",
						".(trim($args['desc_comp_espec']) != '' ? "'".trim($args['desc_comp_espec'])."'" : "DEFAULT")."
			         );";
		}
		else
		{
			$qr_sql = "
				UPDATE projetos.comp_espec 
				   SET nome_comp_espec = ".(trim($args['nome_comp_espec']) != '' ? "'".trim($args['nome_comp_espec'])."'" : "DEFAULT").",
			           desc_comp_espec = ".(trim($args['desc_comp_espec']) != '' ? "'".trim($args['desc_comp_espec'])."'" : "DEFAULT")."
			     WHERE cd_comp_espec = ".intval($args['cd_comp_espec']).";";
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	function listar_escala( &$result, $args=array() )
	{
		$qr_sql = "
		    SELECT cd_escala, 
				   descricao
			  FROM projetos.escala_proficiencia
			 WHERE cd_origem = 'CE' 
			   AND dt_exclusao IS NULL
			   AND UPPER(funcoes.remove_acento(descricao)) LIKE UPPER('%' || funcoes.remove_acento('" . trim(str_replace(' ','%',utf8_decode($args['descricao']))) . "') || '%')
			 ORDER BY cd_escala;";

		$result = $this->db->query($qr_sql);
	}
	
	function carrega_escala( &$result, $args=array() )
	{
		$qr_sql = "
		    SELECT cd_escala, 
				   descricao
			  FROM projetos.escala_proficiencia
			 WHERE cd_origem = 'CE' 
			   AND dt_exclusao IS NULL
			   AND cd_escala = '".trim($args['cd_escala'])."';";

		$result = $this->db->query($qr_sql);
	}
	
	function salvar_escala( &$result, $args=array() )
	{
		if(intval($args['insert']) == 0)
		{
			$qr_sql = " 
				INSERT INTO projetos.escala_proficiencia 
				     ( 
					   cd_escala, 
			           descricao,
					   cd_origem
			         ) 
			    VALUES 
				     ( 
					    ".(trim($args['cd_escala']) != '' ? "'".trim($args['cd_escala'])."'" : "DEFAULT").",
						".(trim($args['descricao']) != '' ? "'".trim($args['descricao'])."'" : "DEFAULT").",
						'CE'
			         );";
		}
		else
		{
			$qr_sql = "
				UPDATE projetos.escala_proficiencia 
				   SET descricao = ".(trim($args['descricao']) != '' ? "'".trim($args['descricao'])."'" : "DEFAULT")."
			     WHERE cd_escala = '".trim($args['cd_escala'])."'
				   AND cd_origem = 'CE';";
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	function escluir_escala( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE projetos.escala_proficiencia 
			   SET dt_exclusao = CURRENT_TIMESTAMP
			 WHERE cd_escala = '".trim($args['cd_escala'])."'
			   AND cd_origem = 'CE';";
			 
		$result = $this->db->query($qr_sql);
	}
}
?>