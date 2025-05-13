<?php
class Pergunta_resposta extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    public function index()
    {
        $this->load->view('cadastro/pergunta_resposta/index');
    }

    public function listar()
    {
        $this->load->model('projetos/pergunta_resposta_model');

        $args = array();

        manter_filtros($args);

        $data['collection'] = $this->pergunta_resposta_model->listar($this->session->userdata('codigo'), $args);

        $this->load->view('cadastro/pergunta_resposta/index_result', $data);
    }

    public function cadastro($cd_pergunta_resposta = 0)
    {
        $this->load->model('projetos/pergunta_resposta_model');

        $data = array(
            'gerencia'   => $this->pergunta_resposta_model->get_gerencia(),
            'cd_usuario' => $this->session->userdata('codigo'),
            'usuarios'   => array()
        );

        if ($cd_pergunta_resposta == 0)
        {
            $data['row'] = array(
                'cd_pergunta_resposta'     => '',
                'cd_gerencia_responsavel'  => '',
                'cd_usuario_responsavel'   => '',
                'fl_usuario_rh'            => 0,
                'ds_usuario_responsavel'   => '',
                'ds_pergunta'              => '',
                'dt_encaminha_responsavel' => '',
                'dt_resposta'              => ''
            );
        } 
        else 
        {
            $data['row'] = $this->pergunta_resposta_model->carrega($cd_pergunta_resposta, $this->session->userdata('codigo'));
        }

        $this->load->view('cadastro/pergunta_resposta/cadastro', $data);
    }

    public function get_usuarios()
    {
        $this->load->model('projetos/pergunta_resposta_model');

        $cd_gerencia_responsavel = $this->input->post('cd_gerencia_responsavel', TRUE);

        $usuarios = $this->pergunta_resposta_model->get_usuario_responsavel($cd_gerencia_responsavel);

        $row = array();
        
		foreach ($usuarios as $item)
		{
			$row[] = array_map("arrayToUTF8", $item);		
		}
		
		echo json_encode($row);
    }

    public function salvar()
    {
        $this->load->model('projetos/pergunta_resposta_model');

        $cd_pergunta_resposta = $this->input->post('cd_pergunta_resposta', TRUE);

        $args = array(
            'nr_pergunta_resposta' => $this->input->post('nr_pergunta_resposta', TRUE),
            'nr_ano'               => $this->input->post('nr_ano', TRUE),
            'ds_pergunta'          => $this->input->post('ds_pergunta', TRUE),
            'cd_usuario'           => $this->session->userdata('codigo') 
        );

        if ($cd_pergunta_resposta == 0)
        {
            $cd_pergunta_resposta = $this->pergunta_resposta_model->salvar($args);

            $this->email_cadastro_pergunta($cd_pergunta_resposta);
        }
        else 
        {
            $this->pergunta_resposta_model->atualizar($cd_pergunta_resposta, $args);
        }
        
        redirect('cadastro/pergunta_resposta/cadastro/'.$cd_pergunta_resposta, 'refresh');
    }

    private function email_cadastro_pergunta($cd_pergunta_resposta)
    {
        $this->load->model(array(
            'projetos/pergunta_resposta_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 358;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $tags = '[LINK]';

        $subs = site_url('cadastro/pergunta_resposta/cadastro/'.intval($cd_pergunta_resposta));

        $texto = str_replace($tags, $subs, $email['email']);
		
		$args = array(
			'de'      => 'Perguntas e Respostas',
			'assunto' => $email['assunto'],
			'para'    => $email['para'],
			'cc'      => $email['cc'],
			'cco'     => $email['cco'],
			'texto'   => $texto
        );
        
        $cd_usuario = $this->session->userdata('codigo');
		
		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function salvar_responsavel()
    {
        $this->load->model('projetos/pergunta_resposta_model');

        $args = array(
            'cd_pergunta_resposta'    => $this->input->post('cd_pergunta_resposta', TRUE),
            'cd_gerencia_responsavel' => $this->input->post('cd_gerencia_responsavel', TRUE),
            'cd_usuario_responsavel'  => $this->input->post('cd_usuario_responsavel', TRUE),
            'cd_usuario'              => $this->session->userdata('codigo')
        );

        $this->pergunta_resposta_model->salvar_responsavel($args);

        $this->email_cadastro_responsavel($args['cd_pergunta_resposta']);

        redirect('cadastro/pergunta_resposta', 'refresh');
    }

    private function email_cadastro_responsavel($cd_pergunta_resposta)
    {
        $this->load->model(array(
            'projetos/pergunta_resposta_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 359;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $row = $this->pergunta_resposta_model->carrega($cd_pergunta_resposta, $this->session->userdata('codigo'));

        $tags = '[LINK]';

        $subs = site_url('cadastro/pergunta_resposta/cadastro/'.intval($cd_pergunta_resposta));

        $texto = str_replace($tags, $subs, $email['email']);
		
		$args = array(
			'de'      => 'Perguntas e Respostas - Encaminhada',
			'assunto' => $email['assunto'],
			'para'    => $row['ds_usuario_responsavel_email'].'@eletroceee.com.br',
			'cc'      => $email['cc'],
			'cco'     => $email['cco'],
			'texto'   => $texto
        );
        
        $cd_usuario = $this->session->userdata('codigo');
		
		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function salvar_resposta()
    {
        $this->load->model('projetos/pergunta_resposta_model');

        $args = array(
            'cd_pergunta_resposta' => $this->input->post('cd_pergunta_resposta', TRUE),
            'ds_resposta'          => $this->input->post('ds_resposta', TRUE),
            'cd_usuario'           => $this->session->userdata('codigo') 
        );

        $this->pergunta_resposta_model->salvar_resposta($args);

        $this->email_cadastro_resposta($args['cd_pergunta_resposta']);

        redirect('cadastro/pergunta_resposta', 'refresh');
    }

    private function email_cadastro_resposta($cd_pergunta_resposta)
    {
        $this->load->model(array(
            'projetos/pergunta_resposta_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 360;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $row = $this->pergunta_resposta_model->carrega($cd_pergunta_resposta, $this->session->userdata('codigo'));

        $tags = '[LINK]';

        $subs = site_url('cadastro/pergunta_resposta/cadastro/'.intval($cd_pergunta_resposta));

        $texto = str_replace($tags, $subs, $email['email']);
		
		$args = array(
			'de'      => 'Perguntas e Respostas - Respondido',
			'assunto' => $email['assunto'],
			'para'    => $row['ds_usuario_inclusao_email'].'@eletroceee.com.br;'.$row['ds_usuario_responsavel_email'].'@eletroceee.com.br',
			'cc'      => $email['cc'],
			'cco'     => $email['cco'],
			'texto'   => $texto
        );
        
        $cd_usuario = $this->session->userdata('codigo');
		
		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }
}