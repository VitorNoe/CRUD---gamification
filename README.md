# Sistema CRUD Gamificado

## Descrição do Projeto

Este é um sistema CRUD completo com gamificação, desenvolvido com frontend React e backend PHP. O sistema implementa um tema visual moderno inspirado no estilo Frutiger Aero, utilizando cores azul, roxo e preto, com animações suaves e efeitos visuais únicos.

## Funcionalidades Implementadas

### ✅ CRUD Completo
- **Criar**: Adicionar novos itens ao sistema (+10 pontos)
- **Ler**: Visualizar lista de todos os itens cadastrados
- **Editar**: Modificar itens existentes (+5 pontos)
- **Deletar**: Remover itens do sistema (+2 pontos)

### ✅ Sistema de Gamificação
- **Pontos**: Sistema de pontuação baseado nas ações do usuário
- **Badges**: Conquistas automáticas baseadas em critérios específicos
- **Ranking**: Classificação de usuários por pontos e badges
- **Perfil**: Estatísticas detalhadas do usuário

### ✅ Badges Disponíveis
1. **Primeiro Passo** - Criar o primeiro item
2. **Organizador** - Criar 10 itens
3. **Mestre do Inventário** - Criar 50 itens
4. **Editor Experiente** - Editar 5 itens
5. **Limpador** - Deletar 10 itens

### ✅ Interface Frutiger Aero
- Design moderno com elementos glass morphism
- Gradientes suaves em azul, roxo e preto
- Animações fluidas com Framer Motion
- Efeitos de hover e transições suaves
- Layout responsivo para desktop e mobile

## Tecnologias Utilizadas

### Frontend
- **React 18** - Framework JavaScript
- **Tailwind CSS** - Framework de estilos
- **Shadcn/UI** - Componentes de interface
- **Framer Motion** - Animações
- **Lucide React** - Ícones
- **Vite** - Build tool

### Backend
- **PHP 8.1** - Linguagem do servidor
- **MySQL 8.0** - Banco de dados
- **Apache 2.4** - Servidor web
- **PDO** - Conexão com banco de dados

## Estrutura do Projeto

```
projeto/
├── frontend/
│   ├── crud-gamificado/
│   │   ├── src/
│   │   │   ├── components/ui/     # Componentes Shadcn/UI
│   │   │   ├── App.jsx           # Componente principal
│   │   │   ├── App.css           # Estilos Frutiger Aero
│   │   │   └── main.jsx          # Ponto de entrada
│   │   ├── index.html            # HTML principal
│   │   └── package.json          # Dependências
├── backend/
│   ├── server_gamification.php   # API REST
│   └── banco_completo.sql        # Schema do banco
└── README_PROJETO.md             # Esta documentação
```

## Sistema de Pontuação

| Ação | Pontos |
|------|--------|
| Criar item | +10 pontos |
| Editar item | +5 pontos |
| Deletar item | +2 pontos |

## Instalação e Execução

### Pré-requisitos
- Node.js 18+
- PHP 8.1+
- MySQL 8.0+
- Apache 2.4+

### Backend (PHP + MySQL)

1. **Instalar dependências do sistema:**
```bash
sudo apt update
sudo apt install -y apache2 mysql-server php php-mysql php-json
```

2. **Configurar MySQL:**
```bash
sudo mysql -e "CREATE DATABASE IF NOT EXISTS cursojs;"
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

3. **Importar schema do banco:**
```bash
sudo mysql cursojs < banco_completo.sql
```

4. **Configurar Apache:**
```bash
sudo cp server_gamification.php /var/www/html/
sudo chown www-data:www-data /var/www/html/server_gamification.php
sudo systemctl start apache2
sudo systemctl start mysql
```

### Frontend (React)

1. **Navegar para o diretório do frontend:**
```bash
cd crud-gamificado
```

2. **Instalar dependências:**
```bash
npm install
```

3. **Executar em modo desenvolvimento:**
```bash
npm run dev -- --host
```

4. **Acessar a aplicação:**
```
http://localhost:5173
```

## Endpoints da API

### Usuários
- `GET /server_gamification.php?endpoint=usuarios` - Listar usuários
- `GET /server_gamification.php?endpoint=usuarios&id=1` - Buscar usuário
- `POST /server_gamification.php?endpoint=usuarios` - Criar usuário

### Itens
- `GET /server_gamification.php?endpoint=itens` - Listar itens
- `POST /server_gamification.php?endpoint=itens` - Criar item
- `PUT /server_gamification.php?endpoint=itens` - Editar item
- `DELETE /server_gamification.php?endpoint=itens` - Deletar item

### Badges
- `GET /server_gamification.php?endpoint=badges` - Listar todas as badges
- `GET /server_gamification.php?endpoint=badges&usuario_id=1` - Badges do usuário

### Ranking
- `GET /server_gamification.php?endpoint=ranking` - Ranking de usuários

## Características do Design Frutiger Aero

### Paleta de Cores
- **Azul Primário**: #4169e1 (Royal Blue)
- **Roxo Secundário**: #9370db (Medium Purple)
- **Azul Claro**: #00bfff (Deep Sky Blue)
- **Preto**: #1a1a1a
- **Background**: #f0f8ff (Alice Blue)

### Efeitos Visuais
- **Glass Morphism**: Elementos com transparência e blur
- **Gradientes**: Transições suaves entre cores
- **Animações**: Movimentos fluidos e naturais
- **Hover Effects**: Interações responsivas
- **Floating Elements**: Animação de flutuação sutil

## Funcionalidades Testadas

✅ Criação de itens com atribuição de pontos
✅ Sistema de badges automático
✅ Ranking dinâmico de usuários
✅ Interface responsiva
✅ Animações e transições
✅ Integração frontend-backend
✅ CORS configurado
✅ Tratamento de erros

## Próximas Melhorias Sugeridas

- [ ] Sistema de níveis baseado em pontos
- [ ] Mais tipos de badges temáticos
- [ ] Notificações push para conquistas
- [ ] Histórico de ações do usuário
- [ ] Sistema de amigos e competições
- [ ] Dashboard administrativo
- [ ] Exportação de dados
- [ ] Modo escuro/claro
