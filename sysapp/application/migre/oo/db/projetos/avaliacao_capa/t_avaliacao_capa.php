<?php
class t_avaliacao_capa implements i_operations
{
	public static function select($where=null)
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		
		$db->setSQL( "

			SELECT *  
			  FROM projetos.avaliacao_capa

		" );

		$col = array();
		$col = $db->get();

		$ret2 = new e_avaliacao_capa_collection();
		$i=-1;
		foreach( $col as $row )
		{	
			$i++;
			$item = new e_avaliacao_capa();
            foreach( $item as $key=>$value )
            {
            	eval( '$item->'.$key.' = $row['.$key.'];' );
            }
			$ret2->add( $item );
		}
		return $ret2;
	}
	public static function insert($entidade)
	{
		// TODO: implements
	}
	public static function update($entidade)
	{
		// TODO: implements
	}
	public static function delete($entidade)
	{
		// TODO: implements
	}
}
?>