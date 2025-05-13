<?php
class Pre_venda_contato_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function salvar($dados,&$e=array())
	{
		$insert=(intval($dados['cd_pre_venda_contato'])==0);

		// ------
		// validation ...

		$name='cd_pre_venda'; $dados[$name] = intval($dados[$name]);
		$name='dt_pre_venda_contato_data'; $dados[$name] = xss_clean($dados[$name]);
		$name='dt_pre_venda_contato_hora'; $dados[$name] = xss_clean($dados[$name]);
		$name='dt_envio_inscricao'; $dados[$name] = $this->input->post($name);
		$name='cd_pre_venda_motivo'; $dados[$name] = $this->input->post($name);		
		$name='observacao'; $dados[$name] = xss_clean($dados[$name]);
		$name='cd_usuario_inclusao'; $dados[$name] = intval($dados[$name]);
		$name='cd_pre_venda_local'; $dados[$name] = intval($dados[$name]);
		$name='cd_evento_institucional'; $dados[$name] = $dados[$name];

		if($dados['cd_pre_venda']=='') $e[sizeof($e)] = 'cd_pre_venda não informado!';
		if($dados['dt_pre_venda_contato_data']=='') $e[sizeof($e)] = 'dt_pre_venda_contato_data não informado!';
		if($dados['dt_pre_venda_contato_hora']=='') $e[sizeof($e)] = 'dt_pre_venda_contato_hora não informado!';
		if($dados['cd_usuario_inclusao']=='') $e[sizeof($e)] = 'cd_usuario_inclusao não informado!';

		// ------
		// adjusts ...
		
		// nenhum ajuste foi necessário!
		
		// ------
		// persist ...
		
		if(sizeof($e)==0)
		{
			if($dados['dt_envio_inscricao']!='' && $dados['cd_pre_venda_motivo']>0) $e[sizeof($e)] = 'dt_envio_inscricao E cd_pre_venda_motivo estão preenchidos, apenas 1 deve ser preenchido!';
			// ------
			// adjusts ...
			
			if($dados['dt_envio_inscricao']=='')
			{
				$dt_envio_inscricao = 'null';
			}
			else
			{
				$dt_envio_inscricao = "TO_DATE( ".$this->db->escape($dados['dt_envio_inscricao']).", 'DD/MM/YYYY' )";
			}			
			
			if($insert)
			{
				// INSERT INTO ...
				$query = $this->db->query( "
					INSERT INTO projetos.pre_venda_contato
					     (
			               cd_pre_venda,
			               dt_pre_venda_contato,
			               observacao,
			               dt_inclusao,
			               cd_usuario_inclusao,
			               dt_exclusao,
			               cd_usuario_exclusao,
					       dt_envio_inscricao,
					       cd_pre_venda_motivo,
						   cd_pre_venda_local,
						   cd_evento_institucional
					     )
				    VALUES
				         (
					       ?,
					       TO_TIMESTAMP(?, 'DD/MM/YYYY HH24:MI'),
					       ".(trim($dados['observacao']) == "" ? "NULL" : "'".$dados['observacao']."'").",
					       CURRENT_TIMESTAMP,
					       ?,
					       NULL,
					       NULL,
					       ".$dt_envio_inscricao.",
					       ".($dados['cd_pre_venda_motivo'] == "" ? "NULL" : $dados['cd_pre_venda_motivo']).",
						   ".$dados['cd_pre_venda_local'].",
						   ".(trim($dados['cd_evento_institucional']) == "" ? "NULL" : $dados['cd_evento_institucional'])."
				          );
				", array(
					$dados['cd_pre_venda']
					, $dados['dt_pre_venda_contato_data'] . ' ' . $dados['dt_pre_venda_contato_hora']
					, $dados['cd_usuario_inclusao']
				) );

				if($query)
				{
					return $this->db->insert_id("projetos.pre_venda_contato", "cd_pre_venda_contato");
				}
				else
				{
					$e[sizeof($e)] = 'Erro no INSERT INTO';
					return false;
				}
			}
			else
			{
				// UPDATE ...
				$query = $this->db->query( "

					UPDATE projetos.pre_venda_contato
					   SET cd_pre_venda         = ?,
					       dt_pre_venda_contato = TO_TIMESTAMP(?, 'DD/MM/YYYY HH24:MI'),
					       dt_envio_inscricao   = ".$dt_envio_inscricao.",
					       cd_pre_venda_motivo  = ".($dados['cd_pre_venda_motivo'] == "" ? "NULL" : $dados['cd_pre_venda_motivo']).",
					       observacao           = ".(trim($dados['observacao']) == "" ? "NULL" : "'".$dados['observacao']."'").",
						   cd_pre_venda_local   = ".$dados['cd_pre_venda_local'].",
						   cd_evento_institucional =  ".(trim($dados['cd_evento_institucional']) == "" ? "NULL" : $dados['cd_evento_institucional'])."
					 WHERE cd_pre_venda_contato = ?;

				", array(
					$dados['cd_pre_venda']
					, $dados['dt_pre_venda_contato_data'] . ' ' . $dados['dt_pre_venda_contato_hora']
					, $dados['cd_pre_venda_contato']
				) );

				if($query)
				{
					return $dados['cd_pre_venda'];
				}
				else
				{
					$e[sizeof($e)] = 'Erro no UPDATE';
				}
			}
		}
		else
		{
			// problems!
			return FALSE;
		}

		// ------
	}
	
	private function setup_pagination($count, $page="")
	{
		// Setup pagination
		$config['enable_query_strings'] = FALSE;
		$config['base_url'] = $this->config->item('base_url') . index_page(). '/' . $page;
		$config['per_page'] = 10000;
		$config['total_rows'] = $count;
		$this->pagination->initialize($config);
	}
}
?>