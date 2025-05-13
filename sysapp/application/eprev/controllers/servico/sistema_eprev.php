<?php
class Sistema_eprev extends Controller
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

    public function get_usuarios()
    {       
        $this->load->model('eprev/sistema_model');

        $cd_gerencia = $this->input->post('cd_gerencia', TRUE);

        echo json_encode($this->sistema_model->get_usuarios($cd_gerencia));
    }

    public function index()
    {
        if($this->get_permissao())
        {
            $this->load->model('eprev/sistema_model');

            $data = array(
                'responsavel' => $this->sistema_model->get_responsavel(),
                'solicitante' => $this->sistema_model->get_solicitante(),
                'gerencia'    => $this->sistema_model->get_gerencia()
            );

            $this->load->view('servico/sistema_eprev/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NГO PERMITIDO');
        }
	}

    public function listar()
    {
		$this->load->model('eprev/sistema_model');

    	$args = array(
            'ds_sistema'              => $this->input->post('ds_sistema', TRUE),
            'cd_usuario_solicitante'  => $this->input->post('cd_usuario_solicitante', TRUE),
            'cd_usuario_responsavel'  => $this->input->post('cd_usuario_responsavel', TRUE),
            'fl_publicado'            => $this->input->post('fl_publicado', TRUE),
            'cd_gerencia_responsavel' => $this->input->post('cd_gerencia_responsavel', TRUE)
    	);
			
		manter_filtros($args);

		$data['collection'] = $this->sistema_model->listar($args);

        $this->load->view('servico/sistema_eprev/index_result', $data);
    }

    public function cadastro($cd_sistema = 0)
    {
    	if($this->get_permissao())
        {
            $this->load->model('eprev/sistema_model');
            
            if(intval($cd_sistema) == 0)
    		{
    			$data['row'] = array(
    				'cd_sistema'              => intval($cd_sistema),
    				'ds_sistema'              => '',
                    'cd_gerencia_responsavel' => '',
                    'cd_usuario_responsavel'  => '',
                    'cd_usuario_solicitante'  => '',
                    'ds_controller'           => '',
                    'dt_publicacao'           => '',
                    'ds_descricao'            => ''
    			);  

                $data['responsavel'] = array();
                $data['solicitante'] = array();
    		}
    		else
    		{
                $data['row'] = $this->sistema_model->carrega($cd_sistema);

                $data['solicitante'] = $this->sistema_model->get_usuarios($data['row']['cd_gerencia_responsavel']);
                $data['responsavel'] = $this->sistema_model->get_usuarios($data['row']['cd_gerencia_responsavel']);
            }

    		$this->load->view('servico/sistema_eprev/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NГO PERMITIDO');
        }           

    }

    public function salvar()
    {
    	$this->load->model('eprev/sistema_model');

        $cd_sistema = $this->input->post('cd_sistema', TRUE);

    	$args = array(
            'cd_sistema'              => $cd_sistema,
            'ds_sistema'              => $this->input->post('ds_sistema', TRUE),
            'cd_gerencia_responsavel' => $this->input->post('cd_gerencia_responsavel', TRUE),
            'cd_usuario_solicitante'  => $this->input->post('cd_usuario_solicitante', TRUE),
            'cd_usuario_responsavel'  => $this->input->post('cd_usuario_responsavel', TRUE),
            'dt_publicacao'           => $this->input->post('dt_publicacao', TRUE),
            'ds_controller'           => $this->input->post('ds_controller', TRUE),
            'ds_descricao'            => $this->input->post('ds_descricao', TRUE),
            'cd_usuario'              => $this->session->userdata('codigo')
    	);

        if(trim($cd_sistema) == 0)
        {
            $cd_sistema = $this->sistema_model->salvar($args);
        }
        else
        {
            $this->sistema_model->atualizar(intval($cd_sistema), $args); 
        }

		redirect('servico/sistema_eprev/metodo/'.$cd_sistema, 'refresh');
    }

    public function acompanhamento($cd_sistema, $cd_sistema_acompanhamento = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('eprev/sistema_model');

            $data = array(
                'sistema'    => $this->sistema_model->carrega($cd_sistema),
                'collection' => $this->sistema_model->listar_acompanhamento($cd_sistema)
            );
                   
            if(trim($cd_sistema_acompanhamento) == 0)
            {
                $data['row'] = array(
                    'cd_sistema'                => intval($cd_sistema),
                    'cd_sistema_acompanhamento' => intval($cd_sistema_acompanhamento),
                    'ds_acompanhamento'         => ''
                );  
            }
            else
            {
                $data['row'] = $this->sistema_model->carrega_acompanhamento($cd_sistema_acompanhamento);
            }

            $this->load->view('servico/sistema_eprev/acompanhamento', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NГO PERMITIDO');
        }
    }

    public function salvar_acompanhamento()
    {
        $this->load->model('eprev/sistema_model');

        $cd_sistema_acompanhamento = $this->input->post('cd_sistema_acompanhamento', TRUE);

        $cd_sistema = $this->input->post('cd_sistema', TRUE);

        $args = array(
            'cd_sistema_acompanhamento' => $cd_sistema_acompanhamento,
            'cd_sistema'                => $cd_sistema,
            'ds_acompanhamento'         => $this->input->post('ds_acompanhamento', TRUE),
            'cd_usuario'                => $this->session->userdata('codigo')
        );

        if(trim($cd_sistema_acompanhamento) == 0)
        {
            $cd_sistema_acompanhamento = $this->sistema_model->salvar_acompanhamento($args);
        }
        else
        {
            $this->sistema_model->atualizar_acompanhamento(intval($cd_sistema_acompanhamento), $args); 
        }

        redirect('servico/sistema_eprev/acompanhamento/'.intval($cd_sistema), 'refresh');
    }

    public function excluir_acompanhamento($cd_sistema, $cd_sistema_acompanhamento)
    {
        if($this->get_permissao())
        {
            $this->load->model('eprev/sistema_model');

            $this->sistema_model->excluir_acompanhamento($cd_sistema_acompanhamento, $this->session->userdata('codigo'));

            redirect('servico/sistema_eprev/acompanhamento/'.intval($cd_sistema), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NГO PERMITIDO');
        }
    }

    public function valida_atividade()
    {
        $this->load->model('eprev/sistema_model');

        $cd_atividade = $this->input->post('cd_atividade', TRUE);

        $data = $this->sistema_model->valida_atividade($cd_atividade);

        echo json_encode($data);
    }

    public function valida_evento()
    {
        $this->load->model('eprev/sistema_model');

        $cd_evento = $this->input->post('cd_evento', TRUE);

        $data = $this->sistema_model->valida_evento($cd_evento);

        echo json_encode($data);
    }

    public function atividade($cd_sistema, $cd_sistema_atividade = 0)
    {
       if($this->get_permissao())
        {
            $this->load->model('eprev/sistema_model');

            $data = array(
                'sistema'    => $this->sistema_model->carrega($cd_sistema),
                'collection' => $this->sistema_model->listar_atividade($cd_sistema)
            );             
                      
            if(intval($cd_sistema_atividade) == 0)
            {
                $data['row'] = array(
                    'cd_sistema'           => intval($cd_sistema),
                    'cd_sistema_atividade' => intval($cd_sistema_atividade),
                    'cd_atividade'         => ''
                );  
            }
            else
            {
                $data['row'] = $this->sistema_model->carrega_atividade($cd_sistema_atividade);
            }

            $this->load->view('servico/sistema_eprev/atividade', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NГO PERMITIDO');
        }
    }  

    public function salvar_atividade()
    {
        $this->load->model('eprev/sistema_model');

        $cd_sistema_atividade = $this->input->post('cd_sistema_atividade', TRUE);

        $cd_sistema = $this->input->post('cd_sistema', TRUE);

        $args = array(
            'cd_sistema_atividade' => $cd_sistema_atividade,
            'cd_sistema'           => $cd_sistema,
            'cd_atividade'         => $this->input->post('cd_atividade', TRUE),
            'cd_usuario'           => $this->session->userdata('codigo')
        );

        if(trim($cd_sistema_atividade) == 0)
        {
            $cd_sistema_atividade = $this->sistema_model->salvar_atividade($args);
        }
        
        redirect('servico/sistema_eprev/atividade/'.intval($cd_sistema), 'refresh');
    } 

    public function excluir_atividade($cd_sistema, $cd_sistema_atividade)
    {
        if($this->get_permissao())
        {
            $this->load->model('eprev/sistema_model');

            $this->sistema_model->excluir_atividade($cd_sistema_atividade, $this->session->userdata('codigo'));

            redirect('servico/sistema_eprev/atividade/'.intval($cd_sistema), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NГO PERMITIDO');
        }
    }

    public function anexo($cd_sistema)
    {
        if($this->get_permissao())
        {
            $this->load->model('eprev/sistema_model');

            $data = array(
                'sistema'    => $this->sistema_model->carrega($cd_sistema),
                'arquivo'    => $this->sistema_model->anexo_carrega($cd_sistema),
                'collection' => $this->sistema_model->anexo_listar($cd_sistema)
            );

            $this->load->view('servico/sistema_eprev/anexo', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NГO PERMITIDO');
        }
    }

    public function anexo_salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('eprev/sistema_model');
            
            $cd_sistema = $this->input->post('cd_sistema', TRUE);
                
            $args = array(
                'cd_sistema'          => $this->input->post('cd_sistema', TRUE),
                'arquivo_nome'        => $this->input->post('arquivo_nome', TRUE),
                'arquivo'             => $this->input->post('arquivo', TRUE),
                'ds_sistema_arquivo'  => $this->input->post('ds_sistema_arquivo', TRUE),
                'cd_usuario'          => $this->session->userdata('codigo')
            ); 
                                  
            $this->sistema_model->anexo_salvar($args);
                   
            redirect('servico/sistema_eprev/anexo/'.$cd_sistema, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NГO PERMITIDO');
        }
    }

    public function anexo_excluir($cd_sistema, $cd_sistema_arquivo)
    {
        if($this->get_permissao())
        {
            $this->load->model('eprev/sistema_model');

            $this->sistema_model->anexo_excluir($cd_sistema, $cd_sistema_arquivo, $this->session->userdata('codigo'));
            
            redirect('servico/sistema_eprev/anexo/'.$cd_sistema, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NГO PERMITIDO');
        }
    } 

    public function rotina($cd_sistema, $cd_sistema_rotina = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('eprev/sistema_model');

            $data = array(
                'sistema'    => $this->sistema_model->carrega($cd_sistema),
                'collection' => $this->sistema_model->rotina_listar($cd_sistema)
            );

            if(intval($cd_sistema_rotina) == 0)
            {
                $data['row'] = array(
                    'cd_sistema'        => intval($cd_sistema),
                    'cd_sistema_rotina' => intval($cd_sistema_rotina),
                    'cd_evento'         => '',
                    'ds_descricao'      => '',
                    'ds_sistema_rotina' => '',
                    'ds_job'            => '',
                    'ds_execucao'       => ''
                ); 
            }
            else
            {
                $data['row'] = $this->sistema_model->rotina_carrega($cd_sistema_rotina);
            }
          
             $this->load->view('servico/sistema_eprev/rotina', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NГO PERMITIDO');
        }
    }

    public function rotina_salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('eprev/sistema_model');
            
            $cd_sistema = $this->input->post('cd_sistema', TRUE);

            $cd_sistema_rotina = $this->input->post('cd_sistema_rotina', TRUE);
            
            $args = array(
                'cd_sistema'        => $this->input->post('cd_sistema', TRUE),
                'cd_evento'         => $this->input->post('cd_evento', TRUE),
                'ds_sistema_rotina' => $this->input->post('ds_sistema_rotina', TRUE),
                'ds_descricao'      => $this->input->post('ds_descricao', TRUE),
                'ds_job'            => $this->input->post('ds_job', TRUE),
                'ds_execucao'       => $this->input->post('ds_execucao', TRUE),
                'cd_usuario'        => $this->session->userdata('codigo')
            ); 

            if(intval($cd_sistema_rotina) == 0)
            {
                $this->sistema_model->rotina_salvar($args);
            }
            else
            {
                $this->sistema_model->atualizar_rotina($cd_sistema_rotina, $args);
            }              
                          
            redirect('servico/sistema_eprev/rotina/'.$cd_sistema, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NГO PERMITIDO');
        }
    }

    public function pdf($cd_sistema)
    {
        $this->load->model('eprev/sistema_model');

		$row = $this->sistema_model->carrega($cd_sistema);

		$atividade = $this->sistema_model->listar_atividade($cd_sistema);

		$rotina = $this->sistema_model->rotina_listar($cd_sistema);

		$acompanhamento = $this->sistema_model->listar_acompanhamento($cd_sistema);

        $metodo = $this->sistema_model->metodo_listar($cd_sistema);

        $anexo = $this->sistema_model->anexo_listar($cd_sistema);

    //  $pendencia = $this->sistema_model->pendencia_listar($cd_sistema);

		$this->load->plugin('fpdf');
        $ob_pdf = new PDF('L', 'mm', 'A4');
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetNrPagDe(true);
        $ob_pdf->SetMargins(10, 15, 5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = $row['ds_sistema'];

        $ob_pdf->AddPage();
        $ob_pdf->SetY($ob_pdf->GetY() + 3);
        $ob_pdf->SetFont('segoeuib', '', 13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), 'Gerencia Responsбvel: ', '0','L');
        $ob_pdf->SetFont('segoeuil', '', 13);
        $ob_pdf->Text(60, $ob_pdf->GetY(), $row['cd_gerencia_responsavel'], '0', '');
        $ob_pdf->SetY($ob_pdf->GetY() + 5);

      	$ob_pdf->SetFont('segoeuib', '', 13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), 'Responsбvel: ', '0', 'L');
        $ob_pdf->SetFont('segoeuil', '', 13);
        $ob_pdf->Text(40, $ob_pdf->GetY(), $row['ds_responsavel']);
        $ob_pdf->SetY($ob_pdf->GetY() + 5); 

        $ob_pdf->SetFont('segoeuib', '', 13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), 'Solicitante: ', '0', 'L');
        $ob_pdf->SetFont('segoeuil','',13);
        $ob_pdf->Text(36, $ob_pdf->GetY(), $row['ds_solicitante']);
        $ob_pdf->SetY($ob_pdf->GetY() + 5);

        if(trim($row['dt_publicacao']) != '')
        {
            $ob_pdf->SetFont('segoeuib','',13);
            $ob_pdf->Text(10, $ob_pdf->GetY(), 'Dt. Publicaзгo: ', '0', 'L');
            $ob_pdf->SetFont('segoeuil', '', 13);
            $ob_pdf->Text(43, $ob_pdf->GetY(), $row['dt_publicacao']);
            $ob_pdf->SetY($ob_pdf->GetY() + 5);
        }
        
        if(trim($row['ds_descricao']) != '')
        {
            $ob_pdf->SetFont('segoeuib', '', 13);
            $ob_pdf->Text(10, $ob_pdf->GetY(), 'Descriзгo: ', '0', 'L');
            $ob_pdf->SetY($ob_pdf->GetY() + 3);
            $ob_pdf->SetFont('segoeuil', '', 13);
            $ob_pdf->MultiCell(280, 6, $row['ds_descricao'], '0', 'L');
            $ob_pdf->SetY($ob_pdf->GetY() + 5);
        }

        $ob_pdf->SetY($ob_pdf->GetY() + 3);

        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(280, 6, '1 - Lista dos Mйtodos', '0', 'L');
        $ob_pdf->SetFont('segoeuib', '', 12);

        $ob_pdf->SetY($ob_pdf->GetY() + 3);       

        $ob_pdf->SetWidths(array(10, 90, 90, 90));
        $ob_pdf->SetAligns(array('C','C', 'C', 'C'));
        $ob_pdf->SetFont('segoeuib', '', 11);
        $ob_pdf->Row(array('Cуd','Mйtodos', 'Descriзгo', 'Evento'));

        foreach($metodo as $item)
        {
            $ob_pdf->SetAligns(array('C','J', 'J', 'J'));
            $ob_pdf->SetFont('segoeuil', '', 10);

            $ob_pdf->Row(array(
                $item['cd_sistema_metodo'],
                $item['ds_sistema_metodo'], 
                $item['ds_descricao'], 
                $item['ds_evento']
            ));
        }

        $ob_pdf->SetY($ob_pdf->GetY() + 5);
        
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(280, 6, '2 - Lista das Rotinas', '0', 'L');
        $ob_pdf->SetFont('segoeuib', '', 12);

        $ob_pdf->SetY($ob_pdf->GetY() + 3); 

        $ob_pdf->SetWidths(array(55, 55, 55, 55, 60));
        $ob_pdf->SetAligns(array('C', 'C', 'C', 'C', 'C'));
        $ob_pdf->SetFont('segoeuib', '', 11);
        $ob_pdf->Row(array('Rotina', 'Job', 'Cуd. Evento', 'Execuзгo', 'Descriзгo'));

        foreach($rotina as $item)
        {
            $ob_pdf->SetAligns(array('J', 'J', 'C', 'J', 'J'));
            $ob_pdf->SetFont('segoeuil', '', 10);

            $ob_pdf->Row(array(
                $item['ds_sistema_rotina'], 
                $item['ds_job'], 
                $item['ds_evento'], 
                $item['ds_execucao'],
                $item['ds_descricao']
            ));
        }

        $ob_pdf->SetY($ob_pdf->GetY() + 5);

        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(280, 6, '3 - Lista das Atividades', '0', 'L');
        $ob_pdf->SetFont('segoeuib', '', 12);

        $ob_pdf->SetY($ob_pdf->GetY() + 3);       

        $ob_pdf->SetWidths(array(15, 20, 55, 55, 105, 30));
        $ob_pdf->SetAligns(array('C', 'C', 'C', 'C', 'C',  'C'));
        $ob_pdf->SetFont('segoeuib', '', 11);
        $ob_pdf->Row(array('N°', 'Data', 'Solicitante', 'Atendente', 'Descriзгo', 'Dt. Conclusгo'));

        foreach($atividade as $item)
        {
            $ob_pdf->SetAligns(array('C', 'C', 'J', 'J', 'J', 'C'));
            $ob_pdf->SetFont('segoeuil', '', 10);

            $ob_pdf->Row(array(
                $item['cd_atividade'], 
                $item['dt_cad'], 
                $item['ds_solicitante'], 
                $item['ds_atendente'],
                $item['descricao'],
                $item['dt_conclusao']
            ));
        }

        $ob_pdf->SetY($ob_pdf->GetY() + 5);

        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(280, 6, '4 - Lista dos Acompanhamento', '0', 'L');
        $ob_pdf->SetFont('segoeuib', '', 12);

        $ob_pdf->SetY($ob_pdf->GetY() + 3); 

        $ob_pdf->SetWidths(array(280));
        $ob_pdf->SetAligns(array('C'));
        $ob_pdf->SetFont('segoeuib', '', 11);
        $ob_pdf->Row(array('Descriзгo'));

        foreach($acompanhamento as $item)
        {
            $ob_pdf->SetAligns(array('J'));
            $ob_pdf->SetFont('segoeuil','',10);
         
            $ob_pdf->Row(array($item['ds_acompanhamento']));
        }

        $ob_pdf->SetY($ob_pdf->GetY() + 5);

        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(280, 6, '5 - Lista dos Anexo', '0', 'L');
        $ob_pdf->SetFont('segoeuib', '', 12);

        $ob_pdf->SetY($ob_pdf->GetY() + 3); 

        $ob_pdf->SetWidths(array(200,80));
        $ob_pdf->SetAligns(array('C', 'C'));
        $ob_pdf->SetFont('segoeuib', '', 11);
        $ob_pdf->Row(array('Descriзгo', 'Arquivo'));

        foreach($anexo as $item)
        {
            $ob_pdf->SetAligns(array('J', 'J'));
            $ob_pdf->SetFont('segoeuil', '', 10);
            $ob_pdf->Row(array(
                $item['ds_sistema_arquivo'], 
                $item['arquivo_nome']
            ));
        }

        $ob_pdf->Output();
        exit;
    }

    public function metodo($cd_sistema, $cd_sistema_metodo = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('eprev/sistema_model');

            $data = array(
                'sistema'    => $this->sistema_model->carrega($cd_sistema),
                'collection' => $this->sistema_model->metodo_listar($cd_sistema) 
            );

            if(intval($cd_sistema_metodo) == 0)
            {
                $data['row'] = array(
                    'cd_sistema'        => intval($cd_sistema),
                    'cd_sistema_metodo' => intval($cd_sistema_metodo),
                    'cd_evento'         => '',
                    'ds_descricao'      => '',
                    'ds_sistema_metodo' => '',
                    'nr_ordem'          => ''
                ); 
            }
            else
            {
               $data['row'] = $this->sistema_model->metodo_carrega($cd_sistema_metodo);
            }
          
             $this->load->view('servico/sistema_eprev/metodo', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NГO PERMITIDO');
        }
    }

    public function metodo_salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('eprev/sistema_model');
            
            $cd_sistema = $this->input->post('cd_sistema', TRUE);

            $cd_sistema_metodo = $this->input->post('cd_sistema_metodo', TRUE);
            
            $args = array(
                'cd_sistema'        => $this->input->post('cd_sistema', TRUE),
                'cd_evento'         => $this->input->post('cd_evento', TRUE),
                'nr_ordem'          => $this->input->post('nr_ordem', TRUE),
                'ds_sistema_metodo' => $this->input->post('ds_sistema_metodo', TRUE),
                'ds_descricao'      => $this->input->post('ds_descricao', TRUE),
                'cd_usuario'        => $this->session->userdata('codigo')
            ); 

            if(intval($cd_sistema_metodo) == 0)
            {
                $this->sistema_model->metodo_salvar($args);
            }
            else
            {
                $this->sistema_model->atualizar_metodo($cd_sistema_metodo, $args);
            }              
                          
            redirect('servico/sistema_eprev/metodo/'.$cd_sistema, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NГO PERMITIDO');
        }
    }

    public function excluir_metodo($cd_sistema,$cd_sistema_metodo)
    {
        if($this->get_permissao())
        {
            $this->load->model('eprev/sistema_model');

            $this->sistema_model->metodo_excluir($cd_sistema_metodo, $this->session->userdata('codigo'));
            
            redirect('servico/sistema_eprev/metodo/'.$cd_sistema, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NГO PERMITIDO');
        }
    }

    public function pendencia($cd_sistema, $cd_sistema_pendencia = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('eprev/sistema_model');

            $data = array(
                'sistema'         => $this->sistema_model->carrega($cd_sistema),
                'collection'      => $this->sistema_model->pendencia_listar($cd_sistema), 
                'pendencia_minha' => $this->sistema_model->get_pendencia_minha()
            );

            if(intval($cd_sistema_pendencia) == 0)
            {
                $data['row'] = array(
                    'cd_sistema'               => intval($cd_sistema),
                    'cd_sistema_pendencia'     => intval($cd_sistema_pendencia),
                    'cd_pendencia_minha_query' => '',
                    'cd_pendencia_minha'       => '',
                    'cd_evento'                => '',
                    'ds_descricao'             => '',
                    'nr_ordem'                 => ''
                ); 

            }
            else
            {
               $data['row'] = $this->sistema_model->pendencia_carrega($cd_sistema_pendencia);
            }
          
             $this->load->view('servico/sistema_eprev/pendencia', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NГO PERMITIDO');
        }
    }

    public function pendencia_salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('eprev/sistema_model');
            
            $cd_sistema = $this->input->post('cd_sistema', TRUE);

            $cd_sistema_pendencia = $this->input->post('cd_sistema_pendencia', TRUE);
            
            $args = array(
                'cd_sistema'               => $this->input->post('cd_sistema', TRUE),
                'cd_evento'                => $this->input->post('cd_evento', TRUE),
                'nr_ordem'                 => $this->input->post('nr_ordem', TRUE),
                'cd_pendencia_minha_query' => $this->input->post('cd_pendencia_minha_query', TRUE),
                'ds_sistema_pendencia'     => $this->input->post('ds_sistema_pendencia', TRUE),
                'ds_descricao'             => $this->input->post('ds_descricao', TRUE),
                'cd_usuario'               => $this->session->userdata('codigo')
            ); 

            if(intval($cd_sistema_pendencia) == 0)
            {
                $this->sistema_model->pendencia_salvar($args); 
            }
            else
            {
                $this->sistema_model->atualizar_pendencia($cd_sistema_pendencia, $args);
            }              
                          
            redirect('servico/sistema_eprev/pendencia/'.$cd_sistema, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NГO PERMITIDO');
        }
    } 

    public function pendencia_excluir($cd_sistema, $cd_sistema_pendencia)
    {
        if($this->get_permissao())
        {
            $this->load->model('eprev/sistema_model');

            $this->sistema_model->pendencia_excluir($cd_sistema_pendencia, $this->session->userdata('codigo'));
            
            redirect('servico/sistema_eprev/pendencia/'.$cd_sistema, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NГO PERMITIDO');
        }
    }
}
?>