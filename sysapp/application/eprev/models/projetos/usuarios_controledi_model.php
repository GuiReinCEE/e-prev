<?php
class Usuarios_controledi_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT uc.codigo,
                           uc.nome,
                           uc.divisao,
                           uc.usuario,
                           uc.guerra,
                           funcoes.get_usuario_avatar(uc.codigo) AS avatar,
                           uc.tipo,
						   uc.nr_ramal,
                           CASE WHEN tipo = 'D' THEN uc.observacao 
                                WHEN tipo = 'G' THEN (CASE WHEN SUBSTRING(uc.divisao FROM 1 FOR 1) = 'A' THEN 'Assessor(a)' ELSE 'Gerente' END)
                                WHEN tipo = 'U' THEN 'Colaborador(a)'
                                WHEN tipo = 'N' THEN 'Colaborador(a)'
                                WHEN tipo = 'P' THEN 'Prestador(a) de Serviço'
                                WHEN tipo = 'A' THEN 'Aprendiz'
                                WHEN tipo = 'E' THEN 'Estagiário(a)'
                                ELSE ''
                           END || (CASE WHEN COALESCE(uc.indic_13,'N') = 'S' THEN ' - Supervisor(a)' ELSE '' END) AS papel,
                           c.nome_cargo,
                           uc.cd_registro_empregado,
						   TO_CHAR(um.dt_admissao,'DD/MM/YYYY') AS dt_admissao
                      FROM projetos.usuarios_controledi uc
                      LEFT JOIN projetos.cargos c
                        ON c.cd_cargo = uc.cd_cargo
					  LEFT JOIN projetos.usuario_matriz um
					    ON um.cd_usuario = uc.codigo
                     WHERE uc.divisao NOT IN ('SNG', 'LM2')
                       ".(trim($args['fl_ativo']) == "S" ? "AND uc.tipo NOT IN ('X','T')": "")."
                       ".(trim($args['fl_ativo']) == "N" ? "AND uc.tipo IN ('X','T')": "")."
                       ".(trim($args['nome']) != "" ? "AND UPPER(funcoes.remove_acento(uc.nome)) LIKE UPPER(funcoes.remove_acento('%".str_replace(" ","%",trim($args['nome']))."%'))": "")."
                       ".(trim($args['usuario']) != "" ? "AND UPPER(funcoes.remove_acento(uc.usuario)) LIKE UPPER(funcoes.remove_acento('%".str_replace(" ","%",trim($args['usuario']))."%'))": "")."
                       ".(trim($args['divisao']) != "" ? "AND uc.divisao = '".trim($args['divisao'])."'" : "")."
                       ".(intval($args['cd_usuario']) > 0 ? "AND uc.codigo = ".intval($args['cd_usuario']) : "")."
                    ORDER BY uc.nome
                  ";
        $result = $this->db->query($qr_sql);
    }
    
    function nome_autocomplete(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT uc.nome
                      FROM projetos.usuarios_controledi uc
                      LEFT JOIN projetos.cargos c
                        ON c.cd_cargo = uc.cd_cargo
                     WHERE uc.tipo    NOT IN ('X','T')
                       AND uc.divisao NOT IN ('SNG', 'LM2')
                       AND funcoes.remove_acento(UPPER(uc.nome)) LIKE funcoes.remove_acento(UPPER('%".str_replace(" ","%",$args['nome'])."%'))
                     ORDER BY uc.nome
                     LIMIT 5
                  ";
        $result = $this->db->query($qr_sql);
    }

    function usuario_autocomplete(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT uc.usuario
                      FROM projetos.usuarios_controledi uc
                      LEFT JOIN projetos.cargos c
                        ON c.cd_cargo = uc.cd_cargo
                     WHERE uc.tipo    NOT IN ('X','T')
                       AND uc.divisao NOT IN ('SNG', 'LM2')
                       AND funcoes.remove_acento(UPPER(uc.usuario)) LIKE funcoes.remove_acento(UPPER('%".str_replace(" ","%",$args['usuario'])."%'))
                     ORDER BY uc.usuario
                     LIMIT 5
                  ";
        $result = $this->db->query($qr_sql);
    }   

    /*
        TIPOS
        select codigo,  
               descricao
          from listas 
         where categoria = 'TPUS'   
    */
    
    function usuario(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT uc.codigo AS cd_usuario, 
                           uc.usuario, 
                           uc.nome, 
                           uc.tipo, 
                           uc.avatar, 
                           TO_CHAR(uc.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
                           uc.divisao AS cd_gerencia, 
                           uc.guerra,
                           uc.cd_registro_empregado,  
                           uc.diretoria AS cd_diretoria, 
                           uc.cd_cargo, 
                           uc.opt_workspace AS fl_exibe_cpuscanner,
                           uc.opt_interatividade AS fl_login_auto, 
                           uc.observacao,
                           uc.estacao_trabalho, 
                           uc.np_computador, 
                           uc.tela_inicial, 
                            
                           
                           uc.fl_ldap_autenticar,
                           uc.senha_md5,
                           
                           uc.indic_01, uc.indic_02, uc.indic_03, uc.indic_04, uc.indic_05, uc.indic_06, 
                           uc.indic_07, uc.indic_08, uc.indic_09, uc.indic_10, uc.indic_11, uc.indic_12, uc.indic_13,        
                           
                           uc.assinatura,
                           
                           uc.chamada_web,
                           uc.gap_atendimento_versao, 
                           uc.nr_ramal, 
                           uc.nr_ramal_callcenter, 
                           uc.nr_ip_callcenter, 
                           
                           uc.fl_intervalo,
                           
                           TO_CHAR(uc.dt_hora_confirmacao,'DD/MM/YYYY HH24:MI:SS') AS dt_hora_confirmacao,
                           TO_CHAR(uc.ultima_resposta_vida,'DD/MM/YYYY HH24:MI:SS') AS dt_ultima_resposta_vida,
                           TO_CHAR(uc.dt_hora_scanner_computador,'DD/MM/YYYY HH24:MI:SS') AS dt_hora_scanner_computador,
                           TO_CHAR(uc.dt_ult_login,'DD/MM/YYYY HH24:MI:SS') AS dt_ult_login,
                           TO_CHAR(uc.dt_login_callcenter,'DD/MM/YYYY HH24:MI:SS') AS dt_login_callcenter,
                           TO_CHAR(uc.dt_monitor_callcenter,'DD/MM/YYYY HH24:MI:SS') AS dt_monitor_callcenter
                      FROM projetos.usuarios_controledi uc
                      LEFT JOIN projetos.cargos c
                        ON c.cd_cargo = uc.cd_cargo                   
                     WHERE uc.codigo = ".intval($args['cd_usuario'])."
                  ";
        $result = $this->db->query($qr_sql);                  
    }
    
    function salvar(&$result, $args=array())
    {
        $retorno = 0;
        
        if(intval($args['cd_usuario']) > 0)
        {
            ##UPDATE
            $qr_sql = " 
                        UPDATE projetos.usuarios_controledi
                           SET usuario               = ".(trim($args['usuario']) == "" ? "DEFAULT" : "LOWER(funcoes.remove_acento('".trim($args['usuario'])."'))").",
                               usu_email             = usuario,
                               nome                  = ".(trim($args['nome']) == "" ? "DEFAULT" : "funcoes.remove_acento('".trim($args['nome'])."')").",
                               guerra                = ".(trim($args['guerra']) == "" ? "DEFAULT" : "funcoes.remove_acento('".trim($args['guerra'])."')").",
                               tipo                  = ".(trim($args['tipo']) == "" ? "DEFAULT" : "'".trim($args['tipo'])."'").",
                               dt_nascimento         = ".(trim($args['dt_nascimento']) == "" ? "DEFAULT" : "TO_DATE('".trim($args['dt_nascimento'])."','DD/MM/YYYY')").",
                               divisao               = ".(trim($args['cd_gerencia']) == "" ? "DEFAULT" : "'".trim($args['cd_gerencia'])."'").",
                               cd_registro_empregado = ".(intval($args['cd_registro_empregado']) == 0 ? "DEFAULT" : intval($args['cd_registro_empregado'])).",
                               diretoria             = ".(trim($args['cd_diretoria']) == "" ? "DEFAULT" : "'".trim($args['cd_diretoria'])."'").",
                               cd_cargo              = ".(intval($args['cd_cargo']) == 0 ? "DEFAULT" : intval($args['cd_cargo'])).",
                               opt_workspace         = ".(trim($args['fl_exibe_cpuscanner']) == "" ? "DEFAULT" : "'".trim($args['fl_exibe_cpuscanner'])."'").",
                               ---opt_interatividade    = ".(trim($args['fl_login_auto']) == "" ? "DEFAULT" : "'".trim($args['fl_login_auto'])."'").",
                               observacao            = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args['observacao'])."'").",
                               tela_inicial          = ".(trim($args['tela_inicial']) == "" ? "DEFAULT" : "'".trim($args['tela_inicial'])."'").",
                               fl_ldap_autenticar    = ".(trim($args['fl_ldap_autenticar']) == "" ? "DEFAULT" : "'".trim($args['fl_ldap_autenticar'])."'").",
                               senha_md5             = ".(trim($args['senha_md5']) == trim($args['senha_md5_old']) ? "senha_md5" : "MD5('".trim($args['senha_md5'])."')").",
                               assinatura            = ".(trim($args['assinatura']) == "" ? "DEFAULT" : "'".trim($args['assinatura'])."'").",
                               nr_ramal              = ".(intval($args['nr_ramal']) == 0 ? "DEFAULT" : intval($args['nr_ramal'])).",
                               nr_ramal_callcenter   = ".(intval($args['nr_ramal_callcenter']) == 0 ? "DEFAULT" : intval($args['nr_ramal_callcenter'])).",
                               nr_ip_callcenter      = ".(trim($args['nr_ip_callcenter']) == "" ? "DEFAULT" : "'".trim($args['nr_ip_callcenter'])."'").",
                               fl_intervalo          = ".(trim($args['fl_intervalo']) == "" ? "DEFAULT" : "'".trim($args['fl_intervalo'])."'").",
                               indic_01              = ".(trim($args['indic_01']) == "" ? "DEFAULT" : "'".trim($args['indic_01'])."'").",
                               indic_02              = ".(trim($args['indic_02']) == "" ? "DEFAULT" : "'".trim($args['indic_02'])."'").", 
                               indic_03              = ".(trim($args['indic_03']) == "" ? "DEFAULT" : "'".trim($args['indic_03'])."'").", 
                               indic_04              = ".(trim($args['indic_04']) == "" ? "DEFAULT" : "'".trim($args['indic_04'])."'").", 
                               ---indic_05              = ".(trim($args['indic_05']) == "" ? "DEFAULT" : "'".trim($args['indic_05'])."'").", 
                               indic_06              = ".(trim($args['indic_06']) == "" ? "DEFAULT" : "'".trim($args['indic_06'])."'").", 
                               indic_07              = ".(trim($args['indic_07']) == "" ? "DEFAULT" : "'".trim($args['indic_07'])."'").", 
                               indic_08              = ".(trim($args['indic_08']) == "" ? "DEFAULT" : "'".trim($args['indic_08'])."'").", 
                               indic_09              = ".(trim($args['indic_09']) == "" ? "DEFAULT" : "'".trim($args['indic_09'])."'").", 
                               indic_10              = ".(trim($args['indic_10']) == "" ? "DEFAULT" : "'".trim($args['indic_10'])."'").", 
                               indic_11              = ".(trim($args['indic_11']) == "" ? "DEFAULT" : "'".trim($args['indic_11'])."'").", 
                               indic_12              = ".(trim($args['indic_12']) == "" ? "DEFAULT" : "'".trim($args['indic_12'])."'").",
                               indic_13              = ".(trim($args['indic_13']) == "" ? "DEFAULT" : "'".trim($args['indic_13'])."'")."
                         WHERE codigo = ".intval($args['cd_usuario'])."
                      ";    
            $this->db->query($qr_sql);
            $retorno = intval($args['cd_usuario']); 
        }
        else
        {
            ##INSERT
            $new_id = intval($this->db->get_new_id("projetos.usuarios_controledi", "codigo"));
            $qr_sql = " 
                        INSERT INTO projetos.usuarios_controledi
                             (
                               codigo, 
                               usuario,
                               usu_email,
                               nome,                                               
                               guerra,
                               tipo,
                               dt_nascimento,
                               divisao,
                               cd_registro_empregado,
                               diretoria,
                               cd_cargo,
                               opt_workspace,
                               opt_interatividade,
                               observacao,
                               tela_inicial,
                               fl_ldap_autenticar,
                               senha_md5,
                               assinatura,
                               nr_ramal,
                               nr_ramal_callcenter,
                               nr_ip_callcenter,
                               fl_intervalo,
                               indic_01,                       
                               indic_02,                               
                               indic_03,                               
                               indic_04,                               
                               indic_05,                               
                               indic_06,                               
                               indic_07,                               
                               indic_08,                               
                               indic_09,                               
                               indic_10,                               
                               indic_11,                               
                               indic_12,                            
                               indic_13                            
                             )
                        VALUES 
                             (
                               ".$new_id.",
                               ".(trim($args['usuario']) == "" ? "DEFAULT" : "LOWER(funcoes.remove_acento('".trim($args['usuario'])."'))").",
                               usuario,
                               ".(trim($args['nome']) == "" ? "DEFAULT" : "funcoes.remove_acento('".trim($args['nome'])."')").",                               
                               ".(trim($args['guerra']) == "" ? "DEFAULT" : "funcoes.remove_acento('".trim($args['guerra'])."')").",                               
                               ".(trim($args['tipo']) == "" ? "DEFAULT" : "'".trim($args['tipo'])."'").",                              
                               ".(trim($args['dt_nascimento']) == "" ? "DEFAULT" : "TO_DATE('".trim($args['dt_nascimento'])."','DD/MM/YYYY')").",                              
                               ".(trim($args['cd_gerencia']) == "" ? "DEFAULT" : "'".trim($args['cd_gerencia'])."'").",                            
                               ".(intval($args['cd_registro_empregado']) == 0 ? "DEFAULT" : intval($args['cd_registro_empregado'])).",                             
                               ".(trim($args['cd_diretoria']) == "" ? "DEFAULT" : "'".trim($args['cd_diretoria'])."'").",                              
                               ".(intval($args['cd_cargo']) == 0 ? "DEFAULT" : intval($args['cd_cargo'])).",                               
                               ".(trim($args['fl_exibe_cpuscanner']) == "" ? "DEFAULT" : "'".trim($args['fl_exibe_cpuscanner'])."'").",                            
                               'N',                            
                               ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args['observacao'])."'").",                              
                               ".(trim($args['tela_inicial']) == "" ? "DEFAULT" : "'".trim($args['tela_inicial'])."'").",                              
                               ".(trim($args['fl_ldap_autenticar']) == "" ? "DEFAULT" : "'".trim($args['fl_ldap_autenticar'])."'").",                              
                               ".(trim($args['senha_md5']) == "" ? "DEFAULT" : "MD5('".trim($args['senha_md5'])."')").",                               
                               ".(trim($args['assinatura']) == "" ? "DEFAULT" : "'".trim($args['assinatura'])."'").",                              
                               ".(intval($args['nr_ramal']) == 0 ? "DEFAULT" : intval($args['nr_ramal'])).",                               
                               ".(intval($args['nr_ramal_callcenter']) == 0 ? "DEFAULT" : intval($args['nr_ramal_callcenter'])).",                             
                               ".(trim($args['nr_ip_callcenter']) == "" ? "DEFAULT" : "'".trim($args['nr_ip_callcenter'])."'").",                              
                               ".(trim($args['fl_intervalo']) == "" ? "DEFAULT" : "'".trim($args['fl_intervalo'])."'").",
                               ".(trim($args['indic_01']) == "" ? "DEFAULT" : "'".trim($args['indic_01'])."'").",                              
                               ".(trim($args['indic_02']) == "" ? "DEFAULT" : "'".trim($args['indic_02'])."'").",                              
                               ".(trim($args['indic_03']) == "" ? "DEFAULT" : "'".trim($args['indic_03'])."'").",                              
                               ".(trim($args['indic_04']) == "" ? "DEFAULT" : "'".trim($args['indic_04'])."'").",                              
                               ".(trim($args['indic_05']) == "" ? "DEFAULT" : "'".trim($args['indic_05'])."'").",                              
                               ".(trim($args['indic_06']) == "" ? "DEFAULT" : "'".trim($args['indic_06'])."'").",                              
                               ".(trim($args['indic_07']) == "" ? "DEFAULT" : "'".trim($args['indic_07'])."'").",                              
                               ".(trim($args['indic_08']) == "" ? "DEFAULT" : "'".trim($args['indic_08'])."'").",                              
                               ".(trim($args['indic_09']) == "" ? "DEFAULT" : "'".trim($args['indic_09'])."'").",                              
                               ".(trim($args['indic_10']) == "" ? "DEFAULT" : "'".trim($args['indic_10'])."'").",                              
                               ".(trim($args['indic_11']) == "" ? "DEFAULT" : "'".trim($args['indic_11'])."'").",                              
                               ".(trim($args['indic_12']) == "" ? "DEFAULT" : "'".trim($args['indic_12'])."'").",                               
                               ".(trim($args['indic_13']) == "" ? "DEFAULT" : "'".trim($args['indic_13'])."'")."                               
                             );         
                      ";
            $this->db->query($qr_sql);  
            $retorno = $new_id;         
            
        }
        
        #echo "<pre>$qr_sql</pre>";
        #exit;
        
        return $retorno;
    }       
    
    function cargoCombo(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT cd_cargo AS value, 
                           nome_cargo AS text 
                      FROM projetos.cargos 
                     WHERE cd_familia IS NOT NULL
                     ORDER BY nome_cargo
                  ";
        $result = $this->db->query($qr_sql);
    }
    
    function diretoriaCombo(&$result, $args=array())
    {
        $qr_sql = "
                SELECT DISTINCT(area) AS value, 
                       area AS text 
                  FROM projetos.divisoes 
                 WHERE area IS NOT NULL 
                    OR TRIM(area) <> ''
                 ORDER BY text";

        $result = $this->db->query($qr_sql);
    }
	
	public function get_usuario_foto($usuario)
	{
		$qr_sql = "
			SELECT funcoes.get_usuario_avatar(codigo) AS avatar,
				   usuario
			  FROM projetos.usuarios_controledi 
			 WHERE LOWER(usuario) = LOWER('".trim($usuario)."');";
		
		return $this->db->query($qr_sql)->row_array();
	}
}
?>