function atendimento_protocolo()
{
	this.version = "1.0";
	this.autor = "cjunior";
	this.required = "minimo prototype 1.6.0";
}

atendimento_protocolo.prototype.input_Blur = 
function(o)
{
	if (o.value=="") 
	{
		Effect.Appear(o.id+"_message");
		o.className = "failed";
	} 
	else 
	{
		Effect.Fade(o.id+"_message");
		o.className = "passed";
	}
}

atendimento_protocolo.prototype.receber_Click =
function(o)
{
	if ( confirm("Atenção\n\nConfirmar recebimento da correspondência?\n\n") ) {
		url = o.getAttribute("urlPartial");
		$("cd_comando_text").value = o.getAttribute("receberId");
		$("only_form").action = url;
		$("only_form").request(
			{
				onLoading:function ()
				{
					$('message_panel').innerHTML = "";
				},
				onComplete:function (originalRequest)
				{
					// Códigos de retorno customizados
					var ajaxReturn = originalRequest.responseText.split("|"); 
	
					// Código "1": Insert ou Update concluído com sucesso,
					// retorna a sequence gerada em caso de insert
					if (ajaxReturn[0]=="1") 
					{
						// alert( "0" );
						auxLoadLista();
					} 
					else 
					{
						$('message_panel').innerHTML = originalRequest.responseText;
					}
				},
				onFailure:function (request)
				{
					$('message_panel').innerHTML = 'Sorry. There was an error.';
				}
			}
		);
	}
}

atendimento_protocolo.prototype.load_cancelar =
function(o)
{
	$('confirm_cancel').show();
	$("cd_comando_text").value = o.getAttribute("correspondenciaId");
	$("motivo_cancelamento_text").focus();
}

atendimento_protocolo.prototype.cancelar_Click =
function(o)
{
	url = o.getAttribute("urlPartial");

	$("only_form").action = url;
	$("only_form").request
	(
		{
			onLoading:function ()
			{
				$('message_panel').innerHTML = "";
			},
			onComplete:function (originalRequest)
			{
				// Códigos de retorno customizados
				var ajaxReturn = originalRequest.responseText.split("|"); 

				// Código "1": Insert ou Update concluído com sucesso,
				// retorna a sequence gerada em caso de insert
				if (ajaxReturn[0]=="1") 
				{
					// alert( "0" );
					auxLoadLista();
				} 
				else 
				{
					$('message_panel').innerHTML = originalRequest.responseText;
				}
			},
			onFailure:function(request)
			{
				$('message_panel').innerHTML = 'Sorry. There was an error.';
			}
		}
	);
}

atendimento_protocolo.prototype.loadPartial_AJAX = 
function( o, url, args )
{
	var myAjax = new Ajax.Request
	(
		url+'?'+args+'',
		{
			method: 'get',
			onLoading: function()
			{
				if(url!="atendimento_protocolo_partial_form.php")
				{
					ajaxLoadShow();
				}
				$('message_panel').innerHTML = "";
			},
			onComplete: function (originalRequest)
			{
				// put returned HTML partial in the innerHTML
				o.innerHTML = originalRequest.responseText;
				$('message_panel').innerHTML = "";
				ajaxLoadHidden();
	
				if(url=="atendimento_protocolo_partial_form.php")
				{
					$("cd_empresa_text").focus();
				}
			},
			onFailure: function (request)
			{
				$('message_panel').innerHTML = "Sorry. There was an error.";
				ajaxLoadHidden();
			}
		}
	);
}

atendimento_protocolo.prototype.initialize = 
function()
{
	this.loadPartial_AJAX( $("div_content"), $("div_content").getAttribute("urlPartial"), $("div_content").getAttribute("args") );
}

atendimento_protocolo.prototype.filtrar_Click =
function(o)
{
	$("filtrar_hidden").value = "true";
	this.loadLista(o)
}

atendimento_protocolo.prototype.loadLista =
function(o)
{
	url = o.getAttribute("urlPartial");
	$("only_form").action = url;
	$("only_form").request(
		{
			onLoading:function (){
				ajaxLoadShow();
				$('message_panel').innerHTML = "";
			},
			onComplete:function (originalRequest)
			{
				ajaxLoadHidden();
				$( o.getAttribute("contentPartial") ).innerHTML = originalRequest.responseText;
				$('message_panel').innerHTML = "";

				if( url=='atendimento_protocolo_partial_lista.php' )
				{
					configurar_filtro();
				}
			},
			onFailure:function (request)
			{
				ajaxLoadHidden();
				alert('Sorry. There was an error.');
			}
		}
	);
}

atendimento_protocolo.prototype.loadInserir =
function(o)
{
	this.loadPartial_AJAX( $(o.getAttribute("contentPartial")), o.getAttribute("urlPartial"), o.getAttribute("args") );
}

atendimento_protocolo.prototype.save_Click =
function(o)
{
	var r = this.validateForm( $("only_form") );

	if (r) 
	{
		if( confirm("Salvar?") )
		{
			$("only_form").action = o.getAttribute("urlPartial");
			$("only_form").request(
				{
					onLoading:function (){
						$('message_panel').innerHTML = "Aguarde ...";
					},
					onComplete:function (originalRequest)
					{
						// Códigos de retorno customizados
						var ajaxReturn = originalRequest.responseText.split("|"); 
	
						// Código "1": Insert ou Update concluído com sucesso,
						// retorna a sequence gerada em caso de insert
						if (ajaxReturn[0]=="1") 
						{
							$('cd_atendimento_protocolo_text').value = ajaxReturn[1];
							$('message_panel').innerHTML = "Correspondência atualizada com sucesso"; 
						} 
						else 
						{
							$('message_panel').innerHTML = originalRequest.responseText;
						}
					},
					onFailure:function (request)
					{
						$('message_panel').innerHTML = "Sorry. There was an error.";
					}
				}
			);
		}
	}
}

atendimento_protocolo.prototype.showHide_Click =
function(o)
{
	if ($('tr_filtro_form').style.display=="none") {
		$('tr_filtro_form').show();
	} else {
		$('tr_filtro_form').hide();
	}
}

atendimento_protocolo.prototype.abaIncluir_Click =
function(o)
{
	$("abaMovimento").className = "";
	$("abaIncluir").className = "abaSelecionada";
	
	this.loadInserir(o);
	
	return true;
}

atendimento_protocolo.prototype.abaMovimento_Click =
function(o)
{
	$("abaMovimento").className = "abaSelecionada";
	$("abaIncluir").className = "";

	this.loadLista(o);

	return true;
}

atendimento_protocolo.prototype.validateForm = 
function(f)
{
	var result = true;
	var sFirstObjectFailed = "";
	for(var index=0; index<f.elements.length; index++) 
	{
		if (f.elements[index].className=="required") 
		{
			if(f.elements[index].value == "")
			{
				Effect.Appear(f.elements[index].id+"_message");
				f.elements[index].className = "failed";
				if (sFirstObjectFailed=="") 
				{
					sFirstObjectFailed = f.elements[index].id;
				}

				result = false;
			}
		}
		else if(f.elements[index].className=="failed")
		{
			if(f.elements[index].value != "")
			{
				Effect.Fade(f.elements[index].id+"_message");
				f.elements[index].className = "passed";
			}
			else
			{
				if (sFirstObjectFailed=="") 
				{
					sFirstObjectFailed = f.elements[index].id;
				}
				result = false;
			}
			
		}
	}
	if (sFirstObjectFailed!="")
	{
		$(sFirstObjectFailed).focus();
	}
	return result;
}

atendimento_protocolo.prototype.reComplete_Blur = 
function( o )
{
	emp = $( o.getAttribute("emp") ).value;
	re = $( o.getAttribute("re") ).value;
	seq = $( o.getAttribute("seq") ).value;
	if (emp!="" && re!="" && seq!="")
	{
		url = o.getAttribute("urlPartial");
		args = o.getAttribute("args");
		args += "&emp="+emp+"&re="+re+"&seq="+seq+"";
		var myAjax = new Ajax.Request
		(
			url+'?'+args+'',
			{
				method: 'get',
				onLoading: function()
				{
					/* $( "nome_participante_text" ).innerHTML = ""; $( "destino_text" ).innerHTML = ""; */
				},
				onComplete: function (originalRequest)
				{
					var ajaxReturn = originalRequest.responseText.toString().split("|");
					$( "nome_participante_text" ).value = ajaxReturn[0];
					$( "destino_text" ).value = ajaxReturn[1];
				},
				onFailure: function (request)
				{
					$( "nome_participante_text" ).value = "Sorry. There was an error.";
					$( "destino_text" ).value = "";
				}
			}
		);

		new Ajax.Updater( 'cd_atendimento_encaminhamento__div', 'atendimento_protocolo_partial_form.php', 
			{ 
				method:'post'
				,parameters:
				{ 
					command:"carregar_atendimento_encaminhamento"
					,emp:$F("cd_empresa_text")
					,re:$F("cd_registro_empregado_text")
					,seq:$F("seq_dependencia_text")
				} 
			}
		);
		
		$("texto_encaminhamento__div").innerHTML = "";
	}
	else
	{
		$( "nome_participante_text" ).value = "";
		$( "destino_text" ).value = "";
	}
}

atendimento_protocolo.prototype.details_Click = 
function( o )
{
	
	$("abaMovimento").className = "";
	$("abaIncluir").className = "abaSelecionada";
	
	url = o.getAttribute("urlPartial");
	args = "id="+o.getAttribute("correspondenciaId")+"&command="+o.getAttribute("command");
	var myAjax = new Ajax.Request
	(
		url+'?'+args+'',
		{
			method: 'get',
			onLoading: function()
			{
				$( "message_panel" ).innerHTML = "";
			},
			onComplete: function (originalRequest)
			{
				$( "div_content" ).innerHTML = originalRequest.responseText;
			},
			onFailure: function (request)
			{
				$( "message_panel" ).innerHTML = "Sorry. There was an error.";
			}
		}
	);
}

atendimento_protocolo.prototype.handleEnter =
function (field, event)
{
	var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	if (keyCode == 13)
	{
		var i;
		for (i = 0; i < field.form.elements.length; i++)
		if (field == field.form.elements[i])
			break;
		i = (i + 1) % field.form.elements.length;
		field.form.elements[i].focus();
		return false;
	}
	else
		return true;
}

atendimento_protocolo.prototype.export_click = 
function(dest)
{
	if(dest=='pdf')
	{
		$("only_form").action = "atendimento_protocolo_lista_pdf.php";
		$("only_form").target = "_blank";
		$("only_form").submit();

		/*
		document.only_form.action = "atendimento_protocolo_lista_pdf.php";
		document.only_form.target = "_blank";
		document.only_form.submit();
		*/
	}
	if(dest=='mala')
	{
		if(confirm('Atenção\n\nA sua seleção do mala direta Eletro será limpa. \n\nPara confirmar clique OK.'))
		{
			$("only_form").action = 'atendimento_protocolo_lista_mala.php';
			$("only_form").request(
				{
					onLoading:function ()
					{
						//$('message_panel').innerHTML = "";
					},
					onComplete:function (originalRequest)
					{
						if (originalRequest.responseText=="true") 
						{
							alert( "Mala direta gerada com sucesso! \n\nAguarde alguns minutos e acesso o Eletro." );
						} 
						else 
						{
							alert( "Ocorreu uma falha ao gerar mala direta!" );
						}
					},
					onFailure:function (request)
					{
						$('message_panel').innerHTML = 'Sorry. There was an error.';
					}
				}
			);
		}

		/*document.forms[0].action = "atendimento_protocolo_lista_mala.php";
		document.forms[0].target = "_blank";
		document.forms[0].submit();*/
	}
}

//////////////

thisPage = new atendimento_protocolo();

// VARIAVEIS GLOBAIS //
var ajax_ob_interval = "";
var ajax_nr_pos_x    = 0;
var ajax_nr_dir      = 2;
var ajax_nr_pos_y    = 0;

function auxLoadLista()
{
	// alert( "1" );
	thisPage.filtrar_Click( $("filtrar_image") );
}

function ajaxLoadShow()
{
	if(document.getElementById("LOADINGDIV").style.visibility != "visible")
	{
		ajax_ob_interval = setInterval(ajaxLoadAnimate,10);
	}
		
	document.getElementById("LOADINGDIV").style.display    = "block";
	document.getElementById("LOADINGDIV").style.visibility = "visible"; 
	
	var nr_scroll = 0;
	if (document.body.scrollTop > 0)
	{
		nr_scroll = document.body.scrollTop;
	}
	document.getElementById("LOADINGDIV").style.top  = ((document.body.clientWidth/4)) + nr_scroll;
}

function ajaxLoadAnimate()
{
	var ob_progress = document.getElementById('LOADINGPROGRESS');
	if(ob_progress != null) 
	{
		if (ajax_nr_pos_x == 0)
		{
			ajax_nr_pos_y += ajax_nr_dir;
		}
		
		if (ajax_nr_pos_y > 32 || ajax_nr_pos_x > 179)
		{
			ajax_nr_pos_x += ajax_nr_dir;
		}
		
		if (ajax_nr_pos_x > 179)
		{
			ajax_nr_pos_y -= ajax_nr_dir;
		}
		
		if (ajax_nr_pos_x > 179 && ajax_nr_pos_y == 0)
		{
			ajax_nr_pos_x = 0;
		}
		
		ob_progress.style.left  = ajax_nr_pos_x;
		ob_progress.style.width = ajax_nr_pos_y;
	}
}

function ajaxLoadHidden()
{
	document.getElementById("LOADINGDIV").style.display    = "none";
	document.getElementById("LOADINGDIV").style.visibility = "hidden"; 
	clearInterval(ajax_ob_interval);
}

function configurar_filtro()
{
	MaskInput( document.getElementById('filtro_dtgap_text'), "99/99/9999" );
	MaskInput( document.getElementById('filtro_hrgap_text'), "99:99" );
	MaskInput( document.getElementById('filtro_dtgap_final_text'), "99/99/9999" );
	MaskInput( document.getElementById('filtro_hrgap_final_text'), "99:99" );
}

function carregar_texto_encaminhamento()
{
	new Ajax.Updater( 'texto_encaminhamento__div', 'atendimento_protocolo_partial_form.php', 
		{ 
			method:'post'
			,parameters:
			{ 
				command:"carregar_texto_encaminhamento"
				,pk_atendimento_encaminhamento:$F("cd_atendimento_encaminhamento__select")
			} 
		} 
	);
}