-- Migration 026 — Remover da biblioteca de Media as fotografias que já não são
-- usadas: fotos de atividades (a página de Atividades passou a usar links) e
-- imagens de produtos da antiga loja. Idempotente.
-- Nota: apaga apenas os registos na tabela `media`; os ficheiros físicos em
-- uploads/ podem ser removidos manualmente se desejado.

DELETE FROM media WHERE entity_type = 'activity';
DELETE FROM media WHERE category = 'products';
