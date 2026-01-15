<?php
/**
 * Sistema de Log Avançado
 * Similar ao Laravel logging system
 */

class Logger {
    // Níveis de log (similar a RFC 5424)
    const EMERGENCY = 'EMERGENCY';  // 0 - Sistema inutilizável
    const ALERT = 'ALERT';          // 1 - Ação imediata necessária
    const CRITICAL = 'CRITICAL';    // 2 - Condições críticas
    const ERROR = 'ERROR';          // 3 - Erros de runtime
    const WARNING = 'WARNING';      // 4 - Situações anormais mas não de erro
    const NOTICE = 'NOTICE';        // 5 - Eventos normais mas significativos
    const INFO = 'INFO';            // 6 - Eventos informativos
    const DEBUG = 'DEBUG';          // 7 - Informações de debug

    private $logDir;
    private $maxFileSize;
    private $environment;

    public function __construct() {
        $this->logDir = __DIR__ . '/../logs';
        $this->maxFileSize = 10 * 1024 * 1024; // 10MB
        $this->environment = env('APP_ENV', 'production');

        // Criar diretório se não existir
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }
    }

    /**
     * Log de erro crítico
     */
    public function error($message, array $context = []) {
        $this->log(self::ERROR, $message, $context);
    }

    /**
     * Log de aviso
     */
    public function warning($message, array $context = []) {
        $this->log(self::WARNING, $message, $context);
    }

    /**
     * Log de informação
     */
    public function info($message, array $context = []) {
        $this->log(self::INFO, $message, $context);
    }

    /**
     * Log de debug
     */
    public function debug($message, array $context = []) {
        $this->log(self::DEBUG, $message, $context);
    }

    /**
     * Log de exceção
     */
    public function exception(Exception $exception) {
        $context = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'code' => $exception->getCode()
        ];
        
        $this->log(self::ERROR, 'Exception: ' . $exception->getMessage(), $context);
    }

    /**
     * Log genérico
     */
    public function log($level, $message, array $context = []) {
        $logEntry = $this->formatLogEntry($level, $message, $context);
        $this->writeLog($level, $logEntry);
    }

    /**
     * Formata a entrada do log com contexto
     */
    private function formatLogEntry($level, $message, $context = []) {
        $timestamp = date('Y-m-d H:i:s.') . substr((string)microtime(), 2, 3);
        $requestId = $this->getRequestId();
        $userId = $this->getUserId();
        $ip = $this->getClientIp();
        $method = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
        $uri = $_SERVER['REQUEST_URI'] ?? '';

        $logLine = "[{$timestamp}] ";
        $logLine .= "[{$level}] ";
        $logLine .= "[ID: {$requestId}] ";
        $logLine .= "[User: {$userId}] ";
        $logLine .= "[IP: {$ip}] ";
        $logLine .= "[{$method} {$uri}] ";
        $logLine .= $message;

        // Adicionar contexto se houver
        if (!empty($context)) {
            $logLine .= " | Context: " . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return $logLine . PHP_EOL;
    }

    /**
     * Escreve o log em arquivo
     */
    private function writeLog($level, $logEntry) {
        $filename = $this->getLogFilename($level);
        $filepath = $this->logDir . '/' . $filename;

        // Fazer backup do arquivo se exceder o tamanho máximo
        if (file_exists($filepath) && filesize($filepath) > $this->maxFileSize) {
            $this->rotateLogFile($filepath);
        }

        // Escrever log
        file_put_contents($filepath, $logEntry, FILE_APPEND | LOCK_EX);

        // Também escrever no log genérico "application.log"
        $generalLog = $this->logDir . '/application.log';
        if ($generalLog !== $filepath) {
            file_put_contents($generalLog, $logEntry, FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * Rotaciona os arquivos de log quando excedem o tamanho
     */
    private function rotateLogFile($filepath) {
        $timestamp = date('Y-m-d_H-i-s');
        $basename = basename($filepath, '.log');
        $newFilepath = dirname($filepath) . '/' . $basename . '_' . $timestamp . '.log';
        
        if (file_exists($filepath)) {
            rename($filepath, $newFilepath);
        }
    }

    /**
     * Gera nome do arquivo de log baseado no nível
     */
    private function getLogFilename($level) {
        $date = date('Y-m-d');
        
        // Separar logs críticos dos informativos
        switch ($level) {
            case self::EMERGENCY:
            case self::ALERT:
            case self::CRITICAL:
            case self::ERROR:
                return "error-{$date}.log";
            
            case self::WARNING:
                return "warning-{$date}.log";
            
            case self::NOTICE:
            case self::INFO:
                return "info-{$date}.log";
            
            case self::DEBUG:
                return "debug-{$date}.log";
            
            default:
                return "application-{$date}.log";
        }
    }

    /**
     * Gera ID único para rastrear requests
     */
    private function getRequestId() {
        if (!isset($_SESSION['request_id'])) {
            $_SESSION['request_id'] = uniqid('REQ-', true);
        }
        return $_SESSION['request_id'];
    }

    /**
     * Obtém ID do usuário logado
     */
    private function getUserId() {
        return $_SESSION['user_id'] ?? 'guest';
    }

    /**
     * Obtém IP do cliente
     */
    private function getClientIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
        return trim($ip);
    }

    /**
     * Exibir logs de um arquivo específico
     */
    public function readLogs($level = self::ERROR, $lines = 100) {
        $date = date('Y-m-d');
        $filename = $this->logDir . '/' . $this->getLevelFilename($level) . '-' . $date . '.log';

        if (!file_exists($filename)) {
            return [];
        }

        $logs = array_reverse(array_filter(file($filename)));
        return array_slice($logs, 0, $lines);
    }

    /**
     * Helper para obter nome do arquivo pelo nível
     */
    private function getLevelFilename($level) {
        switch ($level) {
            case self::ERROR:
                return 'error';
            case self::WARNING:
                return 'warning';
            case self::INFO:
                return 'info';
            case self::DEBUG:
                return 'debug';
            default:
                return 'application';
        }
    }
}

// Singleton para facilitar acesso global
$_logger = null;

function logger() {
    global $_logger;
    if ($_logger === null) {
        $_logger = new Logger();
    }
    return $_logger;
}
?>
