export interface CategoryData { id: number; name: string }
export interface TaskItemData {
  id: number; title: string; description: string | null;
  priority: 'high' | 'medium' | 'low'; status: 'pending' | 'completed';
  session_minutes: number; due_at: string | null; category: CategoryData | null;
  notify_on_end: boolean; five_minute_warning: boolean;
}
export interface DailyStats { completed: number; total: number; deep_work_seconds: number; focus_score: number }
