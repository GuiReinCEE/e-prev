<?php
class gestao_aviso extends Controller
{
	var $ar_periodicidade = array();
	
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model("gestao/gestao_aviso_model");
		
		$this->ar_periodicidade = array(
			array('text' => 'Selecione',     'value' => ''),
			array('text' => 'Eventual',      'value' => 'E'),
			array('text' => 'Diário',        'value' => 'D'),
			array('text' => 'Semanal',       'value' => 'S'),
			array('text' => 'Semestral',     'value' => 'L'),
			array('text' => 'Trimestral',    'value' => 'T'),
			array('text' => 'Quadrimestral', 'value' => 'Q'),
			array('text' => 'Mensal',        'value' => 'M'),
			array('text' => 'Anual',         'value' => 'A'),
			array('text' => 'Bianual',       'value' => 'B'),
			array('text' => 'Mensal (Dias úteis)',       'value' => 'U'),
			array('text' => 'Mensal (Dias úteis - Antes da Data)',       'value' => 'N')
        );			
    }

    function index()
    {	
		$result = null;
		$args   = array();
		$data   = array();

		$data['ar_periodicidade'] = $this->ar_periodicidade;

		$this->load->view("gestao/gestao_aviso/index", $data);	
    }

	function listar()
	{
		$result = null;
		$args   = array();
		$data   = array();
		
		$args["cd_periodicidade"] = $this->input->post("cd_periodicidade", TRUE);
		$args["cd_gerencia"]      = $this->session->userdata("divisao");
		
		manter_filtros($args);

		$this->gestao_aviso_model->listar($result, $args);
		$data['collection'] = $result->result_array();

		$this->load->view('gestao/gestao_aviso/index_result', $data);	
	}
	
    function cadastro($cd_gestao_aviso = 0)
    {	
		$result = null;
		$args   = array();
		$data   = array();
		
		$args["cd_gestao_aviso"] = intval($cd_gestao_aviso);

		$this->gestao_aviso_model->usuario($result, $args);
		$data['ar_periodicidade'] = $this->ar_periodicidade;

		$data['ar_usuario'] = $result->result_array();

		$data['ar_usuario_checked'] = array();
 
        if(intval($cd_gestao_aviso) == 0)
        {
            $data['row'] = array(
                'cd_gestao_aviso'     => intval($cd_gestao_aviso),
                'ds_descricao' 	      => '',     
                'cd_periodicidade'    => '',
                'dt_referencia'		  => '',
                'qt_dia'			  => '', 
                'dt_inclusao'	      => '',
                'dt_verificacao'	  => '',
                'tl_gestao_aviso_controle' => 0
            );
        }
        else
        {
			$data['row'] = $this->gestao_aviso_model->carrega(intval($cd_gestao_aviso));
        }

		$usuario_x = $this->gestao_aviso_model->get_usuario_checked(intval($cd_gestao_aviso));

		foreach($usuario_x as $item)
		{
			$data['ar_usuario_checked'][] = $item['cd_usuario'];
		}

		$this->load->view("gestao/gestao_aviso/cadastro", $data);		
    }

	function salvar()
	{
		$this->load->model('gestao/gestao_aviso_model');

		$result = null;
		$args   = array();
		$data   = array();
		$cd_gestao_aviso = $this->input->post('cd_gestao_aviso', TRUE);

		$args['cd_gestao_aviso']	  = $cd_gestao_aviso;
		$args["ds_descricao"]         = $this->input->post("ds_descricao", TRUE);
		$args["cd_periodicidade"]     = $this->input->post("cd_periodicidade", TRUE);
		$args["qt_dia"]               = $this->input->post("qt_dia", TRUE);
		$args["dt_referencia"]        = $this->input->post("dt_referencia", TRUE);
		$args["ar_usuario"]           = $this->input->post("ar_usuario", TRUE);
		$args["fl_diretoria"]         = $this->input->post("fl_diretoria", TRUE);
		$args["cd_gerencia"]          = $this->session->userdata("divisao");
		$args["cd_usuario_inclusao"]  = $this->session->userdata("codigo");
		$args["cd_usuario_alteracao"] = $this->session->userdata('codigo');
		$args["cd_usuario_exclusao"]  = $this->session->userdata("codigo");

		if(!is_array($args['ar_usuario']))
		{
			$ar_usuario = array();	
		}

		if(intval($cd_gestao_aviso) == 0)
		{
			$this->gestao_aviso_model->salvar($result, $args);
		}
		else
		{
			$this->gestao_aviso_model->atualizar($cd_gestao_aviso, $args);
		}

		$fl_diretoria = $this->input->post("fl_diretoria", TRUE);

		if(trim($fl_diretoria) == 'S')
		{
			redirect("gestao/gestao_aviso/aviso_diretoria", "refresh");	
		}
		else
		{
			redirect("gestao/gestao_aviso", "refresh");	
		}
	}	

    function excluir($cd_gestao_aviso = 0)
    {
		$result = null;
		$args   = array();
		$data   = array();

		$args["cd_gestao_aviso"] = intval($cd_gestao_aviso);
		$args["cd_usuario"]  = $this->session->userdata("codigo");
		
		$this->gestao_aviso_model->excluir($result, $args);

		redirect("gestao/gestao_aviso", "refresh");		
    }		
		
    function verificar($cd_gestao_aviso_verificacao = 0)
    {
		$result = null;
		$args   = array();
		$data   = array();

		$args["cd_gestao_aviso_verificacao"] = intval($cd_gestao_aviso_verificacao);

		$this->gestao_aviso_model->verificar($result, $args);
		$data['row'] = $result->row_array();

		$data['collection'] = $this->gestao_aviso_model->acompanhamento_listar($cd_gestao_aviso_verificacao);
		
		$this->load->view("gestao/gestao_aviso/verificar", $data);
    }	
	
	function verificarSalvar()
	{
		$result = null;
		$args   = array();
		$data   = array();
		
		$args["cd_gestao_aviso_verificacao"] = $this->input->post("cd_gestao_aviso_verificacao", TRUE);
		$args["cd_usuario"]                  = $this->session->userdata("codigo");
		
		$this->gestao_aviso_model->verificarSalvar($result, $args);

		redirect("gestao/gestao_aviso/verificar/".intval($args["cd_gestao_aviso_verificacao"]), "refresh");
	}	

	function historico($cd_gestao_aviso, $fl_diretoria = 'N')
    {
		$result = null;
		$args   = array();
		$data   = array();

		$args["cd_gestao_aviso"] = intval($cd_gestao_aviso);
		$data["fl_diretoria"] = trim($fl_diretoria);
			
		$this->gestao_aviso_model->cadastro($result, $args);
		$data['row'] = $result->row_array();

		$this->gestao_aviso_model->listar_verificar($result, $args);
		$data['collection'] = $result->result_array();

		$data['acompanhamento'] = $this->gestao_aviso_model->aviso_diretoria_acompanhamento_listar($cd_gestao_aviso);
		
		$this->load->view("gestao/gestao_aviso/historico", $data);		
    }

    public function aviso_diretoria()
    {
    	if(gerencia_in(array('DE')))
        {
	        $this->load->model('gestao/gestao_aviso_model');

	        $data = array();

	        $this->load->view('gestao/gestao_aviso/aviso_diretoria', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function aviso_diretoria_listar()
    {
    	$this->load->model('gestao/gestao_aviso_model');

		$args = array(
            'dt_referencia_ini'  => $this->input->post('dt_referencia_ini', TRUE),
            'dt_referencia_fim'  => $this->input->post('dt_referencia_fim', TRUE),
            'dt_verificacao_ini' => $this->input->post('dt_verificacao_ini', TRUE),
            'dt_verificacao_fim' => $this->input->post('dt_verificacao_fim', TRUE),
            'fl_verificado'      => $this->input->post('fl_verificado', TRUE)
        );
		
		manter_filtros($args);

		$data['collection'] = $this->gestao_aviso_model->aviso_diretoria_listar($args);

		foreach ($data['collection'] as $key => $item)
		{
			$data['collection'][$key]['usuario'] = array();

			foreach ($this->gestao_aviso_model->get_usuario_checked($item['cd_gestao_aviso'], 'S') as $key2 => $item2)
			{
				$data['collection'][$key]['usuario'][] = $item2['ds_usuario'];
			}
		}

		$this->load->view('gestao/gestao_aviso/aviso_diretoria_result', $data);
    }

    public function aviso_diretoria_cadastro($cd_gestao_aviso = 0)
    {
		$result = null;
		$args   = array();
		$data   = array();
		
		$args["cd_gestao_aviso"] = intval($cd_gestao_aviso);

		$this->gestao_aviso_model->usuario($result, $args);
		$data['ar_periodicidade'] = $this->ar_periodicidade;

		$data['ar_usuario'] = $result->result_array();

		$data['ar_usuario_checked'] = array();
 
        if(intval($cd_gestao_aviso) == 0)
        {
            $data['row'] = array(
                'cd_gestao_aviso'          => intval($cd_gestao_aviso),
                'ds_descricao' 	           => '',     
                'cd_periodicidade'         => 'E',
                'dt_referencia'		       => '',
                'qt_dia'			       => '', 
                'dt_inclusao'	           => '',
                'dt_verificacao'	       => '',
                'tl_gestao_aviso_controle' => 0
            );

            $usuario_x = $this->gestao_aviso_model->get_usuario_diretoria();

            foreach($usuario_x as $item)
			{
				$data['ar_usuario_checked'][] = $item['cd_usuario'];
			}
        }
        else
        {
			$data['row'] = $this->gestao_aviso_model->carrega(intval($cd_gestao_aviso));
        }

		$usuario_x = $this->gestao_aviso_model->get_usuario_checked(intval($cd_gestao_aviso));

		foreach($usuario_x as $item)
		{
			$data['ar_usuario_checked'][] = $item['cd_usuario'];
		}

		$this->load->view("gestao/gestao_aviso/aviso_diretoria_cadastro", $data);		
    }

    public function aviso_diretoria_minhas()
    {
    	$this->load->model('gestao/gestao_aviso_model');

        $data = array();

        $this->load->view('gestao/gestao_aviso/aviso_diretoria_minhas', $data);
    }

    public function aviso_diretoria_minhas_listar()
	{
		$args = array(
            'dt_referencia_ini'  => $this->input->post('dt_referencia_ini', TRUE),
            'dt_referencia_fim'  => $this->input->post('dt_referencia_fim', TRUE),
            'dt_verificacao_ini' => $this->input->post('dt_verificacao_ini', TRUE),
            'dt_verificacao_fim' => $this->input->post('dt_verificacao_fim', TRUE),
            'fl_verificado'      => $this->input->post('fl_verificado', TRUE)
        );
		
		manter_filtros($args);

		$data['collection'] = $this->gestao_aviso_model->aviso_diretoria_minhas_listar(
			$this->session->userdata('codigo'), 
			$args
		);

		$this->load->view('gestao/gestao_aviso/aviso_diretoria_minhas_result', $data);	
	}

    public function acompanhamento_salvar()
    {
    	$this->load->model('gestao/gestao_aviso_model');

    	$cd_gestao_aviso_verificacao = $this->input->post("cd_gestao_aviso_verificacao", TRUE);
    	$ds_gestao_aviso_verificacao_acompanhamento = $this->input->post("ds_gestao_aviso_verificacao_acompanhamento", TRUE);
		
		$this->gestao_aviso_model->acompanhamento_salvar($cd_gestao_aviso_verificacao, $ds_gestao_aviso_verificacao_acompanhamento, $this->session->userdata("codigo"));

		redirect("gestao/gestao_aviso/verificar/".intval($cd_gestao_aviso_verificacao), "refresh");
    }

    public function controle_pendencia()
    {
    	if(gerencia_in(array('GC')))
        {
	        $this->load->model('gestao/gestao_aviso_model');

	        $data = array();

	        $this->load->view('gestao/gestao_aviso/controle_pendencia', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar_controle_pendencia()
    {
        $this->load->model('gestao/gestao_aviso_model');

        $args = array(
            'dt_referencia_ini'  => $this->input->post('dt_referencia_ini', TRUE),
            'dt_referencia_fim'  => $this->input->post('dt_referencia_fim', TRUE),
            'dt_verificacao_ini' => $this->input->post('dt_verificacao_ini', TRUE),
            'dt_verificacao_fim' => $this->input->post('dt_verificacao_fim', TRUE),
            'fl_verificado'      => $this->input->post('fl_verificado', TRUE)
        );

        manter_filtros($args);

		$data['collection'] = $this->gestao_aviso_model->listar_controle_pendencia($args);

		foreach ($data['collection'] as $key => $item)
		{
			foreach ($this->gestao_aviso_model->get_usuario_checked($item['cd_gestao_aviso']) as $key2 => $item2)
			{
				$data['collection'][$key]['usuario'][] = $item2['ds_usuario'];
			}
		}

        $this->load->view('gestao/gestao_aviso/controle_pendencia_result', $data);
    }
}