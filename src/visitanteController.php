<?php
    namespace Kevin\Pruebas;

    
    use Kevin\Pruebas\mainModel;

    class VisitanteController extends mainModel {
        public function registrarVisitanteControler(array $dataVisitante){
            
            if ($dataVisitante['REQUEST_METHOD'] != 'POST') {
                $mensaje=[
                    "titulo"=>"Peticion incorrecta",
                    "mensaje"=>"Lo sentimos, la accion que intentas realizar no es correcta"
                ];
                return json_encode($mensaje);
                exit();


            }else {/* Validacion de la no existencia de variables post y que vengan vacias a excepcion de los campos de vehiculos que no son obligatorios para el registro para eviar una alerta de error con los campos. */
                if (!isset($dataVisitante['nombres_visitante'],
                $dataVisitante['tipo_doc_visitante'],
                $dataVisitante['correo_visitante'],
                $dataVisitante['apellidos_visitante'],
                $dataVisitante['num_documento_visitante'],
                $dataVisitante['telefono_visitante'])
                ||
                $dataVisitante['nombres_visitante'] == ""  ||
                $dataVisitante['tipo_doc_visitante'] == ""  ||
                $dataVisitante['correo_visitante'] == ""  ||
                $dataVisitante['apellidos_visitante'] == ""  ||
                $dataVisitante['num_documento_visitante'] == ""  ||
                $dataVisitante['telefono_visitante']== "") {
                    $mensaje=[
                        "titulo"=>"Error",
                        "mensaje"=>"Lo sentimos, a ocurrido un error con alguno de los datos, intentalo de nuevo mas tarde."
                    ];
                    return json_encode($mensaje);
                    exit();
                }else {
                    


                    $nombre = $this->limpiarDatos($dataVisitante['nombres_visitante']); 
                    
                    $tipo_doc = $this->limpiarDatos($dataVisitante['tipo_doc_visitante']); 
                    
                    $correo = $this->limpiarDatos($dataVisitante['correo_visitante']); 
                    $apellidos = $this->limpiarDatos($dataVisitante['apellidos_visitante']); 
                    $num_documento = $this->limpiarDatos($dataVisitante['num_documento_visitante']);
                    $telefono = $this->limpiarDatos($dataVisitante['telefono_visitante']);
                        

                    unset($dataVisitante['nombres_visitante'],$dataVisitante['tipo_doc_visitante'],$dataVisitante['correo_visitante'],$dataVisitante['apellidos_visitante'],$dataVisitante['num_documento_visitante'], $dataVisitante['telefono_visitante']);

                    $campos_invalidos = [];
                    if ($this->verificarDatos('[A-Za-z ]{2,64}', $nombre)) {
                        array_push($campos_invalidos, 'NOMBRE(S)');
                    }else {
                        $nombre_vs = $nombre;
                    }
                    if ($this->verificarDatos('[A-Z]{2}', $tipo_doc)) {
                        array_push($campos_invalidos, 'TIPO DE DOCUMENTO');
                    }else {
                        $tipo_doc_vs = $tipo_doc;
                    }
                    if ($this->verificarDatos('[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}', $correo)) {
                        array_push($campos_invalidos, 'CORREO ELECTRONICO');
                    }else {
                        $correo_vs = $correo;
                    }
                    if ($this->verificarDatos('[A-Za-z ]{2,64}', $apellidos)) {
                        array_push($campos_invalidos, 'APELLIDO(S)');
                    }else {
                        $apellidos_vs = $apellidos;
                    }
                    if ($this->verificarDatos('[0-9]{6,15}',$num_documento)) {
                        array_push($campos_invalidos, 'NUMERO DE DOCUMENTO');
                        
                    }else {
                        $num_documento_vs = $num_documento; 
                    }
                    if ($this->verificarDatos('[0-9]{10}', $telefono)) {
                        array_push($campos_invalidos, 'TELEFONO.');
                    }else {
                        $telefono_vs = $telefono;
                    }

                    unset($nombre, $tipo_doc, $correo, $apellidos, $num_documento, $telefono);

                    if (isset($dataVisitante['tipo_vehiculo_visitante'], $dataVisitante['placa_vehiculo_visitante'])) { 
                        
                        $tipo_vehiculo = $this->limpiarDatos($dataVisitante['tipo_vehiculo_visitante']);
                        $placa_vehiculo = $this->limpiarDatos($dataVisitante['placa_vehiculo_visitante']); 
                        if ($tipo_vehiculo != "" && $placa_vehiculo != "") {
                        
                            if ($this->verificarDatos('[A-Z]{2,}',$tipo_vehiculo)) {
                                array_push($campos_invalidos, 'TIPO DE VEHICULO');
                            }else{
                                $tipo_vehiculo_vs = $tipo_vehiculo;
                            }
                            if ($this->verificarDatos('[A-Z0-9]{6,7}',$placa_vehiculo)) {
                                array_push($campos_invalidos, 'PLACA DE VEHICULO');
                            }else {
                                $placa_vehiculo_vs = $placa_vehiculo;
                            }
    
                            unset($placa_vehiculo, $tipo_vehiculo);
    
                        }elseif ($tipo_vehiculo != "" && $placa_vehiculo == "") {
                            $mensaje=[
                                "titulo"=>"Campo incompleto",
                                "mensaje"=>"Lo sentimos, el campo de PLACA DE VEHICULO esta incompleto."
                            ];
                            echo json_encode($mensaje);
                            exit();
                        }elseif($tipo_vehiculo == "" && $placa_vehiculo != "") {
                            $mensaje=[
                                "titulo"=>"Campo incompleto",
                                "mensaje"=>"Lo sentimos, el campo de TIPO DE VEHICULO esta incompleto.",
                                "icono"=> "error",
                                "tipoMensaje"=>"normal"
                            ];
                            echo json_encode($mensaje);
                            exit();
                        }
                    }


                    if (count($campos_invalidos) > 0) {
                        $invalidos = "";
                        foreach ($campos_invalidos as $campos) {
                            if ($invalidos == "") {
                                $invalidos = $campos;
                            }else {
                                $invalidos = $invalidos.", ".$campos;
                            }
                        }
                        $mensaje=[
                            "titulo"=>"Campos incompletos",
                            "mensaje"=>"Lo sentimos, los campos no cumplen con el formato solicitado."
                        ];
                        return json_encode($mensaje);
                        exit();
                    }else {
                        $buscar_visitante_query = "
                            SELECT 'aprendices' AS tabla, a.num_identificacion, a.estado 
                            FROM aprendices a 
                            WHERE num_identificacion = '$num_documento_vs' AND a.estado = 'ACTIVO'
                        UNION ALL
                            SELECT 'funcionarios' AS tabla, fn.num_identificacion, fn.estado 
                            FROM funcionarios fn
                            WHERE num_identificacion = '$num_documento_vs' AND fn.estado = 'ACTIVO'
                        UNION ALL
                            SELECT 'vigilantes' AS tabla, vi.num_identificacion, vi.estado 
                            FROM vigilantes vi 
                            WHERE num_identificacion = '$num_documento_vs' AND vi.estado = 'ACTIVO'
                        UNION ALL
                            SELECT 'visitantes' AS tabla, vs.num_identificacion, vs.estado 
                            FROM visitantes vs 
                            WHERE num_identificacion = '$num_documento_vs';
                        ";
                        $buscar_visitante = $this->ejecutarConsulta($buscar_visitante_query);
                        unset($buscar_visitante_query);
                        if (!$buscar_visitante) {
                            $mensaje=[
                                "titulo"=>"Error de Conexion",
                                "mensaje"=>"Lo sentimos, algo salio mal con la conexion por favor intentalo de nuevo mas tarde.",
                                "icono"=> "error",
                                "tipoMensaje"=>"normal"
                            ];
                            echo json_encode($mensaje);
                            exit();
                        }else {
                            if ($buscar_visitante->num_rows < 1) {
                                if (!isset($tipo_doc_vs,$num_documento_vs,$nombre_vs,$apellidos_vs,$correo_vs,$telefono_vs)) {
                                    $mensaje=[
                                        "titulo"=>"Error al registrar",
                                        "mensaje"=>"Lo sentimos, algo salio mal con el registro por favor intentalo de nuevo mas tarde, si el error persiste comunicate con un asesor.",
                                        "icono"=> "error",
                                        "tipoMensaje"=>"normal"
                                    ];
                                    echo json_encode($mensaje);
                                    exit();
                                
                                }else {
                                    $fecha_hora_actual = date('Y-m-d H:i:s');
                                    $registrar_visitante_query = "INSERT INTO visitantes( tipo_documento, num_identificacion, nombres, apellidos, correo, telefono, estado,  permanencia, fecha_hora_registro) VALUES ('$tipo_doc_vs','$num_documento_vs','$nombre_vs','$apellidos_vs','$correo_vs','$telefono_vs','ACTIVO','FUERA','$fecha_hora_actual')";

                                    unset($tipo_doc_vs,$correo_vs,$telefono_vs);
                                    $registrar_visitante = $this->ejecutarConsulta($registrar_visitante_query);
                                    if (!$registrar_visitante) {
                                        $mensaje=[
                                            "titulo"=>"Error al registrar",
                                            "mensaje"=>"Lo sentimos, algo salio mal con el registro por favor intentalo de nuevo mas tarde, si el error persiste comunicate con un asesor.",
                                            "icono"=> "error",
                                            "tipoMensaje"=>"normal"
                                        ];
                                        echo json_encode($mensaje);
                                        exit();
                                    }else {
                                        if (!isset($tipo_vehiculo_vs,$placa_vehiculo_vs)) {
                                            $mensaje=[
                                                "titulo"=>"Visitante registrado",
                                                "mensaje"=>"Genial, el visitante ".$nombre_vs." ".$apellidos_vs." fue registrado con existo en nuetra base de datos.",
                                                "icono"=> "success",
                                                "tipoMensaje"=>"confirmado"
                                            ];
                
                                            echo json_encode($mensaje);
                                            exit();
                                        }else {
                                            $vehiculo_persona = $this->registrarNuevoVehiculo($placa_vehiculo_vs,$tipo_vehiculo_vs,$num_documento_vs, $_SESSION['datos_usuario']['num_identificacion']);
                                            if (!$vehiculo_persona) {// manejar las respuestas que nos da el metrodo de registrar el vehiculo
                                                $mensaje=[
                                                    "titulo"=>"Informacion",
                                                    "mensaje"=>"Genial, el visitante a sido registrado pero el registro de el vehiculo no ha sido exitoso.",
                                                    "icono"=> "info",
                                                    "tipoMensaje"=>"normal"
                                                ];
                                                echo json_encode($mensaje);
                                                exit();
                                            }else{
                                                $mensaje=[
                                                    "titulo"=>"Visitante Registrado",
                                                    "mensaje"=>"Genial, el visitante ".$nombre_vs." ".$apellidos_vs." fue registrado con exito y el  vehiculo con placas ".$placa_vehiculo_vs." fue asociado a el exitosamente.",
                                                    "icono"=> "success",
                                                    "tipoMensaje"=>"normal"
                                                ];
                                                return json_encode($mensaje);
                                                exit();
                                            }
                                        }
                                    }
                                }
                                
                            }else {
                                $datos_repetidos = $buscar_visitante->fetch_all();
                                $tabla = '';
                                unset($buscar_visitante);
                                foreach ($datos_repetidos as $datos) {
                                    if ($datos[0] != 'visitantes') {
                                        if ($datos[2] == 'ACTIVO' || $datos[2] == 'PERMANECE' ) {

                                            $userSinS = rtrim($datos[0], 's');

                                            $mensaje=[
                                                "titulo"=>"Informacion",
                                                "mensaje"=> $nombre_vs." con numero de documento ".$num_documento_vs." ya se encuentra en nuestra base de datos como ".$userSinS.".",
                                                "icono"=> "info",
                                                "tipoMensaje"=>"normal"
                                            ];
                                            echo json_encode($mensaje);
                                            exit();
                                        }else {
                                            $mensaje=[
                                                "titulo"=>"Pendiente",
                                                "mensaje"=> "Pendiente por programar",
                                                "icono"=> "info",
                                                "tipoMensaje"=>"normal"
                                            ];
                                            echo json_encode($mensaje);
                                            exit();
                                        }
                                    }else {
                                        if ($datos[2] == 'ACTIVO' || $datos[2] == 'PERMANECE' ) {
                                            $mensaje=[
                                                "titulo"=>"Informacion",
                                                "mensaje"=>"El Señor(a) ".$nombre_vs."  ya se encuentra en nuestra base de datos como visitante.",
                                                "icono"=> "info",
                                                "tipoMensaje"=>"normal"
                                            ];
                                            echo json_encode($mensaje);
                                        }elseif ($datos[2] == 'INACTIVO'){
                                            $mensaje=[
                                                "titulo"=>"Informacion",
                                                "mensaje"=>$nombre_vs." con numero de documento ".$num_documento_vs." ya se encuentra en nuestra base de datos inactivo por algun motivo, si deseas cambiar su estado a activo debera hacerlo una persona autoriazada desde el apartado de visitantes INACTIVOS.",
                                                "icono"=> "info",
                                                "tipoMensaje"=>"normal"
                                            ];
                                            echo json_encode($mensaje);
                                            exit();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }  
            }
        }

        
        public function vehiculosVisitante(string $num_id){
         
            $num_identificacion = $this->limpiarDatos($num_id);

            $consultar_vehiculo_query = "SELECT * FROM `vehiculos_personas` WHERE num_identificacion_persona = '$num_identificacion';";
            $consultar_vehiculo = $this->ejecutarConsulta($consultar_vehiculo_query);
            unset($num_id,$num_identificacion,$consultar_vehiculo_query);
            if (!$consultar_vehiculo) {
                
                exit();
            }else {
                if ($consultar_vehiculo->num_rows < 1) {
                
                    $titulo = 'Error';
                    $descripcion = 'Lo sentimos, parece que el visitante que intentas editar no se encuentra en la base de datos.';
                    $alerta_mensaje = 'alerta_mensaje';
                    echo '<tr><td colspan="6">No se encontraron Vehiculos asociados a esta persona</td></tr>';
                    exit();
                }else {
                    $tabla = '';
                    while ($datos = $consultar_vehiculo->fetch_object()) {
                        $tabla.='
                            <tr >
                                <td>'.$datos->num_identificacion_persona.'</td>
                                <td>'.$datos->placa_vehiculo.'</td>
                                <td>'.$datos->permanencia.'</td>
                                <td>
                                    <a color="Blue" href="vehiculoUpdate/'.$datos->placa_vehiculo.'/" class="button is-success is-rounded is-small">
                                        Actualizar
                                    </a>
                                </td>
                                <td>
                                    <form class="FormularioAjax" action="app/ajax/vehiculoAjax.php" method="POST" autocomplete="off" >
    
                                        <input type="hidden" name="modulo_cliente" value="eliminar">
                                        <input type="hidden" name="cliente_id" value="'.$datos->placa_vehiculo.'">
    
                                        <button color="red" type="submit" class="button is-danger is-rounded is-small">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        ';
                    }
                    $consultar_vehiculo->free();
                    unset($consultar_vehiculo);
                    return $tabla;
                }
            }
        }


        public function editarVisitante(){
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                # code...
            }else {/* Validacion de la no existencia de variables post y que vengan vacias a excepcion de los campos de vehiculos que no son obligatorios para el registro para eviar una alerta de error con los campos. */
                if (!isset($_POST['nombres_visitante'],
                 $_POST['tipo_doc_visitante'],
                 $_POST['correo_visitante'],
                 $_POST['apellidos_visitante'],
                 $_POST['num_documento_visitante'],
                 $_POST['telefono_visitante'],
                 $_POST['tipo_vehiculo_visitante'],
                 $_POST['placa_vehiculo_visitante'])
                 ||
                 $_POST['nombres_visitante'] == ""  ||
                 $_POST['tipo_doc_visitante'] == ""  ||
                 $_POST['correo_visitante'] == ""  ||
                 $_POST['apellidos_visitante'] == ""  ||
                 $_POST['num_documento_visitante'] == ""  ||
                 $_POST['telefono_visitante']== "") {
                    $mensaje=[
                        "titulo"=>"Error",
                        "mensaje"=>"Lo sentimos, a ocurrido un error con alguno de los datos, intentalo de nuevo mas tarde.",
                        "icono"=> "error",
                        "tipoMensaje"=>"normal"
                    ];
                    return json_encode($mensaje);
                    exit();
                }else {
                    


                    $nombre = $this->limpiarDatos($_POST['nombres_visitante']); 
                    
                    $tipo_doc = $this->limpiarDatos($_POST['tipo_doc_visitante']); 
                    
                    $correo = $this->limpiarDatos($_POST['correo_visitante']); 
                    $apellidos = $this->limpiarDatos($_POST['apellidos_visitante']); 
                    $telefono = $this->limpiarDatos($_POST['telefono_visitante']);
                    $num_documento = $this->limpiarDatos($_POST['num_documento_visitante']);
                    $tipo_vehiculo = $this->limpiarDatos($_POST['tipo_vehiculo_visitante']);
                    $placa_vehiculo = $this->limpiarDatos($_POST['placa_vehiculo_visitante']); 
                        

                    unset($_POST['nombres_visitante'],$_POST['tipo_doc_visitante'],$_POST['correo_visitante'],$_POST['apellidos_visitante'],$_POST['num_documento_visitante'], $_POST['telefono_visitante'], $_POST['tipo_vehiculo_visitante'],$_POST['placa_vehiculo_visitante']);

                    $campos_invalidos = [];
                    if ($this->verificarDatos('[A-Za-z ]{2,64}', $nombre)) {
                        array_push($campos_invalidos, 'NOMBRE(S)');
                    }else {
                        $nombre_vs = $nombre;
                    }
                    if ($this->verificarDatos('[A-Z]{2}', $tipo_doc)) {
                        array_push($campos_invalidos, 'TIPO DE DOCUMENTO');
                    }else {
                        $tipo_doc_vs = $tipo_doc;
                    }
                    if ($this->verificarDatos('[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}', $correo)) {
                        array_push($campos_invalidos, 'CORREO ELECTRONICO');
                    }else {
                        $correo_vs = $correo;
                    }
                    if ($this->verificarDatos('[A-Za-z ]{2,64}', $apellidos)) {
                        array_push($campos_invalidos, 'APELLIDO(S)');
                    }else {
                        $apellidos_vs = $apellidos;
                    }
                    if ($this->verificarDatos('[0-9]{6,15}',$num_documento)) {
                        array_push($campos_invalidos, 'NUMERO DE DOCUMENTO');
                        
                    }else {
                        $num_documento_vs = $num_documento; 
                    }
                    if ($this->verificarDatos('[0-9]{10}', $telefono)) {
                        array_push($campos_invalidos, 'TELEFONO');
                    }else {
                        $telefono_vs = $telefono;
                    }

                    unset($nombre, $tipo_doc, $correo, $apellidos, $num_documento, $telefono);


                    if (count($campos_invalidos) > 0) {
                        $invalidos = "";
                        foreach ($campos_invalidos as $campos) {
                            if ($invalidos == "") {
                                $invalidos = $campos;
                            }else {
                                $invalidos = $invalidos.", ".$campos;
                            }
                        }
                        $mensaje=[
                            "titulo"=>"Campos incompletos",
                            "mensaje"=>"Lo sentimos, los campos ".$invalidos." no cumplen con el formato solicitado.",
                            "icono"=> "error",
                            "tipoMensaje"=>"normal"
                        ];
                        echo json_encode($mensaje);
                        exit();
                    }else {
                        #code..
                    }
                }  
            }
        }


        public function ListarVisitanteController(){

            header('Content-Type: application/json'); 

            $columnas = [
                'tipo_documento',
                'num_identificacion',
                'nombres',
                'apellidos',
                'correo',
                'telefono',
                'estado',
                'fecha_hora_ultimo_ingreso',
                'permanencia'];

            $tabla = "visitantes";
            $id = 'tipo_documento';
            
            $tipo_listado = $this->limpiarDatos($_POST['tipoListado']);
            unset($_POST['tipoListado']);
            
            $filtro = '';
            if (isset($_POST['filtro']) && $_POST['filtro'] !== '') {
                $filtro = $this->limpiarDatos($_POST['filtro']);
            }

            /* Filtro Like */
            $sentenciaCondicionada = '';

            if ($filtro != '' ) {
                $sentenciaCondicionada = "WHERE (";
                $contadorColumas = count($columnas);
                for ($i=0; $i < $contadorColumas; $i++) { 
                    $sentenciaCondicionada .= $columnas[$i] . " LIKE '%".$filtro."%' OR ";
                }

                $sentenciaCondicionada = substr_replace($sentenciaCondicionada, "", -3);
                $sentenciaCondicionada .= ")";
            }
            /* Filtro Limit */
            $limit = 3;
            if (isset($_POST['registros']) && $_POST['registros'] !== '') {
                $limit = $this->limpiarDatos($_POST['registros']);
            }
            $pagina = 0;
            if (isset($_POST['pagina']) && $_POST['pagina'] !== '') {
                $pagina = $this->limpiarDatos($_POST['pagina']);
            }

            if (!$pagina) {
                $inicio = 0;
                $pagina = 1;
            }else {
                $inicio = ($pagina - 1) * $limit;
            }


            $sLimit = "LIMIT $inicio , $limit";

            $sentencia = "SELECT  SQL_CALC_FOUND_ROWS ". implode(', ', $columnas). " 
            FROM $tabla 
            $sentenciaCondicionada 
            $sLimit";
            $buscar_visitantes = $this->ejecutarConsulta($sentencia);
            $numero_registros = $buscar_visitantes->num_rows;

            
            /*  Consulta total registros*/

            $sentencia_filtro = "SELECT FOUND_ROWS()";
            $busqueda_filtro = $this->ejecutarConsulta($sentencia_filtro);
            $registros_filtro = $busqueda_filtro->fetch_array();
            $total_filtro = $registros_filtro[0];

            /*  Consulta total registros*/

            $sentencia_total = "SELECT count($id) FROM $tabla";
            $busqueda_total = $this->ejecutarConsulta($sentencia_total);
            $registros_total = $busqueda_total->fetch_array();
            $total_registros = $registros_total[0];




            $output = [];
            $output['total_registros'] = $total_registros;
            $output['total_filtro'] = $total_filtro;
            $output['data'] = '';
            $output['paginacion'] = '';
            if (!$buscar_visitantes){
					$output['data'] = $tipo_listado == 'tabla' 
                    ? '
                                    <table class="table">
                                        <thead class="head-table">
                                            <tr>
                                                <th>Tipo de Documento</th>
                                                <th>Número de Identificación</th>
                                                <th>Nombres</th>
                                                <th>Apellidos</th>
                                                <th>Correo</th>
                                                <th>Teléfono</th>
                                                <th>Fecha y Hora Último Ingreso</th>
                                                <th>Permanencia</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table" id="listado_visitantes">
                                        <tr><td colspan="9">Error al cargar los visitantes</td></tr>
                                        </tbody>
                                        </table>' 
                    : '
                    <div class="document-card">
                        <div class="card-header">
                            <div>
                                <p class="document-title">Error en el listado</p>
                                <p class="document-meta">Error al listar los reportes</p>
                            </div>
                            <span class="toggle-icon"><ion-icon name="chevron-down-outline"></ion-icon></span> 
                        </div>
                    </div>';
            } else{
                if ($buscar_visitantes->num_rows < 1) {
                    $output['data'] = $tipo_listado == 'tabla' 
                    ? '
                                    <table class="table">
                                        <thead class="head-table">
                                            <tr>
                                                <th>Tipo de Documento</th>
                                                <th>Número de Identificación</th>
                                                <th>Nombres</th>
                                                <th>Apellidos</th>
                                                <th>Correo</th>
                                                <th>Teléfono</th>
                                                <th>Fecha y Hora Último Ingreso</th>
                                                <th>Permanencia</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table" id="listado_visitantes">
                                        <tr><td colspan="9">No se encontraron Visitantes</td></tr>
                                        </tbody>
                                        </table>'
                    :'
                    <div class="document-card">
                        <div class="card-header">
                            <div>
                                <p class="document-meta">No se encontro Visitantes</p>
                            </div>
                        </div>
                    </div>';
                } else{
                    if ($tipo_listado == 'tabla') {
                        $output['data'] = '
                                    <table class="table">
                                        <thead class="head-table">
                                            <tr>
                                                <th>Tipo de Documento</th>
                                                <th>Número de Identificación</th>
                                                <th>Nombres</th>
                                                <th>Apellidos</th>
                                                <th>Correo</th>
                                                <th>Teléfono</th>
                                                <th>Fecha y Hora Último Ingreso</th>
                                                <th>Permanencia</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table" id="listado_visitantes">
                                    ';
    
                        while ($datos = $buscar_visitantes->fetch_object()) {
                            $output['data'].='
                                <tr >
                                    <td>'.$datos->tipo_documento.'</td>
                                    <td>'.$datos->num_identificacion.'</td>
                                    <td>'.$datos->nombres.'</td>
                                    <td>'.$datos->apellidos.'</td>
                                    <td>'.$datos->correo.'</td>
                                    <td>'.$datos->telefono.'</td>
                                    <td>'.$datos->fecha_hora_ultimo_ingreso.'</td>
                                    <td>'.$datos->permanencia.'</td>
                                    <td class="contenedor-colum-accion">
                                        <a href="editar-visitante/'.$datos->num_identificacion.'/" class="button is-info is-rounded is-small">
                                            Editar
                                        </a>
                                    </td>
                                </tr>
                            ';
                        }
                        $output['data'] .= '</tbody></table>';
                    }elseif ($tipo_listado == 'card') {
                        
                        while ($datos = $buscar_visitantes->fetch_object()) {
                            $output['data'].= '
								<div class="document-card" onclick="toggleCard(this)">
									<div class="card-header">
										<div>
											<p class="document-title">'.$datos->nombres.' '.$datos->apellidos.'</p>
											<p class="document-meta">'.$datos->tipo_documento.': '.$datos->num_identificacion. ' | ' .$datos->permanencia.'</p>
										</div>
										<span class="toggle-icon"><ion-icon name="chevron-down-outline"></ion-icon></span> 
									</div>
									<div class="card-details">
										<p><strong>Fecha y Hora: </strong>'.$datos->fecha_hora_ultimo_ingreso.'</p>
										<p><strong>Correo:</strong>'.$datos->correo.'</p>
										<p><strong>Telefono:</strong>'.$datos->telefono.'</p>
									</div>
                                    
                                    
                                    <div class="contenedor-acciones">
                                        
                                        <a class="btn-cards" href="editar-visitante/'.$datos->num_identificacion.'/" >
                                            <p>
                                                Editar
                                            </p>
                                        </a>
                                    </div>
								</div>';
                        }
                    }


                }
                if ($output['total_registros'] > 0) {
                    $total_paginas = ceil($output['total_registros'] / $limit);
                    $output['paginacion'] .= '<nav>';
                    $output['paginacion'] .= '<ul>';

                    for ($i=1; $i <= $total_paginas ; $i++) { 
                        $output['paginacion'] .= '<li>
                                                    <a href="#" onclick="getData('.$i.')">'.$i.'</a>
                                                </li>';
                    }
                    
                    $output['paginacion'] .= '</ul>';
                    $output['paginacion'] .= '</nav>';
                }
            } 
            return json_encode($output, JSON_UNESCAPED_UNICODE);
        }
    }