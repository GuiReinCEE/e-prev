<?php
class Atividade_anexo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT aa.cd_atividade_anexo,
						   aa.arquivo,
						   aa.arquivo_nome,
						   aa.cd_usuario_inclusao,
						   TO_CHAR(aa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   funcoes.get_usuario_nome(aa.cd_usuario_inclusao) AS nome,
						   CASE WHEN a.status_atual = 'ETES' THEN 'N'
						        WHEN a.dt_fim_real IS NOT NULL THEN 'N'
								WHEN ".intval($args['cd_usuario'])." NOT IN (a.cod_atendente, a.cod_solicitante, a.cod_testador) THEN 'N'
								ELSE 'S'
						   END AS fl_excluir
					  FROM projetos.atividade_anexo aa
					  JOIN projetos.atividades a
						ON a.numero = aa.cd_atividade
					 WHERE aa.cd_atividade = ". $args['cd_atividade']."
					   AND aa.dt_exclusao IS NULL
					 ORDER BY aa.dt_inclusao DESC
			      ";
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.atividade_anexo
			     (
					cd_atividade,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['cd_atividade']).",
					'".trim($args['arquivo'])."',
					'".trim($args['arquivo_nome'])."',
					".intval($args['cd_usuario'])."
				 )";
		$result = $this->db->query($qr_sql);
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.atividade_anexo
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_atividade_anexo = ".intval($args['cd_atividade_anexo']).";";
		$this->db->query($qr_sql);
	}
	
	public function anexo_email($cd_atividade)
	{
		$qr_sql = "
			SELECT a.numero, 
				   a.area,
				   a.cod_atendente,
				   a.cd_substituto,
				   funcoes.get_usuario_nome(a.cod_atendente) AS atendente,
				   funcoes.get_usuario_nome(a.cd_substituto) AS substituto,
				   a.cod_solicitante,  
				   funcoes.get_usuario_nome(a.cod_solicitante) AS solicitante,
				   a.status_atual,
				   CASE WHEN (a.status_atual='AMAN') THEN 'Aguardando Manutenзгo' 
						WHEN (a.status_atual='EMAN') THEN 'Em Manutenзгo' 
						WHEN (a.status_atual='AINI') THEN 'Aguardando Inнcio' 
						WHEN (a.status_atual='LIBE') THEN 'Liberada' 
						WHEN (a.status_atual='CONC') THEN 'Concluнa'
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
			 WHERE numero  = ".intval($args['numero']).";";
		
		return $this->db->query($qr_sql)->row_array();
	}
}

?>