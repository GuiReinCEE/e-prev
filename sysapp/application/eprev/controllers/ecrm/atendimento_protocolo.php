<?php
class atendimento_protocolo extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		$this->load->model('projetos/atendimento_protocolo_model');
    }

    function index($cd_empresa = "", $cd_registro_empregado = "", $seq_dependencia = "")
    {
    	CheckLogin();

		$data   = array();
		$args   = array();
		$result = null;		
		
		$this->atendimento_protocolo_model->remetente($result, $args);
		$data['arr_remetente'] = $result->result_array();

		$this->atendimento_protocolo_model->tipo($result, $args);
		$data['arr_tipo'] = $result->result_array();

		$this->atendimento_protocolo_model->discriminacao($result, $args);
		$data['arr_discriminacao'] = $result->result_array();
		
		$this->atendimento_protocolo_model->comboGerenciaOrigem($result, $args);
		$data['arr_gerencia_origem'] = $result->result_array();

		$this->load->view('ecrm/atendimento_protocolo/index', $data);
    }

    function listar()
    {	
    	CheckLogin();

		$data   = array();
		$args   = array();
		$result = null;

		$args['cd_atendimento_protocolo_tipo']          = $this->input->post('cd_atendimento_protocolo_tipo', TRUE);
		$args['cd_atendimento_protocolo_discriminacao'] = $this->input->post('cd_atendimento_protocolo_discriminacao', TRUE);
		$args['cd_empresa']                             = $this->input->post('cd_empresa', TRUE);
		$args['cd_registro_empregado']                  = $this->input->post('cd_registro_empregado', TRUE);
		$args['seq_dependencia']                        = $this->input->post('seq_dependencia', TRUE);
		$args['nome']                                   = $this->input->post('nome', TRUE);
		$args['fl_recebido']                            = $this->input->post('fl_recebido', TRUE);
		$args['fl_cancelado']                           = $this->input->post('fl_cancelado', TRUE);		
		$args['cd_gerencia_origem']                     = $this->input->post('cd_gerencia_origem', TRUE);		
		$args['dt_inclusao_inicial']                    = $this->input->post('dt_inclusao_inicial', TRUE);
		$args['dt_inclusao_final']                      = $this->input->post('dt_inclusao_final', TRUE);
		$args['hr_inclusao_inicial']                    = $this->input->post('hr_inclusao_inicial', TRUE);
		$args['hr_inclusao_final']                      = $this->input->post('hr_inclusao_final', TRUE);
		$args['dt_cancelamento']                        = $this->input->post('dt_cancelamento', TRUE);
		$args['dt_recebimento_inicial']                 = $this->input->post('dt_recebimento_inicial', TRUE);
		$args['dt_recebimento_final']                   = $this->input->post('dt_recebimento_final', TRUE);
		$args['cd_usuario_inclusao']                    = $this->input->post('cd_usuario_inclusao', TRUE);
		$args['cd_atendimento']                         = $this->input->post('cd_atendimento', TRUE);
		$args['cd_encaminhamento']                      = $this->input->post('cd_encaminhamento', TRUE);
        $args['identificacao']                          = $this->input->post('identificacao', TRUE);        
        $args['dt_devolucao_inicial']                   = $this->input->post('dt_devolucao_inicial', TRUE);
		$args['dt_devolucao_final']                     = $this->input->post('dt_devolucao_final', TRUE);
		$args['fl_devolvido']                           = $this->input->post('fl_devolvido', TRUE);

        manter_filtros($args);

		$this->atendimento_protocolo_model->listar( $result, $args );
        $data['collection'] = $result->result_array();
        
        $data['cd_divisao'] = $this->session->userdata('divisao');

		$this->load->view('ecrm/atendimento_protocolo/partial_result', $data);
    }

    function listarPDF()
    {   
    	CheckLogin();

		$data   = array();
		$args   = array();
		$result = null;
		
		$this->load->plugin('fpdf');

		$args['cd_atendimento_protocolo_tipo']          = $this->input->post('cd_atendimento_protocolo_tipo', TRUE);
		$args['cd_atendimento_protocolo_discriminacao'] = $this->input->post('cd_atendimento_protocolo_discriminacao', TRUE);
		$args['cd_empresa']                             = $this->input->post('cd_empresa', TRUE);
		$args['cd_registro_empregado']                  = $this->input->post('cd_registro_empregado', TRUE);
		$args['seq_dependencia']                        = $this->input->post('seq_dependencia', TRUE);
		$args['nome']                                   = $this->input->post('nome', TRUE);
		$args['fl_recebido']                            = $this->input->post('fl_recebido', TRUE);
		$args['fl_cancelado']                           = $this->input->post('fl_cancelado', TRUE);	
		$args['cd_gerencia_origem']                     = $this->input->post('cd_gerencia_origem', TRUE);			
		$args['dt_inclusao_inicial']                    = $this->input->post('dt_inclusao_inicial', TRUE);
		$args['dt_inclusao_final']                      = $this->input->post('dt_inclusao_final', TRUE);
		$args['hr_inclusao_inicial']                    = $this->input->post('hr_inclusao_inicial', TRUE);
		$args['hr_inclusao_final']                      = $this->input->post('hr_inclusao_final', TRUE);
		$args['dt_cancelamento']                        = $this->input->post('dt_cancelamento', TRUE);
		$args['dt_recebimento_inicial']                 = $this->input->post('dt_recebimento_inicial', TRUE);
		$args['dt_recebimento_final']                   = $this->input->post('dt_recebimento_final', TRUE);
		$args['cd_usuario_inclusao']                    = $this->input->post('cd_usuario_inclusao', TRUE);
		$args['cd_atendimento']                         = $this->input->post('cd_atendimento', TRUE);
		$args['cd_encaminhamento']                      = $this->input->post('cd_encaminhamento', TRUE);
		$args['identificacao']                          = $this->input->post('identificacao', TRUE);

		manter_filtros($args);
		
		$this->atendimento_protocolo_model->listar( $result, $args );
		$collection = $result->result_array();

		$ob_pdf = new PDF('L','mm','A4');

		$ob_pdf->SetNrPag(true);
		$ob_pdf->SetMargins(10,14,5);
		$ob_pdf->header_exibe = true;
		$ob_pdf->header_logo = true;
		$ob_pdf->header_titulo = true;
		$ob_pdf->header_titulo_texto = "Protocolo de Correspondências";

		$ob_pdf->AddPage();
		$ob_pdf->SetY($ob_pdf->GetY() + 2);
		$ob_pdf->SetFont( 'Courier', 'B', 10 );
		$ob_pdf->MultiCell(190, 4.5, "Remessa: ". $args['dt_inclusao_inicial']. " até ".  $args['dt_inclusao_final']);
		$ob_pdf->SetY($ob_pdf->GetY() + 4);

		$ob_pdf->SetLineWidth(0);
		$ob_pdf->SetDrawColor(0,0,0);
		$ob_pdf->SetWidths( array(25, 75, 15, 50, 30, 80) );
		$ob_pdf->SetAligns( array('C','C','C','C','C','C') );
		$ob_pdf->SetFont( 'Courier', 'B', 10 );
		$ob_pdf->Row(array("Emp/RE/Seq", "Nome/destino", "Tipo", "Discriminação", "Atend/Enc", "Histórico"));
		$ob_pdf->SetAligns( array('L','L','C','C','C','L') );
		$ob_pdf->SetFont( 'Courier', '', 10 );
		
		foreach($collection as $ar_reg)
		{
			if($ar_reg['cd_atendimento'] != '' AND $ar_reg['cd_encaminhamento'] != '')
			{
				$atenEnc = $ar_reg['cd_atendimento'].'/'. $ar_reg['cd_encaminhamento'];
			}
			else
			{
				$atenEnc = '';
			}

			$historico = 'Envio: ' . $ar_reg["nome_gap"] . ' ' . $ar_reg["dt_inclusao"];

			if($ar_reg["nome_gad"]!=null)
			{
				$historico.= "\n".'Recebimento: ' . $ar_reg["nome_gad"] . ' ' . $ar_reg["dt_recebimento"];
			}

			if($ar_reg["dt_cancelamento"]!=null)
			{
				$historico.= "\n".'Cancelamento: ' . $ar_reg["dt_cancelamento"];
			}

			$ob_pdf->Row(array($ar_reg['cd_empresa'].'/'.$ar_reg['cd_registro_empregado'].'/'.$ar_reg['seq_dependencia'],
				$ar_reg['nome']."\n". $ar_reg['ds_destino'], $ar_reg['tipo_nome'], $ar_reg['discriminacao_nome'] . "\n" .
				$ar_reg['identificacao'], $atenEnc, $historico));
		}

		$ob_pdf->Output();
		exit;
    }

    function malaDireta()
    {
    	CheckLogin();

		$data   = array();
		$args   = array();
		$result = null;
		
		$this->load->plugin('fpdf');

		$args['cd_atendimento_protocolo_tipo']          = $this->input->post('cd_atendimento_protocolo_tipo', TRUE);
		$args['cd_atendimento_protocolo_discriminacao'] = $this->input->post('cd_atendimento_protocolo_discriminacao', TRUE);
		$args['cd_empresa']                             = $this->input->post('cd_empresa', TRUE);
		$args['cd_registro_empregado']                  = $this->input->post('cd_registro_empregado', TRUE);
		$args['seq_dependencia']                        = $this->input->post('seq_dependencia', TRUE);
		$args['nome']                                   = $this->input->post('nome', TRUE);
		$args['fl_recebido']                            = $this->input->post('fl_recebido', TRUE);
		$args['fl_cancelado']                           = $this->input->post('fl_cancelado', TRUE);
		$args['cd_gerencia_origem']                     = $this->input->post('cd_gerencia_origem', TRUE);	
		$args['dt_inclusao_inicial']                    = $this->input->post('dt_inclusao_inicial', TRUE);
		$args['dt_inclusao_final']                      = $this->input->post('dt_inclusao_final', TRUE);
		$args['hr_inclusao_inicial']                    = $this->input->post('hr_inclusao_inicial', TRUE);
		$args['hr_inclusao_final']                      = $this->input->post('hr_inclusao_final', TRUE);
		$args['dt_cancelamento']                        = $this->input->post('dt_cancelamento', TRUE);
		$args['dt_recebimento_inicial']                 = $this->input->post('dt_recebimento_inicial', TRUE);
		$args['dt_recebimento_final']                   = $this->input->post('dt_recebimento_final', TRUE);


		$args['dt_devolucao_inicial']                   = $this->input->post('dt_devolucao_inicial', TRUE);
		$args['dt_devolucao_final']                     = $this->input->post('dt_devolucao_final', TRUE);
		$args['fl_devolvido']                           = $this->input->post('fl_devolvido', TRUE);

		$args['cd_usuario_inclusao']                    = $this->input->post('cd_usuario_inclusao', TRUE);
		$args['cd_atendimento']                         = $this->input->post('cd_atendimento', TRUE);
		$args['cd_encaminhamento']                      = $this->input->post('cd_encaminhamento', TRUE);
		$args['ds_usuario']                             = $this->session->userdata('usuario');
		$args['identificacao']                          = $this->input->post('identificacao', TRUE);

		manter_filtros($args);
		
		$this->atendimento_protocolo_model->listar( $result, $args );
		$collection = $result->result_array();

		$this->atendimento_protocolo_model->malaDiretaLimpar( $result, $args );

		foreach( $collection as $ar_reg )
		{
			$row['cd_empresa']            = $ar_reg['cd_empresa'];
			$row['cd_registro_empregado'] = $ar_reg['cd_registro_empregado'];
			$row['seq_dependencia']       = $ar_reg['seq_dependencia'];
			$row['ds_usuario']            = $this->session->userdata('usuario');
			$this->atendimento_protocolo_model->malaDireta( $result, $row );
		}
		
		redirect("ecrm/atendimento_protocolo", "refresh");
    }

    function detalhe($cd_atendimento_protocolo=0, $manter = '', $vl_1 = '', $vl_2 = '', $vl_3 = '')
    {
    	CheckLogin();

		$data   = array();
		$args   = array();
		$result = null;

		$data['cd_atendimento_protocolo'] = intval($cd_atendimento_protocolo);

		$this->atendimento_protocolo_model->tipo($result, $args);
		$data['tipo'] = $result->result_array();

		$this->atendimento_protocolo_model->discriminacao($result, $args);
		$data['discriminacao'] = $result->result_array();
		
        $data['manter']                   = '';
        
        $data['cd_divisao'] = $this->session->userdata('divisao');

		if(intval($cd_atendimento_protocolo) == 0 || $manter == 'T')
		{
			$data['manter'] = $manter;

			$cd_empresa = '';
			$cd_registro_empregado = '';
			$seq_dependencia = '';
			$cd_atendimento_protocolo_tipo = '';
			$cd_atendimento_protocolo_discriminacao = 18;
			$ds_identificacao = '';

			if($manter != '')
			{
				if($manter == 'P')
				{
					$cd_empresa = $vl_1;
					$cd_registro_empregado = $vl_2;
					$seq_dependencia = $vl_3;
				}
				else if($manter == 'T')
				{
					$data['manter'] = 'T';
					$cd_atendimento_protocolo_tipo = $vl_1;
					$cd_atendimento_protocolo_discriminacao = $vl_2;
					
					$args['cd_atendimento_protocolo'] = intval($cd_atendimento_protocolo);
					$this->atendimento_protocolo_model->carrega_descricao($result, $args);
					$ds_identificacao = $result->row_array();

				}
			}

			$data['row'] = Array(
				'cd_atendimento_protocolo'               => 0,
				'cd_empresa'                             => $cd_empresa,
				'cd_registro_empregado'                  => $cd_registro_empregado,
				'seq_dependencia'                        => $seq_dependencia,
				'nome'                                   => '',
				'ds_destino'                             => '',
				'ds_identificacao'                       => $ds_identificacao,
				'cd_atendimento_protocolo_tipo'          => $cd_atendimento_protocolo_tipo,
				'cd_atendimento_protocolo_discriminacao' => $cd_atendimento_protocolo_discriminacao,
				'cd_atendimento'                         => '',
				'cd_encaminhamento'                      => '',
				'ds_discriminacao'                       => '',
				'dt_inclusao'                            => date('d/m/Y'),
				'cd_usuario_recebimento'                 => '',
				'dt_recebimento'                         => '',
				'dt_cancelamento'                        => '',
				'nome_gad'                               => '',
				'dt_devolvido'                           => '',
				'dt_devolucao'                           => '',
				'ds_descricao_devolvido'                 => '',
				'ds_motivo'                              => '',
				'ds_descricao_tipo'                      => ''
			);
		} 
		else
		{
			$args['cd_atendimento_protocolo'] = intval($cd_atendimento_protocolo);
			$this->atendimento_protocolo_model->carrega($result, $args);
			
			$data['row'] = $result->row_array();
		}
		$this->load->view('ecrm/atendimento_protocolo/detalhe',$data);

    }

    public function salvar_devolucao()
    {
    	CheckLogin();
    	
        $args = array(
            'cd_atendimento_protocolo' => $this->input->post('cd_atendimento_protocolo', TRUE),
            'dt_devolucao'             => $this->input->post('dt_devolucao', TRUE),
            'ds_descricao_devolvido'   => $this->input->post('ds_descricao_devolvido', TRUE),
            'cd_usuario'               => $this->session->userdata('codigo')
        );

        $this->atendimento_protocolo_model->salvar_devolucao($args);

        redirect('ecrm/atendimento_protocolo', 'refresh');
    }

    function importar()
    {
    	if($this->session->userdata('codigo') == 251)
    	{
    		//OS : 43500
    		//$ponteiro = fopen (BASEPATH.'importar/arquivo20160616.txt', 'r');

    		//OS : 46727
    		//$ponteiro = fopen (BASEPATH.'importar/arquivo20160624.txt', 'r');
  			
    		//OS : 46942
    		//$ponteiro = fopen (BASEPATH.'importar/arquivo20160725.txt', 'r');

    		//OS : 47308
    		//$ponteiro = fopen (BASEPATH.'importar/arquivo20160906.txt', 'r');

    		//OS : 48230
    		$ponteiro = fopen (BASEPATH.'importar/arquivo20170118.txt', 'r');

    		$retorno = array();

  			while (!feof ($ponteiro)) 
  			{
  				$args = array();

  				$linha = fgets($ponteiro, 4096);

  				$array = explode(';', $linha);

  				$args = array(
  					'cd_atendimento_protocolo'               => 0,
  					'cd_empresa'                             => $array[0],
  					'cd_registro_empregado'                  => $array[1],
  					'seq_dependencia'                        => $array[2],
  					'nome'                                   => $array[3],
  					'ds_destino'                             => $array[4],
  					'cd_atendimento_protocolo_tipo'          => $array[5],
  					'cd_atendimento_protocolo_discriminacao' => $array[6],
  					'ds_identificacao'                       => $array[7],
  					'cd_gerencia_origem'                     => 'GP',
  					'cd_usuario_inclusao'                    => 55,
  					'cd_atendimento'                         => '',
  					'cd_encaminhamento'                      => ''
  				);

  				$retorno[] = $this->atendimento_protocolo_model->salvar($result, $args);
  			}

  			fclose ($ponteiro);

  			echo "<pre>";
  			print_r($retorno);
    	}
    	else
    	{
    		redirect("ecrm/atendimento_protocolo", "refresh");
    	}
    }

    public function salvar_orcl()
    {
    	$args   = array();
		$result = null;

		$usuario = $this->atendimento_protocolo_model->get_cd_usuario($this->input->post('usuario', TRUE));

		$args = array(
			'cd_atendimento_protocolo'               => 0,
			'cd_empresa'                             => $this->input->post('cd_empresa', TRUE),
			'cd_registro_empregado'                  => $this->input->post('cd_registro_empregado', TRUE),
			'seq_dependencia'                        => $this->input->post('seq_dependencia', TRUE),
			'nome'                                   => $this->input->post('nome', TRUE),
			'ds_destino'                             => $this->input->post('ds_destino', TRUE),
			'cd_atendimento_protocolo_tipo'          => $this->input->post('cd_atendimento_protocolo_tipo', TRUE),
			'cd_atendimento_protocolo_discriminacao' => $this->input->post('cd_atendimento_protocolo_discriminacao', TRUE),
			'ds_identificacao'                       => $this->input->post('ds_identificacao', TRUE),
			'cd_gerencia_origem'                     => $this->input->post('cd_gerencia_origem', TRUE),
			'cd_usuario_inclusao'                    => intval($usuario['cd_usuario']),
			'cd_atendimento'                         => '',
			'cd_encaminhamento'                      => '',
			'ds_descricao_tipo'                      => ''
		);

		echo $this->atendimento_protocolo_model->salvar($result, $args);
    }

    function salvar()
    {
    	CheckLogin();

		$data   = array();
		$args   = array();
		$result = null;

		$args["cd_atendimento_protocolo"]               = $this->input->post("cd_atendimento_protocolo", TRUE);
		$args["nome"]                                   = $this->input->post("nome", TRUE);
		$args["ds_identificacao"]                       = $this->input->post("ds_identificacao", TRUE);
		$args["cd_usuario_inclusao"]                    = $this->session->userdata('codigo');
		$args["cd_gerencia_origem"]                     = $this->session->userdata('divisao');
		$args["ds_destino"]                             = $this->input->post("ds_destino", TRUE);
		$args["cd_empresa"]                             = $this->input->post("cd_empresa", TRUE);
		$args["cd_registro_empregado"]                  = $this->input->post("cd_registro_empregado", TRUE);
		$args["seq_dependencia"]                        = $this->input->post("seq_dependencia", TRUE);
		$args["cd_atendimento_protocolo_tipo"]          = $this->input->post("cd_atendimento_protocolo_tipo", TRUE);
		$args["cd_atendimento_protocolo_discriminacao"] = $this->input->post("cd_atendimento_protocolo_discriminacao", TRUE);
		$args["cd_protocolo_encaminhamento"]            = $this->input->post("cd_protocolo_encaminhamento", TRUE);
		$args["manter"]                                 = $this->input->post("manter", TRUE);
		$args["ds_descricao_tipo"]                      = $this->input->post("ds_descricao_tipo", TRUE);

		$arrcd_protocolo_encaminhamento = explode('/', $args["cd_protocolo_encaminhamento"]);

		if(count($arrcd_protocolo_encaminhamento) > 1)
		{
			$args["cd_atendimento"]    = $arrcd_protocolo_encaminhamento[0];
			$args["cd_encaminhamento"] = $arrcd_protocolo_encaminhamento[1];
		} 
		else
		{
			$args["cd_atendimento"]    = $this->input->post("cd_atendimento", TRUE);
			$args["cd_encaminhamento"] = $this->input->post("cd_encaminhamento", TRUE);
		}

		$cd_atendimento_protocolo = $this->atendimento_protocolo_model->salvar($result, $args);
		
		if(intval($args["cd_atendimento_protocolo_tipo"]) == 8)
		{
			$this->load->model('projetos/eventos_email_model');
			
			$cd_evento = 474;
        
			$email = $this->eventos_email_model->carrega($cd_evento);

			$tags = array('[LINK]');

			$subs = array(
				site_url('ecrm/atendimento_protocolo/detalhe/'.intval($cd_atendimento_protocolo))
			);

			$texto = str_replace($tags, $subs, $email['email']);

			$ds_usuario_email = $this->session->userdata('usuario').'@eletroceee.com.br';
			
			$args = array(
				'de'      => 'Protocolo Correspondência Expedida',
				'assunto' => $email['assunto'],
				'para'    => $email['para'],
				'cc'      => $email['cc'],
				'cco'     => $email['cco'],
				'texto'   => $texto
			);
			
			$cd_usuario = $this->session->userdata('codigo');
			
			$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
		}
			

		if($args["cd_atendimento_protocolo"] == 0 AND $args["manter"] != '')
		{
			if($args["manter"] == 'P')
			{
				redirect("ecrm/atendimento_protocolo/detalhe/0/P/".$args["cd_empresa"]."/".$args["cd_registro_empregado"]."/".$args["seq_dependencia"], "refresh");
			}
			else if ($args["manter"] == 'T')
			{
				redirect("ecrm/atendimento_protocolo/detalhe/".$cd_atendimento_protocolo."/T/".$args["cd_atendimento_protocolo_tipo"]."/".$args["cd_atendimento_protocolo_discriminacao"], "refresh");
			}

		}
		else
		{
			redirect("ecrm/atendimento_protocolo", "refresh");
		}
    }

    function receber($cd_atendimento_protocolo=0)
    {
    	CheckLogin();

        if(gerencia_in(array('GFC')))
        {
            if(intval($cd_atendimento_protocolo) > 0)
            {
				$data   = array();
				$args   = array();
				$result = null;

                manter_filtros($args);

                $args['cd_usuario_logado']        = $this->session->userdata('codigo');
                $args['cd_atendimento_protocolo'] = intval($cd_atendimento_protocolo);

                $this->atendimento_protocolo_model->receber($result, $args);

                redirect("ecrm/atendimento_protocolo", "refresh"); 
            }
            else
            {
                exibir_mensagem("NÃO FOI POSSIVEL RECEBER.");
            }
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function receber_todos()
	{
		CheckLogin();

		if(gerencia_in(array('GFC')))
        {
			$check = $this->input->post("check", TRUE);  
			
			$args['cd_usuario_logado'] = $this->session->userdata('codigo');
			
			foreach($check as $item)
			{	
				$args['cd_atendimento_protocolo'] = $item;
				
				$this->atendimento_protocolo_model->receber($result, $args);
			}
		
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

    function cancelarSalvar()
    {
    	CheckLogin();

		$data   = array();
		$args   = array();
		$result = null;

		$args['cd_atendimento_protocolo'] = $this->input->post('cd_atendimento_protocolo', TRUE);
		$args['ds_motivo']                = $this->input->post('ds_motivo', TRUE);
		$args["cd_usuario"]               = usuario_id();

		$this->atendimento_protocolo_model->cancelar($result, $args);

		redirect("ecrm/atendimento_protocolo", "refresh");
    }

    function cancelar($cd_atendimento_protocolo=0)
    {
    	CheckLogin();

		if(intval($cd_atendimento_protocolo) > 0)
		{
			$data   = array();
			$args   = array();
			$result = null;

			$data['cd_atendimento_protocolo'] = intval($cd_atendimento_protocolo);

			$this->load->view('ecrm/atendimento_protocolo/cancelar.php', $data);
		}
		else
		{
			exibir_mensagem("NÃO FOI POSSIVEL CANCELAR.");
		}
    }

    function encaminhamento()
    {
    	CheckLogin();

		$data   = array();
		$args   = array();
		$result = null;

		$args['cd_empresa'] = $this->input->post('cd_empresa', TRUE);
		$args['cd_registro_empregado'] = $this->input->post('cd_registro_empregado', TRUE);
		$args['seq_dependencia'] = $this->input->post('seq_dependencia', TRUE);
		$args['cd_encaminhamento'] = $this->input->post('cd_encaminhamento', TRUE);
		$args['cd_atendimento'] = $this->input->post('cd_atendimento', TRUE);

		$this->atendimento_protocolo_model->encaminhamento( $result, $args );

		$data['collection'] = $result->result_array();

		$data['quantos'] = sizeof($data['collection']);

		$check = $args['cd_atendimento'].'/'.$args['cd_encaminhamento'];
		if( $result )
		{
            $combo = '<select id="cd_protocolo_encaminhamento" name="cd_protocolo_encaminhamento" onchange=carregaTexto()>';
            $combo .= '<option value="0">Selecione</option>';
            foreach( $data['collection'] as $item )
			{
                $combo .= '<option value="'.$item['cd_atendimento'].'/'.$item['cd_encaminhamento'].'"';

                if($check == $item['cd_atendimento'].'/'.$item['cd_encaminhamento'])
                {
                    $combo .= ' selected ';
                }

                $combo .= '>'
                            .$item['cd_atendimento'].'/'.$item['cd_encaminhamento']
                         .'</option>';
            }
            $combo .= '</select>';
        }

        echo $combo;
    }

    function carregaTextoEncaminhamento()
    {
    	CheckLogin();

		$data   = array();
		$args   = array();
		$result = null;

        $cd_protocolo_encaminhamento = $this->input->post('cd_protocolo_encaminhamento', TRUE);

        $arrcd_protocolo_encaminhamento = explode('/', $cd_protocolo_encaminhamento);
        if(count($arrcd_protocolo_encaminhamento) > 1)
        {
            $args['cd_atendimento'] = $arrcd_protocolo_encaminhamento[0];
            $args['cd_encaminhamento'] = $arrcd_protocolo_encaminhamento[1];

            $this->atendimento_protocolo_model->textoEncaminhamento( $result, $args );
            $data['row'] = $result->row_array();
            echo $data['row']['texto_encaminhamento'] ;
        }
        else
        {
            echo '';
        }
        
    }
	
    function matriz_documento()
    {	
    	CheckLogin();
    	
		$data   = array();
		$args   = array();
		$result = null;

		$args['cd_tipo_doc'] = $this->input->post('cd_tipo_doc', TRUE);

		$this->atendimento_protocolo_model->matriz_documento($result, $args);
		$data = $result->row_array();

		$ar_ret["cd_discriminacao"] = ((array_key_exists('cd_discriminacao', $data)) ? $data["cd_discriminacao"] : "");
		
		echo json_encode($ar_ret);
    }	
}
?>
