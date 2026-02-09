# SEO Files Implementation - A Casa do Gi

This document describes the SEO files created for the A Casa do Gi website.

## Files Created

### 1. robots.txt
**Location:** `c:\xampp\htdocs\alojamentogi\robots.txt`

**Purpose:** Instructs search engine crawlers which pages to index and which to avoid.

**Key Features:**
- Allows crawling of all public pages
- Blocks admin, API, config, core, models, includes, templates, database, logs, and vendor directories
- Blocks duplicate content from query parameters (`?pesquisa=` and `?pagina=`)
- References the sitemap location

**URL:** `http://localhost/alojamentogi/robots.txt`

---

### 2. sitemap.php (Dynamic Sitemap Generator)
**Location:** `c:\xampp\htdocs\alojamentogi\sitemap.php`

**Purpose:** Generates a dynamic XML sitemap that search engines use to discover and index pages.

**Key Features:**
- **Static Pages (Portuguese):**
  - Homepage (priority: 1.0, daily updates)
  - Alojamento (priority: 0.8, weekly)
  - Loja (priority: 0.8, daily)
  - Atividades (priority: 0.8, weekly)
  - Contactos (priority: 0.6, monthly)
  - Termos e Condições (priority: 0.3, yearly)
  - Política de Privacidade (priority: 0.3, yearly)

- **Static Pages (English):**
  - All English versions with slightly lower priorities (0.5-0.9)

- **Dynamic Product Pages:**
  - Queries database for active products (`products` table where `is_active = 1`)
  - Generates URLs: `/loja/produto/?slug=X` (PT) and `/en/shop/product/?slug=X` (EN)
  - Priority: 0.7 (PT), 0.6 (EN)
  - Includes last modification date from database

- **Dynamic Activity Pages:**
  - Queries database for active activities (`activities` table where `is_active = 1`)
  - Generates URLs: `/atividades/?slug=X` (PT) and `/en/activities/?slug=X` (EN)
  - Priority: 0.7 (PT), 0.6 (EN)
  - Includes last modification date from database

**Technical Details:**
- Outputs proper XML content type header
- Uses site URL from config file
- Error handling with logging
- Follows sitemap protocol standards

**Access:**
- Direct: `http://localhost/alojamentogi/sitemap.php`
- Via rewrite: `http://localhost/alojamentogi/sitemap.xml` (recommended)

---

### 3. .htaccess Update
**Location:** `c:\xampp\htdocs\alojamentogi\.htaccess`

**Added Rule:**
```apache
# Dynamic sitemap
RewriteRule ^sitemap\.xml$ sitemap.php [L]
```

**Purpose:** Makes `sitemap.xml` automatically execute `sitemap.php` for clean SEO-friendly URLs.

---

### 4. humans.txt
**Location:** `c:\xampp\htdocs\alojamentogi\humans.txt`

**Purpose:** A human-readable file that provides information about the team and technologies behind the website.

**Contents:**
- Team information (location, contact)
- Site technologies (HTML5, CSS3, PHP 8, Tailwind CSS, MySQL)
- Software stack (Apache, XAMPP)
- Languages supported (Portuguese, English)

**URL:** `http://localhost/alojamentogi/humans.txt`

---

### 5. security.txt
**Location:** `c:\xampp\htdocs\alojamentogi\.well-known\security.txt`

**Purpose:** Provides security researchers with contact information for responsible disclosure of security vulnerabilities.

**Key Features:**
- Contact email: `info@acasadogi.pt`
- Preferred languages: Portuguese and English
- Expiration date: 2026-12-31

**URL:** `http://localhost/alojamentogi/.well-known/security.txt`

**Standard:** Follows RFC 9116 (security.txt standard)

---

## Testing the Implementation

### 1. Test robots.txt
Visit: `http://localhost/alojamentogi/robots.txt`

Expected: Plain text file with robot instructions

### 2. Test sitemap.xml
Visit: `http://localhost/alojamentogi/sitemap.xml`

Expected: XML file listing all pages with priorities and update frequencies

### 3. Test humans.txt
Visit: `http://localhost/alojamentogi/humans.txt`

Expected: Plain text file with team and site information

### 4. Test security.txt
Visit: `http://localhost/alojamentogi/.well-known/security.txt`

Expected: Plain text file with security contact information

---

## Production Deployment Checklist

When moving to production, update the following:

### robots.txt
- Change sitemap URL from `http://localhost/alojamentogi/sitemap.xml` to production URL
- Example: `https://www.acasadogi.pt/sitemap.xml`

### sitemap.php
- No changes needed (reads from config automatically)

### config.php
- Update `app.url` setting to production URL
- Example: `'url' => 'https://www.acasadogi.pt'`

### security.txt
- Update contact email if different in production
- Update expiration date before it expires

---

## SEO Benefits

1. **robots.txt:** Controls crawler access and prevents indexing of sensitive areas
2. **sitemap.xml:** Helps search engines discover and index all pages efficiently
3. **humans.txt:** Adds a human touch and transparency about the team
4. **security.txt:** Demonstrates security awareness and provides disclosure channel

---

## Maintenance

### Sitemap
The sitemap is **dynamic** and automatically updates when:
- New products are added to the database
- New activities are added to the database
- Products/activities are activated or deactivated
- Product/activity content is updated (lastmod date changes)

No manual updates needed!

### robots.txt
Review and update if:
- New directories need to be blocked
- New query parameters create duplicate content
- Site structure changes significantly

### security.txt
- Update before expiration date (currently set to 2026-12-31)
- Update if contact information changes

---

## Files Summary

| File | Purpose | Update Frequency | Dynamic |
|------|---------|------------------|---------|
| robots.txt | Crawler instructions | Manual | No |
| sitemap.php | Page discovery | Automatic | Yes |
| humans.txt | Team information | Manual | No |
| security.txt | Security contact | Yearly | No |

---

## Technical Implementation

### Database Tables Used
- `products` (columns: id, slug, is_active, created_at, updated_at)
- `activities` (columns: id, slug, is_active, created_at, updated_at, sort_order)

### Dependencies
- PHP 8.0+
- MySQL/MariaDB
- Apache mod_rewrite
- Application init system (`includes/init.php`)
- Configuration file (`config/config.php`)

---

## Support

For questions or issues with SEO files implementation:
- Contact: info@acasadogi.pt
- Review application logs: `logs/php-errors.log`

---

Generated: 2025-02-09
Version: 1.0
