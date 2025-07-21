![Capa](/images/capa.png)

# **Mini ERP**
Este projeto é um **Sistema de Gestão de Recursos Empresariais (ERP) simplificado**, focado em otimizar e centralizar processos essenciais de **gestão de produtos, estoque e vendas** para um ambiente de e-commerce ou varejo. Desenvolvido com PHP no padrão MVC, ele oferece uma base sólida para o controle de seu catálogo de itens e o fluxo de pedidos.

## **🚀 Funcionalidades Principais**

* **Gestão de Produtos:**  
  * Cadastro completo de produtos com nome, preço e quantidade inicial em estoque.  
  * **Controle de Estoque por SKU (Unidade de Manutenção de Estoque):** Permite associar produtos a "Grupos de Variações" (utilizados como SKUs), possibilitando o controle granular de estoque e preço para cada combinação específica de produto \+ SKU (ex: "Tênis Esportivo \- Tamanho 38 \- Cor Preto").  
  * Adição de novas entradas de estoque (SKUs) para produtos já existentes, sem a necessidade de recriar o produto base.  
  * Edição e exclusão de produtos e suas respectivas entradas de estoque (SKUs).  
  ![Tela de Gerenciamento de Produtos](/images/screen-produtos.png)

* **Gestão de Variações e SKUs:**  
  * Cadastro de variações atômicas (ex: "Preto", "P", "38").  
  * Criação de Grupos de Variações (SKUs) que combinam variações atômicas (ex: "Cor", "Tamanho").  
  * Associação e gerenciamento de variações a grupos.  
  * Edição e exclusão de variações e grupos de variações.  
  ![Tela de Gerenciamento de Variações SKU](/images/screen-variacoes.png)
* **Gestão de Pedidos:**  
  * Listagem detalhada de todos os pedidos, incluindo total, status, endereço e data de criação. 
  ![Tela de Gerenciamento de Pedidos](/images/screen-pedidos.png)
  * **Atualização de Status via Webhook:** Um endpoint dedicado permite que sistemas externos atualizem o status de um pedido (ex: de "pendente" para "pago").  
```
curl -X POST 'http://localhost:8080/index.php?page=webhook' \
  -H "Content-Type: application/json" \
  -d '{"id":1,"status":"cancelado"}'

curl -X POST 'http://localhost:8080/index.php?page=webhook' \
  -H "Content-Type: application/json" \
  -d '{"id":2,"status":"pago"}'
```

* **Carrinho de Compras Inteligente:**  
  * Adição e remoção de produtos (SKUs) do carrinho.  
  * Cálculo automático de subtotal e total.  
  * Validação de estoque em tempo real para os itens no carrinho.  
  * Aplicação de cupons de desconto.  
  * Integração com ViaCEP para preenchimento automático de endereço, otimizando o processo de checkout.  
  ![Tela de Gerenciamento de Carinho](/images/screen-carrinho.png)
  * **Disparo de E-mails Assíncrono:** A classe `App\\Services\\JobRunner` é utilizada para processar tarefas em segundo plano, como o envio de e-mails de confirmação de pedido. Isso garante que o usuário não precise esperar o e-mail ser enviado para continuar navegando ou finalizando a compra, proporcionando uma experiência mais fluida.

## **💡 Por Que Este Projeto se Destaca?**

* **Arquitetura (MVC):** O projeto segue o padrão Model-View-Controller, garantindo código limpo, modular, fácil de manter, testar e expandir.  
* **Gestão de Estoque Precisa:** O controle de estoque por SKU permite uma visibilidade granular do inventário, crucial para evitar rupturas e otimizar a logística.  
* **Experiência do Usuário (UX) Moderna:** Com Bootstrap para design responsivo e AJAX (fetch API) para interações dinâmicas, a interface é fluida e intuitiva, minimizando recarregamentos de página. Modais para edições e exclusões aprimoram a usabilidade e segurança.  
* **Integridade e Segurança dos Dados:** A utilização de PDO com prepared statements protege contra injeção SQL, e as restrições de chave estrangeira e UNIQUE no DDL asseguram a consistência dos dados.  
* **Processos Otimizados:** A integração com `ViaCEP` e o processamento assíncrono de e-mails (via `JobRunner`) demonstram um foco na eficiência operacional e na satisfação do cliente.  
* **Pronto para Crescer:** A estrutura bem definida facilita a adição de novas funcionalidades e a integração com outros sistemas.

## **📂 Estrutura do Projeto**

A organização do projeto segue uma estrutura lógica e modular, facilitando a navegação e o desenvolvimento:
```
├── app/  
│   ├── Controllers/        \# Lógica de negócio e manipulação de requisições HTTP.  
│   │   ├── CupomController.php  
│   │   ├── EstoqueController.php  
│   │   ├── PedidoController.php  
│   │   ├── ProdutoController.php  
│   │   ├── VariacaoController.php  
│   │   └── WebhookController.php  
│   ├── Models/             \# Interação com o banco de dados e regras de negócio dos dados.  
│   │   ├── Cupom.php  
│   │   ├── Estoque.php  
│   │   ├── PedidoItem.php  
│   │   ├── Pedido.php  
│   │   ├── Produto.php  
│   │   └── Variacao.php  
│   ├── Services/           \# Classes para serviços específicos (ex: envio de e-mail, execução de jobs).  
│   │   ├── EmailService.php  
│   │   └── JobRunner.php  
│   └── Views/              \# Templates HTML para a interface do usuário.  
│       ├── carrinho.php  
│       ├── pedidos.php  
│       ├── produtos.php  
│       ├── templates/  
│       │   └── layout.php  \# Layout HTML principal para outras views.  
│       └── variacoes.php  
├── composer.json           \# Definições de dependências do Composer.  
├── composer.lock           \# Bloqueio de versões das dependências.  
├── config/                 \# Arquivos de configuração do sistema.  
│   ├── bootstrap.php  
│   ├── db.php              \# Configuração de conexão com o banco de dados.  
│   ├── env.php             \# Variáveis de ambiente.  
│   └── helpers.php         \# Funções auxiliares (Ex.: renderização de Views).  
├── docker/                 \# Arquivos para configuração do ambiente Docker (desenvolvimento).  
│   ├── docker-compose1.yml  
│   ├── docker-compose.yml  
│   ├── Dockerfile  
│   ├── setup.sql           \# Script SQL para criação do banco de dados e dados iniciais.  
│   └── xdebug.ini  
├── Makefile                \# Comandos de automação para Docker e outras tarefas.  
├── public/                 \# Diretório público, ponto de entrada da aplicação.  
│   ├── css/                \# Arquivos CSS (Bootstrap e customizado).  
│   │   ├── bootstrap.min.css  
│   │   └── custom.css  
│   ├── index.php           \# Ponto de entrada principal da aplicação (roteador).  
│   └── js/                 \# Arquivos JavaScript (Bootstrap, jQuery e customizado).  
│       ├── bootstrap.bundle.min.js  
│       ├── carrinho.js  
│       ├── jquery.min.js  
│       ├── produtos.js  
│       └── variacoes.js  
├── README.md               \# Este arquivo.  
└── test-email.php          \# Script para testar o envio de e-mails.
```
## Tecnologias utilizadas:

### Back end
- PHP
- Docker
- MySql

### Front end
- HTML / CSS / JS / 
- Bootstrap


## **🚀 Como Começar**

Para configurar e rodar o projeto localmente, siga os passos abaixo:

### **Pré-requisitos**

* [**Docker**](https://www.docker.com/get-started) (inclui Docker Compose)  
* [**Composer**](https://getcomposer.org/download/) (para gerenciar dependências PHP) (opcional)
* **Make** (para executar os comandos através do comando `make`)  

### **Passos para Instalação**

1. **Clone o Repositório:**  
    ```bash
    git clone https://github.com/EvelynGitHub/mini-erp.git  
    cd mini-erp
    ```
2. **Instalar Dependências PHP:**  
   * Configure as variáveis de ambiente criando um arquivo `.env` cópia de `.env.example`. 
   * Caso tenha dificuldade olhe o arquivo `docker-compose.yml`
   * O host é "db" e senha padrão é "toor"
   * Se não configurar uma senha de app do google ou credenciais corretas de servidor de email, você não receberá o email ao finalizar o checkout.

3. **Configurar o Ambiente Docker:**  
   * Certifique-se de que o Docker esteja em execução.  
   * Construa as imagens e inicie os serviços Docker:  
    ```bash
    # Pasta docker
    docker compose up \--build \-d

    # Pasta raiz
    # executar o projeto
    make build
    make up
    make composer-install

    # executar o testes
    make test

    # parar o projeto
    make down 
    ```
     Isso irá criar e iniciar os contêineres para o servidor web e o banco de dados MySQL (sempre verifique em que pasta está para cada comano).

4. **Configurar o Banco de Dados:**  
   * O script docker/setup.sql contém o DDL e os dados iniciais. Ele é executado automaticamente na primeira vez que o contêiner MySQL é iniciado.  
   * Se precisar recriar o banco de dados com dados iniciais, você pode executar o script manualmente:  
    ```bash
    docker compose -f docker/docker-compose.yml down -v
    make build
    make up
    ```

5. **Acessar a Aplicação:**  
   * A aplicação estará disponível em http://localhost:8080 no seu navegador.
   * A o banco estará disponível em http://localhost:8081 no seu navegador.

## **🎁 Cupons de Desconto (Exemplos)**

Sinta-se à vontade para usar e testar os seguintes cupons de desconto no checkout:

* **PROMO5**: 5% de desconto (valor mínimo de R$ 50.00)  
* **FRETEGRATIS**: 100% de desconto (valor mínimo de R$ 200.00) (não use esse)
* **DESCONTO10**: 10% de desconto (valor mínimo de R$ 100.00)

*(Você pode adicionar mais cupons e suas regras no seu banco de dados na tabela cupons.)*

## **📧 Teste de Envio de E-mail**

Para testar o envio de e-mails (simulado via JobRunner), você pode acessar o script test-email.php diretamente:
```bash

# Envia o email
make test-email

# Mostra os logs do container para ver o que acontece
make logs
```
Este script simulará o disparo de um e-mail, que será processado em segundo plano, demonstrando a funcionalidade assíncrona. Para ver os logs a linha 52 de `JobRunner` precisar ser comentada e a linha 53 ativada (mas isso tor o processo síncrono, portanto cuidado). 

## **🤝 Contribuição**

Contribuições são bem-vindas\! Se você tiver sugestões, melhorias ou encontrar bugs, por favor, abra uma issue ou envie um pull request.

## **📄 Licença**

Este projeto está licenciado sob a Licença MIT.