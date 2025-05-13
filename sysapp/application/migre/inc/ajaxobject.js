/*
AUTOR: CRISTIANO JACOBSEN
DATA: 18/01/2007

ajaxExecute(ds_url, ds_parametros, ds_objeto, ds_operador, fl_metodo)

*/

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



function ajaxCreateLoad()
{
	var ds_img = "inc/loading.gif";
	var nr_img_width = 32;
	var nr_img_height = 32;
    var ob_load = document.createElement("div");
        ob_load.setAttribute('id','__LOADING__');
        ob_load.style.position = 'absolute';
		ajaxRefreshLoad(ob_load,nr_img_width,nr_img_height);
        ob_load.innerHTML = "<img src='" + ds_img + "' border='0'>"; //EXIBE IMAGEM DE LOAD DE DADOS
    document.body.appendChild(ob_load);
}

function ajaxRefreshLoad(ob_load,nr_img_width,nr_img_height)
{
	var nr_scroll = 0;
	try
	{
		//alert("==> A <==\n" + document.body.scrollTop +"\n"+document.body.clientWidth +"\n"+document.body.clientWidth)
		if (document.body.scrollTop > 0)
		{
			nr_scroll = document.body.scrollTop/2;
		}
		ob_load.style.left = (document.body.clientWidth/2) - (nr_img_width/2);
		ob_load.style.top  = ((document.body.clientWidth/2) - (nr_img_height/2)) + nr_scroll;
	}
	catch(e)
	{
		//alert("==> B <==\n" + window.pageYOffset +"\n"+window.offsetWidth +"\n"+window.offsetHeight)
		if (window.pageYOffset > 0)
		{
			nr_scroll = window.pageYOffset/2;
		}
		ob_load.style.left = (window.offsetWidth/2) - (nr_img_width/2);
		ob_load.style.top = ((window.offsetHeight/2) - (nr_img_height/2)) + nr_scroll;				
	}
	
	
}

function ajaxRemoveLoad()
{
    var ob_body = document.body;
    
    while (ob_body.hasChildNodes()) 
    {
        if(ob_body.lastChild.id == "__LOADING__")
        {
            ob_body.removeChild(ob_body.lastChild);
            break;
        }
    }
}

function ajaxExecute(ds_url, ds_parametros, ds_objeto, ds_operador, fl_metodo)
{
    var ob_ajax = ajaxObject();
    ajaxCreateLoad();
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
                                                    ajaxRemoveLoad(); 
 			                                    }
                                                else
                                                {
                                                    //alert(ob_ajax.status)
                                                    ajaxRemoveLoad();
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
                                                            alert('ERRO -> Operador não definido');    
                                                        }
                                                    }
                                                    catch(e)
                                                    {
                                                        alert('ERRO -> Objeto de retorno não definido');
                                                    }
                                                    ajaxRemoveLoad(); 
 			                                    }
                                                else
                                                {
                                                    //alert(ob_ajax.status)
                                                    ajaxRemoveLoad();
                                                }
		                                    }
	                                    }
        ob_ajax.send(ds_parametros); 
    }    
}
