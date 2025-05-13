<?php
class Rpp_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
		SELECT i.cd_rpp,
               TO_CHAR(i.dt_referencia,'MM/YYYY') as mes_referencia,
               i.nr_inpc,
               i.nr_indice_mes,
               i.nr_indice_ano,
               i.nr_inpc_12_meses,
               i.nr_wacc,
               i.nr_peso,
               i.cd_indicador_tabela,
               TO_CHAR(i.dt_inclusao,'DD/MM/YYYY') as dt_inclusao,
               u.nome as nome_usuario_inclusao,
               CASE WHEN (SELECT MAX(i1.dt_referencia)
						    FROM igp.rpp i1
					       WHERE (i1.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")) = i.dt_referencia
					THEN 'S'
					ELSE 'N'
			   END AS fl_editar
		  FROM igp.rpp i
		  JOIN projetos.usuarios_controledi u ON u.codigo=i.cd_usuario_inclusao
		WHERE (i.dt_exclusao is null)
		 ORDER BY i.dt_referencia
		";

		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
		$sql = "SELECT cd_rpp
			, to_char( dt_referencia , 'DD/MM/YYYY' ) as dt_referencia
			, nr_inpc
			, nr_indice_mes
			, nr_indice_ano
			, nr_wacc
			, nr_peso
			, cd_indicador_tabela
			, TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao
			, cd_usuario_inclusao
			FROM igp.rpp ";

		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_rpp={cd_rpp} ";
			esc( "{cd_rpp}", intval($cd), $sql );
			$query=$this->db->query($sql);

			if($query->row_array())
			{
				$row=$query->row_array();
			}
		}

		return $row;
	}

	function salvar($args, &$msg=array())
	{
		if(intval($args['cd_rpp'])==0)
		{
			$sql="
			INSERT INTO igp.rpp (  
			  dt_referencia 
			, nr_inpc
			, nr_indice_mes
			, nr_indice_ano
			, nr_wacc
			, nr_peso 
			, dt_inclusao 
			, cd_usuario_inclusao
			, cd_indicador_tabela
			) VALUES ( 
			  date_trunc( 'month', to_date('{dt_referencia}','DD/MM/YYYY') )
			, {nr_inpc}
			, {nr_indice_mes}
			, {nr_indice_ano}
			, {nr_wacc}
			, {nr_peso} 
			, CURRENT_TIMESTAMP 
			, {cd_usuario_inclusao} 
			, {cd_indicador_tabela} 
			)
			";
		}
		else
		{
			$sql="
			UPDATE igp.rpp SET
			 cd_rpp = {cd_rpp}
			, dt_referencia = date_trunc( 'month', to_date('{dt_referencia}','DD/MM/YYYY') )
			, nr_inpc = {nr_inpc}
			, nr_indice_mes = {nr_indice_mes}
			, nr_indice_ano = {nr_indice_ano}
			, nr_wacc = {nr_wacc}
			, nr_peso = {nr_peso}
			WHERE cd_rpp = {cd_rpp}
			";
		}

		esc("{cd_rpp}", $args["cd_rpp"], $sql, "int", false);
		esc("{dt_referencia}", $args["dt_referencia"], $sql, "str", false);
		esc("{nr_inpc}", $args["nr_inpc"], $sql, "float", false);
		esc("{nr_indice_mes}", $args["nr_indice_mes"], $sql, "float", false);
		esc("{nr_indice_ano}", $args["nr_indice_ano"], $sql, "float", false);
		esc("{nr_wacc}", $args["nr_wacc"], $sql, "float", false);
		esc("{nr_peso}", $args["nr_peso"], $sql, "float", false);
		esc("{cd_usuario_inclusao}", $args["cd_usuario_inclusao"], $sql, "int", false);
		esc("{cd_indicador_tabela}", $args["cd_indicador_tabela"], $sql, "int", false);

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
			UPDATE igp.rpp
			SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
			WHERE md5(cd_rpp::varchar)='{cd_rpp}' 
		";

		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int');
		esc('{cd_rpp}', $id, $sql, 'str');

		$query=$this->db->query($sql);
	}
}
?>