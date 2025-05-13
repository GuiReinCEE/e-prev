	var ds_url    = "exame_ingresso_ajax.php";
	
	function envia(cd_exame_ingresso)
	{
		if(confirm("Deseja realmente enviar o Exame para o Médico?"))
		{
		if(cd_exame_ingresso != "")
		{
			var lt_param = "ds_funcao=envia";
				lt_param+= "&cd_exame_ingresso="+cd_exame_ingresso;
			ajaxExecute(ds_url, lt_param, "retornoAjax", '', 'POST');	
		}
		}
	}

	function retorno(cd_exame_ingresso)
	{
		if(confirm("Confirma o retorno do Exame?"))
		{		
			if(cd_exame_ingresso != "")
			{
				if(trimValue(document.getElementById('fl_apto_'+cd_exame_ingresso).value) == "")
				{
					alert("Informe se Apto Sim ou Não");
					document.getElementById('fl_apto_'+cd_exame_ingresso).focus();
				}
				else if((trimValue(document.getElementById('ds_motivo_'+cd_exame_ingresso).value) == "") && (trimValue(document.getElementById('fl_apto_'+cd_exame_ingresso).value) == "N"))
				{
					alert("Informe o motivo.");
					document.getElementById('ds_motivo_'+cd_exame_ingresso).focus();
				}				
				else
				{
					var lt_param = "ds_funcao=retorno";
						lt_param+= "&cd_exame_ingresso="+cd_exame_ingresso;
						lt_param+= "&fl_apto="+trimValue(document.getElementById('fl_apto_'+cd_exame_ingresso).value);
						lt_param+= "&ds_motivo="+trimValue(document.getElementById('ds_motivo_'+cd_exame_ingresso).value);
					ajaxExecute(ds_url, lt_param, "retornoAjax", '', 'POST');	
				}
			}
		}
	}	
	
	function retornoAjax(retorno)
	{
		if(retorno == "OK")
		{
			document.getElementById('formPesquisa').submit();		
		}
		else
		{
			alert("Ocorreu um erro!");
		}
	}
	
	function buscaParticipante()
	{
		document.getElementById('ds_nome').value = "";
		if(trimValue(document.getElementById('cd_empresa').value) == "")
		{
			alert("Informe a Empresa");
			document.getElementById('cd_empresa').focus();
		}
		else if(trimValue(document.getElementById('cd_registro_empregado').value) == "")
		{
			alert("Informe o Red");
			document.getElementById('cd_registro_empregado').focus();
		}	
		else if(trimValue(document.getElementById('seq_dependencia').value) == "")
		{
			alert("Informe a Sequencia");
			document.getElementById('seq_dependencia').focus();
		}		
		else
		{
			var lt_param = "ds_funcao=buscaParticipante";
				lt_param+= "&cd_empresa="+document.getElementById('cd_empresa').value;
				lt_param+= "&cd_registro_empregado="+document.getElementById('cd_registro_empregado').value;
				lt_param+= "&seq_dependencia="+document.getElementById('seq_dependencia').value;
				
			ajaxExecute(ds_url, lt_param, "retornoParticipante", '', 'POST');	
		}	
	}
	
	function retornoParticipante(retorno)
	{
		if(trimValue(retorno) == "")
		{
			alert('Participante não encontrado');
		}
		else
		{
			document.getElementById('ds_nome').value = retorno;	
			document.getElementById('ds_nome').readOnly = true;
		}
	}	
	
	function limparParticipante()
	{
		document.getElementById('cd_empresa').value = "";
		document.getElementById('cd_registro_empregado').value = "";
		document.getElementById('seq_dependencia').value = "";	
		document.getElementById('ds_nome').readOnly = false;
	}	
