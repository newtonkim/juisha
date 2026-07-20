export interface CategoryData { id: number; name: string }
export interface TaskItemData {
  id: number; title: string; description: string | null;
  priority: 'high' | 'medium' | 'low'; status: 'pending' | 'completed';
  session_minutes: number; due_at: string | null; category: CategoryData | null;
  notify_on_end: boolean; five_minute_warning: boolean;
}
export interface DailyStats { completed: number; total: number; deep_work_seconds: number; focus_score: number }

export interface HistoryRow {
  id: number;
  title: string;
  priority: 'high' | 'medium' | 'low';
  category: string | null;
  completed_at: string | null;
  focused_seconds: number;
}
export interface HistoryStats {
  week_count: number;
  week_change_percent: number;
  total_focus_seconds: number;
  longest_streak_days: number;
}
export interface HistoryFilters {
  q: string | null;
  category_id: number | string | null;
  days: number | string | null;
  priority: string | null;
}

export interface TimerPreferences {
  focus_minutes: number;
  short_break_minutes: number;
  long_break_minutes: number;
  focus_tone: 'soft-bell' | 'deep-gong' | 'digital-ping';
  break_tone: 'upbeat-chime' | 'gentle-marimba' | 'forest-birds';
  browser_notifications: boolean;
  zen_mode: boolean;
}
