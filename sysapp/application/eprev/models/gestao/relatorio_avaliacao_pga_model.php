<?php
class Relatorio_avaliacao_pga_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT DISTINCT ON (rap.cd_relatorio_avaliacao_pga)
				   rap.cd_relatorio_avaliacao_pga,
				   rap.nr_ano,
				   rap.nr_trimestre,
				   (SELECT COUNT(rapd.*)
                      FROM gestao.relatorio_avaliacao_pga_diretoria rapd
                     WHERE rapd.cd_relatorio_avaliacao_pga = rap.cd_relatorio_avaliacao_pga) AS qt_diretores,
                   (SELECT COUNT(rapd2.*)
                      FROM gestao.relatorio_avaliacao_pga_diretoria rapd2
                     WHERE rapd2.dt_assinatura IS NOT NULL
                       AND rapd2.cd_relatorio_avaliacao_pga = rap.cd_relatorio_avaliacao_pga) AS qt_assinados,
				   funcoes.get_usuario_nome(rap.  cd_usuario_encerramento) AS cd_usuario_encerramento,
				   TO_CHAR(rap.dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento,
				   TO_CHAR(rapit.dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
				   (SELECT TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS')
			       	  FROM gestao.relatorio_avaliacao_pga_apresentacao a
			       	 WHERE a.cd_relatorio_avaliacao_pga = rap.cd_relatorio_avaliacao_pga
			       	 ORDER BY a.dt_inclusao DESC
			       	 LIMIT 1) AS dt_apresentacao
			  FROM gestao.relatorio_avaliacao_pga rap
			  LEFT JOIN gestao.relatorio_avaliacao_pga_indicador rapi
			    ON rap.cd_relatorio_avaliacao_pga = rapi.cd_relatorio_avaliacao_pga
			  LEFT JOIN gestao.relatorio_avaliacao_pga_indicador_tabela rapit
			    ON rapi.cd_relatorio_avaliacao_pga_indicador = rapit.cd_relatorio_avaliacao_pga_indicador
			 WHERE rap.dt_exclusao IS NULL
			  ".(intval($args['nr_ano']) > 0 ? "AND rap.nr_ano = ".intval($args['nr_ano']) : '')."
			  ".(intval($args['nr_trimestre']) > 0 ? "AND rap.nr_trimestre = ".intval($args['nr_trimestre']) : '').";";
			   
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_relatorio_avaliacao_pga)
	{
		$qr_sql = "
			SELECT rap.cd_relatorio_avaliacao_pga,
				   rap.nr_ano,
				   rap.nr_trimestre,
				   (SELECT cd_usuario_diretoria
					  FROM gestao.relatorio_avaliacao_pga_diretoria
					 WHERE cd_diretoria = 'SEG'
					   AND cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga).") AS cd_dir_seguridade,
				   (SELECT TO_CHAR(dt_assinatura, 'DD/MM/YYYY HH24:MI:SS') AS dt_assinatura
					  FROM gestao.relatorio_avaliacao_pga_diretoria
					 WHERE cd_diretoria = 'SEG'
					   AND cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga).") AS dt_assinatura_seg,
				   (SELECT cd_relatorio_avaliacao_pga_diretoria
					  FROM gestao.relatorio_avaliacao_pga_diretoria
					 WHERE cd_diretoria = 'SEG'
					   AND cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga).") AS cd_relatorio_avaliacao_pga_diretoria_seg,
				   (SELECT cd_usuario_diretoria
					  FROM gestao.relatorio_avaliacao_pga_diretoria
					 WHERE cd_diretoria = 'PRE'
					   AND cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga).") AS cd_presidente,
				   (SELECT TO_CHAR(dt_assinatura, 'DD/MM/YYYY HH24:MI:SS') AS dt_assinatura
					  FROM gestao.relatorio_avaliacao_pga_diretoria
					 WHERE cd_diretoria = 'PRE'
					   AND cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga).") AS dt_assinatura_pre,
				   (SELECT cd_relatorio_avaliacao_pga_diretoria
					  FROM gestao.relatorio_avaliacao_pga_diretoria
					 WHERE cd_diretoria = 'PRE'
					   AND cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga).") AS cd_relatorio_avaliacao_pga_diretoria_pre,
				   (SELECT cd_usuario_diretoria
					  FROM gestao.relatorio_avaliacao_pga_diretoria
					 WHERE cd_diretoria = 'ADM'
					   AND cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga).") AS cd_dir_administrativo,
				   (SELECT TO_CHAR(dt_assinatura, 'DD/MM/YYYY HH24:MI:SS') AS dt_assinatura
					  FROM gestao.relatorio_avaliacao_pga_diretoria
					 WHERE cd_diretoria = 'ADM'
					   AND cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga).") AS dt_assinatura_adm,
				   (SELECT cd_relatorio_avaliacao_pga_diretoria
					  FROM gestao.relatorio_avaliacao_pga_diretoria
					 WHERE cd_diretoria = 'ADM'
					   AND cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga).") AS cd_relatorio_avaliacao_pga_diretoria_adm,
				   (SELECT cd_usuario_diretoria
					  FROM gestao.relatorio_avaliacao_pga_diretoria
					 WHERE cd_diretoria = 'FIN'
					   AND cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga).") AS cd_dir_financeiro,
				   (SELECT TO_CHAR(dt_assinatura, 'DD/MM/YYYY HH24:MI:SS') AS dt_assinatura
					  FROM gestao.relatorio_avaliacao_pga_diretoria
					 WHERE cd_diretoria = 'FIN'
					   AND cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga).") AS dt_assinatura_fin,
				   (SELECT cd_relatorio_avaliacao_pga_diretoria
					  FROM gestao.relatorio_avaliacao_pga_diretoria
					 WHERE cd_diretoria = 'FIN'
					   AND cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga).") AS cd_relatorio_avaliacao_pga_diretoria_fin,
				   TO_CHAR(rapit.dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
				   funcoes.get_usuario_nome(rap.  cd_usuario_encerramento) AS cd_usuario_encerramento,
				   TO_CHAR(rap.dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento,
				   (SELECT COUNT(rapi2.*)
				      FROM gestao.relatorio_avaliacao_pga_indicador rapi2
					 WHERE rapi2.cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga)."
					   AND rapi2.ds_avaliacao IS NOT NULL) AS avaliados
			  FROM gestao.relatorio_avaliacao_pga rap
			  LEFT JOIN gestao.relatorio_avaliacao_pga_indicador rapi
			    ON rap.cd_relatorio_avaliacao_pga = rapi.cd_relatorio_avaliacao_pga
			  LEFT JOIN gestao.relatorio_avaliacao_pga_indicador_tabela rapit
			    ON rapi.cd_relatorio_avaliacao_pga_indicador = rapit.cd_relatorio_avaliacao_pga_indicador
			  LEFT JOIN gestao.relatorio_avaliacao_pga_diretoria rapd
				ON rapd.cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga)."
			 WHERE rap.cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga)."
			 LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($cd_usuario, $args = array())
	{
		$cd_relatorio_avaliacao_pga = intval($this->db->get_new_id('gestao.relatorio_avaliacao_pga', 'cd_relatorio_avaliacao_pga'));

		$qr_sql = "
			INSERT INTO gestao.relatorio_avaliacao_pga
			     (
			       cd_relatorio_avaliacao_pga,
			       nr_ano,
				   nr_trimestre,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_relatorio_avaliacao_pga).",
                    ".intval($args['nr_ano']).",
                    ".intval($args['nr_trimestre']).",
			        ".intval($cd_usuario).",
			        ".intval($cd_usuario)."
			     );";
				 
		$this->db->query($qr_sql);
		
		return intval($cd_relatorio_avaliacao_pga);
	}

	public function atualizar($cd_relatorio_avaliacao_pga, $cd_usuario, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.relatorio_avaliacao_pga
               SET nr_ano				= ".intval($args['nr_ano']).",
				   nr_trimestre			= ".intval($args['nr_trimestre']).",
				   cd_usuario_alteracao = ".intval($cd_usuario).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga).";";

        $this->db->query($qr_sql);  
	}
	
	public function get_indicadores($cd_relatorio_avaliacao_pga)
	{
		$qr_sql = "
			SELECT rapi.cd_indicador,
				   rapi.cd_relatorio_avaliacao_pga_indicador,
				   (SELECT lit.cd_indicador_tabela 
					  FROM indicador.listar_indicador_tabela_aberta_de_indicador lit 
					 WHERE lit.cd_indicador = rapi.cd_indicador 
					 ORDER BY nr_ano_referencia ASC 
					 LIMIT 1) AS cd_indicador_tabela
			  FROM gestao.relatorio_avaliacao_pga_indicador rapi
			 WHERE rapi.cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga)."
			   AND rapi.dt_exclusao IS NULL;";
			   
        return $this->db->query($qr_sql)->result_array();
	}
	
	public function indicador_tabela($cd_indicador_tabela)
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
			 WHERE it.cd_indicador_tabela = ".intval($cd_indicador_tabela).";";

		return $this->db->query($qr_sql)->row_array();	
	}
	
	public function indicador_parametro($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT *
              FROM indicador.indicador_parametro 
             WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela)."
               AND dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	} 

	public function get_assinaturas($cd_relatorio_avaliacao_pga)
	{
		$qr_sql = "
	  		SELECT COUNT(*) AS qt_assinaturas
              FROM gestao.relatorio_avaliacao_pga_diretoria 
             WHERE cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga)."
               AND dt_assinatura IS NULL;";

        return $this->db->query($qr_sql)->row_array();
	}

	public function get_relatorio_avaliacao_pga_indicador_tabela($cd_indicador, $cd_relatorio_avaliacao_pga_indicador)
	{
		$qr_sql = "
			SELECT rapit.cd_relatorio_avaliacao_pga_indicador_tabela,
				   rapi.cd_indicador
			  FROM gestao.relatorio_avaliacao_pga_indicador_tabela rapit
			  JOIN gestao.relatorio_avaliacao_pga_indicador rapi
			    ON rapi.cd_relatorio_avaliacao_pga_indicador = rapit.cd_relatorio_avaliacao_pga_indicador
			 WHERE rapit.cd_relatorio_avaliacao_pga_indicador = ".intval($cd_relatorio_avaliacao_pga_indicador)."
			   AND rapi.cd_indicador                          = ".intval($cd_indicador)."
			   AND rapit.dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->row_array();	
	}

	public function atualizar_indicador_tabela($cd_relatorio_avaliacao_pga_indicador_tabela, $cd_usuario, $indicador_tabela)
	{
		$qr_sql = "
			UPDATE gestao.relatorio_avaliacao_pga_indicador_tabela
			   SET parametro            = ".str_escape($indicador_tabela).",
			       cd_usuario_alteracao = ".intval($cd_usuario).", 
			       dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_relatorio_avaliacao_pga_indicador_tabela = ".intval($cd_relatorio_avaliacao_pga_indicador_tabela).";";

		$this->db->query($qr_sql);
	}

	public function salvar_indicador_tabela($cd_relatorio_avaliacao_pga_indicador, $cd_usuario, $indicador_tabela)
	{
		$qr_sql = "
			INSERT INTO gestao.relatorio_avaliacao_pga_indicador_tabela
			     (
                   cd_relatorio_avaliacao_pga_indicador,
                   parametro, 
                   cd_usuario_inclusao, 
                   cd_usuario_alteracao
                 )
            VALUES 
                 (
                 	".intval($cd_relatorio_avaliacao_pga_indicador).",
                 	".str_escape($indicador_tabela).",
                 	".intval($cd_usuario).",
                 	".intval($cd_usuario)."
                 );";

		$this->db->query($qr_sql);
	}

	public function atualizar_apresentacao($cd_relatorio_avaliacao_pga, $cd_usuario)
	{	
		$qr_sql = "
			INSERT INTO gestao.relatorio_avaliacao_pga_apresentacao
			     (
                   cd_relatorio_avaliacao_pga, 
                   cd_usuario_inclusao
                 )
            VALUES 
                 (
                 	".intval($cd_relatorio_avaliacao_pga).",
                 	".intval($cd_usuario)."
                 );";

        $this->db->query($qr_sql);  
	}

	public function listar_indicador_pga()
	{
		$qr_sql = "
			SELECT i.cd_indicador
			  FROM indicador.indicador i
			 WHERE i.cd_indicador_grupo = 21 
			   AND i.dt_exclusao IS NULL;";
		
		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_indicador($cd_relatorio_avaliacao_pga)
	{
		$qr_sql = "
			SELECT i.ds_indicador,
				   i.cd_indicador,
				   rapi.cd_relatorio_avaliacao_pga_indicador,
				   rapi.cd_relatorio_avaliacao_pga,
				   rapi.ds_avaliacao,
				   funcoes.get_usuario_nome(rapi.cd_usuario_inclusao) AS cd_usuario_inclusao,
				   funcoes.get_usuario_nome(rapi.cd_usuario_alteracao) AS cd_usuario_alteracao,
				   TO_CHAR(rapi.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(rapi.dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao
			  FROM indicador.indicador i
			  JOIN gestao.relatorio_avaliacao_pga_indicador rapi
			    ON rapi.cd_indicador = i.cd_indicador
			  JOIN gestao.relatorio_avaliacao_pga rap
			    ON rap.cd_relatorio_avaliacao_pga = rapi.cd_relatorio_avaliacao_pga
			   AND rapi.cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga)."
			 WHERE i.cd_indicador_grupo = 21;";
		
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_indicador($cd_relatorio_avaliacao_pga_indicador)
	{
		$qr_sql = "
			SELECT rapi.cd_indicador,
				   rapi.cd_relatorio_avaliacao_pga_indicador,
				   rapi.cd_relatorio_avaliacao_pga,
				   rapi.ds_avaliacao
			  FROM gestao.relatorio_avaliacao_pga_indicador rapi
			 WHERE rapi.dt_exclusao IS NULL
			   AND rapi.cd_relatorio_avaliacao_pga_indicador = ".intval($cd_relatorio_avaliacao_pga_indicador).";";
		
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar_indicador($cd_usuario, $cd_relatorio_avaliacao_pga, $args = array())
	{
		$cd_relatorio_avaliacao_pga_indicador = intval($this->db->get_new_id('gestao.relatorio_avaliacao_pga_indicador', 'cd_relatorio_avaliacao_pga_indicador'));

		$qr_sql = "
			INSERT INTO gestao.relatorio_avaliacao_pga_indicador
			     (
			       cd_relatorio_avaliacao_pga_indicador,
			       cd_relatorio_avaliacao_pga,
			       cd_indicador,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_relatorio_avaliacao_pga_indicador).",
			     	".intval($cd_relatorio_avaliacao_pga).",
			     	".intval($args['cd_indicador']).",
			        ".intval($cd_usuario).",
			        ".intval($cd_usuario)."
			     );";	
				 
		$this->db->query($qr_sql);
	}

	public function atualizar_indicador($cd_relatorio_avaliacao_pga_indicador, $cd_usuario, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.relatorio_avaliacao_pga_indicador
               SET ds_avaliacao			= '".trim($args['ds_avaliacao'])."',
				   cd_usuario_alteracao = ".intval($cd_usuario).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_relatorio_avaliacao_pga_indicador = ".intval($cd_relatorio_avaliacao_pga_indicador).";";

        $this->db->query($qr_sql);  
	}
	
	public function alterar_avaliacao($cd_indicador, $cd_relatorio_avaliacao_pga, $cd_usuario, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.relatorio_avaliacao_pga_indicador
               SET ds_avaliacao			= '".trim($args['ds_avaliacao'])."',
				   cd_usuario_alteracao = ".intval($cd_usuario).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga)."
			   AND cd_indicador				  = ".intval($cd_indicador).";";

        $this->db->query($qr_sql);  
	}
	
	public function salvar_diretoria($cd_relatorio_avaliacao_pga, $cd_usuario_diretoria, $diretoria, $cd_usuario)
	{
		$cd_relatorio_avaliacao_pga_diretoria = intval($this->db->get_new_id('gestao.relatorio_avaliacao_pga_diretoria', 'cd_relatorio_avaliacao_pga_diretoria'));

		$qr_sql = "
			INSERT INTO gestao.relatorio_avaliacao_pga_diretoria
			     (
			       cd_relatorio_avaliacao_pga_diretoria,
			       cd_relatorio_avaliacao_pga,
			       cd_diretoria,
			       cd_usuario_diretoria,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_relatorio_avaliacao_pga_diretoria).",
			     	".intval($cd_relatorio_avaliacao_pga).",
			     	'".trim($diretoria)."',
			     	".intval($cd_usuario_diretoria).",
			        ".intval($cd_usuario).",
			        ".intval($cd_usuario)."
			     );";	
				 
		$this->db->query($qr_sql);
	}

	public function atualizar_diretoria($cd_relatorio_avaliacao_pga, $cd_usuario_diretoria, $diretoria, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.relatorio_avaliacao_pga_diretoria
               SET cd_diretoria			= '".trim($diretoria)."',
				   cd_usuario_diretoria	= '".intval($cd_usuario_diretoria)."',
				   cd_usuario_alteracao = ".intval($cd_usuario).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga)."
			   AND cd_diretoria				  = '".trim($diretoria)."';";

        $this->db->query($qr_sql);  
	}
	
	public function assinar($cd_relatorio_avaliacao_pga, $cd_relatorio_avaliacao_pga_diretoria, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.relatorio_avaliacao_pga_diretoria
               SET cd_usuario_assinatura = ".intval($cd_usuario).",
                   dt_assinatura         = CURRENT_TIMESTAMP,
				   cd_usuario_alteracao  = ".intval($cd_usuario).",
                   dt_alteracao          = CURRENT_TIMESTAMP
             WHERE cd_relatorio_avaliacao_pga 			= ".intval($cd_relatorio_avaliacao_pga)."
			   AND cd_relatorio_avaliacao_pga_diretoria	= ".intval($cd_relatorio_avaliacao_pga_diretoria).";";

        $this->db->query($qr_sql);  
	}
	
	public function encerrar($cd_relatorio_avaliacao_pga, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.relatorio_avaliacao_pga
               SET cd_usuario_encerramento = ".intval($cd_usuario).",
                   dt_encerramento         = CURRENT_TIMESTAMP
            WHERE cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga).";";

        $this->db->query($qr_sql);  
	}
	
	public function get_relatorio_indicador($cd_indicador, $cd_relatorio_avaliacao_pga)
	{
		$qr_sql = "
			SELECT rapit.cd_relatorio_avaliacao_pga_indicador_tabela,
			       rapi.cd_relatorio_avaliacao_pga_indicador,
			       rap.cd_relatorio_avaliacao_pga,
			       rapi.cd_indicador,
				   pfii.descricao,
				   pfii.fl_criterio,
				   (CASE WHEN pfii.fl_criterio = 'T' THEN 'Quantitativo'
				         WHEN pfii.fl_criterio = 'L' THEN 'Qualitativo'
				   END) AS criterio,
				   pfii.meta,
				   pfii.fl_status,
				   (CASE WHEN pfii.fl_status = 'S' THEN 'ATENDE'
				         WHEN pfii.fl_status = 'N' THEN 'NÃO ATENDE'
						 ELSE 'NÃO INFORMADO'
				   END) AS status,
			       rapi.ds_avaliacao,
			       rapit.parametro
			  FROM gestao.relatorio_avaliacao_pga rap
			  LEFT JOIN gestao.relatorio_avaliacao_pga_indicador rapi
			    ON rap.cd_relatorio_avaliacao_pga = rapi.cd_relatorio_avaliacao_pga
			  LEFT JOIN gestao.relatorio_avaliacao_pga_indicador_tabela rapit
			    ON rapi.cd_relatorio_avaliacao_pga_indicador = rapit.cd_relatorio_avaliacao_pga_indicador
			  LEFT JOIN gestao.plano_fiscal_indicador pfi
			    ON pfi.nr_ano = rap.nr_ano
			   AND (1.0 * rap.nr_trimestre * 3.0) = (1.0 * pfi.nr_mes)
			  LEFT JOIN gestao.plano_fiscal_indicador_item pfii
			    ON pfii.cd_indicador = rapi.cd_indicador
			   AND pfi.cd_plano_fiscal_indicador = pfii.cd_plano_fiscal_indicador
			 WHERE rapi.cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga)."
			   AND rapi.cd_indicador               = ".intval($cd_indicador)."
			   AND rap.dt_exclusao IS NULL;";
	
		return $this->db->query($qr_sql)->row_array();
	}

	public function get_ano()
	{
		$qr_sql = "
			SELECT nr_ano
			  FROM gestao.relatorio_avaliacao_pga
			 ORDER BY nr_ano DESC
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function get_trimestre($nr_ano)
	{
		$qr_sql = "
			SELECT nr_trimestre
			  FROM gestao.relatorio_avaliacao_pga
			 WHERE nr_ano = ".intval($nr_ano)."
			 ORDER BY nr_trimestre DESC
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function get_diretor($cd_relatorio_avaliacao_pga, $cd_relatorio_avaliacao_pga_diretoria)
	{
		$qr_sql = "
			SELECT cd_diretoria,
				   cd_usuario_diretoria,
				   TO_CHAR(dt_assinatura, 'DD/MM/YYYY HH24:MI:SS') AS dt_assinatura,
				   cd_usuario_assinatura
			  FROM gestao.relatorio_avaliacao_pga_diretoria
			 WHERE cd_relatorio_avaliacao_pga_diretoria = ".intval($cd_relatorio_avaliacao_pga_diretoria)."
			   AND cd_relatorio_avaliacao_pga           = ".intval($cd_relatorio_avaliacao_pga).";";

		return $this->db->query($qr_sql)->row_array();
	}
	
	public function get_usuarios_de()
    {
        $qr_sql = "
			SELECT codigo AS value,
				   nome AS text,
				   diretoria
			  FROM projetos.usuarios_controledi
			 WHERE divisao = 'DE' 
			   AND tipo    = 'D'
			 ORDER BY 2;";
        
        return $this->db->query($qr_sql)->result_array();
    }

    public function get_usuario_interventor()
    {
        $qr_sql = "
			SELECT codigo AS value,
				   nome AS text,
				   diretoria
			  FROM projetos.usuarios_controledi
			 WHERE divisao = 'DE' 
			   AND tipo    = 'D'
			   AND codigo  = 371;";
        
        return $this->db->query($qr_sql)->result_array();
    }
	
	public function get_diretores_email($cd_relatorio_avaliacao_pga, $cd_diretoria)
	{
		$qr_sql = "
			SELECT rapd.cd_relatorio_avaliacao_pga_diretoria,
				   funcoes.get_usuario_nome(rapd.cd_usuario_diretoria) AS cd_usuario_diretoria,
				   funcoes.get_usuario(rapd.cd_usuario_diretoria)||'@eletroceee.com.br' AS email,
				   rap.nr_ano || '/0' || rap.nr_trimestre AS nr_trimestre
			  FROM gestao.relatorio_avaliacao_pga rap
			  JOIN gestao.relatorio_avaliacao_pga_diretoria rapd
			    ON rapd.cd_relatorio_avaliacao_pga = rap.cd_relatorio_avaliacao_pga
			   AND rapd.cd_diretoria = '".trim($cd_diretoria)."'
			 WHERE rap.cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga)."
			   AND rapd.dt_assinatura             IS NULL;";
		
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function get_diretores_assinatura($cd_relatorio_avaliacao_pga)
	{
		$qr_sql = "
			SELECT cd_diretoria, 
			       CASE WHEN cd_usuario_diretoria = 371   THEN 'Interventor'
			            WHEN cd_diretoria         = 'PRE' THEN 'Presidente'
						WHEN cd_diretoria         = 'SEG' THEN 'Diretor de Seguridade'
						WHEN cd_diretoria         = 'ADM' THEN 'Diretor Administrativo'
						WHEN cd_diretoria         = 'FIN' THEN 'Diretor Financeiro'
				   END AS diretoria,
				   funcoes.get_usuario_nome(cd_usuario_diretoria) AS cd_usuario_diretoria,
				   TO_CHAR(dt_assinatura, 'DD/MM/YYYY HH24:MI:SS') AS dt_assinatura
			  FROM gestao.relatorio_avaliacao_pga_diretoria
			 WHERE cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga).";";
			   
		return $this->db->query($qr_sql)->result_array();
	}

	public function assinatura_diretores($cd_relatorio_avaliacao_pga)
	{
		$qr_sql = "
			SELECT rap.dt_assinatura AS dt_assinatura, 
			       rap.cd_diretoria AS cd_diretoria,
			       rap.cd_usuario_assinatura AS cd_usuario_assinatura,
			       funcoes.get_usuario_nome(rap.cd_usuario_diretoria) AS nome_usuario_assinatura,
			       funcoes.get_usuario(rap.cd_usuario_assinatura) AS usuario,
			       CASE WHEN cd_usuario_diretoria = 371   THEN 'Interventor'
			            ELSE d.ds_diretoria
				   END AS diretoria
			  FROM gestao.relatorio_avaliacao_pga_diretoria rap
			  JOIN projetos.diretoria d
			    ON rap.cd_diretoria = d.cd_diretoria
			 WHERE rap.cd_relatorio_avaliacao_pga = ".intval($cd_relatorio_avaliacao_pga)."
			   AND (SELECT COUNT(*)
			          FROM gestao.relatorio_avaliacao_pga_diretoria rap2
			         WHERE rap2.cd_relatorio_avaliacao_pga = rap.cd_relatorio_avaliacao_pga
			           AND rap2.dt_assinatura IS NOT NULL ) > 0
			   
			 ORDER BY (CASE WHEN rap.cd_diretoria = 'PRE' 
			 				THEN 0
                            WHEN rap.cd_diretoria = 'FIN' 
                            THEN 1
                            WHEN rap.cd_diretoria = 'SEG' 
                            THEN 2
                            ELSE 4 
                      END)  ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function diretoria_listar($cd_usuario ,$args = array())
	{
		$qr_sql = "
			SELECT rapd.cd_relatorio_avaliacao_pga_diretoria,
				   rap.cd_relatorio_avaliacao_pga,
				   rap.nr_ano,
				   rap.nr_trimestre,
				   d.ds_diretoria AS diretoria,
				   TO_CHAR(rapd.dt_assinatura, 'DD/MM/YYYY HH24:MI:SS') AS dt_assinado,
				   funcoes.get_usuario_nome(rapd.cd_usuario_diretoria) AS nome_usuario_assinatura
		      FROM gestao.relatorio_avaliacao_pga rap
		      JOIN gestao.relatorio_avaliacao_pga_diretoria rapd
		        ON rap.cd_relatorio_avaliacao_pga = rapd.cd_relatorio_avaliacao_pga
		      JOIN projetos.diretoria d
		        ON rapd.cd_diretoria = d.cd_diretoria
			 WHERE rap.dt_exclusao IS NULL
			   AND rap.dt_encerramento IS NOT NULL
				  ".(intval($args['nr_ano']) > 0 ? "AND rap.nr_ano = ".intval($args['nr_ano']) : '')."
				  ".(intval($args['nr_trimestre']) > 0 ? "AND rap.nr_trimestre = ".intval($args['nr_trimestre']) : '')."
				  ".(trim($args['fl_assinado']) == 'S' ? "AND rapd.dt_assinatura IS NOT NULL" : '')."
                  ".(trim($args['fl_assinado']) == 'N' ? "AND rapd.dt_assinatura IS NULL" : '')."
                  AND rapd.cd_usuario_diretoria = '".trim($cd_usuario)."';";
			   
		return $this->db->query($qr_sql)->result_array();
	}
}