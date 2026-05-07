# FrotaWeb - Sistema de Gestão de Frota

Este é um sistema web completo para gestão de frota, desenvolvido com PHP, MySQL e Tailwind CSS.

## 🚀 Funcionalidades

- **Dashboard**: Visão geral da frota, custos totais e alertas críticos.
- **Gestão de Veículos**: CRUD completo com filtros avançados e busca.
- **Controle de Abastecimento**: Registro de litros, custos e cálculo automático de KM/L.
- **Relatórios de Custos**: Detalhamento de despesas fixas e variáveis por veículo.
- **Importação/Exportação**: Suporte para CSV (compatível com Excel).
- **Segurança**: Sistema de login e controle de acesso para administradores.

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 8.x (PDO para segurança)
- **Banco de Dados**: MySQL
- **Frontend**: HTML5, JavaScript (ES6+), Tailwind CSS (via CDN)
- **Gráficos**: Chart.js
- **Ícones**: Font Awesome 6

## 📋 Pré-requisitos

1. Servidor Web (Apache/Nginx) com suporte a PHP.
2. Servidor MySQL.
3. Recomendado usar ferramentas como **XAMPP**, **WAMP** ou **Laragon**.

## 🔧 Instalação

1. Clone ou extraia o projeto no diretório do seu servidor (ex: `htdocs/gestao-frota`).
2. Crie um banco de dados chamado `gestao_frota`.
3. Importe o arquivo SQL localizado em `sql/schema.sql` para o seu banco de dados.
4. Ajuste as configurações de conexão em `config/db.php` se necessário (usuário/senha do banco).
5. Acesse no navegador: `http://localhost/gestao-frota/public/`

## 🔐 Acesso Padrão

- **Usuário**: `admin`
- **Senha**: `admin123`

## 📂 Estrutura de Pastas

```text
/
├── config/         # Configurações do banco de dados
├── includes/       # Componentes PHP (Header, Sidebar, Auth)
├── public/         # Arquivos acessíveis via web (Páginas principais)
│   ├── assets/     # CSS, JS, Imagens
│   └── ...
├── sql/            # Script de criação do banco de dados
└── README.md
```

## 📝 Notas de Versão
- **v1.0.0**: Lançamento inicial com todos os requisitos atendidos.
