CREATE DATABASE plataforma_saude_mental;
USE plataforma_saude_mental;

CREATE TABLE login_tentativas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150),
    ip VARCHAR(45),
    tentativas INT DEFAULT 0,
    bloqueado_ate DATETIME,
    ultimo_login DATETIME,
    
    UNIQUE KEY unique_tentativa (email, ip)
);

CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('paciente','profissional','admin') NOT NULL,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('ativo','inativo','bloqueado') DEFAULT 'ativo',
    data_nascimento DATE
);

CREATE TABLE profissional (
    id_profissional INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    registro_profissional VARCHAR(50),
    descricao TEXT,
    cidade VARCHAR(100),
    estado VARCHAR(50),
    validado BOOLEAN DEFAULT FALSE,

    FOREIGN KEY (id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
);

CREATE TABLE especialidade (
    id_especialidade INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT
);

CREATE TABLE profissional_especialidade (
    id_profissional INT,
    id_especialidade INT,
    PRIMARY KEY (id_profissional, id_especialidade),

    FOREIGN KEY (id_profissional)
    REFERENCES profissional(id_profissional)
    ON DELETE CASCADE,

    FOREIGN KEY (id_especialidade)
    REFERENCES especialidade(id_especialidade)
    ON DELETE CASCADE
);

CREATE TABLE consulta (
    id_consulta INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    id_profissional INT NOT NULL,
    data_hora DATETIME NOT NULL,
    status ENUM('agendada','confirmada','cancelada','finalizada') DEFAULT 'agendada',
    tipo ENUM('online','presencial') DEFAULT 'online',

    FOREIGN KEY (id_paciente) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_profissional) REFERENCES profissional(id_profissional)
);

INSERT INTO especialidade (nome) VALUES
('Psicologia'),
('Psiquiatria'),
('Terapia');

-- ADMIN (ATENÇÃO: depois troca a senha por hash)
INSERT INTO usuario (nome, email, senha, tipo_usuario)
VALUES ('Admin', 'admin@admin.com', '123456', 'admin');