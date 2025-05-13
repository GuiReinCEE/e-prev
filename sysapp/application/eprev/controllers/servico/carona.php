<?php
class carona extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
        CheckLogin();

        $this->load->model('projetos/carona_model');

        $args = Array();
        $data = Array();
        $result = null;

        $this->load->view('servico/carona/index',$data);
    }

    function listar()
    {
        CheckLogin();

        $this->load->model('projetos/carona_model');

        $args = Array();
        $data = Array();
        $result = null;
        $data['caronas'] = Array();
        $data['arr_caroneiros'] = Array();

        $args["vagas"] = $this->input->post("vagas", TRUE);
        $args["gerencia"] = $this->input->post("gerencia", TRUE);
        $args["usuario"] = $this->input->post("usuario", TRUE);
        $args["cd_usuario_inclusao"] = $this->session->userdata('codigo');

        manter_filtros($args);

        $this->carona_model->listaCaronas($result, $args);
        $data['caronas'] = $result->result_array();

        $this->carona_model->verificaCaroneiro($result, $args);
        $arr_ver = $result->row_array();
      
        $data['tl_caronas'] = $arr_ver['total'];

        foreach($data['caronas'] as $item)
        {
            $args['cd_carona'] = $item['cd_carona'];
            $this->carona_model->listaCaroneiros($result, $args);
            $data['arr_caroneiros'][$item['cd_carona']] = $result->result_array();
        }

        $this->load->view('servico/carona/partial_result', $data);
    }

    function cadastro($cd_carona = 0)
    {
        CheckLogin();

        $this->load->model('projetos/carona_model');

        $args = Array();
		$data = Array();
		$result = null;
        $data['cd_carona'] = $cd_carona;

        if($data['cd_carona'] == 0 )
        {
            $data['row'] = Array(
                    'cd_carona' => 0,
                    'trajeto_vinda' => '',
                    'trajeto_retorno' => '',
                    'nr_vaga' => 0
                );
        }
        else
        {
            $args['cd_carona'] = intval($cd_carona);
            $this->carona_model->carrega($result, $args);
            $data['row'] = $result->row_array();

            $this->carona_model->listaCaroneiros($result, $args);
            $data['caroneiros'] = $result->result_array();
        }

        $this->load->view('servico/carona/cadastro', $data);
    }

    function salvar()
    {
        CheckLogin();
        $this->load->model('projetos/carona_model');

        $args = Array();
		$data = Array();
		$result = null;

        $args["cd_usuario_inclusao"] = $this->session->userdata('codigo');
        $args["trajeto_vinda"]       = $this->input->post("trajeto_vinda", TRUE);
        $args["trajeto_retorno"]     = $this->input->post("trajeto_retorno", TRUE);
        $args["nr_vaga"]             = $this->input->post("nr_vaga", TRUE);
        $args["cd_carona"]           = $this->input->post("cd_carona", TRUE);

        $this->carona_model->salvar($result, $args);
		redirect("servico/carona", "refresh");
    }

    function entrar($cd_carona =0)
    {
        CheckLogin();
        $this->load->model('projetos/carona_model');

        $args = Array();
		$data = Array();
        $result = null;

        $args["cd_carona"] = $cd_carona;
        $args["cd_usuario_inclusao"] = $this->session->userdata('codigo');

        $this->carona_model->entrar($result, $args);
		redirect("servico/carona", "refresh");
    }

    function sair($cd_carona_caroneiro =0)
    {
        CheckLogin();
        $this->load->model('projetos/carona_model');

        $args = Array();
		$data = Array();
        $result = null;

        $args["cd_carona_caroneiro"] = $cd_carona_caroneiro;
        $args["cd_usuario_exclusao"] = $this->session->userdata('codigo');

        $this->carona_model->sair($result, $args);
		redirect("servico/carona", "refresh");
    }

    function excluir($cd_carona =0)
    {
        CheckLogin();
        $this->load->model('projetos/carona_model');

        $args = Array();
		$data = Array();
        $result = null;

        $args["cd_carona"] = $cd_carona;
        $args["cd_usuario_exclusao"] = $this->session->userdata('codigo');

        $this->carona_model->excluir($result, $args);
		redirect("servico/carona", "refresh");
    }
}

?>