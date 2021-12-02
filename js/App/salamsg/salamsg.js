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
            let contrasenia = $('#txtContrasenia').val().trim();

            $btnInicio = $('#btnIniciarChat');
            let pantalla = $btnInicio.attr('data-pantalla');

            let usrLogin = {
                nombre:nombre,
                correo:correo,
                contrasenia: contrasenia
            };
            if(pantalla == 1){
                chatService.sValidarUsuario(usrLogin).then(x =>{
                    if(x.usrExiste){
                        if(x.isAdmin){
                            $('.toogle-control').hide();
                            $('#div-contrasenia').show();
                            
                            $btnInicio.attr('data-pantalla',3);
                            $btnInicio.text("Iniciar chat");
                        } else{
                            usrLogin.nombre = ':)';
                            chat.inciarChat(usrLogin);
                        }
                        
                    }else{
                        $('.toogle-control').hide();
                        $('#div-nombre').show();
                        
                        $btnInicio.attr('data-pantalla',2);
                        $btnInicio.text("Iniciar chat");
                    }
                });    
            }else if(pantalla == 2){
                chat.inciarChat(usrLogin);
            }else if(pantalla == 3){
                usrLogin.nombre = ':)';                
                chatService.sLogin(usrLogin).then(res => {
                    if(res.usrValido){
                        chat.inciarChat(usrLogin);
                    }else{
                        location.reload();
                    }
                }).fail(x => location.reload());
            }            
            
        });
    },
    inciarChat:(dataLogin)=>{
        chatService.sAgregarUsuarioCliente(dataLogin).then(usuario =>{
            //console.log(usuario);
            $('#mdIniciarChat').modal('hide');
            //chat.guardarLocalStorage(usuario.correo, usuario);
            chat.usuario =usuario;
            chat.isUserAdmin = usuario.idUsuario == 1;
            
            let tituloTienda = chat.isUserAdmin ? 'Super Farmacia-Admin':'Super Farmacia-Cliente'
            $("#nombre-tienda").html(tituloTienda);
            chat.iniciarEventos();
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
                    chatService.sEnviarMensaje(data).then((x)=>chat.refresh());
                }
            }
            $('#txtMensaje').val('');
        });
    },
    eliminarComentario:(target)=>{
        if(confirm('¿Está seguro que desea eliminar este comentario?')){
            let $htmlMensaje = $(target).closest('.no-gutters');
            let data = {
                idMensaje: $htmlMensaje.attr('data-idmensaje')
            };
            chatService.sEliminarMensaje(data).then(x=>{
                $htmlMensaje.remove();
            });            
        }        
    },
    iniciarEstiloVista:()=>{
        $( document ).off('click').on('click','.friend-drawer--onhover',  function() {	

            $( '.chat-bubble' ).hide('slow').show('slow');    
            chat.usuario.idCanal = this.dataset['idcanal'];
            chat.mostrarMensajesChat();
            if(chat.isUserAdmin){
                chat.acutalizarEstatusVisto();
            }
            
        });
    },
    mostrarMensajesChat:()=>{
        var data = {idCanal : chat.usuario.idCanal };
            chatService.sObtenerMensajes(data).then(mensajes=>{
                if(mensajes){
                    var htmlMensaje = mensajes.map(m => {
                        if(m.idUsuario == chat.usuario.idUsuario)
                        {
                            return chat.mensajesHtml(true,m.mensaje, m.idMensaje);
                        }else{
                            return chat.mensajesHtml(false,m.mensaje,m.idMensaje);
                        }
                    });
                    $('#_mensajes').html(htmlMensaje);
                }else{
                    $('#_mensajes').children().remove();
                }
                
            });
    },
    acutalizarEstatusVisto:()=>{
        let data = {
            idUsuario: chat.usuario.idUsuario,
            idCanal: chat.usuario.idCanal            
        };
        
        chatService.sActualizarEstatusVisto(data).then(x=>{
            chat.obtenerUsuarios();
        });
    },
    obtenerUsuarios: ()=>{
        chatService.sObtenerUsuarios().then(usuarios=>{
            if(usuarios){
                if(!chat.isUserAdmin){
                    usuarios = usuarios.filter(x => x.correo === chat.usuario.correo);
                }
    
                var html = usuarios.map(x => chat.usuarioHtml(x));
                $('#_usuarios').html(html);
            }else{
                $('#_usuarios').children().remove();
                $('#_mensajes').children().remove();
            }
            
        });
    },
    usuarioHtml:(dato)=>{
        
        let mensajesNoLeidos = dato.mensajeNoLeido > 0 && chat.isUserAdmin? 
                                                    `<span class="material-icons">notifications</span><sup>${dato.mensajeNoLeido}</sup>`: '';
        let btnEliminar = chat.isUserAdmin ? '<button type="button" class="close" onclick="chat.btnEliminarChat(this);" title="Eliminar chat"><span class="material-icons">delete</span></button>':'';

        let nombrechat = chat.isUserAdmin ? 'Super Farmacias - '+dato.nombre:
        dato.nombre + ' - Super Farmacias'; 
        let html = `<div class="friend-drawer friend-drawer--onhover" data-idcanal='${dato.idCanal}' data-idusuario='${dato.idUsuario}'>	
                        ${btnEliminar}	  
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
    mensajesHtml: (soyYo, mensaje, idMensaje)=>{                
        let botonEliminar = soyYo ? '<button type="button" class="close" onclick="chat.eliminarComentario(this);" title="Eliminar comentario"><span aria-hidden="true">&times;</span></button>':'';
            return ` <div class="row no-gutters" data-idMensaje="${idMensaje}">
                        <div class="col-md-6 ${soyYo?'':'offset-md-6 bubble-right'}">
                            ${botonEliminar}
                            <div class="chat-bubble chat-bubble--${ soyYo ? 'left': 'right' }">${mensaje}</div>
                        </div>
                    </div>`;
    },
    btnEliminarChat:(target)=>{
        let $padreHtml = $(target).closest('.friend-drawer');
        let idCanal = $($padreHtml[0]).attr('data-idcanal');
        let idUsuario = $($padreHtml[0]).attr('data-idusuario');
        let data = {
            idCanal : idCanal,
            idUsuario: idUsuario
        };
        if(confirm('¿Está seguro que desea eliminar este chat?')){
            chatService.sDeleteChat(data).then(res => {                
                $padreHtml.remove();
                $('#_mensajes').children().remove();
                chat.refresh();
            });
        }
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
        data['method']='agregar_usuario_cliente';
        return chatService.sendPost(data);
    },
    sObtenerUsuarios:()=>{              
        data={method:'obtener_usuarios'};
        return chatService.sendPost(data);
    },
    sObtenerMensajes:(data)=>{      
        data['method']='obtener_menesajes';
        return chatService.sendPost(data);
    },
    sEnviarMensaje:(data)=>{      
        data['method']='enviar_mensaje';
        return chatService.sendPost(data);
    },
    sActualizarEstatusVisto:(data)=>{              
        data['method']='act_est_visto';
        return chatService.sendPost(data);
    },
    sEliminarMensaje:(data)=>{      
        data['method']='eliminar_msg';
        return chatService.sendPost(data);
    },
    sValidarUsuario:(data)=>{      
        data['method']='validar_correo';
        return chatService.sendPost(data);
    },
    sDeleteChat:(data)=>{      
        data['method']='eliminar_chat';
        return chatService.sendPost(data);
    },
    sLogin:(data)=>{      
        data['method']='login';
        return chatService.sendPost(data);
    },
    sendPost:(data)=>{
        var $d = $.Deferred();
        
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