<?php
class info_cronograma_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function get_analista_cronograma(&$result, $args=array())
    {
        $qr_sql = "SELECT c.cd_analista AS value,
                          uc.nome       AS text
                     FROM informatica.cronograma c
                     JOIN projetos.usuarios_controledi uc
                       ON uc.codigo = c.cd_analista
                    WHERE c.dt_exclusao IS NULL
                    ORDER BY uc.nome";
        
        $result = $this->db->query($qr_sql);
    }
    
    function cronograma(&$result, $args=array())
    {
        $qr_sql = "SELECT c.cd_cronograma,
                          TO_CHAR(c.nr_mes, 'FM00') || '/' || c.nr_ano AS mes_ano,
						  c.nr_mes,
						  c.nr_ano
                     FROM informatica.cronograma c
                    WHERE c.dt_exclusao IS NULL
                      AND c.cd_analista = ".intval($args['cd_analista'])."
					  ".(
							((intval($args['nr_mes']) > 0) and (intval($args['nr_ano']) > 0)) 
							? "AND TO_DATE('01/'||c.nr_mes||'/'||c.nr_ano, 'DD/MM/YYYY') <= TO_DATE('01/".intval($args['nr_mes'])."/".intval($args['nr_ano'])."', 'DD/MM/YYYY')" 
							: "AND TO_DATE('01/'||c.nr_mes||'/'||c.nr_ano, 'DD/MM/YYYY') <= (SELECT MAX((TO_DATE('01/' || TO_CHAR(c1.nr_mes, 'FM00') || '/' || TO_CHAR(c1.nr_ano, 'FM0000'),'DD/MM/YYYY')))
					                                                                           FROM informatica.cronograma c1
					                                                                          WHERE c1.dt_exclusao   IS NULL
					                                                                            AND c1.cd_analista   =  c.cd_analista)" 
						)."
                    ORDER BY TO_DATE('01/'||c.nr_mes||'/'||c.nr_ano, 'DD/MM/YYYY') DESC
                    LIMIT 13 ";

		#echo "<PRE>$qr_sql</PRE>";
					
        $result = $this->db->query($qr_sql);
    }
    
    function cronograma_item(&$result, $args=array())
    {
        $qr_sql = "SELECT cd_cronograma_item,
                          nr_prioridade,
                          descricao,
                          fl_concluido,
                          observacao
                     FROM informatica.cronograma_item
                    WHERE dt_exclusao IS NULL
                      AND cd_cronograma = ".intval($args['cd_cronograma'])."
                    ORDER BY nr_prioridade ASC, cd_cronograma_item ASC";

        $result = $this->db->query($qr_sql);
    }
    
    function get_analistas(&$result, $args=array())
    {
        $qr_sql = "SELECT codigo AS value,
                          nome   AS text
                     FROM projetos.usuarios_controledi
                    WHERE 'GI' IN (divisao, divisao_ant)
                      AND tipo NOT IN ('X')
                    ORDER BY nome";
        
        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args=array())
    {        
        if(intval($args['cd_cronograma']) > 0)
        {
            $return = intval($args['cd_cronograma']);
            
            $qr_sql = "UPDATE informatica.cronograma
                          SET cd_analista  = " . (trim($args['cd_analista']) == "" ? "DEFAULT" :  intval($args['cd_analista'])). "
                        WHERE cd_cronograma = ".intval($return) ;
            
        }
        else 
        {
            $return = intval($this->db->get_new_id("informatica.cronograma", "cd_cronograma"));
            $qr_sql = "
                INSERT INTO informatica.cronograma
                       (
                         cd_cronograma,
                         nr_mes,
                         nr_ano,
                         cd_analista,
                         cd_usuario_inclusao
                       )
                  VALUES
                       (
                         " . intval($return) . ",
                         " . intval($args['nr_mes']) . ",
                         " . intval($args['nr_ano']) . ",
                         " . (trim($args['cd_analista']) == "" ? "DEFAULT" :  intval($args['cd_analista'])). ",
                         " . (trim($args['cd_usuario_inclusao']) == "" ? "DEFAULT" : intval($args['cd_usuario_inclusao'])) . "
                       )
                ";
        }
        
        $this->db->query($qr_sql);
        
        return $return;
    }
    
    function carrega(&$result, $args=array())
    {
        $qr_sql = "SELECT c.cd_cronograma,
                          TO_CHAR(c.nr_mes, 'FM00') || '/' || c.nr_ano AS mes_ano,
                          c.cd_analista,
						  uc.nome AS analista
                     FROM informatica.cronograma c
					 JOIN projetos.usuarios_controledi uc
					   ON uc.codigo = c.cd_analista
                    WHERE c.cd_cronograma = ".intval($args['cd_cronograma']) ;
        
        $result = $this->db->query($qr_sql);
    }
    
    function excluir_item(&$result, $args=array())
    {
        $qr_sql = "UPDATE informatica.cronograma_item
                      SET dt_exclusao         = CURRENT_TIMESTAMP,
                          cd_usuario_exclusao = ".intval($args['cd_usuario_exclusao'])."
                    WHERE cd_cronograma_item = ".intval($args['cd_cronograma_item']) ;
        
        $result = $this->db->query($qr_sql);
    }
    
    function excluir(&$result, $args=array())
    {
        $qr_sql = "UPDATE informatica.cronograma
                      SET cd_usuario_exclusao  = " . (trim($args['cd_usuario_exclusao']) == "" ? "DEFAULT" :  intval($args['cd_usuario_exclusao'])). ",
                          dt_exclusao          = CURRENT_TIMESTAMP
                    WHERE cd_cronograma = ".intval($args['cd_cronograma']) ;
        
        $result = $this->db->query($qr_sql);
    }
    
    function salvar_item(&$result, $args=array())
    {
        if(intval($args['cd_cronograma_item']) > 0)
        {            
            $qr_sql = "UPDATE informatica.cronograma_item
                          SET nr_prioridade  = " . (trim($args['nr_prioridade']) == "" ? "DEFAULT" :  intval($args['nr_prioridade'])). ",
                              descricao      = " . (trim($args['descricao']) == "" ? "DEFAULT" :  "'".trim($args['descricao'])."'"). ",
                              fl_concluido   = " . (trim($args['fl_concluido']) == "" ? "DEFAULT" :  "'".trim($args['fl_concluido'])."'"). ",
                              observacao     = " . (trim($args['observacao']) == "" ? "DEFAULT" :  "'".trim($args['observacao'])."'"). "
                        WHERE cd_cronograma_item = ".intval($args['cd_cronograma_item']) ;
            
        }
        else 
        {
            $qr_sql = "
                INSERT INTO informatica.cronograma_item
                       (
                         cd_cronograma,
                         nr_prioridade,
                         descricao,
                         fl_concluido,
                         observacao,
                         cd_usuario_inclusao
                       )
                  VALUES
                       (
                         " . intval($args['cd_cronograma']) . ",
                         " . (trim($args['nr_prioridade']) == "" ? "DEFAULT" :  intval($args['nr_prioridade'])). ",
                         " . (trim($args['descricao']) == "" ? "DEFAULT" :  "'".trim($args['descricao'])."'"). ",
                         " . (trim($args['fl_concluido']) == "" ? "DEFAULT" :  "'".trim($args['fl_concluido'])."'"). ",
                         " . (trim($args['observacao']) == "" ? "DEFAULT" :  "'".trim($args['observacao'])."'"). ",
                         " . (trim($args['cd_usuario_inclusao']) == "" ? "DEFAULT" : intval($args['cd_usuario_inclusao'])) . "
                       )
                ";
        }
        
        $this->db->query($qr_sql);
    }
    
    function carrega_cronograma_item(&$result, $args=array())
    {
        $qr_sql = "
			SELECT cd_cronograma_item,
                   nr_prioridade,
                   descricao,
                   fl_concluido,
                   observacao
              FROM informatica.cronograma_item
             WHERE dt_exclusao IS NULL
               AND cd_cronograma_item = ".intval($args['cd_cronograma_item'])."
             ORDER BY nr_prioridade ASC, descricao ASC";

        $result = $this->db->query($qr_sql);
    }
	
    function setPrioridade(&$result, $args=array())
    {
        if(intval($args['cd_cronograma_item']) > 0)
        {            
            $qr_sql = "
						UPDATE informatica.cronograma_item
                           SET nr_prioridade  = " . (trim($args['nr_prioridade']) == "" ? "DEFAULT" :  intval($args['nr_prioridade'])). "
                         WHERE cd_cronograma_item = ".intval($args['cd_cronograma_item'])."
					  ";
			$this->db->query($qr_sql);
        }
    }	
	
    function setConcluido(&$result, $args=array())
    {
        if(intval($args['cd_cronograma_item']) > 0)
        {            
            $qr_sql = "
						UPDATE informatica.cronograma_item
                           SET fl_concluido  = " . (trim($args['fl_concluido']) == "" ? "DEFAULT" :  "'".trim($args['fl_concluido'])."'"). "
                         WHERE cd_cronograma_item = ".intval($args['cd_cronograma_item'])."
					  ";
			$this->db->query($qr_sql);
        }
    }		
}
?>