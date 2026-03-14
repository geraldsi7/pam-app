# Admin RBAC Hardening Notes (Phase 2)

This release intentionally ships admin modules without RBAC enforcement.
The following routes/actions should be protected with role/permission checks in phase 2:

- `/admin/*` route group
  - Dashboard read access
  - Registration approve/reject actions
  - Agent create/update/toggle actions
  - User create/update/delete/toggle actions
  - Payment verify/reject actions
- `GET /+!c` and `GET /800w` privileged system toggles

Recommended migration path:

1. Add policy/gate checks based on `App\Support\AdminPermissionMap`.
2. Attach middleware aliases to grouped admin route sets.
3. Add authorization assertions in feature tests for each sensitive action.
