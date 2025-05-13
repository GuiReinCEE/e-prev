<?php

class Atendimento_obito_seguro_model extends Model
{
	function __construct()
  	{
    	parent::Model();
  	}

	public function listar($args = array())
	{
		$qr_sql = "
		
			SELECT s.cd_empresa,
				   s.cd_registro_empregado,
				   s.seq_dependencia,
				   p.nome,
				   TO_CHAR(s.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(s.cd_usuario_confirmacao) AS usuario_confirmacao,
				   TO_CHAR(s.dt_confirmacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_confirmacao,
				   s.ds_motivo_pendencia
			  FROM projetos.atendimento_obito_seguro s 
			  JOIN public.participantes p
				ON p.cd_empresa            = s.cd_empresa
			   AND p.cd_registro_empregado = s.cd_registro_empregado
			   AND p.seq_dependencia       = s.seq_dependencia
			 WHERE 1 = 1
			   ".(trim($args['cd_empresa']) != '' ? "AND s.cd_empresa = ".intval($args['cd_empresa']) : "")."			   
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND s.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."			   
			   ".(trim($args['seq_dependencia']) != '' ? "AND s.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
			   ".(((trim($args['dt_ini']) != '') AND (trim($args['dt_fim']) != '')) ? " AND DATE_TRUNC('day', s.dt_inclusao) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(trim($args['fl_confirmado']) == 'N' ? "AND s.dt_confirmacao IS NULL" : "")."
			   ".(trim($args['fl_confirmado']) == 'S' ? "AND s.dt_confirmacao IS NOT NULL" : "")."
			   ".(trim($args['ds_motivo_pendencia']) != '' ? str_escape($args['ds_motivo_pendencia']) : "")."
			 ORDER BY s.dt_inclusao;";
			 
		return $this->db->query($qr_sql)->result_array();
	}
	
  	public function get_participante_obito($cd_empresa, $cd_registro_empregado, $seq_dependencia)
  	{
  		$qr_sql = "
			SELECT p.cd_empresa,
			       p.cd_registro_empregado,
				   p.seq_dependencia,
				   funcoes.remove_acento(UPPER(p.nome)) AS nome,
				   TO_CHAR(p.dt_obito,'DD/MM/YYYY') AS dt_obito,
				   TO_CHAR(p.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
				   funcoes.format_cpf(TO_CHAR(p.cpf_mf,'FM00000000000')) AS cpf,
				   funcoes.remove_acento(UPPER(p.endereco)) AS endereco,
				   COALESCE(p.nr_endereco,'') AS nr_endereco,
				   funcoes.remove_acento(UPPER(COALESCE(p.complemento_endereco,''))) AS complemento_endereco,
			       funcoes.remove_acento(UPPER(p.bairro)) AS bairro,
			       TO_CHAR(p.cep,'FM00000') || '-' || TO_CHAR(p.complemento_cep, 'FM000') AS cep,
			       funcoes.remove_acento(UPPER(p.cidade)) AS cidade,
			       p.unidade_federativa AS uf,
				   LOWER(COALESCE(p.email,p.email_profissional)) AS email,
				   (CASE WHEN COALESCE(p.ddd,0) > 0 THEN TO_CHAR(p.ddd,'FM999') ELSE NULL END) AS ddd,
				   (CASE WHEN COALESCE(p.telefone,0) > 0 THEN CAST(p.telefone AS TEXT) ELSE NULL END) AS telefone,
				   (CASE WHEN COALESCE(p.ddd_celular,0) > 0 THEN TO_CHAR(p.ddd_celular,'FM999') ELSE NULL END) AS ddd_celular,
				   (CASE WHEN COALESCE(p.celular,0) > 0 THEN CAST(p.celular AS TEXT) ELSE NULL END) AS celular,
				   COALESCE(tipo_falecimento,0) AS tipo_falecimento
			  FROM public.participantes p
			 WHERE p.cd_empresa            = ".intval($cd_empresa)."
			   AND p.cd_registro_empregado = ".intval($cd_registro_empregado)."
			   AND p.seq_dependencia       = ".intval($seq_dependencia).";";

		return $this->db->query($qr_sql)->row_array();
  	}
	
	public function confirma($cd_empresa, $cd_registro_empregado, $seq_dependencia, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.atendimento_obito_seguro
			   SET cd_usuario_confirmacao = ".intval($cd_usuario).",
				   dt_confirmacao 		  = CURRENT_TIMESTAMP
			 WHERE cd_empresa             = ".intval($cd_empresa)."
			   AND cd_registro_empregado  = ".intval($cd_registro_empregado)."
			   AND seq_dependencia        = ".intval($seq_dependencia).";";
 		
		$this->db->query($qr_sql);
	}
	
	public function alterar_motivo($cd_registro_empregado,  $ds_motivo_pendencia, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.atendimento_obito_seguro
               SET ds_motivo_pendencia   = ".(trim($ds_motivo_pendencia) != '' ? str_escape($ds_motivo_pendencia) : "DEFAULT").",
				   cd_usuario_alteracao  = ".intval($cd_usuario).",
                   dt_alteracao          = CURRENT_TIMESTAMP
             WHERE cd_registro_empregado = ".intval($cd_registro_empregado).";";    

        $this->db->query($qr_sql);  
	}

}