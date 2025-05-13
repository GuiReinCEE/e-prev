<?php
class Area_corporativa_senge extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GCM')))
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
        if($this->get_permissao())
        {
            $this->load->model('senge_previdencia/senge_log_model');

            $data['usuario'] = $this->senge_log_model->get_usuarios();

            $this->load->view('planos/area_corporativa_senge/index', $data); 
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }  
    }

    public function listar()
    {
    	$this->load->model('senge_previdencia/senge_log_model');	

    	$args = array(
    		'cd_usuario'      => $this->input->post('cd_usuario', TRUE),
    		'dt_inclusao_ini' => $this->input->post('dt_inclusao_ini', TRUE),
    		'dt_inclusao_fim' => $this->input->post('dt_inclusao_fim', 	TRUE)
    	);

    	manter_filtros($args);

        $data['collection'] = $this->senge_log_model->listar($args);

        $this->load->view('planos/area_corporativa_senge/index_result', $data);        
	}
}