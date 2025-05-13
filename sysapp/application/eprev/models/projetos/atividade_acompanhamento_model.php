<?php
class Atividade_acompanhamento_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT aa.cd_atividade_acompanhamento, 
			       aa.cd_atividade, 
				   aa.cd_usuario_inclusao,
				   aa.ds_atividade_acompanhamento, 
				   TO_CHAR(aa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome,
				   CASE WHEN a.status_atual = 'ETES' THEN 'N'
						WHEN a.dt_fim_real IS NOT NULL THEN 'N'
						WHEN ".intval($args['cd_usuario'])." NOT IN (a.cod_atendente, a.cod_solicitante, a.cod_testador) THEN 'N'
						ELSE 'S'
				   END AS fl_excluir
              FROM projetos.atividade_acompanhamento aa
			  JOIN projetos.atividades a
				ON a.numero = aa.cd_atividade
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = aa.cd_usuario_inclusao
			 WHERE aa.dt_exclusao IS NULL
			   AND aa.cd_atividade = ".intval($args['cd_atividade']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.atividade_acompanhamento 
			     (
                   cd_atividade, 
				   ds_atividade_acompanhamento, 
                   cd_usuario_inclusao
				 )
            VALUES 
			     (
				   ".intval($args['cd_atividade']).",
				   ".(trim($args['ds_atividade_acompanhamento']) != '' ? str_escape($args['ds_atividade_acompanhamento']) : '').",
				   ".intval($args['cd_usuario'])."
				 );";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.atividade_acompanhamento
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_atividade_acompanhamento = ".intval($args['cd_atividade_acompanhamento']).";";
		$this->db->query($qr_sql);
	}
	
	public function acompanhamento_email($cd_atividade)
	{
		$qr_sql = "
			SELECT a.numero, 
				   a.area,
				   a.cod_atendente,
				   a.cd_substituto,
				   funcoes.get_usuario_nome(a.cod_atendente) AS atendente,
				   a.cod_solicitante,  
				   funcoes.get_usuario_nome(a.cd_substituto) AS substituto,
				   funcoes.get_usuario_nome(a.cod_solicitante) AS solicitante,
				   a.status_atual,
				   CASE WHEN (a.status_atual='AMAN') THEN 'Aguardando Manutenзгo' 
						WHEN (a.status_atual='EMAN') THEN 'Em Manutenзгo' 
						WHEN (a.status_atual='AINI') THEN 'Aguardando Inнcio' 
						WHEN (a.status_atual='LIBE') THEN 'Liberada' 
						WHEN (a.status_atual='CONC') THEN 'Conclusгo'
						WHEN (a.status_atual='CANC') THEN 'Cancelada'
						WHEN (a.status_atual='AGDF') THEN 'Aguardando Definiзгo'
						ELSE 'Em Manutenзгo (Pausa)'
                   END AS status
			  FROM projetos.atividades a
			 WHERE a.numero = ".intval($cd_atividade).";";

		return $this->db->query($qr_sql)->row_array();
	}	
	public function get_emails($args = array())
	{
		$qr_sql = " 
			SELECT funcoes.get_usuario(cod_atendente)   || '@eletroceee.com.br;'  ||
				   funcoes.get_usuario(cod_solicitante) || '@eletroceee.com.br'   ||
				   (CASE WHEN cd_substituto IS NOT NULL THEN ';'|| funcoes.get_usuario(cd_substituto) || '@eletroceee.com.br' 
				        ELSE ''
				   END) ||  
				   (CASE WHEN cod_solicitante = 287 THEN ';' || funcoes.get_usuario(40) || '@eletroceee.com.br;' || funcoes.get_usuario(75) || '@eletroceee.com.br'  
				         ELSE ''
				   END) AS para 		  
			  FROM projetos.atividades
			 WHERE numero = ".intval($args['numero']).";";
		
		return $this->db->query($qr_sql)->row_array();
	}
}
?>