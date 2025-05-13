<?php
class ri_torcida_noticia extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		if(CheckLogin())
		{
	        $this->load->view('ecrm/ri_torcida_noticia/index.php');
		}
    }

    function listar()
    {
        if(CheckLogin())
        {
	        $this->load->model('torcida/Noticia_model');
	
	        $data['collection'] = array();
	        $result = null;
	
	        // --------------------------
			// filtros ...
	
			$args=array();
			$args['tp_noticia']=$this->input->post('tp_noticia', TRUE);
	
			manter_filtros($args);
	
			// --------------------------
			// listar ...
	
	        $this->Noticia_model->listar( $result, $args );
	
			$data['collection'] = $result->result_array();
	
	        if( $result )
	        {
	            $data['collection'] = $result->result_array();
	        }
	
	        // --------------------------
	
	        $this->load->view('ecrm/ri_torcida_noticia/partial_result', $data);
        }
    }

	function detalhe($cd=0)
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Noticia_model');
			$row=$this->Noticia_model->carregar( $cd );
			if($row){ $data['row'] = $row; }
			$this->load->view('ecrm/ri_torcida_noticia/detalhe', $data);
		}
	}

	function salvar()
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Noticia_model');
			
			$args['cd_noticia']=intval($this->input->post('cd_noticia', TRUE));
	
			$args["ds_titulo"] = $this->input->post("ds_titulo",TRUE);
			$args["ds_noticia"] = $this->input->post("ds_noticia",TRUE);
			$args["ds_resumo"] = $this->input->post("ds_resumo",TRUE);
			$args["tp_noticia"] = $this->input->post("tp_noticia",TRUE);
			$args["cd_noticia"] = $this->input->post("cd_noticia",TRUE);
			$args["cd_usuario_inclusao"] = usuario_id();

			$msg=array();
			$retorno = $this->Noticia_model->salvar( $args,$msg );
			
			if($retorno)
			{
				if ($args['cd_noticia'] == 0)
				{
					redirect( "ecrm/ri_torcida_noticia", "refresh" );			
				}
				else
				{
					redirect( "ecrm/ri_torcida_noticia/detalhe/".$args['cd_noticia'], "refresh" );
				}
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
			$this->load->model('torcida/Noticia_model');

			$this->Noticia_model->excluir( $id );

			redirect( 'ecrm/ri_torcida_noticia', 'refresh' );
		}
	}
	
	function liberar()
	{
		if(CheckLogin())
		{
			$this->load->model( 'torcida/Noticia_model', 'dbModel' );
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
			$this->load->model( 'torcida/Noticia_model', 'dbModel' );
			$cd=$this->input->post('cd');
			
			$msg=array();
			$b = $this->dbModel->bloquear( $cd, $msg );
			
			if($b){ echo 'true'; }
			else{ echo 'Algum problema ocorreu.'; }
		}
	}
}