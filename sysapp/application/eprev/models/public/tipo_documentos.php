<?php
class Tipo_documentos extends Model 
{
	function __construct()
	{
		parent::Model();
	}
	
	function select_dropdown()
	{
		$sql = "
			SELECT 
				cd_tipo_doc as value
				, nome_documento as text
			FROM 
				public.tipo_documentos
			ORDER BY
				nome_documento
		";

		$query = $this->db->query( $sql );
		if (  $query )
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}
}
?>