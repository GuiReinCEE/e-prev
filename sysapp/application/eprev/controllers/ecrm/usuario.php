<?php

class usuario extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
        $this->load->model('extranet/usuario_model');
    }
	
	public function index()
    {
		if (gerencia_in(array('GAP')))
        {
			$args = array();
			$data = array();
			$result = null;

			$this->load->view('ecrm/usuario/index', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	public function listar()
    {
		if (gerencia_in(array('GAP')))
        {
			$args = array();
			$data = array();
			$result = null;
		
			$args['cd_empresa'] = $this->input->post("cd_empresa", TRUE); 
				
			manter_filtros($args);

			$this->usuario_model->listar($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('ecrm/usuario/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function cadastro($cd_usuario = 0)
    {
        if (gerencia_in(array('GAP')))
        {
            $args = array();
			$data = array();
			$result = null;

            $args['cd_usuario'] = intval($cd_usuario);
			
            if ($args['cd_usuario'] == 0)
            {			
                 $data['row'] = Array(
                   'cd_usuario'     => 0,
				   'nome'           => '',
				   'usuario'        => '',
				   'senha'          => '',
				   'fl_troca_senha' => '',
				   'cd_empresa'     => '',
				   'cpf'            => '',
				   'email'          => '',
				   'telefone_1'     => '',
				   'telefone_2'     => ''
				   
                 );
            }
            else
            {
                $this->usuario_model->carrega($result, $args);
                $data['row'] = $result->row_array();
            }

            $this->load->view('ecrm/usuario/cadastro', $data);
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
			
            $args['cd_usuario']     = $this->input->post("cd_usuario", TRUE);
			$args['nome']           = $this->input->post("nome", TRUE);
			$args['usuario']        = $this->input->post("usuario", TRUE);
			$args['senha']          = $this->input->post("senha", TRUE);
			$args['senha_old']      = $this->input->post("senha_old", TRUE);
			$args['fl_troca_senha'] = $this->input->post("fl_troca_senha", TRUE);
			$args['cd_empresa']     = $this->input->post("cd_empresa", TRUE);
			$args['cpf']            = $this->input->post("cpf", TRUE);
			$args['email']          = $this->input->post("email", TRUE);
			$args['telefone_1']     = $this->input->post("telefone_1", TRUE);
			$args['telefone_2']     = $this->input->post("telefone_2", TRUE);

            $this->usuario_model->salvar($result, $args);

            redirect("ecrm/usuario/", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function excluir($cd_usuario)
    {
        if (gerencia_in(array('GAP')))
        {
			$args = array();
			$data = array();
			$result = null;
			
			$args['cd_usuario'] = $cd_usuario;
			
			$this->usuario_model->excluir($result, $args);

            redirect("ecrm/usuario/", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
}

?>