<?php
class Simulacao_site_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT ss.cd_simulacao_site,
  				   TO_CHAR(ss.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_simulacao,
  				   (SELECT TO_CHAR(ssa.dt_inclusao, 'DD/MM/YYYY') ||' : ' || ssa.ds_simulacao_site_acompanhamento 
  				      FROM projetos.simulacao_site_acompanhamento ssa
  					   WHERE ssa.cd_simulacao_site = ss.cd_simulacao_site
                 AND ssa.dt_exclusao IS NULL
            ORDER BY ssa.dt_inclusao DESC LIMIT 1) AS acompanhamento,
				     ss.nome,
             ss.telefone,
			       ss.email 
			  FROM projetos.simulacao_site ss
       WHERE ss.cd_empresa = 7
        ".(trim($args['nome']) != '' ? "AND UPPER(funcoes.remove_acento(ss.nome)) LIKE UPPER(funcoes.remove_acento('%".trim($args['nome'])."%'))" : '')."
        ".(trim($args['fl_simulacao']) == 'S' ? "AND  (SELECT COUNT(*) FROM projetos.simulacao_site_acompanhamento ssa
               WHERE ssa.cd_simulacao_site = ss.cd_simulacao_site
                 AND ssa.dt_exclusao IS NULL ) > 0" : '')."
        ".(trim($args['fl_simulacao']) == 'N' ? "AND  (SELECT COUNT(*) FROM projetos.simulacao_site_acompanhamento ssa
               WHERE ssa.cd_simulacao_site = ss.cd_simulacao_site
                 AND ssa.dt_exclusao IS NULL ) = 0 " : '')."
			 GROUP BY ss.cd_simulacao_site
			 ORDER BY ss.dt_inclusao;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_acompanhamento($cd_simulacao_site)
	{
		$qr_sql = "
			SELECT cd_simulacao_site_acompanhamento,
			       cd_simulacao_site, 
			       ds_simulacao_site_acompanhamento, 
			       TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_acompanhamento,
			       funcoes.get_usuario_nome(cd_usuario_inclusao) AS cd_usuario_inclusao
			  FROM projetos.simulacao_site_acompanhamento
			 WHERE dt_exclusao IS NULL
         AND cd_simulacao_site = ".intval($cd_simulacao_site)."
		  ORDER BY dt_inclusao;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_simulacao_site)
    {
        $qr_sql = "
            SELECT cd_simulacao_site,
                   nome,
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao
              FROM projetos.simulacao_site
             WHERE cd_simulacao_site = ".intval($cd_simulacao_site).";";
     
        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $cd_simulacao_site_acompanhamento = intval($this->db->get_new_id('projetos.simulacao_site_acompanhamento', 'cd_simulacao_site_acompanhamento'));
        $qr_sql = "
            INSERT INTO projetos.simulacao_site_acompanhamento
                 (
                    cd_simulacao_site, 
                    ds_simulacao_site_acompanhamento, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                  )
             VALUES 
                  (
                    ".intval($args['cd_simulacao_site']).",
                    ".(trim($args['ds_simulacao_site_acompanhamento']) != '' ? str_escape($args['ds_simulacao_site_acompanhamento']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                  );";

        $this->db->query($qr_sql);

        return $cd_simulacao_site_acompanhamento;
    }

    public function atualizar($cd_simulacao_site_acompanhamento, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.simulacao_site_acompanhamento
               SET ds_simulacao_site_acompanhamento  = ".(trim($args['ds_simulacao_site_acompanhamento']) != '' ? str_escape($args['ds_simulacao_site_acompanhamento']) : "DEFAULT").",
                   cd_usuario_alteracao              = ".intval($args['cd_usuario']).",
                   dt_alteracao                      = CURRENT_TIMESTAMP
             WHERE cd_simulacao_site_acompanhamento  = ".intval($cd_simulacao_site_acompanhamento).";";

        $this->db->query($qr_sql);
    }

    public function carrega_acompanhamento($cd_simulacao_site_acompanhamento)
    {
        $qr_sql = "
            SELECT cd_simulacao_site,
            	   cd_simulacao_site_acompanhamento,
            	   ds_simulacao_site_acompanhamento
              FROM projetos.simulacao_site_acompanhamento
             WHERE cd_simulacao_site_acompanhamento = ".intval($cd_simulacao_site_acompanhamento).";";
     
        return $this->db->query($qr_sql)->row_array();
    }

    public function excluir_acompanhamento($cd_simulacao_site_acompanhamento,$cd_usuario )
    {
      $qr_sql = "
        UPDATE projetos.simulacao_site_acompanhamento
           SET cd_usuario_exclusao  = ".intval($cd_usuario).",
               dt_exclusao          = CURRENT_TIMESTAMP 
         WHERE cd_simulacao_site_acompanhamento = ".intval($cd_simulacao_site_acompanhamento).";";

      $this->db->query($qr_sql);
    }

     public function simulacao($cd_simulacao_site)
     {
        $qr_sql = "
            SELECT cd_simulacao_site,
                   ds_linha,
                   ds_valor
              FROM projetos.simulacao_site_dado
             WHERE cd_simulacao_site = ".intval($cd_simulacao_site).";";
     
        return $this->db->query($qr_sql)->result_array();
     }
}
?>