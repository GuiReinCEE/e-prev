<?php
class Atendimento_encaminhamento_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function get_tipo()
	{
		$qr_sql = "
			SELECT cd_atendimento_encaminhamento_tipo AS value,
			       ds_atendimento_encaminhamento_tipo AS text
			  FROM projetos.atendimento_encaminhamento_tipo
			 WHERE dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_atendente()
	{
		$qr_sql = "
			SELECT codigo as value, 
			       nome as text
			  FROM projetos.usuarios_controledi 
			 WHERE indic_08 IN ('N', 'C', 'P', 'T', 'E') 
			   AND codigo = 99999 
			    OR (
			       divisao = 'GCM' 
			       AND 
			       tipo    NOT IN ('X', 'T')
			    )
			 ORDER BY nome;";

		return $this->db->query($qr_sql)->result_array();
	}

    function listar( &$result, &$count, $args=array() )
	{
		$sql = "
			SELECT a.cd_atendimento,
                   ae.cd_encaminhamento,
			       uc.guerra AS guerra_usuario,
				   a.indic_ativo,
			       a.cd_empresa,
			       a.cd_registro_empregado,
			       a.seq_dependencia,
				   p.nome,
			       TO_CHAR(a.dt_hora_inicio_atendimento,'DD/MM/YYYY HH24:MI:SS') AS dt_hora_inicio_atendimento,
			       ae.texto_encaminhamento AS texto_encaminhamento,
				   a.obs,
				   ao.texto_observacao,
				   ae.dt_cancelado,
				   ae.cd_atendimento_encaminhamento_tipo,
				   ae.ds_observacao_cancelamento,
				   aet.ds_atendimento_encaminhamento_tipo,
				   funcoes.get_usuario_nome(ae.cd_usuario_contrato_emprestimo_1) AS usuario_contrato_emprestimo_1,
				   TO_CHAR(ae.dt_contrato_emprestimo_1, 'DD/MM/YYYY HH24:MI:SS') AS dt_contrato_emprestimo_1,
				   funcoes.get_usuario_nome(ae.cd_usuario_contrato_emprestimo_2) AS usuario_contrato_emprestimo_2,
				   TO_CHAR(ae.dt_contrato_emprestimo_2, 'DD/MM/YYYY HH24:MI:SS') AS dt_contrato_emprestimo_2,				   
				   CASE WHEN ae.dt_cancelado IS NOT NULL THEN 'Cancelado'
                        WHEN ae.dt_retorno_encaminhamento IS NOT NULL THEN 'Processado'
			            ELSE 'Aberto'
			       END AS fl_encaminhamento,
				   CASE WHEN ae.dt_cancelado IS NOT NULL THEN 'gray'
                        WHEN ae.dt_retorno_encaminhamento IS NOT NULL THEN 'blue'
			            ELSE 'green'
			       END AS cor_encaminhamento				   
			  FROM projetos.atendimento a
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = a.id_atendente
			  JOIN projetos.atendimento_encaminhamento ae
			    ON ae.cd_atendimento = a.cd_atendimento
			  LEFT JOIN projetos.atendimento_encaminhamento_tipo aet
				ON aet.cd_atendimento_encaminhamento_tipo = ae.cd_atendimento_encaminhamento_tipo				
			  LEFT JOIN projetos.atendimento_observacao ao
				ON ao.cd_atendimento = a.cd_atendimento
			  LEFT JOIN public.participantes p
				ON p.cd_empresa            = a.cd_empresa
			   AND p.cd_registro_empregado = a.cd_registro_empregado
			   AND p.seq_dependencia       = a.seq_dependencia
			 WHERE 1 = 1
				".(((trim($args['dt_hora_inicio_atendimento_inicio']) != "") and  (trim($args['dt_hora_inicio_atendimento_fim']) != "")) ? " AND DATE_TRUNC('day', a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$args['dt_hora_inicio_atendimento_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_hora_inicio_atendimento_fim']."', 'DD/MM/YYYY')" : "")."
				".((trim($args['cd_empresa']) != "") ? " AND a.cd_empresa = ".intval($args['cd_empresa'])  : "")."
				".((trim($args['cd_registro_empregado']) != "") ? " AND a.cd_registro_empregado = ".intval($args['cd_registro_empregado'])  : "")."
				".((trim($args['seq_dependencia']) != "") ? " AND a.seq_dependencia = ".intval($args['seq_dependencia'])  : "")."
				".((trim($args['id_atendente']) != "") ? " AND a.id_atendente = ".intval($args['id_atendente'])  : "")."
				".((trim($args['cd_atendimento_encaminhamento_tipo']) != "") ? " AND ae.cd_atendimento_encaminhamento_tipo = ".intval($args['cd_atendimento_encaminhamento_tipo'])  : "")."
				".((trim($args['fl_encaminhamento']) == "a") ? " AND (a.dt_encaminhamento IS NULL OR ae.dt_retorno_encaminhamento IS NULL) AND (ae.dt_cancelado IS NULL)"  : "")."
				".((trim($args['fl_encaminhamento']) == "e") ? " AND (a.dt_encaminhamento IS NOT NULL AND ae.dt_retorno_encaminhamento IS NOT NULL) AND (ae.dt_cancelado IS NULL)"  : "")."
				".((trim($args['fl_encaminhamento']) == "c") ? " AND (ae.dt_cancelado IS NOT NULL)"  : "")."
				".((trim($args['nome']) != "") ? " AND UPPER(p.nome) like UPPER('%".trim($args['nome'])."%')"  : "")."
			 GROUP BY
						a.cd_atendimento,
						ae.cd_encaminhamento,
						uc.guerra,
						a.indic_ativo,
						a.cd_empresa,
						a.cd_registro_empregado,
						a.seq_dependencia,
						p.nome,
						a.dt_hora_inicio_atendimento,
						ae.texto_encaminhamento,
						a.obs,
						ao.texto_observacao,
						ae.dt_cancelado,
						ae.ds_observacao_cancelamento,
						ae.cd_atendimento_encaminhamento_tipo,
						aet.ds_atendimento_encaminhamento_tipo,
						usuario_contrato_emprestimo_1,
						dt_contrato_emprestimo_1,
						usuario_contrato_emprestimo_2,					   
						dt_contrato_emprestimo_2,							   
						fl_encaminhamento,
						cor_encaminhamento
		";
		$result = $this->db->query($sql);
	}


	function cancelar($args = array())
	{
		$sql = "
		UPDATE projetos.atendimento_encaminhamento 
		   SET dt_cancelado         	  = CURRENT_TIMESTAMP, 
		       id_atendente_retorno 	  = ".intval($args['cd_usuario_logado']).",
		       ds_observacao_cancelamento = ".(trim($args['ds_observacao_cancelamento']) != '' ? str_escape($args['ds_observacao_cancelamento']) : "DEFAULT")."
		 WHERE cd_atendimento    = ".intval($args['cd_atendimento'])." 
		   AND cd_encaminhamento = ".intval($args['cd_encaminhamento']).";";

		$this->db->query($sql);
	}

	function encerrar($args,&$msg=array())
	{
		$sql = "
		UPDATE projetos.atendimento_encaminhamento 
		SET dt_retorno_encaminhamento = CURRENT_TIMESTAMP, id_atendente_retorno = {id_atendente_retorno}
		WHERE cd_atendimento = {cd_atendimento} AND cd_encaminhamento = {cd_encaminhamento}
		";
		esc('{id_atendente_retorno}',$args['cd_usuario_logado'],$sql);
		esc('{cd_atendimento}',$args['cd_atendimento'],$sql);
		esc('{cd_encaminhamento}',$args['cd_encaminhamento'],$sql);

		$q = $this->db->query($sql);
		
		$msg[]='OK';
		
		return true;
	}

     function atendimento($cd)
    {
        $sql = "
            SELECT a.cd_atendimento,
                   CASE WHEN (a.indic_ativo = 'T') THEN 'Telefônico'
                        WHEN (a.indic_ativo = 'P') THEN 'Pessoal'
                        WHEN (a.indic_ativo = 'C') THEN 'Consulta'
                        WHEN (a.indic_ativo = 'E') THEN 'E-mail'
                        ELSE 'Não Informado'
                   END AS tp_atendimento,
                   TO_CHAR(a.dt_hora_inicio_atendimento,'DD/MM/YYYY HH24:MI') AS dt_atendimento,
                   a.cd_empresa,
                   a.cd_registro_empregado,
                   a.seq_dependencia,
                   p.nome AS nome_participante,
                   a.obs,
                   uc.guerra AS atendente
              FROM projetos.atendimento a
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = a.id_atendente
              LEFT JOIN public.participantes p
                ON p.cd_empresa            = a.cd_empresa
               AND p.cd_registro_empregado = a.cd_registro_empregado
               AND p.seq_dependencia       = a.seq_dependencia
             WHERE a.cd_atendimento = ".$cd;
        $row=array();
        $query = $this->db->query( $sql );

        $query=$this->db->query($sql);

        if($query->row_array())
        {
            $row=$query->row_array();
        }

        return $row;
    }

    function antendimento_encaminhamento($cd, $cd_encaminhamento)
    {
        $sql = "
				SELECT ae.cd_encaminhamento,
					   ae.cd_atendimento,
					   CASE WHEN ae.dt_cancelado IS NOT NULL
							THEN 'Cancelado'
							WHEN ae.dt_retorno_encaminhamento IS NOT NULL
							THEN 'Processado'
							ELSE 'Aberto'
					   END AS fl_atendimento,
					   uc.guerra AS solicitante,
					   TO_CHAR(ae.dt_encaminhamento,'DD/MM/YYYY HH24:MI') AS dt_solicitacao,
					   funcoes.get_usuario_nome(ae.id_atendente_retorno::INTEGER) AS atendente,
					   TO_CHAR(ae.dt_retorno_encaminhamento,'DD/MM/YYYY HH24:MI') AS dt_encaminhamento,
					   TO_CHAR(ae.dt_cancelado,'DD/MM/YYYY HH24:MI') AS dt_cancelado,
					   ae.texto_encaminhamento,
					   ae.cd_contrato_emprestimo_1,
					   ae.cd_contrato_emprestimo_2,
					   ae.cd_usuario_contrato_emprestimo_1,
					   ae.cd_usuario_contrato_emprestimo_2,
					   CASE WHEN ae.cd_usuario_contrato_emprestimo_1 IS NULL THEN 1
					        WHEN ae.cd_usuario_contrato_emprestimo_1 IS NOT NULL AND ae.cd_usuario_contrato_emprestimo_2 IS NULL THEN 2
					        ELSE 0
					   END AS id_confere_emprestimo,
					   funcoes.get_usuario_nome(ae.cd_usuario_contrato_emprestimo_1) AS usuario_contrato_emprestimo_1,
					   TO_CHAR(ae.dt_contrato_emprestimo_1, 'DD/MM/YYYY HH24:MI:SS') AS dt_contrato_emprestimo_1,
					   funcoes.get_usuario_nome(ae.cd_usuario_contrato_emprestimo_2) AS usuario_contrato_emprestimo_2,
					   TO_CHAR(ae.dt_contrato_emprestimo_2, 'DD/MM/YYYY HH24:MI:SS') AS dt_contrato_emprestimo_2,					   
					   ae.cd_atendimento_encaminhamento_tipo,
					   aet.ds_atendimento_encaminhamento_tipo,
					   ae.ds_observacao,
					   ae.ds_observacao_cancelamento
				  FROM projetos.atendimento_encaminhamento ae
				  JOIN projetos.usuarios_controledi uc
					ON uc.codigo = ae.id_atendente
				  LEFT JOIN projetos.atendimento_encaminhamento_tipo aet
					ON aet.cd_atendimento_encaminhamento_tipo = ae.cd_atendimento_encaminhamento_tipo
				 WHERE ae.cd_atendimento    = ".intval($cd)."
				   AND ae.cd_encaminhamento = ".intval($cd_encaminhamento)."
				 ORDER BY ae.dt_encaminhamento ASC
               ";

        $row=array();
        $query = $this->db->query( $sql );

        $query=$this->db->query($sql);

        if($query->row_array())
        {
            $row=$query->row_array();
        }

        return $row;
    }

    function info_atendimento($cd, &$result, $args=array())
    {
        $sql = "
            SELECT tp.nome_tela AS tela,
                   TO_CHAR(atc.dt_acesso,'HH24:MI') AS hr_hora,
                   lt.descricao AS tp_tela
              FROM projetos.atendimento_tela_capturada atc
              LEFT JOIN projetos.telas_programas tp
                ON tp.cd_tela = atc.cd_tela
              LEFT JOIN public.listas lt
                ON lt.codigo = tp.cd_programa_fceee
               AND lt.categoria = 'PRFC'
             WHERE atc.cd_atendimento = ".$cd."
             ORDER BY atc.dt_acesso ASC
          ";
        $result = $this->db->query($sql);
    }
	
	
    function combo_atendimento_encaminhamento_tipo(&$result, $args=array())
    {
        $qr_sql = "
					SELECT cd_atendimento_encaminhamento_tipo AS value,
						   ds_atendimento_encaminhamento_tipo AS text
					  FROM projetos.atendimento_encaminhamento_tipo
					 ORDER BY text
			      ";

        $result = $this->db->query($qr_sql);
    }	
	
	function encaminhamento_emprestimo(&$result, $args=array())
	{
		$qr_sql = "
					UPDATE projetos.atendimento_encaminhamento
					   SET cd_contrato_emprestimo_".intval($args['id_confere_emprestimo'])."         = ".intval($args['cd_contrato_emprestimo']).",
						   cd_usuario_contrato_emprestimo_".intval($args['id_confere_emprestimo'])." = ".intval($args['cd_usuario']).",
						   ds_observacao                                                             = ".str_escape($args['ds_observacao']).",
						   dt_contrato_emprestimo_".intval($args['id_confere_emprestimo'])."         = CURRENT_TIMESTAMP
					 WHERE cd_atendimento    = ".intval($args['cd_atendimento'])."
                       AND cd_encaminhamento = ".intval($args['cd_encaminhamento'])."				 
			      ";
		$result = $this->db->query($qr_sql);
	}

	function emprestimo(&$result, $args=array())
	{
		$qr_sql = "
					SELECT projetos.participante_nome(e.cd_empresa, e.cd_registro_empregado, e.seq_dependencia) AS nome,
					       e.cd_empresa,
						   e.cd_registro_empregado,
						   e.seq_dependencia,
						   UPPER(te.descricao) AS tipo_emprestimo,
						   TO_CHAR(e.dt_solicitacao,'DD/MM/YYYY') AS dt_solicitacao,
						   TO_CHAR(e.dt_deposito,'DD/MM/YYYY') AS dt_deposito,
						   TO_CHAR(e.dt_primeira_prestacao,'DD/MM/YYYY') AS dt_primeiro_pagamento,
						   TO_CHAR(e.dt_ultima_prestacao,'DD/MM/YYYY') AS dt_ultimo_pagamento,
						   e.nro_prestacoes,
						   e.vlr_prestacao,
						   e.vlr_solicitado,
						   e.perc_comprometimento,
						   e.montante_concedido,
						   e.vlr_concedido,
						   e.vlr_deposito,
						   e.cd_agencia AS agencia,
						   e.conta AS conta,
						   if.razao_social_nome AS banco
					  FROM public.emprestimos e
					  LEFT JOIN public.emprestimos_patrocinadoras ep
					    ON ep.seq_emprestimo_patroc = e.seq_emprestimo_patroc
				      LEFT JOIN public.tipos_emprestimos te
					    ON te.cd_tipo = ep.cd_tipo
					  LEFT JOIN instituicao_financeiras if
					    ON if.cd_instituicao = e.cd_instituicao
					   AND if.cd_agencia = '0'
					 WHERE e.cd_contrato = ".intval($args['cd_contrato_emprestimo'])."	
		          ";
		#echo "<PRE>$qr_sql</PRE>";
		$result = $this->db->query($qr_sql);		  
	}
	
    public function cadastroSalvar($args = array())
    {
		$cd_encaminhamento = intval($this->db->get_new_id('projetos.atendimento_encaminhamento', 'cd_encaminhamento'));
		
		$qr_sql = "
					INSERT INTO projetos.atendimento_encaminhamento
						 (
							cd_encaminhamento,
							cd_atendimento,
							cd_empresa,
							cd_registro_empregado,
							seq_dependencia,
							cd_atendente,
							id_atendente,
							dt_encaminhamento,
							texto_encaminhamento,
							cd_atendimento_encaminhamento_tipo
						 )
					VALUES
						 (
							".intval($cd_encaminhamento).",
							".(trim($args['cd_atendimento']) != '' ? intval($args['cd_atendimento']) : "DEFAULT").",
							".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
							".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT").",
							".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
							funcoes.get_usuario(".intval($args['cd_usuario'])."),
							".intval($args['cd_usuario']).",
							CURRENT_TIMESTAMP,
							".(trim($args['descricao']) != '' ? str_escape($args['descricao']) : "DEFAULT").",
							".intval($args['cd_atendimento_encaminhamento_tipo'])." 
						 );
					INSERT INTO projetos.envia_emails
						 (
							dt_envio,
							de,
							para,
							cc,
							cco,
							assunto,
							texto
						 )
					VALUES
						 (
							CURRENT_TIMESTAMP,
							'RAP Atendimento Encaminhamento',
							'gcmatendimento@eletroceee.com.br',
							'',
							'',
							'Encaminhamento de solicitação de Participante',
							'Registro de encaminhamento de solicitação de participante. 
------------------------------------------- 
Participante:  ".trim($args['cd_empresa'])."/".trim($args['cd_registro_empregado'])."/".trim($args['seq_dependencia'])."
Nome:  '|| (SELECT projetos.participante_nome(".trim($args['cd_empresa']).",".trim($args['cd_registro_empregado']).",".trim($args['seq_dependencia']).")) ||' 
-------------------------------------------  
Número do Atendimento:  ".intval($cd_encaminhamento)." 
Registrado por:  '|| funcoes.get_usuario_nome(".intval($args['cd_usuario']).") ||' 
-------------------------------------------  
Tipo:  ' || (SELECT ds_atendimento_encaminhamento_tipo FROM projetos.atendimento_encaminhamento_tipo WHERE cd_atendimento_encaminhamento_tipo = ".intval($args['cd_atendimento_encaminhamento_tipo'])." ) || ' 
-------------------------------------------  
Texto:
' || ".(trim($args['descricao']) != '' ? str_escape($args['descricao']) : "''")." || '
-------------------------------------------  
'
						 );						 
		          ";
        #echo "<PRE>".$qr_sql; exit;
		$this->db->query($qr_sql);

        return $cd_encaminhamento;				  
    }	

    public function salvar_tipo($cd_atendimento, $cd_encaminhamento, $cd_atendimento_encaminhamento_tipo, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.atendimento_encaminhamento
			   SET cd_atendimento_encaminhamento_tipo = ".intval($cd_atendimento_encaminhamento_tipo).",
			       cd_usuario_alteracao               = ".intval($cd_usuario).",
			       dt_alteracao                       = CURRENT_TIMESTAMP
			 WHERE cd_atendimento    = ".intval($cd_atendimento)."
			   AND cd_encaminhamento = ".intval($cd_encaminhamento).";";

		$this->db->query($qr_sql);	
	}
}
