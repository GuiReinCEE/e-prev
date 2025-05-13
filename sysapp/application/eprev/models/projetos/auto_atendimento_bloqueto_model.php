<?php
class Auto_atendimento_bloqueto_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listarArquivo( &$result, &$count, $args=array() )
	{
		$qr_sql = "
					SELECT aaba.cd_auto_atendimento_bloqueto_arquivo AS cd_arquivo, 
					       aaba.ds_arquivo_nome, 
						   aaba.ds_arquivo_fisico, 
                           TO_CHAR(aaba.dt_upload,'DD/MM/YYYY HH24:MI:SS') AS dt_upload,
						   aaba.cd_usuario_upload, 
						   aaba.qt_linha,
						   aaba.qt_registro,
						   aaba.vl_total,
						   TO_CHAR(aaba.dt_carga,'DD/MM/YYYY HH24:MI:SS') AS dt_carga,
						   aaba.cd_usuario_carga,
						   TO_CHAR(aaba.dt_exclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao,
						   TO_CHAR(aaba.dt_envio_email,'DD/MM/YYYY HH24:MI:SS') AS dt_envio_email,
						   TO_CHAR(aaba.dt_envio_banco,'DD/MM/YYYY') AS dt_envio_banco,
						   TO_CHAR(aaba.dt_envio_participantes,'DD/MM/YYYY') AS dt_envio_participantes,
						   TO_CHAR(aaba.dt_bloqueio,'DD/MM/YYYY') AS dt_bloqueio,
						   qt_email,
						   qt_sem_email,
						   aaba.cd_usuario_exclusao,
						   CASE WHEN aaba.cd_usuario_exclusao IS NULL
						        THEN CASE WHEN aaba.qt_registro <> (SELECT COUNT(*)
								                                 FROM projetos.auto_atendimento_bloqueto aab
															    WHERE aab.cd_auto_atendimento_bloqueto_arquivo = aaba.cd_auto_atendimento_bloqueto_arquivo)
									      THEN 'ERRO: Número de registro diferente da quantidade de registros do arquivo'
									      WHEN aaba.vl_total <> (SELECT SUM(valor)
								                                   FROM projetos.auto_atendimento_bloqueto aab
															      WHERE aab.cd_auto_atendimento_bloqueto_arquivo = aaba.cd_auto_atendimento_bloqueto_arquivo)
									      THEN 'ERRO: O valor total é diferente do valor total do arquivo'
									      ELSE 'OK'
										  END
								ELSE 'OK'
						   END AS status
                      FROM projetos.auto_atendimento_bloqueto_arquivo aaba
					 ORDER BY aaba.dt_upload DESC
		       ";
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}

	function listarBloqueto( &$result, &$count, $args=array() )
	{
		$qr_sql = "
					SELECT aab.codigo_cedente, 
					       aab.valor, 
						   aab.nosso_numero, 
						   aab.dia_vencimento, 
						   aab.mes_vencimento, 
						   aab.ano_vencimento, 
						   aab.descricao, 
						   aab.nome, 
						   aab.endereco,
						   aab.cidade, 
						   aab.uf, 
						   aab.cep, 
						   aab.seu_numero, 
						   aab.cd_empresa, 
						   aab.cd_registro_empregado, 
						   aab.seq_dependencia, 
						   TO_CHAR(aab.dt_emissao,'DD/MM/YYYY') AS dt_emissao, 
						   TO_CHAR(aab.dt_vencimento,'DD/MM/YYYY') AS dt_vencimento,
						   aab.cd_auto_atendimento_bloqueto_arquivo,
						   TO_CHAR(aaba.dt_carga,'DD/MM/YYYY HH24:MI:SS') AS dt_carga,
						   aaba.ds_arquivo_nome
                      FROM projetos.auto_atendimento_bloqueto aab
					  LEFT JOIN projetos.auto_atendimento_bloqueto_arquivo aaba
					    ON aaba.cd_auto_atendimento_bloqueto_arquivo = aab.cd_auto_atendimento_bloqueto_arquivo
					 ORDER BY aab.nome ASC
		       ";
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}	

	function enviaArquivo($dados, &$e=array())
	{
		if(trim($dados['cd_usuario_upload']) == '') $e[sizeof($e)] = 'cd_usuario_upload não informado!';
		if(trim($dados['cd_usuario_carga']) == '')  $e[sizeof($e)] = 'cd_usuario_carga não informado!';
		if(trim($dados['ds_arquivo_nome']) == '')   $e[sizeof($e)] = 'ds_arquivo_nome não informado!';
		if(trim($dados['ds_arquivo_fisico']) == '') $e[sizeof($e)] = 'ds_arquivo_fisico não informado!';
		if(trim($dados['qt_linha']) == '')          $e[sizeof($e)] = 'qt_linha não informado!';
		if(trim($dados['qt_registro']) == '')       $e[sizeof($e)] = 'qt_registro não informado!';
		if(trim($dados['vl_total']) == '')          $e[sizeof($e)] = 'vl_total não informado!';
		if($dados['linha'] == '')                   $e[sizeof($e)] = 'linha não informado!';
		if(count($dados['linha']) == 0)             $e[sizeof($e)] = 'linha não informado!';		
		
		$dados['ds_arquivo_nome']   = xss_clean($dados['ds_arquivo_nome']);	
		$dados['ds_arquivo_fisico'] = xss_clean($dados['ds_arquivo_fisico']);		

		if(sizeof($e) == 0)
		{
			
			$NEW_ID = intval($this->db->get_new_id("projetos.auto_atendimento_bloqueto_arquivo", "cd_auto_atendimento_bloqueto_arquivo"));
						
			$qr_sql = "
						INSERT INTO projetos.auto_atendimento_bloqueto_arquivo
						     (
                               cd_auto_atendimento_bloqueto_arquivo,
							   ds_arquivo_nome, 
							   ds_arquivo_fisico, 
                               dt_upload, 
							   cd_usuario_upload,
							   dt_carga,
						       cd_usuario_carga,
						       qt_linha,         
						       qt_registro,
						       vl_total,
							   dt_envio_banco,
							   dt_envio_participantes,
							   dt_bloqueio,
							   tp_origem
							 )
						VALUES 
							 (
							   ".$NEW_ID.",
							   '".$dados['ds_arquivo_nome']."',
							   '".$dados['ds_arquivo_fisico']."',
							   CURRENT_TIMESTAMP,
							   ".$dados['cd_usuario_upload'].",
							   CURRENT_TIMESTAMP,
							   ".$dados['cd_usuario_carga'].",
							   ".$dados['qt_linha'].",
							   ".$dados['qt_registro'].",
							   ".$dados['vl_total'].",
							   TO_DATE('".$dados['dt_envio_banco']."', 'DD/MM/YYYY'),
							   TO_DATE('".$dados['dt_envio_participantes']."', 'DD/MM/YYYY'),
							   TO_DATE('".$dados['dt_bloqueio']."', 'DD/MM/YYYY'),
							   ".((isset($dados['tp_origem']) AND (trim($dados['tp_origem']) != '')) ? str_escape($dados['tp_origem']) : "DEFAULT'")."
							 );			
			          ";

			$ar_linha = $dados['linha'];
			
			foreach($ar_linha as $linha )
			{
				$qr_sql.= "
							INSERT INTO projetos.auto_atendimento_bloqueto
							     (
								   codigo_cedente, 
								   valor, 
								   nosso_numero, 
								   dia_vencimento, 
								   mes_vencimento, 
								   ano_vencimento, 
								   nome, 
								   endereco, 
								   cidade, 
								   uf, 
								   cep, 
								   seu_numero, 
								   cd_empresa, 
								   cd_registro_empregado, 
								   seq_dependencia, 
								   dt_emissao, 
								   dt_vencimento,
								   cd_auto_atendimento_bloqueto_arquivo,
							       descricao
								 )
                            VALUES 
							     (
								   '".$linha['codigo_cedente']."', 
								   ".$linha['valor'].",
								   '".$linha['nosso_numero']."',
								   ".$linha['dia_vencimento'].",
								   ".$linha['mes_vencimento'].",
								   ".$linha['ano_vencimento'].",
								   '".$linha['nome']."',
								   '".$linha['endereco']."', 
								   '".$linha['cidade']."',
								   '".$linha['uf']."',
								   '".$linha['cep']."',
								   '".$linha['seu_numero']."',
								   ".$linha['cd_empresa'].",
								   ".$linha['cd_registro_empregado'].",
								   ".$linha['seq_dependencia'].",
								   TO_DATE('".$linha['dt_emissao']."','DD/MM/YYYY'),
								   TO_DATE('".$linha['dt_vencimento']."','DD/MM/YYYY'),
								   ".$NEW_ID.",
								   ".((isset($linha['descricao']) AND (trim($linha['descricao']) != '')) ? str_escape($linha['descricao']) : "DEFAULT'")." 
								 );							
						  ";
			}					  
					  
			
			#echo "<PRE>";print_r($qr_sql);echo "</PRE>";exit;
			
			$query = $this->db->query($qr_sql);
			if($query)
			{
				#$args = array();
				#$args['cd_arquivo'] = intval($NEW_ID);
				#$args['cd_usuario'] = $this->session->userdata('codigo');
				#print_r($args);
				#$this->enviarEmail($result, $args);
			
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
	
	function limparTabela($dados, &$e=array())
	{
		if(trim($dados['cd_usuario_exclusao']) == '') $e[sizeof($e)] = 'cd_usuario_exclusao não informado!';
		$qr_sql = "
					UPDATE projetos.auto_atendimento_bloqueto_arquivo
					   SET dt_exclusao         = CURRENT_TIMESTAMP,
						   cd_usuario_exclusao = ".$dados['cd_usuario_exclusao']."
					 WHERE dt_exclusao         IS NULL;

					DELETE FROM projetos.auto_atendimento_bloqueto;
				  ";
		#echo "<PRE>";print_r($qr_sql);echo "</PRE>";exit;
		if(sizeof($e) == 0)
		{
			$query = $this->db->query($qr_sql);
			if($query)
			{
				return TRUE;
			}
			else
			{
				$e[sizeof($e)] = 'Erro no DELETE';
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	function deletaArquivo($dados, &$e=array())
	{
		if(trim($dados['cd_arquivo']) == '')          $e[sizeof($e)] = 'cd_arquivo não informado!';
		if(trim($dados['cd_usuario_exclusao']) == '') $e[sizeof($e)] = 'cd_usuario_exclusao não informado!';
		
		$qr_sql = "
					DELETE 
					  FROM projetos.auto_atendimento_bloqueto
					 WHERE cd_auto_atendimento_bloqueto_arquivo = ".$dados['cd_arquivo'].";
					 
					UPDATE projetos.auto_atendimento_bloqueto_arquivo
					   SET dt_exclusao         = CURRENT_TIMESTAMP,
						   cd_usuario_exclusao = ".$dados['cd_usuario_exclusao']."
					 WHERE cd_auto_atendimento_bloqueto_arquivo = ".$dados['cd_arquivo'].";					 
				  ";
		#echo "<PRE>";print_r($qr_sql);echo "</PRE>";exit;
		if(sizeof($e) == 0)
		{
			$query = $this->db->query($qr_sql);
			if($query)
			{
				return TRUE;
			}
			else
			{
				$e[sizeof($e)] = 'Erro no DELETE';
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}			
	}	
	
	function enviarEmail(&$result, $args=array())
	{
		$qr_sql = "
					SELECT rotinas.email_bloqueto_banrisul(".intval($args['cd_arquivo']).", ".intval($args['cd_usuario']).")
		          ";
		$ob_query = $this->db->query($qr_sql);		
	}
}
?>