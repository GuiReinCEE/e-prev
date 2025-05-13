<?php
class atendimento extends Service
{
	static public $entity;
	
	/**
	 * Retorna uma cole��o
	 * 
	 * @return e_atendimento_collection
	 */
	static function select($where=null)
	{
		return t_atendimento::select($where);
	}
	
	/**
	 * Retorna uma cole��o
	 * 
	 * @param string $where Campo obrigat�rio que deve conter um array('cd_empresa'=>{NUMBER})
	 * @return e_atendimento_collection
	 */
	static function select_custom($where)
	{
		return t_atendimento::select_custom($where);
	}
}
?>