<?php
class Cadastro_eleicoes_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT e.ano_eleicao, 
				   e.cd_eleicao, 
				   e.cd_tipo,
				   e.nome, 
				   e.situacao, 
				   CASE WHEN COALESCE(e.situacao,'') = '' THEN 'Aguardando Geração'
						WHEN e.situacao = 'G' THEN 'Cadastro Gerado'
						WHEN e.situacao = 'A' THEN 'Aberta Apuração'
						WHEN e.situacao = 'F' THEN 'Apuração Encerrada'
						ELSE 'Não identificado'
				   END AS status,
				   e.num_votos, 
				   e.votos_apurados, 
				   e.modalidade, 
				   TO_CHAR(e.dt_hr_abertura, 'DD/MM/YYYY HH24:MI') AS dt_hr_abertura,
				   TO_CHAR(e.dt_hr_fechamento, 'DD/MM/YYYY HH24:MI') AS dt_hr_fechamento,
				   e.invalidados, 
				   e.cd_usuario_abertura, 
				   e.cd_usuario_fechamento, 
				   e.id_eleicao,
				   (SELECT COUNT(*)
					  FROM eleicoes.cadastros_eleicoes ce
					 WHERE ce.ano_eleicao = e.ano_eleicao) AS qt_cadastro,
				   e.nr_controle,
				   e.fl_codigo_barra,
				   e.fl_atualiza_oracle
			  FROM eleicoes.eleicao e
			 WHERE 1 = 1
				   ".(intval($args['id_eleicao']) > 0 ? "AND e.id_eleicao = ".intval($args['id_eleicao']) : "")."
			 ORDER BY e.ano_eleicao ASC;";

		$result = $this->db->query($qr_sql);
	}	
	
	function importaOracle(&$result, $args=array())
	{
		$qr_sql = "
			SELECT eleicoes.importa_oracle_cadastro(".intval($args['id_eleicao']).");";

		$result = $this->db->query($qr_sql);
	}		
	
	function geraNumeroControle(&$result, $args=array())
	{
		$qr_sql = "
			SELECT eleicoes.eleicoes_gera_controle(".intval($args['id_eleicao']).");";

		$result = $this->db->query($qr_sql);
	}		
	
	function geraCodigoBarra(&$result, $args=array())
	{
		$qr_sql = "
					SELECT eleicoes.eleicoes_gera_codigo_barra(".intval($args['id_eleicao']).");
					SELECT eleicoes.atualiza_pagina_cadastro(".intval($args['id_eleicao']).");
			      ";

		$result = $this->db->query($qr_sql);
	}		
	
	function atualizaOracle(&$result, $args=array())
	{
		
		//echo br(2).date("d/m/Y H:i:s");
		#---SELECT eleicoes.eleicoes_atualiza_oracle(".intval($args['id_eleicao']).");
		$qr_sql = "
			SELECT numero_codigo_barras,
				   num_kit,
				   cd_empresa,
				   cd_registro_empregado,
				   seq_dependencia
			  FROM eleicoes.cadastros_eleicoes ce
			  JOIN eleicoes.eleicao e
				ON e.ano_eleicao = ce.ano_eleicao
			 WHERE e.id_eleicao = ".intval($args['id_eleicao']).";";

		$result = $this->db->query($qr_sql);
		$ar_reg = $result->result_array();		
		
		$qr_update = "";
		foreach($ar_reg as $item)
		{
			$qr_item = 'UPDATE cadastros_eleicoes SET numero_codigo_barras = '.$item['numero_codigo_barras'].', kit = '.$item['num_kit'].' WHERE cd_empresa = '.$item['cd_empresa'].' AND cd_registro_empregado = '.$item['cd_registro_empregado'].' AND seq_dependencia = '.$item['seq_dependencia'].';
';
			$qr_update.= $qr_item;
			/*$qr_update.= "
							SELECT sincroniza.remote_exec_dbi('SELECT sincroniza.remote_exec_dbi (''".$qr_item."'')');
						 ";*/
		}
		
		echo "<PRE>".print_r($qr_update,true)."</PRE>"; exit;
		
		//$result = $this->db->query($qr_update);
		
		//echo br(2).date("d/m/Y H:i:s");
		
		exit;
	}		
	
	function listarCandidatoDeliberativo(&$result, $args=array())
	{
		#### LISTA CANDIDATOS CONSELHO DELIBERATIVO ####
		$qr_sql = " 
			SELECT TRIM(TO_CHAR(ce.cd_empresa,'00')) AS cd_empresa,
				   TRIM(TO_CHAR(ce.cd_registro_empregado,'000000')) AS cd_registro_empregado,
				   TRIM(TO_CHAR(ce.seq_dependencia,'00')) AS seq_dependencia,
				   ce.nome AS ds_candidato,
				   cce.nome AS cargo,
				   COALESCE(ae.num_votos,0) + COALESCE(ae.num_votos_eletronico,0) AS qt_total_candidato
			  FROM eleicoes.eleicao e
			  JOIN eleicoes.candidatos_eleicoes ce
				ON ce.ano_eleicao = e.ano_eleicao
			   AND ce.cd_eleicao  = e.cd_eleicao
			  JOIN eleicoes.cargos_eleicoes cce
				ON cce.cd_cargo = ce.cd_cargo
			  LEFT JOIN eleicoes.apuracao_eleicoes ae
				ON ae.ano_eleicao           = ce.ano_eleicao
			   AND ae.cd_eleicao            = ce.cd_eleicao
			   AND ae.cd_empresa            = ce.cd_empresa
			   AND ae.cd_registro_empregado = ce.cd_registro_empregado
			   AND ae.seq_dependencia       = ce.seq_dependencia
			 WHERE e.id_eleicao = ".intval($args['id_eleicao'])."
			   AND cce.tp_cargo = 'T'
			   AND cce.cd_cargo = 10
			 ORDER BY ce.posicao;";

		$result = $this->db->query($qr_sql);
	}	
	
	function listarCandidatoFiscal(&$result, $args=array())
	{
		#### LISTA CANDIDATOS CONSELHO FISCAL ####
		$qr_sql = " 
			SELECT TRIM(TO_CHAR(ce.cd_empresa,'00')) AS cd_empresa,
				   TRIM(TO_CHAR(ce.cd_registro_empregado,'000000')) AS cd_registro_empregado,
				   TRIM(TO_CHAR(ce.seq_dependencia,'00')) AS seq_dependencia,
				   ce.nome AS ds_candidato,
				   cce.nome AS cargo,
				   COALESCE(ae.num_votos,0) + COALESCE(ae.num_votos_eletronico,0) AS qt_total_candidato
			  FROM eleicoes.eleicao e
			  JOIN eleicoes.candidatos_eleicoes ce
				ON ce.ano_eleicao = e.ano_eleicao
			   AND ce.cd_eleicao  = e.cd_eleicao
			  JOIN eleicoes.cargos_eleicoes cce
				ON cce.cd_cargo = ce.cd_cargo
			  LEFT JOIN eleicoes.apuracao_eleicoes ae
				ON ae.ano_eleicao           = ce.ano_eleicao
			   AND ae.cd_eleicao            = ce.cd_eleicao
			   AND ae.cd_empresa            = ce.cd_empresa
			   AND ae.cd_registro_empregado = ce.cd_registro_empregado
			   AND ae.seq_dependencia       = ce.seq_dependencia
			 WHERE e.id_eleicao = ".intval($args['id_eleicao'])."
			   AND cce.tp_cargo = 'T'
			   AND cce.cd_cargo = 20
			 ORDER BY ce.posicao;";

		$result = $this->db->query($qr_sql);
	}	
	
	function listarCandidatoDiretor(&$result, $args=array())
	{
		#### LISTA CANDIDATOS DIRETOR ####
		$qr_sql = " 
			SELECT TRIM(TO_CHAR(ce.cd_empresa,'00')) AS cd_empresa,
				   TRIM(TO_CHAR(ce.cd_registro_empregado,'000000')) AS cd_registro_empregado,
				   TRIM(TO_CHAR(ce.seq_dependencia,'00')) AS seq_dependencia,
				   ce.nome AS ds_candidato,
				   cce.nome AS cargo,
				   COALESCE(ae.num_votos,0) + COALESCE(ae.num_votos_eletronico,0) AS qt_total_candidato
			  FROM eleicoes.eleicao e
			  JOIN eleicoes.candidatos_eleicoes ce
				ON ce.ano_eleicao = e.ano_eleicao
			   AND ce.cd_eleicao  = e.cd_eleicao
			  JOIN eleicoes.cargos_eleicoes cce
				ON cce.cd_cargo = ce.cd_cargo
			  LEFT JOIN eleicoes.apuracao_eleicoes ae
				ON ae.ano_eleicao           = ce.ano_eleicao
			   AND ae.cd_eleicao            = ce.cd_eleicao
			   AND ae.cd_empresa            = ce.cd_empresa
			   AND ae.cd_registro_empregado = ce.cd_registro_empregado
			   AND ae.seq_dependencia       = ce.seq_dependencia
			 WHERE e.id_eleicao = ".intval($args['id_eleicao'])."
			   AND cce.tp_cargo  = 'T'
			   AND cce.cd_cargo  = 30
			 ORDER BY ce.posicao; ";

		$result = $this->db->query($qr_sql);
	}	
	
	function listarCandidatoCAPAESSul(&$result, $args=array())
	{
		#### LISTA CANDIDATOS CAP - PLANO UNICO AES SUL ####
		$qr_sql = " 
			SELECT TRIM(TO_CHAR(ce.cd_empresa,'00')) AS cd_empresa,
				   TRIM(TO_CHAR(ce.cd_registro_empregado,'000000')) AS cd_registro_empregado,
				   TRIM(TO_CHAR(ce.seq_dependencia,'00')) AS seq_dependencia,
				   ce.nome AS ds_candidato,
				   cce.nome AS cargo,
				   COALESCE(ae.num_votos,0) + COALESCE(ae.num_votos_eletronico,0) AS qt_total_candidato
			  FROM eleicoes.eleicao e
			  JOIN eleicoes.candidatos_eleicoes ce
				ON ce.ano_eleicao = e.ano_eleicao
			   AND ce.cd_eleicao  = e.cd_eleicao
			  JOIN eleicoes.cargos_eleicoes cce
				ON cce.cd_cargo = ce.cd_cargo
			  LEFT JOIN eleicoes.apuracao_eleicoes ae
				ON ae.ano_eleicao           = ce.ano_eleicao
			   AND ae.cd_eleicao            = ce.cd_eleicao
			   AND ae.cd_empresa            = ce.cd_empresa
			   AND ae.cd_registro_empregado = ce.cd_registro_empregado
			   AND ae.seq_dependencia       = ce.seq_dependencia
			 WHERE e.id_eleicao = ".intval($args['id_eleicao'])."
			   AND cce.tp_cargo  = 'T'
			   AND cce.cd_cargo  = 40
			 ORDER BY ce.posicao; ";

		$result = $this->db->query($qr_sql);
	}	
	
	function listarCandidatoCAPCGTEE(&$result, $args=array())
	{
		#### LISTA CANDIDATOS CAP - PLANO UNICO CGTEE ####
		$qr_sql = " 
			SELECT TRIM(TO_CHAR(ce.cd_empresa,'00')) AS cd_empresa,
				   TRIM(TO_CHAR(ce.cd_registro_empregado,'000000')) AS cd_registro_empregado,
				   TRIM(TO_CHAR(ce.seq_dependencia,'00')) AS seq_dependencia,
				   ce.nome AS ds_candidato,
				   cce.nome AS cargo,
				   COALESCE(ae.num_votos,0) + COALESCE(ae.num_votos_eletronico,0) AS qt_total_candidato
			  FROM eleicoes.eleicao e
			  JOIN eleicoes.candidatos_eleicoes ce
				ON ce.ano_eleicao = e.ano_eleicao
			   AND ce.cd_eleicao  = e.cd_eleicao
			  JOIN eleicoes.cargos_eleicoes cce
				ON cce.cd_cargo = ce.cd_cargo
			  LEFT JOIN eleicoes.apuracao_eleicoes ae
				ON ae.ano_eleicao           = ce.ano_eleicao
			   AND ae.cd_eleicao            = ce.cd_eleicao
			   AND ae.cd_empresa            = ce.cd_empresa
			   AND ae.cd_registro_empregado = ce.cd_registro_empregado
			   AND ae.seq_dependencia       = ce.seq_dependencia
			 WHERE e.id_eleicao = ".intval($args['id_eleicao'])."
			   AND cce.tp_cargo  = 'T'
			   AND cce.cd_cargo  = 50
			 ORDER BY ce.posicao; ";

		$result = $this->db->query($qr_sql);
	}	
	
	function kit(&$result, $args=array())
	{
		$qr_sql = "
			SELECT SUBSTRING(ce.cod_barras_formatado,2,19) AS cd_barra, 
				   ce.cd_empresa, 
				   ce.cd_registro_empregado, 
				   ce.seq_dependencia, 
				   SUBSTRING(p.nome,1,26) AS nome,
				   ce.logradouro, 
				   SUBSTRING(ce.bairro,1,15) AS bairro, 
				   ce.cidade, 
				   TO_CHAR(ce.cep,'FM00000') || '-' || TO_CHAR(ce.complemento_cep,'FM000') AS cep, 				   
				   funcoes.fnc_codigo_barras_cep(cast(ce.cep as bigint), cast(ce.complemento_cep as bigint)) AS cep_net,
				   ce.uf,
				   ce.pagina
			  FROM eleicoes.cadastros_eleicoes ce
			  JOIN eleicoes.eleicao e
				ON e.ano_eleicao = ce.ano_eleicao
			  JOIN public.participantes p
				ON p.cd_empresa            = ce.cd_empresa
			   AND p.cd_registro_empregado = ce.cd_registro_empregado
			   AND p.seq_dependencia       = ce.seq_dependencia
			 WHERE e.id_eleicao = ".intval($args['id_eleicao'])."
			 ORDER BY cep, 
					  nome;";
		$result = $this->db->query($qr_sql);
	}
	
	function comboEleicoes( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT id_eleicao AS value,
				   ano_eleicao || '-' || cd_eleicao AS text
			  FROM eleicoes.eleicao
			 ORDER BY text DESC;";

		$result = $this->db->query($qr_sql);
	}
	
	function apuracao_abrir(&$result, $args=array())
	{
		if((intval($args['id_eleicao']) > 0) and (intval($args['qt_kit_recebido']) > 0) and (intval($args['cd_usuario']) > 0))
		{
			$qr_sql = "
				UPDATE eleicoes.eleicao
				   SET situacao            = 'A', 
					   num_votos           = ".intval($args['qt_kit_recebido']).",
					   cd_usuario_abertura = ".intval($args['cd_usuario']).",
					   dt_hr_abertura      = CURRENT_TIMESTAMP
				 WHERE id_eleicao = ".intval($args['id_eleicao']).";";
			#echo "<PRE>".$qr_sql."</PRE>"; exit;
			
			$result = $this->db->query($qr_sql);
		}
	}

	function apuracao_encerrar(&$result, $args=array())
	{
		if((intval($args['id_eleicao']) > 0) and (intval($args['cd_usuario']) > 0))
		{
			$qr_sql = "
				UPDATE eleicoes.eleicao
				   SET situacao              = 'F', 
					   dt_hr_fechamento      = CURRENT_TIMESTAMP,
					   cd_usuario_fechamento = ".intval($args['cd_usuario'])."
				 WHERE id_eleicao = ".intval($args['id_eleicao']).";";
			#echo "<PRE>".$qr_sql."</PRE>"; exit;
			
			$result = $this->db->query($qr_sql);
		}
	}
	
	function getLote($id_eleicao)
	{
		$qr_sql = "
			SELECT (nextval('eleicoes.eleicao_lote_' || (SELECT ano_eleicao FROM eleicoes.eleicao WHERE id_eleicao = ".intval($id_eleicao).") ) - 1) AS cd_lote;";

		$result = $this->db->query($qr_sql);	
		$ar_lote = $result->row_array();
		return $ar_lote['cd_lote'];
	}
	
	function apuracao_salvar(&$result, $args=array())
	{
		$cd_lote_voto = 0;
		$ar_retorno["cd_lote_voto_invalido"] = 0;
		$ar_retorno["cd_lote_voto"] = 0;
		
		if (intval($args['id_eleicao']) > 0)
		{		
			$qr_sql = "";
			
			if(intval($args['qt_total_invalido']) > 0)
			{
				$ar_retorno["cd_lote_voto_invalido"] = $this->getLote(intval($args['id_eleicao']));
				$qr_sql.= "
					INSERT INTO eleicoes.lotes_apuracao_eleicoes
						 (
						   ano_eleicao, 
						   cd_eleicao, 
						   cd_lote, 
						   cd_empresa, 
						   cd_registro_empregado, 
						   seq_dependencia, 
						   num_votos, 
						   dt_hora_lancamento, 
						   usu_lancamento
						 )
					VALUES 
						(
						   (SELECT ano_eleicao FROM eleicoes.eleicao WHERE id_eleicao = ".intval($args['id_eleicao'])."), 
						   (SELECT cd_eleicao FROM eleicoes.eleicao WHERE id_eleicao = ".intval($args['id_eleicao'])."), 
						   ".$ar_retorno["cd_lote_voto_invalido"] .", 
						   99, 
						   999999, 
						   99, 
						   ".intval($args['qt_total_invalido']).", 
						   CURRENT_TIMESTAMP, 
						   ".$args["cd_usuario"]."								 
						);";			
			}
			
			$nr_conta = 0;
			while (list($cd_candidato, $qt_voto) = each($args['ar_candidato'])) 
			{ 
				$ar_cand = explode("-",$cd_candidato);
				$cd_empresa            = $ar_cand[0];
				$cd_registro_empregado = $ar_cand[1];
				$seq_dependencia       = $ar_cand[2];
				if (intval($qt_voto) > 0)
				{
					$ar_retorno["cd_lote_voto"] = ($nr_conta == 0 ? $this->getLote(intval($args['id_eleicao'])) : $ar_retorno["cd_lote_voto"]);
					
					$qr_sql.= "
						INSERT INTO eleicoes.lotes_apuracao_eleicoes
							 (
							   ano_eleicao, 
							   cd_eleicao, 
							   cd_lote, 
							   cd_empresa, 
							   cd_registro_empregado, 
							   seq_dependencia, 
							   num_votos, 
							   tp_voto,
							   dt_hora_lancamento, 
							   usu_lancamento
							 )
						VALUES 
							(
							   (SELECT ano_eleicao FROM eleicoes.eleicao WHERE id_eleicao = ".intval($args['id_eleicao'])."), 
							   (SELECT cd_eleicao FROM eleicoes.eleicao WHERE id_eleicao = ".intval($args['id_eleicao'])."), 
							   ".$ar_retorno["cd_lote_voto"].", 
							   ".$cd_empresa.", 
							   ".$cd_registro_empregado.", 
							   ".$seq_dependencia.", 
							   ".$qt_voto.", 
							   ".(trim($args["tp_voto"]) != "" ? "'".trim($args["tp_voto"])."'" : "DEFAULT").",
							   CURRENT_TIMESTAMP, 
							   ".$args["cd_usuario"]."							 
							);";		
					$nr_conta++;
				}
			}		
		
			#echo "<PRE>".print_r($qr_sql,true)."</PRE>";exit;
		
			$qr_sql = ((trim($qr_sql) == "") ? "SELECT 1;" : $qr_sql);
			$result = $this->db->query($qr_sql);	
		}
		
		return $ar_retorno;
	}
	
	function lotes(&$result, $args=array())
	{
		$qr_sql = "
			SELECT lae.cd_lote,
				   ce.cd_cargo,
				   cce.nome AS ds_cargo,
				   ce.nome AS ds_candidato,
				   lae.num_votos AS qt_total_candidato,
				   TO_CHAR(lae.dt_hora_exclusao,'DD/MM/YYYY HH24:MI') AS dt_cancela,
				   uc.nome AS ds_usuario,
				   ce.posicao,
				   e.situacao
			  FROM eleicoes.eleicao e
			  JOIN eleicoes.candidatos_eleicoes ce
				ON ce.ano_eleicao = e.ano_eleicao
			   AND ce.cd_eleicao  = e.cd_eleicao
			  JOIN eleicoes.cargos_eleicoes cce
				ON cce.cd_cargo = ce.cd_cargo
			  JOIN eleicoes.lotes_apuracao_eleicoes lae
				ON lae.ano_eleicao           = ce.ano_eleicao
			   AND lae.cd_eleicao            = ce.cd_eleicao
			   AND lae.cd_empresa            = ce.cd_empresa
			   AND lae.cd_registro_empregado = ce.cd_registro_empregado
			   AND lae.seq_dependencia       = ce.seq_dependencia
			  LEFT JOIN projetos.usuarios_controledi uc
				ON uc.codigo = lae.usu_exclusao
			 WHERE e.id_eleicao = ".intval($args['id_eleicao'])."
			   AND cce.tp_cargo  = 'T'

			 UNION		  
						  
			SELECT lae.cd_lote,
				   0 AS cd_cargo,
				   'Kit Inválido' AS ds_cargo,
				   '' AS ds_candidato,
				   lae.num_votos AS qt_total_candidato,
				   TO_CHAR(lae.dt_hora_exclusao,'DD/MM/YYYY HH24:MI') AS dt_cancela,
				   uc.nome AS ds_usuario,
				   0 AS posicao ,
				   e.situacao
			  FROM eleicoes.eleicao e
			  JOIN eleicoes.lotes_apuracao_eleicoes lae
				ON lae.ano_eleicao           = e.ano_eleicao
			   AND lae.cd_eleicao            = e.cd_eleicao
			  LEFT JOIN projetos.usuarios_controledi uc
				ON uc.codigo = lae.usu_exclusao
			 WHERE e.id_eleicao = ".intval($args['id_eleicao'])."
			   AND lae.cd_empresa = 99
			   AND lae.cd_registro_empregado = 999999
			   AND lae.seq_dependencia = 99
			 ORDER BY cd_lote DESC,
					  cd_cargo";
			
		$result = $this->db->query($qr_sql);
	}
	
	function cancelar_lote(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE eleicoes.lotes_apuracao_eleicoes AS lae
			   SET dt_hora_exclusao = CURRENT_TIMESTAMP,
				   usu_exclusao = ".intval($args['cd_usuario'])."
			  FROM eleicoes.eleicao e
			 WHERE e.id_eleicao = ".intval($args['id_eleicao'])."
			   AND lae.ano_eleicao = e.ano_eleicao
			   AND lae.cd_eleicao = e.cd_eleicao
			   AND lae.cd_lote    = ".intval($args['cd_lote']).";";
		
		$result = $this->db->query($qr_sql);
	}
}
?>