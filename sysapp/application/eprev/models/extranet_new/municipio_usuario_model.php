<?php
class Municipio_usuario_model extends Model
{
	function __construct()
	{
		parent::model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
            SELECT u.cd_usuario,
                   u.ds_usuario,
                   u.ds_nome,
                   u.ds_email,
                   u.fl_troca_senha,
                   u.fl_interno,
                   u.cd_empresa,
                   p.cd_empresa || ' - ' || p.sigla AS ds_empresa,
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
              FROM extranet_new.usuario u
              JOIN patrocinadoras p
                ON p.cd_empresa = u.cd_empresa
             WHERE u.dt_exclusao IS NULL
               ".(trim($args['cd_empresa']) != '' ? "AND u.cd_empresa = ".intval($args['cd_empresa']) : "")."
               ".(trim($args['fl_interno']) != '' ? "AND u.fl_interno = '".trim($args['fl_interno'])."'" : "")."
             ORDER BY p.cd_empresa ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_usuario)
	{
		$qr_sql = "
			SELECT cd_usuario,
                   cd_empresa,
                   ds_usuario,
                   ds_nome,
                   ds_email,
                   fl_troca_senha,
                   fl_interno,
                   ds_senha
              FROM extranet_new.usuario
             WHERE cd_usuario = ".intval($cd_usuario).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_empresa()
    {
        $qr_sql = "
            SELECT p.cd_empresa AS value,
			       p.sigla AS text,
			       cd_plano
			  FROM patrocinadoras p
			  JOIN planos_patrocinadoras pp
			    ON pp.cd_empresa = p.cd_empresa
			 WHERE pp.cd_plano = 10
			 ORDER BY p.sigla;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO extranet_new.usuario
                 (
                   cd_empresa,
                   ds_usuario,
                   ds_nome,
                   ds_email,
                   ds_senha,
                   fl_troca_senha,
                   fl_interno,
                   cd_usuario_inclusao,
                   cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
                    ".(trim($args['ds_usuario']) != '' ? str_escape($args['ds_usuario']) : "DEFAULT").",
                    ".(trim($args['ds_nome']) != '' ? str_escape($args['ds_nome']) : "DEFAULT").",
                    ".(trim($args['ds_email']) != '' ? str_escape($args['ds_email']) : "DEFAULT").",
                    ".(trim($args['ds_senha']) != '' ? "'".trim($args['ds_senha'])."'" : "DEFAULT").",
                    ".(trim($args['fl_troca_senha']) != '' ? "'".trim($args['fl_troca_senha'])."'" : "DEFAULT").",
                    ".(trim($args['fl_interno']) != '' ? "'".trim($args['fl_interno'])."'" : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."    
                 );";

        $this->db->query($qr_sql); 
    }

    public function atualizar($cd_usuario, $args = array())
    {
        $qr_sql = "
          UPDATE extranet_new.usuario
             SET cd_empresa           = ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
                 ds_usuario           = ".(trim($args['ds_usuario']) != '' ? str_escape($args['ds_usuario']) : "DEFAULT").",
                 ds_nome              = ".(trim($args['ds_nome']) != '' ? str_escape($args['ds_nome']) : "DEFAULT").",
                 ds_email             = ".(trim($args['ds_email']) != '' ? str_escape($args['ds_email']) : "DEFAULT").",
                 ds_senha             = ".(trim($args['ds_senha']) != '' ? "'".trim($args['ds_senha'])."'" : "DEFAULT").",
                 fl_troca_senha       = ".(trim($args['fl_troca_senha']) != '' ? "'".trim($args['fl_troca_senha'])."'" : "DEFAULT").",
                 fl_interno           = ".(trim($args['fl_interno']) != '' ? "'".trim($args['fl_interno'])."'" : "DEFAULT").",
                 cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                 dt_alteracao         = CURRENT_TIMESTAMP
           WHERE cd_usuario = '".trim($cd_usuario)."';";

        $this->db->query($qr_sql);
    }

}