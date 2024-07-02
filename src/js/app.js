let paso=1;
const pasoInicial=1;
const pasoFinal=3;

const cita={
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
}

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp(){
    mostrarSeccion();
    tabs(); //Cambia la seccion cuando se presionan los tabs
    botonesPaginador();
    paginaSiguiente();
    paginaAnterior();

    consultarAPI(); //Consulta la API en el backend en el PHP

    idCliente();
    nombreCliente();
    seleccionarFecha();
    seleccionarHora();
    mostrarResumen();
}

function mostrarSeccion(){

    //Ocultar la seccion que tenga la clase de mostrar
    const seccionAnterior=document.querySelector('.mostrar');
    if(seccionAnterior){
        seccionAnterior.classList.remove('mostrar');
    }
    

   //Seleccionar la seccion con el paso
   const pasoSlector=`#paso-${paso}`;
   const seccion = document.querySelector(pasoSlector);
   seccion.classList.add('mostrar');

   //Quita la clase actual de la anterior
   const tabAnterior=document.querySelector('.actual');
   if(tabAnterior){
    tabAnterior.classList.remove('actual');
   }

   //Resalta el tab actual
   const tab=document.querySelector(`[data-paso="${paso}"]`);
   tab.classList.add('actual');
}

function tabs(){
    const botones=document.querySelectorAll('.tabs button');

    botones.forEach( boton =>{
        boton.addEventListener('click', function(e){
            paso=parseInt(e.target.dataset.paso);

            mostrarSeccion();
            botonesPaginador();

        
        } );
    });
}

function botonesPaginador(){
    const paginaAnterior=document.querySelector('#anterior');
    const paginaSiguiente=document.querySelector('#siguiente');

    if(paso===1){
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }
    else if(paso===3){
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');
        mostrarResumen();
    }
    else{
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }

    mostrarSeccion();
}

function paginaSiguiente(){
    const paginaSiguiente=document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function(){
        if(paso >= pasoFinal) return;
        paso++;
        botonesPaginador();
        
    });
}

function paginaAnterior(){
    const paginaAnterior=document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function(){
        if(paso <= pasoInicial) return;
        paso--;
        botonesPaginador();
        
    });
}

async function consultarAPI(){
    try{
        const url=`${location.origin}/api/servicios`;
        const resultado=await fetch(url); //Fetch es lo que se usaba en Ajax
        const servicios=await resultado.json();
        //console.log(servicios);
        mostrarServicios(servicios);

    }catch{
        console.log(error);
    }


}

function mostrarServicios(servicios){
    //console.log(servicios);

  

    servicios.forEach(servicio =>{
        const{ id, nombre, precio }= servicio;
        //console.log(NR_EXPEDIENTE);

         const nombreServicio=document.createElement('P');
         nombreServicio.classList.add('nombre-servicio');
         nombreServicio.textContent=nombre;

         const precioServicio=document.createElement('P');
         precioServicio.classList.add('precio-servicio');
         precioServicio.textContent=`$${precio}`;

         const servicioDiv=document.createElement('DIV');
         servicioDiv.classList.add('servicio');
         servicioDiv.dataset.idServicio=id;
         servicioDiv.onclick=function(){
            seleccionarServicio(servicio);
         };

         servicioDiv.appendChild(nombreServicio);
         servicioDiv.appendChild(precioServicio);

         document.querySelector('#servicios').appendChild(servicioDiv);
        // console.log(servicioDiv);
        
    });
   
}

function seleccionarServicio(servicio){
    const {id}=servicio;
    //console.log(servicio);
    const { servicios }=cita;

    //Identificar el elemento al que se le da click
    const divServicio=document.querySelector(`[data-id-servicio="${id}"]`);

    //Comprobar si un servicio ya fue agregado o seleccionado
    if(servicios.some( agregado => agregado.id === id)){
        cita.servicios=servicios.filter(agregado=>agregado.id !== id);
        divServicio.classList.remove('seleccionado');
    }
    else{
        cita.servicios=[...servicios, servicio]; //Spread operation, sintaxis que permite tomar una copia del arreglo y agregarle lo nuevo al dar clic
        divServicio.classList.add('seleccionado');
    }
  
    // console.log(cita);
   
    
}

function idCliente(){
    const id=document.querySelector('#id').value;
    cita.id=id;
}

function nombreCliente(){
    const nombre=document.querySelector('#nombre').value;
    cita.nombre=nombre;
}

function seleccionarFecha(){
    const inputFecha=document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e){
        // console.log(e.target.value);

        const dia=new Date(e.target.value).getUTCDay();
        if([6,0].includes(dia)){
            e.target.value='';
            // console.log('Sabados y Domingos no abremos');
            mostrarAlerta('Fines de semana no permitidos', 'error', '.formulario');
        }
        else{
            // console.log('correcto');
            cita.fecha=e.target.value;
        }
       
    });
}

function seleccionarHora(){
    const inputHora=document.querySelector('#hora');
    inputHora.addEventListener('input', function(e){
        // console.log(e.target.value);

        const horaCita=e.target.value;
        const hora=horaCita.split(":")[0];
        if(hora < 10 || hora > 18){
            mostrarAlerta('Hora no Valida, trabajamos de 10am a 6pm','error', '.formulario');
        }
        else{
            cita.hora=e.target.value;
        }
       
    });
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true){
    //Previene que se generen mas de 1 alerta
    const alertaPrevia=document.querySelector('.alerta');
    if(alertaPrevia){
        alertaPrevia.remove();
    } 

    //Scripting para crear la alerta
    const alerta=document.createElement('DIV');
    alerta.textContent=mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);
    const referencia=document.querySelector(elemento);
    referencia.appendChild(alerta);

    if(desaparece){
    //Eliminar la alerta despues de 3 segundos
    setTimeout(()=>{
        alerta.remove();
    }, 3000);
    }




}

function mostrarResumen(){
    const resumen=document.querySelector('.contenido-resumen');

    //Limpiar el contenido del resumen
    while(resumen.firstChild){
        resumen.removeChild(resumen.firstChild);
    }

    //console.log(Object.values(cita));
    if(Object.values(cita).includes('') || cita.servicios.length === 0){
       // console.log('Hacen falta datos o Servicios');
       mostrarAlerta('Faltan datos de Servicio, Fecha u Hora', 'error', '.contenido-resumen', false);
       return; //Con esto ya no es necesario trabajar con el else
    }

   // else{
        // console.log('Todo bien');
    //Rormatear el div de resumen
    const{nombre, fecha, hora, servicios}=cita;
    

    //Heading para servicios en resumen
    const headingServicios=document.createElement('H3');
    headingServicios.textContent='Resumen de Servicios';
    resumen.appendChild(headingServicios);

    //Iterando los servicios
    servicios.forEach(servicio => {
        const {id, precio, nombre} = servicio;

        const contenedorServicio=document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');
        
        const textoServicio=document.createElement('P');
        textoServicio.textContent = nombre; //Usando la linea const {id, precio, nombre} = servicio; ya no uso servicio.nombre

        const precioServicio=document.createElement('P');
        precioServicio.innerHTML=`<span> Precio: </span> $${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);


    });

    const nombreCliente=document.createElement('P');
    nombreCliente.innerHTML=`<span>Nombre:</span> ${nombre}`;

        //Formatear la fecha en español
        const fechaObj=new Date(fecha);
        const mes=fechaObj.getMonth();
        const dia=fechaObj.getDay() + 2;
        const year=fechaObj.getFullYear();
    
        const fechaUTC=new Date(Date.UTC(year, mes, dia));
    
        const opciones={weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}
        const fechaFormateada=fechaUTC.toLocaleDateString('es-MX', opciones);

    const fechaCita=document.createElement('P');
    fechaCita.innerHTML=`<span>Fecha:</span> ${fechaFormateada}`;


    const horaCita=document.createElement('P');
    horaCita.innerHTML=`<span>Hora:</span> ${hora} Horas`;

        //Heading para cita en resumen
        const headingCita=document.createElement('H3');
        headingCita.textContent='Resumen de Cita';
        resumen.appendChild(headingCita);

        //Boton para crear una Cita
        const botonReservar=document.createElement('BUTTON');
        botonReservar.classList.add('boton');
        botonReservar.textContent='Reservar Cita';
        botonReservar.onclick=reservarCita;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);
    resumen.appendChild(botonReservar);


    console.log(nombreCliente);
    //}
}

async function reservarCita(){
   // console.log('Reservando Cita...');
    const {nombre, fecha, hora, servicios, id}= cita;

    const idServicios = servicios.map(servicio => servicio.id);

    // console.log(idServicios);
    // return;


   const datos=new FormData();
   
   datos.append('fecha', fecha);
   datos.append('hora', hora);
   datos.append('usuariosid', id);
   datos.append('servicios', idServicios);

   //console.log([...datos]);
   
   try {
      //Peticion hacia la api
   const url=`${location.origin}/api/citas`

   const respuesta = await fetch(url, {
    method: 'POST',
    body: datos
   });
   
   //alert(respuesta);

    const resultado=await respuesta.json();
    console.log(resultado.resultado);
    if(resultado.resultado){
        Swal.fire({
            icon: "success",
            title: "Cita Creada",
            text: "Tu cita fue creada correctamente",
           button: 'OK'
          }).then( () =>{
            window.location.reload();
          })
    }
   } catch (error) {
    Swal.fire({
        icon: "error",
        title: "Error",
        text: "Hubo un error al guardar la cita",
        
      });
   }

 
}