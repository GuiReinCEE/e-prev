<?php

class Documento_recebido_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
	
	function listar(&$result, $args = array())
    {
        $qr_sql = "
			SELECT DISTINCT funcoes.nr_documento_recebido(dr.nr_ano, dr.nr_contador) AS nr_documento_recebido,
				   TO_CHAR(dr.dt_cadastro, 'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro,
				   usuario_cadastro.divisao || '-' || usuario_cadastro.guerra AS nome_usuario_cadastro,
				   TO_CHAR(dr.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   usuario_envio.divisao || '-' || usuario_envio.guerra AS nome_usuario_envio,
				   usuario_destino.divisao || '-' || usuario_destino.guerra AS nome_usuario_destino,
				   TO_CHAR(dr.dt_redirecionamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_redirecionamento,
				   CASE WHEN dr.dt_envio IS NULL THEN 'Aguardando Envio'
						WHEN dr.dt_ok IS NULL THEN 'Aguardando Recebimento'
						WHEN dr.dt_ok IS NOT NULL THEN 'Encerrado'
						ELSE 'Aguardando'
				   END AS status,
				   CASE WHEN dr.dt_envio IS NULL THEN 'AE'
						WHEN dr.dt_ok IS NULL THEN 'AR'
						WHEN dr.dt_ok IS NOT NULL THEN 'EN'
						ELSE 'AG'
				   END AS cd_status,						   
				   dr.cd_documento_recebido,
				   dr.cd_usuario_destino,
				   TO_CHAR( dr.dt_ok, 'DD/MM/YYYY' ) AS dt_ok,
				   usuario_ok.divisao AS divisao_usuario_ok,
				   usuario_ok.guerra AS guerra_usuario_ok,
				   grupo.ds_nome AS grupo_destino_nome,
				   dr.cd_usuario_cadastro,
				   drts.ds_documento_recebido_tipo_solic,
				   dr.cd_documento_recebido_grupo,
				   (SELECT COUNT(*) 
					  FROM projetos.documento_recebido_item dri2
					 WHERE dri2.dt_exclusao IS NULL
					   AND dr.cd_documento_recebido = dri2.cd_documento_recebido) AS tl_documentos,
				   (SELECT COUNT(*) 
					  FROM projetos.documento_recebido_item dri3
					 WHERE dri3.dt_exclusao IS NULL
					   AND dri3.dt_recebimento IS NOT NULL
					   AND dr.cd_documento_recebido = dri3.cd_documento_recebido) AS tl_documentos_receb,
				   (SELECT COUNT(*) 
					  FROM projetos.documento_recebido_item dri4
					 WHERE dri4.dt_exclusao IS NULL
					   AND (
					   		TRIM(dri4.ds_observacao) IS NOT NULL AND TRIM(dri4.ds_observacao) != ''
					   		OR
					   		TRIM(dri4.ds_observacao_recebimento) IS NOT NULL AND TRIM(dri4.ds_observacao_recebimento) != ''
					   )
					   AND dr.cd_documento_recebido = dri4.cd_documento_recebido) AS tl_documentos_obs
			 FROM projetos.documento_recebido dr
			 LEFT JOIN projetos.documento_recebido_item dri
			   ON dr.cd_documento_recebido = dri.cd_documento_recebido
			 LEFT JOIN projetos.usuarios_controledi usuario_cadastro
			   ON usuario_cadastro.codigo = dr.cd_usuario_cadastro
			 LEFT JOIN projetos.usuarios_controledi usuario_envio
			   ON usuario_envio.codigo = dr.cd_usuario_envio
			 LEFT JOIN projetos.usuarios_controledi usuario_ok
			   ON usuario_ok.codigo = dr.cd_usuario_ok
			 LEFT JOIN projetos.usuarios_controledi usuario_destino
			   ON usuario_destino.codigo = dr.cd_usuario_destino
			 LEFT JOIN projetos.documento_recebido_grupo grupo
			   ON dr.cd_documento_recebido_grupo = grupo.cd_documento_recebido_grupo 
			  AND grupo.dt_exclusao IS NULL
			 LEFT JOIN projetos.documento_recebido_grupo_usuario grupo_usuario
			   ON grupo_usuario.cd_documento_recebido_grupo = grupo.cd_documento_recebido_grupo 
			  AND grupo_usuario.dt_exclusao IS NULL
			 lEFT JOIN projetos.documento_recebido_tipo_solic drts
			   ON drts.cd_documento_recebido_tipo_solic = dr.cd_documento_recebido_tipo_solic
			  
			WHERE " . intval($args["cd_usuario"]) . "  IN (dr.cd_usuario_cadastro, dr.cd_usuario_ok, dr.cd_usuario_destino, grupo_usuario.cd_usuario)
			  ".(((trim($args['dt_cadastro_ini']) != "") and (trim($args['dt_cadastro_fim']) != "")) ? "AND CAST(dr.dt_cadastro AS DATE) BETWEEN TO_DATE('" . trim($args['dt_cadastro_ini']) . "','DD/MM/YYYY') AND TO_DATE('" . trim($args['dt_cadastro_fim']) . "','DD/MM/YYYY')" : "")."
			  ".(trim($args['fl_mostrar_documentos']) == 'N' ? "AND dri.cd_tipo_doc NOT IN (471, 51)" : '')."
			  " . (trim($args['cd_status']) == "AG" ? "AND (dr.dt_envio IS NULL OR dr.dt_ok IS NULL)" : "") . "
			  " . (trim($args['cd_status']) == "AE" ? "AND dr.dt_envio IS NULL" : "") . "
			  " . (trim($args['cd_status']) == "AR" ? "AND dr.dt_envio IS NOT NULL AND dr.dt_ok IS NULL" : "") . "
			  " . (trim($args['cd_status']) == "EN" ? "AND dr.dt_ok IS NOT NULL" : "") . "
			  ".(((intval($args['nr_ano']) > 0) and (intval($args['nr_contador']) > 0)) ? " AND dr.nr_ano = ".intval($args['nr_ano'])." AND dr.nr_contador = ".intval($args['nr_contador']) : "")."
			  ".(trim($args['cd_gerencia_remetente']) != '' ? "AND usuario_envio.divisao = '".trim($args['cd_gerencia_remetente'])."'" : "")."
			  " . (trim($args['tipo_solicitacao']) != "" ? "AND dr.cd_documento_recebido_tipo_solic ='".trim($args['tipo_solicitacao'])."'" : "")."
			  ".(trim($args['cd_usuario_destino']) != '' ? "AND (usuario_destino.codigo = ".intval($args['cd_usuario_destino'])." OR ".intval($args['cd_usuario_destino'])." IN ( SELECT drgu.cd_usuario 
																																						                            FROM projetos.documento_recebido_grupo_usuario  drgu
																																						                           WHERE drgu.dt_exclusao IS NULL
																																											         AND drgu.cd_documento_recebido_grupo = grupo.cd_documento_recebido_grupo ))" : "")."
			  ;";
        #echo "<pre style='text-align:left;'>".print_r($args,true).$qr_sql."</pre>"; #exit;	

		

        $result = $this->db->query($qr_sql);
    }
	
	function tipo_doc(&$result, $args = array())
    {
        $qr_sql = "
            SELECT cd_tipo_doc AS value, 
                   nome_documento AS text 
              FROM public.tipo_documentos 
             ORDER BY nome_documento";
        
        $result = $this->db->query($qr_sql);
    }
	
	function usuario_envio(&$result, $args = array())
    {
        $qr_sql = "
            SELECT codigo AS value, 
                   nome || '(' || divisao || ')' AS text 
              FROM projetos.usuarios_controledi a 
              JOIN projetos.documento_recebido b 
                ON a.codigo = b.cd_usuario_envio 
             ORDER BY nome";
        
        $result = $this->db->query($qr_sql);
    }
	
	function usuario_destino(&$result, $args = array())
    {
        $qr_sql = "
            SELECT codigo AS value, 
                   nome || '(' || divisao || ')' AS text 
              FROM projetos.usuarios_controledi a 
              JOIN projetos.documento_recebido b 
                ON a.codigo=b.cd_usuario_destino 
             ORDER BY nome";
        
        $result = $this->db->query($qr_sql);
    }
	
	function gerencia(&$result, $args = array())
    {
        $qr_sql = "
            SELECT codigo AS value, 
                   nome AS text 
              FROM projetos.divisoes 
			 WHERE tipo = 'DIV'
             ORDER BY nome;";
        
        $result = $this->db->query($qr_sql);
    }
    
    function usuario_encerrado(&$result, $args = array())
    {
        $qr_sql = "
            SELECT uc.codigo AS value,
                   uc.nome AS text 
              FROM projetos.usuarios_controledi uc 
              LEFT JOIN projetos.documento_recebido_item dri
                ON uc.codigo = dri.cd_usuario_recebimento
             WHERE dri.dt_recebimento IS NOT NULL
               AND dri.dt_exclusao IS NULL
             GROUP BY uc.codigo, uc.nome
             ORDER BY uc.nome";
        
        $result = $this->db->query($qr_sql);
    }

    function relatorio(&$result, $args = array())
    {
        $qr_sql = "
            SELECT DISTINCT a.nr_ano,
                   a.nr_contador,
                   funcoes.nr_documento_recebido(a.nr_ano, a.nr_contador) AS nr_documento_recebido,
                   a.cd_documento_recebido,
                   a.cd_usuario_cadastro,
                   TO_CHAR(a.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
                   a.cd_usuario_envio,
                   TO_CHAR(a.dt_ok, 'DD/MM/YYYY HH24:MI:SS') AS dt_ok,
                   a.cd_usuario_ok,
                   TO_CHAR(a.dt_redirecionamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_redirecionamento,
                   b.cd_tipo_doc,
                   b.cd_empresa,
                   b.cd_registro_empregado,
                   b.seq_dependencia,
                   b.nr_folha,
                   COALESCE(c.nome, b.nome) AS nome_participante,
                   d.nome_documento,
                   e.divisao AS divisao_usuario_destino,
                   e.guerra AS guerra_usuario_destino,
                   b.arquivo,
                   b.arquivo_nome,
                   f.ds_nome AS nome_grupo,
                   b.ds_observacao_recebimento,
                   h.nome AS usuario_encerrado,
                   (SELECT TO_CHAR(dv.dt_inclusao,'DD/MM/YYYY HH24:MI:SS')
				      FROM projetos.documento_recebido_devolucao dv
					 WHERE dv.cd_documento_recebido = a.cd_documento_recebido
					   AND dv.dt_exclusao IS NULL
					 ORDER BY dv.dt_inclusao DESC
					 LIMIT 1)
              FROM projetos.documento_recebido a
			  LEFT JOIN projetos.usuarios_controledi uv
			    ON uv.codigo = a.cd_usuario_cadastro
              JOIN projetos.documento_recebido_item b
                ON a.cd_documento_recebido = b.cd_documento_recebido
              LEFT JOIN public.participantes c
                ON b.cd_empresa            = c.cd_empresa 
               AND b.cd_registro_empregado = c.cd_registro_empregado 
               AND b.seq_dependencia       = c.seq_dependencia
              LEFT JOIN public.tipo_documentos d
                ON b.cd_tipo_doc = d.cd_tipo_doc
              LEFT JOIN projetos.usuarios_controledi e
                ON e.codigo = a.cd_usuario_destino
              LEFT JOIN projetos.documento_recebido_grupo f 
                ON f.cd_documento_recebido_grupo = a.cd_documento_recebido_grupo
              LEFT JOIN projetos.documento_recebido_grupo_usuario g 
                ON g.cd_documento_recebido_grupo = f.cd_documento_recebido_grupo
              LEFT JOIN projetos.usuarios_controledi h
                ON b.cd_usuario_recebimento = h.codigo

             WHERE b.dt_exclusao IS NULL
			 ".(trim($args['cd_gerencia_remetente']) != '' ? "AND uv.divisao = '".trim($args['cd_gerencia_remetente'])."'" : "")."
			 ".(trim($args['cd_usuario_destino']) != '' ? "AND (e.codigo = ".intval($args['cd_usuario_destino'])." OR ".intval($args['cd_usuario_destino'])." IN  ( 
			 		SELECT drgu.cd_usuario 
				      FROM projetos.documento_recebido_grupo_usuario  drgu
					 WHERE drgu.dt_exclusao IS NULL
					   AND drgu.cd_documento_recebido_grupo = f.cd_documento_recebido_grupo ))" : "")."
			 ".(trim($args['fl_encerrado']) == 'S' ? "AND a.dt_ok IS NOT NULL" : '')."
			 ".(trim($args['fl_encerrado']) == 'N' ? "AND a.dt_ok IS NULL" : '')."
			 ".(trim($args['fl_enviado']) == 'S' ? "AND a.dt_envio IS NOT NULL" : '')."
			 ".(trim($args['fl_enviado']) == 'N' ? "AND a.dt_envio IS NULL" : '')."
			 ".(trim($args['fl_mostrar_documentos']) == 'N' ? "AND b.cd_tipo_doc NOT IN (471, 51)" : '')."
			 ".(trim($args['ano']) != '' ? "AND a.nr_ano = ".intval($args['ano']) : '')."
			 ".(trim($args['contador']) != '' ? "AND a.nr_contador = ".intval($args['contador']) : '')."
			 ".(trim($args['cd_empresa']) != '' ? "AND b.cd_empresa = ".intval($args['cd_empresa']) : '')."
			 ".(trim($args['cd_registro_empregado']) != '' ? "AND b.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : '')."
			 ".(trim($args['seq_dependencia']) != '' ? "AND b.seq_dependencia = ".intval($args['seq_dependencia']) : '')."
			 ".(trim($args['cd_tipo_doc']) != '' ? "AND ".(is_numeric($args['cd_tipo_doc']) ? "b.cd_tipo_doc = ".intval($args['cd_tipo_doc']) : "UPPER(d.nome_documento) LIKE UPPER('%" . $db->escape($args['cd_tipo_doc']) . "%')") : '')."
			 ".(((trim($args['dt_envio_inicio']) != "") and (trim($args['dt_envio_fim']) != "")) ? "AND DATE_TRUNC('day', a.dt_envio) BETWEEN TO_DATE('".$args['dt_envio_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY') " : "")."
			 ".(((trim($args['dt_ok_inicio']) != "") and (trim($args['dt_ok_fim']) != "")) ? "AND DATE_TRUNC('day', a.dt_ok) BETWEEN TO_DATE('".$args['dt_ok_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_ok_fim']."', 'DD/MM/YYYY')" : "")."
			 ".(trim($args['cd_usuario_envio']) != '' ? "AND a.cd_usuario_cadastro = ".intval($args['cd_usuario_envio']) : '')."
			 ".(trim($args['cd_usuario_destino']) != '' ? "AND a.cd_usuario_ok = ".intval($args['cd_usuario_destino']) : '')."
			 ".(trim($args['cd_gerencia_destino']) != '' ? "AND e.divisao  = '".trim($args['cd_gerencia_destino'])."'" : '')."
			 ".(trim($args['nome']) != '' ? "AND UPPER(b.nome) LIKE UPPER('%" . $this->db->escape_str($args['nome']) . "%')" : '')."
			 " . (trim($args['tipo_solicitacao']) != "" ? "AND a.cd_documento_recebido_tipo_solic ='".trim($args['tipo_solicitacao'])."'" : "")."
			 ".(((trim($args['dt_devolucao_ini']) != "") and (trim($args['dt_devolucao_fim']) != "")) ? "AND DATE_TRUNC('day', 
			 	   (SELECT dv.dt_inclusao
				      FROM projetos.documento_recebido_devolucao dv
					 WHERE dv.cd_documento_recebido = a.cd_documento_recebido
					   AND dv.dt_exclusao IS NULL
					 ORDER BY dv.dt_inclusao DESC
					 LIMIT 1)) BETWEEN TO_DATE('".$args['dt_devolucao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_devolucao_fim']."', 'DD/MM/YYYY') " : "")."
			 ;";

		$result = $this->db->query($qr_sql);
    }
	
	function recebido_beneficio(&$result, $args = array())
    {
        $qr_sql = "
            SELECT cd_documento_recebido_beneficio AS value,
                   ds_documento_recebido_beneficio AS text
              FROM projetos.documento_recebido_beneficio
             WHERE dt_exclusao IS NULL";

        $result = $this->db->query($qr_sql);
    }
	
	function tabela_beneficio(&$result, $args = array())
    {
        $qr_sql = "
            SELECT cd_documento,
                   nome_documento
              FROM projetos.documento_recebido_beneficio_item doc_re
              JOIN public.tipo_documentos td
                ON td.cd_tipo_doc = doc_re.cd_documento
             WHERE doc_re.dt_exclusao IS NULL
               AND doc_re.cd_documento_recebido_beneficio = ".intval($args['beneficio']).";";

        $result = $this->db->query($qr_sql);
    }
	
	function tabela_inscricao(&$result, $args = array())
    {
        $qr_sql = "
            SELECT cd_documento,
                   nome_documento
              FROM projetos.documento_recebido_inscricao doc_rec
              JOIN public.tipo_documentos td
                ON td.cd_tipo_doc = doc_rec.cd_documento
             WHERE doc_rec.cd_empresa = ".intval($args['cd_plano_empresa']) . "
               AND doc_rec.cd_plano = ".intval($args['cd_plano']).";";

        $result = $this->db->query($qr_sql);
    }
	
	function editar_documento(&$result, $args = array())
    {
        $qr_sql = " 
			SELECT cd_documento_recebido_item,
				   cd_tipo_doc,
				   cd_empresa, 
				   cd_registro_empregado, 
				   seq_dependencia,
				   ds_observacao,
				   nr_folha,
				   arquivo,
				   arquivo_nome,
				   nome
			  FROM projetos.documento_recebido_item
			 WHERE cd_documento_recebido_item = ".intval($args['cd_documento_recebido_item']).";";
			 
        $result = $this->db->query($qr_sql);
    }
	
	function excluir_item(&$result, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.documento_recebido_item 
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
		     WHERE cd_documento_recebido_item = ".intval($args['cd_documento_recebido_item']).";";
			
		$result = $this->db->query($qr_sql);
	}
	
	function excluir(&$result, $args = array())
    {
        $qr_sql = "
		   DELETE
			 FROM projetos.documento_recebido_devolucao
			WHERE cd_documento_recebido = ".intval($args['cd_documento_recebido']).";
		   DELETE
			 FROM projetos.documento_recebido_historico
			WHERE cd_documento_recebido = ".intval($args['cd_documento_recebido']).";
		   DELETE 
			 FROM projetos.documento_recebido_item
			WHERE cd_documento_recebido = ".intval($args['cd_documento_recebido']).";
		   DELETE
			 FROM projetos.documento_recebido
			WHERE cd_documento_recebido = ".intval($args['cd_documento_recebido']).";";
			
        $this->db->query($qr_sql);
    }
	
	function listar_documentos_item(&$result, $args = array())
    {
        $qr_sql = "
			SELECT cd_documento_recebido, 
				   cd_documento_recebido_item, 
				   cd_empresa, 
				   cd_registro_empregado, 
				   seq_dependencia, 
				   nome,
				   cd_tipo_doc,  
				   TO_CHAR(dt_cadastro, 'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro,
				   arquivo,  
				   arquivo_nome,
				   ds_observacao,
				   nr_folha,
				   nr_folha_pdf
			  FROM projetos.documento_recebido_item
			 WHERE cd_documento_recebido_item IN (" . $args["ar_proto_selecionado"] . ");";

        $result = $this->db->query($qr_sql);
    }

	function receber_documento(&$result, $args = array())
    {
		$qr_sql = "
					UPDATE projetos.documento_recebido_item
					   SET dt_recebimento             = CURRENT_TIMESTAMP, 
						   cd_usuario_recebimento     = ".intval($args['cd_usuario'])."
					 WHERE cd_documento_recebido_item = ".intval($args['cd_documento_recebido_item'])."
			      ";
		#echo "<PRE>".$qr_sql."</PRE>"; exit;
        $result = $this->db->query($qr_sql);
    }
	
	function get_documento_recebido(&$result, $args = array())
    {
		$qr_sql = "
					SELECT TO_CHAR(dri.dt_recebimento,'DD/MM/YYYY HH24:MI:SS') AS dt_recebimento,
					       dri.cd_usuario_recebimento,
						   uc.nome,
						   uc.guerra,
						   uc.divisao
					  FROM projetos.documento_recebido_item dri
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = dri.cd_usuario_recebimento
					 WHERE dri.cd_documento_recebido_item = ".intval($args['cd_documento_recebido_item'])."
			      ";
		#echo "<PRE>".$qr_sql."</PRE>"; exit;
        $result = $this->db->query($qr_sql);
    }
	
	function receber_todos_documentos(&$result, $args = array())
    {
		$qr_sql = "
			UPDATE projetos.documento_recebido_item 
			   SET dt_recebimento         = CURRENT_TIMESTAMP, 
			       cd_usuario_recebimento = ".intval($args['cd_usuario'])."
			 WHERE cd_documento_recebido = ".intval($args['cd_documento_recebido'])."
			   AND dt_recebimento IS NULL";
	
        $result = $this->db->query($qr_sql);
    }

    function receber(&$result, $args = array())
    {
		$qr_sql = "
			UPDATE projetos.documento_recebido 
			   SET dt_ok         = CURRENT_TIMESTAMP, 
			       cd_usuario_ok = ".intval($args['cd_usuario'])."
			WHERE cd_documento_recebido = ".intval($args['cd_documento_recebido']).";";
	
        $result = $this->db->query($qr_sql);
    }
	
	function observacao_novo_protocolo(&$result, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.documento_recebido_item
			   SET ds_observacao         = ".utf8_decode(str_escape($args["ds_observacao"]))." || '  ' || COALESCE(ds_observacao, '')
			 WHERE cd_documento_recebido_item = ".intval($args["cd_documento_recebido_item"]).";";
			 
		$result = $this->db->query($qr_sql);
	}

    function adicionar_documento(&$result, $args = array())
    {
        if (intval($args['cd_documento_recebido_item']) > 0)
        {
            $qr_sql = "
				UPDATE projetos.documento_recebido_item
                   SET cd_empresa            = ".intval($args["cd_empresa"]).",
					   cd_registro_empregado = ".intval($args["cd_registro_empregado"]).",
					   seq_dependencia       = ".intval($args["seq_dependencia"]).",
					   ds_observacao         = ".utf8_decode(str_escape($args["ds_observacao"])).",
					   nr_folha              = ".intval($args["nr_folha"]).",
					   cd_tipo_doc           = ".intval($args["cd_tipo_doc"]).",
					   arquivo               = ".utf8_decode(str_escape($args["arquivo"])).",
					   nome                  = ".utf8_decode(str_escape($args["nome"])).",
					   arquivo_nome          = ".utf8_decode(str_escape($args["arquivo_nome"])).",
					   nr_folha_pdf          = ".((isset($args['nr_folha_pdf']) AND intval($args['nr_folha_pdf']) > 0) ? intval($args['nr_folha_pdf']) : "DEFAULT") ."
				 WHERE cd_documento_recebido_item = ".intval($args["cd_documento_recebido_item"]).";";
        }
        else
        {
            $qr_sql = "
                INSERT INTO projetos.documento_recebido_item 
				     ( 
                        cd_documento_recebido,
                        cd_empresa,
                        cd_registro_empregado,
                        seq_dependencia,
                        ds_observacao,
                        nr_folha,
                        cd_tipo_doc, 
                        dt_cadastro,
                        cd_usuario_cadastro,
                        arquivo,
                        nome,
                        arquivo_nome,
                        nr_folha_pdf
                     ) 
				VALUES 
				     ( 
                        ".intval($args["cd_documento_recebido"]).",
						".intval($args["cd_empresa"]).",
						".intval($args["cd_registro_empregado"]).",
						".intval($args["seq_dependencia"]).",
						".utf8_decode(str_escape($args["ds_observacao"])).",
						".intval($args["nr_folha"]).",
						".intval($args["cd_tipo_doc"]).",
						CURRENT_TIMESTAMP, 
						".intval($args["cd_usuario"]).",
						".utf8_decode(str_escape($args["arquivo"])).",
						".utf8_decode(str_escape($args["nome"])).",
						".utf8_decode(str_escape($args["arquivo_nome"])).",
						".((isset($args['nr_folha_pdf']) AND intval($args['nr_folha_pdf']) > 0) ? intval($args['nr_folha_pdf']) : "DEFAULT") ."
                     );";
        }
		
		$result = $this->db->query($qr_sql);
    }

    function salvar_devolucao(&$result, $args = array())
    {
        if (intval($args['cd_documento_recebido']) > 0)
        {
            $qr_sql = " 
				INSERT INTO projetos.documento_recebido_devolucao
					 (
					   cd_documento_recebido,
					   descricao, 
					   cd_usuario_inclusao
					 )
				VALUES 
					 (
					   " . intval($args['cd_documento_recebido']) . ",
					   " . (trim($args['descricao']) == "" ? "DEFAULT" : "'" . $args['descricao'] . "'") . ",
					   " . (intval($args['cd_usuario']) == 0 ? "DEFAULT" : intval($args['cd_usuario'])) . "
					 );";
            $this->db->query($qr_sql);
        }
    }    

	function resumo(&$result, $args = array())
    {
        $qr_sql = "
			SELECT b.cd_tipo_doc,
				   COALESCE(d.nome_documento, 'NÃO IDENTIFICADO') AS nome_documento,
				   COUNT(*) AS total
              FROM projetos.documento_recebido a
              JOIN projetos.documento_recebido_item b
                ON a.cd_documento_recebido = b.cd_documento_recebido
              LEFT JOIN public.tipo_documentos d
                ON b.cd_tipo_doc = d.cd_tipo_doc
             WHERE b.dt_exclusao IS NULL
			 ".(trim($args['cd_tipo_doc']) != '' 
				? (is_numeric($args['cd_tipo_doc']) 
					? "AND b.cd_tipo_doc = " . intval($args['cd_tipo_doc']) 
					: "AND d.nome_documento LIKE UPPER('%" . $db->escape($args['cd_tipo_doc']) . "%') ") 
				: '')."
			".((trim($args['dt_envio_inicio']) != '' AND trim($args['dt_envio_fim']) != '') ? "AND DATE_TRUNC('day', a.dt_envio) BETWEEN TO_DATE('" . $args['dt_envio_inicio'] . "', 'DD/MM/YYYY') AND TO_DATE('" . $args['dt_envio_fim'] . "', 'DD/MM/YYYY') " : '')."
			".((trim($args['dt_ok_inicio']) != '' AND trim($args['dt_ok_fim']) != '') ? "AND DATE_TRUNC('day', a.dt_ok) BETWEEN TO_DATE('" . $args['dt_ok_inicio'] . "', 'DD/MM/YYYY') AND TO_DATE('" . $args['dt_ok_fim'] . "', 'DD/MM/YYYY') " : '')."
			".(intval($args['cd_usuario_envio']) > 0 ? "  AND a.cd_usuario_cadastro = " . intval($args['cd_usuario_envio'])  : '')."
			".(intval($args['cd_usuario_destino']) > 0 ? "  AND a.cd_usuario_ok = " . intval($args['cd_usuario_destino'])  : '')."
			".(intval($args['cd_usuario_encerrado']) > 0 ? "  AND b.cd_usuario_recebimento = " . intval($args['cd_usuario_encerrado'])  : '')."
			 GROUP BY b.cd_tipo_doc, d.nome_documento
			 ORDER BY b.cd_tipo_doc";
        
        $result = $this->db->query($qr_sql);
    }
	
	function carrega_informacoes_protocolo(&$result, $args = array())
	{
		$qr_sql = "
			SELECT d.cd_documento_recebido,
				   d.cd_documento_recebido_grupo,
				   d.cd_documento_recebido_tipo,
				   d.cd_usuario_cadastro,
				   dri.cd_documento_recebido_item,
				   funcoes.nr_documento_recebido(d.nr_ano, d.nr_contador) AS nr_documento_recebido,
				   TO_CHAR(d.dt_envio,'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   TO_CHAR(d.dt_cadastro,'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro,
				   d.nr_ano,
				   d.nr_contador,
				   TO_CHAR(d.dt_ok,'DD/MM/YYYY HH24:MI:SS') AS dt_ok,
				   d.cd_usuario_envio,
				   d.cd_usuario_ok,
				   d.cd_usuario_destino,
				   TO_CHAR(d.dt_redirecionamento,'DD/MM/YYYY HH24:MI:SS') AS dt_redirecionamento,
				   d.observacao_ok,
				   t.ds_tipo,
				   uc.nome as nome_usuario_cadastro,
				   ue.nome as nome_usuario_envio,
				   ud.nome as nome_usuario_destino,
				   uo.nome as nome_usuario_ok,
				   g.ds_nome as grupo_destino_nome,
				   (SELECT TO_CHAR(dv.dt_inclusao,'DD/MM/YYYY HH24:MI:SS')
				     FROM projetos.documento_recebido_devolucao dv
					 WHERE dv.cd_documento_recebido = d.cd_documento_recebido
					   AND dv.dt_exclusao IS NULL
					 ORDER BY dv.dt_inclusao DESC
					 LIMIT 1) AS dt_devolucao,
				   (SELECT descricao
					  FROM projetos.documento_recebido_devolucao dv
					 WHERE dv.cd_documento_recebido = d.cd_documento_recebido
					   AND dv.dt_exclusao IS NULL
					 ORDER BY dv.dt_inclusao DESC
					 LIMIT 1) AS devolucao_descricao,						 
				   (SELECT uc.nome
					  FROM projetos.documento_recebido_devolucao dv
					  JOIN projetos.usuarios_controledi uc
						ON uc.codigo = dv.cd_usuario_inclusao
					 WHERE dv.cd_documento_recebido = d.cd_documento_recebido
					   AND dv.dt_exclusao IS NULL
					 ORDER BY dv.dt_inclusao DESC
					 LIMIT 1) AS devolvido_por,
				   dri.cd_documento_recebido, 
				   dri.cd_empresa, 
				   dri.cd_registro_empregado, 
				   dri.seq_dependencia, 
				   dri.ds_observacao, 
				   dri.ds_observacao_recebimento, 
				   dri.nr_folha, 
				   dri.cd_tipo_doc, 
				   TO_CHAR(dri.dt_cadastro,'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro, 	 
				   dri.arquivo, 
				   dri.nome, 
				   dri.arquivo_nome, 
				   b.nome_documento, 
				   c.nome AS nome_usuario_cadastro_item, 
				   ur.guerra AS guerra_usuario_recebimento, 
				   ur.divisao AS gerencia_usuario_recebimento
			  FROM projetos.documento_recebido d
			  JOIN projetos.documento_recebido_tipo t
				ON d.cd_documento_recebido_tipo = t.cd_documento_recebido_tipo
			  JOIN projetos.documento_recebido_item dri
				ON dri.cd_documento_recebido =  d.cd_documento_recebido
			  LEFT JOIN projetos.usuarios_controledi uc
				ON uc.codigo = d.cd_usuario_cadastro
			  LEFT JOIN projetos.usuarios_controledi ue
				ON ue.codigo = d.cd_usuario_envio
			  LEFT JOIN projetos.usuarios_controledi ud
				ON ud.codigo = d.cd_usuario_destino
			  LEFT JOIN projetos.usuarios_controledi uo
				ON uo.codigo = d.cd_usuario_ok
			  LEFT JOIN projetos.documento_recebido_grupo g
				ON g.cd_documento_recebido_grupo = d.cd_documento_recebido_grupo
			  JOIN public.tipo_documentos b 
				ON dri.cd_tipo_doc = b.cd_tipo_doc 
			  JOIN projetos.usuarios_controledi c
				ON c.codigo = dri.cd_usuario_cadastro 
			  LEFT JOIN projetos.usuarios_controledi ur 
				ON ur.codigo = dri.cd_usuario_recebimento 
			 WHERE dri.dt_exclusao IS NULL
			   AND dri.cd_documento_recebido_item = ".$args['cd_documento_recebido_item'].";";
		   
	$result = $this->db->query($qr_sql);
	}
	
	function excluir_justificado(&$result, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.documento_recebido_item 
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   justificativa       = ".str_escape($args['justificativa'])."
		     WHERE cd_documento_recebido_item = ".intval($args['cd_documento_recebido_item']);
			
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_obs_recebimento(&$result, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.documento_recebido_item
			   SET ds_observacao_recebimento  = ".utf8_decode(str_escape($args['ds_observacao_recebimento']))."   
			 WHERE cd_documento_recebido_item = ".intval($args['cd_documento_recebido_item']).";";
	
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_re(&$result, $args = array())
	{
		$qr_sql = "
					UPDATE projetos.documento_recebido_item
					   SET cd_empresa            = ".$args['cd_empresa'].",
					       cd_registro_empregado = ".$args['cd_registro_empregado'].",
						   seq_dependencia       = ".$args['seq_dependencia'].",
					       nome                  = ".utf8_decode(str_escape($args['nome']))."   
					 WHERE cd_documento_recebido_item = ".intval($args['cd_documento_recebido_item']).";
			      ";
	
		$result = $this->db->query($qr_sql);
	}	
	
	function incluir_protocolo(&$result, $args = array())
    {		
        $cd_documento_recebido = $this->db->get_new_id("projetos.documento_recebido", "cd_documento_recebido");

        $qr_sql = "
			INSERT INTO projetos.documento_recebido 
			     (
					cd_documento_recebido,
					cd_documento_recebido_tipo,
					cd_usuario_cadastro,
					dt_cadastro,
					cd_documento_recebido_tipo_solic,
					cd_documento_recebido_grupo_envio
		         ) 
		    VALUES 
		         (
					".intval($cd_documento_recebido).",
					".intval($args["cd_documento_recebido_tipo"]).",
					".intval($args["cd_usuario"]).",
					CURRENT_TIMESTAMP,
					".(intval($args["cd_documento_recebido_tipo_solic"]) > 0 ? intval($args["cd_documento_recebido_tipo_solic"]) : "DEFAULT").",
					".(intval($args["cd_documento_recebido_grupo_envio"]) > 0 ? intval($args["cd_documento_recebido_grupo_envio"]) : "DEFAULT")."
		         );";
	

		$result = $this->db->query($qr_sql);

       return $cd_documento_recebido;
	}

	function atualizar_protocolo(&$result, $args = array())
    {	
    	$qr_sql = "
    		UPDATE projetos.documento_recebido
    		   SET cd_documento_recebido_tipo_solic = ".(intval($args["cd_documento_recebido_tipo_solic"]) > 0 ? intval($args["cd_documento_recebido_tipo_solic"]) : "DEFAULT")."
    		 WHERE cd_documento_recebido = ".intval($args['cd_documento_recebido']).";";

    	$result = $this->db->query($qr_sql);
    }
	
	function email_dos_usuarios_de_destino($cd_usuario_destino, $cd_documento_recebido_grupo)
    {
        if (intval($cd_usuario_destino) == 0 && intval($cd_documento_recebido_grupo) == 0)
        {
            echo 'Informe OU usuário de destino OU grupo de destino';

            return false;
        }
        else
        {
            $para = '';
            if (intval($cd_usuario_destino) > 0)
            {
                $query = $this->db->query("SELECT usuario FROM projetos.usuarios_controledi WHERE codigo=?", array(intval($cd_usuario_destino)));
                $row = $query->row_array();
                $para = $row['usuario'] . '@eletroceee.com.br';
            }
            elseif (intval($cd_documento_recebido_grupo) > 0)
            {
				$qr_sql = "
					SELECT email_grupo
					  FROM projetos.documento_recebido_grupo
					 WHERE cd_documento_recebido_grupo = ".intval($cd_documento_recebido_grupo)."";
					 
				$query = $this->db->query($qr_sql);
				$row_grupo = $query->row_array();
				
				if(trim($row_grupo['email_grupo']) != '')
				{
					$para = trim($row_grupo['email_grupo']);
				}
				else
				{
					$query = $this->db->query("
						SELECT u.usuario
						FROM projetos.usuarios_controledi u
						JOIN projetos.documento_recebido_grupo_usuario g ON g.cd_usuario=u.codigo
						WHERE g.dt_exclusao IS NULL AND g.cd_documento_recebido_grupo=?", array(intval($cd_documento_recebido_grupo))
					);
					$rows = $query->result_array();

					foreach ($rows as $row)
					{
						$para_array[] = $row['usuario'] . "@eletroceee.com.br";
					}

					$para = implode(';', $para_array);
				}
            }

            return $para;
        }
    }
	
	function redirecionar($args, &$msg = array())
    {
        // *** consistências
        if ((intval($args['cd_documento_recebido_grupo']) == 0) && (intval($args['cd_usuario_destino']) == 0))
        {
            $msg[] = "Consistência: Usuário de destino ou Grupo de destino deve ser informado.";
            return false;
        }

        // *** tratamentos
        $grupo = "";
        $destino = "";
        if (intval($args['cd_documento_recebido_grupo']) > 0)
        {
            $grupo = ", cd_documento_recebido_grupo={cd_documento_recebido_grupo}";
            $destino = ", cd_usuario_destino=null";
        }
        elseif (intval($args['cd_usuario_destino']) > 0)
        {
            $destino = ", cd_usuario_destino={cd_usuario_destino}";
            $grupo = ", cd_documento_recebido_grupo=NULL";
        }

        try
        {
            $sql = "UPDATE projetos.documento_recebido 
			SET dt_redirecionamento=current_timestamp
			$destino
			$grupo
			WHERE cd_documento_recebido={cd_documento_recebido}";

            esc('{cd_usuario_destino}', $args['cd_usuario_destino'], $sql, 'int');
            esc('{cd_documento_recebido}', $args['cd_documento_recebido'], $sql, 'int');
            esc('{cd_documento_recebido_grupo}', $args['cd_documento_recebido_grupo'], $sql, 'int');
            //echo $sql;
            $q = $this->db->query($sql);

            return true;
        }
        catch (Exception $e)
        {
            $msg[] = 'Ops, algum problema aconteceu!';
            return false;
        }
    }
	
	function enviar($args, &$msg = array())
    {
        // *** consistências
        if ((intval($args['cd_documento_recebido_grupo']) == 0) && (intval($args['cd_usuario_destino']) == 0))
        {
            $msg[] = "Consistência: Usuário de destino ou Grupo de destino deve ser informado.";
            return false;
        }
		
        // *** tratamentos
        $grupo = "";
        $destino = "";
        if (intval($args['cd_documento_recebido_grupo']) > 0)
        {
            $grupo = ", cd_documento_recebido_grupo={cd_documento_recebido_grupo}";
        }
        elseif (intval($args['cd_usuario_destino']) > 0)
        {
            $destino = ", cd_usuario_destino={cd_usuario_destino}";
        }

        try
        {
            $sql = "
				UPDATE projetos.documento_recebido 
				SET dt_envio = current_timestamp
				, cd_usuario_envio={cd_usuario_envio}
				$destino
				$grupo
				WHERE cd_documento_recebido={cd_documento_recebido}
			";

            esc('{cd_usuario_envio}', $args['cd_usuario_envio'], $sql, 'int');
            esc('{cd_usuario_destino}', $args['cd_usuario_destino'], $sql, 'int');
            esc('{cd_documento_recebido}', $args['cd_documento_recebido'], $sql, 'int');
            esc('{cd_documento_recebido_grupo}', $args['cd_documento_recebido_grupo'], $sql, 'int');

            $q = $this->db->query($sql);
			
            return true;
        }
        catch (Exception $e)
        {
            $msg[] = 'Ops, algum problema aconteceu!';
            return false;
        }
    }
	
	function enviar_documento_pensao($args)
	{
		$qr_sql = "
					INSERT INTO projetos.documento_recebido_checklist
						 (
							cd_documento_recebido,
							fl_doc_indentificacao,
							fl_carta_concessao,
							fl_comprovante_beneficio,
							fl_certidao_pis,
							ds_tipo_documento,
							fl_nome_titular,
							fl_nome_dependente,
							fl_situacao,
							fl_carimbo,
							fl_pedido_beneficio,
							fl_conta_corrente,
							fl_ordem_pagamento,
							dt_concessao,
							fl_substituto_pis,
							fl_pagamento_anterior,
							cd_usuario_inclusao
						 )
					VALUES
						 (
							".intval($args['cd_documento_recebido']).",
							".(trim($args['fl_doc_indentificacao']) == '' ? "DEFAULT"  : str_escape($args['fl_doc_indentificacao'])).",
							".(trim($args['fl_carta_concessao']) == '' ? "DEFAULT"  : str_escape($args['fl_carta_concessao'])).",
							".(trim($args['fl_comprovante_beneficio']) == '' ? "DEFAULT"  : str_escape($args['fl_comprovante_beneficio'])).",
							".(trim($args['fl_certidao_pis']) == '' ? "DEFAULT"  : str_escape($args['fl_certidao_pis'])).",
							".(trim($args['ds_tipo_documento']) == '' ? "DEFAULT"  : str_escape($args['ds_tipo_documento'])).",
							".(trim($args['fl_nome_titular']) == '' ? "DEFAULT"  : str_escape($args['fl_nome_titular'])).",
							".(trim($args['fl_nome_dependente']) == '' ? "DEFAULT"  : str_escape($args['fl_nome_dependente'])).",
							".(trim($args['fl_situacao']) == '' ? "DEFAULT"  : str_escape($args['fl_situacao'])).",
							".(trim($args['fl_carimbo']) == '' ? "DEFAULT"  : str_escape($args['fl_carimbo'])).",
							".(trim($args['fl_pedido_beneficio']) == '' ? "DEFAULT"  : str_escape($args['fl_pedido_beneficio'])).",
							".(trim($args['fl_conta_corrente']) == '' ? "DEFAULT"  : str_escape($args['fl_conta_corrente'])).",
							".(trim($args['fl_ordem_pagamento']) == '' ? "DEFAULT"  : str_escape($args['fl_ordem_pagamento'])).",
							 ".(trim($args['dt_concessao']) == '' ? "DEFAULT" : "TO_DATE('".$args['dt_concessao']."','DD/MM/YYYY')").",
							".(trim($args['fl_substituto_pis']) == '' ? "DEFAULT"  : str_escape($args['fl_substituto_pis'])).",
							".(trim($args['fl_pagamento_anterior']) == '' ? "DEFAULT"  : str_escape($args['fl_pagamento_anterior'])).",
							".intval($args['cd_usuario_envio'])."
						 );";
					
		$this->db->query($qr_sql);
	}

	public function get_usuario_grupo($cd_documento_recebido_grupo, $cd_usuario)
	{
		$qr_sql = "
			SELECT COUNT(*) AS tl
			  FROM projetos.documento_recebido_grupo_usuario 
		     WHERE dt_exclusao IS NULL
		       AND cd_documento_recebido_grupo = ".intval($cd_documento_recebido_grupo)."
		       AND cd_usuario                  = ".intval($cd_usuario).";";

	    return $this->db->query($qr_sql)->row_array();
	}
	
	function carregar_tipo_solicitacao()
	{
		$qr_sql = "
		SELECT cd_documento_recebido_tipo_solic AS value,
               ds_documento_recebido_tipo_solic AS text
		  FROM projetos.documento_recebido_tipo_solic 
	     WHERE dt_exclusao IS NULL
	     ORDER BY nr_ordem, ds_documento_recebido_tipo_solic;";

	    return $this->db->query($qr_sql)->result_array();
	}

	function carregar($cd, $fl_recebido = '', $fl_tipo_novo_protocolo = '')
    {
        $sql = "
			SELECT d.cd_documento_recebido,
				   d.cd_documento_recebido_grupo,
				   d.cd_documento_recebido_tipo,
				   d.cd_usuario_cadastro,
				   funcoes.nr_documento_recebido(d.nr_ano, d.nr_contador) AS nr_documento_recebido,
				   TO_CHAR(d.dt_envio,'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   TO_CHAR(d.dt_cadastro,'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro,
				   d.nr_ano,
				   d.nr_contador,
				   TO_CHAR(d.dt_ok,'DD/MM/YYYY HH24:MI:SS') AS dt_ok,
				   d.cd_usuario_envio,
				   d.cd_usuario_ok,
				   d.cd_usuario_destino,
				   TO_CHAR(d.dt_redirecionamento,'DD/MM/YYYY HH24:MI:SS') AS dt_redirecionamento,
				   d.observacao_ok,
				   t.ds_tipo,
				   uc.nome AS nome_usuario_cadastro,
				   ue.nome AS nome_usuario_envio,
				   ud.nome AS nome_usuario_destino,
				   uo.nome AS nome_usuario_ok,
				   g.ds_nome AS grupo_destino_nome,
				   (SELECT TO_CHAR(dv.dt_inclusao,'DD/MM/YYYY HH24:MI:SS')
					  FROM projetos.documento_recebido_devolucao dv
					 WHERE dv.cd_documento_recebido = d.cd_documento_recebido
					   AND dv.dt_exclusao IS NULL
					 ORDER BY dv.dt_inclusao DESC
					 LIMIT 1) AS dt_devolucao,
				   (SELECT descricao
					  FROM projetos.documento_recebido_devolucao dv
					 WHERE dv.cd_documento_recebido = d.cd_documento_recebido
					   AND dv.dt_exclusao IS NULL
					 ORDER BY dv.dt_inclusao DESC
					 LIMIT 1) AS devolucao_descricao,						 
				   (SELECT uc.nome
					  FROM projetos.documento_recebido_devolucao dv
					  JOIN projetos.usuarios_controledi uc
						ON uc.codigo = dv.cd_usuario_inclusao
					 WHERE dv.cd_documento_recebido = d.cd_documento_recebido
					   AND dv.dt_exclusao IS NULL
					 ORDER BY dv.dt_inclusao DESC
					 LIMIT 1) AS devolvido_por,
				   d.cd_documento_recebido_tipo_solic,
				   d.cd_documento_recebido_grupo_envio			 
			  FROM projetos.documento_recebido d
			  JOIN projetos.documento_recebido_tipo t
				ON d.cd_documento_recebido_tipo = t.cd_documento_recebido_tipo
			  LEFT JOIN projetos.usuarios_controledi uc
				ON uc.codigo = d.cd_usuario_cadastro
			  LEFT JOIN projetos.usuarios_controledi ue
				ON ue.codigo = d.cd_usuario_envio
			  LEFT JOIN projetos.usuarios_controledi ud
				ON ud.codigo = d.cd_usuario_destino
			  LEFT JOIN projetos.usuarios_controledi uo
				ON uo.codigo = d.cd_usuario_ok
			  LEFT JOIN projetos.documento_recebido_grupo g
				ON g.cd_documento_recebido_grupo = d.cd_documento_recebido_grupo";
        $row = array();
        $query = $this->db->query($sql . ' LIMIT 1 ');
        $fields = $query->field_data();

        foreach ($fields as $field)
        {
            $row[$field->name] = '';
        }

        if (intval($cd) > 0)
        {
            $sql.=" WHERE d.cd_documento_recebido={cd_documento_recebido} ";
            esc("{cd_documento_recebido}", intval($cd), $sql);

            $query = $this->db->query($sql);

            if ($query->row_array())
            {
                $row = $query->row_array();

                $sql = "
					SELECT a.cd_documento_recebido_item, 
					       a.cd_documento_recebido, 
						   a.cd_empresa, 
				           a.cd_registro_empregado, 
						   a.seq_dependencia, 
						   a.ds_observacao, 
						   a.ds_observacao_recebimento, 
						   a.nr_folha, 
				           a.cd_tipo_doc, 
						   TO_CHAR(a.dt_cadastro,'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro, 
						   a.cd_usuario_cadastro, 
						   a.dt_exclusao, 
						   a.cd_usuario_exclusao, 
				           a.arquivo,
						   a.nome, 
						   a.arquivo_nome, 
						   TO_CHAR(a.dt_recebimento,'DD/MM/YYYY HH24:MI:SS') AS dt_recebimento, 
						   a.cd_usuario_recebimento,
					       b.nome_documento, 
						   c.nome AS nome_usuario_cadastro, 
						   ur.guerra AS guerra_usuario_recebimento, 
						   ur.divisao AS gerencia_usuario_recebimento,
						   a.nr_folha_pdf
				      FROM projetos.documento_recebido_item a
				      lEFT JOIN public.tipo_documentos b 
					    ON a.cd_tipo_doc = b.cd_tipo_doc 
				      JOIN projetos.usuarios_controledi c 
					    ON c.codigo = a.cd_usuario_cadastro 
				      LEFT JOIN projetos.usuarios_controledi ur 
					    ON ur.codigo = a.cd_usuario_recebimento 
				     WHERE a.cd_documento_recebido = ".intval($cd)." 
					   AND dt_exclusao IS NULL
					   ".(trim($fl_recebido) == 'S' ? " AND a.dt_recebimento IS NOT NULL " : '') . "
					   ".(trim($fl_recebido) == 'N' ? " AND a.dt_recebimento IS NULL " : '') . "
					   ".(trim($fl_tipo_novo_protocolo) == 'D' ? " AND COALESCE(TRIM(a.arquivo_nome), '') <> ''  AND a.cd_registro_empregado IS NOT NULL " : '') . "
					   ".(trim($fl_tipo_novo_protocolo) == 'P' ? " AND COALESCE(TRIM(a.arquivo_nome), '') = ''  AND a.cd_registro_empregado IS NOT NULL " : '') . "
				     ORDER BY a.cd_empresa, 
					          a.cd_registro_empregado, 
							  a.seq_dependencia, 
							  a.nome ";
				
                $query = $this->db->query($sql);
                $collection = $query->result_array();
                $row['itens'] = array();
                foreach ($collection as $item)
                {
                    $row['itens'][] = $item;
                }

                $row['grupo_destino'] = array();
                if (intval($row['cd_documento_recebido_grupo']) > 0)
                {
                    $sql = "
						SELECT d.cd_usuario,
						       u.nome
						  FROM projetos.documento_recebido_grupo_usuario d
						  JOIN projetos.documento_recebido_grupo g 
						    ON g.cd_documento_recebido_grupo = d.cd_documento_recebido_grupo
						  JOIN projetos.usuarios_controledi u 
						    ON u.codigo = d.cd_usuario
						 WHERE d.cd_documento_recebido_grupo = ".intval($row['cd_documento_recebido_grupo'])."
						   AND g.dt_exclusao IS NULL 
						   AND d.dt_exclusao IS NULL
					";
					
                    $query = $this->db->query($sql);
                    $collection = $query->result_array();
                    $row['grupo_destino'] = array();
                    foreach ($collection as $item)
                    {
                        $row['grupo_destino'][] = $item;
                    }
                }
            }
        }
        #echo "<pre style='text-align:left;'>".$sql."</pre>"; #exit;	
        return $row;
    }

    function reabrir_documento($cd_documento_recebido)
    {
        $qr_sql = "
            UPDATE projetos.documento_recebido
               SET dt_ok         = NULL,
                   cd_usuario_ok = NULL 
             WHERE cd_documento_recebido = ".intval($cd_documento_recebido).";
             
            UPDATE projetos.documento_recebido_item
               SET dt_recebimento         = NULL,
                   cd_usuario_recebimento = NULL 
             WHERE cd_documento_recebido = ".intval($cd_documento_recebido).";";

        $this->db->query($qr_sql); 
    
    }

    public function get_documento_recebido_grupo($cd_usuario)
    {
    	$qr_sql = "
			SELECT cd_documento_recebido_grupo 
			  FROM projetos.documento_recebido_grupo
			 WHERE cd_documento_recebido_grupo = (
				SELECT cd_documento_recebido_grupo 
				  FROM projetos.documento_recebido_grupo_usuario 
				 WHERE cd_usuario = ".intval($cd_usuario)." 
				 ORDER BY dt_inclusao DESC 
				 LIMIT 1
			);";


		return $this->db->query($qr_sql)->row_array();
    }
}
?>