<?php
class usuario_ajax extends Controller
{
	function __construct()
	{
		parent::Controller();
	}

	function json_object()
	{
		$codigo = (int)$this->input->post('codigo', TRUE);
		
		$sql = "
			SELECT *
			FROM projetos.usuarios_controledi
			WHERE codigo=?
		";

		$query = $this->db->query( $sql, array((int)$codigo) );
		$ret = "";
		if($query)
		{
			$row = $query->row_array();
			if($row)
			{
				$ret = json_encode($row);
			}
		}

		echo $ret;
	}
}
?>