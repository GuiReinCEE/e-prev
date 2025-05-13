<?php
class atendimento_encaminhamento
{
	/**
	 * atendimento_encaminhamento::select_01($cd_empresa, $cd_registro_empregado, $seq_dependencia)
	 * Seleciona todos registros filtrando pelos parametros e nas seguintes condições: 
	 * Não cancelados, últimos 5 dias, ainda não relacionados a um protocolo de correspondência
	 * 
	 * @param int $cd_empresa código da empresa
	 * @param int $cd_registro_empregado código do registro do empregado
	 * @param int $seq_dependencia
	 * 
	 * @return array($row) onde row é um array() com as colunas da seleção
	 */
	public static function select_01( $cd_empresa, $cd_registro_empregado, $seq_dependencia, $cd_atendimento=0, $cd_encaminhamento=0 )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT * 
			FROM projetos.atendimento_encaminhamento pae 
			WHERE 
			(
				cd_empresa = {cd_empresa}
				AND cd_registro_empregado = {cd_registro_empregado}
				AND seq_dependencia = {seq_dependencia}
				AND dt_cancelado IS NULL
				AND dt_encaminhamento > ( current_timestamp - '5 days'::interval )
				AND NOT EXISTS
				(
					SELECT 1
					FROM projetos.atendimento_protocolo pap
					WHERE pap.cd_atendimento = pae.cd_atendimento
					AND pap.cd_encaminhamento = pae.cd_encaminhamento
				)
			)
			OR
			(
				cd_atendimento = {cd_atendimento} 
				AND cd_encaminhamento = {cd_encaminhamento}
			)

		" );

		$db->setParameter( "{cd_empresa}", $cd_empresa );
		$db->setParameter( "{cd_registro_empregado}", $cd_registro_empregado );
		$db->setParameter( "{seq_dependencia}", $seq_dependencia );
		$db->setParameter( "{cd_atendimento}", $cd_atendimento );
		$db->setParameter( "{cd_encaminhamento}", $cd_encaminhamento );

		$r = $db->get();

		$collection = array();
		foreach( $r as $row )
		{
			$collection[sizeof($collection)] = $row;
		}

		return $collection;
	}
	
	/**
	 * atendimento_encaminhamento::select_02( $cd_atendimento, $cd_encaminhamento )
	 * Seleciona registro filtrado pela PK 
	 * 
	 * @param int $cd_atendimento
	 * @param int $cd_encaminhamento
	 * 
	 * @return array($row) onde row é um array() com as colunas da seleção
	 */
	public static function select_02( $cd_atendimento, $cd_encaminhamento )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT * 
			  FROM projetos.atendimento_encaminhamento 
			 WHERE cd_atendimento = {cd_atendimento}
			   AND cd_encaminhamento = {cd_encaminhamento}

		" );

		$db->setParameter( "{cd_atendimento}", $cd_atendimento );
		$db->setParameter( "{cd_encaminhamento}", $cd_encaminhamento );

		$r = $db->get();

		$collection = array();
		foreach( $r as $row )
		{
			$collection[sizeof($collection)] = $row;
		}

		return $collection;
	}
}