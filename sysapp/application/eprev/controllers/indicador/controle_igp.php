<?php
class controle_igp extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		$this->load->model('indicador/controle_igp_model');
    }
	
	function index()
    {
		if ($this->session->userdata("indic_12") == '*')
        {
			$this->load->view('indicador/controle_igp/index');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function listar()
    {		
		if ($this->session->userdata("indic_12") == '*')
        {
			$data = array();
			$result = null;
			$args = array();
			
			$args["ano"]          = $this->input->post("ano", TRUE);
			$args["fl_encerrado"] = $this->input->post("fl_encerrado", TRUE);
			
			manter_filtros($args);

			$this->controle_igp_model->listar( $result, $args );
			$data['collection'] = $result->result_array();

			$this->load->view('indicador/controle_igp/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function encerrar($cd_igp)
	{
		if ($this->session->userdata("indic_12") == '*')
        {
			$data = array();
			$result = null;
			$args = array();
			
			$args["cd_igp"]     = $cd_igp;
			$args['cd_usuario'] = $this->session->userdata("codigo");
			
			$this->controle_igp_model->encerrar( $result, $args );
			
			redirect( "indicador/controle_igp", "refresh" );
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
}

?>