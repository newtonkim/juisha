# FocusFlow Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build FocusFlow, a Pomodoro-style focus/task app (Laravel 12 + Inertia v2 + Vue 3) matching the Green Stitch design, deployed to Laravel Cloud from the first scaffold.

**Architecture:** Official Laravel Vue starter kit provides auth, Inertia, Tailwind v4, and TypeScript. Four domain tables (`categories`, `tasks`, `focus_sessions`, `user_preferences`) all scoped by `user_id`. Countdown runs client-side in a Vue composable; the server records focus sessions at start/finish so all stats are computed server-side in a `FocusStats` service.

**Tech Stack:** PHP 8.3, Laravel 12, Inertia v2, Vue 3 + TypeScript, Tailwind CSS v4, Pest, SQLite (local) / Laravel Cloud DB (prod).

## Global Constraints

- Spec: `docs/superpowers/specs/2026-07-18-focusflow-design.md`. Design reference per page: `docs/superpowers/specs/assets/stitch/{dashboard,create-task,focus-mode,task-history,settings}.html` (+ `.png` screenshots, `design-system.md` tokens).
- App display name: **FocusFlow**. Sidebar brand text: "Focus Workspace" / "Deep Work Session".
- Fonts: Manrope (headlines), Inter (body), JetBrains Mono (labels/timers). Icons: Material Symbols Outlined.
- Core colors (verbatim from design-system.md): primary `#012d1d`, primary-container `#1b4332`, on-primary-container `#86af99`, secondary `#0e6c4a`, secondary-container `#a0f4c8`, background/surface `#f8f9fa`, surface-container-lowest `#ffffff`, on-surface `#191c1d`, on-surface-variant `#414844`, outline `#717973`, outline-variant `#c1c8c2`, error `#ba1a1a`, error-container `#ffdad6`.
- Priorities enum: `high|medium|low` (default `medium`). Task status enum: `pending|completed`.
- Preference defaults: focus 25m (range 15–90), short break 5m (1–15), long break 15m (10–45), focus tone `soft-bell`, break tone `upbeat-chime`, browser notifications on, zen mode off.
- Tones allowed: focus `soft-bell|deep-gong|digital-ping`; break `upbeat-chime|gentle-marimba|forest-birds`.
- All feature tests are Pest, run with `php artisan test`. Frontend must pass `npm run build`.
- No npm/composer dependencies beyond what the starter kit ships.
- Commit after every task (steps show exact commands). Do not push until Task 9.

---

### Task 1: Scaffold Laravel 12 + Vue starter kit into the existing repo

The repo root already contains `.git/`, `docs/`, an empty `index.php`, and `.qodo/`. The Laravel installer needs an empty directory, so scaffold in a temp dir and move the app in.

**Files:**
- Create: entire Laravel app at repo root (via installer)
- Delete: `index.php`, `.qodo/`

**Interfaces:**
- Produces: working Laravel app with auth routes (`/login`, `/register`, `/dashboard`), Pest test suite, `npm run build` pipeline. All later tasks live inside this app.

- [ ] **Step 1: Update installer and scaffold in temp dir**

```bash
composer global update laravel/installer --quiet
cd /private/tmp && rm -rf focusflow-scaffold && laravel new focusflow-scaffold --vue --pest --no-interaction --quiet
```

Expected: `focusflow-scaffold/` contains `artisan`, `resources/js/pages/Dashboard.vue`, `tests/Pest.php`. If the installer lacks `--vue`, use `laravel new focusflow-scaffold --using=laravel/vue-starter-kit --pest --no-interaction`.

- [ ] **Step 2: Move the app into the repo root**

```bash
cd /Users/asd/PlayGround/julisha
rm index.php && rm -rf .qodo
rsync -a --exclude=.git /private/tmp/focusflow-scaffold/ ./
rm -rf /private/tmp/focusflow-scaffold
```

Note: the scaffold's `.gitignore` lands at repo root; `docs/` is untouched.

- [ ] **Step 3: Set app name and install JS deps**

In `.env` and `.env.example`, set `APP_NAME=FocusFlow`. Then:

```bash
npm install --no-audit --no-fund
```

- [ ] **Step 4: Verify the scaffold works**

```bash
php artisan test
npm run build
```

Expected: all starter-kit tests PASS (auth, profile, dashboard); Vite build succeeds.

- [ ] **Step 5: Commit**

```bash
git add -A && git commit -m "feat: scaffold Laravel 12 Vue starter kit (FocusFlow)"
```

---

### Task 2: Design tokens, fonts, and WorkspaceLayout shell

**Files:**
- Modify: `resources/css/app.css` (add `@theme` tokens + shadcn variable overrides)
- Modify: `resources/views/app.blade.php` (Google Fonts + Material Symbols links)
- Create: `resources/js/layouts/WorkspaceLayout.vue`
- Create: `resources/js/components/focusflow/MaterialIcon.vue`

**Interfaces:**
- Produces: Tailwind utility classes `bg-surface`, `bg-surface-container`, `bg-surface-container-lowest`, `text-on-surface`, `text-on-surface-variant`, `bg-primary-fx`, `text-on-primary-fx`, `bg-secondary-container-fx`, `border-outline-variant`, `text-error`, `bg-error-container`, `font-display`, `font-mono`.
  (`-fx` suffix avoids colliding with the starter kit's existing shadcn `--color-primary` tokens.)
- Produces: `<WorkspaceLayout title="...">` with named slot `#default`, sidebar nav (Tasks → `/dashboard`, Timer Settings → `/settings/timer`, History → `/history`), "New Task" button linking to `/dashboard?new=1`, Help/Logout footer, top bar with greeting date.
- Produces: `<MaterialIcon name="timer" :fill="true" class="..."/>`.

- [ ] **Step 1: Add fonts to `resources/views/app.blade.php`** inside `<head>`:

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@400;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
```

- [ ] **Step 2: Add tokens to `resources/css/app.css`** (after existing imports, keep starter-kit content):

```css
@theme {
  --font-display: 'Manrope', ui-sans-serif, system-ui, sans-serif;
  --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;
  --font-mono: 'JetBrains Mono', ui-monospace, monospace;

  --color-surface: #f8f9fa;
  --color-surface-container-lowest: #ffffff;
  --color-surface-container-low: #f3f4f5;
  --color-surface-container: #edeeef;
  --color-surface-container-high: #e7e8e9;
  --color-on-surface: #191c1d;
  --color-on-surface-variant: #414844;
  --color-outline: #717973;
  --color-outline-variant: #c1c8c2;
  --color-primary-fx: #012d1d;
  --color-primary-container-fx: #1b4332;
  --color-on-primary-fx: #ffffff;
  --color-on-primary-container-fx: #86af99;
  --color-secondary-fx: #0e6c4a;
  --color-secondary-container-fx: #a0f4c8;
  --color-on-secondary-container-fx: #19724f;
  --color-error: #ba1a1a;
  --color-error-container: #ffdad6;
  --color-on-error-container: #93000a;
}
.material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
.material-symbols-outlined.is-filled { font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
```

Also retheme the starter kit's shadcn variables so its buttons/inputs (auth pages included) go green — in the existing `:root { ... }` block replace the `--primary`, `--ring` values with `#1b4332` equivalents (starter kit uses oklch; `oklch(0.32 0.055 165)` ≈ `#1b4332`) and `--primary-foreground` with white.

- [ ] **Step 3: Create `resources/js/components/focusflow/MaterialIcon.vue`**

```vue
<script setup lang="ts">
defineProps<{ name: string; fill?: boolean }>();
</script>
<template>
  <span class="material-symbols-outlined select-none" :class="{ 'is-filled': fill }" aria-hidden="true">{{ name }}</span>
</template>
```

- [ ] **Step 4: Create `resources/js/layouts/WorkspaceLayout.vue`** — translate the sidebar + top bar from `docs/superpowers/specs/assets/stitch/dashboard.html` (open it; sidebar is the first `<aside>`, top bar the first `<header>` inside `<main>`). Structure:

```vue
<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import MaterialIcon from '@/components/focusflow/MaterialIcon.vue';

defineProps<{ title?: string }>();
const page = usePage();
const isActive = (href: string) => page.url.startsWith(href);
const today = new Date().toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
const nav = [
  { label: 'Tasks', icon: 'checklist', href: '/dashboard' },
  { label: 'History', icon: 'history', href: '/history' },
  { label: 'Timer Settings', icon: 'timer', href: '/settings/timer' },
];
</script>
<template>
  <div class="flex min-h-screen bg-surface font-sans text-on-surface">
    <aside class="flex w-64 shrink-0 flex-col justify-between border-r border-outline-variant/50 bg-surface-container-lowest p-4">
      <div>
        <div class="mb-6 flex items-center gap-2 px-2">
          <div class="flex size-8 items-center justify-center rounded-lg bg-primary-fx text-on-primary-fx"><MaterialIcon name="target" /></div>
          <div>
            <p class="font-display text-base font-bold leading-tight">Focus Workspace</p>
            <p class="font-mono text-[10px] uppercase tracking-widest text-on-surface-variant">Deep Work Session</p>
          </div>
        </div>
        <Link href="/dashboard?new=1" class="mb-6 flex w-full items-center justify-center gap-2 rounded-lg bg-primary-container-fx px-4 py-2.5 text-sm font-semibold text-on-primary-fx shadow-sm transition hover:opacity-90">
          <MaterialIcon name="add" /> New Task
        </Link>
        <nav class="space-y-1">
          <Link v-for="item in nav" :key="item.href" :href="item.href"
            class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium"
            :class="isActive(item.href) ? 'bg-secondary-container-fx/40 text-primary-fx' : 'text-on-surface-variant hover:bg-surface-container-low'">
            <MaterialIcon :name="item.icon" :fill="isActive(item.href)" /> {{ item.label }}
          </Link>
        </nav>
      </div>
      <div class="space-y-1 border-t border-outline-variant/50 pt-4">
        <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-on-surface-variant hover:bg-surface-container-low"><MaterialIcon name="help" /> Help</a>
        <Link href="/logout" method="post" as="button" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-on-surface-variant hover:bg-surface-container-low"><MaterialIcon name="logout" /> Logout</Link>
      </div>
    </aside>
    <main class="flex-1 overflow-y-auto">
      <header class="px-8 pt-6">
        <h1 class="font-display text-2xl font-bold">{{ today }}</h1>
        <p class="text-sm text-on-surface-variant">Good morning, prepare for deep work.</p>
      </header>
      <div class="p-8 pt-6"><slot /></div>
    </main>
  </div>
</template>
```

Then compare against `assets/stitch/dashboard.png` and adjust spacing/classes to match.

- [ ] **Step 5: Verify build**

Run: `npm run build` — expected: success, no TS errors.

- [ ] **Step 6: Commit**

```bash
git add resources && git commit -m "feat: FocusFlow design tokens, fonts, workspace layout"
```

---

### Task 3: Domain migrations, models, factories

**Files:**
- Create: `database/migrations/*_create_categories_table.php`, `*_create_tasks_table.php`, `*_create_focus_sessions_table.php`, `*_create_user_preferences_table.php`
- Create: `app/Models/Category.php`, `app/Models/Task.php`, `app/Models/FocusSession.php`, `app/Models/UserPreference.php`
- Create: `database/factories/CategoryFactory.php`, `TaskFactory.php`, `FocusSessionFactory.php`
- Modify: `app/Models/User.php` (relations + `preferences()` accessor)
- Test: `tests/Feature/ModelRelationsTest.php`

**Interfaces:**
- Produces models:
  - `Task`: fillable `title, description, due_at, priority, session_minutes, notify_on_end, five_minute_warning, category_id, status, completed_at`; casts `due_at/completed_at => immutable_datetime`, `notify_on_end/five_minute_warning => boolean`; relations `user()`, `category()`, `sessions()` (hasMany FocusSession); helper `isCompleted(): bool`.
  - `Category`: fillable `name`; relations `user()`, `tasks()`.
  - `FocusSession`: fillable `task_id, started_at, ended_at, duration_seconds, completed`; casts `started_at/ended_at => immutable_datetime`, `completed => boolean`; relations `user()`, `task()`.
  - `UserPreference`: fillable `focus_minutes, short_break_minutes, long_break_minutes, focus_tone, break_tone, browser_notifications, zen_mode`; casts booleans.
  - `User`: `tasks()`, `categories()`, `focusSessions()`, `preferences()` (hasOne), and `getOrCreatePreferences(): UserPreference` using `preferences()->firstOrCreate([])`.

- [ ] **Step 1: Write failing test `tests/Feature/ModelRelationsTest.php`**

```php
<?php

use App\Models\{Category, FocusSession, Task, User, UserPreference};

test('task belongs to user and category and has sessions', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create(['name' => 'Work']);
    $task = Task::factory()->for($user)->for($category)->create();
    $session = FocusSession::factory()->for($user)->for($task)->create();

    expect($task->user->is($user))->toBeTrue()
        ->and($task->category->is($category))->toBeTrue()
        ->and($task->sessions->first()->is($session))->toBeTrue()
        ->and($task->isCompleted())->toBeFalse();
});

test('user preferences are created on demand with defaults', function () {
    $user = User::factory()->create();
    $prefs = $user->getOrCreatePreferences();

    expect($prefs)->toBeInstanceOf(UserPreference::class)
        ->and($prefs->focus_minutes)->toBe(25)
        ->and($prefs->short_break_minutes)->toBe(5)
        ->and($prefs->long_break_minutes)->toBe(15)
        ->and($prefs->focus_tone)->toBe('soft-bell')
        ->and($prefs->break_tone)->toBe('upbeat-chime')
        ->and($prefs->browser_notifications)->toBeTrue()
        ->and($prefs->zen_mode)->toBeFalse();
});

test('deleting a user cascades to domain rows', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();
    FocusSession::factory()->for($user)->for($task)->create();
    $user->getOrCreatePreferences();

    $user->delete();

    expect(Task::count())->toBe(0)
        ->and(FocusSession::count())->toBe(0)
        ->and(UserPreference::count())->toBe(0);
});
```

- [ ] **Step 2: Run to verify it fails** — `php artisan test --filter=ModelRelationsTest` → FAIL (classes missing).

- [ ] **Step 3: Create migrations** (`php artisan make:migration ...` × 4, then fill in):

```php
// create_categories_table
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('name', 50);
    $table->timestamps();
    $table->unique(['user_id', 'name']);
});

// create_tasks_table
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
    $table->string('title');
    $table->text('description')->nullable();
    $table->dateTime('due_at')->nullable();
    $table->string('priority', 10)->default('medium');
    $table->unsignedSmallInteger('session_minutes')->default(25);
    $table->boolean('notify_on_end')->default(true);
    $table->boolean('five_minute_warning')->default(false);
    $table->string('status', 10)->default('pending');
    $table->dateTime('completed_at')->nullable();
    $table->timestamps();
    $table->index(['user_id', 'status']);
});

// create_focus_sessions_table
Schema::create('focus_sessions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('task_id')->constrained()->cascadeOnDelete();
    $table->dateTime('started_at');
    $table->dateTime('ended_at')->nullable();
    $table->unsignedInteger('duration_seconds')->default(0);
    $table->boolean('completed')->default(false);
    $table->timestamps();
    $table->index(['user_id', 'started_at']);
});

// create_user_preferences_table
Schema::create('user_preferences', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
    $table->unsignedSmallInteger('focus_minutes')->default(25);
    $table->unsignedSmallInteger('short_break_minutes')->default(5);
    $table->unsignedSmallInteger('long_break_minutes')->default(15);
    $table->string('focus_tone', 30)->default('soft-bell');
    $table->string('break_tone', 30)->default('upbeat-chime');
    $table->boolean('browser_notifications')->default(true);
    $table->boolean('zen_mode')->default(false);
    $table->timestamps();
});
```

- [ ] **Step 4: Create models exactly per the Interfaces block.** Example `app/Models/Task.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'due_at', 'priority', 'session_minutes',
        'notify_on_end', 'five_minute_warning', 'category_id', 'status', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'immutable_datetime',
            'completed_at' => 'immutable_datetime',
            'notify_on_end' => 'boolean',
            'five_minute_warning' => 'boolean',
        ];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function sessions(): HasMany { return $this->hasMany(FocusSession::class); }
    public function isCompleted(): bool { return $this->status === 'completed'; }
}
```

`Category`, `FocusSession`, `UserPreference` follow the same shape with their fields from the Interfaces block. In `User.php` add:

```php
public function tasks(): HasMany { return $this->hasMany(Task::class); }
public function categories(): HasMany { return $this->hasMany(Category::class); }
public function focusSessions(): HasMany { return $this->hasMany(FocusSession::class); }
public function preferences(): HasOne { return $this->hasOne(UserPreference::class); }
public function getOrCreatePreferences(): UserPreference { return $this->preferences()->firstOrCreate([]); }
```

- [ ] **Step 5: Create factories.**

```php
// TaskFactory definition()
return [
    'user_id' => User::factory(),
    'title' => fake()->sentence(4),
    'description' => fake()->optional()->sentence(10),
    'due_at' => null,
    'priority' => fake()->randomElement(['high', 'medium', 'low']),
    'session_minutes' => 25,
    'status' => 'pending',
];
// plus state: public function completed(): static
//   => $this->state(['status' => 'completed', 'completed_at' => now()]);

// CategoryFactory definition()
return ['user_id' => User::factory(), 'name' => fake()->unique()->word()];

// FocusSessionFactory definition()
return [
    'user_id' => User::factory(),
    'task_id' => Task::factory(),
    'started_at' => now()->subMinutes(25),
    'ended_at' => now(),
    'duration_seconds' => 1500,
    'completed' => true,
];
```

- [ ] **Step 6: Run tests** — `php artisan test --filter=ModelRelationsTest` → PASS, then full `php artisan test` → PASS.

- [ ] **Step 7: Commit**

```bash
git add app database tests && git commit -m "feat: domain models, migrations, factories"
```

---### Task 4: Task CRUD + inline categories (backend)

**Files:**
- Create: `app/Http/Controllers/TaskController.php`, `app/Http/Controllers/CategoryController.php`
- Create: `app/Http/Requests/TaskRequest.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/TaskCrudTest.php`

**Interfaces:**
- Produces routes (all `auth`-middleware):
  - `POST /tasks` → `tasks.store`, `PATCH /tasks/{task}` → `tasks.update`, `DELETE /tasks/{task}` → `tasks.destroy`, `PATCH /tasks/{task}/toggle` → `tasks.toggle` (flips status, sets/clears `completed_at`), `POST /categories` → `categories.store`. All redirect back.
- Validation (TaskRequest, used by store+update): `title` required string max:255; `description` nullable string max:2000; `due_at` nullable date; `priority` required in:high,medium,low; `session_minutes` required integer 5–180; `notify_on_end`/`five_minute_warning` boolean; `category_id` nullable, must exist and belong to the authenticated user.
- Ownership: acting on another user's task/category → 403 (use `$this->authorize`-free simple check: `abort_unless($task->user_id === $request->user()->id, 403)`).

- [ ] **Step 1: Write failing tests `tests/Feature/TaskCrudTest.php`**

```php
<?php

use App\Models\{Category, Task, User};

test('a user can create a task with all fields', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();

    $this->actingAs($user)->post('/tasks', [
        'title' => 'Draft Q3 strategy',
        'description' => 'Core OKRs',
        'due_at' => now()->addHours(3)->toDateTimeString(),
        'priority' => 'high',
        'session_minutes' => 45,
        'notify_on_end' => true,
        'five_minute_warning' => true,
        'category_id' => $category->id,
    ])->assertRedirect();

    expect($user->tasks()->first())
        ->title->toBe('Draft Q3 strategy')
        ->priority->toBe('high')
        ->session_minutes->toBe(45)
        ->category_id->toBe($category->id);
});

test('validation rejects bad priority and foreign category', function () {
    $user = User::factory()->create();
    $foreign = Category::factory()->create(); // other user's

    $this->actingAs($user)->post('/tasks', [
        'title' => 'x', 'priority' => 'urgent', 'session_minutes' => 25, 'category_id' => $foreign->id,
    ])->assertSessionHasErrors(['priority', 'category_id']);
});

test('toggle completes and reopens a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $this->actingAs($user)->patch("/tasks/{$task->id}/toggle")->assertRedirect();
    expect($task->refresh())->status->toBe('completed')->completed_at->not->toBeNull();

    $this->actingAs($user)->patch("/tasks/{$task->id}/toggle")->assertRedirect();
    expect($task->refresh())->status->toBe('pending')->completed_at->toBeNull();
});

test('users cannot touch other users tasks', function () {
    $task = Task::factory()->create();
    $intruder = User::factory()->create();

    $this->actingAs($intruder)->patch("/tasks/{$task->id}/toggle")->assertForbidden();
    $this->actingAs($intruder)->delete("/tasks/{$task->id}")->assertForbidden();
});

test('a user can create a category inline and duplicates are rejected', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/categories', ['name' => 'Deep Work'])->assertRedirect();
    expect($user->categories()->pluck('name'))->toContain('Deep Work');

    $this->actingAs($user)->post('/categories', ['name' => 'Deep Work'])
        ->assertSessionHasErrors('name');
});
```

- [ ] **Step 2: Run to verify failure** — `php artisan test --filter=TaskCrudTest` → FAIL (404s).

- [ ] **Step 3: Implement.** `app/Http/Requests/TaskRequest.php`:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'due_at' => ['nullable', 'date'],
            'priority' => ['required', Rule::in(['high', 'medium', 'low'])],
            'session_minutes' => ['required', 'integer', 'between:5,180'],
            'notify_on_end' => ['boolean'],
            'five_minute_warning' => ['boolean'],
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('user_id', $this->user()->id)],
        ];
    }
}
```

`TaskController`:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\{RedirectResponse, Request};

class TaskController extends Controller
{
    public function store(TaskRequest $request): RedirectResponse
    {
        $request->user()->tasks()->create($request->validated());
        return back();
    }

    public function update(TaskRequest $request, Task $task): RedirectResponse
    {
        $this->ensureOwner($request, $task);
        $task->update($request->validated());
        return back();
    }

    public function toggle(Request $request, Task $task): RedirectResponse
    {
        $this->ensureOwner($request, $task);
        $task->isCompleted()
            ? $task->update(['status' => 'pending', 'completed_at' => null])
            : $task->update(['status' => 'completed', 'completed_at' => now()]);
        return back();
    }

    public function destroy(Request $request, Task $task): RedirectResponse
    {
        $this->ensureOwner($request, $task);
        $task->delete();
        return back();
    }

    private function ensureOwner(Request $request, Task $task): void
    {
        abort_unless($task->user_id === $request->user()->id, 403);
    }
}
```

`CategoryController::store`: validate `name => ['required','string','max:50', Rule::unique('categories','name')->where('user_id', $request->user()->id)]`, create via `$request->user()->categories()->create(...)`, `return back()`.

Routes in `routes/web.php` inside the existing `auth` group:

```php
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
```

- [ ] **Step 4: Run tests** — `php artisan test --filter=TaskCrudTest` → PASS; full suite PASS.

- [ ] **Step 5: Commit** — `git add -A && git commit -m "feat: task CRUD and inline categories"`

---

### Task 5: FocusStats service (all stats calculations)

**Files:**
- Create: `app/Services/FocusStats.php`
- Test: `tests/Feature/FocusStatsTest.php`

**Interfaces:**
- Produces `App\Services\FocusStats` (instantiate with `new FocusStats()` or resolve from container):
  - `dailyProgress(User $user): array{completed:int, total:int, deep_work_seconds:int, focus_score:int}` — tasks due today or created today; focus_score = completed sessions ÷ started sessions today × 100 (0 when none).
  - `weeklyCompleted(User $user): array{count:int, change_percent:int}` — tasks with `completed_at` in current calendar week vs previous week; change 100 when previous is 0 and current > 0, else 0.
  - `totalFocusSeconds(User $user): int` — sum of `duration_seconds` over all sessions.
  - `longestStreakDays(User $user): int` — longest run of consecutive calendar days having ≥1 completed session.

- [ ] **Step 1: Write failing tests**

```php
<?php

use App\Models\{FocusSession, Task, User};
use App\Services\FocusStats;

beforeEach(fn () => $this->travelTo(now()->parse('2026-07-15 09:00'))); // Wednesday

test('daily progress counts today tasks, deep work seconds, and focus score', function () {
    $user = User::factory()->create();
    Task::factory()->for($user)->count(2)->create();                       // created today
    Task::factory()->for($user)->completed()->create();                    // created+completed today
    Task::factory()->for($user)->create(['created_at' => now()->subDays(3), 'due_at' => now()]); // due today
    Task::factory()->for($user)->create(['created_at' => now()->subDays(3)]); // not today

    $task = $user->tasks()->first();
    FocusSession::factory()->for($user)->for($task)->create(['started_at' => now()->subHour(), 'duration_seconds' => 1500, 'completed' => true]);
    FocusSession::factory()->for($user)->for($task)->create(['started_at' => now()->subHours(2), 'duration_seconds' => 900, 'completed' => false]);
    FocusSession::factory()->for($user)->for($task)->create(['started_at' => now()->subDays(2)]); // not today

    $stats = (new FocusStats())->dailyProgress($user);

    expect($stats)->toBe(['completed' => 1, 'total' => 4, 'deep_work_seconds' => 2400, 'focus_score' => 50]);
});

test('weekly completed compares against previous week', function () {
    $user = User::factory()->create();
    Task::factory()->for($user)->count(3)->completed()->create();
    Task::factory()->for($user)->count(2)->create([
        'status' => 'completed', 'completed_at' => now()->subWeek(),
    ]);

    expect((new FocusStats())->weeklyCompleted($user))->toBe(['count' => 3, 'change_percent' => 50]);
});

test('longest streak counts consecutive days with completed sessions', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();
    foreach ([1, 2, 3, 5, 6, 9] as $daysAgo) {
        FocusSession::factory()->for($user)->for($task)->create(['started_at' => now()->subDays($daysAgo)]);
    }
    FocusSession::factory()->for($user)->for($task)->create(['started_at' => now()->subDays(4), 'completed' => false]); // abandoned, breaks nothing

    expect((new FocusStats())->longestStreakDays($user))->toBe(3);
    expect((new FocusStats())->totalFocusSeconds($user))->toBe(7 * 1500);
});
```

- [ ] **Step 2: Run to verify failure** — `php artisan test --filter=FocusStatsTest` → FAIL (class missing).

- [ ] **Step 3: Implement `app/Services/FocusStats.php`**

```php
<?php

namespace App\Services;

use App\Models\User;

class FocusStats
{
    public function dailyProgress(User $user): array
    {
        $today = now()->toDateString();
        $tasks = $user->tasks()
            ->where(fn ($q) => $q->whereDate('due_at', $today)->orWhereDate('created_at', $today))
            ->get();
        $sessions = $user->focusSessions()->whereDate('started_at', $today)->get();
        $started = $sessions->count();

        return [
            'completed' => $tasks->where('status', 'completed')->count(),
            'total' => $tasks->count(),
            'deep_work_seconds' => (int) $sessions->sum('duration_seconds'),
            'focus_score' => $started > 0 ? (int) round($sessions->where('completed', true)->count() / $started * 100) : 0,
        ];
    }

    public function weeklyCompleted(User $user): array
    {
        $count = $this->completedBetween($user, now()->startOfWeek(), now()->endOfWeek());
        $previous = $this->completedBetween($user, now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek());

        return [
            'count' => $count,
            'change_percent' => $previous > 0
                ? (int) round(($count - $previous) / $previous * 100)
                : ($count > 0 ? 100 : 0),
        ];
    }

    public function totalFocusSeconds(User $user): int
    {
        return (int) $user->focusSessions()->sum('duration_seconds');
    }

    public function longestStreakDays(User $user): int
    {
        $days = $user->focusSessions()->where('completed', true)
            ->pluck('started_at')->map(fn ($d) => $d->toDateString())->unique()->sort()->values();

        $best = $run = $days->isEmpty() ? 0 : 1;
        for ($i = 1; $i < $days->count(); $i++) {
            $run = now()->parse($days[$i - 1])->addDay()->toDateString() === $days[$i] ? $run + 1 : 1;
            $best = max($best, $run);
        }

        return $best;
    }

    private function completedBetween(User $user, $from, $to): int
    {
        return $user->tasks()->where('status', 'completed')->whereBetween('completed_at', [$from, $to])->count();
    }
}
```

- [ ] **Step 4: Run tests** — `php artisan test --filter=FocusStatsTest` → PASS.

- [ ] **Step 5: Commit** — `git add app/Services tests && git commit -m "feat: FocusStats service"`

---

### Task 6: Dashboard page (timer card, daily progress, task list, create-task modal)

**Files:**
- Create: `app/Http/Controllers/DashboardController.php`
- Modify: `routes/web.php` (point `/dashboard` at the controller, replacing the starter kit's closure/route)
- Create: `resources/js/pages/Dashboard.vue` (replace starter kit file)
- Create: `resources/js/components/focusflow/{PriorityChip.vue,TaskItem.vue,CreateTaskModal.vue,DailyProgressCard.vue,TimerCard.vue}`
- Create: `resources/js/types/focusflow.ts`
- Test: `tests/Feature/DashboardTest.php`

**Interfaces:**
- Consumes: `FocusStats::dailyProgress`, task/category routes from Task 4, `WorkspaceLayout`, `MaterialIcon`, tokens from Task 2.
- Produces Inertia page `Dashboard` with props:
  - `tasks`: array of `{id, title, description, priority, status, session_minutes, due_at, category: {id, name} | null, notify_on_end, five_minute_warning}` — tasks due-or-created today, pending first then by priority (high→low)
  - `categories`: `{id, name}[]`
  - `stats`: `{completed, total, deep_work_seconds, focus_score}`
  - `focus_minutes`: user's preference (timer card default when no task selected)
- Produces TS types in `resources/js/types/focusflow.ts`: `TaskItemData`, `CategoryData`, `DailyStats` matching the props above (later tasks import these).
- The New Task modal opens when URL has `?new=1` or the "Add a new task" button is clicked; submits to `POST /tasks`; "Start Timer" on the timer card visits `/focus/{selectedTaskId}` (first pending task by default).

- [ ] **Step 1: Write failing test `tests/Feature/DashboardTest.php`**

```php
<?php

use App\Models\{Task, User};
use Inertia\Testing\AssertableInertia as Assert;

test('dashboard shows today tasks ordered pending-first with stats', function () {
    $user = User::factory()->create();
    Task::factory()->for($user)->completed()->create(['title' => 'Done one']);
    Task::factory()->for($user)->create(['title' => 'High one', 'priority' => 'high']);
    Task::factory()->for($user)->create(['title' => 'Low one', 'priority' => 'low']);
    Task::factory()->for($user)->create(['created_at' => now()->subDays(2)]); // excluded

    $this->actingAs($user)->get('/dashboard')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('tasks', 3)
            ->where('tasks.0.title', 'High one')
            ->where('tasks.2.title', 'Done one')
            ->where('stats.total', 3)
            ->where('stats.completed', 1)
            ->has('categories')
            ->where('focus_minutes', 25));
});
```

- [ ] **Step 2: Run to verify failure** — expects `Dashboard` props → FAIL.

- [ ] **Step 3: Implement `DashboardController`**

```php
<?php

namespace App\Http\Controllers;

use App\Services\FocusStats;
use Illuminate\Http\Request;
use Inertia\{Inertia, Response};

class DashboardController extends Controller
{
    public function index(Request $request, FocusStats $stats): Response
    {
        $user = $request->user();
        $today = now()->toDateString();
        $rank = ['high' => 0, 'medium' => 1, 'low' => 2];

        $tasks = $user->tasks()->with('category:id,name')
            ->where(fn ($q) => $q->whereDate('due_at', $today)->orWhereDate('created_at', $today))
            ->get()
            ->sortBy(fn ($t) => [$t->isCompleted() ? 1 : 0, $rank[$t->priority]])
            ->values()
            ->map(fn ($t) => [
                'id' => $t->id, 'title' => $t->title, 'description' => $t->description,
                'priority' => $t->priority, 'status' => $t->status,
                'session_minutes' => $t->session_minutes, 'due_at' => $t->due_at?->toIso8601String(),
                'category' => $t->category?->only(['id', 'name']),
                'notify_on_end' => $t->notify_on_end, 'five_minute_warning' => $t->five_minute_warning,
            ]);

        return Inertia::render('Dashboard', [
            'tasks' => $tasks,
            'categories' => $user->categories()->orderBy('name')->get(['id', 'name']),
            'stats' => $stats->dailyProgress($user),
            'focus_minutes' => $user->getOrCreatePreferences()->focus_minutes,
        ]);
    }
}
```

Route: replace the starter kit's dashboard route with `Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');`

- [ ] **Step 4: Run test** — `php artisan test --filter=DashboardTest` → PASS.

- [ ] **Step 5: Create `resources/js/types/focusflow.ts`**

```ts
export interface CategoryData { id: number; name: string }
export interface TaskItemData {
  id: number; title: string; description: string | null;
  priority: 'high' | 'medium' | 'low'; status: 'pending' | 'completed';
  session_minutes: number; due_at: string | null; category: CategoryData | null;
  notify_on_end: boolean; five_minute_warning: boolean;
}
export interface DailyStats { completed: number; total: number; deep_work_seconds: number; focus_score: number }
```

- [ ] **Step 6: Build the Vue components**, using `assets/stitch/dashboard.html` and `assets/stitch/create-task.html` as the visual reference (match card structure, chips, and typography; screenshots `dashboard.png` / `create-task.png` are ground truth).

`PriorityChip.vue`:

```vue
<script setup lang="ts">
const props = defineProps<{ priority: 'high' | 'medium' | 'low' }>();
const styles = {
  high: 'bg-error-container text-on-error-container',
  medium: 'bg-secondary-container-fx/60 text-on-secondary-container-fx',
  low: 'bg-surface-container text-on-surface-variant',
} as const;
</script>
<template>
  <span class="rounded px-2 py-0.5 font-mono text-[11px] font-medium uppercase tracking-widest" :class="styles[priority]">
    {{ priority }} priority
  </span>
</template>
```

`TaskItem.vue` — props `{ task: TaskItemData }`; checkbox posts `router.patch(`/tasks/${task.id}/toggle`, {}, { preserveScroll: true })`; completed tasks get `line-through opacity-60`; row shows title, description, `PriorityChip`; clicking the row title area navigates to `/focus/${task.id}` when pending.

`DailyProgressCard.vue` — props `{ stats: DailyStats }`; renders "X/Y TASKS COMPLETED" with progress bar (`width: stats.total ? stats.completed/stats.total*100 : 0`%), deep-work hours (`(deep_work_seconds/3600).toFixed(1)`h), focus score %; labels in `font-mono` uppercase.

`TimerCard.vue` — props `{ minutes: number; taskId: number | null }`; static display `MM:00`, "CURRENT SESSION: DEEP FOCUS" mono label, Start Timer button → `router.visit(taskId ? `/focus/${taskId}` : '/dashboard?new=1')`.

`CreateTaskModal.vue` — props `{ open: boolean; categories: CategoryData[] }`, emits `close`. Uses Inertia `useForm` with fields `title, description, due_at, priority ('medium' default), session_minutes (25), notify_on_end (true), five_minute_warning (false), category_id (null)`; submit `form.post('/tasks', { onSuccess: () => { form.reset(); emit('close'); } })`; shows `form.errors.*` under fields. Category `<select>` includes a "+ New category" option that reveals an inline text input posting to `/categories` via `router.post` then selects it. Layout: fixed inset-0 backdrop (`bg-black/30 backdrop-blur-sm`), centered panel `bg-surface-container-lowest rounded-2xl shadow-xl max-w-lg w-full p-6`, per `create-task.html`.

`Dashboard.vue` (replaces starter kit page):

```vue
<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import WorkspaceLayout from '@/layouts/WorkspaceLayout.vue';
import TimerCard from '@/components/focusflow/TimerCard.vue';
import DailyProgressCard from '@/components/focusflow/DailyProgressCard.vue';
import TaskItem from '@/components/focusflow/TaskItem.vue';
import CreateTaskModal from '@/components/focusflow/CreateTaskModal.vue';
import MaterialIcon from '@/components/focusflow/MaterialIcon.vue';
import type { CategoryData, DailyStats, TaskItemData } from '@/types/focusflow';

const props = defineProps<{ tasks: TaskItemData[]; categories: CategoryData[]; stats: DailyStats; focus_minutes: number }>();
const showModal = ref(false);
onMounted(() => { if (new URLSearchParams(window.location.search).get('new') === '1') showModal.value = true; });
const firstPending = computed(() => props.tasks.find((t) => t.status === 'pending') ?? null);
</script>
<template>
  <WorkspaceLayout title="Dashboard">
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
      <div class="xl:col-span-2"><TimerCard :minutes="firstPending?.session_minutes ?? focus_minutes" :task-id="firstPending?.id ?? null" /></div>
      <DailyProgressCard :stats="stats" />
    </div>
    <section class="mt-8">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="font-display text-lg font-bold">Today's Tasks</h2>
      </div>
      <div class="space-y-3">
        <TaskItem v-for="task in tasks" :key="task.id" :task="task" />
        <button class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-primary-fx hover:bg-surface-container-low" @click="showModal = true">
          <MaterialIcon name="add_circle" /> Add a new task
        </button>
      </div>
    </section>
    <CreateTaskModal :open="showModal" :categories="categories" @close="showModal = false" />
  </WorkspaceLayout>
</template>
```

- [ ] **Step 7: Verify** — `npm run build` succeeds; `php artisan test` all PASS. Then boot `php artisan serve` + `npm run dev`, register a user, create a task via the modal, toggle it, compare against `dashboard.png`.

- [ ] **Step 8: Commit** — `git add -A && git commit -m "feat: dashboard with timer card, daily progress, tasks, create modal"`

---

### Task 7: Focus sessions (backend) + Focus Mode page + countdown/alerts composables

**Files:**
- Create: `app/Http/Controllers/FocusController.php`, `app/Http/Controllers/FocusSessionController.php`
- Modify: `routes/web.php`
- Create: `resources/js/composables/useCountdown.ts`, `resources/js/composables/useAlerts.ts`
- Create: `resources/js/pages/Focus.vue`
- Test: `tests/Feature/FocusSessionTest.php`

**Interfaces:**
- Consumes: `Task`, `FocusSession` models; `TaskItemData` type; tokens/layout from Task 2.
- Produces routes (auth):
  - `GET /focus/{task}` → `focus.show`: Inertia page `Focus` with props `task` (TaskItemData shape), `active_session: {id, started_at, planned_seconds} | null`, `preferences: {browser_notifications, zen_mode, focus_tone, break_tone}`.
  - `POST /tasks/{task}/sessions` → `sessions.store`: abandons any open session for the user (sets `ended_at`, `completed=false`, capped duration), creates a new session `started_at=now()`, redirects to `focus.show`.
  - `PATCH /sessions/{session}/finish` → `sessions.finish`: body `{elapsed_seconds:int, completed:bool, complete_task:bool}`; sets `ended_at=now()`, `duration_seconds=min(elapsed_seconds, actual wall diff)`, `completed`; when `complete_task` also marks the task completed; redirects to `/dashboard`.
- Produces `useCountdown()` composable: `{ remaining: Ref<number>, running: Ref<boolean>, display: ComputedRef<string>, start(seconds), pause(), resume() }` with options `{ onFinish?, onWarning? }` (`onWarning` fires once at 60s remaining).
- Produces `useAlerts(prefs)` composable: `{ notify(title, body), chime(kind: 'focus'|'break') }` — no-ops when `browser_notifications` is false / `zen_mode` is true respectively; chime uses Web Audio oscillator (no asset files).

- [ ] **Step 1: Write failing tests `tests/Feature/FocusSessionTest.php`**

```php
<?php

use App\Models\{FocusSession, Task, User};

test('starting a session closes any open one and creates a new row', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();
    $stale = FocusSession::factory()->for($user)->for($task)->create([
        'started_at' => now()->subMinutes(10), 'ended_at' => null, 'duration_seconds' => 0, 'completed' => false,
    ]);

    $this->actingAs($user)->post("/tasks/{$task->id}/sessions")
        ->assertRedirect("/focus/{$task->id}");

    expect($stale->refresh()->ended_at)->not->toBeNull()
        ->and($stale->completed)->toBeFalse()
        ->and($task->sessions()->whereNull('ended_at')->count())->toBe(1);
});

test('finishing a session records duration capped at wall time and can complete the task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create(['session_minutes' => 25]);
    $session = FocusSession::factory()->for($user)->for($task)->create([
        'started_at' => now()->subMinutes(25), 'ended_at' => null, 'duration_seconds' => 0, 'completed' => false,
    ]);

    $this->actingAs($user)->patch("/sessions/{$session->id}/finish", [
        'elapsed_seconds' => 99999, 'completed' => true, 'complete_task' => true,
    ])->assertRedirect('/dashboard');

    $session->refresh();
    expect($session->completed)->toBeTrue()
        ->and($session->duration_seconds)->toBeLessThanOrEqual(25 * 60 + 5)
        ->and($task->refresh()->status)->toBe('completed');
});

test('focus page shows task and active session', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $this->actingAs($user)->get("/focus/{$task->id}")->assertOk();
    $this->actingAs(User::factory()->create())->get("/focus/{$task->id}")->assertForbidden();
});
```

- [ ] **Step 2: Run to verify failure** — FAIL (routes missing).

- [ ] **Step 3: Implement controllers.** `FocusSessionController`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\{FocusSession, Task};
use Illuminate\Http\{RedirectResponse, Request};

class FocusSessionController extends Controller
{
    public function store(Request $request, Task $task): RedirectResponse
    {
        abort_unless($task->user_id === $request->user()->id, 403);

        $request->user()->focusSessions()->whereNull('ended_at')->get()
            ->each(fn (FocusSession $open) => $open->update([
                'ended_at' => now(),
                'duration_seconds' => min($open->started_at->diffInSeconds(now()), $open->task->session_minutes * 60),
                'completed' => false,
            ]));

        $task->sessions()->create(['user_id' => $request->user()->id, 'started_at' => now()]);

        return redirect()->route('focus.show', $task);
    }

    public function finish(Request $request, FocusSession $session): RedirectResponse
    {
        abort_unless($session->user_id === $request->user()->id, 403);
        $data = $request->validate([
            'elapsed_seconds' => ['required', 'integer', 'min:0'],
            'completed' => ['required', 'boolean'],
            'complete_task' => ['boolean'],
        ]);

        $session->update([
            'ended_at' => now(),
            'duration_seconds' => min($data['elapsed_seconds'], (int) $session->started_at->diffInSeconds(now())),
            'completed' => $data['completed'],
        ]);

        if ($request->boolean('complete_task')) {
            $session->task->update(['status' => 'completed', 'completed_at' => now()]);
        }

        return redirect()->route('dashboard');
    }
}
```

`FocusController::show(Request $request, Task $task)`: ownership check; load `$task->category`; `$active = $task->sessions()->whereNull('ended_at')->latest('started_at')->first();` render `Focus` with props per Interfaces (`planned_seconds = $task->session_minutes * 60`; preferences from `getOrCreatePreferences()`).

Routes:

```php
Route::get('/focus/{task}', [FocusController::class, 'show'])->name('focus.show');
Route::post('/tasks/{task}/sessions', [FocusSessionController::class, 'store'])->name('sessions.store');
Route::patch('/sessions/{session}/finish', [FocusSessionController::class, 'finish'])->name('sessions.finish');
```

- [ ] **Step 4: Run tests** — `php artisan test --filter=FocusSessionTest` → PASS.

- [ ] **Step 5: Create `resources/js/composables/useCountdown.ts`**

```ts
import { computed, onBeforeUnmount, ref } from 'vue';

export function useCountdown(options: { onFinish?: () => void; onWarning?: () => void } = {}) {
  const remaining = ref(0);
  const running = ref(false);
  let handle: number | undefined;
  let target = 0;
  let warned = false;

  function tick() {
    remaining.value = Math.max(0, Math.round((target - Date.now()) / 1000));
    if (!warned && remaining.value <= 60 && remaining.value > 0) { warned = true; options.onWarning?.(); }
    if (remaining.value <= 0) { stop(); options.onFinish?.(); }
  }
  function stop() { if (handle) window.clearInterval(handle); handle = undefined; running.value = false; }
  function start(seconds: number) { target = Date.now() + seconds * 1000; warned = seconds <= 60; stop(); running.value = true; handle = window.setInterval(tick, 250); tick(); }
  function pause() { if (running.value) { tick(); stop(); } }
  function resume() { if (!running.value && remaining.value > 0) start(remaining.value); }

  const display = computed(() => `${String(Math.floor(remaining.value / 60)).padStart(2, '0')}:${String(remaining.value % 60).padStart(2, '0')}`);
  onBeforeUnmount(stop);
  return { remaining, running, display, start, pause, resume };
}
```

- [ ] **Step 6: Create `resources/js/composables/useAlerts.ts`**

```ts
interface AlertPrefs { browser_notifications: boolean; zen_mode: boolean; focus_tone: string; break_tone: string }

const TONES: Record<string, number[]> = {
  'soft-bell': [523.25, 659.25, 783.99],
  'deep-gong': [196.0, 146.83],
  'digital-ping': [880.0, 1174.66],
  'upbeat-chime': [659.25, 523.25],
  'gentle-marimba': [392.0, 523.25, 392.0],
  'forest-birds': [1318.51, 1567.98, 1318.51],
};

export function useAlerts(prefs: AlertPrefs) {
  async function notify(title: string, body: string) {
    if (!prefs.browser_notifications || !('Notification' in window)) return;
    if (Notification.permission === 'default') await Notification.requestPermission();
    if (Notification.permission === 'granted') new Notification(title, { body });
  }
  function chime(kind: 'focus' | 'break') {
    if (prefs.zen_mode) return;
    const ctx = new AudioContext();
    const notes = TONES[kind === 'focus' ? prefs.focus_tone : prefs.break_tone] ?? TONES['soft-bell'];
    notes.forEach((freq, i) => {
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.frequency.value = freq;
      osc.type = 'sine';
      gain.gain.setValueAtTime(0.2, ctx.currentTime + i * 0.18);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + i * 0.18 + 0.4);
      osc.connect(gain).connect(ctx.destination);
      osc.start(ctx.currentTime + i * 0.18);
      osc.stop(ctx.currentTime + i * 0.18 + 0.45);
    });
  }
  return { notify, chime };
}
```

- [ ] **Step 7: Create `resources/js/pages/Focus.vue`** — bare fullscreen layout (no WorkspaceLayout), per `assets/stitch/focus-mode.html` / `focus-mode.png`: warning banner (shown when `showWarning`), centered circular ring with `display`, "REMAINING" mono label, task card (category chip, title, description), Pause/Resume + Complete buttons, "Exit Focus Mode" bottom-right link.

```vue
<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useCountdown } from '@/composables/useCountdown';
import { useAlerts } from '@/composables/useAlerts';
import MaterialIcon from '@/components/focusflow/MaterialIcon.vue';
import type { TaskItemData } from '@/types/focusflow';

const props = defineProps<{
  task: TaskItemData;
  active_session: { id: number; started_at: string; planned_seconds: number } | null;
  preferences: { browser_notifications: boolean; zen_mode: boolean; focus_tone: string; break_tone: string };
}>();

const alerts = useAlerts(props.preferences);
const showWarning = ref(false);
const { display, running, start, pause, resume, remaining } = useCountdown({
  onWarning: () => { showWarning.value = true; }, // final-minute banner, always
  onFinish: () => {
    if (props.task.notify_on_end) { alerts.chime('focus'); alerts.notify('Session complete', `"${props.task.title}" focus block finished.`); }
    finish(true, false);
  },
});

// Optional 5-minute heads-up chime, per task toggle
watch(remaining, (v) => {
  if (v === 300 && props.task.five_minute_warning) {
    alerts.chime('break');
    alerts.notify('5 minutes left', `Wrap up "${props.task.title}" soon.`);
  }
});

const elapsed = () => (props.active_session ? props.active_session.planned_seconds - remaining.value : 0);
function finish(completed: boolean, completeTask: boolean) {
  if (!props.active_session) return;
  router.patch(`/sessions/${props.active_session.id}/finish`, {
    elapsed_seconds: elapsed(), completed, complete_task: completeTask,
  });
}

onMounted(() => {
  if (!props.active_session) { router.post(`/tasks/${props.task.id}/sessions`); return; }
  const already = Math.floor((Date.now() - new Date(props.active_session.started_at).getTime()) / 1000);
  start(Math.max(0, props.active_session.planned_seconds - already));
});
</script>
<template>
  <div class="flex min-h-screen flex-col bg-surface font-sans text-on-surface">
    <div v-if="showWarning" class="flex items-center gap-2 bg-primary-container-fx px-4 py-2 font-mono text-xs text-on-primary-fx">
      <MaterialIcon name="info" /> 1 minute remaining in current focus session. Wrap up your thought.
      <button class="ml-auto" @click="showWarning = false"><MaterialIcon name="close" /></button>
    </div>
    <div class="flex flex-1 flex-col items-center justify-center gap-10 p-6">
      <div class="flex size-64 flex-col items-center justify-center rounded-full border-4 border-primary-container-fx bg-surface-container-lowest shadow-xl">
        <span class="font-display text-6xl font-bold tracking-tight">{{ display }}</span>
        <span class="mt-1 font-mono text-[11px] uppercase tracking-widest text-on-surface-variant">Remaining</span>
      </div>
      <div class="w-full max-w-md rounded-2xl bg-surface-container-lowest p-6 text-center shadow-sm">
        <span v-if="task.category" class="rounded bg-secondary-container-fx/60 px-2 py-0.5 font-mono text-[11px] uppercase tracking-widest text-on-secondary-container-fx">{{ task.category.name }}</span>
        <h2 class="mt-2 font-display text-xl font-bold">{{ task.title }}</h2>
        <p v-if="task.description" class="mt-1 text-sm text-on-surface-variant">{{ task.description }}</p>
      </div>
      <div class="flex items-center gap-3">
        <button class="flex items-center gap-2 rounded-lg border border-outline-variant bg-surface-container-lowest px-5 py-2.5 text-sm font-semibold" @click="running ? pause() : resume()">
          <MaterialIcon :name="running ? 'pause' : 'play_arrow'" /> {{ running ? 'Pause' : 'Resume' }}
        </button>
        <button class="flex items-center gap-2 rounded-lg bg-primary-container-fx px-5 py-2.5 text-sm font-semibold text-on-primary-fx" @click="finish(true, true)">
          <MaterialIcon name="check_circle" /> Complete
        </button>
      </div>
    </div>
    <div class="p-4 text-right">
      <button class="font-mono text-xs text-on-surface-variant hover:text-on-surface" @click="finish(false, false)">Exit Focus Mode</button>
    </div>
  </div>
</template>
```

Warning semantics: the final-minute banner always appears at 60s (matching the design); the task's `five_minute_warning` toggle adds the extra chime + notification at 300s via the `watch` above.

- [ ] **Step 8: Verify** — `npm run build` + `php artisan test` PASS. Manual: start a task's session from dashboard, watch countdown, Complete → redirected to dashboard with task struck through and Daily Progress updated.

- [ ] **Step 9: Commit** — `git add -A && git commit -m "feat: focus sessions with fullscreen focus mode"`

---

### Task 8: Task History page + Timer Settings page

**Files:**
- Create: `app/Http/Controllers/HistoryController.php`, `app/Http/Controllers/TimerSettingsController.php`
- Modify: `routes/web.php`
- Create: `resources/js/pages/History.vue`, `resources/js/pages/settings/Timer.vue`
- Create: `resources/js/components/focusflow/StatCard.vue`
- Test: `tests/Feature/HistoryTest.php`, `tests/Feature/TimerSettingsTest.php`

**Interfaces:**
- Consumes: `FocusStats` (weeklyCompleted, totalFocusSeconds, longestStreakDays), `WorkspaceLayout`, `StatCard`.
- Produces routes (auth):
  - `GET /history` → `history.index`: props `stats: {week_count, week_change_percent, total_focus_seconds, longest_streak_days}`, `rows: {id, title, priority, category: string|null, completed_at: iso, focused_seconds: int}[]` (completed tasks, newest first, max 50), `filters: {q, category_id, days, priority}`, `categories: {id,name}[]`. Query params filter: `q` (title contains), `category_id`, `days` (7|30|90), `priority`.
  - `GET /settings/timer` → `settings.timer.edit`: props `preferences` (all 7 fields).
  - `PATCH /settings/timer` → `settings.timer.update`: validates `focus_minutes` int 15–90, `short_break_minutes` int 1–15, `long_break_minutes` int 10–45, `focus_tone` in allowed focus tones, `break_tone` in allowed break tones, `browser_notifications`/`zen_mode` boolean; updates preferences; redirects back.
- `focused_seconds` per task = sum of that task's sessions' `duration_seconds`.

- [ ] **Step 1: Write failing tests**

`tests/Feature/HistoryTest.php`:

```php
<?php

use App\Models\{Category, FocusSession, Task, User};
use Inertia\Testing\AssertableInertia as Assert;

test('history lists completed tasks with focused seconds and stats', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->completed()->create(['title' => 'Ship report']);
    FocusSession::factory()->for($user)->for($task)->count(2)->create(['duration_seconds' => 900]);
    Task::factory()->for($user)->create(); // pending — excluded

    $this->actingAs($user)->get('/history')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('History')
            ->has('rows', 1)
            ->where('rows.0.title', 'Ship report')
            ->where('rows.0.focused_seconds', 1800)
            ->where('stats.week_count', 1)
            ->where('stats.total_focus_seconds', 1800));
});

test('history filters by search, priority, and category', function () {
    $user = User::factory()->create();
    $cat = Category::factory()->for($user)->create();
    Task::factory()->for($user)->completed()->create(['title' => 'Alpha work', 'priority' => 'high', 'category_id' => $cat->id]);
    Task::factory()->for($user)->completed()->create(['title' => 'Beta work', 'priority' => 'low']);

    $this->actingAs($user)->get('/history?q=Alpha')
        ->assertInertia(fn (Assert $p) => $p->component('History')->has('rows', 1));
    $this->actingAs($user)->get('/history?priority=low')
        ->assertInertia(fn (Assert $p) => $p->component('History')->has('rows', 1)->where('rows.0.title', 'Beta work'));
    $this->actingAs($user)->get("/history?category_id={$cat->id}")
        ->assertInertia(fn (Assert $p) => $p->component('History')->has('rows', 1)->where('rows.0.title', 'Alpha work'));
});
```

`tests/Feature/TimerSettingsTest.php`:

```php
<?php

use App\Models\User;

test('user can view and update timer preferences', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/settings/timer')->assertOk();

    $this->actingAs($user)->patch('/settings/timer', [
        'focus_minutes' => 50, 'short_break_minutes' => 10, 'long_break_minutes' => 20,
        'focus_tone' => 'deep-gong', 'break_tone' => 'gentle-marimba',
        'browser_notifications' => false, 'zen_mode' => true,
    ])->assertRedirect();

    expect($user->preferences->refresh())
        ->focus_minutes->toBe(50)->zen_mode->toBeTrue()->focus_tone->toBe('deep-gong');
});

test('out of range durations are rejected', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->patch('/settings/timer', [
        'focus_minutes' => 5, 'short_break_minutes' => 30, 'long_break_minutes' => 20,
        'focus_tone' => 'soft-bell', 'break_tone' => 'upbeat-chime',
        'browser_notifications' => true, 'zen_mode' => false,
    ])->assertSessionHasErrors(['focus_minutes', 'short_break_minutes']);
});
```

- [ ] **Step 2: Run to verify failure** — both FAIL (routes missing).

- [ ] **Step 3: Implement `HistoryController`**

```php
<?php

namespace App\Http\Controllers;

use App\Services\FocusStats;
use Illuminate\Http\Request;
use Inertia\{Inertia, Response};

class HistoryController extends Controller
{
    public function index(Request $request, FocusStats $stats): Response
    {
        $user = $request->user();
        $weekly = $stats->weeklyCompleted($user);

        $rows = $user->tasks()->with('category:id,name')->withSum('sessions', 'duration_seconds')
            ->where('status', 'completed')
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->when($request->filled('priority'), fn ($q) => $q->where('priority', $request->string('priority')))
            ->when($request->filled('days'), fn ($q) => $q->where('completed_at', '>=', now()->subDays($request->integer('days'))))
            ->latest('completed_at')->limit(50)->get()
            ->map(fn ($t) => [
                'id' => $t->id, 'title' => $t->title, 'priority' => $t->priority,
                'category' => $t->category?->name, 'completed_at' => $t->completed_at?->toIso8601String(),
                'focused_seconds' => (int) ($t->sessions_sum_duration_seconds ?? 0),
            ]);

        return Inertia::render('History', [
            'rows' => $rows,
            'stats' => [
                'week_count' => $weekly['count'],
                'week_change_percent' => $weekly['change_percent'],
                'total_focus_seconds' => $stats->totalFocusSeconds($user),
                'longest_streak_days' => $stats->longestStreakDays($user),
            ],
            'filters' => $request->only(['q', 'category_id', 'days', 'priority']),
            'categories' => $user->categories()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
```

`TimerSettingsController`: `edit` renders `settings/Timer` with `['preferences' => $request->user()->getOrCreatePreferences()]`; `update` validates per Interfaces (use `Rule::in([...])` with the Global Constraints tone lists) then `$request->user()->getOrCreatePreferences()->update($validated); return back();`

Routes:

```php
Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
Route::get('/settings/timer', [TimerSettingsController::class, 'edit'])->name('settings.timer.edit');
Route::patch('/settings/timer', [TimerSettingsController::class, 'update'])->name('settings.timer.update');
```

- [ ] **Step 4: Run tests** — both filters PASS; full suite PASS.

- [ ] **Step 5: Build `StatCard.vue`** (props `{ label: string; value: string; sub?: string; highlight?: boolean }` — mono uppercase label, `font-display` big value, optional sub line; `highlight` gives the streak card its tinted border per `task-history.png`).

- [ ] **Step 6: Build `History.vue`** per `assets/stitch/task-history.html`: WorkspaceLayout; "Task History" heading + subtitle; 3 `StatCard`s (`COMPLETED THIS WEEK` value `{{ stats.week_count }}` sub `+{{ stats.week_change_percent }}%`, `TOTAL FOCUS TIME` value formatted `{Math.floor(s/3600)}h {Math.floor(s%3600/60)}m`, `LONGEST STREAK` value `{{ stats.longest_streak_days }}` sub `Days`, highlight). Filter bar: search input + category/days/priority `<select>`s driving `router.get('/history', {...filters}, { preserveState: true, replace: true })` (debounce search 300ms with a small `setTimeout` watcher). Rows: checkbox-checked icon, strikethrough title, PriorityChip, category + completed date in mono, right-aligned `⏱ Xm focused` chip. Client-side "Load More": show rows in chunks of 10 (`visibleCount = ref(10)`; button increments by 10; hidden when all visible).

- [ ] **Step 7: Build `settings/Timer.vue`** per `assets/stitch/settings.html`: WorkspaceLayout; "Settings & Preferences" heading; `useForm` seeded from `preferences`; Session Durations card with three `<input type="range">` sliders (Focus Block 15–90 showing `{{ form.focus_minutes }}m`, Short Break 1–15, Long Break 10–45); Sound Design card with two `<select>`s (focus tones: `soft-bell` "Soft Bell (Default)", `deep-gong` "Deep Gong", `digital-ping` "Digital Ping"; break tones: `upbeat-chime` "Upbeat Chime (Default)", `gentle-marimba` "Gentle Marimba", `forest-birds` "Forest Birds"); Permissions card with two toggles (Browser Notifications, Zen Mode). Footer buttons: "Discard Changes" (`form.reset()`) and "Save Preferences" (`form.patch('/settings/timer', { preserveScroll: true })`).

- [ ] **Step 8: Verify** — `npm run build` + full `php artisan test` PASS; manual check of both pages against `task-history.png` / `settings.png`.

- [ ] **Step 9: Commit** — `git add -A && git commit -m "feat: task history and timer settings pages"`

---

### Task 9: GitHub repo + Laravel Cloud deployment

**Files:**
- Modify: none required beyond what `cloud`/Cloud dashboard generates (commit any `.laravel-cloud/` or env changes it makes)

**Interfaces:**
- Consumes: the complete app from Tasks 1–8.
- Produces: private GitHub repo `julisha`, Laravel Cloud app **focusflow** with production environment, live HTTPS URL. Verify the live URL serves the login page (HTTP 200).

- [ ] **Step 1: Verify tooling auth**

```bash
gh auth status
cloud whoami || cloud login
```

If `gh` is not authenticated, checkpoint: ask the user to run `gh auth login`. If `cloud login` is interactive, checkpoint: ask the user to complete browser login.

- [ ] **Step 2: Push to GitHub**

```bash
gh repo create julisha --private --source=. --push
```

Expected: repo created, `main` pushed.

- [ ] **Step 3: Create and deploy the Laravel Cloud app** — REQUIRED SUB-SKILL: invoke `deploying-laravel-cloud` and follow it to: create app `focusflow` from the `julisha` GitHub repo, provision the default database, set env (`APP_NAME=FocusFlow`), run migrations on deploy (`php artisan migrate --force` in deploy commands), and trigger the first deployment.

- [ ] **Step 4: Verify live**

```bash
curl -sS -o /dev/null -w "%{http_code}" https://<deployed-url>/login
```

Expected: `200`. Register a user in the browser and create a task as a smoke test.

- [ ] **Step 5: Commit any generated config** — `git add -A && git commit -m "chore: Laravel Cloud deployment config" && git push` (skip if nothing changed).

---

### Task 10: Polish pass — auth pages, empty states, final verification

**Files:**
- Modify: `resources/js/pages/auth/Login.vue`, `resources/js/pages/auth/Register.vue` (starter kit paths; heading font + copy only)
- Modify: `resources/js/pages/Dashboard.vue`, `resources/js/pages/History.vue` (empty states)
- Test: existing suite (no new tests; visual polish)

**Interfaces:**
- Consumes: everything prior.
- Produces: shippable v1.

- [ ] **Step 1: Auth pages** — the shadcn variable override from Task 2 already themes buttons/inputs green. Add `font-display` to the auth card headings and set title copy to "FocusFlow". Verify `/login` and `/register` render with Manrope headings and green primary buttons.

- [ ] **Step 2: Empty states** — Dashboard with zero tasks: centered card "No tasks for today yet" + "Add a new task" button opening the modal. History with zero rows: "No completed sessions yet — run your first focus session." Both styled `text-on-surface-variant` with a large `MaterialIcon` (`self_improvement`).

- [ ] **Step 3: Full verification**

```bash
php artisan test
npm run build
```

Expected: everything PASS. Manual run-through of the whole loop: register → create task (with category + priority) → start focus session → complete → check dashboard stats → check history → change settings.

- [ ] **Step 4: Deploy final** — `git add -A && git commit -m "feat: polish auth, empty states" && git push` (Cloud auto-deploys on push; otherwise trigger via `cloud deploy`).

- [ ] **Step 5: Confirm live URL** shows the polished app; share URL with the user.
