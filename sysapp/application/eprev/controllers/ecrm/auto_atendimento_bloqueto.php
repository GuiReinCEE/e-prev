<?php
class auto_atendimento_bloqueto extends Controller
{
	function __construct()
	{
		parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/Auto_atendimento_bloqueto_model');
	}

	function index()
	{
		if(gerencia_in(array('GFC','GI')))
		{
			$this->load->view('ecrm/auto_atendimento_bloqueto/index');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}

	function bloqueto()
	{
		if(gerencia_in(array('GFC','GI')))
		{
			$this->load->view('ecrm/auto_atendimento_bloqueto/bloqueto');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}
	
	function detalhe()
	{
		if(gerencia_in(array('GFC','GI')))
		{
			$this->load->view('ecrm/auto_atendimento_bloqueto/detalhe');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}	

	function listar_arquivo()
    {		
		if(gerencia_in(array('GFC','GI')))
		{			
			$data['collection'] = array();
			$result = null;

			// --------------------------

			$count = 0;
			$args['page'] = $this->input->post('current_page');

			$this->Auto_atendimento_bloqueto_model->listarArquivo( $result, $count, $args );

			$data['quantos'] = $count;
			if( $result )
			{
				$data['collection'] = $result->result_array();
			}

			$this->load->view('ecrm/auto_atendimento_bloqueto/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
	
	function listar_bloqueto()
    {
		if(gerencia_in(array('GFC','GI')))
		{
			$data['collection'] = array();
			$result = null;

			// --------------------------

			$count = 0;
			$args['page'] = $this->input->post('current_page');

			$this->Auto_atendimento_bloqueto_model->listarBloqueto( $result, $count, $args );

			$data['quantos'] = $count;
			if( $result )
			{
				$data['collection'] = $result->result_array();
			}

			$this->load->view('ecrm/auto_atendimento_bloqueto/bloqueto_partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	

	
	function envia_arquivo()
	{
		CheckLogin();
		if(gerencia_in(array('GFC','GI')))
		{
			$this->load->model('projetos/Auto_atendimento_bloqueto_model');
			
			$config['upload_path'] = './up/bloqueto/';
			$config['allowed_types'] = 'txt';
			$config['encrypt_name'] = TRUE;
					
			$this->load->library('upload', $config);
		
			if (!$this->upload->do_upload())
			{
				$error = array('error' => $this->upload->display_errors());
				echo '<pre>';
				var_dump($error);
				echo '</pre>';
				exit;			
			}	
			else
			{
				$ar_dado = Array(); 
				$ar_file = Array('upload_data' => $this->upload->data());
				
				$ar_dado['cd_usuario_upload']      = usuario_id();
				$ar_dado['cd_usuario_carga']       = usuario_id();
				$ar_dado['ds_arquivo_nome']        = $ar_file['upload_data']['orig_name'];
				$ar_dado['ds_arquivo_fisico']      = $ar_file['upload_data']['file_name'];		
				$ar_dado['dt_envio_banco']         = $this->input->post("dt_envio_banco", TRUE);
				$ar_dado['dt_envio_participantes'] = $this->input->post("dt_envio_participantes", TRUE);
				$ar_dado['dt_bloqueio']            = $this->input->post("dt_bloqueio", TRUE);
				$ar_dado['tp_origem']              = $this->input->post("tp_origem", TRUE);

				$qt_linha = 0;
				$qt_registro = 0;
				$vl_total = 0;
				$ar_linha = Array();
				
				$ob_arq = fopen("./up/bloqueto/".$ar_dado['ds_arquivo_fisico'], 'r');
				
				while (!feof($ob_arq)) 
				{
					$linha = "";
					$linha = fgets($ob_arq);
					
					if (substr($linha, 0, 3) == '1  ')
					{
						$vl_valor = trim(substr($linha, 126,11).".".substr($linha, 137, 2));
						$vl_total+= $vl_valor;
						$ar_linha[] = Array (
												'codigo_cedente'        => trim(substr($linha, 17, 12)),
												'valor'                 => $vl_valor,
												'nosso_numero'          => trim(substr($linha, 62, 10)),
												'dia_vencimento'        => intval(trim(substr($linha, 120, 2))),
												'mes_vencimento'        => intval(trim(substr($linha, 122, 2))),
												'ano_vencimento'        => intval(trim(substr($linha, 124, 2))),
												'nome'                  => trim(str_replace("'","", substr($linha, 234, 35))),
												'endereco'              => trim(str_replace("'","", substr($linha, 274, 35))),
												'cidade'                => trim(str_replace("'","", substr($linha, 334, 15))),
												'uf'                    => trim(substr($linha, 349, 2)),
												'cep'                   => trim(substr($linha, 326, 8)),
												'seu_numero'            => trim(substr($linha, 110, 10)),
												'cd_empresa'            => intval(trim(substr($linha, 62,1))),
												'cd_registro_empregado' => intval(trim(substr($linha, 62,7))),
												'seq_dependencia'       => intval(trim(substr($linha, 69,1))),
												'dt_emissao'            => trim(substr($linha, 150, 2)."/".substr($linha, 152, 2)."/20".substr($linha, 154, 2)), #Formato DD/MM/YYY
												'dt_vencimento'         => trim(substr($linha, 120, 2)."/".substr($linha, 122, 2)."/20".substr($linha, 124, 2)),  #Formato DD/MM/YYY
												'descricao'             => ""
											);
						$qt_registro++;
					}
					else if (substr($linha, 0, 17) == '10290884412000124')
					{
						$descricao = str_replace("'","", substr($linha, 110, 273));
						$descricao = str_replace(chr(0),"", $descricao);
						
						if(substr($descricao, 0, 25) == '** PRIMEIRO VENCIMENTO EM')
						{
							#### ARQUIVO AUTOPATROCINIO ####
							$desc = Array();
							$descricao = trim(substr($descricao, 182));
							
							#echo "<PRE><HR>".$qt_linha."|".$descricao."<BR>";
							
							$td = 43;
							$t = strlen($descricao);
							$q = intval($t / $td);
							$q = (intval($q) == 0 ? 1 : $q);
							
							$i = 0;
							$x = 1;
							
							$ds = $descricao;
							#echo $t."|".$q."<BR>";
							while($x <= $q)
							{
								$desc[] = trim(substr(trim($ds), 0,$td)); 
								$ds = trim(substr(trim($ds),$td));
								$x++;
							}
							
							#print_r($desc);
						}
						else
						{
							$desc = Array();
							$descricao = str_replace('1** ESTE PAGAMENTO PODE SER EFETUADO DIRETAMENTE PELO BANRIFONE (0XX 51) 3210.0122 **       INFORMAMOS ABAIXO A COMPOSICAO DE SUA DIVIDA NA ELETROCEEE',"", $descricao);
							$descricao = trim($descricao);
							
							$td = 43;
							$t = strlen($descricao);
							$q = intval($t / $td);
							$q = (intval($q) == 0 ? 1 : $q);
							
							$i = 0;
							$x = 1;
							
							$ds = $descricao;
							while($x <= $q)
							{
								$desc[] = trim(substr(trim($ds), 0,$td)); 
								$ds = trim(substr(trim($ds),$td));
								$x++;
							}
						}
						
						$ar_linha[(count($ar_linha) -1)]['descricao'].= (trim($ar_linha[(count($ar_linha) -1)]['descricao']) == "" ? "" : PHP_EOL).implode(PHP_EOL, $desc); #print_r($desc,true);
					}
					$qt_linha++;
				}
				fclose($ob_arq);					

				
				$ar_dado['qt_linha']         = $qt_linha;
				$ar_dado['qt_registro']      = $qt_registro;
				$ar_dado['linha']            = $ar_linha;	
				$ar_dado['vl_total']         = $vl_total;	

				#echo "<PRE>";print_r($ar_dado);echo "</PRE>";exit;

				$saved = $this->Auto_atendimento_bloqueto_model->enviaArquivo($ar_dado, $erros);
				if($saved)
				{
					redirect("ecrm/auto_atendimento_bloqueto", "refresh");
				}
				else
				{
					echo '<pre>';
					var_dump($erros);
					echo '</pre>';
					exit;
				}
			}		
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
		
	}
	
	function limpar_tabela()
    {
        CheckLogin();
		
		if(gerencia_in(array('GFC','GI')))
		{
			$this->load->model('projetos/Auto_atendimento_bloqueto_model');
			$erros = Array();
			$ar_dado = Array();
			
			$ar_dado['cd_usuario_exclusao'] = usuario_id();			
			
			$saved = $this->Auto_atendimento_bloqueto_model->limparTabela($ar_dado,$erros);
			if($saved)
			{
				redirect("ecrm/auto_atendimento_bloqueto/bloqueto", "refresh");
			}
			else
			{
				echo '<pre>';
				var_dump($erros);
				echo '</pre>';
				exit;
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
	function deleta_arquivo()
    {
        CheckLogin();
		
		if(gerencia_in(array('GFC','GI')))
		{
			$this->load->model('projetos/Auto_atendimento_bloqueto_model');
			$erros = Array();
			$ar_dado = Array();
			
			$ar_dado['cd_arquivo']          = $this->input->post('cd_arquivo');
			$ar_dado['cd_usuario_exclusao'] = usuario_id();
			
			$saved = $this->Auto_atendimento_bloqueto_model->deletaArquivo($ar_dado, $erros);
			if(!$saved)
			{
				echo '<pre>';
				var_dump($erros);
				echo '</pre>';
				exit;
			}			
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
	function enviar_email($cd_arquivo = 0)
    {
        $args   = Array();
		$result = null;
		
		CheckLogin();
		
		if(gerencia_in(array('GFC','GI')))
		{
			if(intval($cd_arquivo) > 0)
			{
				$this->load->model('projetos/Auto_atendimento_bloqueto_model');
				
				$args['cd_arquivo'] = intval($cd_arquivo);
				$args['cd_usuario'] = usuario_id();
				
				#print_r($args);
				
				$this->Auto_atendimento_bloqueto_model->enviarEmail($result, $args);
				
				redirect("ecrm/auto_atendimento_bloqueto", "refresh");
			}
			else
			{
				exibir_mensagem("CÓDIGO INVÁLIDO");
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }		

    public function teste_arquivo()
    {
		$ob_arq = fopen("./up/bloqueto/7921616d68d844922e6e356f7eb311ad.TXT", 'r');
				echo '<pre>';
				$qt_linha = 0;
				$vl_total = 0;
				$qt_registro = 0;
				while (!feof($ob_arq)) 
				{
					$linha = "";
					$linha = fgets($ob_arq);
					
					if (substr($linha, 0, 3) == '1  ')
					{
						$vl_valor = trim(substr($linha, 126,11).".".substr($linha, 137, 2));
						$vl_total+= $vl_valor;

						$ar_linha[] = Array (
												'codigo_cedente'        => trim(substr($linha, 17, 12)),
												'valor'                 => $vl_valor,
												'nosso_numero'          => trim(substr($linha, 62, 10)),
												'dia_vencimento'        => intval(trim(substr($linha, 120, 2))),
												'mes_vencimento'        => intval(trim(substr($linha, 122, 2))),
												'ano_vencimento'        => intval(trim(substr($linha, 124, 2))),
												'nome'                  => trim(str_replace("'","", substr($linha, 234, 35))),
												'endereco'              => trim(str_replace("'","", substr($linha, 274, 35))),
												'cidade'                => trim(str_replace("'","", substr($linha, 334, 15))),
												'uf'                    => trim(substr($linha, 349, 2)),
												'cep'                   => trim(substr($linha, 326, 8)),
												'seu_numero'            => trim(substr($linha, 110, 10)),
												'cd_empresa'            => intval(trim(substr($linha, 62,1))),
												'cd_registro_empregado' => intval(trim(substr($linha, 62,7))),
												'seq_dependencia'       => intval(trim(substr($linha, 69,1))),
												/*
												'cd_empresa'            => intval(trim(substr($linha, 62,1))),
												'cd_registro_empregado' => intval(trim(substr($linha, 63,6))),
												'seq_dependencia'       => intval(trim(substr($linha, 69,1))),
												*/
												'dt_emissao'            => trim(substr($linha, 150, 2)."/".substr($linha, 152, 2)."/20".substr($linha, 154, 2)), #Formato DD/MM/YYY
												'dt_vencimento'         => trim(substr($linha, 120, 2)."/".substr($linha, 122, 2)."/20".substr($linha, 124, 2)),  #Formato DD/MM/YYY
												'descricao'             => ""
											);
						$qt_registro++;
/*
												print_r( Array (
												'codigo_cedente'        => trim(substr($linha, 17, 12)),
												'valor'                 => $vl_valor,
												'nosso_numero'          => trim(substr($linha, 62, 10)),
												'dia_vencimento'        => intval(trim(substr($linha, 120, 2))),
												'mes_vencimento'        => intval(trim(substr($linha, 122, 2))),
												'ano_vencimento'        => intval(trim(substr($linha, 124, 2))),
												'nome'                  => trim(str_replace("'","", substr($linha, 234, 35))),
												'endereco'              => trim(str_replace("'","", substr($linha, 274, 35))),
												'cidade'                => trim(str_replace("'","", substr($linha, 334, 15))),
												'uf'                    => trim(substr($linha, 349, 2)),
												'cep'                   => trim(substr($linha, 326, 8)),
												'seu_numero'            => trim(substr($linha, 110, 10)),
												'cd_empresa'            => intval(trim(substr($linha, 62,1))),
												'cd_registro_empregado' => intval(trim(substr($linha, 63,6))),
												'seq_dependencia'       => intval(trim(substr($linha, 69,1))),
												'dt_emissao'            => trim(substr($linha, 150, 2)."/".substr($linha, 152, 2)."/20".substr($linha, 154, 2)), #Formato DD/MM/YYY
												'dt_vencimento'         => trim(substr($linha, 120, 2)."/".substr($linha, 122, 2)."/20".substr($linha, 124, 2)),  #Formato DD/MM/YYY
												'descricao'             => ""
											));
											*/
					}

					$qt_linha++;
				}
				fclose($ob_arq);


			echo $qt_registro.br();
			print_r($ar_linha);
			exit;
    }
}
?>