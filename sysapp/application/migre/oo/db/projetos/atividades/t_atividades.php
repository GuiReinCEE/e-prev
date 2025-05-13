<?php
class t_atividades implements i_operations
{
	public static function select($where=array())
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$sql = "
			SELECT *
			  FROM projetos.atividades
			{WHERE_EXT}
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

		$ret2 = new e_atividades_collection();
		$i=-1;
		foreach( $rows as $row )
		{	
			$i++;
			$item = new e_atividades();
            foreach( $item as $key=>$value )
            {
            	eval( '$item->'.$key.' = $row['.$key.'];' );
            }
			$ret2->add( $item );
		}
		return $ret2;
	}
	public static function atividades_by_limite($where = array())
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$sql = "
			SELECT a.numero
				, u.guerra
				, l.descricao as status_descricao
				, a.titulo as atividade_titulo
				, TO_CHAR( a.dt_limite , 'DD/MM/YYYY' ) as dt_limite
				, TO_CHAR( a.dt_limite_testes , 'DD/MM/YYYY' ) as dt_limite_testes
				, TO_CHAR( a.dt_fim_real , 'DD/MM/YYYY' ) as dt_fim_real
				FROM projetos.atividades a 
				JOIN projetos.usuarios_controledi u ON u.codigo=a.cod_atendente
				JOIN public.listas l ON a.status_atual=l.codigo
				WHERE 
				    extract( 'month' from A.dt_limite )={mes} 
				AND extract('year' from A.dt_limite)={ano}
				AND area='{area}'
		   ORDER BY status_atual ASC;
		";

		$db->setSQL( $sql );
		
		$db->setParameter("{area}", $where['area']);
		$db->setParameter("{mes}", $where['mes']);
		$db->setParameter("{ano}", $where['ano']);
		
		$rows = array();
		$rows = $db->get();

		if( $db->haveError() )
		{
			echo $db->getMessage();
		}

		$ret2 = new e_atividades_collection();
		$i=-1;
		foreach( $rows as $row )
		{	
			$i++;
			$item = new e_atividades_ext();
			$item->atendente = new e_usuarios_controledi();
			$item->status = new e_listas();
            
			$item->numero = $row['numero'];
			$item->atendente->guerra = $row['guerra'];
			$item->status->descricao = $row['status_descricao'];
			$item->titulo = $row['atividade_titulo'];
			$item->dt_limite = $row['dt_limite'];
			$item->dt_limite_testes = $row['dt_limite_testes'];
			$item->dt_fim_real = $row['dt_fim_real'];
			
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