<?php
class contato_institucional_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function combo_tipo(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd_contato_institucional_tipo AS value,
                   ds_contato_institucional_tipo AS text
              FROM projetos.contato_institucional_tipo
             WHERE dt_exclusao IS NULL
             ORDER BY ds_contato_institucional_tipo";
        
        $result = $this->db->query($qr_sql);
    }
    
    function combo_empresa(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd_contato_institucional_empresa AS value,
                   ds_contato_institucional_empresa AS text
              FROM projetos.contato_institucional_empresa
             WHERE dt_exclusao IS NULL
             ORDER BY ds_contato_institucional_empresa";
        
        $result = $this->db->query($qr_sql);
    }
    
    function combo_cargo(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd_contato_institucional_cargo AS value,
                   ds_contato_institucional_cargo AS text
              FROM projetos.contato_institucional_cargo
             WHERE dt_exclusao IS NULL
             ORDER BY ds_contato_institucional_cargo";
        
        $result = $this->db->query($qr_sql);
    }
    
    function combo_uf(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd_uf AS value,
                   ds_uf AS text
              FROM geografico.uf
             ORDER BY ds_uf";
        
        $result = $this->db->query($qr_sql);
    }
    
    function listar(&$result, $args=array())
    {
        $qr_sql = "
            SELECT ci.cd_contato_institucional,
                   cit.ds_contato_institucional_tipo,
                   cie.ds_contato_institucional_empresa,
                   ci.nome,
                   cic.ds_contato_institucional_cargo,
                   ci.telefone_1,
                   ci.telefone_2,
                   ci.email_1,
                   ci.email_2,
                   ci.sec_nome,
                   ci.sec_telefone_1,
                   ci.sec_telefone_2,
                   ci.sec_email_1,
                   ci.sec_email_2,
				   SUBSTRING(ci.nome,1,37) AS etiq_nome,
				   funcoes.cep_correio(ci.cep) AS etiq_cep_net,
				   SUBSTRING(ci.logradouro,1,37) AS etiq_endereco,
				   SUBSTRING(ci.cidade,1,30) || ' ' || ci.uf AS etiq_localidade,
				   SUBSTRING(('N ' || COALESCE(ci.numero,'') || ' / ' || COALESCE(ci.complemento,'')),1,37) AS etiq_nr_complemento,
				   ci.cep AS etiq_cep
              FROM projetos.contato_institucional ci
              JOIN projetos.contato_institucional_tipo cit
                ON ci.cd_contato_institucional_tipo = cit.cd_contato_institucional_tipo
              JOIN projetos.contato_institucional_empresa cie
                ON ci.cd_contato_institucional_empresa = cie.cd_contato_institucional_empresa
              JOIN projetos.contato_institucional_cargo cic
                ON ci.cd_contato_institucional_cargo = cic.cd_contato_institucional_cargo
             WHERE ci.dt_exclusao IS NULL
               ".(trim($args['cd_contato_institucional_tipo'] != '' ? "AND cit.cd_contato_institucional_tipo = ".intval($args['cd_contato_institucional_tipo']) : ''))."
               ".(trim($args['cd_contato_institucional_empresa'] != '' ? "AND cie.cd_contato_institucional_empresa = ".intval($args['cd_contato_institucional_empresa']) : ''))."
               ".(trim($args['cd_contato_institucional_cargo'] != '' ? "AND cic.cd_contato_institucional_cargo = ".intval($args['cd_contato_institucional_cargo']) : ''))."
               ".(trim($args['nome'] != '' ? "AND UPPER(funcoes.remove_acento(ci.nome)) LIKE UPPER(funcoes.remove_acento('%".trim($args['nome'])."%'))" : ''))."
               ".(trim($args['sec_nome'] != '' ? "AND UPPER(funcoes.remove_acento(ci.sec_nome)) LIKE UPPER(funcoes.remove_acento('%".trim($args['sec_nome'])."%'))" : ''))."
             ORDER BY ds_contato_institucional_tipo ASC, ci.nome ASC;";

        $result = $this->db->query($qr_sql);
    }
    
    function carrega(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd_contato_institucional,
                   cd_contato_institucional_tipo,
                   cd_contato_institucional_empresa,
                   cd_contato_institucional_cargo,
                   nome,
                   telefone_1,
                   telefone_2,
                   email_1,
                   email_2,
                   cep,
                   logradouro,
                   numero,
                   complemento,
                   bairro,
                   cidade,
                   uf,
                   sec_nome,
                   sec_telefone_1,
                   sec_telefone_2,
                   sec_email_1,
                   sec_email_2
              FROM projetos.contato_institucional
             WHERE cd_contato_institucional = ".intval($args['cd_contato_institucional']).";";
        
        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args=array())
    {
        if(intval($args['cd_contato_institucional']) > 0)
        {
            $qr_sql = "
                UPDATE projetos.contato_institucional
                   SET cd_contato_institucional_tipo    = ".(trim($args['cd_contato_institucional_tipo']) != '' ? intval($args['cd_contato_institucional_tipo']) : "DEFAULT").",
                       cd_contato_institucional_empresa = ".(trim($args['cd_contato_institucional_empresa']) != '' ? intval($args['cd_contato_institucional_empresa']) : "DEFAULT").",
                       cd_contato_institucional_cargo   = ".(trim($args['cd_contato_institucional_cargo']) != '' ? intval($args['cd_contato_institucional_cargo']) : "DEFAULT").",
                       nome                             = ".(trim($args['nome']) != '' ? "UPPER(funcoes.remove_acento('".trim($args['nome'])."'))" : "DEFAULT").",
                       telefone_1                       = ".(trim($args['telefone_1']) != '' ? "'".trim($args['telefone_1'])."'" : "DEFAULT").",
                       telefone_2                       = ".(trim($args['telefone_2']) != '' ? "'".trim($args['telefone_2'])."'" : "DEFAULT").",
                       email_1                          = ".(trim($args['email_1']) != '' ? "'".trim($args['email_1'])."'" : "DEFAULT").",
                       email_2                          = ".(trim($args['email_2']) != '' ? "'".trim($args['email_2'])."'" : "DEFAULT").",
                       cep                              = ".(trim($args['cep']) != '' ? "'".trim($args['cep'])."'" : "DEFAULT").",
                       logradouro                       = ".(trim($args['logradouro']) != '' ? "UPPER(funcoes.remove_acento('".trim($args['logradouro'])."'))" : "DEFAULT").",
                       numero                           = ".(trim($args['numero']) != '' ? "'".trim($args['numero'])."'" : "DEFAULT").",
                       complemento                      = ".(trim($args['complemento']) != '' ? "UPPER(funcoes.remove_acento('".trim($args['complemento'])."'))" : "DEFAULT").",
                       bairro                           = ".(trim($args['bairro']) != '' ? "UPPER(funcoes.remove_acento('".trim($args['bairro'])."'))" : "DEFAULT").",
                       cidade                           = ".(trim($args['cidade']) != '' ? "UPPER(funcoes.remove_acento('".trim($args['cidade'])."'))" : "DEFAULT").",
                       uf                               = ".(trim($args['uf']) != '' ? "'".trim($args['uf'])."'" : "DEFAULT").",
                       sec_nome                         = ".(trim($args['sec_nome']) != '' ? "'".trim($args['sec_nome'])."'" : "DEFAULT").",
                       sec_telefone_1                   = ".(trim($args['sec_telefone_1']) != '' ? "'".trim($args['sec_telefone_1'])."'" : "DEFAULT").",
                       sec_telefone_2                   = ".(trim($args['sec_email_2']) != '' ? "'".trim($args['sec_email_2'])."'" : "DEFAULT").",
                       sec_email_1                      = ".(trim($args['sec_email_1']) != '' ? "'".trim($args['sec_email_1'])."'" : "DEFAULT").",
                       sec_email_2                      = ".(trim($args['sec_email_2']) != '' ? "'".trim($args['sec_email_2'])."'" : "DEFAULT").",
                       cd_usuario_alteracao             = ".(trim($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT").",
                       dt_alteracao                     = CURRENT_TIMESTAMP
                 WHERE cd_contato_institucional = ".intval($args['cd_contato_institucional']);
        }
        else
        {
            $qr_sql = "
                INSERT INTO projetos.contato_institucional
                     (
                        cd_contato_institucional_tipo,
                        cd_contato_institucional_empresa,
                        cd_contato_institucional_cargo,
                        nome,
                        telefone_1,
                        telefone_2,
                        email_1,
                        email_2,
                        cep,
                        logradouro,
                        numero,
                        complemento,
                        bairro,
                        cidade,
                        uf,
                        sec_nome,
                        sec_telefone_1,
                        sec_telefone_2,
                        sec_email_1,
                        sec_email_2,
                        cd_usuario_inclusao,
                        cd_usuario_alteracao
                     )
                VALUES
                     (
                        ".(trim($args['cd_contato_institucional_tipo']) != '' ? intval($args['cd_contato_institucional_tipo']) : "DEFAULT").",
                        ".(trim($args['cd_contato_institucional_empresa']) != '' ? intval($args['cd_contato_institucional_empresa']) : "DEFAULT").",
                        ".(trim($args['cd_contato_institucional_cargo']) != '' ? intval($args['cd_contato_institucional_cargo']) : "DEFAULT").",
                        ".(trim($args['nome']) != '' ? "UPPER(funcoes.remove_acento('".trim($args['nome'])."'))" : "DEFAULT").",
                        ".(trim($args['telefone_1']) != '' ? "'".trim($args['telefone_1'])."'" : "DEFAULT").",
                        ".(trim($args['telefone_2']) != '' ? "'".trim($args['telefone_2'])."'" : "DEFAULT").",
                        ".(trim($args['email_1']) != '' ? "'".trim($args['email_1'])."'" : "DEFAULT").",
                        ".(trim($args['email_2']) != '' ? "'".trim($args['email_2'])."'" : "DEFAULT").",
                        ".(trim($args['cep']) != '' ? "'".trim($args['cep'])."'" : "DEFAULT").",
                        ".(trim($args['logradouro']) != '' ? "UPPER(funcoes.remove_acento('".trim($args['logradouro'])."'))" : "DEFAULT").",
                        ".(trim($args['numero']) != '' ? "'".trim($args['numero'])."'" : "DEFAULT").",
                        ".(trim($args['complemento']) != '' ? "UPPER(funcoes.remove_acento('".trim($args['complemento'])."'))" : "DEFAULT").",
                        ".(trim($args['bairro']) != '' ? "UPPER(funcoes.remove_acento('".trim($args['bairro'])."'))" : "DEFAULT").",
                        ".(trim($args['cidade']) != '' ? "UPPER(funcoes.remove_acento('".trim($args['cidade'])."'))" : "DEFAULT").",
                        ".(trim($args['uf']) != '' ? "'".trim($args['uf'])."'" : "DEFAULT").",
                        ".(trim($args['sec_nome']) != '' ? "'".trim($args['sec_nome'])."'" : "DEFAULT").",
                        ".(trim($args['sec_telefone_1']) != '' ? "'".trim($args['sec_telefone_1'])."'" : "DEFAULT").",
                        ".(trim($args['sec_telefone_2']) != '' ? "'".trim($args['sec_telefone_2'])."'" : "DEFAULT").",
                        ".(trim($args['sec_email_1']) != '' ? "'".trim($args['sec_email_1'])."'" : "DEFAULT").",
                        ".(trim($args['sec_email_2']) != '' ? "'".trim($args['sec_email_2'])."'" : "DEFAULT").",
                        ".(trim($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT").",
                        ".(trim($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT")."
                     )";
        }
        
        $this->db->query($qr_sql);
    }
    
    function excluir(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE projetos.contato_institucional
               SET dt_exclusao = CURRENT_TIMESTAMP,
                   cd_usuario_exclusao = ".intval($args['cd_usuario'])."
             WHERE cd_contato_institucional = ".intval($args['cd_contato_institucional']).";
                ";
        
        $this->db->query($qr_sql);
    }
}
?>