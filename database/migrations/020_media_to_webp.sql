-- ============================================================
-- Migration 020 — Apontar caminhos de imagens na BD para .webp
-- Idempotente (só altera valores que ainda terminam em jpg/jpeg/png).
-- Requer que os ficheiros .webp existam (gerados a partir dos originais).
-- ============================================================

SET NAMES utf8mb4;

UPDATE media
   SET file_path = REGEXP_REPLACE(file_path, '\\.(jpe?g|png)$', '.webp')
 WHERE file_path REGEXP '\\.(jpe?g|png)$';

UPDATE accommodation
   SET hero_image = REGEXP_REPLACE(hero_image, '\\.(jpe?g|png)$', '.webp')
 WHERE hero_image REGEXP '\\.(jpe?g|png)$';

UPDATE accommodation
   SET cover_image = REGEXP_REPLACE(cover_image, '\\.(jpe?g|png)$', '.webp')
 WHERE cover_image REGEXP '\\.(jpe?g|png)$';

UPDATE content_blocks
   SET content = REGEXP_REPLACE(content, '\\.(jpe?g|png)$', '.webp')
 WHERE content REGEXP '\\.(jpe?g|png)$';
-- Nota: product_images referencia a tabela media (media_id), já coberto acima.
