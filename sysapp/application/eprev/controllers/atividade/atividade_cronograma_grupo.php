<?php

class atividade_cronograma_grupo extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
        $this->load->model('projetos/atividade_cronograma_grupo_model');
    }
	
	public function index()
    {
		if (gerencia_in(array('GI')))
        {
			$args = array();
			$data = array();
			$result = null;
			
			$this->atividade_cronograma_grupo_model->usuarios($result, $args);
			$data['arr_usuarios'] = $result->result_array();

			$this->load->view('atividade/atividade_cronograma_grupo/index', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	public function listar()
    {
		if (gerencia_in(array('GI')))
        {
			$args = array();
			$data = array();
			$result = null;
			
			$args['cd_usuario'] = $this->input->post("cd_usuario", TRUE);
			
			manter_filtros($args);
				
			$this->atividade_cronograma_grupo_model->listar($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('atividade/atividade_cronograma_grupo/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function cadastro($cd_atividade_cronograma_grupo = 0)
    {
        if (gerencia_in(array('GI')))
        {
            $args = array();
			$data = array();
			$result = null;

            $args['cd_atividade_cronograma_grupo'] = intval($cd_atividade_cronograma_grupo);
			
            if ($args['cd_atividade_cronograma_grupo'] == 0)
            {			
                 $data['row'] = Array(
                   'cd_atividade_cronograma_grupo' => 0,
				   'ds_atividade_cronograma_grupo' => ''
                 );
            }
            else
            {
                $this->atividade_cronograma_grupo_model->carrega($result, $args);
                $data['row'] = $result->row_array();
            }

            $this->load->view('atividade/atividade_cronograma_grupo/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function salvar()
    {
        if (gerencia_in(array('GAP')))
        {
            $args = array();
			$data = array();
			$result = null;
			
            $args['cd_atividade_cronograma_grupo'] = $this->input->post("cd_atividade_cronograma_grupo", TRUE);
			$args['ds_atividade_cronograma_grupo'] = $this->input->post("ds_atividade_cronograma_grupo", TRUE);
			$args['cd_usuario']                    = $this->session->userdata('codigo');

            $this->atividade_cronograma_grupo_model->salvar($result, $args);

            redirect("atividade/atividade_cronograma_grupo/", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function excluir($cd_atividade_cronograma_grupo)
    {
        if (gerencia_in(array('GAP')))
        {
			$args = array();
			$data = array();
			$result = null;
			
			$args['cd_atividade_cronograma_grupo'] = $cd_atividade_cronograma_grupo;
			$args['cd_usuario']                    = $this->session->userdata('codigo');
			
			$this->atividade_cronograma_grupo_model->excluir($result, $args);

            redirect("atividade/atividade_cronograma_grupo/", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
}
?>