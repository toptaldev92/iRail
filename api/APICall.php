<?php
  /* Copyright (C) 2011 by iRail vzw/asbl
   * © 2015 by Open Knowledge Belgium vzw/asbl
   *
   * This class foresees in basic HTTP functionality. It will get all the GET vars and put it in a request.
   * This requestobject will be given as a parameter to the DataRoot object, which will fetch the data and will give us a printer to print the header and body of the HTTP response.
   *
   * @author Pieter Colpaert
   */

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

ini_set('include_path', '.:data');
include_once 'data/DataRoot.php';
include_once 'data/structs.php';
class APICall
{
    private $VERSION = 1.1;

    protected $request;
    protected $dataRoot;
    protected $log;
    /**
     * @param $functionname
     */
    public function __construct($resourcename)
    {
        //When the HTTP request didn't set a User Agent, set it to a blank
        if (! isset($_SERVER['HTTP_USER_AGENT'])) {
            $_SERVER['HTTP_USER_AGENT'] = '';
        }
        //Default timezone is Brussels
        date_default_timezone_set('Europe/Brussels');
        //This is the current resource that's handled. E.g., stations, connections, vehicleinfo or liveboard
        $this->resourcename = $resourcename;
        try {
            $this->log = new Logger('irapi');
            //Create a formatter for the logs
            $logFormatter = new LineFormatter("%datetime% | %message% | %context%\n", 'Y-m-d\TH:i:s');
            $streamHandler = new StreamHandler(__DIR__ . '/../storage/irapi.log', Logger::INFO);
            $streamHandler->setFormatter($logFormatter);
            $this->log->pushHandler($streamHandler);
            $requestname = ucfirst(strtolower($resourcename)).'Request';
            include_once "requests/$requestname.php";
            $this->request = new $requestname();
            $this->dataRoot = new DataRoot($resourcename, $this->VERSION, $this->request->getFormat());
        } catch (Exception $e) {
            $this->buildError($e);
        }
    }

    /**
     * @param $e
     */
    private function buildError($e)
    {
        $this->logError($e);
        //Build a nice output
        $format = '';
        if (isset($_GET['format'])) {
            $format = $_GET['format'];
        }
        if ($format == '') {
            $format = 'Xml';
        }
        $format = ucfirst(strtolower($format));
        if (isset($_GET['callback']) && $format == 'Json') {
            $format = 'Jsonp';
        }
        if (! file_exists("output/$format.php")) {
            $format = 'Xml';
        }
        include_once "output/$format.php";
        $printer = new $format(null);
        $printer->printError($e->getCode(), $e->getMessage());
        exit(0);
    }

    public function executeCall()
    {
        try {
            $this->dataRoot->fetchData($this->request, $this->request->getSystem());
            $this->dataRoot->printAll();
            $this->writeLog();
        } catch (Exception $e) {
            $this->buildError($e);
        }
    }

    /**
     * @param Exception $e
     */
    protected function logError(Exception $e)
    {
        if ($e->getCode() >= 500) {
            $this->log->addCritical($this->resourcename . ',,,' . '"' . $e->getMessage() . '"');
        } else {
            $this->log->addError($this->resourcename . ',,,' . '"' . $e->getMessage() . '"');
        }
    }

    /**
     * @param $from
     * @param $to
     * @param $err
     * @throws Exception
     */
     protected function writeLog($err)
     {
        $query = [];
        if ($this->resourcename === 'connections') {
            $query['departureStop'] = $this->request->getFrom();
            $query['arrivalStop'] = $this->request->getTo();
        } else if ($this->resourcename === 'liveboard') {
            $query['departureStop'] = $this->request->getStation();
        } else if ($this->resourcename === 'vehicle') {
            $query['vehicle'] = $this->request->getVehicleId();
        }
        
        $this->log->addInfo($this->resourcename, [
            'query' => $query,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'error' => $err
        ]);
        
    }
}
