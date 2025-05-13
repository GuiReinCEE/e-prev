function atendimento_recadastro()
{
	this.version = "1.0";
	this.autor = "cjunior";
	this.required = "minimo prototype 1.6.0";
}

atendimento_recadastro.prototype.input_Blur = 
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

atendimento_recadastro.prototype.load_cancelar =
function(o)
{
	$('confirm_cancel').show();
	$("cd_comando_text").value = o.getAttribute("correspondenciaId");
	$("motivo_cancelamento_text").focus();
}

atendimento_recadastro.prototype.cancelar_Click =
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

atendimento_recadastro.prototype.loadPartial_AJAX = 
function( o, url, args )
{
	var myAjax = new Ajax.Request
	(
		url+'?'+args+'',
		{
			method: 'get',
			onLoading: function()
			{
				if(url!="atendimento_recadastro_partial_form.php")
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

				if(url=="atendimento_recadastro_partial_form.php")
				{
					$("cd_empresa_text").focus();
				}
				if(url=="atendimento_recadastro_partial_lista.php")
				{
					load_filter();
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

atendimento_recadastro.prototype.initialize = 
function()
{
	this.loadPartial_AJAX( $("div_content"), $("div_content").getAttribute("urlPartial"), $("div_content").getAttribute("args") );
}

atendimento_recadastro.prototype.filtrar_Click =
function(o)
{
	$("filtrar_hidden").value = "true";
	this.loadLista(o)
}

atendimento_recadastro.prototype.loadLista =
function(o)
{
	$("only_form").action = o.getAttribute("urlPartial");
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
			},
			onFailure:function (request)
			{
				ajaxLoadHidden();
				alert('Sorry. There was an error.');
			}
		}
	);
}

atendimento_recadastro.prototype.loadInserir =
function(o)
{
	this.loadPartial_AJAX( $(o.getAttribute("contentPartial")), o.getAttribute("urlPartial"), o.getAttribute("args") );
}

atendimento_recadastro.prototype.save_Click =
function(o)
{
	var r = this.validateForm( $("only_form") );

	if (r) {
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
						$('cd_atendimento_recadastro_text').value = ajaxReturn[1];
						$('message_panel').innerHTML = "Atualizado com sucesso"; 
					} 
					else 
					{
						$('message_panel').innerHTML = '';
						alert(ajaxReturn[1]);
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

atendimento_recadastro.prototype.showHide_Click =
function(o)
{
	if ($('tr_filtro_form').style.display=="none") {
		$('tr_filtro_form').show();
	} else {
		$('tr_filtro_form').hide();
	}
}

atendimento_recadastro.prototype.abaIncluir_Click =
function(o)
{
	$("abaMovimento").className = "";
	$("abaIncluir").className = "abaSelecionada";
	
	this.loadInserir(o);
	
	return true;
}

atendimento_recadastro.prototype.abaMovimento_Click =
function(o)
{
	$("abaMovimento").className = "abaSelecionada";
	$("abaIncluir").className = "";

	this.loadLista(o);

	return true;
}

atendimento_recadastro.prototype.validateForm = 
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

atendimento_recadastro.prototype.reComplete_Blur = 
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
					/*$( "nome_participante_text" ).innerHTML = "";
					$( "destino_text" ).innerHTML = "";*/
				},
				onComplete: function (originalRequest)
				{
					var ajaxReturn = originalRequest.responseText.toString().split("|");
					$( "nome_participante_text" ).value = ajaxReturn[0];
					if(url=="atendimento_recadastro_partial_lista.php")
					{
						load_filter();
					}
					
				},
				onFailure: function (request)
				{
					$( "nome_participante_text" ).value = "Sorry. There was an error.";
				}
			}
		);
	}
	else
	{
		$( "nome_participante_text" ).value = "";
	}
}
atendimento_recadastro.prototype.details_Click = 
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
				if(url=="atendimento_recadastro_partial_lista.php")
				{
					load_filter();
				}
			},
			onFailure: function (request)
			{
				$( "message_panel" ).innerHTML = "Sorry. There was an error.";
			}
		}
	);
}

atendimento_recadastro.prototype.handleEnter =
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

//////////////

thisPage = new atendimento_recadastro();

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

function load_filter()
{
	MaskInput(document.getElementById('filtro_dtgap_text'), "99/99/9999");
	MaskInput(document.getElementById('filtro_dtgap_final_text'), "99/99/9999");
	MaskInput(document.getElementById('filtro_empresa_text'), "9999999999");
	MaskInput(document.getElementById('filtro_re_text'), "9999999999");
	MaskInput(document.getElementById('filtro_seq_text'), "9999999999");
}