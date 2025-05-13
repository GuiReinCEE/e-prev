<?php
class divisoes
{
	/**
	 * Query para lista completa de divisões
	 * 
	 * @return array
	 * 
	 *		SELECT *
	 *		FROM projetos.divisoes
	 * 		WHERE tipo IN ('ASS', 'DIV')
	 *		ORDER BY nome;
	 * 
	 */
	public static function select_1()
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "
			SELECT *
			FROM projetos.divisoes
			WHERE tipo IN ('ASS', 'DIV')
			ORDER BY nome;
		" );

		$r = $db->get();

		$collection = array();
		foreach( $r as $row )
		{
			$collection[sizeof($collection)] = $row;
		}

		return $collection;
	}
}
?>