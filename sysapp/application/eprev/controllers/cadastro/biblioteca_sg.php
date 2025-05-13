<?php
class Biblioteca_sg extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model("projetos/biblioteca_sg_model");
    }

    function index()
    {	
		$result = null;
		$args   = Array();
		$data   = Array();
							
		$this->load->view("cadastro/biblioteca_sg/index", $data);
    }

    function listar()
    {		
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$args["cd_usuario"] = $this->session->userdata("codigo");

		manter_filtros($args);
		
		$this->biblioteca_sg_model->listar($result, $args);
		$data["collection"] = $result->result_array();
		
		$this->load->view("cadastro/biblioteca_sg/index_result", $data);
    }

    function cadastro($cd_biblioteca_livro = 0)
	{
		if(gerencia_in(array("SG")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_biblioteca_livro"] = $cd_biblioteca_livro;

			if(intval($cd_biblioteca_livro) == 0)
			{
				$data["row"] = array(
					"cd_biblioteca_livro" => intval($cd_biblioteca_livro),
					"nr_biblioteca_livro" => "",
					"ds_biblioteca_livro" => "",
					"autor"               => ""
				);
			}
			else
			{
				$this->biblioteca_sg_model->carrega($result, $args);
				$data["row"] = $result->row_array();
			}
			
			$this->load->view("cadastro/biblioteca_sg/cadastro", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function salvar()
	{
		if(gerencia_in(array("SG")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_biblioteca_livro"] = $this->input->post("cd_biblioteca_livro", TRUE);
			$args["nr_biblioteca_livro"] = $this->input->post("nr_biblioteca_livro", TRUE);
			$args["ds_biblioteca_livro"] = $this->input->post("ds_biblioteca_livro", TRUE);
			$args["autor"]               = $this->input->post("autor", TRUE);
			$args["cd_usuario"]          = $this->session->userdata("codigo");

			$this->biblioteca_sg_model->salvar($result, $args);
			
			redirect("cadastro/biblioteca_sg", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function excluir($cd_biblioteca_livro)
	{
		if(gerencia_in(array("SG")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_biblioteca_livro"] = $cd_biblioteca_livro;
			$args["cd_usuario"]          = $this->session->userdata("codigo");
			
			$this->biblioteca_sg_model->excluir($result, $args);
			
			redirect("cadastro/biblioteca_sg", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function devolver($cd_biblioteca_livro_movimento)
	{
		if(gerencia_in(array("SG")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_biblioteca_livro_movimento"] = $cd_biblioteca_livro_movimento;
			$args["cd_usuario"]                    = $this->session->userdata("codigo");
			
			$this->biblioteca_sg_model->devolver($result, $args);
			
			redirect("cadastro/biblioteca_sg", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function alugar($cd_biblioteca_livro)
	{
		if(gerencia_in(array("SG")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_biblioteca_livro"] = $cd_biblioteca_livro;

			$this->biblioteca_sg_model->carrega($result, $args);
			$data["row"] = $result->row_array();

			$this->load->view("cadastro/biblioteca_sg/alugar", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function locacao_salvar()
	{
		if(gerencia_in(array("SG")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_biblioteca_livro"] = $this->input->post("cd_biblioteca_livro", TRUE);
			$args["cpf"]                 = $this->input->post("cpf", TRUE);
			$args["cd_usuario"]          = $this->session->userdata("codigo");

			$this->biblioteca_sg_model->alugar($result, $args);
			
			redirect("cadastro/biblioteca_sg", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function busca_participante()
	{
		$result = null;
		$args   = Array();
		$data   = Array();

		$args["cpf"] = $this->input->post("cpf", TRUE);

		$this->biblioteca_sg_model->busca_participante($result, $args);
		$row = $result->row_array();

		echo json_encode($row);
	}

	function historico($cd_biblioteca_livro)
	{
		if(gerencia_in(array("SG")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_biblioteca_livro"] = $cd_biblioteca_livro;

			$this->biblioteca_sg_model->carrega($result, $args);
			$data["row"] = $result->row_array();

			$this->biblioteca_sg_model->historico($result, $args);
			$data["collection"] = $result->result_array();

			$this->load->view("cadastro/biblioteca_sg/historico", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

}