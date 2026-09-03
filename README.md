# Controle Financeiro - Desafio Técnico

Projeto desenvolvido para o teste técnico de Laravel. É um sistema simples de controle de contas a pagar e contas a receber, com cadastro de pessoas/empresas, dashboard com totais e tela de relatórios.

## Como rodar o projeto

Eu configurei usando o Docker com Laravel Sail:

1. Copiar o `.env`:
```bash
cp .env.example .env
```

2. Instalar as dependências do Composer:
Se você ainda não tiver a pasta vendor baixada (primeira vez clonando), precisa rodar o composer antes. Se o seu PHP local for diferente do PHP 8.5 do projeto, use `--ignore-platform-reqs`:
```bash
composer install --ignore-platform-reqs
```
*(ou se não tiver composer/php na máquina, dá pra rodar direto pela imagem docker do sail):*
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php85-composer:latest \
    composer install --ignore-platform-reqs
```

3. Subir os containers:
```bash
./vendor/bin/sail up -d
```

4. Gerar a key e rodar as migrations com os dados de teste:
```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

5. Compilar os assets:
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

Depois é só acessar no navegador: `http://localhost`

*(Se preferir rodar sem docker, dá pra rodar direto com `php artisan serve` e configurando o mysql no .env normal).*

---

## Usuário de teste

O seeder já cria um usuário pronto pra testar e alguns clientes, fornecedores e lançamentos:

- **E-mail:** `admin@financeiro.local`
- **Senha:** `senha123`

---

## O que tem no sistema

- **Pessoas/Empresas:** cadastro de clientes e fornecedores. Coloquei uma validação de CPF e CNPJ pra não deixar passar documento errado.
- **Contas a Pagar / Contas a Receber:** listagem e cadastro com vencimento e valor. Tem botão pra dar baixa (informando a data do pagamento/recebimento) e pra cancelar o título.
- **Dashboard:** tela inicial mostrando os totalizadores (a receber, recebido, a pagar, pago, saldos previsto e realizado) e coloquei um gráfico simples de barras usando Chart.js mostrando o fluxo dos últimos meses.
- **Relatório:** filtros por período de vencimento, pessoa, tipo e status, com os totalizadores em cima e um botão pra exportar os dados em CSV (dá pra abrir no Excel).
- **Rotina de vencidos:** criei um comando `php artisan finance:check-overdue` que busca o que tá pendente com data de vencimento anterior a hoje e troca o status pra vencido. Deixei agendado no `routes/console.php`.

---

## Algumas decisões que tomei

- Usei uma tabela só (`financial_entries`) com uma coluna `type` (payable/receivable) em vez de criar duas tabelas separadas. Achei mais fácil e organizado pra puxar os totais do dashboard e fazer a tela de relatório sem precisar ficar fazendo union de tabelas.
- Validação dos formulários fiz em Form Requests separados pra não deixar os controllers cheios de regra.
- O front fiz com o Blade e Tailwind que já vem no Breeze mesmo, tentando deixar o mais limpo e direto possível.
