<?php
class Gerencia_unidade extends Controller
{
    function __construct()
    {
		parent::Controller();

		CheckLogin();
	}

	private function get_permissao()
    {
        if(gerencia_in(array('GTI')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    private function get_tipo()
    {
    	return array(
    		array('value' => 'DIV', 'text' => 'Gerência'),
    		array('value' => 'COM', 'text' => 'Comitê'),
    		array('value' => 'CON', 'text' => 'Conselho'),
    		array('value' => 'OUT', 'text' => 'Outro')
    	);
    }

    private function get_diretoria()
    {
    	return array(
    		array('value' => 'SEG', 'text' => 'Diretoria de Previdência'),
    		array('value' => 'FIN', 'text' => 'Diretoria Financeiro'),
    		/*array('value' => 'ADM', 'text' => 'Diretoria Administrativo'),*/
    		array('value' => 'PRE', 'text' => 'Presidência')
    	);
    }

	public function index()
    {
        if($this->get_permissao())
        {
        	$this->load->model('projetos/gerencia_unidade_model');

        	$data = array(
        		'tipo'      => $this->get_tipo(),
        		'diretoria' => $this->get_diretoria()
        	);

            $this->load->view('servico/gerencia_unidade/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function listar()
    {
		$this->load->model('projetos/gerencia_unidade_model');

    	$args = array(
            'fl_tipo'     => $this->input->post('fl_tipo', TRUE),
            'fl_area'     => $this->input->post('fl_area', TRUE)
    	);
			
		manter_filtros($args);

		$data['collection'] = $this->gerencia_unidade_model->listar($args);

		foreach($data['collection'] as $key => $item) 
		{
			$data['collection'][$key]['unidade'] = array();

			foreach($this->gerencia_unidade_model->get_unidade_gerencia($item['codigo']) as $unidade) 
			{
				$data['collection'][$key]['unidade'][] = $unidade['ds_descricao'];
			}

            $data['collection'][$key]['supervisor'] = array();

            foreach($this->gerencia_unidade_model->get_supervisor($item['codigo']) as $unidade) 
            {
                $data['collection'][$key]['supervisor'][] = $unidade['nome'];
            }
		}

        $this->load->view('servico/gerencia_unidade/index_result', $data);
    }

    public function cadastro($cd_gerencia = '')
    {
    	if($this->get_permissao())
        {
        	$this->load->model('projetos/gerencia_unidade_model');

        	$data = array(
        		'tipo'      => $this->get_tipo(),
        		'diretoria' => $this->get_diretoria()
        	);

        	if(trim($cd_gerencia) == '')
            {
            	$data['row'] = array(
                    'codigo'          => trim($cd_gerencia),
                    'nome'            => '', 
                    'area'            => '',
                    'tipo'            => '',
                    'fl_atividade'    => '',
                    'dt_vigencia_ini' => ''
                );
            }
            else
            {
            	$data['row'] = $this->gerencia_unidade_model->carrega($cd_gerencia);
            }

            $this->load->view('servico/gerencia_unidade/cadastro', $data);

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
            $this->load->model('projetos/gerencia_unidade_model');

        	$codigo = $this->input->post('codigo_h', TRUE);

        	$args = array(
        		'codigo'          => $this->input->post('codigo', TRUE),
                'nome'            => $this->input->post('nome', TRUE),
                'area'            => $this->input->post('area', TRUE),
                'tipo'            => $this->input->post('tipo', TRUE),
                'dt_vigencia_ini' => $this->input->post('dt_vigencia_ini', TRUE),
                'dt_vigencia_fim' => $this->input->post('dt_vigencia_fim', TRUE),
                'fl_atividade'    => $this->input->post('fl_atividade', TRUE),
        		'cd_usuario'      => $this->session->userdata('codigo')
        	);

        	if(trim($codigo) == '')
    		{
    			$codigo = $this->input->post('codigo', TRUE);

        		$this->gerencia_unidade_model->salvar($args);
            } 
    		else
    		{
    			$this->gerencia_unidade_model->atualizar(trim($codigo), $args);
    		}

    		redirect('servico/gerencia_unidade/cadastro/'.$codigo, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function unidade($cd_gerencia, $cd_gerencia_unidade = '')
    {
    	if($this->get_permissao())
        {
        	$this->load->model('projetos/gerencia_unidade_model');

        	$data = array(
        		'gerencia'   => $this->gerencia_unidade_model->carrega($cd_gerencia),
        		'collection' => $this->gerencia_unidade_model->get_unidade_gerencia($cd_gerencia)
        	);

        	foreach($data['collection'] as $key => $item) 
			{
				$data['collection'][$key]['usuario'] = array();

				foreach($this->gerencia_unidade_model->get_usuario_unidade($item['cd_gerencia_unidade']) as $usuario) 
				{
					$data['collection'][$key]['usuario'][] = $usuario['nome'];
				}
			}

        	if(trim($cd_gerencia_unidade) == '')
            {
            	$data['row'] = array(
                    'cd_gerencia_unidade' => trim($cd_gerencia_unidade),
                    'ds_descricao'        => '',
                    'dt_vigencia_ini'     => '',
                    'ds_email'            => ''
                );
            }
            else
            {
            	$data['row'] = $this->gerencia_unidade_model->carrega_unidade($cd_gerencia_unidade);
            }

            $this->load->view('servico/gerencia_unidade/unidade', $data);

        }
        else
        {
        	exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_unidade()
    {
    	if($this->get_permissao())
        {
            $this->load->model('projetos/gerencia_unidade_model');

        	$cd_gerencia_unidade = $this->input->post('cd_gerencia_unidade_h', TRUE);

        	$args = array(
        		'cd_gerencia_unidade' => $this->input->post('cd_gerencia_unidade', TRUE),
                'cd_gerencia'         => $this->input->post('codigo', TRUE),
                'ds_descricao'        => $this->input->post('ds_descricao', TRUE),
                'dt_vigencia_ini'     => $this->input->post('dt_vigencia_ini', TRUE),
                'dt_vigencia_fim'     => $this->input->post('dt_vigencia_fim', TRUE),
                'ds_email'            => $this->input->post('ds_email', TRUE),
        		'cd_usuario'          => $this->session->userdata('codigo')
        	);

        	if(trim($cd_gerencia_unidade) == '')
    		{
    			$cd_gerencia_unidade = $this->input->post('cd_gerencia_unidade', TRUE);

        		$this->gerencia_unidade_model->salvar_unidade($args);
            } 
    		else
    		{
    			$this->gerencia_unidade_model->atualizar_unidade(trim($cd_gerencia_unidade), $args);
    		}

    		redirect('servico/gerencia_unidade/unidade/'.$args['cd_gerencia'], 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function mapa()
    {
    	if($this->get_permissao())
        {
        	$this->load->model('projetos/gerencia_unidade_model');

			$args = array(
                'fl_tipo'     => 'DIV',
                'fl_area'     => ''
    	    );

			$data['collection'] = $this->gerencia_unidade_model->listar($args);	

			foreach($data['collection'] as $key => $item) 
			{
				$data['collection'][$key]['unidade'] = array();
                $data['collection'][$key]['usuario'] = array();
                                   
                $usuario = $this->gerencia_unidade_model->get_usuario_sem_unidade($item['codigo']);

                foreach ($usuario as $key1=> $usuario) 
				{
					if(trim($usuario['divisao']) == 'DE')
                    {
                        $data['collection'][$key]['usuario'][] = $usuario['ds_diretoria'];
                    }
                    else
                    {
                       $data['collection'][$key]['usuario'][] = $usuario['nome']; 
                    }
                }

                $data['collection'][$key]['supervisor'] = array();

                foreach($this->gerencia_unidade_model->get_supervisor_gerencia($item['codigo']) as $key2 => $supervisor) 
                {
                    $data['collection'][$key]['supervisor'][] = $supervisor['nome'];
                }

				$gerencia = $this->gerencia_unidade_model->get_unidade_gerencia($item['codigo']);

				foreach($gerencia as $key2 => $unidade) 
				{
                    if(count($unidade['ds_descricao']) > 0)
					{
						$data['collection'][$key]['unidade']['usuario_unidade'][] = '<font style="font-weight:bold; color:#0000FF">'.$unidade['ds_descricao'].'</font>';
					} 

                    $supervisor_unidade = $this->gerencia_unidade_model->get_supervisor_unidade($unidade['cd_gerencia_unidade']);

                    foreach($supervisor_unidade as $key3 => $usuario_unidade)
                    {
                        $data['collection'][$key]['unidade']['usuario_unidade'][] = '<b>Supervisor: '.$usuario_unidade['nome'].'</b>';
                    }
					
					$usuario_gerencia = $this->gerencia_unidade_model->get_cd_unidade($unidade['cd_gerencia_unidade']);

				    foreach($usuario_gerencia as $key3 => $usuario_unidade)
					{
						$data['collection'][$key]['unidade']['usuario_unidade'][] = '&emsp;&emsp;&emsp;'.$usuario_unidade['nome'];
					}


				}
			}	

          	$this->load->view('servico/gerencia_unidade/mapa',$data);
    	}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function pdf()
    { 	
     	$this->load->model('projetos/gerencia_unidade_model');
        
        $this->load->plugin('fpdf');
        $ob_pdf = new PDF('P', 'mm', 'A4');
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetNrPagDe(true);
        $ob_pdf->SetMargins(10,20,5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = 'Gerência - Unidade';
        $w = 130;

        $ob_pdf->AddPage();
        $args = array(
            'fl_tipo'     => 'DIV',
            'fl_area'     => ''
        );        

        $gerencias = $this->gerencia_unidade_model->listar($args);

        foreach($gerencias as $item)
        {       
            $unidade = $this->gerencia_unidade_model->get_unidade_gerencia($item['codigo']);

	        $uni     = '';
			$usuario = '';
            $usu_unidade = '';

            $ob_pdf->SetY($ob_pdf->GetY() + 3);
            $ob_pdf->SetFont('segoeuib', '', 13);
            $ob_pdf->MultiCell($i = 130, 3, $item['ds_gerencia'],0, 1);
            $ob_pdf->SetY($ob_pdf->GetY() + 3);

            if(trim($item['ds_gerente']) != '')
            {
            	$ob_pdf->SetFont('segoeuib', '', 11);
                $ob_pdf->MultiCell($i = 130,3, 'Gerente: '.$item['ds_gerente'], '0');
                $ob_pdf->SetY($ob_pdf->GetY() + 3);
            }
            
            if(trim($item['ds_substituto']) != '')
            {
				$ob_pdf->SetFont('segoeuib', '', 11);
	            $ob_pdf->MultiCell($i = 130, 3, 'Substituto: '.$item['ds_substituto'], '0');
	            $ob_pdf->SetY($ob_pdf->GetY() + 3);
            } 

            $usuario = $this->gerencia_unidade_model->get_usuario_sem_unidade($item['codigo']);

            foreach ($usuario as $key1=> $usuario_s_unidade) 
            {
                if($usuario_s_unidade['divisao'] == 'DE')
                {
                    $ob_pdf->SetFont('segoeuib', '', 10);
                    $ob_pdf->SetX(10);
                    $ob_pdf->MultiCell($i = 130,3, $usuario_s_unidade['observacao'], '0');
                    $ob_pdf->SetFont('segoeuil', '', 10);
                    $ob_pdf->SetX(20);
                    $ob_pdf->MultiCell($i = 130,4, $usuario_s_unidade['nome'], '0');
                    $ob_pdf->SetY($ob_pdf->GetY() + 3);
                }
                else
                {
                    $ob_pdf->SetFont('segoeuil', '', 10);
                    $ob_pdf->MultiCell($i = 130, 2, $usuario_s_unidade['nome'], '0');
                    $ob_pdf->SetY($ob_pdf->GetY() + 3);
                }                
            }

            foreach($unidade as $key1 => $item2) 
			{
				$uni = $item2['ds_descricao'];

				if(trim($item2['cd_gerencia_unidade']) != '')
				{
					$referencia = $item2['cd_gerencia_unidade'];
				}
				else
				{
					$referencia = $item2['codigo'];
				}

				if(trim($item2['ds_descricao']) != '')
	            {
	            	$ob_pdf->SetFont('segoeuib', '', 10);
		            $ob_pdf->MultiCell($i = 130,3, $uni, '0', 'L');
		            $ob_pdf->SetY($ob_pdf->GetY() + 1);
	            } 
			
				$usuario_gerencia = $this->gerencia_unidade_model->get_cd_unidade($referencia);

                foreach($usuario_gerencia as $key2 => $item1)
				{                      
				    $usuario = $item1['nome'];

					$ob_pdf->SetFont('segoeuil', '', 10);
                    $ob_pdf->SetX(20);
		            $ob_pdf->MultiCell($i = 130, 2, $usuario, '0', 'L');
                    $ob_pdf->SetY($ob_pdf->GetY() + 3);	
                }
			}
        }        
      
        $ob_pdf->Output('MAPA.pdf', 'I');
        exit;
    }
}
?>