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
	{ text: 'Divisão' 
	,handler: function(){ location.href='lst_atividades_divisao.php' } 
	}, 
	{ text: 'Soluções do Suporte' 
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
	{ text: 'Informática' 
	,handler: function(){ location.href='cad_atividade_solic.php?aa=GI' } 
	}, 
	{ text: 'Suporte' 
	,handler: function(){ location.href='cad_atividade_solic.php?aa=GI&tm=s' } 
	}, 
	{ text: 'Atendimento' 
	,handler: function(){ location.href='cad_atividade_solic.php?aa=GAP' } 
	}, 
	{ text: 'Benefícios' 
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
	{ text: 'Informática' 
	,handler: function(){ location.href='gra_atividades_mes.php?aa=GI' } 
	}, 
	{ text: 'Suporte' 
	,handler: function(){ location.href='gra_atividades_mes.php?aa=GI&tm=s' } 
	}, 
	{ text: 'Atendimento' 
	,handler: function(){ location.href='gra_atividades_mes.php?aa=GAP' } 
	}, 
	{ text: 'Benefício' 
	,handler: function(){ location.href='gra_atividades_mes.php?aa=GB' } 
	}, 
	{ text: 'Comunicação' 
	,handler: function(){ location.href='gra_atividades_mes.php?aa=GRI' } 
	} 

	]}}, 
	{ text: 'Abertas / Atendente' 
	, menu:{items:[
	{ text: 'Informática' 
	,handler: function(){ location.href='gra_atividades_atendente_mes.php?aa=GI' } 
	}, 
	{ text: 'Suporte' 
	}, 
	{ text: 'Atendimento' 
	,handler: function(){ location.href='gra_atividades_atendente_mes.php?aa=GAP' } 
	}, 
	{ text: 'Benefícios' 
	,handler: function(){ location.href='gra_atividades_atendente_mes.php?aa=GB' } 
	}, 
	{ text: 'Comunicação' 
	,handler: function(){ location.href='gra_atividades_atendente_mes.php?aa=GRI' } 
	} 

	]}}, 
	{ text: 'Abertas / Divisão' 
	, menu:{items:[
	{ text: 'Informática' 
	,handler: function(){ location.href='gra_atividades_divisao_mes.php?aa=GI' } 
	}, 
	{ text: 'Suporte' 
	,handler: function(){ location.href='gra_atividades_divisao_mes.php?aa=GI&tm=s' } 
	}, 
	{ text: 'Atendimento' 
	,handler: function(){ location.href='gra_atividades_divisao_mes.php?aa=GAP' } 
	}, 
	{ text: 'Benefício' 
	,handler: function(){ location.href='gra_atividades_divisao_mes.php?aa=GB' } 
	}, 
	{ text: 'Comunicação' 
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
	{ text: 'Gráfico Gantt' 
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
	{ text: 'Previsão Orçam' 
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
	{ text: 'Informática' 
	,handler: function(){ location.href='lst_acomp_projetos.php?aa=GI' } 
	}, 
	{ text: 'Suporte' 
	,handler: function(){ location.href='lst_acomp_projetos.php?aa=GI&tm=s' } 
	}, 
	{ text: 'Atendimento' 
	,handler: function(){ location.href='lst_acomp_projetos.php?aa=GAP' } 
	}, 
	{ text: 'Benefícios' 
	,handler: function(){ location.href='lst_acomp_projetos.php?aa=GB' } 
	}, 
	{ text: 'Comunicação' 
	,handler: function(){ location.href='lst_acomp_projetos.php?aa=GRI' } 
	} 

	]}}, 
	{ text: 'Tempo / Atividades' 
	, menu:{items:[
	{ text: 'Informática' 
	,handler: function(){ location.href='gra_atendimentos_mes.php?aa=GI' } 
	}, 
	{ text: 'Suporte' 
	,handler: function(){ location.href='gra_atendimentos_mes.php?aa=GI&tm=s' } 
	}, 
	{ text: 'Atendimento' 
	,handler: function(){ location.href='gra_atendimentos_mes.php?aa=GAP' } 
	}, 
	{ text: 'Benefícios' 
	,handler: function(){ location.href='gra_atendimentos_mes.php?aa=GB' } 
	}, 
	{ text: 'Comunicação' 
	,handler: function(){ location.href='gra_atendimentos_mes.php?aa=GRI' } 
	} 

	]}}, 
	{ text: 'Relatórios' 
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
	{ text: 'GI - Informática' 
	,handler: function(){ location.href='resumo_atividades.php' } 
	}, 
	{ text: 'GRI - Relações institucionais' 
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
	{ text: 'Correspondências' 
	, menu:{items:[
	{ text: 'Cadastro' 
	,handler: function(){ location.href='frm_correspondencias.php' } 
	}, 
	{ text: 'Recebidas' 
	,handler: function(){ location.href='frm_docs_recebidos.php' } 
	}, 
	{ text: 'Relatório Resumo' 
	,handler: function(){ location.href='sel_periodo_correspondencia.php' } 
	} 

	]}}, 
	{ text: 'Divisões' 
	,handler: function(){ location.href='lst_divisoes.php' } 
	}, 



	{ text: 'Equipamentos de informática' 
	, menu:{items:[
	{ text: 'Consulta' 
	,handler: function(){ location.href='lst_equipamentos.php' } 
	}, 
	{ text: 'Distribuição Física' 
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
	{ text: 'Meus Relatórios' 
	,handler: function(){ location.href='lst_meus_relatorios.php' } 
	}, 
	{ text: 'Projetos' 
	,handler: function(){ location.href='lst_projetos.php' } 
	}, 
	{ text: 'Publicações' 
	,handler: function(){ location.href='lst_publicacoes.php' } 
	}, 
	{ text: 'Serviços' 
	,handler: function(){ location.href='lst_servicos.php' } 
	}, 
	{ text: 'Softwares' 
	,handler: function(){ location.href='lst_programas.php' } 
	}, 
	{ text: 'Recursos Humanos' 
	, menu:{items:[
	{ text: 'Avaliação' 
	, menu:{items:[
	{ text: 'Família cargos' 
	,handler: function(){ location.href='lst_familias.php' } 
	}, 
	{ text: 'Cargos' 
	,handler: function(){ location.href='lst_cargos.php' } 
	}, 
	{ text: 'Competências Institucionais' 
	,handler: function(){ location.href='lst_comp_inst.php' } 
	}, 
	{ text: 'Competências Específicas' 
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
	{ text: 'Comitê de Avaliação' 
	,handler: function(){ location.href='avaliacao_config.php?lbu=nomearcomite' } 
	}, 
	{ text: 'Relatório' 
	,handler: function(){ location.href='avaliacao_config.php?lbu=relatorio' } 
	}, 
	{ text: 'Manutenção' 
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
	{ text: 'Biblioteca Multimídia' 
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
	{ text: 'GA - Atuária' 
	,handler: function(){ location.href='lst_intra_div.php?div=GA' } 
	}, 
	{ text: 'GAD - Administrativa' 
	,handler: function(){ location.href='lst_intra_div.php?div=GAD' } 
	}, 
	{ text: 'GAP - Atendimento' 
	,handler: function(){ location.href='lst_intra_div.php?div=GAP' } 
	}, 
	{ text: 'GB - Benefícios' 
	,handler: function(){ location.href='lst_intra_div.php?div=GB' } 
	}, 
	{ text: 'GC - Controladoria' 
	,handler: function(){ location.href='lst_intra_div.php?div=GC' } 
	}, 
	{ text: 'GF - Financeira' 
	,handler: function(){ location.href='lst_intra_div.php?div=GF' } 
	}, 
	{ text: 'GI - Informática' 
	,handler: function(){ location.href='lst_intra_div.php?div=GI' } 
	}, 
	{ text: 'GIN - Investimentos' 
	,handler: function(){ location.href='lst_intra_div.php?div=GIN' } 
	}, 
	{ text: 'GJ - Jurídica' 
	,handler: function(){ location.href='lst_intra_div.php?div=GJ' } 
	}, 
	{ text: 'GRI - Relações Intitucionais' 
	,handler: function(){ location.href='lst_intra_div.php?div=GRI' } 
	}, 
	{ text: 'SG' 
	,handler: function(){ location.href='lst_intra_div.php?div=SG' } 
	}, 
	{ text: 'AELETRO' 
	,handler: function(){ location.href='lst_intra_div.php?div=AE' } 
	}, 
	{ text: 'Comitê de Qualidade' 
	,handler: function(){ location.href='lst_intra_div.php?div=CQ' } 
	}, 
	{ text: 'Comitê Previdenciário' 
	,handler: function(){ location.href='lst_intra_div.php?div=CP' } 
	}, 
	{ text: 'Comitê de Educação Ambiental' 
	,handler: function(){ location.href='lst_intra_div.php?div=CEA' } 
	}, 
	{ text: 'Coral' 
	,handler: function(){ location.href='lst_intra_div.php?div=COR' } 
	}, 
	{ text: 'Expansão' 
	,handler: function(){ location.href='lst_intra_div.php?div=GE' } 
	} 

	]}}, 
	{ text: 'CRM Analítico' 
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
	{ text: 'Controle de ligações' 
	,handler: function(){ window.open('http://10.63.255.5:5800'); } 
	}, 
	{ text: 'Emails empréstimos' 
	,handler: function(){ location.href='lst_envia_emails.php?e=EMP' } 
	}, 
	{ text: 'Emails contatos' 
	,handler: function(){ location.href='lst_envia_emails.php?e=CON' } 
	}, 
	{ text: 'Segmentação' 
	,handler: function(){ location.href='acessos_segmentos.php' } 
	}, 
	{ text: 'Empréstimos WEB' 
	,handler: function(){ location.href='lst_sol_emp_web.php' } 
	}, 
	{ text: 'Senhas Solicitadas' 
	,handler: function(){ location.href='rel_senhas_solicitadas.php' } 
	}, 
	{ text: 'Todas Senhas' 
	,handler: function(){ location.href='rel_senhas_solicitadas.php' } 
	}, 
	{ text: 'Relatório resumo' 
	,handler: function(){ location.href='sel_periodo_resumo.php' } 
	}, 
	{ text: 'E-mail Reclamação' 
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
	{ text: 'Correspondências' 
	,handler: function(){ location.href='atendimento_protocolo.php' } 
	}, 
	{ text: 'Relatórios' 
	,handler: function(){ location.href='lst_meus_relatorios.php?ESPEC=atnd' } 
	}, 
	{ text: 'Pós venda' 
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
	{ text: 'Publicações' 
	,handler: function(){ location.href='lst_publicacoes.php' } 
	}, 
	{ text: 'Tipos de eventos' 
	,handler: function(){ location.href='lst_listas.php?cat=TEVN' } 
	}, 
	{ text: 'Protocolos - Digitalização' 
	,handler: function(){ location.href='documento_protocolo.php' } 
	}, 
	{ text: 'Recadastro' 
	,handler: function(){ location.href='atendimento_recadastro.php' } 
	}, 
	{ text: 'Ingresso / Exame Médico' 
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
	{ text: 'Emails automáticos' 
	,handler: function(){ location.href='lst_relacionamento.php' } 
	}, 
	{ text: 'Pesquisa' 
	,handler: function(){ location.href='lst_enquetes.php' } 
	}, 
	{ text: 'Pesquisa Grupo' 
	,handler: function(){ location.href='lst_enquete_grupo.php' } 
	} 

	]}}, 
	{ text: 'Solicitação de Participante' 
	,handler: function(){ location.href='lst_ativ_re.php' } 
	}, 
	{ text: 'Solicitação de senhas' 
	,handler: function(){ location.href='lista_solicitacao_senhas.php' } 
	}, 
	{ text: 'Visitantes' 
	, menu:{items:[
	{ text: 'Cadastro' 
	,handler: function(){ location.href='cad_visitantes.php' } 
	}, 
	{ text: 'Relatório Diário' 
	,handler: function(){ location.href='rel_visitantes_diario.php' } 
	}, 
	{ text: 'Relatório Mensal' 
	,handler: function(){ location.href='rel_visitantes_mensal.php' } 
	}, 
	{ text: 'Relatório Anual' 
	,handler: function(){ location.href='rel_visitantes_anual.php' } 
	}, 
	{ text: 'Relatório Visitante' 
	,handler: function(){ location.href='rel_visitantes.php' } 
	}, 
	{ text: 'Movimento Visitatnes' 
	,handler: function(){ location.href='lst_visitantes.php' } 
	}, 
	{ text: 'Manutenção Destino' 
	,handler: function(){ location.href='cad_visitante_manutencao_destino.php' } 
	}, 
	{ text: 'Manutenção Nome' 
	,handler: function(){ location.href='cad_visitante_manutencao_nome.php' } 
	}, 
	{ text: 'Manutenção Proced.' 
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
	{ text: 'Divulgação' 
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
	{ text: 'Cenário Legal' 
	,handler: function(){ location.href='lst_edicao_cenario.php' } 
	}, 
	{ text: 'Clipping Diário' 
	,handler: function(){ location.href='lst_noticias.php' } 
	} 

	]}}, 
	{ text: 'Relações Intitucionais' 
	, menu:{items:[
	{ text: 'Seminário Econômico' 
	, menu:{items:[
	{ text: 'Inscritos' 
	,handler: function(){ location.href='lst_inscritos_seminario.php' } 
	} 

	]}}, 
	{ text: 'Eventos Institucionais' 
	, menu:{items:[
	{ text: 'Inscrição' 
	,handler: function(){ location.href='evento_institucional_inscricao.php' } 
	}, 
	{ text: 'Apuração' 
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
	{ text: 'Class. Geográfica' 
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
	{ text: 'Microregiões' 
	,handler: function(){ location.href='lst_microregioes.php' } 
	}, 
	{ text: 'Regiões' 
	,handler: function(){ location.href='lst_macroregioes.php' } 
	} 

	]}}, 
	{ text: 'Class. Econômica' 
	, menu:{items:[
	{ text: 'Ramo de atividade' 
	,handler: function(){ location.href='lst_ramo.php' } 
	}, 
	{ text: 'Segmento' 
	,handler: function(){ location.href='lst_segmento.php' } 
	} 

	]}}, 
	{ text: 'Pré-Venda' 
	,handler: function(){ cieprev_acesso( 'ecrm/prevenda' ); } 
	} 

	]}}, 
	{ text: 'Iniciar Atendimento' 
	,handler: function(){ location.href='inicio_prevnet.php' } 
	}, 
	{ text: 'Manutenção' 
	, menu:{items:[
	{ text: 'Titular' 
	,handler: function(){ location.href='participante_atendente.php' } 
	}, 
	{ text: 'Dependentes' 
	,handler: function(){ location.href='lista_dependentes.php' } 
	}, 
	{ text: 'Formulários' 
	,handler: function(){ location.href='frm_formularios_beneficio.php' } 
	} 

	]}}, 
	{ text: 'Consultas' 
	, menu:{items:[
	{ text: 'Benefícios' 
	,handler: function(){ location.href='beneficios.php' } 
	}, 
	{ text: 'Contra cheque' 
	,handler: function(){ location.href='contra_cheque.php' } 
	}, 
	{ text: 'Débitos' 
	,handler: function(){ location.href='debitos.php' } 
	}, 
	{ text: 'Consulta' 
	,handler: function(){ location.href='documentos.php' } 
	}, 
	{ text: 'Gravações e emails' 
	,handler: function(){ location.href='gravacoes_emails.php' } 
	}, 
	{ text: 'Restituições' 
	,handler: function(){ location.href='restituicoes.php' } 
	}, 
	{ text: 'Seguro' 
	,handler: function(){ location.href='lista_apolices_seguro.php' } 
	} 

	]}}, 
	{ text: 'Empréstimos' 
	, menu:{items:[
	{ text: 'Simulação' 
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
	{ text: 'Instâncias' 
	,handler: function(){ location.href='lst_instancias.php' } 
	}, 
	{ text: 'Processos' 
	,handler: function(){ location.href='lst_processos.php' } 
	} 

	]}}, 
	{ text: 'Não conformidades' 
	,handler: function(){ location.href='lst_nao_conf.php' } 
	}, 
	{ text: 'Relatórios NC/AC' 
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
	{ text: 'Biblioteca Multimídia' 
	,handler: function(){ location.href='intranet_fotos.php' } 
	}, 
	{ text: 'Cenário Legal' 
	,handler: function(){ location.href='cenario_capa.php' } 
	}, 
	{ text: 'Clipping Diário' 
	,handler: function(){ window.open('http://www.e-prev.com.br/clipping/publicar.html'); } 
	}, 
	{ text: 'Expansão' 
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
	{ text: 'Legislação' 
	,handler: function(){ location.href='intranet_dic.php' } 
	}, 
	{ text: 'Pesquisa mapas' 
	,handler: function(){ location.href='intranet_mapas.php' } 
	}, 
	{ text: 'Pesquisa na WEB' 
	,handler: function(){ location.href='intranet_pesquisa.php' } 
	} 

	]}}, 
	{ text: 'Você na Intranet' 
	, menu:{items:[
	{ text: 'DE' 
	,handler: function(){ location.href='cad_intranet_div.php?div=DE&c=p' } 
	}, 
	{ text: 'GA - Atuária' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GA&c=p' } 
	}, 
	{ text: 'GAD - Administrativa' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GAD&c=p' } 
	}, 
	{ text: 'GAP - Atendimento' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GAP&c=p' } 
	}, 
	{ text: 'GB - Benefícios' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GB&c=p' } 
	}, 
	{ text: 'GC - Controladoria' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GC&c=p' } 
	}, 
	{ text: 'GF - Financeira' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GF&c=p' } 
	}, 
	{ text: 'GI - Informática' 
	, menu:{items:[
	{ text: 'GI - Informática' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GI&c=p' } 
	}, 
	{ text: 'Política de Segurança' 
	,handler: function(){ window.open('http://www.e-prev.com.br/controle_projetos/documentos/politica_seguranca_di.pdf'); } 
	} 

	]}}, 
	{ text: 'GIN - Investimentos' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GIN&c=p' } 
	}, 
	{ text: 'GJ - Jurídica' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GJ&c=p' } 
	}, 
	{ text: 'GRI - Relações Institucionais' 
	,handler: function(){ location.href='cad_intranet_div.php?div=GRI&c=p' } 
	}, 
	{ text: 'SG' 
	,handler: function(){ location.href='cad_intranet_div.php?div=SG&c=p' } 
	}, 
	{ text: 'AELETRO' 
	,handler: function(){ location.href='cad_intranet_div.php?div=AE&c=p' } 
	}, 
	{ text: 'Comitê de Qualidade' 
	,handler: function(){ location.href='cad_intranet_div.php?div=CQ&c=p' } 
	}, 
	{ text: 'Comitê Previdenciário' 
	,handler: function(){ location.href='cad_intranet_div.php?div=CP&c=p' } 
	}, 
	{ text: 'Comitê de Educação Ambiental' 
	,handler: function(){ location.href='cad_intranet_div.php?div=CEA&c=p' } 
	}, 
	{ text: 'Coral' 
	,handler: function(){ location.href='cad_intranet_div.php?div=COR&c=p' } 
	}, 
	{ text: 'Expansão' 
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
	{ text: 'Notícias' 
	, menu:{items:[
	{ text: 'Agência Estado' 
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
	{ text: 'Invertia Previdência' 
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
	{ text: 'Revista Época' 
	,handler: function(){ window.open('http://revistaepoca.globo.com/'); } 
	}, 
	{ text: 'Isto É' 
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
	{ text: 'Revista Você SA' 
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
	{ text: 'SENGE Previdência' 
	, menu:{items:[
	{ text: 'Cadastro' 
	,handler: function(){ location.href='lst_planos.php?p=7' } 
	}, 
	{ text: 'Contribuições' 
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
	{ text: 'Nova Inscrição' 
	,handler: function(){ location.href='senge_inscricao_etapa_0.php?sender=eprev' } 
	}, 
	{ text: 'Relatório de Acompanhamento' 
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
	{ text: 'Plano Único AES Sul' 
	, menu:{items:[
	{ text: 'Cadastro' 
	,handler: function(){ location.href='lst_planos.php?p=1' } 
	}, 
	{ text: 'Carta Julho/2007' 
	,handler: function(){ location.href='rel_aes_junho2007.php' } 
	} 

	]}}, 
	{ text: 'SINPRORS Previdência' 
	, menu:{items:[
	{ text: 'Cadastro' 
	,handler: function(){ location.href='lst_planos.php?p=8' } 
	}, 
	{ text: 'Pré Cadastro' 
	,handler: function(){ location.href='sinprors_pre_cadastro.php' } 
	}, 
	{ text: 'Contribuições' 
	, menu:{items:[
	{ text: 'Normal' 
	,handler: function(){ location.href='contribuicao_sinprors_normal.php' } 
	}, 
	{ text: 'Atrasadas' 
	,handler: function(){ location.href='contribuicao_sinprors_atraso.php' } 
	}, 
	{ text: 'Relatório' 
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
	{ text: 'Apresentações' 
	, menu:{items:[
	{ text: 'Sindicatos' 
	,handler: function(){ window.open('http://www.e-prev.com.br/controle_projetos/apresentacoes/apresentacao_instituidores.html'); } 
	} 

	]}}, 
	{ text: 'Avaliação de Competências' 
	, menu:{items:[
	{ text: 'Nova' 
	,handler: function(){ location.href='avaliacao.php?tipo=N&cd_capa=0' } 
	}, 
	{ text: 'Avaliações' 
	,handler: function(){ location.href='avaliacao.php' } 
	} 

	]}}, 
	{ text: 'Contra cheque' 
	,handler: function(){ location.href='cc.php' } 
	}, 
	{ text: 'Meu extrato' 
	,handler: function(){ location.href='meu_extrato.php' } 
	}, 
	{ text: 'Controle Cenário' 
	,handler: function(){ location.href='lst_controle_cenario.php' } 
	}, 
	{ text: 'Troca de senha' 
	,handler: function(){ location.href='troca_senha.php' } 
	}, 
	{ text: 'Troca de usuário' 
	,handler: function(){ location.href='index.php' } 
	}, 
	{ text: 'WEB mail' 
	,handler: function(){ window.open('http://webmail.eletroceee.com.br'); } 
	}, 
	{ text: 'Diagnóstico médico' 
	, menu:{items:[
	{ text: 'Consultas' 
	,handler: function(){ location.href='consulta_medica.php' } 
	}, 
	{ text: 'Tabelas médicas' 
	, menu:{items:[
	{ text: 'Sintomas' 
	,handler: function(){ location.href='lst_sintomas.php' } 
	} 

	]}} 

	]}}, 
	{ text: 'Parametros do sistema' 
	, menu:{items:[
	{ text: 'Parâmetros GA' 
	, menu:{items:[
	{ text: 'Fator atuarial' 
	,handler: function(){ location.href='lst_fator_atuarial.php' } 
	}, 
	{ text: 'Taxas simulação' 
	,handler: function(){ location.href='lst_taxas_simulacao.php' } 
	}, 
	{ text: 'Parâmetros atuariais' 
	,handler: function(){ location.href='lst_listas.php?cat=PRSI' } 
	} 

	]}}, 
	{ text: 'Parâmetros GRI' 
	, menu:{items:[
	{ text: 'Comunidade' 
	,handler: function(){ location.href='lst_listas.php?cat=CACS' } 
	}, 
	{ text: 'Distribuição' 
	,handler: function(){ location.href='lst_listas.php?cat=DACS' } 
	}, 
	{ text: 'Localização' 
	,handler: function(){ location.href='lst_listas.php?cat=LACS' } 
	}, 
	{ text: 'Participantes' 
	,handler: function(){ location.href='lst_listas.php?cat=PACS' } 
	}, 
	{ text: 'Seções do site' 
	,handler: function(){ location.href='lst_listas.php?cat=SSIT' } 
	}, 
	{ text: 'Segm. Empresariais' 
	,handler: function(){ location.href='lst_listas.php?cat=SACS' } 
	}, 
	{ text: 'Tipo de Evento' 
	,handler: function(){ location.href='lst_listas.php?cat=TEVN' } 
	} 

	]}}, 
	{ text: 'Parâmetros GJ' 
	, menu:{items:[
	{ text: 'Receb. de Audiências' 
	,handler: function(){ location.href='lst_agenda_outlook.php?dv=GJ' } 
	} 

	]}}, 
	{ text: 'Parâmetros GAP' 
	, menu:{items:[
	{ text: 'Fech. Empréstimos' 
	,handler: function(){ location.href='cad_periodos_emprestimo.php' } 
	}, 
	{ text: 'Forma de Solicitação' 
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
	{ text: 'Tipos de Manutenção' 
	,handler: function(){ location.href='lst_listas.php?cat=TPMN&div=GRI' } 
	}, 
	{ text: 'Tipo de Reclamação' 
	,handler: function(){ location.href='lst_listas.php?cat=TDAP' } 
	} 

	]}}, 
	{ text: 'Parâmetros GB' 
	, menu:{items:[
	{ text: 'Status / Atividade' 
	,handler: function(){ location.href='lst_listas.php?cat=STAT&div=GB' } 
	}, 
	{ text: 'Tipo Atividade' 
	,handler: function(){ location.href='lst_listas.php?cat=TPAT&div=GB' } 
	}, 
	{ text: 'Tipo Manutenção' 
	,handler: function(){ location.href='lst_listas.php?cat=TPMN&div=GB' } 
	} 

	]}}, 
	{ text: 'Parâmetros GC' 
	, menu:{items:[
	{ text: 'Resp. Cenário Legal' 
	,handler: function(){ location.href='lst_resp_cenario.php' } 
	}, 
	{ text: 'Status / Atividade' 
	,handler: function(){ location.href='lst_listas.php?cat=STAT&div=GC' } 
	}, 
	{ text: 'Tipo Atividade' 
	,handler: function(){ location.href='lst_listas.php?cat=TPAT&div=GC' } 
	} 

	]}}, 
	{ text: 'Parâmetros GI' 
	, menu:{items:[
	{ text: 'Prioridade / Tarefa' 
	,handler: function(){ location.href='lst_listas.php?cat=TPTR&div=GI' } 
	}, 
	{ text: 'Status / Atividade' 
	,handler: function(){ location.href='lst_listas.php?cat=STAT&div=GI' } 
	}, 
	{ text: 'Situação Equipamento' 
	,handler: function(){ location.href='lst_listas.php?cat=SITU&div=GI' } 
	}, 
	{ text: 'Tipo de Atividade' 
	,handler: function(){ location.href='lst_listas.php?cat=TPAT&div=GI' } 
	}, 
	{ text: 'Tipo de Manutenção' 
	,handler: function(){ location.href='lst_listas.php?cat=TPMN&div=GI' } 
	}, 
	{ text: 'Tipo de Equipmanento' 
	,handler: function(){ location.href='lst_listas.php?cat=EQUP&div=GI' } 
	} 

	]}}, 
	{ text: 'Parâmetros GF' 
	, menu:{items:[
	{ text: 'Status / Atividades' 
	,handler: function(){ location.href='lst_listas.php?cat=STAT&div=GB' } 
	}, 
	{ text: 'Tipo de Atividade' 
	,handler: function(){ location.href='lst_listas.php?cat=TPAT&div=GB' } 
	}, 
	{ text: 'Tipos de Manutenção' 
	,handler: function(){ location.href='lst_listas.php?cat=TPMN&div=GB' } 
	} 

	]}} 

	]}}, 
	{ text: 'Manutenções' 
	, menu:{items:[
	{ text: 'Arquivos CCheque' 
	, handler: function(){ location.href='arquivos_contra_cheque.php' }
	}, 
	{ text: 'Bloquetos' 
	, menu:{items:[
	{ text: 'Manutenção' 
	,handler: function(){ location.href='manut_bloquetos.php' } 
	}, 
	{ text: 'Lista' 
	,handler: function(){ location.href='lst_bloquetos.php' } 
	} 

	]}}, 
	{ text: 'Eleições' 
	, menu:{items:[
	{ text: 'Administração' 
	,handler: function(){ location.href='lst_adm_eleicoes.php' } 
	}, 
	{ text: 'Cadastro' 
	,handler: function(){ location.href='lst_eleitores.php' } 
	}, 
	{ text: 'Candidatos' 
	,handler: function(){ location.href='lst_candidatos.php' } 
	}, 
	{ text: 'Eleitores / Município' 
	,handler: function(){ location.href='lst_eleitores_municipio.php' } 
	}, 
	{ text: 'Etapas do Projeto' 
	,handler: function(){ location.href='lst_etapas_projeto.php?c=138' } 
	}, 
	{ text: 'Eleitores Votantes' 
	,handler: function(){ location.href='lst_votantes.php' } 
	}, 
	{ text: 'Histórico' 
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
	{ text: 'Valida Ass. 2ª Inst.' 
	,handler: function(){ location.href='valida_assin_seg_instancia.php' } 
	}, 
	{ text: 'Votação' 
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
	{ text: 'Relatórios Dinâmicos' 
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
	new Ext.Button( {text:'Início', handler: function(){ location.href='workspace.php'; }} )
	, new Ext.Button({text:'Atividades',menu:atividade})
	, new Ext.Button({text:'Cadastros',menu:cadastro})
	, new Ext.Button({text:'e-CRM',menu:ecrm})
	, new Ext.Button({text:'Gestão',menu:gestao})
	, new Ext.Button({text:'Intranet',menu:intranet})
	, new Ext.Button({text:'Planos',menu:planos})
	, new Ext.Button({text:'Serviços',menu:servicos})
	]
	});
});