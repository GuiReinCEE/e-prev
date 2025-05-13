<?php
class movimento_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT t.cd_termo,
				   m.cd_movimento,
				   TO_CHAR(m.dt_referencia, 'MM/YYYY') AS dt_referencia,
				   entidades.nr_movimento(m.nr_ano, m.nr_numero) AS nr_ano_numero,
				   TO_CHAR(m.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   TO_CHAR(m.dt_recebido, 'DD/MM/YYYY HH24:MI:SS') AS dt_recebido,
				   TO_CHAR(m.dt_retorno, 'DD/MM/YYYY HH24:MI:SS') AS dt_retorno,
				   e.ds_entidade
			  FROM entidades.movimento m
			  JOIN entidades.entidade e
			    ON e.cd_entidade = m.cd_entidade
			  JOIN entidades.termo t
			    ON t.cd_termo = m.cd_termo
			 WHERE t.dt_exclusao IS NULL
			   AND m.dt_exclusao IS NULL
			   AND m.dt_envio IS NOT NULL
			   ".(trim($args['nr_ano']) != '' ? "AND m.nr_ano = ".intval($args['nr_ano']) : "")."
			   ".(trim($args['nr_numero']) != '' ? "AND m.nr_numero = ".intval($args['nr_numero']) : "")."
			   ".(trim($args['nr_mes_ref']) != '' ? "AND TO_CHAR(m.dt_referencia, 'MM') = '".trim($args['nr_mes_ref'])."'" : "")."
			   ".(trim($args['nr_ano_ref']) != '' ? "AND TO_CHAR(m.dt_referencia, 'YYYY') = '".trim($args['nr_ano_ref'])."'" : "")."
			   ".(((trim($args['dt_envio_ini']) != "") AND  (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', m.dt_envio) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_recebido_ini']) != "") AND  (trim($args['dt_recebido_fim']) != "")) ? " AND DATE_TRUNC('day', m.dt_recebido) BETWEEN TO_DATE('".$args['dt_recebido_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_recebido_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_retorno_ini']) != "") AND  (trim($args['dt_retorno_fim']) != "")) ? " AND DATE_TRUNC('day', m.dt_retorno) BETWEEN TO_DATE('".$args['dt_retorno_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_retorno_fim']."', 'DD/MM/YYYY')" : "").";";

		$result = $this->db->query($qr_sql);
	}	
	
	public function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT t.cd_termo,
				   m.cd_movimento,
				   TO_CHAR(m.dt_referencia, 'MM') AS mes_referencia,
				   TO_CHAR(m.dt_referencia, 'YYYY') AS ano_referencia,
				   entidades.nr_movimento(m.nr_ano, m.nr_numero) AS nr_ano_numero,
				   TO_CHAR(m.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   TO_CHAR(m.dt_recebido, 'DD/MM/YYYY HH24:MI:SS') AS dt_recebido,
				   TO_CHAR(m.dt_retorno, 'DD/MM/YYYY HH24:MI:SS') AS dt_retorno,
				   t.ds_termo,
				   (SELECT COUNT(*)
				      FROM entidades.movimento_anexo ma
					 WHERE dt_exclusao IS NULL
					   AND ma.cd_movimento = m.cd_movimento
					   AND cd_usuario_inclusao_fceee IS NOT NULL) AS tl_anexo,
				   e.ds_entidade
			  FROM entidades.movimento m
			  JOIN entidades.entidade e
			    ON e.cd_entidade = m.cd_entidade
			  JOIN entidades.termo t
			    ON t.cd_termo = m.cd_termo
			 WHERE m.cd_movimento = ".intval($args['cd_movimento']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	public function receber(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE entidades.movimento
			   SET cd_usuario_recebido = ".intval($args['cd_usuario']).",
				   dt_recebido         = CURRENT_TIMESTAMP
			 WHERE cd_movimento = ".intval($args['cd_movimento']).";";
		
		$result = $this->db->query($qr_sql);
	}
	
	public function retorno_tipo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_movimento_retorno_tipo AS value,
			       ds_movimento_retorno_tipo AS text
			  FROM entidades.movimento_retorno_tipo
			 WHERE dt_exclusao IS NULL
			   AND cd_movimento_retorno_tipo > 0
			 ORDER BY ds_movimento_retorno_tipo;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	public function salvar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO entidades.movimento_anexo
			     (
					cd_movimento,
					cd_movimento_retorno_tipo,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao_fceee,
					cd_usuario_alteracao_fceee
				 )
			VALUES
			     (
					".intval($args['cd_movimento']).",
					".intval($args['cd_movimento_retorno_tipo']).",
					'".trim($args['arquivo'])."',
					'".trim($args['arquivo_nome'])."',
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				 )";
				 
		$this->db->query($qr_sql);
	}
	
	public function listar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cia.cd_movimento_anexo,
			       cia.arquivo,
				   cia.arquivo_nome,
				   ci.dt_retorno,
				   TO_CHAR(cia.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   rt.ds_movimento_retorno_tipo,
				   rt.class_label
			  FROM entidades.movimento_anexo cia
			  JOIN entidades.movimento ci
			    ON ci.cd_movimento = cia.cd_movimento
			  JOIN entidades.movimento_retorno_tipo rt
			    ON rt.cd_movimento_retorno_tipo = cia.cd_movimento_retorno_tipo
			 WHERE cia.dt_exclusao IS NULL
			   AND cia.cd_usuario_inclusao_fceee IS NOT NULL
			   AND cia.cd_movimento = ".intval($args['cd_movimento']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	public function anexo_entidade(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cia.cd_movimento_anexo,
			       cia.arquivo,
				   cia.arquivo_nome,
				   TO_CHAR(cia.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao 
			  FROM entidades.movimento_anexo cia
			 WHERE cia.dt_exclusao IS NULL
			   AND cia.cd_usuario_inclusao IS NOT NULL
			   AND cia.cd_usuario_inclusao_fceee IS NULL
			   AND cia.cd_movimento = ".intval($args['cd_movimento']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	public function excluir_anexo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE entidades.movimento_anexo
			   SET cd_usuario_exclusao_fceee = ".intval($args['cd_usuario']).",
			       dt_exclusao               = CURRENT_TIMESTAMP
			 WHERE cd_movimento_anexo = ".intval($args['cd_movimento_anexo']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	public function salvar_retorno(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE entidades.movimento
			   SET cd_usuario_retorno = ".intval($args['cd_usuario']).",
				   dt_retorno         = CURRENT_TIMESTAMP
			 WHERE cd_movimento = ".intval($args['cd_movimento']).";";
		
		$result = $this->db->query($qr_sql);
	}
}
?>