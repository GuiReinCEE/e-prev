<?php
class Envio_extrato extends Controller {

	function __construct()
    {
        parent::Controller();
        
		CheckLogin();

		ini_set('max_execution_time', 0);
    }

	private function get_permissao()
    {
    	if(gerencia_in(array('GFC', 'GAP.', 'GTI')))
    	{
    		return TRUE;
    	}
    	else
    	{
    		return FALSE;
    	}
    }

	public function index($cd_plano = '', $cd_empresa = '', $nro_extrato = '')
    {
		if($this->get_permissao())
		{
			$data['row'] = array(
				'cd_empresa'  => $cd_empresa,
				'cd_plano'    => $cd_plano,
				'nro_extrato' => $nro_extrato
			);

			$this->load->view('planos/envio_extrato/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function listar()
    {
    	$this->load->model('projetos/extrato_envio_controle_model');

    	$args = array(
    		'cd_plano_empresa' => $this->input->post('cd_plano_empresa', TRUE),
    		'cd_plano'         => $this->input->post('cd_plano', TRUE),
    		'nro_extrato'      => $this->input->post('nro_extrato', TRUE)
    	);
				
		manter_filtros($args);
		
		$data['collection'] = $this->extrato_envio_controle_model->listar($args);

		$this->load->view('planos/envio_extrato/index_result', $data);
    }

    public function enviado()
    {
    	if($this->get_permissao())
		{
			$this->load->view('planos/envio_extrato/enviado');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function enviado_listar()
    {
    	$this->load->model('projetos/extrato_envio_controle_model');

    	$args = array(
    		'cd_plano_empresa' => $this->input->post('cd_plano_empresa', TRUE),
    		'cd_plano'         => $this->input->post('cd_plano', TRUE),
    		'nro_extrato'      => $this->input->post('nro_extrato', TRUE),
    		'dt_gerado_ini'	   => $this->input->post('dt_gerado_ini', TRUE),
    		'dt_gerado_fim'	   => $this->input->post('dt_gerado_fim', TRUE),
    		'dt_envio_ini'     => $this->input->post('dt_envio_ini', TRUE),
    		'dt_envio_fim'     => $this->input->post('dt_envio_fim', TRUE)
    	);
				
		manter_filtros($args);
		
		$data['collection'] = $this->extrato_envio_controle_model->enviado_listar($args);

		foreach($data['collection'] as $key => $item) 
		{
			/*
			$row = $this->extrato_envio_controle_model->email_enviado($item['cd_plano'], $item['cd_empresa'], $item['nr_extrato'],$args);

			$data['collection'][$key]['dt_agendado']    = $row['dt_agendado'];
			$data['collection'][$key]['qt_aguardando']  = $row['qt_aguardando'];
			$data['collection'][$key]['qt_enviado_nao'] = $row['qt_enviado_nao'];
			$data['collection'][$key]['qt_enviado']     = $row['qt_enviado'];

			if(trim($data['collection'][$key]['dt_agendado']) == '')
			{
				unset($data['collection'][$key]);
			}
			*/

			$data['collection'][$key]['dt_agendado']    = '';
			$data['collection'][$key]['qt_aguardando']  = '';
			$data['collection'][$key]['qt_enviado_nao'] = '';
			$data['collection'][$key]['qt_enviado']     = '';
		}	
		

		

		$this->load->view('planos/envio_extrato/enviado_result', $data);
    }

    public function agendar_envio($cd_plano, $cd_empresa, $nro_extrato)
    {
    	if(gerencia_in(array('GFC')))
		{
			$this->load->model('projetos/extrato_envio_controle_model');

			$data = array(
				'extrato'    => $this->extrato_envio_controle_model->get_extrato($cd_plano, $cd_empresa, $nro_extrato),
				'collection' => $this->extrato_envio_controle_model->get_sem_email($cd_plano, $cd_empresa, $nro_extrato)
			);

			$this->load->view('planos/envio_extrato/agendar_envio', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function salvar_agendamento()
    {
		exibir_mensagem('ACESSO NÃO PERMITIDO');
		exit;
    	if(gerencia_in(array('GFC')))
		{
			$this->load->model('projetos/extrato_envio_controle_model');

			$args = array(
	    		'cd_empresa'  => $this->input->post('cd_empresa', TRUE),
	    		'cd_plano'    => $this->input->post('cd_plano', TRUE),
	    		'nro_extrato' => $this->input->post('nro_extrato', TRUE),
	    		'nr_ano'      => $this->input->post('nr_ano', TRUE),
	    		'nr_mes'      => $this->input->post('nr_mes', TRUE),
	    		'dt_envio'    => $this->input->post('dt_envio', TRUE),
	    		'qt_extrato'  => $this->input->post('qt_extrato', TRUE),
	    		'cd_usuario'  => $this->session->userdata('codigo')
	    	);

	    	$this->extrato_envio_controle_model->salvar_agendamento($args);

	    	$fl_enviar_email_cadastro = $this->input->post('fl_enviar_email_cadastro', TRUE);

	    	if(trim($fl_enviar_email_cadastro) == 'S')
	    	{
	    		$sem_email = $this->extrato_envio_controle_model->get_sem_email($args['cd_plano'], $args['cd_empresa'], $args['nro_extrato']);

	    		if(count($sem_email) > 0)
	    		{
	    			$this->load->model('projetos/eventos_email_model');

	    			$cd_evento = 276;

	    			$tabela = '
	    				<table border="1" style="font-family: calibri, arial; font-size: 12pt;">
						    <tr>
						        <td align="center" style="background:yellow;"><b>RE</b></td>
						        <td align="center" style="background:yellow;"><b>Nome</b></td>
						    </tr>';

	    			foreach ($sem_email as $key => $item) 
	    			{
	    				$tabela .= '
	    					<tr>
	    						<td align="center">'.$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'].'</td>
		                        <td align="left">'.$item['nome'].'</td>
		                    </tr>';
	    			}

	    			$tabela .= '</table>'; 

	    			$email = $this->eventos_email_model->carrega($cd_evento);

	    			$tags = array('[CD_PLANO]', '[CD_EMPRESA]', '[NR_EXTRATO]');

	    			$subs = array($args['cd_plano'], $args['cd_empresa'], $args['nro_extrato']);

	    			$assunto = str_replace($tags, $subs, $email['assunto']);

	    			$tags[] = '[TABELA]';

	    			$subs[] = $tabela;

	    			$cd_usuario = $this->session->userdata('codigo');

	    			$texto = str_replace($tags, $subs, $email['email']);

	    			$args_email = array(
		                'de'      => 'Envio Extrato - Participantes sem e-mail',
		                'assunto' => $assunto,
		                'para'    => $email['para'],
		                'cc'      => $email['cc'],
		                'cco'     => $email['cco'],
		                'texto'   => $texto
		            );

		            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args_email);
	    		}
	    	}

	    	$this->load->model('meu_retrato/edicao_model');

	    	$meu_retrato = array(
	    		'cd_empresa'      => $this->input->post('cd_empresa', TRUE),
	    		'cd_plano'        => $this->input->post('cd_plano', TRUE),
	    		'dt_base_extrato' => $this->input->post('dt_base', TRUE),
	    		'tp_participante' => 'ATIV',
	    		'cd_usuario'      => $this->session->userdata('codigo')
	    	);

	    	$this->edicao_model->salvar($meu_retrato);

	    	redirect('planos/envio_extrato/extrato_enviado/'.$args['cd_plano'].'/'.$args['cd_empresa'].'/'.$args['nro_extrato'], 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function extrato_enviado($cd_plano, $cd_empresa, $nro_extrato)
    {
    	if($this->get_permissao())
		{
			$this->load->model('projetos/extrato_envio_controle_model');

			$data['extrato'] = $this->extrato_envio_controle_model->get_extrato_enviado($cd_plano, $cd_empresa, $nro_extrato);

			$data['collection'] = $this->extrato_envio_controle_model->get_participantes_enviados($cd_plano, $cd_empresa, $nro_extrato);

			$args = array(
				'dt_envio_ini' => '',
				'dt_envio_fim' => ''
			);

			$row = $this->extrato_envio_controle_model->email_enviado(
				$data['extrato']['cd_plano'], 
				$data['extrato']['cd_empresa'], 
				$data['extrato']['nr_extrato'],
				$args
			);

			$data['extrato']['dt_agendado']    = $row['dt_agendado'];
			$data['extrato']['qt_aguardando']  = $row['qt_aguardando'];
			$data['extrato']['qt_enviado_nao'] = $row['qt_enviado_nao'];
			$data['extrato']['qt_enviado']     = $row['qt_enviado'];

			$this->load->view('planos/envio_extrato/extrato_enviado', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}	
    }

}