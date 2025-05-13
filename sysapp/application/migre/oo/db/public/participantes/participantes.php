<?php
class participantes extends Service
{
	static public $entity;
	
	static function get_by_key($emp, $re, $seq)
	{
		$where = array( 'cd_empresa'=>$emp, 'cd_registro_empregado'=>$re, 'seq_dependencia'=>$seq );
		return t_participantes::select_custom($where);
	}
}
?>