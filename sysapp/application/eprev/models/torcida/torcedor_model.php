<?php
class Torcedor_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
					SELECT cd_torcedor,
						   nome,
						   email,
						   cd_empresa || '/' || cd_registro_empregado || '/' ||  seq_dependencia as RE,
						   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') as dt_inclusao,
						   TO_CHAR(dt_libera,'DD/MM/YYYY HH24:MI:SS') as dt_libera,
						   cd_usuario_libera,
						   TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao,
						   cd_usuario_exclusao,
						   ip,
						   fl_brinde
					  FROM torcida.torcedor
					 WHERE dt_exclusao IS NULL 
					    {DT_INCLUSAO}
						{FL_BRINDE}
						{FL_LIBERADO}
		       ";

			   
		
		if((trim($args['dt_inclusao_ini']) != "") and (trim($args['dt_inclusao_fim']) != ""))
		{
			$sql = str_replace("{DT_INCLUSAO}","AND CAST(dt_inclusao AS DATE) BETWEEN TO_DATE('".trim($args['dt_inclusao_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_inclusao_fim'])."','DD/MM/YYYY') ",$sql);
		}
		
		if(trim($args['fl_brinde']) != "")
		{
			$sql = str_replace("{FL_BRINDE}","AND fl_brinde = '".trim($args['fl_brinde'])."'",$sql);
		}		
		
		if(trim($args['fl_liberado']) != "")
		{
			$sql = str_replace("{FL_LIBERADO}","AND dt_libera IS NOT NULL",$sql);
		}		
		
		$sql = str_replace("{DT_INCLUSAO}","",$sql);
		$sql = str_replace("{FL_BRINDE}","",$sql);
		$sql = str_replace("{FL_LIBERADO}","",$sql);
		
			   
		// return result ...
		$result = $this->db->query($sql);
	}

	function liberar($cd,$cd_usuario_libera,&$msg=array())
	{
		if( $cd=='' ){ $msg[]='Parametro $cd obrigatório (usar MD5)!'; return false; }
		if( $cd_usuario_libera=='' ){ $msg[]='Parametro $cd_usuario_libera obrigatório!'; return false; }

		$sql="
		UPDATE torcida.torcedor 
		SET dt_libera=current_timestamp, cd_usuario_libera={cd_usuario_libera} 
		WHERE md5(cd_torcedor::varchar) = '{cd_torcedor}' 
		";

		esc("{cd_torcedor}", $cd, $sql, "str", FALSE);
		esc("{cd_usuario_libera}", $cd_usuario_libera, $sql, "int", FALSE);

		try
		{
			$query = $this->db->query($sql);
			return true;
		}
		catch(Exception $e)
		{
			$msg[]=$e->getMessage();
			return false;
		}
	}
	
	function brinde($cd_torcedor, $fl_brinde,&$msg=array())
	{
		$sql = "
				UPDATE torcida.torcedor 
				   SET fl_brinde = '".$fl_brinde."'
				 WHERE cd_torcedor = ".$cd_torcedor."
		       ";
		try
		{
			$query = $this->db->query($sql);
			return true;
		}
		catch(Exception $e)
		{
			$msg[]=$e->getMessage();
			return false;
		}
	}	
	
	function etiqueta( &$result, $args=array() )
	{
		$sql = "
				SELECT p.cd_empresa, 
					   p.cd_registro_empregado, 
					   p.seq_dependencia, 
					   SUBSTRING(p.nome,1,26) AS nome,
					   p.logradouro, 
					   SUBSTRING(p.bairro,1,15) AS bairro, 
					   p.cidade, 
					   TO_CHAR(p.cep,'FM00000') || '-' || TO_CHAR(p.complemento_cep,'FM000') AS cep, 				   
					   funcoes.fnc_codigo_barras_cep(cast(p.cep as bigint), cast(p.complemento_cep as bigint)) AS cep_net,
					   p.unidade_federativa AS uf
				  FROM torcida.torcedor t
				  JOIN public.participantes p
					ON p.cd_empresa            = t.cd_empresa
				   AND p.cd_registro_empregado = t.cd_registro_empregado
				   AND p.seq_dependencia       = t.seq_dependencia
				 WHERE t.dt_exclusao IS NULL 
					{DT_INCLUSAO}
					{FL_BRINDE}
					{FL_LIBERADO};
		       ";
			   
		if((trim($args['dt_inclusao_ini']) != "") and (trim($args['dt_inclusao_fim']) != ""))
		{
			$sql = str_replace("{DT_INCLUSAO}","AND CAST(t.dt_inclusao AS DATE) BETWEEN TO_DATE('".trim($args['dt_inclusao_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_inclusao_fim'])."','DD/MM/YYYY') ",$sql);
		}
		
		if(trim($args['fl_brinde']) != "")
		{
			$sql = str_replace("{FL_BRINDE}","AND t.fl_brinde = '".trim($args['fl_brinde'])."'",$sql);
		}		
		
		if(trim($args['fl_liberado']) != "")
		{
			$sql = str_replace("{FL_LIBERADO}","AND t.dt_libera IS NOT NULL",$sql);
		}		
		
		$sql = str_replace("{DT_INCLUSAO}","",$sql);
		$sql = str_replace("{FL_BRINDE}","",$sql);
		$sql = str_replace("{FL_LIBERADO}","",$sql);	
		
			   
		// return result ...
		$result = $this->db->query($sql);
	}	
	
	function etiquetaMarca($args=array(),&$msg=array())
	{

		$sql = "
				UPDATE torcida.torcedor AS t
				   SET fl_brinde = 'S'
				  FROM public.participantes p
				 WHERE t.dt_exclusao IS NULL 
				   AND p.cd_empresa            = t.cd_empresa
				   AND p.cd_registro_empregado = t.cd_registro_empregado
				   AND p.seq_dependencia       = t.seq_dependencia					   
					{DT_INCLUSAO}
					{FL_BRINDE}
					{FL_LIBERADO};					  
		       ";

		if((trim($args['dt_inclusao_ini']) != "") and (trim($args['dt_inclusao_fim']) != ""))
		{
			$sql = str_replace("{DT_INCLUSAO}","AND CAST(t.dt_inclusao AS DATE) BETWEEN TO_DATE('".trim($args['dt_inclusao_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_inclusao_fim'])."','DD/MM/YYYY') ",$sql);
		}
		
		if(trim($args['fl_brinde']) != "")
		{
			$sql = str_replace("{FL_BRINDE}","AND t.fl_brinde = '".trim($args['fl_brinde'])."'",$sql);
		}		
		
		if(trim($args['fl_liberado']) != "")
		{
			$sql = str_replace("{FL_LIBERADO}","AND t.dt_libera IS NOT NULL",$sql);
		}		
		
		$sql = str_replace("{DT_INCLUSAO}","",$sql);
		$sql = str_replace("{FL_BRINDE}","",$sql);
		$sql = str_replace("{FL_LIBERADO}","",$sql);
	   
		#echo $sql; exit;
			   
		try
		{
			$query = $this->db->query($sql);
			return true;
		}
		catch(Exception $e)
		{
			$msg[]=$e->getMessage();
			return false;
		}
	}	
	
	function bloquear($cd,&$msg=array())
	{
		if( $cd=='' ){ $msg[]='Parametro $cd obrigatório (usar MD5)!'; return false; }

		$sql="
		UPDATE torcida.torcedor
		SET dt_libera=null, cd_usuario_libera=null 
		WHERE md5(cd_torcedor::varchar) = '{cd_torcedor}' 
		";

		esc("{cd_torcedor}", $cd, $sql, "str", FALSE);

		try
		{
			$query = $this->db->query($sql);
			return true;
		}
		catch(Exception $e)
		{
			$msg[]=$e->getMessage();
			return false;
		}
	}
	
	function excluir($id)
	{
				$sql = " 
		UPDATE torcida.torcedor 
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
		WHERE md5(cd_torcedor::varchar)='{cd_torcedor}' 
		"; 
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_torcedor}', $id, $sql, 'str'); 
 
		$query=$this->db->query($sql); 
	}
}
