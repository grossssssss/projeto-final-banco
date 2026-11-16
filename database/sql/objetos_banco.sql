-- ============================================================================
-- 1. ÍNDICES RELEVANTES (Otimização de consultas frequentes)
-- ============================================================================

-- Otimiza buscas, filtros e reajustes em lote por categoria de produto
CREATE INDEX IF NOT EXISTS idx_produtos_categoria 
ON produtos (categoria);

-- Índice composto para relatórios e listagens de pedidos por status e data
CREATE INDEX IF NOT EXISTS idx_pedidos_status_data 
ON pedidos (status, data_pedido);


-- ============================================================================
-- 2. FUNCTIONS (Cálculos e consultas reutilizáveis)
-- ============================================================================

-- Function 1: Calcula o valor total somando os itens de um pedido específico
CREATE OR REPLACE FUNCTION fn_calcular_total_pedido(p_pedido_id BIGINT)
RETURNS NUMERIC(10, 2)
LANGUAGE plpgsql
AS $$
DECLARE
    v_total NUMERIC(10, 2);
BEGIN
    SELECT COALESCE(SUM(quantidade * preco_unitario), 0.00)
    INTO v_total
    FROM pedido_itens
    WHERE pedido_id = p_pedido_id;

    RETURN v_total;
END;
$$;

-- Function 2: Retorna o valor total já gasto por um cliente em pedidos concluídos
CREATE OR REPLACE FUNCTION fn_total_gasto_cliente(p_cliente_id BIGINT)
RETURNS NUMERIC(10, 2)
LANGUAGE plpgsql
AS $$
DECLARE
    v_total_gasto NUMERIC(10, 2);
BEGIN
    SELECT COALESCE(SUM(valor_total), 0.00)
    INTO v_total_gasto
    FROM pedidos
    WHERE cliente_id = p_cliente_id
      AND status = 'concluido';

    RETURN v_total_gasto;
END;
$$;


-- ============================================================================
-- 3. STORED PROCEDURES (Regras de negócio e operações transacionais)
-- ============================================================================

-- Procedure 1: Recalcula o total e finaliza um pedido válido
CREATE OR REPLACE PROCEDURE sp_finalizar_pedido(p_pedido_id BIGINT)
LANGUAGE plpgsql
AS $$
DECLARE
    v_total NUMERIC(10, 2);
BEGIN
    v_total := fn_calcular_total_pedido(p_pedido_id);

    IF v_total <= 0 THEN
        RAISE EXCEPTION 'Não é possível finalizar o pedido % sem itens vinculados.', p_pedido_id;
    END IF;

    UPDATE pedidos
    SET valor_total = v_total,
        status = 'concluido',
        updated_at = NOW()
    WHERE id = p_pedido_id;
END;
$$;

-- Procedure 2: Aplica reajuste percentual nos preços de uma categoria inteira
CREATE OR REPLACE PROCEDURE sp_reajustar_preco_categoria(
    p_categoria VARCHAR,
    p_percentual NUMERIC
)
LANGUAGE plpgsql
AS $$
BEGIN
    UPDATE produtos
    SET preco = ROUND(preco * (1 + (p_percentual / 100.0)), 2),
        updated_at = NOW()
    WHERE categoria = p_categoria;
END;
$$;


-- ============================================================================
-- 4. TRIGGERS (Automação de estoque, totais e auditoria)
-- ============================================================================

-- Trigger 1: Valida estoque, baixa/estorna unidades e atualiza o total do pedido
CREATE OR REPLACE FUNCTION fn_trg_gerenciar_estoque()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_estoque_atual INTEGER;
BEGIN
    IF TG_OP = 'INSERT' THEN
        SELECT estoque INTO v_estoque_atual
        FROM produtos
        WHERE id = NEW.produto_id;

        IF v_estoque_atual < NEW.quantidade THEN
            RAISE EXCEPTION 'Estoque insuficiente para o produto ID % (Disponível: %, Solicitado: %).',
                NEW.produto_id, v_estoque_atual, NEW.quantidade;
        END IF;

        UPDATE produtos
        SET estoque = estoque - NEW.quantidade,
            updated_at = NOW()
        WHERE id = NEW.produto_id;

        UPDATE pedidos
        SET valor_total = fn_calcular_total_pedido(NEW.pedido_id),
            updated_at = NOW()
        WHERE id = NEW.pedido_id;

        RETURN NEW;

    ELSIF TG_OP = 'DELETE' THEN
        UPDATE produtos
        SET estoque = estoque + OLD.quantidade,
            updated_at = NOW()
        WHERE id = OLD.produto_id;

        UPDATE pedidos
        SET valor_total = fn_calcular_total_pedido(OLD.pedido_id),
            updated_at = NOW()
        WHERE id = OLD.pedido_id;

        RETURN OLD;
    END IF;

    RETURN NULL;
END;
$$;

DROP TRIGGER IF EXISTS trg_atualizar_estoque_pedido_item ON pedido_itens;
CREATE TRIGGER trg_atualizar_estoque_pedido_item
AFTER INSERT OR DELETE ON pedido_itens
FOR EACH ROW
EXECUTE FUNCTION fn_trg_gerenciar_estoque();


-- Trigger 2: Registra histórico na tabela auditoria_precos ao alterar valor de produto
CREATE OR REPLACE FUNCTION fn_trg_auditar_preco()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF NEW.preco IS DISTINCT FROM OLD.preco THEN
        INSERT INTO auditoria_precos (produto_id, preco_antigo, preco_novo, alterado_em)
        VALUES (OLD.id, OLD.preco, NEW.preco, NOW());
    END IF;

    RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS trg_auditar_preco_produto ON produtos;
CREATE TRIGGER trg_auditar_preco_produto
AFTER UPDATE OF preco ON produtos
FOR EACH ROW
EXECUTE FUNCTION fn_trg_auditar_preco();