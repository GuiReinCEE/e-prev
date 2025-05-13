<?php
class log_acessos_usuario extends Service
{
	static public $entity;
	
	static function select($where=null)
	{
		return t_log_acessos_usuario::select($where);
	}
	
	/**
	 * 
	 */
	static function insert(e_log_acessos_usuario $ent)
	{
		try
		{
			t_log_acessos_usuario::insert($ent);
			return true;
		}
		catch (Exception $e)
		{
			echo '<div id="logger_div" style="display:none;">' . $e->getMessage() . '</div>';
		}
	}
}
?>