<?php
class senge_inscricao extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('expansao/inscritos_model');
    }

    function index()
    {
		$args = Array();
		$data = Array();
		$result = null;

        $this->load->view('planos/senge_inscricao/index');
    }

    function listar()
    {
        $args = Array();
		$data = Array();
		$result = null;

		$args['situacao'] = $this->input->post('situacao',TRUE);

		manter_filtros($args);

        $this->inscritos_model->listar( $result, $args );
		$data['collection'] = $result->result_array();

        $this->load->view('planos/senge_inscricao/index_result', $data);
    }
	
	function cadastro($cd_registro_empregado)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_registro_empregado'] = $cd_registro_empregado;
		$args['cd_empresa']            = 7;
		$args['seq_dependencia']       = 0;
		
		$this->inscritos_model->carrega($result, $args);
		$data['row'] = $result->row_array();
		
		$this->inscritos_model->mensagem_pedido_inscricao($result, $args);
		$row = $result->row_array();
		
		$data['mensagem'] = $row['email'];
		
		$args['cd_instituicao'] = $data['row']['cd_instituicao'];
		
		$this->inscritos_model->banco( $result, $args );
		$data['arr_banco'] = $result->result_array();
		
		$this->inscritos_model->agencia( $result, $args );
		$data['arr_agencia'] = $result->result_array();
		
		$this->inscritos_model->estado_civil( $result, $args );
		$data['arr_estado_civil'] = $result->result_array();
		
		$this->inscritos_model->grau_instrucao( $result, $args );
		$data['arr_grau_instrucao'] = $result->result_array();
		
		$this->inscritos_model->peculio( $result, $args );
		$data['arr_peculio'] = $result->result_array();
		
		$this->load->view('planos/senge_inscricao/cadastro', $data);
	}
	
	function salvar()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['nome']                  = $this->input->post('nome',TRUE);
		$cpf = $this->input->post('cpf',TRUE);
		$cpf = str_replace('.', '', $cpf);
		$cpf = str_replace('-', '', $cpf);
		$args['cpf']                   = $cpf;
		$args['rg']                    = $this->input->post('rg',TRUE);
		$args['emissor']               = $this->input->post('emissor',TRUE);
		$args['dt_emissao']            = $this->input->post('dt_emissao',TRUE);
		$args['crea']                  = $this->input->post('crea',TRUE);
		$args['cd_instituicao']        = $this->input->post('cd_instituicao',TRUE);
		$args['cd_agencia']            = $this->input->post('cd_agencia',TRUE);
		$args['conta_bco']             = $this->input->post('conta_bco',TRUE);
		$args['sexo']                  = $this->input->post('sexo',TRUE);
		$args['dt_nascimento']         = $this->input->post('dt_nascimento',TRUE);
		$args['cd_estado_civil']       = $this->input->post('cd_estado_civil',TRUE);
		$args['cd_grau_instrucao']     = $this->input->post('cd_grau_instrucao',TRUE);
		$args['nome_pai']              = $this->input->post('nome_pai',TRUE);
		$args['nome_mae']              = $this->input->post('nome_mae',TRUE);
		$args['opt_irpf']              = $this->input->post('opt_irpf',TRUE);
		$args['cd_empresa']            = $this->input->post('cd_empresa',TRUE);
		$args['cd_registro_empregado'] = $this->input->post('cd_registro_empregado',TRUE);
		
		$this->inscritos_model->salvar( $result, $args );
		
		redirect("planos/senge_inscricao/cadastro/".$args['cd_registro_empregado'], "refresh");
	}
	
	function contato($cd_registro_empregado)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_registro_empregado'] = $cd_registro_empregado;
		$args['cd_empresa']            = 7;
		$args['seq_dependencia']       = 0;
		
		$this->inscritos_model->carrega($result, $args);
		$data['row'] = $result->row_array();
		
		$args['uf']    = $data['row']['uf'];
		$args['email'] = $data['row']['email'];
		
		$this->inscritos_model->estado( $result, $args );
		$data['arr_estado'] = $result->result_array();
		
		$this->inscritos_model->cidade( $result, $args );
		$data['arr_cidade'] = $result->result_array();
		
		$this->inscritos_model->contato( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->load->view('planos/senge_inscricao/contato', $data);
	}
	
	function pedido_inscricao($cd_registro_empregado, $tp_irpf = '')
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_registro_empregado'] = $cd_registro_empregado;
		$args['cd_empresa']            = 7;
		$args['seq_dependencia']       = 0;
		
		$this->inscritos_model->carrega($result, $args);
		$row = $result->row_array();
		
		if(count($row) == 0)
		{
			$row = array(
				'nome'                     => '',
				'dt_nascimento'            => '',
				'sexo'                     => '',
				'cpf'                      => '',
				'descricao_grau_instrucao' => '',
				'rg'                       => '',
				'emissor'                  => '', 
				'dt_emissao'               => '',
				'banco'                    => '',
				'cd_agencia'               => '',
				'conta_bco'                => '',
				'endereco'                 => '',
				'bairro'                   => '',
				'nome_cidade'              => '',
				'uf'                       => '',
				'cep_complemento'          => '',
				'ddd_telefone'             => '',
				'email'                    => '',
				'nome_pai'                 => '',
				'nome_mae'                 => '',
				'naturalidade'             => '',
				'nacionalidade'            => '',
				'cd_pacote'                => '',
				'opt_irpf'                 => ''
			);
		}
		
		$this->inscritos_model->peculio( $result, $args );
		$arr_peculio = $result->result_array();
		
		$args['tipo_cobranca'] = 'I';
		
		$this->inscritos_model->taxa_adm($result, $args);
		$row_internet = $result->row_array();
		
		$args['tipo_cobranca'] = 'C';
		
		$this->inscritos_model->taxa_adm($result, $args);
		$row_correio = $result->row_array();
		
		$opt_internet = '  ';
		$opt_correio  = '  ';
		
		if(intval($cd_registro_empregado) > 4)
		{
			if (intval($row['cd_pacote']) == 1) 
			{
				$opt_internet = 'X';
				
				$vl_adm = $row_internet['preco'];
			}
			else 
			{
				$opt_correio  = 'X';
				
				$vl_adm = $row_correio['preco'];
			}
		}

		
		if((intval($cd_registro_empregado) == 1) OR (intval($cd_registro_empregado) == 3))
		{
			$args['cd_materia'] = 33;
		}
		else if((intval($cd_registro_empregado) == 2) OR (intval($cd_registro_empregado) == 4))
		{
			$args['cd_materia'] = 32;
		}
		else if(intval($cd_registro_empregado) == 0)
		{
			if(intval($tp_irpf) == 0)
			{
				$args['cd_materia'] = 32;
			}
			else if(intval($tp_irpf) == 1)
			{
				$args['cd_materia'] = 33;
			}
		}
		else
		{
			if(intval($row['opt_irpf']) == 1)
			{
				$args['cd_materia'] = 32;
			}
			else
			{
				$args['cd_materia'] = 33;
			}
		}
		
		$this->inscritos_model->conteudo($result, $args);
		$row_conteudo = $result->row_array();
		
		$fl_debito = true;
		
		if(intval($cd_registro_empregado) > 4)
		{
			$this->inscritos_model->conta_contribuicao($result, $args);
			$row_conta_contribuicao = $result->row_array();

			if(count($row_conta_contribuicao) > 0)
			{
				$vl_valor   = number_format(($row_conta_contribuicao['vlr_debito'] + $vl_adm),2,',','');
				$nr_banco   = $row_conta_contribuicao['banco'];
				$nr_agencia = $row_conta_contribuicao['agencia'];
				$nr_conta   = $row_conta_contribuicao['conta'];			
			}
			else
			{
				$fl_debito = false;
			}
		}
		else
		{
			$nr_banco   = "BANRISUL";
			$nr_agencia = "_____________________________";
			$nr_conta   = "_____________________________";
			$vl_valor   = "_________________";
		}
		
		$this->load->plugin('fpdf');
		
		$ob_pdf = new PDF('P', 'mm', 'A4');
		$ob_pdf->SetNrPag(true);
		$ob_pdf->SetMargins(10, 14, 5);
		$ob_pdf->header_exibe = true;
		$ob_pdf->header_logo = true;
		$ob_pdf->header_titulo = true;
		$ob_pdf->header_titulo_texto = "Pedido de Inscrição";
		
		$ob_pdf->AddPage();
		
		$ob_pdf->SetFont('Arial', '', 9);
		$ob_pdf->MultiCell(190, 5, "À
FUNDAÇÃO CEEE DE SEGURIDADE SOCIAL - ELETROCEEE
Solicito a minha inscrição no quadro de participantes desta Fundação CEEE, no Plano de Benefícios do SENGE Previdência, responsabilizando-me, para todos os fins, pelas informações prestadas, quanto aos dados cadastrais, bem como da relação de meus beneficiários, comprometendo-me a informar a essa Fundação CEEE, de qualquer alteração que possa vir a ocorrer nestas informações.", '0', 'L');
		
		$ob_pdf->setY($ob_pdf->getY() + 3);
		
		$ob_pdf->MultiCell(190, 5, "Atenciosamente,", '0', 'C');
		
		$ob_pdf->SetFont('Arial', 'B', 9);
		$ob_pdf->MultiCell(190, 5, "Assinatura do Requerente:____________________________", '0', 'L');
		$ob_pdf->MultiCell((intval($cd_registro_empregado) > 10 ? 190 : 174), -5, "Data: ".(intval($cd_registro_empregado) > 10 ? date('d/m/Y') : ''), '0', 'R');
		$ob_pdf->MultiCell(190, 5, "", '0', 'R');
		
		$ob_pdf->SetFont('Arial', '', 9);
		$ob_pdf->MultiCell(190, 5, "Nome: ".$row['nome'], '0', 'L');
		$ob_pdf->MultiCell(190, 5, "Instituidor: Sindicato dos Engenheiros no Estado do Rio Grande do Sul - SENGE/RS", '0', 'L');
		
		$ob_pdf->setY($ob_pdf->getY() + 3);
		
		$ob_pdf->MultiCell(190, 7, "Dados Cadastrais", '0', 'C');
		
		$ob_pdf->MultiCell(190, 5, "Data de Nascimento: ".$row['dt_nascimento'], '0', 'L');
		$ob_pdf->MultiCell(190, 5, "Sexo: ".(trim($row['sexo']) == 'M' ? 'Masculino' : 'Feminino'), '0', 'L');
		$ob_pdf->MultiCell(190, 5, "CPF: ".$row['cpf'], '0', 'L');
		$ob_pdf->MultiCell(190, 5, "Grau Instrução: ".$row['descricao_grau_instrucao'], '0', 'L');
		
		$ob_pdf->MultiCell(190, 5, "Identidade: ".$row['rg'], '0', 'L');
		$ob_pdf->MultiCell(190, 5, "Órgão Expedidor: ".$row['emissor'], '0', 'L');
		$ob_pdf->MultiCell(190, 5, "Data Expedição: ".$row['dt_emissao'], '0', 'L');
		
		$ob_pdf->MultiCell(190, 5, "Banco: ".$row['banco'], '0', 'L');
		$ob_pdf->MultiCell(190, 5, "Agência: ".$row['cd_agencia'], '0', 'L');
		$ob_pdf->MultiCell(190, 5, "Conta: ".$row['conta_bco'], '0', 'L');
		
		$ob_pdf->MultiCell(190, 5, "Logradouro: ".$row['endereco'], '0', 'L');
		$ob_pdf->MultiCell(190, 5, "Bairro: ".$row['bairro'], '0', 'L');
		$ob_pdf->MultiCell(190, 5, "Cidade: ".$row['nome_cidade'], '0', 'L');
		$ob_pdf->MultiCell(190, 5, "UF: ".$row['uf'], '0', 'L');
		$ob_pdf->MultiCell(190, 5, "CEP: ".$row['cep_complemento'], '0', 'L');
		
		$ob_pdf->MultiCell(190, 5, "Telefone: ".$row['ddd_telefone'], '0', 'L');
		$ob_pdf->MultiCell(190, 5, "Email: ".$row['email'], '0', 'L');
		
		$ob_pdf->MultiCell(190, 5, "Nome da Mãe: ".$row['nome_mae'], '0', 'L');
		$ob_pdf->MultiCell(190, 5, "Nome do Pai: ".$row['nome_pai'], '0', 'L');
		
		$ob_pdf->MultiCell(190, 5, "Naturalidade: ".$row['naturalidade'], '0', 'L');
		$ob_pdf->MultiCell(190, 5, "Nacionalidade: ".$row['nacionalidade'], '0', 'L');
		
		if(intval($cd_registro_empregado) <= 4)
		{
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			$ob_pdf->MultiCell(190, 7, "Inscrição de Beneficiários Pecúlio", '0', 'C');
				
			$ob_pdf->SetWidths(array(135, 55));
			$ob_pdf->SetAligns(array('C', 'C'));
			$ob_pdf->Row(array("Nome ", "Percentual"));
			$ob_pdf->SetAligns(array('L', 'R'));
			
			$i = 0;
			
			while(intval($i) <= 8)
			{
				$ob_pdf->Row(array('', ' %  '));
				
				$i++;
			}
		}
		else
		{
			if(count($arr_peculio) > 0)
			{
				$ob_pdf->setY($ob_pdf->getY() + 3);
				
				$ob_pdf->MultiCell(190, 7, "Inscrição de Beneficiários Pecúlio", '0', 'C');
				
				$ob_pdf->SetWidths(array(135, 55));
				$ob_pdf->SetAligns(array('C', 'C'));
				$ob_pdf->Row(array("Nome ", "Percentual"));
				$ob_pdf->SetAligns(array('L', 'C'));
				
				foreach ($arr_peculio as $item)
				{
					$ob_pdf->Row(array($item['nome'], number_format($item['percentual'],2,',','').'%'));
				}
			}
		}
		
		$ob_pdf->setY($ob_pdf->getY() + 3);
		
		$ob_pdf->MultiCell(190, 7, "Opção pela Taxa de Administração", '0', 'C');

		$ob_pdf->MultiCell(190, 5, "( ".$opt_internet." ) ".$row_internet['descricao']." - Envio de correspondências pela INTERNET - ".number_format($row_internet['preco'],2,',',''), '0', 'L');
		$ob_pdf->MultiCell(190, 5, "( ".$opt_correio." ) ".$row_correio['descricao']." - Envio de correspondências através dos CORREIOS - ".number_format($row_correio['preco'],2,',',''), '0', 'L');
		
		$ob_pdf->SetFont('Arial', 'B', 9);
		$ob_pdf->MultiCell(190, 7, "Rubrica:________", '0', 'L');
		
		$ob_pdf->setY($ob_pdf->getY() + 3);
		
		$ob_pdf->SetFont('Arial', '', 9);
		$ob_pdf->MultiCell(190, 7, strip_tags($row_conteudo['conteudo']), '0', 'L');
		
		if($fl_debito)
		{
			$ob_pdf->setY($ob_pdf->getY() + 3);
		
			$ob_pdf->MultiCell(190, 7, 'Autorização para Débito em Conta', '0', 'C');
			$ob_pdf->MultiCell(190, 5, 'Autorizo a Fundação CEEE de Seguridade Social a debitar na conta corrente do BANRISUL abaixo indicada, no primeiro dia útil de cada mês, o valor de R$ '.$vl_valor.', referente a contribuição do plano SENGE Previdência.  
Estou ciente de que não ocorrendo o débito em conta do valor autorizado, efetuarei o pagamento através de documento de arrecadação.
O débito em conta corrente autorizado será sempre no primeiro dia útil de cada mês.
A primeira contribuição para o plano SENGE Previdência deverá ser efetuada via documento de arrecadação.', '0', 'L');

			$ob_pdf->SetFont('Arial', 'B', 9);
			$ob_pdf->MultiCell(190, 7, 'Banco: '.$nr_banco, '0', 'L');
			$ob_pdf->MultiCell(190, 7, 'Agência: '.$nr_agencia, '0', 'L');
			$ob_pdf->MultiCell(190, 7, 'Conta: '.$nr_conta, '0', 'L');
			$ob_pdf->MultiCell(190, 7, 'Local/Data:_______________________,  ___/___/______ Assinatura do Requerente:  _________________________', '0', 'L');
		}
		
		$ob_pdf->setY($ob_pdf->getY() + 3);
		
		$ob_pdf->SetFont('Arial', '', 9);
		
		$ob_pdf->MultiCell(190, 7, 'Para uso Exclusivo da Fundação', '0', 'C');
		
		$ob_pdf->MultiCell(190, 5, 'Assinatura / Carimbo Atendente____________________________________', '0', 'L');
		$ob_pdf->MultiCell(190, -5, 'Data:___/___/______', '0', 'R');
		
		$ob_pdf->setY($ob_pdf->getY() + 8);
		
		$ob_pdf->MultiCell(190, 5, 'Assinatura Gerente____________________________________', '0', 'L');
		$ob_pdf->MultiCell(190, -5, 'Data:___/___/______', '0', 'R');
		
		$ob_pdf->setY($ob_pdf->getY() + 8);
		
		$ob_pdf->MultiCell(190, 5, 'Assinatura Diretoria de Seguridade____________________________________', '0', 'L');
		$ob_pdf->MultiCell(190, -5, 'Em:___/___/______', '0', 'R');
		
		$ob_pdf->Output();
		
	}
	
	function salvar_contato()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$cep = $this->input->post('cep',TRUE);
		$arr_cep = explode("-", $cep);
		
		$telefone = $this->input->post('telefone',TRUE);
		$celular  = $this->input->post('celular',TRUE);
		$fax      = $this->input->post('fax',TRUE);
		
		$args['endereco']              = $this->input->post('endereco',TRUE);
		$args['bairro']                = $this->input->post('bairro',TRUE);
		$args['uf']                    = $this->input->post('uf',TRUE);
		$args['cidade']                = $this->input->post('cidade',TRUE);
		$args['ramal']                 = $this->input->post('ramal',TRUE);
		$args['email']                 = $this->input->post('email',TRUE);
		$args['cep']                   = (isset($arr_cep[0]) ? $arr_cep[0] : '');
		$args['complemento_cep']       = (isset($arr_cep[1]) ? $arr_cep[1] : '');
		$args['cd_empresa']            = $this->input->post('cd_empresa',TRUE);
		$args['cd_registro_empregado'] = $this->input->post('cd_registro_empregado',TRUE);
		$args['ddd']                   = '';
		$args['telefone']              = '';
		$args['ddd_cel']               = '';
		$args['celular']               = '';
		$args['ddd_fax']               = '';
		$args['fax']                   = '';
		
		if(trim($telefone) != '')
		{
			$arr_telefone = explode(")", $telefone);
			
			$args['ddd']      = trim(str_replace("(", "", $arr_telefone[0]));
			$args['telefone'] = trim($arr_telefone[1]);
		}
		
		if(trim($celular) != '')
		{
			$arr_celular = explode(")", $celular);
			
			$args['ddd_cel'] = trim(str_replace("(", "", $arr_celular[0]));
			$args['celular'] = trim($arr_celular[1]);
		}
		
		if(trim($fax) != '')
		{
			$arr_fax = explode(")", $fax);
			
			$args['ddd_fax'] = trim(str_replace("(", "", $arr_fax[0]));
			$args['fax']     = trim($arr_fax[1]);
		}
		
		$this->inscritos_model->salvar_contato( $result, $args );
		
		redirect("planos/senge_inscricao/contato/".$args['cd_registro_empregado'], "refresh");
	}
	
	function email_senha($cd_registro_empregado)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_registro_empregado'] = $cd_registro_empregado;
		$args['cd_empresa']            = 7;
		
		$this->inscritos_model->senha_inscrito( $result, $args );
		$row = $result->row_array();
		
		$args['cd_registro_empregado'] = '';
		$args['cd_empresa']            = '';
		$args['assunto'] = 'Senha SENGE Previdencia';
		$args['para']    = $row['email'];
		$args['de']      = 'FUNDAÇÃO CEEE - Senge Previdencia';
		$args['texto']   = 'Prezada(o) '.$row['nome'].'

Sua senha pessoal do plano Senge Previdência encontra-se logo abaixo nesta mensagem.

Confira seus dados pessoais:
-------------------------------------------------------------
REd (sua identificação junto à Fundação CEEE): '.$row['cd_registro_empregado'].'
Nome: '.$row['nome'].'
CPF: '.$row['cpf'].'
Identidade (RG): '.$row['rg'].'
Sua senha pessoal para acesso ao auto-atendimento: '.$row['codigo_345'].'
-------------------------------------------------------------
Por favor, caso você não tenha solicitado o envio deste email, entre em contato com nosso auto-atendimento: 0800-51-2596;
-------------------------------------------------------------

Esta mensagem foi enviada pelo Sistema SENGE Previdência.';
		
		$this->inscritos_model->envia_email( $result, $args );
		
		redirect("planos/senge_inscricao/contato/".$cd_registro_empregado, "refresh");
	}
	
	function email_confirmacao($cd_registro_empregado)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_registro_empregado'] = $cd_registro_empregado;
		$args['cd_empresa']            = 7;
		
		$this->inscritos_model->senha_inscrito( $result, $args );
		$row = $result->row_array();
		
		$args['assunto'] = 'Confirmação de Inscrição no plano SENGE Previdencia';
		$args['para']    = $row['email'];
		$args['de']      = 'FUNDAÇÃO CEEE - Senge Previdencia';
		$args['texto']   = 'Prezada(o) '.$row['nome'].'

Sua inscrição no Plano SENGE Previdência foi enviada para a Fundação CEEE.

Confira seus dados pessoais:
-------------------------------------------------------------
REd (sua identificação junto à Fundação CEEE): '.$row['cd_registro_empregado'].'
Nome: '.$row['nome'].'
CPF: '.$row['cpf'].'
Identidade (RG): '.$row['rg'].'
-------------------------------------------------------------
Para podermos confirmar seu endereço de email basta você clicar no link abaixo: 
http://www.sengeprevidencia.com.br/confirma_email.php?n=" '.$row['cd_registro_empregado'].'
-------------------------------------------------------------

Esta mensagem foi enviada pelo Sistema SENGE Previdência.';
		
		$this->inscritos_model->envia_email( $result, $args );
		
		redirect("planos/senge_inscricao/contato/".$cd_registro_empregado, "refresh");
	}
	
	function documento($cd_registro_empregado)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_registro_empregado'] = $cd_registro_empregado;
		$args['cd_empresa']            = 7;
		$args['seq_dependencia']       = 0;
		
		$this->inscritos_model->carrega($result, $args);
		$data['row'] = $result->row_array();
		
		$this->inscritos_model->documento( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->load->view('planos/senge_inscricao/documento', $data);
	}
	
	function  salvar_documento()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_empresa']            = $this->input->post('cd_empresa',TRUE);
		$args['cd_registro_empregado'] = $this->input->post('cd_registro_empregado',TRUE);
		$args['seq_dependencia']       = $this->input->post('seq_dependencia',TRUE);
		$args['cd_doc']                = $this->input->post('opt_tipo_doc',TRUE);
		$args['dt_entrega']            = $this->input->post('dt_entrega',TRUE);
		$args['cd_usuario']            = $this->session->userdata('codigo');
		
		$this->inscritos_model->salvar_documento( $result, $args );
		
		redirect("planos/senge_inscricao/documento/".$args['cd_registro_empregado'], "refresh");
	}

	function anexo($cd_registro_empregado)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_registro_empregado'] = $cd_registro_empregado;
		$args['cd_empresa']            = 7;
		$args['seq_dependencia']       = 0;
		
		$this->inscritos_model->carrega($result, $args);
		$data['row'] = $result->row_array();
		
		$this->inscritos_model->anexo( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->load->view('planos/senge_inscricao/anexo', $data);
	}
	
	function salvar_anexo()
	{
		$result = null;
		$data = Array();
		$args = Array();
	
		$qt_arquivo = intval($this->input->post("arquivo_m_count", TRUE));
		
		$args['cd_registro_empregado'] = $this->input->post("cd_registro_empregado", TRUE);
		$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
		$args["cd_sequencia"]          = $this->input->post("seq_dependencia", TRUE);
		$args["cd_usuario"]            = $this->session->userdata('codigo');
		
		if($qt_arquivo > 0)
		{
			$nr_conta = 0;
			
			while($nr_conta < $qt_arquivo)
			{				
				$args['arquivo_nome']  = $this->input->post("arquivo_m_".$nr_conta."_name", TRUE);
				$args['arquivo']       = $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE);
				
				$this->inscritos_model->salvar_anexo($result, $args);
				
				$nr_conta++;
			}
		}
		
		redirect("planos/senge_inscricao/anexo/".intval($args["cd_registro_empregado"]), "refresh");
	}
	
	function excluir_anexo($cd_registro_empregado, $cd_inscritos_anexo)
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_registro_empregado'] = $cd_registro_empregado;
		$args["cd_inscritos_anexo"]    = $cd_inscritos_anexo;
		$args["cd_usuario"]            = $this->session->userdata('codigo');
		
		$this->inscritos_model->excluir_anexo($result, $args);
		
		redirect("planos/senge_inscricao/anexo/".intval($args["cd_registro_empregado"]), "refresh");
	}
	
	function historico($cd_registro_empregado)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_registro_empregado'] = $cd_registro_empregado;
		$args['cd_empresa']            = 7;
		$args['seq_dependencia']       = 0;
		
		$this->inscritos_model->carrega($result, $args);
		$data['row'] = $result->row_array();
		
		$this->load->view('planos/senge_inscricao/historico', $data);
	}
	
	function confirmar()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_registro_empregado'] = $this->input->post("cd_registro_empregado", TRUE);
		$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
		$args["cd_sequencia"]          = $this->input->post("seq_dependencia", TRUE);
		
		$this->inscritos_model->confirmar( $result, $args );
		
		redirect("planos/senge_inscricao/historico/".$args['cd_registro_empregado'], "refresh");
	}
}
?>