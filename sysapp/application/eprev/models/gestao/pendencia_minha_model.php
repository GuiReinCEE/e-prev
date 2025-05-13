<?php
class pendencia_minha_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function listar(&$result, $args=array())
    {
        $qr_sql = "
					SELECT p.cd_pendencia,
                           p.ds_descricao,
                           p.link,
                           TO_CHAR(p.dt_limite,'DD/MM/YYYY') AS dt_limite,
                           CASE WHEN CAST(p.dt_limite AS DATE) < CURRENT_DATE THEN 'S' ELSE 'N' END AS fl_atrasada,
                           CASE WHEN CAST(p.dt_limite AS DATE) < CURRENT_DATE THEN 'Sim' ELSE 'Não' END AS ds_atrasada,
						   CASE WHEN CAST(p.dt_limite AS DATE) < CURRENT_DATE THEN 'label-important' 
						        WHEN CAST((p.dt_limite - '2 week'::INTERVAL) AS DATE) < CURRENT_DATE THEN 'label-warning'
						        ELSE '' 
						   END AS cor_limite,
                           p.cd_responsavel,
                           p.cd_substituto,
                           funcoes.get_usuario_nome(p.cd_responsavel) AS ds_responsavel,
                           funcoes.get_usuario_nome(p.cd_substituto) AS ds_substituto
                      FROM gestao.pendencia(".intval($args["cd_usuario"]).") p
					 WHERE p.cd_pendencia IN ('".(is_array(($args['cd_pendencia'])) ? implode($args['cd_pendencia'],"','") : "")."')
					 ".(trim($args['fl_atrasada']) != '' ? "AND (CASE WHEN CAST(p.dt_limite AS DATE) < CURRENT_DATE THEN 'S' ELSE 'N' END) = '".trim($args['fl_atrasada'])."'" : "")."
					 ".(((trim($args['dt_limite_ini']) != "") and  (trim($args['dt_limite_fim']) != "")) ? " AND CAST(p.dt_limite AS DATE) BETWEEN TO_DATE('".$args['dt_limite_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_limite_fim']."', 'DD/MM/YYYY')" : "")."
                     ORDER BY p.dt_limite,
                              p.ds_descricao
                  ";
		#echo "<PRE>$qr_sql</PRE>";
        $result = $this->db->query($qr_sql);
    }
	
    function checarPC(&$result, $args=array())
    {
		
		if(intval($args["cd_usuario"]) > 0)
		{
			$qr_sql = "
						SELECT COUNT(*) AS qt_pendencia,
						       SUM(CASE WHEN CAST(p.dt_limite AS DATE) < CURRENT_DATE THEN 1 ELSE 0 END) AS qt_atrasada
						  FROM gestao.pendencia(".intval($args["cd_usuario"]).") p
						 WHERE 1 = (CASE WHEN CAST(p.dt_limite AS DATE) < CURRENT_DATE THEN 1
										 WHEN CAST((p.dt_limite - '2 week'::INTERVAL) AS DATE) < CURRENT_DATE THEN 1
										 ELSE 0 
								   END)
						   AND (EXTRACT(HOUR FROM CURRENT_TIMESTAMP) BETWEEN 7 AND 20) = TRUE
						   AND 0 < (SELECT COUNT(*)
                                      FROM projetos.usuarios_controledi uc
                                     WHERE uc.tipo <> 'D'     
									   AND uc.codigo = ".intval($args["cd_usuario"]).")
					  ";		
		}
		elseif(trim($args["ds_usuario"]) != "")
		{
			$qr_sql = "
						SELECT COUNT(*) AS qt_pendencia,
						       SUM(CASE WHEN CAST(p.dt_limite AS DATE) < CURRENT_DATE THEN 1 ELSE 0 END) AS qt_atrasada
						  FROM gestao.pendencia(funcoes.get_usuario(LOWER('".trim($args["ds_usuario"])."'))) p
						 WHERE 1 = (CASE WHEN CAST(p.dt_limite AS DATE) < CURRENT_DATE THEN 1
										 WHEN CAST((p.dt_limite - '2 week'::INTERVAL) AS DATE) < CURRENT_DATE THEN 1
										 ELSE 0 
								   END)
						   AND (EXTRACT(HOUR FROM CURRENT_TIMESTAMP) BETWEEN 7 AND 20) = TRUE
						   AND 0 < (SELECT COUNT(*)
									  FROM projetos.usuarios_controledi uc
									 WHERE uc.tipo <> 'D'     
									   AND uc.codigo = funcoes.get_usuario(LOWER('".trim($args["ds_usuario"])."')))
					  ";	
		}
		
		
		$qr_sql = "SELECT 0 AS qt_pendencia, 0 AS qt_atrasada";		
        $result = $this->db->query($qr_sql);
    }	
	
    function checar(&$result, $args=array())
    {
		/*
		if(intval($args["cd_usuario"]) > 0)
		{
			$qr_sql = "
						SELECT COUNT(*) AS qt_pendencia,
						       SUM(CASE WHEN CAST(p.dt_limite AS DATE) < CURRENT_DATE THEN 1 ELSE 0 END) AS qt_atrasada
						  FROM gestao.pendencia(".intval($args["cd_usuario"]).") p
						 WHERE 1 = (CASE WHEN CAST(p.dt_limite AS DATE) < CURRENT_DATE THEN 1
										 WHEN CAST((p.dt_limite - '2 week'::INTERVAL) AS DATE) < CURRENT_DATE THEN 1
										 ELSE 0 
								   END)
						   AND (EXTRACT(HOUR FROM CURRENT_TIMESTAMP) BETWEEN 7 AND 20) = TRUE
						   AND 0 < (SELECT COUNT(*)
                                      FROM projetos.usuarios_controledi uc
                                     WHERE uc.tipo <> 'D'     
									   AND uc.codigo = ".intval($args["cd_usuario"]).")
					  ";		
		}
		elseif(trim($args["ds_usuario"]) != "")
		{
			$qr_sql = "
						SELECT COUNT(*) AS qt_pendencia,
						       SUM(CASE WHEN CAST(p.dt_limite AS DATE) < CURRENT_DATE THEN 1 ELSE 0 END) AS qt_atrasada
						  FROM gestao.pendencia(funcoes.get_usuario(LOWER('".trim($args["ds_usuario"])."'))) p
						 WHERE 1 = (CASE WHEN CAST(p.dt_limite AS DATE) < CURRENT_DATE THEN 1
										 WHEN CAST((p.dt_limite - '2 week'::INTERVAL) AS DATE) < CURRENT_DATE THEN 1
										 ELSE 0 
								   END)
						   AND (EXTRACT(HOUR FROM CURRENT_TIMESTAMP) BETWEEN 7 AND 20) = TRUE
						   AND 0 < (SELECT COUNT(*)
									  FROM projetos.usuarios_controledi uc
									 WHERE uc.tipo <> 'D'     
									   AND uc.codigo = funcoes.get_usuario(LOWER('".trim($args["ds_usuario"])."')))
					  ";	
		}
		*/
		
		$qr_sql = "SELECT (CASE WHEN COALESCE(fl_pendencia,'N') = 'S' THEN 1 ELSE 0 END) AS qt_pendencia FROM gestao.pendencia_checar(".intval($args["cd_usuario"]).")";		
		
		#$qr_sql = "SELECT 0 AS qt_pendencia";		
        $result = $this->db->query($qr_sql);
    }	
	
    function comboPendencia(&$result, $args=array())
    {
        $qr_sql = "
					SELECT pm.cd_pendencia_minha AS value,
						   pm.ds_pendencia_minha AS text
					  FROM gestao.pendencia_minha pm
					 WHERE pm.dt_exclusao IS NULL
                     ORDER BY text
                  ";
        $result = $this->db->query($qr_sql);
    }	
	
    function comboResp1(&$result, $args=array())
    {
        $qr_sql = "
					SELECT DISTINCT p.cd_responsavel AS value,
                           funcoes.get_usuario_nome(p.cd_responsavel) AS text
                      FROM gestao.pendencia(".intval($args["cd_usuario"]).") p
                     ORDER BY text
                  ";
        $result = $this->db->query($qr_sql);
    }

    function comboResp2(&$result, $args=array())
    {
        $qr_sql = "
					SELECT DISTINCT p.cd_substituto AS value,
                           funcoes.get_usuario_nome(p.cd_substituto) AS text
                      FROM gestao.pendencia(".intval($args["cd_usuario"]).") p
                     ORDER BY text
                  ";
        $result = $this->db->query($qr_sql);
    }	

    public function listar_pendencias($args = array())
    {
        $qr_sql = "
            SELECT cd_pendencia_minha,
                   ds_pendencia_minha
              FROM gestao.pendencia_minha 
             WHERE dt_exclusao IS NULL
               ".(trim($args['cd_pendencia_minha']) != '' ? "AND UPPER(funcoes.remove_acento(cd_pendencia_minha)) LIKE UPPER(funcoes.remove_acento('%".trim($args['cd_pendencia_minha'])."%'))" : "").";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO gestao.pendencia_minha
            (
              cd_pendencia_minha,
              ds_pendencia_minha,
              cd_usuario_inclusao,
              cd_usuario_alteracao,
              dt_inclusao,
              dt_alteracao
            )
            VALUES
            (
               ".(trim($args['cd_pendencia_minha']) != '' ? str_escape($args['cd_pendencia_minha']) : "DEFAULT").",
               ".(trim($args['ds_pendencia_minha']) != '' ? str_escape($args['ds_pendencia_minha']) : "DEFAULT").",
               ".intval($args['cd_usuario']).",
               ".intval($args['cd_usuario']).",
               CURRENT_TIMESTAMP,
               CURRENT_TIMESTAMP
          );";
       
      $this->db->query($qr_sql);
  }

  public function carrega($cd_pendencia_minha)
  {
      $qr_sql = "
          SELECT cd_pendencia_minha,
                 ds_pendencia_minha
            FROM gestao.pendencia_minha 
           WHERE dt_exclusao IS NULL
             AND cd_pendencia_minha = '".trim($cd_pendencia_minha)."';";

      return $this->db->query($qr_sql)->row_array();
  }

  public function atualizar($cd_pendencia_minha, $args = array())
  {
    $qr_sql = "
      UPDATE gestao.pendencia_minha
         SET ds_pendencia_minha   = ".(trim($args['ds_pendencia_minha']) != '' ? str_escape($args['ds_pendencia_minha']) : "DEFAULT").",
             cd_usuario_alteracao = ".intval($args['cd_usuario']).",
             dt_alteracao         = CURRENT_TIMESTAMP
       WHERE cd_pendencia_minha = '".trim($cd_pendencia_minha)."';";

    $this->db->query($qr_sql);
  }
  
  
    public function checarUsuarioSalvarLog($args = array())
    {
        $qr_sql = "
					INSERT INTO temporario.pendencia_aviso_log
					     (
							cd_usuario, 
							qt_pendencia,
							ip
						 )
					VALUES 
					     (
							funcoes.get_usuario(LOWER('".$args['ds_usuario']."')),
							".intval($args['qt_pendencia']).",
							".(trim($args['nr_ip']) != '' ? str_escape($args['nr_ip']) : "DEFAULT")."
						 );
				  ";
		$this->db->query($qr_sql);
	}  
}
?>