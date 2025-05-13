<?php
class relatorio_atividades extends Controller
{
    function __construct()
    {
        parent::Controller();
		$this->load->model('projetos/relatorio_atividades_model');
		$this->load->model('projetos/atividade_historico_model');
		$this->load->model('projetos/atividade_acompanhamento_model');
    }

	function index($cd_gerencia, $nr_ano, $cd_atendente = '')
    {
		CheckLogin();
        if(gerencia_in(array('GTI')))
        {
			$result = null;
			$args = Array();
			$data = Array();

			$args['cd_gerencia']  = $cd_gerencia;
			$args['nr_ano']       = $nr_ano;
			$args['cd_atendente'] = $cd_atendente;
			
            $this->relatorio_atividades_model->listarAbertasPeriodo($result, $args);
			$ar_periodo = $result->result_array();			
			
            $this->relatorio_atividades_model->listarEncerradas($result, $args);
			$ar_encerrada = $result->result_array();


            $this->relatorio_atividades_model->listarEmTeste($result, $args);
			$ar_teste = $result->result_array();	
			
            $this->relatorio_atividades_model->listarAguardaUsuario($result, $args);
			$ar_aguardando_usuario = $result->result_array();	

            $this->relatorio_atividades_model->listarAbertas($result, $args);
			$ar_aberta = $result->result_array();	
			
			
			$this->load->plugin('fpdf');
			
			
			$ob_pdf = new PDF('P', 'mm', 'A4');
			$ob_pdf->AddFont('segoeuil');
			$ob_pdf->AddFont('segoeuib');		
			#$ob_pdf->SetNrPag(true);
			$ob_pdf->SetNrPagDe(true);
			$ob_pdf->SetMargins(10, 14, 5);
			$ob_pdf->header_exibe = true;
			$ob_pdf->header_logo = true;
			$ob_pdf->header_titulo = true;
			$ob_pdf->header_titulo_texto = "Relatório de Atividades solicitadas para a TI \n".$args['cd_gerencia']." - ".$args['nr_ano'];
			$ob_pdf->AddPage();

			$altura_linha = 6;
			$ob_pdf->SetY($ob_pdf->GetY() + 4);
			$ob_pdf->SetFont('segoeuib', '', 16);				
			$ob_pdf->MultiCell(190, $altura_linha, "Área: ".$args['cd_gerencia']);	
			
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetFont('segoeuib', '', 16);				
			$ob_pdf->MultiCell(190, $altura_linha, "Período: ".$args['nr_ano']);				
			
			$ob_pdf->SetY($ob_pdf->GetY() + 4);
			$ob_pdf->SetFont('segoeuib', '', 16);				
			$ob_pdf->MultiCell(190, $altura_linha, "Resumo");	
			$ob_pdf->SetY($ob_pdf->GetY() + 2);				

			
			$altura_linha = 8;
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
		
			$ob_pdf->SetFont('segoeuil', '', 12);				
			$ob_pdf->MultiCell(190, $altura_linha, "Total de atividades EM ANDAMENTO: ".count($ar_aberta));	

			$ob_pdf->SetFont('segoeuil', '', 12);				
			$ob_pdf->MultiCell(190, $altura_linha, "Total de atividades AGUARDANDO VALIDAÇÃO por parte do usuário (EM TESTE): ".count($ar_teste));

			$ob_pdf->SetFont('segoeuil', '', 12);				
			$ob_pdf->MultiCell(190, $altura_linha, "Total de atividades PENDENTES por parte do usuário (AGUARDANDO USUÁRIO): ".count($ar_aguardando_usuario));			

			$ob_pdf->SetFont('segoeuil', '', 12);				
			$ob_pdf->MultiCell(190, $altura_linha, "Total de atividades ABERTAS no período: ".count($ar_periodo));				
			
			$ob_pdf->SetFont('segoeuil', '', 12);				
			$ob_pdf->MultiCell(190, $altura_linha, "Total de atividades ATENDIDAS no período: ".count($ar_encerrada));	
			
			$ob_pdf->SetFont('segoeuil', '', 12);				
			$ob_pdf->MultiCell(190, $altura_linha, "Percentual de ATENDIMENTO no período: ".number_format(((count($ar_encerrada)/count($ar_periodo)) * 100),2,',','.')."%");	
			
			
			#$ob_pdf->SetFont('segoeuil', '', 12);				
			#$ob_pdf->MultiCell(190, $altura_linha, "Atividades ATENDIDAS por MÊS no período: ".floor((count($ar_encerrada) / 12)) );				
			
			$altura_linha = 5;
			
			$ob_pdf->SetY($ob_pdf->GetY() + 4);
			$ob_pdf->SetFont('segoeuib', '', 16);				
			$ob_pdf->MultiCell(190, $altura_linha, "Introdução");	
			$ob_pdf->SetY($ob_pdf->GetY() + 2);			
			
			$ob_pdf->SetFont('segoeuil', '', 10);				
			$ob_pdf->MultiCell(190, $altura_linha, "O presente relatório tem por objetivo apresentar todas as atividades realizadas pela GTI no período informado acima. De acordo com as ITs da GTI, para toda solicitação é necessário ser aberta uma atividade no e-prev.

Com relação a sua situação, as atividades são agrupadas em quatro categorias:

- Atividades ATENDIDAS no período: atividades que, independente da data de sua abertura, foram encerrados no período;

- Atividades ABERTAS no período: atividades que, independente do status atual, foram registradas no período;

- Atividades PENDENTES por parte do usuário (AGUARDANDO USUÁRIO): atividades que aguardam providências por parte do solicitante para o seu prosseguimento.

- Atividades AGUARDANDO VALIDAÇÃO por parte do usuário (EM TESTE): atividades que aguardam validação/verificação por parte do solicitante para o seu encerramento.

- Atividades EM ANDAMENTO: atividades que, independente da data de sua abertura e das ações realizadas no período, ainda não foram encerrados;

");				
			$altura_linha = 5;
			$ob_pdf->SetY($ob_pdf->GetY() + 4);
/*

O Tempo Atendimento: é calculado em dias úteis a partir da diferença entre a data de cadastro da atividade e a data de envio para validação por parte do usuário (em teste) da atividade, cabe ressaltar que nesse espaço tempo está contabilizado o tempo em que a atividade ficou pendente por parte do usuário (aguardando usuário).

O Tempo Operacional: é calculado em dias úteis a partir da diferença entre a data de cadastro da atividade e a data de conclusão da atividade, cabe ressaltar que nesse espaço tempo está contabilizado o tempo em que a atividade ficou pendente por parte do usuário (aguardando usuário) e/ou aguardando validação por parte do usuário (em teste).
*/		
			
			
			#### ATENDIDAS #####
			$ob_pdf->AddPage();
			$ob_pdf->SetFont('segoeuil', '', 10);
			$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");				
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetFont('segoeuib', '', 17);
			#$ob_pdf->SetTextColor(220,50,50);#vermelho
			#$ob_pdf->SetTextColor(0,127,14);#verde
			#$ob_pdf->SetTextColor(255,140,0);#laranja
			$ob_pdf->SetTextColor(63,72,204);#azul
			$ob_pdf->MultiCell(190, $altura_linha, "Atividades atendidas no período (".count($ar_encerrada).")");
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetTextColor(0,0,0);
			
			$ob_pdf->SetFont('segoeuil', '', 10);
			$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");			
			
			foreach ($ar_encerrada as $ar_item)
			{
				$ob_pdf->SetY($ob_pdf->GetY() + 3);
				$ob_pdf->SetFont('segoeuib','', 12);
				$ob_pdf->MultiCell(190, $altura_linha, "#".$ar_item['numero']." - ".$ar_item['assunto']);
				$ob_pdf->SetY($ob_pdf->GetY() + 2);				

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Dt cadastro | Dt teste | Dt conclusão:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['dt_cadastro']." | ".$ar_item['dt_teste']." | ".$ar_item['dt_fim']);	
				
				#$ob_pdf->SetFont('segoeuib', '', 10);
				#$ob_pdf->MultiCell(190, $altura_linha, "Tempo Atendimento:");	
				#$ob_pdf->SetFont('segoeuil', '', 10);				
				#$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ta']." dia(s) útil(eis)");					
				#
                #
				#$ob_pdf->SetFont('segoeuib', '', 10);
				#$ob_pdf->MultiCell(190, $altura_linha, "Tempo Operacional:");	
				#$ob_pdf->SetFont('segoeuil', '', 10);				
				#$ob_pdf->MultiCell(190, $altura_linha, $ar_item['to']." dia(s) útil(eis)");					
					

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Status:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_status']);					
				
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Solicitante:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_solicitante']);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Atendente:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_atendente']);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Descrição:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['descricao']);				
				
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Justificativa:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['justificativa']);		

				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Histórico:");

				$args['cd_atividade'] = $ar_item['numero'];
				$this->atividade_historico_model->listar($result, $args);
				$ar_historico = $result->result_array();				

				foreach(array_reverse($ar_historico) as $ar_reg)
				{
					$ob_pdf->SetFont('segoeuil', '', 10);	
					$ob_pdf->MultiCell(190, $altura_linha, "* ".$ar_reg['data']." - ".$ar_reg['responsavel']."\n".$ar_reg['status']." ".(trim($ar_reg['complemento']) != "" ? ": ".$ar_reg['complemento'] : ""));	
					$ob_pdf->SetY($ob_pdf->GetY() + 2);
				}

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Acompanhamento(s):");	

				$args['cd_usuario']   = $ar_item['cd_solicitante'];
				$args['cd_atividade'] = $ar_item['numero'];
				$this->atividade_acompanhamento_model->listar($result, $args);
				$ar_acomp = $result->result_array();
				
				foreach(array_reverse($ar_acomp) as $ar_reg)
				{
					$ob_pdf->SetFont('segoeuil', '', 10);	
					$ob_pdf->MultiCell(190, $altura_linha, "* ".$ar_reg['dt_inclusao']." - ".$ar_reg['nome']."\n".$ar_reg['ds_atividade_acompanhamento']);	
					$ob_pdf->SetY($ob_pdf->GetY() + 2);
				}				
				
				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetFont('segoeuil', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");
			}			
			
			#### AGUARDANDO USUÁRIO #####
			$ob_pdf->AddPage();
			$ob_pdf->SetFont('segoeuil', '', 10);
			$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");				
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetFont('segoeuib', '', 16);
			$ob_pdf->SetTextColor(220,50,50);#vermelho
			$ob_pdf->MultiCell(190, $altura_linha, "Atividades pendentes por parte do usuário (aguardando usuário) (".count($ar_aguardando_usuario).")");
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetTextColor(0,0,0);
			
			$ob_pdf->SetFont('segoeuil', '', 10);
			$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");			
		
			foreach ($ar_aguardando_usuario as $ar_item)
			{
				$ob_pdf->SetY($ob_pdf->GetY() + 3);
				$ob_pdf->SetFont('segoeuib','', 12);
				$ob_pdf->MultiCell(190, $altura_linha, "#".$ar_item['numero']." - ".$ar_item['assunto']);
				$ob_pdf->SetY($ob_pdf->GetY() + 2);		

				$ob_pdf->SetFont('segoeuib', '', 11);
				$ob_pdf->MultiCell(190, $altura_linha, "DT LIMITE PARA DEFINIÇÃO: ".$ar_item['dt_aguardando_usuario_limite']);				
				$ob_pdf->SetY($ob_pdf->GetY() + 2);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Dt cadastro:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['dt_cadastro']);		

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Prioridade:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['nr_prioridade']);					

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Status:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_status']);	
				
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Solicitante:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_solicitante']);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Atendente:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_atendente']);				

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Descrição:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['descricao']);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Justificativa:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['justificativa']);					

				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Histórico:");	

				$args['cd_atividade'] = $ar_item['numero'];
				$this->atividade_historico_model->listar($result, $args);
				$ar_historico = $result->result_array();
				
				foreach(array_reverse($ar_historico) as $ar_reg)
				{
					$ob_pdf->SetFont('segoeuil', '', 10);	
					$ob_pdf->MultiCell(190, $altura_linha, "* ".$ar_reg['data']." - ".$ar_reg['responsavel']."\n".$ar_reg['status']." ".(trim($ar_reg['complemento']) != "" ? ": ".$ar_reg['complemento'] : ""));	
					$ob_pdf->SetY($ob_pdf->GetY() + 2);
				}

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Acompanhamento(s):");	

				$args['cd_usuario']   = $ar_item['cd_solicitante'];
				$args['cd_atividade'] = $ar_item['numero'];
				$this->atividade_acompanhamento_model->listar($result, $args);
				$ar_acomp = $result->result_array();
				
				foreach(array_reverse($ar_acomp) as $ar_reg)
				{
					$ob_pdf->SetFont('segoeuil', '', 10);	
					$ob_pdf->MultiCell(190, $altura_linha, "* ".$ar_reg['dt_inclusao']." - ".$ar_reg['nome']."\n".$ar_reg['ds_atividade_acompanhamento']);	
					$ob_pdf->SetY($ob_pdf->GetY() + 2);
				}

				
				
				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetFont('segoeuil', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");
			}			
			
			
			#### EM TESTE #####
			$ob_pdf->AddPage();
			$ob_pdf->SetFont('segoeuil', '', 10);
			$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");				
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetFont('segoeuib', '', 17);
			$ob_pdf->SetTextColor(255,140,0);#laranja
			$ob_pdf->MultiCell(190, $altura_linha, "Atividades aguardando validação por parte do usuário (em teste) (".count($ar_teste).")");
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetTextColor(0,0,0);

			
			$ob_pdf->SetFont('segoeuil', '', 10);
			$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");			
		
			foreach ($ar_teste as $ar_item)
			{
				$ob_pdf->SetY($ob_pdf->GetY() + 3);
				$ob_pdf->SetFont('segoeuib','', 12);
				$ob_pdf->MultiCell(190, $altura_linha, "#".$ar_item['numero']." - ".$ar_item['assunto']);
				$ob_pdf->SetY($ob_pdf->GetY() + 2);				

				$ob_pdf->SetFont('segoeuib', '', 11);
				$ob_pdf->MultiCell(190, $altura_linha, "DT LIMITE PARA TESTE: ".$ar_item['dt_limite_teste']);				
				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Dt cadastro | Dt Teste");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['dt_cadastro']." | ".$ar_item['dt_teste']);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Status:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_status']);	
				
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Solicitante:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_solicitante']);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Atendente:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_atendente']);				

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Descrição:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['descricao']);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Justificativa:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['justificativa']);					
				
				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetFont('segoeuil', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");
			}			
			
				
			
			#### EM ANDAMENTO #####
			$ob_pdf->AddPage();
			$ob_pdf->SetFont('segoeuil', '', 10);
			$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");				
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetFont('segoeuib', '', 17);
			$ob_pdf->SetTextColor(0,127,14);#verde
			$ob_pdf->MultiCell(190, $altura_linha, "Atividades em andamento (".count($ar_aberta).")");
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetTextColor(0,0,0);
			
			$ob_pdf->SetFont('segoeuil', '', 10);
			$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");			
		
			foreach ($ar_aberta as $ar_item)
			{
				$ob_pdf->SetY($ob_pdf->GetY() + 3);
				$ob_pdf->SetFont('segoeuib','', 12);
				$ob_pdf->MultiCell(190, $altura_linha, "#".$ar_item['numero']." - ".$ar_item['assunto']);
				$ob_pdf->SetY($ob_pdf->GetY() + 2);				
				
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Dt cadastro");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['dt_cadastro']);	
				
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Prioridade:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['nr_prioridade']);					

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Status:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_status']);					
				
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Solicitante:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_solicitante']);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Atendente:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_atendente']);				

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Descrição:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['descricao']);		

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Justificativa:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['justificativa']);		

				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Histórico:");	

				$args['cd_atividade'] = $ar_item['numero'];
				$this->atividade_historico_model->listar($result, $args);
				$ar_historico = $result->result_array();
				
				foreach(array_reverse($ar_historico) as $ar_reg)
				{
					$ob_pdf->SetFont('segoeuil', '', 10);	
					$ob_pdf->MultiCell(190, $altura_linha, "* ".$ar_reg['data']." - ".$ar_reg['responsavel']."\n".$ar_reg['status']." ".(trim($ar_reg['complemento']) != "" ? ": ".$ar_reg['complemento'] : ""));	
					$ob_pdf->SetY($ob_pdf->GetY() + 2);
				}

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Acompanhamento(s):");	

				$args['cd_usuario']   = $ar_item['cd_solicitante'];
				$args['cd_atividade'] = $ar_item['numero'];
				$this->atividade_acompanhamento_model->listar($result, $args);
				$ar_acomp = $result->result_array();
				
				foreach(array_reverse($ar_acomp) as $ar_reg)
				{
					$ob_pdf->SetFont('segoeuil', '', 10);	
					$ob_pdf->MultiCell(190, $altura_linha, "* ".$ar_reg['dt_inclusao']." - ".$ar_reg['nome']."\n".$ar_reg['ds_atividade_acompanhamento']);	
					$ob_pdf->SetY($ob_pdf->GetY() + 2);
				}				
				
				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetFont('segoeuil', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");
			}			
					
			
			$ob_pdf->Output();
			exit;			
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	
	
	
    function index_BKP_20171211($cd_gerencia, $nr_mes, $nr_ano)
    {
		CheckLogin();
        if(gerencia_in(array('GTI')))
        {
			$result = null;
			$args = Array();
			$data = Array();

			$args['cd_gerencia'] = $cd_gerencia;
			$args['nr_mes']      = $nr_mes;
			$args['nr_ano']      = $nr_ano;
			
            $this->relatorio_atividades_model->listarEncerradas($result, $args);
			$ar_encerrada = $result->result_array();


            $this->relatorio_atividades_model->listarEmTeste($result, $args);
			$ar_teste = $result->result_array();	
			
            $this->relatorio_atividades_model->listarAguardaUsuario($result, $args);
			$ar_aguardando_usuario = $result->result_array();	

            $this->relatorio_atividades_model->listarAbertas($result, $args);
			$ar_aberta = $result->result_array();	

            $this->relatorio_atividades_model->tmaArea($result, $args);
			$ar_tma = $result->row_array();			
			
            $this->relatorio_atividades_model->tmaInformatica($result, $args);
			$ar_tma_inf = $result->row_array();				
            
			#echo "<PRE>"; print_r($ar_tma); exit;
			
			
			$this->load->plugin('fpdf');
			
			
			$ob_pdf = new PDF('P', 'mm', 'A4');
			$ob_pdf->AddFont('segoeuil');
			$ob_pdf->AddFont('segoeuib');		
			$ob_pdf->SetNrPag(true);
			$ob_pdf->SetMargins(10, 14, 5);
			$ob_pdf->header_exibe = true;
			$ob_pdf->header_logo = true;
			$ob_pdf->header_titulo = true;
			$ob_pdf->header_titulo_texto = "Relatório Mensal de Atividades de TI \n".$args['cd_gerencia']." - ".$args['nr_mes']."/".$args['nr_ano'];
			$ob_pdf->AddPage();

			$altura_linha = 6;
			$ob_pdf->SetY($ob_pdf->GetY() + 4);
			$ob_pdf->SetFont('segoeuib', '', 16);				
			$ob_pdf->MultiCell(190, $altura_linha, "Área: ".$args['cd_gerencia']);	

			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetFont('segoeuil', '', 12);				
			$ob_pdf->MultiCell(190, $altura_linha, "Período: ".$args['nr_mes']."/".$args['nr_ano']);	
			
			$ob_pdf->SetFont('segoeuil', '', 12);				
			$ob_pdf->MultiCell(190, $altura_linha, "Total de atividades EM ANDAMENTO: ".count($ar_aberta));	

			$ob_pdf->SetFont('segoeuil', '', 12);				
			$ob_pdf->MultiCell(190, $altura_linha, "Total de atividades aguardando validação por parte do usuário (EM TESTE): ".count($ar_teste));

			$ob_pdf->SetFont('segoeuil', '', 12);				
			$ob_pdf->MultiCell(190, $altura_linha, "Total de atividades pendentes por parte do usuário (AGUARDANDO USUÁRIO): ".count($ar_aguardando_usuario));			

			$ob_pdf->SetFont('segoeuil', '', 12);				
			$ob_pdf->MultiCell(190, $altura_linha, "Total de atividades ATENDIDAS no período: ".count($ar_encerrada));	

			$tma = 0;
			foreach ($ar_encerrada as $ar_item)
			{
				$tma+= intval($ar_item['tma']);
			}
			
			$ob_pdf->SetFont('segoeuil', '', 12);				
			$ob_pdf->MultiCell(190, $altura_linha, "Tempo médio de atendimento (TMA) no período: ".intval($tma/( count($ar_encerrada) == 0 ? 1 : count($ar_encerrada) ))." dia(s)");				
			
			
			$ob_pdf->SetFont('segoeuil', '', 12);				
			$ob_pdf->MultiCell(190, $altura_linha, "Tempo médio de atendimento (TMA) do ano da área solicitante: ".$ar_tma['tma']." dia(s)");				
			
			
			$ob_pdf->SetFont('segoeuil', '', 12);				
			$ob_pdf->MultiCell(190, $altura_linha, "Tempo médio de atendimento (TMA) do ano da GTI: ".$ar_tma_inf['tma']." dia(s)");				
			$ob_pdf->SetY($ob_pdf->GetY() + 6);	
			
			
			$altura_linha = 5;
			
			
			$ob_pdf->SetFont('segoeuib', '', 16);				
			$ob_pdf->MultiCell(190, $altura_linha, "Introdução");	
			$ob_pdf->SetY($ob_pdf->GetY() + 2);			
			
			$ob_pdf->SetFont('segoeuil', '', 10);				
			$ob_pdf->MultiCell(190, $altura_linha, "O presente relatório tem por objetivo apresentar todas as atividades realizadas pela GTI no período supracitado. De acordo com as ITs da GTI, para toda solicitação é necessário ser aberta uma atividade no e-prev.
			
Com relação a sua situação, os atividades são agrupados em quatro macro categorias:

- Atividades ATENDIDAS no período: atividades que, independente da data de sua abertura, foram encerrados no período;

- Atividades pendentes por parte do usuário (AGUARDANDO USUÁRIO): atividades que aguardam providências por parte do solicitante para o seu prosseguimento.

- Atividades aguardando validação por parte do usuário (EM TESTE): atividades que aguardam validação/verificação por parte do solicitante para o seu encerramento.

- Atividades EM ANDAMENTO: atividades que, independente da data de sua abertura e das ações realizadas no período, ainda não foram encerrados;

O Tempo médio de atendimento (TMA) no período: é calculado em dias corridos a partir do tempo entre a data de cadastro da atividade e a da data de conclusão das atividades solicitadas pela área supracitada, considerado apenas as concluídas no período supracitado, cabe ressaltar que nesse espaço tempo pode estar incluído o tempo em que a atividade ficou pendente por parte do usuário (aguardando usuário) e aguardando validação por parte do usuário (em teste).

O Tempo médio de atendimento (TMA) do ano da área solicitante: é calculado  em dias corridos a partir do tempo entre a data de cadastro da atividade e a da data de conclusão das atividades solicitadas pela área supracitada, considerado o ano da conclusão, cabe ressaltar que nesse espaço tempo pode estar incluído o tempo em que a atividade ficou pendente por parte do usuário (aguardando usuário) e aguardando validação por parte do usuário (em teste).

O Tempo médio de atendimento (TMA) do ano da GTI: é calculado em dias corridos a partir do tempo entre a data de cadastro da atividade e a da data de conclusão de todas ativiades atendidas pela GTI, considerado o ano da conclusão, cabe ressaltar que nesse espaço tempo pode estar incluído o tempo em que a atividade ficou pendente por parte do usuário (aguardando usuário) e aguardando validação por parte do usuário (em teste).
");				
			
			$ob_pdf->SetY($ob_pdf->GetY() + 4);
		
			
			
			#### ATENDIDAS #####
			$ob_pdf->AddPage();
			$ob_pdf->SetFont('segoeuil', '', 10);
			$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");				
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetFont('segoeuib', '', 17);
			#$ob_pdf->SetTextColor(220,50,50);#vermelho
			#$ob_pdf->SetTextColor(0,127,14);#verde
			#$ob_pdf->SetTextColor(255,140,0);#laranja
			$ob_pdf->SetTextColor(63,72,204);#azul
			$ob_pdf->MultiCell(190, $altura_linha, "Atividades atendidas no período");
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetTextColor(0,0,0);
			
			$ob_pdf->SetFont('segoeuil', '', 10);
			$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");			
			
			foreach ($ar_encerrada as $ar_item)
			{
				$ob_pdf->SetY($ob_pdf->GetY() + 3);
				$ob_pdf->SetFont('segoeuib','', 12);
				$ob_pdf->MultiCell(190, $altura_linha, "#".$ar_item['numero']." - ".$ar_item['assunto']);
				$ob_pdf->SetY($ob_pdf->GetY() + 2);				

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Tempo de atendimento:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['tma']." dia(s)");	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Dt cadastro | Dt conclusão:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['dt_cadastro']." | ".$ar_item['dt_fim']);		

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Status:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_status']);					
				
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Solicitante:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_solicitante']);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Atendente:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_atendente']);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Descrição:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['descricao']);				
				
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Justificativa:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['justificativa']);					
				
				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetFont('segoeuil', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");
			}			
			
			#### AGUARDANDO USUÁRIO #####
			$ob_pdf->AddPage();
			$ob_pdf->SetFont('segoeuil', '', 10);
			$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");				
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetFont('segoeuib', '', 16);
			$ob_pdf->SetTextColor(220,50,50);#vermelho
			$ob_pdf->MultiCell(190, $altura_linha, "Atividades pendentes por parte do usuário (aguardando usuário)");
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetTextColor(0,0,0);
			
			$ob_pdf->SetFont('segoeuil', '', 10);
			$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");			
		
			foreach ($ar_aguardando_usuario as $ar_item)
			{
				$ob_pdf->SetY($ob_pdf->GetY() + 3);
				$ob_pdf->SetFont('segoeuib','', 12);
				$ob_pdf->MultiCell(190, $altura_linha, "#".$ar_item['numero']." - ".$ar_item['assunto']);
				$ob_pdf->SetY($ob_pdf->GetY() + 2);		

				$ob_pdf->SetFont('segoeuib', '', 11);
				$ob_pdf->MultiCell(190, $altura_linha, "DT LIMITE PARA DEFINIÇÃO: ".$ar_item['dt_aguardando_usuario_limite']);				
				$ob_pdf->SetY($ob_pdf->GetY() + 2);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Dt cadastro:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['dt_cadastro']);					

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Status:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_status']);	
				
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Solicitante:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_solicitante']);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Atendente:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_atendente']);				

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Descrição:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['descricao']);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Justificativa:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['justificativa']);					

				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Histórico:");	

				$args['cd_atividade'] = $ar_item['numero'];
				$this->atividade_historico_model->listar($result, $args);
				$ar_historico = $result->result_array();
				
				foreach(array_reverse($ar_historico) as $ar_reg)
				{
					$ob_pdf->SetFont('segoeuil', '', 10);	
					$ob_pdf->MultiCell(190, $altura_linha, "* ".$ar_reg['data']." - ".$ar_reg['responsavel']."\n".$ar_reg['status']." ".(trim($ar_reg['complemento']) != "" ? ": ".$ar_reg['complemento'] : ""));	
					$ob_pdf->SetY($ob_pdf->GetY() + 2);
				}

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Acompanhamento(s):");	

				$args['cd_usuario']   = $ar_item['cd_solicitante'];
				$args['cd_atividade'] = $ar_item['numero'];
				$this->atividade_acompanhamento_model->listar($result, $args);
				$ar_acomp = $result->result_array();
				
				foreach(array_reverse($ar_acomp) as $ar_reg)
				{
					$ob_pdf->SetFont('segoeuil', '', 10);	
					$ob_pdf->MultiCell(190, $altura_linha, "* ".$ar_reg['dt_inclusao']." - ".$ar_reg['nome']."\n".$ar_reg['ds_atividade_acompanhamento']);	
					$ob_pdf->SetY($ob_pdf->GetY() + 2);
				}

				
				
				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetFont('segoeuil', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");
			}			
			
			
			#### EM TESTE #####
			$ob_pdf->AddPage();
			$ob_pdf->SetFont('segoeuil', '', 10);
			$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");				
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetFont('segoeuib', '', 17);
			$ob_pdf->SetTextColor(255,140,0);#laranja
			$ob_pdf->MultiCell(190, $altura_linha, "Atividades aguardando validação por parte do usuário (em teste)");
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetTextColor(0,0,0);

			
			$ob_pdf->SetFont('segoeuil', '', 10);
			$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");			
		
			foreach ($ar_teste as $ar_item)
			{
				$ob_pdf->SetY($ob_pdf->GetY() + 3);
				$ob_pdf->SetFont('segoeuib','', 12);
				$ob_pdf->MultiCell(190, $altura_linha, "#".$ar_item['numero']." - ".$ar_item['assunto']);
				$ob_pdf->SetY($ob_pdf->GetY() + 2);				

				$ob_pdf->SetFont('segoeuib', '', 11);
				$ob_pdf->MultiCell(190, $altura_linha, "DT LIMITE PARA TESTE: ".$ar_item['dt_limite_teste']);				
				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Dt cadastro");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['dt_cadastro']);					

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Status:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_status']);	
				
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Solicitante:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_solicitante']);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Atendente:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_atendente']);				

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Descrição:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['descricao']);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Justificativa:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['justificativa']);					
				
				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetFont('segoeuil', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");
			}			
			
				
			
			#### EM ANDAMENTO #####
			$ob_pdf->AddPage();
			$ob_pdf->SetFont('segoeuil', '', 10);
			$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");				
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetFont('segoeuib', '', 17);
			$ob_pdf->SetTextColor(0,127,14);#verde
			$ob_pdf->MultiCell(190, $altura_linha, "Atividades em andamento");
			$ob_pdf->SetY($ob_pdf->GetY() + 2);
			$ob_pdf->SetTextColor(0,0,0);
			
			$ob_pdf->SetFont('segoeuil', '', 10);
			$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");			
		
			foreach ($ar_aberta as $ar_item)
			{
				$ob_pdf->SetY($ob_pdf->GetY() + 3);
				$ob_pdf->SetFont('segoeuib','', 12);
				$ob_pdf->MultiCell(190, $altura_linha, "#".$ar_item['numero']." - ".$ar_item['assunto']);
				$ob_pdf->SetY($ob_pdf->GetY() + 2);				
				
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Dt cadastro");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['dt_cadastro']);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Status:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_status']);					
				
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Solicitante:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_solicitante']);	

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Atendente:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['ds_atendente']);				

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Descrição:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['descricao']);		

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Justificativa:");	
				$ob_pdf->SetFont('segoeuil', '', 10);				
				$ob_pdf->MultiCell(190, $altura_linha, $ar_item['justificativa']);		

				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Histórico:");	

				$args['cd_atividade'] = $ar_item['numero'];
				$this->atividade_historico_model->listar($result, $args);
				$ar_historico = $result->result_array();
				
				foreach(array_reverse($ar_historico) as $ar_reg)
				{
					$ob_pdf->SetFont('segoeuil', '', 10);	
					$ob_pdf->MultiCell(190, $altura_linha, "* ".$ar_reg['data']." - ".$ar_reg['responsavel']."\n".$ar_reg['status']." ".(trim($ar_reg['complemento']) != "" ? ": ".$ar_reg['complemento'] : ""));	
					$ob_pdf->SetY($ob_pdf->GetY() + 2);
				}

				$ob_pdf->SetFont('segoeuib', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "Acompanhamento(s):");	

				$args['cd_usuario']   = $ar_item['cd_solicitante'];
				$args['cd_atividade'] = $ar_item['numero'];
				$this->atividade_acompanhamento_model->listar($result, $args);
				$ar_acomp = $result->result_array();
				
				foreach(array_reverse($ar_acomp) as $ar_reg)
				{
					$ob_pdf->SetFont('segoeuil', '', 10);	
					$ob_pdf->MultiCell(190, $altura_linha, "* ".$ar_reg['dt_inclusao']." - ".$ar_reg['nome']."\n".$ar_reg['ds_atividade_acompanhamento']);	
					$ob_pdf->SetY($ob_pdf->GetY() + 2);
				}				
				
				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetFont('segoeuil', '', 10);
				$ob_pdf->MultiCell(190, $altura_linha, "-------------------------------------------------------------------------------------------------------------------------------------");
			}			
					
			
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