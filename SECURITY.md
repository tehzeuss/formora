# Segurança

Não publique arquivos `.env`, chaves de aplicação, bancos locais, uploads de usuários ou logs.

O Formora possui login com senha criptografada, validação no servidor, proteção CSRF, limites de tentativas e controle de propriedade. A identificação por CPF ou e-mail em formulários abertos é declarada pelo respondente; não há confirmação de titularidade.

Antes de disponibilizar uma instalação na internet, configure HTTPS, `APP_ENV=production`, `APP_DEBUG=false`, envio de e-mails quando aplicável e backups do banco e dos arquivos privados. O cadastro público está habilitado por padrão.

Não há confirmação de e-mail nem recuperação de senha por e-mail nesta versão. Administradores podem redefinir a senha de uma conta pela gestão de usuários. Não use esta aplicação para coletar informações sensíveis sem avaliar as necessidades da sua instalação.
