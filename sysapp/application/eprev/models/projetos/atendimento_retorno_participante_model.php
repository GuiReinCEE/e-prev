<?php
class Atendimento_retorno_participante_model extends Model
{
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT arp.cd_atendimento_retorno_participante,
			       arp.cd_atividade,
			       TO_CHAR(arp.dt_retorno, 'DD/MM/YYYY') AS dt_retorno,
			       arp.ds_observacao,
			       a.cd_empresa,
			       a.cd_registro_empregado,
			       a.cd_sequencia,
			       a.cd_empresa||'/'||a.cd_registro_empregado||'/'||a.cd_sequencia AS ds_re,
			       p.nome
			  FROM projetos.atendimento_retorno_participante arp
			  JOIN projetos.atividades a
			    ON a.numero = arp.cd_atividade
			  JOIN public.participantes p
			    ON p.cd_registro_empregado = a.cd_registro_empregado
			   AND p.cd_empresa 		   = a.cd_empresa
			   AND p.seq_dependencia 	   = a.cd_sequencia
			 WHERE arp.dt_exclusao IS NULL
			   AND a.cod_solicitante = ".intval($args['cd_usuario'])."
			   ".(intval($args['cd_empresa']) > 0 ? "AND a.cd_empresa = ".intval($args['cd_empresa']) : "")."
			   ".(intval($args['cd_registro_empregado']) > 0 ? "AND a.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
			   ".(intval($args['seq_dependencia']) > 0 ? "AND a.cd_sequencia = ".intval($args['seq_dependencia']) : "")."
			   ".(((trim($args['dt_retorno_ini']) != '') AND (trim($args['dt_retorno_fim']) != '')) ? "AND DATE_TRUNC('day', arp.dt_retorno) BETWEEN TO_DATE('".$args['dt_retorno_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_retorno_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(trim($args['fl_retorno']) == 'N' ? "AND arp.dt_retorno IS NULL" : "")."
			   ".(trim($args['fl_retorno']) == 'S' ? "AND arp.dt_retorno IS NOT NULL" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_atendimento_retorno_participante)
	{
		$qr_sql = "
			SELECT arp.cd_atendimento_retorno_participante,
			       arp.cd_atividade,
			       a.cd_empresa,
			       a.cd_registro_empregado,
			       a.cd_sequencia,
			       a.cd_empresa||'/'||a.cd_registro_empregado||'/'||a.cd_sequencia AS ds_re,
			       TO_CHAR(arp.dt_retorno, 'DD/MM/YYYY') AS dt_retorno,
			       arp.ds_observacao,
			       p.nome
			  FROM projetos.atendimento_retorno_participante arp
			  JOIN projetos.atividades a
			    ON a.numero = arp.cd_atividade
			  JOIN public.participantes p
			    ON p.cd_registro_empregado = a.cd_registro_empregado
			   AND p.cd_empresa 		   = a.cd_empresa
			   AND p.seq_dependencia 	   = a.cd_sequencia
			 WHERE arp.dt_exclusao 						   IS NULL
			   AND arp.cd_atendimento_retorno_participante = ".intval($cd_atendimento_retorno_participante).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($cd_atendimento_retorno_participante, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.atendimento_retorno_participante
			   SET dt_retorno 			= ".(trim($args['dt_retorno']) != '' ? "TO_DATE('".$args['dt_retorno']."', 'DD/MM/YYYY')" : "DEFAULT").",
			   	   ds_observacao 		= ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
			   	   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
			   	   dt_alteracao 		= CURRENT_TIMESTAMP
			 WHERE cd_atendimento_retorno_participante = ".intval($cd_atendimento_retorno_participante).";";

		$this->db->query($qr_sql);
	}
}