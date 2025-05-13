<?php
class t_log_acessos_usuario implements i_operations
{
	public static function select($where=null)
	{
		$db = DBFactory::createObject();
		
		$db->setSQL( "

		SELECT sid, to_char( hora, 'hh24:mi:ss' ) as hora, pagina  
		  FROM public.log_acessos_usuario
	  ORDER BY sid

		" );

		$col = array();
		$col = $db->get();

		$ret2 = new e_log_acessos_usuario_collection();
		$i=-1;
		foreach( $col as $row )
		{	
			$i++;
			$item = new e_log_acessos_usuario();
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
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL("
			INSERT INTO public.log_acessos_usuario
				(sid, hora, pagina )
			VALUES
				({sid}, CURRENT_TIMESTAMP, '{pagina}' );
		");
		
		$db->setParameter("{sid}", $entidade->sid);
		$db->setParameter("{pagina}", $entidade->pagina);
		
		$db->execute();
		
		if( $db->haveError() )
		{
			throw new Exception( $db->getMessage() );
		}
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