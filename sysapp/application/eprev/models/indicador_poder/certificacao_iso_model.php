<?php
class certificacao_iso_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT i.cd_certificacao_iso,
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
						   i.nr_meta,
						   i.nr_faixa,
						   i.fl_meta,
						   i.fl_direcao,
						   (SELECT i1.tp_analise
							  FROM indicador.indicador_tabela it
							  JOIN indicador.indicador i1
								ON i1.cd_indicador = it.cd_indicador
							 WHERE it.cd_indicador_tabela = i.cd_indicador_tabela) AS tp_analise						   
					  FROM indicador_poder.certificacao_iso i
					 WHERE i.dt_exclusao IS NULL
					   AND (i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
					 ORDER BY i.dt_referencia ASC
		          ";
		$result = $this->db->query($qr_sql);
	}

	function carregar($cd)
	{
		$sql = " 
            SELECT cd_certificacao_iso,
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
                   nr_valor_2
		      FROM indicador_poder.certificacao_iso ";

		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();

        foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_certificacao_iso=".intval($cd);
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
		if(intval($args['cd_certificacao_iso'])==0)
		{
			$sql="
			INSERT INTO indicador_poder.certificacao_iso
                      (
                        dt_referencia,
                        dt_inclusao,
                        cd_usuario_inclusao ,
                        cd_indicador_tabela,
                        fl_media,
                        nr_valor_1,
                        nr_valor_2
                      )
                      VALUES
                      (
                        TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY') ,
                        CURRENT_TIMESTAMP ,
                        ".intval($args["cd_usuario_inclusao"]).",
                        ".intval($args["cd_indicador_tabela"]).",
                        '".$args["fl_media"]."',
                        ".floatval($args["nr_valor_1"]).",
                        ".floatval($args["nr_valor_2"])."
                      )
			";
		}
		else
		{
			$sql="
			UPDATE indicador_poder.certificacao_iso
			   SET cd_certificacao_iso = ".intval($args["cd_certificacao_iso"]).",
                   dt_referencia           = TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY'),
                   cd_indicador_tabela     = ".intval($args["cd_indicador_tabela"]).",
                   fl_media                = '".$args["fl_media"]."',
                   nr_valor_1              = ".floatval($args["nr_valor_1"]).",
                   nr_valor_2              = ".floatval($args["nr_valor_2"])."
			 WHERE cd_certificacao_iso = ".intval($args["cd_certificacao_iso"])."
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
		UPDATE indicador_poder.certificacao_iso
		   SET dt_exclusao         = CURRENT_TIMESTAMP,
               cd_usuario_exclusao = ".intval(usuario_id())."
		 WHERE md5(cd_certificacao_iso::varchar)='".$id."'
		"; 

		$query=$this->db->query($sql); 
	}
}
?>