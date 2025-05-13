<?php

class Relatorio_auditoria_contabil_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "
            SELECT rac.cd_relatorio_auditoria_contabil, 
                   rac.ds_relatorio_auditoria_contabil, 
                   funcoes.nr_relatorio_auditoria_contabil(rac.nr_ano, rac.nr_numero) AS ano_numero,
                   rac.arquivo, 
                   rac.arquivo_nome, 
                   TO_CHAR(rac.dt_envio_gc, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_gc, 
                   uc2.nome AS usuario_envio_gc,
                   TO_CHAR(rac.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
                   uc.nome AS usuario_inclusao,
                   (SELECT COUNT(raci.*)
                      FROM projetos.relatorio_auditoria_contabil_item raci
                     WHERE raci.cd_relatorio_auditoria_contabil = rac.cd_relatorio_auditoria_contabil
                       AND raci.dt_exclusao IS NULL) AS qt_itens,
                   (SELECT COUNT(raci.*)
                      FROM projetos.relatorio_auditoria_contabil_item raci
                     WHERE raci.cd_relatorio_auditoria_contabil = rac.cd_relatorio_auditoria_contabil
                       AND raci.dt_exclusao IS NULL
                       AND raci.dt_resposta IS NOT NULL) AS qt_respondidos,
                   (SELECT COUNT(raci.*)
                      FROM projetos.relatorio_auditoria_contabil_item raci
                     WHERE raci.cd_relatorio_auditoria_contabil = rac.cd_relatorio_auditoria_contabil
                       AND raci.dt_exclusao IS NULL
                       AND raci.dt_resposta IS NOT NULL
                       AND raci.dt_resposta::date > raci.dt_limite::date) AS qt_respondidos_limite,
                   TO_CHAR(rac.dt_envio_sg, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_sg,
                   TO_CHAR(rac.dt_alchemy, 'DD/MM/YYYY HH24:MI:SS') AS dt_alchemy
              FROM projetos.relatorio_auditoria_contabil rac
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = rac.cd_usuario_inclusao
              LEFT JOIN projetos.usuarios_controledi uc2
                ON uc2.codigo = rac.cd_usuario_envio_gc
             WHERE rac.dt_exclusao IS NULL
               ".(((trim($args['dt_inclusao_ini']) != "") AND  (trim($args['dt_inclusao_fim']) != "")) ? " AND DATE_TRUNC('day', rac.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_envio_ini']) != "") AND  (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', rac.dt_envio_gc) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "").";";
	
        $result = $this->db->query($qr_sql);
    }
	
	function cadastro(&$result, $args=array())
	{
		$qr_sql = "
            SELECT rac.cd_relatorio_auditoria_contabil, 
                   rac.ds_relatorio_auditoria_contabil, 
                   funcoes.nr_relatorio_auditoria_contabil(rac.nr_ano, rac.nr_numero) AS ano_numero,
                   rac.arquivo, 
                   rac.arquivo_nome, 
                   TO_CHAR(rac.dt_envio_gc, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_gc,
                   (SELECT COUNT(raci1.*)
                      FROM projetos.relatorio_auditoria_contabil_item raci1
                     WHERE raci1.dt_exclusao IS NULL
                       AND raci1.cd_relatorio_auditoria_contabil = rac.cd_relatorio_auditoria_contabil) AS qt_itens,
                   (SELECT COUNT(raci2.*)
                      FROM projetos.relatorio_auditoria_contabil_item raci2
                     WHERE raci2.dt_exclusao IS NULL
                       AND raci2.cd_relatorio_auditoria_contabil = rac.cd_relatorio_auditoria_contabil
                       AND raci2.dt_envio IS NOT NULL) AS qt_itens_enviado,
                   (SELECT COUNT(raci3.*)
                      FROM projetos.relatorio_auditoria_contabil_item raci3
                     WHERE raci3.cd_relatorio_auditoria_contabil = rac.cd_relatorio_auditoria_contabil
                       AND raci3.dt_exclusao IS NULL
                       AND raci3.dt_resposta IS NOT NULL) AS qt_itens_respondidos,
                   TO_CHAR(rac.dt_encaminhamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encaminhamento,
                   TO_CHAR(rac.dt_aprovado, 'DD/MM/YYYY HH24:MI:SS') AS dt_aprovado,
                   TO_CHAR(rac.dt_recusar, 'DD/MM/YYYY HH24:MI:SS') AS dt_recusar,
                   uc1.nome AS usuario_aprovado,
                   uc2.nome AS usuario_recusar,
                   TO_CHAR(rac.dt_envio_sg, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_sg,
                   TO_CHAR(rac.dt_alchemy, 'DD/MM/YYYY HH24:MI:SS') AS dt_alchemy
              FROM projetos.relatorio_auditoria_contabil rac
              LEFT JOIN projetos.usuarios_controledi uc1
                ON uc1.codigo = rac.cd_usuario_aprovado
              LEFT JOIN projetos.usuarios_controledi uc2
                ON uc2.codigo = rac.cd_usuario_recusar
             WHERE rac.cd_relatorio_auditoria_contabil = ".intval($args['cd_relatorio_auditoria_contabil']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
    function salvar(&$result, $args=array())
	{		
		if(intval($args['cd_relatorio_auditoria_contabil']) == 0)
		{
            $cd_relatorio_auditoria_contabil = intval($this->db->get_new_id("projetos.relatorio_auditoria_contabil", "cd_relatorio_auditoria_contabil"));
            
			$qr_sql = "
                INSERT INTO projetos.relatorio_auditoria_contabil
                     (
                       cd_relatorio_auditoria_contabil,
                       ds_relatorio_auditoria_contabil, 
                       arquivo, 
                       arquivo_nome,
                       cd_usuario_inclusao,
                       cd_usuario_alteracao
                     )
                VALUES 
                     (
                       ".intval($cd_relatorio_auditoria_contabil).",
                       ".(trim($args['ds_relatorio_auditoria_contabil']) != '' ? str_escape($args['ds_relatorio_auditoria_contabil']) : "DEFAULT").",
                       ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
                       ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
                       ".intval($args['cd_usuario']).",
                       ".intval($args['cd_usuario'])."
                     );";		
		}
		else
		{
            $cd_relatorio_auditoria_contabil = intval($args['cd_relatorio_auditoria_contabil']);
            
			$qr_sql = "
                UPDATE projetos.relatorio_auditoria_contabil
                   SET ds_relatorio_auditoria_contabil = ".(trim($args['ds_relatorio_auditoria_contabil']) != '' ? str_escape($args['ds_relatorio_auditoria_contabil']) : "DEFAULT").",
                       arquivo                         = ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
                       arquivo_nome                    = ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
                       dt_envio_sg                     = ".(trim($args['dt_envio_sg']) != '' ? "TO_DATE('".trim($args['dt_envio_sg'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                       dt_alchemy                      = ".(trim($args['dt_alchemy']) != '' ? "TO_DATE('".trim($args['dt_alchemy'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                       cd_usuario_alteracao            = ".intval($args['cd_usuario']).",
                       dt_alteracao                    = CURRENT_TIMESTAMP
                 WHERE cd_relatorio_auditoria_contabil = ".intval($args['cd_relatorio_auditoria_contabil']).";";
		}

        $result = $this->db->query($qr_sql);
        
        return $cd_relatorio_auditoria_contabil;
	}
    
    function enviar_gc(&$result, $args=array())
	{
		$qr_sql = "
            UPDATE projetos.relatorio_auditoria_contabil
               SET cd_usuario_envio_gc            = ".intval($args['cd_usuario']).",
                   dt_envio_gc                    = CURRENT_TIMESTAMP
             WHERE cd_relatorio_auditoria_contabil = ".intval($args['cd_relatorio_auditoria_contabil']).";";
			 
		$result = $this->db->query($qr_sql);
	}
    
    function salvar_item(&$result, $args=array())
	{
		$qr_sql = "
            INSERT INTO projetos.relatorio_auditoria_contabil_item
                (
                   cd_relatorio_auditoria_contabil,
                   ds_relatorio_auditoria_contabil_item, 
                   nr_numero_item,  
                   cd_usuario_responsavel, 
                   cd_usuario_substituto, 
                   dt_limite,  
                   cd_usuario_inclusao, 
                   cd_usuario_alteracao
                )
           VALUES 
                (
                    ".(trim($args['cd_relatorio_auditoria_contabil']) != '' ? intval($args['cd_relatorio_auditoria_contabil']) : "DEFAULT").",
                    ".(trim($args['ds_relatorio_auditoria_contabil_item']) != '' ? str_escape($args['ds_relatorio_auditoria_contabil_item']) : "DEFAULT").",
                    ".(trim($args['nr_numero_item']) != '' ? intval($args['nr_numero_item']) : "DEFAULT").",
                    ".(trim($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
                    ".(trim($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : "DEFAULT").",
                    ".(trim($args['dt_limite']) != '' ? "TO_DATE('".trim($args['dt_limite'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                );";
			 
		$result = $this->db->query($qr_sql);
	}
    
    function excluir_item(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE projetos.relatorio_auditoria_contabil_item
               SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE CD_relatorio_auditoria_contabil_item = ".intval($args['cd_relatorio_auditoria_contabil_item']).";";
        
        $result = $this->db->query($qr_sql);
    }
    
    function listar_itens(&$result, $args=array())
    {
        $qr_sql = "
            SELECT raci.cd_relatorio_auditoria_contabil_item, 
                   raci.ds_relatorio_auditoria_contabil_item, 
                   raci.nr_numero_item,  
                   uc.nome AS usuario_responsavel,
                   uc2.nome AS usuario_substituto,
                   TO_CHAR(raci.dt_limite, 'DD/MM/YYYY') AS dt_limite, 
                   TO_CHAR(raci.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
                   TO_CHAR(raci.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio, 
                   TO_CHAR(raci.dt_resposta, 'DD/MM/YYYY HH24:MI:SS') AS dt_resposta, 
                   uc3.nome AS usuario_inclusao,
                   uc4.nome AS usuario_resposta,
                   raci.arquivo,
                   raci.arquivo_nome,
                   raci.ds_resposta
              FROM projetos.relatorio_auditoria_contabil_item raci
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = raci.cd_usuario_responsavel
              JOIN projetos.usuarios_controledi uc2
                ON uc2.codigo = raci.cd_usuario_substituto
              JOIN projetos.usuarios_controledi uc3
                ON uc3.codigo = raci.cd_usuario_inclusao
              LEFT JOIN projetos.usuarios_controledi uc4
                ON uc4.codigo = raci.cd_usuario_resposta
             WHERE raci.cd_relatorio_auditoria_contabil = ".intval($args['cd_relatorio_auditoria_contabil'])."
               AND raci.dt_exclusao IS NULL;";
        
        $result = $this->db->query($qr_sql);
    }
    
    function enviar(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE projetos.relatorio_auditoria_contabil_item
               SET cd_usuario_envio = ".intval($args['cd_usuario']).",
                   dt_envio         = CURRENT_TIMESTAMP
             WHERE cd_relatorio_auditoria_contabil = ".intval($args['cd_relatorio_auditoria_contabil']).";";
        
        $result = $this->db->query($qr_sql);
    }
    
    function listar_minhas(&$result, $args=array())
    {
        $qr_sql = "
            SELECT raci.cd_relatorio_auditoria_contabil_item, 
                   raci.ds_relatorio_auditoria_contabil_item, 
                   raci.nr_numero_item,  
                   rac.ds_relatorio_auditoria_contabil, 
                   uc.nome AS usuario_responsavel,
                   uc2.nome AS usuario_substituto,
                   TO_CHAR(raci.dt_limite, 'DD/MM/YYYY') AS dt_limite, 
                   TO_CHAR(raci.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
                   uc3.nome AS usuario_inclusao,
                   TO_CHAR(raci.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio, 
                   uc4.nome AS usuario_envio,
                   TO_CHAR(raci.dt_resposta, 'DD/MM/YYYY HH24:MI:SS') AS dt_resposta, 
                   uc5.nome AS usuario_resposta,
                   raci.arquivo,
                   raci.arquivo_nome,
                   funcoes.nr_relatorio_auditoria_contabil(rac.nr_ano, rac.nr_numero) AS ano_numero
              FROM projetos.relatorio_auditoria_contabil_item raci
              JOIN projetos.relatorio_auditoria_contabil rac
                ON rac.cd_relatorio_auditoria_contabil = raci.cd_relatorio_auditoria_contabil
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = raci.cd_usuario_responsavel
              JOIN projetos.usuarios_controledi uc2
                ON uc2.codigo = raci.cd_usuario_substituto
              JOIN projetos.usuarios_controledi uc3
                ON uc3.codigo = raci.cd_usuario_inclusao
              JOIN projetos.usuarios_controledi uc4
                ON uc4.codigo = raci.cd_usuario_envio
              LEFT JOIN projetos.usuarios_controledi uc5
                ON uc5.codigo = raci.cd_usuario_resposta
             WHERE (raci.cd_usuario_responsavel = ".intval($args['cd_usuario'])." OR raci.cd_usuario_substituto = ".intval($args['cd_usuario']).") 
               AND raci.dt_envio IS NOT NULL
               AND raci.dt_exclusao IS NULL
               ".(((trim($args['dt_envio_ini']) != "") AND  (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', raci.dt_envio) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_limite_ini']) != "") AND  (trim($args['dt_limite_fim']) != "")) ? " AND DATE_TRUNC('day', raci.dt_limite) BETWEEN TO_DATE('".$args['dt_limite_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_limite_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_resposta_ini']) != "") AND  (trim($args['dt_resposta_fim']) != "")) ? " AND DATE_TRUNC('day', raci.dt_resposta) BETWEEN TO_DATE('".$args['dt_resposta_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_resposta_fim']."', 'DD/MM/YYYY')" : "")."
               ".(trim($args['fl_resposta']) == 'S' ? "AND raci.dt_resposta IS NOT NULL" : "")."    
               ".(trim($args['fl_resposta']) == 'N' ? "AND raci.dt_resposta IS NULL" : "").";";
        
        $result = $this->db->query($qr_sql);
    }
    
    function resposta(&$result, $args=array())
    {
        $qr_sql = "
            SELECT raci.cd_relatorio_auditoria_contabil, 
                   raci.cd_relatorio_auditoria_contabil_item, 
                   raci.ds_relatorio_auditoria_contabil_item, 
                   raci.nr_numero_item,  
                   rac.ds_relatorio_auditoria_contabil, 
                   uc.nome AS usuario_responsavel,
                   uc2.nome AS usuario_substituto,
                   TO_CHAR(raci.dt_limite, 'DD/MM/YYYY') AS dt_limite, 
                   TO_CHAR(raci.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
                   uc3.nome AS usuario_inclusao,
                   TO_CHAR(raci.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio, 
                   uc4.nome AS usuario_envio,
                   TO_CHAR(raci.dt_resposta, 'DD/MM/YYYY HH24:MI:SS') AS dt_resposta, 
                   uc5.nome AS usuario_resposta,
                   raci.arquivo,
                   raci.arquivo_nome,
                   rac.arquivo AS arquivo_relatorio,
                   rac.arquivo_nome AS arquivo_nome_relatorio,
                   funcoes.nr_relatorio_auditoria_contabil(rac.nr_ano, rac.nr_numero) AS ano_numero,
                   raci.ds_resposta
              FROM projetos.relatorio_auditoria_contabil_item raci
              JOIN projetos.relatorio_auditoria_contabil rac
                ON rac.cd_relatorio_auditoria_contabil = raci.cd_relatorio_auditoria_contabil
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = raci.cd_usuario_responsavel
              JOIN projetos.usuarios_controledi uc2
                ON uc2.codigo = raci.cd_usuario_substituto
              JOIN projetos.usuarios_controledi uc3
                ON uc3.codigo = raci.cd_usuario_inclusao
              JOIN projetos.usuarios_controledi uc4
                ON uc4.codigo = raci.cd_usuario_envio
              LEFT JOIN projetos.usuarios_controledi uc5
                ON uc5.codigo = raci.cd_usuario_resposta
             WHERE (raci.cd_usuario_responsavel = ".intval($args['cd_usuario'])." OR raci.cd_usuario_substituto = ".intval($args['cd_usuario']).") 
               AND cd_relatorio_auditoria_contabil_item = ".intval($args['cd_relatorio_auditoria_contabil_item'])."
               AND raci.dt_envio IS NOT NULL
               AND raci.dt_exclusao IS NULL;";
        
        $result = $this->db->query($qr_sql);
    }
    
    function salvar_resposta(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE projetos.relatorio_auditoria_contabil_item
               SET arquivo              = ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
                   arquivo_nome         = ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
                   ds_resposta          = ".(trim($args['ds_resposta']) != '' ? str_escape($args['ds_resposta']) : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_relatorio_auditoria_contabil_item = ".intval($args['cd_relatorio_auditoria_contabil_item']).";";
        
        $result = $this->db->query($qr_sql);
    }
    
    function confimar(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE projetos.relatorio_auditoria_contabil_item
               SET cd_usuario_resposta = ".intval($args['cd_usuario']).",
                   dt_resposta         = CURRENT_TIMESTAMP
             WHERE cd_relatorio_auditoria_contabil_item = ".intval($args['cd_relatorio_auditoria_contabil_item']).";";
        
        $result = $this->db->query($qr_sql);
    }
    
    function listar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT a.cd_relatorio_auditoria_contabil_anexo,
				   a.arquivo,
				   a.arquivo_nome,
				   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome,
                   rac.dt_encaminhamento
			  FROM projetos.relatorio_auditoria_contabil_anexo a
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = a.cd_usuario_inclusao
              JOIN projetos.relatorio_auditoria_contabil rac
                ON rac.cd_relatorio_auditoria_contabil = a.cd_relatorio_auditoria_contabil
			 WHERE a.cd_relatorio_auditoria_contabil = ". $args['cd_relatorio_auditoria_contabil']."
			   AND a.dt_exclusao IS NULL
			 ORDER BY a.dt_inclusao DESC;";
        
		$result = $this->db->query($qr_sql);
	}
    
    function salvar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.relatorio_auditoria_contabil_anexo
			     (
					cd_relatorio_auditoria_contabil,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['cd_relatorio_auditoria_contabil']).",
                    ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",    
					".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").", 
					".intval($args['cd_usuario'])."
				 );";
        
		$result = $this->db->query($qr_sql);
	}
    
    function excluir_anexo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.relatorio_auditoria_contabil_anexo
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_relatorio_auditoria_contabil_anexo = ".intval($args['cd_relatorio_auditoria_contabil_anexo']).";";
        
		$this->db->query($qr_sql);
	}
    
    function encaminhar_aprovacao(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE projetos.relatorio_auditoria_contabil
               SET cd_usuario_encaminhamento = ".intval($args['cd_usuario']).",
                   dt_encaminhamento         = CURRENT_TIMESTAMP,
                   dt_aprovado               = NULL,
                   cd_usuario_aprovado       = NULL,
                   dt_recusar                = NULL,
                   cd_usuario_recusar        = NULL
             WHERE cd_relatorio_auditoria_contabil = ".intval($args['cd_relatorio_auditoria_contabil']).";";
			 
		$result = $this->db->query($qr_sql);
    }
    
    function salvar_acompanhamento(&$result, $args=array())
    {
        $qr_sql = "
            INSERT INTO projetos.relatorio_auditoria_contabil_acompanhamento
                 (
                   cd_relatorio_auditoria_contabil, 
                   acompanhamento, 
                   cd_usuario_inclusao
                 )
            VALUES 
                 (
                   ".intval($args["cd_relatorio_auditoria_contabil"]).",
                   ".(trim($args["acompanhamento"]) != '' ? str_escape($args["acompanhamento"]) : "DEFAULT").",
                   ".intval($args["cd_usuario"])."
                 );";
        
        $result = $this->db->query($qr_sql);
    }
    
    function listar_acompanhamento(&$result, $args=array())
    {
        $qr_sql = "
            SELECT a.cd_relatorio_auditoria_contabil_acompanhamento,
                   a.acompanhamento,
                   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   uc.nome
              FROM projetos.relatorio_auditoria_contabil_acompanhamento a
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = a.cd_usuario_inclusao
             WHERE dt_exclusao IS NULL
               AND cd_relatorio_auditoria_contabil = ".intval($args["cd_relatorio_auditoria_contabil"]).";";
        
        $result = $this->db->query($qr_sql);
    }
    
    function recusar(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE projetos.relatorio_auditoria_contabil
               SET cd_usuario_recusar        = ".intval($args['cd_usuario']).",
                   dt_recusar                = CURRENT_TIMESTAMP,
                   cd_usuario_encaminhamento = NULL,
                   dt_encaminhamento         = NULL
             WHERE cd_relatorio_auditoria_contabil = ".intval($args['cd_relatorio_auditoria_contabil']).";";
        
        $result = $this->db->query($qr_sql);
    }
    
    function confirmar_aprovacao(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE projetos.relatorio_auditoria_contabil
               SET cd_usuario_aprovado        = ".intval($args['cd_usuario']).",
                   dt_aprovado                = CURRENT_TIMESTAMP
             WHERE cd_relatorio_auditoria_contabil = ".intval($args['cd_relatorio_auditoria_contabil']).";";
        
        $result = $this->db->query($qr_sql);
    }
}

?>