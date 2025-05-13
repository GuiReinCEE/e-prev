<?php

class nao_conformidade_auditor_model extends Model
{

    function __construct()
    {
        parent::Model();
    }
    
    function listar(&$result, $args=array())
    {
        $qr_sql = "
				   SELECT p.cd_processo,
                          p.procedimento,
                          us_t.nome AS usuario_titular,
                          us_s.nome AS usuario_substituto,
                          (CASE WHEN p.dt_ini_vigencia <= CURRENT_DATE AND COALESCE(p.dt_fim_vigencia, CURRENT_DATE) >= CURRENT_DATE 
                          	    THEN 'S' 
                          	    ELSE 'N' 
                          END) AS fl_vigente						  
                     FROM projetos.processos p
                     LEFT JOIN gestao.nao_conformidade_auditor nca
                       ON nca.cd_processo = p.cd_processo
                     LEFT JOIN projetos.usuarios_controledi us_t
                       ON us_t.codigo = nca.cd_usuario_titular
                     LEFT JOIN projetos.usuarios_controledi us_s
                       ON us_s.codigo = nca.cd_usuario_substituto
					WHERE 1 = 1   
					".(intval($args['cd_auditor']) > 0 ? "AND (nca.cd_usuario_titular = ".intval($args['cd_auditor'])." OR nca.cd_usuario_substituto = ".intval($args['cd_auditor']).")" : "")."  
					".(intval($args['cd_processo']) > 0 ? "AND p.cd_processo = ".intval($args['cd_processo'])."" : "")."  
					".(trim($args['fl_vigente']) != "" ? "AND '".trim($args['fl_vigente'])."' = (CASE WHEN p.dt_ini_vigencia <= CURRENT_DATE AND COALESCE(p.dt_fim_vigencia, CURRENT_DATE) >= CURRENT_DATE THEN 'S' ELSE 'N' END)" : "")."
                    ORDER BY p.procedimento
				";

        $result = $this->db->query($qr_sql);
    }
    
    function carrega(&$result, $args=array())
    {
        $qr_sql = "
				   SELECT nca.cd_usuario_titular,
                          nca.cd_usuario_substituto,
						  p.procedimento
                     FROM projetos.processos p
					 LEFT JOIN gestao.nao_conformidade_auditor nca
					   ON nca.cd_processo = p.cd_processo
                    WHERE p.cd_processo = ".intval($args['cd_processo']);
        
        $result = $this->db->query($qr_sql);
    }
	
    function combo_processo(&$result, $args=array())
    {
        $qr_sql = "
					SELECT p.cd_processo AS value,
                           p.procedimento AS text
                     FROM projetos.processos p
					ORDER BY p.procedimento
		          ";

        $result = $this->db->query($qr_sql);
    }
	
    function usuario_auditor(&$result, $args=array())
    {
        $qr_sql = "SELECT uc.codigo AS value,
                          uc.nome AS text
                     FROM projetos.usuarios_controledi uc
                    WHERE uc.indic_12 = '*'
                      AND (tipo <> 'X' OR 0 < (SELECT COUNT(nca.*) 
                                                 FROM gestao.nao_conformidade_auditor nca
                                                WHERE nca.cd_usuario_titular = uc.codigo 
												   OR nca.cd_usuario_substituto = uc.codigo))
                    ORDER BY uc.nome";

        $result = $this->db->query($qr_sql);
    }
    
    function usuario_titular(&$result, $args=array())
    {
        $qr_sql = "SELECT codigo AS value,
                          nome AS text
                     FROM projetos.usuarios_controledi
                    WHERE indic_12 = '*'
                      AND (tipo <> 'X' OR 0 < (SELECT COUNT(*) 
                                                 FROM gestao.nao_conformidade_auditor
                                                WHERE cd_processo = ".intval($args['cd_processo'])."
                                                  AND cd_usuario_titular = ".intval($args['cd_usuario_titular']).")
                          )
                    ORDER BY nome";

        $result = $this->db->query($qr_sql);
    }
    
    function usuario_substituto(&$result, $args=array())
    {
        $qr_sql = "SELECT codigo AS value,
                          nome AS text
                     FROM projetos.usuarios_controledi
                    WHERE indic_12 = '*'
                      AND (tipo <> 'X' OR 0 < (SELECT COUNT(*) 
                                                 FROM gestao.nao_conformidade_auditor
                                                WHERE cd_processo = ".intval($args['cd_processo'])."
                                                  AND cd_usuario_substituto = ".intval($args['cd_usuario_substituto']).")
                          )
                    ORDER BY nome";

        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args=array())
    {        
        if(intval($args['fl_status']) > 0)
        {
            $qr_sql = "UPDATE gestao.nao_conformidade_auditor
                          SET cd_usuario_substituto = ".intval($args['cd_usuario_substituto']).",
                              cd_usuario_titular    = ".intval($args['cd_usuario_titular']).",
                              cd_usuario_alteracao  = ".intval($args['cd_usuario']).",
                              dt_alteracao          = CURRENT_TIMESTAMP
                        WHERE cd_processo = ".intval($args['cd_processo']);
        }
        else
        {
            $qr_sql = "INSERT INTO gestao.nao_conformidade_auditor
                              (
                                cd_usuario_substituto,
                                cd_usuario_titular,
                                cd_processo,
                                cd_usuario_inclusao,
                                dt_inclusao
                              )
                         VALUES
                              (
                                ".intval($args['cd_usuario_substituto']).",
                                ".intval($args['cd_usuario_titular']).",
                                ".intval($args['cd_processo']).",
                                ".intval($args['cd_usuario']).",
                                CURRENT_TIMESTAMP
                                    
                              )";
        }
        
        $this->db->query($qr_sql);
    }

}

?>