<?php
class beneficio_inc_senge_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
		SELECT 
		
		cd_beneficio_inc_senge
		, to_char(dt_referencia,'YYYY') as ano_referencia
		, to_char(dt_referencia,'MM/YYYY') as mes_referencia
		, dt_referencia
		, TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao
		, cd_usuario_inclusao
		, TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao
		, cd_usuario_exclusao
		, cd_indicador_tabela
		, fl_media
        , observacao
		, nr_valor_1
		, nr_valor_2
		, nr_percentual_f
		, nr_meta 

		FROM indicador_plugin.beneficio_inc_senge 
		WHERE dt_exclusao IS NULL
		AND (
			fl_media='S' 
			OR cd_indicador_tabela={cd_indicador_tabela}
		)
		ORDER BY dt_referencia ASC
		";

		esc( "{cd_indicador_tabela}", $args['cd_indicador_tabela'], $sql );

		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
		$sql = " SELECT 

		cd_beneficio_inc_senge
		, to_char(dt_referencia,'YYYY') as ano_referencia
		, to_char(dt_referencia,'MM/YYYY') as mes_referencia
		, to_char(dt_referencia,'DD/MM/YYYY') as dt_referencia
		, TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao
		, cd_usuario_inclusao
		, TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao
		, cd_usuario_exclusao
		, cd_indicador_tabela
		, fl_media
        , observacao
		, nr_valor_1
		, nr_valor_2
		, nr_percentual_f
		, nr_meta 

		FROM indicador_plugin.beneficio_inc_senge ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_beneficio_inc_senge={cd_beneficio_inc_senge} ";
			esc( "{cd_beneficio_inc_senge}", intval($cd), $sql );
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
		if(intval($args['cd_beneficio_inc_senge'])==0)
		{
			$sql="
			INSERT INTO indicador_plugin.beneficio_inc_senge ( 

			dt_referencia 
			, dt_inclusao 
			, cd_usuario_inclusao 
			, cd_indicador_tabela
			, fl_media
            , observacao
			, nr_valor_1
			, nr_valor_2
			, nr_meta 

			) VALUES ( 

			TO_DATE('{dt_referencia}', 'DD/MM/YYYY') 
			, CURRENT_TIMESTAMP 
			, {cd_usuario_inclusao}
			, {cd_indicador_tabela} 
			, '{fl_media}'
            , '{observacao}'
			, {nr_valor_1}
			, {nr_valor_2}
			, {nr_meta}

			)
			";
		}
		else
		{
			$sql="
			UPDATE indicador_plugin.beneficio_inc_senge 
			SET 
			
			cd_beneficio_inc_senge = {cd_beneficio_inc_senge} 
			, dt_referencia = TO_DATE('{dt_referencia}', 'DD/MM/YYYY') 
			, cd_indicador_tabela = {cd_indicador_tabela} 
			, fl_media = '{fl_media}'
			, observacao = '{observacao}'
			, nr_valor_1 = {nr_valor_1} 
			, nr_valor_2 = {nr_valor_2} 
			, nr_meta = {nr_meta} 

			 WHERE 
			cd_beneficio_inc_senge = {cd_beneficio_inc_senge} 
			";
		}

		esc("{cd_beneficio_inc_senge}", $args["cd_beneficio_inc_senge"], $sql, "int", FALSE);
		esc("{dt_referencia}", $args["dt_referencia"], $sql, "str", FALSE);
		esc("{cd_usuario_inclusao}", $args["cd_usuario_inclusao"], $sql, "int", FALSE);
		esc("{cd_indicador_tabela}", $args["cd_indicador_tabela"], $sql, "int", FALSE);
		esc("{fl_media}", $args["fl_media"], $sql, "str", FALSE);
        esc("{observacao}", $args["observacao"], $sql, "str", FALSE);
		esc("{nr_valor_1}", $args["nr_valor_1"], $sql, "float", FALSE);
		esc("{nr_valor_2}", $args["nr_valor_2"], $sql, "float", FALSE);
		esc("{nr_meta}", $args["nr_meta"], $sql, "float", FALSE);


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
		UPDATE indicador_plugin.beneficio_inc_senge 
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
		WHERE md5(cd_beneficio_inc_senge::varchar)='{cd_beneficio_inc_senge}' 
		"; 
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_beneficio_inc_senge}', $id, $sql, 'str'); 

		$query=$this->db->query($sql); 
	}
}
?>