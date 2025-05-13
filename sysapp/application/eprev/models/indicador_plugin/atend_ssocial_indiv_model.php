<?php
class atend_ssocial_indiv_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT ia.cd_atend_ssocial_indiv,
		                   TO_CHAR(ia.dt_referencia,'YYYY') AS ano_referencia,
		                   TO_CHAR(ia.dt_referencia,'MM/YYYY') AS mes_referencia,
		                   ia.nr_valor_1,
		                   ia.fl_media,
		                   ia.cd_indicador_tabela,
                           ia.observacao
		              FROM indicador_plugin.atend_ssocial_indiv ia
		             WHERE ia.dt_exclusao IS NULL
		               AND (ia.fl_media = 'S' OR ia.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
		               AND DATE_TRUNC('month', ia.dt_referencia) < DATE_TRUNC('month', CURRENT_TIMESTAMP)
		             ORDER BY ia.dt_referencia ASC
		          ";
		$result = $this->db->query($qr_sql);		
	}

	function carregar($cd)
	{
		$sql = " SELECT 

		cd_atend_ssocial_indiv
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

		FROM indicador_plugin.atend_ssocial_indiv ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_atend_ssocial_indiv={cd_atend_ssocial_indiv} ";
			esc( "{cd_atend_ssocial_indiv}", intval($cd), $sql );
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
		if(intval($args['cd_atend_ssocial_indiv'])==0)
		{
			$sql="
			INSERT INTO indicador_plugin.atend_ssocial_indiv ( 

			dt_referencia 
			, dt_inclusao 
			, cd_usuario_inclusao 
			, cd_indicador_tabela
			, fl_media
            , observacao
			, nr_valor_1

			) VALUES ( 

			TO_DATE('{dt_referencia}', 'DD/MM/YYYY') 
			, CURRENT_TIMESTAMP 
			, {cd_usuario_inclusao}
			, {cd_indicador_tabela} 
			, '{fl_media}'
            , '{observacao}'
			, {nr_valor_1}

			)
			";
		}
		else
		{
			$sql="
			UPDATE indicador_plugin.atend_ssocial_indiv 
			SET 
			
			cd_atend_ssocial_indiv = {cd_atend_ssocial_indiv} 
			, dt_referencia = TO_DATE('{dt_referencia}', 'DD/MM/YYYY') 
			, cd_indicador_tabela = {cd_indicador_tabela} 
			, fl_media = '{fl_media}'
			, observacao = '{observacao}'
			, nr_valor_1 = {nr_valor_1} 

			 WHERE 
			cd_atend_ssocial_indiv = {cd_atend_ssocial_indiv} 
			";
		}

		esc("{cd_atend_ssocial_indiv}", $args["cd_atend_ssocial_indiv"], $sql, "int", FALSE);
		esc("{dt_referencia}", $args["dt_referencia"], $sql, "str", FALSE);
		esc("{cd_usuario_inclusao}", $args["cd_usuario_inclusao"], $sql, "int", FALSE);
		esc("{cd_indicador_tabela}", $args["cd_indicador_tabela"], $sql, "int", FALSE);
		esc("{fl_media}", $args["fl_media"], $sql, "str", FALSE);
        esc("{observacao}", $args["observacao"], $sql, "str", FALSE);
		esc("{nr_valor_1}", $args["nr_valor_1"], $sql, "float", FALSE);


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
		UPDATE indicador_plugin.atend_ssocial_indiv 
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
		WHERE md5(cd_atend_ssocial_indiv::varchar)='{cd_atend_ssocial_indiv}' 
		"; 
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_atend_ssocial_indiv}', $id, $sql, 'str'); 

		$query=$this->db->query($sql); 
	}
}
?>