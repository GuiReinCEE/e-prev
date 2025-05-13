<?php
class registro_operacional extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
        $this->load->model('projetos/acompanhamento_registro_operacional_model');
    }

    function index()
    {
        $this->load->view('atividade/registro_operacional/index');
    }

    function listar()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_usuario"] = $this->session->userdata('codigo');

		manter_filtros($args);

        $this->acompanhamento_registro_operacional_model->listar( $result, $args );
		$data['collection'] = $result->result_array();

        $this->load->view('atividade/registro_operacional/partial_result', $data);
    }
	
	function cadastro($cd_acompanhamento_registro_operacional = '')
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_acompanhamento_registro_operacional'] = $cd_acompanhamento_registro_operacional;
		$args["cd_usuario"]                             = $this->session->userdata('codigo');
		
		if(intval($args['cd_acompanhamento_registro_operacional']) == 0)
		{
			$data['row'] = array(
				'cd_acompanhamento_registro_operacional' => $cd_acompanhamento_registro_operacional,
				'cd_acomp'                               => '',
				'ds_nome'                                => '',
				'ds_processo_faz'                        => '',
				'ds_processo_executado'                  => '',
				'ds_calculo'                             => '',
				'ds_responsaveis'                        => '',
				'ds_requesito'                           => '',
				'ds_necessario'                          => '',
				'ds_integridade'                         => '',
				'ds_resultado'                           => '',
				'ds_local'                               => '',
				'dt_finalizado'                          => ''
			);
		}
		else
		{
			$this->acompanhamento_registro_operacional_model->carrega($result, $args);
			$data['row'] = $result->row_array();
		}
		
		$args['cd_acomp'] = $data['row']['cd_acomp'];
		
		$this->acompanhamento_registro_operacional_model->projetos( $result, $args );
		$data['arr_projeto'] = $result->result_array();
		
		$this->load->view('atividade/registro_operacional/cadastro', $data);
	}
	
	function salvar()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_acompanhamento_registro_operacional'] = $this->input->post("cd_acompanhamento_registro_operacional", TRUE); 
		$args['cd_acomp']                               = $this->input->post("cd_acomp", TRUE); 
		$args['ds_nome']                                = $this->input->post("ds_nome", TRUE); 
		$args['ds_processo_faz']                        = $this->input->post("ds_processo_faz", TRUE); 
		$args['ds_processo_executado']                  = $this->input->post("ds_processo_executado", TRUE); 
		$args['ds_calculo']                             = $this->input->post("ds_calculo", TRUE); 
		$args['ds_responsaveis']                        = $this->input->post("ds_responsaveis", TRUE); 
		$args['ds_requesito']                           = $this->input->post("ds_requesito", TRUE); 
		$args['ds_necessario']                          = $this->input->post("ds_necessario", TRUE); 
		$args['ds_integridade']                         = $this->input->post("ds_integridade", TRUE); 
		$args['ds_resultado']                           = $this->input->post("ds_resultado", TRUE); 
		$args['ds_local']                               = $this->input->post("ds_local", TRUE); 
		$args['cd_usuario']                             = $this->session->userdata('codigo');
		
		$cd_acompanhamento_registro_operacional = $this->acompanhamento_registro_operacional_model->salvar( $result, $args );
		
		redirect("atividade/registro_operacional/cadastro/".intval($cd_acompanhamento_registro_operacional), "refresh");
	}
	
	function excluir()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_acompanhamento_registro_operacional'] = $this->input->post("cd_acompanhamento_registro_operacional", TRUE); 
		$args['cd_usuario']                             = $this->session->userdata('codigo');
		
		$this->acompanhamento_registro_operacional_model->excluir( $result, $args );
	}
	
	function finalizar($cd_acompanhamento_registro_operacional)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_acompanhamento_registro_operacional'] = $cd_acompanhamento_registro_operacional; 
		$args['cd_usuario']                             = $this->session->userdata('codigo');
		
		$this->acompanhamento_registro_operacional_model->finalizar( $result, $args );
		
		redirect("atividade/registro_operacional/", "refresh");
	}
	
	function imprimir($cd_acompanhamento_registro_operacional)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_acompanhamento_registro_operacional'] = $cd_acompanhamento_registro_operacional; 
		
		$this->acompanhamento_registro_operacional_model->carrega($result, $args);
		$row = $result->row_array();
		
		$this->acompanhamento_registro_operacional_model->anexo( $result, $args );
		$arr_anexo = $result->result_array();

		$this->load->plugin('fpdf');

        $ob_pdf = new PDF('P', 'mm', 'A4');
        $ob_pdf->SetNrPag(true);
        $ob_pdf->SetMargins(10, 14, 5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = "Registro Operacional";
		
		$ob_pdf->AddPage();
		
		$ob_pdf->SetFont('Arial', 'B', 13);
		$ob_pdf->MultiCell(190, 7, "Projeto: ". $row['projeto'], '0', 'L');
		
		$ob_pdf->SetFont('Arial', '', 12);
		$ob_pdf->MultiCell(190, 7, "Autor: ". $row['nome'], '0', 'L');
		$ob_pdf->MultiCell(190, 7, "Nome Processo: ". $row['ds_nome'], '0', 'L');
		
		$ob_pdf->setY($ob_pdf->getY() + 5);
		
		$ob_pdf->SetFont('Arial', 'B', 12);
		$ob_pdf->MultiCell(190, 7, "1) O que o processo faz?", '0', 'L');
		$ob_pdf->SetFont('Arial', '', 12);
		$ob_pdf->MultiCell(190, 7, $row['ds_processo_faz'], '0', 'J');
		
		if(trim($row['ds_processo_faz_complemento']) != '')
		{
			$ob_pdf->setY($ob_pdf->getY() + 2.5);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "Complemento Analista", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_processo_faz_complemento'], '0', 'J');
		}
		
		$ob_pdf->setY($ob_pdf->getY() + 5);
		
		$ob_pdf->SetFont('Arial', 'B', 12);
		$ob_pdf->MultiCell(190, 7, "2) De que maneira й executado o processo?", '0', 'L');
		$ob_pdf->SetFont('Arial', '', 12);
		$ob_pdf->MultiCell(190, 7, $row['ds_processo_executado'], '0', 'J');
		
		if(trim($row['ds_processo_executado_complemento']) != '')
		{
			$ob_pdf->setY($ob_pdf->getY() + 2.5);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "Complemento Analista", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_processo_executado_complemento'], '0', 'J');
		}
		
		$ob_pdf->setY($ob_pdf->getY() + 5);
		
		$ob_pdf->SetFont('Arial', 'B', 12);
		$ob_pdf->MultiCell(190, 7, "3) Cбlculos?", '0', 'L');
		$ob_pdf->SetFont('Arial', '', 12);
		$ob_pdf->MultiCell(190, 7, $row['ds_calculo'], '0', 'J');
		
		if(trim($row['ds_calculo_complemento']) != '')
		{
			$ob_pdf->setY($ob_pdf->getY() + 2.5);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "Complemento Analista", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_calculo_complemento'], '0', 'J');
		}
		
		$ob_pdf->setY($ob_pdf->getY() + 5);
		
		$ob_pdf->SetFont('Arial', 'B', 12);
		$ob_pdf->MultiCell(190, 7, "4) Responsбveis?", '0', 'L');
		$ob_pdf->SetFont('Arial', '', 12);
		$ob_pdf->MultiCell(190, 7, $row['ds_responsaveis'], '0', 'J');
		
		$ob_pdf->setY($ob_pdf->getY() + 5);
		
		$ob_pdf->SetFont('Arial', 'B', 12);
		$ob_pdf->MultiCell(190, 7, "5) O que й necessбrio para que este processo possa ocontecer?", '0', 'L');
		$ob_pdf->SetFont('Arial', '', 12);
		$ob_pdf->MultiCell(190, 7, $row['ds_requesito'], '0', 'J');
		
		if(trim($row['ds_requesito_complemento']) != '')
		{
			$ob_pdf->setY($ob_pdf->getY() + 2.5);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "Complemento Analista", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_requesito_complemento'], '0', 'J');
		}
		
		$ob_pdf->setY($ob_pdf->getY() + 5);
		
		$ob_pdf->SetFont('Arial', 'B', 12);
		$ob_pdf->MultiCell(190, 7, "6) Este processo й necessбrio para qual(is) outro(s) processo(s)?", '0', 'L');
		$ob_pdf->SetFont('Arial', '', 12);
		$ob_pdf->MultiCell(190, 7, $row['ds_necessario'], '0', 'J');
		
		if(trim($row['ds_necessario_complemento']) != '')
		{
			$ob_pdf->setY($ob_pdf->getY() + 2.5);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "Complemento Analista", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_necessario_complemento'], '0', 'J');
		}
		
		$ob_pdf->setY($ob_pdf->getY() + 5);
		
		$ob_pdf->SetFont('Arial', 'B', 12);
		$ob_pdf->MultiCell(190, 7, "7) Integraзгo com outros sistemas?", '0', 'L');
		$ob_pdf->SetFont('Arial', '', 12);
		$ob_pdf->MultiCell(190, 7, $row['ds_integridade'], '0', 'J');
		
		if(trim($row['ds_integridade_complemento']) != '')
		{
			$ob_pdf->setY($ob_pdf->getY() + 2.5);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "Complemento Analista", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_integridade_complemento'], '0', 'J');
		}
		
		$ob_pdf->setY($ob_pdf->getY() + 5);
		
		$ob_pdf->SetFont('Arial', 'B', 12);
		$ob_pdf->MultiCell(190, 7, "8) Resultados?", '0', 'L');
		$ob_pdf->SetFont('Arial', '', 12);
		$ob_pdf->MultiCell(190, 7, $row['ds_resultado'], '0', 'J');
		
		if(trim($row['ds_resultado_complemento']) != '')
		{
			$ob_pdf->setY($ob_pdf->getY() + 2.5);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "Complemento Analista", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_resultado_complemento'], '0', 'J');
		}
		
		$ob_pdf->setY($ob_pdf->getY() + 5);
		
		$ob_pdf->SetFont('Arial', 'B', 12);
		$ob_pdf->MultiCell(190, 7, "9) Telas / Relatуrios / Planilhas?", '0', 'L');
		$ob_pdf->SetFont('Arial', '', 12);
		$ob_pdf->MultiCell(190, 7, $row['ds_local'], '0', 'J');
		
		$ob_pdf->setY($ob_pdf->getY() + 5);
		
		$ob_pdf->SetWidths(array(90, 45,55));
		$ob_pdf->SetAligns(array('C', 'C', 'C'));
		$ob_pdf->SetFont('Arial','',10);
		$ob_pdf->Row(array("Anexo ", "Dt. Inclusao", "Usuбrio"));
		$ob_pdf->SetAligns(array('L', 'C', 'L'));
		
		foreach ($arr_anexo as $item)
        {
			$ob_pdf->Row(array($item['arquivo_nome'], $item['dt_inclusao'], $item['nome']));
		}
		
		$ob_pdf->Output();
		
	}

}
?>