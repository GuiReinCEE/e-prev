<?php
class documento_protocolo_descarte_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function gerencias(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value,
			       nome AS text
			  FROM projetos.divisoes
			 WHERE tipo = 'DIV';";
		
		$result = $this->db->query($qr_sql);
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT dpd.cd_documento,
				   td.nome_documento,
				   CASE WHEN dpd.fl_descarte = 'S' THEN 'Sim'
                        ELSE 'Não'
                   END AS descarte,
				   dpd.cd_divisao,
				   TO_CHAR(dpd.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome
			  FROM projetos.documento_protocolo_descarte dpd
			  JOIN public.tipo_documentos td
				ON td.cd_tipo_doc = dpd.cd_documento
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = dpd.cd_usuario_inclusao
			 WHERE dpd.dt_exclusao IS NULL
			 ".(trim($args['cd_gerencia']) != '' ? "AND dpd.cd_divisao = '".trim($args['cd_gerencia'])."'" : "")."
			 ".(trim($args['fl_descarte']) != '' ? "AND dpd.fl_descarte = '".trim($args['fl_descarte'])."'" : "")."
			 ORDER BY cd_documento ASC;";
		
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT dpd.cd_documento,
			       dpd.fl_descarte,
				   dpd.cd_divisao,
				   dpd.cd_documento || ' - ' || td.nome_documento AS documento,
				   d.nome AS gerencia
			  FROM projetos.documento_protocolo_descarte dpd
			  JOIN public.tipo_documentos td
				ON td.cd_tipo_doc = dpd.cd_documento
		      JOIN projetos.divisoes d
			    ON d.codigo = dpd.cd_divisao
			 WHERE dpd.cd_documento = ".intval($args['cd_documento'])."
			   AND dpd.cd_divisao  = '".trim($args['cd_divisao'])."'
			   AND dpd.dt_exclusao IS NULL;";
	
		$result = $this->db->query($qr_sql);
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.documento_protocolo_descarte
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_documento = ".intval($args['cd_documento']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function verifica_documento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT COUNT(*) AS tl
			  FROM projetos.documento_protocolo_descarte
			 WHERE cd_documento = ".intval($args['cd_documento'])."
			   AND cd_divisao   = '".trim($args['cd_gerencia'])."'";
 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(trim($args['acao']) == 's')
		{
			$qr_sql = "
				INSERT INTO projetos.documento_protocolo_descarte
				     (
						cd_documento,
						cd_divisao,
						fl_descarte,
						cd_usuario_inclusao
					 )
			    VALUES 
				     (
						".intval($args['cd_documento']).",
						'".trim($args['cd_divisao'])."',
						'".trim($args['fl_descarte'])."',
						".intval($args['cd_usuario'])."
					 )";
		}
		else if(trim($args['acao']) == 'e')
		{
			$qr_sql = "
				UPDATE projetos.documento_protocolo_descarte
				   SET fl_descarte          = '".trim($args['fl_descarte'])."',
				       cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_documento = ".intval($args['cd_documento'])."
				   AND cd_divisao = '".trim($args['cd_divisao'])."'";
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	
	

}
?>