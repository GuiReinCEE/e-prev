<?php
class multimidia extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();
		if(gerencia_in(array('GRI')))
		{
			$this->load->view('ecrm/multimidia/index.php');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}				
    }

    function listar_video()
    {
        CheckLogin();
		if(gerencia_in(array('GRI')))
		{			
			$this->load->model('acs/Videos_model');

			$data['collection'] = array();
			$result = null;

			// --------------------------
			// filtros ...

			$args=array();
			$args['ano'] = $this->input->post('ano', true);

			manter_filtros($args);

			// --------------------------
			// listar ...

			$this->Videos_model->listar( $result, $args );

			$data['collection'] = $result->result_array();

			if( $result )
			{
				$data['collection'] = $result->result_array();
			}

			// --------------------------

			$this->load->view('ecrm/multimidia/partial_result_video', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
    }
	
    function video_cadastro($cd_video = 0)
    {
		CheckLogin();
		if(gerencia_in(array('GRI')))
		{			
			$this->load->model('acs/Videos_model');
			
			$data = Array();
			$data['cd_video'] = intval($cd_video);
			$data['collection'] = Array();
			
			$args=array();	
			$args['cd_video'] = intval($cd_video);
			
			if(intval($cd_video) == 0)
			{
				$data['row'] = Array('cd_video'=>0,
									 'dt_evento' => '',
									 'titulo' => '',
									 'ds_local' => '',
									 'diretorio' => '',
									 'arquivo' => '',
									 'arquivo_original' => ''
									);
			}
			else
			{
				$this->Videos_model->getVideo($result, $args);
				$data['row'] = $result->row_array();	
			}
			$this->load->view('ecrm/multimidia/video_cadastro', $data);		
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
    function videoSalvar()
    {
		CheckLogin();
		if(gerencia_in(array('GRI')))
		{		
			$this->load->model('acs/Videos_model');

			$data['row'] = array();
			$result = null;
			$args = Array();

			$args["cd_video"]         = $this->input->post("cd_video", TRUE);
			$args["dt_evento"]        = $this->input->post("dt_evento", TRUE);
			$args["titulo"]           = $this->input->post("titulo", TRUE);
			$args["ds_local"]         = $this->input->post("ds_local", TRUE);
			$args["diretorio"]        = $this->input->post("diretorio", TRUE);
			$args["arquivo"]          = $this->input->post("arquivo", TRUE);
			$args["arquivo_original"] = $this->input->post("arquivo_original", TRUE);
			$args["cd_usuario"]       = $this->session->userdata('codigo');
		
			$retorno = $this->Videos_model->videoSalvar( $result, $args );
			
			redirect("ecrm/multimidia/video_cadastro/".$retorno, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
    }	

    function foto()
    {
		CheckLogin();
		if(gerencia_in(array('GRI')))
		{	
			$this->load->view('ecrm/multimidia/foto');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function listar_foto()
    {
        CheckLogin();
		if(gerencia_in(array('GRI')))
		{		
			$this->load->model('acs/Fotos_model');

			$data['collection'] = array();
			$result = null;

			// --------------------------
			// filtros ...

			$args=array();
			$args['ano'] = $this->input->post('ano', true);

			manter_filtros($args);

			// --------------------------
			// listar ...

			$this->Fotos_model->listar( $result, $args );

			$data['collection'] = $result->result_array();

			if( $result )
			{
				$data['collection'] = $result->result_array();
			}

			// --------------------------

			$this->load->view('ecrm/multimidia/partial_result_foto', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
	
    function foto_cadastro($cd_fotos = 0)
    {
		CheckLogin();
		if(gerencia_in(array('GRI')))
		{		
			$this->load->model('acs/Fotos_model');
			
			$data = Array();
			$data['cd_fotos'] = intval($cd_fotos);
			$data['collection'] = Array();
			
			$args=array();	
			$args['cd_fotos'] = intval($cd_fotos);
			
			if(intval($cd_fotos) == 0)
			{
				$data['row'] = Array('cd_fotos'=>0,
									 'ds_titulo' => '',
									 'ds_caminho' => '',
									 'dt_data' => ''
									);
			}
			else
			{
				$this->Fotos_model->getFoto($result, $args);
				$data['row'] = $result->row_array();	
			}
			$this->load->view('ecrm/multimidia/foto_cadastro', $data);	
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
    }	
	
    function fotoSalvar()
    {
		CheckLogin();
		if(gerencia_in(array('GRI')))
		{		
			$this->load->model('acs/Fotos_model');

			$data['row'] = array();
			$result = null;
			$args = Array();

			$args["cd_fotos"]   = $this->input->post("cd_fotos", TRUE);
			$args["dt_data"]    = $this->input->post("dt_data", TRUE);
			$args["ds_titulo"]  = $this->input->post("ds_titulo", TRUE);
			$args["ds_caminho"] = $this->input->post("ds_caminho", FALSE);
			$args["cd_usuario"] = $this->session->userdata('codigo');
		
			$retorno = $this->Fotos_model->fotoSalvar( $result, $args );
			
			redirect("ecrm/multimidia/foto_cadastro/".$retorno, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
    }	
}
?>