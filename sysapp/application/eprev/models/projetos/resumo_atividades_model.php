<?php
class resumo_atividades_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	#### GERENCIA ####
    function resumoGerencia(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT d.codigo AS cd_gerencia, 
                           -- ABERTA
                           (SELECT COUNT(a1.numero)
                              FROM projetos.atividades a1
                             WHERE a1.area    = 'GI'
                               AND a1.tipo    <> 'L'
                               AND a1.divisao = d.codigo
                               AND a1.tipo    = 'S' --SUPORTE
							   ".(intval($args['nr_ano']) > 0 ? "AND TO_CHAR(a1.dt_cad,'YYYY') = '".intval($args['nr_ano'])."'": "")."
							   ".(trim($args["cd_atendente"]) != "" ? " AND a1.cod_atendente = ".intval($args['cd_atendente']) : "")."
                           ) AS qt_suporte_aberta,
                           (SELECT COUNT(a1.numero)
                              FROM projetos.atividades a1
                             WHERE a1.area    = 'GI'
                               AND a1.tipo    <> 'L'
                               AND a1.divisao = d.codigo
                               AND a1.tipo    <> 'S' --DESENVOLVIMENTO
							   ".(intval($args['nr_ano']) > 0 ? "AND TO_CHAR(a1.dt_cad,'YYYY') = '".intval($args['nr_ano'])."'": "")."
							   ".(trim($args["cd_atendente"]) != "" ? " AND a1.cod_atendente = ".intval($args['cd_atendente']) : "")."
                           ) AS qt_desenv_aberta,
                            
                           -- CONCLUIDA
                           (SELECT COUNT(a1.numero)
                              FROM projetos.atividades a1
                             WHERE a1.status_atual = 'CONC'
                               AND a1.area         = 'GI'
                               AND a1.tipo         <> 'L'
                               AND a1.divisao      = d.codigo
                               AND a1.tipo         = 'S' --SUPORTE
							   ".(intval($args['nr_ano']) > 0 ? "AND TO_CHAR(a1.dt_fim_real,'YYYY') = '".intval($args['nr_ano'])."'": "")."
							   ".(trim($args["cd_atendente"]) != "" ? " AND a1.cod_atendente = ".intval($args['cd_atendente']) : "")."
                           ) AS qt_suporte_concluida,
                           (SELECT COUNT(a1.numero)
                              FROM projetos.atividades a1
                             WHERE a1.status_atual = 'CONC'
                               AND a1.area         = 'GI'
                               AND a1.tipo         <> 'L'
                               AND a1.divisao      = d.codigo
                               AND a1.tipo         <> 'S' --DESENVOLVIMENTO
							   ".(intval($args['nr_ano']) > 0 ? "AND TO_CHAR(a1.dt_fim_real,'YYYY') = '".intval($args['nr_ano'])."'": "")."
							   ".(trim($args["cd_atendente"]) != "" ? " AND a1.cod_atendente = ".intval($args['cd_atendente']) : "")."
                           ) AS qt_desenv_concluida,
                            
                           -- CANCELADA
                           (SELECT COUNT(a1.numero)
                              FROM projetos.atividades a1
                             WHERE a1.status_atual IN ('CANC','AGDF')
                               AND a1.area         = 'GI'
                               AND a1.tipo         <> 'L'
                               AND a1.divisao      = d.codigo
                               AND a1.tipo         = 'S' --SUPORTE
							   ".(intval($args['nr_ano']) > 0 ? "AND TO_CHAR(a1.dt_fim_real,'YYYY') = '".intval($args['nr_ano'])."'": "")."
							   ".(trim($args["cd_atendente"]) != "" ? " AND a1.cod_atendente = ".intval($args['cd_atendente']) : "")."
                           ) AS qt_suporte_cancelada,
                           (SELECT COUNT(a1.numero)
                              FROM projetos.atividades a1
                             WHERE a1.status_atual IN ('CANC','AGDF')
                               AND a1.area         = 'GI'
                               AND a1.tipo         <> 'L'
                               AND a1.divisao      = d.codigo
                               AND a1.tipo         <> 'S' --DESENVOLVIMENTO
							   ".(intval($args['nr_ano']) > 0 ? "AND TO_CHAR(a1.dt_fim_real,'YYYY') = '".intval($args['nr_ano'])."'": "")."
							   ".(trim($args["cd_atendente"]) != "" ? " AND a1.cod_atendente = ".intval($args['cd_atendente']) : "")."
                           ) AS qt_desenv_cancelada,
						   
						   -- SUSPENSA
                           (SELECT COUNT(a1.numero)
                              FROM projetos.atividades a1
                             WHERE a1.status_atual IN ('SUSP','ADIR')
                               AND a1.area         = 'GI'
                               AND a1.tipo         <> 'L'
                               AND a1.divisao      = d.codigo
                               AND a1.tipo         = 'S' --SUPORTE
							   ".(intval($args['nr_ano']) > 0 ? "AND TO_CHAR(a1.dt_fim_real,'YYYY') = '".intval($args['nr_ano'])."'": "")."
							   ".(trim($args["cd_atendente"]) != "" ? " AND a1.cod_atendente = ".intval($args['cd_atendente']) : "")."
                           ) AS qt_suporte_suspensa,						   
						   
                           (SELECT COUNT(a1.numero)
                              FROM projetos.atividades a1
                             WHERE a1.status_atual IN ('SUSP','ADIR')
                               AND a1.area         = 'GI'
                               AND a1.tipo         <> 'L'
                               AND a1.divisao      = d.codigo
                               AND a1.tipo         <> 'S' --DESENVOLVIMENTO
							   ".(intval($args['nr_ano']) > 0 ? "AND TO_CHAR(a1.dt_fim_real,'YYYY') = '".intval($args['nr_ano'])."'": "")."
							   ".(trim($args["cd_atendente"]) != "" ? " AND a1.cod_atendente = ".intval($args['cd_atendente']) : "")."
                           ) AS qt_desenv_suspensa					   
                      FROM projetos.divisoes d
                     WHERE 0 < (SELECT COUNT(*) 
					              FROM projetos.atividades ad 
								 WHERE ad.divisao = d.codigo
								 ".(trim($args["cd_atendente"]) != "" ? " AND ad.cod_atendente = ".intval($args['cd_atendente']) : "").")
					 ".(trim($args["fl_considerar_gi"]) == "N" ? "AND d.codigo <> 'GI'": "")."
                     ORDER BY d.codigo
                  ";
		#echo "<PRE>".$qr_sql."</PRE>"; EXIT; 
        $result = $this->db->query($qr_sql);
    }

	function comboAtendente(&$result, $args=array())
	{
		$qr_sql = "
					SELECT uc.codigo AS value,
					       uc.nome AS text
					  FROM projetos.atividades a
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = a.cod_atendente
				     WHERE a.area     = 'GI'
					   AND (uc.divisao = 'GI' OR divisao_ant = 'GI')
					   AND uc.tipo    <> 'X'
					 ORDER BY text
		          ";
		 $result = $this->db->query($qr_sql);	  
	}	
	
	#### RELATORIO ####
	
    function anteriorAbertaSuporte(&$result, $args=array())
    {
        $sql = "
            SELECT COUNT(a.numero) AS qt_ant_aberta
			  FROM projetos.atividades a
			 WHERE a.area   = 'GI'
			   AND DATE_TRUNC('day',a.dt_cad) < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
			   AND a.tipo   = 'S'
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
          ";

        $result = $this->db->query($sql);
    }

    function anteriorConcluidaSuporte(&$result, $args=array())
    {
        $sql = "
            SELECT COUNT(a.numero) AS qt_ant_concluida
			  FROM projetos.atividades a
			 WHERE a.status_atual = 'CONC'
			   AND a.area         = 'GI'
		       AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
			   AND a.tipo         = 'S'
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
		 ";

        $result = $this->db->query($sql);

    }

    function  anteriorConcluidaCritAutoSuporte(&$result, $args=array())
    {
        $sql = "
            SELECT COUNT(a.numero) as qt_ant_concluida
			  FROM projetos.atividades a
			 WHERE a.status_atual = 'CONC'
			   AND a.area         = 'GI'
			   AND a.fl_encerrado_automatico = 'S'
			   AND a.fl_teste_relevante      = 'S'
			   AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
			   AND a.tipo         = 'S'
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
		 ";
        
        $result = $this->db->query($sql);

    }

    function  anteriorConcluidaCritUserSuporte(&$result, $args=array())
    {
        $sql = "
            SELECT COUNT(a.numero) as qt_ant_concluida
			  FROM projetos.atividades a
			 WHERE a.status_atual = 'CONC'
			   AND a.area         = 'GI'
			   AND a.fl_encerrado_automatico = 'N'
			   AND a.fl_teste_relevante      = 'S'
			   AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
			   AND a.tipo         = 'S'
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
		 ";

        $result = $this->db->query($sql);

    }

    function anteriorConcluidaCritNaoSuporte(&$result, $args=array())
    {
        $sql = "
            SELECT COUNT(a.numero) as qt_ant_concluida
			  FROM projetos.atividades a
			 WHERE a.status_atual = 'CONC'
			   AND a.area         = 'GI'
			   AND a.fl_teste_relevante      = 'N'
			   AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
			   AND a.tipo         = 'S'
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
		 ";
        
        $result = $this->db->query($sql);
    }

    function anteriorCanceladasSuporte(&$result, $args=array())
    {
        $sql = "
            SELECT COUNT(a.numero) as qt_ant_cancelada
			  FROM projetos.atividades a
			 WHERE a.status_atual IN ('CANC','AGDF')
			   AND a.area         = 'GI'
		       --AND a.dt_fim_real  < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
			   AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
			   AND a.tipo         = 'S'
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
		 ";

        $result = $this->db->query($sql);
    }

    function anteriorSuspensasSuporte(&$result, $args=array())
    {
        $sql = "
            SELECT COUNT(DISTINCT(a.numero)) as qt_ant_suspensa
			  FROM projetos.atividades a,
			       projetos.atividade_historico h
			 WHERE a.status_atual   IN ('SUSP','ADIR')
			   AND a.numero         = h.cd_atividade
			   AND h.dt_inicio_prev = 
                   (
                   SELECT MAX(h2.dt_inicio_prev)
                     FROM projetos.atividade_historico h2
                    WHERE h2.cd_atividade = a.numero
                   )
			   AND h.status_atual   IN ('SUSP','ADIR')
			   AND a.area           = 'GI'
			   --AND h.dt_inicio_prev < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
			   AND DATE_TRUNC('day',h.dt_inicio_prev) < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
			   AND a.tipo           = 'S'
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
		 ";

        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function abertasSuporte(&$result, $args=array())
    {
        $sql = "
            SELECT EXTRACT(month FROM a.dt_cad) AS nr_mes, 
                   COUNT(a.numero) AS qt_atividade 
              FROM projetos.atividades a
             WHERE a.area   = 'GI'
               --AND a.dt_cad BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               --AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
               AND DATE_TRUNC('day',a.dt_cad) BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY') 
               AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
			   ".(trim($args['mes']) != '' ? "AND TO_CHAR(a.dt_cad, 'MM') = '".trim($args['mes'])."'" : '')."
               AND a.tipo   = 'S'
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
             GROUP BY extract(month FROM a.dt_cad)
             ORDER BY extract(month FROM a.dt_cad)
		 ";

        $result = $this->db->query($sql);
    }

    function concluidasSuporte(&$result, $args=array())
    {
        $sql = "
            SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes,
                   COUNT(a.numero) AS qt_atividade
              FROM projetos.atividades a
             WHERE a.status_atual = 'CONC'
               AND a.area         = 'GI'
               --AND a.dt_fim_real  BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               --AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
               AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
			   ".(trim($args['mes']) != '' ? "AND TO_CHAR(a.dt_fim_real, 'MM') = '".trim($args['mes'])."'" : '')."
               AND a.tipo         = 'S'
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
             GROUP BY extract(month FROM a.dt_fim_real)
             ORDER BY extract(month FROM a.dt_fim_real)
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function concluidaCritAutoSuporte(&$result, $args=array())
    {
        $sql = "
            SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes,
                   COUNT(a.numero) AS qt_atividade
              FROM projetos.atividades a
             WHERE a.status_atual = 'CONC'
               AND a.area         = 'GI'
               AND a.fl_encerrado_automatico = 'S'
               AND a.fl_teste_relevante      = 'S'
               AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
               AND a.tipo         = 'S'
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
             GROUP BY extract(month FROM a.dt_fim_real)
		     ORDER BY extract(month FROM a.dt_fim_real)
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }
    
    function concluidaCritUserSuporte(&$result, $args=array())
    {
        $sql = "
            SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes,
                   COUNT(a.numero) AS qt_atividade
              FROM projetos.atividades a
             WHERE a.status_atual = 'CONC'
               AND a.area         = 'GI'
               AND a.fl_encerrado_automatico = 'N'
               AND a.fl_teste_relevante      = 'S'
               AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
               AND a.tipo         = 'S'
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
             GROUP BY extract(month FROM a.dt_fim_real)
		     ORDER BY extract(month FROM a.dt_fim_real)
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function concluidaCritNaoSuporte(&$result, $args=array())
    {
        $sql = "
            SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes,
                   COUNT(a.numero) AS qt_atividade
              FROM projetos.atividades a
             WHERE a.status_atual = 'CONC'
               AND a.area         = 'GI'
               AND a.fl_teste_relevante      = 'N'
               AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
               AND a.tipo         = 'S'
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
             GROUP BY extract(month FROM a.dt_fim_real)
		     ORDER BY extract(month FROM a.dt_fim_real)
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function canceladasSuporte(&$result, $args=array())
    {
        $sql = "
            SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes,
                   COUNT(a.numero) AS qt_atividade
              FROM projetos.atividades a
             WHERE a.status_atual IN ('CANC','AGDF')
               AND a.area         = 'GI'
               --AND a.dt_fim_real  BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               --AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
               AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
			   ".(trim($args['mes']) != '' ? "AND TO_CHAR(a.dt_fim_real, 'MM') = '".trim($args['mes'])."'" : '')."
               AND a.tipo         = 'S'
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
             GROUP BY extract(month FROM a.dt_fim_real)
		     ORDER BY extract(month FROM a.dt_fim_real)
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }
    
    function suspensasSuporte(&$result, $args=array())
    {
        $sql = "
            SELECT EXTRACT(month FROM h.dt_inicio_prev) AS nr_mes,
                   COUNT(DISTINCT(a.numero)) AS qt_atividade
              FROM projetos.atividades a,
                   projetos.atividade_historico h
             WHERE a.status_atual    IN ('SUSP','ADIR')
               AND a.numero          = h.cd_atividade
               AND h.dt_inicio_prev  =
                   (
                   SELECT MAX(h2.dt_inicio_prev)
                     FROM projetos.atividade_historico h2
                    WHERE h2.cd_atividade = a.numero
                   )
               AND h.status_atual    IN ('SUSP','ADIR')
               --AND h.dt_inicio_prev  BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               --AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
               AND DATE_TRUNC('day',h.dt_inicio_prev) BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
               AND a.area            = 'GI'
               AND a.tipo            = 'S'
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
             GROUP BY extract(month FROM h.dt_inicio_prev) 
			 ORDER BY extract(month FROM h.dt_inicio_prev)

            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function anteriorAbertasSistema(&$result, $args=array())
    {
        $sql = "
            SELECT COUNT(a.numero) as qt_ant_aberta
              FROM projetos.atividades a
             WHERE a.area   = 'GI'
             --AND a.dt_cad < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND DATE_TRUNC('day',a.dt_cad) < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND a.tipo   NOT IN('S','L')
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function anteriorConcluidaCritAutoSistema(&$result, $args=array())
    {
        $sql = "
            SELECT COUNT(a.numero) as qt_ant_concluida
              FROM projetos.atividades a
             WHERE a.status_atual = 'CONC'
               AND a.area         = 'GI'
               AND a.fl_encerrado_automatico = 'S'
               AND a.fl_teste_relevante      = 'S'
               AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND a.tipo         NOT IN('S','L')
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function anteriorConcluidaCritUserSistema(&$result, $args=array())
    {
        $sql = "
            SELECT COUNT(a.numero) as qt_ant_concluida
              FROM projetos.atividades a
             WHERE a.status_atual = 'CONC'
               AND a.area         = 'GI'
               AND a.fl_encerrado_automatico = 'N'
               AND a.fl_teste_relevante      = 'S'
               AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND a.tipo         NOT IN('S','L')
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function anteriorConcluidaCritNaoSistema(&$result, $args=array())
    {
        $sql = "
            SELECT COUNT(a.numero) as qt_ant_concluida
              FROM projetos.atividades a
             WHERE a.status_atual = 'CONC'
               AND a.area         = 'GI'
               AND a.fl_teste_relevante      = 'N'
               AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND a.tipo         NOT IN('S','L')
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function anteriorConcluidaSistema(&$result, $args=array())
    {
        $sql = "
            SELECT COUNT(a.numero) as qt_ant_concluida
              FROM projetos.atividades a
             WHERE a.status_atual = 'CONC'
               AND a.area         = 'GI'
               AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND a.tipo         NOT IN('S','L')
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function anteriorCanceladasSistema(&$result, $args=array())
    {
        $sql = "
            SELECT COUNT(a.numero) as qt_ant_cancelada
              FROM projetos.atividades a
             WHERE a.status_atual IN ('CANC','AGDF')
               AND a.area         = 'GI'
               --AND a.dt_fim_real  < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND a.tipo         NOT IN('S','L')
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function anteriorSuspensasSistema(&$result, $args=array())
    {
        $sql = "
            SELECT COUNT(DISTINCT(a.numero)) as qt_ant_suspensa
              FROM projetos.atividades a,
                   projetos.atividade_historico h
             WHERE a.status_atual   IN ('SUSP','ADIR')
               AND a.numero         = h.cd_atividade
               AND h.dt_inicio_prev =
                   (
                    SELECT MAX(h2.dt_inicio_prev)
                      FROM projetos.atividade_historico h2
                     WHERE h2.cd_atividade = a.numero
                    )
               AND h.status_atual   IN ('SUSP','ADIR')
               AND a.area           = 'GI'
               --AND h.dt_inicio_prev < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND DATE_TRUNC('day',h.dt_inicio_prev) < TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND a.tipo           NOT IN('S','L')
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function abertasSistema(&$result, $args=array())
    {
        $sql = "
            SELECT EXTRACT(month FROM a.dt_cad) AS nr_mes,
                   COUNT(a.numero) AS qt_atividade
              FROM projetos.atividades a
             WHERE a.area   = 'GI'
               --AND a.dt_cad BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               --AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
               AND DATE_TRUNC('day',a.dt_cad) BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
			   ".(trim($args['mes']) != '' ? "AND TO_CHAR(a.dt_cad, 'MM') = '".trim($args['mes'])."'" : '')."
               AND a.tipo   NOT IN('S','L')
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
             GROUP BY extract(month FROM a.dt_cad)
			 ORDER BY extract(month FROM a.dt_cad)
            ";


        $result = $this->db->query($sql);
    }

    function concluidaCritAutoSistema(&$result, $args=array())
    {
        $sql = "
            SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes,
                   COUNT(a.numero) AS qt_atividade
              FROM projetos.atividades a
             WHERE a.status_atual = 'CONC'
               AND a.area         = 'GI'
               AND a.fl_encerrado_automatico = 'S'
               AND a.fl_teste_relevante      = 'S'
               AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
               AND a.tipo         NOT IN('S','L')
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
             GROUP BY extract(month FROM a.dt_fim_real)
			 ORDER BY extract(month FROM a.dt_fim_real)
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function concluidaCritUserSistema(&$result, $args=array())
    {
        $sql = "
            SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes,
                   COUNT(a.numero) AS qt_atividade
              FROM projetos.atividades a
             WHERE a.status_atual = 'CONC'
               AND a.area         = 'GI'
               AND a.fl_encerrado_automatico = 'N'
               AND a.fl_teste_relevante      = 'S'
               AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
               AND a.tipo         NOT IN('S','L')
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
             GROUP BY extract(month FROM a.dt_fim_real)
			 ORDER BY extract(month FROM a.dt_fim_real)
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function concluidaCritNaoSistema(&$result, $args=array())
    {
        $sql = "
            SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes,
                   COUNT(a.numero) AS qt_atividade
              FROM projetos.atividades a
             WHERE a.status_atual = 'CONC'
               AND a.area         = 'GI'
               AND a.fl_teste_relevante      = 'N'
               AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
               AND a.tipo         NOT IN('S','L')
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
             GROUP BY extract(month FROM a.dt_fim_real)
			 ORDER BY extract(month FROM a.dt_fim_real)
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function concluidasSistema(&$result, $args=array())
    {
        $sql = "
            SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes,
                   COUNT(a.numero) AS qt_atividade
              FROM projetos.atividades a
             WHERE a.status_atual = 'CONC'
               AND a.area         = 'GI'
               --AND a.dt_fim_real  BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               --AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
               AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY') AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
			   ".(trim($args['mes']) != '' ? "AND TO_CHAR(a.dt_fim_real, 'MM') = '".trim($args['mes'])."'" : '')."
               AND a.tipo         NOT IN('S','L')
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
             GROUP BY extract(month FROM a.dt_fim_real)
			 ORDER BY extract(month FROM a.dt_fim_real)
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function canceladasSistema(&$result, $args=array())
    {
        $sql = "
            SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes,
                   COUNT(a.numero) AS qt_atividade
              FROM projetos.atividades a
             WHERE a.status_atual IN ('CANC','AGDF')
               AND a.area         = 'GI'
               --AND a.dt_fim_real  BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               --AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
               AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
			   ".(trim($args['mes']) != '' ? "AND TO_CHAR(a.dt_fim_real, 'MM') = '".trim($args['mes'])."'" : '')."
               AND a.tipo         NOT IN('S','L')
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
             GROUP BY extract(month FROM a.dt_fim_real)
			 ORDER BY extract(month FROM a.dt_fim_real)
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function suspensasSistema(&$result, $args=array())
    {
        $sql = "
            SELECT EXTRACT(month FROM h.dt_inicio_prev) AS nr_mes,
                   COUNT(DISTINCT(a.numero)) AS qt_atividade
              FROM projetos.atividades a,
                   projetos.atividade_historico h
             WHERE a.status_atual    IN ('SUSP','ADIR')
               AND a.numero          = h.cd_atividade
               AND h.dt_inicio_prev  = (SELECT MAX(h2.dt_inicio_prev) FROM projetos.atividade_historico h2 WHERE h2.cd_atividade = a.numero)
               AND h.status_atual    IN ('SUSP','ADIR')
               --AND h.dt_inicio_prev  BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               --AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
               AND DATE_TRUNC('day',h.dt_inicio_prev) BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY')
               AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
               AND a.area            = 'GI'
               AND a.tipo            NOT IN('S','L')
               ".(trim($args["cd_usuario"]) != "" ? " AND a.cod_atendente = ".intval($args['cd_usuario']) : "")."
             GROUP BY extract(month FROM h.dt_inicio_prev)
			 ORDER BY extract(month FROM h.dt_inicio_prev)
            ";
        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }

    function resumoDivisao(&$result, $args=array())
    {
        $sql = "
            SELECT DISTINCT(a.divisao) AS ds_divisao,
			       ab.qt_aberta,
			       co.qt_concluida,
			       ca.qt_cancelada,
			       su.qt_suspensa,
			       COALESCE(co.qt_concluida,0) + COALESCE(ca.qt_cancelada,0) AS qt_atendida
			  FROM projetos.atividades a
		      LEFT JOIN -- ABERTA
			     (
                 SELECT a1.divisao,
			            COUNT(a1.numero) as qt_aberta
			       FROM projetos.atividades a1
			      WHERE a1.area   = 'GI'
			        AND a1.tipo   <> 'L'
	                ".(trim($args["cd_usuario"]) != "" ? " AND a1.cod_atendente = ".intval($args['cd_usuario']) : "")."
				  GROUP BY a1.divisao
                  )
                  AS ab ON ab.divisao = a.divisao
			  LEFT JOIN -- CONCLUIDA
			      (
                  SELECT a1.divisao,
				         COUNT(a1.numero) AS qt_concluida
				    FROM projetos.atividades a1
				   WHERE a1.status_atual = 'CONC'
				     AND a1.area         = 'GI'
				     AND a1.tipo         <> 'L'
	                 ".(trim($args["cd_usuario"]) != "" ? " AND a1.cod_atendente = ".intval($args['cd_usuario']) : "")."
				   GROUP BY a1.divisao
                  )
                  AS co ON co.divisao = a.divisao
			  LEFT JOIN -- CANCELADA
				 (
                 SELECT a1.divisao,
					    COUNT(a1.numero) AS qt_cancelada
			       FROM projetos.atividades a1
			      WHERE a1.status_atual IN ('CANC','AGDF')
			        AND a1.area         = 'GI'
			       AND a1.tipo         <> 'L'
                   ".(trim($args["cd_usuario"]) != "" ? " AND a1.cod_atendente = ".intval($args['cd_usuario']) : "")."
				 GROUP BY a1.divisao
                 )
                 AS ca ON ca.divisao = a.divisao
			 LEFT JOIN -- SUSPENSA
				(
                SELECT a1.divisao,
				       COUNT(DISTINCT(a1.numero)) as qt_suspensa
				  FROM projetos.atividades a1,
				       projetos.atividade_historico h1
			     WHERE a1.status_atual   IN ('SUSP','ADIR')
			       AND a1.numero         = h1.cd_atividade
			       AND h1.dt_inicio_prev =
                     (
                     SELECT MAX(h2.dt_inicio_prev)
                       FROM projetos.atividade_historico h2
                      WHERE h2.cd_atividade = a1.numero
                     )
				   AND h1.status_atual   IN ('SUSP','ADIR')
				   AND a1.area           = 'GI'
				   AND a1.tipo           <> 'L'
                   ".(trim($args["cd_usuario"]) != "" ? " AND a1.cod_atendente = ".intval($args['cd_usuario']) : "")."
		         GROUP BY a1.divisao) AS su ON su.divisao = a.divisao
				 WHERE a.area = 'GI'
				   AND a.tipo <> 'L'
				 ";

        #echo '<pre>'.$sql.'</pre>';

        $result = $this->db->query($sql);
    }
	

}
?>