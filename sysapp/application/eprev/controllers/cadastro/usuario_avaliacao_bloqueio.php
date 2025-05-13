<?php
class usuario_avaliacao_bloqueio extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		if(CheckLogin())
		{
	        $this->load->view('cadastro/usuario_avaliacao_bloqueio/index.php');
		}
    }

    function listar()
    {
        if(CheckLogin())
        {
	        $this->load->model('projetos/Usuario_avaliacao_bloqueio_model');
	
	        $data['collection'] = array();
	        $result = null;
	
	        // --------------------------
			// filtros ...
	
			$args=array();
	
			
	
			manter_filtros($args);
	
			// --------------------------
			// listar ...
	
	        $this->Usuario_avaliacao_bloqueio_model->listar( $result, $args );
	
			$data['collection'] = $result->result_array();
	
	        if( $result )
	        {
	            $data['collection'] = $result->result_array();
	        }
	
	        // --------------------------
	
	        $this->load->view('cadastro/usuario_avaliacao_bloqueio/partial_result', $data);
        }
    }

	function detalhe($cd=0)
	{
		if(CheckLogin())
		{
			$this->load->model('projetos/Usuario_avaliacao_bloqueio_model');
			$row=$this->Usuario_avaliacao_bloqueio_model->carregar( $cd );
			if($row){ $data['row'] = $row; }
			$this->load->view('cadastro/usuario_avaliacao_bloqueio/detalhe', $data);
		}
	}

	function salvar()
	{
		if(CheckLogin())
		{
			$this->load->model('projetos/Usuario_avaliacao_bloqueio_model');
			
			$args['cd_usuario_avaliacao_bloqueio']=intval($this->input->post('cd_usuario_avaliacao_bloqueio', TRUE));
	
			$args["cd_usuario"] = $this->input->post("cd_usuario",TRUE);
$args["cd_usuario_avaliacao_bloqueio"] = $this->input->post("cd_usuario_avaliacao_bloqueio",TRUE);
$args["cd_usuario_inclusao"] = usuario_id();


			$msg=array();
			$retorno = $this->Usuario_avaliacao_bloqueio_model->salvar( $args,$msg );
			
			if($retorno)
			{
				redirect( "cadastro/usuario_avaliacao_bloqueio", "refresh" );			
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
			$this->load->model('projetos/Usuario_avaliacao_bloqueio_model');

			$this->Usuario_avaliacao_bloqueio_model->excluir( $id );

			redirect( 'cadastro/usuario_avaliacao_bloqueio', 'refresh' );
		}
	}
}
?>