<?php
class Sg_correspondencia extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    public function index()
    {
    	CheckLogin();

		$this->load->view('cadastro/sg_correspondencia/index');
    }

    public function listar()
    {
    	CheckLogin();

		$this->load->model('projetos/correspondencias_model');

		$args = array(
			'ano'               => $this->input->post('ano', TRUE),
			'numero'            => $this->input->post('numero', TRUE),
			'data_ini'          => $this->input->post('data_ini', TRUE),
			'data_fim'          => $this->input->post('data_fim', TRUE),
			'solicitante'       => $this->input->post('solicitante', TRUE),
			'assinatura'        => $this->input->post('assinatura', TRUE),
			'destinatario_nome' => $this->input->post('destinatario_nome', TRUE),
			'cd_gerencia'       => $this->session->userdata('divisao')
		);
		
		manter_filtros($args);

		$data['collection'] = $this->correspondencias_model->listar($args);

		$this->load->view('cadastro/sg_correspondencia/index_result', $data);   	
	}

	public function cadastro($cd_correspondencia = 0)
	{
		CheckLogin();

		$this->load->model('projetos/correspondencias_model');

		$permissao = true;

		if(intval($cd_correspondencia) == 0)
		{
			$data['row'] = array(
				'cd_correspondencia'    => 0,
				'ano'                   => '',
				'numero'                => '',
				'divisao'               => $this->session->userdata('divisao'),
				'solicitante_emp'       => $this->session->userdata('cd_patrocinadora'),
				'solicitante_re'        => $this->session->userdata('cd_registro_empregado'),
				'solicitante_seq'       => 0,
				'solicitante_nome'      => '',
				'assinatura_emp'        => '',
				'assinatura_re'         => '',
				'assinatura_seq'        => '',
				'assinatura_nome'       => '',
				'cd_empresa'            => '',
				'cd_registro_empregado' => '',
				'seq_dependencia'       => '',
				'destinatario_nome'     => '',
				'assunto'               => '',
				'cd_usuario_inclusao'   => '',
				'fl_restrito'           => 'N',
				'data'                  => date("d/m/Y")
			);

			$data['gerencia'] = $this->correspondencias_model->get_gerencia();
		}
		else
		{
			$data['row'] = $this->correspondencias_model->carrega($cd_correspondencia);

			if(trim($data['row']['fl_restrito']) == 'S' AND $this->session->userdata('divisao') != trim($data['row']['cd_gerencia_inclusao']))
			{
				$permissao = false;
			}

			$data['gerencia'] = $this->correspondencias_model->get_gerencia_anteriores($data['row']['divisao']);
		}
		
		if($permissao)
		{
			$this->load->view('cadastro/sg_correspondencia/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar()
	{
		CheckLogin();

		$this->load->model('projetos/correspondencias_model');

		$cd_correspondencia = $this->input->post('cd_correspondencia', TRUE);

		$args = array(
			'divisao'               => $this->input->post('divisao',TRUE),
			'solicitante_nome'      => $this->input->post('solicitante_nome',TRUE),
			'solicitante_emp'       => $this->input->post('solicitante_emp',TRUE),
			'solicitante_re'        => $this->input->post('solicitante_re',TRUE),
			'solicitante_seq'       => $this->input->post('solicitante_seq',TRUE),
			'assinatura_nome'       => $this->input->post('assinatura_nome',TRUE),
			'assinatura_emp'        => $this->input->post('assinatura_emp',TRUE),
			'assinatura_re'         => $this->input->post('assinatura_re',TRUE),
			'assinatura_seq'        => $this->input->post('assinatura_seq',TRUE),
			'cd_empresa'            => $this->input->post('cd_empresa',TRUE),
			'cd_registro_empregado' => $this->input->post('cd_registro_empregado',TRUE),
			'seq_dependencia'       => $this->input->post('seq_dependencia',TRUE),
			'destinatario_emp'      => $this->input->post('destinatario_emp',TRUE),
			'destinatario_re'       => $this->input->post('destinatario_re',TRUE),
			'destinatario_seq'      => $this->input->post('destinatario_seq',TRUE),
			'destinatario_nome'     => $this->input->post('destinatario_nome',TRUE),
			'assunto'               => $this->input->post('assunto',TRUE),
			'data'                  => $this->input->post('data',TRUE),
			'fl_restrito'           => $this->input->post('fl_restrito',TRUE),
			'cd_usuario'            => $this->session->userdata('codigo')
		);

		if(intval($cd_correspondencia) == 0)
		{
			$cd_correspondencia = $this->correspondencias_model->salvar($args);
		}
		else
		{
			$this->correspondencias_model->atualizar($cd_correspondencia, $args);
		}
		
		redirect('cadastro/sg_correspondencia/cadastro/'.intval($cd_correspondencia), 'refresh');
	}

	public function excluir($cd_correspondencia)
	{
		CheckLogin();

		$this->load->model('projetos/correspondencias_model');
		
		$this->correspondencias_model->excluir($cd_correspondencia, $this->session->userdata('codigo'));
		
		redirect('cadastro/sg_correspondencia', 'refresh');
	}
	
    public function integracao($ds_usuario, $ds_diretoria, $ds_nome, $ds_assunto)
    {
		#### INTEGRAÇÃO ELETRO ####
		#http://10.63.255.222/cieprev/index.php/cadastro/sg_correspondencia/integracao/LRODRIGUEZ/PRE/JOAO%20PAULO%20DUTRA%20SIMAS/RESGATE
		#echo "USUARIO:$usuario|DIRETOR:$diretoria|NOME:$nome|ASSUNTO:$assunto";
		$this->load->model('projetos/correspondencias_model');
		
		$usuario = $this->correspondencias_model->get_usuario($ds_usuario);
	
		$args['cd_correspondencia'] = 0;
		$args['cd_usuario']         = $usuario['codigo'];
		$args['divisao']            = $usuario['divisao'];
		$args['solicitante_nome']   = $usuario['nome'];
		$args['solicitante_emp']    = $usuario['cd_patrocinadora'];
		$args['solicitante_re']     = $usuario['cd_registro_empregado'];
		$args['solicitante_seq']    = 0;
		
		if(in_array(trim($ds_diretoria), array('PREV', 'PRE')))
		{
			if(trim($ds_diretoria) == 'PREV')
			{
				$ds_diretoria = 'SEG';
			}

			$diretor = $this->correspondencias_model->get_re_diretor(trim($ds_diretoria));

			#### RE DIRETOR DE PREVIDENCIA ####
			$args['assinatura_emp']  = '';
			$args['assinatura_re']   = '';
			$args['assinatura_seq']  = '';
			$args['assinatura_nome'] = $diretor['nome'];
		}
		else if(trim($ds_diretoria) == 'INT')
		{
			$args['assinatura_emp']  = '';
			$args['assinatura_re']   = ''; // ADRIANO GFC
			$args['assinatura_seq']  = '';
			$args['assinatura_nome'] = 'ROGER ODILLO KLAFKE';
		}
		/*
		else if(trim($ds_diretoria) == 'GFC')
		{
			$args['assinatura_emp']  = 9;
			$args['assinatura_re']   = 5100; // ADRIANO GFC
			$args['assinatura_seq']  = 0;
			$args['assinatura_nome'] = 'ADRIANO CARLOS O MEDEIROS';
		}
		*/
		else
		{
			$usuario = $this->correspondencias_model->get_re_usuario($ds_diretoria);

			$args['assinatura_emp']  = 9;
			$args['assinatura_re']   = $usuario['cd_registro_empregado'];
			$args['assinatura_seq']  = 0;
			$args['assinatura_nome'] = $usuario['ds_nome'];
		}

		
		$args['cd_empresa']            = '';
		$args['cd_registro_empregado'] = '';
		$args['seq_dependencia']       = '';
		$args['destinatario_emp']            = '';
		$args['destinatario_re'] = '';
		$args['destinatario_seq']       = '';	
		$args['destinatario_nome']     = $ds_nome;
		$args['assunto']               = $ds_assunto;
		$args['fl_restrito']           = '';
		$args['data']                  = date('d/m/Y');
		
		#### INSERE CORRESPONDENCIA ####
		$cd_correspondencia = $this->correspondencias_model->salvar($args);	

		#### BUSCA NUMERO ####
		$correspondencia = $this->correspondencias_model->get_correspondencia($cd_correspondencia);
		
		echo $correspondencia['nr_numero'];
    }

    public function anexo($cd_correspondencia)
    {
    	CheckLogin();

		$this->load->model('projetos/correspondencias_model');

		$data['row'] = $this->correspondencias_model->carrega($cd_correspondencia);

		if(trim($data['row']['fl_restrito']) == 'S' AND $this->session->userdata('divisao') != trim($data['row']['cd_gerencia_inclusao']))
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
		else
		{
			$data['collection'] = $this->correspondencias_model->listar_anexo($cd_correspondencia);

			$this->load->view('cadastro/sg_correspondencia/anexo', $data);
		}
    }

    public function salvar_anexo()
    {
    	CheckLogin();

		$this->load->model('projetos/correspondencias_model');

		$cd_correspondencia = $this->input->post('cd_correspondencia', TRUE);

		$data['row'] = $this->correspondencias_model->carrega($cd_correspondencia);

		if(trim($data['row']['fl_restrito']) == 'S' AND $this->session->userdata('divisao') != trim($data['row']['cd_gerencia_inclusao']))
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
		else
		{
			$qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));

            if($qt_arquivo > 0)
            {
                $nr_conta = 0;

                while($nr_conta < $qt_arquivo)
                {
                    $args = array();        
                    
                    $args['arquivo_nome'] = $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE);
                    $args['arquivo']      = $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE);
                    $args['cd_usuario']   = $this->session->userdata('codigo');      
                    
                    $this->correspondencias_model->salvar_anexo(intval($cd_correspondencia), $args);
                    
                    $nr_conta++;
                }
            }

            redirect('cadastro/sg_correspondencia/anexo/'.intval($cd_correspondencia), 'refresh');
		}
    }

    public function excluir_anexo($cd_correspondencia, $cd_correspondencia_anexo)
    {
    	CheckLogin();

        $this->load->model('projetos/correspondencias_model');

        $this->correspondencias_model->excluir_anexo(
            intval($cd_correspondencia_anexo), 
            $this->session->userdata('codigo')
        );

        redirect('cadastro/sg_correspondencia/anexo/'.intval($cd_correspondencia), 'refresh');
    }
}
?>