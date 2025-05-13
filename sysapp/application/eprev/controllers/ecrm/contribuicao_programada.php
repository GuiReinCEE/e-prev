<?php
class Contribuicao_programada extends Controller
{
    function __construct()
    {
        parent::Controller();
		
        CheckLogin();
    }

    private function get_permissao()
    {
    	#Mauro Oliveira Pyhus
        if($this->session->userdata('codigo') == 73)
        {
            return TRUE;
        }
        #Vanessa dos Santos Dornelles
        else if($this->session->userdata('codigo') == 146)
        {
            return TRUE;
        }
        #Shaiane de Oliveira Tavares SantAnna
        else if($this->session->userdata('codigo') == 228)
        {
            return TRUE;
        }
        #Luciano Rodriguez
        else if($this->session->userdata('codigo') == 251)
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
        	$this->load->view('ecrm/contribuicao_programada/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
    	$this->load->model('projetos/contribuicao_programada_model');	

		$args = array();

		$args['dt_solicitacao_ini']    = $this->input->post('dt_solicitacao_ini', TRUE);
		$args['dt_solicitacao_fim']    = $this->input->post('dt_solicitacao_fim', TRUE);
		$args['dt_inicio_ini']         = $this->input->post('dt_inicio_ini', TRUE);
		$args['dt_inicio_fim']         = $this->input->post('dt_inicio_fim', TRUE);
		$args['nome']                  = $this->input->post('nome', TRUE);
		$args['cd_empresa']            = $this->input->post('cd_empresa', TRUE);
		$args['cd_registro_empregado'] = $this->input->post('cd_registro_empregado', TRUE);
		$args['seq_dependencia']       = $this->input->post('seq_dependencia', TRUE);
		$args['fl_cancelado']		   = $this->input->post('fl_cancelado', TRUE);
		
		manter_filtros($args);

		$data['collection'] = $this->contribuicao_programada_model->listar($args);
		
        $this->load->view('ecrm/contribuicao_programada/index_result', $data);
    }
}
?>