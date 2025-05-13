<?php
class Atendimento_retencao_model extends Model
{
	function __construct()
  	{
    	parent::Model();
  	}

  	public function get_usuario_inclusao()
  	{
  		$qr_sql = "
  			SELECT DISTINCT cd_usuario_inclusao AS value,
  			       funcoes.get_usuario_nome(cd_usuario_inclusao) AS text
  			  FROM projetos.atendimento_retencao
  			 WHERE dt_exclusao IS NULL
  			 ORDER BY 2";

  		return $this->db->query($qr_sql)->result_array();
  	}

  	public function listar($args = array(), $cd_atendimento_retencao = 0)
	{
		$qr_sql = "
			SELECT r.cd_atendimento_retencao,
			       r.cd_empresa,
				   r.cd_registro_empregado,
				   r.seq_dependencia,
				   p.nome,
				   TO_CHAR(r.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(r.cd_usuario_inclusao) AS ds_usuario_inclusao,
				   r.ds_descricao,
				   r.cd_atendimento,
				   r.fl_retido,
				   (SELECT ds_acompanhamento 
				      FROM (SELECT ar.dt_inclusao,
				                   TO_CHAR(ar.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') || ' : ' || ar.ds_descricao AS ds_acompanhamento
				              FROM projetos.atendimento_retencao ar
				             WHERE ar.cd_atendimento_retencao = r.cd_atendimento_retencao
				             UNION
				            SELECT ara.dt_inclusao,
				                   TO_CHAR(ara.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') || ' : ' || ara.ds_descricao AS ds_acompanhamento
					      FROM projetos.atendimento_retencao_acompanhamento ara
				             WHERE ara.cd_atendimento_retencao = r.cd_atendimento_retencao
				               AND ara.dt_exclusao IS NULL) AS x 
				   ORDER BY dt_inclusao DESC
				   LIMIT 1) AS ds_acompanhamento
			  FROM projetos.atendimento_retencao r
			  JOIN public.participantes p
				ON p.cd_empresa            = r.cd_empresa
			   AND p.cd_registro_empregado = r.cd_registro_empregado
			   AND p.seq_dependencia       = r.seq_dependencia
			 WHERE r.dt_exclusao IS NULL
			   ".(intval($cd_atendimento_retencao) > 0 ? "AND r.cd_atendimento_retencao != ".intval($cd_atendimento_retencao) : "")."
			   ".(trim($args['cd_usuario']) != '' ? "AND r.cd_usuario_inclusao = ".intval($args['cd_usuario']) : "")."			   
			   ".(trim($args['cd_empresa']) != '' ? "AND r.cd_empresa = ".intval($args['cd_empresa']) : "")."			   
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND r.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."			   
			   ".(trim($args['seq_dependencia']) != '' ? "AND r.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
			   ".(((trim($args['dt_ini']) != '') AND (trim($args['dt_fim']) != '')) ? " AND DATE_TRUNC('day', r.dt_inclusao) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(trim($args['fl_retido']) == 'S' ? "AND r.fl_retido = 'S'" : "")."
			   ".(trim($args['fl_retido']) == 'N' ? "AND r.fl_retido = 'N'" : "")."
			 ORDER BY r.dt_inclusao;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_atendimento_retencao)
	{
		$qr_sql = "
			SELECT r.cd_atendimento_retencao,
			       r.cd_empresa,
				   r.cd_registro_empregado,
				   r.seq_dependencia,
				   r.ds_descricao,
				   r.cd_atendimento,
				   r.fl_retido,
				   p.nome
		      FROM projetos.atendimento_retencao r
		      JOIN public.participantes p
				ON p.cd_empresa            = r.cd_empresa
			   AND p.cd_registro_empregado = r.cd_registro_empregado
			   AND p.seq_dependencia       = r.seq_dependencia
		     WHERE cd_atendimento_retencao = ".intval($cd_atendimento_retencao).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO projetos.atendimento_retencao
			     (
            		cd_empresa, 
            		cd_registro_empregado, 
            		seq_dependencia, 
            		ds_descricao,
            		cd_atendimento,
					fl_retido,
            		cd_usuario_inclusao,
            		cd_usuario_alteracao
                 )
            VALUES
                 (
                 	".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
                 	".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT").",
                 	".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
                 	".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
                 	".(trim($args['cd_atendimento']) != '' ? intval($args['cd_atendimento']) : "DEFAULT").",
					".(trim($args['fl_retido']) != '' ? "'".trim($args['fl_retido'])."'" : "DEFAULT").",
                 	".intval($args['cd_usuario']).",
                 	".intval($args['cd_usuario'])."
                 );";

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_atendimento_retencao, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.atendimento_retencao
			   SET fl_retido             = ".(trim($args['fl_retido']) != '' ? "'".trim($args['fl_retido'])."'" : "DEFAULT").",
			       cd_usuario_alteracao  = ".intval($args['cd_usuario']).",
			       dt_alteracao          = CURRENT_TIMESTAMP
			 WHERE cd_atendimento_retencao = ".intval($cd_atendimento_retencao).";";

		$this->db->query($qr_sql);
	}

	public function get_retencao_mes($cd_empresa, $cd_registro_empregado, $seq_dependencia)
	{
		$qr_sql = "
			SELECT COUNT(*) AS qt_anterior
			  FROM projetos.atendimento_retencao r2
			 WHERE dt_exclusao                IS NULL
			   AND TO_CHAR(dt_inclusao, 'MM') = TO_CHAR(CURRENT_DATE, 'MM')
			   AND cd_empresa                 = ".intval($cd_empresa)."
			   AND cd_registro_empregado      = ".intval($cd_registro_empregado)."
			   AND seq_dependencia            = ".intval($seq_dependencia).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function acompanhamento($cd_atendimento_retencao)
	{
		$qr_sql = "
			SELECT TO_CHAR(ar.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(ar.cd_usuario_inclusao) AS ds_usuario_inclusao,
				   ar.ds_descricao AS ds_atendimento_retencao_acompanhamento
		      FROM projetos.atendimento_retencao ar
		     WHERE ar.cd_atendimento_retencao = ".intval($cd_atendimento_retencao)."
		     UNION
		    SELECT TO_CHAR(ara.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(ara.cd_usuario_inclusao) AS ds_usuario_inclusao,
				   ara.ds_descricao AS ds_atendimento_retencao_acompanhamento
			  FROM projetos.atendimento_retencao_acompanhamento ara
		     WHERE ara.cd_atendimento_retencao = ".intval($cd_atendimento_retencao)."
		       AND ara.dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function salvar_acompanhamento($cd_atendimento_retencao, $args)
	{
		$qr_sql = "
			INSERT INTO projetos.atendimento_retencao_acompanhamento
			     (
                   cd_atendimento_retencao, 
                   ds_descricao, 
                   cd_usuario_inclusao, 
                   cd_usuario_alteracao
                 )
    		VALUES 
    		     (
    		       ".intval($cd_atendimento_retencao).",
    		       ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
    		       ".intval($args['cd_usuario']).",
                   ".intval($args['cd_usuario'])."
    		     );";

    	$this->db->query($qr_sql);
	}

}