<?php
class Regulamento_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

	public function listar($cd_regulamento_tipo, $args = array())
	{
		$qr_sql = "
            SELECT r.cd_regulamento,
                   TO_CHAR(r.dt_aprovacao_cd, 'DD/MM/YYYY') AS dt_aprovacao_cd,
                   TO_CHAR(r.dt_aprovacao_previc, 'DD/MM/YYYY') AS dt_aprovacao_previc,
                   TO_CHAR(r.dt_envio_previc, 'DD/MM/YYYY') AS dt_envio_previc,
                   TO_CHAR(rt.dt_encerramento_plano, 'DD/MM/YYYY') AS dt_encerramento_plano,
                   r.nr_ata_cd,
                   r.ds_aprovacao_previc,
                   r.cd_regulamento_tipo,
                   r.arquivo_aprovacao_previc,
                   r.arquivo_aprovacao_previc_nome,
                   r.arquivo,
                   r.arquivo_nome,
                   r.arquivo_comparativo,
                   r.arquivo_comparativo_nome,
                   rt.ds_regulamento_tipo,
                   rt.ds_cnpb,
                   r.cd_gerencia_responsavel,
                   CASE WHEN r.fl_publicado_site = 'S'
                        THEN 'Sim'
                        ELSE 'Não'
                   END AS ds_publicado_site
              FROM gestao.regulamento r
              JOIN gestao.regulamento_tipo rt 
                ON r.cd_regulamento_tipo = rt.cd_regulamento_tipo
             WHERE r.dt_exclusao IS NULL
               AND r.cd_regulamento_tipo = ".intval($cd_regulamento_tipo)." 
               ".(trim($args['nr_ata_cd']) != '' ? "AND r.nr_ata_cd = ".intval($args['nr_ata_cd']) : "")."
               ".(((trim($args['dt_aprovacao_previc_ini']) != '') AND trim($args['dt_aprovacao_cd_fim']) != '') ? "AND DATE_TRUNC('day', dt_aprovacao_cd) BETWEEN TO_DATE('".$args['dt_aprovacao_previc_ini']."', 'DD/MM/YYY') AND TO_DATE('".$args['dt_aprovacao_cd_fim']."', 'DD/MM/YYY')" : '')."
               ".(((trim($args['dt_aprovacao_previc_ini']) != '') AND trim($args['dt_aprovacao_previc_fim']) != '') ? "AND DATE_TRUNC('day', dt_aprovacao_previc) BETWEEN TO_DATE('".$args['dt_aprovacao_previc_ini']."', 'DD/MM/YYY') AND TO_DATE('".$args['dt_aprovacao_previc_fim']."', 'DD/MM/YYY')" : '')."
               ".(((trim($args['dt_envio_previc_ini']) != '') AND trim($args['dt_envio_previc_fim']) != '') ? "AND DATE_TRUNC('day', dt_envio_previc) BETWEEN TO_DATE('".$args['dt_envio_previc_ini']."', 'DD/MM/YYY') AND TO_DATE('".$args['dt_envio_previc_fim']."', 'DD/MM/YYY')" : '')."
             ORDER BY r.dt_aprovacao_cd DESC, r.dt_inclusao DESC
             LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_regulamento_tipo()
	{
		$qr_sql = "
			SELECT cd_regulamento_tipo AS value,
                   ds_regulamento_tipo AS text
			  FROM gestao.regulamento_tipo
			 WHERE dt_exclusao IS NULL
			   AND cd_regulamento_tipo_vigente IS NULL
             ORDER BY ds_regulamento_tipo;";

		return $this->db->query($qr_sql)->result_array();
	}	

	public function lista_regulamento_tipo($args = array(), $fl_desligado = '')
	{
		$qr_sql = "
			SELECT cd_regulamento_tipo,
                   ds_regulamento_tipo 
			  FROM gestao.regulamento_tipo 
			 WHERE dt_exclusao                 IS NULL
			   AND cd_regulamento_tipo_vigente IS NULL
			   ".(trim($args['cd_regulamento_tipo']) != '' ? "AND cd_regulamento_tipo = ".intval($args['cd_regulamento_tipo']) : "")." 
			   ".(trim($fl_desligado) == 'N' ? "AND dt_encerramento_plano IS NULL" : "")." 
			   ".(trim($fl_desligado) == 'S' ? "AND dt_encerramento_plano IS NOT NULL" : "")." 
             ORDER BY ds_regulamento_tipo;";

		return $this->db->query($qr_sql)->result_array();
	}	
  
	public function carrega($cd_regulamento)
	{
		$qr_sql = "
			SELECT r.cd_regulamento,
			       TO_CHAR(r.dt_aprovacao_cd, 'DD/MM/YYYY') AS dt_aprovacao_cd,
			       TO_CHAR(r.dt_aprovacao_previc, 'DD/MM/YYYY') AS dt_aprovacao_previc,
			       TO_CHAR(r.dt_envio_previc, 'DD/MM/YYYY') AS dt_envio_previc,
			       TO_CHAR(r.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
			       r.nr_ata_cd,
			       r.ds_aprovacao_previc,
			       r.arquivo_aprovacao_previc,
			       r.arquivo_aprovacao_previc_nome,
			       r.arquivo,
			       r.arquivo_nome,
			       r.arquivo_comparativo,
			       r.arquivo_comparativo_nome,
			       r.cd_regulamento_tipo,
			       funcoes.get_usuario_nome(r.cd_usuario_envio) AS ds_usuario_envio,
			       rt.ds_regulamento_tipo,
                   rt.ds_cnpb,
                   r.fl_envio_email,
                   r.cd_gerencia_responsavel,
                   r.fl_publicado_site
			  FROM gestao.regulamento r
			  JOIN gestao.regulamento_tipo rt 
                ON r.cd_regulamento_tipo = rt.cd_regulamento_tipo
			 WHERE r.cd_regulamento = ".intval($cd_regulamento).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_regulamento = intval($this->db->get_new_id('gestao.regulamento', 'cd_regulamento'));

		$qr_sql = "
			INSERT INTO gestao.regulamento
			     (
			       cd_regulamento,
			       cd_gerencia_responsavel,
			       fl_publicado_site,
			       dt_aprovacao_cd, 
			       dt_aprovacao_previc, 
			       dt_envio_previc,
			       nr_ata_cd,
			       ds_aprovacao_previc,
			       cd_regulamento_tipo,
			       arquivo_aprovacao_previc,
			       arquivo_aprovacao_previc_nome,
			       arquivo,
			       arquivo_nome,
			       arquivo_comparativo,
			       arquivo_comparativo_nome,
			       fl_envio_email,
			       cd_usuario_inclusao,
               	   cd_usuario_alteracao
			     )           
                       
			VALUES
			     (
			        ".intval($cd_regulamento).",
			        ".(trim($args['cd_gerencia_responsavel']) != '' ? "'".$args['cd_gerencia_responsavel']."'" : "DEFAULT").",
			        ".(trim($args['fl_publicado_site']) != '' ? "'".$args['fl_publicado_site']."'" : "DEFAULT").",
				    ".(trim($args['dt_aprovacao_cd']) != '' ? "TO_DATE('".$args['dt_aprovacao_cd']."', 'DD/MM/YYYY')" : "DEFAULT").",
				    ".(trim($args['dt_aprovacao_previc']) != '' ? "TO_DATE('".$args['dt_aprovacao_previc']."', 'DD/MM/YYYY')" : "DEFAULT").",
				    ".(trim($args['dt_envio_previc']) != '' ? "TO_DATE('".$args['dt_envio_previc']."', 'DD/MM/YYYY')" : "DEFAULT").",
                    ".(trim($args['nr_ata_cd']) != '' ? intval($args['nr_ata_cd']) : "DEFAULT").",
			     	".(trim($args['ds_aprovacao_previc']) != '' ? str_escape($args['ds_aprovacao_previc']) : "DEFAULT").",                    			        
			     	".(trim($args['cd_regulamento_tipo']) != '' ? intval($args['cd_regulamento_tipo']) : "DEFAULT").",
				    ".(trim($args['arquivo_aprovacao_previc']) != '' ? "'".$args['arquivo_aprovacao_previc']."'" : "DEFAULT").",
				    ".(trim($args['arquivo_aprovacao_previc_nome']) != '' ? "'".$args['arquivo_aprovacao_previc_nome']."'" : "DEFAULT").",
				    ".(trim($args['arquivo']) != '' ? "'".$args['arquivo']."'" : "DEFAULT").",
                    ".(trim($args['arquivo_nome']) != '' ? "'".$args['arquivo_nome']."'" : "DEFAULT" ).",
                    ".(trim($args['arquivo_comparativo']) != '' ? "'".$args['arquivo_comparativo']."'" : "DEFAULT" ).",
                    ".(trim($args['arquivo_comparativo_nome']) != '' ? "'".$args['arquivo_comparativo_nome']."'" : "DEFAULT" ).",
                    ".(trim($args['fl_envio_email']) != '' ? "'".trim($args['fl_envio_email'])."'" : "DEFAULT" ).",
				    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
			     );";

		$this->db->query($qr_sql); 

		return $cd_regulamento;
	}

	public function atualizar($cd_regulamento, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.regulamento
               SET cd_gerencia_responsavel        = ".(trim($args['cd_gerencia_responsavel']) != '' ? "'".$args['cd_gerencia_responsavel']."'" : "DEFAULT").",
                   fl_publicado_site              = ".(trim($args['fl_publicado_site']) != '' ? "'".$args['fl_publicado_site']."'" : "DEFAULT").",
                   dt_aprovacao_cd                = ".(trim($args['dt_aprovacao_cd']) != '' ? "TO_DATE('".$args['dt_aprovacao_cd']."', 'DD/MM/YYYY')" : "DEFAULT").",
                   dt_aprovacao_previc            = ".(trim($args['dt_aprovacao_previc']) != '' ? "TO_DATE('".$args['dt_aprovacao_previc']."', 'DD/MM/YYYY')" : "DEFAULT").",
                   dt_envio_previc                = ".(trim($args['dt_envio_previc']) != '' ? "TO_DATE('".$args['dt_envio_previc']."', 'DD/MM/YYYY')" : "DEFAULT").",
 	               nr_ata_cd                      = ".(trim($args['nr_ata_cd']) != '' ? intval($args['nr_ata_cd']) : "DEFAULT").",
 	               ds_aprovacao_previc            = ".(trim($args['ds_aprovacao_previc']) != '' ? str_escape($args['ds_aprovacao_previc']) : "DEFAULT").",
                   cd_regulamento_tipo            = ".(trim($args['cd_regulamento_tipo']) != '' ? intval($args['cd_regulamento_tipo']) : "DEFAULT").",
                   arquivo_aprovacao_previc       = ".(trim($args['arquivo_aprovacao_previc']) != '' ? "'".$args['arquivo_aprovacao_previc']."'" : "DEFAULT").",                  
                   arquivo_aprovacao_previc_nome  = ".(trim($args['arquivo_aprovacao_previc_nome']) != '' ? "'".$args['arquivo_aprovacao_previc_nome']."'" : "DEFAULT").",                  
                   arquivo                        = ".(trim($args['arquivo']) != '' ? "'".$args['arquivo']."'" : "DEFAULT").",                  
                   arquivo_nome                   = ".(trim($args['arquivo_nome']) != '' ? "'".$args['arquivo_nome']."'" : "DEFAULT" ).",
                   arquivo_comparativo            = ".(trim($args['arquivo_comparativo']) != '' ? "'".$args['arquivo_comparativo']."'" : "DEFAULT" ).",
                   arquivo_comparativo_nome       = ".(trim($args['arquivo_comparativo_nome']) != '' ? "'".$args['arquivo_comparativo_nome']."'" : "DEFAULT" ).",
                   fl_envio_email                 = ".(trim($args['fl_envio_email']) != '' ? "'".trim($args['fl_envio_email'])."'" : "DEFAULT" ).",
			       cd_usuario_alteracao           = ".intval($args['cd_usuario']).", 
			       dt_alteracao                   =  CURRENT_TIMESTAMP
             WHERE cd_regulamento = ".intval($cd_regulamento).";";

        $this->db->query($qr_sql);  
	}

	public function lista_versoes_anteriores($cd_regulamento, $cd_regulamento_tipo)
	{
		$qr_sql = "
            SELECT r.cd_regulamento,
                   TO_CHAR(r.dt_aprovacao_cd, 'DD/MM/YYYY') AS dt_aprovacao_cd,
                   TO_CHAR(r.dt_aprovacao_previc, 'DD/MM/YYYY') AS dt_aprovacao_previc,
                   TO_CHAR(r.dt_envio_previc, 'DD/MM/YYYY') AS dt_envio_previc,
                   r.nr_ata_cd,
                   r.ds_aprovacao_previc,
                   r.cd_regulamento_tipo,
                   r.arquivo_aprovacao_previc,
                   r.arquivo_aprovacao_previc_nome,
                   r.arquivo,
                   r.arquivo_nome,
                   r.arquivo_comparativo,
                   r.arquivo_comparativo_nome,
                   rt.ds_regulamento_tipo 
              FROM gestao.regulamento r
              JOIN gestao.regulamento_tipo rt 
                ON r.cd_regulamento_tipo = rt.cd_regulamento_tipo
             WHERE r.dt_exclusao IS NULL
               AND (
                    r.cd_regulamento_tipo          = ".intval($cd_regulamento_tipo)."
                    OR
                    rt.cd_regulamento_tipo_vigente = ".intval($cd_regulamento_tipo)."
                   )
               AND r.cd_regulamento != ".intval($cd_regulamento)."
	         ORDER BY r.dt_aprovacao_cd DESC, r.dt_inclusao DESC";

		return $this->db->query($qr_sql)->result_array();
	}

	public function enviar($cd_regulamento, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.regulamento
               SET cd_usuario_envio = ".intval($cd_usuario).", 
			       dt_envio         =  CURRENT_TIMESTAMP
             WHERE cd_regulamento = ".intval($cd_regulamento).";";

        $this->db->query($qr_sql);  
	}

}
?>