<?php
namespace Kevin\Pruebas\Test;

use Kevin\Pruebas\VisitanteController;




use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class visitanteTest extends TestCase {
    private $dbMock;

    protected function setUp(): void {
        $this->dbMock = $this->createMock(\PDO::class);
    }
    

    /* Funcion Caso Prueba Unitaria Intento de registro con una peticion diferente a Post */
    public function testRegistrarVehiculoControlerPeticionIncorrecta() {

        $claseVehiculo = new VisitanteController($this->dbMock);

        $datosVehiculo = [
            'REQUEST_METHOD' => 'GET',
            'nombres_visitante' => '',
            'tipo_doc_visitante' => '',
            'correo_visitante' => '',
            'apellidos_visitante' => '',
            'num_documento_visitante' => '',
            'telefono_visitante' => '',
            'tipo_vehiculo_visitante' => '',
            'placa_vehiculo_visitante' => ''
        ];

        $resultado = $claseVehiculo->registrarVisitanteControler($datosVehiculo);

        $this->assertEquals(
            '{"titulo":"Peticion incorrecta", "mensaje":"Lo sentimos, la accion que intentas realizar no es correcta"}',
            $resultado
        );
    }
    

    /* Funcion Caso Prueba Unitaria Intento de registro con datos vacios y faltantes */
    public function testRegistrarVehiculoControlerDatosVaciosFaltantes() {

        $claseVehiculo = new VisitanteController($this->dbMock);

        $datosVehiculo = [
            'REQUEST_METHOD' => 'POST',
            'nombres_visitante' => '',
            'tipo_doc_visitante' => '',
            'correo_visitante' => '',
            'apellidos_visitante' => '',
            'num_documento_visitante' => '',
            'telefono_visitante' => '',
            'tipo_vehiculo_visitante' => '',
            'placa_vehiculo_visitante' => ''
        ];

        $resultado = $claseVehiculo->registrarVisitanteControler($datosVehiculo);

        $this->assertEquals(
            '{"titulo":"Error","mensaje":"Lo sentimos, a ocurrido un error con alguno de los datos, intentalo de nuevo mas tarde."}',
            $resultado
        );
    }
    
    
    /* Funcion Caso Prueba Unitaria Intento de registro con datos invalidos*/
    public function testRegistrarVehiculoControlerDatosInvalidos() {

        $claseVehiculo = new VisitanteController($this->dbMock);

        $datosVehiculo = [
            'REQUEST_METHOD' => 'POST',
            'nombres_visitante' => '121212123',
            'tipo_doc_visitante' => 'Dc',
            'correo_visitante' => '123hsldf',
            'apellidos_visitante' => '12we23',
            'num_documento_visitante' => 'HOLAMUDNO',
            'telefono_visitante' => 'hlsdlfjsldkjflsdlf',
            'tipo_vehiculo_visitante' => 'hgd',
            'placa_vehiculo_visitante' => 'sldfsdsdf'
        ];

        $resultado = $claseVehiculo->registrarVisitanteControler($datosVehiculo);

        $this->assertEquals(
            '{"titulo":"Campos incompletos","mensaje":"Lo sentimos, los campos no cumplen con el formato solicitado."}',
            $resultado
        );
    }  

    
    /* Funcion Caso Prueba Unitaria Intento de registro con tipo de vehiculo sin placa vehiculo */
    public function testRegistrarVehiculoControlerDatosConPlacasVehiculos() {

        $claseVehiculo = new VisitanteController($this->dbMock);

        $datosVehiculo = [
            'REQUEST_METHOD' => 'POST',
            'nombres_visitante' => 'Dilan Adrian',
            'tipo_doc_visitante' => 'CC',
            'correo_visitante' => 'dilanadrianzapataortiz@gmail.com',
            'apellidos_visitante' => 'Zapata Ortiz',
            'num_documento_visitante' => '1112038485',
            'telefono_visitante' => '3169000133',
            'placa_vehiculo_visitante' => 'HTTPS2'
        ];

        $resultado = $claseVehiculo->registrarVisitanteControler($datosVehiculo);

        $this->assertEquals(
            '{"titulo":"Campo incompleto","mensaje":"Lo sentimos, el campo de PLACA DE VEHICULO esta incompleto."}',
            $resultado
        );
    } 


    /* Funcion Caso Prueba Unitaria dando datos correctos */
    public function testVehiculosVisitante01() {

        $claseVehiculo = new VisitanteController($this->dbMock);


        $resultado = $claseVehiculo->vehiculosVisitante('1112038485');

        $this->assertEquals(
            '{"titulo":"Campo incompleto","mensaje":"Lo sentimos, el campo de PLACA DE VEHICULO esta incompleto."}',
            $resultado
        );
    }  
}
