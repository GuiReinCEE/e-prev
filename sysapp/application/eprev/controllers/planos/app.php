<?php
class App extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissoa()
    {
    	if(gerencia_in(array('GCM', 'GTI')))
        {
    		return TRUE;
    	}
    	else
    	{
    		return FALSE;
    	}
    }

    public function index()
    {
    	if($this->get_permissoa())
    	{
    		$this->load->view('planos/app/index');
    	}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        } 
    }

    public function listar()
    {
    	$this->load->model('autoatendimento/app_model');

    	$args = array(
            'dt_ini'  => $this->input->post('dt_ini', TRUE),
            'dt_fim'  => $this->input->post('dt_fim', TRUE)
        );

        $data['collection'] = $this->app_model->listar($args);

        $this->load->view('planos/app/index_result', $data);
    }
}