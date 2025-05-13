<?php
class sistema extends Controller
{
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        
        $this->load->model('projetos/projetos_model');
    }

    function index()
    {
	$this->load->view('cadastro/sistema/index.php');
    }

    function listar()
    {
        $data['collection'] = array();
        $result = null;
        $args=array();
        
        $args['tipo_usuario']    = $this->session->userdata('tipo');
        $args['usuario']         = $this->session->userdata('usuario');
        $args['divisao_usuario'] = $this->session->userdata('divisao');
        $args['tipo']            = 'S';

        $this->projetos_model->listar( $result, $args );

        $data['collection'] = $result->result_array();

        $this->load->view('cadastro/sistema/partial_result', $data);
    }

    function detalhe($cd=0)
    {
        $args = array();
        $data = array();
        $result = null;
        $data['acompanhamento'] = array();
        
        $args['codigo'] = $cd;
        
        
        $this->projetos_model->areas( $result, $args );
        $data['arr_areas'] = $result->result_array();
        
        $this->projetos_model->analistas( $result, $args );
        $data['arr_analistas'] = $result->result_array();
        
        $this->projetos_model->atendentes( $result, $args );
        $data['arr_atendentes'] = $result->result_array();
        
        $this->projetos_model->responsaveis( $result, $args );
        $data['arr_responsaveis'] = $result->result_array();
        
        $this->projetos_model->projetos( $result, $args );
        $data['arr_projetos'] = $result->result_array();
        
        $this->projetos_model->niveis( $result, $args );
        $data['arr_niveis'] = $result->result_array();
        
        $this->projetos_model->institucionais( $result, $args );
        $data['arr_institucionais'] = $result->result_array();
        
        $this->projetos_model->diretrizes( $result, $args );
        $data['arr_diretrizes'] = $result->result_array();
        
        if(intval($args['codigo']) == 0)
        {   
            $data['row'] = array(
              'codigo'                 => 0,
              'nome'                   => '',
              'descricao'              => '',
              'area'                   => '',
              'nivel'                  => '',
              'administrador1'         => '',
              'administrador2'         => '',
              'atendente'              => '',
              'cod_projeto_superior'   => '',
              'analista_responsavel'   => '',
              'diretriz'               => '',
              'programa_institucional' => '',
              'data_implantacao'       => '',
              'tipo'                   => '',
              'fl_atividade'           => 'S'
            );
        }
        else
        {
            $this->projetos_model->carrega( $result, $args );
            $data['row'] = $result->row_array();
            
            $this->projetos_model->acompanhamento( $result, $args );
            $data['acompanhamento'] = $result->row_array();
        }
                
        $this->load->view('cadastro/sistema/detalhe', $data);
    }
    
    function salvar()
    {
        $args = array();
        $data = array();
        $result = null;
        
        $args['salva']                  = $this->input->post('salva', TRUE);
        $args['codigo']                 = $this->input->post('codigo', TRUE);
        $args['nome']                   = $this->input->post('nome', TRUE);
        $args['descricao']              = $this->input->post('descricao', TRUE);
        $args['area']                   = $this->input->post('area', TRUE);
        $args['nivel']                  = $this->input->post('nivel', TRUE);
        $args['administrador1']         = $this->input->post('administrador1', TRUE);
        $args['administrador2']         = $this->input->post('administrador2', TRUE);
        $args['atendente']              = $this->input->post('atendente', TRUE);
        $args['diretriz']               = $this->input->post('diretriz', TRUE);
        $args['data_implantacao']       = $this->input->post('data_implantacao', TRUE);
        $args['cod_projeto_superior']   = $this->input->post('cod_projeto_superior', TRUE);
        $args['tipo']                   = $this->input->post('tipo', TRUE);
        $args['analista_responsavel']   = $this->input->post('analista_responsavel', TRUE);
        $args['programa_institucional'] = $this->input->post('programa_institucional', TRUE);
        $args['fl_atividade']           = $this->input->post('fl_atividade', TRUE);
        $args['cd_usuario']             = $this->session->userdata('codigo');

        $this->projetos_model->salvar( $result, $args );
        
        redirect( "cadastro/sistema", "refresh" );
    }
    
    function listar_envolvidos()
    {
        $args = array();
        $data = array();
        $result = null;
        
        $args['codigo'] = $this->input->post('codigo', TRUE);
        
        $this->projetos_model->lista_pessoas_envolvidas( $result, $args );
        
        $data['collection'] = $result->result_array();

        $this->load->view('cadastro/sistema/pessoas_envolvidas_result', $data);
    }
    
    function excluir($codigo)
    {
        $args = array();
        $data = array();
        $result = null;

        $args['codigo']     = $codigo;
        $args['cd_usuario'] = $this->session->userdata('codigo');

        $this->projetos_model->excluir( $result, $args );

        redirect( 'cadastro/sistema', 'refresh' );

    }
    
    function excluir_envolvido($codigo, $cd_envolvido)
    {
        $args = array();
        $data = array();
        $result = null;

        $args['cd_envolvido'] = $cd_envolvido;
        $args['cd_usuario']   = $this->session->userdata('codigo');

        $this->projetos_model->excluir_envolvido( $result, $args );

        redirect( 'cadastro/sistema/detalhe/'. $codigo, 'refresh' );
    }
    
    function adicionar_pessoas($codigo)
    {
        $args = array();
        $data = array();
        $result = null;

        $args['codigo'] = $codigo;
        $data['codigo'] = $codigo;
        
        $this->projetos_model->lista_pessoas_envolvidas( $result, $args );
        
        $data['collection'] = $result->result_array();

        $this->load->view('cadastro/sistema/pessoas_envolvidas', $data);
    }
    
    function salvar_envolvido()
    {
        $args = array();
        $data = array();
        $result = null;
        
        $args['codigo']       = $this->input->post('codigo', TRUE);
        $args['cd_envolvido'] = $this->input->post('usuario', TRUE);
        $args['cd_usuario']   = $this->session->userdata('codigo');
        
        $this->projetos_model->salvar_envolvido( $result, $args );
                
        redirect( 'cadastro/sistema/adicionar_pessoas/'. $args['codigo'], 'refresh' );
        
    }
}
