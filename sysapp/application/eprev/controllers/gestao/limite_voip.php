<?php
class limite_voip extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();
	
		if(($this->session->userdata('tipo') == 'G') OR ($this->session->userdata('codigo') == 170))
		{
			$this->load->model('asterisk/Limite_voip_model');

			$data['collection'] = array();
			$result = null;
			$args=array();

			$args["cd_divisao"] = $this->session->userdata('divisao');
			
			$this->Limite_voip_model->listar( $result, $args );
			
			$data['collection'] = $result->result_array();
			$this->load->view('gestao/limite_voip/index', $data);		
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
    function atualizar()
    {
		CheckLogin();
	
		if(($this->session->userdata('tipo') == 'G') OR ($this->session->userdata('codigo') == 170))
		{
			$this->load->model('asterisk/Limite_voip_model');
			$result = null;
			$args = Array();

			$args["cd_limite"]  = $this->input->post("cd_limite", TRUE);
			$args["qt_chamada"] = $this->input->post("qt_chamada", TRUE);
			$args["vl_chamada"] = $this->input->post("vl_chamada", TRUE);
			$args["hr_chamada"] = $this->input->post("hr_chamada", TRUE);
			$args["cd_usuario"] = $this->session->userdata('codigo');

			$retorno = $this->Limite_voip_model->atualizar($result, $args);
			
			echo $retorno;
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
}
