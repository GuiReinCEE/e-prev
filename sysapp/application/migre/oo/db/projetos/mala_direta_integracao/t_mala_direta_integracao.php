<?php
class t_mala_direta_integracao implements i_operations
{
	public static function select($where=array())
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$sql = "
			SELECT * FROM projetos.mala_direta_integracao
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

	/**
	 * Insere coleзгo de objetos
	 * 
	 * @param e_mala_direta_integracao_collection
	 * @return bool
	 */
	public static function insert_collection( e_mala_direta_integracao_collection $collection )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		
		$sql = "";
		$ent = new e_mala_direta_integracao();
		foreach( $collection->items as $ent )
		{
			$sql .= "

				INSERT INTO projetos.mala_direta_integracao
				( cd_empresa, cd_registro_empregado, seq_dependencia, usuario )
				VALUES
				( {cd_empresa}, {cd_registro_empregado}, {seq_dependencia}, '{usuario}' );

			";

			$sql = str_replace( "{cd_empresa}", intval($ent->cd_empresa), $sql );
			$sql = str_replace( "{cd_registro_empregado}", intval($ent->cd_registro_empregado), $sql );
			$sql = str_replace( "{seq_dependencia}", intval($ent->seq_dependencia), $sql );
			$sql = str_replace( "{usuario}", $db->escape($ent->usuario), $sql );
		}

		$db->setSQL($sql);
		$db->execute();

		if($db->haveError())
		{
			throw new Exception( $db->getMessage() );
			return false;
		}

		return true;
	}

	public static function update($entidade)
	{
		// TODO: implements
	}
	public static function delete($where)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$sql = " DELETE FROM projetos.mala_direta_integracao {WHERE_EXT} ";

		$where_ext = "";
		$where_sep = " WHERE ";
		foreach( $where as $key=>$value )
		{
			$where_ext .=  ' ' . $where_sep . ' ' . $key . ' = \'' . $db->escape($value) . '\'';
			$where_sep = ' AND ';
		}
		$sql = str_replace( "{WHERE_EXT}", $where_ext, $sql );

		$db->setSQL($sql);
		$db->execute();
		
		if( $db->haveError() )
		{
			throw new Exception( $db->getMessage() );
			return false;
		}
		
		return true;
	}
}
?>