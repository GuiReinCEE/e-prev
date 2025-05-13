<?php
class link_quebrado extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		if(CheckLogin())
		{
	        $this->load->view('servico/link_quebrado/index.php');
		}
    }

    function listar()
    {
        if(CheckLogin())
        {
	        $this->load->model('projetos/Log_link_model');
	
	        $data['collection'] = array();
	        $result = null;
	
	        // --------------------------
			// filtros ...
	
			$args=array();
	
			$args["dt_erro_inicio"] = $this->input->post("dt_erro_inicio", TRUE);
$args["dt_erro_fim"] = $this->input->post("dt_erro_fim", TRUE);

	
			manter_filtros($args);
	
			// --------------------------
			// listar ...
	
	        $this->Log_link_model->listar( $result, $args );
	
			$data['collection'] = $result->result_array();
	
	        if( $result )
	        {
	            $data['collection'] = $result->result_array();
	        }
	
	        // --------------------------
	
	        $this->load->view('servico/link_quebrado/partial_result', $data);
        }
    }

	function detalhe($cd=0)
	{
		if(CheckLogin())
		{
			$this->load->model('projetos/Log_link_model');
			$row=$this->Log_link_model->carregar( $cd );
			if($row){ $data['row'] = $row; }
			$this->load->view('servico/link_quebrado/detalhe', $data);
		}
	}

	function salvar()
	{
		if(CheckLogin())
		{
			$this->load->model('projetos/Log_link_model');
			
			$args['cd_log_link']=intval($this->input->post('cd_log_link', TRUE));
	
			$args["nr_ip"] = $this->input->post("nr_ip",TRUE);
$args["ds_link_pagina"] = $this->input->post("ds_link_pagina",TRUE);
$args["ds_link_quebrado"] = $this->input->post("ds_link_quebrado",TRUE);
$args["dt_erro"] = $this->input->post("dt_erro",TRUE);
$args["cd_log_link"] = $this->input->post("cd_log_link",TRUE);


			$msg=array();
			$retorno = $this->Log_link_model->salvar( $args,$msg );
			
			if($retorno)
			{
				redirect( "servico/link_quebrado", "refresh" );			
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
			$this->load->model('projetos/Log_link_model');

			$this->Log_link_model->excluir( $id );

			redirect( 'servico/link_quebrado', 'refresh' );
		}
	}
}
?>