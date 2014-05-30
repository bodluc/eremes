

function cenowarki(service)  {
    var products = $('#product tr');
    var row,title,info;
    
    for (i=2; i<products.length; i++ ) {
        row = $('td',products[i]);
        title = row[3].innerHTML.trim();
        //price=$.ajax({type:'GET',url:'/notanio.php?name=dls'});
        req = new XMLHttpRequest();
        req.open('get','/notanio2.php?name='+title+'&service='+service,false);
        req.send();
        info=$.parseJSON(req.response);
        row[10].outerHTML = '<td><a class="notanio" target="_blank" href="http://notanio.pl/szukaj/'+title+'">'+
                            '<span class="conowarki">'+info.name.skapiec+'</span> - '+info.cena.skapiec+' <br>'+
                            '<span class="conowarki">'+info.name.ceneo+'</span> - '+info.cena.ceneo+' <br>'+
                            '<span class="conowarki">'+info.name.nokaut+'</span> - '+info.cena.nokaut+' <br>'+
                            '<span class="conowarki">'+info.name.okazje+'</span> - '+info.cena.okazje+' <br>'+
                            '<span class="conowarki">'+info.name.bazarcen+'</span> - '+info.cena.bazarcen+' <br>'+
                            '<span style=\"color:#555\" class="conowarki">'+info.name.allegro+'</span> - '+info.cena.allegro+' <br>'+
                            '</a></td>';
         
         hurtTD=row[8];
         cena=hurtTD.innerHTML.trim();
         idp=row[1].innerHTML.trim();
         hurtTD.outerHTML = "<td id=tdHurt"+i+" align=right onclick='hurtChange(tdHurt"+i+","+idp+")'>"+cena+"</td>";
         
    }
    $('.notanio').fancybox(optAjax);        
}
function hurtChange(td,id) {
    cena = td.innerHTML.trim();
   td.outerHTML = "<td align=center>"+cena+"<br><input align=center size=8 type=text id=tdhurt"+id+"></input><b onclick='updateHurt(tdhurt"+id+","+id+")'>PROMOCJA</b></td>" ;
   $('#tdhurt'+id).select();
}
function updateHurt(input,id) {
    newhurt = input.value;
    $.get('/admin-dev/update.php?reduction='+input.value+'&id_product='+id);
    input.outerHTML =  "<b style=color:red>"+newhurt+" PLN</b><br/>";
}

function modifyProductTD(href) {
    
    
    tr=$('#product tr')[0];
    if (tr == undefined || href.indexOf('updateproduct') > 0 || href.indexOf('addproduct') > 0 ) 
        return;
    $('#product thead tr th')[10].innerHTML="<a  onclick=\"cenowarki('skapiec')\">Skąpiec</a><br>"+
                            "<a  onclick=\"cenowarki('ceneo')\">Ceneo</a><br>"+
                            "<a  onclick=\"cenowarki('nokaut')\">Nokaut</a><br>"+
                            "<a  onclick=\"cenowarki('okazje')\">Okazje.Info</a><br>"+
                            "<a  onclick=\"cenowarki('okazje')\">Bazar Cen</a><br>"+
                            "<a  onclick=\"cenowarki('okazje')\">Allegro</a>";
                            
     
}

//On dom ready
$().ready(function()
{
	// Hide all elements with .hideOnSubmit class when parent form is submit
	$('form').submit(function()
	{
		$(this).find('.hideOnSubmit').hide();
	});
    
    modifyProductTD(document.location.href);
                            
     
       optAjax ={
            type:'iframe',
            width:'95%',
            height:'100%',
            transitionIn: 'elastic',
            transitionOut:'elestic',
            speedIn:400,
            easingIn: 'swing',
            centerOnScroll : true
        };
     
});
