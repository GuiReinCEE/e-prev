<?php
class Autoatendimento_usuario_acesso extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }
	
	private function permissao_gerencia()
	{
		if(gerencia_in(array('GGS', 'GP')))
    	{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	private function permissao_cadastro()
	{
		if(($this->session->userdata('codigo') == 251) || ($this->session->userdata('codigo') == 8) || ($this->session->userdata('codigo') == 170))
    	{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	private function permissao_lista()
	{
		if(($this->session->userdata('codigo') == 40) || ($this->session->userdata('codigo') == 75) || ($this->session->userdata('codigo') == 39) || ($this->session->userdata('codigo') == 251) || ($this->session->userdata('codigo') == 8))
    	{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	private function get_situacao()
    {
    	return array(
    		array('value' => 'A', 'text' => 'Ativo'),
    		array('value' => 'E', 'text' => 'Excluído')
    	);
    }
	
	public function get_usuarios()
    {
		$this->load->model('autoatendimento/usuario_acesso_model');
		
		$cd_gerencia = $this->input->post('cd_gerencia', TRUE);
		
		$usuarios = $this->usuario_acesso_model->get_usuarios($cd_gerencia);
		
		echo json_encode($usuarios);
    }
	
    public function index()
    {
		if(($this->permissao_gerencia()) || ($this->permissao_lista()))
		{
			$data = array();

			$data['situacao'] = $this->get_situacao();
			
			$this->load->view('servico/autoatendimento_usuario_acesso/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }	
	
    public function listar()
    {
		if(($this->permissao_gerencia()) || ($this->permissao_lista()))
		{		
			$this->load->model('autoatendimento/usuario_acesso_model');

			$data = array();
			$args = array();
			
			$args['fl_situacao'] = trim($this->input->post('fl_situacao', TRUE));
			
			manter_filtros($args);
			
			$data['collection'] = $this->usuario_acesso_model->listar($args);
			
			$this->load->view('servico/autoatendimento_usuario_acesso/index_result', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}		
	}
	
    public function cadastro()
    {
		if($this->permissao_cadastro())
		{
			$this->load->view('servico/autoatendimento_usuario_acesso/cadastro');		
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }	
	
    public function salvar()
    {
		if($this->permissao_cadastro())
		{		
			$this->load->model('autoatendimento/usuario_acesso_model');
			
			$args = array();
			
			$args['cd_usuario']			 = trim($this->input->post('cd_usuario', TRUE));
			$args['cd_usuario_inclusao'] = $this->session->userdata('codigo');

			$this->usuario_acesso_model->salvar($args);
			
			redirect('servico/autoatendimento_usuario_acesso', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}		
	}	
	
    public function excluir($cd_usuario)
    {
		if(($this->permissao_gerencia()) || ($this->permissao_lista()))
		{
			$this->load->model('autoatendimento/usuario_acesso_model');
					
			$this->usuario_acesso_model->excluir($cd_usuario, $this->session->userdata('codigo'));
			
			redirect('servico/autoatendimento_usuario_acesso', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}		
	}
	
	public function reativar($cd_usuario)
    {
		if(($this->permissao_gerencia()) || ($this->permissao_lista()))
		{
			$this->load->model('autoatendimento/usuario_acesso_model');
					
			$this->usuario_acesso_model->reativar($cd_usuario, $this->session->userdata('codigo'));
			
			redirect('servico/autoatendimento_usuario_acesso', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}		
	}
	
	public function acesso($cd_usuario)
    {
		if(($this->permissao_gerencia()) || ($this->permissao_lista()))
		{
			$this->load->model('autoatendimento/usuario_acesso_model');
			
			$data['ds_usuario'] = $this->usuario_acesso_model->acesso($cd_usuario);
			
			$this->load->view('servico/autoatendimento_usuario_acesso/acesso',$data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }	
	
    public function acesso_listar()
    {
		if(($this->permissao_gerencia()) || ($this->permissao_lista()))
		{		
			$this->load->model('autoatendimento/usuario_acesso_model');

			$data = array();
			
			$args = array(
				'dt_acesso_ini' 		=> trim($this->input->post('dt_acesso_ini', TRUE)),
				'dt_acesso_fim' 		=> trim($this->input->post('dt_acesso_fim', TRUE)),
				'dt_login_ini'  		=> trim($this->input->post('dt_login_ini', TRUE)),
				'dt_login_fim'  		=> trim($this->input->post('dt_login_fim', TRUE)),
				'cd_empresa'            => trim($this->input->post('cd_empresa', TRUE)),
				'cd_registro_empregado' => trim($this->input->post('cd_registro_empregado', TRUE)),
				'seq_dependencia'       => trim($this->input->post('seq_dependencia', TRUE)),
				'cd_usuario'   	        => trim($this->input->post('cd_usuario', TRUE))
			);
						
			manter_filtros($args);
			
			$data['collection'] = $this->usuario_acesso_model->acesso_listar($args);
			
			$this->load->view('servico/autoatendimento_usuario_acesso/acesso_result', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}		
	}	
	
    public function pdf($cd_usuario)
    {
		if(($this->permissao_gerencia()) || ($this->permissao_lista()))
		{	
			$this->load->model('autoatendimento/usuario_acesso_model');
			
			$ar_usu = $this->usuario_acesso_model->pdf($cd_usuario);
			
			$this->load->plugin('fpdf');
			$ar_mes = array('Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
			$ob_pdf = new PDF('P','mm','A4'); 
			$ob_pdf->AddPage();
			$ob_pdf->Image('img/logofundacao_carta.jpg', 20, 10, $ob_pdf->ConvertSize(150), $ob_pdf->ConvertSize(33));	

			$ob_pdf->SetXY(20,30);
			$ob_pdf->SetFont('Courier','B',20);			
			$ob_pdf->MultiCell(170, 6, 'TERMO DE COMPROMISSO',0,'C');			
			
			$ob_pdf->SetXY(20,50);
			$ob_pdf->SetFont('Courier','',12);			
			$ob_pdf->MultiCell(170, 6, '      Eu, '.$ar_usu['nome'].', declaro que recebi, nesta data, a senha Master para acesso ao sistema de autoatendimento da Fundação CEEE, com a finalidade de acompanhar e orientar ao participante/assistido durante o atendimento, analisar e propor melhorias no referido sistema.',0,'J');	
			
			$ob_pdf->SetXY(20, $ob_pdf->GetY() + 6);	
			$ob_pdf->MultiCell(170, 6, '     Declaro, também, estar plenamente ciente de que com essa senha é possível conceder empréstimo pessoal e realizar todas as transações e consultas disponíveis ao participante/assistido no autoatendimento e, que, caso venha a utilizá-la de forma a causar prejuízo à Fundação CEEE, estarei sujeito a medidas de ordem disciplinar, previstas internamente com base no artigo 482 e seus incisos da C.L.T. (Consolidação das Leis do Trabalho), bem como de ordem legal.',0,'J');			

			$ob_pdf->SetXY(20, $ob_pdf->GetY() + 6);	
			$ob_pdf->MultiCell(170, 6, 'Porto Alegre, '.$ar_usu['dia'].' de '.strtolower($ar_mes[($ar_usu['mes'] - 1)]).' de '.$ar_usu['ano'].'.',0,'J');			

			$ob_pdf->SetXY(20, $ob_pdf->GetY() + 25);	
			$ob_pdf->MultiCell(170, 6, '___________________________________',0,'J');	
			$ob_pdf->SetX(20);			
			$ob_pdf->MultiCell(170, 6, 'Assinatura empregado',0,'J');			
			
		
			$ob_pdf->SetXY(20, $ob_pdf->GetY() + 10);	
			$ob_pdf->MultiCell(170, 6, 'Nome do empregado: '.$ar_usu['nome'],0,'J');	
			$ob_pdf->SetX(20);			
			$ob_pdf->MultiCell(170, 6, 'Re.d.: '.$ar_usu['cd_empresa'].'/'.$ar_usu['cd_registro_empregado'].'/0',0,'J');		
			
			$ob_pdf->Output();
			exit;			
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }	
}
