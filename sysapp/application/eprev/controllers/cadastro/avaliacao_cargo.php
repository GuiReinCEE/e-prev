<?php
class Avaliacao_cargo extends Controller
{
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
		
        $this->load->model('projetos/cargos_model');
    }

    function index()
    {
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
		
			$this->cargos_model->familia( $result, $args );
            $data['arr_familia'] = $result->result_array();
			
            $this->load->view('cadastro/avaliacao_cargo/index', $data);
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

            $args["nome_cargo"] = $this->input->post("nome_cargo", TRUE);
            $args["cd_familia"] = $this->input->post("cd_familia", TRUE);

            manter_filtros($args);

            $this->cargos_model->listar( $result, $args );
            $data['collection'] = $result->result_array();

            $this->load->view('cadastro/avaliacao_cargo/index_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

	function cadastro($cd_cargo = 0)
	{
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
			
            $data['institucionais'] = array();
            $data['institucionais_chk'] = array();
            $data['especificas'] = array();
            $data['especificas_chk'] = array();
            $data['responsabilidades'] = array();
            $data['responsabilidades_chk'] = array();

            $this->cargos_model->familia( $result, $args );
            $data['familia'] = $result->result_array();

            if($cd_cargo == 0)
            {
                $data['row'] = Array(
					'cd_cargo'   => $cd_cargo,
                    'nome_cargo' => '',
                    'cd_familia' => '',
                    'desc_cargo' => ''
                );
            }
            else
            {
                $args['cd_cargo'] = intval($cd_cargo);

                $this->cargos_model->carregar($result, $args);
                $data['row'] = $result->row_array();

                $this->cargos_model->competencias_institucionais($result, $args);
                $data['institucionais'] = $result->result_array();

                $this->cargos_model->competencias_institucionais_chk($result, $args);
                $institucionais_chk = $result->result_array();

                foreach($institucionais_chk as $item)
                {
                    $data['institucionais_chk'][] = $item['cd_comp_inst'];
                }

                $this->cargos_model->competencias_especificas($result, $args);
                $data['especificas'] = $result->result_array();

                $this->cargos_model->competencias_especificas_chk($result, $args);
                $especificas_chk = $result->result_array();

                foreach($especificas_chk as $item)
                {
                    $data['especificas_chk'][] = $item['cd_comp_espec'];
                }

                $this->cargos_model->responsabilidades($result, $args);
                $data['responsabilidades'] = $result->result_array();

                $this->cargos_model->responsabilidades_chk($result, $args);
                $responsabilidades_chk = $result->result_array();

                foreach($responsabilidades_chk as $item)
                {
                    $data['responsabilidades_chk'][] = $item['cd_responsabilidade'];
                }
            }

            $this->load->view('cadastro/avaliacao_cargo/cadastro', $data);
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
			$args = Array();
			$data = Array();
			$result = null;

            $args["cd_cargo"]          = $this->input->post("cd_cargo", TRUE);
            $args["nome_cargo"]        = $this->input->post("nome_cargo", TRUE);
            $args["cd_familia"]        = $this->input->post("cd_familia", TRUE);
            $args["desc_cargo"]        = $this->input->post("desc_cargo", TRUE);
            $args["institucionais"]    = $this->input->post("institucionais", TRUE);
            $args["especificas"]       = $this->input->post("especificas", TRUE);
            $args["responsabilidades"] = $this->input->post("responsabilidades", TRUE);

            $retorno = $this->cargos_model->salvar($result, $args);

            if(intval($args["cd_cargo"]) > 0)
            {
                redirect("cadastro/avaliacao_cargo", "refresh");
            }
            else
            {
                redirect("cadastro/avaliacao_cargo/cadastro/".$retorno, "refresh");
            }
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }

    }
}
?>