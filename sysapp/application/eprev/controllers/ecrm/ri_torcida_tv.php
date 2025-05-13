<?php
class ri_torcida_tv extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		if(CheckLogin())
		{
	        $this->load->view('ecrm/ri_torcida_tv/index.php');
		}
    }

    function listar()
    {
        if(CheckLogin())
        {
	        $this->load->model('torcida/Tv_model');
	
	        $data['collection'] = array();
	        $result = null;
	
	        // --------------------------
			// filtros ...
	
			$args=array();
	
			
	
			manter_filtros($args);
	
			// --------------------------
			// listar ...
	
	        $this->Tv_model->listar( $result, $args );
	
			$data['collection'] = $result->result_array();
	
	        if( $result )
	        {
	            $data['collection'] = $result->result_array();
	        }
	
	        // --------------------------
	
	        $this->load->view('ecrm/ri_torcida_tv/partial_result', $data);
        }
    }

	function detalhe($cd=0)
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Tv_model');
			$row=$this->Tv_model->carregar( $cd );
			if($row){ $data['row'] = $row; }
			$this->load->view('ecrm/ri_torcida_tv/detalhe', $data);
		}
	}

	function salvar()
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Tv_model');
			
			$args['cd_tv']=intval($this->input->post('cd_tv', TRUE));
	
			$args["titulo"] = $this->input->post("titulo",TRUE);
			$args["resumo"] = $this->input->post("resumo",TRUE);
			$args["caminho"] = $this->input->post("caminho",TRUE);
			$args["icone"] = $this->input->post("icone",TRUE);
			$args["cd_tv"] = $this->input->post("cd_tv",TRUE);
			$args["cd_usuario_inclusao"] = usuario_id();

			$msg=array();
			$retorno = $this->Tv_model->salvar( $args,$msg );
			
			if($retorno)
			{
				redirect( "ecrm/ri_torcida_tv", "refresh" );			
			}
			else
			{
				$mensagens = implode('<br>',$msg);
				exibir_mensagem($msg[0]);
			}

		}
	}

	function excluir($id)
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Tv_model');

			$this->Tv_model->excluir( $id );

			redirect( 'ecrm/ri_torcida_tv', 'refresh' );
		}
	}

	function liberar()
	{
		if(CheckLogin())
		{
			$this->load->model( 'torcida/Tv_model', 'dbModel' );
			$cd=$this->input->post('cd');
			
			$msg=array();
			$b = $this->dbModel->liberar( $cd, usuario_id(), $msg );
			
			if($b){ echo 'true'; }
			else{ echo 'Algum problema ocorreu.'; }
		}
	}

	function bloquear()
	{
		if(CheckLogin())
		{
			$this->load->model( 'torcida/Tv_model', 'dbModel' );
			$cd=$this->input->post('cd');
			
			$msg=array();
			$b = $this->dbModel->bloquear( $cd, $msg );
			
			if($b){ echo 'true'; }
			else{ echo 'Algum problema ocorreu.'; }
		}
	}
}
