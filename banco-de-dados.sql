DROP DATABASE IF EXISTS assistiverse;
CREATE DATABASE assistiverse;
USE assistiverse;

-- Usuários
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('PCD', 'MAKER', 'FAMILIAR', 'ESPECIALISTA DA SAÚDE', 'FORNECEDOR') NOT NULL,
    foto VARCHAR(255),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Recuperação de senha
CREATE TABLE recuperacao_senhas (
    id_recuperacao INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expira_em DATETIME NOT NULL,
    FOREIGN KEY (email) REFERENCES usuarios(email) ON DELETE CASCADE
);

-- Perfil de usuários
CREATE TABLE perfil_usuario (
    id_perfil INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    categoria ENUM('PCD', 'MAKER', 'FAMILIAR', 'ESPECIALISTA DA SAÚDE', 'FORNECEDOR') NOT NULL,
    descricao TEXT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Produtos
CREATE TABLE produtos (
    id_produto INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    nome_produto VARCHAR(255) NOT NULL,
    descricao TEXT,
    para_quem VARCHAR(255),
    por_quem VARCHAR(255),
    por_que TEXT,
    para_que TEXT,
    pre_requisitos TEXT,
    modo_de_uso TEXT,
    imagens TEXT,
    arquivos TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Componentes
CREATE TABLE componentes (
    id_componente INT AUTO_INCREMENT PRIMARY KEY,
    id_produto INT NOT NULL,
    nome_componente VARCHAR(255) NOT NULL,
    descricao TEXT,
    imagens TEXT,
    arquivos TEXT,
    FOREIGN KEY (id_produto) REFERENCES produtos(id_produto) ON DELETE CASCADE
);

-- Dependências entre componentes
CREATE TABLE componentes_dependencias (
    id_dependencia INT AUTO_INCREMENT PRIMARY KEY,
    id_componente_origem INT NOT NULL,
    id_componente_destino INT NOT NULL,
    FOREIGN KEY (id_componente_origem) REFERENCES componentes(id_componente) ON DELETE CASCADE,
    FOREIGN KEY (id_componente_destino) REFERENCES componentes(id_componente) ON DELETE CASCADE
);

-- Materiais
CREATE TABLE materiais (
    id_material INT AUTO_INCREMENT PRIMARY KEY,
    nome_material VARCHAR(255) UNIQUE NOT NULL,
    descricao_material TEXT
);

-- Ferramentas
CREATE TABLE ferramentas (
    id_ferramenta INT AUTO_INCREMENT PRIMARY KEY,
    nome_ferramenta VARCHAR(255) UNIQUE NOT NULL,
    descricao_ferramenta TEXT
);

-- Materiais usados em cada componente
CREATE TABLE componente_materiais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_componente INT NOT NULL,
    id_material INT NOT NULL,
    quantidade DECIMAL(10,2),
    unidade VARCHAR(20),
    FOREIGN KEY (id_componente) REFERENCES componentes(id_componente) ON DELETE CASCADE,
    FOREIGN KEY (id_material) REFERENCES materiais(id_material) ON DELETE CASCADE
);

-- Ferramentas usadas em cada componente
CREATE TABLE componente_ferramentas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_componente INT NOT NULL,
    id_ferramenta INT NOT NULL,
    dimensoes VARCHAR(255),
    FOREIGN KEY (id_componente) REFERENCES componentes(id_componente) ON DELETE CASCADE,
    FOREIGN KEY (id_ferramenta) REFERENCES ferramentas(id_ferramenta) ON DELETE CASCADE
);

-- Passo a passo
CREATE TABLE passo_a_passo (
    id_passo INT AUTO_INCREMENT PRIMARY KEY,
    id_componente INT NOT NULL,
    descricao TEXT,
    arquivo_upload VARCHAR(255),
    FOREIGN KEY (id_componente) REFERENCES componentes(id_componente) ON DELETE CASCADE
);

-- Materiais por passo
CREATE TABLE passo_materiais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_passo INT NOT NULL,
    id_material INT NOT NULL,
    FOREIGN KEY (id_passo) REFERENCES passo_a_passo(id_passo) ON DELETE CASCADE,
    FOREIGN KEY (id_material) REFERENCES materiais(id_material) ON DELETE CASCADE
);

-- Ferramentas por passo
CREATE TABLE passo_ferramentas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_passo INT NOT NULL,
    id_ferramenta INT NOT NULL,
    FOREIGN KEY (id_passo) REFERENCES passo_a_passo(id_passo) ON DELETE CASCADE,
    FOREIGN KEY (id_ferramenta) REFERENCES ferramentas(id_ferramenta) ON DELETE CASCADE
);


ALTER TABLE produtos ADD COLUMN testado_por VARCHAR(255) AFTER por_quem;
--------------------------------------------------------------

-- Tabelas criadas para as seções que aparecem quando o usuário marca uma classificação no cadastro ou na edição
-- Dados familiares
CREATE TABLE dados_familiares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    relacao VARCHAR(100),
    tipo_deficiencia VARCHAR(100),
    descricao TEXT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Dados PCD
CREATE TABLE dados_pcd (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    deficiencia VARCHAR(100),
    limitacoes TEXT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Dados Maker
CREATE TABLE dados_maker (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    formacao VARCHAR(255),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Dados Especialista da Saúde
CREATE TABLE dados_especialista (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    formacao VARCHAR(255),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Dados Fornecedor
CREATE TABLE dados_fornecedor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    atuacao VARCHAR(255),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);
