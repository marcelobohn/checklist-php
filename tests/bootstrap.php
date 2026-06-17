<?php
// Bootstrap da suíte de testes.
//
// Carrega o autoloader do Composer (classes de produção em src/App + os doubles
// de teste). A verificação de "app no ar" (necessária só para os testes
// funcionais) fica no FunctionalTestCase, para que os testes unitários rodem
// sem depender do ambiente dockerizado.

require __DIR__ . '/../vendor/autoload.php';
