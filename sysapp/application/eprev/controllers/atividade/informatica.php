<?php
class informatica extends Controller
{
	function __construct()
	{
		parent::Controller();
	}

	function index()
	{
		redirect('atividade/minhas');
	}

	function solicitacao($cd=0)
	{
		$this->load->model( "projetos/Atividades_model" );

		$row=array();
		$msg=array();
		if($this->Atividades_model->solicitacao($cd, $row, $msg))
		{
			if($row) $data['row'] = $row;
			$this->load->view('atividade/informatica/solicitacao', $data);
		}
		else
		{
			exibir_mensagem("Algum problema ao carregar atividade, tente novamente em alguns instantes.");
		}
	}

	function salvar_solicitacao()
	{
		CheckLogin();

		$incluir=TRUE;

		$template_email_solicitacao="Foi enviada uma solicitação de {TIPO_SOLICITACAO_DESCRICAO}

Solicitante: {SOLICITANTE}
Atendente: {ATENDENTE}
Atividade: {NUMERO}
Situação: {SITUACAO}
-------------------------------------------------------------
Descrição:
{DESCRICAO}

-------------------------------------------------------------
Justificativa: 
{JUSTIFICATIVA}

-------------------------------------------------------------
Emp/Re/Seq: {PARTICIPANTE_RE}
Nome: {PARTICIPANTE_NOME}
Endereço: {ENDERECO} - {BAIRRO} - {CEP} - {CIDADE} - {UF}
Telefone: {DDD} - {TELEFONE}
Email: {EMAIL} / {EMAIL_PROFISSIONAL}

Plano: {PLANO}
Solicitante: {TIPO_SOLICITANTE}
Forma de solicitação: {FORMA_SOLICITACAO}
Forma de Envio: {FORMA_ENVIO}
Protocolo de atendimento: {CD_ATENDIMENTO}

------------------------------------------------------------- 
Link para Atividade:
https://www.e-prev.com.br/cieprev/index.php/atividade/minhas/detalhe/{NUMERO}
-------------------------------------------------------------
Data Limite: {DATA_LIMITE}
-------------------------------------------------------------

Mensagem enviada pelo Controle de Atividades";

		$codigo=$this->input->post('numero', TRUE);

		$args["area"] = $this->input->post("area",TRUE);
		$args["cod_solicitante"] = $this->input->post("cod_solicitante",TRUE);
		$args["tipo_solicitacao"] = $this->input->post("tipo_solicitacao",TRUE);
		$args["tipo"] = $this->input->post("tipo",TRUE);
		$args["cd_recorrente"] = $this->input->post("cd_recorrente",TRUE);
		$args["titulo"] = $this->input->post("titulo",TRUE);
		$args["descricao"] = $this->input->post("descricao",TRUE);
		$args["problema"] = $this->input->post("problema",TRUE);
		$args["divisao"] = $this->input->post("divisao",TRUE);
		$args["cod_atendente"] = $this->input->post("cod_atendente",TRUE);
		$args["dt_limite"] = $this->input->post("dt_limite",TRUE);

		$args["cd_empresa"] = $this->input->post("cd_empresa",TRUE);
		$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado",TRUE);
		$args["cd_sequencia"] = $this->input->post("seq_dependencia",TRUE);
		$args["cd_plano"] = $this->input->post("cd_plano",TRUE);
		$args["solicitante"] = $this->input->post("solicitante",TRUE);
		$args["forma"] = $this->input->post("forma",TRUE);
		$args["tp_envio"] = $this->input->post("tp_envio",TRUE);
		$args["cd_atendimento"] = $this->input->post("cd_atendimento",TRUE);

		if($args["dt_limite"]!='')
		{
			$dt_limite="TO_DATE('{dt_limite}', 'DD/MM/YYYY') ";
		}
		else
		{
			$dt_limite="null";
		}

		$status_atual='';

		// CONSTRAINTS
		$constraint_fail=false;
		if( intval($args["cd_atendimento"])>0 )
		{
			$constraint[] = $this->db->query( "SELECT count(*) as q FROM projetos.atendimento WHERE cd_atendimento=".intval( $args['cd_atendimento'] ) )->row_array();

			if( intval($constraint[0]['q'])==0 )
			{
				exibir_mensagem( "Você informou um Protocolo de atendimento que não existe na base de dados, verifique o número digitado e tente novamente." );
				$constraint_fail=true;
			}
		}

		if( !$constraint_fail )
		{
			if(intval($codigo)==0)
			{
				$incluir=true;
				$codigo = $this->db->get_new_id("projetos.atividades", "numero");

				$sql="
				INSERT INTO projetos.atividades 
				(
				numero
				, dt_cad
				, area 
				, divisao
				, cod_solicitante 
				, tipo_solicitacao 
				, tipo 
				, cd_recorrente 
				, titulo 
				, descricao 
				, problema 
				, cod_atendente 
				, dt_limite
				, status_atual

				, cd_empresa
				, cd_registro_empregado
				, cd_sequencia
				, cd_plano
				, solicitante
				, forma
				, tp_envio
				, cd_atendimento
				)
				VALUES
				( 
				{numero}
				, current_timestamp
				, '{area}'
				, '{divisao}'
				, {cod_solicitante} 
				, '{tipo_solicitacao}' 
				, '{tipo}' 
				, '{cd_recorrente}' 
				, '{titulo}' 
				, '{descricao}' 
				, '{problema}' 
				, {cod_atendente}
				, $dt_limite
				, '{status_atual}'

				, {cd_empresa}
				, {cd_registro_empregado}
				, {cd_sequencia}
				, {cd_plano}
				, '{solicitante}'
				, '{forma}'
				, {tp_envio}
				, {cd_atendimento}
				)
				";

				$status_atual='AINI'; // AGUARDANDO INICIO
			}
			else
			{
				$incluir=false;
				$sql="
				UPDATE projetos.atividades 
				
				SET area = '{area}'
				, divisao = '{divisao}'
				, cod_solicitante = {cod_solicitante} 
				, tipo_solicitacao = '{tipo_solicitacao}' 
				, tipo = '{tipo}' 
				, cd_recorrente = '{cd_recorrente}' 
				, titulo = '{titulo}' 
				, descricao = '{descricao}' 
				, problema = '{problema}' 
				, cod_atendente = {cod_atendente} 
				, dt_limite = $dt_limite

				, cd_empresa = {cd_empresa}
				, cd_registro_empregado = {cd_registro_empregado}
				, cd_sequencia = {cd_sequencia}
				, cd_plano = {cd_plano}
				, solicitante = '{solicitante}'
				, forma = '{forma}'
				, tp_envio = {tp_envio}
				, cd_atendimento = {cd_atendimento}
				
				WHERE numero = {numero} 
				";
			}

			esc("{area}", $args["area"], $sql, "str", FALSE);
			esc("{divisao}", $args["divisao"], $sql, "str", FALSE);
			esc("{cod_solicitante}", $args["cod_solicitante"], $sql, "int", FALSE);
			esc("{tipo_solicitacao}", $args["tipo_solicitacao"], $sql, "str", FALSE);
			esc("{tipo}", $args["tipo"], $sql, "str", FALSE);
			esc("{cd_recorrente}", $args["cd_recorrente"], $sql, "str", FALSE);
			esc("{titulo}", $args["titulo"], $sql, "str", FALSE);
			esc("{descricao}", $args["descricao"], $sql, "str", FALSE);
			esc("{problema}", $args["problema"], $sql, "str", FALSE);
			esc("{cod_atendente}", $args["cod_atendente"], $sql, "int", FALSE);
			esc("{dt_limite}", $args["dt_limite"], $sql, "str", FALSE);

			esc("{status_atual}", $status_atual, $sql, "str", FALSE);
			esc("{numero}", $codigo, $sql, "int", FALSE);

			if($args['cd_empresa']==''){$args['cd_empresa']='null';}
			esc("{cd_empresa}", $args['cd_empresa'], $sql, "str", FALSE);

			if($args['cd_registro_empregado']==''){$args['cd_registro_empregado']='null';}
			esc("{cd_registro_empregado}", $args['cd_registro_empregado'], $sql, "str", FALSE);

			if($args['cd_sequencia']==''){$args['cd_sequencia']='null';}
			esc("{cd_sequencia}", $args['cd_sequencia'], $sql, "str", FALSE);

			if($args['cd_plano']==''){$args['cd_plano']='null';}
			esc("{cd_plano}", $args['cd_plano'], $sql, "str", FALSE);

			if($args['cd_atendimento']==''){$args['cd_atendimento']='null';}
			esc("{cd_atendimento}", $args['cd_atendimento'], $sql, "str", FALSE);

			esc("{solicitante}", $args['solicitante'], $sql, "str", FALSE);
			esc("{forma}", $args['forma'], $sql, "str", FALSE);
			esc("{tp_envio}", $args['tp_envio'], $sql, "int", FALSE);

			$query = $this->db->query($sql);

			if($query)
			{
				$this->load->model("projetos/Atividades_model");
				$atividade = $this->Atividades_model->carregar( intval($codigo) );

				if(sizeof($atividade))
				{
					// ASSUNTO
					if($incluir)
					{
						$email['assunto'] = 'Nova atividade solicitada - nº '.$codigo;
					}
					else
					{
						$email['assunto'] = 'Atividade Alterada - nº '.$codigo;
					}

					// REMETENTE
					$email['de'] = 'Controle de Atividades (Solicitado pela GI)';

					// DESTINO
					$email['para'] = $atividade['usuario_atendente']."@eletroceee.com.br";
					$email['cc'] = $atividade['usuario_solicitante']."@eletroceee.com.br";

					// MENSAGEM
					$mensagem = $template_email_solicitacao;
					$mensagem = str_replace( "{TIPO_SOLICITACAO_DESCRICAO}", $atividade['tipo_solicitacao'], $mensagem );
					$mensagem = str_replace( "{SOLICITANTE}", $atividade['nome_solicitante'], $mensagem );
					$mensagem = str_replace( "{ATENDENTE}", $atividade['nome_atendente'], $mensagem );
					$mensagem = str_replace( "{NUMERO}", $codigo, $mensagem );
					$mensagem = str_replace( "{SITUACAO}", $atividade['status_atual_descricao'], $mensagem );
					$mensagem = str_replace( "{DESCRICAO}", $args["descricao"], $mensagem );
					$mensagem = str_replace( "{JUSTIFICATIVA}", $args["problema"], $mensagem );
					$mensagem = str_replace( "{PARTICIPANTE_RE}", $atividade['cd_empresa'].'/'.$atividade['cd_registro_empregado'].'/'.$atividade['cd_sequencia'], $mensagem );
					$mensagem = str_replace( "{PARTICIPANTE_NOME}", $atividade['participante_nome'], $mensagem );
					$mensagem = str_replace( "{ENDERECO}", $atividade['logradouro'], $mensagem );
					$mensagem = str_replace( "{BAIRRO}", $atividade['bairro'], $mensagem );
					$mensagem = str_replace( "{CEP}", $atividade['cep'].'-'.$atividade['complemento_cep'], $mensagem );
					$mensagem = str_replace( "{CIDADE}", $atividade['cidade'], $mensagem );
					$mensagem = str_replace( "{UF}", $atividade['unidade_federativa'], $mensagem );
					$mensagem = str_replace( "{DDD}", $atividade['ddd'], $mensagem );
					$mensagem = str_replace( "{TELEFONE}", $atividade['telefone'], $mensagem );
					$mensagem = str_replace( "{EMAIL}", $atividade['email'], $mensagem );
					$mensagem = str_replace( "{EMAIL_PROFISSIONAL}", $atividade['email_profissional'], $mensagem );
					$mensagem = str_replace( "{PLANO}", $atividade['plano_nome'], $mensagem );
					$mensagem = str_replace( "{TIPO_SOLICITANTE}", $atividade['tipo_solicitante_descricao'], $mensagem );
					$mensagem = str_replace( "{FORMA_SOLICITACAO}", $atividade['forma_solicitacao_descricao'], $mensagem );

					$forma_envio='';
					if(intval($atividade['tp_envio'])==1){ $forma_envio='Correio'; }
					if(intval($atividade['tp_envio'])==2){ $forma_envio='Central de atendimento'; }
					if(intval($atividade['tp_envio'])==3){ $forma_envio='Email'; }
					$mensagem = str_replace( "{FORMA_ENVIO}", $forma_envio, $mensagem );

					$mensagem = str_replace( "{CD_ATENDIMENTO}", $atividade['cd_atendimento'], $mensagem );
					$mensagem = str_replace( "{DATA_LIMITE}", $args["dt_limite"], $mensagem );

					$email['mensagem'] = $mensagem;

					if( ! enviar_email($email) )
					{
						log_save("DEBUG", "Falha no envio de email: <pre style='text-align:left;'>".$mensagem."</pre>" );
					}
				}
				else
				{
					log_save("AVISO", "Email não enviado na alteração da solicitação da atividade #".$codigo."# pois ocorreu algo inesperado na consulta as informações dessa atividade." );
				}

				redirect( "atividade/informatica/solicitacao/" . $codigo, "refresh" );
			}
			else
			{
				exibir_mensagem("Ocorreu algum erro ao tentar salvar essa atividade.");
			}
		}
	}

	function atendimento($cd=0)
	{
		$this->load->model("projetos/Atividades_model");
		$row=array();
		$msg=array();

		$this->Atividades_model->atendimento($cd,$row,$msg);
		if($row) $data['row'] = $row;

		$msg=array();
		$tarefas=array();
		$this->Atividades_model->listar_tarefas_da_atividade($cd, $tarefas,$msg);

		$data['tarefas']=$tarefas;

		// Quantidade de tarefas não concluídas
		$query = $this->db->query( "SELECT
			  (SELECT COUNT(*) FROM projetos.tarefas t WHERE cd_atividade=".intval($cd)." AND status_atual not in ('CONC')) as quantas_nao_concluidas
			 	/*,(SELECT COUNT(*) FROM projetos.tarefa_historico t1 WHERE cd_atividade=".intval($cd)." AND status_atual not in ('CONC')) as quantas_concluidas_historico*/
			 ,(SELECT COUNT(*) FROM projetos.tarefas t2 WHERE cd_atividade=".intval($cd)." AND status_atual='EMAN') as quantas_em_manutencao
			 	/*,(SELECT COUNT(*) FROM projetos.tarefa_historico t3 WHERE cd_atividade=".intval($cd)." AND status_atual='EMAN') as quantas_em_manutencao_historico*/
		 " )->row_array();

		$data['alguma_tarefa_nao_concluida'] = ( intval($query['quantas_nao_concluidas'])>0 );
		$data['alguma_tarefa_em_manutencao'] = ( intval($query['quantas_em_manutencao'])>0 );

		/*if($data['alguma_tarefa_nao_concluida']) {echo '1 true';}
		echo '<br />';
		if($data['alguma_tarefa_em_manutencao']) {echo '2 true';}
		echo '<br />';*/

		$this->load->view('atividade/informatica/atendimento', $data);
	}

	function salvar_atendimento()
	{
		CheckLogin();

		$codigo=$this->input->post('numero', TRUE);

		if( intval($codigo)>0 ) //codigo
		{
			$args["sistema"] = $this->input->post("sistema", TRUE);
			$args["status_atual"] = $this->input->post("status_atual", TRUE);

			$args["fl_teste_relevante"] = $this->input->post("fl_teste_relevante", TRUE);
			$args["dt_limite_testes"] = $this->input->post("dt_limite_testes", TRUE);
			$args["cod_testador"] = $this->input->post("cod_testador", TRUE);
			$args["dt_inicio_real"] = $this->input->post("dt_inicio_real", TRUE);
			$args["dt_fim_real"] = $this->input->post("dt_fim_real", TRUE);
			$args["solucao"] = $this->input->post("solucao", TRUE);
			$args["complexidade"] = $this->input->post("complexidade", TRUE);
			$args["numero_dias"] = $this->input->post("numero_dias", TRUE);
			$args["periodicidade"] = $this->input->post("periodicidade", TRUE);
			$args["numero"] = $this->input->post("numero", TRUE);
			$args["dt_env_teste"] = $this->input->post("dt_env_teste", TRUE);

			if(intval($args["cod_testador"])==0)
			{
				$cod_testador = "null";
			}
			else
			{
				$cod_testador = "{cod_testador}";
			}

			if($args["dt_inicio_real"]=='')
			{
				$dt_inicio_real = "null";
			}
			else
			{
				$dt_inicio_real = " TO_DATE('{dt_inicio_real}', 'DD/MM/YYYY') ";
			}

			///////////
			//
			// Data de fim real
			//
			if ($args['status_atual'] == 'CANC' || $args['status_atual'] == 'AGDF')
			{
				// Para atividades canceladas ou aguadando definição, gravar data de fim real
				$dt_fim_real="CURRENT_TIMESTAMP";
			}
			else
			{
				// Para outros status, manter o mesmo que já está gravado
				$dt_fim_real="dt_fim_real";
			}
			//
			// Lembrar que uma vez que a data de fim real ter sido gravada, 
			// uma trigger impede qualquer update ou delete para o registro
			//
			///////////

			///////////
			// Data de limite para testes e data de envio para testes
			//
			if( in_array( $args['status_atual'], array('EANA', 'EMAN', 'AUSR', 'ADIR', 'AINI') ) )
			{
				// Status informado indica que a atividade não está em teste, deve gravar NULL
				// para as datas relacionadas a testes
				$dt_env_teste = 'null';
				$dt_limite_testes = 'null';
			}
			else
			{
				if($args['status_atual'] == 'ETES')
				{
					$dt_env_teste='CURRENT_TIMESTAMP';
				}
				else
				{
					$dt_env_teste='dt_env_teste';
				}

				if($args["dt_limite_testes"]=='')
				{
					$dt_limite_testes = "null";
				}
				else
				{
					$dt_limite_testes = " TO_DATE('{dt_limite_testes}', 'DD/MM/YYYY') ";
				}
			}
			//
			///////////

			// Antes de gravar, resgata o STATUS_ATUAL para verificar se foi alterado
			$status_atual_row = $this->db->query( "SELECT status_atual FROM projetos.atividades WHERE numero = ?", array(intval($codigo)) )->row_array();
			$status_atual_antes=$status_atual_row['status_atual'];

			$sql="
			UPDATE projetos.atividades

			SET sistema = {sistema}
			, status_atual = '{status_atual}'
			, dt_env_teste = $dt_env_teste
			, fl_teste_relevante = '{fl_teste_relevante}'
			, dt_limite_testes = $dt_limite_testes
			, cod_testador = $cod_testador
			, dt_inicio_real = $dt_inicio_real
			, dt_fim_real = $dt_fim_real
			, solucao = '{solucao}'
			, complexidade = '{complexidade}'
			, numero_dias = {numero_dias}
			, periodicidade = {periodicidade}

			WHERE numero = {numero}
			";

			esc("{sistema}", $args["sistema"], $sql, "int", FALSE);
			esc("{status_atual}", $args["status_atual"], $sql, "str", FALSE);
			esc("{fl_teste_relevante}", $args["fl_teste_relevante"], $sql, "str", FALSE);
			esc("{dt_limite_testes}", $args["dt_limite_testes"], $sql, "str", FALSE);
			esc("{cod_testador}", $args["cod_testador"], $sql, "int", FALSE);
			esc("{dt_inicio_real}", $args["dt_inicio_real"], $sql, "str", FALSE);
			esc("{dt_fim_real}", $args["dt_fim_real"], $sql, "str", FALSE);
			esc("{solucao}", $args["solucao"], $sql, "str", FALSE);
			esc("{complexidade}", $args["complexidade"], $sql, "str", FALSE);
			esc("{numero_dias}", $args["numero_dias"], $sql, "int", FALSE);
			esc("{periodicidade}", $args["periodicidade"], $sql, "int", FALSE);
			esc("{numero}", $args["numero"], $sql, "int", FALSE);

			$query = $this->db->query($sql);

			// TRATAMENTO DAS TAREFAS
			$fl_tipo=0;
			if( $args['status_atual']=='SUSP' )
			{
				$fl_tipo=1;
				$tarefas = $this->db->query(" SELECT cd_tarefa, cd_recurso FROM projetos.tarefas WHERE cd_atividade = ".intval($args['numero'])." AND status_atual <> 'CONC' ");

				if($tarefas)
				{
					$tarefas=$tarefas->result_array();

					foreach($tarefas as $tarefa)
					{
						$this->db->query( "UPDATE projetos.tarefas SET status_atual = 'SUSP' WHERE cd_atividade = ".$args['numero']." AND cd_tarefa    = ".$tarefa['cd_tarefa'].";INSERT INTO projetos.tarefa_historico( cd_atividade, cd_tarefa, cd_recurso, timestamp_alteracao, descricao, status_atual ) VALUES ( ".$args['numero'].", ".$tarefa['cd_tarefa'].", ".$tarefa['cd_recurso'].", current_timestamp, 'Atividade suspensa.', 'SUSP' );" );
					}
				}
			}

			if( $args['status_atual']=='CANC' )
			{
				$fl_tipo=1;
				$tarefas = $this->db->query(" SELECT cd_tarefa,cd_recurso FROM projetos.tarefas WHERE cd_atividade = ".intval($args['numero'])." AND status_atual <> 'CONC'; ");

				if($tarefas)
				{
					$tarefas=$tarefas->result_array();

					foreach($tarefas as $tarefa)
					{
						$this->db->query( "UPDATE projetos.tarefas SET status_atual = 'CANC' WHERE cd_atividade = " . $args['numero'] . " AND cd_tarefa = " . $tarefa['cd_tarefa'] . ";INSERT INTO projetos.tarefa_historico ( cd_atividade, cd_tarefa, cd_recurso, timestamp_alteracao, descricao, status_atual ) VALUES ( " . $args['numero'] . ", " . $tarefa['cd_tarefa'] . ", " . $tarefa['cd_recurso'] . ", current_timestamp, 'Atividade Cancelada.', 'CANC' );" );
					}
				}
			}

			if( $args['status_atual']=='AGDF' )
			{
				$fl_tipo=1;
				$tarefas = $this->db->query(" SELECT cd_tarefa, cd_recurso FROM projetos.tarefas WHERE cd_atividade = ".intval($args['numero'])." AND status_atual <> 'CONC'; ");

				if($tarefas)
				{
					$tarefas=$tarefas->result_array();

					foreach($tarefas as $tarefa)
					{
						$this->db->query( "UPDATE projetos.tarefas SET status_atual = 'AGDF' WHERE cd_atividade = ".intval($args['numero'])." AND cd_tarefa    = ".$tarefa['cd_tarefa']."; INSERT INTO projetos.tarefa_historico ( cd_atividade, cd_tarefa, cd_recurso, timestamp_alteracao, descricao, status_atual ) VALUES ( ".intval($args['numero']).", ".$tarefa['cd_tarefa'].", ".$tarefa['cd_recurso'].", CURRENT_TIMESTAMP, 'Atividade Aguardando definição.', 'AGDF' ); " );
					}
				}
			}

			if( $args['status_atual']=='ADIR' )
			{
				$fl_tipo=1;
				$tarefas = $this->db->query( "SELECT cd_tarefa,cd_recurso FROM projetos.tarefas WHERE cd_atividade = ".intval($args['numero'])." AND status_atual IN ('EMAN','SUSP') " );

				if($tarefas)
				{
					$tarefas=$tarefas->result_array();

					foreach($tarefas as $tarefa)
					{
						$this->db->query( "UPDATE projetos.tarefas SET status_atual='SUSP' WHERE cd_atividade = ".intval($args['numero'])." AND cd_tarefa = ".$tarefa['cd_tarefa']."; INSERT INTO projetos.tarefa_historico ( cd_atividade, cd_tarefa, cd_recurso, timestamp_alteracao, descricao, status_atual ) VALUES ( ".intval($args['numero']).", ".$tarefa['cd_tarefa'].", ".$tarefa['cd_recurso'].", current_timestamp, 'Atividade Aguardando diretoria.', 'SUSP');" );
					}
				}
			}

			if( $args['status_atual']=='AUSR' )
			{
				$fl_tipo=1;
				$tarefas = $this->db->query( "SELECT cd_tarefa, cd_recurso FROM projetos.tarefas WHERE cd_atividade = " . intval( $args["numero"] ) . " AND status_atual IN ('EMAN','SUSP') " );

				if($tarefas)
				{
					$tarefas=$tarefas->result_array();

					foreach($tarefas as $tarefa)
					{
						$this->db->query( "UPDATE projetos.tarefas SET status_atual = 'SUSP' WHERE cd_atividade = " . intval( $args["numero"] ) . " AND cd_tarefa = " . intval($tarefa['cd_tarefa']) . ";INSERT INTO projetos.tarefa_historico ( cd_atividade, cd_tarefa,cd_recurso,timestamp_alteracao,descricao,status_atual) VALUES ( " . intval( $args["numero"] ) . "," . intval( $tarefa['cd_tarefa'] ) . "," . intval( $tarefa['cd_recurso'] ) . ",CURRENT_TIMESTAMP,'Atividade Aguardando usuário.','SUSP');" );
					}
				}
			}

			if( true or trim($status_atual_antes)!=trim($args['status_atual']) ) //status_diferente
			{
				$sql = "INSERT INTO projetos.atividade_historico 
					( 
						cd_atividade
						, cd_recurso
						, dt_inicio_prev
						, status_atual
						, observacoes 
					)
					VALUES 
					(
						{cd_atividade}
						, {cd_recurso}
						, CURRENT_TIMESTAMP
						, '{status_atual}'
						, 'Troca de Status'
				)";

				esc( '{cd_atividade}', $codigo, $sql, 'int' );
				esc( '{cd_recurso}', usuario_id(), $sql, 'int' );
				esc( '{status_atual}', $args['status_atual'], $sql, 'str' );

				$query = $this->db->query($sql);

				// Envia Email
				$template='{MENSAGEM}
Solicitante: {SOLICITANTE}
Atendente: {ATENDENTE}
Atividade: {NUMERO}
Situação: {SITUACAO}
-------------------------------------------------------------
DATA LIMITE PARA TESTES: {DATA_LIMITE_TESTE}
Testador: {TESTADOR}
-------------------------------------------------------------
Descrição: 
{DESCRICAO}
-------------------------------------------------------------
Link para Atividade: 
https://www.e-prev.com.br/cieprev/index.php/atividade/minha/detalhe/{NUMERO}
-------------------------------------------------------------
Justificativa da Manutenção: 
{JUSTIFICATIVA}
-------------------------------------------------------------
Descrição da Manutenção: 
{DESCRICAO_MANUTENCAO}
-------------------------------------------------------------
Observações: 
{OBSERVACAO}
-------------------------------------------------------------
Esta mensagem foi enviada pelo Controle de Atividades.';

				$this->load->model("projetos/Atividades_model");
				$atividade = $this->Atividades_model->carregar( intval($codigo) );

				if(sizeof($atividade)) //atividades
				{
					// ASSUNTO
					if($args['status_atual']=='CONC')
					{
						$email['assunto'] = "(".strtoupper($atividade['status_atual_descricao']).") A seguinte atividade foi concluída: nº ".$codigo;
					}
					else
					{
						$email['assunto'] = "(".strtoupper($atividade['status_atual_descricao']).") Alteração de Situação da Atividade nº ".$codigo;
					}

					// REMETENTE
					$email['de'] = "Controle de Atividades (Solicitado pela ".$atividade['divisao'].")";

					// DESTINO
					$email['para'] = $atividade['usuario_atendente']."@eletroceee.com.br";
					$email['para'] .= ";".$atividade['usuario_solicitante']."@eletroceee.com.br";
					if(trim($atividade['usuario_testador'])!='')
					{
						$email['para'] .= ";". $atividade['usuario_testador']."@eletroceee.com.br";
					}
					$email['cc'] = "";

					// MENSAGEM
					$mensagem = $template;
					if( $args['status_atual']=='AINI' )
					{
						$mensagem = str_replace( "{MENSAGEM}", "Foi enviada uma solicitação", $mensagem );
					}
					else
					{
						$mensagem = str_replace( "{MENSAGEM}", "Alteração de status da atividade", $mensagem );
					}
					$mensagem = str_replace( "{SOLICITANTE}", $atividade['nome_solicitante'], $mensagem );
					$mensagem = str_replace( "{ATENDENTE}", $atividade['nome_atendente'], $mensagem );
					$mensagem = str_replace( "{NUMERO}", $codigo, $mensagem );
					$mensagem = str_replace( "{SITUACAO}", $atividade['status_atual_descricao'], $mensagem );
					$mensagem = str_replace( "{DATA_LIMITE_TESTE}", $atividade["data_limite_teste"], $mensagem );
					$mensagem = str_replace( "{TESTADOR}", $atividade["nome_testador"], $mensagem );
					$mensagem = str_replace( "{DESCRICAO}", $atividade["descricao"], $mensagem );
					$mensagem = str_replace( "{JUSTIFICATIVA}", $atividade["problema"], $mensagem );
					$mensagem = str_replace( "{DESCRICAO_MANUTENCAO}", $atividade["solucao"], $mensagem );
					$mensagem = str_replace( "{OBSERVACAO}", $atividade["observacoes"], $mensagem );

					$email['mensagem'] = $mensagem;

					if( ! enviar_email($email) )
					{
						log_save("DEBUG", "Falha no envio de email: <pre style='text-align:left;'>".$mensagem."</pre>" );
					}

					// Envia email da troca de status para as tarefas

					$sql = "SELECT t.cd_atividade as atividade,
						   t.cd_tarefa as tarefa,		
						   u.guerra as executor, 
						   t.descricao as t_descricao, 
						   t.programa as programa, 
						   u.usuario as usuario,
						  (SELECT l.descricao
							 FROM projetos.atividades a,
								  listas l
							WHERE a.numero       = t.cd_atividade
							  AND a.status_atual = l.codigo
							  AND a.divisao      = l.divisao
							  AND categoria      = 'STAT') as st_atividade,
						   CASE WHEN (h.status_atual='AMAN') THEN 'Aguardando Manutenção' 
								WHEN (h.status_atual='EMAN') THEN 'Em Manutenção' 
								WHEN (h.status_atual='LIBE') THEN 'Liberada' 
								WHEN (h.status_atual='CANC') THEN 'Cancelada'
								WHEN (h.status_atual='AGDF') THEN 'Aguardando Definição'
								WHEN (h.status_atual='SUSP' AND (SELECT status_atual FROM projetos.atividades WHERE numero = t.cd_atividade)='ADIR') THEN 'Atividade Aguardando Diretoria'
								WHEN (h.status_atual='SUSP' AND (SELECT status_atual FROM projetos.atividades WHERE numero = t.cd_atividade)='AUSR') THEN 'Atividade Aguardando Usuário'
								WHEN (h.status_atual='SUSP' AND (SELECT status_atual FROM projetos.atividades WHERE numero = t.cd_atividade)='SUSP') THEN 'Atividade Suspensa'
								WHEN (h.status_atual='SUSP') THEN 'Em Manutenção (Pausa)'
						   END as status,
						   h.descricao AS historico					   
					  FROM projetos.tarefas t, 
						   projetos.usuarios_controledi u,
						   projetos.tarefa_historico h 					   
					 WHERE t.cd_atividade = {cd_atividade}
					   AND t.cd_recurso   = u.codigo 
					   AND t.cd_atividade = h.cd_atividade
					   AND t.cd_tarefa    = h.cd_tarefa
					   AND t.cd_recurso   = h.cd_recurso
					   AND h.timestamp_alteracao = (SELECT MAX(timestamp_alteracao)
													  FROM projetos.tarefa_historico
													 WHERE cd_atividade = h.cd_atividade
													   AND cd_tarefa    = h.cd_tarefa
													   AND cd_recurso   = h.cd_recurso)				   
					   AND t.status_atual <> 'CONC'
					   AND t.dt_encaminhamento IS NOT NULL
					   AND t.dt_exclusao IS NULL";
					esc("{cd_atividade}", $codigo, $sql, "int");

					//echo '<pre>'.$sql.'</pre>';
					$query=$this->db->query($sql);
					if($query) //tarefas1
					{
						$tarefas=$query->result_array();

						if($tarefas) //tarefas2
						{
							foreach( $tarefas as $tarefa )
							{
								$email=array();
								$email['para'] = $tarefa['usuario'].'@eletroceee.com.br';
								$email['de'] = "Controle de Atividades e Tarefas (GI)";
								$email['assunto'] = "Alteração de Situação da Atividade/Tarefa - nº ".$codigo."/" .$tarefa['tarefa'];	
								$email['mensagem'] = "Prezado(a) ".$tarefa["executor"]."
A tarefa teve seu status alterado pela Atividade:
-------------------------------------------------------------
Tarefa: ".$tarefa['tarefa'].", Atividade: ".$tarefa['atividade']."
-------------------------------------------------------------
Status atual da Atividade: ".$tarefa['st_atividade']."
-------------------------------------------------------------
Status atual da Tarefa: ".$tarefa['status']."
-------------------------------------------------------------
Histórico: ".$tarefa['historico']."
-------------------------------------------------------------
Descrição: ".$tarefa['t_descricao']."
-------------------------------------------------------------";

								if($fl_tipo==0)
								{
									$email['mensagem'] .= "
A presente tarefa deve ser iniciada imediatamente!
-------------------------------------------------------------
";
								}
								elseif($fl_tipo==1)
								{
									$email['mensagem'] .= "
Aguarde alteração de status da Atividade, para dar continuidade a Tarefa.
-------------------------------------------------------------
";
								}

								if( ! enviar_email($email) )
								{
									// echo "falha ao tentar enviar email";
									log_save("DEBUG", "Falha no envio de email de tarefa: <pre style='text-align:left;'>".$email['mensagem']."</pre>" );
								}
							}

						} //tarefas2

					} //tarefas1

				} //atividades

			} //status_diferente

		} //codigo

		if($args['status_atual']=='ETES')
		{
			redirect( "atividade/minhas", "refresh" );
		}
		else
		{
			redirect( "atividade/informatica/atendimento/".$args['numero'], "refresh" );
		}
	}

	function anexo($cd=0)
	{
		CheckLogin();

		if(intval($cd)==0) // código não informado
		{
			echo exibir_mensagem( "Código da atividade deve ser informada" );
		}
		else
		{
			$sql = "SELECT * FROM projetos.atividades";
			$data=array();
			$row=array();
			$query = $this->db->query( $sql . ' LIMIT 1 ' );
			$fields = $query->field_data();
			foreach( $fields as $field )
			{
				$row[$field->name] = '';
			}

			if( intval($cd)>0 )
			{
				$sql .= " WHERE numero={numero} ";
				esc( "{numero}", intval($cd), $sql );
				$query=$this->db->query($sql);
				$row=$query->row_array();
			}

			if($row){ $data['row']=$row; }

			$sql="
			SELECT
			cd_atividade
			, cd_anexo
			, tipo_anexo
			, tam_arquivo
			, TO_CHAR(dt_upload, 'DD/MM/YYYY') as dt_upload
			, nome_arquivo
			, caminho
			FROM projetos.anexos_atividades
			WHERE cd_atividade = ".intval($cd)."
			";
			$query=$this->db->query($sql);

			if($query){ $data['anexos']=$query->result_array(); } else { $data['anexos']=array(); }

			$this->load->view("atividade/informatica/anexo", $data);
		}
	}

	function anexo_excluir()
	{
		CheckLogin();

		$cd_atividade=$this->input->post('cd_atividade');
		$cd_anexo=$this->input->post('cd_anexo');

		$sql="DELETE FROM projetos.anexos_atividades WHERE MD5(TRIM(cd_atividade::varchar))='{cd_atividade}' AND md5(TRIM(cd_anexo::varchar))='{cd_anexo}'";
		esc( "{cd_atividade}", $cd_atividade, $sql );
		esc( "{cd_anexo}", $cd_anexo, $sql );

		$query=$this->db->query($sql);

		if($query)
		{
			echo "true";
		}
		else
		{
			echo "Problemas ao tentar excluir anexo.";
		}
	}

	function anexo_salvar()
	{
		CheckLogin();

		$cd_atividade = intval($this->input->post("numero"));

		$config['upload_path'] = './up/atividade_anexo/';
		$config['allowed_types'] = "frx|bmp|con|doc|ffa|fmx|gif|htm|jpg|lst|pck|pdf|png|ppt|prc|rtf|sql|tab|tif|trc|txt|wav|xls|zip";
		$config['encrypt_name'] = FALSE;
		$this->load->library('upload', $config);

		if($this->upload->do_upload("nome_arquivo"))
		{
			$file = array('upload_data' => $this->upload->data());

			$tipo_anexo = "file";
			$tam_arquivo = intval($file['upload_data']['file_size']);
			$nome_arquivo = $file['upload_data']['file_name'];
			$caminho = "/";

			$sql = "INSERT INTO projetos.anexos_atividades( cd_atividade, cd_anexo, tipo_anexo, tam_arquivo, dt_upload, nome_arquivo, caminho ) VALUES ( {cd_atividade}, (select max(cd_anexo) FROM projetos.anexos_atividades WHERE cd_atividade={cd_atividade})+1, '{tipo_anexo}', {tam_arquivo}, current_date, '{nome_arquivo}', '{caminho}' );";

			esc( "{cd_atividade}", $cd_atividade, $sql, 'int' );
			esc( "{tipo_anexo}", $tipo_anexo, $sql, 'str' );
			esc( "{tam_arquivo}", $tam_arquivo, $sql, 'int' );
			esc( "{nome_arquivo}", $nome_arquivo, $sql, 'str' );
			esc( "{caminho}", $caminho, $sql, 'str' );

			$query = $this->db->query( $sql );

			if($query)
			{
				redirect( "atividade/informatica/anexo/".intval($cd_atividade), 'refresh' );
			}
			else
			{
				echo "Ocorreu algum erro!";
			}
		}
		else
		{
			$error = array('error' => $this->upload->display_errors());
			echo '<pre>';
			var_dump($error);
			echo '</pre>';
			exit;
		}
	}

	function historico($cd=0)
	{
		CheckLogin();

		if(intval($cd)==0) // código não informado
		{
			echo exibir_mensagem( "Código da atividade deve ser informada" );
		}
		else
		{
			$sql = "SELECT * FROM projetos.atividades";
			$data=array();
			$row=array();
			$query = $this->db->query( $sql . ' LIMIT 1 ' );
			$fields = $query->field_data();
			foreach( $fields as $field )
			{
				$row[$field->name] = '';
			}

			if( intval($cd)>0 )
			{
				$sql .= " WHERE numero={numero} ";
				esc( "{numero}", intval($cd), $sql );
				$query=$this->db->query($sql);
				$row=$query->row_array();
			}

			if($row){ $data['row']=$row; }

			$sql="
				SELECT

				t.codigo AS codigo
				, u.nome AS nome
				, l.descricao AS descricao
				, t.observacoes AS obs
				, TO_CHAR(dt_inicio_prev, 'dd/mm/yyyy hh24:mi:ss') AS dt_inicio_prev

				FROM projetos.atividade_historico t 
				INNER JOIN projetos.usuarios_controledi u ON t.cd_recurso = u.codigo
				LEFT OUTER JOIN listas l ON l.codigo = t.status_atual AND l.categoria = 'STAT'

				WHERE t.cd_atividade = ".intval($cd)."

				ORDER BY t.dt_inicio_prev, t.codigo
			";
			$query=$this->db->query($sql);

			if($query){ $data['anexos']=$query->result_array(); } else { $data['anexos']=array(); }

			$this->load->view("atividade/informatica/historico", $data);
		}
	}
}