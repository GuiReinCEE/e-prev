<?php
class Controle_igp extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao()
    {
    	if(gerencia_in(array('GC', 'CQ')))
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
            $this->load->view('gestao/controle_igp/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {       
        $this->load->model('gestao/controle_igp_model');
                
        $args['nr_ano'] = $this->input->post('nr_ano', TRUE);
        
        manter_filtros($args);
        
        $data['collection'] = $this->controle_igp_model->listar($args);

        foreach($data['collection'] as $key => $item)
        {
            $indicadores = $this->controle_igp_model->listar_indicadores($item['cd_controle_igp']);
                
            $data['collection'][$key]['indicadores'] = array();

            foreach($indicadores as $item2)
            {               
                $data['collection'][$key]['indicadores'][] = $item2['indicador'];
            }       
        }
        
        $this->load->view('gestao/controle_igp/index_result', $data);
    }

    public function cadastro($cd_controle_igp = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/controle_igp_model');
            
            if(intval($cd_controle_igp) == 0)
            {
                $row = $this->controle_igp_model->referencia_ano();

                $data['row'] = array(
                    'cd_controle_igp' => intval($cd_controle_igp),
                    'nr_ano'          => $row['nr_ano'] 
                );

                $this->load->view('gestao/controle_igp/cadastro', $data);
            }
            else
            {
                $this->indicador($cd_controle_igp);
            }
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
            $this->load->model('gestao/controle_igp_model');
            
            $cd_controle_igp = $this->input->post('cd_controle_igp', TRUE);

            $args = array(
                'nr_ano'     => $this->input->post('nr_ano', TRUE),
                'cd_usuario' => $this->session->userdata('codigo')
            );
            
            if(intval($cd_controle_igp) == 0)
            {
                $cd_controle_igp = $this->controle_igp_model->salvar($args);

                $referencia = $this->controle_igp_model->referencia_indicador($cd_controle_igp);

                $indicador_novo = $this->controle_igp_model->listar_indicadores($referencia['cd_controle_igp']);

                foreach($indicador_novo as $key => $item)
                {
                    $args = array(
                        'cd_controle_igp'           => $cd_controle_igp,
                        'nr_ordem'                  => $item['nr_ordem'],
                        'cd_indicador'              => $item['cd_indicador'],
                        'cd_controle_igp_categoria' => $item['cd_controle_igp_categoria'],
                        'cd_responsavel'            => $item['cd_responsavel'],
                        'nr_peso'                   => $item['nr_peso'],
                        'ds_consulta'               => $item['ds_consulta'],
                        'cd_usuario'                => $this->session->userdata('codigo') 
                    );
  
                    $cd_controle_igp_indicador = $this->controle_igp_model->salvar_controle_indicador($args); 
                } 
            }
          
            redirect('gestao/controle_igp/indicador/'.$cd_controle_igp);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function indicador($cd_controle_igp, $cd_controle_igp_indicador = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/controle_igp_model');

            $data['row'] = $this->controle_igp_model->carrega($cd_controle_igp);

            $data['collection'] = $this->controle_igp_model->listar_indicadores($cd_controle_igp);

            if(intval($cd_controle_igp_indicador) == 0)
            {
                $row = $this->controle_igp_model->get_ordem($cd_controle_igp);

                $data['controle_indicador'] = array(
                    'cd_controle_igp_indicador' => intval($cd_controle_igp_indicador),
                    'nr_ordem'                  => (isset($row['nr_ordem']) ? intval($row['nr_ordem']) : 1),
                    'cd_indicador'              => '',
                    'cd_controle_igp_categoria' => '',
                    'cd_responsavel'            => '',
                    'nr_peso'                   => 0
                );

                $cd_indicador = 0;
            }
            else
            {
                $data['controle_indicador'] = $this->controle_igp_model->carrega_controle_indicador($cd_controle_igp_indicador);
                
                $cd_indicador = $data['controle_indicador']['cd_indicador'];
            }

            $data['indicador'] = $this->controle_igp_model->get_indicador_igp($cd_controle_igp, $cd_indicador);

            $this->load->view('gestao/controle_igp/indicador', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_controle_indicador()
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/controle_igp_model');
            
            $cd_controle_igp           = $this->input->post('cd_controle_igp', TRUE);
            $cd_controle_igp_indicador = $this->input->post('cd_controle_igp_indicador', TRUE);

            $args = array(
                'cd_controle_igp'           => $cd_controle_igp,
                'nr_ordem'                  => $this->input->post('nr_ordem', TRUE),
                'cd_indicador'              => $this->input->post('cd_indicador', TRUE),
                'cd_controle_igp_categoria' => $this->input->post('cd_controle_igp_categoria', TRUE),
                'cd_responsavel'            => $this->input->post('cd_responsavel', TRUE),
                'nr_peso'                   => app_decimal_para_db($this->input->post('nr_peso', TRUE)),
                'ds_consulta'               => '',
                'cd_usuario'                => $this->session->userdata('codigo')
            );
    
            if(intval($cd_controle_igp_indicador) == 0)
            {
                $cd_controle_igp_indicador = $this->controle_igp_model->salvar_controle_indicador($args);
            }
            else
            {
                $this->controle_igp_model->atualizar_controle_indicador($cd_controle_igp_indicador, $args);
            }
            
            redirect('gestao/controle_igp/indicador/'.$cd_controle_igp);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function set_ordem($cd_controle_igp_indicador)
    {
        $this->load->model('gestao/controle_igp_model');

        $this->controle_igp_model->set_ordem(
            $cd_controle_igp_indicador, 
            $this->input->post('nr_ordem', TRUE), 
            $this->session->userdata('codigo')
        );
    }

    public function fechar_controle($cd_controle_igp)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/controle_igp_model');

            $this->controle_igp_model->fechar($cd_controle_igp, $this->session->userdata('codigo'));

            redirect('gestao/controle_igp', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir_indicador($cd_controle_igp, $cd_controle_igp_indicador)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/controle_igp_model');

            $data['row'] = $this->controle_igp_model->carrega($cd_controle_igp);

            $assunto = $this->controle_igp_model->carrega_controle_indicador($cd_controle_igp_indicador);

            $this->controle_igp_model->excluir_indicador($cd_controle_igp_indicador, 
            $this->session->userdata('codigo'));
        
            redirect('gestao/controle_igp/indicador/'.$cd_controle_igp, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function resultado_mes($cd_controle_igp, $mes = '')
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/controle_igp_model');

            $data = array(
                'row'        => $this->controle_igp_model->carrega($cd_controle_igp),
                'referencia' => $this->controle_igp_model->referencia($cd_controle_igp, $mes),
                'mes'        => trim($mes),
                'collection' => array()
            );

            if(trim($mes) != '')
            {   
                $args = array(
                    'nr_mes' => trim($mes),
                    'nr_ano' => $data['row']['nr_ano']
                );

                if(count($data['referencia']) == 0)
                {
                    $data['collection'] = $this->get_calculo_igp($this->controle_igp_model->listar_resultado(
                        $cd_controle_igp, 
                        $args
                    ));
                }
                else
                {
                    $data['collection'] = $this->controle_igp_model->resultado(
                        $cd_controle_igp, 
                        $data['referencia']['cd_controle_igp_indicador_mes']
                    ); 
                }
            }

            $this->load->view('gestao/controle_igp/resultado_mes', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    private function get_calculo_igp($collection)
    {
        foreach ($collection as $key => $item) 
        {
            $nr_calculo             = 0;
            $nr_resultado           = 0;
            $nr_resultado_ponderado = 0;

            if(trim($item['tp_analise']) == '+')
            {
                if(floatval(number_format($item['nr_meta_indicador'], 2, '.', '')) > 0)
                {
                    $nr_calculo = (number_format($item['nr_resultado_indicador'], 2, '.', '') * 100) / number_format($item['nr_meta_indicador'], 2, '.', '');
                }
            }
            else if(trim($item['tp_analise']) == '-')
            {
                if(floatval(number_format($item['nr_resultado_indicador'], 2, '.', '')) > 0)
                {
                    $nr_calculo = (number_format($item['nr_meta_indicador'], 2, '.', '') * 100) / number_format($item['nr_resultado_indicador'], 2, '.', '');
                }
                else
                {
                    $nr_calculo = 100;
                }
            }

            if($nr_calculo < 0)
            {
                $nr_resultado = 0;
            }
            else if($nr_calculo > 100)
            {
                $nr_resultado = 100;
            }
            else
            {
                $nr_resultado = $nr_calculo;
            }

            $nr_resultado_ponderado = (number_format($nr_resultado, 2, '.', '') * number_format($item['nr_peso'], 2, '.', '')) / 100;

            $collection[$key]['nr_calculo']             = $nr_calculo;
            $collection[$key]['nr_resultado_igp']       = $nr_resultado;
            $collection[$key]['nr_resultado_ponderado'] = $nr_resultado_ponderado;
        }

        return $collection;
    }

    public function fechar_mes($cd_controle_igp, $mes)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/controle_igp_model');

            $row = $this->controle_igp_model->carrega($cd_controle_igp);

            $args = array(
                'nr_mes' => trim($mes),
                'nr_ano' => $row['nr_ano']
            );
 
            $collection = $this->get_calculo_igp($this->controle_igp_model->listar_resultado($cd_controle_igp, $args));

            $fechado = array(
               'cd_controle_igp' => $cd_controle_igp,
               'dt_referencia'   => '01/'.$mes.'/'.$row['nr_ano'], 
               'cd_usuario'      => $this->session->userdata('codigo')
            );
            
            $cd_controle_igp_indicador_mes = $this->controle_igp_model->fechar_mes($fechado);

            foreach($collection as $item)
            {   
                $args = array(
                    'cd_controle_igp_indicador'    => $item['cd_controle_igp_indicador'],
                    'cd_controle_igp_indicador_mes'=> $cd_controle_igp_indicador_mes,
                    'ds_referencia'                => $item['ds_referencia_indicador'],
                    'nr_resultado_indicador'       => $item['nr_resultado_indicador'],
                    'nr_meta_indicador'            => $item['nr_meta_indicador'],
                    'nr_calculo'                   => $item['nr_calculo'],
                    'nr_resultado'                 => $item['nr_resultado_igp'],
                    'nr_resultado_ponderado'       => $item['nr_resultado_ponderado'],
                    'cd_usuario'                   => $this->session->userdata('codigo')
                );

                $this->controle_igp_model->salvar_fechar_mes($args);
            } 
            
            redirect('gestao/controle_igp/resultado_mes/'.$cd_controle_igp.'/'.$mes, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function apresentacao($cd_controle_igp = '')
    {
        $data = $this->get_apresentacao($cd_controle_igp);

        $this->load->view('gestao/controle_igp/apresentacao', $data);
    }

    public function apresentacao_pdf($cd_controle_igp = '')
    {
        $this->load->plugin('fpdf');

        $data = $this->get_apresentacao($cd_controle_igp);
                
        $ob_pdf = new PDF('L','mm','A4');
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib');               
        $ob_pdf->SetNrPag(true);
        $ob_pdf->SetMargins(10, 14, 5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
       
		if($data['row']['nr_ano'] >= 2024)
		{
			$ob_pdf->header_titulo_texto = 'Indicadores do Planejamento Estratégico - '.$data['row']['ds_referenfcia'];
		}
		else
		{
			$ob_pdf->header_titulo_texto = 'Índice Geral de Performance - IGP - '.$data['row']['ds_referenfcia'];
		}	

        $ob_pdf->SetLineWidth(0);
        $ob_pdf->SetDrawColor(0, 0, 0);

        $ob_pdf->AddPage();
        $ob_pdf->SetY($ob_pdf->GetY() + 1);

        $ob_pdf->SetWidths(array(183, 42, 50));
        $ob_pdf->SetAligns(array('C', 'C', 'C'));
        $ob_pdf->SetFont('segoeuib', '', 11);
        $ob_pdf->Row(array('', 'Indicador', 'IGP'));

        $ob_pdf->SetWidths(array(20, 16, 55, 17, 14, 23, 19, 19, 21, 21, 25, 25));
        $ob_pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
        $ob_pdf->SetFont('segoeuib', '', 11);
        $ob_pdf->Row(array('Cat.', 'Resp.', 'Indicador', 'Melhor', 'Peso (%)', 'Unid.', 'Controle', 'Ref.', 'Meta', 'Resultado', 'Meta Ponderada', 'Resultado Ponderado'));

        $ob_pdf->SetAligns(array('C', 'C', 'L', 'C', 'R', 'C', 'C', 'C', 'R', 'R', 'R', 'R'));

        $ob_pdf->SetFont('segoeuib', '', 10);

        $nr_peso                = 0;
        $nr_resultado_ponderado = 0;
        $nr_ano                 = '';

        foreach($data['collection'] as $key => $item)
        {
            $nr_peso                += $item['nr_peso'];
            $nr_resultado_ponderado += $item['nr_resultado_ponderado'];
            $nr_ano                  = $item['nr_ano'];

            $ob_pdf->Row(array(
                $item['ds_controle_igp_categoria'],
                $item['cd_responsavel'],
                $item['indicador'],
                $item['ds_analise'],
                number_format($item['nr_peso'], 2, ',', '.'),
                $item['ds_indicador_unidade_medida'],
                $item['ds_indicador_controle'],
                $item['ds_referencia_indicador'],
                number_format($item['nr_meta_indicador'], 2, ',', '.'),
                number_format($item['nr_resultado_indicador'], 2, ',', '.'),
                number_format($item['nr_peso'], 2, ',', '.'),
                number_format($item['nr_resultado_ponderado'], 2, ',', '.')
            ));
        }

        $ob_pdf->SetWidths(array(225, 25, 25));
        $ob_pdf->SetAligns(array('L', 'R', 'R'));
        $ob_pdf->SetFont('segoeuib', '', 11);
        $ob_pdf->Row(array('Resultado Acumulado '.$nr_ano, number_format($nr_peso, 2, ',', '.'), number_format($nr_resultado_ponderado, 2, ',', '.')));

        foreach($data['resultados'] as $key => $item)
        {
            $ob_pdf->Row(array(
                'Resultado Acumulado '.$item['nr_ano'], 
                number_format($item['nr_peso'], 2, ',', '.'), 
                number_format($item['nr_resultado_ponderado'], 2, ',', '.')
            ));
        }

        $ob_pdf->Output();
    }

    private function get_apresentacao($cd_controle_igp = '')
    {
        $this->load->model('gestao/controle_igp_model');

        $data['anos'] = $this->controle_igp_model->get_anos();

        if(intval($cd_controle_igp) == 0)
        {
            $cd_controle_igp = intval($data['anos'][0]['cd_controle_igp']);
        }

        $data['resultados'] = array();

        foreach ($data['anos'] as $key => $item) 
        {
            if(intval($item['cd_controle_igp']) < intval($cd_controle_igp))
            {
                $data['resultados'][] = $this->controle_igp_model->resultado_anual(intval($item['cd_controle_igp']));
            }
        }

        $data['row'] = $this->controle_igp_model->carrega($cd_controle_igp);

        $row_mes = $this->controle_igp_model->referencia_mes_fechado($cd_controle_igp);
		
		$data['row']['ds_referenfcia'] = $row_mes['ds_referenfcia'];

        $data['collection'] = $this->controle_igp_model->resultado(
            $cd_controle_igp, 
            $row_mes['cd_controle_igp_indicador_mes']
        ); 

        return $data;
    }
}