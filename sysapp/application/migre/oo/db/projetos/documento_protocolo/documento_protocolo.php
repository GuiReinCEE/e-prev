<?php
class documento_protocolo
{
	/**
	 * documento_protocolo::protocolo_ja_confirmado()
	 * 
	 * Retorna true ou false se existir ou nуo existir confirmaчуo (recebimento e indexaчуo) 
	 * 
	 * @return bool
	 */
	public static function protocolo_ja_confirmado( $cd_documento_protocolo )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			SELECT COUNT(*) as quantos
			  FROM projetos.documento_protocolo 
			 WHERE dt_exclusao IS NULL 
			   AND dt_indexacao IS NOT NULL 
			   AND cd_documento_protocolo = {cd_documento_protocolo}

		" );

		$db->setParameter( "{cd_documento_protocolo}", intval($cd_documento_protocolo) );

		$ret = $db->get();

		return ( $ret[0]['quantos']>0 );
	}
	
	public static function confirmar_indexacao( $cd_documento_protocolo )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			UPDATE projetos.documento_protocolo
			   SET dt_indexacao = CURRENT_TIMESTAMP
				   , cd_usuario_indexacao = {cd_usuario_indexacao}
			 WHERE cd_documento_protocolo = {cd_documento_protocolo}

		" );

		$db->setParameter( "{cd_documento_protocolo}", intval($cd_documento_protocolo) );
		$db->setParameter( "{cd_usuario_indexacao}", intval($_SESSION['Z']) );

		$ret = $db->execute();

		return $ret;
	}
	
	public static function carregar( $cd_documento_protocolo )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			SELECT *
			  FROM projetos.documento_protocolo 
			 WHERE cd_documento_protocolo = {cd_documento_protocolo}

		" );

		$db->setParameter( "{cd_documento_protocolo}", intval($cd_documento_protocolo) );

		$ret = $db->get();

		return $ret[0];
	}
}
?>