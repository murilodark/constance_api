# Constance API – Mini ERP de Pedidos

Este projeto é um **mini sistema ERP de pedidos para fornecedores**, desenvolvido como teste técnico utilizando **Laravel 12+**, **Docker**, **Redis**, **Jobs**, **API REST** e **tarefas agendadas**.

O sistema foi pensado para ser **simples, organizado e escalável**, seguindo boas práticas tradicionais de backend moderno.

---

## 🚀 Tecnologias Utilizadas

- PHP 8.3 (PHP-FPM)
- Laravel 12+
- MySQL 8
- Redis
- Docker & Docker Compose
- Nginx
- Mailpit / MailHog (ambiente de testes)
- Vue.js (frontend – fase posterior)

---

## 📦 Arquitetura Geral

- Backend API em Laravel
- Autenticação com usuários do tipo **admin** e **vendedor**
- Comunicação via API REST
- Cache de produtos utilizando Redis
- Processamento assíncrono com Jobs
- Agendamentos com Scheduler (cron)
- Ambiente totalmente dockerizado

---

## 📁 Estrutura do Projeto

/
├── api_laravel/ # Aplicação Laravel
├── docker/
│ ├── nginx/ # Configuração do Nginx
│ └── php/ # Configurações do PHP
├── docker-compose.yml
├── Dockerfile
└── README.md

