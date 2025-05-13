<?php
class Certificado_participante_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
	
    function certificadoLista(&$result, $args=array())
    {
     $qr_sql = "
            SELECT pc.cd_plano, 
                   p.cd_empresa, 
                   p.cd_registro_empregado, 
                   p.seq_dependencia, 
                   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
                   p.nome, 
                   p.logradouro, 
                   p.bairro, 
                   p.cidade, 
                   TO_CHAR(p.cep,'FM00000') || '-' || TO_CHAR(p.complemento_cep,'FM000') AS cep, 				   
                   funcoes.fnc_codigo_barras_cep(cast(p.cep as bigint), cast(p.complemento_cep as bigint)) AS cep_net,
                   p.unidade_federativa AS uf,
                   TO_CHAR(t.dt_ingresso_eletro,'DD') AS dia_ingresso, 
                   TO_CHAR(t.dt_ingresso_eletro,'MM') AS mes_ingresso, 
                   TO_CHAR(t.dt_ingresso_eletro,'YYYY') AS ano_ingresso, 
                   TO_CHAR(t.dt_ingresso_eletro,'DD/MM/YYYY') AS dt_ingresso,
                   TO_CHAR(pc.dt_aprovacao_spc,'DD/MM/YYYY') AS dt_aprovacao_spc, 
                   cd_spc AS cd_plano_spc, 
                   pc.nome_certificado AS nome_plano_certificado, 
                   pc.pos_imagem, 
                   pc.largura_imagem, 
                   pc.coluna_1, 
                   pc.coluna_2, 
                   pc.nr_largura_logo,
                   pc.nr_altura_logo,
                   pc.nr_x_logo,
                   pc.nr_fonte_verso,
                   pc.nr_altura_linha_verso,
                   pc.presidente_nome,
                   pc.presidente_assinatura
              FROM public.participantes p
              JOIN public.titulares t
                ON t.cd_empresa            = p.cd_empresa 
               AND t.cd_registro_empregado = p.cd_registro_empregado 
               AND t.seq_dependencia       = p.seq_dependencia 				   
              JOIN public.planos_certificados pc 
                ON pc.cd_plano = (CASE WHEN p.cd_plano = 1 AND p.cd_empresa = 3 THEN 3 ELSE p.cd_plano END)
             WHERE p.dt_envio_certificado  IS NULL 
               AND p.dt_obito              IS NULL 
               AND CAST(t.dt_ingresso_eletro AS DATE) BETWEEN CAST(COALESCE(pc.dt_inicio,t.dt_ingresso_eletro) AS DATE) AND CAST(COALESCE(pc.dt_final,t.dt_ingresso_eletro)	AS DATE)
               AND p.cd_empresa            = ".intval(trim($args['cd_empresa']))."
                    ".(((trim($args['cd_registro_empregado']) != "") and (trim($args['seq_dependencia']) != "")) 
                        ? 
                        ("AND p.cd_registro_empregado = ".intval(trim($args['cd_registro_empregado']))." AND p.seq_dependencia = ".intval(trim($args['seq_dependencia'])))
                        : 
                        ("AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia)
                           IN (SELECT p1.cd_empresa, 
                                      p1.cd_registro_empregado, 
                                      p1.seq_dependencia
                                 FROM public.participantes p1
                                 JOIN public.titulares t1 
                                   ON t1.cd_empresa            = p1.cd_empresa 
                                  AND t1.cd_registro_empregado = p1.cd_registro_empregado 
                                  AND t1.seq_dependencia       = p1.seq_dependencia 
                                WHERE p1.dt_envio_certificado IS NULL 
                                  AND p1.dt_obito             IS NULL  
                                  AND CAST(t1.dt_ingresso_eletro AS DATE) BETWEEN ".(trim($args['dt_inicial']) != "" ? "TO_DATE('".trim($args['dt_inicial'])."','DD/MM/YYYY')" : "CURRENT_DATE")." AND ".(trim($args['dt_final']) != "" ? "TO_DATE('".trim($args['dt_final'])."','DD/MM/YYYY')" : "CURRENT_DATE")."
                                  ".(trim($args['cd_plano']) != '' ? "AND p1.cd_plano   = ".intval(trim($args['cd_plano'])) : '')."
                                  ".(trim($args['cd_plano']) != '' ? "AND p1.cd_empresa = ".intval(trim($args['cd_empresa'])) : '')."
                                  
                                  )")	
                            )."
                    ".((array_key_exists('part_selecionado', $args) and trim($args['part_selecionado'])) != "" ? " AND funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN (".trim($args['part_selecionado']).")" : "")."
             ORDER BY p.nome
                  ";
        #echo "<pre style='text-align:left;'>".print_r($args,true).$qr_sql."</pre>"; exit;
        $result = $this->db->query($qr_sql);
    }
	
    function certificadoPadrao(&$result, $args=array())
    {
     $qr_sql = "
				SELECT pc.cd_plano, 
					   TO_CHAR(pc.dt_aprovacao_spc,'DD/MM/YYYY') AS dt_aprovacao_spc, 
					   pc.cd_spc AS cd_plano_spc, 
					   pc.nome_certificado AS nome_plano_certificado, 
					   pc.pos_imagem, 
					   pc.largura_imagem, 
					   pc.coluna_1, 
					   pc.coluna_2, 
					   pc.nr_largura_logo,
					   pc.nr_altura_logo,
					   pc.nr_x_logo,
					   pc.nr_fonte_verso,
					   pc.nr_altura_linha_verso,
					   pc.presidente_nome,
					   pc.presidente_assinatura,
             NULL AS dt_ingresso
				  FROM public.planos_certificados pc 
				 WHERE pc.cd_plano = (CASE WHEN ".intval(trim($args['cd_plano']))." = 1 AND ".intval(trim($args['cd_empresa']))." = 3 
										   THEN 3 
										   ELSE ".intval(trim($args['cd_plano']))." 
									  END)
					   ".(((intval(trim($args['cd_plano'])) == 1) and (!in_array(intval(trim($args['cd_empresa'])), array(2,3)))) ? "AND 0 = 1" : "")."			  
				 ORDER BY pc.dt_inicio DESC
				 LIMIT 1			 
                  ";
        #echo "<pre style='text-align:left;'>".print_r($args,true).$qr_sql."</pre>"; exit; in_array("mac", $os)
        $result = $this->db->query($qr_sql);
    }	

    function verificaDocumento(&$result, $args=array())
    {
            $qr_sql = "
                SELECT (CASE WHEN COUNT(*) = 0 THEN 'N' ELSE 'S' END) AS fl_documento
                  FROM public.registros_documentos_ceeeprev d
                 WHERE d.cd_empresa            = ".intval($args['cd_empresa'])."
                   AND d.cd_registro_empregado = ".intval($args['cd_registro_empregado'])."
                   AND d.seq_dependencia       = ".intval($args['seq_dependencia'])."
                   AND d.cd_doc                = ".intval($args['cd_documento'])."
                   AND d.dt_entrega            IS NOT NULL
                      ";
            #echo "<pre style='text-align:left;'>$qr_sql</pre>"; exit;
            $result = $this->db->query($qr_sql);
    }	

    function getDocumento(&$result, $args=array())
    {
            $qr_sql = "
                SELECT b.nome_documento AS ds_documento
                  FROM public.tipo_documentos b
                 WHERE b.cd_tipo_doc = ".intval($args['cd_documento'])."
                      ";
            #echo "<pre style='text-align:left;'>$qr_sql</pre>"; exit;
            $result = $this->db->query($qr_sql);
    }	
        
    function certificado_lista_documentos(&$result, $args=array())
    {
        $qr_sql = "
            SELECT pc.cd_plano, 
                   p.cd_empresa, 
                   p.cd_registro_empregado, 
                   p.seq_dependencia, 
                   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
                   p.nome, 
                   p.logradouro, 
                   p.bairro, 
                   p.cidade, 
                   TO_CHAR(p.cep,'FM00000') || '-' || TO_CHAR(p.complemento_cep,'FM000') AS cep, 				   
                   funcoes.fnc_codigo_barras_cep(cast(p.cep as bigint), cast(p.complemento_cep as bigint)) AS cep_net,
                   p.unidade_federativa AS uf,
                   TO_CHAR(t.dt_ingresso_eletro,'DD') AS dia_ingresso, 
                   TO_CHAR(t.dt_ingresso_eletro,'MM') AS mes_ingresso, 
                   TO_CHAR(t.dt_ingresso_eletro,'YYYY') AS ano_ingresso, 
                   TO_CHAR(t.dt_ingresso_eletro,'DD/MM/YYYY') AS dt_ingresso,
                   TO_CHAR(pc.dt_aprovacao_spc,'DD/MM/YYYY') AS dt_aprovacao_spc, 
                   cd_spc AS cd_plano_spc, 
                   pc.nome_certificado AS nome_plano_certificado, 
                   pc.pos_imagem, 
                   pc.largura_imagem, 
                   pc.coluna_1, 
                   pc.coluna_2, 
                   pc.nr_largura_logo,
                   pc.nr_altura_logo,
                   pc.nr_x_logo,
                   pc.nr_fonte_verso,
                   pc.nr_altura_linha_verso,
                   cpd.cd_documento,
                   cpd.fl_verificar
              FROM projetos.certificado_participante_documento  cpd
              
              JOIN public.participantes p
                ON cpd.cd_empresa = p.cd_empresa 
              JOIN public.tipo_documentos td
                ON td.cd_tipo_doc = cpd.cd_documento
              JOIN public.titulares t
                ON t.cd_empresa            = p.cd_empresa 
               AND t.cd_registro_empregado = p.cd_registro_empregado 
               AND t.seq_dependencia       = p.seq_dependencia 				   
              JOIN public.planos_certificados pc 
                ON pc.cd_plano = (CASE WHEN p.cd_plano = 1 AND p.cd_empresa = 3 THEN 3 ELSE p.cd_plano END)
             WHERE p.dt_envio_certificado  IS NULL 
               AND cpd.dt_exclusao         IS NULL
               AND p.dt_obito              IS NULL 
               AND t.dt_ingresso_eletro    BETWEEN COALESCE(pc.dt_inicio,t.dt_ingresso_eletro) AND COALESCE(pc.dt_final,t.dt_ingresso_eletro)					
               AND p.cd_empresa            = ".intval(trim($args['cd_empresa']))."
                    ".(((trim($args['cd_registro_empregado']) != "") and (trim($args['seq_dependencia']) != "")) 
                                    ? 
                                    ("AND p.cd_registro_empregado = ".intval(trim($args['cd_registro_empregado']))." AND p.seq_dependencia = ".intval(trim($args['seq_dependencia'])))
                                    : 
                                    ("AND (p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia)
                                       IN (SELECT p1.cd_empresa, 
                                                  p1.cd_registro_empregado, 
                                                  p1.seq_dependencia
                                             FROM public.participantes p1
                                             JOIN public.titulares t1 
                                               ON t1.cd_empresa            = p1.cd_empresa 
                                              AND t1.cd_registro_empregado = p1.cd_registro_empregado 
                                              AND t1.seq_dependencia       = p1.seq_dependencia 
                                            WHERE p1.dt_envio_certificado IS NULL 
                                              AND p1.dt_obito             IS NULL  
                                              AND CAST(t1.dt_ingresso_eletro AS DATE) BETWEEN ".(trim($args['dt_inicial']) != "" ? "TO_DATE('".trim($args['dt_inicial'])."','DD/MM/YYYY')" : "CURRENT_DATE")." AND ".(trim($args['dt_final']) != "" ? "TO_DATE('".trim($args['dt_final'])."','DD/MM/YYYY')" : "CURRENT_DATE")."
                                              ".(trim($args['cd_plano']) != '' ? "AND p1.cd_plano   = ".intval(trim($args['cd_plano'])) : '')."
                                              ".(trim($args['cd_plano']) != '' ? "AND p1.cd_empresa = ".intval(trim($args['cd_empresa'])) : '')."
                                              
                                              )") 
                            )."
                    ".((array_key_exists('part_selecionado', $args) and trim($args['part_selecionado'])) != "" ? " AND funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN (".trim($args['part_selecionado']).")" : "")."
                    ".((array_key_exists('prot_selecionado', $args) and trim($args['prot_selecionado'])) != "" ? " AND funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) || MD5(cpd.cd_documento::TEXT) IN (".trim($args['prot_selecionado']).")" : "")."
             ORDER BY p.nome ASC
                  ";

        #echo "<pre style='text-align:left;'>".print_r($args,true).$qr_sql."</pre>"; 
        $result = $this->db->query($qr_sql);
    }
	
	function lista_protocolo_interno(&$result, $args=array())
    {
		$qr_sql = "
			SELECT dci.cd_empresa,
				   dci.cd_registro_empregado,
				   dci.seq_dependencia,
				   dci.cd_tipo_doc,
				   td.nome_documento,
				   p.nome,
				   dci.arquivo,
				   dci.arquivo_nome,
				   dci.cd_documento_recebido_item,
				   dci.cd_documento_recebido,
				   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
				   CASE WHEN COALESCE(dci.arquivo_nome,'') <> '' THEN 'D' ELSE 'P' END AS tipo
			  FROM projetos.documento_recebido dc
			  JOIN projetos.documento_recebido_item dci
				ON dci.cd_documento_recebido = dc.cd_documento_recebido
			  JOIN public.tipo_documentos td
				ON td.cd_tipo_doc = dci.cd_tipo_doc
			  JOIN public.participantes p
				ON p.cd_registro_empregado = dci.cd_registro_empregado
			 WHERE dc.dt_ok IS NOT NULL
			   AND p.cd_empresa = dci.cd_empresa
			   AND p.seq_dependencia = dci.seq_dependencia
			   ".(isset($args['cd_empresa']) && trim($args['cd_empresa']) != '' ? "AND dci.cd_empresa = ".intval($args['cd_empresa']) : '')."
			   ".(isset($args['nr_ano']) && trim($args['nr_ano']) != '' ? "AND dc.nr_ano      = ".intval($args['nr_ano']) : '')."
			   ".(isset($args['nr_contador']) && trim($args['nr_contador']) != '' ? "AND dc.nr_contador = ".intval($args['nr_contador']) : '')."
			   ".((array_key_exists('prot_selecionado', $args) and trim($args['prot_selecionado'])) != "" ? " AND funcoes.cripto_re(p.cd_empresa::INTEGER, p.cd_registro_empregado::INTEGER, p.seq_dependencia::INTEGER) || md5(dci.cd_documento_recebido_item::TEXT) IN (".trim($args['prot_selecionado']).")" : "");
		
		#echo "<pre style='text-align:left;'>".print_r($args,true).$qr_sql."</pre>"; 
		$result = $this->db->query($qr_sql);
	}
	
	function limpa_tmp(&$result, $args=array())
    {
		$qr_sql = "
			DELETE 
			  FROM projetos.certificados_participantes_tmp
			 WHERE cd_usuario_cadastro = ".intval($args['cd_usuario_cadastro'])."";

		$result = $this->db->query($qr_sql);
	}
	
	function salva_certificado_tmp(&$result, $args=array())
    {
		$qr_sql = "
			INSERT INTO projetos.certificados_participantes_tmp
			     (
					id,
					tipo,
					cd_tipo_doc,
					cd_empresa,
					cd_registro_empregado,
					seq_dependencia,
					cd_usuario_cadastro, 
					re_cripto,
					arquivo,
					arquivo_nome,
					fl_verificar,
					cd_documento_recebido,
					cd_documento_recebido_item
				 )
			VALUES
			     (
					".(trim($args['id']) != '' ? "'".trim($args['id'])."'" : "DEFAULT").",
					".(trim($args['tipo']) != '' ? "'".trim($args['tipo'])."'" : "DEFAULT").",
					".intval($args['cd_tipo_doc']).",
					".intval($args['cd_empresa']).",
					".intval($args['cd_registro_empregado']).",
					".intval($args['seq_dependencia']).",
					".intval($args['cd_usuario_cadastro']).",
					'".trim($args['re_cripto'])."',
					".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
					".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
					".(trim($args['fl_verificar']) != '' ? "'".trim($args['fl_verificar'])."'" : "DEFAULT").",
					".(intval($args['cd_documento_recebido']) > 0 ? intval($args['cd_documento_recebido']) : "DEFAULT").",
					".(intval($args['cd_documento_recebido_item']) > 0 ? intval($args['cd_documento_recebido_item']) : "DEFAULT")."
				 )";
	
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_certificado_tmp(&$result, $args=array())
    {
		$qr_sql = "
					DELETE FROM projetos.certificados_participantes_tmp
					 WHERE id = '".$args['id']."'
			   ";
	
		$result = $this->db->query($qr_sql);
	}	
	
	function certificado_tmp(&$result, $args=array())
    {
		$qr_sql = "
			SELECT cpt.id AS idunico,
			       cpt.cd_tipo_doc AS cd_documento,
				   td.nome_documento AS ds_documento,
				   cpt.cd_documento_recebido,
				   cpt.cd_documento_recebido_item,
				   cpt.cd_empresa,
				   cpt.cd_registro_empregado,
				   cpt.seq_dependencia,
				   cpt.cd_usuario_cadastro, 
				   cpt.arquivo,
				   cpt.arquivo_nome,
				   cpt.re_cripto,
				   cpt.tipo,
				   cpt.fl_verificar,
				   p.nome			   
				   
			  FROM projetos.certificados_participantes_tmp cpt
              JOIN public.tipo_documentos td
                ON td.cd_tipo_doc = cpt.cd_tipo_doc			  
			  JOIN public.participantes p
                ON cpt.cd_empresa = p.cd_empresa 
			   AND cpt.cd_registro_empregado = p.cd_registro_empregado 
               AND cpt.seq_dependencia       = p.seq_dependencia 
			 WHERE cpt.cd_usuario_cadastro = ".intval($args['cd_usuario_cadastro'])."
			 ORDER BY ".((array_key_exists('fl_ordenacao_1', $args) and trim($args['fl_ordenacao_1'])) != "" ? $args['fl_ordenacao_1'] . " ". $args['fl_tipo_order_1'].",".$args['fl_ordenacao_2'] . " ". $args['fl_tipo_order_2'] : "p.nome")."";
		$result = $this->db->query($qr_sql);
	}
}
?>