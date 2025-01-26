# Inovanti Holidays - Gerenciamento de Feriados

[![Latest Stable Version](https://poser.pugx.org/inovanti-bank/inovanti-Holidays/v)](https://packagist.org/packages/inovanti-bank/inovanti-Holidays)
[![Total Downloads](https://poser.pugx.org/inovanti-bank/inovanti-Holidays/downloads)](https://packagist.org/packages/inovanti-bank/inovanti-Holidays)
[![License](https://poser.pugx.org/inovanti-bank/inovanti-Holidays/license)](https://packagist.org/packages/inovanti-bank/inovanti-Holidays)
[![PHP Version Require](https://poser.pugx.org/inovanti-bank/inovanti-Holidays/require/php)](https://packagist.org/packages/inovanti-bank/inovanti-Holidays)

## Introdução

O **inovanti-holidays** é um componente Laravel 11 para gerenciamento de feriados nacionais e estaduais. Ele permite:

- Listar, criar, atualizar e deletar feriados
- Calcular o próximo dia útil a partir de uma data
- Popular a tabela de feriados via comando Artisan
- Gerenciar feriados fixos e móveis
- Configurar estados e anos desejados
- Integrar facilmente com outras aplicações Laravel

## 🚀 Instalação

Para instalar o pacote, execute o seguinte comando:

```bash
composer require inovanti-bank/inovanti-holidays
```

Após a instalação, publique a configuração padrão:

```bash
php artisan vendor:publish --tag=holidays-config
```

Em seguida, execute as migrations:

```bash
php artisan migrate
```

## 📩 Uso

### 1. **Listar todos os feriados**

```php
use Inovanti\Holidays\Models\Holiday;

$holidays = Holiday::all();
```

### 2. **Adicionar um novo feriado**

```php
Holiday::create([
    'name' => 'Dia da Independência',
    'date' => '2025-09-07',
    'type' => 'fix',
    'state' => null,
    'optional' => false
    'scope' => 'state'
    'is_national' => true
]);
```

### 3. **Verificar se uma data é feriado**

```php
return Holiday::where('date', '2025-12-25')->exists();
```

### 4. **Calcular o próximo dia útil**

```php
use Carbon\Carbon;
use InovantiBank\Holidays\Helpers\DateHelper;
// Consulte mais métodos de apoio na classe DateHelper

$today = Carbon::today();
return $helper->getNextBusinessDay($today);
```

### 5. **Popular feriados na base de dados**

```bash
php artisan holidays:save
```

Isso irá carregar os feriados padrões definidos no pacote.

## 🧪 Testes

O pacote inclui testes unitários e de feature. Para executá-los:

## Teste geral

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
