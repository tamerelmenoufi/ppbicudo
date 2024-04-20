Carregando = (opc = 'flex') => {
    $(".Carregando").css("display",opc);
    // alert(opc);
}

ultimaPosicao = 0;

function rolar(){
    var atualPosicao = window.scrollY;
    console.log(`${atualPosicao}`)
}


function validarCPF(cpf_v) {
	cpf_v = cpf_v.replace(/[^\d]+/g,'');	
	if(cpf_v == '') return false;	
	// Elimina cpf_vs invalidos conhecidos	
	if (cpf_v.length != 11 || 
		cpf_v == "00000000000" || 
		cpf_v == "11111111111" || 
		cpf_v == "22222222222" || 
		cpf_v == "33333333333" || 
		cpf_v == "44444444444" || 
		cpf_v == "55555555555" || 
		cpf_v == "66666666666" || 
		cpf_v == "77777777777" || 
		cpf_v == "88888888888" || 
		cpf_v == "99999999999")
			return false;		
	// Valida 1o digito	
	add = 0;	
	for (i=0; i < 9; i ++)		
		add += parseInt(cpf_v.charAt(i)) * (10 - i);	
		rev = 11 - (add % 11);	
		if (rev == 10 || rev == 11)		
			rev = 0;	
		if (rev != parseInt(cpf_v.charAt(9)))		
			return false;		
	// Valida 2o digito	
	add = 0;	
	for (i = 0; i < 10; i ++)		
		add += parseInt(cpf_v.charAt(i)) * (11 - i);	
	rev = 11 - (add % 11);	
	if (rev == 10 || rev == 11)	
		rev = 0;	
	if (rev != parseInt(cpf_v.charAt(10)))
		return false;		
	return true;   
}


function validarCNPJ(cnpj) {
 
    cnpj = cnpj.replace(/[^\d]+/g,'');
 
    if(cnpj == '') return false;
     
    if (cnpj.length != 14)
        return false;
 
    // Elimina CNPJs invalidos conhecidos
    if (cnpj == "00000000000000" || 
        cnpj == "11111111111111" || 
        cnpj == "22222222222222" || 
        cnpj == "33333333333333" || 
        cnpj == "44444444444444" || 
        cnpj == "55555555555555" || 
        cnpj == "66666666666666" || 
        cnpj == "77777777777777" || 
        cnpj == "88888888888888" || 
        cnpj == "99999999999999")
        return false;
         
    // Valida DVs
    tamanho = cnpj.length - 2
    numeros = cnpj.substring(0,tamanho);
    digitos = cnpj.substring(tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
      soma += numeros.charAt(tamanho - i) * pos--;
      if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0))
        return false;
         
    tamanho = tamanho + 1;
    numeros = cnpj.substring(0,tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
      soma += numeros.charAt(tamanho - i) * pos--;
      if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1))
          return false;
           
    return true;
    
}


var CopyMemory = function (text) {
    var $txt = $('<textarea />');
    $txt.val(text).css({ width: "1px", height: "1px", position:'fixed', left:-999}).appendTo('body');
    $txt.select();
    if (document.execCommand('copy')) {
        $txt.remove();
    }
};