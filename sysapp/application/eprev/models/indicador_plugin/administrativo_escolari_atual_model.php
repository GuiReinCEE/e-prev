<?php
class administrativo_escolari_atual_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
					SELECT i.cd_administrativo_escolari_atual,
		                   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
		                   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
		                   i.dt_referencia,
		                   TO_CHAR(i.dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
		                   i.cd_usuario_inclusao,
		                   TO_CHAR(i.dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
		                   i.cd_usuario_exclusao,
		                   i.cd_indicador_tabela,
		                   i.fl_media,
		                   i.nr_valor_1,
		                   i.nr_valor_2,
                           i.nr_valor_3,
                           i.nr_valor_4,
                           i.nr_valor_5,
                           i.nr_valor_6,
                           i.nr_valor_7,
                           i.nr_valor_8,
                           i.nr_valor_9,
		                   i.nr_percentual_f,
		                   i.nr_meta,
                           i.observacao,
						   CASE WHEN (SELECT MAX(i1.dt_referencia)
						                FROM indicador_plugin.administrativo_escolari_atual i1
					                   WHERE i1.dt_exclusao IS NULL) = i.dt_referencia
								THEN 'S'
								ELSE 'N'
						   END AS fl_editar
					  FROM indicador_plugin.administrativo_escolari_atual i
					 WHERE i.dt_exclusao IS NULL
		             ORDER BY i.dt_referencia ASC
		          ";

		esc( "{cd_indicador_tabela}", $args['cd_indicador_tabela'], $sql );

		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
		$sql = " SELECT 

		cd_administrativo_escolari_atual
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
        , nr_valor_3
        , nr_valor_4
        , nr_valor_5
        , nr_valor_6
        , nr_valor_7
        , nr_valor_8
        , nr_valor_9
		, nr_percentual_f

		FROM indicador_plugin.administrativo_escolari_atual ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_administrativo_escolari_atual={cd_administrativo_escolari_atual} ";
			esc( "{cd_administrativo_escolari_atual}", intval($cd), $sql );
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
		if(intval($args['cd_administrativo_escolari_atual'])==0)
		{
			$sql="
			INSERT INTO indicador_plugin.administrativo_escolari_atual (

			dt_referencia 
			, dt_inclusao 
			, cd_usuario_inclusao 
			, cd_indicador_tabela
			, fl_media
            , observacao
			, nr_valor_1
			, nr_valor_2
            , nr_valor_3
            , nr_valor_4
            , nr_valor_5
            , nr_valor_6
            , nr_valor_7
            , nr_valor_8
            , nr_valor_9

			) VALUES ( 

			TO_DATE('{dt_referencia}', 'DD/MM/YYYY') 
			, CURRENT_TIMESTAMP 
			, {cd_usuario_inclusao}
			, {cd_indicador_tabela} 
			, '{fl_media}'
            , '{observacao}'
			, {nr_valor_1}
			, {nr_valor_2}
            , {nr_valor_3}
            , {nr_valor_4}
            , {nr_valor_5}
            , {nr_valor_6}
            , {nr_valor_7}
            , {nr_valor_8}
            , {nr_valor_9}

			)
			";
		}
		else
		{
			$sql="
			UPDATE indicador_plugin.administrativo_escolari_atual
			SET 
			
			cd_administrativo_escolari_atual = {cd_administrativo_escolari_atual}
			, dt_referencia = TO_DATE('{dt_referencia}', 'DD/MM/YYYY') 
			, cd_indicador_tabela = {cd_indicador_tabela} 
			, fl_media = '{fl_media}'
            , observacao = '{observacao}'
			, nr_valor_1 = {nr_valor_1} 
			, nr_valor_2 = {nr_valor_2}
            , nr_valor_3 = {nr_valor_3}
            , nr_valor_4 = {nr_valor_4}
            , nr_valor_5 = {nr_valor_5}
            , nr_valor_6 = {nr_valor_6}
            , nr_valor_7 = {nr_valor_7}
            , nr_valor_8 = {nr_valor_8}
            , nr_valor_9 = {nr_valor_9}

			 WHERE 
			cd_administrativo_escolari_atual = {cd_administrativo_escolari_atual}
			";
		}

		esc("{cd_administrativo_escolari_atual}", $args["cd_administrativo_escolari_atual"], $sql, "int", FALSE);
		esc("{dt_referencia}", $args["dt_referencia"], $sql, "str", FALSE);
		esc("{cd_usuario_inclusao}", $args["cd_usuario_inclusao"], $sql, "int", FALSE);
		esc("{cd_indicador_tabela}", $args["cd_indicador_tabela"], $sql, "int", FALSE);
		esc("{fl_media}", $args["fl_media"], $sql, "str", FALSE);

		esc("{nr_valor_1}", $args["nr_valor_1"], $sql, "float", FALSE);
		esc("{nr_valor_2}", $args["nr_valor_2"], $sql, "float", FALSE);
        esc("{nr_valor_3}", $args["nr_valor_3"], $sql, "float", FALSE);
        esc("{nr_valor_4}", $args["nr_valor_4"], $sql, "float", FALSE);
        esc("{nr_valor_5}", $args["nr_valor_5"], $sql, "float", FALSE);
        esc("{nr_valor_6}", $args["nr_valor_6"], $sql, "float", FALSE);
        esc("{nr_valor_7}", $args["nr_valor_7"], $sql, "float", FALSE);
        esc("{nr_valor_8}", $args["nr_valor_8"], $sql, "float", FALSE);
        esc("{nr_valor_9}", $args["nr_valor_9"], $sql, "float", FALSE);
        esc("{observacao}", $args["observacao"], $sql, "str", FALSE);

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
		UPDATE indicador_plugin.administrativo_escolari_atual
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
		WHERE md5(cd_administrativo_escolari_atual::varchar)='{cd_administrativo_escolari_atual}'
		"; 
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_administrativo_escolari_atual}', $id, $sql, 'str');

		$query=$this->db->query($sql); 
	}
}
?>