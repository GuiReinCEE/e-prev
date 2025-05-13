<?php
class ri_torcida_precavida_imagem extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		if(CheckLogin())
		{
	        $this->load->view('ecrm/ri_torcida_precavida_imagem/index.php');
		}
    }

    function listar()
    {
        if(CheckLogin())
        {
	        $this->load->model('torcida/Precavida_imagem_model');
	
	        $data['collection'] = array();
	        $result = null;
	
	        // --------------------------
			// filtros ...
	
			$args=array();

			manter_filtros($args);

			// --------------------------
			// listar ...

	        $this->Precavida_imagem_model->listar( $result, $args );

			$data['collection'] = $result->result_array();

	        if( $result )
	        {
	            $data['collection'] = $result->result_array();
	        }

	        // --------------------------

	        $this->load->view('ecrm/ri_torcida_precavida_imagem/partial_result', $data);
        }
    }

	function detalhe($cd=0)
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Precavida_imagem_model');
			$row=$this->Precavida_imagem_model->carregar( $cd );
			if($row){ $data['row'] = $row; }
			$this->load->view('ecrm/ri_torcida_precavida_imagem/detalhe', $data);
		}
	}

	function salvar()
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Precavida_imagem_model');

			$args['cd_precavida_imagem']=intval($this->input->post('cd_precavida_imagem', TRUE));

			$args["imagem"] = $this->input->post("imagem", TRUE);
			$args["x1"] = $this->input->post("x1", TRUE);
			$args["y1"] = $this->input->post("y1", TRUE);
			$args["x2"] = $this->input->post("x2", TRUE);
			$args["y2"] = $this->input->post("y2", TRUE);
			$args["cd_usuario_inclusao"] = usuario_id();
			$args["cd_precavida_imagem"] = $this->input->post("cd_precavida_imagem", TRUE);

			$msg=array();
			$retorno = $this->Precavida_imagem_model->salvar( $args,$msg );

			if($retorno)
			{
				copy( "./up/torcida_precavida/".$args["imagem"], "./../torcida/precavida/".$args["imagem"] );

				redirect( "ecrm/ri_torcida_precavida_imagem/", "refresh" );			
			}
			else
			{
				$mensagens = implode('<br>', $msg);
				exibir_mensagem($msg[0]);
			}
		}
	}

	function excluir($id)
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Precavida_imagem_model');

			$this->Precavida_imagem_model->excluir( $id );

			redirect( 'ecrm/ri_torcida_precavida_imagem', 'refresh' );
		}
	}

	function liberar()
	{
		if(CheckLogin())
		{
			$this->load->model( 'torcida/Precavida_imagem_model', 'dbModel' );
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
			$this->load->model( 'torcida/Precavida_imagem_model', 'dbModel' );
			$cd=$this->input->post('cd');

			$msg=array();
			$b = $this->dbModel->bloquear( $cd, $msg );

			if($b){ echo 'true'; }
			else{ echo 'Algum problema ocorreu.'; }
		}
	}
}
?>