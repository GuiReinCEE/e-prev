<?php
class Atendimento_reclamatoria_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT ar.cd_atendimento_reclamatoria, 
					       ar.cd_empresa, 
						   ar.cd_registro_empregado, 
						   p.nome,
						   ar.seq_dependencia, 
						   ar.cd_atendimento, 
						   ar.observacao, 
						   TO_CHAR(ar.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
						   TO_CHAR(ar.dt_encerrado,'DD/MM/YYYY HH24:MI') AS dt_encerrado,
						   ar.cd_usuario_inclusao,
						   uc.nome AS usuario
                      FROM projetos.atendimento_reclamatoria ar
					  JOIN public.participantes p
					    ON p.cd_empresa            = ar.cd_empresa           
					   AND p.cd_registro_empregado = ar.cd_registro_empregado
					   AND p.seq_dependencia       = ar.seq_dependencia 
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = ar.cd_usuario_inclusao
					 WHERE ar.dt_exclusao IS NULL
					   ".(trim($args['cd_empresa']) != '' ? "AND ar.cd_empresa = ".intval($args['cd_empresa']) : "")."
					   ".(trim($args['cd_registro_empregado']) != '' ? "AND ar.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
					   ".(trim($args['seq_dependencia']) != '' ? "AND ar.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
					   {PERIODO_DATA}
					 ORDER BY ar.dt_inclusao
		       ";
			   
		if((trim($args["dt_ini"]) != "") and (trim($args["dt_fim"]) != ""))
		{
			$periodo = "AND DATE_TRUNC('day', ar.dt_inclusao) BETWEEN TO_DATE('{dt_ini}','DD/MM/YYYY') AND TO_DATE('{dt_fim}','DD/MM/YYYY')";
			$periodo = str_replace("{dt_ini}", $args["dt_ini"],$periodo);
			$periodo = str_replace("{dt_fim}", $args["dt_fim"],$periodo);
			$qr_sql = str_replace("{PERIODO_DATA}", $periodo, $qr_sql);
		}
		else
		{
			$qr_sql = str_replace("{PERIODO_DATA}", "", $qr_sql);
		}	

		#echo "<PRE>$qr_sql</PRE>";	
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}
	
	function carregar($cd)
	{
		if( intval($cd)>0 )
		{
			$sql = "
					SELECT ar.cd_atendimento_reclamatoria, 
						   ar.cd_empresa, 
						   ar.cd_registro_empregado, 
						   ar.seq_dependencia, 
						   ar.cd_atendimento, 
						   ar.observacao,
						   TO_CHAR(ar.dt_encerrado,'DD/MM/YYYY HH24:MI') AS dt_encerrado
					  FROM projetos.atendimento_reclamatoria ar		
				     WHERE ar.cd_atendimento_reclamatoria = {cd_atendimento_reclamatoria} 
				   ";
			esc("{cd_atendimento_reclamatoria}", intval($cd), $sql);
			$query = $this->db->query($sql);
			$row   = $query->row_array();
		}
		else
		{
			$row = Array();
		}

		return $row;
	}	
	
	function salvar($args)
	{
		if(intval($args["cd_atendimento_reclamatoria"]) == 0)
		{
			$new_id = intval($this->db->get_new_id("projetos.atendimento_reclamatoria", "cd_atendimento_reclamatoria"));
			
			$sql= "
					INSERT INTO projetos.atendimento_reclamatoria
					     (
						   cd_atendimento_reclamatoria, 
						   cd_empresa, 
						   cd_registro_empregado, 
						   seq_dependencia, 
						   cd_atendimento, 
						   observacao, 
						   cd_usuario_inclusao
						 )
                    VALUES 
					     (
							{cd_atendimento_reclamatoria},
							{cd_empresa},
							{cd_registro_empregado},
							{seq_dependencia},
							".(intval($args["cd_atendimento"]) == 0 ? "NULL" :"{cd_atendimento}").",
							".(trim($args["observacao"]) == "" ? "NULL" :"'{observacao}'").",
							{cd_usuario_inclusao}
						 );
			      ";
		}
		else if(intval($args["cd_atendimento_reclamatoria"]) > 0)
		{
			$new_id = $args["cd_atendimento_reclamatoria"];
			$sql= "
					UPDATE projetos.atendimento_reclamatoria
					   SET cd_empresa            = {cd_empresa},
					       cd_registro_empregado = {cd_registro_empregado},
						   seq_dependencia       = {seq_dependencia},
						   cd_atendimento        = ".(intval($args["cd_atendimento"]) == 0 ? "NULL" :"{cd_atendimento}").",
						   observacao            = ".(trim($args["observacao"]) == "" ? "NULL" :"'{observacao}'")."
					 WHERE cd_atendimento_reclamatoria = {cd_atendimento_reclamatoria};
			      ";
		}

		esc("{cd_atendimento_reclamatoria}", $new_id, $sql, "int", FALSE);
		esc("{cd_empresa}", $args["cd_empresa"], $sql, "int", FALSE);
		esc("{cd_registro_empregado}", $args["cd_registro_empregado"], $sql, "int", FALSE);
		esc("{seq_dependencia}", $args["seq_dependencia"], $sql, "int", FALSE);
		esc("{cd_atendimento}", (intval($args["cd_atendimento"]) == 0 ? "NULL" : $args["cd_atendimento"]), $sql, "int", FALSE);
		esc("{observacao}", $args["observacao"], $sql, "str", FALSE);
		esc("{cd_usuario_inclusao}", $args["cd_usuario_inclusao"], $sql, "int", FALSE);
		
		#echo "<PRE>$sql</PRE>";exit;
		$this->db->query($sql);

		return $new_id;
	}	
	
	
	function encerra($args)
	{
		if(intval($args["cd_atendimento_reclamatoria"]) > 0)
		{
			$new_id = $args["cd_atendimento_reclamatoria"];
			$sql= "
					UPDATE projetos.atendimento_reclamatoria
					   SET dt_encerrado         = CURRENT_TIMESTAMP,
					       cd_usuario_encerrado = {cd_usuario_encerrado}
					 WHERE cd_atendimento_reclamatoria = {cd_atendimento_reclamatoria};
			      ";
		}

		esc("{cd_atendimento_reclamatoria}", $args["cd_atendimento_reclamatoria"], $sql, "int", FALSE);
		esc("{cd_usuario_encerrado}", $args["cd_usuario_encerrado"], $sql, "int", FALSE);
		
		#echo "<PRE>$sql</PRE>";exit;
		$this->db->query($sql);
	}

	function acompanhamento(&$result, $args)
	{
		$qr_sql = "
					SELECT ara.cd_atendimento_reclamatoria_acompanhamento, 
						   ara.observacao, 
						   TO_CHAR(ara.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
						   ara.cd_usuario_inclusao,
						   uc.nome AS usuario
                      FROM projetos.atendimento_reclamatoria_acompanhamento ara
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = ara.cd_usuario_inclusao
					 WHERE ara.cd_atendimento_reclamatoria = {cd_atendimento_reclamatoria}
					 ORDER BY ara.dt_inclusao
		       ";
			   
		esc("{cd_atendimento_reclamatoria}", $args['cd_atendimento_reclamatoria'], $qr_sql, "int", FALSE);	

		#echo "<PRE>$qr_sql</PRE>";	
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();	
	}
	
	
	function salvarAcompanhamento($args)
	{
		$sql= "
				INSERT INTO projetos.atendimento_reclamatoria_acompanhamento
					 (
					   cd_atendimento_reclamatoria, 
					   observacao, 
					   cd_usuario_inclusao
					 )
				VALUES 
					 (
						{cd_atendimento_reclamatoria},
						".(trim($args["observacao"]) == "" ? "NULL" :"'{observacao}'").",
						{cd_usuario_inclusao}
					 );
			      ";


		esc("{cd_atendimento_reclamatoria}", $args["cd_atendimento_reclamatoria"], $sql, "int", FALSE);
		esc("{observacao}", $args["observacao"], $sql, "str", FALSE);
		esc("{cd_usuario_inclusao}", $args["cd_usuario_inclusao"], $sql, "int", FALSE);
		
		#echo "<PRE>$sql</PRE>";exit;
		$this->db->query($sql);

		return true;
	}	

	public function anexo($cd_atendimento_reclamatoria)
	{
		$qr_sql = "
			SELECT cd_atendimento_reclamatoria_arquivo,
				   arquivo,
				   arquivo_nome,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(cd_usuario_inclusao) AS nome
			  FROM projetos.atendimento_reclamatoria_arquivo
			 WHERE cd_atendimento_reclamatoria = ".intval($cd_atendimento_reclamatoria)."
			   AND dt_exclusao IS NULL
			 ORDER BY dt_inclusao DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function salvar_anexo($cd_atendimento_reclamatoria, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.atendimento_reclamatoria_arquivo
			     (
					cd_atendimento_reclamatoria,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($cd_atendimento_reclamatoria).",
					'".trim($args['arquivo'])."',
					'".trim($args['arquivo_nome'])."',
					".intval($args['cd_usuario'])."
				 );";
		
		$this->db->query($qr_sql);
	}
	
	public function excluir_anexo($cd_atendimento_reclamatoria_arquivo, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.atendimento_reclamatoria_arquivo
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_atendimento_reclamatoria_arquivo = ".intval($cd_atendimento_reclamatoria_arquivo).";";

		$this->db->query($qr_sql);
	}
}
?>