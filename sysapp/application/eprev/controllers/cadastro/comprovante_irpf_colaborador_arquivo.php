<?php
class comprovante_irpf_colaborador_arquivo extends Controller
{
	function __construct()
	{
		parent::Controller();
		CheckLogin();
		$this->load->model('projetos/comprovante_irpf_colaborador_arquivo_model');
	}
	
	function index()
	{
		$args = Array();
		$data = Array();
		$result = null;				
		
		if($this->session->userdata('indic_04') == "*")
		{
			$this->load->view('cadastro/comprovante_irpf_colaborador_arquivo/index.php');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}	

	function listar()
	{
		$args = Array();
		$data = Array();
		$result = null;				
		
		if($this->session->userdata('indic_04') == "*")
		{
			$args['nr_ano_calendario'] = $this->input->post("nr_ano_calendario", TRUE);   
			$args['nr_ano_exercicio']  = $this->input->post("nr_ano_exercicio", TRUE);   
				
			manter_filtros($args);

			$this->comprovante_irpf_colaborador_arquivo_model->listar($result, $args);
			$data["collection"] = $result->result_array();

			$this->load->view('cadastro/comprovante_irpf_colaborador_arquivo/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}
	
	function liberar()
	{
		$args = Array();
		$data = Array();
		$result = null;				
		
		if($this->session->userdata('indic_04') == "*")
		{
			$args['cd_comprovante_irpf_colaborador'] = $this->input->post("cd_comprovante_irpf_colaborador", TRUE);  
			$args['cd_usuario'] = $this->session->userdata('codigo');			
			$this->comprovante_irpf_colaborador_arquivo_model->liberar($result, $args);
		}
	}

	function excluir()
	{
		$args = Array();
		$data = Array();
		$result = null;				
		
		if($this->session->userdata('indic_04') == "*")
		{
			$args['cd_comprovante_irpf_colaborador'] = $this->input->post("cd_comprovante_irpf_colaborador", TRUE);   
			$args['cd_usuario'] = $this->session->userdata('codigo');	
			$this->comprovante_irpf_colaborador_arquivo_model->excluir($result, $args);
		}
	}	
	
	function cadastro()
	{
		$args = Array();
		$data = Array();
		$result = null;				
		
		if($this->session->userdata('indic_04') == "*")
		{
			$this->load->view('cadastro/comprovante_irpf_colaborador_arquivo/cadastro.php');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}

	function salvar()
	{
		$args = Array();
		$data = Array();
		$result = null;				
		
		if($this->session->userdata('indic_04') == "*")
		{
			$config['upload_path'] = './up/comprovante_irpf_colaborador_arquivo/';
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
				$ar_file = Array('upload_data' => $this->upload->data());
				
				$args['cd_usuario_carga']  = usuario_id();
				$args['ds_arquivo_nome']   = $ar_file['upload_data']['orig_name'];
				$args['ds_arquivo_fisico'] = $ar_file['upload_data']['file_name'];		
				$args['nr_ano_exercicio']  = $this->input->post("nr_ano_exercicio", TRUE);
				$args['nr_ano_calendario'] = $this->input->post("nr_ano_calendario", TRUE);

				$qt_linha = 0;
				$qt_registro = 0;
				$vl_total = 0;
				$ar_linha = Array();
				$ar_infor = Array();
				$ar_colab = Array();
				$nr_index = -1;
				
				$ob_arq = fopen("./up/comprovante_irpf_colaborador_arquivo/".$args['ds_arquivo_fisico'], 'r');		
			
				while (!feof($ob_arq)) 
				{
					$linha = "";
					$linha = fgets($ob_arq);
					$linha = substr($linha,0,100);
					$linha = str_replace("\n","", $linha);
					$linha = str_replace("\r","", $linha);
					
					if(str_replace(" ","",trim(strtoupper($linha))) == "MINISTÉRIODAFAZENDACOMPROVANTEDERENDIMENTOSPAGOSEDE")
					{
						#|                     MINISTÉRIO DA FAZENDA                Comprovante de Rendimentos Pagos e de     |
						$nr_index++;
					}

					if((substr($linha,0,4) == "CPF:") and (strpos($linha, "CPF:") !== false) and (strpos($linha, "Cadastro:") !== false))
					{
						#|CPF:    738.806.200-91      Cadastro:  000007536|
						/*
						echo "<PRE>";
						echo trim(str_replace("CPF:","",substr($linha,0,strpos($linha, "Cadastro:"))));
						echo "<BR>";
						echo trim(str_replace("Cadastro:","",substr($linha,strpos($linha, "Cadastro:"))));
						echo "</PRE>";
						*/
						
						$ar_colab[$nr_index]["CPF"] = trim(str_replace("CPF:","",substr($linha,0,strpos($linha, "Cadastro:"))));
						$ar_colab[$nr_index]["RE"] = trim(str_replace("Cadastro:","",substr($linha,strpos($linha, "Cadastro:"))));
					}
					
					$ar_infor[$nr_index][] = $linha;
				}
				fclose($ob_arq);				
				
				$args['ar_infor'] = $ar_infor;
				$args['ar_colab'] = $ar_colab;
				
				$this->comprovante_irpf_colaborador_arquivo_model->salvar($result, $args);
				redirect("cadastro/comprovante_irpf_colaborador_arquivo", "refresh");
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}	

	function item($cd_comprovante_irpf_colaborador = 0)
	{
		$args = Array();
		$data = Array();
		$result = null;				
		
		if($this->session->userdata('indic_04') == "*")
		{
			$data['cd_comprovante_irpf_colaborador']  = $cd_comprovante_irpf_colaborador;  
			$this->load->view('cadastro/comprovante_irpf_colaborador_arquivo/item.php', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}

	function itemListar()
	{
		$args = Array();
		$data = Array();
		$result = null;				
		
		if($this->session->userdata('indic_04') == "*")
		{
			$args['cd_comprovante_irpf_colaborador'] = $this->input->post("cd_comprovante_irpf_colaborador", TRUE); 
			$args['nome']                            = $this->input->post("nome", TRUE);   
			$args['cd_re_colaborador']               = $this->input->post("cd_re_colaborador", TRUE);   
				
			manter_filtros($args);

			$this->comprovante_irpf_colaborador_arquivo_model->itemListar($result, $args);
			$data["collection"] = $result->result_array();

			$this->load->view('cadastro/comprovante_irpf_colaborador_arquivo/item_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}

	function itemExcluir()
	{
		$args = Array();
		$data = Array();
		$result = null;				
		
		if($this->session->userdata('indic_04') == "*")
		{
			$args['cd_comprovante_irpf_colaborador'] = $this->input->post("cd_comprovante_irpf_colaborador", TRUE);   
			$args['cd_re_colaborador']               = $this->input->post("cd_re_colaborador", TRUE);   
			$args['cd_usuario']                      = $this->session->userdata('codigo');	
			$this->comprovante_irpf_colaborador_arquivo_model->itemExcluir($result, $args);
		}
	}	
}
?>