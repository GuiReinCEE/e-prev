<?php
class Regulamento_alteracao_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

    public function listar($cd_regulamento_tipo, $args = array())
	{
        $qr_sql = "
	        SELECT ra.cd_regulamento_alteracao,
	               TO_CHAR(ra.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao,
                   rt.ds_regulamento_tipo,
                   rt.ds_cnpb,
                   TO_CHAR(ra.dt_alteracao_finalizada, 'DD/MM/YYYY HH24:MI') AS dt_alteracao_finalizada,
                   TO_CHAR(ra.dt_inicio_quadro_comparativo, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio_quadro_comparativo,
                   TO_CHAR(ra.dt_fim_quadro_comparativo, 'DD/MM/YYYY HH24:MI:SS') AS dt_fim_quadro_comparativo,
                   TO_CHAR(ra.dt_envio_previc, 'DD/MM/YYYY') AS dt_envio_previc,
                   ra.ds_aprovacao_previc,
                   ra.arquivo,
                   TO_CHAR(ra.dt_aprovacao_previc, 'DD/MM/YYYY') AS dt_aprovacao_previc
	          FROM gestao.regulamento_alteracao ra
	          JOIN gestao.regulamento_tipo rt 
	            ON ra.cd_regulamento_tipo = rt.cd_regulamento_tipo
	         WHERE ra.dt_exclusao IS NULL
	           AND ra.cd_regulamento_tipo = ".intval($cd_regulamento_tipo)." 
	         ORDER BY ra.dt_inclusao DESC
	         LIMIT 1;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function listar_versao($cd_regulamento_tipo, $cd_regulamento_alteracao)
	{
        $qr_sql = "
            SELECT ra.cd_regulamento_alteracao,
                   TO_CHAR(ra.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao,
                   rt.ds_regulamento_tipo,
                   rt.ds_cnpb,
                   TO_CHAR(ra.dt_alteracao_finalizada, 'DD/MM/YYYY HH24:MI') AS dt_alteracao_finalizada,
                   TO_CHAR(ra.dt_inicio_quadro_comparativo, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio_quadro_comparativo,
                   TO_CHAR(ra.dt_fim_quadro_comparativo, 'DD/MM/YYYY HH24:MI:SS') AS dt_fim_quadro_comparativo,
                   TO_CHAR(ra.dt_envio_previc, 'DD/MM/YYYY') AS dt_envio_previc,
                   ra.ds_aprovacao_previc,
                   ra.arquivo,
                   TO_CHAR(ra.dt_aprovacao_previc, 'DD/MM/YYYY') AS dt_aprovacao_previc
              FROM gestao.regulamento_alteracao ra
              JOIN gestao.regulamento_tipo rt 
                ON rt.cd_regulamento_tipo = ra.cd_regulamento_tipo
             WHERE ra.dt_exclusao              IS NULL
               AND ra.cd_regulamento_tipo      = ".intval($cd_regulamento_tipo)."
               AND ra.cd_regulamento_alteracao != ".intval($cd_regulamento_alteracao).";";
             
        return $this->db->query($qr_sql)->result_array();
    }
    
    public function lista_regulamento_tipo()
	{
		$qr_sql = "
			SELECT cd_regulamento_tipo,
                   ds_regulamento_tipo 
			  FROM gestao.regulamento_tipo 
			 WHERE dt_exclusao                 IS NULL
			   AND cd_regulamento_tipo_vigente IS NULL
             ORDER BY ds_regulamento_tipo;";

		return $this->db->query($qr_sql)->result_array();
    }

	public function carrega($cd_regulamento_alteracao)
	{
		$qr_sql = "
			SELECT ra.cd_regulamento_alteracao,
                   ra.cd_regulamento_alteracao_referencia,
			       ra.cd_regulamento_tipo,
			       rt.ds_regulamento_tipo,
			       rt.ds_cnpb,
			       rt.cd_plano,
			       ra.arquivo,
			       ra.arquivo_nome,
                   ra.ds_aprovacao_previc,
                   TO_CHAR(ra.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   TO_CHAR(ra.dt_aprovacao_previc, 'DD/MM/YYYY') AS dt_aprovacao_previc,
                   TO_CHAR(ra.dt_envio_previc, 'DD/MM/YYYY') AS dt_envio_previc,
                   TO_CHAR(ra.dt_inicio_quadro_comparativo, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio_quadro_comparativo,
                   TO_CHAR(ra.dt_fim_quadro_comparativo, 'DD/MM/YYYY HH24:MI:SS') AS dt_fim_quadro_comparativo,
                   TO_CHAR(ra.dt_alteracao_finalizada, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao_finalizada,
                   (SELECT COUNT(*)
		              FROM gestao.regulamento_alteracao ra2
		             WHERE ra2.cd_regulamento_alteracao_referencia = ra.cd_regulamento_alteracao
		               AND ra2.dt_exclusao                         IS NULL) AS fl_nova_versao,
                   (CASE WHEN ra.dt_aprovacao_previc IS NOT NULL 
                         THEN 'Aprovado pela ' || ds_aprovacao_previc || ' ' || TO_CHAR(ra.dt_aprovacao_previc, 'DD/MM/YYYY')
                         ELSE ''
                   END) AS ds_rodape
			  FROM gestao.regulamento_alteracao ra
			  JOIN gestao.regulamento_tipo rt
			    ON rt.cd_regulamento_tipo = ra.cd_regulamento_tipo
			 WHERE ra.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao).";";

		return $this->db->query($qr_sql)->row_array();
    }

    public function carrega_glossario($cd_regulamento_alteracao, $cd_regulamento_alteracao_glossario)
    {
        $qr_sql = "
            SELECT cd_regulamento_alteracao_glossario,
                   nr_ordem, 
                   ds_regulamento_alteracao_glossario
              FROM gestao.regulamento_alteracao_glossario
             WHERE dt_exclusao                        IS NULL
               AND dt_removido                        IS NULL
               AND cd_regulamento_alteracao           = ".intval($cd_regulamento_alteracao)."
               AND cd_regulamento_alteracao_glossario = ".intval($cd_regulamento_alteracao_glossario).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function carrega_glossario_referencia($cd_regulamento_alteracao_glossario_referencia)
    {
        $qr_sql = "
            SELECT cd_regulamento_alteracao_glossario,
                   nr_ordem, 
                   ds_regulamento_alteracao_glossario
              FROM gestao.regulamento_alteracao_glossario
             WHERE dt_exclusao                        IS NULL
               AND dt_removido                        IS NULL
               AND cd_regulamento_alteracao_glossario = ".intval($cd_regulamento_alteracao_glossario_referencia).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_glossario_referencia($cd_regulamento_alteracao)
    {
        $qr_sql = "
            SELECT COUNT(*) AS qt_glossario
              FROM gestao.regulamento_alteracao_glossario
             WHERE dt_exclusao              IS NULL
               AND dt_removido              IS NULL
               AND cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_next_ordem_glossario($cd_regulamento_alteracao)
    {
        $qr_sql = "
            SELECT (nr_ordem + 1) nr_ordem
              FROM gestao.regulamento_alteracao_glossario
             WHERE dt_exclusao                        IS NULL
               AND dt_removido                        IS NULL
               AND cd_regulamento_alteracao           = ".intval($cd_regulamento_alteracao)."
             ORDER BY nr_ordem DESC
             LIMIT 1;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function listar_glossario($cd_regulamento_alteracao, $fl_removido = 'N')
    {
        $qr_sql = "
            SELECT rag.cd_regulamento_alteracao_glossario,
                   rag.nr_ordem, 
                   rag.ds_regulamento_alteracao_glossario,
                   rag.dt_removido,
                   rag.cd_regulamento_alteracao_glossario_referencia,
                   (CASE WHEN (SELECT TRIM(funcoes.strip_tags(rag2.ds_regulamento_alteracao_glossario))
                                 FROM gestao.regulamento_alteracao_glossario rag2
                                WHERE rag2.cd_regulamento_alteracao_glossario = rag.cd_regulamento_alteracao_glossario_referencia) !=  TRIM(funcoes.strip_tags(rag.ds_regulamento_alteracao_glossario))

                         THEN 'S'
                         WHEN rag.cd_regulamento_alteracao_glossario_referencia IS NULL
                         THEN 'S'
                         ELSE 'N'
                   END) AS fl_alteracao_texto
              FROM gestao.regulamento_alteracao_glossario rag
             WHERE rag.dt_exclusao                        IS NULL
               ".(trim($fl_removido) == 'N' ? "AND rag.dt_removido IS NULL" : "")." 
               AND rag.cd_regulamento_alteracao           = ".intval($cd_regulamento_alteracao)."
             ORDER BY rag.nr_ordem ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_glossario($cd_regulamento_alteracao, $args = array())
    {
        $cd_regulamento_alteracao_glossario = intval($this->db->get_new_id('gestao.regulamento_alteracao_glossario', 'cd_regulamento_alteracao_glossario'));

        $qr_sql = "
            INSERT INTO gestao.regulamento_alteracao_glossario
                 (
                    cd_regulamento_alteracao_glossario,
                    cd_regulamento_alteracao, 
                    cd_regulamento_alteracao_glossario_referencia,
                    nr_ordem, 
                    ds_regulamento_alteracao_glossario,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".(trim($cd_regulamento_alteracao_glossario) != '' ? intval($cd_regulamento_alteracao_glossario) : "DEFAULT").",
                    ".(trim($cd_regulamento_alteracao) != '' ? intval($cd_regulamento_alteracao) : "DEFAULT").",
                    ".(trim($args['cd_regulamento_alteracao_glossario_referencia']) != '' ? intval($args['cd_regulamento_alteracao_glossario_referencia']) : "DEFAULT").",
                    ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                    ".(trim($args['ds_regulamento_alteracao_glossario']) != '' ? str_escape($args['ds_regulamento_alteracao_glossario']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",                   
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);

        return $cd_regulamento_alteracao_glossario;
    }

    public function atualizar_glossario($cd_regulamento_alteracao_glossario, $args = array())
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_glossario
               SET nr_ordem                           = ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                   ds_regulamento_alteracao_glossario = ".(trim($args['ds_regulamento_alteracao_glossario']) != '' ? str_escape($args['ds_regulamento_alteracao_glossario']) : "DEFAULT").",
                   cd_usuario_alteracao               = ".intval($args['cd_usuario']).",
                   dt_alteracao                       = CURRENT_TIMESTAMP
             WHERE cd_regulamento_alteracao_glossario = ".intval($cd_regulamento_alteracao_glossario).";";

        $this->db->query($qr_sql);
    }

    public function remover_glossario($cd_regulamento_alteracao_glossario, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_glossario
               SET cd_usuario_removido = ".intval($cd_usuario).",
                   dt_removido         = CURRENT_TIMESTAMP
             WHERE cd_regulamento_alteracao_glossario = ".intval($cd_regulamento_alteracao_glossario).";";

        $this->db->query($qr_sql);
    }

    public function verifica_ordem_glossario($cd_regulamento_alteracao, $nr_ordem, $ds_operador = '=')
    {
        $qr_sql = "
            SELECT (CASE WHEN COUNT(*) > 0 THEN 'S' ELSE 'N' END) AS fl_ordem
              FROM gestao.regulamento_alteracao_glossario
             WHERE dt_exclusao              IS NULL
               AND cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao)."
               AND nr_ordem                 ".trim($ds_operador)." ".intval($nr_ordem).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function atualizar_renumeracao_glossario($cd_regulamento_alteracao, $cd_regulamento_alteracao_glossario, $args = array(), $ds_operador = '+')
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_glossario AS t
               SET nr_ordem             = x.nr_ordem,
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
              FROM (SELECT cd_regulamento_alteracao_glossario,
                           (nr_ordem ".trim($ds_operador)." 1) AS nr_ordem
                      FROM gestao.regulamento_alteracao_glossario
                     WHERE cd_regulamento_alteracao           = ".intval($cd_regulamento_alteracao)."
                       AND nr_ordem                           >= ".intval($args['nr_ordem'])."
                       AND cd_regulamento_alteracao_glossario != ".intval($cd_regulamento_alteracao_glossario)."
                       AND dt_removido                        IS NULL
                       AND dt_exclusao                        IS NULL
                     ORDER BY nr_ordem) x
             WHERE t.cd_regulamento_alteracao_glossario = x.cd_regulamento_alteracao_glossario;";

        $this->db->query($qr_sql); 
    }

    public function salvar($args = array())
    {
    	$cd_regulamento_alteracao = intval($this->db->get_new_id('gestao.regulamento_alteracao', 'cd_regulamento_alteracao'));

		$qr_sql = "
			INSERT INTO gestao.regulamento_alteracao
			     (
                    cd_regulamento_alteracao, 
                    cd_regulamento_alteracao_referencia, 
                    cd_regulamento_tipo, 
                    ds_aprovacao_previc, 
                    dt_aprovacao_previc,
                   	cd_usuario_inclusao, 
                   	cd_usuario_alteracao
                 )
            VALUES 
                 (
                 	".(trim($cd_regulamento_alteracao) != '' ? intval($cd_regulamento_alteracao) : "DEFAULT").",	
                 	".(trim($args['cd_regulamento_alteracao_referencia']) != '' ? intval($args['cd_regulamento_alteracao_referencia']) : "DEFAULT").",
                 	".(trim($args['cd_regulamento_tipo']) != '' ? intval($args['cd_regulamento_tipo']) : "DEFAULT").",
                    ".(trim($args['ds_aprovacao_previc']) != '' ? str_escape($args['ds_aprovacao_previc']) : "DEFAULT").",
                 	".(trim($args['dt_aprovacao_previc']) != '' ? "TO_DATE('".$args['dt_aprovacao_previc']."', 'DD/MM/YYYY')" : "DEFAULT").",
				    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])." 
                 );";

        $this->db->query($qr_sql);

        return $cd_regulamento_alteracao;
    }

	public function atualizar($cd_regulamento_alteracao, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.regulamento_alteracao
               SET dt_envio_previc      = ".(trim($args['dt_envio_previc']) != '' ? "TO_DATE('".$args['dt_envio_previc']."', 'DD/MM/YYYY')" : "DEFAULT").",
                   ds_aprovacao_previc  = ".(trim($args['ds_aprovacao_previc']) != '' ? str_escape($args['ds_aprovacao_previc']) : "DEFAULT").",
                   dt_aprovacao_previc  = ".(trim($args['dt_aprovacao_previc']) != '' ? "TO_DATE('".$args['dt_aprovacao_previc']."', 'DD/MM/YYYY')" : "DEFAULT").",
			       arquivo              = ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
			       arquivo_nome         = ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",                  
			       cd_usuario_alteracao = ".intval($args['cd_usuario']).",
			       dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao).";";

		$this->db->query($qr_sql);
    }
    
    public function lista_revisao($cd_regulamento_tipo)
    {
        $qr_sql = "
            SELECT rr.cd_regulamento_revisao,
                   rr.ds_regulamento_revisao,
                   rr.ds_descricao,
                   rr.nr_ordem,
                   rr.cd_etapa_automatica,
                   (CASE WHEN rr.cd_regulamento_revisao_pai IS NULL 
                         THEN 0
                         ELSE 1
                   END) AS fl_pai
              FROM gestao.regulamento_revisao rr
             WHERE rr.dt_exclusao IS NULL
               AND ".intval($cd_regulamento_tipo)." NOT IN (SELECT rt.cd_regulamento_tipo 
                                                              FROM gestao.regulamento_revisao_regulamento_tipo rt 
                                                             WHERE rt.dt_exclusao             IS NULL
                                                               AND rt.cd_regulamento_revisao = rr.cd_regulamento_revisao)
             ORDER BY fl_pai ASC, 
                   rr.nr_ordem ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_alteracao_revisao($cd_regulamento_alteracao, $args = array())
    {
        $qr_sql = "
            INSERT INTO gestao.regulamento_alteracao_revisao
                 (
                    cd_regulamento_alteracao, 
                    cd_regulamento_revisao, 
                    ds_regulamento_alteracao_revisao, 
                    ds_descricao, 
                    nr_ordem, 
                    cd_etapa_automatica, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".intval($cd_regulamento_alteracao).",
                    ".(trim($args['cd_regulamento_revisao']) != '' ? intval($args['cd_regulamento_revisao']) : "DEFAULT").",
                    ".(trim($args['ds_regulamento_alteracao_revisao']) != '' ? str_escape($args['ds_regulamento_alteracao_revisao']) : "DEFAULT").",
                    ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
                    ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                    ".(trim($args['cd_etapa_automatica']) != '' ? "'".trim($args['cd_etapa_automatica'])."'" : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])." 
                 );";

        $this->db->query($qr_sql);
    }

    public function atualizar_revisao_ref($cd_regulamento_alteracao)
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_revisao t
               SET cd_regulamento_alteracao_revisao_pai = x.cd_regulamento_alteracao_revisao_pai
              FROM (
                    SELECT rr.cd_regulamento_revisao, 
                           (SELECT rar.cd_regulamento_alteracao_revisao
                              FROM gestao.regulamento_alteracao_revisao rar
                             WHERE rar.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao)."
                               AND rar.cd_regulamento_revisao   = rr.cd_regulamento_revisao_pai) AS cd_regulamento_alteracao_revisao_pai
                      FROM gestao.regulamento_revisao rr
                     WHERE rr.cd_regulamento_revisao_pai IS NOT NULL
                ) x
             WHERE t.cd_regulamento_revisao   = x.cd_regulamento_revisao
               AND t.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao).";";

        $this->db->query($qr_sql);
    }


    public function finalizar_alteracoes($cd_regulamento_alteracao, $cd_usuario_alteracao_finalizada)
	{
		$qr_sql = "
			UPDATE gestao.regulamento_alteracao
			   SET cd_usuario_alteracao_finalizada = ".intval($cd_usuario_alteracao_finalizada).",
               dt_alteracao_finalizada             = CURRENT_TIMESTAMP
			 WHERE cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao).";";

		$this->db->query($qr_sql);
	}

    public function get_gerencia()
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_gerencias_vigente('DIV');";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_atividade_unidade_basica($cd_regulamento_alteracao_unidade_basica)
    {
        $qr_sql = "
            SELECT cd_regulamento_alteracao_atividade,
                   cd_regulamento_alteracao_unidade_basica,
                   TO_CHAR(dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio
              FROM gestao.regulamento_alteracao_atividade
             WHERE cd_regulamento_alteracao_unidade_basica = ".intval($cd_regulamento_alteracao_unidade_basica).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function listar_gerencia_atividade_unidade_basica($cd_regulamento_alteracao_atividade)
    {
        $qr_sql = "
            SELECT cd_regulamento_alteracao_atividade,
                   cd_gerencia
              FROM gestao.regulamento_alteracao_atividade_gerencia
             WHERE dt_exclusao IS NULL 
               AND cd_regulamento_alteracao_atividade = ".intval($cd_regulamento_alteracao_atividade).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_responsavel($cd_gerencia)
    {
        $qr_sql = "
            SELECT funcoes.get_usuario(cd_usuario)||'@eletroceee.com.br' AS ds_usuario
              FROM gestao.regulamento_alteracao_responsavel
             WHERE dt_exclusao IS NULL 
               AND cd_gerencia = '".trim($cd_gerencia)."';";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_atividade_unidade_basica($args = array())
    {
        $cd_regulamento_alteracao_atividade = intval($this->db->get_new_id('gestao.regulamento_alteracao_atividade', 'cd_regulamento_alteracao_atividade'));

        $qr_sql = "
            INSERT INTO gestao.regulamento_alteracao_atividade
                (
                    cd_regulamento_alteracao_atividade,
                    cd_regulamento_alteracao_unidade_basica,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
            VALUES
                (
                    ".intval($cd_regulamento_alteracao_atividade).",
                    ".(intval($args['cd_regulamento_alteracao_unidade_basica']) > 0 ? intval($args['cd_regulamento_alteracao_unidade_basica']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
                );";

        $this->db->query($qr_sql);

        return $cd_regulamento_alteracao_atividade;
    }

    public function salvar_gerencia_atividade($cd_regulamento_alteracao_atividade, $args = array())
    {
        if(count($args['cd_gerencia']) > 0)
        {
            $qr_sql = "
                UPDATE gestao.regulamento_alteracao_atividade_gerencia
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_regulamento_alteracao_atividade = '".trim($cd_regulamento_alteracao_atividade)."'
                   AND dt_exclusao IS NULL
                   AND cd_gerencia NOT IN ('".implode("','", $args['cd_gerencia'])."');
                    
                INSERT INTO gestao.regulamento_alteracao_atividade_gerencia
                (
                       cd_regulamento_alteracao_atividade,
                       cd_gerencia, 
                       cd_usuario_inclusao,
                       cd_usuario_alteracao
                )
                SELECT ".intval($cd_regulamento_alteracao_atividade).", 
                       x.column1, 
                       ".intval($args['cd_usuario']).", 
                       ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['cd_gerencia'])."')) x
                 WHERE x.column1 NOT IN (SELECT a.cd_gerencia
                                           FROM gestao.regulamento_alteracao_atividade_gerencia a
                                          WHERE a.cd_regulamento_alteracao_atividade = '".trim($cd_regulamento_alteracao_atividade)."'
                                            AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql = "
                UPDATE gestao.regulamento_alteracao_atividade_gerencia
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_regulamento_alteracao_atividade = '".trim($cd_regulamento_alteracao_atividade)."'
                   AND dt_exclusao   IS NULL;";
        }

        $this->db->query($qr_sql);
    }

    public function encaminhar_atividade($cd_regulamento_alteracao_atividade, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_atividade
               SET cd_usuario_envio = ".intval($cd_usuario).",
                   dt_envio         = CURRENT_TIMESTAMP
             WHERE cd_regulamento_alteracao_atividade = ".intval($cd_regulamento_alteracao_atividade).";";

        $this->db->query($qr_sql);
    }

    public function listar_atividades_nao_encaminhadas($cd_regulamento_alteracao)
    {
    	$qr_sql = "
			SELECT raa.cd_regulamento_alteracao_atividade
			  FROM gestao.regulamento_alteracao_atividade raa
			  JOIN gestao.regulamento_alteracao_quadro_comparativo raqc
			    ON raqc.cd_regulamento_alteracao_unidade_basica = raa.cd_regulamento_alteracao_unidade_basica
			 WHERE raa.dt_exclusao IS NULL
			   AND raqc.dt_exclusao IS NULL
			   AND raa.dt_envio IS NULL
			   AND raqc.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao).";";

    	return $this->db->query($qr_sql)->result_array();
    }

	public function get_estrutura_tipo($tp_regulamento_alteracao_estrutura_tipo = 'E')
	{
		$qr_sql = "
			SELECT cd_regulamento_alteracao_estrutura_tipo AS value,
			       ds_regulamento_alteracao_estrutura_tipo AS text
			  FROM gestao.regulamento_alteracao_estrutura_tipo
			 WHERE tp_regulamento_alteracao_estrutura_tipo = '".trim($tp_regulamento_alteracao_estrutura_tipo)."'
			 ORDER BY nr_ordem;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function verifica_ordem_estrutura($cd_regulamento_alteracao, $cd_regulamento_alteracao_estrutura_tipo, $nr_ordem, $cd_regulamento_alteracao_estrutura_pai = 0, $ds_operador = '=')
	{
		$qr_sql = "
			SELECT (CASE WHEN COUNT(*) > 0 THEN 'S' ELSE 'N' END) AS fl_ordem
			  FROM gestao.regulamento_alteracao_estrutura
			 WHERE dt_exclusao IS NULL
               AND dt_removido IS NULL
			   AND cd_regulamento_alteracao                = ".intval($cd_regulamento_alteracao)."
			   AND cd_regulamento_alteracao_estrutura_tipo = ".intval($cd_regulamento_alteracao_estrutura_tipo)."
			   AND nr_ordem                                ".trim($ds_operador)." ".intval($nr_ordem)."
               ".(intval($cd_regulamento_alteracao_estrutura_pai) > 0 ? "AND cd_regulamento_alteracao_estrutura_pai = ".intval($cd_regulamento_alteracao_estrutura_pai) : "").";";

		return $this->db->query($qr_sql)->row_array();
    }	
    
    public function verifica_ordem_unidade_basica($cd_regulamento_alteracao, $cd_regulamento_alteracao_estrutura_tipo, $nr_ordem, $ds_operador = '=')
	{
		$qr_sql = "               
            SELECT (CASE WHEN COUNT(*) > 0 THEN 'S' ELSE 'N' END) AS fl_ordem
              FROM gestao.regulamento_alteracao_unidade_basica raub
              JOIN gestao.regulamento_alteracao_estrutura rae
                ON raub.cd_regulamento_alteracao_estrutura = rae.cd_regulamento_alteracao_estrutura
             WHERE raub.dt_exclusao                             IS NULL
               AND rae.dt_removido                              IS NULL
               AND raub.cd_regulamento_alteracao                = ".intval($cd_regulamento_alteracao)."
               AND raub.cd_regulamento_alteracao_estrutura_tipo = ".intval($cd_regulamento_alteracao_estrutura_tipo)."
               AND raub.nr_ordem                                ".trim($ds_operador)." ".intval($nr_ordem).";";

		return $this->db->query($qr_sql)->row_array();
    }	
    
    public function verifica_ordem_unidade_basica_filho($cd_regulamento_alteracao, $cd_regulamento_alteracao_estrutura_tipo, $cd_regulamento_alteracao_unidade_basica_pai, $nr_ordem, $ds_operador = '=')
    {
        $qr_sql = "               
               SELECT (CASE WHEN COUNT(*) > 0 THEN 'S' ELSE 'N' END) AS fl_ordem
               FROM gestao.regulamento_alteracao_unidade_basica raub
               JOIN gestao.regulamento_alteracao_estrutura rae
                 ON raub.cd_regulamento_alteracao_estrutura = rae.cd_regulamento_alteracao_estrutura
              WHERE rae.dt_removido                                  IS NULL
                AND raub.dt_exclusao                                 IS NULL
                AND raub.cd_regulamento_alteracao                    = ".intval($cd_regulamento_alteracao)."
                AND raub.cd_regulamento_alteracao_estrutura_tipo     = ".intval($cd_regulamento_alteracao_estrutura_tipo)."
                AND raub.cd_regulamento_alteracao_unidade_basica_pai = ".intval($cd_regulamento_alteracao_unidade_basica_pai)."
                AND raub.nr_ordem                                    ".trim($ds_operador)." ".intval($nr_ordem).";";    
               
        return $this->db->query($qr_sql)->row_array();
    }

    public function atualiza_unidade_basica_renumeracao_filho($cd_regulamento_alteracao_unidade_basica, $args = array(), $ds_operador = '+')
	{
		$qr_sql = "
            UPDATE gestao.regulamento_alteracao_unidade_basica AS t
               SET nr_ordem             = x.nr_ordem,
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
              FROM (SELECT raub.cd_regulamento_alteracao_unidade_basica,
                          (raub.nr_ordem ".trim($ds_operador)."1) AS nr_ordem
                     FROM gestao.regulamento_alteracao_unidade_basica raub
                     JOIN gestao.regulamento_alteracao_estrutura rae
                       ON raub.cd_regulamento_alteracao_estrutura = rae.cd_regulamento_alteracao_estrutura 
                    WHERE raub.cd_regulamento_alteracao_estrutura_tipo     = ".intval($args['cd_regulamento_alteracao_estrutura_tipo'])."
                      AND raub.cd_regulamento_alteracao                    = ".intval($args['cd_regulamento_alteracao'])."
                      AND raub.nr_ordem                                    >= ".intval($args['nr_ordem'])."
                      AND raub.cd_regulamento_alteracao_unidade_basica_pai = ".intval($args['cd_regulamento_alteracao_unidade_basica_pai'])."
                      AND raub.cd_regulamento_alteracao_unidade_basica     != ".intval($cd_regulamento_alteracao_unidade_basica)."
                      AND rae.dt_removido                                  IS NULL
                      AND raub.dt_removido                                 IS NULL
                      AND raub.dt_exclusao                                 IS NULL
                    ORDER BY raub.nr_ordem) x
             WHERE t.cd_regulamento_alteracao_unidade_basica = x.cd_regulamento_alteracao_unidade_basica;";

		$this->db->query($qr_sql); 
    }

	public function salvar_estrutura($args = array())
	{
		$cd_regulamento_alteracao_estrutura = intval($this->db->get_new_id('gestao.regulamento_alteracao_estrutura', 'cd_regulamento_alteracao_estrutura'));

		$qr_sql = "
			INSERT INTO gestao.regulamento_alteracao_estrutura
			     (
			        cd_regulamento_alteracao_estrutura,
			     	cd_regulamento_alteracao, 
                   	nr_ordem, 
                   	cd_regulamento_alteracao_estrutura_tipo, 
            	   	ds_regulamento_alteracao_estrutura, 
            	  	cd_regulamento_alteracao_estrutura_pai,
            	  	cd_regulamento_alteracao_estrutura_referencia,
                   	cd_usuario_inclusao, 
                   	cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".intval($cd_regulamento_alteracao_estrutura).",
                 	".(trim($args['cd_regulamento_alteracao']) != '' ? intval($args['cd_regulamento_alteracao']) : "DEFAULT").",	
                 	".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                 	".(trim($args['cd_regulamento_alteracao_estrutura_tipo']) != '' ? intval($args['cd_regulamento_alteracao_estrutura_tipo']) : "DEFAULT").",
                 	".(trim($args['ds_regulamento_alteracao_estrutura']) != '' ? str_escape($args['ds_regulamento_alteracao_estrutura']) : "DEFAULT").",
                 	".(trim($args['cd_regulamento_alteracao_estrutura_pai']) != '' ? intval($args['cd_regulamento_alteracao_estrutura_pai']) : "DEFAULT").",
                 	".(((isset($args['cd_regulamento_alteracao_estrutura_referencia'])) AND (trim($args['cd_regulamento_alteracao_estrutura_referencia'])) != '') ? intval($args['cd_regulamento_alteracao_estrutura_referencia']) : "DEFAULT").",
				    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])." 
                 );";

        $this->db->query($qr_sql);

        return $cd_regulamento_alteracao_estrutura;
	}

	public function atualizar_estrutura($cd_regulamento_alteracao_estrutura, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.regulamento_alteracao_estrutura
               SET cd_regulamento_alteracao_estrutura_pai  = ".(trim($args['cd_regulamento_alteracao_estrutura_pai']) != '' ? intval($args['cd_regulamento_alteracao_estrutura_pai']) : "DEFAULT").",
			       cd_regulamento_alteracao_estrutura_tipo = ".(trim($args['cd_regulamento_alteracao_estrutura_tipo']) != '' ? intval($args['cd_regulamento_alteracao_estrutura_tipo']) : "DEFAULT").",
			       nr_ordem                                = ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                   ds_regulamento_alteracao_estrutura      = ".(trim($args['ds_regulamento_alteracao_estrutura']) != '' ? str_escape($args['ds_regulamento_alteracao_estrutura']) : "DEFAULT").",
                   dt_alteracao                            = CURRENT_TIMESTAMP,
                   cd_usuario_alteracao                    = ".intval($args['cd_usuario'])."           
             WHERE cd_regulamento_alteracao_estrutura      = ".intval($cd_regulamento_alteracao_estrutura).";";

        $this->db->query($qr_sql);  
	}

	public function atuliza_estrutura_renumeracao($cd_regulamento_alteracao_estrutura, $args = array(), $ds_operador = '+')
	{
		$qr_sql = "
			UPDATE gestao.regulamento_alteracao_estrutura AS t
			   SET nr_ordem             = x.nr_ordem,
			       cd_usuario_alteracao = ".intval($args['cd_usuario']).",
			       dt_alteracao         = CURRENT_TIMESTAMP
			  FROM (SELECT cd_regulamento_alteracao_estrutura,
					       (nr_ordem ".trim($ds_operador)."1) AS nr_ordem
					  FROM gestao.regulamento_alteracao_estrutura
					 WHERE dt_exclusao                             IS NULL
					   AND dt_removido                             IS NULL
					   AND cd_regulamento_alteracao                = ".intval($args['cd_regulamento_alteracao'])."
					   AND cd_regulamento_alteracao_estrutura_tipo = ".intval($args['cd_regulamento_alteracao_estrutura_tipo'])."
					   AND nr_ordem                                >= ".intval($args['nr_ordem'])."
					   AND cd_regulamento_alteracao_estrutura      != ".intval($cd_regulamento_alteracao_estrutura)."
					   ".(intval($args['cd_regulamento_alteracao_estrutura_pai']) > 0 ? "AND cd_regulamento_alteracao_estrutura_pai = ".intval($args['cd_regulamento_alteracao_estrutura_pai']) : "")."
                     ORDER BY nr_ordem) x
             WHERE t.cd_regulamento_alteracao_estrutura = x.cd_regulamento_alteracao_estrutura;";

		$this->db->query($qr_sql); 
    }

    public function remover_estrutura($cd_regulamento_alteracao_estrutura, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.regulamento_alteracao_estrutura
			   SET cd_usuario_removido = ".intval($cd_usuario).",
			       dt_removido         = CURRENT_TIMESTAMP
			 WHERE cd_regulamento_alteracao_estrutura = ".intval($cd_regulamento_alteracao_estrutura).";";

		$this->db->query($qr_sql); 
	}

    public function excluir_estrutura($cd_regulamento_alteracao_estrutura, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_estrutura
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_regulamento_alteracao_estrutura = ".intval($cd_regulamento_alteracao_estrutura).";";

        $this->db->query($qr_sql); 
    }
    
    public function atualiza_unidade_basica_renumeracao($cd_regulamento_alteracao_unidade_basica, $args = array(), $ds_operador = '+')
	{
		$qr_sql = "
            UPDATE gestao.regulamento_alteracao_unidade_basica AS t
               SET nr_ordem             = x.nr_ordem,
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             FROM (SELECT raub.cd_regulamento_alteracao_unidade_basica,
                          (raub.nr_ordem ".trim($ds_operador)."1) AS nr_ordem
                     FROM gestao.regulamento_alteracao_unidade_basica raub
                     JOIN gestao.regulamento_alteracao_estrutura rae
                       ON rae.cd_regulamento_alteracao_estrutura = raub.cd_regulamento_alteracao_estrutura
                    WHERE raub.dt_exclusao                             IS NULL
                      AND rae.dt_removido                              IS NULL
                      AND raub.cd_regulamento_alteracao                = ".intval($args['cd_regulamento_alteracao'])."
                      AND raub.cd_regulamento_alteracao_estrutura_tipo = ".intval($args['cd_regulamento_alteracao_estrutura_tipo'])."
                      AND raub.nr_ordem                                >= ".intval($args['nr_ordem'])."
                      AND raub.cd_regulamento_alteracao_unidade_basica != ".intval($cd_regulamento_alteracao_unidade_basica)."
                   ORDER BY raub.nr_ordem) x
            WHERE t.cd_regulamento_alteracao_unidade_basica = x.cd_regulamento_alteracao_unidade_basica;";

		$this->db->query($qr_sql); 
    }
    
    public function remover_unidade_basica($cd_regulamento_alteracao_unidade_basica, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.regulamento_alteracao_unidade_basica
			   SET cd_usuario_removido = ".intval($cd_usuario).",
                   dt_removido         = CURRENT_TIMESTAMP
			 WHERE cd_regulamento_alteracao_unidade_basica = ".intval($cd_regulamento_alteracao_unidade_basica).";";

		$this->db->query($qr_sql); 
	}

    public function excluir_unidade_basica($cd_regulamento_alteracao_unidade_basica, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_unidade_basica
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_regulamento_alteracao_unidade_basica = ".intval($cd_regulamento_alteracao_unidade_basica).";";

        $this->db->query($qr_sql); 
    }

	public function atualizar_estrutura_pai($cd_regulamento_alteracao)
	{
		$qr_sql = "
			UPDATE gestao.regulamento_alteracao_estrutura AS t
			   SET cd_regulamento_alteracao_estrutura_pai = x.cd_regulamento_alteracao_estrutura_pai
			  FROM (SELECT rae1.cd_regulamento_alteracao_estrutura,
				           (SELECT rae2.cd_regulamento_alteracao_estrutura 
					          FROM gestao.regulamento_alteracao_estrutura rae2
					         WHERE rae2.cd_regulamento_alteracao_estrutura_referencia = rae1.cd_regulamento_alteracao_estrutura_pai) AS cd_regulamento_alteracao_estrutura_pai
				      FROM gestao.regulamento_alteracao_estrutura rae1
				     WHERE rae1.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao)." 
                       AND rae1.cd_regulamento_alteracao_estrutura_pai IS NOT NULL
                       AND rae1.dt_exclusao IS NULL) x
			 WHERE t.cd_regulamento_alteracao_estrutura = x.cd_regulamento_alteracao_estrutura;";

		$this->db->query($qr_sql); 
	}

	public function atualizar_unidade_basica_estrutura($cd_regulamento_alteracao)
	{
		$qr_sql = "
			UPDATE gestao.regulamento_alteracao_unidade_basica AS t
			   SET cd_regulamento_alteracao_estrutura = x.cd_regulamento_alteracao_estrutura
			  FROM (SELECT raub.cd_regulamento_alteracao_unidade_basica,
				           (SELECT rae.cd_regulamento_alteracao_estrutura 
					          FROM gestao.regulamento_alteracao_estrutura rae
					         WHERE rae.cd_regulamento_alteracao_estrutura_referencia = raub.cd_regulamento_alteracao_estrutura) AS cd_regulamento_alteracao_estrutura
				      FROM gestao.regulamento_alteracao_unidade_basica raub
                     WHERE raub.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao)."
                       AND raub.dt_exclusao IS NULL) x
			 WHERE t.cd_regulamento_alteracao_unidade_basica = x.cd_regulamento_alteracao_unidade_basica
               AND x.cd_regulamento_alteracao_estrutura IS NOT NULL;";

		$this->db->query($qr_sql); 
	}

	public function atualizar_unidade_basica_pai($cd_regulamento_alteracao)
	{
		$qr_sql = "
			UPDATE gestao.regulamento_alteracao_unidade_basica AS t
			   SET cd_regulamento_alteracao_unidade_basica_pai = x.cd_regulamento_alteracao_unidade_basica_pai
			  FROM (SELECT raub1.cd_regulamento_alteracao_unidade_basica,
				           (SELECT raub2.cd_regulamento_alteracao_unidade_basica
					          FROM gestao.regulamento_alteracao_unidade_basica raub2
					         WHERE raub2.cd_regulamento_alteracao_unidade_basica_referencia = raub1.cd_regulamento_alteracao_unidade_basica_pai) AS cd_regulamento_alteracao_unidade_basica_pai
				      FROM gestao.regulamento_alteracao_unidade_basica raub1
				     WHERE raub1.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao)." 
                       AND raub1.cd_regulamento_alteracao_unidade_basica_pai IS NOT NULL
                       AND raub1.dt_exclusao IS NULL) x
			 WHERE t.cd_regulamento_alteracao_unidade_basica = x.cd_regulamento_alteracao_unidade_basica
               AND x.cd_regulamento_alteracao_unidade_basica_pai IS NOT NULL;";

		$this->db->query($qr_sql); 
    }
    
    public function atualizar_unidade_basica_ref($cd_regulamento_alteracao)
    {
        $qr_sql = "
			UPDATE gestao.regulamento_alteracao_unidade_basica_ref AS t
			   SET cd_regulamento_alteracao_unidade_basica = x.cd_regulamento_alteracao_unidade_basica
			  FROM (SELECT raubr.cd_regulamento_alteracao_unidade_basica_ref,
                           (SELECT raub2.cd_regulamento_alteracao_unidade_basica
                              FROM gestao.regulamento_alteracao_unidade_basica raub2
                             WHERE raub2.cd_regulamento_alteracao_unidade_basica_referencia = raubr.cd_regulamento_alteracao_unidade_basica) AS cd_regulamento_alteracao_unidade_basica
                      FROM gestao.regulamento_alteracao_unidade_basica_ref raubr
                     WHERE raubr.dt_exclusao             IS NULL
                       AND raubr.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao).") x
             WHERE t.cd_regulamento_alteracao_unidade_basica_ref = x.cd_regulamento_alteracao_unidade_basica_ref
               AND x.cd_regulamento_alteracao_unidade_basica IS NOT NULL;
             
             UPDATE gestao.regulamento_alteracao_unidade_basica_ref AS t
                SET cd_regulamento_alteracao_unidade_basica_referenciado = x.cd_regulamento_alteracao_unidade_basica_referenciado
               FROM (SELECT raubr.cd_regulamento_alteracao_unidade_basica_ref,
                            (SELECT raub2.cd_regulamento_alteracao_unidade_basica
                               FROM gestao.regulamento_alteracao_unidade_basica raub2
                              WHERE raub2.cd_regulamento_alteracao_unidade_basica_referencia = raubr.cd_regulamento_alteracao_unidade_basica_referenciado) AS cd_regulamento_alteracao_unidade_basica_referenciado
                       FROM gestao.regulamento_alteracao_unidade_basica_ref raubr
                      WHERE raubr.dt_exclusao             IS NULL
                        AND raubr.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao).") x
              WHERE t.cd_regulamento_alteracao_unidade_basica_ref = x.cd_regulamento_alteracao_unidade_basica_ref
                AND x.cd_regulamento_alteracao_unidade_basica_referenciado IS NOT NULL;";

        $this->db->query($qr_sql); 
    }

    public function atualizar_estrutura_ref($cd_regulamento_alteracao)
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_unidade_basica_estrutura_ref AS t
               SET cd_regulamento_alteracao_unidade_basica = x.cd_regulamento_alteracao_unidade_basica
             FROM (SELECT rauber.cd_regulamento_alteracao_unidade_basica_estrutura_ref,
                          (SELECT raub.cd_regulamento_alteracao_unidade_basica
                             FROM gestao.regulamento_alteracao_unidade_basica raub
                            WHERE raub.cd_regulamento_alteracao_unidade_basica_referencia = rauber.cd_regulamento_alteracao_unidade_basica) AS cd_regulamento_alteracao_unidade_basica
                     FROM gestao.regulamento_alteracao_unidade_basica_estrutura_ref rauber
                    WHERE rauber.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao)."
                      AND rauber.dt_exclusao IS NULL) x
             WHERE t.cd_regulamento_alteracao_unidade_basica_estrutura_ref = x.cd_regulamento_alteracao_unidade_basica_estrutura_ref
               AND x.cd_regulamento_alteracao_unidade_basica IS NOT NULL;
             
            UPDATE gestao.regulamento_alteracao_unidade_basica_estrutura_ref AS t
               SET cd_regulamento_alteracao_estrutura_referenciado = x.cd_regulamento_alteracao_estrutura_referenciado
              FROM (SELECT rauber.cd_regulamento_alteracao_unidade_basica_estrutura_ref,
                           (SELECT rae.cd_regulamento_alteracao_estrutura
                              FROM gestao.regulamento_alteracao_estrutura rae
                             WHERE rae.cd_regulamento_alteracao_estrutura_referencia = rauber.cd_regulamento_alteracao_estrutura_referenciado) AS cd_regulamento_alteracao_estrutura_referenciado
                      FROM gestao.regulamento_alteracao_unidade_basica_estrutura_ref rauber
                     WHERE rauber.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao)."
                       AND rauber.dt_exclusao IS NULL) x
              WHERE t.cd_regulamento_alteracao_unidade_basica_estrutura_ref = x.cd_regulamento_alteracao_unidade_basica_estrutura_ref
                AND x.cd_regulamento_alteracao_estrutura_referenciado IS NOT NULL;";

        $this->db->query($qr_sql); 
    }

	public function get_estrutura($cd_regulamento_alteracao, $cd_regulamento_alteracao_estrutura = 0, $cd_regulamento_alteracao_estrutura_pai = 0, $tipo = array(), $fl_removido = 'N')
	{
		$qr_sql = "
			SELECT rae.cd_regulamento_alteracao_estrutura,
			       rae.cd_regulamento_alteracao,
			       rae.nr_ordem,
			       raet.ds_class_label,
			       rae.cd_regulamento_alteracao_estrutura_tipo,
			       rae.ds_regulamento_alteracao_estrutura,
			       raet.ds_regulamento_alteracao_estrutura_tipo || ' ' || TO_CHAR(rae.nr_ordem, 'FMRN') AS ds_tipo,
			       rae.cd_regulamento_alteracao_estrutura_pai,
			       rae.cd_regulamento_alteracao_estrutura AS value,
			       raet.ds_regulamento_alteracao_estrutura_tipo || ' ' || TO_CHAR(rae.nr_ordem, 'FMRN') || ' - ' || rae.ds_regulamento_alteracao_estrutura AS text,
                   rae.cd_regulamento_alteracao_estrutura_referencia,
                   raet.ds_regulamento_alteracao_estrutura_tipo,
                   rae.dt_removido,
                   (CASE WHEN rae.dt_removido IS NOT NULL
                         THEN 0
                         ELSE 1
                   END) nr_ordem_removido,
			       (SELECT COUNT(*)
			          FROM gestao.regulamento_alteracao_estrutura rae_filho
			         WHERE rae_filho.dt_exclusao IS NULL
			           AND rae_filho.dt_removido IS NULL
			           AND rae_filho.cd_regulamento_alteracao_estrutura_pai = rae.cd_regulamento_alteracao_estrutura) AS qt_filho,
			       (SELECT COUNT(*)
					  FROM gestao.regulamento_alteracao_unidade_basica raub
					 WHERE raub.dt_exclusao                             IS NULL
					   AND raub.cd_regulamento_alteracao_estrutura_tipo = 4
					   AND raub.cd_regulamento_alteracao_estrutura      = rae.cd_regulamento_alteracao_estrutura) AS qt_artigo,
                   (CASE WHEN (SELECT TRIM(rae2.ds_regulamento_alteracao_estrutura)
                                  FROM gestao.regulamento_alteracao_estrutura rae2
                                 WHERE rae2.cd_regulamento_alteracao_estrutura = rae.cd_regulamento_alteracao_estrutura_referencia) != TRIM(rae.ds_regulamento_alteracao_estrutura) 
                         THEN 'S'
                         WHEN rae.cd_regulamento_alteracao_estrutura_referencia IS NULL
                         THEN 'S'
                         ELSE 'N'
                   END) AS fl_alteracao_texto,
                   (CASE WHEN (SELECT rae2.nr_ordem
                                 FROM gestao.regulamento_alteracao_estrutura rae2
                                WHERE rae2.cd_regulamento_alteracao_estrutura = rae.cd_regulamento_alteracao_estrutura_referencia) != rae.nr_ordem
                         THEN 'S'
                         WHEN rae.cd_regulamento_alteracao_estrutura_referencia IS NULL
                         THEN 'S'
                         ELSE 'N'
                   END) AS fl_alteracao_ordem
			  FROM gestao.regulamento_alteracao_estrutura rae
			  JOIN gestao.regulamento_alteracao_estrutura_tipo raet
			    ON raet.cd_regulamento_alteracao_estrutura_tipo = rae.cd_regulamento_alteracao_estrutura_tipo
			 WHERE rae.dt_exclusao                        IS NULL
               ".(trim($fl_removido) == 'N' ? "AND rae.dt_removido IS NULL" : "")."
			   AND rae.cd_regulamento_alteracao           = ".intval($cd_regulamento_alteracao)."
			   AND rae.cd_regulamento_alteracao_estrutura != ".intval($cd_regulamento_alteracao_estrutura)."
			   AND ".(intval($cd_regulamento_alteracao_estrutura_pai) > 0 ? "cd_regulamento_alteracao_estrutura_pai = ".intval($cd_regulamento_alteracao_estrutura_pai) : "cd_regulamento_alteracao_estrutura_pai IS NULL")."
			   ".(count($tipo) > 0 ? "AND rae.cd_regulamento_alteracao_estrutura_tipo IN (".implode(',', $tipo).")" : "")."
			 ORDER BY rae.nr_ordem ASC, nr_ordem_removido ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_estrutura($cd_regulamento_alteracao_estrutura)
	{
		$qr_sql = "
			SELECT rae.cd_regulamento_alteracao_estrutura,
                   raet.cd_regulamento_alteracao_estrutura_tipo_filho,
                   raet.ds_regulamento_alteracao_estrutura_tipo,
                   rae.nr_ordem,
                   rae.cd_regulamento_alteracao_estrutura_tipo,
                   rae.ds_regulamento_alteracao_estrutura,
                   rae.cd_regulamento_alteracao_estrutura_pai,
                   raet.ds_class_label,
                   raet_pai.ds_regulamento_alteracao_estrutura_tipo || ' ' || TO_CHAR(rae_pai.nr_ordem, 'FMRN') AS ds_tipo,
                   rae_pai.ds_regulamento_alteracao_estrutura AS ds_regulamento_alteracao_estrutura_pai,
                   raet.ds_regulamento_alteracao_estrutura_tipo || ' ' || TO_CHAR(rae.nr_ordem, 'FMRN') AS ds_ordem,
                   raet.ds_regulamento_alteracao_estrutura_tipo || ' ' || TO_CHAR(rae.nr_ordem, 'FMRN') || ' - ' || rae.ds_regulamento_alteracao_estrutura AS ds_estrutura,
                   rae.dt_removido,
                   (CASE WHEN (SELECT rae2.nr_ordem
                                  FROM gestao.regulamento_alteracao_estrutura rae2
                                 WHERE rae2.cd_regulamento_alteracao_estrutura = rae.cd_regulamento_alteracao_estrutura_referencia) != rae.nr_ordem
                         THEN 'S'
                         ELSE 'N'
                   END) AS fl_alteracao_ordem,
                   (CASE WHEN (SELECT rae2.nr_ordem
                                 FROM gestao.regulamento_alteracao_estrutura rae2
                                WHERE rae2.cd_regulamento_alteracao_estrutura = rae.cd_regulamento_alteracao_estrutura_referencia) != rae.nr_ordem
                         THEN (SELECT raet2.ds_regulamento_alteracao_estrutura_tipo || ' ' || TO_CHAR(rae2.nr_ordem, 'FMRN')
                                 FROM gestao.regulamento_alteracao_estrutura rae2
                                 JOIN gestao.regulamento_alteracao_estrutura_tipo raet2
                                   ON raet2.cd_regulamento_alteracao_estrutura_tipo = rae2.cd_regulamento_alteracao_estrutura_tipo 
                                WHERE rae2.cd_regulamento_alteracao_estrutura = rae.cd_regulamento_alteracao_estrutura_referencia)
                         ELSE ''
                   END) AS ds_alteracao_referencia
			  FROM gestao.regulamento_alteracao_estrutura rae
			  JOIN gestao.regulamento_alteracao_estrutura_tipo raet
			    ON raet.cd_regulamento_alteracao_estrutura_tipo = rae.cd_regulamento_alteracao_estrutura_tipo
			  LEFT JOIN gestao.regulamento_alteracao_estrutura rae_pai
			    ON rae_pai.cd_regulamento_alteracao_estrutura = rae.cd_regulamento_alteracao_estrutura_pai
			  LEFT JOIN gestao.regulamento_alteracao_estrutura_tipo raet_pai
			    ON raet_pai.cd_regulamento_alteracao_estrutura_tipo = rae_pai.cd_regulamento_alteracao_estrutura_tipo
			 WHERE rae.dt_exclusao                        IS NULL
			 --  AND rae.dt_removido                        IS NULL
			   AND rae.cd_regulamento_alteracao_estrutura = ".intval($cd_regulamento_alteracao_estrutura).";";

		return $this->db->query($qr_sql)->row_array();
    }

    public function carrega_estrutura_artigo($cd_regulamento_alteracao_unidade_basica)
	{
		$qr_sql = "
            SELECT raub.cd_regulamento_alteracao_unidade_basica,
                   raub.cd_regulamento_alteracao_estrutura,
                   raub.nr_ordem,
                   raub.ds_regulamento_alteracao_unidade_basica,
                   raet.ds_regulamento_alteracao_estrutura_tipo || ' ' || TO_CHAR(rae.nr_ordem, 'FMRN') || ' - ' || rae.ds_regulamento_alteracao_estrutura AS ds_estrutura  
              FROM gestao.regulamento_alteracao_unidade_basica raub
              JOIN gestao.regulamento_alteracao_estrutura rae
                ON rae.cd_regulamento_alteracao_estrutura = raub.cd_regulamento_alteracao_estrutura
              JOIN gestao.regulamento_alteracao_estrutura_tipo raet
                ON raet.cd_regulamento_alteracao_estrutura_tipo = rae.cd_regulamento_alteracao_estrutura_tipo
             WHERE raub.dt_exclusao IS NULL
               AND raub.cd_regulamento_alteracao_unidade_basica = ".intval($cd_regulamento_alteracao_unidade_basica).";";

		return $this->db->query($qr_sql)->row_array();
    }

    public function carrega_estrutura_unidade_artigo($cd_regulamento_alteracao_unidade_basica)
	{
		$qr_sql = "
            SELECT raub.cd_regulamento_alteracao_unidade_basica,
                   raub.cd_regulamento_alteracao_unidade_basica_pai,
                   raub.cd_regulamento_alteracao_estrutura_tipo,
                   raub.nr_ordem,
                   raub.ds_regulamento_alteracao_unidade_basica,
                   raet.ds_regulamento_alteracao_estrutura_tipo,
                   (SELECT raub2.ds_regulamento_alteracao_unidade_basica
                      FROM gestao.regulamento_alteracao_unidade_basica raub2
                     WHERE raub2.dt_exclusao IS NULL 
                       AND raub2.cd_regulamento_alteracao_unidade_basica = raub.cd_regulamento_alteracao_unidade_basica_pai) AS ds_regulamento_alteracao_unidadE_basica_pai
              FROM gestao.regulamento_alteracao_unidade_basica raub
              JOIN gestao.regulamento_alteracao_estrutura_tipo raet
                ON raet.cd_regulamento_alteracao_estrutura_tipo = raub.cd_regulamento_alteracao_estrutura_tipo
             WHERE raub.dt_exclusao IS NULL 
               AND raub.cd_regulamento_alteracao_unidade_basica = ".intval($cd_regulamento_alteracao_unidade_basica).";";

		return $this->db->query($qr_sql)->row_array();
    }

	public function get_next_ordem($cd_regulamento_alteracao, $cd_regulamento_alteracao_estrutura_pai = 0)
	{
		$qr_sql = "
			SELECT (COALESCE(MAX(nr_ordem), 0) + 1) AS nr_ordem
			  FROM gestao.regulamento_alteracao_estrutura
			 WHERE dt_exclusao              IS NULL
               AND dt_removido              IS NULL
			   AND cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao)." 
			   ".(intval($cd_regulamento_alteracao_estrutura_pai) > 0 ? "AND cd_regulamento_alteracao_estrutura_pai = ".intval($cd_regulamento_alteracao_estrutura_pai) : "").";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_next_ordem_artigo($cd_regulamento_alteracao)
	{
		$qr_sql = "
			SELECT (COALESCE(MAX(nr_ordem), 0) + 1) AS nr_ordem
			  FROM gestao.regulamento_alteracao_unidade_basica
			 WHERE dt_exclusao                             IS NULL
               AND dt_removido                             IS NULL
			   AND cd_regulamento_alteracao_estrutura_tipo = 4
			   AND cd_regulamento_alteracao                = ".intval($cd_regulamento_alteracao).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar_estrutura_unidade_basica($args = array())
	{  
        $cd_regulamento_alteracao_unidade_basica = intval($this->db->get_new_id('gestao.regulamento_alteracao_unidade_basica', 'cd_regulamento_alteracao_unidade_basica'));

		$qr_sql = "
			INSERT INTO gestao.regulamento_alteracao_unidade_basica
			     (
                    cd_regulamento_alteracao_unidade_basica,
			        cd_regulamento_alteracao,
            		nr_ordem,
                    cd_regulamento_alteracao_unidade_basica_pai,
            		cd_regulamento_alteracao_estrutura_tipo, 
            		ds_regulamento_alteracao_unidade_basica, 
                    cd_regulamento_alteracao_estrutura,
                    cd_regulamento_alteracao_unidade_basica_referencia,
            		cd_usuario_inclusao, 
                    cd_usuario_alteracao                    
                 )
            VALUES 
                 (
                    ".intval($cd_regulamento_alteracao_unidade_basica).",
                 	".(trim($args['cd_regulamento_alteracao']) != '' ? intval($args['cd_regulamento_alteracao']) : "DEFAULT").",	
                 	".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                 	".(trim($args['cd_regulamento_alteracao_unidade_basica_pai']) != '' ? intval($args['cd_regulamento_alteracao_unidade_basica_pai']) : "DEFAULT").",
                 	".(trim($args['cd_regulamento_alteracao_estrutura_tipo']) != '' ? intval($args['cd_regulamento_alteracao_estrutura_tipo']) : "DEFAULT").",
                 	".(trim($args['ds_regulamento_alteracao_unidade_basica']) != '' ? str_escape($args['ds_regulamento_alteracao_unidade_basica']) : "DEFAULT").",
                     ".(trim($args['cd_regulamento_alteracao_estrutura']) != '' ? intval($args['cd_regulamento_alteracao_estrutura']) : "DEFAULT").",
                     ".(((isset($args['cd_regulamento_alteracao_unidade_basica_referencia'])) AND (trim($args['cd_regulamento_alteracao_unidade_basica_referencia'])) != '') ? intval($args['cd_regulamento_alteracao_unidade_basica_referencia']) : "DEFAULT").",
                 	".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])." 
                 );";

        $this->db->query($qr_sql);

        return $cd_regulamento_alteracao_unidade_basica;
    }
    
    public function atualizar_estrutura_unidade_basica($cd_regulamento_alteracao_unidade_basica, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.regulamento_alteracao_unidade_basica
               SET cd_regulamento_alteracao_estrutura          = ".(trim($args['cd_regulamento_alteracao_estrutura']) != '' ? intval($args['cd_regulamento_alteracao_estrutura']) : "DEFAULT").",	
                   nr_ordem                                    = ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                   cd_regulamento_alteracao_unidade_basica_pai = ".(trim($args['cd_regulamento_alteracao_unidade_basica_pai']) != '' ? intval($args['cd_regulamento_alteracao_unidade_basica_pai']) : "DEFAULT").",
                   ds_regulamento_alteracao_unidade_basica     = ".(trim($args['ds_regulamento_alteracao_unidade_basica']) != '' ? str_escape($args['ds_regulamento_alteracao_unidade_basica']) : "DEFAULT").",
                   cd_regulamento_alteracao_estrutura_tipo     = ".(trim($args['cd_regulamento_alteracao_estrutura_tipo']) != '' ? intval($args['cd_regulamento_alteracao_estrutura_tipo']) : "DEFAULT").",
                   dt_alteracao                                = CURRENT_TIMESTAMP,
                   cd_usuario_alteracao                        = ".intval($args['cd_usuario'])."           
             WHERE cd_regulamento_alteracao_unidade_basica = ".intval($cd_regulamento_alteracao_unidade_basica).";";

        $this->db->query($qr_sql);  
	}

	public function get_estrutura_artigo($cd_regulamento_alteracao, $cd_regulamento_alteracao_estrutura = 0, $fl_removido = 'N')
	{
		$qr_sql = "
            SELECT raub.cd_regulamento_alteracao_unidade_basica,
                   raub.cd_regulamento_alteracao_estrutura_tipo,
                   raub.cd_regulamento_alteracao,
                   raub.cd_regulamento_alteracao_unidade_basica_pai,
                   raub.nr_ordem,
                   raub.ds_regulamento_alteracao_unidade_basica,
                   raub.cd_regulamento_alteracao_estrutura,
                   rae.ds_regulamento_alteracao_estrutura,
                   raet.ds_regulamento_alteracao_estrutura_tipo,
                   raub.cd_regulamento_alteracao_unidade_basica_referencia,
                   raub.dt_removido,
                   raet2.ds_regulamento_alteracao_estrutura_tipo AS ds_tipo_unidade_basica,
                   (CASE WHEN raub.dt_removido IS NOT NULL
                         THEN 0
                         ELSE 1
                   END) nr_ordem_removido,
                   raet.ds_regulamento_alteracao_estrutura_tipo || ' ' || TO_CHAR(rae.nr_ordem, 'FMRN') || ' - ' || rae.ds_regulamento_alteracao_estrutura AS ds_estrutura,
                   raet.ds_class_label,
                   'Art. ' || raub.nr_ordem || (CASE WHEN raub.nr_ordem <= 9 THEN 'º ' ELSE '. ' END) || raub.ds_regulamento_alteracao_unidade_basica AS ds_artigo,
                   'Art. ' AS ds_sigla_artigo,
                   raub.nr_ordem || (CASE WHEN raub.nr_ordem <= 9 THEN 'º ' ELSE '. ' END) AS ds_numeracao_sigla_artigo,
                   (CASE WHEN (SELECT raub2.nr_ordem
                                 FROM gestao.regulamento_alteracao_unidade_basica raub2
                                WHERE raub2.cd_regulamento_alteracao_unidade_basica = raub.cd_regulamento_alteracao_unidade_basica_referencia) != raub.nr_ordem
                         THEN 'S'
                         WHEN raub.cd_regulamento_alteracao_unidade_basica_referencia IS NULL
                         THEN 'S'
                         ELSE 'N'
                   END) AS fl_alteracao_ordem,
                   (CASE WHEN (SELECT TRIM(funcoes.strip_tags(raub2.ds_regulamento_alteracao_unidade_basica))
                                 FROM gestao.regulamento_alteracao_unidade_basica raub2
                                WHERE raub2.cd_regulamento_alteracao_unidade_basica = raub.cd_regulamento_alteracao_unidade_basica_referencia) != TRIM(funcoes.strip_tags(raub.ds_regulamento_alteracao_unidade_basica))
                         THEN 'S'
                         WHEN raub.cd_regulamento_alteracao_unidade_basica_referencia IS NULL
                         THEN 'S'
                         ELSE 'N'
                   END) AS fl_alteracao_texto,
                   ((SELECT COUNT(*) 
                       FROM gestao.regulamento_alteracao_unidade_basica_ref raubr
                       JOIN gestao.regulamento_alteracao_unidade_basica raub2
                         ON raub2.cd_regulamento_alteracao_unidade_basica = raubr.cd_regulamento_alteracao_unidade_basica_referenciado
                       JOIN gestao.regulamento_alteracao_estrutura rae2
                         ON rae2.cd_regulamento_alteracao_estrutura = raub2.cd_regulamento_alteracao_estrutura
                      WHERE raubr.cd_regulamento_alteracao_unidade_basica = raub.cd_regulamento_alteracao_unidade_basica
                        AND raubr.dt_exclusao                             IS NULL
                        AND raub2.dt_exclusao                             IS NULL
                        AND rae2.dt_removido                              IS NULL)
                        +
                    (SELECT COUNT(*)
                       FROM gestao.regulamento_alteracao_unidade_basica_estrutura_ref rauber
                       JOIN gestao.regulamento_alteracao_unidade_basica raub2
                         ON raub2.cd_regulamento_alteracao_unidade_basica = rauber.cd_regulamento_alteracao_unidade_basica
                       JOIN gestao.regulamento_alteracao_estrutura rae2
                         ON rae2.cd_regulamento_alteracao_estrutura = raub2.cd_regulamento_alteracao_estrutura
                      WHERE rauber.cd_regulamento_alteracao_unidade_basica =  raub.cd_regulamento_alteracao_unidade_basica
                        AND raub2.dt_exclusao IS NULL
                        AND rae2.dt_removido                               IS NULL
                        AND rauber.dt_exclusao                             IS NULL)) AS qt_referencia
			  FROM gestao.regulamento_alteracao_unidade_basica raub
			  JOIN gestao.regulamento_alteracao_estrutura rae
			    ON rae.cd_regulamento_alteracao_estrutura = raub.cd_regulamento_alteracao_estrutura
               ".(trim($fl_removido) == 'N' ? "AND rae.dt_removido IS NULL" : "")."
              JOIN gestao.regulamento_alteracao_estrutura_tipo raet2
                ON raet2.cd_regulamento_alteracao_estrutura_tipo = raub.cd_regulamento_alteracao_estrutura_tipo
			  JOIN gestao.regulamento_alteracao_estrutura_tipo raet
			    ON raet.cd_regulamento_alteracao_estrutura_tipo = rae.cd_regulamento_alteracao_estrutura_tipo
			 WHERE raub.dt_exclusao                             IS NULL
               ".(trim($fl_removido) == 'N' ? "AND raub.dt_removido IS NULL" : "")."
			   AND raub.cd_regulamento_alteracao_estrutura_tipo = 4
			   AND raub.cd_regulamento_alteracao                = ".intval($cd_regulamento_alteracao)."
			   ".(intval($cd_regulamento_alteracao_estrutura) > 0 ? "AND raub.cd_regulamento_alteracao_estrutura = ".intval($cd_regulamento_alteracao_estrutura) : "")."
			 ORDER BY raub.nr_ordem ASC, nr_ordem_removido ASC;";

		return $this->db->query($qr_sql)->result_array();
    }

	public function carrega_unidade_basica($cd_regulamento_alteracao_unidade_basica)
	{
		$qr_sql = "
			SELECT raub.cd_regulamento_alteracao_unidade_basica,
                   raub.cd_regulamento_alteracao_unidade_basica_pai,
                   raub.cd_regulamento_alteracao_unidade_basica_referencia,
                   raub.nr_ordem,
                   raub.ds_regulamento_alteracao_unidade_basica,
                   raub.cd_regulamento_alteracao_estrutura,
                   rae.ds_regulamento_alteracao_estrutura,
                   raet.ds_regulamento_alteracao_estrutura_tipo,
                   raet.ds_regulamento_alteracao_estrutura_tipo || ' ' || TO_CHAR(rae.nr_ordem, 'FMRN') AS ds_tipo,
                   raet.ds_regulamento_alteracao_estrutura_tipo || ' ' || TO_CHAR(rae.nr_ordem, 'FMRN') || ' - ' || rae.ds_regulamento_alteracao_estrutura AS ds_estrutura,
                   raet.ds_class_label,
                   'Art. ' || raub.nr_ordem || (CASE WHEN raub.nr_ordem <= 9 THEN 'º ' ELSE '. ' END) || raub.ds_regulamento_alteracao_unidade_basica AS ds_artigo,
                   1 AS nr_nivel,
                   (CASE WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 4 
                         THEN 'S'
                         ELSE 'N'
                   END) AS fl_artigo,
                   (CASE WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 7
                         THEN SUBSTR('abcdefghijklmnopqrstuvwxyz', raub.nr_ordem, 1) || ') '
                         WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 8
                         THEN raub.nr_ordem || '. '
                         WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 6
                         THEN TO_CHAR(raub.nr_ordem, 'FMRN') || ' - '
                         WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 5
                         THEN (CASE WHEN (SELECT COUNT(*) 
                                            FROM gestao.regulamento_alteracao_unidade_basica raub2 
                                           WHERE raub2.dt_exclusao IS NULL 
                                             AND raub2.dt_removido IS NULL
                                             AND raub2.cd_regulamento_alteracao_unidade_basica_pai = raub.cd_regulamento_alteracao_unidade_basica_pai
                                             AND raub2.cd_regulamento_alteracao_estrutura_tipo     = raub.cd_regulamento_alteracao_estrutura_tipo) = 1
                                    THEN 'Parágrafo único. '
                                    ELSE '§ ' || raub.nr_ordem || (CASE WHEN raub.nr_ordem <= 9 THEN 'º' ELSE '.' END)
                               END) || ' '
                         WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 4
                         THEN 'Art. ' || raub.nr_ordem || (CASE WHEN raub.nr_ordem <= 9 THEN 'º ' ELSE '. ' END)
                         ELSE ''
                   END) AS ds_ordem,
                   (CASE WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 7
			             THEN SUBSTR('abcdefghijklmnopqrstuvwxyz', raub.nr_ordem, 1) || ') '
			             WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 8
			             THEN raub.nr_ordem || '. '
			             WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 6
			             THEN TO_CHAR(raub.nr_ordem, 'FMRN') || ' - '
			             WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 5
			             THEN (CASE WHEN (SELECT COUNT(*) 
			                                FROM gestao.regulamento_alteracao_unidade_basica raub2 
			                               WHERE raub2.dt_exclusao IS NULL 
                                             AND raub2.dt_removido IS NULL
			                                 AND raub2.cd_regulamento_alteracao_unidade_basica_pai = raub.cd_regulamento_alteracao_unidade_basica_pai
			                                 AND raub2.cd_regulamento_alteracao_estrutura_tipo     = raub.cd_regulamento_alteracao_estrutura_tipo) = 1
                                    THEN 'Parágrafo único. '
                                    ELSE '§ ' || raub.nr_ordem || (CASE WHEN raub.nr_ordem <= 9 THEN 'º' ELSE '.' END)
			                   END) || ' '
			             WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 4
			             THEN 'Art. ' || raub.nr_ordem || (CASE WHEN raub.nr_ordem <= 9 THEN 'º ' ELSE '. ' END)
			             ELSE ''
			       END) || raub.ds_regulamento_alteracao_unidade_basica AS ds_unidade_basica,
                   (CASE WHEN (SELECT raub4.nr_ordem
                                   FROM gestao.regulamento_alteracao_unidade_basica raub4
                                  WHERE raub4.cd_regulamento_alteracao_unidade_basica = raub.cd_regulamento_alteracao_unidade_basica_referencia) != raub.nr_ordem
                           THEN 'S'
                           WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 5 AND 

                                (CASE WHEN (SELECT COUNT(*) 
                                             FROM gestao.regulamento_alteracao_unidade_basica raub2 
                                            WHERE raub2.dt_exclusao IS NULL 
                                              AND raub2.dt_removido IS NULL
                                              AND raub2.cd_regulamento_alteracao_unidade_basica_pai = raub.cd_regulamento_alteracao_unidade_basica_pai
                                              AND raub2.cd_regulamento_alteracao_estrutura_tipo     = raub.cd_regulamento_alteracao_estrutura_tipo) = 1
                                      THEN 'Parágrafo único. '
                                      ELSE '§ '
                                 END)

                                !=

                                (SELECT (CASE WHEN (SELECT COUNT(*) 
                                                      FROM gestao.regulamento_alteracao_unidade_basica raub5 
                                                     WHERE raub5.dt_exclusao IS NULL 
                                                       AND raub5.dt_removido IS NULL
                                                       AND raub5.cd_regulamento_alteracao_unidade_basica_pai = raub4.cd_regulamento_alteracao_unidade_basica_pai
                                                       AND raub5.cd_regulamento_alteracao_estrutura_tipo     = raub4.cd_regulamento_alteracao_estrutura_tipo) = 1
                                              THEN 'Parágrafo único. '
                                              ELSE '§ '
                                         END)
                                   FROM gestao.regulamento_alteracao_unidade_basica raub4
                                  WHERE raub4.cd_regulamento_alteracao_unidade_basica = raub.cd_regulamento_alteracao_unidade_basica_referencia)
                           THEN 'S'
                           ELSE 'N'
                   END) AS fl_alteracao_ordem,
                   raub.dt_removido
			  FROM gestao.regulamento_alteracao_unidade_basica raub
			  JOIN gestao.regulamento_alteracao_estrutura rae
			    ON rae.cd_regulamento_alteracao_estrutura = raub.cd_regulamento_alteracao_estrutura
			  JOIN gestao.regulamento_alteracao_estrutura_tipo raet
			    ON raet.cd_regulamento_alteracao_estrutura_tipo = rae.cd_regulamento_alteracao_estrutura_tipo
			 WHERE raub.dt_exclusao                             IS NULL
               AND raub.cd_regulamento_alteracao_unidade_basica = ".intval($cd_regulamento_alteracao_unidade_basica).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_next_ordem_unidade($cd_regulamento_alteracao, $cd_regulamento_alteracao_unidade_basica_pai)
	{
		$qr_sql = "
			SELECT (COALESCE(MAX(nr_ordem), 0) + 1) AS nr_ordem
			  FROM gestao.regulamento_alteracao_unidade_basica
			 WHERE dt_exclusao                                 IS NULL
               AND dt_removido                                 IS NULL
			   AND cd_regulamento_alteracao_unidade_basica_pai = ".intval($cd_regulamento_alteracao_unidade_basica_pai)."
			   AND cd_regulamento_alteracao                    = ".intval($cd_regulamento_alteracao).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_next_ordem_unidade_tipo($cd_regulamento_alteracao, $cd_regulamento_alteracao_unidade_basica_pai, $cd_regulamento_alteracao_estrutura_tipo)
	{
		$qr_sql = "
			SELECT (COALESCE(MAX(nr_ordem), 0) + 1) AS nr_ordem
			  FROM gestao.regulamento_alteracao_unidade_basica
			 WHERE dt_exclusao                             IS NULL
			   AND cd_regulamento_alteracao_unidade_basica_pai = ".intval($cd_regulamento_alteracao_unidade_basica_pai)."
			   AND cd_regulamento_alteracao                    = ".intval($cd_regulamento_alteracao)."
			   AND cd_regulamento_alteracao_estrutura_tipo     = ".intval($cd_regulamento_alteracao_estrutura_tipo).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_unidade_basica($cd_regulamento_alteracao, $cd_regulamento_alteracao_unidade_basica_pai, $cd_regulamento_alteracao_unidade_basica = 0, $fl_removido = 'N')
	{
        $qr_sql ="
            SELECT raub.cd_regulamento_alteracao_unidade_basica,
                   raub.cd_regulamento_alteracao,
                   raub.nr_ordem,
                   raub.cd_regulamento_alteracao_estrutura,
                   raub.cd_regulamento_alteracao_estrutura_tipo,
                   raub.ds_regulamento_alteracao_unidade_basica,
                   raub.cd_regulamento_alteracao_unidade_basica_pai,
                   raub.cd_regulamento_alteracao_unidade_basica_referencia,
                   raet.ds_regulamento_alteracao_estrutura_tipo,
                   raet2.ds_regulamento_alteracao_estrutura_tipo || ' ' || TO_CHAR(rae.nr_ordem, 'FMRN') || ' - ' || rae.ds_regulamento_alteracao_estrutura AS ds_estrutura,
                   raet2.ds_class_label,
                   raub.dt_removido,
                   (CASE WHEN raub.dt_removido IS NOT NULL
                         THEN 0
                         ELSE 1
                   END) nr_ordem_removido,
                   ((SELECT COUNT(*) 
                       FROM gestao.regulamento_alteracao_unidade_basica_ref raubr
                       JOIN gestao.regulamento_alteracao_unidade_basica raub3
                         ON raub3.cd_regulamento_alteracao_unidade_basica = raubr.cd_regulamento_alteracao_unidade_basica_referenciado
                       JOIN gestao.regulamento_alteracao_estrutura rae2
                         ON rae2.cd_regulamento_alteracao_estrutura = raub3.cd_regulamento_alteracao_estrutura
                      WHERE raubr.cd_regulamento_alteracao_unidade_basica = raub.cd_regulamento_alteracao_unidade_basica
                        AND raubr.dt_exclusao                             IS NULL
                        AND raub3.dt_exclusao                             IS NULL
                        AND rae2.dt_removido                              IS NULL)
                        +
                    (SELECT COUNT(*)
                       FROM gestao.regulamento_alteracao_unidade_basica_estrutura_ref rauber
                       JOIN gestao.regulamento_alteracao_unidade_basica raub3
                         ON raub3.cd_regulamento_alteracao_unidade_basica = rauber.cd_regulamento_alteracao_unidade_basica
                       JOIN gestao.regulamento_alteracao_estrutura rae2
                         ON rae2.cd_regulamento_alteracao_estrutura = raub3.cd_regulamento_alteracao_estrutura
                      WHERE rauber.cd_regulamento_alteracao_unidade_basica =  raub.cd_regulamento_alteracao_unidade_basica
                        AND raub3.dt_exclusao                              IS NULL
                        AND rae2.dt_removido                               IS NULL
                        AND rauber.dt_exclusao                             IS NULL)) qt_referencia,
                   (CASE WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 7
                         THEN SUBSTR('abcdefghijklmnopqrstuvwxyz', raub.nr_ordem, 1) || ') '
                         WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 8
                         THEN raub.nr_ordem || '. '
                         WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 6
                         THEN TO_CHAR(raub.nr_ordem, 'FMRN') || ' - '
                         WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 5
                         THEN (CASE WHEN (SELECT COUNT(*) 
                                            FROM gestao.regulamento_alteracao_unidade_basica raub2 
                                           WHERE raub2.dt_exclusao IS NULL 
                                             AND raub2.dt_removido IS NULL
                                             AND raub2.cd_regulamento_alteracao_unidade_basica_pai = raub.cd_regulamento_alteracao_unidade_basica_pai
                                             AND raub2.cd_regulamento_alteracao_estrutura_tipo     = raub.cd_regulamento_alteracao_estrutura_tipo) = 1
                                    THEN 'Parágrafo único. '
                                    ELSE '§ ' || raub.nr_ordem || (CASE WHEN raub.nr_ordem <= 9 THEN 'º' ELSE '.' END)
                                END) || ' '
                         ELSE ''
                      END) || raub.ds_regulamento_alteracao_unidade_basica AS ds_unidade_basica,
                    raub.cd_regulamento_alteracao_unidade_basica AS value,
                    (CASE WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 7
                          THEN SUBSTR('abcdefghijklmnopqrstuvwxyz', raub.nr_ordem, 1) || ') '
                          WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 8
                          THEN raub.nr_ordem || '. '
                          WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 6
                          THEN TO_CHAR(raub.nr_ordem, 'FMRN') || ' - '
                          WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 5
                          THEN (CASE WHEN (SELECT COUNT(*) 
                                             FROM gestao.regulamento_alteracao_unidade_basica raub2 
                                            WHERE raub2.dt_exclusao IS NULL
                                              AND raub2.dt_removido IS NULL 
                                              AND raub2.cd_regulamento_alteracao_unidade_basica_pai = raub.cd_regulamento_alteracao_unidade_basica_pai
                                              AND raub2.cd_regulamento_alteracao_estrutura_tipo     = raub.cd_regulamento_alteracao_estrutura_tipo) = 1
                                     THEN 'Parágrafo único. '
                                     ELSE '§ ' || raub.nr_ordem || (CASE WHEN raub.nr_ordem <= 9 THEN 'º' ELSE '.' END) 
                                END) || ' '
                             ELSE ''
                     END) || raub.ds_regulamento_alteracao_unidade_basica AS text,
                    (CASE WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 7
                          THEN SUBSTR('abcdefghijklmnopqrstuvwxyz', raub.nr_ordem, 1) || ') '
                          WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 8
                          THEN raub.nr_ordem || '. '
                          WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 6
                          THEN TO_CHAR(raub.nr_ordem, 'FMRN') || ' - '
                          WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 5
                          THEN raub.nr_ordem || (CASE WHEN raub.nr_ordem <= 9 THEN 'º ' ELSE '. ' END)
                          ELSE ''
                     END) AS ds_numeracao_sigla_unidade_basica,
                    (CASE WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 5
                          THEN (CASE WHEN (SELECT COUNT(*) 
                                             FROM gestao.regulamento_alteracao_unidade_basica raub2 
                                            WHERE raub2.dt_exclusao IS NULL 
                                              AND raub2.dt_removido IS NULL
                                              AND raub2.cd_regulamento_alteracao_unidade_basica_pai = raub.cd_regulamento_alteracao_unidade_basica_pai
                                              AND raub2.cd_regulamento_alteracao_estrutura_tipo     = raub.cd_regulamento_alteracao_estrutura_tipo) = 1
                                     THEN 'Parágrafo único. '
                                     ELSE '§ '
                                 END)
                     END) AS ds_simbolo_texto,
                     (CASE WHEN (SELECT raub4.nr_ordem
                                   FROM gestao.regulamento_alteracao_unidade_basica raub4
                                  WHERE raub4.cd_regulamento_alteracao_unidade_basica = raub.cd_regulamento_alteracao_unidade_basica_referencia) != raub.nr_ordem
                           THEN 'S'
                           WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 5 AND 

                                (CASE WHEN (SELECT COUNT(*) 
                                             FROM gestao.regulamento_alteracao_unidade_basica raub2 
                                            WHERE raub2.dt_exclusao IS NULL 
                                              AND raub2.dt_removido IS NULL
                                              AND raub2.cd_regulamento_alteracao_unidade_basica_pai = raub.cd_regulamento_alteracao_unidade_basica_pai
                                              AND raub2.cd_regulamento_alteracao_estrutura_tipo     = raub.cd_regulamento_alteracao_estrutura_tipo) = 1
                                      THEN 'Parágrafo único. '
                                      ELSE '§ '
                                 END)

                                !=

                                (SELECT (CASE WHEN (SELECT COUNT(*) 
                                                      FROM gestao.regulamento_alteracao_unidade_basica raub5 
                                                     WHERE raub5.dt_exclusao IS NULL 
                                                       AND raub5.dt_removido IS NULL
                                                       AND raub5.cd_regulamento_alteracao_unidade_basica_pai = raub4.cd_regulamento_alteracao_unidade_basica_pai
                                                       AND raub5.cd_regulamento_alteracao_estrutura_tipo     = raub4.cd_regulamento_alteracao_estrutura_tipo) = 1
                                              THEN 'Parágrafo único. '
                                              ELSE '§ '
                                         END)
                                   FROM gestao.regulamento_alteracao_unidade_basica raub4
                                  WHERE raub4.cd_regulamento_alteracao_unidade_basica = raub.cd_regulamento_alteracao_unidade_basica_referencia)
                           THEN 'S'
                           WHEN raub.cd_regulamento_alteracao_unidade_basica_referencia IS NULL
                           THEN 'S'
                           ELSE 'N'
                     END) AS fl_alteracao_ordem,
                     (CASE WHEN (SELECT TRIM(funcoes.strip_tags(raub4.ds_regulamento_alteracao_unidade_basica))
                                   FROM gestao.regulamento_alteracao_unidade_basica raub4
                                  WHERE raub4.cd_regulamento_alteracao_unidade_basica = raub.cd_regulamento_alteracao_unidade_basica_referencia) != TRIM(funcoes.strip_tags(raub.ds_regulamento_alteracao_unidade_basica))
                           THEN 'S'
                           WHEN raub.cd_regulamento_alteracao_unidade_basica_referencia IS NULL
                           THEN 'S'
                           ELSE 'N'
                   END) AS fl_alteracao_texto
                FROM gestao.regulamento_alteracao_unidade_basica raub
                JOIN gestao.regulamento_alteracao_estrutura rae
                  ON rae.cd_regulamento_alteracao_estrutura = raub.cd_regulamento_alteracao_estrutura
                JOIN gestao.regulamento_alteracao_estrutura_tipo raet
                  ON raet.cd_regulamento_alteracao_estrutura_tipo = raub.cd_regulamento_alteracao_estrutura_tipo
                JOIN gestao.regulamento_alteracao_estrutura_tipo raet2
                  ON raet2.cd_regulamento_alteracao_estrutura_tipo = rae.cd_regulamento_alteracao_estrutura_tipo
               WHERE raub.dt_exclusao                             IS NULL
                 ".(trim($fl_removido) == 'N' ? "AND raub.dt_removido IS NULL" : "")."
                 AND raub.cd_regulamento_alteracao                = ".intval($cd_regulamento_alteracao)."
                 AND raub.cd_regulamento_alteracao_unidade_basica != ".intval($cd_regulamento_alteracao_unidade_basica)."
                 AND cd_regulamento_alteracao_unidade_basica_pai  = ".intval($cd_regulamento_alteracao_unidade_basica_pai)."
               ORDER BY raet.nr_ordem ASC, raub.nr_ordem ASC, nr_ordem_removido ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_ultimo_tipo_unidade($cd_regulamento_alteracao_unidade_basica_pai)
	{
		$qr_sql = "
			SELECT cd_regulamento_alteracao_estrutura_tipo
			  FROM gestao.regulamento_alteracao_unidade_basica
			 WHERE dt_exclusao                             IS NULL
			   AND cd_regulamento_alteracao_unidade_basica_pai = ".intval($cd_regulamento_alteracao_unidade_basica_pai)."
			 ORDER BY dt_inclusao DESC
			 LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar_referenciado(
		$cd_regulamento_alteracao, 
		$cd_regulamento_alteracao_unidade_basica, 
		$cd_regulamento_alteracao_unidade_basica_referenciado, 
		$cd_usuario, 
		$cd_regulamento_alteracao_unidade_basica_ref_referencia = 0)
	{
		$qr_sql = "
			INSERT INTO gestao.regulamento_alteracao_unidade_basica_ref
			     (
            		cd_regulamento_alteracao,
            		cd_regulamento_alteracao_unidade_basica,
            		cd_regulamento_alteracao_unidade_basica_referenciado,
            		cd_regulamento_alteracao_unidade_basica_ref_referencia,
            		cd_usuario_inclusao, 
            		cd_usuario_alteracao
            	 )
    		VALUES 
    		     (
    		     	".intval($cd_regulamento_alteracao).",
    		     	".intval($cd_regulamento_alteracao_unidade_basica).",
    		     	".intval($cd_regulamento_alteracao_unidade_basica_referenciado).",
    		     	".(intval($cd_regulamento_alteracao_unidade_basica_ref_referencia) > 0 ? intval($cd_regulamento_alteracao_unidade_basica_ref_referencia) : "DEFAULT").",
    		     	".intval($cd_usuario).",
    		     	".intval($cd_usuario)."
    		     );";

		$this->db->query($qr_sql);
    }
    
    public function salvar_referenciado_estrutura(
		$cd_regulamento_alteracao, 
		$cd_regulamento_alteracao_unidade_basica, 
		$cd_regulamento_alteracao_estrutura_referenciado, 
		$cd_usuario, 
		$cd_regulamento_alteracao_unidade_basica_estrutura_ref_referenci = 0)
	{
		$qr_sql = "
			INSERT INTO gestao.regulamento_alteracao_unidade_basica_estrutura_ref
			     (
            		cd_regulamento_alteracao,
            		cd_regulamento_alteracao_unidade_basica,
            		cd_regulamento_alteracao_estrutura_referenciado,
            		cd_regulamento_alteracao_unidade_basica_estrutura_ref_referenci,
            		cd_usuario_inclusao, 
            		cd_usuario_alteracao
            	 )
    		VALUES 
    		     (
    		     	".intval($cd_regulamento_alteracao).",
    		     	".intval($cd_regulamento_alteracao_unidade_basica).",
    		     	".intval($cd_regulamento_alteracao_estrutura_referenciado).",
    		     	".(intval($cd_regulamento_alteracao_unidade_basica_estrutura_ref_referenci) > 0 ? intval($cd_regulamento_alteracao_unidade_basica_estrutura_ref_referenci) : "DEFAULT").",
    		     	".intval($cd_usuario).",
    		     	".intval($cd_usuario)."
    		     );";

		$this->db->query($qr_sql);
    }
    
    public function exclui_referenciado_estrutura($cd_regulamento_alteracao_unidade_basica, $cd_regulamento_alteracao_estrutura_referenciado, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.regulamento_alteracao_unidade_basica_estrutura_ref
			   SET cd_usuario_exclusao = ".intval($cd_usuario).", 
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_regulamento_alteracao_unidade_basica              = ".intval($cd_regulamento_alteracao_unidade_basica)."
			   AND cd_regulamento_alteracao_estrutura_referenciado      = ".intval($cd_regulamento_alteracao_estrutura_referenciado)."
			   AND dt_exclusao                                          IS NULL;";

		$this->db->query($qr_sql);
	}

	public function exclui_referenciado($cd_regulamento_alteracao_unidade_basica, $cd_regulamento_alteracao_unidade_basica_referenciado, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.regulamento_alteracao_unidade_basica_ref
			   SET cd_usuario_exclusao = ".intval($cd_usuario).", 
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_regulamento_alteracao_unidade_basica              = ".intval($cd_regulamento_alteracao_unidade_basica)."
			   AND cd_regulamento_alteracao_unidade_basica_referenciado = ".intval($cd_regulamento_alteracao_unidade_basica_referenciado)."
			   AND dt_exclusao                                          IS NULL;";

		$this->db->query($qr_sql);
	}

	public function get_unidade_basica_referenciado($cd_regulamento_alteracao_unidade_basica)
	{
		$qr_sql = "
			SELECT cd_regulamento_alteracao_unidade_basica_referenciado
			  FROM gestao.regulamento_alteracao_unidade_basica_ref
			 WHERE dt_exclusao                             IS NULL
			   AND cd_regulamento_alteracao_unidade_basica = ".intval($cd_regulamento_alteracao_unidade_basica).";";

		return $this->db->query($qr_sql)->result_array();
    }

    public function get_estrutura_unidade_basica_referenciado($cd_regulamento_alteracao_unidade_basica)
	{
		$qr_sql = "
			SELECT cd_regulamento_alteracao_estrutura_referenciado
			  FROM gestao.regulamento_alteracao_unidade_basica_estrutura_ref
			 WHERE dt_exclusao                             IS NULL
			   AND cd_regulamento_alteracao_unidade_basica = ".intval($cd_regulamento_alteracao_unidade_basica).";";

		return $this->db->query($qr_sql)->result_array();
    }
    
    public function carrega_referencia($cd_regulamento_alteracao_referencia)
    {
        $qr_sql = "
            SELECT raubr.cd_regulamento_alteracao_unidade_basica_ref, 
                   raubr.cd_regulamento_alteracao_unidade_basica, 
                   raubr.cd_regulamento_alteracao_unidade_basica_referenciado
              FROM gestao.regulamento_alteracao_unidade_basica_ref raubr 
              JOIN gestao.regulamento_alteracao_unidade_basica raub
                ON raub.cd_regulamento_alteracao_unidade_basica = raubr.cd_regulamento_alteracao_unidade_basica
             WHERE raub.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao_referencia)."
               AND raubr.dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_referencia_estrutura($cd_regulamento_alteracao_referencia)
    {
        $qr_sql = "               
               SELECT rauber.cd_regulamento_alteracao_unidade_basica_estrutura_ref,
                      rauber.cd_regulamento_alteracao_unidade_basica,
                      rauber.cd_regulamento_alteracao_unidade_basica_estrutura_ref_referenci,
                      rauber.cd_regulamento_alteracao_estrutura_referenciado
                 FROM gestao.regulamento_alteracao_unidade_basica_estrutura_ref rauber
                 JOIN gestao.regulamento_alteracao_unidade_basica raub
                   ON raub.cd_regulamento_alteracao_unidade_basica = rauber.cd_regulamento_alteracao_unidade_basica
                WHERE raub.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao_referencia)."
                  AND rauber.dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_estrutura_unidade_basica_referencia($args = array())
    {
		$qr_sql = "
            INSERT INTO gestao.regulamento_alteracao_unidade_basica_ref
                 (
                    cd_regulamento_alteracao, 
                    cd_regulamento_alteracao_unidade_basica, 
                    cd_regulamento_alteracao_unidade_basica_referenciado, 
                    cd_regulamento_alteracao_unidade_basica_ref_referencia, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES  
                 (
                    ".intval($args['cd_regulamento_alteracao']).",
                    ".intval($args['cd_regulamento_alteracao_unidade_basica']).",
                    ".intval($args['cd_regulamento_alteracao_unidade_basica_referenciado']).",
                    ".intval($args['cd_regulamento_alteracao_unidade_basica_ref_referencia']).",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

		$this->db->query($qr_sql);
    }

    public function salvar_referencia_estrutura($args = array())
    {
        $qr_sql = "
            INSERT INTO gestao.regulamento_alteracao_unidade_basica_estrutura_ref
                 (
                    cd_regulamento_alteracao, 
                    cd_regulamento_alteracao_unidade_basica, 
                    cd_regulamento_alteracao_estrutura_referenciado, 
                    cd_regulamento_alteracao_unidade_basica_estrutura_ref_referenci, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES  
                 (
                    ".intval($args['cd_regulamento_alteracao']).",
                    ".intval($args['cd_regulamento_alteracao_unidade_basica']).",
                    ".intval($args['cd_regulamento_alteracao_estrutura_referenciado']).",
                    ".intval($args['cd_regulamento_alteracao_unidade_basica_estrutura_ref_referenci']).",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

		$this->db->query($qr_sql);
    }

    public function get_estrutura_referencia($cd_regulamento_alteracao_estrutura_referencia)
    {
        $qr_sql = "
            SELECT rae.ds_regulamento_alteracao_estrutura,
                   raet.ds_regulamento_alteracao_estrutura_tipo || ' ' || TO_CHAR(rae.nr_ordem, 'FMRN') AS ds_tipo
              FROM gestao.regulamento_alteracao_estrutura rae
              JOIN gestao.regulamento_alteracao_estrutura_tipo raet
                ON raet.cd_regulamento_alteracao_estrutura_tipo = rae.cd_regulamento_alteracao_estrutura_tipo
             WHERE rae.cd_regulamento_alteracao_estrutura = ".intval($cd_regulamento_alteracao_estrutura_referencia).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_unida_basica_referencia($cd_regulamento_alteracao_unidade_basica_referencia)
    {
        $qr_sql = "
            SELECT (CASE WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 7
                         THEN SUBSTR('abcdefghijklmnopqrstuvwxyz', raub.nr_ordem, 1) || ') '
                         WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 8
                         THEN raub.nr_ordem || '. '
                         WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 6
                         THEN TO_CHAR(raub.nr_ordem, 'FMRN') || ' - '
                         WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 5
                         THEN (CASE WHEN (SELECT COUNT(*) 
                                            FROM gestao.regulamento_alteracao_unidade_basica raub2 
                                           WHERE raub2.dt_exclusao IS NULL 
                                             AND raub2.dt_removido IS NULL
                                             AND raub2.cd_regulamento_alteracao_unidade_basica_pai = raub.cd_regulamento_alteracao_unidade_basica_pai
                                             AND raub2.cd_regulamento_alteracao_estrutura_tipo     = raub.cd_regulamento_alteracao_estrutura_tipo) = 1
                                    THEN 'Parágrafo único. '
                                    ELSE '§ ' || raub.nr_ordem || (CASE WHEN raub.nr_ordem <= 9 THEN 'º' ELSE '.' END)
                               END) || ' '
                         WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 4
                         THEN 'Art. ' || raub.nr_ordem || (CASE WHEN raub.nr_ordem <= 9 THEN 'º ' ELSE '. ' END)
                         ELSE ''
                   END) || raub.ds_regulamento_alteracao_unidade_basica AS ds_unidade_basica
              FROM gestao.regulamento_alteracao_unidade_basica raub
              JOIN gestao.regulamento_alteracao_estrutura_tipo raet
                ON raet.cd_regulamento_alteracao_estrutura_tipo = raub.cd_regulamento_alteracao_estrutura_tipo
             WHERE raub.cd_regulamento_alteracao_unidade_basica = ".intval($cd_regulamento_alteracao_unidade_basica_referencia).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function iniciar_quadro_comparativo($cd_regulamento_alteracao, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao
               SET cd_usuario_inicio_quadro_comparativo = ".intval($cd_usuario).", 
                   dt_inicio_quadro_comparativo         = CURRENT_TIMESTAMP
             WHERE cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao).";";

        $this->db->query($qr_sql);
    }

    public function salvar_quadro_comparativo($args = array())
    {
        $cd_regulamento_alteracao_quadro_comparativo = intval($this->db->get_new_id('gestao.regulamento_alteracao_quadro_comparativo', 'cd_regulamento_alteracao_quadro_comparativo'));

        $qr_sql = "
            INSERT INTO gestao.regulamento_alteracao_quadro_comparativo
                 (
                    cd_regulamento_alteracao_quadro_comparativo,
                    cd_regulamento_alteracao, 
                    cd_regulamento_alteracao_unidade_basica,
                    nr_ordem, 
                    ds_texto_anterior, 
                    ds_texto_atual, 
                    ds_justificativa, 
                    tp_align_anterior, 
                    tp_align_atual, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".intval($cd_regulamento_alteracao_quadro_comparativo).",
                    ".intval($args['cd_regulamento_alteracao']).",
                    ".(intval($args['cd_regulamento_alteracao_unidade_basica']) > 0 ? intval($args['cd_regulamento_alteracao_unidade_basica']) : "DEFAULT").",
                    ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                    ".(trim($args['ds_texto_anterior']) != '' ? str_escape($args['ds_texto_anterior']) : "DEFAULT").",
                    ".(trim($args['ds_texto_atual']) != '' ? str_escape($args['ds_texto_atual']) : "DEFAULT").",
                    ".(trim($args['ds_justificativa']) != '' ? str_escape($args['ds_justificativa']) : "DEFAULT").",
                    ".(trim($args['tp_align_anterior']) != '' ? "'".trim($args['tp_align_anterior'])."'" : "DEFAULT").",
                    ".(trim($args['tp_align_atual']) != '' ? "'".trim($args['tp_align_atual'])."'" : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);

        return $cd_regulamento_alteracao_quadro_comparativo;
    }

    public function atualizar_quadro_comparativo($cd_regulamento_alteracao_quadro_comparativo, $args = array())
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_quadro_comparativo
               SET ds_texto_anterior    = ".(trim($args['ds_texto_anterior']) != '' ? str_escape($args['ds_texto_anterior']) : "DEFAULT").",
                   ds_texto_atual       = ".(trim($args['ds_texto_atual']) != '' ? str_escape($args['ds_texto_atual']) : "DEFAULT").",
                   ds_justificativa     = ".(trim($args['ds_justificativa']) != '' ? str_escape($args['ds_justificativa']) : "DEFAULT").",
                   tp_align_anterior    = ".(trim($args['tp_align_anterior']) != '' ? "'".trim($args['tp_align_anterior'])."'" : "DEFAULT").",
                   tp_align_atual       = ".(trim($args['tp_align_atual']) != '' ? "'".trim($args['tp_align_atual'])."'" : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_regulamento_alteracao_quadro_comparativo = ".intval($cd_regulamento_alteracao_quadro_comparativo).";";

        $this->db->query($qr_sql);
    }

    public function listar_quadro_comparativo($cd_regulamento_alteracao)
    {
        $qr_sql = "
            SELECT raqc.cd_regulamento_alteracao_quadro_comparativo,
             	   raqc.cd_regulamento_alteracao_unidade_basica,
                   raqc.nr_ordem, 
                   raqc.ds_texto_anterior, 
                   raqc.ds_texto_atual, 
                   raqc.ds_justificativa, 
                   raqc.tp_align_anterior, 
                   raqc.tp_align_atual,
                   TO_CHAR(raa.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio
              FROM gestao.regulamento_alteracao_quadro_comparativo raqc
              LEFT JOIN gestao.regulamento_alteracao_atividade raa
                ON raa.cd_regulamento_alteracao_unidade_basica = raqc.cd_regulamento_alteracao_unidade_basica
             WHERE raqc.dt_exclusao IS NULL
               AND raqc.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao)."
			   AND raa.dt_exclusao IS NULL
             ORDER BY raqc.nr_ordem;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_atividade_quadro_comparativo($cd_regulamento_alteracao)
    {
    	$qr_sql = "
			SELECT COUNT(*) AS qt_atividade_cadastrada,
			       (SELECT COUNT(*)
			          FROM gestao.regulamento_alteracao_atividade raa2
			          JOIN gestao.regulamento_alteracao_quadro_comparativo raqc2
			            ON raqc2.cd_regulamento_alteracao_unidade_basica = raa2.cd_regulamento_alteracao_unidade_basica
			         WHERE raa2.dt_exclusao IS NULL
			           AND raa2.dt_envio IS NOT NULL
			           AND raqc2.dt_exclusao IS NULL
			           AND raqc2.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao).") AS qt_atividade_encaminhada
			  FROM gestao.regulamento_alteracao_atividade raa
			  JOIN gestao.regulamento_alteracao_quadro_comparativo raqc
			    ON raqc.cd_regulamento_alteracao_unidade_basica = raa.cd_regulamento_alteracao_unidade_basica
			 WHERE raa.dt_exclusao IS NULL
			   AND raqc.dt_exclusao IS NULL
			   AND raqc.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao).";";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function get_next_ordem_quadro_comparativo($cd_regulamento_alteracao)
    {
        $qr_sql = "
            SELECT (COALESCE(MAX(nr_ordem), 0) + 1) AS nr_ordem
              FROM gestao.regulamento_alteracao_quadro_comparativo
             WHERE dt_exclusao              IS NULL
               AND cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function carrega_quadro_comparativo($cd_regulamento_alteracao_quadro_comparativo)
    {
        $qr_sql = "
            SELECT cd_regulamento_alteracao_quadro_comparativo,
                   nr_ordem, 
                   ds_texto_anterior, 
                   ds_texto_atual, 
                   ds_justificativa, 
                   tp_align_anterior, 
                   tp_align_atual
              FROM gestao.regulamento_alteracao_quadro_comparativo
             WHERE dt_exclusao IS NULL
               AND cd_regulamento_alteracao_quadro_comparativo = ".intval($cd_regulamento_alteracao_quadro_comparativo).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function verifica_ordem_quadro_comparativo($cd_regulamento_alteracao, $nr_ordem, $ds_operador = '=')
    {
        $qr_sql = "
            SELECT (CASE WHEN COUNT(*) > 0 THEN 'S' ELSE 'N' END) AS fl_ordem
              FROM gestao.regulamento_alteracao_quadro_comparativo
             WHERE dt_exclusao IS NULL
               AND cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao)."
               AND nr_ordem                 ".trim($ds_operador)." ".intval($nr_ordem).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function atuliza_quadro_comparativo_renumeracao($cd_regulamento_alteracao_quadro_comparativo, $args = array(), $ds_operador = '+')
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_quadro_comparativo AS t
               SET nr_ordem             = x.nr_ordem,
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
              FROM (SELECT cd_regulamento_alteracao_quadro_comparativo,
                           (nr_ordem ".trim($ds_operador)."1) AS nr_ordem
                      FROM gestao.regulamento_alteracao_quadro_comparativo
                     WHERE dt_exclusao                                IS NULL
                       AND cd_regulamento_alteracao                    = ".intval($args['cd_regulamento_alteracao'])."
                       AND nr_ordem                                    >= ".intval($args['nr_ordem'])."
                       AND cd_regulamento_alteracao_quadro_comparativo != ".intval($cd_regulamento_alteracao_quadro_comparativo)."
                     ORDER BY nr_ordem) x
             WHERE t.cd_regulamento_alteracao_quadro_comparativo = x.cd_regulamento_alteracao_quadro_comparativo;";

        $this->db->query($qr_sql); 
    }

    public function excluir_quadro_comparativo($cd_regulamento_alteracao_quadro_comparativo, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_quadro_comparativo 
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_regulamento_alteracao_quadro_comparativo = ".intval($cd_regulamento_alteracao_quadro_comparativo).";";

        $this->db->query($qr_sql); 
    }

    public function finalizar_quadro_comparativo($cd_regulamento_alteracao, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao
               SET cd_usuario_fim_quadro_comparativo = ".intval($cd_usuario).", 
                   dt_fim_quadro_comparativo         = CURRENT_TIMESTAMP
             WHERE cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao).";";

        $this->db->query($qr_sql);
    }

    public function get_unidade_basica_ref($cd_regulamento_alteracao)
    {
        $qr_sql = "
            SELECT ubr.cd_regulamento_alteracao_unidade_basica_ref AS cd_ref,
                   'U' AS fl_tipo,
                   TO_CHAR(ubr.dt_verificado, 'DD/MM/YYYY HH24:MI:SS') AS dt_verificado,
                   ubr.cd_regulamento_alteracao_unidade_basica,
                   ubr.cd_regulamento_alteracao_unidade_basica_referenciado,
                   0 AS cd_regulamento_alteracao_estrutura_referenciado
              FROM gestao.regulamento_alteracao_unidade_basica_ref ubr
              JOIN gestao.regulamento_alteracao_unidade_basica raub
                ON raub.cd_regulamento_alteracao_unidade_basica = ubr.cd_regulamento_alteracao_unidade_basica
               AND raub.dt_removido IS NULL
             WHERE ubr.dt_exclusao IS NULL
               AND ubr.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao)."
             UNION 
            SELECT uber.cd_regulamento_alteracao_unidade_basica_estrutura_ref AS cd_ref,
                   'E' AS fl_tipo,
                   TO_CHAR(uber.dt_verificado, 'DD/MM/YYYY HH24:MI:SS') AS dt_verificado,
                   uber.cd_regulamento_alteracao_unidade_basica,
                   0 AS cd_regulamento_alteracao_unidade_basica_referenciado,
                   uber.cd_regulamento_alteracao_estrutura_referenciado
              FROM gestao.regulamento_alteracao_unidade_basica_estrutura_ref uber         
              JOIN gestao.regulamento_alteracao_unidade_basica raub
                ON raub.cd_regulamento_alteracao_unidade_basica = uber.cd_regulamento_alteracao_unidade_basica
               AND raub.dt_removido IS NULL
             WHERE uber.dt_exclusao IS NULL
               AND uber.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao).";";

        return $this->db->query($qr_sql)->result_array();
    }

    /*
    public function get_unidade_basica_ref($cd_regulamento_alteracao)
    {
        $qr_sql = "
            SELECT *
              FROM (
                SELECT ubr.cd_regulamento_alteracao_unidade_basica,
                       ubr.cd_regulamento_alteracao_unidade_basica_referenciado,
                       0 AS cd_regulamento_alteracao_estrutura_referenciado,
                       (SELECT (CASE WHEN (SELECT raub4.nr_ordem
                                             FROM gestao.regulamento_alteracao_unidade_basica raub4
                                            WHERE raub4.cd_regulamento_alteracao_unidade_basica = raub.cd_regulamento_alteracao_unidade_basica_referencia) != raub.nr_ordem
                                     THEN 'S'
                                     WHEN raub.cd_regulamento_alteracao_estrutura_tipo = 5 AND 

                                          (CASE WHEN (SELECT COUNT(*) 
                                                        FROM gestao.regulamento_alteracao_unidade_basica raub2 
                                                       WHERE raub2.dt_exclusao IS NULL 
                                                         AND raub2.dt_removido IS NULL
                                                         AND raub2.cd_regulamento_alteracao_unidade_basica_pai = raub.cd_regulamento_alteracao_unidade_basica_pai
                                                         AND raub2.cd_regulamento_alteracao_estrutura_tipo     = raub.cd_regulamento_alteracao_estrutura_tipo) = 1
                                                THEN 'Parágrafo único. '
                                                ELSE '§ '
                                          END)

                                          !=

                                          (SELECT (CASE WHEN (SELECT COUNT(*) 
                                                                FROM gestao.regulamento_alteracao_unidade_basica raub5 
                                                               WHERE raub5.dt_exclusao IS NULL 
                                                                 AND raub5.dt_removido IS NULL
                                                                 AND raub5.cd_regulamento_alteracao_unidade_basica_pai = raub4.cd_regulamento_alteracao_unidade_basica_pai
                                                                 AND raub5.cd_regulamento_alteracao_estrutura_tipo     = raub4.cd_regulamento_alteracao_estrutura_tipo) = 1
                                                        THEN 'Parágrafo único. '
                                                        ELSE '§ '
                                                   END)
                                              FROM gestao.regulamento_alteracao_unidade_basica raub4
                                             WHERE raub4.cd_regulamento_alteracao_unidade_basica = raub.cd_regulamento_alteracao_unidade_basica_referencia)
                                   THEN 'S'
                                   ELSE 'N'
                             END) AS fl_alteracao_ordem
                        FROM gestao.regulamento_alteracao_unidade_basica raub
                       WHERE raub.cd_regulamento_alteracao_unidade_basica = ubr.cd_regulamento_alteracao_unidade_basica_referenciado)
                  FROM gestao.regulamento_alteracao_unidade_basica_ref ubr
                 WHERE ubr.dt_exclusao IS NULL
                   AND ubr.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao)."
                 UNION 
                SELECT uber.cd_regulamento_alteracao_unidade_basica,
                       0 AS cd_regulamento_alteracao_unidade_basica_referenciado,
                       cd_regulamento_alteracao_estrutura_referenciado,
                       (SELECT (CASE WHEN (SELECT rae2.nr_ordem
                                             FROM gestao.regulamento_alteracao_estrutura rae2
                                            WHERE rae2.dt_exclusao IS NULL 
                                              AND rae2.dt_removido IS NULL
                                              AND rae2.cd_regulamento_alteracao_estrutura = rae.cd_regulamento_alteracao_estrutura_referencia) != rae.nr_ordem
                                 THEN 'S'
                                 ELSE 'N'
                               END) AS fl_alteracao_ordem
                          FROM gestao.regulamento_alteracao_estrutura rae
                         WHERE rae.cd_regulamento_alteracao_estrutura = uber.cd_regulamento_alteracao_estrutura_referenciado)
                  FROM gestao.regulamento_alteracao_unidade_basica_estrutura_ref uber
                 WHERE uber.dt_exclusao IS NULL
                   AND uber.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao)."
                 ) x
             ORDER BY x.cd_regulamento_alteracao_unidade_basica;";

        return $this->db->query($qr_sql)->result_array();
    }
    */

    public function get_alteracao_revisao($cd_regulamento_alteracao, $cd_regulamento_alteracao_revisao_pai = 0)
    {
        $qr_sql = "
            SELECT rar.cd_regulamento_alteracao_revisao,
                   rar.ds_regulamento_alteracao_revisao,
                   rar.ds_descricao,
                   rar.nr_ordem,
                   TO_CHAR(rar.dt_verificado, 'DD/MM/YYYY HH24:MI:SS') AS dt_verificado,
                   (SELECT COUNT (*)
                      FROM gestao.regulamento_alteracao_revisao rar2
                     WHERE rar2.dt_exclusao IS NULL
                       AND rar2.cd_regulamento_alteracao_revisao_pai = rar.cd_regulamento_alteracao_revisao) AS tl_filho
              FROM gestao.regulamento_alteracao_revisao rar
             WHERE rar.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao)."
               AND rar.dt_exclusao              IS NULL
               ".(intval($cd_regulamento_alteracao_revisao_pai) > 0 ? "AND rar.cd_regulamento_alteracao_revisao_pai = ".intval($cd_regulamento_alteracao_revisao_pai) : "AND rar.cd_regulamento_alteracao_revisao_pai IS NULL")."
             ORDER BY rar.nr_ordem;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function atualiza_alteracao_revisao($cd_regulamento_alteracao_revisao, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_revisao
               SET cd_usuario_verificado = ".intval($cd_usuario).",
                   dt_verificado         = CURRENT_TIMESTAMP
             WHERE cd_regulamento_alteracao_revisao = ".intval($cd_regulamento_alteracao_revisao).";";

        $this->db->query($qr_sql);
    }

    public function atualiza_alteracao_revisao_pai($cd_regulamento_alteracao, $cd_regulamento_alteracao_revisao, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_revisao t
               SET cd_usuario_verificado = ".intval($cd_usuario).",
                   dt_verificado         = CURRENT_TIMESTAMP
              FROM (SELECT rar.cd_regulamento_alteracao_revisao_pai
                      FROM gestao.regulamento_alteracao_revisao rar
                     WHERE rar.cd_regulamento_alteracao_revisao = ".intval($cd_regulamento_alteracao_revisao)."
                       AND (SELECT COUNT(*)
                              FROM gestao.regulamento_alteracao_revisao rar2
                             WHERE rar2.cd_regulamento_alteracao_revisao_pai = rar.cd_regulamento_alteracao_revisao_pai
                               AND dt_exclusao   IS NULL
                               AND dt_verificado IS NULL) = 0) x
             WHERE t.cd_regulamento_alteracao_revisao = x.cd_regulamento_alteracao_revisao_pai
               AND t.dt_verificado IS NULL
               AND t.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao).";";

        $this->db->query($qr_sql);
    }

    public function excluir_alteracao_revisao($cd_regulamento_alteracao_revisao)
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_revisao
               SET cd_usuario_verificado = NULL,
                   dt_verificado         = NULL
             WHERE cd_regulamento_alteracao_revisao = ".intval($cd_regulamento_alteracao_revisao).";

            UPDATE gestao.regulamento_alteracao_revisao
               SET cd_usuario_verificado = NULL,
                   dt_verificado         = NULL
             WHERE dt_verificado IS NOT NULL
               AND cd_regulamento_alteracao_revisao = (SELECT cd_regulamento_alteracao_revisao_pai
                                                         FROM gestao.regulamento_alteracao_revisao
                                                        WHERE dt_exclusao IS NULL
                                                          AND cd_regulamento_alteracao_revisao = ".intval($cd_regulamento_alteracao_revisao).");";

        $this->db->query($qr_sql);
    }

    public function carrega_alteracao_revisao($cd_regulamento_alteracao_revisao)
    {
        $qr_sql = "
            SELECT rar.cd_regulamento_alteracao_revisao,
                   rar.cd_regulamento_alteracao_revisao_pai,
                   rar.ds_regulamento_alteracao_revisao,
                   rar.ds_descricao,
                   rar.nr_ordem,
                   TO_CHAR(rar.dt_verificado, 'DD/MM/YYYY HH24:MI:SS') AS dt_verificado,
                   (SELECT COUNT (*)
                      FROM gestao.regulamento_alteracao_revisao rar2
                     WHERE rar2.dt_exclusao IS NULL
                       AND rar2.cd_regulamento_alteracao_revisao_pai = rar.cd_regulamento_alteracao_revisao) AS tl_filho,
                   (SELECT TO_CHAR(rar2.dt_verificado, 'DD/MM/YYYY HH24:MI:SS')
                      FROM gestao.regulamento_alteracao_revisao rar2
                     WHERE rar2.dt_exclusao IS NULL
                       AND rar2.cd_regulamento_alteracao_revisao = rar.cd_regulamento_alteracao_revisao_pai) AS dt_verificado_pai
              FROM gestao.regulamento_alteracao_revisao rar
             WHERE rar.cd_regulamento_alteracao_revisao = ".intval($cd_regulamento_alteracao_revisao).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function atualiza_alteracao_unidade_basica_ref($cd_regulamento_alteracao_unidade_basica_ref, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_unidade_basica_ref
               SET cd_usuario_verificado = ".intval($cd_usuario).",
                   dt_verificado         = CURRENT_TIMESTAMP
             WHERE cd_regulamento_alteracao_unidade_basica_ref = ".intval($cd_regulamento_alteracao_unidade_basica_ref).";";

        $this->db->query($qr_sql);
    }

    public function excluir_alteracao_unidade_basica_ref($cd_regulamento_alteracao_unidade_basica_ref)
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_unidade_basica_ref
               SET cd_usuario_verificado = NULL,
                   dt_verificado         = NULL
             WHERE cd_regulamento_alteracao_unidade_basica_ref = ".intval($cd_regulamento_alteracao_unidade_basica_ref).";";

        $this->db->query($qr_sql);
    }

    public function carrega_alteracao_unidade_basica_ref($cd_regulamento_alteracao_unidade_basica_ref)
    {
        $qr_sql = "
            SELECT TO_CHAR(rar.dt_verificado, 'DD/MM/YYYY HH24:MI:SS') AS dt_verificado
              FROM gestao.regulamento_alteracao_unidade_basica_ref rar
             WHERE rar.cd_regulamento_alteracao_unidade_basica_ref = ".intval($cd_regulamento_alteracao_unidade_basica_ref).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function atualiza_alteracao_estrutura_ref($cd_regulamento_alteracao_unidade_basica_estrutura_ref, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_unidade_basica_estrutura_ref
               SET cd_usuario_verificado = ".intval($cd_usuario).",
                   dt_verificado         = CURRENT_TIMESTAMP
             WHERE cd_regulamento_alteracao_unidade_basica_estrutura_ref = ".intval($cd_regulamento_alteracao_unidade_basica_estrutura_ref).";";

        $this->db->query($qr_sql);
    }

    public function excluir_alteracao_estrutura_ref($cd_regulamento_alteracao_unidade_basica_estrutura_ref)
    {
        $qr_sql = "
            UPDATE gestao.regulamento_alteracao_unidade_basica_estrutura_ref
               SET cd_usuario_verificado = NULL,
                   dt_verificado         = NULL
             WHERE cd_regulamento_alteracao_unidade_basica_estrutura_ref = ".intval($cd_regulamento_alteracao_unidade_basica_estrutura_ref).";";

        $this->db->query($qr_sql);
    }

    public function carrega_alteracao_estrutura_ref($cd_regulamento_alteracao_unidade_basica_estrutura_ref)
    {
        $qr_sql = "
            SELECT TO_CHAR(rar.dt_verificado, 'DD/MM/YYYY HH24:MI:SS') AS dt_verificado
              FROM gestao.regulamento_alteracao_unidade_basica_estrutura_ref rar
             WHERE rar.cd_regulamento_alteracao_unidade_basica_estrutura_ref = ".intval($cd_regulamento_alteracao_unidade_basica_estrutura_ref).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function listar_atividades($cd_regulamento_alteracao)
    {
    	$qr_sql = "
			SELECT raa.cd_regulamento_alteracao_atividade,
			       'Art. ' || raub.nr_ordem || (CASE WHEN raub.nr_ordem <= 9 THEN 'º ' ELSE '. ' END) || raub.ds_regulamento_alteracao_unidade_basica AS ds_artigo,
			       (SELECT TO_CHAR(MAX(raag.dt_prevista), 'DD/MM/YYYY')
			          FROM gestao.regulamento_alteracao_atividade_gerencia raag
			         WHERE raag.dt_exclusao IS NULL
			           AND raag.cd_regulamento_alteracao_atividade = raa.cd_regulamento_alteracao_atividade) AS dt_prevista_ref,
			       (SELECT TO_CHAR(MAX(raag.dt_implementacao), 'DD/MM/YYYY')
			          FROM gestao.regulamento_alteracao_atividade_gerencia raag
			         WHERE raag.dt_exclusao IS NULL
			           AND raag.cd_regulamento_alteracao_atividade = raa.cd_regulamento_alteracao_atividade) AS dt_implementecao_ref
			  FROM gestao.regulamento_alteracao_atividade raa
			  JOIN gestao.regulamento_alteracao_unidade_basica raub
			    ON raub.cd_regulamento_alteracao_unidade_basica = raa.cd_regulamento_alteracao_unidade_basica
			 WHERE raa.dt_exclusao IS NULL
			   AND raub.dt_exclusao IS NULL
			   AND raub.cd_regulamento_alteracao = ".intval($cd_regulamento_alteracao).";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function get_atividades($cd_regulamento_alteracao_atividade)
    {
    	$qr_sql = "
			SELECT raag.cd_regulamento_alteracao_atividade_gerencia,
			       raag.cd_regulamento_alteracao_atividade,
			       raag.cd_gerencia,
			       funcoes.get_usuario_nome(raag.cd_usuario_respondente) AS ds_usuario_respondente,
			       TO_CHAR(raag.dt_prevista, 'DD/MM/YYYY') AS dt_prevista,
			       TO_CHAR(raag.dt_implementacao, 'DD/MM/YYYY') AS dt_implementacao,
			       raat.ds_regulamento_alteracao_atividade_tipo,
			       (CASE WHEN raat.cd_regulamento_alteracao_atividade_tipo = 1
			              THEN 'label label-info'
			              WHEN raat.cd_regulamento_alteracao_atividade_tipo = 2
			              THEN 'label label-success'
			              ELSE 'label label-inverse'
			       END) AS ds_class_tipo
			  FROM gestao.regulamento_alteracao_atividade_gerencia raag
			  LEFT JOIN gestao.regulamento_alteracao_atividade_tipo raat
			    ON raat.cd_regulamento_alteracao_atividade_tipo = raag.cd_regulamento_alteracao_atividade_tipo
			 WHERE raag.dt_exclusao IS NULL
			   AND raag.cd_regulamento_alteracao_atividade = ".intval($cd_regulamento_alteracao_atividade).";";

    	return $this->db->query($qr_sql)->result_array();
    }

	public function carrega_atividade_gerencia($cd_regulamento_alteracao_atividade_gerencia)
	{
		$qr_sql = "
			SELECT raag.cd_regulamento_alteracao_atividade_gerencia,
			       raag.dt_implementacao,
			       raa.cd_regulamento_alteracao_atividade,
			       raa.cd_regulamento_alteracao_unidade_basica
			  FROM gestao.regulamento_alteracao_atividade_gerencia raag
			  JOIN gestao.regulamento_alteracao_atividade raa
			    ON raa.cd_regulamento_alteracao_atividade = raag.cd_regulamento_alteracao_atividade
			 WHERE raag.dt_exclusao IS NULL
			   AND raag.cd_regulamento_alteracao_atividade_gerencia = ".intval($cd_regulamento_alteracao_atividade_gerencia)."
			   AND raa.dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function listar_acompanhamento($cd_regulamento_alteracao_atividade_gerencia)
	{
		$qr_sql = "
			SELECT cd_regulamento_alteracao_atividade_acompanhamento,
				   ds_regulamento_alteracao_atividade_acompanhamento,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario,
				   cd_usuario_inclusao
			  FROM gestao.regulamento_alteracao_atividade_acompanhamento
			 WHERE dt_exclusao IS NULL
			   AND cd_regulamento_alteracao_atividade_gerencia = ".intval($cd_regulamento_alteracao_atividade_gerencia).";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_acompanhamento($cd_regulamento_alteracao_atividade_acompanhamento)
	{
		$qr_sql = "
			SELECT cd_regulamento_alteracao_atividade_acompanhamento,
				   ds_regulamento_alteracao_atividade_acompanhamento
			  FROM gestao.regulamento_alteracao_atividade_acompanhamento
			 WHERE dt_exclusao IS NULL
			   AND cd_regulamento_alteracao_atividade_acompanhamento = ".intval($cd_regulamento_alteracao_atividade_acompanhamento).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar_acompanhamento($args = array())
	{
		$qr_sql = "
			INSERT INTO gestao.regulamento_alteracao_atividade_acompanhamento
				(
					ds_regulamento_alteracao_atividade_acompanhamento,
					cd_regulamento_alteracao_atividade_gerencia,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				)
			VALUES
				(
					".(trim($args['ds_regulamento_alteracao_atividade_acompanhamento']) != '' ? str_escape($args['ds_regulamento_alteracao_atividade_acompanhamento']) : "DEFAULT").",
					".(intval($args['cd_regulamento_alteracao_atividade_gerencia']) > 0 ? intval($args['cd_regulamento_alteracao_atividade_gerencia']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
				);";

		$this->db->query($qr_sql);
	}

	public function atualizar_acompanhamento($cd_regulamento_alteracao_atividade_acompanhamento, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.regulamento_alteracao_atividade_acompanhamento
			   SET ds_regulamento_alteracao_atividade_acompanhamento = ".(trim($args['ds_regulamento_alteracao_atividade_acompanhamento']) != '' ? str_escape($args['ds_regulamento_alteracao_atividade_acompanhamento']) : "DEFAULT").",
				   cd_usuario_alteracao 							 = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
				   dt_alteracao 									 = CURRENT_TIMESTAMP
			 WHERE cd_regulamento_alteracao_atividade_acompanhamento = ".intval($cd_regulamento_alteracao_atividade_acompanhamento).";";

		$this->db->query($qr_sql);
	}
}

