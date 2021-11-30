var base = location.origin+'/SuperFarmacia';

// $('.datepicker').datepicker({
//     format: 'yyyy-mm-dd',            
//     locale: 'es'
// });

// jQuery.extend(jQuery.validator.messages, {
//     required: "Este campo es requerido.",    
//     email: "Se requiere un correo válido.",  
//     date: "Se requiere una fecha válido.",
//     dateISO: "Se requiere una fecha válido (ISO).",      
//     number: "Ingrese un número valido.",
//     digits: "Ingrese solo dígitos.",    
//     accept: "Agregue un archivo válido",
//     maxlength: jQuery.validator.format("Ingese no mas de {0} caracteres."),
//     minlength: jQuery.validator.format("Ingrese al menos {0} caracteres."),
//     rangelength: jQuery.validator.format("Ingrese un valor entre {0} y {1} caracteres."),
//     range: jQuery.validator.format("Ingrese un valor entre {0} y {1}."),
//     max: jQuery.validator.format("Ingrese un valor menor o igual a {0}."),
//     min: jQuery.validator.format("Ingrese un valor mayor o igual a {0}.")
// });


var Utils = Utils || {
    procesando: function(){
        $.blockUI({
            message:'Procesando...',
            css:{ 
                border: 'none', 
                padding: '15px', 
                backgroundColor: '#000', 
                '-webkit-border-radius': '10px', 
                '-moz-border-radius': '10px', 
                opacity: .5, 
                color: '#fff',
                zIndex:9999
            }
        });
    },
    post: function(url, data){
        data = data || {};
        Utils.procesando();
        var $d = $.Deferred();
        $.ajax({
            method:'POST',
            url: url,
            data:data,
            type:'json',
            success: function(res) {
                res = res || "";
                try{
                    if(res.length>0){ 
                        res = JSON.parse(res);
                    }
                }catch{
                    res = {
                        success : false,
                        data : null,
                        messageError : 'Ocurrio un error inesperado, favor de ponerse en contacto con el administrador'
                    }
                }
                
                $d.resolve(res);
                
            },
            error: function(err) {                
                var dataError = {
                    status: err.status,
                    message: err.responseText,
                    code: err.statusText
                }                
                $d.reject(dataError);                
            },
            complete: function(){
                $.unblockUI();
            }
        });       
        return $d.promise();
    }
}
 