<?php

declare(strict_types=1);

namespace App\Services;


/**
 * JobRunner - Executa serviços em background (simples estilo "dispatch").
 */
class JobRunner
{
    private static ?\PDO $pdoInstance = null;

    /**
     * Define a instância de PDO global que será usada pelos jobs.
     */
    public static function setPDO(\PDO $pdo): void
    {
        self::$pdoInstance = $pdo;
    }

    /**
     * Dispara um job em background.
     *
     * @param string $serviceClass Classe do serviço (com namespace).
     * @param string $method Método do serviço a ser executado.
     * @param array $params Parâmetros a serem passados ao método.
     */
    public static function dispatch(string $serviceClass, string $method, array $params = []): void
    {
        $rootPath = realpath(__DIR__ . '/../..');
        $php = PHP_BINARY ?: "php";

        $payload = json_encode([
            'service' => $serviceClass,
            'method' => $method,
            'params' => $params
        ], JSON_UNESCAPED_SLASHES);

        // Escapa apenas uma vez para shell
        $escapedPayload = escapeshellarg($payload);

        $phpCodeToExecute = sprintf(
            "require '%s/vendor/autoload.php'; \\App\\Services\\JobRunner::handle(\$argv[1]);",
            $rootPath
        );

        $escapedPhpCode = escapeshellarg($phpCodeToExecute);

        $cmd = sprintf(
            '%s -r %s %s > /dev/null 2>&1 &', // Joga fora a saída e executa em background
            // '%s -r %s %s &',// Joga para os logs do container (segura a execução)
            $php,
            $escapedPhpCode,
            $escapedPayload
        );

        file_put_contents('php://stderr', "[JobRunner Dispatch] Executing command: " . $cmd . PHP_EOL);

        exec($cmd);
    }

    /**
     * Método chamado pelo processo filho para executar o job.
     *
     * @param string|array $payload JSON ou array com service, method, params.
     */
    public static function handle($payload): void
    {
        // Adiciona um log inicial para saber que o handle foi chamado
        // 'php://stderr' escreve para o stream de erro, que é capturado por 'docker logs'
        // file_put_contents('php://stderr', "[JobRunner] Handle iniciado com payload: " . json_encode($payload) . PHP_EOL);
        file_put_contents('php://stderr', "[JobRunner] Handle iniciado com payload: " . $payload . PHP_EOL);

        echo $payload . "fim \n";


        if (is_string($payload)) {
            $payload = json_decode($payload, true);
        }

        if (!isset($payload['service'], $payload['method'])) {
            file_put_contents('php://stderr', "[JobRunner Handle Error] Invalid payload for JobRunner" . PHP_EOL);
            throw new \InvalidArgumentException("Payload inválido para JobRunner");
        }

        $serviceClass = $payload['service'];
        $method = $payload['method'];
        $params = $payload['params'] ?? [];

        try {
            // Instancia o serviço (injeção simples de PDO se necessário)
            $pdo = self::$pdoInstance ?? new \PDO("mysql:host=db;dbname=mini_erp;charset=utf8", "erp", "toor");
            $service = new $serviceClass($pdo);

            // Executa o método com parâmetros
            call_user_func_array([$service, $method], $params);

            file_put_contents('php://stderr', "[JobRunner] Job finalizado com sucesso para o classe: {$serviceClass}:{$method}(...)" . PHP_EOL);

        } catch (\Throwable $e) {
            // Capture e logue qualquer exceção que ocorra durante a execução do job
            file_put_contents('php://stderr', "[JobRunner Error] Exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . PHP_EOL);
            file_put_contents('php://stderr', "[JobRunner Error] Trace: " . $e->getTraceAsString() . PHP_EOL);
        }
    }

}
