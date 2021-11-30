var chat = chat || {
    usuario:null,    
    isUserAdmin:false,
    refrescarChatSeg: 20,
    init:()=>{
        $('#mdIniciarChat').modal({backdrop: 'static', keyboard: false});

        chat.btnSalirChat();
        chat.btnIniciarChat();
        
    },
    iniciarEventos:()=>{
        chat.iniciarEstiloVista();        
        chat.obtenerUsuarios();
        chat.btnEnviarMensaje();
        chat.chatEventListener();
    },
    btnIniciarChat:()=>{
        $('#btnIniciarChat').off('click').on('click',()=>{
            let nombre = $('#txtNombre').val().trim();
            let correo = $('#txtCorreo').val().trim();
            let usrLogin = {
                nombre:nombre,
                correo:correo
            };
            chatService.sAgregarUsuarioCliente(usrLogin).then(usuario =>{
                //console.log(usuario);
                $('#mdIniciarChat').modal('hide');
                //chat.guardarLocalStorage(usuario.correo, usuario);
                chat.usuario =usuario;
                chat.isUserAdmin = usuario.idUsuario == 1;
                
                let tituloTienda = chat.isUserAdmin ? 'Super Farmacia-Admin':'Super Farmacia-Cliente'
                $("#nombre-tienda").html(tituloTienda);

                chat.iniciarEventos();
            });
        });
    },
    btnSalirChat:()=>{
        $('#btnSalirChat').off('click').on('click',()=>{
            window.close();
        });
    },
    btnEnviarMensaje: ()=>{
        $('#btnEnviarMensaje').off('click').on('click', function(){
            let seccionChatAbierta = $('#_mensajes .no-gutters').length;
            if(seccionChatAbierta){
                let mensaje = $('#txtMensaje').val().trim();
                if(mensaje.length){
                    let data = {
                        idUsuario: chat.usuario.idUsuario,
                        idCanal: chat.usuario.idCanal,
                        mensaje : mensaje
                    };
                    chatService.sEnviarMensaje(data);
                }
            }
            $('#txtMensaje').val('');
            
            
            
        });
    },
    iniciarEstiloVista:()=>{
        $( document ).off('click').on('click','.friend-drawer--onhover',  function() {	

            $( '.chat-bubble' ).hide('slow').show('slow');    
            chat.usuario.idCanal = this.dataset['idcanal'];
            chat.mostrarMensajesChat();
            
        });
    },
    mostrarMensajesChat:()=>{
        var data = {idCanal : chat.usuario.idCanal };
            chatService.sObtenerMensajes(data).then(mensajes=>{
                var htmlMensaje = mensajes.map(m => {
                    if(m.idUsuario == chat.usuario.idUsuario)
                    {
                        return chat.mensajesHtml(true,m.mensaje);
                    }else{
                        return chat.mensajesHtml(false,m.mensaje);
                    }
                });
                $('#_mensajes').html(htmlMensaje);
            });
    },
    obtenerUsuarios: ()=>{
        chatService.sObtenerUsuarios().then(usuarios=>{

            if(!chat.isUserAdmin){
                usuarios = usuarios.filter(x => x.correo === chat.usuario.correo);
            }

            var html = usuarios.map(x => chat.usuarioHtml(x));
            $('#_usuarios').html(html);
        });
    },
    usuarioHtml:(dato)=>{
        
        let mensajesNoLeidos = '';/*dato.mensajeNoLeido > 0 ? 
                                                    `<span class="material-icons">notifications</span> ${dato.mensajeNoLeido}`: '';*/
        let nombrechat = chat.isUserAdmin ? 'Super Farmacias - '+dato.nombre:
        dato.nombre + ' - Super Farmacias'; 
        let html = `<div class="friend-drawer friend-drawer--onhover" data-idcanal='${dato.idCanal}'>		  
                        <div class="text">
                        <h6>${nombrechat}</h6>
                        <p class="text-muted">click para ver mensajes</p>
                        </div>
                        <span class="time text-muted small">
                            ${mensajesNoLeidos}
                        </span>
                    </div>
                    <hr>`;
                    return html;
    },
    mensajesHtml: (soyYo, mensaje)=>{                
            return ` <div class="row no-gutters">
                        <div class="col-md-6 ${soyYo?'':'offset-md-6 bubble-right'}">
                        <div class="chat-bubble chat-bubble--${soyYo?'left':'right'}">${mensaje}</div>
                        </div>
                    </div>`;
    },
    chatEventListener:()=>{
        let interval = chat.refrescarChatSeg * 1000;
        setInterval(function(){ 
            chat.refresh();
         }, interval);
    },
    refresh:()=>{
        let seccionChatAbierta = $('#_mensajes .no-gutters').length;
        chat.obtenerUsuarios();
        if(seccionChatAbierta){
            chat.mostrarMensajesChat();
        }
    },
    guardarLocalStorage:(key, data)=> {
        var datajson = JSON.stringify(data);
        localStorage.setItem(key,datajson);
    },
    obtenerLocalStorage:(key)=>{
        let data = localStorage.getItem(key);
        return JSON.parse(data);
    },
    Error:(msg)=>{

    }
    


};

var chatService = chatService || {

    url : base+'/php/salamsg/salamsg.php',
       
    
    sAgregarUsuarioCliente: (data)=>{              
        var $d = $.Deferred();
        data['method']='agregar_usuario_cliente';
        Utils.post(chatService.url, data).then((res)=>{               
            if(res.success){
                res = res.data;
                $d.resolve(res);
            }else{
                chatService.Error(res.messageError);
                $d.reject();
            }            
        }).fail((err)=>{            
            chatService.Error('Ocurrió un error: '+err.message);
            $d.reject();
        });
        return $d.promise();
    },
    sObtenerUsuarios:()=>{      
        var $d = $.Deferred();
        data={method:'obtener_usuarios'};
        Utils.post(chatService.url, data).then((res)=>{            
            if(res.success){
                res = res.data;
                $d.resolve(res);
            }else{
                chatService.Error(res.messageError);
                $d.reject();
            }            
        }).fail((err)=>{            
            chatService.Error('Ocurrió un error: '+err.message);
            $d.reject();
        });
        return $d.promise();
    },
    sObtenerMensajes:(data)=>{      
        var $d = $.Deferred();

        data['method']='obtener_menesajes';

        Utils.post(chatService.url, data).then((res)=>{            
            if(res.success){
                res = res.data;
                $d.resolve(res);
            }else{
                chatService.Error(res.messageError);
                $d.reject();
            }            
        }).fail((err)=>{            
            chatService.Error('Ocurrió un error: '+err.message);
            $d.reject();
        });
        return $d.promise();
    },
    sEnviarMensaje:(data)=>{      
        var $d = $.Deferred();

        data['method']='enviar_mensaje';

        Utils.post(chatService.url, data).then((res)=>{            
            if(res.success){
                res = res.data;
                $d.resolve(res);
            }else{
                chatService.Error(res.messageError);
                $d.reject();
            }            
        }).fail((err)=>{            
            chatService.Error('Ocurrió un error: '+err.message);
            $d.reject();
        });
        return $d.promise();
    },
    Error:(msg)=>{
        alert(msg);
    }
};