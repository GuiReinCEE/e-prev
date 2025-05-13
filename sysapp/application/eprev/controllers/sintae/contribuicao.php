<?php
class contribuicao extends Controller
{
	function __construct()
	{
		parent::Controller();
	}

	/**
	 * Contribuição NORMAL - index
	 * @return unknown_type
	 */
	function normal()
	{
		redirect("sintae/contribuicao/primeiro");
	}

	/**
	 * Contribuição NORMAL - Primeiro
	 * Carregar tela de geração de contribuição para primeiro pagamento
	 */
	function primeiro()
	{
		$COM_EMAIL = TRUE; $TODOS = FALSE;
		$incons=array();

		$this->load->model('helper/contribuicao_model');
		$cm = $this->contribuicao_model;

		// $mes = $this->input->post("mes"); if($mes=='') $mes=date('m');
		$mes = '07'; //$this->input->post("mes"); if($mes=='') $mes=date('m');
		$ano = $this->input->post("ano"); if($ano=='') $ano=date('Y');

		$cobranca_enviada = $cm->cobranca_enviada(
			enum_public_planos::SINPRORS_PREVIDENCIA
			, enum_public_patrocinadoras::SINTAE
			, $mes
			, $ano
		);

		// Confirmação de Inscrição
		$r = $cm->confirmacao_inscricao(
			enum_public_planos::SINPRORS_PREVIDENCIA
			, enum_public_patrocinadoras::SINTAE
			, $mes
			, $ano 
		);
		if($r)
		{
			$inscricao["total"] = array('quantidade'=>intval($r['tot_internet_confirm']));
			$inscricao["bdl"] = array('quantidade'=>intval($r['tot_bdl_confirm']));
			$inscricao["cheque"] = array('quantidade'=>intval($r['tot_cheque_confirm']), 'valor'=>$r['vlr_cheque_confirm']);
			$inscricao["bco"] = array('quantidade'=>(int)$r['tot_debito_cc_confirm'], 'valor'=>$r['vlr_debito_cc_confirm']);
			$inscricao["deposito"] = array('quantidade'=>(int)$r['tot_deposito_confirm'], 'valor'=>$r['vlr_deposito_confirm']);
			$inscricao["folha"] = array('quantidade'=>(int)$r['tot_folha_confirm'], 'valor'=>$r['vlr_folha_confirm']);
		}
		else
		{
			$inscricao["total"] = array('quantidade'=>0);
			$inscricao["bdl"] = array('quantidade'=>0);
			$inscricao["cheque"] = array('quantidade'=>0, 'valor'=>0);
			$inscricao["bco"] = array('quantidade'=>0, 'valor'=>0);
			$inscricao["deposito"] = array('quantidade'=>0, 'valor'=>0);
			$inscricao["folha"] = array('quantidade'=>0, 'valor'=>0);
			
			$incons[] = "Não existe primeiro pagamento para essa competência!<br />";
		}

		// Geração de contribuição
		$r = $cm->geracao_contribuicao( 
			enum_public_planos::SINPRORS_PREVIDENCIA
			, enum_public_patrocinadoras::SINTAE
			, $mes
			, $ano 
		);
		$geracao=array();
		if($r)
		{
			$geracao["geral"] = array('quantidade'=>(int)$r['tot_internet_gerado'],'valor'=>$r['vlr_internet_gerado']);
			$geracao["bdl"] = array('quantidade'=>(int)$r['tot_bdl_gerado']);
			$geracao["cheque"] = array('quantidade'=>(int)$r['tot_cheque_gerado']);
			$geracao["bco"] = array('quantidade'=>(int)$r['tot_debito_cc_gerado'],'valor'=>$r['vlr_debito_cc_gerado']);
			$geracao["deposito"] = array('quantidade'=>(int)$r['tot_deposito_gerado']);
			$geracao["folha"] = array('quantidade'=>(int)$r['tot_folha_gerado'],'valor'=>$r['vlr_folha_gerado']);
		}
		else
		{
			$geracao["geral"] = array('quantidade'=>0,'valor'=>0);
			$geracao["bdl"] = array('quantidade'=>0);
			$geracao["cheque"] = array('quantidade'=>0);
			$geracao["bco"] = array('quantidade'=>0,'valor'=>0);
			$geracao["deposito"] = array('quantidade'=>0);
			$geracao["folha"] = array('quantidade'=>0,'valor'=>0);

			$incons[] = "Não existe geração para essa competência!<br />";
		}

		// Envio de cobrança
		$r = $cm->envio_contribuicao( 
			enum_public_planos::SINPRORS_PREVIDENCIA
			, enum_public_patrocinadoras::SINTAE
			, $mes
			, $ano 
		);
		$envio=array();

		if($r)
		{
			if( $r['dt_envio_bdl']!='' || $r['dt_envio_debito_cc']!='')
			{
				$incons[] = "Cobrança já foi gerada para essa competência!<br />";
			}
			// BDL
			$envio['bdl'] = array(
				 'email'=>$r['tot_bdl_enviado']
				,'quantidade'=>$r['tot_bdl_enviado']
				,'valor'=>$r['vlr_bdl_enviado']
			);

			// BCO
			$email = $r['tot_debito_cc_enviado'];
			$qtd = $r['tot_debito_cc_enviado'];
			$vlr = $r['vlr_debito_cc_enviado'];
			$envio['bdl'] = array('email'=>$email,'quantidade'=>$qtd,'valor'=>$vlr);
		}
		else
		{
			// BDL
			$email = $cm->quantidade_enviar( $COM_EMAIL, enum_public_planos::SINPRORS_PREVIDENCIA, enum_public_patrocinadoras::SINTAE, $mes, $ano, 'BDL' );
			$qtd = $cm->quantidade_enviar( $TODOS, enum_public_planos::SINPRORS_PREVIDENCIA, enum_public_patrocinadoras::SINTAE, $mes, $ano, 'BDL' );
			$envio['bdl'] = array('email'=>$email['contador'],'quantidade'=>$qtd['contador'],'valor'=>$qtd['valor']);

			// BCO
			$email = $cm->quantidade_enviar( $COM_EMAIL, enum_public_planos::SINPRORS_PREVIDENCIA, enum_public_patrocinadoras::SINTAE, $mes, $ano, 'BCO' );
			$qtd = $cm->quantidade_enviar( $TODOS, enum_public_planos::SINPRORS_PREVIDENCIA, enum_public_patrocinadoras::SINTAE, $mes, $ano, 'BCO' );
			$envio['bco'] = array('email'=>$email['contador'],'quantidade'=>$qtd['contador'],'valor'=>$qtd['valor']);
		}

		// GERAL
		$envio['geral'] = $geracao['geral'];

		// CHEQUE
		$envio['cheque'] = $geracao['cheque'];
		
		// DEPOSITO
		$envio['deposito'] = $geracao['deposito'];

		// FOLHA 
		$envio['folha'] = $geracao['folha'];

		// EMAILS GERADOS
		$envio['gerados']['email'] = $cm->contribuicao_controle_contador( 
			$ano, 
			$mes, 
			enum_public_patrocinadoras::SINTAE, 
			array(
				enum_projetos_contribuicao_controle_tipo::PRIMEIRO_PAGAMENTO_BDL,
				enum_projetos_contribuicao_controle_tipo::PRIMEIRO_PAGAMENTO_DEBITO_CONTA_CORRENTE
			)
		);

		// Inconsistências
		if( $envio['bdl']['quantidade']!=$geracao['bdl']['quantidade'] )
		{
			$incons[] = ' - Total BDL Gerado (' . $geracao['bdl']['quantidade'] . ') é diferente do Total BDL para Envio ( ' . $envio['bdl']['quantidade'] . ' )';
		}

		if( $envio['bco']['quantidade']!=$geracao['bco']['quantidade'] )
		{
			$incons[] = ' - Total Débito em Conta Gerado (' . $geracao['bco']['quantidade'] . ') é diferente do Total Débito em Conta para Envio ( ' . $envio['bco']['quantidade'] . ' )';
		}

		// COMANDOS

		// Gerar (existindo gerados ou alguma inconsistencia, não permite gerar)
		$gerar=(sizeof($incons)==0);

		// Listar gerados
		$listar_gerados=(intval($envio['gerados']['email'])>0);

		$listar_gerados_sem_email = (
			((int)$envio['bdl']['quantidade'] + (int)$envio['bco']['quantidade']) > ((int)$envio['bdl']['email'] + (int)$envio['bco']['email'])
		);

		// LISTAR PARTICIPANTES SEM EMAIL
		$participantes_sem_email = $cm->listar_participantes_sem_email( $ano, $mes, enum_public_patrocinadoras::SINTAE, enum_public_planos::SINPRORS_PREVIDENCIA, array('BDL', 'BCO') );

		$data['inscricao'] = $inscricao;
		$data['geracao'] = $geracao;
		$data['envio'] = $envio;
		$data['incons'] = $incons;
		$data['comandos']['gerar'] = $gerar;
		$data['comandos']['listar_gerados'] = $listar_gerados;
		$data['comandos']['listar_gerados_sem_email'] = $listar_gerados_sem_email;
		
		$data['participantes_sem_email'] = $participantes_sem_email;

		$this->load->view("sintae/contribuicao/primeiro", $data);
	}

	/**
	 * Contribuição NORMAL - Mensal
	 * @return unknown_type
	 */
	function mensal()
	{
		$data = array();
		$this->load->view("sintae/contribuicao/mensal", $data);
	}

	function atrasada()
	{
	}

	function relatorio()
	{
	}

	function email()
	{
	}
}