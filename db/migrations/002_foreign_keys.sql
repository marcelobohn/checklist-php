-- =====================================================================
-- Migration 002 — foreign keys (integridade referencial).
--
-- O schema 001 foi reconstruído a partir das queries e não tinha FKs, então
-- era possível criar órfãos (apagar uma pergunta deixava resposta /
-- modelopergunta / registroitem apontando para um id inexistente).
--
-- Estratégia de ON DELETE:
--   CASCADE  — filhos "donos" do pai (somem junto com ele):
--              resposta, modelopergunta, registroitem(do registro).
--   RESTRICT — referências históricas (protegem o que já foi respondido):
--              registro->modelo, registroitem->pergunta.
--
-- registroitem.idResposta NÃO ganha FK: para perguntas marcar='S' essa coluna
-- guarda a sentinela 1/0 (sim/não), não um idResposta real (ver registro.monta.php).
--
-- Idempotência: o controle é por schema_migrations (migrate.sh / initdb.sh),
-- então este arquivo roda uma única vez. Ainda assim, limpamos eventuais
-- órfãos pré-existentes antes de criar as FKs, senão o ALTER falharia.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- Limpeza defensiva de órfãos (necessária para o ADD FOREIGN KEY passar).
-- ---------------------------------------------------------------------
DELETE r FROM resposta r
  LEFT JOIN pergunta p ON r.idPergunta = p.idPergunta
  WHERE p.idPergunta IS NULL;

DELETE mp FROM modelopergunta mp
  LEFT JOIN modelo m ON mp.idModelo = m.idModelo
  WHERE m.idModelo IS NULL;
DELETE mp FROM modelopergunta mp
  LEFT JOIN pergunta p ON mp.idPergunta = p.idPergunta
  WHERE p.idPergunta IS NULL;

DELETE ri FROM registroitem ri
  LEFT JOIN registro r ON ri.idRegistro = r.idRegistro
  WHERE r.idRegistro IS NULL;
DELETE ri FROM registroitem ri
  LEFT JOIN pergunta p ON ri.idPergunta = p.idPergunta
  WHERE p.idPergunta IS NULL;

DELETE reg FROM registro reg
  LEFT JOIN modelo m ON reg.idModelo = m.idModelo
  WHERE m.idModelo IS NULL;

-- ---------------------------------------------------------------------
-- Foreign keys.
-- ---------------------------------------------------------------------
ALTER TABLE resposta
  ADD CONSTRAINT fk_resposta_pergunta
  FOREIGN KEY (idPergunta) REFERENCES pergunta (idPergunta)
  ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE modelopergunta
  ADD CONSTRAINT fk_mp_modelo
  FOREIGN KEY (idModelo) REFERENCES modelo (idModelo)
  ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT fk_mp_pergunta
  FOREIGN KEY (idPergunta) REFERENCES pergunta (idPergunta)
  ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE registro
  ADD CONSTRAINT fk_registro_modelo
  FOREIGN KEY (idModelo) REFERENCES modelo (idModelo)
  ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE registroitem
  ADD CONSTRAINT fk_ri_registro
  FOREIGN KEY (idRegistro) REFERENCES registro (idRegistro)
  ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT fk_ri_pergunta
  FOREIGN KEY (idPergunta) REFERENCES pergunta (idPergunta)
  ON DELETE RESTRICT ON UPDATE CASCADE;
