<?php
class Lista_negra_divulgacao_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function listar($args = array())
    {
    	$qr_sql = "
    	  	SELECT lnd.cd_lista_negra_divulgacao,
    	  	       lnd.ds_lista_negra_divulgacao,
                   TO_CHAR(lnd.dt_inclusao, 'DD/MM/YYYY  HH24:MI:SS') AS dt_inclusao
              FROM projetos.lista_negra_divulgacao lnd
    	     WHERE lnd.dt_exclusao IS NULL
               ".(trim($args['ds_lista_negra_divulgacao']) != '' ? "AND funcoes.remove_acento(UPPER(lnd.ds_lista_negra_divulgacao)) LIKE funcoes.remove_acento(UPPER('%".utf8_decode(trim($args['ds_lista_negra_divulgacao']))."%'))" : '').";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function listar_email($cd_lista_negra_divulgacao)
    {
        $qr_sql = "
            SELECT lnd.cd_lista_negra_divulgacao_email,
                   lnd.cd_lista_negra_divulgacao,
                   lnd.ds_lista_negra_divulgacao_email,
                   TO_CHAR(lnd.dt_inclusao, 'DD/MM/YYYY  HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
              FROM projetos.lista_negra_divulgacao_email lnd
             WHERE lnd.dt_exclusao IS NULL
               AND lnd.cd_lista_negra_divulgacao = ".intval($cd_lista_negra_divulgacao)."";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_lista_negra_divulgacao)
    {
        $qr_sql = "
            SELECT lnd.cd_lista_negra_divulgacao,
                   lnd.ds_lista_negra_divulgacao,
                   TO_CHAR(lnd.dt_inclusao, 'DD/MM/YYYY  HH24:MI:SS') AS dt_inclusao
              FROM projetos.lista_negra_divulgacao lnd
             WHERE lnd.cd_lista_negra_divulgacao = ".intval($cd_lista_negra_divulgacao).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function carrega_email($cd_lista_negra_divulgacao_email)
    {
        $qr_sql = "
            SELECT lnd.cd_lista_negra_divulgacao_email,
                   lnd.ds_lista_negra_divulgacao_email,
                   TO_CHAR(lnd.dt_inclusao, 'DD/MM/YYYY  HH24:MI:SS') AS dt_inclusao
              FROM projetos.lista_negra_divulgacao_email lnd
             WHERE lnd.dt_exclusao IS NULL
               AND lnd.cd_lista_negra_divulgacao_email = ".intval($cd_lista_negra_divulgacao_email).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $cd_lista_negra_divulgacao = intval($this->db->get_new_id('projetos.lista_negra_divulgacao', 'cd_lista_negra_divulgacao'));

        $qr_sql = "
           INSERT INTO projetos.lista_negra_divulgacao
                (
                    cd_lista_negra_divulgacao,
                    ds_lista_negra_divulgacao, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                )
          VALUES 
                (
                    ".intval($cd_lista_negra_divulgacao).",  
                    ".(trim($args['ds_lista_negra_divulgacao']) != '' ? str_escape($args['ds_lista_negra_divulgacao']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."    
                );";

        $this->db->query($qr_sql);

        return $cd_lista_negra_divulgacao;
    }

    public function salvar_email($cd_lista_negra_divulgacao, $args = array())
    {
        $cd_lista_negra_divulgacao_email = intval($this->db->get_new_id('projetos.lista_negra_divulgacao_email', 'cd_lista_negra_divulgacao_email'));
        
        $qr_sql = "
            INSERT INTO projetos.lista_negra_divulgacao_email
                 (
                    cd_lista_negra_divulgacao_email,
                    cd_lista_negra_divulgacao,
                    ds_lista_negra_divulgacao_email, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".intval($cd_lista_negra_divulgacao_email).",
                    ".intval($cd_lista_negra_divulgacao).",   
                    ".(trim($args['ds_lista_negra_divulgacao_email']) != '' ? str_escape($args['ds_lista_negra_divulgacao_email']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."    
                 );";

        $this->db->query($qr_sql);

        return $cd_lista_negra_divulgacao_email;
    }

    public function atualizar($cd_lista_negra_divulgacao, $args = array())
    {
        $qr_sql = " 
            UPDATE projetos.lista_negra_divulgacao
               SET ds_lista_negra_divulgacao = ".(trim($args['ds_lista_negra_divulgacao']) != '' ? str_escape($args['ds_lista_negra_divulgacao']) : "DEFAULT").",
                   cd_usuario_alteracao      = ".intval($args['cd_usuario']).",
                   dt_alteracao              = CURRENT_TIMESTAMP
             WHERE cd_lista_negra_divulgacao = ".intval($cd_lista_negra_divulgacao).";";

        $this->db->query($qr_sql);
    }    

    public function atualizar_email($cd_lista_negra_divulgacao_email, $args = array())
    {    
        $qr_sql = " 
            UPDATE projetos.lista_negra_divulgacao_email
               SET ds_lista_negra_divulgacao_email = ".(trim($args['ds_lista_negra_divulgacao_email']) != '' ? str_escape($args['ds_lista_negra_divulgacao_email']) : "DEFAULT").",
                   cd_usuario_alteracao            = ".intval($args['cd_usuario']).",
                   dt_alteracao                    = CURRENT_TIMESTAMP
             WHERE cd_lista_negra_divulgacao_email = ".intval($cd_lista_negra_divulgacao_email).";";

        $this->db->query($qr_sql);
    }

    public function excluir($cd_lista_negra_divulgacao, $cd_usuario)
    {
        $qr_sql = " 
            UPDATE projetos.lista_negra_divulgacao
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_lista_negra_divulgacao = ".intval($cd_lista_negra_divulgacao).";";

        $this->db->query($qr_sql);     
    }

    public function excluir_email($cd_lista_negra_divulgacao_email, $cd_usuario)
    {
        $qr_sql = " 
            UPDATE projetos.lista_negra_divulgacao_email
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_lista_negra_divulgacao_email = ".intval($cd_lista_negra_divulgacao_email).";";

        $this->db->query($qr_sql);
    }
}
?>