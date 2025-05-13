<?php

class deliberacao_conselho_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function listar(&$result, $args=array())
    {
        $qr_sql = "
			SELECT c.cd_deliberacao_conselho,
			       gestao.nr_deliberacao_conselho(c.nr_ano, c.nr_deliberacao_conselho) AS ano_numero,
				   TO_CHAR(c.dt_deliberacao_conselho, 'DD/MM/YYYY') AS dt_deliberacao_conselho,
				   TO_CHAR(c.dt_divulgacao, 'DD/MM/YYYY') AS dt_divulgacao,
				   c.ds_deliberacao_conselho,
				   CASE WHEN c.fl_situacao = 'N' THEN 'label label-success'
						ELSE 'label label-important'
				   END AS class_situacao,
				   CASE WHEN c.fl_situacao = 'N' THEN 'Normal'
						ELSE 'Revogada'
				   END AS situacao,
				   c.observacao,
				   ca.ds_deliberacao_conselho_abrangencia,
				   TO_CHAR(c.dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
				   uc.nome,
				   c.arquivo,
				   c.nr_ata
			  FROM gestao.deliberacao_conselho c
			  LEFT JOIN gestao.deliberacao_conselho_abrangencia ca
				ON ca.cd_deliberacao_conselho_abrangencia = c.cd_deliberacao_conselho_abrangencia
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = c.cd_usuario_alteracao
			 WHERE c.dt_exclusao IS NULL
			 ".(gerencia_in(array('SG')) ? "" : "AND c.dt_divulgacao IS NOT NULL")."
			 ".(trim($args['nr_deliberacao_conselho']) != '' ? "AND c.nr_deliberacao_conselho = ".intval($args['nr_deliberacao_conselho']) : "")." 
			 ".(trim($args['nr_ano']) != '' ? "AND c.nr_ano = ".intval($args['nr_ano']) : "")." 
			 ".(trim($args['nr_ano']) != '' ? "AND c.nr_ano = ".intval($args['nr_ano']) : "")." 
			 ".(((trim($args['dt_ini']) != "") and  (trim($args['dt_fim']) != "")) ? " AND DATE_TRUNC('day', c.dt_deliberacao_conselho) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
			 ".(trim($args["ds_deliberacao_conselho"]) != "" ? "AND UPPER(funcoes.remove_acento(c.ds_deliberacao_conselho)) LIKE UPPER(funcoes.remove_acento('%".trim($args["ds_deliberacao_conselho"])."%'))" : "").";";

        $result = $this->db->query($qr_sql);
    }
	
	function carrega(&$result, $args=array())
    {
        $qr_sql = "
			SELECT cd_deliberacao_conselho,
			       nr_ano,
				   nr_deliberacao_conselho,
				   TO_CHAR(dt_deliberacao_conselho, 'DD/MM/YYYY') AS dt_deliberacao_conselho,
				   ds_deliberacao_conselho,
				   dt_divulgacao,
				   fl_situacao,
				   cd_deliberacao_conselho_abrangencia,
				   observacao,
				   arquivo,
				   arquivo_nome,
				   nr_ata				   
			  FROM gestao.deliberacao_conselho
			 WHERE cd_deliberacao_conselho = ".intval($args['cd_deliberacao_conselho']).";";

        $result = $this->db->query($qr_sql);
    }
	
	function salvar(&$result, $args=array())
    {
		if(intval($args['cd_deliberacao_conselho']) == 0)
		{
			$cd_deliberacao_conselho = intval($this->db->get_new_id("gestao.deliberacao_conselho", "cd_deliberacao_conselho"));
		
			$qr_sql = "
				INSERT INTO gestao.deliberacao_conselho
				     (
					   cd_deliberacao_conselho,
                       ds_deliberacao_conselho, 
					   nr_deliberacao_conselho, 
					   nr_ano, 
					   dt_deliberacao_conselho, 
					   nr_ata,
                       fl_situacao, 
					   cd_deliberacao_conselho_abrangencia, 
					   observacao, 
					   arquivo, 
					   arquivo_nome, 
                       cd_usuario_inclusao, 
					   cd_usuario_alteracao
                     )
                VALUES 
				     (
					    ".intval($cd_deliberacao_conselho).",
						".(trim($args['ds_deliberacao_conselho']) != '' ? str_escape($args['ds_deliberacao_conselho']) : "DEFAULT").",
						".(trim($args['nr_deliberacao_conselho']) != '' ? intval($args['nr_deliberacao_conselho']) : "DEFAULT").",
						".(trim($args['nr_ano']) != '' ? intval($args['nr_ano']) : "DEFAULT").",
						".(trim($args['dt_deliberacao_conselho']) != '' ? "TO_DATE('".trim($args['dt_deliberacao_conselho'])."', 'DD/MM/YYYY')" : "DEFAULT").",
						".(trim($args['nr_ata']) != '' ? intval($args['nr_ata']) : "DEFAULT").",
						".(trim($args['fl_situacao']) != '' ? str_escape($args['fl_situacao']) : "DEFAULT").",
						".(trim($args['cd_deliberacao_conselho_abrangencia']) != '' ? intval($args['cd_deliberacao_conselho_abrangencia']) : "DEFAULT").",
						".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
						".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
						".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$cd_deliberacao_conselho = intval($args['cd_deliberacao_conselho']);
		
			$qr_sql = "
				UPDATE gestao.deliberacao_conselho
				   SET ds_deliberacao_conselho             = ".(trim($args['ds_deliberacao_conselho']) != '' ? str_escape($args['ds_deliberacao_conselho']) : "DEFAULT").",
				       nr_deliberacao_conselho             = ".(trim($args['nr_deliberacao_conselho']) != '' ? intval($args['nr_deliberacao_conselho']) : "DEFAULT").",
					   nr_ano                             = ".(trim($args['nr_ano']) != '' ? intval($args['nr_ano']) : "DEFAULT").",
					   dt_deliberacao_conselho             = ".(trim($args['dt_deliberacao_conselho']) != '' ? "TO_DATE('".trim($args['dt_deliberacao_conselho'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					   nr_ata                             = ".(trim($args['nr_ata']) != '' ? intval($args['nr_ata']) : "DEFAULT").",
					   fl_situacao                        = ".(trim($args['fl_situacao']) != '' ? str_escape($args['fl_situacao']) : "DEFAULT").",
					   cd_deliberacao_conselho_abrangencia = ".(trim($args['cd_deliberacao_conselho_abrangencia']) != '' ? intval($args['cd_deliberacao_conselho_abrangencia']) : "DEFAULT").",
					   observacao                         = ".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
					   arquivo                            = ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
					   arquivo_nome                       = ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
					   cd_usuario_alteracao               = ".intval($args['cd_usuario']).",
					   dt_alteracao                       = CURRENT_TIMESTAMP 
				 WHERE cd_deliberacao_conselho = ".intval($args['cd_deliberacao_conselho']).";";
		}

        $result = $this->db->query($qr_sql);
		
		return $cd_deliberacao_conselho;
    }
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.deliberacao_conselho
			   SET cd_usuario_exclusao    = ".intval($args['cd_usuario']).",
				   dt_exclusao            = CURRENT_TIMESTAMP 
			 WHERE cd_deliberacao_conselho = ".intval($args['cd_deliberacao_conselho']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function divulgar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT gestao.nr_deliberacao_conselho(c.nr_ano, c.nr_deliberacao_conselho) AS ano_numero,
						   TO_CHAR(c.dt_deliberacao_conselho, 'DD/MM/YYYY') AS dt_deliberacao_conselho,
						   c.ds_deliberacao_conselho
					  FROM gestao.deliberacao_conselho c
					 WHERE c.cd_deliberacao_conselho IN (".implode(",", $args['arr']).")
                       AND c.dt_divulgacao IS NULL 
		          ";
		$ob_resul = $this->db->query($qr_sql);
		$ar_reg   = $ob_resul->result_array();
		
		$nr_conta = 0;
		$itens = "";
		foreach($ar_reg as $ar_item)
		{
			$itens.= $ar_item["ano_numero"]." - ".$ar_item["dt_deliberacao_conselho"]." - ".$ar_item["ds_deliberacao_conselho"].chr(13).chr(10);
			$nr_conta++;
		}
		
		#echo "<PRE>".$qr_sql."</PRE>";
		
		$qr_sql = "
			UPDATE gestao.deliberacao_conselho
			   SET cd_usuario_divulgacao = ".intval($args['cd_usuario']).",
				   dt_divulgacao         = CURRENT_TIMESTAMP,
                   cd_usuario_alteracao  = ".intval($args['cd_usuario']).",
				   dt_alteracao          = CURRENT_TIMESTAMP 				   
			 WHERE cd_deliberacao_conselho IN (".implode(",", $args['arr']).");
			 
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
					'DELIBERACAO CONSELHO',                 
					'todos@eletroceee.com.br',
					'',
					'',
					'DELIBERAÇÃO DO CONSELHO', 
					'Est".(intval($nr_conta) == 1 ? "á" : "ão" )." disponív".(intval($nr_conta) == 1 ? "el" : "eis" )." nova".(intval($nr_conta) == 1 ? "" : "s" )." DELIBERAÇ".(intval($nr_conta) == 1 ? "ÃO" : "ÕES" )." DO CONSELHO.

".$itens."					
Clique no link abaixo para acessar:
https://www.e-prev.com.br/cieprev/index.php/gestao/deliberacao_conselho
',
					172,
					".intval($args['cd_usuario'])."
				);";
			 
		#echo "<PRE>".$qr_sql."</PRE>";exit;
		$result = $this->db->query($qr_sql);
	}
}