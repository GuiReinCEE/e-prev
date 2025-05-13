<?php
class Interesse_municipio_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_interesse_municipio,
                   ds_nome,
                   ds_email,
                   ds_telefone,
                   TO_CHAR(dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento,
                   ds_objetivo,
                   ds_municipio,
                   ds_mensagem,
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
              FROM expansao.interesse_municipio;";

		return $this->db->query($qr_sql)->result_array();
	}

}