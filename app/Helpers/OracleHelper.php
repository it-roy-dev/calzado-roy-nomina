<?php

namespace App\Helpers;

class OracleHelper
{
    /**
     * Conexión Oracle Principal (192.168.0.170)
     */
    public static function connect()
    {
        $tns = "(DESCRIPTION =
                    (ADDRESS_LIST =
                      (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.0.170)(PORT = 1521))
                    )
                    (CONNECT_DATA =
                      (SERVICE_NAME = RPROODS)
                    )
                  )";
        
        $username = "REPORTUSER";
        $password = "REPORT";

        $connection = @oci_connect($username, $password, $tns, 'AL32UTF8');
        
        if (!$connection) {
            $error = oci_error();
            throw new \Exception("Error conectando a Oracle: " . $error['message']);
        }

        return $connection;
    }

    /**
     * Conexión Oracle Rpro (192.168.0.251)
     */
    public static function connectRpro()
    {
        $tns = "(DESCRIPTION =
                    (ADDRESS_LIST =
                      (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.0.251)(PORT = 1521))
                    )
                    (CONNECT_DATA =
                      (SERVICE_NAME = RPROODS)
                    )
                  )";
        
        $username = "REPORTUSER";
        $password = "REPORT";

        $connection = @oci_connect($username, $password, $tns, 'AL32UTF8');
        
        if (!$connection) {
            $error = oci_error();
            throw new \Exception("Error conectando a Oracle Rpro: " . $error['message']);
        }

        return $connection;
    }

    /**
     * Ejecutar query y devolver resultados como array
     */
    public static function query($sql, $connection = null)
    {
        if (!$connection) {
            $connection = self::connect();
            $closeConnection = true;
        } else {
            $closeConnection = false;
        }

        $statement = oci_parse($connection, $sql);
        
        if (!$statement) {
            $error = oci_error($connection);
            throw new \Exception("Error preparando query: " . $error['message']);
        }

        $result = oci_execute($statement);
        
        if (!$result) {
            $error = oci_error($statement);
            throw new \Exception("Error ejecutando query: " . $error['message']);
        }

        $rows = [];
        while ($row = oci_fetch_assoc($statement)) {
            $rows[] = $row;
        }

        oci_free_statement($statement);
        
        if ($closeConnection) {
            oci_close($connection);
        }

        return $rows;
    }

    /**
     * Cerrar conexión
     */
    public static function close($connection)
    {
        if ($connection) {
            oci_close($connection);
        }
    }
}