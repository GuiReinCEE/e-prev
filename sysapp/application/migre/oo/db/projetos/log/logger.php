<?php
class logger
{
	public static function insert($tipo, $mensagem)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$local = $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
		$db->setSQL(
			" INSERT INTO projetos.log (tipo, local, descricao, dt_cadastro) 
				VALUES ('" . pg_escape_string($tipo) . "', '" . pg_escape_string($local) . "', '" . pg_escape_string("<pre>".$mensagem."</pre>") . "', CURRENT_TIMESTAMP); "
		);
		$db->execute();

		return true;
	}
}
?>