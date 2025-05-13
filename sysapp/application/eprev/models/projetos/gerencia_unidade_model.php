<?php
class Gerencia_unidade_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

    public function listar($args = array())
	{
		$qr_sql = "
			SELECT codigo,
			       codigo || ' - ' || nome AS ds_gerencia,
			       (CASE WHEN area = 'SEG' THEN 'Diretoria de Previdência'
			             WHEN area = 'FIN' THEN 'Diretoria Financeiro'
			             WHEN area = 'ADM' THEN 'Diretoria Administrativo'
			             WHEN area = 'PRE' THEN 'Presidência'
			       END) AS ds_diretoria,
			       funcoes.get_usuario_nome(funcoes.get_gerente_gerencia(codigo)) AS ds_gerente,
			       funcoes.get_usuario_nome(funcoes.get_substituto_gerencia(codigo)) AS ds_substituto,
			       (CASE WHEN tipo = 'DIV' THEN 'Gerência'
			             WHEN tipo = 'COM' THEN 'Comitê'
			             WHEN tipo = 'CON' THEN 'Conselho'
			             WHEN tipo = 'OUT' THEN 'Outro'
			       END) AS ds_tipo,
			       TO_CHAR(dt_vigencia_ini, 'DD/MM/YYYY') AS dt_vigencia_ini,
			       TO_CHAR(dt_vigencia_fim, 'DD/MM/YYYY') AS dt_vigencia_fim
			  FROM projetos.divisoes
			 WHERE dt_exclusao IS NULL
			   AND (dt_vigencia_fim IS NULL OR dt_vigencia_fim >= CURRENT_DATE)
			   ".(trim($args['fl_tipo']) != '' ? "AND tipo = '".trim($args['fl_tipo'])."'" : "")."
			   ".(trim($args['fl_area']) != '' ? "AND area = '".trim($args['fl_area'])."'" : "")."
			 ORDER BY codigo;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_unidade_gerencia($cd_gerencia)
	{
		$qr_sql = "
			SELECT gu.cd_gerencia_unidade,
			       gu.ds_descricao,
			       TO_CHAR(gu.dt_vigencia_ini, 'DD/MM/YYYY') AS dt_vigencia_ini,
			       gu.ds_email,
			       d.codigo
			  FROM projetos.divisoes d 
			  LEFT JOIN projetos.gerencia_unidade gu
			    ON gu.cd_gerencia = d.codigo
			 WHERE (gu.dt_vigencia_fim IS NULL OR gu.dt_vigencia_fim >= CURRENT_DATE)
			   AND d.codigo = ".str_escape(strtoupper($cd_gerencia)).";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_supervisor($cd_gerencia)
	{
		$qr_sql = "
			SELECT nome
			  FROM projetos.usuarios_controledi uc
			 WHERE uc.indic_13 = 'S'
			   AND uc.tipo != 'X'
			   AND uc.divisao = ".str_escape(strtoupper($cd_gerencia)).";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_supervisor_gerencia($cd_gerencia)
	{
		$qr_sql = "
			SELECT nome
			  FROM projetos.usuarios_controledi uc
			 WHERE uc.indic_13 = 'S'
			   AND uc.tipo != 'X'
			   AND (SELECT COUNT(*) FROM projetos.gerencia_unidade_supervisor gus WHERE gus.cd_usuario = uc.codigo) = 0
			   AND uc.divisao = ".str_escape(strtoupper($cd_gerencia)).";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_supervisor_unidade($cd_gerencia_unidade)
	{
		$qr_sql = "
			SELECT nome
			  FROM projetos.usuarios_controledi uc
			  JOIN projetos.gerencia_unidade_supervisor gus
			    ON gus.cd_usuario = uc.codigo
			 WHERE uc.indic_13 = 'S'
			   AND uc.tipo != 'X'
			   AND gus.cd_gerencia_unidade = ".str_escape(strtoupper($cd_gerencia_unidade)).";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_gerencia)
	{
		$qr_sql = "
			SELECT codigo,
			       nome,
			       tipo,
			       area,
			       fl_atividade,
			       TO_CHAR(dt_vigencia_ini, 'DD/MM/YYYY') AS dt_vigencia_ini,
			       TO_CHAR(dt_vigencia_fim, 'DD/MM/YYYY') AS dt_vigencia_fim
			  FROM projetos.divisoes
			 WHERE codigo = ".str_escape(strtoupper($cd_gerencia)).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO projetos.divisoes
			     (
			     	codigo,
			     	nome,
			     	tipo,
			     	area,
			     	fl_atividade,
			     	dt_vigencia_ini,
			     	cd_usuario_inclusao,
			     	cd_usuario_alteracao
			     )
			VALUES
			     (
			        ".str_escape(strtoupper($args['codigo'])).",
			        ".(trim($args['nome']) != '' ? str_escape($args['nome']) : "DEFAULT").",
			        ".(trim($args['tipo']) != '' ? str_escape($args['tipo']) : "DEFAULT").",
			        ".(trim($args['area']) != '' ? str_escape($args['area']) : "DEFAULT").",
			        ".(trim($args['fl_atividade']) != '' ? str_escape($args['fl_atividade']) : "DEFAULT").",
			        ".(trim($args['dt_vigencia_ini']) != '' ? "TO_DATE('".$args['dt_vigencia_ini']."', 'DD/MM/YYYY')" : "DEFAULT").",
			        ".intval($args['cd_usuario']).",
			        ".intval($args['cd_usuario'])."
			     );";

		$this->db->query($qr_sql);
	}

	public function atualizar($codigo, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.divisoes
			   SET nome                 = ".(trim($args['nome']) != '' ? str_escape($args['nome']) : "DEFAULT").",
			       tipo                 = ".(trim($args['tipo']) != '' ? str_escape($args['tipo']) : "DEFAULT").",
			       area                 = ".(trim($args['area']) != '' ? str_escape($args['area']) : "DEFAULT").",
			       fl_atividade         = ".(trim($args['fl_atividade']) != '' ? str_escape($args['fl_atividade']) : "DEFAULT").",
			       dt_vigencia_ini      = ".(trim($args['dt_vigencia_ini']) != '' ? "TO_DATE('".$args['dt_vigencia_ini']."', 'DD/MM/YYYY')" : "DEFAULT").",
			       dt_vigencia_fim      = ".(trim($args['dt_vigencia_fim']) != '' ? "TO_DATE('".$args['dt_vigencia_fim']."', 'DD/MM/YYYY')" : "DEFAULT").",
			       cd_usuario_alteracao = ".intval($args['cd_usuario']).",
			       dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE UPPER(codigo) = ".str_escape(strtoupper($codigo)).";";

		$this->db->query($qr_sql);
	}

	public function carrega_unidade($cd_gerencia_unidade)
	{
		$qr_sql = "
			SELECT cd_gerencia_unidade,
			       ds_descricao,
			       ds_email,
			       TO_CHAR(dt_vigencia_ini, 'DD/MM/YYYY') AS dt_vigencia_ini,
			       TO_CHAR(dt_vigencia_fim, 'DD/MM/YYYY') AS dt_vigencia_fim
			  FROM projetos.gerencia_unidade
			 WHERE cd_gerencia_unidade = ".str_escape(strtoupper($cd_gerencia_unidade)).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_usuario_unidade($cd_gerencia_unidade)
	{
		$qr_sql = "
			SELECT nome 
			  FROM funcoes.get_usuario_gerencia_unidade(".str_escape(strtoupper($cd_gerencia_unidade)).");";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_cd_unidade($codigo)
	{
		$qr_sql = "
			SELECT a.nome
			  FROM projetos.usuarios_controledi a
			 WHERE a.cd_gerencia_unidade = ".str_escape(strtoupper($codigo))."
			   AND a.tipo NOT IN ('X','G')
			   AND a.indic_01 <> 'S'			  
			 ORDER BY a.nome ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_usuario_sem_unidade($codigo)
	{
		$qr_sql = "
			SELECT nome,
				   '<b>'||observacao || '</b>: '|| nome AS ds_diretoria,
				   divisao,
				   observacao
			  FROM projetos.usuarios_controledi 
			 WHERE divisao = '".trim($codigo)."'
			    AND cd_gerencia_unidade IS NULL
			    AND tipo NOT IN ('X','G')
			    AND indic_13 <> 'S';";
			
		return $this->db->query($qr_sql)->result_array();
	}

	public function salvar_unidade($args = array())
	{
		$qr_sql = "
			INSERT INTO projetos.gerencia_unidade
			     (
			     	cd_gerencia,
			     	cd_gerencia_unidade,
			     	ds_descricao,			 
			     	dt_vigencia_ini,
			     	ds_email,
			     	cd_usuario_inclusao,
			     	cd_usuario_alteracao
			     )
			VALUES
			     (
			        ".str_escape(strtoupper($args['cd_gerencia'])).",
			        ".str_escape(strtoupper($args['cd_gerencia_unidade'])).",
			        ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
			        ".(trim($args['dt_vigencia_ini']) != '' ? "TO_DATE('".$args['dt_vigencia_ini']."', 'DD/MM/YYYY')" : "DEFAULT").",
			        ".(trim($args['ds_email']) != '' ? str_escape($args['ds_email']) : "DEFAULT").",
			        ".intval($args['cd_usuario']).",
			        ".intval($args['cd_usuario'])."
			     );";

		$this->db->query($qr_sql);
	}

	public function atualizar_unidade($cd_gerencia_unidade, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.gerencia_unidade
			   SET ds_descricao         = ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
			       dt_vigencia_ini      = ".(trim($args['dt_vigencia_ini']) != '' ? "TO_DATE('".$args['dt_vigencia_ini']."', 'DD/MM/YYYY')" : "DEFAULT").",
			       dt_vigencia_fim      = ".(trim($args['dt_vigencia_fim']) != '' ? "TO_DATE('".$args['dt_vigencia_fim']."', 'DD/MM/YYYY')" : "DEFAULT").",
			       ds_email				= ".(trim($args['ds_email']) != '' ? str_escape($args['ds_email']) : "DEFAULT").",
			       cd_usuario_alteracao = ".intval($args['cd_usuario']).",
			       dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE UPPER(cd_gerencia_unidade) = ".str_escape(strtoupper($cd_gerencia_unidade)).";";

		$this->db->query($qr_sql);
	}
}