<?php

namespace AppBundle\Servicios;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;


/**
 * Class EscribeLog
 * @package AppBundle\Servicios
 */
class EscribeLog {

    private $logger;
    private $mensaje;
    private $repo;
    private $filename;

	/**
	 * @param $ficheroLog
	 * @return bool
	 */
    public function escribeLog($ficheroLog) {

        $ficheroLog = 'logs/'.$ficheroLog;
        
        $this->repo = new RotatingFileHandler($ficheroLog, 30,Logger::INFO);
        $this->filename = $this->repo->getUrl();
        $log = new Logger($this->logger);
        $log->pushHandler($this->repo);
        $log->info($this->mensaje);

        return true;
    }

	/**
	 * @return mixed
	 */
    public function getLogger() {
        return $this->logger;
    }

	/**
	 * @return mixed
	 */
    public function getRepo() {
        return $this->repo;
    }

    public function getFilename() {
        return $this->filename;
    }

    public function getMensaje() {
        return $this->mensaje;
    }

    public function setLogger($logger) {
        $this->logger = $logger;
        return $this;
    }

    public function setMensaje($mensaje) {
        $this->mensaje = $mensaje;
        return $this;
    }

}
