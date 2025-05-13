<?php
class Treinamento_diretoria_conselhos extends Controller
{
    function __construct()
    {
        parent::Controller();
		
        CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GC')))
        {
            if($this->session->userdata('indic_09') == "*")
            {
                return TRUE;
            }
            //Julia Souza dos Santos - OS : 48840
            else if($this->session->userdata('codigo') == 362)
            {
                return TRUE;
            }
            //Jucieli Silva Larrossa - OS : 51887
	        else if($this->session->userdata('codigo') == 374)
	        {
	            return TRUE;
	        }
	        //Carla Gomes da Silva - OS : 51887
	        else if($this->session->userdata('codigo') == 352)
	        {
	            return TRUE;
	        }
	        //Adriana Espíndola - OS : 56304
	        else if($this->session->userdata('codigo') == 3)
	        {
	            return TRUE;
	        }
            //Vanessa Silva Alves - OS : 64395
            else if($this->session->userdata('codigo') == 424)
            {
                return TRUE;
            }
            //Roberta Bittencourt da Costa - OS : 81379
            else if($this->session->userdata('codigo') == 474)
            {
                return TRUE;
            }
            //Pamela Vieira da Silva     - OS : 85477
            else if($this->session->userdata('codigo') == 494)
            {
                return TRUE;
            }
            else
            {
                return FALSE;
            }
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
			$this->load->model('projetos/treinamento_diretoria_conselhos_model');
		
            $data['tipo'] = $this->treinamento_diretoria_conselhos_model->get_tipo();

            $this->load->view('cadastro/treinamento_diretoria_conselhos/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    public function listar()
    {
        $this->load->model('projetos/treinamento_diretoria_conselhos_model');
            
        $data = array();
        $args = array();
        
        $args['nr_numero']                       = $this->input->post('nr_numero', TRUE);
        $args['nr_ano']                          = $this->input->post('nr_ano', TRUE);
        $args['ds_nome']                         = $this->input->post('ds_nome', TRUE);
        $args['dt_inicio_ini']                   = $this->input->post('dt_inicio_ini', TRUE);
        $args['dt_inicio_fim']                   = $this->input->post('dt_inicio_fim', TRUE);
        $args['dt_final_ini']                    = $this->input->post('dt_final_ini', TRUE);
        $args['dt_final_fim']                    = $this->input->post('dt_final_fim', TRUE);
        $args['cd_treinamento_colaborador_tipo'] = $this->input->post('cd_treinamento_colaborador_tipo', TRUE);
        $args['cd_empresa']                      = $this->input->post('cd_empresa', TRUE);
        $args['cd_registro_empregado']           = $this->input->post('cd_registro_empregado', TRUE);
        $args['seq_dependencia']                 = $this->input->post('seq_dependencia', TRUE);
        $args['ds_nome_colaborador']             = $this->input->post('ds_nome_colaborador', TRUE);
        
        manter_filtros($args);
        
        $data['collection'] = $this->treinamento_diretoria_conselhos_model->listar($args);

        $this->load->view('cadastro/treinamento_diretoria_conselhos/index_result',$data);
    }
    
    public function cadastro($cd_treinamento_diretoria_conselhos = 0)
    {
        if($this->get_permissao())
		{
			$this->load->model('projetos/treinamento_diretoria_conselhos_model');
			
            $data = array();

            $data['uf'] = $this->treinamento_diretoria_conselhos_model->get_uf();

            if(intval($cd_treinamento_diretoria_conselhos) == 0)
            {
                $data['row'] = array(
                    'cd_treinamento_diretoria_conselhos' => $cd_treinamento_diretoria_conselhos,
                    'ds_nome'						     => '',
                    'ds_promotor'					     => '',
                    'ds_endereco'					     => '',
                    'ds_cidade'						     => '',
                    'ds_uf'							     => '',
                    'dt_inicio'						     => '',
                    'hr_inicio'						     => '',
                    'dt_final' 						     => '',
                    'hr_final'						     => '',
                    'nr_carga_horaria'				     => '',
                    'vl_unitario' 					     => '',
                    'dt_pagamento' 					     => '',
                    'dt_exclusao' 					     => '',
                    'cd_treinamento_colaborador_tipo'    => ''
                );
            }
            else
            {
				$data['row'] = $this->treinamento_diretoria_conselhos_model->carrega($cd_treinamento_diretoria_conselhos);

                $data['collection'] = $this->treinamento_diretoria_conselhos_model->colaboradores($cd_treinamento_diretoria_conselhos);
            }
            $this->load->view('cadastro/treinamento_diretoria_conselhos/cadastro', $data);
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
			$this->load->model('projetos/treinamento_diretoria_conselhos_model');
			
            $args = array();

			$cd_treinamento_diretoria_conselhos = $this->input->post('cd_treinamento_diretoria_conselhos', TRUE);
			
            $args['ds_nome']          					= $this->input->post('ds_nome', TRUE);
            $args['ds_promotor']      					= $this->input->post('ds_promotor', TRUE);
            $args['ds_endereco']      					= $this->input->post('ds_endereco', TRUE);
            $args['ds_cidade']        					= $this->input->post('ds_cidade', TRUE);
            $args['ds_uf']            					= $this->input->post('ds_uf', TRUE);
            $args['dt_inicio']        					= $this->input->post('dt_inicio', TRUE);
            $args['hr_inicio']        					= $this->input->post('hr_inicio', TRUE);
            $args['dt_final']         					= $this->input->post('dt_final', TRUE);
            $args['hr_final']         					= $this->input->post('hr_final', TRUE); 
            $args['nr_carga_horaria']					= $this->input->post('nr_carga_horaria', TRUE); 
            $args['vl_unitario']      					= $this->input->post('vl_unitario', TRUE); 

            $args['vl_unitario'] = str_replace('.', '', $args['vl_unitario']);
            $args['vl_unitario'] = str_replace(',', '.', $args['vl_unitario']);
            
            $args['nr_carga_horaria'] = str_replace('.', '', $args['nr_carga_horaria']);
            $args['nr_carga_horaria'] = str_replace(',', '.', $args['nr_carga_horaria']);

            $args['cd_treinamento_colaborador_tipo'] = $this->input->post('cd_treinamento_colaborador_tipo', TRUE); 
            $args['cd_usuario']                      = $this->session->userdata('codigo');
			
			if(intval($cd_treinamento_diretoria_conselhos) == 0)
			{
				$cd_treinamento_diretoria_conselhos = $this->treinamento_diretoria_conselhos_model->salvar($args);
			}
			else
			{
			    $this->treinamento_diretoria_conselhos_model->atualizar($cd_treinamento_diretoria_conselhos, $args);
			}
			
            redirect('cadastro/treinamento_diretoria_conselhos/cadastro/'.$cd_treinamento_diretoria_conselhos);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    public function excluir($cd_treinamento_diretoria_conselhos)
    {
        if($this->get_permissao())
		{
			$this->load->model('projetos/treinamento_diretoria_conselhos_model');
			
			$this->treinamento_diretoria_conselhos_model->excluir($cd_treinamento_diretoria_conselhos, $this->session->userdata('codigo'));
			
			redirect('cadastro/treinamento_diretoria_conselhos');  
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }     
	
	public function colaborador($cd_treinamento_diretoria_conselhos, $cd_treinamento_diretoria_conselhos_item = 0)
    {
        if($this->get_permissao())
		{
			$this->load->model('projetos/treinamento_diretoria_conselhos_model');

			if(intval($cd_treinamento_diretoria_conselhos_item) == 0)
			{
				$data['row'] = array(
					'cd_treinamento_diretoria_conselhos_item' => 0,
					'cd_empresa' 							  => '',
					'cd_registro_empregado' 				  => '',
					'seq_dependencia' 						  => '',
					'ds_nome' 								  => '',
					'cd_gerencia' 							  => '',
					'ds_centro_custo' 						  => '',
					'arquivo' 								  => '',
					'arquivo_nome' 							  => ''
				);
			}
			else
			{
				$data['row'] = $this->treinamento_diretoria_conselhos_model->carrega_colaborador($cd_treinamento_diretoria_conselhos_item); 
			}
			
            $data['gerencias'] = $this->treinamento_diretoria_conselhos_model->get_gerencias(); 
			
			$data['cd_treinamento_diretoria_conselhos'] = $cd_treinamento_diretoria_conselhos;
			
            $this->load->view('cadastro/treinamento_diretoria_conselhos/colaborador', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    public function salvar_colaborador()
    {
        if($this->get_permissao())
		{
			$this->load->model('projetos/treinamento_diretoria_conselhos_model');
			
            $args = array();

            $cd_treinamento_diretoria_conselhos_item 	= $this->input->post('cd_treinamento_diretoria_conselhos_item', TRUE);

            $args['cd_treinamento_diretoria_conselhos'] = $this->input->post('cd_treinamento_diretoria_conselhos', TRUE);
            $args['cd_empresa']       			        = $this->input->post('cd_empresa', TRUE);
            $args['cd_registro_empregado'] 				= $this->input->post('cd_registro_empregado', TRUE);
            $args['seq_dependencia']       				= $this->input->post('seq_dependencia', TRUE);
            $args['ds_nome']                  			= $this->input->post('ds_nome', TRUE);
            $args['ds_centro_custo']          			= $this->input->post('ds_centro_custo', TRUE);
            $args['cd_gerencia']                  		= $this->input->post('cd_gerencia', TRUE);
            $args['arquivo']                  			= $this->input->post('arquivo', TRUE);
            $args['arquivo_nome']                  		= $this->input->post('arquivo_nome', TRUE);
            $args['usuario']               				= $this->session->userdata('codigo');

            if(intval($cd_treinamento_diretoria_conselhos_item) == 0)
            {
            	$this->treinamento_diretoria_conselhos_model->salvar_colaborador($args);
			
	            $this->treinamento_diretoria_conselhos_model->agenda_atualizar(
	                $args['cd_treinamento_diretoria_conselhos'], 
	                $this->session->userdata('codigo')
	            );
            }
            else
            {
				$this->treinamento_diretoria_conselhos_model->atualizar_colaborador($cd_treinamento_diretoria_conselhos_item, $args);
            }

            redirect('cadastro/treinamento_diretoria_conselhos/cadastro/'.$args['cd_treinamento_diretoria_conselhos']);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    public function excluir_colaborador($cd_treinamento_diretoria_conselhos_item, $cd_treinamento_diretoria_conselhos)
    {
        if($this->get_permissao())
		{
			$this->load->model('projetos/treinamento_diretoria_conselhos_model');
			
            $this->treinamento_diretoria_conselhos_model->excluir_colaborador($cd_treinamento_diretoria_conselhos_item, $this->session->userdata('codigo'));

            $this->treinamento_diretoria_conselhos_model->agenda_atualizar(
                $cd_treinamento_diretoria_conselhos_item, 
                $this->session->userdata('codigo')
            );
			
            redirect('cadastro/treinamento_diretoria_conselhos/cadastro/'.$cd_treinamento_diretoria_conselhos);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    } 

    public function pdf()
    {
        if($this->get_permissao())
		{
			$this->load->model('projetos/treinamento_diretoria_conselhos_model');
			
            $args = array();
            
			$args['nr_numero']                       = $this->input->post('nr_numero', TRUE);
			$args['nr_ano']                          = $this->input->post('nr_ano', TRUE);
			$args['ds_nome']                         = $this->input->post('ds_nome', TRUE);
			$args['dt_inicio_ini']                   = $this->input->post('dt_inicio_ini', TRUE);
			$args['dt_inicio_fim']                   = $this->input->post('dt_inicio_fim', TRUE);
			$args['dt_final_ini']                    = $this->input->post('dt_final_ini', TRUE);
			$args['dt_final_fim']                    = $this->input->post('dt_final_fim', TRUE);
			$args['cd_treinamento_colaborador_tipo'] = $this->input->post('cd_treinamento_colaborador_tipo', TRUE);
			$args['cd_empresa']                      = $this->input->post('cd_empresa', TRUE);
			$args['cd_registro_empregado']           = $this->input->post('cd_registro_empregado', TRUE);
			$args['seq_dependencia']                 = $this->input->post('seq_dependencia', TRUE);
			$args['ds_nome_colaborador']             = $this->input->post('ds_nome_colaborador', TRUE);
            
            manter_filtros($args);
            $collection = $this->treinamento_diretoria_conselhos_model->listar($args);
            
            $this->load->plugin('fpdf');
 
            $ob_pdf = new PDF('L','mm','A4');

            $ob_pdf->SetNrPag(true);
            $ob_pdf->SetMargins(10,14,5);
            $ob_pdf->header_exibe = true;
            $ob_pdf->header_logo = true;
            $ob_pdf->header_titulo = true;
            $ob_pdf->header_titulo_texto = 'Treinamento - Diretoria e Conselhos';
            
            $ob_pdf->AddPage();			
            
            if(trim($args['ds_nome_colaborador']) != '')
            {
                $ob_pdf->SetFont('Courier', '', 10);
                $ob_pdf->MultiCell(190, 6, 'Colaborador: '.$args['ds_nome_colaborador'].(intval($args['cd_registro_empregado']) > 0 ? ' - '.$args['cd_empresa'].'/'.$args['cd_registro_empregado'].'/'.$args['seq_dependencia'] : ''));
            }
            
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            //$ob_pdf->SetWidths( array(25, 75, 15, 50, 30, 80) );
            $ob_pdf->SetWidths(array(20, 45, 45, 30, 10, 25, 25, 30, 25, 20));
            $ob_pdf->SetAligns(array('C','C','C','C','C','C', 'C', 'C', 'C', 'C'));
            $ob_pdf->SetFont('Courier', 'B', 10);
            $ob_pdf->Row(array('Número', 'Nome', 'Promotor', 'Cidade', 'UF', 'Dt Início', 'Dt Final', 'Tipo', 'Carga Horária(H)', 'Qt. Colaboradores'));
            $ob_pdf->SetAligns(array('C','L', 'L','L','C','C','C', 'L', 'R', 'C'));
            $ob_pdf->SetFont('Courier', '', 10);
            
            foreach($collection as $item)
            {
                $ob_pdf->Row(array(
                    $item['nr_numero'], 
					$item['ds_nome'], 
                    $item['ds_promotor'], 
                    $item['ds_cidade'], 
                    $item['ds_uf'],
                    $item['dt_inicio'], 
                    $item['dt_final'], 
                    $item['ds_treinamento_colaborador_tipo'], 
                    str_replace('.', ',', $item['nr_carga_horaria']),
                    $item['tl_colaborador']
				));
            }
            
            $ob_pdf->Output();
            exit;
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
	
	public function agenda($cd_treinamento_diretoria_conselhos)
    {
        if($this->get_permissao())
		{
			$this->load->model('projetos/treinamento_diretoria_conselhos_model');
			
            $data = array();
			
			$data['cd_treinamento_diretoria_conselhos'] = $cd_treinamento_diretoria_conselhos;
			
			$data['collection'] = $this->treinamento_diretoria_conselhos_model->agenda_listar($cd_treinamento_diretoria_conselhos);			
			
            $this->load->view('cadastro/treinamento_diretoria_conselhos/agenda', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function agenda_salvar()
    {
        if($this->get_permissao())
		{
			$this->load->model('projetos/treinamento_diretoria_conselhos_model');
			
            $args = array();

            $args['cd_treinamento_diretoria_conselhos'] = $this->input->post('cd_treinamento_diretoria_conselhos', TRUE);
            $args['dt_agenda']             				= $this->input->post('dt_agenda', TRUE);
            $args['hr_ini']                				= $this->input->post('hr_ini', TRUE);
            $args['hr_fim']                				= $this->input->post('hr_fim', TRUE);
            $args['usuario']               				= $this->session->userdata('codigo');

            $this->treinamento_diretoria_conselhos_model->agenda_salvar($args);
			
            redirect('cadastro/treinamento_diretoria_conselhos/agenda/'.$args['cd_treinamento_diretoria_conselhos']);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }	
	
    public function agenda_excluir($cd_treinamento_diretoria_conselhos_agenda, $cd_treinamento_diretoria_conselhos)
    {
        if($this->get_permissao())
		{
			$this->load->model('projetos/treinamento_diretoria_conselhos_model');
		
            $this->treinamento_diretoria_conselhos_model->agenda_excluir($cd_treinamento_diretoria_conselhos_agenda, $this->session->userdata('codigo'));
			
            redirect('cadastro/treinamento_diretoria_conselhos/agenda/'.$cd_treinamento_diretoria_conselhos);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    } 

    public function agenda_atualizar($cd_treinamento_diretoria_conselhos)
    {
		if($this->get_permissao())
		{
			$this->load->model('projetos/treinamento_diretoria_conselhos_model');
		
            $this->treinamento_diretoria_conselhos_model->agenda_atualizar($cd_treinamento_diretoria_conselhos, $this->session->userdata('codigo'));
            
            redirect('cadastro/treinamento_diretoria_conselhos/agenda/'.$cd_treinamento_diretoria_conselhos);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    } 	
}
?>