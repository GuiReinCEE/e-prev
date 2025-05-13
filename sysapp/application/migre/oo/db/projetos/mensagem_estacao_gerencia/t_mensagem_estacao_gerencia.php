<?php
class t_mensagem_estacao_gerencia
{
	/**
	 * Query para tabela para listas todas as gerencias 
	 * associadas a determinada mensagem
	 *
	 * @param $cd_mensagem Código da projetos.mensagem_estacao
	 *
	 * @return array(array) Resultado da consulta
	 */
	public static function select_1( $cd_mensagem )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "
			SELECT *
			FROM projetos.mensagem_estacao_gerencia
			WHERE cd_mensagem_estacao = {cd_mensagem_estacao}
		" );

		$db->setParameter( "{cd_mensagem_estacao}", $cd_mensagem );

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