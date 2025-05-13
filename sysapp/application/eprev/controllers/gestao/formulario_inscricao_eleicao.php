<?php
class Formulario_inscricao_eleicao extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	private function permissao()
    {
        #Luciano Rodriguez
        if($this->session->userdata('codigo') == 251)
        {
            return true;
        }
        #Lucio Daniel Sartori
        else if($this->session->userdata('codigo') == 415)
        {
            return true;
        }
        #Roberta Bittencourt da Costa
        else if($this->session->userdata('codigo') == 474)
        {
            return true;
        }
        #Cristiano Jacobsen de Oliveira 
        else if($this->session->userdata('codigo') == 170)
        {
            return true;
        }
		#Carlos Alberto Britto Salamoni
		else if($this->session->userdata('codigo') == 1)
		{
		    return true;
		}
		#Moacir Reis de Oliveira Junior
		else if($this->session->userdata('codigo') == 22)
		{
		    return true;
		}
		#Rodrigo Sisnandes Pereira
		else if($this->session->userdata('codigo') == 348)
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
		if($this->permissao())
		{
			$this->load->view('gestao/formulario_inscricao_eleicao/index');
		}
		else
		{
		    exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function listar()
	{
		$this->load->model('gestao/formulario_inscricao_eleicao_model');

		$args = array(
			'tp_cargo' 			=> $this->input->post('tp_cargo', TRUE),
			'fl_cancelamento'	=> $this->input->post('fl_cancelamento', TRUE),
			'dt_inclusao_ini'	=> $this->input->post('dt_inclusao_ini', TRUE),
			'dt_inclusao_fim'	=> $this->input->post('dt_inclusao_fim', TRUE),
			'fl_aprovacao'		=> $this->input->post('fl_aprovacao', TRUE),
			'fl_status'			=> $this->input->post('fl_status',TRUE),
			'nr_ano'			=> $this->input->post('nr_ano',TRUE)
		);

		manter_filtros($args);

		$data['collection']  = $this->formulario_inscricao_eleicao_model->listar($args);

		$this->load->view('gestao/formulario_inscricao_eleicao/index_result', $data);
	}

	public function cadastro($cd_formulario_inscricao_eleicao, $fl_impugnacao = 'N')
	{
		if($this->permissao())
		{
			$this->load->model('gestao/formulario_inscricao_eleicao_model');

			$data['row'] = $this->formulario_inscricao_eleicao_model->listar_cadastro($cd_formulario_inscricao_eleicao);

			$data['collection'] = $this->formulario_inscricao_eleicao_model->listar_acompanhamento($cd_formulario_inscricao_eleicao);

			$data['fl_impugnacao'] = $fl_impugnacao;

			if(trim($fl_impugnacao) == 'S')
			{
				$data['tipo_registro'] = array(
					array('value' => 'I', 'text' => 'Impugnar Inscrição')
				);
			}
			else
			{
				$data['tipo_registro'] = array(
					array('value' => 'A', 'text' => 'Registro Interno')
				);
			}

			if(trim($data['row']['dt_encaminha_pendencia']) == '')
			{
				$data['tipo_registro'][] = array('value' => 'S', 'text' => 'Pendência de Inscrição');
			}

			$this->load->view('gestao/formulario_inscricao_eleicao/cadastro', $data);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar_acompanhamento()
	{
		if($this->permissao())
		{			
			$this->load->model('gestao/formulario_inscricao_eleicao_model');

			$args = array(
				'cd_formulario_inscricao_eleicao'         	 	 => $this->input->post('cd_formulario_inscricao_eleicao', TRUE),
				'tp_formulario_inscricao_eleicao_acompanhamento' => $this->input->post('tp_formulario_inscricao_eleicao_acompanhamento', TRUE),
				'ds_formulario_inscricao_eleicao_acompanhamento' => $this->input->post('ds_formulario_inscricao_eleicao_acompanhamento', TRUE),
				'cd_usuario'  								     => $this->session->userdata('codigo')
			);

			$this->formulario_inscricao_eleicao_model->salvar_acompanhamento($args);

			if(trim($args['tp_formulario_inscricao_eleicao_acompanhamento']) == 'I')
			{
				###NÃO FAZ O ENVIO DE E-MAIL / A COMUNICAÇÃO É FEITA MANUALMENTE POR CARTA###
				//$this->email_impugnar($args['cd_formulario_inscricao_eleicao'], $args['ds_formulario_inscricao_eleicao_acompanhamento']);
			}

			redirect('gestao/formulario_inscricao_eleicao/cadastro/'.$this->input->post('cd_formulario_inscricao_eleicao', TRUE));
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	###NÃO FAZ O ENVIO DE E-MAIL / A COMUNICAÇÃO É FEITA MANUALMENTE POR CARTA###
	public function encaminhar_pendencia($cd_formulario_inscricao_eleicao)
	{
		if($this->permissao())
		{			
			$this->load->model(array(
				'gestao/formulario_inscricao_eleicao_model', 
				'projetos/eventos_email_model'
			));

			$cd_evento = 410;

			$email = $this->eventos_email_model->carrega($cd_evento);

			$cd_usuario = $this->session->userdata('codigo');

			$this->formulario_inscricao_eleicao_model->encaminhar_pendencia($cd_formulario_inscricao_eleicao, $cd_usuario);

			$row = $this->formulario_inscricao_eleicao_model->listar_cadastro($cd_formulario_inscricao_eleicao);

			$texto = str_replace('[DS_NOME]', $row['ds_nome'], $email['email']);

			$para = $row['ds_email_1']; 

	        if(trim($row['ds_email_2']) != '')
	        {
	        	$para .= ';'.$row['ds_email_2']; 
	        }

			$args = array(
				'de'      => 'Eleições FFP 2020',
				'assunto' => $email['assunto'],
				'para'    => $para,
				'cc'      => $row['ds_email_representante'],
				'cco'     => $email['cco'],
				'texto'   => $texto
			);

			###NÃO FAZ O ENVIO DE E-MAIL / A COMUNICAÇÃO É FEITA MANUALMENTE POR CARTA###
			//$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

			redirect('gestao/formulario_inscricao_eleicao/cadastro/'.$cd_formulario_inscricao_eleicao);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function aprovar_inscricao($cd_formulario_inscricao_eleicao)
	{
		if($this->permissao())
		{			
			$this->load->model('gestao/formulario_inscricao_eleicao_model');

			$this->formulario_inscricao_eleicao_model->aprovar_inscricao($cd_formulario_inscricao_eleicao, $this->session->userdata('codigo'));

			redirect('gestao/formulario_inscricao_eleicao/cadastro/'.$cd_formulario_inscricao_eleicao);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function cancelar_inscricao($cd_formulario_inscricao_eleicao)
	{
		if($this->permissao())
		{			
			$this->load->model('gestao/formulario_inscricao_eleicao_model');
			
			$this->formulario_inscricao_eleicao_model->cancelar_inscricao($cd_formulario_inscricao_eleicao);

			redirect('gestao/formulario_inscricao_eleicao/cadastro/'.$cd_formulario_inscricao_eleicao);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function acompanhamento_anexo($cd_formulario_inscricao_eleicao, $cd_formulario_inscricao_eleicao_acompanhamento, $fl_nao_atendeu = '')
	{
		if($this->permissao())
		{	
			$this->load->model('gestao/formulario_inscricao_eleicao_model');

			$data['row'] = $this->formulario_inscricao_eleicao_model->listar_arquivo($cd_formulario_inscricao_eleicao, $cd_formulario_inscricao_eleicao_acompanhamento);

			$data['collection'] = $this->formulario_inscricao_eleicao_model->listar_acompanhamento_anexo($cd_formulario_inscricao_eleicao_acompanhamento);

			$data['fl_nao_atendeu'] = $fl_nao_atendeu;

			$this->load->view('gestao/formulario_inscricao_eleicao/arquivo', $data);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar_atendeu($cd_formulario_inscricao_eleicao, $cd_formulario_inscricao_eleicao_acompanhamento)
	{
		if($this->permissao())
		{	
			$this->load->model('gestao/formulario_inscricao_eleicao_model');

			$this->formulario_inscricao_eleicao_model->salvar_atendeu($cd_formulario_inscricao_eleicao_acompanhamento,$this->session->userdata('codigo'));

			###NÃO FAZ O ENVIO DE E-MAIL / A COMUNICAÇÃO É FEITA MANUALMENTE POR CARTA###
			#$this->email_atendimento($cd_formulario_inscricao_eleicao, 'S');

			redirect('gestao/formulario_inscricao_eleicao/arquivo/'.$cd_formulario_inscricao_eleicao.'/'.$cd_formulario_inscricao_eleicao_acompanhamento);	   
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar_nao_atendeu()
	{
		if($this->permissao())
		{	
			$this->load->model('gestao/formulario_inscricao_eleicao_model');

			$cd_formulario_inscricao_eleicao                = $this->input->post('cd_formulario_inscricao_eleicao', TRUE);
			$cd_formulario_inscricao_eleicao_acompanhamento = $this->input->post('cd_formulario_inscricao_eleicao_acompanhamento', TRUE);
			$ds_formulario_inscricao_eleicao_acompanhamento = $this->input->post('ds_formulario_inscricao_eleicao_acompanhamento', TRUE);

			$this->formulario_inscricao_eleicao_model->salvar_nao_atendeu(
				$cd_formulario_inscricao_eleicao_acompanhamento, 
				$ds_formulario_inscricao_eleicao_acompanhamento,
				$this->session->userdata('codigo')
			);

			###NÃO FAZ O ENVIO DE E-MAIL / A COMUNICAÇÃO É FEITA MANUALMENTE POR CARTA###
			#$this->email_atendimento($cd_formulario_inscricao_eleicao, 'N');

			redirect('gestao/formulario_inscricao_eleicao/arquivo/'.$cd_formulario_inscricao_eleicao.'/'.$cd_formulario_inscricao_eleicao_acompanhamento);	   
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	###NÃO FAZ O ENVIO DE E-MAIL / A COMUNICAÇÃO É FEITA MANUALMENTE POR CARTA###
	private function email_atendimento($cd_formulario_inscricao_eleicao, $fl_atendeu = 'S')
	{
		$this->load->model('projetos/eventos_email_model');

		$cd_evento = 411;

		$email = $this->eventos_email_model->carrega($cd_evento);

		$cd_usuario = $this->session->userdata('codigo');

		$ds_atendeu = 'Atendeu';

		if(trim($fl_atendeu) == 'N')
		{
			$ds_atendeu = 'Não Atendeu';
		}

		$row = $this->formulario_inscricao_eleicao_model->listar_cadastro($cd_formulario_inscricao_eleicao);

		$texto = str_replace('[DS_STATUS]', $ds_atendeu, $email['email']);
		$assunto = str_replace('[DS_STATUS]', $ds_atendeu, $email['assunto']);

		$para = $row['ds_email_1']; 

        if(trim($row['ds_email_2']) != '')
        {
        	$para .= ';'.$row['ds_email_2']; 
        }

		$args = array(
			'de'      => 'Eleições FFP 2020',
			'assunto' => $assunto,
			'para'    => $para,
			'cc'      => $row['ds_email_representante'],
			'cco'     => $email['cco'],
			'texto'   => $texto
		);

		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
	}

	###NÃO FAZ O ENVIO DE E-MAIL / A COMUNICAÇÃO É FEITA MANUALMENTE POR CARTA###
	private function email_impugnar($cd_formulario_inscricao_eleicao, $ds_formulario_inscricao_eleicao_acompanhamento )
	{
		$this->load->model('projetos/eventos_email_model');

		$cd_evento = 412;

		$email = $this->eventos_email_model->carrega($cd_evento);

		$cd_usuario = $this->session->userdata('codigo');

		$row = $this->formulario_inscricao_eleicao_model->listar_cadastro($cd_formulario_inscricao_eleicao);

		$texto = str_replace(
			array('[DS_NOME]', '[DS_TEXTO]'), 
			array($row['ds_nome'], nl2br($ds_formulario_inscricao_eleicao_acompanhamento)), 
			$email['email']
		);

		$para = $row['ds_email_1']; 

        if(trim($row['ds_email_2']) != '')
        {
        	$para .= ';'.$row['ds_email_2']; 
        }

		$args = array(
			'de'      => 'Eleições FFP 2020',
			'assunto' => $email['assunto'],
			'para'    => $para,
			'cc'      => $row['ds_email_representante'],
			'cco'     => $email['cco'],
			'texto'   => $texto
		);

		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
	}

	public function anexos($cd_formulario_inscricao_eleicao)
	{
		if($this->permissao())
		{	
			$this->load->model('gestao/formulario_inscricao_eleicao_model');

			$ds_codigo = $this->formulario_inscricao_eleicao_model->get_ds_codigo($cd_formulario_inscricao_eleicao);

			$data['row'] = array(
				'cd_formulario_inscricao_eleicao'	=> $cd_formulario_inscricao_eleicao,
				'ds_codigo'							=> $ds_codigo,
				'arquivo'							=> '',
				'arquivo_nome'						=> '',
				'ds_codigo'							=> $ds_codigo['ds_codigo']
			);

			$data['collection'] = $this->formulario_inscricao_eleicao_model->listar_anexos($cd_formulario_inscricao_eleicao);

			$this->load->view('gestao/formulario_inscricao_eleicao/anexos', $data);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar_anexos()
	{
		if($this->permissao())
		{	
			$this->load->model('gestao/formulario_inscricao_eleicao_model');

			$data = Array();
			$args = Array();

			$qt_arquivo = intval($this->input->post("arquivo_m_count", TRUE));
			$cd_formulario_inscricao_eleicao = $this->input->post('cd_formulario_inscricao_eleicao', TRUE);

			if($qt_arquivo > 0)
			{
				$nr_conta = 0;
				while($nr_conta < $qt_arquivo)
				{
					$args['arquivo_nome']  					 	= $this->input->post("arquivo_m_".$nr_conta."_name", TRUE);
					$args['arquivo']       				     	= $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE);
					$args['cd_formulario_inscricao_eleicao'] 	= $cd_formulario_inscricao_eleicao;
					$args["cd_usuario"]   						= $this->session->userdata('codigo');

					$this->formulario_inscricao_eleicao_model->salvar_anexos($args);
					
					$nr_conta++;
				}
			}
			
			redirect('gestao/formulario_inscricao_eleicao/anexos/'.$cd_formulario_inscricao_eleicao);   
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function excluir_anexo($cd_formulario_inscricao_eleicao,$cd_formulario_inscricao_eleicao_anexo)
	{
		if($this->permissao())
		{	
			$this->load->model('gestao/formulario_inscricao_eleicao_model');
			
			$this->formulario_inscricao_eleicao_model->excluir_anexo($cd_formulario_inscricao_eleicao_anexo,$this->session->userdata('codigo'));

			redirect('gestao/formulario_inscricao_eleicao/anexos/'.$cd_formulario_inscricao_eleicao);   
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function inscricaoPDF($cd_formulario_inscricao_eleicao)
	{
		$this->load->plugin('fpdf');
		$this->load->model('gestao/formulario_inscricao_eleicao_model');
		$ar_cad = $this->formulario_inscricao_eleicao_model->listar_cadastro($cd_formulario_inscricao_eleicao);	


        $altura_linha = 5;
        $ob_pdf = new PDF('P', 'mm', 'A4');
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');		
        $ob_pdf->SetNrPag(true);
        $ob_pdf->SetMargins(10, 14, 5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = "Requerimento de Inscrição - ".$ar_cad["ds_codigo"];
        $ob_pdf->AddPage();

        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Cargo:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_cad['tp_cargo']);	
	
		if($ar_cad['tp_cargo'] == "CAP")
		{
			$ob_pdf->SetY($ob_pdf->GetY() + 2.5);
			$ob_pdf->SetFont('segoeuib', '', 12);
			$ob_pdf->MultiCell(190, $altura_linha, "Plano:");
			$ob_pdf->SetFont('segoeuil', '', 12);
			$ob_pdf->MultiCell(190, $altura_linha, $ar_cad['tp_cargo']);			
		}
		
        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Nome:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_cad['ds_nome']);

        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "CPF:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_cad['ds_cpf']);

        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Nome na cédula eleitoral:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_cad['ds_vinculacao']);

        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Telefone 1:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_cad['ds_telefone_1']);

        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Telefone 2:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_cad['ds_telefone_2']);
		
        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "E-Mail 1:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_cad['ds_email_1']);	

        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "E-Mail 2:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_cad['ds_email_2']);

        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Representante:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_cad['ds_representante']);

		if(trim($ar_cad['fl_representante']) == 'S')	
		{
			$ob_pdf->SetY($ob_pdf->GetY() + 2.5);
			$ob_pdf->SetFont('segoeuib', '', 12);
			$ob_pdf->MultiCell(190, $altura_linha, "Nome Representante:");
			$ob_pdf->SetFont('segoeuil', '', 12);
			$ob_pdf->MultiCell(190, $altura_linha, $ar_cad['ds_nome_representante']);

			$ob_pdf->SetY($ob_pdf->GetY() + 2.5);
			$ob_pdf->SetFont('segoeuib', '', 12);
			$ob_pdf->MultiCell(190, $altura_linha, "CPF Representante:");
			$ob_pdf->SetFont('segoeuil', '', 12);
			$ob_pdf->MultiCell(190, $altura_linha, $ar_cad['ds_cpf_representante']);

			$ob_pdf->SetY($ob_pdf->GetY() + 2.5);
			$ob_pdf->SetFont('segoeuib', '', 12);
			$ob_pdf->MultiCell(190, $altura_linha, "Telefone Representante:");
			$ob_pdf->SetFont('segoeuil', '', 12);
			$ob_pdf->MultiCell(190, $altura_linha, $ar_cad['ds_telefone_representante']);

			$ob_pdf->SetY($ob_pdf->GetY() + 2.5);
			$ob_pdf->SetFont('segoeuib', '', 12);
			$ob_pdf->MultiCell(190, $altura_linha, "E-Mail Representante:");
			$ob_pdf->SetFont('segoeuil', '', 12);
			$ob_pdf->MultiCell(190, $altura_linha, $ar_cad['ds_email_representante']);		
			
		}

		$ob_pdf->SetY($ob_pdf->GetY() + 2.5);
		$ob_pdf->SetFont('segoeuil', '', 12);
		$ob_pdf->MultiCell(190, $altura_linha, "---------------------------------------------------------------------------------------------------------------");				

        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, 'DECLARO, sob as penas de lei, que não possuo qualquer impedimento legal que impeça a minha inscrição, bem como tenho pleno conhecimento dos termos disposto no Regulamento Eleitoral e no Código de Ética da Fundação CEEE. Neste ato, ainda, DECLARO pleno conhecimento dos termos do § 1º do art. 5º da Resolução CNPC nº 19/2015, alterada pela Resolução CNPC nº 21/2015, bem como da redação dos incisos I e II do art. 3º da Instrução PREVIC nº 6/2017, que preveem a exigibilidade de Certificação para o exercício dos cargos de Conselheiros e Diretor no prazo de um (1) ano a contar da data da posse.');


        $ob_pdf->Output();
        exit;		
		
	}
}