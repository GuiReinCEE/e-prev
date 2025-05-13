<?php
class operacional_enquete_grupo extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/enquete_grupo_model');
    }

    function index()
    {
        $this->load->view('ecrm/operacional_enquete_grupo/index');
    }

    function listar()
    {
        $result = null;
		$args   = Array();
		$data   = Array();

		manter_filtros($args);

        $this->enquete_grupo_model->listar( $result, $args );
		$data['collection'] = $result->result_array();

        $this->load->view('ecrm/operacional_enquete_grupo/index_result', $data);
    }
	
	function cadastro($cd_enquete_grupo = 0)
	{
		$result = null;
		$args   = Array();
		$data   = Array();
			
		$args['cd_enquete_grupo'] = intval($cd_enquete_grupo);
		
		if(intval($args['cd_enquete_grupo']) == 0)
		{
			$data['row'] = array(
				'cd_enquete_grupo' => $args['cd_enquete_grupo'],
				'ds_titulo'        => '',
				'ds_pergunta'      => '',
				'cd_enquete_sim'   => '',
				'cd_enquete_nao'   => ''
			);
		}
		else
		{
			$this->enquete_grupo_model->carrega($result, $args);
			$data['row'] = $result->row_array();
		}
		
		$this->load->view('ecrm/operacional_enquete_grupo/cadastro', $data);
	}
	
	function salvar()
	{
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$args['cd_enquete_grupo'] = $this->input->post("cd_enquete_grupo", TRUE);
		$args['ds_titulo']        = $this->input->post("ds_titulo", TRUE);
		$args['ds_pergunta']      = $this->input->post("ds_pergunta", TRUE);
		$args['seq_dependencia']  = $this->input->post("seq_dependencia", TRUE);
		$args['cd_enquete_sim']   = $this->input->post("cd_enquete_sim", TRUE);
		$args['cd_enquete_nao']   = $this->input->post("cd_enquete_nao", TRUE);
		$args['cd_usuario']       = $this->session->userdata("codigo");
		
		$cd_enquete_grupo = $this->enquete_grupo_model->salvar($result, $args);
		
		redirect('ecrm/operacional_enquete_grupo/cadastro/'.intval($cd_enquete_grupo), 'refresh');
	}
}
?>