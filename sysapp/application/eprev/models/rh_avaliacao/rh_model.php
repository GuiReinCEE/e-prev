<?php
class Rh_model extends Model
{
	public function listar($args = array())
	{
		$qr_sql = "
            SELECT uc.codigo,
                   uc.nome,
                   uc.divisao,
                   uc.usuario,
                   uc.guerra,
                   funcoes.get_usuario_avatar(uc.codigo) AS avatar,
                   uc.tipo,
				   uc.nr_ramal,
                   CASE WHEN tipo = 'D' THEN uc.observacao 
                        WHEN tipo = 'G' THEN (CASE WHEN SUBSTRING(uc.divisao FROM 1 FOR 1) = 'A' THEN 'Assessor(a)' ELSE 'Gerente' END)
                        WHEN tipo = 'U' THEN 'Colaborador(a)'
                        WHEN tipo = 'N' THEN 'Colaborador(a)'
                        WHEN tipo = 'P' THEN 'Prestador(a) de Serviço'
                        WHEN tipo = 'A' THEN 'Aprendiz'
                        WHEN tipo = 'E' THEN 'Estagiário(a)'
                        ELSE ''
                   END || (CASE WHEN COALESCE(uc.indic_13,'N') = 'S' THEN ' - Supervisor(a)' ELSE '' END) AS papel,
                   c.nome_cargo,
                   uc.cd_registro_empregado,
				   TO_CHAR(uc.dt_admissao,'DD/MM/YYYY') AS dt_admissao,
				   s.telefone AS celular
              FROM projetos.usuarios_controledi uc
              LEFT JOIN clicksign.signatario s
                ON s.email = TRIM(LOWER(uc.usuario || '@familiaprevidencia.com.br'))			  
              LEFT JOIN projetos.cargos c
                ON c.cd_cargo = uc.cd_cargo
             WHERE uc.divisao NOT IN ('SNG', 'LM2')
               ".(trim($args['fl_ativo']) == 'S' ? "AND uc.tipo NOT IN ('X','T')": "")."
               ".(trim($args['fl_ativo']) == 'N' ? "AND uc.tipo IN ('X','T')": "")."
               ".(trim($args['cd_gerencia']) != '' ? "AND uc.divisao = '".trim($args['cd_gerencia'])."'" : "")."
            ORDER BY uc.nome;";
#echo $qr_sql; exit;
        return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_usuario)
	{
		$qr_sql = "
			SELECT uc.codigo AS cd_usuario, 
                   uc.usuario, 
                   uc.nome, 
                   uc.tipo, 
				   s.telefone AS celular,
                   uc.avatar, 
                   TO_CHAR(uc.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
                   TO_CHAR(uc.dt_admissao,'DD/MM/YYYY') AS dt_admissao,
                   uc.divisao AS cd_gerencia, 
                   uc.cd_gerencia_unidade,
                   uc.guerra,
                   uc.cd_registro_empregado,  
                   uc.diretoria AS cd_diretoria, 
                   uc.cd_cargo, 
                   uc.cd_escolaridade,
                   uc.opt_workspace AS fl_exibe_cpuscanner,
                   uc.opt_interatividade AS fl_login_auto, 
                   uc.observacao,
                   uc.estacao_trabalho, 
                   uc.np_computador, 
                   uc.tela_inicial, 
                    
                   uc.fl_ldap_autenticar,
                   uc.senha_md5,
                   
                   uc.indic_01, uc.indic_02, uc.indic_03, uc.indic_04, uc.indic_05, uc.indic_06, 
                   uc.indic_07, uc.indic_08, uc.indic_09, uc.indic_10, uc.indic_11, uc.indic_12, uc.indic_13,       
                   
                   uc.assinatura,
                   
                   uc.chamada_web,
                   uc.gap_atendimento_versao, 
                   uc.nr_ramal, 
                   uc.nr_ramal_callcenter, 
                   uc.nr_ip_callcenter, 
                   
                   uc.fl_intervalo,
                   
                   TO_CHAR(uc.dt_hora_confirmacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_hora_confirmacao,
                   TO_CHAR(uc.ultima_resposta_vida, 'DD/MM/YYYY HH24:MI:SS') AS dt_ultima_resposta_vida,
                   TO_CHAR(uc.dt_hora_scanner_computador, 'DD/MM/YYYY HH24:MI:SS') AS dt_hora_scanner_computador,
                   TO_CHAR(uc.dt_ult_login, 'DD/MM/YYYY HH24:MI:SS') AS dt_ult_login,
                   TO_CHAR(uc.dt_login_callcenter, 'DD/MM/YYYY HH24:MI:SS') AS dt_login_callcenter,
                   TO_CHAR(uc.dt_monitor_callcenter, 'DD/MM/YYYY HH24:MI:SS') AS dt_monitor_callcenter
              FROM projetos.usuarios_controledi uc
              LEFT JOIN clicksign.signatario s
                ON s.email = TRIM(LOWER(uc.usuario || '@familiaprevidencia.com.br'))			  
              LEFT JOIN projetos.cargos c
                ON c.cd_cargo = uc.cd_cargo                   
             WHERE uc.codigo = ".intval($cd_usuario).";";

		return $this->db->query($qr_sql)->row_array();
	}

    public function get_gerencia_usuario()
    {
        $qr_sql = "
            SELECT uc.divisao AS value,
                   uc.divisao || ' - ' || d.nome AS text
              FROM projetos.usuarios_controledi uc 
              JOIN projetos.divisoes d
                ON d.codigo = uc.divisao
             GROUP BY value, text
             ORDER BY uc.divisao;";

        return $this->db->query($qr_sql)->result_array();
    }

	public function get_gerencia_vigente($cd_gerencia = '')
	{
		$qr_sql = "
			SELECT codigo AS value, 
			       nome AS text 
			  FROM funcoes.get_gerencias_vigente('DIV, CON', '".trim($cd_gerencia)."');";

		return $this->db->query($qr_sql)->result_array();
	}

    public function get_gerencia_unidade($cd_gerencia)
    {
        $qr_sql = "
            SELECT cd_gerencia_unidade AS value,
                   ds_descricao AS text
              FROM projetos.gerencia_unidade gu
             WHERE cd_gerencia = '".$cd_gerencia."'
               AND dt_vigencia_fim IS NULL;";

        return $this->db->query($qr_sql)->result_array();    
    }

	public function get_cargo()
    {
        $qr_sql = "
            SELECT cd_cargo AS value, 
                   nome_cargo AS text 
              FROM projetos.cargos 
             WHERE cd_familia IS NOT NULL
             ORDER BY nome_cargo;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_diretoria()
    {
        $qr_sql = "
	        SELECT DISTINCT(area) AS value, 
	               area AS text 
	          FROM projetos.divisoes 
	         WHERE area IS NOT NULL 
	            OR TRIM(area) <> ''
	         ORDER BY area;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_escolaridade()
    {
        $qr_sql = "
			SELECT cd_escolaridade AS value,
                   desc_escolaridade AS text
              FROM projetos.escolaridade
             ORDER BY ordem";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_usuario_foto($usuario)
	{
		$qr_sql = "
			SELECT funcoes.get_usuario_avatar(codigo) AS avatar,
				   usuario
			  FROM projetos.usuarios_controledi 
			 WHERE LOWER(usuario) = LOWER('".trim($usuario)."');";
		
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($cd_usuario, $args = array())
	{
		$qr_sql = " 
            UPDATE projetos.usuarios_controledi
               SET usuario               = ".(trim($args['usuario']) != '' ? "LOWER(funcoes.remove_acento('".trim($args['usuario'])."'))" : "DEFAULT").",
                   usu_email             = usuario,
                   nome                  = ".(trim($args['nome']) != '' ? "funcoes.remove_acento('".trim($args['nome'])."')" : "DEFAULT").",
                   guerra                = ".(trim($args['guerra']) != '' ? "funcoes.remove_acento('".trim($args['guerra'])."')" : "DEFAULT").",
                   tipo                  = ".(trim($args['tipo']) != '' ? "'".trim($args['tipo'])."'" : "DEFAULT").",
                   dt_nascimento         = ".(trim($args['dt_nascimento']) != '' ? "TO_DATE('".trim($args['dt_nascimento'])."','DD/MM/YYYY')" : "DEFAULT").",
                   dt_admissao           = ".(trim($args['dt_admissao']) != '' ? "TO_DATE('".trim($args['dt_admissao'])."','DD/MM/YYYY')" : "DEFAULT").",
                   divisao               = ".(trim($args['cd_gerencia']) != '' ? "'".trim($args['cd_gerencia'])."'" : "DEFAULT").",
                   cd_gerencia_unidade   = ".(trim($args['cd_gerencia_unidade']) != '' ? "'".trim($args['cd_gerencia_unidade'])."'" : "DEFAULT").",
                   cd_escolaridade       = ".(intval($args['cd_escolaridade']) > 0 ? intval($args['cd_escolaridade']) : "DEFAULT").",
                   cd_registro_empregado = ".(intval($args['cd_registro_empregado']) > 0 ? intval($args['cd_registro_empregado']) : "DEFAULT").",
                   diretoria             = ".(trim($args['cd_diretoria']) != '' ? "'".trim($args['cd_diretoria'])."'" : "DEFAULT").",
                   cd_cargo              = ".(intval($args['cd_cargo']) > 0 ? intval($args['cd_cargo']) : "DEFAULT").",
                   opt_workspace         = ".(trim($args['fl_exibe_cpuscanner']) != '' ? "'".trim($args['fl_exibe_cpuscanner'])."'" : "DEFAULT").",
                   opt_interatividade    = ".(trim($args['fl_login_auto']) != '' ? "'".trim($args['fl_login_auto'])."'" : "DEFAULT").",
                   observacao            = ".(trim($args['observacao']) != '' ? "'".trim($args['observacao'])."'" : "DEFAULT").",
                   fl_ldap_autenticar    = ".(trim($args['fl_ldap_autenticar']) != '' ? "'".trim($args['fl_ldap_autenticar'])."'" : "DEFAULT").",
                   senha_md5             = ".(trim($args['senha_md5']) == trim($args['senha_md5_old']) ? "senha_md5" : "MD5('".trim($args['senha_md5'])."')").",
                   assinatura            = ".(trim($args['assinatura']) != '' ? "'".trim($args['assinatura'])."'" : "DEFAULT").",
                   nr_ramal              = ".(intval($args['nr_ramal']) > 0 ? intval($args['nr_ramal']) : "DEFAULT").",
                   nr_ramal_callcenter   = ".(intval($args['nr_ramal_callcenter']) > 0 ? intval($args['nr_ramal_callcenter']) : "DEFAULT").",
                   nr_ip_callcenter      = ".(trim($args['nr_ip_callcenter']) != '' ? "'".trim($args['nr_ip_callcenter'])."'" : "DEFAULT").",
                   fl_intervalo          = ".(trim($args['fl_intervalo']) != '' ? "'".trim($args['fl_intervalo'])."'" : "DEFAULT").",
                   indic_01              = ".(trim($args['indic_01']) != '' ? "'".trim($args['indic_01'])."'" : "DEFAULT").",
                   indic_02              = ".(trim($args['indic_02']) != '' ? "'".trim($args['indic_02'])."'" : "DEFAULT").", 
                   indic_03              = ".(trim($args['indic_03']) != '' ? "'".trim($args['indic_03'])."'" : "DEFAULT").", 
                   indic_04              = ".(trim($args['indic_04']) != '' ? "'".trim($args['indic_04'])."'" : "DEFAULT").", 
                   indic_06              = ".(trim($args['indic_06']) != '' ? "'".trim($args['indic_06'])."'" : "DEFAULT").", 
                   indic_07              = ".(trim($args['indic_07']) != '' ? "'".trim($args['indic_07'])."'" : "DEFAULT").", 
                   indic_09              = ".(trim($args['indic_09']) != '' ? "'".trim($args['indic_09'])."'" : "DEFAULT").", 
                   indic_10              = ".(trim($args['indic_10']) != '' ? "'".trim($args['indic_10'])."'" : "DEFAULT").", 
                   indic_12              = ".(trim($args['indic_12']) != '' ? "'".trim($args['indic_12'])."'" : "DEFAULT").",
                   indic_13              = ".(trim($args['indic_13']) != '' ? "'".trim($args['indic_13'])."'" : "DEFAULT")."
             WHERE codigo = ".$cd_usuario.";
			 
            UPDATE clicksign.signatario
               SET telefone       = ".(trim($args['celular']) != '' ? "'".trim($args['celular'])."'" : "DEFAULT").",
                   dt_atualizacao = CURRENT_TIMESTAMP
             WHERE email = TRIM(LOWER(funcoes.remove_acento('".trim($args['usuario'])."') || '@familiaprevidencia.com.br'));
			 
			INSERT INTO clicksign.signatario(email,telefone)
			SELECT TRIM(LOWER(funcoes.remove_acento('".trim($args['usuario'])."') || '@familiaprevidencia.com.br')),
			       TRIM('".trim($args['celular'])."')
			 WHERE 0 = (SELECT COUNT(*) FROM clicksign.signatario a WHERE a.email = TRIM(LOWER(funcoes.remove_acento('".trim($args['usuario'])."') || '@familiaprevidencia.com.br')));
			 ";
		#echo "<PRE>".$qr_sql."</PRE>"; exit;
        $this->db->query($qr_sql);
	}

    public function get_progresso_promocao($cd_usuario)
    {
        $qr_sql = "
            SELECT TO_CHAR(pp.dt_progressao_promocao, 'DD/MM/YYYY') AS dt_progressao_promocao,
                   caa.cd_gerencia || ' - ' || cr.ds_cargo || (CASE WHEN aa.ds_area_atuacao IS NOT NULL THEN ' - ' || aa.ds_area_atuacao ELSE '' END) AS ds_cargo_area_atuacao,
                   TRIM(cr.ds_cargo || (CASE WHEN cl.ds_classe IS NOT NULL THEN ' ' || cl.ds_classe ELSE '' END) || (CASE WHEN ds_padrao IS NOT NULL THEN ' - ' || ds_padrao ELSE '' END)) AS ds_classe
              FROM rh_avaliacao.progressao_promocao pp
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = pp.cd_usuario
              JOIN projetos.divisoes d
                ON d.codigo = uc.divisao  
              JOIN rh_avaliacao.cargo_area_atuacao caa
                ON caa.cd_cargo_area_atuacao = pp.cd_cargo_area_atuacao
              JOIN rh_avaliacao.cargo cr
                ON cr.cd_cargo = caa.cd_cargo
              JOIN rh_avaliacao.classe cl
                ON cl.cd_classe = pp.cd_classe
              LEFT JOIN rh_avaliacao.area_atuacao aa
                ON aa.cd_area_atuacao = caa.cd_area_atuacao
              LEFT JOIN rh_avaliacao.classe_padrao cp
                ON cp.cd_classe_padrao = pp.cd_classe_padrao
             WHERE pp.dt_exclusao IS NULL
               AND pp.cd_usuario = ".intval($cd_usuario)."
             ORDER BY pp.dt_progressao_promocao DESC 
             LIMIT 1;";

        return $this->db->query($qr_sql)->row_array();
    }
}