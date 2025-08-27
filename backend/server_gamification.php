<?php
/**
 * Sistema CRUD Gamificado - API Backend
 * Versão melhorada com melhor organização e segurança
 */

// Configuração do banco de dados
class DatabaseConfig {
    const HOST = 'localhost';
    const DB_NAME = 'cursojs';
    const USERNAME = 'root';
    const PASSWORD = '';
    const CHARSET = 'utf8mb4';
    const PORT = 3306;

}

// Classe para gerenciar conexão com banco de dados
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DatabaseConfig::HOST . 
                   ";dbname=" . DatabaseConfig::DB_NAME . 
                   ";charset=" . DatabaseConfig::CHARSET;
            
            $this->pdo = new PDO($dsn, DatabaseConfig::USERNAME, DatabaseConfig::PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Falha na conexão com o banco de dados");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}

// Classe para gerenciar respostas da API
class ApiResponse {
    public static function success($data = null, $message = null) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $data,
            'message' => $message
        ]);
        exit;
    }

    public static function error($message, $code = 400) {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'error' => $message
        ]);
        exit;
    }
}

// Classe para gerenciar gamificação
class GamificationManager {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrarAcao($usuario_id, $tipo_acao, $pontos) {
        try {
            // Registrar ação
            $stmt = $this->pdo->prepare('INSERT INTO acoes (usuario_id, tipo_acao, pontos_ganhos) VALUES (?, ?, ?)');
            $stmt->execute([$usuario_id, $tipo_acao, $pontos]);
            
            // Atualizar pontos do usuário
            $stmt = $this->pdo->prepare('UPDATE usuarios SET pontos = pontos + ? WHERE id = ?');
            $stmt->execute([$pontos, $usuario_id]);
            
            // Verificar badges
            $this->verificarBadges($usuario_id);
            
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao registrar ação: " . $e->getMessage());
            return false;
        }
    }

    private function verificarBadges($usuario_id) {
        try {
            // Badge: Primeiro Item (criar primeiro item)
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM acoes WHERE usuario_id = ? AND tipo_acao = "criar_item"');
            $stmt->execute([$usuario_id]);
            $count = $stmt->fetchColumn();
            
            if ($count == 1) {
                $this->concederBadge($usuario_id, 1);
            }
            
            // Badge: Organizador (criar 10 itens)
            if ($count >= 10) {
                $this->concederBadge($usuario_id, 2);
            }
            
            // Badge: Mestre do Inventário (criar 50 itens)
            if ($count >= 50) {
                $this->concederBadge($usuario_id, 3);
            }
            
            // Badge: Editor (editar 5 itens)
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM acoes WHERE usuario_id = ? AND tipo_acao = "editar_item"');
            $stmt->execute([$usuario_id]);
            $editCount = $stmt->fetchColumn();
            
            if ($editCount >= 5) {
                $this->concederBadge($usuario_id, 4);
            }

            // Badge: Limpador (deletar 10 itens)
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM acoes WHERE usuario_id = ? AND tipo_acao = "deletar_item"');
            $stmt->execute([$usuario_id]);
            $deleteCount = $stmt->fetchColumn();
            
            if ($deleteCount >= 10) {
                $this->concederBadge($usuario_id, 5);
            }
        } catch (PDOException $e) {
            error_log("Erro ao verificar badges: " . $e->getMessage());
        }
    }

    private function concederBadge($usuario_id, $badge_id) {
        try {
            $stmt = $this->pdo->prepare('INSERT IGNORE INTO usuario_badges (usuario_id, badge_id) VALUES (?, ?)');
            $stmt->execute([$usuario_id, $badge_id]);
        } catch (PDOException $e) {
            error_log("Erro ao conceder badge: " . $e->getMessage());
        }
    }
}

// Classe para gerenciar usuários
class UsuarioController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listar($id = null) {
        try {
            if ($id) {
                $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
                $stmt->execute([$id]);
                $usuario = $stmt->fetch();
                
                if (!$usuario) {
                    ApiResponse::error("Usuário não encontrado", 404);
                }
                
                ApiResponse::success($usuario);
            } else {
                $stmt = $this->pdo->query('SELECT * FROM usuarios ORDER BY pontos DESC');
                $usuarios = $stmt->fetchAll();
                ApiResponse::success($usuarios);
            }
        } catch (PDOException $e) {
            error_log("Erro ao listar usuários: " . $e->getMessage());
            ApiResponse::error("Erro interno do servidor", 500);
        }
    }

    public function criar($data) {
        if (!isset($data['nome']) || empty(trim($data['nome']))) {
            ApiResponse::error("Nome é obrigatório");
        }

        try {
            $stmt = $this->pdo->prepare('INSERT INTO usuarios (nome) VALUES (?)');
            $stmt->execute([trim($data['nome'])]);
            
            ApiResponse::success(['id' => $this->pdo->lastInsertId()], "Usuário criado com sucesso");
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                ApiResponse::error("Nome de usuário já existe");
            }
            error_log("Erro ao criar usuário: " . $e->getMessage());
            ApiResponse::error("Erro interno do servidor", 500);
        }
    }
}

// Classe para gerenciar itens
class ItemController {
    private $pdo;
    private $gamification;

    public function __construct($pdo, $gamification) {
        $this->pdo = $pdo;
        $this->gamification = $gamification;
    }

    public function listar($id = null) {
        try {
            if ($id) {
                $stmt = $this->pdo->prepare('SELECT * FROM itens WHERE id = ?');
                $stmt->execute([$id]);
                $item = $stmt->fetch();
                
                if (!$item) {
                    ApiResponse::error("Item não encontrado", 404);
                }
                
                ApiResponse::success($item);
            } else {
                $stmt = $this->pdo->query('SELECT * FROM itens ORDER BY data_criacao DESC');
                $itens = $stmt->fetchAll();
                ApiResponse::success($itens);
            }
        } catch (PDOException $e) {
            error_log("Erro ao listar itens: " . $e->getMessage());
            ApiResponse::error("Erro interno do servidor", 500);
        }
    }

    public function criar($data) {
        $this->validarDadosItem($data);

        try {
            $stmt = $this->pdo->prepare('INSERT INTO itens (nome, tipo, quantidade) VALUES (?, ?, ?)');
            $stmt->execute([$data['nome'], $data['tipo'], $data['quantidade']]);
            
            // Registrar ação de gamificação
            if (isset($data['usuario_id']) && is_numeric($data['usuario_id'])) {
                $this->gamification->registrarAcao($data['usuario_id'], 'criar_item', 10);
            }
            
            ApiResponse::success(['id' => $this->pdo->lastInsertId()], "Item criado com sucesso");
        } catch (PDOException $e) {
            error_log("Erro ao criar item: " . $e->getMessage());
            ApiResponse::error("Erro interno do servidor", 500);
        }
    }

    public function atualizar($data) {
        $this->validarDadosItem($data);
        
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            ApiResponse::error("ID do item é obrigatório");
        }

        try {
            $stmt = $this->pdo->prepare('UPDATE itens SET nome=?, tipo=?, quantidade=? WHERE id=?');
            $result = $stmt->execute([$data['nome'], $data['tipo'], $data['quantidade'], $data['id']]);
            
            if ($stmt->rowCount() === 0) {
                ApiResponse::error("Item não encontrado", 404);
            }
            
            // Registrar ação de gamificação
            if (isset($data['usuario_id']) && is_numeric($data['usuario_id'])) {
                $this->gamification->registrarAcao($data['usuario_id'], 'editar_item', 5);
            }
            
            ApiResponse::success(null, "Item atualizado com sucesso");
        } catch (PDOException $e) {
            error_log("Erro ao atualizar item: " . $e->getMessage());
            ApiResponse::error("Erro interno do servidor", 500);
        }
    }

    public function deletar($data) {
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            ApiResponse::error("ID do item é obrigatório");
        }

        try {
            $stmt = $this->pdo->prepare('DELETE FROM itens WHERE id=?');
            $stmt->execute([$data['id']]);
            
            if ($stmt->rowCount() === 0) {
                ApiResponse::error("Item não encontrado", 404);
            }
            
            // Registrar ação de gamificação
            if (isset($data['usuario_id']) && is_numeric($data['usuario_id'])) {
                $this->gamification->registrarAcao($data['usuario_id'], 'deletar_item', 2);
            }
            
            ApiResponse::success(null, "Item deletado com sucesso");
        } catch (PDOException $e) {
            error_log("Erro ao deletar item: " . $e->getMessage());
            ApiResponse::error("Erro interno do servidor", 500);
        }
    }

    private function validarDadosItem($data) {
        if (!isset($data['nome']) || empty(trim($data['nome']))) {
            ApiResponse::error("Nome do item é obrigatório");
        }
        
        if (!isset($data['tipo']) || empty(trim($data['tipo']))) {
            ApiResponse::error("Tipo do item é obrigatório");
        }
        
        if (!isset($data['quantidade']) || !is_numeric($data['quantidade']) || $data['quantidade'] < 0) {
            ApiResponse::error("Quantidade deve ser um número válido");
        }
    }
}

// Classe para gerenciar badges
class BadgeController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listar($usuario_id = null) {
        try {
            if ($usuario_id) {
                $stmt = $this->pdo->prepare('
                    SELECT b.*, ub.data_conquista 
                    FROM badges b 
                    JOIN usuario_badges ub ON b.id = ub.badge_id 
                    WHERE ub.usuario_id = ?
                    ORDER BY ub.data_conquista DESC
                ');
                $stmt->execute([$usuario_id]);
            } else {
                $stmt = $this->pdo->query('SELECT * FROM badges ORDER BY id');
            }
            
            $badges = $stmt->fetchAll();
            ApiResponse::success($badges);
        } catch (PDOException $e) {
            error_log("Erro ao listar badges: " . $e->getMessage());
            ApiResponse::error("Erro interno do servidor", 500);
        }
    }
}

// Classe para gerenciar ranking
class RankingController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function obterRanking() {
        try {
            $stmt = $this->pdo->prepare('
                SELECT u.*, 
                       COUNT(ub.badge_id) as total_badges,
                       RANK() OVER (ORDER BY u.pontos DESC) as posicao
                FROM usuarios u 
                LEFT JOIN usuario_badges ub ON u.id = ub.usuario_id 
                GROUP BY u.id 
                ORDER BY u.pontos DESC
            ');
            $stmt->execute();
            $ranking = $stmt->fetchAll();
            
            ApiResponse::success($ranking);
        } catch (PDOException $e) {
            error_log("Erro ao obter ranking: " . $e->getMessage());
            ApiResponse::error("Erro interno do servidor", 500);
        }
    }
}

// Configuração de headers CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Responder a requisições OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Inicialização
try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    $gamification = new GamificationManager($pdo);
    
    $usuarioController = new UsuarioController($pdo);
    $itemController = new ItemController($pdo, $gamification);
    $badgeController = new BadgeController($pdo);
    $rankingController = new RankingController($pdo);
} catch (Exception $e) {
    ApiResponse::error("Erro de inicialização do servidor", 500);
}

// Roteamento
$endpoint = $_GET['endpoint'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true) ?? [];

switch ($endpoint) {
    case 'usuarios':
        switch ($method) {
            case 'GET':
                $usuarioController->listar($_GET['id'] ?? null);
                break;
            case 'POST':
                $usuarioController->criar($data);
                break;
            default:
                ApiResponse::error("Método não permitido", 405);
        }
        break;

    case 'itens':
        switch ($method) {
            case 'GET':
                $itemController->listar($_GET['id'] ?? null);
                break;
            case 'POST':
                $itemController->criar($data);
                break;
            case 'PUT':
                $itemController->atualizar($data);
                break;
            case 'DELETE':
                $itemController->deletar($data);
                break;
            default:
                ApiResponse::error("Método não permitido", 405);
        }
        break;

    case 'badges':
        if ($method === 'GET') {
            $badgeController->listar($_GET['usuario_id'] ?? null);
        } else {
            ApiResponse::error("Método não permitido", 405);
        }
        break;

    case 'ranking':
        if ($method === 'GET') {
            $rankingController->obterRanking();
        } else {
            ApiResponse::error("Método não permitido", 405);
        }
        break;

    default:
        ApiResponse::error("Endpoint não encontrado", 404);
}
?>
