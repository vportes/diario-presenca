# Diario de Presenca NPI

Sistema para gestão de presenças para o NPI construído em Laravel 12 e Tailwind CSS

## Requisitos

* PHP 8.2+
* Composer 2.x
* Node.js 18+ e npm
* Extensões PHP: mbstring, openssl, pdo, pdo_sqlite, tokenizer, xml, ctype, json, fileinfo

## Instalação

1. Clonar o repositório:

```bash
git clone https://github.com/vportes/diario-presenca.git
cd diario-presenca
```

2. Instalar dependências PHP:

```bash
composer install
```

3. Instalar dependências JavaScript:

```bash
npm install
```

4. Copiar variáveis de ambiente e gerar chave:

```bash
cp .env.example .env
php artisan key:generate
```

5. Configurar banco de dados e rodar
```bash
mkdir -p database
touch database/database.sqlite
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run dev
npm run build
php artisan serve
```

Acesse: [http://127.0.0.1:8000](http://127.0.0.1:8000)
