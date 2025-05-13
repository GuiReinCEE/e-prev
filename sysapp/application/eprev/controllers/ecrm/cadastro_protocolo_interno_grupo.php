<?php

class Cadastro_protocolo_interno_grupo extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
        $this->load->model("projetos/documento_recebido_grupo_model");
    }

    public function index()
    {
        $args = Array();
        $data = Array();
        $result = null;
				
		$this->documento_recebido_grupo_model->grupo($result, $args);
		$data["arr_grupo"] = $result->result_array();

        $this->load->view("ecrm/cadastro_protocolo_interno_grupo/index", $data);
    }

    public function listar()
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $args["cd_documento_recebido_grupo"] = $this->input->post("cd_documento_recebido_grupo", TRUE);   
		            
        manter_filtros($args);

        $this->documento_recebido_grupo_model->listar($result, $args);
        $collection = $result->result_array();
		
		$data["collection"] = array();
		
		$i = 0;

		foreach($collection as $item)
		{
			$args["cd_documento_recebido_grupo"] = $item["cd_documento_recebido_grupo"];
			
			$data["collection"][$i] = $item;
			
			$this->documento_recebido_grupo_model->usuario_grupo($result, $args);
			$data["collection"][$i]["usuario"] = $result->result_array();
			
			$i ++;
		}

        $this->load->view("ecrm/cadastro_protocolo_interno_grupo/index_result", $data);
    }

    function cadastro($cd_documento_recebido_grupo = 0)
    {
		if(gerencia_in(array("GI")))
		{
			$args = Array();
			$data = Array();
			$result = null;

			$args["cd_documento_recebido_grupo"] = intval($cd_documento_recebido_grupo);
			$args["cd_usuario"]                  = $this->session->userdata("codigo");
			
			if ($cd_documento_recebido_grupo == 0)
			{
				$data["row"] = Array(
				  "cd_documento_recebido_grupo" => 0,
				  "ds_nome"                     => "",
				  "email_grupo"                 => ""
				);
			}
			else
			{			
				$this->documento_recebido_grupo_model->carrega($result, $args);
				$data["row"] = $result->row_array();
			}

			$this->load->view("ecrm/cadastro_protocolo_interno_grupo/cadastro", $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function salvar()
	{
		if(gerencia_in(array("GI")))
		{
			$args = Array();
			$data = Array();
			$result = null;
		
			$args["cd_documento_recebido_grupo"] = $this->input->post("cd_documento_recebido_grupo", TRUE);  
			$args["ds_nome"]                     = $this->input->post("ds_nome", TRUE);  
			$args["email_grupo"]                 = $this->input->post("email_grupo", TRUE);  
			$args["cd_usuario"]                  = $this->session->userdata("codigo");

			$cd_documento_recebido_grupo = $this->documento_recebido_grupo_model->salvar($result, $args);
			
			if(intval($args["cd_documento_recebido_grupo"]) == 0)
			{
				redirect("ecrm/cadastro_protocolo_interno_grupo/usuario/".$cd_documento_recebido_grupo, "refresh");
			}
			else
			{
				redirect("ecrm/cadastro_protocolo_interno_grupo", "refresh");
			}
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	public function excluir($cd_documento_recebido_grupo)
	{
		if(gerencia_in(array("GI")))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_documento_recebido_grupo"] = $cd_documento_recebido_grupo;
			$args["cd_usuario"]                  = $this->session->userdata("codigo");
			
			$this->documento_recebido_grupo_model->excluir($result, $args);
			
			redirect("ecrm/cadastro_protocolo_interno_grupo", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	public function usuario($cd_documento_recebido_grupo)
	{
		if(gerencia_in(array("GI")))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_documento_recebido_grupo"] = $cd_documento_recebido_grupo;

			$this->documento_recebido_grupo_model->carrega($result, $args);
			$data["row"] = $result->row_array();
			
			$this->documento_recebido_grupo_model->usuario_not_grupo($result, $args);
			$data["arr_usuario"] = $result->result_array();
			
			$this->documento_recebido_grupo_model->usuario_grupo($result, $args);
			$data["collection"] = $result->result_array();
			
			$this->load->view("ecrm/cadastro_protocolo_interno_grupo/usuario", $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	public function salvar_usuario()
	{
		if(gerencia_in(array("GI")))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_documento_recebido_grupo"] = $this->input->post("cd_documento_recebido_grupo", TRUE);   
			$args["cd_usuario"]                  = $this->input->post("cd_usuario", TRUE);    		
			$args["cd_usuario_inclusao"]         = $this->session->userdata("codigo");
			
			$this->documento_recebido_grupo_model->salvar_usuario($result, $args);
			
			redirect("ecrm/cadastro_protocolo_interno_grupo/usuario/".$args["cd_documento_recebido_grupo"], "refresh");	
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	public function excluir_usuario($cd_documento_recebido_grupo, $cd_documento_recebido_grupo_usuario)
	{
		if(gerencia_in(array('GAD')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_documento_recebido_grupo"]         = $cd_documento_recebido_grupo;
			$args["cd_documento_recebido_grupo_usuario"] = $cd_documento_recebido_grupo_usuario;
			$args["cd_usuario"]                          = $this->session->userdata('codigo');
			
			$this->documento_recebido_grupo_model->excluir_usuario($result, $args);
			
			redirect("ecrm/cadastro_protocolo_interno_grupo/usuario/".intval($cd_documento_recebido_grupo), "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
}