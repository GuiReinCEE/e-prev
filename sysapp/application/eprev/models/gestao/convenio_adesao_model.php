<?php
class Convenio_adesao_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_plano, $cd_empresa, $args = array())
	{
		$qr_sql = "
			SELECT ca.cd_convenio_adesao,
				   ca.cd_empresa,
				   ca.cd_plano,
				   ca.ds_convenio_adesao,
				   ca.arquivo,
				   ca.arquivo_nome,
				   ca.arquivo_aprovacao,
				   ca.arquivo_aprovacao_nome,
				   ca.arquivo_termo_aditivo,
				   ca.arquivo_termo_aditivo_nome,
				   ca.arquivo_portaria_aprovacao,
				   ca.arquivo_portaria_aprovacao_nome,
				   ca.arquivo_termo_adesao,
				   ca.arquivo_termo_adesao_nome,
				   ca.dt_envio,
				   ca.fl_lgpd,
				   (CASE WHEN ca.fl_lgpd = 'S' THEN 'SIM' ELSE 'NÃO' END) AS ds_lgpd,
				   p.sigla AS empresa,
				   pl.descricao AS plano,
				   ca.arquivo_portaria_aprovacao_adesao,
				   ca.arquivo_portaria_aprovacao_adesao_nome
			  FROM gestao.convenio_adesao ca
			  JOIN public.patrocinadoras p
			    ON p.cd_empresa = ca.cd_empresa
			  JOIN public.planos pl
			    ON pl.cd_plano = ca.cd_plano
			 WHERE ca.dt_exclusao IS NULL
			   AND ca.cd_plano 	  = ".intval($cd_plano)."
			   AND ca.cd_empresa  = ".intval($cd_empresa)."
			   ".(trim($args['cd_plano']) != '' ? "AND ca.cd_plano = ".intval($args['cd_plano']) : "")."
			   ".(trim($args['cd_empresa']) != '' ? "AND ca.cd_empresa = ".intval($args['cd_empresa']) : "")."
			 ORDER BY dt_inclusao DESC
			 LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_empresa_plano($cd_plano = 0)
	{
		$qr_sql = "
			SELECT p.cd_empresa, 
			       p.sigla, 
			       pl.descricao, 
			       pl.cd_plano
			  FROM patrocinadoras p
			  JOIN planos_patrocinadoras pp
			    ON pp.cd_empresa = p.cd_empresa
			  JOIN planos pl
			    ON pl.cd_plano = pp.cd_plano
             WHERE pl.cd_plano > 0	
               ".(intval($cd_plano) > 0 ? "AND pp.cd_plano = ".intval($cd_plano) : "")."
			 ORDER BY pl.descricao, p.sigla;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_planos()
	{
		$qr_sql = "
			SELECT cd_plano,
			       descricao
			  FROM public.planos
			 WHERE cd_plano > 0;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_convenio_adesao)
	{
		$qr_sql = "
			SELECT ca.cd_convenio_adesao,
				   ca.cd_empresa,
				   ca.cd_plano,
				   ca.ds_convenio_adesao,
				   ca.arquivo,
				   ca.arquivo_nome,
				   ca.arquivo_aprovacao,
				   ca.arquivo_aprovacao_nome,
				   ca.arquivo_termo_aditivo,
				   ca.arquivo_termo_aditivo_nome,
				   ca.arquivo_portaria_aprovacao,
				   ca.arquivo_portaria_aprovacao_nome,
				   ca.arquivo_termo_adesao,
				   ca.arquivo_termo_adesao_nome,
				   ca.fl_lgpd,
				   TO_CHAR(ca.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   funcoes.get_usuario_nome(ca.cd_usuario_envio) AS ds_usuario_envio,
				   p.sigla AS empresa,
				   pl.descricao AS plano,
				   ca.arquivo_portaria_aprovacao_adesao,
				   ca.arquivo_portaria_aprovacao_adesao_nome
			  FROM gestao.convenio_adesao ca
			  JOIN public.patrocinadoras p
			    ON p.cd_empresa = ca.cd_empresa
			  JOIN public.planos pl
			    ON pl.cd_plano = ca.cd_plano
			 WHERE ca.dt_exclusao IS NULL
			   AND ca.cd_convenio_adesao = ".intval($cd_convenio_adesao).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function listar_anteriores($cd_convenio_adesao, $cd_plano, $cd_empresa)
	{
		$qr_sql = "
			SELECT ca.cd_convenio_adesao,
				   ca.cd_empresa,
				   ca.cd_plano,
				   ca.ds_convenio_adesao,
				   ca.arquivo,
				   ca.arquivo_nome,
				   ca.arquivo_aprovacao,
				   ca.arquivo_aprovacao_nome,
				   ca.arquivo_termo_aditivo,
				   ca.arquivo_termo_aditivo_nome,
				   ca.arquivo_portaria_aprovacao,
				   ca.arquivo_portaria_aprovacao_nome,
				   ca.arquivo_termo_adesao,
				   ca.arquivo_termo_adesao_nome,
				   ca.fl_lgpd,
				   (CASE WHEN ca.fl_lgpd = 'S' THEN 'SIM' ELSE 'NÃO' END) AS ds_lgpd,				   
				   p.sigla AS empresa,
				   pl.descricao AS plano,
				   ca.arquivo_portaria_aprovacao_adesao,
				   ca.arquivo_portaria_aprovacao_adesao_nome
			  FROM gestao.convenio_adesao ca
			  JOIN public.patrocinadoras p
			    ON p.cd_empresa = ca.cd_empresa
			  JOIN public.planos pl
			    ON pl.cd_plano = ca.cd_plano
			 WHERE ca.dt_exclusao IS NULL
			   AND ca.cd_convenio_adesao NOT IN (".intval($cd_convenio_adesao).")
			   AND ca.cd_plano 		 	 = ".intval($cd_plano)."
			   AND ca.cd_empresa 		 = ".intval($cd_empresa).";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO gestao.convenio_adesao
				(
					cd_empresa,
					cd_plano,
					ds_convenio_adesao,
					arquivo,
					arquivo_nome,
					arquivo_aprovacao,
					arquivo_aprovacao_nome,
					arquivo_termo_aditivo,
					arquivo_termo_aditivo_nome,
					arquivo_portaria_aprovacao,
					arquivo_portaria_aprovacao_nome,
					arquivo_termo_adesao,
					arquivo_termo_adesao_nome,
				    arquivo_portaria_aprovacao_adesao,
				    arquivo_portaria_aprovacao_adesao_nome,
					fl_lgpd,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				)
			VALUES
				(
					".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
					".(trim($args['cd_plano']) != '' ? intval($args['cd_plano']) : "DEFAULT").",
					".(trim($args['ds_convenio_adesao']) != '' ? str_escape($args['ds_convenio_adesao']) : "DEFAULT").",
					".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
					".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
					".(trim($args['arquivo_aprovacao']) != '' ? str_escape($args['arquivo_aprovacao']) : "DEFAULT").",
					".(trim($args['arquivo_aprovacao_nome']) != '' ? str_escape($args['arquivo_aprovacao_nome']) : "DEFAULT").",
					".(trim($args['arquivo_termo_aditivo']) != '' ? str_escape($args['arquivo_termo_aditivo']) : "DEFAULT").",
					".(trim($args['arquivo_termo_aditivo_nome']) != '' ? str_escape($args['arquivo_termo_aditivo_nome']) : "DEFAULT").",
					".(trim($args['arquivo_portaria_aprovacao']) != '' ? str_escape($args['arquivo_portaria_aprovacao']) : "DEFAULT").",
					".(trim($args['arquivo_portaria_aprovacao_nome']) != '' ? str_escape($args['arquivo_portaria_aprovacao_nome']) : "DEFAULT").",
					".(trim($args['arquivo_termo_adesao']) != '' ? str_escape($args['arquivo_termo_adesao']) : "DEFAULT").",
					".(trim($args['arquivo_termo_adesao_nome']) != '' ? str_escape($args['arquivo_termo_adesao_nome']) : "DEFAULT").",
					".(trim($args['arquivo_portaria_aprovacao_adesao']) != '' ? str_escape($args['arquivo_portaria_aprovacao_adesao']) : "DEFAULT").",
					".(trim($args['arquivo_portaria_aprovacao_adesao_nome']) != '' ? str_escape($args['arquivo_portaria_aprovacao_adesao_nome']) : "DEFAULT").",
					".(trim($args['fl_lgpd']) != '' ? str_escape($args['fl_lgpd']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
				);";

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_convenio_adesao, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.convenio_adesao
			   SET cd_empresa 					          = ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
				   cd_plano 					          = ".(trim($args['cd_plano']) != '' ? intval($args['cd_plano']) : "DEFAULT").",
				   ds_convenio_adesao 			          = ".(trim($args['ds_convenio_adesao']) != '' ? str_escape($args['ds_convenio_adesao']) : "DEFAULT").",
				   arquivo 						          = ".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
				   arquivo_nome 				          = ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
				   arquivo_aprovacao 			          = ".(trim($args['arquivo_aprovacao']) != '' ? str_escape($args['arquivo_aprovacao']) : "DEFAULT").",
				   arquivo_aprovacao_nome 		          = ".(trim($args['arquivo_aprovacao_nome']) != '' ? str_escape($args['arquivo_aprovacao_nome']) : "DEFAULT").",
				   arquivo_termo_aditivo 		          = ".(trim($args['arquivo_termo_aditivo']) != '' ? str_escape($args['arquivo_termo_aditivo']) : "DEFAULT").",
				   arquivo_termo_aditivo_nome 	          = ".(trim($args['arquivo_termo_aditivo_nome']) != '' ? str_escape($args['arquivo_termo_aditivo_nome']) : "DEFAULT").",
				   arquivo_portaria_aprovacao 	          = ".(trim($args['arquivo_portaria_aprovacao']) != '' ? str_escape($args['arquivo_portaria_aprovacao']) : "DEFAULT").",
				   arquivo_portaria_aprovacao_nome        = ".(trim($args['arquivo_portaria_aprovacao_nome']) != '' ? str_escape($args['arquivo_portaria_aprovacao_nome']) : "DEFAULT").",
				   arquivo_termo_adesao 		          = ".(trim($args['arquivo_termo_adesao']) != '' ? str_escape($args['arquivo_termo_adesao']) : "DEFAULT").",
				   arquivo_termo_adesao_nome 	          = ".(trim($args['arquivo_termo_adesao_nome']) != '' ? str_escape($args['arquivo_termo_adesao_nome']) : "DEFAULT").",
				   arquivo_portaria_aprovacao_adesao 	  = ".(trim($args['arquivo_portaria_aprovacao_adesao']) != '' ? str_escape($args['arquivo_portaria_aprovacao_adesao']) : "DEFAULT").",
				   arquivo_portaria_aprovacao_adesao_nome = ".(trim($args['arquivo_portaria_aprovacao_adesao_nome']) != '' ? str_escape($args['arquivo_portaria_aprovacao_adesao_nome']) : "DEFAULT").",
				   fl_lgpd 	                              = ".(trim($args['fl_lgpd']) != '' ? str_escape($args['fl_lgpd']) : "DEFAULT").",
				   cd_usuario_alteracao 		          = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
				   dt_alteracao 				          = CURRENT_TIMESTAMP
			 WHERE cd_convenio_adesao = ".intval($cd_convenio_adesao).";";

		$this->db->query($qr_sql);
	}

	public function enviar($cd_convenio_adesao, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.convenio_adesao
               SET cd_usuario_envio = ".intval($cd_usuario).", 
			       dt_envio         =  CURRENT_TIMESTAMP
             WHERE cd_convenio_adesao = ".intval($cd_convenio_adesao).";";

        $this->db->query($qr_sql);  
	}
}