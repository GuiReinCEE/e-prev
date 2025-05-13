<?php
class usuarios_controledi extends Service
{
	static public $SPECIAL_ATENDIMENTO_PROTOCOLO=0;

	/**
	 * Retorna uma coleзгo
	 * 
	 * @return e_usuarios_controledi_collection
	 */
	static function select($where=null)
	{
		return t_usuarios_controledi::select($where);
	}

	/**
	 * Retorna coleзгo de usuarios com critйrios especiais
	 * 
	 * @param int $objetivo
	 * @return e_usuarios_controledi_collection
	 */
	static function select_special( $objetivo )
	{
		if( $objetivo==usuarios_controledi::$SPECIAL_ATENDIMENTO_PROTOCOLO )
		{
			$ret = t_usuarios_controledi::list_exists_atendimento_protocolo();
		}
		else
		{
			$ret = null;
		}
		
		return $ret;
	}
}
?>