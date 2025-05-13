<?php
class auto_atendimento_formulario extends Service
{
	static public $entity;
	
	/**
	 * Retorna uma coleзгo
	 * 
	 * @param array('cd_plano'=>'valor', 'migrado'=>'valor') $where Parametro para filtros
	 * 
	 * @return e_auto_atendimento_formulario_collection
	 */
	static function select($where)
	{
		return t_auto_atendimento_formulario::select($where);
	}
}
?>