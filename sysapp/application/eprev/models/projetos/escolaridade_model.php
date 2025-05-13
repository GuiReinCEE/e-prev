<?php
class Escolaridade_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_escolaridade,
				   nome_escolaridade
			  FROM projetos.escolaridade
			 WHERE UPPER(funcoes.remove_acento(nome_escolaridade)) LIKE UPPER('%' || funcoes.remove_acento('" . trim(str_replace(' ','%',utf8_decode($args['nome']))) . "') || '%');";

		$result = $this->db->query($qr_sql);
	}

	function carrega( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_escolaridade,
				   nome_escolaridade,
				   desc_escolaridade
			  FROM projetos.escolaridade
			 WHERE cd_escolaridade = ".intval($args['cd_escolaridade']).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function salvar( &$result, $args=array() )
	{
		if(intval($args['cd_escolaridade']) == 0)
		{
			$qr_sql = " 
				INSERT INTO projetos.escolaridade 
				     ( 
					   nome_escolaridade, 
			           desc_escolaridade 
			         ) 
			    VALUES 
				     ( 
					    ".(trim($args['nome_escolaridade']) != '' ? "'".trim($args['nome_escolaridade'])."'" : "DEFAULT").",
						".(trim($args['desc_escolaridade']) != '' ? "'".trim($args['desc_escolaridade'])."'" : "DEFAULT")."
			         );";
		}
		else
		{
			$qr_sql = "
				UPDATE projetos.escolaridade 
				   SET nome_escolaridade = ".(trim($args['nome_escolaridade']) != '' ? "'".trim($args['nome_escolaridade'])."'" : "DEFAULT").",
			           desc_escolaridade = ".(trim($args['desc_escolaridade']) != '' ? "'".trim($args['desc_escolaridade'])."'" : "DEFAULT")."
			     WHERE cd_escolaridade = ".intval($args['cd_escolaridade']).";";
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	function listar_escala( &$result, $args=array() )
	{
		$qr_sql = "
		    SELECT cd_escala, 
				   descricao
			  FROM projetos.escala_proficiencia
			 WHERE cd_origem = 'ES' 
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
			 WHERE cd_origem = 'ES' 
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
						'ES'
			         );";
		}
		else
		{
			$qr_sql = "
				UPDATE projetos.escala_proficiencia 
				   SET descricao = ".(trim($args['descricao']) != '' ? "'".trim($args['descricao'])."'" : "DEFAULT")."
			     WHERE cd_escala = '".trim($args['cd_escala'])."'
				   AND cd_origem = 'ES';";
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	function escluir_escala( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE projetos.escala_proficiencia 
			   SET dt_exclusao = CURRENT_TIMESTAMP
			 WHERE cd_escala = '".trim($args['cd_escala'])."'
			   AND cd_origem = 'ES';";
			 
		$result = $this->db->query($qr_sql);
	}
}
?>