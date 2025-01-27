# Inovanti Holidays - Gerenciamento de Feriados

[![Latest Stable Version](https://poser.pugx.org/inovanti-bank/inovanti-Holidays/v)](https://packagist.org/packages/inovanti-bank/inovanti-Holidays)
[![Total Downloads](https://poser.pugx.org/inovanti-bank/inovanti-Holidays/downloads)](https://packagist.org/packages/inovanti-bank/inovanti-Holidays)
[![License](https://poser.pugx.org/inovanti-bank/inovanti-Holidays/license)](https://packagist.org/packages/inovanti-bank/inovanti-Holidays)
[![PHP Version Require](https://poser.pugx.org/inovanti-bank/inovanti-Holidays/require/php)](https://packagist.org/packages/inovanti-bank/inovanti-Holidays)

## Introdução

O **inovanti-holidays** é um componente Laravel 11 para gerenciamento de feriados nacionais e estaduais. Ele permite:

✅ Listar, criar, atualizar e deletar feriados
✅ Verificar se uma data é feriado
✅ Calcular o próximo dia útil a partir de uma data
✅ Popular a tabela de feriados via comando Artisan
✅ Gerenciar feriados fixos e móveis
✅ Configurar estados e anos desejados
✅ Integrar facilmente com outras aplicações Laravel

## 📦 Instalação

Para instalar o pacote, execute o seguinte comando:

```bash
composer require inovanti-bank/inovanti-holidays
```

Em seguida, execute as migrations:

```bash
php artisan migrate
```
---

## 📌 Modificações e Melhorias na versão 1.1.0
A versão 1.1.0 introduz melhorias significativas no pacote, incluindo:

- 1️⃣ Validação automática de feriados existentes ao cadastrar ou atualizar um feriado, evitando duplicidades com os mesmos parâmetros.
- 2️⃣ Parâmetro persistent_for_all_years: Agora é possível criar um feriado para todos os anos disponíveis na base de dados, eliminando a necessidade de inseri-los manualmente ano a ano.
- 3️⃣ Melhorias no comando holidays:save:
    - **Nova opção** `--truncate`: Permite limpar a tabela antes da inserção.
    - **Nova opção** `--yearFrom=YYYY`: Define a partir de qual ano os feriados devem ser gerados.
    - **Confirmações interativas**: O usuário é perguntado se deseja gerar feriados para 10 anos (padrão) ou escolher outro valor.


## 📩 Uso

### 1. **Listar todos os feriados**

```php
use Inovanti\Holidays\Models\Holiday;

$holidays = Holiday::all();
```

### 2. **Adicionar um novo feriado**

```php
use InovantiBank\Holidays\Services\HolidaysService;

$service = app(HolidaysService::class);

$holiday = $service->create([
    'name' => 'Dia da Independência',
    'date' => '2025-09-07',
    'type' => 'fix',
    'state' => null,
    'optional' => false,
    'scope' => 'state',
]);
// Se o feriado já estiver cadastrado, será lançada uma exceção com a mensagem:
// Já existe um feriado com esses parâmetros.
```

### 3. **Criar feriados para múltiplos anos**
```php
$service->create([
    'name' => 'Feriado Anual',
    'date' => '2025-06-15',
    'type' => 'fix',
    'scope' => 'state',
    'optional' => false,
    'state' => 'SP',
], true); 
// Criará o mesmo feriado para todos os anos da base
// Se um feriado para aquele ano já existir, ele será ignorado.
```

### 4. **Verificar se uma data é feriado**

```php
return Holiday::where('date', '2025-12-25')->exists();
```

### 5. **Calcular o próximo dia útil**

```php
use Carbon\Carbon;
use InovantiBank\Holidays\Helpers\DateHelper;
// Consulte mais métodos de apoio na classe DateHelper

$today = Carbon::today();
return $helper->getNextBusinessDay($today);
```

### 6. **Popular feriados na base de dados**

```bash
php artisan holidays:save
```

➡️ Isso adiciona feriados para os próximos 10 anos (padrão).
- ✅ Novas opções:
    - `--truncate`: Apaga todos os feriados antes de inserir. Requer confirmação.
    - `--yearFrom=YYYY`: Define a partir de qual ano os feriados serão gerados.
    - `--years=N`: Define quantos anos à frente deseja gerar.

### Exemplos:

- Gerar feriados apenas de 2025 a 2030:
```bash
php artisan holidays:save --yearFrom=2025 --years=5
```

- Zerar e recriar feriados para os próximos 10 anos:
```bash
php artisan holidays:save --truncate
```
*Se `--truncate` for passado, será exibida uma pergunta de confirmação antes de apagar a tabela.*


## 🧪 Testes

O pacote inclui testes unitários e de feature. Para executá-los:

## Teste geral
*pode ser usado umm dos dois comandos*

```bash
vendor/bin/phpunit
composer test
```

### Testes Unit

```bash
vendor/bin/phpunit  --testsuite=Unit
composer unit
```

### Testes Feature:

```bash
vendor/bin/phpunit --testsuite=feature
composer feature
```

## 🤝 Contribuindo

Contribuições são bem-vindas! Se você deseja reportar um bug, solicitar um novo recurso ou contribuir com código, fique à vontade para abrir uma issue ou enviar um Pull Request.

1. Faça um Fork do projeto
2. Crie sua feature branch: `git checkout -b minha-nova-feature`
3. Commit suas mudanças: `git commit -m 'Adiciona nova feature'`
4. Faça o push para a branch: `git push origin minha-nova-feature`
5. Crie um novo Pull Request

## 📜 Licença

Este projeto está licenciado sob a [MIT license](https://github.com/Inovanti-Bank/inovanti-Holidays/tree/developer?tab=License-1-ov-file#).


📢 Resumo das mudanças
- ✅ Agora feriados duplicados são evitados automaticamente.
- ✅ Adicionado suporte para criação de feriados para múltiplos anos.
- ✅ Melhorias no comando holidays:save, com opções interativas e novos parâmetros.
- ✅ Mais testes cobrindo os novos recursos.