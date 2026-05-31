---
alwaysApply: true
---

# Full-Stack Development Workflow Skill

## Purpose

When given a coding prompt, the AI must follow this **6-phase workflow** in order:
1. **Plan** — Analyze and create a structured plan
2. **Code** — Implement the code
3. **Review** — Self-review against requirements
4. **Fix** — Address any issues found
5. **Test** — Run and verify with live UI/browser
6. **Iterate** — Fix any bugs found during testing, repeat until stable

---

## Phase 1: PLAN

### Steps

1. **Understand the request**
   - Identify what needs to be built
   - Clarify any ambiguous parts before coding
   - Determine the scope: front-end, back-end, or full-stack

2. **Explore existing codebase** (if applicable)
   - Read relevant files (routes, models, controllers, existing components)
   - Understand data models and API contracts
   - Check existing patterns, conventions, and architecture

3. **Create a structured plan**
   - Break the task into clear, ordered steps
   - Identify dependencies (what must be done before something else)
   - List files to create and modify
   - Use a numbered list for the implementation order

4. **Present the plan** to the user
   - State what will be built
   - List the key steps
   - Ask for confirmation if the scope is large or ambiguous

### When to Ask Before Proceeding
- The request is ambiguous or lacks detail
- There are multiple valid approaches
- The request might break existing functionality
- The task is large (3+ files or complex logic)

---

## Phase 2: CODE

### Principles

1. **Follow existing project conventions** — Match the style of surrounding code
2. **Use Clean Architecture** where applicable — Separate concerns, keep business logic from infrastructure
3. **Type everything** — Use TypeScript types, PHP type hints, Laravel DTOs
4. **No magic strings** — Use enums, constants, typed arrays
5. **Meaningful names** — Variables and functions should be self-documenting
6. **Small, focused functions** — Each function does one thing well
7. **No dead code** — Remove unused imports, variables, and commented-out blocks

### File Operations
- Use dedicated tools (Read, Write, StrReplace, Delete) — not terminal commands for file content
- Always **read before editing** existing files
- Create a `README.md` or `DEPENDENCIES.md` for new projects with setup instructions
- Use `Glob` to find files when the path is uncertain

### Code Quality Checklist Before Moving On
- [ ] All imports resolved (no "cannot find module" errors)
- [ ] TypeScript/PHP types are correct
- [ ] No unused variables or imports
- [ ] No commented-out code left behind
- [ ] Error cases are handled
- [ ] UI matches the project's visual language

---

## Phase 3: REVIEW

### Self-Review Checklist

For each file created or modified, verify:

1. **Correctness** — Does the code do what it should?
2. **Completeness** — Are all required features implemented?
3. **Type safety** — Are all inputs/outputs properly typed?
4. **Error handling** — Are API errors, null values, and edge cases handled?
5. **Security** — No hardcoded secrets, proper auth checks, input validation
6. **Performance** — No N+1 queries, unnecessary re-renders, or heavy operations in hot paths
7. **i18n** — UI text uses translation keys (not hardcoded strings) in multi-language apps
8. **Accessibility** — Form inputs have labels, buttons have text, proper semantic HTML

### Compare Against Requirements
- Re-read the original request
- Check each requirement is addressed
- Note any gaps or partial implementations

---

## Phase 4: FIX

- Fix all issues identified in the Review phase
- Run the linter to catch any issues: `ReadLints` or `npm run build` / `php artisan`
- Fix TypeScript errors by running `tsc --noEmit` or build commands
- Fix any linter warnings that indicate real problems

---

## Phase 5: TEST

### Prerequisites

This project has **Playwright MCP** configured in `.cursor/mcp.json`. This gives the AI full browser automation capabilities — no user manual testing needed for most cases.

### Testing Strategy

#### Back-End (Laravel)
1. **Start the dev server** if not running:
   ```bash
   php artisan serve
   ```
2. **Test API endpoints** using HTTP requests:
   ```bash
   curl -X POST http://localhost:8000/api/login \
     -H "Content-Type: application/json" \
     -d '{"email":"user@test.com","password":"password"}'
   ```
3. **Test database operations** using the DatabaseService:
   ```bash
   php artisan db:query "SELECT * FROM users"
   php artisan db:count users
   ```
4. **Check logs** for errors:
   ```bash
   tail -f storage/logs/laravel.log
   ```

#### Front-End (React)
1. **Start the dev server** if not running:
   ```bash
   npm run dev
   ```
2. **Open the browser** at the dev URL:
   ```bash
   start http://localhost:5173
   ```

#### Playwright MCP Browser Automation (Full Cycle)

This project has Playwright MCP configured. In the TEST phase, the AI should use these tools to perform **automated browser testing**:

| MCP Tool | What to do |
|----------|-----------|
| `browser_navigate` | Open `http://localhost:5173` |
| `browser_get_content` | Read page content to verify elements |
| `browser_fill` | Fill login forms, search inputs |
| `browser_click` | Click buttons, links, filters |
| `browser_screenshot` | Capture screenshots at each step |
| `browser_console_logs` | Check for JS errors |
| `browser_evaluate` | Run JS to verify DOM state |
| `browser_wait_for` | Wait for elements to appear |

**Example automated test flow**:
1. `browser_navigate("http://localhost:5173/login")`
2. `browser_fill("input[name=email]", "test@test.com")`
3. `browser_fill("input[name=password]", "password")`
4. `browser_click("button[type=submit]")`
5. `browser_wait_for("text=Dashboard")`
6. `browser_screenshot("login-success.png")`
7. `browser_console_logs` — verify no errors

**Important**: Always start both dev servers before browser testing:
- Back-end: `php artisan serve` (port 8000)
- Front-end: `npm run dev` (port 5173)

### Running Build Verification
```bash
# Front-end build
npm run build

# Back-end
php artisan route:list
php artisan migrate --pretend
```

---

## Phase 6: ITERATE

### Bug Fixing Loop

When the user reports a bug during testing:

1. **Understand** — Ask the user for the exact steps to reproduce the bug
2. **Locate** — Explore the codebase to find the source of the bug
3. **Fix** — Apply the minimal fix needed
4. **Verify** — Run `npm run build` or `tsc --noEmit` to confirm no new errors
5. **Browser test** — Use Playwright MCP to verify the fix in the browser:
   - Navigate to the affected page
   - Reproduce the original scenario
   - Confirm the bug is fixed with screenshot
6. **Report** — Tell the user exactly what was changed and ask them to retest
7. **Repeat** — If still broken, gather more info and iterate

### When to Stop Iterating
- All TypeScript/build errors are resolved
- The code compiles cleanly
- The user confirms the feature works as expected
- You have exhausted the AI's verification capabilities (code review + build check)

### Communication During Iteration
- Report each bug found and the fix applied
- Ask the user to confirm the fix works on their end
- Never assume — verify with the user

---

## Workflow Summary

```
┌──────────┐    ┌──────────┐    ┌──────────┐
│   PLAN   │───▶│   CODE   │───▶│  REVIEW  │
└──────────┘    └──────────┘    └────┬─────┘
                                      │
                               ┌──────▼──────┐
                               │    FIX       │
                               └──────┬──────┘
                                      │
                               ┌──────▼──────┐
                               │    TEST      │
                               │ (Build +     │
                               │  API tests,  │
                               │  user tests  │
                               │  browser)    │
                               └──────┬──────┘
                                      │
                         Bug found?    │ No bugs
                         ┌────────────┴────────────┐
                         ▼                         ▼
                   ┌──────────┐            ┌──────────┐
                   │ ITERATE  │            │ COMPLETE │
                   │  (Fix)   │───────────▶│          │
                   └──────────┘            └──────────┘
```

**Note**: The AI uses Playwright MCP for automated browser testing (navigate, click, fill, screenshot, console logs). Backend verification via curl/build commands still applies.

---

## Project-Specific Rules

### This Project

- **Back-end**: Laravel 12, PHP 8.2+, Clean Architecture + DDD
- **Front-end**: React 19, Vite, TypeScript, Tailwind CSS v4
- **Auth**: JWT tokens (60-min access, 14-day refresh)
- **API base URL**: `http://localhost:8000/api`
- **Front-end dev server**: `http://localhost:5173`
- **Database**: SQLite (development)

### Important Conventions

1. **API Response Format** — Always return `{ success, message, data }`
2. **Auth Header** — `Authorization: Bearer <token>`
3. **VNPT Office Integration** — Credentials passed in request body for auto-relogin
4. **i18n** — Support English (`en`) and Vietnamese (`vi`)
5. **No WebSocket** — This project uses stateless REST only
