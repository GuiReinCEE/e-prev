Ext.onReady(function() {
	var atividade = new Ext.menu.Menu({ id: 'atividade', items: 
	[
	{ text: '+ Atividades' 
	, menu:{items:[
	{ text: 'Encaminhadas' 
	,handler: function(){ location.href='lst_atividades.php?TA=E' } 
	}, 
	{ text: 'Legais' 
	,handler: function(){ location.href='lst_atividade_cenario.php' } 
	}, 
	{ text: 'REd' 
	,handler: function(){ location.href='lst_atividades.php?TA=R' } 
	}, 
	{ text: 'Divis�o' 
	,handler: function(){ location.href='lst_atividades_divisao.php' } 
	}, 
	{ text: 'Solu��es do Suporte' 
	,handler: function(){ cieprev_acesso( 'suporte/solucao' ); } 
	} 

	]}}, 
	{ text: 'Minhas' 
	, menu:{items:[
	{ text: 'Atividades' 
	,handler: function(){ location.href='lst_atividades.php?TA=A' } 
	}, 
	{ text: 'Tarefas' 
	,handler: function(){ location.href='lst_minhas_tarefas.php' } 
	} 

	]}}, 
	{ text: 'Nova Atividade' 
	, menu:{items:[
	{ text: 'Inform�tica' 
	,handler: function(){ location.href='cad_atividade_solic.php?aa=GI' } 
	}, 
	{ text: 'Suporte' 
	,handler: function(){ location.href='cad_atividade_solic.php?aa=GI&tm=s' } 
	}, 
	{ text: 'Atendimento' 
	,handler: function(){ location.href='cad_atividade_solic.php?aa=GAP' } 
	}, 
	{ text: 'Benef�cios' 
	,handler: function(){ location.href='cad_atividade_solic.php?aa=GB' } 
	}, 
	{ text: 'Financeiro' 
	,handler: function(){ location.href='cad_atividade_solic.php?aa=GF' } 
	}, 
	{ text: 'Rel. Institucionais' 
	,handler: function(){ location.href='cad_atividade_solic.php?aa=GRI' } 
	}, 
	{ text: 'Controladoria' 
	,handler: function(){ location.href='cad_atividade_solic.php?aa=GC' } 
	}, 
	{ text: 'Atuarial' 
	,handler: function(){ location.href='cad_atividade_solic.php?aa=GA' } 
	}

	]}}, 
	{ text: 'Registro Operacional' 
	,handler: function(){ location.href='lst_registro_operacional_projeto.php' } 
	}, 
	{ text: 'Controles' 
	, menu:{items:[
	{ text: 'Atividades Abertas' 
	, menu:{items:[
	{ text: 'Inform�tica' 
	,handler: function(){ location.href='gra_atividades_mes.php?aa=GI' } 
	}, 
	{ text: 'Suporte' 
	,handler: function(){ location.href='gra_atividades_mes.php?aa=GI&tm=s' } 
	}, 
	{ text: 'Atendimento' 
	,handler: function(){ location.href='gra_atividades_mes.php?aa=GAP' } 
	}, 
	{ text: 'Benef�cio' 
	,handler: function(){ location.href='gra_atividades_mes.php?aa=GB' } 
	}, 
	{ text: 'Comunica��o' 
	,handler: function(){ location.href='gra_atividades_mes.php?aa=GRI' } 
	} 

	]}}, 
	{ text: 'Abertas / Atendente' 
	, menu:{items:[
	{ text: 'Inform�tica' 
	,handler: function(){ location.href='gra_atividades_atendente_mes.php?aa=GI' } 
	}, 
	{ text: 'Suporte' 
	}, 
	{ text: 'Atendimento' 
	,handler: function(){ location.href='gra_atividades_atendente_mes.php?aa=GAP' } 
	}, 
	{ text: 'Benef�cios' 
	,handler: function(){ location.href='gra_atividades_atendente_mes.php?aa=GB' } 
	}, 
	{ text: 'Comunica��o' 
	,handler: function(){ location.href='gra_atividades_atendente_mes.php?aa=GRI' } 
	} 

	]}}, 
	{ text: 'Abertas / Divis�o' 
	, menu:{items:[
	{ text: 'Inform�tica' 
	,handler: function(){ location.href='gra_atividades_divisao_mes.php?aa=GI' } 
	}, 
	{ text: 'Suporte' 
	,handler: function(){ location.href='gra_atividades_divisao_mes.php?aa=GI&tm=s' } 
	}, 
	{ text: 'Atendimento' 
	,handler: function(){ location.href='gra_atividades_divisao_mes.php?aa=GAP' } 
	}, 
	{ text: 'Benef�cio' 
	,handler: function(){ location.href='gra_atividades_divisao_mes.php?aa=GB' } 
	}, 
	{ text: 'Comunica��o' 
	,handler: function(){ location.href='gra_atividades_divisao_mes.php?aa=GRI' } 
	} 

	]}}, 
	{ text: 'Cronograma' 
	, menu:{items:[
	{ text: 'Por Atendente' 
	,handler: function(){ location.href='cronograma_atendente.php' } 
	}, 
	{ text: 'Por Programa' 
	,handler: function(){ location.href='cronograma_programa.php' } 
	}, 
	{ text: 'Projeto' 
	,handler: function(){ location.href='cronograma_projeto.php' } 
	} 

	]}}, 
	{ text: 'Desempenho' 
	, menu:{items:[
	{ text: 'Por Atendente' 
	,handler: function(){ location.href='perc_itens_no_prazo.php?sel=A' } 
	}, 
	{ text: 'Por Programa' 
	,handler: function(){ location.href='perc_itens_no_prazo.php?sel=P' } 
	}, 
	{ text: 'Projeto' 
	,handler: function(){ location.href='perc_itens_no_prazo.php?sel=S' } 
	} 

	]}}, 
	{ text: 'Gr�fico Gantt' 
	, menu:{items:[
	{ text: 'Por Atendente' 
	,handler: function(){ location.href='grafico_gantt_projeto.php?sel=A' } 
	}, 
	{ text: 'Por Programa' 
	,handler: function(){ location.href='grafico_gantt_projeto.php?sel=P' } 
	}, 
	{ text: 'Projeto' 
	,handler: function(){ location.href='grafico_gantt_projeto.php?sel=S' } 
	} 

	]}}, 
	{ text: 'Previs�o Or�am' 
	, menu:{items:[
	{ text: 'Por Atendente' 
	,handler: function(){ location.href='previsao_orcamentaria.php?sel=A' } 
	}, 
	{ text: 'Por Programa' 
	,handler: function(){ location.href='previsao_orcamentaria.php?sel=P' } 
	}, 
	{ text: 'Projeto' 
	,handler: function(){ location.href='previsao_orcamentaria.php?sel=S' } 
	} 

	]}}, 
	{ text: 'Projetos' 
	, menu:{items:[
	{ text: 'Inform�tica' 
	,handler: function(){ location.href='lst_acomp_projetos.php?aa=GI' } 
	}, 
	{ text: 'Suporte' 
	,handler: function(){ location.href='lst_acomp_projetos.php?aa=GI&tm=s' } 
	}, 
	{ text: 'Atendimento' 
	,handler: function(){ location.href='lst_acomp_projetos.php?aa=GAP' } 
	}, 
	{ text: 'Benef�cios' 
	,handler: function(){ location.href='lst_acomp_projetos.php?aa=GB' } 
	}, 
	{ text: 'Comunica��o' 
	,handler: function(){ location.href='lst_acomp_projetos.php?aa=GRI' } 
	} 

	]}}, 
	{ text: 'Tempo / Atividades' 
	, menu:{items:[
	{ text: 'Inform�tica' 
	,handler: function(){ location.href='gra_atendimentos_mes.php?aa=GI' } 
	}, 
	{ text: 'Suporte' 
	,handler: function(){ location.href='gra_atendimentos_mes.php?aa=GI&tm=s' } 
	}, 
	{ text: 'Atendimento' 
	,handler: function(){ location.href='gra_atendimentos_mes.php?aa=GAP' } 
	}, 
	{ text: 'Benef�cios' 
	,handler: function(){ location.href='gra_atendimentos_mes.php?aa=GB' } 
	}, 
	{ text: 'Comunica��o' 
	,handler: function(){ location.href='gra_atendimentos_mes.php?aa=GRI' } 
	} 

	]}}, 
	{ text: 'Relat�rios' 
	, menu:{items:[
	{ text: 'Dias e Datas Limites' 
	,handler: function(){ location.href='rel_datas_limites.php' } 
	}, 
	{ text: 'Atividade / Atendente' 
	,handler: function(){ location.href='rel_carga_servico.php' } 
	} 

	]}}, 
	{ text: 'Quadro Resumo' 
	, menu:{items:[
	{ text: 'GI - Inform�tica' 
	,handler: function(){ location.href='resumo_atividades.php' } 
	}, 
	{ text: 'GRI - Rela��es institucionais' 
	,handler: function(){ location.href='resumo_atividades_gri.php' } 
	} 

	]}} 

	]}} 

	]
	});
	var cadastro = new Ext.menu.Menu({ id: 'cadastro', items: 
	[
	{ text: 'Contratos' 
	,handler: function(){ cieprev_acesso('cadastro/contrato'); } 
	}, 
	{ text: 'Correspond�ncias' 
	, menu:{items:[
	{ text: 'Cadastro' 
	,handler: function(){ location.href='frm_correspondencias.php' } 
	}, 
	{ text: 'Recebidas' 
	,handler: function(){ location.href='frm_docs_recebidos.php' } 
	}, 
	{ text: 'Relat�rio Resumo' 
	,handler: function(){ location.href='sel_periodo_correspondencia.php' } 
	} 

	]}}, 
	{ text: 'Divis�es' 
	,handler: function(){ location.href='lst_divisoes.php' } 
	}, 



	{ text: 'Equipamentos de inform�tica' 
	, menu:{items:[
	{ text: 'Consulta' 
	,handler: function(){ location.href='lst_equipamentos.php' } 
	}, 
	{ text: 'Distribui��o F�sica' 
	,handler: function(){ location.href='edificio_sede.php' } 
	}, 
	{ text: 'Lista de equipamentos' 
	,handler: function(){ location.href='lst_equipamentos_new.php' } 
	}, 
	{ text: 'Meu computador' 
	,handler: function(){ location.href='meu_computador.php' } 
	}, 
	{ text: 'Quadro Resumo' 
	,handler: function(){ location.href='lst_equip_div.php' } 
	} 

	]}}, 
	{ text: 'Meus Relat�rios' 
	,handler: function(){ location.href='lst_meus_relatorios.php' } 
	}, 
	{ text: 'Projetos' 
	,handler: function(){ location.href='lst_projetos.php' } 
	}, 
	{ text: 'Publica��es' 
	,handler: function(){ location.href='lst_publicacoes.php' } 
	}, 
	{ text: 'Servi�os' 
	,handler: function(){ location.href='lst_servicos.php' } 
	}, 
	{ text: 'Softwares' 
	,handler: function(){ location.href='lst_programas.php' } 
	}, 
	{ text: 'Recursos Humanos' 
	, menu:{items:[
	{ text: 'Avalia��o' 
	, menu:{items:[
	{ text: 'Fam�lia cargos' 
	,handler: function(){ location.href='lst_familias.php' } 
	}, 
	{ text: 'Cargos' 
	,handler: function(){ location.href='lst_cargos.php' } 
	}, 
	{ text: 'Compet�ncias Institucionais' 
	,handler: function(){ location.href='lst_comp_inst.php' } 
	}, 
	{ text: 'Compet�ncias Espec�ficas' 
	,handler: function(){ location.href='lst_comp_espec.php' } 
	}, 
	{ text: 'Responsabilidades' 
	,handler: function(){ location.href='lst_responsabilidades.php' } 
	}, 
	{ text: 'Escolaridade' 
	,handler: function(){ location.href='lst_escolaridade.php' } 
	}, 
	{ text: 'Habilidades' 
	,handler: function(){ location.href='lst_habilidades.php' } 
	}, 
	{ text: 'Conceitos' 
	,handler: function(){ location.href='avaliacao_config.php?lbu=conceito' } 
	}, 
	{ text: 'Matriz' 
	,handler: function(){ location.href='avaliacao_config.php?lbu=matriz' } 
	}, 
	{ text: 'Comit� de Avalia��o' 
	,handler: function(){ location.href='avaliacao_config.php?lbu=nomearcomite' } 
	}, 
	{ text: 'Relat�rio' 
	,handler: function(){ location.href='avaliacao_config.php?lbu=relatorio' } 
	}, 
	{ text: 'Manuten��o' 
	,handler: function(){ location.href='avaliacao_config.php?lbu=manutencao' } 
	}, 
	{ text: 'Abertura' 
	,handler: function(){ cieprev_acesso( 'avaliacao/controle/abertura' ); } 
	} 

	]}}, 
	{ text: 'Grupo de Trabalho' 
	,handler: function(){ location.href='lst_grupos.php' } 
	}, 
	{ text: 'Pessoas' 
	,handler: function(){ location.href='lst_recursos.php' } 
	} 

	]}}, 
	{ text: 'Tarefas' 
	,handler: function(){ location.href='lst_tarefas.php' } 
	} 

	]
	});
	var ecrm = new Ext.menu.Menu({ id: 'ecrm', items: 
	[
	{ text: 'Atualiza Internet' 
	, menu:{items:[
	{ text: 'Destaques' 
	,handler: function(){ location.href='lst_destaques.php' } 
	}, 
	{ text: 'Imprensa' 
	,handler: function(){ location.href='lst_imprensa.php' } 
	}, 
	{ text: 'Indicadores' 
	,handler: function(){ location.href='lst_indicadores.php' } 
	}, 
	{ text: 'Sites Institucionais' 
	,handler: function(){ location.href='lst_sites.php' } 
	}, 
	{ text: 'Contra cheques' 
	,handler: function(){ location.href='lst_contra_cheque_planos.php' } 
	}, 
	{ text: 'Extratos' 
	,handler: function(){ location.href='lst_extratos_planos.php' } 
	} 

	]}}, 
	{ text: 'Atualiza Intranet' 
	, menu:{items:[
	{ text: 'Biblioteca Multim�dia' 
	, menu:{items:[
	{ text: 'Filmes' 
	,handler: function(){ location.href='lst_filmes.php' } 
	}, 
	{ text: 'Fotos' 
	,handler: function(){ location.href='lst_fotos.php' } 
	} 

	]}}, 
	{ text: 'DE' 
	,handler: function(){ location.href='lst_intra_div.php?div=DE' } 
	}, 
	{ text: 'GA - Atu�ria' 
	,handler: function(){ location.href='lst_intra_div.php?div=GA' } 
	}, 
	{ text: 'GAD - Administrativa' 
	,handler: function(){ location.href='lst_intra_div.php?div=GAD' } 
	}, 
	{ text: 'GAP - Atendimento' 
	,handler: function(){ location.href='lst_intra_div.php?div=GAP' } 
	}, 
	{ text: 'GB - Benef�cios' 
	,handler: function(){ location.href='lst_intra_div.php?div=GB' } 
	}, 
	{ text: 'GC - Controladoria' 
	,handler: function(){ location.href='lst_intra_div.php?div=GC' } 
	}, 
	{ text: 'GF - Financeira' 
	,handler: function(){ location.href='lst_intra_div.php?div=GF' } 
	}, 
	{ text: 'GI - Inform�tica' 
	,handler: function(){ location.href='lst_intra_div.php?div=GI' } 
	}, 
	{ text: 'GIN - Investimentos' 
	,handler: function(){ location.href='lst_intra_div.php?div=GIN' } 
	}, 
	{ text: 'GJ - Jur�dica' 
	,handler: function(){ location.href='lst_intra_div.php?div=GJ' } 
	}, 
	{ text: 'GRI - Rela��es Intitucionais' 
	,handler: function(){ location.href='lst_intra_div.php?div=GRI' } 
	}, 
	{ text: 'SG' 
	,handler: function(){ location.href='lst_intra_div.php?div=SG' } 
	}, 
	{ text: 'AELETRO' 
	,handler: function(){ location.href='lst_intra_div.php?div=AE' } 
	}, 
	{ text: 'Comit� de Qualidade' 
	,handler: function(){ location.href='lst_intra_div.php?div=CQ' } 
	}, 
	{ text: 'Comit� Previdenci�rio' 
	,handler: function(){ location.href='lst_intra_div.php?div=CP' } 
	}, 
	{ text: 'Comit� de Educa��o Ambiental' 
	,handler: function(){ location.href='lst_intra_div.php?div=CEA' } 
	}, 
	{ text: 'Coral' 
	,handler: function(){ location.href='lst_intra_div.php?div=COR' } 
	}, 
	{ text: 'Expans�o' 
	,handler: function(){ location.href='lst_intra_div.php?div=GE' } 
	} 

	]}}, 
	{ text: 'CRM Anal�tico' 
	, menu:{items:[
	{ text: 'Atendimentos' 
	,handler: function(){ location.href='atend_pessoal_analitico.php' } 
	}, 
	{ text: 'Acessos 0800' 
	,handler: function(){ location.href='acessos_call.php' } 
	}, 
	{ text: 'Acessos Internet' 
	,handler: function(){ location.href='acessos_web.php' } 
	}, 
	{ text: 'Controle de liga��es' 
	,handler: function(){ window.open('http://10.63.255.5:5800'); } 
	}, 
	{ text: 'Emails empr�stimos' 
	,handler: function(){ location.href='lst_envia_emails.php?e=EMP' } 
	}, 
	{ text: 'Emails contatos' 
	,handler: function(){ location.href='lst_envia_emails.php?e=CON' } 
	}, 
	{ text: 'Segmenta��o' 
	,handler: function(){ location.href='acessos_segmentos.php' } 
	}, 
	{ text: 'Empr�stimos WEB' 
	,handler: function(){ location.href='lst_sol_emp_web.php' } 
	}, 
	{ text: 'Senhas Solicitadas' 
	,handler: function(){ location.href='rel_senhas_solicitadas.php' } 
	}, 
	{ text: 'Todas Senhas' 
	,handler: function(){ location.href='rel_senhas_solicitadas.php' } 
	}, 
	{ text: 'Relat�rio resumo' 
	,handler: function(){ location.href='sel_periodo_resumo.php' } 
	}, 
	{ text: 'E-mail Reclama��o' 
	,handler: function(){ location.href='controle_email_reclamacao.php' } 
	} 

	]}}, 
	{ text: 'CRM Operacional' 
	, menu:{items:[
	{ text: 'Atendimentos' 
	, menu:{items:[
	{ text: 'Atendentes' 
	,handler: function(){ location.href='adm_atendimento.php' } 
	}, 
	{ text: 'Encaminhamento' 
	,handler: function(){ location.href='lst_atividades.php?TA=R' } 
	}, 
	{ text: 'Encaminhamento Suporte' 
	,handler: function(){ location.href='lst_encaminhamentos_abertos.php' } 
	}, 
	{ text: 'Correspond�ncias' 
	,handler: function(){ location.href='atendimento_protocolo.php' } 
	}, 
	{ text: 'Relat�rios' 
	,handler: function(){ location.href='lst_meus_relatorios.php?ESPEC=atnd' } 
	}, 
	{ text: 'P�s venda' 
	,handler: function(){ cieprev_acesso( 'ecrm/posvenda' ); } 
	} 

	]}}, 
	{ text: 'Cadastros' 
	, menu:{items:[
	{ text: 'Certificados Participantes' 
	,handler: function(){ location.href='lst_certificados_participantes.php' } 
	}, 
	{ text: 'Eventos Institucionais' 
	,handler: function(){ location.href='lst_eventos_institucionais.php' } 
	}, 
	{ text: 'Contatos' 
	,handler: function(){ location.href='lst_pos_venda.php' } 
	}, 
	{ text: 'Publica��es' 
	,handler: function(){ location.href='lst_publicacoes.php' } 
	}, 
	{ text: 'Tipos de eventos' 
	,handler: function(){ location.href='lst_listas.php?cat=TEVN' } 
	}, 
	{ text: 'Protocolos - Digitaliza��o' 
	,handler: function(){ location.href='documento_protocolo.php' } 
	}, 
	{ text: 'Recadastro' 
	,handler: function(){ location.href='atendimento_recadastro.php' } 
	}, 
	{ text: 'Ingresso / Exame M�dico' 
	,handler: function(){ location.href='exame_ingresso.php' } 
	}, 
	{ text: 'Protocolo Interno' 
	,handler: function(){ location.href='documento_recebido.php' } 
	} 

	]}}, 
	{ text: 'Contatos' 
	,handler: function(){ location.href='lista_contatos.php' } 
	}, 
	{ text: 'Erros de login site' 
	,handler: function(){ location.href='erros_login_aa.php' } 
	}, 
	{ text: 'Relacionamento' 
	, menu:{items:[
	{ text: 'Emails autom�ticos' 
	,handler: function(){ location.href='lst_relacionamento.php' } 
	}, 
	{ text: 'Pesquisa' 
	,handler: function(){ location.href='lst_enquetes.php' } 
	}, 
	{ text: 'Pesquisa Grupo' 
	,handler: function(){ location.href='lst_enquete_grupo.php' } 
	} 

	]}}, 
	{ text: 'Solicita��o de Participante' 
	,handler: function(){ location.href='lst_ativ_re.php' } 
	}, 
	{ text: 'Solicita��o de senhas' 
	,handler: function(){ location.href='lista_solicitacao_senhas.php' } 
	}, 
	{ text: 'Visitantes' 
	, menu:{items:[
	{ text: 'Cadastro' 
	,handler: function(){ location.href='cad_visitantes.php' } 
	}, 
	{ text: 'Relat�rio Di�rio' 
	,handler: function(){ location.href='rel_visitantes_diario.php' } 
	}, 
	{ text: 'Relat�rio Mensal' 
	,handler: function(){ location.href='rel_visitantes_mensal.php' } 
	}, 
	{ text: 'Relat�rio Anual' 
	,handler: function(){ location.href='rel_visitantes_anual.php' } 
	}, 
	{ text: 'Relat�rio Visitante' 
	,handler: function(){ location.href='rel_visitantes.php' } 
	}, 
	{ text: 'Movimento Visitatnes' 
	,handler: function(){ location.href='lst_visitantes.php' } 
	}, 
	{ text: 'Manuten��o Destino' 
	,handler: function(){ location.href='cad_visitante_manutencao_destino.php' } 
	}, 
	{ text: 'Manuten��o Nome' 
	,handler: function(){ location.href='cad_visitante_manutencao_nome.php' } 
	}, 
	{ text: 'Manuten��o Proced.' 
	,handler: function(){ location.href='cad_visitante_manutencao_procedencia.php' } 
	} 

	]}}, 
	{ text: 'Chaves' 
	, menu:{items:[
	{ text: 'Chaves' 
	,handler: function(){ location.href='lst_chaves.php' } 
	}, 
	{ text: 'Controle' 
	,handler: function(){ location.href='cad_chaves_movimento.php' } 
	}, 
	{ text: 'Movimento' 
	,handler: function(){ location.href='lst_chaves_movimento.php' } 
	} 

	]}} 

	]}}, 
	{ text: 'Divulga��o' 
	, menu:{items:[
	{ text: 'Email Marketing' 
	,handler: function(){ location.href='lst_email_marketing.php' } 
	}, 
	{ text: 'Emails Enviados' 
	,handler: function(){ location.href='lst_envia_emails.php?e=GRI' } 
	} 

	]}}, 
	{ text: 'Informativos' 
	, menu:{items:[
	{ text: 'Boletim Informativo' 
	,handler: function(){ location.href='lst_edicao_boletim.php' } 
	}, 
	{ text: 'Cen�rio Legal' 
	,handler: function(){ location.href='lst_edicao_cenario.php' } 
	}, 
	{ text: 'Clipping Di�rio' 
	,handler: function(){ location.href='lst_noticias.php' } 
	} 

	]}}, 
	{ text: 'Rela��es Intitucionais' 
	, menu:{items:[
	{ text: 'Semin�rio Econ�mico' 
	, menu:{items:[
	{ text: 'Inscritos' 
	,handler: function(){ location.href='lst_inscritos_seminario.php' } 
	} 

	]}}, 
	{ text: 'Eventos Institucionais' 
	, menu:{items:[
	{ text: 'Inscri��o' 
	,handler: function(){ location.href='evento_institucional_inscricao.php' } 
	}, 
	{ text: 'Apura��o' 
	,handler: function(){ location.href='evento_institucional_inscricao_apuracao.php' } 
	} 

	]}}, 
	{ text: 'Cadastro Empresas' 
	,handler: function(){ location.href='lst_empresas.php?chk_rel=S' } 
	}, 
	{ text: 'Cadastro Pessoas' 
	,handler: function(){ location.href='lst_mailing.php' } 
	}, 
	{ text: 'Consultas' 
	, menu:{items:[
	{ text: 'Comunidades' 
	,handler: function(){ location.href='lst_comunidades.php' } 
	}, 
	{ text: 'Empresas' 
	,handler: function(){ location.href='sel_empresas.php' } 
	}, 
	{ text: 'Participantes' 
	,handler: function(){ location.href='sel_participantes.php' } 
	}, 
	{ text: 'Pessoas' 
	,handler: function(){ location.href='sel_pessoas.php' } 
	} 

	]}}, 
	{ text: 'Mensagem' 
	,handler: function(){ location.href='mensagem_estacao.php' } 
	}, 
	{ text: 'Class. Geogr�fica' 
	, menu:{items:[
	{ text: 'Brasil' 
	,handler: function(){ location.href='cad_brasil.php' } 
	}, 
	{ text: 'Bairros' 
	,handler: function(){ location.href='lst_bairros.php' } 
	}, 
	{ text: 'Cidades' 
	,handler: function(){ location.href='lst_cidades.php' } 
	}, 
	{ text: 'Estados' 
	,handler: function(){ location.href='lst_estados.php' } 
	}, 
	{ text: 'Logradouros' 
	,handler: function(){ location.href='lst_logradouros.php?chkconf=S' } 
	}, 
	{ text: 'Microregi�es' 
	,handler: function(){ location.href='lst_microregioes.php' } 
	}, 
	{ text: 'Regi�es' 
	,handler: function(){ location.href='lst_macroregioes.php' } 
	} 

	]}}, 
	{ text: 'Class. Econ�mica' 
	, menu:{items:[
	{ text: 'Ramo de atividade' 
	,handler: function(){ location.href='lst_ramo.php' } 
	}, 
	{ text: 'Segmento' 
	,handler: function(){ location.href='lst_segmento.php' } 
	} 

	]}}, 
	{ text: 'Pr�-Venda' 
	,handler: function(){ cieprev_acesso( 'ecrm/prevenda' ); } 
	} 

	]}}, 
	{ text: 'Iniciar Atendimento' 
	,handler: function(){ location.href='inicio_prevnet.php' } 
	}, 
	{ text: 'Manuten��o' 
	, menu:{items:[
	{ text: 'Titular' 
	,handler: function(){ location.href='participante_atendente.php' } 
	}, 
	{ text: 'Dependentes' 
	,handler: function(){ location.href='lista_dependentes.php' } 
	}, 
	{ text: 'Formul�rios' 
	,handler: function(){ location.href='frm_formularios_beneficio.php' } 
	} 

	]}}, 
	{ text: 'Consultas' 
	, menu:{items:[
	{ text: 'Benef�cios' 
	,handler: function(){ location.href='beneficios.php' } 
	}, 
	{ text: 'Contra cheque' 
	,handler: function(){ location.href='contra_cheque.php' } 
	}, 
	{ text: 'D�bitos' 
	,handler: function(){ location.href='debitos.php' } 
	}, 
	{ text: 'Consulta' 
	,handler: function(){ location.href='documentos.php' } 
	}, 
	{ text: 'Grava��es e emails' 
	,handler: function(){ location.href='gravacoes_emails.php' } 
	}, 
	{ text: 'Restitui��es' 
	,handler: function(){ location.href='restituicoes.php' } 
	}, 
	{ text: 'Seguro' 
	,handler: function(){ location.href='lista_apolices_seguro.php' } 
	} 

	]}}, 
	{ text: 'Empr�stimos' 
	, menu:{items:[
	{ text: 'Simula��o' 
	,handler: function(){ location.href='simulacao_emp.php' } 
	}, 
	{ text: 'Anteriores' 
	,handler: function(){ location.href='lista_emprestimos.php' } 
	}, 
	{ text: 'Solicitados WEB' 
	,handler: function(){ location.href='lst_emp_solicitados.php' } 
	} 

	]}}, 
	{ text: 'Simulador benef.' 
	, menu:{items:[
	{ text: 'CEEEPrev' 
	, menu:{items:[
	{ text: 'Novo Participante' 
	 ,handler: function(){ window.open('https://www.fundacaoceee.com.br/simulador_ceeeprev.php'); } 
	}, 
	{ text: 'Participante Ativo' 
	 ,handler: function(){ cieprev_acesso('ecrm/simulador_ceeeprev'); } 
	} 

	]}}, 
	{ text: 'CRMPrev' 
	, menu:{items:[
	{ text: 'Novo Participante' 
	 ,handler: function(){ window.open('https://www.fundacaoceee.com.br/simulador_crmprev.php'); } 
	} 

	]}}, 
	{ text: 'SENGE' 
	, menu:{items:[
	{ text: 'Novo Participante' 
	 ,handler: function(){ window.open('https://www.fundacaoceee.com.br/senge_simulador_new.php?cd_secao=MEUP'); } 
	} 

	]}}, 
	{ text: 'SINPRORS' 
	, menu:{items:[
	{ text: 'Novo Participante' 
	 ,handler: function(){ window.open('http://www.sinprorsprevidencia.com.br/simulador.php'); } 
	} 

	]}} 

	]}} 

	]
	 });



	var gestao = new Ext.menu.Menu({ id: 'gestao', items: 
	[
	{ text: 'Cadastros' 
	, menu:{items:[
	{ text: 'Eventos Sistema' 
	,handler: function(){ location.href='lst_eventos.php' } 
	}, 
	{ text: 'Indicadores' 
	,handler: function(){ location.href='lst_indic_raiz.php' } 
	}, 
	{ text: 'Inst�ncias' 
	,handler: function(){ location.href='lst_instancias.php' } 
	}, 
	{ text: 'Processos' 
	,handler: function(){ location.href='lst_processos.php' } 
	} 

	]}}, 
	{ text: 'N�o conformidades' 
	,handler: function(){ location.href='lst_nao_conf.php' } 
	}, 
	{ text: 'Relat�rios NC/AC' 
	,handler: function(){ location.href='rel_nc_ac.php' } 
	}, 
	{ text: 'Departamental' 
	,handler: function(){ location.href='modelo_divisoes.php' } 
	} 

	]
	});
	var intranet = new Ext.menu.Menu({ id: 'intranet', items: 
	[
	{ text: 'Nossa Intranet' 
	, menu:{items:[
	{ text: 'AELETRO' 
	,handler: function(){ location.href='cad_intra_div.php?div=AE&c=1' } 
	}, 
	{ text: 'Biblioteca Multim�dia' 
	,handler: function(){ location.href='intranet_fotos.php' } 
	}, 
	{ text: 'Cen�rio Legal' 
	,handler: function(){ location.href='cenario_capa.php' } 
	}, 
	{ text: 'Clipping Di�rio' 
	,handler: function(){ window.open('http://www.e-prev.com.br/clipping/publicar.html'); } 
	}, 
	{ text: 'Expans�o' 
	,handler: function(){ location.href='cad_intra_div.php?div=GE&c=1' } 
	}, 
	{ text: 'Fale com o Presidente' 
	,handler: function(){ location.href='fale_com_o_presidente.php' } 
	}, 
	{ text: 'Ramais Internos' 
	,handler: function(){ window.open('https://www.e-prev.com.br/controle_projetos/ler_arq.php?ds_arq=\\Srvmultimidia\GAD\lista_telefonica_interna.pdf'); } 
	} 

	]}}, 
	{ text: 'Consultas' 
	, menu:{items:[
	{ text: 'Pesquisa CEP' 
	,handler: function(){ location.href='intranet_ceps.php' } 
	}, 
	{ text: 'Tradutor' 
	,handler: function(){ location.href='intranet_dic.php' } 
	}, 
	{ text: 'Legisla��o' 
	,handler: function(){ location.href='intranet_dic.php' } 
	}, 
	{ text: 'Pesquisa mapas' 
	,handler: function(){ location.href='intranet_mapas.php' } 
	}, 
	{ text: 'Pesquisa na WEB' 
	,handler: function(){ location.href='intranet_pesquisa.php' } 
	} 

	]}}, 
	{ text: 'Voc� na Intranet' 
	, menu:{items:[
	{ text: 'DE' 
	,handler: function(){ location.href='cad_intranet_div.php?div=DE&c=p' } 
	}, 
	{ text: 'GA - Atu�ria' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GA&c=p' } 
	}, 
	{ text: 'GAD - Administrativa' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GAD&c=p' } 
	}, 
	{ text: 'GAP - Atendimento' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GAP&c=p' } 
	}, 
	{ text: 'GB - Benef�cios' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GB&c=p' } 
	}, 
	{ text: 'GC - Controladoria' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GC&c=p' } 
	}, 
	{ text: 'GF - Financeira' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GF&c=p' } 
	}, 
	{ text: 'GI - Inform�tica' 
	, menu:{items:[
	{ text: 'GI - Inform�tica' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GI&c=p' } 
	}, 
	{ text: 'Pol�tica de Seguran�a' 
	,handler: function(){ window.open('http://www.e-prev.com.br/controle_projetos/documentos/politica_seguranca_di.pdf'); } 
	} 

	]}}, 
	{ text: 'GIN - Investimentos' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GIN&c=p' } 
	}, 
	{ text: 'GJ - Jur�dica' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GJ&c=p' } 
	}, 
	{ text: 'GRI - Rela��es Institucionais' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GRI&c=p' } 
	}, 
	{ text: 'SG' 
	,handler: function(){ location.href='cad_intranet_div.php?div=SG&c=p' } 
	}, 
	{ text: 'AELETRO' 
	,handler: function(){ location.href='cad_intranet_div.php?div=AE&c=p' } 
	}, 
	{ text: 'Comit� de Qualidade' 
	,handler: function(){ location.href='cad_intranet_div.php?div=CQ&c=p' } 
	}, 
	{ text: 'Comit� Previdenci�rio' 
	,handler: function(){ location.href='cad_intranet_div.php?div=CP&c=p' } 
	}, 
	{ text: 'Comit� de Educa��o Ambiental' 
	,handler: function(){ location.href='cad_intranet_div.php?div=CEA&c=p' } 
	}, 
	{ text: 'Coral' 
	,handler: function(){ location.href='cad_intranet_div.php?div=COR&c=p' } 
	}, 
	{ text: 'Expans�o' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GE&c=p' } 
	} 

	]}}, 
	{ text: 'Veja na Internet' 
	, menu:{items:[
	{ text: 'Banrisul' 
	,handler: function(){ window.open('http://www.banrisul.com.br'); } 
	}, 
	{ text: 'Links corporativos' 
	, menu:{items:[
	{ text: 'ABRAPP' 
	,handler: function(){ window.open('http://www.abrapp.org.br'); } 
	}, 
	{ text: 'ANAPAR' 
	,handler: function(){ window.open('http://www.anapar.com.br'); } 
	}, 
	{ text: 'MPAS' 
	,handler: function(){ window.open('http://www.mpas.gov.br'); } 
	}, 
	{ text: 'SPC' 
	,handler: function(){ window.open('http://www.mpas.gov.br/pg_secundarias/previdencia_complementar.asp'); } 
	} 

	]}}, 
	{ text: 'Not�cias' 
	, menu:{items:[
	{ text: 'Ag�ncia Estado' 
	,handler: function(){ window.open('http://www.estadao.com.br'); } 
	}, 
	{ text: 'Band News' 
	,handler: function(){ window.open('http://www.bandnews.com.br'); } 
	}, 
	{ text: 'BBC Brasil' 
	,handler: function(){ window.open('http://www.bbc.co.uk/portuguese/index.shtml'); } 
	}, 
	{ text: 'Clic RBS' 
	,handler: function(){ window.open('http://www.clicrbs.com.br/'); } 
	}, 
	{ text: 'CNN' 
	,handler: function(){ window.open('http://www.cnn.com/'); } 
	}, 
	{ text: 'Invertia Previd�ncia' 
	,handler: function(){ window.open('http://invertia.terra.com.br/previdencia'); } 
	}, 
	{ text: 'Globo News' 
	,handler: function(){ window.open('http://globonews.globo.com/'); } 
	}, 
	{ text: 'Infobrazil' 
	,handler: function(){ window.open('http://www.infobrazil.com/'); } 
	}, 
	{ text: 'Infoenergia' 
	,handler: function(){ window.open('http://www.infoenergia.com.br/'); } 
	}, 
	{ text: 'Valor Online' 
	,handler: function(){ window.open('http://www.valoronline.com.br/'); } 
	} 

	]}}, 
	{ text: 'Jornais' 
	, menu:{items:[
	{ text: 'Correio do Povo' 
	,handler: function(){ window.open('http://www.correiodopovo.com.br'); } 
	}, 
	{ text: 'Gazeta Mercantil' 
	,handler: function(){ window.open('http://www.gazetamercantil.com.br'); } 
	}, 
	{ text: 'Jornal do Brasil' 
	,handler: function(){ window.open('http://jbonline.terra.com.br/'); } 
	}, 
	{ text: 'O Estado de SP' 
	,handler: function(){ window.open('http://jpdf.estado.com.br/'); } 
	}, 
	{ text: 'O Globo' 
	,handler: function(){ window.open('http://oglobo.globo.com/jornal/'); } 
	}, 
	{ text: 'Zero Hora' 
	,handler: function(){ window.open('http://zh.clicrbs.com.br/'); } 
	} 

	]}}, 
	{ text: 'Revistas' 
	, menu:{items:[
	{ text: 'Revista �poca' 
	,handler: function(){ window.open('http://revistaepoca.globo.com/'); } 
	}, 
	{ text: 'Isto �' 
	,handler: function(){ window.open('http://www.terra.com.br/istoe/'); } 
	}, 
	{ text: 'Revista Info' 
	,handler: function(){ window.open('http://info.abril.uol.com.br/'); } 
	}, 
	{ text: 'InformationWeek' 
	,handler: function(){ window.open('http://www.informationweek.com.br/'); } 
	}, 
	{ text: 'Revista Veja' 
	,handler: function(){ window.open('http://vejaonline.abril.uol.com.br/'); } 
	}, 
	{ text: 'Revista Voc� SA' 
	,handler: function(){ window.open('http://vocesa.abril.uol.com.br/'); } 
	} 

	]}}, 
	{ text: 'Governo' 
	, menu:{items:[
	{ text: 'Governo Brasileiro' 
	,handler: function(){ window.open('http://www.brasil.gov.br/'); } 
	}, 
	{ text: 'Governo do RS' 
	,handler: function(){ window.open('http://www.estado.rs.gov.br/'); } 
	}, 
	{ text: 'Governo de Porto Alegre' 
	,handler: function(){ window.open('http://www.portoalegre.rs.gov.br/'); } 
	} 

	]}}, 
	{ text: 'Patrocinadoras' 
	, menu:{items:[
	{ text: 'AES SUL' 
	,handler: function(){ window.open('http://www.aessul.com.br/'); } 
	}, 
	{ text: 'CEEE' 
	,handler: function(){ window.open('http://www.ceee.com.br/'); } 
	}, 
	{ text: 'CGTEE' 
	,handler: function(){ window.open('http://www.cgtee.gov.br/'); } 
	}, 
	{ text: 'CRM' 
	,handler: function(){ window.open('http://www.crm.rs.gov.br/'); } 
	}, 
	{ text: 'RGE' 
	,handler: function(){ window.open('http://www.rge-rs.com.br/'); } 
	}, 
	{ text: 'SENGE' 
	,handler: function(){ window.open('http://www.senge.org.br/'); } 
	} 

	]}} 

	]}} 

	]
	});
	var planos = new Ext.menu.Menu({ id: 'planos', items: 
	[
	{ text: 'SENGE Previd�ncia' 
	, menu:{items:[
	{ text: 'Cadastro' 
	,handler: function(){ location.href='lst_planos.php?p=7' } 
	}, 
	{ text: 'Contribui��es' 
	,handler: function(){ location.href='contribuicao_senge.php' } 
	}, 
	{ text: 'Pagamentos' 
	,handler: function(){ window.open('http://www.sengeprev.com.br/rel_pagamentos.php'); } 
	}, 
	{ text: 'Emails Enviados' 
	,handler: function(){ location.href='lst_envia_emails.php?e=SNG' } 
	}, 
	{ text: 'Extratos' 
	,handler: function(){ location.href='lst_email_marketing.php?pub=CS1P' } 
	}, 
	{ text: 'Inscritos' 
	,handler: function(){ location.href='lst_inscritos.php' } 
	}, 
	{ text: 'Nova Inscri��o' 
	,handler: function(){ location.href='senge_inscricao_etapa_0.php?sender=eprev' } 
	}, 
	{ text: 'Relat�rio de Acompanhamento' 
	,handler: function(){ location.href='senge_rel_acompanhamento_libera.php' } 
	} 

	]}}, 
	{ text: 'CEEEPrev' 
	, menu:{items:[
	{ text: 'Cadastro' 
	,handler: function(){ location.href='lst_planos.php?p=2' } 
	}, 
	{ text: 'Enviar Extratos' 
	,handler: function(){ location.href='lst_email_marketing.php?pub=CS2P' } 
	} 

	]}}, 
	{ text: 'CRM Prev' 
	, menu:{items:[
	{ text: 'Cadastro' 
	,handler: function(){ location.href='lst_planos.php?p=6' } 
	}, 
	{ text: 'Enviar Extratos' 
	,handler: function(){ location.href='lst_email_marketing.php?pub=CS6P' } 
	} 

	]}}, 
	{ text: 'Plano �nico AES Sul' 
	, menu:{items:[
	{ text: 'Cadastro' 
	,handler: function(){ location.href='lst_planos.php?p=1' } 
	}, 
	{ text: 'Carta Julho/2007' 
	,handler: function(){ location.href='rel_aes_junho2007.php' } 
	} 

	]}}, 
	{ text: 'SINPRORS Previd�ncia' 
	, menu:{items:[
	{ text: 'Cadastro' 
	,handler: function(){ location.href='lst_planos.php?p=8' } 
	}, 
	{ text: 'Pr� Cadastro' 
	,handler: function(){ location.href='sinprors_pre_cadastro.php' } 
	}, 
	{ text: 'Contribui��es' 
	, menu:{items:[
	{ text: 'Normal' 
	,handler: function(){ location.href='contribuicao_sinprors_normal.php' } 
	}, 
	{ text: 'Atrasadas' 
	,handler: function(){ location.href='contribuicao_sinprors_atraso.php' } 
	}, 
	{ text: 'Relat�rio' 
	,handler: function(){ location.href='contribuicao_sinprors_relatorio.php' } 
	} 

	]}}, 
	{ text: 'Emails Enviados' 
	,handler: function(){ cieprev_acesso( 'sinprors/email_enviado' ); } 
	} 

	]}} 

	]
	});
	var servicos = new Ext.menu.Menu({ id: 'servicos', items: 
	[
	{ text: 'Ajuda' 
	,handler: function(){ location.href='ajuda.php' } 
	}, 
	{ text: 'Apresenta��es' 
	, menu:{items:[
	{ text: 'Sindicatos' 
	,handler: function(){ window.open('http://www.e-prev.com.br/controle_projetos/apresentacoes/apresentacao_instituidores.html'); } 
	} 

	]}}, 
	{ text: 'Avalia��o de Compet�ncias' 
	, menu:{items:[
	{ text: 'Nova' 
	,handler: function(){ location.href='avaliacao.php?tipo=N&cd_capa=0' } 
	}, 
	{ text: 'Avalia��es' 
	,handler: function(){ location.href='avaliacao.php' } 
	} 

	]}}, 
	{ text: 'Contra cheque' 
	,handler: function(){ location.href='cc.php' } 
	}, 
	{ text: 'Meu extrato' 
	,handler: function(){ location.href='meu_extrato.php' } 
	}, 
	{ text: 'Controle Cen�rio' 
	,handler: function(){ location.href='lst_controle_cenario.php' } 
	}, 
	{ text: 'Troca de senha' 
	,handler: function(){ location.href='troca_senha.php' } 
	}, 
	{ text: 'Troca de usu�rio' 
	,handler: function(){ location.href='index.php' } 
	}, 
	{ text: 'WEB mail' 
	,handler: function(){ window.open('http://webmail.eletroceee.com.br'); } 
	}, 
	{ text: 'Diagn�stico m�dico' 
	, menu:{items:[
	{ text: 'Consultas' 
	,handler: function(){ location.href='consulta_medica.php' } 
	}, 
	{ text: 'Tabelas m�dicas' 
	, menu:{items:[
	{ text: 'Sintomas' 
	,handler: function(){ location.href='lst_sintomas.php' } 
	} 

	]}} 

	]}}, 
	{ text: 'Parametros do sistema' 
	, menu:{items:[
	{ text: 'Par�metros GA' 
	, menu:{items:[
	{ text: 'Fator atuarial' 
	,handler: function(){ location.href='lst_fator_atuarial.php' } 
	}, 
	{ text: 'Taxas simula��o' 
	,handler: function(){ location.href='lst_taxas_simulacao.php' } 
	}, 
	{ text: 'Par�metros atuariais' 
	,handler: function(){ location.href='lst_listas.php?cat=PRSI' } 
	} 

	]}}, 
	{ text: 'Par�metros GRI' 
	, menu:{items:[
	{ text: 'Comunidade' 
	,handler: function(){ location.href='lst_listas.php?cat=CACS' } 
	}, 
	{ text: 'Distribui��o' 
	,handler: function(){ location.href='lst_listas.php?cat=DACS' } 
	}, 
	{ text: 'Localiza��o' 
	,handler: function(){ location.href='lst_listas.php?cat=LACS' } 
	}, 
	{ text: 'Participantes' 
	,handler: function(){ location.href='lst_listas.php?cat=PACS' } 
	}, 
	{ text: 'Se��es do site' 
	,handler: function(){ location.href='lst_listas.php?cat=SSIT' } 
	}, 
	{ text: 'Segm. Empresariais' 
	,handler: function(){ location.href='lst_listas.php?cat=SACS' } 
	}, 
	{ text: 'Tipo de Evento' 
	,handler: function(){ location.href='lst_listas.php?cat=TEVN' } 
	} 

	]}}, 
	{ text: 'Par�metros GJ' 
	, menu:{items:[
	{ text: 'Receb. de Audi�ncias' 
	,handler: function(){ location.href='lst_agenda_outlook.php?dv=GJ' } 
	} 

	]}}, 
	{ text: 'Par�metros GAP' 
	, menu:{items:[
	{ text: 'Fech. Empr�stimos' 
	,handler: function(){ location.href='cad_periodos_emprestimo.php' } 
	}, 
	{ text: 'Forma de Solicita��o' 
	,handler: function(){ location.href='lst_listas.php?cat=FDAP' } 
	}, 
	{ text: 'Prog. Institucionais' 
	,handler: function(){ location.href='lst_listas.php?cat=PRFC' } 
	}, 
	{ text: 'Solicitantes' 
	,handler: function(){ location.href='lst_listas.php?cat=SDAP' } 
	}, 
	{ text: 'Status / Atividades' 
	,handler: function(){ location.href='lst_listas.php?cat=STAT&div=GRI' } 
	}, 
	{ text: 'Tipo de Atividade' 
	,handler: function(){ location.href='lst_listas.php?cat=TPAT&div=GRI' } 
	}, 
	{ text: 'Tipos de Manuten��o' 
	,handler: function(){ location.href='lst_listas.php?cat=TPMN&div=GRI' } 
	}, 
	{ text: 'Tipo de Reclama��o' 
	,handler: function(){ location.href='lst_listas.php?cat=TDAP' } 
	} 

	]}}, 
	{ text: 'Par�metros GB' 
	, menu:{items:[
	{ text: 'Status / Atividade' 
	,handler: function(){ location.href='lst_listas.php?cat=STAT&div=GB' } 
	}, 
	{ text: 'Tipo Atividade' 
	,handler: function(){ location.href='lst_listas.php?cat=TPAT&div=GB' } 
	}, 
	{ text: 'Tipo Manuten��o' 
	,handler: function(){ location.href='lst_listas.php?cat=TPMN&div=GB' } 
	} 

	]}}, 
	{ text: 'Par�metros GC' 
	, menu:{items:[
	{ text: 'Resp. Cen�rio Legal' 
	,handler: function(){ location.href='lst_resp_cenario.php' } 
	}, 
	{ text: 'Status / Atividade' 
	,handler: function(){ location.href='lst_listas.php?cat=STAT&div=GC' } 
	}, 
	{ text: 'Tipo Atividade' 
	,handler: function(){ location.href='lst_listas.php?cat=TPAT&div=GC' } 
	} 

	]}}, 
	{ text: 'Par�metros GI' 
	, menu:{items:[
	{ text: 'Prioridade / Tarefa' 
	,handler: function(){ location.href='lst_listas.php?cat=TPTR&div=GI' } 
	}, 
	{ text: 'Status / Atividade' 
	,handler: function(){ location.href='lst_listas.php?cat=STAT&div=GI' } 
	}, 
	{ text: 'Situa��o Equipamento' 
	,handler: function(){ location.href='lst_listas.php?cat=SITU&div=GI' } 
	}, 
	{ text: 'Tipo de Atividade' 
	,handler: function(){ location.href='lst_listas.php?cat=TPAT&div=GI' } 
	}, 
	{ text: 'Tipo de Manuten��o' 
	,handler: function(){ location.href='lst_listas.php?cat=TPMN&div=GI' } 
	}, 
	{ text: 'Tipo de Equipmanento' 
	,handler: function(){ location.href='lst_listas.php?cat=EQUP&div=GI' } 
	} 

	]}}, 
	{ text: 'Par�metros GF' 
	, menu:{items:[
	{ text: 'Status / Atividades' 
	,handler: function(){ location.href='lst_listas.php?cat=STAT&div=GB' } 
	}, 
	{ text: 'Tipo de Atividade' 
	,handler: function(){ location.href='lst_listas.php?cat=TPAT&div=GB' } 
	}, 
	{ text: 'Tipos de Manuten��o' 
	,handler: function(){ location.href='lst_listas.php?cat=TPMN&div=GB' } 
	} 

	]}} 

	]}}, 
	{ text: 'Manuten��es' 
	, menu:{items:[
	{ text: 'Arquivos CCheque' 
	, handler: function(){ location.href='arquivos_contra_cheque.php' }
	}, 
	{ text: 'Bloquetos' 
	, menu:{items:[
	{ text: 'Manuten��o' 
	,handler: function(){ location.href='manut_bloquetos.php' } 
	}, 
	{ text: 'Lista' 
	,handler: function(){ location.href='lst_bloquetos.php' } 
	} 

	]}}, 
	{ text: 'Elei��es' 
	, menu:{items:[
	{ text: 'Administra��o' 
	,handler: function(){ location.href='lst_adm_eleicoes.php' } 
	}, 
	{ text: 'Cadastro' 
	,handler: function(){ location.href='lst_eleitores.php' } 
	}, 
	{ text: 'Candidatos' 
	,handler: function(){ location.href='lst_candidatos.php' } 
	}, 
	{ text: 'Eleitores / Munic�pio' 
	,handler: function(){ location.href='lst_eleitores_municipio.php' } 
	}, 
	{ text: 'Etapas do Projeto' 
	,handler: function(){ location.href='lst_etapas_projeto.php?c=138' } 
	}, 
	{ text: 'Eleitores Votantes' 
	,handler: function(){ location.href='lst_votantes.php' } 
	}, 
	{ text: 'Hist�rico' 
	,handler: function(){ location.href='lst_historico_eleicoes.php' } 
	}, 
	{ text: 'Receb. Etiquetas' 
	,handler: function(){ location.href='receb_etiquetas.php' } 
	}, 
	{ text: 'Receb. Manual' 
	,handler: function(){ location.href='receb_etiquetas_manual.php' } 
	}, 
	{ text: 'Tipo doc/assinatura' 
	,handler: function(){ location.href='lst_tipos_docs_assinatura.php' } 
	}, 
	{ text: 'Valida Assinaturas' 
	,handler: function(){ location.href='valida_assinatura.php' } 
	}, 
	{ text: 'Valida Ass. 2� Inst.' 
	,handler: function(){ location.href='valida_assin_seg_instancia.php' } 
	}, 
	{ text: 'Vota��o' 
	,handler: function(){ location.href='lanca_votos.php' } 
	}, 
	{ text: 'Lotes de votos' 
	,handler: function(){ location.href='lst_lotes_votos.php' } 
	} 

	]}}, 
	{ text: 'Adm. PostgreSQL' 
	,handler: function(){ location.href='adm_postgres.php' } 
	}, 
	{ text: 'Adm. Listner' 
	,handler: function(){ location.href='adm_listner.php' } 
	}, 
	{ text: 'Links quebrados' 
	,handler: function(){ location.href='log_link_quebrado.php' } 
	}, 
	{ text: 'Dicas' 
	,handler: function(){ location.href='lst_dicas.php' } 
	}, 
	{ text: 'Relat�rios Din�micos' 
	,handler: function(){ location.href='lst_relatorios.php' } 
	}, 
	{ text: 'Skins' 
	,handler: function(){ location.href='lst_skins.php' } 
	}, 
	{ text: 'Tabelas a Atualizar' 
	,handler: function(){ location.href='lst_tabelas_atualizar.php' } 
	}, 
	{ text: 'Telas do Sistema' 
	,handler: function(){ location.href='lst_telas_eprev.php' } 
	}, 
	{ text: 'Subversion' 
	, menu:{items:[
	{ text: 'Fontes Oracle' 
	,handler: function(){ location.href='svn_estatistica.php?tipo=ORACLE' } 
	}, 
	{ text: 'Fontes Web' 
	,handler: function(){ location.href='svn_estatistica.php?tipo=WEB' } 
	}, 
	{ text: 'Fontes Visual Basic' 
	,handler: function(){ location.href='svn_estatistica.php?tipo=VB' } 
	} 

	]}}, 
	{ text: 'Logs de Jobs Postgres' 
	,handler: function(){ cieprev_acesso( 'log' ); } 
	} 

	]}} 

	]
	});

	var toolBar = new Ext.Toolbar({
	id:'toolBar'
	, renderTo: 'MenuDiv'
	, items:[
	new Ext.Button( {text:'In�cio', handler: function(){ location.href='workspace.php'; }} )
	, new Ext.Button({text:'Atividades',menu:atividade})
	, new Ext.Button({text:'Cadastros',menu:cadastro})
	, new Ext.Button({text:'e-CRM',menu:ecrm})
	, new Ext.Button({text:'Gest�o',menu:gestao})
	, new Ext.Button({text:'Intranet',menu:intranet})
	, new Ext.Button({text:'Planos',menu:planos})
	, new Ext.Button({text:'Servi�os',menu:servicos})
	]
	});
});