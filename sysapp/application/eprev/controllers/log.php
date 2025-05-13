<?php
class Log extends Controller
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

    private function get_status()
    {
    	return array(
    		array('value' => 'd', 'text' => 'Abortado'),
    		array('value' => 'f', 'text' => 'Falha na Execuчуo'),
    		array('value' => 'i', 'text' => 'Erro Ignorado'),
    		array('value' => 'r', 'text' => 'Em execuчуo'),
    		array('value' => 's', 'text' => 'Finalizado com sucesso')
    	);
    }
	
	public function index($fl_status = 'f')
	{
		if($this->get_permissao())
        {	
			$data = array(
				'status'    => $this->get_status(),
				'fl_status' => $fl_status
			);
					
			$this->load->view('log/index', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
	}
	
	public function listar()
	{
		$this->load->model('projetos/job_log_model');
		
		$args = array(
			'fl_status' => $this->input->post('fl_status'),
			'dt_ini'    => $this->input->post('dt_ini'),
			'dt_fim'    => $this->input->post('dt_fim')
		);

		$data['collection'] = $this->job_log_model->listar($args);

		$this->load->view('log/index_result', $data);
	}

	public function ver_log($cd_job_log)
	{
		$this->load->model('projetos/job_log_model');

		$data['row'] = $this->job_log_model->carrega($cd_job_log);

		$this->load->view('log/ver_log', $data);
	}
}
?>