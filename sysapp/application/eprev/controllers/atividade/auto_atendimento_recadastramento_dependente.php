<?php
    include_once('inc/sessao_auto_atendimento.php');
    include_once('inc/conexao.php');
    include_once('inc/nextval_sequence.php');
    include_once('inc/class.TemplatePower.inc.php');

    $tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
    $tpl->prepare();

    include_once('auto_atendimento_monta_sessao.php');
    
    $ds_arq   = "tpl/tpl_auto_atendimento_recadastramento_dependente.html";
    $ob_arq   = fopen($ds_arq, 'r');
    $conteudo = fread($ob_arq, filesize($ds_arq));

    $qr_sql = "
        INSERT INTO public.log_acessos_usuario 
             (
               sid,
               hora,
               pagina
             ) 
        VALUES
             (
               ".$_SESSION['SID'].",
               CURRENT_TIMESTAMP,
               'RECADASTRAMENTO_DEPENDENTE'
             );";
    @pg_query($db,$qr_sql);  
	
	$ANO_RECADASTRO = 2022;
    /*
    #### INCLUIR VALIDACAO DO PUBLICO ####
    $qr_sql = "
                SELECT COUNT(*) AS fl_recadastro
                 WHERE (".$_SESSION['EMP'].",".$_SESSION['RE'].",".$_SESSION['SEQ'].") 
                    IN (
                            (9,7536,0),
                            (9,7358,0),
                            (9,5151,0),
                            (9,7846,0),
                            (9,7226,0),
                            (9,7528,0),
							
							(0,254436,0), -- ATIVO PU
							(0,224103,0), -- ASSISTIDO PU
							(0,2143,0)    -- EXAU
                       )
              ";
    $ob_resul = pg_query($db,$qr_sql);      
    $ar_reg   = pg_fetch_array($ob_resul);
    */

    $_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_recadastramento_publico");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $_RETORNO = curl_exec($ch);
    curl_close ($ch);

    $FL_RETORNO = TRUE;
    $_RETORNO = json_decode($_RETORNO, TRUE);

    if (!(json_last_error() === JSON_ERROR_NONE))
    {
        switch (json_last_error()) 
        {
            case JSON_ERROR_NONE:
                $FL_RETORNO = TRUE;
            break;
                default:
                $FL_RETORNO = FALSE;
            break;
        }
    }

    $fl_recadastro = 'N';

    if($FL_RETORNO)
    {
        if(intval($_RETORNO['error']['status']) == 0)
        {
            $fl_recadastro = $_RETORNO['result']['valida'];
        }
        else
        {
            $mensagem =  "
                    <div style='height: 5px;'></div>
                    <div style='text-align:center;'>
                        <h2>ERRO RECADASTRAMENTO - 001 (REST)</h2>
                    </div>
                    <BR>
                 ";
                 
            $ds_arq   = "tpl/tpl_auto_atendimento_mensagem.html";
            $ob_arq   = fopen($ds_arq, 'r');
            $conteudo = fread($ob_arq, filesize($ds_arq));
            
            $conteudo = str_replace('{MENSAGEM_TITULO}', "Recadastramento de dependentes-beneficiários", $conteudo);
            $conteudo = str_replace('{MENSAGEM_CONTEUDO}', $mensagem, $conteudo);
            
            $tpl->assign('conteudo',$conteudo);
            $tpl->printToScreen();      
            exit;
        }
    }
    else
    {
        $mensagem =  "
                <div style='height: 5px;'></div>
                <div style='text-align:center;'>
                    <h2>ERRO RECADASTRAMENTO - 002 (JSON)</h2>
                </div>
                <BR>
             ";
             
        $ds_arq   = "tpl/tpl_auto_atendimento_mensagem.html";
        $ob_arq   = fopen($ds_arq, 'r');
        $conteudo = fread($ob_arq, filesize($ds_arq));
        
        $conteudo = str_replace('{MENSAGEM_TITULO}', "Recadastramento de dependentes-beneficiários", $conteudo);
        $conteudo = str_replace('{MENSAGEM_CONTEUDO}', $mensagem, $conteudo);
        
        $tpl->assign('conteudo',$conteudo);
        $tpl->printToScreen();      
        exit;
    }


    if($fl_recadastro == 'N')
    {
        $mensagem =  "
                <div style='height: 5px;'></div>
                <div style='text-align:center;'>
                    <h2>Você não faz parte do recadastramento</h2>
                </div>
                <BR>
             ";
             
        $ds_arq   = "tpl/tpl_auto_atendimento_mensagem.html";
        $ob_arq   = fopen($ds_arq, 'r');
        $conteudo = fread($ob_arq, filesize($ds_arq));
        
        $conteudo = str_replace('{MENSAGEM_TITULO}', "Recadastramento de dependentes-beneficiários", $conteudo);
        $conteudo = str_replace('{MENSAGEM_CONTEUDO}', $mensagem, $conteudo);
        
        $tpl->assign('conteudo',$conteudo);
        $tpl->printToScreen();      
        exit;
    }
    
    #### EMAIL E TELEFONE CELULAR ####
    $qr_sql = "
                SELECT (p.ddd_celular::TEXT || p.celular::TEXT) AS celular,
                       LOWER(COALESCE(COALESCE(p.email,p.email_profissional),'')) AS email
                  FROM public.participantes p
                 WHERE p.cd_empresa                                   = ".$_SESSION['EMP']."
                   AND p.cd_registro_empregado                        = ".$_SESSION['RE']."
                   AND p.seq_dependencia                              = ".$_SESSION['SEQ']."
                   AND COALESCE(p.celular,0)                          > 0
                   AND p.celular::TEXT                                LIKE '9%'                   
                   AND LENGTH(p.ddd_celular::TEXT || p.celular::TEXT) = 11  
                   AND COALESCE(COALESCE(p.email,p.email_profissional),'') LIKE '%@%.%'
              ";
    $ob_resul = pg_query($db,$qr_sql);      
    $ar_reg   = pg_fetch_array($ob_resul);
    $_CELULAR = intval($ar_reg['celular']); 
    $_EMAIL   = trim($ar_reg['email']);     
    
    if((trim($_CELULAR) == "") OR (trim($_EMAIL) == ""))
    {
        $mensagem = "
                <div style='height: 5px;'></div>
                <div style='text-align:center;'>
                    <h2>Telefone celular ou e-mail não identificado</h2>
                    Para assinar digitalmente o recadastramento é necessário ter um telefone celular e um e-mail cadastrado.
                    <BR><BR>
                    <a href='auto_atendimento_participante.php' style='clear:both; font-size: 100%;'>[Clique aqui]</a> para atualizar seus dados.
                </div>
                <BR>
             ";
        $ds_arq   = "tpl/tpl_auto_atendimento_mensagem.html";
        $ob_arq   = fopen($ds_arq, 'r');
        $conteudo = fread($ob_arq, filesize($ds_arq));
        
        $conteudo = str_replace('{MENSAGEM_TITULO}', "Recadastramento de dependentes-beneficiários", $conteudo);
        $conteudo = str_replace('{MENSAGEM_CONTEUDO}', $mensagem, $conteudo);
        
        $tpl->assign('conteudo',$conteudo);
        $tpl->printToScreen();      
        exit;
    }   
	
	$declaracao = "Declaro, sob as penas da lei, que as informações acima prestadas são verdadeiras e substituem qualquer indicação feita anteriormente, assumindo todos os ônus e responsabilidades decorrentes do presente ato.";
	$instrucao_pu = "";
	if((intval($_SESSION['PLANO']) == 1) AND (in_array($_SESSION['TIPO_PARTI'], array('EXAU', 'APOS'))))
	{
		$instrucao_pu = '<p style="text-align: justify;">Salientamos que a inclusão de novo Dependente-Beneficiário, estará sujeita ao pagamento de Joia conforme prevê Regulamento do Plano de Benefícios.</p>';
		$declaracao.=chr(10).chr(13)."Estou ciente de que a inscrição de novo Dependente-Beneficiário, estará sujeita ao pagamento de Joia por Inclusão de Dependente-Beneficiário, conforme prevê Regulamento do Plano Único GRUPO CEEE.";
	}
	
    $conteudo = str_replace('{INSTRUCAO_PLANO_UNICO}', $instrucao_pu, $conteudo);
    $conteudo = str_replace('{EMAIL_PARTICIPANTE}', $_EMAIL, $conteudo);
    $conteudo = str_replace('{CELULAR_PARTICIPANTE}', $_CELULAR, $conteudo);
    $conteudo = str_replace('{TIPO_PARTI}', $_SESSION['TIPO_PARTI'], $conteudo);
    $conteudo = str_replace('{RE}', $_SESSION['EMP'].'/'.$_SESSION['RE'].'/'.$_SESSION['SEQ'], $conteudo);
    $conteudo = str_replace('{NOME_SOLIC}', $_SESSION['NOME'], $conteudo);
    $conteudo = str_replace('{MSG_DECLARACAO}', $declaracao, $conteudo);
    $conteudo = str_replace('{MSG_SEM_DEPENDENTE}', 'INFORMO QUE NÃO POSSUO DEPENDENTE-BENEFICIÁRIO.', $conteudo);
    $conteudo = str_replace('{IMG_EMPRESA}', '_9', $conteudo);
    $conteudo = str_replace('{DATA_FIM}', '10/08/2022', $conteudo);

    $qr_sql = "
                SELECT rd.cd_recadastramento_dependente,
                       TO_CHAR(rd.dt_envio_participante, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_participante,
                       rd.dt_confirmacao, 
                       rd.cd_contrato_digital,
                       c.id_doc
                  FROM autoatendimento.recadastramento_dependente rd
                  LEFT JOIN clicksign.contrato_digital c
                    ON c.cd_contrato_digital = rd.cd_contrato_digital
                 WHERE rd.cd_recadastramento_dependente = ".checkRecadastro($ANO_RECADASTRO)."
				   AND rd.dt_cancelamento IS NULL;
              ";
    $rs  = pg_query($db,$qr_sql);
    $_AR_RECADASTRO = pg_fetch_array($rs);
    
	$conteudo = str_replace('{DT_ENVIO_PARTICIPANTE}', $_AR_RECADASTRO['dt_envio_participante'], $conteudo);
	$conteudo = str_replace('{DT_CONFIRMACAO}', $_AR_RECADASTRO['dt_confirmacao'], $conteudo);
	$conteudo = str_replace('{ID_DOCUMENTO_ASSINATURA}', $_AR_RECADASTRO['id_doc'], $conteudo);
	$conteudo = str_replace('{CD_RECADASTRAMENTO_DEPENDENTE}', intval($_AR_RECADASTRO['cd_recadastramento_dependente']), $conteudo);
	
    $cd_recadastramento_dependente = intval($_AR_RECADASTRO['cd_recadastramento_dependente']);
    
	
	#### VERIFICA SE OS DEPENDENTES FORAM CONFIRMADOS NO ORACLE ####
    $qr_sql = "
				SELECT nr_total 
				  FROM oracle.consulta_recadastros_dependentes(".$_SESSION['EMP'].", ".$_SESSION['RE'].", ".$_SESSION['SEQ'].")
			  ";
    $ob_resul = pg_query($db,$qr_sql);
    $reg_orcl = pg_fetch_array($ob_resul);    
	#print_r($reg_orcl);

    if((intval($reg_orcl['nr_total']) > 0))
    {
        #### CONFIRMADO NO ORACLE ####
		$conteudo = str_replace('{FORM_RECADASTRAMENTO}', 'style="display:none;"', $conteudo);
        $conteudo = str_replace('{TABLE_DEP}', 'style="display:none;"', $conteudo);
        $conteudo = str_replace('{TB_INCLUSO_APOS}', 'style="display:none;"', $conteudo);
        $conteudo = str_replace('{DEPENDENTES_OK}', '', $conteudo);
        $conteudo = str_replace('{DEPENDENTES_INCLUSOS}', '', $conteudo);
        $conteudo = str_replace('{STATUS_RECADASTRO}', 'Formulário enviado em '.$_AR_RECADASTRO['dt_envio_participante'].' foi recebido.', $conteudo);
    }
    else
    {
        
		#### VERIFICA STATUS DA ASSINATURA ####
		if(trim($_AR_RECADASTRO['id_doc']) != "")
		{
			$_AR_DOC_ASS = getAssinaturaStatus($_AR_RECADASTRO['id_doc']);
			#echo "<PRE>".print_r($_AR_DOC_ASS,true)."</PRE>"; exit;
			
			if($_AR_DOC_ASS['fl_erro'] == "S")
			{
				$ds_arq   = "tpl/tpl_auto_atendimento_mensagem.html";
				$ob_arq   = fopen($ds_arq, 'r');
				$conteudo = fread($ob_arq, filesize($ds_arq));
				
				$conteudo = str_replace('{MENSAGEM_TITULO}', "ERRO", $conteudo);
				$conteudo = str_replace('{MENSAGEM_CONTEUDO}', $_AR_DOC_ASS['retorno'], $conteudo);
				
				$tpl->assign('conteudo',$conteudo);
				$tpl->printToScreen();      
				exit;				
			}
			else
			{
				if($_AR_DOC_ASS['fl_status'] == "RUNNING")
				{
					$ds_arq   = "tpl/tpl_auto_atendimento_mensagem.html";
					$ob_arq   = fopen($ds_arq, 'r');
					$conteudo = fread($ob_arq, filesize($ds_arq));
					
					$texto = '
								<meta http-equiv="refresh" content="30">
								ID: '.$_AR_RECADASTRO['cd_recadastramento_dependente'].'
								<BR><BR>
								<B>ASSINATURA PENDENTE</B>
								<BR><BR>
								O seu formulário está pendente de assinatura, clique no link abaixo para assinar e concluir.
								<BR><BR>
								<a href="'.$_AR_DOC_ASS['ar_sign'][0]['url_sign'].'" style="clear:both; font-size: 100%;" target="_blank">'.$_AR_DOC_ASS['ar_sign'][0]['url_sign'].'</a>
								<BR><BR>
							 ';
					
					$conteudo = str_replace('{MENSAGEM_TITULO}', "Atualização de dependentes-beneficiários", $conteudo);
					$conteudo = str_replace('{MENSAGEM_CONTEUDO}', $texto, $conteudo);
					
					$tpl->assign('conteudo',$conteudo);
					$tpl->printToScreen();      
					exit;					
				}
				elseif($_AR_DOC_ASS['fl_status'] == "CANCELED")
				{
					$ds_arq   = "tpl/tpl_auto_atendimento_mensagem.html";
					$ob_arq   = fopen($ds_arq, 'r');
					$conteudo = fread($ob_arq, filesize($ds_arq));
					
					$texto = '
								<B>ASSINATURA CANCELADA</B>
								<BR><BR>
								Entre em contato com a nossa central de atendimento 08005102596 de segunda à sexta.
								<BR><BR>
							 ';
					
					$conteudo = str_replace('{MENSAGEM_TITULO}', "Atualização de dependentes-beneficiários", $conteudo);
					$conteudo = str_replace('{MENSAGEM_CONTEUDO}', $texto, $conteudo);
					
					$tpl->assign('conteudo',$conteudo);
					$tpl->printToScreen();      
					exit;					
				}
			}
		}
		
		$conteudo = str_replace('{TB_INCLUSO_APOS}', '', $conteudo);

        if(isset($_AR_RECADASTRO['cd_recadastramento_dependente']))
        {
            if((trim($_AR_RECADASTRO['dt_confirmacao']) != '') OR (trim($_AR_RECADASTRO['dt_envio_participante']) != ''))
            {
                #### ENVIADO OU CONFIRMADO E-PREV ####
				$conteudo = str_replace('{FORM_RECADASTRAMENTO}', 'style="display:none;"', $conteudo);
                $conteudo = str_replace('{FORM_RECADASTRAMENTO_OK}', '', $conteudo);
        
                if(trim($_AR_RECADASTRO['dt_confirmacao']) != '')
                {
                    #### CONFIRMADO E-PREV ###
					$conteudo = str_replace('{STATUS_RECADASTRO}', 'Formulário enviado em '.$_AR_RECADASTRO['dt_envio_participante'].' foi confirmado.', $conteudo);
                }
                else
                {
                    #### ENVIADO E AGUARDANDO CONFIRMACAO E-PREV ####
					$conteudo = str_replace('{STATUS_RECADASTRO}', 'Formulário enviado em '.$_AR_RECADASTRO['dt_envio_participante'].'.<br/>Aguardando confirmação do formulário.', $conteudo);
                }
                
            }
            else
            {
                $conteudo = str_replace('{FORM_RECADASTRAMENTO_OK}', 'style="display:none;"', $conteudo);
                $conteudo = str_replace('{FORM_RECADASTRAMENTO}', '', $conteudo);
                $conteudo = str_replace('{STATUS_RECADASTRO}', '', $conteudo);
            }   
        }
        else
        {
            $conteudo = str_replace('{FORM_RECADASTRAMENTO_OK}', 'style="display:none;"', $conteudo);
            $conteudo = str_replace('{FORM_RECADASTRAMENTO}', '', $conteudo);
            $conteudo = str_replace('{STATUS_RECADASTRO}', '', $conteudo);
        }

		#### DEPEDENTES CADASTRADOS ####
        $qr_sql = "
					SELECT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS cd_dependente,
						   p.cd_empresa,
						   p.cd_registro_empregado, 
						   p.seq_dependencia,
						   p.nome,
						   TO_CHAR(p.dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento, 
						   CASE WHEN UPPER(p.sexo) = 'M' THEN 'Masculino' 
								WHEN UPPER(p.sexo) = 'F' THEN 'Feminino' 
						   END AS sexo, 
						   d.cd_grau_parentesco,
						   gp.descricao_grau_parentesco,
						   d.id_pensionista,
						   d.seq_pensionista,
						   o.fl_opcao,
						   (CASE WHEN o.fl_opcao = 'M' THEN 'MANTER'
								WHEN o.fl_opcao = 'E' THEN 'EXCLUIR'
								ELSE ''
						   END) AS ds_opcao
					  FROM autoatendimento.recadastramento_dependente rd
					  JOIN public.dependentes d
                        ON d.cd_empresa            = rd.cd_empresa 
					   AND d.cd_registro_empregado = rd.cd_registro_empregado 
					  JOIN public.participantes p 
					    ON p.cd_empresa            = d.cd_empresa 
				       AND p.cd_registro_empregado = d.cd_registro_empregado 
					   AND p.seq_dependencia       = d.seq_dependencia 
					  JOIN public.grau_parentescos gp 
					    ON gp.cd_grau_parentesco   = d.cd_grau_parentesco
					  LEFT JOIN autoatendimento.recadastramento_dependente_opcao o
                        ON o.cd_recadastramento_dependente = rd.cd_recadastramento_dependente
					 WHERE d.dt_desligamento       IS NULL
                       AND p.dt_obito              IS NULL					 
					   AND rd.cd_recadastramento_dependente = ".intval($cd_recadastramento_dependente).";
			       ";

        $ob_resul = pg_query($db, $qr_sql);

        $dependentes = '';
            
        while($ob = pg_fetch_array($ob_resul))
        {
            $dependentes .= '
                <tr>
                    <td>'.$ob['nome'].'</td>
                    <td style="text-align:center;">'.$ob['dt_nascimento'].'</td>
                    <td style="text-align:center;">'.$ob['descricao_grau_parentesco'].'</td>
                    <td style="text-align:center;">'.$ob['sexo'].'</td>
                    <td style="text-align:center;">
                    '.(trim($_AR_RECADASTRO['dt_envio_participante']) == ''
                        ? 
                        '
                        <select class="dependente" id="dependente_'.$ob['cd_dependente'].'" name="dependente['.$ob['cd_dependente'].']">
                            <option value=""  '.(trim($ob["fl_opcao"]) == "" ? "selected" : "").'>MANTER ou EXCLUIR</option>
                            <option value="M" '.($ob["fl_opcao"] == "M" ? "selected" : "").'>MANTER</option>
                            <option value="E" '.($ob["fl_opcao"] == "E" ? "selected" : "").'>EXCLUIR</option>
                        </select>
                        '
                        : 
                        $ob['ds_opcao']
                        ).'
                    </td>
                </tr>';
        }   

        if(trim($dependentes) == '')
        {
            $dependentes = '<tr><td colspan="4">Não foi encontrado dependente.</td></tr>';
        }

        if(trim($_AR_RECADASTRO['dt_envio_participante']) == '')
        {
            $conteudo = str_replace('{DEPENDENTES}', $dependentes, $conteudo);
            $conteudo = str_replace('{DEPENDENTES_OK}', '', $conteudo);
        }
        else
        {
            $conteudo = str_replace('{DEPENDENTES}', '', $conteudo);
            $conteudo = str_replace('{DEPENDENTES_OK}', $dependentes, $conteudo);
        }
        
        $qr_sql = "
					SELECT cd_recadastramento_dependente_grau,
						   ds_recadastramento_dependente_grau
					  FROM autoatendimento.recadastramento_dependente_grau
					 WHERE dt_exclusao IS NULL
					 ORDER BY cd_recadastramento_dependente_grau ASC;
			      ";

        $ob_resul = pg_query($db, $qr_sql);

        $grau_parentesco = '';

        while($ob = pg_fetch_array($ob_resul))
        {
            $grau_parentesco .= '<option value="'.$ob['cd_recadastramento_dependente_grau'].'">'.$ob['ds_recadastramento_dependente_grau'].'</option>';
        }   

        $conteudo = str_replace('{GRAU_PARENTESCO}', $grau_parentesco, $conteudo);

		#### DEPEDENTES INCLUIDOS - NOVOS ####
        $qr_sql = "
					SELECT MD5(d.cd_recadastramento_dependente_cadastro::text) AS cd_recadastramento_dependente_cadastro,
						   ds_nome, 
						   TO_CHAR(dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento,
						   g.ds_recadastramento_dependente_grau,
						   g.cd_recadastramento_dependente_grau,
						   CASE WHEN UPPER(d.fl_sexo) = 'M' THEN 'Masculino' 
							WHEN UPPER(d.fl_sexo) = 'F' THEN 'Feminino' 
						   END AS ds_sexo,
						   CASE WHEN UPPER(d.fl_invalido) = 'S' THEN 'Sim' 
							WHEN UPPER(d.fl_invalido) = 'N' THEN 'Não' 
						   END AS ds_invalido
					  FROM autoatendimento.recadastramento_dependente_cadastro d
					  JOIN autoatendimento.recadastramento_dependente_grau g
						ON g.cd_recadastramento_dependente_grau = d.cd_recadastramento_dependente_grau
					 WHERE d.dt_exclusao IS NULL
					   AND d.cd_recadastramento_dependente = ".intval($cd_recadastramento_dependente).";
			       ";

        $ob_resul = pg_query($db, $qr_sql);

        $conteudo = str_replace('{QT_DEPENDENTE}', pg_num_rows($ob_resul), $conteudo);

        if(pg_num_rows($ob_resul) > 0)
        {
            $dependentes_inclusos = '';

            $conteudo = str_replace('{BTN_CANCELAR}', '', $conteudo);

            while($ob = pg_fetch_array($ob_resul))
            {
                $dependentes_inclusos .= '
                <tr>
                    <td>'.$ob['ds_nome'].'</td>
                    <td style="text-align:center;">'.$ob['dt_nascimento'].'</td>
                    <td style="text-align:center;">'.$ob['ds_recadastramento_dependente_grau'].'</td>
                    <td style="text-align:center;">'.$ob['ds_sexo'].'</td>
                    <td style="text-align:center;">'.(in_array($ob['cd_recadastramento_dependente_grau'], array(3, 4)) ? $ob['ds_invalido'] : '').'</td>
                    '.(trim($_AR_RECADASTRO['dt_envio_participante']) == '' ?
                    '<td style="text-align:center;"><a href="auto_atendimento_recadastramento_dependete_excluir.php?cd='.$ob['cd_recadastramento_dependente_cadastro'].'">[REMOVER]</a></td>'
                    : '').' 
                </tr>';
            }

            $conteudo = str_replace('{DEPENDENTES_INCLUSOS}', $dependentes_inclusos, $conteudo);
            $conteudo = str_replace('{TB_DEPEN_INCLU}', '', $conteudo);
            $conteudo = str_replace('{TB_SEM_DEPEN}', 'style="display:none;"', $conteudo);
        }
        else
        {
            $conteudo = str_replace('{TB_DEPEN_INCLU}', 'style="display:none;"', $conteudo);
            $conteudo = str_replace('{TB_SEM_DEPEN}', '', $conteudo);
            $conteudo = str_replace('{BTN_CANCELAR}', 'style="display:none;"', $conteudo);
            $conteudo = str_replace('{DEPENDENTES_INCLUSOS}', '<tr><td colspan="5">Nenhum dependente incluso</td></tr>', $conteudo);
        }
    }

    $tpl->assign('conteudo',$conteudo);
    $tpl->printToScreen();
    
    
    ##################################################################################
    
    function checkRecadastro($ano)
    {
        global $db;
        
        $qr_sql = "
                    SELECT cd_recadastramento_dependente
                      FROM autoatendimento.recadastramento_dependente
                     WHERE dt_cancelamento       IS NULL
                       AND ano                   = ".intval($ano)."					 
                       AND cd_empresa            = ".$_SESSION['EMP']."
                       AND cd_registro_empregado = ".$_SESSION['RE']."
                       AND seq_dependencia       = ".$_SESSION['SEQ']."
                     ORDER BY dt_solicitacao DESC
                     LIMIT 1;
                  ";

        $ob_resul = pg_query($db,$qr_sql);
        $ar_reg = pg_fetch_array($ob_resul);        
        
        if(intval($ar_reg['cd_recadastramento_dependente']) == 0)
        {
            $cd_rec_dep = getNextval("autoatendimento", "recadastramento_dependente", "cd_recadastramento_dependente", $db);

            $qr_sql = "
                        INSERT INTO autoatendimento.recadastramento_dependente
                             (
                                cd_recadastramento_dependente, 
                                fl_sem_dependente,
								ano,
                                cd_empresa, 
                                cd_registro_empregado, 
                                seq_dependencia
                              )
                         VALUES 
                              (
                                ".intval($cd_rec_dep).",
                                'S',
								".intval($ano).",
                                ".$_SESSION['EMP'].",
                                ".$_SESSION['RE'].",
                                ".$_SESSION['SEQ']."
                              );
                       ";
            pg_query($db,"BEGIN TRANSACTION");  
            $ob_resul= @pg_query($db,$qr_sql);
            if(!$ob_resul)
            {
                $ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
                #### DESFAZ A TRANSACAO COM BD ####
                pg_query($db,"ROLLBACK TRANSACTION");
                pg_close($db);
                #echo $ds_erro; echo "<BR><BR>"; echo $qr_sql;exit;
                
                echo "
                        <script>
                            alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
                            document.location.href = 'auto_atendimento_recadastramento_dependente.php';
                        </script>
                     ";
                exit;
            }
            else
            {
                #### COMITA DADOS NO BD ####
                pg_query($db,"COMMIT TRANSACTION"); 
            }                      
        }
        else
        {
            $cd_rec_dep = intval($ar_reg['cd_recadastramento_dependente']);
        }

        return intval($cd_rec_dep);
    }   
    
    
    function getAssinaturaStatus($id_doc_assinatura)
    {
        $_JSON = array("fl_erro" => "S", "cd_erro" => 99, "retorno" => 'Sem dados');
		
		if(trim($id_doc_assinatura) != "")
        {
            $data_string = '
                { 
                    "token"        : "83eaa4b96dfed1a3a92238b43fe90cec",
                    "cd_documento" : "'.trim($id_doc_assinatura).'"
                }           
            ';      
            
            $ch = curl_init("https://www.e-prev.com.br/cieprev/index.php/clicksign/clicksign/documento_situacao");             
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $_RETORNO = curl_exec($ch);
            $_RT_STATUS = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);    

            #print_r($_RT_STATUS);   
            #print_r($_RETORNO);     

			
            if(intval($_RT_STATUS) == 200)
            {
                $_JSON = json_decode($_RETORNO,TRUE);
                if (!(json_last_error() === JSON_ERROR_NONE))
                {
                    switch (json_last_error()) 
                    {
                        case JSON_ERROR_DEPTH:
                            $_JSON = array("fl_erro" => "S", "cd_erro" => 99, "retorno" => '(JSON) A profundidade maxima da pilha foi excedida');
                        break;
                        case JSON_ERROR_STATE_MISMATCH:
                            $_JSON = array("fl_erro" => "S", "cd_erro" => 99, "retorno" => '(JSON) Invalido ou mal formado');                        
                        break;
                        case JSON_ERROR_CTRL_CHAR:
                            $_JSON = array("fl_erro" => "S", "cd_erro" => 99, "retorno" => '(JSON) Erro de caractere de controle, possivelmente codificado incorretamente');
                        break;
                        case JSON_ERROR_SYNTAX:
                            $_JSON = array("fl_erro" => "S", "cd_erro" => 99, "retorno" => '(JSON) Erro de sintaxe');
                        break;
                        case JSON_ERROR_UTF8:
                            $_JSON = array("fl_erro" => "S", "cd_erro" => 99, "retorno" => '(JSON) Caracteres UTF-8 malformado, possivelmente codificado incorretamente');
                        break;
                        default:
                            $_JSON = array("fl_erro" => "S", "cd_erro" => 99, "retorno" => '(JSON) Erro nao identificado');
                        break;
                    }
                }       
            }
        }
        
        return $_JSON;
        #echo $id_doc_assinatura;
    }   
?>