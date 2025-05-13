<?php
class Contracheque_arquivo extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	private function get_permissoa()
	{
		if(($this->session->userdata('indic_04') == '*') OR ($this->session->userdata('indic_05') == 'S'))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function index()
	{
		if($this->get_permissoa())
		{
			$this->load->model('projetos/contracheque_arquivo_model');

			$data['collection'] = $this->contracheque_arquivo_model->listar();

			$this->load->view('cadastro/contracheque_arquivo/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}		
	}

	public function cadastro()
	{
		if($this->get_permissoa())
		{
			$this->load->view('cadastro/contracheque_arquivo/cadastro');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	} 

	public function salvar()
	{
		if($this->get_permissoa())
		{		
			$this->load->model('projetos/contracheque_arquivo_model');
			
			$config['upload_path']   = './up/cc/';
			$config['allowed_types'] = 'txt';
			$config['encrypt_name']  = TRUE;
					
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
				$ar_dado = array(); 

				$ar_file = array('upload_data' => $this->upload->data());
				
				$dt_pagamento = '';
				$qt_linha = 0;
				$qt_registro_empregado = 0;
				$cd_registro_empregado_controle = '';
				$fl_primeiro = true;
				$ar_linha = Array();
				
				$ob_arq = fopen($ar_file['upload_data']['full_path'], 'r');

				while (!feof($ob_arq)) 
				{
					$linha = '';
					$linha = fgets($ob_arq);
					$ar_reg = explode(';', $linha);
					
					if ($linha != '') 
					{
						if ($fl_primeiro) 
						{
							$fl_primeiro = false;
							$dt_pagamento = $ar_reg[0]; # formato DD/MM/YYYY

							$verifica_data = $this->contracheque_arquivo_model->verifica_data_pagamento($dt_pagamento);

							if (intval($verifica_data['tl_contracheque']) > 0) 
							{
								echo('Já foi importado um arquivo com esta data de pagamento');
								exit;
							}
						}
						
						if ($cd_registro_empregado_controle <> $ar_reg[2]) 
						{
						   $qt_registro_empregado++;
						}

						$cd_registro_empregado_controle = $ar_reg[2];
						$qt_linha++;
						
						$ar_linha[] = array(
							'dt_pgto'               => trim($ar_reg[0]), # formato DD/MM/YYYY
							'cd_empresa'            => intval($ar_reg[1]),
							'cd_registro_empregado' => intval($ar_reg[2]),
							'seq_dependencia'       => intval($ar_reg[3]),
							'divisao'               => trim($ar_reg[4]),
							'banco'                 => trim($ar_reg[5]),
							'agencia'               => trim($ar_reg[6]),
							'conta'                 => trim($ar_reg[7]),
							'codigo'                => trim($ar_reg[8]),
							'descricao'             => trim($ar_reg[9]),
							'referencia'            => (intval($ar_reg[10])/100), #Formato 0000180427 = 1804.27
							'valor'                 => (intval($ar_reg[11])/100), #Formato 0000180427 = 1804.27
							'tipo'                  => trim(str_replace("\r","",str_replace("\n","",$ar_reg[12])))
	                    );
					}
				}

				fclose($ob_arq);
				
				$ar_dado['dt_pagamento']          = $dt_pagamento;
				$ar_dado['dt_liberacao']          = $this->input->post('dt_liberacao', TRUE);
				$ar_dado['cd_usuario_upload']     = $this->session->userdata('codigo');
				$ar_dado['nr_mes']                = $this->input->post('nr_mes', TRUE);
				$ar_dado['nr_ano']                = $this->input->post('nr_ano', TRUE);
				$ar_dado['ds_arquivo_nome']       = $ar_file['upload_data']['orig_name'];
				$ar_dado['ds_arquivo_fisico']     = $ar_file['upload_data']['file_name'];
				$ar_dado['qt_linha']              = $qt_linha;
				$ar_dado['qt_registro_empregado'] = $qt_registro_empregado;
				$ar_dado['linha']                 = $ar_linha;
				
				$saved = $this->contracheque_arquivo_model->salvar($ar_dado, $erros);

				if($saved)
				{
					redirect('cadastro/contracheque_arquivo', 'refresh');
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
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}		
	}
}
?>