<?php
class relatorio_auditoria_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
     
    function listar(&$result, $args=array())
    {
        $qr_sql = "
			SELECT ra.cd_relatorio_auditoria,
				   ra.nr_ano || '/' || TO_CHAR(ra.nr_mes, 'FM00') AS ano_mes,
				   ra.escopo,
				   funcoes.get_usuario_nome(ra.cd_auditor_lider) AS auditor_lider,
				   TO_CHAR(ra.dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
				   ra.ds_empresa,
				   (CASE WHEN ra.fl_tipo = 'I' 
				         THEN 'Interna'
				         ELSE 'Externa'
				   END) AS ds_tipo
			  FROM gestao.relatorio_auditoria ra
			 WHERE ra.dt_exclusao IS NULL
			   ".(trim($args['ano'])!= '' ?  "AND ra.nr_ano = ".intval($args['ano']) : '')."
			   ".(trim($args['fl_tipo'])!= '' ?  "AND ra.fl_tipo = '".trim($args['fl_tipo'])."'" : '')."
			   ".(trim($args['cd_auditor_lider'])!= '' ?  "AND ra.cd_auditor_lider = ".intval($args['cd_auditor_lider']) : '')."
			   ".(trim($args['cd_processo'])!= '' ? "
			   AND 0 < ( SELECT COUNT(*) 
				 		   FROM gestao.relatorio_auditoria_processo rap 
						  WHERE rap.cd_relatorio_auditoria = ra.cd_relatorio_auditoria 
						    AND rap.cd_processo =  ".intval($args['cd_processo']).")" : '')."
			   ".(trim($args['cd_usuario_equipe'])!= '' ? "
			   AND 0 < ( SELECT COUNT(*) 
						   FROM gestao.relatorio_auditoria_equipe rae 
						  WHERE rae.cd_relatorio_auditoria = ra.cd_relatorio_auditoria 
						    AND rae.dt_exclusao IS NULL
						    AND rae.cd_usuario =  ".intval($args['cd_usuario_equipe']).")" : '')."
			   ".(trim($args['tipo'])!= '' ? "
			   AND 0 < ( SELECT COUNT(*) 
						   FROM gestao.relatorio_auditoria_constatacao rac
						  WHERE rac.cd_relatorio_auditoria = ra.cd_relatorio_auditoria 
						    AND rac.dt_exclusao IS NULL
						    AND rac.tipo =  '".trim($args['tipo'])."'
						   ".(trim($args['fl_impacto'])!= '' ?  "AND rac.fl_impacto = '".trim($args['fl_impacto'])."'" : '').")" : '').";";
        $result = $this->db->query($qr_sql);
    }
    
    function processo_checked(&$result, $args=array())
    {
        $qr_sql = "
			SELECT p.procedimento, 
				   p.cd_processo
			  FROM gestao.relatorio_auditoria_processo rap
			  JOIN projetos.processos p
			    ON rap.cd_processo = p.cd_processo
			 WHERE rap.cd_relatorio_auditoria = ".intval($args['cd_relatorio_auditoria']);
        
        $result = $this->db->query($qr_sql);
    }
    
    function equipe_checked(&$result, $args=array())
    {
        $qr_sql = "
			SELECT rae.cd_usuario,
				   u.nome,
				   rae.cd_relatorio_auditoria_equipe,
				   CASE WHEN rae.tipo = 'A' THEN 'Auditor'
					    WHEN rae.tipo = 'E' THEN 'Especialista'
					    ELSE 'Observador'
				   END AS tipo
			  FROM gestao.relatorio_auditoria_equipe rae
			  JOIN projetos.usuarios_controledi u
			    ON u.codigo = rae.cd_usuario
			 WHERE rae.dt_exclusao IS NULL
			   AND rae.cd_relatorio_auditoria = ".intval($args['cd_relatorio_auditoria']);

        $result = $this->db->query($qr_sql);
    }
    
    function get_processos(&$result, $args=array())
    {
        $qr_sql = "
			SELECT p.cd_processo  AS value,
				   p.procedimento AS text
			  FROM projetos.processos p
			 WHERE p.dt_fim_vigencia is NULL
			 UNION 
			SELECT rap.cd_processo AS value,
				   p2.procedimento AS text
			  FROM gestao.relatorio_auditoria_processo rap
			  JOIN projetos.processos p2
			    ON p2.cd_processo = rap.cd_processo
			 WHERE rap.cd_relatorio_auditoria = ".intval($args['cd_relatorio_auditoria'])."
			 ORDER BY text;";
        
        $result = $this->db->query($qr_sql);
    }
    
    function get_usuarios_comite(&$result, $args=array())
    {
        $qr_sql = "
			SELECT codigo AS value,
				   nome   AS text 
			  FROM projetos.usuarios_controledi
			 WHERE indic_12 = '*'
			   AND tipo != 'X';";
        $result = $this->db->query($qr_sql);
    }
    
    function carrega( &$result, $args=array())
    {
        $qr_sql = "
			SELECT ra.cd_relatorio_auditoria,
				   ra.nr_mes || '/' || ra.nr_ano AS mes_ano,
				   ra.escopo,
				   ra.cd_auditor_lider,
				   ra.conclusao,
				   ra.representante,
				   ra.fl_tipo,
				   ra.ds_empresa,
				   TO_CHAR(ra.dt_constatacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_constatacao,
				   funcoes.get_usuario_nome(ra.cd_usuario_constatacao) AS ds_usuario_constatacao
			  FROM gestao.relatorio_auditoria ra
			 WHERE dt_exclusao IS NULL
			   AND cd_relatorio_auditoria = ".intval($args['cd_relatorio_auditoria']);
        $result = $this->db->query($qr_sql);
    }
    
    function get_processos_checked( &$result, $args=array())
    {
        $qr_sql = "
			SELECT cd_processo
			  FROM gestao.relatorio_auditoria_processo
			 WHERE cd_relatorio_auditoria = ".intval($args['cd_relatorio_auditoria']);
        
        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args=array())
    {
        $retorno = 0;
        
        if(intval($args['cd_relatorio_auditoria']) > 0)
        {
            $retorno = intval($args['cd_relatorio_auditoria']);
            
            $qr_sql = "
				UPDATE gestao.relatorio_auditoria
				   SET nr_mes               = " . (trim($args['nr_mes']) == "" ? "DEFAULT" : $args['nr_mes']) . ",
				 	   nr_ano               = " . (trim($args['nr_ano']) == "" ? "DEFAULT" : $args['nr_ano']) . ",
					   escopo               = " . (trim($args['escopo']) == "" ? "DEFAULT" : "'".(trim($args['escopo'])). "'") . ",
					   conclusao            = " . (trim($args['conclusao']) == "" ? "DEFAULT" : "'".(trim($args['conclusao'])). "'") . ",
					   representante        = " . (trim($args['representante']) == "" ? "DEFAULT" : "'".(trim($args['representante'])). "'") . ",
					   fl_tipo              = " . (trim($args['fl_tipo']) == "" ? "DEFAULT" : "'".(trim($args['fl_tipo'])). "'") . ",
					   ds_empresa           = " . (trim($args['ds_empresa']) == "" ? "DEFAULT" : "'".(trim($args['ds_empresa'])). "'") . ",
					   cd_auditor_lider     = " . (trim($args['cd_auditor_lider']) == "" ? "DEFAULT" : intval($args['cd_auditor_lider'])) . ",
					   cd_usuario_alteracao = ".intval($args['cd_usuario_inclusao']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_relatorio_auditoria = ".intval($args['cd_relatorio_auditoria']).";";
            
            if(count($args['ar_processos']) > 0)
			{
                $qr_sql.= "
					DELETE
					  FROM gestao.relatorio_auditoria_processo
					 WHERE cd_relatorio_auditoria = ".intval($args['cd_relatorio_auditoria']).";";

                $nr_conta = 0;
                
                while($nr_conta < count($args['ar_processos']))
                {
                    $qr_sql.= "
						INSERT INTO gestao.relatorio_auditoria_processo
							 (
								cd_relatorio_auditoria,
								cd_processo,
								cd_usuario_inclusao
							 )
						VALUES
							 (
							   ".intval($retorno).",
							   ".intval($args['ar_processos'][$nr_conta]).",
							   ".intval($args['cd_usuario_inclusao'])."
							 );";
                    $nr_conta++;
                }

                $this->db->query($qr_sql);
            }
        }
        else
        {
            $new_id = intval($this->db->get_new_id("gestao.relatorio_auditoria", "cd_relatorio_auditoria"));
            $retorno = $new_id;
            
            $qr_sql = "
				INSERT INTO gestao.relatorio_auditoria
					 (
					   cd_relatorio_auditoria,
					   nr_mes,
					   nr_ano,
					   escopo,
					   conclusao,
					   representante,
					   cd_auditor_lider,
					   fl_tipo,
					   ds_empresa,
					   cd_usuario_inclusao,
					   cd_usuario_alteracao
					 )
				VALUES
					 (
					   ".intval($new_id).",
					   " . (trim($args['nr_mes']) == "" ? "DEFAULT" : $args['nr_mes']) . ",
					   " . (trim($args['nr_ano']) == "" ? "DEFAULT" : $args['nr_ano']) . ",
					   " . (trim($args['escopo']) == "" ? "DEFAULT" : "'".(trim($args['escopo'])). "'") . ",
					   " . (trim($args['conclusao']) == "" ? "DEFAULT" : "'".(trim($args['conclusao'])). "'") . ",
					   " . (trim($args['representante']) == "" ? "DEFAULT" : "'".(trim($args['representante'])). "'") . ",
					   " . (trim($args['cd_auditor_lider']) == "" ? "DEFAULT" : intval($args['cd_auditor_lider'])) . ",
					   " . (trim($args['fl_tipo']) == "" ? "DEFAULT" : "'".(trim($args['fl_tipo'])). "'") . ",
					   " . (trim($args['ds_empresa']) == "" ? "DEFAULT" : "'".(trim($args['ds_empresa'])). "'") . ",
					   ".intval($args['cd_usuario_inclusao']).",
					   ".intval($args['cd_usuario_inclusao'])."
					 );";
            $nr_conta = 0;
            
            while($nr_conta < count($args['ar_processos']))
            {
                $qr_sql.= "
					INSERT INTO gestao.relatorio_auditoria_processo
						 (
							cd_relatorio_auditoria,
							cd_processo,
							cd_usuario_inclusao
						 )
					VALUES
						 (
						   ".intval($retorno).",
						   ".intval($args['ar_processos'][$nr_conta]).",
						   ".intval($args['cd_usuario_inclusao'])."
						 );";
                $nr_conta++;
            }
            
            $this->db->query($qr_sql);
        }
        
        return $retorno;
    }
    
    function excluir_processo(&$result, $args=array())
    {
        $qr_sql = "
			UPDATE gestao.relatorio_auditoria
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario_exclusao']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_relatorio_auditoria = ".intval($args['cd_relatorio_auditoria']).";";
        $this->db->query($qr_sql);
    }
    
    function get_usuarios(&$result, $args=array())
    {
        $qr_sql = "
			SELECT u.codigo AS value,
				   u.nome   AS text 
			  FROM projetos.usuarios_controledi u
			 WHERE u.tipo NOT IN ('X','f','E', 'T')
			 ORDER BY u.nome";
        $result = $this->db->query($qr_sql);
    }
    
    function salvar_equipe(&$result, $args=array())
    {
        if($args["cd_relatorio_auditoria_equipe"] > 0)
        {
            $qr_sql = "
				UPDATE gestao.relatorio_auditoria_equipe
				   SET cd_usuario = " . (trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])) . ",
					   tipo       = " . (trim($args['tipo']) == "" ? "DEFAULT" : "'".(trim($args['tipo'])). "'") . "
				 WHERE cd_relatorio_auditoria = ".intval($args['cd_relatorio_auditoria'])."
				   AND cd_relatorio_auditoria_equipe = ".intval($args['cd_relatorio_auditoria_equipe']);
        }
        else
        {
            $qr_sql = "
				INSERT INTO gestao.relatorio_auditoria_equipe
					 (
					  cd_relatorio_auditoria,
					  cd_usuario,
					  tipo,
					  cd_usuario_inclusao
					 )
			    VALUES
					 (
					  ".intval($args['cd_relatorio_auditoria']).",
					  " . (trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])) . ",
					  " . (trim($args['tipo']) == "" ? "DEFAULT" : "'".(trim($args['tipo'])). "'") . ",
					  ".intval($args['cd_usuario_inclusao'])."
					 );";
        }
        
        $this->db->query($qr_sql);
    }
    
    function excluir_equipe(&$result, $args=array())
    {
        $qr_sql = "
			UPDATE gestao.relatorio_auditoria_equipe
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario_exclusao']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_relatorio_auditoria_equipe = ".intval($args['cd_relatorio_auditoria_equipe']);
        
        $this->db->query($qr_sql);
    }
    
    function carrega_equipe(&$result, $args=array())
    {
        $qr_sql = "
			SELECT cd_relatorio_auditoria_equipe,
				   cd_usuario,
				   tipo
			  FROM gestao.relatorio_auditoria_equipe
			 WHERE cd_relatorio_auditoria_equipe = ".intval($args['cd_relatorio_auditoria_equipe']);
        
        $result = $this->db->query($qr_sql);
    }    
    
    function processo_constatacao(&$result, $args=array())
    {
        $qr_sql = "
			SELECT p.procedimento AS text, 
				   p.cd_processo AS value
			  FROM gestao.relatorio_auditoria_processo rap
			  JOIN projetos.processos p
			    ON rap.cd_processo = p.cd_processo
			 WHERE rap.cd_relatorio_auditoria = ".intval($args['cd_relatorio_auditoria']);
        
        $result = $this->db->query($qr_sql);
    }
    
    function salvar_constatacao(&$result, $args=array())
    {
        if($args["cd_relatorio_auditoria_constatacao"] > 0)
        {
            $qr_sql = "
				UPDATE gestao.relatorio_auditoria_constatacao
				   SET relato               = " . (trim($args['relato']) == "" ? "DEFAULT" : "'".(trim($args['relato'])). "'") . ",
					   cd_processo          = " . (trim($args['cd_processo']) == "" ? "DEFAULT" : intval($args['cd_processo'])) . ",
					   evidencias           = " . (trim($args['evidencias']) == "" ? "DEFAULT" : "'".(trim($args['evidencias'])). "'") . ",
					   tipo                 = " . (trim($args['tipo']) == "" ? "DEFAULT" : "'".(trim($args['tipo'])). "'") . ",
					   fl_impacto           = " . (trim($args['fl_impacto']) == "" ? "DEFAULT" : "'".(trim($args['fl_impacto'])). "'") . ",
					   nr_ano_nc            = " . (trim($args['nr_ano_nc']) == "" ? "DEFAULT" : intval($args['nr_ano_nc'])) . ",
					   nr_nc                = " . (trim($args['nr_nc']) == "" ? "DEFAULT" : intval($args['nr_nc'])) . ",
					   cd_usuario_alteracao = ".intval($args['cd_usuario_inclusao']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_relatorio_auditoria             = ".intval($args['cd_relatorio_auditoria'])."
				   AND cd_relatorio_auditoria_constatacao = ".intval($args['cd_relatorio_auditoria_constatacao']);
        }
        else
        {
            $qr_sql = "
				INSERT INTO gestao.relatorio_auditoria_constatacao
					 (
					  cd_relatorio_auditoria,
					  relato,
					  cd_processo,
					  evidencias,
					  tipo,
					  fl_impacto,
					  cd_usuario_inclusao,
					  cd_usuario_alteracao
					 )
			    VALUES
					 (
					  ".intval($args['cd_relatorio_auditoria']).",
					  " . (trim($args['relato']) == "" ? "DEFAULT" : "'".(trim($args['relato'])). "'") . ",
					  " . (trim($args['cd_processo']) == "" ? "DEFAULT" : intval($args['cd_processo'])) . ",
					  " . (trim($args['evidencias']) == "" ? "DEFAULT" : "'".(trim($args['evidencias'])). "'") . ",
					  " . (trim($args['tipo']) == "" ? "DEFAULT" : "'".(trim($args['tipo'])). "'") . ",
					  " . (trim($args['fl_impacto']) == "" ? "DEFAULT" : "'".(trim($args['fl_impacto'])). "'") . ",
					  ".intval($args['cd_usuario_inclusao']).",
					  ".intval($args['cd_usuario_inclusao'])."
					 );";
        }
        
        $this->db->query($qr_sql);
    }
    
    function lista_constatacao(&$result, $args=array())
    {
        $qr_sql = "
					SELECT rac.cd_relatorio_auditoria_constatacao,
						   rac.relato,
						   rac.evidencias,
						   rac.cd_usuario_inclusao,
						   rac.cd_usuario_alteracao,
						   p.procedimento,
						   u.nome,
						   ua.nome AS usuario_alteracao,
						   TO_CHAR(rac.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   TO_CHAR(rac.dt_alteracao,'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
						   rac.tipo AS cd_tipo,
						   CASE WHEN rac.tipo = 'N' THEN 'Não conformidade'
						        WHEN rac.tipo = 'O' THEN 'Observação'
						        WHEN rac.tipo = 'M' THEN 'Oportunidade de Melhoria'
								ELSE ''
						   END AS tipo,
						   CASE WHEN rac.tipo = 'N' THEN 'label-important'
						        WHEN rac.tipo = 'O' THEN 'label-warning'
								WHEN rac.tipo = 'M' THEN 'label-success'
								ELSE 'label-inverse'
						   END AS tipo_label,						   
						   CASE WHEN rac.tipo = 'N' THEN ''
								WHEN rac.fl_impacto = 'N' AND rac.tipo <> 'N'  THEN 'Não'
								WHEN rac.fl_impacto = 'S' THEN 'Sim'
								ELSE ''
						   END AS fl_impacto,
						   CASE WHEN rac.tipo = 'N' THEN ''
								WHEN rac.fl_impacto = 'N' AND rac.tipo <> 'N'  THEN ''
								WHEN rac.fl_impacto = 'S' THEN 'label-warning'
								ELSE ''
						   END AS fl_impacto_label,
						   funcoes.nr_nc(rac.nr_ano_nc, rac.nr_nc) AS ds_nao_conformidade				   
					  FROM gestao.relatorio_auditoria_constatacao rac
					  JOIN projetos.processos p
						ON rac.cd_processo = p.cd_processo
					  JOIN projetos.usuarios_controledi u
						ON u.codigo = rac.cd_usuario_inclusao
					  LEFT JOIN projetos.usuarios_controledi ua
						ON ua.codigo = rac.cd_usuario_alteracao				
					 WHERE rac.dt_exclusao IS NULL 
					   AND rac.cd_relatorio_auditoria = ". intval($args['cd_relatorio_auditoria'])."
					 ORDER BY rac.dt_inclusao		
		          ";
        
        $result = $this->db->query($qr_sql);
    }
    
    function excluir_constatacao(&$result, $args=array())
    {
        $qr_sql = "
			UPDATE gestao.relatorio_auditoria_constatacao
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario_exclusao']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_relatorio_auditoria_constatacao = ".intval($args['cd_relatorio_auditoria_constatacao']);
        
        $this->db->query($qr_sql);
    }

    public function concluir_constatacao($cd_relatorio_auditoria, $cd_usuario)
    {
    	$qr_sql = "
			UPDATE gestao.relatorio_auditoria
			   SET cd_usuario_constatacao = ".intval($cd_usuario).",
				   dt_constatacao         = CURRENT_TIMESTAMP
			 WHERE cd_relatorio_auditoria = ".intval($cd_relatorio_auditoria);
        
        $this->db->query($qr_sql);
    }
    
    function carrega_constatacao(&$result, $args=array())
    {
        $qr_sql = "
			SELECT relato,
				   cd_processo,
				   evidencias,
				   tipo,
				   fl_impacto,
				   nr_nc,
				   nr_ano_nc
			  FROM gestao.relatorio_auditoria_constatacao
			 WHERE cd_relatorio_auditoria_constatacao = ".intval($args['cd_relatorio_auditoria_constatacao']);
        
        $result = $this->db->query($qr_sql);
    }
    
    function get_auditor_relatorios(&$result, $args=array())
    {
        $qr_sql = "
			SELECT codigo AS value,
			  	   nome   AS text
			  FROM projetos.usuarios_controledi u
			  JOIN gestao.relatorio_auditoria ra
			    ON u.codigo = ra.cd_auditor_lider
			 WHERE ra.dt_exclusao IS NULL";
        
        $result = $this->db->query($qr_sql);
    }
    
    function get_processos_relatorios(&$result, $args=array())
    {
        $qr_sql = "
			SELECT p.procedimento AS text, 
				   p.cd_processo  AS value
			  FROM gestao.relatorio_auditoria_processo rap
			  JOIN projetos.processos p
			    ON rap.cd_processo = p.cd_processo";
        $result = $this->db->query($qr_sql);
    }
    
    function get_equipe_relatorios(&$result, $args=array())
    {
        $qr_sql = "
			SELECT rae.cd_usuario AS value,
				   u.nome AS text
			  FROM gestao.relatorio_auditoria_equipe rae
			  JOIN projetos.usuarios_controledi u
			    ON u.codigo = rae.cd_usuario
			 WHERE rae.dt_exclusao IS NULL";
        
        $result = $this->db->query($qr_sql);
    }
    
    function lista_pdf( &$result, $args=array())
    {
        $qr_sql = "
			SELECT ra.cd_relatorio_auditoria,
				   ra.nr_ano || '/' || TO_CHAR(ra.nr_mes, 'FM00') AS ano_mes,
				   ra.escopo,
				   funcoes.get_usuario_nome(ra.cd_auditor_lider) AS auditor_lider,
				   ra.conclusao,
				   ra.representante,
				   ra.ds_empresa,
				   (CASE WHEN ra.fl_tipo = 'I' 
				         THEN 'Interna'
				         ELSE 'Externa'
				   END) AS ds_tipo,
				   ra.fl_tipo
			  FROM gestao.relatorio_auditoria ra
			 WHERE dt_exclusao IS NULL
			   AND cd_relatorio_auditoria = ".intval($args['cd_relatorio_auditoria']);

        $result = $this->db->query($qr_sql);
    }
	
	function total_constatacao(&$result, $args=array())
	{
		$qr_sql = "
			SELECT SUM (x.tl_nao_conformidade) AS tl_nao_conformidade,
				   SUM (x.tl_melhoria) AS tl_melhoria,
				   SUM (x.tl_observacao) AS tl_observacao
			  FROM (
			SELECT COUNT(*) AS tl_nao_conformidade,
			       0 AS tl_melhoria,
				   0 AS tl_observacao
			  FROM gestao.relatorio_auditoria_constatacao
			 WHERE tipo = 'N'
			   AND dt_exclusao IS NULL
			   AND cd_relatorio_auditoria = ".intval($args['cd_relatorio_auditoria'])."
			 UNION
			SELECT 0 AS tl_nao_conformidade,
			       COUNT(*) AS tl_melhoria,
				   0 AS tl_observacao
			  FROM gestao.relatorio_auditoria_constatacao
			 WHERE tipo = 'M'
			   AND dt_exclusao IS NULL
			   AND cd_relatorio_auditoria = ".intval($args['cd_relatorio_auditoria'])."
			 UNION			 
			 SELECT 0 AS tl_nao_conformidade,
			        0 AS tl_melhoria,
					COUNT(*) AS tl_observacao
			   FROM gestao.relatorio_auditoria_constatacao
			  WHERE tipo = 'O' 
				AND dt_exclusao IS NULL
				AND cd_relatorio_auditoria = ".intval($args['cd_relatorio_auditoria']).") x";
		
		$result = $this->db->query($qr_sql);
	}
	
	function lista_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ac.cd_relatorio_auditoria_acompanhamento,
			       ac.descricao,
				   TO_CHAR(ac.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome
			  FROM gestao.relatorio_auditoria_acompanhamento ac
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = ac.cd_usuario_inclusao
			 WHERE ac.dt_exclusao IS NULL
			   AND ac.cd_relatorio_auditoria = ".intval($args['cd_relatorio_auditoria'])."
			 ORDER BY ac.dt_inclusao DESC";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO gestao.relatorio_auditoria_acompanhamento
				 (
				   cd_relatorio_auditoria,
				   descricao,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
			VALUES
				 (
				   ".intval($args['cd_relatorio_auditoria']).",
				   '".trim($args['descricao'])."',
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";
		
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.relatorio_auditoria_acompanhamento
			   SET dt_exclusao = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_relatorio_auditoria_acompanhamento = ".intval($args['cd_relatorio_auditoria_acompanhamento']);
			 
		$result = $this->db->query($qr_sql);
	}
	
	function lista_anexo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT an.cd_relatorio_auditoria_anexo,
			       an.arquivo,
				   an.arquivo_nome,
				   uc.nome,
				   TO_CHAR(an.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM gestao.relatorio_auditoria_anexo an
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = an.cd_usuario_inclusao
			 WHERE an.dt_exclusao IS NULL
			   AND an.cd_relatorio_auditoria = ".intval($args['cd_relatorio_auditoria'])."";
			
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO gestao.relatorio_auditoria_anexo
			     (
					cd_relatorio_auditoria,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['cd_relatorio_auditoria']).",
					".str_escape($args['arquivo']).",
					".str_escape($args['arquivo_nome']).",
					".intval($args['cd_usuario'])."
				 )";
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_anexo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.relatorio_auditoria_anexo
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_relatorio_auditoria_anexo = ".intval($args['cd_relatorio_auditoria_anexo']).";";
		$this->db->query($qr_sql);
	}
}

?>