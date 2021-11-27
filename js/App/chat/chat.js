var chat = chat || {
    init:()=>{
        //$('#mdIniciarChat').modal({backdrop: 'static', keyboard: false});
        chat.btnSalirChat();
    },
    iniciarEventos:()=>{
        chat.iniciarEstiloVista();
        chat.sObtenerUsuarios();
    },
    btnIniciarChat:()=>{

    },
    btnSalirChat:()=>{
        $('#btnSalirChat').click(()=>{
            location = 'http://localhost:8080/SuperFarmacia';
        });
    },
    iniciarEstiloVista:()=>{
        $( document ).off('click').on('click','.friend-drawer--onhover',  function() {	
            $( '.chat-bubble' ).hide('slow').show('slow');            
        });
    },
    usuarioHtml:(dato)=>{
        var mensajesNoLeidos = dato.mensajeNoLeido > 0 ? 
                                                    `<span class="material-icons">notifications</span> ${dato.mensajeNoLeido}`: '';
        var html = `<div class="friend-drawer friend-drawer--onhover">		  
                        <div class="text">
                        <h6>${dato.nombre}</h6>
                        <p class="text-muted">click para ver mensajes</p>
                        </div>
                        <span class="time text-muted small">
                            ${mensajesNoLeidos}
                        </span>
                    </div>
                    <hr>`;
                    return html;
    },
    sObtenerUsuarios:()=>{
        Utils.post('./php/chat/chat.php',{method:'obtener_usuarios'}).then(res=>{
            if(res.success){
                var c = JSON.parse(res.data);
                var html = c.map(x => chat.usuarioHtml(x));
                $('#_usuarios').html(html);
            }
        });
    }


}