<?php

class Reuniao_sg_model extends Model
{

    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "
                SELECT rs.cd_reuniao_sg, 
                       rs.cd_reuniao_sg_instituicao,
                       rsi.ds_reuniao_sg_instituicao,
                       rs.participantes, 
                       rs.pauta, 
                       TO_CHAR(rs.dt_reuniao,'DD/MM/YYYY HH24:MI') AS dt_reuniao, 
                       TO_CHAR(rs.dt_encerrado,'DD/MM/YYYY HH24:MI') AS dt_encerrado, 
                       rs.cd_usuario_reuniao, 
                       COALESCE(ue.nome,uc.nome) AS usuario_confirma,
                       TO_CHAR(rs.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                       rs.cd_usuario_inclusao,
                       ui.nome AS usuario_cadastro,
                       rs.arquivo,
                       rs.arquivo_nome,
                       CASE WHEN rs.dt_cancela IS NOT NULL THEN 'N'
                            WHEN rs.dt_reuniao IS NOT NULL THEN 'S'
                            ELSE 'A'
                       END AS fl_confirma,
                       CASE WHEN COALESCE(rs.parecer, '') = '' THEN 'N'
                            ELSE 'S'
                       END AS parecer,
                       TO_CHAR(rs.dt_cancela,'DD/MM/YYYY HH24:MI:SS') AS dt_cancela,
                       rs.cd_usuario_cancela,
                       rs.dt_encerrado
              FROM projetos.reuniao_sg rs
              JOIN projetos.reuniao_sg_instituicao rsi
                ON rsi.cd_reuniao_sg_instituicao = rs.cd_reuniao_sg_instituicao					  
              JOIN projetos.usuarios_controledi ui
                ON ui.codigo = rs.cd_usuario_inclusao
              LEFT JOIN projetos.usuarios_controledi uc
                ON uc.codigo = rs.cd_usuario_reuniao
              LEFT JOIN projetos.usuarios_controledi ue
                ON ue.codigo = rs.cd_usuario_cancela						
             WHERE rs.dt_exclusao IS NULL
             " . ((trim($args['cd_usuario']) != "") ? " AND rs.cd_usuario_inclusao =".intval($args['cd_usuario']) : "") . "
             " . ((trim($args['fl_parecer']) == "S") ? " AND COALESCE(rs.parecer, '') = '' " : "") . "
             " . ((trim($args['fl_parecer']) == "N") ? " AND COALESCE(rs.parecer, '') <> '' " : "") . "
             " . ((trim($args['fl_encerrado']) == "S") ? " AND rs.dt_encerrado IS NOT NULL " : "") . "
             " . ((trim($args['fl_encerrado']) == "N") ? " AND rs.dt_encerrado IS NULL " : "") . "
             " . (((trim($args['dt_reuniao_ini']) != "") and (trim($args['dt_reuniao_fim']) != "")) ? " AND DATE_TRUNC('day', rs.dt_reuniao) BETWEEN TO_DATE('" . $args['dt_reuniao_ini'] . "', 'DD/MM/YYYY') AND TO_DATE('" . $args['dt_reuniao_fim'] . "', 'DD/MM/YYYY')" : "") . "
             " . (((trim($args['dt_inclusao_ini']) != "") and (trim($args['dt_inclusao_fim']) != "")) ? " AND DATE_TRUNC('day', rs.dt_inclusao) BETWEEN TO_DATE('" . $args['dt_inclusao_ini'] . "', 'DD/MM/YYYY') AND TO_DATE('" . $args['dt_inclusao_fim'] . "', 'DD/MM/YYYY')" : "") . "
		          ";

        #echo "<pre style='text-align:left;'>$qr_sql</pre>";exit;
        $result = $this->db->query($qr_sql);
    }
    
    function listar_relatorio(&$result, $args=array())
    {
        $qr_sql = "
                SELECT TO_CHAR(rs.dt_reuniao,'DD/MM/YYYY HH24:MI') AS dt_reuniao, 
                       TO_CHAR(rs.dt_reuniao_ini,'DD/MM/YYYY HH24:MI') AS dt_reuniao_ini, 
                       TO_CHAR(rs.dt_reuniao_fim,'DD/MM/YYYY HH24:MI') AS dt_reuniao_fim, 
                       rsi.ds_reuniao_sg_instituicao,
                       rs.parecer,
                       rs.parecer_qualificacao,
                       rs.cd_reuniao_sg,
                       rs.parecer_qualificacao
                  FROM projetos.reuniao_sg rs
                  JOIN projetos.reuniao_sg_instituicao rsi
                    ON rsi.cd_reuniao_sg_instituicao = rs.cd_reuniao_sg_instituicao					  					
                 WHERE rs.dt_exclusao IS NULL
                 " . ((trim($args['cd_reuniao_sg_instituicao']) != "") ? " AND rs.cd_reuniao_sg_instituicao =".intval($args['cd_reuniao_sg_instituicao']) : "") . "
                 " . (((trim($args['dt_reuniao_ini']) != "") and (trim($args['dt_reuniao_fim']) != "")) ? " AND DATE_TRUNC('day', rs.dt_reuniao) BETWEEN TO_DATE('" . $args['dt_reuniao_ini'] . "', 'DD/MM/YYYY') AND TO_DATE('" . $args['dt_reuniao_fim'] . "', 'DD/MM/YYYY')" : "")."
				" . (((trim($args['dt_ini_ini']) != "") and (trim($args['dt_ini_fim']) != "")) ? " AND DATE_TRUNC('day', rs.dt_reuniao_ini) BETWEEN TO_DATE('" . $args['dt_ini_ini'] . "', 'DD/MM/YYYY') AND TO_DATE('" . $args['dt_ini_fim'] . "', 'DD/MM/YYYY')" : "")."
                 ORDER BY rs.dt_reuniao ASC";
                 
        $result = $this->db->query($qr_sql);
    }

    function cadastro(&$result, $args=array())
    {
        $qr_sql = "
                SELECT rs.cd_reuniao_sg, 
					   rs.cd_usuario_inclusao,
                       rs.cd_reuniao_sg_instituicao, 
                       rs.participantes, 
                       rs.pauta, 
                       rs.contato,
                       TO_CHAR(rs.dt_sugerida,'DD/MM/YYYY') AS dt_sugerida, 
                       TO_CHAR(rs.hr_sugerida,'HH24:MI') AS hr_sugerida, 
                       TO_CHAR(rs.dt_reuniao,'DD/MM/YYYY') AS dt_reuniao, 
                       TO_CHAR(rs.dt_reuniao,'HH24:MI') AS hr_reuniao, 
                       rs.cd_usuario_reuniao, 
                       uc.nome AS usuario_reuniao,
                       TO_CHAR(rs.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                       rs.cd_usuario_inclusao,
                       ui.nome AS usuario_cadastro,
                       TO_CHAR(rs.dt_exclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao,
                       TO_CHAR(rs.dt_cancela,'DD/MM/YYYY HH24:MI:SS') AS dt_cancela,
                       dt_encerrado,
                       rs.arquivo,
                       rs.arquivo_nome,
                       rsv.cd_usuario_validacao,
					   uc2.nome AS usuario_validacao,
					   rsi.ds_reuniao_sg_instituicao
                  FROM projetos.reuniao_sg rs
                  JOIN projetos.usuarios_controledi ui
                    ON ui.codigo = rs.cd_usuario_inclusao
                  LEFT JOIN projetos.usuarios_controledi uc
                    ON uc.codigo = rs.cd_usuario_reuniao
                  LEFT JOIN projetos.reuniao_sg_validacao rsv
                    ON rsv.cd_reuniao_sg = rs.cd_reuniao_sg
                   AND rsv.fl_responsavel = 'S'
				  LEFT JOIN projetos.usuarios_controledi uc2
                    ON uc2.codigo = rsv.cd_usuario_validacao
				  LEFT JOIN projetos.reuniao_sg_instituicao rsi
				    ON rsi.cd_reuniao_sg_instituicao = rs.cd_reuniao_sg_instituicao
                 WHERE rs.cd_reuniao_sg = " . intval($args['cd_reuniao_sg']) . "
		          ";

        #echo "<pre style='text-align:left;'>$qr_sql</pre>";exit;
        $result = $this->db->query($qr_sql);
    }

    function salvar(&$result, $args=array())
    {
        $retorno = 0;

        if (intval($args['cd_reuniao_sg']) > 0)
        {
            if (intval($args['fl_confirma']) == 1)
            {
                ##CONFIRMAÇÃO
                $qr_sql = " 
                UPDATE projetos.reuniao_sg
                   SET dt_reuniao             = " . ((trim($args['dt_reuniao']) == "") ? "DEFAULT" : "TO_TIMESTAMP('" . $args['dt_reuniao'] . " " . $args['hr_reuniao'] . "','DD/MM/YYYY HH24:MI')") . ",
                       cd_usuario_reuniao     = " . ((trim($args['dt_reuniao']) == "") ? "DEFAULT" : $args['cd_usuario_atualizacao']) . ",
                       dt_cancela             = NULL,
                       cd_usuario_cancela     = NULL,						   
                       dt_atualizacao         = CURRENT_TIMESTAMP,
                       cd_usuario_atualizacao = " . (intval($args['cd_usuario_atualizacao']) == 0 ? "DEFAULT" : $args['cd_usuario_atualizacao']) . "
                 WHERE cd_reuniao_sg = " . intval($args['cd_reuniao_sg']) . ";";
                
				if(count($args["arr_participante"]) > 0)
				{
					$qr_sql .= "
						UPDATE projetos.reuniao_sg_participante
						   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
							   dt_exclusao         = CURRENT_TIMESTAMP
						 WHERE cd_reuniao_sg              = ".intval($args['cd_reuniao_sg'])."
						   AND cd_reuniao_sg_participante NOT IN('".implode ("','", $args["arr_participante"] )."');";
				
					foreach($args['arr_participante'] as $item)
					{
						$qr_sql .= "
							INSERT INTO projetos.reuniao_sg_participante
								 (
									cd_reuniao_sg,
									cd_usuario_participante,
									cd_usuario_inclusao
								 )
							SELECT ".intval($args['cd_reuniao_sg']).",
								   ".intval($item).",
								   ".intval($args['cd_usuario'])."
							 WHERE 0 =
									 (
									   SELECT COUNT(*)
										 FROM projetos.reuniao_sg_participante
										WHERE cd_reuniao_sg           = ".intval($args['cd_reuniao_sg'])."
										  AND cd_usuario_participante = ".intval($item)."
										  AND dt_exclusao IS NULL
									 );";
				
					}
				}
				
                $this->db->query($qr_sql);
            }
            else
            {
                ##UPDATE
                $qr_sql = " 
                    UPDATE projetos.reuniao_sg
                       SET cd_reuniao_sg_instituicao = " . (intval($args['cd_reuniao_sg_instituicao']) == 0 ? "DEFAULT" : intval($args['cd_reuniao_sg_instituicao'])) . ",
                           participantes      = " . (trim($args['participantes']) == "" ? "DEFAULT" : "'" . $args['participantes'] . "'") . ",
                           pauta              = " . (trim($args['pauta']) == "" ? "DEFAULT" : "'" . $args['pauta'] . "'") . ",
                           contato            = " . (trim($args['contato']) == "" ? "DEFAULT" : "'" . $args['contato'] . "'") . ",
                           dt_sugerida        = " . (trim($args['dt_sugerida']) == "" ? "DEFAULT" : "TO_DATE('" . $args['dt_sugerida'] . "','DD/MM/YYYY')") . ",
                           hr_sugerida        = " . (trim($args['hr_sugerida']) == "" ? "DEFAULT" : "CAST('" . $args['hr_sugerida'] . "' AS TIME)") . ",
                           dt_atualizacao         = CURRENT_TIMESTAMP,
                           cd_usuario_atualizacao = " . (intval($args['cd_usuario']) == 0 ? "DEFAULT" : $args['cd_usuario']) . ",
                           arquivo_nome  = ".(trim($args['arquivo_nome']) == "" ? "DEFAULT" : "'".$args['arquivo_nome']."'").",
                           arquivo       = ".(trim($args['arquivo']) == "" ? "DEFAULT" : "'".$args['arquivo']."'").",
						   cd_usuario_inclusao = " . (intval($args['cd_usuario']) == 0 ? "DEFAULT" : $args['cd_usuario']) . "
                     WHERE cd_reuniao_sg = " . intval($args['cd_reuniao_sg']) . ";
						  ";
                
                $qr_sql .= "UPDATE projetos.reuniao_sg_validacao
                              SET cd_usuario_validacao = " . (intval($args['cd_usuario_validacao']) == 0 ? "DEFAULT" : intval($args['cd_usuario_validacao'])) . "
                            WHERE cd_reuniao_sg = " . intval($args['cd_reuniao_sg'])."
                              AND fl_responsavel = 'S';";
							  
				if(count($args["arr_participante"]) > 0)
				{				
					$qr_sql .= "
						UPDATE projetos.reuniao_sg_participante
						   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
							   dt_exclusao         = CURRENT_TIMESTAMP
						 WHERE cd_reuniao_sg              = ".intval($args['cd_reuniao_sg'])."
						   AND cd_reuniao_sg_participante NOT IN('".implode ("','", $args["arr_participante"] )."');";
				
					foreach($args['arr_participante'] as $item)
					{
						$qr_sql .= "
							INSERT INTO projetos.reuniao_sg_participante
								 (
									cd_reuniao_sg,
									cd_usuario_participante,
									cd_usuario_inclusao
								 )
							SELECT ".intval($args['cd_reuniao_sg']).",
								   ".intval($item).",
								   ".intval($args['cd_usuario'])."
							 WHERE 0 =
									 (
									   SELECT COUNT(*)
										 FROM projetos.reuniao_sg_participante
										WHERE cd_reuniao_sg           = ".intval($args['cd_reuniao_sg'])."
										  AND cd_usuario_participante = ".intval($item)."
										  AND dt_exclusao IS NULL
									 );";
									 
					}
				}
                $this->db->query($qr_sql);
            }

            #echo "<pre>$qr_sql</pre>"; exit;
            
            $retorno = intval($args['cd_reuniao_sg']);
        }
        else
        {
            ##INSERT
            $new_id = intval($this->db->get_new_id("projetos.reuniao_sg", "cd_reuniao_sg"));
            $qr_sql = " 
                    INSERT INTO projetos.reuniao_sg
                         (
                           cd_reuniao_sg, 
                           cd_reuniao_sg_instituicao, 
                           participantes, 
                           pauta, 
                           contato,
                           dt_sugerida, 
                           hr_sugerida, 
                           cd_usuario_inclusao,
                           cd_usuario_atualizacao,
                           arquivo_nome,
                           arquivo
                         )
                    VALUES 
                         (
                           " . $new_id . ",
                           " . (intval($args['cd_reuniao_sg_instituicao']) == 0 ? "DEFAULT" : intval($args['cd_reuniao_sg_instituicao'])) . ",
                           " . (trim($args['participantes']) == "" ? "DEFAULT" : "'" . $args['participantes'] . "'") . ",
                           " . (trim($args['pauta']) == "" ? "DEFAULT" : "'" . $args['pauta'] . "'") . ",
                           " . (trim($args['contato']) == "" ? "DEFAULT" : "'" . $args['contato'] . "'") . ",
                           " . (trim($args['dt_sugerida']) == "" ? "DEFAULT" : "TO_DATE('" . $args['dt_sugerida'] . "','DD/MM/YYYY')") . ",
                           " . (trim($args['hr_sugerida']) == "" ? "DEFAULT" : "CAST('" . $args['hr_sugerida'] . "' AS TIME)") . ",
                           " . (intval($args['cd_usuario']) == 0 ? "DEFAULT" : $args['cd_usuario']) . ",
                           " . (intval($args['cd_usuario']) == 0 ? "DEFAULT" : $args['cd_usuario']) . ",
                           ".(trim($args['arquivo_nome']) == "" ? "DEFAULT" : "'".$args['arquivo_nome']."'").",
                           ".(trim($args['arquivo']) == "" ? "DEFAULT" : "'".$args['arquivo']."'")."
                         );			
              ";
            
            
            $qr_sql .= "INSERT INTO projetos.reuniao_sg_validacao
                             (
                               cd_reuniao_sg, 
                               cd_usuario_validacao, 
                               cd_usuario_inclusao,
                               fl_responsavel
                             )
                        VALUES 
                             (
                               " . $new_id . ",
                               " . (intval($args['cd_usuario_validacao']) == 0 ? "DEFAULT" : intval($args['cd_usuario_validacao'])) . ",
                               " . (intval($args['cd_usuario']) == 0 ? "DEFAULT" : intval($args['cd_usuario'])) . ",
                               'S'
                             );";
				
			foreach($args['arr_participante'] as $item)
			{
				$qr_sql .= "
					INSERT INTO projetos.reuniao_sg_participante
						 (
							cd_reuniao_sg,
							cd_usuario_participante,
							cd_usuario_inclusao
						 )
					VALUES
						 (
						   ".intval($new_id).",
						   ".intval($item).",
						   ".intval($args['cd_usuario'])."
                         );";
			}
            
            $this->db->query($qr_sql);
            
            $retorno = $new_id;
        }

        #echo "<pre>$qr_sql</pre>";
        #exit;

        return $retorno;
    }

    function excluir(&$result, $args=array())
    {
        if (intval($args['cd_reuniao_sg']) > 0)
        {
            $qr_sql = " 
                    UPDATE projetos.reuniao_sg
                       SET dt_exclusao            = CURRENT_TIMESTAMP,
                           cd_usuario_exclusao    = " . (intval($args['cd_usuario']) == 0 ? "DEFAULT" : $args['cd_usuario']) . ",
                           dt_atualizacao         = CURRENT_TIMESTAMP,
                           cd_usuario_atualizacao = " . (intval($args['cd_usuario']) == 0 ? "DEFAULT" : $args['cd_usuario']) . "							   
                     WHERE cd_reuniao_sg = " . intval($args['cd_reuniao_sg']) . "
					  ";
            $this->db->query($qr_sql);
        }
    }

    function naoConfirmar(&$result, $args=array())
    {
        if (intval($args['cd_reuniao_sg']) > 0)
        {
            $qr_sql = " 
                    UPDATE projetos.reuniao_sg
                       SET dt_cancela             = CURRENT_TIMESTAMP,
                           cd_usuario_cancela     = " . (intval($args['cd_usuario']) == 0 ? "DEFAULT" : $args['cd_usuario']) . ",
                           dt_reuniao             = NULL,
                           cd_usuario_reuniao     = NULL,
                           dt_atualizacao         = CURRENT_TIMESTAMP,
                           cd_usuario_atualizacao = " . (intval($args['cd_usuario']) == 0 ? "DEFAULT" : $args['cd_usuario']) . "
                     WHERE cd_reuniao_sg = " . intval($args['cd_reuniao_sg']) . "
					  ";
            $this->db->query($qr_sql);
        }
    }

    function get_assuntos(&$result, $args=array())
    {
        $qr_sql = "SELECT cd_reuniao_sg_assunto_parecer,
                          ds_reuniao_sg_assunto,
                          complemento,
						  cd_reuniao_sg_assunto_parecer
                     FROM projetos.reuniao_sg_assunto_parecer sp
                     JOIN projetos.reuniao_sg_assunto s
                       ON s.cd_reuniao_sg_assunto = sp.cd_reuniao_sg_assunto
                    WHERE s.dt_exclusao IS NULL
                      AND sp.dt_exclusao IS NULL
                      AND sp.cd_reuniao_sg = " . intval($args['cd_reuniao_sg']);

        $result = $this->db->query($qr_sql);
    }
	
	function carrega_assunto(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_reuniao_sg_assunto_parecer,
				   cd_reuniao_sg_assunto,
				   complemento
			 FROM projetos.reuniao_sg_assunto_parecer
			WHERE cd_reuniao_sg_assunto_parecer = ".intval($args['cd_reuniao_sg_assunto_parecer']).";";

        $result = $this->db->query($qr_sql);
	}

    function get_usuarios(&$result, $args=array())
    {
        $qr_sql = "SELECT nome,
                          fl_validacao,
                          cd_reuniao_sg_validacao,
                          TO_CHAR(dt_validacao,'DD/MM/YYYY') AS dt_validacao,
                          TO_CHAR(dt_envio,'DD/MM/YYYY') AS dt_envio,
                          fl_responsavel,
						  uc.codigo
                     FROM projetos.reuniao_sg_validacao rsv
                     JOIN projetos.usuarios_controledi uc
                       ON uc.codigo = rsv.cd_usuario_validacao
                    WHERE rsv.dt_exclusao IS NULL
                      AND cd_reuniao_sg = " . intval($args['cd_reuniao_sg']);

        $result = $this->db->query($qr_sql);
    }

    function get_parecer(&$result, $args=array())
    {
        $qr_sql = "
            SELECT rs.relato,
                   rs.parecer,
                   TO_CHAR(rs.dt_encerrado,'DD/MM/YYYY HH24:MI') AS dt_encerrado, 
                   TO_CHAR(COALESCE(rs.dt_reuniao_ini, rs.dt_reuniao),'DD/MM/YYYY') AS dt_reuniao_ini, 
                   TO_CHAR(COALESCE(rs.dt_reuniao_ini, rs.dt_reuniao),'HH24:MI') AS hr_reuniao_ini, 
				   TO_CHAR(COALESCE(rs.dt_reuniao_fim, rs.dt_reuniao),'DD/MM/YYYY') AS dt_reuniao_fim, 
				   TO_CHAR(COALESCE(rs.dt_reuniao_fim, rs.dt_reuniao + '1 hour'::interval),'HH24:MI') AS hr_reuniao_fim, 
                   uc.nome,
                   rs.parecer_qualificacao
              FROM projetos.reuniao_sg rs
              LEFT JOIN projetos.usuarios_controledi uc
                ON uc.codigo = rs.cd_usuario_encerrado  
             WHERE cd_reuniao_sg = " . intval($args['cd_reuniao_sg']) . 
           " 
		          ";
        
        $result = $this->db->query($qr_sql);
    }

    function salvar_parecer(&$result, $args=array())
    {
        $qr_sql = " 
                UPDATE projetos.reuniao_sg
                   SET relato                 = " . ((trim($args['relato']) == "") ? "DEFAULT" : "'" . $args['relato'] . "'") . ",
                       parecer                = " . ((trim($args['parecer']) == "") ? "DEFAULT" : "'" . $args['parecer'] . "'") . ",
                       parecer_qualificacao   = " . ((trim($args['fl_qualificacao']) == "") ? "DEFAULT" : "'" . $args['fl_qualificacao'] . "'") . ",
                       cd_usuario_atualizacao = " . intval($args['cd_usuario']) . ",
                       dt_atualizacao         = CURRENT_TIMESTAMP,
                       dt_reuniao_ini         = " . ((trim($args['dt_reuniao_ini']) == "" AND trim($args['hr_reuniao_ini']) == "") ? "DEFAULT" : "TO_TIMESTAMP('" . $args['dt_reuniao_ini'] .' '. $args['hr_reuniao_ini']. "','DD/MM/YYYY HH24:MI')") . ",
                       dt_reuniao_fim         = " . ((trim($args['dt_reuniao_fim']) == "" AND trim($args['dt_reuniao_fim']) == "") ? "DEFAULT" : "TO_TIMESTAMP('" . $args['dt_reuniao_fim'] .' '. $args['hr_reuniao_fim']. "','DD/MM/YYYY HH24:MI')") . "
                 WHERE cd_reuniao_sg = " . intval($args['cd_reuniao_sg']) . "
              ";
        
        $this->db->query($qr_sql);
    }

    function encerrar(&$result, $args=array())
    {
        $qr_sql = " 
                UPDATE projetos.reuniao_sg
                   SET cd_usuario_encerrado = " . intval($args['cd_usuario']) . ",
                       dt_encerrado         = CURRENT_TIMESTAMP,
                       parecer_qualificacao = '".trim($args['fl_qualificacao'])."',
                       relato  = '".trim($args['relato'])."',
                       parecer = '".trim($args['parecer'])."'
                 WHERE cd_reuniao_sg = " . intval($args['cd_reuniao_sg']) . "
              ";

        $result = $this->db->query($qr_sql);
    }
	
	function auto_encerrar(&$result, $args=array())
    {
        $qr_sql = " 
                UPDATE projetos.reuniao_sg
                   SET cd_usuario_encerrado = ".intval($args['cd_usuario']).",
                       dt_encerrado         = CURRENT_TIMESTAMP
                 WHERE cd_reuniao_sg = " . intval($args['cd_reuniao_sg']) . "
              ";

        $result = $this->db->query($qr_sql);
    }

    function get_sg_assunto(&$result, $args=array())
    {
        $qr_sql = " 
                SELECT cd_reuniao_sg_assunto AS value,
                       ds_reuniao_sg_assunto AS text
                  FROM projetos.reuniao_sg_assunto
                 WHERE dt_exclusao IS NULL
              ";

        $result = $this->db->query($qr_sql);
    }

    function salvar_assunto(&$result, $args=array())
    {
		if(intval($args['cd_reuniao_sg_assunto_parecer']) == 0)
		{
			$qr_sql = "
				INSERT INTO projetos.reuniao_sg_assunto_parecer
					 (
					   cd_reuniao_sg, 
					   cd_reuniao_sg_assunto, 
					   complemento, 
					   cd_usuario_inclusao
					 )
				VALUES 
					 (
					   ".(intval($args['cd_reuniao_sg']) == 0 ? "DEFAULT" : intval($args['cd_reuniao_sg'])).",
					   ".(intval($args['cd_reuniao_sg_assunto']) == 0 ? "DEFAULT" : intval($args['cd_reuniao_sg_assunto'])).", 
					   ".(trim($args['complemento']) == "" ? "DEFAULT" : str_escape($args['complemento'])).",
					   ".(intval($args['cd_usuario']) == 0 ? "DEFAULT" : intval($args['cd_usuario'])). "
					 )";
		}
		else
		{
			$qr_sql = "
				UPDATE projetos.reuniao_sg_assunto_parecer
				   SET cd_reuniao_sg_assunto = ".(intval($args['cd_reuniao_sg_assunto']) == 0 ? "DEFAULT" : intval($args['cd_reuniao_sg_assunto'])).", 
					   complemento           = ".(trim($args['complemento']) == "" ? "DEFAULT" : str_escape($args['complemento']))."
				 WHERE cd_reuniao_sg_assunto_parecer = ".intval($args['cd_reuniao_sg_assunto_parecer']).";";
		}
        $this->db->query($qr_sql);
    }

    function salvar_usuario(&$result, $args=array())
    {
		$qr_sql = "
			UPDATE projetos.reuniao_sg_validacao
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_reuniao_sg         = ".intval($args['cd_reuniao_sg'])."
			   AND cd_usuario_validacao NOT IN('".implode ("','", $args["arr_participante"] )."');";
            
		foreach($args['arr_participante'] as $item)
		{
			$qr_sql .= "
				INSERT INTO projetos.reuniao_sg_validacao
					 (
					   cd_reuniao_sg, 
					   cd_usuario_validacao, 
					   cd_usuario_inclusao
					 )
				SELECT ".intval($args['cd_reuniao_sg']).",
                       ".intval($item).",
                       ".intval($args['cd_usuario'])."
                 WHERE 0 =
						 (
						   SELECT COUNT(*)
							 FROM projetos.reuniao_sg_validacao
							WHERE cd_reuniao_sg = ".intval($args['cd_reuniao_sg'])."
							  AND cd_usuario_validacao    = ".intval($item)."
							  AND dt_exclusao IS NULL
						 );";
		}
				
        $this->db->query($qr_sql);
    }

    function excluir_assunto(&$result, $args=array())
    {
        $qr_sql = "UPDATE projetos.reuniao_sg_assunto_parecer
                      SET cd_usuario_exclusao = " . intval($args['cd_usuario']) . ",
                          dt_exclusao         = CURRENT_TIMESTAMP
                    WHERE cd_reuniao_sg_assunto_parecer = " . intval($args['cd_reuniao_sg_assunto_parecer']);

        $this->db->query($qr_sql);
    }

    function pdf(&$result, $args=array())
    {
        $qr_sql = "SELECT ds_reuniao_sg_instituicao, 
                          rs.participantes, 
                          TO_CHAR(rs.dt_reuniao,'DD/MM/YYYY HH24:MI') AS dt_reuniao,              
                          TO_CHAR(rs.dt_reuniao_ini,'DD/MM/YYYY HH24:MI') AS dt_reuniao_ini, 
                          TO_CHAR(rs.dt_reuniao_fim,'DD/MM/YYYY HH24:MI') AS dt_reuniao_fim, 
                          rs.parecer,
                          rs.relato,
                          CASE WHEN rs.parecer_qualificacao = 'P' THEN 'Positivo'
                               WHEN rs.parecer_qualificacao = 'N' THEN 'Negativo'
                               ELSE 'Neutro'
                               END AS parecer_qualificacao
                     FROM projetos.reuniao_sg rs
                     JOIN projetos.reuniao_sg_instituicao rsi
                       ON rsi.cd_reuniao_sg_instituicao = rs.cd_reuniao_sg_instituicao
                    WHERE rs.cd_reuniao_sg = " . intval($args['cd_reuniao_sg']);

        $result = $this->db->query($qr_sql);
    }

    function enviar(&$result, $args=array())
    {
        $qr_sql = "SELECT rotinas.email_reunicao_sg_confirma(" . intval($args['cd_reuniao_sg']) . ")";

        $result = $this->db->query($qr_sql);
    }

    function verifica_permissao_confirma(&$result, $args=array())
    {
        $qr_sql = "SELECT cd_reuniao_sg,
                          fl_validacao
                     FROM projetos.reuniao_sg_validacao
                    WHERE cd_reuniao_sg_validacao = " . intval($args['cd_reuniao_sg_validacao']) . "
                      AND cd_usuario_validacao    = " . intval($args['cd_usuario']);

        $result = $this->db->query($qr_sql);
    }

    function verifica_permissao_consulta(&$result, $args=array())
    {
        $qr_sql = "SELECT CASE WHEN COUNT(*) > 0 THEN 'S' ELSE 'N' END AS fl_consulta
                     FROM projetos.reuniao_sg_validacao
                    WHERE cd_reuniao_sg        = " . intval($args['cd_reuniao_sg']) . "
                      AND cd_usuario_validacao = " . intval($args['cd_usuario']);

        $result = $this->db->query($qr_sql);
    }

    function verifica_permissao_lista(&$result, $args=array())
    {
        $qr_sql = "SELECT CASE WHEN COUNT(*) > 0 THEN 'S' ELSE 'N' END AS fl_lista
                     FROM projetos.reuniao_sg_validacao
                    WHERE cd_usuario_validacao = " . intval($args['cd_usuario']);
 
        $result = $this->db->query($qr_sql);
    }
	
	function verifica_reuniao_controle(&$result, $args=array())
    {
        $qr_sql = "SELECT CASE WHEN COUNT(*) > 0 THEN 'S' ELSE 'N' END AS fl_lista
                     FROM projetos.reuniao_sg_permissao
                    WHERE cd_usuario = " . intval($args['cd_usuario']);
 
        $result = $this->db->query($qr_sql);
	}
	
    
    function dt_encerrado(&$result, $args=array())
    {
        $qr_sql = "SELECT CASE WHEN dt_encerrado IS NULL THEN 'S' 
                               ELSE 'N' 
                          END AS fl_encerrado
                     FROM projetos.reuniao_sg
                    WHERE cd_reuniao_sg = " . intval($args['cd_reuniao_sg']);
 
        $result = $this->db->query($qr_sql);
    }

    function salva_confirmacao(&$result, $args=array())
    {
        $qr_sql = "UPDATE projetos.reuniao_sg_validacao
                      SET fl_validacao         = '" . trim($args['fl_validacao']) . "',
                          dt_validacao         = CURRENT_TIMESTAMP
                    WHERE cd_reuniao_sg_validacao = " . intval($args['cd_reuniao_sg_validacao']);

        $this->db->query($qr_sql);
    }
	
	function nao_validado(&$result, $args=array())
    {
        $qr_sql = "
			SELECT rsv1.cd_reuniao_sg,
				   (COUNT(rsv1.*) - (SELECT COUNT(rsv2.*)
									   FROM projetos.reuniao_sg_validacao rsv2
									  WHERE rsv2.dt_exclusao IS NULL
										AND rsv2.dt_validacao IS NOT NULL
										AND rsv2.cd_reuniao_sg = rsv1.cd_reuniao_sg)) AS tl_nao_validado
			  FROM projetos.reuniao_sg_validacao rsv1
			 WHERE rsv1.dt_exclusao IS NULL
			   AND rsv1.cd_reuniao_sg = (SELECT rsv3.cd_reuniao_sg 
										   FROM projetos.reuniao_sg_validacao rsv3
										  WHERE rsv3.cd_reuniao_sg_validacao = ".intval($args['cd_reuniao_sg_validacao']).")
			 GROUP BY cd_reuniao_sg;";

        $result = $this->db->query($qr_sql);
    }
    
    function get_usuarios_solicitante(&$result, $args=array())
    {
        $qr_sql = "SELECT uc.nome   AS text,
                          uc.codigo AS value
                     FROM projetos.reuniao_sg rs
                     JOIN projetos.usuarios_controledi uc
                       ON uc.codigo = rs.cd_usuario_inclusao
                    WHERE rs.dt_exclusao IS NULL";

        $result = $this->db->query($qr_sql);
    }
    
    function get_instituicoes(&$result, $args=array())
    {
        $qr_sql = "SELECT cd_reuniao_sg_instituicao AS value,
                          ds_reuniao_sg_instituicao AS text
                     FROM projetos.reuniao_sg_instituicao
                    WHERE dt_exclusao IS NULL";

        $result = $this->db->query($qr_sql);
    }
    
    function get_usuarios_gin(&$result, $args=array())
    {
        $qr_sql = "SELECT codigo AS value,
                          nome AS text
                     FROM projetos.usuarios_controledi
                    WHERE divisao IN ('GIN', 'DE')
                      AND tipo NOT IN ('X')";
        $result = $this->db->query($qr_sql);
    }
    /*
    function salvar_participante(&$result, $args=array())
    {
        $qr_sql = "INSERT INTO projetos.reuniao_sg_participante
                             (
                              cd_reuniao_sg,
                              cd_usuario_participante,
                              cd_usuario_inclusao
                             )
                        VALUES
                             (
                               " . (intval($args['cd_reuniao_sg']) == 0 ? "DEFAULT" : intval($args['cd_reuniao_sg'])) . ",
                               " . (intval($args['cd_usuario_participante']) == 0 ? "DEFAULT" : intval($args['cd_usuario_participante'])) . ",
                               " . (intval($args['cd_usuario']) == 0 ? "DEFAULT" : intval($args['cd_usuario'])) . "
                             )";
        
        $this->db->query($qr_sql);
    }
    
    function excluir_participante(&$result, $args=array())
    {
        $qr_sql = "UPDATE projetos.reuniao_sg_participante
                      SET cd_usuario_exclusao = " . (intval($args['cd_usuario']) == 0 ? "DEFAULT" : intval($args['cd_usuario'])) . ",
                          dt_exclusao         = CURRENT_TIMESTAMP
                    WHERE cd_reuniao_sg              = " . (intval($args['cd_reuniao_sg']) == 0 ? "DEFAULT" : intval($args['cd_reuniao_sg'])) . "
                      AND cd_reuniao_sg_participante = " . (intval($args['cd_usuario_participante']) == 0 ? "DEFAULT" : intval($args['cd_usuario_participante']));

        $this->db->query($qr_sql);
    }
	
	function excluir_usuario(&$result, $args=array())
    {
        $qr_sql = "UPDATE projetos.reuniao_sg_validacao
                      SET cd_usuario_exclusao = " . intval($args['cd_usuario']) . ",
                          dt_exclusao         = CURRENT_TIMESTAMP
                    WHERE cd_reuniao_sg_validacao = " . intval($args['cd_reuniao_sg_validacao']);

        $this->db->query($qr_sql);
    }
	
    */
    function participantes(&$result, $args=array())
    {
        $qr_sql = "SELECT uc.nome,
                          uc.divisao,
                          uc.divisao || ' - ' || d.nome AS gerencia,
                          rsp.cd_reuniao_sg_participante ,
						  uc.codigo
                     FROM projetos.reuniao_sg_participante rsp
                     JOIN projetos.usuarios_controledi uc
                       ON uc.codigo = rsp.cd_usuario_participante
                     JOIN projetos.divisoes d
                       ON d.codigo = uc.divisao
                    WHERE rsp.dt_exclusao IS NULL
                      AND cd_reuniao_sg = ".intval($args['cd_reuniao_sg']);
        
        $result = $this->db->query($qr_sql);
    }
    
    function get_assinatura(&$result, $args=array())
    {
        $qr_sql = "SELECT assinatura 
                     FROM projetos.usuarios_controledi
                    WHERE codigo = ". intval($args['cd_usuario']);

        $result = $this->db->query($qr_sql);
    }
	
	function get_usuarios_de(&$result, $args=array())
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM projetos.usuarios_controledi
             WHERE divisao = 'DE'
               AND tipo = 'D'
             ORDER BY nome";
        
        $result = $this->db->query($qr_sql);
    }
	
	function listar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT a.cd_reuniao_sg_anexo,
				   a.arquivo,
				   a.arquivo_nome,
				   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome
			  FROM projetos.reuniao_sg_anexo a
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = a.cd_usuario_inclusao
			 WHERE a.cd_reuniao_sg = ".intval($args['cd_reuniao_sg'])."
			   AND a.dt_exclusao IS NULL
			 ORDER BY a.dt_inclusao DESC;";
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.reuniao_sg_anexo
			     (
					cd_reuniao_sg,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['cd_reuniao_sg']).",
					".str_escape($args['arquivo']).",
					".str_escape($args['arquivo_nome']).",
					".intval($args['cd_usuario'])."
				 )";
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_anexo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.reuniao_sg_anexo
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_reuniao_sg_anexo = ".intval($args['cd_reuniao_sg_anexo']).";";
		$this->db->query($qr_sql);
	}
	
	function gerente_gin(&$result, $args=array())
	{
		$qr_sql = "
			SELECT assinatura, 
				   nome
			  FROM projetos.usuarios_controledi
			 WHERE divisao = 'GIN'
			   AND tipo = 'G';";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function usuario_participante(&$result, $args=array())
    {
        $qr_sql = "
			SELECT DISTINCT uc.codigo AS value, 
                   uc.divisao || ' - ' || uc.nome AS text,
				   (CASE WHEN r.cd_reuniao_sg_participante IS NOT NULL 
						 THEN 0 
						 ELSE 1 
				   END) AS ordem
			  FROM projetos.usuarios_controledi uc
			  LEFT JOIN projetos.reuniao_sg_participante r
				ON r.cd_usuario_participante = uc.codigo
			   AND r.dt_exclusao IS NULL
			   ".(intval($args['cd_reuniao_sg']) > 0 ? "AND r.cd_reuniao_sg = ".intval($args['cd_reuniao_sg']) : "")."
			 WHERE uc.tipo IN ('U', 'N', 'G', 'D','P')
			 ORDER BY ordem,
				   text;";

        $result = $this->db->query($qr_sql);
    }
	
	function usuario(&$result, $args=array())
    {
        $qr_sql = "
			SELECT DISTINCT uc.codigo AS value, 
                   uc.divisao || ' - ' || uc.nome AS text,
				   (CASE WHEN r.cd_reuniao_sg_validacao IS NOT NULL 
						 THEN 0 
						 ELSE 1 
				   END) AS ordem
			  FROM projetos.usuarios_controledi uc
			  LEFT JOIN projetos.reuniao_sg_validacao r
				ON r.cd_usuario_validacao = uc.codigo
			   AND r.dt_exclusao IS NULL
			   ".(intval($args['cd_reuniao_sg']) > 0 ? "AND r.cd_reuniao_sg = ".intval($args['cd_reuniao_sg']) : "")."
			 WHERE uc.tipo IN ('U', 'N', 'G', 'D','P')
			 ORDER BY ordem,
				   text;";
        
        $result = $this->db->query($qr_sql);
    }
	
	function usuario_participante_parecer(&$result, $args=array())
  {
    $qr_sql = "
      SELECT DISTINCT uc.codigo AS value, 
                   uc.divisao || ' - ' || uc.nome AS text,
           (CASE WHEN r.cd_reuniao_sg_participante_parecer IS NOT NULL 
             THEN 0 
             ELSE 1 
           END) AS ordem
        FROM projetos.usuarios_controledi uc
        LEFT JOIN projetos.reuniao_sg_participante_parecer r
        ON r.cd_usuario_participante = uc.codigo
         AND r.dt_exclusao IS NULL
         ".(intval($args['cd_reuniao_sg']) > 0 ? "AND r.cd_reuniao_sg = ".intval($args['cd_reuniao_sg']) : "")."
       WHERE uc.tipo IN ('U', 'N', 'G', 'D')
       ORDER BY ordem,
           text;";

        $result = $this->db->query($qr_sql);
  }

  function get_usuarios_parecer(&$result, $args=array())
  {
      $qr_sql = "
        SELECT uc.codigo,
               uc.nome,
               uc.divisao || ' - ' || d.nome AS gerencia
          FROM projetos.reuniao_sg_participante_parecer rsv
          JOIN projetos.usuarios_controledi uc
            ON uc.codigo = rsv.cd_usuario_participante
          JOIN projetos.divisoes d
            ON d.codigo = uc.divisao
         WHERE rsv.dt_exclusao IS NULL
           AND cd_reuniao_sg = " . intval($args['cd_reuniao_sg']);

      $result = $this->db->query($qr_sql);
  }

  function salvar_participante_parecer(&$result, $args=array())
  {
    $qr_sql = "
      UPDATE projetos.reuniao_sg_participante_parecer
         SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
           dt_exclusao         = CURRENT_TIMESTAMP
       WHERE cd_reuniao_sg         = ".intval($args['cd_reuniao_sg'])."
         AND cd_usuario_participante NOT IN('".implode ("','", $args["arr_participante_parecer"] )."');";
            
    foreach($args['arr_participante_parecer'] as $item)
    {
      $qr_sql .= "
        INSERT INTO projetos.reuniao_sg_participante_parecer
           (
             cd_reuniao_sg, 
             cd_usuario_participante, 
             cd_usuario_inclusao
           )
        SELECT ".intval($args['cd_reuniao_sg']).",
                       ".intval($item).",
                       ".intval($args['cd_usuario'])."
                 WHERE 0 =
             (
               SELECT COUNT(*)
               FROM projetos.reuniao_sg_participante_parecer
              WHERE cd_reuniao_sg = ".intval($args['cd_reuniao_sg'])."
                AND cd_usuario_participante    = ".intval($item)."
                AND dt_exclusao IS NULL
             );";
    }
        
        $this->db->query($qr_sql);
  }
}

?>