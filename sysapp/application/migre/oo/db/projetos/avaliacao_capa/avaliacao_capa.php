<?php
class avaliacao_capa extends Service
{
	static public $entity;
	
	/**
	 * Retorna uma coleзгo da entidade de capas (e_avaliacao_capa_collection)
	 * 
	 * @return e_avaliacao_capa_collection
	 */
	static function select($where=null)
	{
		return t_avaliacao_capa::select($where);
	}
}
?>