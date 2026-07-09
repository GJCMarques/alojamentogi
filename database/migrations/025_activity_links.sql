-- Migration 025 — Tabela de links informativos da página de Atividades.
-- A página de Atividades deixou de usar fotos; passa a mostrar links com texto
-- (recursos oficiais + guias), agora geríveis no admin. Idempotente.

CREATE TABLE IF NOT EXISTS activity_links (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    section VARCHAR(20) NOT NULL DEFAULT 'official',
    tag_pt VARCHAR(60) DEFAULT NULL,
    tag_en VARCHAR(60) DEFAULT NULL,
    title_pt VARCHAR(190) NOT NULL,
    title_en VARCHAR(190) NOT NULL,
    desc_pt TEXT DEFAULT NULL,
    desc_en TEXT DEFAULT NULL,
    url VARCHAR(500) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Semear apenas se a tabela estiver vazia (idempotente).
INSERT INTO activity_links (section, tag_pt, tag_en, title_pt, title_en, desc_pt, desc_en, url, sort_order, is_active)
SELECT * FROM (
    SELECT 'official' AS section, 'Site Oficial' AS tag_pt, 'Official Site' AS tag_en,
           'Câmara Municipal de Mogadouro' AS title_pt, 'Mogadouro Town Council' AS title_en,
           'Informação oficial do concelho: o que visitar, património, eventos e contactos.' AS desc_pt,
           'Official municipal information: what to visit, heritage, events and contacts.' AS desc_en,
           'https://www.mogadouro.pt/' AS url, 1 AS sort_order, 1 AS is_active
    UNION ALL SELECT 'official', 'Turismo', 'Tourism',
           'Posto de Turismo de Mogadouro', 'Mogadouro Tourism Office',
           'Loja Interativa de Turismo — pontos de interesse, percursos e apoio ao visitante.',
           'Interactive Tourism Office — points of interest, trails and visitor support.',
           'https://www.mogadouro.pt/pages/17', 2, 1
    UNION ALL SELECT 'guide', 'Guia', 'Guide',
           'Roteiro por Mogadouro — Vagamundos', 'Mogadouro itinerary — Vagamundos', NULL, NULL,
           'https://www.vagamundos.pt/visitar-mogadouro-roteiro/', 3, 1
    UNION ALL SELECT 'guide', 'Guia', 'Guide',
           'Atrações em torno de Mogadouro — Komoot', 'Attractions around Mogadouro — Komoot', NULL, NULL,
           'https://www.komoot.com/pt-pt/guide/900754/atracoes-em-torno-de-mogadouro', 4, 1
    UNION ALL SELECT 'guide', 'Guia', 'Guide',
           'Mogadouro — Tripadvisor', 'Mogadouro — Tripadvisor', NULL, NULL,
           'https://www.tripadvisor.pt/Attractions-g1458520-Activities-Mogadouro_Braganca_District_Northern_Portugal.html', 5, 1
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM activity_links LIMIT 1);
