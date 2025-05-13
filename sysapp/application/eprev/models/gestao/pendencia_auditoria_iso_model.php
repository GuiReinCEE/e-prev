<?php
class pendencia_auditoria_iso_model extends Model
{

    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "SELECT pa.cd_pendencia_auditoria_iso,
                          pa.cd_pendencia_auditoria_iso_tipo,
                          TO_CHAR(pa.dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
                          pa.nr_contatacao,
                          pa.fl_impacto,
                          pa.cd_processo,
                          pa.cd_responsavel,
                          pa.cd_gerencia,
                          pa.ds_item,
                          pat.ds_pendencia_auditoria_iso_tipo,
                          pp.procedimento,
                          u.nome AS nome_usuario,
                          d.nome AS nome_gerencia,
                          pa.dt_encerrada
                     FROM gestao.pendencia_auditoria_iso pa
                     JOIN projetos.divisoes d
                       ON d.codigo = pa.cd_gerencia
                     JOIN gestao.pendencia_auditoria_iso_tipo pat
                       ON pat.cd_pendencia_auditoria_iso_tipo = pa.cd_pendencia_auditoria_iso_tipo
                     JOIN projetos.processos pp
                       ON pp.cd_processo = pa.cd_processo
                     JOIN projetos.usuarios_controledi u
                       ON u.codigo = pa.cd_responsavel
                    WHERE pa.dt_exclusao IS null
                    ".($args["cd_processo"] != '' ? "AND pa.cd_processo =".intval($args['cd_processo']) : '')."
                    ".(((trim($args['dt_inicial']) != "") and  (trim($args['dt_final']) != "")) ? " AND DATE_TRUNC('day', pa.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inicial']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_final']."', 'DD/MM/YYYY')" : "")."
                    ".($args["cd_pendencia_auditoria_iso_tipo"] != '' ? "AND pa.cd_pendencia_auditoria_iso_tipo =".intval($args['cd_pendencia_auditoria_iso_tipo']) : '')."
                    ".($args["fl_impacto"] != '' ? "AND pa.fl_impacto ='".trim($args['fl_impacto'])."'" : '')."
                    ".($args["cd_gerencia"] != '' ? "AND pa.cd_gerencia ='".trim($args['cd_gerencia'])."'" : '')."
                    ".(($args["cd_responsavel"] != '' AND $args["cd_responsavel"] != 0) ? "AND pa.cd_responsavel =".intval($args['cd_responsavel']) : '')."
                    ".($args["fl_situacao"] == 'A' ? "AND pa.dt_encerrada IS NULL" : '')."
                    ".($args["fl_situacao"] == 'E' ? "AND pa.dt_encerrada IS NOT NULL" : '');
        #echo '<pre>'.$qr_sql.'<pre>';
        $result = $this->db->query($qr_sql);
    }
    
    function lista_iso_acompanhamento(&$result, $args=array())
    {
        $qr_sql = "SELECT TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') || ': ' || ds_pendencia_auditoria_iso_acompanhamento AS text
                     FROM gestao.pendencia_auditoria_iso_acompanhamento
                    WHERE cd_pendencia_auditoria_iso = ".$args['cd_pendencia_auditoria_iso']."
                      AND dt_exclusao IS NULL
                    ORDER BY ds_pendencia_auditoria_iso_acompanhamento DESC";
        
        $result = $this->db->query($qr_sql);
    }

    function carrega(&$result, $args=array())
    {
        $qr_sql = "SELECT cd_pendencia_auditoria_iso,
                          cd_pendencia_auditoria_iso_tipo,
                          TO_CHAR(dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
                          nr_contatacao,
                          fl_impacto,
                          cd_processo,
                          cd_responsavel,
                          cd_gerencia,
                          ds_item,
                          dt_encerrada
                     FROM gestao.pendencia_auditoria_iso
                    WHERE cd_pendencia_auditoria_iso = ".intval($args['cd_pendencia_auditoria_iso']);

        $result = $this->db->query($qr_sql);
    }

    function lista_auditoria(&$result, $args=array())
    {
        $qr_sql = "SELECT cd_pendencia_auditoria_iso_tipo AS VALUE,
                          ds_pendencia_auditoria_iso_tipo AS TEXT
                     FROM gestao.pendencia_auditoria_iso_tipo";

        $result = $this->db->query($qr_sql);
    }

    function lista_processo(&$result, $args=array())
    {
        $qr_sql = "
                SELECT cd_processo AS value,
                       procedimento AS text
                  FROM projetos.processos
                 ORDER BY text
		         ";

        $result = $this->db->query($qr_sql);
    }

    function lista_gerencia(&$result, $args=array())
    {
        $qr_sql = "
                SELECT codigo AS value,
                       nome AS text
                  FROM projetos.divisoes
                 WHERE tipo NOT IN ('OUT', 'CON')
                 ORDER BY text
		         ";

        $result = $this->db->query($qr_sql);
    }

    function salvar(&$result, $args=array())
    {
        $retorno = 0;

        if (intval($args['cd_pendencia_auditoria_iso']) > 0) {
            $qr_sql = "
                    UPDATE gestao.pendencia_auditoria_iso
                       SET cd_pendencia_auditoria_iso_tipo = " . intval($args['cd_pendencia_auditoria_iso_tipo']) . ",
                           nr_contatacao                   = " . intval($args['nr_contatacao']) . ",
                           fl_impacto                      = " . (trim($args['fl_impacto']) == "" ? "DEFAULT" : "'" . (trim($args['fl_impacto'])) . "'") . ",
                           cd_processo                     = " . (trim($args['cd_processo']) == "" ? "DEFAULT" : $args['cd_processo']) . ",
                           cd_responsavel                  = " . (trim($args['cd_responsavel']) == "" ? "DEFAULT" : $args['cd_responsavel']) . ",
                           cd_gerencia                     = " . (trim($args['cd_gerencia']) == "" ? "DEFAULT" : "'". trim($args['cd_gerencia']). "'") . ",
                           ds_item                         = " . (trim($args['ds_item']) == "" ? "DEFAULT" : "'" . $args['ds_item'] . "'") . ",
                           dt_alteracao                    = CURRENT_TIMESTAMP,
                           cd_usuario_alteracao            =  " . (trim($args['cd_usuario_inclusao']) == "" ? "DEFAULT" : $args['cd_usuario_inclusao']) . "
                     WHERE cd_pendencia_auditoria_iso = " . intval($args['cd_pendencia_auditoria_iso']) . "
                ";

            $this->db->query($qr_sql);
            $retorno = $args['cd_pendencia_auditoria_iso'];
        } 
        else
        {
            $new_id = intval($this->db->get_new_id("gestao.pendencia_auditoria_iso", "cd_pendencia_auditoria_iso"));
            $retorno = $new_id;
            $qr_sql = "
                INSERT INTO gestao.pendencia_auditoria_iso
                       (
                         cd_pendencia_auditoria_iso,
                         cd_pendencia_auditoria_iso_tipo,
                         nr_contatacao,
                         fl_impacto,
                         cd_processo,
                         cd_responsavel,
                         cd_gerencia,
                         ds_item,
                         cd_usuario_inclusao,
                         dt_inclusao
                       )
                  VALUES
                       (
                         " . $new_id . ",
                         " . intval($args['cd_pendencia_auditoria_iso_tipo']) . ",
                         " . intval($args['nr_contatacao']) . ",
                         " . (trim($args['fl_impacto']) == "" ? "DEFAULT" : "'" . (trim($args['fl_impacto'])) . "'") . ",
                         " . (trim($args['cd_processo']) == "" ? "DEFAULT" : $args['cd_processo']) . ",
                         " . (trim($args['cd_responsavel']) == "" ? "DEFAULT" : $args['cd_responsavel']) . ",
                         " . (trim($args['cd_gerencia']) == "" ? "DEFAULT" : "'". trim($args['cd_gerencia']). "'") . ",
                         " . (trim($args['ds_item']) == "" ? "DEFAULT" : "'" . trim($args['ds_item']) . "'") . ",
                         " . (trim($args['cd_usuario_inclusao']) == "" ? "DEFAULT" : $args['cd_usuario_inclusao']) . ",
                         CURRENT_TIMESTAMP
                       )
                ";

            $this->db->query($qr_sql);
        }

        return $retorno;
    }

    function salva_acompanhamento(&$result, $args=array())
    {
        $new_id = intval($this->db->get_new_id("gestao.pendencia_auditoria_iso_acompanhamento", "cd_pendencia_auditoria_iso_acompanhamento"));

        $qr_sql = "
                INSERT INTO gestao.pendencia_auditoria_iso_acompanhamento
                       (
                         cd_pendencia_auditoria_iso_acompanhamento,
                         ds_pendencia_auditoria_iso_acompanhamento,
                         cd_pendencia_auditoria_iso,
                         cd_usuario_inclusao
                       )
                  VALUES
                       (
                         " . $new_id . ",
                         " . (trim($args['ds_pendencia_auditoria_iso_acompanhamento']) == "" ? "DEFAULT" : "'".$args['ds_pendencia_auditoria_iso_acompanhamento']."'") . ",
                         " . intval($args['cd_pendencia_auditoria_iso']) . ",
                         " . (trim($args['cd_usuario_inclusao']) == "" ? "DEFAULT" : $args['cd_usuario_inclusao']) . "
                       )
                ";

            $this->db->query($qr_sql);
    }

    function lista_acompanhamento(&$result, $args=array())
    {
        $qr_sql = "SELECT paa.ds_pendencia_auditoria_iso_acompanhamento ,
                          u.nome,
                          TO_CHAR(paa.dt_inclusao,'DD/MM/YYYY') AS dt_inclusao
                     FROM gestao.pendencia_auditoria_iso_acompanhamento paa
                     JOIN projetos.usuarios_controledi u
                       ON u.codigo = paa.cd_usuario_inclusao
                    WHERE cd_pendencia_auditoria_iso = ". intval($args['cd_pendencia_auditoria_iso']) ;

        $result = $this->db->query($qr_sql);
    }

    function encerrar(&$result, $args=array())
    {
        $qr_sql = "UPDATE gestao.pendencia_auditoria_iso
                      SET dt_encerrada         = CURRENT_TIMESTAMP,
                          cd_usuario_encerrada = ".intval($args['cd_usuario_encerrada'])."
                    WHERE cd_pendencia_auditoria_iso = ".intval($args['cd_pendencia_auditoria_iso']);

        $this->db->query($qr_sql);
    }

}
