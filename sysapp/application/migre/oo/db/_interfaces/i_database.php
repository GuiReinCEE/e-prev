<?php
interface i_database
{
	// Conex�o
	function connect();

	/**
	 * Cria��o da query, setando os parametros
	 * 
	 * @param string $key
	 * 
	 * @param string $value
	 * 
	 * @param array $options Para tratamentos especiais em parametros
	 * 
	 * 		$options['is_date'] : indica que o $value se trata de data ptbr e deve ter o formato modificado para yyyy-mm-dd
	 * 		$options['use_null'] : indica que o $value se estiver em branco deve ser substitu�do por NULL e se preenchido deve ser devolvido entre ap�stofres.
	 * 
	 */
	function setParameter($key, $value, $options=array());

	// Execu��o da query
	function execute();
	function get();
	function getFirst();
	function haveError();
	
	//util
	function escape($value);
	function newId($value);
}
?>