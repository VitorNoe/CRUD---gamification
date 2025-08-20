-- Banco de dados completo com gamificação

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL UNIQUE,
  pontos INT DEFAULT 0,
  data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de badges
CREATE TABLE IF NOT EXISTS badges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL UNIQUE,
  descricao TEXT,
  criterio VARCHAR(255) NOT NULL,
  icone VARCHAR(100) DEFAULT 'fa-trophy'
);

-- Tabela de relacionamento usuário-badges
CREATE TABLE IF NOT EXISTS usuario_badges (
  usuario_id INT,
  badge_id INT,
  data_conquista DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (usuario_id, badge_id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE
);

-- Tabela de ações (log de atividades)
CREATE TABLE IF NOT EXISTS acoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT,
  tipo_acao VARCHAR(100) NOT NULL,
  pontos_ganhos INT NOT NULL,
  data_acao DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de itens (original)
CREATE TABLE IF NOT EXISTS itens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  tipo VARCHAR(50) NOT NULL,
  quantidade INT NOT NULL,
  data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Inserir badges padrão
INSERT INTO badges (nome, descricao, criterio, icone) VALUES
('Primeiro Passo', 'Criou seu primeiro item!', 'criar_primeiro_item', 'fa-star'),
('Organizador', 'Criou 10 itens no sistema', 'criar_10_itens', 'fa-list'),
('Mestre do Inventário', 'Criou 50 itens no sistema', 'criar_50_itens', 'fa-crown'),
('Editor Experiente', 'Editou 5 itens diferentes', 'editar_5_itens', 'fa-edit'),
('Limpador', 'Deletou 10 itens', 'deletar_10_itens', 'fa-trash');

-- Usuário padrão para testes
INSERT INTO usuarios (nome) VALUES ('Usuário Teste');

