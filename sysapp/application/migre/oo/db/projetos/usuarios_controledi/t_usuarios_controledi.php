<?php
class t_usuarios_controledi implements i_operations
{
	public static function select_1($divisao)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL("
			SELECT codigo, nome
			FROM projetos.usuarios_controledi
			WHERE tipo NOT IN('X') and divisao = '{divisao}'
		  	ORDER BY nome
		");
		
		$db->setParameter("{divisao}", "$divisao");
		
		$query = $db->get();
		
		return $query;
	}
	
	public static function select($where=null)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$sql = "
			SELECT *
			  FROM projetos.usuarios_controledi
			{WHERE_EXT}
		  ORDER BY nome
		";
		$sep = ' WHERE ';
		foreach( $where as $key=>$value )
		{
			$where_ext .=  $sep . $key . ' = \'' . $db->escape($value) . '\'';
			$sep = ' AND ';
		}
		$sql = str_replace( "{WHERE_EXT}", $where_ext, $sql );

		$db->setSQL( $sql );

		$rows = array();
		$rows = $db->get();

		if( $db->haveError() )
		{
			// echo $db->getMessage();
		}

		$ret2 = new e_usuarios_controledi_collection();
		$i=-1;
		foreach( $rows as $row )
		{	
			$i++;
			$item = new e_usuarios_controledi();
            foreach( $item as $key=>$value )
            {
            	eval( '$item->'.$key.' = $row['.$key.'];' );
            }
			$ret2->add( $item );
		}
		return $ret2;
	}
	
	public static function list_exists_atendimento_protocolo($where=null)
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		
		$db->setSQL( "

			SELECT a.codigo, a.nome 
			  FROM projetos.usuarios_controledi a
		     WHERE EXISTS(
	     			SELECT 1 
	     			  FROM projetos.atendimento_protocolo b 
	     			 WHERE a.codigo = b.cd_usuario_criacao
	     			 )
		  ORDER BY a.nome;
		" );

		$col = array();
		$col = $db->get();

		$ret2 = new e_usuarios_controledi_collection();
		$i=-1;
		foreach( $col as $row )
		{	
			$i++;
			$item = new e_usuarios_controledi();
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
	}
	public static function update($entidade)
	{
	}
	public static function delete($entidade)
	{
	}
}
?>