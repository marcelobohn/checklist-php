-- =====================================================================
-- Migration 001 — schema inicial do banco `checklist`.
--
-- Tabelas reconstruídas a partir das queries do código (o projeto original
-- não acompanhava .sql). Charset utf8mb4 (UTF-8 ponta a ponta após a migração
-- para PDO). Idempotente (CREATE TABLE IF NOT EXISTS).
-- =====================================================================

SET NAMES utf8mb4;

-- Usuários do sistema (login + perfil admin)
CREATE TABLE IF NOT EXISTS usuario (
  idUsuario INT(11) NOT NULL AUTO_INCREMENT,
  nome      VARCHAR(100) NOT NULL,
  senha     VARCHAR(255) NOT NULL,         -- hash bcrypt (password_hash)
  admin     CHAR(1) NOT NULL DEFAULT 'N',  -- 'S' = administrador
  PRIMARY KEY (idUsuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Perguntas do checklist
-- marcar='S'  -> pergunta sim/não (radio)
-- resposta='S'-> pergunta de múltipla escolha (usa tabela `resposta`)
CREATE TABLE IF NOT EXISTS pergunta (
  idPergunta INT(11) NOT NULL AUTO_INCREMENT,
  descricao  VARCHAR(255) NOT NULL,
  marcar     CHAR(1) NOT NULL DEFAULT 'N',
  resposta   CHAR(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (idPergunta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Alternativas de resposta (para perguntas de múltipla escolha)
CREATE TABLE IF NOT EXISTS resposta (
  idResposta INT(11) NOT NULL AUTO_INCREMENT,
  idPergunta INT(11) NOT NULL,
  descricao  VARCHAR(255) NOT NULL,
  PRIMARY KEY (idResposta),
  KEY idx_resposta_pergunta (idPergunta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Modelo de checklist (cabeçalho)
CREATE TABLE IF NOT EXISTS modelo (
  idModelo INT(11) NOT NULL AUTO_INCREMENT,
  nome     VARCHAR(150) NOT NULL,
  PRIMARY KEY (idModelo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Associação modelo <-> perguntas, com ordem de exibição
CREATE TABLE IF NOT EXISTS modelopergunta (
  idModeloPergunta INT(11) NOT NULL AUTO_INCREMENT,
  idModelo   INT(11) NOT NULL,
  idPergunta INT(11) NOT NULL,
  ordem      INT(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (idModeloPergunta),
  KEY idx_mp_modelo (idModelo),
  KEY idx_mp_pergunta (idPergunta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Checklist respondido (cabeçalho do registro)
CREATE TABLE IF NOT EXISTS registro (
  idRegistro INT(11) NOT NULL AUTO_INCREMENT,
  idModelo   INT(11) NOT NULL,
  `rand`     INT(11) DEFAULT NULL,
  `data`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  usuario    VARCHAR(255) DEFAULT NULL,
  versao     VARCHAR(100) DEFAULT NULL,
  base       VARCHAR(255) DEFAULT NULL,    -- "Cliente" na tela
  tarefa     VARCHAR(255) DEFAULT NULL,
  codCliente VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (idRegistro),
  KEY idx_registro_modelo (idModelo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Respostas dadas em cada checklist respondido (itens do registro)
CREATE TABLE IF NOT EXISTS registroitem (
  idRegistroItem INT(11) NOT NULL AUTO_INCREMENT,
  idRegistro INT(11) NOT NULL,
  idPergunta INT(11) NOT NULL,
  idResposta INT(11) DEFAULT NULL,  -- 1/0 p/ "marcar"; idResposta p/ múltipla
  PRIMARY KEY (idRegistroItem),
  KEY idx_ri_registro (idRegistro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
