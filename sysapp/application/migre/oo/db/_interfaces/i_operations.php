<?php
interface i_operations
{
	public static function select($where=null);
	public static function insert($entidade);
	public static function update($entidade);
	public static function delete($entidade);
}
?>