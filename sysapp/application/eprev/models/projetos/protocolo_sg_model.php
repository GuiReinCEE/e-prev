<?php
class Protocolo_sg_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function get_gerencia()
	{
		$qr_sql = "
			SELECT codigo AS value,
                   codigo || ' - ' || nome AS text
              FROM funcoes.get_gerencias_vigente();";

        return $this->db->query($qr_sql)->result_array();
	}

	public function get_usuarios($cd_gerencia)
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_usuario_gerencia('".trim($cd_gerencia)."');";

        return $this->db->query($qr_sql)->result_array();
    }

	public function listar($args = array())
	{		
		$qr_sql = "
			SELECT p.cd_protocolo_sg,
			       funcoes.nr_protocolo_sg(p.nr_ano, p.nr_numero) AS ano_numero,
			       p.ds_protocolo_sg,
			       p.cd_gerencia_responsavel,
			       funcoes.get_usuario_nome(p.cd_usuario_responsavel) AS ds_usuario_responsavel,
		           p.cd_gerencia_substituto,
		           funcoes.get_usuario_nome(p.cd_usuario_substituto) AS ds_usuario_substituto,
			       TO_CHAR(p.dt_prazo, 'DD/MM/YYYY') AS dt_prazo,
			       TO_CHAR(p.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
			       TO_CHAR(p.dt_respondido, 'DD/MM/YYYY HH24:MI:SS') AS dt_respondido,
			       CASE WHEN p.fl_conhecimento = 'S' THEN 'Para Conhecimento' 
			            WHEN dt_respondido IS NULL AND dt_envio IS NULL THEN 'Não Enviado'
			            WHEN dt_respondido IS NULL AND dt_envio IS NOT NULL THEN 'Enviado'
			            ELSE 'Recebido'
			       END AS status,
			       CASE WHEN p.fl_conhecimento = 'S' THEN 'label label-warning'
			            WHEN dt_respondido IS NULL AND dt_envio IS NULL THEN 'label'
			            WHEN dt_respondido IS NULL AND dt_envio IS NOT NULL THEN 'label label-success'
			            ELSE 'label label-info'
			       END AS class_status,
			       CASE WHEN CURRENT_DATE > p.dt_prazo THEN 'label label-important'
			            ELSE 'label label-success'
			       END AS class_prazo,
			       p.arquivo,
			       p.arquivo_nome,
			       p.fl_conhecimento
			  FROM projetos.protocolo_sg p
			 WHERE p.dt_exclusao IS NULL
			   ".(trim($args['cd_usuario']) != '' ? "AND (p.cd_usuario_responsavel = ".intval($args['cd_usuario'])." OR p.cd_usuario_substituto = ".intval($args['cd_usuario']).")" : "")."
			   ".(trim($args['nr_numero']) != '' ? "AND p.nr_numero = ".intval($args['nr_numero']) : "")."
			   ".(trim($args['nr_ano']) != '' ? "AND p.nr_ano = ".intval($args['nr_ano']) : "")."
			   ".(trim($args['cd_usuario_responsavel']) != '' ? "AND p.cd_usuario_responsavel = ".intval($args['cd_usuario_responsavel']) : "")."
			   ".(trim($args['cd_gerencia_responsavel']) != '' ? "AND p.cd_gerencia_responsavel = '".trim($args['cd_gerencia_responsavel'])."'" : "")."
			   ".(trim($args['cd_usuario_substituto']) != '' ? "AND p.cd_usuario_substituto = ".intval($args['cd_usuario_substituto']) : "")."
			   ".(trim($args['cd_gerencia_substituto']) != '' ? "AND p.cd_gerencia_substituto = '".trim($args['cd_gerencia_substituto'])."'" : "")."
			   ".(((trim($args['dt_prazo_ini']) != '') AND (trim($args['dt_prazo_fim']) != '')) ? " AND (DATE_TRUNC('day', p.dt_prazo) BETWEEN TO_DATE('".$args['dt_prazo_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_prazo_fim']."', 'DD/MM/YYYY')".(trim($args['cd_usuario']) != '' ? "OR p.cd_usuario_responsavel = ".intval($args['cd_usuario'])." OR p.cd_usuario_substituto = ".intval($args['cd_usuario']) : "").")" : "")."
			   ".(trim($args['fl_respondido']) == 'N' ? "AND dt_respondido IS NULL" : "")."
			   ".(trim($args['fl_respondido']) == 'S' ? "AND dt_respondido IS NOT NULL" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_protocolo_sg)
	{
		$qr_sql = "
			SELECT p.cd_protocolo_sg,
			       funcoes.nr_protocolo_sg(p.nr_ano, p.nr_numero) AS ano_numero,
			       p.ds_protocolo_sg,
			       p.cd_gerencia_responsavel,
			       p.cd_usuario_responsavel,
			       funcoes.get_usuario_nome(p.cd_usuario_responsavel) AS ds_usuario_responsavel,
			       p.cd_gerencia_substituto,
			       p.cd_usuario_substituto,
			       funcoes.get_usuario(p.cd_usuario_responsavel) || '@eletroceee.com.br' AS ds_email_responsavel,
			       COALESCE(funcoes.get_usuario(p.cd_usuario_substituto), '') || '@eletroceee.com.br' AS ds_email_substituto,
			       funcoes.get_usuario_nome(p.cd_usuario_substituto) AS ds_usuario_substituto,
			       TO_CHAR(p.dt_prazo, 'DD/MM/YYYY') AS dt_prazo,
			       TO_CHAR(p.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
			       CASE WHEN p.fl_conhecimento = 'S' THEN 'Para Conhecimento' 
			            WHEN dt_respondido IS NULL AND dt_envio IS NULL THEN 'Não Enviado'
			            WHEN dt_respondido IS NULL AND dt_envio IS NOT NULL THEN 'Enviado'
			            ELSE 'Recebido'
			       END AS status,
			       CASE WHEN p.fl_conhecimento = 'S' THEN 'label label-warning'
			            WHEN dt_respondido IS NULL AND dt_envio IS NULL THEN 'label'
			            WHEN dt_respondido IS NULL AND dt_envio IS NOT NULL THEN 'label label-success'
			            ELSE 'label label-info'
			       END AS class_status,
			       p.arquivo,
			       p.arquivo_nome,
			       p.fl_conhecimento
			  FROM projetos.protocolo_sg p
			 WHERE p.cd_protocolo_sg = ".intval($cd_protocolo_sg).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}	

	public function salvar($args = array())
	{
		$cd_protocolo_sg = intval($this->db->get_new_id('projetos.protocolo_sg', 'cd_protocolo_sg'));

		$qr_sql = "
			INSERT INTO projetos.protocolo_sg
			     (
			       cd_protocolo_sg,
                   ds_protocolo_sg, 
                   cd_gerencia_responsavel, 
                   cd_usuario_responsavel,
                   cd_gerencia_substituto,
		           cd_usuario_substituto,
                   dt_prazo,
                   arquivo,
                   arquivo_nome,
                   fl_conhecimento,
                   cd_usuario_inclusao,
                   cd_usuario_alteracao 
                 )
			VALUES 
			     (
			     	".intval($cd_protocolo_sg).",
			     	".(trim($args['ds_protocolo_sg']) != '' ?  str_escape($args['ds_protocolo_sg']) : "DEFAULT").",
			     	".(trim($args['cd_gerencia_responsavel']) != '' ?  str_escape($args['cd_gerencia_responsavel']) : "DEFAULT").",
			     	".(trim($args['cd_usuario_responsavel']) != '' ?  intval($args['cd_usuario_responsavel']) : "DEFAULT").",
			     	".(trim($args['cd_gerencia_substituto']) != '' ?  str_escape($args['cd_gerencia_substituto']) : "DEFAULT").",
			     	".(trim($args['cd_usuario_substituto']) != '' ?  intval($args['cd_usuario_substituto']) : "DEFAULT").",
			     	".(trim($args['dt_prazo']) != '' ? "TO_DATE('".$args['dt_prazo']."','DD/MM/YYYY')" : "DEFAULT").",
			     	".(trim($args['arquivo']) != '' ?  str_escape($args['arquivo']) : "DEFAULT").",
			     	".(trim($args['arquivo_nome']) != '' ?  str_escape($args['arquivo_nome']) : "DEFAULT").",
			     	".(trim($args['fl_conhecimento']) != '' ?  str_escape($args['fl_conhecimento']) : "DEFAULT").",
			     	".intval($args['cd_usuario']).",
			     	".intval($args['cd_usuario'])."
			     );";

     	$this->db->query($qr_sql);

     	return $cd_protocolo_sg;
	}

	public function atualizar($cd_protocolo_sg, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.protocolo_sg
			   SET ds_protocolo_sg         = ".(trim($args['ds_protocolo_sg']) != '' ?  str_escape($args['ds_protocolo_sg']) : "DEFAULT").",
			       cd_gerencia_responsavel = ".(trim($args['cd_gerencia_responsavel']) != '' ?  str_escape($args['cd_gerencia_responsavel']) : "DEFAULT").",
			       cd_usuario_responsavel  = ".(trim($args['cd_usuario_responsavel']) != '' ?  intval($args['cd_usuario_responsavel']) : "DEFAULT").",
			       cd_gerencia_substituto  = ".(trim($args['cd_gerencia_substituto']) != '' ?  str_escape($args['cd_gerencia_substituto']) : "DEFAULT").",
			       cd_usuario_substituto   = ".(trim($args['cd_usuario_substituto']) != '' ?  intval($args['cd_usuario_substituto']) : "DEFAULT").",
			       dt_prazo                = ".(trim($args['dt_prazo']) != '' ? "TO_DATE('".$args['dt_prazo']."','DD/MM/YYYY')" : "DEFAULT").",
			       arquivo                 = ".(trim($args['arquivo']) != '' ?  str_escape($args['arquivo']) : "DEFAULT").",
                   arquivo_nome            = ".(trim($args['arquivo_nome']) != '' ?  str_escape($args['arquivo_nome']) : "DEFAULT").",
                   fl_conhecimento         = ".(trim($args['fl_conhecimento']) != '' ?  str_escape($args['fl_conhecimento']) : "DEFAULT").",
			       cd_usuario_alteracao    = ".intval($args['cd_usuario']).",
			       dt_alteracao            = CURRENT_TIMESTAMP
			 WHERE cd_protocolo_sg = ".intval($cd_protocolo_sg).";";

		$this->db->query($qr_sql);	 
	}

	public function excluir($cd_protocolo_sg, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.protocolo_sg
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_protocolo_sg = ".intval($cd_protocolo_sg).";";

		$this->db->query($qr_sql);
	}

	public function enviar($cd_protocolo_sg, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.protocolo_sg
			   SET cd_usuario_envio = ".intval($cd_usuario).",
			       dt_envio         = CURRENT_TIMESTAMP
			 WHERE cd_protocolo_sg = ".intval($cd_protocolo_sg).";";

		$this->db->query($qr_sql);
	}

	public function receber($cd_protocolo_sg, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.protocolo_sg
			   SET cd_usuario_respondido = ".intval($cd_usuario).",
			       dt_respondido         = CURRENT_TIMESTAMP
			 WHERE cd_protocolo_sg = ".intval($cd_protocolo_sg).";";

		$result = $this->db->query($qr_sql);
	}
}