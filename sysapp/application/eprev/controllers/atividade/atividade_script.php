<?php
class Atividade_script extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();		
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GTI')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function index($cd_atividade, $cd_gerencia)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/atividade_script_model');

            $data = array(
                'cd_atividade' => $cd_atividade,
                'cd_gerencia'  => $cd_gerencia,
                'cd_usuario'   => $this->session->userdata('codigo'),
                'collection'   => $this->atividade_script_model->listar(intval($cd_atividade))
            );

            $this->load->view('atividade/atividade_script/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
        $this->load->model('projetos/atividade_script_model');

        $cd_atividade = $this->input->post('cd_atividade', TRUE);
        $cd_gerencia  = $this->input->post('cd_gerencia', TRUE);

        $args = array(
            'ds_atividade_script' => $this->input->post('ds_atividade_script', TRUE),
            'arquivo'             => $this->input->post('arquivo', TRUE),
            'arquivo_nome'        => $this->input->post('arquivo_nome', TRUE),
            'cd_usuario'          => $this->session->userdata('codigo')
        );

        $this->atividade_script_model->salvar(intval($cd_atividade), $args);

        redirect('atividade/atividade_script/index/'.$cd_atividade.'/'.$cd_gerencia, 'refresh');
    }

    public function excluir($cd_atividade, $cd_gerencia, $cd_atividade_script)
	{
        $this->load->model('projetos/atividade_script_model');
		
        $this->atividade_script_model->excluir($cd_atividade_script, $this->session->userdata('codigo'));
	
		redirect('atividade/atividade_script/index/'.$cd_atividade.'/'.$cd_gerencia, 'refresh');
	}
}
?>