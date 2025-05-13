<?php
class Pauta_cci extends Controller {

	var $diretorio_cci;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();

		$this->diretorio_cci = '../eletroceee/arq/data/CCI/';
    }

    private function cria_diretorio_anual($dt_pauta, $nr_pauta)
    {
		list($dia, $mes, $ano) = explode('/', $dt_pauta);

    	$diretorio = $this->diretorio_cci.'CCI '.$ano;

    	if(!is_dir($diretorio))
    	{
    		mkdir($diretorio, 0777);
    	}

    	$diretorio = $diretorio.'/CCI '.str_pad($nr_pauta, 2, '0', STR_PAD_LEFT).'-'.$ano;

    	if(!is_dir($diretorio))
    	{
    		mkdir($diretorio, 0777);
    	}

    	return $diretorio;
    }

    private function get_permissao()
    {
    	if(gerencia_in(array('SG', 'DE')))
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
			$this->load->view('gestao/pauta_cci/index');
		}
		else
		{
			exibir_mensagem('ACESSO N? PERMITIDO');
		}
    }

    public function listar()
    {
    	$this->load->model('gestao/pauta_cci_model');

    	$args = array(
    		'nr_pauta_cci'         => $this->input->post('nr_pauta_cci', TRUE),
    		'dt_pauta_cci_ini'     => $this->input->post('dt_pauta_cci_ini', TRUE),
    		'dt_pauta_cci_fim'     => $this->input->post('dt_pauta_cci_fim', TRUE),
    		'dt_pauta_cci_fim_ini' => $this->input->post('dt_pauta_cci_fim_ini', TRUE),
    		'dt_pauta_cci_fim_fim' => $this->input->post('dt_pauta_cci_fim_fim', TRUE),
    		'fl_aprovado'          => $this->input->post('fl_aprovado', TRUE)
    	);
				
		manter_filtros($args);
		
		$data['collection'] = $this->pauta_cci_model->listar($args);

		$this->load->view('gestao/pauta_cci/index_result', $data);
    }

    public function cadastro($cd_pauta_cci = 0)
    {
    	if($this->get_permissao())
		{
			if(intval($cd_pauta_cci) == 0)
			{
				$data['row'] = array(
					'cd_pauta_cci'         => intval($cd_pauta_cci),
					'nr_pauta_cci'         => '',
					'dt_pauta_cci'         => '',
					'hr_pauta_cci'         => '',
					'dt_pauta_cci_fim'     => '',
					'hr_pauta_cci_fim'     => '',
					'ds_local'             => '',
					'cd_usuario_aprovacao' => '',
					'dt_aprovacao'         => ''
				);
			}
			else
			{
				$this->load->model('gestao/pauta_cci_model');

				$data['row'] = $this->pauta_cci_model->carrega($cd_pauta_cci);
			}

			$this->load->view('gestao/pauta_cci/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO N? PERMITIDO');
		}
    }

    public function salvar()
    {
    	$this->load->model('gestao/pauta_cci_model');

    	$cd_pauta_cci = $this->input->post('cd_pauta_cci', TRUE);

    	$args = array(
    		'nr_pauta_cci'     => $this->input->post('nr_pauta_cci', TRUE),
    		'dt_pauta_cci'     => $this->input->post('dt_pauta_cci', TRUE).' '.$this->input->post('hr_pauta_cci', TRUE),
    		'dt_pauta_cci_fim' => $this->input->post('dt_pauta_cci_fim', TRUE).' '.$this->input->post('hr_pauta_cci_fim', TRUE),
    		'ds_local'         => $this->input->post('ds_local', TRUE),
    		'cd_usuario'       => $this->session->userdata('codigo')
    	);

    	if(intval($cd_pauta_cci) == 0)
		{
			$diretorio = $this->cria_diretorio_anual(
				trim($this->input->post('dt_pauta_cci', TRUE)), 
				intval($args['nr_pauta_cci'])
			);

			$args['ds_integracao_arq'] = $diretorio;

			$cd_pauta_cci = $this->pauta_cci_model->salvar($args);

			$this->cria_diretorio_anual($this->input->post('dt_pauta_cci', TRUE), $args['nr_pauta_cci']);
		
			$assuntos_removidos = $this->pauta_cci_model->assuntos_removidos($cd_pauta_cci);

			foreach($assuntos_removidos as $item)
			{
				$args = array(
					'cd_pauta_cci'             => $cd_pauta_cci,
					'cd_pauta_cci_assunto'     => $item['cd_pauta_cci_assunto'],
					'nr_item'                  => ($item['nr_item'] != 0 ? count($item['nr_item']) > 0 : 1 ),
					'cd_gerencia_responsavel'  => $item['cd_gerencia_responsavel'],
					'cd_usuario_responsavel'   => $item['cd_usuario_responsavel'],
					'cd_gerencia_substituto'   => $item['cd_gerencia_substituto'],
					'cd_usuario_substituto'    => $item['cd_usuario_substituto'],
					'ds_pauta_cci_assunto'     => $item['ds_pauta_cci_assunto'],
					'cd_usuario'               => $this->session->userdata('codigo')
				);
                
				$this->pauta_cci_model->assunto_salvar($args);
			}
		} 
		else
		{
			$this->pauta_cci_model->atualizar(intval($cd_pauta_cci), $args);
		}

		redirect('gestao/pauta_cci/assunto/'.$cd_pauta_cci, 'refresh');
    }

    public function assunto($cd_pauta_cci, $cd_pauta_cci_assunto = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_cci_model');

			$data['row'] = $this->pauta_cci_model->carrega($cd_pauta_cci);

			$data['collection'] = $this->pauta_cci_model->assunto_listar($cd_pauta_cci);

			if(intval($cd_pauta_cci_assunto) == 0)
			{
				$row = $this->pauta_cci_model->carrega_numero_assunto($cd_pauta_cci);

				$data['assunto'] = array(
					'cd_pauta_cci_assunto'    => intval($cd_pauta_cci_assunto),
					'cd_pauta_cci'            => '',
					'nr_item'         		  => (count($row) > 0 ? $row['nr_item'] : 1) ,
					'cd_gerencia_responsavel' => '',
					'cd_usuario_responsavel'  => '',
					'cd_gerencia_substituto'  => '',
					'cd_usuario_substituto'   => '',
					'ds_pauta_cci_assunto'    => '',
					'ds_recomendacao'         => ''
				);

				$data['responsavel'] = array();
				$data['substituto'] = array();
			}
			else
			{
				$data['assunto']     = $this->pauta_cci_model->assunto_carrega(intval($cd_pauta_cci_assunto));
				$data['responsavel'] = $this->pauta_cci_model->get_usuarios($data['assunto']['cd_gerencia_responsavel']);
				$data['substituto']  = $this->pauta_cci_model->get_usuarios($data['assunto']['cd_gerencia_substituto']);
			}
			
			$this->load->view('gestao/pauta_cci/assunto', $data);
		}
		else
		{
			exibir_mensagem('ACESSO N? PERMITIDO');
		}
	}

	public function get_usuarios()
    {		
		$this->load->model('gestao/pauta_cci_model');

		$cd_gerencia = $this->input->post('cd_gerencia', TRUE);

		echo json_encode($this->pauta_cci_model->get_usuarios($cd_gerencia));
    }
   
	public function assunto_salvar()
	{
		if($this->get_permissao())
		{    
			$this->load->model('gestao/pauta_cci_model');
			
			$cd_pauta_cci_assunto = $this->input->post('cd_pauta_cci_assunto', TRUE);

			$args = array(
				'cd_pauta_cci_assunto'    => $this->input->post('cd_pauta_cci_assunto',TRUE),
				'cd_pauta_cci'			  => $this->input->post('cd_pauta_cci',TRUE),
				'nr_item'         		  => $this->input->post('nr_item',TRUE),
				'cd_gerencia_responsavel' => $this->input->post('cd_gerencia_responsavel',TRUE),
				'cd_usuario_responsavel'  => $this->input->post('cd_usuario_responsavel',TRUE),
				'cd_gerencia_substituto'  => $this->input->post('cd_gerencia_substituto',TRUE),
				'cd_usuario_substituto'   => $this->input->post('cd_usuario_substituto',TRUE),
				'ds_recomendacao'		  => $this->input->post('ds_recomendacao',TRUE),
				'ds_pauta_cci_assunto'    => $this->input->post('ds_pauta_cci_assunto',TRUE),
				'cd_usuario'              => $this->session->userdata('codigo')
			);

			if(intval($cd_pauta_cci_assunto) == 0)
			{    
				$cd_pauta_cci_assunto = $this->pauta_cci_model->assunto_salvar($args);
			}
			else
			{
				$this->pauta_cci_model->assunto_atualizar(intval($cd_pauta_cci_assunto), $args);
			}
             
			redirect('gestao/pauta_cci/assunto/'.$args['cd_pauta_cci'], 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO N? PERMITIDO');
		}
	}

	public function assunto_excluir($cd_pauta_cci, $cd_pauta_cci_assunto)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_cci_model');

			$data['row'] = $this->pauta_cci_model->carrega($cd_pauta_cci);

			$assunto = $this->pauta_cci_model->assunto_carrega($cd_pauta_cci_assunto);

			$this->pauta_cci_model->assunto_excluir($this->session->userdata('codigo'), $cd_pauta_cci_assunto);
		
			redirect('gestao/pauta_cci/assunto/'.$cd_pauta_cci, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function assunto_remover($cd_pauta_cci, $cd_pauta_cci_assunto)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_cci_model');

			$data['row'] = $this->pauta_cci_model->carrega($cd_pauta_cci);

			$assunto = $this->pauta_cci_model->assunto_carrega($cd_pauta_cci_assunto);

			$this->pauta_cci_model->assunto_remover($this->session->userdata('codigo'), $cd_pauta_cci_assunto);

			redirect("gestao/pauta_cci/assunto/".$cd_pauta_cci, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
    
    public function set_ordem($cd_pauta_cci_assunto)
    {
    	$this->load->model('gestao/pauta_cci_model');

		$args = array(
    		'nr_item'    => $this->input->post('nr_item', TRUE),
    		'cd_usuario' => $this->session->userdata('codigo')
    	);
		
		$this->pauta_cci_model->set_ordem($cd_pauta_cci_assunto, $args);
    }

    public function anexo($cd_pauta_cci, $cd_pauta_cci_assunto)
    {
    	if($this->get_permissao())
		{    
			$this->load->model('gestao/pauta_cci_model');

			$data = array(
				'pauta'      => $this->pauta_cci_model->carrega($cd_pauta_cci),
				'assunto'    => $this->pauta_cci_model->assunto_carrega(intval($cd_pauta_cci_assunto)),
				'collection' => $this->pauta_cci_model->anexo_listar(intval($cd_pauta_cci_assunto))
			);

    		$this->load->view('gestao/pauta_cci/anexo', $data);
    	}
		else
		{
			exibir_mensagem('ACESSO N? PERMITIDO');
		}
	}

	public function anexo_salvar()
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_cci_model');

			$cd_pauta_cci         = $this->input->post('cd_pauta_cci', TRUE);
			$cd_pauta_cci_assunto = $this->input->post('cd_pauta_cci_assunto', TRUE);
			$qt_arquivo           = intval($this->input->post('arquivo_m_count', TRUE));

			$pauta   = $this->pauta_cci_model->carrega($cd_pauta_cci);
			$assunto = $this->pauta_cci_model->assunto_carrega($cd_pauta_cci_assunto);
		
			if($qt_arquivo > 0)
			{
				$nr_conta = 0;
				
				while($nr_conta < $qt_arquivo)
				{
					$ds_nome_arquivo_arq = '';

					$arquivo      = $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE);
					$arquivo_nome = $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE);

					if(is_dir($pauta['ds_integracao_arq']))
					{
						$ds_nome_arquivo_arq = str_pad($assunto['nr_item'], 2, '0', STR_PAD_LEFT).' - '.utf8_encode($arquivo_nome);

						copy('../cieprev/up/pauta_cci/'.$arquivo, $pauta['ds_integracao_arq'].'/'.$ds_nome_arquivo_arq);
					}

					$args = array(
						'cd_pauta_cci_assunto' => $cd_pauta_cci_assunto,
						'arquivo_nome'         => $arquivo_nome,
						'arquivo'	           => $arquivo,
						'ds_nome_arquivo_arq'  => $ds_nome_arquivo_arq,
						'cd_usuario'           => $this->session->userdata('codigo')
					);		
									
					$this->pauta_cci_model->anexo_salvar($args);
					
					$nr_conta++;
				}
			}		

			redirect('gestao/pauta_cci/assunto/'.$cd_pauta_cci, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO N? PERMITIDO');
		}
	}

	public function anexo_excluir($cd_pauta_cci, $cd_pauta_cci_assunto, $cd_pauta_cci_assunto_anexo)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_cci_model');

			$pauta   = $this->pauta_cci_model->carrega($cd_pauta_cci);

			$anexo = $this->pauta_cci_model->anexo_carrega($cd_pauta_cci_assunto_anexo);

			$this->pauta_cci_model->anexo_excluir($this->session->userdata('codigo'), $cd_pauta_cci_assunto_anexo);
		
			@unlink($pauta['ds_integracao_arq'].'/'.$anexo['ds_nome_arquivo_arq']);

			redirect('gestao/pauta_cci/anexo/'.$cd_pauta_cci.'/'.$cd_pauta_cci_assunto, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO N? PERMITIDO');
		}
	}

	public function pesquisa()
	{
		if($this->get_permissao())
		{
			$this->load->view('gestao/pauta_cci/pesquisa');
		}
		else
		{
			exibir_mensagem('ACESSO N? PERMITIDO');
		}	
	}
	
	public function pesquisa_listar()
	{
		$this->load->model('gestao/pauta_cci_model');

		$args = array(
    		'nr_pauta_cci'         => $this->input->post('nr_pauta_cci', TRUE),
    		'dt_pauta_cci_ini'     => $this->input->post('dt_pauta_cci_ini', TRUE),
    		'dt_pauta_cci_fim'     => $this->input->post('dt_pauta_cci_fim', TRUE),
    		'dt_pauta_cci_fim_ini' => $this->input->post('dt_pauta_cci_fim_ini', TRUE),
    		'dt_pauta_cci_fim_fim' => $this->input->post('dt_pauta_cci_fim_fim', TRUE),
    		'ds_pauta_cci_assunto' => $this->input->post('ds_pauta_cci_assunto', TRUE)
		);

		manter_filtros($args);

		$data['collection'] = $this->pauta_cci_model->pesquisa_listar($args);

		$this->load->view('gestao/pauta_cci/pesquisa_result', $data);
	}

	public function aprovar($cd_pauta_cci)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_cci_model');

			$this->pauta_cci_model->aprovar($cd_pauta_cci, $this->session->userdata('codigo'));

			redirect('gestao/pauta_cci', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO N? PERMITIDO');
		}
	}

	public function reabrir($cd_pauta_cci)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_cci_model');

			$this->pauta_cci_model->reabrir($cd_pauta_cci);

			redirect('gestao/pauta_cci/assunto/'.$cd_pauta_cci, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO N? PERMITIDO');
		}
	}

	public function pauta($cd_pauta_cci)
	{
		$ds_arquivo = $this->pauta_gerar($cd_pauta_cci);
		
		header('Location: '.base_url('up/pauta_cci/'.$ds_arquivo));
	}

	private function pauta_gerar($cd_pauta_cci)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_cci_model');

			$pauta = $this->pauta_cci_model->carrega($cd_pauta_cci);

			$collection = $this->pauta_cci_model->assunto_listar($cd_pauta_cci, 'N');

			$this->load->plugin('fpdf');
				
			$ob_pdf = new PDF();
			$ob_pdf->AddFont('segoeuil');
			$ob_pdf->AddFont('segoeuib');				
			$ob_pdf->SetNrPag(true);
	        $ob_pdf->SetMargins(10, 14, 5);
	        $ob_pdf->header_exibe = true;
	        $ob_pdf->header_logo = true;
	        $ob_pdf->header_titulo = true;
	        $ob_pdf->header_titulo_texto = '';

	        $ob_pdf->SetLineWidth(0);
	        $ob_pdf->SetDrawColor(0, 0, 0);

	        $ob_pdf->AddPage();
	        $ob_pdf->SetY($ob_pdf->GetY() + 1);
	        $ob_pdf->SetFont('segoeuib', '', 12);
	        $ob_pdf->MultiCell(190, 4.5, 'PAUTA PARA REUNIÃO CCI', 0, 'C');

	        $ob_pdf->SetY($ob_pdf->GetY() + 5);

	        $ob_pdf->SetFont('segoeuib', '', 12);
	        $ob_pdf->MultiCell(190, 5.5, 'ATA nº: '.$pauta['nr_pauta_cci'], 0, 'L');
			$ob_pdf->SetFont('segoeuil', '', 12);

			if(trim($pauta['dt_pauta_cci_fim']) == '')
			{
				$ob_pdf->MultiCell(190, 5.5, 'Data: '.$pauta['dt_pauta_cci'], 0, 'L');
				$ob_pdf->MultiCell(190, 5.5, 'Horário: '.$pauta['hr_pauta_cci'], 0, 'L');
	        }
			else
			{
				$ob_pdf->MultiCell(190, 5.5, 'Data: '.$pauta['dt_pauta_cci'], 0, 'L');
				$ob_pdf->MultiCell(190, 5.5, 'Horário: '.$pauta['hr_pauta_cci'], 0, 'L');
				$ob_pdf->MultiCell(190, 5.5, 'Data Encerramento: '.$pauta['dt_pauta_cci_fim'], 0, 'L');
				$ob_pdf->MultiCell(190, 5.5, 'Hora de Encerramento: '.$pauta['hr_pauta_cci_fim'], 0, 'L');
			}

			$ob_pdf->MultiCell(190, 5.5, 'Local: '.$pauta['ds_local'], 0, 'L');

	        $ob_pdf->SetY($ob_pdf->GetY() + 5);

	      	$ob_pdf->SetWidths(array(10, 180, 125));
			$ob_pdf->SetAligns(array('C', 'C', 'C'));
			$ob_pdf->SetFont('segoeuib', '', 12);
			$ob_pdf->Row(array('Nº', 'ASSUNTOS'));
			$ob_pdf->SetAligns(array('C','J'));

			$ob_pdf->SetFont('segoeuil', '', 12);

	        foreach($collection as $key => $item)
	        {
			 	$ob_pdf->Row(array(
					$item['nr_item'],
					$item['ds_pauta_cci_assunto']
				));
			}

			list($dia, $mes, $ano) = explode('/', $pauta['dt_pauta_cci']);

			$ds_pauta_arquivo = 'Pauta CCI '.str_pad($pauta['nr_pauta_cci'], 2, '0', STR_PAD_LEFT).$ano.' '.$dia.'-'.$mes.'-'.$ano.'.pdf';

			$ob_pdf->Output('up/pauta_cci/'.$ds_pauta_arquivo, 'F');

			if(is_dir($pauta['ds_integracao_arq']))
			{
				copy('../cieprev/up/pauta_cci/'.$ds_pauta_arquivo, $pauta['ds_integracao_arq'].'/'.$ds_pauta_arquivo);
			}

	        return $ds_pauta_arquivo;
		}
		else
		{
			exibir_mensagem('ACESSO N? PERMITIDO');
		}
	}


}	

