<?php
class Rh_aviso extends Controller
{	
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao()
    {
    	if($this->session->userdata('indic_09') == '*')
        {
            return TRUE;
        }
        else if($this->session->userdata('indic_05') == 'S')
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    private function get_periodicidade()
    {
    	return array(
			array('text' => 'Selecione', 'value' => ''),
			array('text' => 'Eventual',  'value' => 'E'),
			array('text' => 'Diário',    'value' => 'D'),
			array('text' => 'Semanal',   'value' => 'S'),
			array('text' => 'Mensal',    'value' => 'M'),
			array('text' => 'Anual',     'value' => 'A')
        );	
    }

    public function index()
    {	
		if($this->get_permissao())
		{
			$data['periodicidade'] = $this->get_periodicidade();

			$this->load->view('cadastro/rh_aviso/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
    }

	public function listar()
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/rh_aviso_model');	

			$args['cd_periodicidade'] = $this->input->post('cd_periodicidade', TRUE);
			
			manter_filtros($args);

			$data['collection'] = $this->rh_aviso_model->listar(
				$this->session->userdata('codigo'),
				$args
			);

			$this->load->view('cadastro/rh_aviso/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
	}
	
    public function cadastro()
    {	
		if($this->get_permissao())
		{
			$this->load->model('projetos/rh_aviso_model');

			$data = array(
				'usuario'       => $this->rh_aviso_model->get_usuario(),
				'periodicidade' => $this->get_periodicidade()
			);

			$this->load->view("cadastro/rh_aviso/cadastro", $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
    }

	public function salvar()
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/rh_aviso_model');

			$args = array(
				'ds_descricao'           => $this->input->post('ds_descricao', TRUE),
				'cd_periodicidade'       => $this->input->post('cd_periodicidade', TRUE),
				'qt_dia'                 => $this->input->post('qt_dia', TRUE),
				'dt_referencia'          => $this->input->post('dt_referencia', TRUE),
				'cd_usuario_conferencia' => $this->input->post('cd_usuario_conferencia', TRUE),
				'cd_usuario'             => $this->session->userdata('codigo')
			);

			$usuario = $this->input->post('usuario', TRUE);

            if(!is_array($usuario))
			{
				$args['usuario'] = array();
			}
			else
			{
				$args['usuario'] = $usuario;
			}
			
			$cd_rh_aviso = $this->rh_aviso_model->salvar($args);

			if(intval($args['cd_usuario_conferencia']) > 0)
			{
				$this->enviar_email_conferencia($cd_rh_aviso);
			}

			redirect('cadastro/rh_aviso', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
	}	

	public function enviar_email_conferencia($cd_rh_aviso)
	{
		$this->load->model(array(
            'projetos/rh_aviso_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 319;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $row = $this->rh_aviso_model->carrega($cd_rh_aviso);  

        $tags = array('[DS_DESCRICAO]', '[LINK]');

        $subs = array(
            $row['ds_descricao'],
            site_url('cadastro/rh_aviso')
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Aviso RH - Conferência',
            'assunto' => $email['assunto'],
            'para'    => $row['ds_email'],  
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);  
	}
		
    public function excluir($cd_rh_aviso = 0)
    {	
		if($this->get_permissao())
		{
			$this->load->model('projetos/rh_aviso_model');
			
			$this->rh_aviso_model->excluir(intval($cd_rh_aviso), $this->session->userdata('codigo'));

			redirect('cadastro/rh_aviso', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
    }	

    public function confirmar($cd_rh_aviso = 0)
    {	
		if($this->get_permissao())
		{
			$this->load->model('projetos/rh_aviso_model');
			
			$this->rh_aviso_model->confirmar(intval($cd_rh_aviso), $this->session->userdata('codigo'));

			$this->enviar_email_conferido($cd_rh_aviso);

			redirect('cadastro/rh_aviso', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
    }	

    public function enviar_email_conferido($cd_rh_aviso)
	{
		$this->load->model(array(
            'projetos/rh_aviso_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 320;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $row = $this->rh_aviso_model->carrega($cd_rh_aviso);  

        $tags = array('[DS_DESCRICAO]', '[LINK]');

        $subs = array(
            $row['ds_descricao'],
            site_url('cadastro/rh_aviso')
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Aviso RH - Conferência',
            'assunto' => $email['assunto'],
            'para'    => $row['ds_email_inclusao'],  
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);  
	}
		
    public function verificar($cd_rh_aviso_verificacao = 0)
    {	
		if($this->get_permissao())
		{
			$this->load->model('projetos/rh_aviso_model');

			$data['row'] = $this->rh_aviso_model->verificar(intval($cd_rh_aviso_verificacao));
			
			$this->load->view('cadastro/rh_aviso/verificar', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
    }	
	
	public function verificar_salvar()
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/rh_aviso_model');
			
            $cd_rh_aviso_verificacao = $this->input->post('cd_rh_aviso_verificacao', TRUE);

			$this->rh_aviso_model->verificar_salvar(
				$this->input->post('cd_rh_aviso_verificacao', TRUE), 
				$this->session->userdata('codigo')
			);

			redirect('cadastro/rh_aviso/verificar/'.intval($cd_rh_aviso_verificacao), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
	}	

	public function historico($cd_rh_aviso)
    {	
		if($this->get_permissao())
		{
			$this->load->model('projetos/rh_aviso_model');
			
			$data = array(
				'row'        => $this->rh_aviso_model->carrega($cd_rh_aviso),
				'collection' => $this->rh_aviso_model->listar_verificar($cd_rh_aviso)
			);
			
			$this->load->view('cadastro/rh_aviso/historico', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
    }
}