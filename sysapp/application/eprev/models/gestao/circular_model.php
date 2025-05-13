<?php

class circular_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function listar(&$result, $args=array())
    {
        $qr_sql = "
			SELECT c.cd_circular,
			       gestao.nr_circular(c.nr_ano, c.nr_circular, c.nr_versao) AS ano_numero,
				   TO_CHAR(c.dt_circular, 'DD/MM/YYYY') AS dt_circular,
				   TO_CHAR(c.dt_divulgacao, 'DD/MM/YYYY') AS dt_divulgacao,
				   c.ds_circular,
				   CASE WHEN c.fl_situacao = 'N' THEN 'label label-success'
						ELSE 'label label-important'
				   END AS class_situacao,
				   CASE WHEN c.fl_situacao = 'N' THEN 'Normal'
						ELSE 'Revogada'
				   END AS situacao,
				   c.observacao,
				   ca.ds_circular_abrangencia,
				   TO_CHAR(c.dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
				   uc.nome,
				   c.arquivo
			  FROM gestao.circular c
			  JOIN gestao.circular_abrangencia ca
				ON ca.cd_circular_abrangencia = c.cd_circular_abrangencia
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = c.cd_usuario_alteracao
			 WHERE c.dt_exclusao IS NULL
			 ".(gerencia_in(array('GC')) ? "" : "AND c.dt_divulgacao IS NOT NULL")."
			 ".(trim($args['nr_circular']) != '' ? "AND c.nr_circular = ".intval($args['nr_circular']) : "")." 
			 ".(trim($args['nr_ano']) != '' ? "AND c.nr_ano = ".intval($args['nr_ano']) : "")." 
			 ".(trim($args['nr_ano']) != '' ? "AND c.nr_ano = ".intval($args['nr_ano']) : "")." 
			 ".(((trim($args['dt_ini']) != "") and  (trim($args['dt_fim']) != "")) ? " AND DATE_TRUNC('day', c.dt_circular) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
			 ".(trim($args["ds_circular"]) != "" ? "AND UPPER(funcoes.remove_acento(c.ds_circular)) LIKE UPPER(funcoes.remove_acento('%".trim($args["ds_circular"])."%'))" : "").";";

        $result = $this->db->query($qr_sql);
    }
	
	function carrega(&$result, $args=array())
    {
        $qr_sql = "
			SELECT cd_circular,
			       nr_ano,
				   nr_circular,
				   TO_CHAR(dt_circular, 'DD/MM/YYYY') AS dt_circular,
				   ds_circular,
				   dt_divulgacao,
				   fl_situacao,
				   cd_circular_abrangencia,
				   observacao,
				   arquivo,
				   arquivo_nome
			  FROM gestao.circular
			 WHERE cd_circular = ".intval($args['cd_circular']).";";

        $result = $this->db->query($qr_sql);
    }
	
	function salvar(&$result, $args=array())
    {
		if(intval($args['cd_circular']) == 0)
		{
			$cd_circular = intval($this->db->get_new_id("gestao.circular", "cd_circular"));
		
			$qr_sql = "
				INSERT INTO gestao.circular
				     (
					   cd_circular,
                       ds_circular, 
					   dt_circular, 
                       fl_situacao, 
					   cd_circular_abrangencia, 
					   observacao, 
					   arquivo, 
					   arquivo_nome, 
                       cd_usuario_inclusao, 
					   cd_usuario_alteracao
                     )
                VALUES 
				     (
					    ".intval($cd_circular).",
						".(trim($args['ds_circular']) != '' ? str_escape($args['ds_circular']) : "DEFAULT").",
						".(trim($args['dt_circular']) != '' ? "TO_DATE('".trim($args['dt_circular'])."', 'DD/MM/YYYY')" : "DEFAULT").",
						".(trim($args['fl_situacao']) != '' ? str_escape($args['fl_situacao']) : "DEFAULT").",
						".(trim($args['cd_circular_abrangencia']) != '' ? intval($args['cd_circular_abrangencia']) : "DEFAULT").",
						".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
						".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
						".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$cd_circular = intval($args['cd_circular']);
		
			$qr_sql = "
				UPDATE gestao.circular
				   SET nr_ano                  = ".(trim($args['nr_ano']) != '' ? intval($args['nr_ano']) : "DEFAULT").",
				       nr_circular             = ".(trim($args['nr_circular']) != '' ? intval($args['nr_circular']) : "DEFAULT").",
				       cd_circular_abrangencia = ".(trim($args['cd_circular_abrangencia']) != '' ? intval($args['cd_circular_abrangencia']) : "DEFAULT").",
				       ds_circular             = ".(trim($args['ds_circular']) != '' ? str_escape($args['ds_circular']) : "DEFAULT").",
					   dt_circular             = ".(trim($args['dt_circular']) != '' ? "TO_DATE('".trim($args['dt_circular'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					   fl_situacao             = ".(trim($args['fl_situacao']) != '' ? str_escape($args['fl_situacao']) : "DEFAULT").",
					   observacao              = ".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
					   arquivo                 = ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
					   arquivo_nome            = ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
					   cd_usuario_alteracao    = ".intval($args['cd_usuario']).",
					   dt_alteracao            = CURRENT_TIMESTAMP 
				 WHERE cd_circular = ".intval($args['cd_circular']).";";
		}

        $result = $this->db->query($qr_sql);
		
		return $cd_circular;
    }
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.circular
			   SET cd_usuario_exclusao    = ".intval($args['cd_usuario']).",
				   dt_exclusao            = CURRENT_TIMESTAMP 
			 WHERE cd_circular = ".intval($args['cd_circular']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function divulgar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT gestao.nr_circular(c.nr_ano, c.nr_circular, c.nr_versao) AS ano_numero,
						   TO_CHAR(c.dt_circular, 'DD/MM/YYYY') AS dt_circular,
						   c.ds_circular
					  FROM gestao.circular c
					 WHERE c.cd_circular IN (".implode(",", $args['arr']).")
                       AND c.dt_divulgacao IS NULL 
		          ";
		$ob_resul = $this->db->query($qr_sql);
		$ar_reg   = $ob_resul->result_array();
		
		$nr_conta = 0;
		$itens = "";
		foreach($ar_reg as $ar_item)
		{
			$itens.= $ar_item["ano_numero"]." - ".$ar_item["dt_circular"]." - ".$ar_item["ds_circular"].chr(13).chr(10);
			$nr_conta++;
		}
		
		#echo "<PRE>".$qr_sql."</PRE>";
		
		$qr_sql = "
			UPDATE gestao.circular
			   SET cd_usuario_divulgacao = ".intval($args['cd_usuario']).",
				   dt_divulgacao         = CURRENT_TIMESTAMP,
                   cd_usuario_alteracao  = ".intval($args['cd_usuario']).",
				   dt_alteracao          = CURRENT_TIMESTAMP 				   
			 WHERE cd_circular IN (".implode(",", $args['arr']).");
			 
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
					'CIRCULARES',                 
					'todos@eletroceee.com.br',
					'',
					'',
					'CIRCULAR', 
					'Est".(intval($nr_conta) == 1 ? "á" : "ão" )." disponív".(intval($nr_conta) == 1 ? "el" : "eis" )." nova".(intval($nr_conta) == 1 ? "" : "s" )." CIRCULAR".(intval($nr_conta) == 1 ? "" : "ES" ).".

".$itens."					
Clique no link abaixo para acessar:
https://www.e-prev.com.br/cieprev/index.php/gestao/circular
',
					172,
					".intval($args['cd_usuario'])."
				);";
			 
		#echo "<PRE>".$qr_sql."</PRE>";exit;
		$result = $this->db->query($qr_sql);
	}
}