<?php
class Formulario_fatca extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    public function index($cd_empresa = '', $cd_registro_empregado = '', $seq_dependencia = '')
    {
		if(gerencia_in(array('GCM')))
    	{
			$data = array();
			
			$data = array(
				'cd_empresa' 			=> $cd_empresa, 
				'cd_registro_empregado' => $cd_registro_empregado, 
				'seq_dependencia' 		=> $seq_dependencia
			);
    	
			$this->load->view('ecrm/formulario_fatca/index', $data);
    	}
    	else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		$this->load->model('autoatendimento/formulario_fatca_model');

		$args = array();
		$data = array();
		
		$args = array(
			'cd_empresa' 			=> $this->input->post('cd_empresa'),
			'cd_registro_empregado' => $this->input->post('cd_registro_empregado'),
			'seq_dependencia' 		=> $this->input->post('seq_dependencia'),
			'dt_ini' 				=> $this->input->post('dt_ini'),
			'dt_fim' 				=> $this->input->post('dt_fim')
		);
		
		manter_filtros($args);
		
		$data['collection'] = $this->formulario_fatca_model->listar($args);
		
		$this->load->view('ecrm/formulario_fatca/index_result', $data);
    }
}
?>