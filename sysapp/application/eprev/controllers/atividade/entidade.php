<?php
class entidade extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('entidades/entidade_model');
    }
	
	function index()
    {
		if(gerencia_in(array('GP')))
		{							
			$this->load->view('atividade/entidade/index');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function listar()
    {		
		if(gerencia_in(array('GP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["ds_entidade"]     = $this->input->post("ds_entidade", TRUE);
			$args["cd_recolhimento"] = $this->input->post("cd_recolhimento", TRUE);
			$args["cnpj"]            = $this->input->post("cnpj", TRUE);
			
			manter_filtros($args);
			
			$this->entidade_model->listar($result, $args);
			$data['collection'] = $result->result_array();

			foreach($data['collection'] as $key => $item)
			{
				$args["cd_entidade"] = $item["cd_entidade"];

				$this->entidade_model->listar_recolhimento_entidade($result, $args);
				$arr = $result->result_array();

				$data["collection"][$key]["recolhimento"] = array();

				foreach($arr as $key2 => $item2)
				{
					$data["collection"][$key]["recolhimento"][] = $item2["cd_recolhimento"];
				}
			}
			
			$this->load->view('atividade/entidade/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
	
	function cadastro($cd_entidade = 0, $cd_entidade_recolhimento = 0)
	{
		if(gerencia_in(array('GP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_entidade"] = $cd_entidade;
			
			if(intval($args["cd_entidade"]) == 0)
			{
				$data['row'] = array(
					'cd_entidade'     => intval($cd_entidade),
					'ds_entidade'     => '',
					'cnpj'            => '',
					'telefone1'       => '',
					'telefone2'       => '',
					'dt_exclusao'     => ''
				);
			}
			else
			{
				$this->entidade_model->carrega($result, $args);
                $data['row'] = $result->row_array();

                $this->entidade_model->listar_recolhimento_entidade($result, $args);
				$data['collection'] = $result->result_array();

                $args["cd_entidade_recolhimento"] = $cd_entidade_recolhimento;

                if(intval($args["cd_entidade_recolhimento"]) == 0)
                {
                	$data["recolhimento"] = array(
                		"cd_entidade_recolhimento" => 0,
                		"ds_entidade_recolhimento" => "",
                		"cd_recolhimento"          => ""
            		);
                }
                else
                {
                	$this->entidade_model->carrega_recolhimento($result, $args);
               		$data['recolhimento'] = $result->row_array();
                }
			}
			
			$this->load->view('atividade/entidade/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function salvar()
	{
		if(gerencia_in(array('GP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_entidade"] = $this->input->post("cd_entidade", TRUE);
			$args["ds_entidade"] = $this->input->post("ds_entidade", TRUE);
			$args["cnpj"]        = $this->input->post("cnpj", TRUE);
			$args["telefone1"]   = $this->input->post("telefone1", TRUE);
			$args["telefone2"]   = $this->input->post("telefone2", TRUE);
			$args["cd_usuario"]  = $this->session->userdata('codigo');
			
			$args['cd_entidade'] = $this->entidade_model->salvar($result, $args);
			
			if(intval($this->input->post("cd_entidade", TRUE)) == 0)
			{
				$this->entidade_model->monta_menu($result, $args);

				redirect("atividade/entidade/cadastro/".$args["cd_entidade"], "refresh");
			}
			else
			{
				redirect("atividade/entidade", "refresh");
			}	
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function desativar()
	{
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$args["cd_entidade"] = $this->input->post("cd_entidade", TRUE);
		$args["cd_usuario"]  = $this->session->userdata('codigo');
		
		$this->entidade_model->desativar($result, $args);
	}
	
	function ativar()
	{
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$args["cd_entidade"] = $this->input->post("cd_entidade", TRUE);
		$args["cd_usuario"]  = $this->session->userdata('codigo');
		
		$this->entidade_model->ativar($result, $args);
	}

	function salvar_recolhimento()
	{
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$args["cd_entidade"]              = $this->input->post("cd_entidade", TRUE);
		$args["cd_entidade_recolhimento"] = $this->input->post("cd_entidade_recolhimento", TRUE);
		$args["ds_entidade_recolhimento"] = $this->input->post("ds_entidade_recolhimento", TRUE);
		$args["cd_recolhimento"]          = $this->input->post("cd_recolhimento", TRUE);
		$args["cd_usuario"]               = $this->session->userdata('codigo');

		$this->entidade_model->salvar_recolhimento($result, $args);

		redirect("atividade/entidade/cadastro/".$args["cd_entidade"], "refresh");
	}

	function excluir_recolhimento($cd_entidade, $cd_entidade_recolhimento)
	{
		$result = null;
		$args   = Array();
		$data   = Array();

		$args["cd_entidade"]              = $cd_entidade;
		$args["cd_entidade_recolhimento"] = $cd_entidade_recolhimento;
		$args["cd_usuario"]               = $this->session->userdata('codigo');

		$this->entidade_model->excluir_recolhimento($result, $args);

		redirect("atividade/entidade/cadastro/".$args["cd_entidade"], "refresh");
	}
}
?>