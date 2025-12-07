# Changelog

## v2.0.0 — 2025-12-07
### Added
- Hamburger menu added to header with dropdown navigation.
- Clickable logo added (now links to homepage).

- **API Token System Enhancements**
  - Token scopes (read, write, export, admin)
  - Token expiry (datetime-based)
  - Token regeneration
  - Token deletion with confirmation
  - Enable/disable token toggle
  - Token masking in UI

- **API Logging Framework**
  - Logs endpoint usage (token, IP, endpoint, user agent)
  - Browser & OS parsing
  - Per-token log viewer
  - API Analytics Dashboard:
    - Daily request trends
    - Top endpoints
    - Top tokens
    - Top IP addresses

### Changed
- Header styling updated for enterprise look and feel.
- Improved dropdown behavior and button interactions.
- Updated card components (malware list UI) to match dark-glass enterprise theme.
- Removed theme toggle (temporary).
- Removed outdated / unused backend logic for related APT computation.
- Enhanced Malware Admin Dashboard UI (enterprise styling).
- Enhanced Malware Editor UI, redesigned form layout, consistent spacing, clearer labels.
- Enhanced Threat Tools Manager UI.
- Bulk CSV import UI improvements.

### Fixed
- Cleaned up layout inconsistencies in APT Edit form.
- Fixed spacing issues in admin tables.
- Fixed missing navigation links + hover states.
- Removed deprecated Three.js script loading to prevent console warnings.
- Ensured all pages load correct Tailwind build without CDN warnings.

## v1.4.0 — 2025-12-05
### Added
- New responsive **hamburger menu** in the header.
- Dropdown navigation panel accessible on mobile + desktop.
- Header branding updated — **IntelCTX logo now links to homepage**.

### Changed
- Footer redesigned to **full-width layout** for cleaner UX.
- Minor UI polish across header elements and spacing.

### Removed
- Deprecated backend logic for the old "related APT" system.
- Removed the incomplete dark/light theme toggle (will reintroduce later).

### Fixed
- Cleaned up layout inconsistencies caused by earlier UI patches.

---

