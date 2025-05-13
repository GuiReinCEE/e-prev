<?php
class Apresentacao_poder_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

    public function get_indicador_lista()
    {
    	$qr_sql = "
    		SELECT ind.cd_indicador, 
			       ind.ds_indicador
			  FROM indicador.indicador ind
			 WHERE ind.dt_exclusao IS NULL
			   AND ind.plugin_nome IS NOT NULL
			   AND ind.fl_poder = 'S'
			 ORDER BY ind.ds_indicador;";

		return $this->db->query($qr_sql)->result_array();
    }

    public function get_indicador($cd_indicador)
    {
    	$qr_sql = "	
			SELECT DISTINCT i.*,
                   it.*,
                   g.*,
				   c.ds_indicador_controle, 
				   u.ds_indicador_unidade_medida, 
				   CASE WHEN i.fl_periodo = 'N' 
						THEN ''
						ELSE ip.ds_periodo
				   END AS ds_periodo,
				   p.procedimento AS ds_processo,
				   (SELECT MAX(ip2.nr_linha) 
                      FROM indicador.indicador_parametro ip2
                     WHERE ip2.cd_indicador_tabela = it.cd_indicador_tabela 
                       AND ip2.dt_exclusao IS NULL) AS maior_linha,
				   (SELECT MAX(ip2.nr_coluna) 
                      FROM indicador.indicador_parametro ip2
                     WHERE ip2.cd_indicador_tabela = it.cd_indicador_tabela 
                       AND ip2.dt_exclusao IS NULL) AS maior_coluna,
				   (SELECT COUNT(*)
                      FROM indicador.indicador_parametro ip2
                     WHERE ip2.cd_indicador_tabela = it.cd_indicador_tabela 
                       AND ip2.dt_exclusao IS NULL) AS quantos
			  FROM indicador.indicador i 
			  JOIN indicador.indicador_controle c 
			    ON c.cd_indicador_controle = i.cd_indicador_controle 
			  JOIN indicador.indicador_unidade_medida u 
			    ON u.cd_indicador_unidade_medida = i.cd_indicador_unidade_medida
			  JOIN indicador.indicador_tabela it 
			    ON it.cd_indicador = i.cd_indicador
			  JOIN indicador.indicador_periodo ip 
			    ON it.cd_indicador_periodo = ip.cd_indicador_periodo
			  JOIN projetos.processos p
				ON p.cd_processo = it.cd_processo				
			  JOIN indicador.indicador_tabela_grafico g
			    ON g.cd_indicador_tabela = it.cd_indicador_tabela 		
			 WHERE i.cd_indicador = ".intval($cd_indicador)."
			   AND i.dt_exclusao IS NULL
			 ORDER BY it.dt_inclusao DESC
			 LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();	
    }

    public function get_indicador_parametro($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT *
              FROM indicador.indicador_parametro 
             WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela)."
               AND dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}
}