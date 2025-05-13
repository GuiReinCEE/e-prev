<?php

class Pauta_cci_model extends Model {
	function __construct()
    {
    	parent::Model();
    }

    public function listar($args = array())
    {
    	$qr_sql = "
    		SELECT a.cd_pauta_cci,
               a.nr_pauta_cci,
               a.ds_local,
               TO_CHAR(a.dt_pauta_cci, 'DD/MM/YYYY HH24:MI') AS dt_pauta_cci,
               TO_CHAR(a.dt_pauta_cci_fim, 'DD/MM/YYYY HH24:MI') AS dt_pauta_cci_fim,
               TO_CHAR(a.dt_aprovacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_aprovacao,
               funcoes.get_usuario_nome(a.cd_usuario_aprovacao) AS nome,
               (SELECT COUNT(*)
                  FROM gestao.pauta_cci_assunto qti
                 WHERE qti.dt_exclusao IS NULL
                   AND qti.cd_pauta_cci = a.cd_pauta_cci) AS qt_item,
               (SELECT COUNT(*)
                  FROM gestao.pauta_cci_assunto qtir
                 WHERE qtir.dt_exclusao IS NULL
                   AND qtir.cd_pauta_cci = a.cd_pauta_cci
                   AND qtir.ds_recomendacao IS NOT NULL
                   AND qtir.dt_removido IS NULL) AS qt_item_recomendado,
               (SELECT COUNT(*)
                  FROM gestao.pauta_cci_assunto qtr
                 WHERE qtr.dt_exclusao IS NULL
                   AND qtr.cd_pauta_cci = a.cd_pauta_cci
                   AND qtr.dt_removido IS NOT NULL) AS qt_item_removido
              FROM gestao.pauta_cci a
             WHERE a.dt_exclusao IS NULL
              ".(trim($args['nr_pauta_cci']) != '' ? "AND nr_pauta_cci = ".intval($args['nr_pauta_cci']) : "")."
              ".(trim($args['fl_aprovado']) == 'S' ? "AND dt_aprovacao IS NOT NULL" : "")."
              ".(trim($args['fl_aprovado']) == 'N' ? "AND dt_aprovacao IS NULL": "")."
              ".(((trim($args['dt_pauta_cci_ini']) != '') AND (trim($args['dt_pauta_cci_fim']) != '')) ? " AND DATE_TRUNC('day', a.dt_pauta_cci) BETWEEN TO_DATE('".$args['dt_pauta_cci_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_pauta_cci_fim']."', 'DD/MM/YYYY')" : "")."
              ".(((trim($args['dt_pauta_cci_fim_ini']) != '') AND (trim($args['dt_pauta_cci_fim_fim']) != '')) ? " AND DATE_TRUNC('day', dt_pauta_cci_fim) BETWEEN TO_DATE('".$args['dt_pauta_cci_fim_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_pauta_cci_fim_fim']."', 'DD/MM/YYYY')" : "").";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_pauta_cci)
    {
        $qr_sql = "
            SELECT a.cd_pauta_cci,
                   a.nr_pauta_cci,
                   a.ds_local,
                   a.ds_integracao_arq,
                   TO_CHAR(a.dt_pauta_cci, 'DD/MM/YYYY') AS dt_pauta_cci,
                   TO_CHAR(a.dt_pauta_cci, 'HH24:MI') AS hr_pauta_cci,
                   TO_CHAR(a.dt_pauta_cci_fim, 'DD/MM/YYYY') AS dt_pauta_cci_fim,
                   TO_CHAR(a.dt_pauta_cci_fim, 'HH24:MI') AS hr_pauta_cci_fim,
                   TO_CHAR(a.dt_aprovacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_aprovacao,
                   funcoes.get_usuario_nome (a.cd_usuario_aprovacao) AS cd_usuario_aprovacao,
                   (
                   (SELECT COUNT(*)
                      FROM gestao.pauta_cci_assunto qti
                     WHERE qti.dt_exclusao IS NULL
                       AND qti.dt_removido IS NULL
                       AND qti.cd_pauta_cci = a.cd_pauta_cci)
                    -
                   (SELECT COUNT(*)
                      FROM gestao.pauta_cci_assunto qtir
                     WHERE qtir.dt_exclusao IS NULL
                       AND qtir.dt_removido IS NULL
                       AND qtir.cd_pauta_cci = a.cd_pauta_cci
                       AND qtir.ds_recomendacao IS NOT NULL)
                   ) 
                   AS tl_sem_recomendacao,
                   (SELECT COUNT(*)
                      FROM gestao.pauta_cci_assunto qtinr
                     WHERE qtinr.dt_exclusao IS NULL
                       AND qtinr.dt_removido IS NULL
                       AND qtinr.cd_pauta_cci = a.cd_pauta_cci
                       AND qtinr.ds_recomendacao IS NOT NULL) AS tl_nao_removido,
                   (SELECT COUNT(*)
                      FROM gestao.pauta_cci_assunto qt
                     WHERE qt.dt_exclusao IS NULL
                       AND qt.dt_removido IS NULL
                       AND qt.cd_pauta_cci = a.cd_pauta_cci) AS tl_assuntos
              FROM gestao.pauta_cci a
             WHERE a.cd_pauta_cci = ".intval($cd_pauta_cci).";";
     
        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $cd_pauta_cci = intval($this->db->get_new_id('gestao.pauta_cci', 'cd_pauta_cci'));

        $qr_sql = "
            INSERT INTO gestao.pauta_cci
                 (
                    cd_pauta_cci, 
                    nr_pauta_cci, 
                    dt_pauta_cci, 
                    dt_pauta_cci_fim, 
                    ds_local, 
                    ds_integracao_arq,
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                  )
             VALUES 
                  (
                    ".intval($cd_pauta_cci).",
                    ".(trim($args['nr_pauta_cci']) != '' ? intval($args['nr_pauta_cci']) : "DEFAULT").",
                    ".(trim($args['dt_pauta_cci']) != '' ? "TO_TIMESTAMP('".trim($args['dt_pauta_cci'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                    ".(trim($args['dt_pauta_cci_fim']) != '' ? "TO_TIMESTAMP('".trim($args['dt_pauta_cci_fim'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                    ".(trim($args['ds_local']) != '' ? str_escape($args['ds_local']) : "DEFAULT").",
                    ".(trim($args['ds_integracao_arq']) != '' ? str_escape($args['ds_integracao_arq']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                  );";

        $this->db->query($qr_sql);

        return $cd_pauta_cci;
    }

    public function atualizar($cd_pauta_cci, $args = array())
    {
        $qr_sql = "
            UPDATE gestao.pauta_cci
               SET nr_pauta_cci         = ".(trim($args['nr_pauta_cci']) != '' ? intval($args['nr_pauta_cci']) : "DEFAULT").",
                   dt_pauta_cci         = ".(trim($args['dt_pauta_cci']) != '' ? "TO_TIMESTAMP('".trim($args['dt_pauta_cci'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                   dt_pauta_cci_fim     = ".(trim($args['dt_pauta_cci_fim']) != '' ? "TO_TIMESTAMP('".trim($args['dt_pauta_cci_fim'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                   ds_local             = ".(trim($args['ds_local']) != '' ? str_escape($args['ds_local']) : "DEFAULT").",        
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_pauta_cci = ".intval($cd_pauta_cci).";";

        $this->db->query($qr_sql);
    }

    public function get_usuarios($divisao)
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_usuario_gerencia('".trim($divisao)."')";

        return $this->db->query($qr_sql)->result_array();
    }
    
    function assunto_salvar($args = array())
    {
      $cd_pauta_cci_assunto = intval($this->db->get_new_id('gestao.pauta_cci_assunto', 'cd_pauta_cci_assunto'));

      $qr_sql = "
        INSERT INTO gestao.pauta_cci_assunto
             (
                cd_pauta_cci_assunto, 
                cd_pauta_cci, 
                ds_pauta_cci_assunto, 
                nr_item, 
                cd_gerencia_responsavel,
                cd_usuario_responsavel,
                cd_gerencia_substituto, 
                cd_usuario_substituto,
                cd_usuario_inclusao,
                cd_usuario_alteracao
                    
             )
        VALUES 
             (
                ".intval($cd_pauta_cci_assunto).",
                ".intval($args['cd_pauta_cci']).", 
                ".str_escape($args['ds_pauta_cci_assunto']).",
                ".(trim($args['nr_item']) != '' ? intval($args['nr_item']) : 'DEFAULT').",
                ".str_escape($args['cd_gerencia_responsavel']).",
                ".(trim($args['cd_usuario_responsavel']) != ''? intval($args['cd_usuario_responsavel']) : 'DEFAULT').",
                ".str_escape($args['cd_gerencia_substituto']).",
                ".(trim($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : 'DEFAULT').",
                ".intval($args['cd_usuario']).",
                ".intval($args['cd_usuario'])."
             );";

        $this->db->query($qr_sql);
     
       	return $cd_pauta_cci_assunto;
    }

    public function assunto_carrega($cd_pauta_cci_assunto)
    {
  		$qr_sql = "
	    	  SELECT cd_pauta_cci_assunto, 
	               cd_pauta_cci, 
	               ds_pauta_cci_assunto, 
	               nr_item, 
	               cd_gerencia_responsavel,
	               cd_usuario_responsavel,
	               cd_gerencia_substituto, 
	               cd_usuario_substituto,
                 ds_recomendacao,
                 funcoes.get_usuario_nome (cd_usuario_responsavel) AS usuario_responsavel,
                 funcoes.get_usuario_nome (cd_usuario_substituto) AS usuario_substituto
	          FROM gestao.pauta_cci_assunto 
	         WHERE cd_pauta_cci_assunto = ".intval($cd_pauta_cci_assunto).";";

      	return $this->db->query($qr_sql)->row_array();
    }

    public function assunto_listar($cd_pauta_cci, $fl_removido = '')
    {
  		$qr_sql = "
  		    SELECT a.cd_pauta_cci_assunto,
  		           a.nr_item, 
  		           a.ds_pauta_cci_assunto, 
  		           a.cd_gerencia_responsavel,
  		           a.cd_usuario_responsavel,
  		           a.cd_gerencia_substituto, 
  		           a.cd_usuario_substituto,
                 a.ds_recomendacao,
                 funcoes.get_usuario_nome(a.cd_usuario_responsavel) AS usuario_responsavel,
                 funcoes.get_usuario_nome(a.cd_usuario_substituto) AS usuario_substituto,
                 (SELECT COUNT(*)
                   FROM gestao.pauta_cci_assunto_anexo an
                  WHERE an.dt_exclusao IS NULL
                    AND an.cd_pauta_cci_assunto = a.cd_pauta_cci_assunto)  AS qt_arquivo,
                 a.dt_removido
  		      FROM gestao.pauta_cci_assunto a
           WHERE a.dt_exclusao IS NULL
  		       AND a.cd_pauta_cci = ".intval($cd_pauta_cci)."
             ".(trim($fl_removido) == 'N' ? "AND a.dt_removido IS NULL" : "")."
           ORDER BY a.nr_item ASC;";
            
      	return $this->db->query($qr_sql)->result_array();
    } 
    
    public function assunto_atualizar($cd_pauta_cci_assunto, $args = array())
    {
      $qr_sql = "
          UPDATE gestao.pauta_cci_assunto
             SET nr_item                 = ".(trim($args['nr_item']) != '' ? intval($args['nr_item']) : 'DEFAULT').",
                 ds_pauta_cci_assunto    = ".str_escape($args['ds_pauta_cci_assunto']).",                
                 cd_gerencia_responsavel = ".str_escape($args['cd_gerencia_responsavel']).",
                 cd_usuario_responsavel  = ".(trim($args['cd_usuario_responsavel']) != ''? intval($args["cd_usuario_responsavel"]) : 'DEFAULT').", 
                 cd_gerencia_substituto  = ".str_escape($args['cd_gerencia_substituto']).", 
                 cd_usuario_substituto   = ".(trim($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : 'DEFAULT').",
                 ds_recomendacao         = ".str_escape($args['ds_recomendacao']).", 
                 cd_usuario_alteracao    = ".intval($args['cd_usuario']).",
                 dt_alteracao            = CURRENT_TIMESTAMP
            WHERE cd_pauta_cci_assunto   = ".intval($cd_pauta_cci_assunto).";";

      $this->db->query($qr_sql);
    }

     public function assunto_excluir($cd_usuario, $cd_pauta_cci_assunto)
    {
      $qr_sql = "
        UPDATE gestao.pauta_cci_assunto
           SET cd_usuario_exclusao  = ".intval($cd_usuario).",
               dt_exclusao          = CURRENT_TIMESTAMP 
         WHERE cd_pauta_cci_assunto = ".intval($cd_pauta_cci_assunto).";";

      $this->db->query($qr_sql);
    }

    public function assunto_remover($cd_usuario, $cd_pauta_cci_assunto)
    {
      $qr_sql = "
        UPDATE gestao.pauta_cci_assunto
           SET cd_usuario_removido = ".intval($cd_usuario).", 
               dt_removido         = CURRENT_TIMESTAMP
         WHERE cd_pauta_cci_assunto = ".intval($cd_pauta_cci_assunto).";";
   
      $this->db->query($qr_sql);
    }

    public function assuntos_removidos($cd_pauta_cci)
    {
      $qr_sql = "
          SELECT a.cd_pauta_cci_assunto,
                 a.nr_item, 
                 a.ds_pauta_cci_assunto, 
                 a.cd_gerencia_responsavel,
                 a.cd_usuario_responsavel,
                 a.cd_gerencia_substituto, 
                 a.cd_usuario_substituto,
                 funcoes.get_usuario_nome(a.cd_usuario_responsavel) AS usuario_responsavel,
                 funcoes.get_usuario_nome(a.cd_usuario_substituto) AS usuario_substituto
            FROM gestao.pauta_cci_assunto a
           WHERE a.dt_exclusao IS NULL
             AND a.ds_recomendacao IS NULL
             AND a.dt_removido IS NOT NULL
             AND a.cd_pauta_cci = (SELECT cd_pauta_cci
                                     FROM gestao.pauta_cci p
                                    WHERE p.dt_exclusao IS NULL
                                      AND p.cd_pauta_cci != ".intval($cd_pauta_cci)."
                                    ORDER BY cd_pauta_cci DESC
                                    LIMIT 1);";  
         return $this->db->query($qr_sql)->result_array();
    }

     public function carrega_numero_assunto($cd_pauta_cci)
    {
      $qr_sql = "
        SELECT nr_item + 1 AS nr_item
          FROM gestao.pauta_cci_assunto
         WHERE cd_pauta_cci = ".intval($cd_pauta_cci)."
           AND dt_exclusao IS NULL
         ORDER BY nr_item DESC limit 1;";

      return $this->db->query($qr_sql)->row_array();
    }

    public function set_ordem($cd_pauta_cci_assunto, $args = array())
    {
      $qr_sql = "
        UPDATE gestao.pauta_cci_assunto
           SET nr_item              = ".(trim($args['nr_item']) != '' ? intval($args['nr_item']) : 'DEFAULT').",
               cd_usuario_alteracao = ".intval($args['cd_usuario']).", 
               dt_alteracao         = CURRENT_TIMESTAMP
         WHERE cd_pauta_cci_assunto = ".intval($cd_pauta_cci_assunto).";";

      $this->db->query($qr_sql);
    }

    public function anexo_salvar($args = array())
    {
        $qr_sql = "
          INSERT INTO gestao.pauta_cci_assunto_anexo
               (
                cd_pauta_cci_assunto,
                arquivo, 
                arquivo_nome,
                ds_nome_arquivo_arq,
                cd_usuario_inclusao
               )
          VALUES
               (
                 ".intval($args['cd_pauta_cci_assunto']).",
                 ".str_escape($args['arquivo']).",
                 ".str_escape($args['arquivo_nome']).",
                 ".(trim($args['ds_nome_arquivo_arq']) != '' ? str_escape($args['ds_nome_arquivo_arq']) : 'DEFAULT').",
                 ".intval($args['cd_usuario'])."
               );";

        $this->db->query($qr_sql);
    }

    public function anexo_listar($cd_pauta_cci_assunto)
    {
      $qr_sql = "
        SELECT a.cd_pauta_cci_assunto_anexo,
               a.arquivo,
               a.arquivo_nome,
               TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
               funcoes.get_usuario_nome(a.cd_usuario_inclusao) AS usuario_inclusao
          FROM gestao.pauta_cci_assunto_anexo  a
         WHERE a.dt_exclusao IS NULL
           AND a.cd_pauta_cci_assunto = ". intval($cd_pauta_cci_assunto)."
         ORDER BY a.dt_inclusao DESC ";
  
      return $this->db->query($qr_sql)->result_array();
    }

    public function anexo_carrega($cd_pauta_cci_assunto_anexo)
    {
      $qr_sql = "
        SELECT a.cd_pauta_cci_assunto_anexo,
               a.arquivo,
               a.arquivo_nome,
               a.ds_nome_arquivo_arq
          FROM gestao.pauta_cci_assunto_anexo  a
         WHERE a.cd_pauta_cci_assunto_anexo = ".intval($cd_pauta_cci_assunto_anexo).";";
  
      return $this->db->query($qr_sql)->row_array();
    }

    public function anexo_excluir($cd_usuario, $cd_pauta_cci_assunto_anexo)
    {
      $qr_sql = "
        UPDATE gestao.pauta_cci_assunto_anexo
           SET cd_usuario_exclusao = ".intval($cd_usuario).",
               dt_exclusao         = CURRENT_TIMESTAMP
         WHERE cd_pauta_cci_assunto_anexo = ".intval($cd_pauta_cci_assunto_anexo).";";

      $this->db->query($qr_sql);
    }

    public function pesquisa_listar($args = array())
    {
        $qr_sql = "
        SELECT a.cd_pauta_cci,
               a.nr_pauta_cci,
               a.ds_local,
               TO_CHAR(a.dt_pauta_cci, 'DD/MM/YYYY HH24:MI') AS dt_pauta_cci,
               TO_CHAR(a.dt_pauta_cci_fim, 'DD/MM/YYYY HH24:MI') AS dt_pauta_cci_fim,
               TO_CHAR(a.dt_aprovacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_aprovacao,
               an.ds_pauta_cci_assunto,
               an.ds_recomendacao
          FROM gestao.pauta_cci a
          JOIN gestao.pauta_cci_assunto an
            ON an.cd_pauta_cci = a.cd_pauta_cci
         WHERE a.dt_exclusao IS NULL
           AND an.dt_exclusao IS NULL
              ".(trim($args['nr_pauta_cci']) != '' ? "AND nr_pauta_cci = ".intval($args['nr_pauta_cci']) : "")."
              ".(((trim($args['dt_pauta_cci_ini']) != '') AND (trim($args['dt_pauta_cci_fim']) != '')) ? " AND DATE_TRUNC('day', a.dt_pauta_cci) BETWEEN TO_DATE('".$args['dt_pauta_cci_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_pauta_cci_fim']."', 'DD/MM/YYYY')" : "")."
              ".(((trim($args['dt_pauta_cci_fim_ini']) != '') AND (trim($args['dt_pauta_cci_fim_fim']) != '')) ? " AND DATE_TRUNC('day', dt_pauta_cci_fim) BETWEEN TO_DATE('".$args['dt_pauta_cci_fim_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_pauta_cci_fim_fim']."', 'DD/MM/YYYY')" : "")."
              ".(trim($args['ds_pauta_cci_assunto'])!= '' ? "AND UPPER(funcoes.remove_acento(an.ds_pauta_cci_assunto)) LIKE UPPER(funcoes.remove_acento('%".trim($args['ds_pauta_cci_assunto'])."%'))" : "")."
              ;";
              
       return $this->db->query($qr_sql)->result_array();
    }
    
    public function aprovar($cd_pauta_cci, $cd_usuario)
    {
      $qr_sql = "
          UPDATE gestao.pauta_cci
             SET dt_aprovacao         = CURRENT_TIMESTAMP, 
                 cd_usuario_aprovacao = ".intval($cd_usuario)."
           WHERE cd_pauta_cci = ".intval($cd_pauta_cci).";";

       $this->db->query($qr_sql);
    }

    public function reabrir($cd_pauta_cci)
    {
      $qr_sql = "
          UPDATE gestao.pauta_cci
             SET dt_aprovacao         = NULL, 
                 cd_usuario_aprovacao = NULL
           WHERE cd_pauta_cci = ".intval($cd_pauta_cci).";";

       $this->db->query($qr_sql);
    }
}