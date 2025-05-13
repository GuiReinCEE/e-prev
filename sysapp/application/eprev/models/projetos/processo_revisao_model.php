<?php

class Processo_revisao_model extends Model
{
	public function get_combo_processo($fl_gerencia, $cd_gerencia)
    {
    	$qr_sql = "
    		SELECT cd_processo AS value,
			       procedimento AS text
			  FROM projetos.processos
			 WHERE dt_ini_vigencia <= CURRENT_DATE AND COALESCE(dt_fim_vigencia, CURRENT_DATE) >= CURRENT_DATE
			 ".($fl_gerencia ? "AND (CASE WHEN cod_responsavel = 'CQ' THEN 'GC' ELSE cod_responsavel END) = ".str_escape($cd_gerencia) : "")."
			 ORDER BY procedimento;";

       	return $this->db->query($qr_sql)->result_array();
    }

    public function listar($fl_gerencia, $cd_usuario, $args = array())
    {
    	$qr_sql = "
    		SELECT pr.cd_processo_revisao,
			       p.procedimento,
				   TO_CHAR(pr.dt_inclusao, 'DD/MM/YYYY HH24:MM:SS') AS dt_inclusao,
			       TO_CHAR(pr.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
			       TO_CHAR(pr.dt_referencia, 'YYYY') AS ano,
			       TO_CHAR(pr.dt_referencia, 'MM') AS mes,
			       TO_CHAR(pr.dt_limite, 'DD/MM/YYYY') AS dt_limite,
			       TO_CHAR(pr.dt_revisao, 'DD/MM/YYYY HH24:MI:SS') AS dt_revisao,
			       funcoes.get_usuario_nome(pr.cd_usuario_revisao) AS usuario_revisao,
			       CASE WHEN pr.fl_alterado = 'S' THEN 'Sim'
			            WHEN pr.fl_alterado = 'N' THEN 'Não'
			            ELSE ''
			       END AS alterado,
			       CASE WHEN pr.fl_alterado = 'S' THEN 'label label-warning'
			            WHEN pr.fl_alterado = 'N' THEN 'label label-success'
			            ELSE ''
			       END AS class_alterado,
			       pr.observacao
			  FROM projetos.processos_revisao pr
			  JOIN projetos.processos p
			    ON p.cd_processo = pr.cd_processo
			 WHERE pr.dt_exclusao IS NULL
			   ".($fl_gerencia ? "AND (pr.cd_responsavel = ".intval($cd_usuario)." OR pr.cd_substituto = ".intval($cd_usuario).")" : "")."
			   ".(trim($args['cd_processo']) != '' ? "AND pr.cd_processo = ".intval($args['cd_processo']) : "")."
			   ".(trim($args['fl_revisado']) == 'S' ? "AND pr.dt_revisao IS NOT NULL" : "")."
			   ".(trim($args['fl_revisado']) == 'N' ? "AND pr.dt_revisao IS NULL" : "")."
			   ".(trim($args['referencia']) != '' ? "AND  TO_CHAR(pr.dt_referencia, 'MM/YYYY') = '".(trim($args['referencia']))."'" : "")." 
			 ORDER BY pr.dt_referencia DESC, 
			          p.procedimento;";

		return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_processo_revisao, $fl_gerencia, $cd_usuario)
    {
    	$qr_sql = "
    		SELECT pr.cd_processo_revisao,
			       p.procedimento,
			       TO_CHAR(pr.dt_inclusao, 'DD/MM/YYYY HH24:MM:SS') AS dt_inclusao,
			       TO_CHAR(pr.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
			       TO_CHAR(pr.dt_limite, 'DD/MM/YYYY') AS dt_limite,
			       TO_CHAR(pr.dt_revisao, 'DD/MM/YYYY HH24:MI:SS') AS dt_revisao,
			       funcoes.get_usuario_nome(pr.cd_usuario_revisao) AS usuario_revisao,
			       pr.fl_alterado,
			       pr.observacao
			  FROM projetos.processos_revisao pr
			  JOIN projetos.processos p
			    ON p.cd_processo = pr.cd_processo
			 WHERE pr.cd_processo_revisao = ".intval($cd_processo_revisao)."
			   ".($fl_gerencia ? "AND (pr.cd_responsavel = ".intval($cd_usuario)." OR pr.cd_substituto = ".intval($cd_usuario).")" : "").";";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function get_responsavel($cd_processo_revisao, $cd_usuario)
    {
    	$qr_sql = "
    		SELECT COUNT(*) AS tl
    		  FROM projetos.processos_revisao
    		 WHERE cd_processo_revisao = ".intval($cd_processo_revisao)."
    		   AND (cd_responsavel = ".intval($cd_usuario)." OR cd_substituto = ".intval($cd_usuario).")";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function atualizar($cd_processo_revisao, $args = array())
    {
    	$qr_sql = "
    		UPDATE projetos.processos_revisao
    		   SET fl_alterado          = ".str_escape($args['fl_alterado']).",
    		       observacao           = ".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
    		       cd_usuario_alteracao = ".intval($args['cd_usuario']).",
    		       cd_usuario_revisao   = ".intval($args['cd_usuario']).",
    		       dt_alteracao         = CURRENT_TIMESTAMP,
    		       dt_revisao           = CURRENT_TIMESTAMP
    		 WHERE cd_processo_revisao = ".$cd_processo_revisao.";";

    	$this->db->query($qr_sql);
    }

    public function listar_historico($cd_processo)
    {
    	$qr_sql = "
    		SELECT pr.cd_processo_revisao,
			       TO_CHAR(pr.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
			       TO_CHAR(pr.dt_limite, 'DD/MM/YYYY') AS dt_limite,
			       TO_CHAR(pr.dt_revisao, 'DD/MM/YYYY HH24:MI:SS') AS dt_revisao,
			       funcoes.get_usuario_nome(pr.cd_usuario_revisao) AS ds_usuario_revisao,
			       CASE WHEN pr.fl_alterado = 'S' THEN 'Sim'
			            WHEN pr.fl_alterado = 'N' THEN 'Não'
			            ELSE ''
			       END AS alterado,
			       CASE WHEN pr.fl_alterado = 'S' THEN 'label label-warning'
			            WHEN pr.fl_alterado = 'N' THEN 'label label-success'
			            ELSE ''
			       END AS class_alterado,
			       pr.observacao
			  FROM projetos.processos_revisao pr
			 WHERE pr.dt_exclusao IS NULL
			   AND pr.cd_processo = ".intval($cd_processo)."
			   AND pr.dt_revisao IS NOT NULL
			 ORDER BY pr.dt_referencia DESC;";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function get_referencia()
    {
    	$qr_sql = "
    		SELECT TO_CHAR(dt_referencia, 'MM/YYYY') AS value,
			       TO_CHAR(dt_referencia, 'MM/YYYY') AS text
			  FROM projetos.processos_revisao 
			 WHERE dt_exclusao IS NULL
			 GROUP BY dt_referencia
			 ORDER BY dt_referencia DESC;";

        return $this->db->query($qr_sql)->result_array();
    }
}