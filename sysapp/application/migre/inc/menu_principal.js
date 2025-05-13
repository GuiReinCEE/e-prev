// JavaScript Document
var v_fim=false;
var v_atualizacoes_pendentes=false;
<!--
// ---------------------------------- Menu Dashboard
function win_dsbrd(r,h,a,l,p) {
	if (h=='C'){
		w = window.open("cal_agenda.php?r="+r+"&h="+h, "wdc", "menubar=no,location=no,scrollbars=no,resizable=no,width=277,height=277");
	}
	if (h=='L'){
		w = window.open("calc.htm", "wdl", "menubar=no,location=no,scrollbars=no,resizable=no,width=277,height=277");
	}
	if (h=='1'){
		w = window.open("dash.php?p="+p, "wd1", "menubar=no,location=no,scrollbars=no,resizable=no,width="+l+",height="+a);
	}
	if (h=='2'){
		w = window.open("dash.php?p="+p, "wd2", "menubar=no,location=no,scrollbars=no,resizable=no,width="+l+",height="+a);
	}
	if (h=='3'){
		w = window.open("dash.php?p="+p, "wd3", "menubar=no,location=no,scrollbars=no,resizable=no,width="+l+",height="+a);
	}
	if (h=='4'){
		w = window.open("dash.php?p="+p, "wd4", "menubar=no,location=no,scrollbars=no,resizable=no,width="+l+",height="+a);
	}
	if (h=='5'){
		w = window.open("dash.php?p="+p, "wd5", "menubar=no,location=no,scrollbars=no,resizable=no,width="+l+",height="+a);
	}
	if (h=='6'){
		w = window.open("dash.php?p="+p, "wd6", "menubar=no,location=no,scrollbars=no,resizable=no,width="+l+",height="+a);
	}
	if (h=='7'){
		w = window.open("dash.php?p="+p, "wd7", "menubar=no,location=no,scrollbars=no,resizable=no,width="+l+",height="+a);
	}
}
// ---------------------------------- Esconde / mostra banner e menus:
function win_esc_mostra_menu() {
	w = window.open("esconde_mostra_menu.php", "wskin", "menubar=no,location=no,scrollbars=no,resizable=no,width=1,height=1");
}
// ---------------------------------- Troca de skin
function win_skin(r,h) {
	w = window.open("at_skin.php?r="+r+"&h="+h, "wskin", "menubar=no,location=no,scrollbars=no,resizable=no,width=277,height=277");
}
// ---------------------------------- Chama tela de dicas
function win_dicas(r,h) {
	w = window.open("dicas.php?r="+r+"&h="+h, "wdicas", "menubar=no,location=no,scrollbars=no,resizable=no,width=380,height=230");
}
// ---------------------------------- Se for necessário executar algo na carga da tela, criar uma funcao chamada fnc_onload
function chama_fnc_onload() {
	if (typeof(this["fnc_onload"]) != 'undefined') {
		fnc_onload();
	}
}
// ---------------------------------- 
function saida_normal()
{
	v_fim = true;
}
// ---------------------------------- 
function marca_atualizacao()
{
	v_atualizacoes_pendentes = true;
}
// ---------------------------------- 
function desmarca_atualizacao()
{
	v_atualizacoes_pendentes = false;
}
// ---------------------------------- Utilizada para testar saída sem encerrar a sessão. 
// ---------------------------------- É necessário colocar uma chamada para a funcao saida_normal em cada onclick de link.
function saida_direta()
{
	//if (v_fim == false) {
	//	alert('Para sua segurança procure desconectar-se clicando na opção "Fim" do menu.');
	//}
}
// ---------------------------------- Para testar a saída, crie eventos onclick nos campos chamando a função marca_atualizacao
function confirma_saida() {
	if (v_atualizacoes_pendentes == true) {
		return "ATENÇÃO: Existem atualizações pendentes que serão perdidas caso clique em OK!!! ";
	}
}
// ---------------------------------- 
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_showHideLayers() 
{ //v6.0
	var i,p,v,obj,args=MM_showHideLayers.arguments;
	var x;
	for (i=0; i<(args.length-2); i+=3) 
	{
		if ((obj=MM_findObj(args[i]))!=null) 
		{ 
			v=args[i+2];
			if (obj.style) 
			{ 
				obj=obj.style; 
				x=(v=='show')?'':(v=='hide')?'none':v; 
				v=(v=='show')?'visible':(v=='hide')?'hidden':v; 
				
			}
			
			//alert(x);
			obj.visibility=v; 
			obj.display=x;
		}
	}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
function mOvr(src, clrOver, clrTextOver, sImg) {
		if (src) {
		src.style.cursor = 'hand';
		src.bgColor = clrOver;
		src.style.color = clrTextOver;
		if (document.images[sImg])
		document.images[sImg].src = sImg.indexOf("overgray") >= 0 ? "/imagens/seta_cinza.gif" : "/imagens/seta_branca.gif";
		}
}
function mOut(src, clrIn, clrTextIn, sImg) {
		if (src) {
			src.style.cursor = 'default';
			src.bgColor = clrIn;
			src.style.color = clrTextIn;
			if (document.images[sImg])
				document.images[sImg].src = sImg.indexOf("gray") == 0 ? "/imagens/seta_cinza.gif" : "/imagens/seta_vermelha.gif";
		}
}
//-->