<?php
class t_auto_atendimento_formulario implements i_operations
{
	public static function select($where='')
	{
		$db = new postgres();
		
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT *
			  FROM projetos.auto_atendimento_formulario
			 WHERE (cd_plano = {cd_plano})
			   AND (fl_migrado = '{fl_migrado}' OR '' = '{fl_migrado}') 
		  ORDER BY ds_formulario

		" );

		foreach( $where as $key=>$value )
		{
			$db->setParameter("{" . $key . "}", $value);
		}

		$rows = array();
		$rows = $db->get();
		
		if( $db->haveError() )
		{
			// echo $db->getMessage();
			exit;
		}

		$ret2 = new e_auto_atendimento_formulario_collection();
		$i=-1;
		foreach( $rows as $row )
		{	
			$i++;
			$item = new e_auto_atendimento_formulario();
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