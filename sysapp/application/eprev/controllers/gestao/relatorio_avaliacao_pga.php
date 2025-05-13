<?php
class Relatorio_avaliacao_pga extends Controller {

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao()
    {
    	if(gerencia_in(array('GC', 'DE')))
    	{
    		return TRUE;
    	}
    	else
    	{
    		return FALSE;
    	}
    }

	private function get_trimestres()
	{
		return array(
			array('value' => '1', 'text' => '1º Trimestre'),
			array('value' => '2', 'text' => '2º Trimestre'),
			array('value' => '3', 'text' => '3º Trimestre'),
    		array('value' => '4', 'text' => '4º Trimestre')
		);
	}
	
    public function index()
    {
    	if($this->get_permissao())
    	{
			$data = array();
			
			$data['trimestres'] = $this->get_trimestres();
			
			$this->load->view('gestao/relatorio_avaliacao_pga/index', $data);
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
			$this->load->model('gestao/relatorio_avaliacao_pga_model');
			
			$args = array();
			$data = array();

			$args = array(
				'nr_ano' 	   => $this->input->post('nr_ano', TRUE),
				'nr_trimestre' => $this->input->post('nr_trimestre', TRUE)
			);
			
			manter_filtros($args);
			
			$data['collection'] = $this->relatorio_avaliacao_pga_model->listar($args);

			$this->load->view('gestao/relatorio_avaliacao_pga/index_result', $data);
		}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function cadastro($cd_relatorio_avaliacao_pga = 0)
    {
    	if($this->get_permissao())
    	{
    		$this->load->model('gestao/relatorio_avaliacao_pga_model');

			$data = array();
			
			$data['trimestres'] = $this->get_trimestres();
			
			$ano = $this->relatorio_avaliacao_pga_model->get_ano();
			
			if(count($ano) > 0)
			{
				$trimestre  = $this->relatorio_avaliacao_pga_model->get_trimestre($ano['nr_ano']);
			}
			
			if(intval($cd_relatorio_avaliacao_pga) == 0)
			{
				$data['row'] = array(
					'cd_relatorio_avaliacao_pga' => $cd_relatorio_avaliacao_pga,
					'nr_ano'					 => (count($ano) > 0 ? (intval($trimestre['nr_trimestre']) == 4 ? intval($ano['nr_ano']) + 1 :  intval($ano['nr_ano'])) : ''),
					'nr_trimestre'				 => (count($ano) > 0 ? (intval($trimestre['nr_trimestre']) == 4 ? 1 : intval($trimestre['nr_trimestre']) + 1) : '')
				);
			}
			else
			{
				$data['row'] = $this->relatorio_avaliacao_pga_model->carrega($cd_relatorio_avaliacao_pga);
			}

			$this->load->view('gestao/relatorio_avaliacao_pga/cadastro', $data);
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
        	$this->load->model('gestao/relatorio_avaliacao_pga_model');

			$args = array();
			
			$cd_relatorio_avaliacao_pga = $this->input->post('cd_relatorio_avaliacao_pga', TRUE);
			$cd_usuario 				= $this->session->userdata('codigo');
			
			$args = array(
				'nr_ano' 	   => $this->input->post('nr_ano', TRUE),
				'nr_trimestre' => $this->input->post('nr_trimestre', TRUE)
			);

			if(intval($args['nr_ano']) == 2017)
			{
				$usuarios_diretoria = $this->relatorio_avaliacao_pga_model->get_usuario_interventor();
			}
			else
			{
				$usuarios_diretoria = $this->relatorio_avaliacao_pga_model->get_usuarios_de();
			}
			
			foreach($usuarios_diretoria as $item)
			{
				$diretoria[] = array(
					'cd_usuario_diretoria' => $item['value'],
					'diretoria' 		   => $item['diretoria']
				);
			}
			
			if(intval($cd_relatorio_avaliacao_pga) == 0)
			{
				$cd_relatorio_avaliacao_pga = $this->relatorio_avaliacao_pga_model->salvar($cd_usuario, $args);
				
				$data['indicadores'] = $this->relatorio_avaliacao_pga_model->listar_indicador_pga($cd_relatorio_avaliacao_pga);
				
				foreach($data['indicadores'] as $item)
				{
					$this->relatorio_avaliacao_pga_model->salvar_indicador($cd_usuario, $cd_relatorio_avaliacao_pga, $item);
				}
				
				foreach($diretoria as $item)
				{
					$this->relatorio_avaliacao_pga_model->salvar_diretoria($cd_relatorio_avaliacao_pga, $item['cd_usuario_diretoria'], $item['diretoria'], $cd_usuario);
				}
				
				$this->atualiza_indicador($cd_relatorio_avaliacao_pga);				
			}
			else
			{
				$this->relatorio_avaliacao_pga_model->atualizar($cd_relatorio_avaliacao_pga, $cd_usuario, $args);
			}
			
			redirect('gestao/relatorio_avaliacao_pga/indicador/'.$cd_relatorio_avaliacao_pga, 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
    public function indicador($cd_relatorio_avaliacao_pga)
    {
    	if($this->get_permissao())
    	{
    		$this->load->model('gestao/relatorio_avaliacao_pga_model');

			$data = array();

			$data['row'] = $this->relatorio_avaliacao_pga_model->carrega($cd_relatorio_avaliacao_pga);

			$data['diretoria'] = $this->relatorio_avaliacao_pga_model->get_usuarios_de();

			$data['assinaturas'] = $this->relatorio_avaliacao_pga_model->get_assinaturas($cd_relatorio_avaliacao_pga);
			
			if($data['row']['dt_encerramento'] != '')
			{
				$data['diretores_assinatura'] = $this->relatorio_avaliacao_pga_model->get_diretores_assinatura($cd_relatorio_avaliacao_pga);
			}
			
			$data['cd_relatorio_avaliacao_pga'] = intval($cd_relatorio_avaliacao_pga);
			
			$data['collection'] = $this->relatorio_avaliacao_pga_model->listar_indicador($cd_relatorio_avaliacao_pga);
			
			foreach($data['diretoria'] as $item)
			{
				if(trim($item['diretoria']) ==  'SEG' AND trim($data['row']['cd_dir_seguridade']) == '')
				{
					$data['row']['cd_dir_seguridade'] = $item['value'];
				}
				else if(trim($item['diretoria']) == 'PRE' AND trim($data['row']['cd_presidente']) == '')
				{
					$data['row']['cd_presidente'] = $item['value'];
				}
				/*
				else if(trim($item['diretoria']) ==  'ADM' AND trim($data['row']['cd_dir_administrativo']) == '')
				{
					$data['row']['cd_dir_administrativo'] = $item['value'];
				}
				*/
				else if(trim($item['diretoria']) ==  'FIN' AND trim($data['row']['cd_dir_financeiro']) == '')
				{
					$data['row']['cd_dir_financeiro'] = $item['value'];
				}
			}

			$this->load->view('gestao/relatorio_avaliacao_pga/indicador', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

	public function salvar_diretoria()
	{
		if($this->get_permissao())
    	{
			$this->load->model('gestao/relatorio_avaliacao_pga_model');
			
			$args = array();
			
			$cd_relatorio_avaliacao_pga = $this->input->post('cd_relatorio_avaliacao_pga', TRUE);
			
			$cd_usuario = $this->session->userdata('codigo');
			
			$args = array(
				array(
					'cd_usuario_diretoria' => $this->input->post('cd_presidente', TRUE),
					'diretoria' 		   => 'PRE'
				)
			);


			if(intval($this->input->post('cd_dir_financeiro', TRUE)) > 0)
			{
				$args[] = array(
					'cd_usuario_diretoria' => $this->input->post('cd_dir_financeiro', TRUE),
					'diretoria' 		   => 'FIN'
				);
			}

			if(intval($this->input->post('cd_dir_administrativo', TRUE)) > 0)
			{
				$args[] = array(
					'cd_usuario_diretoria' => $this->input->post('cd_dir_administrativo', TRUE),
					'diretoria' 		   => 'ADM'
				);
			}

			if(intval($this->input->post('cd_dir_seguridade', TRUE)) > 0)
			{
				$args[] = array(
					'cd_usuario_diretoria' => $this->input->post('cd_dir_seguridade', TRUE),
					'diretoria' 		   => 'SEG'
				);
			}
					
			foreach($args as $item)
			{
				$this->relatorio_avaliacao_pga_model->atualizar_diretoria($cd_relatorio_avaliacao_pga, $item['cd_usuario_diretoria'], $item['diretoria'], $cd_usuario);
			}
			
			redirect('gestao/relatorio_avaliacao_pga/indicador/'.$cd_relatorio_avaliacao_pga, 'refresh');
		}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
	}
	
    public function cadastro_avaliacao($cd_relatorio_avaliacao_pga, $cd_indicador, $cd_relatorio_avaliacao_pga_indicador)
    {
    	if($this->get_permissao())
    	{
    		$this->load->model('gestao/relatorio_avaliacao_pga_model');

			$data = array();
	
			$data['row'] = $this->relatorio_avaliacao_pga_model->carrega_indicador($cd_relatorio_avaliacao_pga_indicador);
			
			$this->load->view('gestao/relatorio_avaliacao_pga/cadastro_avaliacao', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function salvar_indicador()
	{
		if($this->get_permissao())
        {
        	$this->load->model('gestao/relatorio_avaliacao_pga_model');

			$args = array();
			
			$cd_relatorio_avaliacao_pga_indicador = $this->input->post('cd_relatorio_avaliacao_pga_indicador', TRUE);
			$cd_usuario 						  = $this->session->userdata('codigo');
			
			$args = array(
				'cd_relatorio_avaliacao_pga' => $this->input->post('cd_relatorio_avaliacao_pga', TRUE),
				'cd_indicador' 				 => $this->input->post('cd_indicador', TRUE),
				'ds_avaliacao' 				 => $this->input->post('ds_avaliacao', TRUE)
			);

			$this->relatorio_avaliacao_pga_model->atualizar_indicador($cd_relatorio_avaliacao_pga_indicador, $cd_usuario, $args);
			
			redirect('gestao/relatorio_avaliacao_pga/indicador/'.$args['cd_relatorio_avaliacao_pga'], 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}	
	
	public function atualiza_indicador($cd_relatorio_avaliacao_pga)
	{
		if($this->get_permissao())
    	{
			$this->load->model('gestao/relatorio_avaliacao_pga_model');

        	$cd_usuario = $this->session->userdata('codigo');

        	$relatorio = $this->relatorio_avaliacao_pga_model->carrega($cd_relatorio_avaliacao_pga);

			$indicadores = $this->relatorio_avaliacao_pga_model->get_indicadores($cd_relatorio_avaliacao_pga);
			
			foreach($indicadores as $item) 
			{					
				$indicador_tabela = $this->relatorio_avaliacao_pga_model->indicador_tabela(intval($item['cd_indicador_tabela']));

				$indicador_tabela = array_map("arrayToUTF8", $indicador_tabela);		

				$parametro = $this->relatorio_avaliacao_pga_model->indicador_parametro(intval($item['cd_indicador_tabela']));

				$indicador_tabela['parametro'] = array();

				foreach($parametro as $item2)
				{
					$indicador_tabela['parametro'][$item2['nr_linha']][$item2['nr_coluna']] = array_map("arrayToUTF8", $item2);	
				}

				$row = $this->relatorio_avaliacao_pga_model->get_relatorio_avaliacao_pga_indicador_tabela(intval($item['cd_indicador']), intval($item['cd_relatorio_avaliacao_pga_indicador']));

				if((count($row) > 0) AND ($row['cd_relatorio_avaliacao_pga_indicador_tabela'] > 0))
				{
					$this->relatorio_avaliacao_pga_model->atualizar_indicador_tabela($row['cd_relatorio_avaliacao_pga_indicador_tabela'], $cd_usuario, json_encode($indicador_tabela));
				}
				else
				{
					$this->relatorio_avaliacao_pga_model->salvar_indicador_tabela(intval($item['cd_relatorio_avaliacao_pga_indicador']), $cd_usuario, json_encode($indicador_tabela));
				}
			}
		
			$this->relatorio_avaliacao_pga_model->atualizar_apresentacao($cd_relatorio_avaliacao_pga, $cd_usuario);
			
			redirect('gestao/relatorio_avaliacao_pga/indicador/'.$cd_relatorio_avaliacao_pga, 'refresh');
        }
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
	}
	
	public function encerrar($cd_relatorio_avaliacao_pga)
	{
		if($this->get_permissao())
        {
			$this->load->model('gestao/relatorio_avaliacao_pga_model');

			$this->relatorio_avaliacao_pga_model->encerrar(intval($cd_relatorio_avaliacao_pga), $this->session->userdata('codigo'));
			
			$diretorias = array(
				array('diretoria' => 'FIN'),   
				//array('diretoria' => 'ADM'),
				array('diretoria' => 'SEG'),
				array('diretoria' => 'PRE')
			);
				
			foreach($diretorias as $item)
			{
				$this->enviar_email_diretoria($cd_relatorio_avaliacao_pga, $item['diretoria']);
			}

			redirect('gestao/relatorio_avaliacao_pga/index', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function reenviar_email_diretoria($cd_relatorio_avaliacao_pga)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/relatorio_avaliacao_pga_model');

			$diretorias = array(
				array('diretoria' => 'FIN'),
				//array('diretoria' => 'ADM'),
				array('diretoria' => 'SEG'),
				array('diretoria' => 'PRE')
			);	
		
			foreach($diretorias as $item)
			{
				$this->enviar_email_diretoria($cd_relatorio_avaliacao_pga, $item['diretoria']);
			}

			redirect('gestao/relatorio_avaliacao_pga/index', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function enviar_email_diretoria($cd_relatorio_avaliacao_pga, $cd_diretoria)
    {
		$this->load->model('projetos/eventos_email_model');
		$this->load->model('gestao/relatorio_avaliacao_pga_model');
		
		$cd_evento = 229;
		
		$email = $this->eventos_email_model->carrega($cd_evento);
		
		$diretoria = $this->relatorio_avaliacao_pga_model->get_diretores_email($cd_relatorio_avaliacao_pga, $cd_diretoria);
		
		if(count($diretoria) > 0)
		{
			$tags = array('[NOME]', '[TRIMESTRE]', '[LINK]');
	        $subs = array($diretoria['cd_usuario_diretoria'], $diretoria['nr_trimestre'], site_url('gestao/relatorio_avaliacao_pga/assinatura/'.intval($cd_relatorio_avaliacao_pga).'/'.trim($diretoria['cd_relatorio_avaliacao_pga_diretoria'])));

			$texto = str_replace($tags, $subs, $email['email']);
			
			$cd_usuario = $this->session->userdata('codigo');
			
			$args = array(
				'de'      => 'Relatório de Avaliação do PGA',
				'assunto' => $email['assunto'],
				'para'    => $diretoria['email'],
				'cc'      => $email['cc'],
				'cco'     => $email['cco'],
				'texto'   => $texto
			);
			
			$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
		}
    }
	
	public function apresentacao($cd_relatorio_avaliacao_pga)
	{
		$this->load->model('gestao/relatorio_avaliacao_pga_model');
		
		$data['relatorio_avaliacao_pga'] = $this->relatorio_avaliacao_pga_model->carrega(intval($cd_relatorio_avaliacao_pga));
		
		$data['indicador'] = $this->relatorio_avaliacao_pga_model->listar_indicador($cd_relatorio_avaliacao_pga);
		
		$data['qt_indicador'] = count($data['indicador']);

		$data['assinatura_diretores'] = $this->relatorio_avaliacao_pga_model->assinatura_diretores($cd_relatorio_avaliacao_pga);

		$this->load->view('gestao/relatorio_avaliacao_pga/apresentacao', $data);
	}

	public function get_indicador_apresentacao()
	{
		$this->load->model('gestao/relatorio_avaliacao_pga_model');
	
		$cd_indicador               = $this->input->post('cd_indicador', TRUE);
		$cd_relatorio_avaliacao_pga	= $this->input->post('cd_relatorio_avaliacao_pga', TRUE);
		
		$indicador_avaliacao = $this->relatorio_avaliacao_pga_model->get_relatorio_indicador($cd_indicador, $cd_relatorio_avaliacao_pga); 
		
		$retorno['ds_avaliacao'] = utf8_encode($indicador_avaliacao['ds_avaliacao']);
		
		echo json_encode($retorno);
	}
	
	public function alterar_avaliacao()
	{
		$this->load->model('gestao/relatorio_avaliacao_pga_model');
		
		$cd_indicador               = $this->input->post('cd_indicador', TRUE);
		$cd_relatorio_avaliacao_pga	= $this->input->post('cd_relatorio_avaliacao_pga', TRUE);
		$args['ds_avaliacao']		= utf8_decode($this->input->post('ds_avaliacao', TRUE));
		$cd_usuario 				= $this->session->userdata('codigo');
		
		$this->relatorio_avaliacao_pga_model->alterar_avaliacao($cd_indicador, $cd_relatorio_avaliacao_pga, $cd_usuario, $args);
	}
	
	public function apresentacao_indicador()
	{
		$this->load->helper('reuniao_gestao_indicador');
		
		$this->load->model('gestao/relatorio_avaliacao_pga_model');
									
		$cd_indicador               = $this->input->post('cd_indicador', TRUE);
		$cd_relatorio_avaliacao_pga	= $this->input->post('cd_relatorio_avaliacao_pga', TRUE);
									
		$data['row'] = $this->relatorio_avaliacao_pga_model->get_relatorio_indicador($cd_indicador, $cd_relatorio_avaliacao_pga); 
		
		$data['indicador'] = json_decode($data['row']['parametro'], TRUE);
		
		$data['grafico'] = get_grafico_indicador($data['indicador']);
		$data['tabela']  = get_tabela_indicador($data['indicador'], TRUE);

		$this->load->view('gestao/relatorio_avaliacao_pga/apresentacao_result', $data);
	}
	
    public function assinatura($cd_relatorio_avaliacao_pga, $cd_relatorio_avaliacao_pga_diretoria)
    {
    	$this->load->model('gestao/relatorio_avaliacao_pga_model');
		
		$data = array();
		
		$data['diretor'] = $this->relatorio_avaliacao_pga_model->get_diretor($cd_relatorio_avaliacao_pga, $cd_relatorio_avaliacao_pga_diretoria);
		if((gerencia_in(array('GC', 'DE'))) AND ($this->session->userdata('codigo') == intval($data['diretor']['cd_usuario_diretoria'])))
    	{
			$data['row'] = $this->relatorio_avaliacao_pga_model->carrega($cd_relatorio_avaliacao_pga);
			
			$data['cd_relatorio_avaliacao_pga'] = intval($cd_relatorio_avaliacao_pga);
			$data['cd_relatorio_avaliacao_pga_diretoria'] = intval($cd_relatorio_avaliacao_pga_diretoria);
			
			$data['collection'] = $this->relatorio_avaliacao_pga_model->listar_indicador($cd_relatorio_avaliacao_pga);
			
			$this->load->view('gestao/relatorio_avaliacao_pga/assinatura', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function assinar()
    {
    	if($this->get_permissao())
    	{
    		$this->load->model('gestao/relatorio_avaliacao_pga_model');
			
			$cd_relatorio_avaliacao_pga			  = $this->input->post('cd_relatorio_avaliacao_pga', TRUE);
			$cd_relatorio_avaliacao_pga_diretoria = $this->input->post('cd_relatorio_avaliacao_pga_diretoria', TRUE);
			$cd_usuario 						  = $this->session->userdata('codigo');
			
			$this->relatorio_avaliacao_pga_model->assinar($cd_relatorio_avaliacao_pga, $cd_relatorio_avaliacao_pga_diretoria, $cd_usuario);

			redirect('gestao/relatorio_avaliacao_pga/assinatura/'.intval($cd_relatorio_avaliacao_pga).'/'.intval($cd_relatorio_avaliacao_pga_diretoria), 'refresh');
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function diretoria()
    {
		if(gerencia_in(array('DE')))
    	{
			$data['trimestres'] = $this->get_trimestres();

			$this->load->view('gestao/relatorio_avaliacao_pga/diretoria', $data);
		}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    	
    }

	public function diretoria_listar()
	{
		$this->load->model('gestao/relatorio_avaliacao_pga_model');

		$args = array(
			'nr_ano' 	   => $this->input->post('nr_ano', TRUE),
			'nr_trimestre' => $this->input->post('nr_trimestre', TRUE),
			'fl_assinado'  => $this->input->post('fl_assinado', TRUE),
		);
		
		manter_filtros($args);

		$data['collection'] = $this->relatorio_avaliacao_pga_model->diretoria_listar($this->session->userdata('codigo'), $args);

		$this->load->view('gestao/relatorio_avaliacao_pga/diretoria_result', $data);
	}


	public function salvar_imagem()
	{	
		$id_imagem     = $this->input->post('id_imagem');
		$nr_ano        = $this->input->post('nr_ano');
		$nr_trimestre  = $this->input->post('nr_trimestre');
		$ob_imagem     = $this->input->post('ob_imagem');
		
		$ob_imagem = str_replace('data:image/png;base64,', '', $ob_imagem);
		$ob_imagem = str_replace(' ', '+', $ob_imagem);

		$ob_data = base64_decode($ob_imagem);

		$arq = strtolower($this->session->userdata('usuario')).'_'.$nr_ano.'_'.$nr_trimestre."_".$id_imagem;
		$file = '../cieprev/up/relatorio_pga_apresentacao/'.$arq.'.png';
		
		file_put_contents($file, $ob_data);			
	}

    public function gera_pdf($nr_ano = 0, $nr_trimestre = 0, $qt = 0)
	{
	   //set_time_limit(0);

		$this->load->plugin('fpdf');
			
		$ob_pdf = new PDF();
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');	
		$ob_pdf->SetNrPag(true);
		$ob_pdf->SetMargins(10, 14, 5);
		$ob_pdf->header_exibe = true;
		$ob_pdf->header_logo = true;
		$ob_pdf->header_titulo = true;
		$ob_pdf->header_titulo_texto = 'Indicadores de Gestão do PGA - Avaliação da Diretoria Executiva';
		$ob_pdf->header_subtitulo = true;
		$ob_pdf->header_subtitulo_texto = 'Referente: '.$nr_ano.'/'.$nr_trimestre;				

		$ob_pdf->SetLineWidth(0);
		$ob_pdf->SetDrawColor(0, 0, 0);

		$i = 0;

		while($i < $qt)
		{
			$margem_x = 10;
			
			$arq = '../cieprev/up/relatorio_pga_apresentacao/'.strtolower($this->session->userdata('usuario')).'_'.$nr_ano.'_'.$nr_trimestre."_".$i.".png";

			list($w, $h) = getimagesize($arq);  

			if($w > $h)
			{
				$lim_width  = 1050;
				$lim_height = 640;	
				$pr_height = ceil(($lim_width * 100) / $w);
				$height = ($pr_height * $h) / 100;					
				$width  = $lim_width;	

				if($height > $lim_height)
				{
					$pr_width = ceil(($lim_height * 100) / $h);
					$width = ($pr_width * $w) / 100;					
					$height  = $lim_height;								
				}
				
				$ob_pdf->AddPage('L');
			}
			else
			{
				$lim_width  = 720;
				$lim_height = 900;
				$pr_width = ceil(($lim_height * 100) / $h);
				$width = ($pr_width * $w) / 100;					
				$height  = $lim_height;						
				
				if($width > $lim_width)
				{
					$pr_height = ceil(($lim_width * 100) / $w);
					$height = ($pr_height * $h) / 100;					
					$width  = $lim_width;							
				}		
				
				$ob_pdf->AddPage('P');
			}

			if($width < $lim_width)
			{
				$margem_x += $ob_pdf->ConvertSize(floor(($lim_width - $width) / 2));
			}				
				
			
			$ob_pdf->Image(
				$arq, 
				$margem_x, 
				$ob_pdf->GetY(), 
				$ob_pdf->ConvertSize($width), 
				$ob_pdf->ConvertSize($height), 
				'', 
				'',
				true
			);
			
			unlink($arq);
			$i++;
		}

        $ob_pdf->Output();

        exit;	
	}
		
}