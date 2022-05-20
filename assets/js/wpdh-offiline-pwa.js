// source: https://googlechrome.github.io/samples/service-worker/basic/
/*
 Copyright 2016 Google Inc. All Rights Reserved.
 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
*/
// Nomes dos dois caches usados ​​nesta versão do service worker.
// Altera para v2, etc. quando você atualizar algum dos recursos locais, o que
// por sua vez aciona o evento de instalação novamente.
const PRECACHE = 'precache-v1';
const RUNTIME = 'runtime';
// Uma lista de recursos locais que sempre queremos que sejam armazenados em cache.
const PRECACHE_URLS = ['./', 'https://revistasaudevida.com.br/wp-content/cache/autoptimize/css/autoptimize_d770178a470a6aeb6ee49f48e74d364f.css', 'https://revistasaudevida.com.br/wp-includes/js/jquery/jquery.min.js', 'https://revistasaudevida.com.br/wp-content/cache/autoptimize/js/autoptimize_f612b0a0c8694b203963f5bfb2f9cd36.js', ];
// O evento de instalação que cuida do precaching dos recursos que sempre precisamos.
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(PRECACHE)
      .then(cache => cache.addAll(PRECACHE_URLS))
      .then(self.skipWaiting())
  );
});
// O evento de ativação que cuida da limpeza de caches antigos.
self.addEventListener('activate', event => {
  const currentCaches = [PRECACHE, RUNTIME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return cacheNames.filter(cacheName => !currentCaches.includes(cacheName));
    }).then(cachesToDelete => {
      return Promise.all(cachesToDelete.map(cacheToDelete => {
        return caches.delete(cacheToDelete);
      }));
    }).then(() => self.clients.claim())
  );
});
// O evento de busca de recursos exibe respostas para recursos de mesma origem de um cache.
// Se nenhuma resposta for encontrada, ele preencherá o cache em tempo de execução com a resposta
// da rede antes de retornar para a página.
self.addEventListener('fetch', event => {
  // Ignora solicitações de origem cruzada, como as do Google Analytics.
  if (event.request.url.startsWith(self.location.origin)) {
    event.respondWith(
      caches.match(event.request).then(cachedResponse => {
        if (cachedResponse) {
          return cachedResponse;
        }
        return caches.open(RUNTIME).then(cache => {
          return fetch(event.request).then(response => {
            // Coloque uma cópia da resposta no cache em tempo de execução.
            return cache.put(event.request, response.clone()).then(() => {
              return response;
            });
          });
        });
      })
    );
  }
});