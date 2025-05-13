<?php

class resolucao_diretoria_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function listar(&$result, $args=array())
    {
        $qr_sql = "
			SELECT c.cd_resolucao_diretoria,
			       gestao.nr_resolucao_diretoria(c.nr_ano, c.nr_resolucao_diretoria) AS ano_numero,
				   TO_CHAR(c.dt_resolucao_diretoria, 'DD/MM/YYYY') AS dt_resolucao_diretoria,
				   TO_CHAR(c.dt_divulgacao, 'DD/MM/YYYY') AS dt_divulgacao,
				   c.ds_resolucao_diretoria,
				   CASE WHEN c.fl_situacao = 'N' THEN 'label label-success'
						ELSE 'label label-important'
				   END AS class_situacao,
				   CASE WHEN c.fl_situacao = 'N' THEN 'Normal'
						ELSE 'Revogada'
				   END AS situacao,
				   c.observacao,
				   ca.ds_resolucao_diretoria_abrangencia,
				   TO_CHAR(c.dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
				   uc.nome,
				   c.arquivo,
				   c.nr_ata,
				   c.rds,
				   c.area
			  FROM gestao.resolucao_diretoria c
			  LEFT JOIN gestao.resolucao_diretoria_abrangencia ca
				ON ca.cd_resolucao_diretoria_abrangencia = c.cd_resolucao_diretoria_abrangencia
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = c.cd_usuario_alteracao
			 WHERE c.dt_exclusao IS NULL
			 ".(gerencia_in(array('SG')) ? "" : "AND c.dt_divulgacao IS NOT NULL")."
			 ".(trim($args['nr_resolucao_diretoria']) != '' ? "AND c.nr_resolucao_diretoria = ".intval($args['nr_resolucao_diretoria']) : "")." 
			 ".(trim($args['nr_ano']) != '' ? "AND c.nr_ano = ".intval($args['nr_ano']) : "")." 
			 ".(trim($args['nr_ano']) != '' ? "AND c.nr_ano = ".intval($args['nr_ano']) : "")." 
			 ".(((trim($args['dt_ini']) != "") and  (trim($args['dt_fim']) != "")) ? " AND DATE_TRUNC('day', c.dt_resolucao_diretoria) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
			 ".(trim($args["ds_resolucao_diretoria"]) != "" ? "AND UPPER(funcoes.remove_acento(c.ds_resolucao_diretoria)) LIKE UPPER(funcoes.remove_acento('%".trim($args["ds_resolucao_diretoria"])."%'))" : "").";";

        $result = $this->db->query($qr_sql);
    }
	
	function carrega(&$result, $args=array())
    {
        $qr_sql = "
			SELECT d.cd_resolucao_diretoria,
			       d.nr_ano,
				   d.nr_resolucao_diretoria,
				   TO_CHAR(d.dt_resolucao_diretoria, 'DD/MM/YYYY') AS dt_resolucao_diretoria,
				   d.ds_resolucao_diretoria,
				   d.dt_divulgacao,
				   d.fl_situacao,
				   d.cd_resolucao_diretoria_abrangencia,
				   d.observacao,
				   d.arquivo,
				   d.arquivo_nome,
				   d.nr_ata,
				   d.rds,
				   d.area,
				   gestao.nr_resolucao_diretoria(d.nr_ano, d.nr_resolucao_diretoria) AS ano_numero,
				   ca.ds_resolucao_diretoria_abrangencia   
			  FROM gestao.resolucao_diretoria d
			  LEFT JOIN gestao.resolucao_diretoria_abrangencia ca
				ON ca.cd_resolucao_diretoria_abrangencia = d.cd_resolucao_diretoria_abrangencia
			 WHERE d.cd_resolucao_diretoria = ".intval($args['cd_resolucao_diretoria']).";";

        $result = $this->db->query($qr_sql);
    }
	
	function salvar(&$result, $args=array())
    {
		if(intval($args['cd_resolucao_diretoria']) == 0)
		{
			$cd_resolucao_diretoria = intval($this->db->get_new_id("gestao.resolucao_diretoria", "cd_resolucao_diretoria"));
		
			$qr_sql = "
				INSERT INTO gestao.resolucao_diretoria
				     (
					   cd_resolucao_diretoria,
                       ds_resolucao_diretoria, 
					   nr_resolucao_diretoria, 
					   nr_ano, 
					   dt_resolucao_diretoria, 
					   nr_ata,
					   rds,
					   area,
                       fl_situacao, 
					   cd_resolucao_diretoria_abrangencia, 
					   observacao, 
					   arquivo, 
					   arquivo_nome, 
                       cd_usuario_inclusao, 
					   cd_usuario_alteracao
                     )
                VALUES 
				     (
					    ".intval($cd_resolucao_diretoria).",
						".(trim($args['ds_resolucao_diretoria']) != '' ? str_escape($args['ds_resolucao_diretoria']) : "DEFAULT").",
						".(trim($args['nr_resolucao_diretoria']) != '' ? intval($args['nr_resolucao_diretoria']) : "DEFAULT").",
						".(trim($args['nr_ano']) != '' ? intval($args['nr_ano']) : "DEFAULT").",
						".(trim($args['dt_resolucao_diretoria']) != '' ? "TO_DATE('".trim($args['dt_resolucao_diretoria'])."', 'DD/MM/YYYY')" : "DEFAULT").",
						".(trim($args['nr_ata']) != '' ? intval($args['nr_ata']) : "DEFAULT").",
						".(trim($args['rds']) != '' ? str_escape($args['rds']) : "DEFAULT").",
						".(trim($args['area']) != '' ? str_escape($args['area']) : "DEFAULT").",
						".(trim($args['fl_situacao']) != '' ? str_escape($args['fl_situacao']) : "DEFAULT").",
						".(trim($args['cd_resolucao_diretoria_abrangencia']) != '' ? intval($args['cd_resolucao_diretoria_abrangencia']) : "DEFAULT").",
						".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
						".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
						".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$cd_resolucao_diretoria = intval($args['cd_resolucao_diretoria']);
		
			$qr_sql = "
				UPDATE gestao.resolucao_diretoria
				   SET ds_resolucao_diretoria             = ".(trim($args['ds_resolucao_diretoria']) != '' ? str_escape($args['ds_resolucao_diretoria']) : "DEFAULT").",
				       nr_resolucao_diretoria             = ".(trim($args['nr_resolucao_diretoria']) != '' ? intval($args['nr_resolucao_diretoria']) : "DEFAULT").",
					   nr_ano                             = ".(trim($args['nr_ano']) != '' ? intval($args['nr_ano']) : "DEFAULT").",
					   dt_resolucao_diretoria             = ".(trim($args['dt_resolucao_diretoria']) != '' ? "TO_DATE('".trim($args['dt_resolucao_diretoria'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					   nr_ata                             = ".(trim($args['nr_ata']) != '' ? intval($args['nr_ata']) : "DEFAULT").",
					   rds                                = ".(trim($args['rds']) != '' ? str_escape($args['rds']) : "DEFAULT").",
					   area                               = ".(trim($args['area']) != '' ? str_escape($args['area']) : "DEFAULT").",
					   fl_situacao                        = ".(trim($args['fl_situacao']) != '' ? str_escape($args['fl_situacao']) : "DEFAULT").",
					   cd_resolucao_diretoria_abrangencia = ".(trim($args['cd_resolucao_diretoria_abrangencia']) != '' ? intval($args['cd_resolucao_diretoria_abrangencia']) : "DEFAULT").",
					   observacao                         = ".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
					   arquivo                            = ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
					   arquivo_nome                       = ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
					   cd_usuario_alteracao               = ".intval($args['cd_usuario']).",
					   dt_alteracao                       = CURRENT_TIMESTAMP 
				 WHERE cd_resolucao_diretoria = ".intval($args['cd_resolucao_diretoria']).";";
		}

        $result = $this->db->query($qr_sql);
		
		return $cd_resolucao_diretoria;
    }
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.resolucao_diretoria
			   SET cd_usuario_exclusao    = ".intval($args['cd_usuario']).",
				   dt_exclusao            = CURRENT_TIMESTAMP 
			 WHERE cd_resolucao_diretoria = ".intval($args['cd_resolucao_diretoria']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function divulgar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT gestao.nr_resolucao_diretoria(c.nr_ano, c.nr_resolucao_diretoria) AS ano_numero,
						   TO_CHAR(c.dt_resolucao_diretoria, 'DD/MM/YYYY') AS dt_resolucao_diretoria,
						   c.ds_resolucao_diretoria
					  FROM gestao.resolucao_diretoria c
					 WHERE c.cd_resolucao_diretoria IN (".implode(",", $args['arr']).")
                       AND c.dt_divulgacao IS NULL 
		          ";
		$ob_resul = $this->db->query($qr_sql);
		$ar_reg   = $ob_resul->result_array();
		
		$nr_conta = 0;
		$itens = "";
		foreach($ar_reg as $ar_item)
		{
			$itens.= $ar_item["ano_numero"]." - ".$ar_item["dt_resolucao_diretoria"]." - ".$ar_item["ds_resolucao_diretoria"].chr(13).chr(10);
			$nr_conta++;
		}
		
		#echo "<PRE>".$qr_sql."</PRE>";
		
		$qr_sql = "
			UPDATE gestao.resolucao_diretoria
			   SET cd_usuario_divulgacao = ".intval($args['cd_usuario']).",
				   dt_divulgacao         = CURRENT_TIMESTAMP,
                   cd_usuario_alteracao  = ".intval($args['cd_usuario']).",
				   dt_alteracao          = CURRENT_TIMESTAMP 				   
			 WHERE cd_resolucao_diretoria IN (".implode(",", $args['arr']).");
			 
			INSERT INTO projetos.envia_emails 
				 (
					dt_envio, 
					de, 
					para, 
					cc, 
					cco, 
					assunto, 
					texto,
					cd_evento,
					cd_usuario
				 )
			VALUES 
				(
					CURRENT_TIMESTAMP, 
					'RESOLUCAO DIRETORIA',                 
					'todos@eletroceee.com.br',
					'',
					'',
					'RESOLUÇÃO DE DIRETORIA', 
					'Est".(intval($nr_conta) == 1 ? "á" : "ão" )." disponív".(intval($nr_conta) == 1 ? "el" : "eis" )." nova".(intval($nr_conta) == 1 ? "" : "s" )." RESOLUÇ".(intval($nr_conta) == 1 ? "ÃO" : "ÕES" ) ." DE DIRETORIA.

".$itens."					
Clique no link abaixo para acessar:
https://www.e-prev.com.br/cieprev/index.php/gestao/resolucao_diretoria
',
					172,
					".intval($args['cd_usuario'])."
				);";
			 
		#echo "<PRE>".$qr_sql."</PRE>";exit;
		$result = $this->db->query($qr_sql);
	}
	
}