<?php

class reuniao_sg_permissao extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
		$this->load->model('projetos/reuniao_sg_permissao_model');
	}
	
	function index()
    {
        if(gerencia_in(array('GIN')) )
        {
			$this->load->view('atividade/reuniao_sg_permissao/index');
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function listar()
    {
        if(gerencia_in(array('GIN')) )
        {
            $result = null;
            $data = Array();
            $args = Array();

            $this->reuniao_sg_permissao_model->listar($result, $args);
            $data['collection'] = $result->result_array();
            
            $this->load->view('atividade/reuniao_sg_permissao/index_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function cadastro($cd_reuniao_sg_permissao = 0)
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args['cd_reuniao_sg_permissao'] = intval($cd_reuniao_sg_permissao);

        if(gerencia_in(array('GIN')) )
        {
            if (intval($args['cd_reuniao_sg_permissao']) == 0)
            {
                $data['row'] = Array(
                  'cd_reuniao_sg_permissao' => 0,
                  'cd_usuario' => '',
                  'divisao' => ''
                );
            }
            else
            {
                $this->reuniao_sg_permissao_model->cadastro($result, $args);
                $data['row'] = $result->row_array();
            }
            $this->load->view('atividade/reuniao_sg_permissao/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function salvar()
    {	
        if(gerencia_in(array('GIN')) )
        {
            $result = null;
            $data = Array();
            $args = Array();

            $args["cd_reuniao_sg_permissao"] = $this->input->post("cd_reuniao_sg_permissao", TRUE);
            $args["cd_usuario"] = $this->input->post("usuario", TRUE);
            $args["cd_usuario_inclusao"] = $this->session->userdata('codigo');

             $this->reuniao_sg_permissao_model->salvar($result, $args);
            redirect("atividade/reuniao_sg_permissao", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function excluir($cd_reuniao_sg_permissao)
    {	
        if(gerencia_in(array('GIN')) )
        {
            $result = null;
            $data = Array();
            $args = Array();

            $args["cd_reuniao_sg_permissao"] = intval($cd_reuniao_sg_permissao);
            $args["cd_usuario"] =$this->session->userdata('codigo');

             $this->reuniao_sg_permissao_model->excluir($result, $args);
            redirect("atividade/reuniao_sg_permissao", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
}
?>