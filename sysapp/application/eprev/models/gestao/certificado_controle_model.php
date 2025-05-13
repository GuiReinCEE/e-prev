<?php
class Certificado_controle_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function get_tipo()
	{		
		$qr_sql = "
			SELECT cd_certificado_controle_tipo AS value,
				   ds_certificado_controle_tipo AS text
			  FROM gestao.certificado_controle_tipo
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_certificado_controle_tipo ASC;";
		
		return $this->db->query($qr_sql)->result_array();
	}

	public function get_cargo()
	{		
		$qr_sql = "
			SELECT cd_certificado_controle_cargo AS value,
				   ds_certificado_controle_cargo AS text
			  FROM gestao.certificado_controle_cargo
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_certificado_controle_cargo ASC;";
		
		return $this->db->query($qr_sql)->result_array();
	}

	public function listar($args = array())
	{		
		$qr_sql = "
			SELECT cc.cd_certificado_controle,
				   cc.cpf,
				   cc.nome,
				   TO_CHAR(cc.dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento,
				   TO_CHAR(cc.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(cc.dt_posse, 'DD/MM/YYYY') AS dt_posse,
				   TO_CHAR(cc.dt_posse_fim, 'DD/MM/YYYY') AS dt_posse_fim,
				   TO_CHAR(cc.dt_certificao, 'DD/MM/YYYY') AS dt_certificao,
				   TO_CHAR(cc.dt_expira_certificado, 'DD/MM/YYYY') AS dt_expira_certificado,
				   cc.arquivo,
				   cc.arquivo_nome,
				   ccc.ds_certificado_controle_cargo,
				   cct.ds_certificado_controle_tipo,
				   CASE WHEN cc.dt_certificao IS NOT NULL AND cc.dt_expira_certificado >= CURRENT_DATE THEN 'Sim'
				        WHEN (cc.dt_certificao IS NULL OR cc.dt_expira_certificado < CURRENT_DATE) THEN 'Não'
				   END AS certificado,
				   CASE WHEN cc.dt_certificao IS NOT NULL AND cc.dt_expira_certificado >= CURRENT_DATE THEN 'label label-success'
				        WHEN (cc.dt_certificao IS NULL OR cc.dt_expira_certificado < CURRENT_DATE) THEN 'label label-important'
				   END AS class_certificado,
				   CASE WHEN (SELECT COUNT(*)
				   	            FROM gestao.certificado_controle ccf
				   	           WHERE ccf.dt_exclusao IS NULL
				   	             AND ccf.cd_certificado_controle_pai = cc.cd_certificado_controle) > 0 THEN 'label'
				        WHEN CURRENT_DATE BETWEEN CAST(cc.dt_expira_certificado - interval '90 DAY' AS date) AND CAST(cc.dt_expira_certificado AS date) THEN 'label label-warning'
				        WHEN cc.dt_expira_certificado < CURRENT_DATE THEN 'label label-important'
				        ELSE 'label label-success'
				    END AS class_termino,
				    CASE WHEN (SELECT COUNT(*)
				   	            FROM gestao.certificado_controle ccf
				   	           WHERE ccf.dt_exclusao IS NULL
				   	             AND ccf.cd_certificado_controle_pai = cc.cd_certificado_controle) > 0 THEN 'S'
                        ELSE ''
                    END AS fl_recertificacao,
                   (CASE WHEN ccc.fl_pontuacao = 'S' AND cct.fl_pontuacao = 'S'
				         THEN 'S'
				         ELSE 'N'
				   END) AS fl_pontuacao,
				   cc.nr_pontuacao_1,
				   cc.nr_pontuacao_2,
				   cc.nr_pontuacao_3,
				   (cc.nr_pontuacao_1 + cc.nr_pontuacao_2 + cc.nr_pontuacao_3) AS nr_pontuacao_total,
				   cc.fl_indicado
			  FROM gestao.certificado_controle cc
                  
			  LEFT JOIN gestao.certificado_controle_cargo ccc
			    ON ccc.cd_certificado_controle_cargo = cc.cd_certificado_controle_cargo
			  LEFT JOIN gestao.certificado_controle_tipo cct
			    ON cct.cd_certificado_controle_tipo = cc.cd_certificado_controle_tipo
			 WHERE cc.dt_exclusao IS NULL
			   ".(trim($args['cd_certificado_controle_cargo']) != '' ? "AND cc.cd_certificado_controle_cargo = ".intval($args['cd_certificado_controle_cargo']) : "")."
			   ".(trim($args['cd_certificado_controle_tipo']) != '' ? "AND cc.cd_certificado_controle_tipo = ".intval($args['cd_certificado_controle_tipo']) : "")."
			   ".(trim($args['cpf']) != '' ? "AND cc.cpf = '".trim($args['cpf'])."'" : "")."
			   ".(trim($args['nome']) != '' ? "AND UPPER(funcoes.remove_acento(cc.nome)) LIKE (UPPER(funcoes.remove_acento('%".trim($args['nome'])."%')))" : "")."
			   ".(((trim($args['dt_inclusao_ini']) != '') AND  (trim($args['dt_inclusao_fim']) != '')) ? "AND DATE_TRUNC('day', cc.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_certificao_ini']) != '') AND  (trim($args['dt_certificao_fim']) != '')) ? "AND DATE_TRUNC('day', cc.dt_certificao) BETWEEN TO_DATE('".$args['dt_certificao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_certificao_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_expira_certificado_ini']) != '') AND  (trim($args['dt_expira_certificado_fim']) != '')) ? " AND DATE_TRUNC('day', cc.dt_expira_certificado) BETWEEN TO_DATE('".$args['dt_expira_certificado_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_expira_certificado_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(trim($args['fl_certificado']) == 'S' ? "AND cc.dt_certificao IS NOT NULL AND cc.dt_expira_certificado >= CURRENT_DATE"  : "")."
			   ".(trim($args['fl_certificado']) == 'N' ? "AND (cc.dt_certificao IS NULL OR cc.dt_expira_certificado < CURRENT_DATE)"  : "")."

			   ".(trim($args['fl_recertificado']) == 'S' ? "AND (SELECT COUNT(*)
													   	           FROM gestao.certificado_controle ccf
													   	          WHERE ccf.dt_exclusao IS NULL
													   	            AND ccf.cd_certificado_controle_pai = cc.cd_certificado_controle) > 0"  : "")."
			   ".(trim($args['fl_recertificado']) == 'N' ? "AND (SELECT COUNT(*)
													   	           FROM gestao.certificado_controle ccf
													   	          WHERE ccf.dt_exclusao IS NULL
													   	            AND ccf.cd_certificado_controle_pai = cc.cd_certificado_controle) = 0"  : "")."

               ".(trim($args['fl_posse']) == 'S' ? "AND (cc.dt_posse_fim IS NULL OR cc.dt_posse_fim > CURRENT_DATE)"  : "")."
			   ".(trim($args['fl_posse']) == 'N' ? "AND cc.dt_posse_fim < CURRENT_DATE"  : "")."

			   ".(trim($args['fl_pontuacao']) == 'S' ? "AND ccc.fl_pontuacao = 'S' AND cct.fl_pontuacao = 'S'"  : "")."
			   ".(trim($args['fl_pontuacao']) == 'N' ? "AND (ccc.fl_pontuacao = 'N' OR cct.fl_pontuacao = 'N')"  : "")."

			   ;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_certificado_controle)
	{
		$qr_sql = "
			SELECT cc.cd_certificado_controle,
				   cc.cpf,
				   cc.nome,
				   TO_CHAR(cc.dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento,
				   TO_CHAR(cc.dt_posse, 'DD/MM/YYYY') AS dt_posse,
				   TO_CHAR(cc.dt_posse_fim, 'DD/MM/YYYY') AS dt_posse_fim,
				   TO_CHAR(cc.dt_certificao, 'DD/MM/YYYY') AS dt_certificao,
				   TO_CHAR(cc.dt_expira_certificado, 'DD/MM/YYYY') AS dt_expira_certificado,
				   cc.arquivo,
				   cc.arquivo_nome,
				   cc.cd_certificado_controle_cargo,
				   cc.cd_certificado_controle_tipo,
				   cc.cd_certificado_controle_pai,
				   (SELECT COUNT(*)
				   	  FROM gestao.certificado_controle ccf
				   	 WHERE ccf.dt_exclusao IS NULL
				   	   AND ccf.cd_certificado_controle_pai = cc.cd_certificado_controle) AS nr_filho,
				   (CASE WHEN ccc.fl_pontuacao = 'S' AND cct.fl_pontuacao = 'S'
				         THEN 'S'
				         ELSE 'N'
				   END) AS fl_pontuacao,
				   cc.nr_pontuacao_1,
				   cc.nr_pontuacao_2,
				   cc.nr_pontuacao_3,
				   cc.fl_indicado
			  FROM gestao.certificado_controle  cc
			  LEFT JOIN gestao.certificado_controle_cargo ccc
			    ON ccc.cd_certificado_controle_cargo = cc.cd_certificado_controle_cargo
			  LEFT JOIN gestao.certificado_controle_tipo cct
			    ON cct.cd_certificado_controle_tipo = cc.cd_certificado_controle_tipo
			 WHERE cc.cd_certificado_controle = ".intval($cd_certificado_controle).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_certificado_controle = intval($this->db->get_new_id('gestao.certificado_controle', 'cd_certificado_controle'));

		$qr_sql = "
			INSERT INTO gestao.certificado_controle 
			     (
			     	cd_certificado_controle,
				    cpf,
				    nome,
				    dt_nascimento,
				    dt_posse,
				    dt_certificao,
				    dt_expira_certificado,
				    arquivo,
				    arquivo_nome,
				    cd_certificado_controle_cargo,
			        cd_certificado_controle_tipo,
			        cd_certificado_controle_pai,
			        fl_indicado,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				 )
			VALUES
			     (
			     	".intval($cd_certificado_controle).",
				    ".(trim($args['cpf']) != '' ? str_escape($args['cpf']) : "DEFAULT").",
                    ".(trim($args['nome']) != '' ? str_escape($args['nome']) : "DEFAULT").",
                    ".(trim($args['dt_nascimento']) != '' ? "TO_DATE('".$args['dt_nascimento']."', 'DD/MM/YYYY')" : "DEFAULT").",
                    ".(trim($args['dt_posse']) != '' ? "TO_DATE('".$args['dt_posse']."', 'DD/MM/YYYY')" : "DEFAULT").",
                    ".(trim($args['dt_certificao']) != '' ? "TO_DATE('".$args['dt_certificao']."', 'DD/MM/YYYY')" : "DEFAULT").",
                    ".(trim($args['dt_expira_certificado']) != '' ? "TO_DATE('".$args['dt_expira_certificado']."', 'DD/MM/YYYY')" : "DEFAULT").",
					".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
					".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
					".(trim($args['cd_certificado_controle_cargo']) != '' ? intval($args['cd_certificado_controle_cargo']) : "DEFAULT").",
					".(trim($args['cd_certificado_controle_tipo']) != '' ? intval($args['cd_certificado_controle_tipo']) : "DEFAULT").",
				    ".(trim($args['cd_certificado_controle_pai']) != '' ? intval($args['cd_certificado_controle_pai']) : "DEFAULT").",
                    ".(trim($args['fl_indicado']) != '' ? str_escape($args['fl_indicado']) : "DEFAULT").",
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				 );";
	
		$this->db->query($qr_sql);

		return $cd_certificado_controle;
	}

	public function atualizar($cd_certificado_controle, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.certificado_controle 
			   SET cpf                           = ".(trim($args['cpf']) != '' ? str_escape($args['cpf']) : "DEFAULT").",
				   nome                          = ".(trim($args['nome']) != '' ? str_escape($args['nome']) : "DEFAULT").",
				   dt_nascimento                 = ".(trim($args['dt_nascimento']) != '' ? "TO_DATE('".$args['dt_nascimento']."', 'DD/MM/YYYY')" : "DEFAULT").",
				   dt_posse                      = ".(trim($args['dt_posse']) != '' ? "TO_DATE('".$args['dt_posse']."', 'DD/MM/YYYY')" : "DEFAULT").",
				   dt_posse_fim                  = ".(trim($args['dt_posse_fim']) != '' ? "TO_DATE('".$args['dt_posse_fim']."', 'DD/MM/YYYY')" : "DEFAULT").",
				   dt_certificao                 = ".(trim($args['dt_certificao']) != '' ? "TO_DATE('".$args['dt_certificao']."', 'DD/MM/YYYY')" : "DEFAULT").",
				   dt_expira_certificado         = ".(trim($args['dt_expira_certificado']) != '' ? "TO_DATE('".$args['dt_expira_certificado']."', 'DD/MM/YYYY')" : "DEFAULT").",
				   arquivo                       = ".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
				   arquivo_nome                  = ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
				   cd_certificado_controle_cargo = ".(trim($args['cd_certificado_controle_cargo']) != '' ? intval($args['cd_certificado_controle_cargo']) : "DEFAULT").",
			       cd_certificado_controle_tipo  = ".(trim($args['cd_certificado_controle_tipo']) != '' ? intval($args['cd_certificado_controle_tipo']) : "DEFAULT").",
			       fl_indicado                   = ".(trim($args['fl_indicado']) != '' ? str_escape($args['fl_indicado']) : "DEFAULT").",
			       nr_pontuacao_1                = ".(trim($args['nr_pontuacao_1']) != '' ? intval($args['nr_pontuacao_1']) : "DEFAULT").",
			       nr_pontuacao_2                = ".(trim($args['nr_pontuacao_2']) != '' ? intval($args['nr_pontuacao_2']) : "DEFAULT").",
			       nr_pontuacao_3                = ".(trim($args['nr_pontuacao_3']) != '' ? intval($args['nr_pontuacao_3']) : "DEFAULT").",
				   cd_usuario_alteracao          = ".intval($args['cd_usuario']).",
				   dt_alteracao                  = CURRENT_TIMESTAMP
			 WHERE cd_certificado_controle = ".intval($cd_certificado_controle).";";

		$this->db->query($qr_sql);
	}

	public function excluir($cd_certificado_controle, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.certificado_controle
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($cd_usuario)."
		     WHERE cd_certificado_controle = ".intval($cd_certificado_controle).";";
			 
		$this->db->query($qr_sql);
	}

	public function busca_participante($cpf)
	{
		$qr_sql = "
			SELECT p.nome 
			  FROM projetos.participante_cpf('".trim($cpf)."',1) x 
			  JOIN participantes p 
			    ON p.cd_empresa = x.cd_empresa 
			   AND p.cd_registro_empregado = x.cd_registro_empregado 
			   AND p.seq_dependencia = x.seq_dependencia
			 UNION
			SELECT nome
			  FROM gestao.certificado_controle 
			 WHERE cpf = '".trim($cpf)."'
			 LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}
}