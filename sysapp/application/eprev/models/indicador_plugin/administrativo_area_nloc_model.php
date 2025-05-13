<?php
class administrativo_area_nloc_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
            SELECT cd_administrativo_area_nloc,
                   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
                   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_referencia,
                   dt_referencia,
                   TO_CHAR(dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
                   cd_usuario_inclusao,
                   TO_CHAR(dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
                   cd_usuario_exclusao,
                   cd_indicador_tabela,
                   fl_media,
                   nr_valor_1,
                   nr_valor_2,
                   nr_percentual_f,
                   nr_meta,
                   observacao
		      FROM indicador_plugin.administrativo_area_nloc
		     WHERE dt_exclusao IS NULL
		       AND
                 (
			         fl_media='S'
			      OR cd_indicador_tabela=".intval($args['cd_indicador_tabela'])."
		         )
		    ORDER BY dt_referencia ASC
		";

		#esc( "{cd_indicador_tabela}", $args['cd_indicador_tabela'], $sql );

		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
        $row=array();

		$qr_sql = "
            SELECT cd_administrativo_area_nloc,
                   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
                   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_referencia,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   TO_CHAR(dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
                   cd_usuario_inclusao,
                   TO_CHAR(dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
                   cd_usuario_exclusao,
                   cd_indicador_tabela,
                   fl_media,
                   nr_valor_1,
                   nr_valor_2,
                   nr_percentual_f,
                   nr_meta,
                   observacao
	          FROM indicador_plugin.administrativo_area_nloc 
              ".(intval($cd) > 0 ? " WHERE cd_administrativo_area_nloc = ".intval($cd) : " LIMIT 1 ")."
              ";
		
		$ob_resul = $this->db->query($qr_sql);

		if(intval($cd) > 0)
		{
			$row = $ob_resul->row_array();
		}
		else
		{
			$ar_campo = $ob_resul->field_data();
			foreach($ar_campo as $campo)
			{
				$row[$campo->name] = '';
			}
		}

		return $row;
	}

	function salvar($args,&$msg=array())
	{
		if(intval($args['cd_administrativo_area_nloc'])==0)
		{
			$sql="
                INSERT INTO indicador_plugin.administrativo_area_nloc
                     (
                     dt_referencia,
                     dt_inclusao,
                     cd_usuario_inclusao ,
                     cd_indicador_tabela,
                     fl_media,
                     nr_valor_1,
                     nr_valor_2,
                     nr_meta,
                     observacao
			         )
                VALUES
                     (
			         TO_DATE('{dt_referencia}', 'DD/MM/YYYY') ,
                     CURRENT_TIMESTAMP ,
                     {cd_usuario_inclusao},
                     {cd_indicador_tabela} ,
                     '{fl_media}',
                     {nr_valor_1},
                     {nr_valor_2},
                     {nr_meta},
                     '{observacao}'
			         )
			";
		}
		else
		{
			$sql="
                UPDATE indicador_plugin.administrativo_area_nloc
                   SET cd_administrativo_area_nloc = {cd_administrativo_area_nloc} ,
                       dt_referencia                 = TO_DATE('{dt_referencia}', 'DD/MM/YYYY') ,
                       cd_indicador_tabela           = {cd_indicador_tabela} ,
                       fl_media                      = '{fl_media}',
                       nr_valor_1                    = {nr_valor_1} ,
                       nr_valor_2                    = {nr_valor_2} ,
                       nr_meta                       = {nr_meta},
                       observacao                    = '{observacao}'
			     WHERE cd_administrativo_area_nloc = {cd_administrativo_area_nloc}
			";
		}

		esc("{cd_administrativo_area_nloc}", $args["cd_administrativo_area_nloc"], $sql, "int", FALSE);
		esc("{dt_referencia}", $args["dt_referencia"], $sql, "str", FALSE);
		esc("{cd_usuario_inclusao}", $args["cd_usuario_inclusao"], $sql, "int", FALSE);
		esc("{cd_indicador_tabela}", $args["cd_indicador_tabela"], $sql, "int", FALSE);
		esc("{fl_media}", $args["fl_media"], $sql, "str", FALSE);

		esc("{nr_valor_1}", $args["nr_valor_1"], $sql, "float", FALSE);
		esc("{nr_valor_2}", $args["nr_valor_2"], $sql, "float", FALSE);
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
            UPDATE indicador_plugin.administrativo_area_nloc
		       SET dt_exclusao         = CURRENT_TIMESTAMP,
                   cd_usuario_exclusao = {cd_usuario_exclusao}
		     WHERE md5(cd_administrativo_area_nloc::varchar)='{cd_administrativo_area_nloc}'
		";
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int');
		esc('{cd_administrativo_area_nloc}', $id, $sql, 'str');

		$query=$this->db->query($sql);
	}
}
?>