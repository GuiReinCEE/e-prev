<?php
class Reclamacao_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function get_empresas()
    {
        $qr_sql = "
			SELECT cd_empresa AS value, 
				   sigla AS text
			  FROM public.patrocinadoras;";
			  
		return $this->db->query($qr_sql)->result_array();
    }

    public function get_planos()
	{
		$qr_sql = "
			SELECT cd_plano AS value,
				   descricao AS text 
			  FROM public.planos;";
			
		return $this->db->query($qr_sql)->result_array();
	}

	public function get_reclamacao_retorno_classificacao()
	{
		$qr_sql = "
			SELECT rr.cd_reclamacao_retorno_classificacao AS value, 
				   rr.ds_reclamacao_retorno_classificacao AS text
			  FROM projetos.reclamacao_retorno_classificacao rr
			 WHERE rr.dt_exclusao            IS NULL
			   AND rr.cd_reclamacao_retorno_classificacao > 0
			   AND rr.cd_reclamacao_retorno_classificacao_pai IS NULL
			 ORDER BY rr.ds_reclamacao_retorno_classificacao;";

		return $this->db->query($qr_sql)->result_array();
	}	

	public function get_reclamacao_programa()
	{
		$qr_sql = "
			SELECT rp.cd_reclamacao_programa AS value, 
				   rp.ds_reclamacao_programa AS text
			  FROM projetos.reclamacao_programa rp
			 WHERE rp.dt_exclusao            IS NULL
			   AND rp.cd_reclamacao_programa > 0
			 ORDER BY rp.ds_reclamacao_programa;";
			 
		return $this->db->query($qr_sql)->result_array();
	}	
	
	public function get_usuario_inclusao()
	{
		$qr_sql = "
			SELECT DISTINCT cd_usuario_inclusao AS value,
				   funcoes.get_usuario_nome(cd_usuario_inclusao) AS text
			  FROM projetos.reclamacao 
			 WHERE dt_exclusao IS NULL
			 ORDER BY 2;";
			 
		return $this->db->query($qr_sql)->result_array();
	}

	public function get_reclamacao_assunto($cd_reclamacao_assunto = 0)
	{
		$qr_sql = "
			SELECT ra.cd_reclamacao_assunto AS value, 
				   ra.ds_reclamacao_assunto AS text,
				   ra.ds_reclamacao_assunto
			  FROM projetos.reclamacao_assunto ra
			 WHERE ra.dt_exclusao 			 IS NULL
			   AND ra.cd_reclamacao_assunto > 0 
			   ".(trim($cd_reclamacao_assunto) != 0 ? "AND ra.cd_reclamacao_assunto = ".intval($cd_reclamacao_assunto) : "")."
			 ORDER BY ra.ds_reclamacao_assunto;";
		
		if(intval($cd_reclamacao_assunto) > 0)
		{
			return $this->db->query($qr_sql)->row_array();
		}	
		else
		{
			return $this->db->query($qr_sql)->result_array();
		} 
	}	
	
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT funcoes.nr_reclamacao(r.ano, r.numero, r.tipo) AS cd_reclamacao,
				   r.numero,
				   r.ano,
				   r.tipo,
				   r.cd_empresa, 
				   r.cd_registro_empregado, 
				   r.seq_dependencia, 
				   r.nome, 
				   (CASE WHEN r.tipo = 'S'
				         THEN 'SUG'
				         ELSE 'REC'
				   END) AS ds_tipo,	
				   (CASE WHEN r.tipo = 'S'
				         THEN 'info'
				         ELSE 'important'
				   END) AS ds_class_tipo,		   
				   TO_CHAR(r.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao,
				   TO_CHAR(r.dt_cancela, 'DD/MM/YYYY HH24:MI') AS dt_cancela,
				   r.descricao,
				   TO_CHAR(ra.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_encaminhado,
				   funcoes.get_usuario_nome(r.cd_usuario_inclusao) AS ds_usuario_reclamacao,
				   ra.cd_divisao,
				   funcoes.get_usuario_nome(ra.cd_usuario_responsavel) AS ds_usuario_responsavel,
				   TO_CHAR(ra.dt_prazo, 'DD/MM/YYYY') AS dt_prazo_classificacao,
				   TO_CHAR(ra.dt_prorrogacao, 'DD/MM/YYYY') AS dt_prorrogacao_classificacao,
				   TO_CHAR(ra.dt_prazo_acao, 'DD/MM/YYYY') AS dt_prazo_acao,
				   TO_CHAR(ra.dt_prorrogacao_acao, 'DD/MM/YYYY') AS dt_prorrogacao_acao,
				   TO_CHAR(r.dt_encerramento, 'DD/MM/YYYY HH24:MI') AS dt_encerramento,
				   TO_CHAR(rra.dt_retorno, 'DD/MM/YYYY') AS dt_retorno,
				   TO_CHAR(COALESCE(ran.dt_reclamacao_retorno, ran.dt_inclusao), 'DD/MM/YYYY HH24:MI') AS dt_classificacao,
				   ran.nr_ano_nc, 
				   ran.nr_nc,
				   funcoes.nr_nc(ran.nr_ano_nc, ran.nr_nc) AS ds_nc,
				   ran.ds_justificativa,
				   (CASE WHEN rrc.cd_reclamacao_retorno_classificacao_pai IS NOT NULL 
						 THEN (SELECT rrc2.ds_reclamacao_retorno_classificacao
								 FROM projetos.reclamacao_retorno_classificacao rrc2
								WHERE rrc2.cd_reclamacao_retorno_classificacao = rrc.cd_reclamacao_retorno_classificacao_pai) || ' - ' || rrc.ds_reclamacao_retorno_classificacao
						 ELSE rrc.ds_reclamacao_retorno_classificacao
					END) AS ds_reclamacao_retorno_classificacao,
				   rrc.cor
			  FROM projetos.reclamacao r
			  LEFT JOIN public.patrocinadoras patr
				ON patr.cd_empresa = r.cd_empresa
			  LEFT JOIN projetos.reclamacao_atendimento ra
				ON ra.numero = r.numero
			   AND ra.ano    = r.ano
			   AND ra.tipo   = r.tipo				
			  LEFT JOIN projetos.reclamacao_andamento ran
				ON ran.numero                  = r.numero
			   AND ran.ano                     = r.ano
			   AND ran.tipo                    = r.tipo
			   AND ran.tp_reclamacao_andamento = 'R' --RETORNO
			   AND ran.dt_exclusao IS NULL
			  LEFT JOIN projetos.reclamacao_retorno_classificacao rrc
			    ON rrc.cd_reclamacao_retorno_classificacao = ran.cd_reclamacao_retorno_classificacao
			  LEFT JOIN projetos.reclamacao_retorno_atendimento rra
				ON rra.numero                  = r.numero
			   AND rra.ano                     = r.ano
			   AND rra.tipo                    = r.tipo
			 WHERE r.dt_exclusao IS NULL
			   AND ran.dt_exclusao IS NULL
			   ".(intval($args['numero']) > 0 ? "AND r.numero = ".intval($args['numero']) : "")."
			   ".(intval($args['ano']) > 0 ? "AND r.ano = ".intval($args['ano']) : "")."
			   ".(trim($args['tipo']) != '' ? "AND r.tipo = '".trim($args['tipo'])."'" : "")."

			   ".(trim($args['fl_situacao']) == 'A' ? "AND ran.dt_inclusao IS NULL" : "")."
			   ".(trim($args['fl_situacao']) == 'C' ? "AND r.dt_cancela IS NOT NULL" : "")."
			   ".(trim($args['fl_situacao']) == 'T' ? "AND ran.dt_inclusao IS NULL AND (COALESCE(COALESCE(ra.dt_prorrogacao, ra.dt_prazo), CURRENT_DATE - 1) < CURRENT_DATE  OR COALESCE(COALESCE(ra.dt_prorrogacao_acao, ra.dt_prazo_acao), CURRENT_DATE - 1) < CURRENT_DATE)" : "")."
			   ".(trim($args['fl_situacao']) == 'E' ? "AND ran.dt_inclusao IS NOT NULL" : "")."

			   ".(trim($args['fl_prorrogada']) == 'S' ? "AND ra.dt_prorrogacao IS NOT NULL" : "")."
			   ".(trim($args['fl_prorrogada']) == 'N' ? "AND ra.dt_prorrogacao IS NULL" : "")."	

			   ".(trim($args['cd_empresa_patr'] != '') ? "AND patr.cd_empresa = ".intval($args['cd_empresa_patr']) : "")."

			   ".(trim($args['fl_tipo_cliente'] != '') ? "AND patr.tipo_cliente = '".trim($args['fl_tipo_cliente'])."'" : '')."

			   ".(intval($args['cd_empresa']) != '' ? "AND r.cd_empresa = ".intval($args['cd_empresa']) : "")."
			   ".(intval($args['cd_registro_empregado']) > 0 ? "AND r.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
			   ".(intval($args['seq_dependencia']) != '' ? "AND r.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
			   ".(trim($args['nome']) != '' ? "AND UPPER(funcoes.remove_acento(r.nome)) LIKE(UPPER(funcoes.remove_acento('%".$args["nome"]."%')))" : "")."
			   ".(trim($args['cd_plano']) != '' ? "AND r.cd_plano = ".intval($args['cd_plano']) : "")."

			   ".(trim($args['fl_participante']) == 'S' ? "AND COALESCE(r.cd_registro_empregado, 0) > 0" : "")."
			   ".(trim($args['fl_participante']) == 'N' ? "AND COALESCE(r.cd_registro_empregado, 0) = 0" : "")."
			
			   ".(((trim($args['dt_inclusao_ini']) != '') AND (trim($args['dt_inclusao_fim']) != '')) ? "AND CAST(r.dt_inclusao AS DATE) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")."

			   ".(((trim($args['dt_atendimento_ini']) != '') AND (trim($args['dt_atendimento_fim']) != '')) ? "AND CAST(ra.dt_inclusao AS DATE) BETWEEN TO_DATE('".$args['dt_atendimento_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_atendimento_fim']."', 'DD/MM/YYYY')" : "")."

			   ".(trim($args['cd_divisao']) != '' ? "AND ra.cd_divisao = '".trim($args['cd_divisao'])."'" : "")."
			   ".(intval($args['cd_usuario_responsavel']) > 0 ? "AND ra.cd_usuario_responsavel = ".intval($args['cd_usuario_responsavel']) : "")."

			   ".(((trim($args['dt_prazo_acao_ini']) != '') AND (trim($args['dt_prazo_acao_fim']) != '')) ? "AND COALESCE(ra.dt_prorrogacao_acao, ra.dt_prazo_acao) BETWEEN TO_DATE('".$args['dt_prazo_acao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_prazo_acao_fim']."', 'DD/MM/YYYY')" : "")."

			   ".(((trim($args['dt_prazo_classificacao_ini']) != '') AND (trim($args['dt_prazo_classificacao_fim']) != '')) ? "AND COALESCE(ra.dt_prorrogacao, ra.dt_prazo) BETWEEN TO_DATE('".$args['dt_prazo_classificacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_prazo_classificacao_fim']."', 'DD/MM/YYYY')" : "")."

			   ".(((trim($args['dt_encerrado_ini']) != '') AND (trim($args['dt_encerrado_fim']) != '')) ? "AND CAST(r.dt_encerramento AS DATE) BETWEEN TO_DATE('".$args['dt_encerrado_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_encerrado_fim']."', 'DD/MM/YYYY')" : "")."

			   ".(intval($args['cd_reclamacao_retorno_classificacao']) > 0 ? "AND (ran.cd_reclamacao_retorno_classificacao = ".intval($args['cd_reclamacao_retorno_classificacao'])." OR rrc.cd_reclamacao_retorno_classificacao_pai = ".intval($args['cd_reclamacao_retorno_classificacao']).")" : "")."

			   ".(trim($args['cd_reclamacao_programa'] != '') ? "AND r.cd_reclamacao_programa = ".intval($args['cd_reclamacao_programa']) : "")."
			   ".(trim($args['cd_reclamacao_assunto'] != '') ? "AND r.cd_reclamacao_assunto = ".intval($args['cd_reclamacao_assunto']) : "")."

			   ".(trim($args['cd_usuario_inclusao']) != '' ? "AND r.cd_usuario_inclusao = ".intval($args['cd_usuario_inclusao']) : '')."
			 ORDER BY cd_reclamacao DESC;";

		return $this->db->query($qr_sql)->result_array();
	}	
	
	public function get_reclamacao_origem()
	{
		$qr_sql = "
			SELECT ro.cd_reclamacao_origem AS value, 
				   ro.ds_reclamacao_origem AS text
			  FROM projetos.reclamacao_origem ro
			 WHERE ro.dt_exclusao IS NULL
			 ORDER BY ro.ds_reclamacao_origem;";

		return $this->db->query($qr_sql)->result_array();
	}	
	
	public function get_verifica_encerramento($numero, $ano, $tipo)
	{
		$qr_sql = "
			SELECT COUNT(*) AS fl_encerramento
			  FROM projetos.reclamacao_andamento ra
			 WHERE ra.numero                  = ".intval($numero)."
			   AND ra.ano                     = ".intval($ano)."
			   AND ra.tipo                    = '".trim($tipo)."'
			   AND ra.tp_reclamacao_andamento = 'R'
			   AND ra.dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->row_array();
	}
	
	public function verifica_responsavel($numero, $ano, $tipo, $cd_usuario)
	{
		$qr_sql = "
			SELECT COUNT(*) AS fl_responsavel
			  FROM projetos.reclamacao_atendimento ra
			 WHERE ra.numero                  = ".intval($numero)."
			   AND ra.ano                     = ".intval($ano)."
			   AND ra.tipo                    = '".trim($tipo)."'
			   AND ra.cd_usuario_responsavel  = ".intval($cd_usuario).";";

		return $this->db->query($qr_sql)->row_array();
	}	

	public function verifica_atendimento($numero, $ano, $tipo)
	{
		$qr_sql = "
			SELECT COUNT(*) AS fl_atendimento
			  FROM projetos.reclamacao_atendimento ra
			 WHERE ra.numero = ".intval($numero)."
			   AND ra.ano    = ".intval($ano)."
			   AND ra.tipo   = '".trim($tipo)."';";

		return $this->db->query($qr_sql)->row_array();
	}	
	
	public function verifica_gerente($numero, $ano, $tipo, $cd_usuario)
	{
		$qr_sql = "
			SELECT COUNT(*) AS fl_gerente
			  FROM projetos.reclamacao_atendimento ra
			  JOIN projetos.usuarios_controledi uc
				ON uc.divisao = ra.cd_divisao
			   AND (uc.tipo = 'G' OR (uc.indic_01 = 'S' AND uc.tipo <> 'X'))
			 WHERE ra.numero = ".intval($numero)."
			   AND ra.ano    = ".intval($ano)."
			   AND ra.tipo   = '".trim($tipo)."'
			   AND uc.codigo = ".intval($cd_usuario).";";		   

		return $this->db->query($qr_sql)->row_array();
	}	
	
	public function get_validacao_comite_confirmada($numero, $ano, $tipo, $cd_usuario)
	{
		$qr_sql = "
			SELECT dt_confirma
			  FROM projetos.reclamacao_comite  
			 WHERE numero            = ".intval($numero)."
			   AND ano               = ".intval($ano)."
			   AND tipo              = '".trim($tipo)."'
			   AND cd_usuario_comite = ".intval($cd_usuario).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_plano_participante($cd_empresa, $cd_registro_empregado, $seq_dependencia)
	{
		$qr_sql = "
			SELECT cd_plano 
			  FROM public.participantes 
			 WHERE cd_empresa            = ".intval($cd_empresa)." 
			   AND cd_registro_empregado = ".intval($cd_registro_empregado)." 
			   AND seq_dependencia       = ".intval($seq_dependencia).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_classificacao()
	{
		$qr_sql = "
			SELECT rr.cd_reclamacao_retorno_classificacao AS value, 
			       (CASE WHEN rr.cd_reclamacao_retorno_classificacao_pai IS NOT NULL 
				         THEN (SELECT rrc2.ds_reclamacao_retorno_classificacao
					             FROM projetos.reclamacao_retorno_classificacao rrc2
					            WHERE rrc2.cd_reclamacao_retorno_classificacao = rr.cd_reclamacao_retorno_classificacao_pai
					              AND rrc2.dt_exclusao IS NULL) || ' - ' || rr.ds_reclamacao_retorno_classificacao
				     ELSE rr.ds_reclamacao_retorno_classificacao
			       END) AS text
			  FROM projetos.reclamacao_retorno_classificacao rr
			 WHERE rr.dt_exclusao            IS NULL
			   AND rr.cd_reclamacao_retorno_classificacao > 0
			   AND (SELECT COUNT(*)
			          FROM projetos.reclamacao_retorno_classificacao rr2
			         WHERE rr2.cd_reclamacao_retorno_classificacao_pai = rr.cd_reclamacao_retorno_classificacao
			           AND rr2.dt_exclusao IS NULL) = 0
			ORDER BY rr.ds_reclamacao_retorno_classificacao;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_relatorio($args = array())
	{
		$qr_sql = "
			SELECT funcoes.nr_reclamacao(r.ano, r.numero, r.tipo) AS cd_reclamacao,
				   r.numero,
				   r.ano,
				   r.tipo,
				   r.cd_empresa, 
				   r.cd_registro_empregado, 
				   r.seq_dependencia, 
				   r.nome, 
				   r.descricao,	
				   ras.ds_reclamacao_assunto,			   
				   TO_CHAR(COALESCE(ra.dt_prorrogacao, ra.dt_prazo), 'DD/MM/YYYY') AS dt_prazo,
				   TO_CHAR(COALESCE(ra.dt_prorrogacao_acao, ra.dt_prazo_acao), 'DD/MM/YYYY') AS dt_prazo_acao,
				   TO_CHAR(COALESCE(rra.dt_retorno, ra.dt_prazo_acao), 'DD/MM/YYYY') AS dt_retorno,
				   TO_CHAR(r.dt_classificacao, 'DD/MM/YYYY HH24:MI') AS dt_classificacao,
				   ra.cd_divisao,
				   rana.descricao AS ds_acao
			  FROM projetos.reclamacao r
			  LEFT JOIN projetos.reclamacao_comite_retorno rcr
			    ON rcr.numero = r.numero
			   AND rcr.ano    = r.ano
			   AND rcr.tipo   = r.tipo	
			  LEFT JOIN projetos.reclamacao_atendimento ra
				ON ra.numero = r.numero
			   AND ra.ano    = r.ano
			   AND ra.tipo   = r.tipo	
			  LEFT JOIN projetos.reclamacao_andamento ran
				ON ran.numero                  = r.numero
			   AND ran.ano                     = r.ano
			   AND ran.tipo                    = r.tipo
			   AND ran.tp_reclamacao_andamento = 'R' --RETORNO	
              LEFT JOIN projetos.reclamacao_andamento rana
				ON rana.numero                  = r.numero
			   AND rana.ano                     = r.ano
			   AND rana.tipo                    = r.tipo
			   AND rana.tp_reclamacao_andamento = 'A'
		      LEFT JOIN projetos.reclamacao_retorno_classificacao rrc
			    ON rrc.cd_reclamacao_retorno_classificacao = ran.cd_reclamacao_retorno_classificacao
			  LEFT JOIN projetos.reclamacao_assunto ras 
			    ON ras.cd_reclamacao_assunto = r.cd_reclamacao_assunto
			  LEFT JOIN projetos.reclamacao_retorno_atendimento rra
				ON rra.numero                  = r.numero
			   AND rra.ano                     = r.ano
			   AND rra.tipo                    = r.tipo	 
			 WHERE r.dt_exclusao IS NULL
			   AND ran.dt_exclusao IS NULL
			   AND r.dt_cancela  IS NULL
			   AND r.tipo = 'R'
			   ".(((trim($args['dt_inclusao_ini']) != '') AND (trim($args['dt_inclusao_fim']) != '')) ? "AND CAST(r.dt_inclusao AS DATE) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(intval($args['cd_reclamacao_retorno_classificacao']) > 0 ? "AND ran.cd_reclamacao_retorno_classificacao = ".intval($args['cd_reclamacao_retorno_classificacao']) : "")."
			   ".(trim($args['fl_situacao']) == 'NTR' ? "AND ran.dt_inclusao IS NULL" : "")."
			   ".(trim($args['fl_situacao']) == 'TFP' ? "AND COALESCE(ra.dt_prorrogacao, ra.dt_prazo) < ran.dt_inclusao::date" : "")."
			   ".(trim($args['cd_reclamacao_assunto'] != '') ? "AND r.cd_reclamacao_assunto = ".intval($args['cd_reclamacao_assunto']) : "")."
			   ".(trim($args['fl_situacao']) == 'NCC' ? "AND rcr.dt_parecer_final IS NULL AND rcr.numero IS NOT NULL" : "")."

			 ORDER BY cd_reclamacao DESC;";

		return $this->db->query($qr_sql)->result_array();
	}	
	
	public function reclamacao($numero, $ano, $tipo)
	{
		$qr_sql = "
			SELECT funcoes.nr_reclamacao(r.ano, r.numero, r.tipo) AS cd_reclamacao,
			       r.numero, 
				   r.ano, 
				   r.tipo, 
				   (CASE WHEN r.tipo = 'S'
				         THEN 'Sugestão'
				         ELSE 'Reclamação'
				   END) AS ds_tipo,	
				   r.cd_empresa, 
				   r.cd_registro_empregado, 
				   r.seq_dependencia, 
				   r.nome, 
				   COALESCE(p.email,'') AS email,
				   COALESCE(p.email_profissional,'') AS email_profissional,
				   TO_CHAR(p.ddd,'FM(00) ') || TO_CHAR(p.telefone,'FM99999-9999') AS telefone,
				   p.ramal,
				   TO_CHAR(p.celular,'FM99999-9999') AS celular,
				   p.logradouro, 
				   p.bairro, 
				   p.cidade, 
				   p.unidade_federativa AS uf, 
				   TO_CHAR(p.cep,'FM00000') || '-' || TO_CHAR(p.complemento_cep,'FM000') AS cep,							   
				   r.descricao, 
				   r.cd_reclamacao_origem,
				   r.cd_reclamacao_programa,
				   r.cd_reclamacao_assunto,
				   r.cd_atendimento,
				   TO_CHAR(r.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao,
				   TO_CHAR(r.dt_encerramento, 'DD/MM/YYYY HH24:MI') AS dt_encerramento,
				   r.cd_usuario_inclusao,
				   funcoes.get_usuario_nome(r.cd_usuario_inclusao) AS ds_usuario_inclusao, 
				   TO_CHAR(r.dt_cancela, 'DD/MM/YYYY HH24:MI') AS dt_cancela,
				   funcoes.get_usuario_nome(r.cd_usuario_cancela) AS ds_usuario_cancela,
				   TO_CHAR(r.dt_exclusao, 'DD/MM/YYYY HH24:MI') AS dt_exclusao,
				   COALESCE(ra.cd_usuario_responsavel,0) AS cd_usuario_responsavel,
				   funcoes.get_usuario_nome(ra.cd_usuario_responsavel) AS ds_usuario_responsavel,
				   ra.cd_divisao,
				   TO_CHAR(ra.dt_prazo, 'DD/MM/YYYY') AS dt_prazo,
				   TO_CHAR(ra.dt_prazo_acao, 'DD/MM/YYYY') AS dt_prazo_acao,
				   TO_CHAR(ra.dt_prorrogacao, 'DD/MM/YYYY') AS dt_prorrogacao,
				   TO_CHAR(ra.dt_prorrogacao_acao, 'DD/MM/YYYY') AS dt_prorrogacao_acao,
				   r.cd_plano,
				   r.email AS email_novo,
				   r.telefone_1,
				   r.telefone_2,
				   rcr.dt_concorda,
				   rc.cd_reclamacao_comite,				  
				   TO_CHAR(COALESCE(ran.dt_reclamacao_retorno, ran.dt_inclusao), 'DD/MM/YYYY HH24:MI') AS dt_classificacao,
				   rc.ds_justificativa_confirma
			  FROM projetos.reclamacao r
			  LEFT JOIN projetos.reclamacao_atendimento ra
				ON ra.numero = r.numero
			   AND ra.ano    = r.ano
			   AND ra.tipo   = r.tipo
			   LEFT JOIN projetos.reclamacao_andamento ran
				ON ran.numero                  = r.numero
			   AND ran.ano                     = r.ano
			   AND ran.tipo                    = r.tipo
			   AND ran.tp_reclamacao_andamento = 'R' 
			  LEFT JOIN projetos.reclamacao_comite_retorno rcr
				ON rcr.numero = r.numero
			   AND rcr.ano    = r.ano
			   AND rcr.tipo   = r.tipo
			  LEFT JOIN public.participantes p
				ON p.cd_empresa            = r.cd_empresa						
			   AND p.cd_registro_empregado = r.cd_registro_empregado						
			   AND p.seq_dependencia       = r.seq_dependencia		
			  LEFT JOIN projetos.reclamacao_comite rc
			    ON rc.numero = ra.numero
			   AND rc.ano    = ra.ano
			   AND rc.tipo   = ra.tipo									
			 WHERE r.numero = ".intval($numero)."
			   AND r.ano    = ".intval($ano)."
			   AND r.tipo   = '".trim($tipo)."';";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_membros($numero, $ano, $tipo)
	{
		$qr_sql = "
			SELECT cd_usuario_comite AS value,
				   funcoes.get_usuario_nome(cd_usuario_comite) AS text
			  FROM projetos.reclamacao_comite
			 WHERE fl_confirma IS NULL
			   AND numero = ".intval($numero)."
			   AND ano    = ".intval($ano)."
			   AND tipo   = '".trim($tipo)."'
			   AND fl_confirma IS NULL;";		
		
		return $this->db->query($qr_sql)->result_array();
	}

	public function dispensar_membro($numero, $ano, $tipo, $args = array())
	{
		$qr_sql = " 
			UPDATE projetos.reclamacao_comite
			   SET ds_justificativa_confirma = ".(trim($args['ds_justificativa_confirma']) != '' ? str_escape($args['ds_justificativa_confirma']) :  "DEFAULT").",
			   	   fl_confirma		         = 'D',
			   	   cd_usuario_confirma       = ".trim($args['cd_usuario']).",
			       dt_confirma 				 = CURRENT_TIMESTAMP
			 WHERE numero            = ".intval($numero)."
			   AND ano               = ".intval($ano)."
			   AND tipo              = '".trim($tipo)."'
			   AND cd_usuario_comite = ".intval($args['cd_usuario_comite']).";";

		$this->db->query($qr_sql);
	}	

	public function listar_reclamacao_anterior($args = array())
	{
		$qr_sql = "
			SELECT funcoes.nr_reclamacao(r.ano, r.numero, r.tipo) AS cd_reclamacao,
				   r.numero,
				   r.ano,
				   r.tipo,
				   r.cd_empresa, 
				   r.cd_registro_empregado, 
				   r.seq_dependencia, 
				   r.nome, 
				   (CASE WHEN r.tipo = 'S'
				         THEN 'SUG'
				         ELSE 'REC'
				   END) AS ds_tipo,	
				   (CASE WHEN r.tipo = 'S'
				         THEN 'info'
				         ELSE 'important'
				   END) AS ds_class_tipo,		   
				   TO_CHAR(r.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao,
				   TO_CHAR(r.dt_cancela, 'DD/MM/YYYY HH24:MI') AS dt_cancela,
				   r.descricao,
				   TO_CHAR(ra.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_encaminhado,
				   funcoes.get_usuario_nome(r.cd_usuario_inclusao) AS ds_usuario_reclamacao,
				   ra.cd_divisao,
				   funcoes.get_usuario_nome(ra.cd_usuario_responsavel) AS ds_usuario_responsavel,
				   TO_CHAR(ra.dt_prazo, 'DD/MM/YYYY') AS dt_prazo,
				   TO_CHAR(ra.dt_prorrogacao, 'DD/MM/YYYY') AS dt_prorrogacao,
				   TO_CHAR(r.dt_encerramento, 'DD/MM/YYYY HH24:MI') AS dt_encerramento,
				   TO_CHAR(rra.dt_retorno, 'DD/MM/YYYY HH24:MI') AS dt_retorno,
				   TO_CHAR(COALESCE(ran.dt_reclamacao_retorno, ran.dt_inclusao), 'DD/MM/YYYY HH24:MI') AS dt_classificacao,
				   ran.nr_ano_nc, 
				   ran.nr_nc,
				   funcoes.nr_nc(ran.nr_ano_nc, ran.nr_nc) AS ds_nc,
				   ran.ds_justificativa,
				   (CASE WHEN rrc.cd_reclamacao_retorno_classificacao_pai IS NOT NULL 
						 THEN (SELECT rrc2.ds_reclamacao_retorno_classificacao
								 FROM projetos.reclamacao_retorno_classificacao rrc2
								WHERE rrc2.cd_reclamacao_retorno_classificacao = rrc.cd_reclamacao_retorno_classificacao_pai) || ' - ' || rrc.ds_reclamacao_retorno_classificacao
						 ELSE rrc.ds_reclamacao_retorno_classificacao
					END) AS ds_reclamacao_retorno_classificacao,
				   rrc.cor
			  FROM projetos.reclamacao r
			  LEFT JOIN projetos.reclamacao_atendimento ra
				ON ra.numero = r.numero
			   AND ra.ano    = r.ano
			   AND ra.tipo   = r.tipo						
			  LEFT JOIN projetos.reclamacao_andamento ran
				ON ran.numero                  = r.numero
			   AND ran.ano                     = r.ano
			   AND ran.tipo                    = r.tipo
			   AND ran.tp_reclamacao_andamento = 'R' --RETORNO
			  LEFT JOIN projetos.reclamacao_retorno_classificacao rrc
			    ON rrc.cd_reclamacao_retorno_classificacao = ran.cd_reclamacao_retorno_classificacao
			  LEFT JOIN projetos.reclamacao_retorno_atendimento rra
				ON rra.numero                  = r.numero
			   AND rra.ano                     = r.ano
			   AND rra.tipo                    = r.tipo	   
			 WHERE TO_CHAR(r.numero,'FM9999') ||'/'|| TO_CHAR(r.ano,'FM0000') || '/' || r.tipo <> '".intval($args['numero']).'/'.intval($args['ano']).'/'.$args['tipo']."'
			   AND r.cd_registro_empregado = ".intval($args['cd_registro_empregado'])." 
			   AND r.seq_dependencia       = ".intval($args['seq_dependencia'])." 
			   AND r.dt_exclusao IS NULL
			   AND CAST(r.dt_inclusao AS DATE) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."','DD/MM/YYYY')
			 ORDER BY cd_reclamacao DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function salvar_reclamacao_seguro($args)
	{
		#### RECLAMACAO DE SEGURO, ABRIR UM OS PARA GF (OS: 39829) 16/01/2014 #####
		#$args['cd_usuario_atendente'] = 10; #RTORTORELLI
		
		#### RECLAMACAO DE SEGURO, ABRIR UM OS PARA GP (OS: 42847) 04/03/2015 #####
		$args['cd_usuario_atendente'] = 40; #ERODRIGUES
		
		switch (intval($args["cd_reclamacao_origem"])) 
		{
			case 1  : $cd_origem = "FAP3"; break; #CALLCENTER
			case 2  : $cd_origem = "FAP4"; break; #Pessoal
			case 3  : $cd_origem = "FAP2"; break; #E-mail
			case 4  : $cd_origem = "FAP1"; break; #Correspondencia
			case 5  : $cd_origem = "FAP3"; break; #Telefone
			default : $cd_origem = "FAP5"; #Consulta
		}			
		
		$cd_atividade = intval($this->db->get_new_id("projetos.atividades", "numero"));

		$qr_sql = "
					INSERT INTO projetos.atividades 
						 ( 
							numero,
							tipo,
							sistema,
							dt_cad, 
							titulo,
							descricao,
							area,
							cod_atendente,
							divisao,
							cod_solicitante,
							status_atual,
							tipo_solicitacao,
							dt_limite,
							cd_plano,
							cd_empresa,
							cd_registro_empregado,
							cd_sequencia,
							cd_atendimento,
							forma
						 )
			        VALUES 
				         ( 
							".intval($cd_atividade).",
						 	'e',
							139, -- SEGURO
							CURRENT_TIMESTAMP,         
							'Reclamação de ".$args['nome']."', 
							".(trim($args['descricao']) != '' ? str_escape($args['descricao']) :  "DEFAULT").", 
							'GAP',
							".intval($args['cd_usuario_atendente']).",
							".(trim($args['cd_gerencia_solicitante']) != '' ? "'".$args['cd_gerencia_solicitante']."'" : "DEFAULT").",
							".intval($args['cd_usuario']).",
							'AIST',       
							'RESG', --RECLAMACAO SEGURO   
							funcoes.dia_util('DEPOIS', CURRENT_DATE, 5), -- 5 DIAS UTEIS
							".(trim($args['cd_plano']) != '' ? $args['cd_plano'] : "DEFAULT").",             
							".(trim($args['cd_empresa']) != '' ? $args['cd_empresa'] : "DEFAULT").",             
							".(trim($args['cd_registro_empregado']) != '' ? $args['cd_registro_empregado'] : "DEFAULT").",             
							".(trim($args['seq_dependencia']) != '' ? $args['seq_dependencia'] : "DEFAULT").",             
							".(intval($args['cd_atendimento']) > 0 ? intval($args['cd_atendimento']) : "DEFAULT").",        
							".(trim($cd_origem) != '' ? "'".$cd_origem."'"  : "DEFAULT")."
				          );
						  
					INSERT INTO projetos.envia_emails 
						 (
							dt_envio, 
							de, 
							para, 
							cc, 
							cco, 
							assunto, 
							texto,
							cd_evento
						 ) 
					VALUES 
						 (
							CURRENT_TIMESTAMP,
						   'Controle de Reclamação',
						   funcoes.get_usuario(".intval($args['cd_usuario_atendente']).") || '@eletroceee.com.br',     
						   funcoes.get_usuario(funcoes.get_usuario_gerente(".intval($args['cd_usuario_atendente']).")) || '@eletroceee.com.br;' || 
						   funcoes.get_usuario(funcoes.get_usuario_gerente_substituto(".intval($args['cd_usuario_atendente']).")) || '@eletroceee.com.br;' || 
						   'gapsuporte@eletroceee.com.br',     
						   '',
						   'Reclamação de ".$args["nome"]."',
						   'Prezado(a): ' || funcoes.get_usuario_nome(".intval($args['cd_usuario_atendente']).") || '

			ATENÇÃO, você deve dar tratamento a RECLAMAÇÃO registrada, favor verificar através do link abaixo.

			".site_url('atividade/atividade_solicitacao/index/'.intval($cd_atividade))."

			DADOS:
			Dt Limite: ' || TO_CHAR(funcoes.dia_util('DEPOIS', CURRENT_DATE, 5),'DD/MM/YYYY') ||'
			Nome: ".$args["nome"]."
			Emp/RE/Seq: ".intval($args['cd_empresa'])."/".intval($args['cd_registro_empregado'])."/".intval($args['seq_dependencia'])."
			Descrição: ' || ".(trim($args['descricao']) == "" ? "DEFAULT" : str_escape($args['descricao'])).",
										66
						 );
				  ";		
				  
		#echo "<PRE>".print_r($args,true).br(2).$qr_sql.br(5)."</PRE>"; #exit; 	  
		if($this->db->query($qr_sql))
		{
			header("Location: ".site_url('atividade/atividade_solicitacao/index/'.intval($cd_atividade)));
			exit;
		}
	}
	
	public function salvar_reclamacao($args = array())
	{
		$qr_sql = " 
			INSERT INTO projetos.reclamacao
				 (
				   tipo,
				   cd_empresa, 
				   cd_registro_empregado, 
				   seq_dependencia, 
				   cd_plano,
				   nome, 
				   cd_reclamacao_origem,
				   cd_reclamacao_programa,
				   cd_reclamacao_assunto,
				   cd_atendimento,
				   descricao, 
				   email, 
				   telefone_1, 
				   telefone_2, 
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
			VALUES 
				 (
				   ".(trim($args['tipo']) != '' ? "'".$args['tipo']."'" : "DEFAULT").",
				   ".(trim($args['cd_empresa']) != '' ? $args['cd_empresa'] : "DEFAULT").",
				   ".(trim($args['cd_registro_empregado']) != '' ? $args['cd_registro_empregado'] : "DEFAULT").",
				   ".(trim($args['seq_dependencia']) != '' ? $args['seq_dependencia'] : "DEFAULT").",
				   ".(trim($args['cd_plano']) != '' ? $args['cd_plano'] : "DEFAULT").",
				   ".(trim($args['nome']) != '' ? "'".$args['nome']."'" : "DEFAULT").",
				   ".(trim($args['cd_reclamacao_origem']) != '' ? $args['cd_reclamacao_origem'] : "DEFAULT").",
				   ".(trim($args['cd_reclamacao_programa']) != '' ? $args['cd_reclamacao_programa'] : "DEFAULT").",
				   ".(trim($args['cd_reclamacao_assunto']) != '' ? $args['cd_reclamacao_assunto'] : "DEFAULT").",
				   ".(intval($args['cd_atendimento']) != '' ? intval($args['cd_atendimento']) : "DEFAULT").",
				   ".(trim($args['descricao']) != '' ?  str_escape($args['descricao']) : "DEFAULT").",
				   ".(trim($args['email']) != '' ? "'".$args['email']."'" : "DEFAULT").",
				   ".(trim($args['telefone_1']) != '' ? "'".$args['telefone_1']."'" : "DEFAULT").",
				   ".(trim($args['telefone_2']) != '' ? "'".$args['telefone_2']."'" : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";

		$this->db->query($qr_sql);	
				
		$qr_sql = "
			SELECT CAST(numero AS TEXT) || '/' || CAST(ano AS TEXT) || '/' || tipo AS cd_reclamacao
			  FROM projetos.reclamacao
			 WHERE cd_usuario_inclusao = ".intval($args['cd_usuario'])."
			 ORDER BY dt_inclusao DESC
			 LIMIT 1;";

		$row = $this->db->query($qr_sql)->row_array();			

		return $row['cd_reclamacao'];
	}	

	public function atualizar_reclamacao($numero, $ano, $tipo, $args = array())
	{
		$qr_sql = " 
			UPDATE projetos.reclamacao
			   SET cd_empresa             = ".(trim($args['cd_empresa']) != '' ? $args['cd_empresa'] : "DEFAULT").",
				   cd_registro_empregado  = ".(trim($args['cd_registro_empregado']) != '' ? $args['cd_registro_empregado'] : "DEFAULT").",
				   seq_dependencia        = ".(trim($args['seq_dependencia']) != '' ? $args['seq_dependencia'] : "DEFAULT").",
				   cd_plano               = ".(trim($args['cd_plano']) != '' ? $args['cd_plano'] : "DEFAULT").",
				   nome                   = ".(trim($args['nome']) != '' ? "'".$args['nome']."'" : "DEFAULT").",
				   cd_reclamacao_origem   = ".(trim($args['cd_reclamacao_origem']) != '' ? $args['cd_reclamacao_origem'] : "DEFAULT").",
				   cd_reclamacao_programa = ".(trim($args['cd_reclamacao_programa']) != '' ? $args['cd_reclamacao_programa'] : "DEFAULT").",
				   cd_reclamacao_assunto  = ".(trim($args['cd_reclamacao_assunto']) != '' ? $args['cd_reclamacao_assunto'] : "DEFAULT").",
				   descricao              = ".(trim($args['descricao']) != '' ?  str_escape($args['descricao']) : "DEFAULT").",
				   email                  = ".(trim($args['email']) != '' ? "'".$args['email']."'" : "DEFAULT").",
				   telefone_1             = ".(trim($args['telefone_1']) != '' ? "'".$args['telefone_1']."'" : "DEFAULT").",
				   telefone_2             = ".(trim($args['telefone_2']) != '' ? "'".$args['telefone_2']."'" : "DEFAULT").",
				   cd_usuario_alteracao   = ".intval($args['cd_usuario']).",
				   dt_alteracao           = CURRENT_TIMESTAMP
			 WHERE numero = ".intval($numero)."
			   AND ano    = ".intval($ano)."
			   AND tipo   = '".trim($tipo)."';";		
					  
		$this->db->query($qr_sql);
	}
	
	public function cancelar_reclamacao($numero, $ano, $tipo, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE projetos.reclamacao
			   SET cd_usuario_cancela = ".intval($cd_usuario).",
			       dt_cancela         = CURRENT_TIMESTAMP
			 WHERE numero = ".intval($numero)."
			   AND ano    = ".intval($ano)."
			   AND tipo   = '".trim($tipo)."';";

		$this->db->query($qr_sql);
	}	
	
	public function atendimento($numero, $ano, $tipo)
	{
		$qr_sql = "
			SELECT funcoes.nr_reclamacao(ra.ano, ra.numero, ra.tipo) AS cd_reclamacao,
			       r.numero,
				   r.ano,
				   r.tipo,
				   (CASE WHEN r.tipo = 'S'
				         THEN 'Sugestão'
				         ELSE 'Reclamação'
				   END) AS ds_tipo,	
				   ra.ds_justificativa_prorrogacao,
				   ra.cd_divisao,
				   ra.cd_usuario_responsavel,
				   funcoes.get_usuario_nome(ra.cd_usuario_responsavel) AS ds_usuario_reponsavel,
				   TO_CHAR(ra.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao,
				   funcoes.get_usuario_nome(ra.cd_usuario_inclusao) AS ds_usuario_inclusao,
				   funcoes.get_usuario_nome(ra.cd_usuario_atualizacao) AS ds_usuario_atualizacao,
				   TO_CHAR(ra.dt_prazo, 'DD/MM/YYYY') AS dt_prazo,
				   TO_CHAR(ra.dt_prorrogacao, 'DD/MM/YYYY') AS dt_prorrogacao,
				   TO_CHAR(ra.dt_prazo_acao, 'DD/MM/YYYY') AS dt_prazo_acao,
				   TO_CHAR(ra.dt_prorrogacao_acao, 'DD/MM/YYYY') AS dt_prorrogacao_acao,
				   (CASE WHEN ra.numero IS NULL AND ra.ano IS NULL AND ra.tipo IS NULL
				        THEN 0
				        ELSE 1
				   END) AS cd_operacao,
				   r.cd_reclamacao_programa,
				   ra.cd_usuario_atualizacao,
				   TO_CHAR(ra.dt_atualizacao, 'DD/MM/YYYY HH24:MI') AS dt_atualizacao,
				   TO_CHAR(r.dt_encerramento, 'DD/MM/YYYY HH24:MI') AS dt_encerramento,
				   TO_CHAR(r.dt_cancela, 'DD/MM/YYYY HH24:MI') AS dt_cancela,
				   rc.cd_reclamacao_comite,
				   ro.ds_reclamacao_origem,
				   rr.cd_divisao_reencaminhamento, 
				   rr.cd_usuario_responsavel_reencaminhamento,
				   rr.cd_reclamacao_reencaminhamento,
				   rr.ds_justificativa_reencaminhamento,
				   rcr.dt_concorda,
				   d.nome AS ds_divisao,
				   r.cd_usuario_inclusao,
				   ra.arquivo,
				   ra.arquivo_nome,
				   ra.tp_prorrogacao
		      FROM projetos.reclamacao r
			  LEFT JOIN projetos.reclamacao_atendimento ra
				ON ra.numero = r.numero
			   AND ra.ano    = r.ano					  
			   AND ra.tipo   = r.tipo
			  LEFT JOIN projetos.reclamacao_comite_retorno rcr
				ON rcr.numero = r.numero
			   AND rcr.ano    = r.ano
			   AND rcr.tipo   = r.tipo
			  LEFT JOIN projetos.divisoes d
			    ON d.codigo = ra.cd_divisao
			  LEFT JOIN projetos.reclamacao_origem ro
			    ON ro.cd_reclamacao_origem = r.cd_reclamacao_origem
			  LEFT JOIN projetos.reclamacao_reencaminhamento rr
			   	ON rr.cd_reclamacao_reencaminhamento = (SELECT MAX(rr.cd_reclamacao_reencaminhamento)
														  FROM projetos.reclamacao_reencaminhamento rr 
														 WHERE rr.numero = ra.numero
														   AND rr.ano    = ra.ano					  
														   AND rr.tipo   = ra.tipo)
			  LEFT JOIN projetos.reclamacao_comite rc
			    ON rc.numero = ra.numero
			   AND rc.ano    = ra.ano
			   AND rc.tipo   = ra.tipo			   
			 WHERE r.numero = ".intval($numero)."
			   AND r.ano    = ".intval($ano)."
			   AND r.tipo   = '".trim($tipo)."';";
		
		return $this->db->query($qr_sql)->row_array();
	}	

	public function reencaminhamento($numero, $ano, $tipo)
	{
		$qr_sql = "
			SELECT rr.cd_usuario_responsavel_reencaminhamento,
				   rr.cd_usuario_inclusao,
				   d.nome AS ds_divisao,
				   uc.divisao AS cd_divisao_inclusao,
				   funcoes.get_usuario_nome(rr.cd_usuario_inclusao) AS ds_usuario_inclusao,
				   funcoes.get_usuario_nome(rr.cd_usuario_responsavel_reencaminhamento) AS ds_usuario_responsavel_reencaminhamento,
				   rr.cd_divisao_reencaminhamento,
				   rr.ds_justificativa_reencaminhamento,
				   TO_CHAR(rr.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao
			  FROM projetos.reclamacao_reencaminhamento rr
			  LEFT JOIN projetos.divisoes d
			    ON d.codigo = rr.cd_divisao_reencaminhamento
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = rr.cd_usuario_inclusao
			 WHERE rr.numero = ".intval($numero)."
			   AND rr.ano    = ".intval($ano)."
			   AND rr.tipo   = '".trim($tipo)."';";
		
		return $this->db->query($qr_sql)->result_array();
	}

	public function get_usuarios($cd_divisao)
    {
		$qr_sql = "
			SELECT codigo AS value,
				   nome AS text
			  FROM projetos.usuarios_controledi
			 WHERE divisao = '".trim($cd_divisao)."'
			   AND tipo NOT IN ('X')
			 ORDER BY nome;";
				  
		return $this->db->query($qr_sql)->result_array();
	}

	public function get_ferias($cd_usuario)
	{
		$qr_sql = "
			SELECT TO_CHAR(brf.dt_ini_ferias,'DD/MM/YYYY') AS dt_ferias_ini, 
			       TO_CHAR(brf.dt_fim_ferias,'DD/MM/YYYY') AS dt_ferias_fim 
			  FROM public.benef_rh_ferias brf
			 WHERE brf.cd_empresa            = 9
			   AND brf.seq_dependencia       = 0
			   AND brf.cd_registro_empregado = (SELECT COALESCE(cd_registro_empregado,0) FROM projetos.usuarios_controledi WHERE codigo = ".intval($cd_usuario).")
			   AND CURRENT_DATE BETWEEN brf.dt_ini_ferias AND brf.dt_fim_ferias;";

		return $this->db->query($qr_sql)->row_array();
	}
	
	public function atendimento_get_usuario($cd_divisao, $cd_usuario_responsavel)
	{
		$qr_sql = "
			SELECT uc.codigo AS value,
				   uc.nome AS text
			  FROM projetos.usuarios_controledi uc
			 WHERE (
						(uc.divisao = '".trim($cd_divisao)."' AND tipo NOT IN ('X'))
				    	OR
						uc.codigo = ".intval($cd_usuario_responsavel)."
				   );";

		return $this->db->query($qr_sql)->result_array();
	}
	
	public function salvar_atendimento($args = array())
	{
		$cd_reclamacao_reencaminhamento = intval($this->db->get_new_id('projetos.reclamacao_reencaminhamento', 'cd_reclamacao_reencaminhamento'));
	
		$qr_sql = " 
			INSERT INTO projetos.reclamacao_atendimento
				 (
				   numero,
				   ano,
				   tipo,
				   cd_divisao,
				   cd_usuario_responsavel,
				   dt_prazo,
				   dt_prazo_acao,
				   cd_usuario_inclusao
				 )
			VALUES
				 (
				   ".(trim($args['numero']) != '' ? intval($args['numero']) : "DEFAULT").",
				   ".(trim($args['ano']) != '' ? intval($args['ano']) : "DEFAULT").",
				   ".(trim($args['tipo']) != '' ? "'".$args['tipo']."'" : "DEFAULT").",
				   ".(trim($args['cd_divisao']) != '' ? "'".$args['cd_divisao']."'" : "DEFAULT").",
				   ".(trim($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
				   ".(trim($args['dt_prazo']) != '' ? "TO_DATE('".$args['dt_prazo']."','DD/MM/YYYY')" : "DEFAULT").",
				   ".(trim($args['dt_prazo_acao']) != '' ? "TO_DATE('".$args['dt_prazo_acao']."','DD/MM/YYYY')" : "DEFAULT").",
				   ".intval($args['cd_usuario'])."
				 );
				 
			INSERT INTO projetos.reclamacao_reencaminhamento
				 (
				   cd_reclamacao_reencaminhamento,
				   numero, 
				   ano, 
				   tipo,
				   cd_divisao_reencaminhamento, 
				   cd_usuario_responsavel_reencaminhamento, 
				   cd_usuario_inclusao
				 )
			VALUES
				 (
				   ".intval($cd_reclamacao_reencaminhamento).",
				   ".(trim($args['numero']) != '' ? intval($args['numero']) : "DEFAULT").",
				   ".(trim($args['ano']) != '' ? intval($args['ano']) : "DEFAULT").",
				   ".(trim($args['tipo']) != '' ? "'".$args['tipo']."'" : "DEFAULT").",
				   ".(trim($args['cd_divisao']) != '' ? "'".$args['cd_divisao']."'" : "DEFAULT").",
				   ".(trim($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
				   ".intval($args['cd_usuario'])."
				 );";

		$this->db->query($qr_sql);	
	}	

	public function atualizar_atendimento($args = array())
	{
		$qr_sql = "
			UPDATE projetos.reclamacao_atendimento
			   SET cd_usuario_atualizacao 		= ".intval($args['cd_usuario']).",
				   dt_atualizacao         		= CURRENT_TIMESTAMP
			 WHERE numero = ".intval($args['numero'])."
			   AND ano    = ".intval($args['ano'])."
			   AND tipo   = '".trim($args['tipo'])."';";	

		$this->db->query($qr_sql);	
	}
	
	public function salvar_atendimento_prorrogacao($args = array())
	{
		$qr_sql = " 
			UPDATE projetos.reclamacao_atendimento
			   SET tp_prorrogacao               = ".(intval($args['tp_prorrogacao']) > 0 ? intval($args['tp_prorrogacao']) : "DEFAULT").",
			       dt_prorrogacao_acao     		= ".(trim($args['dt_prorrogacao_acao']) != '' ? "TO_DATE('".$args['dt_prorrogacao_acao']."','DD/MM/YYYY')" : "DEFAULT").",
			       dt_prorrogacao         		= ".(trim($args['dt_prorrogacao']) != '' ? "TO_DATE('".$args['dt_prorrogacao']."','DD/MM/YYYY')" : "DEFAULT").",
				   ds_justificativa_prorrogacao = ".(trim($args['ds_justificativa_prorrogacao']) != '' ? str_escape($args['ds_justificativa_prorrogacao']) : "DEFAULT").",
				   arquivo                      = ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
				   arquivo_nome                 = ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
				   cd_usuario_atualizacao 		= ".intval($args['cd_usuario']).",
				   dt_atualizacao         		= CURRENT_TIMESTAMP
			 WHERE numero = ".intval($args['numero'])."
			   AND ano    = ".intval($args['ano'])."
			   AND tipo   = '".$args['tipo']."';";

		$this->db->query($qr_sql);	
	}	
	
	public function salvar_atendimento_reencaminhamento($args = array())
	{
		$qr_sql = " 
			INSERT INTO projetos.reclamacao_reencaminhamento
				 (
				   numero, 
				   ano, 
				   tipo,
				   cd_divisao_reencaminhamento, 
				   cd_usuario_responsavel_reencaminhamento, 
				   ds_justificativa_reencaminhamento, 
				   cd_usuario_inclusao
				 )
			VALUES
				 (
				   ".(trim($args['numero']) != '' ? intval($args['numero']) : "DEFAULT").",
				   ".(trim($args['ano']) != '' ? intval($args['ano']) : "DEFAULT").",
				   ".(trim($args['tipo']) != '' ? "'".$args['tipo']."'" : "DEFAULT").",
				   ".(trim($args['cd_divisao_reencaminhamento']) != '' ? "'".$args['cd_divisao_reencaminhamento']."'" : "DEFAULT").",
				   ".(trim($args['cd_usuario_responsavel_reencaminhamento']) != '' ? intval($args['cd_usuario_responsavel_reencaminhamento']) : "DEFAULT").",
				   ".(trim($args['ds_justificativa_reencaminhamento']) != '' ? str_escape($args['ds_justificativa_reencaminhamento']) : "DEFAULT").",
				   ".intval($args['cd_usuario'])."
				 );
				 
			UPDATE projetos.reclamacao_atendimento
			   SET dt_prazo                          = ".(trim($args['dt_prazo']) != '' ? "TO_DATE('".$args['dt_prazo']."','DD/MM/YYYY')" : "DEFAULT").",
			       dt_prazo_acao                     = ".(trim($args['dt_prazo_acao']) != '' ? "TO_DATE('".$args['dt_prazo_acao']."','DD/MM/YYYY')" : "DEFAULT").",
			       cd_divisao						 = ".(trim($args['cd_divisao_reencaminhamento']) != '' ? "'".$args['cd_divisao_reencaminhamento']."'" : "DEFAULT").",
			       cd_usuario_responsavel			 = ".(trim($args['cd_usuario_responsavel_reencaminhamento']) != '' ? intval($args['cd_usuario_responsavel_reencaminhamento']) : "DEFAULT").",
				   ds_justificativa_reencaminhamento = ".(trim($args['ds_justificativa_reencaminhamento']) != '' ? str_escape($args['ds_justificativa_reencaminhamento']) : "DEFAULT").",
				   cd_usuario_atualizacao 		     = ".intval($args['cd_usuario']).",
				   dt_atualizacao         		     = CURRENT_TIMESTAMP
		     WHERE numero = ".intval($args['numero'])."
		       AND ano    = ".intval($args['ano'])."
		       AND tipo   = '".$args['tipo']."';";
			   
		$this->db->query($qr_sql);	
	}	

	public function listar_acompanhamento($numero, $ano, $tipo)
	{
		$qr_sql = "
			SELECT numero, 
				   ano, 
				   tipo, 
				   ds_acompanhamento,
				   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
			  FROM projetos.reclamacao_acompanhamento
			 WHERE numero   = ".intval($numero)."
			   AND ano      = ".intval($ano)."
			   AND tipo     = '".trim($tipo)."'
			   AND dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function salvar_acompanhamento($args = array())
	{
		$qr_sql = " 
			INSERT INTO projetos.reclamacao_acompanhamento
				 (
				   numero, 
				   ano, 
				   tipo,
				   ds_acompanhamento, 
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
			VALUES 
				 (
				   ".(trim($args['numero']) != '' ? intval($args['numero']) : "DEFAULT").",
				   ".(trim($args['ano']) != '' ? intval($args['ano']) : "DEFAULT").",
				   ".(trim($args['tipo']) != '' ? "'".trim($args['tipo'])."'" : "DEFAULT").",
				   ".(trim($args['ds_acompanhamento']) != '' ? str_escape($args['ds_acompanhamento']) : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";

		$this->db->query($qr_sql);
	}

	public function listar_anexo($numero, $ano, $tipo)
	{
		$qr_sql = "
			SELECT numero, 
				   ano, 
				   tipo, 
				   arquivo,
				   arquivo_nome,
				   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
			  FROM projetos.reclamacao_anexo
			 WHERE numero   = ".intval($numero)."
			   AND ano      = ".intval($ano)."
			   AND tipo     = '".trim($tipo)."'
			   AND dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function salvar_anexo($args = array())
	{
		$qr_sql = " 
			INSERT INTO projetos.reclamacao_anexo
				 (
				   numero, 
				   ano, 
				   tipo,
				   arquivo, 
				   arquivo_nome, 
				   cd_usuario_inclusao
				 )
			VALUES 
				 (
				   ".(trim($args['numero']) != '' ? intval($args['numero']) : "DEFAULT").",
				   ".(trim($args['ano']) != '' ? intval($args['ano']) : "DEFAULT").",
				   ".(trim($args['tipo']) != '' ? "'".trim($args['tipo'])."'" : "DEFAULT").",
				   ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
				   ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
				   ".intval($args['cd_usuario'])."
				 );";

		$this->db->query($qr_sql);
	}

	public function acao_retorno($numero, $ano, $tipo)
	{
		$qr_sql = "
			SELECT funcoes.nr_reclamacao(r.ano, r.numero, r.tipo) AS cd_reclamacao,
			       r.numero, 
				   r.ano, 
				   r.tipo,  
				   r.nome,
				   rcr.fl_concorda,
				   rcr.dt_concorda,
				   rcr.ds_justificativa_concorda,
				   COALESCE(ra.cd_usuario_responsavel, 0) AS cd_usuario_responsavel,
				   TO_CHAR(r.dt_encerramento,'DD/MM/YYYY HH24:MI') AS dt_encerramento,
				   TO_CHAR(r.dt_cancela,'DD/MM/YYYY HH24:MI') AS dt_cancela,
				   rcr.fl_retorno,
				   r.cd_usuario_inclusao
			  FROM projetos.reclamacao r
			  LEFT JOIN projetos.reclamacao_atendimento ra
				ON ra.numero = r.numero
			   AND ra.ano    = r.ano
			   AND ra.tipo   = r.tipo
			  LEFT JOIN projetos.reclamacao_comite_retorno rcr
				ON rcr.numero = r.numero
			   AND rcr.ano    = r.ano
			   AND rcr.tipo   = r.tipo
			 WHERE r.numero = ".intval($numero)."
			   AND r.ano    = ".intval($ano)."
			   AND r.tipo   = '".trim($tipo)."'
			   AND dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->row_array();
	}
	
	public function get_reclamacao_retorno_classificacao_tipo($cd_reclamacao_retorno_classificacao_pai)
	{
		$qr_sql = "
			SELECT rr.cd_reclamacao_retorno_classificacao AS value, 
				   rr.ds_reclamacao_retorno_classificacao AS text
			  FROM projetos.reclamacao_retorno_classificacao rr
			 WHERE rr.dt_exclusao            IS NULL
			   AND rr.cd_reclamacao_retorno_classificacao_pai = ".intval($cd_reclamacao_retorno_classificacao_pai)."
			 ORDER BY rr.ds_reclamacao_retorno_classificacao;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_usuario_callcenter($cd_usuario)
	{
		$qr_sql = " 
			SELECT COUNT(*)
			  FROM projetos.usuarios_controledi 
			 WHERE tipo                != 'X'
			   AND divisao             = 'GCM'
			   AND nr_ramal_callcenter IS NOT NULL
			   AND codigo = ".intval($cd_usuario).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_validacao_comite($numero, $ano, $tipo, $cd_usuario = '')
    {
		$qr_sql = "
			SELECT fl_confirma,
				   CASE WHEN fl_confirma = 'S' THEN 'Confirma'
						WHEN fl_confirma = 'N' THEN 'Não Confirma'
						ELSE 'Não Respondeu'
				   END AS ds_confirma,
				   TO_CHAR(dt_confirma, 'DD/MM/YYYY HH24:MI:SS') AS dt_confirma,
				   ds_justificativa_confirma,
				   fl_voto,
				   funcoes.get_usuario_nome(cd_usuario_comite) AS ds_usuario_comite
			  FROM projetos.reclamacao_comite
			 WHERE numero = ".intval($numero)."
			   AND ano    = ".intval($ano)."
			   AND tipo   = '".trim($tipo)."'
			   ".(trim($cd_usuario) != '' ? "AND cd_usuario_comite NOT IN (".intval($cd_usuario).")" : "")."
			 ORDER BY dt_confirma DESC;";
				  
		return $this->db->query($qr_sql)->result_array();
	}

	public function acao($numero, $ano, $tipo)
	{
		$qr_sql = "
			SELECT ra.cd_reclamacao_andamento,
			       funcoes.nr_reclamacao(ra.ano, ra.numero, ra.tipo) AS cd_reclamacao,
			       ra.numero, 
				   ra.ano, 
				   ra.tipo, 
				   ra.descricao,
				   TO_CHAR(ra.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao,
				   funcoes.get_usuario_nome(ra.cd_usuario_inclusao) AS ds_usuario_inclusao,
				   TO_CHAR(rat.dt_prazo, 'DD/MM/YYYY') AS dt_prazo,
				   TO_CHAR(rat.dt_prorrogacao, 'DD/MM/YYYY') AS dt_prorrogacao,
				   rc.cd_reclamacao_comite				   
			  FROM projetos.reclamacao_andamento ra
			  LEFT JOIN projetos.reclamacao_atendimento rat
				ON rat.numero = ra.numero
			   AND rat.ano    = ra.ano						
			   AND rat.tipo   = ra.tipo						
			  LEFT JOIN projetos.reclamacao_comite rc
			    ON rc.numero = ra.numero
			   AND rc.ano    = ra.ano
			   AND rc.tipo   = ra.tipo
			 WHERE ra.numero                  = ".intval($numero)."
			   AND ra.ano                     = ".intval($ano)."
			   AND ra.tipo                    = '".trim($tipo)."'
			   AND ra.tp_reclamacao_andamento = 'A';";
			   
		return $this->db->query($qr_sql)->row_array();
	}	
	
	public function classificacao($numero, $ano, $tipo)
	{
		$qr_sql = "
			SELECT ra.cd_reclamacao_andamento,
			       funcoes.nr_reclamacao(ra.ano, ra.numero, ra.tipo) AS cd_reclamacao,
			       ra.numero, 
				   ra.ano, 
				   ra.tipo, 					   
				   ra.descricao,
				   TO_CHAR(ra.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao,
				   funcoes.get_usuario_nome(ra.cd_usuario_inclusao) AS ds_usuario_inclusao,
				   ra.cd_reclamacao_retorno_classificacao,
				   ra.nr_nc,
				   ra.nr_ano_nc,
				   ra.ds_justificativa,
				   (CASE WHEN rrc.cd_reclamacao_retorno_classificacao_pai IS NOT NULL 
				         THEN (SELECT rrc2.cd_reclamacao_retorno_classificacao 
				                 FROM projetos.reclamacao_retorno_classificacao rrc2
				                WHERE rrc2.cd_reclamacao_retorno_classificacao = rrc.cd_reclamacao_retorno_classificacao_pai)
					     ELSE ra.cd_reclamacao_retorno_classificacao
				   END) AS cd_reclamacao_retorno_classificacao_pai,
				   (CASE WHEN rrc.cd_reclamacao_retorno_classificacao_pai IS NOT NULL 
						 THEN (SELECT rrc2.ds_reclamacao_retorno_classificacao
								 FROM projetos.reclamacao_retorno_classificacao rrc2
								WHERE rrc2.cd_reclamacao_retorno_classificacao = rrc.cd_reclamacao_retorno_classificacao_pai) || ' - ' || rrc.ds_reclamacao_retorno_classificacao
						 ELSE rrc.ds_reclamacao_retorno_classificacao
					END) AS ds_reclamacao_retorno_classificacao,
				   rc.cd_reclamacao_comite
			  FROM projetos.reclamacao_andamento ra
			  LEFT JOIN projetos.reclamacao_retorno rr
			    ON rr.cd_reclamacao_retorno = ra.cd_reclamacao_retorno
			  LEFT JOIN projetos.reclamacao_comite rc
			    ON rc.numero = ra.numero
			   AND rc.ano    = ra.ano
			   AND rc.tipo   = ra.tipo
			  LEFT JOIN projetos.reclamacao_retorno_classificacao rrc
			    ON rrc.cd_reclamacao_retorno_classificacao = ra.cd_reclamacao_retorno_classificacao
			 WHERE ra.numero                  = ".intval($numero)."
			   AND ra.ano                     = ".intval($ano)."
			   AND ra.tipo                    = '".trim($tipo)."'
			   AND ra.tp_reclamacao_andamento = 'R'
			   AND ra.dt_exclusao IS NULL;";
			   	
		return $this->db->query($qr_sql)->row_array();
	}

	public function retorno_carrega($cd_reclamacao_retorno_classificacao_pai)
	{
		$qr_sql = "
			SELECT rrc.ds_reclamacao_retorno_classificacao AS text,
				   rrc.cd_reclamacao_retorno_classificacao AS value
			  FROM projetos.reclamacao_retorno_classificacao rrc
			 WHERE rrc.cd_reclamacao_retorno_classificacao_pai = ".intval($cd_reclamacao_retorno_classificacao_pai)."
			   AND rrc.dt_exclusao IS NULL;";
			   
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function salvar_acao($args = array())
	{
		$qr_sql = " 
			INSERT INTO projetos.reclamacao_andamento
				 (
				   numero, 
				   ano, 
				   tipo,
				   descricao, 
				   cd_usuario_inclusao,
				   tp_reclamacao_andamento
				 )
			VALUES 
				 (
				   ".(trim($args['numero']) != '' ? intval($args['numero']) : "DEFAULT").",
				   ".(trim($args['ano']) != '' ? intval($args['ano']) : "DEFAULT").",
				   ".(trim($args['tipo']) != '' ? "'".trim($args['tipo'])."'" : "DEFAULT").",
				   ".(trim($args['descricao']) != '' ? str_escape($args['descricao']) : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   'A'
				 );";

		$this->db->query($qr_sql);
	}

	public function atualizar_acao($cd_reclamacao_andamento, $args)
	{
		$qr_sql = " 
			UPDATE projetos.reclamacao_andamento
			   SET descricao = ".(trim($args['descricao']) != '' ? str_escape($args['descricao']) : "DEFAULT")."
			 WHERE cd_reclamacao_andamento = ".intval($cd_reclamacao_andamento)."
			   AND tp_reclamacao_andamento = 'A';";	

		$this->db->query($qr_sql);	
	}	

	public function get_usuarios_comite()
    {
		$qr_sql = "
			SELECT codigo,
				   nome
			  FROM projetos.usuarios_controledi
			 WHERE indic_12 = '*'
			   AND tipo     NOT IN ('X')
			   AND codigo   NOT IN (348)
			 ORDER BY nome;";
				  
		return $this->db->query($qr_sql)->result_array();
	}

	public function salvar_classificacao($args = array(), $fl_classificacao = FALSE)
	{
		$qr_sql = " 
			INSERT INTO projetos.reclamacao_andamento
				 (
				   numero, 
				   ano, 
				   tipo,
				   cd_reclamacao_retorno_classificacao,
				   nr_nc,
				   nr_ano_nc,
				   ds_justificativa,
				   fl_encaminhar_comite,
				   descricao,
				   tp_reclamacao_andamento,
				   cd_usuario_inclusao
				 )
			VALUES 
				 (
				   ".(trim($args['numero']) != '' ? intval($args['numero']) : "DEFAULT").",
				   ".(trim($args['ano']) != '' ? intval($args['ano']) : "DEFAULT").",
				   ".(trim($args['tipo']) != '' ? "'".trim($args['tipo'])."'" : "DEFAULT").",
				   ".(intval($args['cd_reclamacao_retorno_classificacao']) == 0 ? intval($args['cd_reclamacao_retorno_classificacao_pai']) : intval($args['cd_reclamacao_retorno_classificacao'])).",
				   ".(intval($args['nr_nc']) > 0 ? intval($args['nr_nc']) : "DEFAULT").",
				   ".(intval($args['nr_ano_nc']) > 0 ? intval($args['nr_ano_nc']) : "DEFAULT").",
				   ".(trim($args['ds_justificativa']) != '' ? str_escape($args['ds_justificativa']) : "DEFAULT").",
				   ".(trim($args['fl_encaminhar_comite']) != '' ? "'".$args['fl_encaminhar_comite']."'" : "DEFAULT").",
				   'Encerrado',
				   'R',
				   ".intval($args['cd_usuario'])."
				 );";

		if(trim($args['tipo']) == 'S' OR trim($args['fl_encaminhar_comite']) == 'N' OR (intval($args['nr_nc']) > 0 AND (intval($args['nr_ano_nc']) > 0)))
		{
			$qr_sql .= "
		  		UPDATE projetos.reclamacao
		  		   SET dt_encerramento      = CURRENT_TIMESTAMP,
		  		       dt_alteracao         = CURRENT_TIMESTAMP,
		  		       cd_usuario_alteracao = ".intval($args['cd_usuario'])."
		  		 WHERE numero = ".intval($args['numero'])."
				   AND ano    = ".intval($args['ano'])."
				   AND tipo   = '".trim($args['tipo'])."';";
		}

		if($fl_classificacao)
		{
			$qr_sql .= "
		  		UPDATE projetos.reclamacao
		  		   SET dt_classificacao     = CURRENT_TIMESTAMP,
		  		       dt_alteracao         = CURRENT_TIMESTAMP,
		  		       cd_usuario_alteracao = ".intval($args['cd_usuario'])."
		  		 WHERE numero = ".intval($args['numero'])."
				   AND ano    = ".intval($args['ano'])."
				   AND tipo   = '".trim($args['tipo'])."';";
		}

		$this->db->query($qr_sql);
	}

	public function salvar_reclamacao_comite_retorno($args)
	{
		$qr_sql = " 
			INSERT INTO projetos.reclamacao_comite_retorno
				(
				   numero, 
				   ano, 
				   tipo,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
			VALUES 
				 (
				   ".(trim($args['numero']) != '' ? intval($args['numero']) : "DEFAULT").",
				   ".(trim($args['ano']) != '' ? intval($args['ano']) : "DEFAULT").",
				   ".(trim($args['tipo']) != '' ? "'".trim($args['tipo'])."'" : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";
		
		$this->db->query($qr_sql);
	}

	public function salvar_reclamacao_comite($args, $cd_usuario_comite)
	{
		$qr_sql = " 
			INSERT INTO projetos.reclamacao_comite
				 (
				   numero, 
				   ano, 
				   tipo,
				   cd_usuario_comite,
				   fl_voto,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				   
				 )
			VALUES 
				 (
				   ".(trim($args['numero']) != '' ? intval($args['numero']) : "DEFAULT").",
				   ".(trim($args['ano']) != '' ? intval($args['ano']) : "DEFAULT").",
				   ".(trim($args['tipo']) != '' ? "'".trim($args['tipo'])."'" : "DEFAULT").",
				   ".intval($cd_usuario_comite).",
				   'N',
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";
		
		$this->db->query($qr_sql);
	}

	public function salvar_validacao_comite($numero, $ano, $tipo, $args = array())
	{
		$qr_sql = " 
			UPDATE projetos.reclamacao_comite
			   SET fl_confirma				 = ".(trim($args['fl_confirma']) != '' ? "'".$args['fl_confirma']."'" : "DEFAULT").",
			       fl_abrir_nc				 = ".(trim($args['fl_abrir_nc']) != '' ? "'".$args['fl_abrir_nc']."'" : "DEFAULT").",
				   ds_justificativa_confirma = ".(trim($args['ds_justificativa_confirma']) != '' ? str_escape($args['ds_justificativa_confirma']) : "DEFAULT").",
				   cd_usuario_confirma		 = ".intval($args['cd_usuario']).",
				   cd_usuario_alteracao		 = ".intval($args['cd_usuario']).",
				   dt_confirma				 = CURRENT_TIMESTAMP, 
				   dt_alteracao				 = CURRENT_TIMESTAMP
			 WHERE numero                    = ".intval($numero)."
			   AND ano                    	 = ".intval($ano)."
			   AND tipo                   	 = '".trim($tipo)."'
			   AND cd_usuario_comite 		 = ".intval($args['cd_usuario']).";";
				   
		$this->db->query($qr_sql);
	}

	public function get_reclamacao_confirmada($numero, $ano, $tipo)
	{
		$qr_sql = "
	  		SELECT rc.numero, 
	  		       rc.ano, 
	  		       rc.tipo,
	  		       (CASE WHEN (SELECT COUNT(*)   
						         FROM projetos.reclamacao_comite rc1
						        WHERE rc1.numero      = rc.numero
						          AND rc1.ano         = rc.ano
						          AND rc1.tipo        = rc.tipo
						          AND rc1.fl_confirma IS NOT NULL
						      ) 
						      =
						      (SELECT COUNT(*)
						         FROM projetos.reclamacao_comite rc2
						        WHERE rc2.numero = rc.numero
						          AND rc2.ano    = rc.ano
						          AND rc2.tipo   = rc.tipo 
						       ) 
						 THEN 'S' 
						 ELSE 'N'
				   END) AS fl_encerrado,
				   (CASE WHEN (SELECT COUNT(*)   
						         FROM projetos.reclamacao_comite rc3
						        WHERE rc3.numero      = rc.numero
						          AND rc3.ano         = rc.ano
						          AND rc3.tipo        = rc.tipo
						          AND rc3.fl_confirma IS NOT NULL
						          AND rc3.fl_abrir_nc = 'S'
						      )
						      >
				   			  ((SELECT COUNT(*)   
						          FROM projetos.reclamacao_comite rc4
						         WHERE rc4.numero      = rc.numero
						           AND rc4.ano         = rc.ano
						           AND rc4.tipo        = rc.tipo
						           AND rc4.fl_confirma IS NOT NULL
						        ) / 2)
						 THEN 'S'
						 ELSE 'N'
				   END) fl_abrir_nc
			  FROM projetos.reclamacao_comite rc
			 WHERE rc.numero = ".intval($numero)."
			   AND rc.ano    = ".$ano."
			   AND rc.tipo   = '".trim($tipo)."'
			 GROUP BY rc.numero, 
			          rc.ano, 
			          rc.tipo;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function reclamacao_retorno()
	{
		$qr_sql = "
			SELECT rr.cd_reclamacao_retorno AS value, 
				   rr.ds_reclamacao_retorno AS text
			  FROM projetos.reclamacao_retorno rr
			 WHERE rr.dt_exclusao            IS NULL
			   AND rr.cd_reclamacao_retorno > 0
			 ORDER BY rr.ds_reclamacao_retorno;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function salvar_retorno($args = array())
	{
		$qr_sql = " 
			INSERT INTO projetos.reclamacao_retorno_atendimento
				 (
					numero,
					ano,
					tipo,
					cd_reclamacao_retorno,
					dt_retorno,
					ds_observacao_retorno,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				 )
			VALUES
			     (
			     	".intval($args['numero']).",
			     	".intval($args['ano']).",
			     	'".trim($args['tipo'])."',
			     	".(intval($args['cd_reclamacao_retorno']) > 0 ? intval($args['cd_reclamacao_retorno']) : "DEFAULT").",
			     	".(trim($args['dt_retorno']) != '' ? "TO_DATE('".$args['dt_retorno']."','DD/MM/YYYY')" : "DEFAULT").",
			     	".(trim($args['ds_observacao_retorno']) != '' ? str_escape($args['ds_observacao_retorno']) :  "DEFAULT").",
			     	".intval($args['cd_usuario']).",
			     	".intval($args['cd_usuario'])."
			     );";	

		$this->db->query($qr_sql);
	}

	public function get_email_atendimento($numero, $ano, $tipo, $cd_usuario_responsavel)
	{
		$qr_sql = " 
			SELECT numero, 
				   ano, 
				   tipo, 
				   funcoes.get_usuario(".intval($cd_usuario_responsavel).") || '@eletroceee.com.br' AS para
			  FROM projetos.reclamacao_atendimento
			 WHERE numero                 = ".intval($numero)."
			   AND ano                    = ".intval($ano)."
			   AND tipo                   = '".trim($tipo)."';";
		
		return $this->db->query($qr_sql)->row_array();
    }
    
    public function get_email_usuario_responsavel_atendimento($cd_usuario_responsavel)
    {
        $qr_sql = "
            SELECT funcoes.get_usuario(".intval($cd_usuario_responsavel).") || '@eletroceee.com.br' AS ds_responsavel,
                   uc2.usuario || '@eletroceee.com.br' AS ds_gerente,
                   (SELECT uc1.usuario || '@eletroceee.com.br'
                      FROM projetos.usuarios_controledi uc1
                     WHERE uc1.indic_01 = 'S'
                       AND uc1.divisao  = uc2.divisao) AS ds_subgerente
              FROM projetos.usuarios_controledi uc2
             WHERE uc2.tipo    = 'G'
               AND uc2.divisao = funcoes.get_usuario_area(".intval($cd_usuario_responsavel).");";

        return $this->db->query($qr_sql)->row_array();
    }

	public function parecer_comite_listar($args = array())
	{
		$qr_sql = "
			SELECT funcoes.nr_reclamacao(r.ano, r.numero, r.tipo) AS cd_reclamacao,
			       (CASE WHEN (
				             (SELECT COUNT(*)
						        FROM projetos.reclamacao_comite rc
						       WHERE rc.numero = rcr.numero
						         AND rc.ano    = rcr.ano
						         AND rc.tipo   = rcr.tipo) 
					         -
					         (SELECT COUNT(*)
						        FROM projetos.reclamacao_comite rc2
					           WHERE rc2.numero      = rcr.numero
						         AND rc2.ano         = rcr.ano
						         AND rc2.tipo        = rcr.tipo
						         AND rc2.fl_confirma IS NOT NULL)
							) = 0 AND  rcr.dt_parecer_final IS NULL 
					     THEN 'S'
					     ELSE 'N'
				   END) AS fl_validar,
				   CASE WHEN rcr.fl_retorno = 'N' THEN 'Confirma'
						WHEN rcr.fl_retorno = 'S' THEN 'Não Confirma'
						ELSE ''
				   END AS ds_confirma,
				   rcr.fl_retorno,
				   rcr.ds_justificativa_confirma,
			       rcr.cd_reclamacao_comite_retorno,
			       rcr.numero,
			       rcr.ano,
			       rcr.tipo,
			       r.cd_empresa, 
				   r.cd_registro_empregado, 
				   r.seq_dependencia, 
				   r.nome, 
				   r.descricao,
				   TO_CHAR(ran.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_classificacao,
			       TO_CHAR(rcr.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       TO_CHAR(rcr.dt_parecer_final, 'DD/MM/YYYY HH24:MI:SS') AS dt_parecer_final,
			       (CASE WHEN ran.dt_inclusao IS NOT NULL AND rcr.dt_parecer_final IS NOT NULL AND (rcr.dt_concorda IS NOT NULL OR rcr.fl_retorno = 'N')
			             THEN 'Encerrada'
			             WHEN rcr.dt_parecer_final IS NOT NULL AND ran.dt_inclusao IS NULL AND rcr.fl_retorno = 'S'
			             THEN 'Aguardando Reclassificação'
			             WHEN rcr.dt_parecer_final IS NOT NULL AND rcr.dt_concorda IS NULL AND rcr.fl_retorno = 'S'
			             THEN 'Aguardando Retorno'
			             WHEN (
				             (SELECT COUNT(*)
						        FROM projetos.reclamacao_comite rc
						       WHERE rc.numero = rcr.numero
						         AND rc.ano    = rcr.ano
						         AND rc.tipo   = rcr.tipo) 
					         -
					         (SELECT COUNT(*)
						        FROM projetos.reclamacao_comite rc2
					           WHERE rc2.numero      = rcr.numero
						         AND rc2.ano         = rcr.ano
						         AND rc2.tipo        = rcr.tipo
						         AND rc2.fl_confirma IS NOT NULL)
						 ) > 0
			             THEN 'Aguardando Avaliação de Membro(s)'
			             ELSE 'Aguardando Avaliação Final'
			       END) ds_status,
			       (CASE WHEN ran.dt_inclusao IS NOT NULL AND rcr.dt_parecer_final IS NOT NULL AND (rcr.dt_concorda IS NOT NULL OR rcr.fl_retorno = 'N')
			             THEN 'label'
			             WHEN rcr.dt_parecer_final IS NOT NULL AND ran.dt_inclusao IS NULL AND rcr.fl_retorno = 'S'
			             THEN 'label label-info'
			             WHEN rcr.dt_parecer_final IS NOT NULL AND rcr.dt_concorda IS NULL AND rcr.fl_retorno = 'S'
			             THEN 'label label-info'
			             WHEN ( 
			            	 (SELECT COUNT(*)
					            FROM projetos.reclamacao_comite rc
					           WHERE rc.numero = rcr.numero
					             AND rc.ano    = rcr.ano
					             AND rc.tipo   = rcr.tipo) 
				             -
				             (SELECT COUNT(*)
					            FROM projetos.reclamacao_comite rc2
				               WHERE rc2.numero      = rcr.numero
					             AND rc2.ano         = rcr.ano
					             AND rc2.tipo        = rcr.tipo
					             AND rc2.fl_confirma IS NOT NULL)
					     )  > 0
			             THEN 'label label-important'
			             ELSE 'label label-warning'
			       END) ds_class_status 
			  FROM projetos.reclamacao_comite_retorno rcr
			  JOIN projetos.reclamacao r
			    ON r.numero = rcr.numero
			   AND r.ano    = rcr.ano
			   AND r.tipo   = rcr.tipo
			  LEFT JOIN projetos.reclamacao_andamento ran
				ON ran.numero                  = r.numero
			   AND ran.ano                     = r.ano
			   AND ran.tipo                    = r.tipo
			   AND ran.tp_reclamacao_andamento = 'R' --RETORNO	
			   AND ran.dt_exclusao             IS NULL	
			 WHERE r.dt_cancela IS NULL
			   AND r.dt_exclusao IS NULL
			   AND r.dt_inclusao >= '2017-12-01'::date
			   ".(intval($args['numero']) > 0 ? "AND r.numero = ".intval($args['numero']) : "")."
			   ".(intval($args['ano']) > 0 ? "AND r.ano = ".intval($args['ano']) : "")."
			   ".(trim($args['tipo']) != '' ? "AND r.tipo = '".trim($args['tipo'])."'" : "")."
			   ".(trim($args['fl_status']) == 'AM' ? "AND (
		             (SELECT COUNT(*)
				        FROM projetos.reclamacao_comite rc
				       WHERE rc.numero = rcr.numero
				         AND rc.ano    = rcr.ano
				         AND rc.tipo   = rcr.tipo) 
			         -
			         (SELECT COUNT(*)
				        FROM projetos.reclamacao_comite rc2
			           WHERE rc2.numero      = rcr.numero
				         AND rc2.ano         = rcr.ano
				         AND rc2.tipo        = rcr.tipo
				         AND rc2.fl_confirma IS NOT NULL)
				) > 0" : "")."
			   ".(trim($args['fl_status']) == 'AF' ? "AND  rcr.dt_parecer_final IS NULL
			   	AND (
		             (SELECT COUNT(*)
				        FROM projetos.reclamacao_comite rc
				       WHERE rc.numero = rcr.numero
				         AND rc.ano    = rcr.ano
				         AND rc.tipo   = rcr.tipo) 
			         -
			         (SELECT COUNT(*)
				        FROM projetos.reclamacao_comite rc2
			           WHERE rc2.numero      = rcr.numero
				         AND rc2.ano         = rcr.ano
				         AND rc2.tipo        = rcr.tipo
				         AND rc2.fl_confirma IS NOT NULL)
				) = 0" : "")."
			   ".(trim($args['fl_status']) == 'EN' ? "AND ran.dt_inclusao IS NOT NULL AND rcr.dt_parecer_final IS NOT NULL AND (rcr.dt_concorda IS NOT NULL OR rcr.fl_retorno = 'N')" : "")."
			   ".(trim($args['fl_status']) == 'AR' ? "AND rcr.dt_parecer_final IS NOT NULL AND rcr.dt_concorda IS NULL AND rcr.fl_retorno = 'S'" : "")."
			   ".(trim($args['fl_status']) == 'AC' ? "AND rcr.dt_parecer_final IS NOT NULL AND ran.dt_inclusao IS NULL AND rcr.fl_retorno = 'S'" : "")."
			   ".(((trim($args['dt_parecer_final_ini']) != '') AND (trim($args['dt_parecer_final_fim']) != '')) ? "AND CAST(rcr.dt_parecer_final AS DATE) BETWEEN TO_DATE('".$args['dt_parecer_final_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_parecer_final_fim']."', 'DD/MM/YYYY')" : "")."
			 ORDER BY ano DESC, numero DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function menbros_comite_sem_resposta($numero, $ano, $tipo)
	{
		$qr_sql = "
			SELECT funcoes.get_usuario_nome(cd_usuario_comite) AS ds_usuario,
			       cd_usuario_comite
			  FROM projetos.reclamacao_comite
			 WHERE fl_confirma IS NULL
			   AND numero = ".intval($numero)."
			   AND ano    = ".intval($ano)."
			   AND tipo   = '".trim($tipo)."';";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_parecer_final($numero, $ano, $tipo)
	{
		$qr_sql = "
			SELECT COUNT(*) fl_parecer_final 
			  FROM projetos.reclamacao_comite_retorno rcr
			  JOIN projetos.reclamacao r
			    ON r.numero = rcr.numero
			   AND r.ano    = rcr.ano
			   AND r.tipo   = rcr.tipo
			 WHERE r.dt_cancela IS NULL
			   AND r.dt_exclusao IS NULL
			   AND r.dt_inclusao >= '2017-12-01'::date
			   AND rcr.dt_parecer_final IS NULL
			   AND rcr.numero = ".intval($numero)."
			   AND rcr.ano    = ".intval($ano)."
			   AND rcr.tipo   = '".trim($tipo)."'
			   AND (SELECT COUNT(*)
				      FROM projetos.reclamacao_comite rc2
				     WHERE rc2.numero      = rcr.numero
				       AND rc2.ano         = rcr.ano
				       AND rc2.tipo        = rcr.tipo
				       AND rc2.fl_confirma IS NULL) = 0;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega_parecer_final($numero, $ano, $tipo)
	{
		$qr_sql = "
			SELECT 'CRQC PARECER FINAL' AS ds_usuario_comite,
			       rcr.fl_retorno,
			       rcr.ds_justificativa_confirma,
			       TO_CHAR(rcr.dt_parecer_final, 'DD/MM/YYYY HH24:MI:SS') AS dt_parecer_final,
			       (CASE WHEN fl_retorno = 'N' THEN 'Confirma'
						 WHEN fl_retorno = 'S' THEN 'Não Confirma'
						 ELSE 'Não Respondeu'
				   END) AS ds_confirma
			  FROM projetos.reclamacao_comite_retorno rcr
			  JOIN projetos.reclamacao r
			    ON r.numero = rcr.numero
			   AND r.ano    = rcr.ano
			   AND r.tipo   = rcr.tipo
			 WHERE r.dt_cancela IS NULL
			   AND r.dt_exclusao IS NULL
			   AND r.dt_inclusao >= '2017-12-01'::date
			   AND rcr.numero = ".intval($numero)."
			   AND rcr.ano    = ".intval($ano)."
			   AND rcr.tipo   = '".trim($tipo)."';";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar_parecer_comite_avaliacao($numero, $ano, $tipo, $args)
	{
		$qr_sql = " 
			UPDATE projetos.reclamacao_comite_retorno
			   SET fl_retorno				 = ".(trim($args['fl_retorno']) != '' ? "'".$args['fl_retorno']."'" : "DEFAULT").",
				   ds_justificativa_confirma = ".(trim($args['ds_justificativa_confirma']) != '' ? str_escape($args['ds_justificativa_confirma']) : "DEFAULT").",
				   cd_usuario_parecer_final	 = ".intval($args['cd_usuario']).",
				   cd_usuario_alteracao		 = ".intval($args['cd_usuario']).",
				   dt_parecer_final		     = CURRENT_TIMESTAMP, 
				   dt_alteracao				 = CURRENT_TIMESTAMP
			 WHERE numero                    = ".intval($numero)."
			   AND ano                       = ".intval($ano)."
			   AND tipo                      = '".trim($tipo)."';";
				   
		$this->db->query($qr_sql);
	}

	public function get_emails($numero, $ano, $tipo)
	{
		$qr_sql = " 
			SELECT numero, 
				   ano, 
				   tipo, 
				   funcoes.get_usuario(cd_usuario_responsavel) || '@eletroceee.com.br;' ||     
				   (SELECT ucg.usuario 
				      FROM projetos.usuarios_controledi ucg 
					 WHERE ucg.divisao = 'GCM' 
					   AND ucg.tipo    = 'G') || '@eletroceee.com.br;' || 
				   (SELECT uc.usuario 
				      FROM projetos.usuarios_controledi uc 
					 WHERE uc.divisao  = 'GCM' 
					   AND uc.indic_01 = 'S') || '@eletroceee.com.br' AS para,
				   funcoes.get_usuario_area(cd_usuario_responsavel) || '@eletroceee.com.br' AS para_gerencia,
				   funcoes.get_usuario(funcoes.get_usuario_gerente(cd_usuario_responsavel)) || '@eletroceee.com.br' AS para_gerente,
				   funcoes.get_usuario(funcoes.get_usuario_gerente_substituto(cd_usuario_responsavel)) || '@eletroceee.com.br' AS para_gerente_substituto
			  FROM projetos.reclamacao_atendimento
			 WHERE numero                 = ".intval($numero)."
			   AND ano                    = ".intval($ano)."
			   AND tipo                   = '".trim($tipo)."';";
		
		return $this->db->query($qr_sql)->row_array();
	}

	public function encerra_reclamacao($numero, $ano, $tipo)
	{
		$qr_sql = "
	  		UPDATE projetos.reclamacao
	  		   SET dt_encerramento = CURRENT_TIMESTAMP
	  		 WHERE numero = ".intval($numero)."
			   AND ano    = ".intval($ano)."
			   AND tipo   = '".trim($tipo)."';";

		$this->db->query($qr_sql);
	}

	public function salvar_retorno_responsavel($numero, $ano, $tipo, $args = array())
	{
		$qr_sql = " 
			UPDATE projetos.reclamacao_comite_retorno
			   SET fl_concorda				 = ".(trim($args['fl_concorda']) != '' ? "'".$args['fl_concorda']."'" : "DEFAULT").",
				   ds_justificativa_concorda = ".(trim($args['ds_justificativa_concorda']) != '' ? str_escape($args['ds_justificativa_concorda']) : "DEFAULT").",
				   cd_usuario_concorda		 = ".intval($args['cd_usuario']).",
				   cd_usuario_alteracao		 = ".intval($args['cd_usuario']).",
				   dt_alteracao				 = CURRENT_TIMESTAMP,
				   dt_concorda				 = CURRENT_TIMESTAMP
			 WHERE numero                    = ".intval($numero)."
			   AND ano                       = ".intval($ano)."
			   AND tipo                      = '".trim($tipo)."';";
				   
		$this->db->query($qr_sql);
	}

	public function exlcuir_classificao($numero, $ano, $tipo, $args = array())
	{
		$qr_sql = " 
			UPDATE projetos.reclamacao_andamento
			   SET dt_exclusao 		   = CURRENT_TIMESTAMP,
				   cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE numero                  = ".intval($numero)."
			   AND ano                     = ".intval($ano)."
			   AND tipo                    = '".trim($tipo)."'
			   AND tp_reclamacao_andamento = 'R';";
				   
		$this->db->query($qr_sql);
	}

	public function reclamacao_retorno_atendimento($numero, $ano, $tipo)
	{
		$qr_sql = "
			SELECT rra.cd_reclamacao_retorno,
			       TO_CHAR(rra.dt_retorno, 'DD/MM/YYYY') AS dt_retorno,
			       rra.ds_observacao_retorno,
			       rr.ds_reclamacao_retorno
			  FROM projetos.reclamacao_retorno_atendimento rra
			  JOIN projetos.reclamacao_retorno rr
			    ON rr.cd_reclamacao_retorno = rra.cd_reclamacao_retorno
			 WHERE rra.dt_exclusao IS NULL
			   AND rra.numero      = ".intval($numero)."
			   AND rra.ano         = ".intval($ano)."
			   AND rra.tipo        = '".trim($tipo)."'";

		return $this->db->query($qr_sql)->row_array();
    }
    
    public function excluir($numero, $ano, $tipo, $cd_usuario)
    {
		$qr_sql = " 
			UPDATE projetos.reclamacao
			   SET dt_exclusao 		   = CURRENT_TIMESTAMP,
				   cd_usuario_exclusao = ".intval($cd_usuario)."
			 WHERE numero              = ".intval($numero)."
			   AND ano                 = ".intval($ano)."
			   AND tipo                = '".trim($tipo)."';";
				   
		$this->db->query($qr_sql);
    }

    public function salvar_registro_melhoria($args = array())
    {
		$qr_sql = " 
			INSERT INTO projetos.reclamacao_registro_melhoria
				 (
					dt_reclamacao_ini,
					dt_reclamacao_fim,
					cd_reclamacao_assunto,
					cd_usuario_inclusao,
					cd_usuario_alteracao,
					ds_reclamacao_registro_melhoria
				 )
			VALUES
			     (
			     	".(trim($args['dt_reclamacao_ini']) != '' ? "TO_DATE('".$args['dt_reclamacao_ini']."','DD/MM/YYYY')" : "DEFAULT").",
			     	".(trim($args['dt_reclamacao_fim']) != '' ? "TO_DATE('".$args['dt_reclamacao_fim']."','DD/MM/YYYY')" : "DEFAULT").",
			     	".intval($args['cd_reclamacao_assunto']).",
			     	".intval($args['cd_usuario']).",
			     	".intval($args['cd_usuario']).",
			     	".(trim($args['ds_reclamacao_registro_melhoria']) != '' ? str_escape($args['ds_reclamacao_registro_melhoria']) : "DEFAULT")."
			     );";	

		$this->db->query($qr_sql);
    }

    public function relatorio_melhoria_listar($args = array())
    {
    	$qr_sql = "
			SELECT rrm.ds_reclamacao_registro_melhoria,
				   TO_CHAR(rrm.dt_reclamacao_ini,'DD/MM/YYYY') AS dt_reclamacao_ini,
				   TO_CHAR(rrm.dt_reclamacao_fim,'DD/MM/YYYY') AS dt_reclamacao_fim,
				   TO_CHAR(rrm.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   ra.ds_reclamacao_assunto,
                   (SELECT COUNT(*)
					  FROM projetos.reclamacao r
					 WHERE r.dt_exclusao IS NULL
					   AND r.dt_cancela  IS NULL
					   AND r.tipo = 'R'
					   AND CAST(r.dt_inclusao AS DATE) BETWEEN rrm.dt_reclamacao_ini AND rrm.dt_reclamacao_fim
                       AND r.cd_reclamacao_assunto = rrm.cd_reclamacao_assunto) AS tl_reclamacao
			  FROM projetos.reclamacao_registro_melhoria rrm
			  JOIN projetos.reclamacao_assunto ra
			    ON ra.cd_reclamacao_assunto = rrm.cd_reclamacao_assunto
			 WHERE rrm.dt_exclusao IS NULL
			 	".(trim($args['cd_reclamacao_assunto'] != '') ? "AND rrm.cd_reclamacao_assunto = ".intval($args['cd_reclamacao_assunto']) : "")."
			 	".(trim($args['dt_reclamacao_ini']) != '' ? "AND TO_CHAR(rrm.dt_reclamacao_ini,'DD/MM/YYYY') = '".trim($args['dt_reclamacao_ini'])."'" : "")."
			 	".(trim($args['dt_reclamacao_fim']) != '' ? "AND  TO_CHAR(rrm.dt_reclamacao_fim,'DD/MM/YYYY') = '".trim($args['dt_reclamacao_fim'])."'" : "")."
			 	;";
			 	
		return $this->db->query($qr_sql)->result_array();
    }

    public function get_reclamacao_periodo()
    {
    	$qr_sql = "
			SELECT CONCAT( TO_CHAR(dt_reclamacao_ini,'DD/MM/YYYY'), ' - ',TO_CHAR(dt_reclamacao_fim,'DD/MM/YYYY') ) AS text, 
		   		   CONCAT( TO_CHAR(dt_reclamacao_ini,'DD/MM/YYYY'), ' - ',TO_CHAR(dt_reclamacao_fim,'DD/MM/YYYY') ) AS value 
	  		  FROM projetos.reclamacao_registro_melhoria 
	 		 WHERE dt_exclusao 		IS NULL
	 	  ORDER BY dt_reclamacao_ini desc,dt_reclamacao_fim desc;";

	 	return $this->db->query($qr_sql)->result_array();
    }
}
?>