<?php
class Atendimento_reclamatoria_retorno_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT arr.cd_atendimento_reclamatoria_retorno, 
						   arr.observacao, 
						   TO_CHAR(arr.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
						   arr.cd_usuario_inclusao,
						   uc.nome AS usuario
                      FROM projetos.atendimento_reclamatoria_retorno arr
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = arr.cd_usuario_inclusao
					 WHERE arr.cd_atendimento_reclamatoria = {cd_atendimento_reclamatoria}
					 ORDER BY arr.dt_inclusao
		       ";
			   
		esc("{cd_atendimento_reclamatoria}", $args['cd_atendimento_reclamatoria'], $qr_sql, "int", FALSE);		

		#echo "<pre>$qr_sql</pre>";exit;
		
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}
	
	function carregar($cd)
	{
		if(intval($cd)>0 )
		{
			$sql = "
					SELECT arr.cd_atendimento_reclamatoria_retorno, 
						   arr.observacao,
						   arr.cd_atendimento_reclamatoria
					  FROM projetos.atendimento_reclamatoria_retorno arr		
				     WHERE arr.cd_atendimento_reclamatoria_retorno = {cd_atendimento_reclamatoria_retorno} 
				   ";
			esc("{cd_atendimento_reclamatoria_retorno}", intval($cd), $sql);
			$query = $this->db->query($sql);
			$row   = $query->row_array();
		}
		else
		{
			$row = Array();
		}

		return $row;
	}	
	
	function salvar($args)
	{
		if(intval($args["cd_atendimento_reclamatoria_retorno"]) == 0)
		{
			$new_id = intval($this->db->get_new_id("projetos.atendimento_reclamatoria_retorno", "cd_atendimento_reclamatoria_retorno"));
			
			$sql= "
					INSERT INTO projetos.atendimento_reclamatoria_retorno
					     (
						   cd_atendimento_reclamatoria_retorno,
						   cd_atendimento_reclamatoria, 
						   observacao, 
						   cd_usuario_inclusao
						 )
                    VALUES 
					     (
							{cd_atendimento_reclamatoria_retorno},
							{cd_atendimento_reclamatoria},
							".(trim($args["observacao"]) == "" ? "NULL" :"'{observacao}'").",
							{cd_usuario_inclusao}
						 );
			      ";
		}
		else if(intval($args["cd_atendimento_reclamatoria"]) > 0)
		{
			$new_id = $args["cd_atendimento_reclamatoria_retorno"];
			$sql= "
					UPDATE projetos.atendimento_reclamatoria_retorno
					   SET observacao = ".(trim($args["observacao"]) == "" ? "NULL" :"'{observacao}'")."
					 WHERE cd_atendimento_reclamatoria_retorno = {cd_atendimento_reclamatoria_retorno};
			      ";
		}

		esc("{cd_atendimento_reclamatoria_retorno}", $new_id, $sql, "int", FALSE);
		esc("{cd_atendimento_reclamatoria}", $args["cd_atendimento_reclamatoria"], $sql, "int", FALSE);
		esc("{observacao}", $args["observacao"], $sql, "str", FALSE);
		esc("{cd_usuario_inclusao}", $args["cd_usuario_inclusao"], $sql, "int", FALSE);
		
		#echo "<PRE>$sql</PRE>";exit;
		$this->db->query($sql);

		return $new_id;
	}
}
?>