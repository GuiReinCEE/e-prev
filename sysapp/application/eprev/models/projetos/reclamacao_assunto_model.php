<?php
class reclamacao_assunto_model extends model
{
	function __construct()
    {
        parent::Model();

        CheckLogin();
    }

    public function listar()
    {
    	$qr_sql = "
    		SELECT cd_reclamacao_assunto,
                   ds_reclamacao_assunto,
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
    		  FROM projetos.reclamacao_assunto
    		 WHERE dt_exclusao IS NULL;";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_reclamacao_assunto)
    {
    	$qr_sql = "
    		SELECT cd_reclamacao_assunto,
                   ds_reclamacao_assunto
    		  FROM projetos.reclamacao_assunto
    		 WHERE dt_exclusao IS NULL
    		   AND cd_reclamacao_assunto = ".intval($cd_reclamacao_assunto).";";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.reclamacao_assunto
                 (
                    ds_reclamacao_assunto,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".(trim($args['ds_reclamacao_assunto']) != '' ? str_escape($args['ds_reclamacao_assunto']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";
        
        $this->db->query($qr_sql);
    }

    public function atualizar($cd_reclamacao_assunto, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.reclamacao_assunto
               SET ds_reclamacao_assunto  = ".(trim($args['ds_reclamacao_assunto']) != "" ? str_escape($args['ds_reclamacao_assunto']) : "DEFAULT").",
                   cd_usuario_alteracao   = ".intval($args['cd_usuario']).",
                   dt_alteracao           = CURRENT_TIMESTAMP
             WHERE cd_reclamacao_assunto  = ".intval($cd_reclamacao_assunto).";";

        $this->db->query($qr_sql);
    }

    public function excluir($cd_reclamacao_assunto, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.reclamacao_assunto
               SET cd_usuario_exclusao    = ".intval($cd_usuario).",
                   dt_exclusao            = CURRENT_TIMESTAMP
             WHERE cd_reclamacao_assunto  = ".intval($cd_reclamacao_assunto).";";

        $this->db->query($qr_sql);
    }

}