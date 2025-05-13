<?php
class ri_torcida_precavida_texto extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		if(CheckLogin())
		{
	        $this->load->view('ecrm/ri_torcida_precavida_texto/index.php');
		}
    }

    function listar()
    {
        if(CheckLogin())
        {
	        $this->load->model('torcida/Precavida_texto_model');
	
	        $data['collection'] = array();
	        $result = null;
	
	        // --------------------------
			// filtros ...
	
			$args=array();
	
			
	
			manter_filtros($args);
	
			// --------------------------
			// listar ...
	
	        $this->Precavida_texto_model->listar( $result, $args );
	
			$data['collection'] = $result->result_array();
	
	        if( $result )
	        {
	            $data['collection'] = $result->result_array();
	        }
	
	        // --------------------------
	
	        $this->load->view('ecrm/ri_torcida_precavida_texto/partial_result', $data);
        }
    }

	function detalhe($cd=0)
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Precavida_texto_model');
			$row=$this->Precavida_texto_model->carregar( $cd );
			if($row){ $data['row'] = $row; }
			$this->load->view('ecrm/ri_torcida_precavida_texto/detalhe', $data);
		}
	}

	function salvar()
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Precavida_texto_model');
			
			$args['cd_precavida_texto']=intval($this->input->post('cd_precavida_texto', TRUE));
	
			$args["texto"] = $this->input->post("texto",TRUE);
			$args["cd_precavida_texto"] = $this->input->post("cd_precavida_texto",TRUE);
			$args["cd_usuario_inclusao"] = usuario_id();

			$msg=array();
			$retorno = $this->Precavida_texto_model->salvar( $args, $msg );
			
			if($retorno)
			{
				redirect( "ecrm/ri_torcida_precavida_texto", "refresh" );			
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
			$this->load->model('torcida/Precavida_texto_model');

			$this->Precavida_texto_model->excluir( $id );

			redirect( 'ecrm/ri_torcida_precavida_texto', 'refresh' );
		}
	}

	function liberar()
	{
		if(CheckLogin())
		{
			$this->load->model( 'torcida/Precavida_texto_model', 'dbModel' );
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
			$this->load->model( 'torcida/Precavida_texto_model', 'dbModel' );
			$cd=$this->input->post('cd');
			
			$msg=array();
			$b = $this->dbModel->bloquear( $cd, $msg );
			
			if($b){ echo 'true'; }
			else{ echo 'Algum problema ocorreu.'; }
		}
	}
}
?>