<?php
class prevenir_formulario extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('prevenir/prevenir_formulario_model');
    }

    function index()
    {
        $this->load->view('ecrm/prevenir_formulario/index');
    }

    function listar()
    {
        $args = Array();
		$data = Array();
		$result = null;
		
		$args['dt_envio_ini'] = $this->input->post("dt_envio_ini", TRUE);   
		$args['dt_envio_fim'] = $this->input->post("dt_envio_fim", TRUE); 
		
		manter_filtros($args);

        $this->prevenir_formulario_model->listar( $result, $args );
		$data['collection'] = $result->result_array();

        $this->load->view('ecrm/prevenir_formulario/index_result', $data);
    }
	
	function relatorio($cd_pergunta = 1)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_pergunta'] = $cd_pergunta;
		$data['cd_pergunta'] = $cd_pergunta;
		
		$this->prevenir_formulario_model->formulario_item( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->load->view('ecrm/prevenir_formulario/relatorio', $data);
	}
	
	function formulario($cd_prevenir_formulario)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_prevenir_formulario'] = intval($cd_prevenir_formulario);
		
		$data['fl_editar'] = false;
		
		if(gerencia_in(array('GRI', 'AAA', 'DE')))
		{
			$data['fl_editar'] = true;
		}
		
		$this->prevenir_formulario_model->formulario($result, $args);
        $data['row'] = $result->row_array();
		
		$this->prevenir_formulario_model->previnir_formulario($result, $args);
        $data['collection'] = $result->result_array();

		$this->load->view('ecrm/prevenir_formulario/formulario', $data);
	}
	
	function muda_exibicao()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_prevenir_formulario_item'] = $this->input->post("cd_prevenir_formulario_item", TRUE);   
		$args['fl_exibir']                   = $this->input->post("fl_exibir", TRUE); 
		
		$this->prevenir_formulario_model->muda_exibicao($result, $args);
	}


}
?>