<?php
/** 
 * Shortcode para el Gestor de Reservas 
 */

/* Funcion principal que genera el cuerpo del shortcode 
 * 
 * Atributos: la variable $atts contiene los atributos del shortcode y es una variable global de wordpress 
 */
function shortcode($atts) {
    /* Recogemos los atributos de la llamada
     * establecemos los valores 'todas' y 'hoy' por defecto */
    extract(shortcode_atts( array(
        'aula' => 'todas',
        'dia' => 'hoy',
    ), $atts ));
    
    $aula = "{$aula}";  // Contiene el aula que se desea comprobar
    $dia = "{$dia}";    // Contiene el dia que se desea comprobar
    $output = '<div id="calendario">';  // Definimos el div contenedor de todo el html
    
    // Si no se solicito ningun aula especifica
    if($aula=="todas") {
        if($dia=="hoy") $dia = date("l"); // Si queremos ver las reservas de hoy, obtenemos el dia en que nos encontramos
        $output .= generar_vista_dia($dia); // Generamos la vista del dia
    }
    // Si queremos ver las reservas de un aula especifica
    else {
        $output .= generar_vista_aula($aula);   // Generamos la vista del aula
    }
    
    $output .= '</div>';    // Cerramos el div contenedor del html
    
    return $output; // Devolvemos el resultado para que sea mostrado
}
add_shortcode( 'reservas', 'shortcode');    //registramos el shortcode en el sistema Wordpress

/* Funcion que genera la tabla de reservas de las aulas para un dia determinado de la semana
 * 
 * $dia: dia de la semana para la vista
 */
function generar_vista_dia($dia) {
    $dia_comprobacion = explode("_", $dia); 
    
    // Comprobamos si se trata de una peticion para un dia de esta semana o de la que viene
    if($dia_comprobacion[1]=="siguiente") { // Si es para la semana que viene
        $reservas = database::get_reservas("dia",$dia_comprobacion[0]); //Obtenemos las reservas del dia de la semana siguiente
        $dias_semana = definir_semana_siguiente();  // Definimos los dias (dd-mm-yy) de la semana que viene
        $output = imprimir_fecha($dia_comprobacion[0],$dias_semana);    // Codificamos la fecha para imprimirla
    }
    else {  // Si es para esta semana
        $reservas = database::get_reservas("dia",$dia); //Obtenemos las reservas del dia pasado como atributo
        $dias_semana = definir_semana_actual(); // Definimos los dias de la semana (dd-mm-yy)
        $output = imprimir_fecha($dia,$dias_semana);    // Codificamos la fecha para imprimirla
    }
       
    $num_reservas = count($reservas);   //Calculamos el numero de reservas
    $aulas = database::get_aulas(NULL,NULL); //Obtenemos las aulas de la base de datos
    $num_aulas = count($aulas); //Calculamos el numero de aulas
    $hora = "09";   //Establecemos la hora inicial
    $minutos = "00";    //Establecemos los minutos iniciales
    $columna = 0;   // Variable auxiliar que nos situa en cada columna de la tabla generada
    $rowspan = array($num_aulas);   // Variable que determina el tamaño (en fracciones de tiempo) de cada reserva
    $cuatrimestre = definir_cuatrimestre_actual();  // Nos indica en qué cuatrimestre nos encontramos
    $c_cuatrimestral = database::get_opcion("c_cuatrimestral"); // Color de la reserva cuatrimestral
    $c_puntual = database::get_opcion("c_puntual");
    $tabla_puntuales = '<br/><br/><br/><h1>Reservas Puntuales</h1><table><tr><th>Asignatura</th><th>Profesor</th><th>Aula</th><th>Horario</th></tr>';  // Tabla de reservas puntuales
    /* COMENZAMOS A PINTAR LA TABLA --------------------------------------------------------------------- */
    //La tabla se guardara en la variable $output que sera retornada para asi ser mostrada por Wordpress
    $output .= '<table>';
    
    /* Creamos las filas de la tabla con la HORA y el nombre de todas las aulas */
    $output .='<tr><th id="hora">Hora</th>';
    foreach($aulas as $aula) {
        $output .="<th>$aula->nombre</th>";
    }
    /* Fin de la creacion de la primera fila de la tabla ---------------------- */
    /* Creamos el resto de filas de la tabla, una por cada 15 minutos --------- */
    $rowspan[$columna]=0; //Inicializamos el valor de la variable rowspan
    while($hora<"21") {
        $output .='</tr><tr>';  //Cerramos la fila anterior y comenzamos con la nueva
        $output .="<th>$hora:$minutos</th>";    //Pintamos la hora:minuto en la que nos encontramos
        /* Comprobamos aula por aula... */
        foreach($aulas as $aula) {
            /* ...y en todas las reservas... */
            foreach($reservas as $reserva) {
               /* ... si existe una reserva en ese aula programada en la hora actual */
                if($reserva->confirmada==1 && $aula->id==$reserva->aula && $reserva->hora_inicio=="$hora:$minutos:00") {
                    /* Comprobamos si es una reserva cuatrimestral */
                    if($reserva->tipo_reserva==0 && $reserva->cuatrimestre==$cuatrimestre) {
                        /* Si lo es la pintamos */
                         $asignatura = database::get_asignatura("id",$reserva->asignatura);   //Recuperamos el nombre de la asignatura de la base de datos
                         $asignatura = $asignatura[0]; //Cambiamos a un formato legible
                         $grado = database::get_grado("id",$asignatura->grado);               
                         $grado = $grado[0];

                         if($solapa==1) {
                            $final = explode(":",$reserva->hora_fin);
                            $output .="<br>&<br>$asignatura->nombre (G$reserva->grupo)<br/><b>$grado->nombre</b>"; 
                         
                            //Recalculamos el rowspan
                           if($hora==$final[0]) {
                                $rowspan_2 = ($final[1]-$minutos)/15;
                            }
                            else {
                                $rowspan_2 += (($final[1]-$minutos)/15) + 4*($final[0]-$hora);
                            }
                            if($rowspan[$columna]<$rowspan_2) $rowspan[$columna]=$rowspan_2;
                         }               
                         else {
                             /* Definimos el tamaño (en fracciones de 15 minutos) que ocupa nuestra reserva */
                         $final = explode(":",$reserva->hora_fin);
                         if($hora==$final[0]) {
                             $rowspan[$columna] += ($final[1]-$minutos)/15;
                         }
                         else {
                             $rowspan[$columna] += (($final[1]-$minutos)/15) + 4*($final[0]-$hora);
                         }
                         /* Fin de la definicion del tamaño de la reserva ---------------------------- */
                         // Pintamos la reserva 
                            $output .="<td rowspan=$rowspan[$columna] style=\"background:$c_cuatrimestral;color:white\">$asignatura->nombre (G$reserva->grupo)<br/><b>$grado->nombre</b>"; 
                            $solapa = 1;
                         }
                    }
                    /* Si no es una reserva cuatrimestral, asumimos que se trata de una reserva puntual */
                    elseif($reserva->fecha==$dias_semana[0] || $reserva->fecha==$dias_semana[1] || $reserva->fecha==$dias_semana[2] || $reserva->fecha==$dias_semana[3] || $reserva->fecha==$dias_semana[4]){
                        /* Si lo es la pintamos */
                         $asignatura = database::get_asignatura("id",$reserva->asignatura);   //Recuperamos el nombre de la asignatura de la base de datos
                         $asignatura = $asignatura[0]; //Cambiamos a un formato legible                        
                         $profesor = database::get_profesor($reserva->profesor);    //Recuperamos el nombre del profesor del LDAP  
                         $aula = database::get_aulas("id",$reserva->aula);   //Recuperamos el nombre del aula de la base de datos
                         $aula = $aula[0];  //Cambiamos a un formato legible
                         $grado = database::get_grado("id",$asignatura->grado);               
                         $grado = $grado[0];
                         if($solapa == 1) {
                            $final = explode(":",$reserva->hora_fin);
                            $output .="<br>&<br>$asignatura->nombre (G$reserva->grupo)<br/><b>$grado->nombre</b>";
                            $tabla_puntuales .="<tr><td>$asignatura->nombre (G$reserva->grupo)</td><td>$profesor</td><td>$aula->nombre</td><td>De $hora:$minutos a $final[0]:$final[1]</td></tr>";
                            
                            //Recalculamos el rowspan
                           if($hora==$final[0]) {
                                $rowspan_2 = ($final[1]-$minutos)/15;
                            }
                            else {
                                $rowspan_2 += (($final[1]-$minutos)/15) + 4*($final[0]-$hora);
                            }
                            if($rowspan[$columna]<$rowspan_2) $rowspan[$columna]=$rowspan_2;
                         }
                        /* Si la hora coincide definimos el tamaño de la reserva --------------------- */
                        $final = explode(":",$reserva->hora_fin);
                        if($hora==$final[0]) {
                            $rowspan[$columna] += ($final[1]-$minutos)/15;
                        }
                        else {
                             $rowspan[$columna] += (($final[1]-$minutos)/15) + 4*($final[0]-$hora);
                         }
                         /* Fin de la definicion del tamaño de la reserva ---------------------------- */
                         // Pintamos la reserva puntual
                         $output .="<td rowspan=$rowspan[$columna] style=\"background:$c_puntual;color:white\">$asignatura->nombre (G$reserva->grupo)<br/><b>$grado->nombre</b>";
                         $tabla_puntuales .="<tr><td>$asignatura->nombre (G$reserva->grupo)</td><td>$profesor</td><td>$aula->nombre</td><td>De $hora:$minutos a $final[0]:$final[1]</td></tr>";
                         $solapa = 1;
                    }  
                }
           }
           $output.="</td>";
           $solapa=0;
           /* Si no hemos pintado ninguna reserva, cerramos el hueco de la tabla */
           if ($rowspan[$columna]==0) {
                   $output .="<td></td>";               
           }
           /* En caso de haber pintado una reserva, reducimos el tamaño del rowspan y no pintamos nada  */
           else $rowspan[$columna]--;
           $columna++;
        }
        /* Actualizamos el valor de la hora para pasar a la siguiente franja */
        $minutos+="15*$rowspan";
        if($minutos=="60") {
            $minutos="00";
            $hora++;
        }
        $columna = 0;
    }  
    $output .='</tr></table>';  // Cerramos todas las etiquetas abiertas del calendario
    $tabla_puntuales .='</table>';  // Cerramos todas las etiquetas abiertas de la lista de puntuales
    
    $output .= $tabla_puntuales;    // Creamos el codigo HTML definitivo a mostrar
    
    return $output;
}

/* Funcion que genera la tabla de reservas de las aulas para un aula determinada
 * 
 * $aula: aula deseada para la vista
 */
function generar_vista_aula($aula) {
    $aula = database::get_aulas("nombre",$aula);    // Obtenemos el aula que queremos representar
    $reservas = database::get_reservas("aula",$aula[0]->id);    // Obtenemos las reservas del aula pasada como atributo
    $semana = array("Lunes","Martes","Miércoles","Jueves","Viernes");   // Definimos los dias de la semana
    $rowspan = array(5);    // Variable que determina el tamaño (en fracciones de tiempo) de cada reserva
    $columna = 0;   // Variable auxiliar que nos situa en cada columna de la tabla generada
    $hora = "09";   // Establecemos la hora inicial
    $minutos = "00";    // Establecemos los minutos iniciales
    $cuatrimestre = definir_cuatrimestre_actual(); // Nos indica en qué cuatrimestre nos encontramos
    $c_cuatrimestral = database::get_opcion("c_cuatrimestral"); // Color de la reserva cuatrimestral
    $c_puntual = database::get_opcion("c_puntual");
    $dias_semana = definir_semana_actual(); // Nos indica los dias de los que consta esta semana
    $tabla_puntuales = '<br/><br/><h1>Reservas Puntuales</h1><table><tr><th>Asignatura</th><th>Profesor</th><th>Fecha</th><th>Horario</th></tr>';  // Tabla de reservas puntuales

    /* COMENZAMOS A PINTAR LA TABLA ------------------------------------------------------------------- */
    //La tabla se guarda en la variable $output que sera retornada para asi ser mostrada por Wordpress
    $output = '<table>';
    
    /* Creamos las filas de la tabla con la HORA y los dias de la semana */
    $output .='<tr><th>Hora</th>';
    foreach($semana as $dia) {
        $output .="<th>$dia</th>";       
    }
    /* Fin de la creación de la primera fila de la tabla -------------- */
    /* Creamos el resto de filas de la tabla, una por cada 15 minutos */
    $rowspan[$columna] = 0; //Inicializamos el valor de la variable $roewspan 
    while($hora<"21") {
        $output .='</tr><tr>';  //Cerramos la fila anterior y comenzamos con la nueva
        $output .="<th>$hora:$minutos</th>";    //Pintamos la hora:minuto en la que nos encontramos
        /* Comprobamos dia por dia... */
        foreach($semana as $dia) {
            /* ... y en todas las reservas... */
            foreach($reservas as $reserva) {
                /* ... si existe una reserva para ese dia programada en la hora actual */
                if($reserva->confirmada==1 && $dia==$reserva->dia && $reserva->hora_inicio=="$hora:$minutos:00") {
                    /* Comprobamos si es una reserva cuatrimestral */
                    if($reserva->tipo_reserva==0 && $reserva->cuatrimestre==$cuatrimestre) {
                        /* Si lo es la pintamos */
                        $asignatura = database::get_asignatura("id",$reserva->asignatura);  //Recuperamos el nombre de la asignatura de la base de datos
                        $asignatura = $asignatura[0];   //Cambiamos a un formato legible
                        $grado = database::get_grado("id",$asignatura->grado);               
                        $grado = $grado[0];                       
                        
                        if($solapa==1) {
                        $final = explode(":",$reserva->hora_fin);
                        $output .="<br>&<br>$asignatura->nombre (G$reserva->grupo)<br/><b>$grado->nombre</b>";
                        
                        //Recalculamos el rowspan
                           if($hora==$final[0]) {
                                $rowspan_2 = ($final[1]-$minutos)/15;
                            }
                            else {
                                $rowspan_2 += (($final[1]-$minutos)/15) + 4*($final[0]-$hora);
                            }
                            if($rowspan[$columna]<$rowspan_2) $rowspan[$columna]=$rowspan_2;
                        }
                        else {
                            /* Definimos el tamaño (en fracciones de 15 minutos) que ocupa nuestra reserva */
                         $final = explode(":",$reserva->hora_fin);
                         if($hora==$final[0]) {
                             $rowspan[$columna] += ($final[1]-$minutos)/15;
                         }
                         else {
                             $rowspan[$columna] += (($final[1]-$minutos)/15) + 4*($final[0]-$hora);
                         }
                         /* Fin de la definicion del tamaño de la reserva ---------------------------- */
                         // Pintamos la reserva
                         $output .="<td rowspan=$rowspan[$columna] style=\"background:$c_cuatrimestral;color:white\">$asignatura->nombre (G$reserva->grupo)<br/><b>$grado->nombre</b>";
                         $solapa = 1; 
                        }
                    } 
                    /* Si no es una reserva cuatrimestral, asumismos que se trata de una reserva puntual */
                   
                    elseif(($reserva->fecha)>=(date("Y")."-".date("m")."-".date("d"))){
                        $asignatura = database::get_asignatura("id",$reserva->asignatura);  //Recuperamos el nombre de la asignatura de la base de datos
                        $asignatura = $asignatura[0];   //Cambiamos a un formato legible
                        $profesor = database::get_profesor($reserva->profesor);    //Recuperamos el nombre del profesor del LDAP
                        $fecha_array = explode("-",$reserva->fecha);
                        $fecha = "$fecha_array[2]/$fecha_array[1]/$fecha_array[0]";
                        
                        if($solapa==1) {
                           $final = explode(":",$reserva->hora_fin);
                           $output .="<br>&<br>Reserva puntual"; 
                           $tabla_puntuales .="<tr><td>$asignatura->nombre (G$reserva->grupo)</td><td>$profesor</td><td>$fecha</td><td>De $hora:$minutos a $final[0]:$final[1]</td></tr>";
                           
                           //Recalculamos el rowspan
                           if($hora==$final[0]) {
                                $rowspan_2 = ($final[1]-$minutos)/15;
                            }
                            else {
                                $rowspan_2 += (($final[1]-$minutos)/15) + 4*($final[0]-$hora);
                            }
                            if($rowspan[$columna]<$rowspan_2) $rowspan[$columna]=$rowspan_2;
                        }
                        else {
                            /* Si lo es la pintamos */
                        /* Si la hora coincide definimos el tamaño de la reserva */
                        $final = explode(":",$reserva->hora_fin);
                        if($hora==$final[0]) {
                            $rowspan[$columna] += ($final[1]-$minutos)/15;
                        }
                        else {
                            $rowspan[$columna] += (($final[1]-$minutos)/15) + 4*($final[0]-$hora);
                        }
                        /* Fin de la definición del tamaño de la reserva ------ */
                        // Pintamos la reserva puntual
                        $output .="<td rowspan=$rowspan[$columna] style=\"background:$c_puntual;color:white\">Reserva puntual"; 
                        $tabla_puntuales .="<tr><td>$asignatura->nombre (G$reserva->grupo)</td><td>$profesor</td><td>$fecha</td><td>De $hora:$minutos a $final[0]:$final[1]</td></tr>";
                        $solapa = 1;
                        }
                        

                    }
                }
            }
            $output.="</td>";
            $solapa = 0;
            /* Si no hemos pintado ninguna reserva, cerramos el hueco de la tabla */
            if($rowspan[$columna]==0) {
                $output .="<td></td>";
            }
            /* En caso de haber pintado una reserva, reducimos el tamaño del rowspan y no pintamos nada */
            else $rowspan[$columna]--;
            $columna++;
        }
        /* Actualizamos el valor de la hora para pasar a la siguiente franja */
        $minutos+="15*$rowspan";
        if($minutos=="60") {
            $minutos="00";
            $hora++;
        }
        $columna = 0;
    }
    $output .='</tr></table>';  // Cerramos todas las etiquetas abiertas del calendario
    $tabla_puntuales .='</table>';  // Cerramos todas las etiquetas abiertas de la lista de puntuales
    
    $output .= $tabla_puntuales;    // Creamos el codigo HTML definitivo a mostrar
    
    return $output;
}

/* Funcion para determinar, a partir del mes actual, el cuatrimestre en el que nos encontramos */
function definir_cuatrimestre_actual() {
    $fecha = date("m"); // Mes actual
    
    $cuatrimestre1_ini = database::get_opcion("cuatrimestre1_ini");
        $cuatrimestre1_ini = explode("/",$cuatrimestre1_ini);
        $cuatrimestre1_fin = database::get_opcion("cuatrimestre1_fin");
        $cuatrimestre1_fin = explode("/",$cuatrimestre1_fin);
        
        $cuatrimestre2_ini = database::get_opcion("cuatrimestre2_ini");
        $cuatrimestre2_ini = explode("/",$cuatrimestre2_ini);
        $cuatrimestre2_fin = database::get_opcion("cuatrimestre2_fin");
        $cuatrimestre2_fin = explode("/",$cuatrimestre2_fin);
        
        $verano_ini = database::get_opcion("verano_ini");
        $verano_ini = explode("/",$verano_ini);
        $verano_fin = database::get_opcion("verano_fin");
        $verano_fin = explode("/",$verano_fin);
    
    if($fecha>=$verano_ini[0] && $fecha<=$verano_fin[0]) {
        $cuatrimestre = 0; // Entre julio y agosto: verano (0)
    }
    elseif($fecha>=$cuatrimestre1_ini[0] && $fecha<=$cuatrimestre1_fin[0]) {
        $cuatrimestre = 1;  // Entre septiembre y diciembre, primer cuatrimestre (1)
    }
    elseif($fecha>=$cuatrimestre2_ini[0] && $fecha<=$cuatrimestre2_fin[0]) {
        $cuatrimestre = 2;  // Entre enero y junio, segundo cuatrimestre (2)
    }
    
    return $cuatrimestre;
}

/* Funcion para determinar los dias de la semana de las reservas puntuales que deben mostrarse en el calendario */
function definir_semana_actual() {
    $dia_semana = date("l");    // Nombre del dia de la semana
    $dia = date("d");           // Dia
    $mes = date("m");           // Mes
    $ano = date("Y");           // Año

    /* Definimos, a partir del dia de hoy, el resto de dias de la semana */
    if($dia_semana=="Monday") $dias_semana = array($dia,$dia+1,$dia+2,$dia+3,$dia+4);
    if($dia_semana=="Tuesday") $dias_semana = array($dia-1,$dia,$dia+1,$dia+2,$dia+3);
    if($dia_semana=="Wednesday") $dias_semana = array($dia-2,$dia-1,$dia,$dia+1,$dia+2);
    if($dia_semana=="Thursday") $dias_semana = array($dia-3,$dia-2,$dia-1,$dia,$dia+1);
    if($dia_semana=="Friday") $dias_semana = array($dia-4,$dia-3,$dia-2,$dia-1,$dia);

    /* Ahora revisamos que no haya incoherencias en lo hecho en el paso anterior */
    for($i=0;$i<count($dias_semana);$i++) {
        /* Si el dia es un numero negativo, estamos en el mes anterior */
        if($dias_semana[$i]<1) {
            if($mes==5 || $mes==7 || $mes==10 || $mes==12) {
                $dias_semana[$i]= 30+$dias_semana[$i];
                $mes--;
            }
            else if($mes==1 || $mes==2 || $mes==4 || $mes==6 || $mes==8 ||  $mes==9 || $mes==11) {
                $dia_semana[$i]= 31+$dias_semana[$i];
                $mes--;
            }
            else if($mes==3) {
                $bisiesto = checkdate(02,29,date(Y));
                if($bisiesto) $dias_semana[$i] = 29+$dias_semana[$i];
                else $dias_semana[$i]= 28+$dias_semana[$i];
                $mes--;
            }
            if($dias_semana[$i]!=$dia && $dias_semana[$i]<10) $dias_semana[$i]="0$dias_semana[$i]";
            $dias_semana[$i] = "$ano-0$mes-$dias_semana[$i]";
            $mes++;
        }
        /* Si el dia es correcto, lo formateamos de forma adecuada */
        else if($dias_semana[$i]>=1 && $dias_semana[$i]<30) {
            if($dias_semana[$i]!=$dia && $dias_semana[$i]<10) $dias_semana[$i]="0$dias_semana[$i]";
            $dias_semana[$i] = "$ano-$mes-$dias_semana[$i]";
        }
        elseif ($dias_semana[$i]>=1 && $dias_semana[$i]<31 && ($mes==1 || $mes==3 || $mes==5 || $mes==7 || $mes==8 || $mes==10 || $mes==12)){
            if($dias_semana[$i]!=$dia && $dias_semana[$i]<10) $dias_semana[$i]="0$dias_semana[$i]";
            $dias_semana[$i] = "$ano-0$mes-$dias_semana[$i]";
        }
        /* Si nos hemos pasado de dia, entonces estamos en el mes siguiente */
        elseif ($dias_semana[$i]>=1 && $dias_semana[$i]<30 && ($mes==4 || $mes==6 || $mes==9 || $mes==11)) {
            $mes+=1;
            $dia = $dias_semana[$i]-31;
            if($dias_semana[$i]!=$dia && $dias_semana[$i]<10) $dias_semana[$i]="0$dias_semana[$i]";
            $dias_semana[$i] = "$ano-0$mes-$dias_semana";
        }
    }
    /* Devolvemos el resultado como un array de fechas */
    return $dias_semana;
}

/* Funcion para determinar los dias de la semana de las reservas puntuales que deben mostrarse en el calendario */
function definir_semana_siguiente() {
    $dia_semana = date("l");    // Nombre del dia de la semana
    $dia = date("d");           // Dia
    $mes = date("m");           // Mes
    $ano = date("Y");           // Año
    
    // Sumamos siete dias para ver la semana siguiente
    /* Si nos hemos pasado de dia, entonces estamos en el mes siguiente */
    $dia+=7;
    if ($dia>31 && ($mes==1 || $mes==3 || $mes==5 || $mes==7 || $mes==8 || $mes==10 || $mes==12)){
        $dia = $dia-31;
        $mes++;
    }
    elseif ($dia>30 && ($mes==4 || $mes==6 || $mes==9 || $mes==11)) {
        $dia = $dia-30;
        $mes++;
    }
    
    /* Definimos, a partir del dia de hoy, el resto de dias de la semana */
    if($dia_semana=="Monday") $dias_semana = array($dia,$dia+1,$dia+2,$dia+3,$dia+4);
    if($dia_semana=="Tuesday") $dias_semana = array($dia-1,$dia,$dia+1,$dia+2,$dia+3);
    if($dia_semana=="Wednesday") $dias_semana = array($dia-2,$dia-1,$dia,$dia+1,$dia+2);
    if($dia_semana=="Thursday") $dias_semana = array($dia-3,$dia-2,$dia-1,$dia,$dia+1);
    if($dia_semana=="Friday") $dias_semana = array($dia-4,$dia-3,$dia-2,$dia-1,$dia);

    /* Ahora revisamos que no haya incoherencias en lo hecho en el paso anterior */
    for($i=0;$i<count($dias_semana);$i++) {
        /* Si el dia es un numero negativo, estamos en el mes anterior */
        if($dias_semana[$i]<1) {
            if($mes==5 || $mes==7 || $mes==10 || $mes==12) {
                $dias_semana[$i]= 30+$dias_semana[$i];
                $mes--;
            }
            else if($mes==1 || $mes==2 || $mes==4 || $mes==6 || $mes==8 ||  $mes==9 || $mes==11) {
                $dia_semana[$i]= 31+$dias_semana[$i];
                $mes--;
            }
            else if($mes==3) {
                $bisiesto = checkdate(02,29,date(Y));
                if($bisiesto) $dias_semana[$i] = 29+$dias_semana[$i];
                else $dias_semana[$i]= 28+$dias_semana[$i];
                $mes--;
            }
            if($dias_semana[$i]!=$dia && $dias_semana[$i]<10) $dias_semana[$i]="0$dias_semana[$i]";
            $dias_semana[$i] = "$ano-0$mes-$dias_semana[$i]";
            $mes++;
        }
        /* Si el dia es correcto, lo formateamos de forma adecuada */
        else if($dias_semana[$i]>=1 && $dias_semana[$i]<30) {
            if($dias_semana[$i]!=$dia && $dias_semana[$i]<10) $dias_semana[$i]="0$dias_semana[$i]";
            $dias_semana[$i] = "$ano-$mes-$dias_semana[$i]";
        }
        elseif ($dias_semana[$i]>=1 && $dias_semana[$i]<31 && ($mes==1 || $mes==3 || $mes==5 || $mes==7 || $mes==8 || $mes==10 || $mes==12)){
            if($dias_semana[$i]!=$dia && $dias_semana[$i]<10) $dias_semana[$i]="0$dias_semana[$i]";
            $dias_semana[$i] = "$ano-0$mes-$dias_semana[$i]";
        }
        /* Si nos hemos pasado de dia, entonces estamos en el mes siguiente */
        elseif ($dias_semana[$i]>=1 && $dias_semana[$i]<30 && ($mes==4 || $mes==6 || $mes==9 || $mes==11)) {
            $mes+=1;
            $dia = $dias_semana[$i]-31;
            if($dias_semana[$i]!=$dia && $dias_semana[$i]<10) $dias_semana[$i]="0$dias_semana[$i]";
            $dias_semana[$i] = "$ano-0$mes-$dias_semana";
        }
    }
    /* Devolvemos el resultado como un array de fechas */
    return $dias_semana;
}

/* Esta funcion permite imprimir la fecha completa (dia/mes/año) del que se esta mostrando la informacion en 
 * el calendario
 * 
 * $dia = dia del que se quire mostrar información (Lunes, Martes, etc)
 * $semana = dias de la semana en la que nos encontramos con formato año/mes/dia
 * 
 * $imprimir_fecha = devuelve un String con la informacion a imprimir por el sistema
 */
function imprimir_fecha($dia,$semana) {
    /* El primer paso es formatear la fecha para adaptarla a la lectura habitual de España (dia/mes/año) */
    if ($dia=="Lunes" || $dia=="Monday") $fecha = explode("-",$semana[0]);  // Cogemos el primer dia de la semana si es lunes
    if ($dia=="Martes" || $dia=="Tuesday") $fecha = explode("-",$semana[1]); // Cogemos el segundo dia de la semana si es martes
    if ($dia=="Miércoles" || $dia=="Wednesday") $fecha = explode("-",$semana[2]); // Cogemos el tercer dia de la semana si es miercoles
    if ($dia=="Jueves" || $dia=="Thursday") $fecha = explode("-",$semana[3]); // Cogemos el cuarto dia de la semana si es jueves
    if ($dia=="Viernes" || $dia=="Friday") $fecha = explode("-",$semana[4]); // Cogemos el ultimo dia de la semana si es viernes
    
    /* Confeccionamos el valor a devolver por la funcion */
    $imprimir_fecha = "Estas son las reservas programadas para el  <b>$fecha[2]/$fecha[1]/$fecha[0]</b> <br/><br/>";
    
    return $imprimir_fecha; // Devolvemos el String con la información a imprimir por pantalla en HTML
}