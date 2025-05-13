<?php
class Pauta_sg_anual_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

    public function listar($args = array())
	{
		$qr_sql = "
			SELECT pa.cd_pauta_sg_anual, 
			       pa.nr_ano, 
			       pa.fl_colegiado,
			       (CASE WHEN pa.fl_colegiado = 'DE'
			             THEN 'Diretoria Executiva'
			             WHEN pa.fl_colegiado = 'CF'
			             THEN 'Conselho Fiscal'
			             ELSE 'Conselho Deliberativo'
			       END) AS ds_colegiado,
			       (CASE WHEN pa.fl_colegiado = 'DE'
			             THEN 'label label-success'
			             WHEN pa.fl_colegiado = 'CF'
			             THEN 'label label-warning'
			             ELSE 'label label-info'
			       END) AS ds_class_colegiado,
			       TO_CHAR(pa.dt_confirmacao, 'DD/MM/YYYY') AS dt_confirmacao,
			       TO_CHAR(pa.dt_limite, 'DD/MM/YYYY') AS dt_limite,
			       TO_CHAR(pa.dt_envio_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_responsavel,
			       (SELECT COUNT(*)
			          FROM gestao.pauta_sg_anual_assunto paa
			         WHERE paa.dt_exclusao    IS NULL
			           AND paa.cd_pauta_sg_anual = pa.cd_pauta_sg_anual) AS qt_assunto
              FROM gestao.pauta_sg_anual pa
             WHERE pa.dt_exclusao IS NULL
              -- AND pa.dt_envio_responsavel IS NOT NULL
             ".(intval($args['nr_ano']) > 0 ? "AND pa.nr_ano = ".intval($args['nr_ano']) : "")."
             ".(trim($args['fl_colegiado']) != '' ? "AND pa.fl_colegiado = ".str_escape($args['fl_colegiado']) : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

    public function carrega($cd_pauta_sg_anual, $cd_gerencia = '')
	{
		$qr_sql = "
			SELECT pa.cd_pauta_sg_anual,
			       pa.nr_ano,
                   pa.fl_colegiado,
			       (CASE WHEN pa.fl_colegiado = 'DE'
			             THEN 'Diretoria Executiva'
			             WHEN pa.fl_colegiado = 'CF'
			             THEN 'Conselho Fiscal'
			             ELSE 'Conselho Deliberativo'
			       END) AS ds_colegiado,
			       (CASE WHEN pa.fl_colegiado = 'DE'
			             THEN 'label label-success'
			             WHEN pa.fl_colegiado = 'CF'
			             THEN 'label label-warning'
			             ELSE 'label label-info'
			       END) AS ds_class_colegiado,
			       funcoes.get_usuario_nome(pa.cd_usuario_envio_resposanvel) AS ds_usuario_envio_resposanvel,
			       TO_CHAR(pa.dt_envio_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_responsavel,
                   TO_CHAR(pa.dt_confirmacao, 'DD/MM/YYYY') AS dt_confirmacao,
                   TO_CHAR(pa.dt_limite, 'DD/MM/YYYY') AS dt_limite,
                   (SELECT pag.dt_encerramento
                      FROM gestao.pauta_sg_anual_gerencia pag
                     WHERE pag.cd_gerencia       = '".trim($cd_gerencia)."'
                       AND pag.cd_pauta_sg_anual = ".intval($cd_pauta_sg_anual).") AS dt_encerramento
              FROM gestao.pauta_sg_anual pa
             WHERE pa.cd_pauta_sg_anual = ".intval($cd_pauta_sg_anual).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_pauta_sg_anual = intval($this->db->get_new_id('gestao.pauta_sg_anual', 'cd_pauta_sg_anual'));

		$qr_sql = "
			INSERT INTO gestao.pauta_sg_anual
			     (
            		cd_pauta_sg_anual,
            		nr_ano, 
            		fl_colegiado, 
            		dt_limite,
            		cd_usuario_inclusao, 
            		cd_usuario_alteracao
                 )
            VALUES 
                 (
                  	".intval($cd_pauta_sg_anual).",
                  	".(intval($args['nr_ano']) > 0 ? intval($args['nr_ano']) : "DEFAULT").",
                  	".(trim($args['fl_colegiado']) != '' ? "'".trim($args['fl_colegiado'])."'" : "DEFAULT").",
                  	".(trim($args['dt_limite']) != '' ? "TO_DATE('".$args['dt_limite']."', 'DD/MM/YYYY')" : "DEFAULT").",
                  	".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])." 
                 );";

        $this->db->query($qr_sql);

        return $cd_pauta_sg_anual;
	}

	public function atualizar($cd_pauta_sg_anual, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.pauta_sg_anual
               SET dt_confirmacao       = ".(trim($args['dt_confirmacao']) != '' ? "TO_DATE('".$args['dt_confirmacao']."', 'DD/MM/YYYY')" : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
			       dt_alteracao         = CURRENT_TIMESTAMP                   
             WHERE cd_pauta_sg_anual = ".intval($cd_pauta_sg_anual).";";

        $this->db->query($qr_sql);  
	}

	public function salvar_gerencia($cd_pauta_sg_anual, $cd_gerencia, $cd_usuario)
	{
		$qr_sql = "
			INSERT INTO gestao.pauta_sg_anual_gerencia
			     (
                	cd_pauta_sg_anual, 
                	cd_gerencia,
                	cd_usuario_inclusao
                 )
            VALUES 
                 (
                 	".intval($cd_pauta_sg_anual).",
                 	'".trim($cd_gerencia)."',
                 	".intval($cd_usuario)."
                 );";

        $this->db->query($qr_sql);
	}

	public function get_gerencia()
	{
		$qr_sql = "
			SELECT codigo AS value, 
                   nome AS text
              FROM funcoes.get_gerencias_vigente();";

        return $this->db->query($qr_sql)->result_array();
	}

	public function listar_assunto($cd_pauta_sg_anual)
	{
		$qr_sql = "
			SELECT paa.cd_pauta_sg_anual_assunto,
			       TO_CHAR(paa.dt_referencia, 'MM') AS mes,
			       paa.ds_assunto,
			       paa.cd_gerencia_responsavel,
			       pao.ds_pauta_sg_objetivo,
			       paj.ds_pauta_sg_justificativa,
			       paa.nr_tempo,
			       TO_CHAR(paa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       funcoes.get_usuario_nome(paa.cd_usuario_inclusao) AS ds_usuario_inclusao
			  FROM gestao.pauta_sg_anual_assunto paa
			  LEFT JOIN gestao.pauta_sg_objetivo pao
			    ON pao.cd_pauta_sg_objetivo = paa.cd_pauta_sg_objetivo
			  LEFT JOIN gestao.pauta_sg_justificativa paj
			    ON paj.cd_pauta_sg_justificativa = paa.cd_pauta_sg_justificativa
			 WHERE paa.dt_exclusao IS NULL
			   AND paa.cd_pauta_sg_anual = ".intval($cd_pauta_sg_anual)."
			 ORDER BY TO_CHAR(paa.dt_referencia, 'MM')::integer;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_assunto($cd_pauta_sg_anual_assunto)
	{
		$qr_sql = "
			SELECT cd_pauta_sg_anual,
			       cd_pauta_sg_anual_assunto,
			       TO_CHAR(dt_referencia, 'MM') AS mes,
			       ds_assunto,
			       cd_gerencia_responsavel,
			       cd_pauta_sg_objetivo,
			       cd_pauta_sg_justificativa,
			       nr_tempo
			  FROM gestao.pauta_sg_anual_assunto
			 WHERE dt_exclusao IS NULL
			   AND cd_pauta_sg_anual_assunto = ".intval($cd_pauta_sg_anual_assunto).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar_assunto($args)
	{
		$qr_sql = "
			INSERT INTO gestao.pauta_sg_anual_assunto
			     (
            		cd_pauta_sg_anual, 
            		dt_referencia, 
            		ds_assunto, 
            		cd_gerencia_responsavel,
            		cd_pauta_sg_objetivo,
            		cd_pauta_sg_justificativa,
            		nr_tempo,
            		cd_usuario_inclusao, 
            		cd_usuario_alteracao
                 )
            VALUES 
                 (
                  	".intval($args['cd_pauta_sg_anual']).",
                  	".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                  	".(trim($args['ds_assunto']) != '' ? str_escape($args['ds_assunto']) : "DEFAULT").",
                  	".(trim($args['cd_gerencia_responsavel']) != '' ? "'".trim($args['cd_gerencia_responsavel'])."'" : "DEFAULT").",
                  	".(intval($args['cd_pauta_sg_objetivo']) > 0 ? intval($args['cd_pauta_sg_objetivo']) : "DEFAULT").",
                  	".(intval($args['cd_pauta_sg_justificativa']) > 0 ? intval($args['cd_pauta_sg_justificativa']) : "DEFAULT").",
                  	".(trim($args['nr_tempo']) != '' ? intval($args['nr_tempo']): "DEFAULT").",
                  	".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])." 
                 );";

        $this->db->query($qr_sql);
	}

	public function atualizar_assunto($cd_pauta_sg_anual_assunto, $args)
	{
		$qr_sql = "
			UPDATE gestao.pauta_sg_anual_assunto
			   SET dt_referencia             = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
			       ds_assunto                = ".(trim($args['ds_assunto']) != '' ? str_escape($args['ds_assunto']) : "DEFAULT").", 
			       cd_gerencia_responsavel   = ".(trim($args['cd_gerencia_responsavel']) != '' ? "'".trim($args['cd_gerencia_responsavel'])."'" : "DEFAULT").",
			       cd_pauta_sg_objetivo      = ".(intval($args['cd_pauta_sg_objetivo']) > 0 ? intval($args['cd_pauta_sg_objetivo']) : "DEFAULT").",
			       cd_pauta_sg_justificativa = ".(intval($args['cd_pauta_sg_justificativa']) > 0 ? intval($args['cd_pauta_sg_justificativa']) : "DEFAULT").",
			       nr_tempo                  = ".(trim($args['nr_tempo']) != '' ? intval($args['nr_tempo']): "DEFAULT").",
			       cd_usuario_alteracao      = ".intval($args['cd_usuario']).",
			       dt_alteracao              = CURRENT_TIMESTAMP
			 WHERE cd_pauta_sg_anual_assunto = ".intval($cd_pauta_sg_anual_assunto).";";

		$this->db->query($qr_sql);
	}

	public function get_objetivo()
	{
		$qr_sql = "
			SELECT cd_pauta_sg_objetivo AS value,
			       ds_pauta_sg_objetivo AS text
			  FROM gestao.pauta_sg_objetivo
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_pauta_sg_objetivo";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_justificativa()
	{
		$qr_sql = "
			SELECT cd_pauta_sg_justificativa AS value,
			       ds_pauta_sg_justificativa AS text
			  FROM gestao.pauta_sg_justificativa
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_pauta_sg_justificativa";

		return $this->db->query($qr_sql)->result_array();
	}

	public function enviar($cd_pauta_sg_anual, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.pauta_sg_anual
               SET cd_usuario_envio_resposanvel = ".intval($cd_usuario).",
                   cd_usuario_alteracao         = ".intval($cd_usuario).",
			       dt_envio_responsavel         = CURRENT_TIMESTAMP,                  
			       dt_alteracao                 = CURRENT_TIMESTAMP          
             WHERE cd_pauta_sg_anual = ".intval($cd_pauta_sg_anual).";";

        $this->db->query($qr_sql);  
	}

	public function minhas_listar($cd_gerencia, $args)
	{
		$qr_sql = "
			SELECT pa.cd_pauta_sg_anual, 
			       pa.nr_ano, 
			       pa.fl_colegiado,
                   TO_CHAR(pa.dt_envio_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_responsavel,
                   TO_CHAR(pa.dt_limite, 'DD/MM/YYYY') AS dt_limite,
			       (CASE WHEN pa.fl_colegiado = 'DE'
			             THEN 'Diretoria Executiva'
			             WHEN pa.fl_colegiado = 'CF'
			             THEN 'Conselho Fiscal'
			             ELSE 'Conselho Deliberativo'
			       END) AS ds_colegiado,
			       (CASE WHEN pa.fl_colegiado = 'DE'
			             THEN 'label label-success'
			             WHEN pa.fl_colegiado = 'CF'
			             THEN 'label label-warning'
			             ELSE 'label label-info'
			       END) AS ds_class_colegiado,
			       (SELECT COUNT(*)
			          FROM gestao.pauta_sg_anual_assunto paa
			         WHERE paa.dt_exclusao    IS NULL
			           AND paa.cd_pauta_sg_anual = pa.cd_pauta_sg_anual) AS qt_assunto,
                   (SELECT COUNT(*)
                      FROM gestao.pauta_sg_anual_assunto paa2
                     WHERE paa2.dt_exclusao    IS NULL
                       AND paa2.cd_gerencia_responsavel = '".trim($cd_gerencia)."'
                       AND paa2.cd_pauta_sg_anual = pa.cd_pauta_sg_anual) AS qt_assunto_divisao
              FROM gestao.pauta_sg_anual pa
             WHERE pa.dt_exclusao IS NULL  
               AND pa.dt_envio_responsavel IS NOT NULL
               ".(intval($args['nr_ano']) > 0 ? "AND pa.nr_ano = ".intval($args['nr_ano']) : "")."
               ".(trim($args['fl_colegiado']) != '' ? "AND pa.fl_colegiado = ".str_escape($args['fl_colegiado']) : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function meus_assuntos($cd_pauta_sg_anual)
	{
		$qr_sql = "
			SELECT paa.cd_pauta_sg_anual_assunto,
			       TO_CHAR(paa.dt_referencia, 'MM') AS mes,
			       paa.ds_assunto,
			       paa.cd_gerencia_responsavel,
			       paa.cd_pauta_sg_objetivo,
			       paa.cd_pauta_sg_justificativa,
			       paa.nr_tempo,
			       pao.ds_pauta_sg_objetivo,
                   paj.ds_pauta_sg_justificativa
			  FROM gestao.pauta_sg_anual_assunto paa
			  LEFT JOIN gestao.pauta_sg_objetivo pao
			    ON pao.cd_pauta_sg_objetivo = paa.cd_pauta_sg_objetivo
			  LEFT JOIN gestao.pauta_sg_justificativa paj
                ON paj.cd_pauta_sg_justificativa = paa.cd_pauta_sg_justificativa
             WHERE paa.dt_exclusao             IS NULL
               AND paa.cd_pauta_sg_anual       = ".intval($cd_pauta_sg_anual)."
             ORDER BY TO_CHAR(paa.dt_referencia, 'MM')::integer;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function encerrar($cd_pauta_sg_anual, $cd_gerencia, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.pauta_sg_anual_gerencia
			   SET cd_usuario_encerramento  = ".intval($cd_usuario).",
			       dt_encerramento          = CURRENT_TIMESTAMP
			 WHERE cd_pauta_sg_anual = ".intval($cd_pauta_sg_anual)."
			   AND cd_gerencia       = '".trim($cd_gerencia)."';";

		$this->db->query($qr_sql);
	}

	public function get_email_reponsaveis($cd_pauta_sg_anual)
	{
		$qr_sql = "
			SELECT codigo AS cd_gerencia,
			       funcoes.get_usuario(funcoes.get_gerente(codigo)) || '@eletroceee.com.br' AS ds_email_responsavel,
			       funcoes.get_usuario(funcoes.get_substituto_gerencia(codigo)) || '@eletroceee.com.br' AS ds_email_substituto
			  FROM funcoes.get_gerencias_vigente()
			 WHERE codigo != 'DE' ;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_pauta_gerencia($cd_pauta_sg_anual)
	{
		$qr_sql = "
			SELECT cd_gerencia,
			       TO_CHAR(dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento,
			       funcoes.get_usuario_nome(cd_usuario_encerramento) AS ds_usuario_encerramento
			  FROM gestao.pauta_sg_anual_gerencia
			 WHERE cd_pauta_sg_anual = ".intval($cd_pauta_sg_anual).";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function excluir_assunto($cd_pauta_sg_anual_assunto, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.pauta_sg_anual_assunto
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_pauta_sg_anual_assunto = ".intval($cd_pauta_sg_anual_assunto).";";

		$this->db->query($qr_sql);
	}
}