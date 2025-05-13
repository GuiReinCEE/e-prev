<?php

class usuario_horario extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
        
        $this->load->model('projetos/usuario_horario_model');
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GTI','GS')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    function index()
    {
        if($this->get_permissao())
        {
            $data = array();
            
            $this->load->view('servico/usuario_horario/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function listar()
    {
        if($this->get_permissao())
        {
			$args = array();
			$data = array();
			$result = null;

			$args['cd_usuario_gerencia'] = $this->input->post("cd_usuario_gerencia", TRUE);
			$args['cd_usuario']          = $this->input->post("cd_usuario", TRUE);
			
			manter_filtros($args);
			
			$this->usuario_horario_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('servico/usuario_horario/index_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }      
    }
	
    function cadastro($cd_usuario_horario = 0)
    {
        if($this->get_permissao())
        {
			$args = array();
			$data = array();
			$result = null;

			if(intval($cd_usuario_horario) == 0)
			{
				$data['row'] = array(
					'cd_usuario_horario' => intval($cd_usuario_horario),
					'divisao'            => '',
					'cd_usuario'         => '',
					'dt_liberar'         => '',
					'hr_ini'             => '07:30',
					'hr_fim'             => '20:00',
					'ds_obs'             => ''
				);
			}
			else
			{
				$data['row'] = $this->usuario_horario_model->carrega(intval($cd_usuario_horario));
			}
			
			$this->load->view('servico/usuario_horario/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }      
    }   

	function salvar()
	{
		if($this->get_permissao())
		{
			$args = array(
				'cd_usuario_horario' => $this->input->post('cd_usuario_horario', TRUE),
				'cd_usuario'         => $this->input->post('cd_usuario', TRUE),
				'dt_liberar'         => $this->input->post('dt_liberar', TRUE),
				'hr_ini'             => $this->input->post('hr_ini', TRUE),
				'hr_fim'             => $this->input->post('hr_fim', TRUE),
				'ds_obs'             => $this->input->post('ds_obs', TRUE),
				'cd_usuario_logado'  => $this->session->userdata('codigo')
			);
			
			$cd_usuario_horario = $this->usuario_horario_model->salvar($args);
			
			if(intval($cd_usuario_horario) == 0)
			{
				redirect('servico/usuario_horario/', 'refresh');
			}
			else
			{
				redirect('servico/usuario_horario/cadastro/'.$cd_usuario_horario, 'refresh');
			}
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }   		
	}
	
	function excluir($cd_usuario_horario = 0)
	{
		if($this->get_permissao())
		{
			$args = array(
				'cd_usuario_horario' => $cd_usuario_horario,
				'cd_usuario_logado'  => $this->session->userdata('codigo')
			);			
			
			$this->usuario_horario_model->excluir($args);
			
			redirect('servico/usuario_horario/', 'refresh');
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }   		
	}	
}
?>