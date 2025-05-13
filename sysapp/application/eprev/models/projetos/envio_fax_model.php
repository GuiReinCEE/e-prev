<?php
class Envio_fax_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		#print_r($args);
		
		$qr_sql = "
					SELECT af.cd_empresa,
						   af.cd_registro_empregado,
						   af.seq_dependencia,
						   p.nome,
						   af.nr_telefone,
						   af.ds_arquivo,
						   TO_CHAR(af.dt_envio, 'DD/MM/YYYY :HH24:MI') AS dt_envio,
						   uc.nome AS nome_usuario
					  FROM projetos.atendimento_fax af
					  JOIN projetos.usuarios_controledi uc
						ON uc.codigo = af.cd_usuario
					  LEFT JOIN public.participantes p
						ON p.cd_empresa            = af.cd_empresa
					   AND p.cd_registro_empregado = af.cd_registro_empregado
					   AND p.seq_dependencia       = af.seq_dependencia
					 WHERE 1 = 1
				  ";

		if(trim($args["cd_empresa"]) != "")
		{
			$qr_sql.= " AND af.cd_empresa = {cd_empresa}";
			esc("{cd_empresa}", $args["cd_empresa"],$qr_sql);
		}		  

		if(trim($args["cd_registro_empregado"]) != "")
		{
			$qr_sql.= " AND af.cd_registro_empregado = {cd_registro_empregado}";
			esc("{cd_registro_empregado}", $args["cd_registro_empregado"],$qr_sql);
		}		
		
		if(trim($args["seq_dependencia"]) != "")
		{
			$qr_sql.= " AND af.seq_dependencia = {seq_dependencia}";
			esc("{seq_dependencia}", $args["seq_dependencia"],$qr_sql);
		}

		if(trim($args["nr_telefone"]) != "")
		{
			$qr_sql.= " AND af.nr_telefone = '{nr_telefone}'";
			esc("{nr_telefone}", $args["nr_telefone"],$qr_sql);
		}		
		
		if(trim($args["cd_usuario"]) != "")
		{
			$qr_sql.= " AND af.cd_usuario = {cd_usuario}";
			esc("{cd_usuario}", $args["cd_usuario"],$qr_sql);
		}		
		
		if((trim($args["dt_envio_inicio"]) != "") and (trim($args["dt_envio_fim"]) != ""))
		{
			$qr_sql.= " AND DATE_TRUNC('day',af.dt_envio) BETWEEN TO_DATE('{dt_ini}','DD/MM/YYYY') AND TO_DATE('{dt_fim}','DD/MM/YYYY')";
			esc("{dt_ini}", $args["dt_envio_inicio"],$qr_sql);
			esc("{dt_fim}", $args["dt_envio_fim"],$qr_sql);
		}		  
		#echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}
	
	
	function listaUsuario()
	{
		$qr_sql = "
					SELECT af.cd_usuario AS value,
						   uc.nome AS text
					  FROM projetos.atendimento_fax af
					  JOIN projetos.usuarios_controledi uc
						ON uc.codigo = af.cd_usuario
				  ";
		#echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
		$retorno = $result->result_array();
		return $retorno;
	}
	
	function salvar($dados,&$e=array())
	{
		$dados['nr_telefone'] = xss_clean($dados['nr_telefone']);
		$dados['ds_arquivo']  = xss_clean($dados['ds_arquivo']);	

		if(trim($dados['cd_empresa']) == '')
		{
			$dados['cd_empresa'] = "DEFAULT";
		}
		else
		{
			$dados['cd_empresa'] = intval($dados['cd_empresa']);
		}
		if(trim($dados['cd_registro_empregado']) == '')
		{
			$dados['cd_registro_empregado'] = "DEFAULT";
		}
		else
		{
			$dados['cd_registro_empregado'] = intval($dados['cd_registro_empregado']);
		}
		if(trim($dados['seq_dependencia']) == '')
		{
			$dados['seq_dependencia'] = "DEFAULT";
		}
		else
		{
			$dados['seq_dependencia'] = intval($dados['seq_dependencia']);
		}		
		
		if(trim($dados['nr_telefone']) == '') 			$e[sizeof($e)] = 'nr_telefone não informado!';
		if(trim($dados['ds_arquivo']) == '') 			$e[sizeof($e)] = 'ds_arquivo não informado!';

		if(sizeof($e)==0)
		{
			$query = $this->db->query( "
				INSERT INTO projetos.atendimento_fax
				     (
					   cd_empresa, 
					   cd_registro_empregado, 
					   seq_dependencia, 
					   nr_telefone, 
					   ds_arquivo, 
					   cd_usuario
					 )
				VALUES 
				     (
					   ".$dados['cd_empresa'].", 
					   ".$dados['cd_registro_empregado'].", 
					   ".$dados['seq_dependencia'].", 
					   ?, 
					   ?, 
					   ?					 
					 );
							
			", array(
				$dados['nr_telefone'],
				$dados['ds_arquivo'],
				intval($this->session->userdata('codigo'))
			) );
			
			if($query)
			{
				return true;
			}
			else
			{
				$e[sizeof($e)] = 'Erro no INSERT INTO';
				return false;
			}
		}
		else
		{
			return FALSE;
		}
	}	
}
