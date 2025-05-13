<?php
class periodo extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		if(CheckLogin())
		{
	        $this->load->view('indicador/periodo/index.php');
		}
    }

    function listar()
    {
        if(CheckLogin())
        {
	        $this->load->model('projetos/Indicador_periodo_model');
	
	        $data['collection'] = array();
	        $result = null;
	
	        // --------------------------
			// filtros ...
	
			$args=array();
	
			
	
			manter_filtros($args);
	
			// --------------------------
			// listar ...
	
	        $this->Indicador_periodo_model->listar( $result, $args );
	
			$data['collection'] = $result->result_array();
	
	        if( $result )
	        {
	            $data['collection'] = $result->result_array();
	        }
	
	        // --------------------------
	
	        $this->load->view('indicador/periodo/partial_result', $data);
        }
    }

	function detalhe($cd=0)
	{
		if(CheckLogin())
		{
			$this->load->model('projetos/Indicador_periodo_model');
			$row=$this->Indicador_periodo_model->carregar( $cd );
			if($row){ $data['row'] = $row; }
			$this->load->view('indicador/periodo/detalhe', $data);
		}
	}

	function salvar()
	{
		if(CheckLogin())
		{
			$this->load->model('projetos/Indicador_periodo_model');
			
			$args['cd_indicador_periodo']=intval($this->input->post('cd_indicador_periodo', TRUE));
	
			$args["ds_periodo"] = $this->input->post("ds_periodo",TRUE);
$args["dt_inicio"] = $this->input->post("dt_inicio",TRUE);
$args["dt_fim"] = $this->input->post("dt_fim",TRUE);
$args["cd_indicador_periodo"] = $this->input->post("cd_indicador_periodo",TRUE);


			$msg=array();
			$retorno = $this->Indicador_periodo_model->salvar( $args,$msg );
			
			if($retorno)
			{
				redirect( "indicador/periodo", "refresh" );			
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
			$this->load->model('projetos/Indicador_periodo_model');

			$this->Indicador_periodo_model->excluir( $id );

			redirect( 'indicador/periodo', 'refresh' );
		}
	}
}
?>