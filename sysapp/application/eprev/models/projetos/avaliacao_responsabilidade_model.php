<?php
class Avaliacao_responsabilidade_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_responsabilidade,
				   nome_responsabilidade
			  FROM projetos.responsabilidades
			 WHERE UPPER(funcoes.remove_acento(nome_responsabilidade)) LIKE UPPER('%' || funcoes.remove_acento('" . trim(str_replace(' ','%',utf8_decode($args['nome']))) . "') || '%');";

		$result = $this->db->query($qr_sql);
	}
	
	function carrega( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_responsabilidade,
				   nome_responsabilidade,
				   desc_responsabilidade
			  FROM projetos.responsabilidades
			 WHERE cd_responsabilidade = ".intval($args['cd_responsabilidade']).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function salvar( &$result, $args=array() )
	{
		if(intval($args['cd_responsabilidade']) == 0)
		{
			$qr_sql = " 
				INSERT INTO projetos.responsabilidades 
				     ( 
					   nome_responsabilidade, 
			           desc_responsabilidade 
			         ) 
			    VALUES 
				     ( 
					    ".(trim($args['nome_responsabilidade']) != '' ? "'".trim($args['nome_responsabilidade'])."'" : "DEFAULT").",
						".(trim($args['desc_responsabilidade']) != '' ? "'".trim($args['desc_responsabilidade'])."'" : "DEFAULT")."
			         );";
		}
		else
		{
			$qr_sql = "
				UPDATE projetos.responsabilidades 
				   SET nome_responsabilidade = ".(trim($args['nome_responsabilidade']) != '' ? "'".trim($args['nome_responsabilidade'])."'" : "DEFAULT").",
			           desc_responsabilidade = ".(trim($args['desc_responsabilidade']) != '' ? "'".trim($args['desc_responsabilidade'])."'" : "DEFAULT")."
			     WHERE cd_responsabilidade = ".intval($args['cd_responsabilidade']).";";
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	function listar_escala( &$result, $args=array() )
	{
		$qr_sql = "
		    SELECT cd_escala, 
				   descricao
			  FROM projetos.escala_proficiencia
			 WHERE cd_origem = 'RE' 
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
			 WHERE cd_origem = 'RE' 
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
						'RE'
			         );";
		}
		else
		{
			$qr_sql = "
				UPDATE projetos.escala_proficiencia 
				   SET descricao = ".(trim($args['descricao']) != '' ? "'".trim($args['descricao'])."'" : "DEFAULT")."
			     WHERE cd_escala = '".trim($args['cd_escala'])."'
				   AND cd_origem = 'RE';";
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	function escluir_escala( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE projetos.escala_proficiencia 
			   SET dt_exclusao = CURRENT_TIMESTAMP
			 WHERE cd_escala = '".trim($args['cd_escala'])."'
			   AND cd_origem = 'RE';";
			 
		$result = $this->db->query($qr_sql);
	}
}
?>