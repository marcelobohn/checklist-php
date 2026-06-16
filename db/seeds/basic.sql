-- =====================================================================
-- Seed BÁSICO — dados essenciais para a aplicação funcionar.
-- Aplicado automaticamente no 1º `up` (docker/initdb.sh).
--
-- Usuário: admin / Senha: admin (hash bcrypt de 'admin').
-- Idempotente: só insere se ainda não existir.
-- =====================================================================

INSERT INTO usuario (nome, senha, admin)
SELECT 'admin', '$2y$12$Il/ogvC3t1IGLHTciptVguRGy2fcCn6kN3EhtAqN0fG2Qg0aiu0k.', 'S'
WHERE NOT EXISTS (SELECT 1 FROM usuario WHERE nome = 'admin');
