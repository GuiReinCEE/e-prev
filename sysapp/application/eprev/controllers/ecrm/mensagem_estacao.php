<?php
class mensagem_estacao extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		$this->load->model('projetos/mensagem_estacao_model');
    }

    function index()
    {
		CheckLogin();
		
        $this->load->view('ecrm/mensagem_estacao/index.php');
    }

    function listar()
    {
		CheckLogin();
		
        $data = array();
        $result = null;
		$args = array();

		$args["dt_inicio_ini"] = $this->input->post("dt_inicio_ini", TRUE);
		$args["dt_inicio_fim"] = $this->input->post("dt_inicio_fim", TRUE);

		manter_filtros($args);

        $this->mensagem_estacao_model->listar( $result, $args );

		$data['collection'] = $result->result_array();

        $this->load->view('ecrm/mensagem_estacao/partial_result', $data);
    }

	function cadastro($cd_mensagem_estacao = 0)
	{
		CheckLogin();
		
		$data = array();
        $result = null;
		$args = array();
		
		$data['ar_gerencia_checked'] = array();
		
		$args['cd_mensagem_estacao'] = intval($cd_mensagem_estacao);
		
		$this->mensagem_estacao_model->gerencias($result, $args);
		$data['ar_gerencia'] = $result->result_array();	
		
		if($args['cd_mensagem_estacao'] == 0)
		{
			$data['row'] = array(
				'cd_mensagem_estacao' => 0,
				'nome'                => '',
				'url'                 => '',
				'dt_inicio'           => '',
				'dt_final'            => '',
				'hr_inicio'           => '00:00',
				'hr_final'            => '23:59',
				'arquivo'             => '',
				'arquivo_nome'        => ''
			);
		}
		else
		{
			$this->mensagem_estacao_model->carrega($result, $args);
			$data['row'] = $result->row_array();	
			
			$this->mensagem_estacao_model->gerencia_checked($result, $args);
			$ar_gerencia = $result->result_array();	
			
			foreach($ar_gerencia as $item)
			{				
				$data['ar_gerencia_checked'][] = trim($item['gerencia']);
			}
		}
		
		$this->load->view('ecrm/mensagem_estacao/cadastro', $data);
	}

	function salvar()
	{
		CheckLogin();
		
		$data = array();
        $result = null;
		$args = array();
		
		$args["cd_mensagem_estacao"] = $this->input->post("cd_mensagem_estacao", TRUE);
		$args["nome"]                = $this->input->post("nome", TRUE);
		$args["url"]                 = $this->input->post("url", TRUE);
		$args["ar_gerencia"]         = $this->input->post("ar_gerencia", TRUE);
		$args["dt_inicio"]           = $this->input->post("dt_inicio", TRUE);
		$args["hr_inicio"]           = $this->input->post("hr_inicio", TRUE);
		$args["dt_final"]            = $this->input->post("dt_final", TRUE);
		$args["hr_final"]            = $this->input->post("hr_final", TRUE);
		$args['arquivo_nome']        = $this->input->post("arquivo_nome", TRUE);
        $args['arquivo']             = $this->input->post("arquivo", TRUE);
		$args['cd_usuario']          = $this->session->userdata("codigo");

		$this->mensagem_estacao_model->salvar($result, $args);

		redirect( "ecrm/mensagem_estacao", "refresh" );
	}
	
	function excluir($cd_mensagem_estacao)
	{
		CheckLogin();
	
		$data = array();
        $result = null;
		$args = array();
		
		$args['cd_mensagem_estacao'] = $cd_mensagem_estacao;
		$args['cd_usuario']          = $this->session->userdata("codigo");
		
		$this->mensagem_estacao_model->excluir($result, $args);
		
		redirect( "ecrm/mensagem_estacao", "refresh" );
	}
	
	function estacao($usuario)
	{
		$data = array();
        $result = null;
		$args = array();
		
		$args['usuario'] = trim($usuario);
		
		$this->mensagem_estacao_model->carrega_mensagem($result, $args);
		$row = $result->row_array();
		
		if(count($row) > 0)
		{
			if(trim($row['url']) != '')
			{
				$url = str_replace("[USUARIO]",$usuario,$row["url"]);
				
				echo '<center><a href="'.$url.'" title="Clique para abrir"><img src="'.base_url().'up/mensagem_estacao/'.$row["arquivo"].'" border="0"></a></center>';
			}
			else
			{
				echo '<center><img src="'.base_url().'up/mensagem_estacao/'.$row["arquivo"].'" border="0"></center>';
			}
		}
	}
	
	function temMensagem($usuario)
	{
		$data = array();
        $result = null;
		$args = array();
		
		$args['usuario'] = trim($usuario);
		
		$this->mensagem_estacao_model->temMensagem($result, $args);
		$row = $result->row_array();
		
		$ar_ret['fl_mensagem'] = 0;
		if(intval($row['fl_mensagem']) > 0)
		{
			$ar_ret['fl_mensagem'] = 1;
		}
		
		echo json_encode($ar_ret);
	}
	
	function setExibiuMensagem($usuario)
	{
		$data = array();
        $result = null;
		$args = array();
		
		$args['usuario'] = trim($usuario);
		
		$this->mensagem_estacao_model->setExibiuMensagem($result, $args);
	}	
}
?>