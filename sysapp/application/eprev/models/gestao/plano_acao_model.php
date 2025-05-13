<?php
class Plano_acao_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

    public function get_usuarios($cd_divisao)
 	{
		$qr_sql = "
		    SELECT codigo AS value,
				   nome AS text
			  FROM projetos.usuarios_controledi 
			 WHERE divisao = '".trim($cd_divisao)."'
			   AND tipo NOT IN ('X');";
				  
		return $this->db->query($qr_sql)->result_array();
	}

	public function get_gerente($cd_divisao)
	{
		$qr_sql = "SELECT get_usuario_gerente AS cd_usuario FROM funcoes.get_usuario_gerente('".$cd_divisao."');";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_substituto($cd_divisao)
	{
		$qr_sql = "SELECT get_usuario_gerente_substituto AS cd_usuario FROM funcoes.get_usuario_gerente_substituto('".$cd_divisao."');";

		return $this->db->query($qr_sql)->row_array();
	}

   	public function get_processo()
	{
		$qr_sql = "
			SELECT cd_processo AS value,
                   ds_procedimento AS text
			  FROM funcoes.get_processos_vigente();";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_numero_plano_acao($nr_ano)
	{
		$qr_sql = "
			SELECT COALESCE(MAX(nr_plano_acao), 0) + 1 AS nr_plano_acao
		      FROM gestao.plano_acao
		     WHERE nr_ano      = ".intval($nr_ano)."
		       AND dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT pa.cd_plano_acao,
			       gestao.plano_acao_ano_numero(pa.nr_ano, pa.nr_plano_acao) AS ds_ano_numero,
			       pa.ds_situacao,
			       p.procedimento,
			       pa.ds_relatorio_auditoria,
			       TO_CHAR(pa.dt_envio_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_responsavel,
			       (SELECT COUNT(*)
			       	  FROM gestao.plano_acao_item pai
			       	 WHERE pai.cd_plano_acao = pa.cd_plano_acao
			       	   AND pai.dt_exclusao IS NULL
			       ) AS qt_itens,
			       (SELECT COUNT(*)
			       	  FROM gestao.plano_acao_acompanhamento paa
			       	  JOIN gestao.plano_acao_item pai1
			       	    ON paa.cd_plano_acao_item = pai1.cd_plano_acao_item
			       	 WHERE pai1.cd_plano_acao = pa.cd_plano_acao
			       	   AND paa.dt_exclusao IS NULL
			       	   AND paa.fl_status = 'E'
			       ) AS qt_encerrado,
			       (
				       (SELECT COUNT(*)
						  FROM gestao.plano_acao_item pai
						 WHERE pai.cd_plano_acao = pa.cd_plano_acao
						   AND pai.dt_exclusao IS NULL
					   ) 
				       -
				       (SELECT COUNT(*)
						  FROM gestao.plano_acao_acompanhamento paa
						  JOIN gestao.plano_acao_item pai1
						    ON paa.cd_plano_acao_item = pai1.cd_plano_acao_item
						 WHERE pai1.cd_plano_acao = pa.cd_plano_acao
						   AND paa.dt_exclusao IS NULL
						   AND paa.fl_status = 'E'
				       ) 
			       ) AS qt_n_encerrado
			  FROM gestao.plano_acao pa
	          LEFT JOIN projetos.processos p
		        ON p.cd_processo = pa.cd_processo
		     WHERE pa.dt_exclusao IS NULL
		       --AND p.dt_fim_vigencia IS NULL
		        ".(trim($args['ds_situacao']) != '' ? "AND UPPER(pa.ds_situacao) LIKE UPPER('%".trim($args['ds_situacao'])."%')" : "")."
		        ".(intval($args['cd_processo']) != '' ? "AND pa.cd_processo = ".intval($args['cd_processo'])."" : "")."
			    ".(intval($args['nr_plano_acao']) != '' ? "AND pa.nr_plano_acao = ".intval($args['nr_plano_acao']) : "")."
			    ".(intval($args['nr_ano']) != '' ? "AND pa.nr_ano = ".intval($args['nr_ano']) : "")."
			    ".(trim($args['dt_envio_responsavel']) == 'S' ? "AND pa.dt_envio_responsavel IS NOT NULL" : "")."
			    ".(trim($args['dt_envio_responsavel']) == 'N' ? "AND pa.dt_envio_responsavel IS NULL" : "")."
			 ORDER BY pa.nr_plano_acao ASC;";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_nr_item($cd_plano_acao)
	{
		$qr_sql = "
			SELECT (nr_plano_acao_item + 1) AS nr_plano_acao_item
			  FROM gestao.plano_acao_item
			 WHERE dt_exclusao IS NULL
               AND cd_plano_acao = ".intval($cd_plano_acao)."
			 ORDER BY nr_plano_acao_item DESC;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_plano_acao)
	{
		$qr_sql = "
			SELECT pa.cd_plano_acao,
			       gestao.plano_acao_ano_numero(pa.nr_ano, pa.nr_plano_acao) AS ds_ano_numero,
			       TO_CHAR(pa.dt_envio_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_responsavel,
			       pa.ds_situacao,
			       pa.cd_processo,
			       p.procedimento,
			       pa.ds_relatorio_auditoria,
			       (SELECT COUNT(*)
			       	  FROM gestao.plano_acao_item pai
			       	 WHERE pai.cd_plano_acao = pa.cd_plano_acao
			       	   AND pai.dt_exclusao IS NULL
			       ) AS qt_itens
			  FROM gestao.plano_acao pa
		      LEFT JOIN projetos.processos p
		        ON p.cd_processo = pa.cd_processo
		     WHERE pa.dt_exclusao IS NULL
		      -- AND p.dt_fim_vigencia IS NULL
			   AND cd_plano_acao = ".intval($cd_plano_acao).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_plano_acao = intval($this->db->get_new_id('gestao.plano_acao', 'cd_plano_acao'));

		$qr_sql = "
			INSERT INTO gestao.plano_acao
			     (
			       cd_plano_acao,
			       nr_ano,
			       nr_plano_acao,
			       ds_situacao,
			       cd_processo,
			       ds_relatorio_auditoria,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_plano_acao).",
			     	".(trim($args['nr_ano']) != '' ? intval($args['nr_ano']) : "DEFAULT").",
			     	".(trim($args['nr_plano_acao']) != '' ? intval($args['nr_plano_acao']) : "DEFAULT").",
			     	".(trim($args['ds_situacao']) != '' ? str_escape($args['ds_situacao']) : "DEFAULT").",
			     	".(trim($args['cd_processo']) != '' ? intval($args['cd_processo']) : "DEFAULT").",
			     	".(trim($args['ds_relatorio_auditoria']) != '' ? str_escape($args['ds_relatorio_auditoria']) : "DEFAULT").",
			        ".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
			     );";

		$this->db->query($qr_sql); 

		return $cd_plano_acao;
	}

	public function atualizar($cd_plano_acao, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.plano_acao
               SET ds_situacao			  = ".(trim($args['ds_situacao']) != '' ? str_escape($args['ds_situacao']) : "DEFAULT").",
			       cd_processo			  = ".(trim($args['cd_processo']) != '' ? intval($args['cd_processo']) : "DEFAULT").",
			       ds_relatorio_auditoria = ".(trim($args['ds_relatorio_auditoria']) != '' ? str_escape($args['ds_relatorio_auditoria']) : "DEFAULT").",
			       cd_usuario_alteracao   = ".intval($args['cd_usuario']).",
                   dt_alteracao           = CURRENT_TIMESTAMP
            WHERE cd_plano_acao = ".intval($cd_plano_acao).";";

        $this->db->query($qr_sql);  
	}

	public function set_ordem($cd_plano_acao_item, $args = array())
    {
      	$qr_sql = "
	        UPDATE gestao.plano_acao_item
	           SET nr_plano_acao_item   = ".(trim($args['nr_plano_acao_item']) != '' ? intval($args['nr_plano_acao_item']) : 'DEFAULT').",
	               cd_usuario_alteracao = ".intval($args['cd_usuario']).", 
	               dt_alteracao         = CURRENT_TIMESTAMP
	         WHERE cd_plano_acao_item = ".intval($cd_plano_acao_item).";";

      	$this->db->query($qr_sql);
    }

    public function set_ordem_recomendacao($cd_plano_acao_item_recomendacao, $args = array())
    {
      	$qr_sql = "
	        UPDATE gestao.plano_acao_item_recomendacao
	           SET nr_plano_acao_item_recomendacao   = ".(trim($args['nr_plano_acao_item_recomendacao']) != '' ? intval($args['nr_plano_acao_item_recomendacao']) : 'DEFAULT').",
	               cd_usuario_alteracao = ".intval($args['cd_usuario']).", 
	               dt_alteracao         = CURRENT_TIMESTAMP
	         WHERE cd_plano_acao_item_recomendacao = ".intval($cd_plano_acao_item_recomendacao).";";

      	$this->db->query($qr_sql);
    }

	public function carrega_item($cd_plano_acao_item)
	{
		$qr_sql = "
			SELECT pai.cd_plano_acao_item,
				   par.cd_plano_acao_resposta,
			       pai.nr_plano_acao_item,
			       gestao.plano_acao_ano_numero(pa.nr_ano, pa.nr_plano_acao) AS ds_ano_numero,
			       TO_CHAR(pai.dt_prazo, 'DD/MM/YYYY') AS dt_prazo,
			       pai.ds_constatacao,
			       pai.ds_recomendacao,
			       (SELECT (CASE WHEN paa.fl_status = 'E' THEN 'Encerrada'
    			   		         WHEN paa.fl_status = 'A' THEN 'Em Andamento'
    			   	             WHEN paa.fl_status = 'N' THEN 'Não Iniciada' 
    			   	       END) AS ds_status
    			   	  FROM gestao.plano_acao_acompanhamento paa
				     WHERE paa.cd_plano_acao_item = pai.cd_plano_acao_item
				       AND paa.dt_exclusao IS NULL
				     ORDER BY paa.dt_inclusao DESC 
				     LIMIT 1) AS ds_status,
				   (SELECT paa.fl_status
    			   	  FROM gestao.plano_acao_acompanhamento paa
				     WHERE paa.cd_plano_acao_item = pai.cd_plano_acao_item
				       AND paa.dt_exclusao IS NULL
				     ORDER BY paa.dt_inclusao DESC 
				     LIMIT 1) AS fl_status,
			       pai.cd_gerencia_responsavel,
			       pai.cd_usuario_responsavel,
			       pai.cd_usuario_substituto,
			       par.ds_acao
			  FROM gestao.plano_acao_item pai
			  JOIN gestao.plano_acao pa
			    ON pa.cd_plano_acao = pai.cd_plano_acao
			  LEFT JOIN gestao.plano_acao_resposta par
			    ON pai.cd_plano_acao_item = par.cd_plano_acao_item
			 WHERE pai.cd_plano_acao_item = ".intval($cd_plano_acao_item).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function listar_itens($cd_plano_acao, $cd_diretoria = '')
	{
		$qr_sql = "
			SELECT pai.cd_plano_acao_item,
				   pai.cd_plano_acao,
			       pai.nr_plano_acao_item, 
			       TO_CHAR(pai.dt_prazo,'DD/MM/YYYY') AS dt_prazo,
			       pai.ds_constatacao,
			       d.nome,
			       (SELECT paa.fl_status
    			   	  FROM gestao.plano_acao_acompanhamento paa
				     WHERE paa.cd_plano_acao_item = pai.cd_plano_acao_item
				       AND paa.dt_exclusao IS NULL
				     ORDER BY paa.dt_inclusao DESC 
				     LIMIT 1) AS fl_status,
			       (SELECT (CASE WHEN paa.fl_status = 'E' THEN 'Encerrada'
    			   		         WHEN paa.fl_status = 'A' THEN 'Em Andamento'
    			   	             WHEN paa.fl_status = 'N' THEN 'Não Iniciada' 
    			   	             ELSE 'Não Iniciada'
    			   	       END)
    			   	  FROM gestao.plano_acao_acompanhamento paa
				     WHERE paa.cd_plano_acao_item = pai.cd_plano_acao_item
				       AND paa.dt_exclusao IS NULL
				     ORDER BY paa.dt_inclusao DESC 
				     LIMIT 1) AS ds_status,
				   (SELECT (CASE WHEN fl_status = 'E' THEN 'success'
		    			   		 WHEN fl_status = 'A' THEN 'info'
		    			   	     WHEN fl_status = 'N' THEN 'important' 
		    			   	     ELSE 'important'
		    			   END)
    			   	  FROM gestao.plano_acao_acompanhamento paa
				     WHERE paa.cd_plano_acao_item = pai.cd_plano_acao_item
				       AND paa.dt_exclusao IS NULL
				     ORDER BY paa.dt_inclusao DESC 
				     LIMIT 1) AS ds_class,
    			   (SELECT TO_CHAR(paa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') ||' : '|| paa.ds_acompanhamento
    			   	  FROM gestao.plano_acao_acompanhamento paa
				     WHERE paa.cd_plano_acao_item = pai.cd_plano_acao_item
				       AND paa.dt_exclusao IS NULL
				     ORDER BY paa.dt_inclusao DESC 
				     LIMIT 1) AS ds_acompanhamento,
			       pai.cd_gerencia_responsavel,
			       funcoes.get_usuario(pai.cd_usuario_responsavel) AS ds_usuario_gerente,
			       funcoes.get_usuario(pai.cd_usuario_substituto) AS ds_usuario_substituto,
			       par.ds_acao
			  FROM gestao.plano_acao_item pai
			  LEFT JOIN gestao.plano_acao_resposta par
			    ON pai.cd_plano_acao_item = par.cd_plano_acao_item
			  JOIN projetos.divisoes d
			    ON pai.cd_gerencia_responsavel = d.codigo
			 WHERE pai.dt_exclusao IS NULL
			   AND pai.cd_plano_acao = ".intval($cd_plano_acao)."
			   ".(trim($cd_diretoria) != '' ? "AND pai.cd_gerencia_responsavel IN (SELECT codigo 
	  												   							     FROM projetos.divisoes 
	 												 						 	    WHERE area = ".str_escape($cd_diretoria).")" : "")."
	 		ORDER BY pai.nr_plano_acao_item ASC;";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function salvar_item($args = array())
	{
		$cd_plano_acao_item = intval($this->db->get_new_id('gestao.plano_acao_item', 'cd_plano_acao_item'));

		$qr_sql = "
			INSERT INTO gestao.plano_acao_item
			     (
			       cd_plano_acao_item,
			       cd_plano_acao,
			       nr_plano_acao_item,
			       ds_constatacao,
			       cd_gerencia_responsavel,
			       cd_usuario_responsavel,
			       cd_usuario_substituto,
			       dt_prazo,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_plano_acao_item).",
			     	".(trim($args['cd_plano_acao']) != '' ? intval($args['cd_plano_acao']) : "DEFAULT").",
			     	".(intval($args['nr_plano_acao_item']) != '' ? intval($args['nr_plano_acao_item']) : "").",
			     	".(trim($args['ds_constatacao']) != '' ? str_escape($args['ds_constatacao']) : "DEFAULT").",
			        ".(trim($args['cd_gerencia_responsavel']) != '' ? str_escape($args['cd_gerencia_responsavel']) : "DEFAULT").",
			        ".(intval($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "").",
			        ".(intval($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : "").",
			        ".(trim($args['dt_prazo']) != '' ? "TO_DATE('".trim($args['dt_prazo'])."', 'DD/MM/YYYY')" : "DEFAULT").",
			        ".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
			     );";

		$this->db->query($qr_sql); 

		return $cd_plano_acao_item;
	}

	public function salvar_recomendacao($cd_plano_acao_item, $args = array())
	{
		$cd_plano_acao_item_recomendacao = intval($this->db->get_new_id('gestao.plano_acao_item_recomendacao', 'cd_plano_acao_item_recomendacao'));

		$qr_sql = "
			INSERT INTO gestao.plano_acao_item_recomendacao
			     (
			       cd_plano_acao_item_recomendacao,
			       cd_plano_acao_item,
			       nr_plano_acao_item_recomendacao,
			       ds_recomendacao,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_plano_acao_item_recomendacao).",
			     	".intval($cd_plano_acao_item).",
			     	".(intval($args['nr_plano_acao_item_recomendacao']) != '' ? intval($args['nr_plano_acao_item_recomendacao']) : "").",
			     	".(trim($args['ds_recomendacao']) != '' ? str_escape($args['ds_recomendacao']) : "DEFAULT").",
			     	".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
			     );";

		$this->db->query($qr_sql); 

		return $cd_plano_acao_item_recomendacao;
	}

	public function get_nr_recomendacao($cd_plano_acao_item)
	{
		$qr_sql = "
			SELECT (nr_plano_acao_item_recomendacao + 1) AS nr_plano_acao_item_recomendacao
			  FROM gestao.plano_acao_item_recomendacao
			 WHERE dt_exclusao IS NULL
               AND cd_plano_acao_item = ".intval($cd_plano_acao_item)."
			 ORDER BY nr_plano_acao_item_recomendacao DESC;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function listar_recomendacao($cd_plano_acao_item)
	{
		$qr_sql = "
			SELECT par.cd_plano_acao_item_recomendacao,
				   TO_CHAR(par.dt_inclusao, 'DD/MM/YYYY  HH24:MI:SS') AS dt_inclusao,
			       par.cd_plano_acao_item,
			       par.nr_plano_acao_item_recomendacao||' - '||par.ds_recomendacao AS ds_recomendacao_item,
			       par.nr_plano_acao_item_recomendacao,
			       par.ds_recomendacao,
			       par.cd_usuario_inclusao,
			       pai.cd_gerencia_responsavel,
			       funcoes.get_usuario_nome(par.cd_usuario_inclusao) AS ds_usuario_inclusao
			  FROM gestao.plano_acao_item_recomendacao par
			  JOIN gestao.plano_acao_item pai
			    ON par.cd_plano_acao_item = pai.cd_plano_acao_item
			 WHERE par.dt_exclusao IS NULL
			   AND par.cd_plano_acao_item = ".intval($cd_plano_acao_item)."
			 ORDER BY par.nr_plano_acao_item_recomendacao ASC;";

		 return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_recomendacao($cd_plano_acao_item_recomendacao)
    {
    	$qr_sql = "
    		SELECT cd_plano_acao_item_recomendacao,
    			   ds_recomendacao,
    			   nr_plano_acao_item_recomendacao
    		  FROM gestao.plano_acao_item_recomendacao
    		 WHERE dt_exclusao IS NULL
    		   AND cd_plano_acao_item_recomendacao = ".intval($cd_plano_acao_item_recomendacao).";";

    	return $this->db->query($qr_sql)->row_array();
    }


	public function atualizar_recomendacao($cd_plano_acao_item_recomendacao, $args = array())
    {
    	$qr_sql = "
	        UPDATE gestao.plano_acao_item_recomendacao
	           SET nr_plano_acao_item_recomendacao = ".(intval($args['nr_plano_acao_item_recomendacao']) != '' ? intval($args['nr_plano_acao_item_recomendacao']) : "").",
	           	   ds_recomendacao                = ".(trim($args['ds_recomendacao']) != ''? str_escape($args['ds_recomendacao']) : "DEFAULT").",
	           	   cd_usuario_alteracao           = ".intval($args['cd_usuario']).",
	               dt_alteracao                   = CURRENT_TIMESTAMP
	         WHERE cd_plano_acao_item_recomendacao = ".intval($cd_plano_acao_item_recomendacao).";";

    	$this->db->query($qr_sql);
    }

    public function excluir_recomendacao($cd_plano_acao_item_recomendacao, $cd_usuario)
    {
    	$qr_sql = "
	        UPDATE gestao.plano_acao_item_recomendacao
	           SET cd_usuario_exclusao = ".intval($cd_usuario).",
	               dt_exclusao         = CURRENT_TIMESTAMP
	         WHERE cd_plano_acao_item_recomendacao = ".intval($cd_plano_acao_item_recomendacao).";";

    	$this->db->query($qr_sql);
    }

	public function atualizar_item($cd_plano_acao_item, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.plano_acao_item
               SET nr_plano_acao_item		= ".(intval($args['nr_plano_acao_item']) != '' ? intval($args['nr_plano_acao_item']) : "").",
			       ds_constatacao           = ".(trim($args['ds_constatacao']) != '' ? str_escape($args['ds_constatacao']) : "DEFAULT").",
			       cd_gerencia_responsavel  = ".(trim($args['cd_gerencia_responsavel']) != '' ? str_escape($args['cd_gerencia_responsavel']) : "DEFAULT").",
                   cd_usuario_responsavel   = ".(intval($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "").",
			       cd_usuario_substituto    = ".(intval($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : "").",
			       dt_prazo                 = ".(trim($args['dt_prazo']) != '' ? "TO_DATE('".trim($args['dt_prazo'])."', 'DD/MM/YYYY')" : "DEFAULT").",
 			       cd_usuario_alteracao     = ".intval($args['cd_usuario']).",
                   dt_alteracao             = CURRENT_TIMESTAMP
            WHERE cd_plano_acao_item = ".intval($cd_plano_acao_item).";";

        $this->db->query($qr_sql);
	}

	public function encaminhar_email($cd_plano_acao, $cd_usuario)
    {
    	$qr_sql = "
	        UPDATE gestao.plano_acao
	           SET cd_usuario_envio_responsavel = ".intval($cd_usuario).", 
	               dt_envio_responsavel         = CURRENT_TIMESTAMP
	         WHERE cd_plano_acao = ".intval($cd_plano_acao).";";

     	$this->db->query($qr_sql);
    }

    public function salvar_acomapanhamento($args = array())
    {
    	$cd_plano_acao_acompanhamento = intval($this->db->get_new_id('gestao.plano_acao_acompanhamento', 'cd_plano_acao_acompanhamento'));

    	$qr_sql = "
	        INSERT INTO gestao.plano_acao_acompanhamento
	             (
	        		cd_plano_acao_acompanhamento,
	        		cd_plano_acao_item,
	        		ds_acompanhamento,
	        		fl_status,
	        		cd_usuario_inclusao,
	        		cd_usuario_alteracao
	             )
	        VALUES
	             (
		        	".intval($cd_plano_acao_acompanhamento).",
		        	".(trim($args['cd_plano_acao_item']) != '' ? intval($args['cd_plano_acao_item']) : "DEFAULT").",
			     	".(trim($args['ds_acompanhamento']) != '' ? str_escape($args['ds_acompanhamento']) : "DEFAULT").",
			     	".(trim($args['fl_status']) != '' ? str_escape($args['fl_status']) : "DEFAULT").",
			        ".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
	             );";
	        
		$this->db->query($qr_sql);

    	return $cd_plano_acao_acompanhamento;
    }

    public function carrega_acompanhamento($cd_plano_acao_acompanhamento)
    {
    	$qr_sql = "
    		SELECT cd_plano_acao_acompanhamento,
    			   ds_acompanhamento,
    			   fl_status
    		  FROM gestao.plano_acao_acompanhamento
    		 WHERE dt_exclusao IS NULL
    		   AND cd_plano_acao_acompanhamento = ".intval($cd_plano_acao_acompanhamento).";";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function listar_acompanhamento($cd_plano_acao_item)
    {
    	$qr_sql = "
    		SELECT cd_plano_acao_acompanhamento,
    		       cd_plano_acao_item,
    			   ds_acompanhamento,
    			   TO_CHAR(dt_inclusao, 'DD/MM/YYYY  HH24:MI:SS') AS dt_inclusao,
    			   (CASE WHEN fl_status = 'E' THEN 'Encerrada'
    			   		 WHEN fl_status = 'A' THEN 'Em Andamento'
    			   	     WHEN fl_status = 'N' THEN 'Não Iniciada' 
    			   END) AS ds_status,
    			   (CASE WHEN fl_status = 'E' THEN 'success'
    			   		 WHEN fl_status = 'A' THEN 'info'
    			   	     WHEN fl_status = 'N' THEN 'important' 
    			   END) AS ds_class,
    			   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
    		  FROM gestao.plano_acao_acompanhamento
    		 WHERE dt_exclusao IS NULL
    		   AND cd_plano_acao_item = ".intval($cd_plano_acao_item)."
    		 ";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function atualizar_acompanhamento($cd_plano_acao_acompanhamento, $args = array())
    {
    	$qr_sql = "
	        UPDATE gestao.plano_acao_acompanhamento
	           SET fl_status            = ".(trim($args['fl_status']) != '' ? str_escape($args['fl_status']) : "DEFAULT").",
	           	   ds_acompanhamento    = ".(trim($args['ds_acompanhamento']) != '' ? str_escape($args['ds_acompanhamento']) : "DEFAULT").",
	           	   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
	               dt_alteracao         = CURRENT_TIMESTAMP
	         WHERE cd_plano_acao_acompanhamento = ".intval($cd_plano_acao_acompanhamento).";";

    	$this->db->query($qr_sql);
    }

    public function excluir_acompanhamento($cd_plano_acao_acompanhamento, $cd_usuario)
    {
    	$qr_sql = "
	        UPDATE gestao.plano_acao_acompanhamento
	           SET cd_usuario_exclusao = ".intval($cd_usuario).",
	               dt_exclusao         = CURRENT_TIMESTAMP
	         WHERE cd_plano_acao_acompanhamento = ".intval($cd_plano_acao_acompanhamento).";";

    	$this->db->query($qr_sql);
    }

    public function minhas_listar($args = array())
    {
    	$qr_sql = "
            SELECT gestao.plano_acao_ano_numero(pa.nr_ano,pa.nr_plano_acao) AS ds_ano_numero, 
				   pa.cd_processo, 
				   pa.ds_situacao,
				   p.procedimento,
				   pai.cd_plano_acao_item, 
				   pai.cd_plano_acao, 
				   (SELECT (CASE WHEN paa.fl_status = 'E' THEN 'Encerrada'
    			   		         WHEN paa.fl_status = 'A' THEN 'Em Andamento'
    			   	             WHEN paa.fl_status = 'N' THEN 'Não Iniciada' 
    			   	       END)
    			   	  FROM gestao.plano_acao_acompanhamento paa
				     WHERE paa.cd_plano_acao_item = pai.cd_plano_acao_item
				       AND paa.dt_exclusao IS NULL
				     ORDER BY paa.dt_inclusao DESC 
				     LIMIT 1) AS ds_status,
				   (SELECT (CASE WHEN fl_status = 'E' THEN 'success'
		    			   		 WHEN fl_status = 'A' THEN 'info'
		    			   	     WHEN fl_status = 'N' THEN 'important' 
    			   	       END)
    			   	  FROM gestao.plano_acao_acompanhamento paa
				     WHERE paa.cd_plano_acao_item = pai.cd_plano_acao_item
				       AND paa.dt_exclusao IS NULL
				     ORDER BY paa.dt_inclusao DESC 
				     LIMIT 1) AS ds_class,
    			   pai.nr_plano_acao_item, 
				   pai.ds_constatacao, 
				   TO_CHAR(pai.dt_prazo, 'DD/MM/YYYY') AS dt_prazo, 
				   pai.cd_gerencia_responsavel,
				   par.ds_acao,
				   (SELECT TO_CHAR(paa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS')||' : '||paa.ds_acompanhamento
    			   	  FROM gestao.plano_acao_acompanhamento paa
				     WHERE paa.cd_plano_acao_item = pai.cd_plano_acao_item
				       AND paa.dt_exclusao IS NULL
				     ORDER BY paa.dt_inclusao DESC 
				     LIMIT 1) AS ds_acompanhamento
			  FROM gestao.plano_acao_item pai
			  LEFT JOIN gestao.plano_acao_resposta par
			    ON pai.cd_plano_acao_item = par.cd_plano_acao_item
              JOIN gestao.plano_acao pa
                ON pa.cd_plano_acao = pai.cd_plano_acao
               AND pa.dt_envio_responsavel IS NOT NULL
              LEFT JOIN projetos.processos p
		        ON p.cd_processo = pa.cd_processo
		       AND pai.dt_exclusao IS NULL
			 WHERE pai.dt_exclusao IS NULL

			    ".(trim($args['cd_diretoria']) == '' ? "AND pai.cd_gerencia_responsavel = '".trim($args['cd_gerencia_responsavel'])."'" : "")."
			    ".((trim($args['cd_diretoria']) != '' AND !in_array(trim($args['cd_diretoria']), array('PRE'))) ? "AND pai.cd_gerencia_responsavel IN (SELECT codigo   												   							                                   FROM projetos.divisoes												 													  WHERE area = ".str_escape($args['cd_diretoria']).")" : "")."
               AND (
               			(SELECT COUNT(*) 
	                       FROM projetos.usuarios_controledi g
		                  WHERE g.codigo = ".intval($args['cd_usuario'])."
		                    AND g.tipo = 'G'
		                    AND g.divisao = '".trim($args['cd_gerencia_responsavel'])."') > 0
						OR
		     			(SELECT COUNT(*)  
		                   FROM projetos.usuarios_controledi d
			              WHERE d.codigo = ".intval($args['cd_usuario'])."
			                AND d.indic_01 = 'S'
			                AND d.divisao = '".trim($args['cd_gerencia_responsavel'])."') > 0
			            OR pai.cd_usuario_responsavel = ".intval($args['cd_usuario'])."
			            OR pai.cd_usuario_substituto = ".intval($args['cd_usuario'])."
			            ".(intval($args['cd_diretoria']) == 'PRE' ? "OR 1 = 1" : "")."

			       )
			   ".(intval($args['nr_plano_acao']) != '' ? "AND pa.nr_plano_acao = ".intval($args['nr_plano_acao']) : "")."
			   ".(trim($args['fl_acao']) == 'S' ? "AND par.ds_acao IS NOT NULL" : "")."
			   ".(trim($args['fl_acao']) == 'N' ? "AND par.ds_acao IS NULL" : "")."

			   ".(trim($args['fl_status']) != '' ? "AND 
		        										(
		        											((SELECT paa.fl_status
		        	                                            FROM gestao.plano_acao_acompanhamento paa
		        	                                           WHERE paa.dt_exclusao IS NULL
		        	                                             AND paa.cd_plano_acao_item = pai.cd_plano_acao_item
		        	              
		        	                                           ORDER BY paa.dt_inclusao DESC
		        	                                           LIMIT 1) = '".trim($args['fl_status'])."')
		        	                                        ".(trim($args['fl_status']) == 'N' ? "OR (
		        	                                        ((SELECT COUNT(*)
		        	                                            FROM gestao.plano_acao_acompanhamento paa
		        	                                           WHERE dt_exclusao IS NULL
		        	                                             AND paa.cd_plano_acao_item = pai.cd_plano_acao_item) = 0)
		        	                                        )" : "")."
		        	                                    )"
			        : "")."
			   ".(intval($args['nr_ano']) != '' ? "AND pa.nr_ano = ".intval($args['nr_ano']) : "")."
			   ".(((trim($args["dt_prazo_ini"]) != "") and (trim($args["dt_prazo_fim"]) != "")) ? " AND CAST(pai.dt_prazo AS DATE) BETWEEN TO_DATE('".$args["dt_prazo_ini"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_prazo_fim"]."','DD/MM/YYYY')" : "").";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_resposta($args = array())
    {
    	$cd_plano_acao_resposta = intval($this->db->get_new_id('gestao.plano_acao_resposta', 'cd_plano_acao_resposta'));

    	$qr_sql = "
	        INSERT INTO gestao.plano_acao_resposta
		         (
		        	cd_plano_acao_resposta,
		        	cd_plano_acao_item,
		        	ds_acao,
		        	cd_usuario_inclusao,
		        	cd_usuario_alteracao
		         )
	        VALUES
	        	 (
		        	".intval($cd_plano_acao_resposta).",
		        	".(trim($args['cd_plano_acao_item']) != '' ? intval($args['cd_plano_acao_item']) : "DEFAULT").",
			     	".(trim($args['ds_acao']) != '' ? str_escape($args['ds_acao']) : "DEFAULT").",
			     	".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
	             );";
	      
		$this->db->query($qr_sql);

    	return $cd_plano_acao_resposta;
    }

    public function atualizar_resposta($cd_plano_acao_resposta, $args = array())
    {
    	$qr_sql = "
	        UPDATE gestao.plano_acao_resposta
	           SET ds_acao              = ".(trim($args['ds_acao']) != '' ? str_escape($args['ds_acao']) : "DEFAULT").",
	           	   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
	               dt_alteracao         = CURRENT_TIMESTAMP
	         WHERE cd_plano_acao_resposta = ".intval($cd_plano_acao_resposta).";";

    	$this->db->query($qr_sql);
    }

    public function atualizar_prazo($cd_plano_acao_item, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.plano_acao_item
               SET dt_prazo	 	        = ".(trim($args['dt_prazo']) != '' ? "TO_DATE('".trim($args['dt_prazo'])."', 'DD/MM/YYYY')" : "DEFAULT").",
			       cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_plano_acao_item = ".intval($cd_plano_acao_item).";";

        $this->db->query($qr_sql);
	}

	public function listar_anexo($cd_plano_acao_item)
	{
		$qr_sql = "
			SELECT cd_plano_acao_item_anexo,
				   arquivo,
				   arquivo_nome,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_nome
			  FROM gestao.plano_acao_item_anexo
			 WHERE cd_plano_acao_item = ".intval($cd_plano_acao_item)."
			   AND dt_exclusao IS NULL
			 ORDER BY dt_inclusao DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function salvar_anexo($args = array())
	{
		$qr_sql = "
			INSERT INTO gestao.plano_acao_item_anexo
			     (
					cd_plano_acao_item,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['cd_plano_acao_item']).",
					'".trim($args['arquivo'])."',
					'".trim($args['arquivo_nome'])."',
					".intval($args['cd_usuario'])."
				 )";

		$this->db->query($qr_sql);
	}
	
	public function excluir_anexo($cd_plano_acao_item_anexo, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.plano_acao_item_anexo
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_plano_acao_item_anexo = ".intval($cd_plano_acao_item_anexo).";";

		$this->db->query($qr_sql);
	}

}
?>