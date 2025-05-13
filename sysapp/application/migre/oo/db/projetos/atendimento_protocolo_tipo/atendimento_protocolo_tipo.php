<?php
class atendimento_protocolo_tipo extends Service
{
	/**
	 * Retorna uma coleзгo
	 * 
	 * @return e_atendimento_collection
	 */
	static function select($where=array())
	{
		return t_atendimento_protocolo_tipo::select($where);
	}
}
?>