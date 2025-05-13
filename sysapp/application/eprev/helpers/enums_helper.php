<?php
class enum_projetos_tarefa_checklist_tipo
{
	const CHECKLIST_WEB		= 1;
	const CHECKLIST_ORACLE	= 2;
}

class enum_projetos_eventos
{
    const AVALIACAO_DE_DESEMPENHO 			= 34;
    const PEDIDO_INSCRICAO_CEEEPREV 		= 35;
    const PEDIDO_INSCRICAO_AESPU 			= 36;
    const PEDIDO_CALCULO_TAXA_AESPU 		= 36;
    const PEDIDO_INSCRICAO_CRMPREV 			= 37;
    const SINPRORS_EMAIL_CONTRIBUICAO 		= 39;
    const RECADASTRO_GAP 					= 53;
    const SINTAE_EMAIL_CONTRIBUICAO 		= 54;
    const SENGE_EMAIL_CONTRIBUICAO 			= 40;
    const PROTOCOLO_INTERNO_ENVIO 			= 67;
    const PROTOCOLO_INTERNO_REENVIO			= 68;

    const FAMILIA_EMAIL_CONTRIBUICAO 		= 0;
}

class enum_public_planos
{
    const SEM_PLANO 			= 0;
    const UNICO 				= 1;
    const CEEE_PREV 			= 2;
    const CRM_PREV 				= 6;
    const SENGE_PREVIDENCIA 	= 7;
    const SINPRORS_PREVIDENCIA 	= 8;
    const FAMILIA_PREVIDENCIA 	= 9;
}

class enum_public_patrocinadoras
{
    const CEEE 			= 0;
    const RGE 			= 1;
    const AESSUL 		= 2;
    const CGTE 			= 3;
    const CRM 			= 6;
    const SENGE 		= 7;
    const SINPRO 		= 8;
    const FUNDACAO_CEEE = 9;
    const SINTAE 		= 10;
    const AFCEEE 		= 19;
}

class enum_public_codigos_cobrancas
{
	const CONTRIBUICAO_SINPRORS_PREV		= 2450;
	const RISCO_MORTE_SINPRORS 				= 2451;
	const RISCO_INVALIDEZ_SINPRORS 			= 2452;

	const CONTRIBUICAO_SINPRORS_PREV_CC		= 2460;
	const RISCO_MORTE_SINPRORS_CC 			= 2461;
	const RISCO_INVALIDEZ_SINPRORS_CC 		= 2462;

	const CONTRIBUICAO_SINPRORS_FOLHA 		= 2480;
	const RISCO_MORTE_SINPRORS_FOLHA 		= 2481;
	const RISCO_INVALIDEZ_SINPRORS_FOLHA 	= 2482;
}

class enum_cenario_secao
{
	const AGENDA 					= 1;
	const CALENDARIO_ANUAL 			= 2;
	const CAPA 						= 3;
	const EDICOES_ANTERIORES 		= 4;
	const LEGISLACAO_INTEGRA 		= 5;
	const PONTO_VISTA 				= 6;
}

class enum_projetos_contribuicao_controle_tipo
{
	const PRIMEIRO_PAGAMENTO_BDL 					= '1PBDL';
	const PRIMEIRO_PAGAMENTO_DEBITO_CONTA_CORRENTE 	= '1PDCC';
	const PAGAMENTO_MENSAL_BDL 						= 'PMBDL';
	const PAGAMENTO_MENSAL_DEBITO_CONTA_CORRENTE 	= 'PMDCC';
	const COBRANCA_ATRASADO_BDL 					= 'COBDL';
	const COBRANCA_ATRASADO_DEBITO_CONTA_CORRENTE 	= 'CODCC';
	const COBRANCA_ATRASADO_DESCONTO_FOLHA 			= 'COFOL';
	const COBRANCA_ATRASADO_PRIMEIRO_PAGAMENTO		= 'COB1P';
}

class enum_projetos_documento_recebido_tipo
{
	const CENTRAL_ATENDIMENTO 	= 1;
	const FAX 					= 2;
	const MALOTE 				= 3;
	const EMAIL 				= 4;
}

/**
 * INDICADORES QUE COMPOEM O IGP, NÃO DEVEM SER EXCLUÍDOS E DEVEM MANTER ESSES CÓDIGOS
 */
class enum_indicador
{
	//IGP - 2016
	//COMUNICACAO
	const IGP_COMUNICACAO_SAT_PARTICIPANTES_POS_VENDA     = 300;
	const IGP_COMUNICACAO_SAT_PARTICIPANTES_INSTITUIDORES = 301;

	//RH
	const IGP_RH_RETORNO_POR_COLABORADOR = 302;
	const RH_HORAS_EXTRAS_REALIZADAS_X_HORAS_EXTRAS_ORCADAS = 318;

    // AREA 
    // GAP
    const AREA_INGRESSOS_PATROCINADORAS = 166;                                                              // < ingressos
    const AREA_INGRESSOS_INSTITUIDORES  = 167;                                                              // < ingressos
    const AREA_PARTICIPANTES            = 168;                                                              // < nº partc ceee
    const AREA_SENHAS_LIBERADAS         = 169;                                                              // < senhas
    const AREA_DESLIGAMENTO_SOLIC       = 170;                                                              // < deslig
    const AREA_DESLIGAMENTO_INADI       = 171;                                                              // < deslig
    const AREA_DESLIGAMENTO_OBITO       = 172;                                                              // < deslig
    const AREA_CENTRAL_QUANTIDADE       = 173;                                                              // < central
    const AREA_CENTRAL_TP_ESPERA        = 174;                                                              // < central
    const AREA_CENTRAL_TP_ATENDIMENTO   = 175;                                                              // < central
    const AREA_TELE_RECEBIDAS           = 176;                                                              // < tele
    const AREA_TELE_TRANSF              = 177;                                                              // < tele
    const AREA_TELE_TRANSF_NAO_ATEND    = 178;                                                              // < tele
    const AREA_TELE_CALL_CENTER         = 179;                                                              // < tele
    const AREA_TELE_ATENDIMENTO         = 180;                                                              // < tele
    const AREA_ATEND_EMAIL              = 181;                                                              // < e-mail
    const AREA_HABILITACOES             = 182;  
    
    // GC
    const AREA_ORIGINAIS_RETIFICACOES_DCTF = 221;
    const AREA_ORIGINAIS_RETIFICACOES_DIRF = 222;

    // PODER 
    const PODER_EQUILIBRIO_ATUARIAL_PLANO = 153;                                                            // < equilibrio
    const PODER_PERFOM_CARTEIRA_INVESTIMENTO = 154;                                                         // < rentab
    const PODER_PARTICIPANTES_PATRO = 155;                                                                  // < partic
    const PODER_PARTICIPANTES_INST = 156;                                                                   // < partic-inst
    const PODER_REALIZACAO_ORCAMENTARIA = 157;                                                              // < var orc
    const PODER_CUSTO_ESTRUTURA_OPERACIONAL = 158;                                                          // < custo estrut.
    const PODER_NAO_CONFORMIDADES_ACOES_CORRETIVAS = 159;                                                   // < prop ac
    const PODER_NAO_CONFORMIDADES_ACOES_IMPLEMENTADAS = 160;                                                // < impl ac
    const PODER_ACOES_PREVENTIVAS = 161;                                                                    // < sap
    const PODER_CERTIFICACAO_ISO = 162;                                                                     // < iso
    const PODER_RESULTADO_SEMESTRE_1 = 163;
    const PODER_RESULTADO_SEMESTRE_2 = 164;

	const IGP = 16;                                                                                         // < igp

	// igp
	const RPP = 3;                                                                                          // < rpp
	const RECLAMACAO = 4;                                                                                   // < reclamacao
	const BENEFICIO = 5;                                                                                    // < beneficio_erro
	const CALCULO_INICIAL = 6;                                                                              // < calculo_inicial
	const CUSTO_ADMINISTRATIVO = 7;                                                                         // < custo_administrativo
	const EQUILIBRIO = 8;                                                                                   // < equilibrio
	const INFORMATICA = 11;                                                                                 // < informatica
	const REALIZACAO_ORCAMENTARIA = 9;                                                                      // < variacao_orcamentaria
	const AVALIACAO = 14;                                                                                   // < avaliacao
	const SATISFACAO_COLABORADOR = 13;                                                                      // < satisfacao_colab
	const SATISFACAO_PARTICIPANTE = 15;                                                                     // < satisfacao_partic
	const TREINAMENTO = 10;                                                                                 // < treinamento
	const PARTICIPANTE = 12;                                                                                // < participante
	const RENTABILIDADE_CI = 17;                                                                            // < rentabilidade_ci

	const RECLAMATORIA_CUSTO_MEDIO_INDENIZACAO_POR_DEMANADANTE = 296;

	// EXPANSAO
	const EXP_CAPTACAO_LIQUIDA = 429;																	
	const EXP_VALOR_CONTRATADO_MEDIA = 228;																	// < exp_valor_contratado_media
	const EXP_VALOR_CONTRATADO       = 229;																	// < exp_valor_contratado
	const EXP_VENDA                  = 230;																	// < exp_valor_contratado
	const EXP_VOLUME_DE_RECURSOS_FINANCEIROS_CONTRATADOS = 274;
	const EXP_NUMERO_EMPRESA = 275;
	const EXP_NUMERO_DE_VENDAS_X_INSCRICOES_X_INGRESSOS = 276;
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_POTENCIAL_CEEEPREV = 306;
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_POTENCIAL_CGTEE = 307;
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_POTENCIAL_SINPRO = 308;	
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_POTENCIAL_FAMILIA = 310;
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_POTENCIAL = 313;
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_POTENCIAL_SINTEC = 314;
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_POTENCIAL_SINTAE = 315;
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_POTENCIAL_SINTEE = 316;				
	const EXP_VOLUME_DE_RECURSOS_FINANCEIROS_CONTRATADOS_INPEL = 323;						
	const EXP_VOLUME_DE_RECURSOS_FINANCEIROS_CONTRATADOS_CRM = 324;						
	const EXP_VOLUME_DE_RECURSOS_FINANCEIROS_CONTRATADOS_FAMILIA = 325;					
	const EXP_VOLUME_DE_RECURSOS_FINANCEIROS_CONTRATADOS_SINPRO = 326;					
	const EXP_VOLUME_DE_RECURSOS_FINANCEIROS_CONTRATADOS_SENGE = 327;
	const EXP_VOLUME_DE_RECURSOS_FINANCEIROS_CONTRATADOS_CEEEPREV = 328;
	const EXP_VOLUME_DE_RECURSOS_FINANCEIROS_CONTRATADOS_CGTEE = 334;
	const EXP_VOLUME_DE_RECURSOS_FINANCEIROS_CONTRATADOS_CERAN = 337;
	const EXP_VOLUME_DE_RECURSOS_FINANCEIROS_CONTRATADOS_MUNICIPIOS = 433;
	const EXP_VOLUME_DE_RECURSOS_FINANCEIROS_CONTRATADOS_IEABPREV = 443;
	const EXP_VOLUME_DE_RECURSOS_FINANCEIROS_CONTRATADOS_FOZ = 338;		
	const EXP_APORTES_PORTABILIDADE = 342;	
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_PROJETADO_UNICO = 348;
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_PROJETADO_CEEEPREV = 349;
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_PROJETADO_SINPRO = 350;
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_PROJETADO_FAMILIA = 351;
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_PROJETADO_CERANPREV = 352;	
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_PROJETADO_FOZPREV = 353;
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_PROJETADO_INPEL = 357;
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_PROJETADO_SENGE = 355;
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_PROJETADO_CRMPREV = 356;
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_PROJETADO_MUNICIPIOS = 432;	
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_PROJETADO_IEABPREV = 442;
	const EXP_NUMERO_DE_ADESAO_X_NUMERO_PROJETADO_CONSOLIDADO = 358;	
	const EXP_NUMERO_DE_VENDAS_x_INGRESSOS = 354;
	const EXP_SATISFACAO_COM_ACOES_E_VENDAS = 363;
	
	// informatica
	const INFO_SATISFACAO = 21;																				// < info_satisfacao
	const INFO_INDISPONIBILIDADE = 22;																		// < info_indisp
	const INFO_PETI = 24;																					// < info_peti
	const INFO_BACKUP = 23;																					// < info_backup
	const INFO_ATIVIDADE = 19;																				// < info_atividade

	// atuarial
	const ATUARIAL_EAP_PLANO = 36;		//(PLANO) 													    	// < atuarial_eap_plano (EAP-RES)
	const ATUARIAL_CEEE = 26;																				// < atuarial_eap_ceee (EAP-CEEE-2010)
	const ATUARIAL_RGE = 27;																				// < atuarial_eap_rge (EAP-RGE-2010)
	const ATUARIAL_AESSUL = 28;																				// < atuarial_eap_aes (EAP-AES-2010)
	const ATUARIAL_CGTEE = 29;																				// < atuarial_eap_cgtee (EAP-CGTEE-2010)
	const ATUARIAL_CEEEPREV_BD = 30;																		// < atuarial_eap_ceeeprev_bd (EAP-CEEEPREV(BD)-2010)
	const ATUARIAL_SINPRO = 34;																				// < atuarial_eap_sinpro (EAP-SINPRO-2010)
	const ATUARIAL_SENGE = 33;																				// < atuarial_eap_senge (EAP-SENGE-2010)
	const ATUARIAL_CRMPREV = 32;																			// < atuarial_eap_crmprev (EAP-CRMPREV-2010)
	const ATUARIAL_CEEEPREV_CD = 31;																		// < atuarial_eap_ceeeprev_cd (EAP-CEEEPREV(CD)-2010)
	const ATUARIAL_EAP_CONSOLIDADO = 25;		       //CONSOLIDADO 										// < atuarial_eap (EAP-2010)
	const ATUARIAL_FAMILIA = 149;																			// < atuarial_eap_familia (EAP-FAMÍLIA-2010)
    const ATUARIAL_EXIGENCIA_PREVIX_EM_APROVACAO_DE_NOVO_PLANO = 369;
    
    const ATUARIAL_EXIGENCIA_PREVIC_EM_APROVACAO_DE_NOVO_REGULAMENTO = 397;

    const ATUARIAL_EXIGENCIA_PREVIC_EM_APROVACAO_DE_REGULAMENTO = 444;

	const ATUARIAL_PLANO_RGE = 292;	
	const ATUARIAL_PLANO_AES_SUL = 293;	
	const ATUARIAL_PLANO_CGTEE = 294;	
	const ATUARIAL_PLANO_CEEEPREV_MIGRADOS = 295;
	const ATUARIAL_PLANO_CONSOLIDADO = 317;		
	const ATUARIAL_DIVULGACAO_EXTRATO_PRAZO = 445;
	const ATUARIAL_ATENDIMENTO_PRAZO_TRANSFERENCIA_PLANO = 449;

	// administrativo
	const RH_HORA_HOMEM_TREINAMENTO = 132;																	// < administrativo_hhtr
	const RH_EXECUCAO_PLANO_TREINAMENTO = 133;																// < administrativo_ept
	const RH_EXECUCAO_PLANO_TREINAMENTO_HORA = 147;															// < administrativo_ept_hora (PT-HOR-2010)
	const RH_ATINGIMENTO_OBJETIVO_TREINAMENTO_SUPERIOR = 35;                                                // < administrativo_resul_superior (AV RES SUP)
	const RH_ATINGIMENTO_OBJETIVO_TREINAMENTO_COLABORADOR = 37;                                             // < administrativo_resul_colaborador (AV RES COL)
	const RH_ESCOLARIDADE_ATUAL = 38;                                                                       // < administrativo_escolari_atual (Escolaridade)
	const RH_EVOLUCAO_PERFIL_ESCOLARIDADE = 39;                                                             // < administrativo_evo_per_escolar (Hist Escol)
	const RH_SATISFACAO_COLABORADOR = 40;																	// < administrativo_sat_colaborador (SAT)
	const RH_PONTUACAO_MEDIA_AVALIACAO_DESEMPENHO = 41;                                                     // < administrativo_aval_desempenho (AV DES)
	const RH_ORCAMENTO_REALIZADO_INVESTIDO_TREINAMENTO = 42;												// < administrativo_treina_orca (TR ORÇ)
	const RH_ABSENTEISMO = 134;																				// < administrativo_absenteismo (ABS-2010)
	const RH_ROTATIVIDADE = 135;																			// < administrativo_rotatividade (ROT-2010)
	const RH_HORA_EXTRA_VALOR = 136;																		// < administrativo_he_valor (HE VLR-2011)
	const RH_HORA_EXTRA_QUANTIDADE = 137;																	// < administrativo_he_qtd (HE QT-2010)
	const RH_VALOR_CONTRATO_PRESTADOR_SERVICO = 138;														// < administrativo_contrato (VLR-CONT-10)
	const RH_VALOR_REALIZADO_COMPRA_SUPRIMENTO = 139;														// < administrativo_suprimento (SUPR-11)
	const RH_VALOR_REALIZADO_IMPRESSAO = 140;																// < administrativo_impressao (IMPR-10)
	const RH_MEDIA_AVALIACAO_FORNECEDOR_MES = 141;															// < administrativo_aval_fornecedor (AVAL.FORNEC-10)
	const RH_AREA_NAO_LOCADA = 142;																			// < administrativo_area_nloc (ÁREA LOCADA-2010)
	const RH_ITENS_ERRADOS_INVENTARIO = 143;																// < administrativo_inventario (INVENT)
	const RH_MEDIA_AVALIACAO_CONTRATACAO_SERVICO_SEMESTRE = 144;											// < administrativo_aval_fornecedor (AVAL-CONT)
    const RH_HORA_TREINAMENTO_PLANEJADO_X_NAO = 152;                                                        // < administrativo_ht_planejado (TR EXT-2011
    const RH_TOTAL_DIGITALIZADO = 184;
    const RH_DOCUMENTOS_DIGITALIZADOS = 185;
    const RH_DOCUMENTOS_DIGITALIZADOS_ACUM = 186;
    const RH_CRESCIMENTO_PLANEJAMENTO_ESTRATEGICO = 244;
    const RH_ACOES_CORRETIVAS_IMPLEMENTADAS_FORA_DO_PRAZO = 257; //Ações Corretivas implementadas fora do prazo
    const RH_PREPOSICAO_DE_ACOES_CORRETIVAS_FORA_DO_PRAZO = 258; //Proposição de ações corretivas fora do prazo
    const RH_ACOES_PREVENTIVAS = 259; //Ações Preventivas
    const RH_NUMERO_CORRESPONDECIAS = 260; //Nº de Correspondências
    const RH_CUSTO_CORRESPONDECIAS = 261; //Custo Correspondências
    const RH_PROTOCOLO_FORA_DO_PRAZO = 284;
    const RH_RETORNO_POR_PESSOA = 339;
    const RH_ROTATIVIDADE_NOVO = 439;

    const RH_MEDIA_AVALIACAO_CONTRATACAO_SERVICO_ANUAL = 381;

    const ADMINISTRATIVO_CORRESPONDENCIAS_DEVOLVIDAS = 401;
    const ADMINISTRATIVO_BENS_NAO_LOCALIZADOS = 430;


	// atendimento
	const ATENDIMENTO_PARTICIPANTE = 43;																	// < atend_participante(NUM-PARTIC)
	const ATENDIMENTO_PARTICIPANTES_ATIVOS_E_CTP = 44;														// < atend_ativo(% PART-ATIVOS)
	const ATENDIMENTO_DESLIGAMENTOS_DE_PARTICIPANTES_DOS_PLANOS = 46;										// < atend_desligamento(% DESLIG)
	const ATENDIMENTO_TEMPO_MEDIO_DE_ATENDIMENTO_PESSOAL = 49;												// < atend_pessoal(TP-AT)
	const ATENDIMENTO_TEMPO_MEDIO_DE_ESPERA_CALL_CENTER = 51;												// < atend_call(TP-ESP-CALL)
	const ATENDIMENTO_TEMPO_MEDIO_DE_ESPERA = 48;															// < atend_espera(TP-ESP)
	const ATENDIMENTO_CALL_CENTER_LIGACOES_TRANSFERIDAS_E_NAO_ATENDIDAS = 53;								// < atend_call_na(TRANSF Ñ ATEND-CALL)
	const ATENDIMENTO_TEMPO_MEDIO_DE_ATENDIMENTO_CALL = 54;													// < atend_tma_call(TP-AT-CALL)
	const ATENDIMENTO_SENHAS_LIBERADAS = 56;																// < atend_senha(SENHAS-LIB)
	const ATENDIMENTO_INDICE_DE_RECLAMACAO = 57;															// < atend_indice_recl(RECL)
	const ATENDIMENTO_PERCENTUAL_DE_ATENDIMENTOS_POR_EMAILS = 58;											// < atend_email(AT-E-MAIL)
	const ATENDIMENTO_PARTICIPANTES_COM_EMAILS_CADASTRADOS = 59;											// < atend_partic_email(E-MAIL)
	const ATENDIMENTO_ENDERECOS_DESATUALIZADOS = 60;														// < atend_end_desat(% END)
	const ATENDIMENTO_PECULIOS_SEM_DESIGNACAO = 61;															// < atend_peculio(% PECULIOS)
	const ATENDIMENTO_TOTAL_DE_CONSULTAS_AO_AUTO_ATENDIMENTO_CALL_CENTER_INTERNET = 55;						// < atend_consulta_auto(CONS-AUTO)
	const ATENDIMENTO_LIGACOES_RECEBIDAS_QUE_FORAM_TRANSFERIDAS_PARA_OS_ATENDENTES_CALL_CENTER = 50;		// < atend_ligacao_transf(LIG-CALL-TRANSF)
	const ATENDIMENTO_INCONSISTENCIAS_NO_BANCO_DE_DADOS = 64;												// < atend_inconsistencia_bd(INCONS-BCO)
	const ATENDIMENTO_PARTICIPANTES_ATIVOS_NAS_PATROCINADORAS_INSTITUIDORAS = 45;							// < atend_ativo_pi (%PART-AT-PATROC)
	const ATENDIMENTO_TOTAL_DE_PARTICIPANTES_ATENDIDOS_NA_CENTRAL = 47;										// < atend_central (AT-CENTRAL)
	const ATENDIMENTO_TOTAL_DE_LIGACOES_RECEBIDAS_NO_0800 = 52;												// < atend_teleatend (LIG-AT-TELE)
	const ATENDIMENTO_PERCENTUAL_DE_DOCUMENTOS_FALTANTES = 62;												// < atend_doc_faltante (DOC-FALT)
	const ATENDIMENTO_HABILITACOES_INCORRETAS = 63;															// < atend_habilit_incorr (HABIL-IND)
	const ATENDIMENTO_RECADASTRAMENTO_DE_ASSISTIDOS = 65;                                                   // < atend_recad_assistidos (RECAD)
	const ATENDIMENTO_DE_SATISFACAO_DO_CLIENTE_COM_O_ATENDIMENTO = 66;                                      // < atend_sat_cli_atendimento (SATISF-ATEND)
	const ATENDIMENTO_DE_SATISFACAO_ENCONTRO_MAIS_VIDA = 67;                                                // < atend_enc_vida (SAT-ENC-VIDA)
	const ATENDIMENTO_SATISFACAO_COM_O_EVENTO_QUALIDADE_DE_VIDA = 68;										// < atend_qualidade_vida (SAT-QUAL-VIDA)
	const ATENDIMENTO_SATISFACAO_COM_O_EVENTO_ENCONTRO_APOSENTADOS = 69;									// < atend_sat_aposentado (SAT-APOS-PENS)
	const ATENDIMENTO_ATENDIMENTOS = 70;																	// < atend_ssocial_indiv (ATEND INDIV)
	const ATENDIMENTO_SATISFACAO_COM_O_CURSO_PREPARACAO_PARA_APOSENTADORIA = 71;							// < atend_sat_ppa (PPA)
	const ATENDIMENTO_SATISFACAO_COM_O_CURSO_PLANEJAMENTO_FINANCEIRO = 72;									// < atend_sat_planej_fin (PLAN-FIN)
	const ATENDIMENTO_CAMPANHAS_DE_VACINACAO = 73;                                                          // < atend_campanhas_vacinacao (VACINA)
	const ATENDIMENTO_PROGRAMA_DE_EDUCACAO_AMBIENTAL = 74;
	const ATENDIMENTO_SEMINARIO_GAUCHO_DE_SEGURIDADE = 203;
	const ATENDIMENTO_CALL_CENTER_ATENDIMENTO_SEM_FILA = 204;
	const ATENDIMENTO_TEMPO_MEDIO_RESPOSTA_EMAIL = 241;
	const ATENDIMENTO_COM_RETORNO = 242;
	const ATENDIMENTO_NUMERO_INGRESSO_NUMERO_DESLIGADOS = 243;
	const ATENDIMENTO_NUMERO_INGRESSO_NUMERO_DESLIGADOS_B = 438;
	const ATENDIMENTO_NUMERO_ATENDIMENTOS = 329;
	const ATENDIMENTO_ATENDIMENTOS_POR_PROGRAMA = 330;
	const ATENDIMENTO_RECLAMACOES_PROCEDENTES = 331;
	const ATENDIMENTO_RECLAMACOES_FORA_DE_PRAZO = 332;
	const ATENDIMENTO_RECLAMACOES_NAO_TRATADAS = 333;
	const ATENDIMENTO_RETENCAO_CLIENTE = 396;
	const ATENDIMENTO_RETENCAO_CLIENTE_VALORES = 407;
	const ATENDIMENTO_CADASTRO_ATIVIDADES = 441;
	const ATENDIMENTO_AVALIACAO_EXP_ATENDIMENTO = 447;

	// beneficio
	const BENEFICIO_INCORRECOES_NO_VALOR_DO_BENEFICIO_LIQUIDO = 75;											// < beneficio_incorrecao(LIQ-2010)
	const BENEFICIO_BENEFICIOS_COM_O_CALCULO_INICIAL_INCORRETO = 76;										// < beneficio_inicial_incorreto (CÁLCULO INIC-2010)
	const BENEFICIO_BENEFICIOS_PAGOS_A_PARTIR_DA_SOLICITACAO_EM_MAIS_DE_30_DIAS = 77;						// < beneficio_pg_30dias (BEN30-2010)
	const BENEFICIO_PAGAMENTO_DE_PECULIO_EM_MAIS_DE_15_DIAS = 78;											// < beneficio_pg_peculio_15dias (PEC15-2010)
	const BENEFICIO_PAGAMENTO_DE_RESTITUICOES_EM_MAIS_DE_30_DIAS = 79;										// < beneficio_pg_restituicoes_30dias (REST30-2010)
	const BENEFICIO_GERACAO_DA_RECEITA_INCORRECOES_NA_GERACAO_DAS_CONTRIBUICOES_SENGE_PREVIDENCIA = 80;		// < beneficio_inc_senge (INC-INSTIT-2010)
	const BENEFICIO_GERACAO_DA_RECEITA_INCORRECOES_NA_GERACAO_DAS_CONTRIBUICOES_SINPRORS_PREVIDENCIA = 81;  // < beneficio_inc_sinpro (INC-SINPRO-2010)
	const BENEFICIO_GERACAO_DA_RECEITA_INCORRECOES_NA_GERACAO_DAS_CONTRIBUICOES_SINTAE = 82;				// < beneficio_inc_sintae (INC-SINTAE-2010)
	const BENEFICIO_GERACAO_DA_RECEITA_INCORRECOES_NA_GERACAO_DAS_CONTRIBUICOES_FAMILIA = 148;				// < beneficio_inc_familia (INC-FAMILIA-2010)
	const BENEFICIO_GERACAO_DA_RECEITA_INCORRECOES_NA_GERACAO_DAS_CONTRIBUICOES_TCHE = 359;                 // < beneficio_inc_tche (INC-TCHE-2017)
	const BENEFICIO_GERACAO_DA_RECEITA_INCORRECOES_NO_CALCULO_DAS_CONTRIBUICOES = 83;						// < beneficio_inc_patroc (INC-PATROC-2010)
	const BENEFICIO_GERACAO_DA_RECEITA_INCORRECOES_NO_GERACAO_DAS_CONTRIBUICOES = 233;
	const BENEFICIO_GERACAO_DA_RECEITA_INCORRECOES_NA_GERACAO_DAS_CONTRIBUICOES_FFP = 426;
	const BENEFICIO_GERACAO_DA_RECEITA_INCORRECOES_NA_GERACAO_DAS_CONTRIBUICOES_SINTEP = 346;		
	const BENEFICIO_GERACAO_DA_RECEITA_INCORRECOES_NA_GERACAO_DAS_CONTRIBUICOES_ABRH = 425;
	const BENEFICIO_GERACAO_DA_RECEITA_INCORRECOES_NA_GERACAO_DAS_CONTRIBUICOES_CEAPE = 427;
	const BENEFICIO_GERACAO_DA_RECEITA_INCORREÇÕES_NA_GERAÇÃO_DAS_CONTRIBUIÇÕES_SINDHA = 428;




	const BENEFICIO_INCORRECOES_NO_CALCULO_DAS_CONTRIBUICOESCRM = 84;										// < beneficio_inc_crm (INC-CRM-2010)
	const BENEFICIO_INCORRECOES_NO_CALCULO_DAS_CONTRIBUICOESCGTEE = 85;										// < beneficio_inc_cgtee (INC-CGTEE-2010)
	const BENEFICIO_INCORRECOES_NO_CALCULO_DAS_CONTRIBUICOESAES_SUL = 86;									// < beneficio_inc_aessul (INC-AES-2010)
	const BENEFICIO_INCORRECOES_NO_CALCULO_DAS_CONTRIBUICOESRGE = 87;										// < beneficio_inc_rge (INC-RGE-2010)
	const BENEFICIO_INCORRECOES_NO_CALCULO_DAS_CONTRIBUICOESCEEEPREV = 88;									// < beneficio_inc_ceeeprev (INC-CEEEPREV-2010)
	const BENEFICIO_INCORRECOES_NO_CALCULO_DAS_CONTRIBUICOESCEEE = 89;										// < beneficio_inc_ceee (INC-CEEE-2010)
	const BENEFICIO_INCORRECOES_NO_CALCULO_DAS_CONTRIBUICOESINPEL = 291;
	const BENEFICIO_INCORRECOES_NO_CALCULO_DAS_CONTRIBUICOESCERAN = 344;
	const BENEFICIO_INCORRECOES_NO_CALCULO_DAS_CONTRIBUICOESFOZ = 345;
	const BENEFICIO_INCORRECOES_GERACAO_CONTRIBUICOES_SEPRORGS = 403; 										// < beneficio_inc_seprorgs

	// controladoria
	const CONTROLADORIA_NORMAS_IMPLEMENTADAS_FORA_PRAZO_LEGAL = 91;											// < controladoria_norma
	const CONTROLADORIA_SATISFACAO_INFORMATIVO_GERENCIAL = 90;												// < controladoria_inf_gerencial (SAT INF)
    const CONTROLADORIA_SATISFACAO_INFORMATIVO_GERENCIAL_NOVO_2014 = 223;
    const CONTROLADORIA_EQUILIBRIO_ATUARIAL_BD = 231;
    const CONTROLADORIA_COBERTURA_BENEFICIO_CD = 232;
    const CONTROLADORIA_DIV_CADERNO_ACOMP_RESULTADOS = 234;
    const CONTROLADORIA_DIV_RESULTADO_CARTEIRA_INVEST = 235;
    const CONTROLADORIA_QUANT_RISCOS_MITIGADOS = 236;
    const CONTROLADORIA_ALT_REG_SOLICITACAO_PREVIC = 237;
    const CONTROLADORIA_TEMPO_ELABORACAO_NOVO_REGULAMENTO = 238;
    const CONTROLADORIA_PRAZO_ATENDIMENTO_SOLICITACOES_PATROC = 239;
    const CONTROLADORIA_ATENDIMENTO_REQUISICAO_NO_PRAZO = 240;
    const CONTROLADORIA_VARIACAO_ORCAMENTARIA = 299;
    const CONTROLADORIA_IMPACTO_DAS_ACOES_JUDICIAIS_NAS_RESERVAS = 303;
    const CONTROLADORIA_CRESCIMENTO_PATRIMONIAL_EM_RELACAO_AO_SEGMENTO = 304;
    const CONTROLADORIA_OBRIGACOES_LEGAIS = 319;
    const CONTROLADORIA_CERTIFICADO_ISO = 320;
    const CONTROLADORIA_COBERTURA_BENEFICIO_CD_INPEL = 360;
    const CONTROLADORIA_COBERTURA_BENEFICIO_CD_CERAN = 361;
    const CONTROLADORIA_COBERTURA_BENEFICIO_CD_FOZ = 362;
    const CONTROLADORIA_COBERTURA_BENEFICIO_CD_MUNICIPIO = 452;
    const CONTROLADORIA_COBERTURA_ACOM_LEGISLACAO = 364;
    const CONTROLADORIA_PARTICIPACAO_NO_SEGMENTO = 365;
    const CONTROLADORIA_CUMPRIMENTO_PROJETO_MODERNIDADE = 399;
    const CONTROLADORIA_DIVULGACAO_RESULTADO_CI_PRAZO = 446;
    const CONTROLADORIA_IMPLEMENTACAO_PROJETOS = 448;
    const CONTROLADORIA_CRESCIMENTO_PATRIMONIAL = 450;

	// financeiro
	const FINANCEIRO_EXECUCAO_TOTAL_DAS_PATROCINADORAS = 93;												// < financeiro_patrocinadora
	const FINANCEIRO_EXECUCAO_TOTAL_PARTICIPANTE = 94;														// < financeiro_execucao_participante (ARRECb2010)
	const FINANCEIRO_EXECUCAO_FINANCEIRA_TITULOS_DEVOLVIDOS_DE_ITENS = 92;									// < financeiro_exec_fin (ExFin-2010)
	const FINANCEIRO_NAO_PAGOS_NO_MES_PARTICIPANTE = 95;													// < financeiro_deb_participantes (DEB_Partic-2010)
	const FINANCEIRO_ATRASOS_ADMINISTRATIVO_CONTRATOS = 97;													// < financeiro_adm_contratos (EMPb-2010)
	const FINANCEIRO_COBERTURAS_PAGAS_FORA_DO_PRAZO = 98;													// < financeiro_seguro (SEG-2010)
	const FINANCEIRO_ATRASOS_EM_VOLUME_FINANCEIRO_TOTAL = 96;												// < financeiro_atraso_volume_fin_total (EMPa-2010)
	const FINANCEIRO_INADIMPLENCIA_PARTICIPANTES = 266;	
	const FINANCEIRO_INADIMPLENCIA_PATROCINADORAS = 267;	
	const FINANCEIRO_INADIMPLENCIA_PREVIDENCIARIA = 398;	
	const FINANCEIRO_SALDO_MEDIO_CONTAS_CORRENTES = 268;
	const FINANCEIRO_DCTF_ORIGINAIS_X_RETIFICACOES = 269;
	const FINANCEIRO_DIRF_ORIGINAIS_X_RETIFICACOES = 270;
	const FINANCEIRO_ENTREGA_OBRIGACOES_FORA_DO_PRAZO = 271;
	const FINANCEIRO_CONTAS_COM_SALDOS_INCONSISTENTES = 272;
	const FINANCEIRO_VARIACAO_PATRIMONIAL = 273;
	const FINANCEIRO_EXTRATOS_INCONSISTENTES_INST = 343;
	const FINANCEIRO_NUMERO_INGRESSOS_x_PAGAMENTO = 347;
	const FINANCEIRO_DCTF_ORIGINAIS_X_RETIFICACOES_ERROS_OPERACIONAIS = 366;
	const FINANCEIRO_PAGAMENTOS_EFETIVOS_ERRO_OPERACIONAL = 367;
    const FINANCEIRO_DIRF_ORIGINAIS_X_RETIFICACOES_ERROS_OPERACIONAIS = 368;
    const FINANCEIRO_DIVULGACOES_EXTRATOS_PLANOS_PRAZO = 390;
    const FINANCEIRO_DIVULGACOES_COTA = 406;
    const FINANCEIRO_VARIACAO_RECEITA_DIRETA = 451;

    const FINANCEIRO_CONTROLE_ATRASOS_PATROCINADORA = 414;
    const FINANCEIRO_PP_PR_INCONSISTENCIA = 437;

	// relações institucionais
	const RI_MIDIA_FAVORAVEL_X_MIDIA_DESFAVORAVEL = 113;													// < ri_midia_fav_des (MID FAV X DESF10)
	const RI_RECLAMACOES_COM_LINGUAGEM_INADEQUADA = 117;													// < ri_linguagem_inad (RCL.L.INAD.10)
	const RI_SATISFACAO_COM_FUNDACAO_EM_REVISTA = 112;                                                      // < ri_sat_fun_revista (SAT DIV EXT Revista)
	const RI_NUMERO_DE_MIDIA_ESPONTANEA = 114;                                                              // < ri_num_midia_espontanea (MIDIA 10)
	const RI_RETRABALHO = 115;																			    // < ri_retrabalho (%RET1)
	const RI_CUMPRIMENTO_PLANEJAMENTO_DE_MARKETING = 116;                                                   // < ri_cumprimento_planej_marketing (P xREAL)
	const RI_INFORMACOES_DIVULGADAS_FORA_DO_PRAZO = 118;                                                    // < ri_info_divul_fora_prazo (PZDIV)
	const RI_SATISFACAO_COM_SITE_FUNDACAO_CEEE = 119;                                                       // < ri_sat_site (SAT DIV EXT)
	const RI_SATISFACAO_COM_O_BOLETIM_FUNDACAO_ON_LINE = 120;                                               // < ri_sat_boletim_online (SAT DIV EXT On Line)
	const RI_SATISFACAO_COM_A_DIVULGACAO_INTERNA = 121;                                                     // < ri_sat_divul_interna (SAT DIV INT)
	const RI_EMPRESAS_PROSPECTADAS = 122;                                                                   // < ri_empresas_pospectadas (PR.CLIENTES)
	const RI_PUBLICO_PRESENTE_EM_EVENTOS_INTERNOS = 123;                                                    // < ri_pub_eventos_internos (EVINTPUB)
	const RI_PUBLICO_PRESENTE_EM_EVENTOS_EXTERNOS = 124;                                                    // < ri_pub_eventos_externos (EVEXTPUB)
	const RI_SATISFACAO_COM_EVENTOS_INTERNOS = 125;                                                         // < ri_sat_eventos_internos (SAT EV INT)
	const RI_SATISFACAO_COM_EVENTOS_EXTERNOS = 126;                                                         // < ri_sat_eventos_externos (SAT EV EXT)
	const RI_SATISFACAO_DE_PATROCINADORAS_INSTITUIDORAS = 127;                                              // < ri_sat_patro_inst (SAT.PATR)
	const RI_SATISFACAO_DO_PARTICIPANTE = 128;                                                              // < ri_sat_participante (SAT GER)
	const RI_VENDA_DOS_PLANOS_PREVIDENCIARIOS_PATROCINADORA = 129;                                          // < ri_venda_previd_patroc (VDA PLANO patroc11)
    const RI_VENDA_DOS_PLANOS_PREVIDENCIARIOS_INSTITUIDORA = 150;                                           // < ri_venda_previd_instit (VDA PLANO instit11)
	const RI_VENDA_DOS_PLANOS_PREVIDENCIARIOS_CONSOLIDADO = 151;                                            // < ri_venda_previd_consol (VDA PLANO)
    const RI_CUSTO_GRI = 130;                                                                               // < ri_custo_gri (CUSTO)
	const RI_NUMERO_DE_PATROCINADORAS_INSTITUIDORES = 131;                                                  // < ri_num_patro_instituidores (N PAT-INST)
	const RI_VENDA_DOS_PLANOS_PREVIDENCIARIOS = 205;
	const RI_SATISFACAO_COM_CLIENTES_INTERNOS = 206;
	const RI_SATISFACAO_COM_CLIENTES_EXTERNOS = 207;
	const RI_PUBLICO_PRESENTE_EM_EVENTOS_EXTERNOS_2013 = 208;
	const RI_PUBLICO_PRESENTE_EM_EVENTOS_INTERNOS_2013 = 209;
	const RI_ACESSOS_BOLETIM_ON_LINE = 210;
	const RI_ACESSOS_BOLETIM_EM_PAUTA = 211;
	const RI_ACESSOS_EMAIL_MARKETING = 212;
	const RI_RESPONDENTES_JOGOS_INTERATIVOS = 213;
	const RI_SATISFACAO_VENDA_PLANOS_POS_VENDA = 219;
    const RI_SATISFACAO_VENDA_PLANOS_POS_VENDA_EMPRESAS = 220;

    //COMUNICACAO
    const COMUNICACAO_SATISFACAO_COM_CANAIS_COMUNICACAO = 248; 
    const COMUNICACAO_NUMERO_ACESSO_SITE = 249;

	// secretaria geral
	const SECRETARIA_DC_ENCAMINHADA_PARA_DIVULGACAO_FORA_PRAZO = 110;										// < secretaria_dc
	const SECRETARIA_RETORNO_DECISOES_SOLICITACOES_FORA_PRAZO = 109;										// < secretaria_retorno (Retorno_2010)
	const SECRETARIA_RD_ENCAMINHADA_DIVULGACAO_FORA_PRAZO = 111;                                            // < secretaria_rd (RD_2010)
	const SECRETARIA_DIVUGACAO_SUMULAS_CD = 201;                                                               // < NOVA(07/2012)
	const SECRETARIA_DIVUGACAO_SUMULAS_CF = 202;                                                               // < NOVA(07/2012)
	const SECRETARIA_ATAS_CD_ASSINADAS_FORA_PRAZO = 245;   
	const SECRETARIA_ATAS_CF_ASSINADAS_FORA_PRAZO = 246;  
    const SECRETARIA_ATAS_DE_ASSINADAS_FORA_PRAZO = 247; 
    
    const SECRETARIA_ATAS_SUMULAS_DE_FORA_PRAZO = 393; 
    const SECRETARIA_ATAS_SUMULAS_CF_FORA_PRAZO = 394; 
    const SECRETARIA_ATAS_SUMULAS_CD_FORA_PRAZO = 395; 

    const SECRETARIA_SAT_WORKSHOP_DIRIGENTE = 402; 
    
	// investimento
	const INVESTIMENTO_ENQUADRAMENTO_POLITICA_INVESTIMENTOS = 146;											// < investimento_pi
	const INVESTIMENTO_REALIZACAO_META_RENTABILIDADE = 145;
	const INVESTIMENTO_EVOLUCAO_INPC = 277;
	const INVESTIMENTO_RENTABILIDADE_REAL = 278;
	const INVESTIMENTO_RENTABILIDADE_NOMINAL = 279;
	const INVESTIMENTO_RENTABILIDADE_SEGMENTOS = 280;
	const INVESTIMENTO_RENTABILIDADE_PLANO_UNICO = 281;
	const INVESTIMENTO_RENTABILIDADE_RGE = 282;
	const INVESTIMENTO_RENTABILIDADE_CRM = 283;
	const INVESTIMENTO_RENTABILIDADE_CEEEE = 288;
	const INVESTIMENTO_RENTABILIDADE_PGA = 289;
	const INVESTIMENTO_RENTABILIDADE_INSTITUIDORES = 290;
	const INVESTIMENTO_RENTABILIDADE_CARTEIRA_DE_INVESTIMENTOS = 305;
	const INESTIMENTO_RENTABILIDADE_PLANOS_CD_PGA = 321;
	const INESTIMENTO_CARTEIRA_DE_INVESTIMENTO_BD = 322;
	const INVESTIMENTO_ALCADAS = 400;
	const INVESTIMENTO_RENTABILIDADE_COMPETITIVA = 431;

	// juridico
	const JURIDICO_CUSTO_MEDIO_DE_HONORARIOS_POR_ACAO = 104;												// < juridico_honor_acao (HONOR-2010)
	const JURIDICO_NUMERO_DE_SOLICITACOES_DE_PARECERES_POR_GERENCIA = 99;                                   // < juridico_num_sol_par_gere (SOLIC-2010)
	const JURIDICO_EVOLUCAO_DAS_ACOES_JUDICIAIS = 100;                                                      // < juridico_evo_acoes_jud (HISTÓRICO 2011)
	const JURIDICO_NUMERO_DE_ACOES_JUDICIAIS = 101;                                                         // < juridico_num_acoes_jud (AÇÔES-20100)
	const JURIDICO_PARTICIPANTES_COM_ACOES_JUDICIAIS = 102;                                                 // < juridico_par_acoes_jud (PARTIC-2010)
	const JURIDICO_ASSISTIDOS_COM_ACOES_JUDICIAIS = 103;                                                    // < juridico_ass_acoes_jud (ASSIST-2010)
	const JURIDICO_CUSTO_MEDIO_DE_INDENIZACAO_POR_DEMANDANTE = 105;                                         // < juridico_inden_deman (INDENIZ-2010)
	const JURIDICO_SUCESSO_DAS_ACOES = 106;                                                                 // < juridico_sucesso_acoes (SUCES-2010)
	const JURIDICO_VALOR_DO_PERITO_X_VALOR_DA_FUNDACAO = 107;                                               // < juridico_valor_per_fun (PERIC-2010)
	const JURIDICO_NUMERO_DE_SOLICITACOES_EXTERNAS_PATROCINADORAS_E_PERITOS = 108;                          // < juridico_num_sol_ext_patr (SOL-EXT-2010)
	const JURIDICO_NUMERO_DE_ACOES_JUDICIAIS_ESCRITORIOS = 224;                          					// < juridico_num_acoes_jud_escritorio 
	const JURIDICO_SUCESSO_DAS_ACOES_JUCHEM  = 225;                                                         // < juridico_sucesso_acoes_juchem
	const JURIDICO_SUCESSO_DAS_ACOES_RIBEIRO = 226;                                                         // < juridico_sucesso_acoes_ribeiro
	const JURIDICO_SUCESSO_DAS_ACOES_CENCO   = 227;                                                         // < juridico_sucesso_acoes_cenco
	const JURIDICO_INDENIZACOES_PAGAS = 250; 
	const JURIDICO_IMPACTO_DAS_ACOES_NA_FOLHA_NA_BENEFICIOS = 251;
	const JURIDICO_IMPACTO_ADM_DAS_ACOES = 252;
	const JURIDICO_INCREMENTO_REVISAO_ADM_EM_RELACAO_FOLHA_DE_BENEFICIO = 253;
	const JURIDICO_SUCESSO_DAS_ACOES_TOTAL = 262; 
	const JURIDICO_NUMERO_DE_ACOES = 263;
	const JURIDICO_NUMERO_SOLICITACOES_PARECERES_GERENCIA = 264;
	const JURIDICO_NUMERO_SOLICITACOES_PARECERES_GERENCIA_NOVO = 404;
	const JURIDICO_PARECERES_ATENDITOS_FORA_DO_PRAZO = 265;

	const JURIDICO_SUCESSO_DAS_ACOES_RIBEIRO_CIVEL = 370;
	const JURIDICO_SUCESSO_DAS_ACOES_CENCO_CIVEL = 371;
	const JURIDICO_SUCESSO_DAS_ACOES_JUCHEM_CIVEL = 372;

	const JURIDICO_SUCESSO_DAS_ACOES_CONSOLIDADO_TRABALHISTA = 373;
	const JURIDICO_SUCESSO_DAS_ACOES_CONSOLIDADO_CIVEL = 374;

	const JURIDICO_SUCESSO_DAS_ACOES_AUTORA_CIVEL = 375;
	const JURIDICO_SUCESSO_DAS_ACOES_AUTORA_TRABALHISTA = 376;
	const JURIDICO_SUCESSO_DAS_ACOES_AUTORA_CONSOLIDADO = 377;

	const JURIDICO_SUCESSO_DAS_ACOES_RE_CIVEL = 378;
	const JURIDICO_SUCESSO_DAS_ACOES_RE_TRABALHISTA = 379;
    const JURIDICO_SUCESSO_DAS_ACOES_RE_CONSOLIDADO = 380;

    const JURIDICO_SUCESSO_ACOES_BOTHOME_CIVEL_MENSAL          = 382;
    const JURIDICO_SUCESSO_ACOES_BOTHOME_TRAB_MENSAL           = 383;
    const JURIDICO_SUCESSO_ACOES_CASTRO_BARCELLOS_CIVEL_MENSAL = 384;
    const JURIDICO_SUCESSO_ACOES_CASTRO_BARCELLOS_TRAB_MENSAL  = 385;
    const JURIDICO_SUCESSO_ACOES_FEIDEN_CIVEL_MENSAL           = 386;
    const JURIDICO_SUCESSO_ACOES_FEIDEN_TRAB_MENSAL            = 387;
    const JURIDICO_SUCESSO_ACOES_CONSOLIDADO_CIVEL_MENSAL      = 388;
    const JURIDICO_SUCESSO_ACOES_CONSOLIDADO_TRAB_MENSAL       = 389;
    const JURIDICO_SUCESSO_ACOES_JUDICIAIS_INST_MENSAL         = 391;
    const JURIDICO_SUCESSO_ACOES_JUDICIAIS_CONS_MENSAL         = 392;

    const JURIDICO_SUCESSO_ACOES_CIVEL_FUNDACAO_AUTORA         = 408;
    const JURIDICO_SUCESSO_ACOES_TRAB_FUNDACAO_AUTORA          = 409;
    const JURIDICO_SUCESSO_ACOES_CIVEL_FUNDACAO_RE         	   = 410;
    const JURIDICO_SUCESSO_ACOES_TRAB_FUNDACAO_RE 			   = 411;
    const JURIDICO_SUCESSO_ACOES_CIVEL_CONS_INST 			   = 412;
    const JURIDICO_SUCESSO_ACOES_TRAB_CONS_INST 			   = 413;

	//auditoria interna
	const AUDITORIA_ATENDIMENTO_NO_PRAZO = 335;
	const AUDITORIA_HORAS_PREVISTO_REALIZADO = 336;
	const AUDITORIA_ATEND_PLANO_ANUAL_AUDITORIAS = 424;

	const PGA_CUSTO_ADMINISTRATIVO_PREVIDENCIAL   = 214;
	const PGA_DESPESA_PARTICIPANTE                = 215;
	const PGA_DESPESA_PATRIMONIO_PARTICIPANTE     = 453;
	const PGA_DESPESA_SOBRE_O_ATIVO_TOTAL         = 216;
	const PGA_TAXA_DE_ADMINISTRACAO               = 217;
	const PGA_VARIACAO_ORCAMENTARIA               = 218;
	const PGA_AVALIACAO_COLABORADORES             = 254; //Avaliação Colaboradores
	const PGA_ATENDIMENTO_DE_OBJETIVO_TREINAMENTO = 255; //Atendimento de Objetivo de Treinamento
	const PGA_AVALIACAO_FORNECEDORES              = 256; //Avaliação Fornecedores
	const PGA_TAXA_DE_ADMINISTRACAO_CONSOLIDADA   = 297;
	const PGA_DESPESA_SOBRE_RECEITA               = 298;
	const PGA_LIMITE_TAXA_ADMINISTRACAO           = 340;
	const PGA_AVALIACAO_PRESTRADORES_SERVICO      = 341;
	const PGA_FUNDO_ADMINISTRATIVO                = 434;
	const PGA_DESPESA_SOBRE_FUNDO_GARANTIDOR      = 435;
	const PGA_DESPESA_PESSOAL_SOBRE_TOTAL         = 436;

}

class enum_indicador_tabela_restrito
{
	const GRAFICO_IGP = 4;

	const GRAFICO_IGP_RPP = 3;
	const GRAFICO_IGP_RECLAMACAO = 5;
	const GRAFICO_IGP_BENEFICIO_ERRO = 6;
	const GRAFICO_IGP_CALCULO_INICIAL = 7;
	const GRAFICO_IGP_CUSTO_ADMINISTRATIVO = 8;
	const GRAFICO_IGP_EQUILIBRIO = 9;
	const GRAFICO_IGP_INFORMATICA = 12;
	const GRAFICO_IGP_REALIZACAO_ORCAMENTARIA = 10;
	const GRAFICO_IGP_AVALIACAO = 15;
	const GRAFICO_IGP_SATISFACAO_COLABORADOR = 14;
	const GRAFICO_IGP_SATISFACAO_PARTICIPANTE = 16;
	const GRAFICO_IGP_TREINAMENTO = 11;
	const GRAFICO_IGP_PARTICIPANTE = 13;
	const GRAFICO_IGP_RENTABILIDADE_CI = 17;

	const INFO_SATISFACAO = 20;
}

class enum_indicador_grafico_tipo
{
	const LINHA=1;
	const BARRA_ACUMULADO=2;
	const BARRA_MULTIPLO=3;
	const PIZZA=4;
}
?>