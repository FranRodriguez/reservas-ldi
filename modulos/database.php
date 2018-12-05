<?php
/**
 * Creación de la base de datos del sistema gestor de reservas
 */

/**
 * Creamos las tablas requeridas por el plugin.
 */
abstract class database {
    function definirTablas() {

        $sql = "CREATE TABLE reservas_opciones (
            `nombre` varchar(40) NOT NULL,
            `valor` varchar(140),

            PRIMARY KEY(`nombre`)
            );
            
            CREATE TABLE reservas_aulas (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `nombre` varchar(10) NOT NULL,
            `posicion` int(2) NOT NULL,

            PRIMARY KEY(`id`)
            );

            CREATE TABLE reservas_maquinas_virtuales(
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `nombre` varchar(30) NOT NULL,
            `ssoo` varchar(30) NOT NULL, 
            `aplicaciones` varchar(140),
            `capacidad` varchar(10),
            `aulas` varchar(100),

            PRIMARY KEY(`id`)
            );

            CREATE TABLE reservas_software(
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `nombre` varchar(100) NOT NULL,
            `version` varchar(10) NOT NULL,
            `url` varchar(100),
            `descripcion` varchar(140),
            `fecha_instalacion` DATE,
            `instalacion` varchar(500),

            PRIMARY KEY (`id`)
            );

            CREATE TABLE reservas_grado(
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `nombre` varchar(100) NOT NULL,
            `grupos` varchar(140) NOT NULL,

            PRIMARY KEY (`id`)
            );

            CREATE TABLE reservas_asignatura(
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `nombre` varchar(100) NOT NULL,
            `grado` int(11) NOT NULL REFERENCES grado(`id`),
            `cuatrimestre` tinyint(1) NOT NULL DEFAULT '1',
            `curso` int(1),

            PRIMARY KEY (`id`)
            );

            CREATE TABLE reservas_mv_asig(
            `asignatura` int(11) NOT NULL REFERENCES asignatura(`id`),
            `maquina_virtual` int(11) NOT NULL REFERENCES maquinas_virtuales(`id`),

            PRIMARY KEY (`asignatura`,`maquina_virtual`)
            );

            CREATE TABLE reservas_sw_asig(
            `asignatura` int(11) NOT NULL REFERENCES asignatura(`id`),
            `software` int(11) NOT NULL REFERENCES software(`id`),

            PRIMARY KEY (`asignatura`,`software`)
            );

            CREATE TABLE reservas_reservas (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `confirmada` tinyint(1) NOT NULL DEFAULT '1',
            `asignatura` int(11) NOT NULL REFERENCES asignatura(`id`),
            `grupo` varchar(5) NOT NULL,
            `profesor` varchar(100) NOT NULL,
            `aula` int(11) NOT NULL REFERENCES aulas(`id`),
            `tipo_reserva` tinyint(1) NOT NULL,
            `dia` ENUM('Lunes','Martes','Miércoles','Jueves','Viernes') NOT NULL,
            `cuatrimestre` tinyint(1) NOT NULL DEFAULT '1',
            `fecha` DATE NOT NULL,
            `hora_inicio` time NOT NULL,
            `hora_fin` time NOT NULL,

            PRIMARY KEY (`id`)
            )";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    /* Funcion para cargar los valores por defecto de las opciones en la tabla de la base de datos
     * 
     */
    function generarOpciones() {
        global $wpdb;
        
        /* Realizamos la inserción en la BBDD con los datos proporcionados */
        $resultado = $wpdb->insert(
                'reservas_opciones',
                array(
                    'nombre' => "cuatrimestre1_ini",
                    'valor' => "09/01",
                ));
        $resultado = $wpdb->insert(
                'reservas_opciones',
                array(
                    'nombre' => "cuatrimestre1_fin",
                    'valor' => "12/31",
                ));
        $resultado = $wpdb->insert(
                'reservas_opciones',
                array(
                    'nombre' => "cuatrimestre2_ini",
                    'valor' => "01/01",
                ));
        $resultado = $wpdb->insert(
                'reservas_opciones',
                array(
                    'nombre' => "cuatrimestre2_fin",
                    'valor' => "06/30",
                ));
        $resultado = $wpdb->insert(
                'reservas_opciones',
                array(
                    'nombre' => "verano_ini",
                    'valor' => "07/01",
                ));
        $resultado = $wpdb->insert(
                'reservas_opciones',
                array(
                    'nombre' => "verano_fin",
                    'valor' => "08/31",
                ));
        $resultado = $wpdb->insert(
                'reservas_opciones',
                array(
                    'nombre' => "c_cuatrimestral",
                    'valor' => "grey",
                ));
        $resultado = $wpdb->insert(
                'reservas_opciones',
                array(
                    'nombre' => "c_puntual",
                    'valor' => "pink",
                ));
        $resultado = $wpdb->insert(
                'reservas_opciones',
                array(
                    'nombre' => "ldap",
                    'valor' => "ldaps://ldap",
                ));
        $resultado = $wpdb->insert(
                'reservas_opciones',
                array(
                    'nombre' => "usuarioldap",
                    'valor' => "consultas",
                ));
        $resultado = $wpdb->insert(
                'reservas_opciones',
                array(
                    'nombre' => "passwordldap",
                    'valor' => "PASSWORD",
                ));
        $resultado = $wpdb->insert(
                'reservas_opciones',
                array(
                    'nombre' => "dn",
                    'valor' => "ou=People",
                ));
    }
    
    function get_opcion($nombre) {
        global $wpdb;
        
        $resultado = $wpdb->get_var(
                "SELECT valor FROM reservas_opciones WHERE nombre=\"$nombre\"");
        
        return $resultado;
    }
    
    /* Funcion para obtener las reservas en funcion de uno de sus atributos
     * 
     * $atributo: nombre del atributo por el que se quiere filtrar
     * $valor: valor del atributo
     */
    function get_reservas( $atributo, $valor) {
        global $wpdb;
        
        if($atributo=="dia") $valor = database::formatear_fecha($valor);    //Nos aseguramos de poner la fecha en el formato correcto
        if($atributo==NULL) {
            $query = "SELECT * FROM reservas_reservas"; // Si no le pasamos atributo, entonces selecciona toda la tabla
        }
        else {
            $query = "SELECT * FROM reservas_reservas WHERE $atributo=\"$valor\""; //Creamos la consulta
        }
                
        $resultado = $wpdb->get_results($query);    //Ejecutamos la consulta
        
        return $resultado;
    }
    
    function get_reservas_pendientes() {
        global $wpdb;
        
        $query = "SELECT COUNT(*) FROM reservas_reservas WHERE confirmada=0";
        $resultado = $wpdb->get_var($query);
        
        return $resultado;
    }
    
    function confirmar_reservas_pendientes() {
        global $wpdb;
        
        $wpdb->update(
                'reservas_reservas', array('confirmada' =>1), array('confirmada' => 0));
        
    }
    
    /* Funcion para insertar una nueva reserva en la base de datos
     * 
     * $confirmada: 0 si no está confirmada, 1 si la reserva es confirmada
     * $asignatura: id de la asignatura para la reserva
     * $grupo: grupo de la asignatura para la reserva
     * $profesor: profesor que dará la clase o tutor de la asignatura
     * $aula: id del aula para la reserva 
     * $tipo_reserva: naturaleza de la reserva: 0 cuatrimestral, 1 puntual.
     * $dia: dia de la semana (en castellano) en el que se realizará la reserva
     * $cuatrimestre: el cuatrimestre de la reserva
     * $fecha: fecha en formato DD/MM/AA para la reserva
     * $hora_inicio: hora de comienzo de la reserva
     * $hora_fin: hora de finalización de la reserva
     */
    function set_reserva($confirmada,$asignatura,$grupo,$profesor,$aula,$tipo_reserva,$dia,$cuatrimestre,$fecha,$hora_inicio,$hora_fin) {
        global $wpdb;
 
        /* Realizamos la inserción en la BBDD con los datos proporcionados */
        $resultado = $wpdb->insert(
                'reservas_reservas',
                array(
                    'confirmada' => $confirmada,
                    'asignatura' => $asignatura,
                    'grupo' => $grupo,
                    'profesor' => $profesor,
                    'aula' => $aula,
                    'tipo_reserva' => $tipo_reserva,
                    'dia' => $dia,
                    'cuatrimestre' => $cuatrimestre,
                    'fecha' => $fecha,
                    'hora_inicio' => $hora_inicio,
                    'hora_fin' => $hora_fin
                ));
        $resultado = $wpdb->get_results("SELECT * FROM reservas_reservas WHERE "
                . "confirmada=\"$confirmada\" AND "
                . "asignatura=\"$asignatura\" AND "
                . "profesor=\"$profesor\" AND "
                . "aula=\"$aula\" AND "
                . "tipo_reserva=\"$tipo_reserva\" AND "
                . "dia=\"$dia\" AND "
                . "cuatrimestre=\"$cuatrimestre\" AND "
                . "fecha=\"$fecha\" AND "
                . "hora_inicio=\"$hora_inicio\" AND "
                . "hora_fin=\"$hora_fin\"");
        return $resultado[0]->id;
    }
    
    /* Funcion para obtener todas las aulas de la base de datos */
    function get_aulas( $atributo, $valor) {
        global $wpdb;
        
        if($atributo==NULL) {
            $query = "SELECT * FROM reservas_aulas ORDER BY posicion";    //Creamos la consulta   
        }
        else {
            $query = "SELECT * FROM reservas_aulas WHERE $atributo=\"$valor\" ORDER BY posicion"; //Creamos la consulta
        }   
        $resultado = $wpdb->get_results($query);    //Ejecutamos la consulta
        
        return $resultado;
    }
    
    /* Funcion para insertar un nuevo aula en la base de datos
     * 
     * $nombre: el nombre del aula que queremos crear
     * $posicion: la prioridad con respecto a otras aulas
     */
    function set_aula($nombre, $posicion) {
        global $wpdb;

        /* Realizamos la inserción en la BBDD con los datos proporcionados */
        $resultado = $wpdb->insert(
        'reservas_aulas',
        array(
            'nombre' => $nombre,
            'posicion' => $posicion,
        ));
    }
    
    /* Funcion para obtener las asignaturas en funcion de uno de sus atributos 
     * 
     * $atributo: nombre del atributo por el que se quiere filtrar
     * $valor: valor del atributo
     */
    function get_asignatura($atributo, $valor) {
        global $wpdb;
        
        if($atributo==NULL) {
            $query = "SELECT * FROM reservas_asignatura";
        }
        else {
            $query = "SELECT * FROM reservas_asignatura WHERE $atributo=\"$valor\"";   //Creamos la consulta    
        }
        
        $resultado = $wpdb->get_results($query);    //Ejecutamos la consulta
        
        return $resultado;
    }
    
    /* Funcion para obtener todas las aulas de la base de datos */
        function get_asignaturas() {
        global $wpdb;
        
        $query = "SELECT DISTINCT nombre FROM `reservas_asignatura` ";    //Creamos la consulta  
        $resultado = $wpdb->get_results($query);    //Ejecutamos la consulta
        
        return $resultado;
    }
    
    /* Función para insertar una nueva asignatura en la base de datos
     * 
     * $nombre: el nombre de la nueva asignatura
     * $grado: la titulación a la que pertenece la asignatura
     * $curso: curso en el que se imparte la asignatura
     */
    function set_asignatura($nombre,$grado,$curso,$cuatrimestre,$software,$maquinas) {
        global $wpdb;

        /* Realizamos la inserción en la BBDD con los datos proporcionados */
        $resultado = $wpdb->insert(
        'reservas_asignatura',
        array(
            'nombre' => $nombre,
            'grado' => $grado,
            'curso' => $curso,
            'cuatrimestre' => $cuatrimestre,
        ));
        
        /* Añadimos a la tabla de software todas las entradas para la asignatura */
        $software = explode(",",$software);
        for($i=0;$i<count($software)-1;$i++) {
            $id_asignatura= database::get_asignatura("nombre",$nombre);
            $id_software= $software[$i];
            
            database::set_asignatura_software($id_asignatura[0]->id,$id_software);
        }
        
        /* Añadimos a la tabla de maquinas virtuales todas las entradas para dicha asignatura */
        $maquinas = explode(",",$maquinas);
        for($j=0;$j<count($maquinas)-1;$j++) {
            $id_asignatura = database::get_asignatura("nombre",$nombre);
            $id_maquina = $maquinas[$j];
            
            database::set_asignatura_maquinavirtual($id_asignatura[0]->id,$id_maquina);
        }
    }
    
    function get_grado($atributo, $valor) {
        global $wpdb;
        
        if($atributo==NULL) {
            $query = "SELECT * FROM reservas_grado";
        }
        else {
            $query = "SELECT * FROM reservas_grado WHERE $atributo=\"$valor\"";   //Creamos la consulta    
        }
        
        $resultado = $wpdb->get_results($query);    //Ejecutamos la consulta
        
        return $resultado;
    }
    
    function set_grado($nombre, $grupos) {
        global $wpdb;

        /* Realizamos la inserción en la BBDD con los datos proporcionados */
        $resultado = $wpdb->insert(
        'reservas_grado',
        array(
            'nombre' => $nombre,
            'grupos' => $grupos,
        ));
    }
    
    /* Funcion para obtener el software en funcion de uno de sus atributos
     * 
     * $atributo: nombre del atributo por el que se quiere filtrar
     * $valor: valor del atributo
     */
    function get_software( $atributo, $valor) {
        global $wpdb;
        
        if($atributo==NULL) {
            $query = "SELECT * FROM reservas_software"; // Si no le pasamos atributo, entonces selecciona toda la tabla
        }
        else {
            $query = "SELECT * FROM reservas_software WHERE $atributo=\"$valor\""; //Creamos la consulta
        }
                
        $resultado = $wpdb->get_results($query);    //Ejecutamos la consulta
        
        return $resultado;
    }
    
    function set_software($nombre,$version,$url,$descripcion,$fecha,$instalacion) {
        global $wpdb;

        /* Realizamos la inserción en la BBDD con los datos proporcionados */
        $resultado = $wpdb->insert(
        'reservas_software',
        array(
            'nombre' => $nombre,
            'version' => $version,
            'url' => $url,
            'descripcion' => $descripcion,
            'fecha_instalacion' => $fecha,
            'instalacion' => $instalacion,
        ));
    }
    
        /* Funcion para obtener el software en funcion de uno de sus atributos
     * 
     * $atributo: nombre del atributo por el que se quiere filtrar
     * $valor: valor del atributo
     */
    function get_maquinas_virtuales( $atributo, $valor) {
        global $wpdb;
        
        if($atributo==NULL) {
            $query = "SELECT * FROM reservas_maquinas_virtuales"; // Si no le pasamos atributo, entonces selecciona toda la tabla
        }
        else {
            $query = "SELECT * FROM reservas_maquinas_virtuales WHERE $atributo=\"$valor\""; //Creamos la consulta
        }
                
        $resultado = $wpdb->get_results($query);    //Ejecutamos la consulta
        
        return $resultado;
    }
    
    function set_maquina_virtual($nombre,$so,$aplicaciones,$capacidad,$aulas) {
        global $wpdb;

        /* Realizamos la inserción en la BBDD con los datos proporcionados */
        $resultado = $wpdb->insert(
        'reservas_maquinas_virtuales',
        array(
            'nombre' => $nombre,
            'ssoo' => $so,
            'aplicaciones' => $aplicaciones,
            'capacidad' => $capacidad,
            'aulas' => $aulas,
        ));
    }
    
    /* Funcion para obtener los datos del profesor en funcion de su correo electronico
     * 
     * $correo: correo electronico del profesor que imparte la asignatrua
     */
    function get_profesor($correo) {      
        /* Obtenemos la configuración del ldap de las opciones del plugin */
        $servidor_LDAP = database::get_opcion("ldap");
        $dn = database::get_opcion("dn");
        
        /* Obtenemos el nombre de usuario y contraseña para acceder al ldap */
        $usuarioldap = database::get_opcion("usuarioldap");
        $passwordldap = database::get_opcion("passwordldap");
        
        /* Nos conectamos */
        $conectado_LDAP = ldap_connect($servidor_LDAP);
        $option = ldap_set_option($conectado_LDAP, LDAP_OPT_PROTOCOL_VERSION, 3); // Comprobamos la conexión
        
        /* Si hay usuario o contraseña, nos conectamos */
        if($usuarioldap!="" && $passwordldap!="") {
            $option = ldap_bind($conectado_LDAP, "$usuarioldap,$dn",$passwordldap);
        }
        
        /* Realizamos la búsqueda */
        $user = explode("@",$correo);
        $sr=ldap_search($conectado_LDAP, $dn, "(&(uid=".$user[0]."))");
        $info = ldap_get_entries($conectado_LDAP, $sr);
        
        /* Obtenemos el nombre del profesor y devolvemos el resultado */
        $profesor = $info[0]["cn"][0];
        return $profesor;
    }
    
    function get_profesores() {
        global $wpdb;
        
        $query = "SELECT DISTINCT profesor FROM `reservas_reservas` ";    //Creamos la consulta  
        $resultado = $wpdb->get_results($query);    //Ejecutamos la consulta
        
        return $resultado;
    }
    
    function set_asignatura_software($asignatura,$software) {
        global $wpdb;

        /* Realizamos la inserción en la BBDD con los datos proporcionados */
        $resultado = $wpdb->insert(
        'reservas_sw_asig',
        array(
            'asignatura' => $asignatura,
            'software' => $software,
        ));
    }
    
    function set_asignatura_maquinavirtual($asignatura,$maquina) {
        global $wpdb;

        /* Realizamos la inserción en la BBDD con los datos proporcionados */
        $resultado = $wpdb->insert(
        'reservas_mv_asig',
        array(
            'asignatura' => $asignatura,
            'maquina_virtual' => $maquina,
        ));
    }
    
    /* Esta funcion permite corregir el valor que PHP asigna al dia de la semana (en ingles) y ponerlo en un formato entendible por nuestro plugin
     * 
     * $valor: el valor de la fecha que se quiere formatear
     */
    function formatear_fecha($valor) {  
        if($valor=="Monday") $valor="Lunes";
        if($valor=="Tuesday") $valor="Martes";
        if($valor=="Wednesday") $valor="Miércoles";
        if($valor=="Thursday") $valor="Jueves";
        if($valor=="Friday") $valor="Viernes";
        
        return $valor;
    }
    
        /* Funcion para obtener las reservas en funcion de uno de sus atributos
     * 
     * $atributo: nombre del atributo por el que se quiere filtrar
     * $valor: valor del atributo
     */
    function get_reservas_for_statistics($cuatrimestre, $anno) {
        error_reporting(0);
        global $wpdb;
        
        $query = "SELECT * FROM reservas_reservas WHERE cuatrimestre='$cuatrimestre' AND fecha>='$anno-01-01' AND fecha<'".($anno+1)."-01-01' ORDER BY asignatura,aula"; //Creamos la consulta
        $resultado = $wpdb->get_results($query);    //Ejecutamos la consulta

        $size = 0;
        $statistics[100] = new ArrayObject();
        for($i=0;$i<count($resultado);$i++) {
            if($resultado[$i-1]->asignatura==$resultado[$i]->asignatura) {
                if($resultado[$i-1]->aula==$resultado[$i]->aula) {                   
                    $statistics[$size]->asignatura = $resultado[$i]->asignatura;
                    $statistics[$size]->aula = $resultado[$i]->aula;
                    if($resultado[$i]->tipo_reserva==0) $statistics[$size]->horas+=14;
                    if($resultado[$i]->tipo_reserva==1) $statistics[$size]->horas+=2;                  
                }
                else {
                    $size++;
                    $statistics[$size]->asignatura = $resultado[$i]->asignatura;
                    $statistics[$size]->aula = $resultado[$i]->aula;
                    if($resultado[$i]->tipo_reserva==0) $statistics[$size]->horas+=14;
                    if($resultado[$i]->tipo_reserva==1) $statistics[$size]->horas+=2; 
                } 
            }
            else {
                $size++;
                $statistics[$size]->asignatura = $resultado[$i]->asignatura;
                $statistics[$size]->aula = $resultado[$i]->aula;
                if($resultado[$i]->tipo_reserva==0) $statistics[$size]->horas+=14;
                if($resultado[$i]->tipo_reserva==1) $statistics[$size]->horas+=2;   
            }
        }
        
        return $statistics;
    }
    
    function get_years_for_statistics() {
        global $wpdb;
        
        $query = "SELECT DISTINCT fecha FROM reservas_reservas ORDER BY fecha DESC"; //Creamos la consulta
        $date_list = $wpdb->get_results($query);    //Ejecutamos la consulta
        $year_list[count($date_list)];
        $size=0;
        
        for($i=0;$i<count($date_list);$i++) {
             $explode_list = explode("-",$date_list[$i]->fecha);
             
             if($size==0) {
                $year_list[$size] = $explode_list[0];
                $size++;
             }
             else if($year_list[$size-1]!=$explode_list[0]) {
                 
                 $year_list[$size] = $explode_list[0];
                 $size++;
             }
        }
        $years[$size];
        for($i=0;$i<$size;$i++) {
          $years[$i]=$year_list[$i];  
        }
        return $years;
        
    }
}