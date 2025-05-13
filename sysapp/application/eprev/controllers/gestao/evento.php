<?php
class evento extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/eventos_model');
    }

    function index()
    {
        $this->load->view('gestao/evento/index');
    }

    function listar()
    {
        $result = null;
		$args   = Array();
		$data   = Array();

		$args["nome"] = $this->input->post("nome", TRUE);

		manter_filtros($args);

        $this->eventos_model->listar( $result, $args );
		$data['collection'] = $result->result_array();

        $this->load->view('gestao/evento/index_result', $data);
    }

	function cadastro($cd_evento = 0)
	{
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$args['cd_evento'] = intval($cd_evento);
		
		$this->eventos_model->projeto( $result, $args );
		$data['arr_projeto'] = $result->result_array();
		
		$this->eventos_model->lista_evento( $result, $args );
		$data['arr_evento'] = $result->result_array();
		
		$this->eventos_model->lista_referencia_evento( $result, $args );
		$data['arr_referencia_evento'] = $result->result_array();
		
		$this->eventos_model->destino( $result, $args );
		$data['arr_destino'] = $result->result_array();
		
		$data['arr_destino_checked']             = Array();
		$data['arr_destino_alternativo_checked'] = Array();
		
		if(intval($args['cd_evento']) == 0)
		{
			$data['row'] = array(
				'cd_evento'          => $args['cd_evento'],
				'cd_projeto'         => '',
				'nome'               => '',
				'tipo'               => '',
				'dias_dt_referencia' => '',
				'dt_referencia'      => '',
				'indic_historico'    => '',
				'indic_email'        => '',
				'email'              => ''
			);
		}
		else
		{
			$this->eventos_model->carrega($result, $args);
			$data['row'] = $result->row_array();	
			
			$this->eventos_model->destino_checked($result, $args);
			$arr_destino = $result->result_array();
			
			foreach($arr_destino as $item)
			{				
				$data['arr_destino_checked'][] = $item['cd_instancia'];
			}
			
			$this->eventos_model->destino_alternativo_checked($result, $args);
			$arr_destino_alternativo = $result->result_array();
			
			foreach($arr_destino_alternativo as $item)
			{				
				$data['arr_destino_alternativo_checked'][] = $item['cd_instancia'];
			}
		}
	
		$this->load->view('gestao/evento/cadastro', $data);
	}
	
	function salvar()
	{
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$args["cd_evento"]          = $this->input->post("cd_evento", TRUE);
		$args["cd_projeto"]         = $this->input->post("cd_projeto", TRUE);
		$args["nome"]               = $this->input->post("nome", TRUE);
		$args["tipo"]               = $this->input->post("tipo", TRUE);
		$args["dias_dt_referencia"] = $this->input->post("dias_dt_referencia", TRUE);
		$args["dt_referencia"]      = $this->input->post("dt_referencia", TRUE);
		$args["indic_historico"]    = $this->input->post("indic_historico", TRUE);
		$args["indic_email"]        = $this->input->post("indic_email", TRUE);
		$args["email"]              = $this->input->post("email", TRUE);
		$args['arr_destino']        = $this->input->post("arr_destino", TRUE);
		$args['arr_alternativo']    = $this->input->post("arr_alternativo", TRUE);
		
		$this->eventos_model->salvar( $result, $args );
			
		redirect("gestao/evento/", "refresh");	
	} 
	
	
}
?>