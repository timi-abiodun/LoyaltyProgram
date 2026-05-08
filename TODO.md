# TODO - Front-end overhaul

## Goal
Fix the “terrible” front-end by unifying the standalone HTML with the Vite-bundled JS.

## Steps
- [ ] 1) Refactor `public/loyalty_program_dashboard.html` to remove inline `<script>` and load `resources/js/dashboard.js`.

- [ ] 2) Update `resources/js/dashboard.js` to use the HTML’s existing element IDs (`l_user`, `l_pass`, `r_*`, `p_amount`, `toast`, `dashboardView`, etc.).

- [ ] 3) Wire UI events with `addEventListener` and remove reliance on inline `onclick`.

- [ ] 4) Validate API payloads and DOM updates (login/register/purchase/sync/logout).

- [ ] 5) Run `npm run dev` and manually test the dashboard flow.


