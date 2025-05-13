<?php
class administrativo_total_digitalizado_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$sql = "
            SELECT cd_administrativo_total_digitalizado,
                   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
                   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_referencia,
                   dt_referencia,
                   TO_CHAR(dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
                   cd_usuario_inclusao,
                   TO_CHAR(dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
                   cd_usuario_exclusao,
                   cd_indicador_tabela,
                   fl_media,
                   observacao,
                   nr_valor_1,
                   nr_valor_2,
                   nr_valor_3,
                   nr_valor_4, 
                   nr_valor_5, 
                   nr_valor_6, 
                   nr_valor_7, 
                   nr_valor_8, 
                   nr_valor_9, 
                   nr_valor_10, 
                   nr_valor_11, 
                   nr_valor_12, 
                   nr_valor_13, 
                   nr_percentual_f
		      FROM indicador_plugin.administrativo_total_digitalizado
		     WHERE dt_exclusao IS NULL
		       AND (
			            fl_media='S'
			         OR cd_indicador_tabela=".intval($args['cd_indicador_tabela'])."
		           )
		     ORDER BY dt_referencia ASC
		";

		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
		$sql = "
            SELECT cd_administrativo_total_digitalizado,
                   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
                   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_referencia,
		           TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   TO_CHAR(dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
                   cd_usuario_inclusao,
                   TO_CHAR(dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
                   cd_usuario_exclusao,
                   cd_indicador_tabela,
                   fl_media,
                   observacao,
                   nr_valor_1,
                   nr_valor_2,
                   nr_valor_3,
                   nr_valor_4,
                   nr_valor_5,
                   nr_valor_6,
                   nr_valor_7,
                   nr_valor_8,
                   nr_valor_9,
                   nr_valor_10,
                   nr_valor_11,
                   nr_valor_12,
                   nr_valor_13,
                   nr_percentual_f
		      FROM indicador_plugin.administrativo_total_digitalizado
		";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_administrativo_total_digitalizado= ".intval($cd);

			$query=$this->db->query($sql);

			if($query->row_array())
			{
				$row=$query->row_array();
			}
		}

		return $row;
	}

	function salvar($args,&$msg=array())
	{
		if(intval($args['cd_administrativo_total_digitalizado'])==0)
		{
			$sql="
                INSERT INTO indicador_plugin.administrativo_total_digitalizado
                         (
                          dt_referencia,
                          dt_inclusao,
                          cd_usuario_inclusao,
                          cd_indicador_tabela,
                          fl_media,
                          observacao,
                          nr_valor_1,
                          nr_valor_2,
                          nr_valor_3,
                          nr_valor_4,
                          nr_valor_5,
                          nr_valor_6,
                          nr_valor_7,
                          nr_valor_8,
                          nr_valor_9,
                          nr_valor_10,
                          nr_valor_11,
                          nr_valor_12,
                          nr_valor_13
                         )
                    VALUES
                         (
                          TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY'),
                          CURRENT_TIMESTAMP,
                          ".intval($args["cd_usuario_inclusao"]).",
                          ".intval($args["cd_indicador_tabela"]).",
                          '".$args["fl_media"]."',
                          '".$args["observacao"]."',
                          ".intval($args["nr_valor_1"]).",
                          ".intval($args["nr_valor_2"]).",
                          ".intval($args["nr_valor_3"]).",
                          ".intval($args["nr_valor_4"]).",
                          ".intval($args["nr_valor_5"]).",
                          ".intval($args["nr_valor_6"]).",
                          ".intval($args["nr_valor_7"]).",
                          ".intval($args["nr_valor_8"]).",
                          ".intval($args["nr_valor_9"]).",
                          ".intval($args["nr_valor_10"]).",
                          ".intval($args["nr_valor_11"]).",
                          ".intval($args["nr_valor_12"]).",
                          ".intval($args["nr_valor_13"])."
                         )
			";
		}
		else
		{
			$sql="
                UPDATE indicador_plugin.administrativo_total_digitalizado
			       SET cd_administrativo_total_digitalizado = ".intval($args["cd_administrativo_total_digitalizado"]).",
                       dt_referencia = TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY'),
                       cd_indicador_tabela = ".intval($args["cd_indicador_tabela"]).",
                       fl_media = '".$args["fl_media"]."',
                       observacao = '".$args["observacao"]."',
                       nr_valor_1 = ".intval($args["nr_valor_1"]).",
                       nr_valor_2 = ".intval($args["nr_valor_2"]).",
                       nr_valor_3 = ".intval($args["nr_valor_3"]).",
                       nr_valor_4 = ".intval($args["nr_valor_4"]).",
                       nr_valor_5 = ".intval($args["nr_valor_5"]).",
                       nr_valor_6 = ".intval($args["nr_valor_6"]).",
                       nr_valor_7 = ".intval($args["nr_valor_7"]).",
                       nr_valor_8 = ".intval($args["nr_valor_8"]).",
                       nr_valor_9 = ".intval($args["nr_valor_9"]).",
                       nr_valor_10 = ".intval($args["nr_valor_10"]).",
                       nr_valor_11 = ".intval($args["nr_valor_11"]).",
                       nr_valor_12 = ".intval($args["nr_valor_12"]).",
                       nr_valor_13 = ".intval($args["nr_valor_13"])."
			     WHERE cd_administrativo_total_digitalizado = ".intval($args["cd_administrativo_total_digitalizado"])."
			";
		}

		try
		{
			$query = $this->db->query($sql);
			return true;
		}
		catch(Exception $e)
		{
			$msg[]=$e->getMessage();
			return false;
		}
	}

	function excluir($id)
	{
		$sql = "
            UPDATE indicador_plugin.administrativo_total_digitalizado
		       SET dt_exclusao         = CURRENT_TIMESTAMP,
                   cd_usuario_exclusao = ".usuario_id()."
		     WHERE md5(cd_administrativo_total_digitalizado::varchar)='".$id."'
		"; 

		$query=$this->db->query($sql); 
	}
}
?>