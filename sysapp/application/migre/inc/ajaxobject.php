<style>
#LOADINGDIV {
	text-align:center;
	position:absolute;
	top:36%;
	width:100%;
	visibility:hidden;
	display:none;
	z-index:99999;
}
#LOADING {
	font-family:Tahoma, Helvetica, sans;
	font-size:11px;
	color:#000000;
	background-color:#FFFFFF;
	padding:10px 0 16px 0;
	margin:0 auto;
	display:block;
	width:230px;
	border:1px solid #6A6A6A;
	text-align:left;
	position:relative;
}
#LOADINGPROGRESS {
	height:5px;
	font-size:1px;
	width:1px;
	top:1px;
	left:0px;
	background-color:green;
	position:relative;
}
#LOADINGBG {
	background-color:#EBEBE4;
	top:8px;
	left:8px;
	height:7px;
	width:213px;
	font-size:1px;
	position:relative;
}
</style>
<div id="LOADINGDIV">
	<div id="LOADING">
		<div align="center" id="LOADINGMSG">Carregando...</div>
		<div id="LOADINGBG"><div id="LOADINGPROGRESS"> AAAA</div></div>
		<div align="center"><br>Por favor, aguarde um momento!</div>
	</div>
</div>
<script language="JavaScript">
/*
AUTOR: CRISTIANO JACOBSEN
ajaxExecute(ds_url, ds_parametros, ds_objeto, ds_operador, fl_metodo)
*/

// VARIAVEIS GLOBAIS //
var ajax_ob_interval = "";
var ajax_nr_pos_x    = 0;
var ajax_nr_dir      = 2;
var ajax_nr_pos_y    = 0;

function ajaxObject() 
{
    try 
    {
        return new ActiveXObject("Microsoft.XMLHTTP");
    } 
    catch(e) 
    {
        try 
        {
            return new ActiveXObject("Msxml2.XMLHTTP");
        } 
        catch(ex) 
        {
            try 
            {
                return new XMLHttpRequest();
                
            } 
            catch(exc) 
            {
                return false;
            }
        }
    } 
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

function ajaxExecute(ds_url, ds_parametros, ds_objeto, ds_operador, fl_metodo)
{
    var ob_ajax = ajaxObject();
    ajaxLoadShow();
    var dt = new Date();
    if(ds_parametros != "")
    {
        ds_parametros+= '&__no_cache__=' + Math.random();    
    }
    else
    {
        ds_parametros = '__no_cache__='  + Math.random();  
    }
    
    
    if(fl_metodo != "POST")   
    {
        fl_metodo = "GET";
    }
    
    if(fl_metodo == "GET")   
    {
        ob_ajax.open("GET",ds_url + '?' + ds_parametros,true);
	    
        ob_ajax.onreadystatechange =    function() 
                                        {
                                            if(ob_ajax.readyState == 4) 
                                            {
                                                if(ob_ajax.status == 200) 
                                                {
				                                    var ds_resultado = ob_ajax.responseText;
	                                                try 
                                                    {
                                                        tp_objeto = (typeof(eval(ds_objeto)));
                                                        
                                                        try
                                                        {
                                                            
															eval(ds_objeto + ds_operador + "(ds_resultado)");
                                                        }
                                                        catch(ee)
                                                        {   
                                                            alert('ERRO -> Operador não definido');    
                                                        }
                                                    }
                                                    catch(e)
                                                    {
                                                        alert('ERRO -> Objeto de retorno não definido');
                                                    }
                                                    ajaxLoadHidden(); 
 			                                    }
                                                else
                                                {
                                                    //alert(ob_ajax.status)
                                                    ajaxLoadHidden();
                                                }
		                                    }
	                                    }
	    ob_ajax.send(null);         
    }
    
    if(fl_metodo == "POST")   
    {
        ob_ajax.open("POST", ds_url, true); 
        ob_ajax.setRequestHeader("Method", "POST " + ds_url + " HTTP/1.1");
        ob_ajax.setRequestHeader('Content-Type', "application/x-www-form-urlencoded; charset=iso-8859-1"); 
	    ob_ajax.onreadystatechange =    function() 
                                        {
                                            if(ob_ajax.readyState == 4) 
                                            {
                                                if(ob_ajax.status == 200) 
                                                {
				                                    var ds_resultado = ob_ajax.responseText;
                                                    try 
                                                    {
                                                        tp_objeto = (typeof(eval(ds_objeto)));
                                                        try
                                                        {
                                                            eval(ds_objeto + ds_operador + "(ds_resultado)");
                                                        }
                                                        catch(ee)
                                                        {   
                                                            alert('ERRO -> Operador não definido\n\n' + ee + '\n\n' + ds_objeto + ds_operador + '\n\n' + ds_resultado);    
                                                        }
                                                    }
                                                    catch(e)
                                                    {
                                                        alert('ERRO -> Objeto de retorno não definido\n\n' + e );
                                                    }
                                                    ajaxLoadHidden(); 
 			                                    }
                                                else
                                                {
                                                    //alert(ob_ajax.status)
                                                    ajaxLoadHidden();
                                                }
		                                    }
	                                    }
        ob_ajax.send(ds_parametros); 
    }    
}
ajaxLoadHidden();
</script>