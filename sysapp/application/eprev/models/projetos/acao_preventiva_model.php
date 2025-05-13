<?php
class acao_preventiva_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "

			SELECT ap.nr_ano,
                   ap.nr_ap,
		           ap.cd_divisao,
                   d.nome AS gerencia,
                   funcoes.nr_ap(ap.nr_ano,ap.nr_ap) AS numero_cad_ap,
                   ap.potencial_nc,
                   TO_CHAR(ap.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   
                   TO_CHAR(ap.dt_proposta,'DD/MM/YYYY') AS dt_proposta,
                   TO_CHAR(ap.dt_implementacao,'DD/MM/YYYY') AS dt_implementacao,
                   TO_CHAR(ap.dt_cancelado,'DD/MM/YYYY') AS dt_cancelado,
                   TO_CHAR(ap.dt_validacao,'DD/MM/YYYY HH24:MI:SS') AS dt_validacao,
                   TO_CHAR(COALESCE(ap.dt_prazo_validacao_prorroga, ap.dt_prazo_validacao),'DD/MM/YYYY') AS dt_prazo_validacao,
                   uc.nome,
                   p.procedimento,
                   TO_CHAR((SELECT MAX(app.dt_prorrogacao)
                              FROM projetos.acao_preventiva_prorrogacao app
                             WHERE app.nr_ano      = ap.nr_ano
                               AND app.nr_ap       = ap.nr_ap
                               AND app.dt_exclusao IS NULL), 'DD/MM/YYYY') AS dt_prorrogacao,
                   uc2.nome AS auditor,
                   uc3.nome AS substituto
              FROM projetos.acao_preventiva ap
              LEFT JOIN gestao.acao_preventiva_auditor apa
                ON apa.cd_processo = ap.cd_processo
              LEFT JOIN projetos.usuarios_controledi uc2
                ON apa.cd_usuario_titular = uc2.codigo
              LEFT JOIN projetos.usuarios_controledi uc3
                ON apa.cd_usuario_substituto = uc3.codigo
              JOIN projetos.divisoes d
                ON d.codigo = ap.cd_divisao
              JOIN projetos.usuarios_controledi uc
                ON ap.cd_responsavel = uc.codigo
              JOIN projetos.processos p
                ON ap.cd_processo = p.cd_processo
             WHERE ap.dt_exclusao IS NULL
               ".((trim($args['cd_usuario_titular']) != "") ? " AND apa.cd_usuario_titular = ".intval($args['cd_usuario_titular'])  : "")."
               ".((trim($args['cd_usuario_substituto']) != "") ? " AND apa.cd_usuario_substituto = ".intval($args['cd_usuario_substituto'])  : "")."
               ".((trim($args['cancelamento']) == "S") ? " AND dt_cancelado IS NOT NULL"  : "")."
               ".((trim($args['cancelamento']) == "N") ? " AND dt_cancelado IS NULL"  : "")."
               ".((trim($args['validado']) == "S") ? " AND dt_validacao IS NOT NULL"  : "")."
               ".((trim($args['validado']) == "N") ? " AND dt_validacao IS NULL"  : "")."
               ".((trim($args['implementado']) == "S") ? " AND dt_implementacao IS NOT NULL"  : "")."
               ".((trim($args['implementado']) == "N") ? " AND dt_implementacao IS NULL"  : "")."
               ".((trim($args['processo']) != "") ? " AND p.cd_processo = ".intval($args['processo']) : "")."
               ".((trim($args['gerencia']) != "") ? " AND ap.cd_divisao = '".trim($args['gerencia']) ."'" : "")."
               ".((intval($args['usuario']) > 0) ? " AND ap.cd_responsavel = ".trim($args['usuario']) : "")."
               ".(((trim($args['dt_inclussao_ini']) != "") and  (trim($args['dt_inclussao_fim']) != "")) ? " AND DATE_TRUNC('day', ap.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclussao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclussao_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_prazo_validacao_ini']) != "") and  (trim($args['dt_prazo_validacao_fim']) != "")) ? " AND DATE_TRUNC('day', COALESCE(ap.dt_prazo_validacao_prorroga, ap.dt_prazo_validacao)) BETWEEN TO_DATE('".$args['dt_prazo_validacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_prazo_validacao_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_validacao_ini']) != "") and  (trim($args['dt_validacao_fim']) != "")) ? " AND DATE_TRUNC('day', ap.dt_validacao) BETWEEN TO_DATE('".$args['dt_validacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_validacao_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_proposta_ini']) != "") and  (trim($args['dt_proposta_fim']) != "")) ? " AND DATE_TRUNC('day', ap.dt_proposta) BETWEEN TO_DATE('".$args['dt_proposta_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_proposta_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_implementacao_ini']) != "") and  (trim($args['dt_implementacao_fim']) != "")) ? " AND DATE_TRUNC('day', ap.dt_implementacao) BETWEEN TO_DATE('".$args['dt_implementacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_implementacao_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_prorrogacao_ini']) != "") and  (trim($args['dt_prorrogacao_fim']) != "")) ? " AND DATE_TRUNC('day', (SELECT MAX(app.dt_prorrogacao)
                                                                                                                                          FROM projetos.acao_preventiva_prorrogacao app
                                                                                                                                         WHERE app.nr_ano = ap.nr_ano
                                                                                                                                           AND app.nr_ap = ap.nr_ap
                                                                                                                                           AND app.dt_exclusao IS NULL)
                                                                                                                                          ) BETWEEN TO_DATE('".$args['dt_prorrogacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_prorrogacao_fim']."', 'DD/MM/YYYY')" : "")."
            ;";
        $result = $this->db->query($qr_sql);
    }
    
    function auditores(&$result, $args=array())
    {
        $qr_sql = "
			SELECT codigo AS value,
                   nome AS text
              FROM projetos.usuarios_controledi
             WHERE indic_12 = '*'
               AND codigo <> 170
               AND tipo <> 'X'
             ORDER BY nome";

        $result = $this->db->query($qr_sql);
    }
    
    function data_min_prazo_validacao($result, $args)
    {
        $qr_sql = "
            SELECT TO_CHAR(COALESCE(funcoes.dia_util('DEPOIS', CURRENT_DATE, 5)),'DD/MM/YYYY') AS quinto_dia_util";
        $result = $this->db->query($qr_sql);
        return $result->row_array();
    }

    function carrega(&$result, $args=array())
    {
        $qr_sql = "
            SELECT funcoes.nr_ap(ap.nr_ano,ap.nr_ap) AS numero_cad_ap,
                   d.nome AS gerencia,
                   d.codigo AS divisao,
                   uc.nome AS nome_usuario,
                   ap.cd_responsavel AS usuario,
                   ap.cd_processo,
                   ap.nr_ano,
                   ap.nr_ap,
                   ap.cd_acao_preventiva,
                   ap.potencial_nc,
                   ap.causa_nc,
                   ap.fonte_info,
                   ap.acao_proposta,
                   ap.cd_responsavel,
				   ap.cd_substituto,
                   ap.cd_usuario_inclusao,
                   ucad.nome AS usuario_cadastro,
                   ucv.nome AS validado,
                   ucc.nome AS usuario_cancelado,
				   uc2.nome AS nome_substituto,
                   TO_CHAR(ap.dt_prazo_validacao, 'DD/MM/YYYY') AS dt_prazo_validacao,
				   TO_CHAR(ap.dt_prazo_validacao_prorroga, 'DD/MM/YYYY') AS dt_prazo_validacao_prorroga,
                   TO_CHAR(ap.dt_implementacao,'DD/MM/YYYY') AS dt_implementacao,
                   TO_CHAR(ap.dt_validacao,'DD/MM/YYYY HH24:MI:SS') AS dt_validacao,
                   TO_CHAR(ap.dt_proposta,'DD/MM/YYYY') AS dt_proposta,
                   TO_CHAR(ap.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   TO_CHAR(ap.dt_cancelado,'DD/MM/YYYY') AS dt_cancelado,
                   TO_CHAR((SELECT MAX(app.dt_prorrogacao)
                              FROM projetos.acao_preventiva_prorrogacao app
                             WHERE app.nr_ano      = ap.nr_ano
                               AND app.nr_ap       = ap.nr_ap
                               AND app.dt_exclusao IS NULL), 'DD/MM/YYYY') AS dt_prorrogacao,
                   p.procedimento
              FROM projetos.acao_preventiva ap
              JOIN projetos.divisoes d
                ON d.codigo = ap.cd_divisao
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = ap.cd_responsavel
			  JOIN projetos.usuarios_controledi uc2
                ON uc2.codigo = ap.cd_substituto
			  JOIN projetos.usuarios_controledi ucad
                ON ucad.codigo = ap.cd_usuario_inclusao				
              LEFT JOIN projetos.usuarios_controledi ucv
                ON ucv.codigo = ap.cd_usuario_validacao
              LEFT JOIN projetos.usuarios_controledi ucc
                ON ucc.codigo = ap.cd_usuario_cancelado
              JOIN projetos.processos p
                ON ap.cd_processo = p.cd_processo
             WHERE ap.nr_ano = ".intval($args['nr_ano'])."
               AND ap.nr_ap = ".intval($args['nr_ap']);
        $result = $this->db->query($qr_sql);
    }

    function gerar_pdf(&$result, $args=array())
    {
        $qr_sql = "
            SELECT funcoes.nr_ap(ap.nr_ano,ap.nr_ap) AS numero_cad_ap,
                   pr.procedimento AS processo,
                   d.codigo AS divisao,
                   uc.nome AS responsavel,
                   ap.potencial_nc,
                   ap.causa_nc,
                   ap.fonte_info,
                   ap.acao_proposta,
                   TO_CHAR(COALESCE(ap.dt_prazo_validacao_prorroga, ap.dt_prazo_validacao),'DD/MM/YYYY') AS dt_prazo_validacao,
                   TO_CHAR(ap.dt_implementacao,'DD/MM/YYYY') AS dt_implementacao,
                   TO_CHAR(ap.dt_validacao,'DD/MM/YYYY HH24:MI:SS') AS dt_validacao,
                   TO_CHAR(ap.dt_proposta,'DD/MM/YYYY') AS dt_proposta,
                   TO_CHAR(ap.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc2.nome AS nome_substituto,
                   --TO_CHAR(COALESCE(ap.dt_cancelado),'DD/MM/YYYY') AS dt_cancelado,
                   TO_CHAR((SELECT MAX(app.dt_prorrogacao)
                              FROM projetos.acao_preventiva_prorrogacao app
                             WHERE app.nr_ano      = ap.nr_ano
                               AND app.nr_ap       = ap.nr_ap
                               AND app.dt_exclusao IS NULL), 'DD/MM/YYYY') AS dt_prorrogacao
              FROM projetos.acao_preventiva ap
              JOIN projetos.divisoes d
                ON d.codigo = ap.cd_divisao
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = ap.cd_responsavel
			  LEFT JOIN projetos.usuarios_controledi uc2
                ON uc2.codigo = ap.cd_substituto
              LEFT JOIN projetos.usuarios_controledi ucv
                ON ucv.codigo = ap.cd_usuario_validacao
              LEFT JOIN projetos.usuarios_controledi ucc
                ON ucc.codigo = ap.cd_usuario_cancelado
              JOIN projetos.processos pr
               ON pr.cd_processo = ap.cd_processo
             WHERE ap.nr_ano = ".intval($args['nr_ano'])."
              AND  ap.nr_ap = ".intval($args['nr_ap']);
        $result = $this->db->query($qr_sql);
    }

    function validar(&$result, $args=array())
    {
        $retorno = 0;

        $qr_sql = "
			UPDATE projetos.acao_preventiva
               SET dt_validacao         = CURRENT_TIMESTAMP,
                   cd_usuario_validacao =  ".intval($args['usuario'])."
             WHERE cd_acao_preventiva = ".intval($args['cd_acao_preventiva']);
        $this->db->query($qr_sql);
	    $retorno = $args['numero_cad_ap'];

        return $retorno;
    }

    function cancelar(&$result, $args=array())
    {
        $retorno = 0;

        $qr_sql = "
			UPDATE projetos.acao_preventiva
			   SET dt_cancelado         = CURRENT_TIMESTAMP,
				   cd_usuario_cancelado =  ".intval($args['usuario'])."
			 WHERE cd_acao_preventiva = ".intval($args['cd_acao_preventiva']);
        $this->db->query($qr_sql);
	    $retorno = $args['numero_cad_ap'];

        return $retorno;
    }

    function salvar(&$result, $args=array())
    {
        $retorno = 0;

        if(intval($args['cd_acao_preventiva']) > 0)
        {
            $qr_sql = "
				UPDATE projetos.acao_preventiva
				   SET cd_processo            = ".intval($args['processo']).",
					   potencial_nc           = ".(trim($args['potencial_nc']) == "" ? "DEFAULT" : "'".$args['potencial_nc']."'").",
					   causa_nc               = ".(trim($args['causa_nc']) == "" ? "DEFAULT" : "'".$args['causa_nc']."'").",
					   fonte_info             = ".(trim($args['fonte_info']) == "" ? "DEFAULT" : "'".$args['fonte_info']."'").",
					   acao_proposta          = ".(trim($args['acao_proposta']) == "" ? "DEFAULT" : "'".$args['acao_proposta']."'").",
					   dt_proposta            = ".(trim($args['dt_proposta']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_proposta']."','DD/MM/YYYY')").",
					   cd_usuario_atualizacao = ".intval($args['usuario']).",
					   dt_implementacao       = ".(trim($args['dt_implementacao']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_implementacao']."','DD/MM/YYYY')").",
					   dt_prazo_validacao       = ".(trim($args['dt_prazo_validacao']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_prazo_validacao']."','DD/MM/YYYY')")."
				 WHERE cd_acao_preventiva = ".intval($args['cd_acao_preventiva']);

            $this->db->query($qr_sql);
			$retorno = $args['numero_cad_ap'];
        }
        else
        {
            $qr_sql = "
                INSERT INTO projetos.acao_preventiva
                       (
                         cd_processo,
                         potencial_nc,
                         causa_nc,
                         fonte_info,
                         acao_proposta,
                         dt_proposta,
                         cd_divisao,
                         cd_responsavel,
						 cd_substituto,
                         cd_usuario_inclusao,
						 cd_usuario_atualizacao
                       )
                  VALUES
                       (
                         ".intval($args['processo']).",
                         ".(trim($args['potencial_nc']) == "" ? "DEFAULT" : "'".$args['potencial_nc']."'").",
                         ".(trim($args['causa_nc']) == "" ? "DEFAULT" : "'".$args['causa_nc']."'").",
                         ".(trim($args['fonte_info']) == "" ? "DEFAULT" : "'".$args['fonte_info']."'").",
                         ".(trim($args['acao_proposta']) == "" ? "DEFAULT" : "'".$args['acao_proposta']."'").",
                         ".(trim($args['dt_proposta']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_proposta']."','DD/MM/YYYY')").",
                         '".(trim($args['gerencia']))."',
                         ".intval($args['usuario']).",
                         ".intval($args['cd_substituto']).",
                         ".intval($args['usuario_inc']).",
                         ".intval($args['usuario_inc'])."
                       )";
            $this->db->query($qr_sql);

            $qr_sql = "
                SELECT nr_ano,
                       nr_ap
                  FROM projetos.acao_preventiva
                 WHERE cd_usuario_inclusao = ".intval($args['usuario_inc'])."
                 ORDER BY dt_inclusao DESC
                 LIMIT 1";

            $result = $this->db->query($qr_sql);
			$ar_reg = $result->row_array();
			$retorno = $ar_reg['nr_ano'].'/'.$ar_reg['nr_ap'];
        }

        return $retorno;
    }

    function salvar_acompanhamento(&$result, $args=array())
    {
        $qr_sql = "
			INSERT INTO projetos.acao_preventiva_acompanhamento
				   (
					 nr_ap,
					 nr_ano,
					 cd_usuario_inclusao,
					 acompanhamento
				   )
			  VALUES
				   (
					 ".intval($args['nr_ap']).",
					 ".intval($args['nr_ano']).",
					 ".intval($args['usuario']).",
					 ".(trim($args['acompanhamento']) == "" ? "DEFAULT" : str_escape($args['acompanhamento']))."
				   )";

        $this->db->query($qr_sql);
    }

    function acompanhamento(&$result, $args=array())
    {
        $qr_sql = "
			SELECT TO_CHAR(COALESCE(dt_inclusao),'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   acompanhamento,
				   u.nome AS usuario
			  FROM projetos.acao_preventiva_acompanhamento
			  JOIN projetos.usuarios_controledi u
				ON u.codigo = cd_usuario_inclusao
			 WHERE nr_ano = ".intval($args['nr_ano'])."
			   AND nr_ap  = ".intval($args['nr_ap'])."
			   AND dt_exclusao IS NULL";

        $result = $this->db->query($qr_sql);
    }

    function salvar_prorrogacao(&$result, $args=array())
    {
        $qr_sql = "
			INSERT INTO projetos.acao_preventiva_prorrogacao
				   (
					 nr_ap,
					 nr_ano,
					 cd_usuario_inclusao,
					 motivo,
					 dt_prorrogacao
				   )
			  VALUES
				   (
					 ".intval($args['nr_ap']).",
					 ".intval($args['nr_ano']).",
					 ".intval($args['usuario']).",
					 ".(trim($args['motivo']) == "" ? "DEFAULT" : str_escape($args['motivo'])).",
					 ".(trim($args['dt_prorrogacao']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_prorrogacao']."','DD/MM/YYYY')")."
				   )";

        $this->db->query($qr_sql);
    }

    function prorrogacao(&$result, $args=array())
    {
        $qr_sql = "
			SELECT TO_CHAR(COALESCE(dt_prorrogacao),'DD/MM/YYYY') AS dt_prorrogacao,
				   motivo
			  FROM projetos.acao_preventiva_prorrogacao
			 WHERE nr_ano = ".intval($args['nr_ano'])."
			   AND nr_ap  = ".intval($args['nr_ap'])."
			   AND dt_exclusao IS NULL";

        $result = $this->db->query($qr_sql);
    }

    function combo_processo(&$result, $args=array())
    {
        $qr_sql = "
			SELECT cd_processo AS value,
				   procedimento AS text
			  FROM projetos.processos
			 ORDER BY text";

        $result = $this->db->query($qr_sql);
    }

    function implementacao(&$result, $args=array())
    {
        $qr_sql ="
            SELECT dt_implementacao
              FROM projetos.acao_preventiva
             WHERE nr_ano = ".intval($args['nr_ano'])."
               AND nr_ap  = ".intval($args['nr_ap'])."
               AND dt_implementacao IS NULL";

        $result = $this->db->query($qr_sql);
    }

    function cancelamento(&$result, $args=array())
    {
        $qr_sql ="
            SELECT dt_implementacao
              FROM projetos.acao_preventiva
             WHERE nr_ano = ".intval($args['nr_ano'])."
               AND nr_ap  = ".intval($args['nr_ap'])."
               AND dt_cancelado IS NULL";

        $result = $this->db->query($qr_sql);
    }
	
	function prorrogar_validacao(&$result, $args=array())
	{
		$qr_sql ="
            UPDATE projetos.acao_preventiva
			   SET dt_prazo_validacao_prorroga = TO_DATE('".$args['dt_prazo_validacao_prorroga']."','DD/MM/YYYY'),
				   cd_usuario_atualizacao      = ".intval($args['cd_usuario']).",
				   dt_atualizacao              = CURRENT_TIMESTAMP
			 WHERE cd_acao_preventiva = ".intval($args['cd_acao_preventiva']);

        $result = $this->db->query($qr_sql);
	}

    public function salvar_anexo($cd_acao_preventiva, $args = array())
    {
        $qr_sql ="
            INSERT INTO projetos.acao_preventiva_anexo
                 (
                    cd_acao_preventiva, 
                    arquivo, 
                    arquivo_nome, 
                    cd_usuario_inclusao
                 )
            VALUES 
                 (
                    ".intval($cd_acao_preventiva).",
                    '".trim($args['arquivo'])."',
                    '".trim($args['arquivo_nome'])."',
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);
    }

    public function listar_anexo($cd_acao_preventiva)
    {
        $qr_sql = "
            SELECT cd_acao_preventiva_anexo, 
                   cd_usuario_inclusao,
                   arquivo, 
                   arquivo_nome, 
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
              FROM projetos.acao_preventiva_anexo
             WHERE cd_acao_preventiva = ".intval($cd_acao_preventiva)."
               AND dt_exclusao        IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function excluir_anexo($cd_acao_preventiva_anexo, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.acao_preventiva_anexo
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_acao_preventiva_anexo = ".intval($cd_acao_preventiva_anexo).";";

        $this->db->query($qr_sql);
    }
}
?>