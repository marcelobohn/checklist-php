-- =====================================================================
-- Seed de DESENVOLVIMENTO — dados de exemplo para explorar o fluxo.
-- NÃO é carregado automaticamente. Aplique sob demanda:
--   db/seed.sh dev
--
-- Usa LAST_INSERT_ID() (não fixa ids). Pensado para um banco recém-criado;
-- rodar mais de uma vez duplica os exemplos.
-- =====================================================================

INSERT INTO pergunta (descricao, marcar, resposta) VALUES ('Ambiente foi testado?', 'S', 'N');
SET @p1 := LAST_INSERT_ID();

INSERT INTO pergunta (descricao, marcar, resposta) VALUES ('Qual o status da entrega?', 'N', 'S');
SET @p2 := LAST_INSERT_ID();

INSERT INTO resposta (idPergunta, descricao) VALUES
  (@p2, 'Pendente'),
  (@p2, 'Em andamento'),
  (@p2, 'Concluido');

INSERT INTO modelo (nome) VALUES ('Checklist de exemplo');
SET @m := LAST_INSERT_ID();

INSERT INTO modelopergunta (idModelo, idPergunta, ordem) VALUES
  (@m, @p1, 1),
  (@m, @p2, 2);
