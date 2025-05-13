<?php
class Listas_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT categoria,
			       codigo, 
			       descricao, 
				   divisao, 
				   valor, 
				   tipo, 
				   TO_CHAR(dt_exclusao, 'DD/MM/YYYY') AS dt_exclusao
			  FROM public.listas 
			 WHERE categoria = '".trim($args['categoria'])."';";

		$result = $this->db->query($qr_sql);
	}
	
	function cadastro( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT categoria,
			       codigo, 
			       descricao, 
				   divisao, 
				   valor, 
				   TO_CHAR(dt_exclusao, 'DD/MM/YYYY') AS dt_exclusao
			  FROM public.listas 
			 WHERE categoria = '".trim($args['categoria'])."'
			   AND codigo    = '".trim($args['codigo'])."';";

		$result = $this->db->query($qr_sql);
	}
	
	function divisao( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT codigo AS value, 
				   codigo AS text
			  FROM projetos.divisoes
			 WHERE tipo = 'DIV'
			 ORDER BY codigo;";

		$result = $this->db->query($qr_sql);
	}
	
	function salvar( &$result, $args=array() )
	{
		if(trim($args['codigo']) == '')
		{
			$qr_sql = "
				INSERT INTO public.listas
				     (
					   categoria,
					   codigo,
					   descricao,
					   divisao,
					   valor
					 )
				VALUES
				     (
						'".trim($args['categoria'])."',
						'".trim($args['codigo_new'])."',
						".(trim($args['descricao']) != '' ? "'".trim($args['descricao'])."'" : "DEFAULT").",
						".(trim($args['divisao']) != '' ? "'".trim($args['divisao'])."'" : "DEFAULT").",
						".(trim($args['valor']) != '' ? trim($args['valor']) : "DEFAULT")."
					 );";
		}
		else
		{
			$qr_sql = "
				UPDATE public.listas
				   SET codigo    = '".trim($args['codigo_new'])."',
				       descricao = ".(trim($args['descricao']) != '' ? "'".trim($args['descricao'])."'" : "DEFAULT").",
					   divisao   = ".(trim($args['divisao']) != '' ? "'".trim($args['divisao'])."'" : "DEFAULT").",
					   valor     = ".(trim($args['valor']) != '' ? trim($args['valor']) : "DEFAULT")."
				 WHERE categoria = '".trim($args['categoria'])."'
				   AND codigo    = '".trim($args['codigo'])."'";
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE public.listas
			   SET dt_exclusao =  CURRENT_TIMESTAMP
			 WHERE categoria = '".trim($args['categoria'])."'
			   AND codigo    = '".trim($args['codigo'])."'";
			   
		$result = $this->db->query($qr_sql);
	}
}
?>