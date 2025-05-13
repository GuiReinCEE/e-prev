<?php
class habilitacoes_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
            SELECT cd_habilitacoes,
                   TO_CHAR(dt_referencia,'YYYY')     AS ano_referencia,
                   TO_CHAR(dt_referencia,'MM/YYYY')  AS mes_referencia,
                   TO_CHAR(dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
                   TO_CHAR(dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
                   dt_referencia,
                   cd_usuario_inclusao,
                   cd_usuario_exclusao,
                   cd_indicador_tabela,
                   fl_media,
                   nr_valor_1,
                   nr_valor_2,
                   nr_valor_3,
                   nr_valor_4,
                   nr_valor_5,
                   nr_valor_6,
                   nr_valor_7,
                   nr_valor_8,
                   nr_valor_9,
                   beneficio AS ds_beneficio,
                   nr_percentual_f,
                   nr_meta,
                   CASE WHEN beneficio = 'PE' THEN 'Pensão'
                        WHEN beneficio = 'AP' THEN 'Aposentadoria'
                        WHEN beneficio = 'AD' THEN 'Aux-Doença'
                        WHEN beneficio = 'PR' THEN 'Prorrogações'
                        ELSE ''
                   END AS beneficio
		      FROM indicador_atendimento.habilitacoes
		     WHERE dt_exclusao IS NULL
		       AND (
			       fl_media='S'
			    OR cd_indicador_tabela=".intval($args['cd_indicador_tabela'])."
		           )
		     ORDER BY dt_referencia ASC";

		$result = $this->db->query($qr_sql);
	}

	function carregar($cd)
	{
		$qr_sql = "
            SELECT cd_habilitacoes,
                   TO_CHAR(dt_referencia,'YYYY')       AS ano_referencia,
                   TO_CHAR(dt_referencia,'MM/YYYY')    AS mes_referencia,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   TO_CHAR(dt_inclusao,'DD/MM/YYYY')   AS dt_inclusao,
                   TO_CHAR(dt_exclusao,'DD/MM/YYYY')   AS dt_exclusao,
                   cd_usuario_inclusao,
                   cd_usuario_exclusao,
                   cd_indicador_tabela,
                   fl_media,
                   nr_valor_1,
                   nr_valor_2,
                   nr_valor_3,
                   nr_valor_4,
                   nr_valor_5,
                   nr_valor_6,
                   nr_valor_7,
                   nr_valor_8,
                   nr_valor_9,
                   beneficio,
                   nr_percentual_f,
                   nr_meta
		      FROM indicador_atendimento.habilitacoes ";

		$row=array();
		$query = $this->db->query( $qr_sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$qr_sql .= " WHERE cd_habilitacoes=".intval($cd);

			$query=$this->db->query($qr_sql);

			if($query->row_array())
			{
				$row=$query->row_array();
			}
		}

		return $row;
	}

	function salvar($args,&$msg=array())
	{
		if(intval($args['cd_habilitacoes'])==0)
		{
			$qr_sql="
                INSERT INTO indicador_atendimento.habilitacoes
                          (
                            dt_referencia,
                            dt_inclusao,
                            cd_usuario_inclusao,
                            cd_indicador_tabela,
                            fl_media,
                            nr_valor_1,
                            nr_valor_2,
                            nr_valor_3,
                            nr_valor_4,
                            nr_valor_5,
                            nr_valor_6,
                            nr_valor_7,
                            nr_valor_8,
                            nr_valor_9,
                            beneficio,
                            nr_percentual_f

                           )
                     VALUES
                           (
                            TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY'),
                            CURRENT_TIMESTAMP,
                            ".intval($args["cd_usuario_inclusao"]).",
                            ".intval($args["cd_indicador_tabela"]).",
                            '".$args["fl_media"]."',
                            ".intval($args["nr_valor_1"]).",
                            ".intval($args["nr_valor_2"]).",
                            ".intval($args["nr_valor_3"]).",
                            ".intval($args["nr_valor_4"]).",
                            ".intval($args["nr_valor_5"]).",
                            ".intval($args["nr_valor_6"]).",
                            ".intval($args["nr_valor_7"]).",
                            ".intval($args["nr_valor_8"]).",
                            ".intval($args["nr_valor_9"]).",
                            '".trim($args["beneficio"])."',
                            ".intval($args["nr_percentual_f"])."
                          )
                ";
		}
		else
		{
			$qr_sql="
                UPDATE indicador_atendimento.habilitacoes
		           SET cd_habilitacoes     = ".intval($args["cd_habilitacoes"]).",
                       dt_referencia       = TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY'),
                       cd_indicador_tabela = ".intval($args["cd_indicador_tabela"]).",
                       fl_media            = '".$args["fl_media"]."',
                       nr_valor_1          = ".intval($args["nr_valor_1"]).",
                       nr_valor_2          = ".intval($args["nr_valor_2"]).",
                       nr_valor_3          = ".intval($args["nr_valor_3"]).",
                       nr_valor_4          = ".intval($args["nr_valor_4"]).",
                       nr_valor_5          = ".intval($args["nr_valor_5"]).",
                       nr_valor_6          = ".intval($args["nr_valor_6"]).",
                       nr_valor_7          = ".intval($args["nr_valor_7"]).",
                       nr_valor_8          = ".intval($args["nr_valor_8"]).",
                       nr_valor_9          = ".intval($args["nr_valor_9"]).",
                       beneficio           = '".trim($args["beneficio"])."',
                       nr_percentual_f     = ".intval($args["nr_percentual_f"])."
			     WHERE cd_habilitacoes = ".intval($args["cd_habilitacoes"])."
			";
		}

		try
		{
			$query = $this->db->query($qr_sql);
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
		$qr_sql = "
            UPDATE indicador_atendimento.habilitacoes
		       SET dt_exclusao         = CURRENT_TIMESTAMP,
                   cd_usuario_exclusao = ".usuario_id()."
		     WHERE md5(cd_habilitacoes::varchar)='".$id."'
		"; 

		$query=$this->db->query($qr_sql);
	}
}
?>