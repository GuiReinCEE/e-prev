<?php
class cronograma_investimento_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function get_analista_cronograma(&$result, $args=array())
    {
        $qr_sql = "
			SELECT c.cd_analista AS value,
				   uc.nome       AS text
			  FROM projetos.cronograma_investimento c
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = c.cd_analista
			 WHERE c.dt_exclusao IS NULL
               AND uc.tipo NOT IN ('X')
			 ORDER BY uc.nome;";
        
        $result = $this->db->query($qr_sql);
    }
    
    function cronograma(&$result, $args=array())
    {
        $qr_sql = "
			SELECT c.cd_cronograma_investimento,
				   TO_CHAR(c.nr_mes, 'FM00') || '/' || c.nr_ano AS mes_ano,
				   c.nr_mes,
		 		   c.nr_ano,
				   (SELECT COUNT(*)
				      FROM projetos.cronograma_investimento_item cii
					 WHERE cii.dt_exclusao IS NULL
					   AND cii.cd_cronograma_investimento = c.cd_cronograma_investimento) AS tl_item,
					(SELECT COUNT(*)
				       FROM projetos.cronograma_investimento_item cii
					  WHERE cii.dt_exclusao IS NULL
					    AND cii.cd_cronograma_investimento = c.cd_cronograma_investimento
						AND cii.fl_concluido = 'S') AS tl_item_concluido
			  FROM projetos.cronograma_investimento c
			 WHERE c.dt_exclusao IS NULL
			   AND c.cd_analista = ".intval($args['cd_analista'])."
			   ".(
					((intval($args['nr_mes']) > 0) and (intval($args['nr_ano']) > 0)) 
					? "AND TO_DATE('01/'||c.nr_mes||'/'||c.nr_ano, 'DD/MM/YYYY') <= TO_DATE('01/".intval($args['nr_mes'])."/".intval($args['nr_ano'])."', 'DD/MM/YYYY')" 
					: "AND TO_DATE('01/'||c.nr_mes||'/'||c.nr_ano, 'DD/MM/YYYY') <= (SELECT MAX((TO_DATE('01/' || TO_CHAR(c1.nr_mes, 'FM00') || '/' || TO_CHAR(c1.nr_ano, 'FM0000'),'DD/MM/YYYY')))
																					   FROM projetos.cronograma_investimento c1
																					  WHERE c1.dt_exclusao   IS NULL
																						AND c1.cd_analista   =  c.cd_analista)" 
				)."
				
			 ORDER BY TO_DATE('01/'||c.nr_mes||'/'||c.nr_ano, 'DD/MM/YYYY') DESC
			 LIMIT 13;";
					
        $result = $this->db->query($qr_sql);
    }
    
    function cronograma_item(&$result, $args=array())
    {
        $qr_sql = "
			SELECT cd_cronograma_investimento_item,
				   nr_prioridade,
				   descricao,
				   fl_concluido,
				   observacao,
				   TO_CHAR(dt_limite, 'DD/MM/YYYY') AS dt_limite,
				   dt_limite - CURRENT_DATE AS restam
			  FROM projetos.cronograma_investimento_item
		 	 WHERE dt_exclusao IS NULL
			   AND cd_cronograma_investimento = ".intval($args['cd_cronograma_investimento'])."
			   ".(trim($args['fl_concluido']) != '' ? "AND fl_concluido = '".trim($args['fl_concluido'])."'" : "")."
			 ORDER BY nr_prioridade ASC, cd_cronograma_investimento_item ASC";

        $result = $this->db->query($qr_sql);
    }
    
    function get_analistas(&$result, $args=array())
    {
        $qr_sql = "
			SELECT codigo AS value,
				   nome   AS text
			  FROM projetos.usuarios_controledi
			 WHERE divisao = 'GIN'
			   AND tipo NOT IN ('X')
			 ORDER BY nome;";
        
        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args=array())
    {        
        if(intval($args['cd_cronograma_investimento']) > 0)
        {
            $cd_cronograma_investimento = intval($args['cd_cronograma_investimento']);
            
            $qr_sql = "
				UPDATE projetos.cronograma_investimento
				   SET cd_analista  = ".(trim($args['cd_analista']) == "" ? "DEFAULT" :  intval($args['cd_analista']))."
				 WHERE cd_cronograma_investimento = ".intval($cd_cronograma_investimento).";" ;
            
        }
        else 
        {
            $cd_cronograma_investimento = intval($this->db->get_new_id("projetos.cronograma_investimento", "cd_cronograma_investimento"));
            $qr_sql = "
                INSERT INTO projetos.cronograma_investimento
                     (
                         cd_cronograma_investimento,
                         nr_mes,
                         nr_ano,
                         cd_analista,
                         cd_usuario_inclusao
                     )
                VALUES
                     (
                         ".intval($cd_cronograma_investimento).",
                         ".intval($args['nr_mes']).",
                         ".intval($args['nr_ano']).",
                         ".(trim($args['cd_analista']) == "" ? "DEFAULT" :  intval($args['cd_analista'])).",
                         ".(trim($args['cd_usuario_inclusao']) == "" ? "DEFAULT" : intval($args['cd_usuario_inclusao']))."
                     );";
        }
        
        $this->db->query($qr_sql);
        
        return $cd_cronograma_investimento;
    }
	
	function troca_analista(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.cronograma_investimento_item
			   SET cd_cronograma_investimento = (SELECT cd_cronograma_investimento 
			                                       FROM projetos.cronograma_investimento
												  WHERE cd_analista = ".intval($args["cd_analista"])."
												    AND dt_exclusao IS NULL
											      ORDER BY cd_cronograma_investimento DESC
												  LIMIT 1)
			 WHERE cd_cronograma_investimento_item = ".intval($args['cd_cronograma_investimento_item']).";" ;
			 
		$this->db->query($qr_sql);
	}
    
    function carrega(&$result, $args=array())
    {
        $qr_sql = "
			SELECT c.cd_cronograma_investimento,
				   TO_CHAR(c.nr_mes, 'FM00') || '/' || c.nr_ano AS mes_ano,
				   c.cd_analista,
				   uc.nome AS analista
			  FROM projetos.cronograma_investimento c
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = c.cd_analista
			 WHERE c.cd_cronograma_investimento = ".intval($args['cd_cronograma_investimento']).";" ;
        
        $result = $this->db->query($qr_sql);
    }
    
    function excluir_item(&$result, $args=array())
    {
        $qr_sql = "
			UPDATE projetos.cronograma_investimento_item
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			 	   cd_usuario_exclusao = ".intval($args['cd_usuario_exclusao'])."
			 WHERE cd_cronograma_investimento_item = ".intval($args['cd_cronograma_investimento_item']).";" ;
        
        $result = $this->db->query($qr_sql);
    }
    
    function excluir(&$result, $args=array())
    {
        $qr_sql = "
			UPDATE projetos.cronograma_investimento
			   SET cd_usuario_exclusao  = " . (trim($args['cd_usuario_exclusao']) == "" ? "DEFAULT" :  intval($args['cd_usuario_exclusao'])). ",
				   dt_exclusao          = CURRENT_TIMESTAMP
			 WHERE cd_cronograma_investimento = ".intval($args['cd_cronograma_investimento']).";" ;
        
        $result = $this->db->query($qr_sql);
    }
    
    function salvar_item(&$result, $args=array())
    {
        if(intval($args['cd_cronograma_investimento_item']) > 0)
        {            
            $qr_sql = "
				UPDATE projetos.cronograma_investimento_item
				   SET nr_prioridade  = " . (trim($args['nr_prioridade']) == "" ? "DEFAULT" :  intval($args['nr_prioridade'])). ",
					   descricao      = " . (trim($args['descricao']) == "" ? "DEFAULT" :  "'".trim($args['descricao'])."'"). ",
					   fl_concluido   = " . (trim($args['fl_concluido']) == "" ? "DEFAULT" :  "'".trim($args['fl_concluido'])."'"). ",
					   observacao     = " . (trim($args['observacao']) == "" ? "DEFAULT" :  "'".trim($args['observacao'])."'"). ",
					   dt_limite      = ".(trim($args['dt_limite']) == "" ? "DEFAULT" :  "TO_DATE('".trim($args['dt_limite'])."', 'DD/MM/YYYY')")."
				 WHERE cd_cronograma_investimento_item = ".intval($args['cd_cronograma_investimento_item']).";" ;
            
        }
        else 
        {
            $qr_sql = "
                INSERT INTO projetos.cronograma_investimento_item
                       (
                         cd_cronograma_investimento,
                         nr_prioridade,
                         descricao,
                         fl_concluido,
                         observacao,
						 dt_limite,
                         cd_usuario_inclusao
                       )
                  VALUES
                       (
                         " . intval($args['cd_cronograma_investimento']) . ",
                         " . (trim($args['nr_prioridade']) == "" ? "DEFAULT" :  intval($args['nr_prioridade'])). ",
                         " . (trim($args['descricao']) == "" ? "DEFAULT" :  "'".trim($args['descricao'])."'"). ",
                         " . (trim($args['fl_concluido']) == "" ? "DEFAULT" :  "'".trim($args['fl_concluido'])."'"). ",
                         " . (trim($args['observacao']) == "" ? "DEFAULT" :  "'".trim($args['observacao'])."'"). ",
						 ".(trim($args['dt_limite']) == "" ? "DEFAULT" :  "TO_DATE('".trim($args['dt_limite'])."', 'DD/MM/YYYY')").",
                         " . (trim($args['cd_usuario_inclusao']) == "" ? "DEFAULT" : intval($args['cd_usuario_inclusao'])) . "
                       ); ";
        }
        
        $this->db->query($qr_sql);
    }
    
    function carrega_cronograma_item(&$result, $args=array())
    {
        $qr_sql = "
			SELECT cd_cronograma_investimento_item,
                   nr_prioridade,
                   descricao,
                   fl_concluido,
                   observacao,
				   TO_CHAR(dt_limite, 'DD/MM/YYYY') AS dt_limite
              FROM projetos.cronograma_investimento_item
             WHERE dt_exclusao IS NULL
               AND cd_cronograma_investimento_item = ".intval($args['cd_cronograma_investimento_item'])."
             ORDER BY nr_prioridade ASC, descricao ASC";

        $result = $this->db->query($qr_sql);
    }
	
    function setPrioridade(&$result, $args=array())
    {
        if(intval($args['cd_cronograma_investimento_item']) > 0)
        {            
            $qr_sql = "
				UPDATE projetos.cronograma_investimento_item
				   SET nr_prioridade  = " . (trim($args['nr_prioridade']) == "" ? "DEFAULT" :  intval($args['nr_prioridade'])). "
				 WHERE cd_cronograma_investimento_item = ".intval($args['cd_cronograma_investimento_item']).";";
			$this->db->query($qr_sql);
        }
    }	
	
    function setConcluido(&$result, $args=array())
    {
        if(intval($args['cd_cronograma_investimento_item']) > 0)
        {            
            $qr_sql = "
				UPDATE projetos.cronograma_investimento_item
				   SET fl_concluido  = " . (trim($args['fl_concluido']) == "" ? "DEFAULT" :  "'".trim($args['fl_concluido'])."'"). "
				 WHERE cd_cronograma_investimento_item = ".intval($args['cd_cronograma_investimento_item']).";";
			$this->db->query($qr_sql);
        }
    }		
}
?>