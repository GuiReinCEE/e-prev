<?php
class encaminhamento extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index($cd_empresa = "", $cd_registro_empregado = "", $seq_dependencia = "", $cd_atendimento_encaminhamento_tipo = "")
    {
		CheckLogin();
        if(gerencia_in(array('GCM')))
        {
            $this->load->model('projetos/Atendimento_model');
			$this->load->model('projetos/atendimento_encaminhamento_model');
            $args   = array();
			$data   = array();
            $result = null;
			
            $data['atendente_dd'] = $this->atendimento_encaminhamento_model->listar_atendente();
						
            $this->atendimento_encaminhamento_model->combo_atendimento_encaminhamento_tipo($result, $args);
            $data['ar_atendimento_encaminhamento_tipo'] = $result->result_array();
			

            $data['cd_empresa']            = $cd_empresa;
            $data['cd_registro_empregado'] = $cd_registro_empregado;
            $data['seq_dependencia']       = $seq_dependencia;
            $data['cd_atendimento_encaminhamento_tipo'] = $cd_atendimento_encaminhamento_tipo;

            $this->load->view('ecrm/encaminhamento/index.php', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function listar()
    {
        CheckLogin();
        if(gerencia_in(array('GCM')))
        {
            $this->load->model('projetos/atendimento_encaminhamento_model');
            $args   = array();
			$data   = array();
            $result = null;

            $args['cd_empresa']                         = $this->input->post('cd_empresa', TRUE);
            $args['cd_registro_empregado']              = $this->input->post('cd_registro_empregado', TRUE);
            $args['seq_dependencia']                    = $this->input->post('seq_dependencia', TRUE);
            $args['nome']                               = $this->input->post('nome', TRUE);
            $args['dt_hora_inicio_atendimento_inicio']  = $this->input->post('inicio', TRUE);
            $args['dt_hora_inicio_atendimento_fim']     = $this->input->post('fim', TRUE);
            $args['id_atendente']                       = $this->input->post('atendente', TRUE);
            $args['cd_atendimento_encaminhamento_tipo'] = $this->input->post('cd_atendimento_encaminhamento_tipo', TRUE);
            $args['fl_encaminhamento']                  = $this->input->post('situacao_filtro', TRUE);

            manter_filtros($args);

            $this->atendimento_encaminhamento_model->listar( $result, $count, $args );
            $data['collection'] = $result->result_array();

            $this->load->view('ecrm/encaminhamento/partial_result', $data);
       }
       else
       {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
       }
    }

    function detalhe($cd=0, $cd_encaminhamento=0)
    {
        CheckLogin();
        if(gerencia_in(array('GCM')))
        {
            $this->load->model('projetos/Atendimento_encaminhamento_model');

            $data['tipo'] = $this->Atendimento_encaminhamento_model->get_tipo();

            $args=array();
            $data['cd'] = intval($cd);

            $row=$this->Atendimento_encaminhamento_model->atendimento( $cd );
            $data['atendimento'] = $row;

            $row=$this->Atendimento_encaminhamento_model->antendimento_encaminhamento( $cd, $cd_encaminhamento );
            $data['encaminhamento'] = $row;

            $row=$this->Atendimento_encaminhamento_model->info_atendimento( $cd, $result, $args );
            $data['info_atendimento'] = $result->result_array();
			
	/*		$data['emprestimo'] = $this->atendimento_encaminhamento_model->get_emprestimo_dados($cd);
						
			echo '<pre>';
			print_r($data);
			exit;
			*/
            $this->load->view('ecrm/encaminhamento/detalhe', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function encerra_atendimento($cd_atendimento=0,$cd_encaminhamento=0)
    {
         CheckLogin();
         if(gerencia_in(array('GCM')))
         {
             if((intval($cd_atendimento) > 0) AND (intval($cd_encaminhamento) > 0))
             {
                $args=array();
                $result = null;

                $this->load->model('projetos/Atendimento_encaminhamento_model');
                $this->load->model('projetos/Atendimento_model');

                $args['cd_usuario_logado'] = $this->session->userdata('codigo');
                $args['cd_atendimento'] = intval($cd_atendimento);
                $args['cd_encaminhamento'] = intval($cd_encaminhamento);

                $this->Atendimento_encaminhamento_model->encerrar($args);

                $this->Atendimento_model->encaminhar($args);

                redirect("ecrm/encaminhamento/detalhe/".$args['cd_atendimento']. '/'. $args['cd_encaminhamento'], "refresh");


             }
             else
             {
                 exibir_mensagem("NÃO FOI POSSIVEL ENCERRAR O ATENDIMENTO.");
             }
          }
          else
          {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
          }
    }

    function cancela_atendimento($cd_atendimento=0,$cd_encaminhamento=0)
    {
		 CheckLogin();
		 if(gerencia_in(array('GCM')))
		 {
         	$cd_atendimento    = $this->input->post('cd_atendimento', TRUE);
         	$cd_encaminhamento = $this->input->post('cd_encaminhamento', TRUE);

			if((intval($cd_atendimento) > 0) AND (intval($cd_encaminhamento) > 0))
			{
				$args=array();
				$result = null;

				$this->load->model('projetos/Atendimento_encaminhamento_model');
				$this->load->model('projetos/Atendimento_model');

				$args['cd_usuario_logado'] 			= $this->session->userdata('codigo');
				$args['cd_atendimento']    			= intval($cd_atendimento);
				$args['cd_encaminhamento'] 			= intval($cd_encaminhamento);
				$args['ds_observacao_cancelamento'] = $this->input->post('ds_observacao_cancelamento', TRUE);

				$this->Atendimento_encaminhamento_model->cancelar($args);

				$this->Atendimento_model->encaminhar($args);

				redirect("ecrm/encaminhamento/detalhe/".$args['cd_atendimento']. '/'. $args['cd_encaminhamento'], "refresh");
			}
			else
			{
				exibir_mensagem("NÃO FOI POSSIVEL ENCERRAR O ATENDIMENTO.");
			}
		}
		else
		{
		exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	public function emprestimoSalvar()
	{
		CheckLogin();
		if(gerencia_in(array('GCM')))
		{
			$this->load->model('projetos/Atendimento_encaminhamento_model');
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_contrato_emprestimo'] = intval($this->input->post('cd_contrato_emprestimo_'.$this->input->post('id_confere_emprestimo', TRUE), TRUE)); 
			$args['cd_atendimento']         = intval($this->input->post('cd_atendimento', TRUE));   
			$args['cd_encaminhamento']      = intval($this->input->post('cd_encaminhamento', TRUE));   
			$args['id_confere_emprestimo']  = intval($this->input->post('id_confere_emprestimo', TRUE)); 
			$args['ds_observacao']          = trim($this->input->post('ds_observacao', TRUE)); 
			$args['cd_usuario']             = $this->session->userdata('codigo');

			//echo "<pre>";print_r($args);exit;

			$this->Atendimento_encaminhamento_model->encaminhamento_emprestimo($result, $args);
			
			redirect("ecrm/encaminhamento/detalhe/".$args['cd_atendimento']. '/'. $args['cd_encaminhamento'], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}	
	
	public function emprestimo()
	{
		$this->load->model('projetos/Atendimento_encaminhamento_model');
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_contrato_emprestimo'] = intval($this->input->post("cd_contrato_emprestimo", TRUE)); 
		$args['cd_empresa']             = intval($this->input->post("cd_empresa", TRUE)); 
		$args['cd_registro_empregado']  = intval($this->input->post("cd_registro_empregado", TRUE)); 
		$args['seq_dependencia']        = intval($this->input->post("seq_dependencia", TRUE)); 
		
		$this->Atendimento_encaminhamento_model->emprestimo($result, $args);
		$ar_reg = $result->row_array();
		
		
		#print_r($args);
		#print_r($ar_reg);
		
		if(count($ar_reg) > 0)
		{
			if(
			  (intval($args['cd_empresa'])            != intval($ar_reg['cd_empresa'])) or
			  (intval($args['cd_registro_empregado']) != intval($ar_reg['cd_registro_empregado'])) or
			  (intval($args['seq_dependencia'])       != intval($ar_reg['seq_dependencia']))
			  )
			{
				echo "<span style='color: red; font-weigth: bold; font-size: 160%'>CONTRATO NÃO É DO PARTICIPANTE</span>";
			}
			else
			{
				echo '
						<table>
							<tr>
								<td>Nome:</td>
								<td><b>'.$ar_reg["nome"].'</b></td>
							</tr>
							<tr>
								<td>Tipo:</td>
								<td style="font-size: 130%; color: blue;"><b>'.$ar_reg["tipo_emprestimo"].'</b></td>
							</tr>									
							<tr>
								<td>Dt Solicitação:</td>
								<td>'.$ar_reg["dt_solicitacao"].'</td>
							</tr>	
							<tr>
								<td>Dt Depósito:</td>
								<td><b>'.$ar_reg["dt_deposito"].'</b></td>
							</tr>
							<tr>
								<td>Dt 1º Pagamento:</td>
								<td><b>'.$ar_reg["dt_primeiro_pagamento"].'</b></td>
							</tr>	
							<tr>
								<td>Dt Último Pagamento:</td>
								<td>'.$ar_reg["dt_ultimo_pagamento"].'</td>
							</tr>								
							<tr>
								<td>Nr Parcelas:</td>
								<td style="font-size: 130%; color: blue;"><b>'.$ar_reg["nro_prestacoes"].'</b></td>
							</tr>	
							<tr>
								<td>Vl Parcela (R$):</td>
								<td style="font-size: 130%; color: blue;"><b>'.number_format($ar_reg["vlr_prestacao"],2,",",".").'</b></td>
							</tr>	
							<tr>
								<td>Vl Solicitado (R$):</td>
								<td>'.number_format($ar_reg["vlr_solicitado"],2,",",".").'</td>
							</tr>								
							<tr>
								<td>Comprometimento (%):</td>
								<td style="font-size: 130%; color: blue;"><b>'.number_format($ar_reg["perc_comprometimento"],2,",",".").'</b></td>
							</tr>								
							
							
							<tr>
								<td>Vl Montante Concedido (R$):</td>
								<td>'.number_format($ar_reg["montante_concedido"],2,",",".").'</td>
							</tr>	
							<tr>
								<td>Vl Concedido (R$):</td>
								<td>'.number_format($ar_reg["vlr_concedido"],2,",",".").'</td>
							</tr>	
							<tr>
								<td>Vl Depósito (R$):</td>
								<td style="font-size: 130%; color: blue;"><b>'.number_format($ar_reg["vlr_deposito"],2,",",".").'</b></td>
							</tr>
							<tr>
								<td>Banco:</td>
								<td><b>'.$ar_reg["banco"].'</b></td>
							</tr>
							<tr>
								<td>Conta:</td>
								<td><b>'.$ar_reg["conta"].'</b></td>
							</tr>
							<tr>
								<td>Agência:</td>
								<td><b>'.$ar_reg["agencia"].'</b></td>
							</tr>								
						</table>
					 ';
			}
		}
		else
		{
			echo "<span style='color: #6400E0; font-weigth: bold; font-size: 160%'>CONTRATO NÃO ENCONTRADO</span>";
		}
		
	}	
	
    function cadastro($cd_empresa, $cd_registro_empregado, $seq_dependencia, $cd_atendimento = 0)
    {
		CheckLogin();
        if(gerencia_in(array('GCM')))
        {
			$this->load->model('projetos/atendimento_encaminhamento_model');
            $args   = array();
			$data   = array();
            $result = null;
			
			#openCiEprev "https://www.e-prev.com.br/cieprev/index.php/ecrm/encaminhamento/cadastro/" & edEMP.Text & "/" & edRE.Text & "/" & edSEQ.Text & "/" & CD_ATENDIMENTO
			
            $this->atendimento_encaminhamento_model->combo_atendimento_encaminhamento_tipo($result, $args);
            $data['ar_atendimento_encaminhamento_tipo'] = $result->result_array();

            $data['cd_empresa']            = $cd_empresa;
            $data['cd_registro_empregado'] = $cd_registro_empregado;
            $data['seq_dependencia']       = $seq_dependencia;
            $data['cd_atendimento']        = $cd_atendimento;

            $this->load->view('ecrm/encaminhamento/cadastro.php', $data);
			
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

	public function cadastroSalvar()
	{
		CheckLogin();
		if(gerencia_in(array('GCM')))
		{
			$this->load->model('projetos/atendimento_encaminhamento_model');
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_atendimento']                     = $this->input->post('cd_atendimento', TRUE);   
			$args['cd_empresa']                         = $this->input->post('cd_empresa', TRUE);   
			$args['cd_registro_empregado']              = $this->input->post('cd_registro_empregado', TRUE); 
			$args['seq_dependencia']                    = $this->input->post('seq_dependencia', TRUE); 
			$args['cd_atendimento_encaminhamento_tipo'] = $this->input->post('cd_atendimento_encaminhamento_tipo', TRUE); 
			$args['descricao']                          = $this->input->post('descricao', TRUE); 
			$args['cd_usuario']                         = $this->session->userdata('codigo');

			$cd_encaminhamento = $this->atendimento_encaminhamento_model->cadastroSalvar($args);
			
			redirect("ecrm/encaminhamento/detalhe/".$args['cd_atendimento'].'/'.intval($cd_encaminhamento), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}	

	public function salvar_tipo()
	{
		CheckLogin();
		
		if(gerencia_in(array('GCM')))
		{
			$this->load->model('projetos/atendimento_encaminhamento_model');

			$this->atendimento_encaminhamento_model->salvar_tipo(
				$this->input->post('cd_atendimento', TRUE),
				$this->input->post('cd_encaminhamento', TRUE),
				$this->input->post('cd_atendimento_encaminhamento_tipo', TRUE),
				$this->session->userdata('codigo')
			);

			redirect('ecrm/encaminhamento/detalhe/'.$this->input->post('cd_atendimento', TRUE).'/'.$this->input->post('cd_encaminhamento', TRUE), "refresh");
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function cancelamento($cd_atendimento = 0, $cd_encaminhamento = 0)
	{
		CheckLogin();
		
		if(gerencia_in(array('GCM')))
		{
			$this->load->model('projetos/atendimento_encaminhamento_model');

			$data = array(
				'atendimento' 	 => $this->atendimento_encaminhamento_model->atendimento($cd_atendimento),
				'encaminhamento' => $this->atendimento_encaminhamento_model->antendimento_encaminhamento($cd_atendimento, $cd_encaminhamento)
			);

			$this->load->view('ecrm/encaminhamento/cancelamento', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}