Constance API – Mini ERP de Pedidos
Este projeto é um mini sistema ERP de pedidos, desenvolvido com foco em performance e organização. Utiliza Laravel 12+, Vue.js 3, Docker, Redis e processamento de Jobs em fila para importações assíncronas.
O sistema permite que vendedores realizem pedidos para diversos fornecedores, enquanto administradores gerenciam o fluxo global de vendas, faturamento e status.
📂 Estrutura do Repositório
/api_laravel: Backend API em Laravel 12+.
/frontend: Interface SPA em Vue.js 3 (Vite + Tailwind CSS).
docker-compose.yml: Configuração da infraestrutura Docker na raiz do projeto.
🛠️ Como Instalar e Rodar o Projeto
Siga a ordem exata dos comandos abaixo para configurar o ambiente:

1. Clonar o Repositório
bash
git clone https://github.com/murilodark/constance_api.git
cd constance_api
Use o código com cuidado.

2. Subir a Infraestrutura (Docker)
bash

---- veja se a o diretório docker/mysql existe, ----
------se não crie o diretório docker/mysql ---


docker compose up -d
Use o código com cuidado.

3. Configurar o Backend (Laravel)
O arquivo .env já está incluso no repositório para facilitar o teste técnico.
bash
# Acessar o container app
docker exec -u root -it constance_api-app-1 bash

# Navegar para a pasta do Laravel e configurar
cd api_laravel
export COMPOSER_PROCESS_TIMEOUT=2000
composer install
php artisan key:generate
php artisan jwt:secret
php artisan migrate:fresh --seed
Use o código com cuidado.

# Teste da api
No diretório Documentação na raiz, existe um arquivo api_completa_insomnia5.yaml
esse arquivo pode ser carregado o insomnia e possui todas as rotas da api

4. Configurar o Frontend (Vue.js 3)
Em um novo terminal (fora do container), navegue até a pasta do frontend. Certifique-se de estar usando Node.js v20.19+ ou v22.12+.
bash
# Saia da pasta api_laravel se estiver nela e entre no frontend
cd frontend
npm install
npm run dev
Use o código com cuidado.

📦 Processamento de Importação (Filas)
O sistema utiliza o Redis para gerenciar o upload massivo de produtos. Para processar os arquivos CSV enviados, você deve manter o Worker do Laravel rodando dentro do container:
bash
# Dentro do container, na pasta api_laravel
php artisan queue:work
Use o código com cuidado.

Obs: Se este comando for interrompido, as importações de produtos ficarão travadas com status "Pendente".
🌐 Endereços de Acesso (2025)
Serviço	URL	Descrição
Frontend	http://localhost:5173	Painel ERP (Vite)
Backend API	http://localhost:8000	Documentação/Endpoints
Mailpit	http://localhost:8025	Verificação de e-mails enviados
🔑 Credenciais de Teste
O comando --seed cria automaticamente os seguintes acessos:
Perfil	E-mail	Senha
Administrador	admin@sistema.com	password
Vendedor	vendedor@constance.com	password
📊 Principais Funcionalidades
Dashboard Inteligente: Métricas financeiras e volumétricas dos últimos 7 dias.
Módulo Admin: Visualização global de pedidos e troca de status (Pendente, Concluído, Cancelado).
Importação via Job: Upload de arquivos CSV (referencia,nome,cor,preco) processados em background.
Task Scheduling: Relatórios diários automáticos enviados por e-mail para os usuários.
