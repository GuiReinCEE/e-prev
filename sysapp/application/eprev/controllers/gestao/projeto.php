<?php
class Projeto extends Controller
{
	function __construct()
    {
    	parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao()
    {
    	if($this->session->userdata('tipo') == 'G')
    	{
    		return true;
    	}
    	else if($this->session->userdata('indic_01') == 'S')
    	{
    		return true;
    	}
    	else if($this->session->userdata('indic_05') == 'S')
    	{
    		return true;
    	}
    	else if($this->session->userdata('indic_12') == '*')
    	{
    		return true;
    	}
    	else if(gerencia_in(array('GC')))
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }

    public function index()
    {
		if($this->get_permissao())
		{
			$data = array();

			$this->load->model('gestao/projeto_model');
			
			$data['gerencia'] = $this->projeto_model->get_divisao();

			$data['projetos'] = $this->projeto_model->get_projetos();
								
			$this->load->view('gestao/projeto/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function listar()
    {
    	$this->load->model('gestao/projeto_model');

		$args = array();

		$args['cd_projeto']              = $this->input->post('cd_projeto', TRUE);
		$args['dt_inclusao_ini']         = $this->input->post('dt_inclusao_ini', TRUE);
		$args['dt_inclusao_fim']         = $this->input->post('dt_inclusao_fim', TRUE);
		$args['cd_gerencia_resposanvel'] = $this->input->post('cd_gerencia_resposanvel', TRUE);

		manter_filtros($args);
		
		$data['collection'] = $this->projeto_model->listar($args);

		foreach ($data['collection'] as $key => $item) 
		{
			foreach ($this->projeto_model->gerencia_envolvida(intval($item['cd_projeto'])) as $gerencia) 
			{
				$data['collection'][$key]['gerencia_envolvida'][] = $gerencia['cd_gerencia_envolvida'];
			}
		}
		
		$this->load->view('gestao/projeto/index_result', $data);
    }

    public function cadastro($cd_projeto = 0)
	{
		if($this->get_permissao())		
		{
			$this->load->model('gestao/projeto_model');
			
			$data['gerencia'] = $this->projeto_model->get_gerencia();

			if(intval($cd_projeto) == 0)
			{
				$data['row'] = array(
					'cd_projeto'              => intval($cd_projeto),
					'ds_projeto'              => '',
					'objetivo'                => '',
					'justificativa'           => '',
					'cd_gerencia_resposanvel' => ''
				);

				$data['gerencia_envolvida'] = array();
			}
			else
			{
				$data['row'] = $this->projeto_model->carrega(intval($cd_projeto));

				foreach ($this->projeto_model->gerencia_envolvida(intval($cd_projeto)) as $gerencia) 
				{				
					$data['gerencia_envolvida'][] = $gerencia['cd_gerencia_envolvida'];
				}
			}
			
			$this->load->view('gestao/projeto/cadastro', $data);
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
			$this->load->model('gestao/projeto_model');

			$args = array();

			$cd_projeto = $this->input->post('cd_projeto', TRUE);

			$args['ds_projeto']              = $this->input->post('ds_projeto', TRUE);
			$args['objetivo']                = $this->input->post('objetivo', TRUE);
			$args['justificativa']           = $this->input->post('justificativa', TRUE);
			$args['cd_gerencia_resposanvel'] = $this->input->post('cd_gerencia_resposanvel', TRUE);
			$args['cd_usuario']              = $this->session->userdata('codigo');

			if(is_array($this->input->post('gerencia_envolvida', TRUE)))
			{
				$args['gerencia_envolvida'] = $this->input->post('gerencia_envolvida', TRUE);
			}
			else
			{
				$args['gerencia_envolvida'] = array();
			}

			if(intval($cd_projeto) == 0)
			{
				$cd_projeto = $this->projeto_model->salvar($args);
			}
			else
			{
				$this->projeto_model->atualizar(intval($cd_projeto), $args);
			}

			redirect('gestao/projeto/indicador/'.$cd_projeto, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function relatorio()
	{
		if($this->get_permissao())
		{
			$this->load->view('gestao/projeto/relatorio');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	public function relatorio_listar()
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/projeto_model');
			
			$data['collection'] = $this->projeto_model->listar_relatorio();
			
			$this->load->view('gestao/projeto/relatorio_result', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	public function indicador($cd_projeto)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/projeto_model');
			
			$data['row'] = $this->projeto_model->carrega(intval($cd_projeto));

			$data['indicador'] = $this->projeto_model->indicador();

			$data['indicador_checked'] = array();

			foreach($this->projeto_model->projeto_indicador(intval($cd_projeto)) as $indicador)
			{				
				$data['indicador_checked'][] = $indicador['cd_indicador'];
			}

			$this->load->view('gestao/projeto/indicador', $data);
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
			$this->load->model('gestao/projeto_model');

			$cd_projeto = $this->input->post('cd_projeto', TRUE);

			$args['ds_indicador'] = $this->input->post('ds_indicador', TRUE);
			$args['cd_usuario']   = $this->session->userdata('codigo');

			if(is_array($this->input->post('indicador', TRUE)))
			{
				$args['indicador'] = $this->input->post('indicador', TRUE);
			}
			else
			{
				$args['indicador'] = array();
			}
			
			$this->projeto_model->salvar_indicador($cd_projeto, $args);

			redirect('gestao/projeto/custo/'.$cd_projeto, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function custo($cd_projeto, $cd_projeto_custo = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/projeto_model');
			
			$data['row'] = $this->projeto_model->carrega(intval($cd_projeto));

			$data['collection'] = $this->projeto_model->listar_custo(intval($cd_projeto));

			if(intval($cd_projeto_custo) == 0)
			{
				$data['custo'] = array(
					'cd_projeto_custo'  => intval($cd_projeto_custo),
					'ds_projeto_custo'  => '',
					'nr_valor'          => 0,
					'nr_valor_aprovado' => 0
				);
			}
			else
			{
				$data['custo'] = $this->projeto_model->carrega_custo(intval($cd_projeto_custo));
			}

			$this->load->view('gestao/projeto/custo', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar_custo()
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/projeto_model');

			$cd_projeto_custo = $this->input->post('cd_projeto_custo', TRUE);

			$args['cd_projeto']        = $this->input->post('cd_projeto', TRUE);
			$args['ds_projeto_custo']  = $this->input->post('ds_projeto_custo', TRUE);
			$args['nr_valor']          = app_decimal_para_db($this->input->post('nr_valor', TRUE));
			$args['nr_valor_aprovado'] = app_decimal_para_db($this->input->post('nr_valor_aprovado', TRUE));
			$args['cd_usuario']        = $this->session->userdata('codigo');

			if(intval($cd_projeto_custo) == 0)
			{
				$this->projeto_model->salvar_custo($args);
			}
			else
			{
				$this->projeto_model->atualizar_custo(intval($cd_projeto_custo), $args);
			}

			redirect('gestao/projeto/custo/'.$args['cd_projeto'], 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	public function altera_ordem()
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/projeto_model');
			
			$cd_projeto_cronograma = $this->input->post('cd_projeto_cronograma', TRUE);
			$nr_ordem			   = $this->input->post('nr_ordem', TRUE);
			$cd_usuario			   = $this->session->userdata('codigo');
			
			$this->projeto_model->alterar_ordem($cd_projeto_cronograma, $nr_ordem, $cd_usuario);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function cronograma($cd_projeto, $cd_projeto_cronograma = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/divisoes');
			$this->load->model('gestao/projeto_model');

			$data = array();
			
			$data['row'] = $this->projeto_model->carrega(intval($cd_projeto));

			$data['gerencia'] = $this->projeto_model->get_gerencia();
			
			$data['collection'] = $this->projeto_model->listar_cronograma(intval($cd_projeto));
			
			foreach($data['collection'] as $key => $item)
			{
				foreach($this->projeto_model->listar_sub_cronogramas($item['cd_projeto'], $item['cd_projeto_cronograma']) as $sub_cronograma_nome)
				{
					$data['collection'][$key]['sub_cronograma_nome'][] = $sub_cronograma_nome['sub_cronograma'];
				}
			}
			
			foreach($data['collection'] as $key => $item)
			{
				foreach($this->projeto_model->gerencia_responsavel($item['cd_projeto_cronograma']) as $gerencia_lista)
				{
					$data['collection'][$key]['gerencia_lista'][] = $gerencia_lista['cd_gerencia'];
				}
			}
			
			if(intval($cd_projeto_cronograma) == 0)
			{
				$ordem = $this->projeto_model->get_ordem($cd_projeto);
				
				$data['cronograma'] = array(
					'nr_ordem' 					=> (isset($ordem['nr_ordem']) ? intval($ordem['nr_ordem']) : 1),
					'cd_projeto_cronograma'     => intval($cd_projeto_cronograma),
					'ds_projeto_cronograma'     => '',
					'cd_gerencia'				=> '',
					'dt_projeto_cronograma_ini'	=> '',
					'dt_projeto_cronograma_fim'	=> ''
				);
				
				$data['cronograma_gerencia'] = array();
			}
			else
			{
				$data['cronograma'] = $this->projeto_model->carrega_cronograma(intval($cd_projeto_cronograma));
				
				if(count($this->projeto_model->gerencia_responsavel(intval($cd_projeto_cronograma))) > 0)
				{
					foreach ($this->projeto_model->gerencia_responsavel(intval($cd_projeto_cronograma)) as $gerencia) 
					{				
						$data['cronograma_gerencia'][] = $gerencia['cd_gerencia'];
					}
				}
				else
				{
					$data['cronograma_gerencia'] = array();
				}
			}

			$this->load->view('gestao/projeto/cronograma', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	public function sub_cronograma($cd_projeto, $cd_projeto_cronograma_pai, $cd_projeto_cronograma = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/divisoes');
			$this->load->model('gestao/projeto_model');
			
			$data = array();				
			
			$data['row'] = $this->projeto_model->carrega(intval($cd_projeto));

			$data['gerencia'] = $this->projeto_model->get_gerencia();
			
			$data['cronograma'] = $this->projeto_model->carrega_cronograma(intval($cd_projeto_cronograma_pai));

			foreach ($this->projeto_model->gerencia_responsavel(intval($cd_projeto_cronograma_pai)) as $gerencia) 
			{				
				$data['cronograma_gerencia'][] = $gerencia['cd_gerencia'];
			}
			
			$data['collection'] = $this->projeto_model->listar_cronograma($cd_projeto, $cd_projeto_cronograma_pai);  
			
			foreach($data['collection'] as $key => $item)
			{
				foreach($this->projeto_model->listar_sub_cronogramas($item['cd_projeto'], $item['cd_projeto_cronograma']) as $sub_cronograma_nome)
				{
					$data['collection'][$key]['sub_cronograma_nome'][] = $sub_cronograma_nome['sub_cronograma'];
				}
			}
			
			foreach($data['collection'] as $key => $item)
			{
				foreach($this->projeto_model->gerencia_responsavel($item['cd_projeto_cronograma']) as $gerencia_lista)
				{
					$data['collection'][$key]['gerencia_lista'][] = $gerencia_lista['cd_gerencia'];
				}
			}
			
			if(intval($cd_projeto_cronograma) == 0)
			{
				$ordem = $this->projeto_model->get_ordem_sub_cronograma($cd_projeto, $cd_projeto_cronograma_pai);
				
				$data['sub_cronograma'] = array(
					'nr_ordem' 					=> (isset($ordem['nr_ordem']) ? $ordem['nr_ordem'] : 1),
					'cd_projeto_cronograma' 	=> intval($cd_projeto_cronograma),
					'ds_projeto_cronograma'    	=> '',
					'cd_gerencia'				=> '',
					'dt_projeto_cronograma_ini'	=> '',
					'dt_projeto_cronograma_fim'	=> ''
				);
				
				if(isset($data['cronograma_gerencia']))
				{
					$data['sub_cronograma_gerencia'] = $data['cronograma_gerencia'];
				}
				else
				{
					$data['sub_cronograma_gerencia'] = array();
				}
			}
			else
			{
				$data['sub_cronograma'] = $this->projeto_model->carrega_cronograma(intval($cd_projeto_cronograma));

				if(count($this->projeto_model->gerencia_responsavel(intval($cd_projeto_cronograma))) > 0)
				{
					foreach ($this->projeto_model->gerencia_responsavel(intval($cd_projeto_cronograma)) as $gerencia) 
					{				
						$data['sub_cronograma_gerencia'][] = $gerencia['cd_gerencia'];
					}
				}
				else
				{
					$data['sub_cronograma_gerencia'] = array();
				}
			}
			
			$this->load->view('gestao/projeto/sub_cronograma', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar_cronograma()
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/projeto_model');

			$args = array();

			$cd_projeto_cronograma = $this->input->post('cd_projeto_cronograma', TRUE);

			$args['nr_ordem']				   = $this->input->post('nr_ordem', TRUE);
			$args['cd_projeto']                = $this->input->post('cd_projeto', TRUE);
			$args['ds_projeto_cronograma']     = $this->input->post('ds_projeto_cronograma', TRUE);
			$args['cd_projeto_cronograma_pai'] = $this->input->post('cd_projeto_cronograma_pai', TRUE);
			$args['cd_gerencia']    		   = $this->input->post('cd_gerencia', TRUE);
			$args['dt_projeto_cronograma_ini'] = $this->input->post('dt_projeto_cronograma_ini', TRUE);
			$args['dt_projeto_cronograma_fim'] = $this->input->post('dt_projeto_cronograma_fim', TRUE);
			$args['cd_usuario']                = $this->session->userdata('codigo');

			if(is_array($this->input->post('gerencia', TRUE)))
			{
				$args['gerencia'] = $this->input->post('gerencia', TRUE);
			}
			else
			{
				$args['gerencia'] = array();
			}

			if(intval($cd_projeto_cronograma) == 0)
			{
				$this->projeto_model->salvar_cronograma($args);
			}
			else
			{
				$this->projeto_model->atualizar_cronograma(intval($cd_projeto_cronograma), $args);
			}
			
			if(intval($args['cd_projeto_cronograma_pai']) == 0)
			{
				redirect('gestao/projeto/cronograma/'.$args['cd_projeto'], 'refresh');
			}
			else
			{
				redirect('gestao/projeto/sub_cronograma/'.$args['cd_projeto'].'/'.$args['cd_projeto_cronograma_pai'], 'refresh');
			}
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	public function excluir_cronograma($cd_projeto, $cd_projeto_cronograma, $cd_projeto_cronograma_pai = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/projeto_model');

			$args = array();
			
			$cd_usuario = $this->session->userdata('codigo');
			
			$this->projeto_model->excluir_cronograma(intval($cd_projeto), intval($cd_projeto_cronograma), intval($cd_usuario));
			
			if(intval($cd_projeto_cronograma_pai) == 0)
			{
				redirect('gestao/projeto/cronograma/'.intval($cd_projeto), 'refresh');
			}
			else
			{
				redirect('gestao/projeto/sub_cronograma/'.intval($cd_projeto).'/'.intval($cd_projeto_cronograma_pai), 'refresh');
			}
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	public function cronograma_realizado($cd_projeto, $cd_projeto_cronograma)
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/divisoes');
			$this->load->model('gestao/projeto_model');

			$data = array();
			
			$data['row'] = $this->projeto_model->carrega(intval($cd_projeto));

			$data['cronograma'] = $this->projeto_model->carrega_cronograma(intval($cd_projeto_cronograma));

			$gerencia = $this->projeto_model->gerencia_responsavel(intval($cd_projeto_cronograma)); 
			
			$data['gerencia'] = array();
			
			foreach($gerencia as $item)
			{
				$data['gerencia'][] = $item['cd_gerencia'];
			}
			
			$this->load->view('gestao/projeto/cronograma_realizado', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	public function atualizar_cronograma_realizado()
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/projeto_model');

			$args = array();

			$cd_projeto_cronograma = $this->input->post('cd_projeto_cronograma', TRUE);

			$args['cd_projeto']                			 = $this->input->post('cd_projeto', TRUE);
			$args['dt_projeto_cronograma_realizado_ini'] = $this->input->post('dt_projeto_cronograma_realizado_ini', TRUE);
			$args['dt_projeto_cronograma_realizado_fim'] = $this->input->post('dt_projeto_cronograma_realizado_fim', TRUE);
			$args['cd_usuario']                			 = $this->session->userdata('codigo');

			$this->projeto_model->atualizar_cronograma_realizado(intval($cd_projeto_cronograma), $args);
			
			redirect('gestao/projeto/cronograma/'.$args['cd_projeto'], 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	public function grafico($cd_projeto)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/projeto_model');

			$data['row'] = $this->projeto_model->carrega(intval($cd_projeto));

			$data['collection'] = $this->projeto_model->listar_cronograma(intval($cd_projeto));

			$this->load->view('gestao/projeto/grafico', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	private function get_sub_cronogramas($cd_projeto, $cd_projeto_cronograma, &$array = array(), $nivel = 0)
	{
		$this->load->model('gestao/projeto_model');
		
		$nivel ++;
		
		$collection = $this->projeto_model->listar_sub_cronogramas($cd_projeto, $cd_projeto_cronograma);
		
		$i = count($array);
		
		foreach($collection as $key => $item)
		{
			$item['nivel'] = $nivel;
			
			$array[$i] = $item;
			$i++;
			
			$i = $this->get_sub_cronogramas($item['cd_projeto'], $item['cd_projeto_cronograma'], $array, $nivel);
		}
		return $i;
	}
	
	public function pdf($cd_projeto, $fl_debug = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/projeto_model');

			$projeto = $this->projeto_model->carrega(intval($cd_projeto));

			$gerencia_envolvida = array();

			foreach ($this->projeto_model->gerencia_envolvida(intval($cd_projeto)) as $gerencia) 
			{
				$gerencia_envolvida[] = $gerencia['cd_gerencia_envolvida'];
			}

			$custo = $this->projeto_model->listar_custo(intval($cd_projeto));

			$cronograma = $this->projeto_model->listar_cronograma(intval($cd_projeto));

			$indicador = $this->projeto_model->projeto_indicador(intval($cd_projeto));
			
			$this->load->plugin('fpdf');
			$ob_pdf = new PDF();
			$ob_pdf->AddFont('segoeuil');
			$ob_pdf->AddFont('segoeuib');
			$ob_pdf->SetNrPag(true);
			$ob_pdf->SetMargins(10, 14, 5);
			$ob_pdf->header_exibe = true;
			$ob_pdf->header_logo = true;

			$ob_pdf->AddPage('L');
	        $ob_pdf->SetY($ob_pdf->GetY() + 1);
	        $ob_pdf->SetFont('segoeuib', '', 12);
	        $ob_pdf->MultiCell(280, 4.5, 'PROJETO', 0, 'C');

	        $ob_pdf->SetFont('segoeuib', '', 10);
	        $ob_pdf->MultiCell(280, 4.5, '1 - NOME DO PROJETO:', 0, 'L');
	        $ob_pdf->SetFont('segoeuil', '', 10);
	        $ob_pdf->MultiCell(280, 4.5, $projeto['ds_projeto'], 0, 'L');

	        $ob_pdf->SetY($ob_pdf->GetY()+5);

	        $ob_pdf->SetFont('segoeuib', '', 10);
	        $ob_pdf->MultiCell(280, 4.5, '2 - OBJETIVO:', 0, 'L');
	        $ob_pdf->SetFont('segoeuil', '', 10);
	        $ob_pdf->MultiCell(280, 4.5, $projeto['objetivo'], 0, 'J');

	        $ob_pdf->SetY($ob_pdf->GetY()+5);

	        $ob_pdf->SetFont('segoeuib', '', 10);
	        $ob_pdf->MultiCell(280, 4.5, '3 - JUSTIFICATIVA:', 0, 'L');
	        $ob_pdf->SetFont('segoeuil', '', 10);
	        $ob_pdf->MultiCell(280, 4.5, $projeto['justificativa'], 0, 'J');

	        $ob_pdf->SetY($ob_pdf->GetY()+5);

	        $ob_pdf->SetFont('segoeuib', '', 10);
	        $ob_pdf->MultiCell(280, 4.5, '4 - RESPONSÁVEL:', 0, 'L');
	        $ob_pdf->SetFont('segoeuil', '', 10);
	        $ob_pdf->MultiCell(280, 4.5, $projeto['gerencia_resposanvel'], 0, 'L');

	        $ob_pdf->SetY($ob_pdf->GetY()+5);

	        $ob_pdf->SetFont('segoeuib', '', 10);
	        $ob_pdf->MultiCell(280, 4.5, '5 - ENVOLVIDOS:', 0, 'L');
	        $ob_pdf->SetFont('segoeuil', '', 10);
	        $ob_pdf->MultiCell(280, 4.5, implode(', ', $gerencia_envolvida), 0, 'L');

	        $ob_pdf->SetY($ob_pdf->GetY()+5);

	        $ob_pdf->SetFont('segoeuib', '', 10);
	        $ob_pdf->MultiCell(280, 4.5, '6 - CUSTOS PROJETADOS:', 0, 'L');

	        $ob_pdf->SetWidths(array(130, 75, 75));
            $ob_pdf->SetAligns(array('C', 'C', 'C'));
            $ob_pdf->SetFont('segoeuib', '', 10);
            $ob_pdf->Row(array('Item', 'Valor Proposto', 'Valor Aprovado'));
            $ob_pdf->SetAligns(array('L', 'R', 'R'));

            $ob_pdf->SetFont('segoeuil', '', 10);

            foreach($custo as $key => $item)
	        {
	        	$ob_pdf->Row(array(
        			$item['ds_projeto_custo'],
        			'R$ '.number_format($item['nr_valor'], 2, ',', '.'),
					($item['nr_valor_aprovado'] > 0 ? 'R$ ' : '').number_format($item['nr_valor_aprovado'], 2, ',', '.')
        		));
	        }
	        
	        $ob_pdf->SetY($ob_pdf->GetY()+5);


/*
	        $ob_pdf->SetFont('segoeuib', '', 10);
	        $ob_pdf->MultiCell(190, 4.5, '7 -  CRONOGRAMA:', 0, 'L');

	        $ob_pdf->SetWidths(array(190));
            $ob_pdf->SetAligns(array('L'));

            $ob_pdf->SetFont('segoeuil', '', 10);

			$array_ordenacao = array();
			
            foreach($cronograma as $key => $item)
	        {
				$array = array();
				$this->get_sub_cronogramas($item['cd_projeto'], $item['cd_projeto_cronograma'], $array);
				
				//echo $item['nr_ordem'].' - '.$item['ds_projeto_cronograma'].br();
				//echo '<pre>';
				//print_r($array);
				
				$ob_pdf->SetFont('segoeuib', '', 10);
				
	        	$ob_pdf->Row(array(
        			$item['nr_ordem'].' - '.$item['ds_projeto_cronograma']
        		));
				
				$ob_pdf->SetFont('segoeuil', '', 10);
				
				$nivel = '';
				
				foreach($array as $item2)
				{
					if(intval($nivel) > intval($item2['nivel']))
					{
						$i = intval($nivel);
						
						while($i >= intval($item2['nivel']))
						{
							unset($array_ordenacao[$i]);
							
							$i--;
						}
					}
					
					$array_ordenacao[$item2['nivel']] = $item2['nr_ordem'];
					
					$ob_pdf->Row(array(
						$item['nr_ordem'].'.'.implode('.', $array_ordenacao).' - '.$item2['sub_cronograma']
					));
					
					$nivel = $item2['nivel'];
				}
			}
			*/

			$ob_pdf->SetFont('segoeuib', '', 10);
	        $ob_pdf->MultiCell(190, 4.5, '7 -  CRONOGRAMA:', 0, 'L');

	        $ob_pdf->SetWidths(array(110, 25, 25, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10));
            $ob_pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));

            $ob_pdf->SetFont('segoeuil', '', 10);

			$ob_pdf->Row(array(
    			'Etapas',
    			'% Andamento',
    			'Envolvidos',
    			'Jan',
    			'Fev',
    			'Mar',
    			'Abr',
    			'Mai',
    			'Jun',
    			'Jul',
    			'Ago',
    			'Set',
    			'Out',
    			'Nov',
    			'Dez'
    		));

    		$ob_pdf->SetWidths(array(110, 25, 25, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10));
            $ob_pdf->SetAligns(array('L', 'R', 'L', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));

            $array_ordenacao = array();
			
            foreach($cronograma as $key => $item)
	        {
				$array = array();

				$teste = '';

				$this->get_sub_cronogramas($item['cd_projeto'], $item['cd_projeto_cronograma'], $array);

				$andamento = $this->projeto_model->andamento($item['cd_projeto'], $item['cd_projeto_cronograma']);

				foreach ($andamento as $key => $item3) 
				{
					$teste = ($item3['concluido'] > 0 ? ($item3['concluido']/$item3['previsto'])*100 : '');
				}

				if(count($array) > 0)
				{
					$ob_pdf->SetFont('segoeuib', '', 10);
				}
				else
				{
					$ob_pdf->SetFont('segoeuil', '', 10);
				}

				$arr_linha = array(
					$item['nr_ordem'].' - '.$item['ds_projeto_cronograma'],
					($teste > 0 ? number_format($teste, 2, ',', '.') : ''),
					$item['ds_envolvidos']
				);

				$i = 1;
	        	
	        	while($i <= 12)
				{
					if((intval($item['mes_realizado_ini']) <= $i) AND (intval($item['mes_realizado_fim']) >= $i))
					{
						$arr_linha[count($arr_linha)] = 'C';
					}
					else if((intval($item['mes_planejado_ini']) <= $i) AND (intval($item['mes_planejado_fim']) >= $i))
					{
						$arr_linha[count($arr_linha)] = 'P';
					}
					else
					{
						$arr_linha[count($arr_linha)] = '';
					}

					$i++;
				}

				$ob_pdf->Row($arr_linha);

				$ob_pdf->SetFont('segoeuil', '', 10);
				
				$nivel = '';

				foreach($array as $item2)
				{

					if(intval($nivel) > intval($item2['nivel']))
					{
						$i = intval($nivel);
						
						while($i >= intval($item2['nivel']))
						{
							unset($array_ordenacao[$i]);
							
							$i--;
						}
					}
					
					$array_ordenacao[$item2['nivel']] = $item2['nr_ordem'];

					$arr_linha = array(
						$item['nr_ordem'].'.'.implode('.', $array_ordenacao).' - '.$item2['sub_cronograma'],
						'',
						$item2['ds_envolvidos']
					);

					$i = 1;

					while($i <= 12)
					{
						if((intval($item2['mes_realizado_ini']) <= $i) AND (intval($item2['mes_realizado_fim']) >= $i))
						{   
							$arr_linha[count($arr_linha)] = 'C';
							
						}
						else if((intval($item2['mes_planejado_ini']) <= $i) AND (intval($item2['mes_planejado_fim']) >= $i))
						{   
							$arr_linha[count($arr_linha)] = 'P';
							
						}
						else
						{
							$arr_linha[count($arr_linha)] = '';
						}

						$i++;
					} 
					$ob_pdf->Row($arr_linha);
					
					$nivel = $item2['nivel'];
				}
			}

			$ob_pdf->SetY($ob_pdf->GetY()+5);

	        $ob_pdf->SetFont('segoeuib', '', 10);
	        $ob_pdf->MultiCell(280, 4.5, '8 - INDICADORES:', 0, 'L');
	        $ob_pdf->SetFont('segoeuil', '', 10);
	        
	        foreach($indicador as $key => $item)
	        {
	        	$ob_pdf->MultiCell(280, 4.5, $item['text'], 0, 'L');
	        }

	        $ob_pdf->MultiCell(280, 4.5, $projeto['ds_indicador'], 0, 'L');

	        $ob_pdf->Output();
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}