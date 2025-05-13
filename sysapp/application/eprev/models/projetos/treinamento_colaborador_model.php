<?php
class treinamento_colaborador_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
    
    function listar( &$result, $args=array() )
	{
        $qr_sql = "
            SELECT funcoes.nr_treinamento_colaborador(tc.ano, tc.numero) AS numero,
                   tc.nome,
                   tc.promotor,
                   tc.cidade,
                   tc.uf,
                   TO_CHAR(tc.dt_inicio,'DD/MM/YYYY') AS dt_inicio,
                   TO_CHAR(tc.dt_final,'DD/MM/YYYY') AS dt_final,
                   tcp.ds_treinamento_colaborador_tipo,
                   tc.carga_horaria,
                   (CASE WHEN tc.fl_cadastro_rh = 'S' THEN 'Sim'
                         ELSE 'Não'
                    END) AS ds_subsidio_fundacao,
                    (CASE WHEN tc.fl_bem_estar = 'S' THEN 'Sim'
                         ELSE 'Não'
                    END) AS fl_bem_estar,
                   (SELECT COUNT(*)
                      FROM projetos.treinamento_colaborador_item tct
                     WHERE tct.numero            = tc.numero
                       AND tct.ano               = tc.ano
                       AND tct.dt_exclusao IS NULL) AS tl_colaborador,
                   (SELECT COUNT(*)
                      FROM projetos.treinamento_colaborador_resposta tcr
                      JOIN projetos.treinamento_colaborador_formulario tcf
                        ON tcf.cd_treinamento_colaborador_formulario = tcr.cd_treinamento_colaborador_formulario
                     WHERE tcr.cd_treinamento_colaborador = tc.cd_treinamento_colaborador
                       AND tcf.fl_enviar_para             = 'C') AS tl_avaliacao_c,
                   (SELECT COUNT(*)
                      FROM projetos.treinamento_colaborador_resposta tcr
                      JOIN projetos.treinamento_colaborador_formulario tcf
                        ON tcf.cd_treinamento_colaborador_formulario = tcr.cd_treinamento_colaborador_formulario
                     WHERE tcr.cd_treinamento_colaborador = tc.cd_treinamento_colaborador
                       AND tcf.fl_enviar_para             = 'G') AS tl_avaliacao_g,
                   (SELECT COUNT(*)
                      FROM projetos.treinamento_colaborador_resposta tcr
                      JOIN projetos.treinamento_colaborador_formulario tcf
                        ON tcf.cd_treinamento_colaborador_formulario = tcr.cd_treinamento_colaborador_formulario
                     WHERE tcr.cd_treinamento_colaborador = tc.cd_treinamento_colaborador
                       AND tcr.dt_finalizado IS NOT NULL
                       AND tcf.fl_enviar_para             = 'C') AS tl_avaliacao_finalizada_c,
                   (SELECT COUNT(*)
                      FROM projetos.treinamento_colaborador_resposta tcr
                      JOIN projetos.treinamento_colaborador_formulario tcf
                        ON tcf.cd_treinamento_colaborador_formulario = tcr.cd_treinamento_colaborador_formulario
                     WHERE tcr.cd_treinamento_colaborador = tc.cd_treinamento_colaborador
                       AND tcr.dt_finalizado IS NOT NULL
                       AND tcf.fl_enviar_para             = 'G') AS tl_avaliacao_finalizada_g
              FROM projetos.treinamento_colaborador tc
              LEFT JOIN projetos.treinamento_colaborador_tipo tcp
                ON tcp.cd_treinamento_colaborador_tipo = tc.cd_treinamento_colaborador_tipo
             WHERE tc.dt_exclusao IS NULL
               ".((trim($args['cd_empresa']) != '' AND trim($args['cd_registro_empregado']) != '' AND trim($args['seq_dependencia']) != '') ? 
                "AND 0 < (SELECT COUNT(*)
                            FROM projetos.treinamento_colaborador_item tct
                           WHERE tct.numero            = tc.numero
                             AND tct.ano               = tc.ano
                             AND tct.cd_empresa            = ".intval($args['cd_empresa'])."
                             AND tct.cd_registro_empregado = ".intval($args['cd_registro_empregado'])."
                             AND tct.seq_dependencia       = ".intval($args['seq_dependencia'])."
                             AND tct.dt_exclusao IS NULL)" : '')."
							 
               ".((trim($args['nome_colaborador']) != '') ? 
                "AND 0 < (SELECT COUNT(*)
                            FROM projetos.treinamento_colaborador_item tct
                           WHERE tct.numero = tc.numero
                             AND tct.ano    = tc.ano
                             AND UPPER(funcoes.remove_acento(tct.nome)) LIKE UPPER(funcoes.remove_acento('%".str_replace(" ","%",trim($args['nome_colaborador']))."%'))
                             AND tct.dt_exclusao IS NULL)" : '')."							 
							 
               ".(((trim($args['dt_inicio_ini']) != "") and  (trim($args['dt_inicio_fim']) != "")) ? " AND DATE_TRUNC('day', tc.dt_inicio) BETWEEN TO_DATE('".$args['dt_inicio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inicio_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_final_ini']) != "") and  (trim($args['dt_final_fim']) != "")) ? " AND DATE_TRUNC('day', tc.dt_final) BETWEEN TO_DATE('".$args['dt_final_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_final_fim']."', 'DD/MM/YYYY')" : "")."
               ".(trim($args['numero']) != '' ? "AND tc.numero = ".intval($args['numero']) : '' )."
               ".(trim($args['ano']) != '' ? "AND tc.ano = ".intval($args['ano']) : '' )."
               ".(trim($args['cd_treinamento_colaborador_tipo']) != '' ? "AND tc.cd_treinamento_colaborador_tipo = ".intval($args['cd_treinamento_colaborador_tipo']) : '' )."

               ".(trim($args['fl_avaliacoes_preenchidos']) == 'S' ? '
               	AND ((SELECT COUNT(*)
                      FROM projetos.treinamento_colaborador_resposta tcr
                      JOIN projetos.treinamento_colaborador_formulario tcf
                        ON tcf.cd_treinamento_colaborador_formulario = tcr.cd_treinamento_colaborador_formulario
                     WHERE tcr.cd_treinamento_colaborador = tc.cd_treinamento_colaborador) > 0 AND 
                     (SELECT COUNT(*)
		                FROM projetos.treinamento_colaborador_resposta tcr
		                JOIN projetos.treinamento_colaborador_formulario tcf
		                  ON tcf.cd_treinamento_colaborador_formulario = tcr.cd_treinamento_colaborador_formulario
		               WHERE tcr.cd_treinamento_colaborador = tc.cd_treinamento_colaborador) = (SELECT COUNT(*)
						 													                      FROM projetos.treinamento_colaborador_resposta tcr
																			                      JOIN projetos.treinamento_colaborador_formulario tcf
																			                        ON tcf.cd_treinamento_colaborador_formulario = tcr.cd_treinamento_colaborador_formulario
																			                     WHERE tcr.cd_treinamento_colaborador = tc.cd_treinamento_colaborador
																			                       AND tcr.dt_finalizado IS NOT NULL))' : "")."
               ".(trim($args['fl_avaliacoes_preenchidos']) == 'N' ? '
               	AND ((SELECT COUNT(*)
                      FROM projetos.treinamento_colaborador_resposta tcr
                      JOIN projetos.treinamento_colaborador_formulario tcf
                        ON tcf.cd_treinamento_colaborador_formulario = tcr.cd_treinamento_colaborador_formulario
                     WHERE tcr.cd_treinamento_colaborador = tc.cd_treinamento_colaborador) > 0 AND 
                     (SELECT COUNT(*)
		                FROM projetos.treinamento_colaborador_resposta tcr
		                JOIN projetos.treinamento_colaborador_formulario tcf
		                  ON tcf.cd_treinamento_colaborador_formulario = tcr.cd_treinamento_colaborador_formulario
		               WHERE tcr.cd_treinamento_colaborador = tc.cd_treinamento_colaborador) != (SELECT COUNT(*)
						 													                      FROM projetos.treinamento_colaborador_resposta tcr
																			                      JOIN projetos.treinamento_colaborador_formulario tcf
																			                        ON tcf.cd_treinamento_colaborador_formulario = tcr.cd_treinamento_colaborador_formulario
																			                     WHERE tcr.cd_treinamento_colaborador = tc.cd_treinamento_colaborador
																			                       AND tcr.dt_finalizado IS NOT NULL))' : "")."
              ".(trim($args['fl_cadastro_rh']) != '' ? "AND tc.fl_cadastro_rh = '".trim($args['fl_cadastro_rh'])."'" : '')."
              ".(trim($args['fl_bem_estar']) != '' ? "AND tc.fl_bem_estar = '".trim($args['fl_bem_estar'])."'" : '')."
              ".(trim($args['fl_certificado']) == 'S' ? "
                AND (tc.fl_certificado = 'S'
                AND 
                (SELECT COUNT(*) 
                   FROM projetos.treinamento_colaborador_item tct 
                  WHERE tct.numero = tc.numero
                    AND tct.ano    = tc.ano
                    AND tct.dt_exclusao IS NULL
                    AND tct.arquivo IS NOT NULL) 
                = 
               (SELECT COUNT(*) 
                   FROM projetos.treinamento_colaborador_item tct 
                  WHERE tct.numero = tc.numero
                    AND tct.ano    = tc.ano
                    AND tct.dt_exclusao IS NULL))" : '')."
              ".(trim($args['fl_certificado']) == 'N' ? "
                AND (tc.fl_certificado = 'S'
                AND 
                (SELECT COUNT(*) 
                   FROM projetos.treinamento_colaborador_item tct 
                  WHERE tct.numero = tc.numero
                    AND tct.ano    = tc.ano
                    AND tct.dt_exclusao IS NULL
                    AND tct.arquivo IS NULL) > 0)" : '')."
			  ORDER BY numero";
		#echo '<PRE style="text-align: left;">'.print_r($args,true)."<BR>".$qr_sql.'</PRE>';
				
        $result = $this->db->query($qr_sql);
    }
    
    function treinamento_colaborador_tipo( &$result, $args=array() )
	{
        $qr_sql = "
            SELECT cd_treinamento_colaborador_tipo AS value,
                   ds_treinamento_colaborador_tipo AS text
              FROM projetos.treinamento_colaborador_tipo
             WHERE dt_exclusao IS NULL";
        
        $result = $this->db->query($qr_sql);
    }
    
    function carrega(&$result, $args=array())
    {
        $qr_sql = "
            SELECT funcoes.nr_treinamento_colaborador(ano, numero) AS numero_a,
                   numero,
                   ano,
                   nome,
                   promotor,
                   endereco,
                   cidade,
                   uf,
                   TO_CHAR(dt_inicio,'DD/MM/YYYY') AS dt_inicio,
                   TO_CHAR(hr_inicio,'HH24:MI:SS') AS hr_inicio,
                   TO_CHAR(dt_final,'DD/MM/YYYY') AS dt_final,
                   TO_CHAR(hr_final,'HH24:MI:SS') AS hr_final,
                   carga_horaria,
                   vl_unitario,
                   cd_treinamento_colaborador_tipo,
				   TO_CHAR(dt_exclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao,
                   fl_certificado,
                   fl_bem_estar
              FROM projetos.treinamento_colaborador
             WHERE ano    = ".intval($args['ano'])."
               AND numero = ".intval($args['numero']);

        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args=array())
    {
        if(intval($args['numero']) == 0 AND intval($args['ano']) == 0)
        {
            $qr_sql = "INSERT INTO projetos.treinamento_colaborador
                                 (
                                 nome,
                                 promotor,
                                 endereco,
                                 cidade,
                                 uf,
                                 dt_inicio,
                                 hr_inicio,
                                 dt_final,
                                 hr_final,
                                 carga_horaria,
                                 vl_unitario,
                                 fl_certificado,
                                 fl_bem_estar,
                                 cd_treinamento_colaborador_tipo,
                                 cd_usuario_inclusao
                                 
                                 )
                          VALUES 
                                 (
                                 ".str_escape($args['nome']).",
                                 ".str_escape($args['promotor']).",
                                 ".(trim($args['endereco']) == '' ? "DEFAULT"  : str_escape($args['endereco'])).",
                                 ".(trim($args['cidade']) == '' ? "DEFAULT"  : str_escape($args['cidade'])).",
                                 ".(trim($args['uf']) == '' ? "DEFAULT"  : str_escape($args['uf'])).",
                                 ".(trim($args['dt_inicio']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')").",
                                 ".(trim($args['hr_inicio']) == "" ? "DEFAULT" : "CAST('".$args['hr_inicio']."' AS TIME)").",
                                 ".(trim($args['dt_final']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_final']."','DD/MM/YYYY')").",
                                 ".(trim($args['hr_final']) == "" ? "DEFAULT" : "CAST('".$args['hr_final']."' AS TIME)").",
                                 ".(trim($args['carga_horaria']) == "" ? "DEFAULT" : floatval($args['carga_horaria'])).",
                                 ".(trim($args['vl_unitario']) == '' ? "DEFAULT"  : floatval($args['vl_unitario'])).",
                                 ".(trim($args['fl_certificado']) == '' ? "DEFAULT"  : "'".trim($args['fl_certificado'])."'").",
                                 ".(trim($args['fl_bem_estar']) == '' ? "DEFAULT"  : "'".trim($args['fl_bem_estar'])."'").",
                                 ".(trim($args['cd_treinamento_colaborador_tipo']) == '' ? "DEFAULT"  : intval($args['cd_treinamento_colaborador_tipo'])).",
                                 ".intval($args['usuario'])."
                                 )";
            $this->db->query($qr_sql);
            
            $qr_sql = "SELECT funcoes.nr_treinamento_colaborador(ano, numero) AS numero
                         FROM projetos.treinamento_colaborador
                        WHERE cd_usuario_inclusao =".intval($args['usuario'])."
                        ORDER BY dt_inclusao DESC
                        LIMIT 1";
            $result = $this->db->query($qr_sql);
            $arr = $result->row_array();
            
            return $arr['numero'];
        }
        else
        {
            $qr_sql = "UPDATE projetos.treinamento_colaborador
                          SET nome                            = ".str_escape($args['nome']).",
                              promotor                        = ".str_escape($args['promotor']).",
                              endereco                        = ".(trim($args['endereco']) == '' ? "DEFAULT"  : str_escape($args['endereco'])).",
                              cidade                          = ".(trim($args['cidade']) == '' ? "DEFAULT"  : str_escape($args['cidade'])).",
                              uf                              = ".(trim($args['uf']) == '' ? "DEFAULT"  : str_escape($args['uf'])).",
                              dt_inicio                       = ".(trim($args['dt_inicio']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')").",
                              hr_inicio                       = ".(trim($args['hr_inicio']) == "" ? "DEFAULT" : "CAST('".$args['hr_inicio']."' AS TIME)").",
                              dt_final                        = ".(trim($args['dt_final']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_final']."','DD/MM/YYYY')").",   
                              hr_final                        = ".(trim($args['hr_final']) == "" ? "DEFAULT" : "CAST('".$args['hr_final']."' AS TIME)").",
                              carga_horaria                   = ".(trim($args['carga_horaria']) == "" ? "DEFAULT" : floatval($args['carga_horaria'])).",
                              vl_unitario                     = ".(trim($args['vl_unitario']) == '' ? "DEFAULT"  : floatval($args['vl_unitario'])).",
                              fl_certificado                  = ".(trim($args['fl_certificado']) == '' ? "DEFAULT"  : "'".trim($args['fl_certificado'])."'").",
                              fl_bem_estar                    = ".(trim($args['fl_bem_estar']) == '' ? "DEFAULT"  : "'".trim($args['fl_bem_estar'])."'").",
                              cd_treinamento_colaborador_tipo = ".(trim($args['cd_treinamento_colaborador_tipo']) == '' ? "DEFAULT"  : intval($args['cd_treinamento_colaborador_tipo']))."
                        WHERE numero = ".intval($args['numero'])."
                          AND ano    = ".intval($args['ano'])."";
            $this->db->query($qr_sql);
            
            return $args['ano'].'/'.$args['numero'];
        }
    }
    
    function excluir(&$result, $args=array())
    {
        $qr_sql = "
					UPDATE projetos.treinamento_colaborador
                       SET dt_exclusao         = CURRENT_TIMESTAMP,
                           cd_usuario_exclusao = ".intval($args['usuario'])."
                     WHERE numero = ".intval($args['numero'])."
					   AND ano    = ".intval($args['ano'])."
				  ";
        
        $this->db->query($qr_sql);
        
        $qr_sql = "SELECT funcoes.nr_treinamento_colaborador(".intval($args['ano']).", ".intval($args['numero']).") AS numero";
        
        $result = $this->db->query($qr_sql);
        $arr = $result->row_array();
            
        return $arr['numero'];
    }	
	
    function respostas_gerencia($args = array())
    {		
        $qr_sql = "
                 SELECT tci.cd_treinamento_colaborador_item,
                        tci.cd_empresa,
                        tci.cd_registro_empregado,
                        tci.seq_dependencia,
                        tci.nome,
                        tci.area,
                        tci.centro_custo,
						TO_CHAR(tcr.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						TO_CHAR(tcr.dt_finalizado,'DD/MM/YYYY HH24:MI:SS') AS dt_finalizado,
						tcr.cd_treinamento_colaborador_resposta,
						funcoes.get_usuario_nome(tcr.cd_usuario) AS avaliador,
						tcr.cd_envia_emails,
						(SELECT TO_CHAR(eet.dt_inclusao,'DD/MM/YYYY HH24:MI:SS')
							FROM projetos.envia_emails_tracker eet
							WHERE eet.cd_email = tcr.cd_envia_emails 
					        ORDER BY eet.dt_inclusao ASC
							LIMIT 1) AS dt_email_visualizado,
						d.nome AS area_gerencia,
						TO_CHAR(ee.dt_email_enviado,'DD/MM/YYYY HH24:MI:SS') AS dt_email_enviado,
						tcf.fl_enviar_para
                   FROM projetos.treinamento_colaborador_item tci
				    JOIN projetos.treinamento_colaborador_resposta tcr
					 ON tcr.cd_treinamento_colaborador_item = tci.cd_treinamento_colaborador_item
				    JOIN projetos.treinamento_colaborador_formulario tcf
				     ON tcf.cd_treinamento_colaborador_formulario = tcr.cd_treinamento_colaborador_formulario
             AND tcf.fl_enviar_para = 'G'
				   LEFT JOIN projetos.divisoes d
				     ON d.codigo = tci.area
			       LEFT JOIN projetos.envia_emails ee
				     ON ee.cd_email = tcr.cd_envia_emails
                  WHERE tci.numero = ".intval($args['numero'])."
                    AND tci.ano    = ".intval($args['ano'])."
                    AND tci.dt_exclusao IS NULL
					";
					
        return $this->db->query($qr_sql)->result_array();
    }
    
    function colaboradores(&$result, $args=array())
    {		
        $qr_sql = "
                 SELECT tci.cd_treinamento_colaborador_item,
                        tci.cd_empresa,
                        tci.cd_registro_empregado,
                        tci.seq_dependencia,
                        tci.nome,
                        tci.area,
                        tci.centro_custo,
						TO_CHAR(tcr.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						TO_CHAR(tcr.dt_finalizado,'DD/MM/YYYY HH24:MI:SS') AS dt_finalizado,
						tcr.cd_treinamento_colaborador_resposta,
						tcr.cd_envia_emails,
						(SELECT TO_CHAR(eet.dt_inclusao,'DD/MM/YYYY HH24:MI:SS')
							FROM projetos.envia_emails_tracker eet
							WHERE eet.cd_email = tcr.cd_envia_emails 
					        ORDER BY eet.dt_inclusao ASC
							LIMIT 1) AS dt_email_visualizado,
						d.nome AS area_gerencia,
						TO_CHAR(ee.dt_email_enviado,'DD/MM/YYYY HH24:MI:SS') AS dt_email_enviado,
						tcf.fl_enviar_para,
                        COALESCE(tci.fl_certificado, tc.fl_certificado) AS fl_certificado,
                        tci.arquivo,
                        tci.arquivo_nome,
                        tci.dt_certificado,
                        tci.ds_justificativa,
                        tcr.ds_justificativa_finalizado
                   FROM projetos.treinamento_colaborador_item tci
                   JOIN projetos.treinamento_colaborador tc
                     ON tc.ano    = tci.ano
                    AND tc.numero = tci.numero
				   LEFT JOIN projetos.treinamento_colaborador_resposta tcr
					 ON tcr.cd_treinamento_colaborador_item = tci.cd_treinamento_colaborador_item
				   LEFT JOIN projetos.treinamento_colaborador_formulario tcf
				     ON tcf.cd_treinamento_colaborador_formulario = tcr.cd_treinamento_colaborador_formulario
                    AND tcf.fl_enviar_para = 'C'
				   LEFT JOIN projetos.divisoes d
				     ON d.codigo = tci.area
			       LEFT JOIN projetos.envia_emails ee
				     ON ee.cd_email = tcr.cd_envia_emails
                  WHERE tci.numero = ".intval($args['numero'])."
                    AND tci.ano    = ".intval($args['ano'])."
                    AND tci.dt_exclusao IS NULL;";
					
        $result = $this->db->query($qr_sql);
    }

     public function carrega_colaborador($cd_treinamento_colaborador_item)
    {   
        $qr_sql = "
            SELECT tdci.cd_treinamento_colaborador_item,
                   tdci.cd_empresa,
                   tdci.cd_registro_empregado,
                   tdci.seq_dependencia,
                   tdci.nome,
                   tdci.area,
                   tdci.centro_custo,
                   tdci.arquivo,
                   tdci.arquivo_nome
              FROM projetos.treinamento_colaborador_item tdci
             WHERE tdci.cd_treinamento_colaborador_item = ".intval($cd_treinamento_colaborador_item)."
               AND tdci.dt_exclusao IS NULL;";
                    
        return $this->db->query($qr_sql)->row_array();
    }
    
    function gerencias( &$result, $args=array() )
	{
        $qr_sql = "
					SELECT codigo AS value,
						   nome AS text
					  FROM projetos.divisoes
					 ORDER BY text
			      ";
        
        $result = $this->db->query($qr_sql);
    }
    
    function uf( &$result, $args=array() )
	{
        $qr_sql = "
					SELECT cd_uf AS value,
						   ds_uf AS text
					  FROM geografico.uf
					 ORDER BY text
				  ";
        
        $result = $this->db->query($qr_sql);
    }
    
    function salvar_colaborador( &$result, $args=array() )
    {
        
        $qr_sql = "INSERT INTO projetos.treinamento_colaborador_item
                               (
                                numero,
                                ano,
                                cd_empresa,
                                cd_registro_empregado,
                                seq_dependencia,
                                nome,
                                area,
                                centro_custo,
                                arquivo,
                                arquivo_nome,
                                cd_usuario_inclusao,
                                cd_usuario_alteracao
                               )
                         VALUES
                               (
                                ".intval($args['numero']).",
                                ".intval($args['ano']).",
								".(trim($args['cd_empresa'])            == '' ? "DEFAULT"  : intval($args['cd_empresa'])).", 
								".(trim($args['cd_registro_empregado']) == '' ? "DEFAULT"  : intval($args['cd_registro_empregado'])).", 
								".(trim($args['seq_dependencia'])       == '' ? "DEFAULT"  : intval($args['seq_dependencia'])).", 
                                UPPER(funcoes.remove_acento('".trim($args['nome'])."')),
                                ".(trim($args['area']) == '' ? "DEFAULT"  : "'".trim($args['area'])."'").",   
                                ".(trim($args['centro_custo']) == '' ? "DEFAULT"  : "'".trim($args['centro_custo'])."'").",
                                ".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
                                ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
                                ".intval($args['usuario']).",
                                ".intval($args['usuario'])."
                               )";
        
            $this->db->query($qr_sql);
    }

    public function atualizar_colaborador($cd_treinamento_colaborador_item, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.treinamento_colaborador_item
               SET cd_empresa            = ".(trim($args['cd_empresa']) == '' ? "DEFAULT"  : intval($args['cd_empresa'])).",
                   cd_registro_empregado = ".(trim($args['cd_registro_empregado']) == '' ? "DEFAULT"  : intval($args['cd_registro_empregado'])).",
                   seq_dependencia       = ".(trim($args['seq_dependencia'])       == '' ? "DEFAULT"  : intval($args['seq_dependencia'])).",
                   nome                  = ".(trim($args['nome']) != '' ? "UPPER(funcoes.remove_acento('".trim($args['nome'])."'))" : "DEFAULT").",
                   area                  = ".(trim($args['area']) == '' ? "DEFAULT"  : "'".trim($args['area'])."'").",
                   centro_custo          = ".(trim($args['centro_custo']) == '' ? "DEFAULT"  : "'".trim($args['centro_custo'])."'").",  
                   arquivo               = ".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
                   arquivo_nome          = ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
                   cd_usuario_alteracao  = ".intval($args['usuario']).",
                   dt_alteracao          = CURRENT_TIMESTAMP
             WHERE cd_treinamento_colaborador_item = ".intval($cd_treinamento_colaborador_item).";";

        $this->db->query($qr_sql);
    }
    
    function excluir_colaborador(&$result, $args=array())
    {
        $qr_sql = "UPDATE projetos.treinamento_colaborador_item
                      SET dt_exclusao         = CURRENT_TIMESTAMP,
                          cd_usuario_exclusao = ".intval($args['usuario'])."
                    WHERE cd_treinamento_colaborador_item = ".intval($args['cd_treinamento_colaborador_item']);
        
        $this->db->query($qr_sql);
        
        $qr_sql = "SELECT funcoes.nr_treinamento_colaborador(ano, numero) AS nr_treinamento_colaborador,
                          ano,
                          numero
                     FROM projetos.treinamento_colaborador_item
                    WHERE cd_treinamento_colaborador_item = ".intval($args['cd_treinamento_colaborador_item']);
        
        $result = $this->db->query($qr_sql);
        $arr = $result->row_array();
            
        return $arr;
    }

    function agendaListar( &$result, $args=array() )
	{
        $qr_sql = "
					SELECT tca.cd_treinamento_colaborador_agenda,
					       TO_CHAR(tca.dt_agenda_ini,'DD/MM/YYYY') AS dt_agenda,
					       TO_CHAR(tca.dt_agenda_ini,'HH24:MI') AS hr_ini,
					       TO_CHAR(tca.dt_agenda_fim,'HH24:MI') AS hr_fim
					  FROM projetos.treinamento_colaborador_agenda tca
					  JOIN projetos.treinamento_colaborador tc
					    ON tc.cd_treinamento_colaborador = tca.cd_treinamento_colaborador
					 WHERE tca.dt_exclusao IS NULL
					   AND tc.numero = ".intval($args['numero'])."
					   AND tc.ano    = ".intval($args['ano'])."
                  ";
				
        $result = $this->db->query($qr_sql);
    }
	
    function agendaSalvar( &$result, $args=array() )
    {
        $qr_sql = "
					INSERT INTO projetos.treinamento_colaborador_agenda
                         (
							cd_treinamento_colaborador,
							dt_agenda_ini,
							dt_agenda_fim,
							cd_usuario_inclusao,
							cd_usuario_alteracao
                         )
                    VALUES
                         (
							(SELECT cd_treinamento_colaborador 
							   FROM projetos.treinamento_colaborador 
							  WHERE numero = ".intval($args['numero'])."
								AND ano    = ".intval($args['ano'])."),
							".(trim($args['dt_agenda']) != ''? "TO_TIMESTAMP('".$args['dt_agenda']." ".$args['hr_ini']."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
							".(trim($args['dt_agenda']) != ''? "TO_TIMESTAMP('".$args['dt_agenda']." ".$args['hr_fim']."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
							".intval($args['usuario']).",
							".intval($args['usuario'])."
                         )
				  ";
        
        $this->db->query($qr_sql);
    }	
	
    function agendaExcluir( &$result, $args=array() )
    {
        $qr_sql = "
					UPDATE projetos.treinamento_colaborador_agenda
					   SET dt_exclusao         = CURRENT_TIMESTAMP,
					       cd_usuario_exclusao = ".intval($args['usuario'])."
					 WHERE cd_treinamento_colaborador_agenda = ".intval($args['cd_treinamento_colaborador_agenda'])."
				  ";
        
        $this->db->query($qr_sql);
    }	

    function agendaAtualizar( &$result, $args=array() )
    {
        $qr_sql = "
					UPDATE projetos.treinamento_colaborador_agenda AS tca
					   SET dt_alteracao         = CURRENT_TIMESTAMP,
					       cd_usuario_alteracao = ".intval($args['usuario'])."
					 WHERE tca.cd_treinamento_colaborador = (SELECT tc.cd_treinamento_colaborador 
															                       FROM projetos.treinamento_colaborador tc
                                                    WHERE tc.dt_final > CURRENT_DATE
                                                      AND tc.dt_exclusao IS NULL
															                        AND tc.numero = ".intval($args['numero'])."
															                        AND tc.ano    = ".intval($args['ano']).");";

        $this->db->query($qr_sql);
    }

    public function carrega_avaliacao($cd_treinamento_colaborador_resposta)
    {
        $qr_sql = "
            SELECT tc.ano, 
                   tc.numero,
                   funcoes.nr_treinamento_colaborador(tc.ano, tc.numero) AS ds_ano_numero,
                   tcr.cd_treinamento_colaborador_resposta,
                   tcr.dt_inclusao,
                   tci.nome,
                   tc.nome AS ds_nome_evento
              FROM projetos.treinamento_colaborador_resposta tcr
              JOIN projetos.treinamento_colaborador_item tci
                ON tci.cd_treinamento_colaborador_item = tcr.cd_treinamento_colaborador_item
              JOIN projetos.treinamento_colaborador tc
                ON tc.ano    = tci.ano
               AND tc.numero = tci.numero
             WHERE tcr.cd_treinamento_colaborador_resposta = ".intval($cd_treinamento_colaborador_resposta).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_encerramento($cd_treinamento_colaborador_resposta, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.treinamento_colaborador_resposta
               SET ds_justificativa_finalizado = ".(trim($args['ds_justificativa_finalizado']) != '' ? str_escape($args['ds_justificativa_finalizado']) : "DEFAULT").", 
                   cd_usuario_finalizado       = ".intval($args['cd_usuario']).",
                   dt_finalizado               = CURRENT_TIMESTAMP
             WHERE cd_treinamento_colaborador_resposta = ".intval($cd_treinamento_colaborador_resposta).";";

        $this->db->query($qr_sql);
    }
}
