<?php

/**
 * Modulo de administración para la gestión de las máquinas virtuales del gestor de reservas
 */

/* Funcion principal que hace las llamadas al resto de funciones de la clase gest_vm */
function administrar_opciones() {
    comprobar_peticion_opciones();
    load_html_options();
}

/* Funcion que comprueba las peticiones de cambios en la configuracion y actualiza los datos */
function comprobar_peticion_opciones() {
    global $wpdb;
    $modo = $_POST["modo"];
    
    if($modo=="actualizar") {
        /* Recogemos las variables a actualizar */
        $cuatrimestre1_ini = $_POST["cuatrimestre1_ini"];
        $cuatrimestre1_fin = $_POST["cuatrimestre1_fin"];
        $cuatrimestre2_ini = $_POST["cuatrimestre2_ini"];
        $cuatrimestre2_fin = $_POST["cuatrimestre2_fin"];
        $verano_ini = $_POST["verano_ini"];
        $verano_fin = $_POST["verano_fin"];
        $c_cuatrimestral = $_POST["c_cuatrimestral"];
        $c_puntual = $_POST["c_puntual"];
        $usuarioldap = $_POST["usuarioldap"];
        $passwordldap = $_POST["passwordldap"];
        $ldap = $_POST["ldap"];
        $dn = $_POST["dn"];

        /* Actualizamos los datos */
        $resultado = $wpdb->update(
                'reservas_opciones',
                array('valor' => formato_fecha($cuatrimestre1_ini)),
                array('nombre' => "cuatrimestre1_ini"));
        $resultado = $wpdb->update(
                'reservas_opciones',
                array('valor' => formato_fecha($cuatrimestre1_fin)),
                array('nombre' => "cuatrimestre1_fin"));
        $resultado = $wpdb->update(
                'reservas_opciones',
                array('valor' => formato_fecha($cuatrimestre2_ini)),
                array('nombre' => "cuatrimestre2_ini"));
        $resultado = $wpdb->update(
                'reservas_opciones',
                array('valor' => formato_fecha($cuatrimestre2_fin)),
                array('nombre' => "cuatrimestre2_fin",));
        $resultado = $wpdb->update(
                'reservas_opciones',
                array('valor' => formato_fecha($verano_ini)),
                array('nombre' => "verano_ini"));
        $resultado = $wpdb->update(
                'reservas_opciones',
                array('valor' => formato_fecha($verano_fin)),
                array('nombre' => "verano_fin"));
        $resultado = $wpdb->update(
                'reservas_opciones',
                array('valor' => $c_cuatrimestral),
                array('nombre' => "c_cuatrimestral"));
        $resultado = $wpdb->update(
                'reservas_opciones',
                 array('valor' => $c_puntual),
                 array('nombre' => "c_puntual"));
        $resultado = $wpdb->update(
                'reservas_opciones',
                array('valor' => $usuarioldap),
                array('nombre' => "usuarioldap"));
        $resultado = $wpdb->update(
                'reservas_opciones',
                array('valor' => $passwordldap),
                array('nombre' => "passwordldap"));
        $resultado = $wpdb->update(
                'reservas_opciones',
                array('valor' => $ldap),
                array('nombre' => "ldap"));
        $resultado = $wpdb->update(
                'reservas_opciones',
                array('valor' => $dn),
                array('nombre' => "dn"));
    }
}

/* Funcion que carga el codigo html del menu de opciones del plugin */
function load_html_options() {
    global $wpdb;
    
    /* Recogemos los distintos parametros de la configuracion */
    $cuatrimestre1_ini = formato_fecha($wpdb->get_var("SELECT valor FROM reservas_opciones WHERE nombre=\"cuatrimestre1_ini\""));
    $cuatrimestre1_fin = formato_fecha($wpdb->get_var("SELECT valor FROM reservas_opciones WHERE nombre=\"cuatrimestre1_fin\""));
    $cuatrimestre2_ini = formato_fecha($wpdb->get_var("SELECT valor FROM reservas_opciones WHERE nombre=\"cuatrimestre2_ini\""));
    $cuatrimestre2_fin = formato_fecha($wpdb->get_var("SELECT valor FROM reservas_opciones WHERE nombre=\"cuatrimestre2_fin\""));
    $verano_ini = formato_fecha($wpdb->get_var("SELECT valor FROM reservas_opciones WHERE nombre=\"verano_ini\""));
    $verano_fin = formato_fecha($wpdb->get_var("SELECT valor FROM reservas_opciones WHERE nombre=\"verano_fin\""));
    $c_cuatrimestral = $wpdb->get_var("SELECT valor FROM reservas_opciones WHERE nombre=\"c_cuatrimestral\"");
    $c_puntual = $wpdb->get_var("SELECT valor FROM reservas_opciones WHERE nombre=\"c_puntual\"");
    $usuarioldap = $wpdb->get_var("SELECT valor FROM reservas_opciones WHERE nombre=\"usuarioldap\"");
    $passwordldap = $wpdb->get_var("SELECT valor FROM reservas_opciones WHERE nombre=\"passwordldap\"");
    $ldap = $wpdb->get_var("SELECT valor FROM reservas_opciones WHERE nombre=\"ldap\"");
    $dn = $wpdb->get_var("SELECT valor FROM reservas_opciones WHERE nombre=\"dn\"");
    ?>
        <div class="wrap">
            <h2>Opciones</h2>
        </div>
        <?php alertas_opciones() ?>
        <h3 class="title">Cuatrimestres</h3>
        <p>
            Los cuatrimestres indican qué clases están activas en cada momento del curso. Así, las clases 
            "cuatrimestrales" que estén reservadas para el primer cuatrimestre solo serán visibles para los
            usuarios durante el periodo estimado para dicho cuatrimestre. Además, durante el periodo de verano
            solo se mostrarán las reservas puntuales.
        </p>
        <form action="<?php echo get_permalink(); ?>" method="POST">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">Primer cuatrimestre</th>
                    <td>
                        <fieldset>
                            Desde el <input type="text" name="cuatrimestre1_ini" value=<?php echo "$cuatrimestre1_ini" ?>>
                            al <input type="text" name="cuatrimestre1_fin" value=<?php echo "$cuatrimestre1_fin"?>>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Segundo cuatrimestre</th>
                    <td>
                        <fieldset>
                            Desde el <input type="text" name="cuatrimestre2_ini" value=<?php echo "$cuatrimestre2_ini" ?>>
                            al <input type="text" name="cuatrimestre2_fin" value=<?php echo "$cuatrimestre2_fin"?>>
                        </fieldset>
                    </td>
                </tr>
                                <tr>
                    <th scope="row">Verano</th>
                    <td>
                        <fieldset>
                            Desde el <input type="text" name="verano_ini" value=<?php echo "$verano_ini" ?>>
                            al <input type="text" name="verano_fin" value=<?php echo "$verano_fin"?>>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
        <h3 class="title">Estilo</h3>
        <p>
            El calendario de reservas utiliza un sistema de colores para distinguir entre las reservas puntuales
            y las cuatrimestrales. Estos colores pueden ser modificados para adaptarlos al tema de Wordpress que
            usted esté utilizando para su web. 
        </p>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">Color para reservas cuatrimestrales</th>
                    <td>
                        <fieldset>
                            <input type="color" name="c_cuatrimestral" value=<?php echo "$c_cuatrimestral" ?>>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Color para reservas puntuales</th>
                    <td>
                        <fieldset>
                            <input type="color" name="c_puntual" value=<?php echo "$c_puntual" ?>>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
        <h3 class="title">Conexión Ldap</h3>
        <p>
            La autenticación para el acceso al panel de usuario se realiza siempre a través de un servidor LDAP.
            Para que esto se realice de forma correcta, es necesario que configure correctamente los parámetros 
            de su conexión con el servidor.
        </p>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">Usuario</th>
                    <td>
                        <fieldset>
                            <input type="text" name="usuarioldap" value=<?php echo "$usuarioldap" ?>>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Contraseña</th>
                    <td>
                        <fieldset>
                            <input type="password" name="passwordldap" value=<?php echo "$passwordldap" ?>>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Servidor Ldap</th>
                    <td>
                        <fieldset>
                            <input type="text" name="ldap" value=<?php echo "$ldap" ?>>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Base DN</th>
                    <td>
                        <fieldset>
                            <input type="text" name="dn" value="<?php echo "$dn" ?>">
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="hidden" name="modo" value="actualizar">
            <input type="submit" name="submit" class="button button-primary" value="Guardar cambios">
        </p>
        </form>
        <div class="wrap">
            <h2>Ayuda</h2>
        </div>
        <h3 class="title">Shortcodes</h3>
        <p>
            Para el correcto uso del plugin es necesaria la utilización de códigos abreviados o shortcodes de
            Wordpress. Estos códigos sirven para invocar funciones que ejecuten distintas funcionalidades del
            plugin y muestren el contenido del mismo a los usuarios. Los distintos shortcodes que puede utilizar
            con este plugin son:
        </p>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">Reservas de hoy</th>
                    <td>
                        <fieldset>
                            [reservas]
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Reservas del día</th>
                    <td>
                        <fieldset>
                            [reservas dia="(Lunes),(Martes),etc"]
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Reservas por aula</th>
                    <td>
                        <fieldset>
                            [reservas aula="nombre aula"]
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Panel de usuario</th>
                    <td>
                        <fieldset>
                            [login]
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php
}

/* Función encargada de notificar al usuario si se han realizado las acciones que ha solicitado */
function alertas_opciones() {
    $modo = $_POST['modo']; // Determinamos si se ha solicitado y procesado alguna petición.

    if($modo=='actualizar') { // Si hemos insertado, mostramos la notificación pertinente.
        echo "<div id=\"message\" class=\"updated\"><p>Ajustes guardados.</p></div>";
    }
}

/* Función encargada de convertir la fecha de formato mm/dd a dd/mm y viceversa */
function formato_fecha($fecha) {
    $inversor = explode("/",$fecha);    // Separamos el dia y el mes
    $newFecha = "$inversor[1]/$inversor[0]"; // Invertimos el formato
    
    return $newFecha;   // Devolvemos la fecha con el nuevo formato
}

