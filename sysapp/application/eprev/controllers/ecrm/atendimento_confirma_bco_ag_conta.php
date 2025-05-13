<?php
class Atendimento_confirma_bco_ag_conta extends Controller
{
    function __construct()
    {
        parent::Controller();
		
        CheckLogin();
    }

    private function get_confirmacao()
    {
    	return array(
    		array('value' => 'S', 'text' => 'Sim'),
    		array('value' => 'N', 'text' => 'Não')
    	);
    }
	
    private function permissao()
    {
        if(gerencia_in(array('GP')))
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
        if($this->permissao())
		{		
            ini_set('max_execution_time', 0);
            
			$this->load->model('projetos/atendimento_confirma_bco_ag_conta_model');
            
			$data['confirmacao'] = $this->get_confirmacao();
			
			$data['atendente'] = $this->atendimento_confirma_bco_ag_conta_model->get_atendente();
			
			$data['cd_usuario'] = $this->session->userdata('codigo');
			
            $this->load->view('ecrm/atendimento_confirma_bco_ag_conta/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    public function listar()
    {
        $this->load->model('projetos/atendimento_confirma_bco_ag_conta_model');
            
        $data = array();
        $args = array();
        
        $args['dt_atendimento_ini'] = $this->input->post('dt_atendimento_ini', TRUE);
        $args['dt_atendimento_fim'] = $this->input->post('dt_atendimento_fim', TRUE);
        $args['fl_confirmado']      = $this->input->post('fl_confirmado', TRUE);
        $args['fl_atendente']      	= $this->input->post('fl_atendente', TRUE);
        
        manter_filtros($args);
        
        $data['collection'] = $this->atendimento_confirma_bco_ag_conta_model->listar($args);

        $this->load->view('ecrm/atendimento_confirma_bco_ag_conta/index_result',$data);
    }
    
    public function confirmar($cd_atendimento, $cd_atendimento_confirma_bco_ag_conta)
    {
        if($this->permissao())
		{
			$this->load->model('projetos/atendimento_confirma_bco_ag_conta_model');
			
			$this->atendimento_confirma_bco_ag_conta_model->confirmar($cd_atendimento_confirma_bco_ag_conta, $cd_atendimento, $this->session->userdata('codigo'));
			
            redirect('ecrm/atendimento_confirma_bco_ag_conta/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
	
	public function alterar_motivo()
	{
		$this->load->model('projetos/atendimento_confirma_bco_ag_conta_model');
		
		$cd_atendimento 	   				  = utf8_decode($this->input->post('cd_atendimento', TRUE));
		$cd_atendimento_confirma_bco_ag_conta = utf8_decode($this->input->post('cd_atendimento_confirma_bco_ag_conta', TRUE));
		$ds_observacao   	  				  = utf8_decode($this->input->post('ds_observacao', TRUE));
		$cd_usuario           				  = utf8_decode($this->session->userdata('codigo'));
		
		$this->atendimento_confirma_bco_ag_conta_model->alterar_motivo($cd_atendimento_confirma_bco_ag_conta, $cd_atendimento, $ds_observacao, $cd_usuario);
	
		redirect('ecrm/atendimento_confirma_bco_ag_conta/index', 'refresh');
	}
}