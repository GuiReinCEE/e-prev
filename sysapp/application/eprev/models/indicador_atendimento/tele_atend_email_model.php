<?php
class tele_atend_email_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
            SELECT cd_tele_atend_email,
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
                   observacao,
                   nr_meta
		      FROM indicador_atendimento.tele_atend_email
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
            SELECT cd_tele_atend_email,
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
                   observacao,
                   nr_meta
		      FROM indicador_atendimento.tele_atend_email ";

		$row=array();
		$query = $this->db->query( $qr_sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$qr_sql .= " WHERE cd_tele_atend_email=".intval($cd);

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
		if(intval($args['cd_tele_atend_email'])==0)
		{
			$qr_sql="
                INSERT INTO indicador_atendimento.tele_atend_email
                          (
                            dt_referencia,
                            dt_inclusao,
                            cd_usuario_inclusao,
                            cd_indicador_tabela,
                            fl_media,
                            nr_valor_1,
                            observacao

                           )
                     VALUES
                           (
                            TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY'),
                            CURRENT_TIMESTAMP,
                            ".intval($args["cd_usuario_inclusao"]).",
                            ".intval($args["cd_indicador_tabela"]).",
                            '".$args["fl_media"]."',
                            ".intval($args["nr_valor_1"]).",
                            '".trim($args["observacao"])."'
                          )
                ";
		}
		else
		{
			$qr_sql="
                UPDATE indicador_atendimento.tele_atend_email
		           SET cd_tele_atend_email     = ".intval($args["cd_tele_atend_email"]).",
                       dt_referencia       = TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY'),
                       cd_indicador_tabela = ".intval($args["cd_indicador_tabela"]).",
                       fl_media            = '".$args["fl_media"]."',
                       nr_valor_1          = ".intval($args["nr_valor_1"]).",
                       observacao          = '".trim($args["observacao"])."'
			     WHERE cd_tele_atend_email = ".intval($args["cd_tele_atend_email"])."
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
            UPDATE indicador_atendimento.tele_atend_email
		       SET dt_exclusao         = CURRENT_TIMESTAMP,
                   cd_usuario_exclusao = ".usuario_id()."
		     WHERE md5(cd_tele_atend_email::varchar)='".$id."'
		"; 

		$query=$this->db->query($qr_sql);
	}
}
?>