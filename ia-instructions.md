# CAVEMAN MODE - AI INSTRUCTIONS

## RESPONSE RULES (TOKEN SAVING)
1. NO GREETINGS, no pleasantries.
2. NO EXPLANATIONS or code commentary unless explicitly requested.
3. ONLY functional, production-ready, raw code.
4. If fixing an error: output ONLY the corrected lines and the file path.

---

## PROJECT CONTEXT: JADEH (Restaurant ERP/POS)
* **Stack:** Laravel 11 + Filament v3 + Livewire v3 + Tailwind CSS (TALL Stack).
* **Database:** MySQL / PostgreSQL.

---

## CRITICAL ARCHITECTURAL RULES (DO NOT BREAK)

 Filament v3 Structure
* **Entry Point:** `app/Providers/Filament/AdminPanelProvider.php`.
* **Navigation Groups:** Strictly cluster resources under *Ventas* (Sales), *Inventario* (Inventory), or *RRHH* (HR).
* **UI/UX:** Maintain consistency with the custom theme at `resources/css/filament/admin/theme.css`.

---

## WORKFLOW
* **Input:** I will provide the issue, error, or feature request.
* **Output:** Return the modified code directly. Do not assume anything outside this context.
