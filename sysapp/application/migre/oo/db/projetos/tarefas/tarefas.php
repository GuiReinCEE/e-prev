<?php
class tarefas
{
	/**
	 * Carregar tarefa
	 * filtrando pela OS e Tarefa
	 * 
	 * @param $cd_atividade
	 * @param $cd_tarefa
	 * 
	 * @return array(array()) SELECT

				tarefas.*

			FROM

				projetos.tarefas tarefas

			WHERE 

				cd_atividade = {cd_atividade} 
				AND cd_tarefa = {cd_tarefa}

			;
	 *  
	 */
	public static function select_1($cd_atividade, $cd_tarefa)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT

				tarefas.*

			FROM

				projetos.tarefas tarefas

			WHERE 

				cd_atividade = {cd_atividade} 
				AND cd_tarefa = {cd_tarefa}

			;

		" );

		$db->setParameter("{cd_atividade}", intval($cd_atividade));
		$db->setParameter("{cd_tarefa}", intval($cd_tarefa));

		$r = $db->get(true);
		$collection = array();

		foreach( $r as $item )
		{
			$collection[sizeof($collection)] = $item;
		}

		return $collection;
	}
}
?>