-- Remove legacy content blocks for Terms and Privacy
DELETE FROM content_blocks WHERE block_key IN ('terms_content', 'privacy_content');
