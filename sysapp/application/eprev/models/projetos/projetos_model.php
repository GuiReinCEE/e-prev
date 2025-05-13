<?php

class Projetos_model extends Model
{

    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args = array())
    {

        $qr_sql = "
            SELECT DISTINCT codigo,
                   nome,
                   descricao,
                   TO_CHAR(data_cad, 'DD/MM/YYYY') AS data_cad,
                   TO_CHAR(data_implantacao, 'DD/MM/YYYY') AS data_implantacao,
                   area
              FROM projetos.projetos
              ".(trim($args['tipo_usuario']) == 'D' 
                       ? "WHERE tipo = 'D'" 
                       : "WHERE
                              (
				area='".$args['divisao_usuario']."'
				OR atendente='".$args['usuario']."'
				OR analista_responsavel='".$args['usuario']."'
			      )
			    AND dt_exclusao IS NULL
			    AND tipo = '".$args['tipo']."'");
        
        $result = $this->db->query($qr_sql);
    }
    
    function carrega(&$result, $args = array())
    {
        $qr_sql = "
            SELECT codigo,      
                   nome,
                   descricao,
                   area,
                   nivel,
                   administrador1,
                   administrador2,
                   atendente,
                   cod_projeto_superior,
                   analista_responsavel,
                   diretriz,
                   programa_institucional,
                   tipo,
                   fl_atividade,
                   TO_CHAR(data_implantacao, 'DD/MM/YYYY') AS data_implantacao
              FROM projetos.projetos
             WHERE codigo = ".intval($args['codigo']);
        
        $result = $this->db->query($qr_sql);
    }
    
    function areas(&$result, $args = array())
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome   AS text
              FROM projetos.divisoes
             ORDER BY nome";
        
        $result = $this->db->query($qr_sql);
    }
    
    function analistas(&$result, $args = array())
    {
        $qr_sql = "
            SELECT usuario AS value,
                   nome   AS text
              FROM projetos.usuarios_controledi
             WHERE tipo = 'N' 
               AND divisao_ant = 'GI'
             ORDER BY nome";
        
        $result = $this->db->query($qr_sql);
    }
    
    function atendentes(&$result, $args = array())
    {
        $qr_sql = "
            SELECT usuario AS value, 
                   nome AS text 
	      FROM projetos.usuarios_controledi 
	     WHERE (tipo = 'A' OR tipo ='G') 
	     ORDER BY nome";
        
        $result = $this->db->query($qr_sql);
    }
    
    function responsaveis(&$result, $args = array())
    {
        $qr_sql = "
            SELECT usuario AS value, 
                   nome AS text 
	      FROM projetos.usuarios_controledi  
             WHERE tipo NOT IN ('X', 'P', 'T')
	     ORDER BY nome ";
        
        $result = $this->db->query($qr_sql);
    }
    
    function projetos(&$result, $args = array())
    {
        $qr_sql = "
            SELECT codigo AS value, 
                   nome AS text
              FROM projetos.projetos 
             ORDER BY nome";
        
        $result = $this->db->query($qr_sql);
    }
    
    function niveis(&$result, $args = array())
    {
        $qr_sql = "
            SELECT codigo AS value,
                   descricao AS text
	      FROM public.listas 
	     WHERE categoria = 'NIVL' 
	     ORDER BY codigo";
         
        $result = $this->db->query($qr_sql);
    }
    
    function institucionais(&$result, $args = array())
    {
        $qr_sql = "
            SELECT codigo AS value,
                   descricao AS text
	      FROM public.listas 
             WHERE categoria = 'PRFC' 
               AND divisao IS NULL
             ORDER BY descricao";
         
        $result = $this->db->query($qr_sql);
    }
    
    function diretrizes(&$result, $args = array())
    {
        $qr_sql = "
            SELECT codigo AS value,
                   descricao AS text
	      FROM public.listas 
             WHERE categoria = 'DTRZ'
             ORDER BY codigo";
         
        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args = array())
    {
        
        if(intval($args['salva']) == 0)
        {
            $qr_sql = "
                INSERT INTO projetos.projetos
                     ( 
                        nome,
                        descricao,
                        area,
                        nivel,
                        administrador1,
                        administrador2,
                        atendente,
                        diretriz,
                        data_implantacao,
                        cod_projeto_superior,
                        tipo,
                        analista_responsavel,
                        programa_institucional,
                        fl_atividade,
                        data_cad,
                        cd_usuario_inclusao
                        
                    )
               VALUES
                    (
                        ".(trim($args['nome']) != ''                   ? "'".$args['nome']."'"                                     : "DEFAULT" ).",  
                        ".(trim($args['descricao']) != ''              ? "'".$args['descricao']."'"                                : "DEFAULT" ).",
                        ".(trim($args['area']) != ''                   ? "'".$args['area']."'"                                     : "DEFAULT" ).", 
                        ".(trim($args['nivel']) != ''                  ? "'".$args['nivel']."'"                                    : "DEFAULT" ).", 
                        ".(trim($args['administrador1']) != ''         ? "'".$args['administrador1']."'"                           : "DEFAULT" ).", 
                        ".(trim($args['administrador2']) != ''         ? "'".$args['administrador2']."'"                           : "DEFAULT" ).", 
                        ".(trim($args['atendente']) != ''              ? "'".$args['atendente']."'"                                : "DEFAULT" ).", 
                        ".(trim($args['diretriz']) != ''               ? "'".$args['diretriz']."'"                                 : "DEFAULT" ).", 
                        ".(trim($args['data_implantacao']) != ''       ? "TO_DATE('".$args['data_implantacao']."', 'DD/MM/YYYY')"  : "DEFAULT" ).",   
                        ".(trim($args['cod_projeto_superior']) != ''   ? intval($args['cod_projeto_superior'])                     : "DEFAULT" ).", 
                        ".(trim($args['tipo']) != ''                   ? "'".$args['tipo']."'"                                     : "DEFAULT" ).",
                        ".(trim($args['analista_responsavel']) != ''   ? "'".$args['analista_responsavel']."'"                     : "DEFAULT" ).", 
                        ".(trim($args['programa_institucional']) != '' ? "'".$args['programa_institucional']."'"                   : "DEFAULT" ).",
                        ".(trim($args['fl_atividade']) != ''           ? "'".$args['fl_atividade']."'"                             : "DEFAULT" ).",
                        CURRENT_TIMESTAMP,
                        ".intval($args['cd_usuario'])."
                    )
                    ";
        }
        else
        {
            $qr_sql = "
                UPDATE projetos.projetos 
                   SET nome                   = ".(trim($args['nome']) != ''                   ? "'".$args['nome']."'"                                     : "DEFAULT" ).",   
                       descricao              = ".(trim($args['descricao']) != ''              ? "'".$args['descricao']."'"                                : "DEFAULT" ).", 
                       area                   = ".(trim($args['area']) != ''                   ? "'".$args['area']."'"                                     : "DEFAULT" ).",  
                       nivel                  = ".(trim($args['nivel']) != ''                  ? "'".$args['nivel']."'"                                    : "DEFAULT" ).", 
                       administrador1         = ".(trim($args['administrador1']) != ''         ? "'".$args['administrador1']."'"                           : "DEFAULT" ).", 
                       administrador2         = ".(trim($args['administrador2']) != ''         ? "'".$args['administrador2']."'"                           : "DEFAULT" ).", 
                       atendente              = ".(trim($args['atendente']) != ''              ? "'".$args['atendente']."'"                                : "DEFAULT" ).", 
                       diretriz               = ".(trim($args['diretriz']) != ''               ? "'".$args['diretriz']."'"                                 : "DEFAULT" ).", 
                       data_implantacao       = ".(trim($args['data_implantacao']) != ''       ? "TO_DATE('".$args['data_implantacao']."', 'DD/MM/YYYY')"  : "DEFAULT" ).",   
                       cod_projeto_superior   = ".(trim($args['cod_projeto_superior']) != ''   ? intval($args['cod_projeto_superior'])                     : "DEFAULT" ).", 
                       tipo                   = ".(trim($args['tipo']) != ''                   ? "'".$args['tipo']."'"                                     : "DEFAULT" ).",
                       analista_responsavel   = ".(trim($args['analista_responsavel']) != ''   ? "'".$args['analista_responsavel']."'"                     : "DEFAULT" ).", 
                       programa_institucional = ".(trim($args['programa_institucional']) != '' ? "'".$args['programa_institucional']."'"                   : "DEFAULT" ).",
                       fl_atividade           = ".(trim($args['fl_atividade']) != ''           ? "'".$args['fl_atividade']."'"                   : "DEFAULT" ).",
                       dt_alteracao           = CURRENT_TIMESTAMP,
                       cd_usuario_alteracao   = ".intval($args['cd_usuario'])."
                 WHERE codigo = ". intval($args['codigo']);
        }
        
        $this->db->query($qr_sql);
    }
    
    function lista_pessoas_envolvidas(&$result, $args = array())
    {
        $qr_sql = "
            SELECT uc.nome, 
                   pe.cd_envolvido, 
                   pe.cd_projeto 
              FROM projetos.projetos_envolvidos pe
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = pe.cd_envolvido
	     WHERE pe.cd_projeto = ".intval($args['codigo'])."
               AND pe.dt_exclusao IS NULL
             ORDER BY uc.nome ";
        
        $result = $this->db->query($qr_sql);
    }
    
    function excluir(&$result, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.projetos
	       SET dt_exclusao          = CURRENT_TIMESTAMP, 
                   cd_usuario_exclusao  = ".intval($args['cd_usuario'])."
	     WHERE codigo = ".intval($args['codigo']);
		
        $this->db->query($qr_sql);
    }
    
    function excluir_envolvido(&$result, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.projetos_envolvidos
	       SET dt_exclusao          = CURRENT_TIMESTAMP, 
                   cd_usuario_exclusao  = ".intval($args['cd_usuario'])."
	     WHERE cd_envolvido = ".intval($args['cd_envolvido']);
		
        $this->db->query($qr_sql);
    }
    
    function salvar_envolvido(&$result, $args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.projetos_envolvidos
                  (
                    cd_projeto,
                    cd_envolvido,
                    cd_usuario_inclusao,
                    dt_inclusao 
                  )
             VALUES
                  (
                    ".intval($args['codigo']).",
                    ".intval($args['cd_envolvido']).",
                    ".intval($args['cd_usuario']).",
                    CURRENT_TIMESTAMP
                  )";
        
        $this->db->query($qr_sql);
    }
    
    function acompanhamento(&$result, $args = array())
    {
        $qr_sql = "
            SELECT cd_acomp 
              FROM projetos.acompanhamento_projetos  
             WHERE cd_projeto = ". $args['codigo']."
             ORDER BY dt_acomp DESC LIMIT 1";
        
        $result = $this->db->query($qr_sql);
    }

}

?>