<?php
class Posvenda extends Controller
{
	function __construct()
	{
		parent::Controller();
        
        CheckLogin();
	}

    private function get_permissao()
    {
        if(gerencia_in(array('GCM')))
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
			$this->load->model('projetos/pos_venda_participante_model');

            $data = array(
                'usuario_cadastro'     => $this->pos_venda_participante_model->get_usuario_cadastro(),
                'usuario_encerramento' => $this->pos_venda_participante_model->get_usuario_encerramento(),
                'usuario_vendedor'     => $this->pos_venda_participante_model->get_vendedor()
            ); 

			$this->load->view('ecrm/posvenda/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}		
	}
    
    public function listar()
	{
        $this->load->model('projetos/pos_venda_participante_model');

		$args = array(
            'cd_empresa'             => $this->input->post('cd_empresa'),
            'cd_registro_empregado'  => $this->input->post('cd_registro_empregado'), 
            'seq_dependencia'        => $this->input->post('seq_dependencia'),
            'dt_ingresso_ini'        => $this->input->post('dt_ingresso_ini'),
            'dt_ingresso_fim'        => $this->input->post('dt_ingresso_fim'),
            'dt_digita_ingresso_ini' => $this->input->post('dt_digita_ingresso_ini'),
            'dt_digita_ingresso_fim' => $this->input->post('dt_digita_ingresso_fim'),
            'dt_boas_vindas_ini'     => $this->input->post('dt_boas_vindas_ini'),
            'dt_boas_vindas_fim'     => $this->input->post('dt_boas_vindas_fim'),
            'dt_inicio_ini'          => $this->input->post('dt_inicio_ini'),
            'dt_inicio_fim'          => $this->input->post('dt_inicio_fim'),
            'cd_usuario_inicio'      => $this->input->post('cd_usuario_inicio'),
            'dt_final_ini'           => $this->input->post('dt_final_ini'),
            'dt_final_fim'           => $this->input->post('dt_final_fim'),
            'cd_usuario_final'       => $this->input->post('cd_usuario_final'),
            'cd_atendimento'         => $this->input->post('cd_atendimento'),
            'cd_usuario_vendedor'    => $this->input->post('cd_usuario_vendedor')
        );
		
		manter_filtros($args);

		$data['collection'] = $this->pos_venda_participante_model->lista($args);

		$this->load->view('ecrm/posvenda/index_result', $data);
	}
    
    public function excluir($cd_pos_venda_participante)
	{
		if(gerencia_in(array('GCM')))
		{
            $this->load->model('projetos/pos_venda_participante_model');
            
            $this->pos_venda_participante_model->excluir($cd_pos_venda_participante, $this->session->userdata('codigo'));
            
            redirect('ecrm/posvenda', 'refresh');			
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}			
	}

    public function relatorio_email()
    {
        if(gerencia_in(array('GCM')))
        {
            $this->load->view('ecrm/posvenda/relatorio_email');         
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar_relatorio_email()
    {
        $this->load->model('projetos/pos_venda_participante_model');

        $args = array(
            'dt_ini' => $this->input->post('dt_ini'),
            'dt_fim' => $this->input->post('dt_fim')
        );
        
        manter_filtros($args);
        
        $data['collection'] = $this->pos_venda_participante_model->listar_email($args);
        
        $this->load->view('ecrm/posvenda/relatorio_email_result', $data);            
    }
    
    public function posvenda_participante($cd_empresa, $cd_registro_empregado, $seq_dependencia, $cd_atendimento = 0)
    {
        if(gerencia_in(array('GCM')))
		{
            $this->load->model('projetos/pos_venda_participante_model');

            $data['cd_atendimento']            = intval($cd_atendimento);
            $data['cd_pos_venda_participante'] = '';
            
            $data['row'] = $this->pos_venda_participante_model->posvenda_participante($cd_empresa, $cd_registro_empregado, $seq_dependencia);
            
            $posvenda_aberto = $this->pos_venda_participante_model->posvenda_aberto($cd_empresa, $cd_registro_empregado, $seq_dependencia);
  
            $data['fl_iniciar'] = false;
            
            $data['collection'] = array();
  
            if(count($posvenda_aberto) == 1)
            {
                $cd_pos_venda_participante = $posvenda_aberto[0]['cd_pos_venda_participante'];

                $data['cd_pos_venda_participante'] = $posvenda_aberto[0]['cd_pos_venda_participante'];
                
                $perguntas = $this->pos_venda_participante_model->posvenda_aberto_perguntas($posvenda_aberto[0]['cd_pos_venda']);
                
                $count = 1;
                
                foreach ($perguntas as $item)
                {                    
                    $data['collection'][$count]['cd_pos_venda_pergunta'] = $item['cd_pos_venda_pergunta'];
                    $data['collection'][$count]['ds_pergunta']           = $count.') '.$item['ds_pergunta'] ;
                    
                    $resposta = $this->pos_venda_participante_model->posvenda_aberto_respostas($cd_pos_venda_participante, $item['cd_pos_venda_pergunta']);
                    
                    $i = 0;
                    
                    foreach ($resposta as $item2)
                    {
                        $data['collection'][$count]['resposta'][$i]['tp_resposta']                = $item['tp_resposta'];
                        $data['collection'][$count]['resposta'][$i]['cd_pos_venda_pergunta']      = $item['cd_pos_venda_pergunta'];
                        $data['collection'][$count]['resposta'][$i]['fl_respondido']              = $item2['fl_respondido'];
                        $data['collection'][$count]['resposta'][$i]['cd_pos_venda_resposta']      = $item2['cd_pos_venda_resposta'];
                        $data['collection'][$count]['resposta'][$i]['cd_resposta']                = $item2['cd_resposta'];
                        $data['collection'][$count]['resposta'][$i]['ds_resposta']                = $item2['ds_resposta'];
                        $data['collection'][$count]['resposta'][$i]['fl_complemento']             = $item2['fl_complemento'];
                        $data['collection'][$count]['resposta'][$i]['fl_complemento_obrigatorio'] = $item2['fl_complemento_obrigatorio'];
                        $data['collection'][$count]['resposta'][$i]['complemento']                = $item2['complemento'];

                        $i++;
                    }
                    
                    $count ++;
                }
            } 
            else if((count($posvenda_aberto) == 0) AND (trim($data['row']['dt_ultimo']) == ''))
            {
                $data['fl_iniciar'] = true;
            }

            if(count($posvenda_aberto) > 1)
            {
                exibir_mensagem('ERRO!'.br(3).'EXISTE MAIS UM POS VENDA ABERTO');
            }
            else
            {
                $this->load->view('ecrm/posvenda/posvenda_participante', $data);
            }
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}	
    }

    public function iniciar_posvenda($cd_empresa, $cd_registro_empregado, $seq_dependencia, $cd_atendimento)
    {
        if(gerencia_in(array('GCM')))
        {     
            $this->load->model('projetos/pos_venda_participante_model');

            $this->pos_venda_participante_model->iniciar_posvenda(
                $cd_empresa,
                $cd_registro_empregado,
                $seq_dependencia,
                $this->session->userdata('codigo'),
                $cd_atendimento
            );

            redirect('ecrm/posvenda/posvenda_participante/'.$cd_empresa.'/'.$cd_registro_empregado.'/'.$seq_dependencia, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }       
    }

    public function enviar_email($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        if(gerencia_in(array('GCM')))
        {
            $this->load->model('projetos/pos_venda_participante_model');
                
            $this->pos_venda_participante_model->enviar_email(
                $cd_empresa,
                $cd_registro_empregado,
                $seq_dependencia,
                $this->session->userdata('codigo')
            );

            redirect('ecrm/posvenda/relatorio_email', 'refresh');
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    public function salvar_respostas()
    {
        if(gerencia_in(array('GCM')))
        {		   
            $this->load->model('projetos/pos_venda_participante_model');

            $cd_empresa            = $this->input->post('cd_empresa');
            $cd_registro_empregado = $this->input->post('cd_registro_empregado');
            $seq_dependencia       = $this->input->post('seq_dependencia');

            $args = array(
                'cd_pos_venda_participante' => $this->input->post('cd_pos_venda_participante'),
                'fl_encerra'                => $this->input->post('fl_encerra'),
                'cd_usuario'                => $this->session->userdata('codigo')
            );
                        
            $args['pos_venda_participante_resposta'] = array();
            
            $i = 0;
            
            foreach ($_POST as $key => $item) 
            {
                if(substr($key, 0, 2) == 'R_')
                {
                    foreach ($item as $cd_resposta) 
                    {
                        $args['pos_venda_participante_resposta'][$i]['cd_resposta'] = $cd_resposta;
                        $args['pos_venda_participante_resposta'][$i]['complemento'] = $this->input->post('C_'.$cd_resposta);
                        
                        $i++;
                    }
                }
            }
            
            $this->pos_venda_participante_model->salvar_respostas($args);
            
            redirect('ecrm/posvenda/posvenda_participante/'.$cd_empresa.'/'.$cd_registro_empregado.'/'.$seq_dependencia, 'refresh');
        }
        else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}	
    }	
	
    public function relatorio()
	{
        if(gerencia_in(array('GCM')))
        {
            $this->load->model('projetos/pos_venda_participante_model');
            
            $data = array(
                'patrocinadora' => $this->pos_venda_participante_model->patrocinadora(),
                'plano'         => $this->pos_venda_participante_model->plano()
            );

			$data['patrocinadora'] = 

            $this->load->view('ecrm/posvenda/relatorio', $data);		
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
    
    public function gera_relatorio()
    {
        if(gerencia_in(array('GCM')))
        {
            $this->load->model('projetos/pos_venda_participante_model');

            $cd_empresa = $this->input->post('cd_empresa');
            $cd_plano   = $this->input->post('cd_plano');

            $args = array(
                'dt_ini'     => $this->input->post('dt_ini'),
                'dt_fim'     => $this->input->post('dt_fim'),
                'cd_plano'   => $cd_plano,
                'cd_empresa' => $cd_empresa
            );
            
            $row = $this->pos_venda_participante_model->total_ingressos($args);
            $total_ingressos = trim($row['qt_total']);
            $dt_ingresso_ini = trim($row['dt_ingresso_ini']);
            $dt_ingresso_fim = trim($row['dt_ingresso_fim']);
            
            $row = $this->pos_venda_participante_model->total_posvenda($args);
            $total_posvenda = trim($row['qt_total']);
              
            $row = $this->pos_venda_participante_model->total_posvenda_realizado($args);
            $total_posvenda_realizado = trim($row['qt_total']);        

            $pergunta = $this->pos_venda_participante_model->relatorio_pergunta($args);
            
            $this->load->plugin('fpdf');

			$ob_pdf = new PDF('P', 'mm', 'A4');
			$ob_pdf->SetNrPag(true);
			$ob_pdf->SetMargins(10, 14, 5);
			$ob_pdf->header_exibe = true;
			$ob_pdf->header_logo = true;
			$ob_pdf->header_titulo = true;
			$ob_pdf->header_titulo_texto = 'Pós-venda';
            
            $ob_pdf->AddPage();
            
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->SetY($ob_pdf->GetY()+5);
            $ob_pdf->MultiCell(190, 4, 'Período: de '.$args['dt_ini'].' até '.$args['dt_fim'], 0, 'J');

            if(trim($cd_empresa) != '')
            {
                $row = $this->pos_venda_participante_model->nome_empresa($cd_empresa);

                $ob_pdf->MultiCell(190, 4, 'Empresa: '.trim($row['nome_empresa']), 0, 'J');
            }

            if(trim($cd_plano) != '')
            {
                $row = $this->pos_venda_participante_model->nome_plano($cd_plano);

                $ob_pdf->MultiCell(190, 4, 'Plano: '.trim($row['nome_plano']), 0, 'J');
            }
            
            $ob_pdf->SetY($ob_pdf->GetY()+5);
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0, 0, 0);
            $ob_pdf->SetWidths(array(100, 45, 45));
            $ob_pdf->SetAligns(array('L', 'C', 'C'));
            $ob_pdf->SetFont('Courier', 'B', 10);
            $ob_pdf->Row(array('Total', 'Quantidade', 'Percentual'));	
            
            $ob_pdf->SetFont('Courier', '', 10);	
            $ob_pdf->Row(array('De Ingressos ('.$dt_ingresso_ini.' à '.$dt_ingresso_fim.')', $total_ingressos, '100%'));
            
            if(($total_posvenda > 0) and ($total_ingressos > 0))
            {
                $ob_pdf->Row(array('De Pós-Venda', $total_posvenda, number_format((($total_posvenda*100)/$total_ingressos),0,',','.').'%'));	
            }
            else
            {
                $ob_pdf->Row(array('De Pós-Venda', $total_posvenda, '0%'));	
            }
            
            if(($total_posvenda_realizado > 0) and ($total_ingressos > 0))
            {
                $ob_pdf->Row(array('De Pós-Venda realizado', $total_posvenda_realizado, number_format((($total_posvenda_realizado*100)/$total_ingressos),0,',','.').'%'));		
            }
            else
            {
                $ob_pdf->Row(array('De Pós-Venda realizado', $total_posvenda_realizado, '0%'));
            }
            
            
            $i = 1;
            
            $arr_thema = $ob_pdf->getTema();
            
            foreach($pergunta as $item)
            {
                $args['cd_pos_venda_pergunta'] = $item['cd_pos_venda_pergunta'];
                
                $resposta = $this->pos_venda_participante_model->relatorio_respostas($args);
                
                $resposta_complemento = $this->pos_venda_participante_model->relatorio_respostas_complemento($args);
                
                $ob_pdf->SetFont('Courier', '', 10);
                $ob_pdf->SetY($ob_pdf->GetY() +10);
                
                if($ob_pdf->GetY() >= 240)
                {
                    $ob_pdf->AddPage();
                }
                
                $ob_pdf->MultiCell(190, 4, $i.") ".$item['ds_pergunta'], 0, "J");
                
                $grafico = array();
                
                if(count($resposta) > 0)
                {
                    foreach($resposta as $item2)
                    {
                        $grafico[$item2['ds_resposta']] = $item2['qt_total'];
                    }
                    
                    $ob_pdf->SetXY(10, $ob_pdf->GetY());	
                    $ob_pdf->PieChart(150, 40, $grafico, '%l (%p)', $arr_thema['pastel']);		
                    $ob_pdf->SetXY(10, $ob_pdf->GetY()+15);
                }
                
                if(count($resposta_complemento) . 0)
                {
                    $ob_pdf->SetY($ob_pdf->GetY()+12);
                    $ob_pdf->SetLineWidth(0);
                    $ob_pdf->SetDrawColor(0, 0, 0);
                    $ob_pdf->SetWidths(array(40, 150));
                    $ob_pdf->SetAligns(array('L', 'L'));
                    $ob_pdf->SetFont('Courier', 'B', 10);
                    $ob_pdf->Row(array('Resposta', 'Complemento'));		
                    $ob_pdf->SetFont('Courier', '', 10);	
                    
                    foreach($resposta_complemento as $item2)	
                    {
                        $ob_pdf->Row(array($item2['ds_resposta'], $item2['complemento']));					
                    }
                }
                
                $i++;   
            }
            
            $ob_pdf->Output();   
        }
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

	public function resposta($cd_pos_venda_participante)
	{
        if(gerencia_in(array('GCM')))
        {
            $this->load->model('projetos/pos_venda_participante_model');
            
            $data['row'] = $this->pos_venda_participante_model->resposta_participante($cd_pos_venda_participante);

            $data['collection_acompanhamento'] = $this->pos_venda_participante_model->listar_acompanhamento($cd_pos_venda_participante);
          
            $pergunta = $this->pos_venda_participante_model->perguntas($cd_pos_venda_participante);
            
            $data['collection'] = array();
            
            $i = 0;
            
            foreach($pergunta as $item)
            {
                $data['collection'][$i]['cd_pos_venda_pergunta'] = $item['cd_pos_venda_pergunta'];
                $data['collection'][$i]['ds_pergunta']           = $item['ds_pergunta'];
                
                $data['collection'][$i]['resposta'] = $this->pos_venda_participante_model->respostas($cd_pos_venda_participante, $item['cd_pos_venda_pergunta']);
                
                $i++;
            }
                
            $this->load->view('ecrm/posvenda/resposta', $data);
        }
        else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}         
	}
    
    public function salvar_acompanhamento()
    {
        if(gerencia_in(array('GCM')))
        {
            $this->load->model('projetos/pos_venda_participante_model');
            
            $args = array(
                'cd_pos_venda_participante' => $this->input->post('cd_pos_venda_participante', TRUE),
                'acompanhamento'            => $this->input->post('acompanhamento', TRUE),
                'cd_usuario'                => $this->session->userdata('codigo')
            );
            
            $this->pos_venda_participante_model->salvar_acompanhamento($args);
            
            redirect('ecrm/posvenda/resposta/'.$args['cd_pos_venda_participante'], 'refresh');   
        }
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}  
    }	
}
?>