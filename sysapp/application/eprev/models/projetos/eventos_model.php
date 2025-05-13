<?php
class Eventos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT e.cd_evento AS cd_evento, 
				   e.nome AS nome, 
				   p.nome AS nome_projeto, 
				   e.tipo AS tipo
			  FROM projetos.eventos e 
			  JOIN projetos.projetos p
			    ON e.cd_projeto = p.codigo
             WHERE 1 = 1
			   ".(trim($args['nome']) != '' ? "AND UPPER(funcoes.remove_acento(e.nome)) LIKE (UPPER(funcoes.remove_acento('%".trim($args['nome'])."%'))) " : "" )."			
             ORDER BY e.nome;";

		$result = $this->db->query($qr_sql);
	}

	function carrega( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_evento,
				   cd_projeto,
				   nome,
				   tipo,
				   dias_dt_referencia,
				   dt_referencia,
				   indic_historico,
				   indic_email,
				   email
			  FROM projetos.eventos 
             WHERE cd_evento = ".intval($args['cd_evento']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function projeto ( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT codigo AS value, 
				   nome AS text
			  FROM projetos.projetos
			 ORDER BY nome;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function lista_evento ( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT codigo AS value, 
				   descricao AS text
			  FROM listas
			 WHERE categoria = 'EVEN'
			 ORDER BY descricao;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function lista_referencia_evento ( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT codigo AS value, 
				   descricao AS text
			  FROM listas
			 WHERE categoria = 'DTRE'
			 ORDER BY descricao;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function destino ( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_instancia AS value, 
				   nome AS text 
			  FROM projetos.instancias 
			 ORDER BY nome;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function destino_checked ( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_instancia
			  FROM projetos.instancias_eventos
			 WHERE cd_evento = ".intval($args['cd_evento'])."
			 ORDER BY cd_instancia;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function destino_alternativo_checked ( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_instancia
			  FROM projetos.instancias_eventos_sec
			 WHERE cd_evento = ".intval($args['cd_evento'])."
			 ORDER BY cd_instancia;";
			 
		$result = $this->db->query($qr_sql);
	}

	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_evento']) == 0)
		{
			$cd_evento = intval($this->db->get_new_id("projetos.eventos", "cd_evento"));
			
			$qr_sql = "
				INSERT INTO projetos.eventos
				     (
						cd_evento,
						cd_projeto,
						nome,
						tipo,
						dias_dt_referencia,
						dt_referencia,
						indic_historico,
						indic_email,
						email
					 )
				VALUES
				     (
						".intval($cd_evento).",
						".(trim($args['cd_projeto']) != '' ? intval($args['cd_projeto']) : "DEAFULT").",
						".(trim($args['nome']) != '' ? "'".trim($args['nome'])."'" : "DEAFULT").",
						".(trim($args['tipo']) != '' ? "'".trim($args['tipo'])."'" : "DEAFULT").",
						".(trim($args['dias_dt_referencia']) != '' ? intval($args['dias_dt_referencia']) : "DEAFULT").",
						".(trim($args['dt_referencia']) != '' ? "'".trim($args['dt_referencia'])."'" : "DEAFULT").",
						".(trim($args['indic_historico']) != '' ? "'".trim($args['indic_historico'])."'" : "DEAFULT").",
						".(trim($args['indic_email']) != '' ? "'".trim($args['indic_email'])."'" : "DEAFULT").",
						".(trim($args['email']) != '' ? "'".trim($args['email'])."'" : "DEAFULT")."
					 );";
		}
		else
		{
			$cd_evento = intval($args['cd_evento']);
			$qr_sql = "
				UPDATE projetos.eventos
				   SET cd_projeto         = ".(trim($args['cd_projeto']) != '' ? intval($args['cd_projeto']) : "DEAFULT").",
					   nome               = ".(trim($args['nome']) != '' ? "'".trim($args['nome'])."'" : "DEAFULT").",
					   tipo               = ".(trim($args['tipo']) != '' ? "'".trim($args['tipo'])."'" : "DEAFULT").",
					   dias_dt_referencia = ".(trim($args['dias_dt_referencia']) != '' ? intval($args['dias_dt_referencia']) : "DEAFULT").",
					   dt_referencia      = ".(trim($args['dt_referencia']) != '' ? "'".trim($args['dt_referencia'])."'" : "DEAFULT").",
					   indic_historico    = ".(trim($args['indic_historico']) != '' ? "'".trim($args['indic_historico'])."'" : "DEAFULT").",
					   indic_email        = ".(trim($args['indic_email']) != '' ? "'".trim($args['indic_email'])."'" : "DEAFULT").",
					   email              = ".(trim($args['email']) != '' ? "'".trim($args['email'])."'" : "DEAFULT")."
				 WHERE cd_evento = ".intval($cd_evento).";";
			
			$qr_sql .= "
				DELETE 
				  FROM projetos.instancias_eventos
				 WHERE cd_evento = ".intval($cd_evento).";";		
				 
			$qr_sql .= "
				DELETE 
				  FROM projetos.instancias_eventos_sec
				 WHERE cd_evento = ".intval($cd_evento).";";	
			
		}
		
		foreach($args['arr_destino'] as $item)
		{
			$qr_sql .= "
				INSERT INTO projetos.instancias_eventos
					 (
						cd_instancia,
						cd_evento
					 )
				VALUES
					 (
						".intval($item).",
						".intval($cd_evento)."
					 );";
		}
		
		foreach($args['arr_alternativo'] as $item)
		{
			$qr_sql .= "
				INSERT INTO projetos.instancias_eventos_sec
					 (
						cd_instancia,
						cd_evento
					 )
				VALUES
					 (
						".intval($item).",
						".intval($cd_evento)."
					 );";
		}
		
		$result = $this->db->query($qr_sql);
	}
}
?>