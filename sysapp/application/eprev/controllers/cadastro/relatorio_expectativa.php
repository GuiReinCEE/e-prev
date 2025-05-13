<?php
class relatorio_expectativa extends Controller
{
    function __construct()
    {
        parent::Controller();
        $this->load->model('projetos/avaliacao_capa_model');
        CheckLogin();
    }
    
    function index()
    {
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $result = null;
            $args = array();
            $data = array();
            
            $this->load->view('cadastro/relatorio_expectativa/index',$data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function listar()
    {
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $result = null;
            $data = Array();
            $args = Array();
            
            $args["ano"]         = $this->input->post("ano", TRUE);
            $args["cd_gerencia"] = $this->input->post("cd_gerencia", TRUE);
            $args["cd_usuario"]  = $this->input->post("cd_usuario", TRUE);
            
            manter_filtros($args);
            
            $this->avaliacao_capa_model->lista_relatorio_expectativas($result, $args);

            $data['collection'] = $result->result_array();
            
            $this->load->view('cadastro/relatorio_expectativa/partial_result',$data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function pdf()
    {
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $args = Array();
            $data = Array();
            $result = null;
            
            $args["ano"]         = $this->input->post("ano", TRUE);
            $args["cd_gerencia"] = $this->input->post("cd_gerencia", TRUE);
            $args["cd_usuario"]  = $this->input->post("cd_usuario", TRUE);
            
            $this->avaliacao_capa_model->lista_relatorio_expectativas($result, $args);
            
            $this->load->plugin('fpdf');
            
            $collection = $result->result_array();
            
            $ob_pdf = new PDF('L','mm','A4');

            $ob_pdf->SetNrPag(true);
            $ob_pdf->SetMargins(10,14,5);
            $ob_pdf->header_exibe = true;
            $ob_pdf->header_logo = true;
            $ob_pdf->header_titulo = true;
            $ob_pdf->header_titulo_texto = "Relatório Expectativas";
            
            $ob_pdf->AddPage();		
                        
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths( array(15, 20, 40, 60, 80, 60) );
            $ob_pdf->SetAligns( array('C','C','C','C','C','C') );
            $ob_pdf->SetFont( 'Courier', 'B', 10 );
            $ob_pdf->Row(array('Ano', 'Gerência', 'Colaborador', 'Competência', 'Resultado esperado', 'Ações de apoio'));
            $ob_pdf->SetAligns( array('C','C', 'L','L','L') );
            $ob_pdf->SetFont( 'Courier', '', 10 );
            
            foreach($collection as $item)
            {
                $ob_pdf->Row(array($item['dt_periodo'],  $item['divisao'], $item['nome'], $item['aspecto'], $item['resultado_esperado'], $item['acao'] ));	
            }

            $ob_pdf->Output();
            exit;
        }
    }
    
}