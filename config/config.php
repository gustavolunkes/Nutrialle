<?php
/**
 * Configurações gerais do sistema
 */

// URL base do projeto
define('BASE_URL', '/site-institucional-Nutrialle');

// Configurações do sistema
define('SITE_NAME', 'Nutrialle');
define('SITE_DESCRIPTION', 'Sistema de Gerenciamento de Sites');

// Caminhos
define('ROOT_PATH', dirname(__DIR__));
define('ADMIN_PATH', ROOT_PATH . '/admin');
define('UPLOADS_PATH', ROOT_PATH . '/assets/uploads');

// URLs
define('ADMIN_URL', BASE_URL . '/admin');
define('UPLOADS_URL', BASE_URL . '/assets/uploads');

// Timezone
date_default_timezone_set('America/Sao_Paulo');
?>