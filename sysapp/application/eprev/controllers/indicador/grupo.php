<?php
class grupo extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		if(CheckLogin())
		{
			if( usuario_administrador_indicador(usuario_id()) )
			{
		        $this->load->view('indicador/grupo/index.php');
			}
			else
			{
				exibir_mensagem('Você não possui permissão para exibir essa página.');
			}		
		}
    }

    function listar()
    {
        if(CheckLogin())
        {
        	if( ! usuario_administrador_indicador(usuario_id()) )
			{
				exibir_mensagem('Você não possui permissão para exibir essa página.');
			}
			else
			{
		        $this->load->model('projetos/Indicador_grupo_model');
		
		        $data['collection'] = array();
		        $result = null;
		
		        // --------------------------
				// filtros ...
		
				$args=array();
		
				
		
				manter_filtros($args);
		
				// --------------------------
				// listar ...
		
		        $this->Indicador_grupo_model->listar( $result, $args );
		
				$data['collection'] = $result->result_array();
		
		        if( $result )
		        {
		            $data['collection'] = $result->result_array();
		        }
		
		        // --------------------------
		
		        $this->load->view('indicador/grupo/partial_result', $data);
			}
        }
    }

	function detalhe($cd=0)
	{
		if(CheckLogin())
		{
			if( ! usuario_administrador_indicador(usuario_id()) )
			{
				exibir_mensagem('Você não possui permissão para exibir essa página.');
			}
			else
			{
				$this->load->model('projetos/Indicador_grupo_model');
				$row=$this->Indicador_grupo_model->carregar( $cd );
				if($row){ $data['row'] = $row; }
				$this->load->view('indicador/grupo/detalhe', $data);
			}
		}
	}

	function salvar()
	{
		if(CheckLogin())
		{
			if( ! usuario_administrador_indicador(usuario_id()) )
			{
				exibir_mensagem('Você não possui permissão para exibir essa página.');
			}
			else
			{
				$this->load->model('projetos/Indicador_grupo_model');
				
				$args['cd_indicador_grupo']=intval($this->input->post('cd_indicador_grupo', TRUE));
		
				$args["ds_indicador_grupo"] = $this->input->post("ds_indicador_grupo",TRUE);
				$args["ds_missao"] = $this->input->post("ds_missao",TRUE);
				$args["cd_indicador_grupo"] = $this->input->post("cd_indicador_grupo",TRUE);
	
				$msg=array();
				$retorno = $this->Indicador_grupo_model->salvar( $args,$msg );
				
				if($retorno)
				{
					redirect( "indicador/grupo", "refresh" );			
				}
				else
				{
					$mensagens = implode('<br>',$msg);
					exibir_mensagem($msg[0]);
				}
			}
		}
	}

	function excluir($id)
	{
		if(CheckLogin())
		{
			if( ! usuario_administrador_indicador(usuario_id()) )
			{
				exibir_mensagem('Você não possui permissão para exibir essa página.');
			}
			else
			{
				$this->load->model('projetos/Indicador_grupo_model');
	
				$this->Indicador_grupo_model->excluir( $id );
	
				redirect( 'indicador/grupo', 'refresh' );
			}
		}
	}
}
?>