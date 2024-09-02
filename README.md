# API Project

Este projeto é uma API simples desenvolvida em PHP puro. Abaixo você encontrará instruções para configurar o ambiente, instalar dependências, e rodar os testes.

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

## Testes

Os testes unitários estão localizados no diretório tests/. Para garantir que a API funcione conforme esperado, siga as regras e execute os testes descritos abaixo.

**Regras de Teste**

1. **Redefinir estado antes dos testes**

    Antes de iniciar os testes, o estado do sistema deve ser limpo para garantir resultados consistentes. Isso pode incluir a criação e exclusão de contas fictícias.

2. **Obter saldo para conta inexistente**

- Objetivo: Verificar que a API retorna um erro quando tentamos obter o saldo de uma conta que não existe.
- Código de status esperado: 404 Not Found
- Resposta e sperada: {"error": "Account not found"}

3. **Criar uma Conta com Saldo Inicial**

- Objetivo: Testar a criação de uma nova conta com um saldo inicial.
- Método: POST /event
- Payload:
    ```bash
    {
        "type": "deposit",
        "account_id": "1",
        "amount": 100.0
    }
    ```
- Resposta Esperada: {"balance": 100}

4. **Depositar em Conta Existente**

- Objetivo: Testar o depósito de um valor em uma conta existente.
- Método: POST /event
- Payload:
    ```bash
    {
        "type": "deposit",
        "account_id": "1",
        "amount": 50.0
    }
    ```
- Resposta Esperada: {"balance": 150}

5. **Obter Saldo para Conta Existente**

- Objetivo: Verificar o saldo atual de uma conta existente.
- Método: GET /balance
- Resposta Esperada: {"balance": 150}

6. **Saque de uma Conta Inexistente**

- Objetivo: Verificar que a API retorna um erro ao tentar sacar de uma conta que não existe.
- Método: POST /event
- Payload:
    ```bash
    {
        "type": "withdraw",
        "account_id": "1",
        "amount": 50.0
    }
    ```
- Resposta Esperada: {"error": "Account not found"}

7. **Sacar de Conta Existente**

- Objetivo: Testar o saque de um valor de uma conta existente.
- Método: POST /event
- Payload:
    ```bash
    {
        "type": "withdraw",
        "account_id": "1",
        "amount": 50.0
    }
    ```
- Resposta Esperada: {"balance": 100}

8. **Transferir de Conta Existente**

- Objetivo: Testar a transferência de um valor de uma conta existente para outra.
- Método: POST /event
- Payload:
    ```bash
    {
        "type": "transfer",
        "account_id": "1",
        "destination_id": "2",
        "amount": 25.0
    }
    ```
- Resposta Esperada:
    ```bash
    {
        "source_balance": 75,
        "destination_balance": 25
    }
    ```

9. **Transferência de Conta Inexistente**

- Objetivo: Verificar que a API retorna um erro ao tentar transferir de uma conta que não existe.
- Método: POST /event
- Payload:
    ```bash
    {
        "type": "transfer",
        "account_id": "1",
        "destination_id": "2",
        "amount": 25.0
    }
    ```
- Resposta Esperada: {"error": "Account not found"}


