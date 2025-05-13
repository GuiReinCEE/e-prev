<?php
class biblioteca_multimidia extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();
		$data = Array();
		$this->load->view('intranet/biblioteca_multimidia/index.php',$data);
    }	
	
    function videoListar()
    {
        CheckLogin();
        $this->load->model('acs/Videos_model');

        $data['collection'] = Array();
        $result = null;
		$args   = Array();

		$args["ano"] = $this->input->post('ano', TRUE);

        $this->Videos_model->listar( $result, $args );
		$data['collection'] = $result->result_array();
        $this->load->view('intranet/biblioteca_multimidia/index_result', $data);
    }	
	
    function videoPlayer($cd_video = 0)
    {
        CheckLogin();
        $this->load->model('acs/Videos_model');

        $data['collection'] = Array();
        $result = null;
		$args   = Array();

		$args["cd_video"] = $cd_video;

        $this->Videos_model->getVideo( $result, $args );
		$data['row'] = $result->row_array();
        $this->load->view('intranet/biblioteca_multimidia/video_player', $data);
    }

	
    function foto()
    {
		CheckLogin();
		$data = Array();
		$this->load->view('intranet/biblioteca_multimidia/foto.php',$data);
    }	
	
    function fotoListar()
    {
        CheckLogin();
        $this->load->model('acs/Fotos_model');

        $data['collection'] = Array();
        $result = null;
		$args   = Array();

		$args["ano"] = $this->input->post('ano', TRUE);

        $this->Fotos_model->listar( $result, $args );
		$data['collection'] = $result->result_array();
        $this->load->view('intranet/biblioteca_multimidia/foto_result', $data);
    }	

    function foto_ver($cd_fotos = 0)
    {
        CheckLogin();
        $this->load->model('acs/Fotos_model');

        $data['collection'] = Array();
        $result = null;
		$args   = Array();

		$args["cd_fotos"] = $cd_fotos;

        $this->Fotos_model->getFoto( $result, $args );
		$data['row'] = $result->row_array();
        $this->load->view('intranet/biblioteca_multimidia/foto_ver', $data);
    }	
		
}
