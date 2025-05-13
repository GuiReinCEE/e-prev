<?php
class ri_torcida_tv_comentario extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		if(CheckLogin())
		{
			$q=$this->db->query("SELECT titulo as text, cd_tv as value FROM torcida.tv WHERE dt_exclusao IS NULL");
			$data['tv_dd']=$q->result_array();
	        $this->load->view('ecrm/ri_torcida_tv_comentario/index.php',$data);
		}
    }

    function listar()
    {
        if(CheckLogin())
        {
	        $this->load->model('torcida/Tv_comentario_model');
	
	        $data['collection'] = array();
	        $result = null;
	
	        // --------------------------
			// filtros ...
	
			$args=array();
	
			$args["cd_tv"] = intval($this->input->post("cd_tv", TRUE));

	
			manter_filtros($args);
	
			// --------------------------
			// listar ...
	
	        $this->Tv_comentario_model->listar( $result, $args );
	
			$data['collection'] = $result->result_array();
	
	        if( $result )
	        {
	            $data['collection'] = $result->result_array();
	        }
	
	        // --------------------------
	
	        $this->load->view('ecrm/ri_torcida_tv_comentario/partial_result', $data);
        }
    }

	function detalhe($cd=0)
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Tv_comentario_model');
			$row=$this->Tv_comentario_model->carregar( $cd );
			if($row){ $data['row'] = $row; }
			$this->load->view('ecrm/ri_torcida_tv_comentario/detalhe', $data);
		}
	}

	function salvar()
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Tv_comentario_model');

			$args['cd_tv_comentario']=intval($this->input->post('cd_tv_comentario', TRUE));
			$args["nome"] = $this->input->post("nome",true);
			$args["comentario"] = $this->input->post("comentario",true);
			$args["cd_usuario_inclusao"] = usuario_id();

			$msg=array();
			$retorno = $this->Tv_comentario_model->salvar( $args,$msg );

			if($retorno)
			{
				redirect( "ecrm/ri_torcida_tv_comentario/", "refresh" );			
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
			$this->load->model('torcida/Tv_comentario_model');

			$this->Tv_comentario_model->excluir( $id );

			redirect( 'ecrm/ri_torcida_tv_comentario', 'refresh' );
		}
	}

	function liberar()
	{
		if(CheckLogin())
		{
			$this->load->model( 'torcida/Tv_comentario_model', 'dbModel' );
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
			$this->load->model( 'torcida/Tv_comentario_model', 'dbModel' );
			$cd=$this->input->post('cd');
			
			$msg=array();
			$b = $this->dbModel->bloquear( $cd, $msg );
			
			if($b){ echo 'true'; }
			else{ echo 'Algum problema ocorreu.'; }
		}
	}
}
?>