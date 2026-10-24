# Formora

**Crie formulários, compartilhe ideias e transforme respostas em informação.**

Plataforma de formulários desenvolvida com Laravel, Blade e JavaScript. Possui editor dinâmico, diferentes modos de participação, gráficos, exportação de respostas e gestão de usuários.

Projeto de portfólio de [Zeus Ramalho](https://github.com/tehzeuss). A instalação é independente e utiliza apenas as contas e os dados criados no seu ambiente.

## Funcionalidades

- Login e cadastro com e-mail e senha. Novas contas recebem o perfil Usuário.
- Editor de perguntas com formatação de texto e imagens no título e nas perguntas.
- Resposta curta, parágrafo, múltipla escolha, caixas de seleção, lista suspensa, escala linear, classificação, grades, data e horário.
- Publicação com link compartilhável, encerramento manual e prazo opcional no horário de Brasília.
- Duplicação como rascunho, com imagens independentes e sem copiar respostas.
- Exclusão com confirmação explícita.
- Resultados nas abas Resumo, Pergunta e Individual, com gráficos e preservação das versões antigas das perguntas.
- Exportação CSV com acentos e proteção contra fórmulas em campos de texto.
- Administração de usuários e transferência de propriedade com histórico.

### Modos de participação

| Modo | Acesso | Limite |
| --- | --- | --- |
| Fechado | Conta ativa com login | Um envio por conta em cada formulário |
| Aberto com limite | Sem login; solicita CPF ou e-mail | Um envio por identificador em cada formulário |
| Aberto livre | Sem login | Múltiplos envios, sem associação à conta |

O identificador em um formulário aberto não comprova identidade. Variações de pontuação no CPF, maiúsculas e espaços no e-mail são normalizadas. Os limites também são verificados no banco para impedir duplicação por envios simultâneos.

### Permissões

O usuário gerencia seus formulários e consulta suas respostas. O administrador gerencia contas, visualiza perguntas dos demais formulários e transfere a propriedade, mas não pode consultar respostas de formulários que não possui. A transferência dá ao novo proprietário o gerenciamento e o acesso às respostas.

## Tecnologias

- PHP 8.2+ e Laravel 12
- Blade, CSS e JavaScript modular, com build Vite
- SQLite por padrão; PostgreSQL também suportado
- Armazenamento privado de imagens

## Executar localmente

Requisitos: PHP 8.2 ou superior, Composer, Node.js 22.12+ e npm. Habilite as extensões PHP `pdo_sqlite`, `fileinfo`, `mbstring`, `dom`, `xml`, `openssl` e `curl`. Para PostgreSQL, utilize `pdo_pgsql`.

```bash
git clone https://github.com/tehzeuss/formora.git
cd formora/app
composer install
```

Copie o modelo de configuração:

```bash
# Linux ou macOS
cp .env.example .env
```

```powershell
# Windows PowerShell
Copy-Item .env.example .env
```

Prepare o banco e compile a interface:

```bash
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate
npm ci
npm run build
php artisan serve
```

Abra **http://localhost:8000**. Crie sua conta pela tela de cadastro. Não existe senha padrão.

Para criar uma conta administrativa, execute em outro terminal, dentro de `app/`:

```bash
php artisan formora:admin
```

O comando solicita nome, e-mail, senha e confirmação. A senha não é exibida nem salva em arquivos de configuração.

Durante o desenvolvimento, execute `npm run dev` em um segundo terminal para atualizar a interface automaticamente.

### Dados de demonstração

Depois de criar a primeira conta, é possível gerar uma pesquisa com 16 respostas inteiramente fictícias:

```bash
php artisan db:seed
```

O formulário é criado para o primeiro usuário ativo. A execução não duplica a mesma pesquisa e só é permitida em ambiente local. As respostas aparecem como demonstração nos comentários. Nenhuma conta ou senha de demonstração é criada.

### Utilizar PostgreSQL

Crie um banco exclusivo para sua instalação e configure no `.env` local:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=formora
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha_local
```

Execute `php artisan migrate` depois da configuração. O arquivo `.env` não deve ser enviado ao Git.

## Organização

```text
app/
├── app/Http/Controllers/  # Fluxos das páginas
├── app/Http/Requests/     # Validação e autorização
├── app/Models/           # Dados e regras de negócio
├── app/Services/         # Exportação CSV
├── database/migrations/  # Estrutura do banco
├── database/seeders/     # Demonstração opcional
├── resources/views/     # Blades e componentes
├── resources/css/       # Estrutura e estilos por página
├── resources/js/        # Editor e interações
└── routes/              # Rotas web e comando de administrador
```

## Limites desta versão

Não há recuperação de senha por e-mail ou confirmação de titularidade de e-mail. Administradores podem redefinir senhas pela gestão de usuários. CPF e e-mail informados em pesquisas abertas não são autenticados. Os dados de demonstração não representam pessoas reais.

Veja [SECURITY.md](SECURITY.md) antes de disponibilizar uma instalação na internet. Além do banco, faça backup de `app/storage/app/private`, onde ficam as imagens enviadas.
