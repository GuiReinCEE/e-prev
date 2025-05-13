<?php
class Meus_treinamentos extends Controller {

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    public function index($fl_certificado = '')
    {
		$this->load->model('projetos/meus_treinamentos_model');
		
        $data = array(
            'tipo'           => $this->meus_treinamentos_model->get_tipo(),
            'fl_certificado' => trim($fl_certificado)
        );
		
		$this->load->view('servico/meus_treinamentos/index', $data);
    }

    public function listar()
    {
    	$this->load->model('projetos/meus_treinamentos_model');

    	$args = array(
    		'numero'                          => $this->input->post('numero', TRUE),
    		'ano'                             => $this->input->post('ano', TRUE),
    		'nome'                            => $this->input->post('nome', TRUE),
    		'dt_inicio_ini'                   => $this->input->post('dt_inicio_ini', TRUE),
    		'dt_inicio_fim'                   => $this->input->post('dt_inicio_fim', TRUE),
    		'dt_final_ini'                    => $this->input->post('dt_final_ini', TRUE),
    		'dt_final_fim'                    => $this->input->post('dt_final_fim', TRUE),
            'cd_treinamento_colaborador_tipo' => $this->input->post('cd_treinamento_colaborador_tipo', TRUE),
    		'fl_certificado'                  => $this->input->post('fl_certificado', TRUE)
    	);

		manter_filtros($args);
        
        $data['collection'] = $this->meus_treinamentos_model->listar($this->session->userdata('cd_registro_empregado'), $args);
		
		$this->load->view('servico/meus_treinamentos/index_result', $data);
    }

    public function anexo($cd_treinamento_colaborador_item)
    { 
        $this->load->model('projetos/meus_treinamentos_model');

        $data = array(
            'row' => $this->meus_treinamentos_model->carrega(intval($cd_treinamento_colaborador_item))
        );

        $this->load->view('servico/meus_treinamentos/anexo', $data);
    }

    public function salvar_anexo()
    {
        $this->load->model('projetos/meus_treinamentos_model');

        $cd_treinamento_colaborador_item = $this->input->post('cd_treinamento_colaborador_item', TRUE);
        
        $args = array(
            'fl_certificado'   => $this->input->post('fl_certificado', TRUE),
            'arquivo'          => $this->input->post('arquivo', TRUE),
            'arquivo_nome'     => $this->input->post('arquivo_nome', TRUE),
            'ds_justificativa' => $this->input->post('ds_justificativa', TRUE),
            'cd_usuario'       => $this->session->userdata('codigo')
        ); 

        $this->meus_treinamentos_model->salvar_anexo($cd_treinamento_colaborador_item, $args); 
        
        redirect('servico/meus_treinamentos/index');
    }   

    public function documento($cd_treinamento_colaborador_item)
    {
        $this->load->model('projetos/meus_treinamentos_model');

        $data = array(
            'row'        => $this->meus_treinamentos_model->carrega(intval($cd_treinamento_colaborador_item)),
            'collection' => $this->meus_treinamentos_model->listar_documento(intval($cd_treinamento_colaborador_item))
        );

       $this->load->view('servico/meus_treinamentos/documento', $data);
    }

    public function salvar_documento()
    {
        $this->load->model('projetos/meus_treinamentos_model');

        $cd_treinamento_colaborador_item = $this->input->post('cd_treinamento_colaborador_item', TRUE);

        $qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));

        if($qt_arquivo > 0)
        {
            $nr_conta = 0;

            while($nr_conta < $qt_arquivo)
            {
                $args = array();        

                $args['cd_treinamento_colaborador'] = $this->input->post('cd_treinamento_colaborador', TRUE);              
                $args['arquivo_nome']               = $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE);
                $args['arquivo']                    = $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE);
                $args['cd_usuario']                 = $this->session->userdata('codigo');      
                
                $this->meus_treinamentos_model->salvar_documento(intval($cd_treinamento_colaborador_item), $args);
                
                $nr_conta++;
            }
        }

        $this->enviar_email($cd_treinamento_colaborador_item);

        redirect('servico/meus_treinamentos/documento/'.intval($cd_treinamento_colaborador_item), 'refresh');
    }
    
    public function excluir_documento($cd_treinamento_colaborador_item, $cd_treinamento_colaborador_documento)
    {
        $this->load->model('projetos/meus_treinamentos_model');

        $this->meus_treinamentos_model->excluir_documento(
            intval($cd_treinamento_colaborador_documento), 
            $this->session->userdata('codigo')
        );

        redirect('servico/meus_treinamentos/documento/'.intval($cd_treinamento_colaborador_item), 'refresh');
    }

    public function enviar_email($cd_treinamento_colaborador_item)
    {
        $this->load->model(array(
            'projetos/meus_treinamentos_model',
            'projetos/eventos_email_model'
        ));
        
        $cd_evento = 337;
        
        $email = $this->eventos_email_model->carrega($cd_evento);

        $cd_usuario = $this->session->userdata('codigo');

        $row = $this->meus_treinamentos_model->carrega(intval($cd_treinamento_colaborador_item));   

        $tags = array('[DS_USUARIO]', '[DS_TREINAMENTO]', '[LINK]');

        $subs = array($this->session->userdata('nome'), $row['nome'], site_url('servico/treinamentos_documento/documento/'.intval($row['cd_treinamento_colaborador'])));

        $texto = str_replace($tags, $subs, $email['email']);
        
        $args = array(
            'de'      => 'Treinamentos',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );
        
        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }
}