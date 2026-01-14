<?php
/**
 * Carregador de variáveis de ambiente (.env)
 * Função simples para carregar arquivos .env sem dependências externas
 */

function loadEnv($path = __DIR__ . '/../.env') {
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Ignorar comentários
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Separar chave e valor
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Remover aspas do valor se existirem
            $value = trim($value, '"\'');
            
            // Definir variável de ambiente
            if (!array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
                putenv("$name=$value");
            }
        }
    }
    
    return true;
}

/**
 * Obter variável de ambiente com valor padrão
 */
function env($key, $default = null) {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}
?>
