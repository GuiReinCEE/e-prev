<?php
class DBType
{
	static public $POSTGRES = 0;
	static public $MYSQL = 1;
	static public $ORACLE = 2;
}

class DBFactory
{
	static function createObject( $dbtype = 0 )
	{
		switch ($type) {
			case DBType::$POSTGRES:
				$base = new postgres();
				$base->connect();
				return $base;
				break;
			case DBType::$MYSQL:
				$base = new mysql();
				$base->connect();
				return $base;
				break;
			case DBType::$ORACLE:
				$base = new oracle();
				$base->connect();
				return $base;
				break;
			default:
				$base = new postgres();
				$base->connect();
				return $base;
				break;
		}
	}
}
?>