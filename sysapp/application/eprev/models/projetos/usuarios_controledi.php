<?php
class Usuarios_controledi extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function select_dropdown_1($divisao, $args=array())
	{
		if(!(array_key_exists('cd_usuario', $args)))
		{
			$args['cd_usuario'] = -99999;
		}
		elseif (!(intval($args['cd_usuario']) > -1))
		{
			$args['cd_usuario'] = -99999;
		}
		
		$qr_sql = "
					SELECT codigo AS value,
						   nome AS text
					  FROM projetos.usuarios_controledi
					 WHERE divisao = '".$divisao."'
					   AND (tipo NOT IN ('X') OR codigo = ".intval($args['cd_usuario']).")
					 ORDER BY nome
		          ";

		$ob_resul = $this->db->query($qr_sql);
		
		if ($ob_resul)
		{
			return $ob_resul->result_array();
		}
		else
		{
			return FALSE;
		}
	}
}
?>