<?php
class Seguro_obito extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    public function index($cd_empresa = '', $cd_registro_empregado = '', $seq_dependencia = '', $fl_confirmado = '', $periodo = 'last7days' )
    {
		if(gerencia_in(array('GP')))
    	{
			$data = array();
			
			$data = array(
				'cd_empresa' 			=> $cd_empresa, 
				'cd_registro_empregado' => $cd_registro_empregado, 
				'seq_dependencia' 		=> $seq_dependencia, 
				'fl_confirmado' 		=> $fl_confirmado, 
				'periodo' 				=> $periodo
			);
    	
			$this->load->view('ecrm/seguro_obito/index', $data);
    	}
    	else
        {
            exibir_mensagem('ACESSO NรO PERMITIDO');
        }
    }

    public function listar()
    {
		$this->load->model('projetos/atendimento_obito_seguro_model');

		$args = array();
		$data = array();
		
		$args = array(
			'cd_empresa' 			=> $this->input->post('cd_empresa'),
			'cd_registro_empregado' => $this->input->post('cd_registro_empregado'),
			'seq_dependencia' 		=> $this->input->post('seq_dependencia'),
			'dt_ini' 				=> $this->input->post('dt_ini'),
			'dt_fim' 				=> $this->input->post('dt_fim'),
			'fl_confirmado'			=> $this->input->post('fl_confirmado'),
			'ds_motivo_pendencia'	=> $this->input->post('ds_motivo_pendencia')
		);
		
		manter_filtros($args);
		
		$data['collection'] = $this->atendimento_obito_seguro_model->listar($args);
		
		$this->load->view('ecrm/seguro_obito/index_result', $data);
    }

    public function confirma($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
    	if(gerencia_in(array('GP')))
    	{	
			$this->load->model('projetos/atendimento_obito_seguro_model');
			
			$cd_usuario = $this->session->userdata('codigo');			
			
			$this->atendimento_obito_seguro_model->confirma(intval($cd_empresa), intval($cd_registro_empregado), intval($seq_dependencia), intval($cd_usuario));

            redirect('ecrm/seguro_obito', 'refresh');
    	}
    	else
        {
            exibir_mensagem('ACESSO NรO PERMITIDO');
        }
    }

    public function formulario($cd_empresa = -1, $cd_registro_empregado = -1, $seq_dependencia = -1)
    {
    	$this->load->model('projetos/atendimento_obito_seguro_model');

		$this->load->plugin('fpdf');
		
		$row = $this->atendimento_obito_seguro_model->get_participante_obito($cd_empresa, $cd_registro_empregado, $seq_dependencia);
		
		if(count($row) > 0)
		{
			if(trim($row['dt_obito']) == '')
			{
				exibir_mensagem('DATA DE ำBITO NรO INFORMADA');
			}
			elseif(intval($row['tipo_falecimento']) == 0)
			{
				exibir_mensagem('TIPO DE FALECIMENTO NรO INFORMADO');
			}			
			elseif((intval($row['tipo_falecimento']) != 1) and (intval($row['tipo_falecimento']) != 2) and (intval($row['tipo_falecimento']) != 3))
			{
				exibir_mensagem('TIPO DE FALECIMENTO NรO ษ NATURAL OU ACIDENTAL');
			}			
			else
			{
				$ob_pdf = new PDF('P','mm','A4'); 
				$ob_pdf->SetMargins(10,14,5);
				$ob_pdf->AddPage();
				
				$ob_pdf->Image('./img/693_AVISO_DE_SINISTRO.png', 0, 0, $ob_pdf->ConvertSize(775), $ob_pdf->ConvertSize(1096),'','',false);
				
				$ob_pdf->SetTextColor(255, 0, 0);
				$ob_pdf->SetFont('Courier','B',8);	
				
				#### DATA OBITO ####
				$ob_pdf->Text(11.9,185.3,$row['dt_obito']);
				
				#### TIPO OBITO ####
				if(intval($row['tipo_falecimento']) == 1) #NATURAL
				{
					$ob_pdf->Text(11.9,71.8,'X');
				}
				else if(intval($row['tipo_falecimento']) == 3) #NATURAL
				{
					$ob_pdf->Text(11.9,71.8,'X');
				}
				elseif(intval($row['tipo_falecimento']) == 2) #ACIDENTAL
				{
					$ob_pdf->Text(38.3,71.8,'X');
				}			
				
				#### REQUERENTE ####
				$ob_pdf->Text(11.5,90.7,$row['nome']);
				$ob_pdf->Text(120,90.7,$row['dt_nascimento']);
				$ob_pdf->Text(154,90.7,$row['cpf']);
				
				$ob_pdf->Text(11.5,97,$row['endereco']);
				$ob_pdf->Text(119,97,$row['nr_endereco']);
				$ob_pdf->Text(146.5,97,$row['complemento_endereco']);
				
				$ob_pdf->Text(11.5,103,$row['bairro']);
				$ob_pdf->Text(53,103,$row['cidade']);
				$ob_pdf->Text(107,103,$row['uf']);
				$ob_pdf->Text(120,103,$row['cep']);
				$ob_pdf->Text(150,103,(intval($row['ddd']) > 0 ? $row['ddd'] : $row['ddd_celular']));
				$ob_pdf->Text(162,103,(intval($row['ddd']) > 0 ? $row['telefone'] : $row['celular']));			
				
				#### SEGURADO ####
				$ob_pdf->Text(11.5,144,'FUNDAวรO CEEE DE SEGURIDADE SOCIAL - ELETROCEEE');
				$ob_pdf->Text(147,144,'CNPJ: 90.884.412/0001-24');
				
				$ob_pdf->Text(11.5,150,'RUA DOS ANDRADAS');
				$ob_pdf->Text(122.5,150,'702');
				$ob_pdf->Text(150,150,'SALA 803');
				
				$ob_pdf->Text(11.5,156,'CENTRO');
				$ob_pdf->Text(80,156,'PORTO ALEGRE');
				$ob_pdf->Text(147,156,'RS');
				$ob_pdf->Text(160,156,'90020-004');
				
				$ob_pdf->Text(105,162,'51');
				$ob_pdf->Text(115,162,'3027-3161');
				
				$ob_pdf->Text(11.5,168.3,'gfcemprestimo@familiaprevidencia.com.br');
						
				
				$ob_pdf->Output();
				exit;
			}
		}
		else
		{
			exibir_mensagem('PARTICIPANTE NรO ENCONTRADO');
		}
    }
	
	public function altera_motivo()
	{
		$this->load->model('projetos/atendimento_obito_seguro_model');
		
		$cd_registro_empregado = utf8_decode($this->input->post('cd_registro_empregado', TRUE));
		$ds_motivo_pendencia   = utf8_decode($this->input->post('ds_motivo_pendencia', TRUE));
		$cd_usuario            = utf8_decode($this->session->userdata('codigo'));
		
		$this->atendimento_obito_seguro_model->alterar_motivo($cd_registro_empregado, $ds_motivo_pendencia, $cd_usuario);
	}
}
?>