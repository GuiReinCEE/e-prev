<?php
class Treinamento_colaborador extends Controller
{
    function __construct()
    {
        parent::Controller();
        $this->load->model('projetos/treinamento_colaborador_model');
        CheckLogin();
    }

    private function get_permissao()
    {
        if($this->session->userdata('indic_09') == '*')
        {
            return TRUE;
        }
        else if($this->session->userdata('indic_05') == 'S')
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    function index()
    {
        if($this->get_permissao())
		{
            $result = null;
            $args = array();
            $data = array();
            
            $this->treinamento_colaborador_model->treinamento_colaborador_tipo($result, $args);
            $data['arr_tipo'] = $result->result_array();

            $data['drop'] = array(
            	array('value' => 'S', 'text' => 'Sim'),
            	array('value' => 'N', 'text' => 'Não')
            );

            $this->load->view('cadastro/treinamento_colaborador/index',$data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function listar()
    {
        if($this->get_permissao())
		{
            $result = null;
            $data = Array();
            $args = Array();
            
            $args["numero"]                          = $this->input->post("numero", TRUE);
            $args["ano"]                             = $this->input->post("ano", TRUE);
            $args["nome"]                            = $this->input->post("nome", TRUE);
            $args["dt_inicio_ini"]                   = $this->input->post("dt_inicio_ini", TRUE);
            $args["dt_inicio_fim"]                   = $this->input->post("dt_inicio_fim", TRUE);
            $args["dt_final_ini"]                    = $this->input->post("dt_final_ini", TRUE);
            $args["dt_final_fim"]                    = $this->input->post("dt_final_fim", TRUE);
            $args["cd_treinamento_colaborador_tipo"] = $this->input->post("cd_treinamento_colaborador_tipo", TRUE);
            $args["cd_empresa"]                      = $this->input->post("cd_empresa", TRUE);
            $args["cd_registro_empregado"]           = $this->input->post("cd_registro_empregado", TRUE);
            $args["seq_dependencia"]                 = $this->input->post("seq_dependencia", TRUE);
			$args["nome_colaborador"]                = $this->input->post("nome_colaborador", TRUE);
			$args["fl_avaliacoes_preenchidos"]       = $this->input->post("fl_avaliacoes_preenchidos", TRUE);
			$args["fl_cadastro_rh"]       			 = $this->input->post("fl_cadastro_rh", TRUE);
            $args["fl_bem_estar"]                    = $this->input->post("fl_bem_estar", TRUE);    
			$args["fl_certificado"]					 = $this->input->post("fl_certificado", TRUE);
            
            manter_filtros($args);
            
            $this->treinamento_colaborador_model->listar($result, $args);

            $data['collection'] = $result->result_array();

            $this->load->view('cadastro/treinamento_colaborador/partial_result',$data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function cadastro($ano=0, $numero=0)
    {
        if($this->get_permissao())
		{
            $args = Array();
            $data = Array();
            $data['collection'] = Array();
            $result = null;
            $data['numero'] = intval($numero);
            $data['ano'] = intval($ano);

            $this->treinamento_colaborador_model->uf($result, $args);
            $data['arr_uf'] = $result->result_array();

            if($data['numero'] == 0 AND $data['ano'] ==0 )
            {
                $data['row'] = array(
                                'numero_a' 							=> '',
                                'numero' 							=> 0,
                                'ano' 								=> 0,
                                'nome'								=> '',
                                'promotor' 							=> '',
                                'endereco' 							=> '',
                                'cidade' 							=> '',
                                'uf' 								=> '',
                                'dt_inicio' 						=> '',
                                'hr_inicio' 						=> '',
                                'dt_final' 							=> '',
                                'hr_final' 							=> '',
                                'carga_horaria' 					=> '',
                                'vl_unitario' 						=> '',
                                'dt_pagamento' 						=> '',
                                'dt_exclusao' 						=> '',
                                'cd_treinamento_colaborador_tipo' 	=> 0,
                                'fl_certificado' 					=> '',
                                'fl_bem_estar'  					=> ''
                                );
            }
            else
            {
                $args['numero'] = intval($numero);
                $args['ano'] = intval($ano);
                $this->treinamento_colaborador_model->carrega($result, $args);
                $data['row'] = $result->row_array();

                $this->treinamento_colaborador_model->colaboradores($result, $args);
                $data['collection'] = $result->result_array();
				
                $data['collection_gerencia'] = $this->treinamento_colaborador_model->respostas_gerencia($args);
            }

            $this->load->view('cadastro/treinamento_colaborador/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar()
    {
        if($this->get_permissao())
		{
            $args = Array();
            $data = Array();
            $result = null;

            $args["numero"]                          = $this->input->post("numero", TRUE);
            $args["ano"]                             = $this->input->post("ano", TRUE);
            $args["nome"]                            = $this->input->post("nome", TRUE);
            $args["promotor"]                        = $this->input->post("promotor", TRUE);
            $args["endereco"]                        = $this->input->post("endereco", TRUE);
            $args["cidade"]                          = $this->input->post("cidade", TRUE);
            $args["uf"]                              = $this->input->post("uf", TRUE);
            $args["dt_inicio"]                       = $this->input->post("dt_inicio", TRUE);
            $args["hr_inicio"]                       = $this->input->post("hr_inicio", TRUE);
            $args["dt_final"]                        = $this->input->post("dt_final", TRUE);
            $args["hr_final"]                        = $this->input->post("hr_final", TRUE); 
            $args["carga_horaria"]                   = $this->input->post("carga_horaria", TRUE); 
            $args["vl_unitario"]                     = $this->input->post("vl_unitario", TRUE); 
            $args["fl_certificado"]                  = $this->input->post("fl_certificado", TRUE); 
            $args["fl_bem_estar"]                  = $this->input->post("fl_bem_estar", TRUE); 

            $args["vl_unitario"] = str_replace('.', '', $args["vl_unitario"]);
            $args["vl_unitario"] = str_replace(',', '.', $args["vl_unitario"]);
            
            $args["carga_horaria"] = str_replace('.', '', $args["carga_horaria"]);
            $args["carga_horaria"] = str_replace(',', '.', $args["carga_horaria"]);

            $args["cd_treinamento_colaborador_tipo"] = $this->input->post("cd_treinamento_colaborador_tipo", TRUE); 
            $args["usuario"]                         = $this->session->userdata('codigo');

            $numero_ano = $this->treinamento_colaborador_model->salvar($result, $args);
            redirect("cadastro/treinamento_colaborador/cadastro/".$numero_ano, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function excluir($ano=0, $numero=0)
    {
        if($this->get_permissao())
		{
			if((intval($numero) > 0) and (intval($ano) > 0))
			{
				$args = Array();
				$data = Array();
				$result = null;

				$args['numero']  = intval($numero);
				$args['ano']     = intval($ano);
				$args['usuario'] = $this->session->userdata('codigo');

				$numero_ano = $this->treinamento_colaborador_model->excluir($result, $args);
				redirect("cadastro/treinamento_colaborador/cadastro/".$numero_ano, "refresh");
			}
			else
			{
				exibir_mensagem("TREINAMENTO NÃO LOCALIZADO");
			}            
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }     
	
	
	function colaborador($ano=0, $numero=0, $cd_treinamento_colaborador_item = 0)
    {
        if($this->get_permissao())
		{
            $args = Array();
            $data = Array();
            $result = null;
            $args['numero'] = intval($numero);
            $args['ano']    = intval($ano);

            $this->treinamento_colaborador_model->gerencias($result, $args);
            $data['gerencias'] = $result->result_array();

            $data['numero'] = intval($numero);
            $data['ano']    = intval($ano);

            if(intval($cd_treinamento_colaborador_item) == 0)
            {
                $data['row'] = array(
                    'cd_treinamento_colaborador_item' => 0,
                    'cd_empresa'                      => '',
                    'cd_registro_empregado'           => '',
                    'seq_dependencia'                 => '',
                    'nome'                            => '',
                    'area'                            => '',
                    'centro_custo'                    => '',
                    'arquivo'                         => '',
                    'arquivo_nome'                    => ''
                );
            }
            else
            {
                $data['row'] = $this->treinamento_colaborador_model->carrega_colaborador($cd_treinamento_colaborador_item); 
            }

            $this->load->view('cadastro/treinamento_colaborador/colaborador', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvarColaborador()
    {
        if($this->get_permissao())
		{
            $args = Array();
            $data = Array();
            $result = null;

            $cd_treinamento_colaborador_item = $this->input->post("cd_treinamento_colaborador_item", TRUE);

            $args["numero"]                = $this->input->post("numero", TRUE);
            $args["ano"]                   = $this->input->post("ano", TRUE); 
            $args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
            $args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
            $args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
            $args["nome"]                  = $this->input->post("nome", TRUE);
            $args["centro_custo"]          = $this->input->post("centro_custo", TRUE);
            $args["area"]                  = $this->input->post("area", TRUE);
            $args['arquivo']               = $this->input->post('arquivo', TRUE);
            $args['arquivo_nome']          = $this->input->post('arquivo_nome', TRUE);
            $args['usuario']               = $this->session->userdata('codigo');


            if(intval($cd_treinamento_colaborador_item) == 0)
            {
                $this->treinamento_colaborador_model->salvar_colaborador($result, $args);
            
                $this->treinamento_colaborador_model->agendaAtualizar($result, $args);
            }
            else
            {
                $this->treinamento_colaborador_model->atualizar_colaborador($cd_treinamento_colaborador_item, $args);
            }

            
			
            redirect("cadastro/treinamento_colaborador/cadastro/".$args["ano"]."/".$args["numero"], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    
    function excluirColaborador($cd_treinamento_colaborador_item)
    {
        if($this->get_permissao())
		{
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_treinamento_colaborador_item'] = $cd_treinamento_colaborador_item;
            $args['usuario']                         = $this->session->userdata('codigo');

            $row = $this->treinamento_colaborador_model->excluir_colaborador($result, $args);
			
            $args['numero'] = $row['numero'];
            $args['ano']    = $row['ano'];

			$this->treinamento_colaborador_model->agendaAtualizar($result, $args);
			
            redirect("cadastro/treinamento_colaborador/cadastro/".$row['nr_treinamento_colaborador'], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    } 
    
    function pdf()
    {
        if($this->get_permissao())
		{
            $args = Array();
            $data = Array();
            $result = null;
            
            $args["numero"]                          = $this->input->post("numero", TRUE);
            $args["ano"]                             = $this->input->post("ano", TRUE);
            $args["nome_participante"]               = $this->input->post("nome_participante", TRUE);
            $args["dt_inicio_ini"]                   = $this->input->post("dt_inicio_ini", TRUE);
            $args["dt_inicio_fim"]                   = $this->input->post("dt_inicio_fim", TRUE);
            $args["dt_final_ini"]                    = $this->input->post("dt_final_ini", TRUE);
            $args["dt_final_fim"]                    = $this->input->post("dt_final_fim", TRUE);
            $args["cd_treinamento_colaborador_tipo"] = $this->input->post("cd_treinamento_colaborador_tipo", TRUE);
            $args["cd_empresa"]                      = $this->input->post("cd_empresa", TRUE);
            $args["cd_registro_empregado"]           = $this->input->post("cd_registro_empregado", TRUE);
            $args["seq_dependencia"]                 = $this->input->post("seq_dependencia", TRUE);
			$args["nome_colaborador"]                = $this->input->post("nome_colaborador", TRUE);
            
            manter_filtros($args);
            $this->treinamento_colaborador_model->listar($result, $args);
            
            $this->load->plugin('fpdf');
            
            $collection = $result->result_array();
 
            $ob_pdf = new PDF('L','mm','A4');

            $ob_pdf->SetNrPag(true);
            $ob_pdf->SetMargins(10,14,5);
            $ob_pdf->header_exibe = true;
            $ob_pdf->header_logo = true;
            $ob_pdf->header_titulo = true;
            $ob_pdf->header_titulo_texto = "Treinamento Colaborador";
            
            $ob_pdf->AddPage();			
            
            if(trim($args["nome_colaborador"]) != "")
            {
                $ob_pdf->SetFont( 'Courier', '', 10 );
                $ob_pdf->MultiCell(190, 6, "Colaborador: ".$args["nome_colaborador"].(intval($args["cd_registro_empregado"]) > 0 ? ' - '.$args["cd_empresa"].'/'.$args["cd_registro_empregado"].'/'.$args["seq_dependencia"] : ""));
            }
            
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            //$ob_pdf->SetWidths( array(25, 75, 15, 50, 30, 80) );
            $ob_pdf->SetWidths( array(20, 55, 55, 30, 10, 25, 25, 30, 25) );
            $ob_pdf->SetAligns( array('C','C','C','C','C','C', 'C', 'C', 'C') );
            $ob_pdf->SetFont( 'Courier', 'B', 10 );
            $ob_pdf->Row(array("Número", "Nome", "Promotor", "Cidade", "UF", "Dt Início", "Dt Final", "Tipo", "Carga Horária(H)"));
            $ob_pdf->SetAligns( array('C','L', 'L','L','C','C','C', 'L', 'R') );
            $ob_pdf->SetFont( 'Courier', '', 10 );
            
            foreach($collection as $item)
            {
                $ob_pdf->Row(array($item['numero'], $item['nome'], $item['promotor'], $item['cidade'], $item['uf']
                    ,$item['dt_inicio'], $item['dt_final'], $item['ds_treinamento_colaborador_tipo'], str_replace('.', ',', $item['carga_horaria'])));	
  
            }
            
            $ob_pdf->Output();
            exit;
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
        
    }
	
	function agenda($ano=0, $numero=0)
    {
        if($this->get_permissao())
		{
            $args = Array();
            $data = Array();
            $result = null;
            $args['numero'] = intval($numero);
            $args['ano']    = intval($ano);

            $data['numero'] = intval($numero);
            $data['ano']    = intval($ano);

			$this->treinamento_colaborador_model->agendaListar($result, $args);
			$data['collection'] = $result->result_array();			
			
            $this->load->view('cadastro/treinamento_colaborador/agenda', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function agendaSalvar()
    {
        if($this->get_permissao())
		{
            $args = Array();
            $data = Array();
            $result = null;

            $args["numero"]                = $this->input->post("numero", TRUE);
            $args["ano"]                   = $this->input->post("ano", TRUE); 
            $args["dt_agenda"]             = $this->input->post("dt_agenda", TRUE);
            $args["hr_ini"]                = $this->input->post("hr_ini", TRUE);
            $args["hr_fim"]                = $this->input->post("hr_fim", TRUE);
            $args['usuario']               = $this->session->userdata('codigo');

            $this->treinamento_colaborador_model->agendaSalvar($result, $args);
            redirect("cadastro/treinamento_colaborador/agenda/".$args["ano"]."/".$args["numero"], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	
	
    function agendaExcluir($ano=0, $numero=0, $cd_treinamento_colaborador_agenda)
    {
        if($this->get_permissao())
		{
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_treinamento_colaborador_agenda'] = $cd_treinamento_colaborador_agenda;
            $args['usuario']                           = $this->session->userdata('codigo');
            
			$args['numero'] = intval($numero);
            $args['ano']    = intval($ano);			
			
            $data['numero'] = intval($numero);
            $data['ano']    = intval($ano);			

            $numero_ano = $this->treinamento_colaborador_model->agendaExcluir($result, $args);
            redirect("cadastro/treinamento_colaborador/agenda/".$ano."/".$numero, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    } 

    function agendaAtualizar($ano=0, $numero=0)
    {
        if($this->get_permissao())
		{
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_treinamento_colaborador_agenda'] = $this->session->userdata('cd_treinamento_colaborador_agenda');
            $args['usuario']                           = $this->session->userdata('codigo');
			
            $args['numero'] = intval($numero);
            $args['ano']    = intval($ano);			

            $data['numero'] = intval($numero);
            $data['ano']    = intval($ano);			

            $numero_ano = $this->treinamento_colaborador_model->agendaAtualizar($result, $args);
            redirect("cadastro/treinamento_colaborador/agenda/".$ano."/".$numero, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    } 

    public function encerrar_avaliacao($cd_treinamento_colaborador_resposta)
    {
        if($this->get_permissao())
        {
            $data['row'] = $this->treinamento_colaborador_model->carrega_avaliacao($cd_treinamento_colaborador_resposta);

            $this->load->view('cadastro/treinamento_colaborador/encerrar_avaliacao', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_encerramento()
    {
        if($this->get_permissao())
        {
            $ds_ano_numero                       = $this->input->post('ds_ano_numero', TRUE);
            $cd_treinamento_colaborador_resposta = $this->input->post('cd_treinamento_colaborador_resposta', TRUE);

            $args = array(
                'ds_justificativa_finalizado' => $this->input->post('ds_justificativa_finalizado', TRUE),
                'cd_usuario'                  => $this->session->userdata('codigo')
            );

            $this->treinamento_colaborador_model->salvar_encerramento($cd_treinamento_colaborador_resposta, $args);

            redirect('cadastro/treinamento_colaborador/cadastro/'.$ds_ano_numero, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}
?>