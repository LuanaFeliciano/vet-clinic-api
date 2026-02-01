# 🐾 VetClinic+ API (Em Andamento)

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php) ![Docker](https://img.shields.io/badge/Docker-Sail-2496ED?style=for-the-badge&logo=docker)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-18-316192?style=for-the-badge&logo=postgresql)

> API robusta e escalável para gestão de Clínicas Veterinárias (SaaS).

## 🚀 Sobre o Projeto

O **VetClinic+** é uma solução SaaS (Software as a Service) desenvolvida para modernizar a gestão de clínicas veterinárias. O sistema gerencia desde o cadastro da clínica e seus veterinários até prontuários e agendamentos, com foco em segurança e performance.

### ✨ Principais Funcionalidades

* **Autenticação Segura:** Login via JWT (Laravel Sanctum) e verificação de e-mail.
* **Segurança Avançada:** Uso de **UUIDs** em vez de IDs sequenciais (anti-enumeração).
* **Controle de Acesso:** Sistema de Abilities/Roles (Admin, Veterinário, Recepcionista).
* **Emails Personalizados:** Templates transacionais com identidade visual da marca.
* **Arquitetura Limpa:** Uso de Services, FormRequests personalizados e Enums.

---

## 🛠️ Tech Stack

* **Framework:** Laravel 12
* **Linguagem:** PHP 8.3
* **Banco de Dados:** PostgreSQL
* **Ambiente:** Docker (via Laravel Sail)
* **Qualidade de Código:** PHPStan (Larastan) & Laravel Pint
* **Documentação:** Scribe

---

## ⚙️ Como Rodar Localmente

Pré-requisitos: Ter o **Docker** e o **Docker Compose** instalados.

1.  **Clone o repositório**
    ```bash
    git clone [https://github.com/LuanaFeliciano/vet-clinic-api.git](https://github.com/LuanaFeliciano/vet-clinic-api.git)
    cd api-vet
    ```

2.  **Instale as dependências**
    ```bash
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php83-composer:latest \
        composer install --ignore-platform-reqs
    ```

3.  **Configure o Ambiente**
    ```bash
    cp .env.example .env
    ```
    *Ajuste as credenciais do banco no .env se necessário (o padrão do Sail já funciona).*

4.  **Suba os Containers (Sail)**
    ```bash
    ./vendor/bin/sail up -d
    ```

5.  **Gere a Chave e Rode as Migrations**
    ```bash
    ./vendor/bin/sail artisan key:generate
    ./vendor/bin/sail artisan migrate
    ```

A API estará rodando em: `http://localhost:8000`

---

## 🧪 Comandos Úteis (Dev Tools)

Para manter a qualidade do código, utilizamos scripts personalizados no Composer:

* **Análise Estática (Caçar Bugs):**
    ```bash
    ./vendor/bin/sail composer analyse
    ```
* **Padronização de Código (Lint):**
    ```bash
    ./vendor/bin/sail composer lint
    ```
* **Gerar Documentação da API:**
    ```bash
    ./vendor/bin/sail composer docs
    ```

---

## 📧 Testando E-mails (Mailpit)

O projeto utiliza o **Mailpit** para interceptar e-mails em ambiente local.
Acesse o painel para ver os links de verificação e reset de senha:

👉 **http://localhost:8025**

---

## 📚 Documentação da API

A documentação interativa dos endpoints é gerada automaticamente pelo Scribe.
Após rodar o comando de docs, acesse:

👉 **http://localhost:8000/docs**

### Endpoints Principais

| Método | Rota | Descrição | Auth? |
| :--- | :--- | :--- | :---: |
| `POST` | `/api/register` | Cria uma nova clínica e usuário Admin | ❌ |
| `POST` | `/api/login` | Autentica e retorna Token (Sanctum) | ❌ |
| `GET` | `/api/user` | Retorna dados do usuário logado | ✅ |
| `GET` | `/api/email/verify/{id}/{hash}` | Confirma o e-mail do usuário | ❌ |

---

## 📝 Licença

Este projeto está sob a licença [MIT](https://opensource.org/licenses/MIT).

---

Feito com 🧡 por Luana