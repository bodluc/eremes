$(function() {
		$('#formZagiel').submit(function()
		{
			if ($('#zagielzgoda:checked').val() == undefined)
			{
			 	alert('Zanim złożysz zamówienie, zapoznaj sie z procedurą udzielenia kredytu ratalnego eRaty Żagiel.');
				return false;
			} 
			else 
			{
				return true;
			}
		});
		
	});
function nowe_okno()
		{
			window.open('https://www.zagiel.com.pl/kalkulator/jak_kupic.html','nowe_okno','width=710,height=500,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no');
			return false;
		  }
function PoliczRate(koszyk, wariantSklepu, nrSklepu) {
	var quantity_wanted = document.getElementById('quantity_wanted');
	var ilosc;
	if(quantity_wanted.value != '')
		ilosc = quantity_wanted.value;
	else
		ilosc = 1;
	window.open('https://www.eraty.pl/symulator/oblicz.php?numerSklepu='+nrSklepu+'&wariantSklepu='+wariantSklepu+'&typProduktu=0&wartoscTowarow='+(koszyk * ilosc), 'Policz_rate','width=630,height=500,directories=no,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no');
}

var ratyFancy = {
            type:'iframe',
            width:'700',
            height:'900',
            transitionIn: 'elastic',
            transitionOut:'elestic',
            speedIn:400,
            easingIn: 'swing',
            centerOnScroll : true,
            autoDimensions: true,
            srolling: 'auto'
    };
   
   $().ready(function() {
   
        //$("#producent_link").fancybox(fancy);
        $(".raty_link").fancybox(ratyFancy);
    
   });