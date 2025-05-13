<?php
class DatabaseSuper extends Log
{
	// variaveis
	protected $sql;
	protected $host;
	protected $port;
	protected $base;
	protected $user;
	protected $pass;
	protected $attr;
	
	protected $erro;
	
	protected $db;

	function __construct()
	{
		/*
		// CONEXÃO DEFAULT DO PROJETO
		$this->host = DBConfig::$DB_HOST_DEFAULT;
		$this->port = DBConfig::$DB_PORT_DEFAULT;
		$this->base = DBConfig::$DB_BASE_DEFAULT;
		$this->user = DBConfig::$DB_USER_DEFAULT;
		$this->pass = DBConfig::$DB_PASS_DEFAULT;
		$this->attr = DBConfig::$DB_ATTR_DEFAULT;
		*/

		if( $_SERVER['SERVER_ADDR'] == '10.63.255.222' || $_SERVER['SERVER_ADDR'] == '10.63.255.194' || $_SERVER['SERVER_ADDR'] == '10.63.255.150' )
		{
			// CONEXÃO DEFAULT DO PROJETO
			$this->host = "10.63.255.222";
			$this->port = "5555";
			$this->base = "fundacaoweb";
			$this->user = "gerente";
			$this->pass = "";
			$this->attr = "";
		}
		elseif ($_SERVER['SERVER_ADDR'] == '10.63.255.235')
		{
			// CONEXÃO DEFAULT DO PROJETO
			$this->host = "10.63.255.235";
			$this->port = "5555";
			$this->base = "fundacaoweb";
			$this->user = "gerente";
			$this->pass = "";
			$this->attr = "";			
		}		
		elseif( $_SERVER['SERVER_ADDR'] == '10.63.255.5' || $_SERVER['SERVER_ADDR'] == '10.63.255.7' )
		{
			// CONEXÃO DEFAULT DO PROJETO
			$this->host = "10.63.255.5";
			$this->port = "5555";
			$this->base = "fundacaoweb";
			$this->user = "gerente";
			$this->pass = "";
			$this->attr = "";
		}		
	}

	// conexão
	function setHost($value)
	{
		$this->host= $value;
	}
	function setPort($value)
	{
		$this->port = $value;
	}
	function setBase($value)
	{
		$this->base = $value;
	}
	function setUser($value)
	{
		$this->user= $value;
	}
	function setPass($value)
	{
		$this->pass= $value;
	}
	function setAttr($value)
	{
		$this->attr = $value;
	}

	// query
	function setSQL($value)
	{
		$this->sql = $value;
	}
	function getSQL()
	{
		return $this->sql;
	}
}
?>