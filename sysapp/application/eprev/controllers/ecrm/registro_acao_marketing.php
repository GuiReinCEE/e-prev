<?php

class Registro_acao_marketing extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
        $this->load->model("projetos/registro_acao_marketing_model");
    }

    public function index()
    {
    	if (gerencia_in(array('GE')))
        {
			$args = Array();
			$data = Array();
			$result = null;

			$this->load->view('ecrm/registro_acao_marketing/index', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function listar()
    {
		$args = Array();
		$data = Array();
		$result = null;

		$args['ds_registro_acao_marketing'] = $this->input->post("ds_registro_acao_marketing", TRUE);   
		$args['dt_referencia_ini']          = $this->input->post("dt_referencia_ini", TRUE);   
		$args['dt_referencia_fim']          = $this->input->post("dt_referencia_fim", TRUE);   

		manter_filtros($args);

		$this->registro_acao_marketing_model->listar($result, $args);
		$data['collection'] = $result->result_array();

		$this->load->view('ecrm/registro_acao_marketing/index_result', $data);
    }

    public function cadastro($cd_registro_acao_marketing = 0)
    {
    	if (gerencia_in(array('GE')))
        {
			$args = Array();
			$data = Array();
			$result = null;

			$args["cd_registro_acao_marketing"] = $cd_registro_acao_marketing;

			if(intval($args["cd_registro_acao_marketing"]) == 0)
			{
				$data["row"] = array(
					"cd_registro_acao_marketing" => $args["cd_registro_acao_marketing"],
					"ds_registro_acao_marketing" => "",
					"dt_referencia"              => ""
				);
			}
			else
			{
				$this->registro_acao_marketing_model->carrega($result, $args);
	            $data['row'] = $result->row_array();
			}

			$this->load->view('ecrm/registro_acao_marketing/cadastro', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function salvar()
    {
    	if (gerencia_in(array('GE')))
        {
	    	$args = Array();
			$data = Array();
			$result = null;

			$args['cd_registro_acao_marketing'] = $this->input->post("cd_registro_acao_marketing", TRUE);   
			$args['ds_registro_acao_marketing'] = $this->input->post("ds_registro_acao_marketing", TRUE);   
			$args['dt_referencia']              = $this->input->post("dt_referencia", TRUE);   
			$args['cd_usuario']                 = $this->session->userdata("codigo");

			$cd_registro_acao_marketing = $this->registro_acao_marketing_model->salvar($result, $args);

			if(intval($args['cd_registro_acao_marketing']) == 0)
			{
				redirect("ecrm/registro_acao_marketing/cadastro/".$cd_registro_acao_marketing, "refresh");
			}
			else
			{
				redirect("ecrm/registro_acao_marketing/", "refresh");
			}
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function excluir($cd_registro_acao_marketing)
    {
    	if (gerencia_in(array('GE')))
        {
	    	$args = Array();
			$data = Array();
			$result = null;

			$args['cd_registro_acao_marketing'] = $cd_registro_acao_marketing;   
			$args['cd_usuario']                 = $this->session->userdata("codigo");

			$this->registro_acao_marketing_model->excluir($result, $args);

			redirect("ecrm/registro_acao_marketing/", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function acompanhamento($cd_registro_acao_marketing)
    {
    	if (gerencia_in(array('GE')))
        {
	    	$args = Array();
			$data = Array();
			$result = null;

			$args['cd_registro_acao_marketing'] = $cd_registro_acao_marketing;   

			$this->registro_acao_marketing_model->carrega($result, $args);
	        $data['row'] = $result->row_array();

	        $this->load->view('ecrm/registro_acao_marketing/acompanhamento', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function listar_acompanhamento()
    {
    	$args = Array();
		$data = Array();
		$result = null;

		$args['cd_registro_acao_marketing'] = $this->input->post("cd_registro_acao_marketing", TRUE);   

		$this->registro_acao_marketing_model->listar_acompanhamento($result, $args);
		$data['collection'] = $result->result_array();

		foreach($data['collection'] as $key => $item)
		{
			$args['cd_registro_acao_marketing_acompanhamento'] = $item['cd_registro_acao_marketing_acompanhamento'];

			$this->registro_acao_marketing_model->listar_anexo_acompanhamento($result, $args);
			$data['collection'][$key]['anexo'] = $result->result_array();
		}

		$this->load->view('ecrm/registro_acao_marketing/acompanhamento_result', $data);
    }

    public function salvar_acompanhamento()
    {
    	if (gerencia_in(array('GE')))
        {
	    	$args = Array();
			$data = Array();
			$result = null;

			$args['cd_registro_acao_marketing']                = $this->input->post("cd_registro_acao_marketing", TRUE);   
			$args['ds_registro_acao_marketing_acompanhamento'] = $this->input->post("ds_registro_acao_marketing_acompanhamento", TRUE);    
			$args['cd_usuario']                                = $this->session->userdata("codigo");

			$cd_registro_acao_marketing_acompanhamento = $this->registro_acao_marketing_model->salvar_acompanhamento($result, $args);

			$qt_arquivo = intval($this->input->post("arquivo_m_count", TRUE));

			if($qt_arquivo > 0)
			{
				$nr_conta = 0;
				while($nr_conta < $qt_arquivo)
				{
					$result = null;
					$data = Array();
					$args = Array();		
					
					$args['arquivo_nome']  = $this->input->post("arquivo_m_".$nr_conta."_name", TRUE);
					$args['arquivo']       = $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE);
					
					$args['cd_registro_acao_marketing']                = $this->input->post("cd_registro_acao_marketing", TRUE);   
					$args['cd_registro_acao_marketing_acompanhamento'] = $cd_registro_acao_marketing_acompanhamento;    
					$args['cd_usuario']                                = $this->session->userdata("codigo");
					
					$this->registro_acao_marketing_model->salvar_anexo_acompanhamento($result, $args);
					
					$nr_conta++;
				}
			}

			redirect("ecrm/registro_acao_marketing/acompanhamento/".intval($args['cd_registro_acao_marketing']), "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function anexo($cd_registro_acao_marketing)
    {
    	if (gerencia_in(array('GE')))
        {
	    	$args = Array();
			$data = Array();
			$result = null;

			$args['cd_registro_acao_marketing'] = $cd_registro_acao_marketing;   

			$this->registro_acao_marketing_model->carrega($result, $args);
	        $data['row'] = $result->row_array();

	        $this->load->view('ecrm/registro_acao_marketing/anexo', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function listar_anexo()
    {
    	$args = Array();
		$data = Array();
		$result = null;

		$args['cd_registro_acao_marketing'] = $this->input->post("cd_registro_acao_marketing", TRUE);   

		$this->registro_acao_marketing_model->listar_anexo($result, $args);
		$data['collection'] = $result->result_array();

		$this->load->view('ecrm/registro_acao_marketing/anexo_result', $data);
    }

    public function salvar_anexo()
    {
    	if (gerencia_in(array('GE')))
        {
	    	$args = Array();
			$data = Array();
			$result = null;

			$qt_arquivo = intval($this->input->post("arquivo_m_count", TRUE));

			if($qt_arquivo > 0)
			{
				$nr_conta = 0;
				while($nr_conta < $qt_arquivo)
				{
					$result = null;
					$data = Array();
					$args = Array();		
					
					$args['arquivo_nome']  = $this->input->post("arquivo_m_".$nr_conta."_name", TRUE);
					$args['arquivo']       = $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE);
					
					$args['cd_registro_acao_marketing'] = $this->input->post("cd_registro_acao_marketing", TRUE);   
					$args['cd_usuario']                 = $this->session->userdata("codigo");
					
					$this->registro_acao_marketing_model->salvar_anexo($result, $args);
					
					$nr_conta++;
				}
			}

			redirect("ecrm/registro_acao_marketing/anexo/".intval($args['cd_registro_acao_marketing']), "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function excluir_anexo($cd_registro_acao_marketing, $cd_registro_acao_marketing_anexo)
    {
    	if (gerencia_in(array('GE')))
        {
	    	$args = Array();
			$data = Array();
			$result = null;

			$args['cd_registro_acao_marketing_anexo'] = $cd_registro_acao_marketing_anexo;   
			$args['cd_usuario']                       = $this->session->userdata("codigo");

			$this->registro_acao_marketing_model->excluir_anexo($result, $args);

			redirect("ecrm/registro_acao_marketing/anexo/".$cd_registro_acao_marketing, "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
}