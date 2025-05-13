<?php
class ri_torcida_bate_bola_comentario extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		if(CheckLogin())
		{
			$q=$this->db->query('select cd_bate_bola as value, ds_bate_bola as text from torcida.bate_bola where dt_exclusao is null order by ds_bate_bola');
			$data['bate_bola_dd']=$q->result_array();

	        $this->load->view('ecrm/ri_torcida_bate_bola_comentario/index.php', $data);
		}
    }

    function listar()
    {
        if(CheckLogin())
        {
	        $this->load->model('torcida/Bate_bola_comentario_model');

	        $data['collection'] = array();
	        $result=null;

	        // --------------------------
			// filtros ...

			$args=array();

			$args["cd_bate_bola"] = intval($this->input->post("cd_bate_bola", TRUE));

			manter_filtros($args);

			// --------------------------
			// listar ...

	        $this->Bate_bola_comentario_model->listar( $result, $args );

			$data['collection'] = $result->result_array();

	        if( $result )
	        {
	            $data['collection'] = $result->result_array();
	        }

	        // --------------------------

	        $this->load->view('ecrm/ri_torcida_bate_bola_comentario/partial_result', $data);
        }
    }

	function liberar()
	{
		if(CheckLogin())
		{
			$this->load->model( 'torcida/Bate_bola_comentario_model', 'dbModel' );
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
			$this->load->model( 'torcida/Bate_bola_comentario_model', 'dbModel' );
			$cd=$this->input->post('cd');

			$msg=array();
			$b = $this->dbModel->bloquear( $cd, $msg );

			if($b){ echo 'true'; }
			else{ echo 'Algum problema ocorreu.'; }
		}
	}
	
	function excluir($id)
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Bate_bola_comentario_model', 'dbModel');

			$this->dbModel->excluir( $id );

			redirect( 'ecrm/ri_torcida_bate_bola_comentario', 'refresh' );
		}
	}

	function detalhe($cd=0)
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Bate_bola_comentario_model');
			$row=$this->Bate_bola_comentario_model->carregar( $cd );
			if($row){ $data['row'] = $row; }
			$this->load->view('ecrm/ri_torcida_bate_bola_comentario/detalhe', $data);
		}
	}

	function salvar()
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Bate_bola_comentario_model');

			$args['cd_bate_bola_comentario']=intval($this->input->post('cd_bate_bola_comentario', TRUE));
			$args["nome"] = $this->input->post("nome",true);
			$args["comentario"] = $this->input->post("comentario",true);
			$args["cd_usuario_inclusao"] = usuario_id();

			$msg=array();
			$retorno = $this->Bate_bola_comentario_model->salvar( $args,$msg );

			if($retorno)
			{
				redirect( "ecrm/ri_torcida_bate_bola_comentario/", "refresh" );			
			}
			else
			{
				$mensagens = implode('<br>',$msg);
				exibir_mensagem($msg[0]);
			}
		}
	}
}
