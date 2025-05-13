<?php
class Expediente extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao()
    {
    	#Lucio Daniel Sartori
    	if($this->session->userdata('codigo') == 415)
    	{
    		return TRUE;
    	}
    	#Vanessa Silva Alves
    	else if($this->session->userdata('codigo') == 424)
    	{
    		return TRUE;
    	}
    	#Regis Rodrigues da Silveira
    	else if($this->session->userdata('codigo') == 411)
    	{
    		return TRUE;
    	}
    	#Bruna Gomes
        else if($this->session->userdata('codigo') == 497)
        {
            return true;
        }
    	else if($this->session->userdata('codigo') == 251)
    	{
    		return TRUE;
    	}
    	else
    	{
    		return FALSE;
    	}
    }

    public function index()
    {	
		if($this->get_permissao())	
		{
			$this->load->view('cadastro/expediente/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
    }

	public function listar()
	{
		$this->load->model('comite_etica/expediente_model');

		$args = array(
			'dt_ini' => $this->input->post('dt_ini', TRUE),
			'dt_fim' => $this->input->post('dt_fim', TRUE)
		);
		
		manter_filtros($args);

		$data['collection'] = $this->expediente_model->listar($args);

		$this->load->view('cadastro/expediente/index_result', $data);
	}
	
    public function cadastro($cd_expediente = 0)
    {	
		if($this->get_permissao())
		{
            if(intval($cd_expediente) == 0)
            {
                $data['row'] = array(
					'cd_expediente'        => 0,
					'nr_expediente'        => 0,
					'dt_inclusao'          => '',
					'dt_alteracao'         => '',
					'dt_conclusao'         => '',
					'ds_descricao'         => '',
					'cd_expediente_origem' => 0,
					'cd_expediente_status' => 0,
					'ds_expediente_status' => '',
					'dt_envio_comite'      => ''
                );
            }
            else
            {
            	$this->load->model('comite_etica/expediente_model');

                $data['row'] = $this->expediente_model->carrega($cd_expediente);
            }

            $this->load->view('cadastro/expediente/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
    }

    public function salvar()
    {
		if($this->get_permissao())
		{        
			$this->load->model('comite_etica/expediente_model');

			$cd_expediente = $this->input->post('cd_expediente', TRUE);

			$args = array(
				'nr_expediente'        => $this->input->post('nr_expediente', TRUE),
				'ds_descricao'         => $this->input->post('ds_descricao', TRUE),
				'cd_expediente_origem' => $this->input->post('cd_expediente_origem', TRUE),
				'cd_usuario'           => $this->session->userdata('codigo')
			);
			
			if(intval($cd_expediente) == 0)
			{	
				$this->expediente_model->salvar($args);

				redirect('cadastro/expediente', 'refresh');
			}
			else
			{
				$this->expediente_model->atualizar($cd_expediente, $args);

				redirect('cadastro/expediente/cadastro/'.intval($cd_expediente), 'refresh');
			}
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
    }	

    public function concluir($cd_expediente)
    {
		if($this->get_permissao())
		{        
			$this->load->model('comite_etica/expediente_model');

			$this->expediente_model->concluir(intval($cd_expediente), $this->session->userdata('codigo'));

			redirect('cadastro/expediente/cadastro/'.intval($cd_expediente), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
    }	
	
    public function enviar_email($cd_expediente)
    {
    	if($this->get_permissao())
		{        
			$this->load->model(array(
				'comite_etica/expediente_model',
				'projetos/eventos_email_model'
			));

			$cd_evento = 122;

			$email = $this->eventos_email_model->carrega($cd_evento);

			$row = $this->expediente_model->carrega($cd_expediente);

			$anexo = $this->expediente_model->anexo_listar($cd_expediente);

			$quebra = chr(10);
 
			$texto = 'Registro: '.$row['nr_expediente']. $quebra; 
			$texto .= '----------------------------------------------- '.$quebra.$quebra;
			$texto .= 'Descrição do contato: '.$quebra.$row['ds_descricao'].$quebra.$quebra;
			$texto .= '----------------------------------------------- '.$quebra;		

			if(intval($anexo) > 0)
			{
				$texto .= 'Anexos '.$quebra.$quebra;

				foreach ($anexo as $item)
				{
					$texto .= $item['ds_arquivo_nome'].' : https://www.e-prev.com.br/cieprev/up/expediente/'.$item['ds_arquivo'].$quebra.$quebra;
				}
			}

			$this->expediente_model->enviar_email(intval($cd_expediente), $this->session->userdata('codigo'));

			$cd_usuario = $this->session->userdata('codigo');

			$args = array(
				'de'      => 'Comitê de Ética',
				'assunto' => $email['assunto'],
				'para'    => $email['para'],
				'cc'      => $email['cc'],
				'cco'     => $email['cco'],
				'texto'   => $texto
			);

			$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

			redirect('cadastro/expediente/cadastro/'.intval($cd_expediente), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
    }

    public function andamento($cd_expediente)
    {	
		if($this->get_permissao())
		{
            $this->load->model('comite_etica/expediente_model');

            $data = array(
            	'expediente' => $this->expediente_model->carrega($cd_expediente),
            	'andamento'  => $this->expediente_model->andamento_listar($cd_expediente)
            );
			
            $this->load->view('cadastro/expediente/andamento', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
    }	
	
    public function andamento_salvar()
    {
		if($this->get_permissao())
		{        
			$this->load->model(array(
				'comite_etica/expediente_model',
				'projetos/eventos_email_model'
			));

			$cd_evento = 122;

			$cd_expediente = $this->input->post('cd_expediente', TRUE);

			$email = $this->eventos_email_model->carrega($cd_evento);

			$row = $this->expediente_model->carrega($cd_expediente);

			$args = array(
				'ds_expediente_andamento' => $this->input->post('ds_expediente_andamento', TRUE),
				'cd_expediente_status'    => $this->input->post('cd_expediente_status', TRUE),
				'cd_usuario'              => $this->session->userdata('codigo')
			);

			$this->expediente_model->andamento_salvar($cd_expediente, $args);

			$andamento = $this->expediente_model->carrega($cd_expediente);

			$quebra = chr(10);
 
			$texto = 'Registro: '.$row['nr_expediente']. $quebra; 
			$texto .= '----------------------------------------------- '.$quebra.$quebra;
			$texto .= 'Descrição do contato: '.$quebra.$row['ds_descricao'].$quebra.$quebra;
			$texto .= '----------------------------------------------- '.$quebra;	
			$texto .= 'Descrição do andamento: '.$quebra.$args['ds_expediente_andamento'].$quebra.$quebra;	


			$cd_usuario = $this->session->userdata('codigo');

			$args = array(
				'de'      => 'Comitê de Ética',
				'assunto' => $email['assunto'],
				'para'    => $email['para'],
				'cc'      => $email['cc'],
				'cco'     => $email['cco'],
				'texto'   => $texto
			);

			$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
			
			redirect('cadastro/expediente/andamento/'.intval($cd_expediente), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
    }	
	
    public function anexo($cd_expediente)
    {	
		if($this->get_permissao())
		{
            $this->load->model('comite_etica/expediente_model');

            $data = array(
            	'expediente' => $this->expediente_model->carrega($cd_expediente),
            	'collection' => $this->expediente_model->anexo_listar($cd_expediente)
            );
			
            $this->load->view('cadastro/expediente/anexo', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
    }
	
	public function anexo_salvar()
	{
		if($this->get_permissao())
		{ 
			$this->load->model('comite_etica/expediente_model');

			$cd_expediente = $this->input->post('cd_expediente', TRUE);

			$qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));
			
			if($qt_arquivo > 0)
			{
				$nr_conta = 0;

				while($nr_conta < $qt_arquivo)
				{
					$args = array();		
					
					$args = array(
						'arquivo_nome' => $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE),
						'arquivo'      => $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE),
						'cd_usuario'   => $this->session->userdata('codigo')
					);

					$this->expediente_model->anexo_salvar($cd_expediente, $args);
					
					$nr_conta++;
				}
			}		
			
			redirect('cadastro/expediente/anexo/'.intval($cd_expediente), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
	}	
}