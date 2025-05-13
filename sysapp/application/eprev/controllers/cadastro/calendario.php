<?php
class Calendario extends Controller
{
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        
        $this->load->model('projetos/calendario_model');
    }
    
    function index()
    {
        if(gerencia_in(array('GTI', 'GC')))
        {
            $this->load->view('cadastro/calendario/index.php');
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function listar()
    {
        if(gerencia_in(array('GTI', 'GC')))
        {
            $data   = array();
            $result = null;
            $args   = array();

            $args['ano']           = $this->input->post('ano', TRUE);
            $args['tp_calendario'] = $this->input->post('tp_calendario', TRUE);

            manter_filtros($args);
            
            $this->calendario_model->listar( $result, $args );

            $data['collection'] = $result->result_array();

            $this->load->view('cadastro/calendario/partial_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function cadastro($cd_calendario = 0)
    {
        if(gerencia_in(array('GTI', 'GC')))
        {
            $data   = array();
            $result = null;
            $args   = array();

            $args['cd_calendario'] = $cd_calendario;
            $data['cd_calendario'] = $cd_calendario;

            if(intval($args['cd_calendario']) == 0)
            {   
                $data['row'] = array(
                    'cd_calendario' => 0,
                    'dt_calendario' => '',
                    'descricao'     => '',
                    'tp_calendario' => '',
                    'turno'         => '',
                    'ds_url'        => ''
                );
            }
            else
            {
                $this->calendario_model->carrega( $result, $args );

                $data['row'] = $result->row_array();
            }

            $this->load->view('cadastro/calendario/detalhe', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar()
    {
        if(gerencia_in(array('GTI', 'GC')))
        {
            $data   = array();
            $result = null;
            $args   = array();

            $args['cd_calendario'] = $this->input->post('cd_calendario', TRUE);
            $args['dt_calendario'] = $this->input->post('dt_calendario', TRUE);
            $args['tp_calendario'] = $this->input->post('tp_calendario', TRUE);
            $args['turno']         = $this->input->post('turno', TRUE);
            $args['descricao']     = $this->input->post('descricao', TRUE);
            $args['ds_url']        = $this->input->post('ds_url', TRUE);
            $args['cd_usuario']    = $this->session->userdata('codigo');

            $this->calendario_model->salvar( $result, $args );

            redirect( "cadastro/calendario", "refresh" );
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function excluir($cd_calendario)
    {
        if(gerencia_in(array('GTI', 'GC')))
        {
            $args = array();
            $data = array();
            $result = null;

            $args['cd_calendario'] = $cd_calendario;
            $args['cd_usuario']    = $this->session->userdata('codigo');

            $this->calendario_model->excluir( $result, $args );

            redirect( 'cadastro/calendario', 'refresh' );
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

}
?>