# Documentação do Teste de Consumo da API NewsAPI com Laravel

Este documento detalha a implementação de um teste simples para consumir a API NewsAPI utilizando o HTTP Client nativo do Laravel (`Http` facade).

## 1. Objectivo

O objetivo deste teste é demonstrar como realizar uma requisição autenticada a uma API pública externa (NewsAPI) a partir de uma aplicação Laravel, processar a resposta e retornar os dados.

## 2. Configuração

Para utilizar a API NewsAPI, é necessário obter uma API Key. Siga os passos abaixo para configurar a chave na sua aplicação Laravel:

1. Obtenha uma API Key gratuita no site oficial da NewsAPI (https://newsapi.org/).
2. Abra o arquivo `.env` na raiz do seu projeto Laravel.
3. Adicione a seguinte linha ao arquivo `.env`, substituindo `SUA_NEWS_API_KEY` pela chave que você obteve:

   ```dotenv
   NEWS_API_KEY=SUA_NEWS_API_KEY
   ```
4. Certifique-se de que a chave está sendo carregada corretamente no arquivo de configuração `config/services.php`. A configuração esperada é:

   ```php
   //... outras configurações de serviços

   'newsapi' => [
       'key' => env('NEWS_API_KEY'),
   ],

   //... outras configurações de serviços
   ```

## 3. Implementação

A lógica para consumir a API NewsAPI está implementada no controlador `NewsApiController` e a rota correspondente está definida no arquivo de rotas web.

### 3.1 Controlador (`app/Http/Controllers/NewsApiController.php`)

O controlador `NewsApiController` possui um método `HeadLines` que realiza a requisição à API:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewsApiController extends Controller
{
    public function HeadLines()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.newsapi.key'),
            ])->get('https://newsapi.org/v2/top-headlines', [
                'country' => 'us', 
                'category' => 'technology',
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json(['error' => 'Erro na API'], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }
    
}
```

- O método `HeadLines` utiliza a facade `Http` do Laravel para fazer uma requisição GET para o endpoint `/v2/top-headlines` da NewsAPI.
- A autenticação é feita através do cabeçalho `Authorization` com o esquema `Bearer`, utilizando a API Key configurada no arquivo `.env` e acessada via `config('services.newsapi.key')`.
- São passados parâmetros na query string para filtrar as notícias por país (`us`) e categoria (`technology`).
- A resposta da API é verificada: se for bem-sucedida (`$response->successful()`), o corpo da resposta em JSON é retornado; caso contrário, uma resposta de erro com o status da API é retornada.
- Um bloco `try-catch` é utilizado para capturar possíveis exceções durante a requisição e retornar um erro interno.

### 3.2 Rota (`routes/web.php`)

A rota para acessar o método `HeadLines` do controlador está definida no arquivo `routes/web.php`:

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsApiController;

Route::get('/noticias', [NewsApiController::class, 'HeadLines']);
```

- Uma rota GET `/noticias` foi definida, que direciona as requisições para o método `HeadLines` do `NewsApiController`.

## 4. Como Testar

Após configurar a API Key e ter a aplicação Laravel rodando, você pode testar o endpoint da seguinte formas:

- **Postman ou ferramenta similar:** Faça uma requisição GET para a URL `http://localhost:8000/api/noticias`.

Você deverá receber uma resposta JSON contendo as notícias principais de tecnologia dos EUA, conforme retornado pela NewsAPI.

## 5. Exemplo de Resposta Esperada

A resposta esperada é um objeto JSON similar ao seguinte (o conteúdo exato pode variar dependendo das notícias atuais):

```json
{
    "status": "ok",
    "totalResults": 30,
    "articles": [
        {
            "source": {
                "id": null,
                "name": "CNET"
            },
            "author": "See full bio",
            "title": "Apple WWDC 2025 Live: iOS 26, Updates to Apple Intelligence, Mac OS, iPadOS - CNET",
            "description": "The new look comes with lots of features for Apple's devices, including a refreshed Phone app, windowing for the iPad and an enhanced Spotlight for the Mac.",
            "url": "https://www.cnet.com/news-live/apple-wwdc-2025-live-keynote-news-annoucements-for-ios-mac/",
            "urlToImage": "https://www.cnet.com/a/img/resize/de1f665d23e94f425fa9c5a7446e249a689929ef/hub/2025/06/03/b69e5efd-f160-4cf3-afef-7848ff47dfa9/promo-static.jpg?auto=webp&fit=crop&height=675&width=1200",
            "publishedAt": "2025-06-09T18:28:00Z",
            "content": "Jeff Carlson/CNET\r\nIn just a matter of hours, we'll likely see Apple preview the next version of iPhone software. It could be called iOS 19, or as rumors point out, iOS 26. Despite some leaks, CNET e… [+698 chars]"
        },
          // ... outros artigos
    ]
}
```

Em caso de erro, a resposta será um JSON com uma chave `error` e uma mensagem, e o código de status HTTP refletirá o erro (por exemplo, 401 para chave inválida, 500 para erro interno).


