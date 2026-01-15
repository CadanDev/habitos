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
        // Ignorar linhas vazias e comentários
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }

        // Separar chave e valor no primeiro '='
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Processar valor com suporte a aspas duplas e simples
            if ((strpos($value, '"') === 0 && strrpos($value, '"') > 0) ||
                (strpos($value, "'") === 0 && strrpos($value, "'") > 0)) {
                // Remover aspas do início e fim
                $value = substr($value, 1, -1);
            }
            
            // Definir variável de ambiente
            if (!array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
                // Usar putenv apenas se o valor não tiver caracteres especiais problemáticos
                if (strpos($value, '"') === false && strpos($value, "'") === false) {
                    putenv("$name=$value");
                }
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
