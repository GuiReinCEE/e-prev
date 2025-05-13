<?php

class cenario_legal_responsavel_model extends Model
{
	function __construct()
    {
        parent::Model();
    }
	
	function listar(&$result, $args=array())
    {
		$qr_sql = "
			SELECT uc.divisao,
			       uc.nome,
				   uc.codigo
			  FROM projetos.usuarios_controledi uc
			 WHERE uc.indic_03 = '*'
			   AND uc.tipo    NOT IN ('X', 'T')
			 ORDER BY nome;";
			
		$result = $this->db->query($qr_sql);
	}
	
	function gerencia(&$result, $args=array())
    {
		$qr_sql = "
			SELECT codigo AS value,
				   codigo || ' - ' || nome AS text
			  FROM projetos.divisoes
			 WHERE tipo = 'DIV'
			 ORDER BY codigo;";
			
		$result = $this->db->query($qr_sql);
	}
	
	function usuario(&$result, $args=array())
    {
		$qr_sql = "
			SELECT uc.codigo AS value,
			       uc.nome AS text
			  FROM projetos.usuarios_controledi uc
			 WHERE uc.indic_03 IS NULL
			   AND uc.divisao = '".trim($args['cd_gerencia'])."'
			   AND uc.tipo    NOT IN ('X', 'T')
			 ORDER BY nome;";
			
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.usuarios_controledi
			   SET indic_03 = '*'
			 WHERE codigo = ".intval($args['cd_usuario']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function remover(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.usuarios_controledi
			   SET indic_03 = NULL
			 WHERE codigo = ".intval($args['cd_usuario']).";";
			 
		$result = $this->db->query($qr_sql);
	}
}

?>