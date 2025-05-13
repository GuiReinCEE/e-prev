<?php
class avaliacao_familia extends Controller
{
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
		
        $this->load->model('projetos/familias_cargos_model');
    }

    function index()
    {
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $this->load->view('cadastro/avaliacao_familia/index');
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function listar()
    {
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $args = Array();
			$data = Array();
			$result = null;

            manter_filtros($args);

            $this->familias_cargos_model->listar( $result, $args );
            $data['collection'] = $result->result_array();

            $this->load->view('cadastro/avaliacao_familia/index_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function cadastro($cd_familia = 0)
    {
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $data   = array();
            $result = null;
            $args   = null;

            if($cd_familia == 0)
            {
                $data['row'] = Array(
					'cd_familia'   => $cd_familia,
                    'nome_familia' => '',
                    'classe'       => ''
				);
            }
            else
            {
                $args['cd_familia'] = intval($cd_familia);

                $this->familias_cargos_model->carregar($result, $args);
                $data['row'] = $result->row_array();
            }

            $this->load->view('cadastro/avaliacao_familia/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function listar_familias()
	{
		$data   = array();
		$result = null;
		$args   = null;

		$args["cd_familia"] = $this->input->post("cd_familia", TRUE);
		$data["cd_familia"] = $this->input->post("cd_familia", TRUE);
		
		$this->familias_cargos_model->escolaridade($result, $args);
		$data['escoldaridade'] = $result->result_array();

		$this->familias_cargos_model->familias_escolaridades($result, $args);
		$data['familias_escolaridades'] = $result->result_array();
		
		$this->load->view('cadastro/avaliacao_familia/cadastro_result', $data);
	}
	
    function familia($cd_familia, $cd_escolaridade)
    {
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $data   = array();
            $result = null;
            $args   = null;

            $data['cd_familia'] = intval($cd_familia);
            $data['cd_escolaridade'] = intval($cd_escolaridade);

            $args['cd_familia'] = intval($cd_familia);
            $args['cd_escolaridade'] = intval($cd_escolaridade);

            $this->familias_cargos_model->carregar($result, $args);
            $familia = $result->row_array();

            $this->familias_cargos_model->carrega_escolaridade($result, $args);
            $data['nome_escolaridade'] = $result->row_array();

            $this->familias_cargos_model->carrega_familias_escolaridades($result, $args);
            $data['row'] = $result->row_array();

            if(count($data['row']) > 0)
            {
                $data['tipo'] = 1;
            }
            else
            {
                $data['row'] = Array(
					'grau_percentual' => '',
                    'nivel'           => ''
                );
                
				$data['tipo'] = 0;
            }

            $data['nome_familia'] = $familia['nome_familia'];

            $this->load->view('cadastro/avaliacao_familia/familia_cargo', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function salvar()
    {
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $data   = array();
            $result = null;
            $args   = null;

            $args["cd_familia"]   = $this->input->post("cd_familia", TRUE);
            $args["nome_familia"] = $this->input->post("nome_familia", TRUE);
            $args["classe"]       = $this->input->post("classe", TRUE);
            $args["usuario"]      = $this->session->userdata('codigo');

            $retorno = $this->familias_cargos_model->salvar($result, $args);

            if(intval($args["cd_familia"]) > 0)
            {
                redirect("cadastro/avaliacao_familia", "refresh");
            }
            else
            {
                redirect("cadastro/avaliacao_familia/cadastro/".$retorno, "refresh");
            }
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function salva_familia()
    {
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$data   = array();
            $result = null;
            $args   = null;

            $args["cd_familia"]      = $this->input->post("cd_familia", TRUE);
            $args["cd_escolaridade"] = $this->input->post("cd_escolaridade", TRUE);
            $args["nivel"]           = $this->input->post("nivel", TRUE);
            $args["grau_percentual"] = $this->input->post("grau_percentual", TRUE);
            $args["tipo"]            = $this->input->post("tipo", TRUE);

            $this->familias_cargos_model->salva_familia($result, $args);

            redirect("cadastro/avaliacao_familia/cadastro/".$args["cd_familia"], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
}
?>