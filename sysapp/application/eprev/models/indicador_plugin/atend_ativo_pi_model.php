<?php
class atend_ativo_pi_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$qr_sql = "
					SELECT i.cd_atend_ativo_pi,
						   TO_CHAR(i.dt_referencia,'YYYY') as ano_referencia,
						   TO_CHAR(i.dt_referencia,'MM/YYYY') as mes_referencia,
						   i.dt_referencia,
						   TO_CHAR(i.dt_inclusao,'DD/MM/YYYY') as dt_inclusao,
						   i.cd_usuario_inclusao,
						   TO_CHAR(i.dt_exclusao,'DD/MM/YYYY') as dt_exclusao,
						   i.cd_usuario_exclusao,
						   i.cd_indicador_tabela,
						   i.fl_media,
						   i.observacao,
						   i.nr_valor_ceee,
						   i.nr_valor_aes,
						   i.nr_valor_cgtee,
						   i.nr_valor_crm,
						   CASE WHEN (SELECT MAX(i1.dt_referencia)
										FROM indicador_plugin.atend_callcenter_sem_fila_espera i1
									   WHERE (i1.fl_media = 'S' OR i1.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")) = i.dt_referencia
								THEN 'S'
								ELSE 'N'
							END AS fl_editar						   
					  FROM indicador_plugin.atend_ativo_pi i
					 WHERE i.dt_exclusao IS NULL
					   AND (i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")	
					 ORDER BY i.dt_referencia ASC
		          ";
		$result = $this->db->query($qr_sql);
	}

	function carregar($cd)
	{
		$sql = " SELECT 

		cd_atend_ativo_pi
		, TO_CHAR(dt_referencia,'YYYY') as ano_referencia
		, TO_CHAR(dt_referencia,'MM/YYYY') as mes_referencia
		, TO_CHAR(dt_referencia,'DD/MM/YYYY') as dt_referencia
		, TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao
		, cd_usuario_inclusao
		, TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao
		, cd_usuario_exclusao
		, cd_indicador_tabela
		, fl_media
        , observacao
		, nr_valor_ceee
		, nr_valor_aes
		, nr_valor_cgtee
		, nr_valor_crm

		FROM indicador_plugin.atend_ativo_pi ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_atend_ativo_pi={cd_atend_ativo_pi} ";
			esc( "{cd_atend_ativo_pi}", intval($cd), $sql );
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
		if(intval($args['cd_atend_ativo_pi'])==0)
		{
			$sql="
			INSERT INTO indicador_plugin.atend_ativo_pi ( 

			dt_referencia 
			, dt_inclusao 
			, cd_usuario_inclusao 
			, cd_indicador_tabela
			, fl_media
            , observacao
			, nr_valor_ceee
			, nr_valor_aes
			, nr_valor_cgtee
			, nr_valor_crm

			) VALUES ( 

			TO_DATE('{dt_referencia}', 'DD/MM/YYYY') 
			, CURRENT_TIMESTAMP 
			, {cd_usuario_inclusao}
			, {cd_indicador_tabela} 
			, '{fl_media}'
            , '{observacao}'
			, {nr_valor_ceee}
			, {nr_valor_aes}
			, {nr_valor_cgtee}
			, {nr_valor_crm}

			)
			";
		}
		else
		{
			$sql="
			UPDATE indicador_plugin.atend_ativo_pi 
			SET 
			
			cd_atend_ativo_pi = {cd_atend_ativo_pi} 
			, dt_referencia = TO_DATE('{dt_referencia}', 'DD/MM/YYYY') 
			, cd_indicador_tabela = {cd_indicador_tabela} 
			, fl_media = '{fl_media}'
			, observacao = '{observacao}'
			, nr_valor_ceee = {nr_valor_ceee} 
			, nr_valor_aes = {nr_valor_aes} 
			, nr_valor_cgtee = {nr_valor_cgtee} 
			, nr_valor_crm = {nr_valor_crm} 

			 WHERE 
			cd_atend_ativo_pi = {cd_atend_ativo_pi} 
			";
		}

		esc("{cd_atend_ativo_pi}", $args["cd_atend_ativo_pi"], $sql, "int", FALSE);
		esc("{dt_referencia}", $args["dt_referencia"], $sql, "str", FALSE);
		esc("{cd_usuario_inclusao}", $args["cd_usuario_inclusao"], $sql, "int", FALSE);
		esc("{cd_indicador_tabela}", $args["cd_indicador_tabela"], $sql, "int", FALSE);
		esc("{fl_media}", $args["fl_media"], $sql, "str", FALSE);
        esc("{observacao}", $args["observacao"], $sql, "str", FALSE);
		esc("{nr_valor_ceee}", $args["nr_valor_ceee"], $sql, "float", FALSE);
		esc("{nr_valor_aes}", $args["nr_valor_aes"], $sql, "float", FALSE);
		esc("{nr_valor_cgtee}", $args["nr_valor_cgtee"], $sql, "float", FALSE);
		esc("{nr_valor_crm}", $args["nr_valor_crm"], $sql, "float", FALSE);


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
		UPDATE indicador_plugin.atend_ativo_pi 
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
		WHERE md5(cd_atend_ativo_pi::varchar)='{cd_atend_ativo_pi}' 
		"; 
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_atend_ativo_pi}', $id, $sql, 'str'); 

		$query=$this->db->query($sql); 
	}
}
?>