<?php
class t_atendimento implements i_operations
{
	public static function select($where=null)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT *
			  FROM projetos.atendimento
			 WHERE cd_atendimento = {cd_atendimento}
			 LIMIT 100

		" );

		$db->setParameter("{cd_atendimento}", $where['cd_atendimento']);

		$rows = array();
		$rows = $db->get();

		if( $db->haveError() )
		{
			// echo $db->getMessage();
		}

		$ret2 = new e_atendimento_collection();
		$i=-1;
		foreach( $rows as $row )
		{	
			$i++;
			$item = new e_atendimento();
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
	
	
	public static function select_custom($where)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT *
			  FROM projetos.atendimento
			 WHERE cd_empresa = {cd_empresa}
		  ORDER BY cd_atendimento

		" );

		$db->setParameter("{cd_empresa}", $where['cd_empresa']);

		$rows = array();
		$rows = $db->get();

		if( $db->haveError() )
		{
			echo $db->getMessage();
		}

		$ret2 = new e_atendimento_collection();
		$i=-1;
		foreach( $rows as $row )
		{	
			$i++;
			$item = new e_atendimento();
            foreach( $item as $key=>$value )
            {
            	eval( '$item->'.$key.' = $row['.$key.'];' );
            }
			$ret2->add( $item );
		}
		return $ret2;
	}
}
?>