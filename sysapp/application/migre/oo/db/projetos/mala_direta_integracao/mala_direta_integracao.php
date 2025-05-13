<?php
class mala_direta_integracao extends Service
{
	static public $entity;
	
	/**
	 * Retorna uma cole��o
	 * 
	 * @return e_mala_direta_integracao
	 */
	static function select($where=array())
	{
		return t_mala_direta_integracao::select($where);
	}
	
	/**
	 * Prepara e insere pacote de dados na base de dados de um determinado usu�rio (logado)
	 * 
	 * @param e_mala_direta_integracao_collection $data
	 * @return boolean
	 */
	static function create_new_package( $logged_user,  e_mala_direta_integracao_collection $data )
	{
		$ok = true;
		
		// limpa ultimo pacote do usu�rio:
		$ok = t_mala_direta_integracao::delete( array('usuario'=>$logged_user) );
		
		if($ok)
		{
			// insere novo pacote apara o usu�rio:		
			$ok = t_mala_direta_integracao::insert_collection( $data );
		}
		
		return $ok;
	}
}
?>