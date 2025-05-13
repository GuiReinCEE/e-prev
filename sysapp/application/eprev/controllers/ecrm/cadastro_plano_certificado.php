<?php
class cadastro_plano_certificado extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('public/planos_certificados_model');
    }
	
	function index()
    {
		if (gerencia_in(Array('GI')))
        {
			$args = Array();
			$data = Array();
			$result = null;
		
			$this->planos_certificados_model->planos( $result, $args );
			$data['arr_plano'] = $result->result_array();
		
			$this->load->view('ecrm/cadastro_plano_certificado/index', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function listar()
    {	
		if (gerencia_in(Array('GI')))
        {
			$args = Array();
			$data = Array();
			$result = null;

			$args['cd_plano'] = $this->input->post("cd_plano", TRUE);
			
			manter_filtros($args);

			$this->planos_certificados_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->load->view('ecrm/cadastro_plano_certificado/index_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function cadastro($cd_plano, $versao_certificado)
	{
		if (gerencia_in(Array('GI')))
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_plano']           = intval($cd_plano);
			$args['versao_certificado'] = intval($versao_certificado);
			
			$this->planos_certificados_model->carrega($result, $args);
			$data['row'] = $result->row_array();
			
			$this->load->view('ecrm/cadastro_plano_certificado/cadastro', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function salvar()
	{
		if (gerencia_in(Array('GI')))
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_plano']              = $this->input->post("cd_plano", TRUE);
			$args['versao_certificado']    = $this->input->post("versao_certificado", TRUE);
			$args['nome_certificado']      = $this->input->post("nome_certificado", TRUE);
			$args['cd_spc']                = $this->input->post("cd_spc", TRUE);
			$args['dt_aprovacao_spc']      = $this->input->post("dt_aprovacao_spc", TRUE);
			$args['dt_inicio']             = $this->input->post("dt_inicio", TRUE);
			$args['dt_final']              = $this->input->post("dt_final", TRUE);
			$args['pos_imagem']            = $this->input->post("pos_imagem", TRUE);
			$args['largura_imagem']        = $this->input->post("largura_imagem", TRUE);
			$args['coluna_1']              = $this->input->post("coluna_1", TRUE);
			$args['coluna_2']              = $this->input->post("coluna_2", TRUE);
			$args['nr_largura_logo']       = $this->input->post("nr_largura_logo", TRUE);
			$args['nr_altura_logo']        = $this->input->post("nr_altura_logo", TRUE);
			$args['nr_x_logo']             = $this->input->post("nr_x_logo", TRUE);
			$args['nr_fonte_verso']        = $this->input->post("nr_fonte_verso", TRUE);
			$args['nr_altura_linha_verso'] = $this->input->post("nr_altura_linha_verso", TRUE);
			$args['presidente_nome']       = $this->input->post("presidente_nome", TRUE);
			$args['presidente_assinatura'] = $this->input->post("presidente_assinatura", TRUE);
			$args['acao']                  = $this->input->post("acao", TRUE);

			$this->planos_certificados_model->salvar($result, $args);
			
			redirect("ecrm/cadastro_plano_certificado/cadastro/".$args['cd_plano'].'/'.$args['versao_certificado'], "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function imprimir($cd_plano, $versao_certificado)
	{
		if (gerencia_in(Array('GI')))
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_plano']           = $cd_plano;
			$args['versao_certificado'] = $versao_certificado;
			
			$this->planos_certificados_model->carrega_certificado($result, $args);
			$row = $result->row_array();
			
			$this->load->plugin('fpdf');
			
			$ob_pdf = new PDF('P', 'mm', 'A4');
			
			$ob_pdf->AddPage();
			$ob_pdf->SetXY(10, 8);
			$ob_pdf->SetFont('Arial', '', 10);
			$ob_pdf->MultiCell(190, 4, $row['nome_plano_certificado'], 0, "C");

			$ob_pdf->SetFont('Helvetica', '', $row['nr_fonte_verso']);
			$ob_pdf->SetXY(8, 16);
			$ob_pdf->MultiCell(98, $row['nr_altura_linha_verso'], $row['coluna_1'], 0, "J");
			$ob_pdf->SetXY(108, 16);
			$ob_pdf->MultiCell(95, $row['nr_altura_linha_verso'], $row['coluna_2'], 0, "J");

			$ob_pdf->SetXY(20, 267);
			$ob_pdf->Image('img/certificado_logo_plano_' . $row['cd_plano'] . '.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize($row['nr_largura_logo']) / 2, $ob_pdf->ConvertSize($row['nr_altura_logo']) / 2);

			$ob_pdf->SetXY(95, 270);
			$ob_pdf->Image('img/certificado_disqueeletro.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(88), $ob_pdf->ConvertSize(50));

			$ob_pdf->SetXY(150, 272);
			$ob_pdf->Image('img/certificado_ logo_fundacao_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(150), $ob_pdf->ConvertSize(33));
			
			$ob_pdf->Output();
			exit;
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
}

?>