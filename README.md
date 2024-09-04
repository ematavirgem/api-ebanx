# API Simples em PHP Nativo

Este projeto é uma API simples desenvolvida em PHP puro. Abaixo você encontrará instruções para configurar o ambiente, instalar dependências, e rodar os testes. Ela implementa endpoints para criar, consultar saldo, depositar, sacar e transferir valores entre contas, sem persistência durável.

## Requisitos

- PHP 8.1 ou superior
- Composer (gerenciador de pacotes PHP)

## Instalação

1. **Clone o Repositório**

   Primeiro, clone o repositório para o seu ambiente local:

   ```bash
   git clone https://github.com/ematavirgem/api-ebanx
   cd api-ebanx
    ```
    
2. **Instalar Dependências**

   Agora vamos instalar as dependências:

   ```bash
   composer install
    ```

3. **Instruções para Rodar a API**

    1. Execute php -S localhost:8000 no diretório do projeto.
    2. Use uma ferramenta como Postman para interagir com a API.    
    
    
## Endpoints

**Tipos de Operação:**

- deposit
- withdraw
- transfer

1. **Redefinir Estado Antes de Consumir**

    - Objetivo: Antes de consumir a API, o estado do sistema deve ser limpo para garantir resultados consistentes. Isso pode incluir a criação e exclusão de contas fictícias.
    - POST /reset
    - Código de Status Esperado: 200
    - Resposta Esperada: OK


2. **Obter Saldo para Conta Inexistente**

    - Objetivo: Verificar que a API retorna um erro quando tentamos obter o saldo de uma conta que não existe.
    - GET /balance?account_id=1234
    - Código de Status Esperado: 404
    - Resposta Esperada: 0


3. **Criar uma Conta com Saldo Inicial**

    - Objetivo: Criar uma nova conta com um saldo inicial.
    - Método: POST /event
    - Payload:
        ```bash
        {
            "type": "deposit",
            "destination": "1234",
            "amount": 100
        }
        ```
    - Código de Status Esperado: 201
    - Resposta Esperada: {"destination": {"id":"1234", "balance":100}}


4. **Depositar em Conta Existente**

    - Objetivo: Testar o depósito de um valor em uma conta existente.
    - Método: POST /event
    - Payload:
        ```bash
        {
            "type": "deposit",
            "destination": "1234",
            "amount": 50
        }
        ```
    - Código de Status Esperado: 201
    - Resposta Esperada: 
        ```bash
        {
            "destination": {
                "id":"1234", 
                "balance":150
            }
        }
        ```


5. **Obter Saldo para Conta Existente**

    - Objetivo: Verificar o saldo atual de uma conta existente.
    - Método: GET /balance?account_id=1234
    - Código de Status Esperado: 201
    - Resposta Esperada: 150


6. **Saque de uma Conta Inexistente**

    - Objetivo: Verificar que a API retorna um erro ao tentar sacar de uma conta que não existe.
    - Método: POST /event
    - Payload:
        ```bash
        {
            "type": "withdraw",
            "destination": "1234",
            "amount": 50
        }
        ```
    - Código de Status Esperado: 404
    - Resposta Esperada: 0


7. **Sacar de Conta Existente**

    - Objetivo: Saque de um valor de uma conta existente.
    - Método: POST /event
    - Payload:
        ```bash
        {
            "type": "withdraw",
            "origin": "1234",
            "amount": 50
        }
        ```
    - Código de Status Esperado: 201
    - Resposta Esperada: 
        ```bash
        {
            "origin": {
                "id":"1234",
                "balance":100
            }
        }
        ```


8. **Transferir de Conta Existente**

    - Objetivo: Transferência de um valor de uma conta existente para outra.
    - Método: POST /event
    - Payload:
        ```bash
        {
            "type": "transfer",
            "account_id": "1",
            "destination_id": "2",
            "amount": 25
        }
        ```
    - Código de Status Esperado: 201
    - Resposta Esperada: 
        ```bash
        {
            "origin": {
                "id":"1234", 
                "balance":75
            }, 
            "destination": {
                "id":"3234", 
                "balance":25
            }
        }
        ```


9. **Transferência de Conta Inexistente**

    - Objetivo: Verificar que a API retorna um erro ao tentar transferir de uma conta que não existe.
    - Método: POST /event
    - Payload:
        ```bash
        {
            "type": "transfer",
            "origin": "1234",
            "destination": "2234",
            "amount": 25
        }
        ```
    - Código de Status Esperado: 404
    - Resposta Esperada: 0


### Instruções para Rodar os Testes

Os testes unitários estão localizados no diretório tests/. Para garantir que a API funcione conforme esperado, siga as regras e execute o teste descrito abaixo:


1.  Certifique-se de que o PHPUnit está instalado.
2. Execute vendor/bin/phpunit no diretório do projeto para rodar os testes.


### Conclusão

Esse setup inicial fornece uma implementação funcional e testada da API solicitada. Basta rodar o servidor PHP e usar ferramentas como o Postman para testar os endpoints.

