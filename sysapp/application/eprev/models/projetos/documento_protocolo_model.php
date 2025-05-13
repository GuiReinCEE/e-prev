<?php
class Documento_protocolo_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
  	###################################
  	# MODEL CHAMADA DE VÁRIOS LUGARES #
    ###################################
    function listar( &$result, $args=array() )
    {
        $qr_sql = "
            SELECT DISTINCT funcoes.nr_protocolo_digitalizacao(a.ano, a.contador) AS nr_protocolo,
                   a.ano,
                   a.contador,
                   --a3.arquivo,
                   a.tipo,
                   a.cd_documento_protocolo,
                   TO_CHAR(a.dt_cadastro,'DD/MM/YYYY HH24:MI') AS dt_cadastro,
                   a.cd_usuario_cadastro,
                   usuario_cadastro.nome AS nome_usuario_cadastro,
                   TO_CHAR(a.dt_envio,'DD/MM/YYYY HH24:MI') AS dt_envio,
                   a.cd_usuario_envio,
                   usuario_envio.nome AS nome_usuario_envio,
                   usuario_envio.divisao AS divisao_usuario_envio,
                   TO_CHAR(a.dt_ok,'DD/MM/YYYY HH24:MI') AS dt_ok,
                   a.cd_usuario_ok,
                   usuario_ok.nome AS nome_usuario_ok,
                   TO_CHAR(a.dt_exclusao,'DD/MM/YYYY HH24:MI') AS dt_exclusao,
                   a.cd_usuario_exclusao,
                   a.motivo_exclusao,
                   a.ordem_itens,
                   TO_CHAR(a.dt_indexacao,'DD/MM/YYYY') AS dt_indexacao,
                   a.cd_usuario_indexacao,
                   (SELECT COUNT(*) 
                      FROM projetos.documento_protocolo_item a1
                     WHERE a1.dt_exclusao IS NULL 
                       AND a1.cd_documento_protocolo = a.cd_documento_protocolo) AS quantidade_item,
                   (SELECT COUNT(*) 
                      FROM projetos.documento_protocolo_item a1
                     WHERE a1.dt_exclusao IS NULL 
                       AND a1.fl_recebido    = 'S'
                       AND a1.cd_documento_protocolo = a.cd_documento_protocolo) AS quantidade_item_recebido,
                   (SELECT COUNT(*) 
                      FROM projetos.documento_protocolo_item a1
                     WHERE a1.dt_exclusao  IS NULL 
                       AND a1.dt_devolucao IS NOT NULL
                       AND a1.cd_documento_protocolo = a.cd_documento_protocolo ) AS quantidade_item_devolvido 
			  FROM projetos.documento_protocolo a
			  LEFT JOIN projetos.usuarios_controledi usuario_cadastro 
				ON usuario_cadastro.codigo = a.cd_usuario_cadastro
			  LEFT JOIN projetos.usuarios_controledi usuario_envio 
				ON usuario_envio.codigo = a.cd_usuario_envio
			  LEFT JOIN projetos.usuarios_controledi usuario_ok 
				ON usuario_ok.codigo=a.cd_usuario_ok
              LEFT JOIN projetos.documento_protocolo_item a3
                ON a3.cd_documento_protocolo = a.cd_documento_protocolo 
             WHERE a.dt_exclusao IS NULL
                ".(intval($args['ano']) > 0 ? "AND a.ano = ".intval($args['ano']) : "")."
                ".(intval($args['contador']) > 0 ? "AND a.contador = ".intval($args['contador']) : "")."
                ".(trim($args['tipo_protocolo']) != "" ? "AND a.tipo = '".trim($args['tipo_protocolo'])."'" : "")."
                ".(trim($args['fl_envio']) == "S" ? "AND a.dt_envio IS NOT NULL" : "")."
                ".(trim($args['fl_envio']) == "N" ? "AND a.dt_envio IS NULL" : "")."
                ".(trim($args['fl_recebido']) == "S" ? "AND a.dt_ok IS NOT NULL" : "")."
                ".(trim($args['fl_recebido']) == "N" ? "AND a.dt_ok IS NULL" : "")."						
                ".(((trim($args['dt_inclusao_ini']) != "") and (trim($args['dt_inclusao_fim']) != "")) ? " AND CAST(a.dt_cadastro AS DATE) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")."
                ".(((trim($args['dt_envio_ini']) != "") and (trim($args['dt_envio_fim']) != "")) ? " AND CAST(a.dt_envio AS DATE) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                ".(((trim($args['dt_recebido_ini']) != "") and (trim($args['dt_recebido_fim']) != "")) ? " AND CAST(a.dt_ok AS DATE) BETWEEN TO_DATE('".$args['dt_recebido_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_recebido_fim']."', 'DD/MM/YYYY')" : "")."
                ".(((array_key_exists("cd_usuario_envio", $args)) and (trim($args['cd_usuario_envio']) != "")) ? " AND a.cd_usuario_envio = ".intval($args["cd_usuario_envio"]) : "")."
                ".(((array_key_exists("cd_gerencia", $args)) and (trim($args['cd_gerencia']) != "")) ? " AND usuario_cadastro.divisao = '".trim($args["cd_gerencia"])."'" : "")."
            AND (usuario_cadastro.codigo = ".intval($args['cd_usuario_logado']) ."
             OR usuario_envio.codigo = ".intval($args['cd_usuario_logado']) ." 
             OR ('".$args["gerencia_responsavel_recebimento"]."' = '".$args["gerencia_usuario_logado"]."' 
             OR ".intval($args['cd_usuario_logado']) ." = 251
             OR ".intval($args['cd_usuario_logado']) ." = 170
            AND a.dt_envio IS NOT NULL 
            AND (a.dt_ok IS NULL OR a.dt_indexacao IS NULL )));";

        $result = $this->db->query($qr_sql);
    }

    function get_usuario_envio(&$result, $args = array())
    {
      $qr_sql = "
        SELECT DISTINCT a.cd_usuario_envio AS value,
               uc.nome AS text
          FROM projetos.documento_protocolo a
          JOIN projetos.usuarios_controledi uc 
            ON uc.codigo = a.cd_usuario_envio
         WHERE a.dt_exclusao IS NULL
         ORDER BY uc.nome";

      $result = $this->db->query($qr_sql);
    }

    public function get_gerencia_envio()
    {
        $qr_sql = "
            SELECT DISTINCT d.codigo AS value,
                   d.codigo || ' - ' || d.nome  AS text
              FROM projetos.documento_protocolo a
              JOIN projetos.usuarios_controledi uc 
                ON uc.codigo = a.cd_usuario_envio
              JOIN projetos.divisoes d
                ON d.codigo = uc.divisao 
             WHERE a.dt_exclusao IS NULL
             ORDER BY d.codigo;";

        return  $this->db->query($qr_sql)->result_array();
    }

    function carregar(&$result, $args=array())
    {
        $qr_sql = " 
            SELECT ano,
                   dp.tipo,
                   dp.contador,
                   dp.cd_documento_protocolo,
                   TO_CHAR(dp.dt_cadastro,'DD/MM/YYYY HH24:MI') AS dt_cadastro,
                   dp.cd_usuario_cadastro,
                   TO_CHAR(dp.dt_envio,'DD/MM/YYYY HH24:MI') AS dt_envio,
                   dp.cd_usuario_envio,
                   TO_CHAR(dp.dt_ok,'DD/MM/YYYY') AS dt_ok,
                   dp.cd_usuario_ok,
                   TO_CHAR(dp.dt_exclusao,'DD/MM/YYYY HH24:MI') AS dt_exclusao,
                   dp.cd_usuario_exclusao,
                   funcoes.get_usuario_nome(dp.cd_usuario_exclusao) AS ds_usuario_exclusao,
                   dp.motivo_exclusao,
                   dp.ordem_itens,
                   TO_CHAR(dp.dt_indexacao,'DD/MM/YYYY') AS dt_indexacao,
                   dp.cd_usuario_indexacao,
                   usuario_cadastro.nome AS nome_usuario_cadastro,
                   usuario_envio.nome AS nome_usuario_envio,
                   usuario_ok.nome AS nome_usuario_ok,
                   usuario_indexacao.nome AS nome_usuario_indexacao,
                   COALESCE(dp.fl_contrato, 'N') AS fl_contrato
              FROM projetos.documento_protocolo dp
              JOIN projetos.usuarios_controledi usuario_cadastro 
                ON usuario_cadastro.codigo = dp.cd_usuario_cadastro
              LEFT JOIN projetos.usuarios_controledi usuario_envio 
                ON usuario_envio.codigo = dp.cd_usuario_envio
              LEFT JOIN projetos.usuarios_controledi usuario_ok 
                ON usuario_ok.codigo = dp.cd_usuario_ok
              LEFT JOIN projetos.usuarios_controledi usuario_indexacao 
                ON usuario_indexacao.codigo = dp.cd_usuario_indexacao
             WHERE cd_documento_protocolo = ".intval($args['cd_documento_protocolo']).";";
			 
        $result = $this->db->query($qr_sql);
    }

    function criar_protocolo($args,&$msg=array(),&$row)
    {
        $new_id = $this->db->get_new_id("projetos.documento_protocolo","cd_documento_protocolo");
		
        $qr_sql = "
                INSERT INTO projetos.documento_protocolo
                       (
                         cd_documento_protocolo,
                         ano,
                         contador,
                         tipo,
                         cd_usuario_cadastro,
						 cd_gerencia_origem,
                         fl_contrato
                        )
                   VALUES 
                        (
                          ".$new_id.",
                          ".intval($args["ano"]).",
                          (SELECT COALESCE(MAX(contador)+1,1) 
                             FROM projetos.documento_protocolo 
                            WHERE ano = ".intval($args["ano"])."),
                          ".(trim($args['tipo_protocolo']) == "" ? "DEFAULT" : "'".$args['tipo_protocolo']."'").",
                          ".intval($args["cd_usuario_cadastro"]).",
						  '".trim($args["cd_gerencia"])."',
                          ".(trim($args['fl_contrato']) != '' ? "'".trim($args['fl_contrato'])."'" : "DEFAULT")."
                          );";
        try
        {
            $query = $this->db->query($qr_sql);

            $codigos['cd_documento_protocolo'] = $new_id;

            $query = $this->db->query("SELECT cd_documento_protocolo, 
                                              ano, 
                                              contador 
                                         FROM projetos.documento_protocolo 
                                        WHERE cd_documento_protocolo=?", array(intval($new_id)));
            $row = $query->row_array();

            if($args['tipo_protocolo'] == "D")
            {
                #### CRIA DIRETORIO PARA UPLOAD ####
                $dir = "../cieprev/up/protocolo_digitalizacao_".$row['cd_documento_protocolo'];
                if(!is_dir($dir))
                {
                    mkdir($dir);
                }	
            }
            return true;
        }
        catch(Exception $e)
        {
            $msg[]=$e->getMessage();
            return false;
        }
    }
	
    function enviar_protocolo($args, &$msg=array())
    {
        if(intval($args['cd_documento_protocolo']) > 0)
        {
            $qr_sql = "
                UPDATE projetos.documento_protocolo
                   SET dt_envio         = CURRENT_TIMESTAMP,
                       cd_usuario_envio = ".intval($args["cd_usuario_envio"])."
                WHERE cd_documento_protocolo = ".intval($args["cd_documento_protocolo"]).";";
            try
            {
                $query = $this->db->query($qr_sql);
                return true;
            }
            catch(Exception $e)
            {
                $msg[]=$e->getMessage();
                return false;
            }
        }
    }
	
    function excluir_protocolo (&$result, $args=array())
    {
        $qr_sql = "
			     UPDATE projetos.documento_protocolo
			        SET dt_exclusao         = CURRENT_TIMESTAMP,
				          cd_usuario_exclusao = ".intval($args["cd_usuario"])."
			      WHERE cd_documento_protocolo = ".intval($args["cd_documento_protocolo"]).";";

        $result = $this->db->query($qr_sql);
    }
    
    function descartar (&$result, $args=array())
    {
        $qr_sql = "SELECT fl_descarte 
                     FROM projetos.documento_protocolo_descarte 
                    WHERE dt_exclusao IS NULL
                      AND cd_documento = ". $args['cd_tipo_doc']."
					  AND cd_divisao   = '".trim($args['cd_divisao'])."';";
					  
        $result = $this->db->query($qr_sql);
    }

    public function get_mes_ano_indicador()
    {
        $qr_sql = "
            SELECT DISTINCT TO_CHAR(p.dt_ok, 'YYYYMM') AS value,
                   TO_CHAR(p.dt_ok, 'MM/YYYY') AS text
              FROM projetos.documento_protocolo p
             WHERE p.dt_exclusao IS NULL
               AND p.dt_ok       IS NOT NULL
               AND p.dt_ok::date >= '2020-06-01'::date
             ORDER BY TO_CHAR(p.dt_ok, 'YYYYMM') DESC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function total($ds_mes_ano_indicador)
    {
        $qr_sql = "
            SELECT COUNT(*) AS total
              FROM projetos.documento_protocolo p
              JOIN projetos.documento_protocolo_item d
                ON d.cd_documento_protocolo = p.cd_documento_protocolo
             WHERE p.dt_exclusao IS NULL
               AND d.dt_exclusao IS NULL
               AND TO_CHAR(p.dt_ok, 'YYYYMM') = '".trim($ds_mes_ano_indicador)."';";
 
        return $this->db->query($qr_sql)->row_array();
    }

    public function fora_prazo($ds_mes_ano_indicador)
    {
        $qr_sql = "
            SELECT COUNT(*) AS total
              FROM projetos.documento_protocolo p
              JOIN projetos.documento_protocolo_item d
                ON d.cd_documento_protocolo = p.cd_documento_protocolo
             WHERE p.dt_exclusao IS NULL
               AND d.dt_exclusao IS NULL
               AND TO_CHAR(p.dt_ok, 'YYYYMM') = '".trim($ds_mes_ano_indicador)."'
               AND funcoes.dia_util('DEPOIS', p.dt_ok::date, 5) < d.dt_indexacao";

        return $this->db->query($qr_sql)->row_array();
    }
	
    function relatorio( &$result, $args=array() )
    {
        $qr_sql = "
            SELECT DISTINCT a.ano,
                   a.contador,
                   a.tipo,
                   funcoes.nr_protocolo_digitalizacao(a.ano, a.contador) AS nr_protocolo,
                   a.cd_documento_protocolo,
                   a.cd_usuario_cadastro,
                   TO_CHAR(a.dt_envio, 'DD/MM/YYYY HH24:MI') AS dt_envio,
                   a.cd_usuario_envio,
                   usuario_envio.nome AS nome_usuario_envio,
                   usuario_envio.divisao AS divisao_usuario_envio,
                   TO_CHAR(a.dt_ok, 'DD/MM/YYYY HH24:MI') AS dt_ok,
                   a.cd_usuario_ok,		
                   usuario_ok.nome AS nome_usuario_ok,
                   usuario_ok.divisao AS divisao_usuario_ok,
                   TO_CHAR(b.dt_indexacao, 'DD/MM/YYYY') AS dt_indexacao,
                   TO_CHAR(b.dt_devolucao, 'DD/MM/YYYY HH24:MI') AS dt_devolucao,
                   TO_CHAR(b.dt_cadastro, 'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro,
                   b.motivo_devolucao,
                   b.observacao,
                   b.ds_observacao_indexacao,
                   b.cd_tipo_doc,
                   d.nome_documento,
                   b.nr_id_contrato,
                   b.cd_doc_juridico,
                   dj.nome_documento AS nome_documento_juridico,
                   b.cd_empresa,
                   b.cd_registro_empregado,
                   b.seq_dependencia,
                   b.nr_folha,
                   b.arquivo,
                   COALESCE(c.nome,'') AS nome_participante,
                   CASE WHEN b.fl_descartar = 'S' THEN 'Sim'
                        ELSE 'Não'
                   END  AS fl_descartar,			   
                   b.ds_processo,
				   b.banco,
				   b.caminho,
				   a.cd_gerencia_origem,
                   b.ds_caminho_liquid,
                   b.ds_tempo_descarte,
                   b.id_classificacao_info_doc,
                   a.dt_ok AS dt_recebimento
              FROM projetos.documento_protocolo a
              JOIN projetos.documento_protocolo_item b
                ON a.cd_documento_protocolo = b.cd_documento_protocolo
              LEFT JOIN public.participantes c
                ON b.cd_empresa            = c.cd_empresa 
               AND b.cd_registro_empregado = c.cd_registro_empregado 
               AND b.seq_dependencia       = c.seq_dependencia
              LEFT JOIN public.tipo_documentos d 
                ON b.cd_tipo_doc = d.cd_tipo_doc
              LEFT JOIN public.tipo_documentos dj 
                ON b.cd_doc_juridico = dj.cd_tipo_doc
              LEFT JOIN projetos.usuarios_controledi usuario_envio 
                ON usuario_envio.codigo = a.cd_usuario_envio
              LEFT JOIN projetos.usuarios_controledi usuario_ok
                ON usuario_ok.codigo = a.cd_usuario_ok
             WHERE a.dt_exclusao IS NULL
			   AND b.dt_exclusao IS NULL
			   --AND 1 = (CASE WHEN a.cd_gerencia_origem = 'AJ' AND funcoes.get_usuario_area(".intval($args['cd_usuario_logado']).") <> 'AJ' THEN 0 ELSE 1 END)
                ".(intval($args['ano']) > 0 ? "AND a.ano = ".intval($args['ano']) : "")."
                ".(intval($args['contador']) > 0 ? "AND a.contador = ".intval($args['contador']) : "")."
                ".(trim($args['tipo_protocolo']) != "" ? "AND a.tipo = '".trim($args['tipo_protocolo'])."'" : "")."
                ".(trim($args['cd_empresa']) != "" ? "AND b.cd_empresa = ".intval($args['cd_empresa']) : "")."
                ".(trim($args['cd_registro_empregado']) != "" ? "AND b.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
                ".(trim($args['seq_dependencia']) != "" ? "AND b.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
                ".(trim($args['nome']) != "" ? "AND UPPER(c.nome) LIKE UPPER('%".intval($args['nome'])."%')" : "")."
                ".(intval($args['cd_tipo_doc']) > 0 ? "AND b.cd_tipo_doc = ".intval($args['cd_tipo_doc']) : "")."
                ".(intval($args['cd_doc_juridico']) > 0 ? "AND b.cd_doc_juridico = ".intval($args['cd_doc_juridico']) : "")."
                ".(intval($args['cd_usuario_envio']) > 0 ? "AND a.cd_usuario_cadastro = ".intval($args['cd_usuario_envio']) : "")."
                ".(trim($args['ds_processo']) != "" ? "AND b.ds_processo = '".trim($args['ds_processo'])."'" : "")."
                ".(trim($args['ds_mes_ano_indicador']) != "" ? "AND TO_CHAR(a.dt_ok, 'YYYYMM') = '".trim($args['ds_mes_ano_indicador'])."'" : "")."
                ".(((trim($args['dt_envio_inicio']) != "") and (trim($args['dt_envio_fim']) != "")) ? " AND CAST(a.dt_envio AS DATE) BETWEEN TO_DATE('".$args['dt_envio_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                ".(((trim($args['dt_ok_inicio']) != "") and (trim($args['dt_ok_fim']) != "")) ? " AND CAST(a.dt_ok AS DATE) BETWEEN TO_DATE('".$args['dt_ok_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_ok_fim']."', 'DD/MM/YYYY')" : "")."
                ORDER BY dt_recebimento DESC
                ".(((trim($args['qt_pagina']) != "") and (trim($args['nr_pagina']) != "")) ? " 
                  LIMIT ".intval($args['qt_pagina'])."
                 OFFSET ".(intval($args['nr_pagina']) == 0 ? 0 : intval($args['nr_pagina'])-1)."" 
                : "")."

         ;";
		  $result = $this->db->query($qr_sql);
    }

    function adicionaDocumento(&$result, $args=array())
    {	
        if(intval($args['cd_documento_protocolo_item']) == 0)
        {
            $cd_protocolo_item_new = $this->db->get_new_id("projetos.documento_protocolo_item","cd_documento_protocolo_item");
        }
        else
        {
            $cd_protocolo_item_new = intval($args['cd_documento_protocolo_item']);
        }

        #### PADRONIZA NOME DO ARQUIVO ####
        $dir = "../cieprev/up/protocolo_digitalizacao_".$args['cd_documento_protocolo'];
		
  		if(! gerencia_in(array('SG', 'GC', 'GS', 'GFC', 'GP')) OR gerencia_in(array('GNR')))
  		{

  		    if((trim($args['arquivo']) != "") and (file_exists($dir."/".trim($args['arquivo']))))
  		    {
  				$ar_tmp = explode(".",trim($args['arquivo']));
          
                  if(intval($args['cd_empresa']) == 0 AND intval($args['cd_registro_empregado']) == 0 AND intval($args['seq_dependencia']) == 0)
                  {
  					$arq = "DAP_".$args['cd_documento']."_".intval($cd_protocolo_item_new).".".$ar_tmp[count($ar_tmp)-1];
                  }
                  else
                  {
                      $arq = $args['cd_empresa']."_".$args['cd_registro_empregado']."_".$args['seq_dependencia']."_".$args['cd_documento']."_".intval($cd_protocolo_item_new).".".$ar_tmp[count($ar_tmp)-1];
                  }
  			
  				rename($dir."/".trim($args['arquivo']), $dir."/".$arq);
  				$args['arquivo'] = $arq;
  			}		
  		}
          else if(gerencia_in(array('GS', 'GFC', 'GP')))
          {
              $dir = "../cieprev/up/protocolo_digitalizacao_".$args['cd_documento_protocolo'];
              if((trim($args['arquivo']) != "") and (file_exists($dir."/".trim($args['arquivo']))))
              {
                $qr_sql = "SELECT funcoes.remove_acento('".trim(utf8_decode($args['arquivo_nome']))."') AS arquivo";

                $row = $this->db->query($qr_sql)->row_array();

                rename($dir."/".trim($args['arquivo']), $dir."/".utf8_decode(trim($row['arquivo'])));
                $args['arquivo'] = trim($row['arquivo']);
              }
          }

          if(intval($args['cd_documento_protocolo_item']) > 0)
          {
  		    $qr_sql = " 
  		        UPDATE projetos.documento_protocolo_item 
  		           SET cd_tipo_doc           = ".$args['cd_documento'].",
  		               cd_empresa            = ".$args['cd_empresa'].",
  		               cd_registro_empregado = ".$args['cd_registro_empregado'].",
  		               seq_dependencia       = ".$args['seq_dependencia'].",
  		               observacao            = '".utf8_decode($args['observacao'])."',
  		               nr_folha              = ".$args['nr_folha'].",
  		               dt_cadastro           = CURRENT_TIMESTAMP,
  		               cd_usuario_cadastro   = ".$args['cd_usuario_cadastro'].",
  		               arquivo               = ".(trim($args['arquivo']) == "" ? "DEFAULT" : "'".$args['arquivo']."'").",
  		               fl_descartar          = ".((!array_key_exists('fl_descartar', $args) OR trim($args['fl_descartar']) == "") ? "DEFAULT" : "'".$args['fl_descartar']."'").",
  		               arquivo_nome          = ".(trim($args['arquivo_nome']) == "" ? "DEFAULT" : "funcoes.remove_acento('".utf8_decode($args['arquivo_nome'])."')").",
  					   banco                 = ".(((isset($args['banco'])) AND (trim($args['banco']) != '')) ? "'".utf8_decode(trim($args['banco']))."'" : "DEFAULT").",
  					   caminho               = ".(((isset($args['caminho'])) AND (trim($args['caminho']) != '')) ? "'".utf8_decode(trim($args['caminho']))."'" : "DEFAULT")." ,
  		               ds_caminho_liquid     = ".(((isset($args['ds_caminho_liquid'])) AND (trim($args['ds_caminho_liquid']) != '')) ? "'".utf8_decode(trim($args['ds_caminho_liquid']))."'" : "DEFAULT").",
  		               fl_tipo_protocolo_contrato     = ".(((isset($args['fl_tipo_protocolo_contrato'])) AND (trim($args['fl_tipo_protocolo_contrato']) != '')) ? "'".(trim($args['fl_tipo_protocolo_contrato']))."'" : "DEFAULT").",
  		               nr_ano_contrato     = ".(((isset($args['nr_ano_contrato'])) AND (trim($args['nr_ano_contrato']) != '')) ? intval($args['nr_ano_contrato']) : "DEFAULT").",
  		               nr_id_contrato     = ".(((isset($args['nr_id_contrato'])) AND (trim($args['nr_id_contrato']) != '')) ? intval($args['nr_id_contrato']) : "DEFAULT").",
                     ds_tempo_descarte = ".(trim($args['ds_tempo_descarte']) != '' ? str_escape($args['ds_tempo_descarte']) : "DEFAULT").",
  		               id_classificacao_info_doc = ".(trim($args['id_classificacao_info_doc']) != '' ? str_escape($args['id_classificacao_info_doc']) : "DEFAULT")."
  		         WHERE cd_documento_protocolo_item = ".$args['cd_documento_protocolo_item'].";";
              
             # $this->db->query(utf8_decode($qr_sql));
          }
          else
          {

              $qr_sql = "
                  INSERT INTO projetos.documento_protocolo_item
                            (
                              cd_documento_protocolo_item,
                              cd_documento_protocolo,
                              cd_tipo_doc,
                              cd_empresa,
                              cd_registro_empregado,
                              seq_dependencia,
                              dt_cadastro,
                              cd_usuario_cadastro,
                              descricao,
                              fl_recebido,
                              observacao,
                              ds_processo,
                              nr_folha,
                              cd_doc_juridico,
                              arquivo,
                              arquivo_nome,
                              fl_descartar,
  							banco,
  							caminho,
                              ds_caminho_liquid,
                              fl_tipo_protocolo_contrato,
                              nr_ano_contrato,
                              nr_id_contrato,
                              ds_tempo_descarte,
                              id_classificacao_info_doc
                             )
                        VALUES 
                             (
                               ".$cd_protocolo_item_new.",
                               ".intval($args['cd_documento_protocolo']).",
                               ".intval($args['cd_documento']).",
                               ".(trim($args['cd_empresa']) == "" ? "DEFAULT" : $args['cd_empresa']).",
                               ".(trim($args['cd_registro_empregado']) == "" ? "DEFAULT" : $args['cd_registro_empregado']).",
                               ".(trim($args['seq_dependencia']) == "" ? "DEFAULT" : $args['seq_dependencia']).",
                               ".(((isset($args['dt_cadastro'])) AND (trim($args['dt_cadastro']) != '')) ? "TO_TIMESTAMP('".$args['dt_cadastro']."', 'DD/MM/YYYY HH24:MI:SS')" : "CAST(timeofday() AS TIMESTAMP)").",
                               ".intval($args['cd_usuario_cadastro']).",
                               NULL,
                               'N',
                               ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".utf8_decode($args['observacao'])."'").",
                               ".(trim($args['ds_processo']) == "" ? "DEFAULT" : "'".$args['ds_processo']."'").",
                               ".intval($args['nr_folha']).",
                               NULL,
                               ".(trim($args['arquivo']) == "" ? "DEFAULT" : "funcoes.remove_acento('".$args['arquivo']."')").",
                               ".(trim($args['arquivo_nome']) == "" ? "DEFAULT" : "'".utf8_decode($args['arquivo_nome'])."'").",
                               ".((!array_key_exists('fl_descartar', $args) OR trim($args['fl_descartar']) == "") ? "DEFAULT" : "'".$args['fl_descartar']."'").",
  							 ".(((isset($args['banco'])) AND (trim($args['banco']) != '')) ? "'".utf8_decode(trim($args['banco']))."'" : "DEFAULT").",
  							 ".(((isset($args['caminho'])) AND (trim($args['caminho']) != '')) ? "'".utf8_decode(trim($args['caminho']))."'" : "DEFAULT").",
                               ".(((isset($args['ds_caminho_liquid'])) AND (trim($args['ds_caminho_liquid']) != '')) ? "'".utf8_decode(trim($args['ds_caminho_liquid']))."'" : "DEFAULT").",
                               ".(((isset($args['fl_tipo_protocolo_contrato'])) AND (trim($args['fl_tipo_protocolo_contrato']) != '')) ? "'".(trim($args['fl_tipo_protocolo_contrato']))."'" : "DEFAULT").",
                               ".(((isset($args['nr_ano_contrato'])) AND (trim($args['nr_ano_contrato']) != '')) ? intval($args['nr_ano_contrato']) : "DEFAULT").",
                               ".(((isset($args['nr_id_contrato'])) AND (trim($args['nr_id_contrato']) != '')) ? intval($args['nr_id_contrato']) : "DEFAULT").",
                               ".(trim($args['ds_tempo_descarte']) != '' ? str_escape($args['ds_tempo_descarte']) : "DEFAULT").",
                               ".(trim($args['id_classificacao_info_doc']) != '' ? str_escape($args['id_classificacao_info_doc']) : "DEFAULT")."
                             );";
          
  		    #echo '<pre>'.$qr_sql.'</pre>'; exit;
              
          }
          $this->db->query($qr_sql);
    }

    function adicionaDocumentoSemDocumento(&$result, $args=array())
    { 
        $cd_protocolo_item_new = intval($args['cd_documento_protocolo_item']);

        if(intval($args['cd_documento_protocolo_item']) > 0)
        {
            $qr_sql = " 
                UPDATE projetos.documento_protocolo_item 
                   SET cd_tipo_doc           = ".$args['cd_documento'].",
                       cd_empresa            = ".$args['cd_empresa'].",
                       cd_registro_empregado = ".$args['cd_registro_empregado'].",
                       seq_dependencia       = ".$args['seq_dependencia'].",
                       observacao            = '".utf8_decode($args['observacao'])."',
                       nr_folha              = ".$args['nr_folha'].",
                       dt_cadastro           = CURRENT_TIMESTAMP,
                       cd_usuario_cadastro   = ".$args['cd_usuario_cadastro'].",
                       fl_descartar          = ".((!array_key_exists('fl_descartar', $args) OR trim($args['fl_descartar']) == "") ? "DEFAULT" : "'".$args['fl_descartar']."'").",
             			banco                = ".(((isset($args['banco'])) AND (trim($args['banco']) != '')) ? "'".utf8_decode(trim($args['banco']))."'" : "DEFAULT").",
             			caminho              = ".(((isset($args['caminho'])) AND (trim($args['caminho']) != '')) ? "'".utf8_decode(trim($args['caminho']))."'" : "DEFAULT")." ,
                       ds_caminho_liquid     = ".(((isset($args['ds_caminho_liquid'])) AND (trim($args['ds_caminho_liquid']) != '')) ? "'".utf8_decode(trim($args['ds_caminho_liquid']))."'" : "DEFAULT").",
                       fl_tipo_protocolo_contrato     = ".(((isset($args['fl_tipo_protocolo_contrato'])) AND (trim($args['fl_tipo_protocolo_contrato']) != '')) ? "'".(trim($args['fl_tipo_protocolo_contrato']))."'" : "DEFAULT").",
                       nr_ano_contrato     = ".(((isset($args['nr_ano_contrato'])) AND (trim($args['nr_ano_contrato']) != '')) ? intval($args['nr_ano_contrato']) : "DEFAULT").",
                       nr_id_contrato     = ".(((isset($args['nr_id_contrato'])) AND (trim($args['nr_id_contrato']) != '')) ? intval($args['nr_id_contrato']) : "DEFAULT").",
                   ds_tempo_descarte = ".(trim($args['ds_tempo_descarte']) != '' ? str_escape($args['ds_tempo_descarte']) : "DEFAULT").",
		               id_classificacao_info_doc = ".(trim($args['id_classificacao_info_doc']) != '' ? str_escape($args['id_classificacao_info_doc']) : "DEFAULT")."
                 WHERE cd_documento_protocolo_item = ".$args['cd_documento_protocolo_item'].";";
            
           # $this->db->query(utf8_decode($qr_sql));
        }

        
        #echo '<pre>'.$qr_sql.'</pre>'; exit;
            
        
        $this->db->query($qr_sql);
    }
	
    function usuario_combo( &$result, $args=array() )
    {
        $qr_sql = "
            SELECT a.codigo AS value, 
                   a.divisao || ' - ' || a.nome AS text 
              FROM projetos.usuarios_controledi a 
              JOIN projetos.documento_protocolo b 
                ON a.codigo = b.cd_usuario_envio 
             ORDER BY a.divisao, 
                   a.nome";
        $result = $this->db->query($qr_sql);
    }	
        
    function adicionar_documentos_por_processo(&$result, $args=array())
    {
        $qr_sql = " 
             INSERT INTO projetos.documento_protocolo_item
                       (
                         cd_documento_protocolo, 
                         cd_tipo_doc, 
                         cd_empresa, 
                         cd_registro_empregado, 
                         seq_dependencia, 
                         dt_cadastro, 
                         cd_usuario_cadastro, 
                         descricao, 
                         fl_recebido, 
                         observacao, 
                         ds_processo, 
                         nr_folha, 
                         cd_doc_juridico
                       )
                        SELECT ".intval($args['cd_documento_protocolo']).", 
                               null, 
                               pj.part_patr_cd_empresa, 
                               pj.part_cd_registro_empregado, 
                               pj.part_seq_dependencia, 
                               CURRENT_TIMESTAMP,
                               ".intval($args['cd_usuario_cadastro']).", 
                               null, 
                               'N', 
                               '', 
                               CAST(pd.proc_nro_processo AS TEXT) || ' - ' || CAST(pd.proc_ano AS TEXT), 
                               1, 
                               dj.cd_documento
                          FROM public.proc_docs pd
                          JOIN public.documentos_juridicos dj
                            ON dj.cd_documento = pd.doc_jur_cd_documento
                          JOIN public.processos_juridicos pj
                            ON pj.nro_processo     = pd.proc_nro_processo
                           AND pj.ano              = pd.proc_ano
                           AND pj.loc_jur_cd_local = pd.proc_cd_local
                          LEFT JOIN public.participantes p
                            ON p.cd_empresa            = pj.part_patr_cd_empresa
                           AND p.cd_registro_empregado = pj.part_cd_registro_empregado
                           AND p.seq_dependencia       = pj.part_seq_dependencia
                         WHERE 1 = 1
                           ".(intval($args['cd_processo']) > 0 ? " AND pd.proc_nro_processo = " . intval($args['cd_processo']) : "")."
                           ".(((trim($args['dt_inicio']) != "") AND (trim($args['dt_fim']) != "")) ? "AND CAST(pd.dt_processo AS DATE) BETWEEN TO_DATE('".trim($args['dt_inicio'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_fim'])."','DD/MM/YYYY') " : "")."
                           ".(intval($args['cd_documento']) > 0 ? " AND pd.doc_jur_cd_documento = " . intval($args['cd_documento']) : "")."
                           ".(intval($args['cd_carta_precatoria']) > 0 ? " AND (pd.proc_nro_processo, 
                                                                                pd.proc_ano, 
                                                                                pd.proc_cd_local) IN (SELECT c.proc_nro_processo, 
                                                                                                             c.proc_ano, 
                                                                                                             c.proc_loc_jur_cd_local 
                                                                                                        FROM cartas_precatorias c
                                                                                                       WHERE c.carta_precatoria = ".intval($args['cd_carta_precatoria']).")" : "");
        #$this->db->query($qr_sql);
    }

    function adicionar_documento_aj(&$result, $args=array())
    {
        $qr_sql = "
              SELECT REPLACE(REPLACE(funcoes.remove_acento(UPPER(TRIM(nome_documento))), ' ', '_'), '/', '-') AS descricao
                FROM public.tipo_documentos 
               WHERE cd_tipo_doc = ".intval($args["cd_documento"])."
                ";

            $result = $this->db->query($qr_sql);
            $doc = $result->row_array();

        if(intval($args['cd_documento_protocolo_item']) == 0)
            {
                $cd_protocolo_item_new = $this->db->get_new_id("projetos.documento_protocolo_item","cd_documento_protocolo_item");
            }
            else
            {
                $cd_protocolo_item_new = intval($args['cd_documento_protocolo_item']);
            }

        #### PADRONIZA NOME DO ARQUIVO ####
            $dir = "../cieprev/up/protocolo_digitalizacao_".$args['cd_documento_protocolo'];

        if((trim($args['arquivo']) != "") and (file_exists($dir."/".trim($args['arquivo']))))
        {
          $ar_tmp = explode(".",trim($args['arquivo']));

          if(intval($args['cd_registro_empregado']) > 0)
          {
            $arq = $args['cd_empresa']."_".$args['cd_registro_empregado']."_".$args['seq_dependencia']."_".$args['cd_documento']."_".$doc["descricao"]."_".(trim($args['ds_processo']) != '' ? trim($args['ds_processo'].'_') : '').intval($cd_protocolo_item_new).".".$ar_tmp[count($ar_tmp)-1];
          
          }
          else
          {
            $arq = $args['cd_documento']."_".$doc["descricao"]."_".(trim($args['ds_processo']) != '' ? trim($args['ds_processo'].'_') : '').intval($cd_protocolo_item_new).".".$ar_tmp[count($ar_tmp)-1];
          
          }

          rename($dir."/".trim($args['arquivo']), $dir."/".$arq);
          $args['arquivo'] = $arq;
        } 

          if(intval($args['cd_documento_protocolo_item']) > 0)
            {
                $qr_sql = " 
                    UPDATE projetos.documento_protocolo_item 
                       SET cd_tipo_doc           = ".$args['cd_documento'].",
                       cd_documento_protocolo = ".intval($args['cd_documento_protocolo']).",
                           cd_empresa            = ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
                           cd_registro_empregado = ".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT").",
                           seq_dependencia       = ".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
                           nome_participante     = '".(trim($args['nome_participante']) != '' ? trim($args['nome_participante']) : "")."',
                           observacao            = '".utf8_decode($args['observacao'])."',
                           ds_processo           = '".(trim($args['ds_processo']) != '' ? trim($args['ds_processo']) : "")."',
                           nr_folha              = ".$args['nr_folha'].",
                           dt_cadastro           = CURRENT_TIMESTAMP,
                           cd_doc_juridico       = ".(trim($args['cd_documento']) != '' ? intval($args['cd_documento']) : "").",
                           cd_usuario_cadastro   = ".$args['cd_usuario_cadastro'].",
                           arquivo               = ".(trim($args['arquivo']) == "" ? "DEFAULT" : "funcoes.remove_acento('".$args['arquivo']."')").",
                           fl_descartar          = ".(trim($args['fl_descartar']) == "" ? "DEFAULT" : "'".$args['fl_descartar']."'").",
                           arquivo_nome          = ".(trim($args['arquivo_nome']) == "" ? "DEFAULT" : "'".utf8_decode($args['arquivo_nome'])."'").",
                           ds_caminho_liquid     = ".(((isset($args['ds_caminho_liquid'])) AND (trim($args['ds_caminho_liquid']) != '')) ? "'".utf8_decode(trim($args['ds_caminho_liquid']))."'" : "DEFAULT").",
                       ds_tempo_descarte = ".(trim($args['ds_tempo_descarte']) != '' ? str_escape($args['ds_tempo_descarte']) : "DEFAULT").",
    		               id_classificacao_info_doc = ".(trim($args['id_classificacao_info_doc']) != '' ? str_escape($args['id_classificacao_info_doc']) : "DEFAULT")."
                     WHERE cd_documento_protocolo_item = ".$args['cd_documento_protocolo_item'].";";
                
               # $this->db->query(utf8_decode($qr_sql));
            }
            else
            {

            $qr_sql = "
                INSERT INTO projetos.documento_protocolo_item
                          (
                            cd_documento_protocolo_item,
                            cd_documento_protocolo,
                            cd_tipo_doc,
                            cd_empresa,
                            cd_registro_empregado,
                            seq_dependencia,
                            nome_participante,
                            dt_cadastro,
                            cd_usuario_cadastro,
                            descricao,
                            fl_recebido,
                            observacao,
                            ds_processo,
                            nr_folha,
                            cd_doc_juridico,
                            fl_descartar,
                            arquivo,
                            arquivo_nome,
                            ds_caminho_liquid,
                            ds_tempo_descarte,
                            id_classificacao_info_doc
                         )
                VALUES
                (
                            ".$cd_protocolo_item_new.",
                     ".intval($args['cd_documento_protocolo']).",
                     ".intval($args['cd_tipo_doc']).",
                     ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
                     ".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT").",
                    ".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
                    '".(trim($args['nome_participante']) != '' ? trim($args['nome_participante']) : "")."',
                     CURRENT_TIMESTAMP,
                     ".intval($args['cd_usuario_cadastro']).",
                     NULL,
                     'N',
                     '".(trim($args['observacao']) != '' ? trim($args['observacao']) : "")."',
                     '".(trim($args['ds_processo']) != '' ? trim($args['ds_processo']) : "")."',
                     ".(trim($args['nr_folha']) != '' ? intval($args['nr_folha']) : "").",
                     ".(trim($args['cd_documento']) != '' ? intval($args['cd_documento']) : "").",
                     ".((!array_key_exists('fl_descartar', $args) OR trim($args['fl_descartar']) == "") ? "DEFAULT" : "'".$args['fl_descartar']."'").",
                     ".(trim($args['arquivo']) == "" ? "DEFAULT" : "funcoes.remove_acento('".$args['arquivo']."')").",
                     ".(trim($args['arquivo_nome']) == "" ? "DEFAULT" : "'".utf8_decode($args['arquivo_nome'])."'").",
                     ".(((isset($args['ds_caminho_liquid'])) AND (trim($args['ds_caminho_liquid']) != '')) ? "'".utf8_decode(trim($args['ds_caminho_liquid']))."'" : "DEFAULT").",
                     ".(trim($args['ds_tempo_descarte']) != '' ? str_escape($args['ds_tempo_descarte']) : "DEFAULT").",
                     ".(trim($args['id_classificacao_info_doc']) != '' ? str_escape($args['id_classificacao_info_doc']) : "DEFAULT")."
                );";

                }
        #echo "<PRE>$qr_sql</PRE>"; exit;
        $this->db->query($qr_sql);
    }

    function adicionar_documento_adm(&$result, $args=array())
    {
         if(intval($args['cd_documento_protocolo_item']) == 0)
           {
               $cd_protocolo_item_new = $this->db->get_new_id("projetos.documento_protocolo_item","cd_documento_protocolo_item");
           }
           else
           {
               $cd_protocolo_item_new = intval($args['cd_documento_protocolo_item']);
           }

           /*
           $qr_sql = "
             SELECT REPLACE(REPLACE(funcoes.remove_acento(UPPER(TRIM(nome_documento))), ' ', '_'), '/', '-') AS descricao
               FROM public.tipo_documentos 
              WHERE cd_tipo_doc = ".intval($args["cd_documento"])."
               ";

           $result = $this->db->query($qr_sql);
           $doc = $result->row_array();
         
       

           #### PADRONIZA NOME DO ARQUIVO ####
               

           if((trim($args['arquivo']) != "") and (file_exists($dir."/".trim($args['arquivo']))))
           {
             $ar_tmp = explode(".",trim($args['arquivo']));

             if(intval($args['cd_registro_empregado']) > 0)
             {
               $arq = $args['cd_empresa']."_".$args['cd_registro_empregado']."_".$args['seq_dependencia']."_".$args['cd_documento']."_".$doc["descricao"]."_".intval($cd_protocolo_item_new).".".$ar_tmp[count($ar_tmp)-1];
             
             }
             else
             {
               $arq = $args['cd_documento']."_".$doc["descricao"]."_".intval($cd_protocolo_item_new).".".$ar_tmp[count($ar_tmp)-1];
             
             }

             rename($dir."/".trim($args['arquivo']), $dir."/".$arq);
             $args['arquivo'] = $arq;
           } 
          */
           $dir = "../cieprev/up/protocolo_digitalizacao_".$args['cd_documento_protocolo'];
           if((trim($args['arquivo']) != "") and (file_exists($dir."/".trim($args['arquivo']))))
           {
            /*
             $qr_sql = "SELECT funcoes.remove_acento('".trim(utf8_decode($args['arquivo_nome']))."') AS arquivo";

             $row = $this->db->query($qr_sql)->row_array();

             rename($dir."/".trim($args['arquivo']), $dir."/".utf8_decode(trim($row['arquivo'])));
             $args['arquivo'] = trim($row['arquivo']);
             */

             $ar_tmp = explode(".",trim($args['arquivo']));

             if(intval($args['cd_documento']) == 309)
                {
                    $arq = $args['cd_empresa']."_".$args['cd_registro_empregado']."_".$args['seq_dependencia']."_".$args['cd_documento']."_".intval($cd_protocolo_item_new).".".$ar_tmp[count($ar_tmp)-1];
                }
                else
                {
                    $qr_sql = "SELECT funcoes.remove_acento('".trim(utf8_decode($args['arquivo_nome']))."') AS arquivo";

                    $row = $this->db->query($qr_sql)->row_array();

                    $arq = $row['arquivo'];
                }

                rename($dir."/".trim($args['arquivo']), $dir."/".utf8_decode(trim($arq)));
                $args['arquivo'] = trim($arq);
            }

             if(intval($args['cd_documento_protocolo_item']) > 0)
               {
                   $qr_sql = " 
                       UPDATE projetos.documento_protocolo_item 
                          SET cd_tipo_doc           = ".$args['cd_documento'].",
                              cd_empresa            = ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
                              cd_registro_empregado = ".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT").",
                              seq_dependencia       = ".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
                              observacao            = '".utf8_decode($args['observacao'])."',
                              nr_folha              = ".$args['nr_folha'].",
                              dt_cadastro           = CURRENT_TIMESTAMP,
                              cd_usuario_cadastro   = ".$args['cd_usuario_cadastro'].",
                              arquivo               = ".(trim($args['arquivo']) == "" ? "DEFAULT" : "funcoes.remove_acento('".utf8_decode($args['arquivo'])."')").",
                              fl_descartar          = ".((!array_key_exists('fl_descartar', $args) OR trim($args['fl_descartar']) == "") ? "DEFAULT" : "'".$args['fl_descartar']."'").",
                              arquivo_nome          = ".(trim($args['arquivo_nome']) == "" ? "DEFAULT" : "'".utf8_decode($args['arquivo_nome'])."'").",
                              banco                 = ".(((isset($args['banco'])) AND (trim($args['banco']) != '')) ? "'".utf8_decode(trim($args['banco']))."'" : "DEFAULT").",
                              caminho               = ".(((isset($args['caminho'])) AND (trim($args['caminho']) != '')) ? "'".utf8_decode(trim($args['caminho']))."'" : "DEFAULT").",
                              ds_caminho_liquid     = ".(((isset($args['ds_caminho_liquid'])) AND (trim($args['ds_caminho_liquid']) != '')) ? "'".utf8_decode(trim($args['ds_caminho_liquid']))."'" : "DEFAULT").",
                         
       		               ds_tempo_descarte = ".(trim($args['ds_tempo_descarte']) != '' ? str_escape($args['ds_tempo_descarte']) : "DEFAULT").",
                         id_classificacao_info_doc = ".(trim($args['id_classificacao_info_doc']) != '' ? str_escape($args['id_classificacao_info_doc']) : "DEFAULT")."
                        WHERE cd_documento_protocolo_item = ".$args['cd_documento_protocolo_item'].";";
                   
                   # $this->db->query(utf8_decode($qr_sql));
               }
               else
               {

                   $qr_sql = "
                       INSERT INTO projetos.documento_protocolo_item
                                 (
                                   cd_documento_protocolo_item,
                                   cd_documento_protocolo,
                                   cd_tipo_doc,
                                   cd_empresa,
                                   cd_registro_empregado,
                                   seq_dependencia,
                                   dt_cadastro,
                                   cd_usuario_cadastro,
                                   descricao,
                                   fl_recebido,
                                   observacao,
                                   ds_processo,
                                   nr_folha,
                                   cd_doc_juridico,
                                   arquivo,
                                   arquivo_nome,
                                   fl_descartar,
                                   banco,
                                   caminho,
                                   ds_caminho_liquid,
                                   ds_tempo_descarte,
                                   id_classificacao_info_doc
                                  )
                             VALUES 
                                  (
                                    ".$cd_protocolo_item_new.",
                                    ".intval($args['cd_documento_protocolo']).",
                                    ".intval($args['cd_documento']).",
                                    ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
                                    ".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT").",
                                    ".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
                                    CAST(timeofday() AS TIMESTAMP),
                                    ".intval($args['cd_usuario_cadastro']).",
                                    NULL,
                                    'N',
                                    ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".utf8_decode($args['observacao'])."'").",
                                    ".(trim($args['ds_processo']) == "" ? "DEFAULT" : "'".$args['ds_processo']."'").",
                                    ".intval($args['nr_folha']).",
                                    NULL,
                                    ".(trim($args['arquivo']) == "" ? "DEFAULT" : "funcoes.remove_acento('".utf8_decode($args['arquivo'])."')").",
                                    ".(trim($args['arquivo_nome']) == "" ? "DEFAULT" : "'".utf8_decode($args['arquivo_nome'])."'").",
                                    ".((!array_key_exists('fl_descartar', $args) OR trim($args['fl_descartar']) == "") ? "DEFAULT" : "'".$args['fl_descartar']."'").",
       			                 ".(((isset($args['banco'])) AND (trim($args['banco']) != '')) ? "'".utf8_decode(trim($args['banco']))."'" : "DEFAULT").",
       			                 ".(((isset($args['caminho'])) AND (trim($args['caminho']) != '')) ? "'".utf8_decode(trim($args['caminho']))."'" : "DEFAULT").",
       			                 ".(((isset($args['ds_caminho_liquid'])) AND (trim($args['ds_caminho_liquid']) != '')) ? "'".utf8_decode(trim($args['ds_caminho_liquid']))."'" : "DEFAULT").",
                             ".(trim($args['ds_tempo_descarte']) != '' ? str_escape($args['ds_tempo_descarte']) : "DEFAULT").",
       			                 ".(trim($args['id_classificacao_info_doc']) != '' ? str_escape($args['id_classificacao_info_doc']) : "DEFAULT")."
                                  );";
                   
               }
         #echo "<PRE>$qr_sql</PRE>"; exit;
         $this->db->query($qr_sql);
    }

    function adicionar_documento_padrao(&$result, $args=array())
    {
         if(intval($args['cd_documento_protocolo_item']) == 0)
           {
               $cd_protocolo_item_new = $this->db->get_new_id("projetos.documento_protocolo_item","cd_documento_protocolo_item");
           }
           else
           {
               $cd_protocolo_item_new = intval($args['cd_documento_protocolo_item']);
           }


           $dir = "../cieprev/up/protocolo_digitalizacao_".$args['cd_documento_protocolo'];
           if((trim($args['arquivo']) != "") and (file_exists($dir."/".trim($args['arquivo']))))
           {
             $qr_sql = "SELECT funcoes.remove_acento('".trim(utf8_decode($args['arquivo_nome']))."') AS arquivo";

             $row = $this->db->query($qr_sql)->row_array();

             rename($dir."/".trim($args['arquivo']), $dir."/".utf8_decode(trim($row['arquivo'])));
             $args['arquivo'] = trim($row['arquivo']);
           }

             if(intval($args['cd_documento_protocolo_item']) > 0)
               {
                   $qr_sql = " 
                       UPDATE projetos.documento_protocolo_item 
                          SET cd_tipo_doc           = ".$args['cd_documento'].",
                              cd_empresa            = ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
                              cd_registro_empregado = ".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT").",
                              seq_dependencia       = ".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
                              observacao            = '".utf8_decode($args['observacao'])."',
                              nr_folha              = ".$args['nr_folha'].",
                              dt_cadastro           = CURRENT_TIMESTAMP,
                              cd_usuario_cadastro   = ".$args['cd_usuario_cadastro'].",
                              arquivo               = ".(trim($args['arquivo']) == "" ? "DEFAULT" : "funcoes.remove_acento('".utf8_decode($args['arquivo'])."')").",
                              fl_descartar          = ".((!array_key_exists('fl_descartar', $args) OR trim($args['fl_descartar']) == "") ? "DEFAULT" : "'".$args['fl_descartar']."'").",
                              arquivo_nome          = ".(trim($args['arquivo_nome']) == "" ? "DEFAULT" : "'".utf8_decode($args['arquivo_nome'])."'").",
                              ds_caminho_liquid     = ".(((isset($args['ds_caminho_liquid'])) AND (trim($args['ds_caminho_liquid']) != '')) ? "'".utf8_decode(trim($args['ds_caminho_liquid']))."'" : "DEFAULT").",
                         ds_tempo_descarte = ".(trim($args['ds_tempo_descarte']) != '' ? str_escape($args['ds_tempo_descarte']) : "DEFAULT").",
       		               id_classificacao_info_doc = ".(trim($args['id_classificacao_info_doc']) != '' ? str_escape($args['id_classificacao_info_doc']) : "DEFAULT")."
                        WHERE cd_documento_protocolo_item = ".$args['cd_documento_protocolo_item'].";";
                   
                  # $this->db->query(utf8_decode($qr_sql));
               }
               else
               {

                   $qr_sql = "
                       INSERT INTO projetos.documento_protocolo_item
                                 (
                                   cd_documento_protocolo_item,
                                   cd_documento_protocolo,
                                   cd_tipo_doc,
                                   cd_empresa,
                                   cd_registro_empregado,
                                   seq_dependencia,
                                   dt_cadastro,
                                   cd_usuario_cadastro,
                                   descricao,
                                   fl_recebido,
                                   observacao,
                                   ds_processo,
                                   nr_folha,
                                   cd_doc_juridico,
                                   arquivo,
                                   arquivo_nome,
                                   fl_descartar,
                                   ds_caminho_liquid,
                                   ds_tempo_descarte,
                                   id_classificacao_info_doc
                                  )
                             VALUES 
                                  (
                                    ".$cd_protocolo_item_new.",
                                    ".intval($args['cd_documento_protocolo']).",
                                    ".intval($args['cd_documento']).",
                                    ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
                                    ".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT").",
                                    ".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
                                    CAST(timeofday() AS TIMESTAMP),
                                    ".intval($args['cd_usuario_cadastro']).",
                                    NULL,
                                    'N',
                                    ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".utf8_decode($args['observacao'])."'").",
                                    ".(trim($args['ds_processo']) == "" ? "DEFAULT" : "'".$args['ds_processo']."'").",
                                    ".intval($args['nr_folha']).",
                                    NULL,
                                    ".(trim($args['arquivo']) == "" ? "DEFAULT" : "funcoes.remove_acento('".utf8_decode($args['arquivo'])."')").",
                                    ".(trim($args['arquivo_nome']) == "" ? "DEFAULT" : "'".utf8_decode($args['arquivo_nome'])."'").",
                                    ".((!array_key_exists('fl_descartar', $args) OR trim($args['fl_descartar']) == "") ? "DEFAULT" : "'".$args['fl_descartar']."'").",
                                    ".(((isset($args['ds_caminho_liquid'])) AND (trim($args['ds_caminho_liquid']) != '')) ? "'".utf8_decode(trim($args['ds_caminho_liquid']))."'" : "DEFAULT").",
                                    ".(trim($args['ds_tempo_descarte']) != '' ? str_escape($args['ds_tempo_descarte']) : "DEFAULT").",
                                    ".(trim($args['id_classificacao_info_doc']) != '' ? str_escape($args['id_classificacao_info_doc']) : "DEFAULT")."
                                  );";
                   
               }
           #echo "<PRE>$qr_sql</PRE>"; exit;
               $this->db->query($qr_sql);
    }
        
    function adicionar_documento_juridico(&$result, $args=array())
    {
   		$qr_sql = "
   					SELECT REPLACE(REPLACE(funcoes.remove_acento(UPPER(TRIM(nome_documento))), ' ', '_'), '/', '-') AS descricao
   					  FROM public.tipo_documentos 
   					 WHERE cd_tipo_doc = ".intval($args["cd_documento"])."
   		        ";

           $result = $this->db->query($qr_sql);
           $doc = $result->row_array();

   		if(intval($args['cd_documento_protocolo_item']) == 0)
           {
               $cd_protocolo_item_new = $this->db->get_new_id("projetos.documento_protocolo_item","cd_documento_protocolo_item");
           }
           else
           {
               $cd_protocolo_item_new = intval($args['cd_documento_protocolo_item']);
           }

   		#### PADRONIZA NOME DO ARQUIVO ####
           $dir = "../cieprev/up/protocolo_digitalizacao_".$args['cd_documento_protocolo'];

   		if((trim($args['arquivo']) != "") and (file_exists($dir."/".trim($args['arquivo']))))
   		{
   			$ar_tmp = explode(".",trim($args['arquivo']));
   			$arq = $args['cd_empresa']."_".$args['cd_registro_empregado']."_".$args['seq_dependencia']."_".$args['cd_documento']."_".$doc["descricao"]."_".trim($args['ds_processo'])."_".intval($cd_protocolo_item_new).".".$ar_tmp[count($ar_tmp)-1];
   			rename($dir."/".trim($args['arquivo']), $dir."/".$arq);
   			$args['arquivo'] = $arq;
   		} 

         if(intval($args['cd_documento_protocolo_item']) > 0)
           {
               $qr_sql = " 
                   UPDATE projetos.documento_protocolo_item 
                      SET cd_tipo_doc           = ".$args['cd_documento'].",
                      cd_documento_protocolo = ".intval($args['cd_documento_protocolo']).",
                          cd_empresa            = ".$args['cd_empresa'].",
                          cd_registro_empregado = ".$args['cd_registro_empregado'].",
                          seq_dependencia       = ".$args['seq_dependencia'].",
                          observacao            = '".utf8_decode($args['observacao'])."',
                          ds_processo           = '".(trim($args['ds_processo']) != '' ? trim($args['ds_processo']) : "")."',
                          nr_folha              = ".$args['nr_folha'].",
                          dt_cadastro           = CURRENT_TIMESTAMP,
                          cd_doc_juridico       = ".(trim($args['cd_documento']) != '' ? intval($args['cd_documento']) : "").",
                          cd_usuario_cadastro   = ".$args['cd_usuario_cadastro'].",
                          arquivo               = ".(trim($args['arquivo']) == "" ? "DEFAULT" : "funcoes.remove_acento('".$args['arquivo']."')").",
                          fl_descartar          = ".(trim($args['fl_descartar']) == "" ? "DEFAULT" : "'".$args['fl_descartar']."'").",
                          arquivo_nome          = ".(trim($args['arquivo_nome']) == "" ? "DEFAULT" : "'".utf8_decode($args['arquivo_nome'])."'").",
                          ds_caminho_liquid     = ".(((isset($args['ds_caminho_liquid'])) AND (trim($args['ds_caminho_liquid']) != '')) ? "'".utf8_decode(trim($args['ds_caminho_liquid']))."'" : "DEFAULT").",
                     ds_tempo_descarte = ".(trim($args['ds_tempo_descarte']) != '' ? str_escape($args['ds_tempo_descarte']) : "DEFAULT").",
   		               id_classificacao_info_doc = ".(trim($args['id_classificacao_info_doc']) != '' ? str_escape($args['id_classificacao_info_doc']) : "DEFAULT")."
                    WHERE cd_documento_protocolo_item = ".$args['cd_documento_protocolo_item'].";";
               
              # $this->db->query(utf8_decode($qr_sql));
           }
           else
           {

               $qr_sql = "
                   INSERT INTO projetos.documento_protocolo_item
                         (
                           cd_documento_protocolo_item,
                           cd_documento_protocolo,
                           cd_tipo_doc,
                           cd_empresa,
                           cd_registro_empregado,
                           seq_dependencia,
                           dt_cadastro,
                           cd_usuario_cadastro,
                           descricao,
                           fl_recebido,
                           observacao,
                           ds_processo,
                           nr_folha,
                           cd_doc_juridico,
                           fl_descartar,
                           arquivo,
                           arquivo_nome,
                           ds_caminho_liquid,
                           ds_tempo_descarte,
                           id_classificacao_info_doc
                        )
                   VALUES
                        (
                           ".$cd_protocolo_item_new.",
                           ".intval($args['cd_documento_protocolo']).",
                           ".intval($args['cd_tipo_doc']).",
                           ".intval($args['cd_empresa']).",
                           ".intval($args['cd_registro_empregado']).",
                           ".intval($args['seq_dependencia']).",
                           CURRENT_TIMESTAMP,
                           ".intval($args['cd_usuario_cadastro']).",
                           NULL,
                           'N',
                           '".(trim($args['observacao']) != '' ? trim($args['observacao']) : "")."',
                           '".(trim($args['ds_processo']) != '' ? trim($args['ds_processo']) : "")."',
                           ".(trim($args['nr_folha']) != '' ? intval($args['nr_folha']) : "").",
                           ".(trim($args['cd_documento']) != '' ? intval($args['cd_documento']) : "").",
                           ".((!array_key_exists('fl_descartar', $args) OR trim($args['fl_descartar']) == "") ? "DEFAULT" : "'".$args['fl_descartar']."'").",
                           ".(trim($args['arquivo']) == "" ? "DEFAULT" : "'".$args['arquivo']."'").",
                           ".(trim($args['arquivo_nome']) == "" ? "DEFAULT" : "'".utf8_decode($args['arquivo_nome'])."'").",
                           ".(((isset($args['ds_caminho_liquid'])) AND (trim($args['ds_caminho_liquid']) != '')) ? "'".utf8_decode(trim($args['ds_caminho_liquid']))."'" : "DEFAULT").",
                           ".(trim($args['ds_tempo_descarte']) != '' ? str_escape($args['ds_tempo_descarte']) : "DEFAULT").",
                           ".(trim($args['id_classificacao_info_doc']) != '' ? str_escape($args['id_classificacao_info_doc']) : "DEFAULT")."
                   );";

           }
   		#echo "<PRE>$qr_sql</PRE>"; exit;
           $this->db->query($qr_sql);
    }
    
    function listar_documento_juridico(&$result, $args=array())
    {
        $qr_sql = "
            SELECT a.cd_usuario_cadastro, 
                   a.cd_documento_protocolo_item, 
                   a.cd_empresa, 
                   a.cd_registro_empregado, 
                   a.seq_dependencia, 
				   projetos.participante_nome(a.cd_empresa,a.cd_registro_empregado,a.seq_dependencia) AS nome_participante,
                   a.ds_processo, 
                   TO_CHAR(a.dt_cadastro,'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro,
                   a.observacao, 
				   a.ds_observacao_indexacao, 
                   a.nr_folha, 
                   a.cd_tipo_doc,
                   b.nome_documento as descricao_documento, 
                   c.nome as nome_usuario_cadastro, 
                   dp.dt_envio,
                   a.arquivo,
                   a.arquivo,
                   a.cd_documento_protocolo,
                   CASE WHEN a.fl_descartar = 'S' THEN 'Sim'
                        ELSE 'Não'
                   END AS fl_descartar,
                   a.ds_caminho_liquid
              FROM projetos.documento_protocolo_item a
              JOIN projetos.documento_protocolo dp 
                ON dp.cd_documento_protocolo=a.cd_documento_protocolo
              JOIN public.tipo_documentos b 
                ON a.cd_doc_juridico = b.cd_tipo_doc			
              JOIN projetos.usuarios_controledi c 
                ON a.cd_usuario_cadastro=c.codigo
             WHERE a.cd_documento_protocolo = ".intval($args['cd_documento_protocolo'])." 
               AND a.dt_exclusao IS NULL
               AND dp.dt_exclusao IS NULL
             ORDER BY a.dt_cadastro DESC";
        
        $result = $this->db->query($qr_sql);
    }

    function listar_documento_aj(&$result, $args=array())
    {
        $qr_sql = "
            SELECT a.cd_usuario_cadastro, 
                   a.cd_documento_protocolo_item, 
                   a.cd_empresa, 
                   a.cd_registro_empregado, 
                   a.seq_dependencia, 
           COALESCE(a.nome_participante, projetos.participante_nome(a.cd_empresa,a.cd_registro_empregado,a.seq_dependencia)) AS nome_participante,
                   a.ds_processo, 
                   TO_CHAR(a.dt_cadastro,'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro,
                   a.observacao, 
           a.ds_observacao_indexacao, 
                   a.nr_folha, 
                   a.cd_tipo_doc,
                   b.nome_documento as descricao_documento, 
                   c.nome as nome_usuario_cadastro, 
                   dp.dt_envio,
                   a.arquivo,
                   a.arquivo,
                   a.cd_documento_protocolo,
                   CASE WHEN a.fl_descartar = 'S' THEN 'Sim'
                        ELSE 'Não'
                   END AS fl_descartar,
                   a.ds_caminho_liquid
              FROM projetos.documento_protocolo_item a
              JOIN projetos.documento_protocolo dp 
                ON dp.cd_documento_protocolo=a.cd_documento_protocolo
              JOIN public.tipo_documentos b 
                ON a.cd_doc_juridico = b.cd_tipo_doc      
              JOIN projetos.usuarios_controledi c 
                ON a.cd_usuario_cadastro=c.codigo
             WHERE a.cd_documento_protocolo = ".intval($args['cd_documento_protocolo'])." 
               AND a.dt_exclusao IS NULL
               AND dp.dt_exclusao IS NULL
             ORDER BY a.dt_cadastro DESC";

        $result = $this->db->query($qr_sql);
    }

    function listar_documento(&$result, $args=array())
    {
        $qr_sql = "
                SELECT a.cd_usuario_cadastro, 
                       a.cd_documento_protocolo_item, 
					   a.cd_documento_protocolo,
                       a.cd_empresa, 
                       a.cd_registro_empregado, 
                       a.seq_dependencia,
					   projetos.participante_nome(a.cd_empresa,a.cd_registro_empregado,a.seq_dependencia) AS nome_participante,
                       a.ds_processo, 
                       to_char(a.dt_cadastro,'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro, 
                       a.observacao, 
					   a.ds_observacao_indexacao, 
                       a.nr_folha, 
                       a.arquivo,
                       a.arquivo_nome,
                       a.cd_tipo_doc,
                       COALESCE(b.nome_documento,'Não informado') AS descricao_documento, 
                       c.nome AS nome_usuario_cadastro, 
                       dp.dt_envio,
                       CASE WHEN a.fl_descartar = 'S' THEN 'Sim'
                            ELSE 'Não'
                       END AS fl_descartar,
                       a.ds_caminho_liquid
                  FROM projetos.documento_protocolo_item a
                  JOIN projetos.documento_protocolo dp 
                    ON dp.cd_documento_protocolo = a.cd_documento_protocolo
                  LEFT JOIN public.tipo_documentos b 
                    ON a.cd_tipo_doc = b.cd_tipo_doc
                  JOIN projetos.usuarios_controledi c 
                    ON a.cd_usuario_cadastro = c.codigo
                 WHERE a.cd_documento_protocolo = ".intval($args['cd_documento_protocolo'])." 
                   AND a.dt_exclusao            IS NULL
                   AND dp.dt_exclusao            IS NULL
				   ";
        
        $result = $this->db->query($qr_sql);
    }
    
    function listar_documento_atendimento(&$result, $args=array())
    {
        $qr_sql = "
                SELECT a.cd_usuario_cadastro, 
                       a.cd_documento_protocolo_item, 
                       a.cd_documento_protocolo,
                       a.cd_empresa, 
                       a.cd_registro_empregado, 
                       a.seq_dependencia, 
					   projetos.participante_nome(a.cd_empresa, a.cd_registro_empregado, a.seq_dependencia) AS nome, 
                       a.ds_processo, 
                       TO_CHAR(a.dt_cadastro,'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro, 
                       a.observacao, 
                       a.ds_observacao_indexacao, 
                       a.nr_folha, 
                       a.cd_tipo_doc,
                       a.arquivo,
                       a.arquivo_nome,								   
                       COALESCE(b.nome_documento,'Não informado') AS descricao_documento, 
                       c.nome AS nome_usuario_cadastro, 
                       dp.dt_envio,
                       CASE WHEN a.fl_descartar = 'S' THEN 'Sim'
                            ELSE 'Não'
                       END AS fl_descartar,
                       a.ds_caminho_liquid
                  FROM projetos.documento_protocolo_item a
                  JOIN projetos.documento_protocolo dp 
                    ON dp.cd_documento_protocolo=a.cd_documento_protocolo
                  LEFT JOIN public.tipo_documentos b 
                    ON a.cd_tipo_doc = b.cd_tipo_doc
                  JOIN projetos.usuarios_controledi c 
                    ON a.cd_usuario_cadastro=c.codigo
                 WHERE a.cd_documento_protocolo = ".intval($args['cd_documento_protocolo'])." 
                   AND a.dt_exclusao IS NULL
                   AND dp.dt_exclusao IS NULL
                 ORDER BY a.dt_cadastro DESC";
        
        $result = $this->db->query($qr_sql);
    }
	
	 function listar_documento_secretaria(&$result, $args=array())
    {
        $qr_sql = "
			SELECT a.cd_usuario_cadastro, 
				   a.cd_documento_protocolo_item, 
				   a.cd_documento_protocolo,
				   a.ds_processo, 
				   TO_CHAR(a.dt_cadastro,'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro, 
				   a.observacao, 
				   a.ds_observacao_indexacao, 
				   a.nr_folha, 
				   a.arquivo,
				   a.arquivo_nome,								   
				   c.nome AS nome_usuario_cadastro, 
				   dp.dt_envio,
				   CASE WHEN a.fl_descartar = 'S' THEN 'Sim'
						ELSE 'Não'
				   END AS fl_descartar,
				   a.banco,
				   a.caminho,
                   a.ds_caminho_liquid,
                   a.cd_tipo_doc,       
                   COALESCE(b.nome_documento,'Não informado') AS descricao_documento
			  FROM projetos.documento_protocolo_item a
			  JOIN projetos.documento_protocolo dp 
				ON dp.cd_documento_protocolo = a.cd_documento_protocolo
			  JOIN projetos.usuarios_controledi c 
				ON a.cd_usuario_cadastro = c.codigo
              LEFT JOIN public.tipo_documentos b 
                ON a.cd_tipo_doc = b.cd_tipo_doc
			 WHERE a.cd_documento_protocolo = ".intval($args['cd_documento_protocolo'])." 
			   AND a.dt_exclusao IS NULL
			   AND dp.dt_exclusao IS NULL
			 ORDER BY a.dt_cadastro DESC";
        
        $result = $this->db->query($qr_sql);
    }

    function listar_documento_adm_contrato(&$result, $args=array())
    {
        $qr_sql = "
            SELECT a.cd_usuario_cadastro, 
                   a.cd_documento_protocolo_item, 
                   a.cd_documento_protocolo,
                   a.ds_processo, 
                   TO_CHAR(a.dt_cadastro,'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro, 
                   a.observacao, 
                   a.ds_observacao_indexacao, 
                   a.nr_folha, 
                   a.arquivo,
                   a.arquivo_nome,                                 
                   c.nome AS nome_usuario_cadastro, 
                   dp.dt_envio,
                   CASE WHEN a.fl_descartar = 'S' THEN 'Sim'
                        ELSE 'Não'
                   END AS fl_descartar,
                   a.banco,
                   a.caminho,
                   a.ds_caminho_liquid,
                   a.nr_ano_contrato,
                   a.nr_id_contrato,
                   a.fl_tipo_protocolo_contrato,
                   (CASE WHEN a.fl_tipo_protocolo_contrato = 'S'
                         THEN 'Serviços'
                         WHEN a.fl_tipo_protocolo_contrato = 'O'
                         THEN 'Ordem de Fornecimento'
                         ELSE ''
                   END) AS ds_tipo_protocolo_contrato
              FROM projetos.documento_protocolo_item a
              JOIN projetos.documento_protocolo dp 
                ON dp.cd_documento_protocolo = a.cd_documento_protocolo
              JOIN projetos.usuarios_controledi c 
                ON a.cd_usuario_cadastro = c.codigo
             WHERE a.cd_documento_protocolo = ".intval($args['cd_documento_protocolo'])." 
               AND a.dt_exclusao IS NULL
               AND dp.dt_exclusao IS NULL
             ORDER BY a.dt_cadastro DESC";
        
        $result = $this->db->query($qr_sql);
    }
	
	 function listar_documento_controladoria(&$result, $args=array())
    {
        $qr_sql = "
			SELECT a.cd_usuario_cadastro, 
				   a.cd_documento_protocolo_item, 
				   a.cd_documento_protocolo,
				   a.ds_processo, 
				   TO_CHAR(a.dt_cadastro,'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro, 
				   a.observacao, 
				   a.ds_observacao_indexacao, 
				   a.nr_folha, 
				   a.arquivo,
				   a.arquivo_nome,								   
				   c.nome AS nome_usuario_cadastro, 
				   dp.dt_envio,
				   CASE WHEN a.fl_descartar = 'S' THEN 'Sim'
						ELSE 'Não'
				   END AS fl_descartar,
				   a.banco,
				   a.caminho,
                   a.ds_caminho_liquid,
                   a.cd_tipo_doc,       
                   COALESCE(b.nome_documento,'Não informado') AS descricao_documento
			  FROM projetos.documento_protocolo_item a
			  JOIN projetos.documento_protocolo dp 
				ON dp.cd_documento_protocolo = a.cd_documento_protocolo
			  JOIN projetos.usuarios_controledi c 
				ON a.cd_usuario_cadastro = c.codigo
              LEFT JOIN public.tipo_documentos b 
                ON a.cd_tipo_doc = b.cd_tipo_doc
			 WHERE a.cd_documento_protocolo = ".intval($args['cd_documento_protocolo'])." 
			   AND a.dt_exclusao IS NULL
			   AND dp.dt_exclusao IS NULL
			 ORDER BY a.dt_cadastro DESC";
        
        $result = $this->db->query($qr_sql);
    }
	
    function excluir_documento(&$result, $args=array())
    {
        $qr_sql = " 
            UPDATE projetos.documento_protocolo_item 
               SET dt_exclusao         = CURRENT_TIMESTAMP, 
                   cd_usuario_exclusao = " . intval($args['cd_usuario_exclusao']) . " 
             WHERE cd_documento_protocolo_item = ".intval($args['cd_documento_protocolo_item']) . ";";
        $this->db->query($qr_sql);
    }
    
    function editar_documento(&$result, $args=array())
    {
        $qr_sql = " 
            SELECT cd_documento_protocolo_item,
                   cd_tipo_doc,
                   cd_empresa, 
                   cd_registro_empregado, 
                   seq_dependencia,
                   observacao,
                   nr_folha,
                   arquivo,
                   arquivo_nome,
                   fl_descartar,
                   ds_processo,
                   cd_doc_juridico,
				   banco,
				   caminho,
                   nome_participante,
                   ds_caminho_liquid,
                   nr_ano_contrato,
                   nr_id_contrato,
                   fl_tipo_protocolo_contrato
              FROM projetos.documento_protocolo_item
             WHERE cd_documento_protocolo_item = " . intval($args['cd_documento_protocolo_item']) . ";";
        $result = $this->db->query($qr_sql);
    }
    
    function excluir_todos_documentos(&$result, $args=array())
    {
        $qr_sql = " 
                UPDATE projetos.documento_protocolo_item 
                   SET dt_exclusao = CURRENT_TIMESTAMP, 
                       cd_usuario_exclusao = ".intval($args['cd_usuario_exclusao'])." 
                 WHERE cd_documento_protocolo = ".intval($args['cd_documento_protocolo'])."; ";
        
        
        $this->db->query($qr_sql, $args);
    }
    
    function verifica_participante(&$result, $args=array())
    {
        $qr_sql = "
            SELECT COUNT(*) AS fl_participante
              FROM public.participantes
             WHERE cd_empresa            = " . $args['cd_empresa'] . "
               AND cd_registro_empregado = " . $args['cd_registro_empregado'] . "
               AND seq_dependencia       = " . $args['seq_dependencia'] . "
                              ";
        $result = $this->db->query($qr_sql);
    }
    
    function zip_docs(&$result, $args=array())
    {
        $qr_sql = "
					SELECT a.cd_usuario_cadastro, 
						   dp.cd_usuario_envio,
						   funcoes.get_usuario(dp.cd_usuario_envio) AS usuario_envio,
						   a.cd_documento_protocolo_item, 
						   a.cd_documento_protocolo,
						   a.cd_empresa, 
						   a.cd_registro_empregado, 
						   a.seq_dependencia, 
						   a.ds_processo, 
						   TO_CHAR(a.dt_cadastro,'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro, 
						   a.observacao, 
						   a.nr_folha, 
						   a.cd_tipo_doc,
						   funcoes.remove_acento(a.arquivo) AS arquivo,
						   funcoes.remove_acento(a.arquivo_nome) AS arquivo_nome,
						   b.nome_documento AS descricao_documento, 
						   c.nome AS nome_usuario_cadastro, 
						   dp.dt_envio,
						   dp.cd_gerencia_origem,
                           dp.ano,
                           dp.contador
					  FROM projetos.documento_protocolo_item a
					  JOIN projetos.documento_protocolo dp 
						ON dp.cd_documento_protocolo=a.cd_documento_protocolo
					  LEFT JOIN public.tipo_documentos b 
						ON a.cd_tipo_doc = b.cd_tipo_doc
					  JOIN projetos.usuarios_controledi c 
						ON a.cd_usuario_cadastro=c.codigo
					 WHERE a.cd_documento_protocolo = " . intval($args['cd_protocolo']) . " 
					   AND a.dt_exclusao IS NULL
					   AND dp.dt_exclusao IS NULL
					 ORDER BY a.dt_cadastro DESC
			           ";
            $result = $this->db->query($qr_sql);
    }
    
    function carrega_documento_protocolo(&$result, $args=array())
    {
        $qr_sql = "SELECT funcoes.nr_protocolo_digitalizacao(dp.ano,dp.contador) AS codigo,
                          dp.cd_documento_protocolo,
                          dp.dt_indexacao,
                          dp.tipo,
                          dp.dt_ok,
                          uc.nome,
						  dp.cd_gerencia_origem,
						  TO_CHAR(dp.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
                          dp.fl_contrato
                     FROM projetos.documento_protocolo dp
                     JOIN projetos.usuarios_controledi uc
                       ON uc.codigo = dp.cd_usuario_envio
                    WHERE dp.cd_documento_protocolo =  ".intval($args['cd_documento_protocolo']);
        $result = $this->db->query($qr_sql);
    }
    
    function lista_documento_receber(&$result, $args=array())
    {
        $qr_sql = "
			SELECT a.cd_documento_protocolo_item,
				   a.cd_documento_protocolo,
				   a.cd_tipo_doc, 
				   a.cd_empresa, 
				   a.cd_registro_empregado, 
				   a.seq_dependencia, 
 				   TO_CHAR(a.dt_cadastro, 'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro,
 				   a.cd_usuario_cadastro, 
           COALESCE(a.nome_participante, projetos.participante_nome(a.cd_empresa,a.cd_registro_empregado,a.seq_dependencia)) AS nome_participante,
 				   a.dt_exclusao, 
				   a.cd_usuario_exclusao, 
				   b.ano, 
				   b.contador,
				   c.nome as guerra_cadastro,
				   a.fl_recebido,
				   d.cd_tipo_doc,
				   d.nome_documento,
				   a.ds_processo,
				   a.cd_doc_juridico,
				   a.observacao,
				   TO_CHAR(a.dt_indexacao, 'DD/MM/YYYY') AS dt_indexacao,
				   TO_CHAR(b.dt_envio, 'DD/MM/YYYY') AS dt_envio,
				   a.ds_observacao_indexacao,
				   a.nr_folha,
				   a.motivo_devolucao,
				   TO_CHAR(a.dt_devolucao, 'DD/MM/YYYY') AS dt_devolucao,
				   a.cd_usuario_devolucao,
				   a.arquivo,
				   a.arquivo_nome,
				   a.fl_descartar,
				   a.banco,
				   a.caminho,
				   b.cd_gerencia_origem,
                   dj.nome_documento AS descricao_documento_juridico,
                   a.ds_caminho_liquid,
                   a.nr_ano_contrato,
                   a.nr_id_contrato,
                   a.fl_tipo_protocolo_contrato,
                   (CASE WHEN a.fl_tipo_protocolo_contrato = 'S'
                         THEN 'Serviços'
                         WHEN a.fl_tipo_protocolo_contrato = 'O'
                         THEN 'Ordem de Fornecimento'
                         ELSE ''
                   END) AS ds_tipo_protocolo_contrato,
				   a.id_classificacao_info_doc
			  FROM projetos.documento_protocolo_item a
			  JOIN projetos.documento_protocolo b 
			    ON a.cd_documento_protocolo = b.cd_documento_protocolo
			  LEFT JOIN public.tipo_documentos dj 
                ON a.cd_doc_juridico = dj.cd_tipo_doc
			  LEFT JOIN projetos.usuarios_controledi c
			    ON a.cd_usuario_cadastro = c.codigo
			  LEFT JOIN tipo_documentos d
			    ON a.cd_tipo_doc = d.cd_tipo_doc
			 WHERE a.dt_exclusao IS NULL
			   AND b.dt_exclusao IS NULL
			   AND a.cd_documento_protocolo = ".intval($args['cd_documento_protocolo']) ."
			 ORDER BY a.dt_cadastro ASC";
        #echo '<pre>'.$qr_sql.'<pre>';
        $result = $this->db->query($qr_sql);
    }
    
    function total_indexado(&$result, $args=array())
    {
        $qr_sql = "
					SELECT COUNT(*) AS quantos
					  FROM projetos.documento_protocolo_item 
		             WHERE cd_documento_protocolo = ".intval($args['cd_documento_protocolo']) ."
		               AND fl_recebido = 'S'
				  ";
   
        $result = $this->db->query($qr_sql);
    }
    
    function total_devolvidos(&$result, $args=array())
    {
        $qr_sql = "
					SELECT count(*) as quantos
					  FROM projetos.documento_protocolo_item 
					 WHERE cd_documento_protocolo = ".intval($args['cd_documento_protocolo']) ."
					   AND dt_devolucao IS NOT NULL
			      ";
        
        $result = $this->db->query($qr_sql);
    }
    
    function protocolo_ja_confirmado(&$result, $args=array())
    {
        $qr_sql = "
			SELECT COUNT(*) AS quantos
			  FROM projetos.documento_protocolo 
			 WHERE dt_exclusao  IS NULL 
			   AND dt_indexacao IS NOT NULL 
			   AND cd_documento_protocolo = ".intval($args['cd_documento_protocolo']);
        
        $result = $this->db->query($qr_sql);
    }
    
    function total_indexados_data(&$result, $args=array())
    {
        $qr_sql = "
					SELECT COUNT(*) AS quantos
		              FROM projetos.documento_protocolo_item 
		             WHERE dt_indexacao = TO_DATE( '".$args['dt_indexacao']."', 'DD/MM/YYYY' )";
        
        $result = $this->db->query($qr_sql);
    }
    
    function salva_documento_receber(&$result, $args=array())
    {
        $qr_sql = "UPDATE projetos.documento_protocolo_item
		      SET fl_recebido = '".trim($args['fl_recebido'])."',
			  dt_indexacao = ".(trim($args['dt_indexacao']) == "" ? "DEFAULT" : " TO_DATE('".trim($args['dt_indexacao'])."', 'DD/MM/YYYY')").",
			  dt_devolucao = ".(trim($args['dt_devolucao']) == "" ? "DEFAULT" : " TO_DATE('".trim($args['dt_devolucao'])."', 'DD/MM/YYYY')").",
			  ds_observacao_indexacao = ".(trim($args['observacao']) == "" ? "DEFAULT" : "  '".trim($args['observacao'])."'").",
                          motivo_devolucao = ".(trim($args['motivo']) == "" ? "DEFAULT" : " '".trim($args['motivo'])."'").",
                          cd_usuario_devolucao = ".(trim($args['dt_devolucao']) == "" ? "DEFAULT" : intval($args['cd_usuario'])) ."
		    WHERE cd_documento_protocolo_item = " . intval($args['cd_documento_protocolo_item']) . ";";

        $this->db->query($qr_sql);
    }
    
    function confirma_documento_receber(&$result, $args=array())
    {
        $qr_sql = "UPDATE projetos.documento_protocolo 
		      SET dt_ok = CURRENT_TIMESTAMP,
		          cd_usuario_ok = " .intval($args['cd_usuario']). "
		   WHERE cd_documento_protocolo = " . intval($args['cd_documento_protocolo']) . ";";
        
        $this->db->query($qr_sql);
    }
    
    function confirma_documento_indexar(&$result, $args=array())
    {
        $qr_sql = "UPDATE projetos.documento_protocolo 
		      SET dt_indexacao = CURRENT_TIMESTAMP,
		          cd_usuario_indexacao = " .intval($args['cd_usuario']). "
		   WHERE cd_documento_protocolo = " . intval($args['cd_documento_protocolo']) . ";";
        
        $this->db->query($qr_sql);
    }
    
    function lista_documento_indexar(&$result, $args=array())
    {
        $qr_sql = "SELECT a.cd_documento_protocolo_item,
                          a.cd_documento_protocolo,
                          a.cd_tipo_doc,
                          a.cd_empresa,
                          a.cd_registro_empregado, 
                          a.seq_dependencia, 
                          TO_CHAR(a.dt_cadastro, 'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro, 
                          a.cd_usuario_cadastro, 
                          a.dt_exclusao, 
                          a.cd_usuario_exclusao, 
                          b.ano,
                          b.contador, 
                          c.nome as guerra_cadastro,
                          a.fl_recebido,
                          d.cd_tipo_doc,
                          d.nome_documento,
                          a.ds_processo,
                          a.observacao,
                          TO_CHAR(a.dt_indexacao, 'DD/MM/YYYY') AS dt_indexacao,
                          a.ds_observacao_indexacao,
                          a.nr_folha,
                          a.motivo_devolucao,
                          TO_CHAR(a.dt_devolucao, 'DD/MM/YYYY') AS dt_devolucao,
                          a.cd_usuario_devolucao,
                          a.arquivo,
                          a.arquivo_nome,
                          a.fl_descartar,
						  a.cd_doc_juridico,
                          a.ds_caminho_liquid,
                          a.ds_tempo_descarte,
                          a.id_classificacao_info_doc
                     FROM projetos.documento_protocolo_item a
                     JOIN projetos.documento_protocolo b 
                       ON a.cd_documento_protocolo = b.cd_documento_protocolo
                     LEFT JOIN projetos.usuarios_controledi c
                       ON a.cd_usuario_cadastro = c.codigo
                     LEFT JOIN tipo_documentos d
                       ON a.cd_tipo_doc = d.cd_tipo_doc
                    WHERE a.dt_exclusao IS NULL
                      AND a.cd_documento_protocolo = ".intval($args['cd_documento_protocolo']) ."
                      AND a.dt_devolucao IS NULL
                    ORDER BY a.dt_cadastro ASC";
        #echo '<pre>'.$qr_sql.'<pre>';
        $result = $this->db->query($qr_sql);
    }
    
    function salva_documento_indexar(&$result, $args=array())
    {
        $qr_sql = "UPDATE projetos.documento_protocolo_item
		      SET fl_recebido = 'S',
			  dt_indexacao = ".(trim($args['dt_indexacao']) == "" ? "DEFAULT" : " TO_DATE('".trim($args['dt_indexacao'])."', 'DD/MM/YYYY')").",
			  ds_observacao_indexacao = ".(trim($args['ds_observacao']) == "" ? "DEFAULT" : "  '".trim($args['ds_observacao'])."'").",
                          motivo_devolucao = ''
		    WHERE cd_documento_protocolo_item = " . intval($args['cd_documento_protocolo_item']) . ";";
        
        $this->db->query($qr_sql);
    }
	
	 function gc_banco_autocomplete(&$result, $args=array())
	 {
		$qr_sql = "
					SELECT DISTINCT dpi.banco
					  FROM projetos.documento_protocolo dp
					  JOIN projetos.documento_protocolo_item dpi
						ON dpi.cd_documento_protocolo = dp.cd_documento_protocolo
					 WHERE dp.cd_gerencia_origem = 'GC'
					   AND dp.dt_exclusao IS NULL
					   AND dpi.dt_exclusao IS NULL
					   AND funcoes.remove_acento(UPPER(dpi.banco)) LIKE funcoes.remove_acento(UPPER('%".str_replace(" ","%",$args['banco'])."%'))
					 ORDER BY dpi.banco
					 LIMIT 5
  	              ";
		$result = $this->db->query($qr_sql);
	 }	
	
	 function gc_caminho_autocomplete(&$result, $args=array())
	 {
		$qr_sql = "
					SELECT DISTINCT dpi.caminho
					  FROM projetos.documento_protocolo dp
					  JOIN projetos.documento_protocolo_item dpi
						ON dpi.cd_documento_protocolo = dp.cd_documento_protocolo
					 WHERE dp.cd_gerencia_origem = 'GC'
					   AND dp.dt_exclusao IS NULL
					   AND dpi.dt_exclusao IS NULL
					   AND funcoes.remove_acento(UPPER(dpi.caminho)) LIKE funcoes.remove_acento(UPPER('%".str_replace(" ","%",$args['caminho'])."%'))
					 ORDER BY dpi.caminho
					 LIMIT 5
  	              ";
		$result = $this->db->query($qr_sql);
	 }	
	
	 function devolver_protocolo(&$result, $args=array())
	 {
		$qr_sql = "
			UPDATE projetos.documento_protocolo
			   SET dt_envio         = NULL,
			       cd_usuario_envio = NULL
			 WHERE cd_documento_protocolo = ".intval($args['cd_documento_protocolo']).";";
			 
		$result = $this->db->query($qr_sql);
	 }
	
    function financeiro_resgate_protocolo(&$result, $args=array())
    {
   		$qr_sql = "
   					SELECT p.nome,
   						   p.cd_empresa,
   						   p.cd_registro_empregado,
   						   p.seq_dependencia,
   						   a.cpf, 
   						   TO_CHAR(a.dt_pagamento,'DD/MM/YYYY') AS dt_pagamento,
   						   a.nr_tit,
   						   a.nr_ano,       
   						   a.vl_liquido
   					  FROM oracle.financeiro_resgate_protocolo(TO_DATE('".$args["dt_resgate_ini"]."','DD/MM/YYYY'), TO_DATE('".$args["dt_resgate_fim"]."','DD/MM/YYYY')) a
   					  JOIN public.participantes p
   						ON funcoes.format_cpf(p.cpf_mf) = a.cpf  	
   					 ORDER BY a.dt_pagamento, a.nr_ano, a.nr_tit  
   		          ";
   		#echo "<PRE> $qr_sql </PRE>"; exit;
   		$result = $this->db->query($qr_sql);
    }	
	
	function get_info_item($cd_documento_protocolo)
	{
		$qr_sql = "
			SELECT observacao,
                   ds_caminho_liquid
			  FROM projetos.documento_protocolo_item  
			 WHERE cd_documento_protocolo = $cd_documento_protocolo 
			   AND dt_exclusao IS NULL 
			   AND observacao IS NOT NULL;";			
				   
		return $this->db->query($qr_sql)->result_array();		
	}

    public function listar_documento_indexado($cd_documento_protocolo)
    {
        $qr_sql = "
            SELECT a.cd_usuario_cadastro, 
                   a.cd_documento_protocolo_item, 
                   a.cd_documento_protocolo,
                   a.cd_empresa, 
                   a.cd_registro_empregado, 
                   a.seq_dependencia,
                   projetos.participante_nome(a.cd_empresa,a.cd_registro_empregado,a.seq_dependencia) AS nome_participante,
                   a.ds_processo, 
                   TO_CHAR(a.dt_cadastro, 'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro, 
                   TO_CHAR(a.dt_exclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao, 
                   a.observacao, 
                   a.ds_observacao_indexacao, 
                   a.nr_folha, 
                   a.arquivo,
                   a.arquivo_nome,
                   a.cd_tipo_doc,
                   COALESCE(b.nome_documento,'Não informado') AS descricao_documento, 
                   funcoes.get_usuario_nome(a.cd_usuario_exclusao) AS ds_usuario_exclusao,
                   dp.dt_envio,
                   CASE WHEN a.fl_descartar = 'S' THEN 'Sim'
                        ELSE 'Não'
                   END AS fl_descartar,
                   a.ds_caminho_liquid,
                   a.caminho,
                   TO_CHAR(a.dt_indexacao, 'DD/MM/YYYY') AS dt_indexacao,
                   TO_CHAR(a.dt_devolucao, 'DD/MM/YYYY HH24:MI') AS dt_devolucao,
                   a.motivo_devolucao,
                   d.nome_documento
              FROM projetos.documento_protocolo_item a
              JOIN projetos.documento_protocolo dp 
                ON dp.cd_documento_protocolo = a.cd_documento_protocolo
              LEFT JOIN public.tipo_documentos b 
                ON a.cd_tipo_doc = b.cd_tipo_doc
              LEFT JOIN public.tipo_documentos d 
                ON b.cd_tipo_doc = d.cd_tipo_doc
             WHERE a.cd_documento_protocolo = ".intval($cd_documento_protocolo).";";
        
        return $this->db->query($qr_sql)->result_array();
    }

    public function alterar_observacao_indexacao($cd_documento_protocolo_item, $ds_observacao_indexacao, $cd_usuario)
    {
        $qr_sql = " 
            UPDATE projetos.documento_protocolo_item 
               SET ds_observacao_indexacao = ".(trim($ds_observacao_indexacao) != '' ? "'".trim($ds_observacao_indexacao)."'" : "DEFAULT")."
             WHERE cd_documento_protocolo_item = ".intval($cd_documento_protocolo_item) . ";";

        $this->db->query($qr_sql);
    }
}
?>