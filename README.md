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
└── README.md                     # Esta documentação
```

## Sistema de Pontuação

| Ação | Pontos |
|------|--------|
| Criar item | +10 pontos |
| Editar item | +5 pontos |
| Deletar item | +2 pontos |

## Instalação e Execução

### Pré-requisitos
- Node.js 18+ (com npm)
- PHP 8.1+
- MySQL 8.0+
- MAMP (ou Apache/Nginx e MySQL)

### 1. Instalar Node.js e npm
Se você ainda não tem o Node.js e o npm instalados, siga estes passos:

1.  **Baixe o instalador do Node.js:**
    *   Vá para o site oficial do Node.js: `https://nodejs.org/`
    *   Recomendo baixar a versão LTS (Long Term Support), que é a mais estável e recomendada para a maioria dos usuários.

2.  **Execute o instalador:**
    *   Abra o arquivo que você baixou e siga as instruções do instalador.
    *   Certifique-se de que a opção "Add to PATH" (Adicionar ao PATH) esteja selecionada durante a instalação. Isso garantirá que o `npm` e o `node` sejam reconhecidos no seu terminal.

3.  **Verifique a instalação:**
    *   Após a instalação, abra um **novo** terminal ou prompt de comando.
    *   Digite os seguintes comandos para verificar se o Node.js e o npm foram instalados corretamente:
        ```bash
        node -v
        npm -v
        ```
    *   Se você vir os números das versões, a instalação foi bem-sucedida.

### 2. Configuração do Backend (PHP + MySQL no MAMP)

1.  **Iniciar o MAMP:**
    *   Abra o aplicativo MAMP (ou MAMP PRO).
    *   Certifique-se de que os servidores Apache e MySQL estejam iniciados (os botões devem estar verdes).

2.  **Criar o Banco de Dados:**
    *   No painel do MAMP, clique em 'Open WebStart Page' ou navegue para `http://localhost/MAMP/` no seu navegador.
    *   Clique em 'phpMyAdmin' no menu superior.
    *   No phpMyAdmin, clique na aba 'Bancos de Dados' (Databases).
    *   No campo 'Criar novo banco de dados' (Create new database), digite `cursojs` e clique em 'Criar' (Create).

3.  **Importar o Esquema do Banco de Dados:**
    *   No phpMyAdmin, selecione o banco de dados `cursojs` que você acabou de criar na barra lateral esquerda.
    *   Clique na aba 'Importar' (Import).
    *   Clique em 'Escolher arquivo' (Choose file) e selecione o arquivo `banco_completo.sql` (fornecido com o projeto).
    *   Role para baixo e clique em 'Executar' (Go) para importar o esquema e os dados iniciais.

4.  **Configurar o Servidor PHP:**
    *   Localize o diretório `htdocs` do MAMP. Geralmente, ele está em `/Applications/MAMP/htdocs/` (macOS) ou `C:\MAMP\htdocs\` (Windows).
    *   Copie o arquivo `server_gamification.php` (fornecido com o projeto) para dentro deste diretório `htdocs`.

### 3. Configuração e Execução do Frontend (React)

1.  **Descompacte o projeto:**
    *   Descompacte o arquivo `sistema_crud_gamificado_essencial.zip` que você recebeu. Ele conterá a pasta `crud-gamificado`.

2.  **Navegar para o diretório do frontend:**
    *   Abra o terminal ou prompt de comando.
    *   Use o comando `cd` para ir até a pasta `crud-gamificado`.
    *   Exemplo: `cd C:\caminho\para\sistema_crud_gamificado_essencial\crud-gamificado`

3.  **Instalar as dependências do projeto:**
    *   Dentro do diretório `crud-gamificado`, execute o seguinte comando:
        ```bash
        npm install --legacy-peer-deps
        ```
    *   A flag `--legacy-peer-deps` é usada para resolver possíveis conflitos de dependências. Aguarde até que o processo seja concluído.

4.  **Executar em modo desenvolvimento:**
    *   Após a instalação das dependências, inicie o servidor de desenvolvimento React:
        ```bash
        npm run dev -- --host
        ```

5.  **Acessar a aplicação:**
    *   O terminal indicará um endereço local, geralmente `http://localhost:5173/` ou `http://localhost:5174/`.
    *   Abra este endereço no seu navegador para acessar o frontend da aplicação.

**Observação:** Se você tiver problemas de CORS (Cross-Origin Resource Sharing) ao acessar o backend, verifique se o `server_gamification.php` está configurado corretamente com os cabeçalhos `Access-Control-Allow-Origin: *`.

## Endpoints da API (Backend PHP)

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
