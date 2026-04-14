<?php

namespace App\DbDumpers;

use Spatie\DbDumper\Databases\PostgreSql;
use Symfony\Component\Process\Process;

class PostgreSqlWithPassword extends PostgreSql
{
    public function dumpToFile(string $dumpFile): void
    {
        $this->guardAgainstIncompleteCredentials();

        $tempFileHandle = tmpfile();
        $this->setTempFileHandle($tempFileHandle);

        $process = $this->getProcess($dumpFile);
        $process->run();

        // Log completo del error
        \Log::error('pg_dump STDERR completo: ' . $process->getErrorOutput());
        \Log::error('pg_dump STDOUT completo: ' . $process->getOutput());
        \Log::error('pg_dump exit code: ' . $process->getExitCode());

        $this->checkIfDumpWasSuccessFul($process, $dumpFile);
    }

    public function getProcess(string $dumpFile): Process
    {
        // Construir comando con -f en lugar de redirección >
        $dumpBinaryPath = 'C:\\Program Files\\PostgreSQL\\16\\bin\\pg_dump.exe';
        
        $command = sprintf(
            '"%s" -U "%s" -h %s -p %s -d "%s" -f "%s"',
            $dumpBinaryPath,
            $this->userName,
            $this->host,
            $this->port,
            $this->dbName,
            $dumpFile
        );
        
        fwrite($this->getTempFileHandle(), $this->getContentsOfCredentialsFile());
        $temporaryCredentialsFile = stream_get_meta_data($this->getTempFileHandle())['uri'];
        $envVars = $this->getEnvironmentVariablesForDumpCommand($temporaryCredentialsFile);
        $envVars['PGPASSWORD'] = $this->password;
        
        \Log::info('Comando final: ' . $command);
        
        return Process::fromShellCommandline($command, null, $envVars, null, $this->timeout);
    }
}