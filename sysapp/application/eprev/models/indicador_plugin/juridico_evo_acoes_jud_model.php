<?php
class juridico_evo_acoes_jud_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$qr_sql = "
					SELECT i.cd_juridico_evo_acoes_jud,
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
		                   i.nr_percentual_f,
		                   i.nr_meta,
                           i.observacao,
						   CASE WHEN (SELECT MAX(i1.dt_referencia)
						                FROM indicador_plugin.juridico_evo_acoes_jud i1
					                   WHERE i1.dt_exclusao IS NULL) = i.dt_referencia
								THEN 'S'
								ELSE 'N'
						   END AS fl_editar
					  FROM indicador_plugin.juridico_evo_acoes_jud i
					 WHERE i.dt_exclusao IS NULL
		             ORDER BY i.dt_referencia ASC
		          ";
		$result = $this->db->query($qr_sql);
	}

	function carregar($cd)
	{
		$sql = " SELECT 

		cd_juridico_evo_acoes_jud
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
		, nr_percentual_f
		, nr_meta 

		FROM indicador_plugin.juridico_evo_acoes_jud ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_juridico_evo_acoes_jud={cd_juridico_evo_acoes_jud} ";
			esc( "{cd_juridico_evo_acoes_jud}", intval($cd), $sql );
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
		if(intval($args['cd_juridico_evo_acoes_jud'])==0)
		{
			$sql="
			INSERT INTO indicador_plugin.juridico_evo_acoes_jud (

			dt_referencia 
			, cd_usuario_inclusao 
			, cd_usuario_alteracao 
			, cd_indicador_tabela
			, fl_media
            , observacao
			, nr_valor_1
			, nr_valor_2
            , nr_valor_3
			, nr_meta 

			) VALUES ( 

			TO_DATE('{dt_referencia}', 'DD/MM/YYYY') 
			, {cd_usuario_inclusao}
			, {cd_usuario_inclusao}
			, {cd_indicador_tabela} 
			, '{fl_media}'
            , '{observacao}'
			, {nr_valor_1}
			, {nr_valor_2}
            , {nr_valor_3}
			, {nr_meta}

			)
			";
		}
		else
		{
			$sql="
			UPDATE indicador_plugin.juridico_evo_acoes_jud
			SET 
			
			cd_juridico_evo_acoes_jud = {cd_juridico_evo_acoes_jud}
			, dt_referencia = TO_DATE('{dt_referencia}', 'DD/MM/YYYY') 
			, cd_indicador_tabela = {cd_indicador_tabela} 
			, fl_media = '{fl_media}'
			, observacao = '{observacao}'
			, nr_valor_1 = {nr_valor_1} 
			, nr_valor_2 = {nr_valor_2}
            , nr_valor_3 = {nr_valor_3}
			, nr_meta = {nr_meta} 
		    , cd_usuario_alteracao = ".intval($args['cd_usuario_inclusao'])."
		    , dt_alteracao         = CURRENT_TIMESTAMP			

			 WHERE 
			cd_juridico_evo_acoes_jud = {cd_juridico_evo_acoes_jud}
			";
		}

		esc("{cd_juridico_evo_acoes_jud}", $args["cd_juridico_evo_acoes_jud"], $sql, "int", FALSE);
		esc("{dt_referencia}", $args["dt_referencia"], $sql, "str", FALSE);
		esc("{cd_usuario_inclusao}", $args["cd_usuario_inclusao"], $sql, "int", FALSE);
		esc("{cd_indicador_tabela}", $args["cd_indicador_tabela"], $sql, "int", FALSE);
		esc("{fl_media}", $args["fl_media"], $sql, "str", FALSE);

		esc("{nr_valor_1}", $args["nr_valor_1"], $sql, "float", FALSE);
		esc("{nr_valor_2}", $args["nr_valor_2"], $sql, "float", FALSE);
        esc("{nr_valor_3}", $args["nr_valor_3"], $sql, "float", FALSE);
		esc("{nr_meta}", $args["nr_meta"], $sql, "float", FALSE);
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
		UPDATE indicador_plugin.juridico_evo_acoes_jud
		   SET dt_exclusao         = CURRENT_TIMESTAMP,
		       cd_usuario_exclusao = ".intval(usuario_id())."
		 WHERE cd_juridico_evo_acoes_jud = ".intval($id).";"; 

		$query=$this->db->query($sql); 
	}
}
?>