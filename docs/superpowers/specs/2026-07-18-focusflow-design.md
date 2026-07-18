# FocusFlow — Design Spec

**Date:** 2026-07-18
**Status:** Approved by user (theme: Green "FocusFlow" variant; frontend: Vue 3; hosting: Laravel Cloud)
**Source design:** Google Stitch project 18052912391671186351 ("Timed Task Tracker"), Green screen set. Assets: [assets/stitch/](assets/stitch/)

## What it is

A Pomodoro-style focus/task web app ("Focus Workspace"). Users create tasks with a planned session
duration, run focus sessions against them with a fullscreen countdown timer, and review their
productivity history (deep-work hours, focus score, streaks). Multi-user with accounts; each user
sees only their own data.

## Stack

- Laravel 12 + Inertia v2 + Vue 3 (official Laravel Vue starter kit: auth, Tailwind CSS v4, Vite, TypeScript)
- Database: SQLite locally, Laravel Cloud managed database in production
- Hosting: Laravel Cloud, deployed from the first scaffold onward
- Tests: Pest feature tests

## Design system ("Organic Minimalism")

Tokens live in [assets/stitch/design-system.md](assets/stitch/design-system.md); each page's
reference implementation is the matching `.html` file (Tailwind-based, translate into Vue components).

- Colors: forest green primary `#012d1d` / `#1b4332`, mint secondary `#0e6c4a` / container `#a0f4c8`,
  warm gray surfaces `#f8f9fa`–`#e1e3e4`, charcoal text `#191c1d`
- Fonts (Google Fonts): Manrope (headlines), Inter (body), JetBrains Mono (labels/timers/metadata)
- Icons: Material Symbols Outlined
- Elevation: tonal layers + soft shadows; modals use backdrop blur
- Radius scale: 0.25/0.5/0.75/1/1.5rem; spacing on an 8px base

## Pages

| Page | Route | Contents |
|---|---|---|
| Dashboard | `/dashboard` | Sidebar nav (Tasks, Categories dropdown-only, Timer Settings), greeting + date, session timer card (planned duration of selected task, Start/Pause), Daily Progress card (tasks completed X/N, deep-work hours, focus score %), Today's Tasks list (checkbox, title, description, priority chip), Add-a-new-task, filter | 
| Create Task | modal on Dashboard | Task Name, Due Date, Category (dropdown with inline create), Priority (High/Medium/Low — added; chips exist in designs but the Stitch modal lacked a source field), Session Duration (minutes), toggles: notify-when-timer-ends, 5-minute-warning, Description (optional) |
| Focus Mode | `/focus/{task}` | Fullscreen single-column view, circular countdown ring, task card (category chip, title, description), Pause / Complete buttons, Exit Focus Mode link, top warning banner in final minute |
| Task History | `/history` | Stat cards: completed this week (+% vs prior week), total focus time, longest streak (days); search; filters (category, date range, priority); completed list rows (strikethrough title, priority, category, completed-at timestamp, focused duration chip); Load More pagination. Export/Archive All buttons deferred (visible in design; out of scope v1) |
| Settings | `/settings` | Session Durations sliders: Focus Block (default 25m), Short Break (5m), Long Break (15m); Sound Design: focus-complete tone, break-complete tone; Permissions: browser notifications toggle, Zen Mode (mute all sounds) toggle; Save/Discard |
| Auth | `/login`, `/register`, etc. | Starter kit pages restyled with the theme |

## Data model

All tables scoped to `user_id`; cascade on user delete.

- **tasks**: `title`, `description?`, `due_at?`, `priority` (enum high/medium/low), `session_minutes`
  (planned focus duration), `notify_on_end` (bool), `five_minute_warning` (bool),
  `category_id?`, `status` (pending/completed), `completed_at?`
- **categories**: `name` (unique per user). Created inline from the task modal dropdown; no dedicated page.
- **focus_sessions**: `task_id`, `started_at`, `ended_at?`, `duration_seconds`, `completed` (bool —
  ran to the end vs abandoned). Source of truth for all stats.
- **user_preferences** (1:1 with users): `focus_minutes`, `short_break_minutes`, `long_break_minutes`,
  `focus_tone`, `break_tone`, `browser_notifications` (bool), `zen_mode` (bool)

## Behavior

- **Timer**: countdown runs client-side (Vue composable). Server records the session on start and on
  finish/abandon (POST/PATCH to focus-session endpoints), so stats survive refresh. Completing a
  session from Focus Mode can also mark the task completed ("Complete" button does both).
- **Stats** (computed server-side from `focus_sessions` + `tasks`):
  - Daily Progress: tasks completed today / tasks due-or-created today; deep-work hours today;
    focus score = completed sessions ÷ started sessions today (%)
  - History: completed count this week and % change vs previous week; total focus time (all time);
    longest streak = max run of consecutive days with ≥1 completed session
- **Notifications/sounds**: browser Notification API + Web Audio chimes at session end and 5-minute
  warning, honoring per-task toggles and user preferences; Zen Mode silences sounds.
- **Errors**: standard Laravel validation surfaced through Inertia form helpers; timer state guards
  against double-starting a session; abandoning a session (leaving Focus Mode) closes it as incomplete.

## Testing

Pest feature tests: auth-scoped task CRUD, category inline creation, focus-session start/complete/abandon
lifecycle, preference updates, and each stats calculation (fixed clock via `travel()`).

## Deployment

Scaffold is deployed to Laravel Cloud before feature work begins; every subsequent milestone is
deployed so the live URL always reflects progress. Existing stray files in the repo root
(empty `index.php`, `.qodo/`) are removed when the Laravel app is scaffolded.

## Out of scope (v1)

- History Export and Archive All buttons
- Dedicated category management page
- Breaks as tracked sessions (breaks are timer-only, not persisted)
- The Lumina Focus / Chromatic Focus themes and any light/dark toggle
