<?php
class mysql extends DatabaseSuper implements i_database
{
	// Conex�o
	function connect()
	{
		// TODO: Implements
	}

	// Cria��o da query
	/**
	 * 
	 * @param string $key
	 * @param string $value
	 */
	function setParameter($key, $value, $options=array())
	{
		// TODO: Implements
	}

	// Execu��o da query
	/**
	 * 
	 * @return void
	 */
	function execute()
	{
		// TODO: Implements
	}
	
	/**
	 * 
	 * @return array Cole��o com todo o resultado da query
	 */
	function get()
	{
		// TODO: Implements
	}
	
	/**
	 * 
	 * @return array Array com colunas da query da primeira linha retornada
	 */
	function getFirst()
	{
		// TODO: Implements
	}
	
	/**
	 * 
	 * @return boolean
	 */
	function haveError()
	{
		// TODO: Implements
	}
	
	/**
	 * 
	 */
	function escape($value)
	{
		// TODO: Implements
	}
	
	/**
	 * 
	 */
	function newId($value)
	{
		// TODO: Implements
	}
}
?>