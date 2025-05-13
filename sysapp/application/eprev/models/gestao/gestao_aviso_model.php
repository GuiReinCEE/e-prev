<?php
class gestao_aviso_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {       
        $qr_sql = "
                    SELECT ra.cd_gestao_aviso,
                           ra.ds_descricao,
                           ra.cd_periodicidade,
						   ra.qt_dia,
                           CASE WHEN ra.cd_periodicidade = 'E' THEN 'Eventual'
                                WHEN ra.cd_periodicidade = 'D' THEN 'Diária'
                                WHEN ra.cd_periodicidade = 'S' THEN 'Semanal'
                                WHEN ra.cd_periodicidade = 'L' THEN 'Semestral'
                                WHEN ra.cd_periodicidade = 'T' THEN 'Trimestral'
                                WHEN ra.cd_periodicidade = 'Q' THEN 'Quadrimestral'
                                WHEN ra.cd_periodicidade = 'M' THEN 'Mensal'
                                WHEN ra.cd_periodicidade = 'A' THEN 'Anual'
                                WHEN ra.cd_periodicidade = 'B' THEN 'Bianual'
                                WHEN ra.cd_periodicidade = 'U' THEN 'Mensal (Dias úteis)'
                                WHEN ra.cd_periodicidade = 'N' THEN 'Mensal (Dias úteis - Antes da Data)'
                                ELSE ''
                           END AS periodicidade,
                           CASE WHEN ra.cd_periodicidade = 'E' THEN 'label-warning'
                                WHEN ra.cd_periodicidade = 'D' THEN 'label-important'
                                WHEN ra.cd_periodicidade = 'S' THEN 'label-inverse'
                                WHEN ra.cd_periodicidade = 'L' THEN 'label-inverse'
                                WHEN ra.cd_periodicidade = 'T' THEN ''
                                WHEN ra.cd_periodicidade = 'Q' THEN ''
                                WHEN ra.cd_periodicidade = 'M' THEN 'label-info'
                                WHEN ra.cd_periodicidade = 'A' THEN 'label-success'
                                WHEN ra.cd_periodicidade = 'B' THEN 'label-success'
                                WHEN ra.cd_periodicidade = 'U' THEN 'label-success'
                                WHEN ra.cd_periodicidade = 'N' THEN 'label-success'
                                ELSE ''
                           END AS cor_periodicidade,						   
                           TO_CHAR(ra.dt_referencia,'DD/MM/YYYY') AS dt_referencia,
						   CASE WHEN ra.cd_periodicidade = 'S' THEN
								(CASE EXTRACT(DOW FROM ra.dt_referencia) 
									  WHEN 0 THEN 'Domingo'
                                      WHEN 1 THEN 'Segunda'
                                      WHEN 2 THEN 'Terça'
                                      WHEN 3 THEN 'Quarta'
                                      WHEN 4 THEN 'Quinta'
                                      WHEN 5 THEN 'Sexta'
                                      WHEN 6 THEN 'Sábado'
                                END)
								ELSE ''
						   END AS dia,
						   TO_CHAR(ra.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                           funcoes.get_usuario_nome(ra.cd_usuario_inclusao) AS usuario,
                           ra.cd_usuario_inclusao
                      FROM gestao.gestao_aviso ra
                     WHERE ra.dt_exclusao IS NULL
					 ".(trim($args['cd_periodicidade']) != "" ? "AND ra.cd_periodicidade = '".trim($args['cd_periodicidade'])."'" : "")."
					 ".(trim($args['cd_gerencia']) != "" ? "AND ra.cd_gerencia = '".trim($args['cd_gerencia'])."'" : "")."
                  ";
        $result = $this->db->query($qr_sql);
    }

    public function carrega($cd_gestao_aviso)
    {
        $qr_sql = "
            SELECT ra.cd_gestao_aviso,
                   ra.ds_descricao,
                   ra.cd_periodicidade,
                   ra.qt_dia,
                   CASE WHEN ra.cd_periodicidade = 'E' THEN 'Eventual'
                        WHEN ra.cd_periodicidade = 'D' THEN 'Diária'
                        WHEN ra.cd_periodicidade = 'S' THEN 'Semanal'
                        WHEN ra.cd_periodicidade = 'L' THEN 'Semestral'
                        WHEN ra.cd_periodicidade = 'T' THEN 'Trimestral'
                        WHEN ra.cd_periodicidade = 'Q' THEN 'Quadrimestral'
                        WHEN ra.cd_periodicidade = 'M' THEN 'Mensal'
                        WHEN ra.cd_periodicidade = 'A' THEN 'Anual'
                        WHEN ra.cd_periodicidade = 'B' THEN 'Bianual'
                        WHEN ra.cd_periodicidade = 'U' THEN 'Mensal (Dias úteis)'
                        WHEN ra.cd_periodicidade = 'N' THEN 'Mensal (Dias úteis - Antes da Data)'
                        ELSE ''
                   END AS periodicidade,
                   CASE WHEN ra.cd_periodicidade = 'E' THEN 'label-warning'
                        WHEN ra.cd_periodicidade = 'D' THEN 'label-important'
                        WHEN ra.cd_periodicidade = 'S' THEN 'label-inverse'
                        WHEN ra.cd_periodicidade = 'L' THEN 'label-inverse'
                        WHEN ra.cd_periodicidade = 'T' THEN ''
                        WHEN ra.cd_periodicidade = 'Q' THEN ''
                        WHEN ra.cd_periodicidade = 'M' THEN 'label-info'
                        WHEN ra.cd_periodicidade = 'A' THEN 'label-success'
                        WHEN ra.cd_periodicidade = 'B' THEN 'label-success'
                        WHEN ra.cd_periodicidade = 'U' THEN 'label-success'
                        WHEN ra.cd_periodicidade = 'N' THEN 'label-success'
                        ELSE ''
                   END AS cor_periodicidade,                           
                   TO_CHAR(ra.dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   CASE WHEN ra.cd_periodicidade = 'S' THEN
                        (CASE EXTRACT(DOW FROM ra.dt_referencia) 
                              WHEN 0 THEN 'Domingo'
                              WHEN 1 THEN 'Segunda'
                              WHEN 2 THEN 'Terça'
                              WHEN 3 THEN 'Quarta'
                              WHEN 4 THEN 'Quinta'
                              WHEN 5 THEN 'Sexta'
                              WHEN 6 THEN 'Sábado'
                        END)
                        ELSE ''
                   END AS dia,
                   TO_CHAR(ra.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(ra.cd_usuario_inclusao) AS usuario,
                   ra.cd_usuario_inclusao,
                   (SELECT COUNT(*) FROM temporario.os71025 t WHERE t.cd_gestao_aviso = ra.cd_gestao_aviso) tl_gestao_aviso_controle
              FROM gestao.gestao_aviso ra
             WHERE cd_gestao_aviso = ".intval($cd_gestao_aviso)."
               AND ra.dt_exclusao IS NULL ;";

        return $this->db->query($qr_sql)->row_array();
    }   

    function cadastro(&$result, $args=array())
    {       
        $qr_sql = "
            SELECT cd_gestao_aviso,
                   ds_descricao
              FROM gestao.gestao_aviso
             WHERE cd_gestao_aviso = ".intval($args["cd_gestao_aviso"])."
                  ";
        $result = $this->db->query($qr_sql);
    } 

    function salvar(&$result, $args=array())
    {
		$cd_gestao_aviso = intval($this->db->get_new_id("gestao.gestao_aviso", "cd_gestao_aviso"));
		
		$qr_sql = "
                    INSERT INTO gestao.gestao_aviso
                         (
							cd_gestao_aviso,
							ds_descricao,
							cd_periodicidade,
							qt_dia,
							dt_referencia,
							cd_gerencia,
                            fl_diretoria,
							cd_usuario_inclusao,
                            cd_usuario_alteracao
                         )
                    VALUES
                         (
                            ".intval($cd_gestao_aviso).",
							".(trim($args["ds_descricao"]) != "" ? "'".trim($args["ds_descricao"])."'" : "DEFAULT").",
                            ".(trim($args["cd_periodicidade"]) != "" ? "'".trim($args["cd_periodicidade"])."'" : "DEFAULT").",
							".(trim($args["qt_dia"]) != "" ? intval($args["qt_dia"]) : "DEFAULT").",
							".(trim($args["dt_referencia"]) != "" ? "TO_DATE('".trim($args["dt_referencia"])."','DD/MM/YYYY')" : "DEFAULT").",
							".(trim($args["cd_gerencia"]) != "" ? "'".trim($args["cd_gerencia"])."'" : "DEFAULT").",
                            ".(trim($args["fl_diretoria"]) != "" ? "'".trim($args["fl_diretoria"])."'": "DEFAULT").",
                            ".intval($args["cd_usuario_inclusao"]).",
                            ".intval($args['cd_usuario_alteracao'])."
                         );
                  ";
        
		foreach($args['ar_usuario'] as $item)
		{
			$qr_sql.= "
						INSERT INTO gestao.gestao_aviso_usuario
							 (
								cd_gestao_aviso,
								cd_usuario,
                                cd_usuario_inclusao
							 )
						VALUES
 						     (
								".intval($cd_gestao_aviso).",
								".intval($item).",
                                ".intval($args['cd_usuario_inclusao'])."
							 );
					  ";
						 
		}	

		$qr_sql.= "SELECT rotinas.gestao_aviso_verificacao();";
		
        #echo "<PRE>$qr_sql".print_r($args,true)."</PRE>"; exit;
        $result = $this->db->query($qr_sql);
    }

    public function atualizar($cd_gestao_aviso, $args, $ar_usuario = 0)
    {
        $qr_sql = "
            UPDATE gestao.gestao_aviso
               SET ds_descricao = ".(trim($args['ds_descricao']) != '' ? "'".trim($args['ds_descricao'])."'" : "DEFAULT").",
                   cd_periodicidade = ".(trim($args["cd_periodicidade"]) != "" ? "'".trim($args["cd_periodicidade"])."'" : "DEFAULT").", 
                   qt_dia = ".(trim($args["qt_dia"]) != "" ? intval($args["qt_dia"]) : "DEFAULT").",
                   dt_referencia = ".(trim($args["dt_referencia"]) != "" ? "TO_DATE('".trim($args["dt_referencia"])."','DD/MM/YYYY')" : "DEFAULT").", 
                   cd_gerencia = ".(trim($args["cd_gerencia"]) != "" ? "'".trim($args["cd_gerencia"])."'" : "DEFAULT").",
                   dt_alteracao = CURRENT_TIMESTAMP, 
                   cd_usuario_inclusao = ".intval($args['cd_usuario_inclusao'])."

             WHERE cd_gestao_aviso = ".intval($cd_gestao_aviso).";";

        if(count($ar_usuario) > 0)
        {
            $qr_sql .= "
                UPDATE gestao.gestao_aviso_usuario
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario_exclusao']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_gestao_aviso = ".intval($cd_gestao_aviso)."
                   AND dt_exclusao IS NULL
                   AND cd_usuario NOT IN (".implode(",", $args['ar_usuario']).");
       
                INSERT INTO gestao.gestao_aviso_usuario
                (
                    cd_gestao_aviso, 
                    cd_usuario, 
                    cd_usuario_inclusao
                )
                SELECT ".intval($cd_gestao_aviso).", x.column1, ".intval($args['cd_usuario_inclusao'])."
                  FROM (VALUES (".implode("),(", $args['ar_usuario']).")) x
                 WHERE x.column1 NOT IN (
                                        SELECT a.cd_usuario
                                          FROM gestao.gestao_aviso_usuario a
                                         WHERE a.cd_gestao_aviso = ".intval($cd_gestao_aviso)."
                                           AND a.dt_exclusao IS NULL);";              
        }

        $qr_sql .= "
            DELETE
              FROM gestao.gestao_aviso_verificacao x
             WHERE cd_gestao_aviso = ".intval($cd_gestao_aviso)."
               AND dt_verificacao  IS NULL
               AND (SELECT COUNT(*) FROM gestao.gestao_aviso_verificacao_acompanhamento a WHERE a.cd_gestao_aviso_verificacao = x.cd_gestao_aviso_verificacao AND a.dt_exclusao IS NULL) = 0;

            SELECT rotinas.gestao_aviso_verificacao();"; 

        $this->db->query($qr_sql);
    }
	
    function excluir(&$result, $args=array())
    {
		$qr_sql = "
                    UPDATE gestao.gestao_aviso
					   SET dt_exclusao         = CURRENT_TIMESTAMP,
					       cd_usuario_exclusao = ".intval($args["cd_usuario"])."
					 WHERE cd_gestao_aviso = ".intval($args["cd_gestao_aviso"])."
                  ";
        
        #echo "<PRE>$qr_sql".print_r($args,true)."</PRE>"; exit;
        $result = $this->db->query($qr_sql);
    }	
	
    function verificar(&$result, $args=array())
    {       
        $qr_sql = "
                    SELECT ra.cd_gestao_aviso,
					       rav.cd_gestao_aviso_verificacao,
                           ra.ds_descricao,
                           ra.cd_periodicidade,
                           CASE WHEN ra.cd_periodicidade = 'E' THEN 'Eventual'
                                WHEN ra.cd_periodicidade = 'D' THEN 'Diária'
                                WHEN ra.cd_periodicidade = 'S' THEN 'Semanal'
                                WHEN ra.cd_periodicidade = 'L' THEN 'Semestral'
                                WHEN ra.cd_periodicidade = 'T' THEN 'Trimestral'
                                WHEN ra.cd_periodicidade = 'M' THEN 'Mensal'
                                WHEN ra.cd_periodicidade = 'A' THEN 'Anual'
                                WHEN ra.cd_periodicidade = 'B' THEN 'Bianual'
                                WHEN ra.cd_periodicidade = 'U' THEN 'Mensal (Dias úteis)'
                                WHEN ra.cd_periodicidade = 'N' THEN 'Mensal (Dias úteis - Antes da Data)'
                                ELSE ''
                           END AS periodicidade,
                           TO_CHAR(rav.dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                           TO_CHAR(rav.dt_verificacao,'DD/MM/YYYY HH24:MI:SS') AS dt_verificacao,
						   funcoes.get_usuario_nome(rav.cd_usuario_verificacao) AS usuario,
						   'Este item foi verificado por ' || funcoes.get_usuario_nome(rav.cd_usuario_verificacao) || ' em ' || TO_CHAR(rav.dt_verificacao,'DD/MM/YYYY HH24:MI:SS') || '.' AS verificado
                      FROM gestao.gestao_aviso ra
					  JOIN gestao.gestao_aviso_verificacao rav
						ON ra.cd_gestao_aviso = rav.cd_gestao_aviso					  
                     WHERE ra.dt_exclusao IS NULL
					   AND rav.cd_gestao_aviso_verificacao = ".intval($args["cd_gestao_aviso_verificacao"])."
                  ";
        $result = $this->db->query($qr_sql);
    } 

    function listar_verificar(&$result, $args=array())
    {       
        $qr_sql = "
                    SELECT ra.cd_gestao_aviso,
                           rav.cd_gestao_aviso_verificacao,
                           ra.ds_descricao,
                           ra.cd_periodicidade,
                           CASE WHEN ra.cd_periodicidade = 'E' THEN 'Eventual'
                                WHEN ra.cd_periodicidade = 'D' THEN 'Diária'
                                WHEN ra.cd_periodicidade = 'S' THEN 'Semanal'
                                WHEN ra.cd_periodicidade = 'L' THEN 'Semestral'
                                WHEN ra.cd_periodicidade = 'T' THEN 'Trimestral'
                                WHEN ra.cd_periodicidade = 'M' THEN 'Mensal'
                                WHEN ra.cd_periodicidade = 'A' THEN 'Anual'
                                WHEN ra.cd_periodicidade = 'B' THEN 'Bianual'
                                WHEN ra.cd_periodicidade = 'U' THEN 'Mensal (Dias úteis)'
                                WHEN ra.cd_periodicidade = 'N' THEN 'Mensal (Dias úteis - Antes da Data)'
                                ELSE ''
                           END AS periodicidade,
                           TO_CHAR(rav.dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                           TO_CHAR(rav.dt_verificacao,'DD/MM/YYYY HH24:MI:SS') AS dt_verificacao,
               funcoes.get_usuario_nome(rav.cd_usuario_verificacao) AS usuario,
               'Este item foi verificado por ' || funcoes.get_usuario_nome(rav.cd_usuario_verificacao) || ' em ' || TO_CHAR(rav.dt_verificacao,'DD/MM/YYYY HH24:MI:SS') || '.' AS verificado
                      FROM gestao.gestao_aviso ra
            LEFT JOIN gestao.gestao_aviso_verificacao rav
            ON ra.cd_gestao_aviso = rav.cd_gestao_aviso           
                     WHERE ra.dt_exclusao IS NULL
             AND ra.cd_gestao_aviso = ".intval($args["cd_gestao_aviso"])."
                  ";
        $result = $this->db->query($qr_sql);
    } 
	
    function verificarSalvar(&$result, $args=array())
    {
		$qr_sql = "
                    UPDATE gestao.gestao_aviso_verificacao
					   SET dt_verificacao         = CURRENT_TIMESTAMP,
					       cd_usuario_verificacao = ".intval($args["cd_usuario"])."
					 WHERE cd_gestao_aviso_verificacao = ".intval($args["cd_gestao_aviso_verificacao"])."
                  ";
        
        #echo "<PRE>$qr_sql".print_r($args,true)."</PRE>"; exit;
        $result = $this->db->query($qr_sql);
    }

    public function get_usuario_checked($cd_gestao_aviso, $fl_sem_diretoria = 'N') ## OK 
    {
        $qr_sql = " 
            SELECT gaa.cd_gestao_aviso_usuario, 
                   gaa.cd_gestao_aviso,
                   gaa.cd_usuario,
                   funcoes.get_usuario_nome(gaa.cd_usuario) AS ds_usuario
              FROM gestao.gestao_aviso_usuario gaa
              JOIN gestao.gestao_aviso ga
                ON ga.cd_gestao_aviso = gaa.cd_gestao_aviso
             WHERE ga.cd_gestao_aviso = ".intval($cd_gestao_aviso)."
               ".(trim($fl_sem_diretoria) == 'S' ? "AND gaa.cd_usuario NOT IN (SELECT codigo FROM projetos.usuarios_controledi WHERE divisao = 'DE' AND tipo = 'D')" : '')."
               AND gaa.dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }	
	
    function usuario(&$result, $args=array())
    {
		$qr_sql = "
				SELECT uc.codigo AS value,
                       uc.divisao || ' - ' || uc.nome AS text
                  FROM projetos.usuarios_controledi uc
                 WHERE uc.divisao NOT IN ('FC','SNG','CF','CEE')
                   AND uc.tipo NOT IN ('X', 'P')
                 ORDER BY uc.divisao,  uc.nome 
                  ";
        
        #echo "<PRE>$qr_sql".print_r($args,true)."</PRE>"; exit;
        $result = $this->db->query($qr_sql);
    }		

    public function listar_controle_pendencia($args = array())
    {
        $qr_sql = "
            SELECT ra.cd_gestao_aviso,
                   ra.ds_descricao,
                   TO_CHAR(rav.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
                   TO_CHAR(rav.dt_verificacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_verificacao,
                   funcoes.get_usuario_nome(rav.cd_usuario_verificacao) AS ds_usuario_verificado
              FROM gestao.gestao_aviso ra
              JOIN gestao.gestao_aviso_verificacao rav
                ON rav.cd_gestao_aviso = ra.cd_gestao_aviso
             WHERE ra.dt_exclusao IS NULL
               AND ra.cd_gestao_aviso IN (SELECT t.cd_gestao_aviso FROM temporario.os71025 t)
               ".(((trim($args['dt_referencia_ini']) != '') AND  (trim($args['dt_referencia_fim']) != '')) ? " AND DATE_TRUNC('day', rav.dt_referencia) BETWEEN TO_DATE('".$args['dt_referencia_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_referencia_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_verificacao_ini']) != '') AND  (trim($args['dt_verificacao_fim']) != '')) ? " AND DATE_TRUNC('day', rav.dt_verificacao) BETWEEN TO_DATE('".$args['dt_verificacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_verificacao_fim']."', 'DD/MM/YYYY')" : "")."
               ".(trim($args['fl_verificado']) == 'S' ? "AND rav.dt_verificacao IS NOT NULL" : '')."
               ".(trim($args['fl_verificado']) == 'N' ? "AND rav.dt_verificacao IS NULL" : '')."
             ORDER BY rav.dt_referencia DESC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_usuario_diretoria()
    {
        $qr_sql = "
            SELECT codigo AS cd_usuario
              FROM projetos.usuarios_controledi
             WHERE divisao = 'DE'
               AND tipo = 'D';";

        return $this->db->query($qr_sql)->result_array();
    }

    public function aviso_diretoria_listar($args = array())
    {
        $qr_sql = "
            SELECT ra.cd_gestao_aviso,
                   ra.ds_descricao,
                   TO_CHAR(rav.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
                   TO_CHAR(rav.dt_verificacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_verificacao,
                   funcoes.get_usuario_nome(rav.cd_usuario_verificacao) AS ds_usuario_verificado,
                   TO_CHAR(ra.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(ra.cd_usuario_inclusao) AS usuario_inclusao,
                   ra.cd_usuario_inclusao,
                   (SELECT TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') || ': ' ||ds_gestao_aviso_verificacao_acompanhamento 
                      FROM gestao.gestao_aviso_verificacao_acompanhamento a
                     WHERE a.dt_exclusao IS NULL
                       AND a.cd_gestao_aviso_verificacao = rav.cd_gestao_aviso_verificacao
                     ORDER BY dt_inclusao DESC
                     LIMIT 1) AS ds_acompanhamento
              FROM gestao.gestao_aviso ra
              JOIN gestao.gestao_aviso_verificacao rav
                ON rav.cd_gestao_aviso = ra.cd_gestao_aviso
             WHERE ra.dt_exclusao IS NULL
               AND ra.fl_diretoria = 'S'
               ".(((trim($args['dt_referencia_ini']) != '') AND  (trim($args['dt_referencia_fim']) != '')) ? " AND DATE_TRUNC('day', rav.dt_referencia) BETWEEN TO_DATE('".$args['dt_referencia_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_referencia_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_verificacao_ini']) != '') AND  (trim($args['dt_verificacao_fim']) != '')) ? " AND DATE_TRUNC('day', rav.dt_verificacao) BETWEEN TO_DATE('".$args['dt_verificacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_verificacao_fim']."', 'DD/MM/YYYY')" : "")."
               ".(trim($args['fl_verificado']) == 'S' ? "AND rav.dt_verificacao IS NOT NULL" : '')."
               ".(trim($args['fl_verificado']) == 'N' ? "AND rav.dt_verificacao IS NULL" : '')."
             ORDER BY rav.dt_referencia DESC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function acompanhamento_salvar($cd_gestao_aviso_verificacao, $ds_gestao_aviso_verificacao_acompanhamento, $cd_usuario)
    {
        $qr_sql = "
            INSERT INTO gestao.gestao_aviso_verificacao_acompanhamento
                 (
                    ds_gestao_aviso_verificacao_acompanhamento, 
                    cd_gestao_aviso_verificacao, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                  )
            VALUES 
                  (
                    ".(trim($ds_gestao_aviso_verificacao_acompanhamento) != '' ? str_escape($ds_gestao_aviso_verificacao_acompanhamento) : "DEFAULT").",
                    ".intval($cd_gestao_aviso_verificacao).",
                    ".intval($cd_usuario).",
                    ".intval($cd_usuario)."
                  );";

        $this->db->query($qr_sql);
    }

    public function acompanhamento_listar($cd_gestao_aviso_verificacao)
    {
        $qr_sql = "
            SELECT cd_gestao_aviso_verificacao_acompanhamento,
                   ds_gestao_aviso_verificacao_acompanhamento,
                   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
              FROM gestao.gestao_aviso_verificacao_acompanhamento
             WHERE dt_exclusao IS NULL
               AND cd_gestao_aviso_verificacao = ".intval($cd_gestao_aviso_verificacao)."
            ORDER BY cd_gestao_aviso_verificacao_acompanhamento DESC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function aviso_diretoria_acompanhamento_listar($cd_gestao_aviso)
    {
        $qr_sql = "
            SELECT a.cd_gestao_aviso_verificacao_acompanhamento,
                   a.ds_gestao_aviso_verificacao_acompanhamento,
                   TO_CHAR(a.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
              FROM gestao.gestao_aviso_verificacao_acompanhamento a
              JOIN gestao.gestao_aviso_verificacao v
                ON v.cd_gestao_aviso_verificacao = a.cd_gestao_aviso_verificacao
             WHERE a.dt_exclusao IS NULL
               AND v.cd_gestao_aviso = ".intval($cd_gestao_aviso)."
            ORDER BY a.cd_gestao_aviso_verificacao_acompanhamento DESC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function aviso_diretoria_minhas_listar($cd_usuario, $args = array())
    {
        $qr_sql = "
            SELECT ra.cd_gestao_aviso,
                   ra.ds_descricao,
                   TO_CHAR(rav.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
                   TO_CHAR(rav.dt_verificacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_verificacao,
                   funcoes.get_usuario_nome(rav.cd_usuario_verificacao) AS ds_usuario_verificado,
                   TO_CHAR(ra.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(ra.cd_usuario_inclusao) AS usuario_inclusao,
                   rav.cd_gestao_aviso_verificacao,
                   ra.cd_usuario_inclusao,
                   (SELECT TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') || ': ' ||ds_gestao_aviso_verificacao_acompanhamento 
                      FROM gestao.gestao_aviso_verificacao_acompanhamento a
                     WHERE a.dt_exclusao IS NULL
                       AND a.cd_gestao_aviso_verificacao = rav.cd_gestao_aviso_verificacao
                     ORDER BY dt_inclusao DESC
                     LIMIT 1) AS ds_acompanhamento
              FROM gestao.gestao_aviso ra
              JOIN gestao.gestao_aviso_verificacao rav
                ON rav.cd_gestao_aviso = ra.cd_gestao_aviso
             WHERE ra.dt_exclusao IS NULL
               AND ra.fl_diretoria = 'S'
               AND (SELECT COUNT(*) FROM gestao.gestao_aviso_usuario gau WHERE gau.dt_exclusao IS NULL AND gau.cd_usuario = ".intval($cd_usuario).") > 0
               ".(((trim($args['dt_referencia_ini']) != '') AND  (trim($args['dt_referencia_fim']) != '')) ? " AND DATE_TRUNC('day', rav.dt_referencia) BETWEEN TO_DATE('".$args['dt_referencia_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_referencia_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_verificacao_ini']) != '') AND  (trim($args['dt_verificacao_fim']) != '')) ? " AND DATE_TRUNC('day', rav.dt_verificacao) BETWEEN TO_DATE('".$args['dt_verificacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_verificacao_fim']."', 'DD/MM/YYYY')" : "")."
               ".(trim($args['fl_verificado']) == 'S' ? "AND rav.dt_verificacao IS NOT NULL" : '')."
               ".(trim($args['fl_verificado']) == 'N' ? "AND rav.dt_verificacao IS NULL" : '')."
             ORDER BY rav.dt_referencia DESC;";

        return $this->db->query($qr_sql)->result_array();
    }
}