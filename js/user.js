/* 
 * Funciones javascript para la gestion del panel de usuario del plugin Gestor de Reservas
 */

function tipoReserva(tipo) {
    if(tipo.value=="cuatrimestral") {
        formulario_nuevo = document.getElementById("cuatrimestral");
        formulario_viejo = document.getElementById("puntual");
    }
    else {
        formulario_viejo = document.getElementById("cuatrimestral");
        formulario_nuevo = document.getElementById("puntual");
    }
    
    formulario_viejo.style.display = (formulario_viejo.style.display == 'none') ? 'block' : 'none';
    formulario_nuevo.style.display = (formulario_nuevo.style.display == 'inline') ? 'block' : 'inline';
}

