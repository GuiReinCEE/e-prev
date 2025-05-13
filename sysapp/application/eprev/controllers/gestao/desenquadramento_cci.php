<?php

class desenquadramento_cci extends Controller
{
	function __construct()
    {
        parent::Controller();
		
        $this->load->model('gestao/desenquadramento_cci_model');
    }

    private function get_permissao_cadastro()
    {
    	#Jorge Alexandre Fetter
        if($this->session->userdata('codigo') == 132)
        {
            return TRUE;
        }
        #Cristina Gomes Gonçalves
        else if($this->session->userdata('codigo') == 118)
        {
            return TRUE;
        }
        #Milena Voigt da Silva
        else if($this->session->userdata('codigo') == 319)
        {
            return true;
        }
		#Lucio Daniel Sartori
        else if($this->session->userdata('codigo') == 415)
        {
            return true;
        }
		#Raquel Ramos
        else if($this->session->userdata('codigo') == 515)
        {
            return true;
        }		
        #Luciano Rodriguez
        else if($this->session->userdata('codigo') == 251)
        {
            return TRUE;
        }
		else
		{
			return FALSE;
		}
    }

    private function get_permissao()
    {
        if($this->get_permissao_cadastro())
        {
            return TRUE;
        }
        else if(gerencia_in(array('DE')))
        {
            return TRUE;
        }
        else if(gerencia_in(array('GIN')))
        {
            return TRUE;
        }
        #Adriana Espíndola da Silva Reichmann
        else if($this->session->userdata('codigo') == 3)
        {
            return TRUE;
        }
        #Regis Rodrigues da Silveira
        else if($this->session->userdata('codigo') == 411)
        {
            return true;
        }
		#Roberta Bittencourt da Costa
        else if($this->session->userdata('codigo') == 474)
        {
            return true;
        }
        #Vanessa Silva Alves
        else if($this->session->userdata('codigo') == 424)
        {
            return TRUE;
        }
        #Tainá Kras Borges Schardosim
        else if($this->session->userdata('codigo') == 320)
        {
            return true;
        }
        #Denis Schmitt
        else if($this->session->userdata('codigo') == 38)
        {
            return TRUE;
        }
        #Moacir Reis de Oliveira Júnior
        else if($this->session->userdata('codigo') == 22)
        {
            return TRUE;
        }
        #Livia Santos Spiller
        else if($this->session->userdata('codigo') == 421)
        {
            return true;
        }
        #Adriano Carlos Oliveira Medeiros
        else if($this->session->userdata('codigo') == 25)
        {
            return TRUE;
        }
        #Lidiane Dias Ferreira
        else if($this->session->userdata('codigo') == 270)
        {
            return true;
        }
        #Gustavo Conrado Homrich
        else if($this->session->userdata('codigo') == 351)
        {
            return TRUE;
        }
        #Marlon da Rosa Pimentel
        else if($this->session->userdata('codigo') == 455)
        {
            return true;
        }
        #Jean Carlos Oliveira Seidler
        else if($this->session->userdata('codigo') == 298)
        {
            return TRUE;
        }
        #Adriana Nobre Nunes
        else if($this->session->userdata('codigo') == 26)
        {
            return true;
        }
        #William Guimaraes da Rocha
        else if($this->session->userdata('codigo') == 475)
        {
            return true;
        }

		else
		{
			return FALSE;
		}
    }

	
	public function index()
    {
    	CheckLogin();

		if ($this->get_permissao())
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$this->desenquadramento_cci_model->fundo($result, $args);
			$data['arr_fundo'] = $result->result_array();

			$this->load->view('gestao/desenquadramento_cci/index', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function listar()
    {
    	CheckLogin();

		$args = Array();
		$data = Array();
		$result = null;
							
		$args['nr_ano'] 					   = $this->input->post("nr_ano", TRUE);
		$args['nr_numero']  				   = $this->input->post("nr_numero", TRUE);					
		$args['dt_ini']  				 	   = $this->input->post("dt_ini", TRUE);					
		$args['dt_fim']  					   = $this->input->post("dt_fim", TRUE);	
        $args['dt_relatorio_ini']  			   = $this->input->post("dt_relatorio_ini", TRUE);					
		$args['dt_relatorio_fim']  			   = $this->input->post("dt_relatorio_fim", TRUE);	
		$args['cd_desenquadramento_cci_fundo'] = $this->input->post("cd_desenquadramento_cci_fundo", TRUE);					
		$args['fl_status']                     = $this->input->post("fl_status", TRUE);					
		$args['fl_encaminhado']  			   = $this->input->post("fl_encaminhado", TRUE);					
		$args['fl_envio']  					   = $this->input->post("fl_envio", TRUE);					
		$args['dt_encaminhado_ini']  		   = $this->input->post("dt_encaminhado_ini", TRUE);					
		$args['dt_encaminhado_fim']  		   = $this->input->post("dt_encaminhado_fim", TRUE);					
						
		manter_filtros($args);

		$this->desenquadramento_cci_model->listar($result, $args);
		$data['collection'] = $result->result_array();

		$this->load->view('gestao/desenquadramento_cci/index_result', $data);
    }
	
	public function cadastro($cd_desenquadramento_cci = 0, $cd_desenquadramento_cci_pai = 0)
	{
		CheckLogin();

		if ($this->get_permissao_cadastro())
        {
			$result   = null;
			$data     = array();
	        $args     = array();
			
			$args['cd_desenquadramento_cci'] = $cd_desenquadramento_cci;
			
			if((intval($args['cd_desenquadramento_cci']) == 0) AND (intval($cd_desenquadramento_cci_pai) > 0))
			{	
				#### CARREGA DADOS DO DESENQUADRAMENTO ANTERIOR (PAI) ####
				$args['cd_desenquadramento_cci'] = intval($cd_desenquadramento_cci_pai);
				$this->desenquadramento_cci_model->carrega($result, $args);
				$data['row'] = $result->row_array();

				$data['row']['cd_desenquadramento_cci']     = 0;
				$data['row']['cd_desenquadramento_cci_pai'] = intval($cd_desenquadramento_cci_pai);
				$data['row']['ano_numero_pai']              = $data['row']['ano_numero'];
				$data['row']['dt_desenquadramento_cci_pai'] = $data['row']['dt_desenquadramento_cci'];
				$data['row']['fl_status']                   = "R";
				$data['row']['dt_regularizado']             = "";
				$data['row']['dt_encaminhado']              = "";
				$data['row']['dt_enviado']                  = "";
				$data['row']['usuario_inclusao']            = "";
				$data['row']['usuario_enviado']             = "";
			}
			elseif(intval($args['cd_desenquadramento_cci']) == 0)
			{
				$data['row'] = array(
					'cd_desenquadramento_cci'               => intval($args['cd_desenquadramento_cci']),
					'dt_desenquadramento_cci'               => '',
					'cd_desenquadramento_cci_fundo'         => '',
					'cd_desenquadramento_cci_administrador' => '',
					'cd_desenquadramento_cci_gestor'        => '',
					'regra'                                 => '',
					'ds_desenquadramento_cci'               => '',
					'providencias_adotadas'                 => '',
					'fl_status'                             => '',
					'observacao'                            => '',
					'dt_regularizado'                       => '',
					'dt_encaminhado'                        => '', 
					'dt_enviado'                            => '',
					'usuario_inclusao'                      => '',
					'usuario_enviado'                       => '',
					'ano_numero_pai'                        => '',
					'ano_numero_filho'                      => ''
				);
			}
			else
			{
				$this->desenquadramento_cci_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}
			
			$data['cd_desenquadramento_cci_pai'] = intval($cd_desenquadramento_cci_pai);
			
			$this->load->view('gestao/desenquadramento_cci/cadastro', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function salvar()
	{
		CheckLogin();

		if ($this->get_permissao_cadastro())
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_desenquadramento_cci_pai']           = $this->input->post("cd_desenquadramento_cci_pai", TRUE);
			$args['cd_desenquadramento_cci']               = $this->input->post("cd_desenquadramento_cci", TRUE);
			$args['dt_desenquadramento_cci']               = $this->input->post("dt_desenquadramento_cci", TRUE);
			$args['cd_desenquadramento_cci_fundo']         = $this->input->post("cd_desenquadramento_cci_fundo", TRUE);
			$args['cd_desenquadramento_cci_administrador'] = $this->input->post("cd_desenquadramento_cci_administrador", TRUE);
			$args['cd_desenquadramento_cci_gestor']        = $this->input->post("cd_desenquadramento_cci_gestor", TRUE);
			$args['regra']                                 = $this->input->post("regra", TRUE);
			$args['ds_desenquadramento_cci']               = $this->input->post("ds_desenquadramento_cci", TRUE);
			$args['providencias_adotadas']                 = $this->input->post("providencias_adotadas", TRUE);
			$args['fl_status']                             = $this->input->post("fl_status", TRUE);
			$args['observacao']                            = $this->input->post("observacao", TRUE);
			$args['dt_regularizado']                       = $this->input->post("dt_regularizado", TRUE);
			$args['cd_usuario']                            = $this->session->userdata("codigo");
			
			$cd_desenquadramento_cci = $this->desenquadramento_cci_model->salvar($result, $args);
			
			redirect("gestao/desenquadramento_cci/cadastro/".intval($cd_desenquadramento_cci), "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function encaminhar($cd_desenquadramento_cci)
	{
		CheckLogin();

		if ($this->get_permissao_cadastro())
        {
			$result   = null;
			$data     = array();
			$args     = array();

			$this->load->model('projetos/eventos_email_model');

			$args['cd_desenquadramento_cci'] = $cd_desenquadramento_cci;

			$this->desenquadramento_cci_model->carrega($result, $args);
			$row = $result->row_array();
			
			$args['cd_desenquadramento_cci'] = $cd_desenquadramento_cci;
			$args['cd_usuario']              = $this->session->userdata("codigo");

			$this->desenquadramento_cci_model->encaminhar($result, $args);

			redirect("gestao/desenquadramento_cci/", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function confirmar($cd_desenquadramento_cci)
	{
		CheckLogin();

		if ($this->get_permissao_cadastro())
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$this->load->model('projetos/eventos_email_model');

			$args['cd_desenquadramento_cci'] = $cd_desenquadramento_cci;

			$this->desenquadramento_cci_model->carrega($result, $args);
			$row = $result->row_array();

            $cd_evento = 446;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $tags = array('[DS_STATUS]', '[LINK]');
			$subs = array($row['ds_status'], site_url('gestao/desenquadramento_cci/desenquadramento_pdf/'.md5($cd_desenquadramento_cci)));

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Desenquadramento',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            $cd_evento = 445;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $tags = array('[DS_STATUS]', '[LINK]');
			$subs = array($row['ds_status'], site_url('gestao/desenquadramento_cci/pdf/'.$cd_desenquadramento_cci));

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Desenquadramento',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
			
			$args['cd_desenquadramento_cci'] = $cd_desenquadramento_cci;
			$args['cd_usuario']              = $cd_usuario;
			
			$this->desenquadramento_cci_model->confirmar($result, $args);

			$this->envia_pydio($cd_desenquadramento_cci);
			
			redirect("gestao/desenquadramento_cci/", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	public function envia_pydio($cd_desenquadramento_cci)
	{
		$this->load->plugin('encoding_pi');

		$result   = null;
		$data     = array();
		$args     = array();
		
		$this->load->model('projetos/eventos_email_model');

		$args['cd_desenquadramento_cci'] = $cd_desenquadramento_cci;

		$this->desenquadramento_cci_model->carrega($result, $args);
		$row = $result->row_array();

		$caminho_desenquadramento = '../eletroceee/pydio/data/DOCUMENTOS_APROVADOS/DESENQUADRAMENTO/'.$row['ds_ano_edicao'];

        if(!is_dir($caminho_desenquadramento))
        {
            mkdir($caminho_desenquadramento, 0777);
        }

        $caminho_desenquadramento .= '/'.$row['ds_mes_edicao'].' - '.fixUTF8(mes_extenso($row['ds_mes_edicao']));

        if(!is_dir($caminho_desenquadramento))
        {
            mkdir($caminho_desenquadramento, 0777);
        }

        $arq_desenqudramento = $cd_desenquadramento_cci.'_'.date('dmyHis').'.pdf';

        $this->load->plugin('fpdf');
		$ob_pdf = new PDF('P', 'mm', 'A4');
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');
		$ob_pdf->SetNrPag(true);
		$ob_pdf->SetMargins(10, 14, 5);
		$ob_pdf->header_exibe = true;
		$ob_pdf->header_logo = true;			
	
		$ob_pdf = $this->pdfItem($ob_pdf, $cd_desenquadramento_cci);
		$ob_pdf->Output('up/desenquadramento_cci_anexo/'.$arq_desenqudramento, 'F');

       
       copy('../cieprev/up/desenquadramento_cci_anexo/'.$arq_desenqudramento, $caminho_desenquadramento.'/'.str_replace('/','-',$row['ano_numero']).'.pdf');
	}

	public function excluir($cd_desenquadramento_cci)
	{
		CheckLogin();

		if ($this->get_permissao_cadastro())
        {			
			$args['cd_desenquadramento_cci'] = $cd_desenquadramento_cci;
			$args['cd_usuario']              = $this->session->userdata("codigo");
			
			$this->desenquadramento_cci_model->excluir($args);

			redirect("gestao/desenquadramento_cci", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }	
	}
	
	public function devolver($cd_desenquadramento_cci)
	{
		CheckLogin();

		if ($this->get_permissao_cadastro())
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_desenquadramento_cci'] = $cd_desenquadramento_cci;
			$args['cd_usuario']              = $this->session->userdata("codigo");
			
			$this->desenquadramento_cci_model->devolver($result, $args);
			
			redirect("gestao/desenquadramento_cci/", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	public function desenquadramento_pdf($cd_desenquadramento_cci_md5)
	{
		$row = $this->desenquadramento_cci_model->carrega_md5($cd_desenquadramento_cci_md5);

		if(isset($row['cd_desenquadramento_cci']) AND intval($row['cd_desenquadramento_cci']) > 0)
		{
			$this->load->plugin('fpdf');
			$ob_pdf = new PDF('P', 'mm', 'A4');
			$ob_pdf->AddFont('segoeuil');
			$ob_pdf->AddFont('segoeuib');
			$ob_pdf->SetNrPag(true);
			$ob_pdf->SetMargins(10, 14, 5);
			$ob_pdf->header_exibe = true;
			$ob_pdf->header_logo = true;
		
			$ob_pdf = $this->pdfItem($ob_pdf, $row['cd_desenquadramento_cci']);
			$ob_pdf->Output();
		}
		else
        {
            echo 'DESENQUADRMANENTO NÃO ECONTRADO';
        }
	}
	
	public function pdf($cd_desenquadramento_cci)
	{
		CheckLogin();
		
		if ($this->get_permissao())
        {
			$this->load->plugin('fpdf');
			$ob_pdf = new PDF('P', 'mm', 'A4');
			$ob_pdf->AddFont('segoeuil');
			$ob_pdf->AddFont('segoeuib');
			$ob_pdf->SetNrPag(true);
			$ob_pdf->SetMargins(10, 14, 5);
			$ob_pdf->header_exibe = true;
			$ob_pdf->header_logo = true;			
		
			$ob_pdf = $this->pdfItem($ob_pdf, $cd_desenquadramento_cci);
			$ob_pdf->Output();
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	private function pdfItem($ob_pdf, $cd_desenquadramento_cci, $fl_nova_pagina = true, $fl_titulo = true, $fl_texto = true, $fl_origem = true)
	{
		$result   = null;
		$data     = array();
		$args     = array();
		$font_1 = 12;
		$font_2 = 11;		
		
		$args['cd_desenquadramento_cci'] = intval($cd_desenquadramento_cci);
		
		$this->desenquadramento_cci_model->carrega($result, $args);
		$row = $result->row_array();			

		$this->desenquadramento_cci_model->lista_acompanhamento($result, $args);
        $acompanhamento = $result->result_array();
		
		if($fl_nova_pagina)
		{
			$ob_pdf->AddPage();
			if($fl_titulo)
			{
				$ob_pdf->SetFont('segoeuib','',$font_1 + 4);
				if(trim($row['fl_status']) == 'P')
				{
					$ob_pdf->MultiCell(190, 5, "Desenquadramento", '0', 'C');
					$ob_pdf->SetY($ob_pdf->GetY() + 6);
				}
			}			
		}
		else
		{
			$ob_pdf->Line(10, ($ob_pdf->GetY() + 5), 200, ($ob_pdf->GetY() + 5));
			$ob_pdf->SetY($ob_pdf->GetY() + 5);
		}
		
		$ob_pdf->SetFont('segoeuil','',$font_1);
		
		if($fl_texto)
		{
			if(trim($row['fl_status']) == 'P')
			{
				$ob_pdf->MultiCell(190, 5, "Em atendimento ao item do item 3 da Política de Investimentos, informamos que o fundo/carteira abaixo identificado apresentou o seguinte desenquadramento: ", '0', 'J');
			}
			else
			{
				$ob_pdf->SetY($ob_pdf->GetY()+4);
				$ob_pdf->MultiCell(190, 5, "Em atendimento ao item do item 3 da Política de Investimentos, informamos que o presente fundo/carteira alterou seu status, conforme desenquadramento apontado abaixo: ", '0', 'J');
			}
		}

		$ob_pdf->SetY($ob_pdf->GetY()+6);
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(30, 5, "Ano/Número:", '0', 'L');
		$ob_pdf->SetFont('segoeuil','',$font_2);
		$ob_pdf->MultiCell(30, 5, $row['ano_numero'], '0', 'L');
		
		$ob_pdf->SetXY(65, ($ob_pdf->GetY()-10));
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(30, 5, "Data:", '0', 'L');
		$ob_pdf->SetX(65, ($ob_pdf->GetY()-10));
		$ob_pdf->SetFont('segoeuil','',$font_2);
		$ob_pdf->MultiCell(30, 5, $row['dt_desenquadramento_cci'], '0', 'L');

		$ob_pdf->SetXY(110, ($ob_pdf->GetY()-10));
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(30, 5, "Status:", '0', 'L');
		
		$ob_pdf->SetX(110, ($ob_pdf->GetY()-10));
		$ob_pdf->SetFont('segoeuib','',$font_1);
		
		if(trim($row['fl_status']) == 'P')
		{
			$ob_pdf->SetTextColor(220,50,50);
			$ob_pdf->MultiCell(100, 5, "Desenquadrado", '0', 'L');
		}
		else if(trim($row['fl_status']) == 'R')
		{
			$ob_pdf->SetTextColor(0,127,14);
			$ob_pdf->MultiCell(100, 5, "Regularizado em ".$row['dt_regularizado'], '0', 'L');
		}
		else
		{
			$ob_pdf->SetTextColor(255,127,42);
			$ob_pdf->MultiCell(100, 5, "Desenquadramento Passivo", '0', 'L');
		}
		$ob_pdf->SetTextColor(0,0,0);
		$ob_pdf->SetY($ob_pdf->GetY()+3);
		
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(190, 5, "Informado por:", '0', 'L');
		$ob_pdf->SetFont('segoeuil','',$font_2);
		$ob_pdf->MultiCell(190, 5, $row['usuario_enviado'], '0', 'J');
		$ob_pdf->SetY($ob_pdf->GetY()+3);			
		
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(190, 5, "Fundo/Carteira:", '0', 'L');
		$ob_pdf->SetFont('segoeuil','',$font_2);
		$ob_pdf->MultiCell(190, 5, $row['ds_desenquadramento_cci_fundo'], '0', 'J');
		$ob_pdf->SetY($ob_pdf->GetY()+3);
		
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(190, 5, "Administrador:", '0', 'L');
		$ob_pdf->SetFont('segoeuil','',$font_2);
		$ob_pdf->MultiCell(190, 5, $row['ds_desenquadramento_cci_administrador'], '0', 'J');
		$ob_pdf->SetY($ob_pdf->GetY()+3);
		
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(190, 5, "Gestor:", '0', 'L');
		$ob_pdf->SetFont('segoeuil','',$font_2);
		$ob_pdf->MultiCell(190, 5, $row['ds_desenquadramento_cci_gestor'], '0', 'J');
		$ob_pdf->SetY($ob_pdf->GetY()+3);
		
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(190, 5, "Regra:", '0', 'L');
		$ob_pdf->SetFont('segoeuil','',$font_2);
		$ob_pdf->MultiCell(190, 5, $row['regra'], '0', 'J');
		$ob_pdf->SetY($ob_pdf->GetY()+3);
		
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(190, 5, "Descrição do Desenquadramento:", '0', 'L');
		$ob_pdf->SetFont('segoeuil','',$font_2);
		$ob_pdf->MultiCell(190, 5, $row['ds_desenquadramento_cci'], '0', 'J');
		$ob_pdf->SetY($ob_pdf->GetY()+3);
		
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(190, 5, "Providências adotadas:", '0', 'L');
		$ob_pdf->SetFont('segoeuil','',$font_2);
		$ob_pdf->MultiCell(190, 5, $row['providencias_adotadas'], '0', 'J');
		$ob_pdf->SetY($ob_pdf->GetY()+3);
		
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(190, 5, "Observações:", '0', 'L');
		$ob_pdf->SetFont('segoeuil','',$font_2);
		$ob_pdf->MultiCell(190, 5, $row['observacao'], '0', 'J');
		$ob_pdf->SetY($ob_pdf->GetY()+3);

		$ob_pdf->SetFont('segoeuib','',$font_2);
		$ob_pdf->MultiCell(190, 5, "Acompanhamento", '0', 'C');
		$ob_pdf->SetY($ob_pdf->GetY()+3);

		$ob_pdf->SetWidths(array(50, 140));
		$ob_pdf->SetAligns(array('C', 'C', 'C'));
		$ob_pdf->Row(array("Dt. Acompanhamento ", "Descrição"));
		$ob_pdf->SetAligns(array('C', 'L'));
		$ob_pdf->SetFont('segoeuil','',$font_2);

		foreach ($acompanhamento as $item)
        {
			$ob_pdf->Row(array($item['dt_inclusao'], $item['descricao']));
		}
		
		#### DESENQUADRAMENTO PAI ####
		if((intval($row['cd_desenquadramento_cci_pai']) > 0) and ($fl_origem))
		{
			$ob_pdf = $this->pdfItem($ob_pdf, intval($row['cd_desenquadramento_cci_pai']));
		}
		
		return $ob_pdf;
	}
	
	public function relatorioPDF()
	{
		CheckLogin();

		if ($this->get_permissao())
        {
			$args = Array();
			$data = Array();
			$result = null;

			$args['nr_ano'] 					   = $this->input->post("nr_ano", TRUE);
			$args['nr_numero']  				   = $this->input->post("nr_numero", TRUE);					
			$args['dt_ini']  				 	   = $this->input->post("dt_ini", TRUE);					
			$args['dt_fim']  					   = $this->input->post("dt_fim", TRUE);	
            $args['dt_relatorio_ini']  			   = $this->input->post("dt_relatorio_ini", TRUE);					
			$args['dt_relatorio_fim']  			   = $this->input->post("dt_relatorio_fim", TRUE);	
			$args['cd_desenquadramento_cci_fundo'] = $this->input->post("cd_desenquadramento_cci_fundo", TRUE);					
			$args['fl_status']                     = $this->input->post("fl_status", TRUE);					
			$args['fl_encaminhado']  			   = $this->input->post("fl_encaminhado", TRUE);					
			$args['fl_envio']  					   = $this->input->post("fl_envio", TRUE);					
			$args['dt_encaminhado_ini']  		   = $this->input->post("dt_encaminhado_ini", TRUE);					
			$args['dt_encaminhado_fim']  		   = $this->input->post("dt_encaminhado_fim", TRUE);		
				
			$this->desenquadramento_cci_model->listar($result, $args);
			$ar_reg = $result->result_array();			
			
			$this->load->plugin('fpdf');
			$ob_pdf = new PDF('P', 'mm', 'A4');
			$ob_pdf->AddFont('segoeuil');
			$ob_pdf->AddFont('segoeuib');
			$ob_pdf->SetNrPag(true);
			$ob_pdf->SetMargins(10, 14, 5);
			$ob_pdf->header_exibe = true;
			$ob_pdf->header_logo = true;	
			
			$font_1 = 12;
			$font_2 = 11;
			$arr_status['']  = 'Todos';
			$arr_status['P'] = 'Desenquadrado';
			$arr_status['R'] = 'Regularizado';			
			$arr_status['D'] = 'Desenquadramento Passivo';			

			$ob_pdf->AddPage();
			$ob_pdf->SetY($ob_pdf->GetY()+2);

			$ob_pdf->SetFont('segoeuib','',$font_1 + 8);
			$ob_pdf->MultiCell(190, 5, "Relatório de Desenquadramentos", '0', 'C');
			
			$ob_pdf->Line(10, ($ob_pdf->GetY() + 5), 200, ($ob_pdf->GetY() + 5));
			$ob_pdf->SetY($ob_pdf->GetY() + 10);			
			
			$ob_pdf->SetFont('segoeuib','',$font_1);
			$ob_pdf->MultiCell(30, 5, "Filtros", '0', 'L');
			
			$ob_pdf->SetXY(30, ($ob_pdf->GetY()-5));
			$ob_pdf->SetFont('segoeuib','',$font_1);
			$ob_pdf->MultiCell(30, 5, "Data:", '0', 'L');
			$ob_pdf->SetX(30, ($ob_pdf->GetY()-10));
			$ob_pdf->SetFont('segoeuil','',$font_2);
			
			if((trim($args['dt_ini']) != "") and (trim($args['dt_fim']) != ""))
			{
				$ob_pdf->MultiCell(100, 5, $args['dt_ini']." à ".$args['dt_fim'], '0', 'L');			
			}
			else
			{
				$ob_pdf->MultiCell(100, 5, "Não Informado", '0', 'L');			
			}			
			
			$ob_pdf->SetXY(90, ($ob_pdf->GetY()-10));
			$ob_pdf->SetFont('segoeuib','',$font_1);
			$ob_pdf->MultiCell(30, 5, "Status:", '0', 'L');
			$ob_pdf->SetX(90, ($ob_pdf->GetY()-10));
			$ob_pdf->SetFont('segoeuil','',$font_2);
			$ob_pdf->MultiCell(30, 5, $arr_status[$args['fl_status']], '0', 'L');
			
			foreach($ar_reg as $ar_item)
			{
				$ob_pdf = $this->pdfItem($ob_pdf, $ar_item['cd_desenquadramento_cci'], false, false, false, false);
			}
			
			$ob_pdf->Output();
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}	
	
	function acompanhamento($cd_desenquadramento_cci)
	{
		CheckLogin();

		if ($this->get_permissao_cadastro())
        {
			$args = array();
			$data = array();
			$result = null;
			
			$data['cd_desenquadramento_cci'] = $cd_desenquadramento_cci;
			
			$this->load->view('gestao/desenquadramento_cci/acompanhamento', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
		
	function listar_acompanhamento()
	{
		CheckLogin();

		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_desenquadramento_cci'] = $this->input->post("cd_desenquadramento_cci", TRUE);
		
        $this->desenquadramento_cci_model->lista_acompanhamento($result, $args);
        $data['collection'] = $result->result_array();

        $this->load->view('gestao/desenquadramento_cci/acompanhamento_result', $data);
	}
	
	function salvar_acompanhamento()
	{
		CheckLogin();

		if ($this->get_permissao_cadastro())
        {
			$args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_desenquadramento_cci'] = $this->input->post("cd_desenquadramento_cci", TRUE);
			$args['descricao']               = $this->input->post("descricao", TRUE);
			$args['cd_usuario']              = $this->session->userdata('codigo');
			
			$this->desenquadramento_cci_model->salvar_acompanhamento($result, $args);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir_acompanhamento()
	{
		CheckLogin();

		if ($this->get_permissao_cadastro())
        {
			$args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_desenquadramento_cci_acompanhamento'] = $this->input->post("cd_desenquadramento_cci_acompanhamento", TRUE);
			$args['cd_usuario']                             = $this->session->userdata('codigo');
			
            $this->desenquadramento_cci_model->excluir_acompanhamento($result, $args);
			
            redirect("gestao/desenquadramento_cci/acompanhamento/" . $args['cd_desenquadramento_cci'], "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function anexo($cd_desenquadramento_cci)
    {
    	CheckLogin();

        $args = Array();
		$data = Array();
		$result = null;
        
        $args['cd_desenquadramento_cci'] = intval($cd_desenquadramento_cci);
        $data['cd_desenquadramento_cci'] = intval($cd_desenquadramento_cci);
        
        $this->desenquadramento_cci_model->lista_anexo( $result, $args );
        $data['collection'] = $result->result_array();
        
        $this->load->view('gestao/desenquadramento_cci/anexo', $data);
    }
	
	function salvar_anexo()
	{
		CheckLogin();

		if ($this->get_permissao_cadastro())
        {
			$result = null;
			$data = Array();
			$args = Array();
		
			$qt_arquivo = intval($this->input->post("arquivo_m_count", TRUE));
			
			if($qt_arquivo > 0)
			{
				$nr_conta = 0;
				
				while($nr_conta < $qt_arquivo)
				{
					$result = null;
					$data = Array();
					$args = Array();		
					
					$args['arquivo_nome']           = $this->input->post("arquivo_m_".$nr_conta."_name", TRUE);
					$args['arquivo']                = $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE);
					$args['cd_desenquadramento_cci'] = $this->input->post("cd_desenquadramento_cci", TRUE);
					$args["cd_usuario"]             = $this->session->userdata('codigo');
					
					$this->desenquadramento_cci_model->salvar_anexo($result, $args);
					
					$nr_conta++;
				}
			}
			
			redirect("gestao/desenquadramento_cci/anexo/".intval($args["cd_desenquadramento_cci"]), "refresh");
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir_anexo($cd_desenquadramento_cci, $cd_desenquadramento_cci_anexo)
	{
		CheckLogin();

		if ($this->get_permissao_cadastro())
        {
			$result = null;
			$data = Array();
			$args = Array();
			
			$args['cd_desenquadramento_cci']       = $cd_desenquadramento_cci;
			$args['cd_desenquadramento_cci_anexo'] = $cd_desenquadramento_cci_anexo;
			$args["cd_usuario"]                   = $this->session->userdata('codigo');

			$this->desenquadramento_cci_model->excluir_anexo($result, $args);
			
			redirect("gestao/desenquadramento_cci/anexo/".intval($args["cd_desenquadramento_cci"]), "refresh");
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}	
}
?>