<?php
class Reuniao_sistema_gestao_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

    public function get_tipo_reuniao()
	{
		$qr_sql = "
			SELECT cd_reuniao_sistema_gestao_tipo AS value,
			       ds_reuniao_sistema_gestao_tipo AS text
			  FROM gestao.reuniao_sistema_gestao_tipo
			 WHERE dt_exclusao IS NULL
			   AND fl_reuniao = 'S'
			 ORDER BY text;";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_processo()
	{
		$qr_sql = "
			SELECT cd_processo AS value,
				   procedimento AS text
			  FROM projetos.processos
			 WHERE (
			 	   dt_fim_vigencia > CURRENT_DATE 
			       OR
			       dt_fim_vigencia IS NULL
			       )
			 ORDER BY procedimento;";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT r.cd_reuniao_sistema_gestao,
			       TO_CHAR(r.dt_reuniao_sistema_gestao, 'DD/MM/YYYY') AS dt_reuniao_sistema_gestao,
			       TO_CHAR(r.dt_encerramento, 'DD/MM/YYYY  HH24:MI:SS') AS dt_encerramento,
			       t.ds_reuniao_sistema_gestao_tipo,
			       (SELECT TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS')
			       	  FROM gestao.reuniao_sistema_gestao_apresentacao a
			       	 WHERE a.cd_reuniao_sistema_gestao = r.cd_reuniao_sistema_gestao
			       	 ORDER BY a.dt_inclusao DESC
			       	 LIMIT 1) AS dt_apresentacao,
			       funcoes.get_usuario_nome(r.cd_usuario_encerramento) AS usuario_encerramento,
			       r.arquivo,
			       r.arquivo_nome
			  FROM gestao.reuniao_sistema_gestao r
			  JOIN gestao.reuniao_sistema_gestao_tipo t
			    ON t.cd_reuniao_sistema_gestao_tipo = r.cd_reuniao_sistema_gestao_tipo
			 WHERE r.dt_exclusao IS NULL
			   ".(trim($args['cd_reuniao_sistema_gestao_tipo']) != '' ? "AND r.cd_reuniao_sistema_gestao_tipo = ".intval($args['cd_reuniao_sistema_gestao_tipo']) : "")."
			   ".(((trim($args['dt_ini']) != '') AND (trim($args['dt_fim']) != '')) ? " AND DATE_TRUNC('day', r.dt_reuniao_sistema_gestao) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
			 ORDER BY r.dt_reuniao_sistema_gestao DESC;";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_reuniao_sistema_gestao)
	{
		$qr_sql = "
			SELECT r.cd_reuniao_sistema_gestao, 
			       TO_CHAR(r.dt_reuniao_sistema_gestao, 'DD/MM/YYYY') AS dt_reuniao_sistema_gestao,
			       TO_CHAR(r.dt_encerramento, 'DD/MM/YYYY  HH24:MI:SS') AS dt_encerramento,
			       r.cd_reuniao_sistema_gestao_tipo,
			       t.ds_reuniao_sistema_gestao_tipo,
			       r.arquivo,
			       r.arquivo_nome,
			       (SELECT TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS')
			       	  FROM gestao.reuniao_sistema_gestao_apresentacao a
			       	 WHERE a.cd_reuniao_sistema_gestao = r.cd_reuniao_sistema_gestao
			       	 ORDER BY a.dt_inclusao DESC
			       	 LIMIT 1) AS dt_apresentacao,
			       funcoes.get_usuario_nome(r.cd_usuario_encerramento) AS usuario_encerramento
			  FROM gestao.reuniao_sistema_gestao r
			  JOIN gestao.reuniao_sistema_gestao_tipo t
			    ON t.cd_reuniao_sistema_gestao_tipo = r.cd_reuniao_sistema_gestao_tipo
			 WHERE r.cd_reuniao_sistema_gestao = ".intval($cd_reuniao_sistema_gestao).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_reuniao_sistema_gestao = intval($this->db->get_new_id('gestao.reuniao_sistema_gestao', 'cd_reuniao_sistema_gestao'));

		$qr_sql = "
			INSERT INTO gestao.reuniao_sistema_gestao
			     (
			       cd_reuniao_sistema_gestao,
			       dt_reuniao_sistema_gestao,
			       cd_reuniao_sistema_gestao_tipo,
			       arquivo,
			       arquivo_nome,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_reuniao_sistema_gestao).",
			     	".(trim($args['dt_reuniao_sistema_gestao']) != '' ? "TO_DATE('".trim($args['dt_reuniao_sistema_gestao'])."', 'DD/MM/YYYY')" : "DEFAULT").",
			        ".(trim($args['cd_reuniao_sistema_gestao_tipo']) != '' ? intval($args['cd_reuniao_sistema_gestao_tipo']) : "DEFAULT").",
                    ".(trim($args['arquivo']) != "" ? "'".$args['arquivo']."'" : "DEFAULT").",
                    ".(trim($args['arquivo_nome']) != "" ? "'".$args['arquivo_nome']."'" : "DEFAULT" ).",
			        ".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
			     );";

		if(count($args['processo_checked']) > 0)
        {
 			$qr_sql .= "
				INSERT INTO gestao.reuniao_sistema_gestao_processo(cd_reuniao_sistema_gestao, cd_processo, cd_usuario_inclusao, cd_usuario_alteracao)
				SELECT ".intval($cd_reuniao_sistema_gestao).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
				  FROM (VALUES (".implode("),(", $args['processo_checked']).")) x;
				INSERT INTO gestao.reuniao_sistema_gestao_indicador(cd_reuniao_sistema_gestao, cd_indicador, cd_usuario_inclusao, cd_usuario_alteracao)
				SELECT ".intval($cd_reuniao_sistema_gestao).", i.cd_indicador , ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
				  FROM indicador.indicador i
				 WHERE i.dt_exclusao IS NULL
				   AND i.cd_processo IN ((".implode("),(", $args['processo_checked'])."));";
		}
		else
		{
			$qr_sql .= "
				INSERT INTO gestao.reuniao_sistema_gestao_processo(cd_reuniao_sistema_gestao, cd_processo, nr_ordem, cd_usuario_inclusao, cd_usuario_alteracao)
				SELECT ".intval($cd_reuniao_sistema_gestao).", tp.cd_processo, tp.nr_ordem, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])." 
                  FROM gestao.reuniao_sistema_gestao_tipo_processo tp
                 WHERE tp.dt_exclusao IS NULL
                   AND tp.cd_reuniao_sistema_gestao_tipo = ".intval($args['cd_reuniao_sistema_gestao_tipo']).";
                INSERT INTO gestao.reuniao_sistema_gestao_indicador(cd_reuniao_sistema_gestao, cd_indicador, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_reuniao_sistema_gestao).", ti.cd_indicador, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])." 
                  FROM gestao.reuniao_sistema_gestao_tipo_indicador ti
                 WHERE ti.dt_exclusao IS NULL
                   AND ti.cd_reuniao_sistema_gestao_tipo = ".intval($args['cd_reuniao_sistema_gestao_tipo']).";";
		}

		$this->db->query($qr_sql);

		return $cd_reuniao_sistema_gestao;
	}

	public function atualizar($cd_reuniao_sistema_gestao, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.reuniao_sistema_gestao
               SET dt_reuniao_sistema_gestao      = ".(trim($args['dt_reuniao_sistema_gestao']) != '' ? "TO_DATE('".trim($args['dt_reuniao_sistema_gestao'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                   cd_reuniao_sistema_gestao_tipo = ".(trim($args['cd_reuniao_sistema_gestao_tipo']) != '' ? intval($args['cd_reuniao_sistema_gestao_tipo']) : "DEFAULT").",
                   arquivo_nome                   = ".(trim($args['arquivo_nome']) != "" ? "'".$args['arquivo_nome']."'" : "DEFAULT" ).",
                   arquivo                        = ".(trim($args['arquivo']) != "" ? "'".$args['arquivo']."'" : "DEFAULT").",
                   cd_usuario_alteracao           = ".intval($args['cd_usuario']).",
                   dt_alteracao                   = CURRENT_TIMESTAMP
            WHERE cd_reuniao_sistema_gestao = ".intval($cd_reuniao_sistema_gestao).";";

        $this->db->query($qr_sql);  
	}

	public function atualizar_processo($cd_reuniao_sistema_gestao, $args)
	{
		if(count($args['processo_checked']) > 0)
        {
        	$qr_sql = "
        		UPDATE gestao.reuniao_sistema_gestao_processo
				   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
					   dt_exclusao         = CURRENT_TIMESTAMP
				 WHERE cd_reuniao_sistema_gestao = ".intval($cd_reuniao_sistema_gestao)."
				   AND dt_exclusao IS NULL
				   AND cd_processo NOT IN (".implode(",", $args['processo_checked']).");
	   
				INSERT INTO gestao.reuniao_sistema_gestao_processo(cd_reuniao_sistema_gestao, cd_processo, cd_usuario_inclusao, cd_usuario_alteracao)
				SELECT ".intval($cd_reuniao_sistema_gestao).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
				  FROM (VALUES (".implode("),(", $args['processo_checked']).")) x
				 WHERE x.column1 NOT IN (SELECT a.cd_processo
										   FROM gestao.reuniao_sistema_gestao_processo a
										  WHERE a.cd_reuniao_sistema_gestao = ".intval($cd_reuniao_sistema_gestao)."
											AND a.dt_exclusao IS NULL);";
        }
        else
        {
        	$qr_sql = "
				UPDATE gestao.reuniao_sistema_gestao_processo
				   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
					   dt_exclusao         = CURRENT_TIMESTAMP
				 WHERE cd_reuniao_sistema_gestao = ".intval($cd_reuniao_sistema_gestao)."
				   AND dt_exclusao IS NULL;";
        }    

        $this->db->query($qr_sql);
	}

	public function excluir($cd_reuniao_sistema_gestao, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.reuniao_sistema_gestao
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_reuniao_sistema_gestao = ".intval($cd_reuniao_sistema_gestao).";";

        $this->db->query($qr_sql);  
	}

	public function encerrar($cd_reuniao_sistema_gestao, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.reuniao_sistema_gestao
               SET cd_usuario_encerramento = ".intval($cd_usuario).",
                   dt_encerramento         = CURRENT_TIMESTAMP
            WHERE cd_reuniao_sistema_gestao = ".intval($cd_reuniao_sistema_gestao).";";

        $this->db->query($qr_sql);  
	}

	public function atualizar_apresentacao($cd_reuniao_sistema_gestao, $cd_usuario)
	{	
		$qr_sql = "
			INSERT INTO gestao.reuniao_sistema_gestao_apresentacao
			     (
                   cd_reuniao_sistema_gestao, 
                   cd_usuario_inclusao
                 )
            VALUES 
                 (
                 	".intval($cd_reuniao_sistema_gestao).",
                 	".intval($cd_usuario)."
                 );";

        $this->db->query($qr_sql);  
	}

	public function get_processo_checked($cd_reuniao_sistema_gestao, $cd_processo = 0)
	{
		$qr_sql = "
			SELECT rp.cd_processo,
			       rp.cd_reuniao_sistema_gestao_processo,
			       rp.nr_ordem,
			       p.procedimento AS processo
			  FROM gestao.reuniao_sistema_gestao_processo rp
			  JOIN projetos.processos p
			    ON p.cd_processo = rp.cd_processo
			 WHERE rp.cd_reuniao_sistema_gestao  = ".intval($cd_reuniao_sistema_gestao)."
			   ".($cd_processo > 0 ? "AND rp.cd_processo = ".intval($cd_processo) : "")."
			   AND rp.dt_exclusao IS NULL
			 ORDER BY rp.nr_ordem ASC, rp.cd_processo;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function anexo_listar($cd_reuniao_sistema_gestao)
	{
		$qr_sql = "
			SELECT cd_reuniao_sistema_gestao_anexo,
			       arquivo,
			       arquivo_nome,
			       TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(cd_usuario_inclusao) AS nome
			  FROM gestao.reuniao_sistema_gestao_anexo 
			 WHERE cd_reuniao_sistema_gestao = ".intval($cd_reuniao_sistema_gestao)."
			   AND dt_exclusao IS NULL
			 ORDER BY dt_inclusao DESC; ";

		return $this->db->query($qr_sql)->result_array();
	}

	public function anexo_salvar($cd_reuniao_sistema_gestao, $args = array())
	{
		$qr_sql = "
			INSERT INTO gestao.reuniao_sistema_gestao_anexo 
			     (
					cd_reuniao_sistema_gestao,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($cd_reuniao_sistema_gestao).",
					'".trim($args['arquivo'])."',
					'".trim($args['arquivo_nome'])."',
					".intval($args['cd_usuario'])."
				 )";

		 $this->db->query($qr_sql);
	}
	
	public function anexo_excluir($cd_reuniao_sistema_gestao_anexo, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.reuniao_sistema_gestao_anexo 
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_reuniao_sistema_gestao_anexo = ".intval($cd_reuniao_sistema_gestao_anexo).";";

		$this->db->query($qr_sql);
	}

	public function get_indicador_checked($cd_processo, $cd_reuniao_sistema_gestao)
	{
		$qr_sql = "
			SELECT i.cd_indicador,
			       (SELECT lit.cd_indicador_tabela 
				      FROM indicador.listar_indicador_tabela_aberta_de_indicador lit 
				     WHERE lit.cd_indicador = i.cd_indicador 
				     ORDER BY nr_ano_referencia ASC 
				     LIMIT 1) AS cd_indicador_tabela,
				   i.ds_indicador
			  FROM indicador.indicador i
			  JOIN gestao.reuniao_sistema_gestao_indicador ri
			    ON ri.cd_indicador = i.cd_indicador
			 WHERE i.dt_exclusao IS NULL
			   AND ri.cd_reuniao_sistema_gestao = ".$cd_reuniao_sistema_gestao."
			   AND i.cd_processo                = ".intval($cd_processo)."
			   AND ri.dt_exclusao IS NULL
			 ORDER BY COALESCE(i.nr_ordem,0), 
			       i.ds_indicador;";

        return $this->db->query($qr_sql)->result_array();
	}

	public function get_indicador($cd_processo)
	{
		$qr_sql = "
			SELECT i.cd_indicador AS value,
			       i.ds_indicador As text
			  FROM indicador.indicador i
			 WHERE i.dt_exclusao IS NULL
			   AND i.cd_processo = ".intval($cd_processo)."
			 ORDER BY COALESCE(i.nr_ordem,0), 
			       i.ds_indicador;";

        return $this->db->query($qr_sql)->result_array();
	}

	public function atualizar_indicador($cd_reuniao_sistema_gestao, $args)
	{
		if(count($args['indicador_checked']) > 0)
        {
        	$qr_sql = "
        		UPDATE gestao.reuniao_sistema_gestao_indicador
				   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
					   dt_exclusao         = CURRENT_TIMESTAMP
				 WHERE cd_reuniao_sistema_gestao = ".intval($cd_reuniao_sistema_gestao)."
				   AND dt_exclusao IS NULL
				   AND cd_indicador NOT IN (".implode(",", $args['indicador_checked']).");	

				INSERT INTO gestao.reuniao_sistema_gestao_indicador(cd_reuniao_sistema_gestao, cd_indicador, cd_usuario_inclusao, cd_usuario_alteracao)
				SELECT ".intval($cd_reuniao_sistema_gestao).", i.cd_indicador , ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
				  FROM indicador.indicador i
				 WHERE i.dt_exclusao IS NULL
				   AND i.cd_indicador NOT IN (SELECT i2.cd_indicador 
				                                FROM gestao.reuniao_sistema_gestao_indicador i2
				                               WHERE i2.cd_reuniao_sistema_gestao = ".intval($cd_reuniao_sistema_gestao)."
				                                 AND i2.dt_exclusao IS NULL)
				   AND i.cd_indicador IN (".implode(",", $args['indicador_checked']).");";
        }
        else
        {
        	$qr_sql = "
				UPDATE gestao.reuniao_sistema_gestao_indicador
				   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
					   dt_exclusao         = CURRENT_TIMESTAMP
				 WHERE cd_reuniao_sistema_gestao = ".intval($cd_reuniao_sistema_gestao)."
				   AND dt_exclusao IS NULL;";
        }    

        $this->db->query($qr_sql);
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

	public function get_reuniao_sistema_gestao_indicador_tabela($cd_indicador, $cd_reuniao_sistema_gestao_processo)
	{
		$qr_sql = "
			SELECT cd_reuniao_sistema_gestao_indicador_tabela
			  FROM gestao.reuniao_sistema_gestao_indicador_tabela
			 WHERE cd_reuniao_sistema_gestao_processo = ".intval($cd_reuniao_sistema_gestao_processo)."
			   AND cd_indicador                       = ".intval($cd_indicador)."
			   AND dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->row_array();	
	}

	public function salvar_indicador($cd_indicador, $cd_reuniao_sistema_gestao_processo, $cd_usuario, $indicador_tabela)
	{
		$qr_sql = "
			INSERT INTO gestao.reuniao_sistema_gestao_indicador_tabela
			     (
                   cd_reuniao_sistema_gestao_processo, 
                   cd_indicador, 
                   parametro, 
                   cd_usuario_inclusao, 
                   cd_usuario_alteracao
                 )
            VALUES 
                 (
                 	".intval($cd_reuniao_sistema_gestao_processo).",
                 	".intval($cd_indicador).",
                 	".str_escape($indicador_tabela).",
                 	".intval($cd_usuario).",
                 	".intval($cd_usuario)."
                 );";

		$this->db->query($qr_sql);
	}

	public function salvar_cadastro_ordem($cd_reuniao_sistema_gestao, $cd_usuario, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.reuniao_sistema_gestao_processo
			   SET nr_ordem     		= ".intval($args['nr_ordem']).",
			       cd_usuario_alteracao = ".intval($cd_usuario).", 
			       dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_reuniao_sistema_gestao = ".intval($cd_reuniao_sistema_gestao)."
			   AND cd_processo				 = ".intval($args['cd_processo']).";";

		$this->db->query($qr_sql);
	}

	public function atualizar_indicador_tabela($cd_reuniao_sistema_gestao_indicador_tabela, $cd_usuario, $indicador_tabela)
	{
		$qr_sql = "
			UPDATE gestao.reuniao_sistema_gestao_indicador_tabela
			   SET parametro            = ".str_escape($indicador_tabela).",
			       cd_usuario_alteracao = ".intval($cd_usuario).", 
			       dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_reuniao_sistema_gestao_indicador_tabela = ".intval($cd_reuniao_sistema_gestao_indicador_tabela).";";

		$this->db->query($qr_sql);
	}

	public function get_processo_indicador($cd_reuniao_sistema_gestao_processo, $cd_reuniao_sistema_gestao)
	{
		$qr_sql = "
			SELECT gi.cd_reuniao_sistema_gestao_indicador_tabela,
			       gi.cd_reuniao_sistema_gestao_processo,
			       gi.cd_indicador,
			       gi.parametro
			  FROM gestao.reuniao_sistema_gestao_indicador_tabela gi
			  JOIN indicador.indicador i
			    ON i.cd_indicador = gi.cd_indicador
			  JOIN gestao.reuniao_sistema_gestao_indicador ri
			    ON ri.cd_indicador = i.cd_indicador 
			 WHERE ri.cd_reuniao_sistema_gestao          = ".intval($cd_reuniao_sistema_gestao)."
			   AND gi.cd_reuniao_sistema_gestao_processo = ".intval($cd_reuniao_sistema_gestao_processo)."
			   AND ri.dt_exclusao IS NULL
			   AND i.dt_exclusao IS NULL
			   AND gi.dt_exclusao IS NULL
			 ORDER BY COALESCE(i.nr_ordem,0), 
			       i.ds_indicador;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_gestao_indicador($cd_indicador, $cd_reuniao_sistema_gestao_processo)
	{
		$qr_sql = "
			SELECT cd_reuniao_sistema_gestao_indicador_tabela,
			       cd_reuniao_sistema_gestao_processo,
			       cd_indicador,
			       parametro
			  FROM gestao.reuniao_sistema_gestao_indicador_tabela
			 WHERE cd_reuniao_sistema_gestao_processo = ".intval($cd_reuniao_sistema_gestao_processo)."
			   AND cd_indicador                       = ".intval($cd_indicador)."
			   AND dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->row_array();
	}
  
  	/*
	public function get_processo_igp($cd_reuniao_sistema_gestao_tipo)
	{
		$qr_sql = "
			SELECT p.cd_processo,
				   p.cd_reuniao_sistema_gestao
			  FROM gestao.reuniao_sistema_gestao_processo p 
			 WHERE p.dt_exclusao IS NULL
			   AND p.cd_reuniao_sistema_gestao = (SELECT g.cd_reuniao_sistema_gestao 
													FROM gestao.reuniao_sistema_gestao g 
												   WHERE g.cd_reuniao_sistema_gestao_tipo = ".intval($cd_reuniao_sistema_gestao_tipo)." 
												     AND g.dt_exclusao IS NULL 
												ORDER BY g.dt_inclusao DESC 
												   LIMIT 1);";
												   
		return $this->db->query($qr_sql)->result_array();
	}
	
	*/
	public function get_indicador_igp()
	{	
		$qr_sql = "
			SELECT cd_indicador, 
			       cd_processo 
			  FROM indicador.indicador i 
			 WHERE i.dt_exclusao IS NULL 
			   AND i.fl_igp  = 'S';";

		/*
		$qr_sql = "
				SELECT p.cd_indicador
				  FROM gestao.reuniao_sistema_gestao_indicador p 
				 WHERE p.dt_exclusao IS NULL 
				   AND p.cd_reuniao_sistema_gestao = ".intval($cd_reuniao_sistema_gestao)."
		      ORDER BY p.cd_indicador;";

      	*/
			
		return $this->db->query($qr_sql)->result_array();
	}

	public function get_processo_tipo($cd_reuniao_sistema_gestao_tipo)
	{
		$qr_sql = "";

		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_reuniao_gestao($args = array())
	{
		$qr_sql = "
			SELECT r.cd_reuniao_sistema_gestao,
			       TO_CHAR(r.dt_reuniao_sistema_gestao, 'DD/MM/YYYY') AS dt_reuniao_sistema_gestao,
			       TO_CHAR(r.dt_encerramento, 'DD/MM/YYYY  HH24:MI:SS') AS dt_encerramento,
			       t.ds_reuniao_sistema_gestao_tipo,
			       (SELECT TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS')
			       	  FROM gestao.reuniao_sistema_gestao_apresentacao a
			       	 WHERE a.cd_reuniao_sistema_gestao = r.cd_reuniao_sistema_gestao
			       	 ORDER BY a.dt_inclusao DESC
			       	 LIMIT 1) AS dt_apresentacao,
			       funcoes.get_usuario_nome(r.cd_usuario_encerramento) AS usuario_encerramento,
			       r.arquivo,
			       r.arquivo_nome,
			       (select rsga.arquivo 
			       from gestao.reuniao_sistema_gestao_anexo rsga
			   where rsga.cd_reuniao_sistema_gestao = r.cd_reuniao_sistema_gestao limit 1) AS anexo
			  FROM gestao.reuniao_sistema_gestao r
			  JOIN gestao.reuniao_sistema_gestao_tipo t
			    ON t.cd_reuniao_sistema_gestao_tipo = r.cd_reuniao_sistema_gestao_tipo
			    WHERE r.dt_encerramento IS NOT NULL
			   AND r.dt_exclusao IS NULL
			   
			   ".(trim($args['cd_reuniao_sistema_gestao_tipo']) != '' ? "AND r.cd_reuniao_sistema_gestao_tipo = ".intval($args['cd_reuniao_sistema_gestao_tipo']) : "")."
			   ".(((trim($args['dt_ini']) != '') AND (trim($args['dt_fim']) != '')) ? " AND DATE_TRUNC('day', r.dt_reuniao_sistema_gestao) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
			 ORDER BY r.dt_reuniao_sistema_gestao DESC;";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_reuniao_gestao_anexo($cd_reuniao_sistema_gestao)
	{
		$qr_sql = "
			SELECT r.cd_reuniao_sistema_gestao,
				   rsga.cd_reuniao_sistema_gestao,
				   rsga.cd_reuniao_sistema_gestao_anexo,
			       rsga.arquivo_nome As nome_anexo,
			       rsga.arquivo AS anexo
			   FROM gestao.reuniao_sistema_gestao r
			  JOIN gestao.reuniao_sistema_gestao_anexo rsga
			    ON rsga.cd_reuniao_sistema_gestao = r.cd_reuniao_sistema_gestao
			 WHERE rsga.cd_reuniao_sistema_gestao = ".intval($cd_reuniao_sistema_gestao)."
			   AND r.dt_exclusao IS NULL
			   AND rsga.dt_exclusao IS NULL";

		return $this->db->query($qr_sql)->result_array();
	}


}