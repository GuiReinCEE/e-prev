<?php
class iso extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

        $this->load->model('gestao/pendencia_auditoria_iso_model');
    }

    function index()
    {
        $args = Array();
		$data = Array();
		$result = null;

        $this->pendencia_auditoria_iso_model->lista_processo( $result, $args );
        $data['processo'] = $result->result_array();

        $this->pendencia_auditoria_iso_model->lista_auditoria( $result, $args );
        $data['auditoria'] = $result->result_array();

        $this->pendencia_auditoria_iso_model->lista_gerencia( $result, $args );
        $data['gerencia'] = $result->result_array();

        $this->load->view('gestao/iso/index.php',$data);
    }

    function listar()
    {
        $args = Array();
		$data = Array();
		$result = null;
        $nr_conta = 0;

        $args["cd_processo"]                     = $this->input->post("cd_processo", TRUE);
        $args["cd_pendencia_auditoria_iso_tipo"] = $this->input->post("cd_pendencia_auditoria_iso_tipo", TRUE);
        $args["dt_inicial"]                      = $this->input->post("dt_inicial", TRUE);
        $args["dt_final"]                        = $this->input->post("dt_final", TRUE);
        $args["fl_impacto"]                      = $this->input->post("fl_impacto", TRUE);
        $args["cd_gerencia"]                     = $this->input->post("cd_gerencia", TRUE);
        $args["fl_situacao"]                     = $this->input->post("fl_situacao", TRUE);
        $args["cd_responsavel"]                     = $this->input->post("cd_responsavel", TRUE);

        manter_filtros($args);

        $this->pendencia_auditoria_iso_model->listar( $result, $args );

        $lista = $result->result_array();
         
        foreach ($lista as $item)
        {
            $args['cd_pendencia_auditoria_iso'] = $item['cd_pendencia_auditoria_iso'];
            
            $this->pendencia_auditoria_iso_model->lista_iso_acompanhamento( $result, $args );
            
            $acompanhamento = $result->result_array();
            
            $lista[$nr_conta]['acompanhamento'] = $acompanhamento;
             
            $nr_conta ++;
        }
        
        $data['collection'] = $lista;
        
        $this->load->view('gestao/iso/partial_result', $data);
    }

    function acompanhamento($cd)
    {
        $args = Array();
		$data = Array();
		$result = null;

        $data['cd_pendencia_auditoria_iso'] = intval($cd);
        $args['cd_pendencia_auditoria_iso'] = intval($cd);

        $this->pendencia_auditoria_iso_model->lista_acompanhamento( $result, $args );

        $data['collection'] = $result->result_array();

        $this->load->view('gestao/iso/acompanhamento', $data);
    }

    function cadastro($cd=0)
    {      
        $args = Array();
		$data = Array();
		$result = null;
        $data['cd'] = intval($cd);
        $args['cd_pendencia_auditoria_iso'] = intval($cd);

        $this->pendencia_auditoria_iso_model->lista_processo( $result, $args );
        $data['processo'] = $result->result_array();

        $this->pendencia_auditoria_iso_model->lista_auditoria( $result, $args );
        $data['auditoria'] = $result->result_array();

        $this->pendencia_auditoria_iso_model->lista_gerencia( $result, $args );
        $data['gerencia'] = $result->result_array();

        if($data['cd'] == 0)
        {
            $data['row'] = Array('cd_pendencia_auditoria_iso' => 0,
                                 'cd_pendencia_auditoria_iso_tipo' => '',
                                 'nr_contatacao' => '',
                                 'fl_impacto' => '',
                                 'cd_processo' => '',
                                 'cd_responsavel' => '',
                                 'cd_gerencia' => '',
                                 'ds_item' => '',
                                 'dt_encerrada' => ''

                            );
        }
        else
        {
            $this->pendencia_auditoria_iso_model->carrega($result, $args);
			$data['row'] = $result->row_array();
            
        }

        $this->load->view('gestao/iso/cadastro', $data);
        
    }

    function salvar()
    {
        $args = Array();
		$data = Array();
		$result = null;

        $args["cd_pendencia_auditoria_iso"]      = $this->input->post("cd_pendencia_auditoria_iso", TRUE);
        $args["cd_pendencia_auditoria_iso_tipo"] = $this->input->post("cd_pendencia_auditoria_iso_tipo", TRUE);
		$args["nr_contatacao"]                   = $this->input->post("nr_contatacao", TRUE);
		$args["fl_impacto"]                      = $this->input->post("fl_impacto", TRUE);
		$args["cd_processo"]                     = $this->input->post("cd_processo", TRUE);
		$args["cd_responsavel"]                  = $this->input->post("cd_responsavel", TRUE);
		$args["cd_gerencia"]                     = $this->input->post("cd_gerencia", TRUE);
        $args["ds_item"]                         = $this->input->post("ds_item", TRUE);
        $args["cd_usuario_inclusao"]             = $this->session->userdata('codigo');

        $cd_pendencia_auditoria_iso = $this->pendencia_auditoria_iso_model->salvar($result, $args);
		redirect("gestao/iso/cadastro/".$cd_pendencia_auditoria_iso, "refresh");
    }

    function salva_acompanhamento()
    {
        $args = Array();
		$data = Array();
		$result = null;

        $args["cd_pendencia_auditoria_iso"]                = $this->input->post("cd_pendencia_auditoria_iso", TRUE);
        $args["ds_pendencia_auditoria_iso_acompanhamento"] = $this->input->post("ds_pendencia_auditoria_iso_acompanhamento", TRUE);
        $args["cd_usuario_inclusao"]                       = $this->session->userdata('codigo');

        $this->pendencia_auditoria_iso_model->salva_acompanhamento($result, $args);
		redirect("gestao/iso/acompanhamento/".$args["cd_pendencia_auditoria_iso"], "refresh");
    }

    function encerrar($cd)
    {
        $args = Array();
		$data = Array();
		$result = null;

        $args["cd_pendencia_auditoria_iso"] = $cd;
        $args["cd_usuario_encerrada"] = $this->session->userdata('codigo');

        $this->pendencia_auditoria_iso_model->encerrar($result, $args);
        redirect("gestao/iso/", "refresh");
    }
}