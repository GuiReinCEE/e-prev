<?php
class administrativo_doc_digitalizados_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$sql = " 
             SELECT cd_administrativo_doc_digitalizados,
                    TO_CHAR(dt_referencia,'YYYY') as ano_referencia,
                    TO_CHAR(dt_referencia,'MM/YYYY') as mes_referencia,
                    TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao,
                    TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao,
                    dt_referencia,
                    cd_usuario_inclusao,
                    cd_usuario_exclusao,
                    cd_indicador_tabela,
                    fl_media,
                    observacao,
                    nr_valor_1
		       FROM indicador_plugin.administrativo_doc_digitalizados
		      WHERE dt_exclusao IS NULL
		       AND (
			            fl_media='S'
			         OR cd_indicador_tabela=".intval($args['cd_indicador_tabela'])."
		           )
		     ORDER BY dt_referencia ASC";

		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
		$sql = " 
            SELECT cd_administrativo_doc_digitalizados,
                   TO_CHAR(dt_referencia,'YYYY') as ano_referencia,
                   TO_CHAR(dt_referencia,'MM/YYYY') as mes_referencia,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') as dt_referencia,
                   TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao,
                   TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao,
                   cd_usuario_inclusao,
                   cd_usuario_exclusao,
                   cd_indicador_tabela,
                   fl_media,
                   observacao,
                   nr_valor_1
		      FROM indicador_plugin.administrativo_doc_digitalizados ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_administrativo_doc_digitalizados=".intval($cd);
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
		if(intval($args['cd_administrativo_doc_digitalizados'])==0)
		{
			$sql="
                INSERT INTO indicador_plugin.administrativo_doc_digitalizados
                          (
                           dt_referencia,
                           dt_inclusao,
                           cd_usuario_inclusao,
                           cd_indicador_tabela,
                           fl_media,
                           observacao,
                           nr_valor_1
                          )
                     VALUES
                          (
                           TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY'),
                           CURRENT_TIMESTAMP,
                           ".intval($args["cd_usuario_inclusao"]).",
                           ".intval($args["cd_indicador_tabela"]).",
                           '".$args["fl_media"]."',
                           '".trim($args["observacao"])."',
                           ".intval($args["nr_valor_1"])."
                          )
			";
		}
		else
		{
			$sql="
                UPDATE indicador_plugin.administrativo_doc_digitalizados
			       SET cd_administrativo_doc_digitalizados = ".intval($args["cd_administrativo_doc_digitalizados"]).",
                       dt_referencia = TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY'),
                       cd_indicador_tabela = ".intval($args["cd_indicador_tabela"]).",
                       fl_media = '".$args["fl_media"]."',
                       observacao = '".trim($args["observacao"])."',
                       nr_valor_1 = ".intval($args["nr_valor_1"])."
			     WHERE cd_administrativo_doc_digitalizados = ".intval($args["cd_administrativo_doc_digitalizados"])."
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
		$sql = " UPDATE indicador_plugin.administrativo_doc_digitalizados
		            SET dt_exclusao        = CURRENT_TIMESTAMP,
                       cd_usuario_exclusao = ".intval(usuario_id())."
		          WHERE md5(cd_administrativo_doc_digitalizados::varchar)='".$id."'
		"; 

		$query=$this->db->query($sql); 
	}
}
?>