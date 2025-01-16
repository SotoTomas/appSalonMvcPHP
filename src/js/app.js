let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita ={
    id: '',
    nombre:'',
    fecha:'',
    hora:'',
    servicios: []
}


document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});

function iniciarApp(){
    tabs();//Cambia la sección cuando se presionen los tabs
    mostrarSeccion(); //Muestra la sección seleccionada
    botonesPaginador();//Agrega o quita los botones del paginador
    paginaSiguiente();
    paginaAnterior();
    
    consultarAPI();//Consulta la API en el backend de php

    idCliente(); //Añade el id del cliente a la cita
    nombreCliente();//Añade el nobre del cliente al objeto de cita
    seleccionarFecha();// Añade la fecha seleccionada al objeto de cita
    seleccionarHora();// Añade la Hora seleccionada al objeto de cita
    mostrarResumen(); //Muestra el resumen de la cita
}


//-----------------------------------------------------------//
function mostrarSeccion(){
    //Ocultar la seccion que tenga la clase de mostrar
    const seccionAnterior = document.querySelector('.mostrar');
    if(seccionAnterior){
        seccionAnterior.classList.remove('mostrar');
    }

    //Seleccionar la sección con el paso...
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    seccion.classList.add('mostrar');


    //Quita la clase de actual al tab anterior
    const tabAnterior = document.querySelector('.actual');
    if(tabAnterior){
        tabAnterior.classList.remove('actual');
    }

    //Resalta el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');
    

    
}
function tabs(){
    const botones= document.querySelectorAll('.tabs button')
    botones.forEach(boton =>{
        boton.addEventListener('click', function(e){
            paso = parseInt(e.target.dataset.paso);
            mostrarSeccion();
            botonesPaginador();
        });
    })

}

function botonesPaginador(){
    
    const paginaSiguiente = document.querySelector('#siguiente');
    const paginaAnterior = document.querySelector('#anterior');

    if(paso===1){
        paginaAnterior.classList.add('ocultar');
    }else if(paso===3){
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');
        mostrarResumen();
    }else{
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }
    mostrarSeccion();
}

function paginaAnterior(){
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function(){
        
        if(paso <= pasoInicial) return;
        paso --;
        botonesPaginador();
    })
}
function paginaSiguiente(){
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function(){
        
        if(paso >= pasoFinal) return;
        paso ++;
        botonesPaginador();
    })
}

async function consultarAPI(){
    try{
        const url= '/api/servicios';
        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);

    }catch(error){
        console.log(error);
    }
}

function mostrarServicios(servicios){
    servicios.forEach(servicio => {
        const { id, nombre, precio } = servicio;

        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = `Servicio: ${nombre}`;
        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `Precio: ${precio}`;

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = function(){
            seleccionarServicio(servicio);
        }
        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv);
        
    });
}
function seleccionarServicio(servicio){
    const {servicios} = cita;
    const {id} = servicio;

    //Identificar al elemento al que se le da click
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

    //Comprobrar si un servicio ya fue agregado
    if( servicios.some( agregado => agregado.id === id ) ){
        //Eliminar servicio
        cita.servicios = servicios.filter( agregado => agregado.id !== id);
        divServicio.classList.remove('seleccionado');
    }else{
        //Agregar servicio
        cita.servicios = [...servicios, servicio];
        divServicio.classList.add('seleccionado');
    }





}

function idCliente(){
    const id = document.querySelector('#id').value;
    cita.id = id;
}
function nombreCliente(){
    const nombre = document.querySelector('#nombre').value;

    cita.nombre = nombre;

}

function seleccionarFecha(){
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e){
        const dia = new Date(e.target.value).getUTCDay();

        if([6,0].includes(dia)){
            mostrarAlerta('El establecimiento no abre los fines de semana', 'error', '.formulario');
            e.target.value= '';
        }else{
            cita.fecha = e.target.value;
            (cita);
        }
    });

}

function seleccionarHora(){
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e){
        
        const horaCita= e.target.value;
        const hora = horaCita.split(":")[0]; 
        if(hora < 8 || hora >= 17 ){
            mostrarAlerta('El local esta cerrado durante ese horario','error', '.formulario');
            e.target.value= '';
        }else{
            cita.hora = e.target.value;
        }
    });


}

function mostrarAlerta(mensaje, tipo, elemento, desaparece=true){

    const alertaPrevia = document.querySelector('.alerta')
        if(alertaPrevia){
            alertaPrevia.remove()
        };

    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);


    
    
    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);

    if(desaparece){
        setTimeout(()=>{
            alerta.remove();
        }, 3000);  
    }    
}


function mostrarResumen(){
    const resumen = document.querySelector('.contenido-resumen');

    //Limpiar el contenido del resumen
    while(resumen.firstChild){
        resumen.removeChild(resumen.firstChild);
    }
    

    if(Object.values(cita).includes('') || cita.servicios.length === 0){
        mostrarAlerta('Faltan datos de Servicios, Fecha u Hora','error','.contenido-resumen', false);
    return;
    }

    //Formatear el div de resumen
    const {nombre, fecha, hora, servicios} = cita;

    //heading para servicios en resumen
    const headingServicios = document.createElement('H3');
    headingServicios.textContent = 'Resumen de Servicios';
    resumen.appendChild(headingServicios);


    //iterando sobre el array de servicios
    servicios.forEach(servicio=>{
        const {id, precio, nombre} = servicio;
        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio =document.createElement('P');
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio: </span>$${precio}`; 

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);
        resumen.appendChild(contenedorServicio);
    });

    const headingCita = document.createElement('H3');
    headingCita.textContent = 'Resumen de la cita';
    resumen.appendChild(headingCita);

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre: </span>` + nombre;

    //Formatear la fecha en español
    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate() + 2;
    const year = fechaObj.getFullYear();
    
    const opciones = {weekday:'long', year:'numeric', month : 'long', day:'numeric'};
    const fechaUTC = new Date (Date.UTC(year, mes, dia));
    const fechaFormateada = fechaUTC.toLocaleDateString('es-AR', opciones);

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha: </span>` + fechaFormateada;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora: </span>` + hora + ' HS';

    //boton para crear una cita
    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent='Reservar Turno';
    botonReservar.onclick = reservarCita;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);

    resumen.appendChild(botonReservar);

}

async function reservarCita(){
    const {nombre, fecha, hora, servicios, id } = cita;
    const idServicios = servicios.map(servicio=> servicio.id);

    const datos = new FormData();
    datos.append('usuarioId', id);
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('servicios', idServicios);

    try {
        //Peticion hacia la api
        const url= '/api/citas';
        //Fetch tiene que saber que existe FormData()
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });
    
        const resultado = await respuesta.json();
        console.log(resultado.resultado);
    
        if(resultado.resultado){
            Swal.fire({
                icon: "success",
                title: "Turno confirmado",
                text: "Tu turno fue reservado correctamente",
                button: 'OK'
              }).then(()=>{
                    window.location.reload();
            });
        }
                    
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Hubo un error al guardar la cita",
          });
    }

}




    