-- Schema completo para PostgreSQL (Akipede Mais)

-- 1. Users
CREATE TABLE IF NOT EXISTS users (
    id_users UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    dt_criado TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(255) UNIQUE,
    nome VARCHAR(255),
    fone VARCHAR(50),
    cpf VARCHAR(20),
    perfil VARCHAR(50),
    apelido VARCHAR(100),
    foto TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    senha TEXT
);

-- 2. Plano
CREATE TABLE IF NOT EXISTS plano (
    id_plano UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nome VARCHAR(100),
    valor DECIMAL(10,2),
    descricao TEXT
);

-- 3. Loja
CREATE TABLE IF NOT EXISTS loja (
    id_loja UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    dt_criado TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    nome VARCHAR(255),
    cnpj VARCHAR(255),
    fone VARCHAR(255),
    email VARCHAR(255),
    logo TEXT,
    url TEXT,
    descricao TEXT,
    logradouro TEXT,
    num VARCHAR(50),
    complemento TEXT,
    bairro TEXT,
    cidade TEXT,
    estado TEXT,
    uf VARCHAR(2),
    instagram TEXT,
    users UUID REFERENCES users(id_users),
    cep VARCHAR(20),
    vencimento_plano TIMESTAMP WITH TIME ZONE,
    status BOOLEAN DEFAULT TRUE,
    plano UUID REFERENCES plano(id_plano),
    responsavel TEXT,
    cor VARCHAR(7)
);

-- 4. Categoria
CREATE TABLE IF NOT EXISTS categoria (
    id_categoria UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nome VARCHAR(100),
    loja UUID REFERENCES loja(id_loja)
);

-- 5. Cliente
CREATE TABLE IF NOT EXISTS cliente (
    id_cliente UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    dt_criado TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    nome VARCHAR(255),
    loja UUID REFERENCES loja(id_loja),
    email VARCHAR(255),
    fone VARCHAR(50),
    cep VARCHAR(20),
    logradouro TEXT,
    num VARCHAR(50),
    complemento TEXT,
    bairro TEXT,
    cidade TEXT,
    estado TEXT,
    uf VARCHAR(2),
    cpf_cnpj VARCHAR(50),
    obs TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    foto TEXT,
    perfil VARCHAR(100)
);

-- 6. Produto
CREATE TABLE IF NOT EXISTS produto (
    id_produto UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    dt_criado TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    nome VARCHAR(255),
    loja UUID REFERENCES loja(id_loja),
    descricao TEXT,
    valor_venda DECIMAL(10,2),
    valor_promocional DECIMAL(10,2),
    valor_custo DECIMAL(10,2),
    ativo BOOLEAN DEFAULT TRUE,
    cod_produto VARCHAR(100),
    foto TEXT,
    categoria UUID REFERENCES categoria(id_categoria),
    agendamento TEXT,
    mostar_valor BOOLEAN DEFAULT TRUE,
    qtd_minima INTEGER DEFAULT 0,
    qtd_atual INTEGER DEFAULT 0
);

-- 7. Orcamento
CREATE TABLE IF NOT EXISTS orcamento (
    id_orcamento UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    numero_sequencial SERIAL,
    dt_criado TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    parceiro UUID REFERENCES users(id_users),
    cliente_nome VARCHAR(255),
    cliente_cpf_cnpj VARCHAR(20),
    cliente_fone VARCHAR(50),
    loja UUID REFERENCES loja(id_loja),
    status VARCHAR(50) DEFAULT 'Pendente',
    validade DATE,
    valor_total DECIMAL(10,2) DEFAULT 0,
    observacoes TEXT,
    desconto DECIMAL(10,2) DEFAULT 0,
    mes VARCHAR(20),
    ano VARCHAR(4),
    data_orcamento TIMESTAMP WITH TIME ZONE,
    data_inicio TIMESTAMP WITH TIME ZONE,
    data_fim TIMESTAMP WITH TIME ZONE
);

-- 8. Orcamento Item
CREATE TABLE IF NOT EXISTS orcamento_item (
    id_item UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    orcamento UUID REFERENCES orcamento(id_orcamento) ON DELETE CASCADE,
    produto UUID REFERENCES produto(id_produto),
    quantidade DECIMAL(10,2),
    valor_unitario DECIMAL(10,2),
    valor_total DECIMAL(10,2),
    data_inicio TIMESTAMP WITH TIME ZONE,
    data_fim TIMESTAMP WITH TIME ZONE
);

-- 9. Pedido
CREATE TABLE IF NOT EXISTS pedido (
    id_pedido UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    dt_criado TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    loja UUID REFERENCES loja(id_loja),
    "user" UUID REFERENCES users(id_users),
    valor_total DECIMAL(10,2),
    status VARCHAR(50) DEFAULT 'Pendente'
);

-- 10. Produto Pedido
CREATE TABLE IF NOT EXISTS produto_pedido (
    id_produto_pedido UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    dt_criado TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    pedido UUID REFERENCES pedido(id_pedido) ON DELETE CASCADE,
    produto UUID REFERENCES produto(id_produto),
    qtd INTEGER,
    valor_unitario DECIMAL(10,2),
    sub_total DECIMAL(10,2)
);

-- 11. Carrinho
CREATE TABLE IF NOT EXISTS carrinho (
    id_carrinho UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    dt_criado TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    user_comprador UUID REFERENCES users(id_users),
    loja UUID REFERENCES loja(id_loja),
    url_loja TEXT
);

-- 12. Produto Carrinho
CREATE TABLE IF NOT EXISTS produto_carrinho (
    id_produto_carrinho UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    dt_criado TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    produto UUID REFERENCES produto(id_produto),
    valor_unitario DECIMAL(10,2),
    qtd INTEGER,
    sub_total DECIMAL(10,2),
    carrinho UUID REFERENCES carrinho(id_carrinho) ON DELETE CASCADE
);

-- 13. Agenda Produto
CREATE TABLE IF NOT EXISTS agenda_produto (
    id_agenda_produto UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    dt_criado TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    data_inicio TIMESTAMP WITH TIME ZONE,
    data_fim TIMESTAMP WITH TIME ZONE,
    status VARCHAR(50),
    produto UUID REFERENCES produto(id_produto),
    obs TEXT,
    orcamento UUID REFERENCES orcamento(id_orcamento)
);

-- Real-time Functions
CREATE OR REPLACE FUNCTION notify_orcamento_change() RETURNS TRIGGER AS $$
BEGIN
    PERFORM pg_notify('orcamento_change', json_build_object(
        'table', TG_TABLE_NAME,
        'action', TG_OP,
        'data', row_to_json(NEW)
    )::text);
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_orcamento_change ON orcamento;
CREATE TRIGGER trg_orcamento_change
AFTER INSERT OR UPDATE OR DELETE ON orcamento
FOR EACH ROW EXECUTE FUNCTION notify_orcamento_change();
