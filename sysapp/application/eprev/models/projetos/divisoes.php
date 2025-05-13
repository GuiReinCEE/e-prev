<?php
class Divisoes extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function select_dropdown()
	{
		$sql = "
					SELECT codigo AS value,
				           codigo || ' - ' || nome AS text
			          FROM projetos.divisoes
			         WHERE tipo IN ('DIV', 'ASS')
			         ORDER BY nome
		       ";
		$query = $this->db->query( $sql );
		if ( $query )
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Verifica se a Gerencia informada no parâmetro
	 * tem permissão para receber atividades
	 * 
	 * @param string $gerencia
	 * @return boolean
	 */
	function permite_nova_atividade($gerencia)
	{
		if($gerencia=='')
		{
			return false;			
		}
		else
		{
			$sql="SELECT COUNT(*) AS fl_atividade
						  FROM projetos.divisoes 
						 WHERE fl_atividade = 'S'	
						   AND codigo       = '{codigo}'";
			esc('{codigo}',$gerencia,$sql);
			$q=$this->db->query($sql);
			
			$r=$q->row_array();
			
			return (intval($r["fl_atividade"])>0);
		}
	}
}
?>