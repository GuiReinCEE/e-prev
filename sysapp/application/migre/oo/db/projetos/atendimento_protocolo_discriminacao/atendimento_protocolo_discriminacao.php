<?php
class atendimento_protocolo_discriminacao extends Service
{
	static public $entity;
	
	/**
	 * Retorna uma cole��o
	 * 
	 * @return e_atendimento_collection
	 */
	static function select($where=array())
	{
		return t_atendimento_protocolo_discriminacao::select($where);
	}
}
?>