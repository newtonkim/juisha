interface AlertPrefs {
    browser_notifications: boolean;
    zen_mode: boolean;
    focus_tone: string;
    break_tone: string;
}

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
        if (!prefs.browser_notifications || !('Notification' in window)) {
            return;
        }

        if (Notification.permission === 'default') {
            await Notification.requestPermission();
        }

        if (Notification.permission === 'granted') {
            new Notification(title, { body });
        }
    }
    function chime(kind: 'focus' | 'break') {
        if (prefs.zen_mode) {
            return;
        }

        const ctx = new AudioContext();
        const notes =
            TONES[kind === 'focus' ? prefs.focus_tone : prefs.break_tone] ??
            TONES['soft-bell'];
        notes.forEach((freq, i) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.frequency.value = freq;
            osc.type = 'sine';
            gain.gain.setValueAtTime(0.2, ctx.currentTime + i * 0.18);
            gain.gain.exponentialRampToValueAtTime(
                0.001,
                ctx.currentTime + i * 0.18 + 0.4,
            );
            osc.connect(gain).connect(ctx.destination);
            osc.start(ctx.currentTime + i * 0.18);
            osc.stop(ctx.currentTime + i * 0.18 + 0.45);
        });
    }

    return { notify, chime };
}
