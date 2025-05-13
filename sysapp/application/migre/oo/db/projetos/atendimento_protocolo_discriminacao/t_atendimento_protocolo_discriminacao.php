<?php
class t_atendimento_protocolo_discriminacao implements i_operations
{
	public static function select($where=array())
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$sql = "
			SELECT *
			  FROM projetos.atendimento_protocolo_discriminacao
			 WHERE dt_exclusao IS NULL
			{WHERE_EXT}
		  ORDER BY nome
		";
		foreach( $where as $key=>$value )
		{
			$where_ext .=  ' AND ' . $key . ' = \'' . $db->escape($value) . '\'';
		}
		$sql = str_replace( "{WHERE_EXT}", $where_ext, $sql );

		$db->setSQL( $sql );

		$rows = array();
		$rows = $db->get();

		if( $db->haveError() )
		{
			// echo $db->getMessage();
		}

		$ret2 = new e_atendimento_protocolo_discriminacao_collection();
		$i=-1;
		foreach( $rows as $row )
		{	
			$i++;
			$item = new e_atendimento_protocolo_discriminacao();
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