<?php
class Contracheque_arquivo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar()
	{
		$qr_sql = "
			SELECT MD5(cd_contracheque_arquivo::TEXT) AS cd_contracheque_arquivo,
                   nr_ano || '/'  || TRIM(TO_CHAR(nr_mes,'00')) AS dt_referente, 
			       ds_arquivo_nome, 
			       ds_arquivo_fisico, 
			       TO_CHAR(dt_upload, 'DD/MM/YYYY HH24:MI:SS') AS dt_upload,
			       TO_CHAR(dt_pagamento, 'DD/MM/YYYY') AS dt_pagamento, 
			       TO_CHAR(dt_liberacao, 'DD/MM/YYYY') AS dt_liberacao,
			       TO_CHAR(dt_envio_email, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_email,
				   qt_linha,
				   qt_registro_empregado
			  FROM projetos.contracheque_arquivo
	         ORDER BY dt_referente DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function verifica_data_pagamento($dt_pagamento)
	{
		$qr_sql = "
			SELECT COUNT(*) AS tl_contracheque 
			  FROM projetos.contracheque
             WHERE dt_pgto = TO_DATE('".$dt_pagamento."','DD/MM/YYYY');";

		return $this->db->query($qr_sql)->row_array();
	}
	
	public function salvar($dados, &$e=array())
	{
		if(trim($dados['dt_pagamento']) == '')          $e[sizeof($e)] = 'dt_pagamento não informado!';
		if(trim($dados['cd_usuario_upload']) == '')     $e[sizeof($e)] = 'cd_usuario_upload não informado!';
		if(trim($dados['nr_mes']) == '')                $e[sizeof($e)] = 'nr_mes não informado!';
		if(trim($dados['nr_ano']) == '')                $e[sizeof($e)] = 'nr_ano não informado!';
		if(trim($dados['ds_arquivo_nome']) == '')       $e[sizeof($e)] = 'ds_arquivo_nome não informado!';
		if(trim($dados['ds_arquivo_fisico']) == '')     $e[sizeof($e)] = 'ds_arquivo_fisico não informado!';
		if(trim($dados['qt_linha']) == '')              $e[sizeof($e)] = 'qt_linha não informado!';
		if(trim($dados['qt_registro_empregado']) == '') $e[sizeof($e)] = 'qt_registro_empregado não informado!';
		if($dados['linha'] == '')                       $e[sizeof($e)] = 'linha não informado!';
		if(count($dados['linha']) == 0)                 $e[sizeof($e)] = 'linha não informado!';
		
		$dados['ds_arquivo_nome']   = xss_clean($dados['ds_arquivo_nome']);	
		$dados['ds_arquivo_fisico'] = xss_clean($dados['ds_arquivo_fisico']);		

		if(sizeof($e) == 0)
		{
			$qr_sql = "
				INSERT INTO projetos.contracheque_arquivo
					 (
					   nr_mes, 
					   nr_ano, 
					   ds_arquivo_nome, 
					   ds_arquivo_fisico, 
					   dt_upload, 
					   cd_usuario_upload, 
					   dt_pagamento, 
					   qt_linha, 
					   qt_registro_empregado,
					   dt_liberacao,
					   cd_usuario_liberacao
					 )
				VALUES 
					 (
					   ".$dados['nr_mes'].", 
					   ".$dados['nr_ano'].", 
					   '".$dados['ds_arquivo_nome']."',
					   '".$dados['ds_arquivo_fisico']."',
					   CURRENT_TIMESTAMP,
					   ".$dados['cd_usuario_upload'].",
					   TO_DATE('".$dados['dt_pagamento']."','DD/MM/YYYY'),
					   ".$dados['qt_linha'].",
					   ".$dados['qt_registro_empregado'].",
					   TO_DATE('".$dados['dt_liberacao']."','DD/MM/YYYY'),
					   ".$dados['cd_usuario_upload']."
					 );";
			$ar_linha = $dados['linha'];
			
			foreach($ar_linha as $linha )
			{
				$qr_sql.= "
		            INSERT INTO projetos.contracheque
					     (
					       dt_pgto, 
						   cd_empresa, 
						   cd_registro_empregado, 
						   seq, 
						   divisao, 
						   banco, 
					       agencia, 
						   conta, 
						   codigo, 
						   descricao, 
						   referencia, 
						   valor, 
						   tipo
						 )
			        VALUES
						 (
					       TO_DATE('".$linha['dt_pgto']."','DD/MM/YYYY'), 
						   ".$linha['cd_empresa'].",  
						   ".$linha['cd_registro_empregado'].", 
						   ".$linha['seq_dependencia'].", 
						   '".$linha['divisao']."',   
						   '".$linha['banco']."', 
					       '".$linha['agencia']."', 
						   '".$linha['conta']."', 
						   ".($linha['codigo'] == "" ? "NULL" : "'".$linha['codigo']."'").", 
						   '".$linha['descricao']."',   
						   ".$linha['referencia'].", 
						   ".$linha['valor'].", 
						   '".$linha['tipo']."'
						 );";
			}
			
			$query = $this->db->query($qr_sql);
			if($query)
			{
				return TRUE;
			}
			else
			{
				$e[sizeof($e)] = 'Erro no INSERT INTO';
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}		
}
?>