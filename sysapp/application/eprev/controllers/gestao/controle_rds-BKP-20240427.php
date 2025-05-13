<?php
class Controle_rds extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    ## SG / GERENTES / SUBGERENTE ##
    private function permissao()
    {	
    	if(gerencia_in(array('GC')))
    	{
    		return true;
    	}
    	else if($this->session->userdata('indic_05') == 'S')
    	{
    		return true;
    	}
    	else if($this->session->userdata('tipo') == 'G')
    	{
    		return true;
    	}
    	else if($this->session->userdata('indic_01') == 'S')
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
		$this->load->view('gestao/controle_rds/index');
    }
	
	public function listar()
    {
		$this->load->model('gestao/controle_rds_model');

		$args = array(
			'nr_rds'          => $this->input->post('nr_rds', TRUE),
			'nr_ano'          => $this->input->post('nr_ano', TRUE),
			'nr_ata'          => $this->input->post('nr_ata', TRUE),
			'dt_rds_ini'      => $this->input->post('dt_rds_ini', TRUE),
			'dt_rds_fim'      => $this->input->post('dt_rds_fim', TRUE),
			'dt_reuniao_ini'  => $this->input->post('dt_reuniao_ini', TRUE),
			'dt_reuniao_fim'  => $this->input->post('dt_reuniao_fim', TRUE),
			'ds_controle_rds' => $this->input->post('ds_controle_rds', TRUE)
		);

		$fl_gerente = 'N';

		if(($this->session->userdata('tipo') == 'G') OR ($this->session->userdata('indic_01') == 'S'))
		{
			$fl_gerente = 'S';
		}

		manter_filtros($args);

		$data['collection'] = $this->controle_rds_model->listar($this->session->userdata('divisao'), $args, $fl_gerente);

		foreach ($data['collection'] as $key => $item) 
		{
			$data['collection'][$key]['gerencia'] = array();

			foreach ($this->controle_rds_model->get_gerencia_rds($item['cd_controle_rds']) as $key2 => $gerencia) 
			{
				$data['collection'][$key]['gerencia'][] = $gerencia['cd_area'];
			}
		}

		$this->load->view('gestao/controle_rds/index_result', $data);		
    }
	
	public function cadastro($cd_controle_rds = 0)
    {
		if($this->permissao())
		{		
			$this->load->model('gestao/controle_rds_model');
			
			if(intval($cd_controle_rds) == 0)
			{
				$data['row'] = array(
					'cd_controle_rds' => intval($cd_controle_rds),
					'nr_rds'          => '',
					'nr_ano'          => '',
					'ds_controle_rds' => '',
					'nr_ata'          => '',
					'dt_reuniao'      => '',
					'arquivo'         => '',
					'arquivo_nome'    => '',
					'fl_restrito'     => 'N'
				);
			}
			else
			{
				$data['row'] = $this->controle_rds_model->carrega(intval($cd_controle_rds));
			}
			
			$this->load->view('gestao/controle_rds/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}		
	}
	
	public function salvar()
    {
		if($this->permissao())
		{			
			$this->load->model('gestao/controle_rds_model');

			$cd_controle_rds = $this->input->post('cd_controle_rds', TRUE); 

			$args = array(
				'ds_controle_rds' => $this->input->post('ds_controle_rds', TRUE),
				'arquivo'         => $this->input->post('arquivo', TRUE),
				'arquivo_nome'    => $this->input->post('arquivo_nome', TRUE),
				'nr_ata'          => $this->input->post('nr_ata', TRUE), 
				'dt_reuniao'      => $this->input->post('dt_reuniao', TRUE),
				'fl_restrito'     => $this->input->post('fl_restrito', TRUE),
				'nr_rds'          => $this->input->post('nr_rds', TRUE),
				'nr_ano'          => $this->input->post('nr_ano', TRUE),
				'cd_usuario'      => $this->session->userdata('codigo')
			);
		
			if(intval($cd_controle_rds) == 0)
			{
				$cd_controle_rds = $this->controle_rds_model->salvar($args);
			}
			else
			{
				$this->controle_rds_model->atualizar(intval($cd_controle_rds), $args);
			}
			
			redirect('gestao/controle_rds/cadastro/'.$cd_controle_rds, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}			
	}
	
	public function excluir($cd_controle_rds)
	{
		if($this->permissao())
		{			
			$this->load->model('gestao/controle_rds_model');
			
			$this->controle_rds_model->excluir(intval($cd_controle_rds), $this->session->userdata('codigo'));
			
			redirect('gestao/controle_rds', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}			
	}
	
	function rds_pdf($cd_controle_rds)
	{
		$this->load->model('gestao/controle_rds_model');
		$ar_rds = $this->controle_rds_model->carrega($cd_controle_rds, 'S'); #### MD5
		
		$this->load->plugin('fpdiprotection');
		
		if(trim($ar_rds["arquivo"]) != "")
		{
			$ob_pdf = new FPDI_Protection('P', 'mm', 'A4');
				   
			$nr_pag = $ob_pdf->setSourceFile('./up/controle_rds/'.$ar_rds['arquivo']);
			
			for($i = 1; $i <=  $nr_pag; $i++)
			{
				$idx = $ob_pdf->importPage($i);
				$ar_size = $ob_pdf->getTemplateSize($idx);
				
				$ob_pdf->addPage($ar_size['h'] > $ar_size['w'] ? 'P' : 'L', array($ar_size['w'], $ar_size['h']));
				$ob_pdf->useTemplate($idx);

				$x = ((35 * $ar_size['w']) / 100);
				$y = ((98 * $ar_size['h']) / 100);
				
				$ob_pdf->SetTextColor(255,0,0);
				$ob_pdf->SetFont('Courier','BI',8);
				$ob_pdf->Text(10, $y, date("d/m/Y H:i:s").' - '.strtoupper($this->session->userdata('usuario')).' - página '. $i.' de '.$nr_pag);			
			}

			$password = "";
			$ob_pdf->SetProtection(array(), $password, "");
			$ob_pdf->Output();		
		}		
	}	
}
?>