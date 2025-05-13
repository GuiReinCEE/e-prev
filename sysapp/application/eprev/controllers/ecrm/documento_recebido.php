<?php
class Documento_recebido extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GCM')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function index($cd_empresa = '', $cd_registro_empregado = '', $seq_dependencia = '')
    {
        if($this->get_permissao())
        {
            $this->load->model('autoatendimento/documento_recebido_model');

            $data = array(
                'cd_empresa'            => $cd_empresa,
                'cd_registro_empregado' => $cd_registro_empregado,
                'seq_dependencia'       => $seq_dependencia,
                'origem'                => $this->documento_recebido_model->get_origem()
            );

            $this->load->view('ecrm/documento_recebido/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('autoatendimento/documento_recebido_model');

        $args = array(
            'cd_empresa'                 => $this->input->post('cd_empresa', TRUE),
            'cd_registro_empregado'      => $this->input->post('cd_registro_empregado', TRUE),
            'seq_dependencia'            => $this->input->post('seq_dependencia', TRUE),
            'nome_participante'          => $this->input->post('nome_participante', TRUE),
            'dt_encaminhamento_ini'      => $this->input->post('dt_encaminhamento_ini', TRUE),
            'dt_encaminhamento_fim'      => $this->input->post('dt_encaminhamento_fim', TRUE),
            'cd_documento_recebido_tipo' => $this->input->post('cd_documento_recebido_tipo', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->documento_recebido_model->listar($args);

        $this->load->view('ecrm/documento_recebido/index_result', $data);
    }
}