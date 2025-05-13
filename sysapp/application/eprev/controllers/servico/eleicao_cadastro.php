<?php
class eleicao_cadastro extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		if(CheckLogin())
		{
	        $this->load->view('servico/eleicao_cadastro/index.php');
		}
    }

    function listar()
    {
        if(CheckLogin())
        {
	        $this->load->model('eleicoes/Cadastro_eleicoes_model');
	
	        $data['collection'] = array();
	        $result = null;
	
	        // --------------------------
			// filtros ...
	
			$args=array();
	
			$args["nome"] = $this->input->post("nome", TRUE);

	
			manter_filtros($args);
	
			// --------------------------
			// listar ...
	
	        $this->Cadastro_eleicoes_model->listar( $result, $args );
	
			$data['collection'] = $result->result_array();
	
	        if( $result )
	        {
	            $data['collection'] = $result->result_array();
	        }
	
	        // --------------------------
	
	        $this->load->view('servico/eleicao_cadastro/partial_result', $data);
        }
    }

	function detalhe($cd=0)
	{
		if(CheckLogin())
		{
			$this->load->model('eleicoes/Cadastro_eleicoes_model');
			$row=$this->Cadastro_eleicoes_model->carregar( $cd );
			if($row){ $data['row'] = $row; }
			$this->load->view('servico/eleicao_cadastro/detalhe', $data);
		}
	}

	function salvar()
	{
		if(CheckLogin())
		{
			$this->load->model('eleicoes/Cadastro_eleicoes_model');
			
			$args['']=intval($this->input->post('', TRUE));
	
			

			$msg=array();
			$retorno = $this->Cadastro_eleicoes_model->salvar( $args,$msg );
			
			if($retorno)
			{
				redirect( "servico/eleicao_cadastro", "refresh" );			
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
			$this->load->model('eleicoes/Cadastro_eleicoes_model');

			$this->Cadastro_eleicoes_model->excluir( $id );

			redirect( 'servico/eleicao_cadastro', 'refresh' );
		}
	}
}
?>