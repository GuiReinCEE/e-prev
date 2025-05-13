<?php
class t_titulares implements i_operations
{
	public static function select($where=null)
	{
		// TODO: Implements
		return array();
	}
	
	public static function select_custom($where)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT *
		      FROM titulares
	         WHERE cd_empresa = {cd_empresa}
			   AND cd_registro_empregado = {cd_registro_empregado}
			   AND seq_dependencia = {seq_dependencia}

		" );

		$db->setParameter("{cd_empresa}", $where['cd_empresa']);
		$db->setParameter("{cd_registro_empregado}", $where['cd_registro_empregado']);
		$db->setParameter("{seq_dependencia}", $where['seq_dependencia']);

		$rows = array();
		$rows = $db->get();

		if( $db->haveError() )
		{
			echo $db->getMessage();
			exit;
		}

		$ret2 = new e_titulares_collection();
		$i=-1;
		foreach( $rows as $row )
		{	
			$i++;
			$item = new e_titulares();
            foreach( $item as $key=>$value )
            {
            	eval( '$item->'.$key.' = $row[' . $key . '];' );
            }
			$ret2->add( $item );
		}
		return $ret2;
	}
	
	public static function insert($entidade)
	{
		// TODO: Implements
	}
	public static function update($entidade)
	{
		// TODO: Implements
	}
	public static function delete($entidade)
	{
		// TODO: Implements
	}
}
?>