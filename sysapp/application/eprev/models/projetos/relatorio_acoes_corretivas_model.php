<?php
class relatorio_acoes_corretivas_model extends Model
{
    function __construct()
	{
		parent::Model();
	}

    function quadro_resumo_conformidade( &$result )
	{
        $qr_sql = "
            	SELECT SUM(t.qt_aberta) AS qt_aberta,
				       SUM(t.qt_nao_implementada_prazo) AS qt_nao_implementada_prazo,
				       SUM(t.qt_nao_implementada_fora) AS qt_nao_implementada_fora,
				       SUM(t.qt_implementada_prazo) AS qt_implementada_prazo,
				       SUM(t.qt_implementada_fora) AS qt_implementada_fora
				FROM (SELECT COUNT(*) AS qt_aberta,
				             0 AS qt_nao_implementada_prazo,
				             0 AS qt_nao_implementada_fora,
				             0 AS qt_implementada_prazo,
                             0 AS qt_implementada_fora
				        FROM projetos.nao_conformidade nc
				       WHERE nc.dt_cancelamento IS NULL

					   UNION

					  SELECT 0 AS qt_aberta,
				             COUNT(*) AS qt_nao_implementada_prazo,
				             0 AS qt_nao_implementada_fora,
				             0 AS qt_implementada_prazo,
                             0 AS qt_implementada_fora
				        FROM projetos.nao_conformidade nc
				        LEFT JOIN projetos.acao_corretiva ac
				          ON ac.cd_nao_conformidade = nc.cd_nao_conformidade
                       WHERE COALESCE(COALESCE(ac.dt_prorrogada,ac.dt_prop_imp),(nc.dt_cadastro + '15 days'::interval)) > CURRENT_DATE
				         AND ac.dt_efe_imp                             IS NULL
				         AND nc.dt_cancelamento IS NULL

                       UNION

				      SELECT 0 AS qt_aberta,
				             0 AS qt_nao_implementada_prazo,
				             COUNT(*) AS qt_nao_implementada_fora,
				             0 AS qt_implementada_prazo,
                             0 AS qt_implementada_fora
				        FROM projetos.nao_conformidade nc
				        LEFT JOIN projetos.acao_corretiva ac
				          ON ac.cd_nao_conformidade = nc.cd_nao_conformidade
                       WHERE COALESCE(COALESCE(ac.dt_prorrogada,ac.dt_prop_imp),(nc.dt_cadastro + '15 days'::interval)) < CURRENT_DATE
				         AND ac.dt_efe_imp                             IS NULL
     				     AND nc.dt_cancelamento IS NULL
                       UNION

				      SELECT 0 AS qt_aberta,
				             0 AS qt_nao_implementada_prazo,
				             0 AS qt_implementada_prazo,
				             COUNT(*) AS qt_implementada_prazo,
                             0 AS qt_implementada_fora
				        FROM projetos.nao_conformidade nc
				        JOIN projetos.acao_corretiva ac
				          ON ac.cd_nao_conformidade = nc.cd_nao_conformidade
				       WHERE COALESCE(ac.dt_prorrogada,ac.dt_prop_imp) >= ac.dt_efe_imp
                         AND nc.dt_cancelamento IS NULL
				       UNION

				      SELECT 0 AS qt_aberta,
				             0 AS qt_nao_implementada_prazo,
				             0 AS qt_nao_implementada_fora,
				             0 AS qt_implementada_prazo,
                             COUNT(*) AS qt_implementada_fora
				        FROM projetos.nao_conformidade nc
				        JOIN projetos.acao_corretiva ac
				          ON ac.cd_nao_conformidade = nc.cd_nao_conformidade
				       WHERE COALESCE(ac.dt_prorrogada,ac.dt_prop_imp) < ac.dt_efe_imp
				         AND nc.dt_cancelamento IS NULL) t
            ";

        $result = $this->db->query($qr_sql);
    }

    function quadro_resumo_corretiva( &$result )
    {
        $qr_sql = "
            SELECT SUM(t.qt_ac_apresentada_prazo) AS qt_ac_apresentada_prazo,
				       SUM(t.qt_ac_apresentada_fora) AS qt_ac_apresentada_fora,
				       SUM(t.qt_ac_nao_apresentada_prazo) AS qt_ac_nao_apresentada_prazo,
				       SUM(t.qt_ac_nao_apresentada_fora) AS qt_ac_nao_apresentada_fora,
                       (SUM(t.qt_ac_apresentada_prazo)+SUM(t.qt_ac_apresentada_fora)+SUM(t.qt_ac_nao_apresentada_prazo)+SUM(t.qt_ac_nao_apresentada_fora)) AS qt_ac_total
				  FROM (SELECT COUNT(*) AS qt_ac_apresentada_prazo, -- COM NO PRAZO
                               0 AS qt_ac_apresentada_fora,
                               0 AS qt_ac_nao_apresentada_prazo,
                               0 AS qt_ac_nao_apresentada_fora
				          FROM projetos.nao_conformidade nc
				          JOIN projetos.acao_corretiva ac
				            ON ac.cd_nao_conformidade = nc.cd_nao_conformidade
                         WHERE ac.dt_apres <= ac.dt_limite_apres
                           AND nc.dt_cancelamento IS NULL

                         UNION

				        SELECT 0 AS qt_ac_apresentada_prazo,
                               COUNT(*) AS qt_ac_apresentada_fora, -- COM FORA DO PRAZO
                               0 AS qt_ac_nao_apresentada_prazo,
                               0 AS qt_ac_nao_apresentada_fora
				          FROM projetos.nao_conformidade nc
				          JOIN projetos.acao_corretiva ac
				            ON ac.cd_nao_conformidade = nc.cd_nao_conformidade
                         WHERE ac.dt_limite_apres < ac.dt_apres
                           AND nc.dt_cancelamento IS NULL

                         UNION

				        SELECT 0 AS qt_ac_apresentada_prazo,
                               0 AS qt_ac_apresentada_fora,
                               COUNT(*) AS qt_ac_nao_apresentada_prazo, -- SEM FUTURO
                               0 AS qt_ac_nao_apresentada_fora
				          FROM projetos.nao_conformidade nc
				          LEFT JOIN projetos.acao_corretiva ac
				            ON ac.cd_nao_conformidade = nc.cd_nao_conformidade
                         WHERE COALESCE(ac.dt_limite_apres, (nc.dt_cadastro + '15 days'::interval)) >= CURRENT_DATE
                           AND ac.dt_apres IS NULL
                           AND nc.dt_cancelamento IS NULL

                         UNION

				        SELECT 0 AS qt_ac_apresentada_prazo,
                               0 AS qt_ac_apresentada_fora,
                               0 AS qt_ac_nao_apresentada_prazo,
                               COUNT(*) AS qt_ac_nao_apresentada_fora -- SEM VENCIDO
				          FROM projetos.nao_conformidade nc
				          LEFT JOIN projetos.acao_corretiva ac
				            ON ac.cd_nao_conformidade = nc.cd_nao_conformidade
                         WHERE COALESCE(ac.dt_limite_apres, (nc.dt_cadastro + '15 days'::interval)) < CURRENT_DATE
                           AND ac.dt_apres IS NULL
                           AND nc.dt_cancelamento IS NULL) t 
            ";

        $result = $this->db->query($qr_sql);
    }

    function corretivas_com_prazo_vencido( &$result )
    {
        $qr_sql = "
                SELECT nc.cd_nao_conformidade,
				       TO_CHAR(nc.dt_cadastro,'DD/MM/YYYY') AS dt_abertura,
				       TO_CHAR(ac.dt_apres,'DD/MM/YYYY') AS dt_apresenta,
				       TO_CHAR(COALESCE(ac.dt_limite_apres, (nc.dt_cadastro + '15 days'::interval)),'DD/MM/YYYY') AS dt_apresenta_limite,
				       uc.nome AS ds_responsavel,
				       p.cod_responsavel AS area_responsavel,
				       (nc.dt_cadastro + '15 days'::interval) , ac.dt_apres
				  FROM projetos.nao_conformidade nc
				  LEFT JOIN projetos.acao_corretiva ac
				    ON ac.cd_nao_conformidade = nc.cd_nao_conformidade
				  JOIN projetos.usuarios_controledi uc
				    ON uc.codigo = nc.cd_responsavel
				  JOIN projetos.processos p
				    ON nc.cd_processo = p.cd_processo
				 WHERE COALESCE(ac.dt_limite_apres, (nc.dt_cadastro + '15 days'::interval)) < CURRENT_DATE
				   AND ac.dt_apres        IS NULL
				   AND nc.dt_cancelamento IS NULL
				 ORDER BY nc.cd_nao_conformidade DESC
            ";

        $result = $this->db->query($qr_sql);
    }

    function corretivas_fora_prazo ( &$result )
    {
        $qr_sql = "
            SELECT nc.cd_nao_conformidade,
				       TO_CHAR(nc.dt_cadastro,'DD/MM/YYYY') AS dt_abertura,
				       TO_CHAR(ac.dt_apres,'DD/MM/YYYY') AS dt_apresenta,
				       TO_CHAR(ac.dt_limite_apres,'DD/MM/YYYY') AS dt_apresenta_limite,
				       TO_CHAR(ac.dt_prop_imp,'DD/MM/YYYY') AS dt_proposta,
				       TO_CHAR(ac.dt_prorrogada,'DD/MM/YYYY') AS dt_prorrogada,
				       TO_CHAR(ac.dt_efe_imp,'DD/MM/YYYY') AS dt_implementacao,
				       uc.nome AS ds_responsavel,
				       p.cod_responsavel AS area_responsavel,
				       (nc.dt_cadastro + '15 days'::interval) , ac.dt_apres
				  FROM projetos.nao_conformidade nc
				  JOIN projetos.acao_corretiva ac
				    ON ac.cd_nao_conformidade = nc.cd_nao_conformidade
				  JOIN projetos.usuarios_controledi uc
				    ON uc.codigo = nc.cd_responsavel
				  JOIN projetos.processos p
				    ON nc.cd_processo = p.cd_processo
				 WHERE CAST(ac.dt_limite_apres AS DATE) < CAST(ac.dt_apres AS DATE)
				   AND nc.dt_cancelamento IS NULL
				 ORDER BY nc.cd_nao_conformidade DESC
            ";

        $result = $this->db->query($qr_sql);
    }

    function corretivas_imple_vencido ( &$result )
    {
        $qr_sql = "
            SELECT nc.cd_nao_conformidade,
				       TO_CHAR(nc.dt_cadastro,'DD/MM/YYYY') AS dt_abertura,
				       TO_CHAR(ac.dt_prop_imp,'DD/MM/YYYY') AS dt_proposta,
				       TO_CHAR(ac.dt_prorrogada,'DD/MM/YYYY') AS dt_prorrogada,
				       TO_CHAR(ac.dt_efe_imp,'DD/MM/YYYY') AS dt_implementacao,
				       uc.nome AS ds_responsavel,
				       p.cod_responsavel AS area_responsavel
				  FROM projetos.nao_conformidade nc
				  JOIN projetos.acao_corretiva ac
				    ON ac.cd_nao_conformidade = nc.cd_nao_conformidade
				  JOIN projetos.usuarios_controledi uc
				    ON uc.codigo = nc.cd_responsavel
				  JOIN projetos.processos p
				    ON nc.cd_processo = p.cd_processo
				 WHERE COALESCE(ac.dt_prorrogada,ac.dt_prop_imp) < CURRENT_DATE
				   AND ac.dt_efe_imp                             IS NULL
				   AND nc.dt_cancelamento IS NULL
				 ORDER BY nc.cd_nao_conformidade DESC
            ";

        $result = $this->db->query($qr_sql);
    }

    function corretivas_imple_fora_prazo ( &$result )
    {
        $qr_sql = "
            SELECT nc.cd_nao_conformidade,
				       TO_CHAR(nc.dt_cadastro,'DD/MM/YYYY') AS dt_abertura,
				       TO_CHAR(ac.dt_prop_imp,'DD/MM/YYYY') AS dt_proposta,
				       TO_CHAR(ac.dt_prorrogada,'DD/MM/YYYY') AS dt_prorrogada,
				       TO_CHAR(ac.dt_efe_imp,'DD/MM/YYYY') AS dt_implementacao,
				       uc.nome AS ds_responsavel,
				       p.cod_responsavel AS area_responsavel
				  FROM projetos.nao_conformidade nc
				  JOIN projetos.acao_corretiva ac
				    ON ac.cd_nao_conformidade = nc.cd_nao_conformidade
				  JOIN projetos.usuarios_controledi uc
				    ON uc.codigo = nc.cd_responsavel
				  JOIN projetos.processos p
				    ON nc.cd_processo = p.cd_processo
				 WHERE COALESCE(ac.dt_prorrogada,ac.dt_prop_imp) < ac.dt_efe_imp
				   AND nc.dt_cancelamento IS NULL
				 ORDER BY nc.cd_nao_conformidade DESC
            ";

        $result = $this->db->query($qr_sql);
    }

}
?>