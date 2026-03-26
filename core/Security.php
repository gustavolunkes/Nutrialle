<?php
/**
 * Classe de Segurança
 * Funções de proteção e validação
 */

class Security {
    
    /**
     * Verifica se o IP excedeu o limite de tentativas de login
     */
    public static function checkLoginAttempts($email, $ip, $max_attempts = 5, $time_window = 15) {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("
            SELECT COUNT(*) as attempts 
            FROM login_attempts 
            WHERE (email = ? OR ip_address = ?) 
            AND attempted_at >= DATE_SUB(NOW(), INTERVAL ? MINUTE)
        ");
        $stmt->execute([$email, $ip, $time_window]);
        $result = $stmt->fetch();
        
        return $result['attempts'] < $max_attempts;
    }
    
    /**
     * Registra uma tentativa de login
     */
    public static function logLoginAttempt($email, $ip) {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("INSERT INTO login_attempts (email, ip_address) VALUES (?, ?)");
        $stmt->execute([$email, $ip]);
    }
    
    /**
     * Limpa tentativas antigas (mais de 24 horas)
     */
    public static function cleanOldLoginAttempts() {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $stmt->execute();
    }
    
    /**
     * Remove sessões inativas (mais de 24 horas sem atividade)
     */
    public static function cleanExpiredSessions() {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("DELETE FROM sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $stmt->execute();
    }
    
    /**
     * Atualiza a última atividade da sessão
     */
    public static function updateSessionActivity($session_id) {
        $db = Database::getInstance()->getConnection();
        
        // Usa 'id' ao invés de 'session_id'
        $stmt = $db->prepare("UPDATE sessions SET last_activity = NOW() WHERE id = ?");
        $stmt->execute([$session_id]);
    }
}
?>