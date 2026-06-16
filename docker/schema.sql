-- =====================================================================
-- Schema do banco `checklist` reconstruído a partir do código-fonte.
--
-- O fonte é de ~2012 (API mysql_*, jQuery 1.7.1) e não acompanhava o
-- arquivo .sql original. Estas tabelas foram inferidas das queries em:
--   conexaoBD.php, login.php, usuario/*, pergunta/*, modelo/*,
--   registro/*, consulta/*.
--
-- Charset utf8mb4: após a migração para PDO o código trabalha em UTF-8
-- ponta a ponta (sem utf8_encode/decode).
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- Usuários do sistema (login + perfil admin)
-- Referências: login.php, usuario/usuario.model.php, usuario.control.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuario (
  idUsuario INT(11) NOT NULL AUTO_INCREMENT,
  nome      VARCHAR(100) NOT NULL,
  senha     VARCHAR(255) NOT NULL,         -- hash bcrypt (password_hash)
  admin     CHAR(1) NOT NULL DEFAULT 'N',  -- 'S' = administrador
  PRIMARY KEY (idUsuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Perguntas do checklist
-- marcar='S'  -> pergunta sim/não (radio)
-- resposta='S'-> pergunta de múltipla escolha (usa tabela `resposta`)
-- Referências: pergunta/pergunta.model.php, pergunta.control.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pergunta (
  idPergunta INT(11) NOT NULL AUTO_INCREMENT,
  descricao  VARCHAR(255) NOT NULL,
  marcar     CHAR(1) NOT NULL DEFAULT 'N',
  resposta   CHAR(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (idPergunta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Alternativas de resposta (para perguntas de múltipla escolha)
-- Referências: pergunta/pergunta.resposta.inclui.php, .lista.php, .apaga.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS resposta (
  idResposta INT(11) NOT NULL AUTO_INCREMENT,
  idPergunta INT(11) NOT NULL,
  descricao  VARCHAR(255) NOT NULL,
  PRIMARY KEY (idResposta),
  KEY idx_resposta_pergunta (idPergunta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Modelo de checklist (cabeçalho)
-- Referências: modelo/modelo.model.php, modelo.control.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS modelo (
  idModelo INT(11) NOT NULL AUTO_INCREMENT,
  nome     VARCHAR(150) NOT NULL,
  PRIMARY KEY (idModelo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Associação modelo <-> perguntas, com ordem de exibição
-- Referências: modelo/modelo.pergunta.inclui.php, .lista.php, .apaga.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS modelopergunta (
  idModeloPergunta INT(11) NOT NULL AUTO_INCREMENT,
  idModelo   INT(11) NOT NULL,
  idPergunta INT(11) NOT NULL,
  ordem      INT(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (idModeloPergunta),
  KEY idx_mp_modelo (idModelo),
  KEY idx_mp_pergunta (idPergunta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Checklist respondido (cabeçalho do registro)
-- `rand` é um número aleatório usado para recuperar o idRegistro
-- recém-inserido (truque pré-mysql_insert_id no código original).
-- Referências: registro/registro.grava.php, consulta/consulta.monta.php,
--              consulta.lista.filtro.php
-- ---------------------------------------------------------------------
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

-- ---------------------------------------------------------------------
-- Respostas dadas em cada checklist respondido (itens do registro)
-- Referências: registro/registro.grava.php, consulta/consulta.monta.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS registroitem (
  idRegistroItem INT(11) NOT NULL AUTO_INCREMENT,
  idRegistro INT(11) NOT NULL,
  idPergunta INT(11) NOT NULL,
  idResposta INT(11) DEFAULT NULL,  -- 1/0 p/ perguntas "marcar"; idResposta p/ múltipla
  PRIMARY KEY (idRegistroItem),
  KEY idx_ri_registro (idRegistro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- Dados iniciais (seed) — necessário para conseguir logar.
-- Usuário: admin / Senha: admin  (perfil administrador)
-- A senha é gravada como hash bcrypt de 'admin' (password_hash/PASSWORD_DEFAULT).
-- =====================================================================
INSERT INTO usuario (nome, senha, admin) VALUES
  ('admin', '$2y$12$Il/ogvC3t1IGLHTciptVguRGy2fcCn6kN3EhtAqN0fG2Qg0aiu0k.', 'S');

-- Dados de exemplo opcionais para visualizar o fluxo -------------------
INSERT INTO pergunta (descricao, marcar, resposta) VALUES
  ('Ambiente foi testado?', 'S', 'N'),
  ('Qual o status da entrega?', 'N', 'S');

INSERT INTO resposta (idPergunta, descricao) VALUES
  (2, 'Pendente'),
  (2, 'Em andamento'),
  (2, 'Concluido');

INSERT INTO modelo (nome) VALUES ('Checklist de exemplo');

INSERT INTO modelopergunta (idModelo, idPergunta, ordem) VALUES
  (1, 1, 1),
  (1, 2, 2);
