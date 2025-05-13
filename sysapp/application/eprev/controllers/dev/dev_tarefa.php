<?php
class dev_tarefa extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		if(CheckLogin())
		{
	        $this->load->view('dev/dev_tarefa/index.php');
		}
    }

    function listar()
    {
        if(CheckLogin())
        {
	        $this->load->model('dev/Dev_tarefa_model');
	
	        $data['collection'] = array();
	        $result = null;
	
	        // --------------------------
			// filtros ...
	
			$args=array();
	
			
	
			manter_filtros($args);
	
			// --------------------------
			// listar ...
	
	        $this->Dev_tarefa_model->listar( $result, $args );
	
			$data['collection'] = $result->result_array();
	
	        if( $result )
	        {
	            $data['collection'] = $result->result_array();
	        }
	
	        // --------------------------
	
	        $this->load->view('dev/dev_tarefa/partial_result', $data);
        }
    }

	function detalhe($cd=0)
	{
		if(CheckLogin())
		{
			$this->load->model('dev/Dev_tarefa_model');
			$row=$this->Dev_tarefa_model->carregar( $cd );
			if($row){ $data['row'] = $row; }
			$this->load->view('dev/dev_tarefa/detalhe', $data);
		}
	}

	function salvar()
	{
		if(CheckLogin())
		{
			$this->load->model('dev/Dev_tarefa_model');
			
			$args['']=intval($this->input->post('', TRUE));
	
			

			$msg=array();
			$retorno = $this->Dev_tarefa_model->salvar( $args,$msg );
			
			if($retorno)
			{
				redirect( "dev/dev_tarefa", "refresh" );			
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
			$this->load->model('dev/Dev_tarefa_model');

			$this->Dev_tarefa_model->excluir( $id );

			redirect( 'dev/dev_tarefa', 'refresh' );
		}
	}
}
?>