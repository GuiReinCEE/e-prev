<?php
class Sumula_assunto extends Controller {

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_colegiado()
    {
        return array(
            array('value' => 'DE', 'text' => 'Diretoria'),
            array('value' => 'CD', 'text' => 'Conselho Deliberativo'),
            array('value' => 'CF', 'text' => 'Conselho Fiscal'),
            array('value' => 'IN', 'text' => 'Interventor')
        );
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GC')))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function index()
    {
        if($this->get_permissao())
        {
            $data['colegiado'] = $this->get_colegiado();

            $this->load->view('gestao/sumula_assunto/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
       $this->load->model('gestao/sumula_assunto_model');

        $args = array(
            'nr_sumula'     => $this->input->post('nr_sumula', TRUE),
            'dt_sumula_ini' => $this->input->post('dt_sumula_ini', TRUE),
            'dt_sumula_fim' => $this->input->post('dt_sumula_fim', TRUE),
            'fl_colegiado'  => $this->input->post('fl_colegiado', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->sumula_assunto_model->listar($args);

        $this->load->view('gestao/sumula_assunto/index_result', $data);
    }

}