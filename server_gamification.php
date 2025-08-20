<?php
// Configuração do banco
$host = 'localhost';
$db = 'cursojs';
$user = 'root';
$pass = '';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Falha na conexão']);
    exit;
}

$endpoint = $_GET['endpoint'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Função para registrar ação e dar pontos
function registrarAcao($pdo, $usuario_id, $tipo_acao, $pontos) {
    $stmt = $pdo->prepare('INSERT INTO acoes (usuario_id, tipo_acao, pontos_ganhos) VALUES (?, ?, ?)');
    $stmt->execute([$usuario_id, $tipo_acao, $pontos]);
    
    $stmt = $pdo->prepare('UPDATE usuarios SET pontos = pontos + ? WHERE id = ?');
    $stmt->execute([$pontos, $usuario_id]);
    
    verificarBadges($pdo, $usuario_id);
}

// Função para verificar e conceder badges
function verificarBadges($pdo, $usuario_id) {
    // Badge: Primeiro Item (criar primeiro item)
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM acoes WHERE usuario_id = ? AND tipo_acao = "criar_item"');
    $stmt->execute([$usuario_id]);
    $count = $stmt->fetchColumn();
    
    if ($count == 1) {
        $stmt = $pdo->prepare('INSERT IGNORE INTO usuario_badges (usuario_id, badge_id) VALUES (?, 1)');
        $stmt->execute([$usuario_id]);
    }
    
    // Badge: Organizador (criar 10 itens)
    if ($count >= 10) {
        $stmt = $pdo->prepare('INSERT IGNORE INTO usuario_badges (usuario_id, badge_id) VALUES (?, 2)');
        $stmt->execute([$usuario_id]);
    }
    
    // Badge: Mestre do Inventário (criar 50 itens)
    if ($count >= 50) {
        $stmt = $pdo->prepare('INSERT IGNORE INTO usuario_badges (usuario_id, badge_id) VALUES (?, 3)');
        $stmt->execute([$usuario_id]);
    }
    
    // Badge: Editor (editar 5 itens)
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM acoes WHERE usuario_id = ? AND tipo_acao = "editar_item"');
    $stmt->execute([$usuario_id]);
    $editCount = $stmt->fetchColumn();
    
    if ($editCount >= 5) {
        $stmt = $pdo->prepare('INSERT IGNORE INTO usuario_badges (usuario_id, badge_id) VALUES (?, 4)');
        $stmt->execute([$usuario_id]);
    }
}

// Endpoint para usuários
if ($endpoint === 'usuarios') {
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
            $stmt->execute([$_GET['id']]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($res ?: []);
        } else {
            $res = $pdo->query('SELECT * FROM usuarios ORDER BY pontos DESC')->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($res);
        }
        exit;
    }
    
    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare('INSERT INTO usuarios (nome) VALUES (?)');
        $stmt->execute([$data['nome']]);
        echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId()]);
        exit;
    }
}

// Endpoint para itens (modificado para incluir gamificação)
if ($endpoint === 'itens') {
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare('SELECT * FROM itens WHERE id = ?');
            $stmt->execute([$_GET['id']]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($res ?: []);
        } else {
            $res = $pdo->query('SELECT * FROM itens')->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($res);
        }
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($method === 'POST') {
        $stmt = $pdo->prepare('INSERT INTO itens (nome, tipo, quantidade) VALUES (?, ?, ?)');
        $stmt->execute([$data['nome'], $data['tipo'], $data['quantidade']]);
        
        // Registrar ação e dar pontos
        if (isset($data['usuario_id'])) {
            registrarAcao($pdo, $data['usuario_id'], 'criar_item', 10);
        }
        
        echo json_encode(['ok' => true]);
        exit;
    }
    
    if ($method === 'PUT') {
        $stmt = $pdo->prepare('UPDATE itens SET nome=?, tipo=?, quantidade=? WHERE id=?');
        $stmt->execute([$data['nome'], $data['tipo'], $data['quantidade'], $data['id']]);
        
        // Registrar ação e dar pontos
        if (isset($data['usuario_id'])) {
            registrarAcao($pdo, $data['usuario_id'], 'editar_item', 5);
        }
        
        echo json_encode(['ok' => true]);
        exit;
    }
    
    if ($method === 'DELETE') {
        $stmt = $pdo->prepare('DELETE FROM itens WHERE id=?');
        $stmt->execute([$data['id']]);
        
        // Registrar ação e dar pontos
        if (isset($data['usuario_id'])) {
            registrarAcao($pdo, $data['usuario_id'], 'deletar_item', 2);
        }
        
        echo json_encode(['ok' => true]);
        exit;
    }
}

// Endpoint para badges
if ($endpoint === 'badges') {
    if ($method === 'GET') {
        if (isset($_GET['usuario_id'])) {
            $stmt = $pdo->prepare('
                SELECT b.*, ub.data_conquista 
                FROM badges b 
                JOIN usuario_badges ub ON b.id = ub.badge_id 
                WHERE ub.usuario_id = ?
            ');
            $stmt->execute([$_GET['usuario_id']]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($res);
        } else {
            $res = $pdo->query('SELECT * FROM badges')->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($res);
        }
        exit;
    }
}

// Endpoint para ranking
if ($endpoint === 'ranking') {
    if ($method === 'GET') {
        $stmt = $pdo->prepare('
            SELECT u.*, 
                   COUNT(ub.badge_id) as total_badges,
                   RANK() OVER (ORDER BY u.pontos DESC) as posicao
            FROM usuarios u 
            LEFT JOIN usuario_badges ub ON u.id = ub.usuario_id 
            GROUP BY u.id 
            ORDER BY u.pontos DESC
        ');
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($res);
        exit;
    }
}

echo json_encode(['erro' => 'Endpoint inválido']);
?>

