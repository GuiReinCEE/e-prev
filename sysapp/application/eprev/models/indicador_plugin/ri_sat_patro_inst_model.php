<?php
class ri_sat_patro_inst_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$qr_sql = "
					SELECT i.cd_ri_sat_patro_inst,
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
		                   i.nr_percentual_f,
		                   i.nr_meta,
                           i.observacao,
						   CASE WHEN (SELECT MAX(i1.dt_referencia)
						                FROM indicador_plugin.ri_sat_patro_inst i1
					                   WHERE i1.dt_exclusao IS NULL) = i.dt_referencia
								THEN 'S'
								ELSE 'N'
						   END AS fl_editar,
						   i.nr_nota
					  FROM indicador_plugin.ri_sat_patro_inst i
					 WHERE i.dt_exclusao IS NULL
		             ORDER BY i.dt_referencia ASC
		          ";
		$result = $this->db->query($qr_sql);
	}

	function carregar($cd)
	{
		$sql = " SELECT 

		cd_ri_sat_patro_inst
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
		, nr_nota
		FROM indicador_plugin.ri_sat_patro_inst ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_ri_sat_patro_inst={cd_ri_sat_patro_inst} ";
			esc( "{cd_ri_sat_patro_inst}", intval($cd), $sql );
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
		if(intval($args['cd_ri_sat_patro_inst'])==0)
		{
			$sql="
			INSERT INTO indicador_plugin.ri_sat_patro_inst (

			dt_referencia 
			, dt_inclusao 
			, cd_usuario_inclusao 
			, cd_indicador_tabela
			, fl_media
            , observacao
			, nr_valor_1
			, nr_valor_2
			, nr_meta 
			, nr_nota
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
			, {nr_nota}

			)
			";
		}
		else
		{
			$sql="
			UPDATE indicador_plugin.ri_sat_patro_inst
			SET 
			
			cd_ri_sat_patro_inst = {cd_ri_sat_patro_inst}
			, dt_referencia = TO_DATE('{dt_referencia}', 'DD/MM/YYYY') 
			, cd_indicador_tabela = {cd_indicador_tabela} 
			, fl_media = '{fl_media}'
			, observacao = '{observacao}'
			, nr_valor_1 = {nr_valor_1} 
			, nr_valor_2 = {nr_valor_2} 
			, nr_meta = {nr_meta} 
			, nr_nota = {nr_nota} 

			 WHERE 
			cd_ri_sat_patro_inst = {cd_ri_sat_patro_inst}
			";
		}

		esc("{cd_ri_sat_patro_inst}", $args["cd_ri_sat_patro_inst"], $sql, "int", FALSE);
		esc("{dt_referencia}", $args["dt_referencia"], $sql, "str", FALSE);
		esc("{cd_usuario_inclusao}", $args["cd_usuario_inclusao"], $sql, "int", FALSE);
		esc("{cd_indicador_tabela}", $args["cd_indicador_tabela"], $sql, "int", FALSE);
		esc("{fl_media}", $args["fl_media"], $sql, "str", FALSE);
        esc("{observacao}", $args["observacao"], $sql, "str", FALSE);
		esc("{nr_valor_1}", $args["nr_valor_1"], $sql, "float", FALSE);
		esc("{nr_valor_2}", $args["nr_valor_2"], $sql, "float", FALSE);
		esc("{nr_meta}", $args["nr_meta"], $sql, "float", FALSE);
		esc("{nr_nota}", $args["nr_nota"], $sql, "float", FALSE);


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
		UPDATE indicador_plugin.ri_sat_patro_inst
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
		WHERE md5(cd_ri_sat_patro_inst::varchar)='{cd_ri_sat_patro_inst}'
		"; 
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_ri_sat_patro_inst}', $id, $sql, 'str');

		$query=$this->db->query($sql); 
	}
}
?>