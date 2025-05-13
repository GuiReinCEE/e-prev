<?php
class Planejamento_estrategico_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_planejamento_estrategico,
				   ds_diretriz_fundamental,
			       nr_ano_inicial,
			       nr_ano_final,
			       arquivo,
			       arquivo_nome
			  FROM gestao.planejamento_estrategico
		     WHERE dt_exclusao IS NULL
		     ".(((intval($args["nr_ano_inicial"]) != "") AND (intval($args["nr_ano_final"]) != "")) ? " AND nr_ano_inicial  BETWEEN ".intval($args["nr_ano_inicial"])." AND ".intval($args["nr_ano_final"]) : "")."
			 ".(((intval($args["nr_ano_inicial"]) != "") AND (intval($args["nr_ano_final"]) != "")) ? " AND nr_ano_final  BETWEEN ".intval($args["nr_ano_inicial"])." AND ".intval($args["nr_ano_final"]) : "")."			 
			ORDER BY nr_ano_inicial DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_planejamento_estrategico)
	{
		$qr_sql = "
			SELECT cd_planejamento_estrategico,
				   ds_diretriz_fundamental,
				   nr_ano_inicial,
				   nr_ano_final,
				   arquivo,
				   arquivo_nome,
				   arquivo_plano_execucao,
                   arquivo_plano_execucao_nome
			  FROM gestao.planejamento_estrategico
			 WHERE cd_planejamento_estrategico = ".intval($cd_planejamento_estrategico).";";
			
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
    {
		$cd_planejamento_estrategico = intval($this->db->get_new_id('gestao.planejamento_estrategico', 'cd_planejamento_estrategico'));

        $qr_sql = "
            INSERT INTO gestao.planejamento_estrategico
                 (
                 	cd_planejamento_estrategico,
                    ds_diretriz_fundamental,
                    nr_ano_inicial,
				    nr_ano_final,
                    arquivo, 
                    arquivo_nome, 
                    arquivo_plano_execucao,
                    arquivo_plano_execucao_nome,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES 
                 (	
                 	".intval($cd_planejamento_estrategico).",
                    ".(trim($args['ds_diretriz_fundamental']) != '' ? str_escape($args['ds_diretriz_fundamental']) : "DEFAULT").",
                    ".(trim($args['nr_ano_inicial']) != '' ? intval($args['nr_ano_inicial']) : 'DEFAULT').",
                    ".(trim($args['nr_ano_final']) != '' ? intval($args['nr_ano_final']) : 'DEFAULT').",
                    '".trim($args['arquivo'])."',
                    '".trim($args['arquivo_nome'])."',
                    ".(trim($args['arquivo_plano_execucao']) != '' ? str_escape($args['arquivo_plano_execucao']) : "DEFAULT").",
                    ".(trim($args['arquivo_plano_execucao_nome']) != '' ? str_escape($args['arquivo_plano_execucao_nome']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 )";

        $this->db->query($qr_sql);

        return $cd_planejamento_estrategico;
    }	

	public function salvar_cronograma($args = array())
    {
        $qr_sql = "
            INSERT INTO gestao.programa_projeto_arquivo
                 (
				    ds_programa_projeto_arquivo, 
				    cd_programa_projeto, 
		            nr_ano, 
					cd_pendencia_gestao,
		            arquivo, 
		            arquivo_nome, 
		            cd_usuario_inclusao, 
		            cd_usuario_alteracao
                 )
            VALUES 
                 (	
				    ".(trim($args['ds_programa_projeto_arquivo']) != '' ? str_escape($args['ds_programa_projeto_arquivo']) : "DEFAULT").", 
		            ".intval($args['cd_programa_projeto']).",
 					".(trim($args['nr_ano']) != '' ? intval($args['nr_ano']) : "DEFAULT").",
 					".(trim($args['cd_pendencia_gestao']) != '' ? intval($args['cd_pendencia_gestao']) : "DEFAULT").",
                    '".trim($args['arquivo'])."',
                    '".trim($args['arquivo_nome'])."',
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 )";

        $this->db->query($qr_sql);
    }	

	public function atualizar($cd_planejamento_estrategico, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.planejamento_estrategico
			   SET ds_diretriz_fundamental     = ".(trim($args['ds_diretriz_fundamental']) != '' ? "'".trim($args['ds_diretriz_fundamental'])."'" : "DEFAULT").",
			       nr_ano_inicial              = ".(trim($args['nr_ano_inicial']) != '' ? intval($args['nr_ano_inicial']) : 'DEFAULT').",   
			       nr_ano_final                = ".(trim($args['nr_ano_final']) != '' ? intval($args['nr_ano_final']) : 'DEFAULT').",
			       arquivo                     = ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
			       arquivo_nome                = ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
			       arquivo_plano_execucao 	   = ".(trim($args['arquivo_plano_execucao']) != '' ? str_escape($args['arquivo_plano_execucao']) : "DEFAULT").",
                   arquivo_plano_execucao_nome = ".(trim($args['arquivo_plano_execucao_nome']) != '' ? str_escape($args['arquivo_plano_execucao_nome']) : "DEFAULT").",
			       cd_usuario_alteracao	       = ".intval($args['cd_usuario']).",
			       dt_alteracao                = CURRENT_TIMESTAMP
			 WHERE cd_planejamento_estrategico = ".intval($cd_planejamento_estrategico).";";

		$this->db->query($qr_sql);
	}

	public function listar_desdobramentos($cd_planejamento_estrategico)
	{
		$qr_sql = "
			SELECT cd_planejamento_estrategico,
				   cd_planejamento_estrategico_desdobramento,
			       cd_planejamento_estrategico_desdobramento AS value,
			       ds_planejamento_estrategico_desdobramento AS text,
			       nr_ordem,
			       ds_planejamento_estrategico_desdobramento,
			       TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM gestao.planejamento_estrategico_desdobramento
			 WHERE cd_planejamento_estrategico = ".intval($cd_planejamento_estrategico)."
			   AND dt_exclusao IS NULL
			 ORDER BY nr_ordem ASC;";
		
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_desdobramentos($cd_planejamento_estrategico_desdobramento)
	{
		$qr_sql = "
			SELECT cd_planejamento_estrategico,
			       cd_planejamento_estrategico_desdobramento,
			       nr_ordem,
			       ds_planejamento_estrategico_desdobramento,
			       TO_CHAR(dt_inclusao, 'DD/MM/YYYY') AS dt_inclusao
			  FROM gestao.planejamento_estrategico_desdobramento 
			 WHERE cd_planejamento_estrategico_desdobramento = ".intval($cd_planejamento_estrategico_desdobramento).";";
		
		return $this->db->query($qr_sql)->row_array();
	}	

    public function salvar_desdobramento($cd_planejamento_estrategico_desdobramento = 0 ,$args = array())
    { 
       	$cd_planejamento_estrategico_desdobramento = intval($this->db->get_new_id('gestao.planejamento_estrategico_desdobramento', 'cd_planejamento_estrategico_desdobramento'));

        $qr_sql = "
			INSERT INTO gestao.planejamento_estrategico_desdobramento
				(	
					cd_planejamento_estrategico_desdobramento,
					cd_planejamento_estrategico,
		            ds_planejamento_estrategico_desdobramento,
		            nr_ordem, 
		            cd_usuario_inclusao, 
		            cd_usuario_alteracao
        		)
    		VALUES 
				( 
					".intval($cd_planejamento_estrategico_desdobramento).",
					".intval($args['cd_planejamento_estrategico']).",
		            ".(trim($args['ds_planejamento_estrategico_desdobramento']) != '' ? str_escape($args['ds_planejamento_estrategico_desdobramento']) : "DEFAULT").",
		            ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : 'DEFAULT').",
		            ".intval($args['cd_usuario']).",
		            ".intval($args['cd_usuario'])." 
        		);";

        if(count($args['objetivo']) > 0)
        {
            $qr_sql .= "
                INSERT INTO gestao.planejamento_estrategico_desdobramento_objetivo(cd_planejamento_estrategico_desdobramento, cd_planejamento_estrategico_objetivo, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_planejamento_estrategico_desdobramento).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['objetivo']).")) x;";
        }

        $this->db->query($qr_sql);

        return $cd_planejamento_estrategico_desdobramento;
    }	

    public function atualizar_desdobramento($cd_planejamento_estrategico_desdobramento, $args)
    {
    	$qr_sql = "
    		UPDATE gestao.planejamento_estrategico_desdobramento
    		   SET ds_planejamento_estrategico_desdobramento = ".(trim($args['ds_planejamento_estrategico_desdobramento']) != '' ? str_escape($args['ds_planejamento_estrategico_desdobramento']) : "DEFAULT").",
    		       cd_usuario_alteracao	     				 = ".intval($args['cd_usuario']).",
    		       dt_alteracao 							 = CURRENT_TIMESTAMP
    		 WHERE cd_planejamento_estrategico_desdobramento = ".intval($cd_planejamento_estrategico_desdobramento).";";	 
    
    	if(count($args['objetivo']) > 0)
    	{
    		$qr_sql .= "
                UPDATE gestao.planejamento_estrategico_desdobramento_objetivo
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_planejamento_estrategico_desdobramento = ".intval($cd_planejamento_estrategico_desdobramento)."
                   AND dt_exclusao IS NULL
                   AND cd_planejamento_estrategico_objetivo NOT IN (".implode(",", $args['objetivo']).");
       
                INSERT INTO gestao.planejamento_estrategico_desdobramento_objetivo
                (
                    cd_planejamento_estrategico_desdobramento, 
                    cd_planejamento_estrategico_objetivo, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                )
                SELECT ".intval($cd_planejamento_estrategico_desdobramento).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['objetivo']).")) x
                 WHERE x.column1 NOT IN (
                                        SELECT a.cd_planejamento_estrategico_objetivo
                                          FROM gestao.planejamento_estrategico_desdobramento_objetivo a
                                         WHERE a.cd_planejamento_estrategico_desdobramento = ".intval($cd_planejamento_estrategico_desdobramento)."
                                           AND a.dt_exclusao IS NULL);";    			
    		
    	}

    	$this->db->query($qr_sql);
    }

	public function listar_objetivo($cd_planejamento_estrategico)
	{
		$qr_sql = "
			SELECT cd_planejamento_estrategico,
			       cd_planejamento_estrategico_objetivo AS value,
			       ds_planejamento_estrategico_objetivo AS text,
			       ds_planejamento_estrategico_objetivo,
			       TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM gestao.planejamento_estrategico_objetivo
			 WHERE cd_planejamento_estrategico = ".intval($cd_planejamento_estrategico)."
			   AND dt_exclusao IS NULL 
			 ORDER BY ds_planejamento_estrategico_objetivo ASC;";
		
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_objetivos($cd_planejamento_estrategico_objetivo)
	{
		$qr_sql = "
			SELECT cd_planejamento_estrategico,
			       cd_planejamento_estrategico_objetivo,
			       ds_planejamento_estrategico_objetivo,
			       TO_CHAR(dt_inclusao, 'DD/MM/YYYY') AS dt_inclusao
			  FROM gestao.planejamento_estrategico_objetivo
			 WHERE cd_planejamento_estrategico_objetivo = ".intval($cd_planejamento_estrategico_objetivo).";";
		
		return $this->db->query($qr_sql)->row_array();
	}

    public function salvar_objetivo($args = array())
    {
    	$qr_sql = "
			INSERT INTO gestao.planejamento_estrategico_objetivo
				(
		            cd_planejamento_estrategico, 
		            ds_planejamento_estrategico_objetivo, 
		            cd_usuario_inclusao, 
		            cd_usuario_alteracao
        		)
    		VALUES 
				( 
					".intval($args['cd_planejamento_estrategico']).",
		            ".(trim($args['ds_planejamento_estrategico_objetivo']) != '' ? str_escape($args['ds_planejamento_estrategico_objetivo']) : "DEFAULT").",
		            ".intval($args['cd_usuario']).",
		            ".intval($args['cd_usuario'])." 
        		)";    		
    	
    	$this->db->query($qr_sql);
    }

    public function atualizar_objetivo($cd_planejamento_estrategico_objetivo, $args)
    {
    	$qr_sql = "
    		UPDATE gestao.planejamento_estrategico_objetivo
    		   SET ds_planejamento_estrategico_objetivo      = ".(trim($args['ds_planejamento_estrategico_objetivo']) != '' ? str_escape($args['ds_planejamento_estrategico_objetivo']) : "DEFAULT").",
    		       cd_usuario_alteracao	     				 = ".intval($args['cd_usuario']).",
    		       dt_alteracao 							 = CURRENT_TIMESTAMP
    		 WHERE cd_planejamento_estrategico_objetivo 	 = ".intval($cd_planejamento_estrategico_objetivo).";";	 
    	
    	$this->db->query($qr_sql);    	
    }

	public function listar_programa($cd_planejamento_estrategico)
	{
		$qr_sql = "
			SELECT pp.cd_programa_projeto,
			       pp.ds_programa_projeto,
			       pp.cd_gerencia_responsavel,
			       pp.cd_planejamento_estrategico,
			       pp.nr_ordem,
			       TO_CHAR(pp.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       pe.nr_ano_inicial,
			       pe.nr_ano_final,
			       ped.nr_ordem AS nr_ordem_desdobramento,
			       ped.ds_planejamento_estrategico_desdobramento
			  FROM gestao.programa_projeto pp
			  JOIN gestao.planejamento_estrategico pe
			    ON pe.cd_planejamento_estrategico = pp.cd_planejamento_estrategico
			  JOIN gestao.planejamento_estrategico_desdobramento ped 
			    ON ped.cd_planejamento_estrategico_desdobramento = pp.cd_planejamento_estrategico_desdobramento
			 WHERE pp.cd_planejamento_estrategico = ".intval($cd_planejamento_estrategico)."
			   AND pp.dt_exclusao IS NULL
			 ORDER BY ped.nr_ordem ASC, pp.nr_ordem";	
		
		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_cronograma($cd_programa_projeto)
	{
		$qr_sql = "
			SELECT ppa.cd_programa_projeto,
				   ppa.cd_programa_projeto_arquivo,
				   ppa.ds_programa_projeto_arquivo,
				   ppa.arquivo,
				   ppa.arquivo_nome,
				   ppa.nr_ano,
				   TO_CHAR(pp.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   pp.cd_planejamento_estrategico,
				   ppa.cd_pendencia_gestao
			  FROM gestao.programa_projeto_arquivo ppa
			  JOIN gestao.programa_projeto pp
			    ON pp.cd_programa_projeto = ppa.cd_programa_projeto
			 WHERE ppa.cd_programa_projeto = ".intval($cd_programa_projeto)."
			   AND ppa.dt_exclusao IS NULL";
		
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_cronograma($cd_programa_projeto_arquivo)
	{
		$qr_sql = "
			SELECT ppa.cd_programa_projeto,
				   ppa.cd_programa_projeto_arquivo,
				   ppa.ds_programa_projeto_arquivo,
				   ppa.nr_ano,
				   ppa.arquivo,
				   ppa.arquivo_nome,
				   TO_CHAR(pp.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   pp.cd_planejamento_estrategico,
				   ppa.cd_pendencia_gestao
			  FROM gestao.programa_projeto_arquivo ppa
			  JOIN gestao.programa_projeto pp
			    ON pp.cd_programa_projeto = ppa.cd_programa_projeto
			 WHERE ppa.cd_programa_projeto_arquivo = ".intval($cd_programa_projeto_arquivo)."
			   AND ppa.dt_exclusao IS NULL";
		
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega_programa($cd_programa_projeto)
	{
		$qr_sql = "
			SELECT pp.cd_programa_projeto,
			       pp.ds_programa_projeto,
			       pp.cd_gerencia_responsavel,
			       TO_CHAR(pp.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       pp.cd_planejamento_estrategico,
			       pp.cd_planejamento_estrategico_desdobramento,
				   pp.nr_ordem,			       
			       ped.ds_planejamento_estrategico_desdobramento,
			       peo.ds_planejamento_estrategico_objetivo,
			       pe.ds_diretriz_fundamental,
			       pe.nr_ano_inicial,
			       pe.nr_ano_final
			  FROM gestao.programa_projeto pp
			  JOIN gestao.planejamento_estrategico pe
			    ON pe.cd_planejamento_estrategico = pp.cd_planejamento_estrategico
			  JOIN gestao.planejamento_estrategico_desdobramento ped 
			    ON ped.cd_planejamento_estrategico_desdobramento = pp.cd_planejamento_estrategico_desdobramento
			  JOIN gestao.planejamento_estrategico_objetivo peo 
			    ON peo.cd_planejamento_estrategico = pp.cd_planejamento_estrategico
			 WHERE pp.cd_programa_projeto = ".intval($cd_programa_projeto)."";

		return $this->db->query($qr_sql)->row_array();
	}

    public function salvar_programa($args = array())
    {
    	$qr_sql = " 
    		INSERT INTO gestao.programa_projeto
				(
		            cd_planejamento_estrategico, 
		            ds_programa_projeto,
		            cd_gerencia_responsavel,
		            cd_planejamento_estrategico_desdobramento,
		            nr_ordem, 
		            cd_usuario_inclusao, 
		            cd_usuario_alteracao
        		)
    		VALUES 
				( 
					".intval($args['cd_planejamento_estrategico']).",
		            ".(trim($args['ds_programa_projeto']) != '' ? str_escape($args['ds_programa_projeto']) : "DEFAULT").",
		            ".str_escape($args['cd_gerencia_responsavel']).",
		            ".(trim($args['cd_planejamento_estrategico_desdobramento']) != '' ? "'".trim($args['cd_planejamento_estrategico_desdobramento'])."'" : "DEFAULT").",
		            ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : 'DEFAULT').",
		            ".intval($args['cd_usuario']).",
		            ".intval($args['cd_usuario'])." 
        		)";    	

        $this->db->query($qr_sql);
    }

    public function atualizar_programa($cd_programa_projeto, $args = array())
    {
    	$qr_sql = "
			UPDATE gestao.programa_projeto
			   SET ds_programa_projeto         = ".(trim($args['ds_programa_projeto']) != '' ? "'".trim($args['ds_programa_projeto'])."'" : "DEFAULT").",
			       cd_gerencia_responsavel = ".str_escape($args['cd_gerencia_responsavel']).",
			       cd_planejamento_estrategico_desdobramento = ".(trim($args['cd_planejamento_estrategico_desdobramento']) != '' ? intval($args['cd_planejamento_estrategico_desdobramento']) : "DEFAULT").",
			       cd_usuario_alteracao	       = ".intval($args['cd_usuario']).",
			       dt_alteracao                = CURRENT_TIMESTAMP
			 WHERE cd_programa_projeto 		   = ".intval($cd_programa_projeto).";";

		$this->db->query($qr_sql);
    }

    public function atualizar_cronograma($cd_programa_projeto_arquivo, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.programa_projeto_arquivo
			   SET ds_programa_projeto_arquivo = ".(trim($args['ds_programa_projeto_arquivo']) != '' ? str_escape($args['ds_programa_projeto_arquivo']) : "DEFAULT").",  
			       nr_ano                      = ".(trim($args['nr_ano']) != '' ? intval($args['nr_ano']) : 'DEFAULT').",
			       cd_pendencia_gestao         = ".(trim($args['cd_pendencia_gestao']) != '' ? intval($args['cd_pendencia_gestao']) : 'DEFAULT').",
			       arquivo                     = ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
			       arquivo_nome                = ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
			       cd_usuario_alteracao	       = ".intval($args['cd_usuario']).",
			       dt_alteracao                = CURRENT_TIMESTAMP
			 WHERE cd_programa_projeto_arquivo = ".intval($cd_programa_projeto_arquivo).";";

		$this->db->query($qr_sql);
	}

    public function get_gerencia()
    {
    	$qr_sql = "
			SELECT codigo AS value,
			       nome AS text
			  FROM funcoes.get_gerencias_vigente()	
			 ORDER BY nome;";

		return $this->db->query($qr_sql)->result_array();	 
    }

    public function listar_desdobramentos_objetivo($cd_planejamento_estrategico_desdobramento)
	{
		$qr_sql = "
			SELECT peo.ds_planejamento_estrategico_objetivo,
				   pedo.cd_planejamento_estrategico_objetivo
			  FROM gestao.planejamento_estrategico_desdobramento_objetivo pedo
			  JOIN gestao.planejamento_estrategico_objetivo peo
			    ON peo.cd_planejamento_estrategico_objetivo = pedo.cd_planejamento_estrategico_objetivo
			 WHERE pedo.dt_exclusao IS NULL
			   AND pedo.cd_planejamento_estrategico_desdobramento = ".intval($cd_planejamento_estrategico_desdobramento)."
			 ORDER BY peo.ds_planejamento_estrategico_objetivo ASC;";
		
		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_desdobramentos_programa_projeto($cd_planejamento_estrategico_desdobramento)
	{
		$qr_sql = "
			SELECT pp.cd_programa_projeto,
			       pp.ds_programa_projeto,
			       pp.cd_gerencia_responsavel,
			       TO_CHAR(pp.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       pp.cd_planejamento_estrategico
			  FROM gestao.programa_projeto pp
			 WHERE pp.cd_planejamento_estrategico_desdobramento = ".intval($cd_planejamento_estrategico_desdobramento)."
			   AND pp.dt_exclusao IS NULL
			 ORDER BY pp.nr_ordem ASC";	
		
		return $this->db->query($qr_sql)->result_array();
	}

	public function get_cronograma($cd_programa_projeto, $ano)
	{
		$qr_sql = "
			SELECT ds_programa_projeto_arquivo,
			       arquivo,
				   cd_pendencia_gestao
			  FROM gestao.programa_projeto_arquivo
			 WHERE cd_programa_projeto = ".intval($cd_programa_projeto)."
			   AND nr_ano              = ".intval($ano).";";

		return $this->db->query($qr_sql)->row_array();
	}
}