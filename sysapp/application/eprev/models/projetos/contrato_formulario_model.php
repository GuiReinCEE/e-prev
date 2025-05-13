<?php
class Contrato_formulario_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
		SELECT a.cd_contrato_formulario, a.ds_contrato_formulario, to_char(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') as dt_inclusao, u.nome as nome_usuario_inclusao
		FROM projetos.contrato_formulario a
		JOIN projetos.usuarios_controledi u ON u.codigo=a.cd_usuario_inclusao
		WHERE a.dt_exclusao IS NULL
		";

		// parse query ...
		
		// echo "<pre>$sql</pre>";

		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar( &$row )
	{
		$sql = " SELECT * FROM projetos.contrato_formulario ";
		
		$row['grupos'] = array();
		if( intval($row['cd_contrato_formulario'])>0 )
		{
			$sql .= " WHERE cd_contrato_formulario={cd_contrato_formulario} ";
			esc( "{cd_contrato_formulario}", intval($row['cd_contrato_formulario']), $sql );
			$query=$this->db->query($sql);
			$row=$query->row_array();

			$sql = "
			SELECT * 
			FROM projetos.contrato_formulario_grupo 
			WHERE cd_contrato_formulario={cd_contrato_formulario} AND dt_exclusao IS NULL 
			ORDER BY nr_ordem";
			esc( "{cd_contrato_formulario}", intval($row['cd_contrato_formulario']), $sql );

			$query = $this->db->query($sql);

			if($query)
			{
				$grupos = $query->result_array();
				for($i=0;$i<sizeof($grupos);$i++)
				{
					$sql = "
					SELECT * 
					FROM projetos.contrato_formulario_pergunta 
					WHERE dt_exclusao IS NULL AND cd_contrato_formulario_grupo={cd_contrato_formulario_grupo} 
					ORDER BY nr_ordem
					";
					esc( "{cd_contrato_formulario_grupo}", $grupos[$i]["cd_contrato_formulario_grupo"], $sql, "int" );
					$query = $this->db->query($sql);
					if($query) $perguntas=$query->result_array(); else $perguntas=array();

					$grupos[$i]['perguntas'] = $perguntas;
				}
				$row['grupos'] = $grupos;
			}
		}
		else
		{
			$query = $this->db->query( $sql . ' LIMIT 1 ' );
			$fields = $query->field_data();
			foreach( $fields as $field )
			{
				$row[$field->name] = '';
			}
		}
	}
}
