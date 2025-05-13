<?php
class Documento_protocolo_conf_gerencia extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	private function get_permissao()
	{
		if(gerencia_in(array('GFC')))
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
			$this->load->view('ecrm/documento_protocolo_conf_gerencia/index');
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
			$this->load->model('projetos/documento_protocolo_conf_gerencia_model');

			$args = array();

			manter_filtros($args);

			$data['collection'] = $this->documento_protocolo_conf_gerencia_model->listar($args);

			$this->load->view('ecrm/documento_protocolo_conf_gerencia/index_result', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function cadastro($cd_documento_protocolo_conf_gerencia)
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/documento_protocolo_conf_gerencia_model');

			$data['row'] 	  = $this->documento_protocolo_conf_gerencia_model->carrega($cd_documento_protocolo_conf_gerencia);
			$data['usuarios'] = $this->documento_protocolo_conf_gerencia_model->get_usuarios($data['row']['cd_gerencia']);

			$this->load->view('ecrm/documento_protocolo_conf_gerencia/cadastro', $data);
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
			$this->load->model('projetos/documento_protocolo_conf_gerencia_model');

			$cd_documento_protocolo_conf_gerencia = $this->input->post('cd_documento_protocolo_conf_gerencia', TRUE);

			$args = array(
				'fl_conferencia' 		 => 'S',
				'cd_usuario_responsavel' => $this->input->post('cd_usuario_responsavel', TRUE),
				'nr_amostragem' 		 => app_decimal_para_db($this->input->post('nr_amostragem', true)),
				'cd_usuario' 			 => $this->session->userdata('codigo')
			);

			$this->documento_protocolo_conf_gerencia_model->atualizar($cd_documento_protocolo_conf_gerencia, $args);

			redirect('ecrm/documento_protocolo_conf_gerencia', 'refresh');
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
			$this->load->model('projetos/documento_protocolo_conf_gerencia_model');

			$data['ano']      = $this->documento_protocolo_conf_gerencia_model->get_ano_relatorio();
			$data['gerencia'] = $this->documento_protocolo_conf_gerencia_model->get_gerencia();

			$this->load->view('ecrm/documento_protocolo_conf_gerencia/relatorio', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function listar_relatorio()
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/documento_protocolo_conf_gerencia_model');

			$args = array(
				'mes_referencia' => $this->input->post('mes_referencia', TRUE),
				'ano_referencia' => $this->input->post('ano_referencia', TRUE),
				'cd_gerencia'    => $this->input->post('cd_gerencia', TRUE)
			);

			manter_filtros($args);

			$data['collection'] = $this->documento_protocolo_conf_gerencia_model->listar_relatorio($args);
 
			$this->load->view('ecrm/documento_protocolo_conf_gerencia/relatorio_result', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function documentos($cd_documento_protocolo_conf_gerencia_item_mes)
	{
		if($this->get_permissao())
		{
			$data['cd_documento_protocolo_conf_gerencia_item_mes'] = $cd_documento_protocolo_conf_gerencia_item_mes;
			$data['drop_status'] = array(
				array('value' => 'P', 'text' => 'Pendente'),
				array('value' => 'C', 'text' => 'Conferido'),
				array('value' => 'A', 'text' => 'Ajustes')
			);
 
			$this->load->view('ecrm/documento_protocolo_conf_gerencia/documentos', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function listar_documentos()
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/documento_protocolo_conf_gerencia_model');

			$cd_documento_protocolo_conf_gerencia_item_mes = $this->input->post('cd_documento_protocolo_conf_gerencia_item_mes', TRUE);

			$args = array(
				'fl_status' => $this->input->post('fl_status', TRUE)
			);

			manter_filtros($args);

			$data['collection'] = $this->documento_protocolo_conf_gerencia_model->listar_docs_relatorio($cd_documento_protocolo_conf_gerencia_item_mes, $args);
 
			$this->load->view('ecrm/documento_protocolo_conf_gerencia/documentos_result', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function acompanhamento($cd_documento_protocolo_conf_gerencia_item)
	{
		$this->load->model('projetos/documento_protocolo_conf_gerencia_model');

		$documento  = $this->documento_protocolo_conf_gerencia_model->carrega_doc($cd_documento_protocolo_conf_gerencia_item);
		$cd_usuario = $this->session->userdata('codigo');

		if($this->get_permissao() OR (intval($documento['cd_usuario_responsavel']) == intval($cd_usuario) OR intval($documento['cd_usuario_envio']) == intval($cd_usuario)))
		{
			$data['documento']      = $documento;
			$data['collection']     = $this->documento_protocolo_conf_gerencia_model->listar_acompanhamento($cd_documento_protocolo_conf_gerencia_item);

			$row = $this->documento_protocolo_conf_gerencia_model->get_acompanhamento($cd_documento_protocolo_conf_gerencia_item);

			$data['cd_usuario_inclusao'] = (isset($row['cd_usuario_inclusao']) ? intval($row['cd_usuario_inclusao']) : 0);

			$data['fl_documento']   = (trim($documento['fl_status']) != 'C' ? $this->get_permissao() : FALSE);
			$data['cd_usuario']     = $cd_usuario;
 
			$this->load->view('ecrm/documento_protocolo_conf_gerencia/acompanhamento', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar_acompanhamento()
	{
		$this->load->model('projetos/documento_protocolo_conf_gerencia_model');

		$cd_documento_protocolo_conf_gerencia_item = $this->input->post('cd_documento_protocolo_conf_gerencia_item', TRUE);

		$documento  = $this->documento_protocolo_conf_gerencia_model->carrega_doc($cd_documento_protocolo_conf_gerencia_item);
		$cd_usuario = $this->session->userdata('codigo');

		if($this->get_permissao() OR (intval($documento['cd_usuario_responsavel']) == intval($cd_usuario) OR intval($documento['cd_usuario_envio']) == intval($cd_usuario)))
		{
			$cd_documento_protocolo_conf_gerencia_item_mes = $this->input->post('cd_documento_protocolo_conf_gerencia_item_mes', TRUE);

			$acompanhamento    = '';
			$tp_acompanhamento = '';
			$fl_ajuste         = FALSE;

			if(trim($this->input->post('fl_status', TRUE)) == 'A' AND $this->get_permissao())
			{
				$acompanhamento    = 'Documento ajustado : ';
				$tp_acompanhamento = 'A';
				$fl_ajuste         = TRUE;
			}

			$args = array(
				'cd_documento_protocolo_conf_gerencia_item' => $cd_documento_protocolo_conf_gerencia_item,
				'ds_acompanhamento' 						=> $acompanhamento.$this->input->post('ds_acompanhamento', TRUE),
				'ds_ajuste' 								=> ($fl_ajuste ? $this->input->post('ds_acompanhamento', TRUE) : ''),
				'fl_status' 								=> (!$fl_ajuste ? $this->input->post('fl_status', TRUE) : 'P'),
				'fl_acompanhamento'							=> (!$fl_ajuste ? 'N' : 'S'),
				'tp_acompanhamento'                         => $tp_acompanhamento,
				'cd_usuario' 								=> $cd_usuario
			);

			$this->documento_protocolo_conf_gerencia_model->salvar_acompanhamento($args);

			$this->documento_protocolo_conf_gerencia_model->salvar_ajuste($cd_documento_protocolo_conf_gerencia_item, $args);

			if(trim($this->input->post('fl_status', TRUE)) == 'A' AND $this->get_permissao())
			{
				$this->load->model('projetos/eventos_email_model');

				$cd_evento = 417;

				$cd_usuario = $this->session->userdata('codigo');

				$email = $this->eventos_email_model->carrega($cd_evento);

				$texto = str_replace('[DT_LIMITE]', $documento['dt_limite'], $email['email']);
				$texto = str_replace('[LINK]', site_url('ecrm/documento_protocolo_conf_gerencia/acompanhamento'.$cd_documento_protocolo_conf_gerencia_item), $email['email']);

				$args = array(
					'de'      => 'Protocolo Digitalização - Conferência de Documento',
					'assunto' => $email['assunto'],
					'para'    => $row['ds_usuario_responsavel_email'].';'.$row['ds_usuario_envio_email'],
					'cc'      => $email['cc'],
					'cco'     => $email['cco'],
					'texto'   => $texto
				);

				$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
				
			}

			redirect('ecrm/documento_protocolo_conf_gerencia/acompanhamento/'.$cd_documento_protocolo_conf_gerencia_item, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function excluir_acompanhamento($cd_documento_protocolo_conf_gerencia_item, $cd_documento_protocolo_conf_gerencia_item_acompanhamento)
	{
		$this->load->model('projetos/documento_protocolo_conf_gerencia_model');

		$acompanhamento = $this->documento_protocolo_conf_gerencia_model->carrega_acompanhamento($cd_documento_protocolo_conf_gerencia_item_acompanhamento);
		$cd_usuario 	= $this->session->userdata('codigo');

		if(intval($cd_usuario) == intval($acompanhamento['cd_usuario_inclusao']) AND trim($acompanhamento['fl_acompanhamento']) != 'S')
		{
			$this->documento_protocolo_conf_gerencia_model->excluir_acompanhamento($cd_documento_protocolo_conf_gerencia_item_acompanhamento, $cd_usuario);

			redirect('ecrm/documento_protocolo_conf_gerencia/acompanhamento/'.$cd_documento_protocolo_conf_gerencia_item, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function gerar_pdf()
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/documento_protocolo_conf_gerencia_model');

			$args = array(
				'mes_referencia' => $this->input->post('mes_referencia', TRUE),
				'ano_referencia' => $this->input->post('ano_referencia', TRUE),
				'cd_gerencia'    => $this->input->post('cd_gerencia', TRUE)
			);

			$collection = $this->documento_protocolo_conf_gerencia_model->listar_relatorio($args);

			$this->load->plugin('fpdf');
				
			$ob_pdf = new PDF();
			$ob_pdf->AddFont('segoeuil');
            $ob_pdf->AddFont('segoeuib');	
			$ob_pdf->SetNrPag(TRUE);
	        $ob_pdf->SetMargins(3, 14, 5);
	        $ob_pdf->header_exibe        = TRUE;
	        $ob_pdf->header_logo         = TRUE;
	        $ob_pdf->header_titulo       = TRUE;
	        $ob_pdf->header_titulo_texto = "";

	        $ob_pdf->SetTitle('Conferência de Documentos - Relatório');
	        $ob_pdf->SetLineWidth(0);
	        $ob_pdf->SetDrawColor(0, 0, 0);

	        $ob_pdf->AddPage();
	        $ob_pdf->SetY($ob_pdf->GetY() + 1);
	        $ob_pdf->SetFont('segoeuib', '', 12);
	        $ob_pdf->MultiCell(190, 4.5, 'Conferência de Documentos - Relatório', 0, 'C');
			
	        $ob_pdf->SetY($ob_pdf->GetY() + 5);

	        $ob_pdf->MultiCell(190, 4.5, 'Mês: '.(intval($args['mes_referencia']) > 0 ? $args['mes_referencia'] : "Todos"), 0, 'L');
	        $ob_pdf->MultiCell(190, 4.5, 'Ano: '.(intval($args['ano_referencia']) > 0 ? $args['ano_referencia'] : "Todos"), 0, 'L');
	        $ob_pdf->MultiCell(190, 4.5, 'Gerência: '.(trim($args['cd_gerencia']) != '' ? $args['cd_gerencia'] : "Todas"), 0, 'L');

	        $ob_pdf->SetY($ob_pdf->GetY() + 5);

			$ob_pdf->SetWidths(array(24, 24, 24, 22, 22, 22, 22, 22, 22));
			$ob_pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
			$ob_pdf->SetFont('segoeuib', '', 10);

			$ob_pdf->Row(array('Dt. Referência',
						       'Dt. Início',
							   'Dt. Limite',
							   'Gerência',
							   'Qt. Doc. Indexados',
					           'Qt. Doc. p/ Conf.',
							   'Qt. Doc. Conferido',
							   'Qt. Doc. Pendentes',
							   'Qt. Doc. Ajustes'));

	        foreach($collection as $key => $item)
	        {
				$ob_pdf->Row(array($item['dt_referencia'],
            				 	   $item['dt_inclusao'],
    						 	   $item['dt_limite'],
    						 	   $item['cd_gerencia'],
    						 	   $item['qt_indexados'],
            				 	   $item['qt_conferencia'],
				    		 	   $item['qt_conferido'],
				    		 	   $item['qt_conferencia_pendente'],
				    		 	   $item['qt_ajuste']));
	        }

	        $ob_pdf->Output();
	        exit;
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}

	}
}